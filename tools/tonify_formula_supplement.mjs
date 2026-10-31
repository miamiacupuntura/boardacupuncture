const chapterSafety = {
  qi: {
    safetyTier: 'standard',
    safety: 'Educational pattern-differentiation material only. Persistent fatigue, shortness of breath, weight loss, recurrent infection, edema, bleeding, or cardiopulmonary symptoms require medical assessment before tonic treatment.',
    safetyEs: 'Material educativo de diferenciación de patrones. Fatiga persistente, disnea, pérdida de peso, infecciones recurrentes, edema, sangrado o síntomas cardiopulmonares requieren evaluación médica antes de un tratamiento tónico.'
  },
  blood: {
    safetyTier: 'high',
    safety: 'Blood-tonifying formulas are not substitutes for investigating anemia or active blood loss. Pregnancy, heavy uterine bleeding, fainting, severe pallor, black stool, or vomiting blood requires prompt medical care.',
    safetyEs: 'Las fórmulas que tonifican Sangre no sustituyen la investigación de anemia o pérdida sanguínea activa. Embarazo, sangrado uterino abundante, desmayo, palidez grave, heces negras o vómito de sangre requieren atención médica pronta.'
  },
  qiblood: {
    safetyTier: 'high',
    safety: 'Use only after the cause of combined Qi and Blood deficiency has been assessed. Unexplained emaciation, irregular pulse, bleeding, pregnancy complications, persistent fever, or suspected infection requires conventional evaluation.',
    safetyEs: 'Usar solo después de evaluar la causa de la deficiencia combinada de Qi y Sangre. Emaciación inexplicada, pulso irregular, sangrado, complicaciones del embarazo, fiebre persistente o sospecha de infección requieren evaluación convencional.'
  },
  yin: {
    safetyTier: 'high',
    safety: 'Rich Yin tonics can aggravate Dampness, poor digestion, or loose stools and do not replace evaluation of diabetes, tuberculosis, bleeding, endocrine disease, visual loss, or chronic cough.',
    safetyEs: 'Los tónicos ricos de Yin pueden agravar Humedad, mala digestión o heces sueltas y no sustituyen la evaluación de diabetes, tuberculosis, sangrado, enfermedad endocrina, pérdida visual o tos crónica.'
  },
  yang: {
    safetyTier: 'critical',
    safety: 'These warming formulas require expert prescribing. Several contain processed Aconiti Radix lateralis (Fu Zi), which can cause fatal arrhythmia or neurologic toxicity if the species, processing, dose, or decoction is wrong. Never self-prepare or self-dose.',
    safetyEs: 'Estas fórmulas calientes requieren prescripción experta. Varias contienen Aconiti Radix lateralis procesada (Fu Zi), capaz de causar arritmia mortal o toxicidad neurológica si la especie, el procesamiento, la dosis o la cocción son incorrectos. Nunca preparar ni dosificar por cuenta propia.'
  },
  yinyang: {
    safetyTier: 'high',
    safety: 'Combined Yin-Yang tonification is a professional strategy, not a general vitality supplement. Acute weakness, aphasia, paralysis, severe hypertension, chest pain, or neurologic change requires emergency or urgent medical assessment.',
    safetyEs: 'La tonificación combinada de Yin-Yang es una estrategia profesional, no un suplemento general de vitalidad. Debilidad aguda, afasia, parálisis, hipertensión grave, dolor torácico o cambio neurológico requieren evaluación médica urgente o de emergencia.'
  }
};

const category = (number, key, name, nameEs, sourcePages, safetyKey) => ({
  number, key, name, nameEs, sourceChapter: 8, sourcePages, safetyKey
});

export const tonifyChapterCategories = [
  category('S4.1', 'tonify-qi-ch8', 'Tonify the Qi · Chapter 8', 'Tonificar el Qi · Capítulo 8', '309–332', 'qi'),
  category('S4.2', 'tonify-blood-ch8', 'Tonify the Blood · Chapter 8', 'Tonificar la Sangre · Capítulo 8', '333–346', 'blood'),
  category('S4.3', 'tonify-qi-blood-ch8', 'Tonify the Qi and Blood · Chapter 8', 'Tonificar el Qi y la Sangre · Capítulo 8', '346–364', 'qiblood'),
  category('S4.4', 'nourish-tonify-yin-ch8', 'Nourish and Tonify the Yin · Chapter 8', 'Nutrir y tonificar el Yin · Capítulo 8', '365–394', 'yin'),
  category('S4.5', 'warm-tonify-yang-ch8', 'Warm and Tonify the Yang · Chapter 8', 'Calentar y tonificar el Yang · Capítulo 8', '395–404', 'yang'),
  category('S4.6', 'tonify-yin-yang-ch8', 'Tonify Both Yin and Yang · Chapter 8', 'Tonificar conjuntamente Yin y Yang · Capítulo 8', '405–411', 'yinyang')
];

const categoryByKey = new Map(tonifyChapterCategories.map(item => [item.key, item]));

const makeFormula = (categoryKey, prefix, sequence, data) => {
  const selected = categoryByKey.get(categoryKey);
  const safety = chapterSafety[selected.safetyKey];
  return {
    number: `${prefix}${sequence}`,
    sequence,
    categoryKey,
    group: selected.name.replace(' · Chapter 8', ''),
    groupEs: selected.nameEs.replace(' · Capítulo 8', ''),
    ...safety,
    ...data,
    examFocus: data.examFocus || data.clue,
    examFocusEs: data.examFocusEs || data.clueEs
  };
};

const qiFormulas = [
  {
    name: 'Si Jun Zi Tang', englishName: 'Four-Gentlemen Decoction', sourcePages: '309–312', kings: 'Ren Shen',
    pattern: 'Spleen Qi deficiency.', patternEs: 'Deficiencia de Qi de Bazo.',
    action: 'Tonifies Qi and strengthens the Spleen.', actionEs: 'Tonifica Qi y fortalece el Bazo.',
    clue: 'Pale complexion, low soft voice, reduced appetite, loose stools, weak limbs, pale tongue, and a deficient or frail pulse.', clueEs: 'Tez pálida, voz baja y suave, poco apetito, heces sueltas, extremidades débiles, lengua pálida y pulso deficiente o débil.',
    ingredients: 'Ren Shen, Bai Zhu, Fu Ling, and Zhi Gan Cao.', ingredientsEs: 'Ren Shen, Bai Zhu, Fu Ling y Zhi Gan Cao.',
    examFocus: 'The balanced four-herb foundation for uncomplicated Spleen Qi deficiency.', examFocusEs: 'La base equilibrada de cuatro hierbas para deficiencia no complicada de Qi de Bazo.',
    associated: [['Liu Jun Zi Tang / Xiang Sha Liu Jun Zi Tang', 'Add Chen Pi and Ban Xia for Phlegm; add Mu Xiang and Sha Ren when Qi stagnation and digestive Dampness are stronger.', 'Añaden Chen Pi y Ban Xia para Flema; añaden Mu Xiang y Sha Ren cuando son mayores el estancamiento de Qi y la Humedad digestiva.']]
  },
  {
    name: 'Shen Ling Bai Zhu San', englishName: 'Ginseng, Poria, and White Atractylodes Powder', sourcePages: '312–315', kings: 'Ren Shen, Bai Zhu, Fu Ling',
    pattern: 'Spleen Qi deficiency with internally generated Dampness.', patternEs: 'Deficiencia de Qi de Bazo con Humedad generada internamente.',
    action: 'Augments Qi, strengthens the Spleen, leaches Dampness, and stops diarrhea.', actionEs: 'Fortalece Qi y Bazo, filtra Humedad y detiene diarrea.',
    clue: 'Chronic loose stools or diarrhea, weak extremities, chest-epigastric fullness, pallid complexion, pale tongue with white coat, and a thin or deficient pulse.', clueEs: 'Heces sueltas o diarrea crónica, extremidades débiles, plenitud toracoepigástrica, tez pálida, lengua pálida con saburra blanca y pulso fino o deficiente.',
    ingredients: 'Ren Shen, Bai Zhu, Fu Ling, Zhi Gan Cao, Shan Yao, Bai Bian Dou, Lian Zi, Yi Yi Ren, Sha Ren, and Jie Geng.', ingredientsEs: 'Ren Shen, Bai Zhu, Fu Ling, Zhi Gan Cao, Shan Yao, Bai Bian Dou, Lian Zi, Yi Yi Ren, Sha Ren y Jie Geng.',
    examFocus: 'Si Jun Zi Tang signs plus chronic diarrhea, Dampness, and poor assimilation.', examFocusEs: 'Signos de Si Jun Zi Tang más diarrea crónica, Humedad y mala asimilación.'
  },
  {
    name: 'Bu Zhong Yi Qi Tang', englishName: 'Tonify the Middle to Augment the Qi Decoction', sourcePages: '315–320', kings: 'Huang Qi',
    pattern: 'Spleen and Stomach Qi deficiency with sinking of clear Yang.', patternEs: 'Deficiencia de Qi de Bazo y Estómago con hundimiento del Yang claro.',
    action: 'Tonifies Middle-Burner Qi, raises clear Yang, and lifts prolapse.', actionEs: 'Tonifica Qi del Jiao Medio, eleva Yang claro y levanta prolapsos.',
    clue: 'Fatigue, shortness of breath, spontaneous sweating, intermittent fever worse with exertion, loose stools, prolapse, pale possibly swollen tongue, and a large but forceless pulse.', clueEs: 'Fatiga, disnea, sudor espontáneo, fiebre intermitente peor con esfuerzo, heces sueltas, prolapsos, lengua pálida posiblemente hinchada y pulso grande pero sin fuerza.',
    ingredients: 'Huang Qi, Ren Shen, Bai Zhu, Zhi Gan Cao, Dang Gui, Chen Pi, Sheng Ma, and Chai Hu.', ingredientsEs: 'Huang Qi, Ren Shen, Bai Zhu, Zhi Gan Cao, Dang Gui, Chen Pi, Sheng Ma y Chai Hu.',
    examFocus: 'The decisive mechanism is sinking: prolapse, chronic diarrhea, or bleeding from failure of Middle Qi to hold.', examFocusEs: 'El mecanismo decisivo es el hundimiento: prolapso, diarrea crónica o sangrado por incapacidad del Qi Medio para contener.'
  },
  {
    name: 'Sheng Yang Yi Wei Tang', englishName: 'Raise the Yang and Augment the Stomach Decoction', sourcePages: '320–322', kings: 'Huang Qi, Ren Shen, Bai Zhu',
    pattern: 'Spleen-Stomach Qi deficiency with Damp-Heat in the lower burner.', patternEs: 'Deficiencia de Qi de Bazo-Estómago con Humedad-Calor en el Jiao Inferior.',
    action: 'Raises Yang, strengthens the Middle, eliminates Dampness, and clears Heat.', actionEs: 'Eleva Yang, fortalece el Centro, elimina Humedad y aclara Calor.',
    clue: 'Loss of taste, abdominal fullness, irregular stools, fatigue and heaviness with joint pain, chills, dark reduced urine, and dry mouth or tongue.', clueEs: 'Pérdida del gusto, plenitud abdominal, evacuaciones irregulares, fatiga y pesadez con dolor articular, escalofríos, orina oscura y reducida y boca o lengua secas.',
    ingredients: 'Huang Qi, Ren Shen, Bai Zhu, Zhi Gan Cao, Ban Xia, Chen Pi, Fu Ling, Ze Xie, Qiang Huo, Du Huo, Fang Feng, Chai Hu, Bai Shao, and Huang Lian.', ingredientsEs: 'Huang Qi, Ren Shen, Bai Zhu, Zhi Gan Cao, Ban Xia, Chen Pi, Fu Ling, Ze Xie, Qiang Huo, Du Huo, Fang Feng, Chai Hu, Bai Shao y Huang Lian.',
    examFocus: 'Qi deficiency is primary, but the formula simultaneously raises clear Yang and clears Damp-Heat below.', examFocusEs: 'La deficiencia de Qi es primaria, pero la fórmula eleva simultáneamente Yang claro y aclara Humedad-Calor abajo.'
  },
  {
    name: 'Yu Ping Feng San', englishName: 'Jade Windscreen Powder', sourcePages: '322–324', kings: 'Huang Qi',
    pattern: 'Protective Qi deficiency with unstable exterior.', patternEs: 'Deficiencia de Qi defensivo con Exterior inestable.',
    action: 'Augments Qi, stabilizes the exterior, and stops spontaneous sweating.', actionEs: 'Fortalece Qi, estabiliza el Exterior y detiene sudor espontáneo.',
    clue: 'Aversion to drafts, spontaneous sweating, recurrent colds, shiny pale complexion, pale tongue with white coat, and a floating deficient soft pulse.', clueEs: 'Aversión a corrientes, sudor espontáneo, resfriados recurrentes, tez pálida brillante, lengua pálida con saburra blanca y pulso flotante, deficiente y blando.',
    ingredients: 'Huang Qi, Bai Zhu, and Fang Feng.', ingredientsEs: 'Huang Qi, Bai Zhu y Fang Feng.',
    examFocus: 'A tonic “screen” for an already weak exterior—not an acute exterior-releasing prescription.', examFocusEs: 'Una “pantalla” tónica para un Exterior ya débil, no una prescripción para liberar un cuadro exterior agudo.'
  },
  {
    name: 'Sheng Mai San', englishName: 'Generate the Pulse Powder', sourcePages: '324–327', kings: 'Ren Shen',
    pattern: 'Simultaneous Lung Qi and Yin deficiency.', patternEs: 'Deficiencia simultánea de Qi y Yin de Pulmón.',
    action: 'Augments Qi, generates fluids, nourishes Yin, and restrains leakage.', actionEs: 'Fortalece Qi, genera líquidos, nutre Yin y contiene pérdidas.',
    clue: 'Chronic weak cough, shortness of breath, spontaneous sweating, dry mouth, pale-red tongue with dry thin coat, and a deficient rapid or thin pulse.', clueEs: 'Tos crónica débil, disnea, sudor espontáneo, boca seca, lengua rojo-pálida con saburra fina y seca y pulso deficiente-rápido o fino.',
    ingredients: 'Ren Shen, Mai Men Dong, and Wu Wei Zi.', ingredientsEs: 'Ren Shen, Mai Men Dong y Wu Wei Zi.',
    examFocus: 'Three directions in three herbs: supplement Qi, restore fluids, and astringe what is leaking.', examFocusEs: 'Tres direcciones en tres hierbas: suplementar Qi, restaurar líquidos y contener lo que se pierde.'
  },
  {
    name: 'Bu Fei Tang', englishName: 'Tonify the Lungs Decoction', sourcePages: '327–329', kings: 'Ren Shen, Huang Qi',
    pattern: 'Lung Qi deficiency.', patternEs: 'Deficiencia de Qi de Pulmón.',
    action: 'Tonifies Lung Qi, secures the exterior, and controls cough.', actionEs: 'Tonifica Qi de Pulmón, asegura el Exterior y controla tos.',
    clue: 'Shortness of breath, spontaneous sweating, weak chronic cough or wheezing, occasional chills and feverishness, pale tongue, and a frail or large deficient pulse.', clueEs: 'Disnea, sudor espontáneo, tos crónica débil o sibilancia, escalofríos y febrícula ocasionales, lengua pálida y pulso débil o grande-deficiente.',
    ingredients: 'Ren Shen, Huang Qi, Shu Di Huang, Wu Wei Zi, Zi Wan, and Sang Bai Pi.', ingredientsEs: 'Ren Shen, Huang Qi, Shu Di Huang, Wu Wei Zi, Zi Wan y Sang Bai Pi.',
    examFocus: 'A direct Lung-Qi tonic for chronic cough, unlike Sheng Mai San where dryness and fluid loss are central.', examFocusEs: 'Tónico directo de Qi de Pulmón para tos crónica, a diferencia de Sheng Mai San, donde son centrales sequedad y pérdida de líquidos.'
  },
  {
    name: 'Ren Shen Ge Jie San', englishName: 'Ginseng and Gecko Powder', sourcePages: '329–332', kings: 'Ge Jie, Ren Shen',
    pattern: 'Lung and Kidney Qi deficiency with Phlegm-Heat collecting internally.', patternEs: 'Deficiencia de Qi de Pulmón y Riñón con Flema-Calor acumulada internamente.',
    action: 'Tonifies Lung-Kidney Qi, assists grasping of Qi, clears Heat, and transforms Phlegm.', actionEs: 'Tonifica Qi de Pulmón-Riñón, ayuda a captar Qi, aclara Calor y transforma Flema.',
    clue: 'Chronic weak cough and wheezing with difficult inhalation, thick yellow or purulent sputum, chest irritability, facial edema, emaciation, and a floating deficient distal pulse.', clueEs: 'Tos crónica débil y sibilancia con inhalación difícil, esputo amarillo espeso o purulento, irritabilidad torácica, edema facial, emaciación y pulso distal flotante-deficiente.',
    ingredients: 'Ge Jie, Ren Shen, Fu Ling, Bei Mu, Sang Bai Pi, Xing Ren, Zhi Mu, and Gan Cao.', ingredientsEs: 'Ge Jie, Ren Shen, Fu Ling, Bei Mu, Sang Bai Pi, Xing Ren, Zhi Mu y Gan Cao.',
    safetyTier: 'high', safety: 'Not for acute excess wheezing. Severe breathlessness, cyanosis, coughing pus or blood, fever, or inability to speak full sentences requires urgent care. Gecko is animal-derived and may be restricted.', safetyEs: 'No corresponde a sibilancia aguda por exceso. Disnea grave, cianosis, tos con pus o sangre, fiebre o incapacidad para hablar frases completas requieren atención urgente. Ge Jie es de origen animal y puede estar restringido.',
    examFocus: 'Deficient cough plus failure of the Kidneys to grasp Qi: inhalation is harder than exhalation.', examFocusEs: 'Tos deficiente más incapacidad de Riñón para captar Qi: inhalar resulta más difícil que exhalar.'
  }
].map((item, index) => makeFormula('tonify-qi-ch8', 'TQ', index + 1, item));

