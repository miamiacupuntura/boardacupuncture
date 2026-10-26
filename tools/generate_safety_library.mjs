import { writeFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '../study-guide/comparisons');
const cssVersion = '20261026-2';

const topics = [
  {
    number: 1, slug: 'safety-preparation', title: { en: 'Preparation & Decoction', es: 'Preparación y Decocción' },
    summary: { en: 'Processing and timing can change direction, potency and toxicity.', es: 'El procesamiento y el momento pueden cambiar dirección, potencia y toxicidad.' },
    search: 'preparation decoction pao zhi processed predecoct add late dissolve separately powder crush char honey fried aconite ban xia',
    en: {
      rule: 'Confirm identity and required processing before deciding how the substance enters the formula.',
      sections: [
        { heading: 'Processing changes the medicinal', rows: [
          ['Aconite: Fù Zǐ / Chuān Wū / Cǎo Wū', 'Prepared product only; requires qualified prescribing and prolonged pre-decoction according to the authenticated product and applicable standard.', 'Raw or improperly prepared aconite can cause fatal arrhythmia and neurologic toxicity.'],
          ['Bàn Xià / Tiān Nán Xīng', 'Use the specified processed form; processing reduces acrid irritant toxicity.', 'Raw material is not an interchangeable substitute.'],
          ['Shēng vs. Gān vs. Pào Jiāng', 'Fresh ginger releases Exterior/stops vomiting; dried warms Interior; blast-fried emphasizes warm hemostasis.', 'The processing word is part of the prescription.'],
          ['Shēng Dì vs. Shú Dì', 'Raw-dried Rehmannia cools Blood/nourishes Yin; prepared Rehmannia tonifies Blood/Essence.', 'Do not collapse raw and prepared forms.'],
          ['Charred herbs', 'Charring often redirects an herb toward stopping bleeding, but the endpoint and product identity matter.', 'Burnt or uncontrolled homemade material is not standardized Páo Zhì.'],
          ['Honey-fried herbs', 'Honey-frying may increase moistening or tonic emphasis, as with Zhì Gān Cǎo or honey-fried Sāng Yè.', 'Account for sugars and the exact prepared form.']
        ]},
        { heading: 'Decoction timing', rows: [
          ['Pre-decoct', 'Dense minerals/shells and specified toxic roots may need to begin earlier.', 'Timing is substance-specific; “pre-decoct everything hard” is not a complete rule.'],
          ['Add late', 'Aromatic herbs and volatile medicinals such as Gōu Téng or Qīng Hāo may be added near the end when specified.', 'Long boiling can reduce volatile or heat-sensitive activity.'],
          ['Dissolve separately', 'Gelatins such as Ē Jiāo or Lù Jiǎo Jiāo are melted into the strained hot decoction.', 'Direct boiling can scorch or clump the product.'],
          ['Powder/pill rather than decoction', 'Some substances such as Qīng Dài or Lù Róng are traditionally administered in powder/pill form.', 'Route and dose must follow a qualified prescription.'],
          ['Crush before decoction', 'Some seeds or fruits release constituents better when cracked or crushed.', 'Crushing also increases extraction and can increase toxicity; verify instructions.'],
          ['Wrap before decoction', 'Fine hairs, powders or floating materials may require a cloth bag.', 'Prevents throat irritation and difficult straining.']
        ]}
      ],
      emergency: 'Never attempt home detoxification of aconite or other toxic medicinals. Numb mouth, spreading weakness, vomiting, faintness, palpitations or an irregular pulse requires emergency care/poison control.'
    },
    es: {
      rule: 'Confirmar identidad y procesamiento requerido antes de decidir cómo entra la sustancia en la fórmula.',
      sections: [
        { heading: 'El procesamiento cambia el medicinal', rows: [
          ['Acónito: Fù Zǐ / Chuān Wū / Cǎo Wū', 'Solo producto procesado; requiere prescripción cualificada y predecocción prolongada según producto autenticado y norma aplicable.', 'Acónito crudo o mal procesado puede causar arritmia fatal y toxicidad neurológica.'],
          ['Bàn Xià / Tiān Nán Xīng', 'Usar la forma procesada indicada; el procesamiento reduce toxicidad irritante acre.', 'El material crudo no es sustituto intercambiable.'],
          ['Shēng vs. Gān vs. Pào Jiāng', 'Jengibre fresco libera Exterior/detiene vómito; seco calienta Interior; tostado enfatiza hemostasia cálida.', 'La palabra de procesamiento forma parte de la receta.'],
          ['Shēng Dì vs. Shú Dì', 'Rehmannia cruda-seca enfría Sangre/nutre Yin; preparada tonifica Sangre/Esencia.', 'No agrupar formas cruda y preparada.'],
          ['Hierbas carbonizadas', 'Carbonizar suele redirigir hacia detener sangrado, pero importan punto final e identidad.', 'Material quemado casero no es Páo Zhì estandarizado.'],
          ['Hierbas fritas con miel', 'Puede aumentar énfasis humectante o tónico, como Zhì Gān Cǎo o Sāng Yè con miel.', 'Considerar azúcares y forma preparada exacta.']
        ]},
        { heading: 'Momento de decocción', rows: [
          ['Predecocción', 'Minerales/conchas densos y raíces tóxicas especificadas pueden comenzar antes.', 'El tiempo es específico; “hervir antes todo lo duro” no es regla completa.'],
          ['Añadir al final', 'Aromáticas y volátiles como Gōu Téng o Qīng Hāo pueden añadirse al final cuando se indique.', 'Hervido largo reduce actividad volátil o termosensible.'],
          ['Disolver aparte', 'Gelatinas como Ē Jiāo o Lù Jiǎo Jiāo se funden en decocción caliente colada.', 'Hervido directo puede quemar o formar grumos.'],
          ['Polvo/píldora, no decocción', 'Sustancias como Qīng Dài o Lù Róng se administran tradicionalmente en polvo/píldora.', 'Vía y dosis siguen prescripción cualificada.'],
          ['Triturar antes', 'Algunas semillas/frutos extraen mejor al romperse.', 'También aumenta extracción y posible toxicidad; verificar instrucciones.'],
          ['Envolver antes', 'Pelos finos, polvos o materiales flotantes pueden requerir bolsa de tela.', 'Evita irritación de garganta y facilita colado.']
        ]}
      ],
      emergency: 'Nunca intentar desintoxicación casera de acónito u otros tóxicos. Boca dormida, debilidad progresiva, vómito, desmayo, palpitaciones o pulso irregular exige urgencias/control toxicológico.'
    }
  },
  {
    number: 2, slug: 'safety-toxicity', title: { en: 'Toxicity & Emergency Red Flags', es: 'Toxicidad y Señales de Emergencia' },
    summary: { en: 'Recognize high-risk identities, overdose patterns and when referral cannot wait.', es: 'Reconoce identidades de alto riesgo, patrones de sobredosis y cuándo no puede esperar la derivación.' },
    search: 'toxicity emergency aconite aristolochia cinnabar mercury realgar arsenic xiang jia pi cardiac glycoside ma huang ephedra xing ren cyanide pregnancy',
    en: {
      rule: 'Identity first, then dose/process, then patient risk. A familiar pinyin sound is never enough.',
      sections: [
        { heading: 'High-risk identity groups', rows: [
          ['Aristolochia names', 'Guān Mù Tōng, Guǎng Fáng Jǐ and Qīng Mù Xiāng are obsolete aristolochic-acid sources.', 'Nephrotoxic and carcinogenic; do not use or substitute.'],
          ['Aconite family', 'Fù Zǐ, Chuān Wū and Cǎo Wū contain cardiotoxic alkaloids.', 'Prepared product and expert protocol only; pregnancy contraindication.'],
          ['Zhū Shā / Xióng Huáng', 'Cinnabar contains mercury; realgar contains arsenic.', 'Restricted/prohibited in many jurisdictions; avoid casual or prolonged internal use.'],
          ['Xiāng Jiā Pí', 'Periploca bark contains cardiac glycosides.', 'Digoxin-like toxicity and cardiac-drug interactions.'],
          ['Kǔ Xìng Rén and other cyanogenic kernels', 'Amygdalin can release cyanide, especially from raw/crushed excess.', 'Use authenticated processed material and professional dosing; keep away from children.'],
          ['Harsh water expellants', 'Gān Suì, Dà Jǐ and Yuán Huā can violently purge fluids.', 'Narrow indications, short-term expert use and major pregnancy/weak-patient cautions.'],
          ['Abrus / Xiāng Sī Zǐ', 'Rosary pea seed contains abrin.', 'Potentially fatal when chewed or ingested; not an internal substitute for Chì Xiǎo Dòu.']
        ]},
        { heading: 'Urgent red flags', rows: [
          ['Cardiac/neurologic', 'Numb lips/tongue, spreading weakness, palpitations, irregular pulse, fainting, seizures.', 'Emergency services and poison control now.'],
          ['Liver injury', 'Dark urine, jaundice, severe fatigue, right-upper abdominal pain, persistent nausea.', 'Stop suspected product and obtain urgent medical evaluation.'],
          ['Kidney injury', 'Markedly reduced urine, swelling, blood in urine, flank pain after an unknown product.', 'Urgent medical evaluation; preserve packaging for identification.'],
          ['Anaphylaxis', 'Trouble breathing, throat/tongue swelling, widespread hives, collapse.', 'Emergency epinephrine protocol and emergency services.'],
          ['Pregnancy emergency', 'Heavy bleeding, severe abdominal pain, faintness or contractions after herbal exposure.', 'Emergency obstetric evaluation.']
        ]}
      ],
      emergency: 'Do not induce vomiting unless poison control directs it. Bring the package, prescription, botanical label, dose and timing to emergency care.'
    },
    es: {
      rule: 'Primero identidad, después dosis/procesamiento y luego riesgo del paciente. Un sonido pinyin familiar nunca basta.',
      sections: [
        { heading: 'Grupos de identidad de alto riesgo', rows: [
          ['Nombres Aristolochia', 'Guān Mù Tōng, Guǎng Fáng Jǐ y Qīng Mù Xiāng son fuentes obsoletas de ácido aristolóquico.', 'Nefrotóxicas y carcinógenas; no usar ni sustituir.'],
          ['Familia del acónito', 'Fù Zǐ, Chuān Wū y Cǎo Wū contienen alcaloides cardiotóxicos.', 'Solo producto procesado y protocolo experto; contraindicado en embarazo.'],
          ['Zhū Shā / Xióng Huáng', 'Cinabrio contiene mercurio; rejalgar contiene arsénico.', 'Restringidos/prohibidos en muchas jurisdicciones; evitar uso interno casual o prolongado.'],
          ['Xiāng Jiā Pí', 'Corteza de Periploca contiene glucósidos cardíacos.', 'Toxicidad tipo digoxina e interacciones cardíacas.'],
          ['Kǔ Xìng Rén y semillas cianogénicas', 'Amigdalina puede liberar cianuro, sobre todo crudas/trituradas en exceso.', 'Usar material procesado autenticado y dosis profesional; alejar de niños.'],
          ['Expulsores drásticos de agua', 'Gān Suì, Dà Jǐ y Yuán Huā pueden purgar líquidos violentamente.', 'Indicaciones estrechas, uso experto corto y grandes cautelas en embarazo/debilidad.'],
          ['Abrus / Xiāng Sī Zǐ', 'Semilla de regaliz americano contiene abrina.', 'Potencialmente mortal al masticarse/ingerirse; no sustituye Chì Xiǎo Dòu.']
        ]},
        { heading: 'Señales urgentes', rows: [
          ['Cardíacas/neurológicas', 'Labios/lengua dormidos, debilidad progresiva, palpitaciones, pulso irregular, desmayo, convulsión.', 'Urgencias y control toxicológico ahora.'],
          ['Lesión hepática', 'Orina oscura, ictericia, fatiga grave, dolor superior derecho, náusea persistente.', 'Suspender producto sospechoso y evaluación médica urgente.'],
          ['Lesión renal', 'Orina muy reducida, edema, sangre urinaria o dolor de flanco tras producto desconocido.', 'Evaluación urgente; conservar envase para identificación.'],
          ['Anafilaxia', 'Dificultad respiratoria, hinchazón garganta/lengua, urticaria extensa, colapso.', 'Protocolo de epinefrina y servicios de emergencia.'],
          ['Emergencia en embarazo', 'Sangrado abundante, dolor abdominal intenso, desmayo o contracciones tras exposición.', 'Evaluación obstétrica de emergencia.']
        ]}
      ],
      emergency: 'No provocar vómito salvo indicación de toxicología. Llevar envase, receta, etiqueta botánica, dosis y horario a urgencias.'
    }
  },
  {
    number: 3, slug: 'safety-interactions', title: { en: 'Herb–Drug Interactions', es: 'Interacciones Hierba–Fármaco' },
    summary: { en: 'Screen by pharmacologic effect, medication class and monitoring plan.', es: 'Evalúa por efecto farmacológico, clase de medicamento y plan de monitorización.' },
    search: 'interactions warfarin anticoagulant antiplatelet digoxin blood pressure stimulant diabetes sedative diuretic electrolyte licorice ma huang goji san qi',
    en: {
      rule: 'Do not memorize isolated pairs only—look for additive effects across the entire formula and medication list.',
      sections: [
        { heading: 'High-yield interaction screens', rows: [
          ['Anticoagulants / antiplatelets', 'Blood-moving or hemostatic herbs such as Dān Shēn, Dāng Guī, Sān Qī, Hóng Huā and Táo Rén may alter bleeding risk; Gǒu Qǐ Zǐ has warfarin/INR reports.', 'Document indication, monitor bruising/bleeding and coordinate INR or medication management.'],
          ['Digoxin / antiarrhythmics', 'Xiāng Jiā Pí has cardiac glycosides; Gān Cǎo-related hypokalemia can increase arrhythmia/digoxin risk.', 'Avoid unsupervised combinations; monitor pulse, potassium and drug levels when clinically indicated.'],
          ['Blood-pressure drugs', 'Má Huáng and bitter-orange synephrine may raise heart rate/BP; Gān Cǎo can raise BP and retain sodium; several calming/draining herbs may lower BP.', 'Check baseline BP, stimulant intake and orthostatic symptoms.'],
          ['Diabetes therapy', 'Rén Shēn/Panax and other tonics may influence glucose; formulas also may contain sugars or honey-prepared products.', 'Increase glucose monitoring when starting, stopping or changing a formula.'],
          ['Diuretics / electrolytes', 'Water-draining herbs plus pharmaceutical diuretics can increase dehydration/electrolyte effects; prolonged high Gān Cǎo can lower potassium.', 'Monitor weight, renal function and electrolytes in higher-risk patients.'],
          ['Sedatives / alcohol', 'Spirit-calming herbs may add to drowsiness from hypnotics, benzodiazepines, opioids, antihistamines or alcohol.', 'Avoid driving and assess falls/respiratory risk.'],
          ['Immunosuppressive therapy', 'Immune-modulating tonics may conflict with transplant, biologic or oncology plans.', 'Coordinate with the prescribing specialist; do not infer “immune boosting” is always desirable.']
        ]},
        { heading: 'A usable workflow', rows: [
          ['1. Reconcile', 'Record every prescription, OTC drug, supplement, tea and concentrated extract.', 'Ask specifically about anticoagulants, cardiac, glucose, seizure and transplant drugs.'],
          ['2. Identify', 'Use pinyin + characters + Latin binomial + medicinal part + processing.', 'Common names alone are insufficient.'],
          ['3. Compare effects', 'Bleeding, BP, glucose, sedation, rhythm, renal/hepatic burden and electrolytes.', 'Evaluate the whole formula, not one headline herb.'],
          ['4. Plan monitoring', 'Define what will be checked, by whom and when.', 'No monitoring plan means the risk decision is incomplete.'],
          ['5. Communicate changes', 'Starting, stopping and dose changes can all alter drug effect.', 'Coordinate with the medication prescriber; never advise abrupt drug discontinuation.']
        ]}
      ],
      emergency: 'Unexpected bleeding, black stool, fainting, severe weakness, hypoglycemia, chest pain, palpitations or trouble breathing requires urgent medical evaluation.'
    },
    es: {
      rule: 'No memorizar solo pares aislados: buscar efectos aditivos en la fórmula completa y lista de medicamentos.',
      sections: [
        { heading: 'Evaluaciones de interacción clave', rows: [
          ['Anticoagulantes / antiagregantes', 'Hierbas que mueven o detienen Sangre como Dān Shēn, Dāng Guī, Sān Qī, Hóng Huā y Táo Rén pueden alterar sangrado; Gǒu Qǐ Zǐ tiene reportes con warfarina/INR.', 'Documentar indicación, vigilar hematomas/sangrado y coordinar INR o medicación.'],
          ['Digoxina / antiarrítmicos', 'Xiāng Jiā Pí tiene glucósidos cardíacos; hipopotasemia por Gān Cǎo puede aumentar arritmia/riesgo de digoxina.', 'Evitar combinaciones no supervisadas; controlar pulso, potasio y niveles cuando corresponda.'],
          ['Fármacos de presión', 'Má Huáng y sinefrina de naranja amarga pueden elevar pulso/PA; Gān Cǎo puede elevar PA/retener sodio; calmantes/drenantes pueden bajarla.', 'Comprobar PA basal, estimulantes y síntomas ortostáticos.'],
          ['Terapia de diabetes', 'Rén Shēn/Panax y otros tónicos pueden influir glucosa; fórmulas pueden contener azúcar o productos con miel.', 'Aumentar control de glucosa al iniciar, suspender o cambiar fórmula.'],
          ['Diuréticos / electrolitos', 'Drenantes más diuréticos farmacéuticos aumentan deshidratación/electrolitos; Gān Cǎo alto prolongado puede bajar potasio.', 'Controlar peso, función renal y electrolitos en pacientes de riesgo.'],
          ['Sedantes / alcohol', 'Hierbas que calman Shen pueden sumar somnolencia con hipnóticos, benzodiacepinas, opioides, antihistamínicos o alcohol.', 'Evitar conducir y evaluar caídas/respiración.'],
          ['Terapia inmunosupresora', 'Tónicos inmunomoduladores pueden chocar con planes de trasplante, biológicos u oncología.', 'Coordinar con especialista; “estimular inmunidad” no siempre es deseable.']
        ]},
        { heading: 'Flujo de trabajo útil', rows: [
          ['1. Conciliar', 'Registrar receta, OTC, suplemento, té y extracto concentrado.', 'Preguntar por anticoagulantes, cardíacos, glucosa, convulsión y trasplante.'],
          ['2. Identificar', 'Usar pinyin + caracteres + binomio latino + parte + procesamiento.', 'Nombres comunes solos no bastan.'],
          ['3. Comparar efectos', 'Sangrado, PA, glucosa, sedación, ritmo, carga renal/hepática y electrolitos.', 'Evaluar fórmula completa, no una sola hierba.'],
          ['4. Planear control', 'Definir qué se revisa, quién y cuándo.', 'Sin plan de monitorización, la decisión de riesgo está incompleta.'],
          ['5. Comunicar cambios', 'Iniciar, suspender o cambiar dosis altera efecto farmacológico.', 'Coordinar con prescriptor; nunca aconsejar suspensión brusca del fármaco.']
        ]}
      ],
      emergency: 'Sangrado inesperado, heces negras, desmayo, debilidad grave, hipoglucemia, dolor torácico, palpitaciones o dificultad respiratoria exige evaluación urgente.'
    }
  },
  {
    number: 4, slug: 'safety-regulation', title: { en: 'Regulation, Identity & Sourcing', es: 'Regulación, Identidad y Procedencia' },
    summary: { en: 'A safe prescription depends on the exact species, part, processing, supplier and jurisdiction.', es: 'Una receta segura depende de especie, parte, procesamiento, proveedor y jurisdicción exactos.' },
    search: 'regulation sourcing identity latin binomial characters part processing gmp certificate analysis coa adulteration contamination cites wildlife obsolete substitutions',
    en: {
      rule: 'The label must answer five questions: what species, what part, what processing, what batch and what jurisdiction permits it?',
      sections: [
        { heading: 'Identity chain', rows: [
          ['Name', 'Pinyin plus Chinese characters.', 'Prevents same-sound substitutions such as Dà Jì/Dà Jǐ or Huáng Qín/Huáng Qí.'],
          ['Botanical identity', 'Accepted Latin binomial and pharmacopoeial name.', 'Separates Stephania from Aristolochia and genuine from adulterant species.'],
          ['Medicinal part', 'Leaf, twig, bark, root, rhizome, seed, fruit, shell or animal material.', 'Same plant part can change direction and risk.'],
          ['Processing', 'Raw, prepared, honey-fried, charred, wine-steamed or other specified Páo Zhì.', 'Processing is part of identity, not an optional note.'],
          ['Batch traceability', 'Lot number, manufacturer, dates and certificate of analysis.', 'Enables recall and investigation of contamination/adulteration.']
        ]},
        { heading: 'Quality and legal screen', rows: [
          ['Contaminants', 'Test for heavy metals, pesticides, microbes, mycotoxins and undeclared pharmaceuticals where relevant.', '“Natural” does not establish purity.'],
          ['Obsolete substances', 'Aristolochia drugs and toxic mineral/animal substances may be prohibited or obsolete.', 'Historical formulas require legal, ethical substitutions—not literal copying.'],
          ['Wildlife', 'Musk, rhinoceros horn, tiger bone, bear bile, pangolin scale and some shells/animals raise CITES and wildlife-law concerns.', 'Use lawful accepted substitutes and document sourcing.'],
          ['Jurisdiction', 'Permitted substances, practitioner scope and labeling rules differ by country/state.', 'A textbook listing is not authorization to possess or prescribe.'],
          ['Supplier qualification', 'Use audited professional suppliers with authenticated inventory and recall procedures.', 'Marketplace appearance or pinyin-only labels are inadequate.'],
          ['Patient-facing label', 'Include ingredients, processing, directions, warnings, lot and contact details.', 'Patients need enough information for emergency identification.']
        ]},
        { heading: 'Classic substitutions', rows: [
          ['Xī Jiǎo 犀角', 'Rhinoceros horn is illegal/obsolete.', 'Shuǐ Niú Jiǎo (water-buffalo horn) is the standard lawful substitute where permitted, with formula adjustment.'],
          ['Hǔ Gǔ 虎骨', 'Tiger bone is prohibited wildlife material.', 'Use strategy-based lawful substitutes; never seek “authentic” wildlife product.'],
          ['Shè Xiāng 麝香', 'Natural musk is wildlife-restricted.', 'Use approved synthetic musk or an accepted formula alternative where lawful.'],
          ['Guān Mù Tōng / Guǎng Fáng Jǐ / Qīng Mù Xiāng', 'Aristolochia sources are unsafe.', 'Do not make casual one-for-one substitutions; reformulate from the intended action using accepted species.']
        ]}
      ],
      emergency: 'If a product is mislabeled, contaminated or causes a serious reaction: stop use, preserve the package/batch, obtain medical care and report through the applicable regulator or poison-control system.'
    },
    es: {
      rule: 'La etiqueta debe responder cinco preguntas: especie, parte, procesamiento, lote y jurisdicción que lo permite.',
      sections: [
        { heading: 'Cadena de identidad', rows: [
          ['Nombre', 'Pinyin más caracteres chinos.', 'Evita sustituciones por sonido como Dà Jì/Dà Jǐ o Huáng Qín/Huáng Qí.'],
          ['Identidad botánica', 'Binomio latino aceptado y nombre farmacopeico.', 'Separa Stephania de Aristolochia y especie genuina de adulterante.'],
          ['Parte medicinal', 'Hoja, rama, corteza, raíz, rizoma, semilla, fruto, concha o material animal.', 'La parte de la misma planta cambia dirección y riesgo.'],
          ['Procesamiento', 'Crudo, preparado, con miel, carbonizado, al vapor con vino u otro Páo Zhì.', 'Procesamiento es parte de identidad, no nota opcional.'],
          ['Trazabilidad', 'Lote, fabricante, fechas y certificado de análisis.', 'Permite retirada e investigación de contaminación/adulteración.']
        ]},
        { heading: 'Control de calidad y legal', rows: [
          ['Contaminantes', 'Analizar metales pesados, pesticidas, microbios, micotoxinas y fármacos no declarados cuando proceda.', '“Natural” no demuestra pureza.'],
          ['Sustancias obsoletas', 'Aristolochia y minerales/animales tóxicos pueden estar prohibidos u obsoletos.', 'Fórmulas históricas requieren sustitución legal/ética, no copia literal.'],
          ['Fauna', 'Almizcle, cuerno de rinoceronte, hueso de tigre, bilis de oso, pangolín y algunos animales implican CITES/leyes.', 'Usar sustitutos legales aceptados y documentar procedencia.'],
          ['Jurisdicción', 'Sustancias, alcance profesional y etiquetado cambian por país/estado.', 'Un libro no autoriza poseer o prescribir.'],
          ['Proveedor cualificado', 'Usar proveedores profesionales auditados con inventario autenticado y retiros.', 'Aspecto de mercado o etiqueta solo pinyin no bastan.'],
          ['Etiqueta al paciente', 'Incluir ingredientes, procesamiento, instrucciones, advertencias, lote y contacto.', 'El paciente necesita datos para identificación en emergencia.']
        ]},
        { heading: 'Sustituciones clásicas', rows: [
          ['Xī Jiǎo 犀角', 'Cuerno de rinoceronte es ilegal/obsoleto.', 'Shuǐ Niú Jiǎo es sustituto legal estándar donde se permite, con ajuste de fórmula.'],
          ['Hǔ Gǔ 虎骨', 'Hueso de tigre es fauna prohibida.', 'Usar sustitutos legales por estrategia; nunca buscar producto “auténtico.”'],
          ['Shè Xiāng 麝香', 'Almizcle natural está restringido.', 'Usar almizcle sintético aprobado o alternativa aceptada donde sea legal.'],
          ['Guān Mù Tōng / Guǎng Fáng Jǐ / Qīng Mù Xiāng', 'Fuentes Aristolochia son inseguras.', 'No sustituir uno-a-uno casualmente; reformular acción con especies aceptadas.']
        ]}
      ],
      emergency: 'Si producto está mal etiquetado, contaminado o causa reacción grave: suspender, conservar envase/lote, obtener atención y reportar al regulador o toxicología aplicable.'
    }
  }
];

