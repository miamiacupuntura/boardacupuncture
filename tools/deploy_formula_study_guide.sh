#!/usr/bin/env bash

set -euo pipefail

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
TARGET_DIR="$MOODLE_ROOT/blocks/myprogress"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups}"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash deploy_formula_study_guide.sh COMMIT_DE_40_CARACTERES"
    exit 1
fi

if [[ ! -f "$MOODLE_ROOT/config.php" || ! -d "$TARGET_DIR" ]]; then
    echo "ERROR: no se encontró la instalación activa de Moodle en $MOODLE_ROOT"
    exit 1
fi

TEMP_DIR="$(mktemp -d)"
BACKUP_DIR="$BACKUP_ROOT/boardacupuncture-formulas-$(date +%Y%m%d-%H%M%S)"
trap 'rm -rf "$TEMP_DIR"' EXIT

curl -fsSL \
    "$REPOSITORY/$REVISION/blocks/myprogress/formulas-study-guide.php" \
    -o "$TEMP_DIR/formulas-study-guide.php"
curl -fsSL \
    "$REPOSITORY/$REVISION/blocks/myprogress/formulas-study-guide-data.json" \
    -o "$TEMP_DIR/formulas-study-guide-data.json"
curl -fsSL \
    "$REPOSITORY/$REVISION/blocks/myprogress/study-guide.php" \
    -o "$TEMP_DIR/study-guide.php"

php -l "$TEMP_DIR/formulas-study-guide.php" >/dev/null
php -l "$TEMP_DIR/study-guide.php" >/dev/null
php -r '
    $data = json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR);
    $chapter8 = array_filter(
        $data["supplementalFormulas"] ?? [],
        static fn(array $formula): bool => str_ends_with($formula["categoryKey"] ?? "", "-ch8")
    );
    $chapter9 = array_filter(
        $data["supplementalFormulas"] ?? [],
        static fn(array $formula): bool => ($formula["categoryKey"] ?? "") === "stabilize-bind-ch9"
    );
    if (count($data["categories"] ?? []) !== 14
            || count($data["formulas"] ?? []) !== 63
            || count($data["supplementalCategories"] ?? []) !== 10
            || count($data["supplementalFormulas"] ?? []) !== 84
            || count($chapter8) !== 45
            || count($chapter9) !== 15) {
        fwrite(STDERR, "ERROR: los datos de fórmulas no pasaron la validación.\n");
        exit(1);
    }
' "$TEMP_DIR/formulas-study-guide-data.json"

grep -q "tonify-chapter-8" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Complete Chapter 8" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Listado completo de fórmulas que tonifican" "$TEMP_DIR/formulas-study-guide.php"
grep -q "stabilize-bind-chapter-9" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Complete Chapter 9" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Listado completo del capítulo 9" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Bensky et al., Chinese Herbal Medicine: Formulas & Strategies" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Dominio Board · Fórmula #" "$TEMP_DIR/formulas-study-guide.php"
grep -q "← Chinese Herbal Medicine" "$TEMP_DIR/formulas-study-guide.php"
grep -q "← Medicina Herbal China" "$TEMP_DIR/formulas-study-guide.php"
grep -q "\$languageurl" "$TEMP_DIR/study-guide.php"
grep -q "Español" "$TEMP_DIR/study-guide.php"
grep -q "← Chinese Herbal Medicine" "$TEMP_DIR/study-guide.php"
grep -q "← Medicina Herbal China" "$TEMP_DIR/study-guide.php"

mkdir -p "$BACKUP_DIR"
cp -a "$TARGET_DIR/formulas-study-guide.php" "$BACKUP_DIR/"
cp -a "$TARGET_DIR/formulas-study-guide-data.json" "$BACKUP_DIR/"
cp -a "$TARGET_DIR/study-guide.php" "$BACKUP_DIR/"

PHP_OWNER="$(stat -c '%U' "$TARGET_DIR/formulas-study-guide.php")"
PHP_GROUP="$(stat -c '%G' "$TARGET_DIR/formulas-study-guide.php")"
JSON_OWNER="$(stat -c '%U' "$TARGET_DIR/formulas-study-guide-data.json")"
JSON_GROUP="$(stat -c '%G' "$TARGET_DIR/formulas-study-guide-data.json")"
STUDY_OWNER="$(stat -c '%U' "$TARGET_DIR/study-guide.php")"
STUDY_GROUP="$(stat -c '%G' "$TARGET_DIR/study-guide.php")"

install -m 0644 -o "$PHP_OWNER" -g "$PHP_GROUP" \
    "$TEMP_DIR/formulas-study-guide.php" "$TARGET_DIR/formulas-study-guide.php"
install -m 0644 -o "$JSON_OWNER" -g "$JSON_GROUP" \
    "$TEMP_DIR/formulas-study-guide-data.json" "$TARGET_DIR/formulas-study-guide-data.json"
install -m 0644 -o "$STUDY_OWNER" -g "$STUDY_GROUP" \
    "$TEMP_DIR/study-guide.php" "$TARGET_DIR/study-guide.php"

if [[ "${SKIP_CACHE_PURGE:-0}" != "1" ]]; then
    if [[ "$(id -u)" -eq 0 ]] && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    else
        php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    fi
fi

grep -q "tonify-chapter-8" "$TARGET_DIR/formulas-study-guide.php"
grep -q "Español" "$TARGET_DIR/study-guide.php"
grep -q "← Chinese Herbal Medicine" "$TARGET_DIR/formulas-study-guide.php"
grep -q "← Chinese Herbal Medicine" "$TARGET_DIR/study-guide.php"

echo "CAPÍTULOS 8 Y 9 INSTALADOS EN MOODLE"
echo "45 fórmulas en 6 secciones: Qi, Sangre, Qi-Sangre, Yin, Yang y Yin-Yang"
echo "CAPÍTULO 9 INSTALADO: 17 fórmulas en 5 secciones (15 perfiles nuevos + 2 Board)"
echo "BOTÓN ESPAÑOL INSTALADO EN LA GUÍA DE HERBOLOGÍA"
echo "NOMBRE UNIFORME INSTALADO: Chinese Herbal Medicine / Medicina Herbal China"
echo "Respaldo: $BACKUP_DIR"
