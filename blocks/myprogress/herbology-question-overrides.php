<?php
// Persistent administrator-authored revisions for the NCBAHM Herbology bank.

defined('MOODLE_INTERNAL') || die();

function block_myprogress_herbology_override_path(): string {
    global $CFG;
    return $CFG->dataroot . '/board-domain-banks/herbology-question-overrides.json';
}

function block_myprogress_herbology_override_empty(): array {
    return [
        'schemaVersion' => 1,
        'updatedAt' => null,
        'questions' => [],
        'customQuestions' => [],
        'instructionTemplates' => [],
        'examProjects' => [],
        'publishedExams' => [],
        'publishedExtraGuideline' => null,
    ];
}

/**
 * Move records created by the short-lived Mock-based outside-guideline flow
 * into the independent Extra guideline bank without touching genuine Mock
 * questions.
 */
function block_myprogress_herbology_migrate_extra_guideline_store(
    array $store,
    string $migrationversion
): array {
    foreach ((array)($store['customQuestions'] ?? []) as $questionid => $record) {
        if (!is_array($record)
                || (string)($record['status'] ?? '') !== 'study_archive'
                || (string)($record['archiveModule'] ?? '') !== 'mock_only'
                || (string)($record['guidelineValidation']['status'] ?? '') !== 'blocked') {
            continue;
        }
        $violations = array_values(array_filter(array_map(
            'strval',
            (array)($record['guidelineValidation']['violations'] ?? [])
        )));
        $record['status'] = 'extra_guideline';
        $record['archiveModule'] = 'extra_guideline';
        $record['extraGuidelineDomain'] = (string)($record['domainId'] ?? '');
        $record['statusBeforeExtraGuideline'] = (string)($record['statusBeforeStudyArchive'] ?? 'draft');
        $record['hadApprovedBeforeExtraGuideline'] = !empty($record['hadApprovedBeforeStudyArchive']);
        $record['statusBeforeStudyArchive'] = '';
        $record['hadApprovedBeforeStudyArchive'] = false;
        $record['mockCategory'] = '';
        $record['mockPlacementStatus'] = '';
        $record['exclusionReason'] = 'Stored in the Extra guideline bank; not eligible for Board, Mock, or Simulation Exams.'
            . ($violations !== [] ? ' ' . implode(' | ', $violations) : '');
        $store['customQuestions'][$questionid] = $record;
    }
    $store['extraGuidelineMigrationVersion'] = $migrationversion;
    return $store;
}

/**
 * Remove tone marks from Pinyin without removing ordinary Spanish accents.
 *
 * Replacements are discovered only in the English/source side of a question.
 * The exact same Pinyin tokens are then replaced in the Spanish translation,
 * which leaves words such as "opción" and "fórmula" untouched.
 */
function block_myprogress_herbology_remove_pinyin_tones_from_question(array $question): array {
    $tonemap = [
        'ā' => 'a', 'á' => 'a', 'ǎ' => 'a', 'à' => 'a',
        'ē' => 'e', 'é' => 'e', 'ě' => 'e', 'è' => 'e',
        'ī' => 'i', 'í' => 'i', 'ǐ' => 'i', 'ì' => 'i',
        'ō' => 'o', 'ó' => 'o', 'ǒ' => 'o', 'ò' => 'o',
        'ū' => 'u', 'ú' => 'u', 'ǔ' => 'u', 'ù' => 'u',
        'ǖ' => 'ü', 'ǘ' => 'ü', 'ǚ' => 'ü', 'ǜ' => 'ü',
        'ǹ' => 'n', 'ń' => 'n', 'ň' => 'n', 'ḿ' => 'm',
        'Ā' => 'A', 'Á' => 'A', 'Ǎ' => 'A', 'À' => 'A',
        'Ē' => 'E', 'É' => 'E', 'Ě' => 'E', 'È' => 'E',
        'Ī' => 'I', 'Í' => 'I', 'Ǐ' => 'I', 'Ì' => 'I',
        'Ō' => 'O', 'Ó' => 'O', 'Ǒ' => 'O', 'Ò' => 'O',
        'Ū' => 'U', 'Ú' => 'U', 'Ǔ' => 'U', 'Ù' => 'U',
        'Ǖ' => 'Ü', 'Ǘ' => 'Ü', 'Ǚ' => 'Ü', 'Ǜ' => 'Ü',
        'Ǹ' => 'N', 'Ń' => 'N', 'Ň' => 'N', 'Ḿ' => 'M',
    ];
    $englishfields = [];
    foreach (['prompt', 'options', 'explanation', 'reviewSummary', 'source', 'nomenclature'] as $field) {
        if (array_key_exists($field, $question)) {
            $englishfields[$field] = $question[$field];
        }
    }
    if (is_array($question['sourceCard'] ?? null)) {
        $englishfields['sourceCardLabel'] = (string)($question['sourceCard']['label'] ?? '');
    }
    $replacements = [];
    $scan = static function($candidate) use (&$scan, &$replacements, $tonemap): void {
        if (is_array($candidate)) {
            foreach ($candidate as $nestedvalue) {
                $scan($nestedvalue);
            }
            return;
        }
        if (!is_string($candidate) || $candidate === '') {
            return;
        }
        preg_match_all('/[\p{L}\p{M}\x{2019}\x{0027}-]+/u', $candidate, $matches);
        foreach ((array)($matches[0] ?? []) as $token) {
            $normalizedtoken = strtr((string)$token, $tonemap);
            if ($normalizedtoken !== (string)$token) {
                $replacements[(string)$token] = $normalizedtoken;
            }
        }
    };
    $scan($englishfields);
    if ($replacements === []) {
        return $question;
    }
    $replace = static function($value) use (&$replace, $replacements) {
        if (is_array($value)) {
            foreach ($value as $key => $nestedvalue) {
                $value[$key] = $replace($nestedvalue);
            }
            return $value;
        }
        return is_string($value) ? strtr($value, $replacements) : $value;
    };
    foreach (array_keys($englishfields) as $field) {
        if ($field === 'sourceCardLabel') {
            $question['sourceCard']['label'] = $replace(
                (string)($question['sourceCard']['label'] ?? '')
            );
            continue;
        }
        $question[$field] = $replace($question[$field]);
    }
    if (is_array($question['translations']['es'] ?? null)) {
        $question['translations']['es'] = $replace($question['translations']['es']);
    }
    if (is_array($question['sourceCard'] ?? null)) {
        $question['sourceCard']['labelEs'] = $replace(
            (string)($question['sourceCard']['labelEs'] ?? '')
        );
    }
    return $question;
}

