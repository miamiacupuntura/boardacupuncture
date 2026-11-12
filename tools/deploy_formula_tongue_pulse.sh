#!/usr/bin/env bash
set -euo pipefail

trap 'status=$?; echo "ERROR: despliegue de Formula Tongue and Pulse detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash tools/deploy_formula_tongue_pulse.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi

for command in curl php grep install stat cp cmp mkdir mktemp date rm id; do
    command -v "$command" >/dev/null 2>&1 || { echo "ERROR: falta el comando $command" >&2; exit 1; }
done

TEMP_DIR="$(mktemp -d)"
trap 'rm -rf -- "$TEMP_DIR"' EXIT

PHP_FILES=(formula-tongue-pulse.php study-guide.php formula-list.php)
for file in "${PHP_FILES[@]}"; do
    curl -fsSL "$REPOSITORY/$REVISION/blocks/myprogress/$file" -o "$TEMP_DIR/$file"
    php -l "$TEMP_DIR/$file" >/dev/null
done
curl -fsSL "$REPOSITORY/$REVISION/blocks/myprogress/formula-tongue-pulse-data.json" -o "$TEMP_DIR/formula-tongue-pulse-data.json"

grep -Fq "Formula Tongue and Pulse" "$TEMP_DIR/formula-tongue-pulse.php"
grep -Fq "count(\$findings['formulas'] ?? []) !== 63" "$TEMP_DIR/formula-tongue-pulse.php"
grep -Fq "data-formula-tongue-pulse-row" "$TEMP_DIR/formula-tongue-pulse.php"
grep -Fq "'from' => 'tonguepulse'" "$TEMP_DIR/formula-tongue-pulse.php"
grep -Fq "CA Formulas, Chart 7" "$TEMP_DIR/formula-tongue-pulse.php"
grep -Fq "'title' => 'Formula Tongue and Pulse'" "$TEMP_DIR/study-guide.php"
grep -Fq "'path' => '/blocks/myprogress/formula-tongue-pulse.php'" "$TEMP_DIR/study-guide.php"
grep -Fq "\$fromtonguepulse = \$fromcontext === 'tonguepulse'" "$TEMP_DIR/formula-list.php"
grep -Fq "Back to Formula Tongue and Pulse" "$TEMP_DIR/formula-list.php"

TARGET_DIR="$MOODLE_ROOT/blocks/myprogress"
[[ -d "$TARGET_DIR" ]] || { echo "ERROR: no existe $TARGET_DIR" >&2; exit 1; }
for file in formulas-study-guide-data.json formula-board-roster-2026.json; do
    [[ -f "$TARGET_DIR/$file" ]] || { echo "ERROR: falta $TARGET_DIR/$file" >&2; exit 1; }
done

php -r '
$findings = json_decode(file_get_contents($argv[1]), true);
$profiles = json_decode(file_get_contents($argv[2]), true);
$roster = json_decode(file_get_contents($argv[3]), true);
if (!is_array($findings) || !is_array($profiles) || !is_array($roster)
    || (int)($findings["total"] ?? 0) !== 63
    || count($findings["formulas"] ?? []) !== 63
    || count($profiles["formulas"] ?? []) !== 63
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
$profilesByNumber = [];
foreach ($profiles["formulas"] as $formula) {
    $profilesByNumber[(int)($formula["number"] ?? 0)] = $formula;
}
$seen = [];
foreach ($findings["formulas"] as $row) {
    $number = (int)($row["number"] ?? 0);
    $name = $normalize((string)($row["name"] ?? ""));
    $officialName = $name === "jin gui shen qi wan" ? "shen qi wan" : $name;
    if ($number < 1 || $number > 63 || isset($seen[$number])
        || !isset($profilesByNumber[$number])
        || $normalize((string)($profilesByNumber[$number]["name"] ?? "")) !== $name
        || !isset($official[$officialName])
        || trim((string)($row["tongue"] ?? "")) === ""
        || trim((string)($row["pulse"] ?? "")) === "") {
        fwrite(STDERR, "ERROR: fila de lengua y pulso inválida #$number.\n");
        exit(1);
    }
    $seen[$number] = true;
}
if (count($seen) !== 63) {
    fwrite(STDERR, "ERROR: se esperaban 63 pares lengua-pulso.\n");
    exit(1);
}
' "$TEMP_DIR/formula-tongue-pulse-data.json" "$TARGET_DIR/formulas-study-guide-data.json" "$TARGET_DIR/formula-board-roster-2026.json"

TIMESTAMP="$(date +%Y%m%d-%H%M%S)"
BACKUP="$BACKUP_ROOT/formula-tongue-pulse-$TIMESTAMP"
mkdir -p "$BACKUP"
reference="$TARGET_DIR/study-guide.php"
[[ -f "$reference" ]] || { echo "ERROR: falta $reference" >&2; exit 1; }
default_owner_group="$(stat -c '%u:%g' "$reference")"
default_mode="$(stat -c '%a' "$reference")"

FILES=("${PHP_FILES[@]}" formula-tongue-pulse-data.json)
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

echo "OK: botón Formula Tongue and Pulse instalado en Secondary Study Guide."
echo "OK: 63 fórmulas oficiales con lengua y pulso verificadas."
echo "OK: búsqueda por fórmula, categoría, lengua y pulso activa."
echo "OK: cada fórmula abre su tarjeta exacta y ofrece regreso a Formula Tongue and Pulse."
echo "RESPALDO: $BACKUP"
