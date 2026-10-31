export const summerheatCategory = {
  number: 'S1',
  key: 'dispel-summerheat',
  name: 'Dispel Summerheat',
  nameEs: 'Eliminar Calor de Verano',
  sourceChapter: 5,
  sourcePages: '231–248'
};

export const summerheatFormulas = [
  {
    number: 'S1', sequence: 1, name: 'Qing Luo Yin', englishName: 'Clear the Collaterals Drink',
    categoryKey: 'dispel-summerheat', group: 'Dispel Summerheat and Clear Heat',
    groupEs: 'Eliminar Calor de Verano y aclarar Calor', sourcePages: '232–233',
    kings: 'Xian Jin Yin Hua, Xian He Ye',
    pattern: 'Mild Summerheat injuring the Qi level of the Lung channel, or a Summerheat-warmth pathogen not fully released by sweating.',
    patternEs: 'Calor de Verano leve que lesiona el nivel Qi del canal de Pulmón, o un patógeno Calor de Verano-Calor no liberado por completo mediante el sudor.',
    action: 'Resolves Summerheat and clears the Lungs.',
    actionEs: 'Resuelve Calor de Verano y aclara los Pulmones.',
    clue: 'Fever, mild thirst, unclear head and vision, light-headedness, slight head distention, and a pink tongue with a thin white coat.',
    clueEs: 'Fiebre, sed leve, cabeza y visión poco claras, mareo, ligera distensión cefálica y lengua rosada con saburra blanca fina.',
    ingredients: 'Xian Jin Yin Hua 6 g, Xian Bian Dou Hua 6 g, Xi Gua 6 g, Si Gua Luo 6 g, Xian He Ye 6 g, Xian Dan Zhu Ye 6 g.',
    ingredientsEs: 'Xian Jin Yin Hua 6 g, Xian Bian Dou Hua 6 g, Xi Gua 6 g, Si Gua Luo 6 g, Xian He Ye 6 g y Xian Dan Zhu Ye 6 g.',
    safety: 'A mild, light formula for a relatively superficial presentation. High fever, altered consciousness, dehydration, or suspected heat stroke requires urgent medical assessment.',
    safetyEs: 'Fórmula suave y ligera para un cuadro relativamente superficial. Fiebre alta, alteración de conciencia, deshidratación o sospecha de golpe de calor requieren evaluación médica urgente.',
    safetyTier: 'standard',
    examFocus: 'Think light and superficial: mild Heat plus slight Dampness expressed mainly through head symptoms.',
    examFocusEs: 'Piensa en un cuadro ligero y superficial: Calor leve más Humedad ligera expresada principalmente en síntomas de la cabeza.',
    ingredientRoles: 'Fresh flowers, leaves and peels gently vent lingering Heat from the Lung collaterals; Xi Gua generates fluids; Si Gua Luo opens the collaterals; Dan Zhu Ye clears the Heart and facilitates urination.',
    ingredientRolesEs: 'Flores, hojas y cáscaras frescas ventilan suavemente el Calor residual de los colaterales de Pulmón; Xi Gua genera líquidos; Si Gua Luo abre colaterales; Dan Zhu Ye aclara Corazón y facilita la orina.',
    comparison: 'Unlike Bai Hu Tang, it does not use heavy, intensely cold substances; unlike Liu Yi San, it does not primarily drain Heat through urination.',
    comparisonEs: 'A diferencia de Bai Hu Tang, no utiliza sustancias pesadas e intensamente frías; a diferencia de Liu Yi San, no drena principalmente el Calor mediante la orina.',
    modifications: [
      ['Nonproductive, clear, high-pitched cough', 'Add Gan Cao, Jie Geng, Mai Men Dong, and Xing Ren.', 'Tos improductiva, clara y de tono alto', 'Añadir Gan Cao, Jie Geng, Mai Men Dong y Xing Ren.'],
      ['Exterior Summerheat with interior Dampness, fever/chills, no thirst, and vomiting of blood', 'Add Xing Ren, Hua Shi, and Yi Yi Ren.', 'Calor de Verano exterior con Humedad interior, fiebre/escalofríos, sin sed y vómito de sangre', 'Añadir Xing Ren, Hua Shi y Yi Yi Ren.']
    ],
    associated: [
      ['Wu Ye Lu Gen Tang', 'More pronounced upper-burner Damp-Heat with chest/epigastric oppression and poor appetite.', 'Humedad-Calor más marcada en el Jiao Superior con opresión torácica/epigástrica y poco apetito.']
    ]
  },
  {
    number: 'S2', sequence: 2, name: 'Xiang Ru San', englishName: 'Mosla Powder',
    categoryKey: 'dispel-summerheat', group: 'Dispel Summerheat and Resolve the Exterior',
    groupEs: 'Eliminar Calor de Verano y resolver el Exterior', sourcePages: '234–236',
    kings: 'Xiang Ru',
    pattern: 'Exterior Cold with interior Dampness contracted in summer—the yin or Cold type of Summerheat.',
    patternEs: 'Frío exterior con Humedad interior contraídos en verano—el tipo yin o frío de Calor de Verano.',
    action: 'Releases the Exterior, scatters Cold, transforms Dampness, and harmonizes the Middle Burner.',
    actionEs: 'Libera el Exterior, dispersa Frío, transforma Humedad y armoniza el Jiao Medio.',
    clue: 'Chills with warm skin, no sweating, heavy painful head, abdominal pain, vomiting or diarrhea, chest oppression, white greasy coat, and a floating pulse.',
    clueEs: 'Escalofríos con piel caliente, ausencia de sudor, cabeza pesada y dolorosa, dolor abdominal, vómitos o diarrea, opresión torácica, saburra blanca grasosa y pulso flotante.',
    ingredients: 'Xiang Ru 9–12 g, Chao Bai Bian Dou 6–9 g, Jiang Hou Po 6–9 g.',
    ingredientsEs: 'Xiang Ru 9–12 g, Chao Bai Bian Dou 6–9 g y Jiang Hou Po 6–9 g.',
    safety: 'Use only for exterior Cold with interior Dampness. It can aggravate yang-type Summerheat and damage Qi and fluids. The cooled preparation is traditionally preferred because its aroma may provoke vomiting when warm.',
    safetyEs: 'Usar solo para Frío exterior con Humedad interior. Puede agravar el Calor de Verano tipo yang y lesionar Qi y líquidos. Tradicionalmente se prefiere la preparación fría porque su aroma puede provocar vómito si se toma caliente.',
    safetyTier: 'high',
    examFocus: 'Summer exposure does not automatically mean a Heat formula: no sweat, chills, white greasy coat, and digestive Dampness identify the Cold type.',
    examFocusEs: 'La exposición veraniega no implica automáticamente una fórmula fría: ausencia de sudor, escalofríos, saburra blanca grasosa y Humedad digestiva identifican el tipo frío.',
    ingredientRoles: 'Xiang Ru releases exterior Cold and disperses Summerheat; Bai Bian Dou strengthens the Spleen and transforms Dampness; Hou Po moves Qi, dries Dampness, and relieves fullness.',
    ingredientRolesEs: 'Xiang Ru libera Frío exterior y dispersa Calor de Verano; Bai Bian Dou fortalece Bazo y transforma Humedad; Hou Po mueve Qi, seca Humedad y alivia plenitud.',
    comparison: 'Versus Xin Jia Xiang Ru Yin: Xiang Ru San has a larger warming-dispering emphasis and treats Cold injuring the Spleen; Xin Jia Xiang Ru Yin uses lighter cooling deputies for stronger Heat lodged in the Lung.',
    comparisonEs: 'Frente a Xin Jia Xiang Ru Yin: Xiang Ru San enfatiza más la dispersión tibia y trata Frío que lesiona Bazo; Xin Jia Xiang Ru Yin usa asistentes ligeros y frescos para Calor más intenso alojado en Pulmón.',
    modifications: [
      ['Stronger Exterior presentation', 'Add Qing Hao.', 'Cuadro Exterior más fuerte', 'Añadir Qing Hao.'],
      ['Severe Cold with nasal obstruction', 'Combine with Cong Chi Tang.', 'Frío intenso con obstrucción nasal', 'Combinar con Cong Chi Tang.']
    ],
    variations: [
      ['Si Wei Xiang Ru Yin', 'Add ginger-fried Huang Lian when interior Dampness has transformed into Heat with thirst and irritability.', 'Añade Huang Lian frito con jengibre cuando la Humedad interior se transforma en Calor con sed e irritabilidad.'],
      ['Wu Wu Xiang Ru Yin', 'Add Fu Ling and Gan Cao for severe interior Dampness with abdominal distention and diarrhea.', 'Añade Fu Ling y Gan Cao para Humedad interior intensa con distensión abdominal y diarrea.'],
      ['Liu Wei Xiang Ru Yin', 'Add Mu Gua to the preceding variation when calf stiffness is also present.', 'Añade Mu Gua a la variación anterior cuando también existe rigidez de pantorrillas.'],
      ['Huang Lian Xiang Ru Yin', 'For sudden turmoil after cold food or drink with spasmodic diarrhea, vomiting, central abdominal pain, cold sweat, and rigid extremities.', 'Para trastorno súbito tras alimentos o bebidas frías con diarrea espasmódica, vómitos, dolor abdominal central, sudor frío y extremidades rígidas.'],
      ['Shi Wei Xiang Ru Yin', 'Adds Qi-tonifying herbs for preexisting Spleen deficiency and Dampness with an Exterior pathogen.', 'Añade tónicos de Qi para deficiencia previa de Bazo y Humedad con un patógeno Exterior.']
    ]
  },
  {
    number: 'S3', sequence: 3, name: 'Xin Jia Xiang Ru Yin', englishName: 'Newly Augmented Mosla Drink',
    categoryKey: 'dispel-summerheat', group: 'Dispel Summerheat and Resolve the Exterior',
    groupEs: 'Eliminar Calor de Verano y resolver el Exterior', sourcePages: '237–238',
    kings: 'Xiang Ru',
    pattern: 'Severe early-stage Summerheat-Dampness in the Lung greater yin with superimposed Cold fettering the Exterior.',
    patternEs: 'Calor de Verano-Humedad grave en etapa inicial en el Taiyin de Pulmón con Frío superpuesto que encadena el Exterior.',
    action: 'Dispels Summerheat, releases the Exterior, clears Heat, and transforms Dampness.',
    actionEs: 'Elimina Calor de Verano, libera el Exterior, aclara Calor y transforma Humedad.',
    clue: 'Fever and chills without sweating, thirst, flushed face, chest oppression, heavy aching body, dark rough urination, red tongue with white greasy coat, flooding right pulse, and small left pulse.',
    clueEs: 'Fiebre y escalofríos sin sudor, sed, rostro enrojecido, opresión torácica, cuerpo pesado y dolorido, orina oscura y áspera, lengua roja con saburra blanca grasosa, pulso derecho desbordante e izquierdo pequeño.',
    ingredients: 'Xiang Ru 6 g, Xian Bian Dou Hua 9 g, Bai Bian Dou 9 g, Hou Po 6 g, Jin Yin Hua 9 g, Lian Qiao 6 g.',
    ingredientsEs: 'Xiang Ru 6 g, Xian Bian Dou Hua 9 g, Bai Bian Dou 9 g, Hou Po 6 g, Jin Yin Hua 9 g y Lian Qiao 6 g.',
    safety: 'Intended to induce sweating. Do not use when the patient is already sweating; discontinue once sweating occurs. Take cold and avoid long-term use. Severe heat illness requires urgent assessment.',
    safetyEs: 'Busca inducir sudor. No usar si el paciente ya está sudando; suspender cuando aparezca el sudor. Se toma fría y no se recomienda uso prolongado. La enfermedad grave por calor requiere evaluación urgente.',
    safetyTier: 'high',
    examFocus: 'The paradox is decisive: strong interior Heat and thirst coexist with chills, no sweat, and a white greasy coat because Cold traps the Summerheat-Dampness.',
    examFocusEs: 'La paradoja es decisiva: Calor interior fuerte y sed coexisten con escalofríos, ausencia de sudor y saburra blanca grasosa porque el Frío atrapa Calor de Verano-Humedad.',
    ingredientRoles: 'Xiang Ru lightly opens the Exterior; Jin Yin Hua, Lian Qiao, and fresh Bian Dou Hua vent and clear Summerheat; Bai Bian Dou transforms Dampness; Hou Po moves Qi and balances the cooling herbs.',
    ingredientRolesEs: 'Xiang Ru abre ligeramente el Exterior; Jin Yin Hua, Lian Qiao y Bian Dou Hua fresca ventilan y aclaran Calor de Verano; Bai Bian Dou transforma Humedad; Hou Po mueve Qi y equilibra las hierbas frescas.',
    comparison: 'Xiang Ru San treats more Cold and Middle-Burner injury; this formula treats stronger Heat in the Lung with a smaller Xiang Ru dose and added light cooling herbs.',
    comparisonEs: 'Xiang Ru San trata más Frío y lesión del Jiao Medio; esta fórmula trata Calor más fuerte en Pulmón con menor dosis de Xiang Ru y hierbas ligeras y frescas añadidas.',
    modifications: [
      ['More severe fever', 'Add Qing Hao and Hua Shi.', 'Fiebre más intensa', 'Añadir Qing Hao y Hua Shi.'],
      ['Heat flaring in the interior', 'Add Da Huang.', 'Calor que se exacerba en el Interior', 'Añadir Da Huang.'],
      ['More pronounced Dampness', 'Add Huo Xiang and Fu Ling.', 'Humedad más pronunciada', 'Añadir Huo Xiang y Fu Ling.']
    ]
  },
  {
    number: 'S4', sequence: 4, name: 'Liu Yi San', englishName: 'Six-to-One Powder',
    categoryKey: 'dispel-summerheat', group: 'Dispel Summerheat and Facilitate the Resolution of Dampness',
    groupEs: 'Eliminar Calor de Verano y facilitar la resolución de Humedad', sourcePages: '238–240',
    kings: 'Hua Shi',
    pattern: 'Summerheat with Dampness in the interior obstructing the Triple Burner and water pathways.',
    patternEs: 'Calor de Verano con Humedad interior que obstruye el Triple Burner y las vías del agua.',
    action: 'Clears Summerheat, resolves Dampness, and augments the Qi.',
    actionEs: 'Aclara Calor de Verano, resuelve Humedad y fortalece el Qi.',
    clue: 'Fever, sweating, thirst, irritability, urinary difficulty, diarrhea, nausea or vomiting, thin yellow greasy coat, and a soggy rapid pulse.',
    clueEs: 'Fiebre, sudor, sed, irritabilidad, dificultad urinaria, diarrea, náuseas o vómitos, saburra amarilla fina y grasosa y pulso empapado y rápido.',
    ingredients: 'Hua Shi six parts; Gan Cao one part.',
    ingredientsEs: 'Hua Shi seis partes y Gan Cao una parte.',
    safety: 'May injure Qi and fluids. Use caution in weak, elderly, or Yin-deficient patients. Contraindicated with copious clear urine or Summerheat without Dampness.',
    safetyEs: 'Puede lesionar Qi y líquidos. Usar con cautela en pacientes débiles, ancianos o con deficiencia de Yin. Contraindicada con orina clara abundante o Calor de Verano sin Humedad.',
    safetyTier: 'high',
    examFocus: 'The 6:1 name is the architecture: Hua Shi drains Summerheat-Dampness through urination while a small amount of Gan Cao protects the Middle and fluids.',
    examFocusEs: 'El nombre 6:1 expresa la arquitectura: Hua Shi drena Calor de Verano-Humedad mediante la orina mientras una pequeña cantidad de Gan Cao protege el Centro y los líquidos.',
    ingredientRoles: 'Heavy, slippery Hua Shi clears Heat throughout the Triple Burner and opens the urinary outlet; Gan Cao harmonizes the Middle, mildly clears Heat, and limits injury to normal Qi and fluids.',
    ingredientRolesEs: 'Hua Shi, pesada y resbaladiza, aclara Calor en todo el Triple Burner y abre la salida urinaria; Gan Cao armoniza el Centro, aclara suavemente Calor y limita la lesión del Qi y los líquidos normales.',
    comparison: 'Versus Gui Ling Gan Lu Yin: Liu Yi San is milder and suits Dampness-predominant disease; Gui Ling Gan Lu Yin is larger and treats more severe combined Heat and Dampness involving Stomach and Intestines.',
    comparisonEs: 'Frente a Gui Ling Gan Lu Yin: Liu Yi San es más suave y corresponde a cuadros donde predomina Humedad; Gui Ling Gan Lu Yin es mayor y trata Calor-Humedad combinados más graves que afectan Estómago e Intestinos.',
    modifications: [
      ['Severe Summerheat', 'Add Xi Gua Shuang and Dan Zhu Ye.', 'Calor de Verano grave', 'Añadir Xi Gua Shuang y Dan Zhu Ye.'],
      ['Severe thirst with a red tongue', 'Add Mai Men Dong, Sha Shen, Shi Hu, and Zhi Mu.', 'Sed intensa con lengua roja', 'Añadir Mai Men Dong, Sha Shen, Shi Hu y Zhi Mu.'],
      ['Injury to Qi and fluids', 'Add Xi Yang Shen.', 'Lesión de Qi y líquidos', 'Añadir Xi Yang Shen.'],
      ['Stony painful urinary dysfunction', 'Add Jin Qian Cao, Ji Nei Jin, and Hai Jin Sha.', 'Disfunción urinaria dolorosa por cálculos', 'Añadir Jin Qian Cao, Ji Nei Jin y Hai Jin Sha.']
    ],
    variations: [
      ['Ji Su San', 'Add Bo He for a concurrent Exterior condition with chills, draft aversion, distending headache, and cough.', 'Añade Bo He para un cuadro Exterior concurrente con escalofríos, aversión a corrientes, cefalea distensiva y tos.'],
      ['Yi Yuan San', 'Traditional Zhu Sha variation for Heart Heat and disturbed Spirit; Zhu Sha is obsolete because of mercury and must not be used.', 'Variación tradicional con Zhu Sha para Calor de Corazón y Shen alterado; Zhu Sha es obsoleta por su mercurio y no debe utilizarse.'],
      ['Bi Yu San', 'Add Qing Dai for concurrent constrained Liver/Gallbladder Heat with red eyes, sore throat, or mouth sores.', 'Añade Qing Dai para Calor constreñido concurrente de Hígado/Vesícula con ojos rojos, dolor de garganta o úlceras bucales.']
    ],
    associated: [
      ['Lei Shi Qing Liang Di Shu Fa', 'Stronger urinary drainage and Heart-Heat clearing for more severe Summerheat.', 'Mayor drenaje urinario y aclaramiento de Calor de Corazón para Calor de Verano más grave.']
    ]
  },
  {
    number: 'S5', sequence: 5, name: 'Gui Ling Gan Lu Yin', englishName: 'Cinnamon and Poria Sweet Dew Drink',
    categoryKey: 'dispel-summerheat', group: 'Dispel Summerheat and Facilitate the Resolution of Dampness',
    groupEs: 'Eliminar Calor de Verano y facilitar la resolución de Humedad', sourcePages: '241–242',
    kings: 'Hua Shi',
    pattern: 'Summerheat with internal stagnation of water and Dampness; Heat is equal to or more pronounced than Dampness.',
    patternEs: 'Calor de Verano con estancamiento interno de agua y Humedad; el Calor es igual o más pronunciado que la Humedad.',
    action: 'Expels Summerheat, clears Heat, transforms Qi, and promotes the resolution of Dampness.',
    actionEs: 'Expulsa Calor de Verano, aclara Calor, transforma Qi y favorece la resolución de Humedad.',
    clue: 'Fever, headache, irritability, thirst, scant difficult urination, or severe sudden vomiting and diarrhea with abdominal fullness, pain, or oppression.',
    clueEs: 'Fiebre, cefalea, irritabilidad, sed, micción escasa y difícil o vómitos y diarrea súbitos graves con plenitud, dolor u opresión abdominal.',
    ingredients: 'Hua Shi 120 g, Zhi Gan Cao 60 g, Shi Gao 60 g, Han Shui Shi 60 g, Rou Gui 15 g, Zhu Ling 15 g, Fu Ling 30 g, Ze Xie 30 g, Bai Zhu 15 g (source proportions).',
    ingredientsEs: 'Hua Shi 120 g, Zhi Gan Cao 60 g, Shi Gao 60 g, Han Shui Shi 60 g, Rou Gui 15 g, Zhu Ling 15 g, Fu Ling 30 g, Ze Xie 30 g y Bai Zhu 15 g (proporciones de la fuente).',
    safety: 'For substantial Summerheat-Dampness when Heat is at least as strong as Dampness. Not for mild cases or Dampness-predominant patterns. Severe dehydration, persistent vomiting/diarrhea, or altered consciousness needs urgent assessment.',
    safetyEs: 'Para Calor de Verano-Humedad importante cuando el Calor es al menos tan fuerte como la Humedad. No corresponde a casos leves ni cuadros donde predomina Humedad. Deshidratación grave, vómitos/diarrea persistentes o alteración de conciencia requieren evaluación urgente.',
    safetyTier: 'high',
    examFocus: 'Compared with Liu Yi San, both Heat and Dampness are severe and the Stomach/Intestines are involved; the formula combines three cooling minerals with water-regulating herbs.',
    examFocusEs: 'Comparada con Liu Yi San, tanto Calor como Humedad son graves y participan Estómago/Intestinos; combina tres minerales fríos con hierbas que regulan agua.',
    ingredientRoles: 'Hua Shi, Shi Gao, and Han Shui Shi clear and direct Heat downward; Zhu Ling, Fu Ling, and Ze Xie promote urination; Bai Zhu strengthens the Spleen; Rou Gui restores lower-burner Qi transformation and balances the cold minerals.',
    ingredientRolesEs: 'Hua Shi, Shi Gao y Han Shui Shi aclaran y dirigen Calor hacia abajo; Zhu Ling, Fu Ling y Ze Xie favorecen la orina; Bai Zhu fortalece Bazo; Rou Gui restaura la transformación del Qi del Jiao Inferior y equilibra los minerales fríos.',
    comparison: 'Liu Yi San is a two-ingredient, milder Triple-Burner/Bladder formula; Gui Ling Gan Lu Yin addresses more severe Heat-Dampness and sudden turmoil involving the digestive tract.',
    comparisonEs: 'Liu Yi San es una fórmula más suave de dos ingredientes para Triple Burner/Vejiga; Gui Ling Gan Lu Yin aborda Calor-Humedad más grave y trastorno súbito que afecta el tracto digestivo.',
    associated: [
      ['Gui Ling Gan Lu Yin from Confucians’ Duties', 'Adds Ren Shen, Huo Xiang, Ge Gen, and Mu Xiang for more severe Spleen deficiency with lurking Summerheat.', 'Añade Ren Shen, Huo Xiang, Ge Gen y Mu Xiang para deficiencia de Bazo más grave con Calor de Verano latente.'],
      ['San Shi Tang', 'Less potent for draining Dampness but stronger for clearing Summerheat from the Triple Burner.', 'Menos potente para drenar Humedad, pero más fuerte para aclarar Calor de Verano del Triple Burner.']
    ]
  },
  {
    number: 'S6', sequence: 6, name: 'Qing Shu Yi Qi Tang (Wang Shi-Xiong)', englishName: 'Clear Summerheat and Augment the Qi Decoction',
    categoryKey: 'dispel-summerheat', group: 'Dispel Summerheat and Augment the Qi',
    groupEs: 'Eliminar Calor de Verano y fortalecer el Qi', sourcePages: '243–245',
    kings: 'Xi Yang Shen, Xi Gua',
    pattern: 'Summerheat injuring both Qi and fluids through interior Heat and profuse sweating.',
    patternEs: 'Calor de Verano que lesiona simultáneamente Qi y líquidos mediante Calor interior y sudoración profusa.',
    action: 'Clears Summerheat, augments Qi, nourishes Yin, and generates fluids.',
    actionEs: 'Aclara Calor de Verano, fortalece Qi, nutre Yin y genera líquidos.',
    clue: 'Fever, profuse sweating, irritability, thirst, scant dark urine, fatigued limbs, shortness of breath, apathy, and a deficient rapid pulse.',
    clueEs: 'Fiebre, sudor profuso, irritabilidad, sed, orina escasa y oscura, extremidades fatigadas, disnea, apatía y pulso deficiente y rápido.',
    ingredients: 'Xi Yang Shen 4.5–6 g, Xi Gua 24–30 g, Lian Geng 12–15 g, Shi Hu 12–15 g, Mai Men Dong 6–9 g, Dan Zhu Ye 4.5–6 g, Zhi Mu 4.5–6 g, Huang Lian 2–3 g, Gan Cao 2–3 g, Jing Mi 12–15 g.',
    ingredientsEs: 'Xi Yang Shen 4.5–6 g, Xi Gua 24–30 g, Lian Geng 12–15 g, Shi Hu 12–15 g, Mai Men Dong 6–9 g, Dan Zhu Ye 4.5–6 g, Zhi Mu 4.5–6 g, Huang Lian 2–3 g, Gan Cao 2–3 g y Jing Mi 12–15 g.',
    safety: 'Its many Yin-nourishing ingredients can be cloying in Summerheat-Dampness and require substantial modification. It is inappropriate after the pathogenic influence has resolved.',
    safetyEs: 'Sus numerosas sustancias que nutren Yin pueden ser empalagosas en Calor de Verano-Humedad y requieren modificación importante. No corresponde cuando el patógeno ya se resolvió.',
    safetyTier: 'high',
    examFocus: 'The decisive combination is Heat plus fluid loss plus Qi collapse signs: profuse sweat, thirst, dark scant urine, fatigue, short breath, and a deficient-rapid pulse.',
    examFocusEs: 'La combinación decisiva es Calor más pérdida de líquidos más signos de caída de Qi: sudor profuso, sed, orina oscura escasa, fatiga, disnea y pulso deficiente-rápido.',
    ingredientRoles: 'Xi Yang Shen restores Qi and fluids; Xi Gua and Lian Geng clear Summerheat; Shi Hu and Mai Men Dong nourish Lung/Stomach Yin; Dan Zhu Ye, Zhi Mu, and Huang Lian clear Heat; Gan Cao and Jing Mi support the Middle.',
    ingredientRolesEs: 'Xi Yang Shen restaura Qi y líquidos; Xi Gua y Lian Geng aclaran Calor de Verano; Shi Hu y Mai Men Dong nutren Yin de Pulmón/Estómago; Dan Zhu Ye, Zhi Mu y Huang Lian aclaran Calor; Gan Cao y Jing Mi apoyan el Centro.',
    comparison: 'Bai Hu Jia Ren Shen Tang suits more exuberant Qi-level Heat; Zhu Ye Shi Gao Tang suits lingering Stomach Heat with nausea and rebellious Qi; this formula emphasizes Lung Qi, sweating, and fluid replenishment.',
    comparisonEs: 'Bai Hu Jia Ren Shen Tang corresponde a Calor del nivel Qi más exuberante; Zhu Ye Shi Gao Tang a Calor residual de Estómago con náusea y Qi rebelde; esta fórmula enfatiza Qi de Pulmón, sudor y reposición de líquidos.',
    modifications: [
      ['Mild Summerheat with severe fluid injury', 'Remove Huang Lian.', 'Calor de Verano leve con lesión grave de líquidos', 'Retirar Huang Lian.'],
      ['Greasy white tongue coating', 'Remove Zhi Mu and Mai Men Dong; add Fu Ling.', 'Saburra blanca y grasosa', 'Retirar Zhi Mu y Mai Men Dong; añadir Fu Ling.'],
      ['Persistent summer fever in children with Qi and fluid deficiency', 'Remove Huang Lian and Zhi Mu; add Bai Wei and Di Gu Pi.', 'Fiebre veraniega persistente infantil con deficiencia de Qi y líquidos', 'Retirar Huang Lian y Zhi Mu; añadir Bai Wei y Di Gu Pi.']
    ],
    associated: [
      ['Qing Shu Yi Qi Tang (Li Dong-Yuan)', 'A different formula with the same name: emphasizes Spleen-Qi deficiency and Dampness using Huang Qi, Ren Shen, Cang Zhu, Bai Zhu, and Qi-moving/draining herbs.', 'Fórmula distinta con el mismo nombre: enfatiza deficiencia de Qi de Bazo y Humedad mediante Huang Qi, Ren Shen, Cang Zhu, Bai Zhu y hierbas que mueven Qi y drenan.'],
      ['Lei Shi Qing Liang Di Shu Tang', 'Upper-burner Summerheat-Dampness with fever/chills, sweating, light-headedness, cough, and a thin slightly greasy coat.', 'Calor de Verano-Humedad del Jiao Superior con fiebre/escalofríos, sudor, mareo, tos y saburra fina ligeramente grasosa.']
    ]
  },
  {
    number: 'S7', sequence: 7, name: 'Wu Shi Lian Mei Tang', englishName: 'Master Wu’s Coptis and Mume Decoction',
    categoryKey: 'dispel-summerheat', group: 'Dispel Summerheat and Preserve the Body Fluids',
    groupEs: 'Eliminar Calor de Verano y preservar los líquidos corporales', sourcePages: '246–247',
    kings: 'Huang Lian',
    pattern: 'Summerheat entering the Lesser Yin and Terminal Yin warps with severe damage to Yin and fluids.',
    patternEs: 'Calor de Verano que penetra los niveles Shaoyin y Jueyin con lesión grave de Yin y líquidos.',
    action: 'Drains Heat, preserves fluids, and transforms Yin.',
    actionEs: 'Drena Calor, conserva líquidos y transforma Yin.',
    clue: 'Wasting and thirsting, Heart Heat, irritability, restlessness, paralysis, clouded consciousness, dry cracked tongue, and a submerged thin pulse.',
    clueEs: 'Desgaste y sed intensa, Calor en Corazón, irritabilidad, inquietud, parálisis, conciencia nublada, lengua seca y agrietada y pulso sumergido y fino.',
    ingredients: 'Huang Lian 6 g, Wu Mei 9 g, Mai Men Dong 9 g, Sheng Di Huang 9 g, E Jiao 6 g.',
    ingredientsEs: 'Huang Lian 6 g, Wu Mei 9 g, Mai Men Dong 9 g, Sheng Di Huang 9 g y E Jiao 6 g.',
    safety: 'Contraindicated when Summerheat has not caused severe fluid loss. Thirst or irritability alone is insufficient: a dry cracked tongue, submerged thin pulse, wasting, or comparable Yin-fluid injury must be present. Clouded consciousness is a medical emergency.',
    safetyEs: 'Contraindicada cuando Calor de Verano no ha causado pérdida grave de líquidos. Sed o irritabilidad aisladas no bastan: debe existir lengua seca y agrietada, pulso sumergido y fino, desgaste u otra lesión comparable de Yin-líquidos. La conciencia nublada es una emergencia médica.',
    safetyTier: 'critical',
    examFocus: 'Astringency is used only after fluid injury dominates. Wu Mei preserves fluids while Huang Lian drains Heat so the pathogen is not trapped.',
    examFocusEs: 'La astringencia se utiliza solo cuando domina la lesión de líquidos. Wu Mei conserva líquidos mientras Huang Lian drena Calor para no retener el patógeno.',
    ingredientRoles: 'Huang Lian drains Heart Heat; Wu Mei astringes and generates fluids; Mai Men Dong and Sheng Di Huang enrich Yin; E Jiao nourishes Blood and fluids, softens the Liver, and assists in extinguishing Wind.',
    ingredientRolesEs: 'Huang Lian drena Calor de Corazón; Wu Mei astringe y genera líquidos; Mai Men Dong y Sheng Di Huang enriquecen Yin; E Jiao nutre Sangre y líquidos, suaviza Hígado y ayuda a extinguir Viento.',
    comparison: 'Unlike early-stage Summerheat formulas, this one uses sour astringents because severe Yin-fluid loss now dominates. It must not be chosen merely for thirst while an unresolved pathogen remains superficial.',
    comparisonEs: 'A diferencia de las fórmulas de etapa inicial, utiliza astringentes agrios porque ahora domina la pérdida grave de Yin-líquidos. No debe elegirse solo por sed mientras el patógeno permanece sin resolver en superficie.',
    associated: [
      ['Ren Shen Wu Mei Tang', 'Late-stage Summerheat with combined Qi/Yin damage after excessive sweating; Ren Shen is often replaced by Xi Yang Shen.', 'Etapa tardía con lesión combinada de Qi/Yin tras sudor excesivo; Ren Shen suele sustituirse por Xi Yang Shen.']
    ]
  }
];

