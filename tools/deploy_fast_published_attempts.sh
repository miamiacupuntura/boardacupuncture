#!/usr/bin/env bash
set -euo pipefail
payload_ref='7974d51d20f41f0cd0f1038439b5cec1e698bffb'
stage="$(mktemp -d)"
trap 'rm -rf "$stage"' EXIT
for asset in herbology-published-attempt-fast.php install_fast_published_attempts.py test_fast_published_attempts.php; do
    curl -fsSL "https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture/$payload_ref/tools/$asset" -o "$stage/$asset"
done
php -l "$stage/herbology-published-attempt-fast.php"
php -l "$stage/test_fast_published_attempts.php"
php "$stage/test_fast_published_attempts.php"
python3 "$stage/install_fast_published_attempts.py" "$@"
