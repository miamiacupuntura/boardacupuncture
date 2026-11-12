#!/usr/bin/env bash
set -euo pipefail

trap 'status=$?; echo "ERROR: despliegue del Chief Herbs Formula Map detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash tools/deploy_formula_chief_herbs_map.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi

for command in curl php grep install stat cp cmp mkdir mktemp date rm id; do
    command -v "$command" >/dev/null 2>&1 || { echo "ERROR: falta el comando $command" >&2; exit 1; }
done

TEMP_DIR="$(mktemp -d)"
trap 'rm -rf -- "$TEMP_DIR"' EXIT

PHP_FILES=(formula-chief-herbs-map.php study-guide.php formula-list.php)
for file in "${PHP_FILES[@]}"; do
    curl -fsSL "$REPOSITORY/$REVISION/blocks/myprogress/$file" -o "$TEMP_DIR/$file"
    php -l "$TEMP_DIR/$file" >/dev/null
done

grep -Fq "Chief Herbs Formula Map" "$TEMP_DIR/formula-chief-herbs-map.php"
grep -Fq "count(\$data['formulas'] ?? []) !== 63" "$TEMP_DIR/formula-chief-herbs-map.php"
grep -Fq "count(\$grouporder) !== 14" "$TEMP_DIR/formula-chief-herbs-map.php"
grep -Fq "data-chief-map-svg" "$TEMP_DIR/formula-chief-herbs-map.php"
grep -Fq "data-chief-map-download" "$TEMP_DIR/formula-chief-herbs-map.php"
grep -Fq "'from' => 'chiefmap'" "$TEMP_DIR/formula-chief-herbs-map.php"
grep -Fq "\$chiefherbsmapurl = new moodle_url" "$TEMP_DIR/study-guide.php"
grep -Fq "Chief Herbs Formula Map" "$TEMP_DIR/study-guide.php"
grep -Fq "\$fromchiefmap = \$fromcontext === 'chiefmap'" "$TEMP_DIR/formula-list.php"
grep -Fq "Back to Chief Herbs Formula Map" "$TEMP_DIR/formula-list.php"

TARGET_DIR="$MOODLE_ROOT/blocks/myprogress"
[[ -d "$TARGET_DIR" ]] || { echo "ERROR: no existe $TARGET_DIR" >&2; exit 1; }
for file in formulas-study-guide-data.json formula-board-roster-2026.json; do
    [[ -f "$TARGET_DIR/$file" ]] || { echo "ERROR: falta $TARGET_DIR/$file" >&2; exit 1; }
done

php -r '
$data = json_decode(file_get_contents($argv[1]), true);
$roster = json_decode(file_get_contents($argv[2]), true);
if (!is_array($data) || !is_array($roster)
    || count($data["formulas"] ?? []) !== 63
    || count($roster["formulas"] ?? []) !== 181) {
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
$categories = [];
$seen = [];
foreach ($data["formulas"] as $formula) {
    $number = (int)($formula["number"] ?? 0);
    $name = $normalize((string)($formula["name"] ?? ""));
    $officialName = $name === "jin gui shen qi wan" ? "shen qi wan" : $name;
    $category = trim((string)($formula["categoryKey"] ?? ""));
    $chief = trim((string)($formula["kings"] ?? ""));
    if ($number < 1 || $number > 63 || isset($seen[$number])
        || !isset($official[$officialName]) || $category === "" || $chief === "") {
        fwrite(STDERR, "ERROR: fila inválida del mapa Chief #$number.\n");
        exit(1);
    }
    $seen[$number] = true;
    $categories[$category] = true;
}
if (count($seen) !== 63 || count($categories) !== 14) {
    fwrite(STDERR, "ERROR: el mapa requiere 63 fórmulas y 14 categorías.\n");
    exit(1);
}
' "$TARGET_DIR/formulas-study-guide-data.json" "$TARGET_DIR/formula-board-roster-2026.json"

TIMESTAMP="$(date +%Y%m%d-%H%M%S)"
BACKUP="$BACKUP_ROOT/formula-chief-herbs-map-$TIMESTAMP"
mkdir -p "$BACKUP"
reference="$TARGET_DIR/study-guide.php"
[[ -f "$reference" ]] || { echo "ERROR: falta $reference" >&2; exit 1; }
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

echo "OK: botón Chief Herbs Formula Map instalado en Main Study Guide."
echo "OK: mapa vectorial limitado a 63 fórmulas oficiales y 14 categorías."
echo "OK: hierbas Chief resaltadas y descarga SVG activa."
echo "OK: cada fórmula abre su tarjeta exacta y ofrece regreso al mapa."
echo "RESPALDO: $BACKUP"
