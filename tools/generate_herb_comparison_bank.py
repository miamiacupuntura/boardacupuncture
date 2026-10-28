#!/usr/bin/env python3
"""Extract the question-and-answer chapters and comparison charts from Yang's book.

The source PDF is intentionally supplied at generation time and is not copied into the
release. The generated JSON preserves the chapter and question order while rebuilding
the vector bar charts as accessible data.
"""

from __future__ import annotations

import argparse
import itertools
import json
import math
import re
from pathlib import Path

import fitz


CHAPTERS = [
    ("One", "The theory and concepts of Chinese herbal medicine", "Teoría y conceptos de la medicina herbal china"),
    ("Two", "Herbs that release the Exterior", "Hierbas que liberan el Exterior"),
    ("Three", "Herbs that clear Heat", "Hierbas que eliminan el Calor"),
    ("Four", "Herbs that drain downwards", "Hierbas que drenan hacia abajo"),
    ("Five", "Herbs that expel Wind-Dampness", "Hierbas que expulsan Viento-Humedad"),
    ("Six", "Herbs that transform Dampness", "Hierbas que transforman la Humedad"),
    ("Seven", "Aromatic substances for transforming Dampness, external application, and opening the orifices", "Sustancias aromáticas que transforman Humedad, uso externo y apertura de orificios"),
    ("Eight", "Herbs that transform Phlegm", "Hierbas que transforman la Flema"),
    ("Nine", "Herbs that relieve food stagnation, expel parasites, and induce vomiting", "Hierbas que alivian estancamiento de alimentos, expulsan parásitos e inducen vómito"),
    ("Ten", "Herbs that regulate the Qi", "Hierbas que regulan el Qi"),
    ("Eleven", "Herbs that regulate the Blood", "Hierbas que regulan la Sangre"),
    ("Twelve", "Herbs that stop bleeding", "Hierbas que detienen el sangrado"),
    ("Thirteen", "Herbs that warm the Interior and expel Cold", "Hierbas que calientan el Interior y expulsan Frío"),
    ("Fourteen", "Herbs that tonify", "Hierbas que tonifican"),
    ("Fifteen", "Herbs that stabilize and bind", "Hierbas que estabilizan y contienen"),
    ("Sixteen", "Substances that calm the Mind", "Sustancias que calman la Mente"),
    ("Seventeen", "Herbs that subdue Liver-Yang and extinguish Liver-Wind", "Hierbas que someten Yang de Hígado y extinguen Viento de Hígado"),
]


def block_text(block: dict) -> str:
    return "\n".join(
        "".join(span["text"] for span in line["spans"]).strip()
        for line in block.get("lines", [])
        if "".join(span["text"] for span in line["spans"]).strip()
    ).strip()


def normalize(value: str) -> str:
    value = re.sub(r"-\n(?=[a-z])", "", value)
    value = re.sub(r"\s+", " ", value)
    return value.strip()


def memory_cue(answer: str) -> str:
    sentences = re.split(r"(?<=[.!?])\s+", answer)
    cue = " ".join(sentences[:2]).strip()
    if len(cue) > 420:
        cue = cue[:417].rsplit(" ", 1)[0] + "…"
    return cue


def chapter_boundaries(document: fitz.Document) -> list[int]:
    starts: list[int] = []
    for word, _, _ in CHAPTERS:
        marker = f"Chapter {word}"
        starts.append(next(index for index, page in enumerate(document) if marker in page.get_text()))
    appendix = next(index for index, page in enumerate(document) if "Appendix 1" in page.get_text())
    return [*starts, appendix]


