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
TARGET_DIR="${DUI_YAO_DIR:-}"
RAW_BASE="https://raw.githubusercontent.com/$REPOSITORY/$REVISION/study-guide/comparisons"
TEMP_DIR="$(mktemp -d)"
BACKUP_DIR="/tmp/dui-yao-module-backup-$(date +%Y%m%d-%H%M%S)"

cleanup() {
    rm -rf "$TEMP_DIR"
}
trap cleanup EXIT

if [[ -z "$TARGET_DIR" ]]; then
    CANDIDATE_DIRS=(
        "/var/www/acupuncturetests-study-guide/current/comparisons"
        "$MOODLE_ROOT/study-guide/comparisons"
        "$(dirname "$MOODLE_ROOT")/study-guide/comparisons"
        "/var/www/html/study-guide/comparisons"
    )
    for candidate in "${CANDIDATE_DIRS[@]}"; do
        if [[ -f "$candidate/dui-yao.html" && -f "$candidate/dui-yao-es.html" ]]; then
            TARGET_DIR="$candidate"
            break
        fi
    done
fi

if [[ -z "$TARGET_DIR" ]]; then
    ACTIVE_INDEX="$(find /var/www -type f -path '*/study-guide/comparisons/dui-yao.html' -print -quit 2>/dev/null || true)"
    if [[ -n "$ACTIVE_INDEX" ]]; then
        TARGET_DIR="$(dirname "$ACTIVE_INDEX")"
    fi
fi

if [[ -z "$TARGET_DIR" || ! -f "$TARGET_DIR/dui-yao.html" || ! -f "$TARGET_DIR/dui-yao-es.html" ]]; then
    echo "ERROR: no se encontró el módulo Duì Yào activo. Puedes indicar su ruta con DUI_YAO_DIR=/ruta/study-guide/comparisons" >&2
    exit 1
fi

echo "Módulo Duì Yào detectado: $TARGET_DIR"
if ! command -v curl >/dev/null 2>&1 || ! command -v php >/dev/null 2>&1; then
    echo "ERROR: este despliegue requiere curl y php." >&2
    exit 1
fi

curl -fsSL "$RAW_BASE/dui-yao-data.json" -o "$TEMP_DIR/dui-yao-data.json"
curl -fsSL "$RAW_BASE/comparison-return.js" -o "$TEMP_DIR/comparison-return.js"
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
grep -q 'id="how-to-use-dui-yao-module"' "$TEMP_DIR/dui-yao-es.html"
grep -q 'formula-study-guide' "$TEMP_DIR/comparison-return.js"
grep -q "'digestive'" "$TEMP_DIR/comparison-return.js"
grep -q 'related-pairs' "$TEMP_DIR/comparison-return.js"
grep -q 'Back to Formula Study Guide' "$TEMP_DIR/comparison-return.js"
grep -q 'Volver a la guía de fórmulas' "$TEMP_DIR/comparison-return.js"
grep -q 'comparison-return.js?v=20261101-2' "$TEMP_DIR/dui-yao.html"
grep -q 'comparison-return.js?v=20261101-2' "$TEMP_DIR/dui-yao-es.html"
grep -q '¿Sabías que…?' "$TEMP_DIR/dui-yao-es.html"
grep -q "las tres fórmulas base y las cuatro parejas sí están" "$TEMP_DIR/dui-yao-es.html"
[[ "$(grep -o 'data-dui-modification-link=' "$TEMP_DIR/dui-yao-es.html" | wc -l)" -eq 10 ]]
for slug in "${SLUGS[@]}"; do
    grep -q 'data-formula-choice=' "$TEMP_DIR/$slug.html"
    grep -q 'data-formula-choice=' "$TEMP_DIR/$slug-es.html"
    grep -q 'comparison-return.js?v=20261101-2' "$TEMP_DIR/$slug.html"
    grep -q 'comparison-return.js?v=20261101-2' "$TEMP_DIR/$slug-es.html"
done

mkdir -p "$BACKUP_DIR"
for file in dui-yao-data.json dui-yao.html dui-yao-es.html comparison-return.js; do
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
for file in dui-yao-data.json dui-yao.html dui-yao-es.html comparison-return.js; do
    install -m 0644 -o "$OWNER" -g "$GROUP" "$TEMP_DIR/$file" "$TARGET_DIR/$file"
done
for slug in "${SLUGS[@]}"; do
    install -m 0644 -o "$OWNER" -g "$GROUP" "$TEMP_DIR/$slug.html" "$TARGET_DIR/$slug.html"
    install -m 0644 -o "$OWNER" -g "$GROUP" "$TEMP_DIR/$slug-es.html" "$TARGET_DIR/$slug-es.html"
done

grep -q 'id="formula-vs-dui-yao"' "$TARGET_DIR/dui-yao.html"
grep -q 'id="formula-modification-bridges"' "$TARGET_DIR/dui-yao-es.html"
grep -q 'id="how-to-use-dui-yao-module"' "$TARGET_DIR/dui-yao-es.html"
grep -q 'formula-study-guide' "$TARGET_DIR/comparison-return.js"
grep -q 'related-pairs' "$TARGET_DIR/comparison-return.js"
grep -q 'Back to Formula Study Guide' "$TARGET_DIR/comparison-return.js"
grep -q 'comparison-return.js?v=20261101-2' "$TARGET_DIR/dui-yao.html"
grep -q '¿Sabías que…?' "$TARGET_DIR/dui-yao-es.html"
grep -q "las tres fórmulas base y las cuatro parejas sí están" "$TARGET_DIR/dui-yao-es.html"
[[ "$(grep -o 'data-dui-modification-link=' "$TARGET_DIR/dui-yao-es.html" | wc -l)" -eq 10 ]]

if [[ "${SKIP_CACHE_PURGE:-0}" != "1" && -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    if [[ "$(id -u)" -eq 0 ]] && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    else
        php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    fi
fi

echo "MÓDULO DUÌ YÀO INSTALADO: 51 parejas, 102 fichas bilingües y 10 puentes con fórmulas"
echo "RETORNO CONTEXTUAL INSTALADO: LAS 14 CATEGORÍAS DE FÓRMULAS CONSERVAN SU PUNTO DE ORIGEN"
echo "DISTINCIÓN SEMÁNTICA INSTALADA: SOLO LAS PAREJAS RELACIONADAS NOMBRAN LA CATEGORÍA DE ORIGEN"
echo "Respaldo: $BACKUP_DIR"
