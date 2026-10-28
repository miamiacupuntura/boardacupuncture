#!/usr/bin/env python3
"""Generate the automatic Spanish layer for Formula Practice.

The English question bank remains authoritative and unchanged. Translations
are stored separately and joined by source question number in the browser.
"""

from __future__ import annotations

import argparse
from concurrent.futures import ProcessPoolExecutor
from functools import lru_cache
import hashlib
import json
import os
import re
from pathlib import Path
from typing import Any, Sequence


os.environ.setdefault("ARGOS_CHUNK_TYPE", "MINISBD")
os.environ.setdefault("ARGOS_INTRA_THREADS", "1")

from argostranslate import package, translate  # noqa: E402


SEPARATOR_RE = re.compile(r"\s*ZXSEG\d{4}ZX\s*")
PROTECTION_TOKEN_RE = re.compile(r"P\d{4}P")
ENGLISH_RESIDUE_RE = re.compile(
    r"\b(?:which|what|both|the|and|with|due|treats?|atrophy|marked|patterns?|"
    r"include|except|chief|deputy|assistant|envoy|tongue|pulse|heat|cold|blood|"
    r"dampness|phlegm|wiry|slippery|floating|tight|thin|white|yellow|red|"
    r"decoction|powder|pill)\b",
    re.IGNORECASE,
)
CLINICAL_TERMS_ES = {
    "which herbal formula corresponds to the zang fu pattern described below?": "¿Qué fórmula herbal corresponde al patrón Zang Fu descrito a continuación?",
    "which herbal formula corresponds to the four levels pattern described below?": "¿Qué fórmula herbal corresponde al patrón de los Cuatro Niveles descrito a continuación?",
    "which formula corresponds to the general description below?": "¿Qué fórmula corresponde a la descripción general que aparece a continuación?",
    "which formula corresponds to the modification described below?": "¿Qué fórmula corresponde a la modificación descrita a continuación?",
    "which formula modification corresponds to the symptoms described below?": "¿Qué modificación de la fórmula corresponde a los síntomas descritos a continuación?",
    "Clinical manifestations:": "Manifestaciones clínicas:",
    "Treatment principle:": "Principio de tratamiento:",
    "Pathology:": "Patología:",
    "Action:": "Acción:",
    "Actions:": "Acciones:",
    "Indication:": "Indicación:",
    "Indications:": "Indicaciones:",
    "clinical manifestations": "manifestaciones clínicas",
    "treatment principle": "principio de tratamiento",
    "chief herb": "hierba principal",
    "chief herbs": "hierbas principales",
    "upper burner": "Jiao Superior",
    "middle burner": "Jiao Medio",
    "middle warmer": "Jiao Medio",
    "lower burner": "Jiao Inferior",
    "upper jiao": "Jiao Superior",
    "middle jiao": "Jiao Medio",
    "lower jiao": "Jiao Inferior",
    "triple burner": "San Jiao",
    "wind-cold": "Viento-Frío",
    "wind-heat": "Viento-Calor",
    "wind-dampness": "Viento-Humedad",
    "wind-type stroke": "apoplejía de tipo Viento",
    "gushing upward of qi": "ascenso excesivo del Qi",
    "damp-heat": "Humedad-Calor",
    "phlegm-heat": "Flema-Calor",
    "phlegm-dampness": "Flema-Humedad",
    "blood stasis": "estasis de Sangre",
    "blood deficiency": "deficiencia de Sangre",
    "yin deficiency": "deficiencia de Yin",
    "yang deficiency": "deficiencia de Yang",
    "qi deficiency": "deficiencia de Qi",
    "qi stagnation": "estancamiento de Qi",
    "qi rebellion": "Qi rebelde",
    "rebellious qi": "Qi rebelde",
    "exterior pattern": "patrón exterior",
    "interior pattern": "patrón interior",
    "cold-damage disorder": "trastorno por lesión de Frío",
    "stomach heat": "Calor de Estómago",
    "qi and fluid injury": "lesión del Qi y los líquidos",
    "wiry pulse": "pulso en cuerda",
    "wiry, long, and forceful pulse": "pulso en cuerda, largo y fuerte",
    "wiry rapid pulse": "pulso en cuerda y rápido",
    "slippery pulse": "pulso resbaladizo",
    "floating pulse": "pulso flotante",
    "deep pulse": "pulso profundo",
    "submerged pulse": "pulso profundo",
    "rapid pulse": "pulso rápido",
    "slow pulse": "pulso lento",
    "weak pulse": "pulso débil",
    "thin pulse": "pulso fino",
    "thready pulse": "pulso filiforme",
    "tight pulse": "pulso tenso",
    "frail pulse": "pulso débil",
    "forceful pulse": "pulso fuerte",
    "forceless pulse": "pulso sin fuerza",
    "soggy pulse": "pulso blando",
    "floating and rapid pulse": "pulso flotante y rápido",
    "floating and tight pulse": "pulso flotante y tenso",
    "floating, tight pulse": "pulso flotante y tenso",
    "thin, white coating": "saburra fina y blanca",
    "thin white coating": "saburra fina y blanca",
    "thin, white tongue coating": "saburra lingual fina y blanca",
    "dry tongue with thin white coating": "lengua seca con saburra fina y blanca",
    "red tongue with slippery coating": "lengua roja con saburra resbaladiza",
    "white tongue coating": "saburra lingual blanca",
    "yellow tongue coating": "saburra lingual amarilla",
    "greasy tongue coating": "saburra lingual grasa",
    "thin tongue coating": "saburra lingual fina",
    "urinary incontinence": "incontinencia urinaria",
    "frequent urination": "micción frecuente",
    "frequent, pale, abundant urination": "micción frecuente con orina clara y abundante",
    "spermatorrhoea": "espermatorrea",
    "spermatorrhea": "espermatorrea",
    "tongue coating": "saburra lingual",
    "white coating": "saburra blanca",
    "pale tongue": "lengua pálida",
    "rice water": "agua de arroz",
    "forgetfulness": "falta de memoria",
    "bedwetting": "enuresis",
    "dry stools": "heces secas",
    "loose stools": "heces blandas",
    "incontinence": "incontinencia",
    "hypochondriac pain": "dolor hipocondríaco",
    "lower back pain": "dolor lumbar",
    "lower backache": "dolor lumbar",
    "shortness of breath": "disnea",
    "night sweats": "sudoración nocturna",
    "spontaneous sweating": "sudoración espontánea",
    "aversion to cold": "aversión al frío",
    "aversion to wind": "aversión al viento",
    "sore throat": "dolor de garganta",
    "itchy throat": "picor de garganta",
    "runny nose": "rinorrea",
    "nasal congestion": "congestión nasal",
    "poor appetite": "falta de apetito",
    "no appetite": "falta de apetito",
    "vaginal discharge": "leucorrea",
    "urethral discharge": "secreción uretral",
    "slight breathlessness": "disnea leve",
    "clear-watery discharge": "secreción clara y acuosa",
    "dull pain": "dolor sordo",
    "discomfort": "molestia",
    "epigastrium": "epigastrio",
    "upper back": "parte superior de la espalda",
    "five-palm heat": "calor en las cinco palmas",
    "dream-disturbed sleep": "sueño intranquilo con abundantes sueños",
    "nose mouth and throat": "nariz, boca y garganta",
    "blocked or runny nose": "congestión nasal o rinorrea",
    "flushed face": "rostro enrojecido",
    "release exterior": "liberar el Exterior",
    "clear heat": "eliminar el Calor",
    "nourish fluids": "nutrir los líquidos",
    "honey-fried licorice decoction": "decocción de regaliz tostado con miel",
    "warm the menses decoction": "decocción para calentar la menstruación",
    "honeysuckle and forsythia powder": "polvo de madreselva y forsitia",
    "cinnamon twig decoction": "decocción de ramita de canela",
    "three nut decoction": "decocción de las tres semillas",
    "red-peeled, tip redder, midline crack reaching to tip": "roja y sin saburra, con la punta más roja y una grieta central que llega hasta la punta",
    "floating-empty and rapid or deep weak on both rear positions and overflowing on both front positions": "flotante, vacío y rápido, o profundo y débil en ambas posiciones posteriores y desbordante en ambas posiciones anteriores",
    "Lungs": "Pulmones",
    "Lung": "Pulmón",
    "Spleen": "Bazo",
    "Liver": "Hígado",
    "Kidneys": "Riñones",
    "Kidney": "Riñón",
    "Heart": "Corazón",
    "Stomach": "Estómago",
    "Gallbladder": "Vesícula Biliar",
    "Small Intestine": "Intestino Delgado",
    "Large Intestine": "Intestino Grueso",
    "Pericardium": "Pericardio",
    "Bladder": "Vejiga",
    "Chief": "Principal",
    "Deputy": "Adjunta",
    "Assistant": "Asistente",
    "Envoy": "Guía",
    "Tongue:": "Lengua:",
    "Pulse:": "Pulso:",
    "Tongue": "lengua",
    "Pulse": "pulso",
    "Pathology": "patología",
    "Action": "acción",
    "Actions": "acciones",
    "Indication": "indicación",
    "Indications": "indicaciones",
}