/**
 * Source-supported obsolete substances used only for safety recognition.
 * They are deliberately not part of the active Appendix A roster.
 */
function block_myprogress_herbology_obsolete_substance_roster(): array {
    return [
        ['name' => 'Zhu Sha', 'latin' => 'Cinnabaris'],
        ['name' => 'Ying Su Ke', 'latin' => 'Papaveris Pericarpium'],
        ['name' => 'Ma Qian Zi', 'latin' => 'Strychni Semen'],
        ['name' => 'Lei Gong Teng', 'latin' => 'Tripterygii wilfordii Radix'],
        ['name' => 'Ma Dou Ling', 'latin' => 'Aristolochiae Fructus'],
        ['name' => 'Guan Mu Tong', 'latin' => 'Aristolochiae manshuriensis Caulis'],
        ['name' => 'Guang Fang Ji', 'latin' => 'Aristolochiae fangchi Radix'],
        ['name' => 'Xun Gu Feng', 'latin' => 'Aristolochiae mollissimae Herba'],
        [
            'name' => 'Qing Fen',
            'latin' => 'Calomelas',
            'aliases' => ['Qi Feng'],
            'latinAliases' => ['Calomela'],
        ],
        ['name' => 'Xiong Huang', 'latin' => 'Realgar'],
        ['name' => 'Mi Tuo Seng', 'latin' => 'Lithargyrum'],
        ['name' => 'Peng Sha', 'latin' => 'Borax'],
        ['name' => 'Xiong Dan', 'latin' => 'Vesica Fellea Ursi'],
        [
            'name' => 'Xi Jiao',
            'latin' => 'Rhinocerotis Cornu',
            'latinAliases' => ['Rhinoceros Horn'],
        ],
        ['name' => 'Chuan Shan Jia', 'latin' => 'Manitis Squama'],
        ['name' => 'Dai Mao', 'latin' => 'Eretmochelydis Carapax'],
        ['name' => 'Hou Zao', 'latin' => 'Macacae mulattae Calculus'],
        ['name' => 'Hu Gu', 'latin' => 'Tigris Os'],
    ];
}

/**
 * Source-supported display names that are useful in editorial material but
 * are not promoted into the active Appendix A roster.
 */
function block_myprogress_herbology_supplemental_display_roster(): array {
    return [
        ['name' => 'Nan Gua Zi', 'latin' => 'Cucurbitae moschatae Semen'],
        ['name' => 'Da Suan', 'latin' => 'Allii sativi Bulbus'],
        ['name' => 'Hu Po', 'latin' => 'Succinum'],
    ];
}

/**
 * Return the official Appendix A/C Pinyin display names, the recognized
 * obsolete safety names, and one reusable case-insensitive matcher. The
 * matcher is built once per request because it is applied to thousands of
 * questions in the administrator bank.
 */
