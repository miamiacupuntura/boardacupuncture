#!/usr/bin/env bash
set -euo pipefail
for tool in curl python3 php mktemp; do
    command -v "$tool" >/dev/null || { echo "ERROR: falta $tool" >&2; exit 1; }
done
WORK_DIR="$(mktemp -d)"
trap 'rm -rf -- "$WORK_DIR"' EXIT
BASE='https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture/bc24ef1beb6a50446d7492a399a4060a8ab118a4/tools'
for file in audit_exam_diversity.py plan_exam_versions.py rebalance_published_exams.py; do
    curl -fsSL "$BASE/$file" -o "$WORK_DIR/$file"
done
python3 -m py_compile "$WORK_DIR/audit_exam_diversity.py" "$WORK_DIR/plan_exam_versions.py" "$WORK_DIR/rebalance_published_exams.py"
python3 "$WORK_DIR/rebalance_published_exams.py" "$@"
