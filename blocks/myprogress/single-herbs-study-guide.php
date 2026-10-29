<?php
// Single Herbs Study Guide category catalog.

require_once(__DIR__ . '/../../config.php');

require_login();

$sourcecourseid = 45;
$focus = optional_param('focus', '', PARAM_ALPHANUMEXT);
$sections = $DB->get_records_select(
    'course_sections',
    'course = :courseid AND section BETWEEN :firstsection AND :lastsection',
    [
        'courseid' => $sourcecourseid,
        'firstsection' => 0,
        'lastsection' => 34,
    ],
    'section ASC',
    'id,section,name'
);

$categories = [];
foreach ($sections as $section) {
    $number = (int) $section->section;
    $name = trim((string) $section->name);
    if ($number === 0 && $name === '') {
        $name = 'General / Uncategorized';
    } else if ($name === '') {
        $name = 'Topic ' . $number;
    }
    $categories[$number] = [
        'number' => $number,
        'name' => format_string($name),
    ];
}

$selected = null;
$ismixed = $focus === 'mixed';
if (!$ismixed && $focus !== '' && ctype_digit($focus)) {
    $number = (int) $focus;
    if (isset($categories[$number])) {
        $selected = $categories[$number];
    }
}

$pageparams = [];
if ($focus !== '') {
    $pageparams['focus'] = $focus;
}
$pageurl = new moodle_url('/blocks/myprogress/single-herbs-study-guide.php', $pageparams);
$categoryindexurl = new moodle_url('/blocks/myprogress/single-herbs-study-guide.php');
$herbologyurl = new moodle_url('/blocks/myprogress/study-guide.php', ['area' => 'herbology']);
$dashboardurl = new moodle_url('/my/');

$PAGE->set_url($pageurl);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Single Herbs Study Guide');
$PAGE->set_heading('Single Herbs Study Guide');

