export const warmInteriorColdCategory = {
  number: 'S2',
  key: 'warm-interior-cold',
  name: 'Warm Interior Cold',
  nameEs: 'Calentar el Interior y dispersar Frío',
  sourceChapter: 6,
  sourcePages: '251–282'
};

const emergencySafety = {
  safetyTier: 'critical',
  safety: 'This source describes an emergency pattern, not a self-treatment protocol. Collapse, shock, severe breathing difficulty, altered consciousness, or an almost imperceptible pulse requires emergency medical care. Fu Zi is toxic unless correctly processed and professionally prescribed.',
  safetyEs: 'La fuente describe un patrón de emergencia, no un protocolo de autotratamiento. Colapso, shock, dificultad respiratoria grave, alteración de conciencia o pulso casi imperceptible requieren atención médica urgente. Fu Zi es tóxico si no está correctamente procesado y prescrito por un profesional.'
};

export const warmInteriorColdFormulas = [
  {
    number: 'W1', sequence: 1, name: 'Dang Gui Si Ni Tang', englishName: 'Tang-kuei Decoction for Frigid Extremities',
    categoryKey: 'warm-interior-cold', group: 'Warm the Channels and Disperse Cold', groupEs: 'Calentar los canales y dispersar Frío', sourcePages: '252–255',
    kings: 'Dang Gui, Gui Zhi',
    pattern: 'Cold congealing in the channels in a patient with underlying Blood and nutritive-Qi deficiency.',
    patternEs: 'Frío que se congela en los canales sobre una deficiencia subyacente de Sangre y Qi nutritivo.',
    action: 'Warms the channels, disperses Cold, nourishes Blood, and unblocks the vessels.',
    actionEs: 'Calienta los canales, dispersa Frío, nutre Sangre y desbloquea los vasos.',
    clue: 'Long-standing cold hands and feet, often to the wrists and ankles, pale tongue with white coat, and a submerged, thin, almost imperceptible pulse.',
    clueEs: 'Manos y pies fríos desde hace tiempo, a menudo hasta muñecas y tobillos, lengua pálida con saburra blanca y pulso sumergido, fino y casi imperceptible.',
    ingredients: 'Dang Gui 9 g, Shao Yao 9 g, Gui Zhi 9 g, Xi Xin 6 g, Zhi Gan Cao 6 g, Da Zao 4–5 pieces (modern), Mu Tong 6 g.',
    ingredientsEs: 'Dang Gui 9 g, Shao Yao 9 g, Gui Zhi 9 g, Xi Xin 6 g, Zhi Gan Cao 6 g, Da Zao 4–5 piezas (uso moderno) y Mu Tong 6 g.',
    safety: 'Use caution in warm seasons or climates because the formula can injure fluids. Contraindicated for Fire from Yin deficiency. Xi Xin and Mu Tong require correct species identification and professional dosing.',
    safetyEs: 'Usar con cautela en estaciones o climas cálidos porque puede lesionar líquidos. Contraindicada en Fuego por deficiencia de Yin. Xi Xin y Mu Tong requieren identificación correcta de especie y dosificación profesional.',
    safetyTier: 'high',
    examFocus: 'The entire hands and feet are chronically cold and painful because Blood is deficient and obstructed by Cold—not because Yang has collapsed.',
    examFocusEs: 'Manos y pies completos están crónicamente fríos y dolorosos porque la Sangre es deficiente y está obstruida por Frío, no por colapso de Yang.',
    ingredientRoles: 'Dang Gui and Shao Yao nourish Blood; Gui Zhi and Xi Xin warm and disperse Cold; Gui Zhi with Shao Yao harmonizes nutritive and protective Qi; Gan Cao and Da Zao support the Middle; Mu Tong opens channels and vessels.',
    ingredientRolesEs: 'Dang Gui y Shao Yao nutren Sangre; Gui Zhi y Xi Xin calientan y dispersan Frío; Gui Zhi con Shao Yao armoniza Qi nutritivo y protector; Gan Cao y Da Zao apoyan el Centro; Mu Tong abre canales y vasos.',
    comparison: 'Si Ni Tang has whole-limb cold, lethargy, watery diarrhea, and Yang collapse. Si Ni San usually has only cold fingertips/toes with a wiry pulse and internal Heat or constraint.',
    comparisonEs: 'Si Ni Tang presenta frío de extremidades completas, letargo, diarrea acuosa y colapso de Yang. Si Ni San suele limitarse a puntas frías con pulso de cuerda y Calor o constricción interna.',
    modifications: [
      ['Persistent mild headache, vertigo, and tinnitus', 'Add Bai Zhi and Fang Feng.', 'Cefalea leve persistente, vértigo y tinnitus', 'Añadir Bai Zhi y Fang Feng.'],
      ['Cold lower-abdominal menstrual pain with scant flow', 'Add Yi Mu Cao and Xiang Fu.', 'Dolor menstrual frío en abdomen inferior con flujo escaso', 'Añadir Yi Mu Cao y Xiang Fu.'],
      ['Chronic resistant sciatica', 'Add Zhi Fu Zi, Da Huang, Xu Duan, and Gou Ji.', 'Ciática crónica resistente', 'Añadir Zhi Fu Zi, Da Huang, Xu Duan y Gou Ji.']
    ],
    variations: [
      ['Dang Gui Si Ni Jia Wu Zhu Yu Sheng Jiang Tang', 'Adds Wu Zhu Yu and Sheng Jiang for chronic Middle-Burner or Ren/Chong Cold with loose stools, nausea, vomiting, and abdominal pain.', 'Añade Wu Zhu Yu y Sheng Jiang para Frío crónico del Jiao Medio o Ren/Chong con heces sueltas, náuseas, vómitos y dolor abdominal.']
    ]
  },
  {
    number: 'W2', sequence: 2, name: 'Huang Qi Gui Zhi Wu Wu Tang', englishName: 'Astragalus and Cinnamon Twig Five-Substance Decoction',
    categoryKey: 'warm-interior-cold', group: 'Warm the Channels and Disperse Cold', groupEs: 'Calentar los canales y dispersar Frío', sourcePages: '255–257',
    kings: 'Huang Qi, Gui Zhi',
    pattern: 'Blood painful obstruction: nutritive and protective Qi deficiency allows Wind-Cold to lodge superficially in the vessels.',
    patternEs: 'Obstrucción dolorosa de la Sangre: la deficiencia de Qi nutritivo y protector permite que Viento-Frío se aloje superficialmente en los vasos.',
    action: 'Augments Qi, warms and harmonizes the channels, and unblocks painful obstruction.',
    actionEs: 'Fortalece Qi, calienta y armoniza los canales y desbloquea la obstrucción dolorosa.',
    clue: 'Relatively superficial numbness or paresthesia, little pain, and a faint, choppy, tight pulse; the tongue may be pale and puffy.',
    clueEs: 'Entumecimiento o parestesias relativamente superficiales, poco dolor y pulso débil, áspero y tenso; la lengua puede ser pálida e hinchada.',
    ingredients: 'Huang Qi 9–12 g, Shao Yao 9 g, Gui Zhi 9 g, Sheng Jiang 12–18 g, Da Zao 12 pieces (source).',
    ingredientsEs: 'Huang Qi 9–12 g, Shao Yao 9 g, Gui Zhi 9 g, Sheng Jiang 12–18 g y Da Zao 12 piezas (fuente).',
    safety: 'Not a formula for an acute stroke. Sudden weakness, paralysis, facial droop, or speech difficulty requires emergency assessment.',
    safetyEs: 'No es una fórmula para un accidente cerebrovascular agudo. Debilidad súbita, parálisis, caída facial o dificultad para hablar requieren evaluación urgente.',
    safetyTier: 'high',
    examFocus: 'Think superficial numbness from deficient Qi and Blood, not fixed severe pain and not acute neurologic loss.',
    examFocusEs: 'Piensa en entumecimiento superficial por deficiencia de Qi y Sangre, no en dolor fijo intenso ni pérdida neurológica aguda.',
    ingredientRoles: 'Huang Qi and Gui Zhi jointly tonify Qi and warm the channels; Shao Yao nourishes Blood; the large Sheng Jiang dose opens pores and moves vessels; Da Zao harmonizes nutritive and protective Qi.',
    ingredientRolesEs: 'Huang Qi y Gui Zhi tonifican Qi y calientan canales; Shao Yao nutre Sangre; la dosis grande de Sheng Jiang abre poros y mueve vasos; Da Zao armoniza Qi nutritivo y protector.',
    comparison: 'Dang Gui Si Ni Tang emphasizes Blood deficiency with chronically cold painful hands and feet. This formula emphasizes superficial numbness from Qi deficiency and Wind-Cold in the vessels.',
    comparisonEs: 'Dang Gui Si Ni Tang enfatiza deficiencia de Sangre con manos y pies crónicamente fríos y dolorosos. Esta fórmula enfatiza entumecimiento superficial por deficiencia de Qi y Viento-Frío en vasos.',
    modifications: [
      ['More pronounced Qi deficiency', 'Increase Huang Qi and add Dang Shen.', 'Deficiencia de Qi más pronunciada', 'Aumentar Huang Qi y añadir Dang Shen.'],
      ['Blood deficiency', 'Add Dang Gui and Ji Xue Teng.', 'Deficiencia de Sangre', 'Añadir Dang Gui y Ji Xue Teng.'],
      ['Yang deficiency with frigid extremities', 'Add Zhi Fu Zi.', 'Deficiencia de Yang con extremidades gélidas', 'Añadir Zhi Fu Zi.'],
      ['Blood stasis', 'Add Hong Hua and Tao Ren.', 'Estasis de Sangre', 'Añadir Hong Hua y Tao Ren.']
    ]
  },
  {
    number: 'W3', sequence: 3, name: 'Li Zhong Wan', englishName: 'Regulate the Middle Pill',
    categoryKey: 'warm-interior-cold', group: 'Warm the Middle and Dispel Cold', groupEs: 'Calentar el Jiao Medio y dispersar Frío', sourcePages: '257–261',
    kings: 'Gan Jiang',
    pattern: 'Middle-Burner deficiency-Cold, also called Middle-Burner Yang deficiency.',
    patternEs: 'Frío por deficiencia del Jiao Medio, también llamado deficiencia de Yang del Jiao Medio.',
    action: 'Warms the Middle Burner and strengthens the Spleen and Stomach.',
    actionEs: 'Calienta el Jiao Medio y fortalece Bazo y Estómago.',
    clue: 'Watery diarrhea, nausea or vomiting, no particular thirst, low appetite, abdominal pain, pale tongue with white coat, and a submerged thin pulse.',
    clueEs: 'Diarrea acuosa, náuseas o vómitos, sin sed particular, poco apetito, dolor abdominal, lengua pálida con saburra blanca y pulso sumergido y fino.',
    ingredients: 'Gan Jiang 9 g, Ren Shen 9 g, Bai Zhu 9 g, Zhi Gan Cao 9 g. Dang Shen commonly replaces Ren Shen in nonacute modern use.',
    ingredientsEs: 'Gan Jiang 9 g, Ren Shen 9 g, Bai Zhu 9 g y Zhi Gan Cao 9 g. Dang Shen sustituye con frecuencia a Ren Shen en uso moderno no agudo.',
    safety: 'Not for externally contracted fever, Yin deficiency, or true Heat. In sudden vomiting and diarrhea, discontinue once the acute Cold presentation resolves.',
    safetyEs: 'No corresponde a fiebre contraída externamente, deficiencia de Yin ni Calor verdadero. En vómitos y diarrea súbitos, suspender cuando se resuelva el cuadro agudo de Frío.',
    safetyTier: 'high',
    examFocus: 'A soft lax abdomen, mild chronic pain relieved by warmth, watery stool, no thirst, and a submerged thin pulse point to Middle deficiency-Cold.',
    examFocusEs: 'Abdomen blando y laxo, dolor crónico leve aliviado por calor, heces acuosas, ausencia de sed y pulso sumergido-fino apuntan a Frío-deficiencia del Centro.',
    ingredientRoles: 'Gan Jiang warms Spleen/Stomach Yang; Ren Shen restores source Qi and ascent/descent; Bai Zhu strengthens the Spleen and dries Dampness; Zhi Gan Cao supports the Middle and harmonizes.',
    ingredientRolesEs: 'Gan Jiang calienta Yang de Bazo/Estómago; Ren Shen restaura Qi fuente y ascenso/descenso; Bai Zhu fortalece Bazo y seca Humedad; Zhi Gan Cao apoya el Centro y armoniza.',
    comparison: 'Hou Po Wen Zhong Tang treats excess Damp-Cold with marked distention but does not tonify. Si Ni Tang treats deeper Yang collapse with incessant diarrhea and obligatorily cold extremities.',
    comparisonEs: 'Hou Po Wen Zhong Tang trata Humedad-Frío de exceso con distensión marcada, pero no tonifica. Si Ni Tang trata colapso más profundo de Yang con diarrea incesante y extremidades necesariamente frías.',
    modifications: [
      ['Severe vomiting', 'Add Sheng Jiang.', 'Vómitos intensos', 'Añadir Sheng Jiang.'],
      ['Yang-deficiency bleeding', 'Use Pao Jiang and add Huang Qi, Dang Gui, and E Jiao.', 'Sangrado por deficiencia de Yang', 'Usar Pao Jiang y añadir Huang Qi, Dang Gui y E Jiao.'],
      ['Severe Cold with cold extremities', 'Add Zhi Fu Zi and Rou Gui.', 'Frío intenso con extremidades frías', 'Añadir Zhi Fu Zi y Rou Gui.'],
      ['Edema with reduced urination', 'Combine with Wu Ling San.', 'Edema con micción reducida', 'Combinar con Wu Ling San.']
    ],
    variations: [
      ['Lian Li Tang', 'Adds Huang Lian when constrained fluids transform into Damp-Heat with sour vomiting, thirst, reduced urine, and mouth ulcers.', 'Añade Huang Lian cuando los líquidos constreñidos se transforman en Humedad-Calor con vómito ácido, sed, poca orina y úlceras bucales.'],
      ['Ding Yu Li Zhong Tang', 'Adds Ding Xiang and Wu Zhu Yu for pediatric Stomach Cold with delayed vomiting of undigested food.', 'Añade Ding Xiang y Wu Zhu Yu para Frío de Estómago pediátrico con vómito tardío de alimento no digerido.'],
      ['Gui Fu Li Zhong Tang', 'Adds Rou Gui and Zhi Fu Zi for severe Spleen, Stomach, and Kidney deficiency-Cold with daybreak diarrhea.', 'Añade Rou Gui y Zhi Fu Zi para Frío-deficiencia grave de Bazo, Estómago y Riñón con diarrea al amanecer.']
    ],
    associated: [
      ['Fu Zi Li Zhong Wan', 'More severe internal Cold with pain, inability to retain food, very cold extremities, mild sweating, and a faint pulse.', 'Frío interno más grave con dolor, incapacidad de retener alimento, extremidades muy frías, sudor leve y pulso débil.'],
      ['Zhi Shi Li Zhong Wan', 'Adds Zhi Shi and Fu Ling for deficiency-Cold with abdominal fullness, distention, and clumping.', 'Añade Zhi Shi y Fu Ling para Frío-deficiencia con plenitud, distensión y acumulación abdominal.']
    ]
  },
  {
    number: 'W4', sequence: 4, name: 'Wu Zhu Yu Tang', englishName: 'Evodia Decoction',
    categoryKey: 'warm-interior-cold', group: 'Warm the Middle and Dispel Cold', groupEs: 'Calentar el Jiao Medio y dispersar Frío', sourcePages: '261–264',
    kings: 'Wu Zhu Yu',
    pattern: 'Stomach deficiency-Cold with Liver involvement and rebellious Qi.',
    patternEs: 'Frío por deficiencia de Estómago con participación de Hígado y Qi rebelde.',
    action: 'Warms and tonifies the Liver and Stomach, directs rebellious Qi downward, and stops vomiting.',
    actionEs: 'Calienta y tonifica Hígado y Estómago, dirige Qi rebelde hacia abajo y detiene el vómito.',
    clue: 'Vomiting or dry heaves with clear fluids, vertex headache, cold extremities, white slippery coat, and a thin slow or thin wiry pulse.',
    clueEs: 'Vómitos o arcadas secas con líquidos claros, cefalea de vértex, extremidades frías, saburra blanca resbaladiza y pulso fino-lento o fino-de cuerda.',
    ingredients: 'Wu Zhu Yu 9–12 g, Sheng Jiang 18 g, Ren Shen 9 g, Da Zao 12 pieces (source; modern dosing is commonly lower).',
    ingredientsEs: 'Wu Zhu Yu 9–12 g, Sheng Jiang 18 g, Ren Shen 9 g y Da Zao 12 piezas (fuente; la dosificación moderna suele ser menor).',
    safety: 'Contraindicated for vomiting or acid reflux due to Heat. Severe vomiting may require urgent evaluation; the source notes that the cooled decoction and small sips can be easier to retain.',
    safetyEs: 'Contraindicada en vómito o reflujo ácido por Calor. El vómito grave puede requerir evaluación urgente; la fuente señala que la decocción fría y sorbos pequeños pueden tolerarse mejor.',
    safetyTier: 'high',
    examFocus: 'Vertex headache plus nausea, clear salivation, cold limbs, and a nonred tongue separates this Cold-rebellion formula from Heat headache patterns.',
    examFocusEs: 'Cefalea de vértex más náuseas, salivación clara, extremidades frías y lengua no roja separan esta fórmula de rebelión por Frío de cefaleas por Calor.',
    ingredientRoles: 'Wu Zhu Yu warms the Liver, Stomach, and Spleen and descends rebellious Qi; Sheng Jiang warms the Stomach and disperses thin fluids; Ren Shen strengthens the Middle and fluids; Da Zao moderates dryness.',
    ingredientRolesEs: 'Wu Zhu Yu calienta Hígado, Estómago y Bazo y desciende Qi rebelde; Sheng Jiang calienta Estómago y dispersa líquidos claros; Ren Shen fortalece Centro y líquidos; Da Zao modera la sequedad.',
    comparison: 'Li Zhong Wan centers on diarrhea and Middle deficiency-Cold. Wu Zhu Yu Tang centers on rebellious Stomach Qi—especially vomiting, clear fluids, and vertex headache with Liver involvement.',
    comparisonEs: 'Li Zhong Wan se centra en diarrea y Frío-deficiencia del Centro. Wu Zhu Yu Tang se centra en Qi de Estómago rebelde, especialmente vómitos, líquidos claros y cefalea de vértex con participación de Hígado.',
    modifications: [
      ['Severe vomiting or morning sickness', 'Add Zhi Ban Xia and Sha Ren.', 'Vómitos graves o del embarazo', 'Añadir Zhi Ban Xia y Sha Ren.'],
      ['Severe headache', 'Add Chuan Xiong and Dang Gui.', 'Cefalea intensa', 'Añadir Chuan Xiong y Dang Gui.'],
      ['Severe Cold', 'Add Hua Jiao and Gan Jiang.', 'Frío intenso', 'Añadir Hua Jiao y Gan Jiang.'],
      ['Acid reflux', 'Add Wa Leng Zi and Hai Piao Xiao.', 'Reflujo ácido', 'Añadir Wa Leng Zi y Hai Piao Xiao.']
    ],
    associated: [
      ['Yan Nian Ban Xia Tang', 'Internal Cold with stagnant fluids and Phlegm blocking descent, often with left-sided chest, shoulder, or hypochondriac pain.', 'Frío interno con líquidos estancados y Flema que bloquea el descenso, a menudo con dolor izquierdo de pecho, hombro o hipocondrio.']
    ]
  },
  {
    number: 'W5', sequence: 5, name: 'Xiao Jian Zhong Tang', englishName: 'Minor Construct the Middle Decoction',
    categoryKey: 'warm-interior-cold', group: 'Warm the Middle and Dispel Cold', groupEs: 'Calentar el Jiao Medio y dispersar Frío', sourcePages: '264–268',
    kings: 'Yi Tang',
    pattern: 'Consumptive Middle-Burner deficiency-Cold with insufficient transformation and disharmony of Yin/Yang and nutritive/protective Qi.',
    patternEs: 'Frío por deficiencia consuntiva del Jiao Medio con transformación insuficiente y desarmonía de Yin/Yang y Qi nutritivo/protector.',
    action: 'Warms and tonifies the Middle Burner and moderates spasmodic abdominal pain.',
    actionEs: 'Calienta y tonifica el Jiao Medio y modera el dolor abdominal espasmódico.',
    clue: 'Intermittent spasmodic abdominal pain relieved by warmth and pressure, low appetite, pale tongue with white coat, and a thin, wiry, moderate pulse.',
    clueEs: 'Dolor abdominal espasmódico intermitente aliviado por calor y presión, poco apetito, lengua pálida con saburra blanca y pulso fino, de cuerda y moderado.',
    ingredients: 'Yi Tang 18–30 g, Gui Zhi 9 g, Bai Shao 18 g, Zhi Gan Cao 6 g, Sheng Jiang 9 g, Da Zao 3–5 pieces (modern).',
    ingredientsEs: 'Yi Tang 18–30 g, Gui Zhi 9 g, Bai Shao 18 g, Zhi Gan Cao 6 g, Sheng Jiang 9 g y Da Zao 3–5 piezas (uso moderno).',
    safety: 'Contraindicated for Yin-deficiency Heat. Modify for vomiting, roundworms, or abdominal distention. Honey may replace Yi Tang in the source; cane sugar should not.',
    safetyEs: 'Contraindicada en Calor por deficiencia de Yin. Modificar ante vómitos, lombrices o distensión abdominal. La fuente permite miel en lugar de Yi Tang; no azúcar de caña.',
    safetyTier: 'high',
    examFocus: 'Pain comes and goes and improves with warmth and pressure. Low fever can occur, but cool limbs and desire for warm drinks identify deficiency-Cold rather than Yin-deficiency Heat.',
    examFocusEs: 'El dolor va y viene y mejora con calor y presión. Puede haber fiebre baja, pero extremidades frescas y deseo de bebidas calientes identifican Frío-deficiencia, no Calor por deficiencia de Yin.',
    ingredientRoles: 'Yi Tang tonifies Qi and Blood and relaxes spasm; Gui Zhi warms; doubled Bai Shao nourishes Yin and moderates pain; Sheng Jiang and Da Zao support the Middle; Zhi Gan Cao harmonizes and stops spasm.',
    ingredientRolesEs: 'Yi Tang tonifica Qi y Sangre y relaja espasmo; Gui Zhi calienta; Bai Shao duplicada nutre Yin y modera dolor; Sheng Jiang y Da Zao apoyan el Centro; Zhi Gan Cao armoniza y detiene espasmo.',
    comparison: 'Da Jian Zhong Tang treats acute, excruciating pain with intense interior Cold and severe vomiting. Xiao Jian Zhong Tang is milder, intermittent, and shows combined Yin/Yang deficiency.',
    comparisonEs: 'Da Jian Zhong Tang trata dolor agudo insoportable con Frío interior intenso y vómitos graves. Xiao Jian Zhong Tang es más suave, intermitente y muestra deficiencia combinada de Yin/Yang.',
    modifications: [
      ['More severe Cold', 'Substitute Rou Gui for Gui Zhi.', 'Frío más intenso', 'Sustituir Gui Zhi por Rou Gui.'],
      ['Qi stagnation', 'Add Mu Xiang and Ji Nei Jin.', 'Estancamiento de Qi', 'Añadir Mu Xiang y Ji Nei Jin.'],
      ['Diarrhea', 'Add Bai Zhu.', 'Diarrea', 'Añadir Bai Zhu.'],
      ['Fullness or nausea after taking the formula', 'Add Chen Pi and Sha Ren.', 'Plenitud o náuseas tras tomar la fórmula', 'Añadir Chen Pi y Sha Ren.']
    ],
    variations: [
      ['Huang Qi Jian Zhong Tang', 'Adds Huang Qi for stronger Qi deficiency with spontaneous sweating, shortness of breath, occasional fever, and a faint frail pulse.', 'Añade Huang Qi para mayor deficiencia de Qi con sudor espontáneo, disnea, fiebre ocasional y pulso débil y frágil.'],
      ['Gui Qi Jian Zhong Tang', 'Adds Dang Gui and Huang Qi when both Qi and Blood are deficient, including postpartum or postsurgical patterns.', 'Añade Dang Gui y Huang Qi cuando Qi y Sangre son deficientes, incluidos cuadros posparto o posquirúrgicos.'],
      ['Nei Bu Dang Gui Jian Zhong Tang', 'Adds a stronger Blood-nourishing emphasis for postpartum emaciation, persistent tight abdominal pain, pallor, and reduced lactation.', 'Añade mayor énfasis en nutrir Sangre para emaciación posparto, dolor abdominal tenso persistente, palidez y lactancia reducida.']
    ],
    associated: [
      ['An Zhong San', 'Deficiency-Cold with Qi stagnation and Blood stasis causing sharp epigastric pain, nausea, sour regurgitation, and fullness.', 'Frío-deficiencia con estancamiento de Qi y Sangre que causa dolor epigástrico punzante, náuseas, regurgitación ácida y plenitud.']
    ]
  },
  {
    number: 'W6', sequence: 6, name: 'Da Jian Zhong Tang', englishName: 'Major Construct the Middle Decoction',
    categoryKey: 'warm-interior-cold', group: 'Warm the Middle and Dispel Cold', groupEs: 'Calentar el Jiao Medio y dispersar Frío', sourcePages: '268–270',
    kings: 'Hua Jiao',
    pattern: 'Middle-Burner Yang deficiency with vigorous interior Cold ascending and driving Qi upward.',
    patternEs: 'Deficiencia de Yang del Jiao Medio con Frío interior vigoroso que asciende e impulsa Qi hacia arriba.',
    action: 'Warms and tonifies Middle-Burner deficiency, directs rebellious Qi downward, and alleviates pain.',
    actionEs: 'Calienta y tonifica la deficiencia del Jiao Medio, dirige Qi rebelde hacia abajo y alivia el dolor.',
    clue: 'Excruciating epigastric or abdominal pain intolerant of touch, intense cold sensation, severe vomiting, white slippery coat, and a thin tight, slow wiry, or hidden pulse.',
    clueEs: 'Dolor epigástrico o abdominal insoportable que no tolera palpación, frío intenso, vómitos graves, saburra blanca resbaladiza y pulso fino-tenso, lento-de cuerda u oculto.',
    ingredients: 'Hua Jiao 3–9 g, Gan Jiang 12 g, Ren Shen 6 g, Yi Tang 18–30 g.',
    ingredientsEs: 'Hua Jiao 3–9 g, Gan Jiang 12 g, Ren Shen 6 g y Yi Tang 18–30 g.',
    safety: 'Contraindicated with internal clumping, Damp-Heat, or Yin/Blood deficiency. Excruciating abdominal pain, persistent vomiting, or suspected obstruction requires urgent medical evaluation.',
    safetyEs: 'Contraindicada con acumulación interna, Humedad-Calor o deficiencia de Yin/Sangre. Dolor abdominal insoportable, vómitos persistentes o sospecha de obstrucción requieren evaluación médica urgente.',
    safetyTier: 'critical',
    examFocus: 'This is the major formula because Cold and pain are severe, acute, ascending, and accompanied by vomiting—not the intermittent deficiency pain of Xiao Jian Zhong Tang.',
    examFocusEs: 'Es la fórmula mayor porque Frío y dolor son graves, agudos, ascendentes y acompañados de vómitos; no el dolor intermitente por deficiencia de Xiao Jian Zhong Tang.',
    ingredientRoles: 'Hua Jiao powerfully warms and disperses Cold; Gan Jiang reinforces warming and quiets the upsurge; Ren Shen and Yi Tang rebuild Middle Qi, moderate spasm, and protect normal Qi.',
    ingredientRolesEs: 'Hua Jiao calienta potentemente y dispersa Frío; Gan Jiang refuerza el calentamiento y calma el ascenso; Ren Shen e Yi Tang reconstruyen Qi del Centro, moderan espasmo y protegen Qi normal.',
    comparison: 'Xiao Jian Zhong Tang treats milder intermittent pain relieved by warmth with mixed Yin/Yang deficiency. Da Jian Zhong Tang treats primary severe Cold with unbearable pain and rebellious vomiting.',
    comparisonEs: 'Xiao Jian Zhong Tang trata dolor intermitente más leve aliviado por calor con deficiencia mixta de Yin/Yang. Da Jian Zhong Tang trata Frío primario grave con dolor insoportable y vómito rebelde.',
    modifications: [
      ['Roundworms', 'Reduce Yi Tang and add Wu Mei, Bing Lang, and Shi Jun Zi.', 'Lombrices', 'Reducir Yi Tang y añadir Wu Mei, Bing Lang y Shi Jun Zi.'],
      ['Stronger pain relief', 'Add Dan Shen, Yan Hu Suo, and Mu Xiang.', 'Mayor alivio del dolor', 'Añadir Dan Shen, Yan Hu Suo y Mu Xiang.'],
      ['Nausea', 'Add Zhi Ban Xia and Sheng Jiang.', 'Náuseas', 'Añadir Zhi Ban Xia y Sheng Jiang.']
    ],
    associated: [
      ['Fu Zi Jing Mi Tang', 'Focuses more on Stomach Cold with stagnant thin fluids, borborygmus, sharp pain, nausea, vomiting, and possible frigid extremities.', 'Se centra más en Frío de Estómago con líquidos claros estancados, borborigmos, dolor punzante, náuseas, vómitos y posibles extremidades gélidas.']
    ]
  },
  {
    number: 'W7', sequence: 7, name: 'Gan Cao Gan Jiang Tang', englishName: 'Licorice and Ginger Decoction',
    categoryKey: 'warm-interior-cold', group: 'Warm the Middle and Dispel Cold', groupEs: 'Calentar el Jiao Medio y dispersar Frío', sourcePages: '270–272',
    kings: 'Pao Jiang',
    pattern: 'Deficiency-Cold Lung atrophy with failure to disseminate and govern fluids.',
    patternEs: 'Atrofia pulmonar por Frío-deficiencia con falla para difundir y gobernar líquidos.',
    action: 'Warms the Lungs and strengthens the Stomach.',
    actionEs: 'Calienta los Pulmones y fortalece el Estómago.',
    clue: 'Cold extremities, excessive clear saliva, dry throat without thirst, no cough, frequent urination, pale moist uncoated tongue, and a frail or submerged slow pulse.',
    clueEs: 'Extremidades frías, saliva clara excesiva, garganta seca sin sed, ausencia de tos, micción frecuente, lengua pálida húmeda sin saburra y pulso frágil o sumergido-lento.',
    ingredients: 'Zhi Gan Cao 12 g, Pao Jiang 6 g.',
    ingredientsEs: 'Zhi Gan Cao 12 g y Pao Jiang 6 g.',
    safety: 'Differentiate carefully from Heat or fluid depletion. Persistent bleeding, respiratory distress, or marked weakness requires medical assessment.',
    safetyEs: 'Diferenciar cuidadosamente de Calor o agotamiento de líquidos. Sangrado persistente, dificultad respiratoria o debilidad marcada requieren evaluación médica.',
    safetyTier: 'high',
    examFocus: 'The paradox is a dry throat without thirst plus clear saliva without cough: fluids are misdistributed by Cold, not consumed by Heat.',
    examFocusEs: 'La paradoja es garganta seca sin sed más saliva clara sin tos: los líquidos están mal distribuidos por Frío, no consumidos por Calor.',
    ingredientRoles: 'Pao Jiang warms the Lungs and restores chest Yang while being less dispersing than raw Gan Jiang; Zhi Gan Cao tonifies Qi and strengthens the Stomach—nurturing Earth to generate Metal.',
    ingredientRolesEs: 'Pao Jiang calienta Pulmón y restaura Yang torácico siendo menos dispersante que Gan Jiang crudo; Zhi Gan Cao tonifica Qi y fortalece Estómago, nutriendo Tierra para generar Metal.',
    comparison: 'Ma Huang Tang treats excess exterior Lung Cold with fever, chills, cough, and no sweat. This formula treats deficiency-Cold with clear saliva, no cough or thirst, and frequent urination.',
    comparisonEs: 'Ma Huang Tang trata Frío pulmonar exterior de exceso con fiebre, escalofríos, tos y ausencia de sudor. Esta fórmula trata Frío-deficiencia con saliva clara, sin tos ni sed y micción frecuente.',
    modifications: [
      ['Urinary incontinence', 'Add Yi Zhi Ren and Wu Yao.', 'Incontinencia urinaria', 'Añadir Yi Zhi Ren y Wu Yao.'],
      ['Spleen/Stomach deficiency-Cold abdominal pain', 'Add Gao Liang Jiang and Rou Gui.', 'Dolor abdominal por Frío-deficiencia de Bazo/Estómago', 'Añadir Gao Liang Jiang y Rou Gui.'],
      ['Cold-deficiency bleeding', 'Use Pao Jiang rather than unprepared Gan Jiang.', 'Sangrado por Frío-deficiencia', 'Usar Pao Jiang en vez de Gan Jiang sin preparar.']
    ],
    associated: [
      ['Sheng Jiang Gan Cao Tang', 'Adds Sheng Jiang, Ren Shen, and Da Zao for Lung atrophy with cough, clear fluids, dry mouth, and thirst—more Qi deficiency than Yang deficiency.', 'Añade Sheng Jiang, Ren Shen y Da Zao para atrofia pulmonar con tos, líquidos claros, boca seca y sed: más deficiencia de Qi que de Yang.']
    ]
  },
  {
    number: 'W8', sequence: 8, name: 'Bai Zhu Fu Zi Tang', englishName: 'White Atractylodes and Aconite Accessory Root Decoction',
    categoryKey: 'warm-interior-cold', group: 'Warm the Middle and Dispel Cold', groupEs: 'Calentar el Jiao Medio y dispersar Frío', sourcePages: '272–274',
    kings: 'Zhi Fu Zi',
    pattern: 'Wind-Cold-Damp painful obstruction with Dampness predominating on a background of Yang deficiency.',
    patternEs: 'Obstrucción dolorosa por Viento-Frío-Humedad con predominio de Humedad sobre deficiencia de Yang.',
    action: 'Warms the channels, disperses Cold, builds the Middle Burner, and promotes resolution of Dampness.',
    actionEs: 'Calienta los canales, dispersa Frío, fortalece el Jiao Medio y favorece la resolución de Humedad.',
    clue: 'Generalized body pain and stiffness with inability to rotate, hard stools, normal urination, no thirst or nausea, and a floating, thin, moderate pulse.',
    clueEs: 'Dolor y rigidez generalizados con incapacidad para girarse, heces duras, micción normal, sin sed ni náuseas y pulso flotante, fino y moderado.',
    ingredients: 'Bai Zhu 12 g, Zhi Fu Zi 9 g, Sheng Jiang 9 g, Zhi Gan Cao 6 g, Da Zao 12 pieces (source).',
    ingredientsEs: 'Bai Zhu 12 g, Zhi Fu Zi 9 g, Sheng Jiang 9 g, Zhi Gan Cao 6 g y Da Zao 12 piezas (fuente).',
    safety: 'A heating and tonifying formula; do not use when hard stools are caused by Heat or Yin deficiency. Fu Zi must be correctly processed, precooked, and professionally prescribed.',
    safetyEs: 'Fórmula caliente y tonificante; no usar cuando las heces duras se deben a Calor o deficiencia de Yin. Fu Zi debe estar correctamente procesado, precocido y prescrito por un profesional.',
    safetyTier: 'critical',
    examFocus: 'Dampness predominates: stiffness and impaired movement outweigh pain, urination is normal, there is no nausea, and the pulse is floating-thin-moderate.',
    examFocusEs: 'Predomina Humedad: rigidez y limitación de movimiento superan al dolor, la micción es normal, no hay náuseas y el pulso es flotante-fino-moderado.',
    ingredientRoles: 'Zhi Fu Zi mobilizes Yang and disperses Cold; Bai Zhu dries Dampness, tonifies Qi, and checks sweating; Sheng Jiang, Zhi Gan Cao, and Da Zao harmonize nutritive/protective Qi and support Middle transformation.',
    ingredientRolesEs: 'Zhi Fu Zi moviliza Yang y dispersa Frío; Bai Zhu seca Humedad, tonifica Qi y controla sudor; Sheng Jiang, Zhi Gan Cao y Da Zao armonizan Qi nutritivo/protector y apoyan transformación del Centro.',
    comparison: 'Gui Zhi Jia Fu Zi Tang emphasizes exterior Wind with a floating deficient rough pulse. Gan Cao Fu Zi Tang has deeper joint pain, inability to flex or extend, sweating, dyspnea, and urinary difficulty.',
    comparisonEs: 'Gui Zhi Jia Fu Zi Tang enfatiza Viento exterior con pulso flotante deficiente y áspero. Gan Cao Fu Zi Tang presenta dolor articular más profundo, incapacidad de flexionar o extender, sudor, disnea y dificultad urinaria.',
    modifications: [
      ['Wind-Damp painful obstruction with numbness and limited joints', 'Add Sang Zhi and Ji Xue Teng.', 'Obstrucción Viento-Humedad con entumecimiento y articulaciones limitadas', 'Añadir Sang Zhi y Ji Xue Teng.'],
      ['Severe internal Dampness with thick white greasy coat', 'Replace Bai Zhu with Cang Zhu and add Yi Yi Ren.', 'Humedad interna grave con saburra blanca gruesa y grasosa', 'Sustituir Bai Zhu por Cang Zhu y añadir Yi Yi Ren.'],
      ['Sweating from Qi deficiency', 'Add Dang Shen.', 'Sudor por deficiencia de Qi', 'Añadir Dang Shen.']
    ],
    associated: [
      ['Bai Zhu Fu Zi Tang from Arcane Essentials', 'Adds Rou Gui and emphasizes Mingmen Yang for severe bone/joint pain, sweating, dyspnea, urinary difficulty, Wind aversion, and slight edema.', 'Añade Rou Gui y enfatiza Yang de Mingmen para dolor grave de huesos/articulaciones, sudor, disnea, dificultad urinaria, aversión al Viento y edema leve.'],
      ['Yin Chen Fu Zi Gan Jiang Tang', 'Cold-Damp yin-type jaundice after excessive cooling treatment, with cold skin and limbs, epigastric focal distention, diarrhea, and dark lusterless jaundice.', 'Ictericia tipo Yin por Frío-Humedad tras exceso de tratamiento frío, con piel y extremidades frías, distensión epigástrica focal, diarrea e ictericia oscura sin brillo.']
    ]
  },
  {
    number: 'W9', sequence: 9, name: 'Si Ni Tang', englishName: 'Frigid Extremities Decoction',
    categoryKey: 'warm-interior-cold', group: 'Rescue Devastated Yang', groupEs: 'Rescatar el Yang devastado', sourcePages: '274–278',
    kings: 'Zhi Fu Zi',
    pattern: 'Cold inversion, usually a Shaoyin pattern, with devastated Yang unable to warm the limbs or transform food and fluids.',
    patternEs: 'Inversión por Frío, generalmente Shaoyin, con Yang devastado incapaz de calentar extremidades o transformar alimentos y líquidos.',
    action: 'Rescues devastated Yang, warms the Middle Burner, and stops diarrhea.',
    actionEs: 'Rescata el Yang devastado, calienta el Jiao Medio y detiene la diarrea.',
    clue: 'Extremely cold limbs, aversion to Cold, constant sleepiness, vomiting, diarrhea with undigested food, no thirst, pale tongue with white slippery coat, and a submerged faint pulse.',
    clueEs: 'Extremidades extremadamente frías, aversión al Frío, somnolencia constante, vómitos, diarrea con alimento no digerido, sin sed, lengua pálida con saburra blanca resbaladiza y pulso sumergido débil.',
    ingredients: 'Zhi Fu Zi 6–9 g, Gan Jiang 4.5 g (often increased in modern use), Zhi Gan Cao 6 g.',
    ingredientsEs: 'Zhi Fu Zi 6–9 g, Gan Jiang 4.5 g (a menudo aumentado en uso moderno) y Zhi Gan Cao 6 g.',
    ...emergencySafety,
    examFocus: 'Cold must extend through the limbs, often beyond elbows and knees, with lethargy and a pulse that is thin because it is almost absent—not merely Blood-deficient.',
    examFocusEs: 'El Frío debe abarcar las extremidades, a menudo más allá de codos y rodillas, con letargo y pulso fino porque casi desaparece, no solo por deficiencia de Sangre.',
    ingredientRoles: 'Zhi Fu Zi revives Mingmen Yang and reaches all twelve channels; Gan Jiang warms the Middle and supports Spleen transformation; Zhi Gan Cao augments Qi, moderates dryness, and reduces Fu Zi toxicity.',
    ingredientRolesEs: 'Zhi Fu Zi revive Yang de Mingmen y alcanza los doce canales; Gan Jiang calienta el Centro y apoya transformación de Bazo; Zhi Gan Cao fortalece Qi, modera sequedad y reduce toxicidad de Fu Zi.',
    comparison: 'Zhen Wu Tang treats Yang deficiency with water accumulation. Li Zhong Wan treats intermittent Middle-Burner deficiency diarrhea and may have warm limbs; Si Ni Tang has incessant diarrhea and obligatorily cold limbs.',
    comparisonEs: 'Zhen Wu Tang trata deficiencia de Yang con acumulación de agua. Li Zhong Wan trata diarrea intermitente por deficiencia del Centro y puede tener extremidades tibias; Si Ni Tang presenta diarrea incesante y extremidades necesariamente frías.',
    modifications: [
      ['Wind-Damp arthritis', 'Add Rou Gui and Bai Zhu.', 'Artritis por Viento-Humedad', 'Añadir Rou Gui y Bai Zhu.'],
      ['Edema from Spleen/Kidney deficiency-Cold', 'Add Dang Shen, Fu Ling, and Ze Xie.', 'Edema por Frío-deficiencia de Bazo/Riñón', 'Añadir Dang Shen, Fu Ling y Ze Xie.']
    ],
    variations: [
      ['Si Ni Jia Ren Shen Tang', 'Adds Ren Shen when diarrhea stops but the limbs remain cold from severe injury to Qi, fluids, Yin, and Blood.', 'Añade Ren Shen cuando cesa la diarrea pero las extremidades siguen frías por lesión grave de Qi, líquidos, Yin y Sangre.'],
      ['Tong Mai Si Ni Tang', 'Uses more Gan Jiang and Fu Zi for true interior Cold with false exterior Heat and an almost imperceptible pulse.', 'Usa más Gan Jiang y Fu Zi para Frío interior verdadero con Calor exterior falso y pulso casi imperceptible.']
    ],
    associated: [
      ['Bai Tong Tang', 'Adds Cong Bai to unblock vessels when accumulated lower Yin forces weakened Yang outward, causing flushed face with diarrhea and cold limbs.', 'Añade Cong Bai para desbloquear vasos cuando Yin inferior acumulado fuerza Yang debilitado hacia fuera, causando rostro rojo con diarrea y extremidades frías.']
    ]
  },
  {
    number: 'W10', sequence: 10, name: 'Hui Yang Jiu Ji Tang', englishName: 'Restore and Revive the Yang Decoction',
    categoryKey: 'warm-interior-cold', group: 'Rescue Devastated Yang', groupEs: 'Rescatar el Yang devastado', sourcePages: '278–279',
    kings: 'Zhi Fu Zi, Gan Jiang, Rou Gui',
    pattern: 'Pathogenic Cold directly attacking all three Yin warps in a patient with exhausted true Yang and deficient nutritive Qi.',
    patternEs: 'Frío patógeno que ataca directamente los tres niveles Yin en un paciente con Yang verdadero agotado y Qi nutritivo deficiente.',
    action: 'Restores and revives Yang, augments Qi, and revives the pulse.',
    actionEs: 'Restaura y revive Yang, fortalece Qi y recupera el pulso.',
    clue: 'Severe chills, cold limbs, vomiting, diarrhea, abdominal pain, constant sleepiness, pale tongue, and a submerged faint or imperceptible pulse; lips and nails may be cyanotic.',
    clueEs: 'Escalofríos graves, extremidades frías, vómitos, diarrea, dolor abdominal, somnolencia constante, lengua pálida y pulso sumergido débil o imperceptible; labios y uñas pueden estar cianóticos.',
    ingredients: 'Zhi Fu Zi 9 g, Gan Jiang 4.5 g, Rou Gui 3 g, Ren Shen 6 g, Chao Bai Zhu 9 g, Fu Ling 9 g, Chen Pi 6 g, Zhi Gan Cao 4.5 g, Wu Wei Zi 3 g, Zhi Ban Xia 9 g, Sheng Jiang 3 slices; She Xiang is historical/restricted.',
    ingredientsEs: 'Zhi Fu Zi 9 g, Gan Jiang 4.5 g, Rou Gui 3 g, Ren Shen 6 g, Chao Bai Zhu 9 g, Fu Ling 9 g, Chen Pi 6 g, Zhi Gan Cao 4.5 g, Wu Wei Zi 3 g, Zhi Ban Xia 9 g y Sheng Jiang 3 rodajas; She Xiang es histórico/restringido.',
    ...emergencySafety,
    examFocus: 'More than Yang collapse: both protective Yang and nutritive Qi fail, so the pulse becomes submerged, slow, weak, and sometimes absent, with possible cyanosis.',
    examFocusEs: 'Es más que colapso de Yang: fallan Yang protector y Qi nutritivo, por lo que el pulso se vuelve sumergido, lento, débil y a veces ausente, con posible cianosis.',
    ingredientRoles: 'Fu Zi, Gan Jiang, and Rou Gui revive Yang; Ren Shen, Bai Zhu, Fu Ling, and Gan Cao rebuild Middle Qi; Wu Wei Zi secures Yang and helps generate the pulse; Chen Pi and Ban Xia regulate Qi and fluids.',
    ingredientRolesEs: 'Fu Zi, Gan Jiang y Rou Gui reviven Yang; Ren Shen, Bai Zhu, Fu Ling y Gan Cao reconstruyen Qi del Centro; Wu Wei Zi asegura Yang y ayuda a generar pulso; Chen Pi y Ban Xia regulan Qi y líquidos.',
    comparison: 'Si Ni Tang is primarily a hot, dispersing rescue formula. Hui Yang Jiu Ji Tang adds Liu Jun Zi Tang-like tonification and Wu Wei Zi because nutritive Qi and the pulse are also collapsing.',
    comparisonEs: 'Si Ni Tang es principalmente una fórmula caliente y dispersante de rescate. Hui Yang Jiu Ji Tang añade tonificación semejante a Liu Jun Zi Tang y Wu Wei Zi porque Qi nutritivo y pulso también colapsan.',
    modifications: [
      ['Thin sputum or Cold abdominal pain', 'Add salt-fried Wu Zhu Yu.', 'Esputo claro o dolor abdominal por Frío', 'Añadir Wu Zhu Yu frito con sal.'],
      ['Incessant diarrhea', 'Add Huang Qi and Sheng Ma.', 'Diarrea incesante', 'Añadir Huang Qi y Sheng Ma.'],
      ['Incessant vomiting', 'Add fresh Sheng Jiang juice.', 'Vómito incesante', 'Añadir jugo fresco de Sheng Jiang.']
    ],
    variations: [
      ['Hui Yang Jiu Ji Tang from Revised Popular Guide', 'Adds Mai Men Dong to Ren Shen and Wu Wei Zi for a Sheng Mai San-like Qi-and-fluid support; use cautiously because it is cooling.', 'Añade Mai Men Dong a Ren Shen y Wu Wei Zi para apoyo de Qi y líquidos semejante a Sheng Mai San; usar con cautela porque es fresca.']
    ]
  },
  {
    number: 'W11', sequence: 11, name: 'Shen Fu Tang', englishName: 'Ginseng and Aconite Accessory Root Decoction',
    categoryKey: 'warm-interior-cold', group: 'Rescue Devastated Yang', groupEs: 'Rescatar el Yang devastado', sourcePages: '279–281',
    kings: 'Ren Shen',
    pattern: 'Severe source-Qi deficiency with sudden collapse of Yang Qi and abandonment of Qi.',
    patternEs: 'Deficiencia grave de Qi fuente con colapso súbito de Yang Qi y abandono de Qi.',
    action: 'Restores Yang, strongly tonifies source Qi, and rescues Qi from collapse due to devastated Yang.',
    actionEs: 'Restaura Yang, tonifica intensamente Qi fuente y rescata Qi del colapso por Yang devastado.',
    clue: 'Cold extremities, profuse sweating, weak breathing and shortness of breath, dizziness, extreme pallor, pale tongue, and a faint almost imperceptible pulse.',
    clueEs: 'Extremidades frías, sudor profuso, respiración débil y disnea, mareo, palidez extrema, lengua pálida y pulso débil casi imperceptible.',
    ingredients: 'Ren Shen 9 g and Zhi Fu Zi 15 g (modern doses stated by the source; historical doses were higher).',
    ingredientsEs: 'Ren Shen 9 g y Zhi Fu Zi 15 g (dosis modernas indicadas por la fuente; las dosis históricas eran mayores).',
    ...emergencySafety,
    examFocus: 'Sweating, wheezing or weak breath, extreme pallor, and a nearly absent pulse identify Qi abandonment in addition to Yang collapse.',
    examFocusEs: 'Sudor, sibilancia o respiración débil, palidez extrema y pulso casi ausente identifican abandono de Qi además de colapso de Yang.',
    ingredientRoles: 'Ren Shen strongly restores postnatal source Qi in Heart and Lungs and supports fluids; Zhi Fu Zi revives prenatal Yang at Mingmen. Together they reconnect upper and lower burners and support the pulse.',
    ingredientRolesEs: 'Ren Shen restaura intensamente Qi fuente postnatal en Corazón y Pulmón y apoya líquidos; Zhi Fu Zi revive Yang prenatal en Mingmen. Juntos reconectan Jiao Superior e Inferior y sostienen el pulso.',
    comparison: 'Si Ni Tang treats Yang debilitated but not yet abandoned and emphasizes warming dispersion. Shen Fu Tang treats Qi abandonment with sweating, weak breathing, and an almost imperceptible pulse, so it strongly tonifies Qi and fluids.',
    comparisonEs: 'Si Ni Tang trata Yang debilitado pero aún no abandonado y enfatiza dispersión caliente. Shen Fu Tang trata abandono de Qi con sudor, respiración débil y pulso casi imperceptible, por lo que tonifica intensamente Qi y líquidos.',
    modifications: [
      ['Historical shock/cardiac-failure branch', 'The source adds Long Gu, Mu Li, Bai Shao, and Zhi Gan Cao; this is a historical reference, not a substitute for emergency care.', 'Rama histórica para shock/insuficiencia cardiaca', 'La fuente añade Long Gu, Mu Li, Bai Shao y Zhi Gan Cao; es una referencia histórica, no un sustituto de atención urgente.']
    ],
    associated: [
      ['Du Shen Tang', 'Ren Shen alone with Da Zao for acute exhaustion of Qi after severe Blood loss, marked pallor, sweating, weak breathing, and an almost absent pulse.', 'Ren Shen solo con Da Zao para agotamiento agudo de Qi tras pérdida grave de Sangre, palidez marcada, sudor, respiración débil y pulso casi ausente.']
    ]
  }
];