export const summerheatCategoryGuide = {
  priorityTitle: 'Supplementary Chapter 5: five treatment routes',
  priorityTitleEs: 'Capítulo suplementario 5: cinco rutas terapéuticas',
  priorityText: 'These seven formulas come from the supplied Chapter 5 source (pages 231–248). They extend the study guide without changing the official 63-formula Board roster or its practice-question totals.',
  priorityTextEs: 'Estas siete fórmulas proceden del capítulo 5 aportado (páginas 231–248). Amplían la guía sin modificar el listado Board oficial de 63 fórmulas ni sus totales de preguntas.',
  routeTitle: 'Choose by the dominant damage',
  routeTitleEs: 'Elige según la lesión dominante',
  route: [
    ['1', 'Mild superficial Heat', 'Qing Luo Yin: mild fever/thirst with unclear head and slight Dampness.', 'Calor superficial leve', 'Qing Luo Yin: fiebre/sed leves con cabeza poco clara y Humedad ligera.'],
    ['2', 'Exterior Cold', 'Xiang Ru San for Cold-Damp; Xin Jia Xiang Ru Yin when trapped Heat is stronger.', 'Frío Exterior', 'Xiang Ru San para Frío-Humedad; Xin Jia Xiang Ru Yin cuando el Calor atrapado es más fuerte.'],
    ['3', 'Dampness and water', 'Liu Yi San is mild; Gui Ling Gan Lu Yin treats severe Heat-Damp with digestive turmoil.', 'Humedad y agua', 'Liu Yi San es suave; Gui Ling Gan Lu Yin trata Calor-Humedad grave con trastorno digestivo.'],
    ['4', 'Qi and fluid injury', 'Qing Shu Yi Qi Tang: profuse sweat, fatigue, short breath, thirst, and dark scant urine.', 'Lesión de Qi y líquidos', 'Qing Shu Yi Qi Tang: sudor profuso, fatiga, disnea, sed y orina oscura escasa.'],
    ['5', 'Severe Yin-fluid loss', 'Wu Shi Lian Mei Tang: wasting, cracked tongue, deep-thin pulse, and possible internal Wind.', 'Pérdida grave de Yin-líquidos', 'Wu Shi Lian Mei Tang: desgaste, lengua agrietada, pulso profundo-fino y posible Viento interno.']
  ],
  quickCompareTitle: 'Chapter 5 principal formulas',
  quickCompareTitleEs: 'Fórmulas principales del capítulo 5',
  quickCompare: [
    ['Qing Luo Yin', 'Mild Lung Qi-level Summerheat', 'Light-headed, unclear head · mild thirst · pink tongue', 'Calor de Verano leve en Qi de Pulmón', 'Mareo y cabeza poco clara · sed leve · lengua rosada'],
    ['Xiang Ru San', 'Exterior Cold with interior Dampness', 'Chills · no sweat · vomiting/diarrhea · white greasy coat', 'Frío Exterior con Humedad interior', 'Escalofríos · sin sudor · vómitos/diarrea · saburra blanca grasosa'],
    ['Xin Jia Xiang Ru Yin', 'Trapped severe Summerheat-Dampness', 'No sweat plus thirst, flushed face, red tongue and strong Heat signs', 'Calor de Verano-Humedad grave atrapado', 'Sin sudor más sed, rostro rojo, lengua roja y signos fuertes de Calor'],
    ['Liu Yi San', 'Mild Summerheat-Dampness', 'Urinary difficulty · soggy-rapid pulse · simple 6:1 architecture', 'Calor de Verano-Humedad leve', 'Dificultad urinaria · pulso empapado-rápido · arquitectura simple 6:1'],
    ['Gui Ling Gan Lu Yin', 'Severe Heat plus water-Damp stagnation', 'Heat and Damp both strong · digestive turmoil', 'Calor grave más estancamiento de agua-Humedad', 'Calor y Humedad fuertes · trastorno digestivo'],
    ['Qing Shu Yi Qi Tang', 'Summerheat injuring Qi and fluids', 'Profuse sweat · thirst · fatigue · short breath · deficient-rapid pulse', 'Calor de Verano lesiona Qi y líquidos', 'Sudor profuso · sed · fatiga · disnea · pulso deficiente-rápido'],
    ['Wu Shi Lian Mei Tang', 'Summerheat entering Shaoyin/Jueyin', 'Wasting · cracked tongue · submerged-thin pulse · clouded consciousness', 'Calor de Verano entra en Shaoyin/Jueyin', 'Desgaste · lengua agrietada · pulso sumergido-fino · conciencia nublada']
  ],
  referencesTitle: 'Variations, associated formulas, and safety notes',
  referencesTitleEs: 'Variaciones, fórmulas asociadas y notas de seguridad',
  referencesText: 'These names support differentiation; they are not additional principal cards. Open each formula card to see its specific variations and modifications.',
  referencesTextEs: 'Estos nombres apoyan la diferenciación; no son tarjetas principales adicionales. Abre cada ficha para consultar sus variaciones y modificaciones específicas.',
  referenceGroups: [
    {
      title: 'Exterior and Lung branches', titleEs: 'Ramas de Exterior y Pulmón',
      items: [
        ['Wu Ye Lu Gen Tang', 'Qing Luo Yin branch for more upper-burner Damp-Heat.', 'Rama de Qing Luo Yin para mayor Humedad-Calor del Jiao Superior.'],
        ['Si/Wu/Liu Wei Xiang Ru Yin', 'Stepwise additions for Heat, Dampness, and calf stiffness.', 'Adiciones progresivas para Calor, Humedad y rigidez de pantorrillas.'],
        ['Huang Lian / Shi Wei Xiang Ru Yin', 'Sudden turmoil or Spleen-deficient exterior Summerheat branches.', 'Ramas para trastorno súbito o Calor de Verano Exterior con deficiencia de Bazo.']
      ]
    },
    {
      title: 'Dampness branches', titleEs: 'Ramas de Humedad',
      items: [
        ['Ji Su / Yi Yuan / Bi Yu San', 'Liu Yi San variations for Exterior, Heart-Shen, or Liver/Gallbladder Heat.', 'Variaciones de Liu Yi San para Exterior, Corazón-Shen o Calor de Hígado/Vesícula.'],
        ['San Shi Tang', 'Stronger Summerheat clearing, less Dampness drainage.', 'Mayor aclaramiento de Calor de Verano y menor drenaje de Humedad.'],
        ['Lei Shi Qing Liang Di Shu Fa', 'Stronger urinary outlet and Heart-Heat emphasis.', 'Mayor énfasis en salida urinaria y Calor de Corazón.']
      ]
    },
    {
      title: 'Deficiency and fluids', titleEs: 'Deficiencia y líquidos',
      items: [
        ['Qing Shu Yi Qi Tang (Li Dong-Yuan)', 'Same name, different formula: Spleen-Qi deficiency with Dampness.', 'Mismo nombre, fórmula distinta: deficiencia de Qi de Bazo con Humedad.'],
        ['Ren Shen Wu Mei Tang', 'Late Qi/Yin damage after excessive sweating.', 'Lesión tardía de Qi/Yin tras sudor excesivo.'],
        ['Obsolete substances', 'Zhu Sha and Jin Zhi are not used; the source gives modern substitutions.', 'Zhu Sha y Jin Zhi no se utilizan; la fuente ofrece sustituciones modernas.']
      ]
    }
  ]
};
