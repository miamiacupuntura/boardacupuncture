#!/usr/bin/env python3
"""Extract the Handbook clinical-case SCORM questions into a Quiz JSON bank.

The Moodle database and file pool are read-only. Only the requested JSON output
is created or atomically replaced.
"""

from __future__ import annotations

import argparse
import difflib
import hashlib
import html
import json
import re
import subprocess
import tempfile
import zipfile
from pathlib import Path
from typing import Any

from scorm_word_export import (
    answer_text,
    choose_placements,
    find_quizdata_member,
    load_inventory,
    normalise_text,
    package_path,
)


SCRIPT_DIR = Path(__file__).resolve().parent
DEFAULT_FORMULA_DATA = (
    SCRIPT_DIR.parents[2] / "blocks" / "myprogress" / "formulas-study-guide-data.json"
)
DEFAULT_COURSES = [45, 76, 41, 82]
TARGET_COURSE = "herbal comp review module exams"
TARGET_TOPIC = "handbook-of-oriental-medicine. clinical cases"
EMBEDDED_ONE = re.compile(r"(?<=[A-Za-z])1(?=[A-Za-z])")
SUSPICIOUS_EMBEDDED_DIGIT = re.compile(r"(?<=[A-Za-z])[0-9](?=[A-Za-z])")
PINYIN_I_OCR = re.compile(r"\b([BDJLMNPQRSTXYZ])[lL]\b")


def normalise_name(value: Any) -> str:
    return re.sub(r"\s+", " ", str(value or "").strip()).casefold()


def source_text(value: Any) -> str:
    """Decode source markup and normalize known OCR substitutions."""
    decoded = normalise_text(value)
    for _ in range(3):
        unescaped = html.unescape(decoded)
        if unescaped == decoded:
            break
        decoded = unescaped
    decoded = re.sub(r"\s*&\s*", " and ", decoded)
    decoded = EMBEDDED_ONE.sub("i", decoded)
    decoded = PINYIN_I_OCR.sub(lambda match: f"{match.group(1)}I", decoded)
    decoded = re.sub(
        r"\bBU ZHONG VI QI TANG\b",
        "BU ZHONG YI QI TANG",
        decoded,
        flags=re.IGNORECASE,
    )
    return decoded


def load_formula_profiles(path: Path) -> dict[str, dict[str, Any]]:
    """Load source-grounded formula profiles keyed by normalized pinyin name."""
    payload = json.loads(path.read_text(encoding="utf-8"))
    profiles: dict[str, dict[str, Any]] = {}
    for collection in ("formulas", "supplementalFormulas"):
        for profile in payload.get(collection) or []:
            if not isinstance(profile, dict):
                continue
            key = normalise_name(profile.get("name"))
            if key and key not in profiles:
                profiles[key] = profile
    if not profiles:
        raise ValueError(f"No formula profiles found in {path}")
    return profiles


