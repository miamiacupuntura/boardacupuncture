import { readFileSync, writeFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '../study-guide/comparisons');
const cssVersion = '20261026-1';

// Instructor audit note: five FWB 12.1 activity headings name Gui Zhi Fu Ling Wan,
// while their keyed ingredients, patterns and symptoms identify Gui Zhi Tang.
// Student-facing pages silently use the internally consistent Gui Zhi Tang reading.

const cards = [
  {
    slug: 'ma-huang-tang-vs-gui-zhi-tang',
    title: 'Ma Huang Tang vs. Gui Zhi Tang',
    category: 'Formulas that Release the Exterior',
    summary: 'Separate an Exterior Wind-Cold excess pattern without sweating from an Exterior deficiency pattern with spontaneous sweating.',
    search: 'ma huang tang gui zhi tang ephedra cinnamon twig exterior wind cold excess deficiency sweating wheezing ying wei',
    en: {
      eyebrow: 'Formula comparison',
      names: ['Ma Huang Tang', 'Gui Zhi Tang'],
      shared: 'Both release Exterior Wind-Cold and may present with fever, chills or aversion to cold, headache, body aches and a floating pulse.',
      fast: ['Exterior excess: closed pores, no sweating, stronger chills and wheezing.', 'Exterior deficiency: spontaneous sweating and aversion to wind from disharmony of the Nutritive and Defensive levels.'],
      rows: [
        ['Primary action', 'Strongly induces sweating, releases the Exterior and disseminates Lung Qi.', 'Releases the Exterior, harmonizes Ying and Wei, and regulates the muscles.'],
        ['Decisive sweat clue', 'Absence of sweating.', 'Spontaneous sweating.'],
        ['Respiratory clue', 'Cough or wheezing is prominent.', 'Respiratory signs are not the defining clue.'],
        ['Pulse', 'Floating and tight.', 'Floating and moderate or frail.'],
        ['Exam choice', 'Choose when the pathogen is strong and the Exterior is tightly constrained.', 'Choose when Exterior deficiency and disharmony permit leakage of sweat.']
      ],
      why: 'The central distinction is not simply “strong versus mild.” It is a closed Exterior excess pattern versus a deficient, disharmonized Exterior that cannot secure the pores.',
      memory: '<strong>Ma Huang Tang closes sweat out;</strong> <strong>Gui Zhi Tang lets sweat leak out.</strong>',
      safety: 'These formulas are not interchangeable. Ma Huang Tang contains Ma Huang and requires cardiovascular and medication screening and qualified professional supervision.',
      prompts: ['Which formula matches Wind-Cold with no sweating and a floating-tight pulse?', 'Which formula harmonizes Ying and Wei?', 'Which formula is selected when spontaneous sweating is the decisive clue?'],
      source: 'Source bank: FWB 1.2, Comparison Formulas to Release the Exterior; corroborated with the Classic Formulas profiles and <em>Chinese Herbal Medicine: Formulas &amp; Strategies</em>, 2nd ed.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas',
      names: ['Ma Huang Tang', 'Gui Zhi Tang'],
      shared: 'Ambas liberan Viento-Frío del Exterior y pueden presentar fiebre, escalofríos o aversión al frío, cefalea, dolor corporal y pulso flotante.',
      fast: ['Exceso Exterior: poros cerrados, ausencia de sudor, escalofríos más fuertes y sibilancias.', 'Deficiencia Exterior: sudoración espontánea y aversión al viento por desarmonía entre los niveles Nutritivo y Defensivo.'],
      rows: [
        ['Acción principal', 'Induce fuertemente sudoración, libera el Exterior y difunde el Qi de Pulmón.', 'Libera el Exterior, armoniza Ying y Wei y regula la capa muscular.'],
        ['Pista decisiva del sudor', 'Ausencia de sudoración.', 'Sudoración espontánea.'],
        ['Pista respiratoria', 'La tos o las sibilancias son prominentes.', 'Los signos respiratorios no son la pista definitoria.'],
        ['Pulso', 'Flotante y tenso.', 'Flotante y moderado o débil.'],
        ['Elección en examen', 'Cuando el patógeno es fuerte y el Exterior está firmemente constreñido.', 'Cuando la deficiencia Exterior y la desarmonía permiten la pérdida de sudor.']
      ],
      why: 'La diferencia central no es solamente “fuerte frente a suave”: es un patrón de exceso Exterior cerrado frente a un Exterior deficiente y desarmonizado que no asegura los poros.',
      memory: '<strong>Ma Huang Tang mantiene el sudor dentro;</strong> <strong>Gui Zhi Tang deja escapar el sudor.</strong>',
      safety: 'No son intercambiables. Ma Huang Tang contiene Ma Huang y requiere evaluación cardiovascular, revisión de medicamentos y supervisión profesional cualificada.',
      prompts: ['¿Cuál corresponde a Viento-Frío sin sudor y pulso flotante-tenso?', '¿Cuál armoniza Ying y Wei?', '¿Cuál se elige cuando la sudoración espontánea es la pista decisiva?'],
      source: 'Banco fuente: FWB 1.2, Comparison Formulas to Release the Exterior; corroborado con los perfiles Classic Formulas y <em>Chinese Herbal Medicine: Formulas &amp; Strategies</em>, 2.ª ed.'
    }
  },
  {
    slug: 'da-cheng-qi-tang-vs-xiao-cheng-qi-tang-vs-tiao-wei-cheng-qi-tang',
    title: 'Da Cheng Qi Tang vs. Xiao Cheng Qi Tang vs. Tiao Wei Cheng Qi Tang',
    category: 'Formulas that Drain Downward',
    summary: 'Grade Yangming bowel excess by severity: severe focal distention and hardness, a moderate obstruction, or Heat-Dryness without marked fullness.',
    search: 'da cheng qi tang xiao cheng qi tang tiao wei cheng qi tang purge downward yangming bowel constipation da huang mang xiao zhi shi hou po',
    en: {
      eyebrow: 'Formula comparison',
      names: ['Da Cheng Qi Tang', 'Xiao Cheng Qi Tang', 'Tiao Wei Cheng Qi Tang'],
      shared: 'All three clear Heat and move stool in Yangming bowel excess. They differ in severity, abdominal findings and the balance between moving Qi and softening Dryness.',
      fast: ['Severe obstruction: focal distention, fullness, Dryness and solidity.', 'Moderate obstruction: focal distention and fullness without the same degree of Dryness.', 'Heat-Dryness and irritability without pronounced focal distention or fullness.'],
      rows: [
        ['Relative strength', 'Strongest of the three.', 'Moderate.', 'Gentler Qi movement; emphasizes Heat and Dryness.'],
        ['Key ingredients', 'Da Huang, Mang Xiao, Zhi Shi and Hou Po.', 'Da Huang, Zhi Shi and Hou Po; no Mang Xiao.', 'Da Huang, Mang Xiao and Zhi Gan Cao; no Zhi Shi or Hou Po.'],
        ['Abdominal clue', 'Marked distention, fullness, hardness and pain.', 'Distention and fullness with constipation, but less severe hardness.', 'Constipation and irritability with less marked distention/fullness.'],
        ['Exam wording', 'Severe Yangming Fu-organ excess.', 'Moderate Yangming Fu-organ excess.', 'Yangming Heat and Dryness needing regulation of the Stomach.']
      ],
      why: 'The names identify a graded family. Da Cheng Qi combines forceful Qi movement with softening; Xiao omits the salt that softens hardness; Tiao Wei omits the strong Qi movers and adds licorice.',
      memory: '<strong>Da = all four and severe;</strong> <strong>Xiao = no Mang Xiao;</strong> <strong>Tiao Wei = regulate with Gan Cao.</strong>',
      safety: 'These are strong purgative formulas for professional use. They are not appropriate for self-treatment and require caution or avoidance in pregnancy, frailty, dehydration, and suspected bowel obstruction.',
      prompts: ['Which Cheng Qi formula contains all four core ingredients?', 'Which one omits Mang Xiao?', 'Which one uses Zhi Gan Cao and lacks Zhi Shi and Hou Po?'],
      source: 'Source bank: FWB 3.2, Comparison Formulas to Purge; corroborated with the Classic Formulas profiles and <em>Chinese Herbal Medicine: Formulas &amp; Strategies</em>, 2nd ed.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas',
      names: ['Da Cheng Qi Tang', 'Xiao Cheng Qi Tang', 'Tiao Wei Cheng Qi Tang'],
      shared: 'Las tres eliminan Calor y movilizan las heces en exceso Yangming intestinal. Se distinguen por gravedad, hallazgos abdominales y el equilibrio entre mover Qi y ablandar Sequedad.',
      fast: ['Obstrucción grave: distensión focal, plenitud, Sequedad y solidez.', 'Obstrucción moderada: distensión y plenitud sin el mismo grado de Sequedad.', 'Calor-Sequedad e irritabilidad sin distensión focal o plenitud marcadas.'],
      rows: [
        ['Fuerza relativa', 'La más fuerte de las tres.', 'Moderada.', 'Movimiento de Qi más suave; enfatiza Calor y Sequedad.'],
        ['Ingredientes clave', 'Da Huang, Mang Xiao, Zhi Shi y Hou Po.', 'Da Huang, Zhi Shi y Hou Po; sin Mang Xiao.', 'Da Huang, Mang Xiao y Zhi Gan Cao; sin Zhi Shi ni Hou Po.'],
        ['Pista abdominal', 'Distensión, plenitud, dureza y dolor marcados.', 'Distensión y plenitud con estreñimiento, pero con menos dureza.', 'Estreñimiento e irritabilidad con menos distensión o plenitud.'],
        ['Lenguaje de examen', 'Exceso grave del órgano Fu de Yangming.', 'Exceso moderado del órgano Fu de Yangming.', 'Calor y Sequedad de Yangming que requieren regular Estómago.']
      ],
      why: 'Los nombres identifican una familia graduada. Da Cheng Qi combina fuerte movimiento de Qi con ablandamiento; Xiao omite la sal que ablanda dureza; Tiao Wei omite los fuertes movilizadores de Qi y añade regaliz.',
      memory: '<strong>Da = los cuatro y grave;</strong> <strong>Xiao = sin Mang Xiao;</strong> <strong>Tiao Wei = regula con Gan Cao.</strong>',
      safety: 'Son fórmulas purgantes fuertes de uso profesional. No son para automedicación y requieren cautela o evitación en embarazo, debilidad, deshidratación y sospecha de obstrucción intestinal.',
      prompts: ['¿Cuál contiene los cuatro ingredientes centrales?', '¿Cuál omite Mang Xiao?', '¿Cuál usa Zhi Gan Cao y carece de Zhi Shi y Hou Po?'],
      source: 'Banco fuente: FWB 3.2, Comparison Formulas to Purge; corroborado con los perfiles Classic Formulas y <em>Chinese Herbal Medicine: Formulas &amp; Strategies</em>, 2.ª ed.'
    }
  },
  {
    slug: 'wu-ling-san-vs-zhu-ling-tang',
    title: 'Wu Ling San vs. Zhu Ling Tang',
    category: 'Formulas that Drain Dampness',
    summary: 'Separate impaired Bladder Qi transformation with Yang involvement from water-Heat accumulation that injures Yin.',
    search: 'wu ling san zhu ling tang water damp bladder qi transformation yang heat yin urinary difficulty thirst edema poria polyporus',
    en: {
      eyebrow: 'Formula comparison',
      names: ['Wu Ling San', 'Zhu Ling Tang'],
      shared: 'Both promote urination and treat fluid accumulation, sharing Fu Ling, Zhu Ling and Ze Xie.',
      fast: ['Warms Yang and restores Bladder Qi transformation.', 'Clears Heat while promoting urination and protects injured Yin.'],
      rows: [
        ['Pattern', 'Taiyang water accumulation from impaired Bladder Qi transformation.', 'Water and Heat in the lower burner with Yin injury.'],
        ['Distinct action', 'Warms Yang and transforms Qi.', 'Clears Heat and nourishes Yin.'],
        ['Key signs', 'Urinary difficulty, edema or fluid retention; thirst with difficulty processing ingested fluids.', 'Urinary difficulty with thirst, fever or irritability; may include painful urination or blood in the urine.'],
        ['Tongue emphasis', 'Often pale or normal with a white coating.', 'Often red with a yellow coating when Heat is pronounced.'],
        ['Exam shortcut', 'Think “transformation failure.”', 'Think “water + Heat + Yin injury.”']
      ],
      why: 'The shared diuretic core can obscure the direction of treatment. Wu Ling San warms and transforms; Zhu Ling Tang drains while cooling and preserving fluids.',
      memory: '<strong>Wu Ling transforms;</strong> <strong>Zhu Ling clears and protects Yin.</strong>',
      safety: 'Urinary retention, fever with flank pain, visible blood in urine, or inability to keep fluids down requires medical assessment rather than self-treatment.',
      prompts: ['Which formula warms Yang to restore Bladder Qi transformation?', 'Which formula addresses water-Heat with Yin injury?', 'Which three herbs are shared?'],
      source: 'Source bank: FWB 5.2 and FWB 5.2.1, Comparison Formulas to Expel Damp; corroborated with the individual formula profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas',
      names: ['Wu Ling San', 'Zhu Ling Tang'],
      shared: 'Ambas promueven la micción y tratan acumulación de líquidos; comparten Fu Ling, Zhu Ling y Ze Xie.',
      fast: ['Calienta Yang y restablece la transformación del Qi de Vejiga.', 'Elimina Calor mientras promueve la micción y protege el Yin lesionado.'],
      rows: [
        ['Patrón', 'Acumulación de agua Taiyang por alteración de la transformación del Qi de Vejiga.', 'Agua y Calor en Jiao Inferior con lesión de Yin.'],
        ['Acción distintiva', 'Calienta Yang y transforma Qi.', 'Elimina Calor y nutre Yin.'],
        ['Signos clave', 'Dificultad urinaria, edema o retención; sed con dificultad para procesar líquidos ingeridos.', 'Dificultad urinaria con sed, fiebre o irritabilidad; puede incluir micción dolorosa o sangre en orina.'],
        ['Énfasis de lengua', 'Con frecuencia pálida o normal con capa blanca.', 'A menudo roja con capa amarilla cuando el Calor es marcado.'],
        ['Atajo de examen', 'Piensa “falla de transformación”.', 'Piensa “agua + Calor + lesión de Yin”.']
      ],
      why: 'El núcleo diurético compartido puede ocultar la dirección terapéutica. Wu Ling San calienta y transforma; Zhu Ling Tang drena mientras enfría y preserva líquidos.',
      memory: '<strong>Wu Ling transforma;</strong> <strong>Zhu Ling enfría y protege Yin.</strong>',
      safety: 'Retención urinaria, fiebre con dolor lumbar, sangre visible en orina o incapacidad para retener líquidos requieren evaluación médica, no automedicación.',
      prompts: ['¿Cuál calienta Yang para restablecer la transformación de Vejiga?', '¿Cuál trata Agua-Calor con lesión de Yin?', '¿Qué tres hierbas comparten?'],
      source: 'Banco fuente: FWB 5.2 y FWB 5.2.1, Comparison Formulas to Expel Damp; corroborado con los perfiles individuales de fórmulas.'
    }
  },
  {
    slug: 'er-chen-tang-vs-wen-dan-tang',
    title: 'Er Chen Tang vs. Wen Dan Tang',
    category: 'Formulas that Transform Phlegm',
    summary: 'Separate foundational Damp-Phlegm with copious white sputum from Phlegm-Heat disturbing the Gallbladder, Stomach and Spirit.',
    search: 'er chen tang wen dan tang two cured warm gallbladder damp phlegm heat cough sputum insomnia bitter taste nausea',
    en: {
      eyebrow: 'Formula comparison',
      names: ['Er Chen Tang', 'Wen Dan Tang'],
      shared: 'Both transform Phlegm, harmonize the Stomach and can address nausea, vomiting, epigastric fullness, dizziness and palpitations.',
      fast: ['Damp-Phlegm: cough with copious thin white sputum and a greasy white coat.', 'Phlegm-Heat: insomnia, irritability or fright, bitter taste and a greasy yellow coat.'],
      rows: [
        ['Primary emphasis', 'Dries Dampness and regulates Qi to transform Phlegm.', 'Clears Phlegm-Heat, regulates Qi and harmonizes the Gallbladder and Stomach.'],
        ['Target pattern', 'Damp-Phlegm centered in Spleen and Lung.', 'Phlegm-Heat disturbing Gallbladder, Stomach and Spirit.'],
        ['Decisive symptoms', 'Cough with profuse white sputum.', 'Insomnia, easy fright, restlessness or bitter taste.'],
        ['Tongue', 'Pale or normal with greasy white coating.', 'Red with greasy yellow coating.'],
        ['Pulse', 'Slippery.', 'Slippery and rapid or wiry.']
      ],
      why: 'Wen Dan Tang is built on an Er Chen Tang-like Phlegm-transforming core but redirects it toward Heat and Gallbladder/Stomach disharmony.',
      memory: '<strong>Er Chen dries white Phlegm;</strong> <strong>Wen Dan clears yellow Phlegm that disturbs sleep.</strong>',
      safety: 'Despite the English name “Warm the Gallbladder,” the commonly tested modern pattern is Phlegm-Heat; do not select it merely because a case contains Cold.',
      prompts: ['Which formula is the foundational choice for Damp-Phlegm?', 'Which formula matches insomnia with bitter taste and greasy yellow coat?', 'Which formula focuses more directly on Spleen-Lung Phlegm?'],
      source: 'Source bank: FWB 6.2 and FWB 6.2.1, Comparison Formulas to Transform Phlegm; corroborated with the individual formula profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas',
      names: ['Er Chen Tang', 'Wen Dan Tang'],
      shared: 'Ambas transforman Flema, armonizan Estómago y pueden tratar náuseas, vómitos, plenitud epigástrica, mareo y palpitaciones.',
      fast: ['Humedad-Flema: tos con esputo blanco fino y abundante y capa blanca grasosa.', 'Flema-Calor: insomnio, irritabilidad o sobresalto, sabor amargo y capa amarilla grasosa.'],
      rows: [
        ['Énfasis principal', 'Seca Humedad y regula Qi para transformar Flema.', 'Elimina Flema-Calor, regula Qi y armoniza Vesícula Biliar y Estómago.'],
        ['Patrón objetivo', 'Humedad-Flema centrada en Bazo y Pulmón.', 'Flema-Calor que altera Vesícula Biliar, Estómago y Shen.'],
        ['Síntomas decisivos', 'Tos con abundante esputo blanco.', 'Insomnio, sobresalto fácil, inquietud o sabor amargo.'],
        ['Lengua', 'Pálida o normal con capa blanca grasosa.', 'Roja con capa amarilla grasosa.'],
        ['Pulso', 'Resbaladizo.', 'Resbaladizo y rápido o de cuerda.']
      ],
      why: 'Wen Dan Tang se construye sobre un núcleo transformador de Flema parecido a Er Chen Tang, pero lo dirige hacia Calor y desarmonía de Vesícula Biliar/Estómago.',
      memory: '<strong>Er Chen seca Flema blanca;</strong> <strong>Wen Dan enfría Flema amarilla que altera el sueño.</strong>',
      safety: 'A pesar del nombre inglés “Warm the Gallbladder”, el patrón moderno evaluado habitualmente es Flema-Calor; no debe elegirse solo porque un caso contenga Frío.',
      prompts: ['¿Cuál es la fórmula base para Humedad-Flema?', '¿Cuál corresponde a insomnio, sabor amargo y capa amarilla grasosa?', '¿Cuál se centra más directamente en Flema de Bazo-Pulmón?'],
      source: 'Banco fuente: FWB 6.2 y FWB 6.2.1, Comparison Formulas to Transform Phlegm; corroborado con los perfiles individuales de fórmulas.'
    }
  },
  {
    slug: 'ding-chuan-tang-vs-ma-xing-shi-gan-tang',
    title: 'Ding Chuan Tang vs. Ma Xing Shi Gan Tang',
    category: 'Formulas that Relieve Coughing and Wheezing',
    summary: 'Separate Wind-Cold constraining a Phlegm-Heat Lung pattern from stronger Lung Heat with an Exterior component.',
    search: 'ding chuan tang ma xing shi gan tang arrest wheezing ephedra apricot gypsum lung heat phlegm wind cold cough asthma',
    en: {
      eyebrow: 'Formula comparison',
      names: ['Ding Chuan Tang', 'Ma Xing Shi Gan Tang'],
      shared: 'Both disseminate and descend Lung Qi to relieve cough and wheezing, and both contain Ma Huang, Xing Ren and Gan Cao.',
      fast: ['Wind-Cold constrains the Exterior while Phlegm-Heat obstructs the Lungs; copious thick sputum is prominent.', 'Lung Heat is more intense; fever, thirst and heat signs dominate, with cough and wheezing.'],
      rows: [
        ['Core pattern', 'External Wind-Cold with internal Phlegm-Heat.', 'Lung Heat with an unresolved Exterior component.'],
        ['Phlegm clue', 'Copious, thick and often yellow sputum.', 'Phlegm may occur, but strong Lung Heat is the central clue.'],
        ['Heat clue', 'Heat is present but embedded in a mixed Cold/Heat pattern.', 'High fever, thirst, sweating and a strong rapid pulse may dominate.'],
        ['Therapeutic emphasis', 'Arrests wheezing and clears Phlegm-Heat while releasing constraint.', 'Strongly clears Lung Heat while facilitating Lung-Qi diffusion.'],
        ['Exam shortcut', 'Think “wheeze + abundant Phlegm.”', 'Think “wheeze + blazing Lung Heat.”']
      ],
      why: 'Both can present with wheezing and Heat, but Ding Chuan Tang is more Phlegm-centered and mixed; Ma Xing Shi Gan Tang is more Heat-centered.',
      memory: '<strong>Ding Chuan = dense Phlegm;</strong> <strong>Ma Xing Shi Gan = Shi Gao-level Lung Heat.</strong>',
      safety: 'Both contain Ma Huang. Cardiovascular disease, hypertension, stimulant use and relevant medications require professional screening.',
      prompts: ['Which formula is more strongly associated with copious thick sputum?', 'Which formula emphasizes strong Lung Heat?', 'Which three ingredients are shared?'],
      source: 'Source bank: FWB 6.2.1, Comparison Formulas to Transform Phlegm; corroborated with the Classic Formulas profiles and formula practice tests.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas',
      names: ['Ding Chuan Tang', 'Ma Xing Shi Gan Tang'],
      shared: 'Ambas difunden y hacen descender Qi de Pulmón para aliviar tos y sibilancias; ambas contienen Ma Huang, Xing Ren y Gan Cao.',
      fast: ['Viento-Frío constriñe el Exterior mientras Flema-Calor obstruye Pulmón; destaca el esputo espeso y abundante.', 'El Calor de Pulmón es más intenso; predominan fiebre, sed y signos de Calor junto con tos y sibilancias.'],
      rows: [
        ['Patrón central', 'Viento-Frío externo con Flema-Calor interna.', 'Calor de Pulmón con componente Exterior no resuelto.'],
        ['Pista de Flema', 'Esputo abundante, espeso y a menudo amarillo.', 'Puede haber Flema, pero el fuerte Calor de Pulmón es la pista central.'],
        ['Pista de Calor', 'Hay Calor, pero dentro de un patrón mixto Frío/Calor.', 'Pueden predominar fiebre alta, sed, sudor y pulso fuerte-rápido.'],
        ['Énfasis terapéutico', 'Detiene sibilancias y elimina Flema-Calor mientras libera constricción.', 'Elimina fuertemente Calor de Pulmón y facilita la difusión del Qi pulmonar.'],
        ['Atajo de examen', 'Piensa “sibilancia + Flema abundante”.', 'Piensa “sibilancia + Calor intenso de Pulmón”.']
      ],
      why: 'Ambas pueden presentar sibilancias y Calor, pero Ding Chuan Tang se centra más en Flema y patrón mixto; Ma Xing Shi Gan Tang se centra más en Calor.',
      memory: '<strong>Ding Chuan = Flema densa;</strong> <strong>Ma Xing Shi Gan = Calor de Pulmón al nivel de Shi Gao.</strong>',
      safety: 'Ambas contienen Ma Huang. Enfermedad cardiovascular, hipertensión, estimulantes y medicamentos pertinentes requieren evaluación profesional.',
      prompts: ['¿Cuál se asocia más con esputo espeso y abundante?', '¿Cuál enfatiza Calor fuerte de Pulmón?', '¿Qué tres ingredientes comparten?'],
      source: 'Banco fuente: FWB 6.2.1, Comparison Formulas to Transform Phlegm; corroborado con perfiles Classic Formulas y exámenes de práctica.'
    }
  },
  {
    slug: 'gui-pi-tang-vs-suan-zao-ren-tang-vs-tian-wang-bu-xin-dan',
    title: 'Gui Pi Tang vs. Suan Zao Ren Tang vs. Tian Wang Bu Xin Dan',
    category: 'Formulas that Calm the Spirit',
    summary: 'Distinguish insomnia from Spleen-Qi/Heart-Blood deficiency, Liver-Blood deficiency with deficiency Heat, and Heart-Kidney Yin deficiency.',
    search: 'gui pi tang suan zao ren tang tian wang bu xin dan insomnia heart blood spleen qi liver blood yin kidney deficiency heat palpitations',
    en: {
      eyebrow: 'Formula comparison',
      names: ['Gui Pi Tang', 'Suan Zao Ren Tang', 'Tian Wang Bu Xin Dan'],
      shared: 'All may treat insomnia, palpitations, anxiety or poor memory when deficiency fails to nourish and settle the Spirit.',
      fast: ['Spleen Qi and Heart Blood deficiency: fatigue, poor appetite, pale complexion and possible bleeding.', 'Liver Blood deficiency with deficiency Heat: irritability, dizziness and night sweating.', 'Heart-Kidney Yin and Blood deficiency with Heart Fire: marked dryness, night sweating and stronger deficiency-Heat signs.'],
      rows: [
        ['Root pattern', 'Spleen Qi deficiency with Heart Blood deficiency.', 'Liver Blood deficiency with deficiency Heat.', 'Heart and Kidney Yin-Blood deficiency with deficient Fire.'],
        ['Decisive clue', 'Fatigue, poor appetite, easy bruising or abnormal bleeding.', 'Irritability, dizziness and night sweats with insomnia.', 'Dry mouth, night sweats, palpitations and pronounced Yin-deficiency Heat.'],
        ['Tongue', 'Pale.', 'Red or pale-red, often with a thin coat.', 'Red with little coating.'],
        ['Pulse', 'Thin and weak.', 'Wiry or thin and rapid.', 'Thin and rapid.'],
        ['Exam shortcut', 'Qi cannot make/hold Blood.', 'Liver Blood does not anchor the Hun.', 'Heart-Kidney Yin cannot control Fire.']
      ],
      why: 'The symptom “insomnia” is not enough. Appetite and bleeding point to Gui Pi Tang; irritability with Liver-Blood clues points to Suan Zao Ren Tang; dryness and Heart-Kidney Yin deficiency point to Tian Wang Bu Xin Dan.',
      memory: '<strong>Gui Pi feeds Qi and Blood;</strong> <strong>Suan Zao Ren settles the Liver;</strong> <strong>Tian Wang connects Heart and Kidney Yin.</strong>',
      safety: 'Classical Tian Wang Bu Xin Dan may list Zhu Sha (cinnabar). Modern study and clinical references should use cinnabar-free, quality-controlled versions; mercury-containing products are not recommended.',
      prompts: ['Which formula combines Spirit symptoms with poor appetite and bleeding?', 'Which matches Liver-Blood deficiency with irritability and night sweating?', 'Which matches a red tongue with little coat and Heart-Kidney Yin deficiency?'],
      source: 'Source bank: FWB 13.1 and FWB 13.1.1, Comparison Formulas to Calm the Mind; corroborated with the Classic Formulas profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas',
      names: ['Gui Pi Tang', 'Suan Zao Ren Tang', 'Tian Wang Bu Xin Dan'],
      shared: 'Todas pueden tratar insomnio, palpitaciones, ansiedad o mala memoria cuando una deficiencia no nutre ni asienta Shen.',
      fast: ['Deficiencia de Qi de Bazo y Sangre de Corazón: fatiga, poco apetito, tez pálida y posible sangrado.', 'Deficiencia de Sangre de Hígado con Calor por deficiencia: irritabilidad, mareo y sudor nocturno.', 'Deficiencia de Yin y Sangre de Corazón-Riñón con Fuego de Corazón: Sequedad marcada, sudor nocturno y signos más fuertes de Calor por deficiencia.'],
      rows: [
        ['Patrón raíz', 'Deficiencia de Qi de Bazo y Sangre de Corazón.', 'Deficiencia de Sangre de Hígado con Calor por deficiencia.', 'Deficiencia de Yin-Sangre de Corazón y Riñón con Fuego vacío.'],
        ['Pista decisiva', 'Fatiga, poco apetito, moretones fáciles o sangrado anormal.', 'Irritabilidad, mareo y sudor nocturno con insomnio.', 'Boca seca, sudor nocturno, palpitaciones y Calor por deficiencia de Yin marcado.'],
        ['Lengua', 'Pálida.', 'Roja o rojo-pálida, a menudo con capa fina.', 'Roja con poca capa.'],
        ['Pulso', 'Fino y débil.', 'De cuerda o fino y rápido.', 'Fino y rápido.'],
        ['Atajo de examen', 'El Qi no produce o no contiene Sangre.', 'La Sangre de Hígado no ancla el Hun.', 'El Yin de Corazón-Riñón no controla Fuego.']
      ],
      why: 'El síntoma “insomnio” no basta. Apetito y sangrado señalan Gui Pi Tang; irritabilidad con pistas de Sangre de Hígado señalan Suan Zao Ren Tang; Sequedad y deficiencia de Yin Corazón-Riñón señalan Tian Wang Bu Xin Dan.',
      memory: '<strong>Gui Pi alimenta Qi y Sangre;</strong> <strong>Suan Zao Ren asienta Hígado;</strong> <strong>Tian Wang conecta Yin de Corazón y Riñón.</strong>',
      safety: 'La versión clásica de Tian Wang Bu Xin Dan puede incluir Zhu Sha (cinabrio). Las referencias modernas deben usar versiones sin cinabrio y con control de calidad; no se recomiendan productos con mercurio.',
      prompts: ['¿Cuál combina síntomas de Shen con poco apetito y sangrado?', '¿Cuál corresponde a deficiencia de Sangre de Hígado con irritabilidad y sudor nocturno?', '¿Cuál corresponde a lengua roja con poca capa y deficiencia de Yin Corazón-Riñón?'],
      source: 'Banco fuente: FWB 13.1 y FWB 13.1.1, Comparison Formulas to Calm the Mind; corroborado con perfiles Classic Formulas.'
    }
  },
  {
    slug: 'liu-wei-di-huang-wan-vs-jin-gui-shen-qi-wan',
    title: 'Liu Wei Di Huang Wan vs. Jin Gui Shen Qi Wan',
    category: 'Formulas that Tonify',
    summary: 'Separate Kidney-Liver Yin deficiency and empty Heat from Kidney Yang deficiency with Cold and impaired water transformation.',
    search: 'liu wei di huang wan jin gui shen qi wan six ingredient rehmannia kidney yin yang deficiency heat cold fu zi gui zhi',
    en: {
      eyebrow: 'Formula comparison',
      names: ['Liu Wei Di Huang Wan', 'Jin Gui Shen Qi Wan'],
      shared: 'Both use the six-herb Liu Wei Di Huang Wan base to address Kidney deficiency, with low-back weakness, dizziness, tinnitus or urinary changes.',
      fast: ['Kidney-Liver Yin deficiency: night sweats, dry mouth, five-center Heat and a red tongue with little coat.', 'Kidney Yang deficiency: cold limbs, edema, urinary difficulty or copious clear urine and a pale swollen tongue.'],
      rows: [
        ['Thermal direction', 'Nourishes Yin; suited to deficiency-Heat signs.', 'Warms and assists Kidney Yang; suited to Cold and water-metabolism signs.'],
        ['Construction', 'The foundational six-herb formula.', 'The six-herb base plus warming Yang assistants, classically Gui Zhi and Fu Zi.'],
        ['Decisive signs', 'Night sweating, dry throat, heat sensations, seminal emission.', 'Cold extremities, edema, weak low back, urinary dysfunction.'],
        ['Tongue and pulse', 'Red with scant coat; thin and rapid.', 'Pale, swollen and moist; deep and weak.'],
        ['Exam shortcut', 'Deficient water: nourish Yin.', 'Weak fire at the gate of vitality: warm Yang and transform water.']
      ],
      why: 'The formulas share a base, but the added warming medicinals reverse the diagnostic direction. Heat/dryness points to Liu Wei; Cold/fluid failure points to Jin Gui Shen Qi.',
      memory: '<strong>Liu Wei builds Kidney water;</strong> <strong>Jin Gui Shen Qi adds fire to move water.</strong>',
      safety: 'Jin Gui Shen Qi Wan includes processed Fu Zi in classical composition and requires qualified professional use and reliable sourcing. It is not a home-dosing formula.',
      prompts: ['Which formula fits night sweats and a red tongue with scant coat?', 'Which fits cold limbs with edema or urinary difficulty?', 'What structural relationship links the two formulas?'],
      source: 'Source bank: FWB 8.2 series, Comparison Formulas to Tonify; corroborated with the Classic Formulas profiles and individual formula files.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas',
      names: ['Liu Wei Di Huang Wan', 'Jin Gui Shen Qi Wan'],
      shared: 'Ambas usan la base de seis hierbas de Liu Wei Di Huang Wan para deficiencia de Riñón, con debilidad lumbar, mareo, tinnitus o cambios urinarios.',
      fast: ['Deficiencia de Yin de Riñón-Hígado: sudor nocturno, boca seca, Calor en cinco centros y lengua roja con poca capa.', 'Deficiencia de Yang de Riñón: extremidades frías, edema, dificultad urinaria u orina clara abundante y lengua pálida-hinchada.'],
      rows: [
        ['Dirección térmica', 'Nutre Yin; corresponde a signos de Calor por deficiencia.', 'Calienta y asiste Yang de Riñón; corresponde a Frío y alteraciones del metabolismo de agua.'],
        ['Construcción', 'La fórmula base de seis hierbas.', 'La base de seis más asistentes que calientan Yang, clásicamente Gui Zhi y Fu Zi.'],
        ['Signos decisivos', 'Sudor nocturno, garganta seca, sensación de Calor, emisión seminal.', 'Extremidades frías, edema, debilidad lumbar, disfunción urinaria.'],
        ['Lengua y pulso', 'Roja con poca capa; fino y rápido.', 'Pálida, hinchada y húmeda; profundo y débil.'],
        ['Atajo de examen', 'Agua deficiente: nutrir Yin.', 'Fuego débil en Ming Men: calentar Yang y transformar agua.']
      ],
      why: 'Comparten una base, pero las sustancias calientes añadidas invierten la dirección diagnóstica. Calor/Sequedad señalan Liu Wei; Frío/falla de líquidos señalan Jin Gui Shen Qi.',
      memory: '<strong>Liu Wei construye agua de Riñón;</strong> <strong>Jin Gui Shen Qi añade fuego para mover agua.</strong>',
      safety: 'Jin Gui Shen Qi Wan incluye Fu Zi procesado en su composición clásica y requiere uso profesional cualificado y fuente confiable. No es una fórmula para dosificación casera.',
      prompts: ['¿Cuál corresponde a sudor nocturno y lengua roja con poca capa?', '¿Cuál corresponde a extremidades frías con edema o dificultad urinaria?', '¿Qué relación estructural une las dos fórmulas?'],
      source: 'Banco fuente: serie FWB 8.2, Comparison Formulas to Tonify; corroborado con perfiles Classic Formulas y archivos individuales de fórmulas.'
    }
  },
  {
    slug: 'si-ni-tang-vs-si-ni-san',
    title: 'Si Ni Tang vs. Si Ni San',
    category: 'Formulas that Warm the Interior / Harmonize',
    summary: 'Do not let the shared name mislead you: one rescues devastated Yang; the other spreads constrained Qi so warmth reaches the extremities.',
    search: 'si ni tang si ni san frigid extremities decoction powder yang collapse cold qi constraint shaoyang liver spleen fu zi',
    en: {
      eyebrow: 'Formula name trap',
      names: ['Si Ni Tang', 'Si Ni San'],
      shared: 'Both may present with cold extremities, but the mechanism and urgency are fundamentally different.',
      fast: ['Severe internal Cold from Yang collapse: cold extends proximally, vitality is weak and the pulse is faint.', 'Qi constraint: only the fingers and toes may be cold while the body is warm; the pulse is wiry.'],
      rows: [
        ['Category', 'Formula that warms the Interior and rescues devastated Yang.', 'Harmonizing formula that spreads constrained Yang/Qi.'],
        ['Mechanism', 'Yang is too weak to warm the body.', 'Yang exists but is blocked from reaching the extremities.'],
        ['Decisive signs', 'Profuse clear diarrhea, absence of thirst, fatigue, curled posture and a minute or faint pulse.', 'Cold hands/feet with irritability, abdominal pain or diarrhea and a wiry pulse.'],
        ['Therapeutic direction', 'Warm the Interior and restore Yang.', 'Spread Liver Qi, harmonize Liver and Spleen, and release constraint.'],
        ['Name trap', 'Tang: collapse and true Cold.', 'San: constraint, not true Yang collapse.']
      ],
      why: 'Cold extremities describe an outcome, not a diagnosis. Ask whether warmth is absent because Yang has collapsed or merely cannot circulate because Qi is constrained.',
      memory: '<strong>Si Ni Tang has no fire;</strong> <strong>Si Ni San has fire that cannot reach the fingers.</strong>',
      safety: 'Si Ni Tang contains Fu Zi; contemporary safe practice requires an appropriately processed ingredient, reliable sourcing and qualified professional supervision. Suspected shock or circulatory collapse is a medical emergency.',
      prompts: ['Which formula treats Yang collapse with a faint pulse?', 'Which treats cold fingers and toes from Qi constraint?', 'Why is the shared phrase “cold extremities” insufficient by itself?'],
      source: 'Source bank: FWB 7.1 series and harmonizing comparisons; corroborated with the Classic Formulas profiles and formula practice tests.'
    },
    es: {
      eyebrow: 'Trampa de nombres de fórmulas',
      names: ['Si Ni Tang', 'Si Ni San'],
      shared: 'Ambas pueden presentar extremidades frías, pero el mecanismo y la urgencia son fundamentalmente distintos.',
      fast: ['Frío Interno grave por colapso de Yang: el frío asciende proximalmente, la vitalidad es débil y el pulso es tenue.', 'Estancamiento de Qi: solo dedos de manos y pies pueden estar fríos mientras el cuerpo está caliente; el pulso es de cuerda.'],
      rows: [
        ['Categoría', 'Fórmula que calienta Interior y rescata Yang devastado.', 'Fórmula armonizante que difunde Yang/Qi constreñido.'],
        ['Mecanismo', 'Yang es demasiado débil para calentar el cuerpo.', 'Yang existe, pero está bloqueado y no llega a las extremidades.'],
        ['Signos decisivos', 'Diarrea clara profusa, ausencia de sed, fatiga, postura encogida y pulso diminuto o tenue.', 'Manos/pies fríos con irritabilidad, dolor abdominal o diarrea y pulso de cuerda.'],
        ['Dirección terapéutica', 'Calentar Interior y restaurar Yang.', 'Difundir Qi de Hígado, armonizar Hígado-Bazo y liberar estancamiento.'],
        ['Trampa del nombre', 'Tang: colapso y Frío verdadero.', 'San: estancamiento, no colapso verdadero de Yang.']
      ],
      why: '“Extremidades frías” describe un resultado, no un diagnóstico. Hay que preguntar si falta calor porque Yang colapsó o si el calor no circula porque Qi está constreñido.',
      memory: '<strong>Si Ni Tang no tiene fuego;</strong> <strong>Si Ni San tiene fuego que no llega a los dedos.</strong>',
      safety: 'Si Ni Tang contiene Fu Zi; el uso moderno seguro exige una sustancia correctamente procesada, fuente confiable y supervisión profesional cualificada. La sospecha de shock o colapso circulatorio es una emergencia médica.',
      prompts: ['¿Cuál trata colapso de Yang con pulso tenue?', '¿Cuál trata dedos fríos por estancamiento de Qi?', '¿Por qué “extremidades frías” no basta para escoger?'],
      source: 'Banco fuente: serie FWB 7.1 y comparaciones de armonización; corroborado con perfiles Classic Formulas y exámenes de práctica.'
    }
  },
  {
    slug: 'gui-zhi-tang-vs-yu-ping-feng-san',
    title: 'Gui Zhi Tang vs. Yu Ping Feng San',
    category: 'Release the Exterior / Tonify Qi',
    summary: 'Separate treatment of an active Wind-Cold invasion with spontaneous sweating from prevention in chronic Defensive-Qi deficiency.',
    search: 'gui zhi tang yu ping feng san cinnamon twig jade windscreen exterior wind cold wei qi deficiency spontaneous sweating recurrent colds prevention',
    en: {
      eyebrow: 'Formula comparison', names: ['Gui Zhi Tang', 'Yu Ping Feng San'],
      shared: 'Both can involve spontaneous sweating and aversion to wind, but one treats an active Exterior pattern while the other strengthens a chronically weak Exterior.',
      fast: ['Active Wind-Cold invasion with fever, chills and a floating-moderate pulse.', 'No active invasion: chronic spontaneous sweating, weak resistance and recurrent colds.'],
      rows: [
        ['Category', 'Formula that releases the Exterior.', 'Formula that tonifies Qi and stabilizes the Exterior.'],
        ['Primary action', 'Harmonizes Ying and Wei while releasing Wind-Cold.', 'Augments Defensive Qi, secures the Exterior and stops sweating.'],
        ['Timing clue', 'Use for the current acute pattern.', 'Use between episodes for constitutional prevention.'],
        ['Decisive signs', 'Fever and chills, headache, sweating, floating-moderate pulse.', 'Recurrent colds, spontaneous sweating, pale complexion, weak pulse.'],
        ['Exam trap', 'Not a preventive tonic.', 'Not the choice during a strong active Exterior pathogen.']
      ],
      why: 'The shared sweating clue can mislead. First determine whether a pathogen is currently present or whether the Exterior is simply too weak to defend and secure itself.',
      memory: '<strong>Gui Zhi treats the visitor;</strong> <strong>Yu Ping Feng repairs the screen.</strong>',
      safety: 'An acute febrile illness with breathing difficulty, dehydration or rapid deterioration requires medical assessment.',
      prompts: ['Which formula treats an active Wind-Cold deficiency pattern?', 'Which is used preventively for recurrent colds?', 'What does the “Jade Windscreen” image help you remember?'],
      source: 'Source bank: FWB 12.1 comparisons; corroborated with the keyed pattern, ingredients, symptoms and the individual formula profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Gui Zhi Tang', 'Yu Ping Feng San'],
      shared: 'Ambas pueden incluir sudoración espontánea y aversión al viento, pero una trata un patrón Exterior activo y la otra fortalece un Exterior crónicamente débil.',
      fast: ['Invasión activa de Viento-Frío con fiebre, escalofríos y pulso flotante-moderado.', 'Sin invasión activa: sudor crónico espontáneo, defensas débiles y resfriados recurrentes.'],
      rows: [
        ['Categoría', 'Fórmula que libera Exterior.', 'Fórmula que tonifica Qi y estabiliza Exterior.'],
        ['Acción principal', 'Armoniza Ying y Wei mientras libera Viento-Frío.', 'Aumenta Qi Defensivo, asegura Exterior y detiene sudor.'],
        ['Pista temporal', 'Para el patrón agudo actual.', 'Entre episodios, para prevención constitucional.'],
        ['Signos decisivos', 'Fiebre y escalofríos, cefalea, sudor, pulso flotante-moderado.', 'Resfriados recurrentes, sudor espontáneo, tez pálida, pulso débil.'],
        ['Trampa de examen', 'No es un tónico preventivo.', 'No es la elección durante un patógeno Exterior fuerte y activo.']
      ],
      why: 'La pista compartida del sudor puede engañar. Primero decide si hay un patógeno presente o si el Exterior es demasiado débil para defenderse y asegurarse.',
      memory: '<strong>Gui Zhi trata al visitante;</strong> <strong>Yu Ping Feng repara la pantalla.</strong>',
      safety: 'Una enfermedad febril aguda con dificultad respiratoria, deshidratación o deterioro rápido requiere evaluación médica.',
      prompts: ['¿Cuál trata un patrón activo de deficiencia por Viento-Frío?', '¿Cuál se usa preventivamente en resfriados recurrentes?', '¿Qué recuerda la imagen de la “Pantalla de Jade”?'],
      source: 'Banco fuente: comparaciones FWB 12.1; corroborado con patrón, ingredientes, síntomas clave y perfiles individuales de las fórmulas.'
    }
  },
  {
    slug: 'zhen-wu-tang-vs-si-ni-tang',
    title: 'Zhen Wu Tang vs. Si Ni Tang',
    category: 'Warm the Interior / Drain Dampness',
    summary: 'Separate Spleen-Kidney Yang deficiency with water accumulation from acute severe Yang collapse dominated by internal Cold.',
    search: 'zhen wu tang si ni tang true warrior frigid extremities kidney spleen yang deficiency water accumulation edema collapse cold fu zi ginger',
    en: {
      eyebrow: 'Formula comparison', names: ['Zhen Wu Tang', 'Si Ni Tang'],
      shared: 'Both warm Yang and may contain Fu Zi and ginger, but their therapeutic targets differ.',
      fast: ['Yang deficiency fails to transform water: edema, scanty urine, heaviness, dizziness or palpitations.', 'Yang collapse fails to warm the body: severe cold limbs, diarrhea, exhaustion and a faint pulse.'],
      rows: [
        ['Primary problem', 'Spleen-Kidney Yang deficiency with retained water.', 'Severe internal Cold with devastated or collapsing Yang.'],
        ['Therapeutic emphasis', 'Warm Yang and promote urination to transform water.', 'Restore Yang and rescue collapse.'],
        ['Fluid clue', 'Edema, urinary difficulty, heaviness and fluid retention are central.', 'Fluid retention is not the defining feature.'],
        ['Ginger distinction', 'Uses Sheng Jiang in the standard formula.', 'Uses Gan Jiang in the standard formula.'],
        ['Exam shortcut', 'Cold + water.', 'Cold + collapse.']
      ],
      why: 'Both are warming formulas, so the discriminating question is whether water accumulation or systemic collapse dominates.',
      memory: '<strong>Zhen Wu moves cold water;</strong> <strong>Si Ni restores failing fire.</strong>',
      safety: 'Both classically involve Fu Zi. Contemporary safe practice requires processed material, verified sourcing and qualified professional supervision; suspected collapse is an emergency.',
      prompts: ['Which formula treats Yang deficiency with edema?', 'Which formula is directed at Yang collapse?', 'Which uses Sheng Jiang and which uses Gan Jiang?'],
      source: 'Source bank: FWB 5.2.1 and FWB 7.1, Comparison Formulas to Expel Damp / Warm the Interior; corroborated with Classic Formulas profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Zhen Wu Tang', 'Si Ni Tang'],
      shared: 'Ambas calientan Yang y pueden contener Fu Zi y jengibre, pero sus objetivos terapéuticos son distintos.',
      fast: ['Deficiencia de Yang no transforma agua: edema, orina escasa, pesadez, mareo o palpitaciones.', 'Colapso de Yang no calienta el cuerpo: extremidades muy frías, diarrea, agotamiento y pulso tenue.'],
      rows: [
        ['Problema principal', 'Deficiencia de Yang de Bazo-Riñón con agua retenida.', 'Frío Interno grave con Yang devastado o en colapso.'],
        ['Énfasis terapéutico', 'Calentar Yang y promover micción para transformar agua.', 'Restaurar Yang y rescatar colapso.'],
        ['Pista de líquidos', 'Edema, dificultad urinaria, pesadez y retención son centrales.', 'La retención de líquidos no es el rasgo definitorio.'],
        ['Distinción del jengibre', 'Usa Sheng Jiang en la fórmula estándar.', 'Usa Gan Jiang en la fórmula estándar.'],
        ['Atajo de examen', 'Frío + agua.', 'Frío + colapso.']
      ],
      why: 'Ambas calientan; la pregunta discriminante es si predominan acumulación de agua o colapso sistémico.',
      memory: '<strong>Zhen Wu mueve agua fría;</strong> <strong>Si Ni restaura el fuego que falla.</strong>',
      safety: 'Ambas incluyen clásicamente Fu Zi. El uso moderno seguro exige material procesado, fuente verificada y supervisión profesional; sospecha de colapso es una emergencia.',
      prompts: ['¿Cuál trata deficiencia de Yang con edema?', '¿Cuál se dirige al colapso de Yang?', '¿Cuál usa Sheng Jiang y cuál Gan Jiang?'],
      source: 'Banco fuente: FWB 5.2.1 y FWB 7.1, Comparison Formulas to Expel Damp / Warm the Interior; corroborado con perfiles Classic Formulas.'
    }
  },
  {
    slug: 'xiao-chai-hu-tang-vs-ban-xia-xie-xin-tang',
    title: 'Xiao Chai Hu Tang vs. Ban Xia Xie Xin Tang',
    category: 'Formulas that Harmonize',
    summary: 'Separate a Shaoyang half-exterior/half-interior disorder from mixed Cold and Heat obstructing the epigastrium.',
    search: 'xiao chai hu tang ban xia xie xin tang minor bupleurum pinellia drain epigastrium harmonize shaoyang half exterior mixed cold heat epigastric focal distention',
    en: {
      eyebrow: 'Formula comparison', names: ['Xiao Chai Hu Tang', 'Ban Xia Xie Xin Tang'],
      shared: 'Both harmonize complex patterns and may include nausea, poor appetite or vomiting.',
      fast: ['Shaoyang disorder: alternating chills and fever, bitter taste, dry throat and hypochondriac fullness.', 'Mixed Cold-Heat in the middle burner: focal epigastric distention with nausea, vomiting, borborygmus or diarrhea.'],
      rows: [
        ['Location', 'Half-exterior, half-interior Shaoyang level.', 'Epigastrium and middle burner.'],
        ['Mechanism', 'Pathogen lodged in Shaoyang with pivot dysfunction.', 'Cold and Heat bind with Stomach-Qi rebellion and Spleen deficiency.'],
        ['Decisive clue', 'Alternating chills and fever.', 'Painless or mildly tender focal epigastric distention.'],
        ['Associated signs', 'Bitter taste, dry throat, dizziness, wiry pulse.', 'Vomiting with diarrhea, intestinal sounds and epigastric fullness.'],
        ['Exam shortcut', 'Harmonize Shaoyang.', 'Open the epigastric bind.']
      ],
      why: '“Nausea” is shared and therefore weak evidence. The alternating Shaoyang pattern points to Xiao Chai Hu; focal epigastric blockage with above-below disharmony points to Ban Xia Xie Xin.',
      memory: '<strong>Xiao Chai Hu turns the pivot;</strong> <strong>Ban Xia Xie Xin opens the knot.</strong>',
      safety: 'New fever with significant abdominal pain, persistent vomiting, black stools or dehydration requires medical assessment.',
      prompts: ['Which formula has alternating chills and fever?', 'Which has focal epigastric distention with vomiting and diarrhea?', 'What location distinguishes the two?'],
      source: 'Source bank: FWB 4.2 series, Comparison Formulas to Harmonize; corroborated with individual formula profiles and formula chapter materials.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Xiao Chai Hu Tang', 'Ban Xia Xie Xin Tang'],
      shared: 'Ambas armonizan patrones complejos y pueden incluir náusea, poco apetito o vómito.',
      fast: ['Trastorno Shaoyang: alternancia de escalofríos y fiebre, sabor amargo, garganta seca y plenitud hipocondríaca.', 'Frío-Calor mixtos en Jiao Medio: distensión epigástrica focal con náusea, vómito, borborigmos o diarrea.'],
      rows: [
        ['Localización', 'Nivel Shaoyang mitad Exterior-mitad Interior.', 'Epigastrio y Jiao Medio.'],
        ['Mecanismo', 'Patógeno alojado en Shaoyang con disfunción de la bisagra.', 'Frío y Calor se ligan con Qi rebelde de Estómago y deficiencia de Bazo.'],
        ['Pista decisiva', 'Alternancia de escalofríos y fiebre.', 'Distensión epigástrica focal indolora o poco sensible.'],
        ['Signos asociados', 'Sabor amargo, garganta seca, mareo, pulso de cuerda.', 'Vómito con diarrea, ruidos intestinales y plenitud epigástrica.'],
        ['Atajo de examen', 'Armonizar Shaoyang.', 'Abrir la ligadura epigástrica.']
      ],
      why: '“Náusea” es compartida y aporta poca evidencia. La alternancia Shaoyang señala Xiao Chai Hu; el bloqueo epigástrico con desarmonía arriba-abajo señala Ban Xia Xie Xin.',
      memory: '<strong>Xiao Chai Hu gira la bisagra;</strong> <strong>Ban Xia Xie Xin abre el nudo.</strong>',
      safety: 'Fiebre nueva con dolor abdominal importante, vómito persistente, heces negras o deshidratación requiere evaluación médica.',
      prompts: ['¿Cuál presenta escalofríos y fiebre alternantes?', '¿Cuál presenta distensión epigástrica con vómito y diarrea?', '¿Qué localización las distingue?'],
      source: 'Banco fuente: serie FWB 4.2, Comparison Formulas to Harmonize; corroborado con perfiles individuales y capítulos de fórmulas.'
    }
  },
  {
    slug: 'tian-ma-gou-teng-yin-vs-zhen-gan-xi-feng-tang',
    title: 'Tian Ma Gou Teng Yin vs. Zhen Gan Xi Feng Tang',
    category: 'Formulas that Extinguish Wind',
    summary: 'Grade Liver-Yang rising with internal Wind from a common hypertensive presentation to severe ascendance with impending Wind-stroke.',
    search: 'tian ma gou teng yin zhen gan xi feng tang gastrodia uncaria sedate liver extinguish wind liver yang rising headache dizziness stroke',
    en: {
      eyebrow: 'Formula comparison', names: ['Tian Ma Gou Teng Yin', 'Zhen Gan Xi Feng Tang'],
      shared: 'Both calm the Liver, subdue ascendant Yang and extinguish internal Wind, often with headache, dizziness, tinnitus or elevated blood pressure.',
      fast: ['Liver-Yang rising with internal Wind and Heat: headache, dizziness, insomnia and irritability.', 'More severe Yang ascendance and Wind with red face, intense vertigo, tremor, weakness or signs threatening Wind-stroke.'],
      rows: [
        ['Severity', 'Moderate to significant Liver-Yang/Wind pattern.', 'Severe, forceful ascendance with greater risk of collapse into Wind-stroke.'],
        ['Primary direction', 'Clears Heat, calms Liver Yang and extinguishes Wind.', 'Heavily anchors Yang, extinguishes Wind and nourishes deficient Liver-Kidney Yin.'],
        ['Decisive clue', 'Headache, dizziness, tinnitus, insomnia and a wiry-rapid pulse.', 'Severe vertigo, red face, tremor, numbness or weakness with a forceful wiry pulse.'],
        ['Exam shortcut', 'Hot, restless Liver-Yang pattern.', 'Heavy anchoring for a dangerous upward surge.']
      ],
      why: 'The formulas overlap in diagnosis; severity and force distinguish them. Neurologic deficits are not merely a formula clue—they are an emergency sign.',
      memory: '<strong>Tian Ma calms the climb;</strong> <strong>Zhen Gan weighs down the surge.</strong>',
      safety: 'Sudden severe headache, facial droop, one-sided weakness, speech difficulty, loss of balance or altered consciousness requires emergency stroke evaluation.',
      prompts: ['Which formula is more strongly anchoring?', 'Which better fits headache and insomnia from Liver-Yang rising?', 'Which findings require emergency referral rather than formula selection?'],
      source: 'Source bank: FWB 14.1 series, Comparison Formulas to Expel Wind; corroborated with the individual formula profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Tian Ma Gou Teng Yin', 'Zhen Gan Xi Feng Tang'],
      shared: 'Ambas calman Hígado, someten Yang ascendente y extinguen Viento interno, con cefalea, mareo, tinnitus o presión elevada.',
      fast: ['Yang de Hígado ascendente con Viento y Calor: cefalea, mareo, insomnio e irritabilidad.', 'Ascenso más grave de Yang y Viento con cara roja, vértigo intenso, temblor, debilidad o amenaza de ataque de Viento.'],
      rows: [
        ['Gravedad', 'Patrón moderado a importante de Yang/Viento de Hígado.', 'Ascenso grave y fuerte con mayor riesgo de evolucionar a ataque de Viento.'],
        ['Dirección principal', 'Elimina Calor, calma Yang de Hígado y extingue Viento.', 'Ancla fuertemente Yang, extingue Viento y nutre Yin deficiente de Hígado-Riñón.'],
        ['Pista decisiva', 'Cefalea, mareo, tinnitus, insomnio y pulso de cuerda-rápido.', 'Vértigo grave, cara roja, temblor, entumecimiento o debilidad con pulso de cuerda-fuerte.'],
        ['Atajo de examen', 'Patrón caliente e inquieto de Yang de Hígado.', 'Anclaje pesado para un ascenso peligroso.']
      ],
      why: 'Los diagnósticos se superponen; las distinguen gravedad y fuerza. Déficits neurológicos no son solo una pista de fórmula: son señal de emergencia.',
      memory: '<strong>Tian Ma calma la subida;</strong> <strong>Zhen Gan pesa sobre el ascenso.</strong>',
      safety: 'Cefalea súbita intensa, caída facial, debilidad unilateral, dificultad del habla, pérdida de equilibrio o alteración de conciencia exige evaluación urgente por ictus.',
      prompts: ['¿Cuál ancla con más fuerza?', '¿Cuál encaja con cefalea e insomnio por ascenso de Yang?', '¿Qué hallazgos exigen emergencia en vez de escoger fórmula?'],
      source: 'Banco fuente: serie FWB 14.1, Comparison Formulas to Expel Wind; corroborado con perfiles individuales de fórmulas.'
    }
  }
];

