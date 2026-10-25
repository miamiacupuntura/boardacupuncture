<?php
// Temporary landing page for the Clean Needle Techniques Exam Key.

require_once(__DIR__ . '/../../config.php');

require_login();

$boardcourseid = 113;
$membershipcourseid = 116;
$context = context_course::instance($boardcourseid);
$membershipcontext = context_course::instance($membershipcourseid);
$hasaccess = is_enrolled($membershipcontext, $USER->id, '', true)
    || has_capability('moodle/course:view', $membershipcontext, $USER->id);

if (!$hasaccess) {
    redirect(
        new moodle_url('/my/'),
        'Purchase the 30-day Board access before opening this learning resource.',
        null,
        \core\output\notification::NOTIFY_ERROR
    );
}

$pageurl = new moodle_url('/blocks/menu/clean-needle-exam-key.php');
$dashboardurl = new moodle_url('/my/');
$PAGE->set_url($pageurl);
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Clean Needle Techniques Exam Key');
$PAGE->set_heading('Clean Needle Techniques Exam Key');

echo $OUTPUT->header();
?>
<main class="board-workspace">
    <a class="board-workspace__back" href="<?php echo $dashboardurl->out(false); ?>">← Back to Dashboard</a>
    <header class="board-workspace__hero">
        <p>EXAM KEY</p>
        <h2>Clean Needle Techniques</h2>
        <span>This Exam Key library is being prepared and will be available here as the content is developed.</span>
    </header>
    <section class="board-workspace__grid" aria-label="Clean Needle Techniques Exam Key status">
        <article class="board-workspace__card">
            <span class="board-workspace__type">Coming Soon</span>
            <h3>Exam Key Library in Development</h3>
            <p>
                The Clean Needle Techniques answer keys and review material are currently being organized.
                Please return to the Dashboard to continue with Questions, Simulation, Games and the available Study Guide.
            </p>
            <div class="board-workspace__actions">
                <a class="board-workspace__button" href="<?php echo $dashboardurl->out(false); ?>">Back to Dashboard</a>
            </div>
        </article>
    </section>
</main>
<?php
echo $OUTPUT->footer();
