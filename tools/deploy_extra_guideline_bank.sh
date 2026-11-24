#!/usr/bin/env bash

set -euo pipefail

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
FILES=(
    "blocks/myprogress/admin-herbology-question-bank.php"
    "blocks/myprogress/herbology-question-overrides.php"
    "blocks/menu/herbology-extra-guideline.php"
    "blocks/menu/herbology-board.php"
)
NEW_FILES=(
    "blocks/menu/herbology-extra-guideline.php"
)

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash deploy_extra_guideline_bank.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi

for command in curl php grep install stat cp cmp mkdir mktemp date rm mv dirname basename id; do
    command -v "$command" >/dev/null 2>&1 || {
        echo "ERROR: falta el comando $command" >&2
        exit 1
    }
done

is_new_file() {
    local candidate="$1"
    local new_file
    for new_file in "${NEW_FILES[@]}"; do
        [[ "$candidate" == "$new_file" ]] && return 0
    done
    return 1
}

for relative in "${FILES[@]}"; do
    [[ -f "$MOODLE_ROOT/$relative" ]] || is_new_file "$relative" || {
        echo "ERROR: no existe $MOODLE_ROOT/$relative" >&2
        exit 1
    }
done

php -r '
$data = json_decode((string)file_get_contents($argv[1]), true);
exit(count((array)($data["pairs"] ?? [])) === 51 ? 0 : 1);
' "$MOODLE_ROOT/blocks/myprogress/dui-yao-smart-data.json" || {
    echo "ERROR: se requiere la tabla vigente de 51 Duì Yào." >&2
    exit 1
}
php -r '
$data = json_decode((string)file_get_contents($argv[1]), true);
exit(count((array)($data["formulas"] ?? [])) === 63 ? 0 : 1);
' "$MOODLE_ROOT/blocks/myprogress/formulas-study-guide-data.json" || {
    echo "ERROR: se requieren los 63 perfiles profundos de fórmulas para elegir una base compatible." >&2
    exit 1
}

TEMP_DIR="$(mktemp -d)"
BACKUP_DIR="$BACKUP_ROOT/extra-guideline-bank-$(date +%Y%m%d-%H%M%S)"
cleanup() {
    rm -rf -- "$TEMP_DIR"
}
trap cleanup EXIT
trap 'status=$?; echo "ERROR: despliegue de Extra guideline detenido en la línea $LINENO (código $status)." >&2; exit "$status"' ERR

for relative in "${FILES[@]}"; do
    source_file="$TEMP_DIR/$relative"
    mkdir -p "$(dirname "$source_file")"
    curl -fsSL \
        -H 'Cache-Control: no-cache' \
        -H 'Pragma: no-cache' \
        "$REPOSITORY/$REVISION/$relative?revision=$REVISION" \
        -o "$source_file"
    php -l "$source_file" >/dev/null
done

