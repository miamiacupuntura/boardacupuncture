export const exteriorInteriorExcessCategory = {
  number: 'S3',
  key: 'exterior-interior-excess',
  name: 'Release Exterior-Interior Excess',
  nameEs: 'Liberar el exceso Exterior-Interior',
  sourceChapter: 7,
  sourcePages: '285–301'
};

const urgentHeatSafety = {
  safetyTier: 'high',
  safety: 'This is a professional pattern-differentiation reference, not a self-treatment protocol. High fever, delirium, blood in vomit or stool, severe dehydration, persistent vomiting, a rigid or acutely painful abdomen, or neurologic deficits require urgent medical assessment.',
  safetyEs: 'Esta es una referencia profesional de diferenciación de patrones, no un protocolo de autotratamiento. Fiebre alta, delirio, sangre en vómito o heces, deshidratación grave, vómito persistente, abdomen rígido o con dolor agudo, o déficits neurológicos requieren evaluación médica urgente.'
};

export const exteriorInteriorExcessFormulas = [
  {
    number: 'EI1', sequence: 1, name: 'Da Chai Hu Tang', englishName: 'Major Bupleurum Decoction',
    categoryKey: 'exterior-interior-excess', group: 'Release the Exterior and Purge the Interior', groupEs: 'Liberar el Exterior y purgar el Interior', sourcePages: '286–289',
    kings: 'Chai Hu, Da Huang',
    pattern: 'Concurrent Shaoyang and Yangming organ-warp excess with Heat clumping in the interior.',
    patternEs: 'Exceso simultáneo de Shaoyang y del nivel órgano Yangming con Calor acumulado en el Interior.',
    action: 'Harmonizes and releases Shaoyang and drains internal clumping due to Heat.',
    actionEs: 'Armoniza y libera Shaoyang y drena la acumulación interna debida a Calor.',
    clue: 'Alternating fever and chills with continuous vomiting, bitter taste, chest or hypochondriac fullness, and hard focal epigastric distention or pain; stools may be absent or hot and burning.',
    clueEs: 'Fiebre y escalofríos alternantes con vómito continuo, sabor amargo, plenitud torácica o hipocondríaca y distensión o dolor epigástrico focal duro; puede faltar la evacuación o haber diarrea caliente y ardiente.',
    ingredients: 'Chai Hu 24 g, Huang Qin 9 g, Zhi Shi 6–9 g, Da Huang 6 g, Shao Yao 9 g, Zhi Ban Xia 24 g, Sheng Jiang 15 g, Da Zao 12 pieces (source; modern doses are commonly lower).',
    ingredientsEs: 'Chai Hu 24 g, Huang Qin 9 g, Zhi Shi 6–9 g, Da Huang 6 g, Shao Yao 9 g, Zhi Ban Xia 24 g, Sheng Jiang 15 g y Da Zao 12 piezas (fuente; las dosis modernas suelen ser menores).',
    ...urgentHeatSafety,
    examFocus: 'It must show both halves: Shaoyang pivot signs plus Yangming organ clumping. Continuous vomiting and a hard, pressure-sensitive epigastrium separate it from Xiao Chai Hu Tang.',
    examFocusEs: 'Debe mostrar ambas mitades: signos del pivote Shaoyang más acumulación de órgano Yangming. El vómito continuo y epigastrio duro y sensible a la presión la separan de Xiao Chai Hu Tang.',
    ingredientRoles: 'Chai Hu vents Shaoyang while Da Huang drains Yangming Heat. Huang Qin clears Shaoyang and the bowels; Zhi Shi breaks Qi clumping; Shao Yao relaxes pain and protects fluids; Ban Xia with Sheng Jiang descends rebellious Stomach Qi; Da Zao harmonizes.',
    ingredientRolesEs: 'Chai Hu ventila Shaoyang mientras Da Huang drena Calor de Yangming. Huang Qin aclara Shaoyang e Intestinos; Zhi Shi rompe acumulación de Qi; Shao Yao relaja dolor y protege líquidos; Ban Xia con Sheng Jiang desciende Qi de Estómago rebelde; Da Zao armoniza.',
    comparison: 'Xiao Chai Hu Tang treats a purer Shaoyang disorder with nausea or occasional vomiting and no hard Yangming clumping. Da Cheng Qi Tang treats stronger Yangming bowel excess without the Shaoyang pivot signs.',
    comparisonEs: 'Xiao Chai Hu Tang trata un cuadro Shaoyang más puro con náusea o vómito ocasional y sin acumulación dura de Yangming. Da Cheng Qi Tang trata exceso intestinal Yangming más fuerte sin signos del pivote Shaoyang.',
    modifications: [
      ['Marked chest and epigastric pain or distention', 'Add Mu Xiang, Yu Jin, and Yin Chen.', 'Dolor o distensión marcada de pecho y epigastrio', 'Añadir Mu Xiang, Yu Jin y Yin Chen.'],
      ['Severe abdominal pain', 'Add Yan Hu Suo, Wu Yao, and Xiang Fu.', 'Dolor abdominal grave', 'Añadir Yan Hu Suo, Wu Yao y Xiang Fu.'],
      ['Severe nausea and vomiting', 'Add Zhu Ru, Xuan Fu Hua, and Huang Lian.', 'Náuseas y vómitos graves', 'Añadir Zhu Ru, Xuan Fu Hua y Huang Lian.'],
      ['Acute jaundice', 'Add Zhi Zi and Huang Bai.', 'Ictericia aguda', 'Añadir Zhi Zi y Huang Bai.'],
      ['Gallstones', 'Add Jin Qian Cao, Shi Wei, and Hai Jin Sha.', 'Cálculos biliares', 'Añadir Jin Qian Cao, Shi Wei y Hai Jin Sha.'],
      ['Pancreatitis branch in the source', 'Combine with Ban Xia Xie Xin Tang; suspected acute pancreatitis requires urgent hospital assessment.', 'Rama de pancreatitis en la fuente', 'Combinar con Ban Xia Xie Xin Tang; la sospecha de pancreatitis aguda requiere evaluación hospitalaria urgente.']
    ],
    associated: [
      ['Hou Po Qi Wu Tang', 'A Taiyang-Yangming combination emphasizing abdominal fullness and constipation with fever and a floating rapid pulse; releases the muscles, moves Qi, and opens the bowels.', 'Combinación Taiyang-Yangming que enfatiza plenitud abdominal y estreñimiento con fiebre y pulso flotante-rápido; libera músculos, mueve Qi y abre Intestinos.'],
      ['Jia Wei Da Chai Hu Tang', 'A historical menstrual Cold-damage branch with Shaoyang obstruction, hearing loss, and possible back, flank, or lower-abdominal pain.', 'Rama histórica de daño por Frío durante la menstruación con obstrucción Shaoyang, pérdida auditiva y posible dolor de espalda, flancos o abdomen inferior.'],
      ['Qing Yi Tang', 'A modern source formula for stabbing epigastric pain radiating to the back with constipation and alternating fever/chills; its acute-pancreatitis context requires urgent medical care.', 'Fórmula de fuente moderna para dolor epigástrico punzante irradiado a espalda con estreñimiento y fiebre/escalofríos alternantes; su contexto de pancreatitis aguda requiere atención urgente.']
    ]
  },
  {
    number: 'EI2', sequence: 2, name: 'Fang Feng Tong Sheng San', englishName: 'Saposhnikovia Powder that Sagely Unblocks',
    categoryKey: 'exterior-interior-excess', group: 'Release the Exterior and Purge the Interior', groupEs: 'Liberar el Exterior y purgar el Interior', sourcePages: '290–292',
    kings: 'Fang Feng, Ma Huang, Jiu Da Huang, Mang Xiao',
    pattern: 'Wind-Heat in the exterior with simultaneous excess Heat in the interior—the classic double-releasing pattern.',
    patternEs: 'Viento-Calor en el Exterior con exceso simultáneo de Calor en el Interior: patrón clásico de doble liberación.',
    action: 'Disperses Wind, releases the exterior, drains Heat through urine and stool, and unblocks the bowels.',
    actionEs: 'Dispersa Viento, libera el Exterior, drena Calor por orina y heces y desbloquea los Intestinos.',
    clue: 'Strong fever and chills, red sore eyes, thick nasal discharge, bitter dry mouth, chest and diaphragm stiffness, constipation, dark rough urine, greasy yellow coat, and a flooding rapid or wiry slippery pulse.',
    clueEs: 'Fiebre y escalofríos fuertes, ojos rojos dolorosos, secreción nasal espesa, boca amarga y seca, rigidez de pecho y diafragma, estreñimiento, orina oscura y áspera, saburra amarilla grasosa y pulso desbordante-rápido o de cuerda-resbaladizo.',
    ingredients: 'Fang Feng 15 g, Ma Huang 15 g, Jiu Da Huang 15 g, Mang Xiao 15 g, Jing Jie 15 g, Bo He 15 g, Zhi Zi 15 g, Hua Shi 90 g, Shi Gao 30 g, Lian Qiao 15 g, Huang Qin 30 g, Jie Geng 30 g, Chuan Xiong 15 g, Dang Gui 15 g, Bai Shao 15 g, Bai Zhu 15 g, Gan Cao 60 g; prepared as powder with Sheng Jiang (source doses).',
    ingredientsEs: 'Fang Feng 15 g, Ma Huang 15 g, Jiu Da Huang 15 g, Mang Xiao 15 g, Jing Jie 15 g, Bo He 15 g, Zhi Zi 15 g, Hua Shi 90 g, Shi Gao 30 g, Lian Qiao 15 g, Huang Qin 30 g, Jie Geng 30 g, Chuan Xiong 15 g, Dang Gui 15 g, Bai Shao 15 g, Bai Zhu 15 g y Gan Cao 60 g; se prepara como polvo con Sheng Jiang (dosis de la fuente).',
    ...urgentHeatSafety,
    examFocus: 'Think simultaneous outlets: sweat the exterior, drain Heat through urine, and purge stool. Constipation plus strong exterior Wind-Heat separates it from formulas that only clear the interior.',
    examFocusEs: 'Piensa en salidas simultáneas: sudar el Exterior, drenar Calor por orina y purgar heces. Estreñimiento más Viento-Calor exterior fuerte la separan de fórmulas que solo aclaran el Interior.',
    ingredientRoles: 'Fang Feng and Ma Huang release the exterior; wine-washed Da Huang and Mang Xiao purge Heat; Jing Jie and Bo He assist exterior release; Zhi Zi and Hua Shi drain through urine; Shi Gao, Lian Qiao, Huang Qin, and Jie Geng clear Lung/Stomach Heat; the Blood herbs protect and move Blood; Bai Zhu and Gan Cao protect the Middle.',
    ingredientRolesEs: 'Fang Feng y Ma Huang liberan el Exterior; Da Huang lavada con vino y Mang Xiao purgan Calor; Jing Jie y Bo He apoyan la liberación; Zhi Zi y Hua Shi drenan por orina; Shi Gao, Lian Qiao, Huang Qin y Jie Geng aclaran Calor de Pulmón/Estómago; las hierbas de Sangre la protegen y movilizan; Bai Zhu y Gan Cao protegen el Centro.',
    comparison: 'Liang Ge San primarily clears and purges Upper- and Middle-Burner Heat. Fang Feng Tong Sheng San adds a strong exterior Wind-Heat presentation and opens all three routes: sweating, urination, and stool.',
    comparisonEs: 'Liang Ge San principalmente aclara y purga Calor de Jiao Superior y Medio. Fang Feng Tong Sheng San añade un cuadro fuerte de Viento-Calor exterior y abre tres vías: sudor, orina y heces.',
    modifications: [
      ['No strong chills', 'Remove Ma Huang.', 'Sin escalofríos fuertes', 'Retirar Ma Huang.'],
      ['No strong fever', 'Remove Shi Gao.', 'Sin fiebre fuerte', 'Retirar Shi Gao.'],
      ['Cough with sputum', 'Add Zhi Ban Xia fried in ginger juice.', 'Tos con esputo', 'Añadir Zhi Ban Xia frita en jugo de jengibre.']
    ],
    variations: [
      ['Shuang Jie Tong Sheng San', 'Removes Da Huang and Mang Xiao when constipation is absent.', 'Retira Da Huang y Mang Xiao cuando no hay estreñimiento.']
    ],
    associated: [
      ['Qu Feng Zhi Bao Dan', 'A large historical pill for organ-type Wind-stroke with internally generated Wind and Heat. Facial asymmetry, paralysis, or altered consciousness requires emergency care.', 'Píldora histórica extensa para ataque de Viento tipo órgano con Viento y Calor generados internamente. Asimetría facial, parálisis o conciencia alterada requieren atención urgente.']
    ]
  },
  {
    number: 'EI3', sequence: 3, name: 'Ge Gen Huang Qin Huang Lian Tang', englishName: 'Kudzu, Scutellaria, and Coptis Decoction',
    categoryKey: 'exterior-interior-excess', group: 'Release the Exterior and Clear the Interior', groupEs: 'Liberar el Exterior y aclarar el Interior', sourcePages: '292–294',
    kings: 'Ge Gen',
    pattern: 'An incompletely resolved exterior disorder with blazing Damp-Heat in the Stomach and Intestines.',
    patternEs: 'Trastorno exterior no resuelto por completo con Humedad-Calor ardiente en Estómago e Intestinos.',
    action: 'Releases the exterior, drains Heat, dries Dampness, and stops dysenteric diarrhea.',
    actionEs: 'Libera el Exterior, drena Calor, seca Humedad y detiene diarrea disentérica.',
    clue: 'Fever, sweating, thirst, foul-smelling diarrhea with anal burning, irritability and Heat in the chest or epigastrium, red tongue with yellow coat, and a rapid pulse; wheezing may occur.',
    clueEs: 'Fiebre, sudor, sed, diarrea fétida con ardor anal, irritabilidad y Calor en pecho o epigastrio, lengua roja con saburra amarilla y pulso rápido; puede haber sibilancia.',
    ingredients: 'Ge Gen 15–24 g, Huang Qin 9 g, Huang Lian 6 g, Zhi Gan Cao 6 g.',
    ingredientsEs: 'Ge Gen 15–24 g, Huang Qin 9 g, Huang Lian 6 g y Zhi Gan Cao 6 g.',
    safetyTier: 'high',
    safety: 'Contraindicated for diarrhea without fever and with a submerged slow pulse. Severe, bloody, persistent, or dehydrating diarrhea requires medical evaluation; this card is not a substitute for infectious-disease care.',
    safetyEs: 'Contraindicada en diarrea sin fiebre y con pulso sumergido-lento. Diarrea grave, sanguinolenta, persistente o deshidratante requiere evaluación médica; esta tarjeta no sustituye la atención de enfermedades infecciosas.',
    examFocus: 'The diarrhea itself is hot: foul odor and anal burning with fever, thirst, a red-yellow tongue, and rapid pulse. Ge Gen both releases the muscle layer and raises clear Yang.',
    examFocusEs: 'La diarrea es caliente: olor fétido y ardor anal con fiebre, sed, lengua roja-amarilla y pulso rápido. Ge Gen libera la capa muscular y eleva Yang claro.',
    ingredientRoles: 'Ge Gen releases the exterior, clears Heat, and raises clear Yang to stop diarrhea; Huang Lian drains Yangming Heat and dries intestinal Dampness; Huang Qin clears relatively superficial Lung and intestinal Heat; Zhi Gan Cao protects the Middle and harmonizes.',
    ingredientRolesEs: 'Ge Gen libera el Exterior, aclara Calor y eleva Yang claro para detener diarrea; Huang Lian drena Calor Yangming y seca Humedad intestinal; Huang Qin aclara Calor relativamente superficial de Pulmón e Intestinos; Zhi Gan Cao protege el Centro y armoniza.',
    comparison: 'Gui Zhi Ren Shen Tang has cold, watery, odorless diarrhea and abdominal pain relieved by warmth. This formula has hot, foul diarrhea with anal burning, thirst, and a rapid pulse.',
    comparisonEs: 'Gui Zhi Ren Shen Tang presenta diarrea fría, acuosa y sin olor y dolor abdominal aliviado por calor. Esta fórmula presenta diarrea caliente y fétida con ardor anal, sed y pulso rápido.',
    modifications: [
      ['Abdominal pain', 'Add Mu Xiang and Bai Shao.', 'Dolor abdominal', 'Añadir Mu Xiang y Bai Shao.'],
      ['Tenesmus', 'Add Mu Xiang and Bing Lang.', 'Tenesmo', 'Añadir Mu Xiang y Bing Lang.'],
      ['High fever with blood or pus in stool', 'Add Bai Tou Weng and Qin Pi; urgent medical assessment is also required.', 'Fiebre alta con sangre o pus en heces', 'Añadir Bai Tou Weng y Qin Pi; también se requiere evaluación médica urgente.'],
      ['Prominent exterior signs', 'Add Ju Hua.', 'Signos exteriores prominentes', 'Añadir Ju Hua.'],
      ['Pronounced wheezing', 'Add Sang Bai Pi.', 'Sibilancia pronunciada', 'Añadir Sang Bai Pi.'],
      ['Dampness with greasy coat and slippery pulse', 'Add Jin Yin Hua and Che Qian Zi.', 'Humedad con saburra grasosa y pulso resbaladizo', 'Añadir Jin Yin Hua y Che Qian Zi.'],
      ['Nausea and vomiting', 'Add Ban Xia and Zhu Ru.', 'Náuseas y vómitos', 'Añadir Ban Xia y Zhu Ru.']
    ]
  },
  {
    number: 'EI4', sequence: 4, name: 'Shi Gao Tang', englishName: 'Gypsum Decoction',
    categoryKey: 'exterior-interior-excess', group: 'Release the Exterior and Clear the Interior', groupEs: 'Liberar el Exterior y aclarar el Interior', sourcePages: '294–296',
    kings: 'Shi Gao',
    pattern: 'Severe exterior Cold constraint with blazing toxic Heat throughout the interior.',
    patternEs: 'Constricción grave por Frío exterior con Calor tóxico ardiente en todo el Interior.',
    action: 'Clears Heat, resolves toxicity, and releases the exterior.',
    actionEs: 'Aclara Calor, resuelve toxicidad y libera el Exterior.',
    clue: 'Strong fever and chills without sweating, headache, red face and eyes, thirst, irritability and insomnia, possible bleeding or skin blotches, and a rapid slippery or rapid flooding pulse.',
    clueEs: 'Fiebre y escalofríos fuertes sin sudor, cefalea, cara y ojos rojos, sed, irritabilidad e insomnio, posible sangrado o manchas cutáneas y pulso rápido-resbaladizo o rápido-desbordante.',
    ingredients: 'Shi Gao 6 g (15–30 g commonly used at present), Ma Huang 9 g, Dan Dou Chi 9 g, Huang Lian 6 g, Huang Qin 6 g, Huang Bai 6 g, Zhi Zi 6–9 g.',
    ingredientsEs: 'Shi Gao 6 g (actualmente se usan con frecuencia 15–30 g), Ma Huang 9 g, Dan Dou Chi 9 g, Huang Lian 6 g, Huang Qin 6 g, Huang Bai 6 g y Zhi Zi 6–9 g.',
    ...urgentHeatSafety,
    examFocus: 'The paradox is decisive: blazing interior Heat with a rapid flooding pulse, yet severe exterior constraint prevents sweating. It is broader and hotter than Da Qing Long Tang.',
    examFocusEs: 'La paradoja es decisiva: Calor interior ardiente con pulso rápido-desbordante, pero la constricción exterior grave impide sudar. Es más amplia y caliente que Da Qing Long Tang.',
    ingredientRoles: 'Shi Gao clears severe Heat and irritability; Ma Huang opens the constrained exterior; Dan Dou Chi assists release; Huang Lian, Huang Qin, Huang Bai, and Zhi Zi drain toxic Heat from all three burners.',
    ingredientRolesEs: 'Shi Gao aclara Calor grave e irritabilidad; Ma Huang abre el Exterior constreñido; Dan Dou Chi apoya la liberación; Huang Lian, Huang Qin, Huang Bai y Zhi Zi drenan Calor tóxico de los tres calentadores.',
    comparison: 'Da Qing Long Tang has stronger exterior Yang constraint but less severe interior Heat. Shi Gao Tang is selected when Heat is blazing through all three burners and may move Blood recklessly.',
    comparisonEs: 'Da Qing Long Tang presenta constricción exterior de Yang más fuerte pero menor Calor interior. Shi Gao Tang se elige cuando Calor arde en los tres calentadores y puede mover Sangre de forma imprudente.',
    variations: [
      ['Shi Gao Tang from Six Texts', 'Adds Sheng Jiang, Da Zao, and green tea for Heat in all three burners with generalized pain.', 'Añade Sheng Jiang, Da Zao y té verde para Calor en los tres calentadores con dolor generalizado.']
    ],
    associated: [
      ['Shuang Jie Jia Cong Chi Tang', 'Releases warm-pathogen Heat through sweating and urination before clumping develops; uses Cong Bai and Dan Dou Chi with cooling and draining herbs.', 'Libera Calor de patógeno tibio mediante sudor y orina antes de que se acumule; usa Cong Bai y Dan Dou Chi con hierbas frías y drenantes.'],
      ['Liu Shen Tong Jie San', 'A damp-Heat Triple-Burner branch with exterior Wind-Cold, headache, fever/chills without sweat, chest stiffness, reduced or bloody urine, nausea, and a greasy yellow coat.', 'Rama de Humedad-Calor del Triple Calentador con Viento-Frío exterior, cefalea, fiebre/escalofríos sin sudor, rigidez torácica, orina reducida o con sangre, náusea y saburra amarilla grasosa.']
    ]
  },
  {
    number: 'EI5', sequence: 5, name: 'Wu Ji San', englishName: 'Five-Accumulation Powder',
    categoryKey: 'exterior-interior-excess', group: 'Release the Exterior and Warm the Interior', groupEs: 'Liberar el Exterior y calentar el Interior', sourcePages: '296–298',
    kings: 'Ma Huang, Bai Zhi, Gan Jiang, Rou Gui',
    pattern: 'Externally contracted Wind-Cold with internal Cold injury producing five accumulations: Cold, Dampness, Qi, Blood, and Phlegm.',
    patternEs: 'Viento-Frío contraído externamente con lesión interna por Frío que produce cinco acumulaciones: Frío, Humedad, Qi, Sangre y Flema.',
    action: 'Releases the exterior, warms the interior, regulates Qi, transforms Phlegm, invigorates Blood, and reduces accumulation.',
    actionEs: 'Libera el Exterior, calienta el Interior, regula Qi, transforma Flema, moviliza Sangre y reduce acumulación.',
    clue: 'Fever and chills without sweat, headache and body aches, chest-abdominal fullness, nausea or vomiting, cold abdominal pain, diarrhea with borborygmus, white greasy coat, and a submerged wiry or floating slow pulse.',
    clueEs: 'Fiebre y escalofríos sin sudor, cefalea y dolores corporales, plenitud toracoabdominal, náuseas o vómitos, dolor abdominal frío, diarrea con borborigmos, saburra blanca grasosa y pulso sumergido-de cuerda o flotante-lento.',
    ingredients: 'Modern decoction ranges from the source: Ma Huang 1.5–4.5 g, Bai Zhi 1.5–4.5 g, Gan Jiang 1.5–4.5 g, Rou Gui 1.5–4.5 g, Cang Zhu 3–9 g, Hou Po 1.5–4.5 g, Chen Pi 3–9 g, Zhi Ban Xia 3–9 g, Fu Ling 3–9 g, Jie Geng 3–9 g, Zhi Ke 1.5–4.5 g, Dang Gui 1.5–4.5 g, Bai Shao 1.5–4.5 g, Chuan Xiong 1.5–4.5 g, Gan Cao 1.5–4.5 g.',
    ingredientsEs: 'Rangos modernos de decocción indicados por la fuente: Ma Huang 1.5–4.5 g, Bai Zhi 1.5–4.5 g, Gan Jiang 1.5–4.5 g, Rou Gui 1.5–4.5 g, Cang Zhu 3–9 g, Hou Po 1.5–4.5 g, Chen Pi 3–9 g, Zhi Ban Xia 3–9 g, Fu Ling 3–9 g, Jie Geng 3–9 g, Zhi Ke 1.5–4.5 g, Dang Gui 1.5–4.5 g, Bai Shao 1.5–4.5 g, Chuan Xiong 1.5–4.5 g y Gan Cao 1.5–4.5 g.',
    safetyTier: 'high',
    safety: 'Contraindicated for Damp-Heat or Yin deficiency. The formula is complex and should be modified to the actual presentation rather than used indiscriminately; Ma Huang and Rou Gui require professional screening.',
    safetyEs: 'Contraindicada en Humedad-Calor o deficiencia de Yin. Es una fórmula compleja que debe adaptarse al cuadro real y no utilizarse indiscriminadamente; Ma Huang y Rou Gui requieren evaluación profesional.',
    examFocus: 'Cold is primary, but the differentiator is breadth: exterior Wind-Cold plus Dampness, Phlegm, Qi stagnation, and Blood disharmony in the interior.',
    examFocusEs: 'Frío es primario, pero la amplitud diferencia el cuadro: Viento-Frío exterior más Humedad, Flema, estancamiento de Qi y desarmonía de Sangre en el Interior.',
    ingredientRoles: 'Ma Huang and Bai Zhi release exterior Cold; Gan Jiang and Rou Gui warm the interior; Cang Zhu and Hou Po dry Dampness; Chen Pi, Ban Xia, and Fu Ling transform Phlegm; Jie Geng and Zhi Ke regulate ascent/descent; Dang Gui, Bai Shao, and Chuan Xiong nourish and move Blood; Gan Cao harmonizes.',
    ingredientRolesEs: 'Ma Huang y Bai Zhi liberan Frío exterior; Gan Jiang y Rou Gui calientan Interior; Cang Zhu y Hou Po secan Humedad; Chen Pi, Ban Xia y Fu Ling transforman Flema; Jie Geng y Zhi Ke regulan ascenso/descenso; Dang Gui, Bai Shao y Chuan Xiong nutren y mueven Sangre; Gan Cao armoniza.',
    comparison: 'Chai Hu Gui Zhi Gan Jiang Tang places the exterior pathogen in Shaoyang and internal Cold-Dampness in the Triple Burner, often with head sweating. Wu Ji San keeps the exterior in Taiyang/Yangming and centers internal Cold-Dampness in the Middle Burner.',
    comparisonEs: 'Chai Hu Gui Zhi Gan Jiang Tang sitúa el patógeno exterior en Shaoyang y Frío-Humedad interno en Triple Calentador, a menudo con sudor cefálico. Wu Ji San mantiene el Exterior en Taiyang/Yangming y centra Frío-Humedad interno en Jiao Medio.',
    modifications: [
      ['More acute exterior Cold', 'Substitute Gui Zhi for Rou Gui.', 'Frío exterior más agudo', 'Sustituir Rou Gui por Gui Zhi.'],
      ['Deficient exterior with sweating', 'Remove Ma Huang and Cang Zhu.', 'Exterior deficiente con sudor', 'Retirar Ma Huang y Cang Zhu.'],
      ['Qi deficiency with weakness', 'Remove Zhi Ke and Chen Pi; add Ren Shen and Bai Zhu.', 'Deficiencia de Qi con debilidad', 'Retirar Zhi Ke y Chen Pi; añadir Ren Shen y Bai Zhu.'],
      ['Severe Cold with intense abdominal pain', 'Add Wu Zhu Yu.', 'Frío grave con dolor abdominal intenso', 'Añadir Wu Zhu Yu.'],
      ['Irregular menstruation with Cold abdominal pain', 'Remove Ma Huang and Bai Zhi; add Xiang Fu, Yan Hu Suo, and Ai Ye.', 'Menstruación irregular con dolor abdominal por Frío', 'Retirar Ma Huang y Bai Zhi; añadir Xiang Fu, Yan Hu Suo y Ai Ye.']
    ]
  },
  {
    number: 'EI6', sequence: 6, name: 'Gui Zhi Ren Shen Tang', englishName: 'Cinnamon Twig and Ginseng Decoction',
    categoryKey: 'exterior-interior-excess', group: 'Release the Exterior and Warm the Interior', groupEs: 'Liberar el Exterior y calentar el Interior', sourcePages: '298–300',
    kings: 'Gui Zhi, Ren Shen',
    pattern: 'Unresolved Taiyang exterior Wind-Cold with Cold obstructing Taiyin after damage to Spleen and Stomach Yang.',
    patternEs: 'Viento-Frío exterior Taiyang no resuelto con Frío que obstruye Taiyin tras lesión del Yang de Bazo y Estómago.',
    action: 'Releases the exterior, warms the interior, augments Qi, and eliminates focal distention.',
    actionEs: 'Libera el Exterior, calienta el Interior, fortalece Qi y elimina distensión focal.',
    clue: 'Mild exterior fever, chills, headache or joint pain accompanied by severe watery odorless diarrhea and hard focal epigastric distention; abdominal pain improves with warmth.',
    clueEs: 'Fiebre, escalofríos, cefalea o dolor articular exteriores leves acompañados de diarrea acuosa intensa sin olor y distensión epigástrica focal dura; el dolor abdominal mejora con calor.',
    ingredients: 'Gui Zhi 12 g, Zhi Gan Cao 12 g, Bai Zhu 9 g, Ren Shen 9 g, Gan Jiang 9 g.',
    ingredientsEs: 'Gui Zhi 12 g, Zhi Gan Cao 12 g, Bai Zhu 9 g, Ren Shen 9 g y Gan Jiang 9 g.',
    safetyTier: 'high',
    safety: 'Strongly warming and drying; contraindicated for interior Heat or Yin deficiency. Severe or persistent diarrhea, dehydration, chest pain, or cardiac symptoms require medical assessment.',
    safetyEs: 'Fuertemente caliente y secante; contraindicada en Calor interior o deficiencia de Yin. Diarrea grave o persistente, deshidratación, dolor torácico o síntomas cardiacos requieren evaluación médica.',
    examFocus: 'Cold appears on both sides of the boundary: mild unresolved exterior signs plus severe cold, watery, odorless diarrhea and hard epigastric focal distention.',
    examFocusEs: 'Frío aparece a ambos lados del límite: signos exteriores leves no resueltos más diarrea fría, acuosa, intensa y sin olor y distensión epigástrica focal dura.',
    ingredientRoles: 'Gui Zhi releases Taiyang and helps descend turbid Qi; Ren Shen restores primal Qi and transformation; Gan Jiang warms the Middle; Bai Zhu strengthens the Spleen, dries Dampness, and stops diarrhea; Zhi Gan Cao supports the Spleen and harmonizes.',
    ingredientRolesEs: 'Gui Zhi libera Taiyang y ayuda a descender Qi turbio; Ren Shen restaura Qi primordial y transformación; Gan Jiang calienta el Centro; Bai Zhu fortalece Bazo, seca Humedad y detiene diarrea; Zhi Gan Cao apoya Bazo y armoniza.',
    comparison: 'Ge Gen Huang Qin Huang Lian Tang has foul hot diarrhea, anal burning, thirst, a red-yellow tongue, and rapid pulse. Gui Zhi Ren Shen Tang has watery odorless diarrhea, deficient Cold, and pain relieved by warmth.',
    comparisonEs: 'Ge Gen Huang Qin Huang Lian Tang presenta diarrea caliente y fétida, ardor anal, sed, lengua roja-amarilla y pulso rápido. Gui Zhi Ren Shen Tang presenta diarrea acuosa sin olor, Frío deficiente y dolor aliviado por calor.',
    modifications: [
      ['More severe interior Cold', 'Add Zhi Fu Zi.', 'Frío interior más grave', 'Añadir Zhi Fu Zi.'],
      ['Abdominal pain', 'Add Bai Shao.', 'Dolor abdominal', 'Añadir Bai Shao.'],
      ['Incessant diarrhea', 'Add Huang Qi and Sheng Ma; assess urgently for dehydration.', 'Diarrea incesante', 'Añadir Huang Qi y Sheng Ma; evaluar urgentemente la deshidratación.']
    ]
  }
];