const bloodFormulas = [
  {
    name: 'Si Wu Tang', englishName: 'Four-Substance Decoction', sourcePages: '333–336', kings: 'Shu Di Huang',
    pattern: 'Generalized Blood deficiency with some Blood stagnation.', patternEs: 'Deficiencia generalizada de Sangre con cierto estancamiento sanguíneo.',
    action: 'Tonifies and regulates Blood.', actionEs: 'Tonifica y regula Sangre.',
    clue: 'Dizziness, pale dull complexion and nails, muscle tension, insomnia, scant irregular menses, lower-abdominal pain, pale tongue, and a thin wiry or choppy pulse.', clueEs: 'Mareo, tez y uñas pálidas y opacas, tensión muscular, insomnio, menstruación escasa e irregular, dolor abdominal inferior, lengua pálida y pulso fino-de cuerda o áspero.',
    ingredients: 'Shu Di Huang, Bai Shao, Dang Gui, and Chuan Xiong.', ingredientsEs: 'Shu Di Huang, Bai Shao, Dang Gui y Chuan Xiong.',
    examFocus: 'Four-herb Blood foundation: nourish without allowing the rich tonics to stagnate.', examFocusEs: 'Base de cuatro hierbas para Sangre: nutrir sin permitir que los tónicos ricos se estanquen.'
  },
  {
    name: 'Dang Gui Bu Xue Tang', englishName: 'Tang-kuei Decoction to Tonify the Blood', sourcePages: '336–338', kings: 'Huang Qi',
    pattern: 'Deficiency Heat from Blood deficiency with floating Yang.', patternEs: 'Calor por deficiencia de Sangre con Yang flotante.',
    action: 'Strongly augments Qi so that Blood can be generated.', actionEs: 'Fortalece intensamente Qi para que pueda generarse Sangre.',
    clue: 'Hot sensation in the muscles, red face, irritability, thirst for warm drinks, pale tongue, and a floating large deficient pulse that collapses under pressure.', clueEs: 'Sensación de calor muscular, cara roja, irritabilidad, sed de bebidas calientes, lengua pálida y pulso flotante, grande y deficiente que colapsa con presión.',
    ingredients: 'Huang Qi and Dang Gui in a classical 5:1 ratio.', ingredientsEs: 'Huang Qi y Dang Gui en proporción clásica 5:1.',
    examFocus: 'Apparent Heat after Blood loss with an empty pulse; abundant Qi is used to generate Blood.', examFocusEs: 'Calor aparente tras pérdida de Sangre con pulso vacío; se usa Qi abundante para generar Sangre.'
  },
  {
    name: 'Guo Qi Yin', englishName: 'Delayed Menstruation Drink', sourcePages: '338–340', kings: 'Dang Gui, Shu Di Huang',
    pattern: 'Blood deficiency and stasis in a patient who tends to Cold.', patternEs: 'Deficiencia y estasis de Sangre en una paciente con tendencia al Frío.',
    action: 'Nourishes and moves Blood, regulates menstruation, and warms Cold.', actionEs: 'Nutre y moviliza Sangre, regula menstruación y calienta Frío.',
    clue: 'Delayed scant menses, often with clots, lower-abdominal pain and distention before or during menstruation, and a submerged strong pulse.', clueEs: 'Menstruación retrasada y escasa, a menudo con coágulos, dolor y distensión abdominal inferior antes o durante la regla y pulso sumergido-fuerte.',
    ingredients: 'A warming, moving adaptation of the Four-Substance strategy centered on Dang Gui and Shu Di Huang.', ingredientsEs: 'Adaptación caliente y movilizante de la estrategia de las Cuatro Sustancias, centrada en Dang Gui y Shu Di Huang.',
    examFocus: 'Delayed menstruation with combined deficiency, clots, pain, and a Cold tendency.', examFocusEs: 'Menstruación retrasada con deficiencia combinada, coágulos, dolor y tendencia al Frío.'
  },
  {
    name: 'Nu Ke Bai Zi Ren Wan', englishName: 'Arborvitae Seed Pill for Women’s Disorders', sourcePages: '340–342', kings: 'Bai Zi Ren',
    pattern: 'Gradual amenorrhea from Yin and Blood deficiency with stasis.', patternEs: 'Amenorrea gradual por deficiencia de Yin y Sangre con estasis.',
    action: 'Nourishes Blood and Yin, calms the Spirit, and regulates menstruation.', actionEs: 'Nutre Sangre y Yin, calma Shen y regula menstruación.',
    clue: 'Gradual cessation of menses with disquiet, afternoon tidal fever, emaciation, and aching uncomfortable hands and feet.', clueEs: 'Cese gradual de la menstruación con inquietud, fiebre vespertina, emaciación y dolor o malestar en manos y pies.',
    ingredients: 'Bai Zi Ren with Blood- and Yin-nourishing, menstruation-regulating medicinals as specified in the source.', ingredientsEs: 'Bai Zi Ren con sustancias que nutren Sangre y Yin y regulan menstruación, según la fuente.',
    examFocus: 'A slow deficiency amenorrhea picture, not abrupt amenorrhea from pure excess stasis.', examFocusEs: 'Cuadro lento de amenorrea por deficiencia, no amenorrea súbita por estasis pura de exceso.'
  },
  {
    name: 'Shao Yao Gan Cao Tang', englishName: 'Peony and Licorice Decoction', sourcePages: '342–344', kings: 'Bai Shao, Zhi Gan Cao',
    pattern: 'Blood deficiency or injury to fluids with painful muscular tension.', patternEs: 'Deficiencia de Sangre o lesión de líquidos con tensión muscular dolorosa.',
    action: 'Nourishes Blood and Yin, relaxes spasm, and stops pain.', actionEs: 'Nutre Sangre y Yin, relaja espasmos y detiene dolor.',
    clue: 'Irritability, mild chills, calf or abdominal spasms, cramps, and little or no tongue coating.', clueEs: 'Irritabilidad, escalofríos leves, espasmos de pantorrilla o abdomen, calambres y poca o ninguna saburra lingual.',
    ingredients: 'Bai Shao and Zhi Gan Cao.', ingredientsEs: 'Bai Shao y Zhi Gan Cao.',
    examFocus: 'The sour-sweet pair restores nutritive substance and releases painful contraction.', examFocusEs: 'La pareja ácido-dulce restaura sustancia nutritiva y libera contracción dolorosa.'
  },
  {
    name: 'Dang Gui Sheng Jiang Yang Rou Tang', englishName: 'Mutton Stew with Tang-kuei and Fresh Ginger', sourcePages: '344–346', kings: 'Yang Rou, Dang Gui',
    pattern: 'Interior deficiency with Cold in the Blood.', patternEs: 'Deficiencia interior con Frío en la Sangre.',
    action: 'Warms the Middle, nourishes Blood, dispels Cold, and stops pain.', actionEs: 'Calienta el Centro, nutre Sangre, dispersa Frío y detiene dolor.',
    clue: 'Postpartum abdominal pain or cold abdominal/flank pain relieved by pressure and warmth, with a submerged wiry forceless pulse.', clueEs: 'Dolor abdominal posparto o dolor frío abdominal/de flancos aliviado por presión y calor, con pulso sumergido, de cuerda y sin fuerza.',
    ingredients: 'Mutton, Dang Gui, and Sheng Jiang.', ingredientsEs: 'Carne de cordero, Dang Gui y Sheng Jiang.',
    safetyTier: 'high', safety: 'Historical food-medicine formula. Postpartum or acute abdominal pain can signal hemorrhage, infection, retained products, ectopic pregnancy, or another emergency and requires medical assessment.', safetyEs: 'Fórmula histórica alimento-medicina. El dolor abdominal posparto o agudo puede indicar hemorragia, infección, restos retenidos, embarazo ectópico u otra urgencia y requiere evaluación médica.',
    examFocus: 'Deficiency-Cold pain after childbirth that improves with both warmth and pressure.', examFocusEs: 'Dolor posparto por Frío-deficiencia que mejora tanto con calor como con presión.'
  }
].map((item, index) => makeFormula('tonify-blood-ch8', 'TB', index + 1, item));