def extract_questions(document: fitz.Document, boundaries: list[int]) -> list[dict]:
    questions: list[dict] = []
    for chapter_index, (_, title, title_es) in enumerate(CHAPTERS, 1):
        current: dict | None = None
        chapter_questions: list[dict] = []
        chart_section = False
        for page_index in range(boundaries[chapter_index - 1], boundaries[chapter_index]):
            page = document[page_index]
            for block in page.get_text("dict")["blocks"]:
                if "lines" not in block:
                    continue
                raw = block_text(block)
                if not raw:
                    continue
                spans = [span for line in block["lines"] for span in line["spans"]]
                is_question = bool(re.match(r"^\d{1,2}\s+", raw)) and any(
                    span["size"] >= 13.5 and "Bold" in span["font"] for span in spans
                )
                if is_question:
                    if current:
                        chapter_questions.append(current)
                    match = re.match(r"^(\d{1,2})\s+(.*)", normalize(raw))
                    if not match:
                        raise RuntimeError(f"Question heading could not be parsed on PDF page {page_index + 1}")
                    current = {
                        "chapter": chapter_index,
                        "number": int(match.group(1)),
                        "question": match.group(2),
                        "answerParagraphs": [],
                        "pdfPage": page_index + 1,
                    }
                    chart_section = False
                    continue
                if raw.startswith("Comparisons of strength") or raw.startswith("Fig."):
                    chart_section = True
                if raw == "References":
                    chart_section = True
                if not current or chart_section:
                    continue
                if re.fullmatch(r"\d+", raw) or raw.startswith("part two") or re.fullmatch(r"Chapter \d+", raw):
                    continue
                if "p a r t" in raw or "C h a p t e r" in raw:
                    continue
                if any(span["size"] >= 19 for span in spans):
                    continue
                current["answerParagraphs"].append(normalize(raw))
        if current:
            chapter_questions.append(current)
        expected = list(range(1, len(chapter_questions) + 1))
        actual = [question["number"] for question in chapter_questions]
        if actual != expected:
            raise RuntimeError(f"Chapter {chapter_index} question sequence is not continuous: {actual}")
        for question in chapter_questions:
            answer = "\n\n".join(question.pop("answerParagraphs"))
            if not answer:
                raise RuntimeError(f"Chapter {chapter_index}, question {question['number']} has no answer")
            question["id"] = f"{chapter_index}-{question['number']}"
            question["answer"] = answer
            question["memoryCue"] = memory_cue(answer)
        questions.extend(chapter_questions)
    return questions


def parse_caption(text: str) -> tuple[str, str, list[str]]:
    text = normalize(text)
    match = re.match(r"^Fig\.\s*(\d+\.\d+)\s*•\s*(.*)$", text)
    if not match:
        raise RuntimeError(f"Figure caption could not be parsed: {text[:80]}")
    figure_id, remainder = match.groups()
    title, _, details = remainder.partition(".")
    names = [
        re.sub(r"^and\s+", "", name.strip())
        for name in re.findall(r"(?:^|,\s|\band\s+)([^,(]+?)\s*\(", details)
    ]
    return figure_id, title.strip(), names


def color_hex(fill: tuple[float, float, float]) -> str:
    return "#" + "".join(f"{max(0, min(255, round(channel * 255))):02x}" for channel in fill)


def temperature_label(fill: tuple[float, float, float]) -> str:
    red, green, blue = fill
    if max(fill) - min(fill) < 0.04:
        brightness = sum(fill) / 3
        if brightness < 0.18:
            return "very cold"
        if brightness < 0.7:
            return "cold"
        if brightness < 0.94:
            return "slightly cold"
        return "neutral"
    if red > 0.9 and green < 0.25:
        return "very hot"
    if red > 0.9 and green < 0.65:
        return "warm"
    return "slightly warm"


def fit_bar_slots(rectangles: list[tuple[fitz.Rect, tuple]], count: int) -> list[tuple | None]:
    if not rectangles:
        return [None] * count
    rectangles = sorted(rectangles, key=lambda item: item[0].x0)
    centers = [(rect.x0 + rect.x1) / 2 for rect, _ in rectangles]
    if len(rectangles) > count:
        rectangles = sorted(rectangles, key=lambda item: item[0].height, reverse=True)[:count]
        rectangles.sort(key=lambda item: item[0].x0)
        centers = [(rect.x0 + rect.x1) / 2 for rect, _ in rectangles]
    best: tuple[float, tuple[int, ...], float, float] | None = None
    for slots in itertools.combinations(range(count), len(rectangles)):
        mean_slot = sum(slots) / len(slots)
        mean_x = sum(centers) / len(centers)
        denominator = sum((slot - mean_slot) ** 2 for slot in slots)
        if denominator:
            step = sum((slot - mean_slot) * (x - mean_x) for slot, x in zip(slots, centers, strict=True)) / denominator
        else:
            step = 30
        if not 12 <= step <= 90:
            continue
        start = mean_x - step * mean_slot
        error = sum((x - (start + step * slot)) ** 2 for slot, x in zip(slots, centers, strict=True))
        candidate = (error, slots, start, step)
        if best is None or candidate[0] < best[0]:
            best = candidate
    if best is None:
        raise RuntimeError(f"Could not align {len(rectangles)} bars to {count} labels")
    output: list[tuple | None] = [None] * count
    for slot, rectangle in zip(best[1], rectangles, strict=True):
        output[slot] = rectangle
    return output


