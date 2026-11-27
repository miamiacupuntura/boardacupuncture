#!/usr/bin/env bash
set -euo pipefail
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
TARGET="$MOODLE_ROOT/blocks/myprogress/herbology-question-overrides.php"
for tool in python3 php cp stat install mktemp date; do
    command -v "$tool" >/dev/null || { echo "ERROR: falta $tool" >&2; exit 1; }
done
TEMP_DIR="$(mktemp -d)"
trap 'rm -rf -- "$TEMP_DIR"' EXIT
python3 - "$TARGET" "$TEMP_DIR/overrides.php" <<'PY'
from pathlib import Path
import sys
source=Path(sys.argv[1]).read_text()
marker='// yin-chai-hu-distinct-identity-v1'
if marker in source:raise SystemExit('La distinción Yin Chai Hu / Chai Hu ya está instalada; no se cambió nada.')
start=source.find('function block_myprogress_herbology_supplemental_display_roster(): array {')
end=source.find('\n}',start)
if start<0 or end<0:raise SystemExit('ERROR: no se identificó el listado de nombres editoriales.')
entry="        ['name' => 'Yin Chai Hu', 'latin' => 'Stellariae Radix'],\n"
if entry.strip() not in source[start:end]:
    pos=source.find('    return [\n',start,end)
    if pos<0:raise SystemExit('ERROR: no se identificó el punto de actualización.')
    pos+=len('    return [\n')
    source=source[:pos]+"        // Correct display only; Yin Chai Hu remains outside the active Appendix A roster.\n"+entry+source[pos:]
anchor='        $embeddedherbformularepairs = ['
if source.count(anchor)!=1:raise SystemExit('ERROR: el normalizador no coincide con la versión esperada.')
repair=r'''
        // yin-chai-hu-distinct-identity-v1
        // Repair the imported doubled parentheses and the erroneous expansion
        // of the shorter Chai Hu identity inside the complete Yin Chai Hu name.
        $yinchairepaired = preg_replace(
            [
                '/(?<![A-Za-z0-9])Yin\s+Chai\s+Hu\s*\(\s*Bupleuri\s+Radix\s*\)'
                    . '(?:\s*\({1,3}\s*Stellariae\s+Radix\s*\){1,3})?/iu',
                '/(?<![A-Za-z0-9])Yin\s+Chai\s+Hu\s*\({1,3}\s*Stellariae\s+Radix\s*\){1,3}/iu',
            ],
            'Yin Chai Hu (Stellariae Radix)',
            $value
        );
        if (is_string($yinchairepaired)) { $value = $yinchairepaired; }
'''
source=source.replace(anchor,repair+anchor,1)
Path(sys.argv[2]).write_text(source)
print('Preparada identidad independiente: Yin Chai Hu (Stellariae Radix).')
PY
php -l "$TEMP_DIR/overrides.php"
cat > "$TEMP_DIR/test.php" <<'PHP'
<?php
define('MOODLE_INTERNAL', true);
$CFG=(object)['dirroot'=>$argv[2]];
require $argv[1];
foreach (['Yin Chai Hu ((Stellariae Radix)', 'Yin Chai Hu (Bupleuri Radix) ((Stellariae Radix)', 'Yin Chai Hu (Stellariae Radix)', 'Yin Chai Hu'] as $input) {
    $question=['prompt'=>'Choose the herb.','options'=>[['label'=>'D','text'=>$input]],'correctLabel'=>'A',
        'translations'=>['es'=>['prompt'=>'Elija la hierba.','options'=>[['label'=>'D','text'=>$input]]]]];
    $actual=block_myprogress_herbology_canonicalize_pinyin_case_in_question($question);
    if (($actual['options'][0]['text'] ?? '')!=='Yin Chai Hu (Stellariae Radix)'
            || ($actual['translations']['es']['options'][0]['text'] ?? '')!=='Yin Chai Hu (Stellariae Radix)'
            || $actual['correctLabel']!=='A') {
        throw new RuntimeException('Yin Chai Hu display regression: '.$input);
    }
}
$normal=block_myprogress_herbology_canonicalize_pinyin_case_in_question(['options'=>[['label'=>'A','text'=>'Chai Hu']]]);
if (($normal['options'][0]['text'] ?? '')!=='Chai Hu (Bupleuri Radix)') {
    throw new RuntimeException('Chai Hu identity must remain Bupleuri Radix');
}
echo "OK: identidades separadas, paréntesis corregidos y español/inglés comprobados.\n";
PHP
php "$TEMP_DIR/test.php" "$TEMP_DIR/overrides.php" "$MOODLE_ROOT"
BACKUP="${BACKUP_ROOT:-/var/backups/acupuncturetests}/yin-chai-hu-display-$(date +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP"
cp -a "$TARGET" "$BACKUP/herbology-question-overrides.php"
OWNER_GROUP="$(stat -c '%u:%g' "$TARGET")"
MODE="$(stat -c '%a' "$TARGET")"
if ! install -o "${OWNER_GROUP%:*}" -g "${OWNER_GROUP#*:}" -m "$MODE" "$TEMP_DIR/overrides.php" "$TARGET" || ! php -l "$TARGET"; then
    cp -a "$BACKUP/herbology-question-overrides.php" "$TARGET"
    echo 'ERROR: se restauró el archivo anterior.' >&2
    exit 1
fi
echo 'OK: Yin Chai Hu = Stellariae Radix; Chai Hu = Bupleuri Radix.'
echo 'OK: el roster y la respuesta correcta no se modifican; no se publica la pregunta.'
echo "RESPALDO: $BACKUP"