grep -Fq 'name="saveoutsideguideline"' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'Guardar en Extra guideline' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'name="formaction" value="saveextraguideline"' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'Extra guideline bank (<?php echo (int)$extraguidelinecount; ?>)' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
extra_button_count="$(grep -Fc 'name="formaction" value="saveextraguideline"' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php")"
[[ "$extra_button_count" -ge 2 ]]
grep -Fq "'status' => \$saveoutsideguideline ? 'extra_guideline' : 'draft'" "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq '2026-11-23-extra-guideline-bank-v1' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq "'su ye' => 'zi su ye'" "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq "'status' => 'approved_with_warning'" "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'Publicar con advertencia' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
warning_button_count="$(grep -Fc 'Publicar con advertencia' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php")"
[[ "$warning_button_count" -ge 3 ]]
grep -Fq '$questionactionexamproject = is_array($currentexamproject)' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'Published in the bank. Use “Add to ' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'name="reviewforextraguideline"' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'Review for Extra guideline' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq "\$record['reviewDestination']" "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq "ncbahm-mock-editor-5.0" "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'manifestation-only choices' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'lastAutomaticReviewFailure' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'View Extra guideline' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'Back to Exam builder' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'name="formaction" value="publishextraguideline"' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'Publish collection' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq "'appendix-a-current-roster'" "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq "'dui-yao-annex'" "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq "'guide-formula-roster-title'" "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'Appendix A · Herbs' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'Appendix B · Duì Yào' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'Appendix C · Formulas' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'name="autorepairguideline" value="1"' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'Corregir guideline automáticamente' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'Mentioning an invalid "herb or formula used as a distractor" is not a' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'out-of-roster name used as a distractor' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'allowedAppendixCFormulas' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'The Board draft still contains names outside Appendix A or Appendix C' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq '$questionpriorexamlabels' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq '$questionassignedtoexamlookup' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'The active bank must never show a question that already belongs to' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'Available questions' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq '$projectapprovedcandidateids' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'removed from Temporary Review Order' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'Cannot reuse · ' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'The question is already published, but it cannot be added again:' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq '$findkeyedanswerguidelineviolations' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'Automatic Board repair is unavailable:' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq '$cardguidelineextraautoreview' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'Automatically review is still available:' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq '$preservecorrectchoice' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'if ($editquestion !== null):' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'builtin-two-stage-dui-yao-formula-modification' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'Clinical return: modify a formula with a Dui Yao' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'SELECTED VERIFIED CLINICAL-RETURN EVIDENCE' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'Each question must use one of the 51 official Appendix B pairs' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq "'kind' => 'individualized'" "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq '$twostageinstructioncontentdetected' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'or when a link/form lost its hidden template identifier' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq '$clinicalreturnfailures' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'The clinical-return draft still needs correction:' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq "\$decodeditem['contentType'] = 'dui_yao';" "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq '$autorepairfeedbacken' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'Completed the clinical-return answer and bilingual feedback' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq "'publishedExtraGuideline' => null" "$TEMP_DIR/blocks/myprogress/herbology-question-overrides.php"
grep -Fq 'Mock-only, Extra guideline and recycled administrator questions' "$TEMP_DIR/blocks/myprogress/herbology-question-overrides.php"
grep -Fq 'Student view for the separately published Extra guideline collection' "$TEMP_DIR/blocks/menu/herbology-extra-guideline.php"
grep -Fq 'Check answer' "$TEMP_DIR/blocks/menu/herbology-extra-guideline.php"
grep -Fq 'Open Extra guideline →' "$TEMP_DIR/blocks/menu/herbology-board.php"

mkdir -p "$BACKUP_DIR"
for relative in "${FILES[@]}"; do
    target="$MOODLE_ROOT/$relative"
    source_file="$TEMP_DIR/$relative"
    backup="$BACKUP_DIR/$relative"
    staged="$MOODLE_ROOT/$(dirname "$relative")/.$(basename "$relative").extra-guideline.$$"
    if [[ -f "$target" ]]; then
        mkdir -p "$(dirname "$backup")"
        cp -a -- "$target" "$backup"
        owner_group="$(stat -c '%u:%g' "$target")"
        mode="$(stat -c '%a' "$target")"
    else
        owner_group="$(stat -c '%u:%g' "$(dirname "$target")")"
        mode="0644"
    fi
    install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" "$source_file" "$staged"
    cmp -s "$source_file" "$staged"
    mv -f -- "$staged" "$target"
    cmp -s "$source_file" "$target"
done

for relative in "${FILES[@]}"; do
    php -l "$MOODLE_ROOT/$relative" >/dev/null
done
grep -Fq 'Guardar en Extra guideline' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'name="formaction" value="saveextraguideline"' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
installed_button_count="$(grep -Fc 'name="formaction" value="saveextraguideline"' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php")"
[[ "$installed_button_count" -ge 2 ]]
grep -Fq '2026-11-23-extra-guideline-bank-v1' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq "'su ye' => 'zi su ye'" "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq "'status' => 'approved_with_warning'" "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'Publicar con advertencia' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq '$questionactionexamproject = is_array($currentexamproject)' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'Published in the bank. Use “Add to ' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'name="reviewforextraguideline"' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'Review for Extra guideline' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq "\$record['reviewDestination']" "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq "ncbahm-mock-editor-5.0" "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'manifestation-only choices' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'lastAutomaticReviewFailure' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'View Extra guideline' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'Back to Exam builder' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'name="formaction" value="publishextraguideline"' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq "'appendix-a-current-roster'" "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq "'dui-yao-annex'" "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq "'guide-formula-roster-title'" "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'name="autorepairguideline" value="1"' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'Corregir guideline automáticamente' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'Mentioning an invalid "herb or formula used as a distractor" is not a' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'out-of-roster name used as a distractor' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'allowedAppendixCFormulas' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'The Board draft still contains names outside Appendix A or Appendix C' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq '$questionpriorexamlabels' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq '$questionassignedtoexamlookup' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'The active bank must never show a question that already belongs to' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'Available questions' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq '$projectapprovedcandidateids' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'removed from Temporary Review Order' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'Cannot reuse · ' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'The question is already published, but it cannot be added again:' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq '$findkeyedanswerguidelineviolations' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'Automatic Board repair is unavailable:' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq '$cardguidelineextraautoreview' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'Automatically review is still available:' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq '$preservecorrectchoice' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'if ($editquestion !== null):' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'builtin-two-stage-dui-yao-formula-modification' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'Clinical return: modify a formula with a Dui Yao' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'SELECTED VERIFIED CLINICAL-RETURN EVIDENCE' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'Each question must use one of the 51 official Appendix B pairs' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq "'kind' => 'individualized'" "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq '$twostageinstructioncontentdetected' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'or when a link/form lost its hidden template identifier' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq '$clinicalreturnfailures' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'The clinical-return draft still needs correction:' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq "\$decodeditem['contentType'] = 'dui_yao';" "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq '$autorepairfeedbacken' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'Completed the clinical-return answer and bilingual feedback' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'Student view for the separately published Extra guideline collection' "$MOODLE_ROOT/blocks/menu/herbology-extra-guideline.php"
grep -Fq 'Open Extra guideline →' "$MOODLE_ROOT/blocks/menu/herbology-board.php"

