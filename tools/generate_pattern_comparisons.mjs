import { writeFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '../study-guide/comparisons');
const cssVersion = '20261026-2';

const cards = [
  {
    slug: 'liver-qi-stagnation-vs-liver-blood-deficiency',
    title: 'Liver Qi Stagnation vs. Liver Blood Deficiency',
    titleEs: 'Estancamiento de Qi de Hígado vs. Deficiencia de Sangre de Hígado',
    summary: 'Separate an excess failure of free coursing from deficient nourishment of the sinews, eyes and menstrual cycle.',
    summaryEs: 'Distingue una falla excesiva del libre flujo de una nutrición deficiente de tendones, ojos y ciclo menstrual.',
    search: 'liver qi stagnation blood deficiency constraint rib side distention mood menstruation dizziness eyes tendons pale wiry choppy',
    en: {
      names: ['Liver Qi Stagnation', 'Liver Blood Deficiency'],
      shared: 'Both can affect menstruation, mood, eyes, sinews and the rib-side because both involve Liver function.',
      fast: ['Distention and movement: irritability, sighing, rib-side fullness and symptoms that fluctuate with emotion.', 'Failure to nourish: dizziness, blurred vision, numbness, cramps, scanty menses and pale signs.'],
      rows: [
        ['Pathomechanism', 'Liver Qi fails to course freely.', 'Liver Blood is insufficient to nourish and anchor.'],
        ['Pain', 'Distending, wandering or emotionally aggravated.', 'Dull discomfort with cramps, numbness or dryness.'],
        ['Tongue / pulse', 'Tongue may be normal or dusky; pulse wiry.', 'Pale tongue; pulse thin or choppy.'],
        ['Formula examples', 'Chai Hu Shu Gan San; Xiao Yao San when deficiency coexists.', 'Si Wu Tang; Ba Zhen Tang when Qi deficiency coexists.']
      ],
      why: '“Liver” is not enough. Stagnation produces pressure and variability; Blood deficiency produces poor nourishment and pale, dry signs.',
      memory: '<strong>Qi stagnation presses;</strong> <strong>Blood deficiency fails to feed.</strong>',
      safety: 'Severe abdominal pain, jaundice, heavy bleeding or pregnancy-related pain requires medical evaluation.',
      prompts: ['Which pattern produces rib-side distention?', 'Which produces blurred vision and cramps?', 'How do the pulse qualities differ?']
    },
    es: {
      names: ['Estancamiento de Qi de Hígado', 'Deficiencia de Sangre de Hígado'],
      shared: 'Ambos afectan menstruación, ánimo, ojos, tendones y costados porque implican función de Hígado.',
      fast: ['Distensión y movimiento: irritabilidad, suspiros, plenitud costal y síntomas variables con emociones.', 'Falla de nutrición: mareo, visión borrosa, entumecimiento, calambres, menstruación escasa y palidez.'],
      rows: [
        ['Mecanismo', 'Qi de Hígado no circula libremente.', 'Sangre de Hígado no nutre ni ancla.'],
        ['Dolor', 'Distensivo, móvil o agravado emocionalmente.', 'Molestia sorda con calambres, entumecimiento o sequedad.'],
        ['Lengua / pulso', 'Lengua normal u oscura; pulso de cuerda.', 'Lengua pálida; pulso fino o áspero.'],
        ['Ejemplos de fórmulas', 'Chai Hu Shu Gan San; Xiao Yao San si hay deficiencia.', 'Si Wu Tang; Ba Zhen Tang si también falta Qi.']
      ],
      why: 'Estancamiento produce presión y variabilidad; deficiencia de Sangre produce mala nutrición y signos pálidos/secos.',
      memory: '<strong>Qi estancado presiona;</strong> <strong>Sangre deficiente no alimenta.</strong>',
      safety: 'Dolor abdominal grave, ictericia, hemorragia o dolor en embarazo requiere evaluación.',
      prompts: ['¿Cuál causa distensión costal?', '¿Cuál causa visión borrosa y calambres?', '¿Cómo difieren los pulsos?']
    }
  },
  {
    slug: 'kidney-yin-deficiency-vs-kidney-yang-deficiency',
    title: 'Kidney Yin Deficiency vs. Kidney Yang Deficiency', titleEs: 'Deficiencia de Yin de Riñón vs. Deficiencia de Yang de Riñón',
    summary: 'Separate empty Heat and deficient fluids from internal Cold and failed warming or water transformation.',
    summaryEs: 'Distingue Calor vacío y líquidos deficientes de Frío interno y falla de calentamiento o transformación de agua.',
    search: 'kidney yin yang deficiency empty heat cold night sweat tinnitus low back knees urine edema reproductive red pale deep weak rapid slow',
    en: {
      names: ['Kidney Yin Deficiency', 'Kidney Yang Deficiency'],
      shared: 'Both may cause weak low back and knees, tinnitus, reproductive weakness, urinary changes and a deep weak pulse.',
      fast: ['Empty Heat and dryness: night sweating, five-center heat, dry mouth, red peeled tongue.', 'Cold and fluid failure: cold limbs, edema, frequent clear urine, pale swollen tongue.'],
      rows: [
        ['Thermal sign', 'Heat in the afternoon/night.', 'Persistent internal Cold.'],
        ['Fluids', 'Insufficient fluids and Essence.', 'Fluids accumulate because transformation is weak.'],
        ['Tongue / pulse', 'Red, little coat; thin-rapid-deep.', 'Pale, swollen, wet; deep-slow-weak.'],
        ['Formula examples', 'Liu Wei Di Huang Wan; Zuo Gui Wan.', 'Jin Gui Shen Qi Wan; You Gui Wan.']
      ],
      why: 'The shared Kidney symptoms identify the organ; temperature, moisture and water metabolism identify Yin versus Yang.',
      memory: '<strong>Yin deficiency is dry and hot;</strong> <strong>Yang deficiency is wet and cold.</strong>',
      safety: 'Edema, major urinary changes, infertility or persistent night sweats require biomedical assessment as well as pattern differentiation.',
      prompts: ['Which pattern has a red peeled tongue?', 'Which produces edema and clear urine?', 'What signs are shared?']
    },
    es: {
      names: ['Deficiencia de Yin de Riñón', 'Deficiencia de Yang de Riñón'],
      shared: 'Ambas causan lumbar/rodillas débiles, tinnitus, debilidad reproductiva, cambios urinarios y pulso profundo débil.',
      fast: ['Calor vacío y sequedad: sudor nocturno, calor en cinco centros, boca seca y lengua roja pelada.', 'Frío y falla de líquidos: extremidades frías, edema, orina clara frecuente y lengua pálida hinchada.'],
      rows: [
        ['Temperatura', 'Calor por tarde/noche.', 'Frío interno persistente.'],
        ['Líquidos', 'Líquidos y Esencia insuficientes.', 'Líquidos se acumulan por transformación débil.'],
        ['Lengua / pulso', 'Roja, poca capa; fino-rápido-profundo.', 'Pálida, hinchada y húmeda; profundo-lento-débil.'],
        ['Ejemplos de fórmulas', 'Liu Wei Di Huang Wan; Zuo Gui Wan.', 'Jin Gui Shen Qi Wan; You Gui Wan.']
      ],
      why: 'Los síntomas renales identifican órgano; temperatura, humedad y metabolismo de agua identifican Yin o Yang.',
      memory: '<strong>Yin deficiente es seco y caliente;</strong> <strong>Yang deficiente es húmedo y frío.</strong>',
      safety: 'Edema, cambios urinarios importantes, infertilidad o sudor nocturno persistente requieren evaluación biomédica.',
      prompts: ['¿Cuál tiene lengua roja pelada?', '¿Cuál produce edema y orina clara?', '¿Qué signos comparten?']
    }
  },
  {
    slug: 'spleen-qi-deficiency-vs-spleen-yang-deficiency',
    title: 'Spleen Qi Deficiency vs. Spleen Yang Deficiency', titleEs: 'Deficiencia de Qi de Bazo vs. Deficiencia de Yang de Bazo',
    summary: 'Separate weak transformation and transport from the same deficiency progressing to pronounced internal Cold.',
    summaryEs: 'Distingue transporte y transformación débiles de la misma deficiencia progresada a Frío interno marcado.',
    search: 'spleen qi yang deficiency appetite fatigue loose stool cold abdominal pain edema pale swollen teeth marks weak slow',
    en: {
      names: ['Spleen Qi Deficiency', 'Spleen Yang Deficiency'],
      shared: 'Both cause fatigue, poor appetite, loose stools, abdominal weakness, pale tongue and a weak pulse.',
      fast: ['Weak digestion without strong Cold: fatigue and loose stool predominate.', 'Qi deficiency plus Cold: abdominal pain better with warmth/pressure, cold limbs and edema.'],
      rows: [
        ['Progression', 'Earlier or milder functional deficiency.', 'Deeper deficiency with loss of warming function.'],
        ['Cold signs', 'Absent or mild.', 'Pronounced.'],
        ['Tongue / pulse', 'Pale with teeth marks; weak.', 'Pale, swollen and wet; deep-slow-weak.'],
        ['Formula examples', 'Si Jun Zi Tang; Shen Ling Bai Zhu San.', 'Li Zhong Wan; Zhen Wu Tang if water accumulates.']
      ],
      why: 'Spleen-Yang deficiency includes Spleen-Qi deficiency plus Cold and often water retention.',
      memory: '<strong>Qi is weak;</strong> <strong>Yang is weak and cold.</strong>',
      safety: 'Persistent diarrhea, dehydration, weight loss, bleeding or sudden edema needs medical assessment.',
      prompts: ['Which pattern has strong Cold signs?', 'Which is the earlier stage?', 'When does edema become a key clue?']
    },
    es: {
      names: ['Deficiencia de Qi de Bazo', 'Deficiencia de Yang de Bazo'],
      shared: 'Ambas causan fatiga, poco apetito, heces blandas, abdomen débil, lengua pálida y pulso débil.',
      fast: ['Digestión débil sin Frío fuerte: dominan fatiga y heces blandas.', 'Deficiencia de Qi más Frío: dolor mejor con calor/presión, extremidades frías y edema.'],
      rows: [
        ['Progresión', 'Deficiencia funcional temprana o leve.', 'Deficiencia profunda con pérdida de función calentadora.'],
        ['Frío', 'Ausente o leve.', 'Marcado.'],
        ['Lengua / pulso', 'Pálida con marcas dentales; débil.', 'Pálida, hinchada y húmeda; profundo-lento-débil.'],
        ['Ejemplos de fórmulas', 'Si Jun Zi Tang; Shen Ling Bai Zhu San.', 'Li Zhong Wan; Zhen Wu Tang si acumula agua.']
      ],
      why: 'Deficiencia de Yang incluye deficiencia de Qi más Frío y a menudo retención de agua.',
      memory: '<strong>Qi está débil;</strong> <strong>Yang está débil y frío.</strong>',
      safety: 'Diarrea persistente, deshidratación, pérdida de peso, sangrado o edema súbito necesita evaluación.',
      prompts: ['¿Cuál tiene Frío fuerte?', '¿Cuál es etapa temprana?', '¿Cuándo el edema es clave?']
    }
  },
  {
    slug: 'lung-qi-deficiency-vs-lung-yin-deficiency',
    title: 'Lung Qi Deficiency vs. Lung Yin Deficiency', titleEs: 'Deficiencia de Qi de Pulmón vs. Deficiencia de Yin de Pulmón',
    summary: 'Separate weak respiratory function and Defensive Qi from dry Lung tissue with empty Heat.',
    summaryEs: 'Distingue función respiratoria y Qi Defensivo débiles de tejido pulmonar seco con Calor vacío.',
    search: 'lung qi yin deficiency weak cough short breath voice sweat recurrent colds dry cough scant sputum throat night sweat',
    en: {
      names: ['Lung Qi Deficiency', 'Lung Yin Deficiency'],
      shared: 'Both may cause chronic cough, shortness of breath, fatigue and a weak pulse.',
      fast: ['Weak function: low voice, spontaneous sweating and recurrent colds.', 'Dryness and empty Heat: dry cough, scant sticky sputum, dry throat and night sweating.'],
      rows: [
        ['Core failure', 'Lung cannot govern Qi or secure the Exterior.', 'Lung lacks moistening and cooling Yin.'],
        ['Cough', 'Weak, low and worse with exertion.', 'Dry, hacking, with scant sputum.'],
        ['Tongue / pulse', 'Pale; weak, especially right cun.', 'Red and peeled; thin-rapid.'],
        ['Formula examples', 'Yu Ping Feng San for Defensive Qi; Bu Fei Tang.', 'Sha Shen Mai Men Dong Tang; Bai He Gu Jin Tang.']
      ],
      why: 'Both are deficient coughs. Sweat timing, voice strength and dryness separate functional Qi weakness from tissue-fluid deficiency.',
      memory: '<strong>Qi cannot push or protect;</strong> <strong>Yin cannot moisten.</strong>',
      safety: 'Cough over three weeks, coughing blood, chest pain, low oxygen or weight loss requires medical evaluation.',
      prompts: ['Which causes spontaneous sweating?', 'Which causes dry cough and night sweating?', 'How does the tongue differ?']
    },
    es: {
      names: ['Deficiencia de Qi de Pulmón', 'Deficiencia de Yin de Pulmón'],
      shared: 'Ambas causan tos crónica, falta de aire, fatiga y pulso débil.',
      fast: ['Función débil: voz baja, sudor espontáneo y resfriados recurrentes.', 'Sequedad y Calor vacío: tos seca, esputo escaso pegajoso, garganta seca y sudor nocturno.'],
      rows: [
        ['Falla central', 'Pulmón no gobierna Qi ni asegura Exterior.', 'Pulmón carece de Yin humectante y refrescante.'],
        ['Tos', 'Débil, baja y peor con esfuerzo.', 'Seca, irritativa, con poco esputo.'],
        ['Lengua / pulso', 'Pálida; débil, especialmente cun derecho.', 'Roja y pelada; fino-rápido.'],
        ['Ejemplos de fórmulas', 'Yu Ping Feng San para Qi Defensivo; Bu Fei Tang.', 'Sha Shen Mai Men Dong Tang; Bai He Gu Jin Tang.']
      ],
      why: 'Ambas son tos deficientes; momento del sudor, fuerza vocal y sequedad separan Qi funcional de líquidos tisulares.',
      memory: '<strong>Qi no empuja ni protege;</strong> <strong>Yin no humecta.</strong>',
      safety: 'Tos mayor de tres semanas, sangre, dolor torácico, oxígeno bajo o pérdida de peso requiere evaluación.',
      prompts: ['¿Cuál causa sudor espontáneo?', '¿Cuál tos seca y sudor nocturno?', '¿Cómo difiere lengua?']
    }
  },
  {
    slug: 'heart-blood-deficiency-vs-heart-yin-deficiency',
    title: 'Heart Blood Deficiency vs. Heart Yin Deficiency', titleEs: 'Deficiencia de Sangre de Corazón vs. Deficiencia de Yin de Corazón',
    summary: 'Separate pale undernourishment of the Spirit from deeper fluid deficiency with empty Heat.',
    summaryEs: 'Distingue desnutrición pálida del Shen de una deficiencia más profunda de líquidos con Calor vacío.',
    search: 'heart blood yin deficiency palpitations insomnia memory anxiety pale red night sweat dry mouth empty heat',
    en: {
      names: ['Heart Blood Deficiency', 'Heart Yin Deficiency'],
      shared: 'Both fail to nourish the Heart and Spirit, causing palpitations, insomnia, anxiety, poor memory and a thin pulse.',
      fast: ['Pale deficiency without Heat: dizziness, pale lips/tongue and scanty menses may coexist.', 'Blood deficiency plus dryness and empty Heat: night sweat, dry mouth, five-center heat and red peeled tongue.'],
      rows: [
        ['Depth', 'Blood nourishment is insufficient.', 'Yin fluids are deficient, often including Blood deficiency.'],
        ['Thermal signs', 'No significant Heat.', 'Empty Heat, especially at night.'],
        ['Tongue / pulse', 'Pale; thin or choppy.', 'Red, little coat; thin-rapid.'],
        ['Formula examples', 'Gui Pi Tang; Si Wu Tang when appropriate.', 'Tian Wang Bu Xin Dan.']
      ],
      why: 'Heart-Yin deficiency resembles Heart-Blood deficiency with added dryness and Heat.',
      memory: '<strong>Blood deficiency is pale;</strong> <strong>Yin deficiency is red and hot.</strong>',
      safety: 'Chest pain, fainting, severe or irregular palpitations requires biomedical assessment.',
      prompts: ['Which has a pale tongue?', 'Which has night sweating and empty Heat?', 'What Spirit symptoms are shared?']
    },
    es: {
      names: ['Deficiencia de Sangre de Corazón', 'Deficiencia de Yin de Corazón'],
      shared: 'Ambas no nutren Corazón y Shen y causan palpitaciones, insomnio, ansiedad, mala memoria y pulso fino.',
      fast: ['Deficiencia pálida sin Calor: mareo, labios/lengua pálidos y menstruación escasa.', 'Deficiencia con sequedad y Calor vacío: sudor nocturno, boca seca, calor en cinco centros y lengua roja pelada.'],
      rows: [
        ['Profundidad', 'Nutrición de Sangre insuficiente.', 'Líquidos Yin deficientes, a menudo con Sangre deficiente.'],
        ['Calor', 'Sin Calor importante.', 'Calor vacío, especialmente nocturno.'],
        ['Lengua / pulso', 'Pálida; fino o áspero.', 'Roja, poca capa; fino-rápido.'],
        ['Ejemplos de fórmulas', 'Gui Pi Tang; Si Wu Tang según caso.', 'Tian Wang Bu Xin Dan.']
      ],
      why: 'Deficiencia de Yin cardíaco parece deficiencia de Sangre con sequedad y Calor añadidos.',
      memory: '<strong>Sangre deficiente es pálida;</strong> <strong>Yin deficiente es rojo y caliente.</strong>',
      safety: 'Dolor torácico, desmayo o palpitaciones graves/irregulares requiere evaluación biomédica.',
      prompts: ['¿Cuál tiene lengua pálida?', '¿Cuál sudor nocturno y Calor vacío?', '¿Qué síntomas de Shen comparten?']
    }
  },
  {
    slug: 'damp-cold-vs-damp-heat', title: 'Damp-Cold vs. Damp-Heat', titleEs: 'Humedad-Frío vs. Humedad-Calor',
    summary: 'Separate heavy, cloudy and cold Dampness from heavy, sticky Dampness combined with Heat and inflammation.',
    summaryEs: 'Distingue Humedad pesada, turbia y fría de Humedad pegajosa combinada con Calor e inflamación.',
    search: 'damp cold heat heaviness sticky greasy coat white yellow thirst urine stool odor spleen bladder liver gallbladder',
    en: {
      names: ['Damp-Cold', 'Damp-Heat'],
      shared: 'Both cause heaviness, fullness, sticky symptoms, a greasy tongue coating and a slippery pulse.',
      fast: ['Cold, pale and clear: no thirst, white greasy coat, loose stool and clear discharges.', 'Hot, red and foul: thirst, yellow greasy coat, dark urine and foul-smelling discharges.'],
      rows: [
        ['Color / odor', 'Pale, white, clear and mild odor.', 'Yellow, dark, red and strong odor.'],
        ['Temperature', 'Cold limbs or preference for warmth.', 'Fever, burning or aversion to heat.'],
        ['Tongue / pulse', 'Pale with white greasy coat; slippery-slow.', 'Red with yellow greasy coat; slippery-rapid.'],
        ['Formula examples', 'Ping Wei San; Huo Xiang Zheng Qi San.', 'Ba Zheng San; Long Dan Xie Gan Tang according to location.']
      ],
      why: 'Dampness supplies heaviness and stickiness; the attached temperature supplies the decisive color, odor and tongue clues.',
      memory: '<strong>Cold Damp is white and clear;</strong> <strong>Damp-Heat is yellow and foul.</strong>',
      safety: 'Fever with flank pain, jaundice, inability to urinate, bloody diarrhea or severe dehydration requires urgent evaluation.',
      prompts: ['Which has a white greasy coat?', 'Which has dark urine and foul odor?', 'What symptoms come from Dampness itself?']
    },
    es: {
      names: ['Humedad-Frío', 'Humedad-Calor'],
      shared: 'Ambas causan pesadez, plenitud, síntomas pegajosos, capa grasosa y pulso resbaladizo.',
      fast: ['Frío, pálido y claro: sin sed, capa blanca grasosa, heces blandas y secreciones claras.', 'Caliente, rojo y fétido: sed, capa amarilla grasosa, orina oscura y secreciones malolientes.'],
      rows: [
        ['Color / olor', 'Pálido, blanco, claro y olor leve.', 'Amarillo, oscuro, rojo y olor fuerte.'],
        ['Temperatura', 'Extremidades frías o preferencia por calor.', 'Fiebre, ardor o aversión al calor.'],
        ['Lengua / pulso', 'Pálida con capa blanca grasosa; resbaladizo-lento.', 'Roja con capa amarilla grasosa; resbaladizo-rápido.'],
        ['Ejemplos de fórmulas', 'Ping Wei San; Huo Xiang Zheng Qi San.', 'Ba Zheng San; Long Dan Xie Gan Tang según localización.']
      ],
      why: 'Humedad aporta pesadez y pegajosidad; la temperatura aporta color, olor y lengua decisivos.',
      memory: '<strong>Humedad-Frío es blanca y clara;</strong> <strong>Humedad-Calor es amarilla y fétida.</strong>',
      safety: 'Fiebre con dolor lumbar, ictericia, anuria, diarrea sanguinolenta o deshidratación exige urgencia.',
      prompts: ['¿Cuál tiene capa blanca grasosa?', '¿Cuál orina oscura y olor fuerte?', '¿Qué signos vienen de Humedad?']
    }
  },
  {
    slug: 'cold-phlegm-vs-phlegm-heat', title: 'Cold-Phlegm vs. Phlegm-Heat', titleEs: 'Flema-Frío vs. Flema-Calor',
    summary: 'Separate copious thin white sputum and Cold from thick yellow sputum and Heat.',
    summaryEs: 'Distingue esputo blanco fino y abundante con Frío de esputo amarillo espeso con Calor.',
    search: 'cold phlegm heat sputum white thin copious yellow thick sticky cough wheeze greasy coat slippery slow rapid',
    en: {
      names: ['Cold-Phlegm', 'Phlegm-Heat'],
      shared: 'Both obstruct Lung Qi and may cause cough, wheezing, chest fullness, nausea, a greasy coating and slippery pulse.',
      fast: ['Copious, thin, watery white sputum; cold limbs and white greasy coat.', 'Thick, sticky yellow sputum; thirst, irritability and yellow greasy coat.'],
      rows: [
        ['Sputum', 'White, thin, watery and easy to expectorate.', 'Yellow, thick, sticky and difficult to expectorate.'],
        ['Temperature', 'Cold signs predominate.', 'Heat and inflammation predominate.'],
        ['Tongue / pulse', 'Pale with white greasy coat; slippery-slow.', 'Red with yellow greasy coat; slippery-rapid.'],
        ['Formula examples', 'Er Chen Tang; Xiao Qing Long Tang when Exterior Cold is present.', 'Wen Dan Tang by organ pattern; Ding Chuan Tang for wheezing.']
      ],
      why: 'Sputum color and texture are high-yield, but must be confirmed by the tongue, pulse and whole thermal pattern.',
      memory: '<strong>Cold makes white water;</strong> <strong>Heat cooks it yellow and thick.</strong>',
      safety: 'Breathing difficulty, low oxygen, high fever, chest pain or coughing blood requires urgent medical care.',
      prompts: ['Which produces watery white sputum?', 'Which produces sticky yellow sputum?', 'What tongue coats confirm each?']
    },
    es: {
      names: ['Flema-Frío', 'Flema-Calor'],
      shared: 'Ambas obstruyen Qi de Pulmón y causan tos, sibilancia, plenitud torácica, náusea, capa grasosa y pulso resbaladizo.',
      fast: ['Esputo blanco, fino, acuoso y abundante; extremidades frías y capa blanca grasosa.', 'Esputo amarillo, espeso y pegajoso; sed, irritabilidad y capa amarilla grasosa.'],
      rows: [
        ['Esputo', 'Blanco, fino, acuoso y fácil de expulsar.', 'Amarillo, espeso, pegajoso y difícil de expulsar.'],
        ['Temperatura', 'Predomina Frío.', 'Predominan Calor e inflamación.'],
        ['Lengua / pulso', 'Pálida con capa blanca grasosa; resbaladizo-lento.', 'Roja con capa amarilla grasosa; resbaladizo-rápido.'],
        ['Ejemplos de fórmulas', 'Er Chen Tang; Xiao Qing Long Tang si hay Frío Exterior.', 'Wen Dan Tang según órgano; Ding Chuan Tang para sibilancia.']
      ],
      why: 'Color y textura son claves, pero se confirman con lengua, pulso y patrón térmico completo.',
      memory: '<strong>Frío hace agua blanca;</strong> <strong>Calor la cocina amarilla y espesa.</strong>',
      safety: 'Disnea, oxígeno bajo, fiebre alta, dolor torácico o hemoptisis requiere atención urgente.',
      prompts: ['¿Cuál produce esputo blanco acuoso?', '¿Cuál amarillo pegajoso?', '¿Qué capas confirman?']
    }
  },
  {
    slug: 'liver-yang-rising-vs-liver-fire-vs-liver-wind',
    title: 'Liver Yang Rising vs. Liver Fire vs. Liver Wind', titleEs: 'Ascenso de Yang de Hígado vs. Fuego de Hígado vs. Viento de Hígado',
    summary: 'Grade upward Liver pathology from deficiency-rooted ascendance to blazing excess Fire and movement-producing internal Wind.',
    summaryEs: 'Gradúa patología ascendente de Hígado desde ascenso con raíz deficiente hasta Fuego excesivo y Viento interno con movimiento.',
    search: 'liver yang rising fire wind headache dizziness tinnitus anger red face tremor convulsion stroke yin deficiency excess',
    en: {
      names: ['Liver Yang Rising', 'Liver Fire Blazing', 'Liver Wind'],
      shared: 'All can rise to the head and cause headache, dizziness, tinnitus, red face or irritability.',
      fast: ['Deficiency-rooted ascendance: dizziness and headache with weak low back or Yin-deficiency signs.', 'Full excess Heat: intense anger, bitter taste, red eyes, constipation and forceful rapid pulse.', 'Movement and neurologic signs: tremor, spasm, convulsion, numbness or sudden loss of control.'],
      rows: [
        ['Mechanism', 'Liver/Kidney Yin fails to anchor Yang.', 'Excess Liver Heat flares upward.', 'Extreme Yang/Fire or deficiency generates internal movement.'],
        ['Defining clue', 'Upward symptoms plus deficiency below.', 'Strong excess Heat signs.', 'Involuntary movement or acute neurologic change.'],
        ['Tongue / pulse', 'Red sides; wiry, often thin.', 'Red with yellow coat; wiry-forceful-rapid.', 'Varies by cause; wiry, rapid or deficient.'],
        ['Formula examples', 'Tian Ma Gou Teng Yin.', 'Long Dan Xie Gan Tang by full pattern.', 'Zhen Gan Xi Feng Tang for severe ascendance; emergency assessment first.']
      ],
      why: 'These are related but not interchangeable. Wind is identified by movement; Fire by full Heat; Yang rising by a deficient root.',
      memory: '<strong>Yang rises;</strong> <strong>Fire burns;</strong> <strong>Wind moves.</strong>',
      safety: 'Sudden severe headache, facial droop, weakness, slurred speech, seizure or altered consciousness is an emergency.',
      prompts: ['Which pattern has a deficient root?', 'Which has full Heat?', 'Which is defined by involuntary movement?']
    },
    es: {
      names: ['Ascenso de Yang de Hígado', 'Fuego de Hígado', 'Viento de Hígado'],
      shared: 'Todos ascienden a cabeza y causan cefalea, mareo, tinnitus, cara roja o irritabilidad.',
      fast: ['Ascenso con raíz deficiente: mareo/cefalea con lumbar débil o signos de Yin deficiente.', 'Calor excesivo pleno: ira intensa, sabor amargo, ojos rojos, estreñimiento y pulso fuerte-rápido.', 'Movimiento neurológico: temblor, espasmo, convulsión, entumecimiento o pérdida súbita de control.'],
      rows: [
        ['Mecanismo', 'Yin Hígado/Riñón no ancla Yang.', 'Calor excesivo de Hígado flamea arriba.', 'Yang/Fuego extremo o deficiencia genera movimiento.'],
        ['Pista', 'Síntomas arriba más deficiencia abajo.', 'Signos fuertes de Calor pleno.', 'Movimiento involuntario o cambio neurológico agudo.'],
        ['Lengua / pulso', 'Bordes rojos; cuerda, a menudo fino.', 'Roja con capa amarilla; cuerda-fuerte-rápido.', 'Varía; cuerda, rápido o deficiente.'],
        ['Ejemplos de fórmulas', 'Tian Ma Gou Teng Yin.', 'Long Dan Xie Gan Tang según patrón.', 'Zhen Gan Xi Feng Tang para ascenso grave; primero descartar emergencia.']
      ],
      why: 'Viento se identifica por movimiento; Fuego por Calor pleno; Yang ascendente por raíz deficiente.',
      memory: '<strong>Yang asciende;</strong> <strong>Fuego quema;</strong> <strong>Viento mueve.</strong>',
      safety: 'Cefalea súbita grave, caída facial, debilidad, habla alterada, convulsión o confusión es emergencia.',
      prompts: ['¿Cuál tiene raíz deficiente?', '¿Cuál tiene Calor pleno?', '¿Cuál se define por movimiento involuntario?']
    }
  },
  {
    slug: 'blood-deficiency-vs-blood-stasis', title: 'Blood Deficiency vs. Blood Stasis', titleEs: 'Deficiencia de Sangre vs. Estasis de Sangre',
    summary: 'Separate insufficient Blood nourishment from obstructed Blood movement.',
    summaryEs: 'Distingue nutrición insuficiente de Sangre de movimiento sanguíneo obstruido.',
    search: 'blood deficiency stasis pale dizziness blurred vision cramps scant menses fixed stabbing pain clots purple choppy wiry',
    en: {
      names: ['Blood Deficiency', 'Blood Stasis'],
      shared: 'Both can affect menstruation, cause pain and produce a choppy pulse, and deficiency may eventually contribute to stasis.',
      fast: ['Not enough Blood: pallor, dizziness, blurred vision, numbness, cramps and scanty flow.', 'Blood does not move: fixed stabbing pain, dark clots, masses and purple discoloration.'],
      rows: [
        ['Primary problem', 'Quantity and nourishment.', 'Circulation and obstruction.'],
        ['Pain', 'Dull, cramping or associated with weakness.', 'Fixed, sharp, stabbing and often worse at night.'],
        ['Tongue / pulse', 'Pale; thin or choppy.', 'Purple/dusky with spots; wiry or choppy.'],
        ['Formula examples', 'Si Wu Tang; Ba Zhen Tang.', 'Xue Fu Zhu Yu Tang; Tao He Cheng Qi Tang by location/pattern.']
      ],
      why: 'Both involve Blood, but deficiency lacks substance while stasis lacks movement.',
      memory: '<strong>Deficiency is pale and empty;</strong> <strong>stasis is purple and fixed.</strong>',
      safety: 'Sudden severe pain, chest pain, one-sided swelling, pregnancy bleeding or heavy bleeding requires urgent evaluation.',
      prompts: ['Which causes fixed stabbing pain?', 'Which causes pallor and blurred vision?', 'How do tongue colors differ?']
    },
    es: {
      names: ['Deficiencia de Sangre', 'Estasis de Sangre'],
      shared: 'Ambas afectan menstruación, causan dolor y pueden producir pulso áspero; deficiencia puede contribuir a estasis.',
      fast: ['No hay suficiente Sangre: palidez, mareo, visión borrosa, entumecimiento, calambres y flujo escaso.', 'Sangre no se mueve: dolor fijo punzante, coágulos oscuros, masas y color púrpura.'],
      rows: [
        ['Problema', 'Cantidad y nutrición.', 'Circulación y obstrucción.'],
        ['Dolor', 'Sordo, cólico o asociado con debilidad.', 'Fijo, agudo, punzante y peor de noche.'],
        ['Lengua / pulso', 'Pálida; fino o áspero.', 'Púrpura/oscura con puntos; cuerda o áspero.'],
        ['Ejemplos de fórmulas', 'Si Wu Tang; Ba Zhen Tang.', 'Xue Fu Zhu Yu Tang; Tao He Cheng Qi Tang según patrón.']
      ],
      why: 'Deficiencia carece de sustancia; estasis carece de movimiento.',
      memory: '<strong>Deficiencia es pálida y vacía;</strong> <strong>estasis es púrpura y fija.</strong>',
      safety: 'Dolor súbito grave, dolor torácico, hinchazón unilateral o sangrado fuerte/del embarazo exige urgencia.',
      prompts: ['¿Cuál causa dolor fijo punzante?', '¿Cuál palidez y visión borrosa?', '¿Cómo difieren colores de lengua?']
    }
  },
  {
    slug: 'exterior-wind-cold-excess-vs-deficiency',
    title: 'Exterior Wind-Cold Excess vs. Exterior Wind-Cold Deficiency', titleEs: 'Viento-Frío Exterior por Exceso vs. por Deficiencia',
    summary: 'Separate a tightly closed Exterior with no sweating from Ying-Wei disharmony with spontaneous sweating.',
    summaryEs: 'Distingue Exterior firmemente cerrado sin sudor de desarmonía Ying-Wei con sudor espontáneo.',
    search: 'exterior wind cold excess deficiency no sweat spontaneous sweating chills aversion wind floating tight moderate ma huang gui zhi',
    en: {
      names: ['Exterior Wind-Cold Excess', 'Exterior Wind-Cold Deficiency'],
      shared: 'Both present acutely with fever, aversion to cold, headache, body aches and a floating pulse.',
      fast: ['Closed pores: no sweating, stronger chills, wheezing and a floating-tight pulse.', 'Disharmonized pores: spontaneous sweating, aversion to wind and a floating-moderate or frail pulse.'],
      rows: [
        ['Exterior dynamic', 'Strong pathogen tightly constrains the surface.', 'Defensive Qi cannot regulate the surface and Ying-Wei are disharmonized.'],
        ['Sweat', 'Absent.', 'Present spontaneously.'],
        ['Pulse', 'Floating and tight.', 'Floating and moderate/frail.'],
        ['Formula examples', 'Ma Huang Tang.', 'Gui Zhi Tang.']
      ],
      why: 'The word “deficiency” describes the state of the Exterior, not necessarily a chronic constitutional deficiency.',
      memory: '<strong>Excess closes;</strong> <strong>deficiency leaks.</strong>',
      safety: 'Difficulty breathing, chest pain, high fever, confusion or severe dehydration requires medical care. Ma Huang formulas require cardiovascular screening.',
      prompts: ['Which pattern has no sweating?', 'Which has spontaneous sweating?', 'What pulse accompanies each?']
    },
    es: {
      names: ['Viento-Frío Exterior por Exceso', 'Viento-Frío Exterior por Deficiencia'],
      shared: 'Ambos aparecen agudamente con fiebre, aversión al frío, cefalea, dolor corporal y pulso flotante.',
      fast: ['Poros cerrados: sin sudor, escalofríos fuertes, sibilancia y pulso flotante-tenso.', 'Poros desarmonizados: sudor espontáneo, aversión al viento y pulso flotante-moderado o débil.'],
      rows: [
        ['Dinámica Exterior', 'Patógeno fuerte constriñe superficie.', 'Qi Defensivo no regula superficie y Ying-Wei se desarmonizan.'],
        ['Sudor', 'Ausente.', 'Presente espontáneamente.'],
        ['Pulso', 'Flotante y tenso.', 'Flotante y moderado/débil.'],
        ['Ejemplos de fórmulas', 'Ma Huang Tang.', 'Gui Zhi Tang.']
      ],
      why: '“Deficiencia” describe estado del Exterior, no necesariamente una deficiencia constitucional crónica.',
      memory: '<strong>Exceso cierra;</strong> <strong>deficiencia fuga.</strong>',
      safety: 'Disnea, dolor torácico, fiebre alta, confusión o deshidratación requiere atención. Ma Huang exige evaluación cardiovascular.',
      prompts: ['¿Cuál no suda?', '¿Cuál tiene sudor espontáneo?', '¿Qué pulso acompaña cada uno?']
    }
  }
];

