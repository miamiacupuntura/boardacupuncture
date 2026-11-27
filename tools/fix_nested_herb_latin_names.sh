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
marker='// nested-herb-latin-display-v1'
if marker in source:raise SystemExit('La reparación de nombres latinos anidados ya está instalada; no se cambió nada.')
anchor="""            if ($commonnameherbpattern !== '') {
                $protected = preg_replace_callback($commonnameherbpattern, $protectsegment, $value);"""
if source.count(anchor)!=1:raise SystemExit('ERROR: no se identificó la protección de nombres; no se cambió nada.')
protect=r'''
            // nested-herb-latin-display-v1
            // Exact known Latin names may contain parentheses, e.g. Gu Ya.
            // Protect them before expanding bare Pinyin, just like formulas.
            if ($parentheticalherbpattern !== '') {
                $protected = preg_replace_callback($parentheticalherbpattern, $protectsegment, $value);
                if (is_string($protected)) { $value = $protected; }
            }
'''
source=source.replace(anchor,protect+anchor,1)
anchor='        $replaced = preg_replace_callback($pattern, static function(array $match) use ($canonicalnames): string {'
if source.count(anchor)!=1:raise SystemExit('ERROR: no se identificó el final del normalizador; no se cambió nada.')
cleanup=r'''
        // Remove only repeated copies of the same known pharmaceutical name.
        // Keep its internal botanical parentheses and all following prose.
        foreach ($herbdisplays as $identity) {
            $identityname = (string)($identity['name'] ?? '');
            $identitylatin = (string)($identity['latin'] ?? '');
            if ($identityname === '' || strpos($identitylatin, '(') === false
                    || stripos($value, $identityname) === false) { continue; }
            $repeatpattern = '~(?<![A-Za-z0-9])' . preg_quote($identityname, '~')
                . '(?:\s*\(\s*' . preg_quote($identitylatin, '~') . '\s*\)){2,}~iu';
            $display = $identityname . ' (' . $identitylatin . ')';
            $cleaned = preg_replace_callback($repeatpattern, static fn(array $match): string => $display, $value);
            if (is_string($cleaned)) { $value = $cleaned; }
        }
'''
source=source.replace(anchor,cleanup+anchor,1)
Path(sys.argv[2]).write_text(source)
print('Preparada protección de nombres latinos con paréntesis internos y limpieza de repeticiones.')
PY
php -l "$TEMP_DIR/overrides.php"
cat > "$TEMP_DIR/test.php" <<'PHP'
<?php
define('MOODLE_INTERNAL',true);
$CFG=(object)['dirroot'=>$argv[2]];
require $argv[1];
$display='Gu Ya (Setariae (Oryzae) Fructus germinatus)';
foreach ([1,2,3,5] as $copies) {
    $input='Gu Ya'.str_repeat(' (Setariae (Oryzae) Fructus germinatus)',$copies);
    $q=['prompt'=>'Which herb?', 'options'=>[['label'=>'B','text'=>'Lai Fu Zi'],['label'=>'C','text'=>$input]],
        'correctLabel'=>'B', 'explanation'=>'Option C: '.$input.', although useful for food stagnation.',
        'translations'=>['es'=>['prompt'=>'¿Qué hierba?', 'options'=>[['label'=>'C','text'=>$input]],'correctLabel'=>'B',
            'explanation'=>'Opción C: '.$input.' ayuda a la digestión.']]];
    for ($pass=0;$pass<4;$pass++) {
        $q=block_myprogress_herbology_canonicalize_pinyin_case_in_question($q);
        if ($q['options'][1]['text']!==$display || $q['translations']['es']['options'][0]['text']!==$display
                || $q['correctLabel']!=='B' || $q['translations']['es']['correctLabel']!=='B'
                || $q['explanation']!=='Option C: '.$display.', although useful for food stagnation.'
                || $q['translations']['es']['explanation']!=='Opción C: '.$display.' ayuda a la digestión.') {
            throw new RuntimeException('Nested Latin display regression, copies='.$copies.', pass='.$pass);
        }
    }
}
echo "OK: Gu Ya sin duplicados en inglés/español, texto posterior intacto y respuesta B conservada.\n";
PHP
php "$TEMP_DIR/test.php" "$TEMP_DIR/overrides.php" "$MOODLE_ROOT"
BACKUP="${BACKUP_ROOT:-/var/backups/acupuncturetests}/nested-herb-latin-names-$(date +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP"
cp -a "$TARGET" "$BACKUP/herbology-question-overrides.php"
OWNER_GROUP="$(stat -c '%u:%g' "$TARGET")"
MODE="$(stat -c '%a' "$TARGET")"
if ! install -o "${OWNER_GROUP%:*}" -g "${OWNER_GROUP#*:}" -m "$MODE" "$TEMP_DIR/overrides.php" "$TARGET" || ! php -l "$TARGET"; then
    cp -a "$BACKUP/herbology-question-overrides.php" "$TARGET"
    echo 'ERROR: se restauró el normalizador anterior.' >&2
    exit 1
fi
echo 'OK: Gu Ya (Setariae (Oryzae) Fructus germinatus) aparece una sola vez.'
echo 'OK: opciones, explicaciones y traducciones normalizadas; ninguna respuesta ni publicación modificada.'
echo "RESPALDO: $BACKUP"
