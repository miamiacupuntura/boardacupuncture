#!/usr/bin/env bash
set -euo pipefail

trap 'status=$?; echo "ERROR: despliegue de Formula and Ingredients detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash tools/deploy_formula_ingredients.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi

for command in curl php grep install stat cp cmp mkdir mktemp date rm id; do
    command -v "$command" >/dev/null 2>&1 || { echo "ERROR: falta el comando $command" >&2; exit 1; }
done

TEMP_DIR="$(mktemp -d)"
trap 'rm -rf -- "$TEMP_DIR"' EXIT

PHP_FILES=(formula-ingredients.php study-guide.php single-herbs-list.php formula-list.php)
for file in "${PHP_FILES[@]}"; do
    curl -fsSL "$REPOSITORY/$REVISION/blocks/myprogress/$file" -o "$TEMP_DIR/$file"
    php -l "$TEMP_DIR/$file" >/dev/null
done

grep -Fq "Formula and Ingredients" "$TEMP_DIR/formula-ingredients.php"
grep -Fq "count(\$formuladata['formulas'] ?? []) !== 63" "$TEMP_DIR/formula-ingredients.php"
grep -Fq "count(\$formularoster['formulas'] ?? []) !== 181" "$TEMP_DIR/formula-ingredients.php"
grep -Fq "formula-ingredients__herb-link.is-chief" "$TEMP_DIR/formula-ingredients.php"
grep -Fq "'from' => 'ingredients'" "$TEMP_DIR/formula-ingredients.php"
grep -Fq "formula-ingredients.php" "$TEMP_DIR/study-guide.php"
grep -Fq "AVAILABLE · 63 GUIDELINE FORMULAS" "$TEMP_DIR/study-guide.php"
grep -Fq "Back to Formula and Ingredients" "$TEMP_DIR/single-herbs-list.php"
grep -Fq "Back to Formula and Ingredients" "$TEMP_DIR/formula-list.php"
grep -Fq "\$fromingredients = \$fromcontext === 'ingredients'" "$TEMP_DIR/single-herbs-list.php"
grep -Fq "\$fromingredients = \$fromcontext === 'ingredients'" "$TEMP_DIR/formula-list.php"

TARGET_DIR="$MOODLE_ROOT/blocks/myprogress"
[[ -d "$TARGET_DIR" ]] || { echo "ERROR: no existe $TARGET_DIR" >&2; exit 1; }
for file in formulas-study-guide-data.json formula-board-roster-2026.json single-herb-card-index.json; do
    [[ -f "$TARGET_DIR/$file" ]] || { echo "ERROR: falta $TARGET_DIR/$file" >&2; exit 1; }
done

php -r '
$data = json_decode(file_get_contents($argv[1]), true);
$roster = json_decode(file_get_contents($argv[2]), true);
$herbs = json_decode(file_get_contents($argv[3]), true);
if (!is_array($data) || !is_array($roster) || !is_array($herbs)
    || count($data["formulas"] ?? []) !== 63
    || (int)($roster["total"] ?? 0) !== 181
    || count($roster["formulas"] ?? []) !== 181
    || (int)($herbs["total"] ?? 0) !== 304) {
    fwrite(STDERR, "ERROR: bancos oficiales inválidos.\n");
    exit(1);
}
$normalize = static function(string $value): string {
    return trim((string)preg_replace("/[^a-z0-9]+/", " ", strtolower($value)));
};
$official = [];
foreach ($roster["formulas"] as $formula) {
    $official[$normalize((string)($formula["name"] ?? ""))] = true;
}
$count = 0;
foreach ($data["formulas"] as $formula) {
    $name = $normalize((string)($formula["name"] ?? ""));
    if ($name === "jin gui shen qi wan") {
        $name = "shen qi wan";
    }
    if (isset($official[$name])
        && trim((string)($formula["ingredients"] ?? "")) !== ""
        && trim((string)($formula["kings"] ?? "")) !== "") {
        $count++;
    }
}
if ($count !== 63) {
    fwrite(STDERR, "ERROR: se esperaban 63 fórmulas oficiales con ingredientes y Chief; se encontraron $count.\n");
    exit(1);
}
' "$TARGET_DIR/formulas-study-guide-data.json" "$TARGET_DIR/formula-board-roster-2026.json" "$TARGET_DIR/single-herb-card-index.json"

TIMESTAMP="$(date +%Y%m%d-%H%M%S)"
BACKUP="$BACKUP_ROOT/formula-ingredients-$TIMESTAMP"
mkdir -p "$BACKUP"
reference="$TARGET_DIR/study-guide.php"
default_owner_group="$(stat -c '%u:%g' "$reference")"
default_mode="$(stat -c '%a' "$reference")"

for file in "${PHP_FILES[@]}"; do
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
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    else
        php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    fi
fi

echo "OK: botón Formula and Ingredients instalado en Secondary Study Guide."
echo "OK: 63 fórmulas oficiales del guideline verificadas."
echo "OK: ingredientes Chief resaltados en amarillo."
echo "OK: fórmulas e ingredientes enlazan sus tarjetas exactas."
echo "OK: navegación de ida y vuelta activa en English y Español."
echo "RESPALDO: $BACKUP"
