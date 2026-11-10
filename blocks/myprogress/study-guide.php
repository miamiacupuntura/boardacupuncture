<?php
// This file is part of Moodle - http://moodle.org/.

require_once(__DIR__ . '/../../config.php');

require_login();

$displaylang = optional_param('lang', 'en', PARAM_ALPHA);
$displaylang = $displaylang === 'es' ? 'es' : 'en';
$ises = $displaylang === 'es';

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
        'description' => 'Study Single Herbs, Duì Yào and Formulas, then compare their related—but distinct—Bensky classification levels.',
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
                'description' => 'Study all 21 Bensky formula chapters while distinguishing detailed platform profiles from the 181 official Appendix C formulas.',
                'path' => '/blocks/myprogress/formulas-study-guide.php',
                'anchor' => '',
                'status' => 'AVAILABLE · 21 CHAPTERS',
            ],
            [
                'title' => 'Bensky Category Crosswalk',
                'description' => 'Compare 34 action categories for individual herbs with 21 strategy chapters for complete formulas. Similar names show a study relationship, not the same classification.',
                'path' => '/blocks/myprogress/herb-formula-category-map.php',
                'anchor' => '',
                'status' => 'NEW · REFERENCE MAP',
                'priority' => true,
                'comparison' => true,
            ],
            [
                'title' => 'Herbal Comparison Study Bank',
                'description' => 'Study 249 comparative herb questions in book order with visible explanations, recall flashcards and 117 strength-and-temperature charts.',
                'path' => '/study-guide/comparisons/herb-comparison-bank.html',
                'anchor' => '',
                'status' => 'AVAILABLE · 249 QUESTIONS',
                'button_label' => 'Open',
                'practice_exam' => true,
                'study_bank' => true,
            ],
            [
                'title' => '63 Formulas Comparison',
                'description' => 'Find and study 63 core formulas, their patterns, actions, ingredients, safety notes and connected study questions.',
                'path' => '/study-guide/comparisons/formula-navigator.html',
                'anchor' => '',
                'status' => 'AVAILABLE · CONNECTED STUDY',
                'secondary' => true,
                'study_bank' => true,
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
                'title' => 'Pattern Comparison Maciocia',
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
                'title' => 'Herbs Secondary Actions',
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
                'title' => 'Formula Modifications Quiz',
                'description' => 'Choose answers and test the 305 formula modifications in focused Quiz sessions after reviewing the reference cards.',
                'path' => '/study-guide/comparisons/formula-practice.html',
                'params' => [
                    'category' => 'modifications',
                    'size' => 25,
                ],
                'anchor' => '',
                'status' => 'AVAILABLE · 305 QUESTIONS',
                'practice_exam' => true,
                'quiz' => true,
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

$spanishcopy = [
    'Chinese Herbology' => 'Herbología China',
    'Study Single Herbs, Duì Yào and Formulas, then compare their related—but distinct—Bensky classification levels.' => 'Estudia Hierbas individuales, Duì Yào y Fórmulas; después compara sus niveles de clasificación Bensky, relacionados pero distintos.',
    'Single Herbs' => 'Hierbas individuales',
    'Study single herbs through the same General, Topics 1–34 and Mixed Review categories used in the Chinese Herbology Board Level workspace.' => 'Estudia las hierbas individuales con las mismas categorías General, Temas 1–34 y Repaso mixto del espacio Board Level de Herbología China.',
    'Duì Yào Pairs' => 'Parejas Duì Yào',
    'Master 51 official herb pairs through 15 clinical blocks. This Domain I resource emphasizes combined actions, individual roles, decisive clues, formulas and safety.' => 'Domina las 51 parejas oficiales en 15 bloques clínicos. Este recurso del Dominio I destaca acciones combinadas, funciones individuales, pistas decisivas, fórmulas y seguridad.',
    'Formulas' => 'Fórmulas',
    'Study all 21 Bensky formula chapters while distinguishing detailed platform profiles from the 181 official Appendix C formulas.' => 'Estudia los 21 capítulos de fórmulas de Bensky diferenciando las fichas detalladas de la plataforma de las 181 fórmulas oficiales del Appendix C.',
    'Bensky Category Crosswalk' => 'Mapa comparativo Bensky',
    'Compare 34 action categories for individual herbs with 21 strategy chapters for complete formulas. Similar names show a study relationship, not the same classification.' => 'Compara 34 categorías de acción para hierbas individuales con 21 capítulos de estrategias para fórmulas completas. Los nombres parecidos muestran una relación de estudio, no la misma clasificación.',
    'Herbal Comparison Study Bank' => 'Banco de estudio comparativo de hierbas',
    'Study 249 comparative herb questions in book order with visible explanations, recall flashcards and 117 strength-and-temperature charts.' => 'Estudia 249 preguntas comparativas en el orden del libro con explicaciones visibles, flashcards y 117 tablas de potencia y temperatura.',
    '63 Formulas Comparison' => 'Comparación de 63 fórmulas',
    'Find and study 63 core formulas, their patterns, actions, ingredients, safety notes and connected study questions.' => 'Busca y estudia 63 fórmulas principales, sus patrones, acciones, ingredientes, notas de seguridad y preguntas de estudio conectadas.',
    'Formula Families / Variations' => 'Familias y variaciones de fórmulas',
    'Study 22 formula families and their named variations. Each card shows the root formula, structural change, resulting pattern and the decisive clue for choosing each branch.' => 'Estudia 22 familias de fórmulas y sus variaciones. Cada ficha muestra la fórmula raíz, el cambio estructural, el patrón resultante y la pista decisiva.',
    'Name Traps' => 'Trampas de nombres',
    'Separate nearly identical herb names by characters, identity, function and the safety alert that must not be missed.' => 'Distingue nombres de hierbas casi idénticos por caracteres, identidad, función y la alerta de seguridad esencial.',
    'Herb Families & Parts' => 'Familias y partes de hierbas',
    'Learn how leaf, twig, bark, root, seed and fruit within an herb family change direction, function and safety.' => 'Aprende cómo hoja, rama, corteza, raíz, semilla y fruto cambian la dirección, función y seguridad dentro de una familia.',
    'Safety Library' => 'Biblioteca de seguridad',
    'Review preparation, toxicity, interactions and regulation while studying herbs, pairs and formulas.' => 'Repasa preparación, toxicidad, interacciones y regulación mientras estudias hierbas, parejas y fórmulas.',
    'Formula Name Traps' => 'Trampas en nombres de fórmulas',
    'Separate formulas with nearly identical names by architecture, mechanism and the clinical clue that changes the answer.' => 'Distingue fórmulas con nombres casi idénticos por arquitectura, mecanismo y la pista clínica que cambia la respuesta.',
    'Misleading Formula Names' => 'Nombres engañosos de fórmulas',
    'Learn formulas whose historical, poetic or shared name does not literally describe the modern clinical strategy.' => 'Aprende fórmulas cuyo nombre histórico, poético o compartido no describe literalmente la estrategia clínica moderna.',
    'Advanced Name Traps' => 'Trampas avanzadas de nombres',
    'Separate pinyin groups, geographic prefixes and Western common names that hide different species.' => 'Distingue grupos de pinyin, prefijos geográficos y nombres comunes occidentales que ocultan especies diferentes.',
    'Supplemental Herb Families' => 'Familias suplementarias de hierbas',
    'Review six additional plant, medicinal-part and related-herb groups that are commonly confused.' => 'Repasa seis grupos adicionales de plantas, partes medicinales y hierbas relacionadas que suelen confundirse.',
    'Pattern Comparison Maciocia' => 'Comparación de patrones Maciocia',
    'Review signs, tongue, pulse and representative formulas through 100 organ-pattern clinical tips.' => 'Repasa signos, lengua, pulso y fórmulas representativas mediante 100 consejos clínicos de patrones de órganos.',
    'Most Important Formulas' => 'Fórmulas más importantes',
    'Review 85 formulas by pattern, action, decisive clue, contrast, memory cue and safety.' => 'Repasa 85 fórmulas por patrón, acción, pista decisiva, contraste, recurso de memoria y seguridad.',
    'Formula Composition' => 'Composición de fórmulas',
    'Learn herb relationships, Jun–Chen–Zuo–Shi hierarchy, dose architecture, preparation and an exam method.' => 'Aprende relaciones entre hierbas, jerarquía Jun–Chen–Zuo–Shi, arquitectura de dosis, preparación y un método de examen.',
    'Herbal Incompatibilities' => 'Incompatibilidades herbales',
    'Master the seven herb relationships, Eighteen Incompatibilities, Nineteen Antagonisms and their decisive exam clues.' => 'Domina las siete relaciones herbales, las Dieciocho Incompatibilidades, los Diecinueve Antagonismos y sus pistas decisivas.',
    'Herbs Secondary Actions' => 'Acciones secundarias de hierbas',
    'Study 60 secondary herb functions while keeping the primary category of each medicinal in view.' => 'Estudia 60 funciones secundarias sin perder de vista la categoría principal de cada sustancia medicinal.',
    'Formula Comparisons' => 'Comparaciones de fórmulas',
    'Review 110 focused comparisons by treatment strategy, pattern, actions, ingredients and the decisive clues that distinguish similar formulas.' => 'Repasa 110 comparaciones por estrategia, patrón, acciones, ingredientes y pistas decisivas que distinguen fórmulas similares.',
    'Single Herbs Comparisons' => 'Comparaciones de hierbas individuales',
    'Compare Bensky categories, pathologies and Chinese medicine patterns.' => 'Compara categorías Bensky, patologías y patrones de medicina china.',
    'Review 247 comparative study notes that distinguish similar herbs by actions, indications, channels and decisive clinical clues.' => 'Repasa 247 notas comparativas que distinguen hierbas similares por acciones, indicaciones, canales y pistas clínicas decisivas.',
    'Pathology Comparisons' => 'Comparaciones de patologías',
    'Review 66 clinical comparisons organized by pathology, symptom location and the clues that guide herb or formula selection.' => 'Repasa 66 comparaciones clínicas por patología, localización de síntomas y pistas que orientan la selección de hierbas o fórmulas.',
    'Pattern Comparisons' => 'Comparaciones de patrones',
    'Review 50 pattern comparisons that separate similar Chinese medicine presentations by mechanism, signs, tongue and pulse.' => 'Repasa 50 comparaciones que distinguen cuadros similares por mecanismo, signos, lengua y pulso.',
    'Formula Modifications' => 'Modificaciones de fórmulas',
    'Review 305 clinical modifications as reference cards organized by formula, presentation and the corresponding ingredient change.' => 'Repasa 305 modificaciones clínicas organizadas por fórmula, presentación y cambio correspondiente de ingredientes.',
    'Formula Modifications Quiz' => 'Quiz de modificaciones de fórmulas',
    'Choose answers and test the 305 formula modifications in focused Quiz sessions after reviewing the reference cards.' => 'Elige respuestas y evalúa las 305 modificaciones de fórmulas en sesiones de Quiz después de repasar las fichas de referencia.',
    'Commonly Used Herbal Combinations' => 'Combinaciones herbales de uso común',
    'Study 70 Appendix 2 combinations by relationship, clinical application and the role of each herb. This chapter remains separate from the 51 official Duì Yào pairs.' => 'Estudia 70 combinaciones del Apéndice 2 por relación, aplicación clínica y función de cada hierba. Este capítulo permanece separado de las 51 parejas Duì Yào oficiales.',
    'Study Guide' => 'Guía de estudio',
    'Study Library' => 'Biblioteca de estudio',
    'Library →' => 'Biblioteca →',
    '← Back to Dashboard' => '← Volver al Dashboard',
    'Open' => 'Abrir',
    'Study Guides' => 'Guías de estudio',
    'Open the principal learning resources.' => 'Abre los recursos principales de aprendizaje.',
    'Questions & Flashcards' => 'Preguntas y flashcards',
    'Review questions with answers and explanations, or use recall flashcards.' => 'Repasa preguntas con respuestas y explicaciones, o utiliza flashcards de memoria.',
    'Quiz' => 'Quiz',
    'Choose answers and receive immediate feedback.' => 'Elige respuestas y recibe feedback inmediato.',
    'Open question banks and focused practice sessions.' => 'Abre bancos de preguntas y sesiones de práctica enfocadas.',
    'Open the comparative study collections.' => 'Abre las colecciones de estudio comparativo.',
    'Reference Library' => 'Biblioteca de referencia',
    'Open families, safety, theory and supplemental study resources.' => 'Abre familias, seguridad, teoría y recursos suplementarios.',
    'PRACTICE' => 'PRÁCTICA',
    'Practice Exams' => 'Exámenes de práctica',
    'Practice herb comparisons, principal formulas and their clinical modifications.' => 'Practica comparaciones de hierbas, fórmulas principales y sus modificaciones clínicas.',
    'COMPARATIVE STUDY' => 'ESTUDIO COMPARATIVO',
    'Comparisons' => 'Comparaciones',
    'Compare single herbs, formulas, pathologies and Chinese medicine patterns.' => 'Compara hierbas individuales, fórmulas, patologías y patrones de medicina china.',
    'ADDITIONAL STUDY' => 'ESTUDIO ADICIONAL',
    'Other chapters' => 'Otros capítulos',
    'Additional source-based chapters that complement the principal study guides.' => 'Capítulos adicionales basados en fuentes que complementan las guías principales.',
    'AVAILABLE' => 'DISPONIBLE',
];
$tr = static function(string $text) use ($ises, $spanishcopy): string {
    if (!$ises) {
        return $text;
    }
    if (isset($spanishcopy[$text])) {
        return $spanishcopy[$text];
    }
    return strtr($text, [
        'AVAILABLE' => 'DISPONIBLE',
        'NEW' => 'NUEVO',
        'HIGH PRIORITY' => 'PRIORIDAD ALTA',
        'FORMULAS' => 'FÓRMULAS',
        'CHAPTERS' => 'CAPÍTULOS',
        'REFERENCE MAP' => 'MAPA DE REFERENCIA',
        'QUESTIONS' => 'PREGUNTAS',
        'COMPARISONS' => 'COMPARACIONES',
        'MODIFICATIONS' => 'MODIFICACIONES',
        'FAMILIES' => 'FAMILIAS',
        'ACTIONS' => 'ACCIONES',
        'CLINICAL TIPS' => 'CONSEJOS CLÍNICOS',
        'THEORY TOPICS' => 'TEMAS DE TEORÍA',
        'SAFETY SECTIONS' => 'SECCIONES DE SEGURIDAD',
        'SAFETY CHAPTERS' => 'CAPÍTULOS DE SEGURIDAD',
        'NAME TRAPS' => 'TRAMPAS DE NOMBRES',
        'FORMULA NAMES' => 'NOMBRES DE FÓRMULAS',
        'CONNECTED STUDY' => 'ESTUDIO CONECTADO',
        'APPENDIX' => 'APÉNDICE',
    ]);
};

$areaid = optional_param('area', 'pointlocation', PARAM_ALPHA);
if (!array_key_exists($areaid, $areas)) {
    $areaid = 'pointlocation';
}
$area = $areas[$areaid];
if ($ises) {
    $area['title'] = $tr($area['title']);
    $area['description'] = $tr($area['description']);
    foreach ($area['chapters'] as &$translatedchapter) {
        foreach (['title', 'description', 'status', 'button_label', 'guide_status', 'board_practice'] as $field) {
            if (isset($translatedchapter[$field])) {
                $translatedchapter[$field] = $tr($translatedchapter[$field]);
            }
        }
    }
    unset($translatedchapter);
}
$primarychapters = array_values(array_filter($area['chapters'], static function(array $chapter): bool {
    return empty($chapter['secondary']) && empty($chapter['practice_exam']) && empty($chapter['comparison']);
}));
$practicechapters = array_values(array_filter($area['chapters'], static function(array $chapter): bool {
    return !empty($chapter['practice_exam']) && empty($chapter['quiz']);
}));
$studybankchapters = array_values(array_filter($area['chapters'], static function(array $chapter): bool {
    return !empty($chapter['study_bank']);
}));
$quizchapters = array_values(array_filter($area['chapters'], static function(array $chapter): bool {
    return !empty($chapter['quiz']);
}));
$comparisonchapters = array_values(array_filter($area['chapters'], static function(array $chapter): bool {
    return !empty($chapter['comparison']);
}));
$comparisonoverviewchapters = array_values(array_filter($comparisonchapters, static function(array $chapter): bool {
    return !in_array($chapter['path'] ?? '', [
        '/study-guide/comparisons/formulas.html',
        '/study-guide/comparisons/index.html',
    ], true);
}));
$otherchapters = array_values(array_filter($area['chapters'], static function(array $chapter): bool {
    return !empty($chapter['secondary']) && empty($chapter['practice_exam']) && empty($chapter['comparison']);
}));
$secondaryguidechapters = array_values(array_filter(
    $otherchapters,
    static function(array $chapter): bool {
        return !in_array($chapter['path'] ?? '', [
            '/study-guide/comparisons/formula-navigator.html',
            '/study-guide/comparisons/patterns-tips-maciocia.html',
            '/study-guide/comparisons/safety.html',
        ], true);
    }
));
$requestedview = optional_param('view', '', PARAM_ALPHA);
$showconstruction = $requestedview === 'construction';
$showlibrary = $requestedview === 'library';
$catalogview = in_array($requestedview, ['guides', 'comparisons', 'other', 'practice'], true)
    ? $requestedview
    : '';
$librarygroups = [
    [
        'id' => 'core-guides',
        'title' => 'Study Guides',
        'description' => 'Open the principal learning resources.',
        'modifier' => 'primary',
        'chapters' => $primarychapters,
    ],
    [
        'id' => 'questions-flashcards',
        'title' => 'Questions & Flashcards',
        'description' => 'Review questions with answers and explanations, or use recall flashcards.',
        'modifier' => 'study',
        'chapters' => $studybankchapters,
    ],
    [
        'id' => 'quiz-resources',
        'title' => 'Quiz',
        'description' => 'Choose answers and receive immediate feedback.',
        'modifier' => 'quiz',
        'chapters' => $quizchapters,
    ],
    [
        'id' => 'comparisons',
        'title' => 'Comparisons',
        'description' => 'Open the comparative study collections.',
        'modifier' => 'comparisons',
        'chapters' => $comparisonoverviewchapters,
    ],
    [
        'id' => 'reference-library',
        'title' => 'Reference Library',
        'description' => 'Open families, safety, theory and supplemental study resources.',
        'modifier' => 'reference',
        'chapters' => $otherchapters,
    ],
];

$baseparams = ['area' => $areaid];
if ($ises) {
    $baseparams['lang'] = 'es';
}
$pageurl = new moodle_url('/blocks/myprogress/study-guide.php', $baseparams);
$chapterlisturl = new moodle_url('/blocks/myprogress/study-guide.php', $baseparams);
$libraryparams = $baseparams + ['view' => 'library'];
$libraryurl = new moodle_url('/blocks/myprogress/study-guide.php', $libraryparams);
$singleherbsstudyurl = new moodle_url(
    '/blocks/myprogress/single-herbs-study-guide.php',
    $ises ? ['lang' => 'es'] : []
);
$duiyaostudyurl = new moodle_url(
    $ises ? '/study-guide/comparisons/dui-yao-es.html' : '/study-guide/comparisons/dui-yao.html'
);
$formulasstudyurl = new moodle_url(
    '/blocks/myprogress/formulas-study-guide.php',
    $ises ? ['lang' => 'es'] : []
);
$safetylibraryurl = new moodle_url(
    $ises ? '/study-guide/comparisons/safety-es.html' : '/study-guide/comparisons/safety.html'
);
$singleherbscomparisonsurl = new moodle_url(
    $ises ? '/study-guide/comparisons/index-es.html' : '/study-guide/comparisons/index.html'
);
$formulacomparisonsurl = new moodle_url(
    $ises ? '/study-guide/comparisons/formulas-es.html' : '/study-guide/comparisons/formulas.html'
);
$formulastudycenterurl = new moodle_url(
    $ises ? '/study-guide/comparisons/formula-navigator-es.html' : '/study-guide/comparisons/formula-navigator.html'
);
$benskycrosswalkurl = new moodle_url(
    '/blocks/myprogress/herb-formula-category-map.php',
    $ises ? ['lang' => 'es'] : []
);
$pathologycomparisonsurl = new moodle_url(
    $ises ? '/study-guide/comparisons/pathologies-es.html' : '/study-guide/comparisons/pathologies.html'
);
$patterncomparisonsurl = new moodle_url(
    $ises ? '/study-guide/comparisons/patterns-es.html' : '/study-guide/comparisons/patterns.html'
);
$patternmaciociacomparisonurl = new moodle_url(
    $ises ? '/study-guide/comparisons/patterns-tips-maciocia-es.html' : '/study-guide/comparisons/patterns-tips-maciocia.html'
);
$boardlanguagecontext = $ises ? ['lang' => 'es'] : [];
$boardpracticeurl = new moodle_url('/blocks/menu/herbology-board.php', ['track' => 'herbs'] + $boardlanguagecontext);
$boardcompletepracticeurl = new moodle_url('/blocks/menu/herbology-board.php', ['track' => 'practice'] + $boardlanguagecontext);
$boardformulasurl = new moodle_url('/blocks/menu/herbology-board.php', ['track' => 'formulas'] + $boardlanguagecontext);
$boarddomainsurl = new moodle_url('/blocks/menu/herbology-board.php', ['track' => 'domains'] + $boardlanguagecontext);
$boardquizurl = new moodle_url('/blocks/menu/herbology-board.php', ['track' => 'quiz'] + $boardlanguagecontext);
$boardherbquizurl = new moodle_url('/blocks/menu/herbology-board.php', ['track' => 'quiz', 'quizsection' => 'herbs'] + $boardlanguagecontext);
$boardformulaquizurl = new moodle_url('/blocks/menu/herbology-board.php', ['track' => 'quiz', 'quizsection' => 'formulas'] + $boardlanguagecontext);
$boardclinicalquizurl = new moodle_url('/blocks/menu/herbology-board.php', ['track' => 'quiz', 'quizsection' => 'clinical'] + $boardlanguagecontext);
$boardclinicalcasesurl = new moodle_url('/blocks/menu/herbology-board.php', ['track' => 'clinicalcases'] + $boardlanguagecontext);
$boardexamsurl = new moodle_url('/blocks/menu/herbology-board.php', ['track' => 'exams'] + $boardlanguagecontext);
$boardlinearexamurl = new moodle_url('/blocks/menu/herbology-board.php', ['track' => 'exams', 'exam' => 'linear'] + $boardlanguagecontext);
$boardadaptiveexamurl = new moodle_url('/blocks/menu/herbology-board.php', ['track' => 'exams', 'exam' => 'adaptive'] + $boardlanguagecontext);
$boardsimulationurl = new moodle_url('/blocks/menu/herbology-board.php', ['track' => 'simulation'] + $boardlanguagecontext);
$boardlinearsimulationurl = new moodle_url('/blocks/menu/herbology-board.php', ['track' => 'simulation', 'exam' => 'linear'] + $boardlanguagecontext);
$boardadaptivesimulationurl = new moodle_url('/blocks/menu/herbology-board.php', ['track' => 'simulation', 'exam' => 'adaptive'] + $boardlanguagecontext);
if ($showconstruction) {
    $pageurl->param('view', 'construction');
} elseif ($showlibrary) {
    $pageurl->param('view', 'library');
} elseif ($catalogview !== '') {
    $pageurl->param('view', $catalogview);
}
$languageparams = ['area' => $areaid];
if ($showlibrary) {
    $languageparams['view'] = 'library';
} elseif ($catalogview !== '') {
    $languageparams['view'] = $catalogview;
}
if (!$ises) {
    $languageparams['lang'] = 'es';
}
$languageurl = new moodle_url('/blocks/myprogress/study-guide.php', $languageparams);
$makechapterurl = static function(array $chapter) use ($ises): moodle_url {
    $path = $chapter['path'];
    $params = $chapter['params'] ?? [];
    if ($ises) {
        if (str_ends_with($path, '.php')) {
            $params['lang'] = 'es';
        } elseif (str_ends_with($path, '.html')) {
            $spanishpath = substr($path, 0, -5) . '-es.html';
            $spanishfile = __DIR__ . '/../../' . ltrim($spanishpath, '/');
            if (is_readable($spanishfile)) {
                $path = $spanishpath;
            }
        }
    }
    $url = new moodle_url($path, $params);
    if ($chapter['anchor'] !== '') {
        $url->set_anchor($chapter['anchor']);
    }
    return $url;
};

$PAGE->set_url($pageurl);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$pagelabel = $showlibrary ? ($ises ? ' · Biblioteca' : ' Library') : ($ises ? ' · Guía de estudio' : ' Study Guide');
$PAGE->set_title($area['title'] . $pagelabel);
$PAGE->set_heading($showlibrary && $areaid === 'herbology' ? '' : $area['title'] . $pagelabel);

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
.subject-study-library__map{padding:22px;border:1px solid #b8d5d2;border-radius:12px;background:#f7fcfb}
.subject-study-library__map-header{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;margin-bottom:16px}.subject-study-library__map-header h3{margin:0 0 5px;color:#123246;font-size:23px}.subject-study-library__map-header p{margin:0;color:#587083;line-height:1.5}.subject-study-library__map-copy{min-width:0}.subject-study-library__map-actions{display:flex;flex:0 0 auto;align-items:center;gap:9px}.subject-study-library__map-actions a{margin:0}
.subject-study-library__journey{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:12px}
.subject-study-library__journey-card{display:flex;min-height:235px;grid-column:span 2;flex-direction:column;padding:16px;border:1px solid #b9cbd6;border-top:4px solid #0b817d;border-radius:11px;background:#fff;color:#123246}
.subject-study-library__journey-card:nth-child(8){border-top-color:#6d4bc3}.subject-study-library__journey-card:nth-child(9){border-top-color:#2563eb}.subject-study-library__journey-card:nth-child(10){border-top-color:#f77e25}
.subject-study-library__journey-heading{display:flex;align-items:flex-start;gap:11px;margin-bottom:13px}
.subject-study-library__journey-step{display:inline-flex;width:31px;height:31px;flex:0 0 31px;align-items:center;justify-content:center;border-radius:50%;background:#0b817d;color:#fff;font-weight:900}
.subject-study-library__journey-card:nth-child(8) .subject-study-library__journey-step{background:#6d4bc3}.subject-study-library__journey-card:nth-child(9) .subject-study-library__journey-step{background:#2563eb}.subject-study-library__journey-card:nth-child(10) .subject-study-library__journey-step{background:#e96512}
.subject-study-library__journey-copy{display:block;min-width:0}.subject-study-library__journey-copy strong,.subject-study-library__journey-copy b{display:block}.subject-study-library__journey-copy strong{font-size:16px;line-height:1.25}.subject-study-library__journey-copy b{margin-top:5px;color:#08746f;font-size:10px;letter-spacing:.05em;text-transform:uppercase}
.subject-study-library__journey-options{display:grid;gap:7px;margin-top:0}
.subject-study-library__journey-option{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:8px;padding:9px 10px;border:1px solid #c8d5de;border-radius:8px;background:#f8fafc;color:#173f58;font-weight:850;line-height:1.25;text-decoration:none!important}
.subject-study-library__journey-option small{display:block;margin-top:4px;color:#64748b;font-size:13px;font-weight:600;line-height:1.35}.subject-study-library__journey-option small.subject-study-library__journey-option-heading{color:inherit;font-size:inherit;font-weight:inherit;line-height:1.25}.subject-study-library__journey-option span:last-child{align-self:center;font-size:18px}
.subject-study-library__journey-option:hover,.subject-study-library__journey-option:focus{border-color:#0b817d;background:#edf9f6;color:#075f5b}
.subject-study-library__journey-option--primary{min-height:46px;align-items:center;justify-content:center;margin-bottom:3px;background:#f7fcfb;font-size:14px;text-align:center}.subject-study-library__journey-option--primary:hover,.subject-study-library__journey-option--primary:focus{background:#0b817d;color:#fff}
.subject-study-library__search{display:grid;grid-template-columns:minmax(0,1fr) auto;align-items:end;gap:12px;padding:18px;border:1px solid #d4dde4;border-radius:11px;background:#fff}.subject-study-library__search label{display:grid;gap:6px;color:#213e51;font-weight:800}.subject-study-library__search input{width:100%;min-height:48px;box-sizing:border-box;padding:10px 13px;border:1px solid #9fb3c1;border-radius:8px;background:#fff;color:#17232a;font-size:16px}.subject-study-library__search input:focus{border-color:#2563eb;outline:3px solid rgba(37,99,235,.16)}.subject-study-library__search-count{padding-bottom:12px;color:#52677a;font-size:13px;font-weight:800;white-space:nowrap}
.subject-study-library__empty{margin:0;padding:18px;border:1px dashed #9fb3c1;border-radius:10px;background:#f8fafc;color:#52677a;text-align:center}
.subject-study-library__group{padding:22px;border:1px solid #d8e1e7;border-radius:12px;background:#fff}
.subject-study-library__group[hidden]{display:none}
.subject-study-library__group-header{display:flex;align-items:flex-end;justify-content:space-between;gap:14px;margin-bottom:15px}
.subject-study-library__group-header h3{margin:0 0 4px;color:#17232a;font-size:21px}
.subject-study-library__group-header p{margin:0;color:#64748b}
.subject-study-library__count{flex:0 0 auto;color:#52677a;font-size:13px;font-weight:800}
.subject-study-library__buttons{display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:10px}
.subject-study-library__button{display:grid;min-height:104px;grid-template-columns:minmax(0,1fr) auto;align-items:center;gap:12px;padding:14px;border:1px solid #b8c8d5;border-radius:10px;background:#fff;color:#123246;text-align:left;text-decoration:none!important}
.subject-study-library__button[hidden]{display:none}.subject-study-library__button-copy{display:block;min-width:0}.subject-study-library__button-title,.subject-study-library__button-description,.subject-study-library__button-status{display:block}.subject-study-library__button-title{font-weight:900;line-height:1.3}.subject-study-library__button-description{display:-webkit-box;overflow:hidden;margin-top:5px;color:#607487;font-size:12px;font-weight:500;line-height:1.4;-webkit-box-orient:vertical;-webkit-line-clamp:2}.subject-study-library__button-status{margin-top:7px;color:#08746f;font-size:10px;font-weight:900;letter-spacing:.04em;text-transform:uppercase}.subject-study-library__button-arrow{font-size:20px;font-weight:900}
.subject-study-library__button:hover{border-color:#2563eb;background:#eff6ff;color:#1746a2;text-decoration:none}
.subject-study-library__group--primary{border-top:4px solid #2563eb}
.subject-study-library__group--study{border-top:4px solid #0b817d}
.subject-study-library__group--quiz{border-top:4px solid #6d4bc3}
.subject-study-library__group--comparisons{border-top:4px solid #f77e25}
.subject-study-library__group--reference{border-top:4px solid #64748b}
.subject-study-library__group--study .subject-study-library__button{border-color:#8bc8bd;background:#f4fbf9}
.subject-study-library__group--quiz .subject-study-library__button{border-color:#c6b5ef;background:#f8f5ff}
.subject-study-library__group--comparisons .subject-study-library__button{border-color:#f4b27e;background:#fffaf6}
.subject-study-library__tips{display:flex;align-items:center;justify-content:space-between;gap:18px;margin:0 0 20px;padding:16px 18px;border:1px solid #efc7a8;border-left:5px solid #f37424;border-radius:11px;background:#fff8f2;text-align:left}.subject-study-library__tips strong,.subject-study-library__tips span{display:block}.subject-study-library__tips span{margin-top:3px;color:#755844;font-size:.9rem}.subject-study-library__tips a{display:inline-flex;min-height:44px;flex:0 0 auto;align-items:center;justify-content:center;padding:10px 15px;border-radius:8px;background:#c9570c;color:#fff!important;font-weight:800;text-decoration:none!important}.subject-study-library__tips a:hover,.subject-study-library__tips a:focus{background:#a74408}
.subject-study-library__footer{margin-top:6px;padding-top:8px;border-top:1px solid #d8e2e7}.subject-study-library__footer .subject-study-library__tips{margin-bottom:0}
.subject-study-guide__construction{padding:clamp(36px,8vw,76px) 22px;border:1px dashed #aeb8c0;border-radius:14px;background:#fff;text-align:center}
.subject-study-guide__construction-mark{margin-bottom:15px;font-size:48px}
.subject-study-guide__construction h3{margin:0 0 12px;color:#a64f12;font-size:28px}
.subject-study-guide__construction p{max-width:570px;margin:0 auto 26px;color:#64748b;line-height:1.65}
.subject-study-guide a:focus-visible{outline:3px solid #185cca;outline-offset:3px}
@media(max-width:700px){.subject-study-guide{padding:0 10px}.subject-study-guide__top{display:block}.subject-study-guide__top-actions{align-items:stretch;flex-direction:column;margin-top:16px}.subject-study-guide__top-actions a{width:100%;box-sizing:border-box}.subject-study-guide__back{margin-top:0}.subject-study-guide__board-overview{padding:26px 22px}.subject-study-guide__benefits{grid-template-columns:1fr}.subject-study-guide__grid,.subject-study-guide__grid--herbology{grid-template-columns:1fr}.subject-study-guide__future{grid-column:auto}.subject-study-library__intro{padding:22px 18px}.subject-study-library__map,.subject-study-library__group{padding:17px}.subject-study-library__map-header{align-items:stretch;flex-direction:column}.subject-study-library__map-actions{display:grid;grid-template-columns:1fr 1fr}.subject-study-library__map-actions a{width:100%;box-sizing:border-box;text-align:center}.subject-study-library__map-actions .subject-study-library__dashboard{grid-column:1/-1;justify-content:center}.subject-study-library__journey{grid-template-columns:1fr}.subject-study-library__journey-card{min-height:0;grid-column:auto}.subject-study-library__journey-options{margin-top:0}.subject-study-library__search{grid-template-columns:1fr}.subject-study-library__search-count{padding:0}.subject-study-library__tips{align-items:stretch;flex-direction:column}.subject-study-library__tips a{width:100%;box-sizing:border-box}.subject-study-library__group-header{align-items:flex-start;flex-direction:column}.subject-study-library__buttons{grid-template-columns:1fr}.subject-study-library__button{min-height:96px;padding:13px}}
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
    <?php if (!$showlibrary || $areaid !== 'herbology'): ?>
    <header class="subject-study-guide__top">
        <div>
            <p class="subject-study-guide__eyebrow"><?php echo s($tr($showlibrary ? 'Study Library' : 'Study Guide')); ?></p>
            <h2><?php echo s($area['title'] . ($showlibrary ? ($ises ? ' · Biblioteca' : ' Library') : '')); ?></h2>
            <p class="subject-study-guide__intro"><?php echo $showlibrary ? s($ises ? 'Abre todos los recursos Board Level desde una biblioteca de acceso rápido.' : 'Open every Board Level resource from one quick-access library.') : s($area['description']); ?></p>
        </div>
        <nav class="subject-study-guide__top-actions" aria-label="<?php echo s($areaid === 'herbology'
            ? ($ises ? 'Regresar a las actividades Board Level' : 'Return to Board Level activities')
            : 'Study Guide navigation'); ?>">
            <?php if ($areaid === 'herbology'): ?>
                <a class="subject-study-guide__back" href="<?php echo $libraryurl->out(false); ?>"><?php echo s($ises
                    ? '← Actividades Board Level' : '← Board Level Activities'); ?></a>
                <a class="subject-study-guide__back" hreflang="<?php echo $ises ? 'en' : 'es'; ?>"
                    href="<?php echo $languageurl->out(false); ?>"><?php echo $ises ? 'English' : 'Español'; ?></a>
            <?php else: ?>
                <a class="subject-study-guide__back" href="<?php echo (new moodle_url('/my/'))->out(false); ?>"><?php echo s($tr('← Back to Dashboard')); ?></a>
            <?php endif; ?>
        </nav>
    </header>
    <?php endif; ?>

    <?php if ($showlibrary): ?>
        <section class="subject-study-library" aria-labelledby="<?php echo $areaid === 'herbology' ? 'library-navigation-map-title' : 'study-library-title'; ?>">
            <?php if ($areaid !== 'herbology'): ?>
                <header class="subject-study-library__intro">
                    <p class="subject-study-guide__eyebrow"><?php echo s($ises ? 'Acceso rápido' : 'Quick access'); ?></p>
                    <h3 id="study-library-title"><?php echo s($area['title'] . ($ises ? ' · Biblioteca' : ' Library')); ?></h3>
                    <p><?php echo s($ises ? 'Usa el mapa para seguir el recorrido Board Level o busca directamente entre todos los recursos disponibles.' : 'Use the map to follow the Board Level study path or search directly across every available resource.'); ?></p>
                </header>
            <?php endif; ?>
            <?php if ($areaid === 'herbology'): ?>
                <section class="subject-study-library__map" aria-labelledby="library-navigation-map-title">
                    <header class="subject-study-library__map-header">
                        <div class="subject-study-library__map-copy">
                            <h3 id="library-navigation-map-title"><?php echo s($ises ? 'Elige una actividad Board Level' : 'Choose a Board Level activity'); ?></h3>
                            <p><?php echo s($ises
                                ? 'Cada bloque contiene las opciones que realmente pertenecen a esa actividad.'
                                : 'Each block contains the actual options that belong to that activity.'); ?></p>
                        </div>
                        <nav class="subject-study-library__map-actions" aria-label="<?php echo s($ises ? 'Controles de navegación' : 'Navigation controls'); ?>">
                            <a class="subject-study-guide__back" hreflang="<?php echo $ises ? 'en' : 'es'; ?>" href="<?php echo $languageurl->out(false); ?>"><?php echo $ises ? 'English' : 'Español'; ?></a>
                            <a class="subject-study-guide__back" data-library-page-back href="<?php echo (new moodle_url('/my/'))->out(false); ?>"><?php echo s($ises ? '← Página anterior' : '← Previous page'); ?></a>
                            <a class="subject-study-guide__back subject-study-library__dashboard" data-library-dashboard href="<?php echo (new moodle_url('/my/'))->out(false); ?>">Dashboard</a>
                        </nav>
                    </header>
                    <div class="subject-study-library__journey" aria-label="<?php echo s($ises ? 'Actividades Board Level' : 'Board Level activities'); ?>">
                        <section class="subject-study-library__journey-card">
                            <header class="subject-study-library__journey-heading">
                                <span class="subject-study-library__journey-step">1</span>
                                <span class="subject-study-library__journey-copy">
                                    <strong><?php echo s($ises ? 'Guía de estudio principal' : 'Main Study Guide'); ?></strong>
                                    <b><?php echo s($ises ? 'Aprender el contenido' : 'Learn the material'); ?></b>
                                </span>
                            </header>
                            <nav class="subject-study-library__journey-options" aria-label="<?php echo s($ises ? 'Guía de estudio principal' : 'Main Study Guide'); ?>">
                                <a class="subject-study-library__journey-option" href="<?php echo $singleherbsstudyurl->out(false); ?>">
                                    <span><?php echo s($ises ? 'Guía de estudio de hierbas individuales' : 'Single Herbs Study Guide'); ?><small class="subject-study-library__journey-option-heading"><?php echo s($ises ? 'Categorías' : 'Categories'); ?></small></span><span aria-hidden="true">›</span>
                                </a>
                                <a class="subject-study-library__journey-option" href="<?php echo $duiyaostudyurl->out(false); ?>">
                                    <span><?php echo s($ises ? 'Guía de estudio de pares Duì Yào' : 'Duì Yào Pairs Study Guide'); ?><small class="subject-study-library__journey-option-heading"><?php echo s($ises ? 'Categorías' : 'Categories'); ?></small></span><span aria-hidden="true">›</span>
                                </a>
                                <a class="subject-study-library__journey-option" href="<?php echo $formulasstudyurl->out(false); ?>">
                                    <span><?php echo s($ises ? 'Guía de estudio de fórmulas' : 'Formulas Study Guide'); ?><small class="subject-study-library__journey-option-heading"><?php echo s($ises ? 'Categorías' : 'Categories'); ?></small></span><span aria-hidden="true">›</span>
                                </a>
                                <a class="subject-study-library__journey-option" href="<?php echo $safetylibraryurl->out(false); ?>">
                                    <span><?php echo s($tr('Safety Library')); ?><small><?php echo s($ises ? '12 secciones de seguridad' : '12 safety sections'); ?></small></span><span aria-hidden="true">›</span>
                                </a>
                            </nav>
                        </section>

                        <section class="subject-study-library__journey-card">
                            <header class="subject-study-library__journey-heading">
                                <span class="subject-study-library__journey-step">2</span>
                                <span class="subject-study-library__journey-copy">
                                    <strong><?php echo s($ises ? 'Comparación' : 'Comparison'); ?></strong>
                                    <b><?php echo s($ises ? 'Material complementario' : 'Supplemental material'); ?></b>
                                </span>
                            </header>
                            <nav class="subject-study-library__journey-options" aria-label="<?php echo s($ises ? 'Comparación' : 'Comparison'); ?>">
                                <a class="subject-study-library__journey-option" href="<?php echo $singleherbscomparisonsurl->out(false); ?>">
                                    <span><?php echo s($ises ? 'Comparaciones de hierbas individuales' : 'Single Herbs Comparisons'); ?><small><?php echo s($ises ? 'Estudio comparativo de hierbas individuales' : 'Comparative single-herb study'); ?></small></span><span aria-hidden="true">›</span>
                                </a>
                                <a class="subject-study-library__journey-option" href="<?php echo $formulacomparisonsurl->out(false); ?>">
                                    <span><?php echo s($ises ? 'Comparaciones de fórmulas' : 'Formula Comparisons'); ?><small><?php echo s($ises ? 'Estudio comparativo de fórmulas' : 'Comparative formula study'); ?></small></span><span aria-hidden="true">›</span>
                                </a>
                                <a class="subject-study-library__journey-option" href="<?php echo $formulastudycenterurl->out(false); ?>">
                                    <span><?php echo s($tr('63 Formulas Comparison')); ?><small><?php echo s($ises ? 'Estudio conectado de fórmulas' : 'Connected formula study'); ?></small></span><span aria-hidden="true">›</span>
                                </a>
                                <a class="subject-study-library__journey-option" href="<?php echo $benskycrosswalkurl->out(false); ?>">
                                    <span><?php echo s($tr('Bensky Category Crosswalk')); ?><small><?php echo s($ises ? 'Compara categorías de hierbas y fórmulas' : 'Compare herb and formula categories'); ?></small></span><span aria-hidden="true">›</span>
                                </a>
                                <a class="subject-study-library__journey-option" href="<?php echo $pathologycomparisonsurl->out(false); ?>">
                                    <span><?php echo s($tr('Pathology Comparisons')); ?><small><?php echo s($ises ? '66 comparaciones clínicas' : '66 clinical comparisons'); ?></small></span><span aria-hidden="true">›</span>
                                </a>
                                <a class="subject-study-library__journey-option" href="<?php echo $patterncomparisonsurl->out(false); ?>">
                                    <span><?php echo s($tr('Pattern Comparisons')); ?><small><?php echo s($ises ? '50 comparaciones de patrones' : '50 pattern comparisons'); ?></small></span><span aria-hidden="true">›</span>
                                </a>
                                <a class="subject-study-library__journey-option" href="<?php echo $patternmaciociacomparisonurl->out(false); ?>">
                                    <span><?php echo s($tr('Pattern Comparison Maciocia')); ?><small><?php echo s($ises ? '100 consejos clínicos de patrones' : '100 clinical pattern tips'); ?></small></span><span aria-hidden="true">›</span>
                                </a>
                            </nav>
                        </section>

                        <section class="subject-study-library__journey-card">
                            <header class="subject-study-library__journey-heading">
                                <span class="subject-study-library__journey-step">3</span>
                                <span class="subject-study-library__journey-copy">
                                    <strong><?php echo s($ises ? 'Comparación de patologías y patrones' : 'Comparison Pathology and Patterns'); ?></strong>
                                    <b><?php echo s($ises ? 'Comparaciones clínicas' : 'Clinical comparisons'); ?></b>
                                </span>
                            </header>
                            <nav class="subject-study-library__journey-options" aria-label="<?php echo s($ises ? 'Comparación de patologías y patrones' : 'Comparison Pathology and Patterns'); ?>">
                            </nav>
                        </section>

                        <section class="subject-study-library__journey-card">
                            <header class="subject-study-library__journey-heading">
                                <span class="subject-study-library__journey-step">4</span>
                                <span class="subject-study-library__journey-copy">
                                    <strong><?php echo s($ises ? 'Guía de estudio secundaria' : 'Secondary Study Guide'); ?></strong>
                                    <b><?php echo s($ises ? 'Material de estudio adicional' : 'Additional study material'); ?></b>
                                </span>
                            </header>
                            <nav class="subject-study-library__journey-options" aria-label="<?php echo s($ises ? 'Guía de estudio secundaria' : 'Secondary Study Guide'); ?>">
                                <?php foreach ($secondaryguidechapters as $chapter): ?>
                                    <?php $secondarychapterurl = $makechapterurl($chapter); ?>
                                    <a class="subject-study-library__journey-option" href="<?php echo $secondarychapterurl->out(false); ?>">
                                        <span><?php echo s($chapter['title']); ?><small><?php echo s($chapter['status'] ?? $chapter['description']); ?></small></span><span aria-hidden="true">›</span>
                                    </a>
                                <?php endforeach; ?>
                            </nav>
                        </section>

                        <section class="subject-study-library__journey-card">
                            <header class="subject-study-library__journey-heading">
                                <span class="subject-study-library__journey-step">5</span>
                                <span class="subject-study-library__journey-copy">
                                    <strong><?php echo s($ises ? 'Misceláneos de hierbas' : 'Herbs Miscellaneous'); ?></strong>
                                    <b><?php echo s($ises ? 'Material adicional de hierbas' : 'Additional herbs'); ?></b>
                                </span>
                            </header>
                        </section>

                        <section class="subject-study-library__journey-card">
                            <header class="subject-study-library__journey-heading">
                                <span class="subject-study-library__journey-step">6</span>
                                <span class="subject-study-library__journey-copy">
                                    <strong><?php echo s($ises ? 'Misceláneos de fórmulas' : 'Formulas Miscellaneous'); ?></strong>
                                    <b><?php echo s($ises ? 'Material adicional de fórmulas' : 'Additional formulas'); ?></b>
                                </span>
                            </header>
                        </section>

                        <section class="subject-study-library__journey-card">
                            <header class="subject-study-library__journey-heading">
                                <span class="subject-study-library__journey-step">7</span>
                                <span class="subject-study-library__journey-copy">
                                    <strong>Herbology Practice</strong>
                                    <b><?php echo s($ises ? 'Practicar con respuestas' : 'Practice with answers'); ?></b>
                                </span>
                            </header>
                            <nav class="subject-study-library__journey-options" aria-label="Herbology Practice">
                                <a class="subject-study-library__journey-option" href="<?php echo $boardpracticeurl->out(false); ?>"><span><?php echo s($ises ? 'Práctica de hierbas individuales' : 'Single Herbs Practice'); ?><small><?php echo s($ises ? 'Preguntas con respuestas visibles' : 'Questions with visible answers'); ?></small></span><span aria-hidden="true">›</span></a>
                                <a class="subject-study-library__journey-option" href="<?php echo $boardformulasurl->out(false); ?>"><span><?php echo s($ises ? 'Práctica de fórmulas' : 'Formulas Practice'); ?><small><?php echo s($ises ? 'Estudio pasivo de fórmulas' : 'Passive formula study'); ?></small></span><span aria-hidden="true">›</span></a>
                                <a class="subject-study-library__journey-option" href="<?php echo $boarddomainsurl->out(false); ?>"><span><?php echo s($ises ? 'Práctica por dominios' : 'Domains Practice'); ?><small><?php echo s($ises ? 'Repaso pasivo por dominio' : 'Passive review by domain'); ?></small></span><span aria-hidden="true">›</span></a>
                            </nav>
                        </section>

                        <section class="subject-study-library__journey-card">
                            <header class="subject-study-library__journey-heading">
                                <span class="subject-study-library__journey-step">8</span>
                                <span class="subject-study-library__journey-copy">
                                    <strong>Quiz</strong>
                                    <b><?php echo s($ises ? 'Comprobar lo aprendido' : 'Check your learning'); ?></b>
                                </span>
                            </header>
                            <nav class="subject-study-library__journey-options" aria-label="Quiz">
                                <a class="subject-study-library__journey-option" href="<?php echo $boardherbquizurl->out(false); ?>"><span><?php echo s($ises ? 'Quiz de hierbas individuales' : 'Single Herbs Quiz'); ?><small><?php echo s($ises ? 'Feedback inmediato' : 'Immediate feedback'); ?></small></span><span aria-hidden="true">›</span></a>
                                <a class="subject-study-library__journey-option" href="<?php echo $boardformulaquizurl->out(false); ?>"><span><?php echo s($ises ? 'Quiz de fórmulas' : 'Formula Quiz'); ?><small><?php echo s($ises ? 'Feedback inmediato' : 'Immediate feedback'); ?></small></span><span aria-hidden="true">›</span></a>
                                <a class="subject-study-library__journey-option" href="<?php echo $boardclinicalquizurl->out(false); ?>"><span><?php echo s($ises ? 'Casos clínicos' : 'Clinical Cases'); ?><small><?php echo s($ises ? 'Elige un caso y recibe feedback' : 'Choose a case and receive feedback'); ?></small></span><span aria-hidden="true">›</span></a>

                            </nav>
                        </section>

                        <section class="subject-study-library__journey-card">
                            <header class="subject-study-library__journey-heading">
                                <span class="subject-study-library__journey-step">9</span>
                                <span class="subject-study-library__journey-copy">
                                    <strong><?php echo s($ises ? 'Exámenes' : 'Exams'); ?></strong>
                                    <b><?php echo s($ises ? 'Medir tu preparación' : 'Measure your readiness'); ?></b>
                                </span>
                            </header>
                            <nav class="subject-study-library__journey-options" aria-label="<?php echo s($ises ? 'Exámenes' : 'Exams'); ?>">
                                <a class="subject-study-library__journey-option" href="<?php echo $boardlinearexamurl->out(false); ?>"><span><?php echo s($ises ? 'Examen lineal' : 'Linear exam'); ?><small><?php echo s($ises ? '100 preguntas · historial' : '100 questions · history'); ?></small></span><span aria-hidden="true">›</span></a>
                                <a class="subject-study-library__journey-option" href="<?php echo $boardadaptiveexamurl->out(false); ?>"><span><?php echo s($ises ? 'Examen adaptativo' : 'Adaptive exam'); ?><small><?php echo s($ises ? '100 preguntas · historial' : '100 questions · history'); ?></small></span><span aria-hidden="true">›</span></a>
                                <a class="subject-study-library__journey-option" href="<?php echo $boardclinicalcasesurl->out(false); ?>"><span><?php echo s($ises ? 'Examen de casos clínicos' : 'Clinical Cases exam'); ?><small><?php echo s($ises ? '63 preguntas · historial' : '63 questions · history'); ?></small></span><span aria-hidden="true">›</span></a>
                                <a class="subject-study-library__journey-option subject-study-library__journey-option--primary" href="<?php echo $boardexamsurl->out(false); ?>">
                                    <span><?php echo s($ises ? 'Abrir todos los exámenes' : 'Open all Exams'); ?><small><?php echo s($ises ? 'Abre el módulo completo de exámenes' : 'Opens the complete Exams module'); ?></small></span><span aria-hidden="true">›</span>
                                </a>
                            </nav>
                        </section>

                        <section class="subject-study-library__journey-card">
                            <header class="subject-study-library__journey-heading">
                                <span class="subject-study-library__journey-step">10</span>
                                <span class="subject-study-library__journey-copy">
                                    <strong><?php echo s($ises ? 'Simulación' : 'Simulation'); ?></strong>
                                    <b><?php echo s($ises ? 'Ensayar el día del examen' : 'Rehearse test day'); ?></b>
                                </span>
                            </header>
                            <nav class="subject-study-library__journey-options" aria-label="<?php echo s($ises ? 'Simulación' : 'Simulation'); ?>">
                                <a class="subject-study-library__journey-option" href="<?php echo $boardlinearsimulationurl->out(false); ?>"><span><?php echo s($ises ? 'Simulación lineal' : 'Linear simulation'); ?><small><?php echo s($ises ? '100 preguntas · 150 minutos' : '100 questions · 150 minutes'); ?></small></span><span aria-hidden="true">›</span></a>
                                <a class="subject-study-library__journey-option" href="<?php echo $boardadaptivesimulationurl->out(false); ?>"><span><?php echo s($ises ? 'Simulación adaptativa' : 'Adaptive simulation'); ?><small><?php echo s($ises ? '100 preguntas · 150 minutos' : '100 questions · 150 minutes'); ?></small></span><span aria-hidden="true">›</span></a>
                                <a class="subject-study-library__journey-option subject-study-library__journey-option--primary" href="<?php echo $boardsimulationurl->out(false); ?>">
                                    <span><?php echo s($ises ? 'Abrir todas las simulaciones' : 'Open all Simulations'); ?><small><?php echo s($ises ? 'Abre el módulo completo de simulación' : 'Opens the complete Simulation module'); ?></small></span><span aria-hidden="true">›</span>
                                </a>
                            </nav>
                        </section>
                    </div>
                </section>
            <?php else: ?>
            <?php foreach ($librarygroups as $group): ?>
                <?php if (!empty($group['chapters'])): ?>
                    <section id="library-group-<?php echo s($group['id']); ?>"
                        class="subject-study-library__group subject-study-library__group--<?php echo s($group['modifier']); ?>"
                        data-library-group>
                        <header class="subject-study-library__group-header">
                            <div>
                                <h3><?php echo s($tr($group['title'])); ?></h3>
                                <p><?php echo s($tr($group['description'])); ?></p>
                            </div>
                            <?php $groupcount = count($group['chapters']); ?>
                            <span class="subject-study-library__count"><span data-library-group-count><?php echo $groupcount; ?></span> <span data-library-group-unit><?php echo $ises ? ($groupcount === 1 ? 'recurso' : 'recursos') : ($groupcount === 1 ? 'resource' : 'resources'); ?></span></span>
                        </header>
                        <nav class="subject-study-library__buttons" aria-label="<?php echo s($group['title']); ?>">
                            <?php foreach ($group['chapters'] as $chapter): ?>
                                <?php
                                $chapterurl = $makechapterurl($chapter);
                                if ($areaid === 'herbology') {
                                    $chapterurl->param('from', 'library');
                                }
                                $searchtext = implode(' ', [
                                    $chapter['title'],
                                    $chapter['description'],
                                    $chapter['status'] ?? '',
                                    $group['title'],
                                ]);
                                ?>
                                <a class="subject-study-library__button" href="<?php echo $chapterurl->out(false); ?>"
                                    title="<?php echo s($chapter['description']); ?>" data-library-resource
                                    data-library-search-text="<?php echo s($searchtext); ?>">
                                    <span class="subject-study-library__button-copy">
                                        <strong class="subject-study-library__button-title"><?php echo s($chapter['title']); ?></strong>
                                        <span class="subject-study-library__button-description"><?php echo s($chapter['description']); ?></span>
                                        <?php if (!empty($chapter['status'])): ?>
                                            <small class="subject-study-library__button-status"><?php echo s($chapter['status']); ?></small>
                                        <?php endif; ?>
                                    </span>
                                    <span class="subject-study-library__button-arrow" aria-hidden="true">›</span>
                                </a>
                            <?php endforeach; ?>
                        </nav>
                    </section>
                <?php endif; ?>
            <?php endforeach; ?>
            <?php endif; ?>
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
        <?php if ($catalogview === '' || $catalogview === 'guides'): ?>
        <?php foreach ($primarychapters as $chapter): ?>
            <?php
            $chapterurl = $makechapterurl($chapter);
            ?>
            <article class="subject-study-guide__card<?php echo !empty($chapter['priority']) ? ' subject-study-guide__card--priority' : ''; ?>">
                <span class="subject-study-guide__status"><?php echo s($chapter['status'] ?? $tr('AVAILABLE')); ?></span>
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
                <a class="subject-study-guide__open" href="<?php echo $chapterurl->out(false); ?>"><?php echo s($tr('Open')); ?></a>
            </article>
        <?php endforeach; ?>
        <?php endif; ?>

        <?php if (($catalogview === '' || $catalogview === 'practice') && !empty($practicechapters)): ?>
            <section class="subject-study-guide__other subject-study-guide__practice" aria-labelledby="practice-exams-title">
                <header class="subject-study-guide__other-header">
                    <span class="subject-study-guide__status"><?php echo s($tr('PRACTICE')); ?></span>
                    <h3 id="practice-exams-title"><?php echo s($tr('Practice Exams')); ?></h3>
                    <p><?php echo s($tr('Practice herb comparisons, principal formulas and their clinical modifications.')); ?></p>
                </header>
                <div class="subject-study-guide__other-grid">
                    <?php foreach ($practicechapters as $chapter): ?>
                        <?php $chapterurl = $makechapterurl($chapter); ?>
                        <article class="subject-study-guide__card">
                            <span class="subject-study-guide__status"><?php echo s($chapter['status'] ?? $tr('AVAILABLE')); ?></span>
                            <h3><?php echo s($chapter['title']); ?></h3>
                            <p><?php echo s($chapter['description']); ?></p>
                            <a class="subject-study-guide__open" href="<?php echo $chapterurl->out(false); ?>"><?php echo s($tr('Open')); ?></a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if (($catalogview === '' || $catalogview === 'comparisons') && !empty($comparisonoverviewchapters)): ?>
            <section class="subject-study-guide__other subject-study-guide__comparisons" aria-labelledby="comparisons-title">
                <header class="subject-study-guide__other-header">
                    <span class="subject-study-guide__status"><?php echo s($tr('COMPARATIVE STUDY')); ?></span>
                    <h3 id="comparisons-title"><?php echo s($tr('Comparisons')); ?></h3>
                    <p><?php echo s($tr('Compare Bensky categories, pathologies and Chinese medicine patterns.')); ?></p>
                </header>
                <div class="subject-study-guide__other-grid">
                    <?php foreach ($comparisonoverviewchapters as $chapter): ?>
                        <?php
                        $chapterurl = $makechapterurl($chapter);
                        ?>
                        <article class="subject-study-guide__card">
                            <span class="subject-study-guide__status"><?php echo s($chapter['status'] ?? $tr('AVAILABLE')); ?></span>
                            <h3><?php echo s($chapter['title']); ?></h3>
                            <p><?php echo s($chapter['description']); ?></p>
                            <a class="subject-study-guide__open" href="<?php echo $chapterurl->out(false); ?>"><?php echo s($tr('Open')); ?></a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if (($catalogview === '' || $catalogview === 'other') && !empty($otherchapters)): ?>
            <section class="subject-study-guide__other" aria-labelledby="other-chapters-title">
                <header class="subject-study-guide__other-header">
                    <span class="subject-study-guide__status"><?php echo s($tr('ADDITIONAL STUDY')); ?></span>
                    <h3 id="other-chapters-title"><?php echo s($tr('Other chapters')); ?></h3>
                    <p><?php echo s($tr('Additional source-based chapters that complement the principal study guides.')); ?></p>
                </header>
                <div class="subject-study-guide__other-grid">
                    <?php foreach ($otherchapters as $chapter): ?>
                        <?php
                        $chapterurl = $makechapterurl($chapter);
                        ?>
                        <article class="subject-study-guide__card">
                            <span class="subject-study-guide__status"><?php echo s($chapter['status'] ?? $tr('AVAILABLE')); ?></span>
                            <h3><?php echo s($chapter['title']); ?></h3>
                            <p><?php echo s($chapter['description']); ?></p>
                            <a class="subject-study-guide__open" href="<?php echo $chapterurl->out(false); ?>"><?php echo s($tr('Open')); ?></a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php elseif (($catalogview === '' || $catalogview === 'other') && empty($otherchapters)): ?>
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
<?php if ($showlibrary && $areaid === 'herbology'): ?>
<script>
(() => {
    const stepBackScrollKey = 'herbology-step-back-scroll';
    const revealStepBackDestination = event => {
        try {
            if (window.sessionStorage.getItem(stepBackScrollKey) !== 'top') {
                return;
            }
            window.sessionStorage.removeItem(stepBackScrollKey);
            if (event?.persisted) {
                window.location.reload();
                return;
            }
            window.scrollTo(0, 0);
            window.requestAnimationFrame(() => window.scrollTo(0, 0));
        } catch (error) {
            // Library remains usable when session storage is unavailable.
        }
    };
    window.addEventListener('pageshow', revealStepBackDestination);
    revealStepBackDestination();
    const pageBack = document.querySelector('[data-library-page-back]');
    pageBack?.addEventListener('click', event => {
        if (window.history.length > 1) {
            event.preventDefault();
            try {
                window.sessionStorage.setItem(stepBackScrollKey, 'top');
            } catch (error) {
                // History navigation still works when session storage is unavailable.
            }
            window.history.back();
        }
    });
})();
</script>
<?php endif; ?>
<?php
echo $OUTPUT->footer();