const qiBloodFormulas = [
  {
    name: 'Ba Zhen Tang', englishName: 'Eight-Treasure Decoction', sourcePages: '346–348', kings: 'Ren Shen, Shu Di Huang',
    pattern: 'Combined Qi and Blood deficiency.', patternEs: 'Deficiencia combinada de Qi y Sangre.',
    action: 'Tonifies Qi and nourishes Blood.', actionEs: 'Tonifica Qi y nutre Sangre.',
    clue: 'Pallid or sallow complexion, palpitations, reduced appetite, fatigue, light-headedness, pale tongue with white coat, and a thin frail or large forceless pulse.', clueEs: 'Tez pálida o cetrina, palpitaciones, poco apetito, fatiga, mareo, lengua pálida con saburra blanca y pulso fino-débil o grande sin fuerza.',
    ingredients: 'Si Jun Zi Tang plus Si Wu Tang: Ren Shen, Bai Zhu, Fu Ling, Zhi Gan Cao, Shu Di Huang, Bai Shao, Dang Gui, and Chuan Xiong.', ingredientsEs: 'Si Jun Zi Tang más Si Wu Tang: Ren Shen, Bai Zhu, Fu Ling, Zhi Gan Cao, Shu Di Huang, Bai Shao, Dang Gui y Chuan Xiong.',
    examFocus: 'The clean combination of the Four Gentlemen and Four Substances.', examFocusEs: 'La combinación directa de los Cuatro Caballeros y las Cuatro Sustancias.'
  },
  {
    name: 'Shi Quan Da Bu Tang', englishName: 'All-Inclusive Great Tonifying Decoction', sourcePages: '348–350', kings: 'Ren Shen, Shu Di Huang, Huang Qi',
    pattern: 'Qi and Blood deficiency with a tendency toward deficiency-Cold.', patternEs: 'Deficiencia de Qi y Sangre con tendencia a Frío por deficiencia.',
    action: 'Strongly tonifies Qi and Blood and gently warms Yang.', actionEs: 'Tonifica intensamente Qi y Sangre y calienta suavemente Yang.',
    clue: 'Wan complexion, fatigue, reduced appetite, palpitations, spontaneous or night sweating, cold extremities, pale tongue, and a thin frail pulse.', clueEs: 'Tez apagada, fatiga, poco apetito, palpitaciones, sudor espontáneo o nocturno, extremidades frías, lengua pálida y pulso fino y débil.',
    ingredients: 'Ba Zhen Tang plus Huang Qi and Rou Gui; Sheng Jiang and Da Zao may be used in decoction.', ingredientsEs: 'Ba Zhen Tang más Huang Qi y Rou Gui; pueden emplearse Sheng Jiang y Da Zao en decocción.',
    examFocus: 'Ba Zhen Tang plus stronger support and a mild Cold component.', examFocusEs: 'Ba Zhen Tang con apoyo más fuerte y un componente leve de Frío.'
  },
  {
    name: 'Ren Shen Yang Rong Tang', englishName: 'Ginseng Decoction to Nourish Luxuriance', sourcePages: '350–352', kings: 'Ren Shen, Huang Qi',
    pattern: 'Heart-Spleen Qi and Blood deficiency with internal Heat and dryness.', patternEs: 'Deficiencia de Qi y Sangre de Corazón-Bazo con Calor interno y sequedad.',
    action: 'Tonifies Qi and Blood, nourishes Heart, and calms the Spirit.', actionEs: 'Tonifica Qi y Sangre, nutre Corazón y calma Shen.',
    clue: 'Palpitations, forgetfulness, nocturnal restlessness, feverishness, poor appetite, fatigue, exertional dyspnea, weight loss, dry skin, and dry mouth or throat.', clueEs: 'Palpitaciones, olvidos, inquietud nocturna, febrícula, poco apetito, fatiga, disnea de esfuerzo, pérdida de peso, piel seca y boca o garganta secas.',
    ingredients: 'Ren Shen, Huang Qi, Bai Zhu, Fu Ling, Zhi Gan Cao, Dang Gui, Bai Shao, Shu Di Huang, Chen Pi, Wu Wei Zi, Yuan Zhi, and Rou Gui.', ingredientsEs: 'Ren Shen, Huang Qi, Bai Zhu, Fu Ling, Zhi Gan Cao, Dang Gui, Bai Shao, Shu Di Huang, Chen Pi, Wu Wei Zi, Yuan Zhi y Rou Gui.',
    examFocus: 'Qi-Blood deficiency with Heart-Spirit and consumptive dryness signs.', examFocusEs: 'Deficiencia de Qi-Sangre con signos de Shen cardíaco y sequedad consuntiva.'
  },
  {
    name: 'Gui Pi Tang', englishName: 'Restore the Spleen Decoction', sourcePages: '352–355', kings: 'Huang Qi, Long Yan Rou',
    pattern: 'Spleen Qi and Heart Blood deficiency.', patternEs: 'Deficiencia de Qi de Bazo y Sangre de Corazón.',
    action: 'Tonifies Qi, nourishes Blood, strengthens the Spleen, calms the Spirit, and controls bleeding.', actionEs: 'Tonifica Qi, nutre Sangre, fortalece Bazo, calma Shen y controla sangrado.',
    clue: 'Forgetfulness, palpitations, insomnia, dream-disturbed sleep, anxiety, poor appetite, early or prolonged menses, pale tongue, and a thin frail pulse.', clueEs: 'Olvidos, palpitaciones, insomnio, sueño con sueños, ansiedad, poco apetito, menstruación adelantada o prolongada, lengua pálida y pulso fino y débil.',
    ingredients: 'Huang Qi, Ren Shen, Bai Zhu, Fu Ling, Zhi Gan Cao, Long Yan Rou, Dang Gui, Suan Zao Ren, Yuan Zhi, Mu Xiang, Sheng Jiang, and Da Zao.', ingredientsEs: 'Huang Qi, Ren Shen, Bai Zhu, Fu Ling, Zhi Gan Cao, Long Yan Rou, Dang Gui, Suan Zao Ren, Yuan Zhi, Mu Xiang, Sheng Jiang y Da Zao.',
    examFocus: 'One mechanism explains two branches: weak Spleen fails to make/hold Blood, causing both Shen symptoms and chronic bleeding.', examFocusEs: 'Un mecanismo explica dos ramas: Bazo débil no produce ni contiene Sangre, causando síntomas de Shen y sangrado crónico.'
  },
  {
    name: 'Zhi Gan Cao Tang', englishName: 'Prepared Licorice Decoction', sourcePages: '355–358', kings: 'Zhi Gan Cao',
    pattern: 'Consumptive Qi-Blood deficiency with irregular pulse and injury to Yin.', patternEs: 'Deficiencia consuntiva de Qi-Sangre con pulso irregular y lesión de Yin.',
    action: 'Augments Qi, nourishes Blood and Yin, restores the pulse, and calms palpitations.', actionEs: 'Fortalece Qi, nutre Sangre y Yin, restaura el pulso y calma palpitaciones.',
    clue: 'Palpitations with anxiety, irritability, insomnia, emaciation, dyspnea, constipation, dry mouth, pale shiny tongue, and a consistently irregular or slow-irregular pulse.', clueEs: 'Palpitaciones con ansiedad, irritabilidad, insomnio, emaciación, disnea, estreñimiento, boca seca, lengua pálida y brillante y pulso constantemente irregular o lento-irregular.',
    ingredients: 'Zhi Gan Cao, Sheng Jiang, Ren Shen, Sheng Di Huang, Gui Zhi, E Jiao, Mai Men Dong, Huo Ma Ren, and Da Zao.', ingredientsEs: 'Zhi Gan Cao, Sheng Jiang, Ren Shen, Sheng Di Huang, Gui Zhi, E Jiao, Mai Men Dong, Huo Ma Ren y Da Zao.',
    safetyTier: 'critical', safety: 'An irregular pulse, chest pain, fainting, severe breathlessness, or new palpitations requires urgent cardiac evaluation. Licorice can cause hypertension, edema, hypokalemia, and drug interactions at significant doses.', safetyEs: 'Pulso irregular, dolor torácico, desmayo, disnea grave o palpitaciones nuevas requieren evaluación cardiaca urgente. El regaliz puede causar hipertensión, edema, hipopotasemia e interacciones farmacológicas en dosis relevantes.',
    examFocus: 'The principal classical formula for a knotted or intermittent pulse with palpitations from deficiency.', examFocusEs: 'Fórmula clásica principal para pulso anudado o intermitente con palpitaciones por deficiencia.'
  },
  {
    name: 'Tai Shan Pan Shi San', englishName: 'Taishan Bedrock Powder', sourcePages: '358–360', kings: 'Ren Shen, Huang Qi',
    pattern: 'Qi and Blood deficiency failing to nourish and secure pregnancy.', patternEs: 'Deficiencia de Qi y Sangre que no nutre ni asegura el embarazo.',
    action: 'Tonifies Qi and Blood and stabilizes pregnancy.', actionEs: 'Tonifica Qi y Sangre y estabiliza el embarazo.',
    clue: 'Restless fetus or threatened recurrent miscarriage with pale complexion, fatigue, poor appetite, pale tongue with slippery coat, and a slippery forceless pulse.', clueEs: 'Feto inquieto o amenaza de aborto recurrente con tez pálida, fatiga, poco apetito, lengua pálida con saburra resbaladiza y pulso resbaladizo sin fuerza.',
    ingredients: 'Qi- and Blood-tonifying base with Duan Xu, Huang Qin, and Sha Ren to secure and regulate pregnancy.', ingredientsEs: 'Base que tonifica Qi y Sangre con Duan Xu, Huang Qin y Sha Ren para asegurar y regular el embarazo.',
    safetyTier: 'critical', safety: 'Historical obstetric formula only. Bleeding, abdominal pain, fluid loss, reduced fetal movement, or suspected miscarriage requires immediate obstetric assessment; do not self-treat pregnancy complications.', safetyEs: 'Fórmula obstétrica histórica. Sangrado, dolor abdominal, pérdida de líquido, menor movimiento fetal o sospecha de aborto requieren evaluación obstétrica inmediata; no automedicar complicaciones del embarazo.',
    examFocus: 'The bedrock image points to deficient Qi and Blood failing to hold the fetus.', examFocusEs: 'La imagen de roca firme señala Qi y Sangre deficientes incapaces de sostener el feto.'
  },
  {
    name: 'Bao Chan Wu You Fang', englishName: 'Worry-Free Formula to Protect Birth', sourcePages: '360–362', kings: 'Dang Gui, Chuan Xiong',
    pattern: 'Qi-Blood deficiency with unsecured Penetrating and Conception vessels.', patternEs: 'Deficiencia de Qi-Sangre con vasos Penetrante y Concepción no asegurados.',
    action: 'Nourishes and moves Blood, regulates Qi, calms the fetus, and historically assists labor.', actionEs: 'Nutre y mueve Sangre, regula Qi, calma al feto e históricamente ayuda al parto.',
    clue: 'Restless fetus, recurrent miscarriage tendency, malposition, or difficult labor in a deficient presentation.', clueEs: 'Feto inquieto, tendencia a aborto recurrente, mala posición o parto difícil en un cuadro deficiente.',
    ingredients: 'A multi-herb obstetric formula centered on Dang Gui and Chuan Xiong with Qi-regulating, fetus-calming, and channel-opening medicinals.', ingredientsEs: 'Fórmula obstétrica de múltiples hierbas centrada en Dang Gui y Chuan Xiong, con sustancias que regulan Qi, calman al feto y abren canales.',
    safetyTier: 'critical', safety: 'Historical obstetric material, not a labor-induction or miscarriage-prevention protocol. Malpresentation, bleeding, contractions, fluid loss, or reduced fetal movement requires immediate maternity care.', safetyEs: 'Material obstétrico histórico, no protocolo de inducción del parto ni prevención del aborto. Mala presentación, sangrado, contracciones, pérdida de líquido o menor movimiento fetal requieren atención obstétrica inmediata.',
    examFocus: 'Compared with Tai Shan Pan Shi San, this formula moves and regulates more and was historically applied closer to delivery.', examFocusEs: 'Comparada con Tai Shan Pan Shi San, moviliza y regula más y se aplicaba históricamente más cerca del parto.'
  },
  {
    name: 'He Ren Yin', englishName: 'Fleeceflower Root and Ginseng Drink', sourcePages: '362–363', kings: 'He Shou Wu, Ren Shen',
    pattern: 'Chronic malarial-type disorder with Qi and Blood deficiency.', patternEs: 'Trastorno crónico tipo malaria con deficiencia de Qi y Sangre.',
    action: 'Tonifies Qi and Blood without trapping the residual pathogen.', actionEs: 'Tonifica Qi y Sangre sin encerrar el patógeno residual.',
    clue: 'Chronic unrelenting alternating fever and chills, wan complexion, emaciation, aggravation by slight exertion, pale tongue, and a large deficient pulse.', clueEs: 'Fiebre y escalofríos alternantes crónicos e incesantes, tez apagada, emaciación, agravamiento con esfuerzo leve, lengua pálida y pulso grande-deficiente.',
    ingredients: 'He Shou Wu and Ren Shen with supporting Qi- and Blood-regulating medicinals.', ingredientsEs: 'He Shou Wu y Ren Shen con sustancias de apoyo que regulan Qi y Sangre.',
    safetyTier: 'high', safety: 'Not for an early or acute malaria-like febrile illness. Fever after travel requires testing; He Shou Wu has documented hepatotoxicity and requires professional product and liver-risk review.', safetyEs: 'No corresponde a enfermedad febril temprana o aguda tipo malaria. Fiebre tras viaje requiere pruebas; He Shou Wu tiene hepatotoxicidad documentada y exige revisión profesional del producto y riesgo hepático.',
    examFocus: 'A late, deficient, chronic intermittent-fever pattern—not an early pathogen-expelling stage.', examFocusEs: 'Patrón tardío, deficiente y crónico de fiebre intermitente, no etapa temprana para expulsar patógeno.'
  },
  {
    name: 'Di Huang Yin Zi (A Simple Book)', englishName: 'Rehmannia Drink from A Simple Book', sourcePages: '363–364', kings: 'Sheng Di Huang',
    pattern: 'Wasting-thirst disorder from Yangming Heat with depleted Qi, Blood, and Yin.', patternEs: 'Trastorno de consunción-sed por Calor Yangming con Qi, Sangre y Yin agotados.',
    action: 'Tonifies Qi, Blood, and Yin while guiding out residual Heat.', actionEs: 'Tonifica Qi, Sangre y Yin mientras expulsa Calor residual.',
    clue: 'Thirst, irritability, restlessness, dry throat, flushed face, fatigue, and spontaneous sweating.', clueEs: 'Sed, irritabilidad, inquietud, garganta seca, cara enrojecida, fatiga y sudor espontáneo.',
    ingredients: 'Sheng Di Huang with Qi- and Blood-tonifying medicinals as recorded in the source.', ingredientsEs: 'Sheng Di Huang con sustancias que tonifican Qi y Sangre según la fuente.',
    safetyTier: 'high', safety: 'Wasting-thirst overlaps historically with diabetes but is not equivalent to it. Polyuria, weight loss, marked thirst, vomiting, confusion, or abnormal glucose requires medical testing and evidence-based diabetes care.', safetyEs: 'Consunción-sed se solapa históricamente con diabetes, pero no es equivalente. Poliuria, pérdida de peso, sed intensa, vómito, confusión o glucosa anormal requieren pruebas y atención de diabetes basada en evidencia.',
    examFocus: 'Unlike Yu Quan Wan, this presentation emphasizes Yangming Heat with simultaneous depletion of Qi and Blood.', examFocusEs: 'A diferencia de Yu Quan Wan, este cuadro enfatiza Calor Yangming con agotamiento simultáneo de Qi y Sangre.'
  }
].map((item, index) => makeFormula('tonify-qi-blood-ch8', 'TQB', index + 1, item));

