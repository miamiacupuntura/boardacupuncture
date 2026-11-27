#!/usr/bin/env bash
set -euo pipefail
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
TARGET="$MOODLE_ROOT/blocks/myprogress/herbology-question-overrides.php"
for tool in python3 php cp stat install mktemp date cmp; do
    command -v "$tool" >/dev/null || { echo "ERROR: falta $tool" >&2; exit 1; }
done
[[ -f "$TARGET" ]] || { echo "ERROR: no existe $TARGET" >&2; exit 1; }
TEMP_DIR="$(mktemp -d)"
trap 'rm -rf -- "$TEMP_DIR"' EXIT
cat > "$TEMP_DIR/helper.php" <<'PHP'
/** Collapse imported English (Pinyin (English)) displays using known identities. */
function block_myprogress_herbology_collapse_formula_displays(string $text, array $displays): string {
    if (strpos($text, '(') === false) {
        return $text;
    }
    $searchtext = (string)preg_replace('/\s+/u', ' ', $text);
    foreach ($displays as $official) {
        $name = trim((string)($official['name'] ?? ''));
        $english = trim((string)($official['english'] ?? ''));
        if ($name === '' || $english === '' || stripos($searchtext, $name) === false) {
            continue;
        }
        $words = static function(string $value): string {
            return implode('\\s+', array_map(
                static fn(string $word): string => preg_quote($word, '~'),
                preg_split('/\s+/u', $value, -1, PREG_SPLIT_NO_EMPTY) ?: []
            ));
        };
        $namepattern = $words($name);
        $aliases = [$english];
        if ($name === 'Fang Feng Tong Sheng San') {
            $aliases[] = 'Ledebouriella Powder that Sagely Unblocks';
            $aliases[] = 'Polvo de Ledebouriella que Desbloquea Sagazmente';
        }
        $englishpattern = '(?:' . implode('|', array_map($words, $aliases)) . ')';
        $patterns = [
            '~(?<![\p{L}\p{N}])' . $englishpattern . '\s*\(\s*' . $namepattern
                . '\s*(?:\(\s*' . $englishpattern . '\s*\))?\s*\)~iu',
            '~(?<![\p{L}\p{N}])' . $namepattern . '\s*\(\s*' . $namepattern
                . '\s*(?:\(\s*' . $englishpattern . '\s*\))?\s*\)~iu',
        ];
        $display = $name . ' (' . $english . ')';
        do {
            $before = $text;
            $updated = preg_replace_callback($patterns, static fn(array $match): string => $display, $text);
            if (!is_string($updated)) {
                return $text;
            }
            $text = $updated;
        } while ($text !== $before);
    }
    return $text;
}

PHP
python3 - "$TARGET" "$TEMP_DIR/helper.php" "$TEMP_DIR/overrides.php" <<'PY'
from pathlib import Path
import sys
source=Path(sys.argv[1]).read_text()
helper=Path(sys.argv[2]).read_text()
signature='function block_myprogress_herbology_canonicalize_pinyin_case_in_question(array $question): array {'
old='return is_string($replaced) ? $replaced : $value;'
new='return block_myprogress_herbology_collapse_formula_displays(is_string($replaced) ? $replaced : $value, $formuladisplays);'
if 'function block_myprogress_herbology_collapse_formula_displays(' not in source:
    if source.count(signature)!=1 or source.count(old)!=1:
        raise SystemExit('ERROR: el normalizador no coincide con la versión esperada; no se cambió nada.')
    source=source.replace(signature,helper+signature,1).replace(old,new,1)
elif new not in source:
    raise SystemExit('ERROR: existe una reparación distinta; revisar antes de modificar.')
Path(sys.argv[3]).write_text(source)
PY
php -l "$TEMP_DIR/overrides.php"
cat > "$TEMP_DIR/test.php" <<'PHP'
<?php
define('MOODLE_INTERNAL', true);
require $argv[1];
$displays = [
    'wu zhu yu tang' => ['name'=>'Wu Zhu Yu Tang', 'english'=>'Evodia Decoction'],
    'si ni tang' => ['name'=>'Si Ni Tang', 'english'=>'Frigid Extremities Decoction'],
    'si ni san' => ['name'=>'Si Ni San', 'english'=>'Frigid Extremities Powder'],
    'fang feng tong sheng san' => ['name'=>'Fang Feng Tong Sheng San', 'english'=>'Saposhnikovia Powder That Sagely Unblocks'],
];
foreach ($displays as $official) {
    $name=$official['name'];$english=$official['english'];$expected=$name.' ('.$english.')';
    foreach ([$english.' ('.$name.' ('.$english.'))', $english.' ('.$name.')', $name.' ('.$name.' ('.$english.'))', $expected] as $input) {
        $actual=block_myprogress_herbology_collapse_formula_displays($input,$displays);
        if ($actual!==$expected || block_myprogress_herbology_collapse_formula_displays($actual,$displays)!==$expected) {
            throw new RuntimeException('Formula display regression: '.$input);
        }
    }
}
$spanish='En la fórmula Evodia Decoction (Wu Zhu Yu Tang (Evodia Decoction)), ¿qué opción es correcta?';
if (block_myprogress_herbology_collapse_formula_displays($spanish,$displays)!=='En la fórmula Wu Zhu Yu Tang (Evodia Decoction), ¿qué opción es correcta?') {
    throw new RuntimeException('Spanish prose regression');
}
$unknown='Unknown Decoction (Unknown Tang (Unknown Decoction))';
if (block_myprogress_herbology_collapse_formula_displays($unknown,$displays)!==$unknown) {
    throw new RuntimeException('Unknown formula must remain subject to validation');
}
echo "OK: pruebas de nombres duplicados, inglés/español y fórmulas desconocidas.\n";
PHP
php "$TEMP_DIR/test.php" "$TEMP_DIR/overrides.php"
if cmp -s "$TEMP_DIR/overrides.php" "$TARGET"; then
    echo 'Sin cambios: la reparación ya está instalada.'
    exit 0
fi
BACKUP="${BACKUP_ROOT:-/var/backups/acupuncturetests}/nested-formula-names-$(date +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP"
cp -a "$TARGET" "$BACKUP/herbology-question-overrides.php"
OWNER_GROUP="$(stat -c '%u:%g' "$TARGET")"
MODE="$(stat -c '%a' "$TARGET")"
if ! install -o "${OWNER_GROUP%:*}" -g "${OWNER_GROUP#*:}" -m "$MODE" "$TEMP_DIR/overrides.php" "$TARGET" || ! php -l "$TARGET"; then
    cp -a "$BACKUP/herbology-question-overrides.php" "$TARGET"
    echo 'ERROR: se restauró el normalizador anterior.' >&2
    exit 1
fi
echo 'OK: nombres oficiales de fórmula primero, traducción inglesa una sola vez.'
echo 'OK: preguntas, opciones y explicaciones normalizadas al cargar, también en español.'
echo 'OK: no se modifican claves de respuesta, preguntas guardadas ni publicaciones.'
echo "RESPALDO: $BACKUP"