SPANISH_REPLACEMENTS = (
    (r"\bIdentificar la fórmula\b", "Identifique la fórmula"),
    (r"\bIdentificar la fórmula herbal\b", "Identifique la fórmula herbal"),
    (r"¿Cuál de las siguientes fórmulas es adecuado\b", "¿Cuál de las siguientes fórmulas es adecuada"),
    (r"¿Cuál de las siguientes fórmulas es apropiado\b", "¿Cuál de las siguientes fórmulas es apropiada"),
    (r"¿Cuál de las siguientes fórmulas herbales es adecuado\b", "¿Cuál de las siguientes fórmulas herbales es adecuada"),
    (r"¿Cuál de las siguientes fórmulas se pueden utilizar\b", "¿Cuál de las siguientes fórmulas puede utilizarse"),
    (r"¿Qué fórmula mejor trata\b", "¿Qué fórmula trata mejor"),
    (r"¿Qué diferencia hay\?", "¿Cuál es la diferencia?"),
    (r"¿Cuál es su diferencia\?", "¿En qué se diferencian?"),
    (r"\bdescocción\b", "decocción"),
    (r"\brecubrimiento de la lengua\b", "saburra lingual"),
    (r"\brecubrimiento lingual\b", "saburra lingual"),
    (r"\brecubrimiento blanco\b", "saburra blanca"),
    (r"\brecubrimiento amarillo\b", "saburra amarilla"),
    (r"\buna saburra\b", "una saburra"),
    (r"\bun saburra\b", "una saburra"),
    (r"\bel Qi\b", "el Qi"),
    (r"\bdel Qi\b", "del Qi"),
    (r"\bde Qi\b", "de Qi"),
    (r"\bSpleen\b", "Bazo"),
    (r"\bKidney\b", "Riñón"),
    (r"\bLiver\b", "Hígado"),
    (r"\bLung\b", "Pulmón"),
    (r"\bHeart\b", "Corazón"),
    (r"\bTongue:\s*", "Lengua: "),
    (r"\bPulse:\s*", "Pulso: "),
    (r"\bWind-Phlegm\b", "Viento-Flema"),
    (r"\bHeat-Phlegm\b", "Calor-Flema"),
    (r"\bDamp-Phlegm\b", "Humedad-Flema"),
    (r"\bPhlegm-Damp\b", "Flema-Humedad"),
    (r"\bDamp-Cold\b", "Humedad-Frío"),
    (r"\bWind-Damp-Cold\b", "Viento-Humedad-Frío"),
    (r"\bQi Level Heat\b", "Calor en el nivel Qi"),
    (r"\bDeficient Cold\b", "Frío por deficiencia"),
    (r"\bWeak\b", "débil"),
    (r"\bDeep\b", "profundo"),
    (r"\bFine\b", "fino"),
    (r"\bThin\b", "delgado"),
    (r"\bRapid\b", "rápido"),
    (r"\bSlow\b", "lento"),
    (r"\bEmpty\b", "vacío"),
    (r"\bFull\b", "lleno"),
    (r"\bChoppy\b", "áspero"),
    (r"\bRed\b", "rojo"),
    (r"\bPeeled\b", "sin saburra"),
    (r"\bFloating\b", "flotante"),
    (r"\bTight\b", "tenso"),
    (r"\bCold\b", "Frío"),
    (r"\bHeat\b", "Calor"),
    (r"\bBlood\b", "Sangre"),
    (r"\bPhlegm\b", "Flema"),
    (r"\bDampness\b", "Humedad"),
    (r"\bWhite\b", "blanca"),
    (r"\bDisharmony between LV and ST\b", "Desarmonía entre Hígado y Estómago"),
    (r"\bVomiting and sweating\b", "Vómitos y sudoración"),
    (r"\bDisharmony of Ying and Wei Qi\b", "Desarmonía entre el Qi Ying y el Qi Wei"),
    (r"\bDisharmony of Ying and Wei\b", "Desarmonía entre Ying y Wei"),
    (r"\bDisharmony of Ying and wen Qi\b", "Desarmonía entre el Qi Ying y el Qi Wei"),
    (r"\bWarming for (?:cold|Frío) patterns, downward draining for (?:Heat|Calor) patterns\b", "Calentar los patrones de Frío y drenar hacia abajo los patrones de Calor"),
    (r"\bWarming for (?:cold|Frío) patterns, clearing for (?:Heat|Calor) patterns\b", "Calentar los patrones de Frío y eliminar el Calor en los patrones de Calor"),
    (r"\bWarming for Frío patterns, downward drenaje para patrones de calor\b", "Calentar los patrones de Frío y drenar hacia abajo los patrones de Calor"),
    (r"\bDescomfort\b", "Molestia"),
    (r"\bepigastrium\b", "epigastrio"),
    (r"\btrasero superior\b", "parte superior de la espalda"),
    (r"\bascenso excesivo del Qi excesivo\b", "ascenso excesivo del Qi"),
    (r"\btestículos desatendidos\b", "testículos distendidos"),
    (r"\bpiel seca, nariz, boca y garganta\b", "piel seca y sequedad de nariz, boca y garganta"),
    (r"\bextremidades frías débiles\b", "extremidades frías y débiles"),
    (r"\bcuello rígido y rígido y parte superior de la espalda\b", "cuello y parte superior de la espalda rígidos"),
    (r"\bPulso: Profundo\b", "Pulso: profundo"),
)