const yinFormulas = [
  {
    name: 'Liu Wei Di Huang Wan', englishName: 'Six-Ingredient Pill with Rehmannia', sourcePages: '365–370', kings: 'Shu Di Huang',
    pattern: 'Liver and Kidney Yin deficiency.', patternEs: 'Deficiencia de Yin de Hígado y Riñón.',
    action: 'Enriches Liver-Kidney Yin while regulating fluids and deficiency Heat.', actionEs: 'Enriquece Yin de Hígado-Riñón mientras regula líquidos y Calor por deficiencia.',
    clue: 'Sore weak low back, dizziness, tinnitus, reduced hearing, night sweats, nocturnal emissions, possible hot palms/soles, red scant-coated tongue, and a thin rapid pulse.', clueEs: 'Dolor y debilidad lumbar, mareo, tinnitus, menor audición, sudor nocturno, emisiones nocturnas, posible calor en palmas/plantas, lengua roja con poca saburra y pulso fino-rápido.',
    ingredients: 'Shu Di Huang, Shan Zhu Yu, Shan Yao, Ze Xie, Mu Dan Pi, and Fu Ling.', ingredientsEs: 'Shu Di Huang, Shan Zhu Yu, Shan Yao, Ze Xie, Mu Dan Pi y Fu Ling.',
    examFocus: 'Three tonics plus three regulators: balanced, long-term Yin support rather than pure cloying supplementation.', examFocusEs: 'Tres tónicos más tres reguladores: apoyo equilibrado y prolongado de Yin, no suplementación empalagosa pura.',
    associated: [['Qi Ju Di Huang Wan / Zhi Bai Di Huang Wan', 'Add Gou Qi Zi and Ju Hua for deficient eyes; add Zhi Mu and Huang Bai when deficiency Fire is stronger.', 'Añaden Gou Qi Zi y Ju Hua para ojos deficientes; añaden Zhi Mu y Huang Bai cuando Fuego por deficiencia es más fuerte.']]
  },
  {
    name: 'Zuo Gui Wan', englishName: 'Restore the Left [Kidney] Pill', sourcePages: '370–372', kings: 'Shu Di Huang',
    pattern: 'Severe Kidney Yin deficiency with damage to essence and marrow and little excess Heat.', patternEs: 'Deficiencia grave de Yin de Riñón con lesión de esencia y médula y poco Calor de exceso.',
    action: 'Nourishes true Yin, enriches the Kidneys, fills essence, and augments marrow.', actionEs: 'Nutre Yin verdadero, enriquece Riñones, llena esencia y aumenta médula.',
    clue: 'Light-headedness, tinnitus, weak sore low back and legs, emissions, night sweats, dry mouth and throat, thirst, peeled shiny tongue, and thin rapid pulse.', clueEs: 'Mareo, tinnitus, debilidad y dolor lumbar y de piernas, emisiones, sudor nocturno, boca y garganta secas, sed, lengua pelada y brillante y pulso fino-rápido.',
    ingredients: 'Shu Di Huang, Chao Shan Yao, Gou Qi Zi, Shan Zhu Yu, Chuan Niu Xi, Tu Si Zi, Lu Jiao Jiao, and Gui Ban Jiao.', ingredientsEs: 'Shu Di Huang, Chao Shan Yao, Gou Qi Zi, Shan Zhu Yu, Chuan Niu Xi, Tu Si Zi, Lu Jiao Jiao y Gui Ban Jiao.',
    safetyTier: 'high', safety: 'Very rich and cloying; use cautiously with Spleen-Stomach deficiency and avoid excess Heat. Animal-derived gelatins may be restricted or unacceptable to some patients.', safetyEs: 'Muy rica y empalagosa; usar con cautela en deficiencia de Bazo-Estómago y evitar en Calor de exceso. Las gelatinas animales pueden estar restringidas o ser inaceptables para algunos pacientes.',
    examFocus: 'Pure, intensive essence-building with no three draining herbs; generally shorter-term than Liu Wei Di Huang Wan.', examFocusEs: 'Construcción pura e intensa de esencia sin las tres hierbas drenantes; en general más breve que Liu Wei Di Huang Wan.'
  },
  {
    name: 'Da Bu Yin Wan', englishName: 'Great Tonify the Yin Pill', sourcePages: '372–375', kings: 'Shu Di Huang, Gui Ban',
    pattern: 'Yin deficiency with vigorous, drainable Fire.', patternEs: 'Deficiencia de Yin con Fuego vigoroso susceptible de drenaje.',
    action: 'Enriches Yin and directs Fire downward.', actionEs: 'Enriquece Yin y dirige Fuego hacia abajo.',
    clue: 'Afternoon tidal fever, night sweats, emissions, irritability, heat and pain of knees/legs, red scant-coated tongue, and a rapid forceful rear pulse.', clueEs: 'Fiebre vespertina, sudor nocturno, emisiones, irritabilidad, calor y dolor de rodillas/piernas, lengua roja con poca saburra y pulso posterior rápido y fuerte.',
    ingredients: 'Shu Di Huang, Gui Ban, Huang Bai, and Zhi Mu; the historical pill uses honey and pig spinal marrow.', ingredientsEs: 'Shu Di Huang, Gui Ban, Huang Bai y Zhi Mu; la píldora histórica usa miel y médula espinal de cerdo.',
    examFocus: 'Simultaneously cultivate the root and clear the source: nourish Yin while forcefully descending vigorous deficiency Fire.', examFocusEs: 'Cultivar raíz y aclarar fuente simultáneamente: nutrir Yin mientras se hace descender Fuego vigoroso por deficiencia.'
  },
  {
    name: 'Yu Quan Wan', englishName: 'Jade Spring Pill', sourcePages: '375–377', kings: 'Ren Shen, Huang Qi, Fu Ling, Mai Men Dong',
    pattern: 'Wasting-thirst disorder with exhausted Qi and fluids.', patternEs: 'Trastorno de consunción-sed con Qi y líquidos agotados.',
    action: 'Augments Qi, nourishes Yin, clears Heat, and generates fluids.', actionEs: 'Fortalece Qi, nutre Yin, aclara Calor y genera líquidos.',
    clue: 'Irritability with thirst, urinary frequency, dry mouth, increased appetite, weight loss, large rapid pulse, and red dry tongue.', clueEs: 'Irritabilidad con sed, frecuencia urinaria, boca seca, mayor apetito, pérdida de peso, pulso grande-rápido y lengua roja-seca.',
    ingredients: 'Mai Men Dong, Ren Shen, Fu Ling, Huang Qi, Wu Mei, Gan Cao, Tian Hua Fen, and Ge Gen.', ingredientsEs: 'Mai Men Dong, Ren Shen, Fu Ling, Huang Qi, Wu Mei, Gan Cao, Tian Hua Fen y Ge Gen.',
    safetyTier: 'high', safety: 'Do not use as a universal diabetes remedy or replace glucose monitoring and medical treatment. Marked thirst, polyuria, weight loss, vomiting, abdominal pain, confusion, or deep breathing can be an emergency.', safetyEs: 'No usar como remedio universal para diabetes ni sustituir control de glucosa y tratamiento médico. Sed intensa, poliuria, pérdida de peso, vómito, dolor abdominal, confusión o respiración profunda pueden ser una emergencia.',
    examFocus: 'Upper wasting with both Qi and fluid exhaustion; more tonifying than Bai Hu Jia Ren Shen Tang.', examFocusEs: 'Consunción superior con agotamiento de Qi y líquidos; más tónica que Bai Hu Jia Ren Shen Tang.'
  },
  {
    name: 'Hu Qian Wan', englishName: 'Hidden Tiger Pill', sourcePages: '377–379', kings: 'Huang Bai, Gui Ban',
    pattern: 'Atrophy disorder from Liver-Kidney deficiency with uncontrolled ministerial Fire.', patternEs: 'Trastorno de atrofia por deficiencia de Hígado-Riñón con Fuego ministerial descontrolado.',
    action: 'Enriches Yin, directs Fire downward, and strengthens sinews and bones.', actionEs: 'Enriquece Yin, dirige Fuego hacia abajo y fortalece tendones y huesos.',
    clue: 'Weak low back and knees, wasting of legs and feet, difficulty walking, red scant-coated tongue, and a thin frail pulse.', clueEs: 'Debilidad lumbar y de rodillas, atrofia de piernas y pies, dificultad para caminar, lengua roja con poca saburra y pulso fino y débil.',
    ingredients: 'Huang Bai, Zhi Mu, Shu Di Huang, Gui Ban, Bai Shao, Suo Yang, Gan Jiang, and Chen Pi; historical versions included tiger bone.', ingredientsEs: 'Huang Bai, Zhi Mu, Shu Di Huang, Gui Ban, Bai Shao, Suo Yang, Gan Jiang y Chen Pi; versiones históricas incluían hueso de tigre.',
    safetyTier: 'critical', safety: 'Tiger parts are prohibited and must never be used. Weakness, muscle wasting, or difficulty walking requires neurologic and orthopedic evaluation; the formula is contraindicated when atrophy comes from Damp-Heat or Spleen-Stomach deficiency.', safetyEs: 'Las partes de tigre están prohibidas y nunca deben usarse. Debilidad, atrofia muscular o dificultad para caminar requieren evaluación neurológica y ortopédica; la fórmula está contraindicada si la atrofia procede de Humedad-Calor o deficiencia de Bazo-Estómago.',
    examFocus: 'Atrophy of the lower body with Yin deficiency and ministerial Fire—not simple weakness alone.', examFocusEs: 'Atrofia de la parte inferior con deficiencia de Yin y Fuego ministerial, no debilidad simple.'
  },
  {
    name: 'Da Zao Wan', englishName: 'Great Creation Pill', sourcePages: '379–380', kings: 'Zi He Che',
    pattern: 'Severe Kidney and Lung Yin/essence deficiency with consumptive emaciation.', patternEs: 'Deficiencia grave de Yin/esencia de Riñón y Pulmón con emaciación consuntiva.',
    action: 'Tonifies the Kidneys, enriches Yin, drains Heat, anchors Yang, and nourishes the Lungs.', actionEs: 'Tonifica Riñones, enriquece Yin, drena Calor, ancla Yang y nutre Pulmones.',
    clue: 'Emaciation, difficult sticky sputum, tidal fever, five-center Heat, night sweats, dry parched throat, tinnitus, and dizziness.', clueEs: 'Emaciación, esputo pegajoso difícil, fiebre vespertina, Calor en cinco centros, sudor nocturno, garganta seca, tinnitus y mareo.',
    ingredients: 'Historical formula: Zi He Che, Gui Ban, Huang Bai, Du Zhong, Niu Xi, Mai Men Dong, Tian Men Dong, Sheng Di Huang, and Ren Shen.', ingredientsEs: 'Fórmula histórica: Zi He Che, Gui Ban, Huang Bai, Du Zhong, Niu Xi, Mai Men Dong, Tian Men Dong, Sheng Di Huang y Ren Shen.',
    safetyTier: 'critical', safety: 'Human placenta is not a routine or legally importable medicinal in many jurisdictions and carries contamination and infectious risks. It must not be sourced or used. Emaciation, fever, or coughing blood requires medical investigation.', safetyEs: 'La placenta humana no es un medicinal rutinario ni de importación legal en muchas jurisdicciones y conlleva riesgos infecciosos y de contaminación. No debe obtenerse ni usarse. Emaciación, fiebre o tos con sangre requieren investigación médica.',
    examFocus: 'Consumption has reached the level of essence, shown by emaciation plus Lung and Kidney Yin signs.', examFocusEs: 'La consunción alcanza el nivel de esencia, indicada por emaciación más signos de Yin de Pulmón y Riñón.'
  },
  {
    name: 'Yi Guan Jian', englishName: 'Linking Decoction', sourcePages: '381–383', kings: 'Gou Qi Zi',
    pattern: 'Liver-Kidney Yin deficiency with constrained Liver Qi.', patternEs: 'Deficiencia de Yin de Hígado-Riñón con Qi de Hígado constreñido.',
    action: 'Enriches Yin and spreads Liver Qi.', actionEs: 'Enriquece Yin y dispersa Qi de Hígado.',
    clue: 'Hypochondriac, epigastric, or chest pain with dry parched mouth/throat, acid reflux, red dry tongue, and thin frail or deficient wiry pulse.', clueEs: 'Dolor hipocondríaco, epigástrico o torácico con boca/garganta secas, reflujo ácido, lengua roja y seca y pulso fino-débil o deficiente-de cuerda.',
    ingredients: 'Sheng Di Huang, Gou Qi Zi, Sha Shen, Mai Men Dong, Dang Gui, and Chuan Lian Zi.', ingredientsEs: 'Sheng Di Huang, Gou Qi Zi, Sha Shen, Mai Men Dong, Dang Gui y Chuan Lian Zi.',
    examFocus: 'Constraint caused by insufficient moistening: pain feels tight and dry, yet the tongue and pulse show Yin deficiency.', examFocusEs: 'Estancamiento causado por falta de humectación: dolor tirante y seco con lengua y pulso de deficiencia de Yin.'
  },
  {
    name: 'Er Zhi Wan', englishName: 'Two-Solstice Pill', sourcePages: '383–384', kings: 'Nu Zhen Zi, Mo Han Lian',
    pattern: 'Liver and Kidney Yin deficiency.', patternEs: 'Deficiencia de Yin de Hígado y Riñón.',
    action: 'Tonifies Liver-Kidney Yin and cools Blood without being very cloying.', actionEs: 'Tonifica Yin de Hígado-Riñón y enfría Sangre sin ser muy empalagosa.',
    clue: 'Weak sore low back and knees, dry mouth/throat, dizziness, blurred vision, insomnia, emissions, premature graying or hair loss, and red dry tongue.', clueEs: 'Debilidad y dolor lumbar/rodillas, boca/garganta secas, mareo, visión borrosa, insomnio, emisiones, canicie o caída precoz del cabello y lengua roja-seca.',
    ingredients: 'Nu Zhen Zi and Mo Han Lian in equal amounts.', ingredientsEs: 'Nu Zhen Zi y Mo Han Lian en cantidades iguales.',
    examFocus: 'A mild two-herb long-term Yin tonic; especially memorable for hair and chronic bleeding.', examFocusEs: 'Tónico suave de Yin de dos hierbas y uso prolongado; memorable por cabello y sangrado crónico.'
  },
  {
    name: 'Bai He Gu Jin Tang', englishName: 'Lily Bulb Decoction to Preserve the Metal', sourcePages: '384–386', kings: 'Bai He, Sheng Di Huang, Shu Di Huang',
    pattern: 'Lung and Kidney Yin deficiency with deficiency Heat and Lung dryness.', patternEs: 'Deficiencia de Yin de Pulmón-Riñón con Calor por deficiencia y sequedad pulmonar.',
    action: 'Nourishes Yin, moistens the Lungs, transforms Phlegm, and stops coughing.', actionEs: 'Nutre Yin, humedece Pulmones, transforma Flema y detiene tos.',
    clue: 'Cough with blood-streaked sputum, wheezing, dry sore throat, hot palms/soles, night sweats, red scant-coated tongue, and thin rapid pulse.', clueEs: 'Tos con esputo sanguinolento, sibilancia, garganta seca y dolorosa, calor en palmas/plantas, sudor nocturno, lengua roja con poca saburra y pulso fino-rápido.',
    ingredients: 'Bai He, Sheng Di Huang, Shu Di Huang, Mai Men Dong, Xuan Shen, Chuan Bei Mu, Jie Geng, Dang Gui, Bai Shao, and Gan Cao.', ingredientsEs: 'Bai He, Sheng Di Huang, Shu Di Huang, Mai Men Dong, Xuan Shen, Chuan Bei Mu, Jie Geng, Dang Gui, Bai Shao y Gan Cao.',
    safetyTier: 'high', safety: 'Coughing blood, worsening breathlessness, chest pain, fever, weight loss, or low oxygen requires prompt medical care. Rich cold herbs can aggravate Spleen deficiency or food stagnation.', safetyEs: 'Toser sangre, disnea creciente, dolor torácico, fiebre, pérdida de peso o poco oxígeno requieren atención médica pronta. Hierbas frías y ricas pueden agravar deficiencia de Bazo o estancamiento alimentario.',
    examFocus: 'Lung dryness is more prominent than Phlegm; the root is Lung-Kidney Yin deficiency.', examFocusEs: 'Sequedad de Pulmón predomina sobre Flema; la raíz es deficiencia de Yin de Pulmón-Riñón.'
  },
  {
    name: 'Bu Fei E Jiao Tang', englishName: 'Tonify the Lungs Decoction with Ass-Hide Gelatin', sourcePages: '386–388', kings: 'E Jiao',
    pattern: 'Lung Yin deficiency with vigorous Heat scorching the collaterals.', patternEs: 'Deficiencia de Yin de Pulmón con Calor vigoroso que quema colaterales.',
    action: 'Nourishes Yin, tonifies the Lungs, controls cough, and stops bleeding.', actionEs: 'Nutre Yin, tonifica Pulmones, controla tos y detiene sangrado.',
    clue: 'Cough, wheezing, dry throat, scant or blood-streaked sputum, red scant-coated tongue, and floating thin rapid pulse.', clueEs: 'Tos, sibilancia, garganta seca, esputo escaso o sanguinolento, lengua roja con poca saburra y pulso flotante, fino y rápido.',
    ingredients: 'Historical source: E Jiao, Ma Dou Ling, Xing Ren, Niu Bang Zi, Chao Nuo Mi, and Zhi Gan Cao.', ingredientsEs: 'Fuente histórica: E Jiao, Ma Dou Ling, Xing Ren, Niu Bang Zi, Chao Nuo Mi y Zhi Gan Cao.',
    safetyTier: 'critical', safety: 'The historical Ma Dou Ling is an Aristolochia species containing aristolochic acids associated with kidney failure and cancer; it must not be used. Hemoptysis and wheezing require medical evaluation.', safetyEs: 'Ma Dou Ling histórica es una Aristolochia con ácidos aristolóquicos asociados a insuficiencia renal y cáncer; no debe utilizarse. Hemoptisis y sibilancia requieren evaluación médica.',
    examFocus: 'Blood-streaked sputum is more central than broad Yin tonification; the historical Aristolochia makes the original prescription unusable.', examFocusEs: 'Esputo sanguinolento es más central que tonificación amplia de Yin; la Aristolochia histórica vuelve inutilizable la receta original.'
  },
  {
    name: 'Zi Yin Jiang Huo Tang', englishName: 'Decoction to Enrich Yin and Direct Fire Downward', sourcePages: '388–389', kings: 'Sheng Di Huang, Shu Di Huang, Dang Gui, Bai Shao',
    pattern: 'Consumptive Yin-Blood deficiency with Fire from deficiency.', patternEs: 'Deficiencia consuntiva de Yin-Sangre con Fuego por deficiencia.',
    action: 'Enriches Yin, nourishes Blood, supports the Middle, and directs Fire downward.', actionEs: 'Enriquece Yin, nutre Sangre, apoya Centro y dirige Fuego hacia abajo.',
    clue: 'Dry or productive cough, wheezing, night sweats, dry mouth, fever, coughing blood, sore dry throat, wasting-thirst, and steaming bones.', clueEs: 'Tos seca o productiva, sibilancia, sudor nocturno, boca seca, fiebre, tos con sangre, garganta seca y dolorosa, consunción-sed y huesos humeantes.',
    ingredients: 'Dang Gui, Bai Shao, Shu Di Huang, Sheng Di Huang, Tian Men Dong, Mai Men Dong, Chen Pi, Bai Zhu, Zhi Mu, Huang Bai, Zhi Gan Cao, Sheng Jiang, and Da Zao.', ingredientsEs: 'Dang Gui, Bai Shao, Shu Di Huang, Sheng Di Huang, Tian Men Dong, Mai Men Dong, Chen Pi, Bai Zhu, Zhi Mu, Huang Bai, Zhi Gan Cao, Sheng Jiang y Da Zao.',
    safetyTier: 'high', safety: 'Coughing blood, fever, marked weight loss, or suspected tuberculosis requires prompt medical investigation and infection control. This formula is adjunctive historical material, not antimicrobial treatment.', safetyEs: 'Tos con sangre, fiebre, pérdida marcada de peso o sospecha de tuberculosis requieren investigación médica pronta y control infeccioso. Esta fórmula es material histórico complementario, no tratamiento antimicrobiano.',
    examFocus: 'Compared with Moonlight Pill, this formula drains deficiency Fire and protects the Middle more strongly.', examFocusEs: 'Comparada con Yue Hua Wan, drena con mayor fuerza Fuego por deficiencia y protege el Centro.'
  },
  {
    name: 'Qiong Yu Gao', englishName: 'Precious Jade Syrup', sourcePages: '389–391', kings: 'Sheng Di Huang',
    pattern: 'Lung-Kidney Yin deficiency with Spleen Qi deficiency in a chronic consumptive state.', patternEs: 'Deficiencia de Yin de Pulmón-Riñón con deficiencia de Qi de Bazo en estado consuntivo crónico.',
    action: 'Enriches Yin, moistens the Lungs, augments Qi, and tonifies the Spleen.', actionEs: 'Enriquece Yin, humedece Pulmones, fortalece Qi y tonifica Bazo.',
    clue: 'Dry cough with little sputum, dry throat or a few drops of blood, muscle wasting, shortness of breath, weakness, red scant-coated tongue, and thin rapid pulse.', clueEs: 'Tos seca con poco esputo, garganta seca o algunas gotas de sangre, atrofia muscular, disnea, debilidad, lengua roja con poca saburra y pulso fino-rápido.',
    ingredients: 'Sheng Di Huang, Ren Shen, Fu Ling, and good-quality honey, prepared historically as a syrup.', ingredientsEs: 'Sheng Di Huang, Ren Shen, Fu Ling y miel de buena calidad, preparada históricamente como jarabe.',
    examFocus: 'A mild long-term syrup that simultaneously builds Earth and moistens Metal-Water.', examFocusEs: 'Jarabe suave de uso prolongado que simultáneamente construye Tierra y humedece Metal-Agua.'
  },
  {
    name: 'Zhu Jing Wan', englishName: 'Preserve Vistas Pill', sourcePages: '391–393', kings: 'Tu Si Zi, Shu Di Huang',
    pattern: 'Liver-Kidney deficiency with some Dampness obstructing the eyes.', patternEs: 'Deficiencia de Hígado-Riñón con cierta Humedad que obstruye los ojos.',
    action: 'Tonifies Liver-Kidneys, enriches Yin, drains Dampness, and improves vision.', actionEs: 'Tonifica Hígado-Riñones, enriquece Yin, drena Humedad y mejora visión.',
    clue: 'Blurred or diminished vision worse with exertion and better with rest, weak sore low back and legs, and a thin frail pulse.', clueEs: 'Visión borrosa o disminuida peor con esfuerzo y mejor con reposo, debilidad y dolor lumbar y de piernas y pulso fino-débil.',
    ingredients: 'Tu Si Zi 150 g, Shu Di Huang 120 g, and Che Qian Zi 60 g in the source formula.', ingredientsEs: 'Tu Si Zi 150 g, Shu Di Huang 120 g y Che Qian Zi 60 g en la fórmula fuente.',
    safetyTier: 'high', safety: 'New or sudden visual loss, flashes, a curtain-like shadow, eye pain, neurologic symptoms, or acute glaucoma signs require urgent ophthalmic care.', safetyEs: 'Pérdida visual nueva o súbita, destellos, sombra tipo cortina, dolor ocular, síntomas neurológicos o signos de glaucoma agudo requieren atención oftalmológica urgente.',
    examFocus: 'Deficient vision plus Dampness: nourish below while draining pathological water above.', examFocusEs: 'Visión deficiente más Humedad: nutrir abajo mientras se drena agua patológica arriba.'
  },
  {
    name: 'Yi Wei Tang', englishName: 'Benefit the Stomach Decoction', sourcePages: '393–394', kings: 'Sheng Di Huang, Mai Men Dong',
    pattern: 'Stomach Yin deficiency.', patternEs: 'Deficiencia de Yin de Estómago.',
    action: 'Benefits the Stomach and generates fluids.', actionEs: 'Beneficia Estómago y genera líquidos.',
    clue: 'Faint epigastric pain or burning, hunger without desire to eat, dry mouth/throat, constipation, retching or hiccup, red dry scant-coated tongue, and thin rapid pulse.', clueEs: 'Dolor epigástrico leve o ardor, hambre sin deseo de comer, boca/garganta secas, estreñimiento, arcadas o hipo, lengua roja, seca y con poca saburra y pulso fino-rápido.',
    ingredients: 'Sha Shen 9 g, Mai Men Dong 15 g, Sheng Di Huang 15 g, Yu Zhu 4.5 g, and Bing Tang 3 g.', ingredientsEs: 'Sha Shen 9 g, Mai Men Dong 15 g, Sheng Di Huang 15 g, Yu Zhu 4.5 g y Bing Tang 3 g.',
    examFocus: 'Hunger without appetite is the classic paradox of Stomach deficiency Heat.', examFocusEs: 'Hambre sin apetito es la paradoja clásica de Calor por deficiencia de Estómago.'
  }
].map((item, index) => makeFormula('nourish-tonify-yin-ch8', 'TY', index + 1, item));

