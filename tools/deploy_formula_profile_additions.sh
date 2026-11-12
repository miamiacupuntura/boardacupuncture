#!/usr/bin/env bash
set -euo pipefail

trap 'status=$?; echo "ERROR: despliegue de perfiles verificados detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash tools/deploy_formula_profile_additions.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi

for command in curl php grep install stat cp cmp mkdir mktemp date rm id; do
    command -v "$command" >/dev/null 2>&1 || { echo "ERROR: falta el comando $command" >&2; exit 1; }
done

TEMP_DIR="$(mktemp -d)"
trap 'rm -rf -- "$TEMP_DIR"' EXIT

curl -fsSL "$REPOSITORY/$REVISION/blocks/myprogress/formula-list.php" -o "$TEMP_DIR/formula-list.php"
curl -fsSL "$REPOSITORY/$REVISION/blocks/myprogress/formula-guideline-profile-additions.json" -o "$TEMP_DIR/formula-guideline-profile-additions.json"
php -l "$TEMP_DIR/formula-list.php" >/dev/null

grep -Fq "formula-guideline-profile-additions.json" "$TEMP_DIR/formula-list.php"
grep -Fq "\$profileadditions['formulas']" "$TEMP_DIR/formula-list.php"
grep -Fq "'source' => \$detailtext" "$TEMP_DIR/formula-list.php"
grep -Fq "Bensky formula profile, pp. 55–56." "$TEMP_DIR/formula-guideline-profile-additions.json"
grep -Fq "\"name\": \"Jia Jian Wei Rui Tang\"" "$TEMP_DIR/formula-guideline-profile-additions.json"
grep -Fq "\"name\": \"Qing Wei San\"" "$TEMP_DIR/formula-guideline-profile-additions.json"
grep -Fq "\"verificationStatus\": \"complete\"" "$TEMP_DIR/formula-guideline-profile-additions.json"
grep -Fq "Verified partial profile" "$TEMP_DIR/formula-list.php"
grep -Fq "formula-list__chinese" "$TEMP_DIR/formula-list.php"

TARGET_DIR="$MOODLE_ROOT/blocks/myprogress"
[[ -d "$TARGET_DIR" ]] || { echo "ERROR: no existe $TARGET_DIR" >&2; exit 1; }
for file in formulas-study-guide-data.json formula-board-roster-2026.json; do
    [[ -f "$TARGET_DIR/$file" ]] || { echo "ERROR: falta $TARGET_DIR/$file" >&2; exit 1; }
done

php -r '
$additions = json_decode(file_get_contents($argv[1]), true);
$roster = json_decode(file_get_contents($argv[2]), true);
if (!is_array($additions) || !is_array($roster)
    || (int)($additions["total"] ?? -1) !== count($additions["formulas"] ?? [])
    || count($roster["formulas"] ?? []) !== 181) {
    fwrite(STDERR, "ERROR: bancos inválidos.\n");
    exit(1);
}
$normalize = static function(string $value): string {
    return trim((string)preg_replace("/[^a-z0-9]+/", " ", strtolower($value)));
};
$official = [];
foreach ($roster["formulas"] as $formula) {
    $official[$normalize((string)($formula["name"] ?? ""))] = true;
}
$common = ["name", "chineseName", "action", "actionEs", "source", "sourceEs"];
$completeRequired = [
    "kings", "pattern", "patternEs", "clue", "clueEs",
    "ingredients", "ingredientsEs", "ingredientRoles", "ingredientRolesEs",
    "safety", "safetyEs"
];
$complete = 0;
$partial = 0;
foreach ($additions["formulas"] as $profile) {
    $name = $normalize((string)($profile["name"] ?? ""));
    if (!isset($official[$name])) {
        fwrite(STDERR, "ERROR: perfil fuera del guideline: ".($profile["name"] ?? "sin nombre").".\n");
        exit(1);
    }
    foreach ($common as $field) {
        if (trim((string)($profile[$field] ?? "")) === "") {
            fwrite(STDERR, "ERROR: campo $field vacío en ".($profile["name"] ?? "sin nombre").".\n");
            exit(1);
        }
    }
    $status = (string)($profile["verificationStatus"] ?? "");
    if ($status === "complete") {
        foreach ($completeRequired as $field) {
            if (trim((string)($profile[$field] ?? "")) === "") {
                fwrite(STDERR, "ERROR: campo completo $field vacío en ".($profile["name"] ?? "sin nombre").".\n");
                exit(1);
            }
        }
        $complete++;
    } elseif ($status === "partial") {
        $partial++;
    } else {
        fwrite(STDERR, "ERROR: estado de verificación inválido en ".($profile["name"] ?? "sin nombre").".\n");
        exit(1);
    }
}
if ($complete !== 24 || $partial !== 0) {
    fwrite(STDERR, "ERROR: se esperaban 24 perfiles completos y 0 parciales; encontrados $complete y $partial.\n");
    exit(1);
}
' "$TEMP_DIR/formula-guideline-profile-additions.json" "$TARGET_DIR/formula-board-roster-2026.json"

TIMESTAMP="$(date +%Y%m%d-%H%M%S)"
BACKUP="$BACKUP_ROOT/formula-profile-additions-$TIMESTAMP"
mkdir -p "$BACKUP"
reference="$TARGET_DIR/formula-list.php"
[[ -f "$reference" ]] || { echo "ERROR: falta $reference" >&2; exit 1; }
default_owner_group="$(stat -c '%u:%g' "$reference")"
default_mode="$(stat -c '%a' "$reference")"

for file in formula-guideline-profile-additions.json formula-list.php; do
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
    if [[ "$file" == *.php ]]; then
        php -l "$target" >/dev/null
    fi
done

if [[ -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    if command -v runuser >/dev/null 2>&1 && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    else
        php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    fi
fi

echo "OK: Jia Jian Wei Rui Tang completada desde la fuente suministrada, pp. 55–56."
echo "OK: 23 perfiles adicionales completados con patrón, Chief, ingredientes, pista, arquitectura y seguridad."
echo "OK: 24 perfiles detallados disponibles en English y Español."
echo "OK: nombres chinos tradicionales visibles en las 24 tarjetas actualizadas."
echo "OK: solo se aceptan adiciones presentes en el roster oficial de 181 fórmulas."
echo "OK: quedan 24 fórmulas oficiales sin perfil detallado."
echo "RESPALDO: $BACKUP"
