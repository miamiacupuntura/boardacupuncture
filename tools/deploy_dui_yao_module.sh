#!/usr/bin/env bash
set -euo pipefail

if [[ $# -ne 1 ]]; then
    echo "Uso: sudo bash $0 <git-revision>" >&2
    exit 2
fi

REVISION="$1"
if [[ ! "$REVISION" =~ ^[0-9A-Za-z._/-]+$ ]]; then
    echo "ERROR: revisión Git no válida." >&2
    exit 2
fi

REPOSITORY="${REPOSITORY:-miamiacupuntura/boardacupuncture}"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
TARGET_DIR="$MOODLE_ROOT/study-guide/comparisons"
RAW_BASE="https://raw.githubusercontent.com/$REPOSITORY/$REVISION/study-guide/comparisons"
TEMP_DIR="$(mktemp -d)"
BACKUP_DIR="/tmp/dui-yao-module-backup-$(date +%Y%m%d-%H%M%S)"

cleanup() {
    rm -rf "$TEMP_DIR"
}
trap cleanup EXIT

if [[ ! -d "$TARGET_DIR" ]]; then
    echo "ERROR: no existe $TARGET_DIR" >&2
    exit 1
fi
if ! command -v curl >/dev/null 2>&1 || ! command -v php >/dev/null 2>&1; then
    echo "ERROR: este despliegue requiere curl y php." >&2
    exit 1
fi

curl -fsSL "$RAW_BASE/dui-yao-data.json" -o "$TEMP_DIR/dui-yao-data.json"
php -r '
    $data = json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR);
    if (count($data) !== 51) {
        fwrite(STDERR, "ERROR: se esperaban 51 parejas Duì Yào.\n");
        exit(1);
    }
    $numbers = array_column($data, "number");
    sort($numbers);
    if ($numbers !== range(1, 51)) {
        fwrite(STDERR, "ERROR: faltan números o existen duplicados.\n");
        exit(1);
    }
    $bridges = array_values(array_filter($data, fn($item) => ($item["formulaBridge"] ?? null) !== null));
    $direct = array_values(array_filter($bridges, fn($item) => ($item["formulaBridge"]["kind"] ?? "") === "direct"));
    if (count($bridges) !== 10 || count($direct) !== 4) {
        fwrite(STDERR, "ERROR: metadatos de puentes con fórmulas incompletos.\n");
        exit(1);
    }
' "$TEMP_DIR/dui-yao-data.json"

for page in dui-yao.html dui-yao-es.html; do
    curl -fsSL "$RAW_BASE/$page" -o "$TEMP_DIR/$page"
done

mapfile -t SLUGS < <(php -r '
    $data = json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR);
    foreach ($data as $item) {
        echo $item["slug"], "\n";
    }
' "$TEMP_DIR/dui-yao-data.json")

for slug in "${SLUGS[@]}"; do
    curl -fsSL "$RAW_BASE/$slug.html" -o "$TEMP_DIR/$slug.html"
    curl -fsSL "$RAW_BASE/$slug-es.html" -o "$TEMP_DIR/$slug-es.html"
done

grep -q 'id="formula-vs-dui-yao"' "$TEMP_DIR/dui-yao.html"
grep -q 'id="formula-modification-bridges"' "$TEMP_DIR/dui-yao-es.html"
[[ "$(grep -o 'data-dui-modification-link=' "$TEMP_DIR/dui-yao-es.html" | wc -l)" -eq 10 ]]
for slug in "${SLUGS[@]}"; do
    grep -q 'data-formula-choice=' "$TEMP_DIR/$slug.html"
    grep -q 'data-formula-choice=' "$TEMP_DIR/$slug-es.html"
done

mkdir -p "$BACKUP_DIR"
for file in dui-yao-data.json dui-yao.html dui-yao-es.html; do
    [[ -f "$TARGET_DIR/$file" ]] && cp -a "$TARGET_DIR/$file" "$BACKUP_DIR/"
done
for slug in "${SLUGS[@]}"; do
    for file in "$slug.html" "$slug-es.html"; do
        [[ -f "$TARGET_DIR/$file" ]] && cp -a "$TARGET_DIR/$file" "$BACKUP_DIR/"
    done
done

OWNERSHIP_SOURCE="$TARGET_DIR"
[[ -f "$TARGET_DIR/dui-yao.html" ]] && OWNERSHIP_SOURCE="$TARGET_DIR/dui-yao.html"
OWNER="$(stat -c '%U' "$OWNERSHIP_SOURCE")"
GROUP="$(stat -c '%G' "$OWNERSHIP_SOURCE")"
for file in dui-yao-data.json dui-yao.html dui-yao-es.html; do
    install -m 0644 -o "$OWNER" -g "$GROUP" "$TEMP_DIR/$file" "$TARGET_DIR/$file"
done
for slug in "${SLUGS[@]}"; do
    install -m 0644 -o "$OWNER" -g "$GROUP" "$TEMP_DIR/$slug.html" "$TARGET_DIR/$slug.html"
    install -m 0644 -o "$OWNER" -g "$GROUP" "$TEMP_DIR/$slug-es.html" "$TARGET_DIR/$slug-es.html"
done

grep -q 'id="formula-vs-dui-yao"' "$TARGET_DIR/dui-yao.html"
grep -q 'id="formula-modification-bridges"' "$TARGET_DIR/dui-yao-es.html"
[[ "$(grep -o 'data-dui-modification-link=' "$TARGET_DIR/dui-yao-es.html" | wc -l)" -eq 10 ]]

if [[ "${SKIP_CACHE_PURGE:-0}" != "1" && -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    if [[ "$(id -u)" -eq 0 ]] && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    else
        php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    fi
fi

echo "MÓDULO DUÌ YÀO INSTALADO: 51 parejas, 102 fichas bilingües y 10 puentes con fórmulas"
echo "Respaldo: $BACKUP_DIR"