const yangFormulas = [
  {
    name: 'Shen Qi Wan', englishName: 'Kidney Qi Pill', sourcePages: '395–400', kings: 'Sheng Di Huang, Zhi Fu Zi, Gui Zhi',
    pattern: 'Kidney Yang-Qi deficiency with impaired transformation and regulation of water.', patternEs: 'Deficiencia de Yang-Qi de Riñón con transformación y regulación alteradas del agua.',
    action: 'Warms and tonifies Kidney Yang, generates Kidney Qi, and regulates water metabolism.', actionEs: 'Calienta y tonifica Yang de Riñón, genera Qi renal y regula el metabolismo del agua.',
    clue: 'Cold painful weak low back and legs, a tense lower abdomen, pale swollen moist tongue, deep faint proximal pulse, and either difficult urination with edema or frequent copious urine.', clueEs: 'Frío, dolor y debilidad lumbar y de piernas, abdomen inferior tenso, lengua pálida, hinchada y húmeda, pulso proximal profundo y débil y orina difícil con edema o micción frecuente y copiosa.',
    ingredients: 'Sheng Di Huang 24 g, Shan Zhu Yu 12 g, Shan Yao 12 g, baked Zhi Fu Zi 3 g, Gui Zhi 3 g, Ze Xie 9 g, Fu Ling 9 g, and Mu Dan Pi 9 g. Modern versions often use Shu Di Huang and Rou Gui.', ingredientsEs: 'Sheng Di Huang 24 g, Shan Zhu Yu 12 g, Shan Yao 12 g, Zhi Fu Zi horneado 3 g, Gui Zhi 3 g, Ze Xie 9 g, Fu Ling 9 g y Mu Dan Pi 9 g. Versiones modernas suelen usar Shu Di Huang y Rou Gui.',
    examFocus: 'A little Fire generates Qi: a small warming pair activates a larger Yin-tonifying and water-regulating base.', examFocusEs: 'Un poco de Fuego genera Qi: una pareja caliente pequeña activa una base mayor que nutre Yin y regula agua.',
    associated: [
      ['Jia Wei Shen Qi Wan', 'Increases warming and downward-draining actions for Kidney Yang deficiency with edema.', 'Aumenta las acciones calientes y descendentes para deficiencia de Yang de Riñón con edema.'],
      ['Shi Bu Wan', 'Strengthens essence and the Kidney grasping function more than the base formula.', 'Fortalece más la esencia y la función renal de captar Qi.'],
      ['Wu Bi Shan Yao Wan', 'Tonifies Kidney Yang and essence directly, with less emphasis on water-Fire imbalance.', 'Tonifica directamente Yang y esencia de Riñón, con menos énfasis en el desequilibrio agua-Fuego.']
    ]
  },
  {
    name: 'You Gui Wan', englishName: 'Restore the Right [Kidney] Pill', sourcePages: '401–402', kings: 'Zhi Fu Zi, Rou Gui, Lu Jiao Jiao',
    pattern: 'Severe Kidney Yang deficiency with waning Fire at the gate of vitality and depleted essence-Blood.', patternEs: 'Deficiencia grave de Yang de Riñón con declive del Fuego de la puerta de la vitalidad y agotamiento de esencia-Sangre.',
    action: 'Warms and tonifies Kidney Yang, replenishes essence, and tonifies Blood.', actionEs: 'Calienta y tonifica Yang de Riñón, repone esencia y tonifica Sangre.',
    clue: 'Chronic exhaustion, aversion to Cold, cold limbs, impotence or infertility, spermatorrhea, weak sore low back and knees, loose stools, incontinence, or lower-limb edema.', clueEs: 'Agotamiento crónico, aversión al Frío, miembros fríos, impotencia o infertilidad, espermatorrea, debilidad y dolor lumbar/rodillas, heces sueltas, incontinencia o edema de piernas.',
    ingredients: 'Zhi Fu Zi, Rou Gui, Lu Jiao Jiao, Shu Di Huang, Shan Zhu Yu, Shan Yao, Gou Qi Zi, Tu Si Zi, Du Zhong, and Dang Gui.', ingredientsEs: 'Zhi Fu Zi, Rou Gui, Lu Jiao Jiao, Shu Di Huang, Shan Zhu Yu, Shan Yao, Gou Qi Zi, Tu Si Zi, Du Zhong y Dang Gui.',
    safety: 'Contains processed Fu Zi and is strongly warming; never self-prepare. It tonifies without draining and is inappropriate when Dampness or turbidity is prominent. Deer-derived ingredients may be restricted.', safetyEs: 'Contiene Fu Zi procesado y es fuertemente caliente; nunca preparar por cuenta propia. Tonifica sin drenar y es inapropiada cuando predominan Humedad o turbidez. Ingredientes derivados de ciervo pueden estar restringidos.',
    examFocus: 'Compared with Shen Qi Wan, this is deeper, richer tonification for exhaustion and essence deficiency—not a water-metabolism formula.', examFocusEs: 'Frente a Shen Qi Wan, tonifica más profunda y ricamente agotamiento y esencia; no es una fórmula centrada en metabolismo del agua.',
    associated: [['You Gui Yin', 'A somewhat weaker drink form for Kidney Yang deficiency and true Cold with false Heat.', 'Bebida algo más suave para deficiencia de Yang de Riñón y Frío verdadero con Calor falso.'], ['Qing E Wan', 'Targets severe low-back pain from Kidney Yang deficiency and Cold-Damp or trauma.', 'Se dirige a dolor lumbar grave por deficiencia de Yang de Riñón y Frío-Humedad o trauma.']]
  },
  {
    name: 'Tu Si Zi Wan', englishName: 'Cuscuta Seed Pill', sourcePages: '403', kings: 'Tu Si Zi, Lu Rong, Rou Cong Rong, Shan Yao',
    pattern: 'Kidney Qi deficiency with failure to grasp the essence and restrain urine.', patternEs: 'Deficiencia de Qi de Riñón con incapacidad para captar esencia y contener orina.',
    action: 'Warms the Kidneys, tonifies essence, and prevents abnormal leakage.', actionEs: 'Calienta Riñones, tonifica esencia y previene pérdidas anormales.',
    clue: 'Exhaustion, aversion to Cold, frail physique, dizziness, weak painful low back and legs, frequent scant urination with continuous dribbling, and a deep thin frail proximal pulse.', clueEs: 'Agotamiento, aversión al Frío, constitución frágil, mareo, dolor y debilidad lumbar/piernas, micción frecuente y escasa con goteo continuo y pulso proximal profundo, fino y débil.',
    ingredients: 'Tu Si Zi, Lu Rong, Rou Cong Rong, Shan Yao, Zhi Fu Zi, Wu Yao, Wu Wei Zi, Sang Piao Xiao, Yi Zhi Ren, Duan Mu Li, and Ji Nei Jin.', ingredientsEs: 'Tu Si Zi, Lu Rong, Rou Cong Rong, Shan Yao, Zhi Fu Zi, Wu Yao, Wu Wei Zi, Sang Piao Xiao, Yi Zhi Ren, Duan Mu Li y Ji Nei Jin.',
    safety: 'Contains processed Fu Zi and animal-derived ingredients; never self-prepare. New urinary retention, fever, blood in urine, flank pain, or neurologic bladder symptoms require urgent evaluation.', safetyEs: 'Contiene Fu Zi procesado e ingredientes animales; nunca preparar por cuenta propia. Retención urinaria nueva, fiebre, sangre en orina, dolor en flanco o síntomas neurológicos vesicales requieren evaluación urgente.',
    examFocus: 'The hallmark is leakage: deficient Kidney Qi cannot grasp, so urine dribbles and essence may escape.', examFocusEs: 'La clave es la pérdida: Qi renal deficiente no puede captar, por lo que gotea orina y puede escapar esencia.'
  },
  {
    name: 'Zan Yu Dan', englishName: 'Special Pill to Aid Fertility', sourcePages: '403–404', kings: 'Zhi Fu Zi, Rou Gui, Rou Cong Rong, Ba Ji Tian',
    pattern: 'Waning Fire at the gate of vitality with Cold-deficient essential Qi, plus Qi and Blood deficiency.', patternEs: 'Declive del Fuego de la puerta de la vitalidad con Qi esencial frío y deficiente, además de deficiencia de Qi y Sangre.',
    action: 'Strongly warms and tonifies the Kidneys and lower base to promote fertility.', actionEs: 'Calienta y tonifica fuertemente Riñones y base inferior para favorecer fertilidad.',
    clue: 'Impotence or infertility with listlessness, dispiritedness, weak sore low back, pale shiny complexion, and a deep thin pulse.', clueEs: 'Impotencia o infertilidad con apatía, desánimo, debilidad y dolor lumbar, tez pálida y brillante y pulso profundo-fino.',
    ingredients: 'Zhi Fu Zi, Rou Gui, Rou Cong Rong, Ba Ji Tian, Yin Yang Huo, She Chuang Zi, Jiu Zi, Xian Mao, Shan Zhu Yu, Du Zhong, Shu Di Huang, Dang Gui, Gou Qi Zi, and Bai Zhu.', ingredientsEs: 'Zhi Fu Zi, Rou Gui, Rou Cong Rong, Ba Ji Tian, Yin Yang Huo, She Chuang Zi, Jiu Zi, Xian Mao, Shan Zhu Yu, Du Zhong, Shu Di Huang, Dang Gui, Gou Qi Zi y Bai Zhu.',
    safety: 'Contains processed Fu Zi and Xian Mao and requires specialist supervision. Infertility and erectile dysfunction require biomedical assessment; the historical Scolopendra modification should not be self-administered.', safetyEs: 'Contiene Fu Zi procesado y Xian Mao y exige supervisión especializada. Infertilidad y disfunción eréctil requieren evaluación biomédica; la modificación histórica con Scolopendra no debe autoadministrarse.',
    examFocus: 'A powerful fertility-centered Kidney Yang formula, balanced by Yin-Blood and Spleen support.', examFocusEs: 'Fórmula potente de Yang de Riñón centrada en fertilidad, equilibrada con apoyo de Yin-Sangre y Bazo.',
    associated: [['Huan Shao Dan', 'A broader Kidney-Heart-Spleen formula for loss of essence, premature aging, fatigue, and sexual weakness.', 'Fórmula más amplia de Riñón-Corazón-Bazo para pérdida de esencia, envejecimiento precoz, fatiga y debilidad sexual.']]
  }
].map((item, index) => makeFormula('warm-tonify-yang-ch8', 'TG', index + 1, item));

