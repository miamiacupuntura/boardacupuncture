#!/usr/bin/env bash

set -euo pipefail

trap 'status=$?; echo "ERROR: despliegue detenido en la línea $LINENO (código $status)." >&2; exit $status' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups}"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash deploy_bensky_formula_chapter_alignment.sh COMMIT_DE_40_CARACTERES"
    exit 1
fi
if [[ ! -f "$MOODLE_ROOT/config.php" ]]; then
    echo "ERROR: no se encontró Moodle en $MOODLE_ROOT"
    exit 1
fi

FILES=(
    "blocks/myprogress/study-guide.php"
    "blocks/myprogress/herb-formula-category-map.php"
    "blocks/myprogress/formulas-study-guide.php"
    "blocks/myprogress/formulas-study-guide-data.json"
    "blocks/myprogress/formula-board-roster-2026.json"
    "study-guide/guidelines/chinese-herbal-medicine.html"
    "study-guide/guidelines/chinese-herbal-medicine-es.html"
    "study-guide/guidelines/formulas-appendix-c-2026-preview.html"
)

TEMP_DIR="$(mktemp -d)"
BACKUP_DIR="$BACKUP_ROOT/boardacupuncture-bensky-formulas-$(date +%Y%m%d-%H%M%S)"
trap 'rm -rf "$TEMP_DIR"' EXIT

for relative in "${FILES[@]}"; do
    mkdir -p "$TEMP_DIR/$(dirname "$relative")"
    curl -fsSL "$REPOSITORY/$REVISION/$relative" -o "$TEMP_DIR/$relative"
done

php -l "$TEMP_DIR/blocks/myprogress/study-guide.php" >/dev/null
php -l "$TEMP_DIR/blocks/myprogress/herb-formula-category-map.php" >/dev/null
php -l "$TEMP_DIR/blocks/myprogress/formulas-study-guide.php" >/dev/null
php -r '
    $data = json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR);
    $roster = json_decode(file_get_contents($argv[2]), true, 512, JSON_THROW_ON_ERROR);
    $expectedCounts = [
        "1"=>21,"2"=>17,"3"=>12,"4"=>32,"5"=>7,"6"=>11,"7"=>6,
        "8"=>45,"9"=>17,"10"=>9,"11"=>7,"12"=>20,"13"=>23,"14"=>17,
        "15"=>8,"16"=>33,"17"=>17,"18"=>8,"19"=>3,"20"=>14,"21"=>11,
    ];
    if (($roster["total"] ?? null) !== 181 || count($roster["formulas"] ?? []) !== 181) {
        throw new RuntimeException("El roster oficial no contiene exactamente 181 fórmulas.");
    }
    if (($roster["categorySystem"]["principalFormulaCountsByChapter"] ?? null) !== $expectedCounts) {
        throw new RuntimeException("Los totales principales de los 21 capítulos no coinciden con Bensky.");
    }
    $byName = [];
    foreach ($roster["formulas"] as $formula) {
        $byName[$formula["name"]] = $formula;
        if (empty($formula["benskyCategories"])) {
            throw new RuntimeException("Falta ubicación Bensky para " . $formula["name"]);
        }
    }
    $expected = [
        "Bai Hu Tang"=>[4], "Da Cheng Qi Tang"=>[2], "Ban Xia Xie Xin Tang"=>[3],
        "Run Chang Wan"=>[2], "Ji Chuan Jian"=>[2], "Ma Xing Shi Gan Tang"=>[4],
        "Dang Gui Liu Huang Tang"=>[4], "Bai He Gu Jin Tang"=>[4,8],
    ];
    foreach ($expected as $name => $chapters) {
        $actual = array_column($byName[$name]["benskyCategories"] ?? [], "chapter");
        if ($actual !== $chapters) {
            throw new RuntimeException("Capítulo Bensky incorrecto para " . $name);
        }
    }
    $profiles = [];
    foreach ($data["supplementalFormulas"] ?? [] as $formula) {
        $profiles[$formula["name"]] = $formula;
    }
    if (($profiles["Run Chang Wan"]["number"] ?? null) !== "C02-01") {
        throw new RuntimeException("Run Chang Wan no conserva el identificador del capítulo 2.");
    }
    if (($profiles["Xiao Xian Xiong Tang"]["chapterRole"] ?? null) !== "associated"
            || ($profiles["Xiao Xian Xiong Tang"]["associatedWith"] ?? null) !== "Qing Qi Hua Tan Wan") {
        throw new RuntimeException("Xiao Xian Xiong Tang no está marcada como fórmula asociada.");
    }
' "$TEMP_DIR/blocks/myprogress/formulas-study-guide-data.json" \
  "$TEMP_DIR/blocks/myprogress/formula-board-roster-2026.json"

