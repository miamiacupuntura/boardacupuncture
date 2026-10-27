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
    "chinese-herbal-medicine": [
        ("Open Board course", "Abrir curso de Board", "/blocks/myprogress/study-guide.php?area=herbology", "/blocks/myprogress/study-guide.php?area=herbology"),
        ("Orientation · Route, changes and priorities", "Orientación · Ruta, cambios y prioridades", "chinese-herbal-medicine-cards.html", "chinese-herbal-medicine-cards-es.html"),
        ("Step 1 · Individual herbs", "Paso 1 · Hierbas individuales", "../comparisons/index.html", "../comparisons/index-es.html"),
        ("Step 2 · 51 Duì Yào pairs", "Paso 2 · 51 parejas Duì Yào", "../comparisons/dui-yao.html", "../comparisons/dui-yao-es.html"),
        ("Step 3 · Formula composition", "Paso 3 · Composición de fórmulas", "../comparisons/formula-theory.html", "../comparisons/formula-theory-es.html"),
        ("Safety throughout the route", "Seguridad durante toda la ruta", "../comparisons/safety.html", "../comparisons/safety-es.html"),
    ],
    "biomedicine": [
        ("Open Board course", "Abrir curso de Board", "/blocks/myprogress/study-guide.php?area=biomedicine", "/blocks/myprogress/study-guide.php?area=biomedicine"),
    ],
}

