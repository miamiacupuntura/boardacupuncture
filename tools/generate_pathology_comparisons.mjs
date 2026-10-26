import { writeFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '../study-guide/comparisons');
const cssVersion = '20261026-2';

const pathologies = [
  {
    slug: 'headache-location-herbs', title: { en: 'Four Herbs for Headache Location', es: 'Cuatro Hierbas según la Localización de la Cefalea' },
    summary: { en: 'Match vertex, occipital, frontal and lateral or Blood-stasis headaches to their guiding herbs.', es: 'Relaciona cefalea de vértex, occipital, frontal y lateral o por estasis de Sangre con sus hierbas guía.' },
    focus: { en: 'Headache location and channel guidance', es: 'Localización de cefalea y guía por canales' },
    search: 'headache cefalea vertex occipital frontal lateral blood stasis gao ben qiang huo bai zhi chuan xiong',
    en: {
      intro: 'Location supplies the fastest exam clue when selecting a guiding herb for headache.',
      rows: [
        ['Vertex', 'Pain at the top of the head', 'Gao Ben', 'Guides to the vertex.'],
        ['Occipital', 'Pain at the back of the head or upper neck', 'Qiang Huo', 'Guides to the Taiyang/Governing Vessel region.'],
        ['Frontal', 'Forehead, brow or sinus-region pain', 'Bai Zhi', 'Guides to the Yangming frontal region.'],
        ['Lateral or Blood stasis', 'Temporal pain or fixed/stabbing headache', 'Chuan Xiong', 'Moves Blood and reaches the head.'],
      ],
      rule: 'Choose by location first; fixed or stabbing pain adds the Blood-stasis clue.',
      memory: '<strong>Gao Ben—top; Qiang Huo—back; Bai Zhi—front; Chuan Xiong—side or stasis.</strong>',
      safety: 'A sudden thunderclap headache, neurologic deficit, fever with neck stiffness or headache after major trauma requires emergency evaluation.',
      source: 'Source Exam Key CMID 8966, question 2; Bensky et al., Chinese Herbal Medicine: Materia Medica, 3rd ed., p. 23.'
    },
    es: {
      intro: 'La localización ofrece la pista de examen más rápida para seleccionar una hierba guía de cefalea.',
      rows: [
        ['Vértex', 'Dolor en la parte superior de la cabeza', 'Gao Ben', 'Guía hacia el vértex.'],
        ['Occipital', 'Dolor en región posterior o cuello superior', 'Qiang Huo', 'Guía hacia región Taiyang/Vaso Gobernador.'],
        ['Frontal', 'Dolor de frente, cejas o región sinusal', 'Bai Zhi', 'Guía hacia región frontal Yangming.'],
        ['Lateral o estasis de Sangre', 'Dolor temporal o cefalea fija/punzante', 'Chuan Xiong', 'Mueve Sangre y alcanza la cabeza.'],
      ],
      rule: 'Elige primero por localización; dolor fijo o punzante añade la pista de estasis de Sangre.',
      memory: '<strong>Gao Ben—arriba; Qiang Huo—atrás; Bai Zhi—frente; Chuan Xiong—lado o estasis.</strong>',
      safety: 'Cefalea súbita explosiva, déficit neurológico, fiebre con rigidez cervical o cefalea tras trauma importante requiere urgencias.',
      source: 'Exam Key CMID 8966, pregunta 2; Bensky et al., Chinese Herbal Medicine: Materia Medica, 3.ª ed., p. 23.'
    }
  },
  {
    slug: 'common-cold-patterns', title: { en: 'Common Cold Pattern Differentiation', es: 'Diferenciación de Patterns del Resfriado Común' },
    summary: { en: 'Separate Wind-Cold, Wind-Heat, Summerheat-Damp and deficiency presentations.', es: 'Separa Viento-Frío, Viento-Calor, Calor de Verano-Humedad y presentaciones por deficiencia.' },
    focus: { en: 'Exterior pathogen, temperature and constitution', es: 'Patógeno Exterior, temperatura y constitución' },
    search: 'common cold resfriado wind cold heat summerheat damp qi deficiency yin jing fang bai du san yin qiao san xiang ru shen su yin wei rui',
    en: {
      intro: 'The decisive sequence is temperature, sweating, secretions and the patient’s underlying constitution.',
      rows: [
        ['Wind-Cold invasion', 'Strong chills, mild fever, no sweat, body aches, thin white discharge', 'Jing Fang Bai Du San', 'Dispel Wind, scatter Cold and release the Exterior.'],
        ['Wind-Heat invasion', 'More fever, mild chills, sweating, sore throat, thirst, yellow discharge', 'Yin Qiao San', 'Dispel Wind, clear Heat and release the Exterior.'],
        ['Summerheat-Damp invasion', 'Heavy body, nausea, chest oppression, loose stool, scanty dark urine', 'Xin Jia Xiang Ru Yin', 'Clear Summerheat and transform Dampness.'],
        ['Wind-Cold with Qi deficiency', 'Chills and fever with fatigue, short breath and weak voice', 'Shen Su Yin', 'Release the Exterior while supporting Qi.'],
        ['Exterior disorder with Yin deficiency', 'Fever/chills with dry cough, thirst, dry mouth and five-center Heat', 'Jia Jian Wei Rui Tang', 'Release the Exterior while nourishing Yin.'],
      ],
      rule: 'Cold has stronger chills and white discharge; Heat has sore throat and yellow discharge; heaviness/GI signs point to Summerheat-Damp; constitutional weakness identifies the deficiency branches.',
      memory: '<strong>Cold—closed; Heat—throat; Summer—heavy; Qi deficiency—tired; Yin deficiency—dry.</strong>',
      safety: 'Breathing difficulty, confusion, dehydration, persistent high fever or rapid deterioration requires medical evaluation.',
      source: 'Internal Medicine 1, Handout 1: Common Cold.'
    },
    es: {
      intro: 'La secuencia decisiva es temperatura, sudor, secreciones y constitución subyacente.',
      rows: [
        ['Invasión Viento-Frío', 'Escalofríos fuertes, fiebre leve, sin sudor, dolores y secreción blanca fina', 'Jing Fang Bai Du San', 'Dispersar Viento-Frío y liberar Exterior.'],
        ['Invasión Viento-Calor', 'Más fiebre, escalofrío leve, sudor, garganta dolorida, sed y secreción amarilla', 'Yin Qiao San', 'Dispersar Viento, aclarar Calor y liberar Exterior.'],
        ['Calor de Verano-Humedad', 'Cuerpo pesado, náusea, opresión torácica, heces sueltas y orina oscura escasa', 'Xin Jia Xiang Ru Yin', 'Aclarar Calor de Verano y transformar Humedad.'],
        ['Viento-Frío con deficiencia Qi', 'Escalofríos/fiebre con fatiga, disnea y voz débil', 'Shen Su Yin', 'Liberar Exterior apoyando Qi.'],
        ['Exterior con deficiencia Yin', 'Fiebre/escalofríos con tos seca, sed, boca seca y Calor de cinco centros', 'Jia Jian Wei Rui Tang', 'Liberar Exterior nutriendo Yin.'],
      ],
      rule: 'Frío presenta escalofríos y secreción blanca; Calor presenta garganta y secreción amarilla; pesadez/signos GI indican Verano-Humedad; debilidad constitucional identifica ramas deficientes.',
      memory: '<strong>Frío—cerrado; Calor—garganta; Verano—pesado; Qi—cansado; Yin—seco.</strong>',
      safety: 'Dificultad respiratoria, confusión, deshidratación, fiebre alta persistente o deterioro rápido requiere evaluación médica.',
      source: 'Internal Medicine 1, Handout 1: Common Cold.'
    }
  },
  {
    slug: 'cough-patterns', title: { en: 'Cough Pattern Differentiation', es: 'Diferenciación de Patterns de Tos' },
    summary: { en: 'Compare exterior Cold, exterior Heat, Dryness, Damp-Phlegm, Liver Fire and Lung Yin deficiency.', es: 'Compara Frío Exterior, Calor Exterior, Sequedad, Humedad-Flema, Fuego de Hígado y deficiencia de Yin de Pulmón.' },
    focus: { en: 'Sputum, dryness, exterior signs and chronicity', es: 'Esputo, sequedad, signos Exteriores y cronicidad' },
    search: 'cough tos wind cold heat dryness damp phlegm liver fire lung yin san ao zhi sou sang ju sang xing er chen xie bai sha shen',
    en: {
      intro: 'Sputum color and quantity, dryness and exterior signs distinguish the main cough patterns.',
      rows: [
        ['Wind-Cold', 'Scratchy throat, thin white sputum, chills, stuffy or runny nose', 'San Ao Tang or Zhi Sou San', 'Diffuse Lung Qi and dispel Wind-Cold.'],
        ['Wind-Heat', 'Heavy breathing, sticky yellow sputum, sore throat, thirst and sweating', 'Sang Ju Yin', 'Dispel Wind-Heat and diffuse the Lung.'],
        ['Wind-Dryness', 'Dry cough, dry nose/throat, scant sputum or blood streaks', 'Sang Xing Tang; Xing Su San if Cold-Dryness', 'Course Wind and moisten the Lung.'],
        ['Damp-Phlegm', 'Morning cough, copious dilute white sputum, nausea and epigastric fullness', 'Er Chen Tang + San Zi Yang Qin Tang', 'Strengthen Spleen and transform Damp-Phlegm.'],
        ['Liver Fire invading Lung', 'Hacking cough linked to emotion, hypochondriac distention, bitter taste', 'Xie Bai San + Dai Ge San', 'Clear Liver Fire and drain the Lung.'],
        ['Lung Yin deficiency', 'Chronic dry cough, hoarseness, night sweats, tidal fever and scant blood-tinged sputum', 'Sha Shen Mai Dong Tang', 'Nourish Yin and moisten Lung.'],
      ],
      rule: 'White/thin suggests Cold, yellow/sticky suggests Heat, copious white suggests Damp-Phlegm, and scant dry sputum shifts attention to Dryness or Yin deficiency.',
      memory: '<strong>Cold—white; Heat—yellow; Dryness—parched; Damp—copious; Liver—emotion; Yin—chronic night Heat.</strong>',
      safety: 'Coughing blood, shortness of breath, chest pain, low oxygen, unexplained weight loss or cough lasting more than three weeks requires medical evaluation.',
      source: 'Internal Medicine 1, Handout 1: Cough.'
    },
    es: {
      intro: 'Color y cantidad del esputo, sequedad y signos Exteriores distinguen los principales patterns de tos.',
      rows: [
        ['Viento-Frío', 'Garganta áspera, esputo blanco fino, escalofríos y nariz tapada o con secreción', 'San Ao Tang o Zhi Sou San', 'Difundir Qi pulmonar y dispersar Viento-Frío.'],
        ['Viento-Calor', 'Respiración pesada, esputo amarillo pegajoso, dolor de garganta, sed y sudor', 'Sang Ju Yin', 'Dispersar Viento-Calor y difundir Pulmón.'],
        ['Viento-Sequedad', 'Tos seca, nariz/garganta secas, poco esputo o vetas de sangre', 'Sang Xing Tang; Xing Su San si Sequedad-Frío', 'Dispersar Viento y humedecer Pulmón.'],
        ['Humedad-Flema', 'Tos matinal, esputo blanco diluido abundante, náusea y plenitud epigástrica', 'Er Chen Tang + San Zi Yang Qin Tang', 'Fortalecer Bazo y transformar Humedad-Flema.'],
        ['Fuego de Hígado invade Pulmón', 'Tos seca ligada a emoción, distensión hipocondríaca y sabor amargo', 'Xie Bai San + Dai Ge San', 'Aclarar Fuego de Hígado y drenar Pulmón.'],
        ['Deficiencia Yin de Pulmón', 'Tos seca crónica, ronquera, sudor nocturno, fiebre vespertina y esputo escaso con sangre', 'Sha Shen Mai Dong Tang', 'Nutrir Yin y humedecer Pulmón.'],
      ],
      rule: 'Blanco/fino sugiere Frío; amarillo/pegajoso, Calor; blanco abundante, Humedad-Flema; esputo escaso y seco dirige a Sequedad o deficiencia Yin.',
      memory: '<strong>Frío—blanco; Calor—amarillo; Sequedad—reseco; Humedad—abundante; Hígado—emoción; Yin—Calor nocturno crónico.</strong>',
      safety: 'Toser sangre, disnea, dolor torácico, oxígeno bajo, pérdida de peso o tos de más de tres semanas requiere evaluación médica.',
      source: 'Internal Medicine 1, Handout 1: Cough.'
    }
  },
  {
    slug: 'asthma-patterns', title: { en: 'Asthma Pattern Differentiation', es: 'Diferenciación de Patterns de Asma' },
    summary: { en: 'Distinguish acute Wind-Cold or Phlegm-Heat excess from chronic Lung or Kidney deficiency.', es: 'Distingue exceso agudo por Viento-Frío o Flema-Calor de deficiencia crónica de Pulmón o Riñón.' },
    focus: { en: 'Acute excess versus chronic deficiency', es: 'Exceso agudo frente a deficiencia crónica' },
    search: 'asthma wheezing asma wind cold phlegm heat lung qi kidney yang ma huang sang bai pi sheng mai bu fei jin gui shen qi',
    en: {
      intro: 'First decide whether the attack is acute excess or chronic deficiency; then use sputum and constitutional signs.',
      rows: [
        ['Wind-Cold constraining Lung', 'Acute wheeze, chest oppression, thin white or foamy sputum and floating-tight pulse', 'Ma Huang Tang', 'Release Wind-Cold and ventilate Lung.'],
        ['Phlegm-Heat in Lung', 'Loud wheeze, thick yellow sputum, fever, thirst and slippery-rapid pulse', 'Sang Bai Pi Tang', 'Clear Lung Heat and transform Phlegm.'],
        ['Lung Qi deficiency', 'Weak chronic wheeze, feeble voice, spontaneous sweating and sensitivity to drafts', 'Sheng Mai Yin + Bu Fei Tang', 'Supplement Lung Qi and stabilize breathing.'],
        ['Kidney deficiency failing to grasp Qi', 'Persistent exertional wheeze, shallow inhalation, weak back and cold limbs', 'Jin Gui Shen Qi Wan', 'Warm Kidney and assist grasping of Qi.'],
      ],
      rule: 'Acute exterior signs indicate Wind-Cold; yellow phlegm indicates Heat; weak voice/sweat indicates Lung deficiency; difficulty inhaling with lumbar weakness indicates Kidney deficiency.',
      memory: '<strong>Cold—acute white; Heat—acute yellow; Lung—weak exhale; Kidney—cannot inhale deeply.</strong>',
      safety: 'Severe breathlessness, inability to speak full sentences, blue lips, altered consciousness or poor response to rescue medication is an emergency. Do not replace prescribed inhalers.',
      source: 'Internal Medicine 1, Handout 1: Asthma.'
    },
    es: {
      intro: 'Primero decide si el ataque es exceso agudo o deficiencia crónica; después usa esputo y signos constitucionales.',
      rows: [
        ['Viento-Frío constriñe Pulmón', 'Sibilancia aguda, opresión, esputo blanco/espumoso y pulso flotante-tenso', 'Ma Huang Tang', 'Liberar Viento-Frío y ventilar Pulmón.'],
        ['Flema-Calor en Pulmón', 'Sibilancia sonora, esputo amarillo espeso, fiebre, sed y pulso resbaladizo-rápido', 'Sang Bai Pi Tang', 'Aclarar Calor pulmonar y transformar Flema.'],
        ['Deficiencia Qi de Pulmón', 'Sibilancia crónica débil, voz tenue, sudor espontáneo y sensibilidad a corrientes', 'Sheng Mai Yin + Bu Fei Tang', 'Suplementar Qi pulmonar y estabilizar respiración.'],
        ['Riñón no agarra Qi', 'Sibilancia persistente al esfuerzo, inhalación superficial, lumbar débil y extremidades frías', 'Jin Gui Shen Qi Wan', 'Calentar Riñón y ayudar a agarrar Qi.'],
      ],
      rule: 'Signos Exteriores agudos indican Viento-Frío; Flema amarilla indica Calor; voz débil/sudor indica Pulmón; dificultad al inhalar con lumbar débil indica Riñón.',
      memory: '<strong>Frío—agudo blanco; Calor—agudo amarillo; Pulmón—exhalación débil; Riñón—no inspira profundo.</strong>',
      safety: 'Disnea grave, incapacidad para hablar, labios azules, conciencia alterada o mala respuesta al inhalador de rescate es emergencia. No sustituir inhaladores prescritos.',
      source: 'Internal Medicine 1, Handout 1: Asthma.'
    }
  },
  {
    slug: 'abdominal-pain-patterns', title: { en: 'Abdominal Pain Pattern Differentiation', es: 'Diferenciación de Patterns de Dolor Abdominal' },
    summary: { en: 'Compare Cold, Damp-Heat, Spleen-Yang deficiency, Food Stagnation and Qi/Blood stagnation.', es: 'Compara Frío, Humedad-Calor, deficiencia Yang de Bazo, estancamiento alimentario y estancamiento de Qi/Sangre.' },
    focus: { en: 'Pressure, temperature, location and bowel response', es: 'Presión, temperatura, localización y respuesta intestinal' },
    search: 'abdominal pain dolor cold damp heat spleen yang food stagnation qi blood stasis liang fu da cheng qi xiao jian zhong bao he chai hu shu gan shao fu zhu yu',
    en: {
      intro: 'Ask whether warmth or pressure helps, whether pain moves or stays fixed, and whether defecation relieves it.',
      rows: [
        ['Cold obstruction', 'Sudden pain worse with cold and better with warmth; no thirst', 'Liang Fu Wan', 'Warm the Middle, scatter Cold and move Qi.'],
        ['Damp-Heat obstruction', 'Pain worse with pressure, distention, thirst, dark urine and yellow greasy coat', 'Da Cheng Qi Tang', 'Drain stool and clear Damp-Heat when excess is confirmed.'],
        ['Spleen-Yang deficiency', 'Dull periodic pain better with warmth/pressure; loose stools and fatigue', 'Xiao Jian Zhong Tang', 'Warm and supplement the Middle.'],
        ['Food Stagnation', 'Pain/distention after food, foul belching, acid regurgitation and relief after stool', 'Bao He Wan; Zhi Shi Dao Zhi Wan if severe', 'Reduce food and guide out stagnation.'],
        ['Liver-Qi stagnation', 'Moving distending pain triggered by anger and relieved by gas or belching', 'Chai Hu Shu Gan San', 'Soothe Liver and regulate Qi.'],
        ['Blood stasis', 'Severe fixed pain with a purple tongue', 'Shao Fu Zhu Yu Tang when lower-abdominal Cold stasis fits', 'Invigorate Blood and dispel stasis.'],
      ],
      rule: 'Better with warmth suggests Cold; better with pressure suggests deficiency; worse with pressure suggests excess; moving pain is Qi, fixed stabbing pain is Blood.',
      memory: '<strong>Warmth—Cold; pressure helps—deficiency; stool helps—food; moves—Qi; fixed—Blood.</strong>',
      safety: 'Rigid abdomen, rebound tenderness, persistent vomiting, gastrointestinal bleeding, fainting, pregnancy with severe pain, or no stool/gas requires urgent evaluation.',
      source: 'Internal Medicine 1, Handout 3: Abdominal Pain.'
    },
    es: {
      intro: 'Pregunta si calor o presión alivian, si el dolor se mueve o queda fijo y si defecar lo mejora.',
      rows: [
        ['Obstrucción por Frío', 'Dolor súbito peor con frío y mejor con calor; sin sed', 'Liang Fu Wan', 'Calentar Centro, dispersar Frío y mover Qi.'],
        ['Obstrucción Humedad-Calor', 'Dolor peor con presión, distensión, sed, orina oscura y capa amarilla grasosa', 'Da Cheng Qi Tang', 'Drenar heces y aclarar Humedad-Calor cuando se confirma exceso.'],
        ['Deficiencia Yang de Bazo', 'Dolor sordo periódico mejor con calor/presión; heces sueltas y fatiga', 'Xiao Jian Zhong Tang', 'Calentar y suplementar Centro.'],
        ['Estancamiento alimentario', 'Dolor/distensión tras comida, eructo fétido, reflujo y alivio después de defecar', 'Bao He Wan; Zhi Shi Dao Zhi Wan si grave', 'Reducir alimento y conducir estancamiento.'],
        ['Estancamiento Qi de Hígado', 'Dolor distensivo móvil por enojo, aliviado con gases o eructos', 'Chai Hu Shu Gan San', 'Suavizar Hígado y regular Qi.'],
        ['Estasis de Sangre', 'Dolor intenso fijo con lengua púrpura', 'Shao Fu Zhu Yu Tang si corresponde estasis fría inferior', 'Vigorizar Sangre y eliminar estasis.'],
      ],
      rule: 'Mejor con calor sugiere Frío; mejor con presión, deficiencia; peor con presión, exceso; dolor móvil es Qi y fijo/punzante es Sangre.',
      memory: '<strong>Calor—Frío; presión alivia—deficiencia; heces alivian—comida; móvil—Qi; fijo—Sangre.</strong>',
      safety: 'Abdomen rígido, rebote, vómito persistente, sangrado GI, desmayo, embarazo con dolor grave o ausencia de heces/gases requiere urgencias.',
      source: 'Internal Medicine 1, Handout 3: Abdominal Pain.'
    }
  },
  {
    slug: 'diarrhea-patterns', title: { en: 'Diarrhea Pattern Differentiation', es: 'Diferenciación de Patterns de Diarrea' },
    summary: { en: 'Separate Cold-Damp, Damp-Heat, Spleen deficiency, Food Stagnation, Liver-Spleen disharmony and Kidney-Yang deficiency.', es: 'Separa Frío-Humedad, Humedad-Calor, deficiencia de Bazo, alimento estancado, desarmonía Hígado-Bazo y deficiencia Yang renal.' },
    focus: { en: 'Stool quality, timing, burning and triggers', es: 'Calidad y horario de heces, ardor y desencadenantes' },
    search: 'diarrhea diarrea cold damp heat spleen food liver kidney yang huo xiang ge gen qin lian shen ling bai zhu bao he tong xie si shen wan',
    en: {
      intro: 'Stool character, burning, relationship to food or emotion, and timing distinguish the branches.',
      rows: [
        ['Exterior Cold-Damp', 'Watery stool, borborygmus, abdominal pain, white greasy coat', 'Huo Xiang Zheng Qi San', 'Dispel Cold and transform Dampness.'],
        ['Damp-Heat', 'Urgent difficult diarrhea, anal burning, thirst and scant dark urine', 'Ge Gen Huang Qin Huang Lian Tang', 'Clear Heat and eliminate Dampness.'],
        ['Spleen-Stomach deficiency', 'Chronic loose stool worse with greasy food, poor appetite and fatigue', 'Shen Ling Bai Zhu San', 'Strengthen Spleen and drain Dampness.'],
        ['Food Stagnation', 'Foul stool and belching, acid regurgitation, pain relieved after stool', 'Bao He Wan', 'Reduce food and guide out stagnation.'],
        ['Liver-Spleen disharmony', 'Emotion-triggered pain/diarrhea, hypochondriac distention, no relief after stool', 'Tong Xie Yao Fang', 'Soothe Liver and strengthen Spleen.'],
        ['Kidney-Yang deficiency', 'Predawn diarrhea with cold, weak low back and knees', 'Si Shen Wan', 'Warm Kidney Yang and bind intestines.'],
      ],
      rule: 'Burning indicates Heat; watery with chills indicates Cold-Damp; greasy-food sensitivity indicates Spleen; emotions indicate Liver; predawn timing indicates Kidney Yang.',
      memory: '<strong>Cold—watery; Heat—burning; Spleen—food weakness; Liver—emotion; Kidney—dawn.</strong>',
      safety: 'Blood or black stool, high fever, severe dehydration, intense pain, recent antibiotics, infant/elderly frailty or persistent diarrhea requires medical evaluation.',
      source: 'Internal Medicine 1, Handout 3: Diarrhea.'
    },
    es: {
      intro: 'Carácter de heces, ardor, relación con alimento o emoción y horario distinguen las ramas.',
      rows: [
        ['Frío-Humedad Exterior', 'Heces acuosas, borborigmos, dolor abdominal y capa blanca grasosa', 'Huo Xiang Zheng Qi San', 'Dispersar Frío y transformar Humedad.'],
        ['Humedad-Calor', 'Diarrea urgente y difícil, ardor anal, sed y orina oscura escasa', 'Ge Gen Huang Qin Huang Lian Tang', 'Aclarar Calor y eliminar Humedad.'],
        ['Deficiencia Bazo-Estómago', 'Heces sueltas crónicas peor con grasa, poco apetito y fatiga', 'Shen Ling Bai Zhu San', 'Fortalecer Bazo y drenar Humedad.'],
        ['Estancamiento alimentario', 'Heces/eructos fétidos, reflujo ácido y dolor aliviado tras defecar', 'Bao He Wan', 'Reducir alimento y conducir estancamiento.'],
        ['Desarmonía Hígado-Bazo', 'Dolor/diarrea por emoción, distensión hipocondríaca y sin alivio tras heces', 'Tong Xie Yao Fang', 'Suavizar Hígado y fortalecer Bazo.'],
        ['Deficiencia Yang renal', 'Diarrea antes del amanecer con frío y lumbar/rodillas débiles', 'Si Shen Wan', 'Calentar Yang renal y contener intestinos.'],
      ],
      rule: 'Ardor indica Calor; acuosa con escalofríos indica Frío-Humedad; grasa señala Bazo; emoción señala Hígado; horario al amanecer señala Yang renal.',
      memory: '<strong>Frío—acuosa; Calor—ardor; Bazo—debilidad alimentaria; Hígado—emoción; Riñón—amanecer.</strong>',
      safety: 'Sangre o heces negras, fiebre alta, deshidratación grave, dolor intenso, antibióticos recientes, fragilidad o diarrea persistente requiere evaluación médica.',
      source: 'Internal Medicine 1, Handout 3: Diarrhea.'
    }
  },
  {
    slug: 'insomnia-patterns', title: { en: 'Insomnia Pattern Differentiation', es: 'Diferenciación de Patterns de Insomnio' },
    summary: { en: 'Compare Liver Fire, Phlegm-Heat, Yin deficiency, Heart-Spleen deficiency and Heart-Gallbladder Qi deficiency.', es: 'Compara Fuego de Hígado, Flema-Calor, deficiencia Yin, deficiencia Corazón-Bazo y deficiencia Qi Corazón-Vesícula.' },
    focus: { en: 'Heat, Phlegm, deficiency and emotional clues', es: 'Pistas de Calor, Flema, deficiencia y emoción' },
    search: 'insomnia insomnio liver fire phlegm heat yin heart spleen gallbladder long dan wen dan huang lian e jiao gui pi an shen ding zhi',
    en: {
      intro: 'The accompanying mental, digestive, Heat and deficiency signs are more diagnostic than sleeplessness alone.',
      rows: [
        ['Liver constraint transforming Fire', 'Irritability, headache, red eyes, bitter taste, hypochondriac pain', 'Long Dan Xie Gan Tang', 'Drain Liver Fire and calm Spirit.'],
        ['Phlegm-Heat disturbing Spirit', 'Heavy head, nausea, epigastric fullness, bitter taste and yellow greasy coat', 'Wen Dan Tang', 'Clear Heat, transform Phlegm and harmonize Stomach.'],
        ['Yin deficiency with empty Fire', 'Palpitations, tinnitus, weak back, five-center Heat and red peeled tongue', 'Huang Lian E Jiao Tang', 'Nourish Kidney Yin and descend Heart Fire.'],
        ['Heart-Spleen deficiency', 'Light sleep, many dreams, poor memory, fatigue, poor appetite and pale complexion', 'Gui Pi Tang', 'Supplement Qi and Blood of Heart and Spleen.'],
        ['Heart-Gallbladder Qi deficiency', 'Timidity, easy fright, palpitations, short breath and copious clear urine', 'An Shen Ding Zhi Wan', 'Supplement Qi, settle fright and calm Spirit.'],
      ],
      rule: 'Anger and bitter taste indicate Liver Fire; greasy coating and nausea indicate Phlegm; five-center Heat indicates Yin deficiency; fatigue/appetite indicate Heart-Spleen; fright indicates Heart-Gallbladder.',
      memory: '<strong>Angry—Liver; greasy—Phlegm; hot/dry—Yin; tired/pale—Heart-Spleen; timid—Heart-Gallbladder.</strong>',
      safety: 'Insomnia with suicidal thoughts, mania, psychosis, dangerous sleepiness, breathing pauses or abrupt neurologic change requires urgent medical care.',
      source: 'Internal Medicine 1, Handout 5: Insomnia.'
    },
    es: {
      intro: 'Los signos mentales, digestivos, de Calor y deficiencia acompañantes son más diagnósticos que el insomnio aislado.',
      rows: [
        ['Estancamiento de Hígado se transforma en Fuego', 'Irritabilidad, cefalea, ojos rojos, sabor amargo y dolor hipocondríaco', 'Long Dan Xie Gan Tang', 'Drenar Fuego de Hígado y calmar Shen.'],
        ['Flema-Calor perturba Shen', 'Cabeza pesada, náusea, plenitud epigástrica, sabor amargo y capa amarilla grasosa', 'Wen Dan Tang', 'Aclarar Calor, transformar Flema y armonizar Estómago.'],
        ['Deficiencia Yin con Fuego vacío', 'Palpitaciones, tinnitus, lumbar débil, Calor de cinco centros y lengua roja pelada', 'Huang Lian E Jiao Tang', 'Nutrir Yin renal y descender Fuego de Corazón.'],
        ['Deficiencia Corazón-Bazo', 'Sueño ligero, muchos sueños, mala memoria, fatiga, poco apetito y palidez', 'Gui Pi Tang', 'Suplementar Qi y Sangre de Corazón-Bazo.'],
        ['Deficiencia Qi Corazón-Vesícula Biliar', 'Timidez, sobresalto fácil, palpitaciones, disnea y orina clara abundante', 'An Shen Ding Zhi Wan', 'Suplementar Qi, contener sobresalto y calmar Shen.'],
      ],
      rule: 'Ira/sabor amargo indica Fuego de Hígado; capa grasosa/náusea, Flema; Calor de cinco centros, Yin; fatiga/apetito, Corazón-Bazo; sobresalto, Corazón-Vesícula.',
      memory: '<strong>Ira—Hígado; grasoso—Flema; caliente/seco—Yin; cansado/pálido—Corazón-Bazo; tímido—Corazón-Vesícula.</strong>',
      safety: 'Insomnio con ideas suicidas, manía, psicosis, somnolencia peligrosa, pausas respiratorias o cambio neurológico abrupto requiere atención urgente.',
      source: 'Internal Medicine 1, Handout 5: Insomnia.'
    }
  },
  {
    slug: 'stomach-pain-patterns', title: { en: 'Stomach Pain Pattern Differentiation', es: 'Diferenciación de Patterns de Dolor de Estómago' },
    summary: { en: 'Separate Cold, Food Stagnation, Liver-Qi invasion, Heat, Blood stasis and deficiency presentations.', es: 'Separa Frío, estancamiento alimentario, invasión de Qi de Hígado, Calor, estasis de Sangre y presentaciones deficientes.' },
    focus: { en: 'Temperature, pressure, food, emotion and fixed pain', es: 'Temperatura, presión, alimento, emoción y dolor fijo' },
    search: 'stomach pain epigastric dolor estomago cold food liver qi heat blood stasis yin yang liang fu bao he chai hu zuo jin shi xiao yi guan huang qi jian zhong',
    en: {
      intro: 'Temperature, relationship to food or stress, and whether the pain is fixed provide the fastest distinctions.',
      rows: [
        ['Stomach Cold', 'Abrupt pain worse with Cold and relieved by warmth; no thirst', 'Liang Fu Wan', 'Scatter Cold and relieve pain.'],
        ['Food Stagnation', 'Pain worse with pressure, foul belching, reflux and relief after vomiting or gas', 'Bao He Wan', 'Reduce food and move stagnation.'],
        ['Liver Qi disrupting Stomach', 'Distending pain spreading to ribs, stress-related belching and reflux', 'Chai Hu Shu Gan San', 'Soothe Liver and harmonize Stomach.'],
        ['Liver-Stomach Heat', 'Burning acute pain, irritability, bitter dry taste and acid reflux', 'Zuo Jin Wan', 'Clear Heat and harmonize Stomach.'],
        ['Blood stasis', 'Fixed stabbing pain, purple tongue, hematemesis or black stool', 'Shi Xiao San + Dan Shen Yin', 'Invigorate Blood and dispel stasis.'],
        ['Stomach Yin deficiency', 'Dull pain, dry mouth, dry stool and red dry tongue', 'Yi Guan Jian or Shao Yao Gan Cao Tang', 'Nourish Yin and relieve pain.'],
        ['Spleen-Stomach deficiency Cold', 'Dull pain relieved by warmth/pressure with fatigue, cold limbs and loose stool', 'Huang Qi Jian Zhong Tang', 'Warm and supplement the Middle.'],
      ],
      rule: 'Warmth points to Cold; pressure worsens excess but relieves deficiency; stress points to Liver Qi; fixed stabbing pain points to Blood stasis; dryness points to Yin deficiency.',
      memory: '<strong>Cold—warm it; Food—foul; Liver—stress; Heat—burning; Blood—fixed; Yin—dry; Yang—weak and cold.</strong>',
      safety: 'Black stool, vomiting blood, severe persistent pain, weight loss, fainting, chest pressure or rigid abdomen requires urgent biomedical evaluation.',
      source: 'Internal Medicine 1, Handout 2: Stomach Pain.'
    },
    es: {
      intro: 'Temperatura, relación con comida o estrés y si el dolor es fijo ofrecen las distinciones más rápidas.',
      rows: [
        ['Frío de Estómago', 'Dolor súbito peor con frío y aliviado por calor; sin sed', 'Liang Fu Wan', 'Dispersar Frío y aliviar dolor.'],
        ['Estancamiento alimentario', 'Dolor peor con presión, eructo fétido, reflujo y alivio tras vómito o gases', 'Bao He Wan', 'Reducir alimento y mover estancamiento.'],
        ['Qi de Hígado perturba Estómago', 'Dolor distensivo hacia costillas, eructo/reflujo relacionados con estrés', 'Chai Hu Shu Gan San', 'Suavizar Hígado y armonizar Estómago.'],
        ['Calor Hígado-Estómago', 'Dolor ardiente agudo, irritabilidad, sabor amargo seco y reflujo ácido', 'Zuo Jin Wan', 'Aclarar Calor y armonizar Estómago.'],
        ['Estasis de Sangre', 'Dolor fijo punzante, lengua púrpura, hematemesis o heces negras', 'Shi Xiao San + Dan Shen Yin', 'Vigorizar Sangre y eliminar estasis.'],
        ['Deficiencia Yin de Estómago', 'Dolor sordo, boca/heces secas y lengua roja seca', 'Yi Guan Jian o Shao Yao Gan Cao Tang', 'Nutrir Yin y aliviar dolor.'],
        ['Frío por deficiencia Bazo-Estómago', 'Dolor sordo mejor con calor/presión, fatiga, extremidades frías y heces sueltas', 'Huang Qi Jian Zhong Tang', 'Calentar y suplementar Centro.'],
      ],
      rule: 'Calor alivia Frío; presión empeora exceso y alivia deficiencia; estrés señala Qi de Hígado; dolor fijo punzante, estasis; sequedad, deficiencia Yin.',
      memory: '<strong>Frío—calor; Comida—fétido; Hígado—estrés; Calor—ardor; Sangre—fijo; Yin—seco; Yang—débil y frío.</strong>',
      safety: 'Heces negras, vómito de sangre, dolor grave persistente, pérdida de peso, desmayo, presión torácica o abdomen rígido requiere urgencias.',
      source: 'Internal Medicine 1, Handout 2: Stomach Pain.'
    }
  },
  {
    slug: 'vomiting-patterns', title: { en: 'Vomiting Pattern Differentiation', es: 'Diferenciación de Patterns de Vómito' },
    summary: { en: 'Compare exterior invasion, Food Stagnation, Liver-Qi rebellion, Phlegm-Damp, Stomach Yin deficiency and deficiency Cold.', es: 'Compara invasión Exterior, alimento estancado, Qi rebelde de Hígado, Flema-Humedad, deficiencia Yin de Estómago y Frío deficiente.' },
    focus: { en: 'Contents, triggers, coating and constitutional signs', es: 'Contenido, desencadenantes, capa lingual y constitución' },
    search: 'vomiting vomito exterior food liver qi phlegm damp stomach yin spleen cold huo xiang bao he ban xia hou po xiao ban xia mai men dong li zhong',
    en: {
      intro: 'Onset, vomit contents, epigastric signs and the tongue coating distinguish acute excess from chronic deficiency.',
      rows: [
        ['Exterior pathogen invading Stomach', 'Sudden vomiting with chills, fever, body aches and chest oppression', 'Huo Xiang Zheng Qi San', 'Release Exterior and transform turbidity.'],
        ['Food Stagnation', 'Vomiting undigested food, foul belching, reflux and thick greasy coat', 'Bao He Wan', 'Reduce food and guide stagnation out.'],
        ['Liver Qi disrupting Stomach', 'Stress-related vomiting, belching, reflux and hypochondriac pain', 'Ban Xia Hou Po Tang or Zuo Jin Wan when Heat dominates', 'Soothe Liver and descend Stomach Qi.'],
        ['Phlegm-Damp obstruction', 'Clear fluid/mucus, dizziness, palpitations and white greasy coat', 'Xiao Ban Xia Tang + Ling Gui Zhu Gan Tang', 'Warm and transform Phlegm-fluid.'],
        ['Stomach Yin deficiency', 'Repeated retching, thirst, dry mouth, hunger without desire to eat', 'Mai Men Dong Tang', 'Nourish Stomach Yin and descend Qi.'],
        ['Spleen-Stomach deficiency Cold', 'Vomiting after eating with fatigue, cold limbs, loose stool and pale tongue', 'Li Zhong Tang', 'Warm Middle and strengthen Spleen.'],
      ],
      rule: 'Sudden fever/chills indicates Exterior; undigested food and foul odor indicates Food; emotion indicates Liver; clear mucus with greasy coat indicates Phlegm; dry retching indicates Yin deficiency.',
      memory: '<strong>Exterior—sudden; Food—foul; Liver—stress; Phlegm—clear mucus; Yin—dry retching; Cold—weak and pale.</strong>',
      safety: 'Blood or coffee-ground material, severe dehydration, persistent bilious vomiting, severe headache, rigid abdomen or inability to keep fluids down requires urgent assessment.',
      source: 'Internal Medicine 1, Handout 2: Vomiting.'
    },
    es: {
      intro: 'Inicio, contenido del vómito, signos epigástricos y capa lingual distinguen exceso agudo de deficiencia crónica.',
      rows: [
        ['Patógeno Exterior invade Estómago', 'Vómito súbito con escalofríos, fiebre, dolores y opresión torácica', 'Huo Xiang Zheng Qi San', 'Liberar Exterior y transformar turbidez.'],
        ['Estancamiento alimentario', 'Vómito de alimento no digerido, eructo fétido, reflujo y capa gruesa grasosa', 'Bao He Wan', 'Reducir alimento y conducir estancamiento.'],
        ['Qi de Hígado perturba Estómago', 'Vómito por estrés, eructo, reflujo y dolor hipocondríaco', 'Ban Xia Hou Po Tang o Zuo Jin Wan si domina Calor', 'Suavizar Hígado y descender Qi de Estómago.'],
        ['Obstrucción Flema-Humedad', 'Líquido claro/moco, mareo, palpitaciones y capa blanca grasosa', 'Xiao Ban Xia Tang + Ling Gui Zhu Gan Tang', 'Calentar y transformar Flema-líquidos.'],
        ['Deficiencia Yin de Estómago', 'Arcadas repetidas, sed, boca seca y hambre sin deseo de comer', 'Mai Men Dong Tang', 'Nutrir Yin de Estómago y descender Qi.'],
        ['Frío por deficiencia Bazo-Estómago', 'Vómito tras comer, fatiga, extremidades frías, heces sueltas y lengua pálida', 'Li Zhong Tang', 'Calentar Centro y fortalecer Bazo.'],
      ],
      rule: 'Fiebre/escalofríos súbitos indica Exterior; alimento no digerido y olor fétido, Comida; emoción, Hígado; moco claro/capa grasosa, Flema; arcada seca, Yin.',
      memory: '<strong>Exterior—súbito; Comida—fétido; Hígado—estrés; Flema—moco claro; Yin—arcada seca; Frío—débil y pálido.</strong>',
      safety: 'Sangre o material tipo café, deshidratación grave, vómito biliar persistente, cefalea grave, abdomen rígido o no retener líquidos requiere urgencias.',
      source: 'Internal Medicine 1, Handout 2: Vomiting.'
    }
  },
  {
    slug: 'constipation-patterns', title: { en: 'Constipation Pattern Differentiation', es: 'Diferenciación de Patterns de Estreñimiento' },
    summary: { en: 'Separate Heat, Qi stagnation, Qi deficiency, Blood deficiency and Cold constipation.', es: 'Separa estreñimiento por Calor, estancamiento de Qi, deficiencia Qi, deficiencia de Sangre y Frío.' },
    focus: { en: 'Dryness, effort, distention and Cold/deficiency signs', es: 'Sequedad, esfuerzo, distensión y signos de Frío/deficiencia' },
    search: 'constipation estreñimiento heat qi stagnation deficiency blood cold ma zi ren liu mo huang qi run chang ji chuan',
    en: {
      intro: 'Determine whether stool is dry, whether expulsion lacks force, and whether Heat, stagnation, Blood deficiency or Cold accompanies it.',
      rows: [
        ['Heat constipation', 'Dry stool, thirst, halitosis, abdominal fullness and yellow dry coat', 'Ma Zi Ren Wan', 'Clear Heat and moisten Intestines.'],
        ['Qi stagnation', 'Constipation with frequent belching and chest/abdominal distention', 'Liu Mo Yin', 'Move Qi and stagnation.'],
        ['Qi deficiency', 'Difficult expulsion without marked dryness; fatigue, short breath and sweating', 'Huang Qi Tang', 'Boost Qi and free stool.'],
        ['Blood deficiency', 'Dry hard stool with pallor, dizziness and palpitations', 'Run Chang Wan', 'Nourish Blood and moisten Intestines.'],
        ['Cold / Kidney-Yang deficiency', 'Difficult stool with clear urine, cold limbs and weak painful low back', 'Ji Chuan Jian', 'Warm Kidney Yang and moisten bowels.'],
      ],
      rule: 'Dry yellow Heat differs from weak pushing Qi deficiency; pallor/dizziness identifies Blood deficiency; distention/belching identifies Qi stagnation; Cold limbs and lumbar weakness identify Kidney Yang.',
      memory: '<strong>Heat—dry/yellow; Qi stagnation—distended; Qi deficiency—cannot push; Blood—pale/dry; Cold—cold back.</strong>',
      safety: 'No stool or gas with severe distention, vomiting, blood, fever, weight loss or new constipation after age 50 requires prompt medical evaluation.',
      source: 'Internal Medicine 1, Handout 3: Constipation.'
    },
    es: {
      intro: 'Determina si las heces están secas, si falta fuerza para expulsar y si acompañan Calor, estancamiento, deficiencia de Sangre o Frío.',
      rows: [
        ['Estreñimiento por Calor', 'Heces secas, sed, halitosis, plenitud y capa amarilla seca', 'Ma Zi Ren Wan', 'Aclarar Calor y humedecer Intestinos.'],
        ['Estancamiento de Qi', 'Estreñimiento con eructos frecuentes y distensión toracoabdominal', 'Liu Mo Yin', 'Mover Qi y estancamiento.'],
        ['Deficiencia de Qi', 'Expulsión difícil sin sequedad marcada; fatiga, disnea y sudor', 'Huang Qi Tang', 'Aumentar Qi y liberar heces.'],
        ['Deficiencia de Sangre', 'Heces secas duras con palidez, mareo y palpitaciones', 'Run Chang Wan', 'Nutrir Sangre y humedecer Intestinos.'],
        ['Frío / deficiencia Yang renal', 'Heces difíciles con orina clara, extremidades frías y lumbar débil/dolorosa', 'Ji Chuan Jian', 'Calentar Yang renal y humedecer intestino.'],
      ],
      rule: 'Calor seco/amarillo difiere de falta de empuje Qi; palidez/mareo identifica Sangre; distensión/eructo, estancamiento Qi; frío y lumbar débil, Yang renal.',
      memory: '<strong>Calor—seco/amarillo; Qi estancado—distendido; Qi deficiente—no empuja; Sangre—pálido/seco; Frío—lumbar fría.</strong>',
      safety: 'Ausencia de heces y gases con distensión grave, vómito, sangre, fiebre, pérdida de peso o estreñimiento nuevo después de 50 requiere evaluación pronta.',
      source: 'Internal Medicine 1, Handout 3: Constipation.'
    }
  },
  {
    slug: 'palpitations-patterns', title: { en: 'Palpitations Pattern Differentiation', es: 'Diferenciación de Patterns de Palpitaciones' },
    summary: { en: 'Compare fright/Qi deficiency, Blood or Yin deficiency, Yang deficiency, Phlegm-fluid and Blood stasis.', es: 'Compara sobresalto/deficiencia Qi, deficiencia de Sangre o Yin, deficiencia Yang, Flema-líquidos y estasis de Sangre.' },
    focus: { en: 'Spirit, temperature, fluids and chest obstruction', es: 'Shen, temperatura, líquidos y obstrucción torácica' },
    search: 'palpitations palpitaciones heart gallbladder qi blood yin yang phlegm water stasis an shen gui pi tian wang gui zhi gan cao ling gui tao ren hong hua',
    en: {
      intro: 'Emotional fright, pallor, empty Heat, Cold, fluid signs and fixed chest pain separate the principal branches.',
      rows: [
        ['Heart-Gallbladder Qi deficiency', 'Easily frightened, timid, insomnia and profuse dreams', 'An Shen Ding Zhi Wan', 'Supplement Qi, settle fright and calm Spirit.'],
        ['Heart Blood deficiency', 'Palpitations with dizziness, pallor, fatigue and weak-thready pulse', 'Gui Pi Tang', 'Supplement Qi-Blood and nourish Heart.'],
        ['Yin deficiency with Heat', 'Palpitations, insomnia, five-center Heat, tinnitus and red peeled tongue', 'Tian Wang Bu Xin Dan', 'Nourish Yin and descend Fire.'],
        ['Heart Yang deficiency', 'Palpitations, chest oppression, short breath, cold limbs and pale tongue', 'Gui Zhi Gan Cao Long Gu Mu Li Tang', 'Warm Heart Yang and calm Spirit.'],
        ['Phlegm-fluid oppressing Heart', 'Palpitations with dizziness, mucus, edema, scant urine and greasy coat', 'Ling Gui Zhu Gan Tang', 'Warm Yang and transform Phlegm-fluid.'],
        ['Blood stasis obstructing Heart', 'Chest pain/oppression, purple lips or nails and intermittent pulse', 'Tao Ren Hong Hua Jian', 'Invigorate Blood and open connections.'],
      ],
      rule: 'Fright points to Heart-Gallbladder; pallor to Blood; empty Heat to Yin; Cold to Yang; edema/mucus to fluid; fixed chest pain and purple signs to stasis.',
      memory: '<strong>Fright—Qi; pale—Blood; hot—Yin; cold—Yang; wet—Phlegm; purple pain—stasis.</strong>',
      safety: 'Palpitations with chest pain, fainting, severe shortness of breath, sustained rapid/irregular pulse or neurologic symptoms requires urgent cardiac evaluation.',
      source: 'Internal Medicine 1, Handout 5: Palpitations.'
    },
    es: {
      intro: 'Sobresalto, palidez, Calor vacío, Frío, signos de líquidos y dolor torácico fijo separan las ramas principales.',
      rows: [
        ['Deficiencia Qi Corazón-Vesícula', 'Sobresalto fácil, timidez, insomnio y muchos sueños', 'An Shen Ding Zhi Wan', 'Suplementar Qi, contener sobresalto y calmar Shen.'],
        ['Deficiencia de Sangre de Corazón', 'Palpitaciones con mareo, palidez, fatiga y pulso débil-fino', 'Gui Pi Tang', 'Suplementar Qi-Sangre y nutrir Corazón.'],
        ['Deficiencia Yin con Calor', 'Palpitaciones, insomnio, Calor de cinco centros, tinnitus y lengua roja pelada', 'Tian Wang Bu Xin Dan', 'Nutrir Yin y descender Fuego.'],
        ['Deficiencia Yang de Corazón', 'Palpitaciones, opresión, disnea, extremidades frías y lengua pálida', 'Gui Zhi Gan Cao Long Gu Mu Li Tang', 'Calentar Yang de Corazón y calmar Shen.'],
        ['Flema-líquidos oprime Corazón', 'Palpitaciones con mareo, moco, edema, orina escasa y capa grasosa', 'Ling Gui Zhu Gan Tang', 'Calentar Yang y transformar Flema-líquidos.'],
        ['Estasis de Sangre obstruye Corazón', 'Dolor/opresión torácica, labios/uñas púrpuras y pulso intermitente', 'Tao Ren Hong Hua Jian', 'Vigorizar Sangre y abrir conexiones.'],
      ],
      rule: 'Sobresalto señala Corazón-Vesícula; palidez, Sangre; Calor vacío, Yin; Frío, Yang; edema/moco, líquidos; dolor fijo y púrpura, estasis.',
      memory: '<strong>Sobresalto—Qi; pálido—Sangre; caliente—Yin; frío—Yang; húmedo—Flema; dolor púrpura—estasis.</strong>',
      safety: 'Palpitaciones con dolor torácico, desmayo, disnea grave, pulso rápido/irregular sostenido o síntomas neurológicos requiere evaluación cardíaca urgente.',
      source: 'Internal Medicine 1, Handout 5: Palpitations.'
    }
  },
  {
    slug: 'dizziness-vertigo-patterns', title: { en: 'Dizziness and Vertigo Pattern Differentiation', es: 'Diferenciación de Patterns de Mareo y Vértigo' },
    summary: { en: 'Compare Liver Yang, Qi-Blood deficiency, Kidney deficiency and Damp-Phlegm or Phlegm-Heat.', es: 'Compara Yang de Hígado, deficiencia Qi-Sangre, deficiencia renal y Humedad-Flema o Flema-Calor.' },
    focus: { en: 'Upward Yang, deficient nourishment and Phlegm obstruction', es: 'Yang ascendente, nutrición deficiente y obstrucción por Flema' },
    search: 'dizziness vertigo mareo liver yang qi blood kidney yin yang damp phlegm heat tian ma gou teng gui pi zuo gui you gui ban xia bai zhu wen dan',
    en: {
      intro: 'Head pressure and anger point upward, pallor points to Qi-Blood deficiency, lumbar signs point to Kidney, and heaviness/nausea point to Phlegm.',
      rows: [
        ['Liver Yang rising', 'Vertigo with distending headache, tinnitus, anger, irritability and wiry pulse', 'Tian Ma Gou Teng Yin', 'Subdue Yang and nourish Liver-Kidney.'],
        ['Qi and Blood deficiency', 'Pallor, fatigue, weak voice, palpitations, insomnia and poor appetite', 'Gui Pi Tang', 'Strengthen Spleen and supplement Qi-Blood.'],
        ['Kidney Yin/Essence deficiency', 'Tinnitus, weak back, emissions, forgetfulness and five-center Heat', 'Zuo Gui Wan', 'Nourish Kidney Yin and Essence.'],
        ['Kidney Yang/Essence deficiency', 'Tinnitus and weak back with cold limbs and pale tongue', 'You Gui Wan', 'Warm Kidney Yang and Essence.'],
        ['Damp-Phlegm in Middle', 'Heavy wrapped head, nausea, chest oppression, sleepiness and white greasy coat', 'Ban Xia Bai Zhu Tian Ma Tang', 'Dry Dampness and transform Phlegm.'],
        ['Phlegm-Heat', 'Heavy head plus irritability, bitter taste, thirst and yellow greasy coat', 'Wen Dan Tang', 'Transform Phlegm and clear Heat.'],
      ],
      rule: 'Anger/head distention identifies Liver Yang; pallor/fatigue identifies Qi-Blood; lumbar-tinnitus signs identify Kidney; heaviness and greasy coat identify Phlegm, with yellow/bitter signs marking Heat.',
      memory: '<strong>Upward—Liver; pale—Qi/Blood; back—Kidney; heavy—Phlegm; yellow—Phlegm-Heat.</strong>',
      safety: 'Sudden vertigo with one-sided weakness, speech difficulty, severe headache, fainting, chest pain or inability to walk requires emergency stroke/cardiac assessment.',
      source: 'Internal Medicine 1, Handout 6: Dizziness and Vertigo.'
    },
    es: {
      intro: 'Presión cefálica e ira apuntan arriba, palidez a Qi-Sangre, signos lumbares a Riñón y pesadez/náusea a Flema.',
      rows: [
        ['Yang de Hígado ascendente', 'Vértigo con cefalea distensiva, tinnitus, ira, irritabilidad y pulso de cuerda', 'Tian Ma Gou Teng Yin', 'Subyugar Yang y nutrir Hígado-Riñón.'],
        ['Deficiencia Qi y Sangre', 'Palidez, fatiga, voz débil, palpitaciones, insomnio y poco apetito', 'Gui Pi Tang', 'Fortalecer Bazo y suplementar Qi-Sangre.'],
        ['Deficiencia Yin/Esencia renal', 'Tinnitus, lumbar débil, emisiones, mala memoria y Calor de cinco centros', 'Zuo Gui Wan', 'Nutrir Yin y Esencia renal.'],
        ['Deficiencia Yang/Esencia renal', 'Tinnitus y lumbar débil con extremidades frías y lengua pálida', 'You Gui Wan', 'Calentar Yang y Esencia renal.'],
        ['Humedad-Flema en Centro', 'Cabeza pesada/envuelta, náusea, opresión, somnolencia y capa blanca grasosa', 'Ban Xia Bai Zhu Tian Ma Tang', 'Secar Humedad y transformar Flema.'],
        ['Flema-Calor', 'Cabeza pesada más irritabilidad, sabor amargo, sed y capa amarilla grasosa', 'Wen Dan Tang', 'Transformar Flema y aclarar Calor.'],
      ],
      rule: 'Ira/distensión cefálica identifica Yang de Hígado; palidez/fatiga, Qi-Sangre; lumbar/tinnitus, Riñón; pesadez/capa grasosa, Flema; amarillo/amargo marca Calor.',
      memory: '<strong>Arriba—Hígado; pálido—Qi/Sangre; lumbar—Riñón; pesado—Flema; amarillo—Flema-Calor.</strong>',
      safety: 'Vértigo súbito con debilidad unilateral, habla alterada, cefalea grave, desmayo, dolor torácico o no poder caminar requiere evaluación urgente de ictus/corazón.',
      source: 'Internal Medicine 1, Handout 6: Dizziness and Vertigo.'
    }
  },
  {
    slug: 'hypertension-patterns', title: { en: 'Hypertension Pattern Differentiation', es: 'Diferenciación de Patterns de Hipertensión' },
    summary: { en: 'Compare Yin deficiency with rising Yang, Liver Fire, Phlegm-Damp and combined Yin-Yang deficiency.', es: 'Compara deficiencia Yin con Yang ascendente, Fuego de Hígado, Flema-Humedad y deficiencia combinada Yin-Yang.' },
    focus: { en: 'TCM presentation alongside biomedical blood-pressure management', es: 'Presentación MTC junto al manejo biomédico de presión arterial' },
    search: 'hypertension hipertension yin yang liver fire phlegm damp qi ju long dan ban xia bai zhu tian ma er xian blood pressure',
    en: {
      intro: 'Blood-pressure measurement establishes the biomedical condition; TCM signs differentiate the accompanying presentation, not the numeric diagnosis itself.',
      rows: [
        ['Yin deficiency with Yang rising', 'Headache/vertigo, tinnitus, weak back, five-center Heat and red scant-coated tongue', 'Qi Ju Di Huang Wan', 'Nourish Liver-Kidney Yin and subdue Yang.'],
        ['Exuberant Liver Fire', 'Red face/eyes, irritability, bitter dry mouth, dark urine and constipation', 'Long Dan Xie Gan Tang', 'Drain Liver Fire.'],
        ['Phlegm-Damp obstruction', 'Heavy painful head, nausea, chest oppression, obesity and white greasy coat', 'Ban Xia Bai Zhu Tian Ma Tang', 'Transform Phlegm and Dampness.'],
        ['Yin and Yang deficiency', 'Movement-aggravated dizziness, blurred vision, tinnitus, palpitations, dyspnea and lumbar weakness', 'Er Xian Tang', 'Support Kidney Yin and Yang.'],
      ],
      rule: 'Empty Heat/lumbar weakness marks Yin deficiency; red angry excess marks Liver Fire; heaviness/greasy coat marks Phlegm-Damp; mixed weakness with both Yin and Yang signs marks combined deficiency.',
      memory: '<strong>Hot-empty—Yin; red-angry—Fire; heavy-greasy—Phlegm; mixed weak—Yin and Yang.</strong>',
      safety: 'Herbs do not replace prescribed antihypertensives. Blood pressure at or above 180/120 with chest pain, neurologic symptoms, severe headache or shortness of breath is an emergency.',
      source: 'Internal Medicine 1, Handout 6: Hypertension.'
    },
    es: {
      intro: 'La medición establece la condición biomédica; los signos MTC diferencian la presentación acompañante, no el diagnóstico numérico.',
      rows: [
        ['Deficiencia Yin con Yang ascendente', 'Cefalea/vértigo, tinnitus, lumbar débil, Calor de cinco centros y lengua roja con poca capa', 'Qi Ju Di Huang Wan', 'Nutrir Yin Hígado-Riñón y subyugar Yang.'],
        ['Fuego exuberante de Hígado', 'Cara/ojos rojos, irritabilidad, boca amarga seca, orina oscura y estreñimiento', 'Long Dan Xie Gan Tang', 'Drenar Fuego de Hígado.'],
        ['Obstrucción Flema-Humedad', 'Cabeza pesada/dolorosa, náusea, opresión torácica, obesidad y capa blanca grasosa', 'Ban Xia Bai Zhu Tian Ma Tang', 'Transformar Flema y Humedad.'],
        ['Deficiencia Yin y Yang', 'Mareo peor al moverse, visión borrosa, tinnitus, palpitaciones, disnea y lumbar débil', 'Er Xian Tang', 'Apoyar Yin y Yang renal.'],
      ],
      rule: 'Calor vacío/lumbar débil marca Yin; exceso rojo e irritable, Fuego; pesadez/capa grasosa, Flema-Humedad; debilidad mixta con signos Yin-Yang, deficiencia combinada.',
      memory: '<strong>Caliente-vacío—Yin; rojo-irritable—Fuego; pesado-grasoso—Flema; débil-mixto—Yin y Yang.</strong>',
      safety: 'Las hierbas no sustituyen antihipertensivos prescritos. Presión ≥180/120 con dolor torácico, síntomas neurológicos, cefalea grave o disnea es emergencia.',
      source: 'Internal Medicine 1, Handout 6: Hypertension.'
    }
  },
  {
    slug: 'low-back-pain-patterns', title: { en: 'Low Back Pain Pattern Differentiation', es: 'Diferenciación de Patterns de Dolor Lumbar' },
    summary: { en: 'Compare Cold-Damp, Damp-Heat, Blood stasis and Kidney Yin or Yang deficiency.', es: 'Compara Frío-Humedad, Humedad-Calor, estasis de Sangre y deficiencia Yin o Yang renal.' },
    focus: { en: 'Temperature, weather, pain quality and deficiency signs', es: 'Temperatura, clima, calidad del dolor y signos de deficiencia' },
    search: 'low back pain lumbar cold damp heat blood stasis kidney yin yang gan cao gan jiang ling zhu si miao shen tong zhu yu you gui zuo gui',
    en: {
      intro: 'Weather and temperature identify Damp branches, fixed stabbing pain identifies stasis, and recurrent aching with weakness identifies Kidney deficiency.',
      rows: [
        ['Cold-Damp', 'Cold heavy pain worse in cold/rain and relieved by warmth', 'Gan Cao Gan Jiang Ling Zhu Tang', 'Warm channels and drain Dampness.'],
        ['Damp-Heat', 'Hot lumbar pain worse in hot/rainy weather with dark urine and yellow greasy coat', 'Si Miao San', 'Clear Heat and drain Dampness.'],
        ['Blood stasis', 'Fixed stabbing night pain, worse with pressure; purple tongue', 'Shen Tong Zhu Yu Tang', 'Invigorate Blood and open channels.'],
        ['Kidney Yang deficiency', 'Recurrent aching better with rest/pressure plus cold and fatigue', 'You Gui Wan', 'Warm Kidney Yang.'],
        ['Kidney Yin deficiency', 'Recurrent aching plus insomnia, dry throat and five-center Heat', 'Zuo Gui Wan', 'Nourish Kidney Yin.'],
      ],
      rule: 'Cold-heavy-weather pain is Damp; hot-yellow signs are Damp-Heat; fixed stabbing is stasis; chronic weakness with Cold is Yang and with empty Heat is Yin.',
      memory: '<strong>Cold-heavy—Cold-Damp; hot-sticky—Damp-Heat; fixed-purple—stasis; cold-weak—Yang; hot-dry—Yin.</strong>',
      safety: 'New weakness, saddle numbness, bladder/bowel loss, fever, major trauma, cancer history or severe night pain requires urgent evaluation.',
      source: 'Internal Medicine 1, Handout 6: Low Back Pain.'
    },
    es: {
      intro: 'Clima y temperatura identifican Humedad, dolor fijo punzante identifica estasis y dolor recurrente con debilidad identifica Riñón.',
      rows: [
        ['Frío-Humedad', 'Dolor frío/pesado peor con frío o lluvia y aliviado por calor', 'Gan Cao Gan Jiang Ling Zhu Tang', 'Calentar canales y drenar Humedad.'],
        ['Humedad-Calor', 'Dolor lumbar caliente peor con clima cálido/lluvioso, orina oscura y capa amarilla grasosa', 'Si Miao San', 'Aclarar Calor y drenar Humedad.'],
        ['Estasis de Sangre', 'Dolor nocturno fijo punzante, peor con presión; lengua púrpura', 'Shen Tong Zhu Yu Tang', 'Vigorizar Sangre y abrir canales.'],
        ['Deficiencia Yang renal', 'Dolor recurrente mejor con reposo/presión más frío y fatiga', 'You Gui Wan', 'Calentar Yang renal.'],
        ['Deficiencia Yin renal', 'Dolor recurrente más insomnio, garganta seca y Calor de cinco centros', 'Zuo Gui Wan', 'Nutrir Yin renal.'],
      ],
      rule: 'Dolor frío-pesado por clima es Humedad; signos calientes/amarillos son Humedad-Calor; fijo punzante es estasis; debilidad fría es Yang y con Calor vacío es Yin.',
      memory: '<strong>Frío-pesado—Frío-Humedad; caliente-pegajoso—Humedad-Calor; fijo-púrpura—estasis; frío-débil—Yang; caliente-seco—Yin.</strong>',
      safety: 'Debilidad nueva, anestesia en silla, pérdida de vejiga/intestino, fiebre, trauma, cáncer o dolor nocturno grave requiere urgencias.',
      source: 'Internal Medicine 1, Handout 6: Low Back Pain.'
    }
  },
  {
    slug: 'seminal-emission-patterns', title: { en: 'Seminal Emission Pattern Differentiation', es: 'Diferenciación de Patterns de Emisión Seminal' },
    summary: { en: 'Separate deficient Fire, Kidney Yang failure, Qi failure to secure and Lower-Burner Damp-Heat.', es: 'Separa Fuego vacío, fallo de Yang renal, Qi que no asegura y Humedad-Calor del Jiao Inferior.' },
    focus: { en: 'Heat versus Cold, fatigue and urinary signs', es: 'Calor frente a Frío, fatiga y signos urinarios' },
    search: 'seminal emission spermatorrhea kidney yin heart fire yang qi deficiency damp heat zhi bai you gui bu zhong bi xie',
    en: {
      intro: 'Heat/Cold, relation to overwork and urinary burning distinguish failure to secure Essence.',
      rows: [
        ['Kidney Yin deficiency with Heart Fire', 'Dream emission, insomnia, dry throat, night sweat and five-center Heat', 'Zhi Bai Di Huang Wan', 'Nourish Yin, clear Fire and secure Essence.'],
        ['Kidney Yang failing to secure', 'Persistent spontaneous discharge with cold limbs, impotence and pale tongue', 'You Gui Wan', 'Warm Kidney Yang and secure Essence.'],
        ['Qi deficiency failing to astringe', 'Emission after overwork with fatigue, poor appetite and loose stool', 'Bu Zhong Yi Qi Tang', 'Support Spleen Qi and secure Essence.'],
        ['Lower-Burner Damp-Heat', 'Emission with burning cloudy urine, bitter taste and yellow greasy coat', 'Cheng Shi Bi Xie Fen Qing Yin', 'Clear Damp-Heat and separate clear from turbid.'],
      ],
      rule: 'Empty Heat points to Yin; cold spontaneous leakage to Yang; overwork/digestion to Qi; burning cloudy urine to Damp-Heat.',
      memory: '<strong>Hot-dry—Yin; cold-leaking—Yang; tired-after-work—Qi; burning-cloudy—Damp-Heat.</strong>',
      safety: 'Pain, blood, fever, genital discharge, urinary retention or new persistent symptoms requires medical evaluation for infection or urologic disease.',
      source: 'Internal Medicine 1, Handout 7: Seminal Emission.'
    },
    es: {
      intro: 'Calor/Frío, relación con exceso de trabajo y ardor urinario distinguen el fallo al asegurar Esencia.',
      rows: [
        ['Deficiencia Yin renal con Fuego de Corazón', 'Emisión en sueños, insomnio, garganta seca, sudor nocturno y Calor de cinco centros', 'Zhi Bai Di Huang Wan', 'Nutrir Yin, aclarar Fuego y asegurar Esencia.'],
        ['Yang renal no asegura', 'Descarga espontánea persistente con frío, impotencia y lengua pálida', 'You Gui Wan', 'Calentar Yang renal y asegurar Esencia.'],
        ['Deficiencia Qi no contiene', 'Emisión tras sobreesfuerzo con fatiga, poco apetito y heces sueltas', 'Bu Zhong Yi Qi Tang', 'Apoyar Qi de Bazo y asegurar Esencia.'],
        ['Humedad-Calor inferior', 'Emisión con orina turbia ardiente, sabor amargo y capa amarilla grasosa', 'Cheng Shi Bi Xie Fen Qing Yin', 'Aclarar Humedad-Calor y separar claro/turbio.'],
      ],
      rule: 'Calor vacío apunta a Yin; pérdida fría espontánea a Yang; sobreesfuerzo/digestión a Qi; orina turbia ardiente a Humedad-Calor.',
      memory: '<strong>Caliente-seco—Yin; frío-pérdida—Yang; cansado-tras trabajo—Qi; ardiente-turbio—Humedad-Calor.</strong>',
      safety: 'Dolor, sangre, fiebre, secreción genital, retención urinaria o síntomas nuevos persistentes requiere evaluación médica.',
      source: 'Internal Medicine 1, Handout 7: Seminal Emission.'
    }
  },
  {
    slug: 'erectile-dysfunction-patterns', title: { en: 'Erectile Dysfunction Pattern Differentiation', es: 'Diferenciación de Patterns de Disfunción Eréctil' },
    summary: { en: 'Separate Life-Gate Fire debilitation from Lower-Burner Damp-Heat.', es: 'Separa debilitamiento del Fuego de Ming Men de Humedad-Calor del Jiao Inferior.' },
    focus: { en: 'Cold deficiency versus hot, heavy urinary signs', es: 'Deficiencia fría frente a signos calientes, pesados y urinarios' },
    search: 'erectile dysfunction impotence life gate kidney yang damp heat wu zi yan zong zan yu dan long dan xie gan',
    en: {
      intro: 'The source contrasts a cold Kidney-deficiency presentation with a hot, heavy Lower-Burner excess.',
      rows: [
        ['Life-Gate Fire debilitation', 'Weak erection with cold limbs, fatigue, tinnitus, weak back and frequent urination', 'Wu Zi Yan Zong Wan or Zan Yu Dan', 'Supplement Kidney and invigorate Yang.'],
        ['Lower-Burner Damp-Heat', 'Erectile difficulty with scrotal dampness, heavy legs, bitter taste and burning dark urine', 'Long Dan Xie Gan Tang', 'Clear Heat and drain Dampness.'],
      ],
      rule: 'Cold, fatigue and lumbar weakness indicate Kidney Yang; damp genital signs, heaviness and burning urine indicate Damp-Heat.',
      memory: '<strong>Cold and weak—Ming Men; hot, heavy and damp—Damp-Heat.</strong>',
      safety: 'New erectile dysfunction can signal cardiovascular, endocrine, neurologic or medication-related disease. Chest symptoms or exertional pain requires prompt evaluation.',
      source: 'Internal Medicine 1, Handout 7: Impotence.'
    },
    es: {
      intro: 'La fuente contrasta una presentación fría por deficiencia renal con un exceso caliente/pesado del Jiao Inferior.',
      rows: [
        ['Debilitamiento del Fuego de Ming Men', 'Erección débil con frío, fatiga, tinnitus, lumbar débil y micción frecuente', 'Wu Zi Yan Zong Wan o Zan Yu Dan', 'Suplementar Riñón e vigorizar Yang.'],
        ['Humedad-Calor del Jiao Inferior', 'Dificultad eréctil con humedad escrotal, piernas pesadas, sabor amargo y orina oscura ardiente', 'Long Dan Xie Gan Tang', 'Aclarar Calor y drenar Humedad.'],
      ],
      rule: 'Frío, fatiga y lumbar débil indican Yang renal; humedad genital, pesadez y orina ardiente indican Humedad-Calor.',
      memory: '<strong>Frío y débil—Ming Men; caliente, pesado y húmedo—Humedad-Calor.</strong>',
      safety: 'Disfunción eréctil nueva puede señalar enfermedad cardiovascular, endocrina, neurológica o efecto farmacológico. Síntomas torácicos requieren evaluación pronta.',
      source: 'Internal Medicine 1, Handout 7: Impotence.'
    }
  },
  {
    slug: 'wasting-thirst-patterns', title: { en: 'Wasting-Thirst Pattern Differentiation', es: 'Diferenciación de Patterns de Wasting-Thirst' },
    summary: { en: 'Compare Lung Heat, Stomach Fire and Kidney Yin depletion across the three burners.', es: 'Compara Calor de Pulmón, Fuego de Estómago y agotamiento de Yin renal en los tres Jiaos.' },
    focus: { en: 'Thirst, hunger and urination by burner', es: 'Sed, hambre y micción según Jiao' },
    search: 'wasting thirst xiao ke diabetes lung heat stomach fire kidney yin thirst hunger urination xiao ke fang yu nu jian liu wei di huang',
    en: {
      intro: 'The classic three-burner map distinguishes predominant thirst, hunger or urination; biomedical glucose testing remains essential.',
      rows: [
        ['Upper Burner · Lung Heat injures fluids', 'Excessive thirst and drinking with dry mouth; urination and hunger may accompany', 'Xiao Ke Fang', 'Clear Lung Heat and generate fluids.'],
        ['Middle Burner · Stomach Fire', 'Excessive hunger/eating, emaciation, thirst and dry stool', 'Yu Nu Jian', 'Clear Stomach Fire and nourish Yin.'],
        ['Lower Burner · Kidney Yin depletion', 'Frequent copious turbid urine, thirst, weak back, dizziness and red cheeks', 'Liu Wei Di Huang Wan', 'Nourish Kidney Yin and secure Kidney.'],
      ],
      rule: 'Thirst leads Upper Burner, hunger leads Middle Burner, and urination leads Lower Burner—but overlap is common.',
      memory: '<strong>Upper drinks; Middle eats; Lower urinates.</strong>',
      safety: 'This framework does not diagnose or replace diabetes care. Confusion, vomiting, deep breathing, severe dehydration or very high/low glucose requires urgent care.',
      source: 'Internal Medicine 1, Handout 7: Wasting-Thirst.'
    },
    es: {
      intro: 'El mapa clásico de tres Jiaos distingue sed, hambre o micción predominante; las pruebas biomédicas de glucosa son esenciales.',
      rows: [
        ['Jiao Superior · Calor de Pulmón lesiona líquidos', 'Sed y bebida excesiva con boca seca; puede acompañar micción y hambre', 'Xiao Ke Fang', 'Aclarar Calor pulmonar y generar líquidos.'],
        ['Jiao Medio · Fuego de Estómago', 'Hambre/comida excesiva, adelgazamiento, sed y heces secas', 'Yu Nu Jian', 'Aclarar Fuego de Estómago y nutrir Yin.'],
        ['Jiao Inferior · Agotamiento Yin renal', 'Orina frecuente abundante/turbia, sed, lumbar débil, mareo y mejillas rojas', 'Liu Wei Di Huang Wan', 'Nutrir Yin renal y asegurar Riñón.'],
      ],
      rule: 'Sed lidera Jiao Superior, hambre Jiao Medio y micción Jiao Inferior, aunque suelen superponerse.',
      memory: '<strong>Superior bebe; Medio come; Inferior orina.</strong>',
      safety: 'Este marco no diagnostica ni sustituye atención de diabetes. Confusión, vómito, respiración profunda, deshidratación o glucosa extrema requiere urgencias.',
      source: 'Internal Medicine 1, Handout 7: Wasting-Thirst.'
    }
  },
  {
    slug: 'urinary-strangury-patterns', title: { en: 'Urinary Strangury Pattern Differentiation', es: 'Diferenciación de Patterns de Urinary Strangury' },
    summary: { en: 'Distinguish Heat, Stone, Qi, Blood, turbid and taxation strangury presentations.', es: 'Distingue presentaciones de strangury por Calor, piedra, Qi, Sangre, turbidez y agotamiento.' },
    focus: { en: 'Burning, stones, blood, turbidity and deficiency', es: 'Ardor, piedras, sangre, turbidez y deficiencia' },
    search: 'urinary strangury lin syndrome heat stone qi blood unctuous taxation ba zheng shi wei chen xiang xiao ji bi xie gao lin wu bi shan yao',
    en: {
      intro: 'Urinary quality and pain identify the lin presentation; excess and deficiency must then be separated.',
      rows: [
        ['Heat lin', 'Frequent scant burning foul urine with thirst and yellow greasy coat', 'Ba Zheng San', 'Clear Damp-Heat and free urine.'],
        ['Stone lin', 'Sand/stone, interrupted stream, severe sacral pain or hematuria', 'Shi Wei San', 'Clear Heat and expel stones.'],
        ['Qi lin', 'Painful difficult urine with abdominal/hypochondriac distention; weak fatigue if deficient', 'Chen Xiang San; Bu Zhong Yi Qi Tang if deficient', 'Regulate or supplement Qi.'],
        ['Blood lin', 'Burning urination with blood/clots; slight pain plus empty Heat when deficient', 'Xiao Ji Yin Zi; Zhi Bai Di Huang Tang if deficient', 'Cool Blood or nourish Yin.'],
        ['Turbid/unctuous lin', 'Milky rice-water urine; fatigue and lumbar weakness mark deficiency', 'Cheng Shi Bi Xie Fen Qing Yin; Gao Lin Tang if deficient', 'Separate clear/turbid and secure Essence.'],
        ['Taxation lin', 'Dribbling after exertion with fatigue and weak low back/knees', 'Wu Bi Shan Yao Wan', 'Strengthen Spleen and Kidney.'],
      ],
      rule: 'Burning marks Heat, sudden stop marks Stone, distention marks Qi, red marks Blood, milky urine marks turbid lin, and exertional recurrence marks taxation.',
      memory: '<strong>Burn—Heat; stop—Stone; distend—Qi; red—Blood; milk—Turbid; strain—Taxation.</strong>',
      safety: 'Fever with flank pain, inability to urinate, vomiting, pregnancy, visible blood or suspected obstruction/stone requires urgent medical evaluation.',
      source: 'Internal Medicine 1, Handout 7: Urinary Strangury.'
    },
    es: {
      intro: 'Calidad urinaria y dolor identifican la presentación lin; luego debe separarse exceso de deficiencia.',
      rows: [
        ['Heat lin', 'Orina frecuente, escasa, ardiente y olorosa con sed/capa amarilla grasosa', 'Ba Zheng San', 'Aclarar Humedad-Calor y liberar orina.'],
        ['Stone lin', 'Arena/piedra, chorro interrumpido, dolor sacro grave o hematuria', 'Shi Wei San', 'Aclarar Calor y expulsar piedras.'],
        ['Qi lin', 'Orina dolorosa/difícil con distensión; fatiga débil si es deficiente', 'Chen Xiang San; Bu Zhong Yi Qi Tang si deficiente', 'Regular o suplementar Qi.'],
        ['Blood lin', 'Micción ardiente con sangre/coágulos; dolor leve y Calor vacío si deficiente', 'Xiao Ji Yin Zi; Zhi Bai Di Huang Tang si deficiente', 'Enfriar Sangre o nutrir Yin.'],
        ['Turbid/unctuous lin', 'Orina lechosa tipo agua de arroz; fatiga/lumbar débil marca deficiencia', 'Cheng Shi Bi Xie Fen Qing Yin; Gao Lin Tang si deficiente', 'Separar claro/turbio y asegurar Esencia.'],
        ['Taxation lin', 'Goteo tras esfuerzo con fatiga y lumbar/rodillas débiles', 'Wu Bi Shan Yao Wan', 'Fortalecer Bazo y Riñón.'],
      ],
      rule: 'Ardor marca Calor, parada súbita Piedra, distensión Qi, rojo Sangre, orina lechosa turbio y recurrencia tras esfuerzo agotamiento.',
      memory: '<strong>Arde—Calor; para—Piedra; distiende—Qi; rojo—Sangre; leche—Turbio; esfuerzo—Agotamiento.</strong>',
      safety: 'Fiebre con dolor de flanco, no poder orinar, vómito, embarazo, sangre visible o sospecha de obstrucción/piedra requiere urgencias.',
      source: 'Internal Medicine 1, Handout 7: Urinary Strangury.'
    }
  },
  {
    slug: 'urinary-retention-patterns', title: { en: 'Urinary Retention Pattern Differentiation', es: 'Diferenciación de Patterns de Retención Urinaria' },
    summary: { en: 'Compare Bladder Damp-Heat, Lung Heat, Liver-Qi stagnation, Blood stasis and Spleen/Kidney deficiency.', es: 'Compara Humedad-Calor de Vejiga, Calor de Pulmón, estancamiento Qi de Hígado, estasis y deficiencia Bazo/Riñón.' },
    focus: { en: 'Acute obstruction, location and deficient urinary force', es: 'Obstrucción aguda, localización y fuerza urinaria deficiente' },
    search: 'urinary retention dribbling block bladder damp heat lung heat liver qi blood stasis spleen kidney ba zheng qing fei chen xiang dai di dang bu zhong wu ling ji sheng shen qi',
    en: {
      intro: 'Heat, respiratory signs, emotion, fixed lower-abdominal pain and weak urinary force distinguish the branches.',
      rows: [
        ['Bladder Damp-Heat', 'Hot scant urine/retention, lower-abdominal fullness, thirst and yellow greasy coat', 'Ba Zheng San', 'Clear Damp-Heat and free urine.'],
        ['Lung Heat congestion', 'Retention with dry throat, chest Heat, thirst, rapid breathing or cough', 'Qing Fei Yin', 'Clear Lung Heat and open water passages.'],
        ['Liver-Qi stagnation', 'Difficult urine with emotional constraint and lower-abdominal/hypochondriac distention', 'Chen Xiang San', 'Soothe Liver and regulate Qi.'],
        ['Blood stasis obstruction', 'Dribbling/retention with fixed lower-abdominal pain and purple tongue', 'Dai Di Dang Wan', 'Invigorate Blood and dispel stasis.'],
        ['Spleen-Qi deficiency', 'Bearing-down fullness, periodic urge, fatigue, poor appetite and weak voice', 'Bu Zhong Yi Qi Tang + Wu Ling San', 'Raise Qi and promote urination.'],
        ['Kidney-Yang exhaustion', 'Weak urinary force with cold, fatigue and cold aching low back/knees', 'Ji Sheng Shen Qi Wan', 'Warm Kidney Yang and transform water.'],
      ],
      rule: 'Hot urine indicates Bladder; respiratory Heat indicates Lung; emotion/distention indicates Liver; fixed purple pain indicates Blood; bearing-down fatigue indicates Spleen; weak cold stream indicates Kidney.',
      memory: '<strong>Hot—Bladder; chest—Lung; emotion—Liver; fixed—Blood; sinking—Spleen; cold weak stream—Kidney.</strong>',
      safety: 'Acute inability to urinate is a medical emergency because obstruction can injure the kidneys. Severe pain, fever, vomiting or blood increases urgency.',
      source: 'Internal Medicine 1, Handout 7: Dribbling Urinary Block.'
    },
    es: {
      intro: 'Calor, signos respiratorios, emoción, dolor fijo hipogástrico y fuerza urinaria débil distinguen las ramas.',
      rows: [
        ['Humedad-Calor de Vejiga', 'Orina caliente escasa/retención, plenitud inferior, sed y capa amarilla grasosa', 'Ba Zheng San', 'Aclarar Humedad-Calor y liberar orina.'],
        ['Congestión por Calor de Pulmón', 'Retención con garganta seca, Calor torácico, sed, respiración rápida o tos', 'Qing Fei Yin', 'Aclarar Pulmón y abrir vías de agua.'],
        ['Estancamiento Qi de Hígado', 'Micción difícil con emoción y distensión hipogástrica/hipocondríaca', 'Chen Xiang San', 'Suavizar Hígado y regular Qi.'],
        ['Obstrucción por estasis', 'Goteo/retención con dolor inferior fijo y lengua púrpura', 'Dai Di Dang Wan', 'Vigorizar Sangre y eliminar estasis.'],
        ['Deficiencia Qi de Bazo', 'Sensación de caída/plenitud, urgencia periódica, fatiga, poco apetito y voz débil', 'Bu Zhong Yi Qi Tang + Wu Ling San', 'Elevar Qi y promover micción.'],
        ['Agotamiento Yang renal', 'Fuerza urinaria débil con frío, fatiga y lumbar/rodillas frías y dolorosas', 'Ji Sheng Shen Qi Wan', 'Calentar Yang renal y transformar agua.'],
      ],
      rule: 'Orina caliente indica Vejiga; Calor respiratorio Pulmón; emoción/distensión Hígado; dolor fijo púrpura Sangre; caída/fatiga Bazo; chorro débil frío Riñón.',
      memory: '<strong>Caliente—Vejiga; pecho—Pulmón; emoción—Hígado; fijo—Sangre; hundimiento—Bazo; chorro frío/débil—Riñón.</strong>',
      safety: 'Incapacidad aguda para orinar es emergencia porque la obstrucción puede dañar riñones. Dolor grave, fiebre, vómito o sangre aumenta urgencia.',
      source: 'Internal Medicine 1, Handout 7: Dribbling Urinary Block.'
    }
  },
  {
    slug: 'depression-patterns', title: { en: 'Depression Pattern Differentiation', es: 'Diferenciación de la Depresión' },
    summary: { en: 'Compare Liver-Qi stagnation, constrained Qi transforming into Fire, Qi-Phlegm obstruction and Yin-Blood deficiency.', es: 'Compara estancamiento de Qi de Hígado, Qi constreñido que se transforma en Fuego, obstrucción Qi-Flema y deficiencia de Yin-Sangre.' },
    focus: { en: 'Emotional constraint, Heat, plum-pit sensation and deficiency signs', es: 'Constreñimiento emocional, Calor, sensación de hueso de ciruela y signos de deficiencia' },
    search: 'depression liver qi stagnation fire phlegm plum pit yin blood chai hu shu gan dan zhi xiao yao zuo jin ban xia hou po gan mai da zao',
    en: {
      intro: 'Mood symptoms are differentiated by the dominant mechanism: constraint, Heat, Phlegm obstruction or deficient nourishment of the Heart-Spirit.',
      rows: [
        ['Liver-Qi stagnation', 'Despondence, frequent sighing, chest/hypochondriac distention, belching and wiry pulse', 'Chai Hu Shu Gan San', 'Soothe Liver, regulate Qi and relieve constraint.'],
        ['Constrained Qi transforms into Fire', 'Agitation, irritability, bitter dry mouth, acid regurgitation, constipation and red tongue', 'Dan Zhi Xiao Yao San + Zuo Jin Wan', 'Clear Liver Fire, harmonize Stomach and relieve constraint.'],
        ['Qi and Phlegm obstruction', 'Plum-pit throat sensation, chest blockage and slippery-wiry pulse', 'Ban Xia Hou Po Tang', 'Regulate Qi, transform Phlegm and dissipate binds.'],
        ['Yin and Blood deficiency', 'Trance-like state, fright, grief/crying, restlessness and pale tongue', 'Gan Mai Da Zao Tang', 'Nourish Heart and quiet the Spirit.'],
      ],
      rule: 'Sighing/distention marks Qi stagnation; red-bitter-irritable marks Fire; throat obstruction with sticky coat marks Phlegm; pale, fragile emotionality marks deficiency.',
      memory: '<strong>Sigh—Qi; burn—Fire; lump—Phlegm; pale and fragile—deficiency.</strong>',
      safety: 'Depression requires biomedical assessment. Suicidal thoughts, self-harm plans, inability to care for oneself, psychosis or immediate danger requires emergency help.',
      source: 'Internal Medicine 1, Handout 8: Depression Patterns.'
    },
    es: {
      intro: 'Los síntomas del ánimo se diferencian por el mecanismo dominante: constreñimiento, Calor, obstrucción por Flema o nutrición deficiente del Corazón-Shen.',
      rows: [
        ['Estancamiento de Qi de Hígado', 'Desánimo, suspiros, distensión torácica/hipocondríaca, eructos y pulso de cuerda', 'Chai Hu Shu Gan San', 'Suavizar Hígado, regular Qi y aliviar constreñimiento.'],
        ['Qi constreñido se transforma en Fuego', 'Agitación, irritabilidad, boca amarga/seca, regurgitación ácida, estreñimiento y lengua roja', 'Dan Zhi Xiao Yao San + Zuo Jin Wan', 'Aclarar Fuego de Hígado, armonizar Estómago y aliviar constreñimiento.'],
        ['Obstrucción de Qi y Flema', 'Sensación de hueso de ciruela en garganta, bloqueo torácico y pulso resbaladizo/de cuerda', 'Ban Xia Hou Po Tang', 'Regular Qi, transformar Flema y disipar nudos.'],
        ['Deficiencia de Yin y Sangre', 'Estado ausente, susto, pena/llanto, inquietud y lengua pálida', 'Gan Mai Da Zao Tang', 'Nutrir Corazón y aquietar Shen.'],
      ],
      rule: 'Suspiros/distensión marcan Qi; rojo-amargo-irritable marca Fuego; obstrucción de garganta con capa pegajosa marca Flema; palidez y fragilidad emocional marcan deficiencia.',
      memory: '<strong>Suspira—Qi; arde—Fuego; bulto—Flema; pálido y frágil—deficiencia.</strong>',
      safety: 'La depresión requiere evaluación biomédica. Ideas o plan suicida, autolesión, incapacidad de autocuidado, psicosis o peligro inmediato requiere ayuda de emergencia.',
      source: 'Internal Medicine 1, Handout 8: Depression Patterns.'
    }
  },
  {
    slug: 'mania-withdrawal-patterns', title: { en: 'Mania and Withdrawal Pattern Differentiation', es: 'Diferenciación de Manía y Retraimiento' },
    summary: { en: 'Separate depressive psychotic presentations from manic Phlegm-Fire and the deficient aftermath of prolonged illness.', es: 'Separa presentaciones psicóticas depresivas de manía por Flema-Fuego y las secuelas deficientes de enfermedad prolongada.' },
    focus: { en: 'Gradual withdrawal versus abrupt agitation, then Heat versus deficiency', es: 'Retraimiento gradual frente a agitación abrupta, luego Calor frente a deficiencia' },
    search: 'mania withdrawal psychosis qi phlegm heart spleen deficiency phlegm fire yin injury shun qi dao tan yang xin gan mai sheng tie luo er yin jian',
    en: {
      intro: 'Tempo and behavior lead: gradual dull withdrawal suggests a depressive branch, while abrupt destructive agitation suggests manic Phlegm-Fire.',
      rows: [
        ['Qi stagnation with Phlegm', 'Gradual withdrawal, dull senses, incoherent self-talk, sadness and white sticky coat', 'Shun Qi Dao Tan Tang', 'Rectify Qi, transform Phlegm and open orifices.'],
        ['Heart-Spleen Qi deficiency', 'Long duration, trance-like withdrawal, palpitations, fright, fatigue, poor appetite and pale tongue', 'Yang Xin Tang + Gan Mai Da Zao Tang', 'Strengthen Spleen, nourish Heart and quiet Spirit.'],
        ['Phlegm-Fire harasses the interior', 'Abrupt severe agitation, aggression, insomnia, red face/eyes and yellow sticky coat', 'Sheng Tie Luo Yin', 'Settle Heart, transform Phlegm and drain Fire.'],
        ['Exuberant Fire injures Yin', 'Prolonged illness with irritability, fright, insomnia, emaciation and red scant-coated tongue', 'Er Yin Jian', 'Nourish Yin, downbear Fire and stabilize emotions.'],
      ],
      rule: 'Gradual-sticky withdrawal is Qi-Phlegm; pale-tired withdrawal is deficiency; abrupt red-yellow aggression is Phlegm-Fire; chronic thin-red agitation is injured Yin.',
      memory: '<strong>Withdraw + sticky—Phlegm; withdraw + pale—deficiency; erupt + red—Fire; chronic + thin—Yin.</strong>',
      safety: 'New psychosis, mania, aggression, inability to sleep/eat, severe disorganization or danger to self/others needs urgent psychiatric and medical assessment.',
      source: 'Internal Medicine 1, Handout 8: Mania and Withdrawal.'
    },
    es: {
      intro: 'El ritmo y la conducta orientan: retraimiento apagado gradual sugiere rama depresiva; agitación destructiva abrupta sugiere Flema-Fuego maníaca.',
      rows: [
        ['Estancamiento de Qi con Flema', 'Retraimiento gradual, sentidos apagados, habla incoherente consigo mismo, tristeza y capa blanca pegajosa', 'Shun Qi Dao Tan Tang', 'Rectificar Qi, transformar Flema y abrir orificios.'],
        ['Deficiencia Qi de Corazón-Bazo', 'Duración larga, retraimiento ausente, palpitaciones, susto, fatiga, poco apetito y lengua pálida', 'Yang Xin Tang + Gan Mai Da Zao Tang', 'Fortalecer Bazo, nutrir Corazón y aquietar Shen.'],
        ['Flema-Fuego acosa el interior', 'Agitación grave abrupta, agresión, insomnio, cara/ojos rojos y capa amarilla pegajosa', 'Sheng Tie Luo Yin', 'Asentar Corazón, transformar Flema y drenar Fuego.'],
        ['Fuego exuberante lesiona Yin', 'Enfermedad prolongada con irritabilidad, susto, insomnio, adelgazamiento y lengua roja con poca capa', 'Er Yin Jian', 'Nutrir Yin, descender Fuego y estabilizar emociones.'],
      ],
      rule: 'Retraimiento gradual/pegajoso es Qi-Flema; pálido/cansado es deficiencia; agresión abrupta rojo-amarilla es Flema-Fuego; agitación crónica delgada/roja es Yin lesionado.',
      memory: '<strong>Retraído + pegajoso—Flema; retraído + pálido—deficiencia; explota + rojo—Fuego; crónico + delgado—Yin.</strong>',
      safety: 'Psicosis nueva, manía, agresión, no dormir/comer, desorganización grave o peligro para sí/otros requiere evaluación psiquiátrica y médica urgente.',
      source: 'Internal Medicine 1, Handout 8: Mania and Withdrawal.'
    }
  },
  {
    slug: 'epilepsy-patterns', title: { en: 'Epilepsy Pattern Differentiation', es: 'Diferenciación de la Epilepsia' },
    summary: { en: 'Compare Wind-Phlegm, internal Phlegm-Fire, Liver-Kidney Yin deficiency and Spleen-Stomach Qi deficiency.', es: 'Compara Viento-Flema, Flema-Fuego interno, deficiencia Yin de Hígado-Riñón y deficiencia Qi de Bazo-Estómago.' },
    focus: { en: 'Seizure presentation plus Phlegm, Heat or chronic deficiency signs', es: 'Presentación convulsiva más signos de Flema, Calor o deficiencia crónica' },
    search: 'epilepsy seizure wind phlegm fire liver kidney yin spleen stomach qi ding xian long dan xie gan di tan zuo gui liu jun zi',
    en: {
      intro: 'Seizures require neurologic diagnosis; the TCM comparison organizes accompanying signs into excess Phlegm/Fire or chronic deficiency branches.',
      rows: [
        ['Wind-Phlegm obstruction', 'Dizziness/chest oppression before sudden collapse, convulsions, foaming and white sticky coat', 'Ding Xian Wan', 'Expel Phlegm, extinguish Wind and open orifices.'],
        ['Internal Phlegm-Fire', 'Seizure with irritability, insomnia, yellow difficult phlegm, bitter mouth, constipation and red greasy tongue', 'Long Dan Xie Gan Tang + Di Tan Tang', 'Drain Fire, transform Phlegm and open orifices.'],
        ['Liver-Kidney Yin deficiency', 'Chronic epilepsy with vertigo, insomnia, forgetfulness, weak back/knees and red scant-coated tongue', 'Zuo Gui Wan', 'Nourish Liver-Kidney Yin and subdue Yang.'],
        ['Spleen-Stomach Qi deficiency', 'Chronic epilepsy with fatigue, poor appetite, loose stools, nausea and pale tongue', 'Liu Jun Zi Tang', 'Strengthen Spleen, boost Qi and transform turbidity.'],
      ],
      rule: 'White-sticky collapse marks Wind-Phlegm; red-yellow-irritable marks Phlegm-Fire; weak back with empty Heat marks Yin; fatigue/digestion with pallor marks Spleen Qi.',
      memory: '<strong>White-sticky—Wind-Phlegm; red-greasy—Fire; red-thin + back—Yin; pale-tired + digestion—Qi.</strong>',
      safety: 'Do not replace antiseizure medication. A seizure lasting 5 minutes, repeated seizures without recovery, first seizure, injury, pregnancy or breathing difficulty is an emergency.',
      source: 'Internal Medicine 1, Handout 8: Epilepsy Patterns.'
    },
    es: {
      intro: 'Las convulsiones requieren diagnóstico neurológico; la comparación MTC organiza signos acompañantes en Flema/Fuego de exceso o deficiencia crónica.',
      rows: [
        ['Obstrucción por Viento-Flema', 'Mareo/opresión antes de caer, convulsiones, espuma y capa blanca pegajosa', 'Ding Xian Wan', 'Expulsar Flema, extinguir Viento y abrir orificios.'],
        ['Flema-Fuego interno', 'Convulsión con irritabilidad, insomnio, flema amarilla difícil, boca amarga, estreñimiento y lengua roja grasosa', 'Long Dan Xie Gan Tang + Di Tan Tang', 'Drenar Fuego, transformar Flema y abrir orificios.'],
        ['Deficiencia Yin de Hígado-Riñón', 'Epilepsia crónica con vértigo, insomnio, olvido, lumbar/rodillas débiles y lengua roja con poca capa', 'Zuo Gui Wan', 'Nutrir Yin de Hígado-Riñón y subyugar Yang.'],
        ['Deficiencia Qi de Bazo-Estómago', 'Epilepsia crónica con fatiga, poco apetito, heces sueltas, náusea y lengua pálida', 'Liu Jun Zi Tang', 'Fortalecer Bazo, aumentar Qi y transformar turbidez.'],
      ],
      rule: 'Colapso blanco-pegajoso marca Viento-Flema; rojo-amarillo-irritable, Flema-Fuego; lumbar débil con Calor vacío, Yin; fatiga/digestión con palidez, Qi de Bazo.',
      memory: '<strong>Blanco-pegajoso—Viento-Flema; rojo-grasoso—Fuego; rojo-delgado + lumbar—Yin; pálido-cansado + digestión—Qi.</strong>',
      safety: 'No sustituir anticonvulsivos. Convulsión de 5 minutos, repetida sin recuperación, primera convulsión, lesión, embarazo o dificultad respiratoria es emergencia.',
      source: 'Internal Medicine 1, Handout 8: Epilepsy Patterns.'
    }
  },
  {
    slug: 'bi-syndrome-patterns', title: { en: 'Bi Syndrome Pattern Differentiation', es: 'Diferenciación del Síndrome Bi' },
    summary: { en: 'Distinguish Wind, Cold, Damp and Heat Bi from chronic stasis-Phlegm and deficiency branches.', es: 'Distingue Bi por Viento, Frío, Humedad y Calor de ramas crónicas por estasis-Flema y deficiencia.' },
    focus: { en: 'Moving versus fixed pain, temperature, swelling and chronic deformity', es: 'Dolor móvil frente a fijo, temperatura, hinchazón y deformidad crónica' },
    search: 'bi syndrome painful obstruction wind cold damp heat blood stasis phlegm deficiency fang feng wu tou yi yi ren bai hu gui zhi tao hong du huo ji sheng',
    en: {
      intro: 'Pain behavior identifies the dominant pathogen; chronic deformity or constitutional weakness signals deeper obstruction or deficiency.',
      rows: [
        ['Wind-dominant Bi', 'Pain moves between joints, flexion/extension difficulty and aversion to Wind', 'Fang Feng Tang', 'Dispel Wind and open channels.'],
        ['Cold-dominant Bi', 'Severe fixed cold pain, worse with Cold and better with Heat; no redness', 'Wu Tou Tang', 'Warm channels and dissipate Cold.'],
        ['Damp-dominant Bi', 'Heavy aching, swelling and numbness, worse in rainy weather', 'Yi Yi Ren Tang', 'Drain Dampness and open channels.'],
        ['Heat-dominant Bi', 'Hot red swollen painful joints with fever, thirst and dark scant urine', 'Bai Hu Jia Gui Zhi Tang', 'Clear Heat and open channels.'],
        ['Blood stasis and Phlegm turbidity', 'Chronic swelling, stiffness, deformity, immobility and purple/dark tongue', 'Tao Hong Yin', 'Dispel stasis, transform Phlegm and relieve pain.'],
        ['Qi-Blood and Liver-Kidney deficiency', 'Prolonged pain with weak back/knees, numbness, dyspnea and pale weak signs', 'Du Huo Ji Sheng Tang or San Bi Tang', 'Supplement deficiency while dispelling Wind-Cold-Damp.'],
      ],
      rule: 'Moving is Wind; severe cold fixed pain is Cold; heavy swollen pain is Damp; red-hot pain is Heat; deformity/purple is stasis-Phlegm; weakness is deficiency.',
      memory: '<strong>Moves—Wind; freezes—Cold; weighs—Damp; burns—Heat; deforms—stasis; weakens—deficiency.</strong>',
      safety: 'A single hot red swollen joint with fever, sudden inability to bear weight, major trauma or rapidly progressive weakness requires urgent evaluation.',
      source: 'Internal Medicine 1, Handout 9: Bi Patterns.'
    },
    es: {
      intro: 'El comportamiento del dolor identifica el patógeno dominante; deformidad crónica o debilidad constitucional indica obstrucción profunda o deficiencia.',
      rows: [
        ['Bi dominante de Viento', 'Dolor migra entre articulaciones, dificultad de flexión/extensión y aversión al Viento', 'Fang Feng Tang', 'Expulsar Viento y abrir canales.'],
        ['Bi dominante de Frío', 'Dolor frío grave y fijo, peor con Frío y mejor con Calor; sin enrojecimiento', 'Wu Tou Tang', 'Calentar canales y disipar Frío.'],
        ['Bi dominante de Humedad', 'Dolor pesado, hinchazón y entumecimiento, peor con lluvia', 'Yi Yi Ren Tang', 'Drenar Humedad y abrir canales.'],
        ['Bi dominante de Calor', 'Articulación caliente, roja, hinchada y dolorosa con fiebre, sed y orina oscura/escasa', 'Bai Hu Jia Gui Zhi Tang', 'Aclarar Calor y abrir canales.'],
        ['Estasis de Sangre y turbidez de Flema', 'Hinchazón crónica, rigidez, deformidad, inmovilidad y lengua oscura/púrpura', 'Tao Hong Yin', 'Eliminar estasis, transformar Flema y aliviar dolor.'],
        ['Deficiencia Qi-Sangre e Hígado-Riñón', 'Dolor prolongado con lumbar/rodillas débiles, entumecimiento, disnea y signos pálidos/débiles', 'Du Huo Ji Sheng Tang o San Bi Tang', 'Suplementar deficiencia y expulsar Viento-Frío-Humedad.'],
      ],
      rule: 'Móvil es Viento; frío fijo grave es Frío; pesado/hinchado es Humedad; rojo/caliente es Calor; deformidad/púrpura es estasis-Flema; debilidad es deficiencia.',
      memory: '<strong>Migra—Viento; congela—Frío; pesa—Humedad; quema—Calor; deforma—estasis; debilita—deficiencia.</strong>',
      safety: 'Una articulación caliente, roja e hinchada con fiebre, incapacidad súbita de apoyar peso, trauma grave o debilidad progresiva requiere evaluación urgente.',
      source: 'Internal Medicine 1, Handout 9: Bi Patterns.'
    }
  },
  {
    slug: 'atony-syndrome-patterns', title: { en: 'Atony Syndrome Pattern Differentiation', es: 'Diferenciación del Síndrome de Atonía' },
    summary: { en: 'Compare Lung Heat fluid injury, Damp-Heat invasion, Spleen-Stomach Qi deficiency and Liver-Kidney Yin depletion.', es: 'Compara lesión de líquidos por Calor de Pulmón, invasión de Humedad-Calor, deficiencia Qi de Bazo-Estómago y agotamiento Yin de Hígado-Riñón.' },
    focus: { en: 'Acute Heat or Dampness versus progressive digestive or Kidney deficiency', es: 'Calor o Humedad agudos frente a deficiencia digestiva o renal progresiva' },
    search: 'atony wei syndrome flaccidity weakness lung heat fluids damp heat spleen stomach qi liver kidney yin qing zao jiu fei er miao shen ling bai zhu hu qian',
    en: {
      intro: 'Flaccidity and limb weakness are sorted by acute dryness/Heat, Damp-Heat heaviness or progressive deficiency signs.',
      rows: [
        ['Lung Heat injures fluids', 'Weak/flaccid limbs with fever, dry cough/throat, thirst, scant urine and dry yellow tongue', 'Qing Zao Jiu Fei Tang', 'Clear Heat, moisten Lung and generate fluids.'],
        ['Damp-Heat invasion', 'Heavy weak limbs with numbness/slight swelling, fever, chest fullness, dark urine and yellow sticky coat', 'Jia Wei Er Miao San', 'Clear Heat and drain Dampness.'],
        ['Spleen-Stomach Qi deficiency', 'Progressive weakness/atrophy with poor appetite, loose stool, fatigue, dyspnea and pale tongue', 'Shen Ling Bai Zhu San', 'Tonify Spleen-Stomach and boost Qi.'],
        ['Liver-Kidney Yin depletion', 'Weak limbs with weak back/knees, vertigo, tinnitus, dry throat and red scant-coated tongue', 'Hu Qian Wan', 'Nourish Liver-Kidney Yin and clear deficient Heat.'],
      ],
      rule: 'Dry febrile signs point to Lung fluids; heavy swollen sticky signs to Damp-Heat; digestion/pallor to Spleen Qi; lumbar weakness/tinnitus/empty Heat to Liver-Kidney Yin.',
      memory: '<strong>Dry-fever—Lung; heavy-sticky—Damp-Heat; thin-tired digestion—Spleen; back-ears-empty Heat—Kidney Yin.</strong>',
      safety: 'Sudden or rapidly progressive weakness, one-sided weakness, breathing/swallowing difficulty, loss of bladder/bowel control or inability to walk is an emergency.',
      source: 'Internal Medicine 1, Handout 9: Atony Patterns.'
    },
    es: {
      intro: 'Flacidez y debilidad de miembros se clasifican por sequedad/Calor agudo, pesadez de Humedad-Calor o signos progresivos de deficiencia.',
      rows: [
        ['Calor de Pulmón lesiona líquidos', 'Miembros débiles/flácidos con fiebre, tos/garganta seca, sed, orina escasa y lengua seca amarilla', 'Qing Zao Jiu Fei Tang', 'Aclarar Calor, humedecer Pulmón y generar líquidos.'],
        ['Invasión de Humedad-Calor', 'Miembros pesados/débiles con entumecimiento o edema leve, fiebre, plenitud torácica, orina oscura y capa amarilla pegajosa', 'Jia Wei Er Miao San', 'Aclarar Calor y drenar Humedad.'],
        ['Deficiencia Qi de Bazo-Estómago', 'Debilidad/atrofia progresiva con poco apetito, heces sueltas, fatiga, disnea y lengua pálida', 'Shen Ling Bai Zhu San', 'Tonificar Bazo-Estómago y aumentar Qi.'],
        ['Agotamiento Yin de Hígado-Riñón', 'Miembros débiles con lumbar/rodillas débiles, vértigo, tinnitus, garganta seca y lengua roja con poca capa', 'Hu Qian Wan', 'Nutrir Yin de Hígado-Riñón y aclarar Calor vacío.'],
      ],
      rule: 'Signos febriles/secos apuntan a líquidos de Pulmón; pesados/hinchados/pegajosos a Humedad-Calor; digestión/palidez a Qi de Bazo; lumbar/tinnitus/Calor vacío a Yin renal.',
      memory: '<strong>Seco-fiebre—Pulmón; pesado-pegajoso—Humedad-Calor; digestión débil—Bazo; lumbar-oídos-Calor vacío—Yin renal.</strong>',
      safety: 'Debilidad súbita o progresiva, unilateral, dificultad al respirar/tragar, pérdida de vejiga/intestino o incapacidad de caminar es emergencia.',
      source: 'Internal Medicine 1, Handout 9: Atony Patterns.'
    }
  },
  {
    slug: 'facial-pain-patterns', title: { en: 'Facial Pain Pattern Differentiation', es: 'Diferenciación del Dolor Facial' },
    summary: { en: 'Separate external Wind-Cold facial pain from Liver-Stomach Fire.', es: 'Separa dolor facial por Viento-Frío externo de Fuego de Hígado-Estómago.' },
    focus: { en: 'Cold relief versus burning pain with red Heat signs', es: 'Alivio con calor frente a dolor ardiente con signos rojos de Calor' },
    search: 'facial pain trigeminal wind cold liver stomach fire chuan xiong cha tiao san xiong zhi shi gao cold heat spasm burning',
    en: {
      intro: 'Temperature and associated eye/nasal signs separate the two presentations in the source.',
      rows: [
        ['External Wind-Cold', 'Sudden facial pain/spasm, worse with Cold, better with Heat, clear runny nose and floating-tight pulse', 'Chuan Xiong Cha Tiao San', 'Dispel Wind-Cold and relieve pain.'],
        ['Liver and Stomach Fire', 'Sudden burning facial pain, irritability, red tearing eyes, thirst, constipation and red-yellow tongue', 'Xiong Zhi Shi Gao Tang', 'Clear Liver-Stomach Fire and relieve pain.'],
      ],
      rule: 'Cold aggravation, Heat relief and clear discharge indicate Wind-Cold; burning pain, red eyes, thirst and constipation indicate Fire.',
      memory: '<strong>Cold hurts and warmth helps—Wind-Cold; face burns and eyes redden—Fire.</strong>',
      safety: 'New facial weakness, speech/vision change, severe sudden headache, rash near the eye, fever/swelling, dental infection or persistent new facial pain requires prompt medical evaluation.',
      source: 'Internal Medicine 1, Handout 9: Facial Pain.'
    },
    es: {
      intro: 'La temperatura y los signos oculares/nasales acompañantes separan las dos presentaciones de la fuente.',
      rows: [
        ['Viento-Frío externo', 'Dolor/espasmo facial súbito, peor con Frío, mejor con Calor, rinorrea clara y pulso flotante-tenso', 'Chuan Xiong Cha Tiao San', 'Expulsar Viento-Frío y aliviar dolor.'],
        ['Fuego de Hígado y Estómago', 'Dolor facial ardiente súbito, irritabilidad, ojos rojos con lagrimeo, sed, estreñimiento y lengua roja/amarilla', 'Xiong Zhi Shi Gao Tang', 'Aclarar Fuego de Hígado-Estómago y aliviar dolor.'],
      ],
      rule: 'Frío agrava, calor alivia y secreción clara indica Viento-Frío; dolor ardiente, ojos rojos, sed y estreñimiento indica Fuego.',
      memory: '<strong>Frío duele y calor ayuda—Viento-Frío; cara arde y ojos enrojecen—Fuego.</strong>',
      safety: 'Debilidad facial nueva, cambio de habla/visión, cefalea súbita grave, erupción cerca del ojo, fiebre/hinchazón, infección dental o dolor facial nuevo persistente requiere evaluación pronta.',
      source: 'Internal Medicine 1, Handout 9: Facial Pain.'
    }
  },
  {
    slug: 'sprain-patterns', title: { en: 'Sprain Clinical Comparison', es: 'Comparación Clínica del Esguince' },
    summary: { en: 'Recognize the Qi-and-Blood stagnation presentation while screening for fracture or severe soft-tissue injury.', es: 'Reconoce la presentación por estancamiento de Qi y Sangre mientras se descarta fractura o lesión grave de tejidos blandos.' },
    focus: { en: 'Local swelling, discoloration, pain and restricted movement', es: 'Hinchazón local, decoloración, dolor y movimiento restringido' },
    search: 'sprain strain trauma qi blood stagnation swelling purple restricted movement shu jin huo xue tang',
    en: {
      intro: 'The source gives one principal presentation; the exam task is to recognize local traumatic Qi-and-Blood stagnation and distinguish it from injuries requiring imaging.',
      rows: [
        ['Qi and Blood stagnation', 'Local distention, swelling, pain, red or dark-purple discoloration and restricted movement', 'Shu Jin Huo Xue Tang', 'Move Qi and Blood, soothe sinews and open channels.'],
      ],
      rule: 'After trauma, swelling plus red-purple discoloration and limited motion identifies local Qi-and-Blood stagnation.',
      memory: '<strong>Trauma + swollen + purple + restricted = Qi and Blood stagnation.</strong>',
      safety: 'Inability to bear weight, deformity, bony tenderness, loss of sensation/circulation, severe swelling or high-energy trauma requires prompt medical assessment and possible imaging.',
      source: 'Internal Medicine 1, Handout 9: Sprain.'
    },
    es: {
      intro: 'La fuente presenta un mecanismo principal; la tarea de examen es reconocer estancamiento traumático local de Qi-Sangre y diferenciar lesiones que requieren imagen.',
      rows: [
        ['Estancamiento de Qi y Sangre', 'Distensión, hinchazón, dolor, color rojo o púrpura oscuro y movimiento restringido', 'Shu Jin Huo Xue Tang', 'Mover Qi y Sangre, relajar tendones y abrir canales.'],
      ],
      rule: 'Tras trauma, hinchazón más color rojo-púrpura y movimiento limitado identifica estancamiento local de Qi-Sangre.',
      memory: '<strong>Trauma + hinchado + púrpura + restringido = estancamiento de Qi y Sangre.</strong>',
      safety: 'No poder apoyar peso, deformidad, dolor óseo, pérdida de sensibilidad/circulación, hinchazón grave o trauma de alta energía requiere evaluación e imagen.',
      source: 'Internal Medicine 1, Handout 9: Sprain.'
    }
  },
  {
    slug: 'crick-neck-patterns', title: { en: 'Crick in the Neck Pattern Differentiation', es: 'Diferenciación de Tortícolis y Rigidez Cervical' },
    summary: { en: 'Separate local Qi-Blood stagnation from an external Wind-Cold presentation.', es: 'Separa estancamiento local de Qi-Sangre de una presentación externa de Viento-Frío.' },
    focus: { en: 'Local palpable tightness versus systemic exterior signs', es: 'Tensión local palpable frente a signos exteriores sistémicos' },
    search: 'crick neck stiff neck torticollis qi blood stagnation wind cold shu jin huo xue ge gen tang fever chills headache',
    en: {
      intro: 'Both branches restrict neck movement; exterior symptoms are the decisive difference.',
      rows: [
        ['Qi and Blood stagnation', 'Neck stiffness/pain, restricted motion, tight muscles and sometimes palpable local knots', 'Shu Jin Huo Xue Tang', 'Move Qi and Blood, soothe sinews and harmonize channels.'],
        ['External Wind-Cold', 'Stiff painful neck plus aversion to Cold, slight fever, headache and floating-tight pulse', 'Ge Gen Tang', 'Dispel Wind-Cold and relax the muscle layer.'],
      ],
      rule: 'Purely local tight painful restriction suggests stagnation; chills, slight fever and headache add an exterior Wind-Cold pattern.',
      memory: '<strong>Local knot—stagnation; stiff neck + chills/headache—Wind-Cold.</strong>',
      safety: 'Neck stiffness with high fever, severe headache, confusion, rash, neurologic deficit or recent major trauma is an emergency.',
      source: 'Internal Medicine 1, Handout 9: Crick in the Neck.'
    },
    es: {
      intro: 'Ambas ramas restringen el cuello; los síntomas exteriores son la diferencia decisiva.',
      rows: [
        ['Estancamiento de Qi y Sangre', 'Rigidez/dolor cervical, movimiento restringido, músculos tensos y a veces nudos locales palpables', 'Shu Jin Huo Xue Tang', 'Mover Qi y Sangre, relajar tendones y armonizar canales.'],
        ['Viento-Frío externo', 'Cuello rígido/doloroso más aversión al Frío, fiebre leve, cefalea y pulso flotante-tenso', 'Ge Gen Tang', 'Expulsar Viento-Frío y relajar capa muscular.'],
      ],
      rule: 'Restricción dolorosa puramente local sugiere estancamiento; escalofríos, fiebre leve y cefalea añaden Viento-Frío externo.',
      memory: '<strong>Nudo local—estancamiento; cuello rígido + escalofríos/cefalea—Viento-Frío.</strong>',
      safety: 'Rigidez cervical con fiebre alta, cefalea grave, confusión, erupción, déficit neurológico o trauma mayor reciente es emergencia.',
      source: 'Internal Medicine 1, Handout 9: Crick in the Neck.'
    }
  },
  {
    slug: 'internal-damage-fever-patterns', title: { en: 'Internal Damage Fever Pattern Differentiation', es: 'Diferenciación de Fiebre por Daño Interno' },
    summary: { en: 'Compare fever from Qi stagnation, Blood stasis, Qi deficiency, Blood deficiency and Yin deficiency.', es: 'Compara fiebre por estancamiento de Qi, estasis de Sangre, deficiencia de Qi, deficiencia de Sangre y deficiencia de Yin.' },
    focus: { en: 'Timing, emotional triggers, exertion, fixed pain and empty-Heat signs', es: 'Horario, detonantes emocionales, esfuerzo, dolor fijo y signos de Calor vacío' },
    search: 'internal damage fever qi stagnation blood stasis qi deficiency blood deficiency yin deficiency dan zhi xiao yao xue fu zhu yu bu zhong yi qi gui pi qing gu san',
    en: {
      intro: 'The comparison concerns chronic or recurrent feverish sensations after external infection and other biomedical causes have been evaluated.',
      rows: [
        ['Qi stagnation fever', 'Feverishness changes with mood; depression/irritability, sighing, hypochondriac distention and wiry-rapid pulse', 'Dan Zhi Xiao Yao San', 'Soothe Liver, resolve constraint and clear Heat.'],
        ['Blood stasis fever', 'Afternoon/night tidal fever with fixed pain or masses, sallow scaly skin and dark-purple tongue', 'Xue Fu Zhu Yu Tang', 'Invigorate Blood, dispel stasis and clear Heat.'],
        ['Qi deficiency fever', 'Fever worse after exertion with fatigue, dyspnea, spontaneous sweat, poor appetite and weak pulse', 'Bu Zhong Yi Qi Tang', 'Tonify Qi and strengthen Spleen.'],
        ['Blood deficiency fever', 'Mild fever with vertigo, fatigue, palpitations, pale lips/nails and pale tongue', 'Gui Pi Tang', 'Benefit Qi and nourish Blood.'],
        ['Yin deficiency fever', 'Afternoon/night tidal fever, five-center Heat, night sweat, dry mouth and red scant-coated tongue', 'Qing Gu San', 'Nourish Yin and clear deficient Heat.'],
      ],
      rule: 'Mood links to Qi; fixed purple pain to stasis; exertion/sweat to Qi deficiency; pallor/palpitations to Blood; night Heat/dryness to Yin.',
      memory: '<strong>Mood—Qi; fixed-purple—stasis; exertion—Qi deficiency; pale-palpitations—Blood; night-dry—Yin.</strong>',
      safety: 'Persistent or recurrent fever requires medical evaluation for infection, inflammation, malignancy, medication reaction and endocrine disease. Confusion, stiff neck, dyspnea or severe dehydration is urgent.',
      source: 'Internal Medicine 1, Handout 10: Internal Damage Fever.'
    },
    es: {
      intro: 'La comparación trata sensación febril crónica o recurrente después de evaluar infección externa y otras causas biomédicas.',
      rows: [
        ['Fiebre por estancamiento de Qi', 'Cambia con el ánimo; depresión/irritabilidad, suspiros, distensión hipocondríaca y pulso de cuerda-rápido', 'Dan Zhi Xiao Yao San', 'Suavizar Hígado, resolver constreñimiento y aclarar Calor.'],
        ['Fiebre por estasis de Sangre', 'Fiebre vespertina/nocturna con dolor fijo o masas, piel cetrina/escamosa y lengua púrpura oscura', 'Xue Fu Zhu Yu Tang', 'Vigorizar Sangre, eliminar estasis y aclarar Calor.'],
        ['Fiebre por deficiencia de Qi', 'Peor tras esfuerzo con fatiga, disnea, sudor espontáneo, poco apetito y pulso débil', 'Bu Zhong Yi Qi Tang', 'Tonificar Qi y fortalecer Bazo.'],
        ['Fiebre por deficiencia de Sangre', 'Fiebre leve con vértigo, fatiga, palpitaciones, labios/uñas pálidos y lengua pálida', 'Gui Pi Tang', 'Beneficiar Qi y nutrir Sangre.'],
        ['Fiebre por deficiencia de Yin', 'Fiebre vespertina/nocturna, Calor de cinco centros, sudor nocturno, boca seca y lengua roja con poca capa', 'Qing Gu San', 'Nutrir Yin y aclarar Calor vacío.'],
      ],
      rule: 'Ánimo enlaza con Qi; dolor fijo/púrpura con estasis; esfuerzo/sudor con Qi deficiente; palidez/palpitaciones con Sangre; Calor nocturno/sequedad con Yin.',
      memory: '<strong>Ánimo—Qi; fijo-púrpura—estasis; esfuerzo—Qi deficiente; pálido-palpitaciones—Sangre; noche-seco—Yin.</strong>',
      safety: 'Fiebre persistente o recurrente requiere evaluación por infección, inflamación, cáncer, reacción farmacológica o enfermedad endocrina. Confusión, rigidez cervical, disnea o deshidratación grave es urgente.',
      source: 'Internal Medicine 1, Handout 10: Internal Damage Fever.'
    }
  },
  {
    slug: 'parasitic-worm-patterns', title: { en: 'Parasitic Worm Clinical Comparison', es: 'Comparación Clínica de Parásitos Intestinales' },
    summary: { en: 'Compare roundworm, pinworm, tapeworm and biliary roundworm presentations.', es: 'Compara presentaciones por lombriz intestinal, oxiuros, tenia y ascariasis biliar.' },
    focus: { en: 'Pain location, nocturnal itching, stool findings and acute biliary pain', es: 'Localización del dolor, prurito nocturno, hallazgos en heces y dolor biliar agudo' },
    search: 'parasites worms roundworm pinworm tapeworm biliary ascaris wu mei wan shi jun zi da huang qu di tang nan gua zi bing lang stool itching',
    en: {
      intro: 'Symptoms suggest a parasite type, but stool testing, tape testing or imaging establishes the biomedical diagnosis and guides antiparasitic treatment.',
      rows: [
        ['Roundworm presentation', 'Intermittent umbilical pain, unusual cravings, emaciation, nose itching, sleep grinding and worms in stool', 'Wu Mei Wan or Shi Jun Zi San', 'Quiet/expel roundworms and support Spleen-Stomach.'],
        ['Pinworm presentation', 'Intense nocturnal anal itching, restless sleep and small white worms around the anus, usually in children', 'Shi Jun Zi Da Huang Fen', 'Expel worms and relieve itching.'],
        ['Tapeworm presentation', 'Mild abdominal discomfort, anal itching, diarrhea and visible tapeworm segments in stool', 'Qu Di Tang', 'Expel worms and rectify digestion.'],
        ['Biliary roundworm presentation', 'Sudden severe epigastric/right hypochondriac pain radiating to back/shoulder with nausea and vomiting', 'Wu Mei Wan or Dan Dao Qu Hui Tang', 'Expel worms and relieve pain after urgent evaluation.'],
      ],
      rule: 'Umbilical symptoms suggest roundworm; night anal itch pinworm; segments tapeworm; acute right-upper abdominal pain biliary migration.',
      memory: '<strong>Navel—roundworm; night itch—pinworm; segments—tapeworm; right-upper attack—biliary.</strong>',
      safety: 'Suspected parasites require diagnostic testing and standard antiparasitic treatment. Severe abdominal/right-upper-quadrant pain, jaundice, fever, persistent vomiting or obstruction is urgent.',
      source: 'Internal Medicine 1, Handout 10: Parasitic Worm Patterns.'
    },
    es: {
      intro: 'Los síntomas sugieren el tipo, pero pruebas de heces, cinta adhesiva o imagen establecen el diagnóstico biomédico y guían el antiparasitario.',
      rows: [
        ['Presentación por lombriz intestinal', 'Dolor umbilical intermitente, antojos inusuales, adelgazamiento, prurito nasal, rechinar nocturno y lombrices en heces', 'Wu Mei Wan o Shi Jun Zi San', 'Calmar/expulsar lombrices y apoyar Bazo-Estómago.'],
        ['Presentación por oxiuros', 'Prurito anal nocturno intenso, sueño inquieto y pequeños gusanos blancos perianales, usualmente en niños', 'Shi Jun Zi Da Huang Fen', 'Expulsar gusanos y aliviar prurito.'],
        ['Presentación por tenia', 'Molestia abdominal leve, prurito anal, diarrea y segmentos visibles en heces', 'Qu Di Tang', 'Expulsar parásitos y rectificar digestión.'],
        ['Presentación por ascariasis biliar', 'Dolor epigástrico/hipocondrio derecho súbito y grave irradiado a espalda/hombro con náusea y vómito', 'Wu Mei Wan o Dan Dao Qu Hui Tang', 'Expulsar gusanos y aliviar dolor tras evaluación urgente.'],
      ],
      rule: 'Síntomas umbilicales sugieren lombriz; prurito anal nocturno oxiuros; segmentos tenia; ataque abdominal superior derecho migración biliar.',
      memory: '<strong>Ombligo—lombriz; prurito nocturno—oxiuros; segmentos—tenia; ataque superior derecho—biliar.</strong>',
      safety: 'Sospecha de parásitos requiere pruebas y antiparasitario estándar. Dolor abdominal/hipocondrio derecho grave, ictericia, fiebre, vómito persistente u obstrucción es urgente.',
      source: 'Internal Medicine 1, Handout 10: Parasitic Worm Patterns.'
    }
  },
  {
    slug: 'pulmonary-consumption-patterns', title: { en: 'Pulmonary Consumption (Tuberculosis) Comparison', es: 'Comparación de Consunción Pulmonar (Tuberculosis)' },
    summary: { en: 'Compare Lung Yin, Yin with empty Heat, Qi-Yin and combined Yin-Yang deficiency presentations.', es: 'Compara presentaciones por deficiencia Yin de Pulmón, Yin con Calor vacío, Qi-Yin y deficiencia combinada Yin-Yang.' },
    focus: { en: 'Dry cough, bleeding, empty Heat and progressive systemic deficiency', es: 'Tos seca, sangrado, Calor vacío y deficiencia sistémica progresiva' },
    search: 'pulmonary consumption tuberculosis lung yin empty heat qi yin yin yang bai he gu jin qin jiao bie jia bao zhen bu tian cough blood night sweat',
    en: {
      intro: 'This is a historical TCM framework for pulmonary consumption; suspected tuberculosis requires immediate biomedical testing and public-health treatment.',
      rows: [
        ['Lung Yin deficiency', 'Dry cough, scant/blood-streaked phlegm, afternoon fever, flushed cheeks, night sweat and dry throat', 'Bai He Gu Jin Tang', 'Nourish Yin, moisten Lung and relieve cough.'],
        ['Yin deficiency with empty Heat', 'Choking cough, yellow scant phlegm, bright-red blood, steaming-bone fever, heavy night sweat and red scant-coated tongue', 'Qin Jiao Bie Jia San', 'Nourish Yin and diminish deficient Fire.'],
        ['Qi and Yin deficiency', 'Weak cough/voice, dyspnea, pale-red blood, afternoon fever, spontaneous/night sweat, fatigue and poor appetite', 'Bao Zhen Tang', 'Benefit Qi and nourish Yin.'],
        ['Yin and Yang deficiency', 'Advanced cough/bleeding with dyspnea, edema, loose stool, spontaneous sweating and reproductive deficiency signs', 'Bu Tian Da Zao Wan', 'Nourish Yin, warm Yang and support severe deficiency.'],
      ],
      rule: 'Dry cough begins with Lung Yin; intense empty Heat/bright bleeding marks deeper Yin depletion; weak voice/fatigue adds Qi; edema/cold digestive-reproductive failure adds Yang.',
      memory: '<strong>Dry—Lung Yin; burning/bleeding—empty Heat; weak voice—Qi-Yin; edema/cold failure—Yin-Yang.</strong>',
      safety: 'Cough over 3 weeks, coughing blood, night sweats, fever, weight loss or TB exposure requires prompt testing. Active TB needs prescribed multidrug therapy and public-health precautions.',
      source: 'Internal Medicine 1, Handout 10: Pulmonary Consumption.'
    },
    es: {
      intro: 'Este es un marco histórico MTC para consunción pulmonar; sospecha de tuberculosis requiere pruebas biomédicas inmediatas y tratamiento de salud pública.',
      rows: [
        ['Deficiencia Yin de Pulmón', 'Tos seca, flema escasa/con sangre, fiebre vespertina, mejillas rojas, sudor nocturno y garganta seca', 'Bai He Gu Jin Tang', 'Nutrir Yin, humedecer Pulmón y aliviar tos.'],
        ['Deficiencia Yin con Calor vacío', 'Tos sofocante, flema amarilla escasa, sangre roja brillante, fiebre de huesos, sudor nocturno intenso y lengua roja con poca capa', 'Qin Jiao Bie Jia San', 'Nutrir Yin y disminuir Fuego vacío.'],
        ['Deficiencia de Qi y Yin', 'Tos/voz débil, disnea, sangre rojo pálido, fiebre vespertina, sudor espontáneo/nocturno, fatiga y poco apetito', 'Bao Zhen Tang', 'Beneficiar Qi y nutrir Yin.'],
        ['Deficiencia de Yin y Yang', 'Tos/sangrado avanzado con disnea, edema, heces sueltas, sudor espontáneo y signos de deficiencia reproductiva', 'Bu Tian Da Zao Wan', 'Nutrir Yin, calentar Yang y apoyar deficiencia grave.'],
      ],
      rule: 'Tos seca inicia con Yin pulmonar; Calor vacío intenso/sangrado brillante marca Yin profundo; voz débil/fatiga añade Qi; edema/frío y fallo digestivo-reproductivo añade Yang.',
      memory: '<strong>Seco—Yin pulmonar; arde/sangra—Calor vacío; voz débil—Qi-Yin; edema/fallo frío—Yin-Yang.</strong>',
      safety: 'Tos mayor de 3 semanas, sangre, sudores nocturnos, fiebre, pérdida de peso o exposición a TB requiere pruebas prontas. TB activa necesita terapia multidroga prescrita y precauciones de salud pública.',
      source: 'Internal Medicine 1, Handout 10: Pulmonary Consumption.'
    }
  },
  {
    slug: 'malaria-patterns', title: { en: 'Malaria Clinical Comparison', es: 'Comparación Clínica de la Malaria' },
    summary: { en: 'Separate a typical cyclic Shaoyang presentation from prolonged taxation malaria with Qi-Blood deficiency.', es: 'Separa una presentación cíclica Shaoyang típica de malaria prolongada por agotamiento con deficiencia Qi-Sangre.' },
    focus: { en: 'Cyclic chills-fever versus recurrent attacks with constitutional weakness', es: 'Ciclos de escalofrío-fiebre frente a ataques recurrentes con debilidad constitucional' },
    search: 'malaria cyclic chills fever shaoyang taxation deficiency xiao chai hu tang he ren yin mosquito travel',
    en: {
      intro: 'The classic comparison distinguishes an active cyclic excess from a prolonged deficient recurrence; neither presentation can confirm malaria without laboratory testing.',
      rows: [
        ['Typical malaria presentation', 'Cyclic yawning/fatigue, chills followed by fever, headache, red eyes, thirst and sweating', 'Xiao Chai Hu Tang', 'Harmonize Shaoyang and dispel the pathogen.'],
        ['Taxation malaria presentation', 'Prolonged recurrent chills-fever triggered by strain with fatigue, dyspnea, poor appetite, emaciation and pale weak signs', 'He Ren Yin', 'Boost Qi, nourish Blood and dispel residual pathogen.'],
      ],
      rule: 'A strong orderly chill-fever-sweat cycle suggests the typical branch; recurrence with exertion and pale constitutional weakness suggests taxation.',
      memory: '<strong>Clear cycle—typical; weak recurrence after strain—taxation.</strong>',
      safety: 'Fever after travel to a malaria area is a medical emergency until excluded. Same-day blood testing and prompt antimalarial treatment are essential; confusion, breathing difficulty or severe weakness needs emergency care.',
      source: 'Internal Medicine 1, Handout 10: Malarial Patterns.'
    },
    es: {
      intro: 'La comparación clásica distingue exceso cíclico activo de recurrencia deficiente prolongada; ninguna presentación confirma malaria sin laboratorio.',
      rows: [
        ['Presentación de malaria típica', 'Ciclos de bostezos/fatiga, escalofríos seguidos de fiebre, cefalea, ojos rojos, sed y sudor', 'Xiao Chai Hu Tang', 'Armonizar Shaoyang y expulsar patógeno.'],
        ['Presentación de malaria por agotamiento', 'Escalofrío-fiebre recurrente y prolongado tras esfuerzo con fatiga, disnea, poco apetito, adelgazamiento y signos pálidos/débiles', 'He Ren Yin', 'Aumentar Qi, nutrir Sangre y expulsar patógeno residual.'],
      ],
      rule: 'Ciclo fuerte y ordenado escalofrío-fiebre-sudor sugiere rama típica; recurrencia por esfuerzo con debilidad pálida sugiere agotamiento.',
      memory: '<strong>Ciclo claro—típica; recurrencia débil tras esfuerzo—agotamiento.</strong>',
      safety: 'Fiebre tras viaje a zona de malaria es emergencia hasta descartarla. Prueba sanguínea el mismo día y antipalúdico pronto son esenciales; confusión, disnea o debilidad grave requiere urgencias.',
      source: 'Internal Medicine 1, Handout 10: Malarial Patterns.'
    }
  }
];

