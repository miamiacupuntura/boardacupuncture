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
    "chinese-herbal-medicine": [
        ("Open Board course", "Abrir curso de Board", "/blocks/myprogress/study-guide.php?area=herbology", "/blocks/myprogress/study-guide.php?area=herbology"),
        ("Three roster cards", "Tres tarjetas de listados", "chinese-herbal-medicine-cards.html", "chinese-herbal-medicine-cards-es.html"),
        ("51 Duì Yào pairs", "51 parejas Duì Yào", "../comparisons/dui-yao.html", "../comparisons/dui-yao-es.html"),
        ("Formula composition", "Composición de fórmulas", "../comparisons/formula-theory.html", "../comparisons/formula-theory-es.html"),
        ("Safety Library", "Biblioteca de seguridad", "../comparisons/safety.html", "../comparisons/safety-es.html"),
    ],
    "biomedicine": [
        ("Open Board course", "Abrir curso de Board", "/blocks/myprogress/study-guide.php?area=biomedicine", "/blocks/myprogress/study-guide.php?area=biomedicine"),
    ],
}

def top(lang, title, alternate, back="../board-guidelines"):
    es = lang == "es"
    suffix = "-es.html" if es else ".html"
    return f'''<!doctype html><html lang="{lang}"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="description" content="{escape(title)}"><title>{escape(title)} | AcupunctureTests</title><link rel="alternate" hreflang="{'en' if es else 'es'}" href="{alternate}"><link rel="stylesheet" href="guidelines.css?v=20261027-2"></head><body><header class="guide-topbar"><div class="guide-shell guide-topbar__inner"><a class="guide-brand" href="{back}{suffix}">☯ AcupunctureTests · {'Guías de Board' if es else 'Board Guidelines'}</a><div class="guide-actions"><a class="guide-button guide-button--outline" href="{alternate}">{'English' if es else 'Español'}</a><a class="guide-button guide-button--outline" href="/my/">← Dashboard</a></div></div></header><main class="guide-shell">'''

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
    html += '</div>'
    audit = AUDITS[board["slug"]]
    html += f'''<section class="guide-section"><p class="guide-eyebrow">{'Control de cobertura' if es else 'Coverage control'}</p><h2>{'Auditoría práctica del Board' if es else 'Practical Board audit'}</h2><p>{'Marca un bloque como cubierto solo cuando puedas resolverlo dentro de un caso, no solamente reconocer el término.' if es else 'Mark a block covered only when you can solve it inside a case, not merely recognize the term.'}</p><div class="guide-flow">'''
    for en_label, es_label, en_body, es_body in audit:
        html += f'''<article class="guide-flow__item"><span class="guide-status">{'Verificar' if es else 'Verify'}</span><h3>{escape(es_label if es else en_label)}</h3><p>{escape(es_body if es else en_body)}</p></article>'''
    html += '</div></section>'
    html += f'''<section class="guide-section"><h2>{'Recursos recomendados' if es else 'Recommended resources'}</h2><div class="guide-resource-list">'''
    for en_label, es_label, en_href, es_href in RESOURCES[board["slug"]]:
        href = es_href if es else en_href
        external = href.startswith("http")
        html += f'''<a class="guide-resource" href="{href}"{' target="_blank" rel="noopener"' if external else ''}><span>{escape(es_label if es else en_label)}</span><strong>→</strong></a>'''
    html += '</div></section>'
    html += f'''<section class="guide-section"><h2>{'Fuente y control de versión' if es else 'Source and version control'}</h2><p>{'Consulta la publicación oficial antes de cambiar el plan de estudio. La fecha de revisión de esta página no convierte el material educativo en una publicación del Board.' if es else 'Check the official publication before changing the study plan. This page’s review date does not make the educational material a Board publication.'}</p><a class="guide-button guide-button--outline" href="{source}" target="_blank" rel="noopener">{'Abrir fuente oficial' if es else 'Open official source'} ↗</a></section>'''
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

