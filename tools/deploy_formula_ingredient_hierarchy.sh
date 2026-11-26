#!/usr/bin/env bash
set -euo pipefail

trap 'status=$?; echo "ERROR: despliegue de Formula Ingredient Hierarchy detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash /tmp/deploy_formula_ingredient_hierarchy.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi

for command in curl php grep install stat cp cmp mkdir mktemp date rm id; do
    command -v "$command" >/dev/null 2>&1 || { echo "ERROR: falta el comando $command" >&2; exit 1; }
done

TEMP_DIR="$(mktemp -d)"
trap 'rm -rf -- "$TEMP_DIR"' EXIT

FILES=(study-guide.php formula-ingredient-hierarchy.php formula-list.php)
for file in "${FILES[@]}"; do
    curl -fsSL "$REPOSITORY/$REVISION/blocks/myprogress/$file" -o "$TEMP_DIR/$file"
    php -l "$TEMP_DIR/$file" >/dev/null
done

grep -Fq "'title' => 'Formula Ingredient Hierarchy'" "$TEMP_DIR/study-guide.php"
grep -Fq "'path' => '/blocks/myprogress/formula-ingredient-hierarchy.php'" "$TEMP_DIR/study-guide.php"
grep -Fq "count(\$rows) !== 19" "$TEMP_DIR/formula-ingredient-hierarchy.php"
grep -Fq "'content' => 'formula_hierarchy'" "$TEMP_DIR/formula-ingredient-hierarchy.php"
grep -Fq "'detail' => 'hierarchy_ingredients'" "$TEMP_DIR/formula-ingredient-hierarchy.php"
grep -Fq "\$fromhierarchy = \$fromcontext === 'hierarchy'" "$TEMP_DIR/formula-list.php"
grep -Fq "Back to Formula Ingredient Hierarchy" "$TEMP_DIR/formula-list.php"

TARGET_DIR="$MOODLE_ROOT/blocks/myprogress"
[[ -d "$TARGET_DIR" ]] || { echo "ERROR: no existe $TARGET_DIR" >&2; exit 1; }
[[ -f "$TARGET_DIR/formulas-study-guide-data.json" ]] || {
    echo "ERROR: falta $TARGET_DIR/formulas-study-guide-data.json" >&2
    exit 1
}

PROFILE_COUNT="$(php -r '
$data = json_decode(file_get_contents($argv[1]), true);
if (!is_array($data) || count($data["formulas"] ?? []) !== 63) { exit(1); }
$count = 0;
foreach ($data["formulas"] as $formula) {
    if (trim((string)($formula["ingredientRoles"] ?? "")) !== "") { $count++; }
}
echo $count;
' "$TARGET_DIR/formulas-study-guide-data.json")"
[[ "$PROFILE_COUNT" == "19" ]] || { echo "ERROR: se esperaban 19 perfiles de roles; encontrados $PROFILE_COUNT" >&2; exit 1; }

TIMESTAMP="$(date +%Y%m%d-%H%M%S)"
BACKUP="$BACKUP_ROOT/formula-ingredient-hierarchy-$TIMESTAMP"
mkdir -p "$BACKUP"

reference="$TARGET_DIR/study-guide.php"
[[ -f "$reference" ]] || { echo "ERROR: falta $reference" >&2; exit 1; }
default_owner_group="$(stat -c '%u:%g' "$reference")"
default_mode="$(stat -c '%a' "$reference")"

for file in "${FILES[@]}"; do
    target="$TARGET_DIR/$file"
    if [[ -f "$target" ]]; then
        cp -a "$target" "$BACKUP/$file"
        owner_group="$(stat -c '%u:%g' "$target")"
        mode="$(stat -c '%a' "$target")"
    else
        owner_group="$default_owner_group"
        mode="$default_mode"
    fi
    install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" "$TEMP_DIR/$file" "$target"
    cmp -s "$TEMP_DIR/$file" "$target"
    php -l "$target" >/dev/null
done

if [[ -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    if command -v runuser >/dev/null 2>&1 && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php" \
            || echo "AVISO: los archivos quedaron instalados, pero Moodle no pudo purgar cachés; revise la conexión de base de datos." >&2
    else
        php "$MOODLE_ROOT/admin/cli/purge_caches.php" \
            || echo "AVISO: los archivos quedaron instalados, pero Moodle no pudo purgar cachés; revise la conexión de base de datos." >&2
    fi
fi

echo "OK: botón Formula Ingredient Hierarchy agregado a Secondary Study Guide."
echo "OK: guía bilingüe con cuatro roles y 19 perfiles respaldados por la fuente."
echo "OK: acceso administrativo al lote SCORM con filtros de jerarquía preservados."
echo "RESPALDO: $BACKUP"
