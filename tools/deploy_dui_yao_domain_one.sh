#!/usr/bin/env bash

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="/var/www/html/moodle"
COMPARISON_ROOT="/var/www/acupuncturetests-study-guide/current/comparisons"

if [ -z "$REVISION" ]; then
    echo "Uso: bash deploy_dui_yao_domain_one.sh REVISION"
    exit 1
fi

if [ ! -f "$MOODLE_ROOT/config.php" ]; then
    echo "No se encontró Moodle en $MOODLE_ROOT"
    exit 1
fi

if [ ! -d "$COMPARISON_ROOT" ]; then
    echo "No se encontró el directorio estático en $COMPARISON_ROOT"
    exit 1
fi

FILES="
advanced-name-traps-es.html
advanced-name-traps.html
dui-yao-es.html
dui-yao.html
families-es.html
families.html
formula-name-traps-es.html
formula-name-traps.html
formula-navigator-es.html
formula-theory-es.html
formula-theory.html
formulas-es.html
formulas.html
herb-families-es.html
herb-families.html
herbal-incompatibilities-es.html
herbal-incompatibilities.html
index-es.html
index.html
misleading-formula-names-es.html
misleading-formula-names.html
most-important-formulas-es.html
most-important-formulas.html
name-traps-es.html
name-traps.html
pathologies-es.html
pathologies.html
patterns-es.html
patterns-tips-maciocia-es.html
patterns-tips-maciocia.html
patterns.html
safety-es.html
safety.html
secondary-actions-es.html
secondary-actions.html
supplemental-herb-families-es.html
supplemental-herb-families.html
"

TEMP_DIR="$(mktemp -d)"
BACKUP_DIR="/var/backups/boardacupuncture-dui-yao-$(date +%Y%m%d-%H%M%S)"
mkdir -p "$TEMP_DIR/comparisons" "$BACKUP_DIR/comparisons"

if ! curl -fsSL "$REPOSITORY/$REVISION/blocks/myprogress/study-guide.php" -o "$TEMP_DIR/study-guide.php"; then
    echo "No se pudo descargar study-guide.php"
    exit 1
fi

for FILE in $FILES; do
    if ! curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/$FILE" -o "$TEMP_DIR/comparisons/$FILE"; then
        echo "No se pudo descargar $FILE"
        exit 1
    fi
done

if ! grep -q "DOMAIN I · HIGH PRIORITY" "$TEMP_DIR/study-guide.php"; then
    echo "La tarjeta principal de Dui Yao no pasó la verificación."
    exit 1
fi

if grep -qE 'href="dui-yao(-es)?\.html">(Duì Yào Pairs|Parejas Duì Yào)</a>' "$TEMP_DIR/comparisons/index.html" "$TEMP_DIR/comparisons/index-es.html"; then
    echo "Dui Yao todavía aparece dentro de la navegación secundaria."
    exit 1
fi

if [ -f "$MOODLE_ROOT/blocks/myprogress/study-guide.php" ]; then
    cp -a "$MOODLE_ROOT/blocks/myprogress/study-guide.php" "$BACKUP_DIR/study-guide.php"
fi

for FILE in $FILES; do
    if [ -f "$COMPARISON_ROOT/$FILE" ]; then
        cp -a "$COMPARISON_ROOT/$FILE" "$BACKUP_DIR/comparisons/$FILE"
    fi
done

install -m 0644 "$TEMP_DIR/study-guide.php" "$MOODLE_ROOT/blocks/myprogress/study-guide.php"
for FILE in $FILES; do
    install -m 0644 "$TEMP_DIR/comparisons/$FILE" "$COMPARISON_ROOT/$FILE"
done

if command -v php >/dev/null 2>&1; then
    sudo -u www-data php "$MOODLE_ROOT/admin/cli/purge_caches.php" || echo "Aviso: no se pudo purgar la caché de Moodle."
fi

echo "DUI YAO MOVIDO A CHINESE HERBOLOGY"
echo "Respaldo: $BACKUP_DIR"
