<?php
// Single Herbs Study Guide category catalog.

require_once(__DIR__ . '/../../config.php');

require_login();

$sourcecourseid = 45;
$focus = optional_param('focus', '', PARAM_ALPHANUMEXT);
$displaylang = optional_param('lang', 'en', PARAM_ALPHA);
$displaylang = $displaylang === 'es' ? 'es' : 'en';
$ises = $displaylang === 'es';
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
$istopictwo = $selected !== null
    && (
        (stripos($selectedname, 'Bo He') !== false && stripos($selectedname, 'Niu Bang Zi') !== false)
        || stripos($selectedname, 'Cool, Acrid') !== false
        || stripos($selectedname, 'Wind-Heat') !== false
        || stripos($selectedname, 'Wind Heat') !== false
    );
$istopicthree = $selected !== null
    && (
        (stripos($selectedname, 'Shi Gao') !== false && stripos($selectedname, 'Zhi Mu') !== false)
        || stripos($selectedname, 'Clear Heat and Drain Fire') !== false
        || stripos($selectedname, 'Clear Heat & Drain Fire') !== false
    );
$istopicfour = $selected !== null
    && (
        (stripos($selectedname, 'Sheng Di Huang') !== false && stripos($selectedname, 'Xuan Shen') !== false)
        || stripos($selectedname, 'Clear Heat and Cool Blood') !== false
        || stripos($selectedname, 'Clear Heat & Cool Blood') !== false
    );
$istopicfive = $selected !== null
    && (
        (stripos($selectedname, 'Huang Qin') !== false && stripos($selectedname, 'Huang Lian') !== false && stripos($selectedname, 'Huang Bai') !== false)
        || stripos($selectedname, 'Clear Heat and Dry Damp') !== false
        || stripos($selectedname, 'Clear Heat & Dry Damp') !== false
    );
$istopicsix = $selected !== null
    && (
        (stripos($selectedname, 'Jin Yin Hua') !== false && stripos($selectedname, 'Lian Qiao') !== false && stripos($selectedname, 'Pu Gong Ying') !== false)
        || stripos($selectedname, 'Clear and resolve toxicity') !== false
        || stripos($selectedname, 'Clear Heat and Relieve Toxicity') !== false
        || stripos($selectedname, 'Clear Heat and Resolve Toxicity') !== false
    );
$istopicseven = $selected !== null
    && (
        (stripos($selectedname, 'Qing Hao') !== false && stripos($selectedname, 'Bai Wei') !== false && stripos($selectedname, 'Di Gu Pi') !== false)
        || stripos($selectedname, 'Clear Deficient Heat') !== false
        || stripos($selectedname, 'Clear Heat from Deficiency') !== false
        || stripos($selectedname, 'Clear Deficiency Heat') !== false
    );
$istopiceight = $selected !== null
    && (
        (stripos($selectedname, 'Qing Hao') !== false && stripos($selectedname, 'He Ye') !== false && stripos($selectedname, 'Xi Gua Pi') !== false)
        || stripos($selectedname, 'Clear Summer Heat') !== false
        || stripos($selectedname, 'Clear Summerheat') !== false
    );
$istopicnine = $selected !== null
    && (
        (stripos($selectedname, 'Da Huang') !== false && stripos($selectedname, 'Mang Xiao') !== false && stripos($selectedname, 'Fan Xie Ye') !== false && stripos($selectedname, 'Lu Hui') !== false)
        || stripos($selectedname, 'Downward Draining (Purgatives)') !== false
        || stripos($selectedname, 'Downward-Draining Purgatives') !== false
    );

$topiconeherbs = [
    [
        'name' => 'Ma Huang',
        'latin' => 'Ephedrae Herba',
        'properties' => 'Acrid, slightly bitter; warm',
        'channels' => 'Lung, Bladder',
        'dose' => '3–9 g',
        'identity' => 'The strongest diaphoretic in this group; disseminates Lung Qi, calms wheezing and promotes urination.',
        'clue' => 'Marked chills, no sweating, body aches and wheezing in an Exterior excess presentation.',
        'memory' => 'MA = Maximum opening: Ma Huang throws open the pores when Cold has locked the exterior.',
        'unique' => "Within this Board subset it is the hottest and ranks highest for both sweating and light dispersion in Yang's comparative figures.",
        'formulas' => 'Chief: Ma Huang Tang. Co-chief: Xiao Qing Long Tang with Gui Zhi, and Ma Xing Shi Gan Tang with Shi Gao.',
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
        'memory' => 'A twig reaches the branches: Gui Zhi carries warmth through the channels and limbs.',
        'unique' => "Yang ranks it second in warmth in this Board subset. Its Heart-channel and Blood-circulating emphasis distinguishes it from Ma Huang's stronger surface release.",
        'formulas' => 'Chief: Gui Zhi Tang. Co-chief: Xiao Qing Long Tang with Ma Huang.',
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
        'memory' => 'The Perilla leaf moves at the surface and settles what is restless in the Middle—including the fetus.',
        'unique' => "Among the least warm herbs in Yang's warm-herb figure, yet it uniquely combines gentle Exterior release with Middle-Jiao Qi regulation and calming the fetus.",
        'formulas' => 'No chief or co-chief formula from the current Board Domain roster is linked here. Keep the herb profile without adding an off-list formula.',
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
        'memory' => 'Jing Jie sweeps Wind from the skin; once charred, the same herb turns inward to stop bleeding.',
        'unique' => "It is among the least warm yet tied for second in light dispersion in Yang's figures; processing can shift it from dispersing Wind to stopping bleeding.",
        'formulas' => 'Co-chief: Xiao Feng San with Fang Feng. Supporting ingredient: Yin Qiao San and Zhi Sou San.',
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
        'memory' => 'Fang Feng is the Wind shield: broader and deeper than Jing Jie, especially when Dampness adds heaviness.',
        'unique' => "Its sweet taste moderates pungency; compared with Jing Jie it is less superficial and reaches Dampness and pain in the muscles more effectively.",
        'formulas' => 'Co-chief: Xiao Feng San with Jing Jie. Supporting ingredient: Yu Ping Feng San, Juan Bi Tang and Du Huo Ji Sheng Tang.',
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
        'memory' => 'Qiang Huo climbs the back of the head: occiput, neck and upper back reveal its Taiyang route.',
        'unique' => "It ties for the highest exogenous-Dampness score among the Board herbs shown by Yang; its decisive location is the occiput, neck and upper Taiyang region.",
        'formulas' => 'Co-chief: Qiang Huo Sheng Shi Tang with Du Huo.',
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
        'memory' => 'Bai Zhi lights the front of the face: forehead, eyebrows, nose, teeth and purulent swelling.',
        'unique' => "It ties Qiang Huo for the highest exogenous-Dampness score among these Board herbs, but its rich aroma and Yangming route point to frontal pain, the nose and pus.",
        'formulas' => 'Appears in Cang Er Zi San and Chuan Xiong Cha Tiao San, but another herb leads each formula.',
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
        'memory' => 'Fresh ginger begins at the front door but returns to the kitchen: Exterior release plus Stomach harmony.',
        'unique' => "It sits in the middle of Yang's warmth comparison, but the text calls it a first-line choice for nausea because it uniquely joins mild Exterior release with Stomach harmony.",
        'formulas' => 'Appears in Gui Zhi Tang and Ju Pi Zhu Ru Tang as a supporting ingredient rather than the chief.',
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
        'memory' => 'The Magnolia flower opens at the nose: when smell disappears, think Xin Yi Hua.',
        'unique' => "It is among the least warm herbs yet tied with Jing Jie for second in light dispersion in Yang's figures; its most specific target is nasal opening and smell.",
        'formulas' => 'Appears in Cang Er Zi San as a supporting nasal-opening ingredient; Cang Er Zi is the chief herb.',
        'caution' => 'The flower hairs can irritate the throat; traditionally decocted in a cloth bag.',
    ],
];

