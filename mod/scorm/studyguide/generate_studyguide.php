<?php

define('CLI_SCRIPT', true);

require_once(__DIR__ . '/../../../config.php');

$scormid = 610;

$sourcefile = __DIR__ . '/scorm_questions_' . $scormid . '.txt';
$outputfile = __DIR__ . '/generated_studyguide_' . $scormid . '_test.txt';

if (!file_exists($sourcefile)) {
    fwrite(STDERR, "ERROR: SCORM source file not found.\n");
    exit(1);
}

$source = file_get_contents($sourcefile);

if ($source === false || trim($source) === '') {
    fwrite(STDERR, "ERROR: SCORM source is empty.\n");
    exit(1);
}

$questionblocks = preg_split('/(?=^QUESTION [0-9]+$)/m', trim($source));
$questionblocks = array_values(array_filter(array_map(function($block) { return trim(preg_replace('/^------------------------------$/m', '', $block)); }, $questionblocks), function($block) { return preg_match('/^QUESTION [0-9]+$/m', $block); }));

$guidesections = [];

foreach ($questionblocks as $questionblock) {

    $questiontext = '';
    $answers = '';
    $rightanswer = '';
    $feedback = '';

    if (preg_match('/^QUESTION TEXT:\s*(.*?)(?=^ANSWERS:\s*$)/ms', $questionblock, $m)) {
        $questiontext = trim($m[1]);
    }

    if (preg_match('/^ANSWERS:\s*(.*?)(?=^RIGHT ANSWER:\s*$)/ms', $questionblock, $m)) {
        $answers = trim($m[1]);
    }

    if (preg_match('/^RIGHT ANSWER:\s*(.*?)(?=^FEEDBACK:\s*$)/ms', $questionblock, $m)) {
        $rightanswer = trim($m[1]);
    }

    if (preg_match('/^FEEDBACK:\s*(.*)$/ms', $questionblock, $m)) {
        $feedback = trim($m[1]);
    }

    $topic = 'Not explicitly stated in source.';
    $clinicalpresentation = 'Not explicitly stated in source.';
    $differentiation = 'Not explicitly stated in source.';
    $prescription = 'Not explicitly stated in source.';

    $questionlines = preg_split('/\R/', $questiontext);

    if (!empty($questionlines)) {
        $topic = trim(array_shift($questionlines));

        $diffindex = null;
        $prescriptionindex = null;
        $questionindex = null;

        foreach ($questionlines as $i => $line) {
            $line = trim($line);

            if (strcasecmp($line, 'Differentiation') === 0) {
                $diffindex = $i;
            }

            if (stripos($line, 'What is the acupuncture prescription') === 0) {
                $prescriptionindex = $i;
            }

            if (stripos($line, 'What is ') === 0) {
                $questionindex = $i;
            }
        }

        if ($diffindex !== null) {
            $clinicallines = array_slice($questionlines, 0, $diffindex);
            $clinicallines = array_values(array_filter(array_map('trim', $clinicallines)));

            if (!empty($clinicallines)) {
                $clinicalpresentation = implode("\n", $clinicallines);
            }

            $diffstart = $diffindex + 1;
            $diffend = $questionindex !== null ? $questionindex : count($questionlines);

            $difflines = array_slice($questionlines, $diffstart, $diffend - $diffstart);
            $difflines = array_values(array_filter(array_map('trim', $difflines), function($line) {
                return strcasecmp($line, 'Differentiation.') !== 0;
            }));

            if (!empty($difflines)) {
                $differentiation = implode("\n", $difflines);
            }

            $differentiation = trim(preg_replace("/\n{2,}/", "\n", $differentiation));
        } else {
            $clinicallines = array_values(array_filter(array_map('trim', $questionlines)));

            if (!empty($clinicallines)) {
                $clinicalpresentation = implode("\n", $clinicallines);
            }
        }

        if ($prescriptionindex !== null) {
            $prescriptiontext = trim($questionlines[$prescriptionindex]);

            if (stripos($prescriptiontext, "What is the acupuncture prescription") !== 0) {
                $prescription = $prescriptiontext;
            }
        }
    }

    if (preg_match('/^QUESTION ([0-9]+)$/m', $questionblock, $m)) {
        $questionnumber = $m[1];
    } else {
        $questionnumber = '?';
    }

    $section = "QUESTION {$questionnumber}\n";
    $section .= "Topic:\n{$topic}\n\n";
    $section .= "Clinical Presentation:\n{$clinicalpresentation}\n\n";
    $section .= "Differentiation:\n{$differentiation}\n\n";
    $section .= "Acupuncture Prescription:\n{$prescription}\n\n";
    $section .= "Answers:\n{$answers}\n\n";
    $section .= "Right Answer:\n{$rightanswer}\n\n";
    $section .= "Feedback:\n{$feedback}";

    $guidesections[] = trim($section);
}

$guide = implode("\n------------------------------\n", $guidesections);

if (trim($guide) === '') {
    fwrite(STDERR, "ERROR: AI returned empty Study Guide.\n");
    exit(1);
}

if (file_put_contents($outputfile, $guide) === false) {
    fwrite(STDERR, "ERROR: Could not save generated Study Guide.\n");
    exit(1);
}

echo "SUCCESS: YES\n";
echo "SCORM ID: {$scormid}\n";
echo "OUTPUT: {$outputfile}\n";
echo "BYTES: " . strlen($guide) . "\n";
