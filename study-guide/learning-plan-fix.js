(function(){
  const plan=document.querySelector('#pc-learning-plan'),table=plan?.querySelector('table');
  if(!table)return;
  const rows=[...table.querySelectorAll('tbody tr')];if(rows.length!==26)return;
  /* Source sections retain their historical import IDs. The plan presents the
     pedagogical order, while each link must still target its real section. */
  const sourceIds=[5,20,19,18,14,24,8,21,12,2,16,26,17,13,7,1,6,10,22,9,3,23,15,25,11,4];
  const en=[
    ['Distinguish the Five Shu categories and their source-listed correspondences and functions.','Jing-Well, Ying-Spring, Shu-Stream, Jing-River and He-Sea classifications; channel and Five Element correspondences and functions present in the source.','Ordered category chart, comparison of source descriptions, point identification and function review.'],
    ['Identify Jing-Well points and distinguish the source-supported characteristics of this category.','Jing-Well point numbers, matching relationships and category statements.','Reference chart, Five Shu category comparison and careful interpretation of negative questions.'],
    ['Identify Ying-Spring point correspondences and distinguish source-supported category statements.','Ying-Spring point numbers, matching relationships and true/false material; preserve source terminology with review.','Reference chart, comparison of statements and checking of negative-question answer keys.'],
    ['Identify Shu-Stream point correspondences and distinguish source-supported statements about the category.','Shu-Stream point numbers and category statements; negative questions require careful interpretation.','Reference chart, classification comparison and explicit checking of whether a source item asks for a true or incorrect statement.'],
    ['Identify Jing-River point correspondences and the source-listed relationships for this category.','Channel–Jing-River point numbers and supporting matching material.','Channel–point chart, comparison within the Five Shu system and targeted recall.'],
    ['Distinguish He-Sea points by their individual indications.','He-Sea point–indication pairs; compare with Lower He-Sea without merging the categories.','Point–indication comparison, explanatory reading of source pairs and matching.'],
    ['Identify the Lower He-Sea points and their channel correspondences in the source.','Lower He-Sea point–channel associations; distinguish this category from the separate He-Sea objective.','Category comparison chart, point–channel matching and targeted review.'],
    ['Associate the source-listed Ying-Spring and Shu-Stream combinations with their stated indications.','Combination–indication pairs in the matching activity.','Paired-point chart, explanation of each source correspondence and matching review.'],
    ['Identify Mother points for the channels represented in the source.','Channel-to-Mother-point number correspondences in the source activities.','Reference chart, recall practice and comparison with the separate Child-point objective.'],
    ['Identify Child points for the channels represented in the source.','Channel-to-Child-point number correspondences in the Fill in the Blank activities.','Channel–point reference table, cover-and-recall practice and source-based flashcards.'],
    ['Identify Xi-Cleft points and distinguish their source-listed point–indication relationships.','Xi-Cleft point identification, multiple-response statements and indication pairs.','Point–indication comparison chart and matching; avoid adding indications absent from the source.'],
    ['Associate Extraordinary Channel Xi-Cleft points with their corresponding channels.','Yang and Yin Motility and Linking vessel–Xi-Cleft point pairs in the source.','Four-pair reference chart, comparison with confluent points and matching.'],
    ['Identify Yuan-Source points for the channels represented in the source.','Channel–Yuan-Source point numbers from the Fill in the Blank activities.','Channel–point chart, retrieval practice and source-based flashcards.'],
    ['Distinguish the Luo-Connecting points and their source-listed indications.','Luo-Connecting point recognition and point–indication pairs from the matching and multiple-response activities.','Point–indication chart, explanation of source pairs, matching and recall.'],
    ['Associate Group Luo points with the four channel groups represented in the Chapter.','Hand Yang: SJ8; Hand Yin: PC5; Foot Yang: GB39; Foot Yin: SP6, as recorded in the source.','Four-group comparison chart with channel names, guided explanation and group-to-point recall.'],
    ['Identify Back-Shu point correspondences for the zangfu represented in the Chapter.','Back-Shu relationships: organ name, point name and point number; compare the source pairs.','Organ–point chart, guided recall, matching and review of the associated exams.'],
    ['Identify Front-Mu point correspondences for the zangfu in the source.','Organ–Front-Mu point pairs and source-listed relationships.','Organ–point reference chart, comparison, recall cards and matching.'],
    ['Associate Hui-Meeting points with the substances or categories identified by the source.','Hui-Meeting correspondences such as zang, fu, Qi and Blood in the matching source.','Category–point chart, comparison of associations and matching.'],
    ['Associate the confluent points with their Extraordinary Vessel correspondences.','Extraordinary Vessel–confluent point pairs in the two associated activities.','Vessel–point chart, comparison between related pairs and matching.'],
    ['Recognize the Heavenly Star point group of Ma Dan-yang as represented in the source.','Point-group recognition and the correct statements supplied by the multiple-response source.','Group identification, statement comparison and review of the original multiple-response material.'],
    ['Associate Command points with their source-listed body regions and purposes.','Command point correspondences, including the Six Command Points material.','Region–point comparison chart, brief explanations and matching practice.'],
    ['Distinguish the Four Seas and their source-listed point groups.','Sea of Qi, Blood, Water and Grain, and Marrow; corresponding point groups recorded in the source.','Four-part organization chart and sea-to-point-group matching.'],
    ['Recognize Window of Heaven point material and source-supported group characteristics.','Point-group recognition and source statements from the associated activities.','Group overview, statement comparison and review of the source answer key.'],
    ['Distinguish the Ghost, Window of Heaven and Heavenly Star groups using the source comparison.','The three named groups and the descriptions paired with them in the matching activity.','Three-group comparison chart and guided distinction between the source descriptions.'],
    ['Identify Meeting Points and the channels or vessels that meet at them.','Crossing-point relationships among Primary Channels and Extraordinary Vessels; distinguish these from Hui-Meeting points.','Crossing-point matrix, fill-in-the-blank practice and comparison of channel relationships.'],
    ['Distinguish entry points from exit points for the channels in the source.','Channel, entry or exit role, and corresponding point; keep the role explicit.','Side-by-side entry/exit chart and focused comparisons before the related exams.']
  ];
  const es=[
    ['Distinguir las categorías de los Cinco Shu y sus correspondencias y funciones indicadas en la fuente.','Clasificaciones Jing-Well, Ying-Spring, Shu-Stream, Jing-River y He-Sea; correspondencias con los canales y los Cinco Elementos, y funciones presentes en la fuente.','Tabla ordenada de categorías, comparación de las descripciones de la fuente, identificación de puntos y repaso de funciones.'],
    ['Identificar los puntos Jing-Well y distinguir las características de esta categoría respaldadas por la fuente.','Números de los puntos Jing-Well, relaciones para asociar y afirmaciones sobre la categoría.','Tabla de referencia, comparación dentro de los Cinco Shu e interpretación cuidadosa de preguntas formuladas en negativo.'],
    ['Identificar las correspondencias de los puntos Ying-Spring y distinguir las afirmaciones respaldadas por la fuente.','Números de los puntos Ying-Spring, relaciones para asociar y material de verdadero o falso; conservación de la terminología de la fuente.','Tabla de referencia, comparación de afirmaciones y revisión de las respuestas de preguntas formuladas en negativo.'],
    ['Identificar las correspondencias de los puntos Shu-Stream y distinguir las afirmaciones respaldadas sobre la categoría.','Números de los puntos Shu-Stream y afirmaciones sobre la categoría; las preguntas negativas requieren una interpretación cuidadosa.','Tabla de referencia, comparación de clasificaciones y comprobación explícita de si se solicita una afirmación verdadera o incorrecta.'],
    ['Identificar las correspondencias de los puntos Jing-River y las relaciones indicadas por la fuente.','Números de los puntos Jing-River de cada canal y material complementario para relacionar.','Tabla canal–punto, comparación dentro del sistema de los Cinco Shu y práctica de recuperación dirigida.'],
    ['Distinguir los puntos He-Sea por sus indicaciones individuales.','Relaciones entre puntos He-Sea e indicaciones; comparación con Lower He-Sea sin mezclar ambas categorías.','Comparación punto–indicación, lectura explicada de las relaciones de la fuente y ejercicios para asociar.'],
    ['Identificar los puntos Lower He-Sea y sus correspondencias de canal en la fuente.','Asociaciones entre Lower He-Sea y canales; distinción respecto del objetivo separado sobre He-Sea.','Tabla comparativa de categorías, asociación punto–canal y repaso dirigido.'],
    ['Relacionar las combinaciones Ying-Spring y Shu-Stream de la fuente con sus indicaciones.','Pares combinación–indicación incluidos en la actividad de asociación.','Tabla de pares de puntos, explicación de cada correspondencia y práctica para relacionar.'],
    ['Identificar los puntos Madre de los canales representados en la fuente.','Correspondencias entre cada canal y el número de su punto Madre.','Tabla de referencia, práctica de recuerdo y comparación con el objetivo separado sobre puntos Hijo.'],
    ['Identificar los puntos Hijo de los canales representados en la fuente.','Correspondencias entre cada canal y el número de su punto Hijo en las actividades de completar espacios.','Tabla canal–punto, práctica de cubrir y recordar y tarjetas basadas en la fuente.'],
    ['Identificar los puntos Xi-Cleft y distinguir sus relaciones punto–indicación según la fuente.','Identificación de Xi-Cleft, afirmaciones de respuesta múltiple y pares punto–indicación.','Tabla comparativa y ejercicios para asociar; sin añadir indicaciones ausentes de la fuente.'],
    ['Relacionar los Xi-Cleft de los Vasos Extraordinarios con sus vasos correspondientes.','Pares de los vasos de Motilidad y Enlace Yin y Yang con sus puntos Xi-Cleft.','Tabla de cuatro pares, comparación con los puntos confluentes y ejercicios para asociar.'],
    ['Identificar los puntos Yuan-Source de los canales representados en la fuente.','Números de los puntos Yuan-Source de cada canal en las actividades de completar espacios.','Tabla canal–punto, práctica de recuperación y tarjetas basadas en la fuente.'],
    ['Distinguir los puntos Luo-Connecting y sus indicaciones según la fuente.','Reconocimiento de Luo-Connecting y pares punto–indicación de actividades de asociación y respuesta múltiple.','Tabla punto–indicación, explicación de los pares, asociación y recuerdo.'],
    ['Relacionar los puntos Luo de Grupo con los cuatro grupos de canales del capítulo.','Yang de la mano: SJ8; Yin de la mano: PC5; Yang del pie: GB39; Yin del pie: SP6.','Tabla comparativa de cuatro grupos con nombres de canales, explicación guiada y recuerdo grupo–punto.'],
    ['Identificar las correspondencias Back-Shu de los zangfu representados en el capítulo.','Relaciones Back-Shu: órgano, nombre y número del punto; comparación de los pares de la fuente.','Tabla órgano–punto, recuerdo guiado, asociación y repaso de los exámenes relacionados.'],
    ['Identificar las correspondencias Front-Mu de los zangfu en la fuente.','Pares órgano–Front-Mu y relaciones indicadas por la fuente.','Tabla órgano–punto, comparación, tarjetas de recuerdo y asociación.'],
    ['Relacionar los Hui-Meeting con las sustancias o categorías identificadas por la fuente.','Correspondencias Hui-Meeting como zang, fu, Qi y Xue/Blood.','Tabla categoría–punto, comparación de asociaciones y ejercicios para relacionar.'],
    ['Relacionar los puntos confluentes con sus Vasos Extraordinarios.','Pares Vaso Extraordinario–punto confluente de las actividades relacionadas.','Tabla vaso–punto, comparación entre pares relacionados y asociación.'],
    ['Reconocer el grupo Estrellas Celestiales de Ma Dan-yang representado en la fuente.','Reconocimiento del grupo y de las afirmaciones correctas presentadas en la actividad de respuesta múltiple.','Identificación del grupo, comparación de afirmaciones y revisión del material original.'],
    ['Relacionar los puntos Comando con las regiones corporales y funciones indicadas.','Correspondencias de puntos Comando, incluido el material sobre los Seis Puntos Comando.','Tabla región–punto, explicaciones breves y práctica para relacionar.'],
    ['Distinguir los Cuatro Mares y sus grupos de puntos según la fuente.','Mar del Qi, de Xue/Blood, del Agua y los Granos y de la Médula; grupos correspondientes.','Esquema dividido en cuatro partes y asociación entre cada mar y su grupo de puntos.'],
    ['Reconocer los puntos Ventanas del Cielo y las características del grupo respaldadas por la fuente.','Reconocimiento del grupo y afirmaciones procedentes de las actividades relacionadas.','Resumen del grupo, comparación de afirmaciones y revisión de las respuestas de la fuente.'],
    ['Distinguir Puntos Fantasma, Ventanas del Cielo y Estrellas Celestiales mediante la comparación de la fuente.','Los tres grupos y las descripciones asociadas con ellos en la actividad.','Tabla comparativa de tres grupos y distinción guiada de sus descripciones.'],
    ['Identificar los Puntos de Cruce y los canales o vasos que se reúnen en ellos.','Relaciones de cruce entre Canales Principales y Vasos Extraordinarios; distinción respecto de Hui-Meeting.','Matriz de cruces, actividad de completar espacios y comparación de relaciones entre canales.'],
    ['Distinguir los puntos de entrada y salida de los canales según la fuente.','Canal, función de entrada o salida y punto correspondiente; la función debe permanecer explícita.','Tabla paralela de entrada y salida y comparaciones dirigidas antes de los exámenes relacionados.']
  ];
  function fix(){
    rows.forEach((row,i)=>{
      const values=row.querySelectorAll('.pc-plan-value');if(values.length<4)return;
      values[0].href='#pc-objective-'+sourceIds[i];
      values[0].textContent=(i+1)+'. '+en[i][0];values[0].dataset.es=(i+1)+'. '+es[i][0];
      values[1].textContent=en[i][1];values[1].dataset.es=es[i][1];
      values[2].textContent=en[i][2];values[2].dataset.es=es[i][2];
      values[3].textContent='To be estimated';values[3].dataset.es='Por determinar';
    });
  }
  function applyLanguage(){
    const spanish=[...document.querySelectorAll('.preview-view-switch button')].find(b=>b.textContent.trim()==='Estudiante');
    const useSpanish=spanish?.getAttribute('aria-pressed')==='true';
    rows.forEach((row,i)=>row.querySelectorAll('.pc-plan-value').forEach((value,j)=>{
      if(useSpanish)value.textContent=value.dataset.es;
      else value.textContent=j===0?(i+1)+'. '+en[i][0]:j<3?en[i][j]:'To be estimated';
    }));
  }
  fix();
  new MutationObserver(()=>queueMicrotask(fix)).observe(document.body,{attributes:true,attributeFilter:['class']});
  document.querySelectorAll('.preview-view-switch button').forEach(button=>button.addEventListener('click',()=>setTimeout(applyLanguage,80)));
})();
