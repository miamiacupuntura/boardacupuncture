#!/usr/bin/env bash

set -euo pipefail

trap 'status=$?; echo "ERROR: despliegue detenido en la línea $LINENO (código $status)." >&2; exit $status' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
TARGET_DIR="$MOODLE_ROOT/blocks/myprogress"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups}"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash deploy_formula_study_guide.sh COMMIT_DE_40_CARACTERES"
    exit 1
fi

if [[ ! -f "$MOODLE_ROOT/config.php" || ! -d "$TARGET_DIR" ]]; then
    echo "ERROR: no se encontró la instalación activa de Moodle en $MOODLE_ROOT"
    exit 1
fi

TEMP_DIR="$(mktemp -d)"
BACKUP_DIR="$BACKUP_ROOT/boardacupuncture-formulas-$(date +%Y%m%d-%H%M%S)"
trap 'rm -rf "$TEMP_DIR"' EXIT

curl -fsSL \
    "$REPOSITORY/$REVISION/blocks/myprogress/formulas-study-guide.php" \
    -o "$TEMP_DIR/formulas-study-guide.php"
curl -fsSL \
    "$REPOSITORY/$REVISION/blocks/myprogress/formulas-study-guide-data.json" \
    -o "$TEMP_DIR/formulas-study-guide-data.json"
curl -fsSL \
    "$REPOSITORY/$REVISION/blocks/myprogress/study-guide.php" \
    -o "$TEMP_DIR/study-guide.php"

