<?php
// Direct, verified repair of the two Exam 5 administrator-authored questions
// whose saved revisions still contained invented formula names.

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "This repair must be run from the command line.\n");
    exit(1);
}

$moodleroot = rtrim((string)($argv[1] ?? ''), '/');
if ($moodleroot === '' || !is_readable($moodleroot . '/config.php')) {
    fwrite(STDERR, "Usage: php repair_exam5_guideline_state.php /path/to/moodle\n");
    exit(1);
}

define('CLI_SCRIPT', true);
require $moodleroot . '/config.php';
require_once $moodleroot . '/blocks/myprogress/herbology-question-overrides.php';

$repairs = [
    'ADMIN-HB-20261121-175513-4D36' => [
        'Mu Li Liu Wei Di Huang Wan' => 'Liu Wei Di Huang Wan with Mu Li added',
        'Ju Hua Liu Wei Di Huang Wan' => 'Liu Wei Di Huang Wan with Ju Hua added',
        'Huang Bai Si Wu Tang' => 'Si Wu Tang with Huang Bai added',
        'Hong Hua Qi Ju Di Huang Wan' => 'Qi Ju Di Huang Wan with Hong Hua added',
    ],
    'ADMIN-HB-20261121-180348-75A9' => [
        'Mu Li Zhi Bai Di Huang Wan' => 'Zhi Bai Di Huang Wan with Mu Li added',
    ],
];
$repairversion = 'exam5-direct-state-formula-repair-v1';
$normalizename = static function(string $name): string {
    $tonemap = [
        'ā'=>'a','á'=>'a','ǎ'=>'a','à'=>'a','ē'=>'e','é'=>'e','ě'=>'e','è'=>'e',
        'ī'=>'i','í'=>'i','ǐ'=>'i','ì'=>'i','ō'=>'o','ó'=>'o','ǒ'=>'o','ò'=>'o',
        'ū'=>'u','ú'=>'u','ǔ'=>'u','ù'=>'u','ǖ'=>'ü','ǘ'=>'ü','ǚ'=>'ü','ǜ'=>'ü',
        'Ā'=>'A','Á'=>'A','Ǎ'=>'A','À'=>'A','Ē'=>'E','É'=>'E','Ě'=>'E','È'=>'E',
        'Ī'=>'I','Í'=>'I','Ǐ'=>'I','Ì'=>'I','Ō'=>'O','Ó'=>'O','Ǒ'=>'O','Ò'=>'O',
        'Ū'=>'U','Ú'=>'U','Ǔ'=>'U','Ù'=>'U','Ǖ'=>'Ü','Ǘ'=>'Ü','Ǚ'=>'Ü','Ǜ'=>'Ü',
    ];
    $name = strtolower(strtr(trim($name), $tonemap));
    $name = preg_replace('/[^a-z0-9]+/', ' ', $name);
    return trim(is_string($name) ? $name : '');
};
$repairvalue = static function($value, array $replacements) use (&$repairvalue) {
    if (is_array($value)) {
        foreach ($value as $key => $nestedvalue) {
            $value[$key] = $repairvalue($nestedvalue, $replacements);
        }
        return $value;
    }
    return is_string($value)
        ? str_ireplace(array_keys($replacements), array_values($replacements), $value)
        : $value;
};
$removenomenclature = static function(array $snapshot, array $invalidlookup) use (
    $normalizename
): array {
    foreach ([['nomenclature', 'formulas'], ['nomenclature', 'feedback', 'formulas']]
            as $formulapath) {
        $formularecords = &$snapshot;
        foreach ($formulapath as $formulapathkey) {
            if (!is_array($formularecords[$formulapathkey] ?? null)) {
                unset($formularecords);
                continue 2;
            }
            $formularecords = &$formularecords[$formulapathkey];
        }
        $formularecords = array_values(array_filter(
            $formularecords,
            static fn($record): bool => !is_array($record)
                || !isset($invalidlookup[$normalizename((string)($record['pinyin'] ?? ''))])
        ));
        unset($formularecords);
    }
    return $snapshot;
};