def parse_args(argv: Sequence[str] | None = None) -> argparse.Namespace:
    root = Path(__file__).resolve().parents[1]
    parser = argparse.ArgumentParser(
        description="Create the automatic Spanish Formula Practice translation layer."
    )
    parser.add_argument(
        "--source",
        type=Path,
        default=root / "study-guide/comparisons/formula-practice-data.json",
    )
    parser.add_argument(
        "--output",
        type=Path,
        default=root / "study-guide/comparisons/formula-practice-translations-es.json",
    )
    parser.add_argument(
        "--cache",
        type=Path,
        default=root / "output/formula-practice-translation-cache-es.json",
    )
    parser.add_argument(
        "--overrides",
        type=Path,
        default=root / "tools/formula_practice_copy_overrides.json",
    )
    parser.add_argument("--batch-chars", type=int, default=5000)
    parser.add_argument("--workers", type=int, default=min(3, os.cpu_count() or 1))
    parser.add_argument(
        "--refresh-base",
        action="store_true",
        help="Retranslate every unmasked source segment before terminology refinement.",
    )
    return parser.parse_args(argv)


def ensure_model() -> None:
    installed = package.get_installed_packages()
    if not any(item.from_code == "en" and item.to_code == "es" for item in installed):
        raise RuntimeError("Argos Translate English-to-Spanish model is not installed")


