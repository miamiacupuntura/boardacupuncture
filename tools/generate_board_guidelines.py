#!/usr/bin/env python3
from pathlib import Path
from html import escape
from urllib.parse import quote
import json
import re
import unicodedata

ROOT = Path(__file__).resolve().parents[1]
OUT = ROOT / "study-guide" / "guidelines"
OFFICIAL = "https://www.ncbahm.org/"
ACPL = "https://www.ncbahm.org/wp-content/uploads/pdf/NCBAHM_ACPL_Exam_Content_Outline-w-Bibliography_Jan_2026.pdf"
CH = "https://www.ncbahm.org/wp-content/uploads/pdf/NCBAHM_CH_Exam_Content_Outline-w-Bibliography_Jan_2026.pdf"
NCCAOM_2020_CH = "https://www.ncbahm.org/wp-content/uploads/pdf/2020-CH-Content-Outline-and-Bibliography-Updated-June-2021.pdf"
APPENDIX_A_HERBS_FILE = ROOT / "tools" / "data" / "ncbahm_2026_appendix_a_herbs.txt"
APPENDIX_C_FORMULAS_FILE = ROOT / "tools" / "data" / "ncbahm_2026_appendix_c_formulas.txt"
NCCAOM_2020_HERBS_FILE = ROOT / "tools" / "data" / "nccaom_2020_appendix_a_herbs.txt"
NCCAOM_2020_FORMULAS_FILE = ROOT / "tools" / "data" / "nccaom_2020_appendix_b_formulas.txt"
FORMULA_GUIDE_DATA_FILE = ROOT / "blocks" / "myprogress" / "formulas-study-guide-data.json"
DUI_YAO_DATA_FILE = ROOT / "study-guide" / "comparisons" / "dui-yao-data.json"
HERB_PAIR_RESEARCH = "https://pubmed.ncbi.nlm.nih.gov/24791518/"
HERB_PAIR_COMPATIBILITY_REVIEW = "https://pubmed.ncbi.nlm.nih.gov/22871585/"

BOARDS = [
    {
        "slug": "foundations",
        "en": "Foundations of Oriental Medicine",
        "es": "Fundamentos de Medicina Oriental",
        "summary_en": "Organize theory, diagnosis, pattern differentiation and treatment principles as one reasoning chain.",
        "summary_es": "Organiza teoría, diagnóstico, diferenciación de patrones y principios de tratamiento como una sola cadena de razonamiento.",
        "scope_en": ["Foundational theories and terminology", "Four examinations and diagnostic synthesis", "Pattern differentiation", "Treatment principles and clinical reasoning"],
        "scope_es": ["Teorías y terminología fundamentales", "Cuatro exámenes y síntesis diagnóstica", "Diferenciación de patrones", "Principios de tratamiento y razonamiento clínico"],
        "study_en": "Study from findings to pattern to treatment principle. Memorized facts become useful only when they support that sequence.",
        "study_es": "Estudia desde los hallazgos al patrón y luego al principio de tratamiento. Los datos memorizados son útiles cuando apoyan esa secuencia.",
    },
    {
        "slug": "acupuncture-point-location",
        "en": "Acupuncture with Point Location",
        "es": "Acupuntura con Localización de Puntos",
        "summary_en": "Use the September 1, 2026 outline and its four weighted domains.",
        "summary_es": "Usa el temario vigente desde el 1 de septiembre de 2026 y sus cuatro dominios ponderados.",
        "scope_en": ["Point Location — 15%", "Safety — 15%", "Treatment Planning — 40%", "Treatment Techniques — 30%"],
        "scope_es": ["Localización de puntos — 15%", "Seguridad — 15%", "Planificación del tratamiento — 40%", "Técnicas de tratamiento — 30%"],
        "study_en": "Weight practice toward treatment planning and technique while preserving daily location and safety review.",
        "study_es": "Da más práctica a planificación y técnica, manteniendo repaso diario de localización y seguridad.",
        "source": ACPL,
    },
    {
        "slug": "chinese-herbal-medicine",
        "en": "Chinese Herbal Medicine",
        "es": "Medicina Herbal China",
        "summary_en": "Turn the current Board outline into a guided study route for herbs, Duì Yào pairs, formulas and clinical safety.",
        "summary_es": "Convierte el temario vigente del Board en una ruta guiada para estudiar hierbas, parejas Duì Yào, fórmulas y seguridad clínica.",
        "scope_en": ["Appendix A — individual herbs", "Appendix B — 51 Duì Yào pairs", "Appendix C — formulas", "Appendix D — obsolete substances and substitutions"],
        "scope_es": ["Apéndice A — hierbas individuales", "Apéndice B — 51 parejas Duì Yào", "Apéndice C — fórmulas", "Apéndice D — sustancias obsoletas y sustituciones"],
        "study_en": "Open the dedicated three-card guide for the herb list, formula list and current-outline analysis.",
        "study_es": "Abre la guía de tres tarjetas: listado de hierbas, listado de fórmulas y análisis del temario vigente.",
    },
    {
        "slug": "biomedicine",
        "en": "Biomedicine",
        "es": "Biomedicina",
        "summary_en": "Connect biomedical knowledge to assessment, differential diagnosis, referral and patient safety.",
        "summary_es": "Conecta conocimiento biomédico con evaluación, diagnóstico diferencial, derivación y seguridad del paciente.",
        "scope_en": ["Biomedical sciences", "History, examination and diagnostic testing", "Conditions and biomedical treatment", "Red flags, referral and public safety"],
        "scope_es": ["Ciencias biomédicas", "Historia, examen y pruebas diagnósticas", "Enfermedades y tratamiento biomédico", "Banderas rojas, derivación y seguridad pública"],
        "study_en": "Use clinical presentations, not isolated vocabulary: identify urgency, likely systems, tests and the appropriate referral boundary.",
        "study_es": "Usa presentaciones clínicas, no vocabulario aislado: identifica urgencia, sistemas probables, pruebas y el límite correcto de derivación.",
    },
]

AUDITS = {
    "foundations": [
        ("Theory", "Teoría", "Explain the governing theory in plain language.", "Explica la teoría aplicable en lenguaje claro."),
        ("Findings", "Hallazgos", "Collect and connect the four examinations instead of reading each sign alone.", "Reúne y conecta los cuatro exámenes sin interpretar cada signo de forma aislada."),
        ("Pattern", "Patrón", "Name the pattern and defend it with positive and negative evidence.", "Nombra el patrón y defiéndelo con evidencia positiva y negativa."),
        ("Principle", "Principio", "Choose a treatment principle that follows directly from the pattern.", "Elige un principio de tratamiento que se derive directamente del patrón."),
    ],
    "acupuncture-point-location": [
        ("15% · Location", "15% · Localización", "Use landmarks, proportional measurement and channel relationships.", "Usa referencias anatómicas, medidas proporcionales y relaciones de canal."),
        ("15% · Safety", "15% · Seguridad", "Review contraindications, depth, angle, adverse events and infection control.", "Repasa contraindicaciones, profundidad, ángulo, eventos adversos y control de infecciones."),
        ("40% · Planning", "40% · Planificación", "Make point selection and treatment strategy the largest practice block.", "Haz de la selección de puntos y la estrategia el bloque principal de práctica."),
        ("30% · Techniques", "30% · Técnicas", "Practice needling and adjunctive techniques through clinical decisions.", "Practica punción y técnicas complementarias mediante decisiones clínicas."),
    ],
    "chinese-herbal-medicine": [
        ("A · Herbs", "A · Hierbas", "Verify identity and part, properties, actions, indications, dosage and safety.", "Verifica identidad y parte, propiedades, acciones, indicaciones, dosis y seguridad."),
        ("B · Duì Yào", "B · Duì Yào", "Know all 51 pairs by shared strategy, division of labor, formula and safety clue.", "Domina las 51 parejas por estrategia compartida, división del trabajo, fórmula y clave de seguridad."),
        ("C · Formulas", "C · Fórmulas", "Connect ingredients and hierarchy to pattern, modification and contraindication.", "Conecta ingredientes y jerarquía con patrón, modificación y contraindicación."),
        ("D · Safety", "D · Seguridad", "Separate obsolete substances, substitutions, interactions and dispensing decisions.", "Separa sustancias obsoletas, sustituciones, interacciones y decisiones de dispensación."),
    ],
    "biomedicine": [
        ("Triage", "Triaje", "Identify emergencies and red flags before building a differential.", "Identifica emergencias y banderas rojas antes de construir el diferencial."),
        ("Assessment", "Evaluación", "Choose the relevant history, examination and diagnostic tests.", "Elige la historia, examen y pruebas diagnósticas pertinentes."),
        ("Differential", "Diferencial", "Compare likely conditions using discriminating findings.", "Compara las condiciones probables usando hallazgos discriminantes."),
        ("Boundary", "Límite", "Decide treat, co-manage, refer or activate emergency care.", "Decide tratar, co-manejar, derivar o activar atención de emergencia."),
    ],
}

RESOURCES = {
    "foundations": [
        ("Open Board course", "Abrir curso de Board", "/blocks/myprogress/study-guide.php?area=foundations", "/blocks/myprogress/study-guide.php?area=foundations"),
        ("Pattern comparisons", "Comparaciones de patrones", "../comparisons/patterns.html", "../comparisons/patterns-es.html"),
        ("Pathology comparisons", "Comparaciones de patologías", "../comparisons/pathologies.html", "../comparisons/pathologies-es.html"),
    ],
    "acupuncture-point-location": [
        ("Open Board course", "Abrir curso de Board", "/blocks/myprogress/study-guide.php?area=pointlocation", "/blocks/myprogress/study-guide.php?area=pointlocation"),
        ("Official 2026 outline", "Temario oficial 2026", ACPL, ACPL),
    ],
    "chinese-herbal-medicine": [],
    "biomedicine": [
        ("Open Board course", "Abrir curso de Board", "/blocks/myprogress/study-guide.php?area=biomedicine", "/blocks/myprogress/study-guide.php?area=biomedicine"),
    ],
}

def top(lang, title, alternate, back="../board-guidelines", css_version="20261027-7"):
    es = lang == "es"
    suffix = "-es.html" if es else ".html"
    return f'''<!doctype html><html lang="{lang}"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="description" content="{escape(title)}"><title>{escape(title)} | AcupunctureTests</title><link rel="alternate" hreflang="{'en' if es else 'es'}" href="{alternate}"><link rel="stylesheet" href="guidelines.css?v={css_version}"></head><body><header class="guide-topbar"><div class="guide-shell guide-topbar__inner"><a class="guide-brand" href="{back}{suffix}">☯ AcupunctureTests · {'Guías de Board' if es else 'Board Guidelines'}</a><div class="guide-actions"><a class="guide-button guide-button--outline" href="{alternate}">{'English' if es else 'Español'}</a><a class="guide-button guide-button--outline" href="/my/">← Dashboard</a></div></div></header><main class="guide-shell">'''

def foot(es=False, returnscript=False):
    script = '<script src="guideline-return.js?v=20261101-2"></script>' if returnscript else ''
    reviewed = (
        ('Revisado el 2 de noviembre de 2026' if es else 'Reviewed November 2, 2026')
        if returnscript else
        ('Revisado el 27 de octubre de 2026' if es else 'Reviewed October 27, 2026')
    )
    return f'''</main><footer class="guide-footer"><div class="guide-shell">{reviewed} · {'El temario oficial vigente siempre prevalece.' if es else 'The current official outline always controls.'}</div></footer>{script}</body></html>'''


def appendix_a_herbs():
    herbs = [line.strip() for line in APPENDIX_A_HERBS_FILE.read_text(encoding="utf-8").splitlines() if line.strip()]
    if len(herbs) != 304:
        raise ValueError(f"Expected 304 herbs in the 2026 NCBAHM Appendix A roster, found {len(herbs)}")
    return herbs


def appendix_c_formulas():
    formulas = [line.strip() for line in APPENDIX_C_FORMULAS_FILE.read_text(encoding="utf-8").splitlines() if line.strip()]
    if len(formulas) != 181 or len(set(formulas)) != 181:
        raise ValueError(
            f"Expected 181 unique formulas in the 2026 NCBAHM Appendix C roster, "
            f"found {len(formulas)} entries and {len(set(formulas))} unique entries"
        )
    for entry in formulas:
        pinyin, separator, english = entry.partition(" (")
        if not pinyin or not separator or not english.endswith(")"):
            raise ValueError(f"Invalid Appendix C formula entry: {entry}")
    return formulas


def historical_rosters():
    old_herbs = [line.strip() for line in NCCAOM_2020_HERBS_FILE.read_text(encoding="utf-8").splitlines() if line.strip()]
    old_formulas = [line.strip() for line in NCCAOM_2020_FORMULAS_FILE.read_text(encoding="utf-8").splitlines() if line.strip()]
    current_herbs = [entry.partition(" (")[0] for entry in appendix_a_herbs()]
    current_formulas = [entry.partition(" (")[0] for entry in appendix_c_formulas()]
    expected = {
        "NCCAOM 2020 herbs": (old_herbs, 272),
        "NCCAOM 2020 formulas": (old_formulas, 162),
        "NCBAHM 2026 herbs": (current_herbs, 304),
        "NCBAHM 2026 formulas": (current_formulas, 181),
    }
    for label, (items, count) in expected.items():
        if len(items) != count or len(set(items)) != count:
            raise ValueError(f"Expected {count} unique {label}, found {len(items)} entries and {len(set(items))} unique names")
    return old_herbs, current_herbs, old_formulas, current_formulas


def historical_roster_annex(es=False):
    old_herbs, current_herbs, old_formulas, current_formulas = historical_rosters()
    herb_aliases = {"Gu Ya": "Gu Ya", "Xin Yi Hua": "Xin Yi"}
    formula_aliases = {
        "Ge Gen Huang Lian Huang Qin Tang": "Ge Gen Huang Qin Huang Lian Tang",
        "Jin Gui Shen Qi Wan": "Shen Qi Wan",
        "Qing Wen Bai Du San": "Qing Wen Bai Du Yin",
        "Tai Shan Pan Shi San": "Taishan Pan Shi San",
    }

    def normalized(name, aliases):
        return aliases.get(name, name)

    def changes(old, current, aliases):
        old_keys = {normalized(name, aliases) for name in old}
        current_keys = {normalized(name, aliases) for name in current}
        removed = [name for name in old if normalized(name, aliases) not in current_keys]
        added = [name for name in current if normalized(name, aliases) not in old_keys]
        return sorted(removed), sorted(added)

    removed_herbs, added_herbs = changes(old_herbs, current_herbs, herb_aliases)
    removed_formulas, added_formulas = changes(old_formulas, current_formulas, formula_aliases)
    if (len(removed_herbs), len(added_herbs), len(removed_formulas), len(added_formulas)) != (6, 38, 2, 21):
        raise ValueError("Unexpected NCCAOM 2020 to NCBAHM 2026 roster diff")

    def list_html(items, links=None):
        links = links or {}
        rendered = []
        for item in items:
            content = escape(item)
            if item in links:
                content = f'<a href="{links[item]}">{content}</a>'
            rendered.append(f'<li>{content}</li>')
        return '<ol class="guide-roster-comparison__list">' + ''.join(rendered) + '</ol>'

    def change_list(items, empty):
        if not items:
            return f'<span class="guide-roster-change__empty">{empty}</span>'
        return '<ul class="guide-roster-change__list">' + ''.join(f'<li>{escape(item)}</li>' for item in items) + '</ul>'

    old_heading = "Antes · NCCAOM 2020" if es else "Before · NCCAOM 2020"
    current_heading = "Ahora · NCBAHM 2026" if es else "Now · NCBAHM 2026"
    no_entries = "Ninguna entrada nueva" if es else "No new entries"
    removed_label = "salieron" if es else "removed"
    added_label = "entraron" if es else "added"
    language_query = "&amp;lang=es" if es else ""
    historical_formula_links = {
        "Run Chang Wan": f'/blocks/myprogress/formulas-study-guide.php?focus=official-formulas-ch3{language_query}#formula-C02-01',
        "Juan Bi Tang": f'/blocks/myprogress/formulas-study-guide.php?focus=expel-damp-chapter-16{language_query}#formula-C16-29',
    }
    summary_rows = [
        (
            "Hierbas" if es else "Herbs",
            f'<strong>{len(removed_herbs)} {removed_label}</strong>{change_list(removed_herbs, no_entries)}',
            f'<strong>{len(added_herbs)} {added_label}</strong>{change_list(added_herbs, no_entries)}',
        ),
        (
            "Fórmulas" if es else "Formulas",
            f'<strong>{len(removed_formulas)} {removed_label}</strong>{change_list(removed_formulas, no_entries)}',
            f'<strong>{len(added_formulas)} {added_label}</strong>{change_list(added_formulas, no_entries)}',
        ),
    ]
    return f'''<section class="guide-section guide-section--annex guide-section--historical" id="historical-roster-crosswalk"><p class="guide-eyebrow">{'Anexo histórico · NCCAOM 2020 → NCBAHM 2026' if es else 'Historical annex · NCCAOM 2020 → NCBAHM 2026'}</p><h2>{'Qué estaba antes y qué está ahora' if es else 'What was listed before and what is listed now'}</h2><p>{'Esta comparación usa el Chinese Herbology Content Outline de NCCAOM efectivo el 1 de enero de 2020 (actualizado en 2021) y el temario vigente de NCBAHM efectivo el 1 de septiembre de 2026. Compara alcance de examen, no eficacia clínica.' if es else 'This comparison uses the NCCAOM Chinese Herbology Content Outline effective January 1, 2020 (updated in 2021) and the current NCBAHM outline effective September 1, 2026. It compares exam scope, not clinical effectiveness.'}</p><h3>{'Hierbas individuales' if es else 'Individual herbs'}</h3><div class="guide-table-wrap"><table class="guide-table guide-roster-comparison"><thead><tr><th>{old_heading} · {len(old_herbs)}</th><th>{current_heading} · {len(current_herbs)}</th></tr></thead><tbody><tr><td>{list_html(old_herbs)}</td><td>{list_html(current_herbs)}</td></tr></tbody></table></div><h3>{'Fórmulas' if es else 'Formulas'}</h3><div class="guide-table-wrap"><table class="guide-table guide-roster-comparison"><thead><tr><th>{old_heading} · {len(old_formulas)}</th><th>{current_heading} · {len(current_formulas)}</th></tr></thead><tbody><tr><td>{list_html(old_formulas, historical_formula_links)}</td><td>{list_html(current_formulas)}</td></tr></tbody></table></div><h3>{'Resumen de salidas y entradas' if es else 'Summary of removals and additions'}</h3><p>{'Para no crear cambios falsos, se normalizaron cuatro variantes de nombre de fórmulas y la variante herbal Xin Yi Hua → Xin Yi. Gu Ya permanece; solo cambió la identificación latina publicada.' if es else 'To avoid false changes, four formula name variants and the herb variant Xin Yi Hua → Xin Yi were normalized. Gu Ya remains; only its published Latin identification changed.'}</p><div class="guide-table-wrap"><table class="guide-table guide-roster-change"><thead><tr><th>{'Listado' if es else 'Roster'}</th><th>{'Se fueron' if es else 'Removed'}</th><th>{'Entraron' if es else 'Added'}</th></tr></thead><tbody>{''.join(f'<tr><th>{label}</th><td>{removed}</td><td>{added}</td></tr>' for label, removed, added in summary_rows)}</tbody></table></div><div class="guide-annex-source"><strong>{'Fuentes oficiales:' if es else 'Official sources:'}</strong> <a href="{NCCAOM_2020_CH}" target="_blank" rel="noopener">NCCAOM · Chinese Herbology Content Outline 2020 ↗</a> · <a href="{CH}" target="_blank" rel="noopener">NCBAHM · Chinese Herbology Content Outline 2026 ↗</a></div></section>'''


