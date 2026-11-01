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
    $chapter10 = array_filter(
        $data["supplementalFormulas"] ?? [],
        static fn(array $formula): bool => ($formula["categoryKey"] ?? "") === "calm-shen-ch10"
    );
    $chapter11 = array_filter(
        $data["supplementalFormulas"] ?? [],
        static fn(array $formula): bool => ($formula["categoryKey"] ?? "") === "open-orifices-ch11"
    );
    $chapter12 = array_filter(
        $data["supplementalFormulas"] ?? [],
        static fn(array $formula): bool => ($formula["categoryKey"] ?? "") === "regulate-qi-ch12"
    );
    $chapter13 = array_filter(
        $data["supplementalFormulas"] ?? [],
        static fn(array $formula): bool => ($formula["categoryKey"] ?? "") === "regulate-blood-ch13"
    );
    if (count($data["categories"] ?? []) !== 14
            || count($data["formulas"] ?? []) !== 63
            || count($data["supplementalCategories"] ?? []) !== 14
            || count($data["supplementalFormulas"] ?? []) !== 131
            || count($chapter8) !== 45
            || count($chapter9) !== 15
            || count($chapter10) !== 6
            || count($chapter11) !== 7
            || count($chapter12) !== 15
            || count($chapter13) !== 19) {
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
grep -q "Categoría oficial del Board: 3 fórmulas" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Fuente Bensky completa: 17 fórmulas" "$TEMP_DIR/formulas-study-guide.php"
grep -q "3 fórmulas Board · Capítulo 9 de Bensky: 17" "$TEMP_DIR/formulas-study-guide.php"
grep -q "calm-shen-chapter-10" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Complete Chapter 10" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Listado completo del capítulo 10" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Calmar el Shen” y “Calmar el Espíritu” se usan indistintamente" "$TEMP_DIR/formulas-study-guide.php"
grep -q "es la misma familia que el Board llama “Calm Shen”, no otra categoría" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Fuente Bensky completa: 9 fórmulas; coincidencia Board: 3" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Calmar el Shen / Calmar el Espíritu" "$TEMP_DIR/formulas-study-guide.php"
grep -q "3 fórmulas Board · Capítulo 10 de Bensky: 9" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Abrir familia unificada →" "$TEMP_DIR/formulas-study-guide.php"
grep -q "open-orifices-chapter-11" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Complete Chapter 11" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Listado completo del capítulo 11" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Dato relevante para el examen · 0 de 7 en Appendix C 2026" "$TEMP_DIR/formulas-study-guide.php"
grep -q "regulate-qi-chapter-12" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Complete Chapter 12" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Listado completo del capítulo 12" "$TEMP_DIR/formulas-study-guide.php"
grep -q "5 fórmulas Board · Capítulo 12 de Bensky: 20" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Dato relevante para el examen · 5 de 20 en Appendix C 2026" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Ver 15 fórmulas adicionales de Bensky · Capítulo 12" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Abrir las 20 fichas del capítulo 12 →" "$TEMP_DIR/formulas-study-guide.php"
! grep -q "Regulate Qi main category" "$TEMP_DIR/formulas-study-guide.php"
grep -q "regulate-blood-chapter-13" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Complete Chapter 13" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Listado completo del capítulo 13" "$TEMP_DIR/formulas-study-guide.php"
grep -q "4 fórmulas Board · Capítulo 13 de Bensky: 23" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Coincidencia con los Board Domains · 4 de 23" "$TEMP_DIR/formulas-study-guide.php"
grep -q "\$focus === 'invigorate-blood'" "$TEMP_DIR/formulas-study-guide.php"
! grep -q "Ver 19 fórmulas adicionales de Bensky · Capítulo 13" "$TEMP_DIR/formulas-study-guide.php"
! grep -q "Abrir las 23 fichas del capítulo 13 →" "$TEMP_DIR/formulas-study-guide.php"
! grep -q "Invigorate Blood main category" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Metales tóxicos históricos" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Clave de estudio (fuera del Appendix C 2026)" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Included in the current Board guideline" "$TEMP_DIR/formulas-study-guide.php"
grep -q "displayformulaname" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Bensky et al., Chinese Herbal Medicine: Formulas & Strategies" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Dominio Board · Fórmula #" "$TEMP_DIR/formulas-study-guide.php"
grep -q "← Board Guideline · Chinese Herbal Medicine" "$TEMP_DIR/formulas-study-guide.php"
grep -q "← Guía de Board · Medicina Herbal China" "$TEMP_DIR/formulas-study-guide.php"
grep -q "\$languageurl" "$TEMP_DIR/study-guide.php"
grep -q "Español" "$TEMP_DIR/study-guide.php"
grep -q "← Board Guideline · Chinese Herbal Medicine" "$TEMP_DIR/study-guide.php"
grep -q "← Guía de Board · Medicina Herbal China" "$TEMP_DIR/study-guide.php"

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
grep -q "Categoría oficial del Board: 3 fórmulas" "$TARGET_DIR/formulas-study-guide.php"
grep -q "3 fórmulas Board · Capítulo 9 de Bensky: 17" "$TARGET_DIR/formulas-study-guide.php"
grep -q "Calmar el Shen” y “Calmar el Espíritu” se usan indistintamente" "$TARGET_DIR/formulas-study-guide.php"
grep -q "Fuente Bensky completa: 9 fórmulas; coincidencia Board: 3" "$TARGET_DIR/formulas-study-guide.php"
grep -q "Calmar el Shen / Calmar el Espíritu" "$TARGET_DIR/formulas-study-guide.php"
grep -q "open-orifices-chapter-11" "$TARGET_DIR/formulas-study-guide.php"
grep -q "Dato relevante para el examen · 0 de 7 en Appendix C 2026" "$TARGET_DIR/formulas-study-guide.php"
grep -q "regulate-qi-chapter-12" "$TARGET_DIR/formulas-study-guide.php"
grep -q "5 fórmulas Board · Capítulo 12 de Bensky: 20" "$TARGET_DIR/formulas-study-guide.php"
grep -q "Dato relevante para el examen · 5 de 20 en Appendix C 2026" "$TARGET_DIR/formulas-study-guide.php"
grep -q "Ver 15 fórmulas adicionales de Bensky · Capítulo 12" "$TARGET_DIR/formulas-study-guide.php"
grep -q "Abrir las 20 fichas del capítulo 12 →" "$TARGET_DIR/formulas-study-guide.php"
! grep -q "Regulate Qi main category" "$TARGET_DIR/formulas-study-guide.php"
grep -q "regulate-blood-chapter-13" "$TARGET_DIR/formulas-study-guide.php"
grep -q "4 fórmulas Board · Capítulo 13 de Bensky: 23" "$TARGET_DIR/formulas-study-guide.php"
grep -q "Coincidencia con los Board Domains · 4 de 23" "$TARGET_DIR/formulas-study-guide.php"
grep -q "\$focus === 'invigorate-blood'" "$TARGET_DIR/formulas-study-guide.php"
! grep -q "Ver 19 fórmulas adicionales de Bensky · Capítulo 13" "$TARGET_DIR/formulas-study-guide.php"
! grep -q "Abrir las 23 fichas del capítulo 13 →" "$TARGET_DIR/formulas-study-guide.php"
! grep -q "Invigorate Blood main category" "$TARGET_DIR/formulas-study-guide.php"
grep -q "Español" "$TARGET_DIR/study-guide.php"
grep -q "← Board Guideline · Chinese Herbal Medicine" "$TARGET_DIR/formulas-study-guide.php"
grep -q "← Board Guideline · Chinese Herbal Medicine" "$TARGET_DIR/study-guide.php"

echo "CAPÍTULOS 8, 9, 10, 11, 12 Y 13 INSTALADOS EN MOODLE"
echo "45 fórmulas en 6 secciones: Qi, Sangre, Qi-Sangre, Yin, Yang y Yin-Yang"
echo "CAPÍTULO 9 INSTALADO: 17 fórmulas en 5 secciones (15 perfiles nuevos + 2 Board)"
echo "REFERENCIA CRUZADA INSTALADA: BOARD STABILIZE AND BIND (3) VS. BENSKY CAPÍTULO 9 (17; COINCIDEN 2)"
echo "TARJETA UNIFICADA INSTALADA: STABILIZE AND BIND APARECE UNA SOLA VEZ EN EL ÍNDICE"
echo "CAPÍTULO 10 INSTALADO: 9 fórmulas en 2 secciones (6 perfiles nuevos + 3 Board)"
echo "REFERENCIA TERMINOLÓGICA INSTALADA: CALM SHEN = CALM THE SPIRIT; BOARD (3) VS. BENSKY CAPÍTULO 10 (9)"
echo "TARJETA UNIFICADA INSTALADA: CALM SHEN / CALM THE SPIRIT APARECE UNA SOLA VEZ EN EL ÍNDICE"
echo "CAPÍTULO 11 INSTALADO: 7 fórmulas en 2 secciones (7 perfiles Bensky, 0 Board)"
echo "ANÁLISIS DE ALCANCE INSTALADO: METALES, ESPECIES PROTEGIDAS, INGREDIENTES REGULADOS, VÍA NASAL Y URGENCIAS"
echo "CAPÍTULO 12 INSTALADO: 20 fórmulas en 2 secciones (15 perfiles nuevos + 5 Board)"
echo "TARJETA UNIFICADA INSTALADA: REGULATE QI APARECE UNA SOLA VEZ EN EL ÍNDICE"
echo "CAPÍTULO 13 INSTALADO: 23 fórmulas en 2 secciones (19 perfiles nuevos + 4 Board)"
echo "VISTA ÚNICA INSTALADA: INVIGORATE BLOOD ABRE DIRECTAMENTE EL CAPÍTULO 13 COMPLETO"
echo "ASTERISCO INSTALADO EN NOMBRES INDIVIDUALES DE FÓRMULAS BOARD"
echo "BOTÓN ESPAÑOL INSTALADO EN LA GUÍA DE HERBOLOGÍA"
echo "NOMBRE UNIFORME INSTALADO: Board Guideline · Chinese Herbal Medicine"
echo "Respaldo: $BACKUP_DIR"
