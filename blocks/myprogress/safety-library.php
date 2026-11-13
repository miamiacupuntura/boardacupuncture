<?php
// This file is part of Moodle - http://moodle.org/.

require_once(__DIR__ . '/../../config.php');

require_login();

$displaylang = optional_param('lang', 'en', PARAM_ALPHA);
$displaylang = $displaylang === 'es' ? 'es' : 'en';
$ises = $displaylang === 'es';

$filename = $ises ? 'safety-library-es.html' : 'safety-library.html';
$source = $CFG->dirroot . '/study-guide/comparisons/' . $filename;

if (!is_readable($source)) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Safety Library source file is unavailable.';
    exit;
}

$html = file_get_contents($source);
if ($html === false) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Safety Library could not be loaded.';
    exit;
}

$returnurl = '/blocks/myprogress/study-guide.php?area=herbology&amp;view=library';
if ($ises) {
    $returnurl .= '&amp;lang=es';
}
$returnlabel = $ises ? '← Guía de estudio' : '← Study Guide';
$languageurl = $ises
    ? '/blocks/myprogress/safety-library.php'
    : '/blocks/myprogress/safety-library.php?lang=es';
$languagelabel = $ises ? 'English' : 'Español';

$html = str_replace('href="/my/"', 'href="' . $returnurl . '"', $html);
$html = preg_replace(
    '#<a class="comparison-brand" href="[^"]*">#',
    '<a class="comparison-brand" href="' . $returnurl . '">',
    $html,
    1
);
$html = preg_replace(
    '#<a class="comparison-back" href="[^"]*">(?:← Back to Dashboard|← Dashboard|← Board Level Activities|← Actividades Board Level|← Study Guide|← Guía de estudio)</a>#u',
    '<a class="comparison-back" href="' . $returnurl . '">' . $returnlabel . '</a>',
    $html,
    1
);
$html = preg_replace(
    '#<a class="comparison-back" href="[^"]*">(?:Español|English)</a>#u',
    '<a class="comparison-back" href="' . $languageurl . '">' . $languagelabel . '</a>',
    $html,
    1
);
$html = preg_replace(
    '#comparisons\\.js\\?v=[^"]+#',
    'comparisons.js?v=20261113-3',
    $html
);
$html = str_replace('<head>', '<head><base href="/study-guide/comparisons/">', $html);

header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

echo $html;