def diagnostic_feedback(
    correctoption: str,
    profiles: dict[str, dict[str, Any]],
    prompt: str = "",
    options: list[str] | None = None,
) -> dict[str, Any] | None:
    """Match the correct option to its guideline profile and build rationale."""
    def match_profile(option: str) -> dict[str, Any] | None:
        normalizedoption = normalise_name(source_text(option))
        for name in sorted(profiles, key=len, reverse=True):
            if normalizedoption == name or normalizedoption.startswith(f"{name} "):
                return profiles[name]

        # OCR defects usually affect one pinyin letter. Compare only the formula
        # name before the translated title so explanatory prose cannot create a match.
        optionname = normalise_name(normalizedoption.split("(", 1)[0])
        candidates = [
            name
            for name in profiles
            if abs(len(name) - len(optionname)) <= (2 if len(name) >= 18 else 1)
        ]
        closematches = difflib.get_close_matches(optionname, candidates, n=1, cutoff=0.90)
        return profiles[closematches[0]] if closematches else None

    profile = match_profile(correctoption)
    if profile is None:
        return None

    fields = {
        "formula": profile.get("name"),
        "pattern": profile.get("pattern"),
        "patternEs": profile.get("patternEs"),
        "clue": profile.get("clue"),
        "clueEs": profile.get("clueEs"),
        "action": profile.get("action"),
        "actionEs": profile.get("actionEs"),
        "reasoning": profile.get("examFocus"),
        "reasoningEs": profile.get("examFocusEs"),
        "differential": profile.get("comparison"),
        "differentialEs": profile.get("comparisonEs"),
    }
    cleaned = {
        key: source_text(value)
        for key, value in fields.items()
        if source_text(value)
    }

    normalizedprompt = normalise_name(prompt)
    profilename = normalise_name(profile.get("name"))
    if (
        profilename == "chai ge jie ji tang"
        and "pain around her eye" in normalizedprompt
        and "slightly surging" in normalizedprompt
    ):
        cleaned["caseReasoning"] = (
            "The illness began as exterior Wind-Cold, shown by chills, body aches, "
            "runny nose and cough with white phlegm. It is now transforming into Heat: "
            "the fever is rising while aversion to cold is only mild, and thirst, a red "
            "tongue with yellow coat, a floating slightly surging pulse and orbital pain "
            "have appeared. This progression identifies constrained exterior Cold in the "
            "muscle layer transforming into Heat, the key Chai Ge Jie Ji Tang pattern."
        )
        cleaned["caseReasoningEs"] = (
            "El cuadro comenzó como Viento-Frío exterior, indicado por escalofríos, dolores "
            "corporales, rinorrea y tos con flema blanca. Ahora se transforma en Calor: la "
            "fiebre aumenta mientras la aversión al frío es leve, y aparecen sed, lengua "
            "roja con capa amarilla, pulso flotante ligeramente desbordante y dolor orbital. "
            "Esta evolución identifica Frío exterior constreñido en la capa muscular que se "
            "transforma en Calor, el patrón clave de Chai Ge Jie Ji Tang."
        )
    elif (
        profilename == "da cheng qi tang"
        and "solid mass" in normalizedprompt
        and "deep pulse" in normalizedprompt
    ):
        cleaned["caseReasoning"] = (
            "This is severe Yangming-Fu Heat binding with stool impaction. The hard, "
            "palpable abdominal mass, guarding and marked pain establish fullness, "
            "hardness and excess; the dry red tongue with yellow coat and deep, rapid, "
            "forceful pulse confirm interior Heat excess. The earlier green, foul watery "
            "stool can represent overflow around an impaction rather than resolution. "
            "That severe combination calls for Da Cheng Qi Tang rather than the milder "
            "Xiao Cheng Qi Tang pattern."
        )
        cleaned["caseReasoningEs"] = (
            "Se trata de una ligadura grave de Calor en Yangming-Fu con impactación fecal. "
            "La masa abdominal dura y palpable, la defensa y el dolor marcado establecen "
            "plenitud, dureza y exceso; la lengua roja seca con capa amarilla y el pulso "
            "profundo, rápido y fuerte confirman Calor interno de exceso. Las heces acuosas, "
            "verdes y fétidas previas pueden representar rebosamiento alrededor de una "
            "impactación, no resolución. Esta combinación grave requiere Da Cheng Qi Tang, "
            "no el patrón más leve de Xiao Cheng Qi Tang."
        )
    elif (
        profilename == "xiao cheng qi tang"
        and "rice cake" in normalizedprompt
        and "rolling and rapid" in normalizedprompt
    ):
        cleaned["caseReasoning"] = (
            "The constipation, abdominal pain, irritability and low fever show Heat and "
            "stagnation binding in the Yangming bowel. A red tongue with a dry yellow coat "
            "and a rolling, rapid pulse support interior Heat with accumulation. The case "
            "does not describe the severe guarding, rock-hard mass, pronounced distention "
            "or deep forceful pulse required for Da Cheng Qi Tang, so the more moderate "
            "purgation of Xiao Cheng Qi Tang is the better match."
        )
        cleaned["caseReasoningEs"] = (
            "El estreñimiento, el dolor abdominal, la irritabilidad y la fiebre baja muestran "
            "Calor y estancamiento ligados en el intestino Yangming. La lengua roja con capa "
            "amarilla seca y el pulso rodante y rápido apoyan Calor interno con acumulación. "
            "El caso no presenta la defensa grave, la masa pétrea, la distensión pronunciada "
            "ni el pulso profundo y fuerte que exigirían Da Cheng Qi Tang; por eso corresponde "
            "la purgación más moderada de Xiao Cheng Qi Tang."
        )
    elif (
        profilename == "tiao wei cheng qi tang"
        and "toothache" in normalizedprompt
        and "foul halitosis" in normalizedprompt
    ):
        cleaned["caseReasoning"] = (
            "The toothache, swollen gums, intense thirst and foul breath locate exuberant "
            "Heat in the Stomach/Yangming system. Constipation, dark urine, a red tongue "
            "with yellow coat and a rolling, rapid pulse show that the Heat is consuming "
            "fluids and creating bowel dryness. Because the case lacks marked abdominal "
            "fullness, focal distention or a hard painful mass, the mild Heat-purging and "
            "dryness-softening action of Tiao Wei Cheng Qi Tang fits better than the stronger "
            "Cheng Qi formulas."
        )
        cleaned["caseReasoningEs"] = (
            "El dolor dental, las encías inflamadas, la sed intensa y la halitosis ubican "
            "Calor exuberante en Estómago/Yangming. El estreñimiento, la orina oscura, la "
            "lengua roja con capa amarilla y el pulso rodante y rápido muestran que el Calor "
            "consume líquidos y produce sequedad intestinal. Como no hay plenitud abdominal "
            "marcada, distensión focal ni masa dura y dolorosa, la acción suave de Tiao Wei "
            "Cheng Qi Tang para purgar Calor y ablandar sequedad encaja mejor que las fórmulas "
            "Cheng Qi más fuertes."
        )
    elif (
        profilename == "huo xiang zheng qi san"
        and "child-care facility" in normalizedprompt
        and "diarrhea and vomiting" in normalizedprompt
    ):
        cleaned["caseReasoning"] = (
            "The acute diarrhea, vomiting, nausea, epigastric bloating, flatulence and loss "
            "of appetite show Dampness obstructing the Middle Burner and disrupting the "
            "ascending and descending of Spleen-Stomach Qi. Feeling feverish with headache "
            "adds an exterior component. Huo Xiang Zheng Qi San fits this combined pattern "
            "because it releases the Exterior while transforming Dampness, regulating Qi "
            "and harmonizing the Middle."
        )
        cleaned["caseReasoningEs"] = (
            "La diarrea y el vómito agudos, la náusea, la distensión epigástrica, la "
            "flatulencia y la pérdida de apetito muestran Humedad obstruyendo el Jiao Medio "
            "y alterando el ascenso y descenso del Qi de Bazo-Estómago. La sensación febril "
            "con cefalea añade un componente exterior. Huo Xiang Zheng Qi San corresponde "
            "a este patrón combinado porque libera el Exterior mientras transforma Humedad, "
            "regula Qi y armoniza el Centro."
        )
    elif (
        profilename == "du huo ji sheng tang"
        and "over five years" in normalizedprompt
        and "cold or rainy days" in normalizedprompt
    ):
        cleaned["caseReasoning"] = (
            "The five-year history, low-back location and worsening with cold or rainy "
            "weather identify chronic Wind-Cold-Damp painful obstruction. Relief from a "
            "heating pad confirms the Cold component. Knee weakness, a pale tongue with "
            "white coat and a thready, feeble pulse show the underlying Liver-Kidney and "
            "Qi-Blood deficiency that allows the obstruction to persist. Du Huo Ji Sheng "
            "Tang is appropriate because it both expels the pathogenic factors and "
            "strengthens the deficient sinews, bones, Qi and Blood."
        )
        cleaned["caseReasoningEs"] = (
            "Los cinco años de evolución, la localización lumbar y el empeoramiento con "
            "frío o lluvia identifican obstrucción dolorosa crónica por Viento-Frío-Humedad. "
            "La mejoría con una almohadilla térmica confirma el componente Frío. La debilidad "
            "de rodillas, la lengua pálida con capa blanca y el pulso filiforme y débil "
            "muestran la deficiencia subyacente de Hígado-Riñón y Qi-Sangre que permite que "
            "la obstrucción persista. Du Huo Ji Sheng Tang corresponde porque expulsa los "
            "patógenos y fortalece tendones, huesos, Qi y Sangre."
        )
    elif (
        profilename == "li zhong wan"
        and "menstrual flow is heavy" in normalizedprompt
        and "chronic loose stool" in normalizedprompt
    ):
        cleaned["caseReasoning"] = (
            "This is Spleen-Stomach deficiency-Cold. Chronic loose stool, poor appetite, "
            "bloating and abdominal pain relieved by warmth and pressure show that the "
            "Middle Burner lacks Qi and Yang to transform food and fluids. The deep, weak "
            "pulse confirms deficiency. Prolonged heavy, light-red bleeding without clots "
            "adds failure of Spleen Qi to contain Blood. Li Zhong Wan fits because it warms "
            "the Middle, tonifies Qi and strengthens the Spleen without treating the severe "
            "Yang collapse or violent Cold pain required by the stronger alternatives."
        )
        cleaned["caseReasoningEs"] = (
            "Se trata de Frío por deficiencia de Bazo-Estómago. Las heces sueltas crónicas, "
            "el poco apetito, la distensión y el dolor abdominal aliviado por calor y presión "
            "muestran que el Jiao Medio carece de Qi y Yang para transformar alimentos y "
            "líquidos. El pulso profundo y débil confirma deficiencia. El sangrado prolongado, "
            "abundante y rojo claro sin coágulos añade incapacidad del Qi de Bazo para contener "
            "la Sangre. Li Zhong Wan corresponde porque calienta el Centro, tonifica Qi y "
            "fortalece Bazo, sin el colapso grave de Yang ni el dolor violento por Frío que "
            "exigirían las alternativas más fuertes."
        )
    elif (
        profilename == "si ni tang"
        and "knees drawn up" in normalizedprompt
        and "undigested food in diarrhea" in normalizedprompt
    ):
        cleaned["caseReasoning"] = (
            "This is Shaoyin Cold transformation with devastated Yang. Persistent cold of "
            "the hands, feet and knees, a curled posture, profound fatigue and sleepiness "
            "show that Yang can no longer warm the body. Vomiting, diarrhea with undigested "
            "food, a cold abdomen and no thirst show failure to transform food and fluids. "
            "The bland tongue with a white slippery coat and deep, weak pulse confirm severe "
            "interior Cold and deficiency. Si Ni Tang is required to rescue Yang, warm the "
            "Middle and restore the pulse; ordinary Qi tonics are not strong enough."
        )
        cleaned["caseReasoningEs"] = (
            "Se trata de transformación Fría de Shaoyin con Yang devastado. El frío persistente "
            "de manos, pies y rodillas, la postura encogida, el agotamiento profundo y la "
            "somnolencia muestran que el Yang ya no calienta el cuerpo. El vómito, la diarrea "
            "con alimento no digerido, el abdomen frío y la ausencia de sed indican incapacidad "
            "para transformar alimentos y líquidos. La lengua insípida con capa blanca "
            "resbaladiza y el pulso profundo y débil confirman Frío interno grave con deficiencia. "
            "Si Ni Tang debe rescatar Yang, calentar el Centro y restaurar el pulso; los tónicos "
            "ordinarios de Qi no son suficientes."
        )
    elif (
        profilename == "si jun zi tang"
        and "weakness of voice" in normalizedprompt
        and "weakness of limbs" in normalizedprompt
    ):
        cleaned["caseReasoning"] = (
            "This is uncomplicated Spleen Qi deficiency. A pale face, weak voice and limb "
            "weakness show deficient Qi, while poor appetite and loose stool identify the "
            "Spleen as the affected system. The bland pale tongue with a thin white coat "
            "and thready, weak pulse confirm deficiency without prominent Cold, Heat, Qi "
            "sinking or Blood deficiency. Si Jun Zi Tang is the foundational choice to "
            "tonify Spleen Qi and restore transportation and transformation."
        )
        cleaned["caseReasoningEs"] = (
            "Se trata de deficiencia simple de Qi de Bazo. El rostro pálido, la voz débil y "
            "la debilidad de extremidades muestran Qi deficiente, mientras que el poco apetito "
            "y las heces sueltas identifican al Bazo como sistema afectado. La lengua pálida e "
            "insípida con capa blanca fina y el pulso filiforme y débil confirman deficiencia "
            "sin Frío, Calor, hundimiento de Qi ni deficiencia de Sangre predominantes. Si Jun "
            "Zi Tang es la elección fundamental para tonificar Qi de Bazo y restaurar transporte "
            "y transformación."
        )
    elif (
        profilename == "bu zhong yi qi tang"
        and "over two months" in normalizedprompt
        and "brought on through exertion" in normalizedprompt
    ):
        cleaned["caseReasoning"] = (
            "The prolonged low-grade fever that appears in the morning and is triggered by "
            "exertion is deficiency fever from sinking Middle Qi, not an excess Heat pattern. "
            "Spontaneous sweating and shortness of breath show Qi failing to secure the "
            "exterior and support respiration; chronic loose stool identifies Spleen Qi "
            "deficiency. A pale tongue and weak pulse confirm deficiency. Thirst with a "
            "preference for warm drinks does not indicate Yin deficiency Heat. Bu Zhong Yi "
            "Qi Tang fits because it tonifies Middle Qi, raises the sunken Yang and treats "
            "deficiency fever."
        )
        cleaned["caseReasoningEs"] = (
            "La fiebre baja prolongada que aparece por la mañana y se desencadena con esfuerzo "
            "es fiebre por deficiencia debida al hundimiento del Qi Medio, no un patrón de "
            "Calor de exceso. El sudor espontáneo y la disnea muestran que el Qi no asegura el "
            "Exterior ni sostiene la respiración; las heces sueltas crónicas identifican "
            "deficiencia de Qi de Bazo. La lengua pálida y el pulso débil confirman deficiencia. "
            "La sed con preferencia por bebidas tibias no indica Calor por deficiencia de Yin. "
            "Bu Zhong Yi Qi Tang corresponde porque tonifica Qi Medio, eleva Yang hundido y "
            "trata la fiebre por deficiencia."
        )
    elif (
        profilename == "sheng mai san"
        and "heart attack" in normalizedprompt
        and "scanty sputum" in normalizedprompt
    ):
        cleaned["caseReasoning"] = (
            "This is combined Heart-Lung Qi and Yin deficiency. Chest pain and shortness of "
            "breath worsened by activity show deficient Qi failing to support circulation and "
            "respiration. Dry mouth, difficult-to-expectorate scanty sputum, night sweating "
            "and a thin dry tongue coat show fluid and Yin depletion. The feeble, thready "
            "pulse confirms deficiency of both Qi and Yin. Sheng Mai San fits because it "
            "augments Qi, nourishes Yin, generates fluids and restrains leakage of sweat."
        )
        cleaned["caseReasoningEs"] = (
            "Se trata de deficiencia combinada de Qi y Yin de Corazón-Pulmón. El dolor torácico "
            "y la disnea agravados por actividad muestran Qi deficiente incapaz de sostener "
            "circulación y respiración. La boca seca, el esputo escaso difícil de expulsar, el "
            "sudor nocturno y la capa lingual fina y seca muestran agotamiento de líquidos y "
            "Yin. El pulso filiforme y débil confirma deficiencia de Qi y Yin. Sheng Mai San "
            "corresponde porque aumenta Qi, nutre Yin, genera líquidos y contiene la pérdida "
            "de sudor."
        )
    elif (
        profilename == "liu wei di huang wan"
        and "heat sensation on palms soles" in normalizedprompt
        and "red, no coating" in normalizedprompt
    ):
        cleaned["caseReasoning"] = (
            "This is Kidney Yin deficiency with deficiency Heat. Low-back soreness and "
            "weakness, tinnitus, reduced hearing and spermatorrhea identify Kidney deficiency. "
            "Dizziness and visual weakness reflect inadequate Essence and Yin nourishment. "
            "Night sweating, heat in the palms and soles, dry throat, a red peeled tongue and "
            "a thready, rapid pulse establish Yin deficiency rather than Qi, Blood or Yang "
            "deficiency. Liu Wei Di Huang Wan fits because it enriches Kidney-Liver Yin while "
            "balancing the associated deficiency Heat and fluid metabolism."
        )
        cleaned["caseReasoningEs"] = (
            "Se trata de deficiencia de Yin de Riñón con Calor por deficiencia. El dolor y la "
            "debilidad lumbar, el tinnitus, la disminución auditiva y la espermatorrea identifican "
            "deficiencia de Riñón. El mareo y la debilidad visual reflejan nutrición insuficiente "
            "de Esencia y Yin. El sudor nocturno, el calor en palmas y plantas, la garganta seca, "
            "la lengua roja pelada y el pulso filiforme y rápido establecen deficiencia de Yin, "
            "no de Qi, Sangre o Yang. Liu Wei Di Huang Wan corresponde porque enriquece Yin de "
            "Riñón-Hígado mientras equilibra el Calor por deficiencia y el metabolismo de líquidos."
        )
    elif (
        profilename == "jin gui shen qi wan"
        and "benign prostate hypertrophy" in normalizedprompt
        and "dribbling through the day" in normalizedprompt
    ):
        cleaned["caseReasoning"] = (
            "This is Kidney Yang deficiency impairing Lower-Burner Qi transformation. "
            "Dysuria, nocturnal frequency and daytime dribbling show that deficient Kidney "
            "Yang cannot regulate the Bladder and transform fluids. Fatigue, feeling cold, "
            "low-back aching and reduced libido identify the underlying Yang deficiency. "
            "A pale tongue with thin white coat and a deep, weak pulse confirm interior Cold "
            "and deficiency. Jin Gui Shen Qi Wan fits because it warms and tonifies Kidney "
            "Yang while restoring Qi transformation and urination."
        )
        cleaned["caseReasoningEs"] = (
            "Se trata de deficiencia de Yang de Riñón que altera la transformación de Qi del "
            "Jiao Inferior. La disuria, la frecuencia nocturna y el goteo diurno muestran que "
            "el Yang de Riñón deficiente no regula Vejiga ni transforma líquidos. La fatiga, "
            "la sensación de frío, el dolor lumbar y la libido reducida identifican la "
            "deficiencia de Yang subyacente. La lengua pálida con capa blanca fina y el pulso "
            "profundo y débil confirman Frío interno y deficiencia. Jin Gui Shen Qi Wan "
            "corresponde porque calienta y tonifica Yang de Riñón mientras restaura la "
            "transformación de Qi y la micción."
        )
    elif (
        profilename == "su zi jiang qi tang"
        and "emphysema" in normalizedprompt
        and "much watery sputum" in normalizedprompt
    ):
        cleaned["caseReasoning"] = (
            "This is excess above with deficiency below. Wheezing, chest tightness, cough "
            "and copious watery sputum show Cold-Phlegm obstructing the Lung and causing Lung "
            "Qi to rebel upward. Weak legs and swollen ankles in an older patient point to "
            "Kidney deficiency below failing to grasp Qi and transform fluids. A greasy white "
            "tongue coat confirms Cold-Phlegm, while the rolling but weak pulse captures both "
            "Phlegm excess and underlying deficiency. Su Zi Jiang Qi Tang fits because it "
            "descends Lung Qi, calms wheezing and warm-transforms Cold-Phlegm while addressing "
            "the lower deficiency."
        )
        cleaned["caseReasoningEs"] = (
            "Se trata de exceso arriba con deficiencia abajo. La sibilancia, la opresión "
            "torácica, la tos y el esputo acuoso abundante muestran Flema-Frío obstruyendo "
            "Pulmón y haciendo que el Qi de Pulmón ascienda en rebelión. Las piernas débiles "
            "y los tobillos hinchados en una paciente mayor señalan deficiencia de Riñón abajo "
            "que no capta Qi ni transforma líquidos. La capa blanca grasosa confirma Flema-Frío, "
            "mientras el pulso rodante pero débil expresa exceso de Flema con deficiencia "
            "subyacente. Su Zi Jiang Qi Tang corresponde porque desciende Qi de Pulmón, calma "
            "sibilancias y calienta-transforma Flema-Frío mientras atiende la deficiencia inferior."
        )
    elif (
        profilename == "tao he cheng qi tang"
        and "speech became unintelligible" in normalizedprompt
        and "very tight and tender" in normalizedprompt
    ):
        cleaned["caseReasoning"] = (
            "This is Blood stasis with Heat accumulation in the Lower Burner. Severe fixed "
            "lower-abdominal pain, a tight tender abdomen and constipation show binding of "
            "matter and Blood; rising high fever, irritability, restlessness and delirium show "
            "Heat disturbing the Shen. A red tongue with yellow coat confirms Heat, while the "
            "choppy, wiry pulse supports Blood stasis and constraint. Tao He Cheng Qi Tang fits "
            "because it drains Heat, opens the bowels and breaks Blood stasis. In actual care, "
            "high fever, delirium and severe abdominal tenderness require emergency evaluation."
        )
        cleaned["caseReasoningEs"] = (
            "Se trata de estasis de Sangre con acumulación de Calor en el Jiao Inferior. El "
            "dolor hipogástrico fijo e intenso, el abdomen tenso y doloroso y el estreñimiento "
            "muestran ligadura de materia y Sangre; la fiebre alta creciente, irritabilidad, "
            "inquietud y delirio muestran Calor perturbando Shen. La lengua roja con capa "
            "amarilla confirma Calor, y el pulso entrecortado y de cuerda apoya estasis y "
            "constricción. Tao He Cheng Qi Tang corresponde porque drena Calor, abre intestino "
            "y rompe estasis de Sangre. En atención real, fiebre alta, delirio y dolor abdominal "
            "intenso requieren evaluación de emergencia."
        )
    elif (
        profilename == "si shen wan"
        and "every morning around 5am" in normalizedprompt
        and "cold hands and feet" in normalizedprompt
    ):
        cleaned["caseReasoning"] = (
            "This is the classic dawn or 'cock-crow' diarrhea pattern from combined Spleen "
            "and Kidney Yang deficiency. Diarrhea predictably around 5 a.m. is the decisive "
            "timing clue. Poor appetite and indigestion show weak Spleen transformation, "
            "while low-back soreness, cold hands and feet, fatigue and weakness identify "
            "Kidney Yang deficiency. A pale tongue and deep, weak pulse confirm interior "
            "deficiency-Cold. Si Shen Wan fits because it warms Spleen-Kidney Yang and binds "
            "the intestines to stop chronic early-morning diarrhea."
        )
        cleaned["caseReasoningEs"] = (
            "Es el patrón clásico de diarrea al amanecer o 'canto del gallo' por deficiencia "
            "combinada de Yang de Bazo y Riñón. La diarrea predecible alrededor de las 5 a. m. "
            "es la clave temporal decisiva. El poco apetito y la indigestión muestran "
            "transformación débil de Bazo, mientras dolor lumbar, manos y pies fríos, fatiga y "
            "debilidad identifican deficiencia de Yang de Riñón. La lengua pálida y el pulso "
            "profundo y débil confirman Frío interno por deficiencia. Si Shen Wan corresponde "
            "porque calienta Yang de Bazo-Riñón y asegura intestinos para detener la diarrea "
            "crónica de madrugada."
        )
    elif (
        profilename == "gan mai da zao tang"
        and "disorientation and depression" in normalizedprompt
        and "cried easily" in normalizedprompt
    ):
        cleaned["caseReasoning"] = (
            "This presentation matches Zang Zao, or restless-organ disorder. Emotional "
            "lability with despondency, unexplained crying, inappropriate laughter, "
            "disorientation and restless sleep shows the Shen is not being properly nourished "
            "and contained. The pale tongue and thready pulse support an underlying deficiency, "
            "while the wiry quality reflects emotional constraint. Gan Mai Da Zao Tang fits "
            "because it nourishes the Heart, calms the Shen and harmonizes the Middle without "
            "treating Phlegm-Heat, skin Wind or pronounced Yin-Heat."
        )
        cleaned["caseReasoningEs"] = (
            "La presentación corresponde a Zang Zao o trastorno del órgano inquieto. La "
            "labilidad emocional con abatimiento, llanto inexplicable, risa inapropiada, "
            "desorientación y sueño inquieto muestra que Shen no está nutrido ni contenido "
            "adecuadamente. La lengua pálida y el pulso filiforme apoyan deficiencia subyacente, "
            "mientras la cualidad de cuerda refleja constricción emocional. Gan Mai Da Zao Tang "
            "corresponde porque nutre Corazón, calma Shen y armoniza Centro sin tratar "
            "Flema-Calor, Viento cutáneo ni Calor pronunciado por deficiencia de Yin."
        )
    elif (
        profilename == "bei mu gua lou san"
        and "weak bronchi" in normalizedprompt
        and "dry, swollen and painful throat" in normalizedprompt
    ):
        cleaned["caseReasoning"] = (
            "This is Lung Dryness with Phlegm congealed by depleted fluids. Dry cough, "
            "difficult-to-expectorate Phlegm and a dry, swollen painful throat show that the "
            "Lung lacks moisture and the remaining fluids have thickened into Phlegm. A red, "
            "dry tongue with a thready, rapid but forceful pulse confirms Dryness with Heat "
            "rather than Damp-Phlegm. Bei Mu Gua Lou San fits because it moistens the Lung, "
            "clears Heat, transforms dry Phlegm and restores the downward movement of Lung Qi."
        )
        cleaned["caseReasoningEs"] = (
            "Se trata de Sequedad de Pulmón con Flema condensada por agotamiento de líquidos. "
            "La tos seca, la Flema difícil de expulsar y la garganta seca, inflamada y dolorosa "
            "muestran falta de humedad en Pulmón y espesamiento de los líquidos restantes en "
            "Flema. La lengua roja y seca con pulso filiforme, rápido pero fuerte confirma "
            "Sequedad con Calor, no Flema-Humedad. Bei Mu Gua Lou San corresponde porque "
            "humedece Pulmón, aclara Calor, transforma Flema seca y restaura el descenso del "
            "Qi de Pulmón."
        )

    if (
        "caseReasoning" not in cleaned
        and cleaned.get("pattern")
        and cleaned.get("clue")
        and cleaned.get("action")
    ):
        cleaned["caseReasoning"] = (
            f"The case is best identified as {cleaned['pattern']} "
            f"The decisive pattern markers are {cleaned['clue']} "
            f"The therapeutic match is {profile.get('name')}: {cleaned['action']}"
        )
    if (
        "caseReasoningEs" not in cleaned
        and cleaned.get("patternEs")
        and cleaned.get("clueEs")
        and cleaned.get("actionEs")
    ):
        cleaned["caseReasoningEs"] = (
            f"El caso se identifica mejor como {cleaned['patternEs']} "
            f"Los marcadores decisivos son {cleaned['clueEs']} "
            f"La correspondencia terapéutica es {profile.get('name')}: {cleaned['actionEs']}"
        )

    alternatives = []
    for option in options or []:
        if option == correctoption:
            continue
        alternative = match_profile(option)
        if alternative is None:
            continue
        alternativefields = {
            "formula": alternative.get("name"),
            "pattern": alternative.get("pattern"),
            "patternEs": alternative.get("patternEs"),
            "clue": alternative.get("clue"),
            "clueEs": alternative.get("clueEs"),
        }
        alternatives.append({
            key: source_text(value)
            for key, value in alternativefields.items()
            if source_text(value)
        })
    if alternatives:
        cleaned["alternatives"] = alternatives
    return cleaned or None