function esc(value) {
  return String(value).replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;');
}

function nav(es) {
  const suffix = es ? '-es' : '';
  return `<nav class="comparison-collection-links" aria-label="${es ? 'Grupos de estudio' : 'Study groups'}"><a href="index${suffix}.html">${es ? 'Comparaciones de Hierbas' : 'Single Herbs Comparisons'}</a><a href="formulas${suffix}.html">${es ? 'Comparaciones de Fórmulas' : 'Formula Comparisons'}</a><a href="pathologies${suffix}.html">${es ? 'Patologías' : 'Pathology Comparisons'}</a><a href="patterns${suffix}.html">${es ? 'Patrones' : 'Pattern Comparisons'}</a><a href="families${suffix}.html">${es ? 'Familias de Fórmulas' : 'Formula Families'}</a><a href="dui-yao${suffix}.html">${es ? 'Parejas Duì Yào' : 'Duì Yào Pairs'}</a><a href="name-traps${suffix}.html">${es ? 'Trampas de Nombres' : 'Name Traps'}</a><a href="herb-families${suffix}.html">${es ? 'Familias y Partes' : 'Herb Families &amp; Parts'}</a><a aria-current="page" href="safety${suffix}.html">${es ? 'Biblioteca de Seguridad' : 'Safety Library'}</a><a href="formula-name-traps${es ? '-es' : ''}.html">${es ? 'Trampas de Fórmulas' : 'Formula Name Traps'}</a><a href="misleading-formula-names${es ? '-es' : ''}.html">${es ? 'Nombres Engañosos' : 'Misleading Formula Names'}</a><a href="advanced-name-traps${es ? '-es' : ''}.html">${es ? 'Trampas Avanzadas' : 'Advanced Name Traps'}</a><a href="supplemental-herb-families${es ? '-es' : ''}.html">${es ? 'Familias Adicionales' : 'Supplemental Herb Families'}</a></nav>`;
}

