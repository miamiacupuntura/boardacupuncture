(() => {
  'use strict';

  const bar = document.querySelector('.preview-view-switch');
  const nav = document.querySelector('nav[aria-label="Study Guide sections"]');
  const root = nav?.closest('main.sgv2-layout');
  if (!bar || !root || !nav) return;
  const buttons = [...bar.querySelectorAll('button')];
  const spanish = buttons.find(button => button.textContent.trim() === 'Estudiante' || button.textContent.trim() === 'Español');
  const student = buttons.find(button => button.textContent.trim() === 'Student');
  const instructor = buttons.find(button => button.textContent.trim() === 'Instructor');
  if (!spanish || !student || !instructor) return;

  const exact = new Map(Object.entries({
    'Study Navigation':'Navegación de estudio','Start here':'Comienza aquí','Introduction':'Introducción','Course Overview':'Descripción del curso','Learning Plan':'Plan de aprendizaje','Chapter Reading':'Lectura del capítulo','NCBAHM Alignment':'Correspondencia con NCBAHM','Objectives':'Objetivos','Chapter resources':'Recursos del capítulo','Practice & exams':'Práctica y exámenes','Visual Review Gallery':'Galería de repaso visual','Continuing Education':'Educación continua','Self-Test':'Autoevaluación','Board Simulation':'Simulación del Board','Exam History & Start Exam':'Historial e inicio de exámenes','Mock Exams':'Exámenes simulados','Review Objectives':'Repasar objetivos','My Student Notes':'Mis notas de estudiante',
    'STUDY GUIDE · CHANNELS AND COLLATERALS':'GUÍA DE ESTUDIO · CANALES Y COLATERALES','Acupuncture & Point Location':'Acupuntura y localización de puntos','The Channels and Collaterals':'Los canales y colaterales','Connect channel functions, pathways and relationships across nine focused objectives.':'Relaciona las funciones, trayectos y conexiones de los canales mediante nueve objetivos específicos.','← Back to course':'← Volver al curso','Start Exam':'Comenzar examen','Chapter overview — pathway directions, not a point-location or needling guide.':'Descripción conceptual del capítulo: muestra direcciones de trayectos; no es una guía de localización de puntos ni de técnica de punción.',
    'How this Chapter aligns with the NCBAHM Exam':'Cómo se relaciona este capítulo con el examen NCBAHM','This chapter supports foundational channel-and-collateral knowledge used in acupuncture point location, treatment planning and point selection. It does not predict a specific examination item.':'Este capítulo apoya los conocimientos fundamentales sobre canales y colaterales utilizados en la localización de puntos, la planificación del tratamiento y la selección de puntos. No predice preguntas específicas del examen.','Study task':'Tarea de estudio','Board-relevant ability':'Capacidad relevante para el Board',
    'Learning Plan':'Plan de aprendizaje','THE CHANNELS AND COLLATERALS — Learning Plan':'LOS CANALES Y COLATERALES — Plan de aprendizaje','Learner Objectives':'Objetivos de aprendizaje','Subject Matter':'Contenido','Teaching Method':'Método de enseñanza','Class Hours':'Horas de clase','To be estimated':'Por determinar',
    'Objective 1 — The Functions of the Channels':'Objetivo 1 — Las funciones de los canales','Objective 2 — The Twelve Primary Channels':'Objetivo 2 — Los doce canales principales','Objective 3 — The Twelve Divergent Channels':'Objetivo 3 — Los doce canales divergentes','Objective 4 — The Eight Extraordinary Vessels':'Objetivo 4 — Los ocho vasos extraordinarios','Objective 5 — The Luo-Connecting Channels':'Objetivo 5 — Los canales Luo-Connecting','Objective 6 — The Twelve Sinew Channels':'Objetivo 6 — Los doce canales tendinomusculares','Objective 7 — The Minute Collaterals':'Objetivo 7 — Los colaterales diminutos','Objective 8 — The Twelve Cutaneous Regions':'Objetivo 8 — Las doce regiones cutáneas','Objective 9 — Integrated Chapter Review: Comparing the Channel Systems':'Objetivo 9 — Repaso integrado: comparación de los sistemas de canales',
    'Introduction and Understanding the Objective':'Introducción y comprensión del objetivo','Understanding the Objective':'Comprensión del objetivo','Explore the Topic':'Explora el tema','Visual Learning':'Aprendizaje visual','Flashcards — Guided Recall':'Tarjetas de estudio — Recuerdo guiado','Self-Test — Practice without score':'Autoevaluación — Práctica sin puntuación','In-depth Reading — Continuing Education':'Lectura profunda — Educación continua','Instructor Resource — Mock 10':'Recurso del instructor — Mock 10','Exam with score':'Examen con puntuación','How does this connect to the Board?':'¿Cómo se relaciona esto con el Board?','Did you know…?':'¿Sabías que…?','Key Concepts':'Conceptos fundamentales','Memory Aids & Study Tips':'Recursos de memoria y consejos de estudio','Review Questions':'Preguntas de repaso','Correct answer:':'Respuesta correcta:','Feedback:':'Explicación:',
    'Chapter Summary Image — Visual Review Gallery':'Imagen resumen del capítulo — Galería de repaso visual','Source and recreation note':'Nota sobre fuentes y recreaciones','Enlarge diagram':'Ampliar diagrama','Close / Cerrar':'Cerrar','Zoom in':'Acercar','Fit image':'Ajustar imagen','Recreated Source Tables':'Tablas de fuentes recreadas','Scope of this gallery':'Alcance de esta galería','Enlarge table':'Ampliar tabla','Fit table':'Ajustar tabla',
    'Four Functions of the Channels':'Cuatro funciones de los canales','Four Directional Groups of the Primary Channels':'Cuatro grupos direccionales de los canales principales','Three Circuits of the Primary Channels':'Tres circuitos de los canales principales','Twelve Primary Channel Names':'Nombres de los doce canales principales','Six-Channel Pairs and Limb Positions':'Parejas de los seis canales y posiciones en las extremidades','Yang and Yin Divergent Channel Patterns':'Patrones Yang y Yin de los canales divergentes','Four Principal Relationships of the Divergent Channels':'Cuatro relaciones principales de los canales divergentes','The Eight Extraordinary Vessels':'Los ocho vasos extraordinarios','Selected Extraordinary Vessel Relationships':'Relaciones seleccionadas de los vasos extraordinarios','The Twelve Luo-Connecting Channels':'Los doce canales Luo-Connecting','Integrated Comparison of the Channel Systems':'Comparación integrada de los sistemas de canales',
    'Function':'Función','Central question':'Pregunta central','Recognition cue':'Clave de reconocimiento','Group':'Grupo','General direction':'Dirección general','Channels':'Canales','Usual limb surface':'Superficie habitual de la extremidad','Circuit':'Circuito','Identity':'Identidad','Hand channel':'Canal de la mano','Foot channel':'Canal del pie','Six-channel identity':'Identidad de los seis canales','Arm position':'Posición en el brazo','Leg position':'Posición en la pierna','Feature':'Característica','Yang Divergent':'Divergente Yang','Yin Divergent':'Divergente Yin','Pathway relationship':'Relación del trayecto','What it helps explain':'Qué ayuda a explicar','English name':'Nombre en inglés','Chinese name':'Nombre en chino','Own points?':'¿Tiene puntos propios?','Principal source relationship':'Relación principal según la fuente','Vessel':'Vaso','Origin recorded in chart':'Origen registrado en la tabla','Coalescent points recorded in chart':'Puntos de reunión registrados','Organizing idea':'Idea organizadora','Primary Channel':'Canal principal','Luo-Connecting point':'Punto Luo-Connecting','Source-described continuation':'Continuación descrita por la fuente','System or level':'Sistema o nivel','Relative location':'Ubicación relativa','Organization':'Organización','Principal study idea':'Idea principal de estudio'
  }));
  exact.set('← Back to chapters', '← Volver a los capítulos');
  exact.set('← Back to Study Guide index', '← Volver al índice de Study Guide');

  const phrases = [
    ['Explain the functions of the channels and collaterals.','Explicar las funciones de los canales y colaterales.'],
    ['Describe the organization and circulation of the Twelve Primary Channels.','Describir la organización y circulación de los doce canales principales.'],
    ['Distinguish the Twelve Divergent Channels from the Primary Channels.','Distinguir los doce canales divergentes de los canales principales.'],
    ['Explain the organization and functions of the Eight Extraordinary Vessels.','Explicar la organización y funciones de los ocho vasos extraordinarios.'],
    ['Describe the Luo-Connecting Channels and distinguish them from Luo-Connecting points.','Describir los canales Luo-Connecting y distinguirlos de los puntos Luo-Connecting.'],
    ['Describe the organization and functions of the Twelve Sinew Channels.','Describir la organización y funciones de los doce canales tendinomusculares.'],
    ['Explain the Minute Collaterals and their place in the channel network.','Explicar los colaterales diminutos y su lugar dentro de la red de canales.'],
    ['Describe the Twelve Cutaneous Regions.','Describir las doce regiones cutáneas.'],
    ['Complete an integrated chapter review of the channel and collateral systems.','Realizar un repaso integrado de los sistemas de canales y colaterales.'],
    ['These clean recreations gather the instructional tables from The Channels and Collaterals in one review library. Select any table to enlarge it. They support comparison and recall and do not replace the assigned source text.','Estas recreaciones reúnen las tablas didácticas de Los canales y colaterales en una biblioteca de repaso. Selecciona cualquier tabla para ampliarla. Sirven para comparar y recordar; no sustituyen el texto asignado.'],
    ['The Learning Plan, exam-history tables and Mock Exam directory are intentionally excluded because they are administrative tools rather than source-content tables.','El Plan de aprendizaje, las tablas del historial de exámenes y el directorio de exámenes simulados se excluyen porque son herramientas administrativas y no tablas de contenido de las fuentes.']
  ];

  const originals = new WeakMap();
  const attributes = new WeakMap();
  let active = false;
  let busy = false;
  let activating = false;
  let nativeTranslator = null;
  let translatorPromise = null;
  let run = 0;

  function translateText(value) {
    const leading = value.match(/^\s*/)?.[0] || '';
    const trailing = value.match(/\s*$/)?.[0] || '';
    const key = value.trim();
    if (!key) return value;
    if (exact.has(key)) return leading + exact.get(key) + trailing;
    let output = key;
    for (const [english, spanishText] of phrases) output = output.replaceAll(english, spanishText);
    output = output
      .replace(/^Objective (\d+)\s*[—-]\s*/i, 'Objetivo $1 — ')
      .replace(/^Question (\d+)/i, 'Pregunta $1')
      .replace(/^Flashcard (\d+)/i, 'Tarjeta de estudio $1')
      .replace(/^Answer:\s*/i, 'Respuesta: ')
      .replace(/^Feedback:\s*/i, 'Explicación: ')
      .replace(/^Reference:\s*/i, 'Referencia: ')
      .replace(/^Source:\s*/i, 'Fuente: ');
    return leading + output + trailing;
  }

  function walk(node) {
    if (node.nodeType === Node.TEXT_NODE) {
      if (node.parentElement?.closest('script,style,.preview-view-switch')) return;
      if (!originals.has(node)) originals.set(node, node.nodeValue);
      node.nodeValue = translateText(originals.get(node));
      return;
    }
    if (node.nodeType !== Node.ELEMENT_NODE) return;
    let saved = attributes.get(node);
    if (!saved) {
      saved = {};
      attributes.set(node, saved);
    }
    for (const name of ['placeholder','title','aria-label','alt']) {
      if (!node.hasAttribute(name)) continue;
      if (!(name in saved)) saved[name] = node.getAttribute(name);
      node.setAttribute(name, translateText(saved[name]));
    }
    [...node.childNodes].forEach(walk);
  }

  function scopes() {
    return [root, ...document.querySelectorAll('.channels-summary-dialog,.channels-source-table-dialog')];
  }

  function translateEditorial() {
    busy = true;
    scopes().forEach(walk);
    busy = false;
    root.lang = 'es';
    document.documentElement.lang = 'es';
  }

  function restore() {
    busy = true;
    scopes().forEach(scope => {
      const walker = document.createTreeWalker(scope, NodeFilter.SHOW_TEXT);
      while (walker.nextNode()) if (originals.has(walker.currentNode)) walker.currentNode.nodeValue = originals.get(walker.currentNode);
      scope.querySelectorAll('*').forEach(element => {
        const saved = attributes.get(element);
        if (saved) Object.entries(saved).forEach(([name, value]) => element.setAttribute(name, value));
      });
    });
    busy = false;
    root.removeAttribute('lang');
    if (!document.getElementById('point-categories-draft')?.hasAttribute('lang')) document.documentElement.lang = 'en';
  }

  async function getTranslator() {
    if (nativeTranslator) return nativeTranslator;
    if (translatorPromise) return translatorPromise;
    translatorPromise = (async () => {
      if (typeof Translator === 'undefined') return null;
      try {
        const availability = await Translator.availability({sourceLanguage:'en', targetLanguage:'es'});
        if (availability === 'unavailable') return null;
        return await Translator.create({sourceLanguage:'en', targetLanguage:'es'});
      } catch (_) {
        return null;
      }
    })();
    nativeTranslator = await translatorPromise;
    return nativeTranslator;
  }

  const englishSignal = /\b(the|and|with|which|what|why|how|from|this|each|answer|correct|study|source|review|channel|vessel|pathway|table|compare|identify|remember|function|relationship)\b/i;
  async function translateRemaining(scope = root) {
    const currentRun = run;
    const translator = await getTranslator();
    if (!translator || !active || currentRun !== run) return;
    const nodes = [];
    const walker = document.createTreeWalker(scope, NodeFilter.SHOW_TEXT);
    while (walker.nextNode()) {
      const node = walker.currentNode;
      const text = node.nodeValue.trim();
      const element = node.parentElement;
      if (text.length < 4 || !englishSignal.test(text) || element?.closest('script,style,textarea,.preview-view-switch')) continue;
      const visible = element?.checkVisibility ? element.checkVisibility({checkOpacity:true, checkVisibilityCSS:true}) : !!element?.getClientRects().length;
      if (visible) nodes.push(node);
    }
    let index = 0;
    const workers = Array.from({length: Math.min(3, nodes.length)}, async () => {
      while (active && currentRun === run) {
        const node = nodes[index++];
        if (!node) break;
        if (!originals.has(node)) originals.set(node, node.nodeValue);
        try {
          const translated = await translator.translate(node.nodeValue.trim());
          if (active && currentRun === run && node.isConnected) node.nodeValue = translated;
        } catch (_) {}
      }
    });
    await Promise.all(workers);
  }

  bar.addEventListener('click', event => {
    if (event.target === spanish) {
      activating = true;
      active = true;
      run += 1;
      requestAnimationFrame(() => {
        translateEditorial();
        setTimeout(() => translateRemaining(root), 0);
        activating = false;
      });
      return;
    }
    if ((event.target === student || event.target === instructor) && !activating && active) {
      active = false;
      run += 1;
      restore();
    }
  }, true);

  root.addEventListener('toggle', event => {
    if (active && event.target.open) setTimeout(() => translateRemaining(event.target), 0);
  }, true);
  new MutationObserver(records => {
    if (!active || busy) return;
    busy = true;
    records.forEach(record => record.addedNodes.forEach(walk));
    busy = false;
  }).observe(root, {childList:true, subtree:true});
})();
