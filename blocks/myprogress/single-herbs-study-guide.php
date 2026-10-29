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

$selectedname = $selected !== null ? (string) $selected['name'] : '';
$istopicone = $selected !== null
    && stripos($selectedname, 'Ma Huang') !== false
    && stripos($selectedname, 'Gui Zhi') !== false
    && stripos($selectedname, 'Xin Yi') !== false;

$topiconeherbs = [
    [
        'name' => 'Ma Huang',
        'latin' => 'Ephedrae Herba',
        'properties' => 'Acrid, slightly bitter; warm',
        'channels' => 'Lung, Bladder',
        'dose' => '3–9 g',
        'identity' => 'The strongest diaphoretic in this group; disseminates Lung Qi, calms wheezing and promotes urination.',
        'clue' => 'Marked chills, no sweating, body aches and wheezing in an Exterior excess presentation.',
        'caution' => 'Use cautiously with spontaneous sweating, Yin deficiency, hypertension, insomnia or cardiovascular disease.',
    ],
    [
        'name' => 'Gui Zhi',
        'latin' => 'Cinnamomi Ramulus',
        'properties' => 'Acrid, sweet; warm',
        'channels' => 'Heart, Lung, Bladder',
        'dose' => '3–9 g',
        'identity' => 'Releases the muscle layer, warms and unblocks the channels, supports Yang and harmonizes Nutritive and Defensive Qi.',
        'clue' => 'Exterior Wind-Cold with sweating, or Cold obstructing the channels and circulation.',
        'caution' => 'Use cautiously with febrile disease, Yin deficiency with Heat, hot Blood or pregnancy with Heat signs.',
    ],
    [
        'name' => 'Zi Su Ye',
        'latin' => 'Perillae Folium',
        'properties' => 'Acrid, aromatic; warm',
        'channels' => 'Lung, Spleen',
        'dose' => '3–9 g',
        'identity' => 'Gently releases Wind-Cold while moving Qi, harmonizing the Middle and calming the fetus.',
        'clue' => 'A mild exterior pattern with chest or epigastric fullness, nausea, vomiting or pregnancy-related Qi constraint.',
        'caution' => 'Its aromatic oils are best preserved with a relatively short decoction.',
    ],
    [
        'name' => 'Jing Jie',
        'latin' => 'Schizonepetae Herba',
        'properties' => 'Acrid; slightly warm',
        'channels' => 'Lung, Liver',
        'dose' => '3–10 g',
        'identity' => 'A light, gentle Wind herb that releases the Exterior, vents rashes and benefits itchy skin eruptions.',
        'clue' => 'Superficial Wind with rash or itching; raw herb disperses Wind, while the charred form is used differently for bleeding.',
        'caution' => 'Do not confuse the actions of raw Jing Jie, Jing Jie Sui and charred Jing Jie.',
    ],
    [
        'name' => 'Fang Feng',
        'latin' => 'Saposhnikoviae Radix',
        'properties' => 'Acrid, sweet; slightly warm',
        'channels' => 'Bladder, Liver, Spleen',
        'dose' => '4.5–9 g',
        'identity' => 'Expels Wind at greater depth than Jing Jie, overcomes Dampness, alleviates pain and addresses spasms.',
        'clue' => 'Wind-Cold-Damp with muscle heaviness or pain, or Wind affecting several levels of the body.',
        'caution' => 'Use cautiously when there is Blood or Yin deficiency without an exterior pathogenic factor.',
    ],
    [
        'name' => 'Qiang Huo',
        'latin' => 'Notopterygii Rhizoma seu Radix',
        'properties' => 'Acrid, bitter; warm',
        'channels' => 'Bladder, Kidney',
        'dose' => '3–9 g',
        'identity' => 'Strongly releases Wind-Cold-Damp and relieves pain, especially along the Taiyang region and upper body.',
        'clue' => 'Occipital headache, stiff upper back and generalized aches aggravated by Cold-Damp.',
        'caution' => 'Drying and dispersing; use cautiously with Yin or Blood deficiency and in patients with depleted fluids.',
    ],
    [
        'name' => 'Bai Zhi',
        'latin' => 'Angelicae Dahuricae Radix',
        'properties' => 'Acrid, aromatic; warm',
        'channels' => 'Lung, Stomach',
        'dose' => '3–9 g',
        'identity' => 'Releases Wind-Cold, opens the nasal passages, relieves Yangming frontal pain and reduces swelling and pus.',
        'clue' => 'Frontal headache with nasal obstruction or thick, foul discharge and sinus fullness.',
        'caution' => 'Its warm, drying nature is unsuitable for headache or nasal disease caused primarily by Yin deficiency or Heat.',
    ],
    [
        'name' => 'Sheng Jiang',
        'latin' => 'Zingiberis Rhizoma Recens',
        'properties' => 'Acrid; slightly warm',
        'channels' => 'Lung, Spleen, Stomach',
        'dose' => '3–10 g',
        'identity' => 'Gently releases Wind-Cold, warms the Middle, stops vomiting, warms the Lung and reduces toxicity of certain herbs.',
        'clue' => 'A mild exterior pattern accompanied by nausea, vomiting, cough or Middle-Jiao Cold.',
        'caution' => 'Use cautiously with spontaneous sweating from deficiency or prominent internal Heat.',
    ],
    [
        'name' => 'Xin Yi Hua',
        'latin' => 'Magnoliae Flos',
        'properties' => 'Acrid, aromatic; warm',
        'channels' => 'Lung, Stomach',
        'dose' => '3–9 g, wrapped',
        'identity' => 'Focuses on opening the nasal passages, dispersing Wind-Cold and restoring the sense of smell.',
        'clue' => 'Nasal obstruction, reduced smell and sinus pain where the nose is the dominant problem.',
        'caution' => 'The flower hairs can irritate the throat; traditionally decocted in a cloth bag.',
    ],
];

