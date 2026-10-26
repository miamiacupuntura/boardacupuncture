import { writeFileSync } from 'node:fs';
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

cards.push(
  {
    slug: 'yin-qiao-san-vs-sang-ju-yin', title: 'Yin Qiao San vs. Sang Ju Yin',
    category: 'Formulas that Release the Exterior',
    summary: 'Separate stronger early Wind-Heat with sore throat from a milder Wind-Heat presentation centered on cough.',
    search: 'yin qiao san sang ju yin honeysuckle forsythia mulberry chrysanthemum wind heat warm disease sore throat cough exterior',
    en: {
      eyebrow: 'Formula comparison', names: ['Yin Qiao San', 'Sang Ju Yin'],
      shared: 'Both treat the early stage of Wind-Heat or warm disease with fever, mild aversion to wind, thirst and a floating-rapid pulse.',
      fast: ['Stronger Exterior Heat and toxicity: sore throat is prominent.', 'Milder Wind-Heat attacking the Lung: cough is prominent.'],
      rows: [
        ['Primary emphasis', 'Releases Wind-Heat and clears toxic Heat.', 'Disperses Wind-Heat and gently clears Lung Heat to stop cough.'],
        ['Decisive clue', 'Sore throat, stronger fever and thirst.', 'Cough with relatively mild systemic signs.'],
        ['Target', 'Wei level with throat/upper-burner Heat.', 'Lung-centered Wind-Heat.'],
        ['Exam shortcut', 'Throat first.', 'Cough first.']
      ],
      why: 'Both are early Wind-Heat formulas. The symptom that dominates—throat pain or cough—usually decides the answer.',
      memory: '<strong>Yin Qiao clears the throat;</strong> <strong>Sang Ju quiets the cough.</strong>',
      safety: 'High fever, breathing difficulty, dehydration or rapidly worsening symptoms require medical assessment.',
      prompts: ['Which formula emphasizes sore throat?', 'Which is milder and more Lung-centered?', 'What signs are shared?'],
      source: 'Source bank: FWB 1.2, Comparison Formulas to Release the Exterior; corroborated with Classic Formulas profiles and formula chapters.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Yin Qiao San', 'Sang Ju Yin'],
      shared: 'Ambas tratan etapa inicial de Viento-Calor o enfermedad tibia con fiebre, leve aversión al viento, sed y pulso flotante-rápido.',
      fast: ['Calor Exterior y toxicidad más fuertes: predomina dolor de garganta.', 'Viento-Calor más leve atacando Pulmón: predomina tos.'],
      rows: [
        ['Énfasis principal', 'Libera Viento-Calor y elimina Calor tóxico.', 'Dispersa Viento-Calor y enfría suavemente Pulmón para detener tos.'],
        ['Pista decisiva', 'Dolor de garganta, fiebre y sed más fuertes.', 'Tos con signos sistémicos relativamente leves.'],
        ['Objetivo', 'Nivel Wei con Calor de garganta/Jiao Superior.', 'Viento-Calor centrado en Pulmón.'],
        ['Atajo de examen', 'Primero la garganta.', 'Primero la tos.']
      ],
      why: 'Ambas son fórmulas iniciales de Viento-Calor. El síntoma dominante—garganta o tos—suele decidir.',
      memory: '<strong>Yin Qiao limpia garganta;</strong> <strong>Sang Ju calma tos.</strong>',
      safety: 'Fiebre alta, dificultad respiratoria, deshidratación o empeoramiento rápido requieren evaluación médica.',
      prompts: ['¿Cuál enfatiza dolor de garganta?', '¿Cuál es más suave y pulmonar?', '¿Qué signos comparten?'],
      source: 'Banco fuente: FWB 1.2, Comparison Formulas to Release the Exterior; corroborado con perfiles Classic Formulas y capítulos.'
    }
  },
  {
    slug: 'da-qing-long-tang-vs-xiao-qing-long-tang', title: 'Da Qing Long Tang vs. Xiao Qing Long Tang',
    category: 'Formulas that Release the Exterior',
    summary: 'Separate Exterior Wind-Cold with internal Heat from Exterior Wind-Cold with internal Cold fluids.',
    search: 'da qing long tang xiao qing long tang major minor blue green dragon exterior wind cold internal heat cold fluids phlegm shi gao',
    en: {
      eyebrow: 'Formula name pair', names: ['Da Qing Long Tang', 'Xiao Qing Long Tang'],
      shared: 'Both strongly release Exterior Wind-Cold and may present with chills, fever, body aches and absence of sweating.',
      fast: ['Internal Heat: irritability, thirst and stronger fever.', 'Internal Cold fluids: cough, wheezing and profuse thin frothy sputum.'],
      rows: [
        ['Interior component', 'Heat constrained beneath an Exterior Cold pattern.', 'Cold fluids retained in the Lungs and Stomach.'],
        ['Decisive ingredient clue', 'Shi Gao clears internal Heat.', 'Gan Jiang, Xi Xin and Wu Wei Zi warm and contain fluid metabolism.'],
        ['Decisive symptom', 'Restlessness or thirst with no sweating.', 'Watery sputum, retching or inability to lie flat.'],
        ['Exam shortcut', 'Big Dragon = Heat inside.', 'Small Dragon = cold water inside.']
      ],
      why: 'The Exterior signs overlap almost completely. The internal branch—Heat versus Cold fluids—is the real comparison.',
      memory: '<strong>Da Qing Long carries fire;</strong> <strong>Xiao Qing Long carries cold water.</strong>',
      safety: 'Both contain Ma Huang; Xiao Qing Long also requires verified Xi Xin sourcing. Cardiovascular and medication screening is essential.',
      prompts: ['Which formula contains Shi Gao?', 'Which treats thin frothy sputum?', 'What internal pattern distinguishes the two Dragons?'],
      source: 'Source bank: FWB 1.2 and formula chapter comparisons; corroborated with Classic Formulas profiles.'
    },
    es: {
      eyebrow: 'Pareja de nombres de fórmulas', names: ['Da Qing Long Tang', 'Xiao Qing Long Tang'],
      shared: 'Ambas liberan fuertemente Viento-Frío Exterior con escalofríos, fiebre, dolor corporal y ausencia de sudor.',
      fast: ['Calor interno: irritabilidad, sed y fiebre más fuerte.', 'Líquidos Fríos internos: tos, sibilancias y esputo fino-espumoso abundante.'],
      rows: [
        ['Componente Interior', 'Calor constreñido bajo patrón Exterior Frío.', 'Líquidos Fríos retenidos en Pulmón y Estómago.'],
        ['Ingrediente decisivo', 'Shi Gao elimina Calor interno.', 'Gan Jiang, Xi Xin y Wu Wei Zi calientan y contienen líquidos.'],
        ['Síntoma decisivo', 'Inquietud o sed sin sudor.', 'Esputo acuoso, arcadas o no poder acostarse.'],
        ['Atajo de examen', 'Dragón Grande = Calor adentro.', 'Dragón Pequeño = agua fría adentro.']
      ],
      why: 'Los signos Exteriores casi coinciden. La rama interna—Calor frente a líquidos Fríos—es la comparación real.',
      memory: '<strong>Da Qing Long lleva fuego;</strong> <strong>Xiao Qing Long lleva agua fría.</strong>',
      safety: 'Ambas contienen Ma Huang; Xiao Qing Long exige fuente verificada de Xi Xin. Es esencial evaluar corazón y medicamentos.',
      prompts: ['¿Cuál contiene Shi Gao?', '¿Cuál trata esputo fino espumoso?', '¿Qué patrón interno distingue los Dragones?'],
      source: 'Banco fuente: FWB 1.2 y comparaciones de capítulos; corroborado con perfiles Classic Formulas.'
    }
  },
  {
    slug: 'li-zhong-wan-vs-si-ni-tang', title: 'Li Zhong Wan vs. Si Ni Tang',
    category: 'Formulas that Warm the Interior',
    summary: 'Separate deficiency-Cold confined mainly to the middle burner from severe systemic Yang collapse.',
    search: 'li zhong wan si ni tang regulate middle pill frigid extremities spleen stomach cold deficiency yang collapse diarrhea vomiting abdominal pain fu zi',
    en: {
      eyebrow: 'Formula comparison', names: ['Li Zhong Wan', 'Si Ni Tang'],
      shared: 'Both warm internal Cold and may treat abdominal pain, vomiting, diarrhea, absence of thirst and a deep pulse.',
      fast: ['Middle-burner deficiency-Cold: digestive signs dominate.', 'Devastated Yang: severe cold limbs, exhaustion and a minute or faint pulse dominate.'],
      rows: [
        ['Location', 'Spleen and Stomach.', 'Heart-Kidney Yang and the whole body.'],
        ['Primary action', 'Warms and tonifies the middle burner.', 'Restores Yang and rescues collapse.'],
        ['Decisive clue', 'Chronic digestive Cold with abdominal pain relieved by warmth/pressure.', 'Cold extending up the limbs with profound weakness.'],
        ['Exam shortcut', 'Cold center.', 'Failing fire everywhere.']
      ],
      why: 'The digestive symptoms may be identical. Distribution and severity distinguish localized middle-burner Cold from systemic Yang failure.',
      memory: '<strong>Li Zhong warms the center;</strong> <strong>Si Ni rescues the system.</strong>',
      safety: 'Si Ni Tang contains Fu Zi and requires processed material and qualified supervision. Suspected shock is an emergency.',
      prompts: ['Which formula targets Spleen-Stomach Cold?', 'Which rescues Yang collapse?', 'How do pulse and cold distribution distinguish them?'],
      source: 'Source bank: FWB 7.1 series, Comparison Formulas to Warm the Interior; corroborated with individual profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Li Zhong Wan', 'Si Ni Tang'],
      shared: 'Ambas calientan Frío Interno y pueden tratar dolor abdominal, vómito, diarrea, ausencia de sed y pulso profundo.',
      fast: ['Frío por deficiencia de Jiao Medio: predominan signos digestivos.', 'Yang devastado: predominan extremidades muy frías, agotamiento y pulso diminuto/tenue.'],
      rows: [
        ['Localización', 'Bazo y Estómago.', 'Yang de Corazón-Riñón y todo el cuerpo.'],
        ['Acción principal', 'Calienta y tonifica Jiao Medio.', 'Restaura Yang y rescata colapso.'],
        ['Pista decisiva', 'Frío digestivo crónico con dolor mejorado por calor/presión.', 'Frío que asciende por extremidades con debilidad profunda.'],
        ['Atajo de examen', 'Centro frío.', 'Fuego fallando en todo el sistema.']
      ],
      why: 'Los signos digestivos pueden coincidir. Distribución y gravedad separan Frío localizado de falla sistémica de Yang.',
      memory: '<strong>Li Zhong calienta el centro;</strong> <strong>Si Ni rescata el sistema.</strong>',
      safety: 'Si Ni Tang contiene Fu Zi y requiere material procesado y supervisión profesional. Sospecha de shock es emergencia.',
      prompts: ['¿Cuál se dirige a Frío de Bazo-Estómago?', '¿Cuál rescata colapso de Yang?', '¿Cómo distinguen pulso y distribución?'],
      source: 'Banco fuente: serie FWB 7.1, Comparison Formulas to Warm the Interior; corroborado con perfiles individuales.'
    }
  },
  {
    slug: 'ban-xia-xie-xin-tang-vs-li-zhong-wan', title: 'Ban Xia Xie Xin Tang vs. Li Zhong Wan',
    category: 'Harmonize / Warm the Interior',
    summary: 'Separate mixed Cold-Heat binding the epigastrium from uncomplicated middle-burner deficiency-Cold.',
    search: 'ban xia xie xin tang li zhong wan pinellia drain epigastrium regulate middle mixed cold heat spleen stomach deficiency cold focal distention',
    en: {
      eyebrow: 'Formula comparison', names: ['Ban Xia Xie Xin Tang', 'Li Zhong Wan'],
      shared: 'Both can treat epigastric discomfort, nausea, vomiting, diarrhea and Spleen-Stomach disharmony.',
      fast: ['Mixed Cold and Heat: focal epigastric distention with borborygmus, vomiting and diarrhea.', 'Pure deficiency-Cold: dull abdominal pain, watery stool and improvement with warmth/pressure.'],
      rows: [
        ['Mechanism', 'Cold and Heat bind while Stomach Qi rebels and Spleen Qi sinks.', 'Middle-burner Yang/Qi deficiency generates Cold.'],
        ['Therapeutic direction', 'Open the bind, clear Heat and warm Cold simultaneously.', 'Warm and tonify Spleen-Stomach.'],
        ['Decisive clue', 'Painless focal distention below the Heart.', 'Cold pain and weak digestion without Heat signs.'],
        ['Exam shortcut', 'Mixed and knotted.', 'Cold and deficient.']
      ],
      why: 'Vomiting plus diarrhea is shared. Look for the epigastric bind and mixed signs versus a uniformly Cold, weak middle burner.',
      memory: '<strong>Ban Xia Xie Xin unties a mixed knot;</strong> <strong>Li Zhong warms a cold center.</strong>',
      safety: 'Persistent vomiting, black stools, severe pain or dehydration requires medical evaluation.',
      prompts: ['Which formula treats mixed Cold-Heat?', 'Which treats pure middle-burner Cold?', 'What is the focal-distention clue?'],
      source: 'Source bank: FWB 4.2 and FWB 7.1 comparisons; corroborated with individual formula profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Ban Xia Xie Xin Tang', 'Li Zhong Wan'],
      shared: 'Ambas pueden tratar molestia epigástrica, náusea, vómito, diarrea y desarmonía Bazo-Estómago.',
      fast: ['Frío-Calor mixtos: distensión epigástrica focal con borborigmos, vómito y diarrea.', 'Frío por deficiencia puro: dolor sordo, heces acuosas y mejoría con calor/presión.'],
      rows: [
        ['Mecanismo', 'Frío y Calor se ligan mientras Qi de Estómago rebela y Qi de Bazo desciende.', 'Deficiencia de Yang/Qi de Jiao Medio genera Frío.'],
        ['Dirección', 'Abrir ligadura, eliminar Calor y calentar Frío simultáneamente.', 'Calentar y tonificar Bazo-Estómago.'],
        ['Pista decisiva', 'Distensión focal indolora bajo Corazón.', 'Dolor frío y digestión débil sin signos de Calor.'],
        ['Atajo de examen', 'Mixto y anudado.', 'Frío y deficiente.']
      ],
      why: 'Vómito más diarrea es compartido. Busca ligadura epigástrica y signos mixtos frente a Jiao Medio uniformemente frío-débil.',
      memory: '<strong>Ban Xia Xie Xin desata nudo mixto;</strong> <strong>Li Zhong calienta centro frío.</strong>',
      safety: 'Vómito persistente, heces negras, dolor grave o deshidratación requieren evaluación médica.',
      prompts: ['¿Cuál trata Frío-Calor mixtos?', '¿Cuál trata Frío puro del centro?', '¿Qué significa la distensión focal?'],
      source: 'Banco fuente: comparaciones FWB 4.2 y FWB 7.1; corroborado con perfiles individuales.'
    }
  },
  {
    slug: 'ban-xia-hou-po-tang-vs-yue-ju-wan', title: 'Ban Xia Hou Po Tang vs. Yue Ju Wan',
    category: 'Formulas that Regulate Qi',
    summary: 'Separate Qi-Phlegm binding in the throat from a broader pattern of six stagnations.',
    search: 'ban xia hou po tang yue ju wan pinellia magnolia escape restraint plum pit qi six stagnations qi blood phlegm fire food damp',
    en: {
      eyebrow: 'Formula comparison', names: ['Ban Xia Hou Po Tang', 'Yue Ju Wan'],
      shared: 'Both move constrained Qi and can treat emotional constraint with chest or epigastric discomfort.',
      fast: ['Qi and Phlegm bind in the throat: “plum-pit Qi,” globus sensation, cough or nausea.', 'Six stagnations: Qi, Blood, Phlegm, Fire, Food and Dampness with broad digestive/emotional constraint.'],
      rows: [
        ['Primary mechanism', 'Qi stagnation generates Phlegm that lodges in throat/chest.', 'Qi stagnation drives several interlocking forms of constraint.'],
        ['Decisive clue', 'Sensation of something stuck in the throat that cannot be swallowed or coughed out.', 'Distention, pain, depression and digestive stagnation without a defining throat sensation.'],
        ['Therapeutic emphasis', 'Move Qi, transform Phlegm and direct Qi downward.', 'Move Qi and relieve the six constraints.'],
        ['Exam shortcut', 'One knot in the throat.', 'Many knots throughout digestion and emotion.']
      ],
      why: 'Both regulate Qi, but Ban Xia Hou Po Tang has a precise throat/Phlegm hallmark; Yue Ju Wan is broader and multi-stagnation.',
      memory: '<strong>Ban Xia Hou Po clears the plum pit;</strong> <strong>Yue Ju opens six restraints.</strong>',
      safety: 'Persistent or progressive difficulty swallowing, weight loss, choking or a neck mass requires medical evaluation.',
      prompts: ['Which formula treats plum-pit Qi?', 'Which addresses six stagnations?', 'What symptom makes Ban Xia Hou Po Tang specific?'],
      source: 'Source bank: FWB 9.1 series, Comparison Formulas to Regulate the Qi; corroborated with individual formula profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Ban Xia Hou Po Tang', 'Yue Ju Wan'],
      shared: 'Ambas mueven Qi constreñido y tratan restricción emocional con molestia torácica o epigástrica.',
      fast: ['Qi y Flema se ligan en garganta: “Qi de hueso de ciruela”, globo, tos o náusea.', 'Seis estancamientos: Qi, Sangre, Flema, Fuego, Alimento y Humedad con restricción amplia.'],
      rows: [
        ['Mecanismo', 'Estancamiento de Qi genera Flema alojada en garganta/pecho.', 'Estancamiento de Qi impulsa varias formas entrelazadas de restricción.'],
        ['Pista decisiva', 'Algo atorado en garganta que no se traga ni se tose.', 'Distensión, dolor, depresión y estancamiento digestivo sin sensación faríngea definitoria.'],
        ['Énfasis', 'Mover Qi, transformar Flema y hacer descender.', 'Mover Qi y aliviar seis restricciones.'],
        ['Atajo de examen', 'Un nudo en garganta.', 'Muchos nudos digestivos y emocionales.']
      ],
      why: 'Ambas regulan Qi, pero Ban Xia Hou Po tiene marca faríngea/Flema precisa; Yue Ju es amplia y multiestancamiento.',
      memory: '<strong>Ban Xia Hou Po elimina hueso de ciruela;</strong> <strong>Yue Ju abre seis restricciones.</strong>',
      safety: 'Dificultad progresiva al tragar, pérdida de peso, atragantamiento o masa cervical requieren evaluación médica.',
      prompts: ['¿Cuál trata Qi de hueso de ciruela?', '¿Cuál trata seis estancamientos?', '¿Qué síntoma hace específica a Ban Xia Hou Po?'],
      source: 'Banco fuente: serie FWB 9.1, Comparison Formulas to Regulate the Qi; corroborado con perfiles individuales.'
    }
  },
  {
    slug: 'zuo-jin-wan-vs-yu-nu-jian', title: 'Zuo Jin Wan vs. Yu Nu Jian',
    category: 'Formulas that Clear Heat',
    summary: 'Separate Liver Fire invading the Stomach from Stomach Fire complicated by Kidney Yin deficiency.',
    search: 'zuo jin wan yu nu jian left metal jade woman liver fire invading stomach stomach fire kidney yin deficiency acid reflux toothache bleeding gums',
    en: {
      eyebrow: 'Formula comparison', names: ['Zuo Jin Wan', 'Yu Nu Jian'],
      shared: 'Both clear pathogenic Fire affecting the Stomach and can present with thirst, irritability, epigastric discomfort or rebellious Stomach Qi.',
      fast: ['Liver Fire attacks Stomach: acid regurgitation, bitter taste, hypochondriac irritability and vomiting.', 'Stomach Fire with Kidney Yin deficiency: toothache, bleeding gums, intense hunger/thirst and loose teeth.'],
      rows: [
        ['Root mechanism', 'Liver Fire overacts on the Stomach.', 'Stomach Fire blazes while Kidney Yin fails to restrain it.'],
        ['Primary action', 'Clears Liver Fire and directs rebellious Stomach Qi downward.', 'Clears Stomach Fire and nourishes Kidney Yin.'],
        ['Decisive clue', 'Acid reflux with Liver-Qi/Fire signs.', 'Dental and gum signs with Yin deficiency.'],
        ['Exam shortcut', 'Liver attacks Stomach.', 'Stomach burns above; Kidney water is weak below.']
      ],
      why: 'The shared Stomach Heat signs are not enough. Acid rebellion plus Liver signs points to Zuo Jin; mouth/teeth plus Yin deficiency points to Yu Nu Jian.',
      memory: '<strong>Zuo Jin redirects Liver Fire;</strong> <strong>Yu Nu cools Stomach and restores Kidney water.</strong>',
      safety: 'Vomiting blood, black stools, severe abdominal pain or spreading dental infection requires urgent medical care.',
      prompts: ['Which formula treats Liver Fire invading Stomach?', 'Which combines Stomach Fire with Kidney Yin deficiency?', 'Which dental clues point to Yu Nu Jian?'],
      source: 'Source bank: FWB 2.2 and FWB 9.1 comparisons; corroborated with individual formula profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Zuo Jin Wan', 'Yu Nu Jian'],
      shared: 'Ambas eliminan Fuego patógeno que afecta Estómago con sed, irritabilidad, molestia epigástrica o Qi rebelde.',
      fast: ['Fuego de Hígado ataca Estómago: regurgitación ácida, sabor amargo, irritabilidad hipocondríaca y vómito.', 'Fuego de Estómago con Yin de Riñón deficiente: dolor dental, encías sangrantes, hambre/sed intensas y dientes flojos.'],
      rows: [
        ['Mecanismo raíz', 'Fuego de Hígado sobreactúa sobre Estómago.', 'Fuego de Estómago arde y Yin de Riñón no lo contiene.'],
        ['Acción principal', 'Elimina Fuego de Hígado y desciende Qi rebelde de Estómago.', 'Elimina Fuego de Estómago y nutre Yin de Riñón.'],
        ['Pista decisiva', 'Reflujo ácido con signos de Qi/Fuego de Hígado.', 'Signos dentales/gingivales con deficiencia de Yin.'],
        ['Atajo de examen', 'Hígado ataca Estómago.', 'Estómago arde arriba; agua de Riñón débil abajo.']
      ],
      why: 'Los signos de Calor de Estómago no bastan. Rebeldía ácida con Hígado señala Zuo Jin; boca/dientes con Yin deficiente señala Yu Nu Jian.',
      memory: '<strong>Zuo Jin redirige Fuego de Hígado;</strong> <strong>Yu Nu enfría Estómago y restaura agua de Riñón.</strong>',
      safety: 'Vómito con sangre, heces negras, dolor abdominal grave o infección dental extendida requieren atención urgente.',
      prompts: ['¿Cuál trata Fuego de Hígado invadiendo Estómago?', '¿Cuál combina Fuego de Estómago con Yin renal deficiente?', '¿Qué pistas dentales señalan Yu Nu?'],
      source: 'Banco fuente: comparaciones FWB 2.2 y FWB 9.1; corroborado con perfiles individuales.'
    }
  },
  {
    slug: 'gui-zhi-fu-ling-wan-vs-wen-jing-tang', title: 'Gui Zhi Fu Ling Wan vs. Wen Jing Tang',
    category: 'Formulas that Invigorate Blood',
    summary: 'Separate a fixed lower-abdominal Blood-stasis mass from Uterine Cold with Blood deficiency and stasis.',
    search: 'gui zhi fu ling wan wen jing tang cinnamon twig poria warm menses blood stasis uterine cold abdominal mass irregular bleeding deficiency gynecology',
    en: {
      eyebrow: 'Formula comparison', names: ['Gui Zhi Fu Ling Wan', 'Wen Jing Tang'],
      shared: 'Both warm and move Blood in gynecologic disorders with lower-abdominal pain and irregular uterine bleeding.',
      fast: ['Blood stasis mass is primary: fixed lower-abdominal mass, pain or persistent bleeding.', 'Cold and deficiency are primary: irregular menses, cold lower abdomen, Blood deficiency and stasis.'],
      rows: [
        ['Primary mechanism', 'Blood stasis obstructs the womb.', 'Cold in the channels plus deficiency causes Blood stasis and irregular flow.'],
        ['Therapeutic emphasis', 'Dispel stasis and reduce masses.', 'Warm channels, dispel Cold, nourish Blood and remove stasis.'],
        ['Decisive clue', 'Fixed mass or focal stasis.', 'Cold signs with thin/dry Blood-deficiency signs.'],
        ['Exam shortcut', 'Mass first.', 'Cold-deficiency cycle disorder first.']
      ],
      why: 'Both move Blood and contain Gui Zhi, but Gui Zhi Fu Ling Wan is mass/stasis-centered while Wen Jing Tang is Cold-deficiency-centered.',
      memory: '<strong>Gui Zhi Fu Ling breaks the mass;</strong> <strong>Wen Jing warms the menstrual pathway.</strong>',
      safety: 'Pregnancy, heavy bleeding, severe pelvic pain or a suspected mass requires obstetric/gynecologic evaluation before herbal treatment.',
      prompts: ['Which formula emphasizes a fixed mass?', 'Which warms channels and nourishes Blood?', 'What Cold-deficiency clues indicate Wen Jing Tang?'],
      source: 'Source bank: FWB 10.1 and FWB 11.1 comparisons; corroborated with individual formula files.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Gui Zhi Fu Ling Wan', 'Wen Jing Tang'],
      shared: 'Ambas calientan y mueven Sangre en trastornos ginecológicos con dolor inferior y sangrado uterino irregular.',
      fast: ['Predomina masa por estasis: masa fija inferior, dolor o sangrado persistente.', 'Predominan Frío y deficiencia: menstruación irregular, abdomen frío, deficiencia y estasis de Sangre.'],
      rows: [
        ['Mecanismo raíz', 'Estasis de Sangre obstruye útero.', 'Frío en canales más deficiencia causa estasis y flujo irregular.'],
        ['Énfasis', 'Dispersar estasis y reducir masas.', 'Calentar canales, disipar Frío, nutrir Sangre y eliminar estasis.'],
        ['Pista decisiva', 'Masa fija o estasis focal.', 'Signos de Frío con signos finos/secos de deficiencia de Sangre.'],
        ['Atajo de examen', 'Primero la masa.', 'Primero trastorno cíclico por Frío-deficiencia.']
      ],
      why: 'Ambas mueven Sangre y contienen Gui Zhi; Gui Zhi Fu Ling se centra en masa/estasis y Wen Jing en Frío-deficiencia.',
      memory: '<strong>Gui Zhi Fu Ling rompe masa;</strong> <strong>Wen Jing calienta vía menstrual.</strong>',
      safety: 'Embarazo, sangrado abundante, dolor pélvico grave o masa sospechada requieren evaluación ginecológica.',
      prompts: ['¿Cuál enfatiza masa fija?', '¿Cuál calienta canales y nutre Sangre?', '¿Qué signos señalan Wen Jing?'],
      source: 'Banco fuente: comparaciones FWB 10.1 y FWB 11.1; corroborado con archivos individuales.'
    }
  },
  {
    slug: 'tian-tai-wu-yao-san-vs-nuan-gan-jian', title: 'Tian Tai Wu Yao San vs. Nuan Gan Jian',
    category: 'Formulas that Regulate Qi / Warm the Interior',
    summary: 'Separate excess Cold-Qi stagnation in the Liver channel from a colder deficiency pattern needing both warming and nourishment.',
    search: 'tian tai wu yao san nuan gan jian top quality lindera warm liver cold qi stagnation shan hernia testicular lower abdominal pain deficiency',
    en: {
      eyebrow: 'Formula comparison', names: ['Tian Tai Wu Yao San', 'Nuan Gan Jian'],
      shared: 'Both warm the Liver channel, move Qi and relieve lower-abdominal, groin or testicular pain from Cold.',
      fast: ['More excess and obstructive: Cold-Qi stagnation with severe shan-disorder pain.', 'More deficient: Liver-Kidney Cold with weakness requiring warming plus nourishment.'],
      rows: [
        ['Primary strategy', 'Warm, disperse Cold and strongly move/downbear Qi.', 'Warm and nourish Liver-Kidney while regulating Qi.'],
        ['Decisive clue', 'Severe fixed or radiating pain from excess Cold stagnation.', 'Chronic pain with weakness, Cold and deficiency signs.'],
        ['Formula character', 'More dispersing and attacking.', 'More warming and supplementing.'],
        ['Exam shortcut', 'Cold obstruction.', 'Cold deficiency.']
      ],
      why: 'The pain location overlaps. Constitution and formula direction—attack stagnation versus warm/nourish deficiency—make the distinction.',
      memory: '<strong>Tian Tai drives Cold-Qi out;</strong> <strong>Nuan Gan warms and supports the Liver.</strong>',
      safety: 'Classical Tian Tai Wu Yao San preparation historically involves Ba Dou processing; it is not for home preparation or self-dosing. Acute testicular pain is an emergency.',
      prompts: ['Which formula is more dispersing?', 'Which fits a chronic deficient-Cold pattern?', 'Why is acute testicular pain a referral sign?'],
      source: 'Source bank: FWB 9.1 series, Comparison Formulas to Regulate the Qi; corroborated with formula references.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Tian Tai Wu Yao San', 'Nuan Gan Jian'],
      shared: 'Ambas calientan canal de Hígado, mueven Qi y alivian dolor inferior, inguinal o testicular por Frío.',
      fast: ['Más exceso-obstrucción: estancamiento de Qi-Frío con dolor intenso de trastorno shan.', 'Más deficiencia: Frío de Hígado-Riñón con debilidad que necesita calentar y nutrir.'],
      rows: [
        ['Estrategia principal', 'Calentar, dispersar Frío y mover/hacer descender Qi fuertemente.', 'Calentar y nutrir Hígado-Riñón mientras regula Qi.'],
        ['Pista decisiva', 'Dolor intenso fijo o irradiado por estancamiento de Frío de exceso.', 'Dolor crónico con debilidad, Frío y signos deficientes.'],
        ['Carácter', 'Más dispersante y atacante.', 'Más caliente y suplementante.'],
        ['Atajo de examen', 'Obstrucción fría.', 'Deficiencia fría.']
      ],
      why: 'La localización coincide. Constitución y dirección—atacar estancamiento frente a calentar/nutrir—distinguen.',
      memory: '<strong>Tian Tai expulsa Qi-Frío;</strong> <strong>Nuan Gan calienta y apoya Hígado.</strong>',
      safety: 'La preparación clásica de Tian Tai Wu Yao San incluye procesamiento histórico con Ba Dou; no es para preparación o dosificación casera. Dolor testicular agudo es emergencia.',
      prompts: ['¿Cuál dispersa con más fuerza?', '¿Cuál encaja con Frío deficiente crónico?', '¿Por qué dolor testicular agudo exige remisión?'],
      source: 'Banco fuente: serie FWB 9.1, Comparison Formulas to Regulate the Qi; corroborado con referencias de fórmulas.'
    }
  },
  {
    slug: 'qing-ying-tang-vs-xi-jiao-di-huang-tang', title: 'Qing Ying Tang vs. Xi Jiao Di Huang Tang',
    category: 'Formulas that Clear Heat',
    summary: 'Separate Heat entering the Nutritive level from deeper Heat entering the Blood level with bleeding and severe Blood disturbance.',
    search: 'qing ying tang xi jiao di huang tang clear nutritive level rhinoceros horn rehmannia blood level heat fever night delirium rash bleeding shui niu jiao',
    en: {
      eyebrow: 'Formula comparison', names: ['Qing Ying Tang', 'Xi Jiao Di Huang Tang'],
      shared: 'Both treat deep warm-disease Heat, cool the Blood, protect Yin and may present with fever worse at night, restlessness, rash or altered consciousness.',
      fast: ['Nutritive-level Heat: early Shen disturbance, faint rash and residual Qi-level Heat.', 'Blood-level Heat: bleeding, dense rash, severe delirium and Blood stasis.'],
      rows: [
        ['Disease level', 'Ying/Nutritive level.', 'Xue/Blood level.'],
        ['Primary action', 'Clears Ying Heat, nourishes Yin and vents Heat toward the Qi level.', 'Cools Blood, stops bleeding and disperses stasis.'],
        ['Decisive clue', 'Fever worse at night with irritability and indistinct rash.', 'Hemorrhage or pronounced macular eruption with severe mental disturbance.'],
        ['Exam shortcut', 'Deep Heat entering Ying.', 'Deeper Heat damaging Blood.']
      ],
      why: 'These are sequential depth patterns. Bleeding and pronounced Blood-level injury move the answer from Qing Ying Tang to Xi Jiao Di Huang Tang.',
      memory: '<strong>Qing Ying vents the nutritive level;</strong> <strong>Xi Jiao Di Huang cools damaged Blood.</strong>',
      safety: 'Xi Jiao (rhinoceros horn) is obsolete and illegal; modern references substitute Shui Niu Jiao. High fever, bleeding or altered consciousness is an emergency.',
      prompts: ['Which formula vents Heat from Ying toward Qi?', 'Which is selected when bleeding dominates?', 'What legal substitution replaces Xi Jiao?'],
      source: 'Source bank: FWB 2.2 series and Heat-clearing materials; corroborated with formula references. Modern substitution: Shui Niu Jiao for Xi Jiao.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Qing Ying Tang', 'Xi Jiao Di Huang Tang'],
      shared: 'Ambas tratan Calor profundo de enfermedad tibia, enfrían Sangre, protegen Yin y pueden presentar fiebre nocturna, inquietud, erupción o conciencia alterada.',
      fast: ['Calor nivel Nutritivo: alteración inicial de Shen, erupción tenue y Calor residual nivel Qi.', 'Calor nivel Sangre: sangrado, erupción densa, delirio grave y estasis.'],
      rows: [
        ['Nivel', 'Ying/Nutritivo.', 'Xue/Sangre.'],
        ['Acción principal', 'Elimina Calor Ying, nutre Yin y ventila Calor hacia nivel Qi.', 'Enfría Sangre, detiene sangrado y dispersa estasis.'],
        ['Pista decisiva', 'Fiebre peor de noche con irritabilidad y erupción indistinta.', 'Hemorragia o erupción macular marcada con alteración mental grave.'],
        ['Atajo de examen', 'Calor profundo entrando Ying.', 'Calor más profundo dañando Sangre.']
      ],
      why: 'Son niveles secuenciales. Sangrado y lesión marcada de Sangre cambian respuesta de Qing Ying a Xi Jiao Di Huang.',
      memory: '<strong>Qing Ying ventila nivel Nutritivo;</strong> <strong>Xi Jiao Di Huang enfría Sangre dañada.</strong>',
      safety: 'Xi Jiao (cuerno de rinoceronte) es obsoleto e ilegal; referencias modernas sustituyen Shui Niu Jiao. Fiebre alta, sangrado o conciencia alterada es emergencia.',
      prompts: ['¿Cuál ventila Calor de Ying hacia Qi?', '¿Cuál se elige cuando domina sangrado?', '¿Qué sustitución legal reemplaza Xi Jiao?'],
      source: 'Banco fuente: serie FWB 2.2 y materiales de Calor; corroborado con referencias. Sustitución moderna: Shui Niu Jiao por Xi Jiao.'
    }
  },
  {
    slug: 'sheng-mai-san-vs-tian-wang-bu-xin-dan', title: 'Sheng Mai San vs. Tian Wang Bu Xin Dan',
    category: 'Tonify Qi and Yin / Calm the Spirit',
    summary: 'Separate acute or subacute Qi-and-Yin injury with sweating from chronic Heart-Kidney Yin-Blood deficiency disturbing the Spirit.',
    search: 'sheng mai san tian wang bu xin dan generate pulse emperor heaven tonify heart qi yin deficiency sweating thirst short breath insomnia palpitations kidney yin',
    en: {
      eyebrow: 'Formula comparison', names: ['Sheng Mai San', 'Tian Wang Bu Xin Dan'],
      shared: 'Both support Heart Yin and fluids and may treat palpitations, thirst, fatigue and a thin or weak pulse.',
      fast: ['Qi and Yin collapse from Heat or chronic illness: sweating, shortness of breath, thirst and weak pulse.', 'Chronic Heart-Kidney Yin and Blood deficiency: insomnia, anxiety, poor memory, dry mouth and night sweats.'],
      rows: [
        ['Primary target', 'Lung-Heart Qi and Yin with loss of fluids.', 'Heart-Kidney Yin/Blood with deficient Fire disturbing Shen.'],
        ['Primary action', 'Augments Qi, generates fluids and restrains leakage.', 'Nourishes Yin and Blood, clears deficient Heat and calms Spirit.'],
        ['Decisive clue', 'Profuse sweating with short breath and weak pulse.', 'Insomnia and palpitations with red scant-coated tongue.'],
        ['Exam shortcut', 'Preserve the pulse and fluids.', 'Nourish the Heart to settle Shen.']
      ],
      why: 'Both address Yin/fluid deficiency, but Sheng Mai San is leakage and functional collapse; Tian Wang is chronic Spirit disturbance from Heart-Kidney deficiency.',
      memory: '<strong>Sheng Mai saves Qi and fluids;</strong> <strong>Tian Wang feeds Yin and calms Shen.</strong>',
      safety: 'Classical Tian Wang Bu Xin Dan may list Zhu Sha; use only cinnabar-free, quality-controlled modern products. Collapse, chest pain or severe dehydration requires urgent care.',
      prompts: ['Which formula treats sweating with shortness of breath?', 'Which treats insomnia from Heart-Kidney Yin deficiency?', 'What obsolete ingredient must be excluded?'],
      source: 'Source bank: FWB 13.1 series, Comparison Formulas to Calm the Mind; corroborated with Classic Formulas profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Sheng Mai San', 'Tian Wang Bu Xin Dan'],
      shared: 'Ambas apoyan Yin/líquidos de Corazón con palpitaciones, sed, fatiga y pulso fino o débil.',
      fast: ['Colapso de Qi y Yin por Calor/enfermedad: sudor, disnea, sed y pulso débil.', 'Deficiencia crónica de Yin-Sangre Corazón-Riñón: insomnio, ansiedad, mala memoria, boca seca y sudor nocturno.'],
      rows: [
        ['Objetivo', 'Qi y Yin de Pulmón-Corazón con pérdida de líquidos.', 'Yin/Sangre de Corazón-Riñón con Fuego vacío alterando Shen.'],
        ['Acción principal', 'Aumenta Qi, genera líquidos y contiene pérdida.', 'Nutre Yin-Sangre, elimina Calor vacío y calma Shen.'],
        ['Pista decisiva', 'Sudor profuso con falta de aire y pulso débil.', 'Insomnio/palpitaciones con lengua roja y poca capa.'],
        ['Atajo de examen', 'Preservar pulso y líquidos.', 'Nutrir Corazón para asentar Shen.']
      ],
      why: 'Ambas tratan deficiencia de Yin/líquidos; Sheng Mai es pérdida y colapso funcional, Tian Wang es alteración crónica de Shen.',
      memory: '<strong>Sheng Mai salva Qi y líquidos;</strong> <strong>Tian Wang alimenta Yin y calma Shen.</strong>',
      safety: 'Tian Wang clásico puede incluir Zhu Sha; usar solo productos modernos sin cinabrio y controlados. Colapso, dolor torácico o deshidratación grave exige urgencia.',
      prompts: ['¿Cuál trata sudor con falta de aire?', '¿Cuál trata insomnio por deficiencia Corazón-Riñón?', '¿Qué ingrediente obsoleto debe excluirse?'],
      source: 'Banco fuente: serie FWB 13.1, Comparison Formulas to Calm the Mind; corroborado con perfiles Classic Formulas.'
    }
  }
);