$windcoldcategoryherbs = [
    [
        'name' => 'Bai Zhi',
        'latin' => 'Angelicae Dahuricae Radix',
        'common' => 'Angelica Root',
        'boardfocus' => true,
        'note' => 'Best known for frontal, eyebrow, dental and gum pain; also addresses swelling, pus and vaginal discharge.',
    ],
    [
        'name' => 'Cong Bai',
        'latin' => 'Allii Fistulosi Bulbus',
        'common' => 'Scallion or Green Onion',
        'boardfocus' => false,
        'note' => 'Gently releases the Exterior in the very early stage of Wind-Cold and may be used topically for abscesses and sores.',
    ],
    [
        'name' => 'E Bu Shi Cao',
        'latin' => 'Centipedae Herba',
        'common' => 'Centipeda Herb',
        'boardfocus' => false,
        'note' => 'Expels Wind-Cold, opens the nasal passages and is especially associated with nasal obstruction, discharge and cough.',
    ],
    [
        'name' => 'Fang Feng',
        'latin' => 'Saposhnikoviae Radix',
        'common' => 'Ledebouriella Root',
        'boardfocus' => true,
        'note' => 'Releases the Exterior and is useful for painful obstruction, particularly when Wind predominates.',
    ],
    [
        'name' => 'Gao Ben',
        'latin' => 'Ligustici Rhizoma et Radix',
        'common' => 'Chinese Lovage Root',
        'boardfocus' => false,
        'note' => 'Expels Wind-Cold and is strongly associated with headache at the vertex.',
    ],
    [
        'name' => 'Gui Zhi',
        'latin' => 'Cinnamomi Ramulus',
        'common' => 'Cinnamon or Cassia Twig',
        'boardfocus' => true,
        'note' => 'Releases the muscle layer and benefits Wind-Cold-Damp painful obstruction in the joints, limbs and shoulders.',
    ],
    [
        'name' => 'Jing Jie',
        'latin' => 'Schizonepetae Herba',
        'common' => 'Schizonepeta Bud or Stem',
        'boardfocus' => true,
        'note' => 'Releases the Exterior, helps vent early carbuncles or boils and, after appropriate processing, can stop bleeding.',
    ],
    [
        'name' => 'Ma Huang',
        'latin' => 'Ephedrae Herba',
        'common' => 'Ephedra Stem',
        'boardfocus' => true,
        'note' => 'Releases the Exterior, induces sweating, disseminates Lung Qi, calms wheezing and promotes urination.',
    ],
    [
        'name' => 'Qiang Huo',
        'latin' => 'Notopterygii Rhizoma seu Radix',
        'common' => 'Notopterygium Root',
        'boardfocus' => true,
        'note' => 'Best for Wind-Cold-Damp body or joint pain and occipital headache, with an affinity for the Taiyang and Governing Vessel regions.',
    ],
    [
        'name' => 'Sheng Jiang',
        'latin' => 'Zingiberis Rhizoma Recens',
        'common' => 'Fresh Ginger Rhizome',
        'boardfocus' => true,
        'note' => 'Disperses Wind-Cold, warms the Middle and Lung, stops vomiting and reduces the toxicity of certain herbs.',
    ],
    [
        'name' => 'Sheng Jiang Pi',
        'latin' => 'Zingiberis Rhizomatis Recens Cortex',
        'common' => 'Fresh Ginger Rhizome Skin',
        'boardfocus' => false,
        'note' => 'The peel has a cooler, more superficial emphasis and promotes urination to reduce edema.',
    ],
    [
        'name' => 'Xi Xin',
        'latin' => 'Asari Herba',
        'common' => 'Chinese Wild Ginger or Asarum',
        'boardfocus' => false,
        'note' => 'Treats Wind-Cold pain, especially with Dampness or underlying Kidney-Yang deficiency; warms the Lung and opens the nose.',
    ],
    [
        'name' => 'Xiang Ru',
        'latin' => 'Moslae Herba',
        'common' => 'Elsholtzia',
        'boardfocus' => false,
        'note' => 'Releases the Exterior in summer, transforms Dampness, promotes urination and reduces swelling.',
    ],
    [
        'name' => 'Xin Yi Hua',
        'latin' => 'Magnoliae Flos',
        'common' => 'Magnolia Flower',
        'boardfocus' => true,
        'note' => 'Most often used with Wind-Cold and remains a major nasal-opening herb for appropriate rhinitis and sinus patterns.',
    ],
    [
        'name' => 'Zi Su Geng',
        'latin' => 'Perillae Caulis',
        'common' => 'Perilla Stalk',
        'boardfocus' => false,
        'note' => 'Moves Qi, relieves chest and epigastric constraint, harmonizes the Middle and helps calm the fetus.',
    ],
    [
        'name' => 'Zi Su Ye',
        'latin' => 'Perillae Folium',
        'common' => 'Perilla Leaf',
        'boardfocus' => true,
        'note' => 'Releases mild Wind-Cold, moves Qi, calms the fetus, relieves morning sickness and may be used for seafood poisoning.',
    ],
];

$windcoldboardherbs = array_values(array_filter($windcoldcategoryherbs, static function(array $herb): bool {
    return $herb['boardfocus'];
}));
$windcoldannexherbs = array_values(array_filter($windcoldcategoryherbs, static function(array $herb): bool {
    return !$herb['boardfocus'];
}));

