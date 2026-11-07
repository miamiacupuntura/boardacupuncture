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
    formula-practice-data.json \
    formula-practice-translations-es.json; do
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

for required in \
    herb-comparison-bank.html \
    herb-comparison-bank-es.html \
    herb-comparison-bank.css \
    herb-comparison-bank.js \
    herb-comparison-bank-data.json \
    herb-comparison-bank-translations-es.json; do
    if [[ ! -f "${PRACTICE_DIR}/${required}" ]]; then
        echo "Herbal comparison bank artifact is missing: ${required}" >&2
        exit 1
    fi
done
node "${REPO_ROOT}/tools/validate_herb_comparison_bank.mjs" \
    "${PRACTICE_DIR}/herb-comparison-bank-data.json" \
    "${PRACTICE_DIR}/herb-comparison-bank-translations-es.json"
if ! grep -q 'herb-comparison-bank.html' "${PRACTICE_DIR}/index.html"; then
    echo "Single Herbs Comparisons does not link to the herbal comparison bank." >&2
    exit 1
fi

mkdir -p "${OUTPUT_DIR}"
ARTIFACT="${OUTPUT_DIR}/study-guide-${RELEASE_ID}.tar.gz"
STAGING_DIR="$(mktemp -d)"
trap 'rm -rf "${STAGING_DIR}"' EXIT

# Keep the repository readable while ensuring every published Study Note loads
# the shared Previous / Jump / Next navigation component.
cp -a "${SOURCE_DIR}/." "${STAGING_DIR}/"
node "${REPO_ROOT}/tools/build_study_note_navigation_manifest.mjs" \
    --root="${STAGING_DIR}/comparisons" \
    --output="${STAGING_DIR}/comparisons/study-note-navigation-manifest.js"
node "${REPO_ROOT}/tools/inject_study_note_navigation.mjs" \
    --write \
    --root="${STAGING_DIR}/comparisons"
node "${REPO_ROOT}/tools/inject_study_note_navigation.mjs" \
    --check \
    --root="${STAGING_DIR}/comparisons"
node "${REPO_ROOT}/tools/inject_library_navigation.mjs" \
    --write \
    --root="${STAGING_DIR}/comparisons"
node "${REPO_ROOT}/tools/inject_library_navigation.mjs" \
    --check \
    --root="${STAGING_DIR}/comparisons"
if [[ ! -f "${STAGING_DIR}/comparisons/study-note-navigation.js" ]]; then
    echo "Shared Study Note navigation asset is missing." >&2
    exit 1
fi
if [[ ! -f "${STAGING_DIR}/comparisons/library-navigation.js" ]]; then
    echo "Shared Library navigation asset is missing."
    exit 1
fi
if [[ ! -f "${STAGING_DIR}/comparisons/study-note-navigation-manifest.js" ]]; then
    echo "Shared Study Note navigation manifest is missing." >&2
    exit 1
fi

tar -czf "${ARTIFACT}" -C "${STAGING_DIR}" .
sha256sum "${ARTIFACT}" > "${ARTIFACT}.sha256"

echo "Built ${ARTIFACT}"
echo "Files: $(find "${SOURCE_DIR}" -type f | wc -l)"
echo "Size: $(du -h "${ARTIFACT}" | awk '{print $1}')"