cards.push(
  {
    slug: 'er-chen-tang-vs-ban-xia-bai-zhu-tian-ma-tang', title: 'Er Chen Tang vs. Ban Xia Bai Zhu Tian Ma Tang',
    category: 'Formulas that Transform Phlegm',
    summary: 'Separate foundational Damp-Phlegm in the Lung and middle burner from Wind-Phlegm rising to the head.',
    search: 'er chen tang ban xia bai zhu tian ma tang two cured pinellia atractylodes gastrodia damp phlegm wind phlegm dizziness vertigo cough sputum',
    en: {
      eyebrow: 'Formula comparison', names: ['Er Chen Tang', 'Ban Xia Bai Zhu Tian Ma Tang'],
      shared: 'Both dry Dampness, transform Phlegm and harmonize the Stomach; both may show nausea, chest fullness and a slippery pulse.',
      fast: ['Damp-Phlegm remains in Lung/middle burner: cough with copious white sputum.', 'Damp-Phlegm generates internal Wind that rises: dizziness, vertigo and headache dominate.'],
      rows: [
        ['Primary target', 'General Damp-Phlegm from Spleen dysfunction.', 'Wind-Phlegm from Spleen deficiency rising to the head.'],
        ['Distinct action', 'Regulates Qi and dries Dampness.', 'Strengthens Spleen and extinguishes Wind with Tian Ma.'],
        ['Decisive clue', 'Cough and sputum.', 'Vertigo and headache.'],
        ['Exam shortcut', 'Phlegm stays low.', 'Phlegm-Wind rises.']
      ],
      why: 'The second formula grows from an Er Chen-style core but adds Spleen support and Wind-extinguishing treatment for head symptoms.',
      memory: '<strong>Er Chen dries Phlegm;</strong> <strong>Ban Xia Bai Zhu Tian Ma stops the spin.</strong>',
      safety: 'Sudden severe vertigo with neurologic deficits, chest pain or inability to walk requires urgent assessment.',
      prompts: ['Which formula is foundational for Damp-Phlegm?', 'Which treats Wind-Phlegm vertigo?', 'What role does Tian Ma signal?'],
      source: 'Source bank: FWB 6.2 series, Comparison Formulas to Transform Phlegm; corroborated with individual profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Er Chen Tang', 'Ban Xia Bai Zhu Tian Ma Tang'],
      shared: 'Ambas secan Humedad, transforman Flema y armonizan Estómago; pueden mostrar náusea, plenitud torácica y pulso resbaladizo.',
      fast: ['Humedad-Flema en Pulmón/Jiao Medio: tos con esputo blanco abundante.', 'Humedad-Flema genera Viento interno ascendente: predominan mareo, vértigo y cefalea.'],
      rows: [
        ['Objetivo', 'Humedad-Flema general por disfunción de Bazo.', 'Viento-Flema por deficiencia de Bazo que asciende a cabeza.'],
        ['Acción distintiva', 'Regula Qi y seca Humedad.', 'Fortalece Bazo y extingue Viento con Tian Ma.'],
        ['Pista decisiva', 'Tos y esputo.', 'Vértigo y cefalea.'],
        ['Atajo', 'Flema queda abajo.', 'Flema-Viento asciende.']
      ],
      why: 'La segunda crece sobre un núcleo tipo Er Chen y añade apoyo de Bazo y extinción de Viento para síntomas cefálicos.',
      memory: '<strong>Er Chen seca Flema;</strong> <strong>Ban Xia Bai Zhu Tian Ma detiene el giro.</strong>',
      safety: 'Vértigo súbito grave con déficit neurológico, dolor torácico o incapacidad para caminar exige urgencia.',
      prompts: ['¿Cuál es base para Humedad-Flema?', '¿Cuál trata vértigo por Viento-Flema?', '¿Qué señala Tian Ma?'],
      source: 'Banco fuente: serie FWB 6.2, Comparison Formulas to Transform Phlegm; corroborado con perfiles individuales.'
    }
  },
  {
    slug: 'si-ni-san-vs-xiao-yao-san', title: 'Si Ni San vs. Xiao Yao San',
    category: 'Formulas that Harmonize',
    summary: 'Separate constrained Yang with cold extremities from Liver-Qi constraint complicated by Spleen and Blood deficiency.',
    search: 'si ni san xiao yao san frigid extremities powder rambling liver qi constraint spleen blood deficiency cold hands feet emotional pms',
    en: {
      eyebrow: 'Formula comparison', names: ['Si Ni San', 'Xiao Yao San'],
      shared: 'Both spread Liver Qi, harmonize Liver and Spleen, and share Chai Hu, Bai Shao and Gan Cao.',
      fast: ['Constraint blocks Yang from reaching extremities: cold fingers/toes, abdominal pain and a wiry pulse.', 'Constraint occurs with deficiency: emotional/PMS signs, fatigue, poor appetite and a wiry-weak pulse.'],
      rows: [
        ['Pattern character', 'More constrained/excess.', 'More deficient and chronic.'],
        ['Distinct action', 'Unbinds constrained Yang and regulates the pivot.', 'Nourishes Blood and strengthens Spleen while moving Liver Qi.'],
        ['Decisive clue', 'Cold extremities with a warm trunk.', 'Mood and menstrual symptoms with digestive weakness.'],
        ['Exam shortcut', 'Qi cannot reach the limbs.', 'Liver constraint drains Spleen and Blood.']
      ],
      why: 'Shared herbs and Liver-Spleen language make them easy to confuse. Cold extremities signal Si Ni San; deficiency and menstrual/emotional context signal Xiao Yao San.',
      memory: '<strong>Si Ni frees trapped Yang;</strong> <strong>Xiao Yao frees Liver while rebuilding.</strong>',
      safety: 'New severe abdominal pain, fainting or cold extremities with systemic collapse requires urgent medical assessment.',
      prompts: ['Which formula has cold fingers and toes?', 'Which adds Spleen and Blood support?', 'Which pulse reflects combined constraint and deficiency?'],
      source: 'Source bank: FWB 4.2 series, Comparison Formulas to Harmonize; corroborated with individual formula profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Si Ni San', 'Xiao Yao San'],
      shared: 'Ambas difunden Qi de Hígado, armonizan Hígado-Bazo y comparten Chai Hu, Bai Shao y Gan Cao.',
      fast: ['Estancamiento bloquea Yang hacia extremidades: dedos fríos, dolor abdominal y pulso de cuerda.', 'Estancamiento con deficiencia: signos emocionales/SPM, fatiga, poco apetito y pulso cuerda-débil.'],
      rows: [
        ['Carácter', 'Más constreñido/exceso.', 'Más deficiente y crónico.'],
        ['Acción distintiva', 'Desata Yang constreñido y regula bisagra.', 'Nutre Sangre y fortalece Bazo mientras mueve Qi de Hígado.'],
        ['Pista decisiva', 'Extremidades frías con tronco caliente.', 'Ánimo y menstruación con debilidad digestiva.'],
        ['Atajo', 'Qi no llega a miembros.', 'Estancamiento agota Bazo y Sangre.']
      ],
      why: 'Hierbas y lenguaje Hígado-Bazo coinciden. Extremidades frías señalan Si Ni; deficiencia y contexto menstrual/emocional señalan Xiao Yao.',
      memory: '<strong>Si Ni libera Yang atrapado;</strong> <strong>Xiao Yao libera Hígado mientras reconstruye.</strong>',
      safety: 'Dolor abdominal grave nuevo, desmayo o extremidades frías con colapso sistémico exige urgencia.',
      prompts: ['¿Cuál presenta dedos fríos?', '¿Cuál añade apoyo de Bazo y Sangre?', '¿Qué pulso muestra estancamiento con deficiencia?'],
      source: 'Banco fuente: serie FWB 4.2, Comparison Formulas to Harmonize; corroborado con perfiles individuales.'
    }
  },
  {
    slug: 'gan-mai-da-zao-tang-vs-xiao-yao-san', title: 'Gan Mai Da Zao Tang vs. Xiao Yao San',
    category: 'Formulas that Harmonize / Calm the Spirit',
    summary: 'Separate Zang Zao emotional instability from Liver-Qi constraint with Spleen-Blood deficiency.',
    search: 'gan mai da zao tang xiao yao san licorice wheat jujube rambling zang zao restless organ crying yawning liver qi constraint spleen blood',
    en: {
      eyebrow: 'Formula comparison', names: ['Gan Mai Da Zao Tang', 'Xiao Yao San'],
      shared: 'Both may treat emotional lability, irritability, anxiety and symptoms influenced by stress.',
      fast: ['Zang Zao: unexplained crying, frequent yawning, restlessness and inability to control emotions.', 'Liver-Qi constraint: mood changes with rib-side distention, PMS and digestive weakness.'],
      rows: [
        ['Primary action', 'Nourishes the Heart, calms the Spirit and harmonizes the middle.', 'Spreads Liver Qi, strengthens Spleen and nourishes Blood.'],
        ['Decisive clue', 'Crying/yawning emotional episodes.', 'Menstrual, rib-side and appetite clues.'],
        ['Pattern focus', 'Heart/Spirit nourishment.', 'Liver-Spleen-Blood disharmony.'],
        ['Exam shortcut', 'Emotion spills over.', 'Emotion is constrained.']
      ],
      why: 'Both can appear as stress-related mood disturbance. The classic crying/yawning picture is Gan Mai Da Zao; Liver-Spleen signs are Xiao Yao.',
      memory: '<strong>Gan Mai Da Zao comforts the Heart;</strong> <strong>Xiao Yao releases the Liver.</strong>',
      safety: 'Severe depression, mania, psychosis, suicidality or abrupt behavioral change requires mental-health evaluation.',
      prompts: ['Which formula treats Zang Zao?', 'Which links mood to PMS and digestion?', 'What classic behavior points to Gan Mai Da Zao Tang?'],
      source: 'Source bank: FWB 13.1 and harmonizing comparisons; corroborated with formula references.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Gan Mai Da Zao Tang', 'Xiao Yao San'],
      shared: 'Ambas pueden tratar labilidad emocional, irritabilidad, ansiedad y síntomas influidos por estrés.',
      fast: ['Zang Zao: llanto inexplicado, bostezos frecuentes, inquietud y emociones incontrolables.', 'Estancamiento de Qi de Hígado: ánimo con distensión costal, SPM y debilidad digestiva.'],
      rows: [
        ['Acción principal', 'Nutre Corazón, calma Shen y armoniza centro.', 'Difunde Qi de Hígado, fortalece Bazo y nutre Sangre.'],
        ['Pista decisiva', 'Episodios de llanto/bostezo.', 'Pistas menstruales, costales y de apetito.'],
        ['Enfoque', 'Nutrición de Corazón/Shen.', 'Desarmonía Hígado-Bazo-Sangre.'],
        ['Atajo', 'La emoción se derrama.', 'La emoción está constreñida.']
      ],
      why: 'Ambas parecen alteración emocional por estrés. Llanto/bostezo clásico es Gan Mai Da Zao; signos Hígado-Bazo son Xiao Yao.',
      memory: '<strong>Gan Mai Da Zao consuela Corazón;</strong> <strong>Xiao Yao libera Hígado.</strong>',
      safety: 'Depresión grave, manía, psicosis, ideas suicidas o cambio conductual abrupto requieren evaluación de salud mental.',
      prompts: ['¿Cuál trata Zang Zao?', '¿Cuál une ánimo con SPM y digestión?', '¿Qué conducta clásica señala Gan Mai Da Zao?'],
      source: 'Banco fuente: FWB 13.1 y comparaciones de armonización; corroborado con referencias.'
    }
  },
  {
    slug: 'wu-ling-san-vs-zhen-wu-tang', title: 'Wu Ling San vs. Zhen Wu Tang',
    category: 'Formulas that Drain Dampness',
    summary: 'Separate impaired Bladder Qi transformation from deeper Spleen-Kidney Yang deficiency with water accumulation.',
    search: 'wu ling san zhen wu tang five ingredient poria true warrior water accumulation bladder qi transformation spleen kidney yang deficiency edema urinary',
    en: {
      eyebrow: 'Formula comparison', names: ['Wu Ling San', 'Zhen Wu Tang'],
      shared: 'Both warm and transform water metabolism and may treat edema, urinary difficulty, dizziness or palpitations.',
      fast: ['Bladder Qi transformation fails, often in a Taiyang pattern; thirst and urinary difficulty dominate.', 'Spleen-Kidney Yang deficiency is deeper; Cold, edema, heaviness and loose stools dominate.'],
      rows: [
        ['Depth', 'Functional water regulation at Bladder/Taiyang level.', 'Constitutional Yang deficiency with retained water.'],
        ['Primary action', 'Promotes urination and transforms Qi.', 'Warms Yang and promotes urination.'],
        ['Decisive clue', 'Thirst but fluids are not transformed properly.', 'Cold limbs, abdominal pain, heaviness and weakness.'],
        ['Exam shortcut', 'Water gate will not open.', 'Fire is too weak to move water.']
      ],
      why: 'Both drain water, but Zhen Wu Tang is chosen when the warming/tonifying need is deeper and more systemic.',
      memory: '<strong>Wu Ling opens transformation;</strong> <strong>Zhen Wu rekindles Yang to move water.</strong>',
      safety: 'Zhen Wu Tang contains Fu Zi and requires processed material and professional supervision. Acute urinary retention or breathlessness with edema needs urgent care.',
      prompts: ['Which formula emphasizes Bladder Qi transformation?', 'Which treats Spleen-Kidney Yang deficiency?', 'What Cold signs point to Zhen Wu Tang?'],
      source: 'Source bank: FWB 5.2 series, Comparison Formulas to Expel Damp; corroborated with individual profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Wu Ling San', 'Zhen Wu Tang'],
      shared: 'Ambas calientan y transforman metabolismo de agua con edema, dificultad urinaria, mareo o palpitaciones.',
      fast: ['Falla transformación de Qi de Vejiga, a menudo Taiyang; predominan sed y dificultad urinaria.', 'Deficiencia profunda de Yang Bazo-Riñón; predominan Frío, edema, pesadez y heces blandas.'],
      rows: [
        ['Profundidad', 'Regulación funcional de agua en Vejiga/Taiyang.', 'Deficiencia constitucional de Yang con agua retenida.'],
        ['Acción', 'Promueve micción y transforma Qi.', 'Calienta Yang y promueve micción.'],
        ['Pista decisiva', 'Sed pero líquidos no se transforman.', 'Extremidades frías, dolor abdominal, pesadez y debilidad.'],
        ['Atajo', 'La compuerta de agua no abre.', 'El fuego es muy débil para mover agua.']
      ],
      why: 'Ambas drenan agua; Zhen Wu se elige cuando calentar/tonificar es más profundo y sistémico.',
      memory: '<strong>Wu Ling abre transformación;</strong> <strong>Zhen Wu reaviva Yang para mover agua.</strong>',
      safety: 'Zhen Wu contiene Fu Zi y requiere material procesado y supervisión. Retención urinaria aguda o disnea con edema requiere urgencia.',
      prompts: ['¿Cuál enfatiza transformación de Vejiga?', '¿Cuál trata Yang deficiente de Bazo-Riñón?', '¿Qué signos de Frío señalan Zhen Wu?'],
      source: 'Banco fuente: serie FWB 5.2, Comparison Formulas to Expel Damp; corroborado con perfiles individuales.'
    }
  },
  {
    slug: 'xiao-jian-zhong-tang-vs-li-zhong-wan', title: 'Xiao Jian Zhong Tang vs. Li Zhong Wan',
    category: 'Formulas that Warm the Interior',
    summary: 'Separate deficiency abdominal spasm needing Yin-Yang harmonization from straightforward middle-burner deficiency-Cold.',
    search: 'xiao jian zhong tang li zhong wan minor construct middle regulate middle abdominal pain spasm deficiency cold spleen stomach diarrhea vomiting yi tang',
    en: {
      eyebrow: 'Formula comparison', names: ['Xiao Jian Zhong Tang', 'Li Zhong Wan'],
      shared: 'Both warm and strengthen the middle burner and can treat deficiency abdominal pain relieved by warmth and pressure.',
      fast: ['Spasmodic pain from deficiency and disharmony of Yin-Yang/Qi-Blood; palpitations or spontaneous sweating may occur.', 'Spleen-Stomach deficiency-Cold with vomiting, watery diarrhea and loss of appetite.'],
      rows: [
        ['Primary emphasis', 'Warm, nourish and relax spasm with Yi Tang and Bai Shao.', 'Warm and tonify while drying middle-burner Cold-Damp.'],
        ['Decisive clue', 'Intermittent cramping pain.', 'Persistent digestive Cold with vomiting/diarrhea.'],
        ['Formula character', 'Sweeter and more nourishing.', 'Warmer and drier.'],
        ['Exam shortcut', 'Deficient spasm.', 'Deficient Cold digestion.']
      ],
      why: 'Both improve pain with warmth/pressure. Spasm and nourishment point to Xiao Jian Zhong; dominant vomiting-diarrhea points to Li Zhong.',
      memory: '<strong>Xiao Jian Zhong softens the cramp;</strong> <strong>Li Zhong warms the digestive center.</strong>',
      safety: 'Severe, rigid or worsening abdominal pain, fever, bleeding or inability to pass stool/gas requires urgent assessment.',
      prompts: ['Which formula uses Yi Tang for deficient spasm?', 'Which better fits vomiting and watery diarrhea?', 'How does formula character differ?'],
      source: 'Source bank: FWB 7.1 comparisons and Classic Formulas profiles; corroborated with individual files.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Xiao Jian Zhong Tang', 'Li Zhong Wan'],
      shared: 'Ambas calientan y fortalecen Jiao Medio y tratan dolor deficiente mejorado por calor y presión.',
      fast: ['Dolor espasmódico por deficiencia/desarmonía Yin-Yang/Qi-Sangre; puede haber palpitaciones o sudor.', 'Frío por deficiencia Bazo-Estómago con vómito, diarrea acuosa y poco apetito.'],
      rows: [
        ['Énfasis', 'Calentar, nutrir y relajar espasmo con Yi Tang y Bai Shao.', 'Calentar/tonificar mientras seca Frío-Humedad del centro.'],
        ['Pista decisiva', 'Dolor cólico intermitente.', 'Frío digestivo persistente con vómito/diarrea.'],
        ['Carácter', 'Más dulce y nutritiva.', 'Más caliente y seca.'],
        ['Atajo', 'Espasmo deficiente.', 'Digestión fría-deficiente.']
      ],
      why: 'Ambas mejoran con calor/presión. Espasmo y nutrición señalan Xiao Jian Zhong; vómito-diarrea dominante señala Li Zhong.',
      memory: '<strong>Xiao Jian Zhong suaviza cólico;</strong> <strong>Li Zhong calienta centro digestivo.</strong>',
      safety: 'Dolor rígido, grave o progresivo, fiebre, sangrado o no expulsar heces/gases requiere urgencia.',
      prompts: ['¿Cuál usa Yi Tang para espasmo deficiente?', '¿Cuál encaja con vómito y diarrea acuosa?', '¿Cómo difiere el carácter?'],
      source: 'Banco fuente: comparaciones FWB 7.1 y perfiles Classic Formulas; corroborado con archivos individuales.'
    }
  },
  {
    slug: 'xie-xin-tang-vs-ban-xia-xie-xin-tang', title: 'Xie Xin Tang vs. Ban Xia Xie Xin Tang',
    category: 'Clear Heat / Harmonize',
    summary: 'Separate pure excess Heat causing epigastric fullness from mixed Cold-Heat binding the epigastrium.',
    search: 'xie xin tang ban xia xie xin tang drain epigastrium three yellows mixed cold heat focal distention bleeding vomiting diarrhea',
    en: {
      eyebrow: 'Formula name trap', names: ['Xie Xin Tang', 'Ban Xia Xie Xin Tang'],
      shared: 'Both may present with epigastric focal distention and use bitter medicinals to drain pathological Heat downward.',
      fast: ['Pure excess Heat/Fire: irritability, red face, possible bleeding and a rapid-forceful pulse.', 'Mixed Cold-Heat with middle deficiency: nausea, vomiting, borborygmus and diarrhea.'],
      rows: [
        ['Mechanism', 'Excess Fire obstructs and agitates the upper/middle burner.', 'Cold and Heat bind with Stomach rebellion and Spleen deficiency.'],
        ['Primary action', 'Drains Fire and clears Heat.', 'Harmonizes Cold-Heat and opens the epigastric bind.'],
        ['Decisive clue', 'Heat signs and possible bleeding.', 'Above-below digestive disharmony.'],
        ['Exam shortcut', 'Three Huang clear Fire.', 'Ban Xia joins Huang herbs for a mixed knot.']
      ],
      why: 'The shared “Xie Xin” name and fullness clue conceal opposite complexity: one is pure Heat, the other mixed Cold-Heat with deficiency.',
      memory: '<strong>Xie Xin drains Fire;</strong> <strong>Ban Xia Xie Xin unties mixed Cold-Heat.</strong>',
      safety: 'Vomiting blood, black stools, severe pain or persistent vomiting requires urgent medical evaluation.',
      prompts: ['Which formula is the Three-Yellows Heat formula?', 'Which treats mixed Cold-Heat?', 'What digestive signs point to Ban Xia Xie Xin Tang?'],
      source: 'Source bank: FWB 2.2 and FWB 4.2 comparisons; corroborated with individual formula files.'
    },
    es: {
      eyebrow: 'Trampa de nombres de fórmulas', names: ['Xie Xin Tang', 'Ban Xia Xie Xin Tang'],
      shared: 'Ambas pueden mostrar distensión epigástrica focal y usan sustancias amargas para drenar Calor hacia abajo.',
      fast: ['Calor/Fuego de exceso puro: irritabilidad, cara roja, posible sangrado y pulso rápido-fuerte.', 'Frío-Calor mixtos con deficiencia central: náusea, vómito, borborigmos y diarrea.'],
      rows: [
        ['Mecanismo', 'Fuego de exceso obstruye/agita Jiao Superior-Medio.', 'Frío y Calor se ligan con rebeldía de Estómago y Bazo deficiente.'],
        ['Acción', 'Drena Fuego y elimina Calor.', 'Armoniza Frío-Calor y abre ligadura epigástrica.'],
        ['Pista decisiva', 'Signos de Calor y posible sangrado.', 'Desarmonía digestiva arriba-abajo.'],
        ['Atajo', 'Tres Huang eliminan Fuego.', 'Ban Xia se une a Huang para nudo mixto.']
      ],
      why: 'Nombre Xie Xin y plenitud coinciden, pero una es Calor puro y la otra Frío-Calor mixtos con deficiencia.',
      memory: '<strong>Xie Xin drena Fuego;</strong> <strong>Ban Xia Xie Xin desata Frío-Calor mixtos.</strong>',
      safety: 'Vómito con sangre, heces negras, dolor grave o vómito persistente exige urgencia.',
      prompts: ['¿Cuál es fórmula de Tres Amarillos?', '¿Cuál trata Frío-Calor mixtos?', '¿Qué signos digestivos señalan Ban Xia Xie Xin?'],
      source: 'Banco fuente: comparaciones FWB 2.2 y FWB 4.2; corroborado con archivos individuales.'
    }
  },
  {
    slug: 'tao-he-cheng-qi-tang-vs-gui-zhi-fu-ling-wan', title: 'Tao He Cheng Qi Tang vs. Gui Zhi Fu Ling Wan',
    category: 'Formulas that Invigorate Blood',
    summary: 'Separate acute lower-burner Blood stasis with Heat from a more chronic uterine Blood-stasis mass.',
    search: 'tao he cheng qi tang gui zhi fu ling wan peach pit order qi cinnamon poria lower burner blood stasis heat uterine mass amenorrhea bleeding',
    en: {
      eyebrow: 'Formula comparison', names: ['Tao He Cheng Qi Tang', 'Gui Zhi Fu Ling Wan'],
      shared: 'Both invigorate Blood and treat lower-abdominal Blood stasis with pain, menstrual obstruction or irregular bleeding.',
      fast: ['Stasis plus Heat is acute/forceful: lower-abdominal urgency, night fever, irritability or delirium, with normal urination.', 'Stasis forms a chronic mass: fixed pelvic fullness, pain or persistent uterine bleeding.'],
      rows: [
        ['Primary mechanism', 'Blood stasis and Heat bind in lower burner.', 'Blood stasis obstructs the womb and forms a mass.'],
        ['Distinct action', 'Breaks stasis and purges Heat downward.', 'Gradually dispels stasis and reduces masses.'],
        ['Formula clue', 'Contains Da Huang and Mang Xiao.', 'Uses balanced warming/moving and cooling Blood herbs.'],
        ['Exam shortcut', 'Hot, acute stasis.', 'Chronic stasis mass.']
      ],
      why: 'Both act in the lower abdomen. Heat and purgation point to Tao He Cheng Qi; a stable mass/persistent bleeding points to Gui Zhi Fu Ling.',
      memory: '<strong>Tao He Cheng Qi purges hot stasis;</strong> <strong>Gui Zhi Fu Ling resolves the mass.</strong>',
      safety: 'Both require pregnancy screening; Tao He Cheng Qi is strongly purgative. Acute pelvic pain, pregnancy bleeding or heavy hemorrhage requires urgent care.',
      prompts: ['Which formula combines Blood movement with purgation?', 'Which focuses on a chronic mass?', 'What Heat signs indicate Tao He Cheng Qi Tang?'],
      source: 'Source bank: FWB 10.1 comparisons; corroborated with individual formula profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Tao He Cheng Qi Tang', 'Gui Zhi Fu Ling Wan'],
      shared: 'Ambas vigorizan Sangre y tratan estasis inferior con dolor, obstrucción menstrual o sangrado irregular.',
      fast: ['Estasis más Calor aguda/fuerte: urgencia abdominal inferior, fiebre nocturna, irritabilidad o delirio, micción normal.', 'Estasis forma masa crónica: plenitud pélvica fija, dolor o sangrado uterino persistente.'],
      rows: [
        ['Mecanismo', 'Estasis y Calor se ligan en Jiao Inferior.', 'Estasis obstruye útero y forma masa.'],
        ['Acción distintiva', 'Rompe estasis y purga Calor hacia abajo.', 'Dispersa gradualmente estasis y reduce masas.'],
        ['Pista de fórmula', 'Contiene Da Huang y Mang Xiao.', 'Equilibra hierbas calientes/móviles y enfriadoras de Sangre.'],
        ['Atajo', 'Estasis caliente aguda.', 'Masa crónica por estasis.']
      ],
      why: 'Ambas actúan abajo. Calor y purgación señalan Tao He; masa estable/sangrado persistente señalan Gui Zhi Fu Ling.',
      memory: '<strong>Tao He Cheng Qi purga estasis caliente;</strong> <strong>Gui Zhi Fu Ling resuelve masa.</strong>',
      safety: 'Ambas requieren evaluar embarazo; Tao He Cheng Qi purga fuertemente. Dolor pélvico agudo, sangrado en embarazo o hemorragia exige urgencia.',
      prompts: ['¿Cuál combina mover Sangre con purgar?', '¿Cuál se centra en masa crónica?', '¿Qué Calor indica Tao He Cheng Qi?'],
      source: 'Banco fuente: comparaciones FWB 10.1; corroborado con perfiles individuales.'
    }
  },
  {
    slug: 'sheng-hua-tang-vs-wen-jing-tang', title: 'Sheng Hua Tang vs. Wen Jing Tang',
    category: 'Formulas that Invigorate Blood / Warm the Menses',
    summary: 'Separate postpartum Cold and retained lochia from chronic Uterine Cold with Blood deficiency and stasis.',
    search: 'sheng hua tang wen jing tang generation transformation warm menses postpartum retained lochia uterine cold blood deficiency stasis abdominal pain irregular menstruation',
    en: {
      eyebrow: 'Formula comparison', names: ['Sheng Hua Tang', 'Wen Jing Tang'],
      shared: 'Both warm, nourish and invigorate Blood for lower-abdominal pain from Cold and stasis.',
      fast: ['Immediate postpartum context: retained lochia, bleeding with clots and abdominal pain.', 'Chronic menstrual/reproductive context: irregular cycles, infertility, Cold and Blood deficiency.'],
      rows: [
        ['Timing', 'After childbirth.', 'Chronic gynecologic pattern.'],
        ['Primary action', 'Transforms and generates new Blood while expelling retained stasis.', 'Warms channels, disperses Cold, nourishes Blood and regulates menses.'],
        ['Decisive clue', 'Postpartum lochia and pain.', 'Long-standing cycle irregularity with Cold-deficiency signs.'],
        ['Exam shortcut', 'Postpartum old Blood out, new Blood in.', 'Warm the channel for chronic regulation.']
      ],
      why: 'The actions overlap, but timing is decisive. Sheng Hua Tang is postpartum; Wen Jing Tang is a broader chronic uterine Cold-deficiency pattern.',
      memory: '<strong>Sheng Hua transforms after birth;</strong> <strong>Wen Jing warms the menstrual channel.</strong>',
      safety: 'Postpartum hemorrhage, fever, foul lochia, dizziness or severe pain requires urgent obstetric evaluation.',
      prompts: ['Which formula is postpartum-specific?', 'Which treats chronic Uterine Cold?', 'What does retained lochia indicate?'],
      source: 'Source bank: FWB 10.1 and FWB 11.1 comparisons; corroborated with individual formula files.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Sheng Hua Tang', 'Wen Jing Tang'],
      shared: 'Ambas calientan, nutren y vigorizan Sangre para dolor inferior por Frío y estasis.',
      fast: ['Contexto posparto inmediato: loquios retenidos, sangrado con coágulos y dolor.', 'Contexto menstrual/reproductivo crónico: ciclos irregulares, infertilidad, Frío y Sangre deficiente.'],
      rows: [
        ['Momento', 'Después del parto.', 'Patrón ginecológico crónico.'],
        ['Acción', 'Transforma/genera Sangre nueva mientras expulsa estasis retenida.', 'Calienta canales, dispersa Frío, nutre Sangre y regula menstruación.'],
        ['Pista decisiva', 'Loquios y dolor posparto.', 'Irregularidad crónica con Frío-deficiencia.'],
        ['Atajo', 'Posparto: sale Sangre vieja, entra nueva.', 'Calentar canal para regulación crónica.']
      ],
      why: 'Acciones coinciden, pero el momento decide. Sheng Hua es posparto; Wen Jing es patrón crónico de Frío-deficiencia uterina.',
      memory: '<strong>Sheng Hua transforma tras parto;</strong> <strong>Wen Jing calienta canal menstrual.</strong>',
      safety: 'Hemorragia posparto, fiebre, loquios fétidos, mareo o dolor grave exige evaluación obstétrica urgente.',
      prompts: ['¿Cuál es específica posparto?', '¿Cuál trata Frío uterino crónico?', '¿Qué indican loquios retenidos?'],
      source: 'Banco fuente: comparaciones FWB 10.1 y FWB 11.1; corroborado con archivos individuales.'
    }
  },
  {
    slug: 'xiang-ru-san-vs-huo-xiang-zheng-qi-san', title: 'Xiang Ru San vs. Huo Xiang Zheng Qi San',
    category: 'Release the Exterior / Transform Dampness',
    summary: 'Separate summer Wind-Cold with Dampness at the Exterior from stronger middle-burner Dampness with digestive rebellion.',
    search: 'xiang ru san huo xiang zheng qi san elsholtzia agastache summer cold damp exterior nausea vomiting diarrhea middle burner',
    en: {
      eyebrow: 'Formula comparison', names: ['Xiang Ru San', 'Huo Xiang Zheng Qi San'],
      shared: 'Both address Exterior Wind-Cold complicated by Dampness in summer and may include headache, chills, heaviness and digestive upset.',
      fast: ['Exterior signs dominate: chills, fever, no sweating and heaviness after cold exposure in summer.', 'Middle-burner Dampness dominates: nausea, vomiting, diarrhea and epigastric fullness.'],
      rows: [
        ['Primary emphasis', 'Releases Exterior, transforms Dampness and clears summer Dampness through urination.', 'Releases Exterior, aromatically transforms Dampness and regulates middle-burner Qi.'],
        ['Decisive clue', 'Strong no-sweat Exterior picture.', 'Strong vomiting-diarrhea picture.'],
        ['Pattern center', 'Exterior and fluids.', 'Spleen-Stomach and Exterior.'],
        ['Exam shortcut', 'Summer “Ma Huang” pattern.', 'Damp digestive rebellion.']
      ],
      why: 'Both combine Exterior and Dampness. Decide whether the surface or the digestive middle burner is clinically louder.',
      memory: '<strong>Xiang Ru vents summer Cold-Damp;</strong> <strong>Huo Xiang rectifies the damp middle.</strong>',
      safety: 'Severe dehydration, high fever, bloody diarrhea or inability to keep fluids down requires medical care.',
      prompts: ['Which formula has stronger Exterior no-sweat signs?', 'Which centers on vomiting and diarrhea?', 'Why is Xiang Ru called summer Ma Huang?'],
      source: 'Source bank: FWB 1.2 and FWB 5.2 comparisons; corroborated with individual formula files and summerheat materials.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Xiang Ru San', 'Huo Xiang Zheng Qi San'],
      shared: 'Ambas tratan Viento-Frío Exterior con Humedad en verano, con cefalea, escalofríos, pesadez y alteración digestiva.',
      fast: ['Predomina Exterior: escalofríos, fiebre, sin sudor y pesadez tras exposición fría en verano.', 'Predomina Humedad de Jiao Medio: náusea, vómito, diarrea y plenitud epigástrica.'],
      rows: [
        ['Énfasis', 'Libera Exterior, transforma Humedad y facilita orina.', 'Libera Exterior, transforma aromáticamente Humedad y regula Qi del centro.'],
        ['Pista decisiva', 'Cuadro Exterior fuerte sin sudor.', 'Cuadro fuerte de vómito-diarrea.'],
        ['Centro', 'Exterior y líquidos.', 'Bazo-Estómago y Exterior.'],
        ['Atajo', 'Patrón “Ma Huang de verano”.', 'Rebeldía digestiva por Humedad.']
      ],
      why: 'Ambas combinan Exterior y Humedad. Decide si clínicamente grita más superficie o Jiao Medio digestivo.',
      memory: '<strong>Xiang Ru ventila Frío-Humedad estival;</strong> <strong>Huo Xiang rectifica centro húmedo.</strong>',
      safety: 'Deshidratación grave, fiebre alta, diarrea con sangre o no retener líquidos requiere atención médica.',
      prompts: ['¿Cuál tiene Exterior sin sudor más fuerte?', '¿Cuál se centra en vómito-diarrea?', '¿Por qué Xiang Ru es Ma Huang de verano?'],
      source: 'Banco fuente: comparaciones FWB 1.2 y FWB 5.2; corroborado con archivos individuales y materiales de verano.'
    }
  },
  {
    slug: 'gu-jing-wan-vs-wen-jing-tang', title: 'Gu Jing Wan vs. Wen Jing Tang',
    category: 'Formulas that Stop Bleeding / Warm the Menses',
    summary: 'Separate uterine bleeding from Kidney-Yin deficiency Heat from bleeding and irregular menses caused by Cold, deficiency and stasis.',
    search: 'gu jing wan wen jing tang stabilize menses warm menses uterine bleeding kidney yin deficiency heat cold blood stasis irregular menstruation',
    en: {
      eyebrow: 'Formula comparison', names: ['Gu Jing Wan', 'Wen Jing Tang'],
      shared: 'Both regulate uterine bleeding in deficiency patterns and may include prolonged or irregular menstruation.',
      fast: ['Deficiency Heat: dark-red bleeding, heat sensations, dry mouth and a red tongue.', 'Deficiency Cold with stasis: pale/dark bleeding, cold lower abdomen, pain and a pale or dusky tongue.'],
      rows: [
        ['Thermal pattern', 'Kidney Yin deficiency with Heat.', 'Uterine Cold with Blood deficiency and stasis.'],
        ['Primary action', 'Nourishes Yin, clears Heat and stabilizes bleeding.', 'Warms channels, nourishes/moves Blood and regulates menses.'],
        ['Decisive clue', 'Heat signs with bleeding.', 'Cold pain and irregular flow with deficiency.'],
        ['Exam shortcut', 'Secure hot bleeding.', 'Warm cold, stagnant bleeding.']
      ],
      why: 'Both involve chronic uterine bleeding, so temperature is decisive: Heat and Yin deficiency versus Cold with Blood deficiency/stasis.',
      memory: '<strong>Gu Jing cools and secures;</strong> <strong>Wen Jing warms and moves.</strong>',
      safety: 'Heavy bleeding, pregnancy bleeding, dizziness, fainting or postmenopausal bleeding requires urgent gynecologic evaluation.',
      prompts: ['Which formula treats deficiency-Heat bleeding?', 'Which treats Cold with Blood stasis?', 'What tongue and temperature clues separate them?'],
      source: 'Source bank: FWB 11.1 and FWB 12.1 comparisons; corroborated with formula references.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Gu Jing Wan', 'Wen Jing Tang'],
      shared: 'Ambas regulan sangrado uterino en patrones deficientes con menstruación prolongada o irregular.',
      fast: ['Calor por deficiencia: sangrado rojo oscuro, calor, boca seca y lengua roja.', 'Frío deficiente con estasis: sangrado pálido/oscuro, abdomen inferior frío, dolor y lengua pálida u oscura.'],
      rows: [
        ['Patrón térmico', 'Deficiencia de Yin de Riñón con Calor.', 'Frío uterino con deficiencia y estasis de Sangre.'],
        ['Acción', 'Nutre Yin, elimina Calor y estabiliza sangrado.', 'Calienta canales, nutre/mueve Sangre y regula menstruación.'],
        ['Pista decisiva', 'Signos de Calor con sangrado.', 'Dolor frío y flujo irregular con deficiencia.'],
        ['Atajo', 'Asegurar sangrado caliente.', 'Calentar sangrado frío-estancado.']
      ],
      why: 'Ambas implican sangrado crónico; decide temperatura: Calor/Yin deficiente frente a Frío con deficiencia-estasis.',
      memory: '<strong>Gu Jing enfría y asegura;</strong> <strong>Wen Jing calienta y mueve.</strong>',
      safety: 'Sangrado abundante, en embarazo, mareo, desmayo o posmenopáusico requiere evaluación ginecológica urgente.',
      prompts: ['¿Cuál trata sangrado por Calor vacío?', '¿Cuál trata Frío con estasis?', '¿Qué lengua y temperatura distinguen?'],
      source: 'Banco fuente: comparaciones FWB 11.1 y FWB 12.1; corroborado con referencias.'
    }
  }
);

