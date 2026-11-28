#!/usr/bin/env bash
set -euo pipefail
for tool in curl python3 php mktemp; do
    command -v "$tool" >/dev/null || { echo "ERROR: falta $tool" >&2; exit 1; }
done
WORK_DIR="$(mktemp -d)"
trap 'rm -rf -- "$WORK_DIR"' EXIT
BASE='https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture/24cef33f2343007ac6a9a53bc7b1ac9079ff2384/tools'
for file in exam-audit-classification.php exam-audit-student-cards.php herbology-fast-exam-chooser.js install_exam_chooser_snapshot_fix.py test_exam_snapshot_cards.php; do
    curl -fsSL "$BASE/$file" -o "$WORK_DIR/$file"
done
php -l "$WORK_DIR/exam-audit-classification.php"
php -l "$WORK_DIR/exam-audit-student-cards.php"
php "$WORK_DIR/test_exam_snapshot_cards.php" "$WORK_DIR/exam-audit-student-cards.php"
python3 -m py_compile "$WORK_DIR/install_exam_chooser_snapshot_fix.py"
python3 "$WORK_DIR/install_exam_chooser_snapshot_fix.py" "$@"
