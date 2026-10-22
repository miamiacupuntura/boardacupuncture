<?php
// Configure the all-Boards membership course as the parent of every Board resource course.

define('CLI_SCRIPT', true);

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/clilib.php');

[$options] = cli_get_params(
    [
        'help' => false,
        'apply' => false,
    ],
    [
        'h' => 'help',
        'a' => 'apply',
    ]
);

if ($options['help']) {
    echo "Configure one 30-day membership course as the parent of all Board resource courses.\n\n";
    echo "Options:\n";
    echo "  --apply, -a    Create missing course meta enrolment links.\n";
    echo "  --help, -h     Show this help.\n";
    exit(0);
}

$parentcourseid = 116;
$parentcourse = $DB->get_record('course', ['id' => $parentcourseid], 'id,fullname', MUST_EXIST);
$metaplugin = enrol_get_plugin('meta');
if (!$metaplugin || !enrol_is_enabled('meta')) {
    cli_error('The Course meta link enrolment plugin must be enabled first.');
}

$targets = [
    ['names' => ['Foundation Exams', 'Foundations Exams'], 'fallbackid' => 30],
    ['names' => ['Foundation Simulation Exams', 'Foundations Simulation Exams']],
    ['names' => ['Playing Foundation Module Exams', 'Playing Foundations Module Exams', 'Playing Foundation']],
    ['names' => ['Foundation Notes', 'Foundations Notes']],
    ['names' => ['Point Location and Point Indication Exams'], 'fallbackid' => 34],
    ['names' => ['Point Location and Point Indication Simulation Exams', 'Point Location Simulation']],
    ['names' => ['Playing Point Location Module Exams']],
    ['names' => ['Point Location Notes']],
    ['names' => ['Single Herbs Exams', 'Single Herbs'], 'fallbackid' => 45],
    ['names' => ['Formulas Exams', 'Herbal Formulas'], 'fallbackid' => 117],
    ['names' => ['Herbal Medicine Simulation Exams'], 'fallbackid' => 41],
    ['names' => ['Playing Herbology Module Exams']],
    ['names' => ['Herbology Notes'], 'fallbackid' => 92],
    ['names' => ['Biomedicine Exams'], 'fallbackid' => 36],
    ['names' => ['Biomedicine Simulation Exams', 'Biomedicine Simulation']],
    ['names' => ['Playing Biomedicine Module Exams']],
    ['names' => ['Biomedicine Notes']],
    ['names' => ['Clean Needle Techniques Exams', 'Clean Needle Techniques'], 'fallbackid' => 113],
    ['names' => [
        'Clean Needle Simulation',
        'Clean Needle Simulation Exams',
        'Clean Needle Techniques Simulation',
    ]],
    ['names' => ['Playing Clean Needle Techniques Module Exams', 'Playing Clean Needle']],
    ['names' => ['Clean Needle Techniques Notes', 'Clean Needle Notes']],
];

$resolvedcourses = [];
$missing = [];
foreach ($targets as $target) {
    $course = null;
    foreach ($target['names'] as $fullname) {
        $course = $DB->get_record('course', ['fullname' => $fullname], 'id,fullname');
        if ($course) {
            break;
        }
    }
    if (!$course && !empty($target['fallbackid'])) {
        $course = $DB->get_record('course', ['id' => $target['fallbackid']], 'id,fullname');
    }
    if (!$course) {
        $missing[] = implode(' / ', $target['names']);
        continue;
    }
    $resolvedcourses[$course->id] = $course;
}

mtrace('Parent access course: ' . $parentcourse->fullname . ' (ID ' . $parentcourseid . ')');
foreach ($resolvedcourses as $course) {
    $conditions = [
        'enrol' => 'meta',
        'courseid' => $course->id,
        'customint1' => $parentcourseid,
    ];
    if ($DB->record_exists('enrol', $conditions)) {
        mtrace('[EXISTS] ' . $course->fullname . ' (ID ' . $course->id . ')');
        continue;
    }
    if (!$options['apply']) {
        mtrace('[PLAN]   ' . $course->fullname . ' (ID ' . $course->id . ')');
        continue;
    }
    $metaplugin->add_instance($course, [
        'customint1' => $parentcourseid,
        'customint2' => 0,
        'status' => ENROL_INSTANCE_ENABLED,
    ]);
    mtrace('[ADDED]  ' . $course->fullname . ' (ID ' . $course->id . ')');
}

foreach ($missing as $fullname) {
    mtrace('[MISSING] ' . $fullname);
}

if (!$options['apply']) {
    mtrace('No changes made. Run again with --apply after reviewing this plan.');
} else {
    mtrace('Board access configuration completed.');
}