cards.push(
  {
    slug: 'liu-wei-di-huang-wan-vs-qing-hao-bie-jia-tang', title: 'Liu Wei Di Huang Wan vs. Qing Hao Bie Jia Tang',
    category: 'Tonify Yin / Clear Deficiency Heat',
    summary: 'Separate chronic Liver-Kidney Yin deficiency from lingering Heat hidden in the Yin level after a warm disease.',
    search: 'liu wei di huang wan qing hao bie jia tang kidney liver yin deficiency heat night fever no sweat shu di sheng di',
    en: {
      eyebrow: 'Formula comparison', names: ['Liu Wei Di Huang Wan', 'Qing Hao Bie Jia Tang'],
      shared: 'Both address Yin deficiency with deficiency-Heat signs and use a Rehmannia root, but their clinical setting and therapeutic direction differ.',
      fast: ['Chronic Liver-Kidney Yin deficiency: soreness of the back and knees, dizziness, tinnitus and developmental or reproductive signs.', 'Late-stage warm disease: night fever that subsides in the morning without sweating and a cool sensation deep in the bones.'],
      rows: [
        ['Root pattern', 'Liver and Kidney Yin deficiency.', 'Heat lodged in the Yin level after warm disease with Yin and fluids injured.'],
        ['Rehmannia clue', 'Shu Di Huang nourishes Yin and Essence.', 'Sheng Di Huang clears Heat and cools the Blood while protecting Yin.'],
        ['Therapeutic emphasis', 'Long-term nourishment and balancing of Kidney-Liver Yin.', 'Leads hidden Heat outward while nourishing Yin.'],
        ['Decisive symptom', 'Chronic lumbar weakness, tinnitus or night sweating.', 'Night fever that improves by morning without sweat.'],
        ['Exam shortcut', 'Constitutional Yin deficiency.', 'Residual hidden Heat after febrile illness.']
      ],
      why: 'Both can show deficiency Heat, but Liu Wei Di Huang Wan rebuilds a chronic deficient root; Qing Hao Bie Jia Tang clears a lingering pathogen from the Yin level.',
      memory: '<strong>Liu Wei fills the Yin;</strong> <strong>Qing Hao leads hidden Heat out.</strong>',
      safety: 'Persistent fever, unexplained weight loss or recurrent night sweats requires medical evaluation rather than self-treatment.',
      prompts: ['Which formula uses Shu Di Huang?', 'Which matches night fever without sweating?', 'Which treats chronic Liver-Kidney Yin deficiency?'],
      source: 'Source bank: FWB 2.2, Comparison Formulas to Clear Heat; corroborated with formula profiles and Heat-clearing materials.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Liu Wei Di Huang Wan', 'Qing Hao Bie Jia Tang'],
      shared: 'Ambas tratan deficiencia de Yin con signos de Calor vacío y usan Rehmannia, pero difieren en contexto y dirección terapéutica.',
      fast: ['Deficiencia crónica de Yin de Hígado-Riñón: debilidad lumbar y de rodillas, mareo, tinnitus y signos reproductivos.', 'Etapa tardía de enfermedad tibia: fiebre nocturna que cede por la mañana sin sudor y calor profundo en huesos.'],
      rows: [
        ['Patrón raíz', 'Deficiencia de Yin de Hígado y Riñón.', 'Calor alojado en nivel Yin tras enfermedad tibia con Yin y líquidos lesionados.'],
        ['Pista de Rehmannia', 'Shu Di Huang nutre Yin y Esencia.', 'Sheng Di Huang elimina Calor, enfría Sangre y protege Yin.'],
        ['Énfasis', 'Nutrición prolongada del Yin de Riñón-Hígado.', 'Conduce Calor oculto hacia afuera mientras nutre Yin.'],
        ['Síntoma decisivo', 'Debilidad lumbar crónica, tinnitus o sudor nocturno.', 'Fiebre nocturna que mejora por la mañana sin sudar.'],
        ['Atajo', 'Deficiencia constitucional de Yin.', 'Calor residual oculto tras fiebre.']
      ],
      why: 'Liu Wei reconstruye una raíz crónicamente deficiente; Qing Hao elimina un patógeno persistente del nivel Yin.',
      memory: '<strong>Liu Wei llena el Yin;</strong> <strong>Qing Hao saca el Calor oculto.</strong>',
      safety: 'Fiebre persistente, pérdida de peso inexplicada o sudores nocturnos recurrentes requieren evaluación médica.',
      prompts: ['¿Cuál usa Shu Di Huang?', '¿Cuál corresponde a fiebre nocturna sin sudor?', '¿Cuál trata deficiencia crónica Hígado-Riñón?'],
      source: 'Banco fuente: FWB 2.2, Comparison Formulas to Clear Heat; corroborado con perfiles y materiales de Calor.'
    }
  },
  {
    slug: 'zhi-sou-san-vs-sang-ju-yin', title: 'Zhi Sou San vs. Sang Ju Yin',
    category: 'Formulas that Relieve Coughing / Release the Exterior',
    summary: 'Separate a persistent cough after Wind-Cold from an early, mild Wind-Heat cough.',
    search: 'zhi sou san sang ju yin stop coughing mulberry chrysanthemum wind cold wind heat cough exterior throat fever',
    en: {
      eyebrow: 'Formula comparison', names: ['Zhi Sou San', 'Sang Ju Yin'],
      shared: 'Both release a mild Exterior pathogen and direct Lung Qi downward to stop cough; cough is the dominant symptom.',
      fast: ['Residual Wind-Cold cough after most Exterior signs have faded; little fever and thin white sputum.', 'Early Wind-Heat attacking the Lung; mild fever, thirst, sore throat and a floating-rapid pulse.'],
      rows: [
        ['Pathogen', 'Residual Wind-Cold with constrained Lung Qi.', 'Wind-Heat at the Lung defensive level.'],
        ['Thermal clue', 'Little or no Heat; sputum is thin and white.', 'Mild Heat, thirst or sore throat; sputum may be yellow.'],
        ['Primary action', 'Stops cough while gently dispersing the remaining pathogen.', 'Disperses Wind-Heat and clears Lung Heat to stop cough.'],
        ['Exam shortcut', 'The cold is nearly gone, but the cough remains.', 'A mild warm-disease cough has just begun.']
      ],
      why: 'Both are cough-centered and relatively gentle. Temperature and timing separate a lingering Wind-Cold cough from the early stage of Wind-Heat.',
      memory: '<strong>Zhi Sou = cough after Cold;</strong> <strong>Sang Ju = cough at the start of Heat.</strong>',
      safety: 'Shortness of breath, chest pain, coughing blood, high fever or cough lasting more than three weeks requires medical assessment.',
      prompts: ['Which treats residual Wind-Cold cough?', 'Which treats early mild Wind-Heat?', 'What sputum and fever clues distinguish them?'],
      source: 'Source bank: FWB 6.2, Comparison Formulas to Transform Phlegm; corroborated with exterior-releasing materials and individual profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Zhi Sou San', 'Sang Ju Yin'],
      shared: 'Ambas liberan suavemente un patógeno Exterior y hacen descender Qi de Pulmón; la tos es el síntoma dominante.',
      fast: ['Tos residual de Viento-Frío cuando casi desaparecieron los signos externos; poca fiebre y esputo blanco fino.', 'Inicio de Viento-Calor en Pulmón; fiebre leve, sed, garganta dolorida y pulso flotante-rápido.'],
      rows: [
        ['Patógeno', 'Viento-Frío residual con Qi de Pulmón constreñido.', 'Viento-Calor en nivel defensivo de Pulmón.'],
        ['Pista térmica', 'Poco o nada de Calor; esputo blanco y fino.', 'Calor leve, sed o garganta dolorida; esputo posiblemente amarillo.'],
        ['Acción', 'Detiene tos mientras dispersa suavemente el patógeno restante.', 'Dispersa Viento-Calor y limpia Calor pulmonar.'],
        ['Atajo', 'El resfriado casi terminó, pero queda la tos.', 'La tos de enfermedad tibia acaba de comenzar.']
      ],
      why: 'Ambas son suaves y centradas en tos; temperatura y momento separan Viento-Frío residual de Viento-Calor temprano.',
      memory: '<strong>Zhi Sou = tos después del Frío;</strong> <strong>Sang Ju = tos al comenzar el Calor.</strong>',
      safety: 'Disnea, dolor torácico, hemoptisis, fiebre alta o tos de más de tres semanas requiere evaluación médica.',
      prompts: ['¿Cuál trata tos residual de Viento-Frío?', '¿Cuál trata Viento-Calor leve temprano?', '¿Qué pistas de esputo y fiebre distinguen?'],
      source: 'Banco fuente: FWB 6.2, Comparison Formulas to Transform Phlegm; corroborado con materiales del Exterior y perfiles individuales.'
    }
  },
  {
    slug: 'chai-hu-shu-gan-san-vs-xiao-yao-san', title: 'Chai Hu Shu Gan San vs. Xiao Yao San',
    category: 'Formulas that Regulate Qi / Harmonize',
    summary: 'Separate stronger Liver-Qi stagnation with pain from Liver-Qi constraint complicated by Blood and Spleen deficiency.',
    search: 'chai hu shu gan san xiao yao san liver qi stagnation constraint pain blood deficiency spleen fatigue menstruation',
    en: {
      eyebrow: 'Formula comparison', names: ['Chai Hu Shu Gan San', 'Xiao Yao San'],
      shared: 'Both course the Liver, regulate Qi and treat rib-side or abdominal discomfort, emotional constraint and menstrual irregularity.',
      fast: ['Excess Qi stagnation: distending pain and a wiry pulse dominate.', 'Qi constraint with deficiency: fatigue, poor appetite, pale signs and menstrual Blood deficiency accompany the mood symptoms.'],
      rows: [
        ['Root pattern', 'Liver-Qi stagnation, primarily excess.', 'Liver-Qi constraint with Spleen-Qi and Blood deficiency.'],
        ['Pain clue', 'More pronounced distending rib-side, epigastric or abdominal pain.', 'Milder pain with fatigue, poor appetite or dizziness.'],
        ['Therapeutic emphasis', 'Moves Qi and relieves pain more strongly.', 'Courses Liver while nourishing Blood and strengthening Spleen.'],
        ['Exam shortcut', 'Stagnation needs movement.', 'Constraint needs movement plus support.']
      ],
      why: 'The formulas share a Liver-Qi focus, but Xiao Yao San protects and rebuilds deficient Blood and Spleen Qi instead of simply moving harder.',
      memory: '<strong>Shu Gan moves pain;</strong> <strong>Xiao Yao moves and nourishes.</strong>',
      safety: 'Persistent or severe abdominal pain, jaundice, fever or pregnancy-related pain requires medical evaluation.',
      prompts: ['Which is stronger for distending pain?', 'Which includes Blood and Spleen deficiency?', 'What signs reveal the deficient pattern?'],
      source: 'Source bank: FWB 4.2.1, Comparison Formulas to Harmonize; corroborated with individual formula profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Chai Hu Shu Gan San', 'Xiao Yao San'],
      shared: 'Ambas regulan Hígado y Qi y tratan molestias costales/abdominales, restricción emocional e irregularidad menstrual.',
      fast: ['Estancamiento de Qi en exceso: predominan dolor distensivo y pulso de cuerda.', 'Restricción con deficiencia: fatiga, poco apetito, palidez y deficiencia de Sangre acompañan síntomas emocionales.'],
      rows: [
        ['Patrón raíz', 'Estancamiento de Qi de Hígado principalmente excesivo.', 'Restricción de Qi de Hígado con deficiencia de Qi de Bazo y Sangre.'],
        ['Dolor', 'Dolor distensivo costal, epigástrico o abdominal más pronunciado.', 'Dolor más leve con fatiga, poco apetito o mareo.'],
        ['Énfasis', 'Mueve Qi y alivia dolor con mayor fuerza.', 'Regula Hígado mientras nutre Sangre y fortalece Bazo.'],
        ['Atajo', 'El estancamiento necesita movimiento.', 'La restricción necesita movimiento y apoyo.']
      ],
      why: 'Comparten enfoque hepático, pero Xiao Yao protege y reconstruye Sangre y Qi de Bazo en vez de mover con más fuerza.',
      memory: '<strong>Shu Gan mueve el dolor;</strong> <strong>Xiao Yao mueve y nutre.</strong>',
      safety: 'Dolor abdominal persistente o grave, ictericia, fiebre o dolor durante embarazo requiere evaluación médica.',
      prompts: ['¿Cuál es más fuerte para dolor distensivo?', '¿Cuál incluye deficiencia de Sangre y Bazo?', '¿Qué signos muestran deficiencia?'],
      source: 'Banco fuente: FWB 4.2.1, Comparison Formulas to Harmonize; corroborado con perfiles individuales.'
    }
  },
  {
    slug: 'xiao-huo-luo-dan-vs-du-huo-ji-sheng-tang', title: 'Xiao Huo Luo Dan vs. Du Huo Ji Sheng Tang',
    category: 'Formulas that Dispel Wind-Dampness',
    summary: 'Separate severe fixed Cold-Damp obstruction with Phlegm and stasis from chronic painful obstruction with Liver-Kidney and Qi-Blood deficiency.',
    search: 'xiao huo luo dan du huo ji sheng tang wind damp cold painful obstruction bi phlegm blood stasis deficiency liver kidney qi blood',
    en: {
      eyebrow: 'Formula comparison', names: ['Xiao Huo Luo Dan', 'Du Huo Ji Sheng Tang'],
      shared: 'Both dispel Wind-Dampness, unblock the channels and treat chronic painful obstruction with numbness or limited movement.',
      fast: ['Severe, fixed and stabbing pain from Wind-Cold-Damp, Phlegm and Blood stasis.', 'Long-standing pain with weakness, soreness and deficiency of Liver-Kidney, Qi and Blood.'],
      rows: [
        ['Pattern', 'Excess obstruction: Cold-Damp plus Phlegm and stasis.', 'Mixed excess-deficiency: Wind-Damp in a chronically deficient patient.'],
        ['Pain clue', 'Intense, fixed, sharp or boring pain.', 'Chronic aching pain with weakness of back and knees.'],
        ['Therapeutic emphasis', 'Strongly warms, transforms Phlegm and invigorates Blood.', 'Dispels Wind-Damp while tonifying Liver-Kidney and nourishing Qi-Blood.'],
        ['Exam shortcut', 'Hard obstruction needs force.', 'Old obstruction needs support.']
      ],
      why: 'Both address chronic Bi syndrome, but one attacks a severe obstructive excess while the other combines pathogen removal with tonification.',
      memory: '<strong>Xiao Huo Luo breaks blockage;</strong> <strong>Du Huo Ji Sheng supports the weak.</strong>',
      safety: 'Classical Xiao Huo Luo Dan contains processed aconite and must not be self-prepared. New weakness, bowel/bladder changes or fever with joint swelling requires urgent assessment.',
      prompts: ['Which matches fixed stabbing pain?', 'Which matches chronic pain with weak back and knees?', 'Which formula contains processed aconite?'],
      source: 'Source bank: FWB 14.1, Comparison Formulas to Expel Wind; corroborated with individual formula profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Xiao Huo Luo Dan', 'Du Huo Ji Sheng Tang'],
      shared: 'Ambas expulsan Viento-Humedad, desobstruyen canales y tratan Bi crónico con entumecimiento o movimiento limitado.',
      fast: ['Dolor grave, fijo y punzante por Viento-Frío-Humedad, Flema y estasis de Sangre.', 'Dolor prolongado con debilidad y deficiencia de Hígado-Riñón, Qi y Sangre.'],
      rows: [
        ['Patrón', 'Obstrucción excesiva: Frío-Humedad más Flema y estasis.', 'Exceso-deficiencia: Viento-Humedad en paciente crónicamente deficiente.'],
        ['Dolor', 'Intenso, fijo, agudo o perforante.', 'Dolor sordo crónico con debilidad lumbar y de rodillas.'],
        ['Énfasis', 'Calienta fuertemente, transforma Flema y vigoriza Sangre.', 'Expulsa Viento-Humedad mientras tonifica Hígado-Riñón y nutre Qi-Sangre.'],
        ['Atajo', 'Obstrucción dura necesita fuerza.', 'Obstrucción antigua necesita apoyo.']
      ],
      why: 'Una ataca exceso obstructivo grave; la otra combina eliminación del patógeno con tonificación.',
      memory: '<strong>Xiao Huo Luo rompe bloqueo;</strong> <strong>Du Huo Ji Sheng apoya al débil.</strong>',
      safety: 'Xiao Huo Luo Dan clásica contiene acónito procesado y no debe prepararse sin supervisión. Debilidad nueva o fiebre articular exige evaluación.',
      prompts: ['¿Cuál corresponde a dolor fijo punzante?', '¿Cuál a dolor crónico con debilidad lumbar?', '¿Cuál contiene acónito procesado?'],
      source: 'Banco fuente: FWB 14.1, Comparison Formulas to Expel Wind; corroborado con perfiles individuales.'
    }
  },
  {
    slug: 'huo-xiang-zheng-qi-san-vs-ping-wei-san', title: 'Huo Xiang Zheng Qi San vs. Ping Wei San',
    category: 'Formulas that Transform Dampness',
    summary: 'Separate External Wind-Cold with internal Dampness from internal Dampness obstructing the middle burner without a defining Exterior pattern.',
    search: 'huo xiang zheng qi san ping wei san dampness middle burner exterior wind cold nausea vomiting diarrhea epigastric fullness',
    en: {
      eyebrow: 'Formula comparison', names: ['Huo Xiang Zheng Qi San', 'Ping Wei San'],
      shared: 'Both aromatically transform Dampness, regulate Qi and treat nausea, vomiting, diarrhea, poor appetite and epigastric fullness.',
      fast: ['Exterior Wind-Cold plus middle-burner Dampness: chills, fever and headache accompany digestive symptoms.', 'Interior Dampness stagnates in Spleen-Stomach: fullness, heavy limbs and greasy coat without a defining Exterior invasion.'],
      rows: [
        ['Location', 'Exterior and middle burner.', 'Middle burner primarily.'],
        ['Chief emphasis', 'Huo Xiang releases the Exterior and harmonizes the center.', 'Cang Zhu strongly dries Dampness and strengthens Spleen transport.'],
        ['Decisive clue', 'Acute digestive upset with chills, fever or headache.', 'Chronic or uncomplicated Damp fullness and heaviness.'],
        ['Exam shortcut', 'Damp stomach flu pattern.', 'Dampness sitting in the middle.']
      ],
      why: 'The shared digestive picture is separated by an Exterior layer. Huo Xiang Zheng Qi reaches outward; Ping Wei stays centered on internal Dampness.',
      memory: '<strong>Huo Xiang = Exterior + center;</strong> <strong>Ping Wei = level the damp center.</strong>',
      safety: 'Bloody diarrhea, high fever, severe dehydration, persistent vomiting or severe abdominal pain requires medical care.',
      prompts: ['Which includes Exterior Wind-Cold?', 'Which is more strongly drying?', 'What symptoms reveal the Exterior layer?'],
      source: 'Source bank: FWB 5.2, Comparison Formulas to Expel Damp; corroborated with individual formula profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Huo Xiang Zheng Qi San', 'Ping Wei San'],
      shared: 'Ambas transforman aromáticamente Humedad, regulan Qi y tratan náusea, vómito, diarrea, poco apetito y plenitud epigástrica.',
      fast: ['Viento-Frío Exterior más Humedad central: escalofríos, fiebre y cefalea acompañan síntomas digestivos.', 'Humedad interna estanca Bazo-Estómago: plenitud, pesadez y capa grasosa sin invasión Exterior definitoria.'],
      rows: [
        ['Ubicación', 'Exterior y Jiao Medio.', 'Principalmente Jiao Medio.'],
        ['Énfasis', 'Huo Xiang libera Exterior y armoniza centro.', 'Cang Zhu seca Humedad con fuerza y fortalece transporte de Bazo.'],
        ['Pista decisiva', 'Malestar digestivo agudo con escalofríos, fiebre o cefalea.', 'Plenitud y pesadez por Humedad interna sin signos externos.'],
        ['Atajo', 'Patrón húmedo tipo gastroenteritis con Exterior.', 'Humedad asentada en el centro.']
      ],
      why: 'El cuadro digestivo compartido se separa por la capa Exterior: Huo Xiang llega afuera; Ping Wei se centra dentro.',
      memory: '<strong>Huo Xiang = Exterior + centro;</strong> <strong>Ping Wei = nivelar centro húmedo.</strong>',
      safety: 'Diarrea con sangre, fiebre alta, deshidratación, vómito persistente o dolor grave requiere atención médica.',
      prompts: ['¿Cuál incluye Viento-Frío Exterior?', '¿Cuál seca con más fuerza?', '¿Qué síntomas muestran capa Exterior?'],
      source: 'Banco fuente: FWB 5.2, Comparison Formulas to Expel Damp; corroborado con perfiles individuales.'
    }
  },
  {
    slug: 'huo-xiang-zheng-qi-san-vs-ren-shen-bai-du-san', title: 'Huo Xiang Zheng Qi San vs. Ren Shen Bai Du San',
    category: 'Release the Exterior with Internal Deficiency',
    summary: 'Separate Wind-Cold with digestive Dampness from Wind-Cold-Damp in a Qi-deficient patient with stronger generalized pain.',
    search: 'huo xiang zheng qi san ren shen bai du san ginseng defeat toxin wind cold damp qi deficiency digestive vomiting diarrhea body aches',
    en: {
      eyebrow: 'Formula comparison', names: ['Huo Xiang Zheng Qi San', 'Ren Shen Bai Du San'],
      shared: 'Both treat External Wind-Cold-Damp while supporting an impaired middle burner and may show fever, chills, headache, nausea or diarrhea.',
      fast: ['Dampness centers on Stomach and Intestines: vomiting, diarrhea and abdominal fullness dominate.', 'Qi deficiency permits Wind-Cold-Damp invasion: severe headache/body aches, fatigue and a weak pulse dominate.'],
      rows: [
        ['Primary center', 'Digestive Dampness with an Exterior component.', 'Exterior pathogen in a Qi-deficient constitution.'],
        ['Distinct action', 'Aromatically transforms Dampness and rectifies rebellious Qi.', 'Augments Qi while dispersing Wind-Cold-Damp.'],
        ['Decisive clue', 'Prominent vomiting and diarrhea.', 'Prominent generalized aches with weakness.'],
        ['Exam shortcut', 'Wet middle burner.', 'Weak patient with painful Exterior invasion.']
      ],
      why: 'Both span Exterior and Interior, but Huo Xiang Zheng Qi is digestive-Damp centered while Ren Shen Bai Du is deficiency-and-body-ache centered.',
      memory: '<strong>Huo Xiang rectifies the gut;</strong> <strong>Ren Shen Bai Du supports the weak Exterior.</strong>',
      safety: 'Severe dehydration, blood in stool, stiff neck, altered consciousness or breathing difficulty requires urgent evaluation.',
      prompts: ['Which is selected when vomiting and diarrhea dominate?', 'Which when body aches and Qi deficiency dominate?', 'What common pathogen do they address?'],
      source: 'Source bank: FWB 5.2, Comparison Formulas to Expel Damp; corroborated with individual formula profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Huo Xiang Zheng Qi San', 'Ren Shen Bai Du San'],
      shared: 'Ambas tratan Viento-Frío-Humedad externo apoyando un centro alterado; pueden mostrar fiebre, escalofríos, cefalea, náusea o diarrea.',
      fast: ['Humedad centrada en Estómago-Intestinos: predominan vómito, diarrea y plenitud abdominal.', 'Deficiencia de Qi permite invasión: predominan cefalea/dolor corporal fuerte, fatiga y pulso débil.'],
      rows: [
        ['Centro', 'Humedad digestiva con componente Exterior.', 'Patógeno Exterior en constitución deficiente de Qi.'],
        ['Acción distintiva', 'Transforma Humedad aromáticamente y rectifica Qi rebelde.', 'Aumenta Qi mientras dispersa Viento-Frío-Humedad.'],
        ['Pista decisiva', 'Vómito y diarrea prominentes.', 'Dolores generalizados con debilidad.'],
        ['Atajo', 'Jiao Medio mojado.', 'Paciente débil con invasión Exterior dolorosa.']
      ],
      why: 'Huo Xiang se centra en Humedad digestiva; Ren Shen Bai Du en deficiencia y dolor corporal.',
      memory: '<strong>Huo Xiang rectifica intestino;</strong> <strong>Ren Shen Bai Du apoya Exterior débil.</strong>',
      safety: 'Deshidratación grave, sangre en heces, rigidez cervical, confusión o disnea requiere evaluación urgente.',
      prompts: ['¿Cuál si dominan vómito y diarrea?', '¿Cuál si dominan dolor corporal y deficiencia?', '¿Qué patógeno comparten?'],
      source: 'Banco fuente: FWB 5.2, Comparison Formulas to Expel Damp; corroborado con perfiles individuales.'
    }
  },
  {
    slug: 'ping-wei-san-vs-li-zhong-wan', title: 'Ping Wei San vs. Li Zhong Wan',
    category: 'Transform Dampness / Warm the Interior',
    summary: 'Separate excess Dampness obstructing the middle burner from Spleen-Stomach deficiency-Cold.',
    search: 'ping wei san li zhong wan calm stomach regulate middle damp excess spleen stomach deficiency cold fullness diarrhea appetite',
    en: {
      eyebrow: 'Formula comparison', names: ['Ping Wei San', 'Li Zhong Wan'],
      shared: 'Both regulate the middle burner and treat poor appetite, epigastric or abdominal fullness, nausea, vomiting and diarrhea.',
      fast: ['Excess Dampness: heaviness, pronounced fullness and a thick greasy coating.', 'Deficiency-Cold: chronic loose stool, abdominal pain improved by warmth/pressure, fatigue and a pale tongue.'],
      rows: [
        ['Root pattern', 'Dampness obstructing Spleen and Stomach.', 'Spleen-Stomach Qi/Yang deficiency with internal Cold.'],
        ['Primary action', 'Dries Dampness and moves Qi.', 'Warms and tonifies the middle burner.'],
        ['Tongue clue', 'Thick, greasy white coating.', 'Pale tongue with white coating.'],
        ['Exam shortcut', 'Excess wet blockage.', 'Deficient cold center.']
      ],
      why: 'Similar digestive symptoms can result from a pathogen blocking the center or from the center lacking warmth and strength.',
      memory: '<strong>Ping Wei dries what is too much;</strong> <strong>Li Zhong warms what is too weak.</strong>',
      safety: 'Severe abdominal pain, persistent vomiting, gastrointestinal bleeding or dehydration requires medical evaluation.',
      prompts: ['Which treats excess Dampness?', 'Which treats deficiency-Cold?', 'How do tongue and response to warmth distinguish them?'],
      source: 'Source bank: FWB 5.2, Comparison Formulas to Expel Damp; corroborated with FWB 7.1 and individual profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Ping Wei San', 'Li Zhong Wan'],
      shared: 'Ambas regulan Jiao Medio y tratan poco apetito, plenitud, náusea, vómito y diarrea.',
      fast: ['Humedad en exceso: pesadez, plenitud marcada y capa gruesa grasosa.', 'Frío por deficiencia: heces blandas crónicas, dolor mejor con calor/presión, fatiga y lengua pálida.'],
      rows: [
        ['Patrón raíz', 'Humedad obstruye Bazo y Estómago.', 'Deficiencia de Qi/Yang de Bazo-Estómago con Frío interno.'],
        ['Acción', 'Seca Humedad y mueve Qi.', 'Calienta y tonifica Jiao Medio.'],
        ['Lengua', 'Capa blanca gruesa y grasosa.', 'Lengua pálida con capa blanca.'],
        ['Atajo', 'Bloqueo húmedo excesivo.', 'Centro frío y deficiente.']
      ],
      why: 'Síntomas digestivos semejantes surgen de patógeno que bloquea centro o de centro sin calor ni fuerza.',
      memory: '<strong>Ping Wei seca lo excesivo;</strong> <strong>Li Zhong calienta lo débil.</strong>',
      safety: 'Dolor abdominal grave, vómito persistente, sangrado digestivo o deshidratación requiere evaluación médica.',
      prompts: ['¿Cuál trata Humedad excesiva?', '¿Cuál Frío por deficiencia?', '¿Cómo distinguen lengua y respuesta al calor?'],
      source: 'Banco fuente: FWB 5.2, Comparison Formulas to Expel Damp; corroborado con FWB 7.1 y perfiles individuales.'
    }
  },
  {
    slug: 'ba-zheng-san-vs-long-dan-xie-gan-tang', title: 'Ba Zheng San vs. Long Dan Xie Gan Tang',
    category: 'Formulas that Clear Damp-Heat',
    summary: 'Separate Damp-Heat concentrated in the Bladder from Liver-Gallbladder Fire or Damp-Heat pouring into the lower burner.',
    search: 'ba zheng san long dan xie gan tang damp heat bladder liver gallbladder painful urination genital discharge rib side bitter taste',
    en: {
      eyebrow: 'Formula comparison', names: ['Ba Zheng San', 'Long Dan Xie Gan Tang'],
      shared: 'Both clear Damp-Heat from the lower burner, promote urination and may treat painful urination with dark, scanty urine.',
      fast: ['Bladder Damp-Heat: severe urinary burning, urgency, frequency or stones dominate.', 'Liver-Gallbladder Damp-Heat/Fire: rib-side pain, bitter taste, headache, red eyes or genital itching/discharge accompanies urinary signs.'],
      rows: [
        ['Primary channel-organ focus', 'Bladder and urinary tract.', 'Liver and Gallbladder, with downward drainage through the urine.'],
        ['Decisive clue', 'Urinary pain is the central presentation.', 'Liver-Gallbladder signs occur above or in the genital region.'],
        ['Therapeutic emphasis', 'Drains Fire and strongly unblocks painful urinary dribbling.', 'Drains Liver Fire and clears Damp-Heat from all three burners.'],
        ['Exam shortcut', 'Hot urinary tract.', 'Liver Fire/Damp-Heat draining downward.']
      ],
      why: 'Both can produce urinary burning, but Ba Zheng San is urinary-tract centered; Long Dan Xie Gan Tang is selected when the Liver-Gallbladder pattern explains the lower-burner symptoms.',
      memory: '<strong>Ba Zheng burns in the bladder;</strong> <strong>Long Dan starts in Liver-GB.</strong>',
      safety: 'Use only verified Akebia Mu Tong or an appropriate modern substitute—never aristolochic-acid Guan Mu Tong. Fever with flank pain, inability to urinate or blood in urine requires urgent care.',
      prompts: ['Which is centered on painful urination?', 'Which includes bitter taste and rib-side signs?', 'What Mu Tong safety distinction is essential?'],
      source: 'Source bank: FWB 5.2.1, Comparison Formulas to Expel Damp; corroborated with individual profiles and modern safety references.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Ba Zheng San', 'Long Dan Xie Gan Tang'],
      shared: 'Ambas eliminan Humedad-Calor del Jiao Inferior, promueven micción y pueden tratar orina oscura, escasa y dolorosa.',
      fast: ['Humedad-Calor de Vejiga: dominan ardor urinario grave, urgencia, frecuencia o cálculos.', 'Humedad-Calor/Fuego de Hígado-Vesícula: dolor costal, sabor amargo, cefalea, ojos rojos o prurito/flujo genital acompaña signos urinarios.'],
      rows: [
        ['Foco', 'Vejiga y vías urinarias.', 'Hígado y Vesícula Biliar con drenaje descendente por orina.'],
        ['Pista decisiva', 'Dolor urinario es presentación central.', 'Signos Hígado-Vesícula arriba o en región genital.'],
        ['Énfasis', 'Drena Fuego y desobstruye fuertemente la micción dolorosa.', 'Drena Fuego de Hígado y limpia Humedad-Calor de tres Jiaos.'],
        ['Atajo', 'Vías urinarias calientes.', 'Fuego/Humedad-Calor de Hígado baja.']
      ],
      why: 'Ba Zheng se centra en vías urinarias; Long Dan cuando el patrón Hígado-Vesícula explica los síntomas inferiores.',
      memory: '<strong>Ba Zheng arde en Vejiga;</strong> <strong>Long Dan comienza en Hígado-VB.</strong>',
      safety: 'Usar solo Mu Tong de Akebia verificado o sustituto moderno; nunca Guan Mu Tong con ácido aristolóquico. Fiebre lumbar, anuria o hematuria exige urgencia.',
      prompts: ['¿Cuál se centra en micción dolorosa?', '¿Cuál incluye sabor amargo y signos costales?', '¿Qué distinción de Mu Tong es esencial?'],
      source: 'Banco fuente: FWB 5.2.1, Comparison Formulas to Expel Damp; corroborado con perfiles y referencias modernas de seguridad.'
    }
  },
  {
    slug: 'wu-ling-san-vs-zhu-ling-tang-vs-wu-pi-san', title: 'Wu Ling San vs. Zhu Ling Tang vs. Wu Pi San',
    category: 'Formulas that Drain Dampness',
    summary: 'Distinguish water retention from failed Qi transformation, water-Heat with Yin injury and superficial edema with Qi stagnation.',
    search: 'wu ling san zhu ling tang wu pi san five peel water retention edema bladder qi transformation heat yin injury skin qi stagnation',
    en: {
      eyebrow: 'Formula comparison', names: ['Wu Ling San', 'Zhu Ling Tang', 'Wu Pi San'],
      shared: 'All promote urination and treat fluid retention, but they direct treatment toward different mechanisms and tissue levels.',
      fast: ['Bladder Qi-transformation failure with thirst and urinary difficulty.', 'Water-Heat in the lower burner with Yin injury and irritability or urinary Heat.', 'Superficial generalized edema with heavy limbs, fullness and Qi stagnation.'],
      rows: [
        ['Mechanism', 'Taiyang water accumulation; Yang cannot transform fluids.', 'Water combines with Heat and damages Yin.', 'Qi stagnation prevents movement of water in the skin and subcutaneous tissues.'],
        ['Distinct action', 'Warms Yang and transforms Qi.', 'Clears Heat, promotes urination and nourishes Yin.', 'Moves Qi and reduces edema with peel medicinals.'],
        ['Decisive clue', 'Thirst but difficulty processing fluids.', 'Heat signs, thirst and possible painful or bloody urination.', 'Pitting edema with abdominal/epigastric fullness.'],
        ['Exam shortcut', 'Transform water.', 'Drain hot water while protecting Yin.', 'Move superficial water by moving Qi.']
      ],
      why: '“Edema” alone is not enough. Decide whether the failed step is Qi transformation, Heat injuring Yin, or Qi stagnation trapping water superficially.',
      memory: '<strong>Wu Ling transforms;</strong> <strong>Zhu Ling cools;</strong> <strong>Wu Pi moves the skin.</strong>',
      safety: 'Sudden edema, breathlessness, chest pain, no urine, fever with flank pain or edema during pregnancy requires prompt medical evaluation.',
      prompts: ['Which warms Yang?', 'Which protects Yin while clearing Heat?', 'Which uses peel medicinals for superficial edema?'],
      source: 'Source bank: FWB 5.2.1, Comparison Formulas to Expel Damp; corroborated with individual formula profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Wu Ling San', 'Zhu Ling Tang', 'Wu Pi San'],
      shared: 'Todas promueven micción y tratan retención de líquidos, dirigiéndose a mecanismos y niveles tisulares distintos.',
      fast: ['Falla de transformación de Qi de Vejiga con sed y dificultad urinaria.', 'Agua-Calor inferior con lesión de Yin e irritabilidad o Calor urinario.', 'Edema superficial generalizado con pesadez, plenitud y estancamiento de Qi.'],
      rows: [
        ['Mecanismo', 'Acumulación Taiyang; Yang no transforma líquidos.', 'Agua se combina con Calor y daña Yin.', 'Estancamiento de Qi impide mover agua en piel y tejido subcutáneo.'],
        ['Acción distintiva', 'Calienta Yang y transforma Qi.', 'Elimina Calor, promueve orina y nutre Yin.', 'Mueve Qi y reduce edema con cáscaras.'],
        ['Pista decisiva', 'Sed pero dificultad para procesar líquidos.', 'Calor, sed y posible orina dolorosa o sanguinolenta.', 'Edema con fóvea y plenitud abdominal/epigástrica.'],
        ['Atajo', 'Transformar agua.', 'Drenar agua caliente protegiendo Yin.', 'Mover agua superficial moviendo Qi.']
      ],
      why: '“Edema” no basta: decide si falla transformación, Calor lesiona Yin o Qi estancado atrapa agua superficial.',
      memory: '<strong>Wu Ling transforma;</strong> <strong>Zhu Ling enfría;</strong> <strong>Wu Pi mueve piel.</strong>',
      safety: 'Edema súbito, disnea, dolor torácico, anuria, fiebre lumbar o edema en embarazo exige evaluación pronta.',
      prompts: ['¿Cuál calienta Yang?', '¿Cuál protege Yin eliminando Calor?', '¿Cuál usa cáscaras para edema superficial?'],
      source: 'Banco fuente: FWB 5.2.1, Comparison Formulas to Expel Damp; corroborado con perfiles individuales.'
    }
  },
  {
    slug: 'ju-pi-zhu-ru-tang-vs-li-zhong-wan', title: 'Ju Pi Zhu Ru Tang vs. Li Zhong Wan',
    category: 'Formulas that Regulate Qi / Warm the Interior',
    summary: 'Separate Stomach Qi deficiency with residual Heat and hiccup from middle-burner deficiency-Cold with vomiting or diarrhea.',
    search: 'ju pi zhu ru tang li zhong wan tangerine peel bamboo shavings regulate middle stomach qi deficiency heat cold hiccup vomiting diarrhea',
    en: {
      eyebrow: 'Formula comparison', names: ['Ju Pi Zhu Ru Tang', 'Li Zhong Wan'],
      shared: 'Both support deficient middle-burner Qi and redirect rebellious Stomach Qi in nausea or vomiting.',
      fast: ['Stomach Qi deficiency with residual Heat: hiccup, retching, thirst and a tender red tongue.', 'Spleen-Stomach deficiency-Cold: chronic vomiting/diarrhea, abdominal pain, no thirst and a pale tongue.'],
      rows: [
        ['Thermal pattern', 'Deficiency with residual Heat.', 'Deficiency with internal Cold.'],
        ['Primary action', 'Clears Heat and descends rebellious Qi while augmenting Stomach Qi.', 'Warms and tonifies the middle burner.'],
        ['Decisive clue', 'Persistent hiccup or retching with Heat signs.', 'Cold abdominal pain and loose stools improved by warmth.'],
        ['Exam shortcut', 'Weak, hot, rebellious Stomach.', 'Weak, cold middle burner.']
      ],
      why: 'Both may vomit from deficiency, so temperature is decisive: Ju Pi Zhu Ru clears residual Heat; Li Zhong restores deficient warmth.',
      memory: '<strong>Zhu Ru cools the hiccup;</strong> <strong>Li Zhong warms the center.</strong>',
      safety: 'Persistent vomiting or hiccup, inability to keep fluids down, blood, severe pain or neurologic symptoms requires medical evaluation.',
      prompts: ['Which treats deficiency with residual Heat?', 'Which treats deficiency-Cold?', 'Which symptom particularly points to Ju Pi Zhu Ru Tang?'],
      source: 'Source bank: FWB 9.1.2, Comparison Formulas to Regulate the Qi; corroborated with individual formula profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Ju Pi Zhu Ru Tang', 'Li Zhong Wan'],
      shared: 'Ambas apoyan Qi deficiente del centro y hacen descender Qi rebelde de Estómago en náusea o vómito.',
      fast: ['Deficiencia de Qi de Estómago con Calor residual: hipo, arcadas, sed y lengua roja sensible.', 'Frío por deficiencia de Bazo-Estómago: vómito/diarrea crónica, dolor abdominal, ausencia de sed y lengua pálida.'],
      rows: [
        ['Patrón térmico', 'Deficiencia con Calor residual.', 'Deficiencia con Frío interno.'],
        ['Acción', 'Elimina Calor y desciende Qi rebelde mientras aumenta Qi de Estómago.', 'Calienta y tonifica Jiao Medio.'],
        ['Pista decisiva', 'Hipo o arcadas persistentes con signos de Calor.', 'Dolor frío y heces blandas mejorados por calor.'],
        ['Atajo', 'Estómago débil, caliente y rebelde.', 'Jiao Medio débil y frío.']
      ],
      why: 'Ambas pueden vomitar por deficiencia; la temperatura decide: Ju Pi elimina Calor residual y Li Zhong restaura calor deficiente.',
      memory: '<strong>Zhu Ru enfría el hipo;</strong> <strong>Li Zhong calienta centro.</strong>',
      safety: 'Vómito o hipo persistente, incapacidad para retener líquidos, sangre, dolor grave o signos neurológicos requiere evaluación.',
      prompts: ['¿Cuál trata deficiencia con Calor residual?', '¿Cuál Frío por deficiencia?', '¿Qué síntoma apunta a Ju Pi Zhu Ru Tang?'],
      source: 'Banco fuente: FWB 9.1.2, Comparison Formulas to Regulate the Qi; corroborado con perfiles individuales.'
    }
  }
);