$changedrecords = 0;
$restoredprojects = 0;
$foundrecords = [];
block_myprogress_herbology_override_update(static function(array $store) use (
    $repairs,
    $repairversion,
    $normalizename,
    $repairvalue,
    $removenomenclature,
    &$changedrecords,
    &$restoredprojects,
    &$foundrecords
): array {
    $savedat = gmdate(DATE_ATOM);
    foreach ($repairs as $questionid => $replacements) {
        $invalidlookup = array_fill_keys(array_map(
            $normalizename,
            array_merge(array_keys($replacements), array_values($replacements))
        ), true);
        foreach (['customQuestions', 'questions'] as $collectionkey) {
            $record = $store[$collectionkey][$questionid] ?? null;
            if (!is_array($record)) {
                continue;
            }
            $foundrecords[$questionid . ':' . $collectionkey] = true;
            $before = json_encode($record, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            foreach (['original', 'draft', 'approved'] as $snapshotkey) {
                if (!is_array($record[$snapshotkey] ?? null)) {
                    continue;
                }
                $snapshot = block_myprogress_herbology_canonicalize_pinyin_case_in_question(
                    $record[$snapshotkey]
                );
                $snapshot = $removenomenclature($snapshot, $invalidlookup);
                $record[$snapshotkey] = $repairvalue($snapshot, $replacements);
            }
            $record['idea'] = $repairvalue((string)($record['idea'] ?? ''), $replacements);
            $record['reviewSummary'] = $repairvalue(
                (string)($record['reviewSummary'] ?? ''),
                $replacements
            );
            if ($collectionkey === 'customQuestions') {
                // Force the installed page to run its current Appendix A/C
                // validator over the repaired snapshot on the next request.
                unset($record['guidelineValidation']);
            }
            $record['directFormulaStateRepairVersion'] = $repairversion;
            $record['directFormulaStateRepairedAt'] = $savedat;
            $after = json_encode($record, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($before !== $after) {
                $record['savedAt'] = $savedat;
                $record['savedByName'] = 'Verified direct Exam 5 state repair';
                $record['revisionNumber'] = (int)($record['revisionNumber'] ?? 0) + 1;
                $changedrecords++;
            }
            $store[$collectionkey][$questionid] = $record;
        }

        $customrecord = $store['customQuestions'][$questionid] ?? null;
        $targetprojectid = is_array($customrecord)
            ? (string)($customrecord['targetExamProjectId'] ?? '')
            : '';
        if ($targetprojectid === '' || !is_array($store['examProjects'][$targetprojectid] ?? null)) {
            foreach ((array)($store['examProjects'] ?? []) as $projectid => $project) {
                if (is_array($project) && in_array(
                    $questionid,
                    array_map('strval', (array)($project['excludedQuestionIds'] ?? [])),
                    true
                )) {
                    $targetprojectid = (string)$projectid;
                    break;
                }
            }
        }
        if ($targetprojectid === ''
                || !is_array($store['examProjects'][$targetprojectid] ?? null)
                || (string)($store['examProjects'][$targetprojectid]['status'] ?? 'draft')
                    === 'published') {
            continue;
        }
        $project = $store['examProjects'][$targetprojectid];
        $questionids = array_values(array_unique(array_filter(array_map(
            'strval',
            (array)($project['questionIds'] ?? [])
        ))));
        $targetcount = max(1, (int)($project['targetCount'] ?? 100));
        if (!in_array($questionid, $questionids, true) && count($questionids) < $targetcount) {
            $questionids[] = $questionid;
        }
        $project['questionIds'] = $questionids;
        $project['excludedQuestionIds'] = array_values(array_filter(
            array_map('strval', (array)($project['excludedQuestionIds'] ?? [])),
            static fn(string $excludedid): bool => $excludedid !== $questionid
        ));
        if (in_array($questionid, $questionids, true)) {
            $project['confirmedQuestionIds'] = array_values(array_unique(array_merge(
                array_map('strval', (array)($project['confirmedQuestionIds'] ?? [])),
                [$questionid]
            )));
        }
        $project['currentCount'] = count($questionids);
        $project['missingCount'] = max(0, $targetcount - count($questionids));
        $project['directFormulaStateRepairedAt'] = $savedat;
        $project['updatedAt'] = $savedat;
        $store['examProjects'][$targetprojectid] = $project;
        $restoredprojects++;
    }
    return $store;
});

$verifiedstore = block_myprogress_herbology_override_load();
$remaining = [];
foreach ($repairs as $questionid => $replacements) {
    $questionfound = false;
    foreach (['customQuestions', 'questions'] as $collectionkey) {
        $record = $verifiedstore[$collectionkey][$questionid] ?? null;
        if (!is_array($record)) {
            continue;
        }
        $questionfound = true;
        foreach (['original', 'draft', 'approved'] as $snapshotkey) {
            if (!is_array($record[$snapshotkey] ?? null)) {
                continue;
            }
            $text = json_encode(
                $record[$snapshotkey],
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );
            foreach (array_keys($replacements) as $invalidname) {
                if (stripos((string)$text, $invalidname) !== false) {
                    $remaining[] = $questionid . ':' . $collectionkey . ':' . $snapshotkey
                        . ':' . $invalidname;
                }
            }
        }
    }
    if (!$questionfound) {
        $remaining[] = $questionid . ':missing-from-state';
    }
}
if ($remaining !== []) {
    throw new RuntimeException(
        'Exam 5 state repair verification failed: ' . implode(' | ', $remaining)
    );
}

echo 'Exam 5 state verified: ' . count($foundrecords) . ' stored records inspected, '
    . $changedrecords . ' repaired, ' . $restoredprojects . ' project links checked.';