def is_clinical_case(record: dict[str, Any]) -> bool:
    course = normalise_name(record.get("coursename"))
    section = normalise_name(record.get("sectionname"))
    exam = normalise_name(record.get("scormname"))
    exact = course == TARGET_COURSE and section == TARGET_TOPIC
    fallback = "clinical cases" in section and (
        "handbook" in section or "clinical" in exam
    )
    return exact or fallback


def feedback_text(question: dict[str, Any]) -> str:
    values: list[str] = []
    for item in question.get("Feedback") or []:
        value = source_text(answer_text(item))
        if value and value not in values:
            values.append(value)
    if not values:
        notes = source_text(question.get("QuestionNotes"))
        if notes:
            values.append(notes)
    return "\n\n".join(values)


def correct_option_index(question: dict[str, Any], options: list[str]) -> int | None:
    sourceoptions = question.get("Answers") or []
    for index, item in enumerate(sourceoptions):
        if isinstance(item, dict) and bool(item.get("Correct")):
            return index

    rightanswer = question.get("RightAnswer")
    if isinstance(rightanswer, int) and 0 <= rightanswer < len(options):
        return rightanswer
    righttext = source_text(answer_text(rightanswer)).casefold()
    if not righttext:
        return None
    if len(righttext) == 1 and "a" <= righttext <= "z":
        labelindex = ord(righttext) - ord("a")
        if labelindex < len(options):
            return labelindex
    for index, option in enumerate(options):
        if option.casefold() == righttext:
            return index
    for index, item in enumerate(sourceoptions):
        if isinstance(item, dict) and normalise_name(item.get("Id")) == righttext:
            return index
    if righttext.isdigit():
        number = int(righttext)
        if 1 <= number <= len(options):
            return number - 1
        if number == 0:
            return 0
    return None


