#!/usr/bin/env bash

set -euo pipefail

trap 'status=$?; echo "ERROR: despliegue detenido en la línea $LINENO (código $status)." >&2; exit $status' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups}"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash deploy_formula_scope_audit.sh COMMIT_DE_40_CARACTERES"
    exit 1
fi
if [[ ! -f "$MOODLE_ROOT/config.php" ]]; then
    echo "ERROR: no se encontró Moodle en $MOODLE_ROOT"
    exit 1
fi

FILES=(
    "blocks/myprogress/formulas-study-guide.php"
    "blocks/myprogress/formulas-study-guide-data.json"
    "blocks/myprogress/formula-board-roster-2026.json"
    "study-guide/comparisons/dui-yao.html"
    "study-guide/comparisons/dui-yao-es.html"
    "study-guide/guidelines/chinese-herbal-medicine.html"
    "study-guide/guidelines/chinese-herbal-medicine-es.html"
)

TEMP_DIR="$(mktemp -d)"
BACKUP_DIR="$BACKUP_ROOT/boardacupuncture-formula-scope-$(date +%Y%m%d-%H%M%S)"
trap 'rm -rf "$TEMP_DIR"' EXIT

for relative in "${FILES[@]}"; do
    mkdir -p "$TEMP_DIR/$(dirname "$relative")"
    curl -fsSL "$REPOSITORY/$REVISION/$relative" -o "$TEMP_DIR/$relative"
done

php -l "$TEMP_DIR/blocks/myprogress/formulas-study-guide.php" >/dev/null
grep -Fq 'href="<?php echo $indexurl->out(false); ?>" aria-current="page"' \
    "$TEMP_DIR/blocks/myprogress/formulas-study-guide.php"
php -r '
    $data = json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR);
    $roster = json_decode(file_get_contents($argv[2]), true, 512, JSON_THROW_ON_ERROR);
    $official = array_fill_keys(array_column($roster["formulas"] ?? [], "name"), true);
    $required = [
        "Run Chang Wan", "Juan Bi Tang", "Zhi Bai Di Huang Wan",
        "Qi Ju Di Huang Wan", "Tao Hong Si Wu Tang", "Gui Zhi Jia Long Gu Mu Li Tang"
    ];
    if (($roster["total"] ?? 0) !== 181 || count($official) !== 181) {
        throw new RuntimeException("El listado oficial no contiene 181 fórmulas únicas.");
    }
    foreach ($required as $name) {
        if (!isset($official[$name])) {
            throw new RuntimeException("Falta fórmula oficial: " . $name);
        }
    }
    $run = null;
    $juan = null;
    foreach ($data["supplementalFormulas"] ?? [] as $formula) {
        if (($formula["name"] ?? "") === "Run Chang Wan") {$run = $formula;}
        if (($formula["name"] ?? "") === "Juan Bi Tang") {$juan = $formula;}
    }
    if (($run["categoryKey"] ?? "") !== "official-formulas-ch3" || $juan === null) {
        throw new RuntimeException("Run Chang Wan o Juan Bi Tang siguen mal clasificadas.");
    }
    foreach ($data["supplementalCategories"] ?? [] as $category) {
        if (($category["key"] ?? "") === "official-formulas-ch3" && ($category["number"] ?? "") === "H1") {
            throw new RuntimeException("La ampliación oficial conserva la etiqueta histórica H1.");
        }
    }
' "$TEMP_DIR/blocks/myprogress/formulas-study-guide-data.json" \
  "$TEMP_DIR/blocks/myprogress/formula-board-roster-2026.json"

grep -Fq 'las cuatro variantes resultantes están en los apéndices vigentes de 2026' \
    "$TEMP_DIR/study-guide/comparisons/dui-yao-es.html"
grep -Fq 'all four resulting variants are in the current 2026 appendices' \
    "$TEMP_DIR/study-guide/comparisons/dui-yao.html"
grep -Fq 'Las 63 fichas detalladas representan la cobertura profunda actual de la plataforma' \
    "$TEMP_DIR/study-guide/guidelines/chinese-herbal-medicine-es.html"
grep -Fq 'The 63 detailed profiles represent the platform current deep coverage' \
    "$TEMP_DIR/study-guide/guidelines/chinese-herbal-medicine.html"

FORBIDDEN=(
    'Run Chang Wan was listed in NCCAOM 2020 Appendix B, but it is not in the current NCBAHM 2026 Appendix C'
    'Run Chang Wan is widely taught and used in clinical practice for dry constipation from Blood and fluid deficiency, yet the current Appendix C does not include it'
    'Run Chang Wan es una fórmula muy enseñada y utilizada en la práctica para estreñimiento seco por deficiencia de Sangre y líquidos, pero el Appendix C vigente no la incluye'
    'Juan Bi Tang is not included in the current official 2026 NCBAHM Appendix C roster'
    'Las cuatro variantes resultantes no están en el Appendix C de 2026'
    'The four resulting variants are not in 2026 Appendix C'
)
for claim in "${FORBIDDEN[@]}"; do
    if grep -R -Fq "$claim" "$TEMP_DIR"; then
        echo "ERROR: persiste una afirmación obsoleta: $claim" >&2
        exit 1
    fi
done

mkdir -p "$BACKUP_DIR"
for relative in "${FILES[@]}"; do
    target="$MOODLE_ROOT/$relative"
    if [[ -f "$target" ]]; then
        mkdir -p "$BACKUP_DIR/$(dirname "$relative")"
        cp -a "$target" "$BACKUP_DIR/$relative"
    fi
done

for relative in "${FILES[@]}"; do
    target="$MOODLE_ROOT/$relative"
    source="$TEMP_DIR/$relative"
    mkdir -p "$(dirname "$target")"
    if [[ -f "$target" ]]; then
        owner="$(stat -c '%U' "$target")"
        group="$(stat -c '%G' "$target")"
    else
        owner="www-data"
        group="www-data"
    fi
    install -m 0644 -o "$owner" -g "$group" "$source" "$target"
done

if [[ "${SKIP_CACHE_PURGE:-0}" != "1" ]]; then
    if [[ "$(id -u)" -eq 0 ]] && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    else
        php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    fi
fi

echo "AUDITORÍA DE ALCANCE INSTALADA: 181 FÓRMULAS OFICIALES"
echo "CORREGIDAS: RUN CHANG WAN, JUAN BI TANG Y 4 VARIANTES DUÌ YÀO"
echo "RESPALDO: $BACKUP_DIR"