$topiconecomparisons = [
    [
        'title' => 'Zi Su Ye vs. Ma Huang',
        'summary' => 'Middle-Jiao Qi, nausea or pregnancy favors Zi Su Ye; strong sweating, wheezing or edema favors Ma Huang.',
        'path' => '/study-guide/comparisons/zi-su-ye-vs-ma-huang.html',
    ],
    [
        'title' => 'Fang Feng vs. Jing Jie',
        'summary' => 'Fang Feng reaches deeper Wind with Dampness; Jing Jie is lighter, more superficial and stronger for rash or itching.',
        'path' => '/study-guide/comparisons/fang-feng-vs-jing-jie.html',
    ],
    [
        'title' => 'Qiang Huo vs. Gao Ben',
        'summary' => 'Both enter the Taiyang–Du region; Qiang Huo emphasizes upper-body Wind-Cold-Damp pain and stiffness.',
        'path' => '/study-guide/comparisons/qiang-huo-vs-gao-ben.html',
    ],
    [
        'title' => 'Xin Yi Hua vs. Hou Po',
        'summary' => 'Magnolia flower with nasal clues indicates Xin Yi Hua; Magnolia bark with fullness and Dampness indicates Hou Po.',
        'path' => '/study-guide/comparisons/xin-yi-hua-vs-hou-po.html',
    ],
];

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
.single-herbs-guide__section{margin-top:28px}.single-herbs-guide__section h4{margin:0 0 7px;color:#123246;font-size:22px}.single-herbs-guide__section-lead{margin:0 0 16px;color:#60717e;line-height:1.5}
.single-herbs-guide__pattern{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin-top:18px}
.single-herbs-guide__pattern article{padding:17px;border-radius:10px;background:#f7fafb;border:1px solid #dce4e8}.single-herbs-guide__pattern strong{display:block;margin-bottom:6px;color:#0f5f58}.single-herbs-guide__pattern span{color:#5b6b79;font-size:14px;line-height:1.45}
.single-herbs-guide__table-shell{overflow-x:auto;border:1px solid #dce4e8;border-radius:10px}
.single-herbs-guide__table{width:100%;min-width:940px;border-collapse:collapse;background:#fff;font-size:14px}.single-herbs-guide__table th,.single-herbs-guide__table td{padding:13px 14px;border-bottom:1px solid #e4eaed;text-align:left;vertical-align:top;line-height:1.45}.single-herbs-guide__table thead th{background:#eaf5f3;color:#123f43;font-size:12px;letter-spacing:.04em;text-transform:uppercase}.single-herbs-guide__table tbody tr:last-child td{border-bottom:0}.single-herbs-guide__table td:first-child strong{display:block;color:#123246;font-size:15px}.single-herbs-guide__table td:first-child small{display:block;margin-top:3px;color:#6b7b87;font-style:italic}.single-herbs-guide__table-caution{color:#7f3f17}
.single-herbs-guide__decision{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px}.single-herbs-guide__decision div{padding:14px;border-left:4px solid #f77e25;border-radius:8px;background:#fff7f0}.single-herbs-guide__decision strong{display:block;margin-bottom:4px}.single-herbs-guide__decision span{color:#68594f;font-size:14px;line-height:1.4}
.single-herbs-guide__comparisons{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.single-herbs-guide__comparison{display:block;padding:17px;border:1px solid #d9e2e7;border-radius:10px;background:#fff;color:inherit;text-decoration:none}.single-herbs-guide__comparison:hover{border-color:#0b817d;color:inherit;text-decoration:none}.single-herbs-guide__comparison strong{display:block;margin-bottom:6px;color:#0d665e}.single-herbs-guide__comparison span{display:block;color:#60717e;font-size:14px;line-height:1.45}.single-herbs-guide__comparison small{display:block;margin-top:10px;color:#185cca;font-weight:800}
.single-herbs-guide__safety{padding:19px 21px;border-left:5px solid #b45309;border-radius:10px;background:#fff7ed;color:#713f12;line-height:1.55}.single-herbs-guide__safety strong{display:block;margin-bottom:6px}
.single-herbs-guide__recall{display:grid;gap:9px}.single-herbs-guide__recall details{border:1px solid #dce4e8;border-radius:9px;background:#fff}.single-herbs-guide__recall summary{cursor:pointer;padding:15px 17px;color:#17364a;font-weight:800}.single-herbs-guide__recall details[open] summary{border-bottom:1px solid #e4eaed;background:#f6fafb}.single-herbs-guide__recall p{margin:0;padding:15px 17px;color:#52677a;line-height:1.5}.single-herbs-guide__source{font-size:13px}
@media(max-width:850px){.single-herbs-guide__pattern,.single-herbs-guide__decision{grid-template-columns:1fr 1fr}}
@media(max-width:720px){.single-herbs-guide__top,.single-herbs-guide__workspace-header{display:block}.single-herbs-guide__actions{justify-content:flex-start;margin-top:16px}.single-herbs-guide__overview{grid-template-columns:1fr}.single-herbs-guide__count{align-items:flex-start}.single-herbs-guide__plan,.single-herbs-guide__pattern,.single-herbs-guide__decision,.single-herbs-guide__comparisons{grid-template-columns:1fr}}
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
                <span class="single-herbs-guide__status"><?php echo $istopicone ? 'EXAMPLE READY' : 'STRUCTURE READY'; ?></span>
            </div>
            <?php if ($istopicone): ?>
                <section aria-labelledby="topic-one-foundations">
                    <h4 id="topic-one-foundations">Category foundations</h4>
                    <p class="single-herbs-guide__section-lead">Warm, acrid Exterior-releasing herbs disperse Wind-Cold, restore the Lung's dispersing function and open the superficial layer. The exam task is to identify each herb's particular emphasis rather than treating the group as interchangeable.</p>
                    <div class="single-herbs-guide__pattern">
                        <article><strong>Core pattern</strong><span>Aversion to cold, fever, headache or body aches, nasal obstruction, cough, thin white tongue coating and a floating pulse.</span></article>
                        <article><strong>Strength spectrum</strong><span>Ma Huang and Gui Zhi are stronger; Jing Jie, Fang Feng, Zi Su Ye and Sheng Jiang are generally gentler and more selective.</span></article>
                        <article><strong>Board distinction</strong><span>Look for the decisive secondary clue: wheezing, sweating, nausea, rash, Damp pain, headache location or nasal obstruction.</span></article>
                    </div>
                </section>

                <section class="single-herbs-guide__section" aria-labelledby="topic-one-decision-title">
                    <h4 id="topic-one-decision-title">Fast decision map</h4>
                    <p class="single-herbs-guide__section-lead">Begin with the dominant clue, then confirm the properties and channels.</p>
                    <div class="single-herbs-guide__decision">
                        <div><strong>No sweat + wheezing</strong><span>Ma Huang</span></div>
                        <div><strong>Sweating + channel Cold</strong><span>Gui Zhi</span></div>
                        <div><strong>Nausea or pregnancy Qi</strong><span>Zi Su Ye</span></div>
                        <div><strong>Rash or superficial itching</strong><span>Jing Jie</span></div>
                        <div><strong>Wind-Damp muscle pain</strong><span>Fang Feng</span></div>
                        <div><strong>Occiput and upper back</strong><span>Qiang Huo</span></div>
                        <div><strong>Frontal sinus pain or pus</strong><span>Bai Zhi</span></div>
                        <div><strong>Vomiting + mild exterior</strong><span>Sheng Jiang</span></div>
                        <div><strong>Nasal obstruction dominates</strong><span>Xin Yi Hua</span></div>
                    </div>
                </section>

                <section class="single-herbs-guide__section" aria-labelledby="topic-one-herbs-title">
                    <h4 id="topic-one-herbs-title">Core herb matrix</h4>
                    <p class="single-herbs-guide__section-lead">A compact reference for recognition and differentiation. Dosage ranges are study references, not prescribing instructions.</p>
                    <div class="single-herbs-guide__table-shell" role="region" aria-label="Warm acrid herbs comparison table" tabindex="0">
                        <table class="single-herbs-guide__table">
                            <thead><tr><th>Herb</th><th>Properties · channels · dose</th><th>Core identity</th><th>Decisive clue</th><th>Safety / preparation</th></tr></thead>
                            <tbody>
                                <?php foreach ($topiconeherbs as $herb): ?>
                                    <tr>
                                        <td><strong><?php echo s($herb['name']); ?></strong><small><?php echo s($herb['latin']); ?></small></td>
                                        <td><?php echo s($herb['properties']); ?><br><?php echo s($herb['channels']); ?><br><strong><?php echo s($herb['dose']); ?></strong></td>
                                        <td><?php echo s($herb['identity']); ?></td>
                                        <td><?php echo s($herb['clue']); ?></td>
                                        <td class="single-herbs-guide__table-caution"><?php echo s($herb['caution']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="single-herbs-guide__section" aria-labelledby="topic-one-comparisons-title">
                    <h4 id="topic-one-comparisons-title">High-yield comparisons</h4>
                    <p class="single-herbs-guide__section-lead">Open the complete comparison when two choices appear plausible.</p>
                    <div class="single-herbs-guide__comparisons">
                        <?php foreach ($topiconecomparisons as $comparison): ?>
                            <?php $comparisonurl = new moodle_url($comparison['path']); ?>
                            <a class="single-herbs-guide__comparison" href="<?php echo $comparisonurl->out(false); ?>">
                                <strong><?php echo s($comparison['title']); ?></strong>
                                <span><?php echo s($comparison['summary']); ?></span>
                                <small>Open full comparison →</small>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="single-herbs-guide__section" aria-labelledby="topic-one-safety-title">
                    <h4 id="topic-one-safety-title">Safety checkpoint</h4>
                    <div class="single-herbs-guide__safety"><strong>Exterior-releasing herbs are dispersing and should not be treated as routine home remedies.</strong>Strong or prolonged diaphoresis can consume Qi and fluids. Extra caution is required with deficiency sweating, pregnancy, bleeding tendencies, Heat presentations and cardiovascular disease. Ma Huang has important cardiovascular and regulatory considerations; professional supervision and current local rules are essential.</div>
                </section>

                <section class="single-herbs-guide__section" aria-labelledby="topic-one-recall-title">
                    <h4 id="topic-one-recall-title">Quick recall</h4>
                    <p class="single-herbs-guide__section-lead">Open each item only after choosing an answer mentally.</p>
                    <div class="single-herbs-guide__recall">
                        <details><summary>Which herb best matches severe Wind-Cold with no sweating and wheezing?</summary><p><strong>Ma Huang.</strong> Its forceful diaphoresis and Lung-Qi dissemination distinguish it from the gentler herbs.</p></details>
                        <details><summary>Which herb is favored when nausea or Qi constraint in pregnancy accompanies a mild exterior pattern?</summary><p><strong>Zi Su Ye.</strong> It releases Wind-Cold while moving Qi, harmonizing the Middle and calming the fetus.</p></details>
                        <details><summary>Which herb is most associated with frontal headache, nasal obstruction and thick or foul sinus discharge?</summary><p><strong>Bai Zhi.</strong> It enters the Lung and Stomach, reaches the Yangming forehead and transforms turbidity and pus.</p></details>
                        <details><summary>How do Jing Jie and Fang Feng differ?</summary><p><strong>Jing Jie</strong> is lighter and more superficial, especially useful for rash and itching. <strong>Fang Feng</strong> reaches deeper Wind, addresses Dampness and is stronger for muscle pain or heaviness.</p></details>
                        <details><summary>Which herb points to occipital headache and stiff upper back from Wind-Cold-Damp?</summary><p><strong>Qiang Huo.</strong> Its Taiyang and upper-body emphasis is the decisive clue.</p></details>
                    </div>
                </section>

                <p class="single-herbs-guide__note single-herbs-guide__source"><strong>Sources used for this prototype:</strong> Bensky, Clavey, Stöger and Gamble, <em>Chinese Herbal Medicine: Materia Medica</em>, 3rd ed., category and individual monographs; and Yang, <em>Chinese Herbal Medicines: Comparisons and Characteristics</em>, 2nd ed., Chapter 2. This is educational review material and not prescribing guidance.</p>
            <?php else: ?>
                <ol class="single-herbs-guide__plan">
                    <li><strong>Category foundations</strong><span>Definition, therapeutic strategy and the distinctions that define this group.</span></li>
                    <li><strong>Core herbs</strong><span>Names, properties, channels, dosage ranges and the most testable identifiers.</span></li>
                    <li><strong>Functions and indications</strong><span>Primary actions, clinical presentations and symptom-pattern relationships.</span></li>
                    <li><strong>Comparisons</strong><span>Differentiate commonly confused herbs and recognize the best choice in context.</span></li>
                    <li><strong>Safety</strong><span>Contraindications, cautions, toxicity, preparation and clinically relevant interactions.</span></li>
                    <li><strong>Recall and Board review</strong><span>Flashcards, image recognition and category-focused multiple-choice review.</span></li>
                </ol>
                <p class="single-herbs-guide__note"><strong>Content stage:</strong> this category framework is ready. Verified educational content will be developed from the supplied Bensky materia medica without changing the Original Bank.</p>
            <?php endif; ?>
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