function esc(value) {
  return String(value).replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;');
}

function detailPage(item, language) {
  const es = language === 'es';
  const data = item[language];
  const alternate = `${item.slug}${es ? '' : '-es'}.html`;
  const index = `pathologies${es ? '-es' : ''}.html`;
  const rows = data.rows.map(row => `<tr><th scope="row">${esc(row[0])}</th><td>${esc(row[1])}</td><td><strong>${esc(row[2])}</strong></td><td>${esc(row[3])}</td></tr>`).join('\n');
  return `<!doctype html><html lang="${es ? 'es' : 'en'}"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="description" content="${esc(item.summary[language])}"><title>${esc(item.title[language])} | AcupunctureTests</title><link rel="alternate" hreflang="${es ? 'en' : 'es'}" href="${alternate}"><link rel="stylesheet" href="comparisons.css?v=${cssVersion}"></head><body>
<header class="comparison-topbar"><div class="comparison-shell comparison-topbar__inner" style="flex-wrap:wrap;padding:10px 0"><a class="comparison-brand" href="${index}"><span class="comparison-brand__mark" aria-hidden="true">☯</span><span>${es ? 'Comparaciones de Patologías' : 'Pathology Comparisons'}</span></a><span style="display:flex;gap:8px;flex-wrap:wrap"><a class="comparison-back" href="${alternate}">${es ? 'English' : 'Español'}</a><a class="comparison-back" href="${index}">${es ? '← Volver a Patologías' : '← Back to Pathologies'}</a></span></div></header>
<main class="comparison-shell"><article class="study-note"><p class="comparison-eyebrow">${es ? 'Comparación por patología o síntoma' : 'Pathology or symptom comparison'}</p><h1 class="study-note__title">${esc(item.title[language])}</h1>
<section class="study-note__block"><h2 class="study-note__heading">${es ? 'Orientación diagnóstica' : 'Diagnostic orientation'}</h2><table class="study-note__focus"><tbody><tr><th scope="row">${es ? 'Idea central' : 'Core idea'}</th><td>${esc(data.intro)}</td></tr><tr><th scope="row">${es ? 'Enfoque de examen' : 'Exam focus'}</th><td>${esc(item.focus[language])}</td></tr></tbody></table></section>
<section class="study-note__block"><h2 class="study-note__heading study-note__heading--orange">${es ? 'Diferenciación comparativa' : 'Comparative differentiation'}</h2><table><thead><tr><th>${es ? 'Pattern o presentación' : 'Pattern or presentation'}</th><th>${es ? 'Pistas decisivas' : 'Decisive clues'}</th><th>${es ? 'Fórmula/hierba representativa' : 'Representative formula/herb'}</th><th>${es ? 'Estrategia' : 'Strategy'}</th></tr></thead><tbody>${rows}</tbody></table></section>
<section class="study-note__block"><h2 class="study-note__heading study-note__heading--purple">${es ? 'Regla de estudio' : 'Study rule'}</h2><table class="study-note__focus"><tbody><tr><th>${es ? 'Cómo distinguir' : 'How to distinguish'}</th><td>${data.rule}</td></tr><tr><th>${es ? 'Memoria' : 'Memory cue'}</th><td>${data.memory}</td></tr><tr><th>${es ? 'Seguridad' : 'Safety'}</th><td>${esc(data.safety)}</td></tr><tr><th>${es ? 'Fuente' : 'Source'}</th><td>${esc(data.source)}</td></tr></tbody></table></section>
</article></main><footer class="comparison-footer"><div class="comparison-shell">${es ? 'Patologías y síntomas separados de los diagnósticos de pattern de MTC.' : 'Pathologies and symptoms kept separate from TCM pattern diagnoses.'}</div></footer></body></html>`;
}