$yangcomparisoncharts = [
    [
        'title' => 'Relative warmth',
        'figure' => 'Yang Fig. 2.1',
        'items' => ['Ma Huang' => 100, 'Gui Zhi' => 80, 'Sheng Jiang' => 50, 'Qiang Huo' => 50, 'Bai Zhi' => 30, 'Fang Feng' => 30, 'Jing Jie' => 20, 'Xin Yi Hua' => 20, 'Zi Su Ye' => 20],
    ],
    [
        'title' => 'Sweat-inducing strength',
        'figure' => 'Yang Fig. 2.2',
        'items' => ['Ma Huang' => 100, 'Gui Zhi' => 50, 'Zi Su Ye' => 30, 'Jing Jie' => 20],
    ],
    [
        'title' => 'Light and dispersing quality',
        'figure' => 'Yang Fig. 2.3',
        'items' => ['Ma Huang' => 100, 'Jing Jie' => 80, 'Xin Yi Hua' => 80, 'Fang Feng' => 60, 'Zi Su Ye' => 50, 'Gui Zhi' => 40],
    ],
    [
        'title' => 'Expelling exogenous Dampness',
        'figure' => 'Yang Fig. 2.5',
        'items' => ['Qiang Huo' => 70, 'Bai Zhi' => 70, 'Zi Su Ye' => 40, 'Fang Feng' => 40],
    ],
];

$yangquestions = [
    [
        'question' => 'What is an Exterior syndrome, how is it treated, and how do Exterior-releasing herbs work?',
        'answer' => 'An Exterior syndrome is a conflict between an exogenous pathogen and Defensive Qi in the superficial layer, often involving the Lung and Bladder channels. Treatment expels the pathogen, restores Lung-Qi dispersion and harmonizes Nutritive and Defensive Qi. Acrid herbs move Qi, open the pores and disperse the Lung; warm herbs activate Yang and can produce the controlled light sweat that escorts Wind-Cold outward.',
    ],
    [
        'question' => 'What precautions should be observed when using Exterior-releasing herbs?',
        'answer' => 'Avoid excessive dosage and prolonged use: heavy sweating can consume Qi, Yin and Body Fluids. Reduce the dose for children, older or weak patients, mild patterns and warm weather. Use extra caution with Qi or Yin deficiency, spontaneous sweating, bleeding and pregnancy. The therapeutic goal is a mild whole-body sweat, not exhaustion.',
    ],
    [
        'question' => 'How do Ma Huang and Gui Zhi differ in action, clinical presentation and cautions?',
        'answer' => 'Ma Huang is faster and stronger: it opens tightly closed pores, induces sweating, disseminates Lung Qi and treats severe chills, no sweating, aches and wheezing. Gui Zhi is sweeter and gentler: it warms the Blood and channels, supports Heart Yang and suits milder Cold with slight sweating or channel obstruction. Both require caution with Heat, Yin deficiency, hypertension, heart disease or excessive sweating.',
    ],
    [
        'question' => 'How do raw Ma Huang, honey-prepared Ma Huang and Ma Huang Gen differ?',
        'answer' => 'Raw Ma Huang most strongly releases the Exterior and induces sweat. Honey-prepared Ma Huang is gentler and more moistening, with greater emphasis on directing Lung Qi downward and relieving wheezing. Ma Huang Gen is a different plant part: neutral, sweet and astringent, it stops spontaneous or night sweating rather than releasing the Exterior. Ma Huang Gen is outside this Board category list.',
    ],
    [
        'question' => 'What are the characteristic functions of Gui Zhi beyond releasing the Exterior?',
        'answer' => 'Gui Zhi warms and strengthens Heart Yang, promotes Blood circulation, spreads Yang Qi and unblocks the channels, especially in the limbs. This explains its use for Cold painful obstruction, cold extremities and pain caused by obstruction of Qi and Blood—not only for an Exterior pattern.',
    ],
    [
        'question' => 'How do Jing Jie and Fang Feng differ from the stronger Ma Huang and Gui Zhi?',
        'answer' => 'Jing Jie and Fang Feng are gentler and suit mild Wind-Cold or mixed presentations where forceful sweating is unnecessary. Jing Jie is lighter and more superficial, excels at dispersing Wind and venting itchy eruptions. Fang Feng is sweeter, warmer and deeper; it reaches the muscles and is preferred when Dampness causes pain or heaviness.',
    ],
    [
        'question' => 'How do the part used and processing method change the actions of Jing Jie?',
        'answer' => 'Jing Jie Sui, the flower spike, is lighter and quicker for early-stage Wind. Raw Jing Jie is more pungent and opens closed pores to cause mild sweating. Dry-fried Jing Jie is less dispersing and suits an already open exterior. Charred Jing Jie becomes astringent and is used to stop bleeding. Jing Jie Sui and the processed forms are related preparations, not separate herbs in this Board category list.',
    ],
    [
        'question' => 'How do the different preparations of Sheng Jiang change its action?',
        'answer' => 'Fresh Sheng Jiang mildly releases Wind-Cold, harmonizes the Stomach and is a first-line choice for nausea. Its juice acts faster and stronger for acute nausea, vomiting or Phlegm blocking the orifices. Roasted ginger is warmer and less dispersing, emphasizing Middle-Jiao Cold. Sheng Jiang Pi is the only cold ginger product and promotes urination for edema. These preparations are study distinctions; only Sheng Jiang is in this Board category list.',
    ],
    [
        'question' => 'How do Qiang Huo and Bai Zhi differ by headache location and accompanying pattern?',
        'answer' => 'Qiang Huo follows the Taiyang route to the occiput, neck and upper back, especially when Wind-Cold-Damp produces stiffness and generalized aches. Bai Zhi follows the Yangming route to the forehead, eyebrows, nose and teeth, especially with nasal obstruction, thick discharge, turbidity, swelling or pus.',
    ],
    [
        'question' => 'How do Bai Zhi and Xin Yi Hua differ when nasal obstruction is the main complaint?',
        'answer' => 'Bai Zhi is more strongly ascending, aromatic and drying; select it when frontal Yangming pain, foul or thick discharge, sinus fullness, swelling or pus dominates. Xin Yi Hua is lighter and more moderate; its most specific clue is simply opening the obstructed nose and restoring diminished smell.',
    ],
    [
        'question' => 'Beyond a common cold, which pain, respiratory, edema, skin or ENT presentations can these herbs address?',
        'answer' => 'The group can contribute to Wind-Cold-Damp pain and painful obstruction, cough or wheezing from impaired Lung-Qi dispersion, Wind-Water edema, Wind-related itching and eruptions, and nasal or sinus obstruction. Match the herb to the decisive clue: location, sweating, wheezing, Dampness, rash, nausea, edema or loss of smell.',
    ],
];

$topiconecomparisons = [
    [
        'title' => 'Zi Su Ye vs. Ma Huang',
        'summary' => 'Middle-Jiao Qi, nausea or pregnancy favors Zi Su Ye; strong sweating, wheezing or edema favors Ma Huang.',
        'path' => '/study-guide/comparisons/zi-su-ye-vs-ma-huang.html',
        'path_es' => '/study-guide/comparisons/zi-su-ye-vs-ma-huang-es.html',
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
        'path_es' => '/study-guide/comparisons/qiang-huo-vs-gao-ben-es.html',
    ],
    [
        'title' => 'Xin Yi Hua vs. Hou Po',
        'summary' => 'Magnolia flower with nasal clues indicates Xin Yi Hua; Magnolia bark with fullness and Dampness indicates Hou Po.',
        'path' => '/study-guide/comparisons/xin-yi-hua-vs-hou-po.html',
        'path_es' => '/study-guide/comparisons/xin-yi-hua-vs-hou-po-es.html',
    ],
];