cards.push(
  {
    slug: 'bai-hu-tang-vs-zhu-ye-shi-gao-tang',
    title: 'Bai Hu Tang vs. Zhu Ye Shi Gao Tang',
    category: 'Formulas that Clear Heat',
    summary: 'Separate blazing Qi-level excess Heat from lingering Heat after illness with injured Qi and fluids.',
    search: 'bai hu tang zhu ye shi gao tang white tiger lophatherus gypsum qi level heat four bigs residual heat qi fluid injury',
    en: {
      eyebrow: 'Formula comparison', names: ['Bai Hu Tang', 'Zhu Ye Shi Gao Tang'],
      shared: 'Both clear Qi-level Heat and generate fluids, and both use Shi Gao, Gan Cao and Jing Mi.',
      fast: ['Blazing excess Heat: the “four bigs”—high fever, great thirst, profuse sweating and a big forceful pulse.', 'Residual Heat after febrile disease with injured Qi and fluids: lingering fever, thirst, fatigue and rebellious Stomach Qi.'],
      rows: [
        ['Root pattern', 'Strong Yangming Qi-level excess Heat.', 'Residual Heat with Qi and fluid deficiency after illness.'],
        ['Therapeutic emphasis', 'Powerfully clears Heat and generates fluids.', 'Clears lingering Heat, augments Qi, nourishes fluids and harmonizes the Stomach.'],
        ['Decisive clue', 'All four “bigs” are prominent.', 'Weakness plus thirst, irritability, nausea or retching after the major Heat has declined.'],
        ['Pulse', 'Flooding, forceful and rapid.', 'Deficient and rapid.'],
        ['Exam shortcut', 'Heat is still full and blazing.', 'Heat remains, but the patient is depleted.']
      ],
      why: 'Do not choose only by the presence of fever and thirst. The patient’s strength distinguishes a full Heat pattern from residual Heat with deficiency.',
      memory: '<strong>Bai Hu fights the blaze;</strong> <strong>Zhu Ye Shi Gao clears the embers and rebuilds fluids.</strong>',
      safety: 'Persistent high fever, confusion, inability to drink or signs of severe dehydration require urgent medical evaluation.',
      prompts: ['Which formula matches the four bigs?', 'Which formula is used after Heat has injured Qi and fluids?', 'Which pulse distinguishes full Heat from residual Heat with deficiency?'],
      source: 'Source bank: FWB 2.2 series, Comparison Formulas to Clear Heat; corroborated with Classic Formulas profiles and individual formula files.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Bai Hu Tang', 'Zhu Ye Shi Gao Tang'],
      shared: 'Ambas eliminan Calor del nivel Qi y generan líquidos; comparten Shi Gao, Gan Cao y Jing Mi.',
      fast: ['Calor de exceso abrasador: los “cuatro grandes”—fiebre alta, gran sed, sudor profuso y pulso grande-fuerte.', 'Calor residual después de enfermedad febril con Qi y líquidos lesionados: fiebre persistente, sed, fatiga y Qi rebelde de Estómago.'],
      rows: [
        ['Patrón raíz', 'Calor fuerte de exceso en nivel Qi Yangming.', 'Calor residual con deficiencia de Qi y líquidos después de enfermedad.'],
        ['Énfasis terapéutico', 'Elimina poderosamente Calor y genera líquidos.', 'Elimina Calor persistente, aumenta Qi, nutre líquidos y armoniza Estómago.'],
        ['Pista decisiva', 'Los cuatro “grandes” son prominentes.', 'Debilidad con sed, irritabilidad, náusea o arcadas después de disminuir el Calor principal.'],
        ['Pulso', 'Desbordante, fuerte y rápido.', 'Deficiente y rápido.'],
        ['Atajo de examen', 'El Calor sigue lleno y abrasador.', 'Queda Calor, pero el paciente está agotado.']
      ],
      why: 'No elijas solo por fiebre y sed. La fuerza del paciente distingue Calor lleno de Calor residual con deficiencia.',
      memory: '<strong>Bai Hu combate el incendio;</strong> <strong>Zhu Ye Shi Gao elimina brasas y reconstruye líquidos.</strong>',
      safety: 'Fiebre alta persistente, confusión, incapacidad para beber o deshidratación grave requieren evaluación urgente.',
      prompts: ['¿Cuál corresponde a los cuatro grandes?', '¿Cuál se usa después de que el Calor lesiona Qi y líquidos?', '¿Qué pulso distingue exceso de deficiencia?'],
      source: 'Banco fuente: serie FWB 2.2, Comparison Formulas to Clear Heat; corroborado con perfiles Classic Formulas y archivos individuales.'
    }
  },
  {
    slug: 'xiao-chai-hu-tang-vs-da-chai-hu-tang',
    title: 'Xiao Chai Hu Tang vs. Da Chai Hu Tang',
    category: 'Formulas that Harmonize',
    summary: 'Separate a pure Shaoyang pivot disorder from Shaoyang disease complicated by Yangming bowel excess.',
    search: 'xiao chai hu tang da chai hu tang minor major bupleurum shaoyang yangming constipation abdominal fullness da huang zhi shi',
    en: {
      eyebrow: 'Formula comparison', names: ['Xiao Chai Hu Tang', 'Da Chai Hu Tang'],
      shared: 'Both harmonize Shaoyang and may show alternating chills and fever, hypochondriac fullness, bitter taste, nausea and a wiry pulse.',
      fast: ['Primarily Shaoyang: alternating chills and fever without a strong bowel-excess pattern.', 'Shaoyang plus Yangming excess: abdominal or epigastric fullness and pain, persistent vomiting or constipation.'],
      rows: [
        ['Pattern depth', 'Half-exterior/half-interior Shaoyang disorder.', 'Shaoyang disorder combined with interior excess.'],
        ['Distinct action', 'Harmonizes the Shaoyang pivot and supports upright Qi.', 'Harmonizes Shaoyang while draining interior Heat and accumulation.'],
        ['Decisive clue', 'Alternating chills and fever dominate.', 'Constipation and abdominal fullness/pain join the Shaoyang signs.'],
        ['Formula construction', 'Contains Qi-supporting Ren Shen and Gan Cao.', 'Adds Da Huang and Zhi Shi to drain and move the interior.'],
        ['Exam shortcut', 'Shaoyang only.', 'Shaoyang + Yangming bowel excess.']
      ],
      why: 'The larger formula is not merely a stronger Xiao Chai Hu Tang; it adds a second disease location that requires downward drainage.',
      memory: '<strong>Xiao turns the pivot;</strong> <strong>Da turns the pivot and opens the bowel.</strong>',
      safety: 'Da Chai Hu Tang has a purgative component and is not for self-treatment. Severe abdominal pain or suspected obstruction requires urgent assessment.',
      prompts: ['Which formula treats a primarily Shaoyang pattern?', 'Which adds Da Huang and Zhi Shi?', 'What Yangming signs justify choosing Da Chai Hu Tang?'],
      source: 'Source bank: FWB 4.2 series and formula chapter comparisons; corroborated with individual formula profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Xiao Chai Hu Tang', 'Da Chai Hu Tang'],
      shared: 'Ambas armonizan Shaoyang y pueden mostrar escalofríos-fiebre alternantes, plenitud hipocondríaca, sabor amargo, náusea y pulso de cuerda.',
      fast: ['Principalmente Shaoyang: alternancia sin patrón fuerte de exceso intestinal.', 'Shaoyang más exceso Yangming: plenitud y dolor abdominal/epigástrico, vómito persistente o estreñimiento.'],
      rows: [
        ['Profundidad', 'Trastorno Shaoyang mitad Exterior-mitad Interior.', 'Shaoyang combinado con exceso Interior.'],
        ['Acción distintiva', 'Armoniza la bisagra Shaoyang y apoya Qi correcto.', 'Armoniza Shaoyang mientras drena Calor y acumulación Interior.'],
        ['Pista decisiva', 'Predominan escalofríos y fiebre alternantes.', 'Estreñimiento y plenitud/dolor abdominal acompañan los signos Shaoyang.'],
        ['Construcción', 'Contiene Ren Shen y Gan Cao para apoyar Qi.', 'Añade Da Huang y Zhi Shi para drenar y mover Interior.'],
        ['Atajo de examen', 'Solo Shaoyang.', 'Shaoyang + exceso intestinal Yangming.']
      ],
      why: 'La fórmula mayor no es solo una versión más fuerte: añade una segunda localización que requiere drenaje descendente.',
      memory: '<strong>Xiao gira la bisagra;</strong> <strong>Da gira la bisagra y abre el intestino.</strong>',
      safety: 'Da Chai Hu Tang tiene componente purgante y no es para automedicación. Dolor abdominal grave o sospecha de obstrucción exige evaluación urgente.',
      prompts: ['¿Cuál trata principalmente Shaoyang?', '¿Cuál añade Da Huang y Zhi Shi?', '¿Qué signos Yangming justifican Da Chai Hu Tang?'],
      source: 'Banco fuente: serie FWB 4.2 y comparaciones de capítulos; corroborado con perfiles individuales.'
    }
  },
  {
    slug: 'si-wu-tang-vs-xiao-yao-san',
    title: 'Si Wu Tang vs. Xiao Yao San',
    category: 'Tonify Blood / Harmonize Liver and Spleen',
    summary: 'Separate a foundational Blood-deficiency formula from Liver-Qi constraint with Spleen deficiency and secondary Blood deficiency.',
    search: 'si wu tang xiao yao san four substance rambling powder blood deficiency liver qi constraint spleen deficiency menstruation emotional rib side',
    en: {
      eyebrow: 'Formula comparison', names: ['Si Wu Tang', 'Xiao Yao San'],
      shared: 'Both nourish Liver Blood and contain Dang Gui and Bai Shao; both are commonly tested in menstrual presentations.',
      fast: ['Blood deficiency is primary: pale complexion, dizziness, scant or irregular menses and a thin pulse.', 'Liver-Qi constraint is primary: emotional symptoms, rib-side distention, PMS and digestive weakness.'],
      rows: [
        ['Primary strategy', 'Nourish and regulate Blood.', 'Spread Liver Qi, strengthen Spleen and nourish Blood.'],
        ['Root pattern', 'Blood deficiency, sometimes with mild Blood stasis.', 'Liver constraint overacting on a deficient Spleen with insufficient Blood.'],
        ['Decisive clue', 'Pale, dry and undernourished Blood signs dominate.', 'Mood changes, sighing, rib-side or breast distention and appetite changes dominate.'],
        ['Pulse', 'Thin or choppy.', 'Wiry and weak.'],
        ['Exam shortcut', 'Build Blood directly.', 'Move Liver Qi while protecting Qi and Blood.']
      ],
      why: 'Menstrual irregularity is shared. Emotional and digestive constraint points to Xiao Yao San; uncomplicated Blood deficiency points to Si Wu Tang.',
      memory: '<strong>Si Wu fills the Blood;</strong> <strong>Xiao Yao frees the Liver and feeds the Blood.</strong>',
      safety: 'Heavy, unexplained or pregnancy-related bleeding requires medical evaluation rather than formula selection alone.',
      prompts: ['Which formula is the foundational Blood tonic?', 'Which combines Liver-Qi constraint with Spleen deficiency?', 'Which clues make menstrual irregularity a Xiao Yao San pattern?'],
      source: 'Source bank: FWB 8.2 series, Comparison Formulas to Tonify; corroborated with Classic Formulas profiles and individual formula files.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Si Wu Tang', 'Xiao Yao San'],
      shared: 'Ambas nutren Sangre de Hígado y contienen Dang Gui y Bai Shao; ambas se evalúan frecuentemente en cuadros menstruales.',
      fast: ['Predomina deficiencia de Sangre: tez pálida, mareo, menstruación escasa/irregular y pulso fino.', 'Predomina estancamiento de Qi de Hígado: síntomas emocionales, distensión costal, SPM y debilidad digestiva.'],
      rows: [
        ['Estrategia principal', 'Nutrir y regular Sangre.', 'Difundir Qi de Hígado, fortalecer Bazo y nutrir Sangre.'],
        ['Patrón raíz', 'Deficiencia de Sangre, a veces con estasis leve.', 'Estancamiento de Hígado que afecta Bazo deficiente con Sangre insuficiente.'],
        ['Pista decisiva', 'Predominan signos pálidos, secos y desnutridos de Sangre.', 'Predominan cambios de ánimo, suspiros, distensión costal/mamaria y cambios de apetito.'],
        ['Pulso', 'Fino o áspero.', 'De cuerda y débil.'],
        ['Atajo de examen', 'Construir Sangre directamente.', 'Mover Qi de Hígado protegiendo Qi y Sangre.']
      ],
      why: 'La irregularidad menstrual es compartida. Estancamiento emocional-digestivo señala Xiao Yao; deficiencia sencilla de Sangre señala Si Wu.',
      memory: '<strong>Si Wu llena Sangre;</strong> <strong>Xiao Yao libera Hígado y alimenta Sangre.</strong>',
      safety: 'Sangrado abundante, inexplicado o relacionado con embarazo requiere evaluación médica.',
      prompts: ['¿Cuál es el tónico base de Sangre?', '¿Cuál combina estancamiento de Hígado con deficiencia de Bazo?', '¿Qué pistas convierten irregularidad menstrual en patrón Xiao Yao?'],
      source: 'Banco fuente: serie FWB 8.2, Comparison Formulas to Tonify; corroborado con perfiles Classic Formulas y archivos individuales.'
    }
  },
  {
    slug: 'bu-zhong-yi-qi-tang-vs-gui-pi-tang',
    title: 'Bu Zhong Yi Qi Tang vs. Gui Pi Tang',
    category: 'Formulas that Tonify Qi',
    summary: 'Separate sinking Middle Qi with prolapse from Spleen-Qi and Heart-Blood deficiency with Spirit or bleeding signs.',
    search: 'bu zhong yi qi tang gui pi tang tonify middle qi restore spleen qi heart blood sinking prolapse bleeding insomnia palpitations',
    en: {
      eyebrow: 'Formula comparison', names: ['Bu Zhong Yi Qi Tang', 'Gui Pi Tang'],
      shared: 'Both tonify Spleen Qi and may treat fatigue, poor appetite, loose stools and pale weakness.',
      fast: ['Middle Qi sinks: prolapse, chronic diarrhea, bearing-down sensation or Qi-deficiency fever.', 'Spleen Qi fails to make/hold Blood and Heart Blood is deficient: insomnia, palpitations, poor memory or bleeding.'],
      rows: [
        ['Primary direction', 'Raises clear Yang and lifts sunken Qi.', 'Tonifies Spleen Qi, nourishes Heart Blood and calms the Spirit.'],
        ['Decisive clue', 'Prolapse or bearing-down symptoms.', 'Spirit disturbance or abnormal bleeding.'],
        ['Typical signs', 'Fatigue worse with exertion, weak voice, spontaneous sweating, chronic diarrhea.', 'Fatigue, poor appetite, insomnia, palpitations, forgetfulness, easy bruising.'],
        ['Exam shortcut', 'Qi is sinking.', 'Qi is not producing or containing Blood.']
      ],
      why: 'Both start from Spleen-Qi deficiency. The downstream failure—sinking versus Heart-Blood deficiency/bleeding—chooses the formula.',
      memory: '<strong>Bu Zhong lifts;</strong> <strong>Gui Pi builds and holds Blood.</strong>',
      safety: 'New prolapse, unexplained bleeding, black stools or severe anemia signs require medical evaluation.',
      prompts: ['Which formula treats sinking Middle Qi?', 'Which combines insomnia with Spleen deficiency?', 'Which is selected when bleeding reflects failure of Qi to hold Blood?'],
      source: 'Source bank: FWB 8.2 series, Comparison Formulas to Tonify; corroborated with the Classic Formulas profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Bu Zhong Yi Qi Tang', 'Gui Pi Tang'],
      shared: 'Ambas tonifican Qi de Bazo y pueden tratar fatiga, poco apetito, heces blandas y debilidad pálida.',
      fast: ['Qi Medio se hunde: prolapso, diarrea crónica, sensación de descenso o fiebre por deficiencia de Qi.', 'Qi de Bazo no produce/contiene Sangre y Sangre de Corazón es deficiente: insomnio, palpitaciones, mala memoria o sangrado.'],
      rows: [
        ['Dirección principal', 'Eleva Yang claro y levanta Qi hundido.', 'Tonifica Qi de Bazo, nutre Sangre de Corazón y calma Shen.'],
        ['Pista decisiva', 'Prolapso o sensación de descenso.', 'Alteración de Shen o sangrado anormal.'],
        ['Signos típicos', 'Fatiga peor con esfuerzo, voz débil, sudor espontáneo, diarrea crónica.', 'Fatiga, poco apetito, insomnio, palpitaciones, olvido, moretones fáciles.'],
        ['Atajo de examen', 'Qi se hunde.', 'Qi no produce o no contiene Sangre.']
      ],
      why: 'Ambas parten de deficiencia de Qi de Bazo. La falla resultante—hundimiento frente a deficiencia de Sangre/sangrado—elige la fórmula.',
      memory: '<strong>Bu Zhong eleva;</strong> <strong>Gui Pi construye y contiene Sangre.</strong>',
      safety: 'Prolapso nuevo, sangrado inexplicado, heces negras o signos de anemia grave requieren evaluación médica.',
      prompts: ['¿Cuál trata Qi Medio hundido?', '¿Cuál combina insomnio con deficiencia de Bazo?', '¿Cuál se elige cuando Qi no contiene Sangre?'],
      source: 'Banco fuente: serie FWB 8.2, Comparison Formulas to Tonify; corroborado con perfiles Classic Formulas.'
    }
  },
  {
    slug: 'zuo-gui-wan-vs-you-gui-wan',
    title: 'Zuo Gui Wan vs. You Gui Wan',
    category: 'Formulas that Tonify',
    summary: 'Use the Left/Right name pair to separate Kidney Yin-Essence deficiency from Kidney Yang and gate-of-vitality Fire deficiency.',
    search: 'zuo gui wan you gui wan restore left right kidney yin essence yang ming men fire cold heat fu zi rou gui',
    en: {
      eyebrow: 'Formula name pair', names: ['Zuo Gui Wan', 'You Gui Wan'],
      shared: 'Both are rich Kidney-tonifying formulas for deficiency of Essence, low-back weakness, dizziness, tinnitus and reproductive weakness.',
      fast: ['“Restore the Left”: Kidney Yin and Essence deficiency with dryness or deficiency Heat.', '“Restore the Right”: Kidney Yang and gate-of-vitality Fire deficiency with Cold signs.'],
      rows: [
        ['Primary direction', 'Enriches Kidney Yin, Essence and marrow.', 'Warms and tonifies Kidney Yang while replenishing Essence and Blood.'],
        ['Decisive signs', 'Night sweats, dry mouth, heat sensations, seminal emission, red scant-coated tongue.', 'Cold limbs, aversion to cold, impotence, infertility, edema or clear copious urine.'],
        ['Formula clue', 'Uses rich Yin/Essence tonics without Fu Zi or Rou Gui.', 'Includes Fu Zi and Rou Gui to restore Ming Men Fire.'],
        ['Exam shortcut', 'Left = Yin and Essence.', 'Right = Yang and Fire.']
      ],
      why: 'The pair shares deep Kidney supplementation, so temperature is decisive: dryness/empty Heat points left; Cold and failed transformation point right.',
      memory: '<strong>Zuo/Left stores Yin;</strong> <strong>You/Right restores Yang.</strong>',
      safety: 'You Gui Wan contains Fu Zi and requires processed material, reliable sourcing and qualified professional supervision. Neither formula suits Dampness or digestive weakness without modification.',
      prompts: ['Which formula enriches Kidney Yin and marrow?', 'Which restores Ming Men Fire?', 'Which ingredient pair makes You Gui Wan distinctly warming?'],
      source: 'Source bank: FWB 8.2 series and individual formula files; corroborated with <em>Chinese Herbal Medicine: Formulas &amp; Strategies</em>, 2nd ed.'
    },
    es: {
      eyebrow: 'Pareja de nombres de fórmulas', names: ['Zuo Gui Wan', 'You Gui Wan'],
      shared: 'Ambas tonifican profundamente Riñón en deficiencia de Esencia, debilidad lumbar, mareo, tinnitus y debilidad reproductiva.',
      fast: ['“Restaurar la Izquierda”: deficiencia de Yin y Esencia de Riñón con Sequedad o Calor vacío.', '“Restaurar la Derecha”: deficiencia de Yang y Fuego de Ming Men con signos de Frío.'],
      rows: [
        ['Dirección principal', 'Enriquece Yin de Riñón, Esencia y médula.', 'Calienta y tonifica Yang de Riñón mientras repone Esencia y Sangre.'],
        ['Signos decisivos', 'Sudor nocturno, boca seca, sensación de Calor, emisión seminal, lengua roja con poca capa.', 'Extremidades frías, aversión al frío, impotencia, infertilidad, edema u orina clara abundante.'],
        ['Pista de fórmula', 'Usa tónicos ricos de Yin/Esencia sin Fu Zi ni Rou Gui.', 'Incluye Fu Zi y Rou Gui para restaurar Fuego de Ming Men.'],
        ['Atajo de examen', 'Izquierda = Yin y Esencia.', 'Derecha = Yang y Fuego.']
      ],
      why: 'Comparten suplementación profunda de Riñón; decide la temperatura: Sequedad/Calor vacío señala izquierda, Frío y transformación fallida señala derecha.',
      memory: '<strong>Zuo/Izquierda almacena Yin;</strong> <strong>You/Derecha restaura Yang.</strong>',
      safety: 'You Gui Wan contiene Fu Zi y requiere material procesado, fuente confiable y supervisión profesional. Ninguna corresponde a Humedad o debilidad digestiva sin modificación.',
      prompts: ['¿Cuál enriquece Yin de Riñón y médula?', '¿Cuál restaura Fuego de Ming Men?', '¿Qué pareja de ingredientes hace a You Gui Wan claramente caliente?'],
      source: 'Banco fuente: serie FWB 8.2 y archivos individuales; corroborado con <em>Chinese Herbal Medicine: Formulas &amp; Strategies</em>, 2.ª ed.'
    }
  },
  {
    slug: 'xue-fu-zhu-yu-tang-vs-bu-yang-huan-wu-tang',
    title: 'Xue Fu Zhu Yu Tang vs. Bu Yang Huan Wu Tang',
    category: 'Formulas that Invigorate Blood',
    summary: 'Separate Qi stagnation and Blood stasis in the chest from Blood stasis caused by severe Qi deficiency after Wind-stroke.',
    search: 'xue fu zhu yu tang bu yang huan wu tang drive out stasis mansion blood tonify yang restore five chest blood stasis qi deficiency hemiplegia stroke',
    en: {
      eyebrow: 'Formula comparison', names: ['Xue Fu Zhu Yu Tang', 'Bu Yang Huan Wu Tang'],
      shared: 'Both invigorate Blood and dispel stasis, but the Qi pattern, location and clinical setting differ.',
      fast: ['Qi stagnation and Blood stasis above the diaphragm: fixed chest pain, headache, irritability or dark complexion.', 'Severe Qi deficiency cannot move Blood: chronic post-stroke hemiplegia, weakness, numbness or facial paralysis.'],
      rows: [
        ['Root mechanism', 'Qi stagnation drives Blood stasis.', 'Qi deficiency fails to propel Blood.'],
        ['Location', 'Chest and upper burner.', 'Channels and collaterals, often one side of the body.'],
        ['Decisive clue', 'Fixed stabbing chest pain with emotional or upper-body stasis signs.', 'Weak, flaccid post-stroke deficits with a deficient pulse.'],
        ['Therapeutic emphasis', 'Move Qi and Blood and relieve pain.', 'Strongly tonify Qi so Blood can move through the channels.'],
        ['Exam shortcut', 'Stagnant and painful.', 'Deficient and weak.']
      ],
      why: 'Both move Blood, but one disperses an excess-like obstruction while the other supplies Qi to move stasis in a profoundly deficient patient.',
      memory: '<strong>Xue Fu opens the chest mansion;</strong> <strong>Bu Yang supplies the power to move a weak side.</strong>',
      safety: 'New chest pain or any new stroke sign is an emergency. These formulas are not substitutes for acute cardiac or stroke care; anticoagulant use requires professional review.',
      prompts: ['Which formula targets Blood stasis in the chest?', 'Which targets chronic post-stroke weakness from Qi deficiency?', 'How does the pulse help distinguish the mechanisms?'],
      source: 'Source bank: FWB 10.1 series, Comparison Formulas to Invigorate the Blood; corroborated with individual formula profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Xue Fu Zhu Yu Tang', 'Bu Yang Huan Wu Tang'],
      shared: 'Ambas vigorizan Sangre y dispersan estasis, pero difieren el patrón de Qi, localización y contexto clínico.',
      fast: ['Estancamiento de Qi y estasis de Sangre sobre diafragma: dolor fijo de pecho, cefalea, irritabilidad o tez oscura.', 'Deficiencia grave de Qi no mueve Sangre: hemiplejia crónica postictus, debilidad, entumecimiento o parálisis facial.'],
      rows: [
        ['Mecanismo raíz', 'Estancamiento de Qi impulsa estasis de Sangre.', 'Deficiencia de Qi no impulsa Sangre.'],
        ['Localización', 'Pecho y Jiao Superior.', 'Canales y colaterales, a menudo un lado del cuerpo.'],
        ['Pista decisiva', 'Dolor fijo punzante de pecho con signos emocionales o superiores de estasis.', 'Déficits flácidos y débiles postictus con pulso deficiente.'],
        ['Énfasis terapéutico', 'Mover Qi y Sangre y aliviar dolor.', 'Tonificar fuertemente Qi para que Sangre recorra canales.'],
        ['Atajo de examen', 'Estancado y doloroso.', 'Deficiente y débil.']
      ],
      why: 'Ambas mueven Sangre; una dispersa obstrucción tipo exceso y la otra aporta Qi para mover estasis en un paciente profundamente deficiente.',
      memory: '<strong>Xue Fu abre la mansión del pecho;</strong> <strong>Bu Yang aporta fuerza para mover el lado débil.</strong>',
      safety: 'Dolor de pecho nuevo o cualquier signo nuevo de ictus es emergencia. No sustituyen atención aguda; anticoagulantes requieren revisión profesional.',
      prompts: ['¿Cuál se dirige a estasis en pecho?', '¿Cuál trata debilidad crónica postictus por deficiencia de Qi?', '¿Cómo ayuda el pulso?'],
      source: 'Banco fuente: serie FWB 10.1, Comparison Formulas to Invigorate the Blood; corroborado con perfiles individuales.'
    }
  },
  {
    slug: 'bao-he-wan-vs-jian-pi-wan',
    title: 'Bao He Wan vs. Jian Pi Wan',
    category: 'Formulas that Reduce Food Stagnation',
    summary: 'Separate acute excess Food Stagnation from chronic Food Stagnation rooted in Spleen-Qi deficiency.',
    search: 'bao he wan jian pi wan preserve harmony strengthen spleen food stagnation excess deficiency acute chronic sour belching loose stool fatigue',
    en: {
      eyebrow: 'Formula comparison', names: ['Bao He Wan', 'Jian Pi Wan'],
      shared: 'Both reduce Food Stagnation and may treat epigastric fullness, poor appetite, belching, nausea and an irregular stool pattern.',
      fast: ['Acute excess Food Stagnation: distention, sour belching, foul breath, nausea and a thick greasy coat.', 'Chronic stagnation from Spleen deficiency: poor appetite, fatigue, loose stools and a pale tongue.'],
      rows: [
        ['Primary strategy', 'Reduce food, guide out stagnation and harmonize the Stomach.', 'Strengthen Spleen Qi while gradually reducing accumulated food.'],
        ['Constitution', 'Relatively robust, excess presentation.', 'Deficient, chronic presentation.'],
        ['Decisive clue', 'Recent dietary excess with strong stagnation signs.', 'Long-standing weak digestion with recurring stagnation.'],
        ['Tongue and pulse', 'Thick greasy coat; slippery and full.', 'Pale tongue; weak pulse, possibly slippery.'],
        ['Exam shortcut', 'Too much food.', 'Too little digestive power.']
      ],
      why: 'Both can show fullness after eating. Acute intensity points to Bao He Wan; fatigue and chronic Spleen weakness point to Jian Pi Wan.',
      memory: '<strong>Bao He clears the traffic jam;</strong> <strong>Jian Pi repairs the digestive engine.</strong>',
      safety: 'Persistent vomiting, severe abdominal pain, weight loss, blood in stool or inability to pass stool/gas requires medical evaluation.',
      prompts: ['Which formula treats acute food excess?', 'Which treats chronic stagnation with Spleen-Qi deficiency?', 'Which tongue and pulse indicate Jian Pi Wan?'],
      source: 'Source bank: FWB 15.1, Comparison Formulas to Reduce Food Stagnation; corroborated with formula chapters and practice materials.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Bao He Wan', 'Jian Pi Wan'],
      shared: 'Ambas reducen estancamiento de alimentos y pueden tratar plenitud epigástrica, poco apetito, eructos, náusea y alteración de heces.',
      fast: ['Estancamiento agudo de exceso: distensión, eructo agrio, mal aliento, náusea y capa gruesa grasosa.', 'Estancamiento crónico por deficiencia de Bazo: poco apetito, fatiga, heces blandas y lengua pálida.'],
      rows: [
        ['Estrategia principal', 'Reducir alimento, guiar estancamiento y armonizar Estómago.', 'Fortalecer Qi de Bazo mientras reduce gradualmente alimento acumulado.'],
        ['Constitución', 'Presentación de exceso relativamente robusta.', 'Presentación deficiente y crónica.'],
        ['Pista decisiva', 'Exceso dietético reciente con signos fuertes de estancamiento.', 'Digestión débil prolongada con estancamiento recurrente.'],
        ['Lengua y pulso', 'Capa gruesa grasosa; resbaladizo y lleno.', 'Lengua pálida; pulso débil, posiblemente resbaladizo.'],
        ['Atajo de examen', 'Demasiada comida.', 'Muy poca fuerza digestiva.']
      ],
      why: 'Ambas pueden dar plenitud posprandial. Intensidad aguda señala Bao He; fatiga y debilidad crónica de Bazo señalan Jian Pi.',
      memory: '<strong>Bao He despeja el atasco;</strong> <strong>Jian Pi repara el motor digestivo.</strong>',
      safety: 'Vómito persistente, dolor abdominal grave, pérdida de peso, sangre en heces o no expulsar heces/gases requiere evaluación médica.',
      prompts: ['¿Cuál trata exceso alimentario agudo?', '¿Cuál trata estancamiento crónico con deficiencia de Bazo?', '¿Qué lengua y pulso señalan Jian Pi?'],
      source: 'Banco fuente: FWB 15.1, Comparison Formulas to Reduce Food Stagnation; corroborado con capítulos y materiales de práctica.'
    }
  },
  {
    slug: 'su-zi-jiang-qi-tang-vs-ding-chuan-tang',
    title: 'Su Zi Jiang Qi Tang vs. Ding Chuan Tang',
    category: 'Formulas that Relieve Coughing and Wheezing',
    summary: 'Separate Cold-Phlegm with deficiency below from Wind-Cold constraining Phlegm-Heat in the Lungs.',
    search: 'su zi jiang qi tang ding chuan tang perilla fruit direct qi downward arrest wheezing cold phlegm kidney deficiency phlegm heat exterior wind cold',
    en: {
      eyebrow: 'Formula comparison', names: ['Su Zi Jiang Qi Tang', 'Ding Chuan Tang'],
      shared: 'Both descend rebellious Lung Qi, transform Phlegm and relieve cough or wheezing; both may contain Su Zi, Ban Xia and Gan Cao.',
      fast: ['Excess above, deficiency below: Cold-Phlegm with weak Kidney grasping and copious thin white sputum.', 'Exterior Wind-Cold with internal Phlegm-Heat: wheezing with thick yellow sputum and mixed Cold/Heat signs.'],
      rows: [
        ['Thermal pattern', 'Cold-Phlegm with lower-burner deficiency.', 'Phlegm-Heat constrained by external Wind-Cold.'],
        ['Sputum clue', 'Copious, thin and white.', 'Copious, thick and yellow.'],
        ['Distinct action', 'Warms the lower burner and helps Kidneys grasp Qi while directing Lung Qi down.', 'Clears Phlegm-Heat, releases external constraint and arrests wheezing.'],
        ['Decisive signs', 'Shortness of breath worse with exertion, cold limbs or low-back weakness.', 'Fever/chills, chest constraint and loud wheezing with Heat signs.'],
        ['Exam shortcut', 'Cold above + deficiency below.', 'Cold outside + Heat-Phlegm inside.']
      ],
      why: 'Both treat wheezing with Phlegm. Sputum color and the lower-burner deficiency signs separate them quickly.',
      memory: '<strong>Su Zi descends cold Qi and supports below;</strong> <strong>Ding Chuan clears hot Phlegm and stops wheeze.</strong>',
      safety: 'Ding Chuan Tang contains Ma Huang and requires cardiovascular and medication screening. Severe or rapidly worsening breathing difficulty is an emergency.',
      prompts: ['Which formula treats Cold-Phlegm with Kidney deficiency?', 'Which treats thick yellow sputum in a mixed Exterior pattern?', 'How does sputum color guide the choice?'],
      source: 'Source bank: FWB 6.2 series, Comparison Formulas to Transform Phlegm; corroborated with individual formula profiles and practice tests.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Su Zi Jiang Qi Tang', 'Ding Chuan Tang'],
      shared: 'Ambas hacen descender Qi rebelde de Pulmón, transforman Flema y alivian tos/sibilancias; pueden compartir Su Zi, Ban Xia y Gan Cao.',
      fast: ['Exceso arriba, deficiencia abajo: Flema-Frío con Riñón débil para captar Qi y esputo blanco fino abundante.', 'Viento-Frío Exterior con Flema-Calor interna: sibilancias, esputo amarillo espeso y signos mixtos.'],
      rows: [
        ['Patrón térmico', 'Flema-Frío con deficiencia de Jiao Inferior.', 'Flema-Calor constreñida por Viento-Frío externo.'],
        ['Pista del esputo', 'Abundante, fino y blanco.', 'Abundante, espeso y amarillo.'],
        ['Acción distintiva', 'Calienta Jiao Inferior y ayuda a Riñón a captar Qi mientras desciende Qi de Pulmón.', 'Elimina Flema-Calor, libera constricción externa y detiene sibilancias.'],
        ['Signos decisivos', 'Disnea peor con esfuerzo, extremidades frías o debilidad lumbar.', 'Fiebre/escalofríos, constricción torácica y sibilancia fuerte con Calor.'],
        ['Atajo de examen', 'Frío arriba + deficiencia abajo.', 'Frío afuera + Flema-Calor adentro.']
      ],
      why: 'Ambas tratan sibilancias con Flema. El color del esputo y signos de deficiencia inferior las separan rápidamente.',
      memory: '<strong>Su Zi desciende Qi frío y apoya abajo;</strong> <strong>Ding Chuan elimina Flema caliente y detiene sibilancia.</strong>',
      safety: 'Ding Chuan Tang contiene Ma Huang y requiere evaluación cardiovascular y farmacológica. Dificultad respiratoria grave o progresiva es emergencia.',
      prompts: ['¿Cuál trata Flema-Frío con deficiencia de Riñón?', '¿Cuál trata esputo amarillo espeso en patrón Exterior mixto?', '¿Cómo guía el color del esputo?'],
      source: 'Banco fuente: serie FWB 6.2, Comparison Formulas to Transform Phlegm; corroborado con perfiles individuales y exámenes de práctica.'
    }
  }
);