function esc(value) {
  return String(value).replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;');
}

function cardHtml(card, language) {
  const d = card[language];
  const es = language === 'es';
  const title = es ? card.titleEs : card.title;
  const alternate = `${card.slug}${es ? '' : '-es'}.html`;
  const index = `patterns${es ? '-es' : ''}.html`;
  const cells = d.names.map(name => `<th scope="col">${esc(name)}</th>`).join('');
  const fast = d.fast.map(value => `<td>${value}</td>`).join('');
  const rows = d.rows.map(row => `<tr><th scope="row">${esc(row[0])}</th>${row.slice(1).map(value => `<td>${value}</td>`).join('')}</tr>`).join('');
  const prompts = d.prompts.map(value => `<li>${esc(value)}</li>`).join('');
  return `<!doctype html><html lang="${es ? 'es' : 'en'}"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="description" content="${esc(title)} — ${es ? 'comparación de patrones' : 'pattern comparison'}."><title>${esc(title)} | AcupunctureTests</title><link rel="alternate" hreflang="${es ? 'en' : 'es'}" href="${alternate}"><link rel="stylesheet" href="comparisons.css?v=${cssVersion}"></head><body>
<header class="comparison-topbar"><div class="comparison-shell comparison-topbar__inner" style="flex-wrap:wrap;padding:10px 0"><a class="comparison-brand" href="${index}"><span class="comparison-brand__mark" aria-hidden="true">☯</span><span>${es ? 'Comparaciones de Patrones' : 'Pattern Comparisons'}</span></a><span style="display:flex;flex-wrap:wrap;gap:8px"><a class="comparison-back" href="${alternate}">${es ? 'English' : 'Español'}</a><a class="comparison-back" href="${index}">${es ? '← Volver a Patrones' : '← Back to Patterns'}</a></span></div></header>
<main class="comparison-shell"><article class="study-note"><p class="comparison-eyebrow">${es ? 'Comparación de patrones' : 'Pattern comparison'}</p><h1 class="study-note__title">${esc(title)}</h1>
<section class="study-note__block"><h2 class="study-note__heading">${es ? 'Consejo de estudio · Pistas para responder' : 'Study tip · Clues to help answer'}</h2><table><thead><tr><th>${es ? 'Pista' : 'Clue'}</th>${cells}</tr></thead><tbody><tr class="shared-row"><th>${es ? 'Pista compartida' : 'Shared clue'}</th><td colspan="${d.names.length}">${d.shared}</td></tr><tr><th>${es ? 'Diferencia rápida' : 'Fast distinction'}</th>${fast}</tr></tbody></table></section>
<section class="study-note__block"><h2 class="study-note__heading study-note__heading--orange">${es ? 'Clave comparativa' : 'Comparative answer key'}</h2><table><thead><tr><th>${es ? 'Comparar' : 'Compare'}</th>${cells}</tr></thead><tbody>${rows}</tbody></table></section>
<section class="study-note__block"><h2 class="study-note__heading study-note__heading--purple">${es ? 'Guía de estudio · Enfoque del examen' : 'Study guide · Exam focus'}</h2><table class="study-note__focus"><tbody><tr><th>${es ? 'Por qué importa' : 'Why it matters'}</th><td>${d.why}</td></tr><tr><th>${es ? 'Regla mnemotécnica' : 'Memory cue'}</th><td>${d.memory}</td></tr><tr><th>${es ? 'Seguridad' : 'Safety'}</th><td>${d.safety}</td></tr></tbody></table></section>
<section class="study-note__block"><h2 class="study-note__heading study-note__heading--green">${es ? 'Posibles preguntas de examen · Repaso' : 'Possible exam questions · Study prompts'}</h2><ul class="study-note__prompts">${prompts}</ul></section></article></main><footer class="comparison-footer"><div class="comparison-shell">${es ? 'Comparación independiente de patrones · Material educativo, no diagnóstico médico.' : 'Independent Pattern Comparison · Educational material, not medical diagnosis.'}</div></footer></body></html>`;
}