def appendix_a_roster(es=False):
    items = []
    for entry in appendix_a_herbs():
        pinyin, separator, latin = entry.partition(" (")
        if not separator or not latin.endswith(")"):
            raise ValueError(f"Invalid Appendix A herb entry: {entry}")
        herb_url = f'/blocks/myprogress/single-herbs-study-guide.php?herb={quote(pinyin)}'
        if es:
            herb_url += '&amp;lang=es'
        label = f'Abrir ficha de {pinyin}' if es else f'Open card for {pinyin}'
        items.append(f'<li><a class="guide-herb-roster__card-link" href="{herb_url}" aria-label="{escape(label, quote=True)}"><strong>{escape(pinyin)}</strong></a> <span>(<em>{escape(latin[:-1])}</em>)</span></li>')
    return f'''<section class="guide-section guide-section--annex" id="appendix-a-current-roster"><p class="guide-eyebrow">{'Anexo · Apéndice A vigente' if es else 'Annex · Current Appendix A'}</p><h2>{'Listado oficial de 304 hierbas individuales' if es else 'Official roster of 304 individual herbs'}</h2><p>{'Transcripción de Appendix A del Chinese Herbology Content Outline de NCBAHM, efectivo el 1 de septiembre de 2026. El Board indica que el examen se concentra en esta lista, pero puede mencionar otras hierbas y usarlas como distractores.' if es else 'Transcribed from Appendix A of the NCBAHM Chinese Herbology Content Outline, effective September 1, 2026. The Board states that the exam focuses on this roster, but other herbs may still be mentioned and used as distractors.'}</p><ol class="guide-herb-roster">{''.join(items)}</ol><div class="guide-annex-source"><strong>{'Fuente oficial:' if es else 'Official source:'}</strong> <a href="{CH}" target="_blank" rel="noopener">NCBAHM · Chinese Herbology Content Outline 2026 ↗</a> · <a href="#appendix-a-study">{'Volver a la sección de estudio ↑' if es else 'Back to the study section ↑'}</a></div></section>'''


def normalized_pinyin(value):
    decomposed = unicodedata.normalize("NFD", value)
    return "".join(char for char in decomposed if unicodedata.category(char) != "Mn").lower()


def formula_has_herb(ingredients, herb):
    ingredients = normalized_pinyin(ingredients)
    herb = normalized_pinyin(herb)
    return re.search(r"(?<![a-z])" + re.escape(herb).replace(r"\ ", r"\s+") + r"(?![a-z])", ingredients) is not None


def dui_yao_annex(es=False):
    pairs = json.loads(DUI_YAO_DATA_FILE.read_text(encoding="utf-8"))
    formula_data = json.loads(FORMULA_GUIDE_DATA_FILE.read_text(encoding="utf-8"))
    formulas = formula_data["formulas"]
    if len(pairs) != 51 or [pair["number"] for pair in pairs] != list(range(1, 52)):
        raise ValueError("Expected the 51 numbered NCBAHM 2026 Duì Yào pairs")
    if len(formulas) != 63:
        raise ValueError("Expected the 63 detailed core formulas")

    current_herbs = {normalized_pinyin(entry.partition(" (")[0]) for entry in appendix_a_herbs()}
    pair_herbs = []
    for pair in pairs:
        components = [normalized_pinyin(item.strip()).title() for item in pair["title"].split(" + ")]
        if len(components) != 2:
            raise ValueError(f"Invalid Duì Yào title: {pair['title']}")
        pair["components"] = components
        pair_herbs.extend(components)
    unique_pair_herbs = sorted(set(pair_herbs))
    literal_pair_gaps = [herb for herb in unique_pair_herbs if normalized_pinyin(herb) not in current_herbs]
    if literal_pair_gaps != ["Xin Yi Hua"] or normalized_pinyin("Xin Yi") not in current_herbs:
        raise ValueError(f"Unexpected Duì Yào herb coverage result: {literal_pair_gaps}")

    language_query = "&amp;lang=es" if es else ""
    pair_suffix = "-es" if es else ""

    def pair_link(pair):
        return f'../comparisons/{pair["slug"]}{pair_suffix}.html'

    def formula_link(formula):
        return f'/blocks/myprogress/formulas-study-guide.php?focus={quote(formula["categoryKey"])}{language_query}#formula-{formula["number"]}'

    action_items = []
    priority_rows = []
    for pair in pairs:
        action = pair["action"]["es" if es else "en"]
        signature = pair["formulas"]["es" if es else "en"]
        action_items.append(
            f'<li data-dui-action="{pair["number"]}"><a href="{pair_link(pair)}"><strong>{escape(pair["title"])}</strong></a>: '
            f'{escape(action)}<small><strong>{"Fórmula(s) de referencia" if es else "Reference formula(s)"}:</strong> {escape(signature)}</small></li>'
        )
        first, second = pair["components"]
        hits = [formula for formula in formulas if formula_has_herb(formula["ingredients"], first) and formula_has_herb(formula["ingredients"], second)]
        if len(hits) >= 2:
            formula_links = ", ".join(f'<a href="{formula_link(formula)}">{escape(formula["name"])}</a>' for formula in hits)
            priority_rows.append((len(hits), pair, formula_links))
    priority_rows.sort(key=lambda item: (-item[0], item[1]["number"]))

    # Literal labels in the 63-formula data that are not standalone Appendix A names.
    # The first 16 normalize to a listed medicinal; the final historical label requires a scope note.
    ingredient_crosswalk = [
        ("Zhi Gan Cao", "Gan Cao", "processed"),
        ("Sheng Gan Cao", "Gan Cao", "processed"),
        ("Jing Jie Sui", "Jing Jie", "part"),
        ("Zhu Ye", "Dan Zhu Ye", "alias"),
        ("Sheng Jiang Pi", "Sheng Jiang", "part"),
        ("Fu Ling Pi", "Fu Ling", "part"),
        ("Zhi Fu Zi", "Fu Zi", "processed"),
        ("Niu Xi", "Huai Niu Xi / Chuan Niu Xi", "generic"),
        ("Chuan Jiao", "Hua Jiao", "alias"),
        ("Su Zi", "Zi Su Zi", "alias"),
        ("Su Ye", "Zi Su Ye", "alias"),
        ("Ju Pi", "Chen Pi", "alias"),
        ("Chun Gen Pi", "Chun Pi", "alias"),
        ("Zhi Cao Wu", "Cao Wu", "processed"),
        ("Zhi Chuan Wu", "Chuan Wu", "processed"),
        ("Geng Mi", "Jing Mi", "alias"),
        ("Zhu Sha", "Appendix D", "obsolete"),
    ]
    for label, current_name, status in ingredient_crosswalk:
        if not any(formula_has_herb(formula["ingredients"], label) for formula in formulas):
            raise ValueError(f"Formula ingredient audit label not found: {label}")
        if status not in {"outside", "obsolete", "generic"} and normalized_pinyin(current_name) not in current_herbs:
            raise ValueError(f"Formula ingredient crosswalk target not in Appendix A: {current_name}")
    for candidate in ("Huai Niu Xi", "Chuan Niu Xi"):
        if normalized_pinyin(candidate) not in current_herbs:
            raise ValueError(f"Formula ingredient crosswalk target not in Appendix A: {candidate}")

    relationship_rows = [
        ("Dān Xíng · 单行", "Uso individual; es el punto de comparación, no una pareja." if es else "Single use; the comparison baseline, not a pair."),
        ("Xiāng Xū · 相须", "Acciones semejantes se refuerzan mutuamente." if es else "Similar actions mutually reinforce one another."),
        ("Xiāng Shǐ · 相使", "Una hierba dirige y la otra asiste o amplía su efecto." if es else "One medicinal leads while the other assists or extends its effect."),
        ("Xiāng Wèi · 相畏", "La toxicidad o aspereza de una es contenida por la otra." if es else "The toxicity or harshness of one is restrained by the other."),
        ("Xiāng Shā · 相杀", "Una reduce o elimina la toxicidad de la otra; es la perspectiva recíproca de Xiāng Wèi." if es else "One reduces or removes the other's toxicity; the reciprocal perspective of Xiāng Wèi."),
        ("Xiāng Wù · 相恶", "Una debilita o interfiere con el efecto terapéutico buscado de la otra." if es else "One weakens or interferes with the other's intended therapeutic effect."),
        ("Xiāng Fǎn · 相反", "La combinación aumenta toxicidad o reacciones adversas y debe evitarse." if es else "The combination increases toxicity or adverse effects and should be avoided."),
    ]
    status_labels = {
        "processed": "forma procesada" if es else "processed form",
        "part": "parte de la hierba" if es else "herb part",
        "alias": "alias o nombre equivalente" if es else "alias or equivalent name",
        "generic": "nombre genérico" if es else "generic name",
        "obsolete": "histórica y obsoleta" if es else "historical and obsolete",
    }
    crosswalk_rows = []
    for label, current_name, status in ingredient_crosswalk:
        hits = [formula for formula in formulas if formula_has_herb(formula["ingredients"], label)]
        names = ", ".join(f'<a href="{formula_link(formula)}">{escape(formula["name"])}</a>' for formula in hits)
        crosswalk_rows.append(
            f'<tr><th>{escape(label)}</th><td>{escape(current_name)}</td><td>{escape(status_labels[status])}</td><td>{names}</td></tr>'
        )

    priority_html = "".join(
        f'<tr data-dui-priority="{pair["number"]}"><th><a href="{pair_link(pair)}">{escape(pair["title"])}</a></th><td><strong>{count}</strong></td><td>{formula_links}</td></tr>'
        for count, pair, formula_links in priority_rows
    )
    relationship_html = "".join(f'<tr><th>{name}</th><td>{body}</td></tr>' for name, body in relationship_rows)
    return f'''<section class="guide-section guide-section--annex guide-section--dui-yao" id="dui-yao-annex"><p class="guide-eyebrow">{'Anexo · Appendix B vigente' if es else 'Annex · Current Appendix B'}</p><h2>{'Las 51 parejas Duì Yào: acciones, prioridad y auditoría' if es else 'The 51 Duì Yào pairs: actions, priority and audit'}</h2><p>{'El temario NCCAOM 2020 ya evaluaba las funciones, indicaciones y aplicación de parejas comunes, pero no publicaba una lista nominal de parejas. El Appendix B de NCBAHM 2026 convierte ese objetivo en una lista explícita de 51 parejas de enfoque.' if es else 'The NCCAOM 2020 outline already tested the functions, indications and application of common pairs, but it did not publish a named pair roster. NCBAHM 2026 Appendix B turns that objective into an explicit 51-pair focus list.'}</p><div class="guide-alert guide-alert--compact"><strong>{'Cómo leer este anexo:' if es else 'How to read this annex:'}</strong> {'la acción resume el trabajo conjunto. La tabla de prioridad cuenta en cuántas de las 63 fichas detalladas del núcleo aparecen ambos componentes; es una señal de rendimiento para estudiar, no una afirmación de que siempre ocupen la misma jerarquía o función.' if es else 'the action summarizes the combined job. The priority table counts how many of the 63 detailed core profiles contain both components; it is a study-yield signal, not a claim that the pair always has the same hierarchy or function.'}</div><h3>{'Siete relaciones tradicionales de compatibilidad' if es else 'Seven traditional compatibility relationships'}</h3><p>{'Estas relaciones explican el marco clásico de combinación. No significan que cada una de las 51 parejas deba memorizarse bajo una sola etiqueta rígida.' if es else 'These relationships explain the classical combination framework. They do not mean that every one of the 51 pairs must be memorized under one rigid label.'}</p><div class="guide-table-wrap"><table class="guide-table guide-dui-relationships"><thead><tr><th>{'Relación' if es else 'Relationship'}</th><th>{'Significado práctico' if es else 'Practical meaning'}</th></tr></thead><tbody>{relationship_html}</tbody></table></div><h3>{'Resumen de todas las acciones' if es else 'Summary of every action'}</h3><ol class="guide-dui-action-list">{''.join(action_items)}</ol><h3>{'Prioridad por repetición dentro de las 63 fichas detalladas' if es else 'Priority by repetition within the 63 detailed profiles'}</h3><p>{'Se muestran las parejas que coaparecen en dos o más fichas detalladas del núcleo. Una pareja con cero o una coaparición sigue siendo examinable porque Appendix B la incluye expresamente.' if es else 'Pairs co-occurring in two or more detailed core profiles are shown. A pair with zero or one co-occurrence remains testable because Appendix B expressly includes it.'}</p><div class="guide-table-wrap"><table class="guide-table guide-dui-priority"><thead><tr><th>Duì Yào</th><th>{'Coapariciones' if es else 'Co-occurrences'}</th><th>{'Fichas detalladas' if es else 'Detailed profiles'}</th></tr></thead><tbody>{priority_html}</tbody></table></div><h3>{'Auditoría de las 63 fichas detalladas contra las 304 hierbas oficiales' if es else 'Audit of the 63 detailed profiles against the 304 official herbs'}</h3><div class="guide-audit-summary"><article><strong>51</strong><span>{'parejas verificadas' if es else 'verified pairs'}</span></article><article><strong>77</strong><span>{'nombres herbales únicos' if es else 'unique herb names'}</span></article><article><strong>0</strong><span>{'hierbas Duì Yào realmente ausentes' if es else 'Duì Yào herbs substantively absent'}</span></article><article><strong>1</strong><span>{'mención histórica fuera de Appendix A' if es else 'historical mention outside Appendix A'}</span></article></div><p><strong>{'Resultado Duì Yào:' if es else 'Duì Yào result:'}</strong> {'los 77 nombres corresponden a Appendix A. La única diferencia literal es Xīn Yí Huā, publicada como Xīn Yí en la lista vigente; no es una sustancia distinta.' if es else 'all 77 names correspond to Appendix A. The only literal difference is Xīn Yí Huā, published as Xīn Yí in the current roster; it is not a different substance.'}</p><p><strong>{'Resultado de fórmulas:' if es else 'Formula result:'}</strong> {'diecisiete etiquetas de ingredientes no coinciden literalmente con un nombre independiente de Appendix A. Dieciséis son formas procesadas, partes, alias o nombres genéricos de hierbas vigentes; Gēng Mǐ corresponde a Jīng Mǐ. La única mención realmente fuera de Appendix A es Zhū Shā, incluida solo al señalar versiones históricas de Tiān Wáng Bǔ Xīn Dān y clasificada como obsoleta en Appendix D. Por tanto, no hay un ingrediente activo moderno de las 63 fichas sin correspondencia en las 304 hierbas.' if es else 'seventeen ingredient labels do not literally match a standalone Appendix A name. Sixteen are processed forms, parts, aliases or generic names for current herbs; Gēng Mǐ corresponds to Jīng Mǐ. The only mention truly outside Appendix A is Zhū Shā, included only when noting historical versions of Tiān Wáng Bǔ Xīn Dān and classified as obsolete in Appendix D. Therefore, no modern active ingredient in the 63 profiles lacks a match among the 304 herbs.'}</p><div class="guide-table-wrap"><table class="guide-table guide-ingredient-audit"><thead><tr><th>{'Etiqueta en la fórmula' if es else 'Formula label'}</th><th>{'Correspondencia vigente' if es else 'Current match'}</th><th>{'Clasificación' if es else 'Classification'}</th><th>{'Dónde aparece' if es else 'Where it appears'}</th></tr></thead><tbody>{''.join(crosswalk_rows)}</tbody></table></div><div class="guide-annex-source"><strong>{'Fuentes:' if es else 'Sources:'}</strong> <a href="{NCCAOM_2020_CH}" target="_blank" rel="noopener">NCCAOM 2020 ↗</a> · <a href="{CH}" target="_blank" rel="noopener">NCBAHM 2026 ↗</a> · <a href="{HERB_PAIR_RESEARCH}" target="_blank" rel="noopener">PubMed · Duì Yào ↗</a> · <a href="{HERB_PAIR_COMPATIBILITY_REVIEW}" target="_blank" rel="noopener">PubMed · compatibility ↗</a> · <a href="#appendix-b-study">{'Volver a la sección de estudio ↑' if es else 'Back to the study section ↑'}</a></div></section>'''


