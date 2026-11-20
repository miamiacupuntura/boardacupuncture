#!/usr/bin/env bash
set -euo pipefail

# Deploy the completed Exam 3 publication flow without touching unrelated
# Study Guide assets.

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
FILES=(
    blocks/myprogress/admin-herbology-question-bank.php
    blocks/menu/herbology-board.php
    blocks/menu/herbology-exam-history.php
    blocks/menu/exam-history.php
    blocks/menu/herbology-simulation.js
    blocks/myprogress/study-guide.php
    blocks/myprogress/my-evaluations.php
    blocks/myprogress/admin-ncbahm-evaluations.php
)

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash /tmp/deploy_ncbahm_exam3.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi

for command in curl php grep install stat cp cmp mkdir mktemp date rm mv id dirname; do
    command -v "$command" >/dev/null 2>&1 || {
        echo "ERROR: falta el comando $command" >&2
        exit 1
    }
done

TEMP_DIR="$(mktemp -d)"
TIMESTAMP="$(date +%Y%m%d-%H%M%S)"
BACKUP_DIR="$BACKUP_ROOT/ncbahm-exam3-$TIMESTAMP"
INSTALLED_FILES=()

cleanup() {
    rm -rf -- "$TEMP_DIR"
}

on_error() {
    status=$?
    line=$1
    for relative in "${INSTALLED_FILES[@]}"; do
        if [[ -f "$BACKUP_DIR/$relative" ]]; then
            cp -a -- "$BACKUP_DIR/$relative" "$MOODLE_ROOT/$relative"
        fi
    done
    cleanup
    echo "ERROR: despliegue de Exam 3 detenido en la línea $line (código $status)." >&2
    exit "$status"
}

trap 'on_error $LINENO' ERR
trap cleanup EXIT

for relative in "${FILES[@]}"; do
    target="$MOODLE_ROOT/$relative"
    source="$TEMP_DIR/$relative"
    [[ -f "$target" ]] || {
        echo "ERROR: no existe $target" >&2
        exit 1
    }
    mkdir -p "$(dirname "$source")" "$BACKUP_DIR/$(dirname "$relative")"
    curl -fsSL \
        -H 'Cache-Control: no-cache' \
        -H 'Pragma: no-cache' \
        "$REPOSITORY/$REVISION/$relative?revision=$REVISION" \
        -o "$source"
    if [[ "$relative" == *.php ]]; then
        php -l "$source" >/dev/null
    fi
    cp -a -- "$target" "$BACKUP_DIR/$relative"
done

grep -Fq "\$publishexamprojectrequested = optional_param('publishexamproject'" "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq "'publishexamproject' => \$projectid" "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq "\$publishexamerror = optional_param('publishexamerror'" "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'Ready to publish: 100 of 100 approved.' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'required: 28/24/18/15/10/5' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq '$publishedsimulationexams' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq "'simulation3linear'" "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq '$isdynamicpublishedexam' "$TEMP_DIR/blocks/menu/herbology-exam-history.php"
grep -Fq '$dynamicattemptmatch' "$TEMP_DIR/blocks/menu/exam-history.php"
grep -Fq 'Number.parseInt(exam.dataset.examNumber' "$TEMP_DIR/blocks/menu/herbology-simulation.js"
grep -Fq '$publishedexamnumbers' "$TEMP_DIR/blocks/myprogress/study-guide.php"
grep -Fq "preg_match('/^simulation(\\d+)(linear|adaptive)\$/'" "$TEMP_DIR/blocks/myprogress/my-evaluations.php"

for relative in "${FILES[@]}"; do
    target="$MOODLE_ROOT/$relative"
    source="$TEMP_DIR/$relative"
    staged="$(dirname "$target")/.$(basename "$target").exam3.$$"
    owner_group="$(stat -c '%u:%g' "$target")"
    mode="$(stat -c '%a' "$target")"
    install \
        -o "${owner_group%:*}" \
        -g "${owner_group#*:}" \
        -m "$mode" \
        "$source" \
        "$staged"
    cmp -s "$source" "$staged"
    if [[ "$relative" == *.php ]]; then
        php -l "$staged" >/dev/null
    fi
    mv -f -- "$staged" "$target"
    INSTALLED_FILES+=("$relative")
    cmp -s "$source" "$target"
done

if [[ -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    if command -v runuser >/dev/null 2>&1 && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    else
        php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    fi
fi

if command -v systemctl >/dev/null 2>&1; then
    while IFS= read -r php_service; do
        [[ -n "$php_service" ]] || continue
        systemctl reload "$php_service" >/dev/null 2>&1 \
            || systemctl restart "$php_service" >/dev/null 2>&1 \
            || true
    done < <(systemctl list-units --type=service --state=running --no-legend 2>/dev/null \
        | awk '$1 ~ /^php[0-9.]+-fpm\.service$/ {print $1}')
fi

echo "OK: Exam 3 puede publicarse desde Exam builder al completar 100/100."
echo "OK: Exam 3 aparece en Simulations, Exams, historial y Mock Exam inventory."
echo "OK: revisión instalada: $REVISION"
echo "RESPALDO: $BACKUP_DIR"