php -l "$TEMP_DIR/formulas-study-guide.php" >/dev/null
php -l "$TEMP_DIR/study-guide.php" >/dev/null
grep -Fq '$renderchapternavigation = static function' "$TEMP_DIR/formulas-study-guide.php"
grep -Fq 'data-formula-chapter-jump' "$TEMP_DIR/formulas-study-guide.php"
grep -Fq 'formula-study-guide__chapter-path' "$TEMP_DIR/formulas-study-guide.php"
php -r '
    $data = json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR);
    $chapter8 = array_filter(
        $data["supplementalFormulas"] ?? [],
        static fn(array $formula): bool => str_ends_with($formula["categoryKey"] ?? "", "-ch8")
    );
    $chapter9 = array_filter(
        $data["supplementalFormulas"] ?? [],
        static fn(array $formula): bool => ($formula["categoryKey"] ?? "") === "stabilize-bind-ch9"
    );
    $chapter10 = array_filter(
        $data["supplementalFormulas"] ?? [],
        static fn(array $formula): bool => ($formula["categoryKey"] ?? "") === "calm-shen-ch10"
    );
    $chapter11 = array_filter(
        $data["supplementalFormulas"] ?? [],
        static fn(array $formula): bool => ($formula["categoryKey"] ?? "") === "open-orifices-ch11"
    );
    $chapter12 = array_filter(
        $data["supplementalFormulas"] ?? [],
        static fn(array $formula): bool => ($formula["categoryKey"] ?? "") === "regulate-qi-ch12"
    );
    $chapter13 = array_filter(
        $data["supplementalFormulas"] ?? [],
        static fn(array $formula): bool => ($formula["categoryKey"] ?? "") === "regulate-blood-ch13"
    );
    $chapter14 = array_filter(
        $data["supplementalFormulas"] ?? [],
        static fn(array $formula): bool => ($formula["categoryKey"] ?? "") === "expel-wind-ch14"
    );
    $chapter15 = array_filter(
        $data["supplementalFormulas"] ?? [],
        static fn(array $formula): bool => ($formula["categoryKey"] ?? "") === "treat-dryness-ch15"
    );
    $chapter16 = array_filter(
        $data["supplementalFormulas"] ?? [],
        static fn(array $formula): bool => ($formula["categoryKey"] ?? "") === "expel-damp-ch16"
    );
    $chapter17 = array_filter(
        $data["supplementalFormulas"] ?? [],
        static fn(array $formula): bool => ($formula["categoryKey"] ?? "") === "transform-phlegm-ch17"
    );
    $chapter18 = array_filter(
        $data["supplementalFormulas"] ?? [],
        static fn(array $formula): bool => ($formula["categoryKey"] ?? "") === "digestive-ch18"
    );
    $chapter19 = array_filter(
        $data["supplementalFormulas"] ?? [],
        static fn(array $formula): bool => ($formula["categoryKey"] ?? "") === "expel-parasites-ch19"
    );
    $chapter20 = array_filter(
        $data["supplementalFormulas"] ?? [],
        static fn(array $formula): bool => ($formula["categoryKey"] ?? "") === "treat-abscesses-ch20"
    );
    $chapter21 = array_filter(
        $data["supplementalFormulas"] ?? [],
        static fn(array $formula): bool => ($formula["categoryKey"] ?? "") === "external-application-ch21"
    );
    $officialExpansion = array_filter(
        $data["supplementalFormulas"] ?? [],
        static fn(array $formula): bool => ($formula["categoryKey"] ?? "") === "official-formulas-ch3"
    );
    $dampnessSections = $data["categoryGuides"]["expel-damp-chapter-16"]["formulaSections"] ?? [];
    $dampnessSectionNames = [];
    foreach ($dampnessSections as $section) {
        foreach ($section["formulaNames"] ?? [] as $formulaName) {
            $dampnessSectionNames[] = $formulaName;
        }
    }
    $phlegmSections = $data["categoryGuides"]["transform-phlegm-chapter-17"]["formulaSections"] ?? [];
    $dampnessDuiYao = $data["categoryGuides"]["expel-damp-chapter-16"]["relatedDuiYao"] ?? [];
    $phlegmDuiYao = $data["categoryGuides"]["transform-phlegm-chapter-17"]["relatedDuiYao"] ?? [];
    $phlegmSectionNames = [];
    foreach ($phlegmSections as $section) {
        foreach ($section["formulaNames"] ?? [] as $formulaName) {
            $phlegmSectionNames[] = $formulaName;
        }
    }
    $digestiveSections = $data["categoryGuides"]["digestive-chapter-18"]["formulaSections"] ?? [];
    $digestiveDuiYao = $data["categoryGuides"]["digestive-chapter-18"]["relatedDuiYao"] ?? [];
    $digestiveSectionNames = [];
    foreach ($digestiveSections as $section) {
        foreach ($section["formulaNames"] ?? [] as $formulaName) {
            $digestiveSectionNames[] = $formulaName;
        }
    }
    $parasiteSections = $data["categoryGuides"]["expel-parasites-chapter-19"]["formulaSections"] ?? [];
    $parasiteSectionNames = [];
    foreach ($parasiteSections as $section) {
        foreach ($section["formulaNames"] ?? [] as $formulaName) {
            $parasiteSectionNames[] = $formulaName;
        }
    }
    $abscessSections = $data["categoryGuides"]["treat-abscesses-chapter-20"]["formulaSections"] ?? [];
    $abscessSectionNames = [];
    foreach ($abscessSections as $section) {
        foreach ($section["formulaNames"] ?? [] as $formulaName) {
            $abscessSectionNames[] = $formulaName;
        }
    }
    $externalApplicationSections = $data["categoryGuides"]["external-application-chapter-21"]["formulaSections"] ?? [];
    $externalApplicationSectionNames = [];
    foreach ($externalApplicationSections as $section) {
        foreach ($section["formulaNames"] ?? [] as $formulaName) {
            $externalApplicationSectionNames[] = $formulaName;
        }
    }
    $drynessSections = $data["categoryGuides"]["treat-dryness-ch15"]["formulaSections"] ?? [];
    $drynessSectionNames = [];
    foreach ($drynessSections as $section) {
        foreach ($section["formulaNames"] ?? [] as $formulaName) {
            $drynessSectionNames[] = $formulaName;
        }
    }
    $releaseSections = $data["categoryGuides"]["release-exterior"]["formulaSections"] ?? [];
    $releaseDuiYao = $data["categoryGuides"]["release-exterior"]["relatedDuiYao"] ?? [];
    $releaseSectionNames = [];
    foreach ($releaseSections as $section) {
        foreach ($section["formulaNames"] ?? [] as $formulaName) {
            $releaseSectionNames[] = $formulaName;
        }
    }
    $clearHeatSections = $data["categoryGuides"]["clear-heat"]["formulaSections"] ?? [];
    $clearHeatDuiYao = $data["categoryGuides"]["clear-heat"]["relatedDuiYao"] ?? [];
    $clearHeatSectionNames = [];
    foreach ($clearHeatSections as $section) {
        foreach ($section["formulaNames"] ?? [] as $formulaName) {
            $clearHeatSectionNames[] = $formulaName;
        }
    }
    $harmonizeDuiYao = $data["categoryGuides"]["harmonize"]["relatedDuiYao"] ?? [];
    $warmInteriorDuiYao = $data["categoryGuides"]["warm-interior"]["relatedDuiYao"] ?? [];
    $tonifyDuiYao = $data["categoryGuides"]["tonify"]["relatedDuiYao"] ?? [];
    $stabilizeBindDuiYao = $data["categoryGuides"]["stabilize-bind"]["relatedDuiYao"] ?? [];
    $calmShenDuiYao = $data["categoryGuides"]["calm-shen-chapter-10"]["relatedDuiYao"] ?? [];
    $regulateQiDuiYao = $data["categoryGuides"]["regulate-qi-chapter-12"]["relatedDuiYao"] ?? [];
    $invigorateBloodDuiYao = $data["categoryGuides"]["regulate-blood-chapter-13"]["relatedDuiYao"] ?? [];
    $expelWindDuiYao = $data["categoryGuides"]["expel-wind-chapter-14"]["relatedDuiYao"] ?? [];
    $purgeSections = $data["categoryGuides"]["purge"]["formulaSections"] ?? [];
    $purgeDuiYao = $data["categoryGuides"]["purge"]["relatedDuiYao"] ?? [];
    $purgeSectionNames = [];
    foreach ($purgeSections as $section) {
        foreach ($section["formulaNames"] ?? [] as $formulaName) {
            $purgeSectionNames[] = $formulaName;
        }
    }
    $purgeTherapeuticClasses = $data["categoryGuides"]["purge"]["therapeuticClasses"] ?? [];
    $purgeReferenceGroups = $data["categoryGuides"]["purge"]["referenceGroups"] ?? [];
    $purgeReferenceNames = [];
    foreach ($purgeReferenceGroups as $group) {
        foreach ($group["items"] ?? [] as $item) {
            $purgeReferenceNames[] = $item[0] ?? "";
        }
    }
    $expectedDuiYaoAnchors = [
        "release-exterior" => "release-exterior",
        "clear-heat" => "clear-heat",
        "purge" => "purgatives-laxatives",
        "harmonize" => "harmonizing",
        "warm-interior" => "tonics",
        "tonify" => "tonics",
        "stabilize-bind" => "harmonizing",
        "calm-shen-chapter-10" => "regulate-blood",
        "regulate-qi-chapter-12" => "descend-stomach-qi",
        "regulate-blood-chapter-13" => "regulate-blood",
        "expel-wind-chapter-14" => "release-exterior",
        "expel-damp-chapter-16" => "drain-damp",
        "transform-phlegm-chapter-17" => "cough-phlegm",
        "digestive-chapter-18" => "cough-phlegm",
    ];
    foreach ($expectedDuiYaoAnchors as $guideKey => $anchor) {
        if (($data["categoryGuides"][$guideKey]["relatedDuiYaoAnchor"] ?? "") !== $anchor) {
            fwrite(STDERR, "ERROR: ancla Duì Yào incorrecta para {$guideKey}.\n");
            exit(1);
        }
    }
    if (($data["version"] ?? null) !== 7
            || count($data["categories"] ?? []) !== 14
            || count($data["formulas"] ?? []) !== 63
            || count($data["supplementalCategories"] ?? []) !== 23
            || count($data["supplementalFormulas"] ?? []) !== 227
            || count($chapter8) !== 45
            || count($chapter9) !== 15
            || count($chapter10) !== 6
            || count($chapter11) !== 7
            || count($chapter12) !== 15
            || count($chapter13) !== 19
            || count($chapter14) !== 14
            || count($chapter15) !== 8
            || count($chapter16) !== 25
            || count($chapter17) !== 13
            || count($chapter18) !== 7
            || count($chapter19) !== 3
            || count($chapter20) !== 14
            || count($chapter21) !== 11
            || count($officialExpansion) !== 1
            || array_column($officialExpansion, "name") !== ["Run Chang Wan"]
            || array_column($dampnessSections, "key") !== [
                "transform-harmonize-middle", "clear-damp-heat", "promote-urination",
                "warm-transform-water", "dispel-wind-damp"
            ]
            || array_map(static fn(array $section): int => count($section["formulaNames"] ?? []), $dampnessSections)
                !== [5, 10, 5, 7, 6]
            || count($dampnessSectionNames) !== 33
            || count(array_unique($dampnessSectionNames)) !== 33
            || array_column($dampnessDuiYao, "number") !== [19, 31, 47]
            || array_column($dampnessDuiYao, "formulas") !== [
                "Wǔ Líng Sǎn.", "Dú Huó Jì Shēng Tāng.",
                "Huò Xiāng Zhèng Qì Sǎn — modification with Pèi Lán."
            ]
            || array_column($phlegmSections, "key") !== [
                "dry-damp-transform", "clear-heat-transform", "disperse-clumping",
                "moisten-dryness", "warm-phlegm-cold", "transform-extinguish-wind", "induce-vomiting"
            ]
            || array_map(static fn(array $section): int => count($section["formulaNames"] ?? []), $phlegmSections)
                !== [6, 3, 2, 1, 2, 3, 1]
            || count($phlegmSectionNames) !== 18
            || count(array_unique($phlegmSectionNames)) !== 18
            || array_column($phlegmDuiYao, "number") !== [37]
            || array_column($phlegmDuiYao, "formulas") !== ["Èr Chén Tāng."]
            || array_column($digestiveSections, "key") !== [
                "reduce-transform-food", "reduce-tonify-food"
            ]
            || array_map(static fn(array $section): int => count($section["formulaNames"] ?? []), $digestiveSections)
                !== [4, 4]
            || $digestiveSectionNames !== [
                "Bao He Wan", "Zhi Shi Dao Zhi Wan", "Mu Xiang Bing Lang Wan", "Fei Er Wan",
                "Jian Pi Wan", "Zhi Zhu Wan", "Zhi Shi Xiao Pi Wan", "Ge Hua Jie Cheng San"
            ]
            || array_column($digestiveDuiYao, "number") !== [37]
            || array_column($digestiveDuiYao, "formulas") !== ["Bǎo Hé Wán."]
            || array_column($parasiteSections, "key") !== ["expel-intestinal-parasites"]
            || array_map(static fn(array $section): int => count($section["formulaNames"] ?? []), $parasiteSections)
                !== [3]
            || $parasiteSectionNames !== ["Wu Mei Wan", "Hua Chong Wan", "Bu Dai Wan"]
            || array_column($abscessSections, "key") !== [
                "external-abscesses-sores", "internal-abscesses"
            ]
            || array_map(static fn(array $section): int => count($section["formulaNames"] ?? []), $abscessSections)
                !== [8, 6]
            || $abscessSectionNames !== [
                "Xian Fang Huo Ming Yin", "Wu Wei Xiao Du Yin", "Si Miao Yong An Tang",
                "Tuo Li Xiao Du Yin", "Yang He Tang", "Shi Wei Bai Du San",
                "Qian Jin Nei Tuo San", "San Zhong Kui Jian Tang", "Wei Jing Tang",
                "Da Huang Mu Dan Tang", "Qing Chang Yin", "Yi Yi Fu Zi Bai Jiang San",
                "Qing Xin Li Ge Tang", "Shi Liu Wei Liu Qi Yin"
            ]
            || array_column($externalApplicationSections, "key") !== [
                "external-formulas-external-disorders", "external-formulas-internal-disorders"
            ]
            || array_map(static fn(array $section): int => count($section["formulaNames"] ?? []), $externalApplicationSections)
                !== [7, 4]
            || $externalApplicationSectionNames !== [
                "Bing Peng San", "Jin Huang San / Jin Huang Gao", "Zi Dang Gao", "Er Qing Gao",
                "Qing Dai San", "Ku Shen Tang", "Hai Tong Pi Tang", "Wu Zhu Yu Gao",
                "Bi Ba Bing", "San Leng Xi Ru Fang", "An Tai Zhu Gao"
            ]
            || array_column($drynessSections, "key") !== [
                "gently-disperse-external-dryness", "enrich-yin-internal-dryness"
            ]
            || $drynessSectionNames !== [
                "Xing Su San", "Sang Xing Tang", "Qing Zao Jiu Fei Tang", "Sha Shen Mai Men Dong Tang",
                "Mai Men Dong Tang", "Yang Yin Qing Fei Tang", "Yu Ye Tang", "Zeng Ye Tang"
            ]
            || array_column($releaseSections, "key") !== ["wind-cold", "wind-heat", "cold-transforming-heat"]
            || $releaseSectionNames !== [
                "Ma Huang Tang", "Gui Zhi Tang", "Ge Gen Tang", "Ren Shen Bai Du San",
                "Sang Ju Yin", "Yin Qiao San", "Chai Ge Jie Ji Tang"
            ]
            || array_column($releaseDuiYao, "number") !== [1, 2, 4, 7, 15, 16, 22]
            || array_column($releaseDuiYao, "formulas") !== [
                "Má Huáng Tāng.", "Má Huáng Tāng.", "Guì Zhī Tāng.", "Sāng Jú Yǐn.",
                "Yín Qiáo Sǎn.", "Yín Qiáo Sǎn.", "Guì Zhī Tāng."
            ]
            || array_column($clearHeatSections, "key") !== [
                "qi-level-heat", "fire-toxin", "organ-heat", "hidden-deficiency-heat"
            ]
            || $clearHeatSectionNames !== [
                "Bai Hu Tang", "Huang Lian Jie Du Tang", "Ma Xing Shi Gan Tang",
                "Long Dan Xie Gan Tang", "Qing Hao Bie Jia Tang"
            ]
            || array_column($clearHeatDuiYao, "number") !== [3, 11, 17]
            || array_column($clearHeatDuiYao, "formulas") !== [
                "Má Xìng Shí Gān Tāng.", "Bái Hǔ Tāng.", "Qīng Hāo Biē Jiǎ Tāng."
            ]
            || array_column($harmonizeDuiYao, "number") !== [38, 45, 46]
            || array_column($harmonizeDuiYao, "formulas") !== [
                "Xiǎo Chái Hú Tāng.", "Xiǎo Chái Hú Tāng.", "Sì Nì Sǎn; Xiāo Yáo Sǎn."
            ]
            || array_column($warmInteriorDuiYao, "number") !== [42, 44]
            || array_column($warmInteriorDuiYao, "formulas") !== ["Sì Nì Tāng.", "Lǐ Zhōng Wán."]
            || array_column($tonifyDuiYao, "number") !== [9, 18, 19, 24]
            || array_column($tonifyDuiYao, "formulas") !== [
                "Bǔ Zhōng Yì Qì Tāng.", "Bǔ Zhōng Yì Qì Tāng; Guī Pí Tāng.",
                "Sì Jūn Zǐ Tāng.", "Sì Wù Tāng."
            ]
            || array_column($stabilizeBindDuiYao, "number") !== [22]
            || array_column($stabilizeBindDuiYao, "formulas") !== [
                "Sì Shén Wán — traditional preparation."
            ]
            || array_column($calmShenDuiYao, "number") !== [24]
            || array_column($calmShenDuiYao, "formulas") !== [
                "Tiān Wáng Bǔ Xīn Dān — Dāng Guī; Suān Zǎo Rén Tāng — Chuān Xiōng."
            ]
            || array_column($regulateQiDuiYao, "number") !== [2, 22, 37, 38]
            || array_column($regulateQiDuiYao, "formulas") !== [
                "Dìng Chuǎn Tāng.", "Jú Pí Zhú Rú Tāng.",
                "Sū Zǐ Jiàng Qì Tāng.", "Bàn Xià Hòu Pò Tāng."
            ]
            || array_column($invigorateBloodDuiYao, "number") !== [24, 34, 49]
            || array_column($invigorateBloodDuiYao, "formulas") !== [
                "Wēn Jīng Tāng.", "Xuè Fǔ Zhú Yū Tāng.", "Táo Hé Chéng Qì Tāng."
            ]
            || array_column($expelWindDuiYao, "number") !== [5, 35, 51]
            || array_column($expelWindDuiYao, "formulas") !== [
                "Xiāo Fēng Sǎn.", "Xiǎo Huó Luò Dān.", "Tiān Má Gōu Téng Yǐn."
            ]
            || array_column($purgeSections, "key") !== [
                "severe-complete-clumping", "moderate-clumping", "dryness-predominant"
            ]
            || $purgeSectionNames !== [
                "Da Cheng Qi Tang", "Xiao Cheng Qi Tang", "Tiao Wei Cheng Qi Tang"
            ]
            || array_column($purgeDuiYao, "number") !== [49]
            || array_column($purgeDuiYao, "slug") !== ["da-huang-mang-xiao-dui-yao"]
            || array_column($purgeTherapeuticClasses, "key") !== [
                "cold-purgatives", "warm-purgatives", "moistening-laxatives",
                "attack-and-tonify", "harsh-water-expellants"
            ]
            || ($purgeTherapeuticClasses[0]["boardNames"] ?? []) !== [
                "Da Cheng Qi Tang", "Xiao Cheng Qi Tang", "Tiao Wei Cheng Qi Tang"
            ]
            || ($purgeTherapeuticClasses[2]["otherNames"] ?? []) !== [
                "Ma Zi Ren Wan", "Run Chang Wan", "Ji Chuan Jian"
            ]
            || array_column($purgeReferenceGroups, "title") !== [
                "Cold purgatives", "Warm purgatives", "Moistening laxatives",
                "Purge while supporting deficiency", "Harsh water-expellants — high risk"
            ]
            || $purgeReferenceNames !== [
                "Da Xian Xiong Tang", "Da Huang Fu Zi Tang", "Wen Pi Tang",
                "Ma Zi Ren Wan", "Run Chang Wan", "Ji Chuan Jian", "Huang Long Tang",
                "Zeng Ye Cheng Qi Tang", "Shi Zao Tang", "Zhou Che Wan",
                "Yu Gong San", "Ji Jiao Li Huang Wan", "Shu Zao Yin Zi"
            ]) {
        fwrite(STDERR, "ERROR: los datos de fórmulas no pasaron la validación.\n");
        exit(1);
    }