def extract_question(
    question: dict[str, Any],
    examkey: str,
    questionnumber: int,
    formulaprofiles: dict[str, dict[str, Any]] | None = None,
) -> dict[str, Any] | None:
    prompt = source_text(question.get("QuestionText"))
    sourceoptions = question.get("Answers") or []
    if not prompt or not isinstance(sourceoptions, list):
        return None
    options = [source_text(answer_text(item)) for item in sourceoptions]
    if len(options) < 2 or any(not option for option in options):
        return None
    feedback = feedback_text(question)
    # These Handbook packages store later paragraphs of the clinical vignette
    # in Feedback. They are part of the stem and must be shown before options.
    if feedback:
        compactfeedback = re.sub(r"\s+", " ", feedback).lstrip()
        prompt = f"{prompt.rstrip()} {compactfeedback}"
        feedback = ""
    if (
        SUSPICIOUS_EMBEDDED_DIGIT.search(prompt)
        or SUSPICIOUS_EMBEDDED_DIGIT.search(feedback)
        or any(
            SUSPICIOUS_EMBEDDED_DIGIT.search(option) for option in options
        )
    ):
        return None
    correctindex = correct_option_index(question, options)
    if correctindex is None or correctindex >= len(options):
        return None
    normalizedcorrect = normalise_name(options[correctindex])
    normalizedprompt = normalise_name(prompt)
    if (
        normalizedcorrect.startswith("huo xiang zheng qi san ")
        and "child-care facility" in normalizedprompt
        and "diarrhea and vomiting" in normalizedprompt
    ):
        prompt = (
            "CASE: Tiffany, a 40-year-old woman, presents with recent diarrhea and vomiting "
            "after working in a child-care facility. She also feels feverish and reports "
            "epigastric bloating, nausea, flatulence, loss of appetite and headache."
        )
    elif (
        normalizedcorrect.startswith("bu zhong yi qi tang ")
        and "over two months" in normalizedprompt
        and "brought on through exertion" in normalizedprompt
    ):
        prompt = (
            "CASE: Mary, a 35-year-old woman, has had a low-grade fever of 99.7°F for more "
            "than two months. It occurs mainly in the morning and is also triggered by "
            "exertion. Other symptoms include spontaneous sweating, shortness of breath and "
            "chronic loose stool. She is thirsty but prefers warm drinks. Her tongue is "
            "somewhat pale and her pulse is weak."
        )
    elif (
        normalizedcorrect.startswith("jin gui shen qi wan ")
        and "benign prostate hypertrophy" in normalizedprompt
    ):
        prompt = re.sub(
            r"\bSteve also complains\b",
            "He also reports",
            prompt,
            flags=re.IGNORECASE,
        )
    elif (
        normalizedcorrect.startswith("tao he cheng qi tang ")
        and "speech became unintelligible" in normalizedprompt
    ):
        prompt = (
            "CASE: Carl, a 30-year-old man, was sent to the emergency room yesterday for "
            "severe lower-abdominal pain and a rising fever. At the same time, his speech "
            "became unintelligible, and he became markedly irritable, restless and delirious. "
            "He has been constipated for the past week. His temperature is 102°F, and his "
            "abdomen is very tight and tender. His tongue is red with a yellow coat, and his "
            "pulse is choppy and wiry."
        )
    elif (
        normalizedcorrect.startswith("bei mu gua lou san ")
        and "weak bronchi" in normalizedprompt
    ):
        prompt = re.sub(
            r"^CASE:\s*Jenny,\s*42-year-old male;\s*She",
            "CASE: Jenny, a 42-year-old woman. She",
            prompt,
            flags=re.IGNORECASE,
        )
    labels = [chr(65 + index) if index < 26 else str(index + 1) for index in range(len(options))]
    fingerprint = hashlib.sha256(
        (prompt + "\n" + "\n".join(options)).encode("utf-8")
    ).hexdigest()[:16]
    extracted = {
        "id": f"{examkey}-q{questionnumber}-{fingerprint}",
        "number": questionnumber,
        "type": str(question.get("__type") or "MultipleChoiceQuestion"),
        "prompt": prompt,
        "options": [
            {"label": label, "text": text}
            for label, text in zip(labels, options)
        ],
        "correctLabel": labels[correctindex],
        "feedback": feedback,
    }
    diagnosis = diagnostic_feedback(
        options[correctindex],
        formulaprofiles or {},
        prompt,
        options,
    )
    if diagnosis is not None:
        extracted["diagnosis"] = diagnosis
    return extracted