if [[ -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    if command -v runuser >/dev/null 2>&1 && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    else
        php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    fi
fi

if command -v systemctl >/dev/null 2>&1 && systemctl is-active --quiet apache2.service; then
    systemctl reload apache2.service
    echo "RUNTIME RECARGADO: apache2.service"
fi

echo "BANCO EXTRA GUIDELINE INSTALADO"
echo "OK: las preguntas fuera del roster se guardan en un banco administrativo independiente."
echo "OK: el acceso general y el botón de cada tarjeta se muestran en el banco y en Temporary Review Order."
echo "OK: no se envían a Board, Mock ni Simulation Exams."
echo "OK: los registros creados por la versión anterior se separan automáticamente de Mock."
echo "OK: Su Ye se reconoce como alias válido de Zi Su Ye."
echo "OK: un administrador puede publicar con advertencia; usuario, fecha, alerta y versión quedan auditados."
echo "OK: la excepción auditada se respeta al agregar y publicar Simulation Exams."
echo "OK: después de un merge se conserva el contexto y se muestra Agregar al examen activo."
echo "OK: la revisión automática puede completar una pregunta fuera del roster y dejarla lista para Extra guideline."
echo "OK: si la revisión falla, la tarjeta conserva y muestra la causa exacta para corregirla."
echo "OK: Exam builder muestra el total de Extra guideline y permite abrirlo y regresar."
echo "OK: las preguntas de manifestaciones conservan opciones clínicas y no exigen un mapeo falso de fórmulas."
echo "OK: Extra guideline se publica como una colección estudiantil separada, con versión y preguntas pendientes visibles."
echo "OK: el banco administrativo enlaza directamente a Appendix A, Appendix B y Appendix C y conserva el regreso."
echo "OK: toda revisión pendiente permite abrir el editor manual y reemplazar distractores directamente."
echo "OK: la revisión Board reemplaza automáticamente distractores fuera del roster, vuelve a validar Appendix A/C y deja el resultado sin publicar para aprobación."
echo "OK: las preguntas ya publicadas muestran en qué examen se usaron y explican por qué no pueden repetirse en el examen activo."
echo "OK: las preguntas asignadas a cualquier examen desaparecen del banco disponible y de las vistas de candidatas de los demás exámenes."
echo "OK: las preguntas ya aprobadas se incorporan al examen activo y no reaparecen en Temporary Review Order."
echo "OK: la reparación de un distractor de fórmula no se confunde con una solicitud de añadir hierbas visibles."
echo "OK: la reparación automática bloquea por código cualquier cambio de la respuesta correcta y no se ofrece cuando la respuesta correcta está fuera del guideline."
echo "OK: si la respuesta correcta está fuera del roster, Automatically review conserva la clave y completa feedback/español en modo Extra sin publicar."
echo "OK: la plantilla clínica de regreso rota entre los 51 Duì Yào oficiales; usa nombre clásico solo en 4 adiciones directas y conserva las demás como prescripciones individualizadas."
echo "OK: la plantilla clínica se reconoce por identificador o por su contenido; nunca vuelve silenciosamente al formato teórico fórmula versus pareja."
echo "OK: la validación clínica acepta redacción equivalente y, si falla, enumera exactamente el componente que falta en lugar de exigir frases literales."
echo "OK: el sistema completa localmente la opción correcta y el análisis bilingüe A-D con la evidencia verificada antes de validar el borrador."
echo "RESPALDO: $BACKUP_DIR"