function detailPage(topic, language) {
  const es = language === 'es';
  const data = topic[language];
  const index = `safety${es ? '-es' : ''}.html`;
  const alternate = `${topic.slug}${es ? '' : '-es'}.html`;
  const sections = data.sections.map(section => {
    const rows = section.rows.map(row => `<tr><th scope="row">${esc(row[0])}</th><td>${esc(row[1])}</td><td>${esc(row[2])}</td></tr>`).join('');
    return `<section class="study-note__block"><h2 class="study-note__heading study-note__heading--orange">${esc(section.heading)}</h2><table><thead><tr><th>${es ? 'Grupo / paso' : 'Group / step'}</th><th>${es ? 'Qué saber' : 'What to know'}</th><th>${es ? 'Decisión segura' : 'Safe decision'}</th></tr></thead><tbody>${rows}</tbody></table></section>`;
  }).join('');
  return `<!doctype html><html lang="${es ? 'es' : 'en'}"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="description" content="${esc(topic.summary[language])}"><title>${esc(topic.title[language])} | ${es ? 'Biblioteca de Seguridad' : 'Safety Library'} | AcupunctureTests</title><link rel="alternate" hreflang="${es ? 'en' : 'es'}" href="${alternate}"><link rel="stylesheet" href="comparisons.css?v=${cssVersion}"></head><body>
<header class="comparison-topbar"><div class="comparison-shell comparison-topbar__inner" style="flex-wrap:wrap;padding:10px 0"><a class="comparison-brand" href="${index}"><span class="comparison-brand__mark" aria-hidden="true">☯</span><span>${es ? 'Biblioteca de Seguridad' : 'Safety Library'}</span></a><span style="display:flex;gap:8px;flex-wrap:wrap"><a class="comparison-back" href="${alternate}">${es ? 'English' : 'Español'}</a><a class="comparison-back" href="${index}">${es ? '← Volver a Seguridad' : '← Back to Safety'}</a></span></div></header>
<main class="comparison-shell"><article class="study-note"><p class="comparison-eyebrow">${es ? `Seguridad ${topic.number} de 4` : `Safety ${topic.number} of 4`}</p><h1 class="study-note__title">${esc(topic.title[language])}</h1><p>${esc(topic.summary[language])}</p>
<section class="study-note__block"><h2 class="study-note__heading">${es ? 'Regla principal' : 'Core rule'}</h2><table class="study-note__focus"><tbody><tr><th>${es ? 'Regla' : 'Rule'}</th><td>${esc(data.rule)}</td></tr></tbody></table></section>${sections}
<section class="study-note__block"><h2 class="study-note__heading study-note__heading--purple">${es ? 'Cuándo actuar de inmediato' : 'When to act immediately'}</h2><table class="study-note__focus"><tbody><tr><th>${es ? 'Emergencia' : 'Emergency'}</th><td>${esc(data.emergency)}</td></tr></tbody></table></section>
<p><strong>${es ? 'Uso educativo:' : 'Educational use:'}</strong> ${es ? 'esta biblioteca no sustituye evaluación médica, toxicología, normativa local ni consulta farmacológica.' : 'this library does not replace medical evaluation, poison control, local regulation or pharmacist consultation.'}</p>
</article></main><footer class="comparison-footer"><div class="comparison-shell">${es ? 'Grupo 9 · Seguridad enlazada' : 'Group 9 · Linked safety'}</div></footer></body></html>`;
}