cards.push(
  {
    slug: 'zhi-sou-san-vs-ban-xia-bai-zhu-tian-ma-tang', title: 'Zhi Sou San vs. Ban Xia Bai Zhu Tian Ma Tang',
    category: 'Formulas that Transform Phlegm and Stop Coughing',
    summary: 'Separate a residual Exterior cough centered on the Lungs from Wind-Phlegm dizziness rooted in Spleen deficiency.',
    search: 'zhi sou san ban xia bai zhu tian ma tang cough lung wind phlegm dizziness vertigo spleen deficiency residual exterior',
    en: {
      eyebrow: 'Formula comparison', names: ['Zhi Sou San', 'Ban Xia Bai Zhu Tian Ma Tang'],
      shared: 'Both regulate Lung Qi and transform Phlegm, but their defining symptoms and target systems are very different.',
      fast: ['Cough dominates after an Exterior Wind-Cold invasion; the remaining pathogen constrains the Lungs.', 'Dizziness, vertigo, nausea and headache dominate because Spleen deficiency generates Damp-Phlegm and internal Wind.'],
      rows: [
        ['Primary target', 'Lungs and residual Exterior pathogen.', 'Spleen, Liver and head.'],
        ['Main symptom', 'Persistent cough with thin sputum.', 'Vertigo or dizziness with nausea and chest/epigastric fullness.'],
        ['Therapeutic emphasis', 'Gently disperses Wind, disseminates Lung Qi and stops cough.', 'Dries Dampness, transforms Phlegm, strengthens Spleen and extinguishes Wind.'],
        ['Exam shortcut', 'The cold is fading; the cough remains.', 'Phlegm rises with Wind to the head.']
      ],
      why: 'A shared Phlegm label can distract from the clinical center: respiratory persistence versus neurologic dizziness generated by Damp-Phlegm.',
      memory: '<strong>Zhi Sou stops the leftover cough;</strong> <strong>Ban Xia–Tian Ma stops the spinning.</strong>',
      safety: 'Sudden severe dizziness, new neurologic deficits, fainting, chest pain or coughing blood requires urgent medical assessment.',
      prompts: ['Which formula is cough-centered?', 'Which treats Wind-Phlegm vertigo?', 'What organ systems distinguish them?'],
      source: 'Source bank: FWB 6.2 and FWB 6.2.1, Comparison Formulas to Transform Phlegm; corroborated with individual profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Zhi Sou San', 'Ban Xia Bai Zhu Tian Ma Tang'],
      shared: 'Ambas regulan Qi de Pulmón y transforman Flema, pero difieren en síntomas definitorios y sistemas objetivo.',
      fast: ['Predomina tos tras invasión de Viento-Frío; el patógeno restante constriñe Pulmón.', 'Predominan mareo, vértigo, náusea y cefalea porque deficiencia de Bazo genera Humedad-Flema y Viento interno.'],
      rows: [
        ['Objetivo', 'Pulmón y patógeno Exterior residual.', 'Bazo, Hígado y cabeza.'],
        ['Síntoma principal', 'Tos persistente con esputo fino.', 'Vértigo o mareo con náusea y plenitud torácica/epigástrica.'],
        ['Énfasis', 'Dispersa Viento suavemente, difunde Qi pulmonar y detiene tos.', 'Seca Humedad, transforma Flema, fortalece Bazo y extingue Viento.'],
        ['Atajo', 'El resfriado cede; queda la tos.', 'Flema sube con Viento a la cabeza.']
      ],
      why: 'La etiqueta Flema puede distraer: persistencia respiratoria frente a mareo neurológico generado por Humedad-Flema.',
      memory: '<strong>Zhi Sou detiene la tos restante;</strong> <strong>Ban Xia–Tian Ma detiene el giro.</strong>',
      safety: 'Mareo súbito grave, déficit neurológico, desmayo, dolor torácico o hemoptisis exige evaluación urgente.',
      prompts: ['¿Cuál se centra en tos?', '¿Cuál trata vértigo de Viento-Flema?', '¿Qué órganos las distinguen?'],
      source: 'Banco fuente: FWB 6.2 y FWB 6.2.1, Comparison Formulas to Transform Phlegm; corroborado con perfiles individuales.'
    }
  },
  {
    slug: 'ding-chuan-tang-vs-wen-dan-tang', title: 'Ding Chuan Tang vs. Wen Dan Tang',
    category: 'Formulas that Transform Phlegm',
    summary: 'Separate Phlegm-Heat obstructing Lung Qi and causing wheezing from Phlegm-Heat disturbing the Gallbladder, Stomach and Spirit.',
    search: 'ding chuan tang wen dan tang phlegm heat lung wheezing gallbladder stomach spirit insomnia fright bitter taste',
    en: {
      eyebrow: 'Formula comparison', names: ['Ding Chuan Tang', 'Wen Dan Tang'],
      shared: 'Both clear Phlegm-Heat, regulate Qi and may present with chest oppression, a greasy yellow coating and a slippery-rapid pulse.',
      fast: ['Lung Phlegm-Heat with an Exterior constraint: wheezing, cough and thick yellow sputum dominate.', 'Gallbladder-Stomach Phlegm-Heat disturbing the Spirit: insomnia, irritability, easy fright, nausea and bitter taste dominate.'],
      rows: [
        ['Primary target', 'Lungs and Exterior.', 'Gallbladder, Stomach and Spirit.'],
        ['Qi dynamic', 'Descends rebellious Lung Qi to arrest wheezing.', 'Harmonizes Stomach, regulates Gallbladder Qi and settles the Spirit.'],
        ['Decisive clue', 'Severe wheezing with abundant thick sputum.', 'Insomnia or easy fright with bitter taste and nausea.'],
        ['Exam shortcut', 'Hot Phlegm blocks breathing.', 'Hot Phlegm disrupts sleep and digestion.']
      ],
      why: 'The shared Phlegm-Heat mechanism is localized differently. Respiratory obstruction points to Ding Chuan; psycho-digestive disturbance points to Wen Dan.',
      memory: '<strong>Ding Chuan arrests the wheeze;</strong> <strong>Wen Dan settles the disturbed Gallbladder.</strong>',
      safety: 'Ding Chuan Tang contains Ma Huang and requires cardiovascular and medication screening. Severe breathing difficulty is an emergency.',
      prompts: ['Which treats wheezing with thick sputum?', 'Which treats insomnia with bitter taste?', 'How does organ location determine the choice?'],
      source: 'Source bank: FWB 9.1.1, Comparison Formulas to Regulate the Qi; corroborated with FWB 6.2 and individual profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Ding Chuan Tang', 'Wen Dan Tang'],
      shared: 'Ambas eliminan Flema-Calor, regulan Qi y pueden mostrar opresión torácica, capa amarilla grasosa y pulso resbaladizo-rápido.',
      fast: ['Flema-Calor de Pulmón con constricción Exterior: dominan sibilancia, tos y esputo amarillo espeso.', 'Flema-Calor de Vesícula-Estómago altera Shen: dominan insomnio, irritabilidad, sobresalto, náusea y sabor amargo.'],
      rows: [
        ['Objetivo', 'Pulmones y Exterior.', 'Vesícula Biliar, Estómago y Shen.'],
        ['Dinámica de Qi', 'Desciende Qi pulmonar rebelde para detener sibilancia.', 'Armoniza Estómago, regula Qi de Vesícula y calma Shen.'],
        ['Pista decisiva', 'Sibilancia grave con esputo abundante y espeso.', 'Insomnio o sobresalto con sabor amargo y náusea.'],
        ['Atajo', 'Flema caliente bloquea respiración.', 'Flema caliente altera sueño y digestión.']
      ],
      why: 'El mecanismo compartido se localiza distinto: obstrucción respiratoria señala Ding Chuan; alteración psico-digestiva señala Wen Dan.',
      memory: '<strong>Ding Chuan detiene sibilancia;</strong> <strong>Wen Dan asienta Vesícula alterada.</strong>',
      safety: 'Ding Chuan contiene Ma Huang y requiere evaluación cardiovascular y farmacológica. Disnea grave es emergencia.',
      prompts: ['¿Cuál trata sibilancia con esputo espeso?', '¿Cuál insomnio con sabor amargo?', '¿Cómo decide la localización?'],
      source: 'Banco fuente: FWB 9.1.1, Comparison Formulas to Regulate the Qi; corroborado con FWB 6.2 y perfiles individuales.'
    }
  },
  {
    slug: 'si-jun-zi-tang-vs-li-zhong-wan', title: 'Si Jun Zi Tang vs. Li Zhong Wan',
    category: 'Formulas that Tonify Qi / Warm the Interior',
    summary: 'Separate basic Spleen-Qi deficiency from the same deficiency complicated by significant middle-burner Cold.',
    search: 'si jun zi tang li zhong wan four gentlemen regulate middle spleen qi deficiency cold gan jiang poor appetite loose stool abdominal pain',
    en: {
      eyebrow: 'Formula comparison', names: ['Si Jun Zi Tang', 'Li Zhong Wan'],
      shared: 'Both tonify Spleen Qi and treat fatigue, pale complexion, poor appetite, loose stool and a weak pulse; they share Ren Shen, Bai Zhu and Gan Cao.',
      fast: ['Simple Spleen-Qi deficiency without pronounced Cold.', 'Spleen-Stomach deficiency-Cold with abdominal pain, vomiting or diarrhea and cold limbs.'],
      rows: [
        ['Thermal pattern', 'Neutral Qi deficiency.', 'Deficiency-Cold of the middle burner.'],
        ['Distinct ingredient', 'Fu Ling drains Dampness and supports Spleen.', 'Gan Jiang warms the middle and expels Cold.'],
        ['Decisive clue', 'Fatigue and weak digestion predominate.', 'Cold pain improved by warmth and pressure predominates.'],
        ['Exam shortcut', 'Tonify Qi.', 'Tonify Qi and warm Cold.']
      ],
      why: 'Li Zhong Wan resembles a warming adaptation of the basic Spleen-Qi tonic strategy. Strong Cold signs—not merely loose stool—justify the switch.',
      memory: '<strong>Si Jun Zi builds Qi;</strong> <strong>Li Zhong adds dry ginger heat.</strong>',
      safety: 'Persistent vomiting, severe abdominal pain, gastrointestinal bleeding or dehydration requires medical assessment.',
      prompts: ['Which is the basic Spleen-Qi tonic?', 'Which contains Gan Jiang?', 'What Cold signs justify Li Zhong Wan?'],
      source: 'Source bank: FWB 8.2, Comparison Formulas to Tonify; corroborated with FWB 7.1 and individual formula profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Si Jun Zi Tang', 'Li Zhong Wan'],
      shared: 'Ambas tonifican Qi de Bazo y tratan fatiga, palidez, poco apetito, heces blandas y pulso débil; comparten Ren Shen, Bai Zhu y Gan Cao.',
      fast: ['Deficiencia simple de Qi de Bazo sin Frío marcado.', 'Frío por deficiencia de Bazo-Estómago con dolor abdominal, vómito/diarrea y extremidades frías.'],
      rows: [
        ['Patrón térmico', 'Deficiencia neutra de Qi.', 'Frío por deficiencia del Jiao Medio.'],
        ['Ingrediente distintivo', 'Fu Ling drena Humedad y apoya Bazo.', 'Gan Jiang calienta centro y expulsa Frío.'],
        ['Pista decisiva', 'Predominan fatiga y digestión débil.', 'Predomina dolor frío mejor con calor y presión.'],
        ['Atajo', 'Tonificar Qi.', 'Tonificar Qi y calentar Frío.']
      ],
      why: 'Li Zhong es una adaptación caliente de la estrategia tónica básica; signos fuertes de Frío justifican el cambio.',
      memory: '<strong>Si Jun Zi construye Qi;</strong> <strong>Li Zhong añade calor de jengibre seco.</strong>',
      safety: 'Vómito persistente, dolor abdominal grave, sangrado digestivo o deshidratación requiere evaluación médica.',
      prompts: ['¿Cuál es tónico básico de Qi de Bazo?', '¿Cuál contiene Gan Jiang?', '¿Qué signos fríos justifican Li Zhong?'],
      source: 'Banco fuente: FWB 8.2, Comparison Formulas to Tonify; corroborado con FWB 7.1 y perfiles individuales.'
    }
  },
  {
    slug: 'shen-ling-bai-zhu-san-vs-bu-zhong-yi-qi-tang', title: 'Shen Ling Bai Zhu San vs. Bu Zhong Yi Qi Tang',
    category: 'Formulas that Tonify Qi',
    summary: 'Separate Spleen-Qi deficiency with Dampness and chronic diarrhea from sinking middle Qi with prolapse or persistent fever.',
    search: 'shen ling bai zhu san bu zhong yi qi tang spleen qi deficiency dampness diarrhea sinking qi prolapse fatigue appetite',
    en: {
      eyebrow: 'Formula comparison', names: ['Shen Ling Bai Zhu San', 'Bu Zhong Yi Qi Tang'],
      shared: 'Both tonify Spleen Qi and treat fatigue, poor appetite, loose stools, pale tongue and a weak pulse.',
      fast: ['Spleen-Qi deficiency fails to transform fluids: chronic diarrhea, weight loss and Dampness predominate.', 'Middle Qi sinks: prolapse, bearing-down sensation, chronic organ descent or Qi-deficiency fever predominate.'],
      rows: [
        ['Qi failure', 'Transportive failure with Damp accumulation.', 'Elevating and holding failure with sinking.'],
        ['Distinct action', 'Strengthens Spleen, leaches Dampness and stops diarrhea.', 'Raises clear Yang and lifts sunken Qi.'],
        ['Decisive clue', 'Chronic loose stool with Dampness.', 'Prolapse or bearing-down sensation.'],
        ['Exam shortcut', 'Weak Spleen cannot move water.', 'Weak middle cannot hold organs up.']
      ],
      why: 'Both start from Spleen-Qi deficiency, but the failed function differs: transport of fluids versus lifting and containment.',
      memory: '<strong>Shen Ling drains and stops diarrhea;</strong> <strong>Bu Zhong lifts.</strong>',
      safety: 'Blood in stool, significant weight loss, dehydration, a painful non-reducible prolapse or persistent fever requires medical evaluation.',
      prompts: ['Which treats chronic diarrhea with Dampness?', 'Which treats prolapse?', 'What Spleen function fails in each?'],
      source: 'Source bank: FWB 8.2.2, Comparison Formulas to Tonify; corroborated with individual formula profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Shen Ling Bai Zhu San', 'Bu Zhong Yi Qi Tang'],
      shared: 'Ambas tonifican Qi de Bazo y tratan fatiga, poco apetito, heces blandas, lengua pálida y pulso débil.',
      fast: ['Bazo no transforma líquidos: predominan diarrea crónica, pérdida de peso y Humedad.', 'Qi Medio se hunde: predominan prolapso, sensación descendente o fiebre por deficiencia de Qi.'],
      rows: [
        ['Falla de Qi', 'Falla de transporte con acumulación de Humedad.', 'Falla de elevación y contención con hundimiento.'],
        ['Acción distintiva', 'Fortalece Bazo, filtra Humedad y detiene diarrea.', 'Eleva Yang claro y levanta Qi hundido.'],
        ['Pista decisiva', 'Heces blandas crónicas con Humedad.', 'Prolapso o sensación de descenso.'],
        ['Atajo', 'Bazo débil no mueve agua.', 'Centro débil no sostiene órganos.']
      ],
      why: 'Parten de deficiencia de Qi de Bazo, pero falla transporte de líquidos frente a elevación y contención.',
      memory: '<strong>Shen Ling drena y detiene diarrea;</strong> <strong>Bu Zhong eleva.</strong>',
      safety: 'Sangre en heces, pérdida de peso, deshidratación, prolapso doloroso no reducible o fiebre persistente requiere evaluación.',
      prompts: ['¿Cuál trata diarrea crónica con Humedad?', '¿Cuál prolapso?', '¿Qué función de Bazo falla en cada una?'],
      source: 'Banco fuente: FWB 8.2.2, Comparison Formulas to Tonify; corroborado con perfiles individuales.'
    }
  },
  {
    slug: 'gui-pi-tang-vs-ba-zhen-tang', title: 'Gui Pi Tang vs. Ba Zhen Tang',
    category: 'Formulas that Tonify Qi and Blood',
    summary: 'Separate Heart-Spleen deficiency with Spirit or bleeding symptoms from generalized Qi-and-Blood deficiency.',
    search: 'gui pi tang ba zhen tang restore spleen eight treasure qi blood deficiency insomnia palpitations bleeding fatigue pale',
    en: {
      eyebrow: 'Formula comparison', names: ['Gui Pi Tang', 'Ba Zhen Tang'],
      shared: 'Both tonify Qi and nourish Blood and may treat fatigue, pale complexion, dizziness, reduced appetite and a weak pulse.',
      fast: ['Spleen Qi fails to produce/contain Blood and Heart Blood fails to nourish the Spirit: insomnia, palpitations, poor memory or bleeding.', 'Generalized Qi-and-Blood deficiency without a defining Spirit or bleeding presentation.'],
      rows: [
        ['Primary focus', 'Heart and Spleen.', 'Qi and Blood systemically.'],
        ['Distinct action', 'Calms the Spirit and helps Spleen contain Blood.', 'Combines the core four-herb Qi and four-herb Blood tonic strategies.'],
        ['Decisive clue', 'Insomnia/palpitations or chronic bleeding with Spleen deficiency.', 'Broad fatigue, pallor, dizziness and weakness.'],
        ['Exam shortcut', 'Qi-Blood deficiency with Heart/bleeding consequences.', 'Basic combined Qi-and-Blood deficiency.']
      ],
      why: 'Gui Pi Tang is more targeted than a general tonic: it links Spleen failure to Heart-Blood and containment symptoms.',
      memory: '<strong>Gui Pi returns Blood to Heart and vessels;</strong> <strong>Ba Zhen builds all eight.</strong>',
      safety: 'Heavy bleeding, fainting, chest pain, severe palpitations or new neurologic symptoms requires urgent assessment.',
      prompts: ['Which treats insomnia and palpitations?', 'Which is the basic Qi-and-Blood tonic?', 'Which treats bleeding from Spleen failure to contain Blood?'],
      source: 'Source bank: FWB 8.2.2, Comparison Formulas to Tonify; corroborated with individual formula profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Gui Pi Tang', 'Ba Zhen Tang'],
      shared: 'Ambas tonifican Qi y nutren Sangre; tratan fatiga, palidez, mareo, poco apetito y pulso débil.',
      fast: ['Qi de Bazo no produce/contiene Sangre y Sangre de Corazón no nutre Shen: insomnio, palpitaciones, mala memoria o sangrado.', 'Deficiencia general de Qi y Sangre sin presentación definitoria de Shen o sangrado.'],
      rows: [
        ['Foco', 'Corazón y Bazo.', 'Qi y Sangre sistémicos.'],
        ['Acción distintiva', 'Calma Shen y ayuda a Bazo a contener Sangre.', 'Combina estrategias básicas de cuatro tónicos de Qi y cuatro de Sangre.'],
        ['Pista decisiva', 'Insomnio/palpitaciones o sangrado crónico con Bazo deficiente.', 'Fatiga, palidez, mareo y debilidad general.'],
        ['Atajo', 'Deficiencia Qi-Sangre con consecuencias cardíacas/hemorrágicas.', 'Deficiencia combinada básica.']
      ],
      why: 'Gui Pi es más dirigida: conecta falla de Bazo con Sangre de Corazón y contención vascular.',
      memory: '<strong>Gui Pi devuelve Sangre a Corazón y vasos;</strong> <strong>Ba Zhen construye los ocho.</strong>',
      safety: 'Sangrado abundante, desmayo, dolor torácico, palpitaciones graves o síntomas neurológicos nuevos exige urgencia.',
      prompts: ['¿Cuál trata insomnio y palpitaciones?', '¿Cuál es tónico básico Qi-Sangre?', '¿Cuál trata sangrado por Bazo no conteniendo?'],
      source: 'Banco fuente: FWB 8.2.2, Comparison Formulas to Tonify; corroborado con perfiles individuales.'
    }
  },
  {
    slug: 'wu-zhu-yu-tang-vs-li-zhong-wan', title: 'Wu Zhu Yu Tang vs. Li Zhong Wan',
    category: 'Formulas that Warm the Interior',
    summary: 'Separate Liver-Stomach Cold with rebellious Qi and vertex headache from Spleen-Stomach deficiency-Cold centered on digestion.',
    search: 'wu zhu yu tang li zhong wan evodia regulate middle interior cold liver stomach spleen vomiting vertex headache diarrhea',
    en: {
      eyebrow: 'Formula comparison', names: ['Wu Zhu Yu Tang', 'Li Zhong Wan'],
      shared: 'Both warm the Interior, support the middle burner and treat vomiting, abdominal discomfort, cold limbs and a slow or weak pulse.',
      fast: ['Cold in Liver/Stomach drives Qi upward: severe vomiting, acid regurgitation and vertex headache.', 'Spleen-Stomach deficiency-Cold fails to transform food and fluids: chronic diarrhea, poor appetite and abdominal pain.'],
      rows: [
        ['Primary organs', 'Liver and Stomach; may involve Kidney.', 'Spleen and Stomach.'],
        ['Qi direction', 'Strongly descends rebellious Qi.', 'Warms and restores middle-burner transformation.'],
        ['Decisive clue', 'Vertex headache with vomiting or acid regurgitation.', 'Chronic loose stool with cold abdominal pain.'],
        ['Exam shortcut', 'Cold rebellion rises.', 'Cold deficiency weakens the center.']
      ],
      why: 'Both warm vomiting patterns, but Wu Zhu Yu Tang is chosen for upward Cold rebellion and Liver involvement; Li Zhong for deficient middle transport.',
      memory: '<strong>Wu Zhu Yu sends Cold rebellion down;</strong> <strong>Li Zhong warms the weak center.</strong>',
      safety: 'Severe headache with neurologic symptoms, blood in vomit/stool, dehydration or severe abdominal pain requires urgent evaluation.',
      prompts: ['Which treats vertex headache with vomiting?', 'Which treats chronic deficiency-Cold diarrhea?', 'How does Qi direction differ?'],
      source: 'Source bank: FWB 7.1, Comparison Formulas to Warm the Interior; corroborated with individual formula profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Wu Zhu Yu Tang', 'Li Zhong Wan'],
      shared: 'Ambas calientan Interior, apoyan centro y tratan vómito, molestia abdominal, extremidades frías y pulso lento o débil.',
      fast: ['Frío en Hígado-Estómago impulsa Qi arriba: vómito grave, regurgitación ácida y cefalea de vértex.', 'Frío por deficiencia de Bazo-Estómago no transforma alimentos/líquidos: diarrea crónica, poco apetito y dolor abdominal.'],
      rows: [
        ['Órganos', 'Hígado y Estómago; puede incluir Riñón.', 'Bazo y Estómago.'],
        ['Dirección de Qi', 'Desciende fuertemente Qi rebelde.', 'Calienta y restaura transformación central.'],
        ['Pista decisiva', 'Cefalea de vértex con vómito o reflujo ácido.', 'Heces blandas crónicas con dolor frío.'],
        ['Atajo', 'Rebeldía fría asciende.', 'Deficiencia fría debilita centro.']
      ],
      why: 'Wu Zhu Yu se elige por rebeldía fría ascendente y Hígado; Li Zhong por transporte central deficiente.',
      memory: '<strong>Wu Zhu Yu baja rebeldía fría;</strong> <strong>Li Zhong calienta centro débil.</strong>',
      safety: 'Cefalea grave con signos neurológicos, sangre en vómito/heces, deshidratación o dolor grave exige urgencia.',
      prompts: ['¿Cuál trata cefalea de vértex con vómito?', '¿Cuál diarrea crónica por Frío?', '¿Cómo difiere dirección de Qi?'],
      source: 'Banco fuente: FWB 7.1, Comparison Formulas to Warm the Interior; corroborado con perfiles individuales.'
    }
  },
  {
    slug: 'da-jian-zhong-tang-vs-li-zhong-wan', title: 'Da Jian Zhong Tang vs. Li Zhong Wan',
    category: 'Formulas that Warm the Interior',
    summary: 'Separate severe Middle-Jiao Cold with intense abdominal pain and rebellious Qi from milder chronic Spleen-Stomach deficiency-Cold.',
    search: 'da jian zhong tang li zhong wan major construct middle regulate middle severe interior cold abdominal pain vomiting diarrhea',
    en: {
      eyebrow: 'Formula comparison', names: ['Da Jian Zhong Tang', 'Li Zhong Wan'],
      shared: 'Both warm and tonify the middle burner for deficiency-Cold with abdominal pain and digestive dysfunction.',
      fast: ['Severe internal Cold: intense abdominal pain, vomiting, inability to eat and visible or palpable abdominal movement.', 'Moderate chronic deficiency-Cold: dull pain, loose stool, poor appetite and fatigue.'],
      rows: [
        ['Relative strength', 'Stronger and more urgent warming.', 'Gentler foundational warming-tonification.'],
        ['Distinct ingredients', 'Shu Jiao and Yi Tang strongly disperse Cold and relieve pain.', 'Bai Zhu strengthens Spleen and dries Dampness.'],
        ['Decisive clue', 'Severe pain and rebellious Qi with marked Cold.', 'Chronic diarrhea and weakness.'],
        ['Exam shortcut', 'Major Cold pain.', 'Routine middle deficiency-Cold.']
      ],
      why: 'These formulas share a deficient middle but differ in intensity. Da Jian Zhong treats severe Cold obstruction and rebellion, not merely chronic weak digestion.',
      memory: '<strong>Da Jian Zhong for dramatic Cold pain;</strong> <strong>Li Zhong for chronic cold weakness.</strong>',
      safety: 'Severe or rigid abdominal pain, persistent vomiting, abdominal distention with no stool/gas, fever or bleeding is an emergency, not a home-treatment pattern.',
      prompts: ['Which is stronger for severe Cold pain?', 'Which is more appropriate for chronic loose stool?', 'What findings require emergency referral?'],
      source: 'Source bank: FWB 7.1 and FWB 7.1.1, Comparison Formulas to Warm the Interior; corroborated with formula references.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Da Jian Zhong Tang', 'Li Zhong Wan'],
      shared: 'Ambas calientan y tonifican Jiao Medio para Frío por deficiencia con dolor abdominal y disfunción digestiva.',
      fast: ['Frío interno grave: dolor intenso, vómito, incapacidad para comer y movimiento abdominal visible/palpable.', 'Frío deficiente crónico moderado: dolor sordo, heces blandas, poco apetito y fatiga.'],
      rows: [
        ['Fuerza', 'Calentamiento más fuerte y urgente.', 'Calentamiento-tonificación fundamental más suave.'],
        ['Ingredientes distintivos', 'Shu Jiao y Yi Tang dispersan Frío y alivian dolor con fuerza.', 'Bai Zhu fortalece Bazo y seca Humedad.'],
        ['Pista decisiva', 'Dolor grave y Qi rebelde con Frío marcado.', 'Diarrea crónica y debilidad.'],
        ['Atajo', 'Dolor mayor por Frío.', 'Frío deficiente central habitual.']
      ],
      why: 'Comparten centro deficiente pero difieren en intensidad: Da Jian Zhong trata obstrucción fría grave y rebeldía.',
      memory: '<strong>Da Jian Zhong para dolor frío dramático;</strong> <strong>Li Zhong para debilidad fría crónica.</strong>',
      safety: 'Dolor abdominal rígido/grave, vómito persistente, distensión sin heces/gases, fiebre o sangrado es emergencia.',
      prompts: ['¿Cuál es más fuerte para dolor por Frío?', '¿Cuál para heces blandas crónicas?', '¿Qué hallazgos exigen urgencia?'],
      source: 'Banco fuente: FWB 7.1 y FWB 7.1.1, Comparison Formulas to Warm the Interior; corroborado con referencias.'
    }
  },
  {
    slug: 'tian-ma-gou-teng-yin-vs-ban-xia-bai-zhu-tian-ma-tang', title: 'Tian Ma Gou Teng Yin vs. Ban Xia Bai Zhu Tian Ma Tang',
    category: 'Formulas that Extinguish Wind / Transform Phlegm',
    summary: 'Separate Liver-Yang rising with internal Wind and Heat from Wind-Phlegm dizziness generated by Spleen deficiency.',
    search: 'tian ma gou teng yin ban xia bai zhu tian ma tang dizziness vertigo liver yang wind heat wind phlegm spleen deficiency',
    en: {
      eyebrow: 'Formula comparison', names: ['Tian Ma Gou Teng Yin', 'Ban Xia Bai Zhu Tian Ma Tang'],
      shared: 'Both contain Tian Ma, extinguish internal Wind and treat dizziness, vertigo, headache or unsteadiness.',
      fast: ['Liver-Yang rises with Heat and Wind: headache, tinnitus, irritability, insomnia, red tongue and wiry-rapid pulse.', 'Spleen deficiency produces Damp-Phlegm and Wind: heavy head, nausea, chest fullness, greasy coat and slippery pulse.'],
      rows: [
        ['Root pattern', 'Liver-Yang rising with internal Wind and Heat.', 'Spleen deficiency with Damp-Phlegm generating Wind.'],
        ['Thermal clue', 'Heat and agitation.', 'Dampness, heaviness and digestive weakness.'],
        ['Distinct action', 'Calms Liver, clears Heat and extinguishes Wind.', 'Strengthens Spleen, dries Dampness and transforms Phlegm.'],
        ['Exam shortcut', 'Hot rising dizziness.', 'Phlegmy heavy dizziness.']
      ],
      why: 'Tian Ma and dizziness are shared; tongue coat, pulse and associated Heat versus Damp signs reveal the mechanism.',
      memory: '<strong>Gou Teng cools the rising Liver;</strong> <strong>Ban Xia dries the spinning Phlegm.</strong>',
      safety: 'Sudden severe vertigo, one-sided weakness, slurred speech, facial droop, inability to walk or severe new headache requires emergency stroke evaluation.',
      prompts: ['Which has red tongue and wiry-rapid pulse?', 'Which has greasy coat and nausea?', 'What role does Tian Ma share?'],
      source: 'Source bank: FWB 14.1.1, Comparison Formulas to Expel Wind; corroborated with FWB 6.2 and individual profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Tian Ma Gou Teng Yin', 'Ban Xia Bai Zhu Tian Ma Tang'],
      shared: 'Ambas contienen Tian Ma, extinguen Viento interno y tratan mareo, vértigo, cefalea o inestabilidad.',
      fast: ['Yang de Hígado asciende con Calor y Viento: cefalea, tinnitus, irritabilidad, insomnio, lengua roja y pulso cuerda-rápido.', 'Bazo deficiente produce Humedad-Flema y Viento: cabeza pesada, náusea, plenitud, capa grasosa y pulso resbaladizo.'],
      rows: [
        ['Patrón raíz', 'Ascenso de Yang de Hígado con Viento interno y Calor.', 'Deficiencia de Bazo con Humedad-Flema generando Viento.'],
        ['Pista térmica', 'Calor y agitación.', 'Humedad, pesadez y debilidad digestiva.'],
        ['Acción distintiva', 'Calma Hígado, elimina Calor y extingue Viento.', 'Fortalece Bazo, seca Humedad y transforma Flema.'],
        ['Atajo', 'Mareo caliente ascendente.', 'Mareo pesado y flemoso.']
      ],
      why: 'Comparten Tian Ma y mareo; capa, pulso y signos asociados de Calor o Humedad revelan el mecanismo.',
      memory: '<strong>Gou Teng enfría Hígado ascendente;</strong> <strong>Ban Xia seca Flema que gira.</strong>',
      safety: 'Vértigo súbito grave, debilidad unilateral, habla alterada, caída facial, incapacidad de caminar o cefalea nueva grave exige emergencia.',
      prompts: ['¿Cuál tiene lengua roja y pulso cuerda-rápido?', '¿Cuál capa grasosa y náusea?', '¿Qué función comparte Tian Ma?'],
      source: 'Banco fuente: FWB 14.1.1, Comparison Formulas to Expel Wind; corroborado con FWB 6.2 y perfiles individuales.'
    }
  },
  {
    slug: 'tao-he-cheng-qi-tang-vs-wu-ling-san', title: 'Tao He Cheng Qi Tang vs. Wu Ling San',
    category: 'Lower-Burner Taiyang Patterns',
    summary: 'Separate Blood accumulation with Heat in the lower burner from water accumulation caused by impaired Bladder Qi transformation.',
    search: 'tao he cheng qi tang wu ling san taiyang lower burner blood accumulation water accumulation bladder urination mental symptoms thirst',
    en: {
      eyebrow: 'Formula comparison', names: ['Tao He Cheng Qi Tang', 'Wu Ling San'],
      shared: 'Both treat lower-burner Taiyang accumulation patterns involving the Bladder region and may present with lower-abdominal fullness.',
      fast: ['Blood accumulation with Heat: urination remains normal; acute lower-abdominal pain, mental agitation and Blood-stasis signs dominate.', 'Water accumulation from failed Qi transformation: urinary difficulty, thirst, edema or vomiting after drinking dominate.'],
      rows: [
        ['Accumulated substance', 'Blood with Heat.', 'Water and fluids.'],
        ['Urination', 'Typically normal.', 'Scanty or difficult.'],
        ['Distinct action', 'Purges Heat and breaks lower-burner Blood stasis.', 'Promotes urination and warms Yang to transform Qi.'],
        ['Exam shortcut', 'Lower abdominal stasis + normal urine.', 'Fluid retention + abnormal urine.']
      ],
      why: 'Urination is the classic separator between Taiyang Blood accumulation and Taiyang water accumulation.',
      memory: '<strong>Tao He: Blood, urine okay;</strong> <strong>Wu Ling: water, urine blocked.</strong>',
      safety: 'Tao He Cheng Qi Tang is strongly moving and purgative and is avoided in pregnancy. Acute abdominal pain, pregnancy bleeding, urinary retention or altered mental status requires urgent care.',
      prompts: ['Which pattern preserves normal urination?', 'Which formula restores Bladder Qi transformation?', 'What substance accumulates in each?'],
      source: 'Source bank: FWB 10.1, Comparison Formulas to Invigorate the Blood; corroborated with FWB 5.2 and formula profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Tao He Cheng Qi Tang', 'Wu Ling San'],
      shared: 'Ambas tratan acumulación Taiyang inferior en región de Vejiga y pueden presentar plenitud hipogástrica.',
      fast: ['Acumulación de Sangre con Calor: micción normal; dominan dolor hipogástrico agudo, agitación mental y estasis.', 'Acumulación de agua por falla de transformación: dominan dificultad urinaria, sed, edema o vómito tras beber.'],
      rows: [
        ['Sustancia acumulada', 'Sangre con Calor.', 'Agua y líquidos.'],
        ['Micción', 'Generalmente normal.', 'Escasa o difícil.'],
        ['Acción distintiva', 'Purga Calor y rompe estasis inferior.', 'Promueve orina y calienta Yang para transformar Qi.'],
        ['Atajo', 'Estasis inferior + orina normal.', 'Retención de líquido + orina anormal.']
      ],
      why: 'La micción es el separador clásico entre acumulación Taiyang de Sangre y de agua.',
      memory: '<strong>Tao He: Sangre, orina normal;</strong> <strong>Wu Ling: agua, orina bloqueada.</strong>',
      safety: 'Tao He mueve y purga con fuerza y se evita en embarazo. Dolor agudo, sangrado en embarazo, retención urinaria o confusión exige urgencia.',
      prompts: ['¿Qué patrón conserva micción normal?', '¿Cuál restaura transformación de Vejiga?', '¿Qué se acumula en cada uno?'],
      source: 'Banco fuente: FWB 10.1, Comparison Formulas to Invigorate the Blood; corroborado con FWB 5.2 y perfiles.'
    }
  },
  {
    slug: 'tao-he-cheng-qi-tang-vs-tiao-wei-cheng-qi-tang', title: 'Tao He Cheng Qi Tang vs. Tiao Wei Cheng Qi Tang',
    category: 'Invigorate Blood / Drain Downward',
    summary: 'Separate lower-burner Blood stasis with Heat from Yangming bowel Heat and Dryness causing constipation.',
    search: 'tao he cheng qi tang tiao wei cheng qi tang peach pit regulate stomach blood stasis heat constipation lower burner yangming',
    en: {
      eyebrow: 'Formula comparison', names: ['Tao He Cheng Qi Tang', 'Tiao Wei Cheng Qi Tang'],
      shared: 'Both contain Da Huang, Mang Xiao and Zhi Gan Cao and purge Heat downward, but only one targets Blood stasis.',
      fast: ['Lower-burner Blood stasis with Heat: acute lower-abdominal pain/fullness, normal urination and agitation or gynecologic stasis signs.', 'Yangming Heat and Dryness: constipation, thirst, irritability and a dry yellow tongue coating without defining Blood-stasis signs.'],
      rows: [
        ['Primary pathology', 'Blood stasis with Heat.', 'Bowel Heat and Dryness.'],
        ['Distinct ingredients', 'Tao Ren and Gui Zhi move Blood and open the lower burner.', 'No Blood-moving pair; regulates Stomach and softens stool.'],
        ['Decisive clue', 'Lower-abdominal stasis signs with normal urination.', 'Dry constipation and Yangming Heat.'],
        ['Exam shortcut', 'Cheng Qi plus Blood movement.', 'Cheng Qi for dry stool.']
      ],
      why: 'The shared purgative trio can obscure the purpose. Tao He adds a Blood-stasis strategy; Tiao Wei remains focused on Heat-Dryness in the bowels.',
      memory: '<strong>Tao Ren marks Blood stasis;</strong> <strong>Tiao Wei marks dry Yangming stool.</strong>',
      safety: 'Both are strong purgative formulas and are inappropriate for self-treatment. Avoid in pregnancy; suspected obstruction, severe pain, vomiting or no passage of gas requires emergency assessment.',
      prompts: ['Which contains Tao Ren and Gui Zhi?', 'Which treats Yangming Heat-Dryness?', 'What three ingredients are shared?'],
      source: 'Source bank: FWB 10.1, Comparison Formulas to Invigorate the Blood; corroborated with FWB 3.2 and individual profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Tao He Cheng Qi Tang', 'Tiao Wei Cheng Qi Tang'],
      shared: 'Ambas contienen Da Huang, Mang Xiao y Zhi Gan Cao y purgan Calor hacia abajo; solo una trata estasis de Sangre.',
      fast: ['Estasis de Sangre inferior con Calor: dolor/plenitud hipogástrica aguda, micción normal y agitación o estasis ginecológica.', 'Calor-Sequedad Yangming: estreñimiento, sed, irritabilidad y capa amarilla seca sin signos definitorios de estasis.'],
      rows: [
        ['Patología', 'Estasis de Sangre con Calor.', 'Calor y Sequedad intestinal.'],
        ['Ingredientes distintivos', 'Tao Ren y Gui Zhi mueven Sangre y abren Jiao Inferior.', 'Sin pareja movilizadora; regula Estómago y ablanda heces.'],
        ['Pista decisiva', 'Estasis hipogástrica con micción normal.', 'Estreñimiento seco y Calor Yangming.'],
        ['Atajo', 'Cheng Qi más movimiento de Sangre.', 'Cheng Qi para heces secas.']
      ],
      why: 'El trío purgante compartido puede confundir: Tao He añade estrategia de estasis; Tiao Wei se centra en Calor-Sequedad intestinal.',
      memory: '<strong>Tao Ren señala estasis;</strong> <strong>Tiao Wei señala heces secas Yangming.</strong>',
      safety: 'Son purgantes fuertes y no para automedicación. Evitar en embarazo; obstrucción sospechada, dolor grave, vómito o ausencia de gases exige emergencia.',
      prompts: ['¿Cuál contiene Tao Ren y Gui Zhi?', '¿Cuál trata Calor-Sequedad Yangming?', '¿Qué tres ingredientes comparten?'],
      source: 'Banco fuente: FWB 10.1, Comparison Formulas to Invigorate the Blood; corroborado con FWB 3.2 y perfiles individuales.'
    }
  }
);