def exam_number(value: str) -> int:
    match = re.search(r"\bexam\s*(\d+)\b", value, re.IGNORECASE)
    return int(match.group(1)) if match else 9999


def exam_key(value: str, contenthash: str) -> str:
    number = exam_number(value)
    if number != 9999:
        return f"exam-{number}"
    slug = re.sub(r"[^a-z0-9]+", "-", value.casefold()).strip("-") or "exam"
    return f"{slug[:48]}-{contenthash[:8]}"


def load_quiz(packagepath: Path) -> list[Any]:
    with zipfile.ZipFile(packagepath, "r") as package:
        member = find_quizdata_member(package)
        if not member:
            raise ValueError("QUIZDATA.json not found")
        quizdata = json.loads(package.read(member).decode("utf-8-sig"))
    questions = quizdata.get("Questions")
    if not isinstance(questions, list):
        raise ValueError("QUIZDATA.json has no Questions array")
    return questions


def unique_key(basekey: str, used: set[str]) -> str:
    if basekey not in used:
        used.add(basekey)
        return basekey
    suffix = 2
    while f"{basekey}-set-{suffix}" in used:
        suffix += 1
    key = f"{basekey}-set-{suffix}"
    used.add(key)
    return key


def build_bank(
    inventory: dict[str, Any],
    courses: list[int],
    formulaprofiles: dict[str, dict[str, Any]],
) -> dict[str, Any]:
    dataroot = Path(str(inventory["dataroot"])).resolve()
    records = [record for record in inventory.get("records") or [] if is_clinical_case(record)]
    grouped = choose_placements(records, courses)
    exams: list[dict[str, Any]] = []
    errors: list[dict[str, str]] = []
    usedkeys: set[str] = set()
    signatures: set[str] = set()

    for contenthash, placements in grouped.items():
        placement = placements[0]
        title = str(placement.get("scormname") or "Clinical Cases Exam")
        try:
            sourcequestions = load_quiz(package_path(dataroot, contenthash))
            signature = hashlib.sha256(
                json.dumps(sourcequestions, ensure_ascii=False, sort_keys=True).encode("utf-8")
            ).hexdigest()
            if signature in signatures:
                continue
            signatures.add(signature)
            key = unique_key(exam_key(title, contenthash), usedkeys)
            questions = []
            skipped = 0
            for number, sourcequestion in enumerate(sourcequestions, start=1):
                if not isinstance(sourcequestion, dict):
                    skipped += 1
                    continue
                question = extract_question(sourcequestion, key, number, formulaprofiles)
                if question is None:
                    skipped += 1
                    continue
                questions.append(question)
            if questions:
                exams.append({
                    "key": key,
                    "title": title,
                    "number": exam_number(title),
                    "questionCount": len(questions),
                    "sourceQuestionCount": len(sourcequestions),
                    "skippedQuestionCount": skipped,
                    "questions": questions,
                })
        except (OSError, ValueError, json.JSONDecodeError, zipfile.BadZipFile) as error:
            errors.append({"exam": title, "error": str(error)})

    exams.sort(key=lambda item: (int(item["number"]), str(item["title"]).casefold(), str(item["key"])))
    for item in exams:
        if item["number"] == 9999:
            item["number"] = None
    return {
        "schemaVersion": 1,
        "source": "Handbook of Oriental Medicine — Clinical Cases",
        "sourceReadOnly": True,
        "examCount": len(exams),
        "questionCount": sum(int(exam["questionCount"]) for exam in exams),
        "diagnosticFeedbackCount": sum(
            1
            for exam in exams
            for question in exam["questions"]
            if question.get("diagnosis")
        ),
        "errors": errors,
        "exams": exams,
    }


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--courses", default=",".join(str(item) for item in DEFAULT_COURSES))
    parser.add_argument("--php-bin", default="php")
    parser.add_argument("--inventory", type=Path)
    parser.add_argument("--formula-data", type=Path, default=DEFAULT_FORMULA_DATA)
    parser.add_argument("--output", type=Path)
    return parser.parse_args()