const yinYangFormulas = [
  {
    name: 'Di Huang Yin Zi (Rehmannia Drink)', englishName: 'Rehmannia Drink', sourcePages: '405–407', kings: 'Shu Di Huang, Shan Zhu Yu, Rou Cong Rong, Ba Ji Tian',
    pattern: 'Kidney Yin-Yang deficiency with deficient Yang flaring upward and Phlegm turbidity blocking the orifices.', patternEs: 'Deficiencia de Yin-Yang de Riñón con Yang deficiente que asciende y turbidez de Flema que bloquea orificios.',
    action: 'Enriches Kidney Yin, tonifies Kidney Yang, opens the orifices, and transforms Phlegm.', actionEs: 'Enriquece Yin de Riñón, tonifica Yang renal, abre orificios y transforma Flema.',
    clue: 'Stiff tongue with inability to speak, lower-extremity disability or paralysis, dry mouth without thirst, greasy yellow coat, and a deep slow thin frail pulse.', clueEs: 'Lengua rígida con incapacidad para hablar, discapacidad o parálisis de piernas, boca seca sin sed, saburra amarilla grasosa y pulso profundo, lento, fino y débil.',
    ingredients: 'Shu Di Huang, Shan Zhu Yu, Rou Cong Rong, Ba Ji Tian, Zhi Fu Zi, Rou Gui, Shi Hu, Fu Ling, Wu Wei Zi, Mai Men Dong, Shi Chang Pu, Yuan Zhi; traditionally with Sheng Jiang and Da Zao.', ingredientsEs: 'Shu Di Huang, Shan Zhu Yu, Rou Cong Rong, Ba Ji Tian, Zhi Fu Zi, Rou Gui, Shi Hu, Fu Ling, Wu Wei Zi, Mai Men Dong, Shi Chang Pu y Yuan Zhi; tradicionalmente con Sheng Jiang y Da Zao.',
    safetyTier: 'critical', safety: 'Sudden aphasia, facial droop, weakness, or paralysis is a stroke emergency: activate emergency services immediately. This historical formula contains processed Fu Zi and is not acute stroke treatment or appropriate for self-use.', safetyEs: 'Afasia súbita, caída facial, debilidad o parálisis constituyen emergencia por ictus: activar servicios de emergencia inmediatamente. Esta fórmula histórica contiene Fu Zi procesado, no trata el ictus agudo ni es apta para autouso.',
    examFocus: 'Deficiency below and obstruction above: Kidney Yin-Yang are depleted while turbid Phlegm closes speech and movement.', examFocusEs: 'Deficiencia abajo y obstrucción arriba: Yin-Yang de Riñón están agotados mientras Flema turbia cierra habla y movimiento.'
  },
  {
    name: 'Gui Lu Er Xian Jiao', englishName: 'Tortoise Shell and Deer Antler Two-Immortal Syrup', sourcePages: '407–408', kings: 'Lu Jiao Jiao, Gui Ban Jiao',
    pattern: 'Severe Kidney Yin-Yang deficiency with insufficiency of essence and Blood in the Conception and Governing vessels.', patternEs: 'Deficiencia grave de Yin-Yang de Riñón con insuficiencia de esencia y Sangre en Ren Mai y Du Mai.',
    action: 'Replenishes Yin and essence, augments Qi, nourishes Blood, and strengthens Yang.', actionEs: 'Repone Yin y esencia, fortalece Qi, nutre Sangre y fortalece Yang.',
    clue: 'Emaciation, weakness, spermatorrhea, impotence, diminished vision, weak aching low back and knees, daybreak diarrhea, and a thin frail pulse.', clueEs: 'Emaciación, debilidad, espermatorrea, impotencia, visión disminuida, dolor y debilidad lumbar/rodillas, diarrea al amanecer y pulso fino-débil.',
    ingredients: 'Lu Jiao Jiao, Gui Ban Jiao, Gou Qi Zi, and Ren Shen, historically prepared as a syrup.', ingredientsEs: 'Lu Jiao Jiao, Gui Ban Jiao, Gou Qi Zi y Ren Shen, preparados históricamente como jarabe.',
    safety: 'Very cloying and unsuitable for Spleen-Stomach deficiency; contraindicated in Yin-deficiency Fire. Deer and turtle products may be legally restricted, ethically unacceptable, or unavailable—verify sustainable lawful sourcing and alternatives.', safetyEs: 'Muy empalagosa e inapropiada en deficiencia de Bazo-Estómago; contraindicada en Fuego por deficiencia de Yin. Productos de ciervo y tortuga pueden estar restringidos, ser éticamente inaceptables o no disponibles; verificar procedencia legal sostenible y alternativas.',
    examFocus: 'The most substance-building choice: severe depletion of essence, marrow, Qi, Blood, Yin, and Yang with few excess signs.', examFocusEs: 'La opción que más construye sustancia: agotamiento grave de esencia, médula, Qi, Sangre, Yin y Yang con pocos signos de exceso.'
  },
  {
    name: 'Qi Bao Mei Ran Dan', englishName: 'Seven-Treasure Special Pill for Beautiful Whiskers', sourcePages: '409–410', kings: 'He Shou Wu',
    pattern: 'Liver Blood and Kidney Yin deficiency with insufficient essence, plus a small Yang deficiency component.', patternEs: 'Deficiencia de Sangre de Hígado y Yin de Riñón con esencia insuficiente, más un componente menor de deficiencia de Yang.',
    action: 'Enriches Kidney Yin, nourishes Liver Blood, secures essence, and supports Yang.', actionEs: 'Enriquece Yin de Riñón, nutre Sangre de Hígado, asegura esencia y apoya Yang.',
    clue: 'Premature graying or hair loss, loose teeth, spontaneous or nocturnal emissions, and weakness or soreness of the low back and knees.', clueEs: 'Canicie o caída precoz del cabello, dientes flojos, emisiones espontáneas o nocturnas y debilidad o dolor lumbar y de rodillas.',
    ingredients: 'Prepared He Shou Wu, Fu Ling, Chi Fu Ling, Niu Xi, Dang Gui, Gou Qi Zi, Tu Si Zi, and Bu Gu Zhi; black sesame is used in processing.', ingredientsEs: 'He Shou Wu preparado, Fu Ling, Chi Fu Ling, Niu Xi, Dang Gui, Gou Qi Zi, Tu Si Zi y Bu Gu Zhi; sésamo negro se usa en el procesamiento.',
    safetyTier: 'critical', safety: 'He Shou Wu products have caused severe, sometimes fatal liver injury. Do not self-treat hair loss or graying; avoid in liver disease and stop with jaundice, dark urine, severe fatigue, nausea, or right-upper abdominal pain. Evaluate sudden or patchy hair loss medically.', safetyEs: 'Productos con He Shou Wu han causado lesión hepática grave y a veces mortal. No automedicarse para caída o canicie; evitar en hepatopatía y suspender ante ictericia, orina oscura, fatiga intensa, náusea o dolor abdominal superior derecho. Evaluar médicamente la caída súbita o en parches.',
    examFocus: 'Hair and teeth mark essence-Blood deficiency; Fu Ling drains and Bu Gu Zhi supplies the Yang within Yin.', examFocusEs: 'Cabello y dientes señalan deficiencia de esencia-Sangre; Fu Ling drena y Bu Gu Zhi aporta Yang dentro de Yin.'
  },
  {
    name: 'Er Xian Tang', englishName: 'Two-Immortal Decoction', sourcePages: '410–411', kings: 'Xian Mao, Yin Yang Huo',
    pattern: 'Kidney Yin-Yang deficiency with flaring Fire at the gate of vitality and Chong-Ren disharmony.', patternEs: 'Deficiencia de Yin-Yang de Riñón con Fuego ascendente en la puerta de la vitalidad y desarmonía Chong-Ren.',
    action: 'Warms Kidney Yang, tonifies essence, drains Kidney Fire, nourishes Blood, and regulates the Penetrating and Conception vessels.', actionEs: 'Calienta Yang de Riñón, tonifica esencia, drena Fuego renal, nutre Sangre y regula Chong Mai y Ren Mai.',
    clue: 'Menopausal hot flashes, sweating, irritability, insomnia, palpitations, hypertension or menstrual irregularity together with fatigue, depression, lassitude, and urinary frequency.', clueEs: 'Sofocos menopáusicos, sudor, irritabilidad, insomnio, palpitaciones, hipertensión o irregularidad menstrual junto con fatiga, depresión, lasitud y frecuencia urinaria.',
    ingredients: 'Xian Mao 6–15 g, Yin Yang Huo 9–15 g, Ba Ji Tian 9 g, Huang Bai 4.5–9 g, Zhi Mu 4.5–9 g, and Dang Gui 9 g.', ingredientsEs: 'Xian Mao 6–15 g, Yin Yang Huo 9–15 g, Ba Ji Tian 9 g, Huang Bai 4.5–9 g, Zhi Mu 4.5–9 g y Dang Gui 9 g.',
    safetyTier: 'critical', safety: 'Xian Mao is potent and potentially toxic; this formula requires expert supervision. Severe hypertension, chest pain, syncope, marked palpitations, postmenopausal bleeding, or new psychiatric symptoms require prompt medical evaluation.', safetyEs: 'Xian Mao es potente y potencialmente tóxico; la fórmula requiere supervisión experta. Hipertensión grave, dolor torácico, síncope, palpitaciones marcadas, sangrado posmenopáusico o síntomas psiquiátricos nuevos requieren evaluación pronta.',
    examFocus: 'Simultaneous Cold below and Fire above: warm deficient Kidney Yang while Zhi Mu-Huang Bai drain flaring Fire.', examFocusEs: 'Frío abajo y Fuego arriba simultáneos: calentar Yang renal deficiente mientras Zhi Mu-Huang Bai drenan Fuego ascendente.'
  }
].map((item, index) => makeFormula('tonify-yin-yang-ch8', 'TYY', index + 1, item));

