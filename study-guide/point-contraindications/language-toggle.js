(function () {
  'use strict';

  const button = document.getElementById('language-toggle');
  if (!button) return;

  const exact = new Map(Object.entries({
    'Skip to study content': 'Saltar al contenido de estudio',
    'Study Navigation': 'Navegación de estudio',
    'Start here': 'Comienza aquí',
    'Introduction': 'Introducción',
    'Two learning paths': 'Dos rutas de aprendizaje',
    'Starting reflection': 'Reflexión inicial',
    'Course overview': 'Descripción general del curso',
    'Safety boundary': 'Límite de seguridad',
    'Source chart guide': 'Guía de la tabla fuente',
    'Learning objectives': 'Objetivos de aprendizaje',
    'General cautions': 'Precauciones generales',
    'Modalities': 'Modalidades',
    'Regional risk comparison': 'Comparación de riesgos regionales',
    'Integrated safety case': 'Caso integrado de seguridad',
    'Practice & review': 'Práctica y repaso',
    'Interactive flashcards': 'Tarjetas interactivas',
    'Memory tools': 'Herramientas de memoria',
    '12-question integrated practice': 'Práctica integrada de 12 preguntas',
    'School Level Exam': 'Examen de nivel escolar',
    'Board Level Exam': 'Examen de nivel Board',
    'Continuing Education begins here': 'Continuing Education comienza aquí',
    'Continuing Education review questions': 'Preguntas de repaso de Continuing Education',
    'Certificate preview': 'Vista previa del certificado',
    'Continuing Education assessment': 'Evaluación de Continuing Education',
    'Final reflection': 'Reflexión final',
    'References': 'Referencias',
    'STUDY GUIDE · POINT CONTRAINDICATIONS': 'GUÍA DE ESTUDIO · CONTRAINDICACIONES DE PUNTOS',
    'Acupuncture & Point Location': 'Acupuntura y localización de puntos',
    'Point Contraindications': 'Contraindicaciones de puntos',
    'Recognize the type of precaution, connect it to relevant anatomy or clinical context, and distinguish current safety principles from source-specific historical restrictions.': 'Reconoce el tipo de precaución, relaciónalo con la anatomía o el contexto clínico pertinente y distingue los principios actuales de seguridad de las restricciones históricas específicas de una fuente.',
    '← Back to Study Guide index': '← Volver al índice de Study Guide',
    'Continuing Education': 'Continuing Education',
    'Continuing Education Exam': 'Examen de Continuing Education',
    'CHAPTER LEARNING PATHS': 'RUTAS DE APRENDIZAJE DEL CAPÍTULO',
    'One chapter, two learning paths': 'Un capítulo, dos rutas de aprendizaje',
    'Board Exam Preparation': 'Preparación para el examen Board',
    'Start Board preparation': 'Comenzar la preparación Board',
    'Open the CE learning plan': 'Abrir el plan de aprendizaje CE',
    'Objective 1': 'Objetivo 1',
    'Objective 2': 'Objetivo 2',
    'Objective 3': 'Objetivo 3',
    'Objective 4': 'Objetivo 4',
    'Objective 5': 'Objetivo 5',
    'Objective 6': 'Objetivo 6',
    'Objective 7': 'Objetivo 7',
    'Objective 8': 'Objetivo 8',
    'Objective 9': 'Objetivo 9',
    'Objective 10': 'Objetivo 10',
    'Objective 11': 'Objetivo 11',
    'Objective 12': 'Objetivo 12',
    'General point cautions and contraindications': 'Precauciones y contraindicaciones generales de los puntos',
    'Du Mai channel cautions': 'Precauciones del canal Du Mai',
    'Gall Bladder channel cautions': 'Precauciones del canal de Vesícula Biliar',
    'Kidney channel cautions': 'Precauciones del canal de Riñón',
    'Liver channel cautions': 'Precauciones del canal de Hígado',
    'Pericardium channel cautions': 'Precauciones del canal de Pericardio',
    'Ren Mai cautions': 'Precauciones de Ren Mai',
    'San Jiao channel cautions': 'Precauciones del canal San Jiao',
    'Urinary Bladder channel cautions': 'Precauciones del canal de Vejiga Urinaria',
    'Precautions for modalities': 'Precauciones para las modalidades',
    'Compare regional risk patterns': 'Comparar patrones regionales de riesgo',
    'Apply integrated safety reasoning': 'Aplicar el razonamiento integrado de seguridad',
    'Exam': 'Examen',
    'Attempts': 'Intentos',
    'Last Attempt': 'Último intento',
    'Average': 'Promedio',
    'Grade': 'Calificación',
    'Action': 'Acción',
    'Start Exam': 'Comenzar examen',
    'Start Mock 10': 'Comenzar Mock 10',
    'View Exam History': 'Ver historial de exámenes',
    'Continuing Education Review Questions (10)': 'Preguntas de repaso de Continuing Education (10)',
    'Check answer': 'Revisar respuesta',
    'Select one answer first.': 'Selecciona primero una respuesta.',
    'Correct.': 'Correcto.',
    'Not yet. Review the rationale below.': 'Aún no. Revisa la explicación a continuación.',
    'Why the other options are incorrect': 'Por qué las otras opciones son incorrectas',
    'Copy question bank': 'Copiar banco de preguntas',
    'Download TXT': 'Descargar TXT',
    'Start a new attempt': 'Comenzar un nuevo intento',
    'Certificate of Completion': 'Certificado de finalización',
    'Student Name': 'Nombre del estudiante',
    'Completion date': 'Fecha de finalización',
    'Certificate ID': 'Identificación del certificado',
    'Provider / approval': 'Proveedor / aprobación',
    'CONTINUING EDUCATION · STRUCTURED LEARNING PLAN': 'CONTINUING EDUCATION · PLAN DE APRENDIZAJE ESTRUCTURADO',
    'Learner Objectives': 'Objetivos del estudiante',
    'Subject Matter': 'Contenido temático',
    'Teaching Method': 'Método de enseñanza',
    'Class Hours': 'Horas de clase',
    'Total planned learning time': 'Tiempo total de aprendizaje planificado'
  }));

  const originals = new WeakMap();
  const ignored = 'script,style,textarea,input,option';
  let spanish = false;
  let busy = false;

  function translateValue(value) {
    const leading = value.match(/^\s*/)[0];
    const trailing = value.match(/\s*$/)[0];
    const key = value.trim();
    if (!key) return value;
    if (exact.has(key)) return leading + exact.get(key) + trailing;
    let match = key.match(/^(\d+) of (\d+) checked · (\d+) correct$/);
    if (match) return `${leading}${match[1]} de ${match[2]} revisadas · ${match[3]} correctas${trailing}`;
    match = key.match(/^(\d+) of (\d+) answered$/);
    if (match) return `${leading}${match[1]} de ${match[2]} respondidas${trailing}`;
    match = key.match(/^Correct answer:\s*(.*)$/);
    if (match) return `${leading}Respuesta correcta: ${match[1]}${trailing}`;
    match = key.match(/^General feedback:\s*(.*)$/);
    if (match) return `${leading}Retroalimentación general: ${match[1]}${trailing}`;
    const localizedCourseTitle = title => title === 'Point Contraindications' ? 'Contraindicaciones de puntos' : title;
    match = key.match(/^School Level Exam — (.*)$/);
    if (match) return `${leading}Examen de nivel escolar — ${localizedCourseTitle(match[1])}${trailing}`;
    match = key.match(/^Board Level Exam — (.*)$/);
    if (match) return `${leading}Examen de nivel Board — ${localizedCourseTitle(match[1])}${trailing}`;
    match = key.match(/^Continuing Education Exam — (.*)$/);
    if (match) return `${leading}Examen de Continuing Education — ${localizedCourseTitle(match[1])}${trailing}`;
    match = key.match(/^Continuing Education Learning Plan — (.*)$/);
    if (match) return `${leading}Plan de aprendizaje de Continuing Education — ${localizedCourseTitle(match[1])}${trailing}`;
    return value;
  }

  function translateNode(node) {
    if (node.nodeType === Node.TEXT_NODE) {
      if (node.parentElement && node.parentElement.closest(ignored)) return;
      if (!originals.has(node)) originals.set(node, node.nodeValue);
      node.nodeValue = translateValue(originals.get(node));
      return;
    }
    if (node.nodeType !== Node.ELEMENT_NODE) return;
    node.childNodes.forEach(translateNode);
  }

  function restoreNode(node) {
    if (node.nodeType === Node.TEXT_NODE) {
      if (originals.has(node)) node.nodeValue = originals.get(node);
      return;
    }
    if (node.nodeType !== Node.ELEMENT_NODE) return;
    node.childNodes.forEach(restoreNode);
  }

  function applyLanguage(useSpanish) {
    spanish = useSpanish;
    busy = true;
    if (spanish) translateNode(document.body); else restoreNode(document.body);
    document.documentElement.lang = spanish ? 'es' : 'en';
    button.textContent = spanish ? 'English' : 'Español';
    button.setAttribute('aria-label', spanish ? 'View this chapter in English' : 'Ver este capítulo en español');
    try { localStorage.setItem('point-contraindications-language', spanish ? 'es' : 'en'); } catch (_) {}
    busy = false;
    document.dispatchEvent(new CustomEvent('chapterlanguagechange', {detail: {language: spanish ? 'es' : 'en'}}));
  }

  button.addEventListener('click', () => applyLanguage(!spanish));
  new MutationObserver(records => {
    if (!spanish || busy) return;
    busy = true;
    records.forEach(record => record.addedNodes.forEach(translateNode));
    busy = false;
  }).observe(document.body, {childList: true, subtree: true});

  let saved = 'en';
  try { saved = localStorage.getItem('point-contraindications-language') || 'en'; } catch (_) {}
  if (new URLSearchParams(location.search).get('lang') === 'es') saved = 'es';
  applyLanguage(saved === 'es');
})();
