<?php
// Chinese Herbology Board Level workspace.

require_once(__DIR__ . '/../../config.php');

require_login();

$track = optional_param('track', 'herbs', PARAM_ALPHA);
if (!in_array($track, ['herbs', 'formulas'], true)) {
    $track = 'herbs';
}
$sectionkey = optional_param('section', '', PARAM_ALPHANUMEXT);

$membershipcourseid = 116;
$membershipcourse = $DB->get_record('course', ['id' => $membershipcourseid], '*', MUST_EXIST);
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

$pageparams = ['track' => $track];
if ($sectionkey !== '') {
    $pageparams['section'] = $sectionkey;
}
$pageurl = new moodle_url('/blocks/menu/herbology-board.php', $pageparams);
$dashboardurl = new moodle_url('/my/');
$singleherbsurl = new moodle_url('/blocks/menu/herbology-board.php', ['track' => 'herbs']);
$formulasurl = new moodle_url('/blocks/menu/herbology-board.php', ['track' => 'formulas']);

$PAGE->set_url($pageurl);
$PAGE->set_context($membershipcontext);
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Chinese Herbology Board Level');
$PAGE->set_heading('Chinese Herbology Board Level');

$topics = [];
$clearheatsections = [
    'drain-fire' => [
        'number' => '2.1',
        'name' => 'Clear Heat and Drain Fire',
        'focus' => 'Qi-level excess heat, especially in the Lung and Stomach.',
        'distinction' => 'Choose by site and severity: Shi Gao is strongest for blazing Lung/Stomach heat; Zhi Mu adds yin nourishment and moistening; Lu Gen is milder and generates fluids.',
        'coreherbs' => [
            ['name' => 'Shí Gāo', 'pharmaceutical' => 'Gypsum fibrosum', 'use' => 'Strongly clears qi-level heat from the Lung and Stomach.'],
            ['name' => 'Zhī Mǔ', 'pharmaceutical' => 'Anemarrhenae Rhizoma', 'use' => 'Drains fire while nourishing yin and moistening dryness.'],
            ['name' => 'Lú Gēn', 'pharmaceutical' => 'Phragmitis Rhizoma', 'use' => 'Clears Lung/Stomach heat and generates fluids.'],
            ['name' => 'Zhī Zǐ', 'pharmaceutical' => 'Gardeniae Fructus', 'use' => 'Drains fire and also addresses damp-heat.'],
            ['name' => 'Dàn Zhú Yè', 'pharmaceutical' => 'Lophatheri Herba', 'use' => 'Clears Heart/Stomach heat and promotes urination.'],
        ],
        'warnings' => [
            'These cold substances can aggravate Spleen/Stomach cold or deficiency; pattern differentiation remains essential.',
            'High fever, delirium, coma or convulsions require urgent biomedical assessment and are not self-treatment indications.',
        ],
        'annex' => [
            'comparison' => 'Shi Gao emphasizes forceful heat clearing, Zhi Mu combines cooling with yin support, and Lu Gen emphasizes fluids and Lung involvement. Gardeniae Fructus is milder and additionally drains damp-heat; Lophatheri Herba emphasizes Heart and Stomach fire.',
            'preparation' => 'Mineral substances and aromatic or fresh materials may require different preparation methods. Verify preparation and dosage in the individual monograph and local pharmacopeia.',
            'nomenclature' => 'Names are presented as pinyin plus pharmaceutical name to reduce ambiguity across regional common names.',
            'supplemental' => [
                'Hán Shuǐ Shí, Lián Zǐ Xīn, Xī Guā and related summerheat materials remain supplemental within the present five-part structure.',
                'Eye-focused materials in the source summary include Jué Míng Zǐ, Gǔ Jīng Cǎo, Mì Méng Huā, Qīng Xiāng Zǐ, Xià Kū Cǎo and Yè Míng Shā.',
            ],
        ],
    ],
    'cool-blood' => [
        'number' => '2.2',
        'name' => 'Clear Heat and Cool the Blood',
        'focus' => 'Nutritive- or blood-level heat, bleeding and heat injury to yin.',
        'distinction' => 'Shui Niu Jiao leads for intense blood-level heat; Sheng Di Huang and Xuan Shen cool while nourishing yin; Mu Dan Pi cools and moves blood; Zi Cao vents rashes.',
        'coreherbs' => [
            ['name' => 'Shuǐ Niú Jiǎo', 'pharmaceutical' => 'Bubali Cornu', 'use' => 'Clears intense nutritive- and blood-level heat.'],
            ['name' => 'Shēng Dì Huáng', 'pharmaceutical' => 'Rehmanniae Radix', 'use' => 'Cools blood, nourishes yin and generates fluids.'],
            ['name' => 'Xuán Shēn', 'pharmaceutical' => 'Scrophulariae Radix', 'use' => 'Cools blood, nourishes yin and resolves fire toxicity.'],
            ['name' => 'Mǔ Dān Pí', 'pharmaceutical' => 'Moutan Cortex', 'use' => 'Cools and gently moves the blood without causing stasis.'],
            ['name' => 'Zǐ Cǎo', 'pharmaceutical' => 'Arnebiae/Lithospermi Radix', 'use' => 'Cools and moves blood and helps vent rashes.'],
        ],
        'warnings' => [
            'Active bleeding, altered consciousness or very high fever requires urgent biomedical evaluation.',
            'Cold, rich blood-cooling herbs may be inappropriate with digestive cold/deficiency or without a true heat pattern.',
        ],
        'annex' => [
            'comparison' => 'Rehmanniae Radix is emphasized for reckless movement of hot blood, while Scrophulariae Radix is preferred when heat toxin affects the throat or produces nodules. Moutan Cortex adds dispersing action; Zi Cao is distinguished by rash venting.',
            'preparation' => 'Shui Niu Jiao is a legal substitute for endangered rhinoceros horn and is commonly prepared separately or longer than ordinary botanicals. Confirm the current pharmacopeial method.',
            'nomenclature' => 'Use Bubali Cornu rather than obsolete endangered-animal terminology. Zi Cao may refer to more than one accepted botanical source, so supplier identity matters.',
            'supplemental' => ['Bensky expands comparison by depth of heat, bleeding, yin injury and whether blood stasis or rash is present.'],
        ],
    ],
    'dry-dampness' => [
        'number' => '2.3',
        'name' => 'Clear Heat and Dry Dampness',
        'focus' => 'Damp-heat or fire toxicity differentiated by burner and organ system.',
        'distinction' => 'Huang Qin favors the upper burner, Huang Lian the middle and Heart, and Huang Bai the lower burner; Long Dan Cao targets Liver/Gallbladder damp-heat.',
        'coreherbs' => [
            ['name' => 'Huáng Qín', 'pharmaceutical' => 'Scutellariae Radix', 'use' => 'Clears upper-burner heat and dries dampness.'],
            ['name' => 'Huáng Lián', 'pharmaceutical' => 'Coptidis Rhizoma', 'use' => 'Strongly clears Heart and middle-burner fire.'],
            ['name' => 'Huáng Bǎi', 'pharmaceutical' => 'Phellodendri Cortex', 'use' => 'Clears lower-burner damp-heat and deficiency fire.'],
            ['name' => 'Lóng Dǎn Cǎo', 'pharmaceutical' => 'Gentianae Radix', 'use' => 'Drains Liver/Gallbladder fire and damp-heat.'],
            ['name' => 'Kǔ Shēn', 'pharmaceutical' => 'Sophorae flavescentis Radix', 'use' => 'Clears lower-burner damp-heat and relieves itching.'],
            ['name' => 'Qín Pí', 'pharmaceutical' => 'Fraxini Cortex', 'use' => 'Clears damp-heat, especially in the Large Intestine and eyes.'],
        ],
        'warnings' => [
            'These strongly bitter-cold herbs can damage Spleen/Stomach qi and yin when used excessively or in the wrong pattern.',
            'Persistent jaundice, bloody dysentery or painful urinary symptoms require biomedical assessment rather than self-treatment.',
        ],
        'annex' => [
            'comparison' => 'The source organizes the three Huang herbs by burner: Scutellariae Radix for the upper, Coptidis Rhizoma for the middle, and Phellodendri Cortex for the lower. Gentianae Radix narrows the focus to Liver/Gallbladder; Ku Shen emphasizes the lower burner and genitals; Qin Pi adds Large Intestine and eye indications.',
            'preparation' => 'Processing can redirect or moderate bitter-cold actions. Use the exact processed form specified by a formula or individual monograph.',
            'nomenclature' => 'The pharmaceutical name is especially important for distinguishing similarly translated “yellow” herbs and regional substitutes.',
            'supplemental' => ['The source also includes historical comparisons for dysenteric disorders, jaundice, urinary dysfunction and hot skin lesions.'],
        ],
    ],
    'resolve-toxicity' => [
        'number' => '2.4',
        'name' => 'Clear Heat and Resolve Toxicity',
        'focus' => 'Heat toxin with sores, abscesses, throat swelling or dysenteric presentations.',
        'distinction' => 'Jin Yin Hua vents and disperses; Lian Qiao reduces clumps; Pu Gong Ying favors firm sores and urination; Zi Hua Di Ding favors deep-rooted sores; Ye Ju Hua adds throat and eye use.',
        'coreherbs' => [
            ['name' => 'Jīn Yín Huā', 'pharmaceutical' => 'Lonicerae Flos', 'use' => 'Clears heat toxin with a stronger dispersing and venting action.'],
            ['name' => 'Lián Qiào', 'pharmaceutical' => 'Forsythiae Fructus', 'use' => 'Clears heat toxin and disperses constraint and clumps.'],
            ['name' => 'Pú Gōng Yīng', 'pharmaceutical' => 'Taraxaci Herba', 'use' => 'Addresses firm sores and supports urination.'],
            ['name' => 'Zǐ Huā Dì Dīng', 'pharmaceutical' => 'Violae Herba', 'use' => 'Traditionally emphasized for deep-rooted sores.'],
            ['name' => 'Yě Jú Huā', 'pharmaceutical' => 'Chrysanthemi indici Flos', 'use' => 'Clears heat toxin involving the throat or eyes.'],
        ],
        'warnings' => [
            'Yā Dǎn Zǐ, Shān Dòu Gēn, Yǔ Zhōu Lòu Lú and other supplemental substances have major dose-related toxicity; they are not self-treatment herbs.',
            'Bái Xiǎn Pí has reports of serious liver injury. Běi Dòu Gēn requires verified identity because hazardous Aristolochia adulteration or substitution has occurred.',
            'Suspected abscess, systemic infection, diphtheria, snakebite or cancer requires prompt conventional medical care.',
        ],
        'annex' => [
            'comparison' => 'The source distinguishes this group by location and texture of swelling, degree of heat toxin, dampness or blood stasis, and involvement of the throat, breast, intestine, Lung, skin or urinary tract.',
            'preparation' => 'External use does not eliminate toxicity risk. Verify species, processing, route and dose; do not infer interchangeability from a shared common name.',
            'nomenclature' => 'Botanical verification is essential for Běi Dòu Gēn and similarly named products. Lòu Lú and Yǔ Zhōu Lòu Lú are not interchangeable.',
            'supplemental' => [
                'Supplemental materials include Yā Dǎn Zǐ, Mǎ Chǐ Xiàn, Hóng Téng, Bái Xiǎn Pí, Tǔ Fú Líng, Chuí Pén Cǎo, Jī Gǔ Cǎo, Jī Xuě Cǎo, Bàn Zhī Lián, Mǎ Bó, Shān Dòu Gēn, Běi Dòu Gēn, Qīng Guǒ, Shè Gān, Tǔ Niú Xī, Bái Liǎn, Lòu Lú and Yǔ Zhōu Lòu Lú.',
                'Historical/source-reported only—not a recommendation: older materia medica describe uses for cancers, diphtheria, syphilitic sores, snakebite and antidotal purposes. These claims must not replace evidence-based care.',
            ],
        ],
    ],
    'deficiency-heat' => [
        'number' => '2.5',
        'name' => 'Clear Heat from Deficiency',
        'focus' => 'Lingering or deficiency heat, steaming-bone patterns and afternoon/night fever.',
        'distinction' => 'Qing Hao vents hidden heat; Di Gu Pi cools blood and Lung deficiency fire; Bai Wei adds blood-deficient steaming bone; Yin Chai Hu favors childhood nutritional impairment; Hu Huang Lian adds damp-heat.',
        'coreherbs' => [
            ['name' => 'Qīng Hāo', 'pharmaceutical' => 'Artemisiae annuae Herba', 'use' => 'Vents hidden heat and treats night heat with morning coolness.'],
            ['name' => 'Dì Gǔ Pí', 'pharmaceutical' => 'Lycii Cortex', 'use' => 'Cools blood and clears Lung or deficiency fire.'],
            ['name' => 'Bái Wēi', 'pharmaceutical' => 'Cynanchi atrati Radix', 'use' => 'Cools blood and clears deficiency heat.'],
            ['name' => 'Yín Chái Hú', 'pharmaceutical' => 'Stellariae Radix', 'use' => 'Clears deficiency heat and childhood nutritional-impairment heat.'],
            ['name' => 'Hú Huáng Lián', 'pharmaceutical' => 'Picrorhizae Rhizoma', 'use' => 'Clears deficiency heat and drains damp-heat.'],
        ],
        'warnings' => [
            'Qīng Hāo should not be subjected to prolonged cooking; its preparation differs from dense roots and minerals.',
            'Bái Wēi can be toxic in excessive doses. Hú Huáng Lián is intensely bitter-cold and is CITES Appendix II regulated.',
            'Use caution with Spleen/Stomach cold or deficiency. Yín Chái Hú is not for blood deficiency without heat.',
        ],
        'annex' => [
            'comparison' => 'Qing Hao vents warm pathogens from the yin aspect and is distinguished by night heat with morning coolness. Yin Chai Hu specifically cools deficiency heat without a raising action. Hu Huang Lian is more intensely bitter-cold and adds a downward, damp-heat-draining action.',
            'preparation' => 'Add Qīng Hāo late or use the preparation specified by the formula. Picrorhizae Rhizoma trade may require documentation because the standard species is CITES Appendix II listed.',
            'nomenclature' => 'The standard Hú Huáng Lián species is Picrorhiza scrophulariiflora; common-name substitution should not override botanical verification.',
            'supplemental' => [
                'Bensky discusses Stellariae Radix with Trionycis Carapax for deeper yin-level deficiency heat and compares it with Bupleuri Radix and Artemisiae annuae Herba.',
                'Childhood nutritional impairment and steaming-bone disorder are traditional diagnostic terms, not biomedical diagnoses.',
            ],
        ],
    ],
];
$selectedclearheat = null;
if ($track === 'herbs' && $sectionkey !== '' && isset($clearheatsections[$sectionkey])) {
    $selectedclearheat = $clearheatsections[$sectionkey];
} else if ($sectionkey !== '') {
    $sectionkey = '';
}
$formulacategories = [];
$formulatotals = [
    'questions' => 0,
    'formulas' => 0,
    'categories' => 0,
];
if ($track === 'herbs') {
    $sections = $DB->get_records_select(
        'course_sections',
        'course = :courseid AND section BETWEEN :firstsection AND :lastsection',
        [
            'courseid' => 45,
            'firstsection' => 0,
            'lastsection' => 34,
        ],
        'section ASC',
        'id,section,name'
    );

    foreach ($sections as $section) {
        $number = (int)$section->section;
        $name = trim((string)$section->name);
        if ($number === 0 && $name === '') {
            $name = 'General / Uncategorized';
        } else if ($name === '') {
            $name = 'Topic ' . $number;
        }
        $topics[] = [
            'number' => $number,
            'name' => format_string($name),
        ];
    }
} else {
    $formulabankpath = $CFG->dirroot . '/study-guide/comparisons/formula-practice-data.json';
    if (is_readable($formulabankpath)) {
        $formulabank = json_decode(file_get_contents($formulabankpath), true);
        if (is_array($formulabank)) {
            $manifestpath = $CFG->dirroot . '/study-guide/comparisons/formula-modifications-manifest.json';
            if (is_readable($manifestpath)) {
                $manifest = json_decode(file_get_contents($manifestpath), true);
                $knowncategories = array_column($formulabank['categories'] ?? [], null, 'key');
                $knownquestions = array_fill_keys(array_map(
                    static fn($question) => (string)($question['sourceNumber'] ?? ''),
                    $formulabank['questions'] ?? []
                ), true);
                foreach ($manifest['files'] ?? [] as $filename) {
                    $safe = basename((string)$filename);
                    $supplementpath = $CFG->dirroot . '/study-guide/comparisons/' . $safe;
                    if (!is_readable($supplementpath)) {
                        continue;
                    }
                    $supplement = json_decode(file_get_contents($supplementpath), true);
                    if (!is_array($supplement)) {
                        continue;
                    }
                    foreach ($supplement['categories'] ?? [] as $category) {
                        $key = (string)($category['key'] ?? '');
                        if ($key !== '' && !isset($knowncategories[$key])) {
                            $formulabank['categories'][] = $category;
                            $knowncategories[$key] = $category;
                        }
                    }
                    foreach ($supplement['questions'] ?? [] as $question) {
                        $source = (string)($question['sourceNumber'] ?? '');
                        if ($source !== '' && !isset($knownquestions[$source])) {
                            unset($question['translationEs']);
                            $formulabank['questions'][] = $question;
                            $knownquestions[$source] = true;
                        }
                    }
                }
                $formulabank['totals']['questions'] = count($formulabank['questions'] ?? []);
                $formulabank['totals']['categories'] = count($formulabank['categories'] ?? []);
            }
            $formulatotals['questions'] = (int)($formulabank['totals']['questions'] ?? 0);
            $formulatotals['formulas'] = (int)($formulabank['totals']['formulas'] ?? 0);
            $formulatotals['categories'] = (int)($formulabank['totals']['categories'] ?? 0);
            foreach ($formulabank['categories'] ?? [] as $category) {
                $key = clean_param($category['key'] ?? '', PARAM_ALPHANUMEXT);
                $name = trim((string)($category['en'] ?? ''));
                if ($key === '' || $name === '') {
                    continue;
                }
                $count = 0;
                foreach ($formulabank['questions'] ?? [] as $question) {
                    if (in_array($key, $question['categoryKeys'] ?? [], true)) {
                        $count++;
                    }
                }
                $formulacategories[] = [
                    'key' => $key,
                    'name' => $name,
                    'count' => $count,
                ];
            }
        }
    }
}