' "$TEMP_DIR/formulas-study-guide-data.json"

validate_release_files() {
    local formula_file="$1"
    local data_file="$2"
    local study_file="$3"

    grep -Fq '$renderchapternavigation = static function' "$formula_file"
    grep -Fq 'data-formula-chapter-jump' "$formula_file"
    grep -Fq 'formula-study-guide__chapter-path' "$formula_file"
    grep -Fq '$dashboardurl = new moodle_url' "$formula_file"
    grep -Fq '← Back to Dashboard' "$formula_file"
    ! grep -Fq '<div class="formula-study-guide__module-switcher"' "$formula_file"
    ! grep -Fq '← Board Guideline · Chinese Herbal Medicine' "$formula_file"
    ! grep -Fq '← Library' "$formula_file"
    grep -Fq "'external-application-chapter-21'" "$formula_file"
    grep -Fq "'Run Chang Wan'" "$formula_file"
    grep -Fq 'relatedDuiYao' "$formula_file"
    grep -Fq '"sourceType": "official-expansion"' "$data_file"
    grep -Fq '"slug": "da-huang-mang-xiao-dui-yao"' "$data_file"
    grep -Fq '← Board Guideline · Chinese Herbal Medicine' "$study_file"
}

validate_release_files \
    "$TEMP_DIR/formulas-study-guide.php" \
    "$TEMP_DIR/formulas-study-guide-data.json" \
    "$TEMP_DIR/study-guide.php"