def load_cache(path: Path) -> dict[str, str]:
    if not path.is_file():
        return {}
    value = json.loads(path.read_text(encoding="utf-8"))
    return value if isinstance(value, dict) else {}


def save_cache(path: Path, cache: dict[str, str]) -> None:
    path.parent.mkdir(parents=True, exist_ok=True)
    path.write_text(json.dumps(cache, ensure_ascii=False), encoding="utf-8")


def make_batches(values: list[str], maximum_chars: int) -> list[list[str]]:
    batches: list[list[str]] = []
    current: list[str] = []
    current_length = 0
    for value in values:
        added = len(value) + 20
        if current and current_length + added > maximum_chars:
            batches.append(current)
            current = []
            current_length = 0
        current.append(value)
        current_length += added
    if current:
        batches.append(current)
    return batches


def translate_one(value: str) -> str:
    translated = translate.translate(value, "en", "es").strip()
    required_tokens = set(PROTECTION_TOKEN_RE.findall(value))
    if required_tokens.issubset(set(PROTECTION_TOKEN_RE.findall(translated))):
        return translated
    return value


def translate_batch(values: list[str]) -> list[str]:
    if len(values) == 1:
        return [translate_one(values[0])]
    joined = values[0] + "".join(
        f"\nZXSEG{index:04d}ZX\n{value}" for index, value in enumerate(values[1:], 1)
    )
    translated = translate.translate(joined, "en", "es")
    pieces = SEPARATOR_RE.split(translated)
    if len(pieces) == len(values):
        results = [piece.strip() for piece in pieces]
        return [
            result
            if set(PROTECTION_TOKEN_RE.findall(source)).issubset(
                set(PROTECTION_TOKEN_RE.findall(result))
            )
            else translate_one(source)
            for source, result in zip(values, results, strict=True)
        ]
    return [translate_one(value) for value in values]


