import { writeFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '../study-guide/comparisons');
const cssVersion = '20261026-2';

const pairs = [
  {
    number: 1, slug: 'ma-huang-gui-zhi-dui-yao', title: 'Má Huáng + Guì Zhī', characters: '麻黄 + 桂枝',
    category: { en: 'Exterior-Resolving · Pungent-Warm', es: 'Liberar Exterior · Acre-Tibio' },
    search: 'ma huang gui zhi ephedra cinnamon twig sweating wind cold exterior excess no sweat ma huang tang',
    en: {
      mnemonic: 'The door-opener and the furnace.',
      job: 'Strongly induce sweating and release an excess Wind-Cold Exterior pattern.',
      roles: [['Má Huáng', 'Throws open the pores and strongly induces sweating.'], ['Guì Zhī', 'Warms the nutritive/muscle layer and pushes the release outward.']],
      clues: 'Strong chills, mild fever, no sweating, body aches, wheezing and a floating-tight pulse.',
      formulas: 'Má Huáng Tāng.',
      trap: 'Spontaneous sweating means Exterior deficiency; this strong opening pair is usually the wrong direction.',
      prep: 'Use only with professional dose and duration control.',
      safety: 'Má Huáng requires cardiovascular, blood-pressure, stimulant and MAOI screening. Palpitations, chest pain or severe headache requires urgent assessment.'
    },
    es: {
      mnemonic: 'El abrepuertas y el horno.',
      job: 'Inducir sudoración con fuerza y liberar Viento-Frío Exterior por exceso.',
      roles: [['Má Huáng', 'Abre los poros e induce sudoración con fuerza.'], ['Guì Zhī', 'Calienta capa nutritiva/muscular y empuja la liberación hacia afuera.']],
      clues: 'Escalofríos fuertes, fiebre leve, sin sudor, dolor corporal, sibilancias y pulso flotante-tenso.',
      formulas: 'Má Huáng Tāng.',
      trap: 'Sudor espontáneo significa deficiencia Exterior; esta pareja fuertemente abridora suele ser dirección incorrecta.',
      prep: 'Solo con control profesional de dosis y duración.',
      safety: 'Má Huáng exige revisar sistema cardiovascular, presión, estimulantes e IMAO. Palpitaciones, dolor torácico o cefalea grave requiere urgencia.'
    }
  },
  {
    number: 2, slug: 'ma-huang-xing-ren-dui-yao', title: 'Má Huáng + Xìng Rén', characters: '麻黄 + 杏仁',
    category: { en: 'Exterior-Resolving · Regulate Lung Qi', es: 'Liberar Exterior · Regular Qi de Pulmón' },
    search: 'ma huang xing ren apricot kernel diffuse descend lung qi cough wheezing ma huang tang ma xing shi gan tang',
    en: {
      mnemonic: 'One opens the window; one pushes the air down.',
      job: 'Restore Lung diffusion and descent to stop cough and wheezing in appropriately balanced hot or cold formulas.',
      roles: [['Má Huáng', 'Diffuses and disseminates constrained Lung Qi.'], ['Xìng Rén', 'Descends rebellious Lung Qi and stops cough.']],
      clues: 'Cough and wheezing from failure of Lung Qi to move in both directions.',
      formulas: 'Má Huáng Tāng; Má Xìng Shí Gān Tāng.',
      trap: 'The pair itself is not exclusively hot or cold; the surrounding formula determines thermal direction.',
      prep: 'Xìng Rén is commonly crushed before decoction.',
      safety: 'Xìng Rén is slightly toxic and must remain within professional dosing; Má Huáng requires cardiovascular and interaction screening.'
    },
    es: {
      mnemonic: 'Uno abre la ventana; otro empuja el aire hacia abajo.',
      job: 'Restaurar difusión y descenso de Qi pulmonar para detener tos/sibilancias en fórmulas calientes o frías bien equilibradas.',
      roles: [['Má Huáng', 'Difunde y disemina Qi de Pulmón constreñido.'], ['Xìng Rén', 'Desciende Qi de Pulmón rebelde y detiene tos.']],
      clues: 'Tos y sibilancias por falla del Qi pulmonar para moverse en ambas direcciones.',
      formulas: 'Má Huáng Tāng; Má Xìng Shí Gān Tāng.',
      trap: 'La pareja no es exclusivamente caliente ni fría; la fórmula que la rodea determina dirección térmica.',
      prep: 'Xìng Rén suele triturarse antes de decocción.',
      safety: 'Xìng Rén es ligeramente tóxica y debe mantenerse en dosis profesional; Má Huáng exige revisión cardiovascular e interacciones.'
    }
  },
  {
    number: 3, slug: 'ma-huang-shi-gao-dui-yao', title: 'Má Huáng + Shí Gāo', characters: '麻黄 + 石膏',
    category: { en: 'Clear Lung Heat · Relieve Wheezing', es: 'Aclarar Calor de Pulmón · Aliviar Sibilancias' },
    search: 'ma huang shi gao gypsum lung heat hot wheezing sweating ma xing shi gan da qing long',
    en: {
      mnemonic: 'A fire hose aimed at the Lung.',
      job: 'Diffuse Lung Qi while strongly clearing Lung Heat for hot wheezing.',
      roles: [['Má Huáng', 'Opens and diffuses constrained Lung Qi.'], ['Shí Gāo', 'Clears blazing Lung Heat and restrains the sweating tendency when used in the larger dose.']],
      clues: 'Fever, thirst, cough and wheezing with Heat signs, with or without sweating.',
      formulas: 'Má Xìng Shí Gān Tāng; Dà Qīng Lóng Tāng.',
      trap: 'Do not label the pair simply warming because it contains Má Huáng; the larger Shí Gāo dose redirects it toward Lung Heat.',
      prep: 'Crush Shí Gāo and decoct it first.',
      safety: 'Má Huáng requires cardiovascular and drug-interaction screening. Severe wheezing or breathing difficulty is an emergency.'
    },
    es: {
      mnemonic: 'Una manguera contra el fuego dirigida al Pulmón.',
      job: 'Difundir Qi de Pulmón mientras aclara con fuerza Calor pulmonar para sibilancia caliente.',
      roles: [['Má Huáng', 'Abre y difunde Qi de Pulmón constreñido.'], ['Shí Gāo', 'Aclara Calor ardiente de Pulmón y limita sudoración cuando domina en dosis.']],
      clues: 'Fiebre, sed, tos y sibilancias con signos de Calor, con o sin sudor.',
      formulas: 'Má Xìng Shí Gān Tāng; Dà Qīng Lóng Tāng.',
      trap: 'No clasificarla solo como caliente por Má Huáng; la dosis mayor de Shí Gāo la redirige a Calor de Pulmón.',
      prep: 'Triturar Shí Gāo y decoctarlo primero.',
      safety: 'Má Huáng exige revisión cardiovascular y farmacológica. Sibilancia grave o dificultad respiratoria es emergencia.'
    }
  },
  {
    number: 4, slug: 'gui-zhi-bai-shao-dui-yao', title: 'Guì Zhī + Bái Sháo', characters: '桂枝 + 白芍',
    category: { en: 'Harmonize Ying and Wei', es: 'Armonizar Ying y Wei' },
    search: 'gui zhi bai shao harmonize ying wei nutritive defensive wind cold sweating aversion wind gui zhi tang xiao jian zhong',
    en: {
      mnemonic: 'One pushes out; one holds in.',
      job: 'Harmonize nutritive and protective Qi in Exterior deficiency and regulate tension between movement and containment.',
      roles: [['Guì Zhī', 'Warms the channels and releases the muscle layer outward.'], ['Bái Sháo', 'Preserves Yin and nutritive Qi so the Exterior does not leak excessively.']],
      clues: 'Wind-Cold with spontaneous sweating, aversion to wind and a floating-moderate pulse.',
      formulas: 'Guì Zhī Tāng; Bái Sháo is doubled in Xiǎo Jiàn Zhōng Tāng.',
      trap: 'This is not the closed, no-sweat excess pattern of Má Huáng + Guì Zhī.',
      prep: 'The relative dose changes the pair from Exterior harmonizing toward spasm/pain relief.',
      safety: 'Persistent fever, breathing difficulty or dehydration requires medical evaluation; do not induce further sweating in a depleted patient.'
    },
    es: {
      mnemonic: 'Uno empuja afuera; otro contiene adentro.',
      job: 'Armonizar Qi nutritivo y protector en deficiencia Exterior y regular tensión entre movimiento y contención.',
      roles: [['Guì Zhī', 'Calienta canales y libera capa muscular hacia afuera.'], ['Bái Sháo', 'Preserva Yin y Qi nutritivo para que Exterior no pierda en exceso.']],
      clues: 'Viento-Frío con sudor espontáneo, aversión al viento y pulso flotante-moderado.',
      formulas: 'Guì Zhī Tāng; Bái Sháo se duplica en Xiǎo Jiàn Zhōng Tāng.',
      trap: 'No es el pattern cerrado sin sudor de Má Huáng + Guì Zhī.',
      prep: 'La dosis relativa cambia la pareja de armonizar Exterior hacia aliviar espasmo/dolor.',
      safety: 'Fiebre persistente, disnea o deshidratación requiere evaluación; no inducir más sudor en paciente agotado.'
    }
  },
  {
    number: 5, slug: 'jing-jie-fang-feng-dui-yao', title: 'Jīng Jiè + Fáng Fēng', characters: '荆芥 + 防风',
    category: { en: 'Dispel Wind from the Exterior', es: 'Expulsar Viento del Exterior' },
    search: 'jing jie fang feng wind exterior itching rash sores wind cold heat jing fang bai du xiao feng san charred bleeding',
    en: {
      mnemonic: 'The mild wind-chasing duo.',
      job: 'Dispel Wind from the surface and stop itching across Wind-Cold, Wind-Heat, rash and early sore presentations.',
      roles: [['Jīng Jiè', 'Lightly vents the Exterior and rashes; the charred form changes toward stopping bleeding.'], ['Fáng Fēng', 'Expels Wind while relieving pain, spasm and itching.']],
      clues: 'Itching or migrating superficial symptoms without the harsh sweating action of stronger Exterior herbs.',
      formulas: 'Jīng Fáng Bài Dú Sǎn; Xiāo Fēng Sǎn.',
      trap: 'The pair is broader than a simple Wind-Cold duo; both can appear in rash and Wind-Heat contexts when balanced.',
      prep: 'Use charred Jīng Jiè only when the intended job changes to hemostasis.',
      safety: 'A rapidly spreading rash, breathing difficulty, facial swelling, blistering or fever requires urgent assessment.'
    },
    es: {
      mnemonic: 'El dúo suave que persigue el Viento.',
      job: 'Expulsar Viento de la superficie y detener picor en Viento-Frío, Viento-Calor, erupciones y llagas tempranas.',
      roles: [['Jīng Jiè', 'Ventila suavemente Exterior y erupciones; carbonizada cambia a detener sangrado.'], ['Fáng Fēng', 'Expulsa Viento y alivia dolor, espasmo y picor.']],
      clues: 'Picor o signos superficiales migratorios sin sudoración agresiva de hierbas Exterior más fuertes.',
      formulas: 'Jīng Fáng Bài Dú Sǎn; Xiāo Fēng Sǎn.',
      trap: 'Es más amplia que una pareja simple Viento-Frío; aparece en erupción y Viento-Calor cuando se equilibra.',
      prep: 'Usar Jīng Jiè carbonizada solo cuando el trabajo buscado cambia a hemostasia.',
      safety: 'Rash que se extiende rápido, disnea, hinchazón facial, ampollas o fiebre requiere urgencia.'
    }
  },
  {
    number: 6, slug: 'cang-er-zi-xin-yi-hua-dui-yao', title: 'Cāng Ěr Zǐ + Xīn Yí Huā', characters: '苍耳子 + 辛夷花',
    category: { en: 'Open the Nasal Passages', es: 'Abrir Pasajes Nasales' },
    search: 'cang er zi xin yi hua nose nasal congestion sinus headache loss smell cang er zi san toxic wrap',
    en: {
      mnemonic: 'The burr and fuzzy bud nose crew.',
      job: 'Dispel Wind and open the nasal passages for congestion, sinus pressure and reduced smell.',
      roles: [['Cāng Ěr Zǐ', 'Strongly opens the nose and dispels Wind-Damp from the head.'], ['Xīn Yí Huā', 'Directs aromatic opening action to the nose and sinus region.']],
      clues: 'Stuffy nose, frontal/sinus headache and loss or reduction of smell.',
      formulas: 'Cāng Ěr Zǐ Sǎn.',
      trap: 'This pair treats obstruction; discharge color and whole pattern still determine whether Cold, Heat or Dampness must also be addressed.',
      prep: 'Wrap Xīn Yí Huā during decoction because its hairs irritate the throat.',
      safety: 'Cāng Ěr Zǐ is toxic and requires professional dosing. Facial swelling, visual changes, severe headache, high fever or neurologic signs needs urgent care.'
    },
    es: {
      mnemonic: 'La fresa espinosa y el capullo velloso del equipo nasal.',
      job: 'Expulsar Viento y abrir pasajes nasales para congestión, presión sinusal y olfato reducido.',
      roles: [['Cāng Ěr Zǐ', 'Abre nariz con fuerza y expulsa Viento-Humedad de cabeza.'], ['Xīn Yí Huā', 'Dirige apertura aromática a nariz y senos.']],
      clues: 'Nariz tapada, cefalea frontal/sinusal y pérdida o reducción del olfato.',
      formulas: 'Cāng Ěr Zǐ Sǎn.',
      trap: 'Trata obstrucción; color de secreción y pattern completo determinan si también hay Frío, Calor o Humedad.',
      prep: 'Envolver Xīn Yí Huā al decoctar porque sus pelos irritan garganta.',
      safety: 'Cāng Ěr Zǐ es tóxica y requiere dosis profesional. Hinchazón facial, cambios visuales, cefalea grave, fiebre alta o signos neurológicos requiere urgencia.'
    }
  },
  {
    number: 7, slug: 'sang-ye-ju-hua-dui-yao', title: 'Sāng Yè + Jú Huā', characters: '桑叶 + 菊花',
    category: { en: 'Disperse Wind-Heat · Brighten the Eyes', es: 'Dispersar Viento-Calor · Aclarar Ojos' },
    search: 'sang ye ju hua mulberry leaf chrysanthemum wind heat lung liver cough red sore eyes headache sang ju yin',
    en: {
      mnemonic: 'A leaf and a flower for a hot head.',
      job: 'Disperse Wind-Heat, clear Lung and Liver Heat and brighten the eyes.',
      roles: [['Sāng Yè', 'Focuses more on Lung Heat, cough and dryness while also clearing the eyes.'], ['Jú Huā', 'Focuses more on Liver Heat, headache and red painful eyes.']],
      clues: 'Early Wind-Heat with cough, headache and red, sore or tearing eyes.',
      formulas: 'Sāng Jú Yǐn.',
      trap: 'Jú Huā with Sāng Yè treats excess Wind-Heat eyes; with Gǒu Qǐ Zǐ it treats deficient dry/blurred eyes.',
      prep: 'Light aromatic materials generally do not need prolonged boiling.',
      safety: 'Sudden visual loss, severe eye pain, halos, trauma or neurologic symptoms requires urgent ophthalmic/medical evaluation.'
    },
    es: {
      mnemonic: 'Una hoja y una flor para una cabeza caliente.',
      job: 'Dispersar Viento-Calor, aclarar Calor de Pulmón/Hígado y mejorar ojos.',
      roles: [['Sāng Yè', 'Enfatiza Calor de Pulmón, tos y sequedad, además de ojos.'], ['Jú Huā', 'Enfatiza Calor de Hígado, cefalea y ojos rojos dolorosos.']],
      clues: 'Viento-Calor temprano con tos, cefalea y ojos rojos, dolorosos o llorosos.',
      formulas: 'Sāng Jú Yǐn.',
      trap: 'Jú Huā con Sāng Yè trata ojos por exceso Viento-Calor; con Gǒu Qǐ Zǐ trata ojos secos/borrosos por deficiencia.',
      prep: 'Materiales aromáticos ligeros generalmente no necesitan cocción prolongada.',
      safety: 'Pérdida visual súbita, dolor ocular grave, halos, trauma o signos neurológicos requiere urgencia oftálmica/médica.'
    }
  },
  {
    number: 8, slug: 'ge-gen-sheng-ma-dui-yao', title: 'Gé Gēn + Shēng Má', characters: '葛根 + 升麻',
    category: { en: 'Vent Rashes · Release the Muscle Layer', es: 'Ventilar Erupciones · Liberar Capa Muscular' },
    search: 'ge gen sheng ma pueraria cimicifuga measles rash vent muscle layer sheng ma ge gen tang',
    en: {
      mnemonic: 'Push the measles rash outward.',
      job: 'Release the muscle layer and vent an early rash that is failing to erupt fully.',
      roles: [['Gé Gēn', 'Releases the muscle layer, raises clear Yang and generates fluids.'], ['Shēng Má', 'Raises and vents toxin/rash toward the surface.']],
      clues: 'Early measles or another appropriately diagnosed rash that is incomplete or slow to emerge.',
      formulas: 'Shēng Má Gé Gēn Tāng.',
      trap: 'Stop the venting strategy once the rash is fully out; do not use it to force a late, dark or complicated rash.',
      prep: 'Use as part of a professionally differentiated formula, not as isolated rash self-care.',
      safety: 'Suspected measles requires immediate public-health coordination and airborne isolation. Breathing difficulty, dehydration, confusion or seizure is an emergency.'
    },
    es: {
      mnemonic: 'Empujar el rash de sarampión hacia afuera.',
      job: 'Liberar capa muscular y ventilar erupción temprana que no emerge completamente.',
      roles: [['Gé Gēn', 'Libera capa muscular, eleva Yang claro y genera líquidos.'], ['Shēng Má', 'Eleva y ventila toxina/erupción hacia superficie.']],
      clues: 'Sarampión temprano u otra erupción bien diagnosticada que está incompleta o tarda en salir.',
      formulas: 'Shēng Má Gé Gēn Tāng.',
      trap: 'Detener estrategia ventiladora cuando rash ya salió; no forzar rash tardío, oscuro o complicado.',
      prep: 'Usar dentro de fórmula diferenciada profesionalmente, no como autocuidado aislado.',
      safety: 'Sarampión sospechado exige coordinación inmediata con salud pública y aislamiento aéreo. Disnea, deshidratación, confusión o convulsión es emergencia.'
    }
  },
  {
    number: 9, slug: 'chai-hu-sheng-ma-dui-yao', title: 'Chái Hú + Shēng Má', characters: '柴胡 + 升麻',
    category: { en: 'Raise Clear Yang', es: 'Elevar Yang Claro' },
    search: 'chai hu sheng ma raise clear yang sinking middle qi prolapse chronic diarrhea bu zhong yi qi tang small doses',
    en: {
      mnemonic: 'The two-herb forklift.',
      job: 'Raise clear Yang and guide a tonic formula upward for sinking Middle Qi.',
      roles: [['Chái Hú', 'Raises clear Yang through the Liver/Shaoyang axis and relieves constraint.'], ['Shēng Má', 'Lifts sunken Spleen Qi and directs action upward.']],
      clues: 'Prolapse, bearing-down heaviness, chronic diarrhea and fatigue from sinking Qi.',
      formulas: 'Bǔ Zhōng Yì Qì Tāng.',
      trap: 'They are steering herbs in small doses; the tonic herbs provide the fuel. Large dispersing doses would change the strategy.',
      prep: 'Dose relationship matters more than simply recognizing both herbs.',
      safety: 'New or painful prolapse, bleeding, bowel obstruction symptoms or severe weakness requires medical evaluation.'
    },
    es: {
      mnemonic: 'El montacargas de dos hierbas.',
      job: 'Elevar Yang claro y guiar fórmula tónica hacia arriba para Qi Medio hundido.',
      roles: [['Chái Hú', 'Eleva Yang claro por eje Hígado/Shaoyang y alivia estancamiento.'], ['Shēng Má', 'Levanta Qi de Bazo hundido y dirige acción arriba.']],
      clues: 'Prolapso, pesadez hacia abajo, diarrea crónica y fatiga por Qi hundido.',
      formulas: 'Bǔ Zhōng Yì Qì Tāng.',
      trap: 'Son hierbas guía en dosis pequeñas; los tónicos aportan combustible. Dosis dispersantes grandes cambian estrategia.',
      prep: 'La relación de dosis importa más que solo reconocer ambas hierbas.',
      safety: 'Prolapso nuevo/doloroso, sangrado, signos de obstrucción intestinal o debilidad grave requiere evaluación.'
    }
  },
  {
    number: 10, slug: 'zhi-zi-dan-dou-chi-dui-yao', title: 'Zhī Zǐ + Dàn Dòu Chǐ', characters: '栀子 + 淡豆豉',
    category: { en: 'Vent Constrained Heat from the Chest', es: 'Ventilar Calor Constreñido del Pecho' },
    search: 'zhi zi dan dou chi gardenia fermented soybean constrained heat chest restlessness insomnia post fever zhi zi chi tang',
    en: {
      mnemonic: 'A cold bitter fruit and fermented bean let leftover Heat escape.',
      job: 'Clear and vent constrained residual Heat from the chest after an unresolved febrile process.',
      roles: [['Zhī Zǐ', 'Clears Fire from the three burners and relieves irritability.'], ['Dàn Dòu Chǐ', 'Lightly vents lingering Heat outward from the chest and Exterior.']],
      clues: 'Restlessness, insomnia and stuffy chest after a fever, without the four-greats picture of blazing Qi-level Heat.',
      formulas: 'Zhī Zǐ Chǐ Tāng.',
      trap: 'Do not confuse residual constrained Heat with full Yangming Heat requiring Shí Gāo + Zhī Mǔ.',
      prep: 'Dàn Dòu Chǐ is a prepared/fermented soybean medicinal, not ordinary food soy.',
      safety: 'Persistent fever, chest pain, breathing difficulty, confusion or severe insomnia with behavioral change requires medical evaluation.'
    },
    es: {
      mnemonic: 'Una fruta amarga-fría y un frijol fermentado dejan escapar Calor residual.',
      job: 'Aclarar y ventilar Calor residual constreñido del pecho tras proceso febril no resuelto.',
      roles: [['Zhī Zǐ', 'Aclara Fuego de los tres Jiaos y alivia irritabilidad.'], ['Dàn Dòu Chǐ', 'Ventila suavemente Calor persistente hacia afuera desde pecho/Exterior.']],
      clues: 'Inquietud, insomnio y pecho tapado tras fiebre, sin los cuatro grandes del Calor ardiente de nivel Qi.',
      formulas: 'Zhī Zǐ Chǐ Tāng.',
      trap: 'No confundir Calor residual constreñido con Calor Yangming pleno que requiere Shí Gāo + Zhī Mǔ.',
      prep: 'Dàn Dòu Chǐ es soja medicinal preparada/fermentada, no soja alimentaria ordinaria.',
      safety: 'Fiebre persistente, dolor torácico, disnea, confusión o insomnio grave con cambio conductual requiere evaluación.'
    }
  },
  {
    number: 11, slug: 'shi-gao-zhi-mu-dui-yao', title: 'Shí Gāo + Zhī Mǔ', characters: '石膏 + 知母',
    category: { en: 'Clear Qi-Level Heat', es: 'Aclarar Calor de Nivel Qi' },
    search: 'shi gao zhi mu gypsum anemarrhena white tiger qi level heat four bigs fever sweat thirst flooding pulse bai hu tang',
    en: {
      mnemonic: 'The White Tiger’s claws.',
      job: 'Clear blazing Qi-level Heat, drain Lung and Stomach Fire and protect fluids.',
      roles: [['Shí Gāo', 'Powerfully clears excess Heat from the Lung and Stomach Qi level.'], ['Zhī Mǔ', 'Clears Heat while enriching Yin and restoring fluids damaged by the blaze.']],
      clues: 'The four bigs: high fever, profuse sweating, great thirst and a flooding-forceful pulse.',
      formulas: 'Bái Hǔ Tāng.',
      trap: 'This is full excess Qi-level Heat, not lingering constrained Heat treated by Zhī Zǐ + Dàn Dòu Chǐ.',
      prep: 'Crush Shí Gāo and decoct it first; use the pair only within a correctly differentiated formula.',
      safety: 'Avoid a strong cold-clearing strategy in Spleen/Stomach deficiency without true excess Heat. Altered mental status, severe dehydration or persistent high fever requires urgent care.'
    },
    es: {
      mnemonic: 'Las garras del Tigre Blanco.',
      job: 'Aclarar Calor ardiente de nivel Qi, drenar Fuego de Pulmón/Estómago y proteger líquidos.',
      roles: [['Shí Gāo', 'Aclara con fuerza Calor por exceso del nivel Qi de Pulmón y Estómago.'], ['Zhī Mǔ', 'Aclara Calor mientras nutre Yin y recupera líquidos dañados por el ardor.']],
      clues: 'Los cuatro grandes: fiebre alta, sudor profuso, mucha sed y pulso inundado-fuerte.',
      formulas: 'Bái Hǔ Tāng.',
      trap: 'Es Calor pleno por exceso de nivel Qi, no Calor residual constreñido tratado por Zhī Zǐ + Dàn Dòu Chǐ.',
      prep: 'Triturar Shí Gāo y decoctarlo primero; usar dentro de fórmula correctamente diferenciada.',
      safety: 'Evitar estrategia fría potente en deficiencia Bazo/Estómago sin Calor pleno. Alteración mental, deshidratación grave o fiebre alta persistente requiere urgencia.'
    }
  },
  {
    number: 12, slug: 'zhi-mu-huang-bai-dui-yao', title: 'Zhī Mǔ + Huáng Bǎi', characters: '知母 + 黄柏',
    category: { en: 'Drain Deficiency Fire', es: 'Drenar Fuego por Deficiencia' },
    search: 'zhi mu huang bai anemarrhena phellodendron yin deficiency empty fire night sweats steaming bones seminal emission zhi bai di huang wan da bu yin wan',
    en: {
      mnemonic: 'Two bitter-cold herbs put out the fire beneath an empty kettle.',
      job: 'Enrich Yin while draining deficiency Fire from the Kidney and lower burner.',
      roles: [['Zhī Mǔ', 'Clears deficiency Heat and nourishes Yin and fluids.'], ['Huáng Bǎi', 'Drains ministerial Fire and Damp-Heat from the lower burner.']],
      clues: 'Night sweats, steaming-bone fever, afternoon Heat, seminal emission and other Empty-Heat signs.',
      formulas: 'Zhī Bǎi Dì Huáng Wán; Dà Bǔ Yīn Wán.',
      trap: 'Do not confuse deficiency Fire with the four-greats excess pattern of Shí Gāo + Zhī Mǔ.',
      prep: 'The cold, bitter pair is normally embedded in a Yin-enriching formula rather than used as a stand-alone tonic.',
      safety: 'Use cautiously with chronic loose stools, poor appetite or Spleen deficiency because the pair is bitter and cold.'
    },
    es: {
      mnemonic: 'Dos amargas-frías apagan el fuego bajo una olla vacía.',
      job: 'Enriquecer Yin mientras drena Fuego por deficiencia de Riñón y Jiao inferior.',
      roles: [['Zhī Mǔ', 'Aclara Calor por deficiencia y nutre Yin y líquidos.'], ['Huáng Bǎi', 'Drena Fuego ministerial y Humedad-Calor del Jiao inferior.']],
      clues: 'Sudor nocturno, calor en huesos, Calor vespertino, emisión seminal y otros signos de Calor vacío.',
      formulas: 'Zhī Bǎi Dì Huáng Wán; Dà Bǔ Yīn Wán.',
      trap: 'No confundir Fuego por deficiencia con los cuatro grandes por exceso de Shí Gāo + Zhī Mǔ.',
      prep: 'La pareja amarga-fría suele incorporarse en fórmula que enriquece Yin, no como tónico aislado.',
      safety: 'Usar con cautela en diarrea crónica, poco apetito o deficiencia de Bazo porque es amarga y fría.'
    }
  },
  {
    number: 13, slug: 'sang-bai-pi-di-gu-pi-dui-yao', title: 'Sāng Bái Pí + Dì Gǔ Pí', characters: '桑白皮 + 地骨皮',
    category: { en: 'Drain Smoldering Lung Heat', es: 'Drenar Calor Latente de Pulmón' },
    search: 'sang bai pi di gu pi mulberry root bark lycium root bark lung heat cough wheezing afternoon skin heat xie bai san',
    en: {
      mnemonic: 'Two root barks drain a smoldering Lung.',
      job: 'Drain Lung Heat, calm cough and wheezing and clear lingering afternoon Heat.',
      roles: [['Sāng Bái Pí', 'Drains Lung Heat, descends Lung Qi and promotes urination.'], ['Dì Gǔ Pí', 'Clears deficiency Heat and cools the Blood while targeting smoldering Lung Heat.']],
      clues: 'Cough or wheezing with hot skin that worsens in the afternoon and no blazing exterior picture.',
      formulas: 'Xiè Bái Sǎn.',
      trap: '“Drain the White” refers to the Lung; this is gentler smoldering Heat, not the four-greats Yangming pattern.',
      prep: 'Differentiate excess Lung Heat from Yin-deficiency Heat before choosing the surrounding formula.',
      safety: 'Acute breathing difficulty, blue lips, chest pain or inability to speak full sentences requires emergency care.'
    },
    es: {
      mnemonic: 'Dos cortezas de raíz drenan un Pulmón que arde lentamente.',
      job: 'Drenar Calor pulmonar, calmar tos/sibilancia y aclarar Calor vespertino persistente.',
      roles: [['Sāng Bái Pí', 'Drena Calor de Pulmón, desciende Qi pulmonar y promueve orina.'], ['Dì Gǔ Pí', 'Aclara Calor por deficiencia y enfría Sangre, enfocándose en Calor pulmonar latente.']],
      clues: 'Tos o sibilancia con piel caliente por la tarde sin cuadro Exterior ardiente.',
      formulas: 'Xiè Bái Sǎn.',
      trap: '“Drenar lo Blanco” alude al Pulmón; es Calor latente más suave, no los cuatro grandes Yangming.',
      prep: 'Diferenciar Calor pulmonar por exceso de Calor por deficiencia de Yin antes de formular.',
      safety: 'Disnea aguda, labios azules, dolor torácico o incapacidad para hablar frases completas requiere emergencia.'
    }
  },
  {
    number: 14, slug: 'huang-lian-rou-gui-dui-yao', title: 'Huáng Lián + Ròu Guì', characters: '黄连 + 肉桂',
    category: { en: 'Restore Heart–Kidney Communication', es: 'Restaurar Comunicación Corazón–Riñón' },
    search: 'huang lian rou gui coptis cinnamon bark heart kidney communication insomnia palpitations restlessness jiao tai wan cold above heat below',
    en: {
      mnemonic: 'Ice on top and a heater below.',
      job: 'Restore Heart–Kidney communication by descending Heart Fire and warming Kidney Yang so Water can rise.',
      roles: [['Huáng Lián', 'Bitter-cold; descends blazing Heart Fire and quiets restlessness.'], ['Ròu Guì', 'Warm; kindles Kidney Yang and guides separated Fire back to its source.']],
      clues: 'Insomnia, palpitations and restlessness from Heart Fire above with deficient Kidney warmth below.',
      formulas: 'Jiāo Tài Wán.',
      trap: 'The opposite temperatures are deliberate; this is not a simple all-Heat or all-Cold prescription.',
      prep: 'Add Ròu Guì late or administer it as powder to preserve its aromatic warmth.',
      safety: 'Ròu Guì requires pregnancy and anticoagulant caution. New palpitations, fainting or chest pain requires prompt medical assessment.'
    },
    es: {
      mnemonic: 'Hielo arriba y calefacción abajo.',
      job: 'Restaurar comunicación Corazón–Riñón descendiendo Fuego cardíaco y calentando Yang renal para que Agua ascienda.',
      roles: [['Huáng Lián', 'Amarga-fría; desciende Fuego ardiente de Corazón y calma inquietud.'], ['Ròu Guì', 'Caliente; enciende Yang renal y guía Fuego separado a su fuente.']],
      clues: 'Insomnio, palpitaciones e inquietud por Fuego de Corazón arriba con calor renal deficiente abajo.',
      formulas: 'Jiāo Tài Wán.',
      trap: 'Las temperaturas opuestas son intencionales; no es prescripción simple de solo Calor o solo Frío.',
      prep: 'Añadir Ròu Guì al final o administrarla en polvo para preservar su calor aromático.',
      safety: 'Ròu Guì exige cautela en embarazo y con anticoagulantes. Palpitaciones nuevas, síncope o dolor torácico requiere evaluación pronta.'
    }
  },
  {
    number: 15, slug: 'jin-yin-hua-lian-qiao-dui-yao', title: 'Jīn Yín Huā + Lián Qiào', characters: '金银花 + 连翘',
    category: { en: 'Clear Heat and Resolve Toxicity', es: 'Aclarar Calor y Resolver Toxicidad' },
    search: 'jin yin hua lian qiao honeysuckle forsythia wind heat toxin sore throat fever boils sores yin qiao san',
    en: {
      mnemonic: 'The toxin-and-Wind-Heat duo.',
      job: 'Disperse Wind-Heat, clear Heat and resolve toxicity at the surface and in hot swellings.',
      roles: [['Jīn Yín Huā', 'Vents Wind-Heat from the surface and clears hot toxicity.'], ['Lián Qiào', 'Clears Heat, disperses clumps and reduces hot swellings.']],
      clues: 'Early fever with sore throat or hot, red, painful sores and boils.',
      formulas: 'Yín Qiáo Sǎn.',
      trap: 'Their shared toxin-clearing action does not replace drainage or medical treatment for a mature abscess.',
      prep: 'These light aromatic medicinals generally should not be subjected to unnecessarily prolonged boiling.',
      safety: 'Rapidly spreading redness, severe pain, high fever, streaking, facial infection or immune suppression requires medical evaluation.'
    },
    es: {
      mnemonic: 'El dúo de toxina y Viento-Calor.',
      job: 'Dispersar Viento-Calor, aclarar Calor y resolver toxicidad en superficie e hinchazones calientes.',
      roles: [['Jīn Yín Huā', 'Ventila Viento-Calor de superficie y aclara toxicidad caliente.'], ['Lián Qiào', 'Aclara Calor, dispersa nódulos y reduce hinchazón caliente.']],
      clues: 'Fiebre temprana con dolor de garganta o llagas/forúnculos rojos, calientes y dolorosos.',
      formulas: 'Yín Qiáo Sǎn.',
      trap: 'Su acción antitóxica compartida no sustituye drenaje ni tratamiento médico de absceso maduro.',
      prep: 'Estas medicinas aromáticas ligeras generalmente no deben hervirse innecesariamente por largo tiempo.',
      safety: 'Enrojecimiento que se extiende, dolor grave, fiebre alta, estrías, infección facial o inmunosupresión requiere evaluación.'
    }
  },
  {
    number: 16, slug: 'niu-bang-zi-lian-qiao-dui-yao', title: 'Niú Bàng Zǐ + Lián Qiào', characters: '牛蒡子 + 连翘',
    category: { en: 'Benefit the Throat · Reduce Swelling', es: 'Beneficiar Garganta · Reducir Hinchazón' },
    search: 'niu bang zi lian qiao burdock forsythia swollen sore throat mumps wind heat toxin yin qiao san pu ji xiao du yin',
    en: {
      mnemonic: 'The swollen-throat pair.',
      job: 'Disperse Wind-Heat, resolve toxicity, benefit the throat and reduce swelling.',
      roles: [['Niú Bàng Zǐ', 'Vents Wind-Heat, opens the throat and promotes rash expression.'], ['Lián Qiào', 'Clears toxicity and breaks up hot swelling and clumps.']],
      clues: 'Marked swollen sore throat, painful neck swelling or mumps-type presentation.',
      formulas: 'Yín Qiáo Sǎn; Pǔ Jì Xiāo Dú Yǐn.',
      trap: 'Compared with Jīn Yín Huā + Lián Qiào, this pair emphasizes throat and swelling more strongly.',
      prep: 'Niú Bàng Zǐ is commonly crushed before decoction.',
      safety: 'Niú Bàng Zǐ can loosen stool. Drooling, inability to swallow, muffled voice, airway symptoms or severe dehydration requires urgent care.'
    },
    es: {
      mnemonic: 'La pareja para garganta hinchada.',
      job: 'Dispersar Viento-Calor, resolver toxicidad, beneficiar garganta y reducir hinchazón.',
      roles: [['Niú Bàng Zǐ', 'Ventila Viento-Calor, abre garganta y promueve salida de erupción.'], ['Lián Qiào', 'Aclara toxicidad y rompe hinchazón/nódulos calientes.']],
      clues: 'Dolor de garganta muy hinchada, hinchazón dolorosa del cuello o cuadro tipo paperas.',
      formulas: 'Yín Qiáo Sǎn; Pǔ Jì Xiāo Dú Yǐn.',
      trap: 'Frente a Jīn Yín Huā + Lián Qiào, enfatiza con más fuerza garganta e hinchazón.',
      prep: 'Niú Bàng Zǐ suele triturarse antes de decocción.',
      safety: 'Niú Bàng Zǐ puede soltar heces. Babeo, imposibilidad de tragar, voz apagada, signos respiratorios o deshidratación grave requiere urgencia.'
    }
  },
  {
    number: 17, slug: 'qing-hao-bie-jia-dui-yao', title: 'Qīng Hāo + Biē Jiǎ', characters: '青蒿 + 鳖甲',
    category: { en: 'Vent Heat Lurking in Yin', es: 'Ventilar Calor Oculto en Yin' },
    search: 'qing hao bie jia sweet wormwood turtle carapace yin lurking heat night fever cool morning no sweat qing hao bie jia tang',
    en: {
      mnemonic: 'The turtle digs in; the wormwood leads Heat out.',
      job: 'Enrich Yin and vent Heat lurking deep in the Yin level after warm disease.',
      roles: [['Qīng Hāo', 'Lightly vents hidden Heat outward without damaging Yin.'], ['Biē Jiǎ', 'Enters deeply, enriches Yin and searches out lurking Heat.']],
      clues: 'Fever at night, cool by morning and no sweating as the fever recedes.',
      formulas: 'Qīng Hāo Biē Jiǎ Tāng.',
      trap: 'This is lurking Heat after warm disease, not ordinary Yin-deficiency night sweats or full Qi-level Heat.',
      prep: 'Crush and decoct Biē Jiǎ first; add Qīng Hāo late and do not boil it for long.',
      safety: 'Biē Jiǎ requires pregnancy caution and verified legal/ethical sourcing. Persistent or recurrent fever requires medical diagnosis.'
    },
    es: {
      mnemonic: 'La tortuga excava; el ajenjo conduce el Calor hacia afuera.',
      job: 'Enriquecer Yin y ventilar Calor oculto profundamente en nivel Yin tras enfermedad caliente.',
      roles: [['Qīng Hāo', 'Ventila suavemente Calor oculto sin dañar Yin.'], ['Biē Jiǎ', 'Entra profundo, enriquece Yin y busca Calor escondido.']],
      clues: 'Fiebre nocturna, fresco por la mañana y sin sudor cuando cede la fiebre.',
      formulas: 'Qīng Hāo Biē Jiǎ Tāng.',
      trap: 'Es Calor oculto tras enfermedad caliente, no sudor nocturno común por deficiencia de Yin ni Calor pleno de nivel Qi.',
      prep: 'Triturar y decoctar Biē Jiǎ primero; añadir Qīng Hāo tarde y no hervirla mucho.',
      safety: 'Biē Jiǎ exige cautela en embarazo y procedencia legal/ética verificada. Fiebre persistente o recurrente requiere diagnóstico médico.'
    }
  },
  {
    number: 18, slug: 'ren-shen-huang-qi-dui-yao', title: 'Rén Shēn + Huáng Qí', characters: '人参 + 黄芪',
    category: { en: 'Strongly Supplement Qi', es: 'Suplementar Qi con Fuerza' },
    search: 'ren shen huang qi ginseng astragalus original qi spleen lung raise yang secure exterior exhaustion prolapse spontaneous sweating bu zhong yi qi gui pi tang',
    en: {
      mnemonic: 'The Qi power couple: fill the tank and lift it.',
      job: 'Strongly supplement original and Spleen–Lung Qi, raise Yang and secure the Exterior.',
      roles: [['Rén Shēn', 'Powerfully replenishes original Qi from within and generates fluids.'], ['Huáng Qí', 'Raises clear Yang, strengthens protective Qi and secures the surface.']],
      clues: 'Profound fatigue, prolapse, spontaneous sweating, weak voice and signs of Spleen–Lung Qi deficiency.',
      formulas: 'Bǔ Zhōng Yì Qì Tāng; Guī Pí Tāng.',
      trap: 'Rén Shēn fills from within while Huáng Qí lifts and protects; their roles are complementary, not interchangeable.',
      prep: 'Use authenticated Rén Shēn and distinguish it from lower-intensity substitutes such as Dǎng Shēn.',
      safety: 'Rén Shēn is incompatible with Lí Lú and traditionally opposed to Wǔ Líng Zhī. Screen diabetes drugs, warfarin, stimulants and immunosuppressants.'
    },
    es: {
      mnemonic: 'La pareja poderosa del Qi: llenar el tanque y elevarlo.',
      job: 'Suplementar fuertemente Qi original y de Bazo–Pulmón, elevar Yang y asegurar Exterior.',
      roles: [['Rén Shēn', 'Repone con fuerza Qi original desde dentro y genera líquidos.'], ['Huáng Qí', 'Eleva Yang claro, fortalece Qi defensivo y asegura superficie.']],
      clues: 'Fatiga profunda, prolapso, sudor espontáneo, voz débil y signos de deficiencia Qi Bazo–Pulmón.',
      formulas: 'Bǔ Zhōng Yì Qì Tāng; Guī Pí Tāng.',
      trap: 'Rén Shēn llena desde dentro; Huáng Qí eleva y protege. Sus papeles se complementan, no son intercambiables.',
      prep: 'Usar Rén Shēn autenticado y distinguirlo de sustitutos menos intensos como Dǎng Shēn.',
      safety: 'Rén Shēn es incompatible con Lí Lú y tradicionalmente opuesto a Wǔ Líng Zhī. Revisar antidiabéticos, warfarina, estimulantes e inmunosupresores.'
    }
  },
  {
    number: 19, slug: 'bai-zhu-fu-ling-dui-yao', title: 'Bái Zhú + Fú Líng', characters: '白术 + 茯苓',
    category: { en: 'Strengthen the Spleen · Drain Dampness', es: 'Fortalecer Bazo · Drenar Humedad' },
    search: 'bai zhu fu ling atractylodes poria spleen qi dampness loose stools edema poor appetite phlegm fluids si jun zi wu ling ling gui zhu gan',
    en: {
      mnemonic: 'The Spleen’s mop and bucket.',
      job: 'Strengthen the Spleen while drying and draining Dampness so fluids move normally.',
      roles: [['Bái Zhú', 'Tonifies Spleen Qi and dries Dampness at its source.'], ['Fú Líng', 'Leaches Dampness through urination while supporting the Spleen.']],
      clues: 'Poor appetite, loose stools, fatigue, edema or phlegm-fluids from weak Spleen transformation.',
      formulas: 'Sì Jūn Zǐ Tāng; Wǔ Líng Sǎn; Líng Guì Zhú Gān Tāng.',
      trap: 'Bái Zhú dries and strengthens; Fú Líng drains gently. The pair treats both the weak root and Damp branch.',
      prep: 'Formula context determines whether the pair primarily tonifies Qi or manages water and phlegm-fluids.',
      safety: 'New one-sided swelling, sudden breathlessness, chest pain or rapidly worsening edema requires urgent medical assessment.'
    },
    es: {
      mnemonic: 'El trapeador y el cubo del Bazo.',
      job: 'Fortalecer Bazo mientras seca y drena Humedad para normalizar movimiento de líquidos.',
      roles: [['Bái Zhú', 'Tonifica Qi de Bazo y seca Humedad en su origen.'], ['Fú Líng', 'Filtra Humedad por orina mientras apoya Bazo.']],
      clues: 'Poco apetito, heces sueltas, fatiga, edema o flema-líquidos por transformación débil de Bazo.',
      formulas: 'Sì Jūn Zǐ Tāng; Wǔ Líng Sǎn; Líng Guì Zhú Gān Tāng.',
      trap: 'Bái Zhú seca y fortalece; Fú Líng drena suavemente. Trata raíz débil y rama húmeda.',
      prep: 'El contexto formulaico determina si la pareja tonifica Qi o maneja agua y flema-líquidos.',
      safety: 'Hinchazón unilateral nueva, disnea súbita, dolor torácico o edema que empeora rápido requiere urgencia.'
    }
  },
  {
    number: 20, slug: 'bai-shao-gan-cao-dui-yao', title: 'Bái Sháo + Gān Cǎo', characters: '白芍 + 甘草',
    category: { en: 'Relax Spasm · Stop Pain', es: 'Relajar Espasmo · Detener Dolor' },
    search: 'bai shao gan cao peony licorice sour sweet nourish yin blood relax spasm cramps calf abdominal menstrual shao yao gan cao tang',
    en: {
      mnemonic: 'Sour plus sweet melts the cramp.',
      job: 'Nourish Yin and Blood, soften tension, relax spasm and stop cramping pain.',
      roles: [['Bái Sháo', 'Nourishes Blood, preserves Yin and softens contracted sinews.'], ['Gān Cǎo', 'Tonifies and harmonizes while urgently relaxing spasm and pain.']],
      clues: 'Calf cramps, cramping abdominal pain or menstrual spasm with tight, contracted muscles.',
      formulas: 'Sháo Yào Gān Cǎo Tāng.',
      trap: 'This pair directly relaxes cramp; Guì Zhī + Bái Sháo instead emphasizes harmonizing Ying and Wei.',
      prep: 'Honey-fried Gān Cǎo may emphasize supplementation, but processing must match the formula source.',
      safety: 'Large or prolonged Gān Cǎo doses can raise blood pressure and lower potassium; screen diuretics, corticosteroids, digoxin and cardiovascular disease.'
    },
    es: {
      mnemonic: 'Ácido más dulce derrite el calambre.',
      job: 'Nutrir Yin y Sangre, suavizar tensión, relajar espasmo y detener dolor tipo calambre.',
      roles: [['Bái Sháo', 'Nutre Sangre, preserva Yin y suaviza tendones contraídos.'], ['Gān Cǎo', 'Tonifica y armoniza mientras relaja con rapidez espasmo y dolor.']],
      clues: 'Calambres de pantorrilla, dolor abdominal espasmódico o dolor menstrual con músculos tensos.',
      formulas: 'Sháo Yào Gān Cǎo Tāng.',
      trap: 'Relaja directamente calambre; Guì Zhī + Bái Sháo enfatiza armonizar Ying y Wei.',
      prep: 'Gān Cǎo tostada con miel puede enfatizar tonificación, pero el procesamiento debe coincidir con la fuente formulaica.',
      safety: 'Dosis grandes/prolongadas de Gān Cǎo pueden subir presión y bajar potasio; revisar diuréticos, corticosteroides, digoxina y enfermedad cardiovascular.'
    }
  },
  {
    number: 21, slug: 'jie-geng-gan-cao-dui-yao', title: 'Jié Gěng + Gān Cǎo', characters: '桔梗 + 甘草',
    category: { en: 'Benefit the Throat · Expel Pus', es: 'Beneficiar Garganta · Expulsar Pus' },
    search: 'jie geng gan cao platycodon licorice sore throat hoarseness lung abscess diffuse lung expel pus jie geng tang',
    en: {
      mnemonic: 'The sore-throat duo.',
      job: 'Benefit the throat, diffuse Lung Qi and expel phlegm or pus from the upper burner.',
      roles: [['Jié Gěng', 'Carries the action upward, diffuses Lung Qi and expels phlegm and pus.'], ['Gān Cǎo', 'Soothes the throat, clears toxicity and moderates Jié Gěng.']],
      clues: 'Sore throat, hoarseness or cough with thick sputum; classically also Lung abscess.',
      formulas: 'Jié Gěng Tāng.',
      trap: 'Jié Gěng opens and lifts toward the Lung and throat; it is not primarily a descending cough herb.',
      prep: 'Use the processed form of Gān Cǎo specified by the formula source; do not assume honey-fried in every case.',
      safety: 'Large or prolonged Gān Cǎo doses can raise blood pressure and lower potassium. Fever, chest pain, foul sputum, blood or breathing difficulty requires medical evaluation.'
    },
    es: {
      mnemonic: 'El dúo para dolor de garganta.',
      job: 'Beneficiar garganta, difundir Qi de Pulmón y expulsar flema o pus del Jiao superior.',
      roles: [['Jié Gěng', 'Lleva acción hacia arriba, difunde Qi pulmonar y expulsa flema y pus.'], ['Gān Cǎo', 'Suaviza garganta, aclara toxicidad y modera Jié Gěng.']],
      clues: 'Dolor de garganta, ronquera o tos con esputo espeso; clásicamente también absceso pulmonar.',
      formulas: 'Jié Gěng Tāng.',
      trap: 'Jié Gěng abre y eleva hacia Pulmón/garganta; no es principalmente hierba descendente para tos.',
      prep: 'Usar la forma de Gān Cǎo indicada por la fuente; no asumir tostada con miel en todos los casos.',
      safety: 'Dosis grandes/prolongadas de Gān Cǎo pueden subir presión y bajar potasio. Fiebre, dolor torácico, esputo fétido, sangre o disnea requiere evaluación.'
    }
  },
  {
    number: 22, slug: 'sheng-jiang-da-zao-dui-yao', title: 'Shēng Jiāng + Dà Zǎo', characters: '生姜 + 大枣',
    category: { en: 'Harmonize Ying–Wei and the Middle', es: 'Armonizar Ying–Wei y el Centro' },
    search: 'sheng jiang da zao fresh ginger jujube harmonize ying wei spleen stomach protect middle buffer harsh herbs gui zhi tang xiao chai hu xiao yao',
    en: {
      mnemonic: 'The house band that plays in half the formulas.',
      job: 'Harmonize Ying and Wei, support the Spleen and Stomach and buffer harsher medicinals.',
      roles: [['Shēng Jiāng', 'Warms and moves the Middle, disperses lightly and harmonizes Stomach Qi.'], ['Dà Zǎo', 'Tonifies and protects the Middle while nourishing the nutritive level.']],
      clues: 'A supporting pair inside mixed formulas where the Exterior and Middle need harmonizing.',
      formulas: 'Guì Zhī Tāng; Xiǎo Chái Hú Tāng; Xiāo Yáo Sǎn.',
      trap: 'They are usually assistants, not the main treatment; identify what the rest of the formula is doing.',
      prep: 'Slice Shēng Jiāng and split Dà Zǎo when directed so their constituents extract evenly.',
      safety: 'Concentrated ginger requires caution with anticoagulants and bleeding disorders. Persistent vomiting, dehydration or severe abdominal pain requires assessment.'
    },
    es: {
      mnemonic: 'La banda de casa que toca en media farmacopea.',
      job: 'Armonizar Ying y Wei, apoyar Bazo/Estómago y amortiguar medicinas más fuertes.',
      roles: [['Shēng Jiāng', 'Calienta y mueve Centro, dispersa suavemente y armoniza Qi de Estómago.'], ['Dà Zǎo', 'Tonifica y protege Centro mientras nutre nivel nutritivo.']],
      clues: 'Pareja asistente en fórmulas mixtas donde Exterior y Centro necesitan armonización.',
      formulas: 'Guì Zhī Tāng; Xiǎo Chái Hú Tāng; Xiāo Yáo Sǎn.',
      trap: 'Suelen ser asistentes, no tratamiento principal; identificar el trabajo del resto de la fórmula.',
      prep: 'Cortar Shēng Jiāng y abrir Dà Zǎo cuando se indique para extracción uniforme.',
      safety: 'Jengibre concentrado exige cautela con anticoagulantes y trastornos hemorrágicos. Vómito persistente, deshidratación o dolor abdominal grave requiere evaluación.'
    }
  },
  {
    number: 23, slug: 'dang-gui-huang-qi-dui-yao', title: 'Dāng Guī + Huáng Qí', characters: '当归 + 黄芪',
    category: { en: 'Supplement Qi to Generate Blood', es: 'Suplementar Qi para Generar Sangre' },
    search: 'dang gui huang qi angelica astragalus qi generate blood 5 to 1 blood loss deficiency fever slow healing sores dang gui bu xue tang',
    en: {
      mnemonic: 'Five parts Qi make one part Blood.',
      job: 'Use strongly supplemented Qi to generate Blood and support tissue recovery after loss or depletion.',
      roles: [['Huáng Qí', 'At the large dose, powerfully drives Qi and production of new Blood.'], ['Dāng Guī', 'Supplies and nourishes the Blood material while gently moving it.']],
      clues: 'Blood deficiency after blood loss, low-grade deficiency fever, fatigue or slow-healing sores.',
      formulas: 'Dāng Guī Bǔ Xuè Tāng — Huáng Qí 5 : Dāng Guī 1.',
      trap: 'The unusual 5:1 ratio is the exam key; equal doses miss the formula’s Qi-generates-Blood logic.',
      prep: 'Preserve the classical dose relationship unless a qualified practitioner has a specific reason to modify it.',
      safety: 'Screen anticoagulants/antiplatelets, diabetes medicines and immunosuppressants. Active or unexplained bleeding requires medical evaluation first.'
    },
    es: {
      mnemonic: 'Cinco partes de Qi producen una de Sangre.',
      job: 'Usar Qi fuertemente suplementado para generar Sangre y apoyar reparación tras pérdida o agotamiento.',
      roles: [['Huáng Qí', 'En dosis grande impulsa con fuerza Qi y producción de Sangre nueva.'], ['Dāng Guī', 'Aporta y nutre material de Sangre mientras la mueve suavemente.']],
      clues: 'Deficiencia de Sangre tras hemorragia, fiebre baja por deficiencia, fatiga o llagas que cicatrizan lento.',
      formulas: 'Dāng Guī Bǔ Xuè Tāng — Huáng Qí 5 : Dāng Guī 1.',
      trap: 'La proporción 5:1 es clave de examen; dosis iguales pierden lógica Qi-genera-Sangre.',
      prep: 'Conservar relación clásica salvo razón específica de un profesional calificado para modificarla.',
      safety: 'Revisar anticoagulantes/antiagregantes, antidiabéticos e inmunosupresores. Sangrado activo o inexplicado requiere evaluación primero.'
    }
  },
  {
    number: 24, slug: 'dang-gui-chuan-xiong-dui-yao', title: 'Dāng Guī + Chuān Xiōng', characters: '当归 + 川芎',
    category: { en: 'Nourish and Invigorate Blood', es: 'Nutrir y Movilizar Sangre' },
    search: 'dang gui chuan xiong angelica ligusticum moving half four substances nourish invigorate blood menses headache postpartum stasis fo shou san si wu tang',
    en: {
      mnemonic: 'The moving half of the Four Substances.',
      job: 'Nourish and invigorate Blood, regulate menstruation and stop pain from deficiency with stasis.',
      roles: [['Dāng Guī', 'Nourishes Blood while moving it gently and regulating menstruation.'], ['Chuān Xiōng', 'Moves Blood and Qi more strongly and reaches upward to the head.']],
      clues: 'Irregular or painful periods, headache or postpartum pain with Blood deficiency and stasis.',
      formulas: 'Fó Shǒu Sǎn; Sì Wù Tāng.',
      trap: 'Dāng Guī supplies while Chuān Xiōng moves; too much movement is wrong when pure deficiency dominates.',
      prep: 'Use within a balanced formula when deficiency and stasis coexist rather than treating all bleeding as stasis.',
      safety: 'Screen warfarin, antiplatelets and bleeding disorders. Chuān Xiōng requires pregnancy caution; severe postpartum bleeding is an emergency.'
    },
    es: {
      mnemonic: 'La mitad móvil de las Cuatro Sustancias.',
      job: 'Nutrir y movilizar Sangre, regular menstruación y detener dolor por deficiencia con estasis.',
      roles: [['Dāng Guī', 'Nutre Sangre mientras la mueve suavemente y regula menstruación.'], ['Chuān Xiōng', 'Mueve Sangre/Qi con más fuerza y alcanza cabeza.']],
      clues: 'Reglas irregulares o dolorosas, cefalea o dolor posparto con deficiencia y estasis de Sangre.',
      formulas: 'Fó Shǒu Sǎn; Sì Wù Tāng.',
      trap: 'Dāng Guī aporta y Chuān Xiōng mueve; demasiado movimiento es incorrecto si domina deficiencia pura.',
      prep: 'Usar en fórmula equilibrada cuando coexisten deficiencia y estasis, no tratar todo sangrado como estasis.',
      safety: 'Revisar warfarina, antiagregantes y trastornos hemorrágicos. Chuān Xiōng exige cautela en embarazo; hemorragia posparto grave es emergencia.'
    }
  },
  {
    number: 25, slug: 'dang-gui-xiang-fu-dui-yao', title: 'Dāng Guī + Xiāng Fù', characters: '当归 + 香附',
    category: { en: 'Regulate Qi, Blood and Menses', es: 'Regular Qi, Sangre y Menstruación' },
    search: 'dang gui xiang fu angelica cyperus gynecology liver qi constraint blood menstrual pms breast distention painful irregular periods xiang fu si wu tang',
    en: {
      mnemonic: 'The gynecology pair: feed the Blood and free the stuck Qi.',
      job: 'Regulate Qi and Blood, regulate menstruation and relieve pain from Liver-Qi constraint with Blood deficiency.',
      roles: [['Dāng Guī', 'Nourishes and gently moves Blood to support the menstrual cycle.'], ['Xiāng Fù', 'Spreads constrained Liver Qi and strongly regulates menstruation.']],
      clues: 'PMS, breast distention and painful or irregular periods linked to emotional constraint.',
      formulas: 'Xiāng Fù Sì Wù Tāng — an off-list exemplar built on Sì Wù Tāng.',
      trap: 'Compared with Dāng Guī + Chuān Xiōng, this pair emphasizes Qi constraint and emotional/menstrual distention.',
      prep: 'Vinegar-processed Xiāng Fù may strengthen its Liver-channel and pain-regulating emphasis when specified.',
      safety: 'Screen anticoagulants and bleeding disorders. New severe pelvic pain, fainting, pregnancy or heavy bleeding requires urgent evaluation.'
    },
    es: {
      mnemonic: 'La pareja ginecológica: nutrir Sangre y liberar Qi atascado.',
      job: 'Regular Qi y Sangre, regular menstruación y aliviar dolor por estancamiento de Qi hepático con deficiencia de Sangre.',
      roles: [['Dāng Guī', 'Nutre y mueve suavemente Sangre para apoyar ciclo menstrual.'], ['Xiāng Fù', 'Dispersa Qi de Hígado constreñido y regula menstruación con fuerza.']],
      clues: 'PMS, distensión mamaria y reglas dolorosas/irregulares relacionadas con tensión emocional.',
      formulas: 'Xiāng Fù Sì Wù Tāng — ejemplo fuera de lista basado en Sì Wù Tāng.',
      trap: 'Frente a Dāng Guī + Chuān Xiōng, enfatiza estancamiento de Qi y distensión emocional/menstrual.',
      prep: 'Xiāng Fù procesada con vinagre puede reforzar énfasis en canal de Hígado y dolor cuando se especifica.',
      safety: 'Revisar anticoagulantes y trastornos hemorrágicos. Dolor pélvico grave nuevo, síncope, embarazo o sangrado abundante requiere urgencia.'
    }
  },
  {
    number: 26, slug: 'ai-ye-e-jiao-dui-yao', title: 'Ài Yè + Ē Jiāo', characters: '艾叶 + 阿胶',
    category: { en: 'Warm the Womb · Stop Bleeding', es: 'Calentar Útero · Detener Sangrado' },
    search: 'ai ye e jiao mugwort donkey hide gelatin warm womb nourish blood stop bleeding calm fetus uterine bleeding spotting threatened miscarriage jiao ai tang',
    en: {
      mnemonic: 'Warm the womb and glue the leak.',
      job: 'Warm the channels, nourish Blood, stop uterine bleeding and calm the fetus.',
      roles: [['Ài Yè', 'Warms the womb and channels and stops bleeding from Cold.'], ['Ē Jiāo', 'Nourishes Blood and Yin and stops bleeding by enriching the deficient root.']],
      clues: 'Uterine bleeding or spotting with Cold/deficiency signs; classically threatened miscarriage.',
      formulas: 'Jiāo Ài Tāng.',
      trap: 'The pair is for deficiency-Cold bleeding, not bright-red excess-Heat bleeding.',
      prep: 'Melt Ē Jiāo into the strained hot decoction; do not boil it directly with the other herbs.',
      safety: 'Bleeding during pregnancy requires obstetric co-management. Ē Jiāo is an animal product requiring authenticated, legal and ethically reviewed sourcing.'
    },
    es: {
      mnemonic: 'Calentar el útero y pegar la fuga.',
      job: 'Calentar canales, nutrir Sangre, detener sangrado uterino y calmar feto.',
      roles: [['Ài Yè', 'Calienta útero/canales y detiene sangrado por Frío.'], ['Ē Jiāo', 'Nutre Sangre/Yin y detiene sangrado enriqueciendo raíz deficiente.']],
      clues: 'Sangrado uterino o manchado con signos Frío/deficiencia; clásicamente amenaza de aborto.',
      formulas: 'Jiāo Ài Tāng.',
      trap: 'Es para sangrado por deficiencia-Frío, no sangrado rojo brillante por exceso-Calor.',
      prep: 'Derretir Ē Jiāo en decocción caliente ya colada; no hervir directamente con otras hierbas.',
      safety: 'Sangrado en embarazo exige manejo obstétrico conjunto. Ē Jiāo es producto animal que requiere procedencia autenticada, legal y ética.'
    }
  },
  {
    number: 27, slug: 'mai-men-dong-sha-shen-dui-yao', title: 'Mài Mén Dōng + Shā Shēn', characters: '麦门冬 + 沙参',
    category: { en: 'Nourish Lung–Stomach Yin', es: 'Nutrir Yin de Pulmón–Estómago' },
    search: 'mai men dong sha shen ophiopogon glehnia adenophora lung stomach yin dry cough mouth fluids moisten dryness sha shen mai men dong tang yi guan jian',
    en: {
      mnemonic: 'Two moist roots for a dry cough and dry mouth.',
      job: 'Nourish Lung and Stomach Yin, generate fluids and moisten dryness.',
      roles: [['Mài Mén Dōng', 'Enriches Lung, Stomach and Heart Yin while generating fluids.'], ['Shā Shēn', 'Nourishes Lung and Stomach Yin and clears deficiency Heat from dryness.']],
      clues: 'Dry cough, dry throat or mouth, thirst and scanty sputum from injured fluids.',
      formulas: 'Shā Shēn Mài Mén Dōng Tāng; Yī Guàn Jiān.',
      trap: 'This is dry Yin/fluid injury, not Damp-Phlegm with a greasy coat and copious sputum.',
      prep: 'Use verified Shā Shēn species and formula-specific preparation; regional products are not automatically interchangeable.',
      safety: 'Shā Shēn is traditionally incompatible with Lí Lú. Use cautiously with marked Spleen deficiency, loose stools or Dampness.'
    },
    es: {
      mnemonic: 'Dos raíces húmedas para tos y boca secas.',
      job: 'Nutrir Yin de Pulmón/Estómago, generar líquidos y humedecer sequedad.',
      roles: [['Mài Mén Dōng', 'Enriquece Yin de Pulmón, Estómago y Corazón mientras genera líquidos.'], ['Shā Shēn', 'Nutre Yin de Pulmón/Estómago y aclara Calor por deficiencia asociado a sequedad.']],
      clues: 'Tos seca, garganta/boca seca, sed y esputo escaso por líquidos lesionados.',
      formulas: 'Shā Shēn Mài Mén Dōng Tāng; Yī Guàn Jiān.',
      trap: 'Es lesión seca de Yin/líquidos, no Flema-Humedad con capa grasosa y esputo copioso.',
      prep: 'Usar especie verificada de Shā Shēn y preparación formulaica; productos regionales no son automáticamente intercambiables.',
      safety: 'Shā Shēn es tradicionalmente incompatible con Lí Lú. Cautela con deficiencia marcada de Bazo, diarrea o Humedad.'
    }
  },
  {
    number: 28, slug: 'gou-qi-zi-ju-hua-dui-yao', title: 'Gǒu Qǐ Zǐ + Jú Huā', characters: '枸杞子 + 菊花',
    category: { en: 'Nourish Liver–Kidney · Brighten Eyes', es: 'Nutrir Hígado–Riñón · Aclarar Ojos' },
    search: 'gou qi zi ju hua goji chrysanthemum liver kidney yin dry blurry tired eyes dizziness qi ju di huang wan',
    en: {
      mnemonic: 'Goji feeds the eyes from below; chrysanthemum clears them from above.',
      job: 'Nourish Liver and Kidney Yin and brighten deficient, dry or blurred eyes.',
      roles: [['Gǒu Qǐ Zǐ', 'Nourishes Liver–Kidney Yin and Blood to support vision.'], ['Jú Huā', 'Clears mild Liver Heat and directly brightens the eyes.']],
      clues: 'Dry, blurry or tired eyes with dizziness and other Liver–Kidney deficiency signs.',
      formulas: 'Qǐ Jú Dì Huáng Wán.',
      trap: 'Jú Huā with Gǒu Qǐ Zǐ treats deficient eyes; with Sāng Yè it treats excess Wind-Heat red eyes.',
      prep: 'Add delicate Jú Huā late rather than subjecting it to an unnecessarily long boil.',
      safety: 'Gǒu Qǐ Zǐ can interact with warfarin. Sudden vision loss, severe eye pain, halos or neurologic symptoms requires urgent evaluation.'
    },
    es: {
      mnemonic: 'Goji nutre ojos desde abajo; crisantemo los aclara desde arriba.',
      job: 'Nutrir Yin de Hígado/Riñón y aclarar ojos deficientes, secos o borrosos.',
      roles: [['Gǒu Qǐ Zǐ', 'Nutre Yin y Sangre de Hígado–Riñón para apoyar visión.'], ['Jú Huā', 'Aclara Calor hepático leve y mejora ojos directamente.']],
      clues: 'Ojos secos, borrosos o cansados con mareo y otros signos de deficiencia Hígado–Riñón.',
      formulas: 'Qǐ Jú Dì Huáng Wán.',
      trap: 'Jú Huā con Gǒu Qǐ Zǐ trata ojos deficientes; con Sāng Yè trata ojos rojos por exceso Viento-Calor.',
      prep: 'Añadir Jú Huā delicada al final en vez de hervirla innecesariamente por largo tiempo.',
      safety: 'Gǒu Qǐ Zǐ puede interactuar con warfarina. Pérdida visual súbita, dolor ocular grave, halos o signos neurológicos requiere urgencia.'
    }
  },
  {
    number: 29, slug: 'han-lian-cao-nu-zhen-zi-dui-yao', title: 'Hàn Lián Cǎo + Nǚ Zhēn Zǐ', characters: '旱莲草 + 女贞子',
    category: { en: 'Gently Enrich Liver–Kidney Yin', es: 'Enriquecer Suavemente Yin Hígado–Riñón' },
    search: 'han lian cao nu zhen zi eclipta privet two solstices liver kidney yin cool blood stop bleeding early graying dizziness er zhi wan',
    en: {
      mnemonic: 'The two solstices: eclipta in summer, privet in winter.',
      job: 'Gently enrich Liver–Kidney Yin, cool the Blood and stop deficiency bleeding.',
      roles: [['Hàn Lián Cǎo', 'Nourishes Liver–Kidney Yin, cools Blood and stops bleeding.'], ['Nǚ Zhēn Zǐ', 'Gently enriches Liver–Kidney Yin and improves deficient vision and dizziness.']],
      clues: 'Early graying, dizziness, blurred vision or Yin-deficiency bleeding without severe cloying.',
      formulas: 'Èr Zhì Wán — the formula is the pair.',
      trap: 'This is a gentle two-herb Yin tonic, not the heavy anchoring shell strategy of Guī Bǎn + Biē Jiǎ.',
      prep: 'Correct botanical identity matters because both fruits/herbs can be confused with regional substitutes.',
      safety: 'Use cautiously with chronic loose stools or Spleen deficiency. Unexplained bleeding still requires medical diagnosis.'
    },
    es: {
      mnemonic: 'Los dos solsticios: eclipta en verano y ligustro en invierno.',
      job: 'Enriquecer suavemente Yin de Hígado–Riñón, enfriar Sangre y detener sangrado por deficiencia.',
      roles: [['Hàn Lián Cǎo', 'Nutre Yin Hígado–Riñón, enfría Sangre y detiene sangrado.'], ['Nǚ Zhēn Zǐ', 'Enriquece suavemente Yin Hígado–Riñón y mejora visión/mareo por deficiencia.']],
      clues: 'Canas tempranas, mareo, visión borrosa o sangrado por deficiencia Yin sin empalago intenso.',
      formulas: 'Èr Zhì Wán — la fórmula es la pareja.',
      trap: 'Es tónico Yin suave de dos hierbas, no estrategia pesada de conchas Guī Bǎn + Biē Jiǎ.',
      prep: 'Identidad botánica correcta importa porque ambas pueden confundirse con sustitutos regionales.',
      safety: 'Cautela con diarrea crónica o deficiencia Bazo. Sangrado inexplicado aún requiere diagnóstico médico.'
    }
  },
  {
    number: 30, slug: 'gui-ban-bie-jia-dui-yao', title: 'Guī Bǎn + Biē Jiǎ', characters: '龟板 + 鳖甲',
    category: { en: 'Enrich Yin · Subdue Yang · Extinguish Wind', es: 'Enriquecer Yin · Someter Yang · Extinguir Viento' },
    search: 'gui ban bie jia tortoise plastron turtle carapace two shells yin subdue yang deficiency wind steaming bones tremors late warm disease da ding feng zhu',
    en: {
      mnemonic: 'The two shells: build below and soften what is bound.',
      job: 'Deeply enrich Yin, subdue Yang and extinguish deficiency Wind in late-stage depletion.',
      roles: [['Guī Bǎn', 'Builds Kidney Yin, Essence and bones and anchors rising Yang.'], ['Biē Jiǎ', 'Clears deficiency Heat, enters the Liver and softens hard masses or clumping.']],
      clues: 'Steaming-bone Heat, tremor or internal Wind late in warm disease with severe Yin depletion.',
      formulas: 'Dà Dìng Fēng Zhū.',
      trap: 'Biē Jiǎ with Guī Bǎn anchors deficiency Wind; with Qīng Hāo it vents lurking night fever.',
      prep: 'Crush both shells and decoct them first before adding softer medicinals.',
      safety: 'Both require pregnancy caution and authenticated legal/ethical sourcing. New tremor, weakness, confusion or seizure requires urgent medical evaluation.'
    },
    es: {
      mnemonic: 'Las dos conchas: construir abajo y suavizar lo endurecido.',
      job: 'Enriquecer Yin profundamente, someter Yang y extinguir Viento por deficiencia en agotamiento tardío.',
      roles: [['Guī Bǎn', 'Construye Yin/Esencia de Riñón y huesos, y ancla Yang ascendente.'], ['Biē Jiǎ', 'Aclara Calor por deficiencia, entra en Hígado y suaviza masas/endurecimiento.']],
      clues: 'Calor en huesos, temblor o Viento interno tardío en enfermedad caliente con agotamiento Yin grave.',
      formulas: 'Dà Dìng Fēng Zhū.',
      trap: 'Biē Jiǎ con Guī Bǎn ancla Viento por deficiencia; con Qīng Hāo ventila fiebre nocturna oculta.',
      prep: 'Triturar ambas conchas y decoctarlas primero antes de añadir medicinas más blandas.',
      safety: 'Ambas exigen cautela en embarazo y procedencia autenticada, legal y ética. Temblor nuevo, debilidad, confusión o convulsión requiere urgencia.'
    }
  },
  {
    number: 31, slug: 'du-zhong-xu-duan-dui-yao', title: 'Dù Zhòng + Xù Duàn', characters: '杜仲 + 续断',
    category: { en: 'Strengthen the Back · Calm the Fetus', es: 'Fortalecer Espalda · Calmar Feto' },
    search: 'du zhong xu duan eucommia dipsacus liver kidney yang sinews bones low back knees fetus pregnancy shou tai wan du huo ji sheng tang',
    en: {
      mnemonic: 'Rubber-thread bark plus “reconnect the broken.”',
      job: 'Supplement Liver and Kidney, strengthen sinews and bones and stabilize pregnancy.',
      roles: [['Dù Zhòng', 'Tonifies Liver–Kidney Yang and strongly supports the low back and knees.'], ['Xù Duàn', 'Tonifies without excessive cloying, reconnects injured sinews/bones and calms the fetus.']],
      clues: 'Sore weak low back and knees from deficiency; classically also threatened miscarriage.',
      formulas: 'Shòu Tāi Wán emphasizes Xù Duàn; Dú Huó Jì Shēng Tāng emphasizes Dù Zhòng.',
      trap: 'Dù Zhòng leans toward pure deficiency; Xù Duàn also addresses injury and Blood stasis around damaged tissue.',
      prep: 'Salt-processing may direct either medicinal more strongly toward the Kidney when specified by the source.',
      safety: 'Pain or bleeding during pregnancy requires obstetric evaluation. Trauma with weakness, numbness or bowel/bladder change requires urgent assessment.'
    },
    es: {
      mnemonic: 'Corteza con hilos de goma más “reconectar lo roto”.',
      job: 'Suplementar Hígado/Riñón, fortalecer tendones/huesos y estabilizar embarazo.',
      roles: [['Dù Zhòng', 'Tonifica Yang de Hígado–Riñón y apoya con fuerza espalda baja/rodillas.'], ['Xù Duàn', 'Tonifica sin empalagar demasiado, reconecta tejidos/huesos lesionados y calma feto.']],
      clues: 'Espalda baja y rodillas doloridas/débiles por deficiencia; clásicamente amenaza de aborto.',
      formulas: 'Shòu Tāi Wán enfatiza Xù Duàn; Dú Huó Jì Shēng Tāng enfatiza Dù Zhòng.',
      trap: 'Dù Zhòng se inclina a deficiencia pura; Xù Duàn también aborda lesión y estasis alrededor del tejido dañado.',
      prep: 'Procesamiento con sal puede dirigir ambas medicinas más al Riñón cuando la fuente lo especifica.',
      safety: 'Dolor o sangrado en embarazo exige evaluación obstétrica. Trauma con debilidad, entumecimiento o cambio intestinal/vesical requiere urgencia.'
    }
  },
  {
    number: 32, slug: 'dang-gui-rou-cong-rong-dui-yao', title: 'Dāng Guī + Ròu Cōng Róng', characters: '当归 + 肉苁蓉',
    category: { en: 'Warm and Moisten the Intestines', es: 'Calentar y Humedecer Intestinos' },
    search: 'dang gui rou cong rong angelica cistanche nourish blood kidney yang moisten intestines elderly constipation cold back clear urine ji chuan jian',
    en: {
      mnemonic: 'Oil the pipes from below.',
      job: 'Nourish Blood, warm Kidney Yang and moisten the intestines for deficiency constipation.',
      roles: [['Dāng Guī', 'Nourishes Blood and moistens intestinal dryness.'], ['Ròu Cōng Róng', 'Gently warms Kidney Yang while moistening rather than drying.']],
      clues: 'Constipation in an older or depleted person with cold low back, weakness and frequent clear urine.',
      formulas: 'Jì Chuān Jiān.',
      trap: 'Loose stools or Damp accumulation means the moistening direction is wrong.',
      prep: 'Wine-processing may strengthen Ròu Cōng Róng’s warming and moving direction when the formula specifies it.',
      safety: 'Avoid self-treatment for severe abdominal pain, vomiting, rigid distention or no stool and no gas; these may indicate obstruction.'
    },
    es: {
      mnemonic: 'Aceitar las tuberías desde abajo.',
      job: 'Nutrir Sangre, calentar Yang de Riñón y humedecer intestinos en estreñimiento por deficiencia.',
      roles: [['Dāng Guī', 'Nutre Sangre y humedece sequedad intestinal.'], ['Ròu Cōng Róng', 'Calienta suavemente Yang renal mientras humedece sin resecar.']],
      clues: 'Estreñimiento en persona mayor o agotada con espalda fría/débil y orina clara frecuente.',
      formulas: 'Jì Chuān Jiān.',
      trap: 'Heces sueltas o acumulación de Humedad significa que la dirección humectante es incorrecta.',
      prep: 'Procesamiento con vino puede reforzar dirección cálida/móvil de Ròu Cōng Róng cuando se especifica.',
      safety: 'No autocuidar dolor abdominal grave, vómito, distensión rígida o ausencia de heces y gases; puede ser obstrucción.'
    }
  },
  {
    number: 33, slug: 'di-yu-huai-mi-dui-yao', title: 'Dì Yú + Huái Mǐ', characters: '地榆 + 槐米',
    category: { en: 'Cool Blood · Stop Lower Bleeding', es: 'Enfriar Sangre · Detener Sangrado Inferior' },
    search: 'di yu huai mi sanguisorba sophora lower bleeding hemorrhoids blood stool hot dysentery huai hua san charred',
    en: {
      mnemonic: 'Two cold plugs for the back door.',
      job: 'Cool the Blood and stop bleeding concentrated in the lower body.',
      roles: [['Dì Yú', 'Cools Blood and astringes bleeding, especially from the lower burner.'], ['Huái Mǐ', 'Cools Liver/Large-Intestine Blood and stops hemorrhoidal or intestinal bleeding.']],
      clues: 'Bleeding hemorrhoids, bright blood in stool or hot dysentery with blood.',
      formulas: 'Huái Huā Sǎn.',
      trap: 'The pair addresses hot lower bleeding; it does not explain or rule out the biomedical source.',
      prep: 'Charred forms may emphasize hemostasis when specifically prescribed.',
      safety: 'New rectal bleeding—especially after age 50, with weight loss, black stool, dizziness or pain—requires prompt medical evaluation.'
    },
    es: {
      mnemonic: 'Dos tapones fríos para la puerta trasera.',
      job: 'Enfriar Sangre y detener sangrado concentrado en parte inferior del cuerpo.',
      roles: [['Dì Yú', 'Enfría Sangre y astringe sangrado, especialmente del Jiao inferior.'], ['Huái Mǐ', 'Enfría Sangre de Hígado/Intestino Grueso y detiene sangrado hemorroidal o intestinal.']],
      clues: 'Hemorroides sangrantes, sangre roja en heces o disentería caliente con sangre.',
      formulas: 'Huái Huā Sǎn.',
      trap: 'Aborda sangrado inferior caliente; no explica ni descarta su causa biomédica.',
      prep: 'Formas carbonizadas pueden enfatizar hemostasia cuando se prescriben específicamente.',
      safety: 'Sangrado rectal nuevo—especialmente después de 50, con pérdida de peso, heces negras, mareo o dolor—requiere evaluación pronta.'
    }
  },
  {
    number: 34, slug: 'tao-ren-hong-hua-dui-yao', title: 'Táo Rén + Hóng Huā', characters: '桃仁 + 红花',
    category: { en: 'Invigorate Blood · Dispel Stasis', es: 'Movilizar Sangre · Disipar Estasis' },
    search: 'tao ren hong hua peach kernel safflower blood stasis clots amenorrhea fixed stabbing pain trauma tao hong si wu tang xue fu zhu yu tang',
    en: {
      mnemonic: 'A peach pit and red flower break up stasis.',
      job: 'Invigorate Blood and dispel stasis for fixed pain, clots and obstructed menstruation.',
      roles: [['Táo Rén', 'Breaks Blood stasis while moistening the intestines.'], ['Hóng Huā', 'Moves Blood through the channels and intensifies stasis dispersion.']],
      clues: 'Painful periods with dark clots, amenorrhea, fixed stabbing pain or trauma.',
      formulas: 'Táo Hóng Sì Wù Tāng; Xuè Fǔ Zhú Yū Tāng.',
      trap: 'Stabbing fixed pain and purple/clotted signs point to stasis; pale diffuse pain alone points elsewhere.',
      prep: 'Táo Rén is commonly crushed before decoction to improve extraction.',
      safety: 'Contraindicated in pregnancy and requires anticoagulant/antiplatelet screening. Active bleeding or suspected internal injury needs urgent care.'
    },
    es: {
      mnemonic: 'Un hueso de durazno y una flor roja rompen estasis.',
      job: 'Movilizar Sangre y disipar estasis en dolor fijo, coágulos y menstruación obstruida.',
      roles: [['Táo Rén', 'Rompe estasis de Sangre mientras humedece intestinos.'], ['Hóng Huā', 'Mueve Sangre por canales e intensifica dispersión de estasis.']],
      clues: 'Reglas dolorosas con coágulos oscuros, amenorrea, dolor fijo punzante o trauma.',
      formulas: 'Táo Hóng Sì Wù Tāng; Xuè Fǔ Zhú Yū Tāng.',
      trap: 'Dolor fijo punzante y signos morados/coagulados indican estasis; dolor pálido difuso aislado apunta a otra cosa.',
      prep: 'Táo Rén suele triturarse antes de decocción para mejorar extracción.',
      safety: 'Contraindicada en embarazo y exige revisar anticoagulantes/antiagregantes. Sangrado activo o lesión interna sospechada requiere urgencia.'
    }
  },
  {
    number: 35, slug: 'ru-xiang-mo-yao-dui-yao', title: 'Rǔ Xiāng + Mò Yào', characters: '乳香 + 没药',
    category: { en: 'Move Qi and Blood · Stop Pain', es: 'Mover Qi y Sangre · Detener Dolor' },
    search: 'ru xiang mo yao frankincense myrrh blood qi trauma sores swelling pain generate flesh bi xiao huo luo dan',
    en: {
      mnemonic: 'Frankincense and myrrh: the wise men’s pain kit.',
      job: 'Invigorate Blood, move Qi, reduce swelling, stop pain and support tissue repair.',
      roles: [['Rǔ Xiāng', 'Moves Qi and Blood through the channels and relaxes sinew tension.'], ['Mò Yào', 'Breaks Blood stasis more directly and reduces fixed swelling and pain.']],
      clues: 'Trauma, painful sores, chest or abdominal pain and painful obstruction with swelling.',
      formulas: 'Xiǎo Huó Luò Dān.',
      trap: 'Rǔ Xiāng leans slightly more to moving Qi; Mò Yào slightly more to breaking Blood stasis.',
      prep: 'Use processed resins when prescribed to reduce stickiness, smoke and gastrointestinal irritation.',
      safety: 'Contraindicated in pregnancy; screen anticoagulants and bleeding disorders. Both can cause nausea and should not replace evaluation of chest or severe abdominal pain.'
    },
    es: {
      mnemonic: 'Incienso y mirra: el botiquín analgésico de los sabios.',
      job: 'Movilizar Sangre, mover Qi, reducir hinchazón, detener dolor y apoyar reparación tisular.',
      roles: [['Rǔ Xiāng', 'Mueve Qi/Sangre por canales y relaja tensión tendinosa.'], ['Mò Yào', 'Rompe estasis de Sangre más directamente y reduce hinchazón/dolor fijo.']],
      clues: 'Trauma, llagas dolorosas, dolor torácico/abdominal y obstrucción dolorosa con hinchazón.',
      formulas: 'Xiǎo Huó Luò Dān.',
      trap: 'Rǔ Xiāng se inclina algo más a mover Qi; Mò Yào a romper estasis de Sangre.',
      prep: 'Usar resinas procesadas cuando se indique para reducir pegajosidad, humo e irritación gastrointestinal.',
      safety: 'Contraindicada en embarazo; revisar anticoagulantes y trastornos hemorrágicos. Ambas pueden causar náusea y no sustituyen evaluación de dolor torácico/abdominal grave.'
    }
  },
  {
    number: 36, slug: 'san-leng-e-zhu-dui-yao', title: 'Sān Léng + É Zhú', characters: '三棱 + 莪术',
    category: { en: 'Break Qi and Blood · Disperse Masses', es: 'Romper Qi y Sangre · Dispersar Masas' },
    search: 'san leng e zhu sparganium zedoary demolition crew break blood qi abdominal masses amenorrhea food stagnation xiang leng wan pregnancy mang xiao',
    en: {
      mnemonic: 'The demolition crew for hard accumulations.',
      job: 'Forcefully break Blood and Qi, disperse masses and relieve severe fixed distention and pain.',
      roles: [['Sān Léng', 'Breaks Blood stasis more strongly and attacks fixed masses.'], ['É Zhú', 'Breaks constrained Qi more strongly and reduces food accumulation.']],
      clues: 'Abdominal masses, amenorrhea from stasis or severe food stagnation with fixed pain.',
      formulas: 'Xiāng Léng Wán.',
      trap: 'This is a forceful excess-stasis strategy, not a gentle Blood-moving pair for a deficient patient.',
      prep: 'Vinegar-processing may direct the herbs toward the Liver and strengthen pain-relieving action when specified.',
      safety: 'Contraindicated in pregnancy. Classical 19 Wèi warning: Máng Xiāo fears Sān Léng. Any new abdominal mass requires biomedical diagnosis.'
    },
    es: {
      mnemonic: 'El equipo de demolición para acumulaciones duras.',
      job: 'Romper con fuerza Sangre/Qi, dispersar masas y aliviar distensión/dolor fijo severo.',
      roles: [['Sān Léng', 'Rompe estasis de Sangre con más fuerza y ataca masas fijas.'], ['É Zhú', 'Rompe Qi constreñido con más fuerza y reduce acumulación alimentaria.']],
      clues: 'Masas abdominales, amenorrea por estasis o estancamiento alimentario grave con dolor fijo.',
      formulas: 'Xiāng Léng Wán.',
      trap: 'Es estrategia potente para estasis-exceso, no pareja suave para mover Sangre en paciente deficiente.',
      prep: 'Procesamiento con vinagre puede dirigir al Hígado y reforzar alivio del dolor cuando se especifica.',
      safety: 'Contraindicada en embarazo. Advertencia clásica 19 Wèi: Máng Xiāo teme a Sān Léng. Masa abdominal nueva requiere diagnóstico biomédico.'
    }
  },
  {
    number: 37, slug: 'ban-xia-chen-pi-dui-yao', title: 'Bàn Xià + Chén Pí', characters: '半夏 + 陈皮',
    category: { en: 'Dry Dampness · Transform Phlegm', es: 'Secar Humedad · Transformar Flema' },
    search: 'ban xia chen pi pinellia citrus two aged dry damp transform phlegm regulate qi nausea chest fullness er chen tang prepared aconite',
    en: {
      mnemonic: 'The two aged ones stop Phlegm from forming.',
      job: 'Dry Dampness, transform Phlegm, regulate Qi and harmonize the Middle.',
      roles: [['Bàn Xià', 'Dries Dampness, transforms Phlegm and redirects rebellious Stomach Qi downward.'], ['Chén Pí', 'Moves Qi so Dampness and Phlegm stop accumulating.']],
      clues: 'Copious white sputum, nausea, poor appetite and a stuffy chest with a greasy coat.',
      formulas: 'Èr Chén Tāng.',
      trap: 'Bàn Xià handles the Phlegm already present; Chén Pí corrects Qi movement that keeps producing it.',
      prep: 'Prepared Bàn Xià only; raw Bàn Xià is not an acceptable substitute.',
      safety: 'Classical 18 Fǎn: Bàn Xià is incompatible with aconite medicinals. Severe vomiting, aspiration risk or breathing difficulty requires urgent care.'
    },
    es: {
      mnemonic: 'Las dos añejas impiden que siga formándose Flema.',
      job: 'Secar Humedad, transformar Flema, regular Qi y armonizar Centro.',
      roles: [['Bàn Xià', 'Seca Humedad, transforma Flema y desciende Qi rebelde de Estómago.'], ['Chén Pí', 'Mueve Qi para que Humedad/Flema dejen de acumularse.']],
      clues: 'Esputo blanco copioso, náusea, poco apetito y pecho tapado con capa grasosa.',
      formulas: 'Èr Chén Tāng.',
      trap: 'Bàn Xià trata Flema presente; Chén Pí corrige movimiento Qi que continúa produciéndola.',
      prep: 'Solo Bàn Xià preparada; la forma cruda no es sustituto aceptable.',
      safety: '18 Fǎn clásico: Bàn Xià es incompatible con aconitos. Vómito grave, riesgo de aspiración o disnea requiere urgencia.'
    }
  },
  {
    number: 38, slug: 'ban-xia-sheng-jiang-dui-yao', title: 'Bàn Xià + Shēng Jiāng', characters: '半夏 + 生姜',
    category: { en: 'Stop Vomiting · Transform Phlegm', es: 'Detener Vómito · Transformar Flema' },
    search: 'ban xia sheng jiang pinellia fresh ginger vomiting descend stomach qi phlegm detox safety xiao ban xia tang xiao chai hu er chen',
    en: {
      mnemonic: 'The anti-vomiting pair with built-in moderation.',
      job: 'Descend rebellious Stomach Qi, stop vomiting and transform Phlegm.',
      roles: [['Bàn Xià', 'Strongly redirects Stomach Qi downward and dries Phlegm-Damp.'], ['Shēng Jiāng', 'Warms and harmonizes the Stomach while reducing Bàn Xià’s irritant toxicity.']],
      clues: 'Nausea or vomiting with Phlegm, a stuffy epigastrium and upward-rebellious Stomach Qi.',
      formulas: 'Xiǎo Bàn Xià Tāng; also used within Xiǎo Chái Hú Tāng and Èr Chén Tāng.',
      trap: 'Ginger moderates Bàn Xià but does not make raw Bàn Xià safe for dispensing.',
      prep: 'Use prepared Bàn Xià only; ginger-processing also creates Jiāng Bàn Xià for vomiting-focused use.',
      safety: 'Classical 18 Fǎn prohibits Bàn Xià with aconite. Persistent vomiting, blood, severe pain, confusion or dehydration requires urgent evaluation.'
    },
    es: {
      mnemonic: 'La pareja antiemética con moderación incorporada.',
      job: 'Descender Qi rebelde de Estómago, detener vómito y transformar Flema.',
      roles: [['Bàn Xià', 'Desciende fuertemente Qi de Estómago y seca Flema-Humedad.'], ['Shēng Jiāng', 'Calienta/armoniza Estómago mientras reduce toxicidad irritante de Bàn Xià.']],
      clues: 'Náusea o vómito con Flema, epigastrio tapado y Qi de Estómago rebelde hacia arriba.',
      formulas: 'Xiǎo Bàn Xià Tāng; también dentro de Xiǎo Chái Hú Tāng y Èr Chén Tāng.',
      trap: 'Jengibre modera Bàn Xià, pero no hace segura la Bàn Xià cruda para dispensar.',
      prep: 'Solo Bàn Xià preparada; procesarla con jengibre crea Jiāng Bàn Xià enfocada en vómito.',
      safety: '18 Fǎn clásico prohíbe Bàn Xià con aconito. Vómito persistente, sangre, dolor grave, confusión o deshidratación requiere urgencia.'
    }
  },
  {
    number: 39, slug: 'gua-lou-xie-bai-dui-yao', title: 'Guā Lóu + Xiè Bái', characters: '瓜蒌 + 薤白',
    category: { en: 'Unblock Chest Yang · Dissipate Binding', es: 'Desbloquear Yang Torácico · Disipar Opresión' },
    search: 'gua lou xie bai trichosanthes allium chest bi phlegm chest yang pain back breathlessness gua lou xie bai ban xia tang heart attack',
    en: {
      mnemonic: 'Sweep out Phlegm and light the chest’s pilot flame.',
      job: 'Unblock chest Yang, dissipate Phlegm binding and move Qi through the chest.',
      roles: [['Guā Lóu', 'Clears and sweeps Phlegm from the chest while expanding constraint.'], ['Xiè Bái', 'Warms and opens obstructed chest Yang and moves Qi.']],
      clues: 'Chest painful obstruction radiating to the back, breathlessness and a constricted chest.',
      formulas: 'Guā Lóu Xiè Bái Bàn Xià Tāng.',
      trap: 'The cool Phlegm-clearing fruit and warm Yang-opening bulb deliberately balance one another.',
      prep: 'Follow formula-specific instructions for Guā Lóu fruit, peel or seed; they are not interchangeable parts.',
      safety: 'Classical 18 Fǎn: Guā Lóu is incompatible with aconite. Chest pain must be evaluated first for heart attack or another emergency.'
    },
    es: {
      mnemonic: 'Barrer Flema y encender la llama piloto del pecho.',
      job: 'Desbloquear Yang torácico, disipar opresión por Flema y mover Qi en pecho.',
      roles: [['Guā Lóu', 'Aclara/barre Flema torácica mientras expande constreñimiento.'], ['Xiè Bái', 'Calienta y abre Yang de pecho obstruido y mueve Qi.']],
      clues: 'Obstrucción dolorosa torácica que irradia a espalda, disnea y pecho constreñido.',
      formulas: 'Guā Lóu Xiè Bái Bàn Xià Tāng.',
      trap: 'Fruto fresco que aclara Flema y bulbo cálido que abre Yang se equilibran intencionalmente.',
      prep: 'Seguir instrucciones para fruto, cáscara o semilla de Guā Lóu; no son partes intercambiables.',
      safety: '18 Fǎn clásico: Guā Lóu es incompatible con aconito. Dolor torácico debe evaluarse primero por infarto u otra emergencia.'
    }
  },
  {
    number: 40, slug: 'zi-wan-kuan-dong-hua-dui-yao', title: 'Zǐ Wǎn + Kuǎn Dōng Huā', characters: '紫菀 + 款冬花',
    category: { en: 'Descend Lung Qi · Stop Cough', es: 'Descender Qi de Pulmón · Detener Tos' },
    search: 'zi wan kuan dong hua aster coltsfoot honey fried cough twins transform phlegm stop cough hot cold acute chronic she gan ma huang tang',
    en: {
      mnemonic: 'The honey-fried cough twins.',
      job: 'Moisten the Lung, descend Qi, transform Phlegm and stop cough across hot or cold, acute or chronic patterns.',
      roles: [['Zǐ Wǎn', 'Leans toward opening and transforming Phlegm while descending Lung Qi.'], ['Kuǎn Dōng Huā', 'Leans toward directly calming severe cough and moistening the Lung.']],
      clues: 'Persistent cough where both difficult expectoration and cough intensity need treatment.',
      formulas: 'Shè Gān Má Huáng Tāng.',
      trap: 'The pair is thermally flexible; surrounding herbs determine whether the full formula treats Heat or Cold.',
      prep: 'Honey-fried forms are commonly selected to strengthen moistening and cough-relieving action in chronic cases.',
      safety: 'Use professionally and avoid prolonged unsupervised use. Coughing blood, low oxygen, chest pain, high fever or breathing difficulty requires prompt medical care.'
    },
    es: {
      mnemonic: 'Las gemelas de la tos tostadas con miel.',
      job: 'Humedecer Pulmón, descender Qi, transformar Flema y detener tos en patterns calientes/fríos, agudos/crónicos.',
      roles: [['Zǐ Wǎn', 'Se inclina a abrir/transformar Flema mientras desciende Qi pulmonar.'], ['Kuǎn Dōng Huā', 'Se inclina a calmar directamente tos intensa y humedecer Pulmón.']],
      clues: 'Tos persistente donde se necesita tratar expectoración difícil e intensidad de tos.',
      formulas: 'Shè Gān Má Huáng Tāng.',
      trap: 'La pareja es térmicamente flexible; hierbas circundantes determinan si fórmula trata Calor o Frío.',
      prep: 'Formas tostadas con miel suelen elegirse para reforzar humectación y alivio de tos en casos crónicos.',
      safety: 'Uso profesional y no prolongado sin supervisión. Sangre al toser, oxígeno bajo, dolor torácico, fiebre alta o disnea requiere atención pronta.'
    }
  },
  {
    number: 41, slug: 'ting-li-zi-da-zao-dui-yao', title: 'Tíng Lì Zǐ + Dà Zǎo', characters: '葶苈子 + 大枣',
    category: { en: 'Drain Lung Fluid · Calm Wheezing', es: 'Drenar Líquido Pulmonar · Calmar Sibilancia' },
    search: 'ting li zi da zao lepidium descurainia jujube harsh drain bodyguard lung water phlegm wheezing orthopnea edema ting li da zao xie fei tang',
    en: {
      mnemonic: 'A harsh drain with a bodyguard.',
      job: 'Drain water and Phlegm from the Lung, calm wheezing and promote urination while protecting the Middle.',
      roles: [['Tíng Lì Zǐ', 'Forcefully drains Lung fluid and directs Lung Qi downward.'], ['Dà Zǎo', 'Protects Spleen/Stomach Qi from the harsh draining action.']],
      clues: 'Wheezing, inability to lie flat and facial or chest fluid accumulation.',
      formulas: 'Tíng Lì Dà Zǎo Xiè Fèi Tāng.',
      trap: 'Dà Zǎo is protective here; it does not cancel the forceful draining nature of Tíng Lì Zǐ.',
      prep: 'Tíng Lì Zǐ is commonly crushed before decoction; dose and duration require professional control.',
      safety: 'Breathlessness when lying flat, swollen legs, chest pain or blue lips may indicate heart failure or another emergency and requires urgent evaluation.'
    },
    es: {
      mnemonic: 'Un drenaje fuerte con guardaespaldas.',
      job: 'Drenar agua/Flema de Pulmón, calmar sibilancia y promover orina mientras protege Centro.',
      roles: [['Tíng Lì Zǐ', 'Drena con fuerza líquido pulmonar y dirige Qi de Pulmón hacia abajo.'], ['Dà Zǎo', 'Protege Qi de Bazo/Estómago de la acción drenante agresiva.']],
      clues: 'Sibilancia, incapacidad para acostarse y líquido acumulado en cara o pecho.',
      formulas: 'Tíng Lì Dà Zǎo Xiè Fèi Tāng.',
      trap: 'Dà Zǎo protege; no cancela la naturaleza fuertemente drenante de Tíng Lì Zǐ.',
      prep: 'Tíng Lì Zǐ suele triturarse antes de decocción; dosis y duración requieren control profesional.',
      safety: 'Disnea al acostarse, piernas hinchadas, dolor torácico o labios azules puede indicar falla cardíaca u otra emergencia.'
    }
  },
  {
    number: 42, slug: 'fu-zi-gan-jiang-dui-yao', title: 'Fù Zǐ + Gān Jiāng', characters: '附子 + 干姜',
    category: { en: 'Restore Devastated Yang', es: 'Restaurar Yang Devastado' },
    search: 'fu zi gan jiang aconite dried ginger restore devastated yang collapse cold limbs watery diarrhea si ni tang toxic prepared predecoct',
    en: {
      mnemonic: '“Fù Zǐ without Gān Jiāng is not hot.”',
      job: 'Restore devastated Yang, warm the Middle and rescue collapse from severe interior Cold.',
      roles: [['Fù Zǐ', 'Rapidly revives Yang and reaches all twelve channels.'], ['Gān Jiāng', 'Warms and anchors the restored heat in the Middle burner.']],
      clues: 'Cold limbs, profound weakness or collapse, watery diarrhea and a minute/deep pulse.',
      formulas: 'Sì Nì Tāng.',
      trap: 'This is an emergency Cold-collapse strategy, not routine treatment for feeling chilly.',
      prep: 'Prepared Fù Zǐ only; it requires professional dosing and prolonged pre-decoction before other ingredients.',
      safety: 'Contraindicated in pregnancy. Numb mouth, vomiting, weakness, palpitations or irregular pulse after aconite exposure requires emergency care or Poison Control.'
    },
    es: {
      mnemonic: '“Fù Zǐ sin Gān Jiāng no está caliente.”',
      job: 'Restaurar Yang devastado, calentar Centro y rescatar colapso por Frío interior grave.',
      roles: [['Fù Zǐ', 'Revive Yang rápidamente y alcanza los doce canales.'], ['Gān Jiāng', 'Calienta y mantiene el calor restaurado en Jiao Medio.']],
      clues: 'Extremidades frías, debilidad profunda o colapso, diarrea acuosa y pulso diminuto/profundo.',
      formulas: 'Sì Nì Tāng.',
      trap: 'Es estrategia de emergencia para colapso por Frío, no tratamiento rutinario por sentir frío.',
      prep: 'Solo Fù Zǐ preparada; requiere dosis profesional y predecocción prolongada antes de otras medicinas.',
      safety: 'Contraindicada en embarazo. Boca dormida, vómito, debilidad, palpitaciones o pulso irregular tras aconito requiere urgencia o Poison Control.'
    }
  },
  {
    number: 43, slug: 'fu-zi-rou-gui-dui-yao', title: 'Fù Zǐ + Ròu Guì', characters: '附子 + 肉桂',
    category: { en: 'Warm and Supplement Kidney Yang', es: 'Calentar y Suplementar Yang de Riñón' },
    search: 'fu zi rou gui aconite cinnamon bark ming men kidney yang fire source cold back knees clear urine impotence you gui wan shen qi wan',
    en: {
      mnemonic: 'Stoke the basement furnace at Mìng Mén.',
      job: 'Warm and supplement Kidney Yang and lead Fire back to its source.',
      roles: [['Fù Zǐ', 'Powerfully warms Kidney Yang and revives functional Fire.'], ['Ròu Guì', 'Warms Mìng Mén, enters the Blood level and guides floating Fire home.']],
      clues: 'Cold low back and knees, frequent clear urine, impotence and chronic lower-burner Cold.',
      formulas: 'Yòu Guī Wán; later versions of Shèn Qì Wán.',
      trap: 'Compared with Fù Zǐ + Gān Jiāng, this pair treats chronic Kidney-Yang deficiency rather than acute collapse.',
      prep: 'Use prepared Fù Zǐ with required pre-decoction; add Ròu Guì late or use it as powder when directed.',
      safety: 'Fù Zǐ is toxic and the pair is contraindicated in pregnancy. Classical 19 Wèi: Ròu Guì is incompatible with Chì Shí Zhī.'
    },
    es: {
      mnemonic: 'Avivar el horno del sótano en Mìng Mén.',
      job: 'Calentar/suplementar Yang de Riñón y conducir Fuego de vuelta a su fuente.',
      roles: [['Fù Zǐ', 'Calienta con fuerza Yang renal y revive Fuego funcional.'], ['Ròu Guì', 'Calienta Mìng Mén, entra al nivel Sangre y guía Fuego flotante a casa.']],
      clues: 'Espalda/rodillas frías, orina clara frecuente, impotencia y Frío crónico del Jiao inferior.',
      formulas: 'Yòu Guī Wán; versiones tardías de Shèn Qì Wán.',
      trap: 'Frente a Fù Zǐ + Gān Jiāng, trata deficiencia Yang renal crónica, no colapso agudo.',
      prep: 'Usar Fù Zǐ preparada con predecocción requerida; añadir Ròu Guì tarde o como polvo cuando se indique.',
      safety: 'Fù Zǐ es tóxica y la pareja está contraindicada en embarazo. 19 Wèi clásico: Ròu Guì es incompatible con Chì Shí Zhī.'
    }
  },
  {
    number: 44, slug: 'gan-jiang-bai-zhu-dui-yao', title: 'Gān Jiāng + Bái Zhú', characters: '干姜 + 白术',
    category: { en: 'Warm the Middle · Dry Dampness', es: 'Calentar Centro · Secar Humedad' },
    search: 'gan jiang bai zhu dried ginger atractylodes warm middle spleen dry damp cold abdominal pain loose stool vomiting li zhong wan',
    en: {
      mnemonic: 'Dry heat for a cold, soggy Middle.',
      job: 'Warm the Middle, strengthen the Spleen and dry Cold-Dampness.',
      roles: [['Gān Jiāng', 'Warms Spleen/Stomach Yang and disperses interior Cold.'], ['Bái Zhú', 'Strengthens Spleen Qi and dries Dampness that produces loose stool.']],
      clues: 'Cold abdominal pain, loose stools, vomiting, poor appetite and cold limbs from Middle deficiency-Cold.',
      formulas: 'Lǐ Zhōng Wán.',
      trap: 'The pair is warm and dry; it is wrong for Yin deficiency, dry stool or Damp-Heat.',
      prep: 'Distinguish dried Gān Jiāng from fresh Shēng Jiāng; they are not interchangeable in this warming role.',
      safety: 'Persistent vomiting, blood in stool, severe abdominal pain or dehydration requires medical assessment.'
    },
    es: {
      mnemonic: 'Calor seco para un Centro frío y empapado.',
      job: 'Calentar Centro, fortalecer Bazo y secar Frío-Humedad.',
      roles: [['Gān Jiāng', 'Calienta Yang de Bazo/Estómago y dispersa Frío interior.'], ['Bái Zhú', 'Fortalece Qi de Bazo y seca Humedad que produce heces sueltas.']],
      clues: 'Dolor abdominal frío, heces sueltas, vómito, poco apetito y extremidades frías por deficiencia-Frío del Centro.',
      formulas: 'Lǐ Zhōng Wán.',
      trap: 'Es cálida y seca; incorrecta para deficiencia Yin, heces secas o Humedad-Calor.',
      prep: 'Distinguir Gān Jiāng seca de Shēng Jiāng fresca; no son intercambiables en este papel.',
      safety: 'Vómito persistente, sangre en heces, dolor abdominal grave o deshidratación requiere evaluación.'
    }
  },
  {
    number: 45, slug: 'chai-hu-huang-qin-dui-yao', title: 'Chái Hú + Huáng Qín', characters: '柴胡 + 黄芩',
    category: { en: 'Harmonize Shaoyang', es: 'Armonizar Shaoyang' },
    search: 'chai hu huang qin bupleurum scutellaria shaoyang alternating chills fever bitter mouth rib side fullness xiao chai hu tang da chai hu',
    en: {
      mnemonic: 'Chái opens the door; Qín cleans the room.',
      job: 'Harmonize Shaoyang by venting the pathogen outward while clearing Heat inside.',
      roles: [['Chái Hú', 'Courses the Shaoyang pivot and vents the trapped pathogen outward.'], ['Huáng Qín', 'Clears Heat from the Gallbladder/Shaoyang interior.']],
      clues: 'Alternating chills and fever, bitter mouth, dry throat and rib-side fullness.',
      formulas: 'Xiǎo Chái Hú Tāng; Dà Chái Hú Tāng.',
      trap: 'The alternation and pivot signs distinguish Shaoyang from a pure Exterior or pure interior-Heat pattern.',
      prep: 'Formula context determines whether the pair gently harmonizes or joins a stronger purging strategy.',
      safety: 'Xiǎo Chái Hú Tāng should not be combined with interferon because serious pulmonary reactions have been reported. Persistent fever requires diagnosis.'
    },
    es: {
      mnemonic: 'Chái abre la puerta; Qín limpia la habitación.',
      job: 'Armonizar Shaoyang ventilando patógeno hacia afuera mientras aclara Calor interior.',
      roles: [['Chái Hú', 'Regula pivote Shaoyang y ventila patógeno atrapado hacia afuera.'], ['Huáng Qín', 'Aclara Calor del interior Vesícula Biliar/Shaoyang.']],
      clues: 'Alternancia de escalofríos/fiebre, boca amarga, garganta seca y plenitud costal.',
      formulas: 'Xiǎo Chái Hú Tāng; Dà Chái Hú Tāng.',
      trap: 'Alternancia y signos de pivote distinguen Shaoyang de Exterior puro o Calor interior puro.',
      prep: 'El contexto determina si armoniza suavemente o se une a estrategia purgante más fuerte.',
      safety: 'Xiǎo Chái Hú Tāng no debe combinarse con interferón por reacciones pulmonares graves reportadas. Fiebre persistente requiere diagnóstico.'
    }
  },
  {
    number: 46, slug: 'chai-hu-bai-shao-dui-yao', title: 'Chái Hú + Bái Sháo', characters: '柴胡 + 白芍',
    category: { en: 'Course the Liver · Nourish Blood', es: 'Mover Hígado · Nutrir Sangre' },
    search: 'chai hu bai shao bupleurum peony liver qi constraint nourish blood rib pain irritability irregular menses si ni san xiao yao san chai hu shu gan san',
    en: {
      mnemonic: 'Move the Liver and feed the Liver.',
      job: 'Course Liver Qi, nourish Blood, soften tension and relieve constraint pain.',
      roles: [['Chái Hú', 'Spreads constrained Liver Qi and restores free coursing.'], ['Bái Sháo', 'Nourishes Liver Blood and Yin and prevents excessive dispersal.']],
      clues: 'Rib-side pain, irritability, abdominal tension or irregular periods from Liver-Qi constraint with Blood deficiency.',
      formulas: 'Sì Nì Sǎn; Xiāo Yáo Sǎn; Chái Hú Shū Gān Sǎn.',
      trap: 'Chái Hú moves while Bái Sháo contains and nourishes; the therapeutic balance is the pair’s point.',
      prep: 'Relative dose shifts the emphasis between stronger coursing and stronger nourishing/softening.',
      safety: 'New severe abdominal or chest pain, jaundice, pregnancy bleeding or significant menstrual hemorrhage requires medical evaluation.'
    },
    es: {
      mnemonic: 'Mover Hígado y alimentar Hígado.',
      job: 'Mover Qi hepático, nutrir Sangre, suavizar tensión y aliviar dolor por estancamiento.',
      roles: [['Chái Hú', 'Dispersa Qi de Hígado constreñido y restaura libre circulación.'], ['Bái Sháo', 'Nutre Sangre/Yin de Hígado y evita dispersión excesiva.']],
      clues: 'Dolor costal, irritabilidad, tensión abdominal o reglas irregulares por estancamiento Qi con deficiencia de Sangre.',
      formulas: 'Sì Nì Sǎn; Xiāo Yáo Sǎn; Chái Hú Shū Gān Sǎn.',
      trap: 'Chái Hú mueve mientras Bái Sháo contiene/nutre; el equilibrio es el punto de la pareja.',
      prep: 'La dosis relativa desplaza énfasis entre movilizar más o nutrir/suavizar más.',
      safety: 'Dolor abdominal/torácico grave nuevo, ictericia, sangrado en embarazo o hemorragia menstrual importante requiere evaluación.'
    }
  },
  {
    number: 47, slug: 'huo-xiang-pei-lan-dui-yao', title: 'Huò Xiāng + Pèi Lán', characters: '藿香 + 佩兰',
    category: { en: 'Aromatically Transform Dampness', es: 'Transformar Humedad Aromáticamente' },
    search: 'huo xiang pei lan patchouli eupatorium aromatic twins damp middle summerheat nausea chest fullness sweet sticky mouth short boil huo xiang zheng qi san',
    en: {
      mnemonic: 'Two fragrant leaves air out a damp, musty Middle.',
      job: 'Aromatically transform Dampness, awaken the Spleen and relieve Summerheat-Dampness.',
      roles: [['Huò Xiāng', 'Warmer; better for vomiting and an accompanying Exterior disorder.'], ['Pèi Lán', 'More neutral; better for a sweet, greasy or foul sensation in the mouth.']],
      clues: 'Nausea, stuffy chest, poor appetite and a sweet-sticky mouth in Summerheat or Dampness.',
      formulas: 'Huò Xiāng Zhèng Qì Sǎn modified with Pèi Lán.',
      trap: 'Huò Xiāng emphasizes vomiting/Exterior; Pèi Lán emphasizes oral foulness and mild Damp-Heat.',
      prep: 'Add both aromatic medicinals late and use a short boil to preserve volatile oils.',
      safety: 'Aromatics can consume Qi and Yin with excessive use. Persistent vomiting, high fever or dehydration requires evaluation.'
    },
    es: {
      mnemonic: 'Dos hojas fragantes ventilan un Centro húmedo y mohoso.',
      job: 'Transformar Humedad aromáticamente, despertar Bazo y aliviar Humedad-Calor de verano.',
      roles: [['Huò Xiāng', 'Más cálida; mejor para vómito y trastorno Exterior acompañante.'], ['Pèi Lán', 'Más neutra; mejor para sensación dulce, grasosa o fétida en boca.']],
      clues: 'Náusea, pecho tapado, poco apetito y boca dulce-pegajosa en verano o Humedad.',
      formulas: 'Huò Xiāng Zhèng Qì Sǎn modificada con Pèi Lán.',
      trap: 'Huò Xiāng enfatiza vómito/Exterior; Pèi Lán mal olor oral y Humedad-Calor leve.',
      prep: 'Añadir ambas aromáticas tarde y hervir poco para preservar aceites volátiles.',
      safety: 'Aromáticas pueden consumir Qi/Yin con uso excesivo. Vómito persistente, fiebre alta o deshidratación requiere evaluación.'
    }
  },
  {
    number: 48, slug: 'jin-qian-cao-hai-jin-sha-dui-yao', title: 'Jīn Qián Cǎo + Hǎi Jīn Shā', characters: '金钱草 + 海金沙',
    category: { en: 'Unblock Lin · Expel Stones', es: 'Desbloquear Lin · Expulsar Cálculos' },
    search: 'jin qian cao hai jin sha lysimachia lygodium stones sand urination lin gallstones urinary stones burning san jin tang wrap',
    en: {
      mnemonic: 'Gold-coin grass dislodges the stone; sea-gold sand flushes the grit.',
      job: 'Promote urination, unblock painful Lin and help expel urinary or biliary stones.',
      roles: [['Jīn Qián Cǎo', 'Dislodges and expels stones while clearing Damp-Heat.'], ['Hǎi Jīn Shā', 'Flushes fine gravel through the urine and relieves burning pain.']],
      clues: 'Stone-type urinary pain, burning urination or gallstone presentation with Damp-Heat.',
      formulas: 'Sān Jīn Tāng.',
      trap: 'The pair may support stone passage but does not establish stone size, obstruction or infection severity.',
      prep: 'Wrap Hǎi Jīn Shā in a cloth bag during decoction because its fine spores cloud the liquid.',
      safety: 'Fever with flank pain, vomiting, jaundice, severe colic or inability to urinate may signal obstruction/infection and requires emergency care.'
    },
    es: {
      mnemonic: 'Hierba moneda de oro desprende piedra; arena de oro marino lava grava.',
      job: 'Promover orina, desbloquear Lin doloroso y ayudar a expulsar cálculos urinarios o biliares.',
      roles: [['Jīn Qián Cǎo', 'Desprende/expulsa cálculos mientras aclara Humedad-Calor.'], ['Hǎi Jīn Shā', 'Lava grava fina por orina y alivia ardor.']],
      clues: 'Dolor urinario tipo cálculo, micción ardiente o cálculos biliares con Humedad-Calor.',
      formulas: 'Sān Jīn Tāng.',
      trap: 'Puede apoyar paso de cálculo, pero no determina tamaño, obstrucción ni gravedad de infección.',
      prep: 'Envolver Hǎi Jīn Shā en bolsa de tela al decoctar porque sus esporas finas enturbian líquido.',
      safety: 'Fiebre con dolor de flanco, vómito, ictericia, cólico grave o incapacidad de orinar puede indicar obstrucción/infección y exige urgencia.'
    }
  },
  {
    number: 49, slug: 'da-huang-mang-xiao-dui-yao', title: 'Dà Huáng + Máng Xiāo', characters: '大黄 + 芒硝',
    category: { en: 'Purge Heat · Soften Hardness', es: 'Purgar Calor · Ablandar Dureza' },
    search: 'da huang mang xiao rhubarb glauber salt yangming bowel excess dry hard stool distention tidal fever delirium da cheng qi tiao wei cheng qi dissolve',
    en: {
      mnemonic: 'The General pushes; the salt softens.',
      job: 'Purge Heat accumulation and soften dry hardness in Yangming bowel excess.',
      roles: [['Dà Huáng', 'Drives accumulated Heat and stool forcefully downward.'], ['Máng Xiāo', 'Draws water into the bowel and softens hard, dry stool.']],
      clues: 'Dry hard stool, focal distention/fullness, tidal fever, delirium and a forceful excess pulse.',
      formulas: 'Dà Chéng Qì Tāng; Tiáo Wèi Chéng Qì Tāng.',
      trap: 'This is interior excess with hardness, not deficiency constipation needing moistening and tonification.',
      prep: 'Add Dà Huáng late when purgation is intended; dissolve Máng Xiāo in the strained decoction and never cook it directly.',
      safety: 'Contraindicated in pregnancy. Classical 19 Wèi warnings involve Máng Xiāo with Sān Léng and Liú Huáng. A rigid tender abdomen or no stool and gas is a surgical emergency.'
    },
    es: {
      mnemonic: 'El General empuja; la sal ablanda.',
      job: 'Purgar acumulación de Calor y ablandar dureza seca en exceso intestinal Yangming.',
      roles: [['Dà Huáng', 'Empuja con fuerza Calor/heces acumuladas hacia abajo.'], ['Máng Xiāo', 'Atrae agua al intestino y ablanda heces duras/secas.']],
      clues: 'Heces duras/secas, distensión/plenitud focal, fiebre mareal, delirio y pulso fuerte por exceso.',
      formulas: 'Dà Chéng Qì Tāng; Tiáo Wèi Chéng Qì Tāng.',
      trap: 'Es exceso interior con dureza, no estreñimiento por deficiencia que necesita humectar/tonificar.',
      prep: 'Añadir Dà Huáng tarde para purgar; disolver Máng Xiāo en decocción colada y nunca cocinarla directamente.',
      safety: 'Contraindicada en embarazo. 19 Wèi clásico relaciona Máng Xiāo con Sān Léng y Liú Huáng. Abdomen rígido/doloroso o sin heces/gases es emergencia quirúrgica.'
    }
  },
  {
    number: 50, slug: 'long-gu-mu-li-dui-yao', title: 'Lóng Gǔ + Mǔ Lì', characters: '龙骨 + 牡蛎',
    category: { en: 'Anchor Yang · Calm Spirit · Secure Leakage', es: 'Anclar Yang · Calmar Shen · Asegurar Pérdidas' },
    search: 'long gu mu li dragon bone oyster shell heavy anchors calm spirit yang astringe sweat semen discharge palpitations insomnia fright gui zhi chai hu zhen gan xi feng',
    en: {
      mnemonic: 'Two heavy anchors: fossil bone and oyster shell.',
      job: 'Settle and calm the Spirit, anchor floating Yang and secure deficiency leakage.',
      roles: [['Lóng Gǔ', 'Calms fright and the Spirit more strongly and astringes leakage.'], ['Mǔ Lì', 'Anchors rising Yang more strongly and softens hard lumps and nodules.']],
      clues: 'Palpitations, insomnia, fright, dizziness, night sweats, seminal leakage or chronic discharge.',
      formulas: 'Guì Zhī Jiā Lóng Gǔ Mǔ Lì Tāng; Chái Hú Jiā Lóng Gǔ Mǔ Lì Tāng; Zhèn Gān Xī Fēng Tāng.',
      trap: 'Lóng Gǔ emphasizes calming/astringing; Mǔ Lì emphasizes anchoring/softening hardness.',
      prep: 'Crush and decoct both first; calcined forms shift the emphasis toward astringing leakage.',
      safety: 'Mineral/shell products require identity and contaminant testing. New palpitations, fainting, severe headache or neurologic deficits requires urgent evaluation.'
    },
    es: {
      mnemonic: 'Dos anclas pesadas: hueso fósil y concha de ostra.',
      job: 'Asentar/calmar Shen, anclar Yang flotante y asegurar pérdidas por deficiencia.',
      roles: [['Lóng Gǔ', 'Calma susto/Shen con más fuerza y astringe pérdidas.'], ['Mǔ Lì', 'Ancla Yang ascendente con más fuerza y suaviza masas/nódulos duros.']],
      clues: 'Palpitaciones, insomnio, susto, mareo, sudor nocturno, pérdida seminal o descarga crónica.',
      formulas: 'Guì Zhī Jiā Lóng Gǔ Mǔ Lì Tāng; Chái Hú Jiā Lóng Gǔ Mǔ Lì Tāng; Zhèn Gān Xī Fēng Tāng.',
      trap: 'Lóng Gǔ enfatiza calmar/astringir; Mǔ Lì anclar/suavizar dureza.',
      prep: 'Triturar y decoctar ambas primero; formas calcinadas cambian énfasis hacia astringir pérdidas.',
      safety: 'Minerales/conchas requieren prueba de identidad y contaminantes. Palpitaciones nuevas, síncope, cefalea grave o déficit neurológico exige urgencia.'
    }
  },
  {
    number: 51, slug: 'tian-ma-gou-teng-dui-yao', title: 'Tiān Má + Gōu Téng', characters: '天麻 + 钩藤',
    category: { en: 'Calm the Liver · Extinguish Wind', es: 'Calmar Hígado · Extinguir Viento' },
    search: 'tian ma gou teng gastrodia uncaria wind dizziness liver yang rising hypertension headache tremor spasm tian ma gou teng yin add late stroke',
    en: {
      mnemonic: 'The Wind-and-dizziness pair.',
      job: 'Calm the Liver, extinguish internal Wind, stop dizziness and headache and ease spasms.',
      roles: [['Tiān Má', 'Neutral and versatile; extinguishes Wind and treats dizziness across deficiency or excess patterns.'], ['Gōu Téng', 'Cool; clears Liver Heat, subdues rising Yang and relieves tremor or spasm.']],
      clues: 'Liver-Yang-rising headache, dizziness, hypertension pattern, tremor or muscular spasm.',
      formulas: 'Tiān Má Gōu Téng Yǐn.',
      trap: 'Tiān Má can address many dizziness patterns; Gōu Téng makes the pair especially appropriate when Heat or rising Yang is present.',
      prep: 'Add Gōu Téng late and boil it only briefly to preserve its Wind-extinguishing activity.',
      safety: 'Monitor blood pressure and prescription-drug effects. Sudden severe headache, one-sided weakness, facial droop or slurred speech requires immediate stroke protocol.'
    },
    es: {
      mnemonic: 'La pareja del Viento y el mareo.',
      job: 'Calmar Hígado, extinguir Viento interno, detener mareo/cefalea y aliviar espasmos.',
      roles: [['Tiān Má', 'Neutra y versátil; extingue Viento y trata mareo en patterns de deficiencia o exceso.'], ['Gōu Téng', 'Fresca; aclara Calor de Hígado, somete Yang ascendente y alivia temblor/espasmo.']],
      clues: 'Cefalea y mareo por Yang de Hígado ascendente, pattern hipertensivo, temblor o espasmo muscular.',
      formulas: 'Tiān Má Gōu Téng Yǐn.',
      trap: 'Tiān Má aborda muchos mareos; Gōu Téng hace la pareja especialmente apropiada cuando hay Calor o Yang ascendente.',
      prep: 'Añadir Gōu Téng al final y hervirla brevemente para preservar actividad que extingue Viento.',
      safety: 'Vigilar presión y efectos de fármacos prescritos. Cefalea súbita grave, debilidad unilateral, caída facial o habla arrastrada exige protocolo de ictus inmediato.'
    }
  }
];