def main() -> int:
    args = parse_args()
    courses = [int(value.strip()) for value in args.courses.split(",") if value.strip().isdigit()]
    if not courses:
        raise SystemExit("At least one valid course ID is required.")
    if args.inventory:
        inventory = json.loads(args.inventory.read_text(encoding="utf-8"))
    else:
        inventory = load_inventory(args.php_bin, courses)
    try:
        formulaprofiles = load_formula_profiles(args.formula_data.resolve())
    except (OSError, ValueError, json.JSONDecodeError) as error:
        raise SystemExit(f"Unable to load formula diagnostic profiles: {error}") from error
    bank = build_bank(inventory, courses, formulaprofiles)
    if not bank["exams"] or not bank["questionCount"]:
        raise SystemExit("No usable Handbook clinical-case questions were found.")
    if bank["diagnosticFeedbackCount"] != bank["questionCount"]:
        missing = bank["questionCount"] - bank["diagnosticFeedbackCount"]
        raise SystemExit(
            f"Diagnostic feedback is missing for {missing} clinical-case question(s); "
            "the existing bank was not replaced."
        )
    dataroot = Path(str(inventory["dataroot"])).resolve()
    output = args.output.resolve() if args.output else (
        dataroot / "board-domain-banks" / "herbology-clinical-cases.json"
    )
    output.parent.mkdir(parents=True, exist_ok=True)
    with tempfile.NamedTemporaryFile(
        "w", encoding="utf-8", dir=output.parent, delete=False, prefix=f".{output.name}-"
    ) as handle:
        json.dump(bank, handle, ensure_ascii=False, indent=2)
        handle.write("\n")
        temporary = Path(handle.name)
    temporary.replace(output)
    print(json.dumps({
        "output": str(output),
        "exams": bank["examCount"],
        "questions": bank["questionCount"],
        "diagnosticFeedback": bank["diagnosticFeedbackCount"],
        "errors": len(bank["errors"]),
    }, ensure_ascii=False))
    return 0


if __name__ == "__main__":
    try:
        raise SystemExit(main())
    except subprocess.CalledProcessError as error:
        print(error.stderr or str(error))
        raise SystemExit(error.returncode or 1)
