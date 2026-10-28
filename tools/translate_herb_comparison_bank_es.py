#!/usr/bin/env python3
"""Create the Spanish content layer for the herbal comparison question bank.

The extracted English data remains authoritative. Translations are joined by stable
chapter-question and figure identifiers, so they can be reviewed or overridden without
regenerating the source extraction.
"""

from __future__ import annotations

import argparse
import hashlib
import json
import re
from pathlib import Path
from types import SimpleNamespace

from translate_formula_practice_es import (
    ensure_model,
    load_cache,
    polish_spanish,
    save_cache,
    translate_values,
)


def parse_args() -> argparse.Namespace:
    root = Path(__file__).resolve().parents[1]
    parser = argparse.ArgumentParser()
    parser.add_argument("--source", type=Path, default=root / "study-guide/comparisons/herb-comparison-bank-data.json")
    parser.add_argument("--output", type=Path, default=root / "study-guide/comparisons/herb-comparison-bank-translations-es.json")
    parser.add_argument("--cache", type=Path, default=root / "output/herb-comparison-translation-cache-es.json")
    parser.add_argument("--overrides", type=Path, default=root / "tools/herb_comparison_translation_overrides_es.json")
    parser.add_argument("--batch-chars", type=int, default=5000)
    parser.add_argument("--workers", type=int, default=3)
    return parser.parse_args()


def answer_parts(value: str) -> list[str]:
    return [part.strip() for part in value.split("\n\n") if part.strip()]


def comparison_polish(value: str, source: str) -> str:
    heading_translations = {
        "Pungency": "Sabor picante",
        "Sweetness": "Dulzura",
        "Sourness": "Acidez",
        "Bitterness": "Amargor",
        "Saltiness": "Sabor salado",
        "Blandness": "Insipidez",
        "Aromatic herbs": "Hierbas aromáticas",
        "Astringency": "Astringencia",
    }
    if source in heading_translations:
        return heading_translations[source]
    value = polish_spanish(value, source)
    replacements = (
        (r"\bWind-Heat\b", "Viento-Calor"),
        (r"\bWind-Calor\b", "Viento-Calor"),
        (r"\bWind-Cold\b", "Viento-Frío"),
        (r"\bWind-Frío\b", "Viento-Frío"),
        (r"\bWind-Dampness\b", "Viento-Humedad"),
        (r"\bWind-Humedad\b", "Viento-Humedad"),
        (r"\bLiver-Yang\b", "Yang de Hígado"),
        (r"\bHígado-\s*Yang\b", "Yang de Hígado"),
        (r"\bLiver-Qi\b", "Qi de Hígado"),
        (r"\bHígado-Qi\b", "Qi de Hígado"),
        (r"\bHeart-Qi\b", "Qi de Corazón"),
        (r"\bCorazón-Qi\b", "Qi de Corazón"),
        (r"\bLung-Qi\b", "Qi de Pulmón"),
        (r"\bPulmón-Qi\b", "Qi de Pulmón"),
        (r"\bKidney-Yang\b", "Yang de Riñón"),
        (r"\bKidney-Yin\b", "Yin de Riñón"),
        (r"\bKidney-Essence\b", "Esencia de Riñón"),
        (r"\bMiddle Jiao\b", "Jiao Medio"),
        (r"\bUpper Jiao\b", "Jiao Superior"),
        (r"\bLower Jiao\b", "Jiao Inferior"),
        (r"\bQi level\b", "nivel Qi"),
        (r"\bBlood level\b", "nivel de la Sangre"),
        (r"\bNutritive level\b", "nivel Nutritivo"),
        (r"\bDefensive level\b", "nivel Defensivo"),
        (r"\bPungent-bitter-cold herbs\b", "hierbas picantes, amargas y frías"),
        (r"\bPungent-bitter-warm herbs\b", "hierbas picantes, amargas y tibias"),
        (r"\bTraditional Chinese Medicine\b", "medicina china tradicional"),
        (r"\bpungentes\b", "picantes"),
        (r"\bpungente\b", "picante"),
        (r"a saber, caliente, fría y fría", "a saber, calientes, tibias, frías y frescas"),
        (r"corresponden a los Yang y Yin", "corresponden al Yang y al Yin"),
        (r"hierbas que inducen sudar", "hierbas que inducen la sudoración"),
        (r"características de luz y dispersión", "carácter ligero y dispersante"),
        (r"Comparación de las carácter ligero y dispersante de", "Comparación del carácter ligero y dispersante de"),
        (r"\bSon picantes y cálidos\b", "Son picantes y cálidas"),
    )
    for pattern, replacement in replacements:
        value = re.sub(pattern, replacement, value, flags=re.IGNORECASE)
    return value


def main() -> int:
    args = parse_args()
    ensure_model()
    source_bytes = args.source.read_bytes()
    data = json.loads(source_bytes)
    overrides = json.loads(args.overrides.read_text(encoding="utf-8")) if args.overrides.is_file() else {}
    cache = load_cache(args.cache)
    values: list[str] = []
    for question in data["questions"]:
        values.extend((question["question"], question["memoryCue"]))
        values.extend(answer_parts(question["answer"]))
    values.extend(chart["title"] for chart in data["charts"])
    values = list(dict.fromkeys(value for value in values if value.strip()))
    translation_args = SimpleNamespace(
        batch_chars=args.batch_chars,
        workers=args.workers,
        cache=args.cache,
    )
    translate_values(values, cache, translation_args, "Herbal comparison Spanish")
    save_cache(args.cache, cache)

    questions = []
    for question in data["questions"]:
        parts = answer_parts(question["answer"])
        translated_question = {
            "id": question["id"],
            "question": comparison_polish(cache[question["question"]], question["question"]),
            "memoryCue": comparison_polish(cache[question["memoryCue"]], question["memoryCue"]),
            "answer": "\n\n".join(comparison_polish(cache[part], part) for part in parts),
        }
        override = overrides.get(question["id"], {})
        if override.get("question"):
            translated_question["question"] = override["question"]
        if override.get("memoryCue"):
            translated_question["memoryCue"] = override["memoryCue"]
        if override.get("answerParts"):
            translated_question["answer"] = "\n\n".join(override["answerParts"])
        questions.append(translated_question)
    charts = [
        {"figure": chart["figure"], "title": comparison_polish(cache[chart["title"]], chart["title"])}
        for chart in data["charts"]
    ]
    output = {
        "version": 1,
        "sourceDataSha256": hashlib.sha256(source_bytes).hexdigest(),
        "engine": "Argos Translate en-es 1.0 with terminology cleanup",
        "automatic": True,
        "questions": questions,
        "charts": charts,
    }
    args.output.write_text(json.dumps(output, ensure_ascii=False, separators=(",", ":")) + "\n", encoding="utf-8")
    args.output.chmod(0o644)
    print(f"Wrote {len(questions)} question and {len(charts)} chart translations to {args.output}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
