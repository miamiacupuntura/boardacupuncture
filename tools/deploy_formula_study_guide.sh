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
    $historical = array_filter(
        $data["supplementalFormulas"] ?? [],
        static fn(array $formula): bool => ($formula["categoryKey"] ?? "") === "historical-nccaom-formulas"
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
    $drynessSections = $data["categoryGuides"]["treat-dryness-ch15"]["formulaSections"] ?? [];
    $drynessSectionNames = [];
    foreach ($drynessSections as $section) {
        foreach ($section["formulaNames"] ?? [] as $formulaName) {
            $drynessSectionNames[] = $formulaName;
        }
    }
    $releaseSections = $data["categoryGuides"]["release-exterior"]["formulaSections"] ?? [];
    $releaseSectionNames = [];
    foreach ($releaseSections as $section) {
        foreach ($section["formulaNames"] ?? [] as $formulaName) {
            $releaseSectionNames[] = $formulaName;
        }
    }
    $clearHeatSections = $data["categoryGuides"]["clear-heat"]["formulaSections"] ?? [];
    $clearHeatSectionNames = [];
    foreach ($clearHeatSections as $section) {
        foreach ($section["formulaNames"] ?? [] as $formulaName) {
            $clearHeatSectionNames[] = $formulaName;
        }
    }
    $harmonizeDuiYao = $data["categoryGuides"]["harmonize"]["relatedDuiYao"] ?? [];
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
    if (($data["version"] ?? null) !== 7
            || count($data["categories"] ?? []) !== 14
            || count($data["formulas"] ?? []) !== 63
            || count($data["supplementalCategories"] ?? []) !== 19
            || count($data["supplementalFormulas"] ?? []) !== 192
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
            || count($historical) !== 1
            || array_column($historical, "name") !== ["Run Chang Wan"]
            || array_column($dampnessSections, "key") !== [
                "transform-harmonize-middle", "clear-damp-heat", "promote-urination",
                "warm-transform-water", "dispel-wind-damp"
            ]
            || array_map(static fn(array $section): int => count($section["formulaNames"] ?? []), $dampnessSections)
                !== [5, 10, 5, 7, 6]
            || count($dampnessSectionNames) !== 33
            || count(array_unique($dampnessSectionNames)) !== 33
            || array_column($dampnessDuiYao, "number") !== [19, 20, 31, 44, 47, 48]
            || count(array_unique(array_column($dampnessDuiYao, "slug"))) !== 6
            || array_column($phlegmSections, "key") !== [
                "dry-damp-transform", "clear-heat-transform", "disperse-clumping",
                "moisten-dryness", "warm-phlegm-cold", "transform-extinguish-wind", "induce-vomiting"
            ]
            || array_map(static fn(array $section): int => count($section["formulaNames"] ?? []), $phlegmSections)
                !== [6, 3, 2, 1, 2, 3, 1]
            || count($phlegmSectionNames) !== 18
            || count(array_unique($phlegmSectionNames)) !== 18
            || array_column($phlegmDuiYao, "number") !== [2, 37, 39, 40, 41]
            || count(array_unique(array_column($phlegmDuiYao, "slug"))) !== 5
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
            || array_column($clearHeatSections, "key") !== [
                "qi-level-heat", "fire-toxin", "organ-heat", "hidden-deficiency-heat"
            ]
            || $clearHeatSectionNames !== [
                "Bai Hu Tang", "Huang Lian Jie Du Tang", "Ma Xing Shi Gan Tang",
                "Long Dan Xie Gan Tang", "Qing Hao Bie Jia Tang"
            ]
            || array_column($harmonizeDuiYao, "number") !== [38, 45, 46]
            || count(array_unique(array_column($harmonizeDuiYao, "slug"))) !== 3
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

grep -q "tonify-chapter-8" "$TEMP_DIR/formulas-study-guide.php"
grep -q "selectedformulasections" "$TEMP_DIR/formulas-study-guide.php"
grep -q "clinical-section-" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Por qué Viento-Frío y Viento-Calor deben estudiarse por separado" "$TEMP_DIR/formulas-study-guide-data.json"
grep -q '"key": "wind-cold"' "$TEMP_DIR/formulas-study-guide-data.json"
grep -q '"key": "wind-heat"' "$TEMP_DIR/formulas-study-guide-data.json"
grep -q '"key": "cold-transforming-heat"' "$TEMP_DIR/formulas-study-guide-data.json"
grep -q "No todo Calor es igual: identifica profundidad, toxicidad, localización y raíz" "$TEMP_DIR/formulas-study-guide-data.json"
grep -q '"key": "qi-level-heat"' "$TEMP_DIR/formulas-study-guide-data.json"
grep -q '"key": "fire-toxin"' "$TEMP_DIR/formulas-study-guide-data.json"
grep -q '"key": "organ-heat"' "$TEMP_DIR/formulas-study-guide-data.json"
grep -q '"key": "hidden-deficiency-heat"' "$TEMP_DIR/formulas-study-guide-data.json"
grep -q "Una familia Cheng Qi, tres grados de aglomeración y tres arquitecturas" "$TEMP_DIR/formulas-study-guide-data.json"
grep -q '"key": "severe-complete-clumping"' "$TEMP_DIR/formulas-study-guide-data.json"
grep -q '"key": "moderate-clumping"' "$TEMP_DIR/formulas-study-guide-data.json"
grep -q '"key": "dryness-predominant"' "$TEMP_DIR/formulas-study-guide-data.json"
grep -q "La categoría Purge contiene cinco clases terapéuticas diferentes" "$TEMP_DIR/formulas-study-guide-data.json"
grep -q '"key": "cold-purgatives"' "$TEMP_DIR/formulas-study-guide-data.json"
grep -q '"key": "warm-purgatives"' "$TEMP_DIR/formulas-study-guide-data.json"
grep -q '"key": "moistening-laxatives"' "$TEMP_DIR/formulas-study-guide-data.json"
grep -q '"key": "attack-and-tonify"' "$TEMP_DIR/formulas-study-guide-data.json"
grep -q '"key": "harsh-water-expellants"' "$TEMP_DIR/formulas-study-guide-data.json"
grep -q '"Ji Chuan Jian"' "$TEMP_DIR/formulas-study-guide-data.json"
grep -q '"Run Chang Wan"' "$TEMP_DIR/formulas-study-guide-data.json"
grep -q '"key": "historical-nccaom-formulas"' "$TEMP_DIR/formulas-study-guide-data.json"
grep -q "NCCAOM 2020 · retirada del listado vigente" "$TEMP_DIR/formulas-study-guide.php"
grep -q "el uso clínico frecuente no garantiza inclusión en el Board" "$TEMP_DIR/formulas-study-guide-data.json"
grep -q "therapeuticClasses" "$TEMP_DIR/formulas-study-guide.php"
grep -q "boardScopeTitle" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Complete Chapter 8" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Listado completo de fórmulas que tonifican" "$TEMP_DIR/formulas-study-guide.php"
grep -q "stabilize-bind-chapter-9" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Complete Chapter 9" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Listado completo del capítulo 9" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Categoría oficial del Board: 3 fórmulas" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Fuente Bensky completa: 17 fórmulas" "$TEMP_DIR/formulas-study-guide.php"
grep -q "3 fórmulas Board · Capítulo 9 de Bensky: 17" "$TEMP_DIR/formulas-study-guide.php"
grep -q "calm-shen-chapter-10" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Complete Chapter 10" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Listado completo del capítulo 10" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Calmar el Shen” y “Calmar el Espíritu” se usan indistintamente" "$TEMP_DIR/formulas-study-guide.php"
grep -q "es la misma familia que el Board llama “Calm Shen”, no otra categoría" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Fuente Bensky completa: 9 fórmulas; coincidencia Board: 3" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Calmar el Shen / Calmar el Espíritu" "$TEMP_DIR/formulas-study-guide.php"
grep -q "3 fórmulas Board · Capítulo 10 de Bensky: 9" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Abrir familia unificada →" "$TEMP_DIR/formulas-study-guide.php"
grep -q "open-orifices-chapter-11" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Complete Chapter 11" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Listado completo del capítulo 11" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Dato relevante para el examen · 0 de 7 en Appendix C 2026" "$TEMP_DIR/formulas-study-guide.php"
grep -q "regulate-qi-chapter-12" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Complete Chapter 12" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Listado completo del capítulo 12" "$TEMP_DIR/formulas-study-guide.php"
grep -q "5 fórmulas Board · Capítulo 12 de Bensky: 20" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Dato relevante para el examen · 5 de 20 en Appendix C 2026" "$TEMP_DIR/formulas-study-guide.php"
grep -q "'regulate-qi' => 'regulate-qi-chapter-12'" "$TEMP_DIR/formulas-study-guide.php"
! grep -q "Ver 15 fórmulas adicionales de Bensky · Capítulo 12" "$TEMP_DIR/formulas-study-guide.php"
! grep -q "Abrir las 20 fichas del capítulo 12 →" "$TEMP_DIR/formulas-study-guide.php"
! grep -q "Regulate Qi main category" "$TEMP_DIR/formulas-study-guide.php"
grep -q "regulate-blood-chapter-13" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Complete Chapter 13" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Listado completo del capítulo 13" "$TEMP_DIR/formulas-study-guide.php"
grep -q "4 fórmulas Board · Capítulo 13 de Bensky: 23" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Coincidencia con los Board Domains · 4 de 23" "$TEMP_DIR/formulas-study-guide.php"
grep -q "'invigorate-blood' => 'regulate-blood-chapter-13'" "$TEMP_DIR/formulas-study-guide.php"
! grep -q "Ver 19 fórmulas adicionales de Bensky · Capítulo 13" "$TEMP_DIR/formulas-study-guide.php"
! grep -q "Abrir las 23 fichas del capítulo 13 →" "$TEMP_DIR/formulas-study-guide.php"
! grep -q "Invigorate Blood main category" "$TEMP_DIR/formulas-study-guide.php"
grep -q "expel-wind-chapter-14" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Complete Chapter 14" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Listado completo del capítulo 14" "$TEMP_DIR/formulas-study-guide.php"
grep -q "3 fórmulas Board · Capítulo 14 de Bensky: 17" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Coincidencia con los Board Domains · 3 de 17" "$TEMP_DIR/formulas-study-guide.php"
grep -q "'expel-wind' => 'expel-wind-chapter-14'" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Fórmulas del Board primero" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Explicación médica: por qué estas tres son representativas" "$TEMP_DIR/formulas-study-guide.php"
grep -q "formula-study-guide__medical-rationale" "$TEMP_DIR/formulas-study-guide.php"
grep -q "el Board no publica una justificación médica fórmula por fórmula" "$TEMP_DIR/formulas-study-guide.php"
grep -q "una fórmula clásica no sustituye el tratamiento agudo" "$TEMP_DIR/formulas-study-guide.php"
grep -q "\$chapterinterestinginsights = \[" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Dato interesante: el Calor de Verano" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Dato interesante: cuatro fórmulas representan cuatro profundidades" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Dato interesante: el Board puede descomponer las fórmulas mixtas" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Dato interesante: nueve prototipos comprimen" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Dato interesante: la categoría oficial representa tres tipos de pérdida" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Dato interesante: el Board favorece fórmulas que nutren el Shen" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Dato interesante: una familia clásica completa" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Dato interesante: cinco fórmulas cartografían" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Dato interesante: el Board representa estasis de Sangre" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Una sospecha clínica y curricular razonable" "$TEMP_DIR/formulas-study-guide.php"
grep -q "\['Xiao Feng San', 1, 'board'" "$TEMP_DIR/formulas-study-guide.php"
grep -q "\['Xiao Huo Luo Dan', 2, 'board'" "$TEMP_DIR/formulas-study-guide.php"
grep -q "\['Tian Ma Gou Teng Yin', 3, 'board'" "$TEMP_DIR/formulas-study-guide.php"
grep -Fq "return \$name . (\$getboardformula(\$name) !== null ? '*' : '');" "$TEMP_DIR/formulas-study-guide.php"
! grep -q "Expel Wind main category" "$TEMP_DIR/formulas-study-guide.php"
grep -q "treat-dryness-ch15" "$TEMP_DIR/formulas-study-guide.php"
grep -q '"number": "C15-08"' "$TEMP_DIR/formulas-study-guide-data.json"
grep -q '"key": "gently-disperse-external-dryness"' "$TEMP_DIR/formulas-study-guide-data.json"
grep -q '"key": "enrich-yin-internal-dryness"' "$TEMP_DIR/formulas-study-guide-data.json"
grep -q "Dato interesante: el Board no tiene una categoría propia para tratar Sequedad" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Las seis fórmulas asociadas permanecen dentro de sus fichas principales" "$TEMP_DIR/formulas-study-guide-data.json"
grep -q "0 de 8 coincidencias directas" "$TEMP_DIR/formulas-study-guide.php"
grep -q "expel-damp-chapter-16" "$TEMP_DIR/formulas-study-guide.php"
grep -q '"number":"C16-33"' "$TEMP_DIR/formulas-study-guide-data.json"
grep -q '"key":"transform-harmonize-middle"' "$TEMP_DIR/formulas-study-guide-data.json"
grep -q '"key":"dispel-wind-damp"' "$TEMP_DIR/formulas-study-guide-data.json"
grep -q "Coincidencia con los Board Domains · 8 de 33" "$TEMP_DIR/formulas-study-guide.php"
grep -q "8 fórmulas Board · Capítulo 16 de Bensky: 33" "$TEMP_DIR/formulas-study-guide.php"
grep -q "'expel-damp' => 'expel-damp-chapter-16'" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Dato interesante: ocho fórmulas del Board" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Juan Bi Tang no figura en el listado oficial vigente del Appendix C 2026 de NCBAHM" "$TEMP_DIR/formulas-study-guide-data.json"
grep -q '"slug":"bai-zhu-fu-ling-dui-yao"' "$TEMP_DIR/formulas-study-guide-data.json"
grep -q "Duì Yào relacionados · Drenar o transformar Humedad" "$TEMP_DIR/formulas-study-guide-data.json"
grep -q "comparisonchapternumber" "$TEMP_DIR/formulas-study-guide.php"
grep -q "transform-phlegm-chapter-17" "$TEMP_DIR/formulas-study-guide.php"
grep -q '"number":"C17-18"' "$TEMP_DIR/formulas-study-guide-data.json"
grep -q '"key":"dry-damp-transform"' "$TEMP_DIR/formulas-study-guide-data.json"
grep -q '"key":"induce-vomiting"' "$TEMP_DIR/formulas-study-guide-data.json"
grep -q "Capítulo 17: identifica textura, temperatura, localización y movimiento de la Flema" "$TEMP_DIR/formulas-study-guide-data.json"
grep -q "'transform-phlegm' => 'transform-phlegm-chapter-17'" "$TEMP_DIR/formulas-study-guide.php"
grep -q "related-dui-yao-title" "$TEMP_DIR/formulas-study-guide.php"
grep -q "'from' => 'formula-study-guide'" "$TEMP_DIR/formulas-study-guide.php"
grep -q "'related-pairs'" "$TEMP_DIR/formulas-study-guide.php"
grep -q "'digestive'" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Duì Yào relacionados · Liberar Viento-Frío o Viento-Calor" "$TEMP_DIR/formulas-study-guide-data.json"
grep -q '"slug": "ma-huang-gui-zhi-dui-yao"' "$TEMP_DIR/formulas-study-guide-data.json"
grep -q "Duì Yào relacionados · Aclarar Calor según nivel, localización y raíz" "$TEMP_DIR/formulas-study-guide-data.json"
grep -q '"slug": "shi-gao-zhi-mu-dui-yao"' "$TEMP_DIR/formulas-study-guide-data.json"
grep -q "Duì Yào relacionados · Purgar Calor y ablandar dureza seca" "$TEMP_DIR/formulas-study-guide-data.json"
grep -q '"slug": "da-huang-mang-xiao-dui-yao"' "$TEMP_DIR/formulas-study-guide-data.json"
grep -q "Duì Yào relacionados · Armonizar Shaoyang, Hígado y Centro" "$TEMP_DIR/formulas-study-guide-data.json"
grep -q '"slug": "chai-hu-huang-qin-dui-yao"' "$TEMP_DIR/formulas-study-guide-data.json"
grep -q '"slug":"ban-xia-chen-pi-dui-yao"' "$TEMP_DIR/formulas-study-guide-data.json"
grep -q "Duì Yào relacionados · Detener Tos y transformar Flema" "$TEMP_DIR/formulas-study-guide-data.json"
grep -q "Metales tóxicos históricos" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Clave de estudio (fuera del Appendix C 2026)" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Included in the current Board guideline" "$TEMP_DIR/formulas-study-guide.php"
grep -q "displayformulaname" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Bensky et al., Chinese Herbal Medicine: Formulas & Strategies" "$TEMP_DIR/formulas-study-guide.php"
grep -q "Dominio Board · Fórmula #" "$TEMP_DIR/formulas-study-guide.php"
grep -q "← Board Guideline · Chinese Herbal Medicine" "$TEMP_DIR/formulas-study-guide.php"
grep -q "← Guía de Board · Medicina Herbal China" "$TEMP_DIR/formulas-study-guide.php"
grep -q "\$languageurl" "$TEMP_DIR/study-guide.php"
grep -q "Español" "$TEMP_DIR/study-guide.php"
grep -q "← Board Guideline · Chinese Herbal Medicine" "$TEMP_DIR/study-guide.php"
grep -q "← Guía de Board · Medicina Herbal China" "$TEMP_DIR/study-guide.php"

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

grep -q "tonify-chapter-8" "$TARGET_DIR/formulas-study-guide.php"
grep -q "selectedformulasections" "$TARGET_DIR/formulas-study-guide.php"
grep -q "clinical-section-" "$TARGET_DIR/formulas-study-guide.php"
grep -q "Por qué Viento-Frío y Viento-Calor deben estudiarse por separado" "$TARGET_DIR/formulas-study-guide-data.json"
grep -q '"key": "wind-cold"' "$TARGET_DIR/formulas-study-guide-data.json"
grep -q '"key": "wind-heat"' "$TARGET_DIR/formulas-study-guide-data.json"
grep -q '"key": "cold-transforming-heat"' "$TARGET_DIR/formulas-study-guide-data.json"
grep -q "No todo Calor es igual: identifica profundidad, toxicidad, localización y raíz" "$TARGET_DIR/formulas-study-guide-data.json"
grep -q '"key": "qi-level-heat"' "$TARGET_DIR/formulas-study-guide-data.json"
grep -q '"key": "fire-toxin"' "$TARGET_DIR/formulas-study-guide-data.json"
grep -q '"key": "organ-heat"' "$TARGET_DIR/formulas-study-guide-data.json"
grep -q '"key": "hidden-deficiency-heat"' "$TARGET_DIR/formulas-study-guide-data.json"
grep -q "Una familia Cheng Qi, tres grados de aglomeración y tres arquitecturas" "$TARGET_DIR/formulas-study-guide-data.json"
grep -q '"key": "severe-complete-clumping"' "$TARGET_DIR/formulas-study-guide-data.json"
grep -q '"key": "moderate-clumping"' "$TARGET_DIR/formulas-study-guide-data.json"
grep -q '"key": "dryness-predominant"' "$TARGET_DIR/formulas-study-guide-data.json"
grep -q "La categoría Purge contiene cinco clases terapéuticas diferentes" "$TARGET_DIR/formulas-study-guide-data.json"
grep -q '"key": "cold-purgatives"' "$TARGET_DIR/formulas-study-guide-data.json"
grep -q '"key": "warm-purgatives"' "$TARGET_DIR/formulas-study-guide-data.json"
grep -q '"key": "moistening-laxatives"' "$TARGET_DIR/formulas-study-guide-data.json"
grep -q '"key": "attack-and-tonify"' "$TARGET_DIR/formulas-study-guide-data.json"
grep -q '"key": "harsh-water-expellants"' "$TARGET_DIR/formulas-study-guide-data.json"
grep -q '"Ji Chuan Jian"' "$TARGET_DIR/formulas-study-guide-data.json"
grep -q '"Run Chang Wan"' "$TARGET_DIR/formulas-study-guide-data.json"
grep -q '"key": "historical-nccaom-formulas"' "$TARGET_DIR/formulas-study-guide-data.json"
grep -q "NCCAOM 2020 · retirada del listado vigente" "$TARGET_DIR/formulas-study-guide.php"
grep -q "el uso clínico frecuente no garantiza inclusión en el Board" "$TARGET_DIR/formulas-study-guide-data.json"
grep -q "therapeuticClasses" "$TARGET_DIR/formulas-study-guide.php"
grep -q "boardScopeTitle" "$TARGET_DIR/formulas-study-guide.php"
grep -q "Categoría oficial del Board: 3 fórmulas" "$TARGET_DIR/formulas-study-guide.php"
grep -q "3 fórmulas Board · Capítulo 9 de Bensky: 17" "$TARGET_DIR/formulas-study-guide.php"
grep -q "Calmar el Shen” y “Calmar el Espíritu” se usan indistintamente" "$TARGET_DIR/formulas-study-guide.php"
grep -q "Fuente Bensky completa: 9 fórmulas; coincidencia Board: 3" "$TARGET_DIR/formulas-study-guide.php"
grep -q "Calmar el Shen / Calmar el Espíritu" "$TARGET_DIR/formulas-study-guide.php"
grep -q "open-orifices-chapter-11" "$TARGET_DIR/formulas-study-guide.php"
grep -q "Dato relevante para el examen · 0 de 7 en Appendix C 2026" "$TARGET_DIR/formulas-study-guide.php"
grep -q "regulate-qi-chapter-12" "$TARGET_DIR/formulas-study-guide.php"
grep -q "5 fórmulas Board · Capítulo 12 de Bensky: 20" "$TARGET_DIR/formulas-study-guide.php"
grep -q "Dato relevante para el examen · 5 de 20 en Appendix C 2026" "$TARGET_DIR/formulas-study-guide.php"
grep -q "'regulate-qi' => 'regulate-qi-chapter-12'" "$TARGET_DIR/formulas-study-guide.php"
! grep -q "Ver 15 fórmulas adicionales de Bensky · Capítulo 12" "$TARGET_DIR/formulas-study-guide.php"
! grep -q "Abrir las 20 fichas del capítulo 12 →" "$TARGET_DIR/formulas-study-guide.php"
! grep -q "Regulate Qi main category" "$TARGET_DIR/formulas-study-guide.php"
grep -q "regulate-blood-chapter-13" "$TARGET_DIR/formulas-study-guide.php"
grep -q "4 fórmulas Board · Capítulo 13 de Bensky: 23" "$TARGET_DIR/formulas-study-guide.php"
grep -q "Coincidencia con los Board Domains · 4 de 23" "$TARGET_DIR/formulas-study-guide.php"
grep -q "'invigorate-blood' => 'regulate-blood-chapter-13'" "$TARGET_DIR/formulas-study-guide.php"
! grep -q "Ver 19 fórmulas adicionales de Bensky · Capítulo 13" "$TARGET_DIR/formulas-study-guide.php"
! grep -q "Abrir las 23 fichas del capítulo 13 →" "$TARGET_DIR/formulas-study-guide.php"
! grep -q "Invigorate Blood main category" "$TARGET_DIR/formulas-study-guide.php"
grep -q "expel-wind-chapter-14" "$TARGET_DIR/formulas-study-guide.php"
grep -q "3 fórmulas Board · Capítulo 14 de Bensky: 17" "$TARGET_DIR/formulas-study-guide.php"
grep -q "Coincidencia con los Board Domains · 3 de 17" "$TARGET_DIR/formulas-study-guide.php"
grep -q "'expel-wind' => 'expel-wind-chapter-14'" "$TARGET_DIR/formulas-study-guide.php"
grep -q "Fórmulas del Board primero" "$TARGET_DIR/formulas-study-guide.php"
grep -q "Explicación médica: por qué estas tres son representativas" "$TARGET_DIR/formulas-study-guide.php"
grep -q "formula-study-guide__medical-rationale" "$TARGET_DIR/formulas-study-guide.php"
grep -q "el Board no publica una justificación médica fórmula por fórmula" "$TARGET_DIR/formulas-study-guide.php"
grep -q "una fórmula clásica no sustituye el tratamiento agudo" "$TARGET_DIR/formulas-study-guide.php"
grep -q "\$chapterinterestinginsights = \[" "$TARGET_DIR/formulas-study-guide.php"
grep -q "Dato interesante: el Calor de Verano" "$TARGET_DIR/formulas-study-guide.php"
grep -q "Dato interesante: cuatro fórmulas representan cuatro profundidades" "$TARGET_DIR/formulas-study-guide.php"
grep -q "Dato interesante: el Board puede descomponer las fórmulas mixtas" "$TARGET_DIR/formulas-study-guide.php"
grep -q "Dato interesante: nueve prototipos comprimen" "$TARGET_DIR/formulas-study-guide.php"
grep -q "Dato interesante: la categoría oficial representa tres tipos de pérdida" "$TARGET_DIR/formulas-study-guide.php"
grep -q "Dato interesante: el Board favorece fórmulas que nutren el Shen" "$TARGET_DIR/formulas-study-guide.php"
grep -q "Dato interesante: una familia clásica completa" "$TARGET_DIR/formulas-study-guide.php"
grep -q "Dato interesante: cinco fórmulas cartografían" "$TARGET_DIR/formulas-study-guide.php"
grep -q "Dato interesante: el Board representa estasis de Sangre" "$TARGET_DIR/formulas-study-guide.php"
grep -q "Una sospecha clínica y curricular razonable" "$TARGET_DIR/formulas-study-guide.php"
grep -q "\['Xiao Feng San', 1, 'board'" "$TARGET_DIR/formulas-study-guide.php"
grep -q "\['Xiao Huo Luo Dan', 2, 'board'" "$TARGET_DIR/formulas-study-guide.php"
grep -q "\['Tian Ma Gou Teng Yin', 3, 'board'" "$TARGET_DIR/formulas-study-guide.php"
grep -Fq "return \$name . (\$getboardformula(\$name) !== null ? '*' : '');" "$TARGET_DIR/formulas-study-guide.php"
! grep -q "Expel Wind main category" "$TARGET_DIR/formulas-study-guide.php"
grep -q "treat-dryness-ch15" "$TARGET_DIR/formulas-study-guide.php"
grep -q '"number": "C15-08"' "$TARGET_DIR/formulas-study-guide-data.json"
grep -q '"key": "gently-disperse-external-dryness"' "$TARGET_DIR/formulas-study-guide-data.json"
grep -q '"key": "enrich-yin-internal-dryness"' "$TARGET_DIR/formulas-study-guide-data.json"
grep -q "Dato interesante: el Board no tiene una categoría propia para tratar Sequedad" "$TARGET_DIR/formulas-study-guide.php"
grep -q "Las seis fórmulas asociadas permanecen dentro de sus fichas principales" "$TARGET_DIR/formulas-study-guide-data.json"
grep -q "0 de 8 coincidencias directas" "$TARGET_DIR/formulas-study-guide.php"
grep -q "expel-damp-chapter-16" "$TARGET_DIR/formulas-study-guide.php"
grep -q '"number":"C16-33"' "$TARGET_DIR/formulas-study-guide-data.json"
grep -q '"key":"transform-harmonize-middle"' "$TARGET_DIR/formulas-study-guide-data.json"
grep -q '"key":"dispel-wind-damp"' "$TARGET_DIR/formulas-study-guide-data.json"
grep -q "Coincidencia con los Board Domains · 8 de 33" "$TARGET_DIR/formulas-study-guide.php"
grep -q "8 fórmulas Board · Capítulo 16 de Bensky: 33" "$TARGET_DIR/formulas-study-guide.php"
grep -q "'expel-damp' => 'expel-damp-chapter-16'" "$TARGET_DIR/formulas-study-guide.php"
grep -q "Dato interesante: ocho fórmulas del Board" "$TARGET_DIR/formulas-study-guide.php"
grep -q "Juan Bi Tang no figura en el listado oficial vigente del Appendix C 2026 de NCBAHM" "$TARGET_DIR/formulas-study-guide-data.json"
grep -q '"slug":"bai-zhu-fu-ling-dui-yao"' "$TARGET_DIR/formulas-study-guide-data.json"
grep -q "Duì Yào relacionados · Drenar o transformar Humedad" "$TARGET_DIR/formulas-study-guide-data.json"
grep -q "comparisonchapternumber" "$TARGET_DIR/formulas-study-guide.php"
grep -q "transform-phlegm-chapter-17" "$TARGET_DIR/formulas-study-guide.php"
grep -q '"number":"C17-18"' "$TARGET_DIR/formulas-study-guide-data.json"
grep -q '"key":"dry-damp-transform"' "$TARGET_DIR/formulas-study-guide-data.json"
grep -q '"key":"induce-vomiting"' "$TARGET_DIR/formulas-study-guide-data.json"
grep -q "Capítulo 17: identifica textura, temperatura, localización y movimiento de la Flema" "$TARGET_DIR/formulas-study-guide-data.json"
grep -q "'transform-phlegm' => 'transform-phlegm-chapter-17'" "$TARGET_DIR/formulas-study-guide.php"
grep -q "related-dui-yao-title" "$TARGET_DIR/formulas-study-guide.php"
grep -q "'from' => 'formula-study-guide'" "$TARGET_DIR/formulas-study-guide.php"
grep -q "'related-pairs'" "$TARGET_DIR/formulas-study-guide.php"
grep -q "Duì Yào relacionados · Liberar Viento-Frío o Viento-Calor" "$TARGET_DIR/formulas-study-guide-data.json"
grep -q '"slug": "ma-huang-gui-zhi-dui-yao"' "$TARGET_DIR/formulas-study-guide-data.json"
grep -q "Duì Yào relacionados · Aclarar Calor según nivel, localización y raíz" "$TARGET_DIR/formulas-study-guide-data.json"
grep -q '"slug": "shi-gao-zhi-mu-dui-yao"' "$TARGET_DIR/formulas-study-guide-data.json"
grep -q "Duì Yào relacionados · Purgar Calor y ablandar dureza seca" "$TARGET_DIR/formulas-study-guide-data.json"
grep -q '"slug": "da-huang-mang-xiao-dui-yao"' "$TARGET_DIR/formulas-study-guide-data.json"
grep -q "Duì Yào relacionados · Armonizar Shaoyang, Hígado y Centro" "$TARGET_DIR/formulas-study-guide-data.json"
grep -q '"slug": "chai-hu-huang-qin-dui-yao"' "$TARGET_DIR/formulas-study-guide-data.json"
grep -q '"slug":"ban-xia-chen-pi-dui-yao"' "$TARGET_DIR/formulas-study-guide-data.json"
grep -q "Duì Yào relacionados · Detener Tos y transformar Flema" "$TARGET_DIR/formulas-study-guide-data.json"
grep -q "Español" "$TARGET_DIR/study-guide.php"
grep -q "← Board Guideline · Chinese Herbal Medicine" "$TARGET_DIR/formulas-study-guide.php"
grep -q "← Board Guideline · Chinese Herbal Medicine" "$TARGET_DIR/study-guide.php"

echo "CAPÍTULOS 8, 9, 10, 11, 12, 13, 14, 15, 16 Y 17 INSTALADOS EN MOODLE"
echo "CATEGORÍA 1 REORGANIZADA: VIENTO-FRÍO, VIENTO-CALOR Y TRANSFORMACIÓN A CALOR"
echo "CATEGORÍA 2 REORGANIZADA: NIVEL QI, FUEGO-TOXINA, CALOR DE ÓRGANOS Y CALOR OCULTO"
echo "CATEGORÍA 3 REORGANIZADA: AGLOMERACIÓN CHENG QI GRAVE, MODERADA Y SECA"
echo "CLASIFICACIÓN PURGE INSTALADA: PURGANTES FRÍOS/CALIENTES, LAXANTES, ATAQUE-TONIFICACIÓN Y EXPELENTES"
echo "RUN CHANG WAN INSTALADA: LAXANTE HUMECTANTE SECUNDARIO, SIN ASTERISCO BOARD"
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
echo "PUENTE DUÌ YÀO INSTALADO EN EXPEL DAMPNESS: 6 PAREJAS OFICIALES RELACIONADAS"
echo "CAPÍTULO 17 INSTALADO: 18 FÓRMULAS EN 7 SECCIONES (13 PERFILES NUEVOS + 5 BOARD)"
echo "VISTA ÚNICA INSTALADA: TRANSFORM PHLEGM ABRE DIRECTAMENTE EL CAPÍTULO 17 COMPLETO"
echo "ANEXO INSTALADO: LAS 7 TABLAS COMPARATIVAS DEL CAPÍTULO 17 QUEDAN PLEGADAS AL FINAL"
echo "PUENTE DUÌ YÀO INSTALADO: 5 PAREJAS OFICIALES RELACIONADAS CON TOS Y FLEMA"
echo "PUENTE DUÌ YÀO INSTALADO: 7 PAREJAS OFICIALES PARA LIBERAR EL EXTERIOR"
echo "PUENTE DUÌ YÀO INSTALADO: 7 PAREJAS OFICIALES PARA ACLARAR CALOR"
echo "PUENTE DUÌ YÀO INSTALADO: 1 PAREJA OFICIAL PARA PURGAR CALOR Y ABLANDAR SEQUEDAD"
echo "PUENTE DUÌ YÀO INSTALADO: 3 PAREJAS OFICIALES PARA ARMONIZAR"
echo "RETORNO DUÌ YÀO PREPARADO PARA LAS 14 CATEGORÍAS DE FÓRMULAS"
echo "ASTERISCO INSTALADO EN NOMBRES INDIVIDUALES DE FÓRMULAS BOARD"
echo "BOTÓN ESPAÑOL INSTALADO EN LA GUÍA DE HERBOLOGÍA"
echo "NOMBRE UNIFORME INSTALADO: Board Guideline · Chinese Herbal Medicine"
echo "Respaldo: $BACKUP_DIR"
