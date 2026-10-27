export const safetyLibraryExpansion = [
  {
    number: 5,
    slug: 'safety-toxic-herbs',
    title: {en: 'High-Risk & Toxic Herbs', es: 'Hierbas Tóxicas y de Alto Riesgo'},
    summary: {en: 'Identify toxic constituents, required processing and characteristic overdose signals.', es: 'Identifica componentes tóxicos, procesamiento obligatorio y señales características de sobredosis.'},
    search: 'toxic herbs aconite fu zi chuan wu cao wu ma qian zi strychnine ban xia tian nan xing ku xing ren cyanide xiang jia pi lei gong teng gan sui da ji yuan hua',
    en: {
      rule: 'Verify species, part, processing, dose, duration and patient risk every time a high-risk medicinal is considered.',
      sections: [{heading: 'High-risk medicinals', rows: [
        ['Fù Zǐ / Chuān Wū / Cǎo Wū', 'Aconitine-type alkaloids can cause paresthesia, weakness, hypotension and lethal ventricular arrhythmias.', 'Authenticated processed material and expert supervision only; never use raw or improvise processing.'],
        ['Mǎ Qián Zǐ', 'Strychnos alkaloids can cause painful rigidity, spasms and seizures with preserved awareness.', 'Narrow therapeutic range; professional processed products only where legally permitted.'],
        ['Bàn Xià / Tiān Nán Xīng / Bái Fù Zǐ', 'Raw Araceae material can cause oral burning, swelling and airway injury.', 'The specified processed form is part of the identity; raw material is not interchangeable.'],
        ['Kǔ Xìng Rén and cyanogenic kernels', 'Amygdalin may release cyanide, especially when raw, crushed or excessive.', 'Use authenticated processed material and professional dosing; keep away from children.'],
        ['Xiāng Jiā Pí', 'Periploca contains cardiac glycosides with digoxin-like rhythm toxicity.', 'Avoid with cardiac glycosides or rhythm disease unless specialist-supervised.'],
        ['Léi Gōng Téng', 'Tripterygium can cause marrow, hepatic, renal, reproductive and immune toxicity.', 'Requires specialist indication and laboratory monitoring; contraindicated in pregnancy.'],
        ['Gān Suì / Dà Jǐ / Yuán Huā / Bā Dòu', 'Drastic purgation may cause severe fluid loss, electrolyte disturbance and tissue injury.', 'Short-term expert use only; avoid in pregnancy, frailty, dehydration and suspected obstruction.']
      ]}, {heading: 'Overdose signals', rows: [
        ['Cardiac/neurologic', 'Numb mouth, spreading tingling, weakness, palpitations, irregular pulse, fainting, spasms or seizures.', 'Stop exposure and seek emergency care/poison control immediately.'],
        ['Airway/organ injury', 'Tongue or throat swelling, persistent vomiting, jaundice, reduced urine, edema or unusual bleeding.', 'Preserve the package and obtain urgent medical evaluation.']
      ]}],
      emergency: 'Do not induce vomiting or attempt home detoxification. Bring the package, prescription, batch, dose and timing to emergency care.'
    },
    es: {
      rule: 'Verificar especie, parte, procesamiento, dosis, duración y riesgo del paciente cada vez que se considere un medicinal de alto riesgo.',
      sections: [{heading: 'Medicinales de alto riesgo', rows: [
        ['Fù Zǐ / Chuān Wū / Cǎo Wū', 'Alcaloides tipo aconitina causan parestesia, debilidad, hipotensión y arritmia ventricular mortal.', 'Solo material procesado autenticado y supervisión experta; nunca crudo ni procesamiento improvisado.'],
        ['Mǎ Qián Zǐ', 'Alcaloides de Strychnos causan rigidez dolorosa, espasmos y convulsiones con conciencia preservada.', 'Margen terapéutico estrecho; solo producto procesado profesional donde sea legal.'],
        ['Bàn Xià / Tiān Nán Xīng / Bái Fù Zǐ', 'Araceae cruda causa ardor oral, edema y lesión de vía aérea.', 'La forma procesada indicada es parte de la identidad; la cruda no es intercambiable.'],
        ['Kǔ Xìng Rén y semillas cianogénicas', 'Amigdalina puede liberar cianuro, sobre todo cruda, triturada o excesiva.', 'Usar material procesado autenticado y dosis profesional; alejar de niños.'],
        ['Xiāng Jiā Pí', 'Periploca contiene glucósidos cardíacos con toxicidad del ritmo tipo digoxina.', 'Evitar con glucósidos cardíacos o enfermedad del ritmo salvo supervisión especializada.'],
        ['Léi Gōng Téng', 'Tripterygium causa toxicidad medular, hepática, renal, reproductiva e inmunitaria.', 'Requiere especialista y laboratorio; contraindicado en embarazo.'],
        ['Gān Suì / Dà Jǐ / Yuán Huā / Bā Dòu', 'Purgación drástica causa pérdida grave de líquidos, alteración electrolítica y lesión tisular.', 'Solo uso experto corto; evitar en embarazo, debilidad, deshidratación y obstrucción.']
      ]}, {heading: 'Señales de sobredosis', rows: [
        ['Cardíacas/neurológicas', 'Boca dormida, hormigueo progresivo, debilidad, palpitaciones, pulso irregular, desmayo, espasmos o convulsión.', 'Suspender y acudir inmediatamente a urgencias/toxicología.'],
        ['Vía aérea/órganos', 'Hinchazón de lengua o garganta, vómito persistente, ictericia, orina reducida, edema o sangrado inusual.', 'Conservar envase y obtener evaluación urgente.']
      ]}],
      emergency: 'No provocar vómito ni intentar desintoxicación casera. Llevar envase, receta, lote, dosis y horario a urgencias.'
    }
  },
  {
    number: 6,
    slug: 'safety-pregnancy',
    title: {en: 'Pregnancy & Lactation', es: 'Embarazo y Lactancia'},
    summary: {en: 'Screen uterine stimulation, Blood movement, purgation, toxicity and infant exposure before prescribing.', es: 'Evalúa estimulación uterina, movimiento de Sangre, purgación, toxicidad y exposición infantil antes de prescribir.'},
    search: 'pregnancy lactation breastfeeding miscarriage uterine blood moving purgative toxic she xiang san leng e zhu gan sui da ji yuan hua ba dou aconite',
    en: {
      rule: 'Pregnancy is not a setting for self-treatment; benefit, trimester, dose, processing and alternatives require qualified review.',
      sections: [{heading: 'Major risk groups', rows: [
        ['Strong Blood breakers', 'Sān Léng, É Zhú, Shuǐ Zhì and Tǔ Biē Chóng may disturb pregnancy.', 'Generally avoid unless a specialist determines an exceptional indication.'],
        ['Drastic purgatives', 'Bā Dòu, Gān Suì, Dà Jǐ, Yuán Huā and Qiān Niú Zǐ may cause severe cramping and fluid loss.', 'Contraindicated or avoided during pregnancy.'],
        ['Penetrating/opening agents', 'Shè Xiāng and similarly forceful movers may stimulate uterine activity.', 'Avoid and verify wildlife/legal status.'],
        ['Toxic medicinals', 'Aconites, Mǎ Qián Zǐ, Léi Gōng Téng and toxic minerals risk maternal and fetal harm.', 'Do not use outside specialist protocols; several are contraindicated.'],
        ['Strong downward movers', 'Dà Huáng, Máng Xiāo and Zhǐ Shí require case-specific caution.', 'A common herb is not automatically safe at any dose.']
      ]}, {heading: 'Traditional pregnancy-avoidance list supplied for review', rows: [
        ['Shuǐ Zhì', 'Hirudo/Whitmania: strongly breaks Blood and disperses stasis.', 'Avoid during pregnancy.'],
        ['Shè Xiāng', 'Moschus: strongly opens the orifices and moves Blood.', 'Avoid during pregnancy; also verify protected-wildlife status.'],
        ['Bān Máo', 'Mylabris: intensely toxic vesicant substance.', 'Contraindicated; not appropriate for routine internal use.'],
        ['Dà Jǐ', 'Euphorbiae Radix: drastic water expellant/purgative.', 'Avoid during pregnancy.'],
        ['Shāng Lù', 'Phytolaccae Radix: toxic drastic water expellant.', 'Avoid during pregnancy.'],
        ['Sān Léng / É Zhú', 'Sparganii Rhizoma and Curcumae Rhizoma: strongly break Blood and masses.', 'Avoid during pregnancy except an exceptional specialist-directed indication.'],
        ['Bā Dòu / Qiān Niú Zǐ', 'Crotonis Fructus and Pharbitidis Semen: drastic purgatives.', 'Contraindicated or avoided during pregnancy.'],
        ['Méng Chóng', 'Tabanus: strongly breaks Blood and disperses stasis.', 'Avoid during pregnancy.']
      ]}, {heading: 'Pregnancy and lactation workflow', rows: [
        ['Before treatment', 'Confirm gestational age, prior loss, bleeding, contractions, placental problems and all medications.', 'Document rationale, alternatives and coordination with obstetric care.'],
        ['Lactation', 'Consider transfer into milk, infant prematurity, toxicity, sedation and effects on milk supply.', 'Avoid high-risk products and observe feeding, alertness, rash and stool.']
      ]}],
      emergency: 'Heavy bleeding, severe or one-sided pain, fainting, contractions, fluid leakage, reduced fetal movement or breathing difficulty requires urgent obstetric evaluation.'
    },
    es: {
      rule: 'El embarazo no es contexto de automedicación; beneficio, trimestre, dosis, procesamiento y alternativas requieren revisión cualificada.',
      sections: [{heading: 'Grupos de riesgo mayor', rows: [
        ['Rompedores fuertes de Sangre', 'Sān Léng, É Zhú, Shuǐ Zhì y Tǔ Biē Chóng pueden alterar el embarazo.', 'Generalmente evitar salvo indicación excepcional del especialista.'],
        ['Purgantes drásticos', 'Bā Dòu, Gān Suì, Dà Jǐ, Yuán Huā y Qiān Niú Zǐ causan cólico y pérdida de líquidos.', 'Contraindicados o evitados durante embarazo.'],
        ['Agentes penetrantes/de apertura', 'Shè Xiāng y movilizadores similares pueden estimular útero.', 'Evitar y verificar estado legal/de fauna.'],
        ['Medicinales tóxicos', 'Acónitos, Mǎ Qián Zǐ, Léi Gōng Téng y minerales tóxicos arriesgan madre y feto.', 'No usar fuera de protocolos especializados; varios están contraindicados.'],
        ['Descendentes fuertes', 'Dà Huáng, Máng Xiāo y Zhǐ Shí requieren cautela individual.', 'Una hierba común no es segura automáticamente a cualquier dosis.']
      ]}, {heading: 'Lista tradicional de evitación en embarazo suministrada', rows: [
        ['Shuǐ Zhì', 'Hirudo/Whitmania: rompe Sangre y dispersa estasis con fuerza.', 'Evitar durante embarazo.'],
        ['Shè Xiāng', 'Moschus: abre orificios y mueve Sangre con fuerza.', 'Evitar durante embarazo; verificar además fauna protegida.'],
        ['Bān Máo', 'Mylabris: sustancia vesicante intensamente tóxica.', 'Contraindicada; no corresponde a uso interno rutinario.'],
        ['Dà Jǐ', 'Euphorbiae Radix: expulsor de agua/purgante drástico.', 'Evitar durante embarazo.'],
        ['Shāng Lù', 'Phytolaccae Radix: expulsor drástico tóxico.', 'Evitar durante embarazo.'],
        ['Sān Léng / É Zhú', 'Sparganii Rhizoma y Curcumae Rhizoma: rompen Sangre y masas con fuerza.', 'Evitar salvo indicación excepcional dirigida por especialista.'],
        ['Bā Dòu / Qiān Niú Zǐ', 'Crotonis Fructus y Pharbitidis Semen: purgantes drásticos.', 'Contraindicados o evitados durante embarazo.'],
        ['Méng Chóng', 'Tabanus: rompe Sangre y dispersa estasis con fuerza.', 'Evitar durante embarazo.']
      ]}, {heading: 'Flujo de embarazo y lactancia', rows: [
        ['Antes de tratar', 'Confirmar edad gestacional, pérdidas, sangrado, contracciones, placenta y medicamentos.', 'Documentar razón, alternativas y coordinación obstétrica.'],
        ['Lactancia', 'Considerar paso a leche, prematuridad, toxicidad, sedación y producción.', 'Evitar alto riesgo y observar alimentación, alerta, erupción y heces.']
      ]}],
      emergency: 'Sangrado abundante, dolor grave o unilateral, desmayo, contracciones, salida de líquido, menor movimiento fetal o dificultad respiratoria exige urgencia obstétrica.'
    }
  },
  {
    number: 7,
    slug: 'safety-anticoagulants',
    title: {en: 'Anticoagulants & Bleeding Risk', es: 'Anticoagulantes y Riesgo de Sangrado'},
    summary: {en: 'Evaluate additive bleeding, INR instability and peri-procedural risk across the whole formula.', es: 'Evalúa sangrado aditivo, inestabilidad del INR y riesgo perioperatorio en la fórmula completa.'},
    search: 'anticoagulants warfarin apixaban rivaroxaban dabigatran heparin aspirin clopidogrel bleeding inr dan shen dang gui san qi hong hua tao ren gou qi zi',
    en: {
      rule: 'Reconcile every anticoagulant, antiplatelet, NSAID, supplement and herb; one familiar ingredient cannot establish safety.',
      sections: [{heading: 'High-yield screens', rows: [
        ['Blood-invigorating herbs', 'Dān Shēn, Hóng Huā, Táo Rén, Chuān Xiōng, Yù Jīn and É Zhú may add bleeding risk.', 'Assess necessity, dose, combinations and monitoring before use.'],
        ['Dāng Guī / Sān Qī', '“Tonic” or “stops bleeding” does not guarantee anticoagulant compatibility.', 'Treat as a medication-level decision, not a label shortcut.'],
        ['Gǒu Qǐ Zǐ', 'Case reports describe elevated INR with warfarin.', 'Coordinate INR checks when starting, changing or stopping.'],
        ['DOACs versus warfarin', 'Routine INR tracks warfarin but does not measure apixaban, rivaroxaban or dabigatran effect.', 'Use the medication-specific monitoring plan with prescriber/pharmacist.'],
        ['Hemostatic herbs', 'A hemostatic herb does not safely reverse an anticoagulant.', 'Never use as home treatment for drug-related bleeding.'],
        ['Procedures', 'Acupuncture, surgery, dental work and injections have different bleeding consequences.', 'Share the full formula and follow the procedural clinician’s stop/restart plan.']
      ]}],
      emergency: 'Black stool, vomiting blood, severe headache, sudden weakness, uncontrolled bleeding, fainting or head injury while anticoagulated requires urgent evaluation.'
    },
    es: {
      rule: 'Conciliar anticoagulante, antiagregante, AINE, suplemento y hierba; un ingrediente familiar no demuestra seguridad.',
      sections: [{heading: 'Evaluaciones clave', rows: [
        ['Hierbas que vigorizan Sangre', 'Dān Shēn, Hóng Huā, Táo Rén, Chuān Xiōng, Yù Jīn y É Zhú pueden sumar sangrado.', 'Evaluar necesidad, dosis, combinaciones y monitorización.'],
        ['Dāng Guī / Sān Qī', '“Tónico” o “detiene sangrado” no garantiza compatibilidad anticoagulante.', 'Tratar como decisión farmacológica, no atajo por etiqueta.'],
        ['Gǒu Qǐ Zǐ', 'Reportes de casos describen INR elevado con warfarina.', 'Coordinar INR al iniciar, cambiar o suspender.'],
        ['DOAC frente a warfarina', 'INR rutinario sigue warfarina, no mide efecto de apixabán, rivaroxabán o dabigatrán.', 'Usar plan específico con prescriptor/farmacéutico.'],
        ['Hierbas hemostáticas', 'Una hemostática no revierte anticoagulante con seguridad.', 'Nunca usar como tratamiento casero del sangrado farmacológico.'],
        ['Procedimientos', 'Acupuntura, cirugía, odontología e inyecciones tienen consecuencias distintas.', 'Compartir fórmula y seguir plan de suspensión/reinicio del profesional.']
      ]}],
      emergency: 'Heces negras, vómito de sangre, cefalea grave, debilidad súbita, sangrado incontrolable, desmayo o golpe en cabeza anticoagulado exige urgencia.'
    }
  },
  {
    number: 8,
    slug: 'safety-ma-huang-hypertension',
    title: {en: 'Hypertension, Stimulants & Má Huáng', es: 'Hipertensión, Estimulantes y Má Huáng'},
    summary: {en: 'Screen cardiovascular disease, stimulant stacking and medication conflicts before ephedra exposure.', es: 'Evalúa enfermedad cardiovascular, suma de estimulantes y conflictos farmacológicos antes de usar efedra.'},
    search: 'ma huang ephedra ephedrine hypertension blood pressure tachycardia arrhythmia stimulant caffeine decongestant maoi thyroid glaucoma',
    en: {
      rule: 'Má Huáng is a cardiovascular-active prescription herb, not a general energy or weight-loss supplement.',
      sections: [{heading: 'Cardiovascular screen', rows: [
        ['Heart and vascular disease', 'Uncontrolled hypertension, arrhythmia, coronary disease, prior stroke or heart failure increases harm.', 'Avoid unsupervised use and obtain medical risk assessment.'],
        ['Stimulant stacking', 'Caffeine, nicotine, ADHD stimulants, decongestants and weight-loss agents can add tachycardia and hypertension.', 'Reconcile prescription, OTC and supplement sources.'],
        ['MAO inhibitors', 'Combined catecholamine effects may cause dangerous hypertension or hyperthermia.', 'Avoid unless explicitly managed by an appropriate specialist.'],
        ['Other risk states', 'Hyperthyroidism, glaucoma, urinary retention, heat, dehydration and intense exercise may magnify harm.', 'Assess control and avoid performance/weight-loss use.'],
        ['Identity and duration', 'Má Huáng stem differs from Má Huáng Gēn; concentrates change exposure.', 'Use exact authenticated part for the shortest justified professional course.'],
        ['Monitor', 'Check baseline BP/pulse and watch insomnia, tremor, anxiety, headache and sustained BP/pulse rise.', 'Stop and reassess adverse effects rather than masking them.']
      ]}],
      emergency: 'Chest pain, severe headache, fainting, neurologic deficit, marked shortness of breath or persistent rapid/irregular heartbeat requires emergency care.'
    },
    es: {
      rule: 'Má Huáng es una hierba de receta con actividad cardiovascular, no suplemento general de energía o pérdida de peso.',
      sections: [{heading: 'Evaluación cardiovascular', rows: [
        ['Enfermedad cardiovascular', 'Hipertensión no controlada, arritmia, enfermedad coronaria, ictus o insuficiencia aumenta daño.', 'Evitar uso no supervisado y obtener evaluación médica.'],
        ['Suma de estimulantes', 'Cafeína, nicotina, estimulantes TDAH, descongestionantes y adelgazantes suman taquicardia e hipertensión.', 'Conciliar receta, OTC y suplementos.'],
        ['IMAO', 'Efectos catecolaminérgicos combinados causan hipertensión o hipertermia peligrosa.', 'Evitar salvo manejo explícito del especialista.'],
        ['Otros riesgos', 'Hipertiroidismo, glaucoma, retención urinaria, calor, deshidratación y ejercicio intenso aumentan daño.', 'Evaluar control y evitar uso deportivo/adelgazante.'],
        ['Identidad y duración', 'Tallo Má Huáng difiere de Má Huáng Gēn; concentrados cambian exposición.', 'Usar parte autenticada exacta durante curso profesional mínimo.'],
        ['Monitorizar', 'Revisar PA/pulso basal y vigilar insomnio, temblor, ansiedad, cefalea y aumento sostenido.', 'Suspender y reevaluar, no ocultar efectos.']
      ]}],
      emergency: 'Dolor torácico, cefalea grave, desmayo, déficit neurológico, falta de aire marcada o latido rápido/irregular persistente exige urgencia.'
    }
  },
  {
    number: 9,
    slug: 'safety-fu-zi-cardiac-toxicity',
    title: {en: 'Fù Zǐ & Cardiac Toxicity', es: 'Fù Zǐ y Toxicidad Cardíaca'},
    summary: {en: 'Understand aconite processing, rhythm toxicity, early symptoms and emergency response.', es: 'Comprende procesamiento del acónito, toxicidad del ritmo, síntomas tempranos y respuesta urgente.'},
    search: 'fu zi aconite aconitine cardiac toxicity arrhythmia ventricular paresthesia numbness processed predecoction chuan wu cao wu',
    en: {
      rule: 'Processing reduces aconite risk but cannot make identity, dose or decoction errors harmless.',
      sections: [{heading: 'Safe-use framework', rows: [
        ['Mechanism', 'Aconitine-type alkaloids disrupt sodium channels and cardiac electrical stability.', 'Expect mixed neurologic, gastrointestinal and rhythm findings.'],
        ['Identity', 'Fù Zǐ, Chuān Wū and Cǎo Wū differ in material, potency and use.', 'Never substitute by pinyin familiarity or appearance.'],
        ['Processing/decoction', 'Correct Páo Zhì and specified prolonged pre-decoction reduce toxic alkaloids.', 'Use authenticated prepared product; never process raw aconite at home.'],
        ['Risk factors', 'Excess dose, concentrated products, alcohol, arrhythmia, electrolyte disturbance and rhythm-active drugs reduce safety.', 'Review antiarrhythmics, digoxin, diuretics and cardiac history.'],
        ['Early symptoms', 'Numb lips/tongue, tingling, weakness, dizziness, nausea, vomiting or diarrhea may begin quickly.', 'Stop immediately; do not call it a therapeutic reaction.'],
        ['Severe toxicity', 'Palpitations, slow or rapid irregular pulse, hypotension, chest symptoms or syncope.', 'Emergency cardiac monitoring and treatment may be required.']
      ]}],
      emergency: 'Suspected aconite exposure with numbness, vomiting, weakness, palpitations or irregular pulse is an emergency. Do not induce vomiting; bring the product.'
    },
    es: {
      rule: 'Procesar reduce el riesgo del acónito, pero no vuelve inocuos errores de identidad, dosis o decocción.',
      sections: [{heading: 'Marco de uso seguro', rows: [
        ['Mecanismo', 'Alcaloides tipo aconitina alteran canales de sodio y estabilidad eléctrica cardíaca.', 'Esperar hallazgos neurológicos, gastrointestinales y del ritmo mezclados.'],
        ['Identidad', 'Fù Zǐ, Chuān Wū y Cǎo Wū difieren en material, potencia y uso.', 'Nunca sustituir por familiaridad del pinyin o apariencia.'],
        ['Procesamiento/decocción', 'Páo Zhì correcto y predecocción prolongada especificada reducen alcaloides tóxicos.', 'Usar producto preparado autenticado; nunca procesar crudo en casa.'],
        ['Factores de riesgo', 'Dosis excesiva, concentrados, alcohol, arritmia, electrolitos y fármacos del ritmo reducen seguridad.', 'Revisar antiarrítmicos, digoxina, diuréticos e historia cardíaca.'],
        ['Síntomas tempranos', 'Labios/lengua dormidos, hormigueo, debilidad, mareo, náusea, vómito o diarrea comienzan rápido.', 'Suspender inmediatamente; no llamarlo reacción terapéutica.'],
        ['Toxicidad grave', 'Palpitaciones, pulso irregular lento o rápido, hipotensión, síntomas torácicos o síncope.', 'Puede requerir monitorización y tratamiento cardíaco urgente.']
      ]}],
      emergency: 'Exposición sospechada con adormecimiento, vómito, debilidad, palpitaciones o pulso irregular es emergencia. No provocar vómito; llevar producto.'
    }
  },
  {
    number: 10,
    slug: 'safety-clinical-interaction-cases',
    title: {en: 'Clinical Herb–Drug Interaction Cases', es: 'Casos Clínicos de Interacción Hierba–Fármaco'},
    summary: {en: 'Apply effect-based screening to common medication scenarios and define monitoring before treatment.', es: 'Aplica evaluación por efectos a escenarios farmacológicos comunes y define monitorización antes del tratamiento.'},
    search: 'clinical interaction warfarin digoxin antihypertensive diabetes insulin sedative transplant immunosuppressant diuretic licorice gan cao ginseng',
    en: {
      rule: 'Every interaction decision should end with an action: avoid, modify, monitor or coordinate.',
      sections: [{heading: 'Common clinical cases', rows: [
        ['Warfarin + Blood movers', 'Additive bleeding and INR instability are possible.', 'Coordinate before starting and schedule INR after any change.'],
        ['Digoxin + Gān Cǎo or Xiāng Jiā Pí', 'Licorice-related hypokalemia can increase rhythm risk; Periploca has cardiac glycosides.', 'Avoid Xiāng Jiā Pí and monitor potassium/rhythm when relevant.'],
        ['Antihypertensives + Má Huáng', 'Sympathomimetic stimulation can oppose BP control and raise pulse.', 'Avoid with uncontrolled hypertension and reconcile stimulants.'],
        ['Insulin/sulfonylurea + glucose-active tonics', 'Glucose may fall after formula or diet/appetite changes.', 'Increase glucose checks and coordinate medication adjustment.'],
        ['Sedatives/opioids + Shen-calming herbs', 'Drowsiness, falls and respiratory impairment may add.', 'Avoid alcohol/driving and assess dose, age and breathing risk.'],
        ['Transplant/biologic therapy + immune-active tonics', 'Immune modulation may conflict with the therapeutic goal or drug levels.', 'Specialist approval is required.'],
        ['Diuretics + water-draining formulas', 'Volume depletion, hypotension, kidney injury and electrolyte shifts may add.', 'Monitor weight, pressure, renal function and electrolytes.']
      ]}, {heading: 'Document the plan', rows: [
        ['Exposure and endpoint', 'Record exact product, concentration and dose; define the measurable risk endpoint.', 'Retain label/lot and specify bleeding, BP, glucose, rhythm or laboratory monitoring.'],
        ['Owner and stop plan', 'State who reviews results and what symptoms or values trigger stopping.', 'Give written instructions and communicate across practitioner, physician and pharmacist.']
      ]}],
      emergency: 'Major bleeding, severe hypoglycemia, chest pain, syncope, breathing suppression, anaphylaxis or sudden neurologic symptoms requires emergency care.'
    },
    es: {
      rule: 'Toda decisión de interacción debe terminar en acción: evitar, modificar, monitorizar o coordinar.',
      sections: [{heading: 'Casos clínicos comunes', rows: [
        ['Warfarina + movilizadores de Sangre', 'Puede sumar sangrado e inestabilidad INR.', 'Coordinar antes de iniciar y programar INR tras cambios.'],
        ['Digoxina + Gān Cǎo o Xiāng Jiā Pí', 'Hipopotasemia por regaliz aumenta riesgo del ritmo; Periploca tiene glucósidos cardíacos.', 'Evitar Xiāng Jiā Pí y controlar potasio/ritmo.'],
        ['Antihipertensivos + Má Huáng', 'Estimulación simpaticomimética se opone al control de PA y eleva pulso.', 'Evitar con hipertensión no controlada y conciliar estimulantes.'],
        ['Insulina/sulfonilurea + tónicos activos en glucosa', 'Glucosa puede bajar tras cambios de fórmula, dieta o apetito.', 'Aumentar controles y coordinar ajuste farmacológico.'],
        ['Sedantes/opioides + hierbas para Shen', 'Somnolencia, caídas y deterioro respiratorio pueden sumarse.', 'Evitar alcohol/conducción y evaluar dosis, edad y respiración.'],
        ['Trasplante/biológico + tónicos inmunoactivos', 'Inmunomodulación puede contradecir objetivo o niveles del fármaco.', 'Requiere aprobación especialista.'],
        ['Diuréticos + fórmulas drenantes', 'Se suman hipovolemia, hipotensión, lesión renal y cambios electrolíticos.', 'Controlar peso, presión, función renal y electrolitos.']
      ]}, {heading: 'Documentar el plan', rows: [
        ['Exposición y variable', 'Registrar producto, concentración y dosis; definir variable medible.', 'Conservar etiqueta/lote y especificar sangrado, PA, glucosa, ritmo o laboratorio.'],
        ['Responsable y suspensión', 'Indicar quién revisa y qué síntomas/valores obligan a detener.', 'Dar instrucciones escritas y comunicar entre profesional, médico y farmacéutico.']
      ]}],
      emergency: 'Sangrado mayor, hipoglucemia grave, dolor torácico, síncope, depresión respiratoria, anafilaxia o síntomas neurológicos súbitos exige emergencia.'
    }
  },
  {
    number: 11,
    slug: 'safety-obsolete-prohibited-substances',
    title: {en: 'Obsolete, Prohibited & Protected Substances', es: 'Sustancias Obsoletas, Prohibidas y Protegidas'},
    summary: {en: 'Recognize substances that should not be copied literally from historical formulas and select lawful strategies.', es: 'Reconoce sustancias que no deben copiarse literalmente de fórmulas históricas y elige estrategias legales.'},
    search: 'obsolete prohibited protected aristolochia guan mu tong guang fang ji qing mu xiang xi jiao rhinoceros hu gu tiger bone she xiang musk xiong dan bear bile chuan shan jia pangolin zhu sha xiong huang cites',
    en: {
      rule: 'A classical source is historical evidence, not automatic legal or safety authorization for the original substance.',
      sections: [{heading: 'Exam scope is not the same as safety or legality', rows: [
        ['Asterisk/exclusion list', 'The supplied document excludes many substances for curriculum reasons; exclusion alone does not mean toxic, illegal or obsolete.', 'Keep exam-scope labels separate from clinical safety and legal status.'],
        ['Internal contradiction found', 'Cao Wu, Fu Zi, Ma Huang, Huo Ma Ren, Shi Chang Pu, Tan Xiang and Lu Jiao Jiao appear in both the included and excluded portions.', 'Do not publish the supplied list as a current official NCCAOM list without the current official content outline.'],
        ['Verification rule', 'Exam blueprints and legal restrictions change independently over time and jurisdiction.', 'Date-stamp the source and verify against the current NCCAOM outline and local law before changing exam coverage.']
      ]}, {heading: 'Unsafe or obsolete identities', rows: [
        ['Guān Mù Tōng', 'Aristolochia manshuriensis is linked to aristolochic-acid nephropathy and cancer.', 'Do not use; distinguish from authenticated Akebia/Chuan Mu Tong where permitted.'],
        ['Guǎng Fáng Jǐ', 'Aristolochia fangchi was confused with Hàn Fáng Jǐ/Stephania tetrandra.', 'Never substitute by the shared name Fáng Jǐ.'],
        ['Qīng Mù Xiāng', 'Aristolochia root is unsafe despite historical listing.', 'Reformulate the intended action with accepted species.'],
        ['Zhū Shā / Xióng Huáng', 'Cinnabar contains mercury and realgar contains arsenic.', 'Internal use is restricted/prohibited in many places; choose safer alternatives.']
      ]}, {heading: 'Protected wildlife and legal variation', rows: [
        ['Xī Jiǎo / Hǔ Gǔ', 'Rhinoceros horn and tiger bone are prohibited wildlife materials.', 'Use lawful strategy-based substitutes; Shuǐ Niú Jiǎo may replace Xī Jiǎo where permitted.'],
        ['Chuān Shān Jiǎ', 'Pangolin scale is protected/prohibited and appears in historical formulas.', 'Use an accepted lawful substitute or redesign the formula action.'],
        ['Shè Xiāng / Xióng Dǎn', 'Natural musk and bear bile have major wildlife/legal restrictions.', 'Use approved synthetic or botanical alternatives where allowed.'],
        ['Má Huáng and jurisdiction-sensitive products', 'Rules differ by product type, concentration, location and practitioner scope.', 'Verify current local law; textbook listing or marketplace availability is not authorization.'],
        ['Identity and traceability', 'Pinyin-only labels may conceal protected species, adulterants or heavy-metal risk.', 'Require species/chemical identity, part, source, processing, lot and certificate of analysis.']
      ]}],
      emergency: 'If an obsolete or prohibited ingredient is found, stop dispensing, quarantine the batch, preserve records and follow regulator/poison-control guidance.'
    },
    es: {
      rule: 'Una fuente clásica es evidencia histórica, no autorización automática legal o de seguridad para la sustancia original.',
      sections: [{heading: 'Alcance del examen no equivale a seguridad o legalidad', rows: [
        ['Lista con asterisco/exclusión', 'El documento suministrado excluye muchas sustancias por currículo; excluir no significa por sí solo tóxica, ilegal u obsoleta.', 'Mantener separado alcance de examen, seguridad clínica y estado legal.'],
        ['Contradicción interna encontrada', 'Cao Wu, Fu Zi, Ma Huang, Huo Ma Ren, Shi Chang Pu, Tan Xiang y Lu Jiao Jiao aparecen tanto incluidos como excluidos.', 'No publicar la lista como NCCAOM oficial vigente sin el esquema oficial actual.'],
        ['Regla de verificación', 'Temarios y restricciones legales cambian de forma independiente según fecha y jurisdicción.', 'Fechar la fuente y verificar con esquema NCCAOM vigente y ley local antes de cambiar cobertura.']
      ]}, {heading: 'Identidades inseguras u obsoletas', rows: [
        ['Guān Mù Tōng', 'Aristolochia manshuriensis se vincula con nefropatía por ácido aristolóquico y cáncer.', 'No usar; distinguir de Akebia/Chuan Mu Tong autenticada donde sea permitida.'],
        ['Guǎng Fáng Jǐ', 'Aristolochia fangchi se confundió con Hàn Fáng Jǐ/Stephania tetrandra.', 'Nunca sustituir por el nombre compartido Fáng Jǐ.'],
        ['Qīng Mù Xiāng', 'Raíz de Aristolochia es insegura pese a listados históricos.', 'Reformular acción con especies aceptadas.'],
        ['Zhū Shā / Xióng Huáng', 'Cinabrio contiene mercurio y rejalgar arsénico.', 'Uso interno restringido/prohibido en muchos lugares; elegir alternativas seguras.']
      ]}, {heading: 'Fauna protegida y variación legal', rows: [
        ['Xī Jiǎo / Hǔ Gǔ', 'Cuerno de rinoceronte y hueso de tigre son materiales prohibidos.', 'Usar sustitutos legales por estrategia; Shuǐ Niú Jiǎo puede sustituir Xī Jiǎo donde se permita.'],
        ['Chuān Shān Jiǎ', 'Escama de pangolín está protegida/prohibida y aparece en fórmulas históricas.', 'Usar sustituto legal aceptado o rediseñar acción.'],
        ['Shè Xiāng / Xióng Dǎn', 'Almizcle natural y bilis de oso tienen grandes restricciones.', 'Usar alternativas sintéticas o botánicas aprobadas.'],
        ['Má Huáng y productos según jurisdicción', 'Reglas cambian por tipo, concentración, lugar y alcance profesional.', 'Verificar ley local vigente; libro o mercado no autorizan.'],
        ['Identidad y trazabilidad', 'Etiqueta solo pinyin puede ocultar especie protegida, adulterante o metal pesado.', 'Exigir identidad, parte, origen, procesamiento, lote y certificado.']
      ]}],
      emergency: 'Si aparece ingrediente obsoleto o prohibido, suspender dispensación, aislar lote, conservar registros y seguir al regulador/toxicología.'
    }
  }
];
