#!/usr/bin/env bash
set -euo pipefail
stage="$(mktemp -d)"
trap 'rm -rf "$stage"' EXIT
base_ref='7974d51d20f41f0cd0f1038439b5cec1e698bffb'
overview_ref='c631f6ccff6af09a30a4a4b4e4d3990d49c29e42'
for asset in install_fast_published_attempts.py herbology-published-attempt-fast.php test_fast_published_attempts.php; do
 curl -fsSL "https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture/$base_ref/tools/$asset" -o "$stage/$asset"
done
curl -fsSL "https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture/$overview_ref/tools/install_fast_simulation_overview.py" -o "$stage/install_fast_simulation_overview.py"
php -l "$stage/herbology-published-attempt-fast.php"
php "$stage/test_fast_published_attempts.php"
python3 "$stage/install_fast_simulation_overview.py" "$@"
echo 'OK: Open all simulations usa las versiones publicadas y conserva las tarjetas de auditoría; omite la preparación del banco completo y de Mock.'
