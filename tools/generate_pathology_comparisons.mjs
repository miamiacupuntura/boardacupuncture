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
<nav class="comparison-collection-links" aria-label="${es ? 'Grupos de estudio' : 'Study groups'}"><a href="index.html">${es ? 'Comparaciones de Hierbas' : 'Single Herbs Comparisons'}</a><a href="formulas${es ? '-es' : ''}.html">${es ? 'Comparaciones de Fórmulas' : 'Formula Comparisons'}</a><a aria-current="page" href="pathologies${es ? '-es' : ''}.html">${es ? 'Comparaciones de Patologías' : 'Pathology Comparisons'}</a><a href="patterns${es ? '-es' : ''}.html">${es ? 'Comparaciones de Patterns' : 'Pattern Comparisons'}</a><a href="families${es ? '-es' : ''}.html">${es ? 'Familias de Fórmulas' : 'Formula Families'}</a></nav>
<section class="comparison-tools comparison-tools--search-only"><label><span class="sr-only">${es ? 'Buscar' : 'Search'}</span><input class="comparison-search" type="search" placeholder="${es ? 'Buscar patologías, síntomas o pistas' : 'Search pathologies, symptoms or clues'}" data-comparison-search></label></section><section><header class="comparison-summary"><h2>${es ? 'Notas disponibles' : 'Available notes'}</h2><p><span data-visible-count>${pathologies.length}</span> ${es ? 'comparaciones clínicas' : 'clinical comparisons'}</p></header><div class="comparison-grid" data-comparison-grid>${cards}</div><p class="comparison-empty" data-comparison-empty hidden>${es ? 'No hay patologías coincidentes.' : 'No Pathology Comparisons match this search.'}</p></section></main><footer class="comparison-footer"><div class="comparison-shell">${es ? 'Patologías y síntomas separados de los patterns diagnósticos de MTC.' : 'Pathologies and symptoms kept separate from TCM diagnostic patterns.'}</div></footer></body></html>`;
}

for (const item of pathologies) {
  writeFileSync(resolve(root, `${item.slug}.html`), detailPage(item, 'en'));
  writeFileSync(resolve(root, `${item.slug}-es.html`), detailPage(item, 'es'));
}
writeFileSync(resolve(root, 'pathologies.html'), indexPage('en'));
writeFileSync(resolve(root, 'pathologies-es.html'), indexPage('es'));
console.log(`Generated ${pathologies.length * 2} pathology pages and 2 indexes`);
