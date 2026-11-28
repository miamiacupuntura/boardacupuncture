#!/usr/bin/env bash
set -euo pipefail
for tool in curl python3 php mktemp; do
    command -v "$tool" >/dev/null || { echo "ERROR: falta $tool" >&2; exit 1; }
done
WORK_DIR="$(mktemp -d)"
trap 'rm -rf -- "$WORK_DIR"' EXIT
BASE='https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture/dbc45591f3dcfbd4fc5d2db397a57035f7d3354b/tools'
for file in balance_user_published_1_8.py audit_exam_diversity.py plan_exam_versions.py rebalance_published_exams.py herbology-exam-one-snapshot.php herbology-exam-source-note.php published-exam-admin-view.php published-exam-admin.php test_published_exam_counts.php; do
    curl -fsSL "$BASE/$file" -o "$WORK_DIR/$file"
done
# No generated-question package is downloaded or executed.
for file in published-exam-admin-view.php published-exam-admin.php herbology-exam-one-snapshot.php herbology-exam-source-note.php; do
    php -l "$WORK_DIR/$file"
done
php "$WORK_DIR/test_published_exam_counts.php" "$WORK_DIR/published-exam-admin-view.php"
python3 -m py_compile "$WORK_DIR/balance_user_published_1_8.py"
python3 "$WORK_DIR/balance_user_published_1_8.py" --apply-available "$@"
