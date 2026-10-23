(() => {
  'use strict';

  const configurations = [
    {
      key: 'channels',
      hero: '#sgv2-introduction',
      overview: '#sgv2-course-overview',
      begin: '#channels-ncbahm-alignment',
      board: '#channels-ncbahm-alignment',
      continuing: '#sgv2-formb',
      examHistory: '#chapter-exam-history',
      finalHost: '#student_notes_form',
      finalPlacement: 'after',
      title: {en: 'The Channels and Collaterals', es: 'Canales y colaterales'},
      overviewTitle: {
        en: 'Build a clear map of the channel and collateral system',
        es: 'Construye un mapa claro del sistema de canales y colaterales'
      },
      overviewText: {
        en: 'This chapter connects channel functions, circulation, naming, depth and pathway relationships. The goal is to move from isolated facts to a framework you can reconstruct and apply in Board-style questions.',
        es: 'Este capítulo conecta las funciones, circulación, nomenclatura, profundidad y relaciones de los canales. La meta es pasar de datos aislados a un marco que puedas reconstruir y aplicar en preguntas de estilo Board.'
      },
      stats: [
        ['9', {en: 'focused objectives', es: 'objetivos enfocados'}],
        ['12', {en: 'Regular Channels', es: 'Canales Regulares'}],
        ['4', {en: 'directional groups', es: 'grupos direccionales'}],
        ['2', {en: 'learning paths', es: 'rutas de aprendizaje'}]
      ],
      frameworkTitle: {en: 'One system, four organizing questions', es: 'Un sistema, cuatro preguntas organizadoras'},
      framework: [
        {en: 'What does it do?', es: '¿Qué función cumple?'},
        {en: 'Where does it travel?', es: '¿Por dónde circula?'},
        {en: 'What does it connect?', es: '¿Qué conecta?'},
        {en: 'How is it classified?', es: '¿Cómo se clasifica?'}
      ],
      method: [
        [{en: 'Orient', es: 'Orientar'}, {en: 'Place the channel in its Hand/Foot and Yin/Yang group.', es: 'Ubica el canal en su grupo Mano/Pie y Yin/Yang.'}],
        [{en: 'Trace', es: 'Trazar'}, {en: 'Reconstruct its direction and principal relationships.', es: 'Reconstruye su dirección y relaciones principales.'}],
        [{en: 'Compare', es: 'Comparar'}, {en: 'Separate similar systems by depth, organization and function.', es: 'Distingue sistemas parecidos por profundidad, organización y función.'}],
        [{en: 'Apply', es: 'Aplicar'}, {en: 'Use the evidence to answer and eliminate distractors.', es: 'Usa la evidencia para responder y eliminar distractores.'}]
      ],
      outcome: {
        en: 'By the end of the chapter, the learner can reconstruct the four Primary Channel groups, compare the major channel systems and explain the evidence supporting a pathway relationship.',
        es: 'Al finalizar, el estudiante puede reconstruir los cuatro grupos de Canales Primarios, comparar los sistemas principales y explicar la evidencia que sustenta una relación de trayecto.'
      },
      topics: [
        ['Primary Channel circulation', 'Circulación de Canales Primarios'],
        ['Internal channel connections', 'Conexiones internas'],
        ['Channel naming system', 'Sistema de nomenclatura'],
        ['Superficial-to-deep organization', 'Organización de superficial a profundo'],
        ['Divergent Channels', 'Canales Divergentes'],
        ['Extraordinary Vessels', 'Vasos Extraordinarios'],
        ['Luo-Connecting Channels', 'Canales Luo-Connecting'],
        ['Sinew Channels', 'Canales Sinew'],
        ['Minute collaterals and cutaneous regions', 'Colaterales diminutos y regiones cutáneas']
      ],
      outcomes: [
        ['Reconstruct the four directional groups', 'Reconstruir los cuatro grupos direccionales'],
        ['Explain internal and paired relationships', 'Explicar relaciones internas y de pareja'],
        ['Distinguish one channel system from another', 'Distinguir un sistema de canales de otro'],
        ['Answer Board-style comparison questions', 'Responder preguntas comparativas de estilo Board'],
        ['Create a reliable review plan', 'Crear un plan de repaso confiable'],
        ['Identify the topics that need more practice', 'Identificar los temas que necesitan más práctica']
      ]
    },
    {
      key: 'point-categories',
      hero: '#pc-introduction',
      overview: '#pc-overview',
      begin: '#pc-reading',
      board: '#pc-board-alignment',
      continuing: '#pc-learning-plan',
      examHistory: '#pc-exam-history',
      finalHost: '#point-categories-draft main',
      finalPlacement: 'append',
      title: {en: 'Point Categories', es: 'Categorías de puntos'},
      overviewTitle: {
        en: 'Organize point categories for accurate recall and application',
        es: 'Organiza las categorías de puntos para recordarlas y aplicarlas con precisión'
      },
      overviewText: {
        en: 'This chapter turns a large collection of point groups into a repeatable comparison method. Learners identify category membership, connect each group with its defining relationship and apply those distinctions to Board-style questions.',
        es: 'Este capítulo convierte una colección amplia de grupos de puntos en un método comparativo repetible. El estudiante identifica la categoría, conecta cada grupo con su relación distintiva y aplica esas diferencias a preguntas de estilo Board.'
      },
      stats: [
        ['26', {en: 'focused objectives', es: 'objetivos enfocados'}],
        ['5', {en: 'Five Shu stages', es: 'etapas de los Cinco Shu'}],
        ['12', {en: 'Regular Channels', es: 'Canales Regulares'}],
        ['2', {en: 'learning paths', es: 'rutas de aprendizaje'}]
      ],
      frameworkTitle: {en: 'Four questions for every point category', es: 'Cuatro preguntas para cada categoría'},
      framework: [
        {en: 'What defines it?', es: '¿Qué la define?'},
        {en: 'Which points belong?', es: '¿Qué puntos pertenecen?'},
        {en: 'What relationship matters?', es: '¿Qué relación importa?'},
        {en: 'How might it be tested?', es: '¿Cómo puede evaluarse?'}
      ],
      method: [
        [{en: 'Classify', es: 'Clasificar'}, {en: 'Name the point group requested by the stem.', es: 'Nombra el grupo de puntos que solicita la pregunta.'}],
        [{en: 'Match', es: 'Relacionar'}, {en: 'Connect the point code, channel and category.', es: 'Relaciona el código, canal y categoría del punto.'}],
        [{en: 'Differentiate', es: 'Diferenciar'}, {en: 'Compare the closest category or point alternative.', es: 'Compara la categoría o punto alternativo más cercano.'}],
        [{en: 'Apply', es: 'Aplicar'}, {en: 'Use the defining rule to select and justify the answer.', es: 'Usa la regla distintiva para seleccionar y justificar la respuesta.'}]
      ],
      outcome: {
        en: 'By the end of the chapter, the learner can identify major point categories, recall representative correspondences and distinguish plausible alternatives using the defining rule of each group.',
        es: 'Al finalizar, el estudiante puede identificar las categorías principales, recordar correspondencias representativas y distinguir alternativas plausibles mediante la regla distintiva de cada grupo.'
      },
      topics: [
        ['Five Shu points', 'Puntos Cinco Shu'],
        ['Yuan-Source points', 'Puntos Yuan-Source'],
        ['Luo-Connecting points', 'Puntos Luo-Connecting'],
        ['Xi-Cleft points', 'Puntos Xi-Cleft'],
        ['Back-Shu and Front-Mu points', 'Puntos Back-Shu y Front-Mu'],
        ['Lower He-Sea points', 'Puntos Lower He-Sea'],
        ['Command and Meeting points', 'Puntos Command y Meeting'],
        ['Confluent and Extraordinary Vessel points', 'Puntos Confluent y Vasos Extraordinarios'],
        ['Mother, Child and special point groups', 'Grupos Mother, Child y puntos especiales']
      ],
      outcomes: [
        ['Recognize a category from its defining rule', 'Reconocer una categoría por su regla distintiva'],
        ['Match point codes with the correct group', 'Relacionar códigos de puntos con el grupo correcto'],
        ['Compare categories that are often confused', 'Comparar categorías que suelen confundirse'],
        ['Answer Board-style identification questions', 'Responder preguntas de identificación estilo Board'],
        ['Create a reliable category study grid', 'Crear una tabla confiable de categorías'],
        ['Identify the topics that need more practice', 'Identificar los temas que necesitan más práctica']
      ]
    }
  ];

  const shared = {
    en: {
      eyebrow: 'BEFORE YOU STUDY · 3–5 MINUTES',
      surveyTitle: 'What do you need from this course?',
      surveyLead: 'Select the areas you believe you need to review and define the result you expect. Your answers are saved as your starting point.',
      stage: '1 · Starting point',
      topicsLegend: 'Which topics do you need to review?',
      topicsHelp: 'Choose every topic that currently feels uncertain.',
      outcomesLegend: 'What results do you expect?',
      outcomesHelp: 'Choose one or more practical outcomes.',
      depthLegend: 'How deeply do you plan to study?',
      depths: [
        ['1 · Overview', 'recognize the main ideas'],
        ['2 · Working knowledge', 'answer with notes'],
        ['3 · Board application', 'answer independently'],
        ['4 · Teach-back mastery', 'explain and defend the answer']
      ],
      timeLegend: 'How much study time do you expect to need?',
      times: ['Less than 30 minutes', '30–60 minutes', '1–2 hours', '2–3 hours', 'More than 3 hours'],
      confidenceLegend: 'How confident are you right now?',
      confidenceHelp: 'Use the descriptions—not the number alone—to choose your starting point.',
      confidence: [
        ['1 · Not yet', 'I am not ready to answer these questions yet.'],
        ['2 · With notes', 'I can recognize some answers with help.'],
        ['3 · Independently', 'I can answer most questions without help.'],
        ['4 · I can teach it', 'I can explain and justify my answers.']
      ],
      goal: 'My most important goal for this course',
      goalPlaceholder: 'Example: identify my weakest topic and explain the correct answer without using notes.',
      historyEyebrow: 'EXAM HISTORY',
      historyTitle: 'You do not need to remember a score',
      historyText: 'Moodle keeps the results of completed scored activities. Use Exam History to review attempts and identify the topics that need more practice.',
      historyLink: 'Open Exam History',
      historyNote: 'The study-needs responses on this page are saved only in this browser. Official examination results remain in Moodle.',
      saved: 'Responses save automatically in this browser.',
      savedNow: 'Starting point saved in this browser.',
      begin: 'Begin the course →',
      overviewSummary: 'Course Overview',
      pathsEyebrow: 'CHAPTER LEARNING PATHS',
      pathsTitle: 'One Chapter, Two Learning Paths',
      pathsLead: 'Use this chapter to strengthen Board Exam preparation and complete a structured Continuing Education review.',
      boardBadge: 'BOARD PREPARATION',
      boardTitle: 'Board Exam Preparation',
      boardText: 'Review essential concepts, practice questions and Board-level activities.',
      boardButton: 'Start Board Preparation',
      ceBadge: 'CONTINUING EDUCATION',
      ceTitle: 'Continuing Education',
      ceText: 'Follow the learning plan, review explanatory feedback and complete the Continuing Education activities.',
      ceButton: 'Start Continuing Education',
      pathsNote: 'Board preparation and Continuing Education are separate learning paths. Official completion, credit and certificate records remain in Moodle.',
      glance: 'COURSE AT A GLANCE',
      methodTitle: 'The study method',
      learningOutcome: 'Learning outcome',
      openPlan: 'Open the learning plan →'
    },
    es: {
      eyebrow: 'ANTES DE ESTUDIAR · 3–5 MINUTOS',
      surveyTitle: '¿Qué necesitas de este curso?',
      surveyLead: 'Selecciona las áreas que consideras necesario repasar y define el resultado que esperas. Tus respuestas se guardan como punto de partida.',
      stage: '1 · Punto de partida',
      topicsLegend: '¿Qué temas necesitas repasar?',
      topicsHelp: 'Elige todos los temas que todavía te resultan inciertos.',
      outcomesLegend: '¿Qué resultados esperas?',
      outcomesHelp: 'Elige uno o más resultados prácticos.',
      depthLegend: '¿Con qué profundidad piensas estudiar?',
      depths: [
        ['1 · Panorama general', 'reconocer las ideas principales'],
        ['2 · Conocimiento funcional', 'responder con apuntes'],
        ['3 · Aplicación al Board', 'responder de forma independiente'],
        ['4 · Dominio para enseñar', 'explicar y defender la respuesta']
      ],
      timeLegend: '¿Cuánto tiempo de estudio crees que necesitarás?',
      times: ['Menos de 30 minutos', '30–60 minutos', '1–2 horas', '2–3 horas', 'Más de 3 horas'],
      confidenceLegend: '¿Qué tan seguro te sientes ahora?',
      confidenceHelp: 'Usa las descripciones, no solo el número, para elegir tu punto de partida.',
      confidence: [
        ['1 · Todavía no', 'Aún no estoy listo para responder estas preguntas.'],
        ['2 · Con apuntes', 'Puedo reconocer algunas respuestas con ayuda.'],
        ['3 · Independientemente', 'Puedo responder la mayoría sin ayuda.'],
        ['4 · Puedo enseñarlo', 'Puedo explicar y justificar mis respuestas.']
      ],
      goal: 'Mi meta más importante para este curso',
      goalPlaceholder: 'Ejemplo: identificar mi tema más débil y explicar la respuesta correcta sin apuntes.',
      historyEyebrow: 'HISTORIAL DE EXÁMENES',
      historyTitle: 'No necesitas recordar una puntuación',
      historyText: 'Moodle conserva los resultados de las actividades calificadas. Usa el Historial de exámenes para revisar intentos e identificar los temas que necesitan más práctica.',
      historyLink: 'Abrir Historial de exámenes',
      historyNote: 'Las respuestas sobre tus necesidades se guardan solo en este navegador. Los resultados oficiales permanecen en Moodle.',
      saved: 'Las respuestas se guardan automáticamente en este navegador.',
      savedNow: 'Punto de partida guardado en este navegador.',
      begin: 'Comenzar el curso →',
      overviewSummary: 'Descripción del curso',
      pathsEyebrow: 'RUTAS DE APRENDIZAJE DEL CAPÍTULO',
      pathsTitle: 'Un capítulo, dos rutas de aprendizaje',
      pathsLead: 'Usa este capítulo para fortalecer la preparación para el Board y completar un repaso estructurado de Continuing Education.',
      boardBadge: 'PREPARACIÓN PARA EL BOARD',
      boardTitle: 'Preparación para el Board Exam',
      boardText: 'Repasa conceptos esenciales, preguntas de práctica y actividades de nivel Board.',
      boardButton: 'Comenzar preparación para el Board',
      ceBadge: 'CONTINUING EDUCATION',
      ceTitle: 'Continuing Education',
      ceText: 'Sigue el plan de aprendizaje, revisa el feedback explicativo y completa las actividades de Continuing Education.',
      ceButton: 'Comenzar Continuing Education',
      pathsNote: 'La preparación para el Board y Continuing Education son rutas separadas. Los registros oficiales de finalización, créditos y certificados permanecen en Moodle.',
      glance: 'EL CURSO DE UN VISTAZO',
      methodTitle: 'El método de estudio',
      learningOutcome: 'Resultado de aprendizaje',
      openPlan: 'Abrir el plan de aprendizaje →'
    }
  };

  const reflectionCopy = {
    en: {
      eyebrow: 'AFTER YOU STUDY · 5 MINUTES', title: 'Did the course meet your learning needs?',
      lead: 'Compare your starting point with what you can do now. Use your Moodle exam results as the objective check for this self-reflection.',
      stage: '2 · Reflection', confidence: 'How confident are you now?', confidenceHelp: 'Choose the same scale used at the beginning so the comparison is meaningful.',
      confidenceOptions: [['1 · Not yet', 'I still need help with the main ideas.'], ['2 · With notes', 'I can recognize answers with support.'], ['3 · Independently', 'I can answer most questions without help.'], ['4 · I can teach it', 'I can explain and justify my answers.']],
      historyEyebrow: 'MOODLE EXAM RESULTS', historyTitle: 'Your scored results are already recorded in Moodle',
      historyText: 'You do not need to remember or enter a score here. Open Exam History to compare this reflection with your completed attempts and grades.', historyLink: 'Open Exam History',
      depth: 'How deeply did you study?', depthOptions: [['1 · Skimmed', 'read or viewed once'], ['2 · Reviewed', 'used notes or tables'], ['3 · Practiced', 'completed recall or exam work'], ['4 · Mastered', 'can explain and defend answers']],
      review: 'How much more review do you need?', reviewOptions: [['1 · Extensive', 'rebuild the foundations'], ['2 · Moderate', 'revisit several areas'], ['3 · Targeted', 'review a few weak areas'], ['4 · Minimal', 'maintain with practice']],
      useful: 'How useful was the course?', usefulOptions: ['1 · Not useful yet', '2 · Somewhat useful', '3 · Useful', '4 · Very useful'],
      expectations: 'Were your expected results achieved?', expectationOptions: ['1 · Not yet', '2 · Partly', '3 · Met', '4 · Exceeded'],
      topics: 'Which topics still need review?', topicsHelp: 'Select only the areas that should appear in your recommendation.',
      helped: 'What helped you most?', helpedPlaceholder: 'Example: comparison tables and practice questions.',
      next: 'What will you do next?', nextPlaceholder: 'Example: review two weak topics and repeat the related exam.',
      saved: 'Responses save automatically in this browser.', savedNow: 'Reflection saved in this browser.', generate: 'Generate My Study Reflection',
      resultEyebrow: 'YOUR BEFORE–AFTER REVIEW', resultTitle: 'Study reflection and recommendations', ready: 'Reflection ready', incomplete: 'Complete the ratings',
      confidenceMetric: 'Confidence change', depthMetric: 'Study depth', usefulnessMetric: 'Course usefulness', reviewMetric: 'Review need',
      original: 'Your original learning priorities', remaining: 'Topics to review next', recommendations: 'Personalized recommendations', plan: 'Your stated plan',
      noOriginal: 'No starting priorities were recorded.', noRemaining: 'No remaining topic was selected. Maintain learning with spaced practice.', noPlan: 'Write what helped most and one next step to create a personal plan.',
      note: 'This is a guided self-assessment, not an exam score. Confirm your readiness with the related Moodle exams.',
      notRated: 'Not rated', extensive: 'Extensive', moderate: 'Moderate', targeted: 'Targeted', minimal: 'Minimal',
      reviewSelected: topic => `Review ${topic} with active recall, then verify the result in the related Moodle exam.`,
      recLowConfidence: 'Return to the chapter summary and complete one short recall activity before attempting another exam.',
      recConfidenceFlat: 'Your confidence did not increase. Review the questions you missed and explain why each distractor is incorrect.',
      recDepthGap: 'Your reported study depth was lower than planned. Schedule one focused session to close that gap.',
      recHighReview: 'Use two short sessions: first rebuild the key rules, then answer questions without notes.',
      recLowUseful: 'Repeat the resource format that helped most and note which explanation or practice format was missing.',
      recExpectations: 'Choose one original expected result and turn it into a specific task for your next study session.',
      recMaintain: 'Maintain readiness with spaced practice and one mixed review before the next examination.',
      recExam: 'Open Moodle Exam History and compare this self-assessment with your latest attempt and grade.',
      summaryReady: 'Your reflection is complete. Use the recommendations below to plan the next study session.',
      summaryPartial: 'Complete all five ratings for a fuller comparison and more precise recommendations.',
      goalPrefix: 'Goal', expectedPrefix: 'Expected result', helpfulPrefix: 'Most helpful', nextPrefix: 'Next step'
    },
    es: {
      eyebrow: 'DESPUÉS DE ESTUDIAR · 5 MINUTOS', title: '¿El curso respondió a tus necesidades de aprendizaje?',
      lead: 'Compara tu punto de partida con lo que puedes hacer ahora. Usa tus resultados de Moodle como comprobación objetiva de esta autorreflexión.',
      stage: '2 · Reflexión', confidence: '¿Qué nivel de confianza tienes ahora?', confidenceHelp: 'Usa la misma escala del inicio para que la comparación sea significativa.',
      confidenceOptions: [['1 · Todavía no', 'Aún necesito ayuda con las ideas principales.'], ['2 · Con apuntes', 'Puedo reconocer respuestas con apoyo.'], ['3 · De forma independiente', 'Puedo responder la mayoría sin ayuda.'], ['4 · Puedo enseñarlo', 'Puedo explicar y justificar mis respuestas.']],
      historyEyebrow: 'RESULTADOS DE EXÁMENES EN MOODLE', historyTitle: 'Tus resultados calificados ya están registrados en Moodle',
      historyText: 'No necesitas recordar ni escribir una puntuación aquí. Abre el Historial de exámenes para comparar esta reflexión con tus intentos y calificaciones.', historyLink: 'Abrir Historial de exámenes',
      depth: '¿Con qué profundidad estudiaste?', depthOptions: [['1 · Lectura rápida', 'leí o vi el contenido una vez'], ['2 · Repaso', 'utilicé apuntes o tablas'], ['3 · Práctica', 'completé recuerdo activo o exámenes'], ['4 · Dominio', 'puedo explicar y defender respuestas']],
      review: '¿Cuánto repaso adicional necesitas?', reviewOptions: [['1 · Extenso', 'reconstruir los fundamentos'], ['2 · Moderado', 'repasar varias áreas'], ['3 · Dirigido', 'repasar algunas áreas débiles'], ['4 · Mínimo', 'mantener con práctica']],
      useful: '¿Qué tan útil fue el curso?', usefulOptions: ['1 · Todavía no fue útil', '2 · Algo útil', '3 · Útil', '4 · Muy útil'],
      expectations: '¿Se alcanzaron los resultados que esperabas?', expectationOptions: ['1 · Todavía no', '2 · Parcialmente', '3 · Sí', '4 · Se superaron'],
      topics: '¿Qué temas todavía necesitas repasar?', topicsHelp: 'Selecciona solamente las áreas que deben aparecer en tu recomendación.',
      helped: '¿Qué te ayudó más?', helpedPlaceholder: 'Ejemplo: las tablas comparativas y las preguntas de práctica.',
      next: '¿Qué harás a continuación?', nextPlaceholder: 'Ejemplo: repasar dos temas débiles y repetir el examen relacionado.',
      saved: 'Las respuestas se guardan automáticamente en este navegador.', savedNow: 'Reflexión guardada en este navegador.', generate: 'Generar mi reflexión de estudio',
      resultEyebrow: 'TU COMPARACIÓN ANTES–DESPUÉS', resultTitle: 'Reflexión de estudio y recomendaciones', ready: 'Reflexión lista', incomplete: 'Completa las escalas',
      confidenceMetric: 'Cambio de confianza', depthMetric: 'Profundidad de estudio', usefulnessMetric: 'Utilidad del curso', reviewMetric: 'Necesidad de repaso',
      original: 'Tus prioridades iniciales', remaining: 'Temas para repasar ahora', recommendations: 'Recomendaciones personalizadas', plan: 'Tu plan declarado',
      noOriginal: 'No se registraron prioridades iniciales.', noRemaining: 'No seleccionaste temas pendientes. Mantén el aprendizaje con práctica espaciada.', noPlan: 'Escribe qué te ayudó más y un próximo paso para crear tu plan personal.',
      note: 'Esta es una autoevaluación guiada, no una calificación. Confirma tu preparación con los exámenes relacionados en Moodle.',
      notRated: 'Sin responder', extensive: 'Extensa', moderate: 'Moderada', targeted: 'Dirigida', minimal: 'Mínima',
      reviewSelected: topic => `Repasa ${topic} mediante recuerdo activo y verifica el resultado en el examen relacionado de Moodle.`,
      recLowConfidence: 'Regresa al resumen del capítulo y completa una actividad breve de recuerdo antes de intentar otro examen.',
      recConfidenceFlat: 'Tu confianza no aumentó. Repasa las preguntas falladas y explica por qué cada distractor es incorrecto.',
      recDepthGap: 'La profundidad de estudio informada fue menor que la planeada. Programa una sesión enfocada para cerrar esa brecha.',
      recHighReview: 'Usa dos sesiones breves: primero reconstruye las reglas clave y luego responde preguntas sin apuntes.',
      recLowUseful: 'Repite el formato de recurso que más te ayudó e identifica qué explicación o práctica hizo falta.',
      recExpectations: 'Elige un resultado esperado original y conviértelo en una tarea concreta para tu próxima sesión.',
      recMaintain: 'Mantén la preparación con práctica espaciada y un repaso mixto antes del próximo examen.',
      recExam: 'Abre el Historial de exámenes de Moodle y compara esta autoevaluación con tu intento y calificación más recientes.',
      summaryReady: 'Tu reflexión está completa. Usa las recomendaciones para planificar la próxima sesión de estudio.',
      summaryPartial: 'Completa las cinco escalas para obtener una comparación y recomendaciones más precisas.',
      goalPrefix: 'Meta', expectedPrefix: 'Resultado esperado', helpfulPrefix: 'Lo más útil', nextPrefix: 'Próximo paso'
    }
  };

  const make = (tag, className, text) => {
    const node = document.createElement(tag);
    if (className) node.className = className;
    if (text !== undefined) node.textContent = text;
    return node;
  };
  const language = () => document.documentElement.lang.toLowerCase().startsWith('es') ? 'es' : 'en';
  const localized = value => typeof value === 'string' ? value : value[language()];

  function openTarget(event) {
    const selector = event.currentTarget.getAttribute('href');
    const target = selector?.startsWith('#') ? document.querySelector(selector) : null;
    if (!target) return;
    event.preventDefault();
    if (target.tagName === 'DETAILS') target.open = true;
    target.querySelector(':scope > details')?.setAttribute('open', '');
    history.replaceState(null, '', selector);
    target.scrollIntoView({behavior: 'smooth', block: 'start'});
    const heading = target.querySelector(':scope > summary, :scope > h2, :scope > h3') || target;
    heading.tabIndex = -1;
    heading.focus({preventScroll: true});
  }

  function choice(parent, type, name, value, text, details) {
    const label = make('label', type === 'radio' ? 'course-intro-radio' : 'course-intro-choice');
    const input = make('input');
    input.type = type;
    input.name = name;
    input.value = value;
    const content = make('span');
    if (details) {
      content.append(make('b', '', text), make('small', '', details));
    } else {
      content.textContent = text;
    }
    label.append(input, content);
    parent.append(label);
  }

  function readForm(form) {
    const data = {};
    new FormData(form).forEach((value, key) => {
      if (data[key] === undefined) data[key] = value;
      else data[key] = Array.isArray(data[key]) ? [...data[key], value] : [data[key], value];
    });
    return data;
  }

  function restoreForm(form, data) {
    if (!data || typeof data !== 'object') return;
    [...form.elements].forEach(input => {
      const value = data[input.name];
      if (input.type === 'checkbox') input.checked = Array.isArray(value) ? value.includes(input.value) : value === input.value;
      else if (input.type === 'radio') input.checked = value === input.value;
      else if (input.name && typeof value === 'string') input.value = value;
    });
  }

  function buildSurvey(config) {
    const words = shared[language()];
    const section = make('section', 'course-intro-survey sgv2-card');
    section.id = `course-intro-survey-${config.key}`;
    section.setAttribute('aria-labelledby', `course-intro-survey-title-${config.key}`);
    const heading = make('div', 'course-intro-survey-heading');
    const copy = make('div');
    copy.append(make('span', 'course-intro-eyebrow', words.eyebrow));
    const title = make('h3', '', words.surveyTitle);
    title.id = `course-intro-survey-title-${config.key}`;
    copy.append(title, make('p', '', words.surveyLead));
    heading.append(copy, make('span', 'course-intro-stage', words.stage));
    section.append(heading);

    const form = make('form', 'course-intro-form');
    form.dataset.course = config.key;
    const topics = make('fieldset', 'course-intro-fieldset');
    topics.append(make('legend', '', words.topicsLegend), make('p', 'course-intro-help', words.topicsHelp));
    const topicGrid = make('div', 'course-intro-choice-grid course-intro-topic-grid');
    config.topics.forEach(([en, es]) => choice(topicGrid, 'checkbox', 'review-topic', en, language() === 'es' ? es : en));
    topics.append(topicGrid);
    form.append(topics);

    const outcomes = make('fieldset', 'course-intro-fieldset');
    outcomes.append(make('legend', '', words.outcomesLegend), make('p', 'course-intro-help', words.outcomesHelp));
    const outcomeGrid = make('div', 'course-intro-choice-grid');
    config.outcomes.forEach(([en, es]) => choice(outcomeGrid, 'checkbox', 'expected-result', en, language() === 'es' ? es : en));
    outcomes.append(outcomeGrid);
    form.append(outcomes);

    const twoColumn = make('div', 'course-intro-two-column');
    const depth = make('fieldset', 'course-intro-fieldset');
    depth.append(make('legend', '', words.depthLegend));
    const depthList = make('div', 'course-intro-radio-list');
    words.depths.forEach(([titleText, detail], index) => choice(depthList, 'radio', 'planned-depth', String(index + 1), titleText, detail));
    depth.append(depthList);
    const time = make('fieldset', 'course-intro-fieldset');
    time.append(make('legend', '', words.timeLegend));
    const timeList = make('div', 'course-intro-radio-list');
    words.times.forEach((text, index) => choice(timeList, 'radio', 'planned-time', String(index + 1), text));
    time.append(timeList);
    twoColumn.append(depth, time);
    form.append(twoColumn);

    const confidence = make('fieldset', 'course-intro-fieldset');
    confidence.append(make('legend', '', words.confidenceLegend), make('p', 'course-intro-help', words.confidenceHelp));
    const scale = make('div', 'course-intro-confidence');
    words.confidence.forEach(([titleText, detail], index) => choice(scale, 'radio', 'initial-confidence', String(index + 1), titleText, detail));
    confidence.append(scale);
    form.append(confidence);

    const finalRow = make('div', 'course-intro-two-column course-intro-final-row');
    const goal = make('label', 'course-intro-goal');
    goal.append(make('span', '', words.goal));
    const textarea = make('textarea');
    textarea.name = 'starting-goal';
    textarea.rows = 4;
    textarea.placeholder = words.goalPlaceholder;
    goal.append(textarea);
    const historyBox = make('aside', 'course-intro-history');
    historyBox.append(make('span', 'course-intro-eyebrow', words.historyEyebrow), make('h4', '', words.historyTitle), make('p', '', words.historyText));
    const historyLink = make('a', '', words.historyLink);
    historyLink.href = config.examHistory;
    historyLink.addEventListener('click', openTarget);
    historyBox.append(historyLink, make('small', '', words.historyNote));
    finalRow.append(goal, historyBox);
    form.append(finalRow);

    const saveRow = make('div', 'course-intro-save-row');
    const status = make('p', '', words.saved);
    status.setAttribute('role', 'status');
    status.setAttribute('aria-live', 'polite');
    const begin = make('a', '', words.begin);
    begin.href = config.begin;
    begin.addEventListener('click', openTarget);
    saveRow.append(status, begin);
    form.append(saveRow);
    section.append(form);

    const storageKey = `acupuncturetests:study-needs:${config.key}:v1`;
    try { restoreForm(form, JSON.parse(localStorage.getItem(storageKey) || 'null')); } catch (error) {}
    let timer;
    form.addEventListener('input', () => {
      clearTimeout(timer);
      timer = setTimeout(() => {
        try {
          localStorage.setItem(storageKey, JSON.stringify(readForm(form)));
          status.textContent = words.savedNow;
        } catch (error) {
          status.textContent = words.saved;
        }
      }, 120);
    });
    return section;
  }

  function ensureLearningPaths(config) {
    const existing = document.getElementById(`dual-learning-paths-${config.key}`);
    if (existing && existing.dataset.courseIntroFallback !== 'true') return existing;
    existing?.remove();
    const hero = document.querySelector(config.hero);
    if (!hero) return null;
    const words = shared[language()];
    const section = make('section', 'dual-learning-paths');
    section.id = `dual-learning-paths-${config.key}`;
    section.dataset.chapter = config.key;
    section.dataset.courseIntroFallback = 'true';
    section.setAttribute('aria-labelledby', `dual-learning-title-${config.key}`);
    const heading = make('div', 'dual-learning-heading');
    heading.append(make('p', 'dual-learning-eyebrow', words.pathsEyebrow));
    const title = make('h3', '', words.pathsTitle);
    title.id = `dual-learning-title-${config.key}`;
    heading.append(title, make('p', 'dual-learning-lead', words.pathsLead));
    const grid = make('div', 'dual-learning-grid');
    const path = (type, badgeText, titleText, bodyText, buttonText, href) => {
      const article = make('article', `learning-path-card ${type}-path`);
      article.append(make('span', 'learning-path-badge', badgeText), make('h4', '', titleText), make('p', '', bodyText));
      const link = make('a', 'learning-path-button', buttonText);
      link.href = href;
      link.addEventListener('click', openTarget);
      article.append(link);
      return article;
    };
    grid.append(
      path('board', words.boardBadge, words.boardTitle, words.boardText, words.boardButton, config.board),
      path('continuing', words.ceBadge, words.ceTitle, words.ceText, words.ceButton, config.continuing)
    );
    section.append(heading, grid, make('p', 'dual-learning-note', words.pathsNote));
    hero.after(section);
    return section;
  }

  function buildOverview(config, overview) {
    const words = shared[language()];
    overview.classList.add('course-intro-enhanced');
    overview.open = true;
    const summary = overview.querySelector(':scope > summary');
    if (summary) summary.textContent = words.overviewSummary;
    overview.querySelector(':scope > .course-intro-overview')?.remove();
    const panel = make('div', 'course-intro-overview');
    const header = make('header', 'course-intro-overview-heading');
    const copy = make('div');
    copy.append(make('span', 'course-intro-eyebrow', words.glance), make('h3', '', localized(config.overviewTitle)), make('p', '', localized(config.overviewText)));
    const badge = make('div', 'course-intro-course-badge');
    badge.append(make('strong', '', localized(config.title)), make('span', '', words.overviewSummary));
    header.append(copy, badge);
    panel.append(header);
    const stats = make('div', 'course-intro-stats');
    stats.setAttribute('aria-label', words.glance);
    config.stats.forEach(([number, label]) => {
      const card = make('article');
      card.append(make('strong', '', number), make('span', '', localized(label)));
      stats.append(card);
    });
    panel.append(stats);
    const framework = make('section', 'course-intro-framework');
    framework.append(make('h4', '', localized(config.frameworkTitle)));
    const tags = make('div');
    config.framework.forEach(item => tags.append(make('span', '', localized(item))));
    framework.append(tags);
    panel.append(framework);
    const method = make('section', 'course-intro-method');
    method.append(make('h4', '', words.methodTitle));
    const list = make('ol');
    config.method.forEach(([titleText, detail]) => {
      const item = make('li');
      item.append(make('b', '', localized(titleText)), make('span', '', localized(detail)));
      list.append(item);
    });
    method.append(list);
    panel.append(method);
    const footer = make('footer', 'course-intro-outcome');
    const outcome = make('div');
    outcome.append(make('b', '', words.learningOutcome), make('p', '', localized(config.outcome)));
    const link = make('a', '', words.openPlan);
    link.href = config.begin;
    link.addEventListener('click', openTarget);
    footer.append(outcome, link);
    panel.append(footer);
    summary?.after(panel);
  }

  const asArray = value => value ? [].concat(value) : [];
  const ratingNumber = value => {
    const number = Number(value);
    return Number.isFinite(number) ? number : null;
  };

  function topicLabel(config, value) {
    const topic = config.topics.find(([en]) => en === value);
    return topic ? topic[language() === 'es' ? 1 : 0] : value;
  }

  function outcomeLabel(config, value) {
    const outcome = config.outcomes.find(([en]) => en === value);
    return outcome ? outcome[language() === 'es' ? 1 : 0] : value;
  }

  function reflectionList(parent, items, emptyText) {
    parent.replaceChildren();
    (items.length ? items : [emptyText]).forEach(text => parent.append(make('li', '', text)));
  }

  function buildReflection(config) {
    const words = reflectionCopy[language()];
    const section = make('section', 'course-final-reflection sgv2-card');
    section.id = `course-final-reflection-${config.key}`;
    section.dataset.language = language();
    section.setAttribute('aria-labelledby', `course-final-reflection-title-${config.key}`);

    const heading = make('div', 'course-final-reflection-heading');
    const headingCopy = make('div');
    headingCopy.append(make('span', 'course-intro-eyebrow', words.eyebrow));
    const title = make('h3', '', words.title);
    title.id = `course-final-reflection-title-${config.key}`;
    headingCopy.append(title, make('p', '', words.lead));
    heading.append(headingCopy, make('span', 'course-final-reflection-stage', words.stage));
    section.append(heading);

    const form = make('form', 'course-final-reflection-form');
    const confidence = make('fieldset', 'course-intro-fieldset');
    confidence.append(make('legend', '', words.confidence), make('p', 'course-intro-help', words.confidenceHelp));
    const confidenceScale = make('div', 'course-intro-confidence');
    words.confidenceOptions.forEach(([label, detail], index) => choice(confidenceScale, 'radio', 'final-confidence', String(index + 1), label, detail));
    confidence.append(confidenceScale);
    form.append(confidence);

    const historyBox = make('aside', 'course-final-exam-history');
    const historyCopy = make('div');
    historyCopy.append(make('span', 'course-intro-eyebrow', words.historyEyebrow), make('h4', '', words.historyTitle), make('p', '', words.historyText));
    const historyLink = make('a', '', words.historyLink);
    historyLink.href = config.examHistory;
    historyLink.addEventListener('click', openTarget);
    historyBox.append(historyCopy, historyLink);
    form.append(historyBox);

    const scaleGrid = make('div', 'course-final-scale-grid');
    const addDetailedScale = (legend, name, options) => {
      const fieldset = make('fieldset', 'course-intro-fieldset');
      fieldset.append(make('legend', '', legend));
      const list = make('div', 'course-intro-radio-list');
      options.forEach(([label, detail], index) => choice(list, 'radio', name, String(index + 1), label, detail));
      fieldset.append(list);
      scaleGrid.append(fieldset);
    };
    const addSimpleScale = (legend, name, options) => {
      const fieldset = make('fieldset', 'course-intro-fieldset');
      fieldset.append(make('legend', '', legend));
      const list = make('div', 'course-intro-radio-list');
      options.forEach((label, index) => choice(list, 'radio', name, String(index + 1), label));
      fieldset.append(list);
      scaleGrid.append(fieldset);
    };
    addDetailedScale(words.depth, 'actual-depth', words.depthOptions);
    addDetailedScale(words.review, 'review-need', words.reviewOptions);
    addSimpleScale(words.useful, 'usefulness', words.usefulOptions);
    addSimpleScale(words.expectations, 'expectations-met', words.expectationOptions);
    form.append(scaleGrid);

    const topics = make('fieldset', 'course-intro-fieldset');
    topics.append(make('legend', '', words.topics), make('p', 'course-intro-help', words.topicsHelp));
    const topicGrid = make('div', 'course-intro-choice-grid course-intro-topic-grid');
    config.topics.forEach(([en, es]) => choice(topicGrid, 'checkbox', 'remaining-topic', en, language() === 'es' ? es : en));
    topics.append(topicGrid);
    form.append(topics);

    const textGrid = make('div', 'course-intro-two-column');
    const textField = (labelText, name, placeholder) => {
      const label = make('label', 'course-final-text-field');
      label.append(make('span', '', labelText));
      const textarea = make('textarea');
      textarea.name = name;
      textarea.rows = 3;
      textarea.placeholder = placeholder;
      label.append(textarea);
      return label;
    };
    textGrid.append(textField(words.helped, 'most-helpful', words.helpedPlaceholder), textField(words.next, 'next-step', words.nextPlaceholder));
    form.append(textGrid);

    const actions = make('div', 'course-final-actions');
    const status = make('p', '', words.saved);
    status.setAttribute('role', 'status');
    status.setAttribute('aria-live', 'polite');
    const generate = make('button', '', words.generate);
    generate.type = 'button';
    actions.append(status, generate);
    form.append(actions);
    section.append(form);

    const result = make('section', 'course-final-result');
    result.hidden = true;
    result.setAttribute('aria-live', 'polite');
    const resultHeading = make('div', 'course-final-result-heading');
    const resultCopy = make('div');
    resultCopy.append(make('span', 'course-intro-eyebrow', words.resultEyebrow), make('h3', '', words.resultTitle));
    const level = make('span', 'course-final-result-level', words.ready);
    resultHeading.append(resultCopy, level);
    result.append(resultHeading);

    const metrics = make('div', 'course-final-metrics');
    const metricNodes = {};
    [['confidence', words.confidenceMetric], ['depth', words.depthMetric], ['usefulness', words.usefulnessMetric], ['review', words.reviewMetric]].forEach(([key, label]) => {
      const card = make('article');
      metricNodes[key] = make('strong', '', '—');
      card.append(metricNodes[key], make('span', '', label));
      metrics.append(card);
    });
    const summary = make('p', 'course-final-summary');
    const evaluationGrid = make('div', 'course-final-evaluation-grid');
    const evaluationLists = {};
    [['original', words.original], ['remaining', words.remaining], ['recommendations', words.recommendations], ['plan', words.plan]].forEach(([key, label]) => {
      const card = make('article');
      card.append(make('h4', '', label));
      const list = make(key === 'recommendations' ? 'ol' : 'ul');
      evaluationLists[key] = list;
      card.append(list);
      evaluationGrid.append(card);
    });
    result.append(metrics, summary, evaluationGrid, make('p', 'course-final-note', words.note));
    section.append(result);

    const storageKey = `acupuncturetests:final-reflection:${config.key}:v1`;
    const startingKey = `acupuncturetests:study-needs:${config.key}:v1`;
    try { restoreForm(form, JSON.parse(localStorage.getItem(storageKey) || 'null')); } catch (error) {}
    const save = () => {
      try {
        localStorage.setItem(storageKey, JSON.stringify(readForm(form)));
        status.textContent = words.savedNow;
      } catch (error) {
        status.textContent = words.saved;
      }
    };
    form.addEventListener('input', save);
    form.addEventListener('change', save);

    generate.addEventListener('click', () => {
      save();
      const after = readForm(form);
      let before = {};
      try { before = JSON.parse(localStorage.getItem(startingKey) || '{}') || {}; } catch (error) {}
      const initialConfidence = ratingNumber(before['initial-confidence']);
      const finalConfidence = ratingNumber(after['final-confidence']);
      const plannedDepth = ratingNumber(before['planned-depth']);
      const actualDepth = ratingNumber(after['actual-depth']);
      const reviewNeed = ratingNumber(after['review-need']);
      const usefulness = ratingNumber(after.usefulness);
      const expectations = ratingNumber(after['expectations-met']);
      const remaining = asArray(after['remaining-topic']);
      const recommendations = [];
      if (finalConfidence !== null && finalConfidence <= 2) recommendations.push(words.recLowConfidence);
      if (initialConfidence !== null && finalConfidence !== null && finalConfidence <= initialConfidence) recommendations.push(words.recConfidenceFlat);
      if (plannedDepth !== null && actualDepth !== null && actualDepth < plannedDepth) recommendations.push(words.recDepthGap);
      if (reviewNeed !== null && reviewNeed <= 2) recommendations.push(words.recHighReview);
      remaining.slice(0, 4).forEach(topic => recommendations.push(words.reviewSelected(topicLabel(config, topic))));
      if (usefulness !== null && usefulness <= 2) recommendations.push(words.recLowUseful);
      if (expectations !== null && expectations <= 2) recommendations.push(words.recExpectations);
      if (!remaining.length && finalConfidence !== null && finalConfidence >= 3) recommendations.push(words.recMaintain);
      recommendations.push(words.recExam);

      const answered = [finalConfidence, actualDepth, reviewNeed, usefulness, expectations].filter(value => value !== null).length;
      level.textContent = answered === 5 ? words.ready : words.incomplete;
      metricNodes.confidence.textContent = initialConfidence === null && finalConfidence === null ? words.notRated : `${initialConfidence ?? '—'} → ${finalConfidence ?? '—'}${initialConfidence !== null && finalConfidence !== null ? ` (${finalConfidence - initialConfidence >= 0 ? '+' : ''}${finalConfidence - initialConfidence})` : ''}`;
      metricNodes.depth.textContent = actualDepth === null ? words.notRated : `${actualDepth}/4`;
      metricNodes.usefulness.textContent = usefulness === null ? words.notRated : `${usefulness}/4`;
      metricNodes.review.textContent = reviewNeed === null ? words.notRated : `${reviewNeed}/4 · ${[words.extensive, words.moderate, words.targeted, words.minimal][reviewNeed - 1]}`;
      summary.textContent = answered === 5 ? words.summaryReady : words.summaryPartial;

      const original = asArray(before['review-topic']).map(topic => topicLabel(config, topic));
      asArray(before['expected-result']).forEach(item => original.push(`${words.expectedPrefix}: ${outcomeLabel(config, item)}.`));
      if (before['starting-goal']) original.push(`${words.goalPrefix}: ${before['starting-goal']}`);
      reflectionList(evaluationLists.original, original, words.noOriginal);
      reflectionList(evaluationLists.remaining, remaining.map(topic => topicLabel(config, topic)), words.noRemaining);
      reflectionList(evaluationLists.recommendations, recommendations.slice(0, 7), words.recExam);
      const plan = [];
      if (after['most-helpful']) plan.push(`${words.helpfulPrefix}: ${after['most-helpful']}`);
      if (after['next-step']) plan.push(`${words.nextPrefix}: ${after['next-step']}`);
      reflectionList(evaluationLists.plan, plan, words.noPlan);
      result.hidden = false;
      result.scrollIntoView({behavior: 'smooth', block: 'start'});
    });
    return section;
  }

  function ensureReflection(config) {
    const host = document.querySelector(config.finalHost);
    if (!host) return null;
    const id = `course-final-reflection-${config.key}`;
    let reflection = document.getElementById(id);
    if (!reflection || reflection.dataset.language !== language()) {
      reflection?.remove();
      reflection = buildReflection(config);
    }
    if (config.finalPlacement === 'after') {
      if (host.nextElementSibling !== reflection) host.after(reflection);
    } else if (host.lastElementChild !== reflection) {
      host.append(reflection);
    }
    return reflection;
  }

  function render(config) {
    const hero = document.querySelector(config.hero);
    const paths = ensureLearningPaths(config);
    const overview = document.querySelector(config.overview);
    if (!hero || !paths || !overview) return;
    document.getElementById(`course-intro-survey-${config.key}`)?.remove();
    const survey = buildSurvey(config);
    paths.after(survey);
    survey.after(overview);
    buildOverview(config, overview);
    ensureReflection(config);
  }

  function renderAll() {
    configurations.forEach(render);
  }

  const style = make('style');
  style.id = 'course-intro-blocks-style';
  style.textContent = `
    .dual-learning-paths{box-sizing:border-box;margin:18px 0 24px;padding:24px;border:1px solid #bed6d5;border-top:5px solid #087774;border-radius:14px;background:linear-gradient(135deg,#f4fbfa 0%,#fff 50%,#faf6ff 100%);box-shadow:0 8px 22px rgba(16,65,74,.08);color:#173747}.dual-learning-heading{max-width:760px;margin:0 auto 20px;text-align:center}.dual-learning-eyebrow{margin:0 0 7px!important;color:#08706c;font-size:.75rem;font-weight:850;letter-spacing:.12em}.dual-learning-heading h3{margin:0 0 8px;color:#123b4a;font-size:clamp(1.45rem,3vw,1.75rem);line-height:1.15}.dual-learning-lead{margin:0!important;color:#4a616c;font-size:1rem;line-height:1.5}.dual-learning-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}.learning-path-card{display:flex;min-width:0;box-sizing:border-box;flex-direction:column;padding:20px;border:1px solid #c8d9dc;border-radius:12px;background:#fff}.board-path{border-top:5px solid #087774}.continuing-path{border-top:5px solid #7652a5}.learning-path-badge{align-self:flex-start;margin-bottom:12px;padding:5px 9px;border-radius:999px;font-size:.7rem;font-weight:850;letter-spacing:.06em}.board-path .learning-path-badge{background:#dff1ee;color:#075f60}.continuing-path .learning-path-badge{background:#eee6f8;color:#5d3c88}.learning-path-card h4{margin:0 0 9px;color:#173747;font-size:1.25rem}.learning-path-card p{margin:0 0 17px;color:#50666f;line-height:1.5}.learning-path-button{display:inline-flex;align-items:center;justify-content:center;min-height:44px;box-sizing:border-box;margin-top:auto;padding:10px 14px;border-radius:9px;color:#fff!important;text-decoration:none;font-weight:800;text-align:center}.board-path .learning-path-button{background:#087774}.continuing-path .learning-path-button{background:#7652a5}.dual-learning-note{margin:18px 2px 0!important;padding-top:14px;border-top:1px solid #d8e3e3;color:#65757c;font-size:.76rem;line-height:1.5}
    .course-intro-survey{box-sizing:border-box;margin:0 0 24px!important;padding:24px!important;border:1px solid #bed6d5!important;border-top:5px solid #f47e2c!important;border-radius:14px!important;background:#fff!important;box-shadow:0 8px 22px rgba(16,65,74,.08);color:#173747}.course-intro-survey-heading{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;margin-bottom:17px}.course-intro-survey-heading h3{margin:4px 0 7px;color:#123b4a;font-size:clamp(1.35rem,2.5vw,1.7rem)}.course-intro-survey-heading p{margin:0;color:#516873;line-height:1.5}.course-intro-eyebrow{display:block;color:#087774;font-size:.7rem;font-weight:900;letter-spacing:.12em}.course-intro-stage{flex:none;padding:7px 11px;border-radius:999px;background:#fff0df;color:#9a4c12;font-size:.72rem;font-weight:850}.course-intro-form{display:grid;gap:13px}.course-intro-fieldset{min-width:0;margin:0;padding:14px;border:1px solid #c6d9df;border-radius:10px;background:#fbfdfe}.course-intro-fieldset legend{padding:0 5px;color:#183f50;font-weight:850}.course-intro-help{margin:0 0 10px!important;color:#617581!important;font-size:.8rem}.course-intro-choice-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:7px}.course-intro-choice,.course-intro-radio{display:flex;align-items:flex-start;gap:8px;min-width:0;padding:8px 9px;border:1px solid #c7d8df;border-radius:7px;background:#fff;color:#294957;line-height:1.35;cursor:pointer}.course-intro-choice:has(input:checked),.course-intro-radio:has(input:checked){border-color:#087774;background:#eaf7f5;box-shadow:inset 0 0 0 1px #087774}.course-intro-choice input,.course-intro-radio input{flex:none;margin:2px 0 0;accent-color:#087774}.course-intro-choice span,.course-intro-radio span{min-width:0;font-size:.8rem}.course-intro-radio span{display:grid;gap:2px}.course-intro-radio small{color:#657985;font-size:.7rem}.course-intro-two-column{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:13px}.course-intro-radio-list{display:grid;gap:7px}.course-intro-confidence{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:8px}.course-intro-confidence .course-intro-radio{min-height:76px}.course-intro-final-row{align-items:stretch}.course-intro-goal{display:grid;grid-template-rows:auto 1fr;gap:7px;color:#183f50;font-size:.8rem;font-weight:850}.course-intro-goal textarea{box-sizing:border-box;width:100%;min-height:132px;padding:12px;border:1px solid #a9c1cb;border-radius:8px;color:#173747;font:inherit;resize:vertical}.course-intro-history{padding:15px;border:1px solid #acd6d3;border-radius:10px;background:#edf9f7}.course-intro-history h4{margin:5px 0 7px;color:#123f50}.course-intro-history p{margin:0 0 9px;line-height:1.45}.course-intro-history a{display:inline-flex;margin-bottom:8px;padding:8px 11px;border-radius:7px;background:#087774;color:#fff!important;font-weight:800;text-decoration:none}.course-intro-history small{display:block;color:#56717a;line-height:1.45}.course-intro-save-row{display:flex;align-items:center;justify-content:space-between;gap:12px}.course-intro-save-row p{margin:0;color:#08706c;font-size:.78rem;font-weight:800}.course-intro-save-row a,.course-intro-outcome a{display:inline-flex;align-items:center;justify-content:center;min-height:42px;padding:9px 15px;border-radius:8px;background:#f47e2c;color:#142a32!important;font-weight:850;text-decoration:none}
    .course-intro-enhanced{box-sizing:border-box}.course-intro-overview{margin-top:14px;overflow:hidden;border:1px solid #bed6d5;border-radius:12px;background:#fff;box-shadow:0 7px 20px rgba(16,65,74,.07);color:#173747}.course-intro-overview-heading{display:grid;grid-template-columns:minmax(0,1fr) minmax(150px,220px);align-items:center;gap:18px;padding:22px;background:linear-gradient(125deg,#edf9f7,#fff 55%,#faf5ff)}.course-intro-overview-heading h3{margin:5px 0 8px;color:#123b4a;font-size:clamp(1.4rem,2.8vw,1.8rem)}.course-intro-overview-heading p{margin:0;color:#4f6772;line-height:1.55}.course-intro-course-badge{display:grid;place-items:center;align-content:center;min-height:112px;padding:13px;border:2px solid #087774;border-radius:50%;color:#08706c;text-align:center}.course-intro-course-badge strong{font-size:1rem;line-height:1.15}.course-intro-course-badge span{margin-top:5px;font-size:.68rem;font-weight:850;letter-spacing:.08em;text-transform:uppercase}.course-intro-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));border-top:1px solid #d4e1e4;border-bottom:1px solid #d4e1e4}.course-intro-stats article{display:grid;place-items:center;align-content:center;min-height:88px;padding:10px;text-align:center}.course-intro-stats article+article{border-left:1px solid #d4e1e4}.course-intro-stats strong{color:#087774;font-size:1.5rem}.course-intro-stats span{margin-top:3px;color:#5a6f78;font-size:.68rem;font-weight:850;letter-spacing:.04em;text-transform:uppercase}.course-intro-framework,.course-intro-method{padding:20px 22px}.course-intro-framework h4,.course-intro-method h4{margin:0 0 12px;color:#123f50}.course-intro-framework>div{display:flex;flex-wrap:wrap;gap:8px}.course-intro-framework span{padding:6px 10px;border-radius:999px;background:#e6f4f1;color:#086864;font-size:.75rem;font-weight:850}.course-intro-method{border-top:1px solid #d7e3e5}.course-intro-method ol{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;margin:0;padding:0;list-style:none;counter-reset:intro-method}.course-intro-method li{position:relative;display:grid;gap:5px;min-width:0;padding:41px 12px 13px;border:1px solid #c9dce0;border-radius:9px;background:#fbfdfe;counter-increment:intro-method}.course-intro-method li::before{content:counter(intro-method);position:absolute;top:10px;left:12px;display:grid;place-items:center;width:24px;height:24px;border-radius:50%;background:#f47e2c;color:#152d36;font-weight:900}.course-intro-method b{color:#123f50}.course-intro-method span{color:#5a707a;font-size:.76rem;line-height:1.45}.course-intro-outcome{display:flex;align-items:center;justify-content:space-between;gap:20px;padding:18px 22px;background:#edf8f6}.course-intro-outcome b{color:#08706c}.course-intro-outcome p{max-width:760px;margin:5px 0 0;line-height:1.5}.course-intro-outcome a{flex:none;background:#087774;color:#fff!important}
    .course-final-reflection{box-sizing:border-box;margin:30px auto!important;padding:24px!important;border:1px solid #c9b8df!important;border-top:6px solid #7652a5!important;border-radius:14px!important;background:#fff!important;box-shadow:0 9px 24px rgba(52,31,78,.09);color:#173747;scroll-margin-top:90px}.course-final-reflection-heading{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;margin-bottom:18px}.course-final-reflection-heading h3{margin:4px 0 7px;color:#123b4a;font-size:clamp(1.4rem,2.7vw,1.8rem)}.course-final-reflection-heading p{margin:0;color:#516873;line-height:1.5}.course-final-reflection-stage{flex:none;padding:7px 11px;border-radius:999px;background:#eee6f8;color:#5d3c88;font-size:.72rem;font-weight:850}.course-final-reflection-form{display:grid;gap:13px}.course-final-exam-history{display:flex;align-items:center;justify-content:space-between;gap:18px;padding:15px;border:1px solid #acd6d3;border-left:5px solid #087774;border-radius:10px;background:#edf9f7}.course-final-exam-history h4{margin:5px 0 7px;color:#123f50}.course-final-exam-history p{margin:0;color:#47616b;line-height:1.45}.course-final-exam-history a{display:inline-flex;flex:none;align-items:center;justify-content:center;min-height:42px;padding:9px 14px;border-radius:8px;background:#087774;color:#fff!important;font-weight:850;text-decoration:none}.course-final-scale-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:13px}.course-final-text-field{display:grid;gap:7px;color:#183f50;font-size:.8rem;font-weight:850}.course-final-text-field textarea{box-sizing:border-box;width:100%;min-height:92px;padding:12px;border:1px solid #a9c1cb;border-radius:8px;color:#173747;font:inherit;resize:vertical}.course-final-actions{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:16px;border-radius:10px;background:#173f51}.course-final-actions p{margin:0;color:#d9e8ec;font-size:.78rem;font-weight:800}.course-final-actions button{min-height:44px;padding:10px 16px;border:0;border-radius:8px;background:#f47e2c;color:#142a32;font:inherit;font-weight:900;cursor:pointer}.course-final-result{margin-top:18px;padding:21px;border:1px solid #c9b8df;border-top:5px solid #7652a5;border-radius:12px;background:#fff}.course-final-result[hidden]{display:none}.course-final-result-heading{display:flex;align-items:flex-start;justify-content:space-between;gap:15px}.course-final-result-heading h3{margin:5px 0 0;color:#123b4a}.course-final-result-level{padding:7px 10px;border-radius:999px;background:#eee6f8;color:#5d3c88;font-size:.72rem;font-weight:850}.course-final-metrics{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));overflow:hidden;margin:18px 0;border:1px solid #d9cbe8;border-radius:10px}.course-final-metrics article{min-width:0;padding:14px;text-align:center}.course-final-metrics article+article{border-left:1px solid #e1d7ec}.course-final-metrics strong,.course-final-metrics span{display:block}.course-final-metrics strong{color:#69409a;font-size:1.1rem;overflow-wrap:anywhere}.course-final-metrics span{margin-top:4px;color:#607080;font-size:.68rem;font-weight:900;letter-spacing:.04em;text-transform:uppercase}.course-final-summary{padding:12px;border-left:4px solid #087774;background:#edf8f6;line-height:1.5}.course-final-evaluation-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.course-final-evaluation-grid article{padding:15px;border:1px solid #d5e0e3;border-radius:9px;background:#fbfdfe}.course-final-evaluation-grid h4{margin:0 0 9px;color:#123f50}.course-final-evaluation-grid ul,.course-final-evaluation-grid ol{margin:0;padding-left:20px}.course-final-evaluation-grid li{margin:6px 0;line-height:1.45}.course-final-note{margin:15px 0 0!important;color:#61727a;font-size:.78rem;line-height:1.5}
    @media(max-width:760px){.dual-learning-paths{padding:18px 14px}.dual-learning-grid{grid-template-columns:1fr}.learning-path-card{padding:17px}.learning-path-button{width:100%}.course-intro-survey{padding:18px 14px!important}.course-intro-survey-heading{display:grid}.course-intro-stage{justify-self:start}.course-intro-choice-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.course-intro-confidence{grid-template-columns:repeat(2,minmax(0,1fr))}.course-intro-overview-heading{grid-template-columns:1fr}.course-intro-course-badge{justify-self:center;width:145px;min-height:145px;box-sizing:border-box}.course-intro-stats{grid-template-columns:repeat(2,minmax(0,1fr))}.course-intro-stats article:nth-child(3){border-left:0;border-top:1px solid #d4e1e4}.course-intro-stats article:nth-child(4){border-top:1px solid #d4e1e4}.course-intro-method ol{grid-template-columns:repeat(2,minmax(0,1fr))}.course-intro-outcome{align-items:stretch;flex-direction:column}.course-intro-outcome a{width:100%;box-sizing:border-box}.course-final-reflection{padding:18px 14px!important}.course-final-reflection-heading{display:grid}.course-final-reflection-stage{justify-self:start}.course-final-exam-history{align-items:stretch;flex-direction:column}.course-final-exam-history a{width:100%;box-sizing:border-box}.course-final-metrics{grid-template-columns:repeat(2,minmax(0,1fr))}.course-final-metrics article:nth-child(3){border-left:0;border-top:1px solid #e1d7ec}.course-final-metrics article:nth-child(4){border-top:1px solid #e1d7ec}}
    @media(max-width:520px){.course-intro-two-column,.course-intro-choice-grid,.course-intro-confidence,.course-intro-method ol,.course-final-scale-grid,.course-final-evaluation-grid{grid-template-columns:1fr}.course-intro-save-row,.course-final-actions{align-items:stretch;flex-direction:column}.course-intro-save-row a,.course-final-actions button{width:100%;box-sizing:border-box}.course-intro-stats strong{font-size:1.3rem}.course-final-result-heading{display:grid}.course-final-result-level{justify-self:start}}
  `;
  document.head.append(style);

  const ready = () => configurations.every(config =>
    document.getElementById(`course-intro-survey-${config.key}`) &&
    document.querySelector(`${config.overview} > .course-intro-overview`) &&
    document.getElementById(`course-final-reflection-${config.key}`)
  );
  let renderQueued = false;
  const readinessObserver = new MutationObserver(() => {
    if (ready()) return;
    if (renderQueued) return;
    renderQueued = true;
    queueMicrotask(() => {
      renderQueued = false;
      renderAll();
    });
  });
  readinessObserver.observe(document.body, {childList: true, subtree: true});
  renderAll();
  [0, 250, 1000, 2500].forEach(delay => setTimeout(renderAll, delay));
  new MutationObserver(renderAll).observe(document.documentElement, {attributes: true, attributeFilter: ['lang']});
})();