function esc(value) {
  return String(value).replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;');
}

function detailPage(pair, language) {
  const es = language === 'es';
  const data = pair[language];
  const index = `dui-yao${es ? '-es' : ''}.html`;
  const alternate = `${pair.slug}${es ? '' : '-es'}.html`;
  const roles = data.roles.map(role => `<tr><th scope="row">${esc(role[0])}</th><td>${esc(role[1])}</td></tr>`).join('');
  return `<!doctype html><html lang="${es ? 'es' : 'en'}"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="description" content="${esc(data.job)}"><title>${esc(pair.title)} | Duì Yào | AcupunctureTests</title><link rel="alternate" hreflang="${es ? 'en' : 'es'}" href="${alternate}"><link rel="stylesheet" href="comparisons.css?v=${cssVersion}"></head><body>
<header class="comparison-topbar"><div class="comparison-shell comparison-topbar__inner" style="flex-wrap:wrap;padding:10px 0"><a class="comparison-brand" href="${index}"><span class="comparison-brand__mark" aria-hidden="true">☯</span><span>${es ? 'Parejas Duì Yào' : 'Duì Yào Pairs'}</span></a><span style="display:flex;gap:8px;flex-wrap:wrap"><a class="comparison-back" href="${alternate}">${es ? 'English' : 'Español'}</a><a class="comparison-back" href="${index}">${es ? '← Volver a Duì Yào' : '← Back to Duì Yào'}</a></span></div></header>
<main class="comparison-shell"><article class="study-note"><p class="comparison-eyebrow">${es ? `Pareja Appendix B · ${pair.number} de 51` : `Appendix B pair · ${pair.number} of 51`}</p><h1 class="study-note__title">${esc(pair.title)}</h1><p style="font-size:1.35rem;margin-top:-8px">${esc(pair.characters)}</p>
<section class="study-note__block"><h2 class="study-note__heading">${es ? 'Una imagen, un trabajo' : 'One picture, one job'}</h2><table class="study-note__focus"><tbody><tr><th>${es ? 'Imagen' : 'Picture'}</th><td>${esc(data.mnemonic)}</td></tr><tr><th>${es ? 'Trabajo conjunto' : 'Combined job'}</th><td>${esc(data.job)}</td></tr><tr><th>${es ? 'Categoría' : 'Category'}</th><td>${esc(pair.category[language])}</td></tr></tbody></table></section>
<section class="study-note__block"><h2 class="study-note__heading study-note__heading--orange">${es ? 'Cómo funciona la pareja' : 'How the pair works'}</h2><table><thead><tr><th>${es ? 'Hierba' : 'Herb'}</th><th>${es ? 'Papel dentro de la pareja' : 'Role within the pair'}</th></tr></thead><tbody>${roles}</tbody></table></section>
<section class="study-note__block"><h2 class="study-note__heading study-note__heading--purple">${es ? 'Enfoque de examen y seguridad' : 'Exam focus and safety'}</h2><table class="study-note__focus"><tbody><tr><th>${es ? 'Pistas' : 'Clues'}</th><td>${esc(data.clues)}</td></tr><tr><th>${es ? 'Fórmula firma' : 'Signature formula'}</th><td>${esc(data.formulas)}</td></tr><tr><th>${es ? 'Trampa' : 'Trap'}</th><td>${esc(data.trap)}</td></tr><tr><th>${es ? 'Preparación' : 'Preparation'}</th><td>${esc(data.prep)}</td></tr><tr><th>${es ? 'Seguridad' : 'Safety'}</th><td>${esc(data.safety)}</td></tr></tbody></table></section>
</article></main><footer class="comparison-footer"><div class="comparison-shell">${es ? 'Grupo independiente · Parejas Duì Yào de Appendix B' : 'Independent group · Appendix B Duì Yào pairs'}</div></footer></body></html>`;
}