def final_discoveries_summary(es=False):
    old_herbs, current_herbs, old_formulas, current_formulas = historical_rosters()
    pairs = json.loads(DUI_YAO_DATA_FILE.read_text(encoding="utf-8"))
    formula_data = json.loads(FORMULA_GUIDE_DATA_FILE.read_text(encoding="utf-8"))
    formulas = formula_data["formulas"]
    if len(pairs) != 51 or len(formulas) != 63 or len(formula_data["categories"]) != 14:
        raise ValueError("Unexpected current herbology summary counts")

    herb_aliases = {"Xin Yi Hua": "Xin Yi"}
    formula_aliases = {
        "Ge Gen Huang Lian Huang Qin Tang": "Ge Gen Huang Qin Huang Lian Tang",
        "Jin Gui Shen Qi Wan": "Shen Qi Wan",
        "Qing Wen Bai Du San": "Qing Wen Bai Du Yin",
        "Tai Shan Pan Shi San": "Taishan Pan Shi San",
    }
    old_herb_keys = {herb_aliases.get(name, name) for name in old_herbs}
    current_herb_keys = set(current_herbs)
    old_formula_keys = {formula_aliases.get(name, name) for name in old_formulas}
    current_formula_keys = set(current_formulas)
    removed_herbs = [name for name in old_herbs if herb_aliases.get(name, name) not in current_herb_keys]
    added_herbs = [name for name in current_herbs if name not in old_herb_keys]
    removed_formulas = [name for name in old_formulas if formula_aliases.get(name, name) not in current_formula_keys]
    added_formulas = [name for name in current_formulas if name not in old_formula_keys]
    if (len(removed_herbs), len(added_herbs), len(removed_formulas), len(added_formulas)) != (6, 38, 2, 21):
        raise ValueError("Unexpected final historical summary counts")

    pair_suffix = "-es" if es else ""
    repeated_pairs = []
    unique_pair_herbs = set()
    for pair in pairs:
        components = [normalized_pinyin(item.strip()).title() for item in pair["title"].split(" + ")]
        unique_pair_herbs.update(components)
        hits = [formula for formula in formulas if all(formula_has_herb(formula["ingredients"], herb) for herb in components)]
        if len(hits) >= 2:
            repeated_pairs.append((len(hits), pair))
    repeated_pairs.sort(key=lambda item: (-item[0], item[1]["number"]))
    if len(unique_pair_herbs) != 77 or len(repeated_pairs) != 16:
        raise ValueError("Unexpected Duì Yào summary result")

    category_counts = {
        category["key"]: sum(1 for formula in formulas if formula["categoryKey"] == category["key"])
        for category in formula_data["categories"]
    }
    largest_count = max(category_counts.values())
    largest_categories = [
        category["nameEs" if es else "name"]
        for category in formula_data["categories"]
        if category_counts[category["key"]] == largest_count
    ]
    repeated_rows = "".join(
        f'<tr data-final-dui-priority="{pair["number"]}"><th><a href="../comparisons/{pair["slug"]}{pair_suffix}.html">{escape(pair["title"])}</a></th><td><strong>{count}</strong></td></tr>'
        for count, pair in repeated_pairs
    )
    transition_rows = [
        (
            "Hierbas" if es else "Herbs",
            f"{len(old_herbs)}",
            f"{len(current_herbs)}",
            (f"{len(removed_herbs)} salieron · {len(added_herbs)} entraron · cambio neto +{len(current_herbs) - len(old_herbs)}" if es else f"{len(removed_herbs)} removed · {len(added_herbs)} added · net change +{len(current_herbs) - len(old_herbs)}"),
        ),
        (
            "Fórmulas" if es else "Formulas",
            f"{len(old_formulas)}",
            f"{len(current_formulas)}",
            (f"{len(removed_formulas)} salieron · {len(added_formulas)} entraron · cambio neto +{len(current_formulas) - len(old_formulas)}" if es else f"{len(removed_formulas)} removed · {len(added_formulas)} added · net change +{len(current_formulas) - len(old_formulas)}"),
        ),
        (
            "Duì Yào",
            "Sin lista nominal" if es else "No named roster",
            "51",
            "77 nombres herbales únicos" if es else "77 unique herb names",
        ),
    ]
    transition_html = "".join(f'<tr><th>{label}</th><td>{old}</td><td>{current}</td><td>{finding}</td></tr>' for label, old, current, finding in transition_rows)
    study_questions = [
        "¿Qué acción produce la pareja en una oración?" if es else "What action does the pair produce in one sentence?",
        "¿Qué aporta la primera hierba?" if es else "What does the first herb contribute?",
        "¿Qué aporta la segunda?" if es else "What does the second herb contribute?",
        "¿Qué relación de compatibilidad existe entre ellas?" if es else "What compatibility relationship exists between them?",
        "¿Cuál es la pista clínica decisiva?" if es else "What is the decisive clinical clue?",
        "¿En qué fórmula representativa aparece y cuáles son sus precauciones?" if es else "In which representative formula does it appear, and what are its precautions?",
    ]
    examples = [
        ("Ma Huang + Gui Zhi", "refuerzan la sudoración para liberar Viento-Frío exterior por exceso." if es else "reinforce sweating to release an excess Exterior Wind-Cold pattern."),
        ("Ma Huang + Xing Ren", "una difunde y la otra desciende el Qi de Pulmón." if es else "one diffuses and the other descends Lung Qi."),
        ("Gui Zhi + Bai Shao", "armonizan Ying y Wei mediante movimiento y contención." if es else "harmonize Ying and Wei through movement and containment."),
        ("Huang Lian + Rou Gui", "coordinan Fuego y Agua, Corazón y Riñón." if es else "coordinate Fire and Water, Heart and Kidney."),
        ("Ting Li Zi + Da Zao", "combinan drenaje fuerte con protección del Centro." if es else "combine strong drainage with protection of the Middle."),
    ]
    curiosities = [
        "Appendix C creció de 162 fórmulas en 2020 a 181 en 2026: tras normalizar cuatro variantes nominales, entraron 21 y salieron 2." if es else "Appendix C grew from 162 formulas in 2020 to 181 in 2026: after normalizing four name variants, 21 entered and 2 left.",
        "La lista de hierbas creció de 272 a 304: entraron 38 nombres y salieron 6, para un aumento neto de 32." if es else "The herb roster grew from 272 to 304: 38 names entered and 6 left, for a net increase of 32.",
        "Una salida del listado no siempre significa que desapareció la sustancia: varias diferencias son formas procesadas, partes medicinales o variantes nominales." if es else "Removal from the roster does not always mean the substance disappeared: several differences are processed forms, medicinal parts or name variants.",
        "Xin Yi Hua corresponde a Xin Yi; Geng Mi corresponde a Jing Mi." if es else "Xin Yi Hua corresponds to Xin Yi; Geng Mi corresponds to Jing Mi.",
        "De 17 diferencias literales entre ingredientes y Appendix A, 16 se resuelven por alias, parte, procesamiento o nombre genérico." if es else "Of 17 literal differences between formula ingredients and Appendix A, 16 resolve through an alias, part, processing or generic name.",
        "Zhu Sha es la única mención realmente fuera de Appendix A: aparece solo en una nota sobre versiones históricas de Tian Wang Bu Xin Dan y está clasificada como obsoleta en Appendix D." if es else "Zhu Sha is the only mention truly outside Appendix A: it appears only in a note about historical versions of Tian Wang Bu Xin Dan and is classified as obsolete in Appendix D.",
        "No hay ningún ingrediente activo moderno de las 63 fichas sin correspondencia entre las 304 hierbas." if es else "No modern active ingredient in the 63 profiles lacks a match among the 304 herbs.",
        f"Las 63 fichas detalladas se distribuyen en 14 categorías; {(' y '.join(largest_categories) if es else ' and '.join(largest_categories))} son las categorías más grandes, con {largest_count} fórmulas cada una." if es else f"The 63 detailed profiles are distributed across 14 categories; {' and '.join(largest_categories)} are the largest, with {largest_count} formulas each.",
        "La prioridad por repetición es una ayuda pedagógica de AcupunctureTests basada en coaparición; no es un ranking oficial del Board ni asegura que la pareja conserve siempre la misma jerarquía." if es else "Repetition priority is an AcupunctureTests study aid based on co-occurrence; it is not an official Board ranking and does not imply the pair always keeps the same hierarchy.",
    ]
    site_audit = [
        "51 de 51 parejas oficiales, sin números faltantes ni duplicados." if es else "51 of 51 official pairs, with no missing or duplicate numbers.",
        "Cada pareja tiene ficha individual en inglés y español, acción, fórmula representativa y precauciones." if es else "Every pair has an individual English and Spanish card, action, representative formula and precautions.",
        "Las parejas se organizan pedagógicamente en 15 bloques clínicos propios del sitio; Appendix B presenta una lista continua y no establece esas categorías." if es else "The pairs are organized pedagogically into 15 site-defined clinical blocks; Appendix B presents a continuous list and does not establish those categories.",
        "El Board se concentra en las 51 parejas, pero el alcance no excluye que aparezcan otras combinaciones o distractores." if es else "The Board focuses on the 51 pairs, but the scope does not exclude other combinations or distractors.",
    ]
    return f'''<section class="guide-section guide-section--final-summary" id="final-discoveries-summary"><p class="guide-eyebrow">{'Resumen final · Datos curiosos y hallazgos' if es else 'Final summary · Notable facts and findings'}</p><h2>{'Lo más importante de los nuevos listados de hierbas, fórmulas y Duì Yào' if es else 'What matters most in the new herb, formula and Duì Yào rosters'}</h2><div class="guide-audit-summary"><article><strong>304</strong><span>{'hierbas actuales' if es else 'current herbs'}</span></article><article><strong>181</strong><span>{'fórmulas oficiales' if es else 'official formulas'}</span></article><article><strong>51</strong><span>{'parejas Duì Yào' if es else 'Duì Yào pairs'}</span></article><article><strong>63</strong><span>{'fichas detalladas del núcleo' if es else 'detailed core profiles'}</span></article></div><h3>{'Qué es un Duì Yào' if es else 'What a Duì Yào is'}</h3><p>{'Un Duì Yào es una combinación relativamente estable de dos hierbas con una lógica terapéutica concreta. Es el puente entre estudiar hierbas individuales y comprender fórmulas completas. La pareja puede reforzar una misma acción, dividir funciones complementarias, equilibrar movimientos opuestos, proteger el Zheng Qi mientras elimina el patógeno o reducir toxicidad y moderar una acción demasiado intensa.' if es else 'A Duì Yào is a relatively stable two-herb combination with a specific therapeutic logic. It bridges individual-herb study and complete formulas. A pair may reinforce one action, divide complementary functions, balance opposing movements, protect Zheng Qi while eliminating a pathogen, or reduce toxicity and moderate an overly intense action.'}</p><p><a href="{HERB_PAIR_RESEARCH}" target="_blank" rel="noopener">{'Revisión especializada en PubMed' if es else 'Specialist review on PubMed'} ↗</a> · <a href="{HERB_PAIR_COMPATIBILITY_REVIEW}" target="_blank" rel="noopener">{'Revisión sobre compatibilidad herbal' if es else 'Herbal compatibility review'} ↗</a></p><h3>{'El cambio histórico, en una tabla' if es else 'The historical change in one table'}</h3><div class="guide-table-wrap"><table class="guide-table"><thead><tr><th>{'Listado' if es else 'Roster'}</th><th>NCCAOM 2020</th><th>NCBAHM 2026</th><th>{'Hallazgo' if es else 'Finding'}</th></tr></thead><tbody>{transition_html}</tbody></table></div><div class="guide-alert guide-alert--compact"><strong>{'Cambio clave:' if es else 'Key change:'}</strong> {'NCCAOM 2020 exigía conocer las funciones, indicaciones y aplicación de “common herbal pairs”, pero no publicaba una lista nominal. NCBAHM 2026 creó Appendix B con 51 parejas identificadas expresamente y relaciones como Xiang Xu, Xiang Shi, Xiang Wei, Xiang Sha, Xiang Wu y Xiang Fan.' if es else 'NCCAOM 2020 required knowledge of the functions, indications and application of “common herbal pairs,” but did not publish a named roster. NCBAHM 2026 created Appendix B with 51 expressly identified pairs and relationships such as Xiang Xu, Xiang Shi, Xiang Wei, Xiang Sha, Xiang Wu and Xiang Fan.'}</div><h3>{'Cómo estudiar cada pareja: seis preguntas' if es else 'How to study each pair: six questions'}</h3><ol>{''.join(f'<li>{escape(question)}</li>' for question in study_questions)}</ol><div class="guide-final-examples">{''.join(f'<p><strong>{name}:</strong> {body}</p>' for name, body in examples)}</div><h3>{'Auditoría de AcupunctureTests' if es else 'AcupunctureTests audit'}</h3><ul>{''.join(f'<li>{item}</li>' for item in site_audit)}</ul><h3>{'Datos curiosos descubiertos al cruzar los listados' if es else 'Notable facts discovered by cross-checking the rosters'}</h3><ul class="guide-final-findings">{''.join(f'<li>{item}</li>' for item in curiosities)}</ul><h3>{'Duì Yào que merecen prioridad por repetición' if es else 'Duì Yào deserving priority through repetition'}</h3><p>{'Estas son las 16 parejas que aparecen juntas en dos o más de las 63 fichas detalladas del núcleo. Las primeras ocho son las de mayor rendimiento para comenzar el repaso.' if es else 'These are the 16 pairs that occur together in two or more of the 63 detailed core profiles. The first eight provide the highest-yield starting point for review.'}</p><div class="guide-table-wrap"><table class="guide-table guide-final-priority"><thead><tr><th>Duì Yào</th><th>{'Número de fórmulas' if es else 'Number of formulas'}</th></tr></thead><tbody>{repeated_rows}</tbody></table></div><p class="guide-source"><a href="{CH}" target="_blank" rel="noopener">{'Guideline oficial NCBAHM 2026' if es else 'Official NCBAHM 2026 guideline'} ↗</a> · <a href="{NCCAOM_2020_CH}" target="_blank" rel="noopener">{'Guideline anterior NCCAOM 2020' if es else 'Previous NCCAOM 2020 guideline'} ↗</a></p></section>'''


def dui_yao_formula_bridge(es=False):
    pairs = json.loads(DUI_YAO_DATA_FILE.read_text(encoding="utf-8"))
    formulas = json.loads(FORMULA_GUIDE_DATA_FILE.read_text(encoding="utf-8"))["formulas"]
    suffix = "-es" if es else ""
    language_query = "&lang=es" if es else ""

    def formula_link(formula):
        return f'/blocks/myprogress/formulas-study-guide.php?focus={quote(formula["categoryKey"])}{language_query}#formula-{formula["number"]}'

    bridge_rows = []
    pairs_with_current_bridge = 0
    for pair in pairs:
        components = [normalized_pinyin(item.strip()).title() for item in pair["title"].split(" + ")]
        hits = [formula for formula in formulas if all(formula_has_herb(formula["ingredients"], herb) for herb in components)]
        if hits:
            pairs_with_current_bridge += 1
            current_context = ", ".join(f'<a href="{formula_link(formula)}">{escape(formula["name"])}</a>' for formula in hits)
            status = (f"Ambas hierbas aparecen juntas en {len(hits)} ficha{'s' if len(hits) != 1 else ''} detallada{'s' if len(hits) != 1 else ''}; muestra contexto, no equivalencia terapéutica." if es else f"Both herbs occur together in {len(hits)} detailed profile{'s' if len(hits) != 1 else ''}; this shows context, not therapeutic equivalence.")
        else:
            current_context = "—"
            status = "Sin coaparición directa dentro de las 63 fichas detalladas; comprueba por separado el listado oficial de 181 fórmulas." if es else "No direct co-occurrence within the 63 detailed profiles; verify the separate official 181-formula roster."
        if "formula is the pair" in pair["formulas"]["en"]:
            status += " " + ("Excepción documentada: en Èr Zhì Wán la pareja constituye la fórmula." if es else "Documented exception: in Èr Zhì Wán the pair constitutes the formula.")
        bridge_rows.append(
            f'<tr data-dui-formula-bridge="{pair["number"]}"><th><a href="../comparisons/{pair["slug"]}{suffix}.html">{escape(pair["title"])}</a></th><td>{escape(pair["action"]["es" if es else "en"])}</td><td>{escape(pair["formulas"]["es" if es else "en"])}</td><td>{current_context}<small>{status}</small></td></tr>'
        )
    if len(bridge_rows) != 51 or pairs_with_current_bridge != 28:
        raise ValueError("Unexpected Duì Yào/formula bridge counts")

    decision_rows = [
        ("Patrón completo y clásico, con varias ramas" if es else "Complete classical pattern with several branches", "Fórmula completa" if es else "Complete formula", "La arquitectura cubre raíz, ramas, protección y dirección del tratamiento." if es else "Its architecture covers root, branches, protection, and treatment direction."),
        ("Objetivo estrecho y bien definido" if es else "Narrow, well-defined objective", "Duì Yào posible" if es else "Duì Yào may be appropriate", "La pareja puede ser una prescripción breve bajo juicio profesional, si no se necesita una estrategia más amplia." if es else "The pair may be a brief prescription under professional judgment when a broader strategy is unnecessary."),
        ("El patrón encaja con una fórmula, pero falta una función" if es else "The pattern fits a formula but one function is missing", "Fórmula base + Duì Yào" if es else "Base formula + Duì Yào", "Usa la pareja como módulo de modificación, sin perder la estructura principal." if es else "Use the pair as a modification module without losing the main structure."),
        ("Dos síntomas comparten una sola dinámica" if es else "Two symptoms share one dynamic", "Duì Yào como núcleo" if es else "Duì Yào as a core", "La pareja organiza ascenso/descenso, difusión/descenso, ataque/protección u otra relación complementaria." if es else "The pair organizes ascent/descent, diffusion/descent, attack/protection, or another complementary relationship."),
        ("Presentación compleja, deficiencia importante o riesgo" if es else "Complex presentation, major deficiency, or safety risk", "No sustituir automáticamente" if es else "Do not substitute automatically", "Una pareja sola puede omitir armonización, protección, toxicidad, dosis y contraindicaciones cubiertas por la fórmula." if es else "A pair alone may omit harmonization, protection, toxicity, dosage, and contraindication controls supplied by the formula."),
        ("La fórmula clásica contiene exactamente la pareja" if es else "The classical formula consists exactly of the pair", "Pareja = fórmula, según la fuente" if es else "Pair = formula, according to the source", "No es sustitución: son dos nombres para la misma composición; todavía importan proporción, dosis y preparación." if es else "This is not substitution: they are two names for the same composition; ratio, dosage, and preparation still matter."),
    ]
    decision_html = "".join(f'<tr><th>{situation}</th><td><strong>{choice}</strong></td><td>{reason}</td></tr>' for situation, choice, reason in decision_rows)
    question_mix = [
        (4, "Acción conjunta y pista clínica" if es else "Combined action and clinical clue"),
        (3, "Aporte y dirección de cada componente" if es else "Contribution and direction of each component"),
        (4, "Elegir fórmula completa vs. Duì Yào" if es else "Choose a complete formula vs. a Duì Yào"),
        (3, "Pareja como núcleo, modificación o puente hacia una fórmula" if es else "Pair as core, modification, or bridge to a formula"),
        (2, "Relación tradicional de compatibilidad" if es else "Traditional compatibility relationship"),
        (2, "Seguridad, límites y contraindicaciones" if es else "Safety, limits, and contraindications"),
    ]
    if sum(count for count, _ in question_mix) != 18:
        raise ValueError("Duì Yào sub-blueprint must total 18")
    question_rows = "".join(f'<tr data-dui-question-count="{count}"><th>{objective}</th><td><strong>{count}</strong></td></tr>' for count, objective in question_mix)
    return f'''<section class="guide-section guide-section--dui-bridge" id="dui-yao-formula-bridge"><p class="guide-eyebrow">{'Duì Yào vs. fórmula · Decisión clínica' if es else 'Duì Yào vs. formula · Clinical decision'}</p><h2>{'Cuándo escoger una fórmula, cuándo una pareja y cuándo combinarlas' if es else 'When to choose a formula, a pair, or both'}</h2><div class="guide-alert guide-alert--warning"><strong>{'Regla central:' if es else 'Central rule:'}</strong> {'un Duì Yào puede funcionar como núcleo, modificación o prescripción breve para un objetivo estrecho, pero no es un equivalente terapéutico ni sustituto automático de una fórmula completa. Si una fórmula está compuesta exactamente por esas dos hierbas, la pareja y la fórmula coinciden; aun así deben respetarse proporción, dosis, preparación y seguridad.' if es else 'a Duì Yào may function as a core, modification, or brief prescription for a narrow objective, but it is not a therapeutic equivalent or automatic substitute for a complete formula. If a formula consists exactly of those two herbs, the pair and formula coincide; ratio, dosage, preparation, and safety still control.'}</div><div class="guide-table-wrap"><table class="guide-table guide-dui-decision"><thead><tr><th>{'Situación' if es else 'Situation'}</th><th>{'Elección de estudio' if es else 'Study choice'}</th><th>{'Razón' if es else 'Reason'}</th></tr></thead><tbody>{decision_html}</tbody></table></div><h3>{'Cómo repartir las 18 preguntas Duì Yào del modelo hipotético' if es else 'How to distribute the 18 Duì Yào questions in the hypothetical model'}</h3><p>{'La comparación “fórmula vs. pareja” recibe cuatro preguntas propias y también aparece integrada en los casos de núcleo y modificación.' if es else 'The “formula vs. pair” comparison receives four dedicated questions and also appears within core and modification cases.'}</p><div class="guide-table-wrap"><table class="guide-table guide-dui-question-mix"><thead><tr><th>{'Objetivo' if es else 'Objective'}</th><th>{'Preguntas por cada 100 del banco herbal' if es else 'Questions per 100-item herbal bank'}</th></tr></thead><tbody>{question_rows}</tbody><tfoot><tr><th>Total Duì Yào</th><td><strong>18</strong></td></tr></tfoot></table></div><h3>{'Puentes funcionales con fórmulas representativas: no equivalentes automáticos' if es else 'Functional bridges to representative formulas: not automatic equivalents'}</h3><div class="guide-audit-summary"><article><strong>51</strong><span>{'parejas auditadas' if es else 'pairs audited'}</span></article><article><strong>28</strong><span>{'con puente en las 63 fichas detalladas' if es else 'with a bridge in the 63 detailed profiles'}</span></article><article><strong>23</strong><span>{'sin coaparición en las 63 fichas detalladas' if es else 'without co-occurrence in the 63 detailed profiles'}</span></article><article><strong>1</strong><span>{'ejemplo explícito pareja = fórmula' if es else 'explicit pair = formula example'}</span></article></div><p>{'La columna “fórmula representativa” reproduce el contexto pedagógico de la ficha Duì Yào; no afirma que todas esas fórmulas pertenezcan al Appendix C actual. La última columna verifica por separado si ambas hierbas coaparecen en alguna de las 63 fichas detalladas del núcleo.' if es else 'The “representative formula” column reproduces the teaching context from the Duì Yào card; it does not claim that every cited formula belongs to current Appendix C. The final column separately verifies whether both herbs co-occur in any of the 63 detailed core profiles.'}</p><div class="guide-table-wrap"><table class="guide-table guide-dui-bridge-table"><thead><tr><th>Duì Yào</th><th>{'Trabajo estrecho de la pareja' if es else 'Pair’s narrow job'}</th><th>{'Fórmula representativa' if es else 'Representative formula'}</th><th>{'Puente dentro de las 63 fichas detalladas' if es else 'Bridge within the 63 detailed profiles'}</th></tr></thead><tbody>{''.join(bridge_rows)}</tbody></table></div><p class="guide-source-note">{'“Puente” significa contexto compartido o uso como módulo; no significa equivalencia de alcance, dosis, proporción ni seguridad.' if es else '“Bridge” means shared context or modular use; it does not mean equivalent scope, dosage, ratio, or safety.'}</p></section>'''


