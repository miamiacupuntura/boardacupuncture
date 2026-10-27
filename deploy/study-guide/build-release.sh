#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "${SCRIPT_DIR}/../.." && pwd)"
SOURCE_DIR="${REPO_ROOT}/study-guide"
OUTPUT_DIR="${1:?usage: build-release.sh OUTPUT_DIR RELEASE_ID}"
RELEASE_ID="${2:?usage: build-release.sh OUTPUT_DIR RELEASE_ID}"

if [[ ! "${RELEASE_ID}" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Release ID must be a 40-character Git commit SHA." >&2
    exit 1
fi

if [[ ! -f "${SOURCE_DIR}/index.html" ]]; then
    echo "study-guide/index.html is missing." >&2
    exit 1
fi

if find "${SOURCE_DIR}" -type l -print -quit | grep -q .; then
    echo "Symlinks are not allowed in the Study Guide artifact." >&2
    exit 1
fi

if find "${SOURCE_DIR}" -type f \( -iname '*.php' -o -iname '*.phtml' -o -iname '*.phar' \) -print -quit | grep -q .; then
    echo "Executable PHP content is not allowed in the static Study Guide artifact." >&2
    exit 1
fi

if ! grep -q 'Acupuncture &amp; Point Location' "${SOURCE_DIR}/index.html"; then
    echo "The expected Study Guide heading was not found." >&2
    exit 1
fi

PRACTICE_DIR="${SOURCE_DIR}/comparisons"
for required in \
    formula-practice.html \
    formula-practice-es.html \
    formula-practice.js \
    formula-practice-data.json; do
    if [[ ! -f "${PRACTICE_DIR}/${required}" ]]; then
        echo "Formula practice artifact is missing: ${required}" >&2
        exit 1
    fi
done
node "${REPO_ROOT}/tools/validate_formula_practice.mjs" \
    "${PRACTICE_DIR}/formula-practice-data.json"
if ! grep -q 'formula-practice.html' "${PRACTICE_DIR}/formula-navigator.html"; then
    echo "Formula Navigator does not link to Formula Practice." >&2
    exit 1
fi

mkdir -p "${OUTPUT_DIR}"
ARTIFACT="${OUTPUT_DIR}/study-guide-${RELEASE_ID}.tar.gz"

tar -czf "${ARTIFACT}" -C "${SOURCE_DIR}" .
sha256sum "${ARTIFACT}" > "${ARTIFACT}.sha256"

echo "Built ${ARTIFACT}"
echo "Files: $(find "${SOURCE_DIR}" -type f | wc -l)"
echo "Size: $(du -h "${ARTIFACT}" | awk '{print $1}')"
