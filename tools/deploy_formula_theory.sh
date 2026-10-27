#!/usr/bin/env bash
set -euo pipefail

SOURCE_DIR="${1:?Usage: deploy_formula_theory.sh SOURCE_DIR TARGET_DIR}"
TARGET_DIR="${2:?Usage: deploy_formula_theory.sh SOURCE_DIR TARGET_DIR}"

INDEXES=(
  index formulas pathologies patterns families dui-yao name-traps herb-families
  safety formula-name-traps misleading-formula-names advanced-name-traps
  supplemental-herb-families patterns-tips-maciocia most-important-formulas
)

EN_COUNT="$(grep -o 'data-type="formula-theory"' "$SOURCE_DIR/formula-theory.html" | wc -l | tr -d ' ')"
ES_COUNT="$(grep -o 'data-type="formula-theory"' "$SOURCE_DIR/formula-theory-es.html" | wc -l | tr -d ' ')"
DETAIL_COUNT="$(find "$SOURCE_DIR" -maxdepth 1 -type f -name 'formula-theory-*.html' ! -name 'formula-theory-es.html' | wc -l | tr -d ' ')"

test "$EN_COUNT" -eq 12
test "$ES_COUNT" -eq 12
test "$DETAIL_COUNT" -eq 24
test -d "$TARGET_DIR"

install -m 0644 "$SOURCE_DIR"/formula-theory*.html "$TARGET_DIR"/
install -m 0644 "$SOURCE_DIR/continuous-reader.html" "$SOURCE_DIR/continuous-reader.js" "$TARGET_DIR"/
for base in "${INDEXES[@]}"; do
  install -m 0644 "$SOURCE_DIR/${base}.html" "$SOURCE_DIR/${base}-es.html" "$TARGET_DIR"/
done

test "$(grep -o 'data-type="formula-theory"' "$TARGET_DIR/formula-theory.html" | wc -l | tr -d ' ')" -eq 12
test "$(grep -o 'data-type="formula-theory"' "$TARGET_DIR/formula-theory-es.html" | wc -l | tr -d ' ')" -eq 12
grep -q 'href="formula-theory.html"' "$TARGET_DIR/index.html"
grep -q 'href="formula-theory-es.html"' "$TARGET_DIR/index-es.html"

echo "FORMULA COMPOSITION GROUP DEPLOYED OK"
echo "English topics: 12"
echo "Spanish topics: 12"