mkdir -p "$BACKUP_DIR"
cp -a "$TARGET_DIR/formulas-study-guide.php" "$BACKUP_DIR/"
cp -a "$TARGET_DIR/formulas-study-guide-data.json" "$BACKUP_DIR/"
cp -a "$TARGET_DIR/study-guide.php" "$BACKUP_DIR/"

PHP_OWNER="$(stat -c '%U' "$TARGET_DIR/formulas-study-guide.php")"
PHP_GROUP="$(stat -c '%G' "$TARGET_DIR/formulas-study-guide.php")"
JSON_OWNER="$(stat -c '%U' "$TARGET_DIR/formulas-study-guide-data.json")"
JSON_GROUP="$(stat -c '%G' "$TARGET_DIR/formulas-study-guide-data.json")"
STUDY_OWNER="$(stat -c '%U' "$TARGET_DIR/study-guide.php")"
STUDY_GROUP="$(stat -c '%G' "$TARGET_DIR/study-guide.php")"

install -m 0644 -o "$PHP_OWNER" -g "$PHP_GROUP" \
    "$TEMP_DIR/formulas-study-guide.php" "$TARGET_DIR/formulas-study-guide.php"
install -m 0644 -o "$JSON_OWNER" -g "$JSON_GROUP" \
    "$TEMP_DIR/formulas-study-guide-data.json" "$TARGET_DIR/formulas-study-guide-data.json"