function esc(value) {
  return String(value).replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;');
}

function cardHtml(card, language) {
  const data = card[language];
  const spanish = language === 'es';
  const file = spanish ? `${card.slug}-es.html` : `${card.slug}.html`;
  const alternate = spanish ? `${card.slug}.html` : `${card.slug}-es.html`;
  const headings = spanish ? {
    brand: 'Comparaciones de Fórmulas', back: '← Volver a Fórmulas', alt: 'English',
    tip: 'Consejo de estudio · Pistas para responder', clue: 'Pista', shared: 'Pista compartida', fast: 'Diferencia rápida',
    key: 'Clave comparativa', compare: 'Comparar', focus: 'Guía de estudio · Enfoque del examen',
    why: 'Por qué importa', memory: 'Regla mnemotécnica', safety: 'Seguridad', source: 'Fuente',
    prompts: 'Posibles preguntas de examen · Repaso', footer: 'Nota Comparativa de Fórmulas independiente · Los Exam Keys y el feedback de los Mock permanecen en Exam Key.'
  } : {
    brand: 'Formula Comparisons', back: '← Back to Formulas', alt: 'Español',
    tip: 'Study tip · Clues to help answer', clue: 'Clue', shared: 'Shared clue', fast: 'Fast distinction',
    key: 'Comparative answer key', compare: 'Compare', focus: 'Study guide · Exam focus',
    why: 'Why it matters', memory: 'Memory cue', safety: 'Safety', source: 'Source',
    prompts: 'Possible exam questions · Study prompts', footer: 'Independent Formula Comparison · Exam keys and Mock feedback remain in Exam Key.'
  };
  const langCode = spanish ? 'es' : 'en';
  const index = spanish ? 'formulas-es.html' : 'formulas.html';
  const cells = data.names.map(name => `<th scope="col">${esc(name)}</th>`).join('');
  const fastCells = data.fast.map(value => `<td>${value}</td>`).join('');
  const rows = data.rows.map(row => `<tr><th scope="row">${esc(row[0])}</th>${row.slice(1).map(value => `<td>${value}</td>`).join('')}</tr>`).join('\n        ');
  const prompts = data.prompts.map(prompt => `<li>${esc(prompt)}</li>`).join('');
  return `<!doctype html>
<html lang="${langCode}"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="description" content="${esc(card.title)} — ${spanish ? 'comparación de fórmulas' : 'formula comparison'}."><title>${esc(card.title)} | AcupunctureTests</title><link rel="alternate" hreflang="${spanish ? 'en' : 'es'}" href="${alternate}"><link rel="stylesheet" href="comparisons.css?v=${cssVersion}"></head>
<body><header class="comparison-topbar"><div class="comparison-shell comparison-topbar__inner" style="flex-wrap:wrap;padding:10px 0"><a class="comparison-brand" href="${index}"><span class="comparison-brand__mark" aria-hidden="true">☯</span><span>${headings.brand}</span></a><span style="display:flex;flex-wrap:wrap;gap:8px"><a class="comparison-back" hreflang="${spanish ? 'en' : 'es'}" href="${alternate}">${headings.alt}</a><a class="comparison-back" href="${index}">${headings.back}</a></span></div></header>
<main class="comparison-shell"><article class="study-note">
  <p class="comparison-eyebrow">${data.eyebrow}</p><h1 class="study-note__title">${esc(card.title)}</h1>
  <section class="study-note__block" aria-labelledby="clues-title"><h2 class="study-note__heading" id="clues-title">${headings.tip}</h2><table><thead><tr><th scope="col">${headings.clue}</th>${cells}</tr></thead><tbody>
    <tr class="shared-row"><th scope="row">${headings.shared}</th><td colspan="${data.names.length}">${data.shared}</td></tr>
    <tr><th scope="row">${headings.fast}</th>${fastCells}</tr>
  </tbody></table></section>
  <section class="study-note__block" aria-labelledby="answer-key-title"><h2 class="study-note__heading study-note__heading--orange" id="answer-key-title">${headings.key}</h2><table><thead><tr><th scope="col">${headings.compare}</th>${cells}</tr></thead><tbody>
        ${rows}
  </tbody></table></section>
  <section class="study-note__block" aria-labelledby="focus-title"><h2 class="study-note__heading study-note__heading--purple" id="focus-title">${headings.focus}</h2><table class="study-note__focus"><tbody>
    <tr><th scope="row">${headings.why}</th><td>${data.why}</td></tr>
    <tr><th scope="row">${headings.memory}</th><td>${data.memory}</td></tr>
    <tr><th scope="row">${headings.safety}</th><td>${data.safety}</td></tr>
    <tr><th scope="row">${headings.source}</th><td>${data.source}</td></tr>
  </tbody></table></section>
  <section class="study-note__block" aria-labelledby="prompts-title"><h2 class="study-note__heading study-note__heading--green" id="prompts-title">${headings.prompts}</h2><ul class="study-note__prompts">${prompts}</ul></section>
</article></main><footer class="comparison-footer"><div class="comparison-shell">${headings.footer}</div></footer></body></html>
`;
}

