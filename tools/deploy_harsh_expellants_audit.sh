#!/usr/bin/env bash

set -euo pipefail
trap 'status=$?; echo "ERROR: despliegue detenido en la línea $LINENO: $BASH_COMMAND (código $status)" >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups}"
PHP_BIN="${PHP_BIN:-}"
CATEGORY_PATH="blocks/myprogress/single-herbs-category-harsh-expellants.php"
GUIDE_PATH="blocks/myprogress/single-herbs-study-guide.php"
PROPERTIES_PATH="blocks/myprogress/single-herb-properties.json"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash deploy_harsh_expellants_audit.sh COMMIT_DE_40_CARACTERES"
    exit 1
fi

if [[ ! -f "$MOODLE_ROOT/config.php" ]]; then
    echo "ERROR: no se encontró Moodle en $MOODLE_ROOT"
    exit 1
fi

for relative_path in "$CATEGORY_PATH" "$GUIDE_PATH" "$PROPERTIES_PATH"; do
    if [[ ! -f "$MOODLE_ROOT/$relative_path" ]]; then
        echo "ERROR: no se encontró el archivo activo $MOODLE_ROOT/$relative_path"
        exit 1
    fi
done

if [[ -z "$PHP_BIN" ]]; then
    for candidate in php php8.4 php8.3 php8.2 php8.1 php8.0; do
        if command -v "$candidate" >/dev/null 2>&1; then
            PHP_BIN="$(command -v "$candidate")"
            break
        fi
    done
fi

if [[ -z "$PHP_BIN" || ! -x "$PHP_BIN" ]]; then
    echo "ERROR: no se encontró PHP CLI. Indica la ruta con PHP_BIN=/ruta/al/php"
    exit 1
fi

if ! command -v python3 >/dev/null 2>&1; then
    echo "ERROR: se necesita python3 para validar el JSON"
    exit 1
fi

TEMP_DIR="$(mktemp -d)"
BACKUP_DIR="$BACKUP_ROOT/harsh-expellants-audit-$(date +%Y%m%d-%H%M%S)"
trap 'rm -rf "$TEMP_DIR"' EXIT

echo "Moodle detectado: $MOODLE_ROOT"
echo "PHP CLI detectado: $PHP_BIN"
echo "Descargando auditoría de la categoría 11 desde $REVISION..."

for relative_path in "$CATEGORY_PATH" "$GUIDE_PATH" "$PROPERTIES_PATH"; do
    mkdir -p "$TEMP_DIR/$(dirname "$relative_path")"
    curl -fsSL "$REPOSITORY/$REVISION/$relative_path" -o "$TEMP_DIR/$relative_path"
done

"$PHP_BIN" -l "$TEMP_DIR/$CATEGORY_PATH" >/dev/null
"$PHP_BIN" -l "$TEMP_DIR/$GUIDE_PATH" >/dev/null
python3 -m json.tool "$TEMP_DIR/$PROPERTIES_PATH" >/dev/null

EXPECTED_TITLE='Downward Draining Harsh Expellants (Cathartics) (Gan Sui*, Hong Da Ji, Yuan Hua, Qian Niu Zi, Ba Dou*)'
grep -Fq "$EXPECTED_TITLE" "$TEMP_DIR/$GUIDE_PATH"
grep -Fq "Only Gan Sui and Ba Dou are in the current Board guideline" "$TEMP_DIR/$CATEGORY_PATH"
grep -Fq "'name' => 'Hong Da Ji', 'latin' => 'Knoxiae Radix', 'official' => false" "$TEMP_DIR/$CATEGORY_PATH"
grep -Fq "3–6 g decoction; ≤3 g powder" "$TEMP_DIR/$CATEGORY_PATH"
grep -Fq "0.1–0.3 g/day; correctly defatted preparation only" "$TEMP_DIR/$CATEGORY_PATH"
grep -Fq '"en": "Bitter"' "$TEMP_DIR/$PROPERTIES_PATH"
grep -Fq '"dose": "0.1–0.3 g/day; defatted only"' "$TEMP_DIR/$PROPERTIES_PATH"

OFFICIAL_COUNT="$(grep -c "'official' => true" "$TEMP_DIR/$CATEGORY_PATH")"
OUTSIDE_COUNT="$(grep -c "'official' => false" "$TEMP_DIR/$CATEGORY_PATH")"
if [[ "$OFFICIAL_COUNT" -ne 2 || "$OUTSIDE_COUNT" -ne 3 ]]; then
    echo "ERROR: se esperaban 2 hierbas vigentes y 3 fuera del guideline; se detectaron $OFFICIAL_COUNT y $OUTSIDE_COUNT"
    exit 1
fi

mkdir -p "$BACKUP_DIR"
for relative_path in "$CATEGORY_PATH" "$GUIDE_PATH" "$PROPERTIES_PATH"; do
    target="$MOODLE_ROOT/$relative_path"
    temporary="$TEMP_DIR/$relative_path"
    backup_target="$BACKUP_DIR/$relative_path"
    mkdir -p "$(dirname "$backup_target")"
    cp -a "$target" "$backup_target"
    owner_group="$(stat -c '%u:%g' "$target")"
    file_mode="$(stat -c '%a' "$target")"
    install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$file_mode" "$temporary" "$target"
done

"$PHP_BIN" -l "$MOODLE_ROOT/$CATEGORY_PATH" >/dev/null
"$PHP_BIN" -l "$MOODLE_ROOT/$GUIDE_PATH" >/dev/null
python3 -m json.tool "$MOODLE_ROOT/$PROPERTIES_PATH" >/dev/null

if [[ -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    "$PHP_BIN" "$MOODLE_ROOT/admin/cli/purge_caches.php"
fi

echo "CATEGORÍA 11 ACTUALIZADA CORRECTAMENTE"
echo "Con asterisco: Gan Sui y Ba Dou"
echo "Sin asterisco: Hong Da Ji, Yuan Hua y Qian Niu Zi"
echo "Propiedades, identidad botánica, dosis y advertencias auditadas"
echo "Respaldo: $BACKUP_DIR"