$pageparams = [];
if ($focus !== '') {
    $pageparams['focus'] = $focus;
}
if ($ises) {
    $pageparams['lang'] = 'es';
}
$pageurl = new moodle_url('/blocks/myprogress/single-herbs-study-guide.php', $pageparams);
$categoryindexparams = $ises ? ['lang' => 'es'] : [];
$categoryindexurl = new moodle_url('/blocks/myprogress/single-herbs-study-guide.php', $categoryindexparams);
$languageparams = [];
if ($focus !== '') {
    $languageparams['focus'] = $focus;
}
if (!$ises) {
    $languageparams['lang'] = 'es';
}
$languageurl = new moodle_url('/blocks/myprogress/single-herbs-study-guide.php', $languageparams);
$herbologyurl = new moodle_url('/blocks/myprogress/study-guide.php', ['area' => 'herbology']);
$dashboardurl = new moodle_url('/my/');
$guidelinepath = $ises
    ? '/study-guide/guidelines/chinese-herbal-medicine-es.html'
    : '/study-guide/guidelines/chinese-herbal-medicine.html';
$guidelineurl = new moodle_url($guidelinepath, ['return' => $pageurl->out(false)]);

$PAGE->set_url($pageurl);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$pagetitle = $ises ? 'Guía de estudio de hierbas individuales' : 'Single Herbs Study Guide';
$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);

