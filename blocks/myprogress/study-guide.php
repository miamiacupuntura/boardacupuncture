<?php
// This file is part of Moodle - http://moodle.org/.

require_once(__DIR__ . '/../../config.php');

require_login();

// Change this value whenever the Study Guide entry document changes. It keeps
// browsers from reusing an older, partially transformed chapter page.
$studyguideversion = '20261021-1';

$areas = [
    'foundations' => [
        'title' => 'Foundations',
        'description' => 'Study Guide chapters for the foundations of Oriental medicine.',
        'chapters' => [],
    ],
    'pointlocation' => [
        'title' => 'Acupuncture & Point Location',
        'description' => 'Choose one of the currently available Point Location Study Guide chapters.',
        'chapters' => [
            [
                'title' => 'The Channels and Collaterals. Introduction',
                'description' => 'Channel functions, pathways, circulation and internal relationships.',
                'path' => '/study-guide/index.html',
                'anchor' => '',
            ],
            [
                'title' => 'Point Categories',
                'description' => 'Back-Shu, Front-Mu, Five Shu, Luo-Connecting and other point categories.',
                'path' => '/study-guide/index.html',
                'anchor' => 'point-categories-draft',
            ],
        ],
    ],
    'herbology' => [
        'title' => 'Chinese Herbology',
        'description' => 'Study Guide chapters for Single Herbs and Formulas.',
        'chapters' => [],
    ],
    'biomedicine' => [
        'title' => 'Biomedicine',
        'description' => 'Study Guide chapters for biomedical concepts and clinical knowledge.',
        'chapters' => [],
    ],
    'cleanneedle' => [
        'title' => 'Clean Needle Techniques',
        'description' => 'Study Guide chapters for clean needle technique and safety principles.',
        'chapters' => [],
    ],
];

$areaid = optional_param('area', 'pointlocation', PARAM_ALPHA);
if (!array_key_exists($areaid, $areas)) {
    $areaid = 'pointlocation';
}
$area = $areas[$areaid];
$showconstruction = optional_param('view', '', PARAM_ALPHA) === 'construction';

$pageurl = new moodle_url('/blocks/myprogress/study-guide.php', ['area' => $areaid]);
$chapterlisturl = new moodle_url('/blocks/myprogress/study-guide.php', ['area' => $areaid]);
if ($showconstruction) {
    $pageurl->param('view', 'construction');
}

$PAGE->set_url($pageurl);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title($area['title'] . ' Study Guide');
$PAGE->set_heading($area['title'] . ' Study Guide');