def collect_texts(data: dict[str, Any]) -> list[str]:
    values: list[str] = []
    for question in data["questions"]:
        values.append(question["prompt"])
        values.extend(question.get("body", []))
        values.extend(option["text"] for option in question["options"])
        values.extend(question.get("feedback", []))
    return list(dict.fromkeys(value for value in values if value.strip()))


def collect_formula_names(data: dict[str, Any], texts: list[str]) -> list[str]:
    names = [formula["name"] for formula in data["formulas"]]
    title_pattern = re.compile(
        r"\b(?:[A-Z][A-Za-z'-]*\s+){1,7}(?:Tang|San|Wan|Dan|Yin|Gao|Jian|Fang|Zi|Zhu)\b"
    )
    parenthetical_pattern = re.compile(
        r"\(([a-z][a-z' -]{2,80}(?:tang|san|wan|dan|yin|gao|jian|fang|zi|zhu))\)"
    )
    for text_value in texts:
        for match in title_pattern.finditer(text_value):
            candidate = match.group(0)
            candidate = re.sub(r"^(?:Both|Which|What|The|This|Identify)\s+", "", candidate)
            names.append(candidate)
        names.extend(
            " ".join(word.capitalize() for word in match.group(1).split())
            for match in parenthetical_pattern.finditer(text_value)
        )
    return list(dict.fromkeys(names))


@lru_cache(maxsize=4)
def formula_gloss_patterns(
    formula_names: tuple[str, ...]
) -> tuple[re.Pattern[str], re.Pattern[str]]:
    alternatives = "|".join(
        re.escape(name) for name in sorted(formula_names, key=len, reverse=True)
    )
    forward = re.compile(
        rf"({alternatives})\s*\((?:[^()]|\([^()]*\)){{1,180}}\)"
    )
    reverse = re.compile(
        rf"(^|\n)([^()\n]{{1,140}})\(({alternatives})\)", re.MULTILINE
    )
    return forward, reverse


def remove_english_formula_glosses(
    value: str, formula_names: Sequence[str], reverse_glosses: bool = False
) -> str:
    if not formula_names:
        return value
    forward, reverse = formula_gloss_patterns(tuple(formula_names))
    cleaned = forward.sub(r"\1", value)
    return reverse.sub(r"\1\3", cleaned) if reverse_glosses else cleaned