GUIDE="$TEMP_DIR/blocks/myprogress/formulas-study-guide.php"
grep -Fq "'clear-heat' => 4" "$GUIDE"
grep -Fq "'purge' => 2" "$GUIDE"
grep -Fq "'harmonize' => 3" "$GUIDE"
grep -Fq "Compare all 17 principal formulas in Bensky Chapter 17" "$GUIDE"
! sed -n '/\$transformphlegmchapterplan = \[/,/^\];/p' "$GUIDE" | grep -Fq "Xiao Xian Xiong Tang"
grep -Fq "['Bu Huan Jin Zheng Qi San', 2" "$GUIDE"
grep -Fq "['Huo Xiang Zheng Qi San', 3" "$GUIDE"
grep -Fq "\$categories['purge']['formulas'][] = \$runchangwanprofile;" "$GUIDE"
grep -Fq "'official-formulas-ch3' => 'purge'" "$GUIDE"
grep -Fq "\$runchangwanprofile['integratedOfficialProfile'] = true;" "$GUIDE"
grep -Fq "'formulaNames' => ['Run Chang Wan']" "$GUIDE"
! grep -Fq '$nextcategorycardnumber' "$GUIDE"
! grep -Fq 'foreach ($officialexpansionindexcategories as $category)' "$GUIDE"
grep -Fq "['number' => 20, 'key' => 'treat-abscesses-chapter-20', 'name' => 'Formulas That Treat Abscesses and Sores'" "$GUIDE"
grep -Fq "['number' => 21, 'key' => 'external-application-chapter-21', 'name' => 'Formulas for External Application'" "$GUIDE"
grep -Fq 'foreach ($benskychapters as $chapter)' "$GUIDE"
grep -Fq "'21 capítulos de Bensky'" "$GUIDE"
grep -Fq "'title' => 'Bensky Category Crosswalk'" "$TEMP_DIR/blocks/myprogress/study-guide.php"
grep -Fq "'path' => '/blocks/myprogress/herb-formula-category-map.php'" "$TEMP_DIR/blocks/myprogress/study-guide.php"
grep -Fq 'LEVEL 1 · HERBS' "$TEMP_DIR/blocks/myprogress/herb-formula-category-map.php"
grep -Fq 'LEVEL 2 · DUÌ YÀO' "$TEMP_DIR/blocks/myprogress/herb-formula-category-map.php"
grep -Fq 'LEVEL 3 · FORMULAS' "$TEMP_DIR/blocks/myprogress/herb-formula-category-map.php"
grep -Fq 'HOW THEY RELATE · CLINICAL EXPLANATION' "$TEMP_DIR/blocks/myprogress/herb-formula-category-map.php"
grep -Fq 'colspan="3"' "$TEMP_DIR/blocks/myprogress/herb-formula-category-map.php"
grep -Fq 'Examples inside formulas:' "$TEMP_DIR/blocks/myprogress/herb-formula-category-map.php"
grep -Fq 'Ma Huang Tang → Ma Huang + Gui Zhi' "$TEMP_DIR/blocks/myprogress/herb-formula-category-map.php"
grep -Fq 'Representative Duì Yào' "$TEMP_DIR/blocks/myprogress/herb-formula-category-map.php"
grep -Fq 'Official pair ' "$TEMP_DIR/blocks/myprogress/herb-formula-category-map.php"
grep -Fq 'Formula bridge:' "$TEMP_DIR/blocks/myprogress/herb-formula-category-map.php"
grep -Fq 'Pair category:' "$TEMP_DIR/blocks/myprogress/herb-formula-category-map.php"
grep -Fq 'Categoría de la pareja:' "$TEMP_DIR/blocks/myprogress/herb-formula-category-map.php"
grep -Fq "[3, 2, 'Herbs that Clear Heat and Drain Fire'" "$TEMP_DIR/blocks/myprogress/herb-formula-category-map.php"
grep -Fq "Chapters 4 and 8 · Formulas that Clear Heat / Formulas that Tonify" \
    "$TEMP_DIR/study-guide/guidelines/formulas-appendix-c-2026-preview.html"

mkdir -p "$BACKUP_DIR"
for relative in "${FILES[@]}"; do
    target="$MOODLE_ROOT/$relative"
    if [[ -f "$target" ]]; then
        mkdir -p "$BACKUP_DIR/$(dirname "$relative")"
        cp -a "$target" "$BACKUP_DIR/$relative"
    fi
done

for relative in "${FILES[@]}"; do
    target="$MOODLE_ROOT/$relative"
    source="$TEMP_DIR/$relative"
    mkdir -p "$(dirname "$target")"
    if [[ -f "$target" ]]; then
        owner="$(stat -c '%U' "$target")"
        group="$(stat -c '%G' "$target")"
    else
        owner="www-data"
        group="www-data"
    fi
    install -m 0644 -o "$owner" -g "$group" "$source" "$target"
done

if [[ "${SKIP_CACHE_PURGE:-0}" != "1" ]]; then
    if [[ "$(id -u)" -eq 0 ]] && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    else
        php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    fi
fi

echo "Alineación Bensky instalada desde $REVISION"
echo "Respaldo: $BACKUP_DIR"
echo "Verificado: 21 capítulos · 181 fórmulas oficiales · capítulo 17 con 17 fórmulas principales."