function indexPage(language) {
  const es = language === 'es';
  const cards = pathologies.map(item => `<article class="comparison-card" data-type="pathologies" data-search="${esc(item.search)}"><span class="comparison-card__type">${es ? 'Patología / síntoma' : 'Pathology / symptom'}</span><h3>${esc(item.title[language])}</h3><p>${esc(item.summary[language])}</p><div class="comparison-card__category"><strong>${es ? 'Enfoque de examen:' : 'Exam focus:'}</strong><br>${esc(item.focus[language])}</div><a class="comparison-card__open" href="${item.slug}${es ? '-es' : ''}.html">${es ? 'Abrir Nota →' : 'Open Study Note →'}</a></article>`).join('\n');
  return `<!doctype html><html lang="${es ? 'es' : 'en'}"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="description" content="${es ? 'Comparaciones de patologías y síntomas para medicina china.' : 'Pathology- and symptom-focused Chinese medicine comparison notes.'}"><title>${es ? 'Comparaciones de Patologías' : 'Pathology Comparisons'} | AcupunctureTests</title><link rel="alternate" hreflang="${es ? 'en' : 'es'}" href="pathologies${es ? '' : '-es'}.html"><link rel="stylesheet" href="comparisons.css?v=${cssVersion}"><script defer src="comparisons.js?v=20261025-1"></script></head><body>
<header class="comparison-topbar"><div class="comparison-shell comparison-topbar__inner"><a class="comparison-brand" href="/my/"><span class="comparison-brand__mark" aria-hidden="true">☯</span><span>AcupunctureTests</span></a><span style="display:flex;gap:8px;flex-wrap:wrap"><a class="comparison-back" href="pathologies${es ? '' : '-es'}.html">${es ? 'English' : 'Español'}</a><a class="comparison-back" href="/my/">${es ? '← Dashboard' : '← Back to Dashboard'}</a></span></div></header>
<main class="comparison-shell"><section class="comparison-hero"><p class="comparison-eyebrow">${es ? 'Grupo 3 · Patologías y síntomas' : 'Group 3 · Pathologies and symptoms'}</p><h1>${es ? 'Comparaciones de Patologías' : 'Pathology Comparisons'}</h1><p>${es ? 'Compara una enfermedad o síntoma por sus posibles presentaciones, pistas clínicas y selección representativa de fórmulas.' : 'Compare a disease or symptom by its possible presentations, clinical clues and representative formula selection.'}</p></section>
<nav class="comparison-collection-links" aria-label="${es ? 'Grupos de estudio' : 'Study groups'}"><a href="index${es ? '-es' : ''}.html">${es ? 'Comparaciones de Hierbas' : 'Single Herbs Comparisons'}</a><a href="formulas${es ? '-es' : ''}.html">${es ? 'Comparaciones de Fórmulas' : 'Formula Comparisons'}</a><a aria-current="page" href="pathologies${es ? '-es' : ''}.html">${es ? 'Comparaciones de Patologías' : 'Pathology Comparisons'}</a><a href="patterns${es ? '-es' : ''}.html">${es ? 'Comparaciones de Patterns' : 'Pattern Comparisons'}</a><a href="families${es ? '-es' : ''}.html">${es ? 'Familias de Fórmulas' : 'Formula Families'}</a></nav>
<section class="comparison-tools comparison-tools--search-only"><label><span class="sr-only">${es ? 'Buscar' : 'Search'}</span><input class="comparison-search" type="search" placeholder="${es ? 'Buscar patologías, síntomas o pistas' : 'Search pathologies, symptoms or clues'}" data-comparison-search></label></section><section><header class="comparison-summary"><h2>${es ? 'Notas disponibles' : 'Available notes'}</h2><p><span data-visible-count>${pathologies.length}</span> ${es ? 'comparaciones clínicas' : 'clinical comparisons'}</p></header><div class="comparison-grid" data-comparison-grid>${cards}</div><p class="comparison-empty" data-comparison-empty hidden>${es ? 'No hay patologías coincidentes.' : 'No Pathology Comparisons match this search.'}</p></section></main><footer class="comparison-footer"><div class="comparison-shell">${es ? 'Patologías y síntomas separados de los patterns diagnósticos de MTC.' : 'Pathologies and symptoms kept separate from TCM diagnostic patterns.'}</div></footer></body></html>`;
}

for (const item of pathologies) {
  writeFileSync(resolve(root, `${item.slug}.html`), detailPage(item, 'en'));
  writeFileSync(resolve(root, `${item.slug}-es.html`), detailPage(item, 'es'));
}
writeFileSync(resolve(root, 'pathologies.html'), indexPage('en'));
writeFileSync(resolve(root, 'pathologies-es.html'), indexPage('es'));
console.log(`Generated ${pathologies.length * 2} pathology pages and 2 indexes`);