$formulapracticeurl = new moodle_url('/study-guide/comparisons/formula-practice.html');

echo $OUTPUT->header();
?>
<main class="herb-board">
    <a class="herb-board__back" href="<?php echo $dashboardurl->out(false); ?>">← Back to Dashboard</a>
    <header class="herb-board__hero">
        <p class="herb-board__eyebrow">CHINESE HERBOLOGY · BOARD LEVEL</p>
        <h2>Herbs Practice Exams</h2>
        <span>Build focused multiple-choice practice from the Board question pool and review your progress over time.</span>
        <nav class="herb-board__tabs" aria-label="Chinese Herbology Board Level sections">
            <a class="herb-board__tab" href="<?php echo $singleherbsurl->out(false); ?>"
                <?php echo $track === 'herbs' ? 'aria-current="page"' : ''; ?>>Single Herbs</a>
            <a class="herb-board__tab" href="<?php echo $formulasurl->out(false); ?>"
                <?php echo $track === 'formulas' ? 'aria-current="page"' : ''; ?>>Formulas</a>
        </nav>
        <div class="herb-board__summary" aria-label="Practice features">
            <span>Multiple choice</span>
            <span>Mock-style feedback</span>
            <span>Flexible session size</span>
            <span>Exam history</span>
        </div>
    </header>

    <?php if ($track === 'herbs') { ?>
        <section class="herb-board__panel" aria-labelledby="herb-board-topics-title">
            <div class="herb-board__panel-heading">
                <div>
                    <h3 id="herb-board-topics-title">Choose a Single Herbs topic</h3>
                    <p>Includes the opening general section, Topics 1–34 and a mixed review.</p>
                </div>
            </div>
            <div class="herb-board__topics">
                <?php foreach ($topics as $topic) {
                    $normalizedname = core_text::strtolower(trim(strip_tags($topic['name'])));
                    $isclearheat = in_array($normalizedname, ['clear heat', 'herbs that clear heat'], true);
                ?>
                    <div class="herb-board__topic">
                        <strong><?php echo $topic['number'] === 0 ? 'G' : $topic['number']; ?></strong>
                        <span><?php echo $topic['name']; ?></span>
                    </div>
                    <?php if ($isclearheat) { ?>
                        <section class="herb-board__subcategories" aria-labelledby="clear-heat-subcategories-title">
                            <div class="herb-board__subcategories-heading">
                                <span class="herb-board__subcategories-kicker">Subcategories</span>
                                <h4 id="clear-heat-subcategories-title">Clear Heat sections</h4>
                            </div>
                            <div class="herb-board__subcategories-grid">
                                <?php foreach ($clearheatsections as $subcategorykey => $subcategory) {
                                    $subcategoryurl = new moodle_url('/blocks/menu/herbology-board.php', [
                                        'track' => 'herbs',
                                        'section' => $subcategorykey,
                                    ], 'clear-heat-section');
                                    $isselected = $sectionkey === $subcategorykey;
                                ?>
                                    <a class="herb-board__topic herb-board__topic--subcategory<?php echo $isselected ? ' herb-board__topic--selected' : ''; ?>"
                                        href="<?php echo $subcategoryurl->out(false); ?>"
                                        <?php echo $isselected ? 'aria-current="page"' : ''; ?>>
                                        <strong><?php echo s($subcategory['number']); ?></strong>
                                        <span>
                                            <?php echo s($subcategory['name']); ?>
                                            <small><?php echo s($subcategory['focus']); ?></small>
                                        </span>
                                    </a>
                                <?php } ?>
                            </div>
                            <p class="herb-board__subcategories-note">The original Clear Heat topic remains the main
                                Yang-based card. These five Bensky sections are study supplements, not replacements.</p>
                        </section>
                    <?php } ?>
                <?php } ?>
                <div class="herb-board__topic herb-board__topic--mixed">
                    <strong>M</strong>
                    <span>Mixed Review</span>
                </div>
            </div>
        </section>

        <?php if ($selectedclearheat) { ?>
            <section class="herb-board__panel herb-board__clear-heat" id="clear-heat-section"
                aria-labelledby="clear-heat-section-title">
                <header class="herb-board__clear-heat-heading">
                    <div>
                        <p class="herb-board__eyebrow">BENSKY SUPPLEMENT · SECTION <?php echo s($selectedclearheat['number']); ?></p>
                        <h3 id="clear-heat-section-title"><?php echo s($selectedclearheat['name']); ?></h3>
                        <p><?php echo s($selectedclearheat['focus']); ?></p>
                    </div>
                    <span class="herb-board__scope-badge">5-section Clear Heat structure</span>
                </header>

                <div class="herb-board__high-yield">
                    <strong>High-yield distinction</strong>
                    <span><?php echo s($selectedclearheat['distinction']); ?></span>
                </div>

                <h4 class="herb-board__subheading">Core herbs</h4>
                <div class="herb-board__herbs">
                    <?php foreach ($selectedclearheat['coreherbs'] as $herb) { ?>
                        <article class="herb-board__herb-card">
                            <h5><?php echo s($herb['name']); ?></h5>
                            <span><?php echo s($herb['pharmaceutical']); ?></span>
                            <p><?php echo s($herb['use']); ?></p>
                        </article>
                    <?php } ?>
                </div>

                <aside class="herb-board__safety" aria-labelledby="clear-heat-safety-title">
                    <h4 id="clear-heat-safety-title">Safety stays visible</h4>
                    <ul>
                        <?php foreach ($selectedclearheat['warnings'] as $warning) { ?>
                            <li><?php echo s($warning); ?></li>
                        <?php } ?>
                    </ul>
                </aside>

                <details class="herb-board__annex">
                    <summary>
                        <span>Bensky extended annex</span>
                        <small>Comparisons, preparation, nomenclature and supplemental herbs</small>
                    </summary>
                    <div class="herb-board__annex-body">
                        <section>
                            <h4>Comparative notes</h4>
                            <p><?php echo s($selectedclearheat['annex']['comparison']); ?></p>
                        </section>
                        <section>
                            <h4>Preparation and product notes</h4>
                            <p><?php echo s($selectedclearheat['annex']['preparation']); ?></p>
                        </section>
                        <section>
                            <h4>Nomenclature and identity</h4>
                            <p><?php echo s($selectedclearheat['annex']['nomenclature']); ?></p>
                        </section>
                        <section class="herb-board__annex-wide">
                            <h4>Supplemental/source notes</h4>
                            <ul>
                                <?php foreach ($selectedclearheat['annex']['supplemental'] as $note) { ?>
                                    <li><?php echo s($note); ?></li>
                                <?php } ?>
                            </ul>
                        </section>
                    </div>
                </details>
                <p class="herb-board__educational-note">Educational Board-review content only. Traditional indications
                    describe pattern-based materia medica and do not constitute diagnosis, prescribing or emergency care.</p>
            </section>
        <?php } ?>
    <?php } else { ?>
        <section class="herb-board__panel" aria-labelledby="herb-board-formulas-title">
            <div class="herb-board__panel-heading">
                <div>
                    <h3 id="herb-board-formulas-title">Choose a Formula practice focus</h3>
                    <p>Open the complete bank, select one formula, or begin with a clinical category.</p>
                </div>
            </div>
            <div class="herb-board__summary herb-board__summary--panel" aria-label="Formula bank totals">
                <span><strong><?php echo $formulatotals['questions']; ?></strong> verified questions</span>
                <span><strong><?php echo $formulatotals['formulas']; ?></strong> core formulas</span>
                <span><strong><?php echo $formulatotals['categories']; ?></strong> clinical categories</span>
            </div>
            <div class="herb-board__launches">
                <a class="herb-board__launch" href="<?php echo $formulapracticeurl->out(false); ?>">
                    <strong>Full question bank</strong>
                    <span>Configure a 10, 25, 50 or 100-question session.</span>
                </a>
                <a class="herb-board__launch" href="<?php echo $formulapracticeurl->out(false); ?>?formula=1&amp;size=25">
                    <strong>Practice by formula</strong>
                    <span>Choose one of the 63 core formulas in the practice screen.</span>
                </a>
            </div>
            <h4 class="herb-board__subheading">Practice by category</h4>
            <div class="herb-board__topics">
                <?php foreach ($formulacategories as $category) {
                    $categoryurl = new moodle_url('/study-guide/comparisons/formula-practice.html', [
                        'category' => $category['key'],
                        'size' => 25,
                    ]);
                ?>
                    <a class="herb-board__topic" href="<?php echo $categoryurl->out(false); ?>">
                        <strong><?php echo $category['count']; ?></strong>
                        <span><?php echo s($category['name']); ?></span>
                    </a>
                <?php } ?>
            </div>
            <div class="herb-board__notice">
                <strong>Formula modifications:</strong> the verified modification questions already in the bank remain
                available within their clinical categories. A dedicated Modifications filter will be added when the
                expanded question set is received.
            </div>
        </section>
    <?php } ?>

    <?php if ($track === 'herbs') { ?>
        <section class="herb-board__panel" aria-labelledby="herb-board-session-title">
            <div class="herb-board__panel-heading">
                <div>
                    <h3 id="herb-board-session-title">Configure the session</h3>
                    <p>These controls establish the Board Level practice structure before the new question pool is connected.</p>
                </div>
            </div>
            <div class="herb-board__setup">
                <div class="herb-board__option">
                    <strong>Number of questions</strong>
                    <span>Choose from the questions available in the selected topic.</span>
                    <div class="herb-board__choices">
                        <span class="herb-board__choice herb-board__choice--selected">10</span>
                        <span class="herb-board__choice">25</span>
                        <span class="herb-board__choice">50</span>
                        <span class="herb-board__choice">100</span>
                    </div>
                </div>
                <div class="herb-board__option">
                    <strong>Feedback</strong>
                    <span>Use the same answer-and-explanation style as the current mock exams.</span>
                    <div class="herb-board__choices">
                        <span class="herb-board__choice herb-board__choice--selected">After each answer</span>
                        <span class="herb-board__choice">At the end</span>
                    </div>
                </div>
                <div class="herb-board__option">
                    <strong>Time limit</strong>
                    <span>Practice without pressure or turn on an exam countdown.</span>
                    <div class="herb-board__choices">
                        <span class="herb-board__choice herb-board__choice--selected">No limit</span>
                        <span class="herb-board__choice">Timed</span>
                    </div>
                </div>
            </div>
            <div class="herb-board__notice">
                Design stage: topic selection is visible now. Starting attempts and saving history will be enabled when the verified Board question pool is connected.
            </div>
        </section>
    <?php } ?>
</main>
<?php
echo $OUTPUT->footer();
