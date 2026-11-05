#!/usr/bin/env bash

set -euo pipefail
trap 'status=$?; echo "ERROR: despliegue detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
STUDY_NOTE_DIR="${STUDY_NOTE_DIR:-}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash deploy_herb_practice_hub.sh COMMIT_DE_40_CARACTERES"
    exit 1
fi

for command in curl php install; do
    command -v "$command" >/dev/null 2>&1 || { echo "ERROR: falta el comando $command"; exit 1; }
done

if [[ ! -f "$MOODLE_ROOT/config.php" ]]; then
    echo "ERROR: no se encontró Moodle en $MOODLE_ROOT"
    exit 1
fi

if [[ -z "$STUDY_NOTE_DIR" ]]; then
    for candidate in \
        "/var/www/acupuncturetests-study-guide/current/comparisons" \
        "$MOODLE_ROOT/study-guide/comparisons" \
        "$(dirname "$MOODLE_ROOT")/study-guide/comparisons" \
        "/var/www/html/study-guide/comparisons"; do
        if [[ -d "$candidate" && -f "$candidate/comparisons.css" ]]; then
            STUDY_NOTE_DIR="$candidate"
            break
        fi
    done
fi
if [[ -z "$STUDY_NOTE_DIR" || ! -d "$STUDY_NOTE_DIR" ]]; then
    echo "ERROR: no se encontró el directorio activo de Study Notes. Indica STUDY_NOTE_DIR=/ruta/comparisons"
    exit 1
fi

TEMP_DIR="$(mktemp -d)"
BACKUP_DIR="$BACKUP_ROOT/herb-practice-hub-$(date +%Y%m%d-%H%M%S)"
trap 'rm -rf "$TEMP_DIR"' EXIT
mkdir -p "$TEMP_DIR/blocks/menu" "$TEMP_DIR/study-guide/comparisons" "$BACKUP_DIR/moodle" "$BACKUP_DIR/static"

MOODLE_FILES=(
    "blocks/menu/herbology-board.php"
    "blocks/menu/version.php"
)
STATIC_FILES=(
    "herb-comparison-bank.html"
    "herb-comparison-bank-es.html"
    "herb-comparison-bank.css"
    "herb-comparison-bank.js"
    "herb-comparison-bank-data.json"
    "herb-comparison-bank-translations-es.json"
    "study-return-navigation.js"
)

for relative in "${MOODLE_FILES[@]}"; do
    curl -fsSL "$REPOSITORY/$REVISION/$relative" -o "$TEMP_DIR/$relative"
    php -l "$TEMP_DIR/$relative" >/dev/null
done
for file in "${STATIC_FILES[@]}"; do
    curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/$file" \
        -o "$TEMP_DIR/study-guide/comparisons/$file"
done

grep -Fq 'Choose a Single Herbs practice focus' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'herb-comparison-bank-data.json' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'Practice by herb' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq "params.get('focus') === 'herb'" "$TEMP_DIR/study-guide/comparisons/herb-comparison-bank.js"
php -r '
$data = json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR);
if (($data["totals"]["questions"] ?? 0) < 1 || count($data["chapters"] ?? []) < 1) {
    throw new RuntimeException("El banco de Single Herbs está vacío.");
}
' "$TEMP_DIR/study-guide/comparisons/herb-comparison-bank-data.json"

for relative in "${MOODLE_FILES[@]}"; do
    target="$MOODLE_ROOT/$relative"
    [[ -f "$target" ]] || { echo "ERROR: no existe $target"; exit 1; }
    cp -a "$target" "$BACKUP_DIR/moodle/$(basename "$relative")"
    owner_group="$(stat -c '%u:%g' "$target")"
    mode="$(stat -c '%a' "$target")"
    install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" "$TEMP_DIR/$relative" "$target"
done

STATIC_REFERENCE="$STUDY_NOTE_DIR/comparisons.css"
owner_group="$(stat -c '%u:%g' "$STATIC_REFERENCE")"
mode="$(stat -c '%a' "$STATIC_REFERENCE")"
for file in "${STATIC_FILES[@]}"; do
    target="$STUDY_NOTE_DIR/$file"
    [[ -f "$target" ]] && cp -a "$target" "$BACKUP_DIR/static/$file"
    install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" \
        "$TEMP_DIR/study-guide/comparisons/$file" "$target"
done

php -l "$MOODLE_ROOT/blocks/menu/herbology-board.php" >/dev/null
php -r '
$data = json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR);
echo "Single Herbs bank: questions=" . $data["totals"]["questions"]
    . ", chapters=" . $data["totals"]["chapters"]
    . ", charts=" . $data["totals"]["charts"] . PHP_EOL;
' "$STUDY_NOTE_DIR/herb-comparison-bank-data.json"

if [[ -f "$MOODLE_ROOT/admin/cli/upgrade.php" ]]; then
    if [[ "$(id -u)" -eq 0 ]] && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/upgrade.php" --non-interactive
    else
        php "$MOODLE_ROOT/admin/cli/upgrade.php" --non-interactive
    fi
fi
if [[ "${SKIP_CACHE_PURGE:-0}" != "1" && -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    if [[ "$(id -u)" -eq 0 ]] && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    else
        php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    fi
fi

echo "CENTRO DE PRÁCTICA DE SINGLE HERBS INSTALADO"
echo "Respaldo: $BACKUP_DIR"
echo "Página: https://www.acupuncturetests.com/blocks/menu/herbology-board.php?track=herbs&hub=$REVISION"
