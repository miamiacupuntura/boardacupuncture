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
marker='// complete-an-gong-niu-huang-formula-v1'
if marker in source:raise SystemExit('La reparación de An Gong Niu Huang Wan ya está instalada; no se cambió nada.')
start=source.find('$legacyformulaprotected = preg_replace_callback(')
end=source.find('$protectsegment,',start)
if start<0 or end<0:raise SystemExit('ERROR: no se identificó la protección de fórmulas fuente.')
block=source[start:end]
tail=")(?![A-Za-z0-9])/iu'"
if block.count(tail)!=1:raise SystemExit('ERROR: patrón de protección inesperado; no se cambió nada.')
block=block.replace(tail,r"|An\s+Gong\s+Niu\s+Huang\s+Wan"+tail,1)
source=source[:start]+block+source[end:]
anchor='        $embeddedherbformularepairs = ['
if source.count(anchor)!=1:raise SystemExit('ERROR: no se identificó la reparación de nombres.')
repair=r'''
            // complete-an-gong-niu-huang-formula-v1
            // Keep a source-supported formula intact without adding it to Appendix C.
            '/(?<![A-Za-z0-9])Calm\s+the\s+Palace\s+Pill\s+with\s+Cattle\s+Gallstone\s*\('
                . '\s*An\s+Gong\s+Niu\s+Huang(?:\s*\(\s*Bovis\s+Calculus\s*\))?\s+Wan\s*\)/iu'
                => 'An Gong Niu Huang Wan (Calm the Palace Pill with Cattle Gallstone)',
            '/(?<![A-Za-z0-9])An\s+Gong\s+Niu\s+Huang\s*\(\s*Bovis\s+Calculus\s*\)'
                . '\s+Wan(?![A-Za-z0-9])/iu' => 'An Gong Niu Huang Wan',
'''
source=source.replace(anchor,anchor+repair,1)
Path(sys.argv[2]).write_text(source)
print('Preparado nombre completo An Gong Niu Huang Wan; sin alterar elegibilidad del roster.')
PY
php -l "$TEMP_DIR/overrides.php"
cat > "$TEMP_DIR/test.php" <<'PHP'
<?php
define('MOODLE_INTERNAL',true);
$CFG=(object)['dirroot'=>$argv[2]];
require $argv[1];
$display='An Gong Niu Huang Wan (Calm the Palace Pill with Cattle Gallstone)';
foreach ([
    'Calm the Palace Pill with Cattle Gallstone (An Gong Niu Huang (Bovis Calculus) Wan)'=>$display,
    'Calm the Palace Pill with Cattle Gallstone (An Gong Niu Huang Wan)'=>$display,
    'An Gong Niu Huang (Bovis Calculus) Wan'=>'An Gong Niu Huang Wan',
    'An Gong Niu Huang Wan'=>'An Gong Niu Huang Wan',
] as $input=>$expected) {
    $q=['prompt'=>$input,'explanation'=>$input,'correctLabel'=>'A','translations'=>['es'=>['prompt'=>$input,'correctLabel'=>'A']]];
    for ($pass=0;$pass<3;$pass++) {
        $q=block_myprogress_herbology_canonicalize_pinyin_case_in_question($q);
        if ($q['prompt']!==$expected || $q['explanation']!==$expected || $q['translations']['es']['prompt']!==$expected
                || $q['correctLabel']!=='A' || $q['translations']['es']['correctLabel']!=='A') {
            throw new RuntimeException('Formula-name regression: '.$q['prompt']);
        }
    }
}
$herb=block_myprogress_herbology_canonicalize_pinyin_case_in_question(['options'=>[['label'=>'A','text'=>'Niu Huang']]]);
if ($herb['options'][0]['text']!=='Niu Huang (Bovis Calculus)') { throw new RuntimeException('Standalone herb name must retain its Latin'); }
echo "OK: fórmula intacta, Niu Huang independiente correcto y clave de respuesta conservada.\n";
PHP
php "$TEMP_DIR/test.php" "$TEMP_DIR/overrides.php" "$MOODLE_ROOT"
BACKUP="${BACKUP_ROOT:-/var/backups/acupuncturetests}/an-gong-niu-huang-name-$(date +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP"
cp -a "$TARGET" "$BACKUP/herbology-question-overrides.php"
OWNER_GROUP="$(stat -c '%u:%g' "$TARGET")"
MODE="$(stat -c '%a' "$TARGET")"
if ! install -o "${OWNER_GROUP%:*}" -g "${OWNER_GROUP#*:}" -m "$MODE" "$TEMP_DIR/overrides.php" "$TARGET" || ! php -l "$TARGET"; then
    cp -a "$BACKUP/herbology-question-overrides.php" "$TARGET"
    echo 'ERROR: se restauró el normalizador anterior.' >&2
    exit 1
fi
echo 'OK: An Gong Niu Huang Wan se mantiene como fórmula completa, sin insertar Bovis Calculus dentro del nombre.'
echo 'OK: se conservan respuesta, opciones y comprobaciones del roster; no se publica nada.'
echo "RESPALDO: $BACKUP"