function block_myprogress_herbology_official_pinyin_case_data(): array {
    global $CFG;

    static $data = null;
    if (is_array($data)) {
        return $data;
    }
    $canonicalnames = [];
    $herbdisplays = [];
    $herbpinyinpatterns = [];
    $herblatinpatterns = [];
    $formuladisplays = [];
    $formulapatterns = [];
    $appendixaherbaliases = [
        // Imported sources commonly abbreviate Huai Niu Xi to generic Niu Xi.
        // Appendix A distinguishes it from Chuan Niu Xi; the attached
        // Achyranthis bidentatae Radix identity resolves the abbreviation.
        'Huai Niu Xi' => ['Niu Xi'],
        // Legacy SCORM sources label processed aconite as Zhi Fu Zi. It is
        // the processed presentation of the Appendix A entry Fu Zi, not a
        // separate herb identity.
        'Fu Zi' => ['Zhi Fu Zi'],
    ];
    $pharmaceuticalparts = [
        'bulbus', 'calculus', 'caulis', 'cortex', 'flos', 'folium', 'fructus', 'herba',
        'pericarpium', 'radix', 'ramulus', 'resina', 'rhizoma', 'semen', 'spora', 'stigma',
        'tuber',
    ];
    $flexiblepattern = static function(string $name): string {
        return implode('\\s+', array_map(
            static fn(string $word): string => preg_quote($word, '/'),
            preg_split('/\s+/u', trim($name), -1, PREG_SPLIT_NO_EMPTY)
        ));
    };
    $addherbdisplay = static function(
        string $officialname,
        string $officiallatin,
        array $aliases = [],
        array $additionallatinaliases = []
    ) use (
        &$canonicalnames,
        &$herbdisplays,
        &$herbpinyinpatterns,
        &$herblatinpatterns,
        $pharmaceuticalparts,
        $flexiblepattern
    ): void {
        $officiallatin = trim($officiallatin);
        if (trim($officialname) === '' || $officiallatin === '') {
            return;
        }
        $latinaliases = array_values(array_unique(array_merge([$officiallatin], $additionallatinaliases)));
        $latinwords = preg_split('/\s+/u', $officiallatin, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        foreach ($latinwords as $latinindex => $latinword) {
            if (!in_array(strtolower($latinword), $pharmaceuticalparts, true) || $latinindex === 0) {
                continue;
            }
            $legacywords = $latinwords;
            unset($legacywords[$latinindex]);
            array_unshift($legacywords, $latinword);
            $latinaliases[] = implode(' ', $legacywords);
        }
        $normalizedlatinaliases = [];
        foreach (array_values(array_unique($latinaliases)) as $latinalias) {
            $normalizedlatin = strtolower((string)preg_replace('/\s+/u', ' ', trim($latinalias)));
            if ($normalizedlatin === '') {
                continue;
            }
            $normalizedlatinaliases[$normalizedlatin] = true;
            $herblatinpatterns[$normalizedlatin] = $flexiblepattern($latinalias);
        }
        foreach (array_values(array_unique(array_merge([$officialname], $aliases))) as $pinyinname) {
            $lookupname = strtolower((string)preg_replace('/\s+/u', ' ', trim((string)$pinyinname)));
            if ($lookupname === '') {
                continue;
            }
            $canonicalnames[$lookupname] = $officialname;
            $herbdisplays[$lookupname] = [
                'name' => $officialname,
                'latin' => $officiallatin,
                'latinAliases' => $normalizedlatinaliases,
            ];
            $herbpinyinpatterns[$lookupname] = $flexiblepattern((string)$pinyinname);
        }
    };
    foreach ([
        [$CFG->dirroot . '/blocks/myprogress/single-herb-card-index.json', 'herbs'],
        [$CFG->dirroot . '/blocks/myprogress/formula-board-roster-2026.json', 'formulas'],
    ] as [$rosterpath, $collectionkey]) {
        if (!is_readable($rosterpath)) {
            continue;
        }
        $roster = json_decode((string)file_get_contents($rosterpath), true);
        foreach ((array)($roster[$collectionkey] ?? []) as $record) {
            $officialname = trim((string)($record['name'] ?? ''));
            $lookupname = strtolower((string)preg_replace('/\s+/u', ' ', $officialname));
            if ($lookupname !== '') {
                $canonicalnames[$lookupname] = $officialname;
            }
            if ($collectionkey === 'formulas' && $lookupname !== '') {
                $formulapatterns[$lookupname] = $flexiblepattern($officialname);
                $formuladisplays[$lookupname] = [
                    'name' => $officialname,
                    'english' => trim((string)($record['englishName'] ?? '')),
                ];
            }
            if ($collectionkey !== 'herbs' || $lookupname === '') {
                continue;
            }
            $officiallatin = trim((string)($record['latin'] ?? ''));
            $addherbdisplay(
                $officialname,
                $officiallatin,
                (array)($appendixaherbaliases[$officialname] ?? [])
            );
        }
    }
    foreach (block_myprogress_herbology_obsolete_substance_roster() as $obsoletesubstance) {
        $addherbdisplay(
            (string)($obsoletesubstance['name'] ?? ''),
            (string)($obsoletesubstance['latin'] ?? ''),
            (array)($obsoletesubstance['aliases'] ?? []),
            (array)($obsoletesubstance['latinAliases'] ?? [])
        );
    }
    // These source-supported Bensky teaching herbs are intentionally not
    // promoted into Appendix A eligibility.  Registering their display names
    // here only lets legacy SCORM choices use the same Pinyin + pharmaceutical
    // Latin presentation as current-roster choices.
    foreach (block_myprogress_herbology_supplemental_display_roster() as $legacyteachingherb) {
        $addherbdisplay(
            (string)$legacyteachingherb['name'],
            (string)$legacyteachingherb['latin']
        );
    }
    if ($canonicalnames === []) {
        $data = [
            'names' => [],
            'pattern' => '',
            'herbDisplays' => [],
            'formulaDisplays' => [],
            'bareHerbPattern' => '',
            'bareFormulaPattern' => '',
            'formulaPattern' => '',
            'parentheticalFormulaPattern' => '',
            'parentheticalHerbPattern' => '',
            'commonNameHerbPattern' => '',
            'legacyHerbPattern' => '',
            'legacyHerbOptionPattern' => '',
        ];
        return $data;
    }
    $lookupnames = array_keys($canonicalnames);
    usort($lookupnames, static fn(string $left, string $right): int => strlen($right) <=> strlen($left));
    uksort(
        $herbpinyinpatterns,
        static fn(string $left, string $right): int => strlen($right) <=> strlen($left)
    );
    uksort(
        $herblatinpatterns,
        static fn(string $left, string $right): int => strlen($right) <=> strlen($left)
    );
    uksort(
        $formulapatterns,
        static fn(string $left, string $right): int => strlen($right) <=> strlen($left)
    );
    $alternatives = array_map(static function(string $lookupname): string {
        return implode('\\s+', array_map(
            static fn(string $word): string => preg_quote($word, '/'),
            preg_split('/\s+/u', $lookupname, -1, PREG_SPLIT_NO_EMPTY)
        ));
    }, $lookupnames);
    $data = [
        'names' => $canonicalnames,
        'pattern' => '/(?<![A-Za-z0-9])(?:' . implode('|', $alternatives) . ')(?![A-Za-z0-9])/iu',
        'herbDisplays' => $herbdisplays,
        'formulaDisplays' => $formuladisplays,
        'bareHerbPattern' => $herbpinyinpatterns === []
            ? ''
            : '/(?<![A-Za-z0-9])(?<pinyin>' . implode('|', array_values($herbpinyinpatterns)) . ')'
                . '(?![A-Za-z0-9])/iu',
        'formulaPattern' => $formulapatterns === []
            ? ''
            : '/(?<![A-Za-z0-9])(?:' . implode('|', array_values($formulapatterns)) . ')'
                . '(?![A-Za-z0-9])/iu',
        'bareFormulaPattern' => $formulapatterns === []
            ? ''
            : '/(?<![A-Za-z0-9])(?<formula>' . implode('|', array_values($formulapatterns)) . ')'
                . '(?![A-Za-z0-9]|\s*\()/iu',
        'parentheticalFormulaPattern' => $formulapatterns === []
            ? ''
            : '/(?<![A-Za-z0-9])(?<formula>' . implode('|', array_values($formulapatterns)) . ')'
                . '\s*\([^()\r\n]{1,160}\)(?![A-Za-z0-9])/iu',
        'parentheticalHerbPattern' => $herbpinyinpatterns === [] || $herblatinpatterns === []
            ? ''
            : '/(?<![A-Za-z0-9])(?<pinyin>' . implode('|', array_values($herbpinyinpatterns)) . ')'
                . '\s*\(\s*(?<latin>' . implode('|', array_values($herblatinpatterns)) . ')\s*\)'
                . '(?![A-Za-z0-9])/iu',
        'commonNameHerbPattern' => $herbpinyinpatterns === []
            ? ''
            : '/(?<![A-Za-z0-9])(?<pinyin>' . implode('|', array_values($herbpinyinpatterns)) . ')'
                . '\s*\([^()\r\n]{1,120}\)(?![A-Za-z0-9])/iu',
        'legacyHerbPattern' => $herbpinyinpatterns === [] || $herblatinpatterns === []
            ? ''
            : '/(?<![A-Za-z0-9])(?<pinyin>' . implode('|', array_values($herbpinyinpatterns)) . ')'
                . '\s*[-\x{2013}\x{2014}]\s*[^\/\r\n]{1,80}?\s*\/\s*'
                . '(?<latin>' . implode('|', array_values($herblatinpatterns)) . ')(?![A-Za-z0-9])/iu',
        // Imported SCORM choices often use an obsolete common-name display,
        // for example "Xuan Shen - Scrophularia / Rx Scrophulariae".  Once
        // the leading Pinyin resolves unambiguously to Appendix A, replace the
        // complete choice with the controlling guideline display instead of
        // requiring the historical Latin abbreviation to match exactly.
        'legacyHerbOptionPattern' => $herbpinyinpatterns === []
            ? ''
            : '/^\s*(?<pinyin>' . implode('|', array_values($herbpinyinpatterns)) . ')'
                . '\s*[-\x{2013}\x{2014}]\s*[^\/\r\n]{1,120}\s*\/\s*'
                . '[^\r\n]{1,180}?\s*[.;]?\s*$/iu',
    ];
    return $data;
}

/**
 * Restore the official capitalization of every recognized Pinyin herb and
 * formula name without title-casing ordinary English or Spanish prose.
 */
function block_myprogress_herbology_canonicalize_pinyin_case_in_question(array $question): array {
    $question = block_myprogress_herbology_remove_pinyin_tones_from_question($question);
    $casedata = block_myprogress_herbology_official_pinyin_case_data();
    $canonicalnames = (array)($casedata['names'] ?? []);
    $pattern = (string)($casedata['pattern'] ?? '');
    $herbdisplays = (array)($casedata['herbDisplays'] ?? []);
    $formuladisplays = (array)($casedata['formulaDisplays'] ?? []);
    $bareherbpattern = (string)($casedata['bareHerbPattern'] ?? '');
    $bareformulapattern = (string)($casedata['bareFormulaPattern'] ?? '');
    $formulapattern = (string)($casedata['formulaPattern'] ?? '');
    $parentheticalformulapattern = (string)($casedata['parentheticalFormulaPattern'] ?? '');
    $parentheticalherbpattern = (string)($casedata['parentheticalHerbPattern'] ?? '');
    $commonnameherbpattern = (string)($casedata['commonNameHerbPattern'] ?? '');
    $legacyherbpattern = (string)($casedata['legacyHerbPattern'] ?? '');
    $legacyherboptionpattern = (string)($casedata['legacyHerbOptionPattern'] ?? '');
    if ($canonicalnames === [] || $pattern === '') {
        return $question;
    }
    $replace = static function($value) use (
        &$replace,
        $canonicalnames,
        $pattern,
        $herbdisplays,
        $formuladisplays,
        $bareherbpattern,
        $bareformulapattern,
        $formulapattern,
        $parentheticalformulapattern,
        $parentheticalherbpattern,
        $commonnameherbpattern,
        $legacyherbpattern,
        $legacyherboptionpattern
    ) {
        if (is_array($value)) {
            foreach ($value as $key => $nestedvalue) {
                $value[$key] = $replace($nestedvalue);
            }
            return $value;
        }
        if (!is_string($value) || $value === '') {
            return $value;
        }
        if ($parentheticalformulapattern !== '' && $formuladisplays !== []) {
            $formulareplaced = preg_replace_callback(
                $parentheticalformulapattern,
                static function(array $match) use ($formuladisplays): string {
                    $formula = strtolower((string)preg_replace(
                        '/\s+/u',
                        ' ',
                        trim((string)($match['formula'] ?? ''))
                    ));
                    $official = (array)($formuladisplays[$formula] ?? []);
                    if ($official === [] || trim((string)($official['english'] ?? '')) === '') {
                        return (string)$match[0];
                    }
                    return (string)$official['name'] . ' (' . (string)$official['english'] . ')';
                },
                $value
            );
            if (is_string($formulareplaced)) {
                $value = $formulareplaced;
            }
        }
        if ($bareformulapattern !== '' && $formuladisplays !== []) {
            $formulaexpanded = preg_replace_callback(
                $bareformulapattern,
                static function(array $match) use ($formuladisplays): string {
                    $formula = strtolower((string)preg_replace(
                        '/\s+/u',
                        ' ',
                        trim((string)($match['formula'] ?? ''))
                    ));
                    $official = (array)($formuladisplays[$formula] ?? []);
                    if ($official === [] || trim((string)($official['english'] ?? '')) === '') {
                        return (string)$match[0];
                    }
                    return (string)$official['name'] . ' (' . (string)$official['english'] . ')';
                },
                $value
            );
            if (is_string($formulaexpanded)) {
                $value = $formulaexpanded;
            }
        }
        if ($parentheticalherbpattern !== '' && $herbdisplays !== []) {
            $parentheticalreplaced = preg_replace_callback(
                $parentheticalherbpattern,
                static function(array $match) use ($herbdisplays): string {
                    $pinyin = strtolower((string)preg_replace(
                        '/\s+/u',
                        ' ',
                        trim((string)($match['pinyin'] ?? ''))
                    ));
                    $latin = strtolower((string)preg_replace(
                        '/\s+/u',
                        ' ',
                        trim((string)($match['latin'] ?? ''))
                    ));
                    $official = (array)($herbdisplays[$pinyin] ?? []);
                    if ($official === [] || !isset($official['latinAliases'][$latin])) {
                        return (string)$match[0];
                    }
                    return (string)$official['name'] . ' (' . (string)$official['latin'] . ')';
                },
                $value
            );
            if (is_string($parentheticalreplaced)) {
                $value = $parentheticalreplaced;
            }
        }
        if ($commonnameherbpattern !== '' && $herbdisplays !== []) {
            $commonnamereplaced = preg_replace_callback(
                $commonnameherbpattern,
                static function(array $match) use ($herbdisplays): string {
                    $pinyin = strtolower((string)preg_replace(
                        '/\s+/u',
                        ' ',
                        trim((string)($match['pinyin'] ?? ''))
                    ));
                    $official = (array)($herbdisplays[$pinyin] ?? []);
                    if ($official === []) {
                        return (string)$match[0];
                    }
                    return (string)$official['name'] . ' (' . (string)$official['latin'] . ')';
                },
                $value
            );
            if (is_string($commonnamereplaced)) {
                $value = $commonnamereplaced;
            }
        }
        if ($legacyherboptionpattern !== '' && $herbdisplays !== []
                && preg_match($legacyherboptionpattern, $value, $optionmatch) === 1) {
            $optionpinyin = strtolower((string)preg_replace(
                '/\s+/u',
                ' ',
                trim((string)($optionmatch['pinyin'] ?? ''))
            ));
            $optionofficial = (array)($herbdisplays[$optionpinyin] ?? []);
            if ($optionofficial !== []) {
                $value = (string)$optionofficial['name']
                    . ' (' . (string)$optionofficial['latin'] . ')';
            }
        }
        if ($legacyherbpattern !== '' && $herbdisplays !== []) {
            $legacyreplaced = preg_replace_callback(
                $legacyherbpattern,
                static function(array $match) use ($herbdisplays): string {
                    $pinyin = strtolower((string)preg_replace(
                        '/\s+/u',
                        ' ',
                        trim((string)($match['pinyin'] ?? ''))
                    ));
                    $latin = strtolower((string)preg_replace(
                        '/\s+/u',
                        ' ',
                        trim((string)($match['latin'] ?? ''))
                    ));
                    $official = (array)($herbdisplays[$pinyin] ?? []);
                    if ($official === [] || !isset($official['latinAliases'][$latin])) {
                        return (string)$match[0];
                    }
                    return (string)$official['name'] . ' (' . (string)$official['latin'] . ')';
                },
                $value
            );
            if (is_string($legacyreplaced)) {
                $value = $legacyreplaced;
            }
        }
        if ($bareherbpattern !== '' && $herbdisplays !== []) {
            // Protect complete formula names and herbs that already have a
            // parenthetical display. Otherwise a herb such as Chai Hu inside
            // Xiao Chai Hu Tang would be expanded and corrupt the formula.
            $protectedsegments = [];
            $protectsegment = static function(array $match) use (&$protectedsegments): string {
                $placeholder = "\x1AHP" . count($protectedsegments) . "\x1A";
                $protectedsegments[$placeholder] = (string)$match[0];
                return $placeholder;
            };
            if ($formulapattern !== '') {
                $protected = preg_replace_callback($formulapattern, $protectsegment, $value);
                if (is_string($protected)) {
                    $value = $protected;
                }
            }
            if ($commonnameherbpattern !== '') {
                $protected = preg_replace_callback($commonnameherbpattern, $protectsegment, $value);
                if (is_string($protected)) {
                    $value = $protected;
                }
            }
            $expanded = preg_replace_callback(
                $bareherbpattern,
                static function(array $match) use ($herbdisplays): string {
                    $pinyin = strtolower((string)preg_replace(
                        '/\s+/u',
                        ' ',
                        trim((string)($match['pinyin'] ?? ''))
                    ));
                    $official = (array)($herbdisplays[$pinyin] ?? []);
                    if ($official === []) {
                        return (string)$match[0];
                    }
                    return (string)$official['name'] . ' (' . (string)$official['latin'] . ')';
                },
                $value
            );
            if (is_string($expanded)) {
                $value = $expanded;
            }
            if ($protectedsegments !== []) {
                $value = strtr($value, $protectedsegments);
            }
        }
        $replaced = preg_replace_callback($pattern, static function(array $match) use ($canonicalnames): string {
            $lookupname = strtolower((string)preg_replace('/\s+/u', ' ', trim((string)$match[0])));
            return (string)($canonicalnames[$lookupname] ?? $match[0]);
        }, $value);
        return is_string($replaced) ? $replaced : $value;
    };
    foreach (['prompt', 'options', 'explanation', 'reviewSummary', 'source', 'nomenclature', 'translations',
        'sourceCard'] as $field) {
        if (array_key_exists($field, $question)) {
            $question[$field] = $replace($question[$field]);
        }
    }
    $expandbareherboptions = static function(array $options) use ($herbdisplays): array {
        foreach ($options as &$option) {
            if (!is_array($option)) {
                continue;
            }
            $optiontext = trim((string)($option['text'] ?? ''));
            foreach ($herbdisplays as $official) {
                $officialname = trim((string)($official['name'] ?? ''));
                $officiallatin = trim((string)($official['latin'] ?? ''));
                if ($officialname === '' || $officiallatin === '') {
                    continue;
                }
                $namepattern = implode('\\s+', array_map(
                    static fn(string $word): string => preg_quote($word, '/'),
                    preg_split('/\\s+/u', $officialname, -1, PREG_SPLIT_NO_EMPTY) ?: []
                ));
                $latinpattern = implode('\\s+', array_map(
                    static fn(string $word): string => preg_quote($word, '/'),
                    preg_split('/\\s+/u', $officiallatin, -1, PREG_SPLIT_NO_EMPTY) ?: []
                ));
                if (preg_match(
                    '/^\\s*' . $namepattern . '\\s*\\(\\s*' . $latinpattern
                        . '\\s*\\)\\s*[-\\x{2013}\\x{2014}]\\s*.+?\\/\\s*(?:Rx\\.?\\s*)?.+$/iu',
                    $optiontext
                ) === 1) {
                    // Imported choices sometimes append a prose definition
                    // and a second legacy pharmaceutical name after an exact
                    // Pinyin (Latin) identity. Keep only the official display;
                    // the prose belongs in the explanation, not the choice.
                    $optiontext = $officialname . ' (' . $officiallatin . ')';
                    $option['text'] = $optiontext;
                    break;
                }
            }
            if ($optiontext === '' || str_contains($optiontext, '(')) {
                continue;
            }
            $optionlookup = strtolower((string)preg_replace('/\s+/u', ' ', $optiontext));
            $official = (array)($herbdisplays[$optionlookup] ?? []);
            if ($official === []) {
                continue;
            }
            $option['text'] = (string)$official['name'] . ' (' . (string)$official['latin'] . ')';
        }
        unset($option);
        return $options;
    };
    if (is_array($question['options'] ?? null)) {
        $question['options'] = $expandbareherboptions($question['options']);
    }
    if (is_array($question['translations']['es']['options'] ?? null)) {
        $question['translations']['es']['options'] = $expandbareherboptions(
            $question['translations']['es']['options']
        );
    }
    foreach ([['nomenclature', 'herbs'], ['nomenclature', 'feedback', 'herbs']] as $herbpath) {
        $herbrecords = &$question;
        foreach ($herbpath as $herbpathkey) {
            if (!is_array($herbrecords[$herbpathkey] ?? null)) {
                unset($herbrecords);
                continue 2;
            }
            $herbrecords = &$herbrecords[$herbpathkey];
        }
        foreach ($herbrecords as &$herbrecord) {
            if (!is_array($herbrecord)) {
                continue;
            }
            $recordpinyin = trim((string)($herbrecord['pinyin'] ?? ''));
            if (preg_match('/^(.+?)\s*\([^()]+\)$/u', $recordpinyin, $displaymatch) === 1) {
                $recordpinyin = trim((string)$displaymatch[1]);
            }
            $lookuppinyin = strtolower((string)preg_replace('/\s+/u', ' ', $recordpinyin));
            $official = (array)($herbdisplays[$lookuppinyin] ?? []);
            if ($official === []) {
                continue;
            }
            $herbrecord['pinyin'] = (string)$official['name'];
            $herbrecord['pharmaceuticalName'] = (string)$official['latin'];
        }
        unset($herbrecord, $herbrecords);
    }
    foreach ([['nomenclature', 'formulas'], ['nomenclature', 'feedback', 'formulas']] as $formulapath) {
        $formularecords = &$question;
        foreach ($formulapath as $formulapathkey) {
            if (!is_array($formularecords[$formulapathkey] ?? null)) {
                unset($formularecords);
                continue 2;
            }
            $formularecords = &$formularecords[$formulapathkey];
        }
        foreach ($formularecords as &$formularecord) {
            if (!is_array($formularecord)) {
                continue;
            }
            $recordpinyin = trim((string)($formularecord['pinyin'] ?? ''));
            if (preg_match('/^(.+?)\s*\([^()]+\)$/u', $recordpinyin, $displaymatch) === 1) {
                $recordpinyin = trim((string)$displaymatch[1]);
            }
            $lookupformula = strtolower((string)preg_replace('/\s+/u', ' ', $recordpinyin));
            $official = (array)($formuladisplays[$lookupformula] ?? []);
            if ($official === []) {
                continue;
            }
            // Structured nomenclature stores the identity and display name in
            // separate fields. Keep parentheses only in visible prose so the
            // guideline validator does not read "Pinyin (English)" as a new
            // formula name and append the English name a second time.
            $formularecord['pinyin'] = (string)$official['name'];
            $formularecord['englishName'] = (string)$official['english'];
        }
        unset($formularecord, $formularecords);
    }
    return $question;
}

function block_myprogress_herbology_remove_pinyin_tones_from_domains(array $domains): array {
    foreach ($domains as &$questions) {
        if (!is_array($questions)) {
            continue;
        }
        foreach ($questions as &$question) {
            if (is_array($question)) {
                $question = block_myprogress_herbology_canonicalize_pinyin_case_in_question($question);
            }
        }
        unset($question);
    }
    unset($questions);
    return $domains;
}

function block_myprogress_herbology_override_load_unlocked(string $path): array {
    if (!is_readable($path)) {
        return block_myprogress_herbology_override_empty();
    }
    $decoded = json_decode((string)file_get_contents($path), true);
    if (!is_array($decoded) || !is_array($decoded['questions'] ?? null)) {
        return block_myprogress_herbology_override_empty();
    }
    return $decoded + block_myprogress_herbology_override_empty();
}

function block_myprogress_herbology_override_load(): array {
    return block_myprogress_herbology_override_load_unlocked(
        block_myprogress_herbology_override_path()
    );
}

function block_myprogress_herbology_override_update(callable $mutator): array {
    $path = block_myprogress_herbology_override_path();
    $directory = dirname($path);
    make_writable_directory($directory);
    $lockpath = $path . '.lock';
    $lock = fopen($lockpath, 'c+');
    if ($lock === false || !flock($lock, LOCK_EX)) {
        if (is_resource($lock)) {
            fclose($lock);
        }
        throw new RuntimeException('Unable to lock the Herbology question revision store.');
    }
    try {
        $store = block_myprogress_herbology_override_load_unlocked($path);
        $updated = $mutator($store);
        if (!is_array($updated)
                || !is_array($updated['questions'] ?? null)
                || !is_array($updated['customQuestions'] ?? null)) {
            throw new RuntimeException('The Herbology question revision store is invalid.');
        }
        $updated['schemaVersion'] = 1;
        $updated['updatedAt'] = gmdate(DATE_ATOM);
        $encoded = json_encode(
            $updated,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
        ) . PHP_EOL;
        $temporary = tempnam($directory, '.herbology-question-overrides-');
        if ($temporary === false || file_put_contents($temporary, $encoded, LOCK_EX) === false) {
            throw new RuntimeException('Unable to write the Herbology question revision store.');
        }
        @chmod($temporary, 0660);
        if (!rename($temporary, $path)) {
            @unlink($temporary);
            throw new RuntimeException('Unable to publish the Herbology question revision store.');
        }
        return $updated;
    } finally {
        flock($lock, LOCK_UN);
        fclose($lock);
    }
}

/**
 * Merge administrator-authored questions into the domain bank.
 *
 * Drafts are included only on administrator pages. Student-facing pages receive
 * approved custom questions exclusively.
 */
function block_myprogress_herbology_merge_custom_questions(
    array $domains,
    array $store,
    bool $includepending = false
): array {
    foreach (($store['customQuestions'] ?? []) as $questionid => $record) {
        if (!is_array($record)) {
            continue;
        }
        $status = (string)($record['status'] ?? 'draft');
        $approved = is_array($record['approved'] ?? null) ? $record['approved'] : null;
        $isexcluded = in_array(
            $status,
            ['exclusion_candidate', 'study_archive', 'extra_guideline', 'deleted'],
            true
        );
        if ($isexcluded && !$includepending) {
            // Mock-only, Extra guideline and recycled administrator questions
            // must never leak into student-facing practice or Simulation Exams
            // merely because they retain an approved snapshot for recovery.
            continue;
        }
        if ($status !== 'approved' && !$includepending && $approved === null) {
            continue;
        }
        $question = $status === 'approved'
            ? ($approved ?? ($record['draft'] ?? null))
            : ($includepending ? ($record['draft'] ?? null) : $approved);
        if (!is_array($question)) {
            continue;
        }
        $domainid = (string)($record['domainId'] ?? $question['domainId'] ?? '');
        if ($domainid === '') {
            continue;
        }
        if (!isset($domains[$domainid]) || !is_array($domains[$domainid])) {
            $domains[$domainid] = [];
        }
        $question['questionId'] = (string)($question['questionId'] ?? $questionid);
        $question['domainId'] = $domainid;
        $question['sourceKind'] = 'admin';
        $question['_customStatus'] = $status;
        if ($isexcluded) {
            $question['_excludedFromUse'] = true;
            $question['_editorialRevision'] = [
                'status' => $status,
                'requestReclassification' => false,
                'exclusionReason' => (string)($record['exclusionReason'] ?? ''),
                'savedAt' => (string)($record['savedAt'] ?? ''),
                'savedByName' => (string)($record['savedByName'] ?? ''),
                'revisionNumber' => (int)($record['revisionNumber'] ?? 1),
                'archiveModule' => (string)($record['archiveModule'] ?? ''),
                'mockCategory' => (string)($record['mockCategory'] ?? ''),
                'mockPlacementStatus' => (string)($record['mockPlacementStatus'] ?? ''),
                'extraGuidelineDomain' => (string)($record['extraGuidelineDomain'] ?? ''),
            ];
        }
        $domains[$domainid] = array_values(array_filter(
            $domains[$domainid],
            static fn(array $existing): bool => (string)($existing['questionId'] ?? '') !== $question['questionId']
        ));
        $domains[$domainid][] = $question;
    }
    return $domains;
}

function block_myprogress_herbology_apply_revision(
    array $question,
    ?array $revision,
    bool $includepending = false
): array {
    if (!$revision) {
        return $question;
    }
    $status = (string)($revision['status'] ?? 'draft');
    $isexcluded = in_array(
        $status,
        ['exclusion_candidate', 'study_archive', 'extra_guideline', 'deleted'],
        true
    );
    $hasbankplacement = (int)($revision['bankPosition'] ?? 0) > 0;
    $draft = null;
    if ($includepending && is_array($revision['draft'] ?? null)) {
        $draft = $revision['draft'];
    } else if (is_array($revision['approved'] ?? null)) {
        $draft = $revision['approved'];
    } else if ($status === 'approved' && is_array($revision['draft'] ?? null)) {
        // Backward compatibility with revisions saved before the approved snapshot was introduced.
        $draft = $revision['draft'];
    } else if (!$isexcluded && !$hasbankplacement) {
        return $question;
    }
    if (is_array($draft)) {
        foreach ([
            'prompt', 'options', 'correctLabel', 'explanation', 'translations',
            'contentType', 'cognitiveLevel', 'difficultyBand', 'source', 'sourceCard',
        ] as $field) {
            if (array_key_exists($field, $draft)) {
                $question[$field] = $draft[$field];
            }
        }
    }
    $question['_excludedFromUse'] = $isexcluded;
    $question['_editorialRevision'] = [
        'status' => $status,
        'requestReclassification' => !empty($revision['requestReclassification']),
        'exclusionReason' => (string)($revision['exclusionReason'] ?? ''),
        'savedAt' => (string)($revision['savedAt'] ?? ''),
        'savedByName' => (string)($revision['savedByName'] ?? ''),
        'revisionNumber' => (int)($revision['revisionNumber'] ?? 1),
        'aiBatchId' => (string)($revision['aiBatchId'] ?? ''),
        'aiPromptVersion' => (string)($revision['aiPromptVersion'] ?? ''),
        'archiveModule' => (string)($revision['archiveModule'] ?? ''),
        'mockCategory' => (string)($revision['mockCategory'] ?? ''),
        'mockPlacementStatus' => (string)($revision['mockPlacementStatus'] ?? ''),
        'extraGuidelineDomain' => (string)($revision['extraGuidelineDomain'] ?? ''),
        'bankPosition' => (int)($revision['bankPosition'] ?? 0),
        'bankMovedAt' => (string)($revision['bankMovedAt'] ?? ''),
        'bankMovedByName' => (string)($revision['bankMovedByName'] ?? ''),
        'bankDeferred' => !empty($revision['bankDeferred']),
    ];
    return $question;
}

function block_myprogress_herbology_apply_revisions_to_domains(
    array $domains,
    array $store,
    bool $includepending = false
): array {
    $revisions = $store['questions'] ?? [];
    foreach ($domains as &$questions) {
        if (!is_array($questions)) {
            continue;
        }
        foreach ($questions as &$question) {
            $questionid = (string)($question['questionId'] ?? '');
            if ($questionid !== '' && isset($revisions[$questionid])) {
                $question = block_myprogress_herbology_apply_revision(
                    $question,
                    $revisions[$questionid],
                    $includepending
                );
            }
        }
        unset($question);
    }
    unset($questions);
    return $domains;
}
