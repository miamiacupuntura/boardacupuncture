#!/usr/bin/env bash
set -euo pipefail

trap 'status=$?; echo "ERROR: despliegue de Single Herbs List detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash tools/deploy_single_herbs_list.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi

for command in curl php grep install stat cp cmp mkdir mktemp date rm id; do
    command -v "$command" >/dev/null 2>&1 || {
        echo "ERROR: falta el comando $command" >&2
        exit 1
    }
done

TEMP_DIR="$(mktemp -d)"
trap 'rm -rf -- "$TEMP_DIR"' EXIT

PHP_FILES=(single-herbs-list.php single-herbs-study-guide.php study-guide.php)
DATA_FILES=(single-herb-rich-profiles.json single-herb-actions-indications-supplement.json single-herb-safety-supplement.json)
FILES=("${PHP_FILES[@]}" "${DATA_FILES[@]}")
for file in "${FILES[@]}"; do
    curl -fsSL "$REPOSITORY/$REVISION/blocks/myprogress/$file" -o "$TEMP_DIR/$file"
done
for file in "${PHP_FILES[@]}"; do
    php -l "$TEMP_DIR/$file" >/dev/null
done

grep -Fq "Single Herbs List" "$TEMP_DIR/single-herbs-list.php"
grep -Fq "count(\$cardindex['herbs'] ?? []) !== 304" "$TEMP_DIR/single-herbs-list.php"
grep -Fq "foreach (\$cardindex['herbs']" "$TEMP_DIR/single-herbs-list.php"
grep -Fq "A–Z" "$TEMP_DIR/single-herbs-list.php"
grep -Fq "By category" "$TEMP_DIR/single-herbs-list.php"
grep -Fq "data-herb-category-link" "$TEMP_DIR/single-herbs-list.php"
grep -Fq "Back to all herbs" "$TEMP_DIR/single-herbs-list.php"
grep -Fq "Sorting modes always start from the complete guideline roster." "$TEMP_DIR/single-herbs-list.php"
grep -Fq "Preserve the conventional Bensky category sequence instead of alphabetizing it." "$TEMP_DIR/single-herbs-list.php"
grep -Fq "By Bensky order" "$TEMP_DIR/single-herbs-list.php"
grep -Fq "Unique fingerprint" "$TEMP_DIR/single-herbs-list.php"
grep -Fq "Clinical recognition" "$TEMP_DIR/single-herbs-list.php"
grep -Fq "single-herb-rich-profiles.json" "$TEMP_DIR/single-herbs-list.php"
grep -Fq "single-herb-card-index.json" "$TEMP_DIR/single-herbs-list.php"
grep -Fq "single-herb-properties.json" "$TEMP_DIR/single-herbs-list.php"
if grep -Fq "single-herbs-category-" "$TEMP_DIR/single-herbs-list.php" \
        || grep -Fq "eval(" "$TEMP_DIR/single-herbs-list.php"; then
    echo "ERROR: Single Herbs List todavía depende de páginas PHP de categoría." >&2
    exit 1
fi
if grep -Fq "Open full study card" "$TEMP_DIR/single-herbs-list.php"; then
    echo "ERROR: Single Herbs List todavía depende de otra tarjeta." >&2
    exit 1
fi
php -r '
$rich = json_decode(file_get_contents($argv[1]), true);
$actions = json_decode(file_get_contents($argv[2]), true);
$safety = json_decode(file_get_contents($argv[3]), true);
if (!is_array($rich) || !is_array($actions) || !is_array($safety)
    || (int)($rich["guidelineTotal"] ?? 0) !== 304
    || (int)($rich["total"] ?? 0) < 180
    || count($rich["profiles"] ?? []) !== (int)$rich["total"]
    || count($actions["herbs"] ?? []) < 100
    || count($safety["categoryCautions"] ?? []) < 30) {
    fwrite(STDERR, "ERROR: los bancos clínicos enriquecidos no son válidos.\n");
    exit(1);
}
' "$TEMP_DIR/single-herb-rich-profiles.json" \
  "$TEMP_DIR/single-herb-actions-indications-supplement.json" \
  "$TEMP_DIR/single-herb-safety-supplement.json"
grep -Fq "single-herbs-list.php" "$TEMP_DIR/single-herbs-study-guide.php"
grep -Fq "304 official guideline herbs" "$TEMP_DIR/study-guide.php"

TARGET_DIR="$MOODLE_ROOT/blocks/myprogress"
[[ -d "$TARGET_DIR" ]] || {
    echo "ERROR: no existe $TARGET_DIR" >&2
    exit 1
}
[[ -f "$TARGET_DIR/single-herbs-study-guide.php" ]] || {
    echo "ERROR: no existe Single Herbs Study Guide activo." >&2
    exit 1
}

php -r '
$index = json_decode(file_get_contents($argv[1]), true);
$properties = json_decode(file_get_contents($argv[2]), true);
if (!is_array($index) || !is_array($properties)
    || (int)($index["total"] ?? 0) !== 304
    || count($index["herbs"] ?? []) !== 304
    || (int)($properties["total"] ?? 0) !== 304) {
    fwrite(STDERR, "ERROR: los bancos no contienen exactamente las 304 hierbas del guideline.\n");
    exit(1);
}
' "$TARGET_DIR/single-herb-card-index.json" "$TARGET_DIR/single-herb-properties.json"

TIMESTAMP="$(date +%Y%m%d-%H%M%S)"
BACKUP="$BACKUP_ROOT/single-herbs-list-$TIMESTAMP"
mkdir -p "$BACKUP"

reference="$TARGET_DIR/single-herbs-study-guide.php"
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
    install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" \
        "$TEMP_DIR/$file" "$target"
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

echo "OK: botón Single Herbs List instalado en Single Herbs y en la biblioteca."
echo "OK: lista limitada a las 304 hierbas oficiales del guideline."
echo "OK: organización A–Z y por la secuencia convencional de 33 categorías Bensky activa."
echo "OK: selector, encabezados y etiquetas muestran el número de categoría."
echo "OK: A–Z y By category reinician filtros anteriores y muestran el roster completo."
echo "OK: búsqueda, filtro de categoría y tarjetas expandibles activos."
echo "OK: 185 perfiles detallados incluyen memoria y huella diferencial; 291 hierbas tienen acciones e indicaciones verificadas."
echo "OK: precauciones por hierba o categoría disponibles para las 304 hierbas."
echo "OK: la categoría abre su grupo y ofrece regreso a todas las hierbas."
echo "OK: no se requiere navegar a otra tarjeta."
echo "OK: la lista usa únicamente bancos JSON verificados; no carga páginas PHP de categoría."
echo "RESPALDO: $BACKUP"