def extract_charts(document: fitz.Document) -> list[dict]:
    charts: list[dict] = []
    for page_index, page in enumerate(document):
        captions: list[dict] = []
        for block in page.get_text("dict")["blocks"]:
            if "lines" not in block:
                continue
            text = block_text(block)
            if not text.startswith("Fig."):
                continue
            figure_id, title, names = parse_caption(text)
            captions.append({
                "figure": figure_id,
                "title": title,
                "names": names,
                "rect": fitz.Rect(block["bbox"]),
            })
        for caption in captions:
            chapter = int(caption["figure"].split(".")[0])
            region_x0, region_x1 = 40.0, 505.0
            if caption["rect"].width < 300:
                if (caption["rect"].x0 + caption["rect"].x1) / 2 < 270:
                    region_x1 = 270.0
                else:
                    region_x0 = 260.0
            prior_rows = [
                other["rect"].y1
                for other in captions
                if other["rect"].y1 < caption["rect"].y0 - 12
            ]
            region_y0 = max(prior_rows, default=45.0)
            candidates: list[tuple[fitz.Rect, tuple]] = []
            for drawing in page.get_drawings():
                rect = drawing["rect"]
                fill = drawing["fill"]
                center = (rect.x0 + rect.x1) / 2
                if (
                    fill is not None
                    and 5 <= rect.width <= 20
                    and 3 <= rect.height <= 155
                    and region_x0 <= center <= region_x1
                    and region_y0 < rect.y1 < caption["rect"].y0 - 2
                ):
                    candidates.append((rect, fill))
            baselines: list[list[tuple[fitz.Rect, tuple]]] = []
            for rectangle in candidates:
                group = next(
                    (items for items in baselines if abs(items[0][0].y1 - rectangle[0].y1) < 1.5),
                    None,
                )
                (group if group is not None else baselines.append([]))
                if group is None:
                    group = baselines[-1]
                group.append(rectangle)
            if not baselines:
                raise RuntimeError(f"No bars found for figure {caption['figure']}")
            bars = max(baselines, key=len)
            names = caption["names"]
            if not names:
                raise RuntimeError(f"No herb labels found for figure {caption['figure']}")
            try:
                slots = fit_bar_slots(bars, len(names))
            except RuntimeError as error:
                raise RuntimeError(f"Figure {caption['figure']}: {error}") from error
            maximum_height = max((item[0].height for item in slots if item), default=1)
            values = []
            for name, item in zip(names, slots, strict=True):
                if item is None:
                    values.append({"herb": name, "value": 0, "color": "#ffffff", "temperature": "neutral"})
                    continue
                rect, fill = item
                value = int(round((rect.height / maximum_height) * 20) * 5)
                values.append({
                    "herb": name,
                    "value": min(100, value),
                    "color": color_hex(fill),
                    "temperature": temperature_label(fill),
                })
            charts.append({
                "figure": caption["figure"],
                "chapter": chapter,
                "title": caption["title"],
                "pdfPage": page_index + 1,
                "bars": values,
            })
    charts.sort(key=lambda chart: tuple(int(value) for value in chart["figure"].split(".")))
    if len(charts) != 117:
        raise RuntimeError(f"Expected 117 figures, extracted {len(charts)}")
    return charts


def main() -> int:
    root = Path(__file__).resolve().parents[1]
    parser = argparse.ArgumentParser()
    parser.add_argument("source_pdf", type=Path)
    parser.add_argument(
        "--output",
        type=Path,
        default=root / "study-guide/comparisons/herb-comparison-bank-data.json",
    )
    args = parser.parse_args()
    document = fitz.open(args.source_pdf)
    boundaries = chapter_boundaries(document)
    questions = extract_questions(document, boundaries)
    charts = extract_charts(document)
    chapters = []
    for index, (_, title, title_es) in enumerate(CHAPTERS, 1):
        chapters.append({
            "number": index,
            "title": title,
            "titleEs": title_es,
            "questionCount": sum(question["chapter"] == index for question in questions),
            "chartCount": sum(chart["chapter"] == index for chart in charts),
        })
    data = {
        "version": 1,
        "source": {
            "title": "Chinese Herbal Medicines: Comparisons and Characteristics",
            "author": "Yifan Yang",
            "edition": "Second edition",
            "year": 2010,
        },
        "totals": {"chapters": len(chapters), "questions": len(questions), "charts": len(charts)},
        "chapters": chapters,
        "questions": questions,
        "charts": charts,
    }
    args.output.parent.mkdir(parents=True, exist_ok=True)
    args.output.write_text(json.dumps(data, ensure_ascii=False, separators=(",", ":")) + "\n", encoding="utf-8")
    args.output.chmod(0o644)
    print(f"Generated {len(questions)} questions and {len(charts)} charts in {args.output}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