export const tonifyChapterFormulas = [
  ...qiFormulas,
  ...bloodFormulas,
  ...qiBloodFormulas,
  ...yinFormulas,
  ...yangFormulas,
  ...yinYangFormulas
];

const guideRoutes = {
  'tonify-qi-ch8': [
    ['1', 'Start with the location', 'Spleen weakness points to Si Jun Zi Tang or Shen Ling Bai Zhu San; Lung weakness points to Bu Fei Tang; Lung-Kidney grasping failure points to Ren Shen Ge Jie San.', 'Empieza por la localización', 'Debilidad de Bazo orienta a Si Jun Zi Tang o Shen Ling Bai Zhu San; Pulmón a Bu Fei Tang; fallo de captación Pulmón-Riñón a Ren Shen Ge Jie San.'],
    ['2', 'Then identify movement', 'Sinking favors Bu Zhong Yi Qi Tang; unstable exterior favors Yu Ping Feng San; fluid loss with Qi deficiency favors Sheng Mai San.', 'Luego identifica movimiento', 'Hundimiento favorece Bu Zhong Yi Qi Tang; Exterior inestable, Yu Ping Feng San; pérdida de líquidos con deficiencia de Qi, Sheng Mai San.'],
    ['3', 'Separate Dampness and Phlegm', 'Chronic diarrhea and Dampness favor Shen Ling Bai Zhu San; Damp-Heat below favors Sheng Yang Yi Wei Tang; Phlegm-Heat with difficult inhalation favors Ren Shen Ge Jie San.', 'Separa Humedad y Flema', 'Diarrea crónica y Humedad favorecen Shen Ling Bai Zhu San; Humedad-Calor abajo, Sheng Yang Yi Wei Tang; Flema-Calor con inhalación difícil, Ren Shen Ge Jie San.']
  ],
  'tonify-blood-ch8': [
    ['1', 'Choose the Blood foundation', 'Si Wu Tang is the general nourish-and-move base; Dang Gui Bu Xue Tang generates Blood through a high dose of Qi tonic.', 'Elige la base de Sangre', 'Si Wu Tang es la base general para nutrir y mover; Dang Gui Bu Xue Tang genera Sangre mediante una dosis alta de tónico de Qi.'],
    ['2', 'Locate Cold, stasis, or spasm', 'Guo Qi Yin addresses delayed menses with Cold and stasis; Dang Gui Sheng Jiang Yang Rou Tang treats deficient Cold pain; Shao Yao Gan Cao Tang treats painful contraction.', 'Localiza Frío, estasis o espasmo', 'Guo Qi Yin aborda menstruación retrasada con Frío y estasis; Dang Gui Sheng Jiang Yang Rou Tang dolor por Frío deficiente; Shao Yao Gan Cao Tang contracción dolorosa.'],
    ['3', 'Do not miss loss', 'Amenorrhea, postpartum pain, or apparent Heat after Blood loss needs biomedical assessment before selecting a tonic strategy.', 'No omitas la pérdida', 'Amenorrea, dolor posparto o Calor aparente tras pérdida de Sangre requieren evaluación biomédica antes de una estrategia tónica.']
  ],
  'tonify-qi-blood-ch8': [
    ['1', 'Select breadth and temperature', 'Ba Zhen Tang is the neutral combined base; Shi Quan Da Bu Tang is warmer; Ren Shen Yang Rong Tang adds Heart-Shen and dryness emphasis.', 'Elige amplitud y temperatura', 'Ba Zhen Tang es la base combinada neutra; Shi Quan Da Bu Tang es más caliente; Ren Shen Yang Rong Tang añade énfasis en Corazón-Shen y sequedad.'],
    ['2', 'Follow the dominant organ', 'Gui Pi Tang centers Spleen-Heart with bleeding or insomnia; Zhi Gan Cao Tang centers Heart Qi-Blood with irregular pulse; He Ren Yin centers chronic malarial depletion.', 'Sigue el órgano dominante', 'Gui Pi Tang centra Bazo-Corazón con sangrado o insomnio; Zhi Gan Cao Tang Qi-Sangre de Corazón con pulso irregular; He Ren Yin agotamiento malárico crónico.'],
    ['3', 'Pregnancy is a separate safety route', 'Tai Shan Pan Shi San and Bao Chan Wu You Fang are historical obstetric formulas—not self-care for threatened miscarriage or labor complications.', 'Embarazo tiene una ruta de seguridad separada', 'Tai Shan Pan Shi San y Bao Chan Wu You Fang son fórmulas obstétricas históricas, no autocuidado para amenaza de aborto o complicaciones del parto.']
  ],
  'nourish-tonify-yin-ch8': [
    ['1', 'Locate the Yin deficiency', 'Kidney-Liver: Liu Wei, Zuo Gui, Da Bu Yin, Er Zhi, or Zhu Jing; Lung-Kidney: Bai He Gu Jin, Zi Yin Jiang Huo, or Qiong Yu; Stomach: Yi Wei Tang.', 'Localiza la deficiencia de Yin', 'Riñón-Hígado: Liu Wei, Zuo Gui, Da Bu Yin, Er Zhi o Zhu Jing; Pulmón-Riñón: Bai He Gu Jin, Zi Yin Jiang Huo o Qiong Yu; Estómago: Yi Wei Tang.'],
    ['2', 'Grade Fire and depletion', 'Liu Wei is balanced; Zuo Gui builds essence without Fire signs; Da Bu Yin and Zi Yin Jiang Huo actively descend deficiency Fire; severe consumption demands urgent evaluation.', 'Gradúa Fuego y agotamiento', 'Liu Wei es equilibrada; Zuo Gui construye esencia sin Fuego; Da Bu Yin y Zi Yin Jiang Huo descienden activamente Fuego por deficiencia; consunción grave exige evaluación urgente.'],
    ['3', 'Match the branch symptom', 'Wasting-thirst points to Yu Quan; lower-body atrophy to Hu Qian; dry constrained pain to Yi Guan; bleeding cough to the Lung formulas; deficient vision with Dampness to Zhu Jing.', 'Empareja el síntoma rama', 'Consunción-sed orienta a Yu Quan; atrofia inferior a Hu Qian; dolor seco constreñido a Yi Guan; tos sanguinolenta a fórmulas pulmonares; visión deficiente con Humedad a Zhu Jing.']
  ],
  'warm-tonify-yang-ch8': [
    ['1', 'Water or exhaustion?', 'Shen Qi Wan regulates water and opening-closing; You Gui Wan deeply rebuilds Yang, essence, and Blood without draining.', '¿Agua o agotamiento?', 'Shen Qi Wan regula agua y apertura-cierre; You Gui Wan reconstruye profundamente Yang, esencia y Sangre sin drenar.'],
    ['2', 'Leakage or fertility?', 'Tu Si Zi Wan is organized around urinary and essence leakage; Zan Yu Dan around infertility or impotence from waning source Fire.', '¿Pérdida o fertilidad?', 'Tu Si Zi Wan se organiza en torno a pérdida urinaria y de esencia; Zan Yu Dan alrededor de infertilidad o impotencia por declive del Fuego fuente.'],
    ['3', 'Apply the Fu Zi boundary', 'All four require professional assessment; Fu Zi-containing formulas must never be improvised or self-decocted.', 'Aplica el límite de Fu Zi', 'Las cuatro requieren evaluación profesional; fórmulas con Fu Zi nunca deben improvisarse ni autodecoctarse.']
  ],
  'tonify-yin-yang-ch8': [
    ['1', 'Identify the dominant layer', 'Di Huang Yin Zi treats deficiency below with Phlegm blocking above; Gui Lu Er Xian Jiao treats pure severe depletion; Qi Bao Mei Ran Dan emphasizes Liver Blood-Kidney Yin; Er Xian Tang treats Cold below with Fire above.', 'Identifica el nivel dominante', 'Di Huang Yin Zi trata deficiencia abajo con Flema bloqueando arriba; Gui Lu Er Xian Jiao agotamiento puro grave; Qi Bao Mei Ran Dan Sangre de Hígado-Yin renal; Er Xian Tang Frío abajo con Fuego arriba.'],
    ['2', 'Separate excess branches', 'Phlegm turbidity and flaring Fire require targeted moving or draining herbs; pure essence depletion has few excess signs and is treated more richly.', 'Separa ramas de exceso', 'Turbidez de Flema y Fuego ascendente requieren hierbas dirigidas a mover o drenar; agotamiento puro de esencia tiene pocos signos de exceso y se trata más ricamente.'],
    ['3', 'Respect urgent mimics', 'Aphasia, paralysis, severe hypertension, palpitations, bleeding, visual loss, or rapid wasting must be evaluated medically before historical pattern treatment.', 'Respeta imitadores urgentes', 'Afasia, parálisis, hipertensión grave, palpitaciones, sangrado, pérdida visual o emaciación rápida deben evaluarse médicamente antes del tratamiento histórico por patrones.']
  ]
};