def polish_spanish(
    value: str, source: str = "", formula_names: Sequence[str] = (),
    reverse_formula_glosses: bool = False,
) -> str:
    """Apply safe, repeatable grammar and terminology fixes after translation."""
    polished = (
        re.sub(r"[ \t]+", " ", value)
        .replace("&quot;", "y")
        .replace("&amp;", "y")
        .strip()
    )
    for pattern, replacement in SPANISH_REPLACEMENTS:
        polished = re.sub(pattern, replacement, polished, flags=re.IGNORECASE)
    polished = re.sub(r"^(Cuál|Cuáles|Qué|Cómo|Por qué|En qué)\b", r"¿\1", polished)
    polished = re.sub(r"([.!])\s+(Cuál|Cuáles|Qué|Cómo|Por qué|En qué)\b", r"\1 ¿\2", polished)
    polished = re.sub(r"\bLengua:\s*[Pp]ale,?\s*(?:y\s+)?mojado\b", "Lengua: pálida y húmeda", polished)
    polished = re.sub(r"\bLengua:\s*[Pp]ale\b", "Lengua: pálida", polished)
    polished = re.sub(r"\bPulso:\s*[Ff]loating\b", "Pulso: flotante", polished)
    polished = re.sub(r"\bPulso:\s*[Dd]eep\b", "Pulso: profundo", polished)
    polished = re.sub(r"\bwir(?:y|e)\b", "en cuerda", polished, flags=re.IGNORECASE)
    polished = re.sub(r"\bslippery\b", "resbaladizo", polished, flags=re.IGNORECASE)
    polished = re.sub(r"\bthready\b", "filiforme", polished, flags=re.IGNORECASE)
    polished = re.sub(r"\bforceless\b", "sin fuerza", polished, flags=re.IGNORECASE)
    polished = re.sub(r"\bsoggy\b", "blando", polished, flags=re.IGNORECASE)
    polished = re.sub(r"\bthin-white\b", "fina y blanca", polished, flags=re.IGNORECASE)
    polished = re.sub(r"\bfloating-rapid\b", "flotante y rápido", polished, flags=re.IGNORECASE)
    polished = re.sub(r"\bfloating-tight\b", "flotante y tenso", polished, flags=re.IGNORECASE)
    polished = re.sub(r"¿Cuál es (?:el|la) Indicación\b", "¿Cuál es la indicación", polished, flags=re.IGNORECASE)
    polished = re.sub(r"¿Qué son tres hierbas principales en\b", "¿Cuáles son las tres hierbas principales de", polished, flags=re.IGNORECASE)
    polished = re.sub(r"¿Qué son las hierbas principales en\b", "¿Cuáles son las hierbas principales de", polished, flags=re.IGNORECASE)
    polished = re.sub(r"¿Qué son los ingredientes (?:en|de)\b", "¿Cuáles son los ingredientes de", polished, flags=re.IGNORECASE)
    polished = re.sub(r"¿Qué son las manifestaciones de\b", "¿Cuáles son las manifestaciones de", polished, flags=re.IGNORECASE)
    polished = re.sub(r"\b(Bazo|Hígado|Riñón|Pulmón|Corazón|Estómago) (Deficiencia de (?:Yin|Yang|Qi|Sangre))\b", r"\2 de \1", polished, flags=re.IGNORECASE)
    polished = re.sub(r"\bDeficiencia de\b", "deficiencia de", polished)
    polished = re.sub(r"\bEstancamiento de Qi\b", "estancamiento de Qi", polished)
    polished = re.sub(r"\bdebido a Bazo Yang incapaz de controlar (?:La|la) [Ss]angre\b", "debido a que el Yang de Bazo no puede controlar la Sangre", polished)
    polished = re.sub(r"\b(?:El|el) Lengua\b", "la lengua", polished, flags=re.IGNORECASE)
    polished = re.sub(r"\bun lengua\b", "una lengua", polished, flags=re.IGNORECASE)
    polished = re.sub(r"\blengua es muy pálido\b", "lengua es muy pálida", polished, flags=re.IGNORECASE)
    polished = re.sub(r"\bsecreción uretral blanco\b", "secreción uretral blanca", polished, flags=re.IGNORECASE)
    polished = re.sub(r"\bsaburra blanca delgado\b", "saburra fina y blanca", polished, flags=re.IGNORECASE)
    polished = re.sub(r"\bdelgado, saburra blanca\b", "saburra fina y blanca", polished, flags=re.IGNORECASE)
    polished = re.sub(r"\bflotante y pulso rápido\b", "pulso flotante y rápido", polished, flags=re.IGNORECASE)
    polished = re.sub(r"\bflotando y apretado\b", "flotante y tenso", polished, flags=re.IGNORECASE)
    polished = re.sub(r"\ben cuerda pulso rápido\b", "pulso en cuerda y rápido", polished, flags=re.IGNORECASE)
    polished = re.sub(r"\bun en cuerda, long y pulso fuerte\b", "un pulso en cuerda, largo y fuerte", polished, flags=re.IGNORECASE)
    polished = re.sub(r"\bun sumergido y lento o un pulso en cuerda\b", "un pulso profundo y lento o un pulso en cuerda", polished, flags=re.IGNORECASE)
    polished = re.sub(r"\bJiao Medio Calor y acumulación fría\b", "acumulación de Calor y Frío en el Jiao Medio", polished, flags=re.IGNORECASE)
    polished = re.sub(r"\benciente la decocción de los menses\b", "decocción para calentar la menstruación", polished, flags=re.IGNORECASE)
    polished = re.sub(r"\benciende la decocción de los menses\b", "decocción para calentar la menstruación", polished, flags=re.IGNORECASE)
    if re.fullmatch(r"What is the indication (?:of|for) .+\?", source, flags=re.IGNORECASE):
        polished = re.sub(r"^¿Cuál es (?:el|la) indicación", "¿Cuál es la indicación", polished, flags=re.IGNORECASE)
    if re.fullmatch(r"What are (?:the )?ingredients (?:in|of) .+\?", source, flags=re.IGNORECASE):
        polished = re.sub(r"^¿(?:Qué son|Cuáles son) (?:los )?ingredientes (?:en|de)", "¿Cuáles son los ingredientes de", polished, flags=re.IGNORECASE)
    polished = re.sub(r"\s+([,.;:!?])", r"\1", polished)
    return remove_english_formula_glosses(
        polished, formula_names, reverse_formula_glosses
    )