def crosswalk(lang):
    es = lang == "es"
    alternate = "nccaom-ncbahm-crosswalk.html" if es else "nccaom-ncbahm-crosswalk-es.html"
    html = top(lang, "Crosswalk NCCAOM a NCBAHM" if es else "NCCAOM to NCBAHM Crosswalk", alternate)
    html += f'''<section class="guide-hero"><p class="guide-eyebrow">{'Control de transición' if es else 'Transition control'}</p><h1>NCCAOM → NCBAHM</h1><p>{'Una guía para migrar materiales históricos al alcance actual sin convertir recursos internos en requisitos oficiales.' if es else 'A guide for migrating historical material to the current scope without turning internal resources into official requirements.'}</p></section><div class="guide-alert"><strong>{'Regla principal:' if es else 'Primary rule:'}</strong> {'el documento oficial vigente y fechado controla; NCCAOM identifica material histórico y NCBAHM identifica la organización actual.' if es else 'the dated current official document controls; NCCAOM identifies historical material and NCBAHM identifies the current organization.'}</div>'''
    levels = [
        ("1", "Fuente oficial vigente" if es else "Current official source", "Temario, apéndice o manual del candidato con fecha efectiva. Es la autoridad para alcance y elegibilidad." if es else "Content outline, appendix or candidate handbook with an effective date. This controls scope and eligibility."),
        ("2", "Archivo NCCAOM" if es else "Archived NCCAOM source", "Sirve para detectar continuidad o cambio, pero no debe etiquetarse como vigente sin cotejo." if es else "Useful for detecting continuity or change, but it must not be labeled current without reconciliation."),
        ("3", "Recurso educativo" if es else "Educational resource", "Tarjetas, comparaciones y conteos del sitio apoyan el aprendizaje; no definen por sí solos el listado oficial." if es else "Site cards, comparisons and counts support learning; they do not independently define the official roster."),
    ]
    html += '<div class="guide-grid">'
    for number, title, body in levels:
        html += f'''<article class="guide-card"><p class="guide-eyebrow">{'Nivel' if es else 'Level'} {number}</p><h2>{title}</h2><p>{body}</p></article>'''
    html += '</div>'
    board_rows = [
        ("Foundations of Oriental Medicine", "Teoría, diagnóstico, patrones y principios de tratamiento." if es else "Theory, diagnosis, patterns and treatment principles.", "Confirmar edición; no trasladar porcentajes de otro Board." if es else "Confirm the edition; do not transfer percentages from another Board."),
        ("Acupuncture with Point Location", "Temario vigente desde el 1 de septiembre de 2026: 15% localización, 15% seguridad, 40% planificación y 30% técnicas." if es else "Outline effective September 1, 2026: 15% location, 15% safety, 40% planning and 30% techniques.", "Usar esos pesos para distribuir práctica y simulacros." if es else "Use those weights to distribute practice and mock exams."),
        ("Chinese Herbal Medicine", "Apéndices A: hierbas, B: 51 Duì Yào, C: fórmulas y D: sustancias obsoletas/sustituciones." if es else "Appendices A: herbs, B: 51 Duì Yào, C: formulas and D: obsolete substances/substitutions.", "Cotejar identidad y parte; separar lista, competencia clínica y seguridad." if es else "Reconcile identity and part; separate roster, clinical competency and safety."),
        ("Biomedicine", "Ciencias biomédicas, evaluación, diagnóstico, tratamiento, banderas rojas y derivación." if es else "Biomedical sciences, assessment, diagnosis, treatment, red flags and referral.", "Confirmar el temario vigente antes de asignar pesos." if es else "Confirm the current outline before assigning weights."),
    ]
    html += f'''<section class="guide-section"><p class="guide-eyebrow">{'Mapa por Board' if es else 'Board map'}</p><h2>{'Qué se conserva y qué debe verificarse' if es else 'What carries forward and what must be verified'}</h2><div class="guide-table-wrap"><table class="guide-table"><thead><tr><th>Board</th><th>{'Alcance de trabajo' if es else 'Working scope'}</th><th>{'Acción de transición' if es else 'Transition action'}</th></tr></thead><tbody>'''
    for board, scope, action in board_rows:
        html += f'<tr><th>{board}</th><td>{scope}</td><td>{action}</td></tr>'
    html += '</tbody></table></div></section>'
    confirmed = [
        "La sigla vigente es NCBAHM; NCCAOM queda como referencia histórica." if es else "The current acronym is NCBAHM; NCCAOM remains a historical reference.",
        "Herbal Medicine usa la estructura de Apéndices A–D y Appendix B contiene 51 parejas Duì Yào." if es else "Herbal Medicine uses the Appendix A–D structure and Appendix B contains 51 Duì Yào pairs.",
        "Appendix D separa nueve sustancias obsoletas en el material vigente suministrado." if es else "Appendix D separates nine obsolete substances in the supplied current material.",
        "El nuevo temario ACPL entra en vigor el 1 de septiembre de 2026 y publica cuatro pesos explícitos." if es else "The new ACPL outline takes effect September 1, 2026 and publishes four explicit weights.",
    ]
    boundaries = [
        "247 notas comparativas no significan 247 hierbas oficiales." if es else "247 comparative notes do not mean 247 official herbs.",
        "110 comparaciones de fórmulas no significan 110 fórmulas oficiales." if es else "110 formula comparisons do not mean 110 official formulas.",
        "85 resúmenes de fórmulas son una colección interna de alto rendimiento, no un conteo oficial." if es else "85 formula summaries are an internal high-yield collection, not an official count.",
        "Una variante de pinyin, sinónimo o forma procesada no constituye automáticamente una adición o eliminación." if es else "A pinyin variant, synonym or processed form is not automatically an addition or removal.",
    ]
    html += f'''<div class="guide-grid"><article class="guide-card"><p class="guide-eyebrow">{'Confirmado' if es else 'Confirmed'}</p><h2>{'Hallazgos publicables' if es else 'Publishable findings'}</h2><ul>{''.join(f'<li>{x}</li>' for x in confirmed)}</ul></article><article class="guide-card"><p class="guide-eyebrow">{'Límites' if es else 'Boundaries'}</p><h2>{'No inferir de los conteos internos' if es else 'Do not infer from internal counts'}</h2><ul>{''.join(f'<li>{x}</li>' for x in boundaries)}</ul></article></div>'''
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
    css = "guidelines/guidelines.css?v=20261027-2"
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