echo $OUTPUT->header();
?>
<style>
.subject-study-guide{max-width:1050px;margin:24px auto 56px;padding:0 14px}
.subject-study-guide__top{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;margin-bottom:24px}
.subject-study-guide__eyebrow{margin:0 0 5px;color:#52616b;font-size:13px;font-weight:800;letter-spacing:.08em;text-transform:uppercase}
.subject-study-guide h2{margin:0 0 8px;color:#17232a;font-size:clamp(26px,4vw,36px)}
.subject-study-guide__intro{max-width:720px;margin:0;color:#64748b;font-size:16px;line-height:1.55}
.subject-study-guide__back{display:inline-flex;min-height:42px;align-items:center;padding:9px 15px;border:1px solid #ccd4da;border-radius:8px;background:#fff;color:#334155;font-weight:700;text-decoration:none;white-space:nowrap}
.subject-study-guide__back:hover{background:#f1f5f9;text-decoration:none}
.subject-study-guide__grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}
.subject-study-guide__card{display:flex;min-height:220px;flex-direction:column;padding:22px;border:1px solid #dce2e6;border-top:5px solid #f77e25;border-radius:12px;background:#fff;box-shadow:0 3px 12px rgba(15,23,42,.06)}
.subject-study-guide__status{align-self:flex-start;margin:0 0 13px;padding:5px 9px;border-radius:999px;background:#e8f5f1;color:#116553;font-size:12px;font-weight:800}
.subject-study-guide__card h3{margin:0 0 9px;color:#17232a;font-size:21px;line-height:1.25}
.subject-study-guide__card p{margin:0 0 20px;color:#64748b;line-height:1.55}
.subject-study-guide__open{display:inline-flex;min-height:43px;align-items:center;align-self:flex-start;justify-content:center;margin-top:auto;padding:9px 15px;border-radius:8px;background:#2563eb;color:#fff;font-weight:800;text-decoration:none}
.subject-study-guide__open:hover{background:#1d4ed8;color:#fff;text-decoration:none}
.subject-study-guide__future{grid-column:1/-1;min-height:180px;border-top-color:#94a3b8;border-style:dashed;text-align:center}
.subject-study-guide__future .subject-study-guide__status,.subject-study-guide__future .subject-study-guide__open{align-self:center}
.subject-study-guide__construction{padding:clamp(36px,8vw,76px) 22px;border:1px dashed #aeb8c0;border-radius:14px;background:#fff;text-align:center}
.subject-study-guide__construction-mark{margin-bottom:15px;font-size:48px}
.subject-study-guide__construction h3{margin:0 0 12px;color:#a64f12;font-size:28px}
.subject-study-guide__construction p{max-width:570px;margin:0 auto 26px;color:#64748b;line-height:1.65}
.subject-study-guide a:focus-visible{outline:3px solid #185cca;outline-offset:3px}
@media(max-width:700px){.subject-study-guide__top{display:block}.subject-study-guide__back{margin-top:16px}.subject-study-guide__grid{grid-template-columns:1fr}.subject-study-guide__future{grid-column:auto}}
</style>
<main class="subject-study-guide">
<?php if ($showconstruction): ?>
    <section class="subject-study-guide__construction" aria-labelledby="construction-title">
        <div class="subject-study-guide__construction-mark" aria-hidden="true">⌛</div>
        <h2 id="construction-title">More chapters</h2>
        <h3>Under construction / En construcción</h3>
        <p>We are preparing the next <?php echo s($area['title']); ?> Study Guide chapters. They will appear on the chapter screen as they are completed.</p>
        <a class="subject-study-guide__back" href="<?php echo $chapterlisturl->out(false); ?>">← Back to chapters</a>
    </section>
<?php else: ?>
    <header class="subject-study-guide__top">
        <div>
            <p class="subject-study-guide__eyebrow">Study Guide</p>
            <h2><?php echo s($area['title']); ?></h2>
            <p class="subject-study-guide__intro"><?php echo s($area['description']); ?></p>
        </div>
        <a class="subject-study-guide__back" href="<?php echo (new moodle_url('/my/'))->out(false); ?>">← Back to Dashboard</a>
    </header>

    <section class="subject-study-guide__grid" aria-label="<?php echo s($area['title']); ?> Study Guide chapters">
        <?php foreach ($area['chapters'] as $chapter): ?>
            <?php
            $chapterurl = new moodle_url($chapter['path'], [
                'track' => 'board',
                'release' => $studyguideversion,
            ]);
            if ($chapter['anchor'] !== '') {
                $chapterurl->set_anchor($chapter['anchor']);
            }
            ?>
            <article class="subject-study-guide__card">
                <span class="subject-study-guide__status">AVAILABLE</span>
                <h3><?php echo s($chapter['title']); ?></h3>
                <p><?php echo s($chapter['description']); ?></p>
                <a class="subject-study-guide__open" href="<?php echo $chapterurl->out(false); ?>">Open Study Guide →</a>
            </article>
        <?php endforeach; ?>

        <article class="subject-study-guide__card subject-study-guide__future">
            <span class="subject-study-guide__status">COMING SOON</span>
            <h3><?php echo empty($area['chapters']) ? 'Study Guide chapters' : 'More chapters'; ?></h3>
            <p>New chapters will be added here as their Study Guides are completed.</p>
            <a class="subject-study-guide__open" href="<?php echo (new moodle_url('/blocks/myprogress/study-guide.php', ['area' => $areaid, 'view' => 'construction']))->out(false); ?>">View status</a>
        </article>
    </section>
<?php endif; ?>
</main>
<?php
echo $OUTPUT->footer();