def translated_question(
    question: dict[str, Any], cache: dict[str, str], overrides: dict[str, Any],
    formula_names: Sequence[str]
) -> dict[str, Any]:
    translated = {
        "sourceNumber": question["sourceNumber"],
        "prompt": polish_spanish(cache[question["prompt"]], question["prompt"], formula_names),
        "body": [polish_spanish(cache[value], value, formula_names) for value in question.get("body", [])],
        "options": [
            {
                "label": option["label"],
                "text": polish_spanish(
                    cache[option["text"]], option["text"], formula_names, True
                ),
            }
            for option in question["options"]
        ],
        "feedback": [polish_spanish(cache[value], value, formula_names) for value in question.get("feedback", [])],
    }
    override = overrides.get(str(question["sourceNumber"]), {})
    if override.get("promptEs"):
        translated["prompt"] = override["promptEs"]
    option_overrides = override.get("optionsEs", {})
    for option in translated["options"]:
        if option["label"] in option_overrides:
            option["text"] = option_overrides[option["label"]]
    return translated


def translate_values(
    values: list[str], cache: dict[str, str], args: argparse.Namespace, label: str
) -> None:
    missing = [
        value
        for value in values
        if value not in cache
        or not set(PROTECTION_TOKEN_RE.findall(value)).issubset(
            set(PROTECTION_TOKEN_RE.findall(cache[value]))
        )
    ]
    batches = make_batches(missing, args.batch_chars)
    print(
        f"{label}: {len(values)} unique segments, {len(missing)} missing, "
        f"{len(batches)} batches",
        flush=True,
    )
    completed = 0
    if args.workers > 1 and batches:
        executor = ProcessPoolExecutor(max_workers=args.workers)
        translated_batches = executor.map(translate_batch, batches)
    else:
        executor = None
        translated_batches = map(translate_batch, batches)
    for batch_number, (batch, results) in enumerate(
        zip(batches, translated_batches, strict=True), 1
    ):
        for source, result in zip(batch, results, strict=True):
            cache[source] = result or source
        completed += len(batch)
        save_cache(args.cache, cache)
        print(
            f"Translated {completed}/{len(missing)} new segments "
            f"(batch {batch_number}/{len(batches)})",
            flush=True,
        )
    if executor is not None:
        executor.shutdown()


def refine_unchanged_parentheticals(
    texts: list[str], cache: dict[str, str], args: argparse.Namespace
) -> None:
    candidates = [
        source
        for source in texts
        if cache.get(source) == source and "(" in source and ")" in source
    ]
    parentheticals = list(
        dict.fromkeys(
            part.strip()
            for source in candidates
            for part in re.findall(r"\(([^()]*)\)", source)
            if part.strip()
        )
    )
    translate_values(parentheticals, cache, args, "Parenthetical refinement")
    for source in candidates:
        cache[source] = re.sub(
            r"\(([^()]*)\)",
            lambda match: f"({cache.get(match.group(1).strip(), match.group(1))})",
            source,
        )
    save_cache(args.cache, cache)


