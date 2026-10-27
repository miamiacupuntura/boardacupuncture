#!/usr/bin/env python3
from pathlib import Path
from html import escape

ROOT = Path(__file__).resolve().parents[1]
OUT = ROOT / "study-guide" / "guidelines"
OFFICIAL = "https://www.ncbahm.org/"
ACPL = "https://www.ncbahm.org/wp-content/uploads/pdf/NCBAHM_ACPL_Exam_Content_Outline-w-Bibliography_Jan_2026.pdf"

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
        "summary_en": "Separate the official herb and formula rosters from the competencies required to use them safely.",
        "summary_es": "Separa los listados oficiales de hierbas y fórmulas de las competencias necesarias para usarlas con seguridad.",
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

def top(lang, title, alternate, back="../board-guidelines"):
    es = lang == "es"
    suffix = "-es.html" if es else ".html"
    return f'''<!doctype html><html lang="{lang}"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="description" content="{escape(title)}"><title>{escape(title)} | AcupunctureTests</title><link rel="alternate" hreflang="{'en' if es else 'es'}" href="{alternate}"><link rel="stylesheet" href="guidelines.css?v=20261027-1"></head><body><header class="guide-topbar"><div class="guide-shell guide-topbar__inner"><a class="guide-brand" href="{back}{suffix}">☯ AcupunctureTests · {'Guías de Board' if es else 'Board Guidelines'}</a><div class="guide-actions"><a class="guide-button guide-button--outline" href="{alternate}">{'English' if es else 'Español'}</a><a class="guide-button guide-button--outline" href="/my/">← Dashboard</a></div></div></header><main class="guide-shell">'''

def foot(es=False):
    return f'''</main><footer class="guide-footer"><div class="guide-shell">{'Revisado el 27 de octubre de 2026 · El temario oficial vigente siempre prevalece.' if es else 'Reviewed October 27, 2026 · The current official outline always controls.'}</div></footer></body></html>'''

def board_page(board, lang):
    es = lang == "es"
    title = board["es" if es else "en"]
    summary = board["summary_es" if es else "summary_en"]
    scope = board["scope_es" if es else "scope_en"]
    study = board["study_es" if es else "study_en"]
    suffix = "-es" if es else ""
    alternate = f'{board["slug"]}{"" if es else "-es"}.html'
    source = board.get("source", OFFICIAL)
    html = top(lang, title, alternate)
    html += f'''<section class="guide-hero"><p class="guide-eyebrow">{'Guía de Board' if es else 'Board guideline'}</p><h1>{escape(title)}</h1><p>{escape(summary)}</p></section><div class="guide-alert"><strong>{'Límite de la guía:' if es else 'Guide boundary:'}</strong> {'Este mapa organiza el estudio; no reemplaza el manual del candidato, el temario oficial ni las reglas de elegibilidad.' if es else 'This map organizes study; it does not replace the candidate handbook, official content outline or eligibility rules.'}</div><div class="guide-grid">'''
    cards = [
        ("1", "Alcance principal" if es else "Core scope", scope),
        ("2", "Método de estudio" if es else "Study method", [study]),
        ("3", "Auditoría antes del examen" if es else "Pre-exam audit", ["Confirmar versión y fecha del temario oficial." if es else "Confirm the official outline version and effective date.", "Relacionar cada recurso del curso con una competencia del temario." if es else "Map every course resource to an outline competency.", "Separar contenido oficial, material educativo y reglas clínicas o legales." if es else "Separate official scope, educational material, and clinical or legal rules."]),
    ]
    for number, heading, rows in cards:
        html += f'<article class="guide-card"><p class="guide-eyebrow">{number} · {"Paso" if es else "Step"}</p><h2>{heading}</h2><ul>' + ''.join(f'<li>{escape(x)}</li>' for x in rows) + '</ul></article>'
    if board["slug"] == "chinese-herbal-medicine":
        html += f'''<article class="guide-card guide-card--wide guide-card--purple"><h2>{'Tres tarjetas de Herbal Medicine' if es else 'Three Herbal Medicine cards'}</h2><p>{'Abre los dos listados y el análisis vigente en una página independiente.' if es else 'Open the two rosters and current-outline analysis on a dedicated page.'}</p><a class="guide-button" href="chinese-herbal-medicine-cards{suffix}.html">{'Abrir las 3 tarjetas' if es else 'Open the 3 cards'} →</a></article>'''
    html += f'''</div><section class="guide-section"><h2>{'Fuente y control de versión' if es else 'Source and version control'}</h2><p>{'Consulta la publicación oficial antes de cambiar el plan de estudio. La fecha de revisión de esta página no convierte el material educativo en una publicación del Board.' if es else 'Check the official publication before changing the study plan. This page’s review date does not make the educational material a Board publication.'}</p><a class="guide-button guide-button--outline" href="{source}" target="_blank" rel="noopener">{'Abrir fuente oficial' if es else 'Open official source'} ↗</a></section>'''
    return html + foot(es)