def dui_yao_formula_modifications(es=False):
    pairs = {pair["number"]: pair for pair in json.loads(DUI_YAO_DATA_FILE.read_text(encoding="utf-8"))}
    suffix = "-es" if es else ""
    _, _, old_formulas, current_formulas = historical_rosters()
    direct = [
        (12, "Liu Wei Di Huang Wan", "Zhi Bai Di Huang Wan", "Añadir Zhī Mǔ + Huáng Bǎi para Fuego vacío más marcado." if es else "Add Zhī Mǔ + Huáng Bǎi for more pronounced deficient Fire.", "Directa" if es else "Direct"),
        (28, "Liu Wei Di Huang Wan", "Qi Ju Di Huang Wan", "Añadir Gǒu Qǐ Zǐ + Jú Huā cuando la deficiencia de Yin de Hígado-Riñón afecta los ojos." if es else "Add Gǒu Qǐ Zǐ + Jú Huā when Liver-Kidney Yin deficiency affects the eyes.", "Directa" if es else "Direct"),
        (34, "Si Wu Tang", "Tao Hong Si Wu Tang", "Añadir Táo Rén + Hóng Huā para convertir nutrición/movilización suave en una estrategia más clara contra estasis." if es else "Add Táo Rén + Hóng Huā to turn nourishment/gentle movement into a clearer Blood-stasis strategy.", "Directa" if es else "Direct"),
        (50, "Gui Zhi Tang", "Gui Zhi Jia Long Gu Mu Li Tang", "Añadir Lóng Gǔ + Mǔ Lì para anclar y contener pérdidas por deficiencia." if es else "Add Lóng Gǔ + Mǔ Lì to anchor and contain deficiency leakage.", "Directa" if es else "Direct"),
        (26, "Si Wu Tang", "Jiao Ai Tang", "Añadir Ài Yè + Ē Jiāo y Gān Cǎo; la pareja calienta/contiene y nutre Sangre para sangrado de Chong-Ren." if es else "Add Ài Yè + Ē Jiāo plus Gān Cǎo; the pair warms/contains and nourishes Blood for Chong-Ren bleeding.", "Pareja + cofactor" if es else "Pair + cofactor"),
        (43, "Liu Wei Di Huang Wan", "Ba Wei Di Huang Wan / Jin Gui Shen Qi Wan lineage", "Añadir Fù Zǐ procesado + Ròu Guì para girar hacia Yang renal y transformación de agua. Algunas fuentes/versiones emplean Guì Zhī en vez de Ròu Guì." if es else "Add processed Fù Zǐ + Ròu Guì to pivot toward Kidney Yang and water transformation. Some sources/versions use Guì Zhī instead of Ròu Guì.", "Dependiente de versión" if es else "Version-dependent"),
    ]
    related = [
        (47, "Huo Xiang Zheng Qi San", "Huo Xiang Zheng Qi San + Pei Lan", "La base ya contiene Huò Xiāng; se añade solo Pèi Lán. La pareja aparece después de la modificación, pero no se añadió completa." if es else "The base already contains Huò Xiāng; only Pèi Lán is added. The pair appears after modification, but the whole pair was not added."),
        (4, "Gui Zhi Tang", "Xiao Jian Zhong Tang", "La base ya contiene Guì Zhī + Bái Sháo; se duplica Bái Sháo y se añade Yí Táng. Es cambio de proporción más adición, no suma de la pareja." if es else "The base already contains Guì Zhī + Bái Sháo; Bái Sháo is doubled and Yí Táng is added. This is a ratio change plus an addition, not addition of the pair."),
        (3, "Ma Huang Tang", "Ma Xing Shi Gan Tang", "Se conserva Má Huáng, se retira Guì Zhī, se añade Shí Gāo y se reequilibra la fórmula. Es transformación estructural, no simple adición del Duì Yào." if es else "Má Huáng is retained, Guì Zhī is removed, Shí Gāo is added, and the formula is rebalanced. This is structural transformation, not simple addition of the Duì Yào."),
    ]
    direct_rows = "".join(
        f'<tr data-dui-modification="{number}"><th><a href="../comparisons/{pairs[number]["slug"]}{suffix}.html">{escape(pairs[number]["title"])}</a></th><td>{base}</td><td>{result}</td><td>{change}</td><td><strong>{level}</strong></td></tr>'
        for number, base, result, change, level in direct
    )
    related_rows = "".join(
        f'<tr data-dui-structural-change="{number}"><th><a href="../comparisons/{pairs[number]["slug"]}{suffix}.html">{escape(pairs[number]["title"])}</a></th><td>{base}</td><td>{result}</td><td>{change}</td></tr>'
        for number, base, result, change in related
    )
    board_scope = [
        ("Liu Wei Di Huang Wan", 12, "Zhi Bai Di Huang Wan"),
        ("Liu Wei Di Huang Wan", 28, "Qi Ju Di Huang Wan"),
        ("Si Wu Tang", 34, "Tao Hong Si Wu Tang"),
        ("Gui Zhi Tang", 50, "Gui Zhi Jia Long Gu Mu Li Tang"),
    ]
    if not all(base in current_formulas and variant in current_formulas for base, _, variant in board_scope):
        raise ValueError("Unexpected current Board status in direct Duì Yào bridges")
    scope_rows = "".join(
        f'<tr data-board-formula-pair-question="{number}"><th>{escape(base)}</th><td><a href="../comparisons/{pairs[number]["slug"]}{suffix}.html">{escape(pairs[number]["title"])}</a></td><td>{escape(variant)}</td><td><strong>{"Base, pareja y variante pertenecen al alcance vigente de 2026." if es else "Base, pair, and variant all belong to the current 2026 scope."}</strong>{(" También aparecía en el listado NCCAOM 2020." if es else " It also appeared in the NCCAOM 2020 roster.") if variant in old_formulas else (" La variante se incorporó al Appendix C de 2026." if es else " The variant was added to the 2026 Appendix C.")}</td></tr>'
        for base, number, variant in board_scope
    )
    return f'''<section class="guide-section guide-section--dui-modifications" id="dui-yao-formula-modifications"><p class="guide-eyebrow">{'Investigación · Duì Yào como módulo de modificación' if es else 'Research · Duì Yào as a modification module'}</p><h2>{'Parejas que se añaden a una fórmula base para producir una variante' if es else 'Pairs added to a base formula to produce a variant'}</h2><p>{'Estos son los casos más claros encontrados al cruzar las 51 parejas con familias y composiciones clásicas. “Directa” significa que la pareja completa se añade a la base; no significa que el Duì Yào por sí solo sea equivalente a la fórmula resultante.' if es else 'These are the clearest cases found by cross-checking the 51 pairs with classical families and compositions. “Direct” means the complete pair is added to the base; it does not mean the Duì Yào alone is equivalent to the resulting formula.'}</p><div class="guide-table-wrap"><table class="guide-table guide-dui-modification-table"><thead><tr><th>Duì Yào</th><th>{'Fórmula base' if es else 'Base formula'}</th><th>{'Variante resultante' if es else 'Resulting variant'}</th><th>{'Qué cambia' if es else 'What changes'}</th><th>{'Fuerza del vínculo' if es else 'Strength of link'}</th></tr></thead><tbody>{direct_rows}</tbody></table></div><div class="guide-alert guide-alert--compact"><strong>{'¿Sabías que…?' if es else 'Did you know?'}</strong> {'Las cuatro conversiones directas permiten elaborar preguntas en ambas direcciones: fórmula base + pareja → variante, o variante → fórmula base + pareja distintiva. También producen preguntas de composición, rama clínica y discriminación frente a transformaciones estructurales.' if es else 'The four direct conversions support questions in both directions: base formula + pair → variant, or variant → base formula + distinguishing pair. They also support composition, clinical-branch, and structural-transformation discrimination questions.'}</div><h3>{'Pregunta especial: ¿qué fórmula del Board + cuál Duì Yào oficial forman la variante?' if es else 'Special question: which Board formula + which official Duì Yào form the variant?'}</h3><div class="guide-table-wrap"><table class="guide-table"><thead><tr><th>{'Fórmula base vigente' if es else 'Current base formula'}</th><th>{'Duì Yào vigente' if es else 'Current Duì Yào'}</th><th>{'Variante clásica resultante' if es else 'Resulting classical variant'}</th><th>{'Alcance del examen' if es else 'Exam scope'}</th></tr></thead><tbody>{scope_rows}</tbody></table></div><div class="guide-alert guide-alert--warning"><strong>{'Distinción esencial:' if es else 'Essential distinction:'}</strong> {'las tres bases, las cuatro parejas y las cuatro variantes resultantes pertenecen al alcance vigente de 2026. Estas conversiones pueden evaluarse directamente como relaciones entre fórmulas oficiales.' if es else 'the three bases, all four pairs, and all four resulting variants belong to the current 2026 scope. These conversions can be tested directly as relationships among official formulas.'}</div><div class="guide-alert guide-alert--compact"><strong>{'Respuesta corta:' if es else 'Short answer:'}</strong> {'las conversiones más limpias para preguntas de examen son Liu Wei → Zhi Bai, Liu Wei → Qi Ju, Si Wu → Tao Hong Si Wu y Gui Zhi Tang → Gui Zhi Jia Long Gu Mu Li Tang. Jiao Ai Tang necesita además Gan Cao; el caso Fu Zi + Rou Gui exige reconocer la variante textual.' if es else 'the cleanest exam conversions are Liu Wei → Zhi Bai, Liu Wei → Qi Ju, Si Wu → Tao Hong Si Wu, and Gui Zhi Tang → Gui Zhi Jia Long Gu Mu Li Tang. Jiao Ai Tang also requires Gan Cao; the Fu Zi + Rou Gui case requires recognizing the textual variant.'}</div><h3>{'Casos relacionados que no deben redactarse como “añadir la pareja completa”' if es else 'Related cases that must not be written as “add the complete pair”'}</h3><div class="guide-table-wrap"><table class="guide-table"><thead><tr><th>Duì Yào</th><th>{'Base' if es else 'Base'}</th><th>{'Resultado' if es else 'Result'}</th><th>{'Por qué no es una adición directa' if es else 'Why this is not direct addition'}</th></tr></thead><tbody>{related_rows}</tbody></table></div><h3>{'Modelos de pregunta recomendados' if es else 'Recommended question models'}</h3><ol><li>{'Dada la fórmula base y una nueva rama clínica, elegir qué Duì Yào añadir.' if es else 'Given a base formula and a new clinical branch, choose which Duì Yào to add.'}</li><li>{'Dada la variante resultante, identificar la pareja que la distingue de la base.' if es else 'Given the resulting variant, identify the pair that distinguishes it from the base.'}</li><li>{'Distinguir una adición directa de un cambio de proporción, sustitución o reconstrucción de la fórmula.' if es else 'Distinguish direct addition from a ratio change, substitution, or formula reconstruction.'}</li><li>{'Reconocer cuándo una pareja aporta solo una rama y no cubre la estrategia completa de la variante.' if es else 'Recognize when a pair supplies only one branch and does not cover the variant’s full strategy.'}</li></ol><p class="guide-source-note">{'Tabla pedagógica basada en composiciones y familias clásicas. Las ediciones y linajes pueden variar; para práctica clínica deben verificarse fuente, procesamiento, proporción, dosis y seguridad.' if es else 'Teaching table based on classical compositions and families. Editions and lineages may vary; clinical practice requires verification of source, processing, ratio, dosage, and safety.'}</p></section>'''


def new_student_site_reference(es=False):
    suffix = "-es" if es else ""
    steps = [
        ("1", "Confirmar el alcance oficial" if es else "Confirm official scope", "El Board Guideline separa las 304 hierbas, 51 parejas y 181 fórmulas vigentes de los listados históricos; además identifica por separado las 63 fichas detalladas del núcleo." if es else "The Board Guideline separates the current 304 herbs, 51 pairs, and 181 formulas; it separately identifies the 63 detailed profiles from historical rosters."),
        ("2", "Aprender el trabajo de la pareja" if es else "Learn the pair’s job", "El índice Duì Yào agrupa las parejas en 15 bloques pedagógicos; cada ficha explica acción, división del trabajo, pista, fórmula y seguridad." if es else "The Duì Yào index groups pairs into 15 teaching blocks; every card explains action, division of labor, clue, formula, and safety."),
        ("3", "Distinguir pareja de fórmula" if es else "Distinguish pair from formula", "La pareja resuelve una relación estrecha; la fórmula organiza una estrategia completa. El sitio enseña cuándo escoger cada una y cuándo combinarlas." if es else "A pair resolves a narrow relationship; a formula organizes a complete strategy. The site teaches when to choose each and when to combine them."),
        ("4", "Practicar puentes y variantes" if es else "Practice bridges and variants", "Los 10 puentes muestran adición directa, casos calificados, reconstrucción estructural e identidad entre pareja y fórmula." if es else "The 10 bridges demonstrate direct addition, qualified cases, structural reconstruction, and identity between pair and formula."),
        ("5", "Convertirlo en preguntas" if es else "Turn it into questions", "El peso Duì Yào del 18% es una hipótesis editorial para balancear práctica; no es un porcentaje publicado por NCBAHM." if es else "The 18% Duì Yào weight is an editorial hypothesis for balancing practice; it is not an NCBAHM-published percentage."),
    ]
    cards = "".join(f'<article class="guide-card"><span class="guide-status">{number}</span><h3>{title}</h3><p>{body}</p></article>' for number, title, body in steps)
    bridge_rows = [
        ("4", "Adiciones directas" if es else "Direct additions", "Liu Wei → Zhi Bai; Liu Wei → Qi Ju; Si Wu → Tao Hong Si Wu; Gui Zhi Tang → Gui Zhi Jia Long Gu Mu Li Tang."),
        ("2", "Casos calificados" if es else "Qualified cases", "Ai Ye + E Jiao requiere Gan Cao para Jiao Ai Tang; Fu Zi + Rou Gui depende de la versión textual." if es else "Ai Ye + E Jiao requires Gan Cao for Jiao Ai Tang; Fu Zi + Rou Gui depends on the textual version."),
        ("3", "Transformaciones estructurales" if es else "Structural transformations", "Ma Huang + Shi Gao; Gui Zhi + Bai Shao; Huo Xiang + Pei Lan aparecen mediante retiro, cambio de proporción o adición de solo la compañera faltante." if es else "Ma Huang + Shi Gao; Gui Zhi + Bai Shao; Huo Xiang + Pei Lan arise through removal, ratio change, or addition of only the missing partner."),
        ("1", "Pareja = fórmula" if es else "Pair = formula", "Han Lian Cao + Nu Zhen Zi constituye Er Zhi Wan; proporción, dosis y preparación todavía importan." if es else "Han Lian Cao + Nu Zhen Zi constitutes Er Zhi Wan; ratio, dose, and preparation still matter."),
    ]
    rows = "".join(f'<tr><th><strong>{count}</strong></th><td>{kind}</td><td>{example}</td></tr>' for count, kind, example in bridge_rows)
    return f'''<section class="guide-section" id="new-student-site-reference" data-student-onboarding><p class="guide-eyebrow">{'Referencia para estudiantes nuevos' if es else 'New-student reference'}</p><h2>{'Cómo usar AcupunctureTests para estudiar Duì Yào y fórmulas' if es else 'How to use AcupunctureTests to study Duì Yào and formulas'}</h2><p>{'Esta es la ruta de lectura que conecta los datos del Board con las herramientas pedagógicas del sitio. Lo oficial determina qué estudiar; las categorías, puentes y pesos hipotéticos explican cómo organizarlo y practicarlo.' if es else 'This reading route connects Board data with the site’s teaching tools. Official material determines what to study; categories, bridges, and hypothetical weights explain how to organize and practice it.'}</p><div class="guide-grid guide-grid--three">{cards}</div><h3>{'Referencia rápida de los 10 puentes documentados' if es else 'Quick reference to the 10 documented bridges'}</h3><div class="guide-table-wrap"><table class="guide-table"><thead><tr><th>{'Cantidad' if es else 'Count'}</th><th>{'Tipo de vínculo' if es else 'Link type'}</th><th>{'Ejemplos y límite' if es else 'Examples and boundary'}</th></tr></thead><tbody>{rows}</tbody></table></div><div class="guide-alert guide-alert--warning"><strong>{'Cómo leer 28 + 23:' if es else 'How to read 28 + 23:'}</strong> {'28 parejas coaparecen en al menos una de las 63 fichas detalladas del núcleo y 23 no. Esto describe cobertura del listado vigente, no eficacia ni equivalencia terapéutica. Las 51 siguen siendo materia del Appendix B.' if es else '28 pairs co-occur in at least one of the 63 detailed core profiles and 23 do not. This describes current-roster coverage, not efficacy or therapeutic equivalence. All 51 remain Appendix B material.'}</div><div class="guide-actions guide-actions--section"><a class="guide-button" href="../comparisons/dui-yao{suffix}.html#how-to-use-dui-yao-module">{'Abrir orientación del módulo Duì Yào' if es else 'Open the Duì Yào module orientation'} →</a><a class="guide-button guide-button--outline" href="#dui-yao-formula-modifications">{'Ver anexo de variantes' if es else 'View the variants annex'} ↓</a></div></section>'''