export const warmInteriorColdCategoryGuide = {
  priorityTitle: 'Supplementary Chapter 6: locate the depth of Cold',
  priorityTitleEs: 'Capítulo suplementario 6: localiza la profundidad del Frío',
  priorityText: 'These eleven principal formulas come from the supplied Chapter 6 source (pages 251–282). They extend the guide without changing the official 63-formula Board roster or its practice totals.',
  priorityTextEs: 'Estas once fórmulas principales proceden del capítulo 6 aportado (páginas 251–282). Amplían la guía sin modificar el listado Board oficial de 63 fórmulas ni sus totales de práctica.',
  routeTitle: 'Choose by depth and deficiency',
  routeTitleEs: 'Elige según profundidad y deficiencia',
  route: [
    ['1', 'Channels with Blood/Qi deficiency', 'Dang Gui Si Ni Tang for Blood deficiency and cold painful limbs; Huang Qi Gui Zhi Wu Wu Tang for superficial numbness.', 'Canales con deficiencia de Sangre/Qi', 'Dang Gui Si Ni Tang para deficiencia de Sangre y extremidades frías dolorosas; Huang Qi Gui Zhi Wu Wu Tang para entumecimiento superficial.'],
    ['2', 'Middle-Burner deficiency-Cold', 'Li Zhong Wan centers on diarrhea; Wu Zhu Yu Tang on vomiting and vertex headache; Xiao Jian Zhong Tang on intermittent spasm.', 'Frío-deficiencia del Jiao Medio', 'Li Zhong Wan se centra en diarrea; Wu Zhu Yu Tang en vómitos y cefalea de vértex; Xiao Jian Zhong Tang en espasmo intermitente.'],
    ['3', 'Severe Cold, fluids, or Dampness', 'Da Jian Zhong Tang for excruciating ascending Cold; Gan Cao Gan Jiang Tang for misdistributed clear fluids; Bai Zhu Fu Zi Tang for Damp obstruction.', 'Frío grave, líquidos o Humedad', 'Da Jian Zhong Tang para Frío ascendente insoportable; Gan Cao Gan Jiang Tang para líquidos claros mal distribuidos; Bai Zhu Fu Zi Tang para obstrucción por Humedad.'],
    ['4', 'Devastated Yang', 'Si Ni Tang rescues Yang; Hui Yang Jiu Ji Tang also rebuilds nutritive Qi; Shen Fu Tang targets Qi abandonment with sweating and weak breath.', 'Yang devastado', 'Si Ni Tang rescata Yang; Hui Yang Jiu Ji Tang también reconstruye Qi nutritivo; Shen Fu Tang trata abandono de Qi con sudor y respiración débil.']
  ],
  quickCompareTitle: 'Chapter 6 principal formulas',
  quickCompareTitleEs: 'Fórmulas principales del capítulo 6',
  quickCompare: [
    ['Dang Gui Si Ni Tang', 'Channel Cold plus Blood deficiency', 'Chronically cold painful hands/feet · pale tongue · thin pulse', 'Frío de canales más deficiencia de Sangre', 'Manos/pies crónicamente fríos y dolorosos · lengua pálida · pulso fino'],
    ['Huang Qi Gui Zhi Wu Wu Tang', 'Blood painful obstruction', 'Superficial numbness · little pain · faint-choppy-tight pulse', 'Obstrucción dolorosa de la Sangre', 'Entumecimiento superficial · poco dolor · pulso débil-áspero-tenso'],
    ['Li Zhong Wan', 'Middle deficiency-Cold', 'Watery diarrhea · no thirst · submerged-thin pulse', 'Frío-deficiencia del Centro', 'Diarrea acuosa · sin sed · pulso sumergido-fino'],
    ['Wu Zhu Yu Tang', 'Stomach Cold with Liver rebellion', 'Vomiting clear fluids · vertex headache · cold limbs', 'Frío de Estómago con rebelión de Hígado', 'Vómito de líquidos claros · cefalea de vértex · extremidades frías'],
    ['Xiao Jian Zhong Tang', 'Consumptive Middle deficiency-Cold', 'Intermittent spasm relieved by warmth and pressure', 'Frío-deficiencia consuntiva del Centro', 'Espasmo intermitente aliviado por calor y presión'],
    ['Da Jian Zhong Tang', 'Severe ascending interior Cold', 'Excruciating pain · intense cold · severe vomiting', 'Frío interior ascendente grave', 'Dolor insoportable · frío intenso · vómitos graves'],
    ['Gan Cao Gan Jiang Tang', 'Deficiency-Cold Lung atrophy', 'Clear saliva · dry throat without thirst · no cough', 'Atrofia pulmonar por Frío-deficiencia', 'Saliva clara · garganta seca sin sed · sin tos'],
    ['Bai Zhu Fu Zi Tang', 'Wind-Cold-Damp with Yang deficiency', 'Stiffness over pain · normal urine · floating-thin-moderate pulse', 'Viento-Frío-Humedad con deficiencia de Yang', 'Rigidez sobre dolor · orina normal · pulso flotante-fino-moderado'],
    ['Si Ni Tang', 'Shaoyin Cold inversion', 'Extremely cold limbs · somnolence · undigested diarrhea', 'Inversión por Frío Shaoyin', 'Extremidades gélidas · somnolencia · diarrea con alimento no digerido'],
    ['Hui Yang Jiu Ji Tang', 'Three-Yin Cold with exhausted Yang', 'Cyanosis possible · nutritive-Qi failure · imperceptible pulse', 'Frío de los tres Yin con Yang agotado', 'Posible cianosis · falla de Qi nutritivo · pulso imperceptible'],
    ['Shen Fu Tang', 'Qi abandonment plus Yang collapse', 'Profuse sweat · weak breathing · extreme pallor', 'Abandono de Qi más colapso de Yang', 'Sudor profuso · respiración débil · palidez extrema']
  ],
  referencesTitle: 'Variations, associated formulas, and safety boundaries',
  referencesTitleEs: 'Variaciones, fórmulas asociadas y límites de seguridad',
  referencesText: 'The branches below support differentiation and do not create additional principal cards. Fu Zi formulas and collapse patterns require professional or emergency care.',
  referencesTextEs: 'Las ramas siguientes apoyan la diferenciación y no crean tarjetas principales adicionales. Las fórmulas con Fu Zi y los cuadros de colapso requieren atención profesional o urgente.',
  referenceGroups: [
    {
      title: 'Channel and Middle branches', titleEs: 'Ramas de canales y Jiao Medio',
      items: [
        ['Dang Gui Si Ni Jia Wu Zhu Yu Sheng Jiang Tang', 'Adds chronic Middle/Ren-Chong Cold to the Dang Gui Si Ni Tang picture.', 'Añade Frío crónico de Centro/Ren-Chong al cuadro de Dang Gui Si Ni Tang.'],
        ['Huang/Gui Qi Jian Zhong Tang', 'Increase Qi support, or Qi-and-Blood support, around Xiao Jian Zhong Tang.', 'Aumentan apoyo de Qi, o de Qi y Sangre, alrededor de Xiao Jian Zhong Tang.'],
        ['Fu Zi Jing Mi Tang', 'Stomach Cold with stagnant thin fluids and rebellious Qi.', 'Frío de Estómago con líquidos claros estancados y Qi rebelde.']
      ]
    },
    {
      title: 'Dampness and fluid branches', titleEs: 'Ramas de Humedad y líquidos',
      items: [
        ['Sheng Jiang Gan Cao Tang', 'Qi-deficient Lung atrophy with cough and thirst.', 'Atrofia pulmonar por deficiencia de Qi con tos y sed.'],
        ['Bai Zhu Fu Zi Tang from Arcane Essentials', 'Deeper Mingmen-Yang emphasis with severe joint pain and urinary difficulty.', 'Mayor énfasis en Yang de Mingmen con dolor articular grave y dificultad urinaria.'],
        ['Yin Chen Fu Zi Gan Jiang Tang', 'Cold-Damp yin-type jaundice with cold skin and dark lusterless color.', 'Ictericia tipo Yin por Frío-Humedad con piel fría y color oscuro sin brillo.']
      ]
    },
    {
      title: 'Yang-rescue branches', titleEs: 'Ramas para rescatar Yang',
      items: [
        ['Si Ni Jia Ren Shen / Tong Mai Si Ni Tang', 'Restore injured Qi/fluids, or unblock a nearly absent pulse with false exterior Heat.', 'Restauran Qi/líquidos lesionados o desbloquean pulso casi ausente con Calor exterior falso.'],
        ['Bai Tong Tang', 'Cong Bai opens vessels when lower Yin rejects Yang outward.', 'Cong Bai abre vasos cuando Yin inferior rechaza Yang hacia fuera.'],
        ['Du Shen Tang', 'Qi collapse after acute severe Blood loss; an emergency-care distinction, not self-treatment.', 'Colapso de Qi tras pérdida aguda grave de Sangre; distinción de urgencia, no autotratamiento.']
      ]
    }
  ]
};
