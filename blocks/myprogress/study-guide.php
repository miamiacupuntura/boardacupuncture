<?php
// This file is part of Moodle - http://moodle.org/.

require_once(__DIR__ . '/../../config.php');

require_login();

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
                'title' => 'The Channels and Collaterals',
                'description' => 'Study channel functions, pathways, circulation, relationships and organization through guided reading, visual review and structured Board practice.',
                'objectives' => 9,
                'guide_status' => 'Ready',
                'board_practice' => 'Included',
                'path' => '/study-guide/index.html',
                'anchor' => '',
            ],
            [
                'title' => 'Point Categories',
                'description' => 'Review Back-Shu, Front-Mu, Five Shu, Luo-Connecting and other point categories through clinical relationships, key distinctions and practice exams.',
                'objectives' => 26,
                'guide_status' => 'Ready',
                'board_practice' => 'Included',
                'path' => '/study-guide/index.html',
                'anchor' => 'point-categories-draft',
            ],
            [
                'title' => 'Channel Pathway Identification',
                'description' => 'Identify and compare Primary, Divergent, Luo-Connecting, Sinew and Extraordinary pathways using visual evidence, anatomical landmarks and Board-level practice.',
                'objectives' => 12,
                'guide_status' => 'Ready',
                'board_practice' => 'Included',
                'path' => '/study-guide/channel-pathways/index.html',
                'anchor' => '',
            ],
            [
                'title' => 'Point Contraindications',
                'description' => 'Classify anatomical risks, patient and pregnancy considerations, modality precautions and source-specific restrictions through twelve focused safety objectives.',
                'objectives' => 12,
                'guide_status' => 'Ready',
                'board_practice' => '125 questions',
                'path' => '/study-guide/point-contraindications/index.html',
                'anchor' => '',
            ],
        ],
    ],
    'herbology' => [
        'title' => 'Chinese Herbology',
        'description' => 'Study Guide chapters for Single Herbs and Formulas.',
        'chapters' => [
            [
                'title' => 'Single Herbs',
                'description' => 'Study single herbs through the same General, Topics 1–34 and Mixed Review categories used in the Chinese Herbology Board Level workspace.',
                'path' => '/blocks/myprogress/single-herbs-study-guide.php',
                'anchor' => '',
            ],
        ],
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
.subject-study-guide__top-actions{display:flex;align-items:center;justify-content:flex-end;gap:10px;flex-wrap:wrap}
.subject-study-guide__catalog{display:inline-flex;min-height:42px;align-items:center;justify-content:center;padding:9px 15px;border-radius:8px;background:#13705c;color:#fff;font-weight:800;text-decoration:none;white-space:nowrap}
.subject-study-guide__catalog--ce{background:#7652a5}.subject-study-guide__catalog:hover{background:#0d5a4a;color:#fff;text-decoration:none}.subject-study-guide__catalog--ce:hover{background:#654391}
.subject-study-guide__board-overview{margin:0 0 18px;padding:34px 40px;border-top:5px solid #0b817d;border-radius:13px;background:linear-gradient(135deg,#f8fcfb 0%,#fbf8fd 100%);box-shadow:0 3px 12px rgba(15,23,42,.05)}
.subject-study-guide__board-overview .subject-study-guide__eyebrow{margin-bottom:8px;color:#08746f}
.subject-study-guide__board-overview h3{margin:0 0 10px;color:#123246;font-size:clamp(28px,4vw,42px);line-height:1.12}
.subject-study-guide__board-overview p:last-child{max-width:760px;margin:0;color:#52677a;line-height:1.55}
.subject-study-guide__benefits{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin:0 0 26px}
.subject-study-guide__benefit{padding:18px;border:1px solid #cfdae0;border-radius:10px;background:#fff}
.subject-study-guide__benefit h3{margin:0 0 8px;color:#17232a;font-size:16px}
.subject-study-guide__benefit p{margin:0;color:#52677a;font-size:14px;line-height:1.5}
.subject-study-guide__grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:18px}
.subject-study-guide__card{display:flex;min-height:220px;flex-direction:column;padding:22px;border:1px solid #dce2e6;border-top:5px solid #f77e25;border-radius:12px;background:#fff;box-shadow:0 3px 12px rgba(15,23,42,.06)}
.subject-study-guide__status{align-self:flex-start;margin:0 0 13px;padding:5px 9px;border-radius:999px;background:#e8f5f1;color:#116553;font-size:12px;font-weight:800}
.subject-study-guide__card h3{margin:0 0 9px;color:#17232a;font-size:21px;line-height:1.25}
.subject-study-guide__card p{margin:0 0 18px;color:#64748b;line-height:1.55}
.subject-study-guide__facts{display:grid;grid-template-columns:max-content 1fr;gap:6px 12px;margin:0 0 20px;color:#52677a;font-size:14px;line-height:1.35}
.subject-study-guide__facts dt{color:#17232a;font-weight:800}
.subject-study-guide__facts dd{margin:0}
.subject-study-guide__open{display:inline-flex;min-height:43px;align-items:center;align-self:flex-start;justify-content:center;margin-top:auto;padding:9px 15px;border-radius:8px;background:#2563eb;color:#fff;font-weight:800;text-decoration:none}
.subject-study-guide__open:hover{background:#1d4ed8;color:#fff;text-decoration:none}
.subject-study-guide__future{grid-column:1/-1;min-height:180px;border-top-color:#94a3b8;border-style:dashed;text-align:center}
.subject-study-guide__future .subject-study-guide__status,.subject-study-guide__future .subject-study-guide__open{align-self:center}
.subject-study-guide__construction{padding:clamp(36px,8vw,76px) 22px;border:1px dashed #aeb8c0;border-radius:14px;background:#fff;text-align:center}
.subject-study-guide__construction-mark{margin-bottom:15px;font-size:48px}
.subject-study-guide__construction h3{margin:0 0 12px;color:#a64f12;font-size:28px}
.subject-study-guide__construction p{max-width:570px;margin:0 auto 26px;color:#64748b;line-height:1.65}
.subject-study-guide a:focus-visible{outline:3px solid #185cca;outline-offset:3px}
@media(max-width:700px){.subject-study-guide__top{display:block}.subject-study-guide__top-actions{align-items:stretch;flex-direction:column;margin-top:16px}.subject-study-guide__top-actions a{width:100%;box-sizing:border-box}.subject-study-guide__back{margin-top:0}.subject-study-guide__board-overview{padding:26px 22px}.subject-study-guide__benefits{grid-template-columns:1fr}.subject-study-guide__grid{grid-template-columns:1fr}.subject-study-guide__future{grid-column:auto}}
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
        <nav class="subject-study-guide__top-actions" aria-label="Study Guide course catalogs">
            <a class="subject-study-guide__back" href="<?php echo (new moodle_url('/my/'))->out(false); ?>">← Back to Dashboard</a>
            <a class="subject-study-guide__catalog subject-study-guide__catalog--ce" href="<?php echo (new moodle_url('/study-guide/continuing-education.html'))->out(false); ?>">Continuing Education</a>
        </nav>
    </header>

    <?php if ($areaid === 'pointlocation'): ?>
        <section class="subject-study-guide__board-overview" aria-labelledby="board-preparation-title">
            <p class="subject-study-guide__eyebrow">Monthly Membership</p>
            <h3 id="board-preparation-title">Board Preparation Courses</h3>
            <p>Choose from the Study Guides that are ready in your AcupunctureTests membership. Each guide combines chapter reading, learning objectives, visual review and Board-level practice.</p>
        </section>

        <section class="subject-study-guide__benefits" aria-label="Board Study Guide features">
            <article class="subject-study-guide__benefit">
                <h3>Ready Study Guides</h3>
                <p>Only Study Guides that are ready for students appear in this catalog.</p>
            </article>
            <article class="subject-study-guide__benefit">
                <h3>Objective-based learning</h3>
                <p>Each chapter is organized around focused objectives, readings and review resources.</p>
            </article>
            <article class="subject-study-guide__benefit">
                <h3>Board preparation</h3>
                <p>Use the practice activities and Board-level exams to evaluate your progress.</p>
            </article>
        </section>
    <?php endif; ?>

    <section id="study-guide-chapters" class="subject-study-guide__grid" aria-label="<?php echo s($area['title']); ?> Study Guide chapters">
        <?php foreach ($area['chapters'] as $chapter): ?>
            <?php
            $chapterurl = new moodle_url($chapter['path']);
            if ($chapter['anchor'] !== '') {
                $chapterurl->set_anchor($chapter['anchor']);
            }
            ?>
            <article class="subject-study-guide__card">
                <span class="subject-study-guide__status">AVAILABLE</span>
                <h3><?php echo s($chapter['title']); ?></h3>
                <p><?php echo s($chapter['description']); ?></p>
                <?php if (isset($chapter['objectives'])): ?>
                    <dl class="subject-study-guide__facts">
                        <dt>Learning objectives</dt>
                        <dd><?php echo (int) $chapter['objectives']; ?></dd>
                        <dt>Study Guide</dt>
                        <dd><?php echo s($chapter['guide_status']); ?></dd>
                        <dt>Board-level practice</dt>
                        <dd><?php echo s($chapter['board_practice']); ?></dd>
                    </dl>
                <?php endif; ?>
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