def hypothetical_exam_model(es=False):
    weights = [
        ("formula_differentiation", 28, "Selección y diferenciación de fórmulas" if es else "Formula selection and differentiation", "Patrón → fórmula, comparación entre opciones cercanas y pista decisiva." if es else "Pattern → formula, comparison of close options, and decisive clue.", "Alta" if es else "High"),
        ("single_herb", 24, "Hierbas individuales" if es else "Individual herbs", "Categoría, acciones, indicaciones, naturaleza, canales y distinciones clínicas." if es else "Category, actions, indications, nature, channels, and clinical distinctions.", "Alta" if es else "High"),
        ("dui_yao", 18, "Duì Yào y compatibilidad" if es else "Duì Yào and compatibility", "Acción conjunta, aporte de cada hierba y relación tradicional. El concepto es clásico; lo novedoso es que el Board estadounidense haya creado en 2026 un Appendix nominal de 51 parejas. No es, por definición, una fórmula comercial: es una unidad de compatibilidad que puede dispensarse con hierbas o gránulos e integrarse en una prescripción personalizada. Hipótesis: su formato modular, más fácil de memorizar y ajustar a una presentación individual, marca un giro curricular hacia el razonamiento clínico relacional en Estados Unidos; complementa, no reemplaza, las fórmulas clásicas." if es else "Combined action, each herb’s contribution, and traditional relationship. The concept is classical; what is new is the U.S. Board’s creation in 2026 of a named 51-pair appendix. By definition it is not a commercial formula: it is a compatibility unit that may be dispensed with herbs or granules and incorporated into a customized prescription. Hypothesis: its modular format—easier to memorize and adapt to an individual presentation—marks a U.S. curricular shift toward relational clinical reasoning; it complements rather than replaces classical formulas.", "Alta en la dirección; baja en el % exacto" if es else "High for direction; low for the exact %"),
        ("safety_dispensing", 15, "Seguridad y dispensación" if es else "Safety and dispensing", "Dosis, preparación, toxicidad, embarazo, interacciones, obsoletas y sustitutos." if es else "Dosage, preparation, toxicity, pregnancy, interactions, obsolete substances, and substitutes.", "Alta" if es else "High"),
        ("formula_composition", 10, "Composición y modificación de fórmulas" if es else "Formula composition and modification", "Jun–Chen–Zuo–Shi, ingrediente estratégico y modificación según presentación." if es else "Jun–Chen–Zuo–Shi, strategic ingredient, and presentation-based modification.", "Media-alta" if es else "Medium-high"),
        ("scope_aliases", 5, "Límites del listado, alias y sustituciones" if es else "Roster boundaries, aliases, and substitutions", "Reconocer variantes nominales y evitar elegir como activa una opción histórica u obsoleta." if es else "Recognize name variants and avoid selecting a historical or obsolete option as active.", "Media" if es else "Medium"),
    ]
    if sum(weight for _, weight, _, _, _ in weights) != 100:
        raise ValueError("Hypothetical question weights must total 100")
    rows = "".join(
        f'<tr data-question-class="{code}" data-hypothetical-weight="{weight}"><th>{label}<small><code>{code}</code></small></th><td><strong>{weight}%</strong></td><td>{application}</td><td>{confidence}</td></tr>'
        for code, weight, label, application, confidence in weights
    )
    return f'''<section class="guide-section guide-section--hypothesis" id="hypothetical-exam-model"><p class="guide-eyebrow">{'Hipótesis editorial · Elaboración de exámenes' if es else 'Editorial hypothesis · Exam authoring'}</p><h2>{'Qué parece señalar el nuevo guideline y cómo convertirlo en pesos de práctica' if es else 'What the new guideline appears to signal and how to convert it into practice weights'}</h2><div class="guide-inference"><p><strong>{'Interpretación:' if es else 'Interpretation:'}</strong> {'Appendix C aumentó de 162 a 181 fórmulas. Las 63 fichas detalladas representan la cobertura profunda actual de la plataforma, no una reducción del alcance oficial. El aumento de 272 a 304 hierbas amplía la precisión de materia médica. La primera lista nominal de 51 Duì Yào hace mucho más probable —aunque no cuantificable oficialmente— que se evalúe razonamiento relacional y no solo recuerdo aislado.' if es else 'Appendix C expanded from 162 to 181 formulas. The 63 detailed profiles represent the platform current deep coverage, not a reduction in official scope. The increase from 272 to 304 herbs broadens materia-medica precision. The first named list of 51 Duì Yào makes relational reasoning—not isolated recall—much more likely, although the increase cannot be quantified officially.'}</p></div><div class="guide-alert guide-alert--warning"><strong>{'Importante:' if es else 'Important:'}</strong> {'estos porcentajes no fueron publicados por NCBAHM y no predicen la composición real del examen. Son una hipótesis editorial de AcupunctureTests para balancear bancos y simulacros. Cada pregunta se cuenta una sola vez por su objetivo principal; los objetivos secundarios se etiquetan para análisis, pero no duplican el peso.' if es else 'these percentages were not published by NCBAHM and do not predict the real exam composition. They are an AcupunctureTests editorial hypothesis for balancing question banks and mock exams. Each item is counted once under its primary objective; secondary objectives are tagged for analysis but do not duplicate its weight.'}</div><div class="guide-table-wrap"><table class="guide-table guide-hypothetical-weights"><thead><tr><th>{'Objetivo principal de la pregunta' if es else 'Primary question objective'}</th><th>{'Peso hipotético' if es else 'Hypothetical weight'}</th><th>{'Aplicación al banco' if es else 'Question-bank application'}</th><th>{'Confianza de la inferencia' if es else 'Inference confidence'}</th></tr></thead><tbody>{rows}</tbody><tfoot><tr><th>{'Total editorial' if es else 'Editorial total'}</th><td><strong>100%</strong></td><td colspan="2">{'Revisar después de cada ciclo usando el desempeño real de los estudiantes; nunca presentarlo como peso oficial.' if es else 'Review after each cycle using actual student performance; never present it as an official weight.'}</td></tr></tfoot></table></div><p class="guide-source-note">{'Lectura práctica: en un simulacro herbal de 100 preguntas, la tabla equivale a 28 + 24 + 18 + 15 + 10 + 5 preguntas. En formularios de otro tamaño se aplicará redondeo por mayor residuo para conservar exactamente el total.' if es else 'Practical reading: in a 100-question herbal mock, the table corresponds to 28 + 24 + 18 + 15 + 10 + 5 questions. For other form sizes, largest-remainder rounding will preserve the exact total.'}</p></section>'''


def herb_properties_weight_section(es=False):
    rows = [
        (
            "temperature",
            "Temperatura / naturaleza" if es else "Temperature / nature",
            "Prioridad práctica muy alta" if es else "Very high practical priority",
            "Evita escoger una hierba fría para un patrón frío o una caliente para un patrón de Calor; también separa opciones clínicamente cercanas." if es else "Prevents choosing a cold herb for a Cold pattern or a hot herb for a Heat pattern; it also separates clinically close options.",
        ),
        (
            "channels",
            "Canales o meridianos" if es else "Channels or meridians",
            "Prioridad práctica alta" if es else "High practical priority",
            "Localiza la afinidad orgánica y regional y ayuda a distinguir hierbas con acciones semejantes." if es else "Localizes organ and regional affinity and helps distinguish herbs with similar actions.",
        ),
        (
            "flavor",
            "Sabor" if es else "Flavor",
            "Prioridad práctica media-alta" if es else "Medium-high practical priority",
            "Explica la tendencia funcional —dispersar, drenar o secar, tonificar o armonizar, contener, ablandar o filtrar—, pero rara vez decide por sí solo." if es else "Explains functional tendency—dispersing, draining or drying, tonifying or harmonizing, containing, softening, or leaching—but rarely decides the answer by itself.",
        ),
    ]
    row_markup = "".join(
        f'<tr data-property-priority="{key}"><th>{label}</th><td><strong>{priority}</strong></td><td>{application}</td></tr>'
        for key, label, priority, application in rows
    )
    official = (
        "NCBAHM exige conocer las propiedades de las hierbas del Appendix A, pero no publica un porcentaje separado para temperatura, sabor o canales. Que no exista un subpeso oficial no significa que sean opcionales."
        if es else
        "NCBAHM requires knowledge of the properties of Appendix A herbs, but it does not publish separate percentages for temperature, flavor, or channels. The absence of an official subweight does not make them optional."
    )
    editorial = (
        "El 24% de hierbas individuales es una hipótesis editorial de AcupunctureTests para balancear simulacros. Incluye categoría, acciones, indicaciones, naturaleza, canales y diferenciación clínica; no debe dividirse en porcentajes independientes ni presentarse como peso oficial del examen."
        if es else
        "The 24% individual-herb allocation is an AcupunctureTests editorial hypothesis for balancing mock exams. It includes category, actions, indications, nature, channels, and clinical differentiation; it must not be split into independent percentages or presented as an official exam weight."
    )
    signature = (
        "categoría → temperatura → sabor principal → canales principales → acción distintiva → indicación decisiva."
        if es else
        "category → temperature → primary flavor → primary channels → distinguishing action → decisive indication."
    )
    return f'''<article class="guide-reading-step" id="herb-properties-weight"><div class="guide-reading-step__number">4A</div><div><p class="guide-eyebrow">{'Appendix A · Peso y prioridad' if es else 'Appendix A · Weight and priority'}</p><h2>{'Cuánto pesan temperatura, sabor y canales' if es else 'How much temperature, flavor, and channels matter'}</h2><div class="guide-alert guide-alert--compact"><strong>{'Respuesta oficial:' if es else 'Official answer:'}</strong> {official}</div><div class="guide-table-wrap"><table class="guide-table"><thead><tr><th>{'Propiedad' if es else 'Property'}</th><th>{'Prioridad de estudio' if es else 'Study priority'}</th><th>{'Por qué importa en una pregunta' if es else 'Why it matters in a question'}</th></tr></thead><tbody>{row_markup}</tbody></table></div><p><strong>{'Firma recomendada para cada hierba:' if es else 'Recommended signature for each herb:'}</strong> {signature}</p><div class="guide-alert guide-alert--warning"><strong>{'Límite del 24%:' if es else '24% boundary:'}</strong> {editorial}</div></div></article>'''

