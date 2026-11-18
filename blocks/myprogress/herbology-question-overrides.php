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
        'examProjects' => [],
    ];
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
    $isexcluded = in_array($status, ['exclusion_candidate', 'deleted'], true);
    $draft = null;
    if ($includepending && is_array($revision['draft'] ?? null)) {
        $draft = $revision['draft'];
    } else if (is_array($revision['approved'] ?? null)) {
        $draft = $revision['approved'];
    } else if ($status === 'approved' && is_array($revision['draft'] ?? null)) {
        // Backward compatibility with revisions saved before the approved snapshot was introduced.
        $draft = $revision['draft'];
    } else if (!$isexcluded) {
        return $question;
    }
    if (is_array($draft)) {
        foreach ([
            'prompt', 'options', 'correctLabel', 'explanation', 'translations',
            'contentType', 'cognitiveLevel', 'difficultyBand',
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
