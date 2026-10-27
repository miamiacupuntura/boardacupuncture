export const classicFormulaCategories = [
  ['release-exterior', 'Release the Exterior', 'Liberar el Exterior'],
  ['clear-heat', 'Clear Heat', 'Aclarar Calor'],
  ['purge', 'Purge', 'Purgar'],
  ['harmonize', 'Harmonize', 'Armonizar'],
  ['expel-damp', 'Expel Dampness', 'Eliminar Humedad'],
  ['warm-interior', 'Warm the Interior', 'Calentar el Interior'],
  ['tonify', 'Tonify', 'Tonificar'],
  ['regulate-qi', 'Regulate Qi', 'Regular el Qi'],
  ['invigorate-blood', 'Invigorate Blood', 'Movilizar Sangre'],
  ['stabilize-bind', 'Stabilize and Bind', 'Estabilizar y contener'],
  ['calm-shen', 'Calm Shen', 'Calmar el Shen'],
  ['expel-wind', 'Expel Wind', 'Expulsar Viento'],
  ['transform-phlegm', 'Transform Phlegm', 'Transformar Flema'],
  ['digestive', 'Reduce Food Stagnation', 'Reducir retención de alimentos']
];

export const classicFormulaRoutes = [
  ['respiratory', 'Cough & breathing', 'Tos y respiración'],
  ['febrile', 'Fever & Heat', 'Fiebre y Calor'],
  ['digestive', 'Digestion & abdomen', 'Digestión y abdomen'],
  ['pain', 'Pain & channels', 'Dolor y canales'],
  ['fluids', 'Fluids & urination', 'Líquidos y micción'],
  ['gynecology', 'Gynecology & bleeding', 'Ginecología y sangrado'],
  ['shen', 'Sleep & emotions', 'Sueño y emociones'],
  ['neurologic', 'Dizziness & Internal Wind', 'Mareo y Viento interno']
];