def board_page(board, lang):
    es = lang == "es"
    title = board["es" if es else "en"]
    summary = board["summary_es" if es else "summary_en"]
    scope = board["scope_es" if es else "scope_en"]
    study = board["study_es" if es else "study_en"]
    suffix = "-es" if es else ""
    alternate = f'{board["slug"]}{"" if es else "-es"}.html'
    source = board.get("source", OFFICIAL)
    herbal = board["slug"] == "chinese-herbal-medicine"
    html = top(lang, title, alternate, css_version="20261101-3" if herbal else "20261027-7")
    html += f'''<section class="guide-hero"><p class="guide-eyebrow">{'Guía de Board' if es else 'Board guideline'}</p><h1>{escape(title)}</h1><p>{escape(summary)}</p></section>'''
    if herbal:
        html += f'''<div class="guide-alert"><strong>{'Documento de lectura continua.' if es else 'Continuous reading document.'}</strong> {'Lee de arriba hacia abajo sin cambiar de página. Esta guía contiene el orden de estudio, los cambios principales, las prioridades y el control final.' if es else 'Read from top to bottom without changing pages. This guide contains the study order, key changes, priorities and final review.'}</div>'''
        html += f'''<nav class="guide-quick-access" aria-label="{'Accesos directos' if es else 'Quick access'}"><strong>{'Acceso directo:' if es else 'Quick access:'}</strong><a class="guide-button guide-button--primary" href="#guide-formula-roster-title">{'Ver las 181 fórmulas dentro de esta guía' if es else 'View all 181 formulas inside this guide'}</a><a class="guide-button guide-button--outline" href="formulas-appendix-c-2026-preview.html">{'Abrir índice de fórmulas con buscador' if es else 'Open searchable formula index'}</a><a class="guide-button guide-button--outline" href="#appendix-a-current-roster">{'Ver las 304 hierbas' if es else 'View all 304 herbs'}</a></nav>'''
    else:
        html += f'''<div class="guide-alert"><strong>{'Límite de la guía:' if es else 'Guide boundary:'}</strong> {'Este mapa organiza el estudio; no reemplaza el manual del candidato, el temario oficial ni las reglas de elegibilidad.' if es else 'This map organizes study; it does not replace the candidate handbook, official content outline or eligibility rules.'}</div>'''
        cards = [
            ("1", "Alcance principal" if es else "Core scope", scope),
            ("2", "Método de estudio" if es else "Study method", [study]),
            ("3", "Auditoría antes del examen" if es else "Pre-exam audit", ["Confirmar versión y fecha del temario oficial." if es else "Confirm the official outline version and effective date.", "Relacionar cada recurso del curso con una competencia del temario." if es else "Map every course resource to an outline competency.", "Separar contenido oficial, material educativo y reglas clínicas o legales." if es else "Separate official scope, educational material, and clinical or legal rules."]),
        ]
    if herbal:
        transition_rows = [
            ("Organización" if es else "Organization", "Hierbas y fórmulas eran los dos listados centrales de preparación." if es else "Herbs and formulas were the two central preparation lists.", "El alcance se organiza en A: hierbas, B: 51 Duì Yào, C: fórmulas y D: sustancias obsoletas y sustituciones." if es else "The scope is organized into A: herbs, B: 51 Duì Yào, C: formulas and D: obsolete substances and substitutions.", "Estudia en el orden A → B → C e integra D desde el comienzo." if es else "Study in A → B → C order and integrate D from the beginning."),
            ("Hierbas" if es else "Herbs", "Nombre, categoría, propiedades, acciones e indicaciones." if es else "Name, category, properties, actions and indications.", "Conserva esa base y añade identidad/parte, dosis, preparación, seguridad y selección dentro de casos." if es else "Keep that foundation and add identity/part, dosage, preparation, safety and case-based selection.", "No vuelvas a empezar; amplía cada ficha hasta poder tomar una decisión." if es else "Do not restart; expand each herb profile until you can make a decision."),
            ("Duì Yào", "Las parejas podían aparecer dentro de materia médica o al estudiar ingredientes de fórmulas." if es else "Pairs could appear within materia medica or while studying formula ingredients.", "Appendix B las convierte en un bloque explícito de 51 parejas." if es else "Appendix B makes them an explicit block of 51 pairs.", "Aprende primero las hierbas por separado y después el trabajo conjunto." if es else "Learn the herbs separately first, then their combined work."),
            ("Fórmulas" if es else "Formulas", "Reconocimiento del nombre, ingredientes, acción e indicación principal." if es else "Recognition of name, ingredients, action and primary indication.", "Mayor aplicación de jerarquía, estrategia, modificación, comparación y seguridad." if es else "Greater application of hierarchy, strategy, modification, comparison and safety.", "Practica por qué una fórmula corresponde y otra cercana no." if es else "Practice why one formula fits and a similar one does not."),
            ("Sustancias obsoletas" if es else "Obsolete substances", "Podían aparecer en listas antiguas o en la composición clásica de una fórmula." if es else "They could appear in older lists or in a formula’s classical composition.", "Appendix D separa nueve sustancias obsoletas y sus sustituciones." if es else "Appendix D separates nine obsolete substances and their substitutions.", "Reconócelas para seguridad; no las elijas como tratamientos activos." if es else "Recognize them for safety; do not select them as active treatments."),
        ]
        appendix_rows = [
            ("A", "Hierbas individuales" if es else "Individual herbs", "Identidad, propiedades, acciones, indicaciones, dosis, preparación y seguridad." if es else "Identity, properties, actions, indications, dosage, preparation and safety.", "Distinguir la mejor opción dentro de un caso." if es else "Distinguish the best option within a case."),
            ("B", "51 parejas Duì Yào" if es else "51 Duì Yào pairs", "Trabajo conjunto, aporte de cada hierba, fórmula representativa y seguridad." if es else "Combined job, each herb’s contribution, representative formula and safety.", "Explicar por qué la combinación funciona mejor que cada componente aislado." if es else "Explain why the combination works better than either component alone."),
            ("C", "Fórmulas" if es else "Formulas", "Ingredientes, jerarquía, patrón, modificaciones, comparaciones y contraindicaciones." if es else "Ingredients, hierarchy, pattern, modifications, comparisons and contraindications.", "Seleccionar y modificar desde el patrón del paciente." if es else "Select and modify from the patient’s pattern."),
            ("D", "Obsoletas y sustituciones" if es else "Obsolete substances and substitutions", "Estatus, riesgo o protección y alternativa apropiada." if es else "Status, safety or protection issue and appropriate alternative.", "Evitar una respuesta terapéutica insegura u obsoleta." if es else "Avoid an unsafe or obsolete therapeutic answer."),
        ]
        priority_rows = [
            ("Hierba" if es else "Herb", "Identidad, pista distintiva, dosis y seguridad." if es else "Identity, distinguishing clue, dosage and safety.", "Solo nombre y traducción." if es else "Name and translation alone."),
            ("Duì Yào", "Trabajo conjunto y función diferente de cada componente." if es else "Combined job and the different function of each component.", "Memorizar dos nombres sin explicar la relación." if es else "Memorizing two names without explaining the relationship."),
            ("Fórmula" if es else "Formula", "Patrón, jerarquía, comparación, modificación y contraindicación." if es else "Pattern, hierarchy, comparison, modification and contraindication.", "Lista aislada de ingredientes." if es else "An isolated ingredient list."),
            ("Seguridad" if es else "Safety", "Procesamiento, interacciones, embarazo, toxicidad, obsoletas y sustitutos." if es else "Processing, interactions, pregnancy, toxicity, obsolete substances and substitutes.", "Dejarla como repaso separado al final." if es else "Leaving it as a separate review at the end."),
        ]
        module_rows = [
            ("Single Herbs Comparisons", "Después de revisar el mapa A–D." if es else "After reviewing the A–D map.", "Elige una categoría y lee sus comparaciones en secuencia; escribe la pista que separa cada par." if es else "Choose one category and read its comparisons in sequence; write the clue that separates each pair.", "Tres reglas de contraste que puedas explicar sin mirar." if es else "Three contrast rules you can explain without looking."),
            ("Secondary Actions · Herb Families & Parts · Name Traps", "Cuando ya reconoces la acción principal de la hierba." if es else "Once you recognize the herb’s primary action.", "Comprueba acciones secundarias, identidad, parte medicinal y nombres que se confunden." if es else "Check secondary actions, identity, medicinal part and confusing names.", "Una lista corta de errores que no debes repetir." if es else "A short list of errors not to repeat."),
            ("Duì Yào Pairs", "Después de dominar las hierbas que forman la pareja." if es else "After mastering the herbs that form the pair.", "Aplica los seis pasos: trabajo conjunto, aporte de cada hierba, fórmula, pista positiva, pista negativa y seguridad." if es else "Apply the six steps: combined job, each herb’s contribution, formula, positive clue, negative clue and safety.", "Una explicación completa de la pareja, no solo dos nombres." if es else "A complete explanation of the pair, not just two names."),
            ("Formula Composition", "Antes de intentar comparar muchas fórmulas." if es else "Before attempting to compare many formulas.", "Identifica patrón, estrategia, jerarquía Jun–Chen–Zuo–Shi, dosis relativa y preparación." if es else "Identify pattern, strategy, Jun–Chen–Zuo–Shi hierarchy, relative dosage and preparation.", "Un esquema que explique por qué cada ingrediente está presente." if es else "An outline explaining why each ingredient is present."),
            ("Formula Comparisons · Formula Families · Most Important Formulas", "Después de comprender la arquitectura de la fórmula." if es else "After understanding formula architecture.", "Compara fórmulas vecinas y localiza el ingrediente, patrón o contraindicación que cambia la respuesta." if es else "Compare neighboring formulas and locate the ingredient, pattern or contraindication that changes the answer.", "Una regla de decisión entre dos fórmulas cercanas." if es else "A decision rule between two similar formulas."),
            ("Safety Library · Herbal Incompatibilities", "Durante todos los bloques, no solamente al final." if es else "During every block, not only at the end.", "Añade a cada ficha una línea sobre dosis, procesamiento, embarazo, interacción, toxicidad u obsolescencia." if es else "Add one line to every note about dosage, processing, pregnancy, interaction, toxicity or obsolete status.", "Una decisión de seguridad: usar, modificar, evitar, sustituir o derivar." if es else "One safety decision: use, modify, avoid, substitute or refer."),
            ("Continuous Reader", "Cuando entres a cualquier grupo de tarjetas." if es else "Whenever you enter a card group.", "Lee hacia adelante dentro del mismo grupo y conserva el número de capítulo para no perder tu posición." if es else "Read forward within the same group and keep the chapter number so you do not lose your position.", "Un grupo terminado en orden, sin saltar entre páginas." if es else "One group completed in order without jumping between pages."),
        ]
        html += f'''<section class="guide-reading-flow" aria-label="{'Lectura guiada del módulo' if es else 'Guided module reading'}">'''
        html += f'''<article class="guide-reading-step"><div class="guide-reading-step__number">1</div><div><p class="guide-eyebrow">{'Punto de partida' if es else 'Starting point'}</p><h2>{'Si comienzas desde cero: cuatro conceptos' if es else 'If you are starting from zero: four concepts'}</h2><dl class="guide-definitions"><dt>{'Materia médica' if es else 'Materia medica'}</dt><dd>{'El estudio de cada sustancia medicinal individual: qué es, qué hace, cuándo se usa y cuándo no.' if es else 'The study of each individual medicinal substance: what it is, what it does, when it is used and when it is not.'}</dd><dt>Duì Yào (对药)</dt><dd>{'Una pareja de hierbas elegida porque juntas refuerzan, complementan, equilibran o dirigen una estrategia. No es una fórmula completa.' if es else 'A pair of herbs selected because together they reinforce, complement, balance or direct a strategy. It is not a complete formula.'}</dd><dt>{'Fórmula' if es else 'Formula'}</dt><dd>{'Una prescripción organizada de varias hierbas con jerarquía y una estrategia para un patrón definido.' if es else 'An organized prescription of multiple herbs with a hierarchy and a strategy for a defined pattern.'}</dd><dt>{'Seguridad' if es else 'Safety'}</dt><dd>{'Identidad, procesamiento, dosis, interacciones, embarazo, toxicidad, contraindicaciones y sustituciones.' if es else 'Identity, processing, dosage, interactions, pregnancy, toxicity, contraindications and substitutions.'}</dd></dl></div></article>'''
        html += f'''<article class="guide-reading-step"><div class="guide-reading-step__number">2</div><div><p class="guide-eyebrow">{'Mapa vigente' if es else 'Current map'}</p><h2>{'Qué contiene cada apéndice y para qué sirve' if es else 'What each appendix contains and why it matters'}</h2><div class="guide-table-wrap"><table class="guide-table"><thead><tr><th>{'Apéndice' if es else 'Appendix'}</th><th>{'Contenido' if es else 'Content'}</th><th>{'Qué debes aprender' if es else 'What to learn'}</th><th>{'Resultado esperado' if es else 'Expected result'}</th></tr></thead><tbody>{''.join(f'<tr><th>{a}</th><td>{b}</td><td>{c}</td><td>{d}</td></tr>' for a,b,c,d in appendix_rows)}</tbody></table></div></div></article>'''
        html += f'''<article class="guide-reading-step"><div class="guide-reading-step__number">3</div><div><p class="guide-eyebrow">NCCAOM → NCBAHM</p><h2>{'Qué cambia en la preparación' if es else 'What changes in preparation'}</h2><p>{'La comparación muestra qué conocimientos conservar, qué contenido añadir y dónde redistribuir el tiempo de estudio para el alcance actual.' if es else 'The comparison shows what knowledge to retain, what content to add and where to redistribute study time for the current scope.'}</p><div class="guide-table-wrap"><table class="guide-table"><thead><tr><th>{'Área' if es else 'Area'}</th><th>{'Preparación anterior' if es else 'Earlier preparation'}</th><th>{'Preparación actual NCBAHM' if es else 'Current NCBAHM preparation'}</th><th>{'Acción del alumno' if es else 'Student action'}</th></tr></thead><tbody>{''.join(f'<tr><th>{a}</th><td>{b}</td><td>{c}</td><td>{d}</td></tr>' for a,b,c,d in transition_rows)}</tbody></table></div></div></article>'''
        html += f'''<article class="guide-reading-step" id="appendix-a-study"><div class="guide-reading-step__number">4</div><div><p class="guide-eyebrow">Appendix A · {'Hierbas individuales' if es else 'Individual herbs'}</p><h2>{'Construye primero una base que permita decidir' if es else 'First build a foundation that supports decisions'}</h2><p>{'Para cada hierba aprende identidad y parte medicinal, categoría, naturaleza, sabor, canales, acciones, indicaciones, dosis, preparación y seguridad. Añade una frase de contraste: “La elijo en vez de una hierba parecida cuando aparece esta pista”.' if es else 'For every herb learn identity and medicinal part, category, nature, flavor, channels, actions, indications, dosage, preparation and safety. Add one contrast sentence: “I choose it instead of a similar herb when this clue appears.”'}</p><p><strong>{'Utilidad:' if es else 'Why this matters:'}</strong> {'sin esa base, Duì Yào se convierte en memorización de parejas y las fórmulas se convierten en listas sin mecanismo.' if es else 'without that foundation, Duì Yào becomes pair memorization and formulas become lists without mechanism.'}</p><p><a href="#appendix-a-current-roster"><strong>{'Consultar las 304 hierbas en el anexo ↓' if es else 'See all 304 herbs in the annex ↓'}</strong></a></p></div></article>'''
        html += herb_properties_weight_section(es)
        html += f'''<article class="guide-reading-step" id="appendix-b-study"><div class="guide-reading-step__number">5</div><div><p class="guide-eyebrow">Appendix B · Duì Yào</p><h2>{'Qué es Duì Yào y por qué aparece después de las hierbas' if es else 'What Duì Yào is and why it comes after herbs'}</h2><p>{'Duì Yào significa literalmente “medicinales emparejados”. Es la unidad intermedia entre una hierba individual y una fórmula completa. La pareja permite ver cómo dos funciones se refuerzan, se complementan, se equilibran o cambian de dirección al combinarse.' if es else 'Duì Yào literally means “paired medicinals.” It is the intermediate unit between an individual herb and a complete formula. The pair shows how two functions reinforce, complement, balance or redirect one another when combined.'}</p><p>{'No se estudia primero porque necesitas conocer el aporte individual antes de comprender la relación. Tampoco debes confundir una pareja con una prescripción completa: normalmente es un núcleo funcional dentro de una fórmula mayor.' if es else 'It is not studied first because you need to know each individual contribution before understanding the relationship. Do not confuse a pair with a complete prescription: it is usually a functional core inside a larger formula.'}</p><h3>{'Ejemplo: Má Huáng + Guì Zhī' if es else 'Example: Má Huáng + Guì Zhī'}</h3><div class="guide-table-wrap"><table class="guide-table"><thead><tr><th>Má Huáng</th><th>Guì Zhī</th><th>{'Trabajo conjunto' if es else 'Combined job'}</th><th>{'Pista de caso' if es else 'Case clue'}</th></tr></thead><tbody><tr><td>{'Abre con fuerza el Exterior, induce sudoración y difunde Qi de Pulmón.' if es else 'Strongly opens the Exterior, induces sweating and diffuses Lung Qi.'}</td><td>{'Calienta la capa nutritiva y muscular y apoya la liberación hacia afuera.' if es else 'Warms the nutritive and muscle layers and supports outward release.'}</td><td>{'Liberación potente de Viento-Frío Exterior por exceso; pareja característica de Má Huáng Tāng.' if es else 'Powerful release of excess Exterior Wind-Cold; a characteristic pair in Má Huáng Tāng.'}</td><td>{'Escalofríos, dolores corporales, ausencia de sudor y posible sibilancia; revisar riesgo cardiovascular de Má Huáng.' if es else 'Chills, body aches, absence of sweating and possible wheezing; screen Má Huáng cardiovascular risk.'}</td></tr></tbody></table></div><p><a href="#dui-yao-annex"><strong>{'Consultar las 51 acciones, prioridades y auditoría en el anexo ↓' if es else 'See all 51 actions, priorities and the audit in the annex ↓'}</strong></a></p></div></article>'''
        html += f'''<article class="guide-reading-step"><div class="guide-reading-step__number">6</div><div><p class="guide-eyebrow">{'Método Duì Yào' if es else 'Duì Yào method'}</p><h2>{'Cómo dominar cada una de las 51 parejas' if es else 'How to master each of the 51 pairs'}</h2><ol><li>{'Nombra el trabajo conjunto en una sola frase.' if es else 'State the combined job in one sentence.'}</li><li>{'Explica qué aporta la primera hierba.' if es else 'Explain what the first herb contributes.'}</li><li>{'Explica qué aporta la segunda y en qué se diferencia.' if es else 'Explain what the second contributes and how it differs.'}</li><li>{'Identifica una fórmula representativa donde la pareja sea funcional.' if es else 'Identify a representative formula where the pair is functional.'}</li><li>{'Añade una pista que confirme la pareja y una que la descarte.' if es else 'Add one clue that confirms the pair and one that rules it out.'}</li><li>{'Cierra con dosis, preparación, interacción o contraindicación relevante.' if es else 'Finish with relevant dosage, preparation, interaction or contraindication.'}</li></ol><p><strong>{'Prueba de dominio:' if es else 'Mastery test:'}</strong> {'si solo recuerdas dos nombres, aún no dominas la pareja; debes poder explicar la relación.' if es else 'if you only remember two names, you do not yet master the pair; you must explain the relationship.'}</p></div></article>'''
        html += f'''<article class="guide-reading-step"><div class="guide-reading-step__number">7</div><div><p class="guide-eyebrow">Appendix C · {'Fórmulas' if es else 'Formulas'}</p><h2>{'Pasa de la pareja a la arquitectura de la fórmula' if es else 'Move from the pair to formula architecture'}</h2><p>{'Una fórmula no es una suma de ingredientes. Tiene una estrategia y una jerarquía Jun–Chen–Zuo–Shi: jefe, adjunto, asistente y mensajero. Estudia qué patrón trata, cómo cada ingrediente sostiene la estrategia, qué modificación cambia la dirección y qué contraindicación obliga a descartarla.' if es else 'A formula is not a sum of ingredients. It has a strategy and a Jun–Chen–Zuo–Shi hierarchy: chief, deputy, assistant and envoy. Study the pattern it treats, how each ingredient supports the strategy, which modification changes direction and which contraindication rules it out.'}</p><p><strong>{'Pregunta útil:' if es else 'Useful question:'}</strong> {'¿qué ingrediente o pareja explica la diferencia entre esta fórmula y otra de la misma familia?' if es else 'which ingredient or pair explains the difference between this formula and another in the same family?'}</p></div></article>'''
        html += f'''<article class="guide-reading-step"><div class="guide-reading-step__number">8</div><div><p class="guide-eyebrow">Appendix D · {'Seguridad' if es else 'Safety'}</p><h2>{'Qué no estudiar como tratamiento activo' if es else 'What not to study as an active treatment'}</h2><p>{'No memorices como opciones terapéuticas utilizables las nueve sustancias obsoletas: Xi Jiao, Hu Gu, Xiong Dan, Chuan Shan Jia, Guan Mu Tong, Guang Fang Ji, Ma Dou Ling, Zhu Sha y Ying Su Ke.' if es else 'Do not memorize the nine obsolete substances as usable therapeutic options: Xi Jiao, Hu Gu, Xiong Dan, Chuan Shan Jia, Guan Mu Tong, Guang Fang Ji, Ma Dou Ling, Zhu Sha and Ying Su Ke.'}</p><p>{'Sí debes reconocer su nombre, el motivo de seguridad o protección y la sustitución apropiada. Integra procesamiento, dosis, embarazo, toxicidad e interacciones durante el estudio de hierbas, parejas y fórmulas.' if es else 'You should still recognize each name, the safety or protection issue and the appropriate substitution. Integrate processing, dosage, pregnancy, toxicity and interactions while studying herbs, pairs and formulas.'}</p></div></article>'''
        html += f'''<article class="guide-reading-step"><div class="guide-reading-step__number">9</div><div><p class="guide-eyebrow">{'Aplicación al examen' if es else 'Exam application'}</p><h2>{'Método para resolver una pregunta' if es else 'Method for solving a question'}</h2><ol><li>{'Identifica el patrón y el principio de tratamiento antes de mirar los nombres.' if es else 'Identify the pattern and treatment principle before looking at names.'}</li><li>{'Decide si la pregunta pide una hierba, una pareja, una fórmula o una decisión de seguridad.' if es else 'Decide whether the question asks for an herb, pair, formula or safety decision.'}</li><li>{'Compara temperatura, humedad/sequedad, localización y exceso/deficiencia.' if es else 'Compare temperature, moisture/dryness, location and excess/deficiency.'}</li><li>{'Busca la pista decisiva que separa las dos opciones más cercanas.' if es else 'Find the decisive clue separating the two closest options.'}</li><li>{'Haz una última comprobación de dosis, preparación, interacción, embarazo, toxicidad u obsolescencia.' if es else 'Make a final check for dosage, preparation, interaction, pregnancy, toxicity or obsolete status.'}</li></ol></div></article>'''
        html += f'''<article class="guide-reading-step"><div class="guide-reading-step__number">10</div><div><p class="guide-eyebrow">{'Práctica dentro de AcupunctureTests' if es else 'Practice within AcupunctureTests'}</p><h2>{'Cómo recorrer los módulos sin perder el hilo' if es else 'How to move through the modules without losing the thread'}</h2><p>{'No tienes que abrir todos los módulos a la vez. Trabaja una categoría o un caso y haz que avance por la misma secuencia: hierba individual → comparación → Duì Yào → fórmula → seguridad. Los nombres de la primera columna son los módulos que encontrarás en AcupunctureTests.' if es else 'You do not need to open every module at once. Work with one category or case and move it through the same sequence: individual herb → comparison → Duì Yào → formula → safety. The first-column names are the modules you will find in AcupunctureTests.'}</p><div class="guide-table-wrap"><table class="guide-table"><thead><tr><th>{'Módulo' if es else 'Module'}</th><th>{'Cuándo usarlo' if es else 'When to use it'}</th><th>{'Qué hacer' if es else 'What to do'}</th><th>{'Qué debes producir' if es else 'What you should produce'}</th></tr></thead><tbody>{''.join(f'<tr><th>{a}</th><td>{b}</td><td>{c}</td><td>{d}</td></tr>' for a,b,c,d in module_rows)}</tbody></table></div><p><strong>{'Regla de navegación:' if es else 'Navigation rule:'}</strong> {'termina el grupo que estás leyendo con Continuous Reader, anota el capítulo y solo entonces pasa al siguiente módulo de la secuencia.' if es else 'finish the group you are reading with Continuous Reader, note the chapter, and only then move to the next module in the sequence.'}</p></div></article>'''
        html += f'''<article class="guide-reading-step"><div class="guide-reading-step__number">11</div><div><p class="guide-eyebrow">{'Ejemplo completo dentro del sitio' if es else 'Complete example within the site'}</p><h2>{'Cómo estudiaría Má Huáng, Guì Zhī y Má Huáng Tāng' if es else 'How I would study Má Huáng, Guì Zhī and Má Huáng Tāng'}</h2><ol><li><strong>Single Herbs Comparisons.</strong> {'Primero compararía Má Huáng con Guì Zhī. Escribiría esta regla: Má Huáng domina cuando hay Viento-Frío Exterior por exceso, ausencia de sudor y posible sibilancia; Guì Zhī adquiere especial importancia al armonizar las capas nutritiva y defensiva y en cuadros con sudoración espontánea.' if es else 'First I would compare Má Huáng with Guì Zhī. I would write this rule: Má Huáng dominates in excess Exterior Wind-Cold with absence of sweating and possible wheezing; Guì Zhī is especially important for harmonizing the nutritive and defensive layers and in presentations with spontaneous sweating.'}</li><li><strong>Duì Yào Pairs.</strong> {'Después explicaría la pareja: Má Huáng abre con fuerza el Exterior y difunde el Qi de Pulmón; Guì Zhī calienta, apoya la exteriorización y refuerza la liberación de Viento-Frío. Juntas forman un núcleo más potente que cualquiera de las dos por separado.' if es else 'Next I would explain the pair: Má Huáng strongly opens the Exterior and diffuses Lung Qi; Guì Zhī warms, supports outward movement and strengthens the release of Wind-Cold. Together they form a more powerful core than either herb alone.'}</li><li><strong>Formula Composition.</strong> {'Luego reconstruiría Má Huáng Tāng: Má Huáng como jefe; Guì Zhī como adjunto; Xìng Rén como asistente que desciende el Qi de Pulmón; Gān Cǎo como mensajero que armoniza y modera.' if es else 'Then I would reconstruct Má Huáng Tāng: Má Huáng as chief; Guì Zhī as deputy; Xìng Rén as assistant that descends Lung Qi; Gān Cǎo as envoy that harmonizes and moderates.'}</li><li><strong>Formula Comparisons.</strong> {'La compararía con Guì Zhī Tāng. La pista decisiva sería exceso con ausencia de sudor para Má Huáng Tāng frente a deficiencia del Exterior, sudoración espontánea y aversión al viento para Guì Zhī Tāng.' if es else 'I would compare it with Guì Zhī Tāng. The decisive clue would be excess with absence of sweating for Má Huáng Tāng versus Exterior deficiency, spontaneous sweating and aversion to wind for Guì Zhī Tāng.'}</li><li><strong>Safety Library.</strong> {'Antes de cerrar revisaría dosis, procesamiento e idoneidad de Má Huáng, especialmente antecedentes cardiovasculares, hipertensión, estimulantes concomitantes y otras contraindicaciones pertinentes.' if es else 'Before finishing I would review Má Huáng dosage, processing and suitability, especially cardiovascular history, hypertension, concurrent stimulants and other relevant contraindications.'}</li><li><strong>{'Comprobación final.' if es else 'Final check.'}</strong> {'Respondería un caso sin mirar las notas y justificaría en voz alta patrón, elección, jerarquía de ingredientes y seguridad. Si no puedo explicar una de esas cuatro partes, regreso únicamente al módulo correspondiente.' if es else 'I would answer one case without looking at notes and justify aloud the pattern, choice, ingredient hierarchy and safety. If I cannot explain one of those four parts, I return only to the corresponding module.'}</li></ol><p><strong>{'Producto de esta sesión:' if es else 'Output from this session:'}</strong> {'una regla de contraste entre dos hierbas, una explicación Duì Yào, un mapa de fórmula, una comparación entre fórmulas y una decisión de seguridad.' if es else 'one contrast rule between two herbs, one Duì Yào explanation, one formula map, one formula comparison and one safety decision.'}</p></div></article>'''
        html += f'''<article class="guide-reading-step"><div class="guide-reading-step__number">12</div><div><p class="guide-eyebrow">{'Rutina repetible' if es else 'Repeatable routine'}</p><h2>{'Una sesión de 45–60 minutos en AcupunctureTests' if es else 'A 45–60 minute AcupunctureTests session'}</h2><ol><li><strong>10 {'minutos' if es else 'minutes'}:</strong> {'repasa tres hierbas de una misma categoría y di su pista distintiva.' if es else 'review three herbs from the same category and state each distinguishing clue.'}</li><li><strong>15 {'minutos' if es else 'minutes'}:</strong> {'lee en orden un grupo de Single Herbs Comparisons con Continuous Reader y redacta tres reglas de contraste.' if es else 'read one Single Herbs Comparisons group in order with Continuous Reader and write three contrast rules.'}</li><li><strong>10 {'minutos' if es else 'minutes'}:</strong> {'estudia una pareja en Duì Yào Pairs y explica el aporte de cada componente.' if es else 'study one pair in Duì Yào Pairs and explain each component’s contribution.'}</li><li><strong>15 {'minutos' if es else 'minutes'}:</strong> {'reconstruye una fórmula en Formula Composition y compárala con su vecina más parecida.' if es else 'reconstruct one formula in Formula Composition and compare it with its closest neighbor.'}</li><li><strong>5 {'minutos' if es else 'minutes'}:</strong> {'añade una comprobación de Safety Library o Herbal Incompatibilities.' if es else 'add one Safety Library or Herbal Incompatibilities check.'}</li><li><strong>5 {'minutos' if es else 'minutes'}:</strong> {'resuelve un caso o explica el recorrido completo sin consultar las tarjetas.' if es else 'solve one case or explain the full sequence without consulting the cards.'}</li></ol><p>{'Al día siguiente no reinicies todo: comienza intentando recuperar de memoria los cinco productos de la sesión anterior y usa el módulo específico solo para corregir el punto débil.' if es else 'The next day, do not restart everything: begin by retrieving the five outputs from the prior session from memory and use only the specific module needed to correct the weak point.'}</p></div></article>'''
        html += f'''<article class="guide-reading-step guide-reading-step--last"><div class="guide-reading-step__number">13</div><div><p class="guide-eyebrow">{'Prioridades' if es else 'Priorities'}</p><h2>{'En qué hacer más énfasis' if es else 'Where to place more emphasis'}</h2><div class="guide-table-wrap"><table class="guide-table"><thead><tr><th>{'Bloque' if es else 'Block'}</th><th>{'Debes dominar' if es else 'You must master'}</th><th>{'No es suficiente' if es else 'Not sufficient'}</th></tr></thead><tbody>{''.join(f'<tr><th>{a}</th><td>{b}</td><td>{c}</td></tr>' for a,b,c in priority_rows)}</tbody></table></div><p><strong>{'Meta final:' if es else 'Final goal:'}</strong> {'distinguir opciones parecidas, explicar la función de cada componente, seleccionar y modificar una fórmula desde el patrón y detectar una elección insegura u obsoleta.' if es else 'distinguish similar options, explain each component’s role, select and modify a formula from the pattern, and detect an unsafe or obsolete choice.'}</p></div></article></section>'''
    else:
        html += '<div class="guide-grid">'
        for number, heading, rows in cards:
            html += f'<article class="guide-card"><p class="guide-eyebrow">{number} · {"Paso" if es else "Step"}</p><h2>{heading}</h2><ul>' + ''.join(f'<li>{escape(x)}</li>' for x in rows) + '</ul></article>'
        html += '</div>'
    audit = AUDITS[board["slug"]]
    audit_heading = ("Comprueba lo que ya puedes hacer" if es else "Check what you can already do") if herbal else ("Auditoría práctica del Board" if es else "Practical Board audit")
    audit_intro = ("No marques un bloque como terminado solo por haber leído esta guía. Márcalo cuando puedas aplicar el conocimiento a una pregunta clínica y explicar por qué las otras opciones son incorrectas." if es else "Do not mark a block complete only because you read this guide. Mark it when you can apply the knowledge to a clinical question and explain why the other options are wrong.") if herbal else ("Marca un bloque como cubierto solo cuando puedas resolverlo dentro de un caso, no solamente reconocer el término." if es else "Mark a block covered only when you can solve it inside a case, not merely recognize the term.")
    html += f'''<section class="guide-section"><p class="guide-eyebrow">{'Control de progreso' if herbal and es else 'Progress check' if herbal else 'Control de cobertura' if es else 'Coverage control'}</p><h2>{audit_heading}</h2><p>{audit_intro}</p><div class="guide-flow{' guide-flow--linear' if herbal else ''}">'''
    for en_label, es_label, en_body, es_body in audit:
        status_label = ("Debes poder" if es else "You can") if herbal else ("Verificar" if es else "Verify")
        html += f'''<article class="guide-flow__item"><span class="guide-status">{status_label}</span><h3>{escape(es_label if es else en_label)}</h3><p>{escape(es_body if es else en_body)}</p></article>'''
    html += '</div></section>'
    if herbal:
        html += appendix_a_roster(es)
        html += dui_yao_annex(es)
        html += historical_roster_annex(es)
        html += f'''<section class="guide-section"><p class="guide-eyebrow">{'Cierre del documento' if es else 'Document close'}</p><h2>{'Fuente y control de versión' if es else 'Source and version control'}</h2><p>{'El alcance vigente publicado por NCBAHM controla la preparación. Antes del examen confirma la fecha efectiva del temario y de los Apéndices A–D.' if es else 'The current scope published by NCBAHM controls preparation. Before the exam, confirm the effective date of the outline and Appendices A–D.'}</p><p class="guide-source"><a href="{CH}" target="_blank" rel="noopener">{CH}</a></p></section>'''
        html += final_discoveries_summary(es)
        html += new_student_site_reference(es)
        html += dui_yao_formula_bridge(es)
        html += dui_yao_formula_modifications(es)
        html += hypothetical_exam_model(es)
    else:
        html += f'''<section class="guide-section"><h2>{'Recursos recomendados' if es else 'Recommended resources'}</h2><div class="guide-resource-list">'''
        for en_label, es_label, en_href, es_href in RESOURCES[board["slug"]]:
            href = es_href if es else en_href
            external = href.startswith("http")
            html += f'''<a class="guide-resource" href="{href}"{' target="_blank" rel="noopener"' if external else ''}><span>{escape(es_label if es else en_label)}</span><strong>→</strong></a>'''
        html += '</div></section>'
        html += f'''<section class="guide-section"><h2>{'Fuente y control de versión' if es else 'Source and version control'}</h2><p>{'Consulta la publicación oficial antes de cambiar el plan de estudio. La fecha de revisión de esta página no convierte el material educativo en una publicación del Board.' if es else 'Check the official publication before changing the study plan. This page’s review date does not make the educational material a Board publication.'}</p><a class="guide-button guide-button--outline" href="{source}" target="_blank" rel="noopener">{'Abrir fuente oficial' if es else 'Open official source'} ↗</a></section>'''
    return html + foot(es, returnscript=herbal)