function indexHtml(language) {
  const es = language === 'es';
  const cardsHtml = cards.map(card => `<article class="comparison-card" data-type="patterns" data-search="${esc(card.search)}"><span class="comparison-card__type">${es ? 'Patrones' : 'Patterns'}</span><h3>${esc(es ? card.titleEs : card.title)}</h3><p>${esc(es ? card.summaryEs : card.summary)}</p><div class="comparison-card__category"><strong>${es ? 'Enfoque:' : 'Focus:'}</strong><br>${es ? 'Diferenciación de patrones de medicina china' : 'Chinese medicine pattern differentiation'}</div><a class="comparison-card__open" href="${card.slug}${es ? '-es' : ''}.html">${es ? 'Abrir Nota de Estudio →' : 'Open Study Note →'}</a></article>`).join('');
  return `<!doctype html><html lang="${es ? 'es' : 'en'}"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="description" content="${es ? 'Comparaciones de patrones diagnósticos de medicina china.' : 'Chinese medicine diagnostic pattern comparisons.'}"><title>${es ? 'Comparaciones de Patrones' : 'Pattern Comparisons'} | AcupunctureTests</title><link rel="alternate" hreflang="${es ? 'en' : 'es'}" href="patterns${es ? '' : '-es'}.html"><link rel="stylesheet" href="comparisons.css?v=${cssVersion}"><script defer src="comparisons.js?v=20261025-1"></script></head><body>
<header class="comparison-topbar"><div class="comparison-shell comparison-topbar__inner"><a class="comparison-brand" href="/my/"><span class="comparison-brand__mark" aria-hidden="true">☯</span><span>AcupunctureTests</span></a><span style="display:flex;gap:8px;flex-wrap:wrap"><a class="comparison-back" href="patterns${es ? '' : '-es'}.html">${es ? 'English' : 'Español'}</a><a class="comparison-back" href="/my/">${es ? '← Dashboard' : '← Back to Dashboard'}</a></span></div></header>
<main class="comparison-shell"><section class="comparison-hero"><p class="comparison-eyebrow">${es ? 'Patrones de medicina china' : 'Chinese Medicine Patterns'}</p><h1>${es ? 'Comparaciones de Patrones' : 'Pattern Comparisons'}</h1><p>${es ? 'Compara diagnósticos de patrones por mecanismo, temperatura, lengua, pulso, signos decisivos y fórmulas representativas.' : 'Compare diagnostic patterns by mechanism, temperature, tongue, pulse, decisive signs and representative formulas.'}</p></section>
<nav class="comparison-collection-links" aria-label="${es ? 'Grupos de estudio' : 'Study groups'}"><a href="index.html">${es ? 'Comparaciones de Hierbas' : 'Single Herbs Comparisons'}</a><a href="formulas${es ? '-es' : ''}.html">${es ? 'Comparaciones de Fórmulas' : 'Formula Comparisons'}</a><a href="pathologies.html">${es ? 'Comparaciones de Patologías' : 'Pathology Comparisons'}</a><a aria-current="page" href="patterns${es ? '-es' : ''}.html">${es ? 'Comparaciones de Patrones' : 'Pattern Comparisons'}</a></nav>
<section class="comparison-tools comparison-tools--search-only"><label><span class="sr-only">${es ? 'Buscar patrones' : 'Search patterns'}</span><input class="comparison-search" type="search" placeholder="${es ? 'Buscar patrones o signos diagnósticos' : 'Search patterns or diagnostic signs'}" data-comparison-search></label></section>
<section><header class="comparison-summary"><h2>${es ? 'Notas disponibles' : 'Available notes'}</h2><p><span data-visible-count>${cards.length}</span> ${es ? 'comparaciones de patrones' : 'pattern comparisons'}</p></header><div class="comparison-grid" data-comparison-grid>${cardsHtml}</div><p class="comparison-empty" data-comparison-empty hidden>${es ? 'Ningún patrón coincide con la búsqueda.' : 'No Pattern Comparisons match this search.'}</p></section></main><footer class="comparison-footer"><div class="comparison-shell">${es ? 'Patrones de medicina china separados de diagnósticos biomédicos y síntomas.' : 'Chinese medicine patterns kept separate from biomedical diagnoses and symptom-based pathologies.'}</div></footer></body></html>`;
}

for (const card of cards) {
  writeFileSync(resolve(root, `${card.slug}.html`), cardHtml(card, 'en'));
  writeFileSync(resolve(root, `${card.slug}-es.html`), cardHtml(card, 'es'));
}
writeFileSync(resolve(root, 'patterns.html'), indexHtml('en'));
writeFileSync(resolve(root, 'patterns-es.html'), indexHtml('es'));
console.log(`Generated ${cards.length * 2} pattern cards and 2 pattern indexes in ${root}`);
