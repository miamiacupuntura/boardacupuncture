#!/usr/bin/env python3
"""Generate the automatic Spanish layer for Formula Practice.

The English question bank remains authoritative and unchanged. Translations
are stored separately and joined by source question number in the browser.
"""

from __future__ import annotations

import argparse
from concurrent.futures import ProcessPoolExecutor
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
CLINICAL_TERMS_ES = {
    "urinary incontinence": "incontinencia urinaria",
    "frequent urination": "micción frecuente",
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
}


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
        r"\b(?:[A-Z][A-Za-z'-]*\s+){1,7}(?:Tang|San|Wan|Dan|Yin|Gao)\b"
    )
    parenthetical_pattern = re.compile(
        r"\(([a-z][a-z' -]{2,80}(?:tang|san|wan|dan|yin|gao))\)"
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


def translated_question(
    question: dict[str, Any], cache: dict[str, str], overrides: dict[str, Any]
) -> dict[str, Any]:
    translated = {
        "sourceNumber": question["sourceNumber"],
        "prompt": cache[question["prompt"]],
        "body": [cache[value] for value in question.get("body", [])],
        "options": [
            {"label": option["label"], "text": cache[option["text"]]}
            for option in question["options"]
        ],
        "feedback": [cache[value] for value in question.get("feedback", [])],
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
            replacement = CLINICAL_TERMS_ES[match.group(1).casefold()]
            if match.group(1)[0].isupper():
                replacement = replacement[0].upper() + replacement[1:]
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
        cache[source] = result
    save_cache(args.cache, cache)


def main(argv: Sequence[str] | None = None) -> int:
    args = parse_args(argv)
    ensure_model()
    source_bytes = args.source.read_bytes()
    data = json.loads(source_bytes)
    overrides = json.loads(args.overrides.read_text(encoding="utf-8"))
    cache = load_cache(args.cache)
    texts = collect_texts(data)
    translate_values(texts, cache, args, "Spanish translation")
    refine_formula_names(
        texts, collect_formula_names(data, texts), cache, args
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
            translated_question(question, cache, overrides)
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
