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
REFERENCE_PATH="blocks/myprogress/single-herbs-dui-yao-reference.php"
DUI_DATA_PATH="study-guide/comparisons/dui-yao-data.json"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash deploy_harsh_expellants_audit.sh COMMIT_DE_40_CARACTERES"
    exit 1
fi

if [[ ! -f "$MOODLE_ROOT/config.php" ]]; then
    echo "ERROR: no se encontró Moodle en $MOODLE_ROOT"
    exit 1
fi

for relative_path in "$CATEGORY_PATH" "$GUIDE_PATH" "$PROPERTIES_PATH" "$DUI_DATA_PATH"; do
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
BACKUP_DIR="$BACKUP_ROOT/single-herbs-dui-yao-$(date +%Y%m%d-%H%M%S)"
trap 'rm -rf "$TEMP_DIR"' EXIT

echo "Moodle detectado: $MOODLE_ROOT"
echo "PHP CLI detectado: $PHP_BIN"
echo "Descargando referencias Duì Yào para todas las categorías desde $REVISION..."

for relative_path in "$CATEGORY_PATH" "$GUIDE_PATH" "$PROPERTIES_PATH" "$REFERENCE_PATH" "$DUI_DATA_PATH"; do
    mkdir -p "$TEMP_DIR/$(dirname "$relative_path")"
    curl -fsSL "$REPOSITORY/$REVISION/$relative_path" -o "$TEMP_DIR/$relative_path"
done

"$PHP_BIN" -l "$TEMP_DIR/$CATEGORY_PATH" >/dev/null
"$PHP_BIN" -l "$TEMP_DIR/$GUIDE_PATH" >/dev/null
"$PHP_BIN" -l "$TEMP_DIR/$REFERENCE_PATH" >/dev/null
python3 -m json.tool "$TEMP_DIR/$PROPERTIES_PATH" >/dev/null
python3 -m json.tool "$TEMP_DIR/$DUI_DATA_PATH" >/dev/null

EXPECTED_TITLE='Downward Draining Harsh Expellants (Cathartics) (Gan Sui*, Hong Da Ji, Yuan Hua, Qian Niu Zi, Ba Dou*)'
grep -Fq "$EXPECTED_TITLE" "$TEMP_DIR/$GUIDE_PATH"
grep -Fq "Only Gan Sui and Ba Dou are in the current Board guideline" "$TEMP_DIR/$CATEGORY_PATH"
grep -Fq "single-herbs-dui-yao-reference.php" "$TEMP_DIR/$GUIDE_PATH"
grep -Fq "No match in the current 51-pair Duì Yào roster" "$TEMP_DIR/$REFERENCE_PATH"
grep -Fq "Gan Sui–Gan Cao and Ba Dou–Qian Niu Zi are incompatibility or prohibited-combination relationships, not Duì Yào" "$TEMP_DIR/$REFERENCE_PATH"
grep -Fq "data-dui-yao-pair" "$TEMP_DIR/$REFERENCE_PATH"
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

DUI_PAIR_COUNT="$(python3 - "$TEMP_DIR/$DUI_DATA_PATH" <<'PY'
import json
import sys
with open(sys.argv[1], encoding='utf-8') as source:
    print(len(json.load(source)))
PY
)"
if [[ "$DUI_PAIR_COUNT" -ne 51 ]]; then
    echo "ERROR: se esperaban 51 parejas Duì Yào y se detectaron $DUI_PAIR_COUNT"
    exit 1
fi

mkdir -p "$BACKUP_DIR"
for relative_path in "$CATEGORY_PATH" "$GUIDE_PATH" "$PROPERTIES_PATH" "$REFERENCE_PATH" "$DUI_DATA_PATH"; do
    target="$MOODLE_ROOT/$relative_path"
    temporary="$TEMP_DIR/$relative_path"
    backup_target="$BACKUP_DIR/$relative_path"
    if [[ -f "$target" ]]; then
        mkdir -p "$(dirname "$backup_target")"
        cp -a "$target" "$backup_target"
        owner_group="$(stat -c '%u:%g' "$target")"
        file_mode="$(stat -c '%a' "$target")"
    else
        owner_group="$(stat -c '%u:%g' "$MOODLE_ROOT/$GUIDE_PATH")"
        file_mode="644"
    fi
    mkdir -p "$(dirname "$target")"
    install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$file_mode" "$temporary" "$target"
done

"$PHP_BIN" -l "$MOODLE_ROOT/$CATEGORY_PATH" >/dev/null
"$PHP_BIN" -l "$MOODLE_ROOT/$GUIDE_PATH" >/dev/null
"$PHP_BIN" -l "$MOODLE_ROOT/$REFERENCE_PATH" >/dev/null
python3 -m json.tool "$MOODLE_ROOT/$PROPERTIES_PATH" >/dev/null
python3 -m json.tool "$MOODLE_ROOT/$DUI_DATA_PATH" >/dev/null

if [[ -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    "$PHP_BIN" "$MOODLE_ROOT/admin/cli/purge_caches.php"
fi

echo "REFERENCIAS DUÌ YÀO INSTALADAS EN TODAS LAS CATEGORÍAS"
echo "Las coincidencias se calculan desde las 51 parejas vigentes"
echo "Las categorías sin pareja muestran una nota breve con enlace al índice"
echo "Con asterisco: Gan Sui y Ba Dou"
echo "Sin asterisco: Hong Da Ji, Yuan Hua y Qian Niu Zi"
echo "Duì Yào: ninguna de estas cinco hierbas pertenece a las 51 parejas vigentes"
echo "Propiedades, identidad botánica, dosis y advertencias auditadas"
echo "Respaldo: $BACKUP_DIR"