function indexPage(language) {
  const es = language === 'es';
  const cards = pairs.map(pair => {
    const data = pair[language];
    return `<article class="comparison-card" data-type="dui-yao" data-sequence="${pair.number}" data-search="${esc(pair.search)}"><span class="comparison-card__type">${es ? `Pareja ${pair.number}` : `Pair ${pair.number}`}</span><h3>${esc(pair.title)}</h3><p>${esc(data.job)}</p><div class="comparison-card__category"><strong>${es ? 'Fórmula firma:' : 'Signature formula:'}</strong><br>${esc(data.formulas)}</div><a class="comparison-card__open" href="${pair.slug}${es ? '-es' : ''}.html">${es ? 'Abrir Pareja →' : 'Open Pair →'}</a></article>`;
  }).join('');
  return `<!doctype html><html lang="${es ? 'es' : 'en'}"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="description" content="${es ? 'Parejas Duì Yào de Appendix B con trabajo, fórmula y seguridad.' : 'Appendix B Duì Yào herb pairs with job, formula and safety.'}"><title>${es ? 'Parejas Duì Yào' : 'Duì Yào Pairs'} | AcupunctureTests</title><link rel="alternate" hreflang="${es ? 'en' : 'es'}" href="dui-yao${es ? '' : '-es'}.html"><link rel="stylesheet" href="comparisons.css?v=${cssVersion}"><script defer src="comparisons.js?v=20261026-3"></script></head><body>
<header class="comparison-topbar"><div class="comparison-shell comparison-topbar__inner"><a class="comparison-brand" href="/my/"><span class="comparison-brand__mark" aria-hidden="true">☯</span><span>AcupunctureTests</span></a><span style="display:flex;gap:8px;flex-wrap:wrap"><a class="comparison-back" href="dui-yao${es ? '' : '-es'}.html">${es ? 'English' : 'Español'}</a><a class="comparison-back" href="/my/">${es ? '← Dashboard' : '← Back to Dashboard'}</a></span></div></header>
<main class="comparison-shell"><section class="comparison-hero"><p class="comparison-eyebrow">${es ? 'Grupo 6 · Parejas de hierbas' : 'Group 6 · Herb pairs'}</p><h1>${es ? 'Parejas Duì Yào' : 'Duì Yào Pairs'}</h1><p>${es ? 'Biblioteca completa de las 51 parejas oficiales de Appendix B: una imagen, un trabajo conjunto, una fórmula firma y la seguridad decisiva.' : 'Complete library of all 51 official Appendix B pairs: one picture, one combined job, one signature formula and decisive safety.'}</p></section>
<nav class="comparison-collection-links" aria-label="${es ? 'Grupos de estudio' : 'Study groups'}"><a href="index${es ? '-es' : ''}.html">${es ? 'Comparaciones de Hierbas' : 'Single Herbs Comparisons'}</a><a href="formulas${es ? '-es' : ''}.html">${es ? 'Comparaciones de Fórmulas' : 'Formula Comparisons'}</a><a href="pathologies${es ? '-es' : ''}.html">${es ? 'Patologías' : 'Pathology Comparisons'}</a><a href="patterns${es ? '-es' : ''}.html">${es ? 'Patrones' : 'Pattern Comparisons'}</a><a href="families${es ? '-es' : ''}.html">${es ? 'Familias de Fórmulas' : 'Formula Families'}</a><a aria-current="page" href="dui-yao${es ? '-es' : ''}.html">${es ? 'Parejas Duì Yào' : 'Duì Yào Pairs'}</a><a href="name-traps${es ? '-es' : ''}.html">${es ? 'Trampas de Nombres' : 'Name Traps'}</a><a href="herb-families${es ? '-es' : ''}.html">${es ? 'Familias y Partes' : 'Herb Families &amp; Parts'}</a><a href="safety${es ? '-es' : ''}.html">${es ? 'Biblioteca de Seguridad' : 'Safety Library'}</a><a href="formula-name-traps${es ? '-es' : ''}.html">${es ? 'Trampas de Fórmulas' : 'Formula Name Traps'}</a><a href="misleading-formula-names${es ? '-es' : ''}.html">${es ? 'Nombres Engañosos' : 'Misleading Formula Names'}</a><a href="advanced-name-traps${es ? '-es' : ''}.html">${es ? 'Trampas Avanzadas' : 'Advanced Name Traps'}</a></nav>
<section class="comparison-tools comparison-tools--search-only"><label><span class="sr-only">${es ? 'Buscar parejas' : 'Search pairs'}</span><input class="comparison-search" type="search" placeholder="${es ? 'Buscar pareja, trabajo o fórmula' : 'Search pair, job or formula'}" data-comparison-search></label></section><section><header class="comparison-summary"><h2>${es ? 'Parejas disponibles' : 'Available pairs'}</h2><p><span data-visible-count>${pairs.length}</span> ${es ? 'de 51 parejas' : 'of 51 pairs'}</p></header><div class="comparison-grid" data-comparison-grid>${cards}</div><p class="comparison-empty" data-comparison-empty hidden>${es ? 'Ninguna pareja coincide.' : 'No Duì Yào pairs match this search.'}</p></section></main><footer class="comparison-footer"><div class="comparison-shell">${es ? 'Appendix B · Biblioteca completa · Lote 6 de 6' : 'Appendix B · Complete library · Batch 6 of 6'}</div></footer></body></html>`;
}

for (const pair of pairs) {
  writeFileSync(resolve(root, `${pair.slug}.html`), detailPage(pair, 'en'));
  writeFileSync(resolve(root, `${pair.slug}-es.html`), detailPage(pair, 'es'));
}
writeFileSync(resolve(root, 'dui-yao.html'), indexPage('en'));
writeFileSync(resolve(root, 'dui-yao-es.html'), indexPage('es'));
console.log(`Generated ${pairs.length * 2} Duì Yào pages and 2 indexes`);