export const exteriorInteriorExcessCategoryGuide = {
  priorityTitle: 'Supplementary Chapter 7: identify both sides of the boundary',
  priorityTitleEs: 'Capítulo suplementario 7: identifica ambos lados del límite',
  priorityText: 'These six principal formulas come from the supplied Chapter 7 source (pages 285–301). They extend the guide without changing the official 63-formula Board roster or its practice totals.',
  priorityTextEs: 'Estas seis fórmulas principales proceden del capítulo 7 aportado (páginas 285–301). Amplían la guía sin modificar el listado Board oficial de 63 fórmulas ni sus totales de práctica.',
  routeTitle: 'Choose by the interior treatment required',
  routeTitleEs: 'Elige según el tratamiento interior requerido',
  route: [
    ['1', 'Purge clumped interior Heat', 'Da Chai Hu Tang combines Shaoyang harmonization with Yangming purging; Fang Feng Tong Sheng San opens sweat, urine, and stool.', 'Purgar Calor interior acumulado', 'Da Chai Hu Tang combina armonización Shaoyang con purgación Yangming; Fang Feng Tong Sheng San abre sudor, orina y heces.'],
    ['2', 'Clear blazing interior Heat', 'Ge Gen Huang Qin Huang Lian Tang centers on hot diarrhea; Shi Gao Tang on severe systemic Heat trapped behind an unsweating exterior.', 'Aclarar Calor interior ardiente', 'Ge Gen Huang Qin Huang Lian Tang se centra en diarrea caliente; Shi Gao Tang en Calor sistémico grave atrapado tras un Exterior sin sudor.'],
    ['3', 'Warm interior Cold', 'Wu Ji San treats five Cold accumulations; Gui Zhi Ren Shen Tang treats severe cold diarrhea with mild unresolved exterior signs.', 'Calentar Frío interior', 'Wu Ji San trata cinco acumulaciones por Frío; Gui Zhi Ren Shen Tang trata diarrea fría intensa con signos exteriores leves no resueltos.']
  ],
  quickCompareTitle: 'Chapter 7 principal formulas',
  quickCompareTitleEs: 'Fórmulas principales del capítulo 7',
  quickCompare: [
    ['Da Chai Hu Tang', 'Shaoyang plus Yangming clumping', 'Alternating fever/chills · continuous vomiting · hard epigastrium', 'Shaoyang más acumulación Yangming', 'Fiebre/escalofríos alternantes · vómito continuo · epigastrio duro'],
    ['Fang Feng Tong Sheng San', 'Exterior and interior Heat excess', 'Strong Wind-Heat · constipation · dark urine · greasy yellow coat', 'Exceso de Calor exterior e interior', 'Viento-Calor fuerte · estreñimiento · orina oscura · saburra amarilla grasosa'],
    ['Ge Gen Huang Qin Huang Lian Tang', 'Lingering exterior with intestinal Damp-Heat', 'Foul hot diarrhea · anal burning · thirst · rapid pulse', 'Exterior persistente con Humedad-Calor intestinal', 'Diarrea caliente fétida · ardor anal · sed · pulso rápido'],
    ['Shi Gao Tang', 'Exterior Cold with blazing interior Heat', 'No sweat · red face/eyes · possible bleeding · flooding pulse', 'Frío exterior con Calor interior ardiente', 'Sin sudor · cara/ojos rojos · posible sangrado · pulso desbordante'],
    ['Wu Ji San', 'Wind-Cold plus five internal accumulations', 'Cold pain · chest-abdominal fullness · white greasy coat', 'Viento-Frío más cinco acumulaciones internas', 'Dolor frío · plenitud toracoabdominal · saburra blanca grasosa'],
    ['Gui Zhi Ren Shen Tang', 'Taiyang exterior plus Taiyin Cold', 'Mild exterior signs · watery odorless diarrhea · hard epigastrium', 'Exterior Taiyang más Frío Taiyin', 'Signos exteriores leves · diarrea acuosa sin olor · epigastrio duro']
  ],
  referencesTitle: 'Variations, associated formulas, and safety boundaries',
  referencesTitleEs: 'Variaciones, fórmulas asociadas y límites de seguridad',
  referencesText: 'The branches below support differentiation and do not create additional principal cards. Acute abdominal, infectious, bleeding, neurologic, or cardiopulmonary presentations require appropriate medical care.',
  referencesTextEs: 'Las ramas siguientes apoyan la diferenciación y no crean tarjetas principales adicionales. Los cuadros abdominales, infecciosos, hemorrágicos, neurológicos o cardiopulmonares agudos requieren atención médica apropiada.',
  referenceGroups: [
    {
      title: 'Purge and double-release branches', titleEs: 'Ramas de purgación y doble liberación',
      items: [
        ['Hou Po Qi Wu / Jia Wei Da Chai Hu Tang', 'Shift emphasis toward Taiyang-Yangming fullness or a menstrual Shaoyang obstruction.', 'Desplazan el énfasis hacia plenitud Taiyang-Yangming u obstrucción Shaoyang menstrual.'],
        ['Qing Yi Tang', 'A modern acute-abdomen branch derived from Da Chai Hu Tang; not a substitute for emergency pancreatitis care.', 'Rama moderna de abdomen agudo derivada de Da Chai Hu Tang; no sustituye atención urgente de pancreatitis.'],
        ['Shuang Jie Tong Sheng San', 'Removes the purgatives when constipation is absent.', 'Retira los purgantes cuando no hay estreñimiento.']
      ]
    },
    {
      title: 'Exterior-clear-Heat branches', titleEs: 'Ramas Exterior-aclarar Calor',
      items: [
        ['Shi Gao Tang from Six Texts', 'Adds Sheng Jiang, Da Zao, and green tea for generalized pain.', 'Añade Sheng Jiang, Da Zao y té verde para dolor generalizado.'],
        ['Shuang Jie Jia Cong Chi Tang', 'Vents warm-pathogen Heat through sweat and urine before clumping.', 'Ventila Calor de patógeno tibio por sudor y orina antes de la acumulación.'],
        ['Liu Shen Tong Jie San', 'Damp-Heat in the Triple Burner with exterior Wind-Cold.', 'Humedad-Calor del Triple Calentador con Viento-Frío exterior.']
      ]
    },
    {
      title: 'Cold-side separator', titleEs: 'Separador del lado Frío',
      items: [
        ['Wu Ji San', 'Broad Cold-Damp-Phlegm-Qi-Blood accumulation with a white greasy coat.', 'Acumulación amplia de Frío-Humedad-Flema-Qi-Sangre con saburra blanca grasosa.'],
        ['Gui Zhi Ren Shen Tang', 'A narrower deficiency-Cold pattern: watery odorless diarrhea and hard epigastric focal distention.', 'Patrón más estrecho de Frío-deficiencia: diarrea acuosa sin olor y distensión epigástrica focal dura.']
      ]
    }
  ]
};
