<?php
// Board-specific learning workspace.

require_once(__DIR__ . '/../../config.php');

require_login();

$boards = [
    'foundations' => [
        'name' => 'Foundations of Oriental Medicine',
        'courseid' => 30,
        'area' => 'foundations',
        'questions' => ['Foundation Exams', 'Foundations Exams'],
        'simulation' => ['Foundation Simulation Exams', 'Foundations Simulation Exams'],
        'games' => ['Playing Foundation Module Exams', 'Playing Foundations Module Exams', 'Playing Foundation'],
        'notes' => ['Foundation Notes', 'Foundations Notes'],
    ],
    'pointlocation' => [
        'name' => 'Acupuncture & Point Location',
        'courseid' => 34,
        'area' => 'pointlocation',
        'questions' => ['Point Location and Point Indication Exams'],
        'simulation' => ['Point Location and Point Indication Simulation Exams', 'Point Location Simulation'],
        'games' => ['Playing Point Location Module Exams'],
        'notes' => ['Point Location Notes'],
    ],
    'herbology' => [
        'name' => 'Chinese Herbology',
        'courseid' => 45,
        'area' => 'herbology',
        'questions' => ['Single Herbs Exams', 'Single Herbs'],
        'simulation' => ['Herbal Medicine Simulation Exams'],
        'games' => ['Playing Herbology Module Exams'],
        'notes' => ['Herbology Notes'],
        'formulas' => ['Formulas Exams', 'Herbal Formulas'],
    ],
    'biomedicine' => [
        'name' => 'Biomedicine',
        'courseid' => 36,
        'area' => 'biomedicine',
        'questions' => ['Biomedicine Exams'],
        'simulation' => ['Biomedicine Simulation Exams', 'Biomedicine Simulation'],
        'games' => ['Playing Biomedicine Module Exams'],
        'notes' => ['Biomedicine Notes'],
    ],
    'cleanneedle' => [
        'name' => 'Clean Needle Techniques',
        'courseid' => 113,
        'area' => 'cleanneedle',
        'questions' => ['Clean Needle Techniques Exams', 'Clean Needle Techniques'],
        'simulation' => [
            'Clean Needle Simulation',
            'Clean Needle Simulation Exams',
            'Clean Needle Techniques Simulation',
        ],
        'games' => ['Playing Clean Needle Techniques Module Exams', 'Playing Clean Needle'],
        'notes' => ['Clean Needle Techniques Notes', 'Clean Needle Notes'],
    ],
];

$boardid = required_param('board', PARAM_ALPHA);
if (!isset($boards[$boardid])) {
    throw new moodle_exception('invalidparameter');
}

$board = $boards[$boardid];
$context = context_course::instance($board['courseid']);
$membershipcourseid = 116;
$membershipcontext = context_course::instance($membershipcourseid);
$hasaccess = is_enrolled($membershipcontext, $USER->id, '', true)
    || has_capability('moodle/course:view', $membershipcontext, $USER->id);

if (!$hasaccess) {
    redirect(
        new moodle_url('/my/'),
        'Purchase the 30-day Board access before opening these learning resources.',
        null,
        \core\output\notification::NOTIFY_ERROR
    );
}

$pageurl = new moodle_url('/blocks/menu/board.php', ['board' => $boardid]);
$PAGE->set_url($pageurl);
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_title($board['name']);
$PAGE->set_heading($board['name']);

/**
 * Find the first visible course whose full name matches a configured candidate.
 *
 * @param array $names Candidate full names.
 * @param int|null $fallbackid Known primary/fallback course ID.
 * @return stdClass|null
 */
function block_menu_find_board_course(array $names, ?int $fallbackid = null): ?stdClass {
    global $DB;

    foreach ($names as $name) {
        $record = $DB->get_record('course', ['fullname' => $name, 'visible' => 1], 'id,fullname');
        if ($record) {
            return $record;
        }
    }

    if ($fallbackid) {
        return $DB->get_record('course', ['id' => $fallbackid, 'visible' => 1], 'id,fullname') ?: null;
    }

    return null;
}