echo $OUTPUT->header();
?>
<style>
.single-herbs-guide{max-width:1160px;margin:24px auto 58px;padding:0 16px;color:#17232a}
.single-herbs-guide a:focus-visible{outline:3px solid #185cca;outline-offset:3px}
.single-herbs-guide__top{display:flex;align-items:flex-start;justify-content:space-between;gap:22px;margin-bottom:22px}
.single-herbs-guide__eyebrow{margin:0 0 6px;color:#08746f;font-size:13px;font-weight:900;letter-spacing:.09em;text-transform:uppercase}
.single-herbs-guide h2{margin:0 0 9px;font-size:clamp(29px,4vw,42px);line-height:1.12}
.single-herbs-guide__lead{max-width:760px;margin:0;color:#5b6b79;font-size:16px;line-height:1.6}
.single-herbs-guide__actions{display:flex;gap:9px;flex-wrap:wrap;justify-content:flex-end}
.single-herbs-guide__back{display:inline-flex;min-height:42px;align-items:center;padding:9px 14px;border:1px solid #cbd5dc;border-radius:8px;background:#fff;color:#334155;font-weight:800;text-decoration:none;white-space:nowrap}
.single-herbs-guide__back:hover{background:#f1f5f9;text-decoration:none}
.single-herbs-guide__overview{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:24px;align-items:center;margin-bottom:24px;padding:23px 26px;border-left:5px solid #0b817d;border-radius:12px;background:#eef8f6;box-shadow:0 3px 12px rgba(15,23,42,.05)}
.single-herbs-guide__overview h3{margin:0 0 7px;font-size:21px}.single-herbs-guide__overview p{margin:0;color:#52677a;line-height:1.55}
.single-herbs-guide__count{display:flex;min-width:116px;flex-direction:column;align-items:center;padding:13px 18px;border-radius:10px;background:#fff;color:#0f5f58}
.single-herbs-guide__count strong{font-size:29px;line-height:1}.single-herbs-guide__count span{margin-top:5px;font-size:12px;font-weight:800;text-transform:uppercase}
.single-herbs-guide__grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:14px}
.single-herbs-guide__category{display:flex;min-height:135px;flex-direction:column;padding:18px;border:1px solid #d9e2e7;border-top:4px solid #f77e25;border-radius:11px;background:#fff;color:inherit;text-decoration:none;box-shadow:0 2px 8px rgba(15,23,42,.05);transition:transform .15s ease,box-shadow .15s ease}
.single-herbs-guide__category:hover{color:inherit;text-decoration:none;transform:translateY(-2px);box-shadow:0 7px 18px rgba(15,23,42,.1)}
.single-herbs-guide__category-number{display:inline-flex;width:34px;height:34px;align-items:center;justify-content:center;margin-bottom:12px;border-radius:50%;background:#fff0e5;color:#a64f12;font-weight:900}
.single-herbs-guide__category strong{font-size:16px;line-height:1.35}.single-herbs-guide__category small{margin-top:auto;padding-top:12px;color:#19706a;font-weight:800}
.single-herbs-guide__category--mixed{border-top-color:#0b817d;background:#f6fbfa}.single-herbs-guide__category--mixed .single-herbs-guide__category-number{background:#dff2ee;color:#0d665e}
.single-herbs-guide__empty{padding:34px;border:1px dashed #aeb8c0;border-radius:12px;background:#fff;text-align:center;color:#64748b}
.single-herbs-guide__workspace{padding:28px;border:1px solid #d9e2e7;border-top:5px solid #0b817d;border-radius:13px;background:#fff;box-shadow:0 3px 12px rgba(15,23,42,.06)}
.single-herbs-guide__workspace-header{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;padding-bottom:20px;border-bottom:1px solid #e2e8ec}
.single-herbs-guide__workspace h3{margin:0 0 8px;font-size:clamp(24px,3vw,32px)}
.single-herbs-guide__workspace-intro{max-width:740px;margin:0;color:#5b6b79;line-height:1.6}
.single-herbs-guide__status{display:inline-flex;padding:6px 10px;border-radius:999px;background:#fff0e5;color:#a64f12;font-size:12px;font-weight:900;white-space:nowrap}
.single-herbs-guide__plan{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;margin:0;padding:0;list-style:none;counter-reset:guide-step}
.single-herbs-guide__plan li{counter-increment:guide-step;position:relative;min-height:105px;padding:18px 18px 18px 58px;border:1px solid #dde4e8;border-radius:10px;background:#f9fbfc}
.single-herbs-guide__plan li:before{content:counter(guide-step);position:absolute;left:17px;top:17px;display:flex;width:28px;height:28px;align-items:center;justify-content:center;border-radius:50%;background:#176b70;color:#fff;font-weight:900}
.single-herbs-guide__plan strong{display:block;margin-bottom:5px}.single-herbs-guide__plan span{color:#60717e;font-size:14px;line-height:1.45}
.single-herbs-guide__note{margin:20px 0 0;padding:15px 17px;border-radius:9px;background:#f0f5ff;color:#40536b;line-height:1.5}
@media(max-width:720px){.single-herbs-guide__top,.single-herbs-guide__workspace-header{display:block}.single-herbs-guide__actions{justify-content:flex-start;margin-top:16px}.single-herbs-guide__overview{grid-template-columns:1fr}.single-herbs-guide__count{align-items:flex-start}.single-herbs-guide__plan{grid-template-columns:1fr}}
</style>

<main class="single-herbs-guide">
    <header class="single-herbs-guide__top">
        <div>
            <p class="single-herbs-guide__eyebrow">Chinese Herbology · Study Guide</p>
            <h2>Single Herbs</h2>
            <p class="single-herbs-guide__lead">Study the materia medica using the same category boundaries and sequence as the Single Herbs Board Level workspace.</p>
        </div>
        <nav class="single-herbs-guide__actions" aria-label="Single Herbs Study Guide navigation">
            <a class="single-herbs-guide__back" href="<?php echo $herbologyurl->out(false); ?>">← Chinese Herbology</a>
            <a class="single-herbs-guide__back" href="<?php echo $dashboardurl->out(false); ?>">Dashboard</a>
        </nav>
    </header>

    <?php if ($selected !== null || $ismixed): ?>
        <?php
        $focuslabel = $ismixed ? 'Mixed Review' : ($selected['number'] === 0 ? 'General' : 'Category ' . $selected['number']);
        $focustitle = $ismixed ? 'Mixed Review' : $selected['name'];
        ?>
        <section class="single-herbs-guide__workspace" aria-labelledby="single-herbs-focus-title">
            <div class="single-herbs-guide__workspace-header">
                <div>
                    <p class="single-herbs-guide__eyebrow"><?php echo s($focuslabel); ?></p>
                    <h3 id="single-herbs-focus-title"><?php echo s($focustitle); ?></h3>
                    <p class="single-herbs-guide__workspace-intro">This category uses the same scope as Board Level practice. Its source-verified learning material will follow one consistent study sequence.</p>
                </div>
                <span class="single-herbs-guide__status">STRUCTURE READY</span>
            </div>
            <ol class="single-herbs-guide__plan">
                <li><strong>Category foundations</strong><span>Definition, therapeutic strategy and the distinctions that define this group.</span></li>
                <li><strong>Core herbs</strong><span>Names, properties, channels, dosage ranges and the most testable identifiers.</span></li>
                <li><strong>Functions and indications</strong><span>Primary actions, clinical presentations and symptom-pattern relationships.</span></li>
                <li><strong>Comparisons</strong><span>Differentiate commonly confused herbs and recognize the best choice in context.</span></li>
                <li><strong>Safety</strong><span>Contraindications, cautions, toxicity, preparation and clinically relevant interactions.</span></li>
                <li><strong>Recall and Board review</strong><span>Flashcards, image recognition and category-focused multiple-choice review.</span></li>
            </ol>
            <p class="single-herbs-guide__note"><strong>Content stage:</strong> this category framework is ready. Verified educational content will be developed from the supplied Bensky materia medica without changing the Original Bank.</p>
        </section>
        <p style="margin-top:18px"><a class="single-herbs-guide__back" href="<?php echo $categoryindexurl->out(false); ?>">← All Single Herbs categories</a></p>
    <?php else: ?>
        <section class="single-herbs-guide__overview" aria-labelledby="single-herbs-categories-title">
            <div>
                <h3 id="single-herbs-categories-title">Board-aligned category index</h3>
                <p>The category names and order come from the same Moodle sections used by Board Level, followed by a Mixed Review workspace.</p>
            </div>
            <div class="single-herbs-guide__count" aria-label="Category count">
                <strong><?php echo count($categories) + 1; ?></strong>
                <span>study areas</span>
            </div>
        </section>

        <?php if ($categories): ?>
            <section class="single-herbs-guide__grid" aria-label="Single Herbs categories">
                <?php foreach ($categories as $category): ?>
                    <?php $categoryurl = new moodle_url('/blocks/myprogress/single-herbs-study-guide.php', ['focus' => (string) $category['number']]); ?>
                    <a class="single-herbs-guide__category" href="<?php echo $categoryurl->out(false); ?>">
                        <span class="single-herbs-guide__category-number"><?php echo $category['number'] === 0 ? 'G' : $category['number']; ?></span>
                        <strong><?php echo $category['name']; ?></strong>
                        <small>Open study framework →</small>
                    </a>
                <?php endforeach; ?>
                <?php $mixedurl = new moodle_url('/blocks/myprogress/single-herbs-study-guide.php', ['focus' => 'mixed']); ?>
                <a class="single-herbs-guide__category single-herbs-guide__category--mixed" href="<?php echo $mixedurl->out(false); ?>">
                    <span class="single-herbs-guide__category-number">M</span>
                    <strong>Mixed Review</strong>
                    <small>Open study framework →</small>
                </a>
            </section>
        <?php else: ?>
            <div class="single-herbs-guide__empty">The Single Herbs category source is temporarily unavailable. No course content was changed.</div>
        <?php endif; ?>
    <?php endif; ?>
</main>
<?php
echo $OUTPUT->footer();