function indexPage(language) {
  const es = language === 'es';
  const cards = topics.map(topic => `<article class="comparison-card" data-type="safety" data-sequence="${topic.number}" data-search="${esc(topic.search)}"><span class="comparison-card__type">${es ? `Seguridad ${topic.number}` : `Safety ${topic.number}`}</span><h3>${esc(topic.title[language])}</h3><p>${esc(topic.summary[language])}</p><div class="comparison-card__category"><strong>${es ? 'Regla:' : 'Rule:'}</strong><br>${esc(topic[language].rule)}</div><a class="comparison-card__open" href="${topic.slug}${es ? '-es' : ''}.html">${es ? 'Abrir Seguridad →' : 'Open Safety →'}</a></article>`).join('');
  return `<!doctype html><html lang="${es ? 'es' : 'en'}"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="description" content="${es ? 'Preparación, toxicidad, interacciones y regulación de medicina herbal china.' : 'Preparation, toxicity, interactions and regulation for Chinese herbal medicine.'}"><title>${es ? 'Biblioteca de Seguridad' : 'Safety Library'} | AcupunctureTests</title><link rel="alternate" hreflang="${es ? 'en' : 'es'}" href="safety${es ? '' : '-es'}.html"><link rel="stylesheet" href="comparisons.css?v=${cssVersion}"><script defer src="comparisons.js?v=20261026-3"></script></head><body>
<header class="comparison-topbar"><div class="comparison-shell comparison-topbar__inner"><a class="comparison-brand" href="/my/"><span class="comparison-brand__mark" aria-hidden="true">☯</span><span>AcupunctureTests</span></a><span style="display:flex;gap:8px;flex-wrap:wrap"><a class="comparison-back" href="safety${es ? '' : '-es'}.html">${es ? 'English' : 'Español'}</a><a class="comparison-back" href="/my/">${es ? '← Dashboard' : '← Back to Dashboard'}</a></span></div></header>
<main class="comparison-shell"><section class="comparison-hero"><p class="comparison-eyebrow">${es ? 'Grupo 9 · Seguridad clínica' : 'Group 9 · Clinical safety'}</p><h1>${es ? 'Biblioteca de Seguridad' : 'Safety Library'}</h1><p>${es ? 'Consulta preparación, toxicidad, interacciones y regulación desde una sola biblioteca enlazada con los grupos de estudio.' : 'Check preparation, toxicity, interactions and regulation in one library linked to every study group.'}</p></section>
${nav(es)}
<section class="comparison-tools comparison-tools--search-only"><label><span class="sr-only">${es ? 'Buscar seguridad' : 'Search safety'}</span><input class="comparison-search" type="search" placeholder="${es ? 'Buscar sustancia, riesgo o procedimiento' : 'Search substance, risk or procedure'}" data-comparison-search></label></section><section><header class="comparison-summary"><h2>${es ? 'Secciones disponibles' : 'Available sections'}</h2><p><span data-visible-count>${topics.length}</span> ${es ? 'secciones de seguridad' : 'safety sections'}</p></header><div class="comparison-grid" data-comparison-grid>${cards}</div><p class="comparison-empty" data-comparison-empty hidden>${es ? 'Ninguna sección coincide.' : 'No safety sections match this search.'}</p></section></main><footer class="comparison-footer"><div class="comparison-shell">${es ? 'Preparación · Toxicidad · Interacciones · Regulación' : 'Preparation · Toxicity · Interactions · Regulation'}</div></footer></body></html>`;
}

for (const topic of topics) {
  writeFileSync(resolve(root, `${topic.slug}.html`), detailPage(topic, 'en'));
  writeFileSync(resolve(root, `${topic.slug}-es.html`), detailPage(topic, 'es'));
}
writeFileSync(resolve(root, 'safety.html'), indexPage('en'));
writeFileSync(resolve(root, 'safety-es.html'), indexPage('es'));
console.log(`Generated ${topics.length * 2} Safety pages and 2 indexes`);
