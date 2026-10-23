(function () {
  'use strict';

  const button = document.getElementById('language-toggle');
  if (!button) return;

  const exact = new Map(Object.entries({
    'Skip to study content': 'Saltar al contenido de estudio',
    'Acupuncture & Point Location': 'Acupuntura y localización de puntos',
    'Study Navigation': 'Navegación de estudio',
    'Start here': 'Comienza aquí',
    'Introduction': 'Introducción',
    'Two learning paths': 'Dos rutas de aprendizaje',
    'Course overview': 'Descripción del curso',
    'NCBAHM Exam Learning Plan': 'Plan de aprendizaje para el examen NCBAHM',
    'Chapter reading': 'Lectura del capítulo',
    'Pathway objectives': 'Objetivos de los recorridos',
    'Practice & review': 'Práctica y repaso',
    'Visual review gallery': 'Galería de repaso visual',
    'Flashcards': 'Tarjetas de estudio',
    'Memory tips': 'Recursos mnemotécnicos',
    'Anatomical region atlas': 'Atlas de regiones anatómicas',
    'Region flashcards': 'Tarjetas de regiones',
    'Practice without score': 'Práctica sin puntuación',
    'Review objectives': 'Repasar los objetivos',
    'My notes': 'Mis notas',
    'Further reading': 'Lectura adicional',
    'Continuing Education Learning Plan': 'Plan de aprendizaje de educación continua',
    'Continuing Education alignment': 'Correspondencia con educación continua',
    'Exams': 'Exámenes',
    'Chapter Review': 'Repaso del capítulo',
    'School Level Exam': 'Examen de nivel escolar',
    'Board Level Exam': 'Examen de nivel Board',
    'Continuing Education Exam': 'Examen de educación continua',
    'STUDY GUIDE · CHANNEL PATHWAYS': 'GUÍA DE ESTUDIO · RECORRIDOS DE LOS CANALES',
    'Channel Pathway Identification': 'Identificación de los recorridos de los canales',
    '← Back to Study Guide index': '← Volver al índice de la guía de estudio',
    'CHAPTER LEARNING PATHS': 'RUTAS DE APRENDIZAJE DEL CAPÍTULO',
    'One Chapter, Two Learning Paths': 'Un capítulo, dos rutas de aprendizaje',
    'BOARD PREPARATION': 'PREPARACIÓN PARA EL BOARD',
    'Board Exam Preparation': 'Preparación para el examen Board',
    'Start Board Preparation': 'Comenzar preparación para el Board',
    'CONTINUING EDUCATION': 'EDUCACIÓN CONTINUA',
    'Continuing Education': 'Educación continua',
    'Start Continuing Education': 'Comenzar educación continua',
    'Course Overview': 'Descripción del curso',
    'Channel Pathways': 'Recorridos de los canales',
    'NCBAHM Exam Learning Plan': 'Plan de aprendizaje para el examen NCBAHM',
    'Chapter Reading': 'Lectura del capítulo',
    'A four-step visual identification method': 'Método visual de identificación en cuatro pasos',
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
    'Anatomical regions': 'Regiones anatómicas',
    'Internal connections': 'Conexiones internas',
    'Convergence landmarks': 'Puntos de convergencia',
    'Integrated pathway mapping': 'Mapa integrado de recorridos',
    'Organize channel relationships by anatomical region and body surface': 'Organizar las relaciones de los canales por región anatómica y superficie corporal',
    'Analyze internal-organ, cavity and diaphragm relationships': 'Analizar las relaciones con órganos internos, cavidades y diafragma',
    'Compare major convergence and transition landmarks': 'Comparar los principales puntos de convergencia y transición',
    'Construct and defend an integrated pathway map for an unfamiliar region': 'Construir y justificar un mapa integrado de recorridos para una región no familiar',
    'Chapter Summary Image — Visual Review Gallery': 'Imagen resumen del capítulo — Galería de repaso visual',
    'Flashcards — Channel Recognition': 'Tarjetas de estudio — Reconocimiento de canales',
    'Pathway Flashcards': 'Tarjetas de recorridos',
    'Flashcard set': 'Grupo de tarjetas',
    'Tap image to enlarge': 'Toca la imagen para ampliarla',
    'Which channel or vessel is shown?': '¿Qué canal o vaso se muestra?',
    'Say the system, complete name and two visual clues before revealing the answer.': 'Di el sistema, el nombre completo y dos pistas visuales antes de revelar la respuesta.',
    '← Previous': '← Anterior',
    'Flip card': 'Voltear tarjeta',
    'Show question': 'Mostrar pregunta',
    'Next →': 'Siguiente →',
    'Shuffle': 'Mezclar',
    'Enlarge image': 'Ampliar imagen',
    'Mnemonic Resources and Recognition Tips': 'Recursos mnemotécnicos y consejos de reconocimiento',
    'Remember the system before the name': 'Recuerda el sistema antes del nombre',
    'Certificate requirement:': 'Requisito para el certificado:',
    'Certificate': 'Certificado',
    'Issued after the mandatory module exam': 'Se emite después del examen obligatorio del módulo',
    'Planning time': 'Tiempo planificado',
    '2 hours · mandatory exam time to be added': '2 horas · falta añadir el tiempo del examen obligatorio',
    '3 hours · mandatory exam time to be added': '3 horas · falta añadir el tiempo del examen obligatorio',
    'CE credits': 'Créditos de educación continua',
    '2 credits · whole credits only': '2 créditos · solo créditos completos',
    '3 credits · whole credits only': '3 créditos · solo créditos completos',
    'Current total and CE credits': 'Total actual y créditos de educación continua',
    '120 minutes · 2 credits': '120 minutos · 2 créditos',
    '180 minutes · 3 credits': '180 minutos · 3 créditos',
    'Mandatory for certificate · Other exams optional': 'Obligatorio para el certificado · Los demás exámenes son opcionales',
    'CERTIFICATE PREVIEW': 'VISTA PREVIA DEL CERTIFICADO',
    'Your Continuing Education Certificate': 'Tu certificado de educación continua',
    'Certificate of Completion': 'Certificado de finalización',
    'This certifies that': 'Se certifica que',
    'Student Name': 'Nombre del estudiante',
    'has successfully completed the Continuing Education course': 'ha completado satisfactoriamente el curso de educación continua',
    'Channel Pathway Identification and Comparison': 'Identificación y comparación de recorridos de canales',
    '2 CE Credits': '2 créditos de educación continua',
    '3 CE Credits': '3 créditos de educación continua',
    'Credits earned': 'Créditos obtenidos',
    'Completion Date': 'Fecha de finalización',
    'Date completed': 'Fecha completada',
    'Certificate ID': 'Número de certificado',
    'Verification number': 'Número de verificación',
    'Course Instructor': 'Instructor del curso',
    'Program Director': 'Director del programa',
    'Preview:': 'Vista previa:',
    'DOWNLOADABLE COURSE DOCUMENT': 'DOCUMENTO DESCARGABLE DEL CURSO',
    'Complete Continuing Education Course Summary': 'Resumen completo del curso de educación continua',
    'Download Word Summary ↓': 'Descargar resumen en Word ↓',
    'Areas Reached by the Channels — Anatomical Region Atlas': 'Áreas alcanzadas por los canales — Atlas de regiones anatómicas',
    'ANATOMICAL REFERENCE': 'REFERENCIA ANATÓMICA',
    'Find every channel that reaches an area': 'Encuentra todos los canales que alcanzan una región',
    'Search the atlas': 'Buscar en el atlas',
    'Body region': 'Región corporal',
    'All regions': 'Todas las regiones',
    'Expand visible': 'Abrir visibles',
    'Collapse all': 'Cerrar todas',
    'anatomical areas': 'regiones anatómicas',
    'Memory cue': 'Pista mnemotécnica',
    'Anatomical Region Flashcards — Reverse Recall': 'Tarjetas de regiones anatómicas — Recuerdo inverso',
    'REVERSE RECALL': 'RECUERDO INVERSO',
    'Which channels reach this area?': '¿Qué canales alcanzan esta región?',
    'AREA': 'REGIÓN',
    'Reveal pathways': 'Mostrar recorridos',
    'Self-Test — Practice without score': 'Autoevaluación — Práctica sin puntuación',
    'What channel or vessel is shown?': '¿Qué canal o vaso se muestra?',
    'Reveal identity': 'Mostrar identidad',
    'New image': 'Nueva imagen',
    'My Student Notes': 'Mis notas de estudiante',
    'Check answer': 'Comprobar respuesta',
    'Reset this Mock 10': 'Reiniciar este Mock 10',
    'Reset this Exam Mock 10': 'Reiniciar este Exam Mock 10',
    'Reset this practice exam': 'Reiniciar este examen de práctica',
    'Select one answer first.': 'Selecciona primero una respuesta.',
    'Correct': 'Correcto',
    'Review this answer': 'Revisa esta respuesta',
    'Why are the other options incorrect?': '¿Por qué las otras opciones son incorrectas?',
    'Open Mock 10': 'Abrir Mock 10',
    'Open Exam Mock 10': 'Abrir Exam Mock 10',
    'Open Practice Exam': 'Abrir examen de práctica',
    'Approval pending · Practice preview only': 'Aprobación pendiente · Solo vista previa de práctica',
    'Study needs survey': 'Encuesta de necesidades de estudio',
    'End-of-study reflection': 'Reflexión final del estudio',
    'My Exam Mock 10 Progress': 'Mi progreso en Exam Mock 10',
    'My Practice Exam Progress': 'Mi progreso en los exámenes de práctica',
    'My Practice Exam Results': 'Mis resultados de los exámenes de práctica',
    'BEFORE YOU STUDY · 3–5 MINUTES': 'ANTES DE ESTUDIAR · 3–5 MINUTOS',
    'What do you need from this course?': '¿Qué necesitas de este curso?',
    'Select the areas you believe you need to review and define the result you expect. Your answers will be compared with your final reflection.': 'Selecciona las áreas que consideras necesario repasar y define el resultado que esperas. Tus respuestas se compararán con la reflexión final.',
    '1 · Starting point': '1 · Punto de partida',
    'Which topics do you need to review?': '¿Qué temas necesitas repasar?',
    'Choose every topic that currently feels uncertain.': 'Selecciona todos los temas en los que aún tienes dudas.',
    'Primary directional groups': 'Grupos direccionales de los canales principales',
    'Primary routes and internal branches': 'Recorridos principales y ramas internas',
    'Divergent Channels': 'Canales divergentes',
    'Luo-Connecting Channels': 'Canales Luo de conexión',
    'Sinew Channels': 'Canales tendinomusculares',
    'Extraordinary Vessels': 'Vasos extraordinarios',
    'Internal connections and action words': 'Conexiones internas y verbos de recorrido',
    'Image identification and look-alikes': 'Identificación de imágenes y recorridos similares',
    'What results do you expect?': '¿Qué resultados esperas?',
    'Choose one or more practical outcomes.': 'Selecciona uno o más resultados prácticos.',
    'Recognize the channel system': 'Reconocer el sistema de canales',
    'Trace a complete pathway': 'Trazar un recorrido completo',
    'Distinguish look-alike routes': 'Distinguir recorridos similares',
    'Justify with two landmarks': 'Justificar con dos referencias anatómicas',
    'Improve my mock-exam score': 'Mejorar mi puntuación en los simulacros',
    'Improve my practice-exam score': 'Mejorar mi puntuación en los exámenes de práctica',
    'Prepare for Board or CE assessment': 'Prepararme para el Board o educación continua',
    'How deeply do you plan to study?': '¿Con qué profundidad planeas estudiar?',
    'How much study time do you expect to need?': '¿Cuánto tiempo de estudio crees necesitar?',
    '1 · Overview': '1 · Vista general',
    '2 · Working knowledge': '2 · Conocimiento funcional',
    '3 · Board application': '3 · Aplicación para el Board',
    '4 · Teach-back mastery': '4 · Dominio para explicarlo',
    'Less than 30 minutes': 'Menos de 30 minutos',
    '30–60 minutes': '30–60 minutos',
    '1–2 hours': '1–2 horas',
    '2–3 hours': '2–3 horas',
    'More than 3 hours': 'Más de 3 horas',
    'How confident are you right now?': '¿Qué nivel de confianza tienes ahora?',
    'Use the descriptions—not the number alone—to choose your starting point.': 'Usa las descripciones, no solo el número, para elegir tu punto de partida.',
    '1 · Not yet': '1 · Todavía no',
    '2 · With notes': '2 · Con apuntes',
    '3 · Independently': '3 · De forma independiente',
    '4 · I can teach it': '4 · Puedo enseñarlo',
    'My most important goal for this course': 'Mi objetivo más importante para este curso',
    'Baseline mock score, if available': 'Puntuación inicial del simulacro, si está disponible',
    'Leave blank if you have not taken a related mock.': 'Déjalo en blanco si no has realizado un simulacro relacionado.',
    'Have you taken a related mock exam before starting this course?': '¿Realizaste un simulacro relacionado antes de comenzar este curso?',
    'Have you taken a related mock exam after studying?': '¿Realizaste un simulacro relacionado después de estudiar?',
    'Yes': 'Sí',
    'No': 'No',
    'If yes, enter your score': 'Si respondes sí, escribe tu puntuación',
    'If yes, enter your latest score': 'Si respondes sí, escribe tu puntuación más reciente',
    'Enter the percentage without the % sign.': 'Escribe el porcentaje sin el signo %.',
    'If no, continue without entering a score.': 'Si respondes no, continúa sin escribir una puntuación.',
    'This provides an objective check alongside your self-assessment.': 'Esto aporta una comprobación objetiva junto con tu autoevaluación.',
    'AUTOMATIC MOCK HISTORY': 'HISTORIAL AUTOMÁTICO DE SIMULACROS',
    'AUTOMATIC EXAM HISTORY': 'HISTORIAL AUTOMÁTICO DE EXÁMENES',
    'You do not need to remember a score': 'No necesitas recordar una puntuación',
    'From now on, this guide saves the answers you check in every Mock 10 and calculates the results automatically.': 'Desde ahora, esta guía guarda las respuestas que compruebes en cada Mock 10 y calcula los resultados automáticamente.',
    'From now on, this guide saves the answers you check in every Exam Mock 10 and calculates the results automatically by objective.': 'Desde ahora, esta guía guarda las respuestas que compruebes en cada Exam Mock 10 y calcula los resultados automáticamente por objetivo.',
    'From now on, this guide saves the answers you check in every 10-question practice exam and calculates the results automatically by objective.': 'Desde ahora, esta guía guarda las respuestas que compruebes en cada examen de práctica de 10 preguntas y calcula los resultados automáticamente por objetivo.',
    'No saved Mock 10 results yet.': 'Todavía no hay resultados guardados de Mock 10.',
    'No saved Exam Mock 10 results yet.': 'Todavía no hay resultados guardados de Exam Mock 10.',
    'No saved practice exam results yet.': 'Todavía no hay resultados guardados de exámenes de práctica.',
    'Open Board Mock 10 activities': 'Abrir las actividades Mock 10 del Board',
    'Open Board Exam Mock 10 activities': 'Abrir las actividades Exam Mock 10 del Board',
    'Open Board practice exams': 'Abrir los exámenes de práctica del Board',
    'Only Mock 10 activities completed inside this guide can be read here. Official Moodle exam scores remain in Moodle Exam History.': 'Aquí solo se pueden leer las actividades Mock 10 realizadas dentro de esta guía. Las puntuaciones oficiales de Moodle permanecen en el historial de exámenes de Moodle.',
    'Only Exam Mock 10 activities completed inside this guide can be read here. Official Moodle exam scores remain in Moodle Exam History.': 'Aquí solo se pueden leer las actividades Exam Mock 10 realizadas dentro de esta guía. Las puntuaciones oficiales de Moodle permanecen en el historial de exámenes de Moodle.',
    'Only practice exams completed inside this guide can be read here. Official Moodle exam scores remain in Moodle Exam History.': 'Aquí solo se pueden leer los exámenes de práctica realizados dentro de esta guía. Las puntuaciones oficiales de Moodle permanecen en el historial de exámenes de Moodle.',
    'AUTOMATIC PRACTICE SUMMARY': 'RESUMEN AUTOMÁTICO DE PRÁCTICA',
    'Your saved Mock 10 results': 'Tus resultados guardados de Mock 10',
    'AUTOMATIC EXAM SUMMARY': 'RESUMEN AUTOMÁTICO DE EXÁMENES',
    'Your saved Exam Mock 10 results by objective': 'Tus resultados guardados de Exam Mock 10 por objetivo',
    'Your saved practice exam results by objective': 'Tus resultados guardados de los exámenes de práctica por objetivo',
    'No activity yet': 'Todavía no hay actividad',
    'Mocks completed': 'Simulacros completados',
    'Exams completed': 'Exámenes completados',
    'Completed-mock average': 'Promedio de simulacros completados',
    'Completed-exam average': 'Promedio de exámenes completados',
    'Mocks in progress': 'Simulacros en curso',
    'Exams in progress': 'Exámenes en curso',
    'Complete questions in a Mock 10 to see its progress here.': 'Completa preguntas en un Mock 10 para ver aquí su progreso.',
    'Complete questions in an Exam Mock 10 to see its objective and progress here.': 'Completa preguntas en un Exam Mock 10 para ver aquí su objetivo y progreso.',
    'Complete questions in a practice exam to see its objective and progress here.': 'Completa preguntas en un examen de práctica para ver aquí su objetivo y progreso.',
    'What is included?': '¿Qué se incluye?',
    'The 12 objective mocks and the Continuing Education practice mock inside this guide. Moodle School Level and official Continuing Education exam scores remain in Moodle Exam History and are not transmitted to this page.': 'Los 12 simulacros de los objetivos y el simulacro de práctica de educación continua incluidos en esta guía. Las puntuaciones de los exámenes School Level y del examen oficial de educación continua permanecen en el historial de Moodle y no se transmiten a esta página.',
    'The 12 objective Exam Mock 10 activities and the Continuing Education Exam Mock 10 inside this guide. Moodle School Level and official Continuing Education exam scores remain in Moodle Exam History and are not transmitted to this page.': 'Las 12 actividades Exam Mock 10 de los objetivos y el Exam Mock 10 de educación continua incluidos en esta guía. Las puntuaciones de los exámenes School Level y del examen oficial de educación continua permanecen en el historial de Moodle y no se transmiten a esta página.',
    'The 12 objective practice exams and the Continuing Education practice exam inside this guide. Moodle School Level and official Continuing Education exam scores remain in Moodle Exam History and are not transmitted to this page.': 'Los 12 exámenes de práctica de los objetivos y el examen de práctica de educación continua incluidos en esta guía. Las puntuaciones de los exámenes School Level y del examen oficial de educación continua permanecen en el historial de Moodle y no se transmiten a esta página.',
    'Automatic mock results': 'Resultados automáticos de simulacros',
    'Automatic practice exam results': 'Resultados automáticos de los exámenes de práctica',
    'VISIBLE AUTOMATIC RECORD': 'REGISTRO AUTOMÁTICO VISIBLE',
    'AUTOMATIC PRACTICE EXAM RECORD': 'REGISTRO AUTOMÁTICO DE EXÁMENES DE PRÁCTICA',
    'One clear record for all 12 objective exams and the Continuing Education practice exam.': 'Un solo registro claro para los 12 exámenes de los objetivos y el examen de práctica de educación continua.',
    '13 practice exams tracked automatically in this browser': '13 exámenes de práctica registrados automáticamente en este navegador',
    'This panel updates immediately whenever you check an answer in an Exam Mock 10.': 'Este panel se actualiza inmediatamente cada vez que compruebas una respuesta en un Exam Mock 10.',
    'This panel updates immediately whenever you check an answer in a 10-question practice exam.': 'Este panel se actualiza inmediatamente cada vez que compruebas una respuesta en un examen de práctica de 10 preguntas.',
    'Latest and best result for each objective': 'Último y mejor resultado de cada objetivo',
    'Average of all completed Exam Mock 10 activities': 'Promedio de todas las actividades Exam Mock 10 completadas',
    'Average of all completed practice exams': 'Promedio de todos los exámenes de práctica completados',
    'Completed examinations and examinations in progress': 'Exámenes completados y exámenes en curso',
    'Completed exams and exams in progress': 'Exámenes completados y exámenes en curso',
    'Automatic identification of the lowest-scoring objectives': 'Identificación automática de los objetivos con menor puntuación',
    'Simple final evaluation with personalized recommendations': 'Evaluación final sencilla con recomendaciones personalizadas',
    'Results saved automatically in this browser': 'Resultados guardados automáticamente en este navegador',
    'Current average': 'Promedio actual',
    'No Exam Mock 10 activity has been recorded yet. Open an exam below and check an answer to begin.': 'Todavía no se ha registrado ninguna actividad Exam Mock 10. Abre un examen abajo y comprueba una respuesta para comenzar.',
    'No practice exam activity has been recorded yet. Open an exam below and check an answer to begin.': 'Todavía no se ha registrado ninguna actividad de examen de práctica. Abre un examen abajo y comprueba una respuesta para comenzar.',
    'Saved automatically:': 'Guardado automáticamente:',
    'You do not need to remember or type a score. Results from Moodle examinations remain in Moodle Exam History because Moodle does not send those grades to this page.': 'No necesitas recordar ni escribir una puntuación. Los resultados de los exámenes de Moodle permanecen en el historial de exámenes porque Moodle no envía esas calificaciones a esta página.',
    'Open My Final Evaluation →': 'Abrir mi evaluación final →',
    'AUTOMATIC RESULT CONNECTION': 'CONEXIÓN AUTOMÁTICA DE RESULTADOS',
    'Your practice exam results are included automatically': 'Tus resultados de los exámenes de práctica se incluyen automáticamente',
    'The reflection below uses the results saved in your main Practice Exam Results panel. You do not need to remember or enter a score again.': 'La reflexión siguiente utiliza los resultados guardados en el panel principal de resultados. No necesitas recordar ni volver a escribir una puntuación.',
    '12 objective exams + 1 CE practice exam': '12 exámenes de objetivos + 1 examen de práctica de educación continua',
    'Practice in progress': 'Práctica en curso',
    'Foundation review recommended': 'Se recomienda repasar los fundamentos',
    'Developing': 'En desarrollo',
    'Approaching independent readiness': 'Acercándose al trabajo independiente',
    'Strong practice performance': 'Rendimiento sólido en la práctica',
    'No completed mocks': 'No hay simulacros completados',
    'No completed practice exams': 'No hay exámenes de práctica completados',
    'Finish this mock to include it in the average.': 'Termina este simulacro para incluirlo en el promedio.',
    'Finish this exam to include it in the average.': 'Termina este examen para incluirlo en el promedio.',
    'Responses save automatically in this browser.': 'Las respuestas se guardan automáticamente en este navegador.',
    'Begin the course →': 'Comenzar el curso →',
    'AFTER YOU STUDY · 5 MINUTES': 'DESPUÉS DE ESTUDIAR · 5 MINUTOS',
    'Did the course meet your learning needs?': '¿El curso respondió a tus necesidades de aprendizaje?',
    'Compare your starting point with what you can do now. This reflection measures perceived progress; use your mock-exam results as the objective check.': 'Compara tu punto de partida con lo que puedes hacer ahora. Esta reflexión mide el progreso percibido; usa los resultados de los simulacros como comprobación objetiva.',
    'Compare your starting point with what you can do now. This reflection measures perceived progress; use your practice-exam results as the objective check.': 'Compara tu punto de partida con lo que puedes hacer ahora. Esta reflexión mide el progreso percibido; usa los resultados de los exámenes de práctica como comprobación objetiva.',
    '2 · Reflection': '2 · Reflexión',
    'How confident are you now?': '¿Qué nivel de confianza tienes ahora?',
    'Choose the same scale used at the beginning so the comparison is meaningful.': 'Usa la misma escala del inicio para que la comparación sea significativa.',
    'Latest related mock score, if available': 'Puntuación más reciente del simulacro, si está disponible',
    'A score gives an objective check alongside your self-assessment.': 'La puntuación aporta una comprobación objetiva junto con tu autoevaluación.',
    'How deeply did you study?': '¿Con qué profundidad estudiaste?',
    'How much more review do you need?': '¿Cuánto repaso adicional necesitas?',
    'How useful was the course?': '¿Qué tan útil fue el curso?',
    'Were your expected results achieved?': '¿Se alcanzaron los resultados que esperabas?',
    'Which topics still need review?': '¿Qué temas todavía necesitas repasar?',
    'Select only the areas that should appear in your recommendation.': 'Selecciona solamente las áreas que deben aparecer en tu recomendación.',
    'What helped you most?': '¿Qué te ayudó más?',
    'What will you do next?': '¿Qué harás a continuación?',
    'Generate My Study Reflection': 'Generar mi reflexión de estudio',
    'YOUR BEFORE–AFTER REVIEW': 'TU COMPARACIÓN ANTES–DESPUÉS',
    'Study reflection and recommendations': 'Reflexión y recomendaciones de estudio',
    'Reflection ready': 'Reflexión preparada',
    'Confidence change': 'Cambio de confianza',
    'Mock score': 'Puntuación del simulacro',
    'Study depth': 'Profundidad del estudio',
    'Course usefulness': 'Utilidad del curso',
    'Review need': 'Necesidad de repaso',
    'Your original learning priorities': 'Tus prioridades iniciales de aprendizaje',
    'Topics to review next': 'Temas que debes repasar',
    'Personalized recommendations': 'Recomendaciones personalizadas',
    'Your stated plan': 'Tu plan personal'
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
    match = key.match(/^Correct answer:\s*(.*)$/);
    if (match) return `${leading}Respuesta correcta: ${match[1]}${trailing}`;
    match = key.match(/^Feedback:\s*(.*)$/);
    if (match) return `${leading}Retroalimentación: ${match[1]}${trailing}`;
    match = key.match(/^(\d+) of (\d+) Mock 10 activities completed · (\d+)% average$/);
    if (match) return `${leading}${match[1]} de ${match[2]} actividades Mock 10 completadas · promedio de ${match[3]} %${trailing}`;
    match = key.match(/^(\d+) Mock 10 (activity|activities) in progress$/);
    if (match) return `${leading}${match[1]} ${Number(match[1]) === 1 ? 'actividad' : 'actividades'} Mock 10 en curso${trailing}`;
    match = key.match(/^(\d+) of (\d+) Exam Mock 10 activities completed · (\d+)% average$/);
    if (match) return `${leading}${match[1]} de ${match[2]} actividades Exam Mock 10 completadas · promedio de ${match[3]} %${trailing}`;
    match = key.match(/^(\d+) Exam Mock 10 (activity|activities) in progress$/);
    if (match) return `${leading}${match[1]} ${Number(match[1]) === 1 ? 'actividad' : 'actividades'} Exam Mock 10 en curso${trailing}`;
    match = key.match(/^(\d+) of (\d+) practice exams completed · (\d+)% average$/);
    if (match) return `${leading}${match[1]} de ${match[2]} exámenes de práctica completados · promedio de ${match[3]} %${trailing}`;
    match = key.match(/^(\d+) practice (exam|exams) in progress$/);
    if (match) return `${leading}${match[1]} ${Number(match[1]) === 1 ? 'examen de práctica en curso' : 'exámenes de práctica en curso'}${trailing}`;
    match = key.match(/^(\d+)\/(\d+) answered$/);
    if (match) return `${leading}${match[1]}/${match[2]} respondidas${trailing}`;
    match = key.match(/^(\d+) completed (attempt|attempts)( · best (\d+)%)*$/);
    if (match) return `${leading}${match[1]} ${Number(match[1]) === 1 ? 'intento completado' : 'intentos completados'}${match[4] ? ` · mejor ${match[4]} %` : ''}${trailing}`;
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
    try { localStorage.setItem('channel-pathway-language', spanish ? 'es' : 'en'); } catch (_) {}
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
  try { saved = localStorage.getItem('channel-pathway-language') || 'en'; } catch (_) {}
  if (new URLSearchParams(location.search).get('lang') === 'es') saved = 'es';
  applyLanguage(saved === 'es');
})();