/**
 * Render an action for a Board resource without exposing unavailable child courses.
 *
 * @param stdClass|null $resource Resolved Moodle course.
 * @param string $label Button label.
 * @return string
 */
function block_menu_board_course_action(?stdClass $resource, string $label): string {
    global $USER;

    if (!$resource) {
        return '<span class="board-workspace__pending">Resource connection pending</span>';
    }

    $resourcecontext = context_course::instance($resource->id);
    $hasaccess = is_enrolled($resourcecontext, $USER->id, '', true)
        || has_capability('moodle/course:view', $resourcecontext, $USER->id);
    if (!$hasaccess) {
        return '<span class="board-workspace__pending">Access connection pending</span>';
    }

    $url = new moodle_url('/course/view.php', ['id' => $resource->id]);
    return '<a class="board-workspace__button" href="' . $url->out(false) . '">' . s($label) . '</a>';
}

$questions = block_menu_find_board_course($board['questions'], $board['courseid']);
$simulation = block_menu_find_board_course($board['simulation']);
$games = block_menu_find_board_course($board['games']);
$notes = block_menu_find_board_course($board['notes']);
$studyguideurl = new moodle_url('/blocks/myprogress/study-guide.php', ['area' => $board['area']]);

$resources = [
    [
        'type' => 'Questions',
        'title' => 'Questions',
        'description' => 'Practice the question modules included with this Board.',
        'action' => block_menu_board_course_action($questions, 'Open Questions'),
    ],
    [
        'type' => 'Simulation',
        'title' => 'Simulation',
        'description' => 'Practice under conditions designed to resemble the Board exam.',
        'action' => block_menu_board_course_action($simulation, 'Open Simulation'),
    ],
    [
        'type' => 'Games',
        'title' => 'Games',
        'description' => 'Reinforce recall through the available interactive activities.',
        'action' => block_menu_board_course_action($games, 'Open Games'),
    ],
    [
        'type' => 'Notes',
        'title' => 'Notes',
        'description' => 'Review the notes and reference material for this Board.',
        'action' => block_menu_board_course_action($notes, 'Open Notes'),
    ],
    [
        'type' => 'Study Guide',
        'title' => 'Study Guide',
        'description' => 'Read the available Study Guide chapters for this Board.',
        'action' => '<a class="board-workspace__button" href="' . $studyguideurl->out(false) . '">Open Study Guide</a>',
    ],
];

if ($boardid === 'herbology') {
    $formulas = block_menu_find_board_course($board['formulas'], 117);
    $resources[0]['action'] .= block_menu_board_course_action($formulas, 'Open Formulas Questions');
}

$resourcehtml = '';
foreach ($resources as $resource) {
    $resourcehtml .= '<article class="board-workspace__card">'
        . '<span class="board-workspace__type">' . s($resource['type']) . '</span>'
        . '<h3>' . s($resource['title']) . '</h3>'
        . '<p>' . s($resource['description']) . '</p>'
        . '<div class="board-workspace__actions">' . $resource['action'] . '</div>'
        . '</article>';
}

echo $OUTPUT->header();
?>
<main class="board-workspace">
    <a class="board-workspace__back" href="<?php echo (new moodle_url('/my/'))->out(false); ?>">← Back to Boards</a>
    <header class="board-workspace__hero">
        <p>YOUR BOARD</p>
        <h2><?php echo s($board['name']); ?></h2>
        <span>Included with your 30-day access to all five Boards. Choose the learning activity you want to continue.</span>
    </header>
    <section class="board-workspace__grid" aria-label="<?php echo s($board['name']); ?> learning resources">
        <?php echo $resourcehtml; ?>
    </section>
</main>
<?php
echo $OUTPUT->footer();
