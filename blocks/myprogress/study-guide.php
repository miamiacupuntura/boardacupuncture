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
        'description' => 'Study Guide chapters for Single Herbs, Duì Yào and Formulas.',
        'chapters' => [
            [
                'title' => 'Single Herbs',
                'description' => 'Study single herbs through the same General, Topics 1–34 and Mixed Review categories used in the Chinese Herbology Board Level workspace.',
                'path' => '/blocks/myprogress/single-herbs-study-guide.php',
                'anchor' => '',
            ],
            [
                'title' => 'Duì Yào Pairs',
                'description' => 'Master 51 official herb pairs through 15 clinical blocks. This Domain I resource emphasizes combined actions, individual roles, decisive clues, formulas and safety.',
                'path' => '/study-guide/comparisons/dui-yao.html',
                'anchor' => '',
                'status' => 'DOMAIN I · HIGH PRIORITY',
                'priority' => true,
            ],
            [
                'title' => 'Formulas',
                'description' => 'Study 63 core formulas through 14 treatment categories with patterns, actions, decisive clues, essential ingredients and safety review.',
                'path' => '/blocks/myprogress/formulas-study-guide.php',
                'anchor' => '',
                'status' => 'AVAILABLE · 63 FORMULAS',
            ],
            [
                'title' => 'Herbal Comparisons Question Bank',
                'description' => 'Practice 249 comparative herb questions in book order with recall flashcards and 117 strength-and-temperature charts.',
                'path' => '/study-guide/comparisons/herb-comparison-bank.html',
                'anchor' => '',
                'status' => 'AVAILABLE · 249 QUESTIONS',
                'button_label' => 'Open question bank →',
                'practice_exam' => true,
            ],
            [
                'title' => 'Formulas Practice Exams',
                'description' => 'Find and study 63 core formulas, then build focused sessions from 572 connected practice questions.',
                'path' => '/study-guide/comparisons/formula-navigator.html',
                'anchor' => '',
                'status' => 'AVAILABLE · CONNECTED STUDY',
                'practice_exam' => true,
            ],
            [
                'title' => 'Formula Families / Variations',
                'description' => 'Study 22 formula families and their named variations. Each card shows the root formula, structural change, resulting pattern and the decisive clue for choosing each branch.',
                'path' => '/study-guide/comparisons/families.html',
                'anchor' => '',
                'status' => 'AVAILABLE · 22 FAMILIES',
                'secondary' => true,
            ],
            [
                'title' => 'Name Traps',
                'description' => 'Separate nearly identical herb names by characters, identity, function and the safety alert that must not be missed.',
                'path' => '/study-guide/comparisons/name-traps.html',
                'anchor' => '',
                'status' => 'AVAILABLE · 22 NAME TRAPS',
                'secondary' => true,
            ],
            [
                'title' => 'Herb Families & Parts',
                'description' => 'Learn how leaf, twig, bark, root, seed and fruit within an herb family change direction, function and safety.',
                'path' => '/study-guide/comparisons/herb-families.html',
                'anchor' => '',
                'status' => 'AVAILABLE · 20 FAMILIES',
                'secondary' => true,
            ],
            [
                'title' => 'Safety Library',
                'description' => 'Review preparation, toxicity, interactions and regulation while studying herbs, pairs and formulas.',
                'path' => '/study-guide/comparisons/safety.html',
                'anchor' => '',
                'status' => 'AVAILABLE · 12 SAFETY SECTIONS',
                'secondary' => true,
            ],
            [
                'title' => 'Formula Name Traps',
                'description' => 'Separate formulas with nearly identical names by architecture, mechanism and the clinical clue that changes the answer.',
                'path' => '/study-guide/comparisons/formula-name-traps.html',
                'anchor' => '',
                'status' => 'AVAILABLE · 22 NAME TRAPS',
                'secondary' => true,
            ],
            [
                'title' => 'Misleading Formula Names',
                'description' => 'Learn formulas whose historical, poetic or shared name does not literally describe the modern clinical strategy.',
                'path' => '/study-guide/comparisons/misleading-formula-names.html',
                'anchor' => '',
                'status' => 'AVAILABLE · 6 FORMULA NAMES',
                'secondary' => true,
            ],
            [
                'title' => 'Advanced Name Traps',
                'description' => 'Separate pinyin groups, geographic prefixes and Western common names that hide different species.',
                'path' => '/study-guide/comparisons/advanced-name-traps.html',
                'anchor' => '',
                'status' => 'AVAILABLE · 8 NAME TRAPS',
                'secondary' => true,
            ],
            [
                'title' => 'Supplemental Herb Families',
                'description' => 'Review six additional plant, medicinal-part and related-herb groups that are commonly confused.',
                'path' => '/study-guide/comparisons/supplemental-herb-families.html',
                'anchor' => '',
                'status' => 'AVAILABLE · 6 FAMILIES',
                'secondary' => true,
            ],
            [
                'title' => 'Patterns Tips Maciocia',
                'description' => 'Review signs, tongue, pulse and representative formulas through 100 organ-pattern clinical tips.',
                'path' => '/study-guide/comparisons/patterns-tips-maciocia.html',
                'anchor' => '',
                'status' => 'AVAILABLE · 100 CLINICAL TIPS',
                'secondary' => true,
            ],
            [
                'title' => 'Most Important Formulas',
                'description' => 'Review 85 formulas by pattern, action, decisive clue, contrast, memory cue and safety.',
                'path' => '/study-guide/comparisons/most-important-formulas.html',
                'anchor' => '',
                'status' => 'AVAILABLE · 85 FORMULAS',
                'secondary' => true,
            ],
            [
                'title' => 'Formula Composition',
                'description' => 'Learn herb relationships, Jun–Chen–Zuo–Shi hierarchy, dose architecture, preparation and an exam method.',
                'path' => '/study-guide/comparisons/formula-theory.html',
                'anchor' => '',
                'status' => 'AVAILABLE · 12 THEORY TOPICS',
                'secondary' => true,
            ],
            [
                'title' => 'Herbal Incompatibilities',
                'description' => 'Master the seven herb relationships, Eighteen Incompatibilities, Nineteen Antagonisms and their decisive exam clues.',
                'path' => '/study-guide/comparisons/herbal-incompatibilities.html',
                'anchor' => '',
                'status' => 'AVAILABLE · 10 SAFETY CHAPTERS',
                'secondary' => true,
            ],
            [
                'title' => 'Secondary Actions',
                'description' => 'Study 60 secondary herb functions while keeping the primary category of each medicinal in view.',
                'path' => '/study-guide/comparisons/secondary-actions.html',
                'anchor' => '',
                'status' => 'AVAILABLE · 60 ACTIONS',
                'secondary' => true,
            ],
            [
                'title' => 'Formula Comparisons',
                'description' => 'Review 110 focused comparisons by treatment strategy, pattern, actions, ingredients and the decisive clues that distinguish similar formulas.',
                'path' => '/study-guide/comparisons/formulas.html',
                'anchor' => '',
                'status' => 'AVAILABLE · 110 COMPARISONS',
                'comparison' => true,
            ],
            [
                'title' => 'Single Herbs Comparisons',
                'description' => 'Review 247 comparative study notes that distinguish similar herbs by actions, indications, channels and decisive clinical clues.',
                'path' => '/study-guide/comparisons/index.html',
                'anchor' => '',
                'status' => 'AVAILABLE · 247 COMPARISONS',
                'comparison' => true,
            ],
            [
                'title' => 'Pathology Comparisons',
                'description' => 'Review 66 clinical comparisons organized by pathology, symptom location and the clues that guide herb or formula selection.',
                'path' => '/study-guide/comparisons/pathologies.html',
                'anchor' => '',
                'status' => 'AVAILABLE · 66 COMPARISONS',
                'comparison' => true,
            ],
            [
                'title' => 'Pattern Comparisons',
                'description' => 'Review 50 pattern comparisons that separate similar Chinese medicine presentations by mechanism, signs, tongue and pulse.',
                'path' => '/study-guide/comparisons/patterns.html',
                'anchor' => '',
                'status' => 'AVAILABLE · 50 COMPARISONS',
                'comparison' => true,
            ],
            [
                'title' => 'Formula Modifications',
                'description' => 'Review 305 clinical modifications as reference cards organized by formula, presentation and the corresponding ingredient change.',
                'path' => '/study-guide/comparisons/formula-modifications.html',
                'anchor' => '',
                'status' => 'AVAILABLE · 305 MODIFICATIONS',
                'secondary' => true,
            ],
            [
                'title' => 'Formula Modification Practice Exams',
                'description' => 'Test the 305 formula modifications in focused question sessions after reviewing the reference cards.',
                'path' => '/study-guide/comparisons/formula-practice.html',
                'params' => [
                    'category' => 'modifications',
                    'size' => 25,
                ],
                'anchor' => '',
                'status' => 'AVAILABLE · 305 QUESTIONS',
                'practice_exam' => true,
            ],
            [
                'title' => 'Commonly Used Herbal Combinations',
                'description' => 'Study 70 Appendix 2 combinations by relationship, clinical application and the role of each herb. This chapter remains separate from the 51 official Duì Yào pairs.',
                'path' => '/blocks/myprogress/common-herbal-combinations.php',
                'anchor' => '',
                'status' => 'AVAILABLE · APPENDIX 2',
                'secondary' => true,
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
$primarychapters = array_values(array_filter($area['chapters'], static function(array $chapter): bool {
    return empty($chapter['secondary']) && empty($chapter['practice_exam']) && empty($chapter['comparison']);
}));
$practicechapters = array_values(array_filter($area['chapters'], static function(array $chapter): bool {
    return !empty($chapter['practice_exam']);
}));
$comparisonchapters = array_values(array_filter($area['chapters'], static function(array $chapter): bool {
    return !empty($chapter['comparison']);
}));
$otherchapters = array_values(array_filter($area['chapters'], static function(array $chapter): bool {
    return !empty($chapter['secondary']) && empty($chapter['practice_exam']) && empty($chapter['comparison']);
}));
$showconstruction = optional_param('view', '', PARAM_ALPHA) === 'construction';
$showlibrary = optional_param('view', '', PARAM_ALPHA) === 'library';
$librarygroups = [
    [
        'title' => 'Main Study Guides',
        'description' => 'Open the principal learning resources.',
        'modifier' => 'primary',
        'chapters' => $primarychapters,
    ],
    [
        'title' => 'Practice Exams',
        'description' => 'Open question banks and focused practice sessions.',
        'modifier' => 'practice',
        'chapters' => $practicechapters,
    ],
    [
        'title' => 'Comparisons',
        'description' => 'Open the comparative study collections.',
        'modifier' => 'comparisons',
        'chapters' => $comparisonchapters,
    ],
    [
        'title' => 'Other chapters',
        'description' => 'Open families, safety, theory and supplemental study resources.',
        'modifier' => 'other',
        'chapters' => $otherchapters,
    ],
];

$pageurl = new moodle_url('/blocks/myprogress/study-guide.php', ['area' => $areaid]);
$chapterlisturl = new moodle_url('/blocks/myprogress/study-guide.php', ['area' => $areaid]);
$libraryurl = new moodle_url('/blocks/myprogress/study-guide.php', [
    'area' => $areaid,
    'view' => 'library',
]);
if ($showconstruction) {
    $pageurl->param('view', 'construction');
} elseif ($showlibrary) {
    $pageurl->param('view', 'library');
}
$currentguidelineurl = null;
if ($areaid === 'herbology') {
    $currentguidelineurl = new moodle_url('/study-guide/guidelines/chinese-herbal-medicine.html', [
        'return' => $pageurl->out(false),
    ]);
}

$PAGE->set_url($pageurl);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$pagelabel = $showlibrary ? ' Library' : ' Study Guide';
$PAGE->set_title($area['title'] . $pagelabel);
$PAGE->set_heading($area['title'] . $pagelabel);

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
.subject-study-guide__board-overview{margin:0 0 18px;padding:34px 40px;border-top:5px solid #0b817d;border-radius:13px;background:linear-gradient(135deg,#f8fcfb 0%,#fbf8fd 100%);box-shadow:0 3px 12px rgba(15,23,42,.05)}
.subject-study-guide__board-overview .subject-study-guide__eyebrow{margin-bottom:8px;color:#08746f}
.subject-study-guide__board-overview h3{margin:0 0 10px;color:#123246;font-size:clamp(28px,4vw,42px);line-height:1.12}
.subject-study-guide__board-overview p:last-child{max-width:760px;margin:0;color:#52677a;line-height:1.55}
.subject-study-guide__benefits{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin:0 0 26px}
.subject-study-guide__benefit{padding:18px;border:1px solid #cfdae0;border-radius:10px;background:#fff}
.subject-study-guide__benefit h3{margin:0 0 8px;color:#17232a;font-size:16px}
.subject-study-guide__benefit p{margin:0;color:#52677a;font-size:14px;line-height:1.5}
.subject-study-guide__grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:18px}
.subject-study-guide__grid--herbology{grid-template-columns:repeat(auto-fit,minmax(235px,1fr))}
.subject-study-guide__card{display:flex;min-height:220px;flex-direction:column;padding:22px;border:1px solid #dce2e6;border-top:5px solid #f77e25;border-radius:12px;background:#fff;box-shadow:0 3px 12px rgba(15,23,42,.06)}
.subject-study-guide__card--priority{border-top-color:#0b817d;background:linear-gradient(180deg,#f7fffd 0%,#fff 42%)}
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
.subject-study-guide__other{grid-column:1/-1;padding:22px;border:1px solid #d8e1e7;border-top:4px solid #94a3b8;border-radius:12px;background:#f8fafc}
.subject-study-guide__other-header{margin-bottom:18px;text-align:center}
.subject-study-guide__other-header .subject-study-guide__status{display:inline-block;margin-bottom:9px}
.subject-study-guide__other-header h3{margin:0 0 7px;color:#17232a;font-size:23px}
.subject-study-guide__other-header p{margin:0;color:#64748b;line-height:1.5}
.subject-study-guide__other-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:18px}
.subject-study-guide__other .subject-study-guide__card{min-height:200px;text-align:left}
.subject-study-guide__practice{border-top-color:#0b817d;background:#f5fbfa}
.subject-study-guide__practice .subject-study-guide__status{background:#dff4ee;color:#0b675b}
.subject-study-guide__comparisons{border-top-color:#f77e25;background:#fffaf6}
.subject-study-guide__comparisons .subject-study-guide__status{background:#fff0df;color:#9a4300}
.subject-study-library{display:grid;gap:18px}
.subject-study-library__intro{padding:30px;border-top:5px solid #0b817d;border-radius:13px;background:linear-gradient(135deg,#f5fbfa 0%,#fffaf6 100%);box-shadow:0 3px 12px rgba(15,23,42,.05)}
.subject-study-library__intro .subject-study-guide__eyebrow{color:#08746f}
.subject-study-library__intro h3{margin:0 0 8px;color:#123246;font-size:clamp(27px,4vw,40px)}
.subject-study-library__intro p:last-child{max-width:760px;margin:0;color:#52677a;line-height:1.55}
.subject-study-library__group{padding:22px;border:1px solid #d8e1e7;border-radius:12px;background:#fff}
.subject-study-library__group-header{display:flex;align-items:flex-end;justify-content:space-between;gap:14px;margin-bottom:15px}
.subject-study-library__group-header h3{margin:0 0 4px;color:#17232a;font-size:21px}
.subject-study-library__group-header p{margin:0;color:#64748b}
.subject-study-library__count{flex:0 0 auto;color:#52677a;font-size:13px;font-weight:800}
.subject-study-library__buttons{display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:10px}
.subject-study-library__button{display:flex;min-height:48px;align-items:center;justify-content:center;padding:10px 14px;border:1px solid #b8c8d5;border-radius:9px;background:#fff;color:#123246;font-weight:800;text-align:center;text-decoration:none}
.subject-study-library__button:hover{border-color:#2563eb;background:#eff6ff;color:#1746a2;text-decoration:none}
.subject-study-library__group--primary{border-top:4px solid #2563eb}
.subject-study-library__group--practice{border-top:4px solid #0b817d}
.subject-study-library__group--comparisons{border-top:4px solid #f77e25}
.subject-study-library__group--other{border-top:4px solid #94a3b8}
.subject-study-library__group--practice .subject-study-library__button{border-color:#8bc8bd;background:#f4fbf9}
.subject-study-library__group--comparisons .subject-study-library__button{border-color:#f4b27e;background:#fffaf6}
.subject-study-guide__construction{padding:clamp(36px,8vw,76px) 22px;border:1px dashed #aeb8c0;border-radius:14px;background:#fff;text-align:center}
.subject-study-guide__construction-mark{margin-bottom:15px;font-size:48px}
.subject-study-guide__construction h3{margin:0 0 12px;color:#a64f12;font-size:28px}
.subject-study-guide__construction p{max-width:570px;margin:0 auto 26px;color:#64748b;line-height:1.65}
.subject-study-guide a:focus-visible{outline:3px solid #185cca;outline-offset:3px}
@media(max-width:700px){.subject-study-guide__top{display:block}.subject-study-guide__top-actions{align-items:stretch;flex-direction:column;margin-top:16px}.subject-study-guide__top-actions a{width:100%;box-sizing:border-box}.subject-study-guide__back{margin-top:0}.subject-study-guide__board-overview{padding:26px 22px}.subject-study-guide__benefits{grid-template-columns:1fr}.subject-study-guide__grid,.subject-study-guide__grid--herbology{grid-template-columns:1fr}.subject-study-guide__future{grid-column:auto}.subject-study-library__intro{padding:24px 20px}.subject-study-library__group-header{align-items:flex-start;flex-direction:column}.subject-study-library__buttons{grid-template-columns:1fr}}
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
            <p class="subject-study-guide__eyebrow"><?php echo $showlibrary ? 'Study Library' : 'Study Guide'; ?></p>
            <h2><?php echo s($area['title'] . ($showlibrary ? ' Library' : '')); ?></h2>
            <p class="subject-study-guide__intro"><?php echo $showlibrary ? 'Open every Study Guide resource from one quick-access library.' : s($area['description']); ?></p>
        </div>
        <nav class="subject-study-guide__top-actions" aria-label="Study Guide course catalogs">
            <?php if ($showlibrary): ?>
                <a class="subject-study-guide__back" href="<?php echo $chapterlisturl->out(false); ?>">← Study Guide</a>
            <?php elseif ($areaid === 'herbology'): ?>
                <a class="subject-study-guide__back" href="<?php echo $libraryurl->out(false); ?>">Library →</a>
            <?php endif; ?>
            <?php if ($currentguidelineurl !== null): ?>
                <a class="subject-study-guide__back" href="<?php echo $currentguidelineurl->out(false); ?>">2026 Board Guideline ↔</a>
            <?php endif; ?>
            <a class="subject-study-guide__back" href="<?php echo (new moodle_url('/my/'))->out(false); ?>">← Back to Dashboard</a>
        </nav>
    </header>

    <?php if ($showlibrary): ?>
        <section class="subject-study-library" aria-labelledby="study-library-title">
            <header class="subject-study-library__intro">
                <p class="subject-study-guide__eyebrow">Quick access</p>
                <h3 id="study-library-title"><?php echo s($area['title']); ?> Library</h3>
                <p>Everything available in the Study Guide is listed below as a direct button. New classified resources will appear here automatically.</p>
            </header>
            <?php foreach ($librarygroups as $group): ?>
                <?php if (!empty($group['chapters'])): ?>
                    <section class="subject-study-library__group subject-study-library__group--<?php echo s($group['modifier']); ?>">
                        <header class="subject-study-library__group-header">
                            <div>
                                <h3><?php echo s($group['title']); ?></h3>
                                <p><?php echo s($group['description']); ?></p>
                            </div>
                            <span class="subject-study-library__count"><?php echo count($group['chapters']); ?> resources</span>
                        </header>
                        <nav class="subject-study-library__buttons" aria-label="<?php echo s($group['title']); ?>">
                            <?php foreach ($group['chapters'] as $chapter): ?>
                                <?php
                                $chapterurl = new moodle_url($chapter['path'], $chapter['params'] ?? []);
                                if ($chapter['anchor'] !== '') {
                                    $chapterurl->set_anchor($chapter['anchor']);
                                }
                                ?>
                                <a class="subject-study-library__button" href="<?php echo $chapterurl->out(false); ?>" title="<?php echo s($chapter['description']); ?>"><?php echo s($chapter['title']); ?></a>
                            <?php endforeach; ?>
                        </nav>
                    </section>
                <?php endif; ?>
            <?php endforeach; ?>
        </section>
    <?php else: ?>
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

    <section id="study-guide-chapters" class="subject-study-guide__grid<?php echo $areaid === 'herbology' ? ' subject-study-guide__grid--herbology' : ''; ?>" aria-label="<?php echo s($area['title']); ?> Study Guide chapters">
        <?php foreach ($primarychapters as $chapter): ?>
            <?php
            $chapterurl = new moodle_url($chapter['path'], $chapter['params'] ?? []);
            if ($chapter['anchor'] !== '') {
                $chapterurl->set_anchor($chapter['anchor']);
            }
            ?>
            <article class="subject-study-guide__card<?php echo !empty($chapter['priority']) ? ' subject-study-guide__card--priority' : ''; ?>">
                <span class="subject-study-guide__status"><?php echo s($chapter['status'] ?? 'AVAILABLE'); ?></span>
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

        <?php if (!empty($practicechapters)): ?>
            <section class="subject-study-guide__other subject-study-guide__practice" aria-labelledby="practice-exams-title">
                <header class="subject-study-guide__other-header">
                    <span class="subject-study-guide__status">PRACTICE</span>
                    <h3 id="practice-exams-title">Practice Exams</h3>
                    <p>Practice herb comparisons, principal formulas and their clinical modifications.</p>
                </header>
                <div class="subject-study-guide__other-grid">
                    <?php foreach ($practicechapters as $chapter): ?>
                        <?php
                        $chapterurl = new moodle_url($chapter['path'], $chapter['params'] ?? []);
                        if ($chapter['anchor'] !== '') {
                            $chapterurl->set_anchor($chapter['anchor']);
                        }
                        ?>
                        <article class="subject-study-guide__card">
                            <span class="subject-study-guide__status"><?php echo s($chapter['status'] ?? 'AVAILABLE'); ?></span>
                            <h3><?php echo s($chapter['title']); ?></h3>
                            <p><?php echo s($chapter['description']); ?></p>
                            <a class="subject-study-guide__open" href="<?php echo $chapterurl->out(false); ?>"><?php echo s($chapter['button_label'] ?? 'Open Study Guide →'); ?></a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!empty($comparisonchapters)): ?>
            <section class="subject-study-guide__other subject-study-guide__comparisons" aria-labelledby="comparisons-title">
                <header class="subject-study-guide__other-header">
                    <span class="subject-study-guide__status">COMPARATIVE STUDY</span>
                    <h3 id="comparisons-title">Comparisons</h3>
                    <p>Compare single herbs, formulas, pathologies and Chinese medicine patterns.</p>
                </header>
                <div class="subject-study-guide__other-grid">
                    <?php foreach ($comparisonchapters as $chapter): ?>
                        <?php
                        $chapterurl = new moodle_url($chapter['path'], $chapter['params'] ?? []);
                        if ($chapter['anchor'] !== '') {
                            $chapterurl->set_anchor($chapter['anchor']);
                        }
                        ?>
                        <article class="subject-study-guide__card">
                            <span class="subject-study-guide__status"><?php echo s($chapter['status'] ?? 'AVAILABLE'); ?></span>
                            <h3><?php echo s($chapter['title']); ?></h3>
                            <p><?php echo s($chapter['description']); ?></p>
                            <a class="subject-study-guide__open" href="<?php echo $chapterurl->out(false); ?>">Open Study Guide →</a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!empty($otherchapters)): ?>
            <section class="subject-study-guide__other" aria-labelledby="other-chapters-title">
                <header class="subject-study-guide__other-header">
                    <span class="subject-study-guide__status">ADDITIONAL STUDY</span>
                    <h3 id="other-chapters-title">Other chapters</h3>
                    <p>Additional source-based chapters that complement the principal study guides.</p>
                </header>
                <div class="subject-study-guide__other-grid">
                    <?php foreach ($otherchapters as $chapter): ?>
                        <?php
                        $chapterurl = new moodle_url($chapter['path'], $chapter['params'] ?? []);
                        if ($chapter['anchor'] !== '') {
                            $chapterurl->set_anchor($chapter['anchor']);
                        }
                        ?>
                        <article class="subject-study-guide__card">
                            <span class="subject-study-guide__status"><?php echo s($chapter['status'] ?? 'AVAILABLE'); ?></span>
                            <h3><?php echo s($chapter['title']); ?></h3>
                            <p><?php echo s($chapter['description']); ?></p>
                            <a class="subject-study-guide__open" href="<?php echo $chapterurl->out(false); ?>">Open Study Guide →</a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php else: ?>
            <article class="subject-study-guide__card subject-study-guide__future">
                <span class="subject-study-guide__status">COMING SOON</span>
                <h3><?php echo empty($primarychapters) ? 'Study Guide chapters' : 'More chapters'; ?></h3>
                <p>New chapters will be added here as their Study Guides are completed.</p>
                <a class="subject-study-guide__open" href="<?php echo (new moodle_url('/blocks/myprogress/study-guide.php', ['area' => $areaid, 'view' => 'construction']))->out(false); ?>">View status</a>
            </article>
        <?php endif; ?>
    </section>
    <?php endif; ?>
<?php endif; ?>
</main>
<?php
echo $OUTPUT->footer();