export const classicFormulaOutline = [
  [1, 'Ma Huang Tang', 'release-exterior', 'Ma Huang', ['respiratory', 'febrile']],
  [2, 'Gui Zhi Tang', 'release-exterior', 'Gui Zhi', ['febrile']],
  [3, 'Ge Gen Tang', 'release-exterior', 'Ge Gen', ['febrile', 'pain', 'digestive']],
  [4, 'Sang Ju Yin', 'release-exterior', 'Sang Ye, Ju Hua', ['respiratory', 'febrile']],
  [5, 'Yin Qiao San', 'release-exterior', 'Jin Yin Hua, Lian Qiao', ['febrile', 'respiratory']],
  [6, 'Chai Ge Jie Ji Tang', 'release-exterior', 'Chai Hu, Ge Gen', ['febrile']],
  [7, 'Ren Shen Bai Du San', 'release-exterior', 'Qiang Huo, Du Huo', ['febrile', 'pain']],
  [8, 'Bai Hu Tang', 'clear-heat', 'Shi Gao', ['febrile']],
  [9, 'Huang Lian Jie Du Tang', 'clear-heat', 'Huang Lian', ['febrile']],
  [10, 'Ma Xing Shi Gan Tang', 'clear-heat', 'Ma Huang, Shi Gao', ['respiratory', 'febrile']],
  [11, 'Long Dan Xie Gan Tang', 'clear-heat', 'Long Dan Cao', ['febrile', 'fluids', 'gynecology']],
  [12, 'Qing Hao Bie Jia Tang', 'clear-heat', 'Qing Hao, Bie Jia', ['febrile']],
  [13, 'Da Cheng Qi Tang', 'purge', 'Da Huang', ['digestive', 'febrile']],
  [14, 'Xiao Cheng Qi Tang', 'purge', 'Da Huang', ['digestive', 'febrile']],
  [15, 'Tiao Wei Cheng Qi Tang', 'purge', 'Da Huang', ['digestive', 'febrile']],
  [16, 'Xiao Chai Hu Tang', 'harmonize', 'Chai Hu', ['febrile', 'digestive']],
  [17, 'Si Ni San', 'harmonize', 'Chai Hu', ['digestive', 'pain']],
  [18, 'Xiao Yao San', 'harmonize', 'Chai Hu', ['gynecology', 'shen', 'digestive']],
  [19, 'Ban Xia Xie Xin Tang', 'harmonize', 'Ban Xia', ['digestive']],
  [20, 'Ping Wei San', 'expel-damp', 'Cang Zhu', ['digestive']],
  [21, 'Huo Xiang Zheng Qi San', 'expel-damp', 'Huo Xiang', ['digestive', 'febrile']],
  [22, 'Ba Zheng San', 'expel-damp', 'Mu Tong', ['fluids', 'febrile']],
  [23, 'Wu Ling San', 'expel-damp', 'Ze Xie', ['fluids']],
  [24, 'Zhu Ling Tang', 'expel-damp', 'Zhu Ling', ['fluids', 'febrile']],
  [25, 'Wu Pi San', 'expel-damp', 'Fu Ling Pi', ['fluids']],
  [26, 'Zhen Wu Tang', 'expel-damp', 'Fu Zi', ['fluids', 'digestive']],
  [27, 'Du Huo Ji Sheng Tang', 'expel-damp', 'Du Huo', ['pain']],
  [28, 'Li Zhong Wan', 'warm-interior', 'Gan Jiang', ['digestive']],
  [29, 'Wu Zhu Yu Tang', 'warm-interior', 'Wu Zhu Yu', ['digestive', 'neurologic']],
  [30, 'Da Jian Zhong Tang', 'warm-interior', 'Chuan Jiao', ['digestive', 'pain']],
  [31, 'Si Ni Tang', 'warm-interior', 'Fu Zi', ['digestive']],
  [32, 'Si Jun Zi Tang', 'tonify', 'Ren Shen', ['digestive']],
  [33, 'Bu Zhong Yi Qi Tang', 'tonify', 'Huang Qi', ['digestive']],
  [34, 'Sheng Mai San', 'tonify', 'Ren Shen', ['respiratory', 'fluids']],
  [35, 'Si Wu Tang', 'tonify', 'Shu Di Huang', ['gynecology']],
  [36, 'Gui Pi Tang', 'tonify', 'Ren Shen, Huang Qi, Bai Zhu, Zhi Gan Cao', ['shen', 'gynecology', 'digestive']],
  [37, 'Shi Quan Da Bu Tang', 'tonify', 'Ren Shen, Shu Di Huang', ['digestive']],
  [38, 'Liu Wei Di Huang Wan', 'tonify', 'Shu Di Huang', ['fluids', 'neurologic']],
  [39, 'Jin Gui Shen Qi Wan', 'tonify', 'Sheng Di Huang / Shu Di Huang (según linaje)', ['fluids']],
  [40, 'Yue Ju Wan', 'regulate-qi', 'Xiang Fu', ['digestive', 'shen']],
  [41, 'Ban Xia Hou Po Tang', 'regulate-qi', 'Ban Xia', ['respiratory', 'shen']],
  [42, 'Su Zi Jiang Qi Tang', 'regulate-qi', 'Su Zi', ['respiratory']],
  [43, 'Ding Chuan Tang', 'regulate-qi', 'Ma Huang, Bai Guo', ['respiratory', 'febrile']],
  [44, 'Ju Pi Zhu Ru Tang', 'regulate-qi', 'Chen Pi, Zhu Ru', ['digestive']],
  [45, 'Tao He Cheng Qi Tang', 'invigorate-blood', 'Tao Ren, Da Huang', ['gynecology', 'digestive', 'pain']],
  [46, 'Xue Fu Zhu Yu Tang', 'invigorate-blood', 'Tao Ren, Hong Hua, Chuan Xiong', ['pain']],
  [47, 'Wen Jing Tang', 'invigorate-blood', 'Wu Zhu Yu, Gui Zhi', ['gynecology']],
  [48, 'Gui Zhi Fu Ling Wan', 'invigorate-blood', 'Gui Zhi, Fu Ling', ['gynecology']],
  [49, 'Yu Ping Feng San', 'stabilize-bind', 'Huang Qi', ['respiratory']],
  [50, 'Si Shen Wan', 'stabilize-bind', 'Bu Gu Zhi', ['digestive']],
  [51, 'Gu Jing Wan', 'stabilize-bind', 'Gui Ban, Bai Shao', ['gynecology']],
  [52, 'Suan Zao Ren Tang', 'calm-shen', 'Suan Zao Ren', ['shen']],
  [53, 'Tian Wang Bu Xin Dan', 'calm-shen', 'Sheng Di Huang', ['shen']],
  [54, 'Gan Mai Da Zao Tang', 'calm-shen', 'Fu Xiao Mai', ['shen']],
  [55, 'Xiao Feng San', 'expel-wind', 'Jing Jie, Fang Feng, Niu Bang Zi, Chan Tui', ['pain']],
  [56, 'Xiao Huo Luo Dan', 'expel-wind', 'Zhi Chuan Wu, Zhi Cao Wu', ['pain', 'neurologic']],
  [57, 'Tian Ma Gou Teng Yin', 'expel-wind', 'Tian Ma, Gou Teng, Shi Jue Ming', ['neurologic', 'pain']],
  [58, 'Er Chen Tang', 'transform-phlegm', 'Ban Xia, Ju Hong', ['respiratory', 'digestive']],
  [59, 'Wen Dan Tang', 'transform-phlegm', 'Zhu Ru', ['shen', 'digestive', 'neurologic']],
  [60, 'Bei Mu Gua Lou San', 'transform-phlegm', 'Chuan Bei Mu', ['respiratory']],
  [61, 'Zhi Sou San', 'transform-phlegm', 'Zi Wan, Bai Bu, Bai Qian', ['respiratory']],
  [62, 'Ban Xia Bai Zhu Tian Ma Tang', 'transform-phlegm', 'Ban Xia, Tian Ma', ['neurologic', 'digestive']],
  [63, 'Bao He Wan', 'digestive', 'Shan Zha', ['digestive']]
];

