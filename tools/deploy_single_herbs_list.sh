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

FILES=(single-herbs-list.php single-herbs-study-guide.php study-guide.php)
for file in "${FILES[@]}"; do
    curl -fsSL "$REPOSITORY/$REVISION/blocks/myprogress/$file" -o "$TEMP_DIR/$file"
    php -l "$TEMP_DIR/$file" >/dev/null
done

grep -Fq "Single Herbs List" "$TEMP_DIR/single-herbs-list.php"
grep -Fq "count(\$cardindex['herbs'] ?? []) !== 304" "$TEMP_DIR/single-herbs-list.php"
grep -Fq "foreach (\$cardindex['herbs']" "$TEMP_DIR/single-herbs-list.php"
grep -Fq "A–Z" "$TEMP_DIR/single-herbs-list.php"
grep -Fq "By category" "$TEMP_DIR/single-herbs-list.php"
grep -Fq "data-herb-category-link" "$TEMP_DIR/single-herbs-list.php"
grep -Fq "Back to all herbs" "$TEMP_DIR/single-herbs-list.php"
grep -Fq "Unique fingerprint" "$TEMP_DIR/single-herbs-list.php"
grep -Fq "Formula boundary" "$TEMP_DIR/single-herbs-list.php"
grep -Fq "token_get_all" "$TEMP_DIR/single-herbs-list.php"
grep -Fq "T_CLOSE_TAG" "$TEMP_DIR/single-herbs-list.php"
if grep -Fq "include(\$path)" "$TEMP_DIR/single-herbs-list.php"; then
    echo "ERROR: Single Herbs List todavía ejecuta páginas completas de categoría." >&2
    exit 1
fi
if grep -Fq "Open full study card" "$TEMP_DIR/single-herbs-list.php"; then
    echo "ERROR: Single Herbs List todavía depende de otra tarjeta." >&2
    exit 1
fi
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
    php -l "$target" >/dev/null
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
echo "OK: organización A–Z y por categoría activa."
echo "OK: búsqueda, filtro de categoría y tarjetas expandibles activos."
echo "OK: cada tarjeta contiene identidad, pista, memoria, huella, límite de fórmulas y seguridad disponibles."
echo "OK: la categoría abre su grupo y ofrece regreso a todas las hierbas."
echo "OK: no se requiere navegar a otra tarjeta."
echo "OK: solo se leen los datos de categoría; sus páginas completas no se ejecutan."
echo "RESPALDO: $BACKUP"