function indexHtml(language) {
  const spanish = language === 'es';
  const cardsHtml = cards.map(card => `<article class="comparison-card" data-type="formulas" data-search="${esc(card.search)}">
          <span class="comparison-card__type">${spanish ? 'Fórmulas' : 'Formulas'}</span><h3>${esc(card.title)}</h3><p>${esc(spanish ? card.es.why : card.summary)}</p><div class="comparison-card__category"><strong>${spanish ? 'Categoría:' : 'Category:'}</strong><br>${esc(card.category)}</div><a class="comparison-card__open" href="${card.slug}${spanish ? '-es' : ''}.html">${spanish ? 'Abrir Nota de Estudio →' : 'Open Study Note →'}</a>
        </article>`).join('\n\n        ');
  return `<!doctype html>
<html lang="${spanish ? 'es' : 'en'}"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="description" content="${spanish ? 'Comparaciones fundamentales de fórmulas de medicina china.' : 'Fundamental Chinese medicine formula comparisons.'}"><title>${spanish ? 'Comparaciones de Fórmulas' : 'Formula Comparisons'} | AcupunctureTests</title><link rel="alternate" hreflang="${spanish ? 'en' : 'es'}" href="formulas${spanish ? '' : '-es'}.html"><link rel="stylesheet" href="comparisons.css?v=${cssVersion}"><script defer src="comparisons.js?v=20261025-1"></script></head>
<body><header class="comparison-topbar"><div class="comparison-shell comparison-topbar__inner"><a class="comparison-brand" href="index.html"><span class="comparison-brand__mark" aria-hidden="true">☯</span><span>AcupunctureTests</span></a><span style="display:flex;flex-wrap:wrap;gap:8px"><a class="comparison-back" href="formulas${spanish ? '' : '-es'}.html">${spanish ? 'English' : 'Español'}</a><a class="comparison-back" href="index.html">${spanish ? '← Hierbas individuales' : '← Single Herbs'}</a></span></div></header>
<main class="comparison-shell"><section class="comparison-hero"><p class="comparison-eyebrow">${spanish ? 'Grupo 2' : 'Group 2'}</p><h1>${spanish ? 'Comparaciones de Fórmulas' : 'Formula Comparisons'}</h1><p>${spanish ? 'Diferencias diagnósticas y terapéuticas de alto rendimiento para evitar las confusiones más frecuentes en examen.' : 'High-yield diagnostic and therapeutic distinctions designed to prevent the most common exam confusions.'}</p></section>
<nav class="comparison-collection-links" aria-label="${spanish ? 'Grupos de estudio' : 'Study groups'}"><a href="index.html">${spanish ? 'Hierbas individuales' : 'Single Herbs'}</a><a aria-current="page" href="formulas${spanish ? '-es' : ''}.html">${spanish ? 'Fórmulas' : 'Formulas'}</a></nav>
<section class="comparison-tools" aria-label="${spanish ? 'Buscar comparaciones de fórmulas' : 'Search Formula Comparisons'}"><label><span class="sr-only">${spanish ? 'Buscar' : 'Search'}</span><input class="comparison-search" type="search" placeholder="${spanish ? 'Buscar fórmulas o categorías' : 'Search formulas or categories'}" data-comparison-search></label></section>
<section><header class="comparison-summary"><h2>${spanish ? 'Notas disponibles' : 'Available notes'}</h2><p><span data-visible-count>${cards.length}</span> ${spanish ? 'comparaciones de fórmulas' : 'formula comparisons'}</p></header><div class="comparison-grid" data-comparison-grid>
        ${cardsHtml}
      </div><p class="comparison-empty" data-comparison-empty hidden>${spanish ? 'No hay notas que coincidan con esta búsqueda.' : 'No Formula Comparisons match this search.'}</p></section></main>
<footer class="comparison-footer"><div class="comparison-shell">${spanish ? 'Material independiente de estudio. Los Exam Keys y el feedback de los Mock permanecen en Exam Key.' : 'Independent study material. Exam keys and Mock feedback remain in Exam Key.'}</div></footer></body></html>`;
}