def herbal_cards(lang):
    es = lang == "es"
    suffix = "-es" if es else ""
    alt = "chinese-herbal-medicine-cards.html" if es else "chinese-herbal-medicine-cards-es.html"
    html = top(lang, "Tres tarjetas de Medicina Herbal China" if es else "Three Chinese Herbal Medicine Cards", alt)
    html += f'''<section class="guide-hero"><p class="guide-eyebrow">{'Medicina Herbal China · 3 tarjetas' if es else 'Chinese Herbal Medicine · 3 cards'}</p><h1>{'Listados y análisis NCBAHM' if es else 'NCBAHM rosters and analysis'}</h1><p>{'Una entrada para las hierbas, otra para las fórmulas y una tercera para convertir el temario vigente en un plan de estudio.' if es else 'One entry for herbs, one for formulas, and a third that turns the current outline into a study plan.'}</p></section><div class="guide-alert"><strong>NCBAHM</strong> — {'National Certification Board for Acupuncture and Herbal Medicine, antes NCCAOM. Usa NCBAHM como la sigla oficial vigente.' if es else 'National Certification Board for Acupuncture and Herbal Medicine, formerly NCCAOM. Use NCBAHM as the current official acronym.'}</div><div class="guide-grid">'''
    herb_categories_es = ["Liberar el Exterior", "Limpiar Calor", "Drenar hacia abajo", "Drenar Humedad", "Disipar Viento-Humedad", "Transformar Flema y aliviar tos", "Transformar Humedad", "Reducir estancamiento de alimentos", "Regular Qi", "Regular Sangre", "Calentar el Interior", "Tonificar", "Estabilizar y astringir", "Calmar el Shen", "Abrir orificios", "Extinguir Viento", "Expulsar parásitos", "Uso externo"]
    herb_categories_en = ["Release the Exterior", "Clear Heat", "Drain Downward", "Drain Dampness", "Dispel Wind-Damp", "Transform Phlegm and relieve cough", "Transform Dampness", "Reduce Food Stagnation", "Regulate Qi", "Regulate Blood", "Warm the Interior", "Tonify", "Stabilize and Astringe", "Calm the Spirit", "Open the Orifices", "Extinguish Wind", "Expel Parasites", "External Use"]
    formula_categories_es = ["Liberar Exterior", "Limpiar Calor", "Drenar hacia abajo", "Armonizar", "Drenar Humedad", "Humedecer Sequedad", "Calentar Interior", "Tonificar", "Regular Qi", "Regular Sangre", "Estabilizar y astringir", "Calmar el Shen", "Expulsar Viento", "Abrir orificios", "Transformar Flema", "Reducir alimentos", "Expulsar parásitos"]
    formula_categories_en = ["Release the Exterior", "Clear Heat", "Drain Downward", "Harmonize", "Drain Dampness", "Moisten Dryness", "Warm the Interior", "Tonify", "Regulate Qi", "Regulate Blood", "Stabilize and Astringe", "Calm the Spirit", "Expel Wind", "Open the Orifices", "Transform Phlegm", "Reduce Food Stagnation", "Expel Parasites"]
    herbs = herb_categories_es if es else herb_categories_en
    formulas = formula_categories_es if es else formula_categories_en
    herb_comparison = [
        ("Autoridad y fecha" if es else "Authority and date", "Lista NCCAOM histórica suministrada; varias páginas internas no indican edición." if es else "Supplied historical NCCAOM roster; several internal pages do not identify an edition.", "Apéndices NCBAHM vigentes; comprobar siempre fecha efectiva." if es else "Current NCBAHM appendices; always verify the effective date."),
        ("Organización" if es else "Organization", "Listado plano y categorías internas; mezclaba alcance de examen con notas de seguridad." if es else "Flat roster plus internal categories; exam scope and safety notes were sometimes mixed.", "A: hierbas individuales · B: 51 Duì Yào · C: fórmulas · D: sustancias obsoletas y sustitutos." if es else "A: individual herbs · B: 51 Duì Yào · C: formulas · D: obsolete substances and substitutes."),
        ("Cambio confirmado" if es else "Confirmed change", "El material histórico incluía o mencionaba sustancias hoy inseguras junto a elementos activos." if es else "Historical material included or mentioned now-unsafe substances beside active materia medica.", "Nueve sustancias están separadas como obsoletas en Appendix D: Xi Jiao, Hu Gu, Xiong Dan, Chuan Shan Jia, Guan Mu Tong, Guang Fang Ji, Ma Dou Ling, Zhu Sha y Ying Su Ke." if es else "Nine substances are separated as obsolete in Appendix D: Xi Jiao, Hu Gu, Xiong Dan, Chuan Shan Jia, Guan Mu Tong, Guang Fang Ji, Ma Dou Ling, Zhu Sha and Ying Su Ke."),
        ("Regla para el diff" if es else "Diff rule", "No asumir que una variante de pinyin, parte procesada o sinónimo es una hierba distinta." if es else "Do not treat a pinyin variant, processed form or synonym as a different herb.", "Comparar nombre chino, identidad botánica/mineral/animal y parte; luego clasificar: retenida, renombrada, nueva, retirada u obsoleta." if es else "Compare Chinese name, botanical/mineral/animal identity and part; then classify retained, renamed, added, removed or obsolete."),
    ]
    herb_table = ''.join(f'<tr><th>{a}</th><td>{b}</td><td>{c}</td></tr>' for a,b,c in herb_comparison)
    html += f'''<article class="guide-card"><p class="guide-eyebrow">{'Tarjeta 1 · Apéndice A' if es else 'Card 1 · Appendix A'}</p><h2>{'Nuevo listado de hierbas' if es else 'Current herb roster'}</h2><p>{'Estudia cada entrada por identidad y parte, categoría, naturaleza/sabor/canales, acciones, indicaciones, dosis, preparación y seguridad. Las categorías son un mapa; el Apéndice A oficial decide qué entradas pertenecen al examen.' if es else 'Study each entry by identity and part, category, nature/flavor/channels, actions, indications, dosage, preparation and safety. Categories are a map; official Appendix A controls which entries belong to the exam.'}</p><h3>{'Comparación NCCAOM → NCBAHM' if es else 'NCCAOM → NCBAHM comparison'}</h3><div class="guide-table-wrap"><table class="guide-table"><thead><tr><th>{'Punto' if es else 'Point'}</th><th>NCCAOM</th><th>NCBAHM</th></tr></thead><tbody>{herb_table}</tbody></table></div><div class="guide-tags">{''.join(f'<span class="guide-tag">{escape(x)}</span>' for x in herbs)}</div><div class="guide-card__meta">{'No confundir el número de tarjetas internas con el número de hierbas oficiales. Las altas y bajas individuales se publicarán solo cuando ambos apéndices oficiales fechados estén cotejados identidad por identidad.' if es else 'Do not confuse the number of internal cards with the number of official herbs. Item-level additions and removals will be published only after both dated official appendices are reconciled identity by identity.'}</div><a class="guide-button" href="../comparisons/index{suffix}.html">{'Abrir índice de hierbas' if es else 'Open herb index'} →</a><a class="guide-button guide-button--outline" href="../comparisons/secondary-actions{suffix}.html">{'Acciones secundarias' if es else 'Secondary actions'} →</a></article>'''
    formula_comparison = [
        ("Autoridad" if es else "Authority", "Material histórico identificado como NCCAOM y listas internas del curso." if es else "Historical NCCAOM-labeled material and internal course lists.", "Appendix C de la publicación NCBAHM vigente." if es else "Appendix C of the current NCBAHM publication."),
        ("Qué debe saberse" if es else "Required knowledge", "Los listados internos enfatizaban nombre, traducción y reconocimiento." if es else "Internal rosters emphasized name, translation and recognition.", "Ingredientes, jerarquía, acciones, indicaciones, relaciones sinérgicas, seguridad y selección según el paciente." if es else "Ingredients, hierarchy, actions, indications, synergistic relationships, safety and patient-based selection."),
        ("Coincidencia confirmada" if es else "Confirmed overlap", "El material NCCAOM suministrado estudia las familias Ma Huang Tang, Gui Zhi Tang, Si Jun Zi Tang, Si Wu Tang, Liu Wei Di Huang Wan, Er Chen Tang, Xiao Chai Hu Tang y Cheng Qi." if es else "The supplied NCCAOM material studies the Ma Huang Tang, Gui Zhi Tang, Si Jun Zi Tang, Si Wu Tang, Liu Wei Di Huang Wan, Er Chen Tang, Xiao Chai Hu Tang and Cheng Qi families.", "Esas familias siguen presentes en el conjunto curricular NCBAHM suministrado; debe verificarse cada fórmula concreta contra Appendix C." if es else "Those families remain present in the supplied NCBAHM curriculum set; each exact formula must still be checked against Appendix C."),
        ("Conteos" if es else "Counts", "La lista interna de 85 fórmulas no equivale automáticamente al listado oficial NCCAOM." if es else "The internal 85-formula collection is not automatically the official NCCAOM roster.", "Las 110 comparaciones son ejercicios del sitio, no 110 fórmulas oficiales." if es else "The 110 comparisons are site exercises, not 110 official formulas."),
    ]
    formula_table = ''.join(f'<tr><th>{a}</th><td>{b}</td><td>{c}</td></tr>' for a,b,c in formula_comparison)
    html += f'''<article class="guide-card"><p class="guide-eyebrow">{'Tarjeta 2 · Apéndice C' if es else 'Card 2 · Appendix C'}</p><h2>{'Nuevo listado de fórmulas' if es else 'Current formula roster'}</h2><p>{'Para cada fórmula: ingredientes, jerarquía, acciones, indicaciones, modificaciones, contraindicaciones y contraste con fórmulas cercanas. El Apéndice C oficial controla la inclusión.' if es else 'For each formula: ingredients, hierarchy, actions, indications, modifications, contraindications and contrast with neighboring formulas. Official Appendix C controls inclusion.'}</p><h3>{'Comparación NCCAOM → NCBAHM' if es else 'NCCAOM → NCBAHM comparison'}</h3><div class="guide-table-wrap"><table class="guide-table"><thead><tr><th>{'Punto' if es else 'Point'}</th><th>NCCAOM</th><th>NCBAHM</th></tr></thead><tbody>{formula_table}</tbody></table></div><div class="guide-tags">{''.join(f'<span class="guide-tag">{escape(x)}</span>' for x in formulas)}</div><div class="guide-card__meta">{'Las altas y bajas individuales requieren cotejar el Appendix C fechado con el listado oficial NCCAOM archivado; no se deducen de los 85 resúmenes ni de las 110 comparaciones.' if es else 'Item-level additions and removals require a dated Appendix C and the archived official NCCAOM roster; they are not inferred from the 85 summaries or 110 comparisons.'}</div><a class="guide-button" href="../comparisons/most-important-formulas{suffix}.html">{'Abrir 85 fórmulas' if es else 'Open 85 formulas'} →</a><a class="guide-button guide-button--outline" href="../comparisons/formulas{suffix}.html">{'Abrir comparaciones' if es else 'Open comparisons'} →</a></article>'''
    analysis_rows = [
        ("I", "Materia medica y fórmulas" if es else "Materia medica and formulas", "Reconocer no basta: relacionar propiedades, función y patrón." if es else "Recognition is not enough: connect properties, function and pattern."),
        ("II", "Seguridad y dispensación" if es else "Safety and dispensing", "Procesamiento, dosis, toxicidad, interacciones, embarazo, calidad y sustancias obsoletas." if es else "Processing, dosage, toxicity, interactions, pregnancy, quality and obsolete substances."),
        ("III", "Planificación del tratamiento" if es else "Treatment planning", "Seleccionar, combinar, modificar, sustituir, monitorizar y derivar según la evaluación." if es else "Select, combine, modify, substitute, monitor and refer from the patient assessment."),
    ]
    html += f'''<article class="guide-card guide-card--wide guide-card--purple"><p class="guide-eyebrow">{'Tarjeta 3 · Análisis' if es else 'Card 3 · Analysis'}</p><h2>{'Cómo leer el guideline actual' if es else 'How to read the current guideline'}</h2><div class="guide-table-wrap"><table class="guide-table"><thead><tr><th>{'Bloque' if es else 'Block'}</th><th>{'Responsabilidad' if es else 'Responsibility'}</th><th>{'Aplicación al estudio' if es else 'Study application'}</th></tr></thead><tbody>{''.join(f'<tr><th>{a}</th><td>{b}</td><td>{c}</td></tr>' for a,b,c in analysis_rows)}</tbody></table></div><p><strong>{'Cambio clave:' if es else 'Key change:'}</strong> {'usa NCBAHM como nombre actual y NCCAOM solo para identificar material histórico. No renombres listas antiguas como vigentes sin comprobar edición y fecha.' if es else 'use NCBAHM as the current name and NCCAOM only when identifying historical material. Do not relabel old rosters as current without checking edition and date.'}</p><p><strong>{'Prioridad:' if es else 'Priority:'}</strong> {'combina memorización de Apéndices A–C con seguridad y razonamiento clínico; una lista sola no cubre la competencia.' if es else 'combine Appendix A–C memorization with safety and clinical reasoning; a roster alone does not cover the competency.'}</p><a class="guide-button guide-button--outline" href="{OFFICIAL}" target="_blank" rel="noopener">{'Verificar publicación oficial vigente' if es else 'Verify current official publication'} ↗</a></article></div>'''
    return html + foot(es)