def herbal_cards(lang):
    es = lang == "es"
    suffix = "-es" if es else ""
    alt = "chinese-herbal-medicine-cards.html" if es else "chinese-herbal-medicine-cards-es.html"
    html = top(lang, "Tres tarjetas de Medicina Herbal China" if es else "Three Chinese Herbal Medicine Cards", alt)
    html += f'''<section class="guide-hero"><p class="guide-eyebrow">{'Medicina Herbal China · 3 tarjetas' if es else 'Chinese Herbal Medicine · 3 cards'}</p><h1>{'Listados y análisis NCBAHM' if es else 'NCBAHM rosters and analysis'}</h1><p>{'Una entrada para las hierbas, otra para las fórmulas y una tercera para convertir el temario vigente en un plan de estudio.' if es else 'One entry for herbs, one for formulas, and a third that turns the current outline into a study plan.'}</p><div class="guide-actions guide-hero__actions"><a class="guide-button guide-button--outline" href="chinese-herbal-medicine{suffix}.html">{'← Regresar a la ruta de Medicina Herbal' if es else '← Back to the Herbal Medicine route'}</a></div></section><div class="guide-alert"><strong>NCBAHM</strong> — {'National Certification Board for Acupuncture and Herbal Medicine, antes NCCAOM. Usa NCBAHM como la sigla oficial vigente.' if es else 'National Certification Board for Acupuncture and Herbal Medicine, formerly NCCAOM. Use NCBAHM as the current official acronym.'}</div><div class="guide-grid">'''
    herb_categories_es = ["Liberar el Exterior", "Limpiar Calor", "Drenar hacia abajo", "Drenar Humedad", "Disipar Viento-Humedad", "Transformar Flema y aliviar tos", "Transformar Humedad", "Reducir estancamiento de alimentos", "Regular Qi", "Regular Sangre", "Calentar el Interior", "Tonificar", "Estabilizar y astringir", "Calmar el Shen", "Abrir orificios", "Extinguir Viento", "Expulsar parásitos", "Uso externo"]
    herb_categories_en = ["Release the Exterior", "Clear Heat", "Drain Downward", "Drain Dampness", "Dispel Wind-Damp", "Transform Phlegm and relieve cough", "Transform Dampness", "Reduce Food Stagnation", "Regulate Qi", "Regulate Blood", "Warm the Interior", "Tonify", "Stabilize and Astringe", "Calm the Spirit", "Open the Orifices", "Extinguish Wind", "Expel Parasites", "External Use"]
    formula_categories_es = ["Liberar Exterior", "Limpiar Calor", "Drenar hacia abajo", "Armonizar", "Drenar Humedad", "Humedecer Sequedad", "Calentar Interior", "Tonificar", "Regular Qi", "Regular Sangre", "Estabilizar y astringir", "Calmar el Shen", "Expulsar Viento", "Abrir orificios", "Transformar Flema", "Reducir alimentos", "Expulsar parásitos"]
    formula_categories_en = ["Release the Exterior", "Clear Heat", "Drain Downward", "Harmonize", "Drain Dampness", "Moisten Dryness", "Warm the Interior", "Tonify", "Regulate Qi", "Regulate Blood", "Stabilize and Astringe", "Calm the Spirit", "Expel Wind", "Open the Orifices", "Transform Phlegm", "Reduce Food Stagnation", "Expel Parasites"]
    herbs = herb_categories_es if es else herb_categories_en
    formulas = formula_categories_es if es else formula_categories_en
    herb_comparison = [
        ("Qué continúa" if es else "What continues", "Conserva la base de materia médica: categoría, naturaleza, sabor, canales, acciones e indicaciones." if es else "Keep the materia medica foundation: category, nature, flavor, channels, actions and indications.", "Estudia las hierbas incluidas en el Apéndice A vigente y aplícalas a patrones y decisiones clínicas." if es else "Study the herbs in the current Appendix A and apply them to patterns and clinical decisions."),
        ("Qué no estudiar como opción activa" if es else "What not to study as an active option", "No dediques tiempo a memorizar como tratamientos utilizables las sustancias que ahora se clasifican como obsoletas." if es else "Do not spend time memorizing substances now classified as obsolete as usable treatments.", "Xi Jiao, Hu Gu, Xiong Dan, Chuan Shan Jia, Guan Mu Tong, Guang Fang Ji, Ma Dou Ling, Zhu Sha y Ying Su Ke: reconócelas solo por su estatus, riesgo y sustitución en el Apéndice D." if es else "Xi Jiao, Hu Gu, Xiong Dan, Chuan Shan Jia, Guan Mu Tong, Guang Fang Ji, Ma Dou Ling, Zhu Sha and Ying Su Ke: recognize them for status, risk and substitution in Appendix D."),
        ("Qué estudiar ahora" if es else "What to study now", "No limites la preparación a recordar hierbas individuales por nombre o categoría." if es else "Do not limit preparation to recalling individual herbs by name or category.", "A: hierbas individuales · B: 51 parejas Duì Yào · C: fórmulas · D: sustancias obsoletas y sustitutos." if es else "A: individual herbs · B: 51 Duì Yào pairs · C: formulas · D: obsolete substances and substitutes."),
        ("Dónde hacer más énfasis" if es else "Where to place more emphasis", "Usa la memorización como base, no como meta final." if es else "Use memorization as a foundation, not the final goal.", "Prioriza diferencias entre hierbas parecidas, parejas Duì Yào, dosis, preparación, toxicidad, embarazo, interacciones, contraindicaciones y sustitutos." if es else "Prioritize distinctions between similar herbs, Duì Yào pairs, dosage, preparation, toxicity, pregnancy, interactions, contraindications and substitutes."),
    ]
    herb_table = ''.join(f'<tr><th>{a}</th><td>{b}</td><td>{c}</td></tr>' for a,b,c in herb_comparison)
    html += f'''<article class="guide-card"><p class="guide-eyebrow">{'Tarjeta 1 · Apéndice A' if es else 'Card 1 · Appendix A'}</p><h2>{'Nuevo listado de hierbas' if es else 'Current herb roster'}</h2><p>{'Usa esta comparación para decidir qué mantener, qué dejar de estudiar como opción terapéutica activa y dónde concentrar tu preparación para NCBAHM.' if es else 'Use this comparison to decide what to retain, what to stop studying as an active therapeutic option and where to concentrate your NCBAHM preparation.'}</p><h3>{'Cambio de enfoque: NCCAOM → NCBAHM' if es else 'Change in focus: NCCAOM → NCBAHM'}</h3><div class="guide-table-wrap"><table class="guide-table"><thead><tr><th>{'Decisión de estudio' if es else 'Study decision'}</th><th>{'Base que conservas' if es else 'Foundation to retain'}</th><th>{'Preparación actual NCBAHM' if es else 'Current NCBAHM preparation'}</th></tr></thead><tbody>{herb_table}</tbody></table></div><div class="guide-tags">{''.join(f'<span class="guide-tag">{escape(x)}</span>' for x in herbs)}</div><div class="guide-card__meta">{'Las etiquetas verdes organizan el repaso por categorías. El alcance que debes dominar se estudia con los Apéndices A–D: no conviertas una sustancia obsoleta en opción de tratamiento, pero sí aprende a reconocerla y sustituirla con seguridad.' if es else 'The green labels organize review by category. Study the required scope through Appendices A–D: do not treat an obsolete substance as a treatment option, but do learn to recognize and replace it safely.'}</div><a class="guide-button" href="../comparisons/index{suffix}.html">{'Abrir índice de hierbas' if es else 'Open herb index'} →</a><a class="guide-button guide-button--outline" href="../comparisons/secondary-actions{suffix}.html">{'Acciones secundarias' if es else 'Secondary actions'} →</a></article>'''
    formula_comparison = [
        ("Qué continúa" if es else "What continues", "Conserva el reconocimiento del nombre, la categoría, la función principal y el patrón de cada fórmula." if es else "Keep recognition of each formula's name, category, primary function and pattern.", "Domina las fórmulas incluidas en el Apéndice C vigente." if es else "Master the formulas included in the current Appendix C."),
        ("Qué no priorizar" if es else "What not to prioritize", "No centres el estudio en acumular nombres o traducciones de memoria." if es else "Do not center study on accumulating names or translations from memory.", "No dediques tiempo principal de examen a una fórmula ausente del Apéndice C vigente; consérvala solo como ampliación clínica o del curso." if es else "Do not devote primary exam time to a formula absent from the current Appendix C; retain it only as clinical or course enrichment."),
        ("Qué estudiar ahora" if es else "What to study now", "Usa el nombre y la categoría como punto de entrada." if es else "Use the name and category as the entry point.", "Aprende ingredientes, jerarquía Jun–Chen–Zuo–Shi, acciones, indicaciones, modificaciones, contraindicaciones y seguridad." if es else "Learn ingredients, Jun–Chen–Zuo–Shi hierarchy, actions, indications, modifications, contraindications and safety."),
        ("Dónde hacer más énfasis" if es else "Where to place more emphasis", "Repasa familias de fórmulas para reconocer su estructura común." if es else "Review formula families to recognize their shared structure.", "Practica la elección entre fórmulas cercanas, el ingrediente que cambia la estrategia, la modificación según el paciente y las contraindicaciones." if es else "Practice choosing between similar formulas, the ingredient that changes the strategy, patient-based modification and contraindications."),
    ]
    formula_table = ''.join(f'<tr><th>{a}</th><td>{b}</td><td>{c}</td></tr>' for a,b,c in formula_comparison)
    html += f'''<article class="guide-card"><p class="guide-eyebrow">{'Tarjeta 2 · Apéndice C' if es else 'Card 2 · Appendix C'}</p><h2>{'Nuevo listado de fórmulas' if es else 'Current formula roster'}</h2><p>{'Usa esta comparación para redistribuir el tiempo de estudio: conserva la base aprendida y dedica la práctica principal a las fórmulas y competencias del alcance NCBAHM actual.' if es else 'Use this comparison to redistribute study time: retain the foundation already learned and devote primary practice to formulas and competencies in the current NCBAHM scope.'}</p><h3>{'Cambio de enfoque: NCCAOM → NCBAHM' if es else 'Change in focus: NCCAOM → NCBAHM'}</h3><div class="guide-table-wrap"><table class="guide-table"><thead><tr><th>{'Decisión de estudio' if es else 'Study decision'}</th><th>{'Base que conservas' if es else 'Foundation to retain'}</th><th>{'Preparación actual NCBAHM' if es else 'Current NCBAHM preparation'}</th></tr></thead><tbody>{formula_table}</tbody></table></div><div class="guide-tags">{''.join(f'<span class="guide-tag">{escape(x)}</span>' for x in formulas)}</div><div class="guide-card__meta">{'Las etiquetas verdes sirven para organizar las familias. Para el examen, prioriza las fórmulas del Apéndice C y conviértelas en decisiones clínicas, no en una lista aislada de nombres.' if es else 'The green labels organize formula families. For the exam, prioritize Appendix C formulas and turn them into clinical decisions rather than an isolated list of names.'}</div><a class="guide-button" href="../comparisons/most-important-formulas{suffix}.html">{'Abrir 85 fórmulas' if es else 'Open 85 formulas'} →</a><a class="guide-button guide-button--outline" href="../comparisons/formulas{suffix}.html">{'Abrir comparaciones' if es else 'Open comparisons'} →</a></article>'''
    analysis_rows = [
        ("I", "Materia medica y fórmulas" if es else "Materia medica and formulas", "Reconocer no basta: relacionar propiedades, función y patrón." if es else "Recognition is not enough: connect properties, function and pattern."),
        ("II", "Seguridad y dispensación" if es else "Safety and dispensing", "Procesamiento, dosis, toxicidad, interacciones, embarazo, control de calidad del producto herbal y sustancias obsoletas." if es else "Processing, dosage, toxicity, interactions, pregnancy, herbal product quality control and obsolete substances."),
        ("III", "Planificación del tratamiento" if es else "Treatment planning", "Seleccionar, combinar, modificar, sustituir, monitorizar y derivar según la evaluación." if es else "Select, combine, modify, substitute, monitor and refer from the patient assessment."),
    ]
    html += f'''<article class="guide-card guide-card--wide guide-card--purple"><p class="guide-eyebrow">{'Tarjeta 3 · Análisis' if es else 'Card 3 · Analysis'}</p><h2>{'Cómo leer el guideline actual' if es else 'How to read the current guideline'}</h2><div class="guide-table-wrap"><table class="guide-table"><thead><tr><th>{'Bloque' if es else 'Block'}</th><th>{'Responsabilidad' if es else 'Responsibility'}</th><th>{'Aplicación al estudio' if es else 'Study application'}</th></tr></thead><tbody>{''.join(f'<tr><th>{a}</th><td>{b}</td><td>{c}</td></tr>' for a,b,c in analysis_rows)}</tbody></table></div><p><strong>{'Cambio clave:' if es else 'Key change:'}</strong> {'no necesitas empezar de cero. Conserva la materia médica y las fórmulas que continúan vigentes, reorganiza el repaso según los Apéndices A–D y deja de usar las sustancias obsoletas como respuestas terapéuticas.' if es else 'you do not need to start over. Keep the materia medica and formulas that remain current, reorganize review around Appendices A–D, and stop using obsolete substances as therapeutic answers.'}</p><p><strong>{'Prioridad:' if es else 'Priority:'}</strong> {'después de reconocer nombres y categorías, dedica más práctica a comparar opciones cercanas, elegir según el patrón, ajustar la fórmula y resolver seguridad, dosis, interacciones y contraindicaciones.' if es else 'after recognizing names and categories, devote more practice to comparing close options, selecting by pattern, adjusting the formula, and solving safety, dosage, interaction and contraindication questions.'}</p><a class="guide-button guide-button--outline" href="{OFFICIAL}" target="_blank" rel="noopener">{'Verificar publicación oficial vigente' if es else 'Verify current official publication'} ↗</a></article></div>'''
    return html + foot(es)

