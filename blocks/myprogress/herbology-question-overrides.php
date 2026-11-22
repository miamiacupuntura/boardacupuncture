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
    ];
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
 * Return the official Appendix A/C Pinyin display names and one reusable
 * case-insensitive matcher. The matcher is built once per request because it
 * is applied to thousands of questions in the administrator bank.
 */
function block_myprogress_herbology_official_pinyin_case_data(): array {
    global $CFG;

    static $data = null;
    if (is_array($data)) {
        return $data;
    }
    $canonicalnames = [];
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
        }
    }
    if ($canonicalnames === []) {
        $data = ['names' => [], 'pattern' => ''];
        return $data;
    }
    $lookupnames = array_keys($canonicalnames);
    usort($lookupnames, static fn(string $left, string $right): int => strlen($right) <=> strlen($left));
    $alternatives = array_map(static function(string $lookupname): string {
        return implode('\\s+', array_map(
            static fn(string $word): string => preg_quote($word, '/'),
            preg_split('/\s+/u', $lookupname, -1, PREG_SPLIT_NO_EMPTY)
        ));
    }, $lookupnames);
    $data = [
        'names' => $canonicalnames,
        'pattern' => '/(?<![A-Za-z0-9])(?:' . implode('|', $alternatives) . ')(?![A-Za-z0-9])/iu',
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
    if ($canonicalnames === [] || $pattern === '') {
        return $question;
    }
    $replace = static function($value) use (&$replace, $canonicalnames, $pattern) {
        if (is_array($value)) {
            foreach ($value as $key => $nestedvalue) {
                $value[$key] = $replace($nestedvalue);
            }
            return $value;
        }
        if (!is_string($value) || $value === '') {
            return $value;
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
    $isexcluded = in_array($status, ['exclusion_candidate', 'study_archive', 'deleted'], true);
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