echo $OUTPUT->header();
$spanishtranslations = $ises ? require(__DIR__ . '/single-herbs-study-guide-es.php') : [];
ob_start();
?>
<style>
.single-herbs-guide{max-width:1160px;margin:24px auto 58px;padding:0 16px;color:#17232a}
.single-herbs-guide :is(a,button):focus-visible{outline:3px solid #185cca;outline-offset:3px}
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
.single-herbs-guide__workspace h3{margin:0 0 8px;font-size:clamp(18px,1.9vw,22px);line-height:1.25}
.single-herbs-guide__workspace-intro{max-width:740px;margin:0;color:#5b6b79;line-height:1.6}
.single-herbs-guide__plan{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;margin:0;padding:0;list-style:none;counter-reset:guide-step}
.single-herbs-guide__plan li{counter-increment:guide-step;position:relative;min-height:105px;padding:18px 18px 18px 58px;border:1px solid #dde4e8;border-radius:10px;background:#f9fbfc}
.single-herbs-guide__plan li:before{content:counter(guide-step);position:absolute;left:17px;top:17px;display:flex;width:28px;height:28px;align-items:center;justify-content:center;border-radius:50%;background:#176b70;color:#fff;font-weight:900}
.single-herbs-guide__plan strong{display:block;margin-bottom:5px}.single-herbs-guide__plan span{color:#60717e;font-size:14px;line-height:1.45}
.single-herbs-guide__note{margin:20px 0 0;padding:15px 17px;border-radius:9px;background:#f0f5ff;color:#40536b;line-height:1.5}
.single-herbs-guide__section{margin-top:28px}.single-herbs-guide__section h4{margin:0 0 7px;color:#123246;font-size:22px}.single-herbs-guide__section-lead{margin:0 0 16px;color:#60717e;line-height:1.5}
.single-herbs-guide__pattern{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin-top:18px}
.single-herbs-guide__pattern article{padding:17px;border-radius:10px;background:#f7fafb;border:1px solid #dce4e8}.single-herbs-guide__pattern strong{display:block;margin-bottom:6px;color:#0f5f58}.single-herbs-guide__pattern span{color:#5b6b79;font-size:14px;line-height:1.45}
.single-herbs-guide__memory-path{margin-top:18px;padding:19px 21px;border-left:5px solid #0b817d;border-radius:10px;background:linear-gradient(135deg,#eef8f6,#f8fbff);color:#405565;line-height:1.6}.single-herbs-guide__memory-path strong{color:#0d665e}.single-herbs-guide__memory-path p{margin:0}.single-herbs-guide__memory-path p+p{margin-top:8px}.single-herbs-guide__memory-line{font-size:17px;font-weight:900;color:#123246!important}
.single-herbs-guide__house{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:6px 18px;margin:13px 0;padding:0;list-style:none}.single-herbs-guide__house li{color:#52677a}.single-herbs-guide__house strong{color:#17364a}
.single-herbs-guide__catalog-heading{margin:25px 0 0;color:#123246;font-size:19px}.single-herbs-guide__catalog-intro{margin:6px 0 14px;color:#60717e;line-height:1.5}.single-herbs-guide__category-herbs{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin:0;padding:0;list-style:none;counter-reset:wind-cold-herb}.single-herbs-guide__category-herb{counter-increment:wind-cold-herb;position:relative;padding:14px 15px 14px 48px;border:1px solid #dde4e8;border-radius:9px;background:#fbfcfd}.single-herbs-guide__category-herb:before{content:counter(wind-cold-herb);position:absolute;left:14px;top:14px;display:flex;width:24px;height:24px;align-items:center;justify-content:center;border-radius:50%;background:#eaf5f3;color:#0d665e;font-size:12px;font-weight:900}.single-herbs-guide__category-herb--core{border-left:4px solid #0b817d;background:#f7fcfb}.single-herbs-guide__category-herb:not(.single-herbs-guide__category-herb--core){border-left:4px solid #d97706;background:#fffaf2}.single-herbs-guide__category-herb-head{display:flex;align-items:flex-start;gap:7px;flex-wrap:wrap}.single-herbs-guide__category-herb-head strong{color:#17364a}.single-herbs-guide__category-herb-head em{color:#6b7b87;font-size:12px}.single-herbs-guide__category-herb-badge{display:inline-flex;padding:2px 7px;border-radius:999px;background:#dff2ee;color:#0d665e;font-size:10px;font-weight:900;text-transform:uppercase}.single-herbs-guide__category-herb-badge--outside{background:#ffedd5;color:#9a3412}.single-herbs-guide__category-herb-common{display:block;margin-top:3px;color:#9a4a12;font-size:12px;font-weight:800}.single-herbs-guide__category-herb p{margin:7px 0 0;color:#52677a;font-size:13px;line-height:1.45}.single-herbs-guide__annex{margin-top:24px;padding-top:20px;border-top:1px dashed #d3dce2}.single-herbs-guide__annex .single-herbs-guide__catalog-heading{margin-top:0;color:#8a461b}
.single-herbs-guide__yang-charts{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.single-herbs-guide__yang-chart{padding:16px;border:1px solid #dce4e8;border-radius:10px;background:#fff}.single-herbs-guide__yang-chart h5{margin:0;color:#17364a;font-size:16px}.single-herbs-guide__yang-chart small{display:block;margin:2px 0 12px;color:#7a647e}.single-herbs-guide__yang-bar{display:grid;grid-template-columns:82px minmax(0,1fr) 30px;gap:8px;align-items:center;margin-top:7px;font-size:11px;color:#4c6070}.single-herbs-guide__yang-bar-track{height:8px;overflow:hidden;border-radius:99px;background:#e9eef2}.single-herbs-guide__yang-bar-fill{display:block;height:100%;border-radius:99px;background:linear-gradient(90deg,#ef5b72,#f48276)}.single-herbs-guide__yang-bar b{text-align:right;color:#17364a;font-size:11px}.single-herbs-guide__yang-note{margin-top:13px;padding:12px 15px;border-radius:8px;background:#f4f0ff;color:#5b4283;font-size:13px;line-height:1.5}
.single-herbs-guide__formula-leads{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.single-herbs-guide__formula-lead{padding:17px;border:1px solid #d9e2e7;border-left:4px solid #7c3aed;border-radius:10px;background:#faf8ff}.single-herbs-guide__formula-lead h5{margin:0 0 7px;color:#50259a;font-size:17px}.single-herbs-guide__formula-lead p{margin:0;color:#52677a;line-height:1.55}.single-herbs-guide__formula-lead strong{color:#25364a}
.single-herbs-guide__yang-deck{max-width:820px;margin:0 auto}.single-herbs-guide__yang-card{display:none;min-height:300px;overflow:hidden;border:1px solid #d7e0e5;border-top:5px solid #7c3aed;border-radius:13px;background:#fff;box-shadow:0 7px 22px rgba(15,23,42,.08)}.single-herbs-guide__yang-card.is-active{display:block}.single-herbs-guide__yang-face{min-height:295px;padding:25px 27px}.single-herbs-guide__yang-face--back{display:none;background:linear-gradient(145deg,#faf8ff,#f6fbfa)}.single-herbs-guide__yang-card.is-flipped .single-herbs-guide__yang-face--front{display:none}.single-herbs-guide__yang-card.is-flipped .single-herbs-guide__yang-face--back{display:block}.single-herbs-guide__yang-label{display:block;margin-bottom:17px;color:#6d28d9;font-size:12px;font-weight:900;letter-spacing:.08em;text-transform:uppercase}.single-herbs-guide__yang-question{margin:0;color:#17364a;font-size:clamp(20px,2.4vw,27px);line-height:1.35}.single-herbs-guide__yang-answer{margin:0;color:#405565;font-size:16px;line-height:1.65}.single-herbs-guide__yang-answer strong{color:#0d665e}.single-herbs-guide__yang-controls{display:grid;grid-template-columns:1fr auto 1fr;gap:10px;align-items:center;margin-top:13px}.single-herbs-guide__yang-control{min-height:42px;padding:9px 14px;border:1px solid #cbd5dc;border-radius:8px;background:#fff;color:#334155;font-weight:900;cursor:pointer}.single-herbs-guide__yang-control:hover{background:#f1f5f9}.single-herbs-guide__yang-control--next{justify-self:end}.single-herbs-guide__yang-control--flip{border-color:#7c3aed;background:#7c3aed;color:#fff}.single-herbs-guide__yang-control--flip:hover{background:#6d28d9}.single-herbs-guide__yang-progress{margin:10px 0 0;color:#60717e;font-size:13px;text-align:center}.single-herbs-guide__scope-note{margin-top:14px;padding:12px 15px;border-radius:8px;background:#fff8e8;color:#725110;line-height:1.5}
.single-herbs-guide__profiles{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px}
.single-herbs-guide__profile{overflow:hidden;border:1px solid #d9e2e7;border-top:4px solid #f77e25;border-radius:10px;background:#fff}
.single-herbs-guide__profile summary{position:relative;cursor:pointer;padding:17px 42px 17px 17px;list-style:none}.single-herbs-guide__profile summary::-webkit-details-marker{display:none}.single-herbs-guide__profile summary:after{content:'+';position:absolute;right:16px;top:16px;display:flex;width:24px;height:24px;align-items:center;justify-content:center;border-radius:50%;background:#eaf5f3;color:#0d665e;font-size:19px;font-weight:900}.single-herbs-guide__profile[open] summary:after{content:'−'}
.single-herbs-guide__profile-name{display:block;color:#123246;font-size:18px;font-weight:900}.single-herbs-guide__profile-latin{display:block;margin-top:3px;color:#6b7b87;font-size:12px;font-style:italic}.single-herbs-guide__profile-meta{display:block;margin-top:9px;color:#0d665e;font-size:12px;font-weight:800;line-height:1.4}
.single-herbs-guide__profile-body{padding:0 17px 17px;border-top:1px solid #e5eaed}.single-herbs-guide__profile-body dl{display:grid;grid-template-columns:max-content 1fr;gap:7px 10px;margin:15px 0 0;font-size:13px;line-height:1.45}.single-herbs-guide__profile-body dt{color:#17364a;font-weight:900}.single-herbs-guide__profile-body dd{margin:0;color:#5b6b79}.single-herbs-guide__profile-body .single-herbs-guide__profile-safety{color:#8a461b}
.single-herbs-guide__profile-body .single-herbs-guide__profile-memory{color:#0d665e;font-weight:700}.single-herbs-guide__profile-body .single-herbs-guide__profile-formulas{color:#364f63}
.single-herbs-guide__decision{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px}.single-herbs-guide__decision div{padding:14px;border-left:4px solid #f77e25;border-radius:8px;background:#fff7f0}.single-herbs-guide__decision strong{display:block;margin-bottom:4px}.single-herbs-guide__decision span{color:#68594f;font-size:14px;line-height:1.4}
.single-herbs-guide__comparisons{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.single-herbs-guide__comparison{display:block;padding:17px;border:1px solid #d9e2e7;border-radius:10px;background:#fff;color:inherit;text-decoration:none}.single-herbs-guide__comparison:hover{border-color:#0b817d;color:inherit;text-decoration:none}.single-herbs-guide__comparison strong{display:block;margin-bottom:6px;color:#0d665e}.single-herbs-guide__comparison span{display:block;color:#60717e;font-size:14px;line-height:1.45}.single-herbs-guide__comparison small{display:block;margin-top:10px;color:#185cca;font-weight:800}
.single-herbs-guide__safety{padding:19px 21px;border-left:5px solid #b45309;border-radius:10px;background:#fff7ed;color:#713f12;line-height:1.55}.single-herbs-guide__safety strong{display:block;margin-bottom:6px}
.single-herbs-guide__recall{display:grid;gap:9px}.single-herbs-guide__recall details{border:1px solid #dce4e8;border-radius:9px;background:#fff}.single-herbs-guide__recall summary{cursor:pointer;padding:15px 17px;color:#17364a;font-weight:800}.single-herbs-guide__recall details[open] summary{border-bottom:1px solid #e4eaed;background:#f6fafb}.single-herbs-guide__recall p{margin:0;padding:15px 17px;color:#52677a;line-height:1.5}.single-herbs-guide__source{font-size:13px}
@media(max-width:850px){.single-herbs-guide__pattern,.single-herbs-guide__decision,.single-herbs-guide__profiles,.single-herbs-guide__house,.single-herbs-guide__formula-leads,.single-herbs-guide__category-herbs,.single-herbs-guide__yang-charts{grid-template-columns:1fr 1fr}}
@media(max-width:720px){.single-herbs-guide__top,.single-herbs-guide__workspace-header{display:block}.single-herbs-guide__actions{justify-content:flex-start;margin-top:16px}.single-herbs-guide__overview{grid-template-columns:1fr}.single-herbs-guide__count{align-items:flex-start}.single-herbs-guide__plan,.single-herbs-guide__pattern,.single-herbs-guide__decision,.single-herbs-guide__comparisons,.single-herbs-guide__profiles,.single-herbs-guide__house,.single-herbs-guide__formula-leads,.single-herbs-guide__category-herbs,.single-herbs-guide__yang-charts{grid-template-columns:1fr}.single-herbs-guide__yang-controls{grid-template-columns:1fr 1fr}.single-herbs-guide__yang-control--flip{grid-column:1/-1;grid-row:1}.single-herbs-guide__yang-control--next{justify-self:stretch}.single-herbs-guide__yang-face{padding:21px 19px}}
</style>

<main class="single-herbs-guide">
    <header class="single-herbs-guide__top">
        <div>
            <p class="single-herbs-guide__eyebrow">Chinese Herbology · Study Guide</p>
            <h2>Single Herbs</h2>
            <p class="single-herbs-guide__lead">Study the materia medica using the same category boundaries and sequence as the Single Herbs Board Level workspace.</p>
        </div>
        <nav class="single-herbs-guide__actions" aria-label="Single Herbs Study Guide navigation">
            <a class="single-herbs-guide__back" href="<?php echo $guidelineurl->out(false); ?>">2026 Board Guideline ↔</a>
            <a class="single-herbs-guide__back" hreflang="<?php echo $ises ? 'en' : 'es'; ?>" href="<?php echo $languageurl->out(false); ?>"><?php echo $ises ? 'English' : 'Español'; ?></a>
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
            </div>
            <?php if ($istopicone): ?>
                <section aria-labelledby="topic-one-foundations">
                    <h4 id="topic-one-foundations">Wind-Cold Exterior-releasing herbs: general introduction</h4>
                    <p class="single-herbs-guide__section-lead">Many herbs in this group are acrid and warm. They disperse Wind-Cold, restore the Lung's dispersing function and open the superficial layer.</p>
                    <div class="single-herbs-guide__pattern">
                        <article><strong>Typical presentation</strong><span>Mild fever, severe chills, headache, body or neck pain and no thirst.</span></article>
                        <article><strong>Primary focus</strong><span>The superficial layer of the body and the Lung. The treatment aim is a controlled, light sweat rather than heavy diaphoresis.</span></article>
                        <article><strong>Board distinction</strong><span>Identify the decisive secondary clue: wheezing, sweating, nausea, rash, Damp pain, headache location or nasal obstruction.</span></article>
                    </div>
                    <p class="single-herbs-guide__scope-note"><strong>General precautions:</strong> do not overdose herbs that promote sweating, because excessive dispersion consumes Qi and Yin. Use additional caution with bleeding conditions and pregnancy because these herbs are moving and dispersing.</p>
                    <aside class="single-herbs-guide__memory-path" aria-label="Memory path for warm acrid exterior-releasing herbs">
                        <p class="single-herbs-guide__memory-line">Picture the Exterior as a house.</p>
                        <ul class="single-herbs-guide__house">
                            <li><strong>Roof and walls:</strong> body surface</li>
                            <li><strong>Doors and windows:</strong> pores</li>
                            <li><strong>Ventilation:</strong> Lung</li>
                            <li><strong>Exterior guard:</strong> Wei Qi</li>
                            <li><strong>Intruder:</strong> Wind-Cold</li>
                            <li><strong>Exit strategy:</strong> a light sweat</li>
                        </ul>
                        <p><strong>Acrid disperses, Warm mobilizes, the Lung opens and sweat escorts Wind-Cold out.</strong> This is the mechanism represented by the house image.</p>
                        <p>Walk through the house to remember the group: <strong>Ma Huang opens the locked door; Gui Zhi warms the branches; Zi Su Ye settles the kitchen; Jing Jie sweeps the skin; Fang Feng raises a Wind shield; Qiang Huo climbs the neck; Bai Zhi clears the forehead and sinuses; Sheng Jiang stops at the Stomach; Xin Yi Hua opens at the nose.</strong></p>
                    </aside>
                    <h5 class="single-herbs-guide__catalog-heading">Current Board guideline herbs</h5>
                    <p class="single-herbs-guide__catalog-intro">Study these nine herbs first. They are the herbs named in the current Board category guideline.</p>
                    <ol class="single-herbs-guide__category-herbs">
                        <?php foreach ($windcoldboardherbs as $categoryherb): ?>
                            <li class="single-herbs-guide__category-herb single-herbs-guide__category-herb--core">
                                <div class="single-herbs-guide__category-herb-head">
                                    <strong><?php echo s($categoryherb['name']); ?></strong>
                                    <em><?php echo s($categoryherb['latin']); ?></em>
                                    <span class="single-herbs-guide__category-herb-badge">In Board category list</span>
                                </div>
                                <span class="single-herbs-guide__category-herb-common"><?php echo s($categoryherb['common']); ?></span>
                                <p><?php echo s($categoryherb['note']); ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ol>

                    <div class="single-herbs-guide__annex">
                        <h5 class="single-herbs-guide__catalog-heading">Annex · Additional Wind-Cold materia medica</h5>
                        <p class="single-herbs-guide__catalog-intro">These related herbs or plant parts add textbook context but are outside this current Board category list. Use them for recognition only; they do not expand the category scope.</p>
                        <ol class="single-herbs-guide__category-herbs">
                            <?php foreach ($windcoldannexherbs as $categoryherb): ?>
                                <li class="single-herbs-guide__category-herb">
                                    <div class="single-herbs-guide__category-herb-head">
                                        <strong><?php echo s($categoryherb['name']); ?></strong>
                                        <em><?php echo s($categoryherb['latin']); ?></em>
                                        <span class="single-herbs-guide__category-herb-badge single-herbs-guide__category-herb-badge--outside">Outside this Board category list</span>
                                    </div>
                                    <span class="single-herbs-guide__category-herb-common"><?php echo s($categoryherb['common']); ?></span>
                                    <p><?php echo s($categoryherb['note']); ?></p>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    </div>
                </section>

                <section class="single-herbs-guide__section" aria-labelledby="topic-one-yang-title">
                    <h4 id="topic-one-yang-title">Yang comparative fingerprint</h4>
                    <p class="single-herbs-guide__section-lead">Use the bars to see what makes each Board herb stand out relative to its neighbors. The view below keeps only the nine herbs in this Board category.</p>
                    <div class="single-herbs-guide__yang-charts">
                        <?php foreach ($yangcomparisoncharts as $chart): ?>
                            <article class="single-herbs-guide__yang-chart">
                                <h5><?php echo s($chart['title']); ?></h5>
                                <small><?php echo s($chart['figure']); ?></small>
                                <?php foreach ($chart['items'] as $herbname => $value): ?>
                                    <div class="single-herbs-guide__yang-bar">
                                        <span><?php echo s($herbname); ?></span>
                                        <span class="single-herbs-guide__yang-bar-track"><span class="single-herbs-guide__yang-bar-fill" style="width:<?php echo (int) $value; ?>%"></span></span>
                                        <b><?php echo (int) $value; ?></b>
                                    </div>
                                <?php endforeach; ?>
                            </article>
                        <?php endforeach; ?>
                    </div>
                    <p class="single-herbs-guide__yang-note"><strong>How to read these bars:</strong> they are relative comparison indices transcribed from Yang, Chapter 2, Figures 2.1, 2.2, 2.3 and 2.5. They are not doses, efficacy percentages or prescribing instructions. Herbs outside this Board category that appear in the original figures are intentionally omitted here.</p>
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

                <section class="single-herbs-guide__section" aria-labelledby="topic-one-profiles-title">
                    <h4 id="topic-one-profiles-title">Individual herb profiles</h4>
                    <p class="single-herbs-guide__section-lead">Each profile compresses the facts into one exam clue, one memorable image and a bridge only to formulas found in the current Board Domain rosters.</p>
                    <div class="single-herbs-guide__profiles">
                        <?php foreach ($topiconeherbs as $herb): ?>
                            <details class="single-herbs-guide__profile"<?php echo $herb['name'] === 'Ma Huang' ? ' open' : ''; ?>>
                                <summary>
                                    <span class="single-herbs-guide__profile-name"><?php echo s($herb['name']); ?></span>
                                    <span class="single-herbs-guide__profile-latin"><?php echo s($herb['latin']); ?></span>
                                    <span class="single-herbs-guide__profile-meta"><?php echo s($herb['properties']); ?> · <?php echo s($herb['channels']); ?></span>
                                </summary>
                                <div class="single-herbs-guide__profile-body">
                                    <dl>
                                        <dt>Dose</dt><dd><?php echo s($herb['dose']); ?></dd>
                                        <dt>Identity</dt><dd><?php echo s($herb['identity']); ?></dd>
                                        <dt>Exam clue</dt><dd><?php echo s($herb['clue']); ?></dd>
                                        <dt>Memory</dt><dd class="single-herbs-guide__profile-memory"><?php echo s($herb['memory']); ?></dd>
                                        <dt>Unique fingerprint</dt><dd><?php echo s($herb['unique']); ?></dd>
                                        <dt>Formula bridge</dt><dd class="single-herbs-guide__profile-formulas"><?php echo s($herb['formulas']); ?></dd>
                                        <dt>Safety</dt><dd class="single-herbs-guide__profile-safety"><?php echo s($herb['caution']); ?></dd>
                                    </dl>
                                </div>
                            </details>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="single-herbs-guide__section" aria-labelledby="topic-one-formula-leads-title">
                    <h4 id="topic-one-formula-leads-title">When the herb leads the formula</h4>
                    <p class="single-herbs-guide__section-lead">These connections are limited to formulas in the current Board Domain rosters in which one of the nine herbs is a chief or co-chief ingredient. Off-list textbook connections are deliberately omitted.</p>
                    <div class="single-herbs-guide__formula-leads">
                        <article class="single-herbs-guide__formula-lead">
                            <h5>Ma Huang</h5>
                            <p><strong>Chief:</strong> Ma Huang Tang. <strong>Co-chief:</strong> Xiao Qing Long Tang with Gui Zhi; Ma Xing Shi Gan Tang with Shi Gao.</p>
                        </article>
                        <article class="single-herbs-guide__formula-lead">
                            <h5>Gui Zhi</h5>
                            <p><strong>Chief:</strong> Gui Zhi Tang, where it releases the muscle layer and harmonizes Ying and Wei. <strong>Co-chief:</strong> Xiao Qing Long Tang with Ma Huang.</p>
                        </article>
                        <article class="single-herbs-guide__formula-lead">
                            <h5>Jing Jie + Fang Feng</h5>
                            <p><strong>Co-chief pair:</strong> Xiao Feng San, where Jing Jie and Fang Feng expel Wind and relieve itching.</p>
                        </article>
                        <article class="single-herbs-guide__formula-lead">
                            <h5>Qiang Huo</h5>
                            <p><strong>Co-chief:</strong> Qiang Huo Sheng Shi Tang with Du Huo for Wind-Damp pain concentrated in the upper body.</p>
                        </article>
                    </div>
                    <p class="single-herbs-guide__scope-note"><strong>Role check:</strong> Fang Feng supports Yu Ping Feng San, Bai Zhi and Xin Yi Hua support Cang Er Zi San, and Sheng Jiang supports Gui Zhi Tang and Ju Pi Zhu Ru Tang. These formulas are on the Board rosters, but the named herb does not lead each formula.</p>
                </section>

                <section class="single-herbs-guide__section" aria-labelledby="topic-one-comparisons-title">
                    <h4 id="topic-one-comparisons-title">High-yield comparisons</h4>
                    <p class="single-herbs-guide__section-lead">Open the complete comparison when two choices appear plausible.</p>
                    <div class="single-herbs-guide__comparisons">
                        <?php foreach ($topiconecomparisons as $comparison): ?>
                            <?php
                            $comparisonpath = $ises && !empty($comparison['path_es']) ? $comparison['path_es'] : $comparison['path'];
                            $comparisonurl = new moodle_url($comparisonpath, [
                                'from' => 'single-herbs',
                                'focus' => (string) $selected['number'],
                            ]);
                            ?>
                            <a class="single-herbs-guide__comparison" href="<?php echo $comparisonurl->out(false); ?>">
                                <strong><?php echo s($comparison['title']); ?></strong>
                                <span><?php echo s($comparison['summary']); ?></span>
                                <small>Open full comparison →</small>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="single-herbs-guide__section" aria-labelledby="topic-one-safety-title">
                    <h4 id="topic-one-safety-title">Ma Huang safety checkpoint</h4>
                    <div class="single-herbs-guide__safety"><strong>Ma Huang requires a separate safety review beyond the general category precautions above.</strong>It has important cardiovascular, medication-interaction and regulatory considerations. Professional supervision, individual screening and current local rules are essential.</div>
                </section>

                <section class="single-herbs-guide__section" aria-labelledby="topic-one-yang-questions-title">
                    <h4 id="topic-one-yang-questions-title">Yang questions for this category</h4>
                    <p class="single-herbs-guide__section-lead">One study card holds all selected Chapter 2 questions. Think through the question, reveal the answer, then move to the next card. References outside the current list are explicitly identified.</p>
                    <div class="single-herbs-guide__yang-deck" data-yang-deck data-card-label="<?php echo $ises ? 'Tarjeta' : 'Card'; ?>" data-of-label="<?php echo $ises ? 'de' : 'of'; ?>">
                        <?php foreach ($yangquestions as $questionindex => $yangquestion): ?>
                            <article class="single-herbs-guide__yang-card<?php echo $questionindex === 0 ? ' is-active' : ''; ?>" data-yang-card aria-hidden="<?php echo $questionindex === 0 ? 'false' : 'true'; ?>">
                                <div class="single-herbs-guide__yang-face single-herbs-guide__yang-face--front">
                                    <span class="single-herbs-guide__yang-label"><?php echo $ises ? 'Pregunta' : 'Question'; ?> <?php echo $questionindex + 1; ?> <?php echo $ises ? 'de' : 'of'; ?> <?php echo count($yangquestions); ?> · Yang, <?php echo $ises ? 'capítulo' : 'Chapter'; ?> 2</span>
                                    <p class="single-herbs-guide__yang-question"><?php echo s($yangquestion['question']); ?></p>
                                </div>
                                <div class="single-herbs-guide__yang-face single-herbs-guide__yang-face--back">
                                    <span class="single-herbs-guide__yang-label"><?php echo $ises ? 'Respuesta' : 'Answer'; ?> <?php echo $questionindex + 1; ?> <?php echo $ises ? 'de' : 'of'; ?> <?php echo count($yangquestions); ?></span>
                                    <p class="single-herbs-guide__yang-answer"><?php echo s($yangquestion['answer']); ?></p>
                                </div>
                            </article>
                        <?php endforeach; ?>
                        <div class="single-herbs-guide__yang-controls" aria-label="Yang study card controls">
                            <button class="single-herbs-guide__yang-control" type="button" data-yang-prev>← Previous</button>
                            <button class="single-herbs-guide__yang-control single-herbs-guide__yang-control--flip" type="button" data-yang-flip data-show-label="Show answer" data-hide-label="Show question">Show answer</button>
                            <button class="single-herbs-guide__yang-control single-herbs-guide__yang-control--next" type="button" data-yang-next>Next →</button>
                        </div>
                        <p class="single-herbs-guide__yang-progress" data-yang-progress><?php echo $ises ? 'Tarjeta' : 'Card'; ?> 1 <?php echo $ises ? 'de' : 'of'; ?> <?php echo count($yangquestions); ?></p>
                    </div>
                    <p class="single-herbs-guide__scope-note"><strong>Category boundary:</strong> questions concerning cool, acrid Exterior-releasing herbs belong to later Board Level categories and are intentionally excluded here.</p>
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
            <?php elseif ($istopictwo): ?>
                <?php require(__DIR__ . '/single-herbs-category-cool-acrid.php'); ?>
            <?php elseif ($istopicthree): ?>
                <?php require(__DIR__ . '/single-herbs-category-clear-heat-drain-fire.php'); ?>
            <?php elseif ($istopicfour): ?>
                <?php require(__DIR__ . '/single-herbs-category-clear-heat-cool-blood.php'); ?>
            <?php elseif ($istopicfive): ?>
                <?php require(__DIR__ . '/single-herbs-category-clear-heat-dry-damp.php'); ?>
            <?php elseif ($istopicsix): ?>
                <?php require(__DIR__ . '/single-herbs-category-clear-resolve-toxicity.php'); ?>
            <?php elseif ($istopicseven): ?>
                <?php require(__DIR__ . '/single-herbs-category-clear-deficient-heat.php'); ?>
            <?php elseif ($istopiceight): ?>
                <?php require(__DIR__ . '/single-herbs-category-clear-summer-heat.php'); ?>
            <?php elseif ($istopicnine): ?>
                <?php require(__DIR__ . '/single-herbs-category-downward-draining-purgatives.php'); ?>
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
                    <?php
                    $categoryparams = ['focus' => (string) $category['number']];
                    if ($ises) {
                        $categoryparams['lang'] = 'es';
                    }
                    $categoryurl = new moodle_url('/blocks/myprogress/single-herbs-study-guide.php', $categoryparams);
                    ?>
                    <a class="single-herbs-guide__category" href="<?php echo $categoryurl->out(false); ?>">
                        <span class="single-herbs-guide__category-number"><?php echo $category['number'] === 0 ? 'G' : $category['number']; ?></span>
                        <strong><?php echo $category['name']; ?></strong>
                        <small>Open study framework →</small>
                    </a>
                <?php endforeach; ?>
                <?php
                $mixedparams = ['focus' => 'mixed'];
                if ($ises) {
                    $mixedparams['lang'] = 'es';
                }
                $mixedurl = new moodle_url('/blocks/myprogress/single-herbs-study-guide.php', $mixedparams);
                ?>
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
<script>
(function() {
    const deck = document.querySelector('[data-yang-deck]');
    if (!deck) {
        return;
    }
    const cards = Array.from(deck.querySelectorAll('[data-yang-card]'));
    const previous = deck.querySelector('[data-yang-prev]');
    const next = deck.querySelector('[data-yang-next]');
    const flip = deck.querySelector('[data-yang-flip]');
    const progress = deck.querySelector('[data-yang-progress]');
    let current = 0;

    function showCard(index) {
        current = (index + cards.length) % cards.length;
        cards.forEach(function(card, cardindex) {
            const active = cardindex === current;
            card.classList.toggle('is-active', active);
            card.classList.remove('is-flipped');
            card.setAttribute('aria-hidden', active ? 'false' : 'true');
        });
        flip.textContent = flip.dataset.showLabel;
        progress.textContent = deck.dataset.cardLabel + ' ' + (current + 1) + ' ' + deck.dataset.ofLabel + ' ' + cards.length;
    }

    if (!cards.length || !previous || !next || !flip || !progress) {
        return;
    }
    previous.addEventListener('click', function() {
        showCard(current - 1);
    });
    next.addEventListener('click', function() {
        showCard(current + 1);
    });
    flip.addEventListener('click', function() {
        const activeCard = cards[current];
        const flipped = activeCard.classList.toggle('is-flipped');
        flip.textContent = flipped ? flip.dataset.hideLabel : flip.dataset.showLabel;
    });
})();
</script>
<?php
$guidecontent = ob_get_clean();
echo $ises ? strtr($guidecontent, $spanishtranslations) : $guidecontent;
echo $OUTPUT->footer();