def top(lang, title, alternate, back="../board-guidelines"):
    es = lang == "es"
    suffix = "-es.html" if es else ".html"
    return f'''<!doctype html><html lang="{lang}"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="description" content="{escape(title)}"><title>{escape(title)} | AcupunctureTests</title><link rel="alternate" hreflang="{'en' if es else 'es'}" href="{alternate}"><link rel="stylesheet" href="guidelines.css?v=20261027-4"></head><body><header class="guide-topbar"><div class="guide-shell guide-topbar__inner"><a class="guide-brand" href="{back}{suffix}">☯ AcupunctureTests · {'Guías de Board' if es else 'Board Guidelines'}</a><div class="guide-actions"><a class="guide-button guide-button--outline" href="{alternate}">{'English' if es else 'Español'}</a><a class="guide-button guide-button--outline" href="/my/">← Dashboard</a></div></div></header><main class="guide-shell">'''

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
    herbal = board["slug"] == "chinese-herbal-medicine"
    html = top(lang, title, alternate)
    hero_actions = ''
    if herbal:
        hero_actions = f'''<div class="guide-actions guide-hero__actions"><a class="guide-button" href="../comparisons/index{suffix}.html">{'Comenzar Paso 1: hierbas individuales' if es else 'Start Step 1: individual herbs'} →</a><a class="guide-button guide-button--outline" href="chinese-herbal-medicine-cards{suffix}.html">{'Ver ruta, cambios y prioridades' if es else 'View route, changes and priorities'} →</a></div>'''
    html += f'''<section class="guide-hero"><p class="guide-eyebrow">{'Guía de Board' if es else 'Board guideline'}</p><h1>{escape(title)}</h1><p>{escape(summary)}</p>{hero_actions}</section>'''
    if herbal:
        route_items = [
            ("0", "Orientación" if es else "Orientation", "Ruta y prioridades" if es else "Route and priorities", f"chinese-herbal-medicine-cards{suffix}.html", "guide-route__item--orientation"),
            ("1", "Hierbas" if es else "Herbs", "Empieza aquí" if es else "Start here", f"../comparisons/index{suffix}.html", "guide-route__item--start"),
            ("2", "Duì Yào", "51 parejas" if es else "51 pairs", f"../comparisons/dui-yao{suffix}.html", ""),
            ("3", "Fórmulas" if es else "Formulas", "Composición y comparación" if es else "Composition and comparison", f"../comparisons/formula-theory{suffix}.html", ""),
            ("D", "Seguridad" if es else "Safety", "Durante toda la ruta" if es else "Throughout the route", f"../comparisons/safety{suffix}.html", "guide-route__item--safety"),
        ]
        html += f'''<section class="guide-route" aria-labelledby="herbal-route-title"><div class="guide-route__heading"><div><p class="guide-eyebrow">{'Navegación del módulo' if es else 'Module navigation'}</p><h2 id="herbal-route-title">{'Ruta de estudio' if es else 'Study route'}</h2></div><p>{'Sigue los pasos 1–3 en orden. Consulta seguridad en cada paso.' if es else 'Follow steps 1–3 in order. Check safety at every step.'}</p></div><nav class="guide-route__items" aria-label="{'Ruta de estudio de Medicina Herbal' if es else 'Chinese Herbal Medicine study route'}">'''
        for number, label, hint, href, modifier in route_items:
            html += f'''<a class="guide-route__item {modifier}" href="{href}"><span class="guide-route__number">{number}</span><span><strong>{label}</strong><small>{hint}</small></span></a>'''
        html += '</nav></section>'
        html += f'''<div class="guide-alert"><strong>{'¿Para qué sirve este módulo?' if es else 'What is this module for?'}</strong> {'Te indica qué debes estudiar, en qué orden abrir los recursos y qué debes ser capaz de hacer antes del examen. Las tres tarjetas orientan el recorrido; el estudio comienza en A · Hierbas, continúa en B · Duì Yào y C · Fórmulas, con D · Seguridad integrado en todas las etapas.' if es else 'It tells you what to study, which resources to open in what order, and what you must be able to do before the exam. The three cards orient the route; study begins with A · Herbs, continues through B · Duì Yào and C · Formulas, with D · Safety integrated throughout.'}</div>'''
        cards = [
            ("1", "Qué vas a estudiar" if es else "What you will study", [
                "A · Hierbas: diferéncialas por propiedades, acciones, indicaciones, dosis y seguridad." if es else "A · Herbs: distinguish them by properties, actions, indications, dosage and safety.",
                "B · Duì Yào: comprende por qué se combinan las 51 parejas y qué aporta cada hierba." if es else "B · Duì Yào: understand why all 51 pairs combine and what each herb contributes.",
                "C · Fórmulas: conecta ingredientes y jerarquía con patrón, uso y modificación." if es else "C · Formulas: connect ingredients and hierarchy to pattern, use and modification.",
                "D · Seguridad: reconoce toxicidad, interacciones, contraindicaciones y sustituciones." if es else "D · Safety: recognize toxicity, interactions, contraindications and substitutions.",
            ]),
            ("2", "Ruta recomendada" if es else "Recommended route", [
                "1. Abre las 3 tarjetas para ubicar listados, cambios y prioridades." if es else "1. Open the 3 cards to understand rosters, changes and priorities.",
                "2. Estudia hierbas individuales antes de comparar parejas y fórmulas." if es else "2. Study individual herbs before comparing pairs and formulas.",
                "3. Continúa con Duì Yào y luego con composición y comparación de fórmulas." if es else "3. Continue with Duì Yào, then formula composition and comparisons.",
                "4. Revisa seguridad durante todo el recorrido, no solo al final." if es else "4. Review safety throughout the route, not only at the end.",
            ]),
            ("3", "Tu meta al terminar" if es else "Your completion goal", [
                "Distinguir opciones parecidas usando la pista decisiva del caso." if es else "Distinguish similar options using the decisive clue in the case.",
                "Explicar la función de cada hierba dentro de una pareja o fórmula." if es else "Explain each herb’s role inside a pair or formula.",
                "Seleccionar una fórmula desde el patrón y justificar modificaciones." if es else "Select a formula from the pattern and justify modifications.",
                "Detectar cuándo una elección es insegura, obsoleta o requiere sustitución." if es else "Detect when a choice is unsafe, obsolete or requires substitution.",
            ]),
        ]
    else:
        html += f'''<div class="guide-alert"><strong>{'Límite de la guía:' if es else 'Guide boundary:'}</strong> {'Este mapa organiza el estudio; no reemplaza el manual del candidato, el temario oficial ni las reglas de elegibilidad.' if es else 'This map organizes study; it does not replace the candidate handbook, official content outline or eligibility rules.'}</div>'''
        cards = [
            ("1", "Alcance principal" if es else "Core scope", scope),
            ("2", "Método de estudio" if es else "Study method", [study]),
            ("3", "Auditoría antes del examen" if es else "Pre-exam audit", ["Confirmar versión y fecha del temario oficial." if es else "Confirm the official outline version and effective date.", "Relacionar cada recurso del curso con una competencia del temario." if es else "Map every course resource to an outline competency.", "Separar contenido oficial, material educativo y reglas clínicas o legales." if es else "Separate official scope, educational material, and clinical or legal rules."]),
        ]
    html += f'<div class="guide-grid{" guide-grid--three" if herbal else ""}">'
    for number, heading, rows in cards:
        html += f'<article class="guide-card"><p class="guide-eyebrow">{number} · {"Paso" if es else "Step"}</p><h2>{heading}</h2><ul>' + ''.join(f'<li>{escape(x)}</li>' for x in rows) + '</ul></article>'
    if herbal:
        html += f'''<article class="guide-card guide-card--wide guide-card--purple"><p class="guide-eyebrow">{'Punto de entrada' if es else 'Entry point'}</p><h2>{'Comienza con el mapa de los listados' if es else 'Begin with the roster map'}</h2><p>{'Las tres tarjetas explican el listado de hierbas, el listado de fórmulas y cómo interpretar el cambio de NCCAOM a NCBAHM. Después vuelve aquí para seguir la ruta A–D.' if es else 'The three cards explain the herb roster, formula roster and how to interpret the transition from NCCAOM to NCBAHM. Then return here to follow the A–D route.'}</p><div class="guide-actions"><a class="guide-button" href="chinese-herbal-medicine-cards{suffix}.html">{'Abrir las 3 tarjetas' if es else 'Open the 3 cards'} →</a><a class="guide-button guide-button--outline" href="nccaom-ncbahm-crosswalk{suffix}.html">{'Entender el cambio NCCAOM → NCBAHM' if es else 'Understand the NCCAOM → NCBAHM transition'} →</a></div></article>'''
    html += '</div>'
    audit = AUDITS[board["slug"]]
    audit_heading = ("Comprueba lo que ya puedes hacer" if es else "Check what you can already do") if herbal else ("Auditoría práctica del Board" if es else "Practical Board audit")
    audit_intro = ("No marques un bloque como terminado por haber leído las tarjetas. Márcalo cuando puedas aplicar el conocimiento a una pregunta clínica y explicar por qué las otras opciones son incorrectas." if es else "Do not mark a block complete because you read the cards. Mark it when you can apply the knowledge to a clinical question and explain why the other options are wrong.") if herbal else ("Marca un bloque como cubierto solo cuando puedas resolverlo dentro de un caso, no solamente reconocer el término." if es else "Mark a block covered only when you can solve it inside a case, not merely recognize the term.")
    html += f'''<section class="guide-section"><p class="guide-eyebrow">{'Control de progreso' if herbal and es else 'Progress check' if herbal else 'Control de cobertura' if es else 'Coverage control'}</p><h2>{audit_heading}</h2><p>{audit_intro}</p><div class="guide-flow">'''
    for en_label, es_label, en_body, es_body in audit:
        status_label = ("Debes poder" if es else "You can") if herbal else ("Verificar" if es else "Verify")
        html += f'''<article class="guide-flow__item"><span class="guide-status">{status_label}</span><h3>{escape(es_label if es else en_label)}</h3><p>{escape(es_body if es else en_body)}</p></article>'''
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
    alternate = "nccaom-ncbahm-crosswalk.html" if es else "nccaom-ncbahm-crosswalk-es.html"
    html = top(lang, "Crosswalk NCCAOM a NCBAHM" if es else "NCCAOM to NCBAHM Crosswalk", alternate)
    html += f'''<section class="guide-hero"><p class="guide-eyebrow">{'Ajuste del plan de estudio' if es else 'Study plan adjustment'}</p><h1>NCCAOM → NCBAHM</h1><p>{'Una guía para saber qué conocimientos conservar, qué dejar de estudiar como contenido activo y dónde aumentar la práctica para el Board actual.' if es else 'A guide to what knowledge to retain, what to stop studying as active content and where to increase practice for the current Board.'}</p></section><div class="guide-alert"><strong>{'Objetivo:' if es else 'Goal:'}</strong> {'reorganizar tu preparación alrededor del alcance NCBAHM actual sin perder los fundamentos que siguen siendo aplicables.' if es else 'reorganize your preparation around the current NCBAHM scope without losing foundations that still apply.'}</div>'''
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
