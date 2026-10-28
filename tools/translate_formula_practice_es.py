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


def translate_batch(values: list[str]) -> list[str]:
    if len(values) == 1:
        return [translate.translate(values[0], "en", "es").strip()]
    joined = values[0] + "".join(
        f"\nZXSEG{index:04d}ZX\n{value}" for index, value in enumerate(values[1:], 1)
    )
    translated = translate.translate(joined, "en", "es")
    pieces = SEPARATOR_RE.split(translated)
    if len(pieces) == len(values):
        return [piece.strip() for piece in pieces]
    return [translate.translate(value, "en", "es").strip() for value in values]


def collect_texts(data: dict[str, Any]) -> list[str]:
    values: list[str] = []
    for question in data["questions"]:
        values.append(question["prompt"])
        values.extend(question.get("body", []))
        values.extend(option["text"] for option in question["options"])
        values.extend(question.get("feedback", []))
    return list(dict.fromkeys(value for value in values if value.strip()))


def translated_question(question: dict[str, Any], cache: dict[str, str]) -> dict[str, Any]:
    return {
        "sourceNumber": question["sourceNumber"],
        "prompt": cache[question["prompt"]],
        "body": [cache[value] for value in question.get("body", [])],
        "options": [
            {"label": option["label"], "text": cache[option["text"]]}
            for option in question["options"]
        ],
        "feedback": [cache[value] for value in question.get("feedback", [])],
    }


def translate_values(
    values: list[str], cache: dict[str, str], args: argparse.Namespace, label: str
) -> None:
    missing = [value for value in values if value not in cache]
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
    """Retranslate formula-bearing segments while shielding official Pinyin names."""
    ordered_names = sorted(formula_names, key=len, reverse=True)
    alternatives = "|".join(re.escape(name) for name in ordered_names)
    pattern = re.compile(
        r"(?<![A-Za-z])(" + alternatives + r")(?:['’]s)?(?![A-Za-z])",
        re.IGNORECASE,
    )
    canonical = {name.casefold(): name for name in ordered_names}
    records: list[tuple[str, str, dict[str, str]]] = []
    for source in texts:
        replacements: dict[str, str] = {}

        def mask(match: re.Match[str]) -> str:
            token = f"ZXFORMULA{len(replacements):04d}ZX"
            replacements[token] = canonical[match.group(1).casefold()]
            return token

        masked = pattern.sub(mask, source)
        if replacements:
            records.append((source, masked, replacements))

    masked_values = list(dict.fromkeys(masked for _, masked, _ in records))
    translate_values(masked_values, cache, args, "Formula-name protection")
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
    cache = load_cache(args.cache)
    texts = collect_texts(data)
    translate_values(texts, cache, args, "Spanish translation")
    refine_formula_names(
        texts, [formula["name"] for formula in data["formulas"]], cache, args
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
            translated_question(question, cache) for question in data["questions"]
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