def hub(lang):
    es = lang == "es"
    suffix = "-es" if es else ""
    alternate = "board-guidelines.html" if es else "board-guidelines-es.html"
    css = "guidelines/guidelines.css?v=20261027-1"
    html = f'''<!doctype html><html lang="{lang}"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="description" content="{'Guías de preparación por Board' if es else 'Board preparation guidelines'}"><title>{'Guías de Board' if es else 'Board Guidelines'} | AcupunctureTests</title><link rel="alternate" hreflang="{'en' if es else 'es'}" href="{alternate}"><link rel="stylesheet" href="{css}"></head><body><header class="guide-topbar"><div class="guide-shell guide-topbar__inner"><a class="guide-brand" href="/my/">☯ AcupunctureTests</a><div class="guide-actions"><a class="guide-button guide-button--outline" href="{alternate}">{'English' if es else 'Español'}</a><a class="guide-button guide-button--outline" href="/my/">← Dashboard</a></div></div></header><main class="guide-shell"><section class="guide-hero"><p class="guide-eyebrow">NCBAHM · {'Centro independiente' if es else 'Independent hub'}</p><h1>{'Guidelines por Board' if es else 'Guidelines by Board'}</h1><p>{'Abre la guía del examen que estás preparando. Cada página separa el alcance oficial, el método de estudio y la auditoría de cobertura.' if es else 'Open the guideline for the exam you are preparing. Each page separates official scope, study method and coverage audit.'}</p></section><div class="guide-alert"><strong>{'Control de versión:' if es else 'Version control:'}</strong> {'Revisado el 27 de octubre de 2026. Antes del examen, confirma siempre la versión y fecha efectiva en NCBAHM.' if es else 'Reviewed October 27, 2026. Before the exam, always confirm the version and effective date with NCBAHM.'}</div><div class="guide-grid">'''
    for board in BOARDS:
        title = board["es" if es else "en"]
        summary = board["summary_es" if es else "summary_en"]
        html += f'''<article class="guide-card"><p class="guide-eyebrow">{'Guía de examen' if es else 'Exam guideline'}</p><h2>{escape(title)}</h2><p>{escape(summary)}</p><a class="guide-button" href="guidelines/{board['slug']}{suffix}.html">{'Abrir guideline' if es else 'Open guideline'} →</a></article>'''
    html += f'''<article class="guide-card guide-card--wide guide-card--purple"><p class="guide-eyebrow">{'Requisito adicional de seguridad' if es else 'Additional safety requirement'}</p><h2>Clean Needle Technique</h2><p>{'Se mantiene visible junto a la preparación, pero no se presenta como un quinto examen NCBAHM. La elegibilidad y la certificación deben comprobarse con la entidad que administre el requisito vigente.' if es else 'It remains visible beside Board preparation, but is not presented as a fifth NCBAHM examination. Eligibility and certification must be confirmed with the organization administering the current requirement.'}</p><a class="guide-button guide-button--outline" href="/blocks/myprogress/study-guide.php?area=cleanneedle">{'Abrir recursos CNT' if es else 'Open CNT resources'} →</a></article></div>'''
    return html + foot(es)

def main():
    OUT.mkdir(parents=True, exist_ok=True)
    (ROOT / "study-guide" / "board-guidelines.html").write_text(hub("en"), encoding="utf-8")
    (ROOT / "study-guide" / "board-guidelines-es.html").write_text(hub("es"), encoding="utf-8")
    for board in BOARDS:
        (OUT / f'{board["slug"]}.html').write_text(board_page(board, "en"), encoding="utf-8")
        (OUT / f'{board["slug"]}-es.html').write_text(board_page(board, "es"), encoding="utf-8")
    (OUT / "chinese-herbal-medicine-cards.html").write_text(herbal_cards("en"), encoding="utf-8")
    (OUT / "chinese-herbal-medicine-cards-es.html").write_text(herbal_cards("es"), encoding="utf-8")

if __name__ == "__main__":
    main()
