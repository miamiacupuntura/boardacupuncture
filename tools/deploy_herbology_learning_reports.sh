#!/usr/bin/env bash
set -euo pipefail
for tool in curl python3 php mktemp; do
    command -v "$tool" >/dev/null || { echo "ERROR: falta $tool" >&2; exit 1; }
done
WORK_DIR="$(mktemp -d)"
trap 'rm -rf -- "$WORK_DIR"' EXIT
BASE='https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture/befef9f4b88dc7ac2876ebfd014e95a2d6049704/tools'
for file in herbology-learning-report-lib.php herbology-learning-report.php herbology-learning-report.js install_herbology_learning_reports.py test_herbology_learning_reports.php; do
    curl -fsSL "$BASE/$file" -o "$WORK_DIR/$file"
done
php -l "$WORK_DIR/herbology-learning-report-lib.php"
php -l "$WORK_DIR/herbology-learning-report.php"
php "$WORK_DIR/test_herbology_learning_reports.php" "$WORK_DIR/herbology-learning-report-lib.php"
python3 -m py_compile "$WORK_DIR/install_herbology_learning_reports.py"
python3 "$WORK_DIR/install_herbology_learning_reports.py" "$@"
