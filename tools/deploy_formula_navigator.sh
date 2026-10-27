#!/usr/bin/env bash
set -euo pipefail

SOURCE_DIR="${1:?Usage: deploy_formula_navigator.sh SOURCE_DIR TARGET_DIR}"
TARGET_DIR="${2:?Usage: deploy_formula_navigator.sh SOURCE_DIR TARGET_DIR}"

INDEXES=(
  index formulas pathologies patterns families dui-yao name-traps herb-families
  safety formula-name-traps misleading-formula-names advanced-name-traps
  supplemental-herb-families patterns-tips-maciocia most-important-formulas
  formula-theory herbal-incompatibilities secondary-actions formula-navigator
)

test -d "$TARGET_DIR"
test "$(grep -o 'data-formula-number=' "$SOURCE_DIR/formula-navigator.html" | wc -l | tr -d ' ')" -eq 63
test "$(grep -o 'data-formula-number=' "$SOURCE_DIR/formula-navigator-es.html" | wc -l | tr -d ' ')" -eq 63

install -m 0644 "$SOURCE_DIR"/formula-navigator*.html "$TARGET_DIR"/
install -m 0644 "$SOURCE_DIR/formula-navigator.js" "$SOURCE_DIR/comparisons.js" "$SOURCE_DIR/comparisons.css" "$TARGET_DIR"/
install -m 0644 "$SOURCE_DIR/continuous-reader.html" "$SOURCE_DIR/continuous-reader.js" "$TARGET_DIR"/
for base in "${INDEXES[@]}"; do
  install -m 0644 "$SOURCE_DIR/${base}.html" "$SOURCE_DIR/${base}-es.html" "$TARGET_DIR"/
done

grep -q 'href="formula-navigator.html"' "$TARGET_DIR/index.html"
grep -q 'href="formula-navigator-es.html"' "$TARGET_DIR/index-es.html"

echo "63-FORMULA NAVIGATOR DEPLOYED OK"