export const classicFormulaFallbacks = {
  'Ma Huang Tang': {
    pattern: 'Exterior Wind-Cold excess with closed pores and Lung-Qi constraint.', patternEs: 'Viento-Frío exterior de exceso con poros cerrados y Qi de Pulmón constreñido.',
    action: 'Induce sweating, release the Exterior, disseminate Lung Qi and calm wheezing.', actionEs: 'Induce sudoración, libera el Exterior, difunde el Qi de Pulmón y calma las sibilancias.',
    clue: 'Strong chills, no sweating, body aches and wheezing with a floating-tight pulse.', clueEs: 'Escalofríos intensos, ausencia de sudor, dolor corporal y sibilancias con pulso flotante-tenso.',
    ingredients: 'Ma Huang, Gui Zhi, Xing Ren, Zhi Gan Cao.', ingredientsEs: 'Ma Huang, Gui Zhi, Xing Ren y Zhi Gan Cao.',
    safety: 'Ma Huang requires cardiovascular, blood-pressure and medication screening; not a self-care stimulant.', safetyEs: 'Ma Huang exige evaluar riesgo cardiovascular, presión arterial y medicamentos; no es un estimulante de autocuidado.'
  },
  'Chai Ge Jie Ji Tang': {
    pattern: 'Unresolved Exterior Wind-Cold constrained in the muscle layer and transforming into Heat.', patternEs: 'Viento-Frío exterior no resuelto, constreñido en la capa muscular y transformándose en Calor.',
    action: 'Release the muscle layer and clear Interior Heat.', actionEs: 'Libera la capa muscular y aclara Calor interior.',
    clue: 'Increasing fever, decreasing chills, thirst, headache and eye or orbital pain.', clueEs: 'Fiebre creciente, escalofríos decrecientes, sed, cefalea y dolor ocular u orbitario.',
    ingredients: 'Chai Hu, Ge Gen, Qiang Huo, Bai Zhi, Huang Qin, Shi Gao, Jie Geng, Bai Shao, Gan Cao, Sheng Jiang, Da Zao.', ingredientsEs: 'Chai Hu, Ge Gen, Qiang Huo, Bai Zhi, Huang Qin, Shi Gao, Jie Geng, Bai Shao, Gan Cao, Sheng Jiang y Da Zao.',
    safety: 'Persistent high fever, confusion, dehydration or eye pain needs biomedical assessment.', safetyEs: 'Fiebre alta persistente, confusión, deshidratación o dolor ocular requiere evaluación biomédica.'
  },
  'Bai Hu Tang': {
    pattern: 'Blazing Yangming/Qi-level Heat with fluid injury.', patternEs: 'Calor intenso de Yangming/nivel Qi con lesión de líquidos.',
    action: 'Clear Qi-level Heat, drain Stomach Fire, generate fluids and relieve thirst.', actionEs: 'Aclara Calor del nivel Qi, drena Fuego de Estómago, genera líquidos y alivia la sed.',
    clue: 'The Four Greats: great fever, great thirst, great sweating and a great surging pulse.', clueEs: 'Los Cuatro Grandes: fiebre, sed y sudoración intensas con pulso grande y desbordante.',
    ingredients: 'Shi Gao, Zhi Mu, Geng Mi and Zhi Gan Cao.', ingredientsEs: 'Shi Gao, Zhi Mu, Geng Mi y Zhi Gan Cao.',
    safety: 'Not for Cold or deficiency presentations; severe fever and altered mental status require urgent care.', safetyEs: 'No corresponde a cuadros de Frío o deficiencia; fiebre intensa y alteración mental requieren atención urgente.'
  },
  'Huang Lian Jie Du Tang': {
    pattern: 'Fire-Toxin obstructing the Three Burners.', patternEs: 'Fuego-Toxina obstruyendo los Tres Calentadores.',
    action: 'Drain Fire and relieve toxicity.', actionEs: 'Drena Fuego y alivia toxicidad.',
    clue: 'High fever, marked irritability, insomnia, delirium or bleeding with a red tongue and rapid-forceful pulse.', clueEs: 'Fiebre alta, irritabilidad marcada, insomnio, delirio o sangrado con lengua roja y pulso rápido-fuerte.',
    ingredients: 'Huang Lian, Huang Qin, Huang Bai and Zhi Zi.', ingredientsEs: 'Huang Lian, Huang Qin, Huang Bai y Zhi Zi.',
    safety: 'A very cold, bitter formula; avoid indiscriminate prolonged use and urgently assess sepsis-like signs.', safetyEs: 'Fórmula muy fría y amarga; evitar uso prolongado indiscriminado y evaluar con urgencia signos semejantes a sepsis.'
  },
  'Qing Hao Bie Jia Tang': {
    pattern: 'Heat smoldering in the Yin level after a febrile disease with depleted Yin and fluids.', patternEs: 'Calor latente en el nivel Yin después de enfermedad febril, con Yin y líquidos agotados.',
    action: 'Nourish Yin and vent hidden Heat.', actionEs: 'Nutre Yin y libera Calor oculto.',
    clue: 'Fever at night that recedes by morning without sweating, emaciation and a red tongue with little coat.', clueEs: 'Fiebre nocturna que cede por la mañana sin sudor, adelgazamiento y lengua roja con poca capa.',
    ingredients: 'Qing Hao, Bie Jia, Sheng Di Huang, Zhi Mu and Mu Dan Pi.', ingredientsEs: 'Qing Hao, Bie Jia, Sheng Di Huang, Zhi Mu y Mu Dan Pi.',
    safety: 'Bie Jia is animal-derived; persistent night fever or weight loss requires medical evaluation.', safetyEs: 'Bie Jia es de origen animal; fiebre nocturna persistente o pérdida de peso requiere evaluación médica.'
  },
  'Da Cheng Qi Tang': {
    pattern: 'Severe Yangming-Fu Heat accumulation with hardness, dryness, fullness and focal distention.', patternEs: 'Acumulación grave de Calor en Yangming-Fu con dureza, sequedad, plenitud y distensión focal.',
    action: 'Vigorously purge Heat accumulation.', actionEs: 'Purga vigorosamente la acumulación de Calor.',
    clue: 'Severe constipation and a hard, full, painful abdomen that resists pressure.', clueEs: 'Estreñimiento intenso y abdomen duro, lleno y doloroso que rechaza la presión.',
    ingredients: 'Da Huang, Mang Xiao, Hou Po and Zhi Shi.', ingredientsEs: 'Da Huang, Mang Xiao, Hou Po y Zhi Shi.',
    safety: 'A strong professional purgative; avoid in pregnancy, frailty, dehydration or suspected obstruction.', safetyEs: 'Purgante fuerte de uso profesional; evitar en embarazo, fragilidad, deshidratación o sospecha de obstrucción.'
  },
  'Xiao Cheng Qi Tang': {
    pattern: 'Milder Yangming-Fu clumping with fullness and focal distention but less dryness.', patternEs: 'Aglomeración más leve de Yangming-Fu con plenitud y distensión focal, pero menos sequedad.',
    action: 'Moderately purge clumped Heat.', actionEs: 'Purga moderadamente el Calor aglomerado.',
    clue: 'Constipation with abdominal fullness and a slippery-rapid pulse, without the severe dryness of Da Cheng Qi Tang.', clueEs: 'Estreñimiento con plenitud abdominal y pulso resbaladizo-rápido, sin la sequedad intensa de Da Cheng Qi Tang.',
    ingredients: 'Da Huang, Hou Po and Zhi Shi.', ingredientsEs: 'Da Huang, Hou Po y Zhi Shi.',
    safety: 'Not a routine laxative; severe abdominal pain, vomiting or inability to pass gas needs urgent assessment.', safetyEs: 'No es un laxante rutinario; dolor abdominal intenso, vómito o incapacidad para expulsar gases requiere evaluación urgente.'
  },
  'Tiao Wei Cheng Qi Tang': {
    pattern: 'Dry Heat in the Stomach and Intestines with mild Yangming-Fu constipation.', patternEs: 'Calor seco en Estómago e Intestinos con estreñimiento leve de Yangming-Fu.',
    action: 'Mildly purge clumped Heat and soften dryness.', actionEs: 'Purga suavemente el Calor aglomerado y ablanda la sequedad.',
    clue: 'Constipation with thirst and irritability but little marked abdominal focal distention.', clueEs: 'Estreñimiento con sed e irritabilidad, pero poca distensión abdominal focal marcada.',
    ingredients: 'Da Huang, Mang Xiao and Zhi Gan Cao.', ingredientsEs: 'Da Huang, Mang Xiao y Zhi Gan Cao.',
    safety: 'Avoid in pregnancy, frailty, dehydration and suspected obstruction.', safetyEs: 'Evitar durante embarazo, fragilidad, deshidratación y sospecha de obstrucción.'
  },
  'Ban Xia Xie Xin Tang': {
    pattern: 'Mixed Cold-Heat and deficiency-excess disharmony in the Middle Burner causing focal epigastric distention.', patternEs: 'Desarmonía mixta Frío-Calor y deficiencia-exceso en el Jiao Medio que produce distensión epigástrica focal.',
    action: 'Harmonize the Stomach, descend rebellious Qi, disperse clumping and eliminate focal distention.', actionEs: 'Armoniza Estómago, desciende Qi rebelde, dispersa aglomeración y elimina distensión focal.',
    clue: 'Painless epigastric focal distention with nausea, borborygmus and diarrhea.', clueEs: 'Distensión epigástrica focal sin dolor, con náusea, borborigmos y diarrea.',
    ingredients: 'Ban Xia, Gan Jiang, Huang Qin, Huang Lian, Ren Shen, Da Zao and Zhi Gan Cao.', ingredientsEs: 'Ban Xia, Gan Jiang, Huang Qin, Huang Lian, Ren Shen, Da Zao y Zhi Gan Cao.',
    safety: 'Ban Xia must be processed; persistent vomiting, bleeding or severe pain requires evaluation.', safetyEs: 'Ban Xia debe estar procesada; vómito persistente, sangrado o dolor intenso requiere evaluación.'
  },
  'Huo Xiang Zheng Qi San': {
    pattern: 'External Wind-Cold with internal Dampness obstructing the Middle Burner.', patternEs: 'Viento-Frío exterior con Humedad interna obstruyendo el Jiao Medio.',
    action: 'Release the Exterior, transform Dampness, regulate Qi and harmonize the Middle.', actionEs: 'Libera el Exterior, transforma Humedad, regula Qi y armoniza el Centro.',
    clue: 'Chills and headache with nausea, vomiting, diarrhea and epigastric fullness.', clueEs: 'Escalofríos y cefalea con náusea, vómito, diarrea y plenitud epigástrica.',
    ingredients: 'Huo Xiang, Hou Po, Chen Pi, Zi Su Ye, Bai Zhi, Ban Xia, Da Fu Pi, Bai Zhu, Fu Ling, Jie Geng, Zhi Gan Cao, Sheng Jiang and Da Zao.', ingredientsEs: 'Huo Xiang, Hou Po, Chen Pi, Zi Su Ye, Bai Zhi, Ban Xia, Da Fu Pi, Bai Zhu, Fu Ling, Jie Geng, Zhi Gan Cao, Sheng Jiang y Da Zao.',
    safety: 'Processed Ban Xia is required; dehydration, bloody stool or persistent fever needs medical care.', safetyEs: 'Ban Xia debe estar procesada; deshidratación, sangre en heces o fiebre persistente requiere atención médica.'
  },
  'Zhu Ling Tang': {
    pattern: 'Water and Heat accumulation in the Bladder with Yin and fluid injury.', patternEs: 'Acumulación de Agua y Calor en Vejiga con lesión de Yin y líquidos.',
    action: 'Promote urination, clear Heat and nourish Yin.', actionEs: 'Promueve micción, aclara Calor y nutre Yin.',
    clue: 'Urinary difficulty with thirst, irritability or insomnia and signs of Heat.', clueEs: 'Dificultad urinaria con sed, irritabilidad o insomnio y signos de Calor.',
    ingredients: 'Zhu Ling, Fu Ling, Ze Xie, Hua Shi and E Jiao.', ingredientsEs: 'Zhu Ling, Fu Ling, Ze Xie, Hua Shi y E Jiao.',
    safety: 'Urinary retention, fever, flank pain or blood in urine needs prompt evaluation.', safetyEs: 'Retención urinaria, fiebre, dolor en flanco o sangre en orina requiere evaluación pronta.'
  },
  'Wu Zhu Yu Tang': {
    pattern: 'Stomach/Liver deficiency-Cold with rebellious Qi or Jueyin vertex headache.', patternEs: 'Frío por deficiencia de Estómago/Hígado con Qi rebelde o cefalea de vértex Jueyin.',
    action: 'Warm Liver and Stomach, descend rebellious Qi and stop vomiting.', actionEs: 'Calienta Hígado y Estómago, desciende Qi rebelde y detiene el vómito.',
    clue: 'Vomiting after eating with vertex headache, cold extremities and a deep-slow pulse.', clueEs: 'Vómito después de comer con cefalea de vértex, extremidades frías y pulso profundo-lento.',
    ingredients: 'Wu Zhu Yu, Sheng Jiang, Ren Shen and Da Zao.', ingredientsEs: 'Wu Zhu Yu, Sheng Jiang, Ren Shen y Da Zao.',
    safety: 'Wu Zhu Yu is potentially toxic and requires verified identity and professional dosing.', safetyEs: 'Wu Zhu Yu es potencialmente tóxica y exige identidad verificada y dosificación profesional.'
  },
  'Da Jian Zhong Tang': {
    pattern: 'Middle-Burner Yang deficiency with vigorous internal Yin-Cold.', patternEs: 'Deficiencia de Yang del Jiao Medio con Frío-Yin interno vigoroso.',
    action: 'Warm and tonify the Middle, descend rebellious Qi and relieve pain.', actionEs: 'Calienta y tonifica el Centro, desciende Qi rebelde y alivia dolor.',
    clue: 'Severe episodic abdominal cold pain with vomiting and cold extremities, improved by warmth.', clueEs: 'Dolor abdominal frío intenso y episódico con vómito y extremidades frías, mejorado por calor.',
    ingredients: 'Chuan Jiao, Gan Jiang, Ren Shen and Yi Tang.', ingredientsEs: 'Chuan Jiao, Gan Jiang, Ren Shen y Yi Tang.',
    safety: 'Severe abdominal pain can be a surgical emergency; Yi Tang also requires dietary/glucose review.', safetyEs: 'El dolor abdominal intenso puede ser una urgencia quirúrgica; Yi Tang también requiere revisar dieta y glucosa.'
  },
  'Sheng Mai San': {
    pattern: 'Qi and Yin deficiency, especially of the Lung, with fluid loss.', patternEs: 'Deficiencia de Qi y Yin, especialmente de Pulmón, con pérdida de líquidos.',
    action: 'Augment Qi, generate fluids, preserve Yin and stop excessive sweating.', actionEs: 'Aumenta Qi, genera líquidos, preserva Yin y detiene sudoración excesiva.',
    clue: 'Shortness of breath, spontaneous sweating, thirst and a weak-thready pulse.', clueEs: 'Disnea, sudoración espontánea, sed y pulso débil-fino.',
    ingredients: 'Ren Shen, Mai Men Dong and Wu Wei Zi.', ingredientsEs: 'Ren Shen, Mai Men Dong y Wu Wei Zi.',
    safety: 'Shortness of breath, chest pain, fainting or heat illness requires urgent biomedical care.', safetyEs: 'Disnea, dolor torácico, desmayo o enfermedad por calor requiere atención biomédica urgente.'
  },
  'Su Zi Jiang Qi Tang': {
    pattern: 'Cold-Phlegm excess above with Kidney deficiency below failing to grasp Qi.', patternEs: 'Flema-Frío en exceso arriba con deficiencia de Riñón abajo que no capta el Qi.',
    action: 'Descend rebellious Lung Qi, calm wheezing and warm-transform Cold-Phlegm.', actionEs: 'Desciende Qi rebelde de Pulmón, calma sibilancias y calienta-transforma Flema-Frío.',
    clue: 'Watery sputum, difficult inhalation and low-back weakness with a white-greasy tongue.', clueEs: 'Esputo acuoso, inhalación difícil y debilidad lumbar con lengua blanca-grasosa.',
    ingredients: 'Su Zi, Ban Xia, Hou Po, Qian Hu, Chen Pi, Rou Gui, Dang Gui, Sheng Jiang, Su Ye, Zhi Gan Cao and Da Zao.', ingredientsEs: 'Su Zi, Ban Xia, Hou Po, Qian Hu, Chen Pi, Rou Gui, Dang Gui, Sheng Jiang, Su Ye, Zhi Gan Cao y Da Zao.',
    safety: 'Processed Ban Xia is required; acute respiratory distress requires emergency treatment.', safetyEs: 'Ban Xia debe estar procesada; dificultad respiratoria aguda requiere tratamiento de emergencia.'
  },
  'Tao He Cheng Qi Tang': {
    pattern: 'Blood stasis and Heat accumulation in the Lower Burner.', patternEs: 'Estasis de Sangre y acumulación de Calor en el Jiao Inferior.',
    action: 'Drain Heat and break Blood stasis.', actionEs: 'Drena Calor y rompe estasis de Sangre.',
    clue: 'Severe fixed lower-abdominal pain with Heat, agitation and a purple tongue.', clueEs: 'Dolor hipogástrico fijo intenso con Calor, agitación y lengua violácea.',
    ingredients: 'Tao Ren, Da Huang, Gui Zhi, Mang Xiao and Zhi Gan Cao.', ingredientsEs: 'Tao Ren, Da Huang, Gui Zhi, Mang Xiao y Zhi Gan Cao.',
    safety: 'Contraindicated in pregnancy; strong Blood-moving and purgative formula for professional use.', safetyEs: 'Contraindicada en embarazo; fórmula movilizadora de Sangre y purgante fuerte de uso profesional.'
  },
  'Gu Jing Wan': {
    pattern: 'Constrained Liver Fire injuring Chong and Ren on a background of Yin deficiency.', patternEs: 'Fuego constreñido de Hígado lesionando Chong y Ren sobre deficiencia de Yin.',
    action: 'Nourish Yin, clear Heat, stop bleeding and stabilize the menses.', actionEs: 'Nutre Yin, aclara Calor, detiene sangrado y estabiliza la menstruación.',
    clue: 'Persistent or profuse bright-red uterine bleeding with Heat signs and a red tongue.', clueEs: 'Sangrado uterino rojo brillante, persistente o profuso, con signos de Calor y lengua roja.',
    ingredients: 'Gui Ban, Bai Shao, Huang Qin, Huang Bai, Xiang Fu and Chun Gen Pi.', ingredientsEs: 'Gui Ban, Bai Shao, Huang Qin, Huang Bai, Xiang Fu y Chun Gen Pi.',
    safety: 'Heavy, gestational or postmenopausal bleeding requires urgent medical evaluation; Gui Ban is animal-derived.', safetyEs: 'Sangrado abundante, gestacional o posmenopáusico requiere evaluación médica urgente; Gui Ban es de origen animal.'
  },
  'Tian Wang Bu Xin Dan': {
    pattern: 'Heart-Kidney Yin and Blood deficiency with deficient Fire disturbing Shen.', patternEs: 'Deficiencia de Yin y Sangre de Corazón-Riñón con Fuego por deficiencia perturbando el Shen.',
    action: 'Enrich Yin, nourish Blood, tonify Heart and calm Shen.', actionEs: 'Enriquece Yin, nutre Sangre, tonifica Corazón y calma el Shen.',
    clue: 'Restless insomnia, palpitations, night sweats, dryness and a red tongue with little coat.', clueEs: 'Insomnio inquieto, palpitaciones, sudor nocturno, sequedad y lengua roja con poca capa.',
    ingredients: 'Sheng Di Huang, Dan Shen, Dang Gui, Bai Zi Ren, Yuan Zhi, Ren Shen, Fu Ling, Tian Men Dong, Mai Men Dong, Xuan Shen, Wu Wei Zi, Suan Zao Ren, Jie Geng; historical versions may include Zhu Sha.', ingredientsEs: 'Sheng Di Huang, Dan Shen, Dang Gui, Bai Zi Ren, Yuan Zhi, Ren Shen, Fu Ling, Tian Men Dong, Mai Men Dong, Xuan Shen, Wu Wei Zi, Suan Zao Ren y Jie Geng; versiones históricas pueden incluir Zhu Sha.',
    safety: 'Historical Zhu Sha contains mercury and is prohibited or restricted for internal use in many jurisdictions.', safetyEs: 'El Zhu Sha histórico contiene mercurio y está prohibido o restringido para uso interno en muchas jurisdicciones.'
  },
  'Gan Mai Da Zao Tang': {
    pattern: 'Heart nourishment/Yin deficiency with Liver constraint causing classical Zang Zao.', patternEs: 'Desnutrición o deficiencia de Yin de Corazón con constricción de Hígado que causa Zang Zao clásico.',
    action: 'Nourish Heart, calm Shen and harmonize the Middle.', actionEs: 'Nutre Corazón, calma el Shen y armoniza el Jiao Medio.',
    clue: 'Frequent crying, emotional lability, restlessness and yawning.', clueEs: 'Llanto frecuente, labilidad emocional, inquietud y bostezos.',
    ingredients: 'Fu Xiao Mai, Gan Cao and Da Zao.', ingredientsEs: 'Fu Xiao Mai, Gan Cao y Da Zao.',
    safety: 'Contains wheat and substantial licorice; review gluten allergy/celiac disease and licorice interactions.', safetyEs: 'Contiene trigo y una cantidad importante de regaliz; revisar alergia/celiaquía e interacciones de Gan Cao.'
  },
  'Xiao Huo Luo Dan': {
    pattern: 'Wind-Cold-Dampness and Phlegm obstructing channels with Blood stasis.', patternEs: 'Viento-Frío-Humedad y Flema obstruyendo canales con estasis de Sangre.',
    action: 'Warm channels, transform Phlegm, invigorate Blood and open collaterals.', actionEs: 'Calienta canales, transforma Flema, moviliza Sangre y abre colaterales.',
    clue: 'Severe cold-aggravated joint or channel pain with numbness and reduced movement.', clueEs: 'Dolor intenso articular o de canales agravado por frío, con entumecimiento y movimiento reducido.',
    ingredients: 'Zhi Cao Wu, Zhi Chuan Wu, Tian Nan Xing, Mo Yao, Ru Xiang and Di Long.', ingredientsEs: 'Zhi Cao Wu, Zhi Chuan Wu, Tian Nan Xing, Mo Yao, Ru Xiang y Di Long.',
    safety: 'High-risk formula containing processed aconites and processed toxic Tian Nan Xing; specialist use only.', safetyEs: 'Fórmula de alto riesgo con aconitos procesados y Tian Nan Xing tóxico procesado; solo uso especializado.'
  },
  'Tian Ma Gou Teng Yin': {
    pattern: 'Liver-Yang rising with Internal Wind over Liver-Kidney Yin deficiency.', patternEs: 'Ascenso de Yang de Hígado con Viento interno sobre deficiencia de Yin de Hígado-Riñón.',
    action: 'Calm Liver, extinguish Wind, clear Heat, invigorate Blood and support Liver-Kidney.', actionEs: 'Calma Hígado, extingue Viento, aclara Calor, moviliza Sangre y apoya Hígado-Riñón.',
    clue: 'Headache, vertigo, tinnitus and insomnia with a red-yellow tongue and wiry-rapid pulse.', clueEs: 'Cefalea, vértigo, tinnitus e insomnio con lengua roja-amarilla y pulso de cuerda-rápido.',
    ingredients: 'Tian Ma, Gou Teng, Shi Jue Ming, Zhi Zi, Huang Qin, Yi Mu Cao, Chuan Niu Xi, Du Zhong, Sang Ji Sheng, Ye Jiao Teng and Fu Shen.', ingredientsEs: 'Tian Ma, Gou Teng, Shi Jue Ming, Zhi Zi, Huang Qin, Yi Mu Cao, Chuan Niu Xi, Du Zhong, Sang Ji Sheng, Ye Jiao Teng y Fu Shen.',
    safety: 'Stroke symptoms or hypertensive emergency signs require emergency care; not a substitute for acute treatment.', safetyEs: 'Signos de accidente cerebrovascular o emergencia hipertensiva requieren urgencias; no sustituye tratamiento agudo.'
  }
};

export const classicFormulaSafetyTiers = {
  'Xiao Huo Luo Dan': 'critical',
  'Tian Wang Bu Xin Dan': 'critical',
  'Si Ni Tang': 'high',
  'Zhen Wu Tang': 'high',
  'Jin Gui Shen Qi Wan': 'high',
  'Ma Huang Tang': 'high',
  'Ma Xing Shi Gan Tang': 'high',
  'Ding Chuan Tang': 'high',
  'Da Cheng Qi Tang': 'high',
  'Tao He Cheng Qi Tang': 'high'
};
