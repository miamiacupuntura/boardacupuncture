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
      fast: ['Cold, pale and clear: no thirst, white greasy coat, loose stool and clear discharges.', 'Hot, red and foul: thirst, yellow greasy coat, dark urine; may cause dysentery, painful urination, jaundice, furuncles or eczema.'],
      rows: [
        ['Color / odor', 'Pale, white, clear and mild odor.', 'Yellow, dark, red and strong odor.'],
        ['Temperature', 'Cold limbs or preference for warmth.', 'Fever, burning or aversion to heat.'],
        ['Tongue / pulse', 'Pale with white greasy coat; slippery-slow.', 'Red with yellow greasy coat; slippery-rapid.'],
        ['Formula examples', 'Ping Wei San; Huo Xiang Zheng Qi San.', 'Ba Zheng San; Long Dan Xie Gan Tang according to location.']
      ],
      why: 'Dampness supplies heaviness and stickiness; the attached temperature supplies the decisive color, odor and tongue clues.',
      memory: '<strong>Cold Damp is white and clear;</strong> <strong>Damp-Heat is yellow and foul.</strong>',
      safety: 'Bitter-cold Damp-Heat medicinals can injure a deficient Spleen/Stomach and require careful differentiation. Fever with flank pain, jaundice, inability to urinate, bloody diarrhea or severe dehydration requires urgent evaluation.',
      prompts: ['Which has a white greasy coat?', 'Which has dark urine and foul odor?', 'What symptoms come from Dampness itself?']
    },
    es: {
      names: ['Humedad-Frío', 'Humedad-Calor'],
      shared: 'Ambas causan pesadez, plenitud, síntomas pegajosos, capa grasosa y pulso resbaladizo.',
      fast: ['Frío, pálido y claro: sin sed, capa blanca grasosa, heces blandas y secreciones claras.', 'Caliente, rojo y fétido: sed, capa amarilla grasosa y orina oscura; puede causar disentería, micción dolorosa, ictericia, furúnculos o eczema.'],
      rows: [
        ['Color / olor', 'Pálido, blanco, claro y olor leve.', 'Amarillo, oscuro, rojo y olor fuerte.'],
        ['Temperatura', 'Extremidades frías o preferencia por calor.', 'Fiebre, ardor o aversión al calor.'],
        ['Lengua / pulso', 'Pálida con capa blanca grasosa; resbaladizo-lento.', 'Roja con capa amarilla grasosa; resbaladizo-rápido.'],
        ['Ejemplos de fórmulas', 'Ping Wei San; Huo Xiang Zheng Qi San.', 'Ba Zheng San; Long Dan Xie Gan Tang según localización.']
      ],
      why: 'Humedad aporta pesadez y pegajosidad; la temperatura aporta color, olor y lengua decisivos.',
      memory: '<strong>Humedad-Frío es blanca y clara;</strong> <strong>Humedad-Calor es amarilla y fétida.</strong>',
      safety: 'Las sustancias amargas y frías para Humedad-Calor pueden lesionar un Bazo/Estómago deficiente y requieren diferenciación cuidadosa. Fiebre con dolor lumbar, ictericia, anuria, diarrea sanguinolenta o deshidratación exige urgencia.',
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

cards.push(
  {
    slug: 'qi-deficiency-vs-yang-deficiency', title: 'Qi Deficiency vs. Yang Deficiency', titleEs: 'Deficiencia de Qi vs. Deficiencia de Yang',
    summary: 'Separate reduced functional energy from the same weakness progressing to loss of warming and transforming power.',
    summaryEs: 'Distingue energía funcional reducida de la misma debilidad progresada a pérdida de calentamiento y transformación.',
    search: 'qi yang deficiency fatigue weak voice breath spontaneous sweat cold limbs edema pale swollen tongue weak slow pulse',
    en: {
      names: ['Qi Deficiency', 'Yang Deficiency'],
      shared: 'Both cause fatigue, weakness, low voice, shortness of breath, spontaneous sweating and a weak pulse.',
      fast: ['Functional weakness without pronounced Cold.', 'Qi deficiency plus internal Cold, edema or failed fluid transformation.'],
      rows: [['Temperature', 'Usually neutral; Cold may be mild.', 'Clear aversion to cold and cold limbs.'], ['Fluids', 'Transport may be weak, but edema is not defining.', 'Water retention and edema are common.'], ['Tongue / pulse', 'Pale; weak.', 'Pale, swollen and wet; deep-slow-weak.'], ['Formula examples', 'Si Jun Zi Tang; Bu Zhong Yi Qi Tang by mechanism.', 'Li Zhong Wan; Zhen Wu Tang by organ and fluid pattern.']],
      why: 'Yang deficiency includes Qi deficiency plus loss of warmth and transformation.',
      memory: '<strong>Qi is weak;</strong> <strong>Yang is weak, cold and wet.</strong>',
      safety: 'Sudden profound weakness, hypothermia, fainting, breathlessness or rapid edema requires urgent assessment.',
      prompts: ['Which pattern includes Cold?', 'Which commonly produces edema?', 'What symptoms do both share?']
    },
    es: {
      names: ['Deficiencia de Qi', 'Deficiencia de Yang'],
      shared: 'Ambas causan fatiga, debilidad, voz baja, falta de aire, sudor espontáneo y pulso débil.',
      fast: ['Debilidad funcional sin Frío marcado.', 'Deficiencia de Qi más Frío interno, edema o transformación deficiente.'],
      rows: [['Temperatura', 'Generalmente neutra; Frío leve.', 'Aversión clara al frío y extremidades frías.'], ['Líquidos', 'Transporte débil, pero edema no define.', 'Retención de agua y edema frecuentes.'], ['Lengua / pulso', 'Pálida; débil.', 'Pálida, hinchada y húmeda; profundo-lento-débil.'], ['Ejemplos de fórmulas', 'Si Jun Zi Tang; Bu Zhong Yi Qi Tang según mecanismo.', 'Li Zhong Wan; Zhen Wu Tang según órgano y líquidos.']],
      why: 'Deficiencia de Yang incluye deficiencia de Qi más pérdida de calor y transformación.',
      memory: '<strong>Qi está débil;</strong> <strong>Yang está débil, frío y húmedo.</strong>',
      safety: 'Debilidad súbita profunda, hipotermia, desmayo, disnea o edema rápido requiere urgencia.',
      prompts: ['¿Cuál incluye Frío?', '¿Cuál produce edema?', '¿Qué síntomas comparten?']
    }
  },
  {
    slug: 'yin-deficiency-vs-blood-deficiency', title: 'Yin Deficiency vs. Blood Deficiency', titleEs: 'Deficiencia de Yin vs. Deficiencia de Sangre',
    summary: 'Separate fluid-Essence depletion with empty Heat from insufficient Blood nourishment with pale signs.',
    summaryEs: 'Distingue agotamiento de líquidos/Esencia con Calor vacío de nutrición insuficiente de Sangre con palidez.',
    search: 'yin blood deficiency dryness empty heat night sweat five center pale dizziness blurred vision cramps scant menses red peeled thin rapid',
    en: {
      names: ['Yin Deficiency', 'Blood Deficiency'],
      shared: 'Both fail to nourish tissues and can cause dizziness, insomnia, dryness, scanty menses and a thin pulse.',
      fast: ['Dryness plus empty Heat: night sweating, five-center heat and red peeled tongue.', 'Pallor plus poor nourishment: blurred vision, numbness, cramps and pale tongue.'],
      rows: [['Thermal sign', 'Empty Heat is common.', 'No Heat unless another pattern is present.'], ['Primary tissue clue', 'Dry mouth, throat, skin or mucosa.', 'Pale complexion, lips and nails.'], ['Tongue / pulse', 'Red with little coat; thin-rapid.', 'Pale; thin or choppy.'], ['Formula examples', 'Liu Wei Di Huang Wan; Tian Wang Bu Xin Dan by organ.', 'Si Wu Tang; Gui Pi Tang by organ.']],
      why: 'Blood is part of Yin, but exam patterns distinguish pale Blood undernourishment from red, dry Yin depletion.',
      memory: '<strong>Blood deficiency is pale;</strong> <strong>Yin deficiency is dry and hot.</strong>',
      safety: 'Persistent night sweats, unexplained weight loss, heavy bleeding or severe anemia symptoms require medical evaluation.',
      prompts: ['Which produces a red peeled tongue?', 'Which produces pallor and cramps?', 'What symptoms overlap?']
    },
    es: {
      names: ['Deficiencia de Yin', 'Deficiencia de Sangre'],
      shared: 'Ambas no nutren tejidos y causan mareo, insomnio, sequedad, menstruación escasa y pulso fino.',
      fast: ['Sequedad más Calor vacío: sudor nocturno, calor en cinco centros y lengua roja pelada.', 'Palidez más mala nutrición: visión borrosa, entumecimiento, calambres y lengua pálida.'],
      rows: [['Temperatura', 'Calor vacío frecuente.', 'Sin Calor salvo otro patrón.'], ['Pista tisular', 'Boca, garganta, piel o mucosas secas.', 'Tez, labios y uñas pálidos.'], ['Lengua / pulso', 'Roja con poca capa; fino-rápido.', 'Pálida; fino o áspero.'], ['Ejemplos de fórmulas', 'Liu Wei Di Huang Wan; Tian Wang Bu Xin Dan según órgano.', 'Si Wu Tang; Gui Pi Tang según órgano.']],
      why: 'Sangre forma parte de Yin, pero examen separa palidez de Sangre de agotamiento Yin rojo y seco.',
      memory: '<strong>Sangre deficiente es pálida;</strong> <strong>Yin deficiente es seco y caliente.</strong>',
      safety: 'Sudor nocturno persistente, pérdida de peso, sangrado fuerte o anemia grave requiere evaluación.',
      prompts: ['¿Cuál produce lengua roja pelada?', '¿Cuál palidez y calambres?', '¿Qué síntomas se superponen?']
    }
  },
  {
    slug: 'liver-qi-stagnation-vs-liver-yang-rising', title: 'Liver Qi Stagnation vs. Liver Yang Rising', titleEs: 'Estancamiento de Qi de Hígado vs. Ascenso de Yang de Hígado',
    summary: 'Separate constrained horizontal Qi movement from upward Yang ascendance rooted in Yin deficiency.',
    summaryEs: 'Distingue movimiento horizontal constreñido de Qi de ascenso de Yang con raíz en deficiencia de Yin.',
    search: 'liver qi stagnation yang rising rib side distention sighing mood headache dizziness tinnitus red face yin deficiency wiry',
    en: {
      names: ['Liver Qi Stagnation', 'Liver Yang Rising'],
      shared: 'Both can produce irritability, a wiry pulse, headache and symptoms worsened by stress.',
      fast: ['Constraint and distention: sighing, rib-side fullness, mood swings and irregular menses.', 'Upward pressure: dizziness, tinnitus, vertex/temporal headache and red face with deficiency below.'],
      rows: [['Direction', 'Qi is constrained and fails to spread.', 'Yang rises excessively to the head.'], ['Root', 'Usually excess or functional constraint.', 'Usually Liver-Kidney Yin deficiency below.'], ['Tongue / pulse', 'Often normal or dusky; wiry.', 'Red sides, little coat; wiry, sometimes thin.'], ['Formula examples', 'Chai Hu Shu Gan San; Xiao Yao San if deficient.', 'Tian Ma Gou Teng Yin.']],
      why: 'Both involve Liver dysregulation, but stagnation feels tight and distended while rising Yang concentrates symptoms in the head.',
      memory: '<strong>Qi stagnation presses sideways;</strong> <strong>Yang rising pushes upward.</strong>',
      safety: 'Sudden severe headache, neurologic deficit, very high blood pressure or altered consciousness is an emergency.',
      prompts: ['Which pattern centers on rib-side distention?', 'Which centers on dizziness and tinnitus?', 'What deficient root often underlies rising Yang?']
    },
    es: {
      names: ['Estancamiento de Qi de Hígado', 'Ascenso de Yang de Hígado'],
      shared: 'Ambos causan irritabilidad, pulso de cuerda, cefalea y síntomas peores con estrés.',
      fast: ['Restricción y distensión: suspiros, plenitud costal, cambios de ánimo y menstruación irregular.', 'Presión ascendente: mareo, tinnitus, cefalea de vértex/temporal y cara roja con deficiencia abajo.'],
      rows: [['Dirección', 'Qi está constreñido y no se difunde.', 'Yang asciende excesivamente a cabeza.'], ['Raíz', 'Generalmente exceso o restricción funcional.', 'Generalmente deficiencia de Yin Hígado-Riñón abajo.'], ['Lengua / pulso', 'Normal u oscura; cuerda.', 'Bordes rojos, poca capa; cuerda, a veces fino.'], ['Ejemplos de fórmulas', 'Chai Hu Shu Gan San; Xiao Yao San si deficiente.', 'Tian Ma Gou Teng Yin.']],
      why: 'Estancamiento se siente tenso y distendido; Yang ascendente concentra síntomas en cabeza.',
      memory: '<strong>Qi estancado presiona lateralmente;</strong> <strong>Yang ascendente empuja arriba.</strong>',
      safety: 'Cefalea súbita grave, déficit neurológico, presión muy alta o confusión es emergencia.',
      prompts: ['¿Cuál se centra en distensión costal?', '¿Cuál en mareo y tinnitus?', '¿Qué raíz tiene Yang ascendente?']
    }
  },
  {
    slug: 'spleen-qi-deficiency-vs-dampness-obstructing-spleen', title: 'Spleen Qi Deficiency vs. Dampness Obstructing the Spleen', titleEs: 'Deficiencia de Qi de Bazo vs. Humedad que Obstruye el Bazo',
    summary: 'Separate weak Spleen function as the root from an excess Damp pathogen blocking transformation.',
    summaryEs: 'Distingue función débil de Bazo como raíz de un patógeno de Humedad excesiva que bloquea transformación.',
    search: 'spleen qi deficiency dampness obstruction appetite fatigue loose stool heaviness fullness greasy coat teeth marks weak slippery',
    en: {
      names: ['Spleen Qi Deficiency', 'Dampness Obstructing the Spleen'],
      shared: 'Both cause poor appetite, loose stool, abdominal fullness, fatigue and impaired fluid transformation, and they often coexist.',
      fast: ['Deficiency dominates: weakness, pale complexion, soft abdomen and teeth-marked tongue.', 'Pathogen dominates: heaviness, marked fullness, nausea and thick greasy coating.'],
      rows: [['Nature', 'Deficiency of function.', 'Excess obstruction by Dampness.'], ['Body sensation', 'Weak and tired.', 'Heavy, foggy and encumbered.'], ['Tongue / pulse', 'Pale, teeth-marked; weak.', 'Swollen with thick greasy coat; slippery.'], ['Formula examples', 'Si Jun Zi Tang; Shen Ling Bai Zhu San.', 'Ping Wei San; Huo Xiang Zheng Qi San by context.']],
      why: 'Spleen deficiency can generate Dampness, but the exam asks which side currently dominates: weak root or obstructing branch.',
      memory: '<strong>Deficiency is weak;</strong> <strong>Dampness is heavy and greasy.</strong>',
      safety: 'Persistent diarrhea, vomiting, weight loss or severe edema requires medical assessment.',
      prompts: ['Which has a thick greasy coat?', 'Which has a weak pulse?', 'How can the two patterns coexist?']
    },
    es: {
      names: ['Deficiencia de Qi de Bazo', 'Humedad que Obstruye el Bazo'],
      shared: 'Ambas causan poco apetito, heces blandas, plenitud, fatiga y líquidos alterados; a menudo coexisten.',
      fast: ['Domina deficiencia: debilidad, tez pálida, abdomen blando y lengua con marcas dentales.', 'Domina patógeno: pesadez, plenitud marcada, náusea y capa gruesa grasosa.'],
      rows: [['Naturaleza', 'Deficiencia funcional.', 'Obstrucción excesiva por Humedad.'], ['Sensación corporal', 'Débil y cansado.', 'Pesado, nublado y cargado.'], ['Lengua / pulso', 'Pálida, dentada; débil.', 'Hinchada con capa gruesa grasosa; resbaladizo.'], ['Ejemplos de fórmulas', 'Si Jun Zi Tang; Shen Ling Bai Zhu San.', 'Ping Wei San; Huo Xiang Zheng Qi San según contexto.']],
      why: 'Bazo deficiente genera Humedad; examen pregunta qué domina: raíz débil o rama obstructiva.',
      memory: '<strong>Deficiencia es débil;</strong> <strong>Humedad es pesada y grasosa.</strong>',
      safety: 'Diarrea persistente, vómito, pérdida de peso o edema grave requiere evaluación.',
      prompts: ['¿Cuál tiene capa gruesa grasosa?', '¿Cuál pulso débil?', '¿Cómo pueden coexistir?']
    }
  },
  {
    slug: 'kidney-qi-deficiency-vs-kidney-yang-deficiency', title: 'Kidney Qi Deficiency vs. Kidney Yang Deficiency', titleEs: 'Deficiencia de Qi de Riñón vs. Deficiencia de Yang de Riñón',
    summary: 'Separate failure to secure urine and Essence from deeper Cold and impaired water transformation.',
    summaryEs: 'Distingue falla para asegurar orina y Esencia de Frío más profundo y transformación de agua alterada.',
    search: 'kidney qi yang deficiency urinary frequency incontinence spermatorrhea cold limbs edema libido low back deep weak slow',
    en: {
      names: ['Kidney Qi Deficiency', 'Kidney Yang Deficiency'],
      shared: 'Both cause weak low back/knees, urinary dysfunction, reproductive weakness and a deep weak pulse.',
      fast: ['Failure to secure: frequency, incontinence, enuresis or spermatorrhea without strong Cold.', 'Failure to warm/transform: cold limbs, low libido, edema and copious clear urine.'],
      rows: [['Primary failure', 'Closing and securing.', 'Warming and transforming.'], ['Cold signs', 'Absent or mild.', 'Pronounced.'], ['Tongue / pulse', 'Pale; deep-weak.', 'Pale, swollen, wet; deep-slow-weak.'], ['Formula examples', 'Suo Quan Wan for urinary leakage; pattern-specific securing formulas.', 'Jin Gui Shen Qi Wan; You Gui Wan.']],
      why: 'Kidney-Yang deficiency is deeper: it includes Qi weakness plus Cold and water-metabolism failure.',
      memory: '<strong>Kidney Qi leaks;</strong> <strong>Kidney Yang freezes and floods.</strong>',
      safety: 'New incontinence, urinary retention, blood in urine, leg weakness or saddle numbness requires urgent medical evaluation.',
      prompts: ['Which pattern primarily leaks?', 'Which produces edema and Cold?', 'What symptoms are shared?']
    },
    es: {
      names: ['Deficiencia de Qi de Riñón', 'Deficiencia de Yang de Riñón'],
      shared: 'Ambas causan lumbar/rodillas débiles, disfunción urinaria, debilidad reproductiva y pulso profundo débil.',
      fast: ['Falla de asegurar: frecuencia, incontinencia, enuresis o espermatorrea sin Frío fuerte.', 'Falla de calentar/transformar: extremidades frías, libido baja, edema y orina clara abundante.'],
      rows: [['Falla principal', 'Cerrar y asegurar.', 'Calentar y transformar.'], ['Frío', 'Ausente o leve.', 'Marcado.'], ['Lengua / pulso', 'Pálida; profundo-débil.', 'Pálida, hinchada y húmeda; profundo-lento-débil.'], ['Ejemplos de fórmulas', 'Suo Quan Wan para fuga urinaria; fórmulas aseguradoras según patrón.', 'Jin Gui Shen Qi Wan; You Gui Wan.']],
      why: 'Deficiencia de Yang es más profunda: añade Frío y falla del metabolismo de agua.',
      memory: '<strong>Qi renal fuga;</strong> <strong>Yang renal congela e inunda.</strong>',
      safety: 'Incontinencia nueva, retención, hematuria, debilidad de piernas o anestesia en silla exige urgencia.',
      prompts: ['¿Cuál principalmente fuga?', '¿Cuál produce edema y Frío?', '¿Qué comparten?']
    }
  },
  {
    slug: 'heart-qi-deficiency-vs-heart-yang-deficiency', title: 'Heart Qi Deficiency vs. Heart Yang Deficiency', titleEs: 'Deficiencia de Qi de Corazón vs. Deficiencia de Yang de Corazón',
    summary: 'Separate weak Heart function from deeper failure to warm and move Blood through the chest.',
    summaryEs: 'Distingue función cardíaca débil de falla más profunda para calentar y mover Sangre en el tórax.',
    search: 'heart qi yang deficiency palpitations short breath spontaneous sweat cold limbs chest pain cyanosis pale purple weak knotted',
    en: {
      names: ['Heart Qi Deficiency', 'Heart Yang Deficiency'],
      shared: 'Both cause palpitations, shortness of breath, fatigue, spontaneous sweating and a weak pulse.',
      fast: ['Weak function without strong Cold: symptoms worsen with exertion.', 'Qi deficiency plus Cold and Blood movement failure: chest pain, cold limbs and bluish-purple lips.'],
      rows: [['Thermal sign', 'Neutral or mildly cold.', 'Marked internal Cold.'], ['Blood movement', 'Weak circulation without defining stasis.', 'Cold slows Blood, producing chest obstruction/stasis.'], ['Tongue / pulse', 'Pale; weak.', 'Pale or purple, wet; deep-weak, possibly knotted.'], ['Formula examples', 'Sheng Mai San when Qi-Yin are injured; individualized Heart-Qi tonics.', 'Gui Zhi Gan Cao Tang or stronger rescue strategies by severity.']],
      why: 'Heart-Yang deficiency adds Cold and impaired Blood movement to Heart-Qi weakness.',
      memory: '<strong>Heart Qi cannot pump strongly;</strong> <strong>Heart Yang cannot warm the circulation.</strong>',
      safety: 'Chest pain, fainting, breathlessness or irregular heartbeat requires urgent cardiac evaluation.',
      prompts: ['Which has pronounced Cold?', 'Which may produce purple lips?', 'What symptoms do both share?']
    },
    es: {
      names: ['Deficiencia de Qi de Corazón', 'Deficiencia de Yang de Corazón'],
      shared: 'Ambas causan palpitaciones, falta de aire, fatiga, sudor espontáneo y pulso débil.',
      fast: ['Función débil sin Frío fuerte; empeora con esfuerzo.', 'Deficiencia de Qi más Frío y Sangre inmóvil: dolor torácico, extremidades frías y labios azul-púrpura.'],
      rows: [['Temperatura', 'Neutra o levemente fría.', 'Frío interno marcado.'], ['Movimiento de Sangre', 'Circulación débil sin estasis definitoria.', 'Frío enlentece Sangre y causa obstrucción torácica.'], ['Lengua / pulso', 'Pálida; débil.', 'Pálida o púrpura, húmeda; profundo-débil, quizá anudado.'], ['Ejemplos de fórmulas', 'Sheng Mai San si Qi-Yin lesionados; tónicos individualizados.', 'Gui Zhi Gan Cao Tang o rescate más fuerte según gravedad.']],
      why: 'Deficiencia de Yang añade Frío y mala circulación a debilidad de Qi cardíaco.',
      memory: '<strong>Qi cardíaco no bombea fuerte;</strong> <strong>Yang cardíaco no calienta circulación.</strong>',
      safety: 'Dolor torácico, desmayo, disnea o arritmia requiere evaluación cardíaca urgente.',
      prompts: ['¿Cuál tiene Frío marcado?', '¿Cuál puede producir labios púrpura?', '¿Qué comparten?']
    }
  },
  {
    slug: 'lung-qi-deficiency-vs-wind-cold-invasion', title: 'Lung Qi Deficiency vs. Wind-Cold Invasion', titleEs: 'Deficiencia de Qi de Pulmón vs. Invasión de Viento-Frío',
    summary: 'Separate a chronic weak respiratory root from an acute Exterior pathogen obstructing Lung diffusion.',
    summaryEs: 'Distingue una raíz respiratoria crónica y débil de un patógeno Exterior agudo que obstruye difusión pulmonar.',
    search: 'lung qi deficiency wind cold invasion chronic acute cough short breath low voice spontaneous sweat chills body aches floating weak tight',
    en: {
      names: ['Lung Qi Deficiency', 'Wind-Cold Invasion'],
      shared: 'Both can cause cough, shortness of breath, aversion to cold and abnormal sweating, and deficiency can predispose to invasion.',
      fast: ['Chronic weakness: low voice, fatigue, spontaneous sweating and recurrent colds.', 'Acute onset: chills, headache, body aches, nasal congestion and a floating pulse.'],
      rows: [['Nature', 'Internal deficiency/root.', 'External excess/branch.'], ['Timing', 'Chronic or recurrent.', 'Acute.'], ['Pulse', 'Weak, especially right cun.', 'Floating-tight or floating-moderate by subtype.'], ['Formula examples', 'Yu Ping Feng San for Defensive-Qi deficiency.', 'Ma Huang Tang or Gui Zhi Tang by sweat pattern.']],
      why: 'A weak root and an acute branch may coexist, but the exam asks whether the current presentation is chronic deficiency or active invasion.',
      memory: '<strong>Deficiency keeps recurring;</strong> <strong>invasion begins suddenly.</strong>',
      safety: 'Breathing difficulty, chest pain, high fever, confusion or low oxygen requires urgent medical assessment.',
      prompts: ['Which pattern is acute?', 'Which causes low voice and recurrent colds?', 'How does the pulse differ?']
    },
    es: {
      names: ['Deficiencia de Qi de Pulmón', 'Invasión de Viento-Frío'],
      shared: 'Ambas causan tos, falta de aire, aversión al frío y sudor alterado; deficiencia predispone a invasión.',
      fast: ['Debilidad crónica: voz baja, fatiga, sudor espontáneo y resfriados recurrentes.', 'Inicio agudo: escalofríos, cefalea, dolor corporal, congestión nasal y pulso flotante.'],
      rows: [['Naturaleza', 'Deficiencia interna/raíz.', 'Exceso externo/rama.'], ['Tiempo', 'Crónico o recurrente.', 'Agudo.'], ['Pulso', 'Débil, especialmente cun derecho.', 'Flotante-tenso o flotante-moderado según subtipo.'], ['Ejemplos de fórmulas', 'Yu Ping Feng San para Qi Defensivo deficiente.', 'Ma Huang Tang o Gui Zhi Tang según sudor.']],
      why: 'Raíz débil y rama aguda coexisten, pero examen pregunta qué domina ahora.',
      memory: '<strong>Deficiencia recurre;</strong> <strong>invasión comienza súbitamente.</strong>',
      safety: 'Disnea, dolor torácico, fiebre alta, confusión u oxígeno bajo requiere urgencia.',
      prompts: ['¿Cuál es agudo?', '¿Cuál causa voz baja y resfriados recurrentes?', '¿Cómo difiere pulso?']
    }
  },
  {
    slug: 'food-stagnation-vs-spleen-qi-deficiency', title: 'Food Stagnation vs. Spleen Qi Deficiency', titleEs: 'Estancamiento de Alimentos vs. Deficiencia de Qi de Bazo',
    summary: 'Separate acute excess retention after overeating from chronic weak transformation and transport.',
    summaryEs: 'Distingue retención excesiva aguda tras comer de más de transformación y transporte débiles crónicos.',
    search: 'food stagnation spleen qi deficiency overeating fullness pain sour belching foul stool appetite fatigue loose stool excess deficiency',
    en: {
      names: ['Food Stagnation', 'Spleen Qi Deficiency'],
      shared: 'Both cause poor appetite, abdominal fullness, belching and bowel changes, and chronic Spleen deficiency can permit Food Stagnation.',
      fast: ['Acute excess after overeating: focal fullness/pain, sour belching, foul stool and thick coat.', 'Chronic deficiency: fatigue, soft abdominal weakness, loose stools and pale teeth-marked tongue.'],
      rows: [['Onset', 'Acute and linked to food intake.', 'Chronic and constitutional.'], ['Abdomen', 'Full, painful and dislikes pressure.', 'Weak, dull discomfort often improved by pressure.'], ['Tongue / pulse', 'Thick greasy coat; slippery/full.', 'Pale, teeth-marked; weak.'], ['Formula examples', 'Bao He Wan.', 'Si Jun Zi Tang; Shen Ling Bai Zhu San.']],
      why: 'Both reduce appetite, but excess retention is full and foul while deficiency is weak and chronic.',
      memory: '<strong>Food Stagnation is stuffed;</strong> <strong>Spleen deficiency is tired.</strong>',
      safety: 'Severe pain, persistent vomiting, inability to pass stool/gas, blood or major weight loss requires medical evaluation.',
      prompts: ['Which follows overeating?', 'Which has sour belching and foul stool?', 'Which has a weak pulse?']
    },
    es: {
      names: ['Estancamiento de Alimentos', 'Deficiencia de Qi de Bazo'],
      shared: 'Ambos causan poco apetito, plenitud, eructos y cambios intestinales; Bazo débil permite estancamiento.',
      fast: ['Exceso agudo tras comer de más: plenitud/dolor focal, eructo ácido, heces fétidas y capa gruesa.', 'Deficiencia crónica: fatiga, abdomen débil, heces blandas y lengua pálida dentada.'],
      rows: [['Inicio', 'Agudo y ligado a comida.', 'Crónico y constitucional.'], ['Abdomen', 'Lleno, doloroso y rechaza presión.', 'Débil, molestia sorda mejor con presión.'], ['Lengua / pulso', 'Capa gruesa grasosa; resbaladizo/lleno.', 'Pálida, dentada; débil.'], ['Ejemplos de fórmulas', 'Bao He Wan.', 'Si Jun Zi Tang; Shen Ling Bai Zhu San.']],
      why: 'Ambos quitan apetito, pero retención es llena y fétida; deficiencia es débil y crónica.',
      memory: '<strong>Estancamiento está lleno;</strong> <strong>Bazo deficiente está cansado.</strong>',
      safety: 'Dolor grave, vómito persistente, ausencia de heces/gases, sangre o pérdida de peso requiere evaluación.',
      prompts: ['¿Cuál sigue a comer de más?', '¿Cuál tiene eructo ácido?', '¿Cuál pulso débil?']
    }
  },
  {
    slug: 'blood-heat-vs-blood-stasis', title: 'Blood Heat vs. Blood Stasis', titleEs: 'Calor en la Sangre vs. Estasis de Sangre',
    summary: 'Separate reckless hot Blood causing bright bleeding from obstructed Blood causing fixed pain and purple signs.',
    summaryEs: 'Distingue Sangre caliente y descontrolada que sangra rojo vivo de Sangre obstruida con dolor fijo y signos púrpura.',
    search: 'blood heat stasis bleeding bright red reckless fixed stabbing pain clots purple tongue rapid wiry choppy',
    en: {
      names: ['Blood Heat', 'Blood Stasis'],
      shared: 'Both can cause abnormal bleeding, menstrual disorders, red-purple skin lesions and a darkened tongue.',
      fast: ['Heat agitates Blood: bright-red rapid bleeding, fever, thirst and a rapid pulse.', 'Obstruction prevents movement: fixed stabbing pain, dark clots, masses and purple tongue.'],
      rows: [['Primary dynamic', 'Blood moves recklessly from Heat.', 'Blood fails to move.'], ['Bleeding', 'Bright red, fresh and often profuse.', 'Dark, clotted or intermittent.'], ['Tongue / pulse', 'Red/crimson; rapid.', 'Purple/dusky with spots; wiry/choppy.'], ['Formula examples', 'Xi Jiao Di Huang Tang with modern Shui Niu Jiao substitution.', 'Xue Fu Zhu Yu Tang; Gui Zhi Fu Ling Wan by location.']],
      why: 'Both can bleed, but Heat accelerates and brightens while stasis darkens, clots and fixes pain.',
      memory: '<strong>Heat makes Blood run;</strong> <strong>stasis makes Blood stop.</strong>',
      safety: 'Heavy bleeding, coughing/vomiting blood, black stool, pregnancy bleeding or severe pain requires urgent care.',
      prompts: ['Which causes bright-red bleeding?', 'Which causes dark clots and fixed pain?', 'How do tongue and pulse differ?']
    },
    es: {
      names: ['Calor en la Sangre', 'Estasis de Sangre'],
      shared: 'Ambos causan sangrado anormal, trastornos menstruales, lesiones rojo-púrpura y lengua oscura.',
      fast: ['Calor agita Sangre: sangrado rojo vivo y rápido, fiebre, sed y pulso rápido.', 'Obstrucción impide movimiento: dolor fijo punzante, coágulos oscuros, masas y lengua púrpura.'],
      rows: [['Dinámica', 'Sangre se mueve imprudentemente por Calor.', 'Sangre no se mueve.'], ['Sangrado', 'Rojo vivo, fresco y a menudo profuso.', 'Oscuro, coagulado o intermitente.'], ['Lengua / pulso', 'Roja/carmesí; rápido.', 'Púrpura/oscura con puntos; cuerda/áspero.'], ['Ejemplos de fórmulas', 'Xi Jiao Di Huang Tang con sustitución moderna Shui Niu Jiao.', 'Xue Fu Zhu Yu Tang; Gui Zhi Fu Ling Wan según lugar.']],
      why: 'Calor acelera y vuelve brillante; estasis oscurece, coagula y fija el dolor.',
      memory: '<strong>Calor hace correr Sangre;</strong> <strong>estasis la detiene.</strong>',
      safety: 'Hemorragia fuerte, hemoptisis/hematemesis, heces negras, sangrado en embarazo o dolor grave exige urgencia.',
      prompts: ['¿Cuál causa sangrado rojo vivo?', '¿Cuál coágulos oscuros y dolor fijo?', '¿Cómo difieren lengua/pulso?']
    }
  },
  {
    slug: 'exterior-wind-cold-vs-wind-heat', title: 'Exterior Wind-Cold vs. Exterior Wind-Heat', titleEs: 'Viento-Frío Exterior vs. Viento-Calor Exterior',
    summary: 'Separate chills-dominant Exterior Cold from fever-and-throat-dominant Exterior Heat.',
    summaryEs: 'Distingue Frío Exterior dominado por escalofríos de Calor Exterior dominado por fiebre y garganta.',
    search: 'exterior wind cold heat chills fever sweat thirst sore throat nasal discharge white yellow floating tight rapid',
    en: {
      names: ['Exterior Wind-Cold', 'Exterior Wind-Heat'],
      shared: 'Both begin acutely with fever, aversion to wind, headache, body aches and a floating pulse.',
      fast: ['Mild fever with severe chills, no thirst, headache and body/neck pain; clear discharge, white coat and floating-tight pulse.', 'More severe fever with mild chills, thirst and sore throat; may vent rashes or affect the eyes.'],
      rows: [['Typical herb nature', 'Mostly acrid and warm.', 'Mostly acrid and cool.'], ['Temperature balance', 'Aversion to cold stronger than fever.', 'Fever stronger than chills.'], ['Layer / organ', 'Superficial Exterior and Lung.', 'Superficial Exterior and Lung with Heat signs.'], ['Additional clue', 'Clear nasal discharge and body/neck pain.', 'Rash needing venting or red, painful Wind-Heat eyes.'], ['Tongue / pulse', 'White coat; floating-tight.', 'Red tip/edges; floating-rapid.'], ['Formula examples', 'Ma Huang Tang or Gui Zhi Tang by sweat pattern.', 'Yin Qiao San or Sang Ju Yin by throat/cough emphasis.']],
      why: 'Both are Exterior patterns. Relative fever/chills, thirst, throat and secretion color determine Cold versus Heat.',
      memory: '<strong>Cold is clear with chills;</strong> <strong>Heat is yellow with throat pain.</strong>',
      safety: 'Do not overuse sweat-promoting medicinals: excessive dispersion can consume Qi and Yin. Use extra caution with bleeding or pregnancy. Breathing difficulty, high persistent fever, confusion, stiff neck or dehydration requires medical evaluation.',
      prompts: ['Which has stronger chills?', 'Which has thirst and sore throat?', 'How do secretions and pulse differ?']
    },
    es: {
      names: ['Viento-Frío Exterior', 'Viento-Calor Exterior'],
      shared: 'Ambos comienzan agudamente con fiebre, aversión al viento, cefalea, dolor corporal y pulso flotante.',
      fast: ['Fiebre leve con escalofríos intensos, sin sed, cefalea y dolor de cuello/cuerpo; secreción clara, capa blanca y pulso flotante-tenso.', 'Fiebre más intensa con escalofríos leves, sed y dolor de garganta; puede ventilar erupciones o afectar ojos.'],
      rows: [['Naturaleza típica de hierbas', 'Principalmente acres y tibias.', 'Principalmente acres y frescas.'], ['Balance térmico', 'Aversión al frío mayor que fiebre.', 'Fiebre mayor que escalofríos.'], ['Nivel / órgano', 'Exterior superficial y Pulmón.', 'Exterior superficial y Pulmón con signos de Calor.'], ['Pista adicional', 'Secreción nasal clara y dolor de cuello/cuerpo.', 'Erupción que necesita ventilarse u ojos rojos/dolorosos por Viento-Calor.'], ['Lengua / pulso', 'Capa blanca; flotante-tenso.', 'Punta/bordes rojos; flotante-rápido.'], ['Ejemplos de fórmulas', 'Ma Huang Tang o Gui Zhi Tang según sudor.', 'Yin Qiao San o Sang Ju Yin según garganta/tos.']],
      why: 'Fiebre/escalofríos relativos, sed, garganta y color de secreciones determinan Frío o Calor.',
      memory: '<strong>Frío es claro con escalofríos;</strong> <strong>Calor es amarillo con garganta.</strong>',
      safety: 'No abusar de hierbas sudoríficas: dispersión excesiva puede consumir Qi y Yin. Precaución adicional en sangrado o embarazo. Disnea, fiebre alta persistente, confusión, rigidez cervical o deshidratación requiere evaluación.',
      prompts: ['¿Cuál tiene más escalofríos?', '¿Cuál sed y garganta dolorida?', '¿Cómo difieren secreciones y pulso?']
    }
  },
  {
    slug: 'full-heat-vs-empty-heat', title: 'Full Heat vs. Empty Heat', titleEs: 'Calor Pleno vs. Calor Vacío',
    summary: 'Separate forceful excess Heat from deficient cooling and fluids that produce afternoon or night Heat.',
    summaryEs: 'Distingue Calor de exceso fuerte de enfriamiento y líquidos deficientes que producen Calor vespertino o nocturno.',
    search: 'full heat empty heat excess deficiency yin high fever afternoon night sweat five center thirst red tongue yellow coat peeled forceful thin rapid',
    en: {
      names: ['Full Heat', 'Empty Heat'],
      shared: 'Both may cause thirst, irritability, a red tongue, Heat sensations and a rapid pulse.',
      fast: ['Strong excess: high or continuous fever, intense thirst for cold drinks, red tongue with yellow coat and a forceful rapid pulse.', 'Yin deficiency: afternoon fever, night sweating, five-center Heat, malar flush, peeled red tongue and a thin rapid pulse.'],
      rows: [
        ['Mechanism', 'Excess pathogenic Heat blazes internally.', 'Yin and fluids fail to restrain Yang.'],
        ['Timing / strength', 'Strong and persistent, often worse in daytime.', 'Low-grade or tidal, classically worse afternoon/night.'],
        ['Tongue / pulse', 'Red with dry yellow coat; full/forceful-rapid.', 'Red with little or no coat; thin-rapid.'],
        ['Formula examples', 'Bai Hu Tang by Qi-level presentation.', 'Zhi Bai Di Huang Wan or Qing Hao Bie Jia Tang by context.']
      ],
      why: 'Redness and rapidity are shared. Coat and pulse strength reveal whether Heat is an excess pathogen or appears because Yin cannot cool.',
      memory: '<strong>Full Heat has force and coat;</strong> <strong>Empty Heat has timing and no coat.</strong>',
      safety: 'High persistent fever, confusion, stiff neck, breathing difficulty, severe dehydration or immunocompromise requires urgent medical evaluation.',
      prompts: ['Which Heat pattern has a forceful pulse?', 'Which peaks at night?', 'How does the tongue coating differ?']
    },
    es: {
      names: ['Calor Pleno', 'Calor Vacío'],
      shared: 'Ambos pueden causar sed, irritabilidad, lengua roja, sensación de Calor y pulso rápido.',
      fast: ['Exceso fuerte: fiebre alta o continua, sed intensa por frío, lengua roja con capa amarilla y pulso fuerte-rápido.', 'Deficiencia Yin: fiebre vespertina, sudor nocturno, Calor de cinco centros, rubor malar, lengua roja pelada y pulso fino-rápido.'],
      rows: [
        ['Mecanismo', 'Calor patógeno excesivo arde internamente.', 'Yin y líquidos no contienen Yang.'],
        ['Horario / fuerza', 'Fuerte y persistente, a menudo peor de día.', 'Bajo o vespertino, clásicamente peor tarde/noche.'],
        ['Lengua / pulso', 'Roja con capa amarilla seca; lleno/fuerte-rápido.', 'Roja con poca o ninguna capa; fino-rápido.'],
        ['Ejemplos de fórmulas', 'Bai Hu Tang según presentación de nivel Qi.', 'Zhi Bai Di Huang Wan o Qing Hao Bie Jia Tang según contexto.']
      ],
      why: 'Rojez y rapidez son comunes. Capa y fuerza del pulso revelan exceso patógeno o falta de Yin para enfriar.',
      memory: '<strong>Calor Pleno tiene fuerza y capa;</strong> <strong>Calor Vacío tiene horario y no tiene capa.</strong>',
      safety: 'Fiebre alta persistente, confusión, nuca rígida, disnea, deshidratación grave o inmunosupresión requiere evaluación urgente.',
      prompts: ['¿Cuál tiene pulso fuerte?', '¿Cuál aumenta de noche?', '¿Cómo difiere la capa lingual?']
    }
  },
  {
    slug: 'interior-excess-cold-vs-yang-deficiency-cold', title: 'Interior Excess Cold vs. Yang-Deficiency Cold', titleEs: 'Frío Interior por Exceso vs. Frío por Deficiencia de Yang',
    summary: 'Separate an obstructing Cold pathogen from chronic failure of Yang to warm and transform.',
    summaryEs: 'Distingue un patógeno Frío obstructivo de la falla crónica de Yang para calentar y transformar.',
    search: 'interior excess cold yang deficiency cold acute chronic severe fixed pain pressure warmth fatigue loose stool pale tongue thick white weak tight slow',
    en: {
      names: ['Interior Excess Cold', 'Yang-Deficiency Cold'],
      shared: 'Both cause aversion to cold, cold limbs, abdominal discomfort, pale tongue signs and a slow pulse.',
      fast: ['Acute obstruction: severe fixed pain, worse with pressure, robust constitution, thick white coat and a deep tight forceful pulse.', 'Chronic weakness: dull pain improved by pressure, fatigue, loose stool, edema or clear urine, pale swollen tongue and deep weak slow pulse.'],
      rows: [
        ['Mechanism', 'Cold pathogen contracts and blocks Qi.', 'Yang Qi fails to warm, move and transform.'],
        ['Pain / pressure', 'Severe, fixed and resistant to pressure; warmth helps.', 'Dull, lingering and relieved by warmth and pressure.'],
        ['Tongue / pulse', 'Pale with thick moist white coat; deep-tight-slow.', 'Pale, swollen and wet; deep-weak-slow.'],
        ['Formula examples', 'Liang Fu Wan or Tian Tai Wu Yao San by location.', 'Li Zhong Wan, Zhen Wu Tang or Si Ni Tang by depth.']
      ],
      why: 'Temperature alone does not distinguish them. Force, onset, pressure response and pulse strength separate excess obstruction from deficient warming.',
      memory: '<strong>Excess Cold grips;</strong> <strong>Yang deficiency fades.</strong>',
      safety: 'Severe abdominal pain, rigid abdomen, persistent vomiting, fainting, GI bleeding or hypothermia requires urgent care. Fu Zi formulas require processed material and expert supervision.',
      prompts: ['Which pattern resists pressure?', 'Which has edema and fatigue?', 'How does pulse strength differ?']
    },
    es: {
      names: ['Frío Interior por Exceso', 'Frío por Deficiencia de Yang'],
      shared: 'Ambos causan aversión al frío, miembros fríos, molestia abdominal, signos de lengua pálida y pulso lento.',
      fast: ['Obstrucción aguda: dolor fijo grave peor con presión, constitución fuerte, capa blanca gruesa y pulso profundo-tenso-fuerte.', 'Debilidad crónica: dolor sordo mejor con presión, fatiga, heces sueltas, edema u orina clara, lengua pálida hinchada y pulso profundo-débil-lento.'],
      rows: [
        ['Mecanismo', 'Patógeno Frío contrae y bloquea Qi.', 'Qi Yang no calienta, mueve ni transforma.'],
        ['Dolor / presión', 'Grave, fijo y rechaza presión; mejora con calor.', 'Sordo, prolongado y mejora con calor y presión.'],
        ['Lengua / pulso', 'Pálida con capa blanca húmeda gruesa; profundo-tenso-lento.', 'Pálida, hinchada y mojada; profundo-débil-lento.'],
        ['Ejemplos de fórmulas', 'Liang Fu Wan o Tian Tai Wu Yao San según localización.', 'Li Zhong Wan, Zhen Wu Tang o Si Ni Tang según profundidad.']
      ],
      why: 'La temperatura sola no distingue. Fuerza, inicio, respuesta a presión y fuerza del pulso separan obstrucción de deficiencia.',
      memory: '<strong>Frío por exceso aprieta;</strong> <strong>Yang deficiente se apaga.</strong>',
      safety: 'Dolor abdominal grave, abdomen rígido, vómito persistente, desmayo, sangrado GI o hipotermia requiere urgencias. Fórmulas con Fu Zi exigen material procesado y experto.',
      prompts: ['¿Cuál rechaza presión?', '¿Cuál tiene edema y fatiga?', '¿Cómo difiere la fuerza del pulso?']
    }
  },
  {
    slug: 'qi-stagnation-vs-blood-stasis', title: 'Qi Stagnation vs. Blood Stasis', titleEs: 'Estancamiento de Qi vs. Estasis de Sangre',
    summary: 'Separate mobile distention influenced by emotion from fixed stabbing pain and purple structural signs.',
    summaryEs: 'Distingue distensión móvil influida por emociones de dolor fijo punzante y signos estructurales púrpura.',
    search: 'qi stagnation blood stasis distending moving emotional fixed stabbing night pain purple tongue clots masses wiry choppy',
    en: {
      names: ['Qi Stagnation', 'Blood Stasis'],
      shared: 'Both obstruct free flow and can cause pain, menstrual irregularity, masses, irritability and a wiry pulse.',
      fast: ['Qi obstruction: distending, moving or fluctuating discomfort strongly linked to emotion; tongue may be normal.', 'Blood obstruction: fixed, stabbing pain worse at night, dark clots, palpable masses and a purple or spotted tongue.'],
      rows: [
        ['Nature of obstruction', 'Functional movement of Qi is impaired.', 'Blood circulation is physically fixed or congealed.'],
        ['Pain', 'Distending, wandering and variable.', 'Sharp, stabbing, fixed and often worse at night.'],
        ['Tongue / pulse', 'Normal or slightly dusky; wiry.', 'Purple/dusky with spots; choppy or wiry.'],
        ['Formula examples', 'Chai Hu Shu Gan San or Yue Ju Wan.', 'Xue Fu Zhu Yu Tang or other Zhu Yu formula by location.']
      ],
      why: 'Qi stagnation often precedes Blood stasis, so they can coexist. Fixity, clots and purple signs show that obstruction has reached Blood.',
      memory: '<strong>Qi moves and distends;</strong> <strong>Blood fixes and stabs.</strong>',
      safety: 'New chest pain, unilateral swelling, neurologic deficit, pregnancy pain, heavy bleeding or a new mass requires urgent biomedical evaluation.',
      prompts: ['Which pain moves with emotion?', 'Which worsens at night?', 'Which produces a purple spotted tongue?']
    },
    es: {
      names: ['Estancamiento de Qi', 'Estasis de Sangre'],
      shared: 'Ambos obstruyen flujo y causan dolor, irregularidad menstrual, masas, irritabilidad y pulso de cuerda.',
      fast: ['Obstrucción de Qi: molestia distensiva, móvil o variable muy ligada a emociones; lengua puede ser normal.', 'Obstrucción de Sangre: dolor fijo punzante peor de noche, coágulos oscuros, masas palpables y lengua púrpura o con puntos.'],
      rows: [
        ['Naturaleza', 'Movimiento funcional de Qi está alterado.', 'Circulación de Sangre está fija o coagulada.'],
        ['Dolor', 'Distensivo, errante y variable.', 'Agudo, punzante, fijo y a menudo peor de noche.'],
        ['Lengua / pulso', 'Normal o algo oscura; de cuerda.', 'Púrpura/oscura con puntos; áspero o de cuerda.'],
        ['Ejemplos de fórmulas', 'Chai Hu Shu Gan San o Yue Ju Wan.', 'Xue Fu Zhu Yu Tang u otra Zhu Yu según localización.']
      ],
      why: 'Qi estancado puede preceder estasis de Sangre y coexistir. Fijeza, coágulos y púrpura muestran que la obstrucción alcanzó Sangre.',
      memory: '<strong>Qi se mueve y distiende;</strong> <strong>Sangre se fija y punza.</strong>',
      safety: 'Dolor torácico nuevo, hinchazón unilateral, déficit neurológico, dolor en embarazo, sangrado abundante o masa nueva requiere urgencia biomédica.',
      prompts: ['¿Qué dolor cambia con emociones?', '¿Cuál empeora de noche?', '¿Cuál da lengua púrpura con puntos?']
    }
  },
  {
    slug: 'liver-blood-deficiency-vs-liver-yin-deficiency', title: 'Liver Blood Deficiency vs. Liver Yin Deficiency', titleEs: 'Deficiencia de Sangre de Hígado vs. Deficiencia de Yin de Hígado',
    summary: 'Separate pale failure to nourish from deeper fluid-Essence depletion with Empty Heat.',
    summaryEs: 'Distingue falla pálida de nutrición de agotamiento más profundo de líquidos-Esencia con Calor Vacío.',
    search: 'liver blood deficiency yin deficiency pale red dry eyes blurred vision cramps numbness scant menses night sweat five center heat tinnitus',
    en: {
      names: ['Liver Blood Deficiency', 'Liver Yin Deficiency'],
      shared: 'Both fail to nourish the eyes, sinews and menstrual system, causing blurred vision, cramps, dizziness, dryness and scanty menses.',
      fast: ['Blood deficiency is pale: pale face/tongue, numbness, brittle nails, scant menses and a thin or choppy pulse.', 'Yin deficiency is red and hot: dry eyes/throat, tinnitus, night sweating, five-center Heat, red peeled tongue and thin rapid pulse.'],
      rows: [
        ['Depth', 'Deficient Blood nutrition.', 'Deeper depletion of Blood, fluids and Yin anchoring.'],
        ['Thermal sign', 'Usually no pronounced Empty Heat.', 'Afternoon/night Heat and dryness are characteristic.'],
        ['Tongue / pulse', 'Pale, often thin; thin/choppy.', 'Red with little coat; thin-rapid.'],
        ['Formula examples', 'Si Wu Tang.', 'Yi Guan Jian or a Liver-Kidney Yin tonic by presentation.']
      ],
      why: 'Eye and menstrual symptoms overlap. Pale versus red, and absence versus presence of Empty Heat, reveal the depth of deficiency.',
      memory: '<strong>Blood deficiency is pale and underfed;</strong> <strong>Yin deficiency is dry, red and warm.</strong>',
      safety: 'Sudden visual loss, severe headache, neurologic symptoms, heavy bleeding or pregnancy-related bleeding requires urgent evaluation.',
      prompts: ['Which pattern has a pale tongue?', 'Which has night sweating?', 'What signs show progression from Blood to Yin deficiency?']
    },
    es: {
      names: ['Deficiencia de Sangre de Hígado', 'Deficiencia de Yin de Hígado'],
      shared: 'Ambas dejan de nutrir ojos, tendones y sistema menstrual, causando visión borrosa, calambres, mareo, sequedad y menstruación escasa.',
      fast: ['Deficiencia de Sangre es pálida: cara/lengua pálida, entumecimiento, uñas frágiles, menstruación escasa y pulso fino/áspero.', 'Deficiencia Yin es roja y caliente: ojos/garganta secos, tinnitus, sudor nocturno, Calor de cinco centros, lengua roja pelada y pulso fino-rápido.'],
      rows: [
        ['Profundidad', 'Nutrición deficiente de Sangre.', 'Agotamiento más profundo de Sangre, líquidos y anclaje Yin.'],
        ['Signo térmico', 'Generalmente sin Calor Vacío pronunciado.', 'Calor tarde/noche y sequedad son característicos.'],
        ['Lengua / pulso', 'Pálida, a menudo fina; fino/áspero.', 'Roja con poca capa; fino-rápido.'],
        ['Ejemplos de fórmulas', 'Si Wu Tang.', 'Yi Guan Jian o tónico Yin de Hígado-Riñón según presentación.']
      ],
      why: 'Ojos y menstruación se superponen. Pálido frente a rojo y ausencia frente a presencia de Calor Vacío revelan profundidad.',
      memory: '<strong>Sangre deficiente es pálida y mal nutrida;</strong> <strong>Yin deficiente es seco, rojo y caliente.</strong>',
      safety: 'Pérdida visual súbita, cefalea grave, signos neurológicos, sangrado abundante o sangrado en embarazo requiere urgencia.',
      prompts: ['¿Cuál tiene lengua pálida?', '¿Cuál sudor nocturno?', '¿Qué muestra progresión de Sangre a Yin?']
    }
  },
  {
    slug: 'stomach-fire-vs-stomach-yin-deficiency', title: 'Stomach Fire vs. Stomach Yin Deficiency', titleEs: 'Fuego de Estómago vs. Deficiencia de Yin de Estómago',
    summary: 'Separate blazing excess with voracious hunger from dry deficient Stomach function with little desire to eat.',
    summaryEs: 'Distingue exceso ardiente con hambre voraz de función seca deficiente del Estómago con poco deseo de comer.',
    search: 'stomach fire yin deficiency burning epigastric ravenous hunger no appetite dry mouth gums bleeding foul breath constipation red tongue yellow coat peeled center',
    en: {
      names: ['Stomach Fire', 'Stomach Yin Deficiency'],
      shared: 'Both may cause thirst, dry mouth, constipation, a red tongue and epigastric discomfort.',
      fast: ['Excess Fire: burning pain, voracious hunger, foul breath, swollen bleeding gums, strong thirst and a forceful rapid pulse.', 'Deficient Yin: dull or empty discomfort, hunger without desire to eat, dry mouth with small sips, dry retching and a peeled central tongue.'],
      rows: [
        ['Appetite', 'Excessive hunger and rapid return of appetite.', 'Poor appetite or hunger without desire to eat.'],
        ['Mouth / gums', 'Foul breath, mouth ulcers, swollen or bleeding gums.', 'Dry mouth/throat without strong foul Heat signs.'],
        ['Tongue / pulse', 'Red with thick/dry yellow coat; slippery-forceful-rapid.', 'Red, dry, peeled in center; thin-rapid.'],
        ['Formula examples', 'Qing Wei San; Yu Nu Jian when Kidney Yin deficiency coexists.', 'Yi Wei Tang or Sha Shen Mai Dong Tang by context.']
      ],
      why: 'Both are dry and red. Appetite intensity, gum Heat, tongue coating and pulse strength separate excess Fire from depleted Yin.',
      memory: '<strong>Stomach Fire devours;</strong> <strong>Stomach Yin wants but cannot receive.</strong>',
      safety: 'Vomiting blood, black stool, severe persistent pain, progressive swallowing difficulty, dehydration or unexplained weight loss needs prompt medical evaluation.',
      prompts: ['Which pattern has voracious hunger?', 'Which has a peeled center tongue?', 'Which produces swollen bleeding gums?']
    },
    es: {
      names: ['Fuego de Estómago', 'Deficiencia de Yin de Estómago'],
      shared: 'Ambos pueden causar sed, boca seca, estreñimiento, lengua roja y molestia epigástrica.',
      fast: ['Fuego de exceso: dolor ardiente, hambre voraz, mal aliento, encías hinchadas/sangrantes, sed fuerte y pulso fuerte-rápido.', 'Yin deficiente: molestia sorda/vacía, hambre sin deseo de comer, boca seca con sorbos pequeños, arcadas secas y centro lingual pelado.'],
      rows: [
        ['Apetito', 'Hambre excesiva y retorno rápido del apetito.', 'Poco apetito o hambre sin deseo de comer.'],
        ['Boca / encías', 'Mal aliento, úlceras, encías hinchadas o sangrantes.', 'Boca/garganta seca sin signos fuertes de Fuego fétido.'],
        ['Lengua / pulso', 'Roja con capa amarilla gruesa/seca; resbaladizo-fuerte-rápido.', 'Roja, seca, pelada al centro; fino-rápido.'],
        ['Ejemplos de fórmulas', 'Qing Wei San; Yu Nu Jian si coexiste deficiencia Yin renal.', 'Yi Wei Tang o Sha Shen Mai Dong Tang según contexto.']
      ],
      why: 'Ambos son secos y rojos. Intensidad del apetito, Calor de encías, capa y fuerza del pulso separan Fuego de Yin agotado.',
      memory: '<strong>Fuego de Estómago devora;</strong> <strong>Yin de Estómago quiere pero no recibe.</strong>',
      safety: 'Vómito de sangre, heces negras, dolor grave persistente, disfagia progresiva, deshidratación o pérdida de peso inexplicada requiere evaluación pronta.',
      prompts: ['¿Cuál tiene hambre voraz?', '¿Cuál centro lingual pelado?', '¿Cuál causa encías hinchadas/sangrantes?']
    }
  },
  {
    slug: 'phlegm-misting-heart-vs-phlegm-fire', title: 'Phlegm Misting the Heart vs. Phlegm-Fire Harassing the Heart', titleEs: 'Flema que Nubla el Corazón vs. Flema-Fuego que Acosa el Corazón',
    summary: 'Separate cold turbid withdrawal and clouding from hot agitated mania and insomnia.',
    summaryEs: 'Distingue retraimiento y nubosidad fría-turbia de agitación caliente con manía e insomnio.',
    search: 'phlegm misting heart mind phlegm fire harassing cold turbid confusion lethargy depression mania agitation insomnia incoherent speech greasy white yellow tongue slippery slow rapid',
    en: {
      names: ['Phlegm Misting the Heart', 'Phlegm-Fire Harassing the Heart'],
      shared: 'Both obstruct the Heart orifices and can cause confusion, abnormal speech or behavior, chest oppression and a slippery pulse.',
      fast: ['Cold/turbid and withdrawn: mental dullness, lethargy, depression, muttering, stupor, greasy white coat and slippery-slow pulse.', 'Hot and agitated: insomnia, shouting, mania, violent behavior, bitter taste, red tongue with yellow greasy coat and slippery-rapid pulse.'],
      rows: [
        ['Direction', 'Clouds and suppresses consciousness.', 'Agitates and drives behavior upward/outward.'],
        ['Temperature', 'Usually Cold or neutral Phlegm-Damp.', 'Phlegm combined with Fire.'],
        ['Tongue / pulse', 'Swollen with white greasy coat; slippery-slow.', 'Red with yellow greasy coat; slippery-rapid.'],
        ['Formula examples', 'Di Tan Tang by presentation.', 'Wen Dan Tang or Huang Lian Wen Dan Tang by Heat severity.']
      ],
      why: 'Both involve Phlegm and altered behavior. Withdrawal versus agitation, and white-slow versus yellow-rapid, separate misting from Fire harassment.',
      memory: '<strong>Cold Phlegm dims;</strong> <strong>Phlegm-Fire erupts.</strong>',
      safety: 'New confusion, reduced consciousness, psychosis, suicidal or violent behavior, seizure, fever or suspected intoxication is an emergency requiring immediate biomedical assessment.',
      prompts: ['Which pattern is withdrawn and dull?', 'Which is agitated and sleepless?', 'How do tongue coat and pulse separate them?']
    },
    es: {
      names: ['Flema que Nubla el Corazón', 'Flema-Fuego que Acosa el Corazón'],
      shared: 'Ambos obstruyen orificios de Corazón y causan confusión, habla/conducta anormal, opresión torácica y pulso resbaladizo.',
      fast: ['Fría/turbia y retraída: lentitud mental, letargo, depresión, murmullo, estupor, capa blanca grasosa y pulso resbaladizo-lento.', 'Caliente y agitada: insomnio, gritos, manía, violencia, sabor amargo, lengua roja con capa amarilla grasosa y pulso resbaladizo-rápido.'],
      rows: [
        ['Dirección', 'Nubla y suprime conciencia.', 'Agita e impulsa conducta hacia arriba/afuera.'],
        ['Temperatura', 'Generalmente Flema-Humedad fría o neutra.', 'Flema combinada con Fuego.'],
        ['Lengua / pulso', 'Hinchada con capa blanca grasosa; resbaladizo-lento.', 'Roja con capa amarilla grasosa; resbaladizo-rápido.'],
        ['Ejemplos de fórmulas', 'Di Tan Tang según presentación.', 'Wen Dan Tang o Huang Lian Wen Dan Tang según intensidad de Calor.']
      ],
      why: 'Ambos implican Flema y conducta alterada. Retraimiento frente a agitación y blanco-lento frente a amarillo-rápido los separan.',
      memory: '<strong>Flema fría apaga;</strong> <strong>Flema-Fuego estalla.</strong>',
      safety: 'Confusión nueva, conciencia reducida, psicosis, conducta suicida/violenta, convulsión, fiebre o intoxicación sospechada es emergencia biomédica inmediata.',
      prompts: ['¿Cuál es retraído y lento?', '¿Cuál agitado e insomne?', '¿Cómo separan capa y pulso?']
    }
  },
  {
    slug: 'spleen-qi-deficiency-vs-sinking-vs-not-holding-blood', title: 'Spleen Qi Deficiency vs. Sinking Qi vs. Qi Not Holding Blood', titleEs: 'Deficiencia de Qi de Bazo vs. Qi Hundido vs. Qi que no Contiene Sangre',
    summary: 'Start with the same Spleen-Qi root, then identify prolapse or bleeding as the decisive functional failure.',
    summaryEs: 'Parte de la misma raíz de Qi de Bazo y reconoce prolapso o sangrado como falla funcional decisiva.',
    search: 'spleen qi deficiency sinking qi not holding blood fatigue appetite loose stool prolapse bearing down bleeding bruising bu zhong yi qi gui pi',
    en: {
      names: ['Spleen Qi Deficiency', 'Sinking Spleen Qi', 'Spleen Qi Not Holding Blood'],
      shared: 'All share fatigue, poor appetite, loose stools, pale tongue and a weak pulse because Spleen Qi is deficient.',
      fast: ['Basic transformation failure: digestive weakness and fatigue without a defining downstream failure.', 'Upbearing failure: prolapse, bearing-down sensation, chronic diarrhea or worse symptoms on standing/exertion.', 'Containment failure: easy bruising, prolonged menses or other pale, slow bleeding.'],
      rows: [
        ['Primary failure', 'Transform and transport.', 'Raise and hold organs/tissues.', 'Keep Blood within the vessels.'],
        ['Decisive clue', 'Poor appetite and loose stool dominate.', 'Prolapse or downward dragging.', 'Chronic bleeding without strong Heat signs.'],
        ['Pulse tendency', 'Weak, especially right guan.', 'Weak and possibly deficient/faint.', 'Weak or thin with Blood loss.'],
        ['Formula examples', 'Si Jun Zi Tang.', 'Bu Zhong Yi Qi Tang.', 'Gui Pi Tang.']
      ],
      why: 'The root signs overlap. The exam asks what Spleen Qi fails to do: digest, lift or contain Blood.',
      memory: '<strong>Deficient Qi cannot transform;</strong> <strong>sinking Qi cannot lift;</strong> <strong>uncontained Qi cannot hold Blood.</strong>',
      safety: 'New prolapse, heavy or unexplained bleeding, black stool, fainting or signs of anemia requires medical evaluation.',
      prompts: ['Which pattern causes prolapse?', 'Which causes easy bruising?', 'What signs remain in uncomplicated Spleen-Qi deficiency?']
    },
    es: {
      names: ['Deficiencia de Qi de Bazo', 'Qi de Bazo Hundido', 'Qi de Bazo no Contiene Sangre'],
      shared: 'Todos comparten fatiga, poco apetito, heces sueltas, lengua pálida y pulso débil porque falta Qi de Bazo.',
      fast: ['Falla básica de transformación: debilidad digestiva y fatiga sin una falla secundaria definitoria.', 'Falla de elevación: prolapso, sensación de descenso, diarrea crónica o empeora al estar de pie/esforzarse.', 'Falla de contención: moretones fáciles, menstruación prolongada u otro sangrado pálido y lento.'],
      rows: [
        ['Falla principal', 'Transformar y transportar.', 'Elevar y sostener órganos/tejidos.', 'Mantener Sangre dentro de vasos.'],
        ['Pista decisiva', 'Dominan poco apetito y heces sueltas.', 'Prolapso o arrastre hacia abajo.', 'Sangrado crónico sin Calor fuerte.'],
        ['Pulso', 'Débil, especialmente guan derecho.', 'Débil y posiblemente deficiente/casi imperceptible.', 'Débil o fino con pérdida de Sangre.'],
        ['Ejemplos de fórmulas', 'Si Jun Zi Tang.', 'Bu Zhong Yi Qi Tang.', 'Gui Pi Tang.']
      ],
      why: 'La raíz se superpone. El examen pregunta qué no hace Qi de Bazo: digerir, elevar o contener Sangre.',
      memory: '<strong>Qi deficiente no transforma;</strong> <strong>Qi hundido no eleva;</strong> <strong>Qi sin contención no sostiene Sangre.</strong>',
      safety: 'Prolapso nuevo, sangrado abundante/inexplicado, heces negras, desmayo o anemia requiere evaluación médica.',
      prompts: ['¿Cuál causa prolapso?', '¿Cuál moretones?', '¿Qué queda en deficiencia simple de Qi de Bazo?']
    }
  },
  {
    slug: 'kidney-qi-not-firm-vs-not-grasping-qi', title: 'Kidney Qi Not Firm vs. Kidney Failing to Grasp Qi', titleEs: 'Qi de Riñón no Firme vs. Riñón que no Capta Qi',
    summary: 'Separate lower-orifice leakage from failure of the Kidneys to anchor inhaled Lung Qi.',
    summaryEs: 'Distingue fuga por orificios inferiores de falla renal para anclar el Qi inhalado por Pulmón.',
    search: 'kidney qi not firm failing grasp qi urinary frequency incontinence spermatorrhea vaginal discharge asthma dyspnea inhalation low back',
    en: {
      names: ['Kidney Qi Not Firm', 'Kidney Failing to Grasp Qi'],
      shared: 'Both arise from Kidney-Qi deficiency and may show weak low back/knees, fatigue, urinary weakness and a deep weak pulse.',
      fast: ['Lower gates leak: frequent clear urine, incontinence, enuresis, spermatorrhea or chronic vaginal discharge.', 'Respiration cannot descend: chronic wheezing or dyspnea worse on inhalation and exertion, with short breath and weak low back.'],
      rows: [
        ['Failed function', 'Consolidate Bladder, Essence and lower orifices.', 'Receive and anchor Lung Qi during inhalation.'],
        ['System emphasis', 'Urinary and reproductive leakage.', 'Respiratory weakness rooted below.'],
        ['Decisive clue', 'Loss or leakage.', 'Difficulty drawing breath in.'],
        ['Formula examples', 'Suo Quan Wan or Jin Suo Gu Jing Wan by leakage.', 'Ren Shen Ge Jie San or Su Zi Jiang Qi Tang by presentation.']
      ],
      why: 'Both are Kidney-Qi failures, but one loses through the lower gates while the other fails to pull Lung Qi downward.',
      memory: '<strong>Not firm leaks below;</strong> <strong>not grasping loses the inhale.</strong>',
      safety: 'New incontinence, urinary retention, saddle numbness, severe breathing difficulty, blue lips or rapidly worsening asthma requires urgent assessment.',
      prompts: ['Which pattern causes enuresis?', 'Which makes inhalation difficult?', 'What Kidney signs can both share?']
    },
    es: {
      names: ['Qi de Riñón no Firme', 'Riñón no Capta Qi'],
      shared: 'Ambos nacen de deficiencia de Qi renal y muestran lumbar/rodillas débiles, fatiga, debilidad urinaria y pulso profundo-débil.',
      fast: ['Compuertas inferiores pierden: orina clara frecuente, incontinencia, enuresis, espermatorrea o flujo vaginal crónico.', 'Respiración no desciende: sibilancia/disnea crónica peor al inhalar y con esfuerzo, respiración corta y lumbar débil.'],
      rows: [
        ['Función fallida', 'Consolidar Vejiga, Esencia y orificios inferiores.', 'Recibir y anclar Qi de Pulmón al inhalar.'],
        ['Sistema', 'Fuga urinaria y reproductiva.', 'Debilidad respiratoria arraigada abajo.'],
        ['Pista decisiva', 'Pérdida o fuga.', 'Dificultad para inspirar.'],
        ['Ejemplos de fórmulas', 'Suo Quan Wan o Jin Suo Gu Jing Wan según fuga.', 'Ren Shen Ge Jie San o Su Zi Jiang Qi Tang según presentación.']
      ],
      why: 'Ambos son fallas de Qi renal, pero uno pierde por compuertas inferiores y otro no atrae Qi pulmonar hacia abajo.',
      memory: '<strong>No firme pierde abajo;</strong> <strong>no captar pierde la inhalación.</strong>',
      safety: 'Incontinencia nueva, retención urinaria, anestesia en silla de montar, disnea grave, labios azules o asma progresiva requiere urgencias.',
      prompts: ['¿Cuál causa enuresis?', '¿Cuál dificulta inspirar?', '¿Qué signos renales comparten?']
    }
  },
  {
    slug: 'lung-heat-vs-lung-yin-deficiency', title: 'Lung Heat vs. Lung Yin Deficiency', titleEs: 'Calor de Pulmón vs. Deficiencia de Yin de Pulmón',
    summary: 'Separate an excess hot productive cough from a chronic dry deficient cough with Empty Heat.',
    summaryEs: 'Distingue tos productiva caliente por exceso de tos seca crónica por deficiencia con Calor Vacío.',
    search: 'lung heat yin deficiency cough yellow sputum dry cough scant sputum fever thirst night sweat hoarse voice red tongue yellow coat peeled thin rapid',
    en: {
      names: ['Lung Heat', 'Lung Yin Deficiency'],
      shared: 'Both can cause cough, thirst, dry throat, red tongue signs and a rapid pulse.',
      fast: ['Excess Heat: loud cough, thick yellow sputum, fever, thirst, chest pain and a red tongue with yellow coat.', 'Deficient Yin: chronic dry weak cough, scant sticky sputum, hoarseness, afternoon Heat, night sweats and a red peeled tongue.'],
      rows: [
        ['Sputum', 'Thick, yellow and difficult or profuse.', 'Scant, sticky or absent; possibly blood-streaked from dryness.'],
        ['Systemic sign', 'Acute fever and strong thirst.', 'Chronic dryness and Empty Heat.'],
        ['Tongue / pulse', 'Red with yellow coat; rapid and forceful/slippery.', 'Red, dry, little coat; thin-rapid.'],
        ['Formula examples', 'Ma Xing Shi Gan Tang or Qing Qi Hua Tan Wan.', 'Bai He Gu Jin Tang or Sha Shen Mai Dong Tang.']
      ],
      why: 'Both are hot Lung patterns. Sputum abundance, disease timing, tongue coat and pulse strength distinguish excess Heat from depleted Lung Yin.',
      memory: '<strong>Lung Heat cooks yellow Phlegm;</strong> <strong>Lung Yin deficiency dries the cough.</strong>',
      safety: 'Breathing difficulty, chest pain, coughing blood, low oxygen, high fever or cough lasting more than three weeks needs medical assessment.',
      prompts: ['Which produces thick yellow sputum?', 'Which causes hoarseness and night sweats?', 'How does the tongue coat differ?']
    },
    es: {
      names: ['Calor de Pulmón', 'Deficiencia de Yin de Pulmón'],
      shared: 'Ambos causan tos, sed, garganta seca, lengua roja y pulso rápido.',
      fast: ['Calor por exceso: tos fuerte, esputo amarillo espeso, fiebre, sed, dolor torácico y lengua roja con capa amarilla.', 'Yin deficiente: tos seca débil crónica, esputo escaso pegajoso, ronquera, Calor vespertino, sudor nocturno y lengua roja pelada.'],
      rows: [
        ['Esputo', 'Espeso, amarillo y difícil o abundante.', 'Escaso, pegajoso o ausente; posiblemente con sangre por sequedad.'],
        ['Signo sistémico', 'Fiebre aguda y sed fuerte.', 'Sequedad crónica y Calor Vacío.'],
        ['Lengua / pulso', 'Roja con capa amarilla; rápido y fuerte/resbaladizo.', 'Roja, seca, poca capa; fino-rápido.'],
        ['Ejemplos de fórmulas', 'Ma Xing Shi Gan Tang o Qing Qi Hua Tan Wan.', 'Bai He Gu Jin Tang o Sha Shen Mai Dong Tang.']
      ],
      why: 'Ambos son patterns calientes de Pulmón. Esputo, tiempo, capa y fuerza del pulso separan exceso de Yin agotado.',
      memory: '<strong>Calor de Pulmón cocina Flema amarilla;</strong> <strong>Yin deficiente seca la tos.</strong>',
      safety: 'Disnea, dolor torácico, sangre al toser, oxígeno bajo, fiebre alta o tos mayor de tres semanas necesita evaluación.',
      prompts: ['¿Cuál produce esputo amarillo espeso?', '¿Cuál ronquera y sudor nocturno?', '¿Cómo difiere la capa?']
    }
  },
  {
    slug: 'heart-fire-vs-heart-yin-deficiency', title: 'Heart Fire vs. Heart Yin Deficiency', titleEs: 'Fuego de Corazón vs. Deficiencia de Yin de Corazón',
    summary: 'Separate forceful full Fire from deficient cooling that leaves the Spirit restless at night.',
    summaryEs: 'Distingue Fuego pleno y fuerte de enfriamiento deficiente que deja Shen inquieto por la noche.',
    search: 'heart fire yin deficiency insomnia palpitations agitation mouth tongue ulcers bitter taste night sweat five center red tip yellow coat peeled forceful thin rapid',
    en: {
      names: ['Heart Fire', 'Heart Yin Deficiency'],
      shared: 'Both disturb the Spirit and may cause insomnia, palpitations, anxiety, restlessness, a red tongue tip and rapid pulse.',
      fast: ['Full Fire: intense agitation, mouth/tongue ulcers, bitter taste, dark urine and a red tongue with yellow coat; pulse forceful-rapid.', 'Yin deficiency: chronic insomnia, night sweating, five-center Heat, dry mouth and a red peeled tongue; pulse thin-rapid.'],
      rows: [
        ['Mechanism', 'Excess Fire agitates Heart and Spirit.', 'Heart Yin cannot cool and anchor the Spirit.'],
        ['Mouth / urine', 'Painful ulcers and dark scant urine.', 'Dryness without strong ulcerative or urinary Fire.'],
        ['Tongue / pulse', 'Red tip/body with yellow coat; overflowing/forceful-rapid.', 'Red tip, little coat/cracks; thin-rapid.'],
        ['Formula examples', 'Dao Chi San or Huang Lian Jie Du Tang by severity.', 'Tian Wang Bu Xin Dan.']
      ],
      why: 'Insomnia and red tip overlap. Ulcers, dark urine, coating and pulse force indicate Fire; night Heat, dryness and no coat indicate Yin deficiency.',
      memory: '<strong>Heart Fire flares and sores;</strong> <strong>Heart Yin deficiency dries and wakes.</strong>',
      safety: 'Chest pain, fainting, sustained rapid/irregular heartbeat, mania, suicidality or severe sleep deprivation requires urgent medical assessment.',
      prompts: ['Which pattern produces tongue ulcers?', 'Which has night sweats?', 'How does pulse strength distinguish them?']
    },
    es: {
      names: ['Fuego de Corazón', 'Deficiencia de Yin de Corazón'],
      shared: 'Ambos perturban Shen y causan insomnio, palpitaciones, ansiedad, inquietud, punta roja y pulso rápido.',
      fast: ['Fuego pleno: agitación intensa, úlceras de boca/lengua, sabor amargo, orina oscura y lengua roja con capa amarilla; pulso fuerte-rápido.', 'Yin deficiente: insomnio crónico, sudor nocturno, Calor de cinco centros, boca seca y lengua roja pelada; pulso fino-rápido.'],
      rows: [
        ['Mecanismo', 'Fuego excesivo agita Corazón y Shen.', 'Yin de Corazón no enfría ni ancla Shen.'],
        ['Boca / orina', 'Úlceras dolorosas y orina oscura escasa.', 'Sequedad sin fuerte Fuego ulcerativo o urinario.'],
        ['Lengua / pulso', 'Punta/cuerpo rojo con capa amarilla; desbordante/fuerte-rápido.', 'Punta roja, poca capa/grietas; fino-rápido.'],
        ['Ejemplos de fórmulas', 'Dao Chi San o Huang Lian Jie Du Tang según gravedad.', 'Tian Wang Bu Xin Dan.']
      ],
      why: 'Insomnio y punta roja se superponen. Úlceras, orina oscura, capa y fuerza indican Fuego; Calor nocturno, sequedad y sin capa indican Yin.',
      memory: '<strong>Fuego de Corazón arde y ulcera;</strong> <strong>Yin deficiente seca y despierta.</strong>',
      safety: 'Dolor torácico, desmayo, ritmo rápido/irregular persistente, manía, suicidio o privación grave de sueño requiere evaluación urgente.',
      prompts: ['¿Cuál produce úlceras linguales?', '¿Cuál sudor nocturno?', '¿Cómo distingue la fuerza del pulso?']
    }
  },
  {
    slug: 'dampness-vs-phlegm', title: 'Dampness vs. Phlegm', titleEs: 'Humedad vs. Flema',
    summary: 'Separate diffuse heavy fluid dysfunction from condensed, visible orifice-obstructing Phlegm.',
    summaryEs: 'Distingue disfunción difusa y pesada de líquidos de Flema condensada, visible u obstructora de orificios.',
    search: 'dampness phlegm heaviness fullness edema loose stool nausea sputum nodules dizziness orifices greasy tongue soggy slippery ping wei er chen',
    en: {
      names: ['Dampness', 'Phlegm'],
      shared: 'Both result from impaired fluid transformation and may cause heaviness, chest/epigastric fullness, nausea, a greasy coat and slippery qualities.',
      fast: ['Diffuse and heavy: body heaviness, edema, loose stool, cloudy discharge, poor appetite and a soggy/slippery pulse.', 'Condensed and obstructive: sputum, nodules, globus, dizziness, altered consciousness or a distinctly slippery pulse.'],
      rows: [
        ['Physical idea', 'Wateriness and diffuse accumulation.', 'Condensed pathological fluid.'],
        ['Typical location', 'Middle Jiao, flesh, joints and lower burner.', 'Lung, throat, channels or sensory orifices.'],
        ['Decisive clue', 'Heaviness, swelling and loose stool.', 'Sputum, mass, vertigo or orifice obstruction.'],
        ['Formula examples', 'Ping Wei San or Shen Ling Bai Zhu San by excess/deficiency.', 'Er Chen Tang or Di Tan Tang by location.']
      ],
      why: 'Phlegm is often described as a more condensed development of Dampness. Visible sputum is not required when nodules, vertigo or orifice signs show hidden Phlegm.',
      memory: '<strong>Dampness spreads and weighs;</strong> <strong>Phlegm condenses and blocks.</strong>',
      safety: 'Sudden confusion, focal neurologic deficit, severe vertigo, breathing difficulty, coughing blood or a new neck mass requires biomedical evaluation.',
      prompts: ['Which produces diffuse heaviness?', 'Which produces nodules or altered consciousness?', 'How can Phlegm exist without sputum?']
    },
    es: {
      names: ['Humedad', 'Flema'],
      shared: 'Ambas resultan de transformación alterada de líquidos y causan pesadez, plenitud, náusea, capa grasosa y cualidad resbaladiza.',
      fast: ['Difusa y pesada: pesadez corporal, edema, heces sueltas, secreción turbia, poco apetito y pulso blando/resbaladizo.', 'Condensada y obstructiva: esputo, nódulos, globo faríngeo, vértigo, conciencia alterada o pulso claramente resbaladizo.'],
      rows: [
        ['Idea física', 'Acumulación acuosa y difusa.', 'Líquido patológico condensado.'],
        ['Localización', 'Jiao Medio, carne, articulaciones y Jiao Inferior.', 'Pulmón, garganta, canales u orificios sensoriales.'],
        ['Pista decisiva', 'Pesadez, hinchazón y heces sueltas.', 'Esputo, masa, vértigo u obstrucción de orificios.'],
        ['Ejemplos de fórmulas', 'Ping Wei San o Shen Ling Bai Zhu San según exceso/deficiencia.', 'Er Chen Tang o Di Tan Tang según localización.']
      ],
      why: 'Flema suele ser desarrollo más condensado de Humedad. No exige esputo visible si nódulos, vértigo u orificios muestran Flema oculta.',
      memory: '<strong>Humedad se extiende y pesa;</strong> <strong>Flema se condensa y bloquea.</strong>',
      safety: 'Confusión súbita, déficit neurológico, vértigo grave, disnea, sangre al toser o masa nueva en cuello requiere evaluación biomédica.',
      prompts: ['¿Cuál produce pesadez difusa?', '¿Cuál nódulos o conciencia alterada?', '¿Cómo existe Flema sin esputo?']
    }
  },
  {
    slug: 'internal-wind-extreme-heat-vs-yang-vs-blood-deficiency', title: 'Internal Wind: Extreme Heat vs. Liver Yang vs. Blood Deficiency', titleEs: 'Viento Interno: Calor Extremo vs. Yang de Hígado vs. Deficiencia de Sangre',
    summary: 'Distinguish acute febrile convulsions, chronic rising-Yang Wind and fine deficiency tremor.',
    summaryEs: 'Distingue convulsiones febriles agudas, Viento crónico por Yang ascendente y temblor fino por deficiencia.',
    search: 'internal wind extreme heat liver yang blood deficiency convulsions high fever tremor dizziness headache numbness cramps red pale tongue wiry rapid thin',
    en: {
      names: ['Wind from Extreme Heat', 'Wind from Liver Yang Rising', 'Wind from Blood Deficiency'],
      shared: 'All can produce tremor, spasm, dizziness or abnormal movement because the sinews lose stable nourishment and control.',
      fast: ['Acute emergency: very high fever, delirium, rigid neck and convulsions with a crimson tongue and rapid forceful pulse.', 'Upward chronic excess-root/deficiency-root: severe dizziness, headache, tinnitus, irritability and coarse tremor with wiry pulse.', 'Deficient nourishment: fine tremor, numbness, cramps, pale complexion/tongue and thin pulse.'],
      rows: [
        ['Onset', 'Sudden during severe febrile disease.', 'Usually chronic or episodic with Liver-Yang signs.', 'Gradual with chronic Blood deficiency.'],
        ['Movement', 'Convulsion and marked rigidity.', 'Coarser tremor, dizziness or potential sudden upward event.', 'Fine tremor, twitching and cramps.'],
        ['Tongue / pulse', 'Crimson; rapid-forceful.', 'Red edges; wiry, often rapid.', 'Pale; thin/choppy.'],
        ['Formula examples', 'Ling Jiao Gou Teng Tang historically; endangered animal ingredients require substitution.', 'Tian Ma Gou Teng Yin.', 'Si Wu Tang with Wind-extinguishing support by presentation.']
      ],
      why: 'Movement alone is nonspecific. Fever severity, upward Yang signs or pale deficiency signs identify the source of Wind.',
      memory: '<strong>Heat convulses;</strong> <strong>Yang rises;</strong> <strong>Blood-starved sinews flutter.</strong>',
      safety: 'Any first seizure, seizure lasting 5 minutes, sudden weakness/speech change, severe new headache, high fever with stiff neck or altered consciousness is an emergency.',
      prompts: ['Which Wind has high fever?', 'Which has headache and tinnitus?', 'Which produces fine tremor with pallor?']
    },
    es: {
      names: ['Viento por Calor Extremo', 'Viento por Ascenso de Yang de Hígado', 'Viento por Deficiencia de Sangre'],
      shared: 'Todos causan temblor, espasmo, mareo o movimiento anormal porque tendones pierden nutrición/control estable.',
      fast: ['Emergencia aguda: fiebre muy alta, delirio, nuca rígida y convulsiones con lengua carmesí y pulso rápido-fuerte.', 'Ascenso crónico: mareo grave, cefalea, tinnitus, irritabilidad y temblor grueso con pulso de cuerda.', 'Nutrición deficiente: temblor fino, entumecimiento, calambres, tez/lengua pálida y pulso fino.'],
      rows: [
        ['Inicio', 'Súbito durante enfermedad febril grave.', 'Crónico o episódico con signos de Yang de Hígado.', 'Gradual con deficiencia crónica de Sangre.'],
        ['Movimiento', 'Convulsión y rigidez marcada.', 'Temblor grueso, mareo o posible evento ascendente súbito.', 'Temblor fino, fasciculación y calambres.'],
        ['Lengua / pulso', 'Carmesí; rápido-fuerte.', 'Bordes rojos; de cuerda, a menudo rápido.', 'Pálida; fino/áspero.'],
        ['Ejemplos de fórmulas', 'Ling Jiao Gou Teng Tang histórica; ingredientes animales amenazados exigen sustitución.', 'Tian Ma Gou Teng Yin.', 'Si Wu Tang con apoyo que extingue Viento según presentación.']
      ],
      why: 'Movimiento solo no basta. Gravedad febril, signos de Yang ascendente o palidez deficiente identifican origen del Viento.',
      memory: '<strong>Calor convulsiona;</strong> <strong>Yang asciende;</strong> <strong>tendones sin Sangre aletean.</strong>',
      safety: 'Primera convulsión, duración de 5 minutos, debilidad/habla súbita, cefalea nueva grave, fiebre alta con nuca rígida o conciencia alterada es emergencia.',
      prompts: ['¿Qué Viento tiene fiebre alta?', '¿Cuál cefalea y tinnitus?', '¿Cuál temblor fino con palidez?']
    }
  },
  {
    slug: 'spleen-yang-deficiency-vs-kidney-yang-deficiency', title: 'Spleen Yang Deficiency vs. Kidney Yang Deficiency', titleEs: 'Deficiencia de Yang de Bazo vs. Deficiencia de Yang de Riñón',
    summary: 'Separate failure to warm digestion from deeper Mingmen, urinary, reproductive and water-transformation weakness.',
    summaryEs: 'Distingue falla para calentar digestión de debilidad más profunda de Mingmen, orina, reproducción y transformación de agua.',
    search: 'spleen kidney yang deficiency cold digestion loose stool undigested edema urine low back knees reproductive dawn diarrhea pale swollen deep slow weak',
    en: {
      names: ['Spleen Yang Deficiency', 'Kidney Yang Deficiency'],
      shared: 'Both cause internal Cold, fatigue, cold limbs, edema, pale swollen wet tongue and a deep slow weak pulse.',
      fast: ['Digestive center fails: poor appetite, cold abdominal pain, loose stool with undigested food and edema from weak transport.', 'Mingmen and lower burner fail: weak low back/knees, dawn diarrhea, frequent clear urine, edema, reproductive weakness and pronounced cold.'],
      rows: [
        ['Primary sphere', 'Digestion and Spleen fluid transport.', 'Urinary, reproductive, Essence and whole-body warming.'],
        ['Decisive clue', 'Cold digestive symptoms dominate.', 'Low back/knee, urinary or reproductive signs dominate.'],
        ['Relationship', 'Can progress into Kidney Yang deficiency.', 'Often includes Spleen Yang weakness when advanced.'],
        ['Formula examples', 'Li Zhong Wan.', 'Jin Gui Shen Qi Wan, You Gui Wan or Zhen Wu Tang by presentation.']
      ],
      why: 'Cold and edema overlap. Location and depth decide: Middle-Jiao digestion points to Spleen; lower burner, Essence and Mingmen point to Kidney.',
      memory: '<strong>Spleen Yang warms the pot;</strong> <strong>Kidney Yang powers the furnace and water gate.</strong>',
      safety: 'Rapidly worsening edema, shortness of breath, very low urine output, severe weakness or hypothermia requires urgent medical evaluation.',
      prompts: ['Which pattern has undigested food?', 'Which has dawn diarrhea?', 'Which organ signs show the deeper deficiency?']
    },
    es: {
      names: ['Deficiencia de Yang de Bazo', 'Deficiencia de Yang de Riñón'],
      shared: 'Ambas causan Frío interno, fatiga, miembros fríos, edema, lengua pálida-hinchada-mojada y pulso profundo-lento-débil.',
      fast: ['Falla el centro digestivo: poco apetito, dolor abdominal frío, heces sueltas con alimento no digerido y edema por transporte débil.', 'Fallan Mingmen y Jiao Inferior: lumbar/rodillas débiles, diarrea al amanecer, orina clara frecuente, edema, debilidad reproductiva y Frío marcado.'],
      rows: [
        ['Esfera principal', 'Digestión y transporte de líquidos de Bazo.', 'Orina, reproducción, Esencia y calentamiento corporal.'],
        ['Pista decisiva', 'Dominan signos digestivos fríos.', 'Dominan lumbar/rodillas, orina o reproducción.'],
        ['Relación', 'Puede progresar a deficiencia Yang renal.', 'Avanzada suele incluir Yang de Bazo débil.'],
        ['Ejemplos de fórmulas', 'Li Zhong Wan.', 'Jin Gui Shen Qi Wan, You Gui Wan o Zhen Wu Tang según presentación.']
      ],
      why: 'Frío y edema se superponen. Lugar/profundidad deciden: digestión del Centro es Bazo; Jiao Inferior, Esencia y Mingmen es Riñón.',
      memory: '<strong>Yang de Bazo calienta la olla;</strong> <strong>Yang de Riñón alimenta horno y compuerta de agua.</strong>',
      safety: 'Edema rápidamente progresivo, disnea, orina muy escasa, debilidad grave o hipotermia requiere evaluación urgente.',
      prompts: ['¿Cuál tiene alimento no digerido?', '¿Cuál diarrea al amanecer?', '¿Qué signos muestran deficiencia profunda?']
    }
  },
  {
    slug: 'liver-yin-deficiency-vs-kidney-yin-deficiency', title: 'Liver Yin Deficiency vs. Kidney Yin Deficiency', titleEs: 'Deficiencia de Yin de Hígado vs. Deficiencia de Yin de Riñón',
    summary: 'Separate eye-sinew-menstrual undernourishment from low-back, ear, Essence and bone depletion.',
    summaryEs: 'Distingue desnutrición de ojos-tendones-menstruación de agotamiento lumbar, auditivo, de Esencia y huesos.',
    search: 'liver kidney yin deficiency eyes dry blurred vision tendons cramps menses low back knees tinnitus hearing essence bones night sweat five center heat',
    en: {
      names: ['Liver Yin Deficiency', 'Kidney Yin Deficiency'],
      shared: 'Both cause dryness, dizziness, tinnitus, night sweating, five-center Heat, red peeled tongue and a thin rapid pulse; they often coexist.',
      fast: ['Liver emphasis: dry blurred eyes, night blindness, cramps, tremor, brittle nails and scanty menstruation.', 'Kidney emphasis: weak low back/knees, stronger tinnitus or hearing loss, reproductive/Essence weakness and developmental or bone signs.'],
      rows: [
        ['Nourished tissues', 'Eyes, sinews, nails and menstrual Blood.', 'Bones, marrow, brain, ears and reproductive Essence.'],
        ['Decisive location', 'Visual and sinew symptoms.', 'Low back/knees, ears and Essence symptoms.'],
        ['Common progression', 'May allow Liver Yang to rise.', 'Can fail to nourish Liver Yin and underlie the combined pattern.'],
        ['Formula examples', 'Yi Guan Jian by presentation.', 'Liu Wei Di Huang Wan or Zuo Gui Wan by depth.']
      ],
      why: 'Empty Heat is shared and cannot localize the diagnosis. The affected sensory organ and tissue system identify Liver versus Kidney emphasis.',
      memory: '<strong>Liver Yin moistens eyes and sinews;</strong> <strong>Kidney Yin fills ears, bones and Essence.</strong>',
      safety: 'Sudden vision/hearing loss, severe headache, neurologic deficit, unexplained bleeding or significant weight loss requires medical assessment.',
      prompts: ['Which pattern emphasizes dry eyes?', 'Which emphasizes weak low back and hearing?', 'Why do the two often coexist?']
    },
    es: {
      names: ['Deficiencia de Yin de Hígado', 'Deficiencia de Yin de Riñón'],
      shared: 'Ambas causan sequedad, mareo, tinnitus, sudor nocturno, Calor de cinco centros, lengua roja pelada y pulso fino-rápido; suelen coexistir.',
      fast: ['Énfasis Hígado: ojos secos/borrosos, ceguera nocturna, calambres, temblor, uñas frágiles y menstruación escasa.', 'Énfasis Riñón: lumbar/rodillas débiles, tinnitus o pérdida auditiva más fuerte, debilidad de Esencia/reproducción y signos óseos.'],
      rows: [
        ['Tejidos nutridos', 'Ojos, tendones, uñas y Sangre menstrual.', 'Huesos, médula, cerebro, oídos y Esencia reproductiva.'],
        ['Localización decisiva', 'Síntomas visuales y tendinosos.', 'Lumbar/rodillas, oídos y Esencia.'],
        ['Progresión', 'Puede permitir ascenso de Yang de Hígado.', 'Puede dejar de nutrir Yin de Hígado y sostener pattern combinado.'],
        ['Ejemplos de fórmulas', 'Yi Guan Jian según presentación.', 'Liu Wei Di Huang Wan o Zuo Gui Wan según profundidad.']
      ],
      why: 'Calor Vacío es común y no localiza. Órgano sensorial y tejido afectado identifican énfasis de Hígado o Riñón.',
      memory: '<strong>Yin de Hígado humedece ojos y tendones;</strong> <strong>Yin de Riñón llena oídos, huesos y Esencia.</strong>',
      safety: 'Pérdida súbita visual/auditiva, cefalea grave, déficit neurológico, sangrado inexplicado o pérdida de peso requiere evaluación.',
      prompts: ['¿Cuál enfatiza ojos secos?', '¿Cuál lumbar y audición?', '¿Por qué suelen coexistir?']
    }
  },
  {
    slug: 'four-bi-syndrome-patterns', title: 'Four Bi-Syndrome Patterns', titleEs: 'Cuatro Patterns de Síndrome Bi',
    summary: 'Distinguish Wind, Cold, Damp and Heat painful obstruction by movement, severity, heaviness and inflammation.',
    summaryEs: 'Distingue obstrucción dolorosa por Viento, Frío, Humedad y Calor mediante movilidad, intensidad, pesadez e inflamación.',
    search: 'bi syndrome wind moving xing cold painful tong damp fixed zhuo heat hot painful joints wandering severe swelling numbness red',
    en: {
      names: ['Wind Bi (Xing Bi)', 'Cold Bi (Tong Bi)', 'Damp Bi (Zhuo Bi)', 'Heat Bi (Re Bi)'],
      shared: 'All obstruct channels and joints, causing pain, stiffness or limited movement.',
      fast: ['Pain wanders from joint to joint.', 'Severe fixed pain worsened by cold and improved by warmth.', 'Fixed heavy pain with swelling and numbness.', 'Red, hot, swollen and acutely painful joints.'],
      rows: [
        ['Dominant quality', 'Movement and variability.', 'Contraction and severe pain.', 'Heaviness and lingering swelling.', 'Inflammation and burning Heat.'],
        ['Weather clue', 'Often changes rapidly with exposure.', 'Clearly worse in cold weather.', 'Worse in damp weather.', 'Worse with Heat; warmth may aggravate.'],
        ['Tongue / pulse', 'Thin coat; floating/wiry.', 'White coat; tight/slow.', 'Greasy coat; soggy/slippery.', 'Red with yellow coat; rapid/slippery.'],
        ['Formula examples', 'Fang Feng Tang.', 'Wu Tou Tang—expert use due to aconite.', 'Yi Yi Ren Tang.', 'Bai Hu Jia Gui Zhi Tang or Xuan Bi Tang.']
      ],
      why: 'Pain location alone is weak evidence. Wandering, severity/cold response, heaviness/numbness or red Heat identifies the dominant pathogen.',
      memory: '<strong>Wind moves;</strong> <strong>Cold hurts;</strong> <strong>Damp weighs;</strong> <strong>Heat reddens.</strong>',
      safety: 'A single acutely red-hot joint with fever, inability to bear weight, major trauma, calf swelling or neurologic weakness requires urgent evaluation. Aconite formulas are not for self-treatment.',
      prompts: ['Which Bi wanders?', 'Which produces severe cold-sensitive pain?', 'Which is heavy and numb?', 'Which is red and hot?']
    },
    es: {
      names: ['Bi de Viento (Xing Bi)', 'Bi de Frío (Tong Bi)', 'Bi de Humedad (Zhuo Bi)', 'Bi de Calor (Re Bi)'],
      shared: 'Todos obstruyen canales y articulaciones, causando dolor, rigidez o movimiento limitado.',
      fast: ['Dolor migra de articulación en articulación.', 'Dolor fijo grave peor con frío y mejor con calor.', 'Dolor fijo pesado con hinchazón y entumecimiento.', 'Articulaciones rojas, calientes, hinchadas y dolorosas agudamente.'],
      rows: [
        ['Cualidad dominante', 'Movimiento y variabilidad.', 'Contracción y dolor grave.', 'Pesadez e hinchazón persistente.', 'Inflamación y Calor ardiente.'],
        ['Clima', 'Cambia rápido con exposición.', 'Claramente peor en clima frío.', 'Peor en clima húmedo.', 'Peor con Calor; calor externo puede agravar.'],
        ['Lengua / pulso', 'Capa fina; flotante/de cuerda.', 'Capa blanca; tenso/lento.', 'Capa grasosa; blando/resbaladizo.', 'Roja con capa amarilla; rápido/resbaladizo.'],
        ['Ejemplos de fórmulas', 'Fang Feng Tang.', 'Wu Tou Tang—uso experto por aconito.', 'Yi Yi Ren Tang.', 'Bai Hu Jia Gui Zhi Tang o Xuan Bi Tang.']
      ],
      why: 'Localización sola aporta poco. Migración, gravedad/respuesta al frío, pesadez o rojez identifican patógeno dominante.',
      memory: '<strong>Viento se mueve;</strong> <strong>Frío duele;</strong> <strong>Humedad pesa;</strong> <strong>Calor enrojece.</strong>',
      safety: 'Una articulación rojo-caliente con fiebre, incapacidad de apoyar, trauma, pantorrilla hinchada o debilidad neurológica requiere urgencia. Aconito no es autocuidado.',
      prompts: ['¿Qué Bi migra?', '¿Cuál duele más con frío?', '¿Cuál pesa y entumece?', '¿Cuál es rojo/caliente?']
    }
  },
  {
    slug: 'wei-qi-ying-xue-levels', title: 'Wei, Qi, Ying and Xue Levels', titleEs: 'Niveles Wei, Qi, Ying y Xue',
    summary: 'Track warm disease from superficial defensive signs to blazing Qi Heat, nutritive disturbance and Blood-level bleeding or Wind.',
    summaryEs: 'Sigue enfermedad tibia desde defensa superficial hasta Calor Qi, alteración nutritiva y sangrado o Viento del nivel Sangre.',
    search: 'wei qi ying xue levels warm disease fever chills thirst sweat night fever delirium rash bleeding convulsions tongue pulse yin qiao bai hu qing ying xi jiao di huang',
    en: {
      names: ['Wei Level', 'Qi Level', 'Ying Level', 'Xue Level'],
      shared: 'All describe depth and severity in warm-febrile disease; progression inward generally means stronger Heat and greater fluid, Spirit or Blood injury.',
      fast: ['Exterior fever with mild chills, sore throat and floating-rapid pulse.', 'High fever, sweating, strong thirst and forceful-rapid pulse without chills.', 'Fever worse at night, restlessness/delirium and faint rash with crimson tongue.', 'Bleeding, dense purple rash, severe delirium or convulsions with crimson tongue.'],
      rows: [
        ['Main site', 'Defensive Exterior and Lung.', 'Interior Qi and organs.', 'Nutritive level and Pericardium/Spirit.', 'Blood level.'],
        ['Decisive clue', 'Chills still accompany fever.', 'Four greats: fever, sweat, thirst, pulse.', 'Night fever and early Spirit disturbance.', 'Bleeding, macules and stirring Wind.'],
        ['Tongue / pulse', 'Red tip/edges; floating-rapid.', 'Red with yellow coat; flooding/rapid.', 'Crimson, little coat; thin-rapid.', 'Deep crimson/purple; rapid, possibly wiry.'],
        ['Formula examples', 'Yin Qiao San.', 'Bai Hu Tang.', 'Qing Ying Tang.', 'Xi Jiao Di Huang Tang with modern Shui Niu Jiao substitution.']
      ],
      why: 'Fever is shared. Chills, thirst/sweat, night Spirit change, then bleeding/convulsion mark progressively deeper levels.',
      memory: '<strong>Wei defends;</strong> <strong>Qi blazes;</strong> <strong>Ying disturbs Shen;</strong> <strong>Xue bleeds and stirs Wind.</strong>',
      safety: 'This framework never replaces urgent infectious evaluation. High fever with confusion, stiff neck, breathing difficulty, non-blanching rash, bleeding or seizure is an emergency. Rhinoceros horn is prohibited/obsolete.',
      prompts: ['Which level retains chills?', 'Which has the four greats?', 'Which first disturbs Shen?', 'Which causes bleeding and convulsions?']
    },
    es: {
      names: ['Nivel Wei', 'Nivel Qi', 'Nivel Ying', 'Nivel Xue'],
      shared: 'Todos describen profundidad/gravedad de enfermedad tibia; progresión interna implica más Calor y lesión de líquidos, Shen o Sangre.',
      fast: ['Fiebre Exterior con escalofríos leves, garganta dolorosa y pulso flotante-rápido.', 'Fiebre alta, sudor, sed fuerte y pulso fuerte-rápido sin escalofríos.', 'Fiebre peor de noche, inquietud/delirio y rash tenue con lengua carmesí.', 'Sangrado, rash púrpura denso, delirio grave o convulsiones con lengua carmesí.'],
      rows: [
        ['Sitio', 'Exterior defensivo y Pulmón.', 'Qi Interior y órganos.', 'Nivel nutritivo y Pericardio/Shen.', 'Nivel Sangre.'],
        ['Pista decisiva', 'Escalofríos aún acompañan fiebre.', 'Cuatro grandes: fiebre, sudor, sed, pulso.', 'Fiebre nocturna y alteración temprana de Shen.', 'Sangrado, máculas y Viento.'],
        ['Lengua / pulso', 'Punta/bordes rojos; flotante-rápido.', 'Roja con capa amarilla; desbordante-rápido.', 'Carmesí, poca capa; fino-rápido.', 'Carmesí profunda/púrpura; rápido, posible cuerda.'],
        ['Ejemplos de fórmulas', 'Yin Qiao San.', 'Bai Hu Tang.', 'Qing Ying Tang.', 'Xi Jiao Di Huang Tang con sustitución moderna Shui Niu Jiao.']
      ],
      why: 'Fiebre es común. Escalofríos, sed/sudor, cambio nocturno de Shen y luego sangrado/convulsión marcan profundidad.',
      memory: '<strong>Wei defiende;</strong> <strong>Qi arde;</strong> <strong>Ying perturba Shen;</strong> <strong>Xue sangra y mueve Viento.</strong>',
      safety: 'Este marco no sustituye evaluación infecciosa urgente. Fiebre alta con confusión, nuca rígida, disnea, rash no blanqueable, sangrado o convulsión es emergencia. Cuerno de rinoceronte está prohibido/obsoleto.',
      prompts: ['¿Qué nivel conserva escalofríos?', '¿Cuál tiene cuatro grandes?', '¿Cuál primero altera Shen?', '¿Cuál sangra/convulsiona?']
    }
  },
  {
    slug: 'three-yang-stages', title: 'Taiyang vs. Yangming vs. Shaoyang', titleEs: 'Taiyang vs. Yangming vs. Shaoyang',
    summary: 'Separate an Exterior Taiyang battle, an Interior Yangming Heat excess and a pivot-stage Shaoyang disorder.',
    summaryEs: 'Distingue batalla Exterior Taiyang, exceso de Calor Interior Yangming y trastorno de bisagra Shaoyang.',
    search: 'three yang stages taiyang yangming shaoyang fever chills neck occipital sweating thirst constipation alternating bitter taste rib side fullness floating flooding wiry',
    en: {
      names: ['Taiyang', 'Yangming', 'Shaoyang'],
      shared: 'All are Yang-stage presentations with fever, but location, fever-chill relationship and pulse identify the stage.',
      fast: ['Exterior: fever/chills, occipital headache, stiff neck and floating pulse; sweat status separates excess from deficiency.', 'Interior: high fever without chills, profuse sweat, great thirst or bowel excess with a strong/deep pulse.', 'Pivot: alternating chills and fever, bitter taste, dry throat, dizziness, rib-side fullness and wiry pulse.'],
      rows: [
        ['Location', 'Exterior and channels.', 'Interior Qi or bowel.', 'Half-exterior, half-interior pivot.'],
        ['Temperature clue', 'Fever with aversion to cold.', 'Strong Heat without exterior chills.', 'Alternating chills and fever.'],
        ['Pulse', 'Floating.', 'Flooding/forceful or deep-excess.', 'Wiry.'],
        ['Formula examples', 'Ma Huang Tang or Gui Zhi Tang by sweating.', 'Bai Hu Tang or a Cheng Qi Tang by Qi/bowel pattern.', 'Xiao Chai Hu Tang.']
      ],
      why: 'Fever alone does not locate the stage. Exterior aversion, pure interior Heat or alternating pivot signs solve the comparison.',
      memory: '<strong>Taiyang is outside;</strong> <strong>Yangming is inside and blazing;</strong> <strong>Shaoyang swings at the hinge.</strong>',
      safety: 'High fever, confusion, severe dehydration, stiff neck, breathing difficulty, rigid abdomen or inability to pass stool/gas requires urgent medical evaluation.',
      prompts: ['Which stage has a floating pulse?', 'Which has great fever, sweat and thirst?', 'Which alternates chills and fever?']
    },
    es: {
      names: ['Taiyang', 'Yangming', 'Shaoyang'],
      shared: 'Todas son presentaciones Yang con fiebre, pero lugar, relación fiebre-escalofrío y pulso identifican etapa.',
      fast: ['Exterior: fiebre/escalofríos, cefalea occipital, nuca rígida y pulso flotante; sudor separa exceso/deficiencia.', 'Interior: fiebre alta sin escalofríos, sudor profuso, gran sed o exceso intestinal con pulso fuerte/profundo.', 'Bisagra: fiebre/escalofríos alternantes, sabor amargo, garganta seca, mareo, plenitud costal y pulso de cuerda.'],
      rows: [
        ['Localización', 'Exterior y canales.', 'Qi Interior o intestino.', 'Mitad Exterior-mitad Interior.'],
        ['Temperatura', 'Fiebre con aversión al frío.', 'Calor fuerte sin escalofríos exteriores.', 'Fiebre y escalofríos alternantes.'],
        ['Pulso', 'Flotante.', 'Desbordante/fuerte o profundo-exceso.', 'De cuerda.'],
        ['Ejemplos de fórmulas', 'Ma Huang Tang o Gui Zhi Tang según sudor.', 'Bai Hu Tang o Cheng Qi Tang según Qi/intestino.', 'Xiao Chai Hu Tang.']
      ],
      why: 'Fiebre sola no localiza. Aversión Exterior, Calor Interior puro o alternancia de bisagra resuelven comparación.',
      memory: '<strong>Taiyang está afuera;</strong> <strong>Yangming adentro y ardiente;</strong> <strong>Shaoyang oscila en la bisagra.</strong>',
      safety: 'Fiebre alta, confusión, deshidratación grave, nuca rígida, disnea, abdomen rígido o no expulsar heces/gases requiere urgencia.',
      prompts: ['¿Qué etapa tiene pulso flotante?', '¿Cuál gran fiebre, sudor y sed?', '¿Cuál alterna fiebre/escalofríos?']
    }
  },
  {
    slug: 'damp-heat-liver-gallbladder-vs-bladder-vs-large-intestine', title: 'Damp-Heat by Location: Liver–Gallbladder vs. Bladder vs. Large Intestine', titleEs: 'Humedad-Calor por Lugar: Hígado–Vesícula vs. Vejiga vs. Intestino Grueso',
    summary: 'Use the affected excretory route—bile/genitals, urine or stool—to localize Damp-Heat.',
    summaryEs: 'Usa la vía de excreción afectada—bilis/genitales, orina o heces—para localizar Humedad-Calor.',
    search: 'damp heat liver gallbladder bladder large intestine jaundice bitter taste genital discharge dysuria burning urine diarrhea dysentery mucus blood tenesmus yellow greasy slippery rapid',
    en: {
      names: ['Liver–Gallbladder Damp-Heat', 'Bladder Damp-Heat', 'Large-Intestine Damp-Heat'],
      shared: 'All show Heat plus Damp obstruction: heaviness/fullness, yellow greasy coat and a slippery rapid or wiry-rapid pulse.',
      fast: ['Bile/genital route: jaundice, bitter taste, rib-side pain, genital itching/swelling or yellow discharge.', 'Urinary route: urgency, frequency, burning painful scant dark urine or blood/sand in urine.', 'Stool route: foul urgent diarrhea or dysentery with mucus/blood, abdominal pain, burning anus and tenesmus.'],
      rows: [
        ['Primary route', 'Bile and genital/reproductive region.', 'Urinary tract.', 'Colon and stool.'],
        ['Decisive clue', 'Jaundice or genital Damp-Heat.', 'Burning painful urination.', 'Mucus-blood stool and tenesmus.'],
        ['Pulse tendency', 'Wiry-slippery-rapid.', 'Slippery-rapid.', 'Slippery-rapid.'],
        ['Formula examples', 'Long Dan Xie Gan Tang or Yin Chen Hao Tang by presentation.', 'Ba Zheng San.', 'Ge Gen Qin Lian Tang or Bai Tou Weng Tang by presentation.']
      ],
      why: 'The thermal and tongue signs overlap. Follow what the body is failing to excrete normally: bile/genital fluids, urine or stool.',
      memory: '<strong>Yellow body/genitals—Liver-GB;</strong> <strong>burning urine—Bladder;</strong> <strong>burning stool—Large Intestine.</strong>',
      safety: 'Jaundice, fever with flank pain, inability to urinate, pregnancy urinary symptoms, bloody diarrhea, severe dehydration or rigid abdominal pain requires urgent medical evaluation.',
      prompts: ['Which location causes jaundice?', 'Which causes burning urination?', 'Which causes tenesmus with mucus-blood stool?']
    },
    es: {
      names: ['Humedad-Calor de Hígado–Vesícula', 'Humedad-Calor de Vejiga', 'Humedad-Calor de Intestino Grueso'],
      shared: 'Todas muestran Calor con obstrucción Húmeda: pesadez/plenitud, capa amarilla grasosa y pulso resbaladizo-rápido o cuerda-rápido.',
      fast: ['Vía biliar/genital: ictericia, sabor amargo, dolor costal, picor/hinchazón genital o descarga amarilla.', 'Vía urinaria: urgencia, frecuencia, orina ardiente dolorosa escasa/oscura o sangre/arena.', 'Vía fecal: diarrea/disentería fétida urgente con moco/sangre, dolor, ano ardiente y tenesmo.'],
      rows: [
        ['Vía principal', 'Bilis y región genital/reproductiva.', 'Tracto urinario.', 'Colon y heces.'],
        ['Pista decisiva', 'Ictericia o Humedad-Calor genital.', 'Micción ardiente dolorosa.', 'Heces con moco-sangre y tenesmo.'],
        ['Pulso', 'Cuerda-resbaladizo-rápido.', 'Resbaladizo-rápido.', 'Resbaladizo-rápido.'],
        ['Ejemplos de fórmulas', 'Long Dan Xie Gan Tang o Yin Chen Hao Tang según presentación.', 'Ba Zheng San.', 'Ge Gen Qin Lian Tang o Bai Tou Weng Tang según presentación.']
      ],
      why: 'Temperatura y lengua se superponen. Sigue lo que el cuerpo no excreta normalmente: bilis/líquidos genitales, orina o heces.',
      memory: '<strong>Cuerpo/genitales amarillos—Hígado-VB;</strong> <strong>orina ardiente—Vejiga;</strong> <strong>heces ardientes—Intestino Grueso.</strong>',
      safety: 'Ictericia, fiebre con dolor lumbar, no poder orinar, síntomas urinarios en embarazo, diarrea sangrienta, deshidratación grave o abdomen rígido requiere urgencia.',
      prompts: ['¿Qué lugar causa ictericia?', '¿Cuál micción ardiente?', '¿Cuál tenesmo con moco-sangre?']
    }
  },
  {
    slug: 'yin-stages-taiyin-shaoyin-jueyin', title: 'Yin Stages: Taiyin vs. Shaoyin vs. Jueyin', titleEs: 'Etapas Yin: Taiyin vs. Shaoyin vs. Jueyin',
    summary: 'Track inward Cold from Taiyin digestive weakness to Shaoyin Cold or Heat transformation and terminal Jueyin Cold-Heat complexity.',
    summaryEs: 'Sigue Frío interno desde debilidad digestiva Taiyin hasta transformación Frío/Calor Shaoyin y complejidad Frío-Calor Jueyin.',
    search: 'yin stages taiyin shaoyin cold heat transformation jueyin diarrhea abdominal fullness cold limbs somnolence insomnia thirst upper heat lower cold li zhong si ni huang lian e jiao wu mei wan',
    en: {
      names: ['Taiyin', 'Shaoyin Cold', 'Shaoyin Heat', 'Jueyin'],
      shared: 'All are deeper Yin-stage patterns; the task is to identify digestive deficiency, devastated Yang, depleted Yin or mixed upper Heat/lower Cold.',
      fast: ['Spleen Cold: abdominal fullness, poor appetite, vomiting and diarrhea without thirst.', 'Yang collapse: aversion to cold, cold limbs, somnolence, clear diarrhea and faint pulse.', 'Yin depletion: irritability, insomnia, dry mouth/throat and dark scant urine.', 'Mixed reversal: thirst and chest Heat above with hunger without desire to eat, vomiting or diarrhea and cold below.'],
      rows: [
        ['Core mechanism', 'Middle-Jiao deficiency Cold.', 'Heart-Kidney Yang failure.', 'Heart-Kidney Yin deficiency with Heat.', 'Cold and Heat reversal with disordered ascent/descent.'],
        ['Tongue / pulse', 'Pale, white coat; weak-slow.', 'Pale/wet; deep-faint.', 'Red, little coat; thin-rapid.', 'Variable red tip/pale body; wiry or faint.'],
        ['Decisive clue', 'Digestive Cold without collapse.', 'Profound cold and desire to sleep.', 'Restless insomnia with dryness.', 'Upper Heat and lower Cold together.'],
        ['Formula examples', 'Li Zhong Wan.', 'Si Ni Tang.', 'Huang Lian E Jiao Tang.', 'Wu Mei Wan.']
      ],
      why: 'Diarrhea can occur in several stages. Thermal direction, consciousness, thirst and pulse strength locate the deeper mechanism.',
      memory: '<strong>Taiyin digests poorly;</strong> <strong>Shaoyin freezes or dries;</strong> <strong>Jueyin reverses Heat above and Cold below.</strong>',
      safety: 'Altered consciousness, shock signs, severe dehydration, GI bleeding, rigid abdominal pain or persistent vomiting requires emergency care. Fu Zi formulas require expert use.',
      prompts: ['Which stage is Middle Cold?', 'Which Shaoyin branch has insomnia?', 'Which pattern combines Heat above with Cold below?']
    },
    es: {
      names: ['Taiyin', 'Shaoyin Frío', 'Shaoyin Calor', 'Jueyin'],
      shared: 'Todas son etapas Yin profundas; hay que identificar deficiencia digestiva, Yang devastado, Yin agotado o mezcla Calor arriba/Frío abajo.',
      fast: ['Frío de Bazo: plenitud abdominal, poco apetito, vómito y diarrea sin sed.', 'Colapso Yang: aversión al frío, miembros fríos, somnolencia, diarrea clara y pulso casi ausente.', 'Yin agotado: irritabilidad, insomnio, boca/garganta seca y orina oscura escasa.', 'Reversión mixta: sed y Calor torácico arriba con hambre sin querer comer, vómito/diarrea y Frío abajo.'],
      rows: [
        ['Mecanismo', 'Frío por deficiencia del Jiao Medio.', 'Falla Yang de Corazón-Riñón.', 'Deficiencia Yin de Corazón-Riñón con Calor.', 'Reversión Frío-Calor y ascenso/descenso alterado.'],
        ['Lengua / pulso', 'Pálida, capa blanca; débil-lento.', 'Pálida/mojada; profundo-casi ausente.', 'Roja, poca capa; fino-rápido.', 'Variable: punta roja/cuerpo pálido; cuerda o débil.'],
        ['Pista decisiva', 'Frío digestivo sin colapso.', 'Frío profundo y deseo de dormir.', 'Insomnio inquieto con sequedad.', 'Calor arriba y Frío abajo juntos.'],
        ['Ejemplos de fórmulas', 'Li Zhong Wan.', 'Si Ni Tang.', 'Huang Lian E Jiao Tang.', 'Wu Mei Wan.']
      ],
      why: 'Diarrea aparece en varias etapas. Dirección térmica, conciencia, sed y fuerza del pulso localizan mecanismo.',
      memory: '<strong>Taiyin digiere mal;</strong> <strong>Shaoyin se congela o seca;</strong> <strong>Jueyin invierte Calor arriba y Frío abajo.</strong>',
      safety: 'Conciencia alterada, shock, deshidratación grave, sangrado GI, abdomen rígido o vómito persistente requiere urgencias. Fu Zi exige experto.',
      prompts: ['¿Qué etapa es Frío del Centro?', '¿Qué rama Shaoyin tiene insomnio?', '¿Cuál mezcla Calor arriba y Frío abajo?']
    }
  },
  {
    slug: 'upper-middle-lower-jiao-warm-disease', title: 'Upper vs. Middle vs. Lower Jiao in Warm Disease', titleEs: 'Jiao Superior vs. Medio vs. Inferior en Enfermedad Tibia',
    summary: 'Localize warm disease progression from Lung defense to Spleen-Stomach Heat or Dampness and finally Liver-Kidney Yin injury.',
    summaryEs: 'Localiza progresión tibia desde defensa pulmonar hasta Calor/Humedad de Bazo-Estómago y lesión Yin de Hígado-Riñón.',
    search: 'triple burner warm disease upper middle lower jiao lung fever chills cough sore throat stomach spleen high fever thirst dampness liver kidney yin night fever tremor',
    en: {
      names: ['Upper Jiao', 'Middle Jiao', 'Lower Jiao'],
      shared: 'All describe the location and evolution of warm disease; progression downward generally reflects increasing depth and fluid/Yin injury.',
      fast: ['Lung/defense: fever, mild chills, cough, thirst and sore throat.', 'Spleen-Stomach: blazing Qi Heat with fever/sweat/thirst or Damp-Warm heaviness, fullness and greasy coat.', 'Liver-Kidney: lingering night fever, dry mouth, weak low back, tremor or spasm from depleted Yin.'],
      rows: [
        ['Primary organs', 'Lung and Pericardium.', 'Spleen and Stomach.', 'Liver and Kidney.'],
        ['Decisive clue', 'Exterior respiratory onset.', 'Strong digestive Qi Heat or Damp obstruction.', 'Late-stage Yin depletion and internal Wind.'],
        ['Tongue / pulse', 'Red tip; floating-rapid.', 'Red/yellow or greasy coat; rapid/slippery.', 'Deep red, little coat; thin-rapid or weak.'],
        ['Formula examples', 'Yin Qiao San or Sang Ju Yin.', 'Bai Hu Tang or San Ren Tang by dry/Damp branch.', 'Qing Hao Bie Jia Tang or Da Ding Feng Zhu by presentation.']
      ],
      why: 'Fever is shared. Respiratory exterior signs, digestive Heat/Dampness and late Yin-Wind signs identify the Jiao.',
      memory: '<strong>Upper coughs;</strong> <strong>Middle burns or bogs;</strong> <strong>Lower dries and stirs Wind.</strong>',
      safety: 'Suspected serious infection, confusion, stiff neck, breathing difficulty, dehydration, non-blanching rash or seizure requires urgent medical evaluation.',
      prompts: ['Which Jiao begins in the Lung?', 'Which includes Damp-Warm digestion?', 'Which reflects late Liver-Kidney Yin injury?']
    },
    es: {
      names: ['Jiao Superior', 'Jiao Medio', 'Jiao Inferior'],
      shared: 'Todos describen localización/evolución de enfermedad tibia; descenso suele significar más profundidad y lesión de líquidos/Yin.',
      fast: ['Pulmón/defensa: fiebre, escalofríos leves, tos, sed y garganta dolorosa.', 'Bazo-Estómago: Calor Qi ardiente con fiebre/sudor/sed o Humedad-Tibia con pesadez, plenitud y capa grasosa.', 'Hígado-Riñón: fiebre nocturna persistente, boca seca, lumbar débil, temblor o espasmo por Yin agotado.'],
      rows: [
        ['Órganos', 'Pulmón y Pericardio.', 'Bazo y Estómago.', 'Hígado y Riñón.'],
        ['Pista decisiva', 'Inicio respiratorio Exterior.', 'Calor Qi digestivo fuerte u obstrucción Húmeda.', 'Agotamiento Yin tardío y Viento interno.'],
        ['Lengua / pulso', 'Punta roja; flotante-rápido.', 'Roja/amarilla o capa grasosa; rápido/resbaladizo.', 'Roja profunda, poca capa; fino-rápido o débil.'],
        ['Ejemplos de fórmulas', 'Yin Qiao San o Sang Ju Yin.', 'Bai Hu Tang o San Ren Tang según rama seca/Húmeda.', 'Qing Hao Bie Jia Tang o Da Ding Feng Zhu según presentación.']
      ],
      why: 'Fiebre es común. Signos respiratorios, Calor/Humedad digestiva y signos tardíos Yin-Viento identifican Jiao.',
      memory: '<strong>Superior tose;</strong> <strong>Medio arde o se empantana;</strong> <strong>Inferior se seca y mueve Viento.</strong>',
      safety: 'Infección grave sospechada, confusión, nuca rígida, disnea, deshidratación, rash no blanqueable o convulsión requiere urgencia.',
      prompts: ['¿Qué Jiao inicia en Pulmón?', '¿Cuál incluye Humedad-Tibia digestiva?', '¿Cuál lesión Yin tardía?']
    }
  },
  {
    slug: 'heart-kidney-not-communicating-vs-heart-spleen-deficiency', title: 'Heart–Kidney Not Communicating vs. Heart–Spleen Deficiency', titleEs: 'Corazón–Riñón no Comunican vs. Deficiencia de Corazón–Bazo',
    summary: 'Separate insomnia from upper Fire/lower Yin failure from insomnia rooted in deficient Qi and Blood production.',
    summaryEs: 'Distingue insomnio por Fuego arriba/Yin abajo de insomnio arraigado en producción deficiente de Qi y Sangre.',
    search: 'heart kidney not communicating heart spleen deficiency insomnia palpitations memory tinnitus low back night sweat five center heat fatigue appetite loose stool pale bleeding',
    en: {
      names: ['Heart–Kidney Not Communicating', 'Heart–Spleen Deficiency'],
      shared: 'Both cause insomnia, palpitations, anxiety, poor memory and fatigue.',
      fast: ['Upper Fire/lower Yin: restlessness, night sweating, five-center Heat, tinnitus, weak low back and red peeled tongue.', 'Qi-Blood deficiency: poor appetite, loose stool, pale complexion, dizziness, easy bruising and pale tongue.'],
      rows: [
        ['Root mechanism', 'Kidney Yin fails to restrain Heart Fire.', 'Spleen Qi fails to produce/hold Heart Blood.'],
        ['Thermal/color clue', 'Red, dry and warm at night.', 'Pale, weak and digestive.'],
        ['Pulse', 'Thin-rapid, sometimes floating-empty.', 'Weak and thin.'],
        ['Formula examples', 'Jiao Tai Wan or Tian Wang Bu Xin Dan by depth.', 'Gui Pi Tang.']
      ],
      why: 'Insomnia and palpitations overlap. Tinnitus/low back plus Empty Heat identify Heart-Kidney; digestive weakness and pallor identify Heart-Spleen.',
      memory: '<strong>Heart-Kidney is red and restless;</strong> <strong>Heart-Spleen is pale and depleted.</strong>',
      safety: 'Chest pain, fainting, sustained arrhythmia, suicidal thoughts, mania or severe prolonged insomnia requires prompt medical assessment.',
      prompts: ['Which has tinnitus and night sweats?', 'Which has poor appetite and bruising?', 'How do tongues differ?']
    },
    es: {
      names: ['Corazón–Riñón no Comunican', 'Deficiencia de Corazón–Bazo'],
      shared: 'Ambos causan insomnio, palpitaciones, ansiedad, mala memoria y fatiga.',
      fast: ['Fuego arriba/Yin abajo: inquietud, sudor nocturno, Calor de cinco centros, tinnitus, lumbar débil y lengua roja pelada.', 'Deficiencia Qi-Sangre: poco apetito, heces sueltas, palidez, mareo, moretones y lengua pálida.'],
      rows: [
        ['Mecanismo', 'Yin de Riñón no contiene Fuego de Corazón.', 'Qi de Bazo no produce/contiene Sangre de Corazón.'],
        ['Temperatura/color', 'Rojo, seco y caliente de noche.', 'Pálido, débil y digestivo.'],
        ['Pulso', 'Fino-rápido, a veces flotante-vacío.', 'Débil y fino.'],
        ['Ejemplos de fórmulas', 'Jiao Tai Wan o Tian Wang Bu Xin Dan según profundidad.', 'Gui Pi Tang.']
      ],
      why: 'Insomnio/palpitaciones se superponen. Tinnitus/lumbar más Calor Vacío es Corazón-Riñón; debilidad digestiva/palidez es Corazón-Bazo.',
      memory: '<strong>Corazón-Riñón es rojo e inquieto;</strong> <strong>Corazón-Bazo es pálido y agotado.</strong>',
      safety: 'Dolor torácico, desmayo, arritmia sostenida, ideas suicidas, manía o insomnio grave prolongado requiere evaluación pronta.',
      prompts: ['¿Cuál tiene tinnitus/sudor nocturno?', '¿Cuál poco apetito/moretones?', '¿Cómo difieren lenguas?']
    }
  },
  {
    slug: 'liver-overacting-spleen-vs-stomach', title: 'Liver Overacting on Spleen vs. Liver Overacting on Stomach', titleEs: 'Hígado Atacando Bazo vs. Hígado Atacando Estómago',
    summary: 'Follow constrained Liver Qi into lower digestive transport or upward Stomach rebellion.',
    summaryEs: 'Sigue Qi de Hígado constreñido hacia transporte digestivo inferior o rebeldía ascendente de Estómago.',
    search: 'liver overacting spleen stomach liver spleen disharmony liver stomach disharmony emotional abdominal pain diarrhea constipation epigastric belching nausea vomiting acid reflux',
    en: {
      names: ['Liver Overacting on Spleen', 'Liver Overacting on Stomach'],
      shared: 'Both arise when constrained Liver Qi disrupts digestion; symptoms fluctuate with stress and the pulse is often wiry.',
      fast: ['Transport fails below: abdominal pain/distention with loose stool or alternating diarrhea and constipation, often relieved after defecation.', 'Stomach Qi rebels upward: epigastric/rib-side distention with belching, nausea, vomiting or acid regurgitation.'],
      rows: [
        ['Affected function', 'Spleen transformation and stool formation.', 'Stomach receiving and descending.'],
        ['Direction', 'Irregular movement through intestines.', 'Rebellious upward movement.'],
        ['Decisive clue', 'Emotion-linked bowel change.', 'Emotion-linked belching, nausea or reflux.'],
        ['Formula examples', 'Tong Xie Yao Fang.', 'Chai Hu Shu Gan San, Ban Xia Hou Po Tang or Zuo Jin Wan by thermal branch.']
      ],
      why: 'Stress is shared. Stool disturbance localizes to Spleen; belching, nausea, vomiting and reflux localize to Stomach.',
      memory: '<strong>Liver-Spleen disrupts stool;</strong> <strong>Liver-Stomach pushes upward.</strong>',
      safety: 'Persistent vomiting, blood, black stool, progressive swallowing difficulty, severe localized pain or unexplained weight loss needs medical evaluation.',
      prompts: ['Which causes stress diarrhea?', 'Which causes belching and reflux?', 'What directional clue separates them?']
    },
    es: {
      names: ['Hígado Ataca Bazo', 'Hígado Ataca Estómago'],
      shared: 'Ambos surgen cuando Qi de Hígado constreñido altera digestión; síntomas fluctúan con estrés y pulso suele ser de cuerda.',
      fast: ['Falla transporte abajo: dolor/distensión abdominal con heces sueltas o diarrea/estreñimiento alternante, aliviado tras evacuar.', 'Qi de Estómago asciende rebelde: distensión epigástrica/costal con eructos, náusea, vómito o reflujo ácido.'],
      rows: [
        ['Función afectada', 'Transformación de Bazo y formación de heces.', 'Recepción y descenso de Estómago.'],
        ['Dirección', 'Movimiento intestinal irregular.', 'Movimiento rebelde hacia arriba.'],
        ['Pista decisiva', 'Cambio intestinal ligado a emociones.', 'Eructo, náusea o reflujo ligado a emociones.'],
        ['Ejemplos de fórmulas', 'Tong Xie Yao Fang.', 'Chai Hu Shu Gan San, Ban Xia Hou Po Tang o Zuo Jin Wan según rama térmica.']
      ],
      why: 'Estrés es común. Alteración de heces localiza Bazo; eructo, náusea, vómito y reflujo localizan Estómago.',
      memory: '<strong>Hígado-Bazo altera heces;</strong> <strong>Hígado-Estómago empuja arriba.</strong>',
      safety: 'Vómito persistente, sangre, heces negras, disfagia progresiva, dolor grave localizado o pérdida de peso necesita evaluación.',
      prompts: ['¿Cuál causa diarrea por estrés?', '¿Cuál eructos/reflujo?', '¿Qué dirección los separa?']
    }
  },
  {
    slug: 'kidney-essence-deficiency-vs-kidney-yin-deficiency', title: 'Kidney Essence Deficiency vs. Kidney Yin Deficiency', titleEs: 'Deficiencia de Esencia de Riñón vs. Deficiencia de Yin de Riñón',
    summary: 'Separate developmental, reproductive and marrow insufficiency from fluid-Yin depletion with Empty Heat.',
    summaryEs: 'Distingue insuficiencia del desarrollo, reproducción y médula de agotamiento de líquidos-Yin con Calor Vacío.',
    search: 'kidney essence jing deficiency yin deficiency development growth delayed fontanel bones teeth infertility memory hearing low back night sweat five center heat red peeled',
    en: {
      names: ['Kidney Essence Deficiency', 'Kidney Yin Deficiency'],
      shared: 'Both can cause weak low back/knees, tinnitus, poor memory, reproductive weakness and a deep weak pulse.',
      fast: ['Jing fails to build: delayed growth/development, weak bones/teeth, infertility, premature aging or declining hearing/memory; Heat may be absent.', 'Yin fails to cool: night sweating, five-center Heat, dry mouth, malar flush and a red peeled tongue.'],
      rows: [
        ['Primary substance', 'Jing governing growth, reproduction and marrow.', 'Yin fluids and cooling nourishment.'],
        ['Age/course clue', 'Developmental, constitutional or premature-aging signs.', 'Chronic depletion with dryness and Empty Heat.'],
        ['Tongue / pulse', 'Pale or red depending Yang/Yin branch; weak chi.', 'Red, little coat; deep-thin-rapid.'],
        ['Formula examples', 'Zuo Gui Wan or You Gui Wan according to Yin/Yang branch.', 'Liu Wei Di Huang Wan or Zhi Bai Di Huang Wan if Fire is stronger.']
      ],
      why: 'Kidney weakness overlaps. Development, fertility, marrow and aging point to Essence; dryness and Empty Heat specifically point to Yin.',
      memory: '<strong>Essence builds the blueprint;</strong> <strong>Yin supplies the cooling fluid.</strong>',
      safety: 'Developmental delay, infertility, sudden hearing loss, major cognitive change or unexplained weight loss requires biomedical evaluation.',
      prompts: ['Which affects growth and marrow?', 'Which has five-center Heat?', 'Can Essence deficiency be either Yin- or Yang-leaning?']
    },
    es: {
      names: ['Deficiencia de Esencia de Riñón', 'Deficiencia de Yin de Riñón'],
      shared: 'Ambas causan lumbar/rodillas débiles, tinnitus, mala memoria, debilidad reproductiva y pulso profundo-débil.',
      fast: ['Jing no construye: desarrollo tardío, huesos/dientes débiles, infertilidad, envejecimiento prematuro o audición/memoria declinante; puede no haber Calor.', 'Yin no enfría: sudor nocturno, Calor de cinco centros, boca seca, rubor malar y lengua roja pelada.'],
      rows: [
        ['Sustancia', 'Jing que gobierna crecimiento, reproducción y médula.', 'Líquidos Yin y nutrición enfriadora.'],
        ['Edad/curso', 'Signos del desarrollo, constitucionales o envejecimiento precoz.', 'Agotamiento crónico con sequedad y Calor Vacío.'],
        ['Lengua / pulso', 'Pálida o roja según rama Yang/Yin; chi débil.', 'Roja, poca capa; profundo-fino-rápido.'],
        ['Ejemplos de fórmulas', 'Zuo Gui Wan o You Gui Wan según rama Yin/Yang.', 'Liu Wei Di Huang Wan o Zhi Bai Di Huang Wan si Fuego es fuerte.']
      ],
      why: 'Debilidad renal se superpone. Desarrollo, fertilidad, médula y edad apuntan a Esencia; sequedad y Calor Vacío a Yin.',
      memory: '<strong>Esencia construye el plano;</strong> <strong>Yin aporta líquido refrigerante.</strong>',
      safety: 'Retraso del desarrollo, infertilidad, pérdida auditiva súbita, cambio cognitivo mayor o pérdida de peso requiere evaluación biomédica.',
      prompts: ['¿Cuál afecta crecimiento/médula?', '¿Cuál Calor de cinco centros?', '¿Esencia puede inclinarse a Yin o Yang?']
    }
  },
  {
    slug: 'dryness-attacking-lung-vs-lung-yin-deficiency', title: 'Dryness Attacking the Lung vs. Lung Yin Deficiency', titleEs: 'Sequedad Atacando Pulmón vs. Deficiencia de Yin de Pulmón',
    summary: 'Separate acute seasonal external Dryness from chronic internal failure to moisten the Lung.',
    summaryEs: 'Distingue Sequedad externa estacional aguda de falla interna crónica para humedecer Pulmón.',
    search: 'dryness attacking lung yin deficiency acute autumn dry cough nose throat slight fever chills chronic night sweat afternoon fever hoarse voice peeled tongue floating thin rapid',
    en: {
      names: ['Dryness Attacking the Lung', 'Lung Yin Deficiency'],
      shared: 'Both cause dry cough, dry nose/throat, scant sputum, thirst and possible hoarseness.',
      fast: ['Acute exterior onset, often autumn: slight fever/chills, headache and dry cough with a floating pulse and thin dry coat.', 'Chronic internal deficiency: weak persistent cough, afternoon Heat, night sweats, five-center Heat and red peeled tongue with thin rapid pulse.'],
      rows: [
        ['Onset', 'Acute and seasonal/exposure related.', 'Chronic or post-febrile/consumptive.'],
        ['Exterior signs', 'Mild fever, chills or headache present.', 'Absent; Empty Heat replaces them.'],
        ['Tongue / pulse', 'Dry tongue with thin coat; floating-rapid.', 'Red, dry, little coat; thin-rapid.'],
        ['Formula examples', 'Sang Xing Tang or Qing Zao Jiu Fei Tang by severity.', 'Bai He Gu Jin Tang or Sha Shen Mai Dong Tang.']
      ],
      why: 'Dry cough is shared. Acute exterior timing and a remaining coat indicate invading Dryness; chronic Empty Heat and peeled tongue indicate Yin deficiency.',
      memory: '<strong>External Dryness arrives;</strong> <strong>Lung Yin deficiency lingers.</strong>',
      safety: 'Breathing difficulty, chest pain, coughing blood, low oxygen, high fever or cough lasting over three weeks requires medical assessment.',
      prompts: ['Which begins acutely in dry weather?', 'Which has night sweats?', 'How do pulse and tongue coat differ?']
    },
    es: {
      names: ['Sequedad Ataca Pulmón', 'Deficiencia de Yin de Pulmón'],
      shared: 'Ambas causan tos seca, nariz/garganta seca, esputo escaso, sed y posible ronquera.',
      fast: ['Inicio Exterior agudo, a menudo otoñal: fiebre/escalofríos leves, cefalea y tos seca con pulso flotante y capa fina seca.', 'Deficiencia interna crónica: tos débil persistente, Calor vespertino, sudor nocturno, Calor de cinco centros y lengua roja pelada con pulso fino-rápido.'],
      rows: [
        ['Inicio', 'Agudo y estacional/por exposición.', 'Crónico o posfebril/consuntivo.'],
        ['Signos Exterior', 'Fiebre leve, escalofríos o cefalea.', 'Ausentes; aparecen signos de Calor Vacío.'],
        ['Lengua / pulso', 'Lengua seca con capa fina; flotante-rápido.', 'Roja, seca, poca capa; fino-rápido.'],
        ['Ejemplos de fórmulas', 'Sang Xing Tang o Qing Zao Jiu Fei Tang según gravedad.', 'Bai He Gu Jin Tang o Sha Shen Mai Dong Tang.']
      ],
      why: 'Tos seca es común. Inicio Exterior agudo y capa presente indican Sequedad invasora; Calor Vacío crónico y lengua pelada indican Yin.',
      memory: '<strong>Sequedad externa llega;</strong> <strong>deficiencia Yin de Pulmón permanece.</strong>',
      safety: 'Disnea, dolor torácico, sangre al toser, oxígeno bajo, fiebre alta o tos mayor de tres semanas requiere evaluación.',
      prompts: ['¿Cuál inicia agudo en clima seco?', '¿Cuál tiene sudor nocturno?', '¿Cómo difieren pulso/capa?']
    }
  },
  {
    slug: 'heart-qi-deficiency-vs-heart-blood-deficiency', title: 'Heart Qi Deficiency vs. Heart Blood Deficiency', titleEs: 'Deficiencia de Qi de Corazón vs. Deficiencia de Sangre de Corazón',
    summary: 'Separate weak cardiac function and exertional symptoms from deficient nourishment of the Spirit.',
    summaryEs: 'Distingue función cardíaca débil con síntomas de esfuerzo de nutrición deficiente de Shen.',
    search: 'heart qi blood deficiency palpitations exertion short breath spontaneous sweat fatigue insomnia anxiety memory dizziness pale tongue weak thin pulse',
    en: {
      names: ['Heart Qi Deficiency', 'Heart Blood Deficiency'],
      shared: 'Both may cause palpitations, fatigue, anxiety, pale complexion and a weak pulse.',
      fast: ['Function is weak: palpitations worse with exertion, shortness of breath, spontaneous sweating and general fatigue.', 'Nourishment is weak: insomnia, dream-disturbed sleep, poor memory, dizziness and emotional unease with a pale thin tongue.'],
      rows: [
        ['Primary failure', 'Heart Qi cannot propel and govern effectively.', 'Heart Blood cannot nourish and house the Spirit.'],
        ['Decisive clue', 'Exertional breathlessness and sweating.', 'Insomnia, forgetfulness and dizziness.'],
        ['Tongue / pulse', 'Pale; weak, possibly irregular.', 'Pale and thin/dry; thin or choppy.'],
        ['Formula examples', 'Zhi Gan Cao Tang or Sheng Mai San by presentation.', 'Gui Pi Tang or Suan Zao Ren Tang by root pattern.']
      ],
      why: 'Palpitations overlap. Exertional functional weakness points to Qi; sleep, memory and undernourished Spirit signs point to Blood.',
      memory: '<strong>Heart Qi cannot pump;</strong> <strong>Heart Blood cannot house Shen.</strong>',
      safety: 'Chest pain, fainting, sustained irregular rhythm, severe breathlessness or new palpitations requires prompt medical assessment.',
      prompts: ['Which worsens with exertion?', 'Which produces insomnia and poor memory?', 'How do pulse qualities differ?']
    },
    es: {
      names: ['Deficiencia de Qi de Corazón', 'Deficiencia de Sangre de Corazón'],
      shared: 'Ambas causan palpitaciones, fatiga, ansiedad, palidez y pulso débil.',
      fast: ['Función débil: palpitaciones peores con esfuerzo, falta de aire, sudor espontáneo y fatiga general.', 'Nutrición débil: insomnio, sueños perturbadores, mala memoria, mareo e inquietud con lengua pálida-fina.'],
      rows: [
        ['Falla principal', 'Qi de Corazón no impulsa/gobierna eficazmente.', 'Sangre de Corazón no nutre ni aloja Shen.'],
        ['Pista decisiva', 'Disnea de esfuerzo y sudor.', 'Insomnio, olvido y mareo.'],
        ['Lengua / pulso', 'Pálida; débil, posible irregular.', 'Pálida y fina/seca; fino o áspero.'],
        ['Ejemplos de fórmulas', 'Zhi Gan Cao Tang o Sheng Mai San según presentación.', 'Gui Pi Tang o Suan Zao Ren Tang según raíz.']
      ],
      why: 'Palpitaciones se superponen. Debilidad funcional con esfuerzo apunta a Qi; sueño, memoria y Shen desnutrido apuntan a Sangre.',
      memory: '<strong>Qi de Corazón no impulsa;</strong> <strong>Sangre de Corazón no aloja Shen.</strong>',
      safety: 'Dolor torácico, desmayo, ritmo irregular sostenido, disnea grave o palpitaciones nuevas requiere evaluación pronta.',
      prompts: ['¿Cuál empeora con esfuerzo?', '¿Cuál causa insomnio/mala memoria?', '¿Cómo difieren pulsos?']
    }
  },
  {
    slug: 'lung-qi-deficiency-vs-kidney-failing-to-grasp-qi', title: 'Lung Qi Deficiency vs. Kidney Failing to Grasp Qi', titleEs: 'Deficiencia de Qi de Pulmón vs. Riñón que no Capta Qi',
    summary: 'Separate weak Lung diffusion and defense from chronic inspiratory weakness rooted in the Kidneys.',
    summaryEs: 'Distingue difusión/defensa pulmonar débil de debilidad inspiratoria crónica arraigada en Riñón.',
    search: 'lung qi deficiency kidney failing grasp qi cough weak voice spontaneous sweat recurrent colds dyspnea asthma inhalation exertion low back knees deep pulse',
    en: {
      names: ['Lung Qi Deficiency', 'Kidney Failing to Grasp Qi'],
      shared: 'Both can cause chronic shortness of breath, weak cough, fatigue and a weak pulse.',
      fast: ['Upper/source weakness: weak voice and cough, spontaneous sweating, recurrent colds and breathlessness with speech or mild exertion.', 'Lower/root weakness: difficulty inhaling deeply, chronic wheezing worse with exertion, weak low back/knees and possible urinary or reproductive signs.'],
      rows: [
        ['Failed function', 'Diffuse/descend Lung Qi and secure Defensive Qi.', 'Receive and anchor inhaled Lung Qi.'],
        ['Breathing clue', 'Weak respiration and voice overall.', 'Inhalation is especially difficult or shallow.'],
        ['Pulse', 'Weak, especially right cun.', 'Deep-weak at chi positions.'],
        ['Formula examples', 'Bu Fei Tang or Yu Ping Feng San by presentation.', 'Ren Shen Ge Jie San or Su Zi Jiang Qi Tang by Cold-Phlegm context.']
      ],
      why: 'Breathlessness overlaps. Weak voice/sweating/recurrent infections localize to Lung; inspiratory difficulty plus low-back signs localizes to Kidney.',
      memory: '<strong>Lung Qi cannot send;</strong> <strong>Kidney Qi cannot receive.</strong>',
      safety: 'Severe or rapidly worsening breathlessness, blue lips, chest pain, confusion, low oxygen or inability to speak full sentences is an emergency.',
      prompts: ['Which has recurrent colds?', 'Which struggles mainly to inhale?', 'What lower-burner signs identify Kidney involvement?']
    },
    es: {
      names: ['Deficiencia de Qi de Pulmón', 'Riñón no Capta Qi'],
      shared: 'Ambas causan disnea crónica, tos débil, fatiga y pulso débil.',
      fast: ['Debilidad de fuente superior: voz/tos débil, sudor espontáneo, resfriados frecuentes y falta de aire al hablar o con esfuerzo leve.', 'Debilidad de raíz inferior: dificultad para inspirar profundo, sibilancia crónica peor con esfuerzo, lumbar/rodillas débiles y posibles signos urinarios/reproductivos.'],
      rows: [
        ['Función fallida', 'Difundir/descender Qi de Pulmón y asegurar Qi Defensivo.', 'Recibir y anclar Qi pulmonar inhalado.'],
        ['Respiración', 'Respiración y voz débiles en general.', 'Inspiración especialmente difícil o superficial.'],
        ['Pulso', 'Débil, especialmente cun derecho.', 'Profundo-débil en posiciones chi.'],
        ['Ejemplos de fórmulas', 'Bu Fei Tang o Yu Ping Feng San según presentación.', 'Ren Shen Ge Jie San o Su Zi Jiang Qi Tang con Flema-Frío.']
      ],
      why: 'Disnea se superpone. Voz débil/sudor/infecciones localizan Pulmón; inspiración difícil más lumbar localiza Riñón.',
      memory: '<strong>Qi de Pulmón no envía;</strong> <strong>Qi de Riñón no recibe.</strong>',
      safety: 'Disnea grave/progresiva, labios azules, dolor torácico, confusión, oxígeno bajo o no hablar frases completas es emergencia.',
      prompts: ['¿Cuál tiene resfriados frecuentes?', '¿Cuál lucha al inspirar?', '¿Qué signos inferiores indican Riñón?']
    }
  },
  {
    slug: 'stomach-qi-deficiency-vs-spleen-qi-deficiency', title: 'Stomach Qi Deficiency vs. Spleen Qi Deficiency', titleEs: 'Deficiencia de Qi de Estómago vs. Deficiencia de Qi de Bazo',
    summary: 'Separate weak receiving and descending from weak transformation, transport and tissue support.',
    summaryEs: 'Distingue recepción/descenso débil de transformación, transporte y sostén tisular débil.',
    search: 'stomach spleen qi deficiency appetite epigastric discomfort nausea belching vomiting loose stool bloating fatigue heaviness edema muscles pale tongue weak pulse',
    en: {
      names: ['Stomach Qi Deficiency', 'Spleen Qi Deficiency'],
      shared: 'Both cause poor appetite, fatigue, pale tongue and a weak right-guan pulse; they commonly coexist.',
      fast: ['Receiving/descending fails: epigastric emptiness, poor appetite, mild nausea, belching or tendency to vomiting.', 'Transformation/transport fails: post-meal bloating, loose stools, heaviness, weak limbs and possible Dampness or edema.'],
      rows: [
        ['Direction/function', 'Receives food and descends Qi.', 'Transforms food/fluids and raises clear Qi.'],
        ['Decisive clue', 'Nausea, belching and epigastric emptiness.', 'Loose stool, bloating, heaviness and muscle weakness.'],
        ['Tongue / pulse', 'Pale, possibly center depression; weak.', 'Pale, possibly swollen/toothmarks; weak.'],
        ['Formula examples', 'Si Jun Zi Tang; Xiang Sha Liu Jun Zi Tang if nausea/stagnation.', 'Si Jun Zi Tang or Shen Ling Bai Zhu San when Dampness/diarrhea dominates.']
      ],
      why: 'Poor appetite is shared. Upward rebellion identifies Stomach; stool, Dampness and weak limbs identify Spleen transport failure.',
      memory: '<strong>Stomach cannot receive and descend;</strong> <strong>Spleen cannot transform and lift.</strong>',
      safety: 'Persistent vomiting, progressive swallowing difficulty, GI bleeding, severe pain or unexplained weight loss requires medical evaluation.',
      prompts: ['Which produces belching and nausea?', 'Which produces loose stool and heaviness?', 'Why do they often coexist?']
    },
    es: {
      names: ['Deficiencia de Qi de Estómago', 'Deficiencia de Qi de Bazo'],
      shared: 'Ambas causan poco apetito, fatiga, lengua pálida y pulso guan derecho débil; suelen coexistir.',
      fast: ['Falla recepción/descenso: vacío epigástrico, poco apetito, náusea leve, eructos o tendencia a vomitar.', 'Falla transformación/transporte: distensión posprandial, heces sueltas, pesadez, extremidades débiles y posible Humedad/edema.'],
      rows: [
        ['Dirección/función', 'Recibe alimento y desciende Qi.', 'Transforma alimento/líquidos y eleva Qi claro.'],
        ['Pista decisiva', 'Náusea, eructo y vacío epigástrico.', 'Heces sueltas, distensión, pesadez y músculos débiles.'],
        ['Lengua / pulso', 'Pálida, posible depresión central; débil.', 'Pálida, posible hinchada/marcas dentales; débil.'],
        ['Ejemplos de fórmulas', 'Si Jun Zi Tang; Xiang Sha Liu Jun Zi Tang si náusea/estancamiento.', 'Si Jun Zi Tang o Shen Ling Bai Zhu San si dominan Humedad/diarrea.']
      ],
      why: 'Poco apetito es común. Rebeldía ascendente identifica Estómago; heces, Humedad y miembros débiles identifican Bazo.',
      memory: '<strong>Estómago no recibe/desciende;</strong> <strong>Bazo no transforma/eleva.</strong>',
      safety: 'Vómito persistente, disfagia progresiva, sangrado GI, dolor grave o pérdida de peso inexplicada requiere evaluación.',
      prompts: ['¿Cuál produce eructos/náusea?', '¿Cuál heces sueltas/pesadez?', '¿Por qué coexisten?']
    }
  },
  {
    slug: 'yin-collapse-vs-yang-collapse', title: 'Yin Collapse vs. Yang Collapse', titleEs: 'Colapso de Yin vs. Colapso de Yang',
    summary: 'Recognize two medical emergencies by warm sticky sweat and Heat versus cold profuse sweat and profound Cold.',
    summaryEs: 'Reconoce dos emergencias mediante sudor tibio pegajoso con Calor frente a sudor frío profuso con Frío profundo.',
    search: 'yin collapse yang collapse emergency profuse sweat warm sticky oily cold sweat hot skin cold limbs thirst red dry pale wet rapid minute faint slow',
    en: {
      names: ['Yin Collapse', 'Yang Collapse'],
      shared: 'Both are critical depletion states with profuse sweating, extreme weakness, altered responsiveness and a minute pulse.',
      fast: ['Fluids/Yin exhausted: warm sticky or oily sweat, hot skin, intense thirst, restlessness, red dry tongue and rapid minute pulse.', 'Yang devastated: profuse cold sweat, icy limbs, pale or bluish face, little thirst or desire for warm drinks, pale wet tongue and faint slow pulse.'],
      rows: [
        ['Temperature', 'Heat and dryness predominate.', 'Profound Cold and loss of warming predominate.'],
        ['Sweat', 'Warm, sticky or oily.', 'Cold, copious and clear.'],
        ['Tongue / pulse', 'Red-dry; minute-rapid.', 'Pale-wet; minute-faint-slow.'],
        ['Classical formula association', 'Sheng Mai San in Qi-Yin depletion contexts.', 'Si Ni Tang in devastated-Yang theory; Fu Zi requires expert emergency-level use.']
      ],
      why: 'Both can sweat and collapse. Temperature of skin/sweat, thirst, tongue moisture and pulse speed determine Yin versus Yang failure.',
      memory: '<strong>Yin collapse is hot and sticky;</strong> <strong>Yang collapse is cold and pouring.</strong>',
      safety: 'Both descriptions represent medical emergencies such as shock, severe dehydration, sepsis or cardiac illness. Call emergency services; do not attempt herbal self-treatment.',
      prompts: ['Which has warm sticky sweat?', 'Which has icy limbs?', 'Why are both emergency presentations?']
    },
    es: {
      names: ['Colapso de Yin', 'Colapso de Yang'],
      shared: 'Ambos son estados críticos con sudor profuso, debilidad extrema, respuesta alterada y pulso diminuto.',
      fast: ['Líquidos/Yin agotados: sudor tibio pegajoso o aceitoso, piel caliente, sed intensa, inquietud, lengua roja-seca y pulso diminuto-rápido.', 'Yang devastado: sudor frío profuso, miembros helados, cara pálida/azulada, poca sed o deseo de caliente, lengua pálida-mojada y pulso débil-lento.'],
      rows: [
        ['Temperatura', 'Predominan Calor y sequedad.', 'Predomina Frío profundo y pérdida de calentamiento.'],
        ['Sudor', 'Tibio, pegajoso o aceitoso.', 'Frío, copioso y claro.'],
        ['Lengua / pulso', 'Roja-seca; diminuto-rápido.', 'Pálida-mojada; diminuto-débil-lento.'],
        ['Asociación clásica', 'Sheng Mai San en agotamiento Qi-Yin.', 'Si Ni Tang en teoría de Yang devastado; Fu Zi exige experto y nivel de emergencia.']
      ],
      why: 'Ambos sudan y colapsan. Temperatura de piel/sudor, sed, humedad lingual y velocidad del pulso determinan Yin o Yang.',
      memory: '<strong>Colapso Yin es caliente/pegajoso;</strong> <strong>colapso Yang es frío/copioso.</strong>',
      safety: 'Ambos representan emergencias como shock, deshidratación grave, sepsis o enfermedad cardíaca. Llame a emergencias; no intente autotratamiento herbal.',
      prompts: ['¿Cuál tiene sudor tibio pegajoso?', '¿Cuál miembros helados?', '¿Por qué ambos son emergencia?']
    }
  },
  {
    slug: 'summerheat-vs-exterior-wind-heat', title: 'Summerheat vs. Exterior Wind-Heat', titleEs: 'Summerheat vs. Viento-Calor Exterior',
    summary: 'Separate intense seasonal Heat and fluid injury from a superficial Wind-Heat respiratory presentation.',
    summaryEs: 'Distingue Calor estacional intenso con lesión de líquidos de presentación respiratoria superficial por Viento-Calor.',
    search: 'summerheat wind heat exterior summer high fever profuse sweat intense thirst irritability scant dark urine sore throat cough chills floating rapid dampness',
    en: {
      names: ['Summerheat', 'Exterior Wind-Heat'],
      shared: 'Both occur with fever, thirst, sweating, irritability and a rapid pulse during warm weather.',
      fast: ['Seasonal Heat is intense: high fever, profuse sweat, strong thirst, scant dark urine and fatigue; Dampness may add nausea or diarrhea.', 'Exterior Wind-Heat is superficial: fever with mild chills, sore throat, cough, headache and a floating-rapid pulse.'],
      rows: [
        ['Depth', 'Strong Heat directly injures Qi and fluids; may combine with Dampness.', 'Exterior Wind-Heat at Lung/Wei level.'],
        ['Decisive clue', 'Summer exposure plus marked sweat/thirst and urinary fluid loss.', 'Sore throat/cough with remaining chills and floating pulse.'],
        ['Tongue / pulse', 'Red with dry or greasy yellow coat; rapid, possibly soggy.', 'Red tip/edges with thin coat; floating-rapid.'],
        ['Formula examples', 'Qing Luo Yin or Bai Hu Jia Ren Shen Tang by severity.', 'Yin Qiao San or Sang Ju Yin.']
      ],
      why: 'Warm season alone is not enough. Intense fluid injury and possible Dampness indicate Summerheat; respiratory Exterior signs indicate Wind-Heat.',
      memory: '<strong>Summerheat scorches and drains;</strong> <strong>Wind-Heat stays near the surface and throat.</strong>',
      safety: 'Heat stroke is an emergency: confusion, collapse, seizure, very high temperature or hot altered skin requires immediate cooling and emergency services.',
      prompts: ['Which strongly injures fluids?', 'Which retains mild chills?', 'What signs show Summerheat with Dampness?']
    },
    es: {
      names: ['Summerheat', 'Viento-Calor Exterior'],
      shared: 'Ambos ocurren con fiebre, sed, sudor, irritabilidad y pulso rápido en clima cálido.',
      fast: ['Calor estacional intenso: fiebre alta, sudor profuso, sed fuerte, orina oscura escasa y fatiga; Humedad puede añadir náusea/diarrea.', 'Viento-Calor superficial: fiebre con escalofríos leves, dolor de garganta, tos, cefalea y pulso flotante-rápido.'],
      rows: [
        ['Profundidad', 'Calor fuerte lesiona Qi/líquidos; puede combinarse con Humedad.', 'Viento-Calor Exterior en nivel Pulmón/Wei.'],
        ['Pista decisiva', 'Exposición veraniega más sudor/sed marcados y pérdida urinaria.', 'Garganta/tos con escalofríos restantes y pulso flotante.'],
        ['Lengua / pulso', 'Roja con capa amarilla seca o grasosa; rápido, posible blando.', 'Punta/bordes rojos con capa fina; flotante-rápido.'],
        ['Ejemplos de fórmulas', 'Qing Luo Yin o Bai Hu Jia Ren Shen Tang según gravedad.', 'Yin Qiao San o Sang Ju Yin.']
      ],
      why: 'Clima cálido solo no basta. Lesión intensa de líquidos y posible Humedad indica Summerheat; signos respiratorios Exterior indican Viento-Calor.',
      memory: '<strong>Summerheat abrasa y drena;</strong> <strong>Viento-Calor queda en superficie y garganta.</strong>',
      safety: 'Golpe de calor es emergencia: confusión, colapso, convulsión, temperatura muy alta o piel caliente alterada requiere enfriamiento y emergencias.',
      prompts: ['¿Cuál lesiona fuertemente líquidos?', '¿Cuál conserva escalofríos?', '¿Qué indica Summerheat con Humedad?']
    }
  },
  {
    slug: 'cold-in-liver-channel-vs-liver-qi-stagnation', title: 'Cold in the Liver Channel vs. Liver Qi Stagnation', titleEs: 'Frío en Canal de Hígado vs. Estancamiento de Qi de Hígado',
    summary: 'Separate contracted cold lower-abdominal or genital pain from distending emotion-sensitive Qi constraint.',
    summaryEs: 'Distingue dolor contraído frío hipogástrico/genital de constricción distensiva de Qi sensible a emociones.',
    search: 'cold liver channel liver qi stagnation lower abdomen genital scrotal hernia pain severe contracted cold warmth distending rib side emotional wiry tight',
    en: {
      names: ['Cold in the Liver Channel', 'Liver Qi Stagnation'],
      shared: 'Both may cause lower-abdominal, genital or rib-side pain and a wiry pulse because the Liver channel is involved.',
      fast: ['Cold contraction: severe fixed lower-abdominal, groin or scrotal pain, worse with cold and improved by warmth; pale tongue and deep-wiry-tight pulse.', 'Qi constraint: distending variable pain, sighing, mood changes and menstrual symptoms that worsen with stress; tongue may be normal.'],
      rows: [
        ['Pathogen/mechanism', 'Cold contracts the Liver channel and blocks Qi/Blood.', 'Liver Qi fails to course freely.'],
        ['Pain quality', 'Contracting, severe and cold-sensitive.', 'Distending, mobile and emotion-sensitive.'],
        ['Tongue / pulse', 'Pale with white moist coat; deep-wiry-tight.', 'Normal/dusky; wiry.'],
        ['Formula examples', 'Tian Tai Wu Yao San or Nuan Gan Jian.', 'Chai Hu Shu Gan San or Xiao Yao San when deficiency coexists.']
      ],
      why: 'Channel location overlaps. Cold response and contraction identify Cold; emotional variability and distention identify Qi stagnation.',
      memory: '<strong>Cold contracts the groin;</strong> <strong>Qi stagnation distends with stress.</strong>',
      safety: 'Sudden severe testicular, pelvic or abdominal pain, swelling, fever, vomiting or pregnancy-related pain requires urgent evaluation.',
      prompts: ['Which improves with warmth?', 'Which fluctuates with emotion?', 'How do tongue and pulse distinguish them?']
    },
    es: {
      names: ['Frío en Canal de Hígado', 'Estancamiento de Qi de Hígado'],
      shared: 'Ambos causan dolor hipogástrico, genital o costal y pulso de cuerda porque participa el canal de Hígado.',
      fast: ['Contracción fría: dolor grave fijo hipogástrico, inguinal o escrotal, peor con frío y mejor con calor; lengua pálida y pulso profundo-cuerda-tenso.', 'Constricción de Qi: dolor distensivo variable, suspiros, cambios de ánimo y síntomas menstruales peores con estrés; lengua puede ser normal.'],
      rows: [
        ['Patógeno/mecanismo', 'Frío contrae canal de Hígado y bloquea Qi/Sangre.', 'Qi de Hígado no circula libremente.'],
        ['Dolor', 'Contractivo, grave y sensible al frío.', 'Distensivo, móvil y sensible a emociones.'],
        ['Lengua / pulso', 'Pálida con capa blanca húmeda; profundo-cuerda-tenso.', 'Normal/oscura; de cuerda.'],
        ['Ejemplos de fórmulas', 'Tian Tai Wu Yao San o Nuan Gan Jian.', 'Chai Hu Shu Gan San o Xiao Yao San si hay deficiencia.']
      ],
      why: 'Localización de canal se superpone. Respuesta al frío y contracción indican Frío; variabilidad emocional y distensión indican Qi.',
      memory: '<strong>Frío contrae la ingle;</strong> <strong>Qi estancado distiende con estrés.</strong>',
      safety: 'Dolor testicular, pélvico o abdominal súbito grave, hinchazón, fiebre, vómito o dolor en embarazo requiere urgencia.',
      prompts: ['¿Cuál mejora con calor?', '¿Cuál cambia con emociones?', '¿Cómo distinguen lengua/pulso?']
    }
  }
);

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
<nav class="comparison-collection-links" aria-label="${es ? 'Grupos de estudio' : 'Study groups'}"><a href="index${es ? '-es' : ''}.html">${es ? 'Comparaciones de Hierbas' : 'Single Herbs Comparisons'}</a><a href="formulas${es ? '-es' : ''}.html">${es ? 'Comparaciones de Fórmulas' : 'Formula Comparisons'}</a><a href="pathologies${es ? '-es' : ''}.html">${es ? 'Comparaciones de Patologías' : 'Pathology Comparisons'}</a><a aria-current="page" href="patterns${es ? '-es' : ''}.html">${es ? 'Comparaciones de Patrones' : 'Pattern Comparisons'}</a><a href="families${es ? '-es' : ''}.html">${es ? 'Familias de Fórmulas' : 'Formula Families'}</a><a href="dui-yao${es ? '-es' : ''}.html">${es ? 'Parejas Duì Yào' : 'Duì Yào Pairs'}</a><a href="name-traps${es ? '-es' : ''}.html">${es ? 'Trampas de Nombres' : 'Name Traps'}</a></nav>
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