function updateMasterIndex() {
  const masterPath = resolve(root, 'index.html');
  let html = readFileSync(masterPath, 'utf8');
  const herbCount = (html.match(/data-type="herbs"/g) || []).length;
  const formulaCards = cards.map(card => `        <article class="comparison-card" data-type="formulas" data-search="${esc(card.search)}">
          <span class="comparison-card__type">Formulas</span><h3>${esc(card.title)}</h3><p>${esc(card.summary)}</p><div class="comparison-card__category"><strong>Category:</strong><br>${esc(card.category)}</div><a class="comparison-card__open" href="${card.slug}.html">Open Study Note →</a>
        </article>`).join('\n\n');
  const block = `        <!-- FORMULA-CARDS-START -->\n${formulaCards}\n        <!-- FORMULA-CARDS-END -->`;
  const markerPattern = /        <!-- FORMULA-CARDS-START -->[\s\S]*?        <!-- FORMULA-CARDS-END -->/;
  if (!markerPattern.test(html)) {
    throw new Error('Formula-card markers are missing from comparisons/index.html');
  }
  html = html.replace(markerPattern, block);
  html = html.replace(/<span data-visible-count>\d+<\/span>/, `<span data-visible-count>${herbCount + cards.length}</span>`);
  writeFileSync(masterPath, html);
}

for (const card of cards) {
  writeFileSync(resolve(root, `${card.slug}.html`), cardHtml(card, 'en'));
  writeFileSync(resolve(root, `${card.slug}-es.html`), cardHtml(card, 'es'));
}
writeFileSync(resolve(root, 'formulas.html'), indexHtml('en'));
writeFileSync(resolve(root, 'formulas-es.html'), indexHtml('es'));
updateMasterIndex();

console.log(`Generated ${cards.length * 2} formula cards, 2 indexes and the master listing in ${root}`);