def refine_formula_names(
    texts: list[str],
    formula_names: list[str],
    cache: dict[str, str],
    base_translations: dict[str, str],
    args: argparse.Namespace,
) -> None:
    """Retranslate segments while shielding Pinyin names and clinical terms."""
    ordered_names = sorted(formula_names, key=len, reverse=True)
    alternatives = "|".join(re.escape(name) for name in ordered_names)
    pattern = re.compile(
        r"(?<![A-Za-z])(" + alternatives + r")(?:['’]s)?(?![A-Za-z])",
        re.IGNORECASE,
    )
    canonical: dict[str, str] = {}
    for name in formula_names:
        canonical.setdefault(name.casefold(), name)
    clinical_terms = {term.casefold(): replacement for term, replacement in CLINICAL_TERMS_ES.items()}
    ordered_terms = sorted(CLINICAL_TERMS_ES, key=len, reverse=True)
    term_pattern = re.compile(
        r"(?<![A-Za-z])(" + "|".join(re.escape(term) for term in ordered_terms) + r")(?![A-Za-z])",
        re.IGNORECASE,
    )
    records: list[tuple[str, str, dict[str, str]]] = []
    for source in texts:
        replacements: dict[str, str] = {}

        def mask(match: re.Match[str]) -> str:
            token = f"P{len(replacements):04d}P"
            replacements[token] = canonical[match.group(1).casefold()]
            return token

        def mask_term(match: re.Match[str]) -> str:
            token = f"P{len(replacements):04d}P"
            replacement = clinical_terms[match.group(1).casefold()]
            replacements[token] = replacement
            return token

        masked = term_pattern.sub(mask_term, pattern.sub(mask, source))
        if replacements:
            records.append((source, masked, replacements))

    masked_values = list(dict.fromkeys(masked for _, masked, _ in records))
    translate_values(masked_values, cache, args, "Terminology protection")
    for source, masked, replacements in records:
        result = cache[masked]
        for token, name in replacements.items():
            result = re.sub(re.escape(token), name, result, flags=re.IGNORECASE)
        base = base_translations[source]
        protected_residue = len(ENGLISH_RESIDUE_RE.findall(result))
        base_residue = len(ENGLISH_RESIDUE_RE.findall(base))
        expected_names = [
            canonical[match.group(1).casefold()]
            for match in pattern.finditer(source)
        ]
        base_preserves_names = all(name in base for name in expected_names)
        cache[source] = (
            base
            if base_preserves_names and protected_residue > base_residue
            else result
        )
    save_cache(args.cache, cache)


def main(argv: Sequence[str] | None = None) -> int:
    args = parse_args(argv)
    ensure_model()
    source_bytes = args.source.read_bytes()
    data = json.loads(source_bytes)
    overrides = json.loads(args.overrides.read_text(encoding="utf-8"))
    cache = load_cache(args.cache)
    texts = collect_texts(data)
    if args.refresh_base:
        for value in texts:
            cache.pop(value, None)
    translate_values(texts, cache, args, "Spanish translation")
    base_translations = {value: cache[value] for value in texts}
    formula_names = collect_formula_names(data, texts)
    refine_formula_names(
        texts, formula_names, cache, base_translations, args
    )
    refine_unchanged_parentheticals(texts, cache, args)

    output = {
        "version": 1,
        "sourceDataSha256": hashlib.sha256(source_bytes).hexdigest(),
        "engine": "Argos Translate en-es 1.0",
        "automatic": True,
        "disclaimerEs": (
            "Traducción automática al español. Puede contener errores de idioma o "
            "terminología. Ante cualquier diferencia, consulte el texto original en inglés."
        ),
        "questions": [
            translated_question(
                question,
                cache,
                overrides,
                formula_names,
            )
            for question in data["questions"]
        ],
    }
    args.output.write_text(
        json.dumps(output, ensure_ascii=False, separators=(",", ":")) + "\n",
        encoding="utf-8",
    )
    args.output.chmod(0o644)
    print(f"Wrote {len(output['questions'])} translations to {args.output}", flush=True)
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
