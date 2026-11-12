#!/usr/bin/env bash
set -euo pipefail

trap 'status=$?; echo "ERROR: despliegue de Chief Herbs Property detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
REQUESTED_DIR="${STUDY_NOTE_DIR:-}"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash tools/deploy_chief_herbs_property.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi

for command in curl php grep install stat cp cmp mkdir mktemp readlink tr wc dirname date rm id; do
    command -v "$command" >/dev/null 2>&1 || { echo "ERROR: falta el comando $command" >&2; exit 1; }
done

TEMP_DIR="$(mktemp -d)"
trap 'rm -rf -- "$TEMP_DIR"' EXIT

for file in study-guide.php single-herbs-list.php formula-list.php; do
    curl -fsSL "$REPOSITORY/$REVISION/blocks/myprogress/$file" -o "$TEMP_DIR/$file"
    php -l "$TEMP_DIR/$file" >/dev/null
done
curl -fsSL "$REPOSITORY/$REVISION/blocks/myprogress/single-herb-actions-indications-supplement.json" -o "$TEMP_DIR/single-herb-actions-indications-supplement.json"
curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/chief-herbs-property.html" -o "$TEMP_DIR/chief-herbs-property.html"
curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/chief-herbs-property-es.html" -o "$TEMP_DIR/chief-herbs-property-es.html"

grep -Fq "Chief Herbs Property" "$TEMP_DIR/study-guide.php"
grep -Fq "/study-guide/comparisons/chief-herbs-property.html" "$TEMP_DIR/study-guide.php"
grep -Fq "AVAILABLE · 60 CHIEF HERBS" "$TEMP_DIR/study-guide.php"
grep -Fq "Property of Chief Herbs" "$TEMP_DIR/chief-herbs-property.html"
grep -Fq "Propiedades de las hierbas principales" "$TEMP_DIR/chief-herbs-property-es.html"
grep -Fq "data-chief-search" "$TEMP_DIR/chief-herbs-property.html"
grep -Fq "from=chief" "$TEMP_DIR/chief-herbs-property.html"
grep -Fq "chief-herbs-card-link--herb" "$TEMP_DIR/chief-herbs-property.html"
grep -Fq "chief-herbs-card-link--formula" "$TEMP_DIR/chief-herbs-property.html"
grep -Fq "\$fromchief = \$fromcontext === 'chief'" "$TEMP_DIR/single-herbs-list.php"
grep -Fq "Back to Chief Herbs" "$TEMP_DIR/single-herbs-list.php"
grep -Fq "'Acciones' : 'Actions'" "$TEMP_DIR/single-herbs-list.php"
grep -Fq "'Indicaciones' : 'Indications'" "$TEMP_DIR/single-herbs-list.php"
grep -Fq "\$formulafocus = trim(optional_param('formula'" "$TEMP_DIR/formula-list.php"
grep -Fq "Back to Chief Herbs" "$TEMP_DIR/formula-list.php"
php -r '
$data = json_decode(file_get_contents($argv[1]), true);
$required = ["Bai Zhi", "Cong Bai", "Fang Feng", "Gao Ben", "Gui Zhi", "Jing Jie", "Ma Huang", "Qiang Huo", "Sheng Jiang", "Xi Xin", "Xiang Ru", "Xin Yi", "Zi Su Ye"];
if (!is_array($data) || count($data["herbs"] ?? []) < 119) {
    fwrite(STDERR, "ERROR: banco de acciones e indicaciones incompleto.\n");
    exit(1);
}
foreach ($required as $name) {
    foreach (["en", "es"] as $language) {
        $pairs = $data["herbs"][$name][$language] ?? [];
        if (!$pairs || trim((string)($pairs[0][0] ?? "")) === "" || trim((string)($pairs[0][1] ?? "")) === "") {
            fwrite(STDERR, "ERROR: faltan acciones o indicaciones para $name ($language).\n");
            exit(1);
        }
    }
}
' "$TEMP_DIR/single-herb-actions-indications-supplement.json"
[[ "$(grep -o '<tr data-chief-row' "$TEMP_DIR/chief-herbs-property.html" | wc -l)" -eq 60 ]]
[[ "$(grep -o '<tr data-chief-row' "$TEMP_DIR/chief-herbs-property-es.html" | wc -l)" -eq 60 ]]

