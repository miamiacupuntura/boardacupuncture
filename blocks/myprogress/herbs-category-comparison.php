<?php
// Category-by-category herb comparison library.

require_once(__DIR__ . '/../../config.php');

require_login();

$requestedui = optional_param('ui', '', PARAM_ALPHA);
$requestedlang = optional_param('lang', '', PARAM_ALPHANUMEXT);
$moodlelang = current_language();
$displaylang = $requestedui !== '' ? $requestedui : ($requestedlang !== '' ? $requestedlang : $moodlelang);
$ises = preg_match('/^es(?:[_-].*)?$/i', $displaylang) === 1;

$categoryids = [
    'disperse-wind-cold',
    'disperse-wind-heat',
    'clear-and-purge-heat',
    'clear-blood-heat',
    'clear-damp-heat',
    'clear-toxic-heat',
    'clear-deficient-heat',
    'purgatives',
    'laxatives',
    'cathartics',
    'diuretics',
    'dispel-wind-damp',
    'dissolve-hot-phlegm',
    'dissolve-cold-phlegm',
    'stop-cough-wheezing',
    'aromatics-for-damp',
    'digestives',
    'regulate-qi',
    'stop-bleeding',
    'activate-blood',
    'warm-interior',
    'qi-tonics',
    'blood-tonics',
    'yang-tonics',
    'yin-tonics',
    'astringents',
    'anchor-calm-shen',
    'nourish-calm-shen',
    'orifice-opening-aromatics',
    'extinguish-liver-wind',
    'expel-parasites',
    'external-use',
];
$selectedcategory = optional_param('category', '', PARAM_ALPHANUMEXT);
if (!in_array($selectedcategory, $categoryids, true)) {
    $selectedcategory = '';
}

function board_herb_category_localize(string $text, bool $ises): string {
    if (!$ises || $text === '' || $text === '—') {
        return $text;
    }

    static $translations = [
        "1. Disperse Wind-Cold" => "1. Dispersar Viento-Frío",
        "2. Disperse Wind-Heat" => "2. Dispersar Viento-Calor",
        "3. Clear and Purge Heat" => "3. Limpiar y purgar Calor",
        "4. Clear Blood-Heat" => "4. Limpiar Calor de la Sangre",
        "5. Clear Damp-Heat" => "5. Limpiar Humedad-Calor",
        "6. Clear Toxic-Heat" => "6. Limpiar Calor Tóxico",
        "7. Clear Deficient-Heat" => "7. Limpiar Calor por Deficiencia",
        "8. Purgatives" => "8. Purgantes",
        "9. Laxatives" => "9. Laxantes",
        "10. Cathartics" => "10. Catárticos",
        "11. Diuretics" => "11. Diuréticos",
        "12. Dispel Wind-Damp" => "12. Dispersar Viento-Humedad",
        "Similarities and Clinical Overlaps" => "Semejanzas y superposiciones clínicas",
        "Shared clinical relationship" => "Relación clínica compartida",
        "Herb / Pharmaceutical name" => "Hierba / nombre farmacéutico",
        "Main functions and indications" => "Funciones e indicaciones principales",
        "Cautions / contraindications" => "Precauciones / contraindicaciones",
        "Selection focus:" => "Enfoque de selección:",
        "Common foundation:" => "Fundamento común:",
        "Board Guideline 2026" => "Guía Board 2026",
        "Comparison" => "Comparación",
        "Herbs" => "Hierbas",
        "Taste" => "Sabor",
        "Nature" => "Naturaleza",
        "Channels" => "Canales",
        "Dosage" => "Dosis",
        "Preparation" => "Preparación",
        "Strong exterior release / diaphoresis" => "Liberación exterior intensa / diaforesis",
        "Gentle Wind-Cold release" => "Liberación suave de Viento-Frío",
        "Wind-Cold-Damp pain and headache" => "Dolor y cefalea por Viento-Frío-Humedad",
        "Nasal congestion and sinus discharge" => "Congestión nasal y secreción sinusal",
        "Lung Qi, cough and phlegm" => "Qi de Pulmón, tos y flema",
        "Three-burner emphasis" => "Énfasis en los tres calentadores",
        "Painful urinary dribbling" => "Micción dolorosa y entrecortada",
        "Wind-Damp Bi syndrome" => "Síndrome Bi por Viento-Humedad",
        "Lower body versus upper limbs" => "Parte inferior del cuerpo frente a extremidades superiores",
        "Unblock channels and relax sinews" => "Desobstruir canales y relajar tendones",
        "Wind-Damp with Heat" => "Viento-Humedad con Calor",
        "Strengthen Liver, Kidney, sinews and bones" => "Fortalecer Hígado, Riñón, tendones y huesos",
        "Bitter, slightly acrid" => "Amargo, ligeramente picante",
        "Acrid, slightly bitter" => "Picante, ligeramente amargo",
        "Acrid, bitter, salty" => "Picante, amargo, salado",
        "Salty, sweet, bitter" => "Salado, dulce, amargo",
        "Bitter, sweet, salty" => "Amargo, dulce, salado",
        "Sweet, slightly bitter" => "Dulce, ligeramente amargo",
        "Bitter, slightly sweet" => "Amargo, ligeramente dulce",
        "Acrid, sweet" => "Picante, dulce",
        "Acrid, bitter" => "Picante, amargo",
        "Bitter, acrid" => "Amargo, picante",
        "Bitter, sweet" => "Amargo, dulce",
        "Sweet, bitter" => "Dulce, amargo",
        "Bitter, salty" => "Amargo, salado",
        "Sweet, salty" => "Dulce, salado",
        "Sweet, bland" => "Dulce, insípido",
        "Acrid, salty" => "Picante, salado",
        "Slightly warm, aromatic" => "Ligeramente tibia, aromática",
        "Warm, aromatic" => "Tibia, aromática",
        "Slightly warm" => "Ligeramente tibia",
        "Slightly cold" => "Ligeramente fría",
        "Very cold" => "Muy fría",
        "Warm, toxic" => "Tibia, tóxica",
        "Cold, toxic" => "Fría, tóxica",
        "Warm" => "Tibia",
        "Cold" => "Fría",
        "Neutral" => "Neutra",
        "Aromatic" => "Aromática",
        "Acrid" => "Picante",
        "Bitter" => "Amargo",
        "Sweet" => "Dulce",
        "Salty" => "Salado",
        "Sour" => "Ácido",
        "Bland" => "Insípido",
        "Wind-Cold-Damp" => "Viento-Frío-Humedad",
        "Wind-Cold" => "Viento-Frío",
        "Wind-Heat" => "Viento-Calor",
        "Wind-Damp" => "Viento-Humedad",
        "Damp-Heat" => "Humedad-Calor",
        "Toxic-Heat" => "Calor Tóxico",
        "deficient Heat" => "Calor por Deficiencia",
        "deficiency Heat" => "Calor por Deficiencia",
        "Blood Heat" => "Calor de la Sangre",
        "Lung Heat" => "Calor de Pulmón",
        "Heart Heat" => "Calor de Corazón",
        "Liver Heat" => "Calor de Hígado",
        "Stomach Heat" => "Calor de Estómago",
        "Summer-Heat" => "Calor de Verano",
        "Fire toxicity" => "toxicidad de Fuego",
        "Fire toxin" => "toxina de Fuego",
        "Fire" => "Fuego",
        "Heat" => "Calor",
        "Cold patterns" => "patrones de Frío",
        "Cold pattern" => "patrón de Frío",
        "cold patterns" => "patrones de Frío",
        "cold pattern" => "patrón de Frío",
        "Dampness" => "Humedad",
        "Damp" => "Humedad",
        "Wind" => "Viento",
        "Blood" => "Sangre",
        "Lung" => "Pulmón",
        "Heart" => "Corazón",
        "Liver" => "Hígado",
        "Kidney" => "Riñón",
        "Spleen" => "Bazo",
        "Stomach" => "Estómago",
        "Gallbladder" => "Vesícula Biliar",
        "Large Intestine" => "Intestino Grueso",
        "Small Intestine" => "Intestino Delgado",
        "Intestines" => "Intestinos",
        "Intestine" => "Intestino",
        "upper burner" => "calentador superior",
        "middle burner" => "calentador medio",
        "lower burner" => "calentador inferior",
        "upper body" => "parte superior del cuerpo",
        "lower body" => "parte inferior del cuerpo",
        "upper limbs" => "extremidades superiores",
        "lower back and legs" => "zona lumbar y piernas",
        "painful urinary dribbling" => "micción dolorosa y entrecortada",
        "urinary dribbling" => "goteo urinario",
        "promotes urination" => "promueve la micción",
        "Promotes urination" => "Promueve la micción",
        "promote urination" => "promover la micción",
        "Promote urination" => "Promover la micción",
        "urination" => "micción",
        "urinary" => "urinario",
        "constipation" => "estreñimiento",
        "diarrhea" => "diarrea",
        "vomiting" => "vómitos",
        "nausea" => "náuseas",
        "cough" => "tos",
        "wheezing" => "sibilancias",
        "phlegm" => "flema",
        "mucus" => "mucosidad",
        "headache" => "cefalea",
        "toothache" => "dolor dental",
        "pain" => "dolor",
        "itching" => "picazón",
        "swelling" => "hinchazón",
        "edema" => "edema",
        "bleeding" => "sangrado",
        "jaundice" => "ictericia",
        "sweating" => "sudoración",
        "dryness" => "sequedad",
        "thirst" => "sed",
        "sores" => "llagas",
        "rashes" => "erupciones",
        "rash" => "erupción",
        "skin" => "piel",
        "eyes" => "ojos",
        "eye" => "ojo",
        "throat" => "garganta",
        "nose" => "nariz",
        "joints" => "articulaciones",
        "joint" => "articulación",
        "sinews" => "tendones",
        "bones" => "huesos",
        "stool" => "heces",
        "fluids" => "líquidos",
        "fluid" => "líquido",
        "pregnancy" => "embarazo",
        "menstruation" => "menstruación",
        "postpartum" => "posparto",
        "deficiency" => "deficiencia",
        "excess" => "exceso",
        "toxic" => "tóxica",
        "toxicity" => "toxicidad",
        "parasites" => "parásitos",
        "convulsions" => "convulsiones",
        "tremors" => "temblores",
        "abscesses" => "abscesos",
        "abscess" => "absceso",
        "nodules" => "nódulos",
        "stasis" => "estasis",
        "channels" => "canales",
        "channel" => "canal",
        "exterior" => "exterior",
        "interior" => "interior",
        "pattern" => "patrón",
        "patterns" => "patrones",
        "syndrome" => "síndrome",
        "disorders" => "trastornos",
        "disorder" => "trastorno",
        "conditions" => "afecciones",
        "condition" => "afección",
        "symptoms" => "síntomas",
        "signs" => "signos",
        "location" => "localización",
        "clearing" => "eliminación",
        "draining" => "drenaje",
        "releasing" => "liberación",
        "relieving" => "alivio",
        "promoting" => "promoción",
        "transforming" => "transformación",
        "nourishing" => "nutrición",
        "strengthening" => "fortalecimiento",
        "warming" => "calentamiento",
        "cooling" => "enfriamiento",
        "moistening" => "humectación",
        "softening" => "ablandamiento",
        "moving" => "movilización",
        "invigorating" => "vigorización",
        "harmonizing" => "armonización",
        "painful" => "doloroso",
        "warm-febrile disease" => "enfermedad febril cálida",
        "medicinal material" => "material medicinal",
        "clinical relationship" => "relación clínica",
        "secondary actions" => "acciones secundarias",
        "constitutional deficiency" => "deficiencia constitucional",
        "properly prepared" => "preparado correctamente",
        "Clears" => "Elimina",
        "clears" => "elimina",
        "Clear" => "Eliminar",
        "clear" => "eliminar",
        "Dispels" => "Dispersa",
        "dispels" => "dispersa",
        "Dispel" => "Dispersar",
        "dispel" => "dispersar",
        "Drains" => "Drena",
        "drains" => "drena",
        "Drain" => "Drenar",
        "drain" => "drenar",
        "Releases" => "Libera",
        "releases" => "libera",
        "Release" => "Liberar",
        "release" => "liberar",
        "Relieves" => "Alivia",
        "relieves" => "alivia",
        "Relieve" => "Aliviar",
        "relieve" => "aliviar",
        "Promotes" => "Promueve",
        "promotes" => "promueve",
        "Transforms" => "Transforma",
        "transforms" => "transforma",
        "Nourishes" => "Nutre",
        "nourishes" => "nutre",
        "Strengthens" => "Fortalece",
        "strengthens" => "fortalece",
        "Strengthen" => "Fortalecer",
        "strengthen" => "fortalecer",
        "Tonifies" => "Tonifica",
        "tonifies" => "tonifica",
        "Benefits" => "Beneficia",
        "benefits" => "beneficia",
        "Warms" => "Calienta",
        "warms" => "calienta",
        "Cools" => "Enfría",
        "cools" => "enfría",
        "Moistens" => "Humedece",
        "moistens" => "humedece",
        "Softens" => "Ablanda",
        "softens" => "ablanda",
        "Calms" => "Calma",
        "calms" => "calma",
        "Stops" => "Detiene",
        "stops" => "detiene",
        "Reduces" => "Reduce",
        "reduces" => "reduce",
        "Expels" => "Expulsa",
        "expels" => "expulsa",
        "Unblocks" => "Desobstruye",
        "unblocks" => "desobstruye",
        "Unblock" => "Desobstruir",
        "unblock" => "desobstruir",
        "Moves" => "Mueve",
        "moves" => "mueve",
        "Invigorates" => "Vigoriza",
        "invigorates" => "vigoriza",
        "Treats" => "Trata",
        "treats" => "trata",
        "Addresses" => "Trata",
        "addresses" => "trata",
        "Supports" => "Apoya",
        "supports" => "apoya",
        "Harmonizes" => "Armoniza",
        "harmonizes" => "armoniza",
        "Settles" => "Calma",
        "settles" => "calma",
        "Avoid" => "Evitar",
        "avoid" => "evitar",
        "Use cautiously" => "Usar con precaución",
        "use cautiously" => "usar con precaución",
        "Contraindicated" => "Contraindicado",
        "contraindicated" => "contraindicado",
        "incompatible with" => "incompatible con",
        "traditionally" => "tradicionalmente",
        "Traditionally" => "Tradicionalmente",
        "especially" => "especialmente",
        "Especially" => "Especialmente",
        " while " => " mientras ",
        " without " => " sin ",
        " with " => " con ",
        " from " => " por ",
        " during " => " durante ",
        " when " => " cuando ",
        " because " => " porque ",
        " and " => " y ",
        " or " => " o ",
        " of the " => " del ",
        " of " => " de ",
        " for " => " para ",
        " in " => " en ",
        "topically" => "tópicamente",
        "externally" => "externamente",
        "decoction" => "decocción",
        "powder" => "polvo",
        "pill" => "píldora",
        "capsule" => "cápsula",
        "up to" => "hasta",
        "Cook first; remove foam." => "Cocinar primero y retirar la espuma.",
        "Add late when appropriate." => "Agregar al final cuando sea apropiado.",
        "Char for bleeding indications." => "Carbonizar para indicaciones de sangrado.",
        "Wrap in cloth while decocting." => "Envolver en tela durante la decocción.",
        "Fresh ginger." => "Jengibre fresco.",
        "Use the white bulb." => "Usar el bulbo blanco.",
        "Do not subject to prolonged cooking." => "No someter a cocción prolongada.",
        "Dissolve into the strained decoction." => "Disolver en la decocción ya colada.",
        "Crush before cooking." => "Triturar antes de cocinar.",
        "Wrap in a porous bag before decocting." => "Envolver en una bolsa porosa antes de la decocción.",
        "No specific caution noted in the guideline." => "La guía no señala una precaución específica.",
        "Use only correctly identified and properly prepared medicinal material." => "Usar únicamente material medicinal correctamente identificado y preparado.",
        "Use only the correctly identified, non-aristolochic medicinal species." => "Usar únicamente la especie medicinal correctamente identificada y sin ácido aristolóquico.",
        "Board Guideline 2026 · 15 herbs · Common foundation: predominantly acrid herbs that release exterior Wind-Cold." => "Guía Board 2026 · 15 hierbas · Fundamento común: hierbas predominantemente picantes que liberan el Viento-Frío exterior.",
        "Board Guideline 2026 · 11 herbs · Common foundation: predominantly cool and acrid herbs that disperse exterior Wind-Heat." => "Guía Board 2026 · 11 hierbas · Fundamento común: hierbas predominantemente frescas y picantes que dispersan el Viento-Calor exterior.",
        "Board Guideline 2026 · 8 herbs · Common foundation: cold herbs that clear internal Heat, drain Fire or protect fluids." => "Guía Board 2026 · 8 hierbas · Fundamento común: hierbas frías que eliminan Calor interno, drenan Fuego o protegen los líquidos.",
        "Board Guideline 2026 · 5 herbs · Common foundation: cool Blood-level Heat and protect against injury from Fire." => "Guía Board 2026 · 5 hierbas · Fundamento común: enfriar el Calor en el nivel de la Sangre y proteger del daño por Fuego.",
        "Board Guideline 2026 · 5 herbs · Common foundation: bitter, cold herbs that clear Heat and dry Dampness." => "Guía Board 2026 · 5 hierbas · Fundamento común: hierbas amargas y frías que eliminan Calor y secan Humedad.",
        "Board Guideline 2026 · 9 herbs · Common foundation: clear Heat and relieve Fire toxicity, with distinct clinical locations and secondary actions." => "Guía Board 2026 · 9 hierbas · Fundamento común: eliminar Calor y aliviar toxicidad de Fuego, con diferentes localizaciones clínicas y acciones secundarias.",
        "Board Guideline 2026 · 3 herbs · Common foundation: clear deficient Heat and cool the Blood." => "Guía Board 2026 · 3 hierbas · Fundamento común: eliminar Calor por Deficiencia y enfriar la Sangre.",
        "Board Guideline 2026 · 3 herbs · Common foundation: drain Heat and move stool to purge accumulation." => "Guía Board 2026 · 3 hierbas · Fundamento común: drenar Calor y movilizar las heces para purgar acumulación.",
        "Board Guideline 2026 · 1 herb · Common foundation: gently moisten the Intestines and relieve dry constipation." => "Guía Board 2026 · 1 hierba · Fundamento común: humedecer suavemente los Intestinos y aliviar el estreñimiento seco.",
        "Board Guideline 2026 · 1 herb · Common foundation: strongly drain retained fluids downward." => "Guía Board 2026 · 1 hierba · Fundamento común: drenar intensamente hacia abajo los líquidos retenidos.",
        "Board Guideline 2026 · 17 herbs · Common foundation: promote urination, eliminate Water and Dampness, reduce edema or relieve painful urinary dribbling." => "Guía Board 2026 · 17 hierbas · Fundamento común: promover la micción, eliminar Agua y Humedad, reducir edema o aliviar la micción dolorosa y entrecortada.",
        "Board Guideline 2026 · 10 herbs · Common foundation: dispel Wind-Damp, relieve pain from Bi syndrome, unblock channels and benefit sinews." => "Guía Board 2026 · 10 hierbas · Fundamento común: dispersar Viento-Humedad, aliviar dolor por síndrome Bi, desobstruir canales y beneficiar los tendones.",
        "sweating strength, pain location, nasal involvement, Lung symptoms and Dampness." => "intensidad de la sudoración, localización del dolor, afectación nasal, síntomas de Pulmón y Humedad.",
        "throat and rash involvement, eye symptoms, headache location, Lung dryness, Shaoyang signs and sinking Qi." => "afectación de garganta y erupciones, síntomas oculares, localización de la cefalea, sequedad de Pulmón, signos Shaoyang y hundimiento del Qi.",
        "severity of Fire, fluid injury, Lung or Stomach involvement, irritability, urinary signs, eye symptoms and nodules." => "gravedad del Fuego, daño de líquidos, afectación de Pulmón o Estómago, irritabilidad, signos urinarios, síntomas oculares y nódulos.",
        "toxin severity, bleeding, Yin injury, deficiency Heat, Blood stasis, throat swelling and skin manifestations." => "gravedad de la toxina, sangrado, daño del Yin, Calor por Deficiencia, estasis de Sangre, hinchazón de garganta y manifestaciones cutáneas.",
        "upper, middle or lower burner location; Liver/Gallbladder Fire; diarrhea; jaundice; urinary or genital signs; and itching." => "localización en calentador superior, medio o inferior; Fuego de Hígado/Vesícula Biliar; diarrea; ictericia; signos urinarios o genitales; y picazón.",
        "Wind-Heat stage, throat severity, abscess location, Damp-Heat involvement, skin lesions, Summer-Heat and bleeding." => "etapa de Viento-Calor, gravedad de garganta, localización del absceso, afectación por Humedad-Calor, lesiones cutáneas, Calor de Verano y sangrado.",
        "sweating with Yin-deficiency Heat, Lung cough, Summer-Heat or malarial patterns, urinary involvement, toxicity and swelling." => "sudoración con Calor por Deficiencia de Yin, tos de Pulmón, Calor de Verano o patrones maláricos, afectación urinaria, toxicidad e hinchazón.",
        "strength of purgation, dry-hard stool, Blood Heat or stasis, Liver Heat, external swelling and constitutional deficiency." => "intensidad de la purgación, heces secas y duras, Calor o estasis de Sangre, Calor de Hígado, hinchazón externa y deficiencia constitucional.",
        "dryness or deficiency constipation, need for gentle action, Yin and fluid status, and long-term-use cautions." => "estreñimiento por sequedad o deficiencia, necesidad de una acción suave, estado del Yin y de los líquidos, y precauciones de uso prolongado.",
        "severe fluid accumulation, edema, swelling or nodules, patient strength, toxicity and formula incompatibility." => "acumulación grave de líquidos, edema, hinchazón o nódulos, fortaleza del paciente, toxicidad e incompatibilidad de fórmula.",
        "edema versus Lin syndrome, Spleen deficiency, jaundice, Heart Fire, bleeding, Blood stasis, lactation, Lung phlegm, itching and cloudy urine." => "edema frente a síndrome Lin, deficiencia de Bazo, ictericia, Fuego de Corazón, sangrado, estasis de Sangre, lactancia, flema de Pulmón, picazón y orina turbia.",
        "upper versus lower body, Cold versus Heat, channel obstruction, cramping, edema, skin involvement, convulsions and Liver-Kidney deficiency." => "parte superior frente a parte inferior del cuerpo, Frío frente a Calor, obstrucción de canales, calambres, edema, afectación cutánea, convulsiones y deficiencia de Hígado-Riñón.",
        "Bi syndrome" => "síndrome Bi",
        "Yin deficiency" => "deficiencia de Yin",
        "Blood deficiency" => "deficiencia de Sangre",
        "Qi deficiency" => "deficiencia de Qi",
        "Yang deficiency" => "deficiencia de Yang",
        "Kidney deficiency" => "deficiencia de Riñón",
        "Spleen deficiency" => "deficiencia de Bazo",
        "Stomach deficiency" => "deficiencia de Estómago",
        "Liver-Kidney deficiency" => "deficiencia de Hígado-Riñón",
        "Spleen and Stomach deficient Cold" => "Frío por deficiencia de Bazo y Estómago",
        "Heat signs" => "signos de Calor",
        "from the lower back and legs" => "de la zona lumbar y las piernas",
        "the exterior" => "el exterior",
        "the Blood" => "la Sangre",
        "the Lung" => "el Pulmón",
        "the Heart" => "el Corazón",
        "the Liver" => "el Hígado",
        "the Kidney" => "el Riñón",
        "the Spleen" => "el Bazo",
        "the Stomach" => "el Estómago",
        "the Intestines" => "los Intestinos",
        "the channels" => "los canales",
        "the joints" => "las articulaciones",
        "the sinews" => "los tendones",
        "the skin" => "la piel",
        "the eyes" => "los ojos",
        "the throat" => "la garganta",
        "the nose" => "la nariz",
        "the womb" => "el útero",
        "All address" => "Todas tratan",
        "All clear" => "Todas eliminan",
        "All are" => "Todas son",
        "All cool" => "Todas enfrían",
        "All drain" => "Todas drenan",
        "All promote" => "Todas promueven",
        "Both clear" => "Ambas eliminan",
        "Both treat" => "Ambas tratan",
        "Both protect" => "Ambas protegen",
        "Both drain" => "Ambas drenan",
        "painful obstruction" => "obstrucción dolorosa",
        "with selection based on" => "seleccionándolas según",
        "based on" => "según",
        "channel involvement" => "afectación de canales",
        "clinical emphasis" => "énfasis clínico",
        "secondary emphasis" => "énfasis secundario",
        "distinctive choice" => "opción distintiva",
        "most specific for" => "más específica para",
        "herbs" => "hierbas",
        "herb" => "hierba",
        "benefit" => "beneficiar",
        "Benefit" => "Beneficiar",
        "assists" => "ayuda",
        "assisting" => "ayudando",
        "protects" => "protege",
        "protect" => "proteger",
        "guides" => "guía",
        "guiding" => "guiando",
        "opens" => "abre",
        "open" => "abrir",
        "directs" => "dirige",
        "direct" => "dirigir",
        "breaks" => "rompe",
        "break" => "romper",
        "leaches" => "drena",
        "leach" => "drenar",
        "quiets" => "calma",
        "quiet" => "calmar",
        "disseminates" => "difunde",
        "disseminate" => "difundir",
        "kills" => "elimina",
        "kill" => "eliminar",
        "raises" => "eleva",
        "raise" => "elevar",
        "separates" => "separa",
        "separate" => "separar",
        "supports" => "apoya",
        "support" => "apoyar",
        "vents" => "exterioriza",
        "vent" => "exteriorizar",
        "dissipates" => "disipa",
        "dissipate" => "disipar",
        "checks" => "controla",
        "check" => "controlar",
        "adds" => "añade",
        "additionally" => "además",
        "especially for" => "especialmente para",
        "especially in" => "especialmente en",
        "primarily" => "principalmente",
        "strongly" => "intensamente",
        "gently" => "suavemente",
        "severe" => "grave",
        "gentle" => "suave",
        "strong" => "intenso",
        "mild" => "leve",
        "upper" => "superior",
        "lower" => "inferior",
        "middle" => "medio",
        "All" => "Todas",
        "Both" => "Ambas",
        "selection" => "selección",
        "temperature" => "temperatura",
        "involvement" => "afectación",
        "obstruction" => "obstrucción",
        "accumulation" => "acumulación",
        "retention" => "retención",
        "discharge" => "secreción",
        "severity" => "gravedad",
        "strength" => "intensidad",
        "action" => "acción",
        "actions" => "acciones",
        "emphasis" => "énfasis",
        "ability" => "capacidad",
        "choice" => "elección",
        "need" => "necesidad",
        "patient" => "paciente",
        "patients" => "pacientes",
        "long-term use" => "uso prolongado",
        "excessive use" => "uso excesivo",
        "prolonged use" => "uso prolongado",
        "may cause" => "puede causar",
        "may damage" => "puede dañar",
        "may reduce" => "puede reducir",
        "may be used" => "puede utilizarse",
        "should not be used" => "no debe utilizarse",
        "should not be taken" => "no debe tomarse",
        "No specific" => "Ninguna específica",
        " from the lower back and legs" => " de la zona lumbar y las piernas",
        "Wind-Damp painful obstruction" => "obstrucción dolorosa por Viento-Humedad",
        "Shaoyin headache and toothache" => "cefalea Shaoyin y dolor dental",
        "Dispels Wind-Cold-Damp, especially from the lower back and legs; relieves pain from Bi syndrome; releases the exterior; treats Shaoyin headache and toothache." => "Dispersa Viento-Frío-Humedad, especialmente de la zona lumbar y las piernas; alivia el dolor del síndrome Bi; libera el exterior; trata cefalea Shaoyin y dolor dental.",
        "Avoid in Yin deficiency with Heat signs." => "Evitar en deficiencia de Yin con signos de Calor.",
        "Dispels Wind-Damp; unblocks the channels and relieves pain; softens and transforms fish bones; addresses focal distension and accumulation." => "Dispersa Viento-Humedad; desobstruye los canales y alivia el dolor; ablanda y transforma espinas de pescado; trata distensión y acumulación focal.",
        "Avoid in Qi deficiency or Blood deficiency." => "Evitar en deficiencia de Qi o de Sangre.",
        "Dispels Wind-Damp and unblocks the channels; promotes urination and reduces edema; treats itching skin lesions; may be used as a gargle for toothache from dental cavities." => "Dispersa Viento-Humedad y desobstruye los canales; promueve la micción y reduce el edema; trata lesiones cutáneas con picazón; puede utilizarse como gárgara para dolor dental por caries.",
        "Avoid in Blood deficiency." => "Evitar en deficiencia de Sangre.",
        "Dispels Wind-Damp and relaxes the sinews; clears deficiency Heat; resolves Dampness and jaundice; moistens the Intestines and moves stool." => "Dispersa Viento-Humedad y relaja los tendones; elimina Calor por Deficiencia; resuelve Humedad e ictericia; humedece los Intestinos y moviliza las heces.",
        "Avoid with frequent urination, chronic pain with emaciation, or Spleen deficiency with diarrhea." => "Evitar con micción frecuente, dolor crónico con emaciación o deficiencia de Bazo con diarrea.",
        "Relaxes the sinews and relieves cramping; harmonizes the Stomach and transforms Dampness; reduces food retention." => "Relaja los tendones y alivia calambres; armoniza el Estómago y transforma Humedad; reduce la retención de alimentos.",
        "Avoid during exterior disorders; excessive use may damage the teeth and bones." => "Evitar durante trastornos exteriores; el uso excesivo puede dañar dientes y huesos.",
        "Dispels Wind-Damp, unblocks the channels and benefits the joints, especially the upper limbs; may reduce edema." => "Dispersa Viento-Humedad, desobstruye los canales y beneficia las articulaciones, especialmente las extremidades superiores; puede reducir el edema.",
        "Dispels Wind-Damp and strengthens the sinews; clears Heat and calms the Liver; transforms Damp-Heat; traditionally used in hypertension patterns." => "Dispersa Viento-Humedad y fortalece los tendones; elimina Calor y calma el Hígado; transforma Humedad-Calor; se usa tradicionalmente en patrones de hipertensión.",
        "Avoid in Yin deficiency or Blood deficiency." => "Evitar en deficiencia de Yin o de Sangre.",
        "Powerfully dispels Wind from the skin and sinews; opens the channels; settles tremors and convulsions." => "Dispersa intensamente el Viento de la piel y los tendones; abre los canales; calma temblores y convulsiones.",
        "Avoid in Yin deficiency with Heat; use cautiously in Blood deficiency." => "Evitar en deficiencia de Yin con Calor; usar con precaución en deficiencia de Sangre.",
        "Dispels Wind-Damp; strengthens the Liver and Kidneys, sinews and bones; transforms Dampness and reduces swelling." => "Dispersa Viento-Humedad; fortalece Hígado y Riñones, tendones y huesos; transforma Humedad y reduce hinchazón.",
        "Use cautiously in Yin deficiency with Heat." => "Usar con precaución en deficiencia de Yin con Calor.",
        "Dispels Wind-Damp; strengthens the Liver and Kidneys, sinews and bones; nourishes Blood; calms the womb; benefits the skin; traditionally used in hypertension patterns." => "Dispersa Viento-Humedad; fortalece Hígado y Riñones, tendones y huesos; nutre la Sangre; calma el útero; beneficia la piel; se usa tradicionalmente en patrones de hipertensión.",
        "No specific caution noted in the guideline." => "La guía no señala una precaución específica.",
        "Use only correctly identified and properly prepared medicinal material." => "Usar únicamente material medicinal correctamente identificado y preparado.",
        "Wind-Damp Bi syndrome" => "Síndrome Bi por Viento-Humedad",
        "All address Wind-Damp painful obstruction, with selection based on location, temperature, deficiency, edema and channel involvement." => "Todas tratan la obstrucción dolorosa por Viento-Humedad; se eligen según localización, temperatura, deficiencia, edema y afectación de canales.",
        "Lower body versus upper limbs" => "Parte inferior del cuerpo frente a extremidades superiores",
        "Du Huo emphasizes the lower back and legs, while Sang Zhi is especially useful for the shoulders and upper limbs." => "Du Huo enfatiza la zona lumbar y las piernas, mientras Sang Zhi es especialmente útil para hombros y extremidades superiores.",
        "Unblock channels and relax sinews" => "Desobstruir canales y relajar tendones",
        "Wei Ling Xian strongly unblocks pain, Hai Tong Pi adds edema and skin actions, Qin Jiao clears Heat, Mu Gua treats cramping, and Sang Zhi benefits joints." => "Wei Ling Xian desobstruye intensamente el dolor; Hai Tong Pi añade acciones sobre edema y piel; Qin Jiao elimina Calor; Mu Gua trata calambres; y Sang Zhi beneficia las articulaciones.",
        "Wind-Damp with Heat" => "Viento-Humedad con Calor",
        "Both are cooling choices for Heat-type Bi syndrome; Qin Jiao also clears deficiency Heat, while Xi Xian Cao calms the Liver and strengthens sinews." => "Ambas son opciones refrescantes para síndrome Bi tipo Calor; Qin Jiao también elimina Calor por Deficiencia, mientras Xi Xian Cao calma el Hígado y fortalece tendones.",
        "Edema and Damp accumulation" => "Edema y acumulación de Humedad",
        "All address Wind-Damp with swelling; Hai Tong Pi promotes urination, Sang Zhi benefits the joints, and Wu Jia Pi adds tonic support." => "Todas tratan Viento-Humedad con hinchazón; Hai Tong Pi promueve la micción, Sang Zhi beneficia las articulaciones y Wu Jia Pi añade apoyo tónico.",
        "Strengthen Liver, Kidney, sinews and bones" => "Fortalecer Hígado, Riñón, tendones y huesos",
        "Both treat chronic Wind-Damp with deficiency; Sang Ji Sheng additionally nourishes Blood and calms the womb." => "Ambas tratan Viento-Humedad crónico con deficiencia; Sang Ji Sheng además nutre la Sangre y calma el útero.",
        "Severe Wind, skin and convulsions" => "Viento intenso, piel y convulsiones",
        "Bai Hua She is strongest for entrenched Wind and convulsions, while Xi Xian Cao and Hai Tong Pi emphasize Heat, itching or skin involvement." => "Bai Hua She es la más intensa para Viento arraigado y convulsiones, mientras Xi Xian Cao y Hai Tong Pi enfatizan Calor, picazón o afectación cutánea.",
        "Special indications" => "Indicaciones especiales",
        "Du Huo treats Shaoyin headache and toothache, Wei Ling Xian fish-bone obstruction, Hai Tong Pi dental-cavity pain, and Sang Ji Sheng deficiency with pregnancy support." => "Du Huo trata cefalea Shaoyin y dolor dental; Wei Ling Xian, obstrucción por espinas de pescado; Hai Tong Pi, dolor por caries; y Sang Ji Sheng, deficiencia con apoyo durante el embarazo.",
        "Cool" => "Fresca",
        "Cool, aromatic" => "Fresca, aromática",
        "Cold or warm" => "Fría o tibia",
        "Strongly releases an excess exterior pattern; disseminates Lung Qi; relieves wheezing; promotes urination." => "Libera intensamente el patrón exterior de exceso; difunde el Qi de Pulmón; alivia las sibilancias; promueve la micción.",
        "Avoid in deficiency sweating; use caution with hypertension." => "Evitar en sudoración por deficiencia; usar con precaución en hipertensión.",
        "Releases the muscle layer; warms channels; harmonizes Ying and Wei; assists Yang Qi." => "Libera la capa muscular; calienta los canales; armoniza Ying y Wei; apoya el Qi Yang.",
        "Use caution with febrile disease, Yin-deficiency heat, pregnancy, or excessive bleeding." => "Usar con precaución en enfermedad febril, Calor por deficiencia de Yin, embarazo o sangrado excesivo.",
        "Releases mild Wind-Cold; moves Qi; expands the chest; calms the fetus; helps seafood poisoning." => "Libera Viento-Frío leve; mueve el Qi; expande el tórax; calma el feto; ayuda en intoxicación por mariscos.",
        "Use cautiously with Qi deficiency or spontaneous sweating." => "Usar con precaución en deficiencia de Qi o sudoración espontánea.",
        "Releases the exterior; vents rashes; relieves itching; charred form stops bleeding." => "Libera el exterior; exterioriza erupciones; alivia la picazón; la forma carbonizada detiene el sangrado.",
        "Use cautiously with exterior deficiency and spontaneous sweating." => "Usar con precaución en deficiencia exterior y sudoración espontánea.",
        "Expels Wind; relieves pain, headache and spasms; addresses Wind-Damp and diarrhea." => "Expulsa Viento; alivia dolor, cefalea y espasmos; trata Viento-Humedad y diarrea.",
        "Use cautiously with Blood or Yin deficiency." => "Usar con precaución en deficiencia de Sangre o Yin.",
        "Expels Wind-Cold-Damp; relieves occipital and upper-body pain." => "Expulsa Viento-Frío-Humedad; alivia el dolor occipital y de la parte superior del cuerpo.",
        "Use cautiously with Blood or Yin deficiency; may cause nausea in excess." => "Usar con precaución en deficiencia de Sangre o Yin; en exceso puede causar náuseas.",
        "Expels Wind-Cold and relieves vertex headache." => "Expulsa Viento-Frío y alivia la cefalea del vértex.",
        "Avoid in Blood or Yin deficiency and heat patterns." => "Evitar en deficiencia de Sangre o Yin y en patrones de Calor.",
        "Relieves frontal headache; opens the nose; reduces swelling; expels pus and damp discharge." => "Alivia la cefalea frontal; abre la nariz; reduce la hinchazón; expulsa pus y secreción por Humedad.",
        "Use cautiously with Blood or Yin deficiency and dryness." => "Usar con precaución en deficiencia de Sangre o Yin y en sequedad.",
        "Disperses Wind-Cold; warms Lung; transforms thin mucus; relieves pain; opens the nose." => "Dispersa Viento-Frío; calienta el Pulmón; transforma mucosidad clara; alivia el dolor; abre la nariz.",
        "Dose carefully; avoid in deficiency sweating and use caution with renal risk." => "Dosificar cuidadosamente; evitar en sudoración por deficiencia y usar con precaución ante riesgo renal.",
        "Do not exceed the prescribed dose." => "No exceder la dosis prescrita.",
        "Opens the nasal passages and relieves congestion, discharge, headache and loss of smell." => "Abre las vías nasales y alivia congestión, secreción, cefalea y pérdida del olfato.",
        "Use cautiously with Yin-deficiency heat." => "Usar con precaución en Calor por deficiencia de Yin.",
        "Dispels Wind; opens the nose; relieves sinus obstruction, discharge and headache." => "Dispersa Viento; abre la nariz; alivia obstrucción sinusal, secreción y cefalea.",
        "Toxic in excess; avoid overdose and use cautiously in Blood deficiency." => "Es tóxica en exceso; evitar sobredosis y usar con precaución en deficiencia de Sangre.",
        "Use only properly prepared material." => "Usar únicamente material preparado correctamente.",
        "Releases mild exterior Cold; warms Lung; stops cough; harmonizes the Middle; relieves vomiting." => "Libera Frío exterior leve; calienta el Pulmón; detiene la tos; armoniza el Jiao Medio; alivia los vómitos.",
        "Use cautiously with Lung Heat or Yin-deficiency heat." => "Usar con precaución en Calor de Pulmón o Calor por deficiencia de Yin.",
        "Releases Summer Wind-Cold; transforms Dampness; promotes urination; reduces swelling." => "Libera Viento-Frío de Verano; transforma Humedad; promueve la micción; reduce la hinchazón.",
        "Avoid in Qi deficiency with spontaneous sweating." => "Evitar en deficiencia de Qi con sudoración espontánea.",
        "Cook briefly for exterior disorders." => "Cocinar brevemente para trastornos exteriores.",
        "Releases mild Wind-Cold; opens the Lung; unblocks Yang Qi and resolves clumps." => "Libera Viento-Frío leve; abre el Pulmón; desobstruye el Qi Yang y resuelve acumulaciones.",
        "Use cautiously with Yin deficiency or excessive sweating." => "Usar con precaución en deficiencia de Yin o sudoración excesiva.",
        "Gently releases the exterior; relieves constraint and irritability; harmonizes the Middle." => "Libera suavemente el exterior; alivia la opresión y la irritabilidad; armoniza el Jiao Medio.",
        "Use according to preparation and pattern." => "Usar de acuerdo con la preparación y el patrón.",
        "Prepared soybean; processing influences its tendency." => "Soja preparada; el procesamiento modifica su tendencia.",
        "Warm the channels and promote sweating; Ma Huang is stronger and Gui Zhi harmonizes Ying and Wei." => "Calientan los canales y promueven la sudoración; Ma Huang es más potente y Gui Zhi armoniza Ying y Wei.",
        "Release mild exterior Wind-Cold when strong diaphoresis is unnecessary." => "Liberan Viento-Frío exterior leve cuando no se necesita una diaforesis intensa.",
        "Qiang Huo emphasizes occiput and upper body; Gao Ben the vertex; Bai Zhi the frontal region." => "Qiang Huo enfatiza el occipucio y la parte superior del cuerpo; Gao Ben el vértex; Bai Zhi la región frontal.",
        "Open the nasal passages and relieve obstruction, discharge or headache." => "Abren las vías nasales y alivian obstrucción, secreción o cefalea.",
        "Warm or diffuse Lung Qi, transform phlegm and relieve cough or wheezing." => "Calientan o difunden el Qi de Pulmón, transforman flema y alivian tos o sibilancias.",
        "Address exterior Wind-Cold with Dampness; Xiang Ru especially emphasizes Summer-Damp." => "Tratan Viento-Frío exterior con Humedad; Xiang Ru enfatiza especialmente Humedad de Verano.",
        "Aromatic / Damp or Summer-Damp overlap" => "Aromáticas / coincidencia con Humedad o Humedad de Verano",
        "Clears the head and eyes; benefits the throat; soothes constrained Liver Qi; vents rashes." => "Aclara la cabeza y los ojos; beneficia la garganta; suaviza el Qi de Hígado estancado; exterioriza erupciones.",
        "Use cautiously with Qi-deficiency exterior patterns or Yin-deficiency heat." => "Usar con precaución en patrones exteriores por deficiencia de Qi o Calor por deficiencia de Yin.",
        "Add during the final 5 minutes." => "Agregar durante los últimos 5 minutos.",
        "Disperses Wind-Heat; benefits the throat; clears Heat and toxin; vents rashes; moistens the intestines." => "Dispersa Viento-Calor; beneficia la garganta; elimina Calor y toxinas; exterioriza erupciones; humedece los Intestinos.",
        "Use cautiously with Qi-deficiency diarrhea or open sores without toxin." => "Usar con precaución en diarrea por deficiencia de Qi o llagas abiertas sin toxina.",
        "Crush before decocting." => "Triturar antes de la decocción.",
        "Disperses Wind-Heat; vents rashes; relieves itching; benefits the throat; clears the eyes; extinguishes Liver Wind and spasms." => "Dispersa Viento-Calor; exterioriza erupciones; alivia la picazón; beneficia la garganta; aclara los ojos; extingue Viento de Hígado y espasmos.",
        "Use cautiously in deficiency exterior patterns and during pregnancy." => "Usar con precaución en patrones exteriores por deficiencia y durante el embarazo.",
        "Disperses Wind-Heat; clears Lung Heat and moistens dryness; cools Blood; stops bleeding; clears the eyes." => "Dispersa Viento-Calor; elimina Calor de Pulmón y humedece la sequedad; enfría la Sangre; detiene el sangrado; aclara los ojos.",
        "No major caution noted at standard dosage." => "No se señala una precaución importante con la dosis estándar.",
        "Disperses Wind-Heat; clears the eyes; calms the Liver; extinguishes Wind; clears Heat and toxin." => "Dispersa Viento-Calor; aclara los ojos; calma el Hígado; extingue Viento; elimina Calor y toxinas.",
        "Use cautiously with Qi deficiency, poor appetite or diarrhea." => "Usar con precaución en deficiencia de Qi, poco apetito o diarrea.",
        "Disperses Wind-Heat; clears the head and eyes; relieves headache; expels Wind-Damp." => "Dispersa Viento-Calor; aclara la cabeza y los ojos; alivia la cefalea; expulsa Viento-Humedad.",
        "Use cautiously when headache or eye problems arise from Blood deficiency or weak Stomach Qi." => "Usar con precaución cuando la cefalea o los problemas oculares provienen de deficiencia de Sangre o debilidad del Qi de Estómago.",
        "Processing influences its thermal tendency." => "El procesamiento modifica su tendencia térmica.",
        "Disperses Wind-Heat; brightens the eyes; removes visual obstruction; clears Heat; stops bleeding." => "Dispersa Viento-Calor; mejora la visión; elimina obstrucción visual; elimina Calor; detiene el sangrado.",
        "Use cautiously with frequent urination, depleted fluids, Qi deficiency or pregnancy." => "Usar con precaución en micción frecuente, agotamiento de líquidos, deficiencia de Qi o embarazo.",
        "Resolves Shaoyang disorders; reduces fever; spreads Liver Qi; raises clear Yang Qi." => "Resuelve trastornos Shaoyang; reduce la fiebre; distribuye el Qi de Hígado; eleva el Qi Yang claro.",
        "Use cautiously with Yin-deficiency cough, Liver Fire ascending, headache, nausea or vomiting." => "Usar con precaución en tos por deficiencia de Yin, ascenso de Fuego de Hígado, cefalea, náuseas o vómitos.",
        "Disperses Wind-Heat; clears Heat and toxin; vents rashes; raises Yang Qi." => "Dispersa Viento-Calor; elimina Calor y toxinas; exterioriza erupciones; eleva el Qi Yang.",
        "Use cautiously with Yin-deficiency heat, upper excess, or fully erupted measles." => "Usar con precaución en Calor por deficiencia de Yin, exceso superior o sarampión completamente exteriorizado.",
        "Releases the muscle layer; clears Heat; generates fluids; relieves thirst; treats diarrhea; vents rashes." => "Libera la capa muscular; elimina Calor; genera líquidos; alivia la sed; trata diarrea; exterioriza erupciones.",
        "Benefit the throat and vent rashes" => "Beneficiar la garganta y exteriorizar erupciones",
        "Disperse Wind-Heat while addressing sore throat or incomplete rash expression." => "Dispersan Viento-Calor mientras tratan dolor de garganta o exteriorización incompleta de erupciones.",
        "Clear the head and eyes" => "Aclarar la cabeza y los ojos",
        "Treat Wind-Heat affecting the head or eyes; selection depends on headache, dryness, redness or visual obstruction." => "Tratan Viento-Calor que afecta cabeza u ojos; la selección depende de cefalea, sequedad, enrojecimiento u obstrucción visual.",
        "Liver and Wind involvement" => "Afectación de Hígado y Viento",
        "Share Liver-channel activity ranging from moving Qi to clearing eyes or extinguishing Wind." => "Comparten actividad en el canal de Hígado, desde mover el Qi hasta aclarar los ojos o extinguir Viento.",
        "Lung Heat and dryness" => "Calor y sequedad de Pulmón",
        "Clear upper-burner Heat; Sang Ye emphasizes Lung dryness, while Niu Bang Zi emphasizes throat and toxin." => "Eliminan Calor del Jiao Superior; Sang Ye enfatiza sequedad de Pulmón, mientras Niu Bang Zi enfatiza garganta y toxinas.",
        "Raise clear Yang Qi" => "Elevar el Qi Yang claro",
        "Raise clear Yang; Sheng Ma emphasizes sinking Qi, Ge Gen diarrhea, and Chai Hu Shaoyang with Liver Qi constraint." => "Elevan el Yang claro; Sheng Ma enfatiza hundimiento del Qi, Ge Gen diarrea y Chai Hu Shaoyang con estancamiento del Qi de Hígado.",
        "Gentle exterior release and irritability" => "Liberación exterior suave e irritabilidad",
        "Useful when a lighter exterior-releasing strategy is preferred; Dan Dou Chi especially relieves irritability." => "Útiles cuando se prefiere una estrategia exterior más suave; Dan Dou Chi alivia especialmente la irritabilidad.",
        "Clears intense Qi-level Heat; drains Lung and Stomach Fire; relieves thirst; used topically for burns or sores." => "Elimina Calor intenso del nivel Qi; drena Fuego de Pulmón y Estómago; alivia la sed; se usa tópicamente para quemaduras o llagas.",
        "Avoid in Spleen and Stomach deficiency-cold, weak pulse or Yang deficiency." => "Evitar en Frío por deficiencia de Bazo y Estómago, pulso débil o deficiencia de Yang.",
        "Crush and decoct 20–30 minutes before other herbs." => "Triturar y cocinar 20–30 minutos antes que las otras hierbas.",
        "Clears Heat and drains Fire; enriches Yin; moistens Lung and Kidney dryness; relieves wasting and thirsting." => "Elimina Calor y drena Fuego; enriquece el Yin; humedece la sequedad de Pulmón y Riñón; alivia consunción y sed.",
        "Use cautiously with diarrhea from Spleen deficiency." => "Usar con precaución en diarrea por deficiencia de Bazo.",
        "Clears Lung and Stomach Heat; generates fluids; relieves thirst; moistens Lung dryness; resolves toxicity and expels pus." => "Elimina Calor de Pulmón y Estómago; genera líquidos; alivia la sed; humedece la sequedad de Pulmón; resuelve toxicidad y expulsa pus.",
        "Contraindicated during pregnancy; use cautiously with Spleen and Stomach deficiency-cold or diarrhea." => "Contraindicada durante el embarazo; usar con precaución en Frío por deficiencia de Bazo y Estómago o diarrea.",
        "Clears Heat; generates fluids; relieves thirst; clears Lung Heat; stops vomiting; promotes urination." => "Elimina Calor; genera líquidos; alivia la sed; elimina Calor de Pulmón; detiene los vómitos; promueve la micción.",
        "Use cautiously with Spleen and Stomach deficiency-cold." => "Usar con precaución en Frío por deficiencia de Bazo y Estómago.",
        "Fresh form may be used at a larger dose." => "La forma fresca puede utilizarse en una dosis mayor.",
        "Clears Heat and irritability; promotes urination; clears Heart Heat through the Small Intestine." => "Elimina Calor e irritabilidad; promueve la micción; elimina Calor de Corazón a través del Intestino Delgado.",
        "Use cautiously during pregnancy or with depleted fluids." => "Usar con precaución durante el embarazo o con agotamiento de líquidos.",
        "Drains Fire and eliminates irritability; clears Damp-Heat; cools Blood; stops bleeding; reduces swelling." => "Drena Fuego y elimina irritabilidad; elimina Humedad-Calor; enfría la Sangre; detiene el sangrado; reduce la hinchazón.",
        "Use cautiously with loose stools or poor appetite from deficiency-cold." => "Usar con precaución en heces sueltas o poco apetito por Frío de deficiencia.",
        "Clears Liver Fire and the eyes; dissipates nodules and swelling; supports patterns of Liver Yang rising." => "Elimina Fuego de Hígado y aclara los ojos; disipa nódulos e hinchazón; apoya patrones de ascenso del Yang de Hígado.",
        "Use cautiously with Spleen and Stomach deficiency." => "Usar con precaución en deficiencia de Bazo y Estómago.",
        "Clears Liver Heat and brightens the eyes; moistens the intestines; promotes bowel movement; supports hypertension patterns." => "Elimina Calor de Hígado y mejora la visión; humedece los Intestinos; promueve la evacuación; apoya patrones de hipertensión.",
        "Use cautiously with diarrhea, loose stools or hypotension." => "Usar con precaución en diarrea, heces sueltas o hipotensión.",
        "Clear intense Heat and drain Fire" => "Eliminar Calor intenso y drenar Fuego",
        "Both clear strong internal Heat; Shi Gao acts powerfully at the Qi level, while Zhi Mu also enriches Yin and moistens dryness." => "Ambas eliminan Calor interno intenso; Shi Gao actúa con potencia en el nivel Qi, mientras Zhi Mu también enriquece el Yin y humedece la sequedad.",
        "Generate fluids and relieve thirst" => "Generar líquidos y aliviar la sed",
        "Address Heat damaging fluids; Tian Hua Fen and Lu Gen emphasize fluid generation, while Zhi Mu treats Yin dryness." => "Tratan Calor que daña los líquidos; Tian Hua Fen y Lu Gen enfatizan su generación, mientras Zhi Mu trata sequedad de Yin.",
        "Clear Lung and Stomach Heat" => "Eliminar Calor de Pulmón y Estómago",
        "Share Lung and Stomach Heat indications, with different emphasis on Fire, phlegm, dryness, thirst or vomiting." => "Comparten indicaciones de Calor de Pulmón y Estómago, con distinto énfasis en Fuego, flema, sequedad, sed o vómitos.",
        "Eliminate irritability and promote urination" => "Eliminar irritabilidad y promover la micción",
        "Clear Heart Heat and irritability; both can guide Heat out through urination, while Zhi Zi also treats Damp-Heat broadly." => "Eliminan Calor de Corazón e irritabilidad; ambas pueden expulsar Calor mediante la micción, mientras Zhi Zi también trata ampliamente Humedad-Calor.",
        "Clear Liver Heat and benefit the eyes" => "Eliminar Calor de Hígado y beneficiar los ojos",
        "Treat red or painful eyes from Liver Heat; Xia Ku Cao dissipates nodules, while Jue Ming Zi moistens the intestines." => "Tratan ojos rojos o dolorosos por Calor de Hígado; Xia Ku Cao disipa nódulos, mientras Jue Ming Zi humedece los Intestinos.",
        "Reduce swelling, nodules or toxicity" => "Reducir hinchazón, nódulos o toxicidad",
        "Address Heat-related swelling through different mechanisms: expelling pus, reducing swelling, or dissipating nodules." => "Tratan hinchazón relacionada con Calor mediante distintos mecanismos: expulsar pus, reducir hinchazón o disipar nódulos.",
        "Sweet, acrid" => "Dulce, picante",
        "Gently releases the exterior; eliminates irritability and constraint." => "Libera suavemente el exterior; elimina irritabilidad y opresión.",
        "13. Dissolve Hot Phlegm" => "13. Disolver Flema-Calor",
        "Board Guideline 2026 · 8 herbs · Common foundation: cool and transform Phlegm-Heat, stop cough, open the chest or soften nodules." => "Guía Board 2026 · 8 hierbas · Fundamento común: enfriar y transformar Flema-Calor, detener la tos, abrir el tórax o ablandar nódulos.",
        "cough type, dryness versus productive phlegm, chest constraint, nodules, bowel dryness, vomiting, edema and constitutional Cold." => "tipo de tos, sequedad frente a flema productiva, opresión torácica, nódulos, sequedad intestinal, vómitos, edema y Frío constitucional.",
        "Descends Lung Qi and expels phlegm; releases exterior Wind." => "Hace descender el Qi de Pulmón y expulsa flema; libera Viento exterior.",
        "Traditionally counteracts Li Lu." => "Tradicionalmente contrarresta Li Lu.",
        "Stops cough; moistens the Lung; transforms phlegm; treats Yin-deficiency dry cough with scant sputum that is difficult to expectorate; dissipates nodules." => "Detiene la tos; humedece el Pulmón; transforma flema; trata tos seca por deficiencia de Yin con esputo escaso y difícil de expectorar; disipa nódulos.",
        "Avoid in Spleen and Stomach deficiency; incompatible with Wu Tou; traditionally counteracts Qin Jiao." => "Evitar en deficiencia de Bazo y Estómago; incompatible con Wu Tou; tradicionalmente contrarresta Qin Jiao.",
        "Use 1–1.5 g when taken as powder." => "Usar 1–1.5 g cuando se administra en polvo.",
        "Clears Heat and transforms phlegm; stops productive cough from acute Lung Heat; dissipates nodules." => "Elimina Calor y transforma flema; detiene la tos productiva por Calor agudo de Pulmón; disipa nódulos.",
        "Avoid when phlegm is due to Damp-Cold; incompatible with Wu Tou." => "Evitar cuando la flema se debe a Humedad-Frío; incompatible con Wu Tou.",
        "Clears Heat and transforms phlegm; expands the chest and relieves constraint." => "Elimina Calor y transforma flema; expande el tórax y alivia la opresión.",
        "Avoid in Damp-Phlegm caused by Spleen weakness; incompatible with Wu Tou." => "Evitar en Flema-Humedad causada por debilidad de Bazo; incompatible con Wu Tou.",
        "Clears Heat and transforms phlegm; expands the chest; moistens the Intestines; dissipates nodules." => "Elimina Calor y transforma flema; expande el tórax; humedece los Intestinos; disipa nódulos.",
        "Avoid in Spleen and Stomach deficient Cold, Cold-Phlegm or Damp-Phlegm; incompatible with Wu Tou; traditionally antagonizes Gan Jiang and counteracts Niu Xi." => "Evitar en Frío por deficiencia de Bazo y Estómago, Flema-Frío o Flema-Humedad; incompatible con Wu Tou; tradicionalmente antagoniza Gan Jiang y contrarresta Niu Xi.",
        "Clears Heat and transforms phlegm; moistens the Intestines; dissipates nodules; promotes healing of sores." => "Elimina Calor y transforma flema; humedece los Intestinos; disipa nódulos; favorece la curación de llagas.",
        "Avoid in Spleen and Stomach deficient Cold, diarrhea or patterns without Heat; incompatible with Wu Tou." => "Evitar en Frío por deficiencia de Bazo y Estómago, diarrea o patrones sin Calor; incompatible con Wu Tou.",
        "Clears Phlegm-Heat; clears Gallbladder and Stomach Heat; stops vomiting." => "Elimina Flema-Calor; elimina Calor de Vesícula Biliar y Estómago; detiene los vómitos.",
        "Avoid in nausea or vomiting from Stomach Cold or cold food stagnation." => "Evitar en náuseas o vómitos por Frío de Estómago o estancamiento de alimentos fríos.",
        "Softens hardness and dissipates neck nodules; promotes urination and reduces edema; treats pain associated with hernial disorders." => "Ablanda durezas y disipa nódulos del cuello; promueve la micción y reduce el edema; trata dolor asociado con trastornos herniarios.",
        "Avoid in Spleen and Stomach deficient Cold; incompatible with Gan Cao." => "Evitar en Frío por deficiencia de Bazo y Estómago; incompatible con Gan Cao.",
        "Phlegm-Heat cough" => "Tos por Flema-Calor",
        "Qian Hu · Chuan Bei Mu · Zhe Bei Mu · Gua Lou · Zhu Ru" => "Qian Hu · Chuan Bei Mu · Zhe Bei Mu · Gua Lou · Zhu Ru",
        "All cool or transform Phlegm-Heat; Qian Hu descends Lung Qi, the Bei Mu herbs stop cough, Gua Lou opens the chest, and Zhu Ru stops vomiting." => "Todas enfrían o transforman Flema-Calor; Qian Hu hace descender el Qi de Pulmón, las Bei Mu detienen la tos, Gua Lou abre el tórax y Zhu Ru detiene los vómitos.",
        "Dry versus productive cough" => "Tos seca frente a tos productiva",
        "Chuan Bei Mu · Zhe Bei Mu" => "Chuan Bei Mu · Zhe Bei Mu",
        "Chuan Bei Mu emphasizes dry deficiency cough with scant sputum; Zhe Bei Mu emphasizes acute Lung Heat with productive cough." => "Chuan Bei Mu enfatiza tos seca por deficiencia con esputo escaso; Zhe Bei Mu enfatiza Calor agudo de Pulmón con tos productiva.",
        "Three parts of Gua Lou" => "Tres partes de Gua Lou",
        "Gua Lou Pi · Gua Lou · Gua Lou Ren" => "Gua Lou Pi · Gua Lou · Gua Lou Ren",
        "The peel emphasizes chest expansion, the whole fruit combines chest and intestinal actions, and the seed emphasizes moistening the Intestines." => "La cáscara enfatiza expandir el tórax, el fruto completo combina acciones torácicas e intestinales y la semilla enfatiza humedecer los Intestinos.",
        "Chest constraint and nodules" => "Opresión torácica y nódulos",
        "Gua Lou Pi · Gua Lou · Chuan Bei Mu · Zhe Bei Mu" => "Gua Lou Pi · Gua Lou · Chuan Bei Mu · Zhe Bei Mu",
        "Gua Lou Pi and Gua Lou open the chest, while the Bei Mu herbs dissipate nodules and transform phlegm." => "Gua Lou Pi y Gua Lou abren el tórax, mientras las Bei Mu disipan nódulos y transforman flema.",
        "Moisten the Intestines" => "Humedecer los Intestinos",
        "Gua Lou · Gua Lou Ren" => "Gua Lou · Gua Lou Ren",
        "Both moisten the Intestines; Gua Lou Ren is the seed and places greater emphasis on bowel dryness." => "Ambas humedecen los Intestinos; Gua Lou Ren es la semilla y enfatiza más la sequedad intestinal.",
        "Gallbladder and Stomach Phlegm-Heat" => "Flema-Calor de Vesícula Biliar y Estómago",
        "Zhu Ru" => "Zhu Ru",
        "Zhu Ru is the distinctive choice for Phlegm-Heat with irritability, Gallbladder Fire or nausea and vomiting." => "Zhu Ru es la elección distintiva para Flema-Calor con irritabilidad, Fuego de Vesícula Biliar o náuseas y vómitos.",
        "Hard nodules and edema" => "Nódulos duros y edema",
        "Hai Zao" => "Hai Zao",
        "Hai Zao softens hardness, dissipates neck nodules and promotes urination to reduce edema." => "Hai Zao ablanda durezas, disipa nódulos del cuello y promueve la micción para reducir edema.",
        "14. Dissolve Cold Phlegm" => "14. Disolver Flema-Frío",
        "Board Guideline 2026 · 6 herbs · Common foundation: warm and transform Cold-Phlegm, regulate Lung Qi, stop cough or resolve nodules." => "Guía Board 2026 · 6 hierbas · Fundamento común: calentar y transformar Flema-Frío, regular el Qi de Pulmón, detener la tos o resolver nódulos.",
        "Cold-Phlegm location, dry versus rebellious phlegm, cough, vomiting, throat involvement, channel obstruction, nodules, toxicity and preparation." => "localización de Flema-Frío, flema seca frente a rebelde, tos, vómitos, afectación de garganta, obstrucción de canales, nódulos, toxicidad y preparación.",
        "Dries Dampness and transforms phlegm; disperses Wind-Phlegm from the channels and stops spasms; reduces swelling and relieves pain when used topically." => "Seca Humedad y transforma flema; dispersa Viento-Flema de los canales y detiene espasmos; reduce hinchazón y alivia dolor cuando se usa tópicamente.",
        "Avoid in Yin deficiency, pregnancy, dry phlegm or hot and dry Lung patterns; traditionally counteracts Fu Zi, Gan Jiang and Sheng Jiang." => "Evitar en deficiencia de Yin, embarazo, flema seca o patrones calientes y secos de Pulmón; tradicionalmente contrarresta Fu Zi, Gan Jiang y Sheng Jiang.",
        "Use only the prepared form; Sheng Jiang and Ming Fan reduce its toxicity." => "Usar únicamente la forma preparada; Sheng Jiang y Ming Fan reducen su toxicidad.",
        "Dries Dampness and transforms phlegm; directs rebellious Stomach Qi downward and stops vomiting; dissipates nodules and reduces swelling." => "Seca Humedad y transforma flema; hace descender el Qi rebelde de Estómago y detiene los vómitos; disipa nódulos y reduce hinchazón.",
        "Avoid in bleeding, Yin-deficiency cough, depleted fluids or Heat patterns; incompatible with Wu Tou." => "Evitar en sangrado, tos por deficiencia de Yin, agotamiento de líquidos o patrones de Calor; incompatible con Wu Tou.",
        "Use only the properly prepared form internally." => "Usar internamente únicamente la forma preparada correctamente.",
        "Directs Lung Qi downward; transforms phlegm; treats vomiting, hiccup and belching." => "Hace descender el Qi de Pulmón; transforma flema; trata vómitos, hipo y eructos.",
        "Use sparingly in deficient conditions." => "Usar con moderación en afecciones por deficiencia.",
        "Wrap in cheesecloth before decocting." => "Envolver en tela de gasa antes de la decocción.",
        "Descends Lung Qi and expels phlegm; treats Lung Qi blockage and stagnation, especially cough with phlegm lodged in the throat." => "Hace descender el Qi de Pulmón y expulsa flema; trata obstrucción y estancamiento del Qi de Pulmón, especialmente tos con flema alojada en la garganta.",
        "Avoid in cough from Qi deficiency or Kidney failure to grasp Qi." => "Evitar en tos por deficiencia de Qi o incapacidad del Riñón para recibir el Qi.",
        "Warms the Lung, regulates Qi and expels phlegm; opens the channels and stops pain; dissipates nodules and reduces swelling." => "Calienta el Pulmón, regula el Qi y expulsa flema; abre los canales y detiene el dolor; disipa nódulos y reduce hinchazón.",
        "Avoid in cough from Lung deficiency or Yin-deficiency Heat; do not use topically on sensitive skin." => "Evitar en tos por deficiencia de Pulmón o Calor por deficiencia de Yin; no usar tópicamente sobre piel sensible.",
        "Do not decoct for a long period." => "No cocinar durante un período prolongado.",
        "Opens and diffuses Lung Qi; benefits the throat in both Cold-Phlegm and Phlegm-Heat; promotes discharge of pus; directs the effects of other herbs upward." => "Abre y difunde el Qi de Pulmón; beneficia la garganta tanto en Flema-Frío como en Flema-Calor; promueve la expulsión de pus; dirige hacia arriba los efectos de otras hierbas.",
        "Avoid in hemoptysis; traditionally counteracts Long Dan Cao and Long Yan Rou." => "Evitar en hemoptisis; tradicionalmente contrarresta Long Dan Cao y Long Yan Rou.",
        "Warm and transform Cold-Phlegm" => "Calentar y transformar Flema-Frío",
        "Tian Nan Xing · Ban Xia · Xuan Fu Hua · Bai Qian · Bai Jie Zi · Jie Geng" => "Tian Nan Xing · Ban Xia · Xuan Fu Hua · Bai Qian · Bai Jie Zi · Jie Geng",
        "All transform phlegm, but differ in emphasis on Dampness, rebellious Qi, Lung Qi descent, channel obstruction or throat involvement." => "Todas transforman flema, pero difieren en su énfasis sobre Humedad, Qi rebelde, descenso del Qi de Pulmón, obstrucción de canales o afectación de garganta.",
        "Dry Dampness and transform phlegm" => "Secar Humedad y transformar flema",
        "Tian Nan Xing · Ban Xia" => "Tian Nan Xing · Ban Xia",
        "Both strongly dry Dampness; Tian Nan Xing emphasizes Wind-Phlegm and spasms, while Ban Xia emphasizes Stomach Qi and vomiting." => "Ambas secan intensamente Humedad; Tian Nan Xing enfatiza Viento-Flema y espasmos, mientras Ban Xia enfatiza Qi de Estómago y vómitos.",
        "Rebellious Qi and vomiting" => "Qi rebelde y vómitos",
        "Ban Xia · Xuan Fu Hua" => "Ban Xia · Xuan Fu Hua",
        "Ban Xia descends rebellious Stomach Qi, while Xuan Fu Hua descends Lung Qi and treats vomiting, hiccup and belching." => "Ban Xia hace descender el Qi rebelde de Estómago, mientras Xuan Fu Hua hace descender el Qi de Pulmón y trata vómitos, hipo y eructos.",
        "Descend Lung Qi and stop cough" => "Hacer descender el Qi de Pulmón y detener la tos",
        "Xuan Fu Hua · Bai Qian" => "Xuan Fu Hua · Bai Qian",
        "Both descend Lung Qi; Xuan Fu Hua is useful when vomiting accompanies phlegm, while Bai Qian emphasizes cough with throat phlegm." => "Ambas hacen descender el Qi de Pulmón; Xuan Fu Hua es útil cuando los vómitos acompañan la flema, mientras Bai Qian enfatiza tos con flema en la garganta.",
        "Nodules and swelling" => "Nódulos e hinchazón",
        "Tian Nan Xing · Ban Xia · Bai Jie Zi" => "Tian Nan Xing · Ban Xia · Bai Jie Zi",
        "All dissipate nodules or swelling; Bai Jie Zi also opens channels and stops pain, while Tian Nan Xing may be used topically." => "Todas disipan nódulos o hinchazón; Bai Jie Zi también abre canales y detiene dolor, mientras Tian Nan Xing puede usarse tópicamente.",
        "Channel obstruction and pain" => "Obstrucción de canales y dolor",
        "Tian Nan Xing · Bai Jie Zi" => "Tian Nan Xing · Bai Jie Zi",
        "Tian Nan Xing addresses Wind-Phlegm with spasms; Bai Jie Zi warms the Lung and opens channels to stop pain." => "Tian Nan Xing trata Viento-Flema con espasmos; Bai Jie Zi calienta el Pulmón y abre canales para detener dolor.",
        "Open the Lung and benefit the throat" => "Abrir el Pulmón y beneficiar la garganta",
        "Bai Qian · Jie Geng" => "Bai Qian · Jie Geng",
        "Bai Qian descends Lung Qi and clears lodged phlegm; Jie Geng diffuses Lung Qi, benefits the throat and guides other herbs upward." => "Bai Qian hace descender el Qi de Pulmón y elimina flema alojada; Jie Geng difunde el Qi de Pulmón, beneficia la garganta y guía otras hierbas hacia arriba.",
        "Toxicity and prepared forms" => "Toxicidad y formas preparadas",
        "Tian Nan Xing · Ban Xia · Bai Jie Zi" => "Tian Nan Xing · Ban Xia · Bai Jie Zi",
        "Tian Nan Xing and Ban Xia require properly prepared forms; Bai Jie Zi requires care with sensitive skin and prolonged decoction." => "Tian Nan Xing y Ban Xia requieren formas preparadas correctamente; Bai Jie Zi exige precaución en piel sensible y decocción prolongada.",
        "Bitter, acrid, salty" => "Amargo, picante, salado",
        "15. Stop Cough and Wheezing" => "15. Detener la tos y las sibilancias",
        "Board Guideline 2026 · 7 herbs · Common foundation: relieve cough, stop wheezing, regulate Lung Qi, transform phlegm or moisten the Lung." => "Guía Board 2026 · 7 hierbas · Fundamento común: aliviar la tos, detener las sibilancias, regular el Qi de Pulmón, transformar flema o humedecer el Pulmón.",
        "cough temperature, dry versus phlegmy cough, rebellious Lung or Stomach Qi, bowel dryness, parasites, edema and constitutional deficiency." => "temperatura de la tos, tos seca frente a tos con flema, Qi rebelde de Pulmón o Estómago, sequedad intestinal, parásitos, edema y deficiencia constitucional.",
        "Slightly warm, slightly toxic" => "Ligeramente tibia, ligeramente tóxica",
        "Descends Lung Qi, stops cough and wheezing; broadly used for many types of cough, especially externally contracted dry cough; moistens the Intestines." => "Hace descender el Qi de Pulmón, detiene la tos y las sibilancias; se usa ampliamente para muchos tipos de tos, especialmente tos seca por factor exterior; humedece los Intestinos.",
        "Use cautiously with diarrhea and in infants. Traditionally antagonizes Huang Qi, Ge Gen and Huang Qin." => "Usar con precaución en diarrea y en lactantes. Tradicionalmente antagoniza Huang Qi, Ge Gen y Huang Qin.",
        "Crush and add near the end of decoction." => "Triturar y agregar cerca del final de la decocción.",
        "Descends Lung Qi, stops cough and wheezing; transforms phlegm; relieves a stifling sensation in the chest; moistens the Intestines." => "Hace descender el Qi de Pulmón, detiene la tos y las sibilancias; transforma flema; alivia sensación de opresión en el tórax; humedece los Intestinos.",
        "Avoid in chronic diarrhea." => "Evitar en diarrea crónica.",
        "Usually grind before use." => "Generalmente triturar antes de usar.",
        "Moistens the Lung; transforms phlegm; treats many types of cough and wheezing without being drying." => "Humedece el Pulmón; transforma flema; trata muchos tipos de tos y sibilancias sin resecar.",
        "Use cautiously in cough from Yin deficiency with Heat or cough from excessive Heat; prolonged use or large doses are not recommended. Traditionally counteracts Yin Chen Hao." => "Usar con precaución en tos por deficiencia de Yin con Calor o por Calor excesivo; no se recomiendan el uso prolongado ni dosis grandes. Tradicionalmente contrarresta Yin Chen Hao.",
        "Moistens the Lung; stops cough and wheezing in many patterns without being drying." => "Humedece el Pulmón; detiene la tos y las sibilancias en diversos patrones sin resecar.",
        "Avoid in Heat-type cough. Traditionally antagonizes Xuan Shen and counteracts Bei Mu, Xin Yi Hua, Ma Huang, Huang Qin, Huang Lian and Huang Qi." => "Evitar en tos tipo Calor. Tradicionalmente antagoniza Xuan Shen y contrarresta Bei Mu, Xin Yi Hua, Ma Huang, Huang Qin, Huang Lian y Huang Qi.",
        "Moistens the Lung and stops cough; expels parasites and kills lice when used externally." => "Humedece el Pulmón y detiene la tos; expulsa parásitos y elimina piojos cuando se usa externamente.",
        "Avoid in Spleen deficiency or loose stools." => "Evitar en deficiencia de Bazo o heces sueltas.",
        "Clears Lung Heat and transforms phlegm; descends rebellious Stomach Qi and stops nausea or vomiting from Stomach Heat." => "Elimina Calor de Pulmón y transforma flema; hace descender el Qi rebelde de Estómago y detiene náuseas o vómitos por Calor de Estómago.",
        "Avoid in vomiting from Stomach Cold or cough from externally contracted Cold in the Lung." => "Evitar en vómitos por Frío de Estómago o tos por Frío exterior alojado en el Pulmón.",
        "Clears Lung Heat; stops cough and wheezing; promotes urination and reduces edema; traditionally used in hypertension patterns." => "Elimina Calor de Pulmón; detiene la tos y las sibilancias; promueve la micción y reduce el edema; se usa tradicionalmente en patrones de hipertensión.",
        "Avoid in Lung deficiency, excessive urination or cough from Wind-Cold." => "Evitar en deficiencia de Pulmón, micción excesiva o tos por Viento-Frío.",
        "Descend Lung Qi and stop wheezing" => "Hacer descender el Qi de Pulmón y detener las sibilancias",
        "All descend rebellious Qi; Xing Ren is broad for cough and wheezing, Zi Su Zi adds phlegm and chest constraint, and Pi Pa Ye adds Stomach Heat vomiting." => "Todas hacen descender el Qi rebelde; Xing Ren abarca tos y sibilancias, Zi Su Zi añade flema y opresión torácica, y Pi Pa Ye añade vómitos por Calor de Estómago.",
        "Moistening cough herbs" => "Hierbas que humedecen en la tos",
        "All moisten the Lung without being strongly drying; select by phlegm, Heat and parasite involvement." => "Todas humedecen el Pulmón sin resecar intensamente; seleccionar según flema, Calor y participación de parásitos.",
        "Transform phlegm" => "Transformar flema",
        "Zi Su Zi transforms phlegm with chest fullness, Zi Wan treats varied cough, and Pi Pa Ye clears Lung Heat." => "Zi Su Zi transforma flema con plenitud torácica, Zi Wan trata diversos tipos de tos y Pi Pa Ye elimina Calor de Pulmón.",
        "Hot versus cold cough" => "Tos de Calor frente a tos de Frío",
        "Pi Pa Ye and Sang Bai Pi are cooling for Lung Heat; the warm herbs suit Cold or neutral patterns and require caution with Heat." => "Pi Pa Ye y Sang Bai Pi enfrían el Calor de Pulmón; las hierbas tibias son apropiadas para patrones de Frío o neutros y requieren precaución con Calor.",
        "Moisten the Intestines" => "Humedecer los Intestinos",
        "Both descend Lung Qi and moisten the Intestines, useful when cough overlaps with dry stool." => "Ambas hacen descender el Qi de Pulmón y humedecen los Intestinos, útiles cuando la tos coincide con heces secas.",
        "Nausea and vomiting" => "Náuseas y vómitos",
        "Pi Pa Ye uniquely descends rebellious Stomach Qi and stops nausea or vomiting from Stomach Heat." => "Pi Pa Ye se distingue por hacer descender el Qi rebelde de Estómago y detener náuseas o vómitos por Calor de Estómago.",
        "Parasites and external use" => "Parásitos y uso externo",
        "Bai Bu uniquely expels parasites and kills lice externally; larger guideline doses are reserved for pinworms." => "Bai Bu se distingue por expulsar parásitos y eliminar piojos externamente; las dosis mayores de la guía se reservan para oxiuros.",
        "Edema and hypertension patterns" => "Edema y patrones de hipertensión",
        "Sang Bai Pi clears Lung Heat, promotes urination, reduces edema and is traditionally used in hypertension patterns." => "Sang Bai Pi elimina Calor de Pulmón, promueve la micción, reduce edema y se usa tradicionalmente en patrones de hipertensión.",
        "16. Aromatics for Damp" => "16. Aromáticas que transforman Humedad",
        "Board Guideline 2026 · 7 herbs · Common foundation: transform excess and turbid Dampness from the Middle Jiao, regulate Qi and warm the center." => "Guía Board 2026 · 7 hierbas · Fundamento común: transformar Humedad excesiva y turbia del Jiao Medio, regular el Qi y calentar el centro.",
        "Dampness severity, exterior involvement, Qi stagnation, nausea or vomiting, Spleen Cold, pregnancy, malaria and constitutional deficiency." => "intensidad de la Humedad, participación exterior, estancamiento de Qi, náuseas o vómitos, Frío de Bazo, embarazo, malaria y deficiencia constitucional.",
        "Transforms Dampness; stops nausea and vomiting; releases the Exterior; treats Summer-Heat and may be used for dermatosis." => "Transforma Humedad; detiene náuseas y vómitos; libera el Exterior; trata Calor de Verano y puede utilizarse para dermatosis.",
        "Use cautiously in Yin deficiency with Heat or Stomach Fire." => "Usar con precaución en deficiencia de Yin con Calor o Fuego de Estómago.",
        "Do not cook for more than 15 minutes." => "No cocinar durante más de 15 minutos.",
        "Strongly dries Dampness and strengthens the Spleen; clears Dampness from the Lower Jiao; improves vision; releases the Exterior and induces sweating for Wind-Damp pathogens." => "Seca intensamente la Humedad y fortalece el Bazo; elimina Humedad del Jiao Inferior; mejora la visión; libera el Exterior e induce sudoración ante patógenos Viento-Humedad.",
        "Use cautiously with excessive sweating from Qi deficiency, Yin deficiency with Heat, or loose watery stools." => "Usar con precaución en sudoración excesiva por deficiencia de Qi, deficiencia de Yin con Calor o heces sueltas y acuosas.",
        "Moves Qi and resolves stagnation; directs Qi downward; reduces phlegm and calms wheezing." => "Mueve el Qi y resuelve estancamiento; dirige el Qi hacia abajo; reduce flema y calma las sibilancias.",
        "Use cautiously during pregnancy; traditionally antagonizes Ze Xie and Han Shui Shi." => "Usar con precaución durante el embarazo; tradicionalmente antagoniza Ze Xie y Han Shui Shi.",
        "Transforms Dampness; moves Qi and resolves stagnation; warms the Middle Jiao; descends rebellious Qi to stop nausea and vomiting." => "Transforma Humedad; mueve el Qi y resuelve estancamiento; calienta el Jiao Medio; hace descender el Qi rebelde para detener náuseas y vómitos.",
        "Avoid in Yin or Blood deficiency and when Cold-Damp is absent." => "Evitar en deficiencia de Yin o Sangre y cuando no existe Frío-Humedad.",
        "Transforms Dampness; moves Qi and resolves stagnation; warms the Middle Jiao; stops vomiting and diarrhea from Spleen Cold; calms the fetus and morning sickness; prevents stagnation from tonic herbs." => "Transforma Humedad; mueve el Qi y resuelve estancamiento; calienta el Jiao Medio; detiene vómitos y diarrea por Frío de Bazo; calma el feto y las náuseas matutinas; previene estancamiento causado por hierbas tónicas.",
        "Avoid in Yin deficiency with Heat." => "Evitar en deficiencia de Yin con Calor.",
        "Crush and add near the end." => "Triturar y agregar cerca del final.",
        "Dries Dampness; moves Qi and resolves stagnation; warms the Middle Jiao; descends rebellious Qi to relieve nausea and vomiting." => "Seca Humedad; mueve el Qi y resuelve estancamiento; calienta el Jiao Medio; hace descender el Qi rebelde para aliviar náuseas y vómitos.",
        "Avoid in Yin deficiency." => "Evitar en deficiencia de Yin.",
        "Add near the end." => "Agregar cerca del final.",
        "Strongly dries Dampness and disperses Cold; treats malarial disorders." => "Seca intensamente la Humedad y dispersa Frío; trata trastornos maláricos.",
        "Avoid in Qi or Blood deficiency and when Cold-Damp is absent; overdose may cause vomiting." => "Evitar en deficiencia de Qi o Sangre y cuando no existe Frío-Humedad; una sobredosis puede causar vómitos.",
        "Transform turbid Dampness in the Middle Jiao" => "Transformar Humedad turbia en el Jiao Medio",
        "All transform excess or turbid Dampness in the center; choose by dryness, Qi stagnation, Cold and accompanying symptoms." => "Todas transforman Humedad excesiva o turbia en el centro; seleccionar según sequedad, estancamiento de Qi, Frío y síntomas acompañantes.",
        "Release the Exterior with Dampness" => "Liberar el Exterior con Humedad",
        "Huo Xiang is gentler and especially useful for Summer-Heat with nausea, while Cang Zhu dries more strongly and promotes sweating." => "Huo Xiang es más suave y especialmente útil para Calor de Verano con náuseas, mientras Cang Zhu seca con mayor intensidad y promueve la sudoración.",
        "Strongly dry Dampness" => "Secar intensamente la Humedad",
        "Cang Zhu strongly dries and strengthens the Spleen; Cao Dou Kou warms the center; Cao Guo emphasizes Cold-Damp and malarial disorders." => "Cang Zhu seca intensamente y fortalece el Bazo; Cao Dou Kou calienta el centro; Cao Guo enfatiza Frío-Humedad y trastornos maláricos.",
        "Move Qi and resolve stagnation" => "Mover el Qi y resolver estancamiento",
        "Hou Po, Bai Dou Kou, Sha Ren and Cao Dou Kou move Qi; Hou Po descends Qi and calms wheezing, while the aromatic seeds emphasize the Middle Jiao." => "Hou Po, Bai Dou Kou, Sha Ren y Cao Dou Kou mueven el Qi; Hou Po hace descender el Qi y calma las sibilancias, mientras las semillas aromáticas enfatizan el Jiao Medio.",
        "Nausea and vomiting from Dampness" => "Náuseas y vómitos por Humedad",
        "Huo Xiang directly stops nausea and vomiting; Bai Dou Kou, Sha Ren and Cao Dou Kou warm the center and descend rebellious Qi." => "Huo Xiang detiene directamente náuseas y vómitos; Bai Dou Kou, Sha Ren y Cao Dou Kou calientan el centro y hacen descender el Qi rebelde.",
        "Warm the Middle Jiao" => "Calentar el Jiao Medio",
        "Bai Dou Kou, Sha Ren, Cao Dou Kou and Cao Guo address Cold-Damp in the center; Sha Ren is especially useful with diarrhea or pregnancy." => "Bai Dou Kou, Sha Ren, Cao Dou Kou y Cao Guo tratan Frío-Humedad en el centro; Sha Ren es especialmente útil con diarrea o embarazo.",
        "Distinctive special indications" => "Indicaciones especiales distintivas",
        "Cang Zhu improves vision, Sha Ren calms the fetus and Cao Guo is distinguished for malarial disorders." => "Cang Zhu mejora la visión, Sha Ren calma el feto y Cao Guo se distingue para trastornos maláricos.",
        "17. Digestives" => "17. Digestivas",
        "Board Guideline 2026 · 6 herbs · Common foundation: reduce food stagnation, support digestion and restore the movement of the Middle Jiao." => "Guía Board 2026 · 6 hierbas · Fundamento común: reducir estancamiento de alimentos, favorecer la digestión y restaurar el movimiento del Jiao Medio.",
        "food type, degree of retention, Spleen deficiency, Qi or phlegm involvement, lactation, stones and constitutional excess versus deficiency." => "tipo de alimento, grado de retención, deficiencia de Bazo, participación de Qi o flema, lactancia, cálculos y exceso frente a deficiencia constitucional.",
        "Reduces food stagnation, especially from meat or greasy foods; moves Blood stasis; charred form stops diarrhea; traditionally used in patterns involving hypertension, coronary artery disease or elevated cholesterol." => "Reduce estancamiento de alimentos, especialmente por carne o comidas grasosas; mueve estasis de Sangre; la forma carbonizada detiene diarrea; se usa tradicionalmente en patrones con hipertensión, enfermedad coronaria o colesterol elevado.",
        "Avoid in Spleen and Stomach deficiency without food stagnation or with acid regurgitation." => "Evitar en deficiencia de Bazo y Estómago sin estancamiento de alimentos o con regurgitación ácida.",
        "Reduces food stagnation from starchy foods; strengthens digestion; soothes Liver Qi; inhibits lactation at larger doses." => "Reduce estancamiento de alimentos ricos en almidón; fortalece la digestión; suaviza el Qi de Hígado; inhibe la lactancia en dosis mayores.",
        "Avoid during nursing except at a very low dose; larger doses inhibit lactation." => "Evitar durante la lactancia salvo en dosis muy bajas; las dosis mayores inhiben la lactancia.",
        "Reduces food stagnation from grains and starchy foods; strengthens the Spleen and Stomach." => "Reduce estancamiento de alimentos por granos y almidones; fortalece el Bazo y el Estómago.",
        "No specific caution noted; avoid unnecessary use in pure deficiency without food retention." => "No se señala una precaución específica; evitar uso innecesario en deficiencia pura sin retención de alimentos.",
        "Reduces food stagnation; harmonizes the Stomach; aids digestion and absorption of pills containing heavy minerals." => "Reduce estancamiento de alimentos; armoniza el Estómago; ayuda a digerir y absorber píldoras que contienen minerales pesados.",
        "Use cautiously with Stomach Fire and during pregnancy." => "Usar con precaución en Fuego de Estómago y durante el embarazo.",
        "Strongly reduces many types of food retention and improves Spleen transformation and transportation; secures Essence and stops enuresis; transforms urinary or biliary stones." => "Reduce intensamente muchos tipos de retención de alimentos y mejora la transformación y el transporte del Bazo; asegura la Esencia y detiene enuresis; transforma cálculos urinarios o biliares.",
        "Avoid in Spleen deficiency without food retention." => "Evitar en deficiencia de Bazo sin retención de alimentos.",
        "More effective when taken as powder." => "Más eficaz cuando se toma en polvo.",
        "Reduces food accumulation with fullness and distension; descends Lung Qi and transforms phlegm for cough and wheezing." => "Reduce acumulación de alimentos con plenitud y distensión; hace descender el Qi de Pulmón y transforma flema para tos y sibilancias.",
        "Avoid in Qi deficiency; traditionally antagonizes Ren Shen." => "Evitar en deficiencia de Qi; tradicionalmente antagoniza Ren Shen.",
        "Crush before use." => "Triturar antes de usar.",
        "Reduce food stagnation" => "Reducir estancamiento de alimentos",
        "All six reduce food retention, but differ by food type, strength and accompanying Qi, Blood, phlegm or urinary involvement." => "Las seis reducen retención de alimentos, pero difieren según el tipo de comida, la intensidad y la participación de Qi, Sangre, flema o vías urinarias.",
        "Meat and greasy foods" => "Carne y comidas grasosas",
        "Shan Zha is the distinctive choice for meat or greasy food stagnation and also moves Blood stasis." => "Shan Zha es la elección distintiva para estancamiento por carne o comidas grasosas y además mueve estasis de Sangre.",
        "Grains and starchy foods" => "Granos y alimentos ricos en almidón",
        "Mai Ya and Gu Ya reduce grain and starch retention; Mai Ya also soothes Liver Qi and can inhibit lactation, while Gu Ya gently strengthens the Spleen and Stomach." => "Mai Ya y Gu Ya reducen retención de granos y almidones; Mai Ya además suaviza el Qi de Hígado y puede inhibir la lactancia, mientras Gu Ya fortalece suavemente el Bazo y el Estómago.",
        "Minerals and difficult pills" => "Minerales y píldoras difíciles de digerir",
        "Shen Qu harmonizes the Stomach and is traditionally added to formulas with heavy mineral ingredients to aid digestion and absorption." => "Shen Qu armoniza el Estómago y se añade tradicionalmente a fórmulas con minerales pesados para facilitar digestión y absorción.",
        "Broad and strong food retention" => "Retención de alimentos amplia e intensa",
        "Ji Nei Jin strongly addresses varied food retention and additionally treats enuresis and urinary or biliary stones." => "Ji Nei Jin trata intensamente diversos tipos de retención de alimentos y además aborda enuresis y cálculos urinarios o biliares.",
        "Fullness, distension, phlegm and cough" => "Plenitud, distensión, flema y tos",
        "Lai Fu Zi is preferred when food stagnation combines with Qi constraint, abdominal distension, phlegm, cough or wheezing." => "Lai Fu Zi se prefiere cuando el estancamiento de alimentos se combina con bloqueo de Qi, distensión abdominal, flema, tos o sibilancias.",
        "Distinctive secondary actions" => "Acciones secundarias distintivas",
        "Shan Zha moves Blood, Mai Ya affects lactation, Ji Nei Jin treats stones and enuresis, and Lai Fu Zi descends Lung Qi." => "Shan Zha mueve Sangre, Mai Ya afecta la lactancia, Ji Nei Jin trata cálculos y enuresis, y Lai Fu Zi hace descender el Qi de Pulmón.",
        "Sour, sweet" => "Ácido, dulce",
        "18. Regulate Qi" => "18. Regular el Qi",
        "Board Guideline 2026 · 10 herbs · Common foundation: regulate Qi, remove stagnation, relieve pain and restore movement through the Middle and Lower Jiao." => "Guía Board 2026 · 10 hierbas · Fundamento común: regular el Qi, eliminar estancamiento, aliviar dolor y restaurar el movimiento en el Jiao Medio e Inferior.",
        "location of Qi stagnation, strength needed, Heat or Cold, phlegm, food or Dampness, menstruation, urination, wheezing and constitutional deficiency." => "localización del estancamiento de Qi, intensidad necesaria, Calor o Frío, flema, alimentos o Humedad, menstruación, micción, sibilancias y deficiencia constitucional.",
        "Regulates the Middle Jiao; relieves diaphragm fullness, nausea and vomiting; dries Dampness and transforms phlegm; prevents stagnation from tonic herbs." => "Regula el Jiao Medio; alivia plenitud del diafragma, náuseas y vómitos; seca Humedad y transforma flema; previene estancamiento causado por hierbas tónicas.",
        "Avoid in dry cough from Qi or Yin deficiency, Heat-phlegm with a red tongue, dry cough from excess Heat, or hemoptysis." => "Evitar en tos seca por deficiencia de Qi o Yin, flema-Calor con lengua roja, tos seca por exceso de Calor o hemoptisis.",
        "Breaks up constrained Liver Qi; dissipates clumps and stagnation; treats breast masses associated with Dampness or phlegm." => "Rompe el bloqueo de Qi de Hígado; disipa masas y estancamiento; trata masas mamarias asociadas con Humedad o flema.",
        "Avoid in Qi deficiency." => "Evitar en deficiencia de Qi.",
        "Strongly directs Qi downward; moves stool; transforms phlegm and relieves focal distension; may support formulas for Middle Jiao Qi sinking." => "Dirige intensamente el Qi hacia abajo; moviliza las heces; transforma flema y alivia distensión focal; puede apoyar fórmulas para hundimiento de Qi del Jiao Medio.",
        "Avoid during pregnancy, with weak upright Qi, or with a cold deficient Stomach." => "Evitar durante el embarazo, con Qi correcto débil o con Estómago frío y deficiente.",
        "Gently regulates Qi and relieves distension; milder than Zhi Shi and more suitable for weak patients." => "Regula suavemente el Qi y alivia distensión; es más suave que Zhi Shi y más apropiada para pacientes débiles.",
        "Use cautiously during pregnancy." => "Usar con precaución durante el embarazo.",
        "Directs Qi downward and reduces stagnation; promotes urination and moves Dampness." => "Dirige el Qi hacia abajo y reduce estancamiento; promueve la micción y moviliza Humedad.",
        "Regulates Liver Qi, menstruation and pain; soothes emotional constraint and gynecologic Qi stagnation." => "Regula el Qi de Hígado, la menstruación y el dolor; suaviza bloqueo emocional y estancamiento ginecológico de Qi.",
        "Avoid in Qi deficiency without stagnation or in Yin or Blood deficiency with Heat." => "Evitar en deficiencia de Qi sin estancamiento o en deficiencia de Yin o Sangre con Calor.",
        "Regulates Liver Qi and relieves pain; clears Heat, dries Dampness and kills parasites." => "Regula el Qi de Hígado y alivia dolor; elimina Calor, seca Humedad y elimina parásitos.",
        "Avoid in Spleen and Stomach deficiency with Cold." => "Evitar en deficiencia de Bazo y Estómago con Frío.",
        "Dispels Cold, regulates Qi and relieves pain; warms the Kidney and improves frequent urination." => "Dispersa Frío, regula el Qi y alivia dolor; calienta el Riñón y mejora la micción frecuente.",
        "Avoid in Qi deficiency or Interior Heat." => "Evitar en deficiencia de Qi o Calor Interior.",
        "Directs rebellious Qi downward and regulates the Middle Jiao; warms the Kidney and assists asthma or wheezing." => "Dirige el Qi rebelde hacia abajo y regula el Jiao Medio; calienta el Riñón y ayuda en asma o sibilancias.",
        "Avoid in prolapse from Qi deficiency or Yin deficiency with Heat." => "Evitar en prolapso por deficiencia de Qi o deficiencia de Yin con Calor.",
        "Add near the end of the decoction." => "Agregar cerca del final de la decocción.",
        "Regulates stagnant Qi in the Intestines, relieves pain and distension, and prevents cloying tonic herbs from obstructing Spleen transformation and transportation." => "Regula Qi estancado en los Intestinos, alivia dolor y distensión y evita que hierbas tónicas empalagosas obstruyan la transformación y el transporte del Bazo.",
        "Avoid in Yin deficiency or depleted fluids." => "Evitar en deficiencia de Yin o líquidos agotados.",
        "Regulate Qi and remove stagnation" => "Regular el Qi y eliminar estancamiento",
        "All ten regulate Qi, but their clinical emphasis differs by organ, temperature, strength, pain, phlegm, Dampness and accompanying deficiency." => "Las diez regulan el Qi, pero su énfasis clínico difiere según órgano, temperatura, intensidad, dolor, flema, Humedad y deficiencia acompañante.",
        "Same citrus fruit: mature and immature peel" => "Mismo cítrico: cáscara madura e inmadura",
        "Chen Pi is the mature peel and gently harmonizes the Middle while transforming phlegm; Qing Pi is the immature peel and more strongly breaks constrained Liver Qi." => "Chen Pi es la cáscara madura y armoniza suavemente el Jiao Medio mientras transforma flema; Qing Pi es la cáscara inmadura y rompe con mayor intensidad el Qi de Hígado bloqueado.",
        "Bitter orange: immature and mature fruit" => "Naranja amarga: fruto inmaduro y maduro",
        "Zhi Shi is immature, stronger and more downward-directing; Zhi Ke is mature, gentler and more appropriate for weaker patients." => "Zhi Shi es inmaduro, más fuerte y dirige más hacia abajo; Zhi Ke es maduro, más suave y más apropiado para pacientes débiles.",
        "Break severe Qi accumulation" => "Romper acumulación intensa de Qi",
        "Qing Pi and Zhi Shi act more forcefully on clumps, focal distension and entrenched stagnation than the gentler Qi regulators." => "Qing Pi y Zhi Shi actúan con mayor fuerza sobre masas, distensión focal y estancamiento arraigado que los reguladores suaves de Qi.",
        "Soothe Liver Qi and relieve pain" => "Suavizar el Qi de Hígado y aliviar dolor",
        "Qing Pi, Xiang Fu and Chuan Lian Zi regulate Liver Qi; Xiang Fu emphasizes menstruation, while Chuan Lian Zi clears Heat and kills parasites." => "Qing Pi, Xiang Fu y Chuan Lian Zi regulan el Qi de Hígado; Xiang Fu enfatiza menstruación, mientras Chuan Lian Zi elimina Calor y parásitos.",
        "Direct Qi downward" => "Dirigir el Qi hacia abajo",
        "Zhi Shi and Da Fu Pi reduce abdominal stagnation; Chen Xiang descends rebellious Qi and warms the Kidney; Chen Pi also addresses nausea and vomiting." => "Zhi Shi y Da Fu Pi reducen estancamiento abdominal; Chen Xiang hace descender Qi rebelde y calienta el Riñón; Chen Pi también aborda náuseas y vómitos.",
        "Warm the Kidney and Lower Jiao" => "Calentar el Riñón y el Jiao Inferior",
        "Wu Yao is useful for Cold pain and frequent urination; Chen Xiang warms the Kidney while descending rebellious Qi for asthma or wheezing." => "Wu Yao es útil para dolor por Frío y micción frecuente; Chen Xiang calienta el Riñón mientras hace descender Qi rebelde en asma o sibilancias.",
        "Prevent stagnation from tonics" => "Prevenir estancamiento causado por tónicos",
        "Chen Pi and Mu Xiang prevent rich tonic herbs from obstructing digestion; Mu Xiang especially regulates intestinal Qi." => "Chen Pi y Mu Xiang evitan que hierbas tónicas densas obstruyan la digestión; Mu Xiang regula especialmente el Qi intestinal.",
        "Acrid, slightly bitter, slightly sweet" => "Picante, ligeramente amarga, ligeramente dulce",
        "Cold, slightly toxic" => "Fría, ligeramente tóxica",
        "19. Stop Bleeding" => "19. Detener el sangrado",
        "Board Guideline 2026 · 10 herbs · Common foundation: stop bleeding by moving, astringing, cooling or warming the Blood according to the pattern." => "Guía Board 2026 · 10 hierbas · Fundamento común: detener el sangrado moviendo, astringiendo, enfriando o calentando la Sangre según el patrón.",
        "bleeding mechanism and location, Blood stasis, Heat or Cold, trauma, pregnancy, diarrhea or dysentery, Lung symptoms and topical tissue repair." => "mecanismo y localización del sangrado, estasis de Sangre, Calor o Frío, trauma, embarazo, diarrea o disentería, síntomas de Pulmón y reparación tópica de tejidos.",
        "Astringes Blood and stops bleeding while moving Blood; promotes urination." => "Astringe la Sangre y detiene el sangrado mientras mueve Sangre; promueve la micción.",
        "Avoid during pregnancy." => "Evitar durante el embarazo.",
        "Stops bleeding without retaining stasis; reduces swelling and relieves pain, especially after trauma." => "Detiene el sangrado sin retener estasis; reduce hinchazón y alivia dolor, especialmente después de trauma.",
        "Avoid during pregnancy; use cautiously in Blood or Yin deficiency." => "Evitar durante el embarazo; usar con precaución en deficiencia de Sangre o Yin.",
        "Cools Blood, stops bleeding and moves Blood stasis; especially useful for trauma." => "Enfría la Sangre, detiene el sangrado y mueve estasis de Sangre; especialmente útil para trauma.",
        "Astringes Blood and stops bleeding; alleviates diarrhea and dysentery; eliminates parasites associated with trichomonas vaginitis or tapeworm." => "Astringe la Sangre y detiene el sangrado; alivia diarrea y disentería; elimina parásitos asociados con vaginitis por tricomonas o tenia.",
        "May cause nausea or vomiting." => "Puede causar náuseas o vómitos.",
        "Astringes Blood and stops bleeding, especially from the Lung or Stomach; reduces swelling and generates flesh when used topically." => "Astringe la Sangre y detiene el sangrado, especialmente de Pulmón o Estómago; reduce hinchazón y genera tejido cuando se usa tópicamente.",
        "Avoid in early Lung abscess, hemoptysis from an Exterior condition, or excessive Lung and Stomach Heat; incompatible with Wu Tou and Fu Zi." => "Evitar en etapa inicial de absceso pulmonar, hemoptisis por condición Exterior o Calor excesivo de Pulmón y Estómago; incompatible con Wu Tou y Fu Zi.",
        "Cools Blood and stops Large Intestine bleeding, especially hemorrhoids or dysentery; clears Liver Heat." => "Enfría la Sangre y detiene sangrado de Intestino Grueso, especialmente hemorroides o disentería; elimina Calor de Hígado.",
        "Cools Blood and stops bleeding; clears Heat and generates flesh topically, especially for burns." => "Enfría la Sangre y detiene el sangrado; elimina Calor y genera tejido tópicamente, especialmente para quemaduras.",
        "Avoid in cold deficient patterns." => "Evitar en patrones fríos y deficientes.",
        "Cools Blood and stops bleeding; stops cough and transforms phlegm." => "Enfría la Sangre y detiene el sangrado; detiene la tos y transforma flema.",
        "Long-term or large doses may cause nausea, vomiting or dizziness." => "El uso prolongado o dosis grandes pueden causar náuseas, vómitos o mareos.",
        "Cools Blood and stops bleeding; clears Heat from the Lung and Stomach and promotes urination." => "Enfría la Sangre y detiene el sangrado; elimina Calor de Pulmón y Estómago y promueve la micción.",
        "Avoid in Cold from Spleen deficiency." => "Evitar en Frío por deficiencia de Bazo.",
        "Warms the womb and stops bleeding; calms the fetus; disperses Cold and relieves pain; eliminates Dampness and stops itching." => "Calienta el útero y detiene el sangrado; calma el feto; dispersa Frío y alivia dolor; elimina Humedad y detiene picazón.",
        "Avoid in Blood Heat or Yin deficiency." => "Evitar en Calor de Sangre o deficiencia de Yin.",
        "Stop bleeding by pattern" => "Detener el sangrado según el patrón",
        "All ten stop bleeding, but selection depends on whether the pattern requires moving Blood, astringing, cooling Blood or warming the womb." => "Las diez detienen el sangrado, pero la selección depende de si el patrón requiere mover Sangre, astringir, enfriar la Sangre o calentar el útero.",
        "Move Blood and stop bleeding" => "Mover Sangre y detener el sangrado",
        "Pu Huang, San Qi and Qian Cao Gen stop bleeding while addressing stasis; San Qi emphasizes trauma and pain, while Qian Cao Gen is cooling." => "Pu Huang, San Qi y Qian Cao Gen detienen el sangrado mientras tratan estasis; San Qi enfatiza trauma y dolor, mientras Qian Cao Gen es fría.",
        "Astringe Blood and stop bleeding" => "Astringir la Sangre y detener el sangrado",
        "Pu Huang, Xian He Cao and Bai Ji have astringent action; Bai Ji strongly emphasizes Lung and Stomach bleeding and topical tissue repair." => "Pu Huang, Xian He Cao y Bai Ji tienen acción astringente; Bai Ji enfatiza sangrado de Pulmón y Estómago y reparación tópica de tejidos.",
        "Cool Blood and stop bleeding" => "Enfriar la Sangre y detener el sangrado",
        "Qian Cao Gen, Huai Hua Mi, Di Yu, Ce Bai Ye and Bai Mao Gen treat bleeding associated with Heat, with different organ and symptom emphasis." => "Qian Cao Gen, Huai Hua Mi, Di Yu, Ce Bai Ye y Bai Mao Gen tratan sangrado asociado con Calor, con diferentes énfasis de órgano y síntomas.",
        "Large Intestine bleeding and dysentery" => "Sangrado de Intestino Grueso y disentería",
        "Xian He Cao treats diarrhea and dysentery, Huai Hua Mi emphasizes hemorrhoidal bleeding, and Di Yu clears Heat and repairs tissue." => "Xian He Cao trata diarrea y disentería, Huai Hua Mi enfatiza sangrado hemorroidal y Di Yu elimina Calor y repara tejido.",
        "Trauma and topical tissue repair" => "Trauma y reparación tópica de tejidos",
        "San Qi is central for traumatic bleeding and pain; Bai Ji and Di Yu reduce swelling or generate flesh when used topically." => "San Qi es central para sangrado traumático y dolor; Bai Ji y Di Yu reducen hinchazón o generan tejido cuando se usan tópicamente.",
        "Lung bleeding, cough and phlegm" => "Sangrado de Pulmón, tos y flema",
        "Bai Ji astringes Lung bleeding, Ce Bai Ye stops cough and transforms phlegm, and Bai Mao Gen clears Lung Heat." => "Bai Ji astringe sangrado de Pulmón, Ce Bai Ye detiene la tos y transforma flema y Bai Mao Gen elimina Calor de Pulmón.",
        "Warm the womb and stop bleeding" => "Calentar el útero y detener el sangrado",
        "Ai Ye is the distinctive warm herb for uterine bleeding from Cold and also calms the fetus; it is inappropriate for Blood Heat." => "Ai Ye es la hierba tibia distintiva para sangrado uterino por Frío y además calma el feto; no es apropiada para Calor de Sangre.",
        "Bitter, astringent" => "Amarga, astringente",
        "Bitter, sour" => "Amarga, ácida",
        "20. Activate Blood" => "20. Activar la Sangre",
        "Board Guideline 2026 · 16 herbs · Common foundation: invigorate Blood, remove Blood stasis, regulate menstruation and relieve pain." => "Guía Board 2026 · 16 hierbas · Fundamento común: vigorizar la Sangre, eliminar estasis de Sangre, regular la menstruación y aliviar dolor.",
        "pain location and cause, Heat or Cold, menstrual involvement, trauma, fixed masses, swelling, downward direction, pregnancy and constitutional deficiency." => "localización y causa del dolor, Calor o Frío, participación menstrual, trauma, masas fijas, hinchazón, dirección descendente, embarazo y deficiencia constitucional.",
        "Invigorates Blood and Qi; expels Wind and relieves many types of headache and pain." => "Vigoriza la Sangre y el Qi; expulsa Viento y alivia muchos tipos de cefalea y dolor.",
        "Avoid in Yin deficiency with Heat, Liver Yang rising headache, Qi deficiency, excessive menstrual bleeding or hemoptysis; overdose may cause nausea, vomiting or dizziness." => "Evitar en deficiencia de Yin con Calor, cefalea por ascenso de Yang de Hígado, deficiencia de Qi, sangrado menstrual excesivo o hemoptisis; una sobredosis puede causar náuseas, vómitos o mareos.",
        "Strongly moves Qi and Blood and relieves pain from many causes and locations." => "Mueve intensamente el Qi y la Sangre y alivia dolor de muchas causas y localizaciones.",
        "Cools Blood, moves Qi and Blood, benefits the Gallbladder for jaundice or stones, clears Heart Heat and opens the orifices." => "Enfría la Sangre, mueve Qi y Sangre, beneficia la Vesícula Biliar en ictericia o cálculos, elimina Calor de Corazón y abre los orificios.",
        "Avoid in Yin deficiency from Blood loss or during pregnancy; traditionally antagonizes Ding Xiang." => "Evitar en deficiencia de Yin por pérdida de Sangre o durante el embarazo; tradicionalmente antagoniza Ding Xiang.",
        "Invigorates and nourishes Blood, cools Blood, calms irritability and regulates menstruation." => "Vigoriza y nutre la Sangre, enfría la Sangre, calma irritabilidad y regula la menstruación.",
        "Avoid when there is no Blood stasis; traditionally incompatible with Li Lu." => "Evitar cuando no existe estasis de Sangre; tradicionalmente incompatible con Li Lu.",
        "Cools and invigorates Blood; clears Liver Fire and relieves pain from Blood stasis." => "Enfría y vigoriza la Sangre; elimina Fuego de Hígado y alivia dolor por estasis de Sangre.",
        "Invigorates Blood and regulates menstruation; promotes urination and reduces swelling; clears Heat and resolves toxicity." => "Vigoriza la Sangre y regula la menstruación; promueve la micción y reduce hinchazón; elimina Calor y resuelve toxicidad.",
        "Invigorates Blood, regulates menstruation and promotes urination; treats trauma internally or topically." => "Vigoriza la Sangre, regula la menstruación y promueve la micción; trata trauma interna o tópicamente.",
        "Avoid during pregnancy when Blood stasis is absent." => "Evitar durante el embarazo cuando no existe estasis de Sangre.",
        "Breaks Blood stasis, unblocks menstruation and moistens the Intestines to relieve constipation." => "Rompe estasis de Sangre, desbloquea la menstruación y humedece los Intestinos para aliviar estreñimiento.",
        "Invigorates Blood, unblocks menstruation and relieves pain; lower doses gently harmonize Blood." => "Vigoriza la Sangre, desbloquea la menstruación y alivia dolor; las dosis menores armonizan suavemente la Sangre.",
        "Invigorates Blood and relieves pain; reduces swelling and promotes healing when used topically." => "Vigoriza la Sangre y alivia dolor; reduce hinchazón y promueve cicatrización cuando se usa tópicamente.",
        "Avoid during pregnancy or excessive uterine bleeding." => "Evitar durante el embarazo o sangrado uterino excesivo.",
        "Invigorates Blood, relaxes sinews and opens channels; reduces swelling and generates flesh topically." => "Vigoriza la Sangre, relaja tendones y abre canales; reduce hinchazón y genera tejido tópicamente.",
        "Avoid during pregnancy; use cautiously in Spleen deficiency." => "Evitar durante el embarazo; usar con precaución en deficiencia de Bazo.",
        "Strongly breaks Blood stasis and fixed accumulations, moves Qi and relieves pain; also treats childhood nutritional impairment." => "Rompe intensamente estasis de Sangre y acumulaciones fijas, mueve el Qi y alivia dolor; también trata deterioro nutricional infantil.",
        "Avoid during pregnancy or in deficient patients." => "Evitar durante el embarazo o en pacientes deficientes.",
        "Strongly breaks Blood stasis and accumulations; moves Qi more strongly than San Leng." => "Rompe intensamente estasis de Sangre y acumulaciones; mueve el Qi con más fuerza que San Leng.",
        "Avoid during pregnancy, Qi or Blood deficiency, or excessive menstruation." => "Evitar durante el embarazo, deficiencia de Qi o Sangre o menstruación excesiva.",
        "Invigorates Blood and directs it downward; tonifies Liver and Kidney, strengthens sinews and bones, and clears Damp-Heat from the Lower Jiao." => "Vigoriza la Sangre y la dirige hacia abajo; tonifica Hígado y Riñón, fortalece tendones y huesos y elimina Humedad-Calor del Jiao Inferior.",
        "Avoid in diarrhea from Spleen deficiency, excessive menstruation or spermatorrhea; traditionally should not be used with Bai Qian." => "Evitar en diarrea por deficiencia de Bazo, menstruación excesiva o espermatorrea; tradicionalmente no debe usarse con Bai Qian.",
        "Invigorates Blood and directs it downward; dispels Wind-Damp and unblocks menstruation." => "Vigoriza la Sangre y la dirige hacia abajo; dispersa Viento-Humedad y desbloquea la menstruación.",
        "Avoid during pregnancy, excessive menstruation or spermatorrhea." => "Evitar durante el embarazo, menstruación excesiva o espermatorrea.",
        "Invigorates Blood, unblocks menstruation, promotes lactation and reduces swelling of the breasts or testicles." => "Vigoriza la Sangre, desbloquea la menstruación, promueve la lactancia y reduce hinchazón de mamas o testículos.",
        "Invigorate Blood and remove stasis" => "Vigorizar la Sangre y eliminar estasis",
        "All sixteen activate Blood, but selection depends on pain, temperature, menstruation, trauma, fixed accumulation, swelling and associated organ patterns." => "Las dieciséis activan la Sangre, pero la selección depende de dolor, temperatura, menstruación, trauma, acumulación fija, hinchazón y patrones orgánicos asociados.",
        "Pain and headache emphasis" => "Énfasis en dolor y cefalea",
        "Chuan Xiong treats headache and pain while moving Qi; Yan Hu Suo is among the strongest choices for pain of varied causes; Mo Yao and Ru Xiang emphasize painful stasis and trauma." => "Chuan Xiong trata cefalea y dolor mientras mueve Qi; Yan Hu Suo está entre las opciones más fuertes para dolor de diversas causas; Mo Yao y Ru Xiang enfatizan estasis dolorosa y trauma.",
        "Cool Blood while moving stasis" => "Enfriar la Sangre mientras se mueve estasis",
        "Yu Jin, Dan Shen, Chi Shao and Yi Mu Cao are cooling choices; they differ in Gallbladder, Heart, Liver, menstruation and urination emphasis." => "Yu Jin, Dan Shen, Chi Shao y Yi Mu Cao son opciones refrescantes; difieren en énfasis sobre Vesícula Biliar, Corazón, Hígado, menstruación y micción.",
        "Regulate menstruation and amenorrhea" => "Regular menstruación y amenorrea",
        "Yi Mu Cao, Ze Lan, Tao Ren, Hong Hua and Wang Bu Liu Xing unblock or regulate menstruation; Wang Bu Liu Xing also promotes lactation." => "Yi Mu Cao, Ze Lan, Tao Ren, Hong Hua y Wang Bu Liu Xing desbloquean o regulan la menstruación; Wang Bu Liu Xing además promueve lactancia.",
        "Tao Ren and Hong Hua pair" => "Pareja Tao Ren y Hong Hua",
        "Tao Ren strongly breaks stasis and moistens the Intestines; Hong Hua unblocks menses and relieves pain, with smaller doses gently harmonizing Blood." => "Tao Ren rompe intensamente estasis y humedece los Intestinos; Hong Hua desbloquea la menstruación y alivia dolor, y las dosis menores armonizan suavemente la Sangre.",
        "Trauma, swelling and topical healing" => "Trauma, hinchazón y cicatrización tópica",
        "Ze Lan treats trauma internally or topically; Mo Yao and Ru Xiang reduce swelling, relieve pain and promote healing or tissue generation." => "Ze Lan trata trauma interna o tópicamente; Mo Yao y Ru Xiang reducen hinchazón, alivian dolor y promueven cicatrización o generación de tejido.",
        "Break fixed Blood accumulations" => "Romper acumulaciones fijas de Sangre",
        "San Leng and E Zhu strongly break stasis and masses; E Zhu moves Qi more strongly, while San Leng also addresses childhood nutritional impairment." => "San Leng y E Zhu rompen intensamente estasis y masas; E Zhu mueve el Qi con más fuerza, mientras San Leng también aborda deterioro nutricional infantil.",
        "Huai Niu Xi and Chuan Niu Xi" => "Huai Niu Xi y Chuan Niu Xi",
        "Both direct Blood downward; Huai Niu Xi emphasizes tonifying Liver and Kidney and strengthening sinews and bones, while Chuan Niu Xi emphasizes moving Blood and dispelling Wind-Damp." => "Ambas dirigen la Sangre hacia abajo; Huai Niu Xi enfatiza tonificar Hígado y Riñón y fortalecer tendones y huesos, mientras Chuan Niu Xi enfatiza mover Sangre y dispersar Viento-Humedad.",
        "Pregnancy and strong Blood movement" => "Embarazo y movimiento intenso de Sangre",
        "Most herbs in this category require avoidance or strong caution during pregnancy because they invigorate Blood or break stasis." => "La mayoría de las hierbas de esta categoría requieren evitarse o usarse con gran precaución durante el embarazo porque vigorizan la Sangre o rompen estasis.",
        "Sour, bitter" => "Ácida, amarga",
        "21. Warm Interior" => "21. Calentar el Interior",
        "Board Guideline 2026 · 12 herbs · Common foundation: warm the Interior, expel Cold, restore Yang and alleviate pain." => "Guía Board 2026 · 12 hierbas · Fundamento común: calentar el Interior, expulsar Frío, restaurar Yang y aliviar dolor.",
        "Cold location and severity, Yang collapse, Middle Jiao or Kidney involvement, rebellious Qi, pain, phlegm, toxicity, pregnancy and required preparation." => "localización e intensidad del Frío, colapso de Yang, participación del Jiao Medio o Riñón, Qi rebelde, dolor, flema, toxicidad, embarazo y preparación necesaria.",
        "Expels Wind-Cold-Damp and relieves painful obstruction; reduces swelling and dispels putrefaction; strongly warms the channels." => "Expulsa Viento-Frío-Humedad y alivia obstrucción dolorosa; reduce hinchazón y dispersa putrefacción; calienta intensamente los canales.",
        "Avoid in Yin deficiency with Heat, true Heat with false Cold, or pregnancy; incompatible with Ban Xia, Bei Mu, Gua Lou, Bai Lian and Bai Ji; traditionally antagonizes Wu Gong." => "Evitar en deficiencia de Yin con Calor, Calor verdadero con Frío falso o embarazo; incompatible con Ban Xia, Bei Mu, Gua Lou, Bai Lian y Bai Ji; tradicionalmente antagoniza Wu Gong.",
        "Boil 30–60 minutes longer than other herbs." => "Hervir entre 30 y 60 minutos más que las otras hierbas.",
        "Strongly expels Wind-Cold-Damp and stops pain; more toxic than Chuan Wu and used mainly in carefully prepared or topical applications." => "Expulsa intensamente Viento-Frío-Humedad y detiene dolor; es más tóxica que Chuan Wu y se usa principalmente en preparaciones cuidadosas o aplicaciones tópicas.",
        "Avoid in Heat patterns, Yin deficiency, pregnancy or without proper preparation; shares major aconite incompatibilities." => "Evitar en patrones de Calor, deficiencia de Yin, embarazo o sin preparación adecuada; comparte las principales incompatibilidades del acónito.",
        "Restores devastated Yang, warms Fire and assists Yang, disperses Cold through the channels and relieves pain." => "Restaura Yang devastado, calienta el Fuego y ayuda al Yang, dispersa Frío por los canales y alivia dolor.",
        "Avoid in Yin deficiency with Heat, reckless movement of hot Blood or pregnancy; use only the properly prepared form." => "Evitar en deficiencia de Yin con Calor, movimiento imprudente de Sangre caliente o embarazo; usar únicamente la forma preparada correctamente.",
        "Warms the Spleen and Stomach and expels Cold; warms the Lung and transforms phlegm; the charred form warms channels and stops bleeding." => "Calienta Bazo y Estómago y expulsa Frío; calienta el Pulmón y transforma flema; la forma carbonizada calienta canales y detiene sangrado.",
        "Avoid in Yin deficiency with Heat, Interior excess Heat, reckless movement of hot Blood or pregnancy." => "Evitar en deficiencia de Yin con Calor, Calor Interior de exceso, movimiento imprudente de Sangre caliente o embarazo.",
        "Disperses deep Cold, warms the channels and relieves pain; leads Fire back to its source and encourages generation of Qi and Blood." => "Dispersa Frío profundo, calienta los canales y alivia dolor; conduce el Fuego de regreso a su origen y favorece la generación de Qi y Sangre.",
        "Avoid in Yin deficiency with Heat, Interior excess Heat, reckless movement of hot Blood or pregnancy." => "Evitar en deficiencia de Yin con Calor, Calor Interior de exceso, movimiento imprudente de Sangre caliente o embarazo.",
        "Add near the end; powder may be taken separately." => "Agregar cerca del final; el polvo puede tomarse por separado.",
        "Warms the Middle Jiao and stops pain; kills parasites and alleviates abdominal pain." => "Calienta el Jiao Medio y detiene dolor; elimina parásitos y alivia dolor abdominal.",
        "Avoid in Yin deficiency with Heat or pregnancy; traditionally antagonizes Gua Lou and counteracts Kuan Dong Hua, Fu Zi and Fang Feng." => "Evitar en deficiencia de Yin con Calor o embarazo; tradicionalmente antagoniza Gua Lou y contrarresta Kuan Dong Hua, Fu Zi y Fang Feng.",
        "Warms the Middle Jiao, stops vomiting and diarrhea, disperses Cold and relieves vertex headache; soothes Liver Qi and leads Fire downward." => "Calienta el Jiao Medio, detiene vómitos y diarrea, dispersa Frío y alivia cefalea del vértex; suaviza el Qi de Hígado y conduce el Fuego hacia abajo.",
        "Avoid in Yin deficiency with Heat; prolonged use may injure Yin; traditionally antagonizes Dan Shen and Zi Shi Ying." => "Evitar en deficiencia de Yin con Calor; el uso prolongado puede lesionar Yin; tradicionalmente antagoniza Dan Shen y Zi Shi Ying.",
        "Warms the Liver and Kidney, regulates Qi and stops Cold pain, especially in the lower abdomen." => "Calienta Hígado y Riñón, regula el Qi y detiene dolor por Frío, especialmente en el abdomen inferior.",
        "Warms the Middle Jiao and descends rebellious Stomach Qi; warms the Kidney and assists Yang." => "Calienta el Jiao Medio y hace descender el Qi rebelde de Estómago; calienta el Riñón y ayuda al Yang.",
        "Avoid in febrile disease or Yin deficiency; traditionally antagonizes Yu Jin." => "Evitar en enfermedad febril o deficiencia de Yin; tradicionalmente antagoniza Yu Jin.",
        "Warms the Middle Jiao, stops pain and directs rebellious Stomach Qi downward." => "Calienta el Jiao Medio, detiene dolor y dirige hacia abajo el Qi rebelde de Estómago.",
        "Avoid in Yin deficiency with Heat; weak patients should not use it alone and may require supporting tonics." => "Evitar en deficiencia de Yin con Calor; los pacientes débiles no deben usarla sola y pueden requerir tónicos de apoyo.",
        "Warms the Middle Jiao and stops pain; also used for headache, nasal congestion and toothache in traditional applications." => "Calienta el Jiao Medio y detiene dolor; también se usa para cefalea, congestión nasal y dolor dental en aplicaciones tradicionales.",
        "Avoid in Heat from excess or deficiency." => "Evitar en Calor por exceso o deficiencia.",
        "Warms the Middle Jiao and supports digestion; disperses Cold and relieves abdominal discomfort." => "Calienta el Jiao Medio y favorece la digestión; dispersa Frío y alivia malestar abdominal.",
        "Warm the Interior and expel Cold" => "Calentar el Interior y expulsar Frío",
        "All twelve warm the Interior, but selection depends on Cold depth, organ location, Yang collapse, pain, rebellious Qi, toxicity and constitutional strength." => "Las doce calientan el Interior, pero la selección depende de la profundidad del Frío, localización orgánica, colapso de Yang, dolor, Qi rebelde, toxicidad y fortaleza constitucional.",
        "Aconite group: Chuan Wu, Cao Wu and Fu Zi" => "Grupo de acónitos: Chuan Wu, Cao Wu y Fu Zi",
        "Chuan Wu and Cao Wu emphasize painful Wind-Cold-Damp obstruction; Cao Wu is more toxic. Fu Zi is the principal choice for restoring devastated Yang and warming all channels." => "Chuan Wu y Cao Wu enfatizan obstrucción dolorosa por Viento-Frío-Humedad; Cao Wu es más tóxica. Fu Zi es la opción principal para restaurar Yang devastado y calentar todos los canales.",
        "Restore Yang and warm the Kidney" => "Restaurar Yang y calentar el Riñón",
        "Fu Zi restores collapsed Yang; Rou Gui leads Fire back to its source; Xiao Hui Xiang and Ding Xiang warm the Kidney with more focused Qi-regulating actions." => "Fu Zi restaura Yang colapsado; Rou Gui conduce el Fuego a su origen; Xiao Hui Xiang y Ding Xiang calientan el Riñón con acciones más específicas sobre el Qi.",
        "Warm the Middle Jiao and stop pain" => "Calentar el Jiao Medio y detener dolor",
        "Gan Jiang, Hua Jiao, Wu Zhu Yu, Ding Xiang, Gao Liang Jiang, Bi Ba and Hu Jiao warm the digestive center, differing in vomiting, diarrhea, parasites, pain and digestive emphasis." => "Gan Jiang, Hua Jiao, Wu Zhu Yu, Ding Xiang, Gao Liang Jiang, Bi Ba y Hu Jiao calientan el centro digestivo y difieren en énfasis sobre vómitos, diarrea, parásitos, dolor y digestión.",
        "Descend rebellious Stomach Qi" => "Hacer descender el Qi rebelde de Estómago",
        "Wu Zhu Yu, Ding Xiang and Gao Liang Jiang warm the Middle and direct rebellious Qi downward; Wu Zhu Yu also treats vertex headache and diarrhea." => "Wu Zhu Yu, Ding Xiang y Gao Liang Jiang calientan el Jiao Medio y dirigen Qi rebelde hacia abajo; Wu Zhu Yu además trata cefalea del vértex y diarrea.",
        "Warm the Lung and transform phlegm" => "Calentar el Pulmón y transformar flema",
        "Gan Jiang uniquely emphasizes warming the Lung and transforming Cold-phlegm within this category." => "Gan Jiang enfatiza de forma distintiva calentar el Pulmón y transformar Flema-Frío dentro de esta categoría.",
        "Cold pain and Liver Qi" => "Dolor por Frío y Qi de Hígado",
        "Wu Zhu Yu and Xiao Hui Xiang warm the Liver and relieve Cold pain; Wu Zhu Yu is more descending and toxic, while Xiao Hui Xiang is gentler." => "Wu Zhu Yu y Xiao Hui Xiang calientan el Hígado y alivian dolor por Frío; Wu Zhu Yu es más descendente y tóxica, mientras Xiao Hui Xiang es más suave.",
        "Toxicity and prolonged cooking" => "Toxicidad y cocción prolongada",
        "Chuan Wu, Cao Wu and Fu Zi require correct preparation and prolonged cooking; Hua Jiao and Wu Zhu Yu also require caution because of toxicity or Yin injury." => "Chuan Wu, Cao Wu y Fu Zi requieren preparación correcta y cocción prolongada; Hua Jiao y Wu Zhu Yu también exigen precaución por toxicidad o lesión de Yin.",
        "Heat, Yin deficiency and pregnancy" => "Calor, deficiencia de Yin y embarazo",
        "Warm Interior herbs are generally inappropriate for Heat or Yin-deficiency Heat; several strongly warming herbs also require avoidance during pregnancy." => "Las hierbas que calientan el Interior generalmente no son apropiadas para Calor o Calor por deficiencia de Yin; varias hierbas fuertemente calientes también deben evitarse durante el embarazo.",
        "Hot, very toxic" => "Caliente, muy tóxica",
        "Hot, toxic" => "Caliente, tóxica",
        "Hot" => "Caliente",
        "Hot, slightly toxic" => "Caliente, ligeramente tóxica",
        "22. Qi Tonics" => "22. Tónicos de Qi",
        "Board Guideline 2026 · 11 herbs · Common foundation: tonify the Spleen, augment Qi, generate fluids and support transformation and transportation." => "Guía Board 2026 · 11 sustancias · Fundamento común: tonificar el Bazo, aumentar el Qi, generar líquidos y apoyar la transformación y el transporte.",
        "degree and location of Qi deficiency, Spleen Dampness, Exterior instability, Lung or Kidney involvement, fluid deficiency, formula harmonization and food stagnation." => "grado y localización de la deficiencia de Qi, Humedad de Bazo, inestabilidad del Exterior, participación de Pulmón o Riñón, deficiencia de líquidos, armonización de fórmulas y estancamiento de alimentos.",
        "Strongly tonifies Yuan Qi; generates fluids and stops thirst; benefits Heart Qi and calms the Spirit." => "Tonifica intensamente el Yuan Qi; genera líquidos y detiene la sed; beneficia el Qi de Corazón y calma el Shen.",
        "Avoid in Yin deficiency with Heat, excess Heat without Qi deficiency, Liver Yang rising or hypertension; overuse may cause headache. Incompatible with Li Lu and traditionally antagonized by Wu Ling Zhi." => "Evitar en deficiencia de Yin con Calor, Calor de exceso sin deficiencia de Qi, ascenso de Yang de Hígado o hipertensión; el uso excesivo puede causar cefalea. Incompatible con Li Lu y tradicionalmente antagonizada por Wu Ling Zhi.",
        "Cook separately." => "Cocinar por separado.",
        "Tonifies Spleen and Lung Qi for both Exterior and Interior deficiency; a gentler substitute for Ren Shen requiring a larger dose." => "Tonifica el Qi de Bazo y Pulmón para deficiencia tanto Exterior como Interior; es un sustituto más suave de Ren Shen que requiere una dosis mayor.",
        "Traditionally incompatible with Li Lu." => "Tradicionalmente incompatible con Li Lu.",
        "Tonifies Qi and Blood, raises Yang, strengthens defensive Qi, stabilizes the Exterior, promotes urination and supports discharge of pus and tissue regeneration." => "Tonifica Qi y Sangre, eleva Yang, fortalece el Qi defensivo, estabiliza el Exterior, promueve la micción y favorece la expulsión de pus y regeneración de tejido.",
        "Avoid in Yin deficiency with Heat, Exterior excess, Qi or food stagnation, Damp obstruction, or early skin lesions with active Heat." => "Evitar en deficiencia de Yin con Calor, exceso Exterior, estancamiento de Qi o alimentos, obstrucción por Humedad o lesiones cutáneas tempranas con Calor activo.",
        "Strengthens the Spleen, dries Dampness and calms the fetus in Spleen-deficiency patterns." => "Fortalece el Bazo, seca Humedad y calma el feto en patrones de deficiencia de Bazo.",
        "Avoid in Yin deficiency with Heat or injured fluids." => "Evitar en deficiencia de Yin con Calor o líquidos lesionados.",
        "Strengthens the Spleen, transforms Dampness, clears Summer-Heat and treats food poisoning." => "Fortalece el Bazo, transforma Humedad, elimina Calor de Verano y trata intoxicación alimentaria.",
        "Use cautiously in intermittent fever and chills from Cold disorders." => "Usar con precaución en fiebre y escalofríos intermitentes por trastornos de Frío.",
        "Tonifies Spleen, Lung and Kidney; nourishes Lung Yin, stabilizes Kidney Essence and generates fluids." => "Tonifica Bazo, Pulmón y Riñón; nutre Yin de Pulmón, estabiliza la Esencia de Riñón y genera líquidos.",
        "Avoid in excess patterns, especially fullness from Dampness, stagnation or accumulation; traditionally antagonized by Gan Sui." => "Evitar en patrones de exceso, especialmente plenitud por Humedad, estancamiento o acumulación; tradicionalmente antagonizada por Gan Sui.",
        "Tonifies the Spleen, nourishes Blood, calms the Spirit and moderates or harmonizes the actions of other herbs." => "Tonifica el Bazo, nutre la Sangre, calma el Shen y modera o armoniza las acciones de otras hierbas.",
        "Avoid in excess Dampness, food stagnation, intestinal parasites or Damp-phlegm disorders." => "Evitar en Humedad de exceso, estancamiento de alimentos, parásitos intestinales o trastornos de Flema-Humedad.",
        "Tonifies Qi, moderates spasms and pain, clears Fire toxicity and harmonizes other herbs; prepared Zhi Gan Cao is warmer and more tonifying." => "Tonifica el Qi, modera espasmos y dolor, elimina toxicidad de Fuego y armoniza otras hierbas; Zhi Gan Cao preparada es más tibia y tonificante.",
        "Avoid in excess Dampness, nausea or vomiting; incompatible with Da Ji, Hai Zao, Gan Sui and Yuan Hua; traditionally antagonizes Yuan Zhi." => "Evitar en Humedad de exceso, náuseas o vómitos; incompatible con Da Ji, Hai Zao, Gan Sui y Yuan Hua; tradicionalmente antagoniza Yuan Zhi.",
        "Tonifies the Middle Jiao and relieves abdominal pain from deficiency Cold; moistens the Lung and stops cough." => "Tonifica el Jiao Medio y alivia dolor abdominal por Frío de deficiencia; humedece el Pulmón y detiene la tos.",
        "Avoid in internal Damp-Heat, Middle Jiao fullness with vomiting, phlegm-Heat cough or food stagnation." => "Evitar en Humedad-Calor interna, plenitud del Jiao Medio con vómitos, tos por Flema-Calor o estancamiento de alimentos.",
        "Dissolve in the strained decoction." => "Disolver en la decocción colada.",
        "Tonifies the Middle Jiao, relieves pain, moistens the Lung and lubricates the Intestines." => "Tonifica el Jiao Medio, alivia dolor, humedece el Pulmón y lubrica los Intestinos.",
        "Avoid in excess Dampness, phlegm-Heat, Middle Jiao fullness with vomiting or loose stools." => "Evitar en Humedad de exceso, Flema-Calor, plenitud del Jiao Medio con vómitos o heces sueltas.",
        "Strengthens the Spleen, transforms Dampness, stops thirst, generates fluids and alleviates diarrhea or dysentery." => "Fortalece el Bazo, transforma Humedad, detiene la sed, genera líquidos y alivia diarrea o disentería.",
        "No specific caution noted." => "No se señala una precaución específica.",
        "Tonify the Spleen and augment Qi" => "Tonificar el Bazo y aumentar el Qi",
        "All eleven support Qi or Spleen function, but differ in strength, organ focus, fluids, Dampness, Exterior stability and food-like nourishing qualities." => "Las once apoyan el Qi o la función de Bazo, pero difieren en intensidad, enfoque de órgano, líquidos, Humedad, estabilidad del Exterior y cualidades nutritivas alimentarias.",
        "Ren Shen and Dang Shen" => "Ren Shen y Dang Shen",
        "Ren Shen strongly restores Yuan Qi and is appropriate for severe deficiency; Dang Shen more gently tonifies Spleen and Lung Qi and is usually given at a larger dose." => "Ren Shen restaura intensamente el Yuan Qi y es apropiada para deficiencia grave; Dang Shen tonifica más suavemente el Qi de Bazo y Pulmón y suele administrarse en dosis mayor.",
        "Stabilize the Exterior and stop sweating" => "Estabilizar el Exterior y detener sudoración",
        "Huang Qi strengthens defensive Qi and raises Yang; Bai Zhu strengthens the Spleen and dries Dampness. Together they support Qi deficiency with spontaneous sweating." => "Huang Qi fortalece el Qi defensivo y eleva Yang; Bai Zhu fortalece el Bazo y seca Humedad. Juntas apoyan deficiencia de Qi con sudoración espontánea.",
        "Strengthen the Spleen and transform Dampness" => "Fortalecer el Bazo y transformar Humedad",
        "Bai Zhu is stronger and drying; Bai Bian Dou is gentle and clears Summer-Heat; Geng Mi is food-like and generates fluids." => "Bai Zhu es más fuerte y secante; Bai Bian Dou es suave y elimina Calor de Verano; Geng Mi es alimentaria y genera líquidos.",
        "Tonify Lung and Kidney while securing fluids" => "Tonificar Pulmón y Riñón mientras se conservan líquidos",
        "Shan Yao uniquely tonifies Spleen, Lung and Kidney while nourishing Lung Yin and stabilizing Kidney Essence." => "Shan Yao se distingue por tonificar Bazo, Pulmón y Riñón mientras nutre Yin de Pulmón y estabiliza Esencia de Riñón.",
        "Moderate and harmonize formulas" => "Moderar y armonizar fórmulas",
        "Da Zao nourishes Blood and calms the Spirit; Gan Cao moderates spasms, toxicity and the actions of other herbs." => "Da Zao nutre la Sangre y calma el Shen; Gan Cao modera espasmos, toxicidad y las acciones de otras hierbas.",
        "Tonify the Middle Jiao and relieve pain" => "Tonificar el Jiao Medio y aliviar dolor",
        "Yi Tang, Feng Mi and Gan Cao relieve deficiency-type abdominal pain; Yi Tang is warmer, Feng Mi moistens the Intestines, and Gan Cao moderates spasm." => "Yi Tang, Feng Mi y Gan Cao alivian dolor abdominal por deficiencia; Yi Tang es más tibia, Feng Mi humedece los Intestinos y Gan Cao modera espasmos.",
        "Food-like Qi tonics" => "Tónicos de Qi de naturaleza alimentaria",
        "Da Zao, Yi Tang, Feng Mi and Geng Mi nourish gently, but can aggravate Dampness, fullness or food stagnation when used inappropriately." => "Da Zao, Yi Tang, Feng Mi y Geng Mi nutren suavemente, pero pueden agravar Humedad, plenitud o estancamiento de alimentos si se usan de forma inapropiada.",
        "Pure deficiency versus excess" => "Deficiencia pura frente a exceso",
        "Qi tonics are inappropriate when excess pathogens, Dampness, phlegm or food stagnation predominate without true deficiency." => "Los tónicos de Qi no son apropiados cuando predominan patógenos de exceso, Humedad, flema o estancamiento de alimentos sin deficiencia verdadera.",
        "23. Blood Tonics" => "23. Tónicos de Sangre",
        "Board Guideline 2026 · 7 herbs · Common foundation: nourish Blood, support menstruation, enrich Yin and restore deficient tissues and fluids." => "Guía Board 2026 · 7 hierbas · Fundamento común: nutrir la Sangre, apoyar la menstruación, enriquecer Yin y restaurar tejidos y líquidos deficientes.",
        "Blood versus Yin deficiency, menstruation, bleeding, dryness, bowel function, Liver and Kidney involvement, Shen disturbance, Dampness and digestive tolerance." => "deficiencia de Sangre frente a Yin, menstruación, sangrado, sequedad, función intestinal, participación de Hígado y Riñón, alteración del Shen, Humedad y tolerancia digestiva.",
        "Nourishes Blood and Yin, tonifies Essence and Marrow, and treats wasting and thirsting from deficiency." => "Nutre Sangre y Yin, tonifica Esencia y Médula y trata desgaste y sed por deficiencia.",
        "Use cautiously in Spleen and Stomach deficiency, Qi or phlegm stagnation; its cloying nature may cause abdominal distension or loose stools." => "Usar con precaución en deficiencia de Bazo y Estómago o estancamiento de Qi o flema; su naturaleza empalagosa puede causar distensión abdominal o heces sueltas.",
        "Nourishes, invigorates and harmonizes Blood; regulates menstruation, disperses Cold, moistens the Intestines, reduces swelling and promotes tissue healing." => "Nutre, vigoriza y armoniza la Sangre; regula la menstruación, dispersa Frío, humedece los Intestinos, reduce hinchazón y promueve cicatrización de tejidos.",
        "Avoid in Yin deficiency with Heat; use cautiously in diarrhea or abdominal distension from Dampness." => "Evitar en deficiencia de Yin con Calor; usar con precaución en diarrea o distensión abdominal por Humedad.",
        "Nourishes Blood, preserves Yin, softens the Liver, calms Liver Yang and relieves pain; harmonizes the nutritive and protective levels." => "Nutre la Sangre, preserva Yin, suaviza el Hígado, calma Yang de Hígado y alivia dolor; armoniza los niveles Nutritivo y Protector.",
        "Use cautiously in cold-deficiency diarrhea or Yang-deficient Cold; incompatible with Li Lu and traditionally antagonized by Shi Hu and Mang Xiao." => "Usar con precaución en diarrea por Frío de deficiencia o Frío por deficiencia de Yang; incompatible con Li Lu y tradicionalmente antagonizada por Shi Hu y Mang Xiao.",
        "Nourishes Blood and stops bleeding; nourishes and moistens Yin, especially for dry Lung cough from Yin deficiency or consumption." => "Nutre la Sangre y detiene el sangrado; nutre y humedece Yin, especialmente para tos seca de Pulmón por deficiencia de Yin o consunción.",
        "Avoid in Exterior conditions; use cautiously in Spleen and Stomach deficiency; traditionally counteracted by Da Huang." => "Evitar en condiciones Exteriores; usar con precaución en deficiencia de Bazo y Estómago; tradicionalmente contrarrestada por Da Huang.",
        "Tonifies Liver and Kidney, benefits Essence and vision, enriches Yin, moistens the Lung and mildly supports Kidney Yang." => "Tonifica Hígado y Riñón, beneficia Esencia y visión, enriquece Yin, humedece el Pulmón y apoya suavemente Yang de Riñón.",
        "Avoid in externally contracted excess Heat or Spleen deficiency with Dampness and loose stools." => "Evitar en Calor de exceso contraído externamente o deficiencia de Bazo con Humedad y heces sueltas.",
        "Nourishes Yin, generates fluids and moistens the Intestines." => "Nutre Yin, genera líquidos y humedece los Intestinos.",
        "Avoid in diarrhea from Spleen deficiency." => "Evitar en diarrea por deficiencia de Bazo.",
        "Tonifies Heart and Spleen, nourishes Blood and calms the Spirit." => "Tonifica Corazón y Bazo, nutre la Sangre y calma el Shen.",
        "Avoid in phlegm-Fire or Dampness in the Middle Jiao." => "Evitar en Flema-Fuego o Humedad en el Jiao Medio.",
        "Nourish Blood and restore deficiency" => "Nutrir la Sangre y restaurar deficiencia",
        "All seven nourish Blood or related Yin and fluids, but differ in menstruation, bleeding, dryness, bowel, Liver-Kidney and Shen emphasis." => "Las siete nutren Sangre o Yin y líquidos relacionados, pero difieren en énfasis sobre menstruación, sangrado, sequedad, intestinos, Hígado-Riñón y Shen.",
        "Regulate menstruation" => "Regular la menstruación",
        "Dang Gui nourishes and moves Blood while dispersing Cold; Bai Shao nourishes Blood, preserves Yin and softens the Liver." => "Dang Gui nutre y mueve la Sangre mientras dispersa Frío; Bai Shao nutre Sangre, preserva Yin y suaviza el Hígado.",
        "Nourish Blood and Yin" => "Nutrir Sangre y Yin",
        "Shu Di Huang, E Jiao, Gou Qi Zi and Sang Shen all nourish deficient Blood or Yin; select according to Essence, bleeding, Lung dryness, fluids and bowel dryness." => "Shu Di Huang, E Jiao, Gou Qi Zi y Sang Shen nutren Sangre o Yin deficientes; seleccionar según Esencia, sangrado, sequedad de Pulmón, líquidos y sequedad intestinal.",
        "Dang Gui and Sang Shen relieve dry stool through moistening; Dang Gui also regulates Blood, while Sang Shen more directly generates fluids." => "Dang Gui y Sang Shen alivian heces secas mediante humectación; Dang Gui además regula la Sangre, mientras Sang Shen genera líquidos más directamente.",
        "Tonify Liver and Kidney" => "Tonificar Hígado y Riñón",
        "Shu Di Huang strongly nourishes Blood, Yin and Essence; Gou Qi Zi is gentler and also benefits the eyes and Lung." => "Shu Di Huang nutre intensamente Sangre, Yin y Esencia; Gou Qi Zi es más suave y además beneficia ojos y Pulmón.",
        "Stop bleeding while nourishing" => "Detener el sangrado mientras se nutre",
        "E Jiao is the distinctive Blood tonic that also stops bleeding and moistens Lung Yin." => "E Jiao es el tónico de Sangre distintivo que además detiene sangrado y humedece Yin de Pulmón.",
        "Calm the Spirit" => "Calmar el Shen",
        "Long Yan Rou tonifies Heart and Spleen, nourishes Blood and calms the Spirit, especially when deficiency affects sleep or emotional stability." => "Long Yan Rou tonifica Corazón y Bazo, nutre Sangre y calma el Shen, especialmente cuando la deficiencia afecta sueño o estabilidad emocional.",
        "Cloying tonics and digestive weakness" => "Tónicos empalagosos y debilidad digestiva",
        "Shu Di Huang and E Jiao are rich and cloying; Long Yan Rou and other sweet tonics can aggravate Dampness or phlegm when digestion is weak." => "Shu Di Huang y E Jiao son densas y empalagosas; Long Yan Rou y otros tónicos dulces pueden agravar Humedad o flema cuando la digestión es débil.",
        "24. Yang Tonics" => "24. Tónicos de Yang",
        "Board Guideline 2026 · 14 herbs · Common foundation: tonify Kidney Yang, secure Essence, strengthen sinews and bones, and support reproductive and urinary function." => "Guía Board 2026 · 14 hierbas · Fundamento común: tonificar Yang de Riñón, asegurar Esencia, fortalecer tendones y huesos y apoyar la función reproductiva y urinaria.",
        "degree of Yang deficiency, Essence or urine leakage, infertility, bowel dryness, Wind-Damp pain, bone injury, diarrhea, fetal stability, Heat signs and toxicity." => "grado de deficiencia de Yang, pérdida de Esencia u orina, infertilidad, sequedad intestinal, dolor por Viento-Humedad, lesión ósea, diarrea, estabilidad fetal, signos de Calor y toxicidad.",
        "Strongly tonifies Kidney Yang, Essence and Blood; strengthens the Du, Chong and Ren vessels and fortifies sinews and bones." => "Tonifica intensamente Yang de Riñón, Esencia y Sangre; fortalece los vasos Du, Chong y Ren y robustece tendones y huesos.",
        "Avoid in Yin deficiency with Heat, Lung phlegm-Heat, blazing Stomach Fire, warm febrile disease or Blood Heat." => "Evitar en deficiencia de Yin con Calor, Flema-Calor de Pulmón, Fuego intenso de Estómago, enfermedad febril tibia o Calor de Sangre.",
        "Tonifies Kidney Yang and Essence, moistens the Intestines and treats infertility or cold womb from deficiency." => "Tonifica Yang de Riñón y Esencia, humedece los Intestinos y trata infertilidad o útero frío por deficiencia.",
        "Avoid in Yin deficiency with Heat or diarrhea from Spleen and Stomach deficiency." => "Evitar en deficiencia de Yin con Calor o diarrea por deficiencia de Bazo y Estómago.",
        "Tonifies Kidney Yang and Essence, moistens the Intestines and benefits weakness or atrophy from Liver and Kidney deficiency." => "Tonifica Yang de Riñón y Esencia, humedece los Intestinos y beneficia debilidad o atrofia por deficiencia de Hígado y Riñón.",
        "Avoid in Kidney deficiency with Heat, diarrhea from Spleen deficiency or constipation from excess Heat." => "Evitar en deficiencia de Riñón con Calor, diarrea por deficiencia de Bazo o estreñimiento por Calor de exceso.",
        "Tonifies Kidney Yang, strengthens the low back and knees, warms the Lung and helps the Kidney grasp Qi; moistens the Intestines." => "Tonifica Yang de Riñón, fortalece zona lumbar y rodillas, calienta el Pulmón y ayuda al Riñón a captar el Qi; humedece los Intestinos.",
        "Avoid in phlegm-Fire cough, Yin deficiency with Heat or watery stools." => "Evitar en tos por Flema-Fuego, deficiencia de Yin con Calor o heces acuosas.",
        "Tonifies Kidney Yang and libido; expels Wind-Cold-Damp and relieves painful obstruction." => "Tonifica Yang de Riñón y la libido; expulsa Viento-Frío-Humedad y alivia obstrucción dolorosa.",
        "Avoid in Yin deficiency with Heat; prolonged use may injure Yin and cause dizziness, vomiting, dry mouth, thirst or nosebleed." => "Evitar en deficiencia de Yin con Calor; el uso prolongado puede lesionar Yin y causar mareos, vómitos, boca seca, sed o epistaxis.",
        "Strongly warms Kidney Yang, treats infertility from Cold and expels Wind-Cold-Damp painful obstruction." => "Calienta intensamente Yang de Riñón, trata infertilidad por Frío y expulsa obstrucción dolorosa por Viento-Frío-Humedad.",
        "Avoid in Yin deficiency with Heat; prolonged use is not recommended because toxic reactions may occur." => "Evitar en deficiencia de Yin con Calor; no se recomienda uso prolongado porque pueden aparecer reacciones tóxicas.",
        "Tonifies Kidney Yang, strengthens sinews and bones, expels Wind-Damp and supports Spleen-Kidney Yang diarrhea." => "Tonifica Yang de Riñón, fortalece tendones y huesos, expulsa Viento-Humedad y apoya diarrea por deficiencia de Yang de Bazo y Riñón.",
        "Avoid in Yin deficiency with Heat or Damp-Heat with difficult urination; traditionally antagonizes Dan Shen." => "Evitar en deficiencia de Yin con Calor o Humedad-Calor con dificultad urinaria; tradicionalmente antagoniza Dan Shen.",
        "Warms Spleen and Kidney Yang, stops diarrhea, stabilizes Essence and helps the Kidney grasp Qi; used topically for vitiligo." => "Calienta Yang de Bazo y Riñón, detiene diarrea, estabiliza Esencia y ayuda al Riñón a captar el Qi; se usa tópicamente para vitíligo.",
        "Avoid in Yin deficiency with Heat or constipation; its harshness may injure deficient patients; traditionally antagonizes Gan Cao." => "Evitar en deficiencia de Yin con Calor o estreñimiento; su aspereza puede lesionar pacientes deficientes; tradicionalmente antagoniza Gan Cao.",
        "Warms and secures Kidney and Spleen, retains urine and Essence, and controls excessive salivation." => "Calienta y asegura Riñón y Bazo, retiene orina y Esencia y controla salivación excesiva.",
        "Avoid in frequent urination or vaginal discharge caused by Heat." => "Evitar en micción frecuente o flujo vaginal causado por Calor.",
        "Tonifies Liver and Kidney, strengthens sinews and bones, and expels Wind-Damp painful obstruction." => "Tonifica Hígado y Riñón, fortalece tendones y huesos y expulsa obstrucción dolorosa por Viento-Humedad.",
        "Avoid in difficult urination or Heat from Yin deficiency; traditionally antagonizes Bai Lian and Gan Cao." => "Evitar en dificultad urinaria o Calor por deficiencia de Yin; tradicionalmente antagoniza Bai Lian y Gan Cao.",
        "Tonifies Kidney, mends sinews and bones, invigorates Blood after trauma and may stimulate hair growth topically." => "Tonifica Riñón, repara tendones y huesos, vigoriza Sangre después de trauma y puede estimular crecimiento del cabello tópicamente.",
        "Avoid in Yin deficiency without Blood stasis." => "Evitar en deficiencia de Yin sin estasis de Sangre.",
        "Tonifies Liver and Kidney, strengthens sinews and bones, invigorates Blood, relieves trauma pain and calms the fetus." => "Tonifica Hígado y Riñón, fortalece tendones y huesos, vigoriza Sangre, alivia dolor traumático y calma el feto.",
        "Traditionally antagonized by Lei Wan." => "Tradicionalmente antagonizada por Lei Wan.",
        "Tonifies Liver and Kidney, strengthens sinews and bones, supports smooth Qi and Blood flow, calms the fetus and is traditionally used in hypertension patterns." => "Tonifica Hígado y Riñón, fortalece tendones y huesos, favorece flujo suave de Qi y Sangre, calma el feto y se usa tradicionalmente en patrones de hipertensión.",
        "Avoid in Yin deficiency with Heat; traditionally antagonizes Xuan Shen." => "Evitar en deficiencia de Yin con Calor; tradicionalmente antagoniza Xuan Shen.",
        "Tonifies Liver and Kidney, secures Essence and urine, improves vision, stops diarrhea and calms the fetus." => "Tonifica Hígado y Riñón, asegura Esencia y orina, mejora visión, detiene diarrea y calma el feto.",
        "Avoid in Yin deficiency with Heat, constipation, or scanty dark urine." => "Evitar en deficiencia de Yin con Calor, estreñimiento u orina escasa y oscura.",
        "Tonify Kidney Yang and secure Essence" => "Tonificar Yang de Riñón y asegurar Esencia",
        "All fourteen support Kidney Yang or related functions, but differ in potency, Essence, urine, bowel, pain, bone, diarrhea and reproductive emphasis." => "Las catorce apoyan Yang de Riñón o funciones relacionadas, pero difieren en potencia, Esencia, orina, intestinos, dolor, huesos, diarrea y énfasis reproductivo.",
        "Augment Blood and Essence" => "Aumentar Sangre y Esencia",
        "Lu Rong is the strongest, animal-derived tonic for Yang, Essence and Blood; Rou Cong Rong is gentler and moistens the Intestines." => "Lu Rong es el tónico de origen animal más fuerte para Yang, Esencia y Sangre; Rou Cong Rong es más suave y humedece los Intestinos.",
        "Rou Cong Rong, Suo Yang and Hu Tao Ren tonify Yang while relieving dry constipation; Hu Tao Ren also helps the Kidney grasp Lung Qi." => "Rou Cong Rong, Suo Yang y Hu Tao Ren tonifican Yang mientras alivian estreñimiento seco; Hu Tao Ren además ayuda al Riñón a captar el Qi de Pulmón.",
        "Expel Wind-Damp and relieve pain" => "Expulsar Viento-Humedad y aliviar dolor",
        "Yin Yang Huo, Xian Mao, Ba Ji Tian and Gou Ji combine Yang or Liver-Kidney tonification with Wind-Damp painful obstruction treatment." => "Yin Yang Huo, Xian Mao, Ba Ji Tian y Gou Ji combinan tonificación de Yang o Hígado-Riñón con tratamiento de obstrucción dolorosa por Viento-Humedad.",
        "Warm Spleen and Kidney and stop diarrhea" => "Calentar Bazo y Riñón y detener diarrea",
        "Ba Ji Tian, Bu Gu Zhi and Tu Si Zi support Spleen-Kidney deficiency; Bu Gu Zhi is warmer and Tu Si Zi is neutral and securing." => "Ba Ji Tian, Bu Gu Zhi y Tu Si Zi apoyan deficiencia de Bazo-Riñón; Bu Gu Zhi es más caliente y Tu Si Zi es neutra y astringente.",
        "Secure Essence and urine" => "Asegurar Esencia y orina",
        "Bu Gu Zhi, Yi Zhi Ren and Tu Si Zi retain Essence or urine; Yi Zhi Ren also controls salivation and Tu Si Zi improves vision." => "Bu Gu Zhi, Yi Zhi Ren y Tu Si Zi retienen Esencia u orina; Yi Zhi Ren además controla salivación y Tu Si Zi mejora visión.",
        "Strengthen bones and repair trauma" => "Fortalecer huesos y reparar trauma",
        "Ba Ji Tian and Gou Ji strengthen sinews and bones; Gu Sui Bu and Xu Duan emphasize traumatic injury and bone or tendon repair; Du Zhong supports chronic weakness." => "Ba Ji Tian y Gou Ji fortalecen tendones y huesos; Gu Sui Bu y Xu Duan enfatizan lesión traumática y reparación ósea o tendinosa; Du Zhong apoya debilidad crónica.",
        "Calm the fetus" => "Calmar el feto",
        "Xu Duan, Du Zhong and Tu Si Zi tonify Liver and Kidney while helping stabilize pregnancy." => "Xu Duan, Du Zhong y Tu Si Zi tonifican Hígado y Riñón mientras ayudan a estabilizar el embarazo.",
        "Strong heat and toxicity" => "Calor intenso y toxicidad",
        "Lu Rong is very potent; Xian Mao is hot and toxic; Yin Yang Huo and Bu Gu Zhi are warming and may injure Yin when Heat signs are present." => "Lu Rong es muy potente; Xian Mao es caliente y tóxica; Yin Yang Huo y Bu Gu Zhi son tibias y pueden lesionar Yin cuando existen signos de Calor.",
        "Very warm" => "Muy tibia",
        "Bitter, sweet, acrid" => "Amarga, dulce, picante",
        "Sweet, slightly acrid" => "Dulce, ligeramente picante",
        "25. Yin Tonics" => "25. Tónicos de Yin",
        "Board Guideline 2026 · 9 herbs · Common foundation: nourish Yin, generate fluids, moisten dryness, clear deficiency Heat and anchor Yang." => "Guía Board 2026 · 9 sustancias · Fundamento común: nutrir Yin, generar líquidos, humedecer sequedad, eliminar Calor por deficiencia y anclar Yang.",
        "Lung, Stomach, Liver or Kidney Yin deficiency, dry cough, thirst, bowel dryness, vision, premature greying, floating Yang, masses and digestive weakness." => "deficiencia de Yin de Pulmón, Estómago, Hígado o Riñón, tos seca, sed, sequedad intestinal, visión, canas prematuras, Yang flotante, masas y debilidad digestiva.",
        "Nourishes Lung Yin, moistens dryness, clears Heart Heat and calms the Spirit; used for Lily Bulb disorder." => "Nutre Yin de Pulmón, humedece sequedad, elimina Calor de Corazón y calma el Shen; se usa para el trastorno de Bulbo de Lirio.",
        "Avoid in Wind-Cold cough, phlegm cough or diarrhea from Spleen and Stomach deficiency." => "Evitar en tos por Viento-Frío, tos con flema o diarrea por deficiencia de Bazo y Estómago.",
        "Strongly nourishes Lung and Kidney Yin, clears Heat, generates fluids and moistens the Intestines." => "Nutre intensamente Yin de Pulmón y Riñón, elimina Calor, genera líquidos y humedece los Intestinos.",
        "Avoid in low appetite from Spleen and Stomach deficiency with Cold or Wind-Cold cough." => "Evitar en poco apetito por deficiencia fría de Bazo y Estómago o tos por Viento-Frío.",
        "Nourishes Lung and Stomach Yin, generates fluids, moistens the Intestines, clears Heart Heat and relieves irritability." => "Nutre Yin de Pulmón y Estómago, genera líquidos, humedece los Intestinos, elimina Calor de Corazón y alivia irritabilidad.",
        "Avoid in cold-deficiency diarrhea or congested fluids; traditionally antagonized by Kuan Dong Hua." => "Evitar en diarrea por Frío de deficiencia o líquidos congestionados; tradicionalmente antagonizada por Kuan Dong Hua.",
        "Nourishes Lung and Stomach Yin, clears Lung Heat, generates fluids and moistens dryness or dry itchy skin." => "Nutre Yin de Pulmón y Estómago, elimina Calor de Pulmón, genera líquidos y humedece sequedad o piel seca con picazón.",
        "Avoid in Wind-Cold cough or Spleen deficiency with Cold; traditionally incompatible with Li Lu and antagonized by Fang Ji." => "Evitar en tos por Viento-Frío o deficiencia de Bazo con Frío; tradicionalmente incompatible con Li Lu y antagonizada por Fang Ji.",
        "Nourishes Stomach and Kidney Yin, clears deficiency Heat, generates fluids, brightens vision and strengthens Essence." => "Nutre Yin de Estómago y Riñón, elimina Calor por deficiencia, genera líquidos, mejora la visión y fortalece Esencia.",
        "Avoid in deficiency without Heat, excess Heat patterns, abdominal distension with thick greasy coating or early warm-febrile disease; traditionally antagonized by Ba Dou." => "Evitar en deficiencia sin Calor, patrones de Calor de exceso, distensión abdominal con saburra gruesa y grasosa o etapa temprana de enfermedad febril tibia; tradicionalmente antagonizada por Ba Dou.",
        "Nourishes Liver and Kidney Yin, clears deficiency Heat, improves vision and treats premature greying of hair." => "Nutre Yin de Hígado y Riñón, elimina Calor por deficiencia, mejora la visión y trata canas prematuras.",
        "Avoid in Yang deficiency with diarrhea from Spleen deficiency." => "Evitar en deficiencia de Yang con diarrea por deficiencia de Bazo.",
        "Nourishes Liver and Kidney, enriches Blood, extinguishes Wind, moistens the Intestines and supports recovery or lactation." => "Nutre Hígado y Riñón, enriquece la Sangre, extingue Viento, humedece los Intestinos y apoya recuperación o lactancia.",
        "Avoid in diarrhea." => "Evitar en diarrea.",
        "Nourishes Liver and Kidney Yin, anchors Yang, strengthens bones, nourishes Blood and stabilizes the Chong and Ren vessels." => "Nutre Yin de Hígado y Riñón, ancla Yang, fortalece huesos, nutre Sangre y estabiliza los vasos Chong y Ren.",
        "Avoid during pregnancy or in Stomach Cold-Damp, Yang deficiency or diarrhea; traditionally antagonized by Sha Shen and Ren Shen." => "Evitar durante el embarazo o en Frío-Humedad de Estómago, deficiencia de Yang o diarrea; tradicionalmente antagonizada por Sha Shen y Ren Shen.",
        "Crush and cook 30 minutes longer." => "Triturar y cocinar 30 minutos más.",
        "Nourishes Yin and anchors Yang; invigorates Blood, promotes menstruation and dissipates nodules or palpable masses." => "Nutre Yin y ancla Yang; vigoriza Sangre, promueve menstruación y disipa nódulos o masas palpables.",
        "Avoid during pregnancy, poor appetite or diarrhea from Spleen deficiency, unresolved Exterior conditions or impotence; traditionally antagonized by Ming Fan." => "Evitar durante el embarazo, poco apetito o diarrea por deficiencia de Bazo, condiciones Exteriores sin resolver o impotencia; tradicionalmente antagonizada por Ming Fan.",
        "Nourish Yin and generate fluids" => "Nutrir Yin y generar líquidos",
        "All nine nourish Yin or related fluids, but differ in Lung, Stomach, Liver, Kidney, bowel, vision, Yang anchoring and mass-dissipating actions." => "Las nueve nutren Yin o líquidos relacionados, pero difieren en acciones sobre Pulmón, Estómago, Hígado, Riñón, intestinos, visión, anclaje de Yang y disipación de masas.",
        "Nourish Lung Yin and moisten dry cough" => "Nutrir Yin de Pulmón y humedecer tos seca",
        "Bai He, Tian Men Dong, Mai Men Dong and Sha Shen treat Lung Yin dryness; Bai He calms the Spirit, Tian Men Dong is very cold, Mai Men Dong clears Heart irritability and Sha Shen also nourishes Stomach Yin." => "Bai He, Tian Men Dong, Mai Men Dong y Sha Shen tratan sequedad por deficiencia de Yin de Pulmón; Bai He calma el Shen, Tian Men Dong es muy fría, Mai Men Dong elimina irritabilidad de Corazón y Sha Shen además nutre Yin de Estómago.",
        "Tian Men Dong and Mai Men Dong" => "Tian Men Dong y Mai Men Dong",
        "Tian Men Dong is colder and more strongly nourishes Kidney Yin; Mai Men Dong is gentler, emphasizes Stomach fluids and clears Heart irritability." => "Tian Men Dong es más fría y nutre con más intensidad Yin de Riñón; Mai Men Dong es más suave, enfatiza líquidos de Estómago y elimina irritabilidad de Corazón.",
        "Nourish Stomach Yin and generate fluids" => "Nutrir Yin de Estómago y generar líquidos",
        "Mai Men Dong, Sha Shen and Shi Hu address thirst and Stomach Yin deficiency; Shi Hu also strengthens Kidney Essence and brightens vision." => "Mai Men Dong, Sha Shen y Shi Hu tratan sed y deficiencia de Yin de Estómago; Shi Hu además fortalece Esencia de Riñón y mejora visión.",
        "Liver-Kidney Yin, vision and premature greying" => "Yin de Hígado-Riñón, visión y canas prematuras",
        "Nu Zhen Zi and Hei Zhi Ma nourish Liver and Kidney; Nu Zhen Zi clears deficiency Heat and Hei Zhi Ma more strongly nourishes Blood and moistens the Intestines." => "Nu Zhen Zi y Hei Zhi Ma nutren Hígado y Riñón; Nu Zhen Zi elimina Calor por deficiencia y Hei Zhi Ma nutre con más intensidad la Sangre y humedece los Intestinos.",
        "Tian Men Dong, Mai Men Dong and Hei Zhi Ma relieve bowel dryness while nourishing Yin; avoid them when diarrhea or weak digestion predominates." => "Tian Men Dong, Mai Men Dong y Hei Zhi Ma alivian sequedad intestinal mientras nutren Yin; evitarlas cuando predominan diarrea o digestión débil.",
        "Gui Ban and Bie Jia: anchor Yang" => "Gui Ban y Bie Jia: anclar Yang",
        "Gui Ban emphasizes the ventral aspect, bones, Blood and Chong-Ren deficiency; Bie Jia emphasizes the dorsal aspect, Blood movement and palpable masses." => "Gui Ban enfatiza el aspecto ventral, huesos, Sangre y deficiencia de Chong-Ren; Bie Jia enfatiza el aspecto dorsal, movimiento de Sangre y masas palpables.",
        "Calm the Spirit and clear Heart Heat" => "Calmar el Shen y eliminar Calor de Corazón",
        "Bai He and Mai Men Dong nourish Yin while addressing Heart Heat, irritability or emotional disturbance." => "Bai He y Mai Men Dong nutren Yin mientras abordan Calor de Corazón, irritabilidad o alteración emocional.",
        "Cold and cloying herbs with weak digestion" => "Hierbas frías y empalagosas con digestión débil",
        "Many Yin tonics are cold or rich and can worsen Spleen deficiency, Dampness, phlegm or diarrhea; digestive tolerance guides selection." => "Muchos tónicos de Yin son fríos o densos y pueden empeorar deficiencia de Bazo, Humedad, flema o diarrea; la tolerancia digestiva guía la selección.",
        "Sweet, slightly salty, bland" => "Dulce, ligeramente salada, insípida",
        "Salty, sweet" => "Salada, dulce",
        "26. Astringents" => "26. Astringentes",
        "Board Guideline 2026 · 14 substances · Common foundation: stabilize and bind leakage of Essence, urine, stool, sweat, Blood, fluids or Lung Qi." => "Guía Board 2026 · 14 sustancias · Fundamento común: estabilizar y contener pérdidas de Esencia, orina, heces, sudor, Sangre, líquidos o Qi de Pulmón.",
        "type and location of leakage, deficiency versus excess, Heat or Cold, cough, diarrhea, urination, discharge, bleeding, sweating, toxicity and unresolved pathogens." => "tipo y localización de la pérdida, deficiencia frente a exceso, Calor o Frío, tos, diarrea, micción, descarga, sangrado, sudoración, toxicidad y patógenos sin resolver.",
        "Stabilizes Kidney and Liver, contains Essence and urine, stops excessive sweating and controls uterine bleeding." => "Estabiliza Riñón e Hígado, contiene Esencia y orina, detiene sudoración excesiva y controla sangrado uterino.",
        "Avoid in painful or difficult urination from Damp-Heat; traditionally antagonizes Jie Geng, Fang Feng and Fang Ji." => "Evitar en micción dolorosa o difícil por Humedad-Calor; tradicionalmente antagoniza Jie Geng, Fang Feng y Fang Ji.",
        "Stabilizes Kidney and contains Essence; binds the Intestines and stops diarrhea." => "Estabiliza Riñón y contiene Esencia; astringe los Intestinos y detiene diarrea.",
        "Avoid when excess pathogens remain, especially Heat; excessive or prolonged use may cause abdominal pain or constipation." => "Evitar cuando persisten patógenos de exceso, especialmente Calor; el uso excesivo o prolongado puede causar dolor abdominal o estreñimiento.",
        "Stops bleeding and vaginal discharge, neutralizes Stomach acid, resolves Dampness and promotes healing topically." => "Detiene sangrado y descarga vaginal, neutraliza ácido de Estómago, resuelve Humedad y promueve cicatrización tópica.",
        "Prolonged use may cause constipation; traditionally antagonizes Fu Zi and Bai Ji." => "El uso prolongado puede causar estreñimiento; tradicionalmente antagoniza Fu Zi y Bai Ji.",
        "Restrains urine for enuresis or frequent urination, assists Kidney Yang and improves vision." => "Retiene orina en enuresis o micción frecuente, ayuda al Yang de Riñón y mejora la visión.",
        "Avoid in difficult urination or Yin deficiency with Heat signs." => "Evitar en dificultad urinaria o deficiencia de Yin con signos de Calor.",
        "Strengthens the Spleen, stops diarrhea, dispels Dampness and stops vaginal discharge." => "Fortalece el Bazo, detiene diarrea, dispersa Humedad y detiene descarga vaginal.",
        "Avoid with difficulty urinating or defecating." => "Evitar con dificultad para orinar o defecar.",
        "Strengthens the Spleen, stops diarrhea, nourishes the Heart, stabilizes Kidney Essence and calms the Spirit." => "Fortalece el Bazo, detiene diarrea, nutre el Corazón, estabiliza Esencia de Riñón y calma el Shen.",
        "Avoid in abdominal distension or constipation." => "Evitar en distensión abdominal o estreñimiento.",
        "Contains Lung Qi and stops cough, stabilizes Essence, stops diarrhea and sweating, and generates fluids." => "Contiene el Qi de Pulmón y detiene la tos, estabiliza Esencia, detiene diarrea y sudoración y genera líquidos.",
        "Avoid in Exterior conditions, early-stage rashes or internal excess Heat; may cause heartburn." => "Evitar en condiciones Exteriores, erupciones en etapa temprana o Calor interno de exceso; puede causar acidez.",
        "Contains Lung Qi and stops cough; eliminates Dampness and stops discharge." => "Contiene el Qi de Pulmón y detiene la tos; elimina Humedad y detiene descarga.",
        "Avoid in excess conditions or when thick sputum is difficult to expectorate; toxic if overused." => "Evitar en condiciones de exceso o cuando el esputo espeso es difícil de expectorar; es tóxica si se usa en exceso.",
        "Remove the husk and crush the seed." => "Retirar la cáscara y triturar la semilla.",
        "Contains Lung Qi and stops cough, generates fluids, stops bleeding and expels roundworms with abdominal pain; used topically for corns or warts." => "Contiene el Qi de Pulmón y detiene la tos, genera líquidos, detiene sangrado y expulsa lombrices con dolor abdominal; se usa tópicamente para callos o verrugas.",
        "Avoid in internal Heat, excess stagnation or unresolved Exterior disorders." => "Evitar en Calor interno, estancamiento de exceso o trastornos Exteriores sin resolver.",
        "Binds the Intestines and stops diarrhea, contains Lung Qi and improves the throat." => "Astringe los Intestinos y detiene diarrea, contiene el Qi de Pulmón y mejora la garganta.",
        "Avoid in Exterior patterns or internal Damp-Heat; may inhibit digestion in Spleen deficiency." => "Evitar en patrones Exteriores o Humedad-Calor interna; puede inhibir la digestión en deficiencia de Bazo.",
        "Binds the Intestines, stops diarrhea and bleeding, and promotes healing of wounds." => "Astringe los Intestinos, detiene diarrea y sangrado y promueve cicatrización de heridas.",
        "Avoid in Heat-type diarrhea, dysentery or Damp-Heat; use cautiously during pregnancy; traditionally antagonizes Da Huang." => "Evitar en diarrea tipo Calor, disentería o Humedad-Calor; usar con precaución durante el embarazo; tradicionalmente antagoniza Da Huang.",
        "Warms the Middle Jiao, moves Qi, relieves pain, binds the Intestines and stops diarrhea." => "Calienta el Jiao Medio, mueve el Qi, alivia dolor, astringe los Intestinos y detiene diarrea.",
        "Avoid in Heat-type diarrhea or dysentery." => "Evitar en diarrea o disentería tipo Calor.",
        "Clears Heat, dries Dampness, kills parasites and binds the Intestines to stop diarrhea." => "Elimina Calor, seca Humedad, elimina parásitos y astringe los Intestinos para detener diarrea.",
        "Avoid in Spleen and Stomach deficiency with Cold or Kidney Yin deficiency." => "Evitar en deficiencia de Bazo y Estómago con Frío o deficiencia de Yin de Riñón.",
        "Stops sweating from Qi or Yin deficiency; Xiao Mai also nourishes the Heart and calms the Spirit." => "Detiene sudoración por deficiencia de Qi o Yin; Xiao Mai además nutre el Corazón y calma el Shen.",
        "Avoid when sweating is caused by an unresolved Exterior pathogen." => "Evitar cuando la sudoración es causada por un patógeno Exterior sin resolver.",
        "Stabilize and bind leakage" => "Estabilizar y contener pérdidas",
        "All fourteen restrain leakage, but selection depends on whether the loss involves Essence, urine, stool, sweat, Blood, fluids, discharge or Lung Qi." => "Las catorce restringen pérdidas, pero la selección depende de si afectan Esencia, orina, heces, sudor, Sangre, líquidos, descarga o Qi de Pulmón.",
        "Stabilize Kidney and contain Essence" => "Estabilizar Riñón y contener Esencia",
        "Shan Zhu Yu, Jin Ying Zi, Hai Piao Xiao and Fu Pen Zi address Kidney leakage; they differ in Blood, diarrhea, vaginal discharge, urination and vision emphasis." => "Shan Zhu Yu, Jin Ying Zi, Hai Piao Xiao y Fu Pen Zi tratan pérdidas de Riñón; difieren en énfasis sobre Sangre, diarrea, descarga vaginal, micción y visión.",
        "Bind the Intestines and stop diarrhea" => "Astringir los Intestinos y detener diarrea",
        "Jin Ying Zi, Qian Shi, Lian Zi, He Zi, Chi Shi Zhi, Rou Dou Kou and Chun Pi stop diarrhea; choose by deficiency, Cold, Heat, Dampness or bleeding." => "Jin Ying Zi, Qian Shi, Lian Zi, He Zi, Chi Shi Zhi, Rou Dou Kou y Chun Pi detienen diarrea; seleccionar según deficiencia, Frío, Calor, Humedad o sangrado.",
        "Contain Lung Qi and stop cough" => "Contener Qi de Pulmón y detener tos",
        "Wu Wei Zi, Bai Guo and Wu Mei astringe Lung Qi; Bai Guo also stops discharge, Wu Mei generates fluids and Wu Wei Zi additionally stops sweating and diarrhea." => "Wu Wei Zi, Bai Guo y Wu Mei astringen el Qi de Pulmón; Bai Guo además detiene descarga, Wu Mei genera líquidos y Wu Wei Zi también detiene sudoración y diarrea.",
        "Stop excessive sweating" => "Detener sudoración excesiva",
        "Shan Zhu Yu, Wu Wei Zi and Fu Xiao Mai restrain sweating; Fu Xiao Mai is the most focused and cool, while Wu Wei Zi also generates fluids." => "Shan Zhu Yu, Wu Wei Zi y Fu Xiao Mai restringen sudoración; Fu Xiao Mai es la más específica y fresca, mientras Wu Wei Zi además genera líquidos.",
        "Generate fluids while astringing" => "Generar líquidos mientras se astringe",
        "Wu Wei Zi and Wu Mei both generate fluids while containing leakage; Wu Mei also treats roundworms and topical corns or warts." => "Wu Wei Zi y Wu Mei generan líquidos mientras contienen pérdidas; Wu Mei además trata lombrices y callos o verrugas tópicas.",
        "Vaginal discharge and Dampness" => "Descarga vaginal y Humedad",
        "Hai Piao Xiao, Qian Shi and Bai Guo stop discharge; Qian Shi strengthens the Spleen, Bai Guo clears Dampness and Hai Piao Xiao also stops bleeding and neutralizes acid." => "Hai Piao Xiao, Qian Shi y Bai Guo detienen descarga; Qian Shi fortalece el Bazo, Bai Guo elimina Humedad y Hai Piao Xiao además detiene sangrado y neutraliza ácido.",
        "Lian Zi and Fu Xiao Mai support the Heart and calm the Spirit while also treating leakage patterns." => "Lian Zi y Fu Xiao Mai apoyan el Corazón y calman el Shen mientras también tratan patrones de pérdida.",
        "Toxicity and unresolved pathogens" => "Toxicidad y patógenos sin resolver",
        "Bai Guo is slightly toxic and must not be overused; astringents generally should not trap unresolved Exterior or excess pathogens." => "Bai Guo es ligeramente tóxica y no debe usarse en exceso; los astringentes generalmente no deben atrapar patógenos Exteriores o de exceso sin resolver.",
        "Sour, astringent" => "Ácida, astringente",
        "Salty, astringent" => "Salada, astringente",
        "Sweet, astringent" => "Dulce, astringente",
        "Sweet, bitter, astringent" => "Dulce, amarga, astringente",
        "Neutral, slightly toxic" => "Neutra, ligeramente tóxica",
        "Bitter, sour, astringent" => "Amarga, ácida, astringente",
        "Sweet, sour, astringent" => "Dulce, ácida, astringente",
        "Sweet, slightly salty" => "Dulce, ligeramente salada",
        "27. Anchor & Calm Shen" => "27. Anclar y calmar el Shen",
        "Board Guideline 2026 · 4 substances · Common foundation: settle and calm the Spirit, anchor Liver Yang and control agitation or leakage." => "Guía Board 2026 · 4 sustancias · Fundamento común: asentar y calmar el Shen, anclar Yang de Hígado y controlar agitación o pérdidas.",
        "Liver Yang rising, Heart agitation, mineral weight, leakage, nodules, hearing or vision, topical healing, digestive weakness and required preparation." => "ascenso de Yang de Hígado, agitación de Corazón, peso mineral, pérdidas, nódulos, audición o visión, cicatrización tópica, debilidad digestiva y preparación necesaria.",
        "Settles and calms the Spirit, anchors Liver Yang, prevents leakage of fluids and treats chronic non-healing sores topically." => "Asienta y calma el Shen, ancla Yang de Hígado, previene pérdidas de líquidos y trata llagas crónicas que no cicatrizan de forma tópica.",
        "Avoid in Damp-Heat or externally contracted excess conditions; traditionally counteracted by Shi Gao and should not be mixed with fish." => "Evitar en Humedad-Calor o condiciones de exceso contraídas externamente; tradicionalmente contrarrestada por Shi Gao y no debe mezclarse con pescado.",
        "Crush and cook 20–30 minutes longer." => "Triturar y cocinar entre 20 y 30 minutos más.",
        "Settles the Spirit and anchors Liver Yang; softens hardness, dissipates nodules, absorbs Stomach acid and prevents leakage." => "Asienta el Shen y ancla Yang de Hígado; ablanda durezas, disipa nódulos, absorbe ácido de Estómago y previene pérdidas.",
        "Avoid in high fever from excess without sweating." => "Evitar en fiebre alta por exceso sin sudoración.",
        "Anchors Liver Yang, tonifies Kidney and Liver, benefits hearing and vision, and helps the Kidney grasp Lung Qi." => "Ancla Yang de Hígado, tonifica Riñón e Hígado, beneficia audición y visión y ayuda al Riñón a captar el Qi de Pulmón.",
        "Use cautiously with weak Spleen and Stomach; traditionally antagonizes Mu Dan Pi." => "Usar con precaución en debilidad de Bazo y Estómago; tradicionalmente antagoniza Mu Dan Pi.",
        "Sedates the Heart and calms the Spirit, clears the Liver and superficial visual obstruction, and promotes tissue healing topically." => "Seda el Corazón y calma el Shen, elimina Calor de Hígado y obstrucción visual superficial y promueve cicatrización de tejidos tópicamente.",
        "Use in pill or powder form." => "Usar en forma de píldora o polvo.",
        "Settle, anchor and calm the Spirit" => "Asentar, anclar y calmar el Shen",
        "All four are heavy settling substances, but differ in leakage, nodules, Kidney function, hearing, vision and topical tissue healing." => "Las cuatro son sustancias pesadas que asientan, pero difieren en pérdidas, nódulos, función renal, audición, visión y cicatrización tópica.",
        "Calm Liver Yang" => "Calmar Yang de Hígado",
        "Long Gu, Mu Li and Ci Shi anchor rising Liver Yang; Long Gu emphasizes Spirit and leakage, Mu Li nodules, and Ci Shi Kidney, hearing and vision." => "Long Gu, Mu Li y Ci Shi anclan Yang de Hígado ascendente; Long Gu enfatiza Shen y pérdidas, Mu Li nódulos y Ci Shi Riñón, audición y visión.",
        "Prevent leakage of fluids" => "Prevenir pérdidas de líquidos",
        "Calcined Long Gu and Mu Li astringe sweating, Essence or other leakage while also calming agitation." => "Long Gu y Mu Li calcinadas astringen sudoración, Esencia u otras pérdidas mientras también calman agitación.",
        "Soften hardness and dissipate nodules" => "Ablandar durezas y disipar nódulos",
        "Mu Li is the principal choice for hard nodules and also absorbs Stomach acid; Long Gu is more focused on leakage and Spirit." => "Mu Li es la opción principal para nódulos duros y además absorbe ácido de Estómago; Long Gu se enfoca más en pérdidas y Shen.",
        "Kidney, hearing and grasping Qi" => "Riñón, audición y captación de Qi",
        "Ci Shi uniquely tonifies Kidney and Liver, benefits hearing and helps the Kidney grasp Lung Qi." => "Ci Shi se distingue por tonificar Riñón e Hígado, beneficiar audición y ayudar al Riñón a captar el Qi de Pulmón.",
        "Vision and topical healing" => "Visión y cicatrización tópica",
        "Ci Shi supports vision through Kidney-Liver tonification; Zhen Zhu clears superficial visual obstruction and promotes flesh generation topically." => "Ci Shi apoya visión mediante tonificación de Riñón-Hígado; Zhen Zhu elimina obstrucción visual superficial y promueve generación de tejido tópicamente.",
        "Mineral preparation and digestion" => "Preparación mineral y digestión",
        "Long Gu, Mu Li and Ci Shi require crushing and prolonged cooking; heavy minerals require caution when Spleen and Stomach are weak." => "Long Gu, Mu Li y Ci Shi requieren trituración y cocción prolongada; los minerales pesados exigen precaución cuando Bazo y Estómago están débiles.",
        "28. Nourish & Calm Shen" => "28. Nutrir y calmar el Shen",
        "Board Guideline 2026 · 4 herbs · Common foundation: nourish Heart or Liver Blood, calm the Spirit and address deficiency-related insomnia or agitation." => "Guía Board 2026 · 4 hierbas · Fundamento común: nutrir Sangre de Corazón o Hígado, calmar el Shen y tratar insomnio o agitación relacionados con deficiencia.",
        "Blood or Yin deficiency, insomnia, sweating, bowel dryness, phlegm obstructing the orifices, cough, swelling, channel obstruction and itching." => "deficiencia de Sangre o Yin, insomnio, sudoración, sequedad intestinal, flema que obstruye orificios, tos, hinchazón, obstrucción de canales y picazón.",
        "Nourishes Liver Blood, calms the Spirit and astringes excessive sweating." => "Nutre Sangre de Hígado, calma el Shen y astringe sudoración excesiva.",
        "Avoid in severe diarrhea or excess Heat." => "Evitar en diarrea grave o Calor de exceso.",
        "Nourishes Heart Blood and calms the Spirit, moistens the Intestines and treats Yin-deficiency night sweating." => "Nutre Sangre de Corazón y calma el Shen, humedece los Intestinos y trata sudoración nocturna por deficiencia de Yin.",
        "Avoid in loose stools or phlegm disorders; traditionally antagonizes Ju Hua." => "Evitar en heces sueltas o trastornos de flema; tradicionalmente antagoniza Ju Hua.",
        "Calms the Spirit, expels phlegm and opens the Heart orifices, stops cough and reduces abscesses and swelling." => "Calma el Shen, expulsa flema y abre los orificios de Corazón, detiene la tos y reduce abscesos e hinchazón.",
        "Avoid in Yin deficiency with Heat; use cautiously with ulcers or gastritis; traditionally counteracted by Zhen Zhu and Li Lu." => "Evitar en deficiencia de Yin con Calor; usar con precaución en úlceras o gastritis; tradicionalmente contrarrestada por Zhen Zhu y Li Lu.",
        "Nourishes Blood, calms the Spirit, unblocks channels and alleviates itching when used as an external wash." => "Nutre la Sangre, calma el Shen, desbloquea canales y alivia picazón cuando se usa como lavado externo.",
        "Nourish Blood and calm the Spirit" => "Nutrir Sangre y calmar el Shen",
        "All four calm the Spirit, but differ in sweating, bowel dryness, phlegm, orifices, cough, channels, swelling and itching." => "Las cuatro calman el Shen, pero difieren en sudoración, sequedad intestinal, flema, orificios, tos, canales, hinchazón y picazón.",
        "Nourish Blood for insomnia" => "Nutrir Sangre para insomnio",
        "Suan Zao Ren nourishes Liver Blood and astringes sweating; Bai Zi Ren nourishes Heart Blood and moistens the Intestines; Ye Jiao Teng also unblocks channels." => "Suan Zao Ren nutre Sangre de Hígado y astringe sudoración; Bai Zi Ren nutre Sangre de Corazón y humedece los Intestinos; Ye Jiao Teng además desbloquea canales.",
        "Night sweating" => "Sudoración nocturna",
        "Suan Zao Ren astringes sweating through Liver Blood nourishment, while Bai Zi Ren is useful when Yin-deficiency night sweats accompany dry stool." => "Suan Zao Ren astringe sudoración al nutrir Sangre de Hígado, mientras Bai Zi Ren es útil cuando sudoración nocturna por deficiencia de Yin acompaña heces secas.",
        "Bai Zi Ren is the distinctive Shen-calming herb that also lubricates the bowel; avoid it when loose stools predominate." => "Bai Zi Ren es la hierba calmante del Shen distintiva que además lubrica el intestino; evitarla cuando predominan heces sueltas.",
        "Phlegm and open the orifices" => "Flema y apertura de orificios",
        "Yuan Zhi uniquely transforms phlegm, opens the Heart orifices, stops cough and reduces abscesses or swelling." => "Yuan Zhi se distingue por transformar flema, abrir los orificios de Corazón, detener tos y reducir abscesos o hinchazón.",
        "Unblock channels and relieve itching" => "Desbloquear canales y aliviar picazón",
        "Ye Jiao Teng nourishes Blood while opening channels and may be used externally to relieve itching." => "Ye Jiao Teng nutre la Sangre mientras abre canales y puede usarse externamente para aliviar picazón.",
        "Deficiency versus excess agitation" => "Agitación por deficiencia frente a exceso",
        "These nourishing herbs suit deficiency-related insomnia; severe excess Heat, phlegm or unresolved excess patterns require different treatment." => "Estas hierbas nutritivas son apropiadas para insomnio por deficiencia; Calor intenso de exceso, flema o patrones de exceso sin resolver requieren otro tratamiento.",
        "Sweet, sour" => "Dulce, ácida",
        "29. Orifice Opening Aromatics" => "29. Aromáticas que abren los orificios",
        "30. Extinguish Liver Wind" => "30. Extinguir Viento de Hígado",
        "31. Expel Parasites" => "31. Expulsar parásitos",
        "32. External Use" => "32. Uso externo",
        "Previous" => "Anterior",
        "Next" => "Siguiente",
        "Category navigation" => "Navegación de categorías",
        "Board Guideline 2026 · 1 substance · Common foundation: clear intense Heat, open the orifices, awaken the Spirit, extinguish Wind and relieve toxicity." => "Guía Board 2026 · 1 sustancia · Fundamento común: eliminar Calor intenso, abrir los orificios, despertar el Shen, extinguir Viento y aliviar toxicidad.",
        "Board Guideline 2026 · 6 substances · Common foundation: calm the Liver, extinguish internal Wind, control spasms and address Yang rising or channel obstruction." => "Guía Board 2026 · 6 sustancias · Fundamento común: calmar el Hígado, extinguir Viento interno, controlar espasmos y tratar ascenso de Yang u obstrucción de canales.",
        "Board Guideline 2026 · 1 herb · Common foundation: expel intestinal parasites while moving Qi and relieving accumulation." => "Guía Board 2026 · 1 hierba · Fundamento común: expulsar parásitos intestinales mientras mueve el Qi y alivia acumulación.",
        "Board Guideline 2026 · 1 herb · Common foundation: dry Dampness, kill parasites, stop itching and warm Kidney Yang when appropriately used." => "Guía Board 2026 · 1 hierba · Fundamento común: secar Humedad, eliminar parásitos, detener picazón y calentar Yang de Riñón cuando se usa apropiadamente.",
        "excess Heat, phlegm obstructing the orifices, loss of consciousness, tremor or convulsion, toxicity, pregnancy and digestive deficiency." => "Calor de exceso, flema que obstruye los orificios, pérdida de conciencia, temblor o convulsión, toxicidad, embarazo y deficiencia digestiva.",
        "Heat or Cold origin of internal Wind, Liver Yang rising, spasms, tremor, visual symptoms, rebellious Qi, channel obstruction, wheezing and digestive weakness." => "origen por Calor o Frío del Viento interno, ascenso de Yang de Hígado, espasmos, temblor, síntomas visuales, Qi rebelde, obstrucción de canales, sibilancias y debilidad digestiva.",
        "parasite type, abdominal distension, constipation, tenesmus, urination, malaria and Middle Qi deficiency." => "tipo de parásito, distensión abdominal, estreñimiento, tenesmo, micción, malaria y deficiencia del Qi Medio.",
        "topical versus internal use, Dampness, parasites, itching, painful obstruction, Kidney Yang deficiency, toxicity and Heat patterns." => "uso tópico frente a interno, Humedad, parásitos, picazón, obstrucción dolorosa, deficiencia de Yang de Riñón, toxicidad y patrones de Calor.",
        "Warm, slightly toxic" => "Tibia, ligeramente tóxica",
        "Clears Heart Heat, opens the orifices, awakens the Spirit and expels phlegm; clears Liver Heat, relieves toxicity, extinguishes Wind and stops tremor; drains Heat and relieves toxicity." => "Elimina Calor de Corazón, abre los orificios, despierta el Shen y expulsa flema; elimina Calor de Hígado, alivia toxicidad, extingue Viento y detiene temblor; drena Calor y alivia toxicidad.",
        "Avoid during pregnancy; use only in excess Heat patterns and avoid in Spleen/Stomach deficiency. Traditionally antagonizes Long Gu, Sheng Di, Long Dan Cao and Chang Shan; counteracts Niu Xi." => "Evitar durante el embarazo; usar solo en patrones de Calor de exceso y evitar en deficiencia de Bazo/Estómago. Tradicionalmente antagoniza Long Gu, Sheng Di, Long Dan Cao y Chang Shan; contrarresta Niu Xi.",
        "Controls spasms and tremors; important for internal Liver Wind due to Heat or Cold; extinguishes Wind, relieves pain; used for painful obstruction or Bi syndrome." => "Controla espasmos y temblores; es importante para Viento interno de Hígado por Calor o Frío; extingue Viento y alivia dolor; se usa para obstrucción dolorosa o síndrome Bi.",
        "Drains Liver Heat and pacifies Liver Yang; extinguishes Wind and controls spasms; releases the Exterior." => "Drena Calor de Hígado y pacifica Yang de Hígado; extingue Viento y controla espasmos; libera el Exterior.",
        "Use cautiously when Wind-Heat or excess Heat is absent." => "Usar con precaución cuando no haya Viento-Calor ni Calor de exceso.",
        "Do not cook for more than 20 minutes." => "No cocinar durante más de 20 minutos.",
        "Anchors Liver Yang and clears Liver Heat; drains Liver Fire; improves the eyes and clears superficial visual obstruction; traditionally used for gastric acidity and topically for trauma." => "Ancla Yang de Hígado y elimina Calor de Hígado; drena Fuego de Hígado; mejora los ojos y elimina obstrucción visual superficial; se usa tradicionalmente para acidez gástrica y tópicamente en traumatismos.",
        "Traditionally counteracts Xuan Fu Hua." => "Tradicionalmente contrarresta Xuan Fu Hua.",
        "Crush and cook 60 minutes longer." => "Triturar y cocinar 60 minutos más.",
        "Soothes Liver Qi; disperses Wind-Heat and brightens the eyes; dispels Wind and stops itching." => "Suaviza el Qi de Hígado; dispersa Viento-Calor e ilumina los ojos; expulsa Viento y detiene picazón.",
        "Avoid during pregnancy or in Qi/Blood deficiency." => "Evitar durante el embarazo o en deficiencia de Qi/Sangre.",
        "Strongly directs rebellious Stomach and Lung Qi downward; cools Blood and stops bleeding; anchors Liver Yang." => "Dirige con fuerza hacia abajo el Qi rebelde de Estómago y Pulmón; enfría la Sangre y detiene el sangrado; ancla Yang de Hígado.",
        "Use cautiously during pregnancy; traditionally counteracts Fu Zi." => "Usar con precaución durante el embarazo; tradicionalmente contrarresta Fu Zi.",
        "Clears Heat and unblocks channels and collaterals; stops spasms and convulsions; calms wheezing; promotes urination; traditionally used in hypertension patterns." => "Elimina Calor y desobstruye canales y colaterales; detiene espasmos y convulsiones; calma sibilancias; promueve la micción; se usa tradicionalmente en patrones de hipertensión.",
        "Avoid in weak Spleen/Stomach or when excess Heat is absent." => "Evitar con Bazo/Estómago débiles o cuando no haya Calor de exceso.",
        "Kills a wide variety of intestinal parasites, especially tapeworm; moves Qi in the Intestines for distension, constipation and tenesmus; promotes urination; used for malarial disorders." => "Elimina una amplia variedad de parásitos intestinales, especialmente tenia; mueve el Qi en los Intestinos para distensión, estreñimiento y tenesmo; promueve la micción; se usa para trastornos palúdicos.",
        "Avoid in collapse or deficiency of Middle Qi." => "Evitar en colapso o deficiencia del Qi Medio.",
        "Dries Dampness, kills parasites and stops itching topically; expels Wind-Cold-Damp for painful obstruction; warms Kidney Yang when used internally." => "Seca Humedad, elimina parásitos y detiene picazón por vía tópica; expulsa Viento-Frío-Humedad en obstrucción dolorosa; calienta Yang de Riñón cuando se usa internamente.",
        "Avoid in Lower Jiao Damp-Heat or Yin deficiency with Heat. Traditionally antagonizes Mu Dan Pi, Ba Dou and Bei Mu." => "Evitar en Humedad-Calor del Jiao Inferior o deficiencia de Yin con Calor. Tradicionalmente antagoniza Mu Dan Pi, Ba Dou y Bei Mu.",
        "Primarily used externally; internal use requires caution." => "Se usa principalmente de forma externa; el uso interno requiere precaución.",
        "Open the orifices and awaken the Spirit" => "Abrir los orificios y despertar el Shen",
        "Niu Huang clears Heart Heat, expels phlegm and restores consciousness when Heat and phlegm obstruct the orifices." => "Niu Huang elimina Calor de Corazón, expulsa flema y restaura la conciencia cuando Calor y flema obstruyen los orificios.",
        "Extinguish Wind and stop tremor" => "Extinguir Viento y detener temblor",
        "Niu Huang clears Liver Heat, extinguishes internal Wind and stops tremor or convulsion from intense Heat." => "Niu Huang elimina Calor de Hígado, extingue Viento interno y detiene temblor o convulsión por Calor intenso.",
        "Clear Heat and relieve toxicity" => "Eliminar Calor y aliviar toxicidad",
        "Niu Huang drains intense Heat and relieves Fire toxicity affecting the Heart, Liver or consciousness." => "Niu Huang drena Calor intenso y alivia toxicidad de Fuego que afecta Corazón, Hígado o conciencia.",
        "Excess Heat requirement" => "Requisito de Calor de exceso",
        "Niu Huang is reserved for strong excess Heat and is inappropriate in Spleen or Stomach deficiency." => "Niu Huang se reserva para Calor fuerte de exceso y no es apropiado en deficiencia de Bazo o Estómago.",
        "Pill or powder administration" => "Administración en píldora o polvo",
        "Niu Huang is given in small doses as a pill or powder rather than in a routine decoction." => "Niu Huang se administra en dosis pequeñas como píldora o polvo, no en una decocción habitual.",
        "Extinguish Liver Wind and control spasms" => "Extinguir Viento de Hígado y controlar espasmos",
        "All six calm internal Wind or its manifestations, with different emphasis on Yang rising, Heat, rebellious Qi, channels, pain or wheezing." => "Las seis sustancias calman Viento interno o sus manifestaciones, con distinto énfasis en ascenso de Yang, Calor, Qi rebelde, canales, dolor o sibilancias.",
        "Tian Ma and Gou Teng" => "Tian Ma y Gou Teng",
        "Tian Ma is neutral and treats internal Wind from Heat or Cold; Gou Teng is slightly cold and emphasizes clearing Heat and pacifying Yang." => "Tian Ma es neutra y trata Viento interno por Calor o Frío; Gou Teng es ligeramente fría y enfatiza eliminar Calor y pacificar Yang.",
        "Anchor Liver Yang" => "Anclar Yang de Hígado",
        "Shi Jue Ming and Dai Zhe Shi are heavy substances that anchor rising Liver Yang; Shi Jue Ming emphasizes the eyes and Dai Zhe Shi rebellious Qi." => "Shi Jue Ming y Dai Zhe Shi son sustancias pesadas que anclan el ascenso de Yang de Hígado; Shi Jue Ming enfatiza los ojos y Dai Zhe Shi el Qi rebelde.",
        "Brighten the eyes" => "Iluminar los ojos",
        "Gou Teng, Shi Jue Ming and Bai Ji Li address visual symptoms through clearing Liver Heat, pacifying Yang or dispersing Wind-Heat." => "Gou Teng, Shi Jue Ming y Bai Ji Li tratan síntomas visuales eliminando Calor de Hígado, pacificando Yang o dispersando Viento-Calor.",
        "Clear Heat and stop convulsions" => "Eliminar Calor y detener convulsiones",
        "Gou Teng and Di Long clear Heat and control spasms; Di Long additionally unblocks channels, calms wheezing and promotes urination." => "Gou Teng y Di Long eliminan Calor y controlan espasmos; Di Long además desobstruye canales, calma sibilancias y promueve la micción.",
        "Direct rebellious Qi downward" => "Dirigir el Qi rebelde hacia abajo",
        "Dai Zhe Shi strongly descends rebellious Stomach and Lung Qi while anchoring Liver Yang and cooling Blood." => "Dai Zhe Shi hace descender con fuerza el Qi rebelde de Estómago y Pulmón mientras ancla Yang de Hígado y enfría la Sangre.",
        "Painful obstruction and channels" => "Obstrucción dolorosa y canales",
        "Tian Ma treats painful obstruction, Bai Ji Li disperses Wind and Di Long unblocks channels and collaterals." => "Tian Ma trata obstrucción dolorosa, Bai Ji Li dispersa Viento y Di Long desobstruye canales y colaterales.",
        "Mineral preparation" => "Preparación de minerales",
        "Shi Jue Ming and Dai Zhe Shi require crushing and prolonged cooking before other herbs." => "Shi Jue Ming y Dai Zhe Shi requieren trituración y cocción prolongada antes que las demás hierbas.",
        "Expel intestinal parasites" => "Expulsar parásitos intestinales",
        "Bing Lang kills a broad range of intestinal parasites while moving Qi to relieve accumulation." => "Bing Lang elimina una amplia gama de parásitos intestinales mientras mueve el Qi para aliviar acumulación.",
        "Tapeworm emphasis" => "Énfasis en la tenia",
        "Bing Lang is especially emphasized for tapeworm and associated abdominal symptoms." => "Bing Lang se destaca especialmente para tenia y síntomas abdominales asociados.",
        "Move intestinal Qi" => "Mover el Qi intestinal",
        "Bing Lang relieves distension, constipation and tenesmus by moving Qi in the Intestines." => "Bing Lang alivia distensión, estreñimiento y tenesmo moviendo el Qi en los Intestinos.",
        "Promote urination and treat malaria" => "Promover la micción y tratar malaria",
        "Beyond parasites, Bing Lang promotes urination and is traditionally used for malarial disorders." => "Además de parásitos, Bing Lang promueve la micción y se usa tradicionalmente para trastornos palúdicos.",
        "Middle Qi caution" => "Precaución por Qi Medio",
        "Because Bing Lang moves Qi downward, avoid it in collapse or marked deficiency of Middle Qi." => "Como Bing Lang mueve el Qi hacia abajo, debe evitarse en colapso o deficiencia marcada del Qi Medio.",
        "Topical Dampness and itching" => "Humedad tópica y picazón",
        "She Chuang Zi dries Dampness and stops itching when applied externally." => "She Chuang Zi seca Humedad y detiene picazón cuando se aplica externamente.",
        "Kill parasites" => "Eliminar parásitos",
        "She Chuang Zi kills parasites affecting the skin or genital region and is commonly selected for external washes." => "She Chuang Zi elimina parásitos que afectan piel o región genital y se selecciona comúnmente para lavados externos.",
        "Wind-Cold-Damp painful obstruction" => "Obstrucción dolorosa por Viento-Frío-Humedad",
        "She Chuang Zi expels Wind-Cold-Damp to relieve painful obstruction when appropriately used." => "She Chuang Zi expulsa Viento-Frío-Humedad para aliviar obstrucción dolorosa cuando se usa apropiadamente.",
        "Warm Kidney Yang" => "Calentar Yang de Riñón",
        "When used internally, She Chuang Zi warms Kidney Yang, distinct from its more common topical role." => "Cuando se usa internamente, She Chuang Zi calienta Yang de Riñón, función distinta de su uso tópico más común.",
        "Toxicity and internal-use caution" => "Toxicidad y precaución en uso interno",
        "Its slight toxicity and warming nature require caution internally and avoidance in Damp-Heat or Yin-deficiency Heat." => "Su ligera toxicidad y naturaleza tibia exigen precaución interna y evitarla en Humedad-Calor o Calor por deficiencia de Yin.",
    ];

    return $translations[$text] ?? $text;
}


$languageversion = '20261110search2';
$baseparams = $ises
    ? ['lang' => 'es', 'ui' => 'es', 'v' => $languageversion]
    : ['lang' => 'en', 'ui' => 'en', 'v' => $languageversion];
$activeparams = $baseparams;
$languageparams = $ises
    ? ['lang' => 'en', 'ui' => 'en', 'v' => $languageversion]
    : ['lang' => 'es', 'ui' => 'es', 'v' => $languageversion];
if ($selectedcategory !== '') {
    $activeparams['category'] = $selectedcategory;
    $languageparams['category'] = $selectedcategory;
}

$pageurl = new moodle_url('/blocks/myprogress/herbs-category-comparison.php', $activeparams);
$categoriesurl = new moodle_url('/blocks/myprogress/herbs-category-comparison.php', $baseparams);
$categoryurls = [];
foreach ($categoryids as $categoryid) {
    $categoryparams = $baseparams;
    $categoryparams['category'] = $categoryid;
    $categoryurls[$categoryid] = new moodle_url('/blocks/myprogress/herbs-category-comparison.php', $categoryparams);
}
$previouscategoryurl = null;
$nextcategoryurl = null;
if ($selectedcategory !== '') {
    $selectedcategoryindex = array_search($selectedcategory, $categoryids, true);
    if ($selectedcategoryindex !== false && $selectedcategoryindex > 0) {
        $previouscategoryurl = $categoryurls[$categoryids[$selectedcategoryindex - 1]];
    }
    if ($selectedcategoryindex !== false && $selectedcategoryindex < count($categoryids) - 1) {
        $nextcategoryurl = $categoryurls[$categoryids[$selectedcategoryindex + 1]];
    }
}
$backurl = new moodle_url('/blocks/myprogress/study-guide.php', [
    'area' => 'herbology',
    'view' => 'library',
] + ($ises ? ['lang' => 'es', 'ui' => 'es'] : ['lang' => 'en', 'ui' => 'en']));
$languageurl = new moodle_url('/blocks/myprogress/herbs-category-comparison.php', $languageparams);

$herbs = [
    ['Ma Huang', 'Ephedrae Herba', 'Acrid, slightly bitter', 'Warm', 'LU, UB', 'Strongly releases an excess exterior pattern; disseminates Lung Qi; relieves wheezing; promotes urination.', 'Avoid in deficiency sweating; use caution with hypertension.', '2–9 g', 'Cook first; remove foam.'],
    ['Gui Zhi', 'Cinnamomi Ramulus', 'Acrid, sweet', 'Warm', 'HT, LU, UB', 'Releases the muscle layer; warms channels; harmonizes Ying and Wei; assists Yang Qi.', 'Use caution with febrile disease, Yin-deficiency heat, pregnancy, or excessive bleeding.', '3–9 g', '—'],
    ['Zi Su Ye', 'Perillae Folium', 'Acrid', 'Warm, aromatic', 'LU, SP', 'Releases mild Wind-Cold; moves Qi; expands the chest; calms the fetus; helps seafood poisoning.', 'Use cautiously with Qi deficiency or spontaneous sweating.', '5–9 g', 'Add late when appropriate.'],
    ['Jing Jie', 'Schizonepetae Herba', 'Acrid', 'Slightly warm', 'LU, LV', 'Releases the exterior; vents rashes; relieves itching; charred form stops bleeding.', 'Use cautiously with exterior deficiency and spontaneous sweating.', '4.5–9 g', 'Char for bleeding indications.'],
    ['Fang Feng', 'Saposhnikoviae Radix', 'Acrid, sweet', 'Slightly warm', 'UB, LV, SP', 'Expels Wind; relieves pain, headache and spasms; addresses Wind-Damp and diarrhea.', 'Use cautiously with Blood or Yin deficiency.', '4.5–9 g', '—'],
    ['Qiang Huo', 'Notopterygii Rhizoma', 'Acrid, bitter', 'Warm, aromatic', 'UB, KD', 'Expels Wind-Cold-Damp; relieves occipital and upper-body pain.', 'Use cautiously with Blood or Yin deficiency; may cause nausea in excess.', '3–9 g', '—'],
    ['Gao Ben', 'Ligustici Rhizoma', 'Acrid', 'Warm', 'UB, DU', 'Expels Wind-Cold and relieves vertex headache.', 'Avoid in Blood or Yin deficiency and heat patterns.', '3–9 g', '—'],
    ['Bai Zhi', 'Angelicae Dahuricae Radix', 'Acrid', 'Warm', 'LU, ST, SP', 'Relieves frontal headache; opens the nose; reduces swelling; expels pus and damp discharge.', 'Use cautiously with Blood or Yin deficiency and dryness.', '3–9 g', '—'],
    ['Xi Xin', 'Asari Herba', 'Acrid', 'Warm', 'LU, HT, KD', 'Disperses Wind-Cold; warms Lung; transforms thin mucus; relieves pain; opens the nose.', 'Dose carefully; avoid in deficiency sweating and use caution with renal risk.', '1–3 g', 'Do not exceed the prescribed dose.'],
    ['Xin Yi', 'Magnoliae Flos', 'Acrid', 'Warm', 'LU, ST', 'Opens the nasal passages and relieves congestion, discharge, headache and loss of smell.', 'Use cautiously with Yin-deficiency heat.', '3–9 g', 'Wrap in cloth while decocting.'],
    ['Cang Er Zi', 'Xanthii Fructus', 'Sweet, bitter', 'Warm, toxic', 'LU', 'Dispels Wind; opens the nose; relieves sinus obstruction, discharge and headache.', 'Toxic in excess; avoid overdose and use cautiously in Blood deficiency.', '3–9 g', 'Use only properly prepared material.'],
    ['Sheng Jiang', 'Zingiberis Rhizoma Recens', 'Acrid', 'Slightly warm', 'LU, SP, ST', 'Releases mild exterior Cold; warms Lung; stops cough; harmonizes the Middle; relieves vomiting.', 'Use cautiously with Lung Heat or Yin-deficiency heat.', '3–9 g', 'Fresh ginger.'],
    ['Xiang Ru', 'Moslae Herba', 'Acrid', 'Slightly warm, aromatic', 'LU, ST', 'Releases Summer Wind-Cold; transforms Dampness; promotes urination; reduces swelling.', 'Avoid in Qi deficiency with spontaneous sweating.', '3–9 g', 'Cook briefly for exterior disorders.'],
    ['Cong Bai', 'Allii Fistulosi Bulbus', 'Acrid', 'Warm', 'LU, ST', 'Releases mild Wind-Cold; opens the Lung; unblocks Yang Qi and resolves clumps.', 'Use cautiously with Yin deficiency or excessive sweating.', '3–9 g', 'Use the white bulb.'],
    ['Dan Dou Chi', 'Sojae Semen Praeparatum', 'Acrid, bitter', 'Slightly cold', 'LU, ST', 'Gently releases the exterior; relieves constraint and irritability; harmonizes the Middle.', 'Use according to preparation and pattern.', '6–15 g', 'Prepared soybean; processing influences its tendency.'],
];

$similarities = [
    ['Strong exterior release / diaphoresis', 'Ma Huang · Gui Zhi', 'Warm the channels and promote sweating; Ma Huang is stronger and Gui Zhi harmonizes Ying and Wei.'],
    ['Gentle Wind-Cold release', 'Zi Su Ye · Jing Jie · Fang Feng · Sheng Jiang · Cong Bai · Dan Dou Chi', 'Release mild exterior Wind-Cold when strong diaphoresis is unnecessary.'],
    ['Wind-Cold-Damp pain and headache', 'Fang Feng · Qiang Huo · Gao Ben · Bai Zhi', 'Qiang Huo emphasizes occiput and upper body; Gao Ben the vertex; Bai Zhi the frontal region.'],
    ['Nasal congestion and sinus discharge', 'Bai Zhi · Xi Xin · Xin Yi · Cang Er Zi', 'Open the nasal passages and relieve obstruction, discharge or headache.'],
    ['Lung Qi, cough and phlegm', 'Ma Huang · Zi Su Ye · Xi Xin · Sheng Jiang', 'Warm or diffuse Lung Qi, transform phlegm and relieve cough or wheezing.'],
    ['Aromatic / Damp or Summer-Damp overlap', 'Zi Su Ye · Qiang Huo · Xiang Ru', 'Address exterior Wind-Cold with Dampness; Xiang Ru especially emphasizes Summer-Damp.'],
];

$windheatherbs = [
    ['Bo He', 'Menthae Herba', 'Acrid', 'Cool, aromatic', 'LU, LV', 'Clears the head and eyes; benefits the throat; soothes constrained Liver Qi; vents rashes.', 'Use cautiously with Qi-deficiency exterior patterns or Yin-deficiency heat.', '3–6 g', 'Add during the final 5 minutes.'],
    ['Niu Bang Zi', 'Arctii Fructus', 'Acrid, bitter', 'Cold', 'LU, ST', 'Disperses Wind-Heat; benefits the throat; clears Heat and toxin; vents rashes; moistens the intestines.', 'Use cautiously with Qi-deficiency diarrhea or open sores without toxin.', '6–12 g', 'Crush before decocting.'],
    ['Chan Tui', 'Cicadae Periostracum', 'Sweet, salty', 'Slightly cold', 'LU, LV', 'Disperses Wind-Heat; vents rashes; relieves itching; benefits the throat; clears the eyes; extinguishes Liver Wind and spasms.', 'Use cautiously in deficiency exterior patterns and during pregnancy.', '3–9 g', '—'],
    ['Sang Ye', 'Mori Folium', 'Sweet, bitter', 'Cold', 'LU, LV', 'Disperses Wind-Heat; clears Lung Heat and moistens dryness; cools Blood; stops bleeding; clears the eyes.', 'No major caution noted at standard dosage.', '4.5–9 g', '—'],
    ['Ju Hua', 'Chrysanthemi Flos', 'Sweet, bitter', 'Slightly cold', 'LU, LV', 'Disperses Wind-Heat; clears the eyes; calms the Liver; extinguishes Wind; clears Heat and toxin.', 'Use cautiously with Qi deficiency, poor appetite or diarrhea.', '4.5–15 g', 'Add late when appropriate.'],
    ['Man Jing Zi', 'Viticis Fructus', 'Bitter, acrid', 'Slightly cold', 'UB, LV, ST', 'Disperses Wind-Heat; clears the head and eyes; relieves headache; expels Wind-Damp.', 'Use cautiously when headache or eye problems arise from Blood deficiency or weak Stomach Qi.', '6–12 g', 'Crush before decocting.'],
    ['Dan Dou Chi', 'Sojae Semen Praeparatum', 'Sweet, slightly bitter', 'Cold or warm', 'LU, ST', 'Gently releases the exterior; eliminates irritability and constraint.', 'Use according to preparation and pattern.', '6–12 g', 'Processing influences its thermal tendency.'],
    ['Mu Zei', 'Equiseti Herba', 'Sweet, bitter', 'Neutral', 'GB, LU, LV', 'Disperses Wind-Heat; brightens the eyes; removes visual obstruction; clears Heat; stops bleeding.', 'Use cautiously with frequent urination, depleted fluids, Qi deficiency or pregnancy.', '3–9 g', '—'],
    ['Chai Hu', 'Bupleuri Radix', 'Bitter, acrid', 'Cool', 'GB, LV, PC, SJ', 'Resolves Shaoyang disorders; reduces fever; spreads Liver Qi; raises clear Yang Qi.', 'Use cautiously with Yin-deficiency cough, Liver Fire ascending, headache, nausea or vomiting.', '3–9 g', '—'],
    ['Sheng Ma', 'Cimicifugae Rhizoma', 'Sweet, acrid', 'Slightly cold', 'LI, LU, SP, ST', 'Disperses Wind-Heat; clears Heat and toxin; vents rashes; raises Yang Qi.', 'Use cautiously with Yin-deficiency heat, upper excess, or fully erupted measles.', '3–9 g', '—'],
    ['Ge Gen', 'Puerariae Radix', 'Sweet, acrid', 'Cool', 'SP, ST', 'Releases the muscle layer; clears Heat; generates fluids; relieves thirst; treats diarrhea; vents rashes.', 'No major caution noted at standard dosage.', '9–12 g', '—'],
];

$windheatsimilarities = [
    ['Benefit the throat and vent rashes', 'Bo He · Niu Bang Zi · Chan Tui · Sheng Ma · Ge Gen', 'Disperse Wind-Heat while addressing sore throat or incomplete rash expression.'],
    ['Clear the head and eyes', 'Bo He · Sang Ye · Ju Hua · Man Jing Zi · Mu Zei', 'Treat Wind-Heat affecting the head or eyes; selection depends on headache, dryness, redness or visual obstruction.'],
    ['Liver and Wind involvement', 'Bo He · Chan Tui · Sang Ye · Ju Hua · Man Jing Zi · Chai Hu', 'Share Liver-channel activity ranging from moving Qi to clearing eyes or extinguishing Wind.'],
    ['Lung Heat and dryness', 'Niu Bang Zi · Sang Ye · Ju Hua', 'Clear upper-burner Heat; Sang Ye emphasizes Lung dryness, while Niu Bang Zi emphasizes throat and toxin.'],
    ['Raise clear Yang Qi', 'Chai Hu · Sheng Ma · Ge Gen', 'Raise clear Yang; Sheng Ma emphasizes sinking Qi, Ge Gen diarrhea, and Chai Hu Shaoyang with Liver Qi constraint.'],
    ['Gentle exterior release and irritability', 'Dan Dou Chi · Bo He · Chan Tui', 'Useful when a lighter exterior-releasing strategy is preferred; Dan Dou Chi especially relieves irritability.'],
];

$clearheatherbs = [
    ['Shi Gao', 'Gypsum Fibrosum', 'Sweet, acrid', 'Very cold', 'LU, ST', 'Clears intense Qi-level Heat; drains Lung and Stomach Fire; relieves thirst; used topically for burns or sores.', 'Avoid in Spleen and Stomach deficiency-cold, weak pulse or Yang deficiency.', '15–60 g', 'Crush and decoct 20–30 minutes before other herbs.'],
    ['Zhi Mu', 'Anemarrhenae Rhizoma', 'Bitter, sweet', 'Cold', 'LU, ST, KD', 'Clears Heat and drains Fire; enriches Yin; moistens Lung and Kidney dryness; relieves wasting and thirsting.', 'Use cautiously with diarrhea from Spleen deficiency.', '6–12 g', '—'],
    ['Tian Hua Fen', 'Trichosanthis Radix', 'Bitter, slightly sweet', 'Cold', 'LU, ST', 'Clears Lung and Stomach Heat; generates fluids; relieves thirst; moistens Lung dryness; resolves toxicity and expels pus.', 'Contraindicated during pregnancy; use cautiously with Spleen and Stomach deficiency-cold or diarrhea.', '10–15 g', '—'],
    ['Lu Gen', 'Phragmitis Rhizoma', 'Sweet', 'Cold', 'LU, ST', 'Clears Heat; generates fluids; relieves thirst; clears Lung Heat; stops vomiting; promotes urination.', 'Use cautiously with Spleen and Stomach deficiency-cold.', '15–30 g', 'Fresh form may be used at a larger dose.'],
    ['Dan Zhu Ye', 'Lophatheri Herba', 'Sweet, bland', 'Cold', 'HT, SI, ST', 'Clears Heat and irritability; promotes urination; clears Heart Heat through the Small Intestine.', 'Use cautiously during pregnancy or with depleted fluids.', '6–9 g', '—'],
    ['Zhi Zi', 'Gardeniae Fructus', 'Bitter', 'Cold', 'HT, LU, ST, LV, SJ', 'Drains Fire and eliminates irritability; clears Damp-Heat; cools Blood; stops bleeding; reduces swelling.', 'Use cautiously with loose stools or poor appetite from deficiency-cold.', '6–9 g', 'Crush before decocting.'],
    ['Xia Ku Cao', 'Prunellae Spica', 'Bitter, acrid', 'Cold', 'GB, LV', 'Clears Liver Fire and the eyes; dissipates nodules and swelling; supports patterns of Liver Yang rising.', 'Use cautiously with Spleen and Stomach deficiency.', '9–15 g', '—'],
    ['Jue Ming Zi', 'Cassiae Semen', 'Bitter, sweet, salty', 'Slightly cold', 'LV, LI, KD', 'Clears Liver Heat and brightens the eyes; moistens the intestines; promotes bowel movement; supports hypertension patterns.', 'Use cautiously with diarrhea, loose stools or hypotension.', '9–15 g', 'Crush before decocting.'],
];

$clearheatsimilarities = [
    ['Clear intense Heat and drain Fire', 'Shi Gao · Zhi Mu', 'Both clear strong internal Heat; Shi Gao acts powerfully at the Qi level, while Zhi Mu also enriches Yin and moistens dryness.'],
    ['Generate fluids and relieve thirst', 'Shi Gao · Zhi Mu · Tian Hua Fen · Lu Gen', 'Address Heat damaging fluids; Tian Hua Fen and Lu Gen emphasize fluid generation, while Zhi Mu treats Yin dryness.'],
    ['Clear Lung and Stomach Heat', 'Shi Gao · Zhi Mu · Tian Hua Fen · Lu Gen', 'Share Lung and Stomach Heat indications, with different emphasis on Fire, phlegm, dryness, thirst or vomiting.'],
    ['Eliminate irritability and promote urination', 'Dan Zhu Ye · Zhi Zi', 'Clear Heart Heat and irritability; both can guide Heat out through urination, while Zhi Zi also treats Damp-Heat broadly.'],
    ['Clear Liver Heat and benefit the eyes', 'Xia Ku Cao · Jue Ming Zi', 'Treat red or painful eyes from Liver Heat; Xia Ku Cao dissipates nodules, while Jue Ming Zi moistens the intestines.'],
    ['Reduce swelling, nodules or toxicity', 'Tian Hua Fen · Zhi Zi · Xia Ku Cao', 'Address Heat-related swelling through different mechanisms: expelling pus, reducing swelling, or dissipating nodules.'],
];

$bloodheatherbs = [
    ['Shui Niu Jiao', 'Bubali Cornu', 'Salty', 'Cold', 'HT, LV, ST', 'Clears Heat and toxin; cools the Blood; stops bleeding; arrests tremors and convulsions from blazing Heat.', 'Avoid in deficiency without strong Heat; use cautiously during pregnancy.', '30–120 g decoction; 6–15 g powder', 'Crush and decoct before other herbs.'],
    ['Sheng Di Huang', 'Rehmanniae Radix', 'Sweet, bitter', 'Cold', 'HT, KD, LV', 'Clears Heat and cools Blood; nourishes Yin; generates fluids; relieves wasting and thirsting; clears blazing Heart Fire.', 'Use cautiously with Spleen deficiency, Dampness, poor appetite or diarrhea.', '9–15 g', 'Fresh and prepared forms have different emphases.'],
    ['Xuan Shen', 'Scrophulariae Radix', 'Salty, sweet, bitter', 'Cold', 'KD, LU, ST', 'Cools Blood; nourishes Yin; drains Fire; relieves toxicity, sore throat and swelling; softens hardness and dissipates nodules.', 'Use cautiously with Spleen and Stomach deficiency, Dampness or diarrhea; incompatible with Li Lu.', '9–15 g', '—'],
    ['Mu Dan Pi', 'Moutan Cortex', 'Acrid, bitter', 'Slightly cold', 'HT, LV, KD', 'Clears Heat and cools Blood; clears deficiency Heat without sweating; invigorates Blood; dispels stasis; reduces swelling.', 'Use cautiously with cold patterns, pregnancy, excessive menstruation or bleeding tendency.', '6–12 g', 'Avoid combining with garlic when following traditional cautions.'],
    ['Zi Cao', 'Arnebiae / Lithospermi Radix', 'Sweet', 'Cold', 'HT, LV', 'Cools Blood; vents rashes; relieves Fire toxin; treats Damp-Heat skin disorders; mildly promotes stool and urination.', 'Use cautiously with Spleen and Stomach deficiency or diarrhea because of its mild laxative effect.', '3–9 g', 'Often used topically for skin conditions.'],
];

$bloodheatsimilarities = [
    ['Cool Blood-level Heat', 'Shui Niu Jiao · Sheng Di Huang · Xuan Shen · Mu Dan Pi · Zi Cao', 'All cool the Blood, but differ in emphasis on toxin, Yin damage, stasis, bleeding or rash expression.'],
    ['Nourish Yin while clearing Heat', 'Sheng Di Huang · Xuan Shen', 'Both protect Yin and fluids; Sheng Di Huang emphasizes Blood and fluid generation, while Xuan Shen drains Fire and softens hardness.'],
    ['Heat toxin, swelling and sore throat', 'Shui Niu Jiao · Xuan Shen · Zi Cao', 'Address severe Heat toxin; Xuan Shen emphasizes throat and nodules, while Zi Cao emphasizes skin and rashes.'],
    ['Bleeding versus Blood stasis', 'Shui Niu Jiao · Sheng Di Huang · Mu Dan Pi', 'Shui Niu Jiao and Sheng Di Huang cool Blood to control bleeding; Mu Dan Pi cools while invigorating Blood and dispelling stasis.'],
    ['Deficiency Heat', 'Sheng Di Huang · Xuan Shen · Mu Dan Pi', 'Treat Heat from Yin damage; Mu Dan Pi is notable for deficiency Heat without sweating.'],
    ['Rashes and skin manifestations', 'Zi Cao · Sheng Di Huang · Mu Dan Pi', 'Cool Blood-level Heat affecting the skin; Zi Cao most directly vents rashes and treats topical Fire toxin.'],
];

$dampheatherbs = [
    ['Huang Qin', 'Scutellariae Radix', 'Bitter', 'Cold', 'LU, ST, GB, LI', 'Clears Heat and dries Dampness, especially in the upper burner; clears Lung Heat; treats Shaoyang disorders; clears Heat and stops bleeding when charred; calms the fetus; subdues ascending Liver Yang.', 'Avoid with deficient Lung Heat, cold diarrhea or middle-burner Cold; use cautiously when restless fetus is due to Cold. Traditionally counteracts Mu Dan Pi and Li Lu.', '3–9 g', 'Charred form emphasizes stopping bleeding.'],
    ['Huang Lian', 'Coptidis Rhizoma', 'Bitter', 'Cold', 'HT, LI, LV, ST', 'Clears Heat and dries Dampness, especially in the middle burner; treats Damp-Heat diarrhea or dysentery; clears Heart and Stomach Fire; clears Heat and stops bleeding; may be used topically for Heat.', 'Avoid in Yin deficiency, nausea or vomiting from Spleen-deficient Cold, or diarrhea from Spleen/Kidney deficiency; prolonged use may injure the Spleen and Stomach.', '1.5–9 g', '—'],
    ['Huang Bai', 'Phellodendri Cortex', 'Bitter', 'Cold', 'KD, UB', 'Clears Heat and dries Dampness, especially in the lower burner; drains Kidney deficiency Fire and treats lower-burner Damp-Heat.', 'Avoid in deficient Spleen conditions, particularly with diarrhea.', '3–12 g', '—'],
    ['Long Dan Cao', 'Gentianae Radix', 'Bitter', 'Cold', 'GB, LV, ST', 'Drains Damp-Heat from the Liver and Gallbladder channels, including the eyes and genital region; treats jaundice; drains and pacifies excessive Liver Fire.', 'Avoid with diarrhea due to Spleen and Stomach deficiency.', '3–9 g', '—'],
    ['Ku Shen', 'Sophorae Flavescentis Radix', 'Bitter', 'Cold', 'UB, HT, LV, LI, ST, SI', 'Clears Heat and dries Dampness; dispels Wind, kills parasites and stops itching, especially in the lower body; promotes urination.', 'Avoid in Spleen and Stomach deficient Cold; should not be injected; incompatible with Li Lu and traditionally antagonizes Tu Si Zi and Bei Mu.', '3–9 g; up to 30 g topical', 'May be used externally for itching and parasites.'],
];

$dampheatsimilarities = [
    ['Bitter-cold Damp-Heat clearing', 'Huang Qin · Huang Lian · Huang Bai · Long Dan Cao · Ku Shen', 'All are bitter and cold herbs that clear Heat and dry Dampness; selection depends mainly on location and associated symptoms.'],
    ['Three-burner emphasis', 'Huang Qin · Huang Lian · Huang Bai', 'Huang Qin emphasizes the upper burner, Huang Lian the middle burner and Huang Bai the lower burner.'],
    ['Fire and toxicity', 'Huang Qin · Huang Lian · Long Dan Cao', 'All clear excess Fire; Huang Qin emphasizes Lung and Shaoyang Heat, Huang Lian Heart and Stomach Fire, and Long Dan Cao Liver and Gallbladder Fire.'],
    ['Lower-burner Damp-Heat', 'Huang Bai · Long Dan Cao · Ku Shen', 'Treat lower-burner Damp-Heat; Long Dan Cao emphasizes Liver/Gallbladder and genital signs, while Ku Shen emphasizes itching and parasites.'],
    ['Urinary and genital involvement', 'Long Dan Cao · Ku Shen · Huang Bai', 'Address Damp-Heat below; Long Dan Cao is strong for genital and Liver/Gallbladder patterns, while Ku Shen promotes urination and stops itching.'],
    ['Skin itching and external use', 'Ku Shen · Huang Bai · Huang Lian', 'Clear Damp-Heat affecting the skin; Ku Shen is most specific for parasites and itching, while Huang Lian and Huang Bai emphasize Heat and toxicity.'],
];

$toxicheatherbs = [
    ['Jin Yin Hua', 'Lonicerae Flos', 'Sweet', 'Cold', 'LI, LU, ST', 'Clears Heat and relieves Fire toxicity; disperses externally contracted Wind-Heat; clears Damp-Heat from the lower burner.', 'Avoid in diarrhea from Spleen and Stomach deficient Cold, Qi-deficient sores with clear exudate, or Yin-type ulcers.', '6–20 g', 'Use smaller doses for Wind-Heat and larger doses for toxic Heat.'],
    ['Lian Qiao', 'Forsythiae Fructus', 'Bitter, slightly acrid', 'Slightly cold', 'HT, LU, GB', 'Clears Heat and relieves Fire toxicity; disperses Wind-Heat; reduces abscesses and dissipates clumps; clears Heart Heat.', 'Avoid in diarrhea from Spleen and Stomach deficient Cold, ulcerated carbuncles, or cold Yin-type ulcers.', '6–15 g', '—'],
    ['She Gan', 'Belamcandae Rhizoma', 'Bitter', 'Cold', 'LU', 'Clears Heat and relieves toxicity; benefits the throat; transforms phlegm and clears the Lung.', 'Avoid during pregnancy and use cautiously with diarrhea due to Spleen deficiency.', '3–9 g', '—'],
    ['Ban Lan Gen', 'Isatidis Radix', 'Bitter', 'Cold', 'HT, LU, ST', 'Clears Heat and relieves toxicity; cools the Blood; benefits the throat; helps prevent epidemic febrile disease.', 'Use cautiously in weak patients or when Fire toxin is absent.', '9–15 g', '—'],
    ['Shan Dou Gen', 'Sophorae Tonkinensis Radix', 'Bitter', 'Cold', 'LU, LI', 'Clears Heat and relieves toxicity; strongly benefits a swollen, painful throat; clears Lung Heat and cough.', 'Avoid with diarrhea due to Spleen-deficient Cold; use carefully because excessive doses may cause adverse effects.', '3–9 g', '—'],
    ['Tu Fu Ling', 'Smilacis Glabrae Rhizoma', 'Sweet, bland', 'Neutral', 'LV, ST', 'Relieves toxicity and Dampness; benefits the joints; clears Damp-Heat from the skin; traditionally treats syphilitic lesions.', 'Use cautiously with Liver or Kidney Yin deficiency; traditionally should not be taken with tea.', '15–60 g', 'Do not combine with tea.'],
    ['Pu Gong Ying', 'Taraxaci Herba', 'Bitter, sweet', 'Cold', 'LV, ST', 'Clears Heat and relieves toxicity; reduces abscesses, especially breast abscess and mastitis; promotes lactation; clears Liver Heat from the eyes; treats painful urinary dribbling.', 'Large doses may cause mild diarrhea.', '9–30 g', '—'],
    ['Bai Jiang Cao', 'Patriniae Herba', 'Acrid, bitter', 'Slightly cold', 'LI, LV, ST', 'Clears Heat and relieves toxicity; reduces abscesses; expels pus from intestinal abscesses; dispels Blood stasis and relieves pain.', 'Use cautiously with Spleen and Stomach deficiency; excessive doses may cause nausea or dizziness.', '6–15 g', '—'],
    ['He Ye', 'Nelumbinis Folium', 'Bitter, slightly sweet', 'Neutral', 'HT, LV, SP', 'Clears Summer-Heat; raises and clears Spleen Yang to treat diarrhea; stops bleeding.', 'Avoid when bleeding is due to deficiency or Cold; traditionally counteracts Fu Ling.', '3–9 g', '—'],
];

$toxicheatsimilarities = [
    ['Core Fire-toxin clearing', 'Jin Yin Hua · Lian Qiao · Ban Lan Gen · Pu Gong Ying', 'All clear Heat and toxicity; Jin Yin Hua and Lian Qiao also disperse Wind-Heat, Ban Lan Gen emphasizes epidemic and throat disorders, and Pu Gong Ying emphasizes abscesses.'],
    ['Externally contracted Wind-Heat', 'Jin Yin Hua · Lian Qiao', 'A classic paired comparison for early warm febrile disease; both disperse Wind-Heat while clearing toxicity.'],
    ['Painful or swollen throat', 'She Gan · Ban Lan Gen · Shan Dou Gen', 'All benefit the throat; She Gan adds phlegm transformation, Ban Lan Gen cools Blood and addresses epidemics, and Shan Dou Gen is especially strong for severe throat swelling.'],
    ['Abscesses and suppuration', 'Lian Qiao · Pu Gong Ying · Bai Jiang Cao', 'Lian Qiao dissipates clumps, Pu Gong Ying emphasizes breast abscess and lactation, and Bai Jiang Cao emphasizes intestinal abscess with pus and Blood stasis.'],
    ['Damp-Heat and toxicity', 'Jin Yin Hua · Tu Fu Ling · Pu Gong Ying', 'Treat toxicity complicated by Dampness; Jin Yin Hua emphasizes the lower burner, Tu Fu Ling skin and joints, and Pu Gong Ying urinary or breast involvement.'],
    ['Skin lesions and toxicity', 'Tu Fu Ling · Jin Yin Hua · Pu Gong Ying', 'Clear toxic skin manifestations with different emphases on Dampness, sores, swelling and abscess formation.'],
    ['Distinct supportive actions', 'He Ye · Bai Jiang Cao · Pu Gong Ying', 'He Ye uniquely clears Summer-Heat and raises Spleen Yang; Bai Jiang Cao moves Blood and relieves pain; Pu Gong Ying promotes lactation and urination.'],
];

$deficientheatherbs = [
    ['Qing Hao', 'Artemisiae Annuae Herba', 'Bitter', 'Cold', 'KD, LV, GB', 'Clears deficient Heat and cools the Blood; clears Summer-Heat; stops bleeding; checks malarial disorders and relieves Heat.', 'Avoid in postpartum women with Blood deficiency and in Spleen or Stomach deficient Cold; traditionally should not be used with Sheng Di or Dang Gui.', '6–12 g', 'Do not subject to prolonged cooking.'],
    ['Di Gu Pi', 'Lycii Cortex', 'Sweet, bland', 'Cold', 'LU, LV, KD', 'Clears deficient Heat and cools the Blood; reduces steaming from Kidney Yin deficiency with sweating; clears Lung Heat and stops cough; drains floating Fire from the Kidney channel, including toothache.', 'Avoid with loose stools from Spleen and Stomach deficient Cold and during external disorders.', '6–15 g', '—'],
    ['Bai Wei', 'Cynanchi Atrati Radix', 'Bitter, salty', 'Cold', 'LU, ST, KD', 'Clears deficient Heat and cools the Blood; promotes urination; relieves toxicity and reduces swelling.', 'Traditionally antagonizes Huang Qi, Da Huang, Da Ji, Gan Jiang, Da Zao and Shan Zhu Yu.', '3–12 g', '—'],
];

$deficientheatsimilarities = [
    ['Clear deficient Heat', 'Qing Hao · Di Gu Pi · Bai Wei', 'All clear deficient Heat and cool the Blood, but differ in their secondary emphasis on Summer-Heat, Lung Heat, urination or toxicity.'],
    ['Yin-deficiency steaming', 'Qing Hao · Di Gu Pi', 'Both treat lingering deficiency Heat; Di Gu Pi is especially associated with steaming from Kidney Yin deficiency accompanied by sweating.'],
    ['Cool the Blood', 'Qing Hao · Di Gu Pi · Bai Wei', 'All cool Blood-level Heat; Qing Hao additionally stops bleeding, Di Gu Pi treats steaming Heat, and Bai Wei promotes urination.'],
    ['Febrile, Summer-Heat and malarial patterns', 'Qing Hao', 'Qing Hao is the distinctive choice when deficient Heat overlaps with Summer-Heat or malarial-type febrile disorders.'],
    ['Lung Heat versus urinary toxicity', 'Di Gu Pi · Bai Wei', 'Di Gu Pi emphasizes Lung Heat with cough, while Bai Wei emphasizes urination, toxicity and swelling.'],
];

$purgativeherbs = [
    ['Da Huang', 'Rhei Radix', 'Bitter', 'Cold', 'HT, LI, LV, ST', 'Drains Heat and moves stool to purge accumulation; clears Damp-Heat in jaundice or acute hot dysenteric disorders; clears Blood Heat and stops bleeding when charred; invigorates Blood and dispels stasis when wine-treated; clears toxic Heat.', 'Avoid with exterior conditions, Qi or Blood deficiency, Spleen and Stomach deficient Cold, pregnancy or menstruation; use cautiously in nursing mothers because it may enter breast milk.', '3–15 g', 'Cooking longer than 10 minutes reduces the purgative effect.'],
    ['Mang Xiao', 'Natrii Sulfas', 'Acrid, bitter, salty', 'Very cold', 'ST, LI', 'Purges accumulation; moistens dryness and softens hardness; treats excess Heat in the Stomach and Large Intestine; clears Heat and reduces swelling when used externally.', 'Avoid without excess Heat, in elderly or debilitated patients, Spleen deficiency, pregnancy, the postpartum period or menstruation.', '6–18 g', 'Dissolve into the strained decoction.'],
    ['Lu Hui', 'Aloe', 'Bitter', 'Cold', 'LI, LV, ST', 'Purges accumulation; clears Heat and cools the Liver; kills parasites and strengthens the Stomach.', 'Avoid during pregnancy or menstruation, with Spleen and Stomach deficient Cold, or with rectal bleeding.', '1.5–4.5 g', 'Usually taken as a pill or capsule.'],
];

$purgativesimilarities = [
    ['Strong Heat-purgation', 'Da Huang · Mang Xiao · Lu Hui', 'All drain Heat and move stool, but their secondary actions determine the best choice.'],
    ['Hot, dry or hard stool', 'Da Huang · Mang Xiao', 'Da Huang strongly purges accumulation, while Mang Xiao moistens dryness and softens hardness; they are often paired for excess Heat constipation.'],
    ['Liver and toxic Heat', 'Da Huang · Lu Hui', 'Both enter the Liver; Da Huang adds Blood-moving and toxin-clearing actions, while Lu Hui more specifically cools the Liver and kills parasites.'],
    ['Blood Heat and Blood stasis', 'Da Huang', 'Da Huang uniquely stops bleeding when charred and invigorates Blood when wine-treated.'],
    ['External swelling', 'Mang Xiao', 'Mang Xiao may be used externally to clear Heat and reduce swelling in addition to its internal purgative action.'],
    ['Cold or deficient patients', 'Da Huang · Mang Xiao · Lu Hui', 'All are strong, cold downward-draining substances and require caution in deficiency, Cold patterns and pregnancy.'],
];

$laxativeherbs = [
    ['Huo Ma Ren', 'Cannabis Semen', 'Sweet', 'Neutral', 'LI, SP, ST', 'Nourishes and moistens the Intestines; nourishes Yin; clears Heat and promotes the healing of sores.', 'Excessive or long-term use may result in vaginal discharge or spermatorrhea.', '9–15 g', 'Crush before cooking.'],
];

$laxativesimilarities = [
    ['Gentle moistening laxative', 'Huo Ma Ren', 'Moistens the Intestines and gently moves stool without the strong cold purgative action of the Purgatives.'],
    ['Dryness versus excess Heat', 'Huo Ma Ren · Da Huang · Mang Xiao', 'Choose Huo Ma Ren for dryness or deficiency, Da Huang for strong excess accumulation, and Mang Xiao for hard, dry stool from excess Heat.'],
    ['Yin and intestinal moisture', 'Huo Ma Ren', 'Its nourishing quality makes it especially useful when constipation accompanies fluid or Yin insufficiency.'],
    ['Additional healing action', 'Huo Ma Ren', 'Beyond moistening the bowels, it clears Heat and supports the healing of sores.'],
];

$catharticherbs = [
    ['Da Ji', 'Euphorbiae Pekinensis Radix', 'Bitter, acrid', 'Cold, toxic', 'KD, LI, SP', 'Drains water downward and drives out congested fluids; reduces swelling and dissipates nodules.', 'Contraindicated in cold deficient edema and pregnancy; use cautiously in weak patients; incompatible with Gan Cao.', '1.5–3 g', '—'],
];

$catharticsimilarities = [
    ['Strong water-expelling cathartic', 'Da Ji', 'Drives congested fluids downward rather than primarily treating Heat constipation.'],
    ['Fluid accumulation versus stool accumulation', 'Da Ji · Da Huang · Mang Xiao', 'Use Da Ji for severe retained fluids and edema; use Da Huang or Mang Xiao when Heat and stool accumulation predominate.'],
    ['Swelling and nodules', 'Da Ji', 'Its downward-draining action is paired with an ability to reduce swelling and dissipate nodules.'],
    ['Toxicity and incompatibility', 'Da Ji', 'Because it is toxic and incompatible with Gan Cao, dosing and formula compatibility require particular care.'],
];

$diureticherbs = [
    ['Fu Ling', 'Poria', 'Sweet, bland', 'Neutral', 'HT, SP, KD, LU', 'Promotes urination, leaches Dampness and reduces edema; strengthens the Spleen; harmonizes the middle burner; transforms phlegm; quiets the Heart and calms the spirit.', 'Avoid with frequent, copious urination from deficient Cold; traditionally counteracts Di Yu, Qin Jiao and Bie Jia; avoid taking with vinegar.', '9–15 g', '—'],
    ['Yi Yi Ren', 'Coicis Semen', 'Sweet, bland', 'Slightly cold', 'LU, SP, ST, KD', 'Promotes urination and leaches Dampness; strengthens the Spleen; clears Heat and expels pus; relieves Wind-Damp painful obstruction.', 'Use cautiously during pregnancy.', '9–30 g', '—'],
    ['Zhu Ling', 'Polyporus', 'Sweet, bland', 'Slightly cold', 'SP, KD, UB', 'Promotes urination and leaches out Dampness, especially when water retention and edema predominate.', 'Avoid when Dampness is absent; prolonged use may injure Yin.', '6–12 g', '—'],
    ['Ze Xie', 'Alismatis Rhizoma', 'Sweet, bland', 'Cold', 'KD, UB', 'Promotes urination and leaches Dampness; drains Damp-Heat from the lower burner; settles ministerial Fire in the Kidney.', 'Avoid in spermatorrhea or vaginal discharge from Kidney Yang deficiency and in Damp-Cold patterns.', '4.5–9 g', '—'],
    ['Dong Gua Ren', 'Benincasae Semen', 'Sweet', 'Cold', 'LU, ST, LI, SI', 'Clears Heat and transforms phlegm; promotes urination; expels pus and supports the discharge of abscesses.', 'Use cautiously with Damp-Cold or loose stools.', '9–15 g', '—'],
    ['Han Fang Ji', 'Stephaniae Tetrandrae Radix', 'Bitter, acrid', 'Cold', 'UB, SP, KD', 'Promotes urination and reduces edema; dispels Wind-Damp and relieves pain from painful obstruction.', 'Avoid in Yin deficiency or when Dampness is absent; use cautiously with interior Cold.', '4.5–9 g', '—'],
    ['Yin Chen', 'Artemisiae Scopariae Herba', 'Bitter', 'Slightly cold', 'LV, SP, GB, ST', 'Clears Damp-Heat from the Liver and Gallbladder; promotes urination; is a principal herb for jaundice.', 'Avoid in deficiency jaundice with pale coloration, normal urine and signs that Damp-Heat is absent.', '9–30 g', '—'],
    ['Deng Xin Cao', 'Junci Medulla', 'Sweet, bland', 'Slightly cold', 'HT, LU, SI', 'Promotes urination and relieves painful urinary dribbling; clears Heart Heat and directs Fire downward through the Small Intestine.', 'Use cautiously in deficient Cold patterns.', '1.5–4.5 g', '—'],
    ['Mu Tong', 'Akebiae Caulis', 'Bitter', 'Slightly cold', 'UB, HT, SI', 'Promotes urination and relieves painful urinary dribbling; clears Heart Heat; unblocks Blood stasis and menstruation; promotes lactation.', 'Avoid during pregnancy and in deficiency without Damp-Heat; excessive use may damage Kidney function.', '3–6 g', 'Use only the correctly identified, non-aristolochic medicinal species.'],
    ['Dong Kui Zi', 'Malvae Semen', 'Sweet', 'Cold', 'UB, LI, SI', 'Promotes urination and relieves painful urinary dribbling; benefits the breasts and promotes lactation; moistens the Intestines.', 'Avoid with diarrhea from Spleen deficiency; use cautiously during pregnancy.', '3–9 g', '—'],
    ['Qu Mai', 'Dianthi Herba', 'Bitter', 'Cold', 'UB, HT, SI', 'Promotes urination and relieves painful urinary dribbling; breaks Blood stasis, unblocks menstruation and promotes lactation.', 'Avoid during pregnancy and in Spleen or Kidney deficiency; traditionally antagonizes Sang Piao Xiao.', '9–15 g', '—'],
    ['Bian Xu', 'Polygoni Avicularis Herba', 'Bitter', 'Slightly cold', 'LI, UB', 'Promotes urination and relieves painful urinary dribbling; expels parasites and stops itching.', 'Excessive dosage may drain essential Qi.', '9–15 g', '—'],
    ['Shi Wei', 'Pyrrosiae Folium', 'Bitter, sweet', 'Slightly cold', 'UB, LU', 'Promotes urination and relieves painful urinary dribbling; clears Heat and stops bleeding; clears Lung Heat, expels phlegm and stops cough.', 'Use cautiously when Damp-Heat is absent.', '6–9 g', '—'],
    ['Che Qian Zi', 'Plantaginis Semen', 'Sweet', 'Cold', 'UB, KD, LV, LU', 'Promotes urination and leaches Dampness; relieves painful urinary dribbling; solidifies stool in diarrhea; clears the eyes; clears Lung Heat and phlegm.', 'Avoid during pregnancy and use cautiously with exhausted Yang Qi, spermatorrhea from Kidney deficiency or severe exhaustion.', '9–15 g', 'Wrap in a porous bag before decocting.'],
    ['Hua Shi', 'Talcum', 'Sweet, bland', 'Cold', 'ST, UB', 'Promotes urination and relieves painful urinary dribbling; clears Summer-Heat; absorbs Dampness when used topically for eczema or sores.', 'Avoid with Spleen Qi deficiency, spermatorrhea, depleted fluids after warm-febrile disease or excessive urination; use cautiously during pregnancy.', '9–15 g', '—'],
    ['Di Fu Zi', 'Kochiae Fructus', 'Sweet, bitter', 'Cold', 'UB, KD', 'Promotes urination and clears Damp-Heat; expels Dampness and stops itching, especially in the genital region or skin.', 'Traditionally antagonizes Hai Piao Xiao.', '9–15 g', '—'],
    ['Bi Xie', 'Dioscoreae Hypoglaucae Rhizoma', 'Bitter', 'Neutral', 'UB, LV, ST', 'Separates the clear from the turbid in cloudy painful urinary disorders; dispels Wind-Damp and unblocks channels; clears Damp-Heat from the skin.', 'Avoid in Kidney Yin deficiency patterns.', '9–15 g', '—'],
];

$diureticsimilarities = [
    ['Edema and water retention', 'Fu Ling · Yi Yi Ren · Zhu Ling · Ze Xie · Han Fang Ji', 'All promote urination and reduce retained fluids; Fu Ling and Yi Yi Ren strengthen the Spleen, Zhu Ling emphasizes urination, Ze Xie drains lower-burner Heat, and Han Fang Ji treats Wind-Damp pain.'],
    ['Strengthen the Spleen while draining Dampness', 'Fu Ling · Yi Yi Ren', 'Both drain Dampness without strongly damaging the middle; Fu Ling also calms the spirit and transforms phlegm, while Yi Yi Ren clears Heat and expels pus.'],
    ['Damp-Heat jaundice', 'Yin Chen · Ze Xie', 'Yin Chen is the principal herb for Liver and Gallbladder Damp-Heat jaundice; Ze Xie supports lower-burner drainage when urinary Damp-Heat is present.'],
    ['Heart Fire through the Small Intestine', 'Deng Xin Cao · Mu Tong', 'Both clear Heart Heat and guide Fire downward through urination; Mu Tong is stronger for Blood stasis and lactation.'],
    ['Painful urinary dribbling', 'Deng Xin Cao · Mu Tong · Dong Kui Zi · Qu Mai · Bian Xu · Shi Wei · Che Qian Zi · Hua Shi', 'All address Lin syndrome, with selection based on Heart Fire, Blood stasis, bleeding, stones, diarrhea, Summer-Heat or constitutional deficiency.'],
    ['Blood stasis, menstruation and lactation', 'Mu Tong · Dong Kui Zi · Qu Mai', 'Mu Tong and Qu Mai unblock Blood and menstruation; all three may promote lactation, while Dong Kui Zi also moistens the Intestines.'],
    ['Lung, phlegm and cough involvement', 'Fu Ling · Dong Gua Ren · Shi Wei · Che Qian Zi', 'Fu Ling transforms phlegm, Dong Gua Ren clears Heat and expels pus, Shi Wei stops cough, and Che Qian Zi clears Lung Heat and phlegm.'],
    ['Skin Dampness and itching', 'Bian Xu · Hua Shi · Di Fu Zi · Bi Xie', 'Treat Damp-Heat affecting the skin; Bian Xu and Di Fu Zi emphasize itching, Hua Shi absorbs Dampness topically, and Bi Xie separates turbid Dampness.'],
    ['Cloudy urine and turbid Dampness', 'Bi Xie · Che Qian Zi · Ze Xie', 'Bi Xie is most specific for separating clear from turbid, while Che Qian Zi and Ze Xie promote urinary drainage and clear lower-burner Dampness.'],
];

$winddampherbs = [
    ['Du Huo', 'Angelicae Pubescentis Radix', 'Bitter, acrid', 'Warm', 'KD, UB', 'Dispels Wind-Cold-Damp, especially from the lower back and legs; relieves pain from Bi syndrome; releases the exterior; treats Shaoyin headache and toothache.', 'Avoid in Yin deficiency with Heat signs.', '3–9 g', '—'],
    ['Wei Ling Xian', 'Clematidis Radix', 'Acrid, salty', 'Warm', 'UB', 'Dispels Wind-Damp; unblocks the channels and relieves pain; softens and transforms fish bones; addresses focal distension and accumulation.', 'Avoid in Qi deficiency or Blood deficiency.', '6–9 g; 15–30 g for fish bones', '—'],
    ['Hai Tong Pi', 'Erythrinae Cortex', 'Bitter, acrid', 'Neutral', 'LV, SP, KD', 'Dispels Wind-Damp and unblocks the channels; promotes urination and reduces edema; treats itching skin lesions; may be used as a gargle for toothache from dental cavities.', 'Avoid in Blood deficiency.', '6–15 g', '—'],
    ['Qin Jiao', 'Gentianae Macrophyllae Radix', 'Bitter, acrid', 'Slightly cold', 'GB, LV, ST', 'Dispels Wind-Damp and relaxes the sinews; clears deficiency Heat; resolves Dampness and jaundice; moistens the Intestines and moves stool.', 'Avoid with frequent urination, chronic pain with emaciation, or Spleen deficiency with diarrhea.', '3–9 g', '—'],
    ['Mu Gua', 'Chaenomelis Fructus', 'Sour', 'Warm', 'LV, SP', 'Relaxes the sinews and relieves cramping; harmonizes the Stomach and transforms Dampness; reduces food retention.', 'Avoid during exterior disorders; excessive use may damage the teeth and bones.', '6–12 g', '—'],
    ['Sang Zhi', 'Mori Ramulus', 'Bitter', 'Neutral', 'LV', 'Dispels Wind-Damp, unblocks the channels and benefits the joints, especially the upper limbs; may reduce edema.', 'No specific caution noted in the guideline.', '9–15 g; up to 30 g', '—'],
    ['Xi Xian Cao', 'Siegesbeckiae Herba', 'Bitter', 'Cold', 'KD, LV', 'Dispels Wind-Damp and strengthens the sinews; clears Heat and calms the Liver; transforms Damp-Heat; traditionally used in hypertension patterns.', 'Avoid in Yin deficiency or Blood deficiency.', '9–15 g', '—'],
    ['Bai Hua She', 'Agkistrodon', 'Sweet, salty', 'Warm, toxic', 'LV', 'Powerfully dispels Wind from the skin and sinews; opens the channels; settles tremors and convulsions.', 'Avoid in Yin deficiency with Heat; use cautiously in Blood deficiency.', '3–9 g decoction; 1–1.5 g powder', 'Use only correctly identified and properly prepared medicinal material.'],
    ['Wu Jia Pi', 'Eleutherococci Cortex', 'Acrid, bitter', 'Warm', 'KD, LV', 'Dispels Wind-Damp; strengthens the Liver and Kidneys, sinews and bones; transforms Dampness and reduces swelling.', 'Use cautiously in Yin deficiency with Heat.', '4.5–9 g', '—'],
    ['Sang Ji Sheng', 'Taxilli Herba', 'Bitter, sweet', 'Neutral', 'KD, LV', 'Dispels Wind-Damp; strengthens the Liver and Kidneys, sinews and bones; nourishes Blood; calms the womb; benefits the skin; traditionally used in hypertension patterns.', 'No specific caution noted in the guideline.', '9–15 g', '—'],
];

$winddampsimilarities = [
    ['Wind-Damp Bi syndrome', 'Du Huo · Wei Ling Xian · Hai Tong Pi · Qin Jiao · Mu Gua · Sang Zhi · Xi Xian Cao · Bai Hua She · Wu Jia Pi · Sang Ji Sheng', 'All address Wind-Damp painful obstruction, with selection based on location, temperature, deficiency, edema and channel involvement.'],
    ['Lower body versus upper limbs', 'Du Huo · Sang Zhi', 'Du Huo emphasizes the lower back and legs, while Sang Zhi is especially useful for the shoulders and upper limbs.'],
    ['Unblock channels and relax sinews', 'Wei Ling Xian · Hai Tong Pi · Qin Jiao · Mu Gua · Sang Zhi', 'Wei Ling Xian strongly unblocks pain, Hai Tong Pi adds edema and skin actions, Qin Jiao clears Heat, Mu Gua treats cramping, and Sang Zhi benefits joints.'],
    ['Wind-Damp with Heat', 'Qin Jiao · Xi Xian Cao', 'Both are cooling choices for Heat-type Bi syndrome; Qin Jiao also clears deficiency Heat, while Xi Xian Cao calms the Liver and strengthens sinews.'],
    ['Edema and Damp accumulation', 'Hai Tong Pi · Sang Zhi · Wu Jia Pi', 'All address Wind-Damp with swelling; Hai Tong Pi promotes urination, Sang Zhi benefits the joints, and Wu Jia Pi adds tonic support.'],
    ['Strengthen Liver, Kidney, sinews and bones', 'Wu Jia Pi · Sang Ji Sheng', 'Both treat chronic Wind-Damp with deficiency; Sang Ji Sheng additionally nourishes Blood and calms the womb.'],
    ['Severe Wind, skin and convulsions', 'Bai Hua She · Xi Xian Cao · Hai Tong Pi', 'Bai Hua She is strongest for entrenched Wind and convulsions, while Xi Xian Cao and Hai Tong Pi emphasize Heat, itching or skin involvement.'],
    ['Special indications', 'Du Huo · Wei Ling Xian · Hai Tong Pi · Sang Ji Sheng', 'Du Huo treats Shaoyin headache and toothache, Wei Ling Xian fish-bone obstruction, Hai Tong Pi dental-cavity pain, and Sang Ji Sheng deficiency with pregnancy support.'],
];

$hotphlegmherbs = [
    ['Qian Hu', 'Peucedani Radix', 'Bitter, acrid', 'Slightly cold', 'LU', 'Descends Lung Qi and expels phlegm; releases exterior Wind.', 'Traditionally counteracts Li Lu.', '3–9 g', '—'],
    ['Chuan Bei Mu', 'Fritillariae Cirrhosae Bulbus', 'Bitter, sweet', 'Slightly cold', 'HT, LU', 'Stops cough; moistens the Lung; transforms phlegm; treats Yin-deficiency dry cough with scant sputum that is difficult to expectorate; dissipates nodules.', 'Avoid in Spleen and Stomach deficiency; incompatible with Wu Tou; traditionally counteracts Qin Jiao.', '3–9 g; 1–1.5 g powder', 'Use 1–1.5 g when taken as powder.'],
    ['Zhe Bei Mu', 'Fritillariae Thunbergii Bulbus', 'Bitter', 'Cold', 'HT, LU', 'Clears Heat and transforms phlegm; stops productive cough from acute Lung Heat; dissipates nodules.', 'Avoid when phlegm is due to Damp-Cold; incompatible with Wu Tou.', '4.5–9 g', '—'],
    ['Gua Lou Pi', 'Trichosanthis Pericarpium', 'Sweet', 'Cold', 'LU, ST, LI', 'Clears Heat and transforms phlegm; expands the chest and relieves constraint.', 'Avoid in Damp-Phlegm caused by Spleen weakness; incompatible with Wu Tou.', '6–12 g', '—'],
    ['Gua Lou', 'Trichosanthis Fructus', 'Sweet', 'Cold', 'LI, LU, ST', 'Clears Heat and transforms phlegm; expands the chest; moistens the Intestines; dissipates nodules.', 'Avoid in Spleen and Stomach deficient Cold, Cold-Phlegm or Damp-Phlegm; incompatible with Wu Tou; traditionally antagonizes Gan Jiang and counteracts Niu Xi.', '9–21 g', '—'],
    ['Gua Lou Ren', 'Trichosanthis Semen', 'Sweet', 'Cold', 'LU, ST, LI', 'Clears Heat and transforms phlegm; moistens the Intestines; dissipates nodules; promotes healing of sores.', 'Avoid in Spleen and Stomach deficient Cold, diarrhea or patterns without Heat; incompatible with Wu Tou.', '9–15 g', '—'],
    ['Zhu Ru', 'Bambusae Caulis', 'Sweet', 'Slightly cold', 'LU, ST, GB', 'Clears Phlegm-Heat; clears Gallbladder and Stomach Heat; stops vomiting.', 'Avoid in nausea or vomiting from Stomach Cold or cold food stagnation.', '4.5–9 g', '—'],
    ['Hai Zao', 'Sargassum', 'Bitter, salty', 'Cold', 'KD, LV, LU, ST', 'Softens hardness and dissipates neck nodules; promotes urination and reduces edema; treats pain associated with hernial disorders.', 'Avoid in Spleen and Stomach deficient Cold; incompatible with Gan Cao.', '6–15 g', '—'],
];

$hotphlegmsimilarities = [
    ['Phlegm-Heat cough', 'Qian Hu · Chuan Bei Mu · Zhe Bei Mu · Gua Lou · Zhu Ru', 'All cool or transform Phlegm-Heat; Qian Hu descends Lung Qi, the Bei Mu herbs stop cough, Gua Lou opens the chest, and Zhu Ru stops vomiting.'],
    ['Dry versus productive cough', 'Chuan Bei Mu · Zhe Bei Mu', 'Chuan Bei Mu emphasizes dry deficiency cough with scant sputum; Zhe Bei Mu emphasizes acute Lung Heat with productive cough.'],
    ['Three parts of Gua Lou', 'Gua Lou Pi · Gua Lou · Gua Lou Ren', 'The peel emphasizes chest expansion, the whole fruit combines chest and intestinal actions, and the seed emphasizes moistening the Intestines.'],
    ['Chest constraint and nodules', 'Gua Lou Pi · Gua Lou · Chuan Bei Mu · Zhe Bei Mu', 'Gua Lou Pi and Gua Lou open the chest, while the Bei Mu herbs dissipate nodules and transform phlegm.'],
    ['Moisten the Intestines', 'Gua Lou · Gua Lou Ren', 'Both moisten the Intestines; Gua Lou Ren is the seed and places greater emphasis on bowel dryness.'],
    ['Gallbladder and Stomach Phlegm-Heat', 'Zhu Ru', 'Zhu Ru is the distinctive choice for Phlegm-Heat with irritability, Gallbladder Fire or nausea and vomiting.'],
    ['Hard nodules and edema', 'Hai Zao', 'Hai Zao softens hardness, dissipates neck nodules and promotes urination to reduce edema.'],
];

$coldphlegmherbs = [
    ['Tian Nan Xing', 'Arisaematis Rhizoma Preparatum', 'Bitter, acrid', 'Warm, toxic', 'LV, LU, SP', 'Dries Dampness and transforms phlegm; disperses Wind-Phlegm from the channels and stops spasms; reduces swelling and relieves pain when used topically.', 'Avoid in Yin deficiency, pregnancy, dry phlegm or hot and dry Lung patterns; traditionally counteracts Fu Zi, Gan Jiang and Sheng Jiang.', '4.5–9 g decoction; 0.3–1 g powder', 'Use only the prepared form; Sheng Jiang and Ming Fan reduce its toxicity.'],
    ['Ban Xia', 'Pinelliae Rhizoma Preparatum', 'Acrid', 'Warm, toxic', 'LU, SP, ST', 'Dries Dampness and transforms phlegm; directs rebellious Stomach Qi downward and stops vomiting; dissipates nodules and reduces swelling.', 'Avoid in bleeding, Yin-deficiency cough, depleted fluids or Heat patterns; incompatible with Wu Tou.', '3–9 g', 'Use only the properly prepared form internally.'],
    ['Xuan Fu Hua', 'Inulae Flos', 'Bitter, acrid, salty', 'Slightly warm', 'LV, LU, ST, SP', 'Directs Lung Qi downward; transforms phlegm; treats vomiting, hiccup and belching.', 'Use sparingly in deficient conditions.', '3–9 g', 'Wrap in cheesecloth before decocting.'],
    ['Bai Qian', 'Cynanchi Stauntonii Radix', 'Acrid, sweet', 'Slightly warm', 'LU', 'Descends Lung Qi and expels phlegm; treats Lung Qi blockage and stagnation, especially cough with phlegm lodged in the throat.', 'Avoid in cough from Qi deficiency or Kidney failure to grasp Qi.', '3–9 g', '—'],
    ['Bai Jie Zi', 'Sinapis Semen', 'Acrid', 'Warm', 'LU', 'Warms the Lung, regulates Qi and expels phlegm; opens the channels and stops pain; dissipates nodules and reduces swelling.', 'Avoid in cough from Lung deficiency or Yin-deficiency Heat; do not use topically on sensitive skin.', '3–9 g', 'Do not decoct for a long period.'],
    ['Jie Geng', 'Platycodi Radix', 'Bitter, acrid', 'Neutral', 'LU', 'Opens and diffuses Lung Qi; benefits the throat in both Cold-Phlegm and Phlegm-Heat; promotes discharge of pus; directs the effects of other herbs upward.', 'Avoid in hemoptysis; traditionally counteracts Long Dan Cao and Long Yan Rou.', '3–9 g', '—'],
];

$coldphlegmsimilarities = [
    ['Warm and transform Cold-Phlegm', 'Tian Nan Xing · Ban Xia · Xuan Fu Hua · Bai Qian · Bai Jie Zi · Jie Geng', 'All transform phlegm, but differ in emphasis on Dampness, rebellious Qi, Lung Qi descent, channel obstruction or throat involvement.'],
    ['Dry Dampness and transform phlegm', 'Tian Nan Xing · Ban Xia', 'Both strongly dry Dampness; Tian Nan Xing emphasizes Wind-Phlegm and spasms, while Ban Xia emphasizes Stomach Qi and vomiting.'],
    ['Rebellious Qi and vomiting', 'Ban Xia · Xuan Fu Hua', 'Ban Xia descends rebellious Stomach Qi, while Xuan Fu Hua descends Lung Qi and treats vomiting, hiccup and belching.'],
    ['Descend Lung Qi and stop cough', 'Xuan Fu Hua · Bai Qian', 'Both descend Lung Qi; Xuan Fu Hua is useful when vomiting accompanies phlegm, while Bai Qian emphasizes cough with throat phlegm.'],
    ['Nodules and swelling', 'Tian Nan Xing · Ban Xia · Bai Jie Zi', 'All dissipate nodules or swelling; Bai Jie Zi also opens channels and stops pain, while Tian Nan Xing may be used topically.'],
    ['Channel obstruction and pain', 'Tian Nan Xing · Bai Jie Zi', 'Tian Nan Xing addresses Wind-Phlegm with spasms; Bai Jie Zi warms the Lung and opens channels to stop pain.'],
    ['Open the Lung and benefit the throat', 'Bai Qian · Jie Geng', 'Bai Qian descends Lung Qi and clears lodged phlegm; Jie Geng diffuses Lung Qi, benefits the throat and guides other herbs upward.'],
    ['Toxicity and prepared forms', 'Tian Nan Xing · Ban Xia · Bai Jie Zi', 'Tian Nan Xing and Ban Xia require properly prepared forms; Bai Jie Zi requires care with sensitive skin and prolonged decoction.'],
];


$coughherbs = [
    ['Xing Ren', 'Armeniacae Semen', 'Bitter', 'Slightly warm, slightly toxic', 'LU, LI', 'Descends Lung Qi, stops cough and wheezing; broadly used for many types of cough, especially externally contracted dry cough; moistens the Intestines.', 'Use cautiously with diarrhea and in infants. Traditionally antagonizes Huang Qi, Ge Gen and Huang Qin.', '3–9 g', 'Crush and add near the end of decoction.'],
    ['Zi Su Zi', 'Perillae Fructus', 'Acrid', 'Warm', 'LI, LU', 'Descends Lung Qi, stops cough and wheezing; transforms phlegm; relieves a stifling sensation in the chest; moistens the Intestines.', 'Avoid in chronic diarrhea.', '3–9 g', 'Usually grind before use.'],
    ['Zi Wan', 'Asteris Radix', 'Acrid, bitter', 'Slightly warm', 'LU', 'Moistens the Lung; transforms phlegm; treats many types of cough and wheezing without being drying.', 'Use cautiously in cough from Yin deficiency with Heat or cough from excessive Heat; prolonged use or large doses are not recommended. Traditionally counteracts Yin Chen Hao.', '5–9 g', '—'],
    ['Kuan Dong Hua', 'Farfarae Flos', 'Acrid', 'Warm', 'LU', 'Moistens the Lung; stops cough and wheezing in many patterns without being drying.', 'Avoid in Heat-type cough. Traditionally antagonizes Xuan Shen and counteracts Bei Mu, Xin Yi Hua, Ma Huang, Huang Qin, Huang Lian and Huang Qi.', '4.5–9 g', '—'],
    ['Bai Bu', 'Stemonae Radix', 'Sweet, bitter', 'Slightly warm', 'LU', 'Moistens the Lung and stops cough; expels parasites and kills lice when used externally.', 'Avoid in Spleen deficiency or loose stools.', '3–9 g; 30–60 g for pinworms', '—'],
    ['Pi Pa Ye', 'Eriobotryae Folium', 'Bitter', 'Neutral', 'LU, ST', 'Clears Lung Heat and transforms phlegm; descends rebellious Stomach Qi and stops nausea or vomiting from Stomach Heat.', 'Avoid in vomiting from Stomach Cold or cough from externally contracted Cold in the Lung.', '6–15 g', '—'],
    ['Sang Bai Pi', 'Mori Cortex', 'Sweet', 'Cold', 'LU', 'Clears Lung Heat; stops cough and wheezing; promotes urination and reduces edema; traditionally used in hypertension patterns.', 'Avoid in Lung deficiency, excessive urination or cough from Wind-Cold.', '6–15 g', '—'],
];

$coughsimilarities = [
    ['Descend Lung Qi and stop wheezing', 'Xing Ren · Zi Su Zi · Pi Pa Ye', 'All descend rebellious Qi; Xing Ren is broad for cough and wheezing, Zi Su Zi adds phlegm and chest constraint, and Pi Pa Ye adds Stomach Heat vomiting.'],
    ['Moistening cough herbs', 'Zi Wan · Kuan Dong Hua · Bai Bu', 'All moisten the Lung without being strongly drying; select by phlegm, Heat and parasite involvement.'],
    ['Transform phlegm', 'Zi Su Zi · Zi Wan · Pi Pa Ye', 'Zi Su Zi transforms phlegm with chest fullness, Zi Wan treats varied cough, and Pi Pa Ye clears Lung Heat.'],
    ['Hot versus cold cough', 'Pi Pa Ye · Sang Bai Pi · Xing Ren · Zi Su Zi · Zi Wan · Kuan Dong Hua', 'Pi Pa Ye and Sang Bai Pi are cooling for Lung Heat; the warm herbs suit Cold or neutral patterns and require caution with Heat.'],
    ['Moisten the Intestines', 'Xing Ren · Zi Su Zi', 'Both descend Lung Qi and moisten the Intestines, useful when cough overlaps with dry stool.'],
    ['Nausea and vomiting', 'Pi Pa Ye', 'Pi Pa Ye uniquely descends rebellious Stomach Qi and stops nausea or vomiting from Stomach Heat.'],
    ['Parasites and external use', 'Bai Bu', 'Bai Bu uniquely expels parasites and kills lice externally; larger guideline doses are reserved for pinworms.'],
    ['Edema and hypertension patterns', 'Sang Bai Pi', 'Sang Bai Pi clears Lung Heat, promotes urination, reduces edema and is traditionally used in hypertension patterns.'],
];


$aromaticherbs = [
    ['Huo Xiang', 'Pogostemonis Herba', 'Acrid', 'Slightly warm', 'LU, SP, ST', 'Transforms Dampness; stops nausea and vomiting; releases the Exterior; treats Summer-Heat and may be used for dermatosis.', 'Use cautiously in Yin deficiency with Heat or Stomach Fire.', '3–9 g', 'Do not cook for more than 15 minutes.'],
    ['Cang Zhu', 'Atractylodis Rhizoma', 'Acrid, bitter', 'Warm', 'SP, ST', 'Strongly dries Dampness and strengthens the Spleen; clears Dampness from the Lower Jiao; improves vision; releases the Exterior and induces sweating for Wind-Damp pathogens.', 'Use cautiously with excessive sweating from Qi deficiency, Yin deficiency with Heat, or loose watery stools.', '3–9 g', '—'],
    ['Hou Po', 'Magnoliae Cortex', 'Bitter, acrid', 'Warm', 'LI, LU, SP, ST', 'Moves Qi and resolves stagnation; directs Qi downward; reduces phlegm and calms wheezing.', 'Use cautiously during pregnancy; traditionally antagonizes Ze Xie and Han Shui Shi.', '3–9 g', '—'],
    ['Bai Dou Kou', 'Amomi Fructus Rotundus', 'Acrid', 'Warm, aromatic', 'LU, SP, ST', 'Transforms Dampness; moves Qi and resolves stagnation; warms the Middle Jiao; descends rebellious Qi to stop nausea and vomiting.', 'Avoid in Yin or Blood deficiency and when Cold-Damp is absent.', '3–6 g', '—'],
    ['Sha Ren', 'Amomi Fructus', 'Acrid', 'Warm, aromatic', 'SP, ST', 'Transforms Dampness; moves Qi and resolves stagnation; warms the Middle Jiao; stops vomiting and diarrhea from Spleen Cold; calms the fetus and morning sickness; prevents stagnation from tonic herbs.', 'Avoid in Yin deficiency with Heat.', '3–6 g', 'Crush and add near the end.'],
    ['Cao Dou Kou', 'Alpiniae Katsumadai Semen', 'Acrid', 'Warm, aromatic', 'SP, ST', 'Dries Dampness; moves Qi and resolves stagnation; warms the Middle Jiao; descends rebellious Qi to relieve nausea and vomiting.', 'Avoid in Yin deficiency.', '3–6 g', 'Add near the end.'],
    ['Cao Guo', 'Tsaoko Fructus', 'Acrid', 'Warm', 'SP, ST', 'Strongly dries Dampness and disperses Cold; treats malarial disorders.', 'Avoid in Qi or Blood deficiency and when Cold-Damp is absent; overdose may cause vomiting.', '3–6 g', '—'],
];

$aromaticsimilarities = [
    ['Transform turbid Dampness in the Middle Jiao', 'Huo Xiang · Cang Zhu · Hou Po · Bai Dou Kou · Sha Ren · Cao Dou Kou · Cao Guo', 'All transform excess or turbid Dampness in the center; choose by dryness, Qi stagnation, Cold and accompanying symptoms.'],
    ['Release the Exterior with Dampness', 'Huo Xiang · Cang Zhu', 'Huo Xiang is gentler and especially useful for Summer-Heat with nausea, while Cang Zhu dries more strongly and promotes sweating.'],
    ['Strongly dry Dampness', 'Cang Zhu · Cao Dou Kou · Cao Guo', 'Cang Zhu strongly dries and strengthens the Spleen; Cao Dou Kou warms the center; Cao Guo emphasizes Cold-Damp and malarial disorders.'],
    ['Move Qi and resolve stagnation', 'Hou Po · Bai Dou Kou · Sha Ren · Cao Dou Kou', 'Hou Po, Bai Dou Kou, Sha Ren and Cao Dou Kou move Qi; Hou Po descends Qi and calms wheezing, while the aromatic seeds emphasize the Middle Jiao.'],
    ['Nausea and vomiting from Dampness', 'Huo Xiang · Bai Dou Kou · Sha Ren · Cao Dou Kou', 'Huo Xiang directly stops nausea and vomiting; Bai Dou Kou, Sha Ren and Cao Dou Kou warm the center and descend rebellious Qi.'],
    ['Warm the Middle Jiao', 'Bai Dou Kou · Sha Ren · Cao Dou Kou · Cao Guo', 'Bai Dou Kou, Sha Ren, Cao Dou Kou and Cao Guo address Cold-Damp in the center; Sha Ren is especially useful with diarrhea or pregnancy.'],
    ['Distinctive special indications', 'Cang Zhu · Sha Ren · Cao Guo', 'Cang Zhu improves vision, Sha Ren calms the fetus and Cao Guo is distinguished for malarial disorders.'],
];


$digestiveherbs = [
    ['Shan Zha', 'Crataegi Fructus', 'Sour, sweet', 'Slightly warm', 'LV, SP, ST', 'Reduces food stagnation, especially from meat or greasy foods; moves Blood stasis; charred form stops diarrhea; traditionally used in patterns involving hypertension, coronary artery disease or elevated cholesterol.', 'Avoid in Spleen and Stomach deficiency without food stagnation or with acid regurgitation.', '9–12 g', '—'],
    ['Mai Ya', 'Hordei Fructus Germinatus', 'Sweet', 'Neutral', 'LV, SP, ST', 'Reduces food stagnation from starchy foods; strengthens digestion; soothes Liver Qi; inhibits lactation at larger doses.', 'Avoid during nursing except at a very low dose; larger doses inhibit lactation.', '9–15 g; 30–60 g to inhibit lactation', '—'],
    ['Gu Ya', 'Setariae Fructus Germinatus', 'Sweet', 'Neutral', 'SP, ST', 'Reduces food stagnation from grains and starchy foods; strengthens the Spleen and Stomach.', 'No specific caution noted; avoid unnecessary use in pure deficiency without food retention.', '9–15 g', '—'],
    ['Shen Qu', 'Massa Medicata Fermentata', 'Sweet, acrid', 'Warm', 'SP, ST', 'Reduces food stagnation; harmonizes the Stomach; aids digestion and absorption of pills containing heavy minerals.', 'Use cautiously with Stomach Fire and during pregnancy.', '6–15 g', '—'],
    ['Ji Nei Jin', 'Gigeriae Galli Endothelium', 'Sweet', 'Neutral', 'UB, SI, SP, ST', 'Strongly reduces many types of food retention and improves Spleen transformation and transportation; secures Essence and stops enuresis; transforms urinary or biliary stones.', 'Avoid in Spleen deficiency without food retention.', '3–9 g; 1.5–3 g powder', 'More effective when taken as powder.'],
    ['Lai Fu Zi', 'Raphani Semen', 'Acrid, sweet', 'Neutral', 'LU, SP, ST', 'Reduces food accumulation with fullness and distension; descends Lung Qi and transforms phlegm for cough and wheezing.', 'Avoid in Qi deficiency; traditionally antagonizes Ren Shen.', '4.5–9 g', 'Crush before use.'],
];

$digestivesimilarities = [
    ['Reduce food stagnation', 'Shan Zha · Mai Ya · Gu Ya · Shen Qu · Ji Nei Jin · Lai Fu Zi', 'All six reduce food retention, but differ by food type, strength and accompanying Qi, Blood, phlegm or urinary involvement.'],
    ['Meat and greasy foods', 'Shan Zha', 'Shan Zha is the distinctive choice for meat or greasy food stagnation and also moves Blood stasis.'],
    ['Grains and starchy foods', 'Mai Ya · Gu Ya', 'Mai Ya and Gu Ya reduce grain and starch retention; Mai Ya also soothes Liver Qi and can inhibit lactation, while Gu Ya gently strengthens the Spleen and Stomach.'],
    ['Minerals and difficult pills', 'Shen Qu', 'Shen Qu harmonizes the Stomach and is traditionally added to formulas with heavy mineral ingredients to aid digestion and absorption.'],
    ['Broad and strong food retention', 'Ji Nei Jin', 'Ji Nei Jin strongly addresses varied food retention and additionally treats enuresis and urinary or biliary stones.'],
    ['Fullness, distension, phlegm and cough', 'Lai Fu Zi', 'Lai Fu Zi is preferred when food stagnation combines with Qi constraint, abdominal distension, phlegm, cough or wheezing.'],
    ['Distinctive secondary actions', 'Shan Zha · Mai Ya · Ji Nei Jin · Lai Fu Zi', 'Shan Zha moves Blood, Mai Ya affects lactation, Ji Nei Jin treats stones and enuresis, and Lai Fu Zi descends Lung Qi.'],
];


$regulateqiherbs = [
    ['Chen Pi', 'Citri Pericarpium', 'Acrid, bitter', 'Warm, aromatic', 'LU, SP, ST', 'Regulates the Middle Jiao; relieves diaphragm fullness, nausea and vomiting; dries Dampness and transforms phlegm; prevents stagnation from tonic herbs.', 'Avoid in dry cough from Qi or Yin deficiency, Heat-phlegm with a red tongue, dry cough from excess Heat, or hemoptysis.', '3–9 g', '—'],
    ['Qing Pi', 'Citri Viride Pericarpium', 'Bitter, acrid', 'Warm', 'GB, LV, ST', 'Breaks up constrained Liver Qi; dissipates clumps and stagnation; treats breast masses associated with Dampness or phlegm.', 'Avoid in Qi deficiency.', '3–9 g', '—'],
    ['Zhi Shi', 'Aurantii Fructus Immaturus', 'Bitter, acrid', 'Slightly cold', 'LI, SP, ST', 'Strongly directs Qi downward; moves stool; transforms phlegm and relieves focal distension; may support formulas for Middle Jiao Qi sinking.', 'Avoid during pregnancy, with weak upright Qi, or with a cold deficient Stomach.', '3–9 g', '—'],
    ['Zhi Ke', 'Aurantii Fructus', 'Bitter, acrid', 'Slightly cold', 'SP, ST', 'Gently regulates Qi and relieves distension; milder than Zhi Shi and more suitable for weak patients.', 'Use cautiously during pregnancy.', '3–9 g', '—'],
    ['Da Fu Pi', 'Arecae Pericarpium', 'Acrid', 'Slightly warm', 'LI, SI, SP, ST', 'Directs Qi downward and reduces stagnation; promotes urination and moves Dampness.', 'Avoid in Qi deficiency.', '4.5–9 g', '—'],
    ['Xiang Fu', 'Cyperi Rhizoma', 'Acrid, slightly bitter, slightly sweet', 'Neutral', 'LV, SJ, GB', 'Regulates Liver Qi, menstruation and pain; soothes emotional constraint and gynecologic Qi stagnation.', 'Avoid in Qi deficiency without stagnation or in Yin or Blood deficiency with Heat.', '6–12 g', '—'],
    ['Chuan Lian Zi', 'Toosendan Fructus', 'Bitter', 'Cold, slightly toxic', 'UB, LV, SI, ST', 'Regulates Liver Qi and relieves pain; clears Heat, dries Dampness and kills parasites.', 'Avoid in Spleen and Stomach deficiency with Cold.', '4.5–9 g', '—'],
    ['Wu Yao', 'Linderae Radix', 'Acrid', 'Warm', 'UB, KD, LU, SP', 'Dispels Cold, regulates Qi and relieves pain; warms the Kidney and improves frequent urination.', 'Avoid in Qi deficiency or Interior Heat.', '3–9 g', '—'],
    ['Chen Xiang', 'Aquilariae Lignum', 'Acrid, bitter', 'Warm, aromatic', 'KD, SP, ST', 'Directs rebellious Qi downward and regulates the Middle Jiao; warms the Kidney and assists asthma or wheezing.', 'Avoid in prolapse from Qi deficiency or Yin deficiency with Heat.', '1–1.5 g powder', 'Add near the end of the decoction.'],
    ['Mu Xiang', 'Aucklandiae Radix', 'Acrid, bitter', 'Warm', 'GB, LI, SP, ST, SJ', 'Regulates stagnant Qi in the Intestines, relieves pain and distension, and prevents cloying tonic herbs from obstructing Spleen transformation and transportation.', 'Avoid in Yin deficiency or depleted fluids.', '3–9 g', 'Add during the final 5 minutes.'],
];

$regulateqisimilarities = [
    ['Regulate Qi and remove stagnation', 'Chen Pi · Qing Pi · Zhi Shi · Zhi Ke · Da Fu Pi · Xiang Fu · Chuan Lian Zi · Wu Yao · Chen Xiang · Mu Xiang', 'All ten regulate Qi, but their clinical emphasis differs by organ, temperature, strength, pain, phlegm, Dampness and accompanying deficiency.'],
    ['Same citrus fruit: mature and immature peel', 'Chen Pi · Qing Pi', 'Chen Pi is the mature peel and gently harmonizes the Middle while transforming phlegm; Qing Pi is the immature peel and more strongly breaks constrained Liver Qi.'],
    ['Bitter orange: immature and mature fruit', 'Zhi Shi · Zhi Ke', 'Zhi Shi is immature, stronger and more downward-directing; Zhi Ke is mature, gentler and more appropriate for weaker patients.'],
    ['Break severe Qi accumulation', 'Qing Pi · Zhi Shi', 'Qing Pi and Zhi Shi act more forcefully on clumps, focal distension and entrenched stagnation than the gentler Qi regulators.'],
    ['Soothe Liver Qi and relieve pain', 'Qing Pi · Xiang Fu · Chuan Lian Zi', 'Qing Pi, Xiang Fu and Chuan Lian Zi regulate Liver Qi; Xiang Fu emphasizes menstruation, while Chuan Lian Zi clears Heat and kills parasites.'],
    ['Direct Qi downward', 'Chen Pi · Zhi Shi · Da Fu Pi · Chen Xiang', 'Zhi Shi and Da Fu Pi reduce abdominal stagnation; Chen Xiang descends rebellious Qi and warms the Kidney; Chen Pi also addresses nausea and vomiting.'],
    ['Warm the Kidney and Lower Jiao', 'Wu Yao · Chen Xiang', 'Wu Yao is useful for Cold pain and frequent urination; Chen Xiang warms the Kidney while descending rebellious Qi for asthma or wheezing.'],
    ['Prevent stagnation from tonics', 'Chen Pi · Mu Xiang', 'Chen Pi and Mu Xiang prevent rich tonic herbs from obstructing digestion; Mu Xiang especially regulates intestinal Qi.'],
];


$bleedingherbs = [
    ['Pu Huang', 'Typhae Pollen', 'Sweet, acrid', 'Neutral', 'LV, HT, SP', 'Astringes Blood and stops bleeding while moving Blood; promotes urination.', 'Avoid during pregnancy.', '3–9 g', 'Wrap in cheesecloth before decocting.'],
    ['San Qi', 'Notoginseng Radix', 'Sweet, slightly bitter', 'Warm', 'LV, ST', 'Stops bleeding without retaining stasis; reduces swelling and relieves pain, especially after trauma.', 'Avoid during pregnancy; use cautiously in Blood or Yin deficiency.', '1–1.5 g powder; 3–6 g decoction', '—'],
    ['Qian Cao Gen', 'Rubiae Radix', 'Bitter', 'Cold', 'HT, LV', 'Cools Blood, stops bleeding and moves Blood stasis; especially useful for trauma.', 'Avoid in Spleen and Stomach deficiency with Cold.', '9–15 g', '—'],
    ['Xian He Cao', 'Agrimoniae Herba', 'Bitter, astringent', 'Neutral', 'LU, LV, SP', 'Astringes Blood and stops bleeding; alleviates diarrhea and dysentery; eliminates parasites associated with trichomonas vaginitis or tapeworm.', 'May cause nausea or vomiting.', '6–12 g', '—'],
    ['Bai Ji', 'Bletillae Rhizoma', 'Bitter, sweet', 'Cool', 'LU, ST, LV', 'Astringes Blood and stops bleeding, especially from the Lung or Stomach; reduces swelling and generates flesh when used topically.', 'Avoid in early Lung abscess, hemoptysis from an Exterior condition, or excessive Lung and Stomach Heat; incompatible with Wu Tou and Fu Zi.', '3–15 g; up to 30 g; 1.5–3 g powder', '—'],
    ['Huai Hua Mi', 'Sophorae Flos', 'Bitter', 'Cool', 'LV, LI', 'Cools Blood and stops Large Intestine bleeding, especially hemorrhoids or dysentery; clears Liver Heat.', 'Avoid in Spleen and Stomach deficiency with Cold.', '4.5–9 g', '—'],
    ['Di Yu', 'Sanguisorbae Radix', 'Bitter, sour', 'Slightly cold', 'LV, LI, ST', 'Cools Blood and stops bleeding; clears Heat and generates flesh topically, especially for burns.', 'Avoid in cold deficient patterns.', '9–15 g', '—'],
    ['Ce Bai Ye', 'Platycladi Cacumen', 'Bitter, astringent', 'Slightly cold', 'LU, LV, LI', 'Cools Blood and stops bleeding; stops cough and transforms phlegm.', 'Long-term or large doses may cause nausea, vomiting or dizziness.', '6–15 g', '—'],
    ['Bai Mao Gen', 'Imperatae Rhizoma', 'Sweet', 'Cold', 'LU, ST, SI, UB', 'Cools Blood and stops bleeding; clears Heat from the Lung and Stomach and promotes urination.', 'Avoid in Cold from Spleen deficiency.', '9–30 g', '—'],
    ['Ai Ye', 'Artemisiae Argyi Folium', 'Bitter, acrid', 'Warm', 'SP, LV, KD', 'Warms the womb and stops bleeding; calms the fetus; disperses Cold and relieves pain; eliminates Dampness and stops itching.', 'Avoid in Blood Heat or Yin deficiency.', '3–9 g', '—'],
];

$bleedingsimilarities = [
    ['Stop bleeding by pattern', 'Pu Huang · San Qi · Qian Cao Gen · Xian He Cao · Bai Ji · Huai Hua Mi · Di Yu · Ce Bai Ye · Bai Mao Gen · Ai Ye', 'All ten stop bleeding, but selection depends on whether the pattern requires moving Blood, astringing, cooling Blood or warming the womb.'],
    ['Move Blood and stop bleeding', 'Pu Huang · San Qi · Qian Cao Gen', 'Pu Huang, San Qi and Qian Cao Gen stop bleeding while addressing stasis; San Qi emphasizes trauma and pain, while Qian Cao Gen is cooling.'],
    ['Astringe Blood and stop bleeding', 'Pu Huang · Xian He Cao · Bai Ji', 'Pu Huang, Xian He Cao and Bai Ji have astringent action; Bai Ji strongly emphasizes Lung and Stomach bleeding and topical tissue repair.'],
    ['Cool Blood and stop bleeding', 'Qian Cao Gen · Huai Hua Mi · Di Yu · Ce Bai Ye · Bai Mao Gen', 'Qian Cao Gen, Huai Hua Mi, Di Yu, Ce Bai Ye and Bai Mao Gen treat bleeding associated with Heat, with different organ and symptom emphasis.'],
    ['Large Intestine bleeding and dysentery', 'Xian He Cao · Huai Hua Mi · Di Yu', 'Xian He Cao treats diarrhea and dysentery, Huai Hua Mi emphasizes hemorrhoidal bleeding, and Di Yu clears Heat and repairs tissue.'],
    ['Trauma and topical tissue repair', 'San Qi · Bai Ji · Di Yu', 'San Qi is central for traumatic bleeding and pain; Bai Ji and Di Yu reduce swelling or generate flesh when used topically.'],
    ['Lung bleeding, cough and phlegm', 'Bai Ji · Ce Bai Ye · Bai Mao Gen', 'Bai Ji astringes Lung bleeding, Ce Bai Ye stops cough and transforms phlegm, and Bai Mao Gen clears Lung Heat.'],
    ['Warm the womb and stop bleeding', 'Ai Ye', 'Ai Ye is the distinctive warm herb for uterine bleeding from Cold and also calms the fetus; it is inappropriate for Blood Heat.'],
];


$activatebloodherbs = [
    ['Chuan Xiong', 'Chuanxiong Rhizoma', 'Acrid', 'Warm', 'LV, GB, PC', 'Invigorates Blood and Qi; expels Wind and relieves many types of headache and pain.', 'Avoid in Yin deficiency with Heat, Liver Yang rising headache, Qi deficiency, excessive menstrual bleeding or hemoptysis; overdose may cause nausea, vomiting or dizziness.', '3–9 g decoction; 1–1.5 g powder', '—'],
    ['Yan Hu Suo', 'Corydalis Rhizoma', 'Acrid, bitter', 'Warm', 'HT, LV, ST', 'Strongly moves Qi and Blood and relieves pain from many causes and locations.', 'Avoid during pregnancy.', '3–9 g', '—'],
    ['Yu Jin', 'Curcumae Radix', 'Acrid, bitter', 'Cold', 'HT, LU, LV', 'Cools Blood, moves Qi and Blood, benefits the Gallbladder for jaundice or stones, clears Heart Heat and opens the orifices.', 'Avoid in Yin deficiency from Blood loss or during pregnancy; traditionally antagonizes Ding Xiang.', '6–12 g', '—'],
    ['Dan Shen', 'Salviae Miltiorrhizae Radix', 'Bitter', 'Slightly cold', 'HT, PC, LV', 'Invigorates and nourishes Blood, cools Blood, calms irritability and regulates menstruation.', 'Avoid when there is no Blood stasis; traditionally incompatible with Li Lu.', '6–15 g; up to 60 g for vasculitis', '—'],
    ['Chi Shao', 'Paeoniae Radix Rubra', 'Sour, bitter', 'Slightly cold', 'LV, SP', 'Cools and invigorates Blood; clears Liver Fire and relieves pain from Blood stasis.', 'Avoid in Blood deficiency.', '6–15 g', '—'],
    ['Yi Mu Cao', 'Leonuri Herba', 'Acrid, bitter', 'Slightly cold', 'HT, LV, UB', 'Invigorates Blood and regulates menstruation; promotes urination and reduces swelling; clears Heat and resolves toxicity.', 'Avoid during pregnancy; use cautiously in Blood or Yin deficiency.', '9–15 g; up to 30 g', '—'],
    ['Ze Lan', 'Lycopi Herba', 'Bitter, acrid', 'Slightly warm', 'LV, SP', 'Invigorates Blood, regulates menstruation and promotes urination; treats trauma internally or topically.', 'Avoid during pregnancy when Blood stasis is absent.', '9–15 g', '—'],
    ['Tao Ren', 'Persicae Semen', 'Bitter, sweet', 'Neutral', 'HT, LI, LV, LU', 'Breaks Blood stasis, unblocks menstruation and moistens the Intestines to relieve constipation.', 'Avoid during pregnancy.', '4.5–9 g', 'Crush before decocting.'],
    ['Hong Hua', 'Carthami Flos', 'Acrid', 'Warm', 'HT, LV', 'Invigorates Blood, unblocks menstruation and relieves pain; lower doses gently harmonize Blood.', 'Avoid during pregnancy.', '3–9 g', 'Add near the end of the decoction.'],
    ['Mo Yao', 'Myrrha', 'Bitter', 'Neutral', 'HT, LV, SP', 'Invigorates Blood and relieves pain; reduces swelling and promotes healing when used topically.', 'Avoid during pregnancy or excessive uterine bleeding.', '3–9 g', '—'],
    ['Ru Xiang', 'Olibanum', 'Acrid, bitter', 'Warm', 'HT, LV, SP', 'Invigorates Blood, relaxes sinews and opens channels; reduces swelling and generates flesh topically.', 'Avoid during pregnancy; use cautiously in Spleen deficiency.', '3–9 g', '—'],
    ['San Leng', 'Sparganii Rhizoma', 'Bitter, acrid', 'Neutral', 'LV, SP', 'Strongly breaks Blood stasis and fixed accumulations, moves Qi and relieves pain; also treats childhood nutritional impairment.', 'Avoid during pregnancy or in deficient patients.', '3–9 g', '—'],
    ['E Zhu', 'Curcumae Rhizoma', 'Bitter, acrid', 'Warm', 'LV, SP', 'Strongly breaks Blood stasis and accumulations; moves Qi more strongly than San Leng.', 'Avoid during pregnancy, Qi or Blood deficiency, or excessive menstruation.', '3–9 g', '—'],
    ['Huai Niu Xi', 'Achyranthis Bidentatae Radix', 'Bitter, sour', 'Neutral', 'LV, KD', 'Invigorates Blood and directs it downward; tonifies Liver and Kidney, strengthens sinews and bones, and clears Damp-Heat from the Lower Jiao.', 'Avoid in diarrhea from Spleen deficiency, excessive menstruation or spermatorrhea; traditionally should not be used with Bai Qian.', '6–15 g', '—'],
    ['Chuan Niu Xi', 'Cyathulae Radix', 'Sweet, bitter', 'Neutral', 'LV, KD', 'Invigorates Blood and directs it downward; dispels Wind-Damp and unblocks menstruation.', 'Avoid during pregnancy, excessive menstruation or spermatorrhea.', '4.5–9 g', '—'],
    ['Wang Bu Liu Xing', 'Vaccariae Semen', 'Bitter', 'Neutral', 'LV, ST', 'Invigorates Blood, unblocks menstruation, promotes lactation and reduces swelling of the breasts or testicles.', 'Avoid during pregnancy.', '4.5–9 g', '—'],
];

$activatebloodsimilarities = [
    ['Invigorate Blood and remove stasis', 'Chuan Xiong · Yan Hu Suo · Yu Jin · Dan Shen · Chi Shao · Yi Mu Cao · Ze Lan · Tao Ren · Hong Hua · Mo Yao · Ru Xiang · San Leng · E Zhu · Huai Niu Xi · Chuan Niu Xi · Wang Bu Liu Xing', 'All sixteen activate Blood, but selection depends on pain, temperature, menstruation, trauma, fixed accumulation, swelling and associated organ patterns.'],
    ['Pain and headache emphasis', 'Chuan Xiong · Yan Hu Suo · Mo Yao · Ru Xiang', 'Chuan Xiong treats headache and pain while moving Qi; Yan Hu Suo is among the strongest choices for pain of varied causes; Mo Yao and Ru Xiang emphasize painful stasis and trauma.'],
    ['Cool Blood while moving stasis', 'Yu Jin · Dan Shen · Chi Shao · Yi Mu Cao', 'Yu Jin, Dan Shen, Chi Shao and Yi Mu Cao are cooling choices; they differ in Gallbladder, Heart, Liver, menstruation and urination emphasis.'],
    ['Regulate menstruation and amenorrhea', 'Yi Mu Cao · Ze Lan · Tao Ren · Hong Hua · Wang Bu Liu Xing', 'Yi Mu Cao, Ze Lan, Tao Ren, Hong Hua and Wang Bu Liu Xing unblock or regulate menstruation; Wang Bu Liu Xing also promotes lactation.'],
    ['Tao Ren and Hong Hua pair', 'Tao Ren · Hong Hua', 'Tao Ren strongly breaks stasis and moistens the Intestines; Hong Hua unblocks menses and relieves pain, with smaller doses gently harmonizing Blood.'],
    ['Trauma, swelling and topical healing', 'Ze Lan · Mo Yao · Ru Xiang', 'Ze Lan treats trauma internally or topically; Mo Yao and Ru Xiang reduce swelling, relieve pain and promote healing or tissue generation.'],
    ['Break fixed Blood accumulations', 'San Leng · E Zhu', 'San Leng and E Zhu strongly break stasis and masses; E Zhu moves Qi more strongly, while San Leng also addresses childhood nutritional impairment.'],
    ['Huai Niu Xi and Chuan Niu Xi', 'Huai Niu Xi · Chuan Niu Xi', 'Both direct Blood downward; Huai Niu Xi emphasizes tonifying Liver and Kidney and strengthening sinews and bones, while Chuan Niu Xi emphasizes moving Blood and dispelling Wind-Damp.'],
    ['Pregnancy and strong Blood movement', 'Most herbs in this category', 'Most herbs in this category require avoidance or strong caution during pregnancy because they invigorate Blood or break stasis.'],
];


$warminteriorherbs = [
    ['Chuan Wu', 'Aconiti Radix', 'Acrid, bitter', 'Hot, very toxic', 'LV, SP, KD', 'Expels Wind-Cold-Damp and relieves painful obstruction; reduces swelling and dispels putrefaction; strongly warms the channels.', 'Avoid in Yin deficiency with Heat, true Heat with false Cold, or pregnancy; incompatible with Ban Xia, Bei Mu, Gua Lou, Bai Lian and Bai Ji; traditionally antagonizes Wu Gong.', '1.5–4.5 g', 'Boil 30–60 minutes longer than other herbs.'],
    ['Cao Wu', 'Aconiti Kusnezoffii Radix', 'Acrid, bitter', 'Hot, very toxic', 'LV, SP, KD', 'Strongly expels Wind-Cold-Damp and stops pain; more toxic than Chuan Wu and used mainly in carefully prepared or topical applications.', 'Avoid in Heat patterns, Yin deficiency, pregnancy or without proper preparation; shares major aconite incompatibilities.', '1.5–3 g', 'Boil 30–60 minutes longer than other herbs.'],
    ['Fu Zi', 'Aconiti Lateralis Radix Praeparata', 'Acrid', 'Hot, toxic', 'HT, KD, SP', 'Restores devastated Yang, warms Fire and assists Yang, disperses Cold through the channels and relieves pain.', 'Avoid in Yin deficiency with Heat, reckless movement of hot Blood or pregnancy; use only the properly prepared form.', '3–15 g', 'Boil 30–60 minutes longer than other herbs.'],
    ['Gan Jiang', 'Zingiberis Rhizoma', 'Acrid', 'Hot', 'HT, LU, SP, ST', 'Warms the Spleen and Stomach and expels Cold; warms the Lung and transforms phlegm; the charred form warms channels and stops bleeding.', 'Avoid in Yin deficiency with Heat, Interior excess Heat, reckless movement of hot Blood or pregnancy.', '3–9 g', '—'],
    ['Rou Gui', 'Cinnamomi Cortex', 'Acrid, sweet', 'Hot', 'HT, KD, LV, SP', 'Disperses deep Cold, warms the channels and relieves pain; leads Fire back to its source and encourages generation of Qi and Blood.', 'Avoid in Yin deficiency with Heat, Interior excess Heat, reckless movement of hot Blood or pregnancy.', '1.5–4.5 g decoction; 1–2 g powder', 'Add near the end; powder may be taken separately.'],
    ['Hua Jiao', 'Zanthoxyli Pericarpium', 'Acrid', 'Hot, slightly toxic', 'KD, SP, ST', 'Warms the Middle Jiao and stops pain; kills parasites and alleviates abdominal pain.', 'Avoid in Yin deficiency with Heat or pregnancy; traditionally antagonizes Gua Lou and counteracts Kuan Dong Hua, Fu Zi and Fang Feng.', '1.5–6 g', '—'],
    ['Wu Zhu Yu', 'Evodiae Fructus', 'Acrid, bitter', 'Hot, slightly toxic', 'LV, SP, ST', 'Warms the Middle Jiao, stops vomiting and diarrhea, disperses Cold and relieves vertex headache; soothes Liver Qi and leads Fire downward.', 'Avoid in Yin deficiency with Heat; prolonged use may injure Yin; traditionally antagonizes Dan Shen and Zi Shi Ying.', '3–9 g', '—'],
    ['Xiao Hui Xiang', 'Foeniculi Fructus', 'Acrid', 'Warm', 'LV, KD, SP, ST', 'Warms the Liver and Kidney, regulates Qi and stops Cold pain, especially in the lower abdomen.', 'Use cautiously in Yin deficiency with Heat.', '3–9 g', '—'],
    ['Ding Xiang', 'Caryophylli Flos', 'Acrid', 'Warm', 'KD, SP, ST', 'Warms the Middle Jiao and descends rebellious Stomach Qi; warms the Kidney and assists Yang.', 'Avoid in febrile disease or Yin deficiency; traditionally antagonizes Yu Jin.', '1–3 g', '—'],
    ['Gao Liang Jiang', 'Alpiniae Officinarum Rhizoma', 'Acrid', 'Hot', 'SP, ST', 'Warms the Middle Jiao, stops pain and directs rebellious Stomach Qi downward.', 'Avoid in Yin deficiency with Heat; weak patients should not use it alone and may require supporting tonics.', '3–9 g', '—'],
    ['Bi Ba', 'Piperis Longi Fructus', 'Acrid', 'Hot', 'ST, LI', 'Warms the Middle Jiao and stops pain; also used for headache, nasal congestion and toothache in traditional applications.', 'Avoid in Heat from excess or deficiency.', '1.5–4.5 g', '—'],
    ['Hu Jiao', 'Piperis Fructus', 'Acrid', 'Hot', 'LI, ST', 'Warms the Middle Jiao and supports digestion; disperses Cold and relieves abdominal discomfort.', 'Avoid in Yin deficiency with Heat.', '1.5–3 g decoction; 0.5–1 g pill', 'Crush before use.'],
];

$warminteriorsimilarities = [
    ['Warm the Interior and expel Cold', 'Chuan Wu · Cao Wu · Fu Zi · Gan Jiang · Rou Gui · Hua Jiao · Wu Zhu Yu · Xiao Hui Xiang · Ding Xiang · Gao Liang Jiang · Bi Ba · Hu Jiao', 'All twelve warm the Interior, but selection depends on Cold depth, organ location, Yang collapse, pain, rebellious Qi, toxicity and constitutional strength.'],
    ['Aconite group: Chuan Wu, Cao Wu and Fu Zi', 'Chuan Wu · Cao Wu · Fu Zi', 'Chuan Wu and Cao Wu emphasize painful Wind-Cold-Damp obstruction; Cao Wu is more toxic. Fu Zi is the principal choice for restoring devastated Yang and warming all channels.'],
    ['Restore Yang and warm the Kidney', 'Fu Zi · Rou Gui · Xiao Hui Xiang · Ding Xiang', 'Fu Zi restores collapsed Yang; Rou Gui leads Fire back to its source; Xiao Hui Xiang and Ding Xiang warm the Kidney with more focused Qi-regulating actions.'],
    ['Warm the Middle Jiao and stop pain', 'Gan Jiang · Hua Jiao · Wu Zhu Yu · Ding Xiang · Gao Liang Jiang · Bi Ba · Hu Jiao', 'Gan Jiang, Hua Jiao, Wu Zhu Yu, Ding Xiang, Gao Liang Jiang, Bi Ba and Hu Jiao warm the digestive center, differing in vomiting, diarrhea, parasites, pain and digestive emphasis.'],
    ['Descend rebellious Stomach Qi', 'Wu Zhu Yu · Ding Xiang · Gao Liang Jiang', 'Wu Zhu Yu, Ding Xiang and Gao Liang Jiang warm the Middle and direct rebellious Qi downward; Wu Zhu Yu also treats vertex headache and diarrhea.'],
    ['Warm the Lung and transform phlegm', 'Gan Jiang', 'Gan Jiang uniquely emphasizes warming the Lung and transforming Cold-phlegm within this category.'],
    ['Cold pain and Liver Qi', 'Wu Zhu Yu · Xiao Hui Xiang', 'Wu Zhu Yu and Xiao Hui Xiang warm the Liver and relieve Cold pain; Wu Zhu Yu is more descending and toxic, while Xiao Hui Xiang is gentler.'],
    ['Toxicity and prolonged cooking', 'Chuan Wu · Cao Wu · Fu Zi · Hua Jiao · Wu Zhu Yu', 'Chuan Wu, Cao Wu and Fu Zi require correct preparation and prolonged cooking; Hua Jiao and Wu Zhu Yu also require caution because of toxicity or Yin injury.'],
    ['Heat, Yin deficiency and pregnancy', 'General category caution', 'Warm Interior herbs are generally inappropriate for Heat or Yin-deficiency Heat; several strongly warming herbs also require avoidance during pregnancy.'],
];


$qitonicherbs = [
    ['Ren Shen', 'Ginseng Radix', 'Sweet, slightly bitter', 'Slightly warm', 'LU, SP', 'Strongly tonifies Yuan Qi; generates fluids and stops thirst; benefits Heart Qi and calms the Spirit.', 'Avoid in Yin deficiency with Heat, excess Heat without Qi deficiency, Liver Yang rising or hypertension; overuse may cause headache. Incompatible with Li Lu and traditionally antagonized by Wu Ling Zhi.', '3–9 g; 0.5–1 g powder; up to 30 g emergency', 'Cook separately.'],
    ['Dang Shen', 'Codonopsis Radix', 'Sweet', 'Neutral', 'LU, SP', 'Tonifies Spleen and Lung Qi for both Exterior and Interior deficiency; a gentler substitute for Ren Shen requiring a larger dose.', 'Traditionally incompatible with Li Lu.', '6–9 g; up to 30 g', '—'],
    ['Huang Qi', 'Astragali Radix', 'Sweet', 'Slightly warm', 'LU, SP', 'Tonifies Qi and Blood, raises Yang, strengthens defensive Qi, stabilizes the Exterior, promotes urination and supports discharge of pus and tissue regeneration.', 'Avoid in Yin deficiency with Heat, Exterior excess, Qi or food stagnation, Damp obstruction, or early skin lesions with active Heat.', '9–15 g; up to 30 g', '—'],
    ['Bai Zhu', 'Atractylodis Macrocephalae Rhizoma', 'Bitter, sweet', 'Warm', 'SP, ST', 'Strengthens the Spleen, dries Dampness and calms the fetus in Spleen-deficiency patterns.', 'Avoid in Yin deficiency with Heat or injured fluids.', '6–15 g', '—'],
    ['Bai Bian Dou', 'Lablab Semen', 'Sweet', 'Neutral', 'SP, ST', 'Strengthens the Spleen, transforms Dampness, clears Summer-Heat and treats food poisoning.', 'Use cautiously in intermittent fever and chills from Cold disorders.', '9–30 g', '—'],
    ['Shan Yao', 'Dioscoreae Rhizoma', 'Sweet', 'Neutral', 'KD, LU, SP', 'Tonifies Spleen, Lung and Kidney; nourishes Lung Yin, stabilizes Kidney Essence and generates fluids.', 'Avoid in excess patterns, especially fullness from Dampness, stagnation or accumulation; traditionally antagonized by Gan Sui.', '9–30 g; 60–240 g alone', '—'],
    ['Da Zao', 'Jujubae Fructus', 'Sweet', 'Warm', 'SP, ST', 'Tonifies the Spleen, nourishes Blood, calms the Spirit and moderates or harmonizes the actions of other herbs.', 'Avoid in excess Dampness, food stagnation, intestinal parasites or Damp-phlegm disorders.', '3–12 dates; 10–30 g', '—'],
    ['Gan Cao', 'Glycyrrhizae Radix', 'Sweet', 'Neutral', 'HT, LU, SP, ST; all channels', 'Tonifies Qi, moderates spasms and pain, clears Fire toxicity and harmonizes other herbs; prepared Zhi Gan Cao is warmer and more tonifying.', 'Avoid in excess Dampness, nausea or vomiting; incompatible with Da Ji, Hai Zao, Gan Sui and Yuan Hua; traditionally antagonizes Yuan Zhi.', '1.5–9 g; 15–30 g', '—'],
    ['Yi Tang', 'Maltosum', 'Sweet', 'Slightly warm', 'LU, SP, ST', 'Tonifies the Middle Jiao and relieves abdominal pain from deficiency Cold; moistens the Lung and stops cough.', 'Avoid in internal Damp-Heat, Middle Jiao fullness with vomiting, phlegm-Heat cough or food stagnation.', '15–60 g', 'Dissolve in the strained decoction.'],
    ['Feng Mi', 'Mel', 'Sweet', 'Neutral', 'LU, SP, LI', 'Tonifies the Middle Jiao, relieves pain, moistens the Lung and lubricates the Intestines.', 'Avoid in excess Dampness, phlegm-Heat, Middle Jiao fullness with vomiting or loose stools.', '15–30 g', '—'],
    ['Geng Mi', 'Oryzae Semen', 'Sweet', 'Neutral', 'SP, ST', 'Strengthens the Spleen, transforms Dampness, stops thirst, generates fluids and alleviates diarrhea or dysentery.', 'No specific caution noted.', '3–9 g', '—'],
];

$qitonicsimilarities = [
    ['Tonify the Spleen and augment Qi', 'Ren Shen · Dang Shen · Huang Qi · Bai Zhu · Bai Bian Dou · Shan Yao · Da Zao · Gan Cao · Yi Tang · Feng Mi · Geng Mi', 'All eleven support Qi or Spleen function, but differ in strength, organ focus, fluids, Dampness, Exterior stability and food-like nourishing qualities.'],
    ['Ren Shen and Dang Shen', 'Ren Shen · Dang Shen', 'Ren Shen strongly restores Yuan Qi and is appropriate for severe deficiency; Dang Shen more gently tonifies Spleen and Lung Qi and is usually given at a larger dose.'],
    ['Stabilize the Exterior and stop sweating', 'Huang Qi · Bai Zhu', 'Huang Qi strengthens defensive Qi and raises Yang; Bai Zhu strengthens the Spleen and dries Dampness. Together they support Qi deficiency with spontaneous sweating.'],
    ['Strengthen the Spleen and transform Dampness', 'Bai Zhu · Bai Bian Dou · Geng Mi', 'Bai Zhu is stronger and drying; Bai Bian Dou is gentle and clears Summer-Heat; Geng Mi is food-like and generates fluids.'],
    ['Tonify Lung and Kidney while securing fluids', 'Shan Yao', 'Shan Yao uniquely tonifies Spleen, Lung and Kidney while nourishing Lung Yin and stabilizing Kidney Essence.'],
    ['Moderate and harmonize formulas', 'Da Zao · Gan Cao', 'Da Zao nourishes Blood and calms the Spirit; Gan Cao moderates spasms, toxicity and the actions of other herbs.'],
    ['Tonify the Middle Jiao and relieve pain', 'Yi Tang · Feng Mi · Gan Cao', 'Yi Tang, Feng Mi and Gan Cao relieve deficiency-type abdominal pain; Yi Tang is warmer, Feng Mi moistens the Intestines, and Gan Cao moderates spasm.'],
    ['Food-like Qi tonics', 'Da Zao · Yi Tang · Feng Mi · Geng Mi', 'Da Zao, Yi Tang, Feng Mi and Geng Mi nourish gently, but can aggravate Dampness, fullness or food stagnation when used inappropriately.'],
    ['Pure deficiency versus excess', 'General category caution', 'Qi tonics are inappropriate when excess pathogens, Dampness, phlegm or food stagnation predominate without true deficiency.'],
];


$bloodtonicherbs = [
    ['Shu Di Huang', 'Rehmanniae Radix Praeparata', 'Sweet', 'Slightly warm', 'HT, KD, LV', 'Nourishes Blood and Yin, tonifies Essence and Marrow, and treats wasting and thirsting from deficiency.', 'Use cautiously in Spleen and Stomach deficiency, Qi or phlegm stagnation; its cloying nature may cause abdominal distension or loose stools.', '9–30 g', '—'],
    ['Dang Gui', 'Angelicae Sinensis Radix', 'Sweet, acrid', 'Warm', 'HT, LV, SP', 'Nourishes, invigorates and harmonizes Blood; regulates menstruation, disperses Cold, moistens the Intestines, reduces swelling and promotes tissue healing.', 'Avoid in Yin deficiency with Heat; use cautiously in diarrhea or abdominal distension from Dampness.', '4.5–15 g', '—'],
    ['Bai Shao', 'Paeoniae Radix Alba', 'Bitter, sour', 'Slightly cold', 'LV, SP', 'Nourishes Blood, preserves Yin, softens the Liver, calms Liver Yang and relieves pain; harmonizes the nutritive and protective levels.', 'Use cautiously in cold-deficiency diarrhea or Yang-deficient Cold; incompatible with Li Lu and traditionally antagonized by Shi Hu and Mang Xiao.', '6–15 g', '—'],
    ['E Jiao', 'Asini Corii Colla', 'Sweet', 'Neutral', 'KD, LV, LU', 'Nourishes Blood and stops bleeding; nourishes and moistens Yin, especially for dry Lung cough from Yin deficiency or consumption.', 'Avoid in Exterior conditions; use cautiously in Spleen and Stomach deficiency; traditionally counteracted by Da Huang.', '3–9 g', 'Dissolve in the strained decoction.'],
    ['Gou Qi Zi', 'Lycii Fructus', 'Sweet', 'Neutral', 'LV, LU, KD', 'Tonifies Liver and Kidney, benefits Essence and vision, enriches Yin, moistens the Lung and mildly supports Kidney Yang.', 'Avoid in externally contracted excess Heat or Spleen deficiency with Dampness and loose stools.', '6–12 g', '—'],
    ['Sang Shen', 'Mori Fructus', 'Sweet', 'Cold', 'HT, LV, KD', 'Nourishes Yin, generates fluids and moistens the Intestines.', 'Avoid in diarrhea from Spleen deficiency.', '9–15 g', '—'],
    ['Long Yan Rou', 'Longan Arillus', 'Sweet', 'Warm', 'HT, SP', 'Tonifies Heart and Spleen, nourishes Blood and calms the Spirit.', 'Avoid in phlegm-Fire or Dampness in the Middle Jiao.', '9–15 g', '—'],
];

$bloodtonicsimilarities = [
    ['Nourish Blood and restore deficiency', 'Shu Di Huang · Dang Gui · Bai Shao · E Jiao · Gou Qi Zi · Sang Shen · Long Yan Rou', 'All seven nourish Blood or related Yin and fluids, but differ in menstruation, bleeding, dryness, bowel, Liver-Kidney and Shen emphasis.'],
    ['Regulate menstruation', 'Dang Gui · Bai Shao', 'Dang Gui nourishes and moves Blood while dispersing Cold; Bai Shao nourishes Blood, preserves Yin and softens the Liver.'],
    ['Nourish Blood and Yin', 'Shu Di Huang · E Jiao · Gou Qi Zi · Sang Shen', 'Shu Di Huang, E Jiao, Gou Qi Zi and Sang Shen all nourish deficient Blood or Yin; select according to Essence, bleeding, Lung dryness, fluids and bowel dryness.'],
    ['Moisten the Intestines', 'Dang Gui · Sang Shen', 'Dang Gui and Sang Shen relieve dry stool through moistening; Dang Gui also regulates Blood, while Sang Shen more directly generates fluids.'],
    ['Tonify Liver and Kidney', 'Shu Di Huang · Gou Qi Zi', 'Shu Di Huang strongly nourishes Blood, Yin and Essence; Gou Qi Zi is gentler and also benefits the eyes and Lung.'],
    ['Stop bleeding while nourishing', 'E Jiao', 'E Jiao is the distinctive Blood tonic that also stops bleeding and moistens Lung Yin.'],
    ['Calm the Spirit', 'Long Yan Rou', 'Long Yan Rou tonifies Heart and Spleen, nourishes Blood and calms the Spirit, especially when deficiency affects sleep or emotional stability.'],
    ['Cloying tonics and digestive weakness', 'Shu Di Huang · E Jiao · Long Yan Rou', 'Shu Di Huang and E Jiao are rich and cloying; Long Yan Rou and other sweet tonics can aggravate Dampness or phlegm when digestion is weak.'],
];


$yangtonicherbs = [
    ['Lu Rong', 'Cervi Cornu', 'Sweet, salty', 'Warm', 'KD, LV', 'Strongly tonifies Kidney Yang, Essence and Blood; strengthens the Du, Chong and Ren vessels and fortifies sinews and bones.', 'Avoid in Yin deficiency with Heat, Lung phlegm-Heat, blazing Stomach Fire, warm febrile disease or Blood Heat.', '1–2 g powder; 3–4.5 g decoction', '—'],
    ['Rou Cong Rong', 'Cistanches Herba', 'Sweet, salty', 'Warm', 'LI, KD', 'Tonifies Kidney Yang and Essence, moistens the Intestines and treats infertility or cold womb from deficiency.', 'Avoid in Yin deficiency with Heat or diarrhea from Spleen and Stomach deficiency.', '9–21 g', '—'],
    ['Suo Yang', 'Cynomorii Herba', 'Sweet', 'Warm', 'LI, KD, LV', 'Tonifies Kidney Yang and Essence, moistens the Intestines and benefits weakness or atrophy from Liver and Kidney deficiency.', 'Avoid in Kidney deficiency with Heat, diarrhea from Spleen deficiency or constipation from excess Heat.', '5–15 g', '—'],
    ['Hu Tao Ren', 'Juglandis Semen', 'Sweet', 'Warm', 'KD, LI, LU', 'Tonifies Kidney Yang, strengthens the low back and knees, warms the Lung and helps the Kidney grasp Qi; moistens the Intestines.', 'Avoid in phlegm-Fire cough, Yin deficiency with Heat or watery stools.', '9–15 g; 9–30 g alone', '—'],
    ['Yin Yang Huo', 'Epimedii Herba', 'Acrid, sweet', 'Warm', 'KD, LV', 'Tonifies Kidney Yang and libido; expels Wind-Cold-Damp and relieves painful obstruction.', 'Avoid in Yin deficiency with Heat; prolonged use may injure Yin and cause dizziness, vomiting, dry mouth, thirst or nosebleed.', '3–9 g', '—'],
    ['Xian Mao', 'Curculiginis Rhizoma', 'Acrid', 'Hot, toxic', 'KD', 'Strongly warms Kidney Yang, treats infertility from Cold and expels Wind-Cold-Damp painful obstruction.', 'Avoid in Yin deficiency with Heat; prolonged use is not recommended because toxic reactions may occur.', '3–9 g', '—'],
    ['Ba Ji Tian', 'Morindae Radix', 'Acrid, sweet', 'Slightly warm', 'KD', 'Tonifies Kidney Yang, strengthens sinews and bones, expels Wind-Damp and supports Spleen-Kidney Yang diarrhea.', 'Avoid in Yin deficiency with Heat or Damp-Heat with difficult urination; traditionally antagonizes Dan Shen.', '6–15 g', '—'],
    ['Bu Gu Zhi', 'Psoraleae Fructus', 'Acrid, bitter', 'Very warm', 'KD, SP', 'Warms Spleen and Kidney Yang, stops diarrhea, stabilizes Essence and helps the Kidney grasp Qi; used topically for vitiligo.', 'Avoid in Yin deficiency with Heat or constipation; its harshness may injure deficient patients; traditionally antagonizes Gan Cao.', '4.5–9 g', '—'],
    ['Yi Zhi Ren', 'Alpiniae Oxyphyllae Fructus', 'Acrid', 'Warm', 'KD, SP', 'Warms and secures Kidney and Spleen, retains urine and Essence, and controls excessive salivation.', 'Avoid in frequent urination or vaginal discharge caused by Heat.', '3–9 g', 'Crush before use.'],
    ['Gou Ji', 'Cibotii Rhizoma', 'Bitter, sweet', 'Warm', 'KD, LV', 'Tonifies Liver and Kidney, strengthens sinews and bones, and expels Wind-Damp painful obstruction.', 'Avoid in difficult urination or Heat from Yin deficiency; traditionally antagonizes Bai Lian and Gan Cao.', '9–15 g', '—'],
    ['Gu Sui Bu', 'Drynariae Rhizoma', 'Bitter', 'Warm', 'KD, LV', 'Tonifies Kidney, mends sinews and bones, invigorates Blood after trauma and may stimulate hair growth topically.', 'Avoid in Yin deficiency without Blood stasis.', '9–21 g', '—'],
    ['Xu Duan', 'Dipsaci Radix', 'Bitter, sweet, acrid', 'Slightly warm', 'KD, LV', 'Tonifies Liver and Kidney, strengthens sinews and bones, invigorates Blood, relieves trauma pain and calms the fetus.', 'Traditionally antagonized by Lei Wan.', '9–18 g', '—'],
    ['Du Zhong', 'Eucommiae Cortex', 'Sweet, slightly acrid', 'Warm', 'KD, LV', 'Tonifies Liver and Kidney, strengthens sinews and bones, supports smooth Qi and Blood flow, calms the fetus and is traditionally used in hypertension patterns.', 'Avoid in Yin deficiency with Heat; traditionally antagonizes Xuan Shen.', '9–15 g', '—'],
    ['Tu Si Zi', 'Cuscutae Semen', 'Acrid, sweet', 'Neutral', 'KD, LV', 'Tonifies Liver and Kidney, secures Essence and urine, improves vision, stops diarrhea and calms the fetus.', 'Avoid in Yin deficiency with Heat, constipation, or scanty dark urine.', '6–15 g', '—'],
];

$yangtonicsimilarities = [
    ['Tonify Kidney Yang and secure Essence', 'Lu Rong · Rou Cong Rong · Suo Yang · Hu Tao Ren · Yin Yang Huo · Xian Mao · Ba Ji Tian · Bu Gu Zhi · Yi Zhi Ren · Gou Ji · Gu Sui Bu · Xu Duan · Du Zhong · Tu Si Zi', 'All fourteen support Kidney Yang or related functions, but differ in potency, Essence, urine, bowel, pain, bone, diarrhea and reproductive emphasis.'],
    ['Augment Blood and Essence', 'Lu Rong · Rou Cong Rong', 'Lu Rong is the strongest, animal-derived tonic for Yang, Essence and Blood; Rou Cong Rong is gentler and moistens the Intestines.'],
    ['Moisten the Intestines', 'Rou Cong Rong · Suo Yang · Hu Tao Ren', 'Rou Cong Rong, Suo Yang and Hu Tao Ren tonify Yang while relieving dry constipation; Hu Tao Ren also helps the Kidney grasp Lung Qi.'],
    ['Expel Wind-Damp and relieve pain', 'Yin Yang Huo · Xian Mao · Ba Ji Tian · Gou Ji', 'Yin Yang Huo, Xian Mao, Ba Ji Tian and Gou Ji combine Yang or Liver-Kidney tonification with Wind-Damp painful obstruction treatment.'],
    ['Warm Spleen and Kidney and stop diarrhea', 'Ba Ji Tian · Bu Gu Zhi · Tu Si Zi', 'Ba Ji Tian, Bu Gu Zhi and Tu Si Zi support Spleen-Kidney deficiency; Bu Gu Zhi is warmer and Tu Si Zi is neutral and securing.'],
    ['Secure Essence and urine', 'Bu Gu Zhi · Yi Zhi Ren · Tu Si Zi', 'Bu Gu Zhi, Yi Zhi Ren and Tu Si Zi retain Essence or urine; Yi Zhi Ren also controls salivation and Tu Si Zi improves vision.'],
    ['Strengthen bones and repair trauma', 'Ba Ji Tian · Gou Ji · Gu Sui Bu · Xu Duan · Du Zhong', 'Ba Ji Tian and Gou Ji strengthen sinews and bones; Gu Sui Bu and Xu Duan emphasize traumatic injury and bone or tendon repair; Du Zhong supports chronic weakness.'],
    ['Calm the fetus', 'Xu Duan · Du Zhong · Tu Si Zi', 'Xu Duan, Du Zhong and Tu Si Zi tonify Liver and Kidney while helping stabilize pregnancy.'],
    ['Strong heat and toxicity', 'Lu Rong · Yin Yang Huo · Xian Mao · Bu Gu Zhi', 'Lu Rong is very potent; Xian Mao is hot and toxic; Yin Yang Huo and Bu Gu Zhi are warming and may injure Yin when Heat signs are present.'],
];


$yintonicherbs = [
    ['Bai He', 'Lilii Bulbus', 'Sweet, slightly bitter', 'Slightly cold', 'HT, LU', 'Nourishes Lung Yin, moistens dryness, clears Heart Heat and calms the Spirit; used for Lily Bulb disorder.', 'Avoid in Wind-Cold cough, phlegm cough or diarrhea from Spleen and Stomach deficiency.', '9–30 g', '—'],
    ['Tian Men Dong', 'Asparagi Radix', 'Sweet, bitter', 'Very cold', 'KD, LU', 'Strongly nourishes Lung and Kidney Yin, clears Heat, generates fluids and moistens the Intestines.', 'Avoid in low appetite from Spleen and Stomach deficiency with Cold or Wind-Cold cough.', '6–12 g', '—'],
    ['Mai Men Dong', 'Ophiopogonis Radix', 'Sweet, slightly bitter', 'Slightly cold', 'HT, LU, ST', 'Nourishes Lung and Stomach Yin, generates fluids, moistens the Intestines, clears Heart Heat and relieves irritability.', 'Avoid in cold-deficiency diarrhea or congested fluids; traditionally antagonized by Kuan Dong Hua.', '6–15 g', '—'],
    ['Sha Shen', 'Glehniae Radix', 'Sweet, slightly bitter', 'Slightly cold', 'LU, ST', 'Nourishes Lung and Stomach Yin, clears Lung Heat, generates fluids and moistens dryness or dry itchy skin.', 'Avoid in Wind-Cold cough or Spleen deficiency with Cold; traditionally incompatible with Li Lu and antagonized by Fang Ji.', '9–15 g', '—'],
    ['Shi Hu', 'Dendrobii Herba', 'Sweet, slightly salty, bland', 'Slightly cold', 'KD, ST', 'Nourishes Stomach and Kidney Yin, clears deficiency Heat, generates fluids, brightens vision and strengthens Essence.', 'Avoid in deficiency without Heat, excess Heat patterns, abdominal distension with thick greasy coating or early warm-febrile disease; traditionally antagonized by Ba Dou.', '6–12 g', '—'],
    ['Nu Zhen Zi', 'Ligustri Lucidi Fructus', 'Bitter, sweet', 'Cool', 'KD, LV', 'Nourishes Liver and Kidney Yin, clears deficiency Heat, improves vision and treats premature greying of hair.', 'Avoid in Yang deficiency with diarrhea from Spleen deficiency.', '9–18 g', '—'],
    ['Hei Zhi Ma', 'Sesami Semen Nigrum', 'Sweet', 'Neutral', 'KD, LV, LI', 'Nourishes Liver and Kidney, enriches Blood, extinguishes Wind, moistens the Intestines and supports recovery or lactation.', 'Avoid in diarrhea.', '9–30 g', '—'],
    ['Gui Ban', 'Testudinis Plastrum', 'Salty, sweet', 'Cold', 'HT, KD, LV', 'Nourishes Liver and Kidney Yin, anchors Yang, strengthens bones, nourishes Blood and stabilizes the Chong and Ren vessels.', 'Avoid during pregnancy or in Stomach Cold-Damp, Yang deficiency or diarrhea; traditionally antagonized by Sha Shen and Ren Shen.', '15–45 g', 'Crush and cook 30 minutes longer.'],
    ['Bie Jia', 'Trionycis Carapax', 'Salty', 'Slightly cold', 'LV, SP', 'Nourishes Yin and anchors Yang; invigorates Blood, promotes menstruation and dissipates nodules or palpable masses.', 'Avoid during pregnancy, poor appetite or diarrhea from Spleen deficiency, unresolved Exterior conditions or impotence; traditionally antagonized by Ming Fan.', '9–30 g', 'Crush and cook 30 minutes longer.'],
];

$yintonicsimilarities = [
    ['Nourish Yin and generate fluids', 'Bai He · Tian Men Dong · Mai Men Dong · Sha Shen · Shi Hu · Nu Zhen Zi · Hei Zhi Ma · Gui Ban · Bie Jia', 'All nine nourish Yin or related fluids, but differ in Lung, Stomach, Liver, Kidney, bowel, vision, Yang anchoring and mass-dissipating actions.'],
    ['Nourish Lung Yin and moisten dry cough', 'Bai He · Tian Men Dong · Mai Men Dong · Sha Shen', 'Bai He, Tian Men Dong, Mai Men Dong and Sha Shen treat Lung Yin dryness; Bai He calms the Spirit, Tian Men Dong is very cold, Mai Men Dong clears Heart irritability and Sha Shen also nourishes Stomach Yin.'],
    ['Tian Men Dong and Mai Men Dong', 'Tian Men Dong · Mai Men Dong', 'Tian Men Dong is colder and more strongly nourishes Kidney Yin; Mai Men Dong is gentler, emphasizes Stomach fluids and clears Heart irritability.'],
    ['Nourish Stomach Yin and generate fluids', 'Mai Men Dong · Sha Shen · Shi Hu', 'Mai Men Dong, Sha Shen and Shi Hu address thirst and Stomach Yin deficiency; Shi Hu also strengthens Kidney Essence and brightens vision.'],
    ['Liver-Kidney Yin, vision and premature greying', 'Nu Zhen Zi · Hei Zhi Ma', 'Nu Zhen Zi and Hei Zhi Ma nourish Liver and Kidney; Nu Zhen Zi clears deficiency Heat and Hei Zhi Ma more strongly nourishes Blood and moistens the Intestines.'],
    ['Moisten the Intestines', 'Tian Men Dong · Mai Men Dong · Hei Zhi Ma', 'Tian Men Dong, Mai Men Dong and Hei Zhi Ma relieve bowel dryness while nourishing Yin; avoid them when diarrhea or weak digestion predominates.'],
    ['Gui Ban and Bie Jia: anchor Yang', 'Gui Ban · Bie Jia', 'Gui Ban emphasizes the ventral aspect, bones, Blood and Chong-Ren deficiency; Bie Jia emphasizes the dorsal aspect, Blood movement and palpable masses.'],
    ['Calm the Spirit and clear Heart Heat', 'Bai He · Mai Men Dong', 'Bai He and Mai Men Dong nourish Yin while addressing Heart Heat, irritability or emotional disturbance.'],
    ['Cold and cloying herbs with weak digestion', 'General category caution', 'Many Yin tonics are cold or rich and can worsen Spleen deficiency, Dampness, phlegm or diarrhea; digestive tolerance guides selection.'],
];


$astringentherbs = [
    ['Shan Zhu Yu', 'Corni Fructus', 'Sour', 'Slightly warm', 'KD, LV', 'Stabilizes Kidney and Liver, contains Essence and urine, stops excessive sweating and controls uterine bleeding.', 'Avoid in painful or difficult urination from Damp-Heat; traditionally antagonizes Jie Geng, Fang Feng and Fang Ji.', '6–12 g', '—'],
    ['Jin Ying Zi', 'Rosae Fructus', 'Sour, astringent', 'Neutral', 'UB, KD, LI', 'Stabilizes Kidney and contains Essence; binds the Intestines and stops diarrhea.', 'Avoid when excess pathogens remain, especially Heat; excessive or prolonged use may cause abdominal pain or constipation.', '6–12 g; up to 30 g alone', '—'],
    ['Hai Piao Xiao', 'Sepiae Endoconcha', 'Salty, astringent', 'Slightly warm', 'KD, LV, ST', 'Stops bleeding and vaginal discharge, neutralizes Stomach acid, resolves Dampness and promotes healing topically.', 'Prolonged use may cause constipation; traditionally antagonizes Fu Zi and Bai Ji.', '6–12 g', '—'],
    ['Fu Pen Zi', 'Rubi Fructus', 'Sweet, astringent', 'Neutral', 'KD, LV', 'Restrains urine for enuresis or frequent urination, assists Kidney Yang and improves vision.', 'Avoid in difficult urination or Yin deficiency with Heat signs.', '6–12 g', '—'],
    ['Qian Shi', 'Euryales Semen', 'Sweet, astringent', 'Neutral', 'SP, KD', 'Strengthens the Spleen, stops diarrhea, dispels Dampness and stops vaginal discharge.', 'Avoid with difficulty urinating or defecating.', '9–15 g', '—'],
    ['Lian Zi', 'Nelumbinis Semen', 'Sweet, astringent', 'Neutral', 'HT, KD, SP', 'Strengthens the Spleen, stops diarrhea, nourishes the Heart, stabilizes Kidney Essence and calms the Spirit.', 'Avoid in abdominal distension or constipation.', '6–15 g', '—'],
    ['Wu Wei Zi', 'Schisandrae Fructus', 'Sour, sweet', 'Warm', 'HT, KD, LU', 'Contains Lung Qi and stops cough, stabilizes Essence, stops diarrhea and sweating, and generates fluids.', 'Avoid in Exterior conditions, early-stage rashes or internal excess Heat; may cause heartburn.', '3–6 g', '—'],
    ['Bai Guo', 'Ginkgo Semen', 'Sweet, bitter, astringent', 'Neutral, slightly toxic', 'KD, LU', 'Contains Lung Qi and stops cough; eliminates Dampness and stops discharge.', 'Avoid in excess conditions or when thick sputum is difficult to expectorate; toxic if overused.', '4.5–9 g', 'Remove the husk and crush the seed.'],
    ['Wu Mei', 'Mume Fructus', 'Sour, astringent', 'Warm', 'LV, LU, SP', 'Contains Lung Qi and stops cough, generates fluids, stops bleeding and expels roundworms with abdominal pain; used topically for corns or warts.', 'Avoid in internal Heat, excess stagnation or unresolved Exterior disorders.', '6–12 g', '—'],
    ['He Zi', 'Chebulae Fructus', 'Bitter, sour, astringent', 'Neutral', 'LU, LI, ST', 'Binds the Intestines and stops diarrhea, contains Lung Qi and improves the throat.', 'Avoid in Exterior patterns or internal Damp-Heat; may inhibit digestion in Spleen deficiency.', '3–9 g', '—'],
    ['Chi Shi Zhi', 'Halloysitum', 'Sweet, sour, astringent', 'Warm', 'SP, ST, LI', 'Binds the Intestines, stops diarrhea and bleeding, and promotes healing of wounds.', 'Avoid in Heat-type diarrhea, dysentery or Damp-Heat; use cautiously during pregnancy; traditionally antagonizes Da Huang.', '9–18 g', '—'],
    ['Rou Dou Kou', 'Myristicae Semen', 'Acrid', 'Warm', 'LI, SP, ST', 'Warms the Middle Jiao, moves Qi, relieves pain, binds the Intestines and stops diarrhea.', 'Avoid in Heat-type diarrhea or dysentery.', '3–9 g', '—'],
    ['Chun Pi', 'Ailanthi Cortex', 'Bitter, astringent', 'Cold', 'LI, ST', 'Clears Heat, dries Dampness, kills parasites and binds the Intestines to stop diarrhea.', 'Avoid in Spleen and Stomach deficiency with Cold or Kidney Yin deficiency.', '6–9 g', '—'],
    ['Fu Xiao Mai', 'Tritici Fructus', 'Sweet, slightly salty', 'Cool', 'HT', 'Stops sweating from Qi or Yin deficiency; Xiao Mai also nourishes the Heart and calms the Spirit.', 'Avoid when sweating is caused by an unresolved Exterior pathogen.', '15–30 g', '—'],
];

$astringentsimilarities = [
    ['Stabilize and bind leakage', 'Shan Zhu Yu · Jin Ying Zi · Hai Piao Xiao · Fu Pen Zi · Qian Shi · Lian Zi · Wu Wei Zi · Bai Guo · Wu Mei · He Zi · Chi Shi Zhi · Rou Dou Kou · Chun Pi · Fu Xiao Mai', 'All fourteen restrain leakage, but selection depends on whether the loss involves Essence, urine, stool, sweat, Blood, fluids, discharge or Lung Qi.'],
    ['Stabilize Kidney and contain Essence', 'Shan Zhu Yu · Jin Ying Zi · Hai Piao Xiao · Fu Pen Zi', 'Shan Zhu Yu, Jin Ying Zi, Hai Piao Xiao and Fu Pen Zi address Kidney leakage; they differ in Blood, diarrhea, vaginal discharge, urination and vision emphasis.'],
    ['Bind the Intestines and stop diarrhea', 'Jin Ying Zi · Qian Shi · Lian Zi · He Zi · Chi Shi Zhi · Rou Dou Kou · Chun Pi', 'Jin Ying Zi, Qian Shi, Lian Zi, He Zi, Chi Shi Zhi, Rou Dou Kou and Chun Pi stop diarrhea; choose by deficiency, Cold, Heat, Dampness or bleeding.'],
    ['Contain Lung Qi and stop cough', 'Wu Wei Zi · Bai Guo · Wu Mei', 'Wu Wei Zi, Bai Guo and Wu Mei astringe Lung Qi; Bai Guo also stops discharge, Wu Mei generates fluids and Wu Wei Zi additionally stops sweating and diarrhea.'],
    ['Stop excessive sweating', 'Shan Zhu Yu · Wu Wei Zi · Fu Xiao Mai', 'Shan Zhu Yu, Wu Wei Zi and Fu Xiao Mai restrain sweating; Fu Xiao Mai is the most focused and cool, while Wu Wei Zi also generates fluids.'],
    ['Generate fluids while astringing', 'Wu Wei Zi · Wu Mei', 'Wu Wei Zi and Wu Mei both generate fluids while containing leakage; Wu Mei also treats roundworms and topical corns or warts.'],
    ['Vaginal discharge and Dampness', 'Hai Piao Xiao · Qian Shi · Bai Guo', 'Hai Piao Xiao, Qian Shi and Bai Guo stop discharge; Qian Shi strengthens the Spleen, Bai Guo clears Dampness and Hai Piao Xiao also stops bleeding and neutralizes acid.'],
    ['Calm the Spirit', 'Lian Zi · Fu Xiao Mai', 'Lian Zi and Fu Xiao Mai support the Heart and calm the Spirit while also treating leakage patterns.'],
    ['Toxicity and unresolved pathogens', 'Bai Guo · General category caution', 'Bai Guo is slightly toxic and must not be overused; astringents generally should not trap unresolved Exterior or excess pathogens.'],
];


$anchorcalmherbs = [
    ['Long Gu', 'Fossilia Ossis Mastodi', 'Sweet, astringent', 'Neutral', 'HT, LV, KD', 'Settles and calms the Spirit, anchors Liver Yang, prevents leakage of fluids and treats chronic non-healing sores topically.', 'Avoid in Damp-Heat or externally contracted excess conditions; traditionally counteracted by Shi Gao and should not be mixed with fish.', '15–30 g', 'Crush and cook 20–30 minutes longer.'],
    ['Mu Li', 'Ostreae Concha', 'Salty, astringent', 'Cool', 'LV, KD', 'Settles the Spirit and anchors Liver Yang; softens hardness, dissipates nodules, absorbs Stomach acid and prevents leakage.', 'Avoid in high fever from excess without sweating.', '9–30 g', 'Crush and cook 20–30 minutes longer.'],
    ['Ci Shi', 'Magnetitum', 'Acrid, salty', 'Cold', 'KD, LV', 'Anchors Liver Yang, tonifies Kidney and Liver, benefits hearing and vision, and helps the Kidney grasp Lung Qi.', 'Use cautiously with weak Spleen and Stomach; traditionally antagonizes Mu Dan Pi.', '15–30 g', 'Crush and cook 20–30 minutes longer.'],
    ['Zhen Zhu', 'Margarita', 'Sweet, salty', 'Cold', 'HT, LV', 'Sedates the Heart and calms the Spirit, clears the Liver and superficial visual obstruction, and promotes tissue healing topically.', 'Use cautiously during pregnancy.', '0.3–1 g', 'Use in pill or powder form.'],
];

$anchorcalmsimilarities = [
    ['Settle, anchor and calm the Spirit', 'Long Gu · Mu Li · Ci Shi · Zhen Zhu', 'All four are heavy settling substances, but differ in leakage, nodules, Kidney function, hearing, vision and topical tissue healing.'],
    ['Calm Liver Yang', 'Long Gu · Mu Li · Ci Shi', 'Long Gu, Mu Li and Ci Shi anchor rising Liver Yang; Long Gu emphasizes Spirit and leakage, Mu Li nodules, and Ci Shi Kidney, hearing and vision.'],
    ['Prevent leakage of fluids', 'Long Gu · Mu Li', 'Calcined Long Gu and Mu Li astringe sweating, Essence or other leakage while also calming agitation.'],
    ['Soften hardness and dissipate nodules', 'Mu Li', 'Mu Li is the principal choice for hard nodules and also absorbs Stomach acid; Long Gu is more focused on leakage and Spirit.'],
    ['Kidney, hearing and grasping Qi', 'Ci Shi', 'Ci Shi uniquely tonifies Kidney and Liver, benefits hearing and helps the Kidney grasp Lung Qi.'],
    ['Vision and topical healing', 'Ci Shi · Zhen Zhu', 'Ci Shi supports vision through Kidney-Liver tonification; Zhen Zhu clears superficial visual obstruction and promotes flesh generation topically.'],
    ['Mineral preparation and digestion', 'Long Gu · Mu Li · Ci Shi', 'Long Gu, Mu Li and Ci Shi require crushing and prolonged cooking; heavy minerals require caution when Spleen and Stomach are weak.'],
];

$nourishcalmherbs = [
    ['Suan Zao Ren', 'Ziziphi Spinosae Semen', 'Sweet, sour', 'Neutral', 'GB, HT, LV, SP', 'Nourishes Liver Blood, calms the Spirit and astringes excessive sweating.', 'Avoid in severe diarrhea or excess Heat.', '9–15 g', 'Crush before use.'],
    ['Bai Zi Ren', 'Platycladi Semen', 'Sweet', 'Neutral', 'HT, KD, LI', 'Nourishes Heart Blood and calms the Spirit, moistens the Intestines and treats Yin-deficiency night sweating.', 'Avoid in loose stools or phlegm disorders; traditionally antagonizes Ju Hua.', '3–9 g', '—'],
    ['Yuan Zhi', 'Polygalae Radix', 'Bitter, acrid', 'Slightly warm', 'HT, LU', 'Calms the Spirit, expels phlegm and opens the Heart orifices, stops cough and reduces abscesses and swelling.', 'Avoid in Yin deficiency with Heat; use cautiously with ulcers or gastritis; traditionally counteracted by Zhen Zhu and Li Lu.', '6–15 g', '—'],
    ['Ye Jiao Teng', 'Polygoni Multiflori Caulis', 'Sweet', 'Neutral', 'HT, LU', 'Nourishes Blood, calms the Spirit, unblocks channels and alleviates itching when used as an external wash.', 'No specific caution noted.', '15–30 g', '—'],
];

$nourishcalmsimilarities = [
    ['Nourish Blood and calm the Spirit', 'Suan Zao Ren · Bai Zi Ren · Yuan Zhi · Ye Jiao Teng', 'All four calm the Spirit, but differ in sweating, bowel dryness, phlegm, orifices, cough, channels, swelling and itching.'],
    ['Nourish Blood for insomnia', 'Suan Zao Ren · Bai Zi Ren · Ye Jiao Teng', 'Suan Zao Ren nourishes Liver Blood and astringes sweating; Bai Zi Ren nourishes Heart Blood and moistens the Intestines; Ye Jiao Teng also unblocks channels.'],
    ['Night sweating', 'Suan Zao Ren · Bai Zi Ren', 'Suan Zao Ren astringes sweating through Liver Blood nourishment, while Bai Zi Ren is useful when Yin-deficiency night sweats accompany dry stool.'],
    ['Moisten the Intestines', 'Bai Zi Ren', 'Bai Zi Ren is the distinctive Shen-calming herb that also lubricates the bowel; avoid it when loose stools predominate.'],
    ['Phlegm and open the orifices', 'Yuan Zhi', 'Yuan Zhi uniquely transforms phlegm, opens the Heart orifices, stops cough and reduces abscesses or swelling.'],
    ['Unblock channels and relieve itching', 'Ye Jiao Teng', 'Ye Jiao Teng nourishes Blood while opening channels and may be used externally to relieve itching.'],
    ['Deficiency versus excess agitation', 'General category caution', 'These nourishing herbs suit deficiency-related insomnia; severe excess Heat, phlegm or unresolved excess patterns require different treatment.'],
];


$orificeherbs = [
    ['Niu Huang', 'Bovis Calculus', 'Bitter', 'Cool', 'HT, LV', 'Clears Heart Heat, opens the orifices, awakens the Spirit and expels phlegm; clears Liver Heat, relieves toxicity, extinguishes Wind and stops tremor; drains Heat and relieves toxicity.', 'Avoid during pregnancy; use only in excess Heat patterns and avoid in Spleen/Stomach deficiency. Traditionally antagonizes Long Gu, Sheng Di, Long Dan Cao and Chang Shan; counteracts Niu Xi.', '0.2–0.5 g', 'Use in pill or powder form.'],
];

$orificesimilarities = [
    ['Open the orifices and awaken the Spirit', 'Niu Huang', 'Niu Huang clears Heart Heat, expels phlegm and restores consciousness when Heat and phlegm obstruct the orifices.'],
    ['Extinguish Wind and stop tremor', 'Niu Huang', 'Niu Huang clears Liver Heat, extinguishes internal Wind and stops tremor or convulsion from intense Heat.'],
    ['Clear Heat and relieve toxicity', 'Niu Huang', 'Niu Huang drains intense Heat and relieves Fire toxicity affecting the Heart, Liver or consciousness.'],
    ['Excess Heat requirement', 'Niu Huang', 'Niu Huang is reserved for strong excess Heat and is inappropriate in Spleen or Stomach deficiency.'],
    ['Pill or powder administration', 'Niu Huang', 'Niu Huang is given in small doses as a pill or powder rather than in a routine decoction.'],
];

$liverwindherbs = [
    ['Tian Ma', 'Gastrodiae Rhizoma', 'Sweet', 'Neutral', 'LV', 'Controls spasms and tremors; important for internal Liver Wind due to Heat or Cold; extinguishes Wind, relieves pain; used for painful obstruction or Bi syndrome.', 'No specific caution noted.', '3–9 g', '—'],
    ['Gou Teng', 'Uncariae Ramulus cum Uncis', 'Sweet', 'Slightly cold', 'PC, LV', 'Drains Liver Heat and pacifies Liver Yang; extinguishes Wind and controls spasms; releases the Exterior.', 'Use cautiously when Wind-Heat or excess Heat is absent.', '6–15 g', 'Do not cook for more than 20 minutes.'],
    ['Shi Jue Ming', 'Haliotidis Concha', 'Salty', 'Cold', 'KD, LV', 'Anchors Liver Yang and clears Liver Heat; drains Liver Fire; improves the eyes and clears superficial visual obstruction; traditionally used for gastric acidity and topically for trauma.', 'Traditionally counteracts Xuan Fu Hua.', '15–30 g', 'Crush and cook 60 minutes longer.'],
    ['Bai Ji Li', 'Tribuli Fructus', 'Acrid, bitter', 'Neutral', 'LV', 'Soothes Liver Qi; disperses Wind-Heat and brightens the eyes; dispels Wind and stops itching.', 'Avoid during pregnancy or in Qi/Blood deficiency.', '6–15 g', '—'],
    ['Dai Zhe Shi', 'Haematitum', 'Bitter', 'Cold', 'LV, PC, HT', 'Strongly directs rebellious Stomach and Lung Qi downward; cools Blood and stops bleeding; anchors Liver Yang.', 'Use cautiously during pregnancy; traditionally counteracts Fu Zi.', '9–30 g decoction; 1–3 g powder', 'Crush and cook 20–30 minutes longer.'],
    ['Di Long', 'Pheretima', 'Salty', 'Cold', 'UB, LV, LU, SP', 'Clears Heat and unblocks channels and collaterals; stops spasms and convulsions; calms wheezing; promotes urination; traditionally used in hypertension patterns.', 'Avoid in weak Spleen/Stomach or when excess Heat is absent.', '4.5–15 g', '—'],
];

$liverwindsimilarities = [
    ['Extinguish Liver Wind and control spasms', 'Tian Ma · Gou Teng · Shi Jue Ming · Bai Ji Li · Dai Zhe Shi · Di Long', 'All six calm internal Wind or its manifestations, with different emphasis on Yang rising, Heat, rebellious Qi, channels, pain or wheezing.'],
    ['Tian Ma and Gou Teng', 'Tian Ma · Gou Teng', 'Tian Ma is neutral and treats internal Wind from Heat or Cold; Gou Teng is slightly cold and emphasizes clearing Heat and pacifying Yang.'],
    ['Anchor Liver Yang', 'Shi Jue Ming · Dai Zhe Shi', 'Shi Jue Ming and Dai Zhe Shi are heavy substances that anchor rising Liver Yang; Shi Jue Ming emphasizes the eyes and Dai Zhe Shi rebellious Qi.'],
    ['Brighten the eyes', 'Gou Teng · Shi Jue Ming · Bai Ji Li', 'Gou Teng, Shi Jue Ming and Bai Ji Li address visual symptoms through clearing Liver Heat, pacifying Yang or dispersing Wind-Heat.'],
    ['Clear Heat and stop convulsions', 'Gou Teng · Di Long', 'Gou Teng and Di Long clear Heat and control spasms; Di Long additionally unblocks channels, calms wheezing and promotes urination.'],
    ['Direct rebellious Qi downward', 'Dai Zhe Shi', 'Dai Zhe Shi strongly descends rebellious Stomach and Lung Qi while anchoring Liver Yang and cooling Blood.'],
    ['Painful obstruction and channels', 'Tian Ma · Bai Ji Li · Di Long', 'Tian Ma treats painful obstruction, Bai Ji Li disperses Wind and Di Long unblocks channels and collaterals.'],
    ['Mineral preparation', 'Shi Jue Ming · Dai Zhe Shi', 'Shi Jue Ming and Dai Zhe Shi require crushing and prolonged cooking before other herbs.'],
];

$parasiteherbs = [
    ['Bing Lang', 'Arecae Semen', 'Acrid, bitter', 'Warm', 'LI, ST', 'Kills a wide variety of intestinal parasites, especially tapeworm; moves Qi in the Intestines for distension, constipation and tenesmus; promotes urination; used for malarial disorders.', 'Avoid in collapse or deficiency of Middle Qi.', '6–15 g', '—'],
];

$parasitesimilarities = [
    ['Expel intestinal parasites', 'Bing Lang', 'Bing Lang kills a broad range of intestinal parasites while moving Qi to relieve accumulation.'],
    ['Tapeworm emphasis', 'Bing Lang', 'Bing Lang is especially emphasized for tapeworm and associated abdominal symptoms.'],
    ['Move intestinal Qi', 'Bing Lang', 'Bing Lang relieves distension, constipation and tenesmus by moving Qi in the Intestines.'],
    ['Promote urination and treat malaria', 'Bing Lang', 'Beyond parasites, Bing Lang promotes urination and is traditionally used for malarial disorders.'],
    ['Middle Qi caution', 'Bing Lang', 'Because Bing Lang moves Qi downward, avoid it in collapse or marked deficiency of Middle Qi.'],
];

$externaluseherbs = [
    ['She Chuang Zi', 'Cnidii Fructus', 'Acrid, bitter', 'Warm, slightly toxic', 'KD', 'Dries Dampness, kills parasites and stops itching topically; expels Wind-Cold-Damp for painful obstruction; warms Kidney Yang when used internally.', 'Avoid in Lower Jiao Damp-Heat or Yin deficiency with Heat. Traditionally antagonizes Mu Dan Pi, Ba Dou and Bei Mu.', '3–9 g', 'Primarily used externally; internal use requires caution.'],
];

$externalusesimilarities = [
    ['Topical Dampness and itching', 'She Chuang Zi', 'She Chuang Zi dries Dampness and stops itching when applied externally.'],
    ['Kill parasites', 'She Chuang Zi', 'She Chuang Zi kills parasites affecting the skin or genital region and is commonly selected for external washes.'],
    ['Wind-Cold-Damp painful obstruction', 'She Chuang Zi', 'She Chuang Zi expels Wind-Cold-Damp to relieve painful obstruction when appropriately used.'],
    ['Warm Kidney Yang', 'She Chuang Zi', 'When used internally, She Chuang Zi warms Kidney Yang, distinct from its more common topical role.'],
    ['Toxicity and internal-use caution', 'She Chuang Zi', 'Its slight toxicity and warming nature require caution internally and avoidance in Damp-Heat or Yin-deficiency Heat.'],
];

$PAGE->set_url($pageurl);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title($ises ? 'Comparación de hierbas por categoría' : 'Herbs Category Comparison');
$PAGE->set_heading($ises ? 'Comparación de hierbas por categoría' : 'Herbs Category Comparison');
$PAGE->set_cacheable(false);

$PAGE->requires->js_init_code(<<<'JS'
(function() {
    const initialiseCategorySearch = function() {
        const search = document.getElementById('herb-category-search');
        const searchBox = document.querySelector('.herb-category-library__search');
        const index = document.getElementById('herb-category-index');
        const status = document.getElementById('herb-category-search-status');
        const clear = document.getElementById('herb-category-search-clear');
        const previousResult = document.getElementById('herb-category-search-previous');
        const nextResult = document.getElementById('herb-category-search-next');
        if (!search || !searchBox || !index || !status || !clear || !previousResult || !nextResult ||
                search.dataset.ready === 'true') {
            return;
        }

        search.dataset.ready = 'true';
        const categoryLinks = Array.from(index.querySelectorAll('a'));
        const sections = Array.from(document.querySelectorAll('.herb-category-library__section'));
        const initialSection = sections.find(function(section) {
            return !section.hidden;
        });
        const initialCategory = initialSection ? initialSection.id : '';
        let matches = [];
        let currentMatch = -1;
        let searchTimer = null;

        const normaliseWithMap = function(value) {
            let normalised = '';
            const map = [];
            for (let position = 0; position < value.length; position += 1) {
                const character = value[position];
                const folded = character.normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .toLocaleLowerCase();
                for (let foldedPosition = 0; foldedPosition < folded.length; foldedPosition += 1) {
                    normalised += folded[foldedPosition];
                    map.push(position);
                }
            }
            return {text: normalised, map: map};
        };

        const normalise = function(value) {
            return normaliseWithMap(value || '').text;
        };

        const clearHighlights = function() {
            document.querySelectorAll('mark.herb-category-library__search-highlight').forEach(function(mark) {
                const parent = mark.parentNode;
                mark.replaceWith(document.createTextNode(mark.textContent || ''));
                if (parent) {
                    parent.normalize();
                }
            });
            matches = [];
            currentMatch = -1;
        };

        const matchingOffsets = function(value, normalisedTerm) {
            const folded = normaliseWithMap(value);
            const offsets = [];
            let from = 0;
            let found = folded.text.indexOf(normalisedTerm, from);
            while (found !== -1) {
                const endInFoldedText = found + normalisedTerm.length - 1;
                offsets.push({
                    start: folded.map[found],
                    end: folded.map[endInFoldedText] + 1
                });
                from = found + Math.max(normalisedTerm.length, 1);
                found = folded.text.indexOf(normalisedTerm, from);
            }
            return offsets;
        };

        const highlightSection = function(section, normalisedTerm) {
            const walker = document.createTreeWalker(section, NodeFilter.SHOW_TEXT, {
                acceptNode: function(node) {
                    if (!node.nodeValue || !node.nodeValue.trim()) {
                        return NodeFilter.FILTER_REJECT;
                    }
                    const parent = node.parentElement;
                    if (!parent || parent.closest('.herb-category-library__pager') ||
                            parent.closest('script, style, button, input, textarea, mark')) {
                        return NodeFilter.FILTER_REJECT;
                    }
                    return NodeFilter.FILTER_ACCEPT;
                }
            });
            const textNodes = [];
            let textNode = walker.nextNode();
            while (textNode) {
                textNodes.push(textNode);
                textNode = walker.nextNode();
            }

            textNodes.forEach(function(node) {
                const offsets = matchingOffsets(node.nodeValue || '', normalisedTerm);
                let workingNode = node;
                for (let offsetIndex = offsets.length - 1; offsetIndex >= 0; offsetIndex -= 1) {
                    const offset = offsets[offsetIndex];
                    workingNode.splitText(offset.end);
                    const matchedNode = workingNode.splitText(offset.start);
                    const mark = document.createElement('mark');
                    mark.className = 'herb-category-library__search-highlight';
                    mark.textContent = matchedNode.nodeValue || '';
                    matchedNode.replaceWith(mark);
                }
            });
        };

        const updateCategoryPager = function(section, categoryIndex) {
            const pager = section ? section.querySelector('.herb-category-library__pager') : null;
            if (!pager || categoryIndex < 0) {
                return;
            }
            pager.replaceChildren();
            if (categoryIndex > 0) {
                const previous = document.createElement('a');
                previous.rel = 'prev';
                previous.href = categoryLinks[categoryIndex - 1].href;
                previous.textContent = '← ' + searchBox.dataset.previous;
                pager.appendChild(previous);
            }
            if (categoryIndex < categoryLinks.length - 1) {
                const next = document.createElement('a');
                next.rel = 'next';
                next.href = categoryLinks[categoryIndex + 1].href;
                next.textContent = searchBox.dataset.next + ' →';
                pager.appendChild(next);
            }
        };

        const showCategory = function(category) {
            let activeIndex = -1;
            sections.forEach(function(section) {
                section.hidden = section.id !== category;
            });
            categoryLinks.forEach(function(link, linkIndex) {
                const linkCategory = new URL(link.href, window.location.href).searchParams.get('category') || '';
                if (linkCategory === category) {
                    link.setAttribute('aria-current', 'page');
                    activeIndex = linkIndex;
                } else {
                    link.removeAttribute('aria-current');
                }
            });
            if (activeIndex >= 0) {
                updateCategoryPager(document.getElementById(category), activeIndex);
            }
        };

        const updateStatus = function() {
            if (matches.length === 0) {
                status.textContent = search.value.trim() === '' ? '' : status.dataset.none;
                return;
            }
            const matchingCategories = new Set(matches.map(function(mark) {
                const section = mark.closest('.herb-category-library__section');
                return section ? section.id : '';
            }));
            status.textContent = status.dataset.results
                .replace('{current}', String(currentMatch + 1))
                .replace('{total}', String(matches.length))
                .replace('{categories}', String(matchingCategories.size));
        };

        const activateMatch = function(matchIndex, shouldScroll) {
            if (matches.length === 0) {
                currentMatch = -1;
                updateStatus();
                return;
            }
            currentMatch = (matchIndex + matches.length) % matches.length;
            matches.forEach(function(mark) {
                mark.classList.remove('is-current');
            });
            const current = matches[currentMatch];
            current.classList.add('is-current');
            const section = current.closest('.herb-category-library__section');
            if (section) {
                showCategory(section.id);
            }
            updateStatus();
            if (shouldScroll) {
                current.scrollIntoView({behavior: 'smooth', block: 'center', inline: 'nearest'});
            }
        };

        const resetSearch = function() {
            clearTimeout(searchTimer);
            clearHighlights();
            categoryLinks.forEach(function(link) {
                link.hidden = false;
            });
            clear.hidden = true;
            previousResult.hidden = true;
            nextResult.hidden = true;
            showCategory(initialCategory);
            status.textContent = '';
        };

        const updateSearch = function(shouldScroll) {
            clearHighlights();
            const normalisedTerm = normalise(search.value.trim());
            if (normalisedTerm === '') {
                resetSearch();
                return;
            }

            sections.forEach(function(section) {
                highlightSection(section, normalisedTerm);
            });
            matches = Array.from(document.querySelectorAll('mark.herb-category-library__search-highlight'));
            const matchingCategories = new Set(matches.map(function(mark) {
                const section = mark.closest('.herb-category-library__section');
                return section ? section.id : '';
            }));
            categoryLinks.forEach(function(link) {
                const category = new URL(link.href, window.location.href).searchParams.get('category') || '';
                link.hidden = !matchingCategories.has(category);
            });

            clear.hidden = false;
            previousResult.hidden = matches.length < 2;
            nextResult.hidden = matches.length < 2;

            if (matches.length > 0) {
                const visibleMatch = matches.findIndex(function(mark) {
                    const section = mark.closest('.herb-category-library__section');
                    return section && !section.hidden;
                });
                activateMatch(visibleMatch >= 0 ? visibleMatch : 0, shouldScroll);
            } else {
                sections.forEach(function(section) {
                    section.hidden = true;
                });
                categoryLinks.forEach(function(link) {
                    link.removeAttribute('aria-current');
                });
                updateStatus();
            }
        };

        const scheduleSearch = function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() {
                updateSearch(false);
            }, 120);
        };

        search.addEventListener('input', scheduleSearch);
        search.addEventListener('keydown', function(event) {
            if (event.key === 'Enter' && matches.length > 0) {
                event.preventDefault();
                activateMatch(currentMatch + (event.shiftKey ? -1 : 1), true);
            } else if (event.key === 'Escape') {
                search.value = '';
                resetSearch();
                search.focus();
            }
        });
        previousResult.addEventListener('click', function() {
            activateMatch(currentMatch - 1, true);
        });
        nextResult.addEventListener('click', function() {
            activateMatch(currentMatch + 1, true);
        });
        clear.addEventListener('click', function() {
            search.value = '';
            resetSearch();
            search.focus();
        });
        resetSearch();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialiseCategorySearch, {once: true});
    } else {
        initialiseCategorySearch();
    }
}());
JS
);

echo $OUTPUT->header();
?>
<style>
.herb-category-library{width:min(1500px,calc(100% - 28px));margin:24px auto 56px;color:#102a43}
.herb-category-library__top{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;padding:24px;border:1px solid #b9d8d2;border-top:5px solid #087c69;border-radius:14px;background:#fff}
.herb-category-library__eyebrow{margin:0 0 5px;color:#087c69;font-size:12px;font-weight:900;letter-spacing:.09em;text-transform:uppercase}
.herb-category-library h2{margin:0 0 8px;font-size:clamp(26px,4vw,40px)}
.herb-category-library__intro{max-width:820px;margin:0;color:#526b84;line-height:1.55}
.herb-category-library__language-state{display:inline-flex;margin:12px 0 0;padding:6px 10px;border-radius:999px;background:#e5f6f2;color:#086b5c;font-size:12px;font-weight:900}
.herb-category-library__actions{display:flex;flex-wrap:wrap;justify-content:flex-end;gap:10px}
.herb-category-library__back{display:inline-flex;min-height:42px;align-items:center;padding:9px 15px;border:1px solid #b9c9d5;border-radius:9px;background:#fff;color:#0f4c81;font-weight:800;text-decoration:none;white-space:nowrap}
.herb-category-library__back:hover{background:#eef7f5;text-decoration:none}
.herb-category-library__search{margin:18px 0 12px;padding:16px;border:1px solid #b9d8d2;border-radius:12px;background:#f7fbfb}
.herb-category-library__search label{display:block;margin:0 0 8px;color:#102a43;font-weight:900}
.herb-category-library__search-control{display:flex;align-items:center;gap:9px;padding:0 10px;border:2px solid #8fb9b1;border-radius:10px;background:#fff}
.herb-category-library__search-control:focus-within{border-color:#087c69;box-shadow:0 0 0 3px rgba(8,124,105,.14)}
.herb-category-library__search-icon{color:#087c69;font-size:19px;line-height:1}
.herb-category-library__search input{width:100%;min-height:44px;padding:8px 2px;border:0;background:transparent;color:#102a43;font-size:16px;outline:0}
.herb-category-library__search-action{min-height:34px;padding:6px 10px;border:0;border-radius:8px;background:#e5f6f2;color:#075f52;font-weight:800;cursor:pointer;white-space:nowrap}
.herb-category-library__search-action:hover,.herb-category-library__search-action:focus{background:#087c69;color:#fff}
.herb-category-library__search-status{min-height:20px;margin:7px 1px 0;color:#526b84;font-size:13px;font-weight:700}
.herb-category-library__search-highlight{padding:0 2px;border-radius:3px;background:#fff3a3;color:inherit;box-shadow:inset 0 -2px 0 #e6b800}
.herb-category-library__search-highlight.is-current{background:#ffdc35;box-shadow:0 0 0 4px rgba(255,193,7,.48),0 3px 12px rgba(151,105,0,.38)}
@media (max-width:760px){.herb-category-library__search-control{flex-wrap:wrap;padding:7px 10px}.herb-category-library__search input{flex:1 1 calc(100% - 36px)}.herb-category-library__search-action{flex:1 1 auto}}
.herb-category-library__index{display:flex;flex-wrap:wrap;gap:10px;margin:12px 0 18px}
.herb-category-library__index a{padding:9px 13px;border-radius:999px;background:#087c69;color:#fff;font-weight:800;text-decoration:none}
.herb-category-library__index a[aria-current="page"]{background:#064f46;box-shadow:0 0 0 3px rgba(8,124,105,.18)}
.herb-category-library__empty{margin-top:20px;padding:22px;border:1px dashed #8fb9b1;border-radius:12px;background:#f4fbfa;color:#294c5f;font-weight:700;text-align:center}
.herb-category-library__section{margin-top:20px;padding:20px;border:1px solid #b9d8d2;border-radius:14px;background:#fff;box-shadow:0 8px 24px rgba(15,76,129,.06)}
.herb-category-library__section-heading{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap}
.herb-category-library__section h3{margin:0;font-size:25px}
.herb-category-library__pager{display:flex;align-items:center;justify-content:flex-end;gap:8px;margin-left:auto}
.herb-category-library__pager a{display:inline-flex;min-height:38px;align-items:center;padding:8px 13px;border:1px solid #8fb9b1;border-radius:9px;background:#f4fbfa;color:#075f52;font-weight:800;text-decoration:none;white-space:nowrap}
.herb-category-library__pager a:hover,.herb-category-library__pager a:focus{background:#087c69;color:#fff;text-decoration:none}
@media (max-width:640px){.herb-category-library__section-heading{display:block}.herb-category-library__pager{justify-content:space-between;margin:12px 0 0}.herb-category-library__pager a:only-child{margin-left:auto}}
.herb-category-library__summary{margin:7px 0 16px;color:#526b84}
.herb-category-library__scroll{overflow-x:auto;border:1px solid #a9bbc8;border-radius:10px}
.herb-category-library table{width:100%;border-collapse:collapse;font-size:13px;line-height:1.35}
.herb-category-library th,.herb-category-library td{padding:9px 10px;border:1px solid #b9c7d1;vertical-align:top;overflow-wrap:anywhere}
.herb-category-library thead th{background:#d8f2ef;color:#102a43;text-align:left}
.herb-category-library__comparison-table{min-width:1180px;table-layout:fixed}
.herb-category-library__comparison-table col:nth-child(1){width:14%}
.herb-category-library__comparison-table col:nth-child(2){width:7%}
.herb-category-library__comparison-table col:nth-child(3){width:7%}
.herb-category-library__comparison-table col:nth-child(4){width:7%}
.herb-category-library__comparison-table col:nth-child(5){width:24%}
.herb-category-library__comparison-table col:nth-child(6){width:19%}
.herb-category-library__comparison-table col:nth-child(7){width:8%}
.herb-category-library__comparison-table col:nth-child(8){width:14%}
.herb-category-library__herb-identity{background:#eef9f7}
.herb-category-library__herb-identity strong{display:block;color:#102a43;font-size:14px}
.herb-category-library__herb-identity small{display:block;margin-top:4px;color:#526b84;font-size:12px;font-style:italic;font-weight:600}
.herb-category-library__compact{font-size:12px}
.herb-category-library__dosage{white-space:normal;text-align:center;overflow-wrap:anywhere}
.herb-category-library__similarities{margin:18px 0}
.herb-category-library__similarities h4{margin:0;padding:12px 14px;border-radius:10px 10px 0 0;background:#087c69;color:#fff;font-size:20px}
.herb-category-library__similarities table td:nth-child(1){font-weight:800;background:#eef9f7}
.herb-category-library__similarities table td:nth-child(2){font-weight:700}
.herb-category-library__foundation{margin:14px 0 0;padding:13px 15px;border-left:5px solid #087c69;background:#e5f6f2}
@media(max-width:720px){.herb-category-library__top{flex-direction:column}.herb-category-library__actions{width:100%;flex-direction:column}.herb-category-library__back{width:100%;justify-content:center}}
</style>
<main class="herb-category-library" data-language="<?php echo $ises ? 'es' : 'en'; ?>">
    <header class="herb-category-library__top">
        <div>
            <p class="herb-category-library__eyebrow"><?php echo s($ises ? 'Biblioteca comparativa' : 'Comparative library'); ?></p>
            <h2><?php echo s($ises ? 'Comparación de hierbas por categoría' : 'Herbs Category Comparison'); ?></h2>
            <p class="herb-category-library__intro"><?php echo s($ises
                ? 'Compara propiedades, canales, funciones, precauciones y semejanzas clínicas dentro de cada categoría.'
                : 'Compare properties, channels, functions, cautions and clinical similarities within each herb category.'); ?></p>
            <p class="herb-category-library__language-state"><?php echo s($ises ? 'Idioma: Español' : 'Language: English'); ?></p>
        </div>
        <div class="herb-category-library__actions">
            <?php if ($selectedcategory !== ''): ?>
                <a class="herb-category-library__back" href="<?php echo $categoriesurl->out(false); ?>"><?php echo s($ises ? '← Categorías' : '← Categories'); ?></a>
            <?php endif; ?>
            <a class="herb-category-library__back" href="<?php echo $backurl->out(false); ?>"><?php echo s($ises ? '← Actividades Board Level' : '← Board Level Activities'); ?></a>
            <a class="herb-category-library__back" hreflang="<?php echo $ises ? 'en' : 'es'; ?>" href="<?php echo $languageurl->out(false); ?>"><?php echo s($ises ? 'English' : 'Español'); ?></a>
        </div>
    </header>

    <div class="herb-category-library__search"
        data-previous="<?php echo s($ises ? 'Anterior' : 'Previous'); ?>"
        data-next="<?php echo s($ises ? 'Siguiente' : 'Next'); ?>">
        <label for="herb-category-search"><?php echo s($ises ? 'Buscar categorías o hierbas' : 'Search categories or herbs'); ?></label>
        <div class="herb-category-library__search-control">
            <span class="herb-category-library__search-icon" aria-hidden="true">⌕</span>
            <input id="herb-category-search" type="search" autocomplete="off"
                placeholder="<?php echo s($ises ? 'Escribe una categoría, hierba, función o precaución…' : 'Type a category, herb, function or caution…'); ?>">
            <button class="herb-category-library__search-action" id="herb-category-search-previous" type="button" hidden><?php echo s($ises ? '↑ Coincidencia anterior' : '↑ Previous match'); ?></button>
            <button class="herb-category-library__search-action" id="herb-category-search-next" type="button" hidden><?php echo s($ises ? 'Siguiente coincidencia ↓' : 'Next match ↓'); ?></button>
            <button class="herb-category-library__search-action" id="herb-category-search-clear" type="button" hidden><?php echo s($ises ? 'Borrar' : 'Clear'); ?></button>
        </div>
        <p class="herb-category-library__search-status" id="herb-category-search-status" aria-live="polite"
            data-none="<?php echo s($ises ? 'No se encontraron coincidencias.' : 'No matches found.'); ?>"
            data-results="<?php echo s($ises ? '{current} de {total} coincidencias en {categories} categorías.' : '{current} of {total} matches in {categories} categories.'); ?>"></p>
    </div>

    <nav class="herb-category-library__index" id="herb-category-index" aria-label="<?php echo s($ises ? 'Índice de categorías' : 'Category index'); ?>">
        <a href="<?php echo $categoryurls['disperse-wind-cold']->out(false); ?>"<?php echo $selectedcategory === 'disperse-wind-cold' ? ' aria-current="page"' : ''; ?>><?php echo s(board_herb_category_localize('1. Disperse Wind-Cold', $ises)); ?></a>
        <a href="<?php echo $categoryurls['disperse-wind-heat']->out(false); ?>"<?php echo $selectedcategory === 'disperse-wind-heat' ? ' aria-current="page"' : ''; ?>><?php echo s(board_herb_category_localize('2. Disperse Wind-Heat', $ises)); ?></a>
        <a href="<?php echo $categoryurls['clear-and-purge-heat']->out(false); ?>"<?php echo $selectedcategory === 'clear-and-purge-heat' ? ' aria-current="page"' : ''; ?>><?php echo s(board_herb_category_localize('3. Clear and Purge Heat', $ises)); ?></a>
        <a href="<?php echo $categoryurls['clear-blood-heat']->out(false); ?>"<?php echo $selectedcategory === 'clear-blood-heat' ? ' aria-current="page"' : ''; ?>><?php echo s(board_herb_category_localize('4. Clear Blood-Heat', $ises)); ?></a>
        <a href="<?php echo $categoryurls['clear-damp-heat']->out(false); ?>"<?php echo $selectedcategory === 'clear-damp-heat' ? ' aria-current="page"' : ''; ?>><?php echo s(board_herb_category_localize('5. Clear Damp-Heat', $ises)); ?></a>
        <a href="<?php echo $categoryurls['clear-toxic-heat']->out(false); ?>"<?php echo $selectedcategory === 'clear-toxic-heat' ? ' aria-current="page"' : ''; ?>><?php echo s(board_herb_category_localize('6. Clear Toxic-Heat', $ises)); ?></a>
        <a href="<?php echo $categoryurls['clear-deficient-heat']->out(false); ?>"<?php echo $selectedcategory === 'clear-deficient-heat' ? ' aria-current="page"' : ''; ?>><?php echo s(board_herb_category_localize('7. Clear Deficient-Heat', $ises)); ?></a>
        <a href="<?php echo $categoryurls['purgatives']->out(false); ?>"<?php echo $selectedcategory === 'purgatives' ? ' aria-current="page"' : ''; ?>><?php echo s(board_herb_category_localize('8. Purgatives', $ises)); ?></a>
        <a href="<?php echo $categoryurls['laxatives']->out(false); ?>"<?php echo $selectedcategory === 'laxatives' ? ' aria-current="page"' : ''; ?>><?php echo s(board_herb_category_localize('9. Laxatives', $ises)); ?></a>
        <a href="<?php echo $categoryurls['cathartics']->out(false); ?>"<?php echo $selectedcategory === 'cathartics' ? ' aria-current="page"' : ''; ?>><?php echo s(board_herb_category_localize('10. Cathartics', $ises)); ?></a>
        <a href="<?php echo $categoryurls['diuretics']->out(false); ?>"<?php echo $selectedcategory === 'diuretics' ? ' aria-current="page"' : ''; ?>><?php echo s(board_herb_category_localize('11. Diuretics', $ises)); ?></a>
        <a href="<?php echo $categoryurls['dispel-wind-damp']->out(false); ?>"<?php echo $selectedcategory === 'dispel-wind-damp' ? ' aria-current="page"' : ''; ?>><?php echo s(board_herb_category_localize('12. Dispel Wind-Damp', $ises)); ?></a>
        <a href="<?php echo $categoryurls['dissolve-hot-phlegm']->out(false); ?>"<?php echo $selectedcategory === 'dissolve-hot-phlegm' ? ' aria-current="page"' : ''; ?>><?php echo s(board_herb_category_localize('13. Dissolve Hot Phlegm', $ises)); ?></a>
        <a href="<?php echo $categoryurls['dissolve-cold-phlegm']->out(false); ?>"<?php echo $selectedcategory === 'dissolve-cold-phlegm' ? ' aria-current="page"' : ''; ?>><?php echo s(board_herb_category_localize('14. Dissolve Cold Phlegm', $ises)); ?></a>
        <a href="<?php echo $categoryurls['stop-cough-wheezing']->out(false); ?>"<?php echo $selectedcategory === 'stop-cough-wheezing' ? ' aria-current="page"' : ''; ?>><?php echo s(board_herb_category_localize('15. Stop Cough and Wheezing', $ises)); ?></a>
        <a href="<?php echo $categoryurls['aromatics-for-damp']->out(false); ?>"<?php echo $selectedcategory === 'aromatics-for-damp' ? ' aria-current="page"' : ''; ?>><?php echo s(board_herb_category_localize('16. Aromatics for Damp', $ises)); ?></a>
        <a href="<?php echo $categoryurls['digestives']->out(false); ?>"<?php echo $selectedcategory === 'digestives' ? ' aria-current="page"' : ''; ?>><?php echo s(board_herb_category_localize('17. Digestives', $ises)); ?></a>
        <a href="<?php echo $categoryurls['regulate-qi']->out(false); ?>"<?php echo $selectedcategory === 'regulate-qi' ? ' aria-current="page"' : ''; ?>><?php echo s(board_herb_category_localize('18. Regulate Qi', $ises)); ?></a>
        <a href="<?php echo $categoryurls['stop-bleeding']->out(false); ?>"<?php echo $selectedcategory === 'stop-bleeding' ? ' aria-current="page"' : ''; ?>><?php echo s(board_herb_category_localize('19. Stop Bleeding', $ises)); ?></a>
        <a href="<?php echo $categoryurls['activate-blood']->out(false); ?>"<?php echo $selectedcategory === 'activate-blood' ? ' aria-current="page"' : ''; ?>><?php echo s(board_herb_category_localize('20. Activate Blood', $ises)); ?></a>
        <a href="<?php echo $categoryurls['warm-interior']->out(false); ?>"<?php echo $selectedcategory === 'warm-interior' ? ' aria-current="page"' : ''; ?>><?php echo s(board_herb_category_localize('21. Warm Interior', $ises)); ?></a>
        <a href="<?php echo $categoryurls['qi-tonics']->out(false); ?>"<?php echo $selectedcategory === 'qi-tonics' ? ' aria-current="page"' : ''; ?>><?php echo s(board_herb_category_localize('22. Qi Tonics', $ises)); ?></a>
        <a href="<?php echo $categoryurls['blood-tonics']->out(false); ?>"<?php echo $selectedcategory === 'blood-tonics' ? ' aria-current="page"' : ''; ?>><?php echo s(board_herb_category_localize('23. Blood Tonics', $ises)); ?></a>
        <a href="<?php echo $categoryurls['yang-tonics']->out(false); ?>"<?php echo $selectedcategory === 'yang-tonics' ? ' aria-current="page"' : ''; ?>><?php echo s(board_herb_category_localize('24. Yang Tonics', $ises)); ?></a>
        <a href="<?php echo $categoryurls['yin-tonics']->out(false); ?>"<?php echo $selectedcategory === 'yin-tonics' ? ' aria-current="page"' : ''; ?>><?php echo s(board_herb_category_localize('25. Yin Tonics', $ises)); ?></a>
        <a href="<?php echo $categoryurls['astringents']->out(false); ?>"<?php echo $selectedcategory === 'astringents' ? ' aria-current="page"' : ''; ?>><?php echo s(board_herb_category_localize('26. Astringents', $ises)); ?></a>
        <a href="<?php echo $categoryurls['anchor-calm-shen']->out(false); ?>"<?php echo $selectedcategory === 'anchor-calm-shen' ? ' aria-current="page"' : ''; ?>><?php echo s(board_herb_category_localize('27. Anchor & Calm Shen', $ises)); ?></a>
        <a href="<?php echo $categoryurls['nourish-calm-shen']->out(false); ?>"<?php echo $selectedcategory === 'nourish-calm-shen' ? ' aria-current="page"' : ''; ?>><?php echo s(board_herb_category_localize('28. Nourish & Calm Shen', $ises)); ?></a>
        <a href="<?php echo $categoryurls['orifice-opening-aromatics']->out(false); ?>"<?php echo $selectedcategory === 'orifice-opening-aromatics' ? ' aria-current="page"' : ''; ?>><?php echo s(board_herb_category_localize('29. Orifice Opening Aromatics', $ises)); ?></a>
        <a href="<?php echo $categoryurls['extinguish-liver-wind']->out(false); ?>"<?php echo $selectedcategory === 'extinguish-liver-wind' ? ' aria-current="page"' : ''; ?>><?php echo s(board_herb_category_localize('30. Extinguish Liver Wind', $ises)); ?></a>
        <a href="<?php echo $categoryurls['expel-parasites']->out(false); ?>"<?php echo $selectedcategory === 'expel-parasites' ? ' aria-current="page"' : ''; ?>><?php echo s(board_herb_category_localize('31. Expel Parasites', $ises)); ?></a>
        <a href="<?php echo $categoryurls['external-use']->out(false); ?>"<?php echo $selectedcategory === 'external-use' ? ' aria-current="page"' : ''; ?>><?php echo s(board_herb_category_localize('32. External Use', $ises)); ?></a>
    </nav>

    <?php if ($selectedcategory === ''): ?>
        <div class="herb-category-library__empty"><?php echo s($ises
            ? 'Selecciona una categoría para abrir solamente su tabla comparativa.'
            : 'Select a category to open only its comparison table.'); ?></div>
    <?php endif; ?>

    <section class="herb-category-library__section" id="disperse-wind-cold"<?php echo $selectedcategory === 'disperse-wind-cold' ? '' : ' hidden'; ?>>
        <div class="herb-category-library__section-heading">
            <h3><?php echo s(board_herb_category_localize('1. Disperse Wind-Cold', $ises)); ?></h3>
            <nav class="herb-category-library__pager" aria-label="<?php echo s(board_herb_category_localize('Category navigation', $ises)); ?>">
                <?php if ($previouscategoryurl !== null): ?>
                    <a rel="prev" href="<?php echo $previouscategoryurl->out(false); ?>"><?php echo s('← ' . board_herb_category_localize('Previous', $ises)); ?></a>
                <?php endif; ?>
                <?php if ($nextcategoryurl !== null): ?>
                    <a rel="next" href="<?php echo $nextcategoryurl->out(false); ?>"><?php echo s(board_herb_category_localize('Next', $ises) . ' →'); ?></a>
                <?php endif; ?>
            </nav>
        </div>
        <p class="herb-category-library__summary"><?php echo s(board_herb_category_localize('Board Guideline 2026 · 15 herbs · Common foundation: predominantly acrid herbs that release exterior Wind-Cold.', $ises)); ?></p>
        <div class="herb-category-library__similarities">
            <h4><?php echo s(board_herb_category_localize('Similarities and Clinical Overlaps', $ises)); ?></h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th><?php echo s(board_herb_category_localize('Comparison', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Herbs', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Shared clinical relationship', $ises)); ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($similarities as $similarity): ?>
                        <tr><td><?php echo s(board_herb_category_localize($similarity[0], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[1], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[2], $ises)); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong><?php echo s(board_herb_category_localize('Selection focus:', $ises)); ?></strong> <?php echo s(board_herb_category_localize('sweating strength, pain location, nasal involvement, Lung symptoms and Dampness.', $ises)); ?></p>
        </div>

        <div class="herb-category-library__scroll">
            <table class="herb-category-library__comparison-table">
                <colgroup>
                    <col><col><col><col><col><col><col><col>
                </colgroup>
                <thead>
                    <tr>
                        <th><?php echo s(board_herb_category_localize('Herb / Pharmaceutical name', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Taste', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Nature', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Channels', $ises)); ?></th>
                        <th><?php echo s(board_herb_category_localize('Main functions and indications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Cautions / contraindications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Dosage', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Preparation', $ises)); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($herbs as $herb): ?>
                    <tr>
                        <th class="herb-category-library__herb-identity" scope="row">
                            <strong><?php echo s($herb[0]); ?></strong>
                            <small><?php echo s($herb[1]); ?></small>
                        </th>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[2], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[3], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[4], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[5], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[6], $ises)); ?></td>
                        <td class="herb-category-library__compact herb-category-library__dosage"><?php echo s(board_herb_category_localize($herb[7], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[8], $ises)); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="herb-category-library__section" id="disperse-wind-heat"<?php echo $selectedcategory === 'disperse-wind-heat' ? '' : ' hidden'; ?>>
        <div class="herb-category-library__section-heading">
            <h3><?php echo s(board_herb_category_localize('2. Disperse Wind-Heat', $ises)); ?></h3>
            <nav class="herb-category-library__pager" aria-label="<?php echo s(board_herb_category_localize('Category navigation', $ises)); ?>">
                <?php if ($previouscategoryurl !== null): ?>
                    <a rel="prev" href="<?php echo $previouscategoryurl->out(false); ?>"><?php echo s('← ' . board_herb_category_localize('Previous', $ises)); ?></a>
                <?php endif; ?>
                <?php if ($nextcategoryurl !== null): ?>
                    <a rel="next" href="<?php echo $nextcategoryurl->out(false); ?>"><?php echo s(board_herb_category_localize('Next', $ises) . ' →'); ?></a>
                <?php endif; ?>
            </nav>
        </div>
        <p class="herb-category-library__summary"><?php echo s(board_herb_category_localize('Board Guideline 2026 · 11 herbs · Common foundation: predominantly cool and acrid herbs that disperse exterior Wind-Heat.', $ises)); ?></p>

        <div class="herb-category-library__similarities">
            <h4><?php echo s(board_herb_category_localize('Similarities and Clinical Overlaps', $ises)); ?></h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th><?php echo s(board_herb_category_localize('Comparison', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Herbs', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Shared clinical relationship', $ises)); ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($windheatsimilarities as $similarity): ?>
                        <tr><td><?php echo s(board_herb_category_localize($similarity[0], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[1], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[2], $ises)); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong><?php echo s(board_herb_category_localize('Selection focus:', $ises)); ?></strong> <?php echo s(board_herb_category_localize('throat and rash involvement, eye symptoms, headache location, Lung dryness, Shaoyang signs and sinking Qi.', $ises)); ?></p>
        </div>

        <div class="herb-category-library__scroll">
            <table class="herb-category-library__comparison-table">
                <colgroup>
                    <col><col><col><col><col><col><col><col>
                </colgroup>
                <thead>
                    <tr>
                        <th><?php echo s(board_herb_category_localize('Herb / Pharmaceutical name', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Taste', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Nature', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Channels', $ises)); ?></th>
                        <th><?php echo s(board_herb_category_localize('Main functions and indications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Cautions / contraindications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Dosage', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Preparation', $ises)); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($windheatherbs as $herb): ?>
                    <tr>
                        <th class="herb-category-library__herb-identity" scope="row">
                            <strong><?php echo s($herb[0]); ?></strong>
                            <small><?php echo s($herb[1]); ?></small>
                        </th>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[2], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[3], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[4], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[5], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[6], $ises)); ?></td>
                        <td class="herb-category-library__compact herb-category-library__dosage"><?php echo s(board_herb_category_localize($herb[7], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[8], $ises)); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>


    <section class="herb-category-library__section" id="clear-and-purge-heat"<?php echo $selectedcategory === 'clear-and-purge-heat' ? '' : ' hidden'; ?>>
        <div class="herb-category-library__section-heading">
            <h3><?php echo s(board_herb_category_localize('3. Clear and Purge Heat', $ises)); ?></h3>
            <nav class="herb-category-library__pager" aria-label="<?php echo s(board_herb_category_localize('Category navigation', $ises)); ?>">
                <?php if ($previouscategoryurl !== null): ?>
                    <a rel="prev" href="<?php echo $previouscategoryurl->out(false); ?>"><?php echo s('← ' . board_herb_category_localize('Previous', $ises)); ?></a>
                <?php endif; ?>
                <?php if ($nextcategoryurl !== null): ?>
                    <a rel="next" href="<?php echo $nextcategoryurl->out(false); ?>"><?php echo s(board_herb_category_localize('Next', $ises) . ' →'); ?></a>
                <?php endif; ?>
            </nav>
        </div>
        <p class="herb-category-library__summary"><?php echo s(board_herb_category_localize('Board Guideline 2026 · 8 herbs · Common foundation: cold herbs that clear internal Heat, drain Fire or protect fluids.', $ises)); ?></p>

        <div class="herb-category-library__similarities">
            <h4><?php echo s(board_herb_category_localize('Similarities and Clinical Overlaps', $ises)); ?></h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th><?php echo s(board_herb_category_localize('Comparison', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Herbs', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Shared clinical relationship', $ises)); ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($clearheatsimilarities as $similarity): ?>
                        <tr><td><?php echo s(board_herb_category_localize($similarity[0], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[1], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[2], $ises)); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong><?php echo s(board_herb_category_localize('Selection focus:', $ises)); ?></strong> <?php echo s(board_herb_category_localize('severity of Fire, fluid injury, Lung or Stomach involvement, irritability, urinary signs, eye symptoms and nodules.', $ises)); ?></p>
        </div>

        <div class="herb-category-library__scroll">
            <table class="herb-category-library__comparison-table">
                <colgroup>
                    <col><col><col><col><col><col><col><col>
                </colgroup>
                <thead>
                    <tr>
                        <th><?php echo s(board_herb_category_localize('Herb / Pharmaceutical name', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Taste', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Nature', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Channels', $ises)); ?></th>
                        <th><?php echo s(board_herb_category_localize('Main functions and indications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Cautions / contraindications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Dosage', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Preparation', $ises)); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($clearheatherbs as $herb): ?>
                    <tr>
                        <th class="herb-category-library__herb-identity" scope="row">
                            <strong><?php echo s($herb[0]); ?></strong>
                            <small><?php echo s($herb[1]); ?></small>
                        </th>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[2], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[3], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[4], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[5], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[6], $ises)); ?></td>
                        <td class="herb-category-library__compact herb-category-library__dosage"><?php echo s(board_herb_category_localize($herb[7], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[8], $ises)); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>


    <section class="herb-category-library__section" id="clear-blood-heat"<?php echo $selectedcategory === 'clear-blood-heat' ? '' : ' hidden'; ?>>
        <div class="herb-category-library__section-heading">
            <h3><?php echo s(board_herb_category_localize('4. Clear Blood-Heat', $ises)); ?></h3>
            <nav class="herb-category-library__pager" aria-label="<?php echo s(board_herb_category_localize('Category navigation', $ises)); ?>">
                <?php if ($previouscategoryurl !== null): ?>
                    <a rel="prev" href="<?php echo $previouscategoryurl->out(false); ?>"><?php echo s('← ' . board_herb_category_localize('Previous', $ises)); ?></a>
                <?php endif; ?>
                <?php if ($nextcategoryurl !== null): ?>
                    <a rel="next" href="<?php echo $nextcategoryurl->out(false); ?>"><?php echo s(board_herb_category_localize('Next', $ises) . ' →'); ?></a>
                <?php endif; ?>
            </nav>
        </div>
        <p class="herb-category-library__summary"><?php echo s(board_herb_category_localize('Board Guideline 2026 · 5 herbs · Common foundation: cool Blood-level Heat and protect against injury from Fire.', $ises)); ?></p>

        <div class="herb-category-library__similarities">
            <h4><?php echo s(board_herb_category_localize('Similarities and Clinical Overlaps', $ises)); ?></h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th><?php echo s(board_herb_category_localize('Comparison', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Herbs', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Shared clinical relationship', $ises)); ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($bloodheatsimilarities as $similarity): ?>
                        <tr><td><?php echo s(board_herb_category_localize($similarity[0], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[1], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[2], $ises)); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong><?php echo s(board_herb_category_localize('Selection focus:', $ises)); ?></strong> <?php echo s(board_herb_category_localize('toxin severity, bleeding, Yin injury, deficiency Heat, Blood stasis, throat swelling and skin manifestations.', $ises)); ?></p>
        </div>

        <div class="herb-category-library__scroll">
            <table class="herb-category-library__comparison-table">
                <colgroup>
                    <col><col><col><col><col><col><col><col>
                </colgroup>
                <thead>
                    <tr>
                        <th><?php echo s(board_herb_category_localize('Herb / Pharmaceutical name', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Taste', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Nature', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Channels', $ises)); ?></th>
                        <th><?php echo s(board_herb_category_localize('Main functions and indications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Cautions / contraindications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Dosage', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Preparation', $ises)); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($bloodheatherbs as $herb): ?>
                    <tr>
                        <th class="herb-category-library__herb-identity" scope="row">
                            <strong><?php echo s($herb[0]); ?></strong>
                            <small><?php echo s($herb[1]); ?></small>
                        </th>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[2], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[3], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[4], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[5], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[6], $ises)); ?></td>
                        <td class="herb-category-library__compact herb-category-library__dosage"><?php echo s(board_herb_category_localize($herb[7], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[8], $ises)); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="herb-category-library__section" id="clear-damp-heat"<?php echo $selectedcategory === 'clear-damp-heat' ? '' : ' hidden'; ?>>
        <div class="herb-category-library__section-heading">
            <h3><?php echo s(board_herb_category_localize('5. Clear Damp-Heat', $ises)); ?></h3>
            <nav class="herb-category-library__pager" aria-label="<?php echo s(board_herb_category_localize('Category navigation', $ises)); ?>">
                <?php if ($previouscategoryurl !== null): ?>
                    <a rel="prev" href="<?php echo $previouscategoryurl->out(false); ?>"><?php echo s('← ' . board_herb_category_localize('Previous', $ises)); ?></a>
                <?php endif; ?>
                <?php if ($nextcategoryurl !== null): ?>
                    <a rel="next" href="<?php echo $nextcategoryurl->out(false); ?>"><?php echo s(board_herb_category_localize('Next', $ises) . ' →'); ?></a>
                <?php endif; ?>
            </nav>
        </div>
        <p class="herb-category-library__summary"><?php echo s(board_herb_category_localize('Board Guideline 2026 · 5 herbs · Common foundation: bitter, cold herbs that clear Heat and dry Dampness.', $ises)); ?></p>

        <div class="herb-category-library__similarities">
            <h4><?php echo s(board_herb_category_localize('Similarities and Clinical Overlaps', $ises)); ?></h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th><?php echo s(board_herb_category_localize('Comparison', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Herbs', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Shared clinical relationship', $ises)); ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($dampheatsimilarities as $similarity): ?>
                        <tr><td><?php echo s(board_herb_category_localize($similarity[0], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[1], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[2], $ises)); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong><?php echo s(board_herb_category_localize('Selection focus:', $ises)); ?></strong> <?php echo s(board_herb_category_localize('upper, middle or lower burner location; Liver/Gallbladder Fire; diarrhea; jaundice; urinary or genital signs; and itching.', $ises)); ?></p>
        </div>

        <div class="herb-category-library__scroll">
            <table class="herb-category-library__comparison-table">
                <colgroup>
                    <col><col><col><col><col><col><col><col>
                </colgroup>
                <thead>
                    <tr>
                        <th><?php echo s(board_herb_category_localize('Herb / Pharmaceutical name', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Taste', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Nature', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Channels', $ises)); ?></th>
                        <th><?php echo s(board_herb_category_localize('Main functions and indications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Cautions / contraindications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Dosage', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Preparation', $ises)); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($dampheatherbs as $herb): ?>
                    <tr>
                        <th class="herb-category-library__herb-identity" scope="row">
                            <strong><?php echo s($herb[0]); ?></strong>
                            <small><?php echo s($herb[1]); ?></small>
                        </th>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[2], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[3], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[4], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[5], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[6], $ises)); ?></td>
                        <td class="herb-category-library__compact herb-category-library__dosage"><?php echo s(board_herb_category_localize($herb[7], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[8], $ises)); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="herb-category-library__section" id="clear-toxic-heat"<?php echo $selectedcategory === 'clear-toxic-heat' ? '' : ' hidden'; ?>>
        <div class="herb-category-library__section-heading">
            <h3><?php echo s(board_herb_category_localize('6. Clear Toxic-Heat', $ises)); ?></h3>
            <nav class="herb-category-library__pager" aria-label="<?php echo s(board_herb_category_localize('Category navigation', $ises)); ?>">
                <?php if ($previouscategoryurl !== null): ?>
                    <a rel="prev" href="<?php echo $previouscategoryurl->out(false); ?>"><?php echo s('← ' . board_herb_category_localize('Previous', $ises)); ?></a>
                <?php endif; ?>
                <?php if ($nextcategoryurl !== null): ?>
                    <a rel="next" href="<?php echo $nextcategoryurl->out(false); ?>"><?php echo s(board_herb_category_localize('Next', $ises) . ' →'); ?></a>
                <?php endif; ?>
            </nav>
        </div>
        <p class="herb-category-library__summary"><?php echo s(board_herb_category_localize('Board Guideline 2026 · 9 herbs · Common foundation: clear Heat and relieve Fire toxicity, with distinct clinical locations and secondary actions.', $ises)); ?></p>

        <div class="herb-category-library__similarities">
            <h4><?php echo s(board_herb_category_localize('Similarities and Clinical Overlaps', $ises)); ?></h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th><?php echo s(board_herb_category_localize('Comparison', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Herbs', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Shared clinical relationship', $ises)); ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($toxicheatsimilarities as $similarity): ?>
                        <tr><td><?php echo s(board_herb_category_localize($similarity[0], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[1], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[2], $ises)); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong><?php echo s(board_herb_category_localize('Selection focus:', $ises)); ?></strong> <?php echo s(board_herb_category_localize('Wind-Heat stage, throat severity, abscess location, Damp-Heat involvement, skin lesions, Summer-Heat and bleeding.', $ises)); ?></p>
        </div>

        <div class="herb-category-library__scroll">
            <table class="herb-category-library__comparison-table">
                <colgroup>
                    <col><col><col><col><col><col><col><col>
                </colgroup>
                <thead>
                    <tr>
                        <th><?php echo s(board_herb_category_localize('Herb / Pharmaceutical name', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Taste', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Nature', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Channels', $ises)); ?></th>
                        <th><?php echo s(board_herb_category_localize('Main functions and indications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Cautions / contraindications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Dosage', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Preparation', $ises)); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($toxicheatherbs as $herb): ?>
                    <tr>
                        <th class="herb-category-library__herb-identity" scope="row">
                            <strong><?php echo s($herb[0]); ?></strong>
                            <small><?php echo s($herb[1]); ?></small>
                        </th>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[2], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[3], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[4], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[5], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[6], $ises)); ?></td>
                        <td class="herb-category-library__compact herb-category-library__dosage"><?php echo s(board_herb_category_localize($herb[7], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[8], $ises)); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="herb-category-library__section" id="clear-deficient-heat"<?php echo $selectedcategory === 'clear-deficient-heat' ? '' : ' hidden'; ?>>
        <div class="herb-category-library__section-heading">
            <h3><?php echo s(board_herb_category_localize('7. Clear Deficient-Heat', $ises)); ?></h3>
            <nav class="herb-category-library__pager" aria-label="<?php echo s(board_herb_category_localize('Category navigation', $ises)); ?>">
                <?php if ($previouscategoryurl !== null): ?>
                    <a rel="prev" href="<?php echo $previouscategoryurl->out(false); ?>"><?php echo s('← ' . board_herb_category_localize('Previous', $ises)); ?></a>
                <?php endif; ?>
                <?php if ($nextcategoryurl !== null): ?>
                    <a rel="next" href="<?php echo $nextcategoryurl->out(false); ?>"><?php echo s(board_herb_category_localize('Next', $ises) . ' →'); ?></a>
                <?php endif; ?>
            </nav>
        </div>
        <p class="herb-category-library__summary"><?php echo s(board_herb_category_localize('Board Guideline 2026 · 3 herbs · Common foundation: clear deficient Heat and cool the Blood.', $ises)); ?></p>

        <div class="herb-category-library__similarities">
            <h4><?php echo s(board_herb_category_localize('Similarities and Clinical Overlaps', $ises)); ?></h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th><?php echo s(board_herb_category_localize('Comparison', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Herbs', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Shared clinical relationship', $ises)); ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($deficientheatsimilarities as $similarity): ?>
                        <tr><td><?php echo s(board_herb_category_localize($similarity[0], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[1], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[2], $ises)); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong><?php echo s(board_herb_category_localize('Selection focus:', $ises)); ?></strong> <?php echo s(board_herb_category_localize('sweating with Yin-deficiency Heat, Lung cough, Summer-Heat or malarial patterns, urinary involvement, toxicity and swelling.', $ises)); ?></p>
        </div>

        <div class="herb-category-library__scroll">
            <table class="herb-category-library__comparison-table">
                <colgroup>
                    <col><col><col><col><col><col><col><col>
                </colgroup>
                <thead>
                    <tr>
                        <th><?php echo s(board_herb_category_localize('Herb / Pharmaceutical name', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Taste', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Nature', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Channels', $ises)); ?></th>
                        <th><?php echo s(board_herb_category_localize('Main functions and indications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Cautions / contraindications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Dosage', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Preparation', $ises)); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($deficientheatherbs as $herb): ?>
                    <tr>
                        <th class="herb-category-library__herb-identity" scope="row">
                            <strong><?php echo s($herb[0]); ?></strong>
                            <small><?php echo s($herb[1]); ?></small>
                        </th>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[2], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[3], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[4], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[5], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[6], $ises)); ?></td>
                        <td class="herb-category-library__compact herb-category-library__dosage"><?php echo s(board_herb_category_localize($herb[7], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[8], $ises)); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="herb-category-library__section" id="purgatives"<?php echo $selectedcategory === 'purgatives' ? '' : ' hidden'; ?>>
        <div class="herb-category-library__section-heading">
            <h3><?php echo s(board_herb_category_localize('8. Purgatives', $ises)); ?></h3>
            <nav class="herb-category-library__pager" aria-label="<?php echo s(board_herb_category_localize('Category navigation', $ises)); ?>">
                <?php if ($previouscategoryurl !== null): ?>
                    <a rel="prev" href="<?php echo $previouscategoryurl->out(false); ?>"><?php echo s('← ' . board_herb_category_localize('Previous', $ises)); ?></a>
                <?php endif; ?>
                <?php if ($nextcategoryurl !== null): ?>
                    <a rel="next" href="<?php echo $nextcategoryurl->out(false); ?>"><?php echo s(board_herb_category_localize('Next', $ises) . ' →'); ?></a>
                <?php endif; ?>
            </nav>
        </div>
        <p class="herb-category-library__summary"><?php echo s(board_herb_category_localize('Board Guideline 2026 · 3 herbs · Common foundation: drain Heat and move stool to purge accumulation.', $ises)); ?></p>

        <div class="herb-category-library__similarities">
            <h4><?php echo s(board_herb_category_localize('Similarities and Clinical Overlaps', $ises)); ?></h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th><?php echo s(board_herb_category_localize('Comparison', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Herbs', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Shared clinical relationship', $ises)); ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($purgativesimilarities as $similarity): ?>
                        <tr><td><?php echo s(board_herb_category_localize($similarity[0], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[1], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[2], $ises)); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong><?php echo s(board_herb_category_localize('Selection focus:', $ises)); ?></strong> <?php echo s(board_herb_category_localize('strength of purgation, dry-hard stool, Blood Heat or stasis, Liver Heat, external swelling and constitutional deficiency.', $ises)); ?></p>
        </div>

        <div class="herb-category-library__scroll">
            <table class="herb-category-library__comparison-table">
                <colgroup>
                    <col><col><col><col><col><col><col><col>
                </colgroup>
                <thead>
                    <tr>
                        <th><?php echo s(board_herb_category_localize('Herb / Pharmaceutical name', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Taste', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Nature', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Channels', $ises)); ?></th>
                        <th><?php echo s(board_herb_category_localize('Main functions and indications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Cautions / contraindications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Dosage', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Preparation', $ises)); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($purgativeherbs as $herb): ?>
                    <tr>
                        <th class="herb-category-library__herb-identity" scope="row">
                            <strong><?php echo s($herb[0]); ?></strong>
                            <small><?php echo s($herb[1]); ?></small>
                        </th>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[2], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[3], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[4], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[5], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[6], $ises)); ?></td>
                        <td class="herb-category-library__compact herb-category-library__dosage"><?php echo s(board_herb_category_localize($herb[7], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[8], $ises)); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="herb-category-library__section" id="laxatives"<?php echo $selectedcategory === 'laxatives' ? '' : ' hidden'; ?>>
        <div class="herb-category-library__section-heading">
            <h3><?php echo s(board_herb_category_localize('9. Laxatives', $ises)); ?></h3>
            <nav class="herb-category-library__pager" aria-label="<?php echo s(board_herb_category_localize('Category navigation', $ises)); ?>">
                <?php if ($previouscategoryurl !== null): ?>
                    <a rel="prev" href="<?php echo $previouscategoryurl->out(false); ?>"><?php echo s('← ' . board_herb_category_localize('Previous', $ises)); ?></a>
                <?php endif; ?>
                <?php if ($nextcategoryurl !== null): ?>
                    <a rel="next" href="<?php echo $nextcategoryurl->out(false); ?>"><?php echo s(board_herb_category_localize('Next', $ises) . ' →'); ?></a>
                <?php endif; ?>
            </nav>
        </div>
        <p class="herb-category-library__summary"><?php echo s(board_herb_category_localize('Board Guideline 2026 · 1 herb · Common foundation: gently moisten the Intestines and relieve dry constipation.', $ises)); ?></p>

        <div class="herb-category-library__similarities">
            <h4><?php echo s(board_herb_category_localize('Similarities and Clinical Overlaps', $ises)); ?></h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th><?php echo s(board_herb_category_localize('Comparison', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Herbs', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Shared clinical relationship', $ises)); ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($laxativesimilarities as $similarity): ?>
                        <tr><td><?php echo s(board_herb_category_localize($similarity[0], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[1], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[2], $ises)); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong><?php echo s(board_herb_category_localize('Selection focus:', $ises)); ?></strong> <?php echo s(board_herb_category_localize('dryness or deficiency constipation, need for gentle action, Yin and fluid status, and long-term-use cautions.', $ises)); ?></p>
        </div>

        <div class="herb-category-library__scroll">
            <table class="herb-category-library__comparison-table">
                <colgroup>
                    <col><col><col><col><col><col><col><col>
                </colgroup>
                <thead>
                    <tr>
                        <th><?php echo s(board_herb_category_localize('Herb / Pharmaceutical name', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Taste', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Nature', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Channels', $ises)); ?></th>
                        <th><?php echo s(board_herb_category_localize('Main functions and indications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Cautions / contraindications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Dosage', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Preparation', $ises)); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($laxativeherbs as $herb): ?>
                    <tr>
                        <th class="herb-category-library__herb-identity" scope="row">
                            <strong><?php echo s($herb[0]); ?></strong>
                            <small><?php echo s($herb[1]); ?></small>
                        </th>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[2], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[3], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[4], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[5], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[6], $ises)); ?></td>
                        <td class="herb-category-library__compact herb-category-library__dosage"><?php echo s(board_herb_category_localize($herb[7], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[8], $ises)); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="herb-category-library__section" id="cathartics"<?php echo $selectedcategory === 'cathartics' ? '' : ' hidden'; ?>>
        <div class="herb-category-library__section-heading">
            <h3><?php echo s(board_herb_category_localize('10. Cathartics', $ises)); ?></h3>
            <nav class="herb-category-library__pager" aria-label="<?php echo s(board_herb_category_localize('Category navigation', $ises)); ?>">
                <?php if ($previouscategoryurl !== null): ?>
                    <a rel="prev" href="<?php echo $previouscategoryurl->out(false); ?>"><?php echo s('← ' . board_herb_category_localize('Previous', $ises)); ?></a>
                <?php endif; ?>
                <?php if ($nextcategoryurl !== null): ?>
                    <a rel="next" href="<?php echo $nextcategoryurl->out(false); ?>"><?php echo s(board_herb_category_localize('Next', $ises) . ' →'); ?></a>
                <?php endif; ?>
            </nav>
        </div>
        <p class="herb-category-library__summary"><?php echo s(board_herb_category_localize('Board Guideline 2026 · 1 herb · Common foundation: strongly drain retained fluids downward.', $ises)); ?></p>

        <div class="herb-category-library__similarities">
            <h4><?php echo s(board_herb_category_localize('Similarities and Clinical Overlaps', $ises)); ?></h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th><?php echo s(board_herb_category_localize('Comparison', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Herbs', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Shared clinical relationship', $ises)); ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($catharticsimilarities as $similarity): ?>
                        <tr><td><?php echo s(board_herb_category_localize($similarity[0], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[1], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[2], $ises)); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong><?php echo s(board_herb_category_localize('Selection focus:', $ises)); ?></strong> <?php echo s(board_herb_category_localize('severe fluid accumulation, edema, swelling or nodules, patient strength, toxicity and formula incompatibility.', $ises)); ?></p>
        </div>

        <div class="herb-category-library__scroll">
            <table class="herb-category-library__comparison-table">
                <colgroup>
                    <col><col><col><col><col><col><col><col>
                </colgroup>
                <thead>
                    <tr>
                        <th><?php echo s(board_herb_category_localize('Herb / Pharmaceutical name', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Taste', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Nature', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Channels', $ises)); ?></th>
                        <th><?php echo s(board_herb_category_localize('Main functions and indications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Cautions / contraindications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Dosage', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Preparation', $ises)); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($catharticherbs as $herb): ?>
                    <tr>
                        <th class="herb-category-library__herb-identity" scope="row">
                            <strong><?php echo s($herb[0]); ?></strong>
                            <small><?php echo s($herb[1]); ?></small>
                        </th>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[2], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[3], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[4], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[5], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[6], $ises)); ?></td>
                        <td class="herb-category-library__compact herb-category-library__dosage"><?php echo s(board_herb_category_localize($herb[7], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[8], $ises)); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="herb-category-library__section" id="diuretics"<?php echo $selectedcategory === 'diuretics' ? '' : ' hidden'; ?>>
        <div class="herb-category-library__section-heading">
            <h3><?php echo s(board_herb_category_localize('11. Diuretics', $ises)); ?></h3>
            <nav class="herb-category-library__pager" aria-label="<?php echo s(board_herb_category_localize('Category navigation', $ises)); ?>">
                <?php if ($previouscategoryurl !== null): ?>
                    <a rel="prev" href="<?php echo $previouscategoryurl->out(false); ?>"><?php echo s('← ' . board_herb_category_localize('Previous', $ises)); ?></a>
                <?php endif; ?>
                <?php if ($nextcategoryurl !== null): ?>
                    <a rel="next" href="<?php echo $nextcategoryurl->out(false); ?>"><?php echo s(board_herb_category_localize('Next', $ises) . ' →'); ?></a>
                <?php endif; ?>
            </nav>
        </div>
        <p class="herb-category-library__summary"><?php echo s(board_herb_category_localize('Board Guideline 2026 · 17 herbs · Common foundation: promote urination, eliminate Water and Dampness, reduce edema or relieve painful urinary dribbling.', $ises)); ?></p>

        <div class="herb-category-library__similarities">
            <h4><?php echo s(board_herb_category_localize('Similarities and Clinical Overlaps', $ises)); ?></h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th><?php echo s(board_herb_category_localize('Comparison', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Herbs', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Shared clinical relationship', $ises)); ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($diureticsimilarities as $similarity): ?>
                        <tr><td><?php echo s(board_herb_category_localize($similarity[0], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[1], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[2], $ises)); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong><?php echo s(board_herb_category_localize('Selection focus:', $ises)); ?></strong> <?php echo s(board_herb_category_localize('edema versus Lin syndrome, Spleen deficiency, jaundice, Heart Fire, bleeding, Blood stasis, lactation, Lung phlegm, itching and cloudy urine.', $ises)); ?></p>
        </div>

        <div class="herb-category-library__scroll">
            <table class="herb-category-library__comparison-table">
                <colgroup>
                    <col><col><col><col><col><col><col><col>
                </colgroup>
                <thead>
                    <tr>
                        <th><?php echo s(board_herb_category_localize('Herb / Pharmaceutical name', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Taste', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Nature', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Channels', $ises)); ?></th>
                        <th><?php echo s(board_herb_category_localize('Main functions and indications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Cautions / contraindications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Dosage', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Preparation', $ises)); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($diureticherbs as $herb): ?>
                    <tr>
                        <th class="herb-category-library__herb-identity" scope="row">
                            <strong><?php echo s($herb[0]); ?></strong>
                            <small><?php echo s($herb[1]); ?></small>
                        </th>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[2], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[3], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[4], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[5], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[6], $ises)); ?></td>
                        <td class="herb-category-library__compact herb-category-library__dosage"><?php echo s(board_herb_category_localize($herb[7], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[8], $ises)); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="herb-category-library__section" id="dispel-wind-damp"<?php echo $selectedcategory === 'dispel-wind-damp' ? '' : ' hidden'; ?>>
        <div class="herb-category-library__section-heading">
            <h3><?php echo s(board_herb_category_localize('12. Dispel Wind-Damp', $ises)); ?></h3>
            <nav class="herb-category-library__pager" aria-label="<?php echo s(board_herb_category_localize('Category navigation', $ises)); ?>">
                <?php if ($previouscategoryurl !== null): ?>
                    <a rel="prev" href="<?php echo $previouscategoryurl->out(false); ?>"><?php echo s('← ' . board_herb_category_localize('Previous', $ises)); ?></a>
                <?php endif; ?>
                <?php if ($nextcategoryurl !== null): ?>
                    <a rel="next" href="<?php echo $nextcategoryurl->out(false); ?>"><?php echo s(board_herb_category_localize('Next', $ises) . ' →'); ?></a>
                <?php endif; ?>
            </nav>
        </div>
        <p class="herb-category-library__summary"><?php echo s(board_herb_category_localize('Board Guideline 2026 · 10 herbs · Common foundation: dispel Wind-Damp, relieve pain from Bi syndrome, unblock channels and benefit sinews.', $ises)); ?></p>

        <div class="herb-category-library__similarities">
            <h4><?php echo s(board_herb_category_localize('Similarities and Clinical Overlaps', $ises)); ?></h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th><?php echo s(board_herb_category_localize('Comparison', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Herbs', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Shared clinical relationship', $ises)); ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($winddampsimilarities as $similarity): ?>
                        <tr><td><?php echo s(board_herb_category_localize($similarity[0], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[1], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[2], $ises)); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong><?php echo s(board_herb_category_localize('Selection focus:', $ises)); ?></strong> <?php echo s(board_herb_category_localize('upper versus lower body, Cold versus Heat, channel obstruction, cramping, edema, skin involvement, convulsions and Liver-Kidney deficiency.', $ises)); ?></p>
        </div>

        <div class="herb-category-library__scroll">
            <table class="herb-category-library__comparison-table">
                <colgroup>
                    <col><col><col><col><col><col><col><col>
                </colgroup>
                <thead>
                    <tr>
                        <th><?php echo s(board_herb_category_localize('Herb / Pharmaceutical name', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Taste', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Nature', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Channels', $ises)); ?></th>
                        <th><?php echo s(board_herb_category_localize('Main functions and indications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Cautions / contraindications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Dosage', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Preparation', $ises)); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($winddampherbs as $herb): ?>
                    <tr>
                        <th class="herb-category-library__herb-identity" scope="row">
                            <strong><?php echo s($herb[0]); ?></strong>
                            <small><?php echo s($herb[1]); ?></small>
                        </th>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[2], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[3], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[4], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[5], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[6], $ises)); ?></td>
                        <td class="herb-category-library__compact herb-category-library__dosage"><?php echo s(board_herb_category_localize($herb[7], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[8], $ises)); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="herb-category-library__section" id="dissolve-hot-phlegm"<?php echo $selectedcategory === 'dissolve-hot-phlegm' ? '' : ' hidden'; ?>>
        <div class="herb-category-library__section-heading">
            <h3><?php echo s(board_herb_category_localize('13. Dissolve Hot Phlegm', $ises)); ?></h3>
            <nav class="herb-category-library__pager" aria-label="<?php echo s(board_herb_category_localize('Category navigation', $ises)); ?>">
                <?php if ($previouscategoryurl !== null): ?>
                    <a rel="prev" href="<?php echo $previouscategoryurl->out(false); ?>"><?php echo s('← ' . board_herb_category_localize('Previous', $ises)); ?></a>
                <?php endif; ?>
                <?php if ($nextcategoryurl !== null): ?>
                    <a rel="next" href="<?php echo $nextcategoryurl->out(false); ?>"><?php echo s(board_herb_category_localize('Next', $ises) . ' →'); ?></a>
                <?php endif; ?>
            </nav>
        </div>
        <p class="herb-category-library__summary"><?php echo s(board_herb_category_localize('Board Guideline 2026 · 8 herbs · Common foundation: cool and transform Phlegm-Heat, stop cough, open the chest or soften nodules.', $ises)); ?></p>

        <div class="herb-category-library__similarities">
            <h4><?php echo s(board_herb_category_localize('Similarities and Clinical Overlaps', $ises)); ?></h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th><?php echo s(board_herb_category_localize('Comparison', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Herbs', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Shared clinical relationship', $ises)); ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($hotphlegmsimilarities as $similarity): ?>
                        <tr><td><?php echo s(board_herb_category_localize($similarity[0], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[1], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[2], $ises)); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong><?php echo s(board_herb_category_localize('Selection focus:', $ises)); ?></strong> <?php echo s(board_herb_category_localize('cough type, dryness versus productive phlegm, chest constraint, nodules, bowel dryness, vomiting, edema and constitutional Cold.', $ises)); ?></p>
        </div>

        <div class="herb-category-library__scroll">
            <table class="herb-category-library__comparison-table">
                <colgroup><col><col><col><col><col><col><col><col></colgroup>
                <thead>
                    <tr>
                        <th><?php echo s(board_herb_category_localize('Herb / Pharmaceutical name', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Taste', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Nature', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Channels', $ises)); ?></th>
                        <th><?php echo s(board_herb_category_localize('Main functions and indications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Cautions / contraindications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Dosage', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Preparation', $ises)); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($hotphlegmherbs as $herb): ?>
                    <tr>
                        <th class="herb-category-library__herb-identity" scope="row"><strong><?php echo s($herb[0]); ?></strong><small><?php echo s($herb[1]); ?></small></th>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[2], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[3], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[4], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[5], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[6], $ises)); ?></td>
                        <td class="herb-category-library__compact herb-category-library__dosage"><?php echo s(board_herb_category_localize($herb[7], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[8], $ises)); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="herb-category-library__section" id="dissolve-cold-phlegm"<?php echo $selectedcategory === 'dissolve-cold-phlegm' ? '' : ' hidden'; ?>>
        <div class="herb-category-library__section-heading">
            <h3><?php echo s(board_herb_category_localize('14. Dissolve Cold Phlegm', $ises)); ?></h3>
            <nav class="herb-category-library__pager" aria-label="<?php echo s(board_herb_category_localize('Category navigation', $ises)); ?>">
                <?php if ($previouscategoryurl !== null): ?>
                    <a rel="prev" href="<?php echo $previouscategoryurl->out(false); ?>"><?php echo s('← ' . board_herb_category_localize('Previous', $ises)); ?></a>
                <?php endif; ?>
                <?php if ($nextcategoryurl !== null): ?>
                    <a rel="next" href="<?php echo $nextcategoryurl->out(false); ?>"><?php echo s(board_herb_category_localize('Next', $ises) . ' →'); ?></a>
                <?php endif; ?>
            </nav>
        </div>
        <p class="herb-category-library__summary"><?php echo s(board_herb_category_localize('Board Guideline 2026 · 6 herbs · Common foundation: warm and transform Cold-Phlegm, regulate Lung Qi, stop cough or resolve nodules.', $ises)); ?></p>
        <div class="herb-category-library__similarities">
            <h4><?php echo s(board_herb_category_localize('Similarities and Clinical Overlaps', $ises)); ?></h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th><?php echo s(board_herb_category_localize('Comparison', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Herbs', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Shared clinical relationship', $ises)); ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($coldphlegmsimilarities as $similarity): ?>
                        <tr><td><?php echo s(board_herb_category_localize($similarity[0], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[1], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[2], $ises)); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong><?php echo s(board_herb_category_localize('Selection focus:', $ises)); ?></strong> <?php echo s(board_herb_category_localize('Cold-Phlegm location, dry versus rebellious phlegm, cough, vomiting, throat involvement, channel obstruction, nodules, toxicity and preparation.', $ises)); ?></p>
        </div>
        <div class="herb-category-library__scroll">
            <table class="herb-category-library__comparison-table">
                <colgroup><col><col><col><col><col><col><col><col></colgroup>
                <thead><tr>
                    <th><?php echo s(board_herb_category_localize('Herb / Pharmaceutical name', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Taste', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Nature', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Channels', $ises)); ?></th>
                    <th><?php echo s(board_herb_category_localize('Main functions and indications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Cautions / contraindications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Dosage', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Preparation', $ises)); ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($coldphlegmherbs as $herb): ?>
                    <tr>
                        <th class="herb-category-library__herb-identity" scope="row"><strong><?php echo s($herb[0]); ?></strong><small><?php echo s($herb[1]); ?></small></th>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[2], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[3], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[4], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[5], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[6], $ises)); ?></td>
                        <td class="herb-category-library__compact herb-category-library__dosage"><?php echo s(board_herb_category_localize($herb[7], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[8], $ises)); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="herb-category-library__section" id="stop-cough-wheezing"<?php echo $selectedcategory === 'stop-cough-wheezing' ? '' : ' hidden'; ?>>
        <div class="herb-category-library__section-heading">
            <h3><?php echo s(board_herb_category_localize('15. Stop Cough and Wheezing', $ises)); ?></h3>
            <nav class="herb-category-library__pager" aria-label="<?php echo s(board_herb_category_localize('Category navigation', $ises)); ?>">
                <?php if ($previouscategoryurl !== null): ?>
                    <a rel="prev" href="<?php echo $previouscategoryurl->out(false); ?>"><?php echo s('← ' . board_herb_category_localize('Previous', $ises)); ?></a>
                <?php endif; ?>
                <?php if ($nextcategoryurl !== null): ?>
                    <a rel="next" href="<?php echo $nextcategoryurl->out(false); ?>"><?php echo s(board_herb_category_localize('Next', $ises) . ' →'); ?></a>
                <?php endif; ?>
            </nav>
        </div>
        <p class="herb-category-library__summary"><?php echo s(board_herb_category_localize('Board Guideline 2026 · 7 herbs · Common foundation: relieve cough, stop wheezing, regulate Lung Qi, transform phlegm or moisten the Lung.', $ises)); ?></p>
        <div class="herb-category-library__similarities">
            <h4><?php echo s(board_herb_category_localize('Similarities and Clinical Overlaps', $ises)); ?></h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th><?php echo s(board_herb_category_localize('Comparison', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Herbs', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Shared clinical relationship', $ises)); ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($coughsimilarities as $similarity): ?>
                        <tr><td><?php echo s(board_herb_category_localize($similarity[0], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[1], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[2], $ises)); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong><?php echo s(board_herb_category_localize('Selection focus:', $ises)); ?></strong> <?php echo s(board_herb_category_localize('cough temperature, dry versus phlegmy cough, rebellious Lung or Stomach Qi, bowel dryness, parasites, edema and constitutional deficiency.', $ises)); ?></p>
        </div>
        <div class="herb-category-library__scroll">
            <table class="herb-category-library__comparison-table">
                <colgroup><col><col><col><col><col><col><col><col></colgroup>
                <thead><tr>
                    <th><?php echo s(board_herb_category_localize('Herb / Pharmaceutical name', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Taste', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Nature', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Channels', $ises)); ?></th>
                    <th><?php echo s(board_herb_category_localize('Main functions and indications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Cautions / contraindications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Dosage', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Preparation', $ises)); ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($coughherbs as $herb): ?>
                    <tr>
                        <th class="herb-category-library__herb-identity" scope="row"><strong><?php echo s($herb[0]); ?></strong><small><?php echo s($herb[1]); ?></small></th>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[2], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[3], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[4], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[5], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[6], $ises)); ?></td>
                        <td class="herb-category-library__compact herb-category-library__dosage"><?php echo s(board_herb_category_localize($herb[7], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[8], $ises)); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="herb-category-library__section" id="aromatics-for-damp"<?php echo $selectedcategory === 'aromatics-for-damp' ? '' : ' hidden'; ?>>
        <div class="herb-category-library__section-heading">
            <h3><?php echo s(board_herb_category_localize('16. Aromatics for Damp', $ises)); ?></h3>
            <nav class="herb-category-library__pager" aria-label="<?php echo s(board_herb_category_localize('Category navigation', $ises)); ?>">
                <?php if ($previouscategoryurl !== null): ?>
                    <a rel="prev" href="<?php echo $previouscategoryurl->out(false); ?>"><?php echo s('← ' . board_herb_category_localize('Previous', $ises)); ?></a>
                <?php endif; ?>
                <?php if ($nextcategoryurl !== null): ?>
                    <a rel="next" href="<?php echo $nextcategoryurl->out(false); ?>"><?php echo s(board_herb_category_localize('Next', $ises) . ' →'); ?></a>
                <?php endif; ?>
            </nav>
        </div>
        <p class="herb-category-library__summary"><?php echo s(board_herb_category_localize('Board Guideline 2026 · 7 herbs · Common foundation: transform excess and turbid Dampness from the Middle Jiao, regulate Qi and warm the center.', $ises)); ?></p>
        <div class="herb-category-library__similarities">
            <h4><?php echo s(board_herb_category_localize('Similarities and Clinical Overlaps', $ises)); ?></h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th><?php echo s(board_herb_category_localize('Comparison', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Herbs', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Shared clinical relationship', $ises)); ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($aromaticsimilarities as $similarity): ?>
                        <tr><td><?php echo s(board_herb_category_localize($similarity[0], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[1], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[2], $ises)); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong><?php echo s(board_herb_category_localize('Selection focus:', $ises)); ?></strong> <?php echo s(board_herb_category_localize('Dampness severity, exterior involvement, Qi stagnation, nausea or vomiting, Spleen Cold, pregnancy, malaria and constitutional deficiency.', $ises)); ?></p>
        </div>
        <div class="herb-category-library__scroll">
            <table class="herb-category-library__comparison-table">
                <colgroup><col><col><col><col><col><col><col><col></colgroup>
                <thead><tr>
                    <th><?php echo s(board_herb_category_localize('Herb / Pharmaceutical name', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Taste', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Nature', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Channels', $ises)); ?></th>
                    <th><?php echo s(board_herb_category_localize('Main functions and indications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Cautions / contraindications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Dosage', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Preparation', $ises)); ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($aromaticherbs as $herb): ?>
                    <tr>
                        <th class="herb-category-library__herb-identity" scope="row"><strong><?php echo s($herb[0]); ?></strong><small><?php echo s($herb[1]); ?></small></th>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[2], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[3], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[4], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[5], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[6], $ises)); ?></td>
                        <td class="herb-category-library__compact herb-category-library__dosage"><?php echo s(board_herb_category_localize($herb[7], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[8], $ises)); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="herb-category-library__section" id="digestives"<?php echo $selectedcategory === 'digestives' ? '' : ' hidden'; ?>>
        <div class="herb-category-library__section-heading">
            <h3><?php echo s(board_herb_category_localize('17. Digestives', $ises)); ?></h3>
            <nav class="herb-category-library__pager" aria-label="<?php echo s(board_herb_category_localize('Category navigation', $ises)); ?>">
                <?php if ($previouscategoryurl !== null): ?>
                    <a rel="prev" href="<?php echo $previouscategoryurl->out(false); ?>"><?php echo s('← ' . board_herb_category_localize('Previous', $ises)); ?></a>
                <?php endif; ?>
                <?php if ($nextcategoryurl !== null): ?>
                    <a rel="next" href="<?php echo $nextcategoryurl->out(false); ?>"><?php echo s(board_herb_category_localize('Next', $ises) . ' →'); ?></a>
                <?php endif; ?>
            </nav>
        </div>
        <p class="herb-category-library__summary"><?php echo s(board_herb_category_localize('Board Guideline 2026 · 6 herbs · Common foundation: reduce food stagnation, support digestion and restore the movement of the Middle Jiao.', $ises)); ?></p>
        <div class="herb-category-library__similarities">
            <h4><?php echo s(board_herb_category_localize('Similarities and Clinical Overlaps', $ises)); ?></h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th><?php echo s(board_herb_category_localize('Comparison', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Herbs', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Shared clinical relationship', $ises)); ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($digestivesimilarities as $similarity): ?>
                        <tr><td><?php echo s(board_herb_category_localize($similarity[0], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[1], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[2], $ises)); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong><?php echo s(board_herb_category_localize('Selection focus:', $ises)); ?></strong> <?php echo s(board_herb_category_localize('food type, degree of retention, Spleen deficiency, Qi or phlegm involvement, lactation, stones and constitutional excess versus deficiency.', $ises)); ?></p>
        </div>
        <div class="herb-category-library__scroll">
            <table class="herb-category-library__comparison-table">
                <colgroup><col><col><col><col><col><col><col><col></colgroup>
                <thead><tr>
                    <th><?php echo s(board_herb_category_localize('Herb / Pharmaceutical name', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Taste', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Nature', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Channels', $ises)); ?></th>
                    <th><?php echo s(board_herb_category_localize('Main functions and indications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Cautions / contraindications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Dosage', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Preparation', $ises)); ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($digestiveherbs as $herb): ?>
                    <tr>
                        <th class="herb-category-library__herb-identity" scope="row"><strong><?php echo s($herb[0]); ?></strong><small><?php echo s($herb[1]); ?></small></th>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[2], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[3], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[4], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[5], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[6], $ises)); ?></td>
                        <td class="herb-category-library__compact herb-category-library__dosage"><?php echo s(board_herb_category_localize($herb[7], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[8], $ises)); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="herb-category-library__section" id="regulate-qi"<?php echo $selectedcategory === 'regulate-qi' ? '' : ' hidden'; ?>>
        <div class="herb-category-library__section-heading">
            <h3><?php echo s(board_herb_category_localize('18. Regulate Qi', $ises)); ?></h3>
            <nav class="herb-category-library__pager" aria-label="<?php echo s(board_herb_category_localize('Category navigation', $ises)); ?>">
                <?php if ($previouscategoryurl !== null): ?>
                    <a rel="prev" href="<?php echo $previouscategoryurl->out(false); ?>"><?php echo s('← ' . board_herb_category_localize('Previous', $ises)); ?></a>
                <?php endif; ?>
                <?php if ($nextcategoryurl !== null): ?>
                    <a rel="next" href="<?php echo $nextcategoryurl->out(false); ?>"><?php echo s(board_herb_category_localize('Next', $ises) . ' →'); ?></a>
                <?php endif; ?>
            </nav>
        </div>
        <p class="herb-category-library__summary"><?php echo s(board_herb_category_localize('Board Guideline 2026 · 10 herbs · Common foundation: regulate Qi, remove stagnation, relieve pain and restore movement through the Middle and Lower Jiao.', $ises)); ?></p>
        <div class="herb-category-library__similarities">
            <h4><?php echo s(board_herb_category_localize('Similarities and Clinical Overlaps', $ises)); ?></h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th><?php echo s(board_herb_category_localize('Comparison', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Herbs', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Shared clinical relationship', $ises)); ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($regulateqisimilarities as $similarity): ?>
                        <tr><td><?php echo s(board_herb_category_localize($similarity[0], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[1], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[2], $ises)); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong><?php echo s(board_herb_category_localize('Selection focus:', $ises)); ?></strong> <?php echo s(board_herb_category_localize('location of Qi stagnation, strength needed, Heat or Cold, phlegm, food or Dampness, menstruation, urination, wheezing and constitutional deficiency.', $ises)); ?></p>
        </div>
        <div class="herb-category-library__scroll">
            <table class="herb-category-library__comparison-table">
                <colgroup><col><col><col><col><col><col><col><col></colgroup>
                <thead><tr>
                    <th><?php echo s(board_herb_category_localize('Herb / Pharmaceutical name', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Taste', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Nature', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Channels', $ises)); ?></th>
                    <th><?php echo s(board_herb_category_localize('Main functions and indications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Cautions / contraindications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Dosage', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Preparation', $ises)); ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($regulateqiherbs as $herb): ?>
                    <tr>
                        <th class="herb-category-library__herb-identity" scope="row"><strong><?php echo s($herb[0]); ?></strong><small><?php echo s($herb[1]); ?></small></th>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[2], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[3], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[4], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[5], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[6], $ises)); ?></td>
                        <td class="herb-category-library__compact herb-category-library__dosage"><?php echo s(board_herb_category_localize($herb[7], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[8], $ises)); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="herb-category-library__section" id="stop-bleeding"<?php echo $selectedcategory === 'stop-bleeding' ? '' : ' hidden'; ?>>
        <div class="herb-category-library__section-heading">
            <h3><?php echo s(board_herb_category_localize('19. Stop Bleeding', $ises)); ?></h3>
            <nav class="herb-category-library__pager" aria-label="<?php echo s(board_herb_category_localize('Category navigation', $ises)); ?>">
                <?php if ($previouscategoryurl !== null): ?>
                    <a rel="prev" href="<?php echo $previouscategoryurl->out(false); ?>"><?php echo s('← ' . board_herb_category_localize('Previous', $ises)); ?></a>
                <?php endif; ?>
                <?php if ($nextcategoryurl !== null): ?>
                    <a rel="next" href="<?php echo $nextcategoryurl->out(false); ?>"><?php echo s(board_herb_category_localize('Next', $ises) . ' →'); ?></a>
                <?php endif; ?>
            </nav>
        </div>
        <p class="herb-category-library__summary"><?php echo s(board_herb_category_localize('Board Guideline 2026 · 10 herbs · Common foundation: stop bleeding by moving, astringing, cooling or warming the Blood according to the pattern.', $ises)); ?></p>
        <div class="herb-category-library__similarities">
            <h4><?php echo s(board_herb_category_localize('Similarities and Clinical Overlaps', $ises)); ?></h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th><?php echo s(board_herb_category_localize('Comparison', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Herbs', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Shared clinical relationship', $ises)); ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($bleedingsimilarities as $similarity): ?>
                        <tr><td><?php echo s(board_herb_category_localize($similarity[0], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[1], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[2], $ises)); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong><?php echo s(board_herb_category_localize('Selection focus:', $ises)); ?></strong> <?php echo s(board_herb_category_localize('bleeding mechanism and location, Blood stasis, Heat or Cold, trauma, pregnancy, diarrhea or dysentery, Lung symptoms and topical tissue repair.', $ises)); ?></p>
        </div>
        <div class="herb-category-library__scroll">
            <table class="herb-category-library__comparison-table">
                <colgroup><col><col><col><col><col><col><col><col></colgroup>
                <thead><tr>
                    <th><?php echo s(board_herb_category_localize('Herb / Pharmaceutical name', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Taste', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Nature', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Channels', $ises)); ?></th>
                    <th><?php echo s(board_herb_category_localize('Main functions and indications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Cautions / contraindications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Dosage', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Preparation', $ises)); ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($bleedingherbs as $herb): ?>
                    <tr>
                        <th class="herb-category-library__herb-identity" scope="row"><strong><?php echo s($herb[0]); ?></strong><small><?php echo s($herb[1]); ?></small></th>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[2], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[3], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[4], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[5], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[6], $ises)); ?></td>
                        <td class="herb-category-library__compact herb-category-library__dosage"><?php echo s(board_herb_category_localize($herb[7], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[8], $ises)); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="herb-category-library__section" id="activate-blood"<?php echo $selectedcategory === 'activate-blood' ? '' : ' hidden'; ?>>
        <div class="herb-category-library__section-heading">
            <h3><?php echo s(board_herb_category_localize('20. Activate Blood', $ises)); ?></h3>
            <nav class="herb-category-library__pager" aria-label="<?php echo s(board_herb_category_localize('Category navigation', $ises)); ?>">
                <?php if ($previouscategoryurl !== null): ?>
                    <a rel="prev" href="<?php echo $previouscategoryurl->out(false); ?>"><?php echo s('← ' . board_herb_category_localize('Previous', $ises)); ?></a>
                <?php endif; ?>
                <?php if ($nextcategoryurl !== null): ?>
                    <a rel="next" href="<?php echo $nextcategoryurl->out(false); ?>"><?php echo s(board_herb_category_localize('Next', $ises) . ' →'); ?></a>
                <?php endif; ?>
            </nav>
        </div>
        <p class="herb-category-library__summary"><?php echo s(board_herb_category_localize('Board Guideline 2026 · 16 herbs · Common foundation: invigorate Blood, remove Blood stasis, regulate menstruation and relieve pain.', $ises)); ?></p>
        <div class="herb-category-library__similarities">
            <h4><?php echo s(board_herb_category_localize('Similarities and Clinical Overlaps', $ises)); ?></h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th><?php echo s(board_herb_category_localize('Comparison', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Herbs', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Shared clinical relationship', $ises)); ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($activatebloodsimilarities as $similarity): ?>
                        <tr><td><?php echo s(board_herb_category_localize($similarity[0], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[1], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[2], $ises)); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong><?php echo s(board_herb_category_localize('Selection focus:', $ises)); ?></strong> <?php echo s(board_herb_category_localize('pain location and cause, Heat or Cold, menstrual involvement, trauma, fixed masses, swelling, downward direction, pregnancy and constitutional deficiency.', $ises)); ?></p>
        </div>
        <div class="herb-category-library__scroll">
            <table class="herb-category-library__comparison-table">
                <colgroup><col><col><col><col><col><col><col><col></colgroup>
                <thead><tr>
                    <th><?php echo s(board_herb_category_localize('Herb / Pharmaceutical name', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Taste', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Nature', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Channels', $ises)); ?></th>
                    <th><?php echo s(board_herb_category_localize('Main functions and indications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Cautions / contraindications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Dosage', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Preparation', $ises)); ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($activatebloodherbs as $herb): ?>
                    <tr>
                        <th class="herb-category-library__herb-identity" scope="row"><strong><?php echo s($herb[0]); ?></strong><small><?php echo s($herb[1]); ?></small></th>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[2], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[3], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[4], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[5], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[6], $ises)); ?></td>
                        <td class="herb-category-library__compact herb-category-library__dosage"><?php echo s(board_herb_category_localize($herb[7], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[8], $ises)); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="herb-category-library__section" id="warm-interior"<?php echo $selectedcategory === 'warm-interior' ? '' : ' hidden'; ?>>
        <div class="herb-category-library__section-heading">
            <h3><?php echo s(board_herb_category_localize('21. Warm Interior', $ises)); ?></h3>
            <nav class="herb-category-library__pager" aria-label="<?php echo s(board_herb_category_localize('Category navigation', $ises)); ?>">
                <?php if ($previouscategoryurl !== null): ?>
                    <a rel="prev" href="<?php echo $previouscategoryurl->out(false); ?>"><?php echo s('← ' . board_herb_category_localize('Previous', $ises)); ?></a>
                <?php endif; ?>
                <?php if ($nextcategoryurl !== null): ?>
                    <a rel="next" href="<?php echo $nextcategoryurl->out(false); ?>"><?php echo s(board_herb_category_localize('Next', $ises) . ' →'); ?></a>
                <?php endif; ?>
            </nav>
        </div>
        <p class="herb-category-library__summary"><?php echo s(board_herb_category_localize('Board Guideline 2026 · 12 herbs · Common foundation: warm the Interior, expel Cold, restore Yang and alleviate pain.', $ises)); ?></p>
        <div class="herb-category-library__similarities">
            <h4><?php echo s(board_herb_category_localize('Similarities and Clinical Overlaps', $ises)); ?></h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th><?php echo s(board_herb_category_localize('Comparison', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Herbs', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Shared clinical relationship', $ises)); ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($warminteriorsimilarities as $similarity): ?>
                        <tr><td><?php echo s(board_herb_category_localize($similarity[0], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[1], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[2], $ises)); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong><?php echo s(board_herb_category_localize('Selection focus:', $ises)); ?></strong> <?php echo s(board_herb_category_localize('Cold location and severity, Yang collapse, Middle Jiao or Kidney involvement, rebellious Qi, pain, phlegm, toxicity, pregnancy and required preparation.', $ises)); ?></p>
        </div>
        <div class="herb-category-library__scroll">
            <table class="herb-category-library__comparison-table">
                <colgroup><col><col><col><col><col><col><col><col></colgroup>
                <thead><tr>
                    <th><?php echo s(board_herb_category_localize('Herb / Pharmaceutical name', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Taste', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Nature', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Channels', $ises)); ?></th>
                    <th><?php echo s(board_herb_category_localize('Main functions and indications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Cautions / contraindications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Dosage', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Preparation', $ises)); ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($warminteriorherbs as $herb): ?>
                    <tr>
                        <th class="herb-category-library__herb-identity" scope="row"><strong><?php echo s($herb[0]); ?></strong><small><?php echo s($herb[1]); ?></small></th>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[2], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[3], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[4], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[5], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[6], $ises)); ?></td>
                        <td class="herb-category-library__compact herb-category-library__dosage"><?php echo s(board_herb_category_localize($herb[7], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[8], $ises)); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="herb-category-library__section" id="qi-tonics"<?php echo $selectedcategory === 'qi-tonics' ? '' : ' hidden'; ?>>
        <div class="herb-category-library__section-heading">
            <h3><?php echo s(board_herb_category_localize('22. Qi Tonics', $ises)); ?></h3>
            <nav class="herb-category-library__pager" aria-label="<?php echo s(board_herb_category_localize('Category navigation', $ises)); ?>">
                <?php if ($previouscategoryurl !== null): ?>
                    <a rel="prev" href="<?php echo $previouscategoryurl->out(false); ?>"><?php echo s('← ' . board_herb_category_localize('Previous', $ises)); ?></a>
                <?php endif; ?>
                <?php if ($nextcategoryurl !== null): ?>
                    <a rel="next" href="<?php echo $nextcategoryurl->out(false); ?>"><?php echo s(board_herb_category_localize('Next', $ises) . ' →'); ?></a>
                <?php endif; ?>
            </nav>
        </div>
        <p class="herb-category-library__summary"><?php echo s(board_herb_category_localize('Board Guideline 2026 · 11 herbs · Common foundation: tonify the Spleen, augment Qi, generate fluids and support transformation and transportation.', $ises)); ?></p>
        <div class="herb-category-library__similarities">
            <h4><?php echo s(board_herb_category_localize('Similarities and Clinical Overlaps', $ises)); ?></h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th><?php echo s(board_herb_category_localize('Comparison', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Herbs', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Shared clinical relationship', $ises)); ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($qitonicsimilarities as $similarity): ?>
                        <tr><td><?php echo s(board_herb_category_localize($similarity[0], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[1], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[2], $ises)); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong><?php echo s(board_herb_category_localize('Selection focus:', $ises)); ?></strong> <?php echo s(board_herb_category_localize('degree and location of Qi deficiency, Spleen Dampness, Exterior instability, Lung or Kidney involvement, fluid deficiency, formula harmonization and food stagnation.', $ises)); ?></p>
        </div>
        <div class="herb-category-library__scroll">
            <table class="herb-category-library__comparison-table">
                <colgroup><col><col><col><col><col><col><col><col></colgroup>
                <thead><tr>
                    <th><?php echo s(board_herb_category_localize('Herb / Pharmaceutical name', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Taste', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Nature', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Channels', $ises)); ?></th>
                    <th><?php echo s(board_herb_category_localize('Main functions and indications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Cautions / contraindications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Dosage', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Preparation', $ises)); ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($qitonicherbs as $herb): ?>
                    <tr>
                        <th class="herb-category-library__herb-identity" scope="row"><strong><?php echo s($herb[0]); ?></strong><small><?php echo s($herb[1]); ?></small></th>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[2], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[3], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[4], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[5], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[6], $ises)); ?></td>
                        <td class="herb-category-library__compact herb-category-library__dosage"><?php echo s(board_herb_category_localize($herb[7], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[8], $ises)); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="herb-category-library__section" id="blood-tonics"<?php echo $selectedcategory === 'blood-tonics' ? '' : ' hidden'; ?>>
        <div class="herb-category-library__section-heading">
            <h3><?php echo s(board_herb_category_localize('23. Blood Tonics', $ises)); ?></h3>
            <nav class="herb-category-library__pager" aria-label="<?php echo s(board_herb_category_localize('Category navigation', $ises)); ?>">
                <?php if ($previouscategoryurl !== null): ?>
                    <a rel="prev" href="<?php echo $previouscategoryurl->out(false); ?>"><?php echo s('← ' . board_herb_category_localize('Previous', $ises)); ?></a>
                <?php endif; ?>
                <?php if ($nextcategoryurl !== null): ?>
                    <a rel="next" href="<?php echo $nextcategoryurl->out(false); ?>"><?php echo s(board_herb_category_localize('Next', $ises) . ' →'); ?></a>
                <?php endif; ?>
            </nav>
        </div>
        <p class="herb-category-library__summary"><?php echo s(board_herb_category_localize('Board Guideline 2026 · 7 herbs · Common foundation: nourish Blood, support menstruation, enrich Yin and restore deficient tissues and fluids.', $ises)); ?></p>
        <div class="herb-category-library__similarities">
            <h4><?php echo s(board_herb_category_localize('Similarities and Clinical Overlaps', $ises)); ?></h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th><?php echo s(board_herb_category_localize('Comparison', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Herbs', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Shared clinical relationship', $ises)); ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($bloodtonicsimilarities as $similarity): ?>
                        <tr><td><?php echo s(board_herb_category_localize($similarity[0], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[1], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[2], $ises)); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong><?php echo s(board_herb_category_localize('Selection focus:', $ises)); ?></strong> <?php echo s(board_herb_category_localize('Blood versus Yin deficiency, menstruation, bleeding, dryness, bowel function, Liver and Kidney involvement, Shen disturbance, Dampness and digestive tolerance.', $ises)); ?></p>
        </div>
        <div class="herb-category-library__scroll">
            <table class="herb-category-library__comparison-table">
                <colgroup><col><col><col><col><col><col><col><col></colgroup>
                <thead><tr>
                    <th><?php echo s(board_herb_category_localize('Herb / Pharmaceutical name', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Taste', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Nature', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Channels', $ises)); ?></th>
                    <th><?php echo s(board_herb_category_localize('Main functions and indications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Cautions / contraindications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Dosage', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Preparation', $ises)); ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($bloodtonicherbs as $herb): ?>
                    <tr>
                        <th class="herb-category-library__herb-identity" scope="row"><strong><?php echo s($herb[0]); ?></strong><small><?php echo s($herb[1]); ?></small></th>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[2], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[3], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[4], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[5], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[6], $ises)); ?></td>
                        <td class="herb-category-library__compact herb-category-library__dosage"><?php echo s(board_herb_category_localize($herb[7], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[8], $ises)); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="herb-category-library__section" id="yang-tonics"<?php echo $selectedcategory === 'yang-tonics' ? '' : ' hidden'; ?>>
        <div class="herb-category-library__section-heading">
            <h3><?php echo s(board_herb_category_localize('24. Yang Tonics', $ises)); ?></h3>
            <nav class="herb-category-library__pager" aria-label="<?php echo s(board_herb_category_localize('Category navigation', $ises)); ?>">
                <?php if ($previouscategoryurl !== null): ?>
                    <a rel="prev" href="<?php echo $previouscategoryurl->out(false); ?>"><?php echo s('← ' . board_herb_category_localize('Previous', $ises)); ?></a>
                <?php endif; ?>
                <?php if ($nextcategoryurl !== null): ?>
                    <a rel="next" href="<?php echo $nextcategoryurl->out(false); ?>"><?php echo s(board_herb_category_localize('Next', $ises) . ' →'); ?></a>
                <?php endif; ?>
            </nav>
        </div>
        <p class="herb-category-library__summary"><?php echo s(board_herb_category_localize('Board Guideline 2026 · 14 herbs · Common foundation: tonify Kidney Yang, secure Essence, strengthen sinews and bones, and support reproductive and urinary function.', $ises)); ?></p>
        <div class="herb-category-library__similarities">
            <h4><?php echo s(board_herb_category_localize('Similarities and Clinical Overlaps', $ises)); ?></h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th><?php echo s(board_herb_category_localize('Comparison', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Herbs', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Shared clinical relationship', $ises)); ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($yangtonicsimilarities as $similarity): ?>
                        <tr><td><?php echo s(board_herb_category_localize($similarity[0], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[1], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[2], $ises)); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong><?php echo s(board_herb_category_localize('Selection focus:', $ises)); ?></strong> <?php echo s(board_herb_category_localize('degree of Yang deficiency, Essence or urine leakage, infertility, bowel dryness, Wind-Damp pain, bone injury, diarrhea, fetal stability, Heat signs and toxicity.', $ises)); ?></p>
        </div>
        <div class="herb-category-library__scroll">
            <table class="herb-category-library__comparison-table">
                <colgroup><col><col><col><col><col><col><col><col></colgroup>
                <thead><tr>
                    <th><?php echo s(board_herb_category_localize('Herb / Pharmaceutical name', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Taste', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Nature', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Channels', $ises)); ?></th>
                    <th><?php echo s(board_herb_category_localize('Main functions and indications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Cautions / contraindications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Dosage', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Preparation', $ises)); ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($yangtonicherbs as $herb): ?>
                    <tr>
                        <th class="herb-category-library__herb-identity" scope="row"><strong><?php echo s($herb[0]); ?></strong><small><?php echo s($herb[1]); ?></small></th>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[2], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[3], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[4], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[5], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[6], $ises)); ?></td>
                        <td class="herb-category-library__compact herb-category-library__dosage"><?php echo s(board_herb_category_localize($herb[7], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[8], $ises)); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="herb-category-library__section" id="yin-tonics"<?php echo $selectedcategory === 'yin-tonics' ? '' : ' hidden'; ?>>
        <div class="herb-category-library__section-heading">
            <h3><?php echo s(board_herb_category_localize('25. Yin Tonics', $ises)); ?></h3>
            <nav class="herb-category-library__pager" aria-label="<?php echo s(board_herb_category_localize('Category navigation', $ises)); ?>">
                <?php if ($previouscategoryurl !== null): ?>
                    <a rel="prev" href="<?php echo $previouscategoryurl->out(false); ?>"><?php echo s('← ' . board_herb_category_localize('Previous', $ises)); ?></a>
                <?php endif; ?>
                <?php if ($nextcategoryurl !== null): ?>
                    <a rel="next" href="<?php echo $nextcategoryurl->out(false); ?>"><?php echo s(board_herb_category_localize('Next', $ises) . ' →'); ?></a>
                <?php endif; ?>
            </nav>
        </div>
        <p class="herb-category-library__summary"><?php echo s(board_herb_category_localize('Board Guideline 2026 · 9 herbs · Common foundation: nourish Yin, generate fluids, moisten dryness, clear deficiency Heat and anchor Yang.', $ises)); ?></p>
        <div class="herb-category-library__similarities">
            <h4><?php echo s(board_herb_category_localize('Similarities and Clinical Overlaps', $ises)); ?></h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th><?php echo s(board_herb_category_localize('Comparison', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Herbs', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Shared clinical relationship', $ises)); ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($yintonicsimilarities as $similarity): ?>
                        <tr><td><?php echo s(board_herb_category_localize($similarity[0], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[1], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[2], $ises)); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong><?php echo s(board_herb_category_localize('Selection focus:', $ises)); ?></strong> <?php echo s(board_herb_category_localize('Lung, Stomach, Liver or Kidney Yin deficiency, dry cough, thirst, bowel dryness, vision, premature greying, floating Yang, masses and digestive weakness.', $ises)); ?></p>
        </div>
        <div class="herb-category-library__scroll">
            <table class="herb-category-library__comparison-table">
                <colgroup><col><col><col><col><col><col><col><col></colgroup>
                <thead><tr>
                    <th><?php echo s(board_herb_category_localize('Herb / Pharmaceutical name', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Taste', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Nature', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Channels', $ises)); ?></th>
                    <th><?php echo s(board_herb_category_localize('Main functions and indications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Cautions / contraindications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Dosage', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Preparation', $ises)); ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($yintonicherbs as $herb): ?>
                    <tr>
                        <th class="herb-category-library__herb-identity" scope="row"><strong><?php echo s($herb[0]); ?></strong><small><?php echo s($herb[1]); ?></small></th>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[2], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[3], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[4], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[5], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[6], $ises)); ?></td>
                        <td class="herb-category-library__compact herb-category-library__dosage"><?php echo s(board_herb_category_localize($herb[7], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[8], $ises)); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="herb-category-library__section" id="astringents"<?php echo $selectedcategory === 'astringents' ? '' : ' hidden'; ?>>
        <div class="herb-category-library__section-heading">
            <h3><?php echo s(board_herb_category_localize('26. Astringents', $ises)); ?></h3>
            <nav class="herb-category-library__pager" aria-label="<?php echo s(board_herb_category_localize('Category navigation', $ises)); ?>">
                <?php if ($previouscategoryurl !== null): ?>
                    <a rel="prev" href="<?php echo $previouscategoryurl->out(false); ?>"><?php echo s('← ' . board_herb_category_localize('Previous', $ises)); ?></a>
                <?php endif; ?>
                <?php if ($nextcategoryurl !== null): ?>
                    <a rel="next" href="<?php echo $nextcategoryurl->out(false); ?>"><?php echo s(board_herb_category_localize('Next', $ises) . ' →'); ?></a>
                <?php endif; ?>
            </nav>
        </div>
        <p class="herb-category-library__summary"><?php echo s(board_herb_category_localize('Board Guideline 2026 · 14 substances · Common foundation: stabilize and bind leakage of Essence, urine, stool, sweat, Blood, fluids or Lung Qi.', $ises)); ?></p>
        <div class="herb-category-library__similarities">
            <h4><?php echo s(board_herb_category_localize('Similarities and Clinical Overlaps', $ises)); ?></h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th><?php echo s(board_herb_category_localize('Comparison', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Herbs', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Shared clinical relationship', $ises)); ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($astringentsimilarities as $similarity): ?>
                        <tr><td><?php echo s(board_herb_category_localize($similarity[0], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[1], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[2], $ises)); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong><?php echo s(board_herb_category_localize('Selection focus:', $ises)); ?></strong> <?php echo s(board_herb_category_localize('type and location of leakage, deficiency versus excess, Heat or Cold, cough, diarrhea, urination, discharge, bleeding, sweating, toxicity and unresolved pathogens.', $ises)); ?></p>
        </div>
        <div class="herb-category-library__scroll">
            <table class="herb-category-library__comparison-table">
                <colgroup><col><col><col><col><col><col><col><col></colgroup>
                <thead><tr>
                    <th><?php echo s(board_herb_category_localize('Herb / Pharmaceutical name', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Taste', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Nature', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Channels', $ises)); ?></th>
                    <th><?php echo s(board_herb_category_localize('Main functions and indications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Cautions / contraindications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Dosage', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Preparation', $ises)); ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($astringentherbs as $herb): ?>
                    <tr>
                        <th class="herb-category-library__herb-identity" scope="row"><strong><?php echo s($herb[0]); ?></strong><small><?php echo s($herb[1]); ?></small></th>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[2], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[3], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[4], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[5], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[6], $ises)); ?></td>
                        <td class="herb-category-library__compact herb-category-library__dosage"><?php echo s(board_herb_category_localize($herb[7], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[8], $ises)); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="herb-category-library__section" id="anchor-calm-shen"<?php echo $selectedcategory === 'anchor-calm-shen' ? '' : ' hidden'; ?>>
        <div class="herb-category-library__section-heading">
            <h3><?php echo s(board_herb_category_localize('27. Anchor & Calm Shen', $ises)); ?></h3>
            <nav class="herb-category-library__pager" aria-label="<?php echo s(board_herb_category_localize('Category navigation', $ises)); ?>">
                <?php if ($previouscategoryurl !== null): ?>
                    <a rel="prev" href="<?php echo $previouscategoryurl->out(false); ?>"><?php echo s('← ' . board_herb_category_localize('Previous', $ises)); ?></a>
                <?php endif; ?>
                <?php if ($nextcategoryurl !== null): ?>
                    <a rel="next" href="<?php echo $nextcategoryurl->out(false); ?>"><?php echo s(board_herb_category_localize('Next', $ises) . ' →'); ?></a>
                <?php endif; ?>
            </nav>
        </div>
        <p class="herb-category-library__summary"><?php echo s(board_herb_category_localize('Board Guideline 2026 · 4 substances · Common foundation: settle and calm the Spirit, anchor Liver Yang and control agitation or leakage.', $ises)); ?></p>
        <div class="herb-category-library__similarities">
            <h4><?php echo s(board_herb_category_localize('Similarities and Clinical Overlaps', $ises)); ?></h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th><?php echo s(board_herb_category_localize('Comparison', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Herbs', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Shared clinical relationship', $ises)); ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($anchorcalmsimilarities as $similarity): ?>
                        <tr><td><?php echo s(board_herb_category_localize($similarity[0], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[1], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[2], $ises)); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong><?php echo s(board_herb_category_localize('Selection focus:', $ises)); ?></strong> <?php echo s(board_herb_category_localize('Liver Yang rising, Heart agitation, mineral weight, leakage, nodules, hearing or vision, topical healing, digestive weakness and required preparation.', $ises)); ?></p>
        </div>
        <div class="herb-category-library__scroll">
            <table class="herb-category-library__comparison-table">
                <colgroup><col><col><col><col><col><col><col><col></colgroup>
                <thead><tr>
                    <th><?php echo s(board_herb_category_localize('Herb / Pharmaceutical name', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Taste', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Nature', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Channels', $ises)); ?></th>
                    <th><?php echo s(board_herb_category_localize('Main functions and indications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Cautions / contraindications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Dosage', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Preparation', $ises)); ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($anchorcalmherbs as $herb): ?>
                    <tr>
                        <th class="herb-category-library__herb-identity" scope="row"><strong><?php echo s($herb[0]); ?></strong><small><?php echo s($herb[1]); ?></small></th>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[2], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[3], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[4], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[5], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[6], $ises)); ?></td>
                        <td class="herb-category-library__compact herb-category-library__dosage"><?php echo s(board_herb_category_localize($herb[7], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[8], $ises)); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="herb-category-library__section" id="nourish-calm-shen"<?php echo $selectedcategory === 'nourish-calm-shen' ? '' : ' hidden'; ?>>
        <div class="herb-category-library__section-heading">
            <h3><?php echo s(board_herb_category_localize('28. Nourish & Calm Shen', $ises)); ?></h3>
            <nav class="herb-category-library__pager" aria-label="<?php echo s(board_herb_category_localize('Category navigation', $ises)); ?>">
                <?php if ($previouscategoryurl !== null): ?>
                    <a rel="prev" href="<?php echo $previouscategoryurl->out(false); ?>"><?php echo s('← ' . board_herb_category_localize('Previous', $ises)); ?></a>
                <?php endif; ?>
                <?php if ($nextcategoryurl !== null): ?>
                    <a rel="next" href="<?php echo $nextcategoryurl->out(false); ?>"><?php echo s(board_herb_category_localize('Next', $ises) . ' →'); ?></a>
                <?php endif; ?>
            </nav>
        </div>
        <p class="herb-category-library__summary"><?php echo s(board_herb_category_localize('Board Guideline 2026 · 4 herbs · Common foundation: nourish Heart or Liver Blood, calm the Spirit and address deficiency-related insomnia or agitation.', $ises)); ?></p>
        <div class="herb-category-library__similarities">
            <h4><?php echo s(board_herb_category_localize('Similarities and Clinical Overlaps', $ises)); ?></h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th><?php echo s(board_herb_category_localize('Comparison', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Herbs', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Shared clinical relationship', $ises)); ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($nourishcalmsimilarities as $similarity): ?>
                        <tr><td><?php echo s(board_herb_category_localize($similarity[0], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[1], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[2], $ises)); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong><?php echo s(board_herb_category_localize('Selection focus:', $ises)); ?></strong> <?php echo s(board_herb_category_localize('Blood or Yin deficiency, insomnia, sweating, bowel dryness, phlegm obstructing the orifices, cough, swelling, channel obstruction and itching.', $ises)); ?></p>
        </div>
        <div class="herb-category-library__scroll">
            <table class="herb-category-library__comparison-table">
                <colgroup><col><col><col><col><col><col><col><col></colgroup>
                <thead><tr>
                    <th><?php echo s(board_herb_category_localize('Herb / Pharmaceutical name', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Taste', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Nature', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Channels', $ises)); ?></th>
                    <th><?php echo s(board_herb_category_localize('Main functions and indications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Cautions / contraindications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Dosage', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Preparation', $ises)); ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($nourishcalmherbs as $herb): ?>
                    <tr>
                        <th class="herb-category-library__herb-identity" scope="row"><strong><?php echo s($herb[0]); ?></strong><small><?php echo s($herb[1]); ?></small></th>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[2], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[3], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[4], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[5], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[6], $ises)); ?></td>
                        <td class="herb-category-library__compact herb-category-library__dosage"><?php echo s(board_herb_category_localize($herb[7], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[8], $ises)); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="herb-category-library__section" id="orifice-opening-aromatics"<?php echo $selectedcategory === 'orifice-opening-aromatics' ? '' : ' hidden'; ?>>
        <div class="herb-category-library__section-heading">
            <h3><?php echo s(board_herb_category_localize('29. Orifice Opening Aromatics', $ises)); ?></h3>
            <nav class="herb-category-library__pager" aria-label="<?php echo s(board_herb_category_localize('Category navigation', $ises)); ?>">
                <?php if ($previouscategoryurl !== null): ?>
                    <a rel="prev" href="<?php echo $previouscategoryurl->out(false); ?>"><?php echo s('← ' . board_herb_category_localize('Previous', $ises)); ?></a>
                <?php endif; ?>
                <?php if ($nextcategoryurl !== null): ?>
                    <a rel="next" href="<?php echo $nextcategoryurl->out(false); ?>"><?php echo s(board_herb_category_localize('Next', $ises) . ' →'); ?></a>
                <?php endif; ?>
            </nav>
        </div>
        <p class="herb-category-library__summary"><?php echo s(board_herb_category_localize('Board Guideline 2026 · 1 substance · Common foundation: clear intense Heat, open the orifices, awaken the Spirit, extinguish Wind and relieve toxicity.', $ises)); ?></p>
        <div class="herb-category-library__similarities">
            <h4><?php echo s(board_herb_category_localize('Similarities and Clinical Overlaps', $ises)); ?></h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th><?php echo s(board_herb_category_localize('Comparison', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Herbs', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Shared clinical relationship', $ises)); ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($orificesimilarities as $similarity): ?>
                        <tr><td><?php echo s(board_herb_category_localize($similarity[0], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[1], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[2], $ises)); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong><?php echo s(board_herb_category_localize('Selection focus:', $ises)); ?></strong> <?php echo s(board_herb_category_localize('excess Heat, phlegm obstructing the orifices, loss of consciousness, tremor or convulsion, toxicity, pregnancy and digestive deficiency.', $ises)); ?></p>
        </div>
        <div class="herb-category-library__scroll">
            <table class="herb-category-library__comparison-table">
                <colgroup><col><col><col><col><col><col><col><col></colgroup>
                <thead><tr>
                    <th><?php echo s(board_herb_category_localize('Herb / Pharmaceutical name', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Taste', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Nature', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Channels', $ises)); ?></th>
                    <th><?php echo s(board_herb_category_localize('Main functions and indications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Cautions / contraindications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Dosage', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Preparation', $ises)); ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($orificeherbs as $herb): ?>
                    <tr>
                        <th class="herb-category-library__herb-identity" scope="row"><strong><?php echo s($herb[0]); ?></strong><small><?php echo s($herb[1]); ?></small></th>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[2], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[3], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[4], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[5], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[6], $ises)); ?></td>
                        <td class="herb-category-library__compact herb-category-library__dosage"><?php echo s(board_herb_category_localize($herb[7], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[8], $ises)); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="herb-category-library__section" id="extinguish-liver-wind"<?php echo $selectedcategory === 'extinguish-liver-wind' ? '' : ' hidden'; ?>>
        <div class="herb-category-library__section-heading">
            <h3><?php echo s(board_herb_category_localize('30. Extinguish Liver Wind', $ises)); ?></h3>
            <nav class="herb-category-library__pager" aria-label="<?php echo s(board_herb_category_localize('Category navigation', $ises)); ?>">
                <?php if ($previouscategoryurl !== null): ?>
                    <a rel="prev" href="<?php echo $previouscategoryurl->out(false); ?>"><?php echo s('← ' . board_herb_category_localize('Previous', $ises)); ?></a>
                <?php endif; ?>
                <?php if ($nextcategoryurl !== null): ?>
                    <a rel="next" href="<?php echo $nextcategoryurl->out(false); ?>"><?php echo s(board_herb_category_localize('Next', $ises) . ' →'); ?></a>
                <?php endif; ?>
            </nav>
        </div>
        <p class="herb-category-library__summary"><?php echo s(board_herb_category_localize('Board Guideline 2026 · 6 substances · Common foundation: calm the Liver, extinguish internal Wind, control spasms and address Yang rising or channel obstruction.', $ises)); ?></p>
        <div class="herb-category-library__similarities">
            <h4><?php echo s(board_herb_category_localize('Similarities and Clinical Overlaps', $ises)); ?></h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th><?php echo s(board_herb_category_localize('Comparison', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Herbs', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Shared clinical relationship', $ises)); ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($liverwindsimilarities as $similarity): ?>
                        <tr><td><?php echo s(board_herb_category_localize($similarity[0], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[1], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[2], $ises)); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong><?php echo s(board_herb_category_localize('Selection focus:', $ises)); ?></strong> <?php echo s(board_herb_category_localize('Heat or Cold origin of internal Wind, Liver Yang rising, spasms, tremor, visual symptoms, rebellious Qi, channel obstruction, wheezing and digestive weakness.', $ises)); ?></p>
        </div>
        <div class="herb-category-library__scroll">
            <table class="herb-category-library__comparison-table">
                <colgroup><col><col><col><col><col><col><col><col></colgroup>
                <thead><tr>
                    <th><?php echo s(board_herb_category_localize('Herb / Pharmaceutical name', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Taste', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Nature', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Channels', $ises)); ?></th>
                    <th><?php echo s(board_herb_category_localize('Main functions and indications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Cautions / contraindications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Dosage', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Preparation', $ises)); ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($liverwindherbs as $herb): ?>
                    <tr>
                        <th class="herb-category-library__herb-identity" scope="row"><strong><?php echo s($herb[0]); ?></strong><small><?php echo s($herb[1]); ?></small></th>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[2], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[3], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[4], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[5], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[6], $ises)); ?></td>
                        <td class="herb-category-library__compact herb-category-library__dosage"><?php echo s(board_herb_category_localize($herb[7], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[8], $ises)); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="herb-category-library__section" id="expel-parasites"<?php echo $selectedcategory === 'expel-parasites' ? '' : ' hidden'; ?>>
        <div class="herb-category-library__section-heading">
            <h3><?php echo s(board_herb_category_localize('31. Expel Parasites', $ises)); ?></h3>
            <nav class="herb-category-library__pager" aria-label="<?php echo s(board_herb_category_localize('Category navigation', $ises)); ?>">
                <?php if ($previouscategoryurl !== null): ?>
                    <a rel="prev" href="<?php echo $previouscategoryurl->out(false); ?>"><?php echo s('← ' . board_herb_category_localize('Previous', $ises)); ?></a>
                <?php endif; ?>
                <?php if ($nextcategoryurl !== null): ?>
                    <a rel="next" href="<?php echo $nextcategoryurl->out(false); ?>"><?php echo s(board_herb_category_localize('Next', $ises) . ' →'); ?></a>
                <?php endif; ?>
            </nav>
        </div>
        <p class="herb-category-library__summary"><?php echo s(board_herb_category_localize('Board Guideline 2026 · 1 herb · Common foundation: expel intestinal parasites while moving Qi and relieving accumulation.', $ises)); ?></p>
        <div class="herb-category-library__similarities">
            <h4><?php echo s(board_herb_category_localize('Similarities and Clinical Overlaps', $ises)); ?></h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th><?php echo s(board_herb_category_localize('Comparison', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Herbs', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Shared clinical relationship', $ises)); ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($parasitesimilarities as $similarity): ?>
                        <tr><td><?php echo s(board_herb_category_localize($similarity[0], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[1], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[2], $ises)); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong><?php echo s(board_herb_category_localize('Selection focus:', $ises)); ?></strong> <?php echo s(board_herb_category_localize('parasite type, abdominal distension, constipation, tenesmus, urination, malaria and Middle Qi deficiency.', $ises)); ?></p>
        </div>
        <div class="herb-category-library__scroll">
            <table class="herb-category-library__comparison-table">
                <colgroup><col><col><col><col><col><col><col><col></colgroup>
                <thead><tr>
                    <th><?php echo s(board_herb_category_localize('Herb / Pharmaceutical name', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Taste', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Nature', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Channels', $ises)); ?></th>
                    <th><?php echo s(board_herb_category_localize('Main functions and indications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Cautions / contraindications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Dosage', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Preparation', $ises)); ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($parasiteherbs as $herb): ?>
                    <tr>
                        <th class="herb-category-library__herb-identity" scope="row"><strong><?php echo s($herb[0]); ?></strong><small><?php echo s($herb[1]); ?></small></th>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[2], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[3], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[4], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[5], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[6], $ises)); ?></td>
                        <td class="herb-category-library__compact herb-category-library__dosage"><?php echo s(board_herb_category_localize($herb[7], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[8], $ises)); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="herb-category-library__section" id="external-use"<?php echo $selectedcategory === 'external-use' ? '' : ' hidden'; ?>>
        <div class="herb-category-library__section-heading">
            <h3><?php echo s(board_herb_category_localize('32. External Use', $ises)); ?></h3>
            <nav class="herb-category-library__pager" aria-label="<?php echo s(board_herb_category_localize('Category navigation', $ises)); ?>">
                <?php if ($previouscategoryurl !== null): ?>
                    <a rel="prev" href="<?php echo $previouscategoryurl->out(false); ?>"><?php echo s('← ' . board_herb_category_localize('Previous', $ises)); ?></a>
                <?php endif; ?>
                <?php if ($nextcategoryurl !== null): ?>
                    <a rel="next" href="<?php echo $nextcategoryurl->out(false); ?>"><?php echo s(board_herb_category_localize('Next', $ises) . ' →'); ?></a>
                <?php endif; ?>
            </nav>
        </div>
        <p class="herb-category-library__summary"><?php echo s(board_herb_category_localize('Board Guideline 2026 · 1 herb · Common foundation: dry Dampness, kill parasites, stop itching and warm Kidney Yang when appropriately used.', $ises)); ?></p>
        <div class="herb-category-library__similarities">
            <h4><?php echo s(board_herb_category_localize('Similarities and Clinical Overlaps', $ises)); ?></h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th><?php echo s(board_herb_category_localize('Comparison', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Herbs', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Shared clinical relationship', $ises)); ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($externalusesimilarities as $similarity): ?>
                        <tr><td><?php echo s(board_herb_category_localize($similarity[0], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[1], $ises)); ?></td><td><?php echo s(board_herb_category_localize($similarity[2], $ises)); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong><?php echo s(board_herb_category_localize('Selection focus:', $ises)); ?></strong> <?php echo s(board_herb_category_localize('topical versus internal use, Dampness, parasites, itching, painful obstruction, Kidney Yang deficiency, toxicity and Heat patterns.', $ises)); ?></p>
        </div>
        <div class="herb-category-library__scroll">
            <table class="herb-category-library__comparison-table">
                <colgroup><col><col><col><col><col><col><col><col></colgroup>
                <thead><tr>
                    <th><?php echo s(board_herb_category_localize('Herb / Pharmaceutical name', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Taste', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Nature', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Channels', $ises)); ?></th>
                    <th><?php echo s(board_herb_category_localize('Main functions and indications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Cautions / contraindications', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Dosage', $ises)); ?></th><th><?php echo s(board_herb_category_localize('Preparation', $ises)); ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($externaluseherbs as $herb): ?>
                    <tr>
                        <th class="herb-category-library__herb-identity" scope="row"><strong><?php echo s($herb[0]); ?></strong><small><?php echo s($herb[1]); ?></small></th>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[2], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[3], $ises)); ?></td>
                        <td class="herb-category-library__compact"><?php echo s(board_herb_category_localize($herb[4], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[5], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[6], $ises)); ?></td>
                        <td class="herb-category-library__compact herb-category-library__dosage"><?php echo s(board_herb_category_localize($herb[7], $ises)); ?></td>
                        <td><?php echo s(board_herb_category_localize($herb[8], $ises)); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
<?php
echo $OUTPUT->footer();
