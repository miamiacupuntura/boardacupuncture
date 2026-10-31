#!/usr/bin/env python3
"""Build the Appendix A herb-to-Single-Herbs-card routing index."""

from __future__ import annotations

import json
import re
from pathlib import Path


ROOT = Path(__file__).resolve().parents[1]
ROSTER = ROOT / "tools" / "data" / "ncbahm_2026_appendix_a_herbs.txt"
OUTPUT = ROOT / "blocks" / "myprogress" / "single-herb-card-index.json"

CATEGORY_FILES = {
    1: ROOT / "blocks" / "myprogress" / "single-herbs-study-guide.php",
    2: ROOT / "blocks" / "myprogress" / "single-herbs-category-cool-acrid.php",
    3: ROOT / "blocks" / "myprogress" / "single-herbs-category-clear-heat-drain-fire.php",
    4: ROOT / "blocks" / "myprogress" / "single-herbs-category-clear-heat-cool-blood.php",
    5: ROOT / "blocks" / "myprogress" / "single-herbs-category-clear-heat-dry-damp.php",
    6: ROOT / "blocks" / "myprogress" / "single-herbs-category-clear-resolve-toxicity.php",
    7: ROOT / "blocks" / "myprogress" / "single-herbs-category-clear-deficient-heat.php",
    8: ROOT / "blocks" / "myprogress" / "single-herbs-category-clear-summer-heat.php",
    9: ROOT / "blocks" / "myprogress" / "single-herbs-category-downward-draining-purgatives.php",
    10: ROOT / "blocks" / "myprogress" / "single-herbs-category-moist-laxatives.php",
    11: ROOT / "blocks" / "myprogress" / "single-herbs-category-harsh-expellants.php",
    12: ROOT / "blocks" / "myprogress" / "single-herbs-category-drain-dampness.php",
    13: ROOT / "blocks" / "myprogress" / "single-herbs-category-cool-transform-phlegm.php",
    14: ROOT / "blocks" / "myprogress" / "single-herbs-category-warm-transform-phlegm.php",
    15: ROOT / "blocks" / "myprogress" / "single-herbs-category-relieve-cough-wheezing.php",
    16: ROOT / "blocks" / "myprogress" / "single-herbs-category-aromatic-transform-dampness.php",
    17: ROOT / "blocks" / "myprogress" / "single-herbs-category-relieve-food-stagnation.php",
    18: ROOT / "blocks" / "myprogress" / "single-herbs-category-regulate-qi.php",
    19: ROOT / "blocks" / "myprogress" / "single-herbs-category-invigorate-blood.php",
    20: ROOT / "blocks" / "myprogress" / "single-herbs-category-stop-bleeding.php",
    21: ROOT / "blocks" / "myprogress" / "single-herbs-category-warm-interior.php",
    23: ROOT / "blocks" / "myprogress" / "single-herbs-category-tonify-qi.php",
    24: ROOT / "blocks" / "myprogress" / "single-herbs-category-tonify-blood.php",
    25: ROOT / "blocks" / "myprogress" / "single-herbs-category-tonify-yang.php",
    26: ROOT / "blocks" / "myprogress" / "single-herbs-category-tonify-yin.php",
    27: ROOT / "blocks" / "myprogress" / "single-herbs-category-stabilize-bind.php",
    28: ROOT / "blocks" / "myprogress" / "single-herbs-category-anchor-settle-spirit.php",
    29: ROOT / "blocks" / "myprogress" / "single-herbs-category-nourish-heart-calm-spirit.php",
    30: ROOT / "blocks" / "myprogress" / "single-herbs-category-open-orifices.php",
    31: ROOT / "blocks" / "myprogress" / "single-herbs-category-extinguish-wind.php",
}

ALIASES = {
    "xin yi hua": "xin yi",
    "ci zhi": "ci shi",
}


def normalized(value: str) -> str:
    value = re.sub(r"\s*\*$", "", value).strip().lower()
    value = re.sub(r"[^a-z0-9]+", " ", value).strip()
    return ALIASES.get(value, value)


def roster_entries() -> list[dict[str, object]]:
    entries: list[dict[str, object]] = []
    for line in ROSTER.read_text(encoding="utf-8").splitlines():
        line = line.strip()
        if not line:
            continue
        pinyin, separator, latin = line.rpartition(" (")
        if not separator or not latin.endswith(")"):
            raise ValueError(f"Invalid Appendix A entry: {line}")
        entries.append({"name": pinyin, "latin": latin[:-1]})
    if len(entries) != 304:
        raise ValueError(f"Expected 304 Appendix A herbs, found {len(entries)}")
    return entries


def category_routes() -> dict[str, int]:
    routes: dict[str, int] = {}
    pattern = re.compile(r"'name'\s*=>\s*'([^']+)'")
    for guide_number, path in CATEGORY_FILES.items():
        for match in pattern.finditer(path.read_text(encoding="utf-8")):
            routes.setdefault(normalized(match.group(1)), guide_number)
    return routes


def main() -> None:
    routes = category_routes()
    entries = roster_entries()
    routed = 0
    for entry in entries:
        guide_number = routes.get(normalized(str(entry["name"])))
        entry["guideNumber"] = guide_number
        if guide_number is not None:
            routed += 1
    payload = {
        "version": 1,
        "source": "NCBAHM Chinese Herbology Content Outline 2026, Appendix A",
        "total": len(entries),
        "routedToDetailedStudy": routed,
        "herbs": entries,
    }
    OUTPUT.write_text(json.dumps(payload, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")
    print(f"Wrote {len(entries)} herbs; {routed} route to an existing detailed Single Herbs category.")


if __name__ == "__main__":
    main()