cards.push(
  {
    slug: 'er-chen-tang-vs-wen-dan-tang-vs-ban-xia-bai-zhu-tian-ma-tang', title: 'Er Chen Tang vs. Wen Dan Tang vs. Ban Xia Bai Zhu Tian Ma Tang',
    category: 'Formulas that Transform Phlegm',
    summary: 'Distinguish basic Damp-Phlegm, Phlegm-Heat disturbing the Spirit and Wind-Phlegm rising to the head.',
    search: 'er chen tang wen dan tang ban xia bai zhu tian ma tang damp phlegm heat gallbladder spirit wind vertigo cough',
    en: {
      eyebrow: 'Formula comparison', names: ['Er Chen Tang', 'Wen Dan Tang', 'Ban Xia Bai Zhu Tian Ma Tang'],
      shared: 'All transform Phlegm, regulate Qi and harmonize the Stomach; nausea, fullness and a slippery pulse may occur in each.',
      fast: ['Basic Damp-Phlegm: cough and copious white sputum.', 'Phlegm-Heat disturbs Gallbladder/Stomach and Spirit: insomnia, fright and bitter taste.', 'Wind-Phlegm rises to the head: dizziness, vertigo and headache.'],
      rows: [
        ['Primary location', 'Lung and middle burner.', 'Gallbladder, Stomach and Spirit.', 'Spleen, Liver and head.'],
        ['Thermal quality', 'Damp-Phlegm, usually cool or neutral.', 'Phlegm-Heat.', 'Damp-Phlegm generating internal Wind.'],
        ['Decisive clue', 'Copious thin white sputum.', 'Insomnia or easy fright with greasy yellow coat.', 'Vertigo with nausea and heavy head.'],
        ['Exam shortcut', 'White Phlegm cough.', 'Hot Phlegm disturbs sleep.', 'Phlegm makes the room spin.']
      ],
      why: 'These formulas share a Ban-Xia-centered strategy. The organ target and dominant symptom—not the word “Phlegm” alone—determine the answer.',
      memory: '<strong>Er Chen coughs;</strong> <strong>Wen Dan cannot sleep;</strong> <strong>Ban Xia–Tian Ma spins.</strong>',
      safety: 'Sudden neurologic deficits, severe breathing difficulty, chest pain or altered consciousness requires urgent medical assessment.',
      prompts: ['Which is the foundational Damp-Phlegm formula?', 'Which treats Phlegm-Heat insomnia?', 'Which treats Wind-Phlegm vertigo?'],
      source: 'Source bank: FWB 6.2 and FWB 6.2.1, Comparison Formulas to Transform Phlegm; corroborated with individual profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Er Chen Tang', 'Wen Dan Tang', 'Ban Xia Bai Zhu Tian Ma Tang'],
      shared: 'Todas transforman Flema, regulan Qi y armonizan Estómago; pueden presentar náusea, plenitud y pulso resbaladizo.',
      fast: ['Humedad-Flema básica: tos y esputo blanco abundante.', 'Flema-Calor altera Vesícula/Estómago y Shen: insomnio, sobresalto y sabor amargo.', 'Viento-Flema asciende a cabeza: mareo, vértigo y cefalea.'],
      rows: [
        ['Localización', 'Pulmón y Jiao Medio.', 'Vesícula Biliar, Estómago y Shen.', 'Bazo, Hígado y cabeza.'],
        ['Cualidad térmica', 'Humedad-Flema, generalmente fresca o neutra.', 'Flema-Calor.', 'Humedad-Flema genera Viento interno.'],
        ['Pista decisiva', 'Esputo blanco fino y abundante.', 'Insomnio/sobresalto con capa amarilla grasosa.', 'Vértigo con náusea y cabeza pesada.'],
        ['Atajo', 'Tos con Flema blanca.', 'Flema caliente altera sueño.', 'Flema hace girar.']
      ],
      why: 'Comparten estrategia centrada en Ban Xia; el órgano y síntoma dominante determinan la respuesta.',
      memory: '<strong>Er Chen tose;</strong> <strong>Wen Dan no duerme;</strong> <strong>Ban Xia–Tian Ma gira.</strong>',
      safety: 'Déficit neurológico súbito, disnea grave, dolor torácico o alteración de conciencia exige urgencia.',
      prompts: ['¿Cuál es fórmula base de Humedad-Flema?', '¿Cuál trata insomnio por Flema-Calor?', '¿Cuál vértigo por Viento-Flema?'],
      source: 'Banco fuente: FWB 6.2 y FWB 6.2.1, Comparison Formulas to Transform Phlegm; corroborado con perfiles individuales.'
    }
  },
  {
    slug: 'si-ni-san-vs-xiao-yao-san-vs-yue-ju-wan', title: 'Si Ni San vs. Xiao Yao San vs. Yue Ju Wan',
    category: 'Formulas that Regulate Qi',
    summary: 'Separate constrained Yang in the limbs, Liver-Qi constraint with deficiency and the six stagnations.',
    search: 'si ni san xiao yao san yue ju wan qi constraint liver spleen blood deficiency six stagnations cold extremities depression',
    en: {
      eyebrow: 'Formula comparison', names: ['Si Ni San', 'Xiao Yao San', 'Yue Ju Wan'],
      shared: 'All regulate constrained Qi and may treat emotional depression, chest/rib-side fullness or digestive discomfort.',
      fast: ['Qi constraint blocks Yang from reaching the extremities: cold hands and feet with wiry pulse.', 'Liver-Qi constraint with Spleen-Qi and Blood deficiency: fatigue, poor appetite and menstrual signs.', 'Qi stagnation generates the six stagnations—Qi, Blood, Phlegm, Fire, Dampness and Food.'],
      rows: [
        ['Root pattern', 'Constrained Yang dynamics.', 'Liver-Spleen disharmony with deficiency.', 'Multiple stagnations, beginning with Qi.'],
        ['Decisive clue', 'Cold extremities without true Yang collapse.', 'Constraint plus fatigue, pallor or poor appetite.', 'Mixed signs of several stagnations.'],
        ['Primary emphasis', 'Spreads Qi and restores Yang movement.', 'Courses Liver, nourishes Blood and strengthens Spleen.', 'Moves Qi broadly to release accumulated constraint.'],
        ['Exam shortcut', 'Constraint makes the hands cold.', 'Constraint in a deficient patient.', 'Everything is stuck.']
      ],
      why: 'All move constraint, but the complication differs: blocked Yang movement, deficient Blood/Spleen Qi, or multiple secondary stagnations.',
      memory: '<strong>Si Ni reaches the limbs;</strong> <strong>Xiao Yao supports;</strong> <strong>Yue Ju unblocks six.</strong>',
      safety: 'Cold extremities with fainting, weak pulse, chest pain or altered consciousness may indicate shock and requires emergency care.',
      prompts: ['Which treats cold extremities from constraint?', 'Which combines movement with Blood/Spleen support?', 'Which addresses six stagnations?'],
      source: 'Source bank: FWB 9.1, Comparison Formulas to Regulate the Qi; corroborated with harmonizing materials and individual profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Si Ni San', 'Xiao Yao San', 'Yue Ju Wan'],
      shared: 'Todas regulan Qi constreñido y pueden tratar depresión emocional, plenitud torácica/costal o molestia digestiva.',
      fast: ['Restricción de Qi impide que Yang llegue a extremidades: manos/pies fríos con pulso de cuerda.', 'Restricción de Qi de Hígado con deficiencia de Bazo y Sangre: fatiga, poco apetito y signos menstruales.', 'Estancamiento de Qi genera seis estancamientos: Qi, Sangre, Flema, Fuego, Humedad y Alimento.'],
      rows: [
        ['Patrón raíz', 'Dinámica de Yang constreñida.', 'Desarmonía Hígado-Bazo con deficiencia.', 'Múltiples estancamientos iniciados por Qi.'],
        ['Pista decisiva', 'Extremidades frías sin colapso verdadero de Yang.', 'Restricción más fatiga, palidez o poco apetito.', 'Signos mixtos de varios estancamientos.'],
        ['Énfasis', 'Difunde Qi y restaura movimiento de Yang.', 'Regula Hígado, nutre Sangre y fortalece Bazo.', 'Mueve Qi ampliamente para liberar restricción.'],
        ['Atajo', 'Restricción enfría manos.', 'Restricción en paciente deficiente.', 'Todo está estancado.']
      ],
      why: 'Todas mueven restricción, pero cambia la complicación: Yang bloqueado, deficiencia de Sangre/Bazo o múltiples estancamientos.',
      memory: '<strong>Si Ni llega a extremidades;</strong> <strong>Xiao Yao apoya;</strong> <strong>Yue Ju desbloquea seis.</strong>',
      safety: 'Extremidades frías con desmayo, pulso débil, dolor torácico o confusión puede indicar shock y exige emergencia.',
      prompts: ['¿Cuál trata extremidades frías por restricción?', '¿Cuál combina movimiento y apoyo?', '¿Cuál trata seis estancamientos?'],
      source: 'Banco fuente: FWB 9.1, Comparison Formulas to Regulate the Qi; corroborado con armonización y perfiles individuales.'
    }
  },
  {
    slug: 'wu-zhu-yu-tang-vs-xiao-jian-zhong-tang-vs-da-jian-zhong-tang', title: 'Wu Zhu Yu Tang vs. Xiao Jian Zhong Tang vs. Da Jian Zhong Tang',
    category: 'Formulas that Warm the Interior',
    summary: 'Distinguish Cold rebellion, moderate deficiency-Cold spasm and severe internal Cold abdominal pain.',
    search: 'wu zhu yu tang xiao jian zhong tang da jian zhong tang interior cold vomiting vertex headache abdominal pain spasm severe',
    en: {
      eyebrow: 'Formula comparison', names: ['Wu Zhu Yu Tang', 'Xiao Jian Zhong Tang', 'Da Jian Zhong Tang'],
      shared: 'All warm the middle and treat abdominal pain or vomiting from Interior Cold with deficiency.',
      fast: ['Liver-Stomach Cold rebellion: vomiting, acid regurgitation and vertex headache.', 'Moderate deficiency-Cold with spasmodic abdominal pain relieved by pressure and warmth.', 'Severe deep Cold with intense pain, vomiting and visible/palpable abdominal movement.'],
      rows: [
        ['Primary dynamic', 'Cold Qi rebels upward.', 'Middle deficiency causes tension and spasm.', 'Severe Cold obstructs and forces Qi rebellion.'],
        ['Pain intensity', 'Pain may occur, but vomiting/headache defines it.', 'Intermittent, cramping and relieved by pressure.', 'Intense, persistent or dramatic.'],
        ['Distinct strategy', 'Warms Liver/Stomach and descends rebellion.', 'Warms and nourishes while harmonizing Yin-Yang.', 'Strongly warms, disperses Cold and relieves severe pain.'],
        ['Exam shortcut', 'Cold rises.', 'Cold cramps.', 'Cold attacks intensely.']
      ],
      why: 'Temperature is shared; direction and severity distinguish upward rebellion, nutritive spasm and major Cold obstruction.',
      memory: '<strong>Wu Zhu Yu rises to the vertex;</strong> <strong>Xiao Jian eases cramps;</strong> <strong>Da Jian treats major pain.</strong>',
      safety: 'Severe rigid abdominal pain, repeated vomiting, distention with no stool/gas, fever or bleeding requires emergency evaluation.',
      prompts: ['Which treats vertex headache with vomiting?', 'Which treats pain relieved by pressure?', 'Which is strongest for severe Cold pain?'],
      source: 'Source bank: FWB 7.1.1, Comparison Formulas to Warm the Interior; corroborated with individual formula references.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Wu Zhu Yu Tang', 'Xiao Jian Zhong Tang', 'Da Jian Zhong Tang'],
      shared: 'Todas calientan centro y tratan dolor abdominal o vómito por Frío Interior con deficiencia.',
      fast: ['Rebeldía fría Hígado-Estómago: vómito, reflujo ácido y cefalea de vértex.', 'Frío deficiente moderado con dolor espasmódico mejor por presión y calor.', 'Frío profundo grave con dolor intenso, vómito y movimiento abdominal visible/palpable.'],
      rows: [
        ['Dinámica', 'Qi frío se rebela hacia arriba.', 'Deficiencia central causa tensión y espasmo.', 'Frío grave obstruye y fuerza rebeldía.'],
        ['Intensidad', 'Puede doler, pero vómito/cefalea define.', 'Intermitente, cólico y mejor por presión.', 'Intenso, persistente o dramático.'],
        ['Estrategia', 'Calienta Hígado-Estómago y desciende rebeldía.', 'Calienta/nutre y armoniza Yin-Yang.', 'Calienta fuertemente, dispersa Frío y alivia dolor grave.'],
        ['Atajo', 'Frío asciende.', 'Frío produce cólicos.', 'Frío ataca intensamente.']
      ],
      why: 'La temperatura es compartida; dirección y gravedad separan rebeldía ascendente, espasmo nutritivo y obstrucción mayor.',
      memory: '<strong>Wu Zhu Yu sube al vértex;</strong> <strong>Xiao Jian alivia cólicos;</strong> <strong>Da Jian trata dolor mayor.</strong>',
      safety: 'Dolor rígido grave, vómito repetido, distensión sin heces/gases, fiebre o sangrado requiere emergencia.',
      prompts: ['¿Cuál trata cefalea de vértex con vómito?', '¿Cuál dolor mejor con presión?', '¿Cuál es más fuerte para dolor frío?'],
      source: 'Banco fuente: FWB 7.1.1, Comparison Formulas to Warm the Interior; corroborado con referencias individuales.'
    }
  },
  {
    slug: 'jin-gui-shen-qi-wan-vs-zhen-wu-tang-vs-si-ni-tang', title: 'Jin Gui Shen Qi Wan vs. Zhen Wu Tang vs. Si Ni Tang',
    category: 'Formulas that Warm Yang',
    summary: 'Grade Kidney-Yang deficiency from chronic functional weakness to water accumulation and acute devastated-Yang collapse.',
    search: 'jin gui shen qi wan zhen wu tang si ni tang kidney yang deficiency water accumulation collapse cold edema urination fu zi',
    en: {
      eyebrow: 'Formula comparison', names: ['Jin Gui Shen Qi Wan', 'Zhen Wu Tang', 'Si Ni Tang'],
      shared: 'All warm Yang and may use Fu Zi for Cold, weakness, edema or impaired fluid metabolism; severity and treatment direction differ.',
      fast: ['Chronic Kidney-Yang deficiency with weak back/knees, urinary dysfunction and cold signs.', 'Spleen-Kidney Yang deficiency produces water accumulation, edema, heaviness and dizziness.', 'Devastated Yang collapses acutely with profuse cold sweat, severe cold limbs and a faint pulse.'],
      rows: [
        ['Severity', 'Chronic deficiency.', 'Deficiency complicated by water retention.', 'Acute life-threatening collapse.'],
        ['Primary action', 'Warms Kidney Yang and restores Qi transformation.', 'Warms Yang and promotes movement of accumulated water.', 'Rescues devastated Yang and restores the pulse.'],
        ['Decisive clue', 'Long-term lumbar/urinary/reproductive weakness.', 'Edema and fluid signs dominate.', 'Cold sweat, severe cold limbs and minute pulse.'],
        ['Exam shortcut', 'Rebuild fire.', 'Move water.', 'Rescue now.']
      ],
      why: 'All are “Yang formulas,” but they answer different clinical questions: chronic restoration, water management or emergency rescue.',
      memory: '<strong>Jin Gui rekindles;</strong> <strong>Zhen Wu drains;</strong> <strong>Si Ni rescues.</strong>',
      safety: 'Fu Zi must be professionally processed and prescribed. Collapse signs require emergency services—not herbal self-treatment.',
      prompts: ['Which treats chronic Kidney-Yang deficiency?', 'Which treats Yang-deficiency edema?', 'Which is the emergency rescue formula?'],
      source: 'Source bank: FWB 8.2.1, Comparison Formulas to Tonify; corroborated with FWB 7.1 and individual profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Jin Gui Shen Qi Wan', 'Zhen Wu Tang', 'Si Ni Tang'],
      shared: 'Todas calientan Yang y pueden usar Fu Zi para Frío, debilidad, edema o metabolismo de líquidos alterado; difieren gravedad y dirección.',
      fast: ['Deficiencia crónica de Yang de Riñón con debilidad lumbar/rodillas, disfunción urinaria y Frío.', 'Deficiencia Yang de Bazo-Riñón produce agua acumulada, edema, pesadez y mareo.', 'Yang devastado colapsa agudamente con sudor frío profuso, extremidades heladas y pulso mínimo.'],
      rows: [
        ['Gravedad', 'Deficiencia crónica.', 'Deficiencia complicada por retención de agua.', 'Colapso agudo potencialmente mortal.'],
        ['Acción', 'Calienta Yang renal y restaura transformación de Qi.', 'Calienta Yang y moviliza agua acumulada.', 'Rescata Yang devastado y restaura pulso.'],
        ['Pista decisiva', 'Debilidad lumbar/urinaria/reproductiva prolongada.', 'Dominan edema y líquidos.', 'Sudor frío, extremidades heladas y pulso diminuto.'],
        ['Atajo', 'Reconstruir fuego.', 'Mover agua.', 'Rescatar ahora.']
      ],
      why: 'Responden a preguntas distintas: restauración crónica, manejo de agua o rescate urgente.',
      memory: '<strong>Jin Gui reaviva;</strong> <strong>Zhen Wu drena;</strong> <strong>Si Ni rescata.</strong>',
      safety: 'Fu Zi debe estar procesado y prescrito profesionalmente. Signos de colapso exigen emergencias, no automedicación.',
      prompts: ['¿Cuál trata deficiencia crónica renal?', '¿Cuál edema por deficiencia Yang?', '¿Cuál es rescate urgente?'],
      source: 'Banco fuente: FWB 8.2.1, Comparison Formulas to Tonify; corroborado con FWB 7.1 y perfiles individuales.'
    }
  },
  {
    slug: 'jiao-ai-tang-vs-wen-jing-tang-vs-gu-chong-tang-vs-gu-jing-wan', title: 'Jiao Ai Tang vs. Wen Jing Tang vs. Gu Chong Tang vs. Gu Jing Wan',
    category: 'Formulas that Stop Uterine Bleeding',
    summary: 'Distinguish deficiency-Cold bleeding, Cold with stasis, Spleen-Qi collapse and Kidney-Yin deficiency Heat.',
    search: 'jiao ai tang wen jing tang gu chong tang gu jing wan uterine bleeding pregnancy cold stasis spleen qi kidney yin heat',
    en: {
      eyebrow: 'Formula comparison', names: ['Jiao Ai Tang', 'Wen Jing Tang', 'Gu Chong Tang', 'Gu Jing Wan'],
      shared: 'All treat uterine bleeding, but the color, amount, thermal signs, timing and deficient organ system identify the formula.',
      fast: ['Deficiency-Cold with Blood deficiency, often pregnancy-related bleeding.', 'Uterine Cold with Blood deficiency and stasis causing irregular flow and pain.', 'Spleen Qi fails to contain Blood, causing sudden or profuse gushing.', 'Kidney-Yin deficiency Heat causes chronic dark-red bleeding.'],
      rows: [
        ['Primary pattern', 'Deficiency-Cold and Blood deficiency.', 'Cold, Blood deficiency and stasis.', 'Spleen-Qi deficiency with collapse of containment.', 'Kidney-Yin deficiency with Heat.'],
        ['Bleeding clue', 'Persistent pale bleeding with abdominal discomfort.', 'Irregular scanty or prolonged flow with clots/pain.', 'Sudden heavy or flooding bleeding.', 'Prolonged dark-red bleeding with Heat signs.'],
        ['Distinct action', 'Warms channels, nourishes Blood and stops bleeding.', 'Warms/moves Blood and regulates menses.', 'Tonifies Qi and secures Chong vessel.', 'Nourishes Yin, clears Heat and stabilizes menses.'],
        ['Exam shortcut', 'Cold pregnancy-type bleeding.', 'Cold stagnant menses.', 'Qi cannot hold Blood.', 'Hot deficient bleeding.']
      ],
      why: '“Uterine bleeding” is only the starting clue. Thermal quality, flow and pathogenesis distinguish the four strategies.',
      memory: '<strong>Jiao Ai warms and holds;</strong> <strong>Wen Jing warms and moves;</strong> <strong>Gu Chong lifts and secures;</strong> <strong>Gu Jing cools and secures.</strong>',
      safety: 'Heavy bleeding, fainting, pregnancy bleeding or postmenopausal bleeding requires urgent gynecologic evaluation.',
      prompts: ['Which formula is associated with pregnancy bleeding?', 'Which treats sudden gushing?', 'Which treats deficiency-Heat bleeding?'],
      source: 'Source bank: FWB 11.1, Comparison Formulas to Stop Bleeding; corroborated with FWB 12.1 and individual profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Jiao Ai Tang', 'Wen Jing Tang', 'Gu Chong Tang', 'Gu Jing Wan'],
      shared: 'Todas tratan sangrado uterino; color, cantidad, temperatura, momento y órgano deficiente identifican la fórmula.',
      fast: ['Frío por deficiencia con Sangre deficiente, a menudo sangrado del embarazo.', 'Frío uterino con deficiencia y estasis causa flujo irregular y dolor.', 'Qi de Bazo no contiene Sangre y causa hemorragia súbita/profusa.', 'Calor por deficiencia de Yin renal causa sangrado rojo oscuro crónico.'],
      rows: [
        ['Patrón', 'Frío deficiente y Sangre deficiente.', 'Frío, deficiencia y estasis de Sangre.', 'Deficiencia de Qi de Bazo con falla de contención.', 'Deficiencia de Yin de Riñón con Calor.'],
        ['Sangrado', 'Persistente y pálido con molestia abdominal.', 'Irregular, escaso/prolongado con coágulos/dolor.', 'Súbito, abundante o en inundación.', 'Prolongado rojo oscuro con signos de Calor.'],
        ['Acción', 'Calienta canales, nutre Sangre y detiene sangrado.', 'Calienta/mueve Sangre y regula menstruación.', 'Tonifica Qi y asegura vaso Chong.', 'Nutre Yin, elimina Calor y estabiliza menses.'],
        ['Atajo', 'Sangrado frío tipo embarazo.', 'Menstruación fría-estancada.', 'Qi no contiene Sangre.', 'Sangrado caliente deficiente.']
      ],
      why: '“Sangrado uterino” es solo inicio; cualidad térmica, flujo y patogénesis separan estrategias.',
      memory: '<strong>Jiao Ai calienta y sostiene;</strong> <strong>Wen Jing calienta y mueve;</strong> <strong>Gu Chong eleva y asegura;</strong> <strong>Gu Jing enfría y asegura.</strong>',
      safety: 'Sangrado abundante, desmayo, sangrado en embarazo o posmenopáusico requiere evaluación ginecológica urgente.',
      prompts: ['¿Cuál se asocia con sangrado del embarazo?', '¿Cuál trata inundación súbita?', '¿Cuál sangrado por Calor vacío?'],
      source: 'Banco fuente: FWB 11.1, Comparison Formulas to Stop Bleeding; corroborado con FWB 12.1 y perfiles individuales.'
    }
  },
  {
    slug: 'shi-hui-san-vs-ke-xue-fang-vs-huai-hua-san-vs-xiao-ji-yin-zi', title: 'Shi Hui San vs. Ke Xue Fang vs. Huai Hua San vs. Xiao Ji Yin Zi',
    category: 'Formulas that Stop Bleeding and Clear Heat',
    summary: 'Match Heat-related bleeding to broad acute hemorrhage, the Lungs, the Intestines or the urinary tract.',
    search: 'shi hui san ke xue fang huai hua san xiao ji yin zi bleeding heat hematemesis hemoptysis stool urine location',
    en: {
      eyebrow: 'Formula comparison', names: ['Shi Hui San', 'Ke Xue Fang', 'Huai Hua San', 'Xiao Ji Yin Zi'],
      shared: 'All cool Heat and stop bleeding; location and accompanying symptoms are the fastest exam clues.',
      fast: ['Broad acute Heat bleeding, especially vomiting or coughing blood and nosebleed.', 'Coughing blood from Liver Fire invading the Lungs.', 'Bright-red intestinal or rectal bleeding from Wind-Heat.', 'Blood in urine with painful, burning urination from lower-burner Heat.'],
      rows: [
        ['Primary location', 'Multiple sites, often upper body.', 'Lungs.', 'Large Intestine and rectum.', 'Bladder and urinary tract.'],
        ['Defining symptom', 'Acute profuse bleeding.', 'Hemoptysis with cough and irritability.', 'Blood in stool or hemorrhoidal bleeding.', 'Hematuria with dysuria.'],
        ['Distinct action', 'Cools Blood and broadly stops hemorrhage.', 'Clears Liver/Lung Heat and stops coughing blood.', 'Clears Intestinal Wind-Heat and stops bleeding.', 'Cools Blood, promotes urination and unblocks painful dribbling.'],
        ['Exam shortcut', 'Many hot bleeds.', 'Blood from Lung.', 'Blood from bowel.', 'Blood in urine.']
      ],
      why: 'All may be described simply as “Heat bleeding.” The anatomic outlet is the efficient discriminator.',
      memory: '<strong>Shi Hui—many;</strong> <strong>Ke Xue—cough;</strong> <strong>Huai Hua—stool;</strong> <strong>Xiao Ji—urine.</strong>',
      safety: 'Coughing/vomiting blood, black stool, heavy rectal bleeding or visible blood in urine requires prompt medical evaluation and may be an emergency.',
      prompts: ['Which treats hematuria with burning?', 'Which treats intestinal bleeding?', 'Which specifically treats coughing blood?'],
      source: 'Source bank: FWB 11.1, Comparison Formulas to Stop Bleeding; corroborated with individual formula profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Shi Hui San', 'Ke Xue Fang', 'Huai Hua San', 'Xiao Ji Yin Zi'],
      shared: 'Todas enfrían Calor y detienen sangrado; localización y síntomas acompañantes son las pistas más rápidas.',
      fast: ['Sangrado agudo amplio por Calor, especialmente vómito/tos con sangre y epistaxis.', 'Tos con sangre por Fuego de Hígado invadiendo Pulmón.', 'Sangrado intestinal/rectal rojo vivo por Viento-Calor.', 'Sangre en orina con micción dolorosa y ardiente por Calor inferior.'],
      rows: [
        ['Localización', 'Múltiples sitios, a menudo parte superior.', 'Pulmones.', 'Intestino Grueso y recto.', 'Vejiga y vías urinarias.'],
        ['Síntoma', 'Hemorragia aguda profusa.', 'Hemoptisis con tos e irritabilidad.', 'Sangre en heces o hemorroides.', 'Hematuria con disuria.'],
        ['Acción', 'Enfría Sangre y detiene hemorragia ampliamente.', 'Elimina Calor Hígado/Pulmón y detiene hemoptisis.', 'Elimina Viento-Calor intestinal y detiene sangrado.', 'Enfría Sangre, promueve orina y alivia la micción dolorosa.'],
        ['Atajo', 'Muchos sangrados calientes.', 'Sangre de Pulmón.', 'Sangre intestinal.', 'Sangre en orina.']
      ],
      why: 'Todas pueden describirse como “sangrado por Calor”; la salida anatómica es el discriminador eficiente.',
      memory: '<strong>Shi Hui—muchos;</strong> <strong>Ke Xue—tos;</strong> <strong>Huai Hua—heces;</strong> <strong>Xiao Ji—orina.</strong>',
      safety: 'Toser/vomitar sangre, heces negras, sangrado rectal fuerte o sangre visible en orina requiere evaluación pronta y puede ser emergencia.',
      prompts: ['¿Cuál trata hematuria ardiente?', '¿Cuál sangrado intestinal?', '¿Cuál específicamente hemoptisis?'],
      source: 'Banco fuente: FWB 11.1, Comparison Formulas to Stop Bleeding; corroborado con perfiles individuales.'
    }
  },
  {
    slug: 'tao-he-cheng-qi-tang-vs-xue-fu-zhu-yu-tang-vs-gui-zhi-fu-ling-wan-vs-wen-jing-tang', title: 'Tao He Cheng Qi Tang vs. Xue Fu Zhu Yu Tang vs. Gui Zhi Fu Ling Wan vs. Wen Jing Tang',
    category: 'Formulas that Invigorate Blood',
    summary: 'Match Blood stasis to lower-burner Heat, the chest, a uterine mass or uterine Cold with deficiency.',
    search: 'tao he cheng qi tang xue fu zhu yu tang gui zhi fu ling wan wen jing tang blood stasis heat chest uterus mass cold deficiency',
    en: {
      eyebrow: 'Formula comparison', names: ['Tao He Cheng Qi Tang', 'Xue Fu Zhu Yu Tang', 'Gui Zhi Fu Ling Wan', 'Wen Jing Tang'],
      shared: 'All invigorate Blood and treat fixed pain or gynecologic symptoms, but location and associated pathogen determine the choice.',
      fast: ['Acute lower-burner Blood stasis with Heat and normal urination.', 'Qi stagnation and Blood stasis in the chest with chest pain or chronic headache.', 'Relatively chronic uterine Blood-stasis mass, often during pregnancy only under specialist care.', 'Uterine Cold with Blood deficiency and stasis causing irregular menses and cold pain.'],
      rows: [
        ['Location', 'Lower abdomen/lower burner.', 'Chest and upper burner.', 'Uterus with fixed mass.', 'Uterus and channels.'],
        ['Associated factor', 'Heat and bowel excess.', 'Qi stagnation.', 'Phlegm-fluid/Blood-stasis mass.', 'Cold plus Blood deficiency.'],
        ['Decisive clue', 'Acute pain/fullness with normal urine.', 'Chest pain, dark complexion or stubborn headache.', 'Mobile fetus with persistent uterine mass/bleeding pattern.', 'Cold lower abdomen and irregular flow.'],
        ['Exam shortcut', 'Hot lower stasis.', 'Chest stasis.', 'Uterine mass.', 'Cold deficient stasis.']
      ],
      why: '“Blood stasis” identifies the family, not the final answer. Map the stasis by location and the factor attached to it.',
      memory: '<strong>Tao He—hot below;</strong> <strong>Xue Fu—chest;</strong> <strong>Gui Zhi Fu Ling—mass;</strong> <strong>Wen Jing—cold womb.</strong>',
      safety: 'These are not interchangeable in pregnancy. Severe chest pain, acute abdominal pain, heavy bleeding or pregnancy bleeding requires urgent evaluation.',
      prompts: ['Which targets chest Blood stasis?', 'Which targets a uterine mass?', 'Which combines Cold, deficiency and stasis?'],
      source: 'Source bank: FWB 10.1, Comparison Formulas to Invigorate the Blood; corroborated with FWB 11.1 and individual profiles.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Tao He Cheng Qi Tang', 'Xue Fu Zhu Yu Tang', 'Gui Zhi Fu Ling Wan', 'Wen Jing Tang'],
      shared: 'Todas vigorizan Sangre y tratan dolor fijo o síntomas ginecológicos; localización y patógeno asociado deciden.',
      fast: ['Estasis aguda inferior con Calor y micción normal.', 'Estancamiento de Qi y Sangre en tórax con dolor torácico o cefalea crónica.', 'Masa uterina crónica por estasis, en embarazo solo bajo especialista.', 'Frío uterino con deficiencia y estasis causa menstruación irregular y dolor frío.'],
      rows: [
        ['Localización', 'Hipogastrio/Jiao Inferior.', 'Tórax/Jiao Superior.', 'Útero con masa fija.', 'Útero y canales.'],
        ['Factor asociado', 'Calor y exceso intestinal.', 'Estancamiento de Qi.', 'Masa Flema-líquido/estasis.', 'Frío más deficiencia de Sangre.'],
        ['Pista decisiva', 'Dolor/plenitud aguda con orina normal.', 'Dolor torácico, tez oscura o cefalea persistente.', 'Masa uterina con patrón de sangrado.', 'Hipogastrio frío y flujo irregular.'],
        ['Atajo', 'Estasis caliente abajo.', 'Estasis torácica.', 'Masa uterina.', 'Estasis fría-deficiente.']
      ],
      why: '“Estasis de Sangre” identifica familia; luego se mapea por lugar y factor asociado.',
      memory: '<strong>Tao He—caliente abajo;</strong> <strong>Xue Fu—tórax;</strong> <strong>Gui Zhi Fu Ling—masa;</strong> <strong>Wen Jing—útero frío.</strong>',
      safety: 'No son intercambiables en embarazo. Dolor torácico, dolor abdominal agudo, sangrado fuerte o sangrado del embarazo exige urgencia.',
      prompts: ['¿Cuál trata estasis torácica?', '¿Cuál masa uterina?', '¿Cuál combina Frío, deficiencia y estasis?'],
      source: 'Banco fuente: FWB 10.1, Comparison Formulas to Invigorate the Blood; corroborado con FWB 11.1 y perfiles individuales.'
    }
  },
  {
    slug: 'zhen-ren-yang-zang-tang-vs-si-shen-wan', title: 'Zhen Ren Yang Zang Tang vs. Si Shen Wan',
    category: 'Formulas that Stabilize and Bind',
    summary: 'Separate severe chronic diarrhea from Spleen-Kidney deficiency-Cold from characteristic pre-dawn diarrhea due to Kidney-Yang deficiency.',
    search: 'zhen ren yang zang tang si shen wan chronic diarrhea dysentery predawn fifth watch spleen kidney yang deficiency cold astringe',
    en: {
      eyebrow: 'Formula comparison', names: ['Zhen Ren Yang Zang Tang', 'Si Shen Wan'],
      shared: 'Both warm Spleen-Kidney deficiency-Cold, astringe the Intestines and treat chronic diarrhea with abdominal pain and weakness.',
      fast: ['Severe, long-standing diarrhea or dysenteric disorder with loss of control and broad Qi-Blood deficiency.', 'Characteristic pre-dawn “fifth-watch” diarrhea from Kidney-Yang deficiency.'],
      rows: [
        ['Timing/clue', 'Persistent day-and-night chronic leakage.', 'Occurs predictably before dawn.'],
        ['Primary emphasis', 'Strongly astringes, warms the middle and supports Qi/Blood.', 'Warms Kidney and Spleen Yang and binds the Intestines.'],
        ['Decisive clue', 'Severe chronic diarrhea or dysentery with exhaustion.', 'Pre-dawn diarrhea with cold limbs and weak low back.'],
        ['Exam shortcut', 'Advanced chronic leakage.', 'Fifth-watch diarrhea.']
      ],
      why: 'The pre-dawn timing is the high-yield Si Shen Wan clue; Zhen Ren Yang Zang is broader and more strongly astringent for severe chronic leakage.',
      memory: '<strong>Si Shen owns the dawn;</strong> <strong>Yang Zang closes severe leakage.</strong>',
      safety: 'Classical Zhen Ren Yang Zang Tang contains obsolete opium poppy husk; modern practice requires a legal, safe substitute. Bloody stool, fever, dehydration or weight loss needs medical evaluation.',
      prompts: ['Which treats fifth-watch diarrhea?', 'Which is broader for severe chronic leakage?', 'What obsolete ingredient requires substitution?'],
      source: 'Formula profiles and Astringent mnemonic materials; corroborated with safety and obsolete-substance references.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Zhen Ren Yang Zang Tang', 'Si Shen Wan'],
      shared: 'Ambas calientan Frío por deficiencia Bazo-Riñón, astringen Intestinos y tratan diarrea crónica con dolor y debilidad.',
      fast: ['Diarrea/disentería grave y prolongada con pérdida de control y deficiencia amplia de Qi-Sangre.', 'Diarrea característica antes del amanecer por deficiencia de Yang renal.'],
      rows: [
        ['Momento/pista', 'Fuga crónica persistente día y noche.', 'Ocurre previsiblemente antes del amanecer.'],
        ['Énfasis', 'Astringe fuertemente, calienta centro y apoya Qi/Sangre.', 'Calienta Yang de Riñón-Bazo y contiene Intestinos.'],
        ['Pista decisiva', 'Diarrea/disentería crónica grave con agotamiento.', 'Diarrea preamanecer con extremidades frías y lumbar débil.'],
        ['Atajo', 'Fuga crónica avanzada.', 'Diarrea de quinta vigilia.']
      ],
      why: 'La hora preamanecer señala Si Shen; Zhen Ren Yang Zang es más amplia y fuertemente astringente.',
      memory: '<strong>Si Shen posee el amanecer;</strong> <strong>Yang Zang cierra fuga grave.</strong>',
      safety: 'Zhen Ren Yang Zang clásica contiene cáscara de amapola obsoleta; práctica moderna exige sustituto legal y seguro. Sangre, fiebre, deshidratación o pérdida de peso requiere evaluación.',
      prompts: ['¿Cuál trata diarrea de quinta vigilia?', '¿Cuál fuga crónica grave?', '¿Qué ingrediente obsoleto exige sustitución?'],
      source: 'Perfiles de fórmulas y materiales mnemónicos de Astringentes; corroborado con referencias de seguridad y sustancias obsoletas.'
    }
  },
  {
    slug: 'zuo-gui-wan-vs-liu-wei-di-huang-wan', title: 'Zuo Gui Wan vs. Liu Wei Di Huang Wan',
    category: 'Formulas that Tonify Yin',
    summary: 'Separate strong, enriching Kidney Yin-Essence supplementation from balanced Liver-Kidney Yin tonification with gentle draining.',
    search: 'zuo gui wan liu wei di huang wan kidney liver yin essence deficiency tonify enrich three tonics three drainers',
    en: {
      eyebrow: 'Formula comparison', names: ['Zuo Gui Wan', 'Liu Wei Di Huang Wan'],
      shared: 'Both nourish Kidney-Liver Yin and may treat dizziness, tinnitus, weak back and knees, night sweating and reproductive weakness.',
      fast: ['More severe Kidney Yin and Essence deficiency requiring rich, direct supplementation.', 'Common Liver-Kidney Yin deficiency needing a balanced three-tonify/three-drain strategy.'],
      rows: [
        ['Relative strength', 'Richer and more strongly replenishes Essence.', 'Gentler, more balanced and suitable for broader chronic deficiency.'],
        ['Formula architecture', 'Primarily tonifying; minimizes draining.', 'Three tonics balanced by three draining herbs.'],
        ['Decisive clue', 'Marked Essence depletion, developmental/reproductive weakness.', 'Typical Yin deficiency with possible deficiency Heat and fluid imbalance.'],
        ['Exam shortcut', 'Purely rebuild the Left/Kidney Yin.', 'Balanced six-herb Yin support.']
      ],
      why: 'Both nourish Yin, but Zuo Gui Wan is deliberately richer and less draining; Liu Wei balances supplementation with regulation.',
      memory: '<strong>Zuo Gui fills deeply;</strong> <strong>Liu Wei fills and drains.</strong>',
      safety: 'Rich Yin tonics may aggravate Dampness or weak digestion and require individualized professional use.',
      prompts: ['Which more strongly replenishes Essence?', 'Which uses three tonics and three drainers?', 'Which is richer and more cloying?'],
      source: 'Source bank: FWB 8.2 series, Comparison Formulas to Tonify; corroborated with individual formula profiles and Yin-tonic materials.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['Zuo Gui Wan', 'Liu Wei Di Huang Wan'],
      shared: 'Ambas nutren Yin de Riñón-Hígado y tratan mareo, tinnitus, lumbar/rodillas débiles, sudor nocturno y debilidad reproductiva.',
      fast: ['Deficiencia más grave de Yin y Esencia renal que requiere suplementación rica y directa.', 'Deficiencia común de Yin Hígado-Riñón que requiere equilibrio tres tonifican/tres drenan.'],
      rows: [
        ['Fuerza', 'Más rica y repone Esencia con mayor fuerza.', 'Más suave, equilibrada y amplia para deficiencia crónica.'],
        ['Arquitectura', 'Principalmente tonificante; minimiza drenaje.', 'Tres tónicos equilibrados por tres drenantes.'],
        ['Pista decisiva', 'Agotamiento marcado de Esencia y debilidad reproductiva/desarrollo.', 'Deficiencia típica de Yin con posible Calor vacío y líquidos alterados.'],
        ['Atajo', 'Reconstruir profundamente Izquierda/Yin renal.', 'Apoyo Yin equilibrado de seis hierbas.']
      ],
      why: 'Zuo Gui es más rica y menos drenante; Liu Wei equilibra suplementación con regulación.',
      memory: '<strong>Zuo Gui llena profundamente;</strong> <strong>Liu Wei llena y drena.</strong>',
      safety: 'Tónicos Yin ricos pueden agravar Humedad o digestión débil y requieren individualización profesional.',
      prompts: ['¿Cuál repone Esencia con más fuerza?', '¿Cuál usa tres tónicos y tres drenantes?', '¿Cuál es más empalagosa?'],
      source: 'Banco fuente: serie FWB 8.2, Comparison Formulas to Tonify; corroborado con perfiles y materiales tónicos Yin.'
    }
  },
  {
    slug: 'you-gui-wan-vs-jin-gui-shen-qi-wan', title: 'You Gui Wan vs. Jin Gui Shen Qi Wan',
    category: 'Formulas that Tonify Yang',
    summary: 'Separate strong Kidney-Yang and Essence replenishment from balanced restoration of Kidney Qi transformation and water metabolism.',
    search: 'you gui wan jin gui shen qi wan restore right kidney yang essence qi transformation water metabolism cold edema urination',
    en: {
      eyebrow: 'Formula comparison', names: ['You Gui Wan', 'Jin Gui Shen Qi Wan'],
      shared: 'Both warm Kidney Yang and treat cold limbs, weak back and knees, urinary or reproductive weakness and a deep weak pulse.',
      fast: ['More severe Kidney-Yang and Essence deficiency needing strong, rich warming supplementation.', 'Kidney Yang/Qi deficiency with impaired water transformation, edema or urinary dysfunction; warming is balanced with regulation.'],
      rows: [
        ['Relative strength', 'Stronger, richer Yang-Essence tonic.', 'More moderate and focused on restoring Qi transformation.'],
        ['Therapeutic architecture', 'Directly supplements Yang, Blood and Essence.', 'Warms Kidney within a balanced base that also regulates fluids.'],
        ['Decisive clue', 'Pronounced reproductive or Essence weakness with severe Cold.', 'Edema, urinary difficulty/frequency or fluid-metabolism signs.'],
        ['Exam shortcut', 'Rebuild the Right/Mingmen strongly.', 'Restart Kidney water transformation.']
      ],
      why: 'Both warm Kidney Yang, but You Gui Wan is a richer replacement strategy; Jin Gui Shen Qi Wan is a balanced functional-restoration strategy.',
      memory: '<strong>You Gui refills the fire;</strong> <strong>Jin Gui restarts the water gate.</strong>',
      safety: 'Both require professional differentiation. Products containing Fu Zi must use properly processed material; edema or urinary symptoms also require biomedical assessment.',
      prompts: ['Which is stronger for Yang-Essence depletion?', 'Which emphasizes water transformation?', 'Which signs favor Jin Gui Shen Qi Wan?'],
      source: 'Source bank: FWB 8.2 series, Comparison Formulas to Tonify; corroborated with individual formula profiles and Yang-tonic materials.'
    },
    es: {
      eyebrow: 'Comparación de fórmulas', names: ['You Gui Wan', 'Jin Gui Shen Qi Wan'],
      shared: 'Ambas calientan Yang de Riñón y tratan extremidades frías, lumbar/rodillas débiles, debilidad urinaria/reproductiva y pulso profundo débil.',
      fast: ['Deficiencia más grave de Yang y Esencia renal necesita suplementación caliente, rica y fuerte.', 'Deficiencia de Yang/Qi renal con transformación de agua alterada, edema o disfunción urinaria; calentamiento equilibrado con regulación.'],
      rows: [
        ['Fuerza', 'Tónico Yang-Esencia más fuerte y rico.', 'Más moderada y centrada en restaurar transformación de Qi.'],
        ['Arquitectura', 'Suplementa directamente Yang, Sangre y Esencia.', 'Calienta Riñón dentro de base equilibrada que regula líquidos.'],
        ['Pista decisiva', 'Debilidad reproductiva/Esencia marcada con Frío grave.', 'Edema, dificultad/frecuencia urinaria o signos de líquidos.'],
        ['Atajo', 'Reconstruir Derecha/Mingmen con fuerza.', 'Reiniciar puerta de agua renal.']
      ],
      why: 'You Gui es reposición más rica; Jin Gui Shen Qi es restauración funcional equilibrada.',
      memory: '<strong>You Gui rellena el fuego;</strong> <strong>Jin Gui reinicia la puerta de agua.</strong>',
      safety: 'Requieren diferenciación profesional. Fórmulas con Fu Zi usan material procesado correctamente; edema/síntomas urinarios también requieren evaluación biomédica.',
      prompts: ['¿Cuál es más fuerte para agotamiento Yang-Esencia?', '¿Cuál enfatiza transformación de agua?', '¿Qué signos favorecen Jin Gui?'],
      source: 'Banco fuente: serie FWB 8.2, Comparison Formulas to Tonify; corroborado con perfiles y materiales tónicos Yang.'
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
<body><header class="comparison-topbar"><div class="comparison-shell comparison-topbar__inner"><a class="comparison-brand" href="index.html"><span class="comparison-brand__mark" aria-hidden="true">☯</span><span>AcupunctureTests</span></a><span style="display:flex;flex-wrap:wrap;gap:8px"><a class="comparison-back" href="formulas${spanish ? '' : '-es'}.html">${spanish ? 'English' : 'Español'}</a><a class="comparison-back" href="index.html">${spanish ? '← Comparaciones de Hierbas' : '← Single Herbs Comparisons'}</a></span></div></header>
<main class="comparison-shell"><section class="comparison-hero"><p class="comparison-eyebrow">${spanish ? 'Grupo 2 · Fórmulas' : 'Group 2 · Formulas'}</p><h1>${spanish ? 'Comparaciones de Fórmulas' : 'Formula Comparisons'}</h1><p>${spanish ? 'Compara fórmulas de medicina china por estrategia terapéutica, patrón, acciones, ingredientes y las pistas decisivas que las distinguen en el examen.' : 'Compare Chinese medicine formulas by treatment strategy, pattern, actions, ingredients and the decisive clues that distinguish them on exams.'}</p></section>
<nav class="comparison-collection-links" aria-label="${spanish ? 'Grupos de estudio' : 'Study groups'}"><a href="index.html">${spanish ? 'Comparaciones de Hierbas' : 'Single Herbs Comparisons'}</a><a aria-current="page" href="formulas${spanish ? '-es' : ''}.html">${spanish ? 'Comparaciones de Fórmulas' : 'Formula Comparisons'}</a><a href="pathologies.html">${spanish ? 'Comparaciones de Patologías' : 'Pathology Comparisons'}</a><a href="patterns${spanish ? '-es' : ''}.html">${spanish ? 'Comparaciones de Patrones' : 'Pattern Comparisons'}</a></nav>
<section class="comparison-tools" aria-label="${spanish ? 'Buscar comparaciones de fórmulas' : 'Search Formula Comparisons'}"><label><span class="sr-only">${spanish ? 'Buscar' : 'Search'}</span><input class="comparison-search" type="search" placeholder="${spanish ? 'Buscar fórmulas o categorías' : 'Search formulas or categories'}" data-comparison-search></label></section>
<section><header class="comparison-summary"><h2>${spanish ? 'Notas disponibles' : 'Available notes'}</h2><p><span data-visible-count>${cards.length}</span> ${spanish ? 'comparaciones de fórmulas' : 'formula comparisons'}</p></header><div class="comparison-grid" data-comparison-grid>
        ${cardsHtml}
      </div><p class="comparison-empty" data-comparison-empty hidden>${spanish ? 'No hay notas que coincidan con esta búsqueda.' : 'No Formula Comparisons match this search.'}</p></section></main>
<footer class="comparison-footer"><div class="comparison-shell">${spanish ? 'Material independiente de estudio. Los Exam Keys y el feedback de los Mock permanecen en Exam Key.' : 'Independent study material. Exam keys and Mock feedback remain in Exam Key.'}</div></footer></body></html>`;
}

for (const card of cards) {
  writeFileSync(resolve(root, `${card.slug}.html`), cardHtml(card, 'en'));
  writeFileSync(resolve(root, `${card.slug}-es.html`), cardHtml(card, 'es'));
}
writeFileSync(resolve(root, 'formulas.html'), indexHtml('en'));
writeFileSync(resolve(root, 'formulas-es.html'), indexHtml('es'));

console.log(`Generated ${cards.length * 2} formula cards and 2 formula indexes in ${root}`);