install -m 0644 -o "$STUDY_OWNER" -g "$STUDY_GROUP" \
    "$TEMP_DIR/study-guide.php" "$TARGET_DIR/study-guide.php"

if [[ "${SKIP_CACHE_PURGE:-0}" != "1" ]]; then
    if [[ "$(id -u)" -eq 0 ]] && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    else
        php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    fi
fi

cmp -s "$TEMP_DIR/formulas-study-guide.php" "$TARGET_DIR/formulas-study-guide.php"
cmp -s "$TEMP_DIR/formulas-study-guide-data.json" "$TARGET_DIR/formulas-study-guide-data.json"
cmp -s "$TEMP_DIR/study-guide.php" "$TARGET_DIR/study-guide.php"
validate_release_files \
    "$TARGET_DIR/formulas-study-guide.php" \
    "$TARGET_DIR/formulas-study-guide-data.json" \
    "$TARGET_DIR/study-guide.php"

echo "CAPÍTULOS 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18 Y 19 INSTALADOS EN MOODLE"
echo "CATEGORÍA 1 REORGANIZADA: VIENTO-FRÍO, VIENTO-CALOR Y TRANSFORMACIÓN A CALOR"
echo "CATEGORÍA 2 REORGANIZADA: NIVEL QI, FUEGO-TOXINA, CALOR DE ÓRGANOS Y CALOR OCULTO"
echo "CATEGORÍA 3 REORGANIZADA: AGLOMERACIÓN CHENG QI GRAVE, MODERADA Y SECA"
echo "CLASIFICACIÓN PURGE INSTALADA: PURGANTES FRÍOS/CALIENTES, LAXANTES, ATAQUE-TONIFICACIÓN Y EXPELENTES"
echo "RUN CHANG WAN INSTALADA: FÓRMULA OFICIAL DEL APPENDIX C 2026"
echo "45 fórmulas en 6 secciones: Qi, Sangre, Qi-Sangre, Yin, Yang y Yin-Yang"
echo "CAPÍTULO 9 INSTALADO: 17 fórmulas en 5 secciones (15 perfiles nuevos + 2 Board)"
echo "REFERENCIA CRUZADA INSTALADA: BOARD STABILIZE AND BIND (3) VS. BENSKY CAPÍTULO 9 (17; COINCIDEN 2)"
echo "TARJETA UNIFICADA INSTALADA: STABILIZE AND BIND APARECE UNA SOLA VEZ EN EL ÍNDICE"
echo "CAPÍTULO 10 INSTALADO: 9 fórmulas en 2 secciones (6 perfiles nuevos + 3 Board)"
echo "REFERENCIA TERMINOLÓGICA INSTALADA: CALM SHEN = CALM THE SPIRIT; BOARD (3) VS. BENSKY CAPÍTULO 10 (9)"
echo "TARJETA UNIFICADA INSTALADA: CALM SHEN / CALM THE SPIRIT APARECE UNA SOLA VEZ EN EL ÍNDICE"
echo "CAPÍTULO 11 INSTALADO: 7 fórmulas en 2 secciones (7 perfiles Bensky, 0 Board)"
echo "ANÁLISIS DE ALCANCE INSTALADO: METALES, ESPECIES PROTEGIDAS, INGREDIENTES REGULADOS, VÍA NASAL Y URGENCIAS"
echo "CAPÍTULO 12 INSTALADO: 20 fórmulas en 2 secciones (15 perfiles nuevos + 5 Board)"
echo "VISTA ÚNICA INSTALADA: REGULATE QI ABRE DIRECTAMENTE EL CAPÍTULO 12 COMPLETO"
echo "CAPÍTULO 13 INSTALADO: 23 fórmulas en 2 secciones (19 perfiles nuevos + 4 Board)"
echo "VISTA ÚNICA INSTALADA: INVIGORATE BLOOD ABRE DIRECTAMENTE EL CAPÍTULO 13 COMPLETO"
echo "CAPÍTULO 14 INSTALADO: 17 fórmulas (14 perfiles nuevos + 3 Board; 2 secciones de la fuente)"
echo "PRIORIDAD INSTALADA: LAS 3 FÓRMULAS BOARD APARECEN PRIMERO"
echo "EXPLICACIÓN MÉDICA INSTALADA: COBERTURA DE PATRONES, SEGURIDAD Y LÍMITES CLÍNICOS"
echo "DATOS INTERESANTES INSTALADOS: HIPÓTESIS CLÍNICAS Y CURRICULARES PARA LOS CAPÍTULOS 5–16"
echo "VISTA ÚNICA INSTALADA: EXPEL WIND ABRE DIRECTAMENTE EL CAPÍTULO 14 COMPLETO"
echo "CAPÍTULO 15 INSTALADO: 8 FÓRMULAS PRINCIPALES EN 2 SECCIONES (0 BOARD)"
echo "FÓRMULAS ASOCIADAS INSTALADAS: 6 VARIANTES DENTRO DE SUS FICHAS PRINCIPALES"
echo "SEQUEDAD INSTALADA: EXTERNA FRESCA/CÁLIDA VS. INTERNA POR AGOTAMIENTO DE LÍQUIDOS"
echo "CAPÍTULO 16 INSTALADO: 33 FÓRMULAS EN 5 SECCIONES (25 PERFILES NUEVOS + 8 BOARD)"
echo "VISTA ÚNICA INSTALADA: EXPEL DAMPNESS ABRE DIRECTAMENTE EL CAPÍTULO 16 COMPLETO"
echo "ANEXO INSTALADO: LAS 5 TABLAS COMPARATIVAS DEL CAPÍTULO 16 QUEDAN PLEGADAS AL FINAL"
echo "PUENTE DUÌ YÀO INSTALADO EN EXPEL DAMPNESS: 3 PAREJAS LIMITADAS AL GUIDELINE"
echo "CAPÍTULO 17 INSTALADO: 18 FÓRMULAS EN 7 SECCIONES (13 PERFILES NUEVOS + 5 BOARD)"
echo "VISTA ÚNICA INSTALADA: TRANSFORM PHLEGM ABRE DIRECTAMENTE EL CAPÍTULO 17 COMPLETO"
echo "ANEXO INSTALADO: LAS 7 TABLAS COMPARATIVAS DEL CAPÍTULO 17 QUEDAN PLEGADAS AL FINAL"
echo "PUENTE DUÌ YÀO INSTALADO: 1 PAREJA DE TRANSFORM PHLEGM LIMITADA AL GUIDELINE"
echo "CAPÍTULO 18 INSTALADO: 8 FÓRMULAS EN 2 SECCIONES (7 PERFILES NUEVOS + 1 BOARD)"
echo "VISTA ÚNICA INSTALADA: DIGESTIVE ABRE DIRECTAMENTE EL CAPÍTULO 18 COMPLETO"
echo "PUENTE DUÌ YÀO INSTALADO: BÀN XIÀ + CHÉN PÍ LIMITADO A BǍO HÉ WÁN DEL GUIDELINE"
echo "CAPÍTULO 19 INSTALADO: 3 FÓRMULAS EN 1 SECCIÓN (3 PERFILES SUPLEMENTARIOS, 0 BOARD)"
echo "SEGURIDAD DEL CAPÍTULO 19 INSTALADA: PLOMO, BOTÁNICOS TÓXICOS Y PREPARACIONES HISTÓRICAS OBSOLETAS"
echo "PUENTE DUÌ YÀO INSTALADO: 7 PAREJAS OFICIALES PARA LIBERAR EL EXTERIOR"
echo "PUENTE DUÌ YÀO INSTALADO: 3 PAREJAS DE CLEAR HEAT LIMITADAS AL GUIDELINE"
echo "PUENTE DUÌ YÀO INSTALADO: 1 PAREJA OFICIAL PARA PURGAR CALOR Y ABLANDAR SEQUEDAD"
echo "PUENTE DUÌ YÀO INSTALADO: 3 PAREJAS OFICIALES PARA ARMONIZAR"
echo "PUENTE DUÌ YÀO INSTALADO: 2 PAREJAS OFICIALES PARA CALENTAR EL INTERIOR"
echo "PUENTE DUÌ YÀO INSTALADO: 4 PAREJAS DE TONIFY LIMITADAS AL GUIDELINE"
echo "PUENTE DUÌ YÀO INSTALADO: 1 PAREJA DE STABILIZE AND BIND LIMITADA AL GUIDELINE"
echo "CALM SHEN UNIFICADO: LA TARJETA ABRE DIRECTAMENTE EL CAPÍTULO 10 COMPLETO"
echo "PUENTE DUÌ YÀO INSTALADO: 1 COMPARACIÓN DIVIDIDA DE CALM SHEN LIMITADA AL GUIDELINE"
echo "BLOQUES DUÌ YÀO DE CALM SHEN Y EXPEL WIND MOVIDOS JUSTO ANTES DE SU PRIMERA SECCIÓN"
echo "PUENTE DUÌ YÀO INSTALADO: 4 PAREJAS DE REGULATE QI LIMITADAS AL GUIDELINE"
echo "PUENTE DUÌ YÀO INSTALADO: 3 PAREJAS DE INVIGORATE BLOOD LIMITADAS AL GUIDELINE"
echo "PUENTE DUÌ YÀO INSTALADO: 3 PAREJAS DE EXPEL WIND LIMITADAS AL GUIDELINE"
echo "RETORNO DUÌ YÀO PREPARADO PARA LAS 14 CATEGORÍAS DE FÓRMULAS"
echo "ASTERISCO INSTALADO EN NOMBRES INDIVIDUALES DE FÓRMULAS BOARD"
echo "BOTÓN ESPAÑOL INSTALADO EN LA GUÍA DE HERBOLOGÍA"
echo "NOMBRE UNIFORME INSTALADO: Board Guideline · Chinese Herbal Medicine"
echo "Respaldo: $BACKUP_DIR"