def crosswalk(lang):
    es = lang == "es"
    suffix = "-es" if es else ""
    alternate = "nccaom-ncbahm-crosswalk.html" if es else "nccaom-ncbahm-crosswalk-es.html"
    html = top(lang, "Crosswalk NCCAOM a NCBAHM" if es else "NCCAOM to NCBAHM Crosswalk", alternate)
    html += f'''<section class="guide-hero"><p class="guide-eyebrow">{'Ajuste del plan de estudio' if es else 'Study plan adjustment'}</p><h1>NCCAOM → NCBAHM</h1><p>{'Una guía para saber qué conocimientos conservar, qué dejar de estudiar como contenido activo y dónde aumentar la práctica para el Board actual.' if es else 'A guide to what knowledge to retain, what to stop studying as active content and where to increase practice for the current Board.'}</p><div class="guide-actions guide-hero__actions"><a class="guide-button guide-button--outline" href="chinese-herbal-medicine{suffix}.html">{'← Regresar a la ruta de Medicina Herbal' if es else '← Back to the Herbal Medicine route'}</a><a class="guide-button guide-button--outline" href="chinese-herbal-medicine-cards{suffix}.html">{'← Volver a las 3 tarjetas' if es else '← Back to the 3 cards'}</a></div></section><div class="guide-alert"><strong>{'Objetivo:' if es else 'Goal:'}</strong> {'reorganizar tu preparación alrededor del alcance NCBAHM actual sin perder los fundamentos que siguen siendo aplicables.' if es else 'reorganize your preparation around the current NCBAHM scope without losing foundations that still apply.'}</div>'''
    levels = [
        ("1", "Estudia el alcance vigente" if es else "Study the current scope", "Empieza por los temas y apéndices NCBAHM correspondientes a tu Board; estos definen el contenido que debes dominar ahora." if es else "Begin with the NCBAHM topics and appendices for your Board; these define the content to master now."),
        ("2", "Conserva la base aplicable" if es else "Retain applicable foundations", "Mantén los conceptos, hierbas y fórmulas que continúan en el alcance; no repitas desde cero lo que ya dominas." if es else "Keep concepts, herbs and formulas that remain in scope; do not restart material you already master."),
        ("3", "Redirige la práctica" if es else "Redirect practice", "Dedica más preguntas a comparación, selección clínica, seguridad y los contenidos nuevos o reorganizados." if es else "Devote more questions to comparison, clinical selection, safety and content that is new or reorganized."),
    ]
    html += '<div class="guide-grid">'
    for number, title, body in levels:
        html += f'''<article class="guide-card"><p class="guide-eyebrow">{'Nivel' if es else 'Level'} {number}</p><h2>{title}</h2><p>{body}</p></article>'''
    html += '</div>'
    board_rows = [
        ("Foundations of Oriental Medicine", "Teoría, diagnóstico, patrones y principios de tratamiento." if es else "Theory, diagnosis, patterns and treatment principles.", "Priorizar diferenciación de patrones, principio de tratamiento y aplicación en casos; no estudiar teoría como definiciones aisladas." if es else "Prioritize pattern differentiation, treatment principle and case application; do not study theory as isolated definitions."),
        ("Acupuncture with Point Location", "Temario vigente desde el 1 de septiembre de 2026: 15% localización, 15% seguridad, 40% planificación y 30% técnicas." if es else "Outline effective September 1, 2026: 15% location, 15% safety, 40% planning and 30% techniques.", "Usar esos pesos para distribuir práctica y simulacros." if es else "Use those weights to distribute practice and mock exams."),
        ("Chinese Herbal Medicine", "Apéndices A: hierbas, B: 51 Duì Yào, C: fórmulas y D: sustancias obsoletas/sustituciones." if es else "Appendices A: herbs, B: 51 Duì Yào, C: formulas and D: obsolete substances/substitutions.", "Dar más práctica a comparación, selección, modificación, dosis y seguridad; no usar sustancias obsoletas como tratamientos activos." if es else "Give more practice to comparison, selection, modification, dosage and safety; do not use obsolete substances as active treatments."),
        ("Biomedicine", "Ciencias biomédicas, evaluación, diagnóstico, tratamiento, banderas rojas y derivación." if es else "Biomedical sciences, assessment, diagnosis, treatment, red flags and referral.", "Priorizar reconocimiento de urgencias, decisiones de derivación y aplicación clínica sobre memorización aislada." if es else "Prioritize emergency recognition, referral decisions and clinical application over isolated memorization."),
    ]
    html += f'''<section class="guide-section"><p class="guide-eyebrow">{'Mapa por Board' if es else 'Board map'}</p><h2>{'Dónde concentrar la preparación' if es else 'Where to concentrate preparation'}</h2><div class="guide-table-wrap"><table class="guide-table"><thead><tr><th>Board</th><th>{'Contenido actual' if es else 'Current content'}</th><th>{'Énfasis de estudio' if es else 'Study emphasis'}</th></tr></thead><tbody>'''
    for board, scope, action in board_rows:
        html += f'<tr><th>{board}</th><td>{scope}</td><td>{action}</td></tr>'
    html += '</tbody></table></div></section>'
    confirmed = [
        "La sigla vigente es NCBAHM; NCCAOM queda como referencia histórica." if es else "The current acronym is NCBAHM; NCCAOM remains a historical reference.",
        "Herbal Medicine usa la estructura de Apéndices A–D y Appendix B contiene 51 parejas Duì Yào." if es else "Herbal Medicine uses the Appendix A–D structure and Appendix B contains 51 Duì Yào pairs.",
        "Appendix D separa nueve sustancias obsoletas que deben reconocerse por seguridad y sustitución, no como opciones terapéuticas activas." if es else "Appendix D separates nine obsolete substances to recognize for safety and substitution, not as active therapeutic options.",
        "El nuevo temario ACPL entra en vigor el 1 de septiembre de 2026 y publica cuatro pesos explícitos." if es else "The new ACPL outline takes effect September 1, 2026 and publishes four explicit weights.",
    ]
    boundaries = [
        "247 notas comparativas no significan 247 hierbas oficiales." if es else "247 comparative notes do not mean 247 official herbs.",
        "110 comparaciones de fórmulas no significan 110 fórmulas oficiales." if es else "110 formula comparisons do not mean 110 official formulas.",
        "85 resúmenes de fórmulas indican volumen de práctica, no el número de fórmulas exigidas por el Board." if es else "85 formula summaries indicate practice volume, not the number of formulas required by the Board.",
        "Una variante de pinyin, sinónimo o forma procesada no constituye automáticamente una adición o eliminación." if es else "A pinyin variant, synonym or processed form is not automatically an addition or removal.",
    ]
    html += f'''<div class="guide-grid"><article class="guide-card"><p class="guide-eyebrow">{'Cambios que afectan el estudio' if es else 'Changes that affect study'}</p><h2>{'Qué debes incorporar al plan' if es else 'What to add to the plan'}</h2><ul>{''.join(f'<li>{x}</li>' for x in confirmed)}</ul></article><article class="guide-card"><p class="guide-eyebrow">{'Uso de las prácticas' if es else 'Using practice resources'}</p><h2>{'No confundir cantidad con alcance' if es else 'Do not confuse quantity with scope'}</h2><ul>{''.join(f'<li>{x}</li>' for x in boundaries)}</ul></article></div>'''
    steps = [
        ("1", "Registrar edición y fecha" if es else "Record edition and date"),
        ("2", "Normalizar nombre, identidad y parte" if es else "Normalize name, identity and part"),
        ("3", "Clasificar: retenida, renombrada, nueva, retirada u obsoleta" if es else "Classify: retained, renamed, added, removed or obsolete"),
        ("4", "Separar alcance de examen, riesgo clínico y legalidad" if es else "Separate exam scope, clinical risk and legality"),
        ("5", "Publicar la fuente junto al resultado" if es else "Publish the source beside the result"),
    ]
    html += f'''<section class="guide-section"><p class="guide-eyebrow">{'Protocolo' if es else 'Protocol'}</p><h2>{'Cómo reconciliar los dos listados' if es else 'How to reconcile the two rosters'}</h2><div class="guide-flow">{''.join(f'<article class="guide-flow__item"><span class="guide-status">{number}</span><h3>{label}</h3></article>' for number, label in steps)}</div><div class="guide-actions guide-actions--section"><a class="guide-button" href="chinese-herbal-medicine-cards{'-es' if es else ''}.html">{'Abrir comparación de hierbas y fórmulas' if es else 'Open herb and formula comparison'} →</a><a class="guide-button guide-button--outline" href="../comparisons/safety-obsolete-prohibited-substances{'-es' if es else ''}.html">{'Revisar sustancias obsoletas' if es else 'Review obsolete substances'} →</a></div></section>'''
    return html + foot(es)

def hub(lang):
    es = lang == "es"
    suffix = "-es" if es else ""
    alternate = "board-guidelines.html" if es else "board-guidelines-es.html"
    css = "guidelines/guidelines.css?v=20261027-3"
    html = f'''<!doctype html><html lang="{lang}"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="description" content="{'Guías de preparación por Board' if es else 'Board preparation guidelines'}"><title>{'Guías de Board' if es else 'Board Guidelines'} | AcupunctureTests</title><link rel="alternate" hreflang="{'en' if es else 'es'}" href="{alternate}"><link rel="stylesheet" href="{css}"></head><body><header class="guide-topbar"><div class="guide-shell guide-topbar__inner"><a class="guide-brand" href="/my/">☯ AcupunctureTests</a><div class="guide-actions"><a class="guide-button guide-button--outline" href="{alternate}">{'English' if es else 'Español'}</a><a class="guide-button guide-button--outline" href="/my/">← Dashboard</a></div></div></header><main class="guide-shell"><section class="guide-hero"><p class="guide-eyebrow">NCBAHM · {'Centro independiente' if es else 'Independent hub'}</p><h1>{'Guidelines por Board' if es else 'Guidelines by Board'}</h1><p>{'Abre la guía del examen que estás preparando. Cada página separa el alcance oficial, el método de estudio y la auditoría de cobertura.' if es else 'Open the guideline for the exam you are preparing. Each page separates official scope, study method and coverage audit.'}</p></section><div class="guide-alert"><strong>{'Control de versión:' if es else 'Version control:'}</strong> {'Revisado el 27 de octubre de 2026. Antes del examen, confirma siempre la versión y fecha efectiva en NCBAHM.' if es else 'Reviewed October 27, 2026. Before the exam, always confirm the version and effective date with NCBAHM.'}</div><div class="guide-grid">'''
    for board in BOARDS:
        title = board["es" if es else "en"]
        summary = board["summary_es" if es else "summary_en"]
        html += f'''<article class="guide-card"><p class="guide-eyebrow">{'Guía de examen' if es else 'Exam guideline'}</p><h2>{escape(title)}</h2><p>{escape(summary)}</p><a class="guide-button" href="guidelines/{board['slug']}{suffix}.html">{'Abrir guideline' if es else 'Open guideline'} →</a></article>'''
    html += f'''<article class="guide-card guide-card--wide"><p class="guide-eyebrow">{'Transición de contenido' if es else 'Content transition'}</p><h2>NCCAOM → NCBAHM Crosswalk</h2><p>{'Distingue cambios confirmados, fuentes históricas y recursos educativos antes de comparar los listados.' if es else 'Separate confirmed changes, historical sources and educational resources before comparing rosters.'}</p><a class="guide-button" href="guidelines/nccaom-ncbahm-crosswalk{suffix}.html">{'Abrir crosswalk' if es else 'Open crosswalk'} →</a></article>'''
    html += f'''<article class="guide-card guide-card--wide guide-card--purple"><p class="guide-eyebrow">{'Requisito adicional de seguridad' if es else 'Additional safety requirement'}</p><h2>Clean Needle Technique</h2><p>{'Se mantiene visible junto a la preparación, pero no se presenta como un quinto examen NCBAHM. La elegibilidad y la certificación deben comprobarse con la entidad que administre el requisito vigente.' if es else 'It remains visible beside Board preparation, but is not presented as a fifth NCBAHM examination. Eligibility and certification must be confirmed with the organization administering the current requirement.'}</p><a class="guide-button guide-button--outline" href="/blocks/myprogress/study-guide.php?area=cleanneedle">{'Abrir recursos CNT' if es else 'Open CNT resources'} →</a></article></div>'''
    return html + foot(es)

def main():
    OUT.mkdir(parents=True, exist_ok=True)
    (ROOT / "study-guide" / "board-guidelines.html").write_text(hub("en"), encoding="utf-8")
    (ROOT / "study-guide" / "board-guidelines-es.html").write_text(hub("es"), encoding="utf-8")
    for board in BOARDS:
        (OUT / f'{board["slug"]}.html').write_text(board_page(board, "en"), encoding="utf-8")
        (OUT / f'{board["slug"]}-es.html').write_text(board_page(board, "es"), encoding="utf-8")
    (OUT / "nccaom-ncbahm-crosswalk.html").write_text(crosswalk("en"), encoding="utf-8")
    (OUT / "nccaom-ncbahm-crosswalk-es.html").write_text(crosswalk("es"), encoding="utf-8")
    (OUT / "chinese-herbal-medicine-cards.html").write_text(herbal_cards("en"), encoding="utf-8")
    (OUT / "chinese-herbal-medicine-cards-es.html").write_text(herbal_cards("es"), encoding="utf-8")

if __name__ == "__main__":
    main()