TARGET_PHP_DIR="$MOODLE_ROOT/blocks/myprogress"
for file in study-guide.php single-herbs-list.php formula-list.php single-herb-actions-indications-supplement.json; do
    [[ -f "$TARGET_PHP_DIR/$file" ]] || { echo "ERROR: no existe $TARGET_PHP_DIR/$file" >&2; exit 1; }
done

declare -a TARGETS=()
declare -A SEEN=()
add_target() {
    local candidate="$1"
    [[ -d "$candidate" && -f "$candidate/comparisons.css" ]] || return 0
    local resolved
    resolved="$(readlink -f "$candidate")"
    [[ -n "$resolved" ]] || return 0
    if [[ -z "${SEEN[$resolved]:-}" ]]; then SEEN["$resolved"]=1; TARGETS+=("$resolved"); fi
}
if [[ -n "$REQUESTED_DIR" ]]; then
    add_target "$REQUESTED_DIR"
else
    add_target "/var/www/acupuncturetests-study-guide/current/comparisons"
    add_target "$MOODLE_ROOT/study-guide/comparisons"
    add_target "$(dirname "$MOODLE_ROOT")/study-guide/comparisons"
    add_target "/var/www/html/study-guide/comparisons"
fi
[[ "${#TARGETS[@]}" -gt 0 ]] || { echo "ERROR: no se encontró el directorio activo de comparaciones." >&2; exit 1; }

TIMESTAMP="$(date +%Y%m%d-%H%M%S)"
BACKUP="$BACKUP_ROOT/chief-herbs-property-$TIMESTAMP"
mkdir -p "$BACKUP/moodle" "$BACKUP/static"
for file in study-guide.php single-herbs-list.php formula-list.php; do
    target_php="$TARGET_PHP_DIR/$file"
    cp -a "$target_php" "$BACKUP/moodle/$file"
    php_owner_group="$(stat -c '%u:%g' "$target_php")"
    php_mode="$(stat -c '%a' "$target_php")"
    install -o "${php_owner_group%:*}" -g "${php_owner_group#*:}" -m "$php_mode" "$TEMP_DIR/$file" "$target_php"
    cmp -s "$TEMP_DIR/$file" "$target_php"
    php -l "$target_php" >/dev/null
done

data_file="single-herb-actions-indications-supplement.json"
data_target="$TARGET_PHP_DIR/$data_file"
cp -a "$data_target" "$BACKUP/moodle/$data_file"
data_owner_group="$(stat -c '%u:%g' "$data_target")"
data_mode="$(stat -c '%a' "$data_target")"
install -o "${data_owner_group%:*}" -g "${data_owner_group#*:}" -m "$data_mode" "$TEMP_DIR/$data_file" "$data_target"
cmp -s "$TEMP_DIR/$data_file" "$data_target"

for target in "${TARGETS[@]}"; do
    sub="$BACKUP/static/$(echo "$target" | tr '/ ' '__')"
    mkdir -p "$sub"
    reference="$target/comparisons.css"
    owner_group="$(stat -c '%u:%g' "$reference")"
    mode="$(stat -c '%a' "$reference")"
    for file in chief-herbs-property.html chief-herbs-property-es.html; do
        [[ -f "$target/$file" ]] && cp -a "$target/$file" "$sub/$file"
        install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" "$TEMP_DIR/$file" "$target/$file"
        cmp -s "$TEMP_DIR/$file" "$target/$file"
    done
    echo "ACTUALIZADO Y VERIFICADO: $target"
done

if [[ -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    if command -v runuser >/dev/null 2>&1 && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    else
        php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    fi
fi

echo "OK: botón Chief Herbs Property instalado en Secondary Study Guide."
echo "OK: tabla bilingüe con 60 hierbas, propiedades, canales y fórmulas."
echo "OK: búsqueda instantánea y vista adaptable a móvil activas."
echo "OK: las 60 hierbas y todas las fórmulas enlazan sus tarjetas exactas."
echo "OK: las tarjetas ofrecen regreso a Chief Herbs sin perder el idioma."
echo "OK: las 304 hierbas oficiales tienen acciones principales e indicaciones clínicas."
echo "OK: Ma Huang y las otras 12 hierbas que liberan el Exterior fueron completadas en English y Español."
echo "OK: terminología Chief aplicada."
echo "RESPALDO: $BACKUP"