const referenceItems = {
  'tonify-qi-ch8': [['Liu Jun Zi Tang', 'Si Jun Zi Tang plus Chen Pi and Ban Xia when Phlegm-Dampness complicates Spleen Qi deficiency.', 'Si Jun Zi Tang más Chen Pi y Ban Xia cuando Flema-Humedad complica deficiencia de Qi de Bazo.'], ['Xiang Sha Liu Jun Zi Tang', 'Adds aromatic Qi movers for stronger epigastric stagnation and nausea.', 'Añade movilizadores aromáticos de Qi para mayor estancamiento epigástrico y náusea.']],
  'tonify-blood-ch8': [['Si Wu Tang family', 'The reference base from which warmer, more moving, or organ-specific Blood formulas are differentiated.', 'La base de referencia desde la cual se distinguen fórmulas de Sangre más calientes, movilizantes o específicas de órgano.']],
  'tonify-qi-blood-ch8': [['Eight- and Ten-Treasure families', 'Ba Zhen Tang supplies the base; Shi Quan Da Bu Tang adds Huang Qi and Rou Gui for greater warmth and Qi support.', 'Ba Zhen Tang aporta la base; Shi Quan Da Bu Tang añade Huang Qi y Rou Gui para más calor y apoyo de Qi.'], ['Obstetric boundary', 'Threatened miscarriage, hemorrhage, hypertension, infection, or labor difficulty requires obstetric care.', 'Amenaza de aborto, hemorragia, hipertensión, infección o dificultad del parto requieren atención obstétrica.']],
  'nourish-tonify-yin-ch8': [['Liu Wei Di Huang Wan family', 'Qi Ju, Zhi Bai, Ba Xian Chang Shou, Er Long Zuo Ci, and Ming Mu variants redirect the same core toward eyes, Fire, Lung, ear, or vision patterns.', 'Variantes Qi Ju, Zhi Bai, Ba Xian Chang Shou, Er Long Zuo Ci y Ming Mu redirigen el mismo núcleo hacia ojos, Fuego, Pulmón, oído o visión.'], ['Lung-Yin branches', 'Yue Hua Wan, San Cai Tang, and related formulas adjust the balance among cough, bleeding, Qi deficiency, and essence depletion.', 'Yue Hua Wan, San Cai Tang y fórmulas afines ajustan el equilibrio entre tos, sangrado, deficiencia de Qi y agotamiento de esencia.']],
  'warm-tonify-yang-ch8': [['Shen Qi Wan family', 'Jia Wei Shen Qi Wan emphasizes edema; Shi Bu Wan essence and grasping; Wu Bi Shan Yao Wan direct Kidney Yang and fatigue.', 'Jia Wei Shen Qi Wan enfatiza edema; Shi Bu Wan esencia y captación; Wu Bi Shan Yao Wan Yang renal directo y fatiga.'], ['You Gui family', 'You Gui Yin is weaker and more flexible; Qing E Wan focuses on severe low-back pain.', 'You Gui Yin es más suave y flexible; Qing E Wan se centra en dolor lumbar grave.']],
  'tonify-yin-yang-ch8': [['Pure depletion versus obstruction', 'Gui Lu Er Xian Jiao is the richest pure-depletion strategy; Di Huang Yin Zi adds orifice-opening and Phlegm-transforming herbs.', 'Gui Lu Er Xian Jiao es la estrategia más rica para agotamiento puro; Di Huang Yin Zi añade hierbas que abren orificios y transforman Flema.'], ['Cold below and Fire above', 'Er Xian Tang deliberately combines warming Xian Mao-Yin Yang Huo with cooling Huang Bai-Zhi Mu.', 'Er Xian Tang combina deliberadamente Xian Mao-Yin Yang Huo calientes con Huang Bai-Zhi Mu fríos.']]
};

const guideFor = selected => {
  const formulas = tonifyChapterFormulas.filter(item => item.categoryKey === selected.key);
  return {
    priorityTitle: `Supplementary Chapter 8: ${selected.name.replace(' · Chapter 8', '')}`,
    priorityTitleEs: `Capítulo suplementario 8: ${selected.nameEs.replace(' · Capítulo 8', '')}`,
    priorityText: `These ${formulas.length} principal formulas come from the supplied Chapter 8 source (pages ${selected.sourcePages}). They extend the guide without changing the official 63-formula Board roster or its practice totals.`,
    priorityTextEs: `Estas ${formulas.length} fórmulas principales proceden del capítulo 8 aportado (páginas ${selected.sourcePages}). Amplían la guía sin modificar el listado Board oficial de 63 fórmulas ni sus totales de práctica.`,
    routeTitle: 'Choose by mechanism and decisive sign',
    routeTitleEs: 'Elige por mecanismo y signo decisivo',
    route: guideRoutes[selected.key],
    quickCompareTitle: `${selected.name.replace(' · Chapter 8', '')} principal formulas`,
    quickCompareTitleEs: `Fórmulas principales: ${selected.nameEs.replace(' · Capítulo 8', '')}`,
    quickCompare: formulas.map(item => [item.name, item.pattern, item.clue, item.patternEs, item.clueEs]),
    referencesTitle: 'Associated formulas and safety boundaries',
    referencesTitleEs: 'Fórmulas asociadas y límites de seguridad',
    referencesText: 'Associated branches support differentiation and do not create additional principal cards. Historical ingredients, dosage, preparation, and biomedical red flags require qualified professional judgment.',
    referencesTextEs: 'Las ramas asociadas apoyan la diferenciación y no crean tarjetas principales adicionales. Ingredientes históricos, dosis, preparación y señales biomédicas de alarma requieren juicio profesional cualificado.',
    referenceGroups: [{
      title: 'High-yield branches', titleEs: 'Ramas de alto rendimiento',
      items: referenceItems[selected.key]
    }]
  };
};

export const tonifyChapterCategoryGuides = Object.fromEntries(
  tonifyChapterCategories.map(selected => [selected.key, guideFor(selected)])
);

if (tonifyChapterFormulas.length !== 45) {
  throw new Error(`Unexpected Chapter 8 formula count: ${tonifyChapterFormulas.length}`);
}
