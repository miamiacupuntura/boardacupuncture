import { writeFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '../study-guide/comparisons');
const cssVersion = '20261026-2';

const families = [
  {
    slug: 'ma-huang-tang-family', title: 'Ma Huang Tang Family',
    category: { en: 'Release the Exterior · Regulate Lung Qi', es: 'Liberar el Exterior · Regular el Qi de Pulmón' },
    summary: { en: 'Follow the Ma Huang–Xing Ren axis from closed Wind-Cold to internal Heat or retained thin fluids.', es: 'Sigue el eje Ma Huang–Xing Ren desde Viento-Frío cerrado hasta Calor interno o retención de líquidos finos.' },
    search: 'ma huang tang family da qing long tang xiao qing long tang ma xing shi gan tang exterior wind cold lung heat thin fluids wheezing',
    en: {
      root: 'Ma Huang Tang establishes the family logic: strongly open a closed Wind-Cold Exterior, disseminate Lung Qi and relieve wheezing.',
      architecture: 'Core axis: Ma Huang opens and disseminates; Xing Ren descends Lung Qi. Gui Zhi strengthens Exterior warming, while Shi Gao redirects the family toward Heat.',
      members: [
        ['Ma Huang Tang', 'Ma Huang + Gui Zhi + Xing Ren + Zhi Gan Cao', 'Exterior Wind-Cold excess', 'No sweating, strong chills, floating-tight pulse and wheezing.'],
        ['Da Qing Long Tang', 'Expands the Ma Huang Tang strategy and adds Shi Gao', 'Exterior Wind-Cold excess with internal Heat', 'No sweating plus irritability, thirst or stronger Heat.'],
        ['Xiao Qing Long Tang', 'Uses Ma Huang/Gui Zhi with warming, drying and fluid-transforming herbs', 'Exterior Wind-Cold with internal thin fluids', 'Clear copious sputum, cough, wheezing, retching or chest fullness.'],
        ['Ma Xing Shi Gan Tang', 'Retains Ma Huang-Xing Ren, removes Gui Zhi and adds a large Shi Gao emphasis', 'Lung Heat with constrained Lung Qi', 'Fever, sweating or thirst with cough and wheezing; Heat dominates.'],
      ],
      rule: 'First ask whether the Exterior is closed. Then decide whether the branch is pure Cold, internal Heat or thin-fluid retention.',
      memory: '<strong>Base closes sweat;</strong> <strong>Da Qing adds Heat;</strong> <strong>Xiao Qing adds water;</strong> <strong>Ma Xing Shi Gan becomes Lung Heat.</strong>',
      safety: 'Every member contains Ma Huang and requires cardiovascular, stimulant and medication screening. Xiao Qing Long Tang also uses processed Ban Xia and Xi Xin.'
    },
    es: {
      root: 'Ma Huang Tang establece la lógica: abrir con fuerza un Exterior cerrado por Viento-Frío, difundir Qi de Pulmón y aliviar sibilancias.',
      architecture: 'Eje central: Ma Huang abre y difunde; Xing Ren hace descender Qi pulmonar. Gui Zhi refuerza calentamiento Exterior y Shi Gao redirige la familia hacia Calor.',
      members: [
        ['Ma Huang Tang', 'Ma Huang + Gui Zhi + Xing Ren + Zhi Gan Cao', 'Exceso Exterior Viento-Frío', 'Sin sudor, escalofríos fuertes, pulso flotante-tenso y sibilancias.'],
        ['Da Qing Long Tang', 'Amplía Ma Huang Tang y añade Shi Gao', 'Exceso Exterior Viento-Frío con Calor interno', 'Sin sudor más irritabilidad, sed o Calor más fuerte.'],
        ['Xiao Qing Long Tang', 'Ma Huang/Gui Zhi más hierbas calientes, secantes y transformadoras de líquidos', 'Viento-Frío Exterior con líquidos finos internos', 'Esputo claro abundante, tos, sibilancias, arcadas o plenitud torácica.'],
        ['Ma Xing Shi Gan Tang', 'Conserva Ma Huang-Xing Ren, elimina Gui Zhi y enfatiza Shi Gao', 'Calor de Pulmón con Qi pulmonar constreñido', 'Fiebre, sudor o sed con tos y sibilancias; domina Calor.'],
      ],
      rule: 'Primero pregunta si el Exterior está cerrado. Después decide si la rama es Frío puro, Calor interno o retención de líquidos finos.',
      memory: '<strong>La base cierra sudor;</strong> <strong>Da Qing añade Calor;</strong> <strong>Xiao Qing añade agua;</strong> <strong>Ma Xing Shi Gan se vuelve Calor pulmonar.</strong>',
      safety: 'Todos contienen Ma Huang y requieren evaluación cardiovascular, de estimulantes y medicamentos. Xiao Qing Long también usa Ban Xia procesada y Xi Xin.'
    }
  },
  {
    slug: 'si-jun-zi-tang-family', title: 'Si Jun Zi Tang Family',
    category: { en: 'Tonify Spleen Qi', es: 'Tonificar el Qi de Bazo' },
    summary: { en: 'Build from simple Spleen-Qi deficiency to Phlegm, Qi stagnation, Damp diarrhea, Middle-Cold or sinking Qi.', es: 'Avanza desde deficiencia simple de Qi de Bazo hacia Flema, estancamiento de Qi, diarrea por Humedad, Frío del Centro o hundimiento de Qi.' },
    search: 'si jun zi tang family liu jun zi tang xiang sha liu jun zi tang shen ling bai zhu san li zhong wan bu zhong yi qi tang spleen qi',
    en: {
      root: 'Si Jun Zi Tang is the foundational Spleen-Qi formula: supplement Qi, strengthen the Spleen and support fluid transformation.',
      architecture: 'Core: Ren Shen, Bai Zhu, Fu Ling and Zhi Gan Cao. The family changes by adding Phlegm transformation, Qi movement, Damp drainage, warming or lifting.',
      members: [
        ['Si Jun Zi Tang', 'Four-herb Qi-tonic core', 'Spleen-Stomach Qi deficiency', 'Fatigue, poor appetite, loose stools, pale tongue and weak pulse.'],
        ['Liu Jun Zi Tang', 'Si Jun Zi Tang + Chen Pi + processed Ban Xia', 'Qi deficiency with Damp-Phlegm', 'The base picture plus nausea, chest/epigastric fullness or Phlegm.'],
        ['Xiang Sha Liu Jun Zi Tang', 'Liu Jun Zi Tang + Mu Xiang + Sha Ren', 'Qi deficiency with Phlegm-Damp and Qi stagnation', 'More distention, pain, belching or poor appetite.'],
        ['Shen Ling Bai Zhu San', 'Broadens the tonic core with herbs that drain Dampness and stop diarrhea', 'Spleen-Qi deficiency with Dampness', 'Chronic diarrhea, weakness, weight loss and a pale greasy tongue.'],
        ['Li Zhong Wan', 'Replaces the fluid-draining emphasis with Gan Jiang warming', 'Middle-Jiao deficiency Cold', 'Cold abdominal pain, vomiting or diarrhea improved by warmth.'],
        ['Bu Zhong Yi Qi Tang', 'Adds Huang Qi plus lifting herbs Sheng Ma and Chai Hu', 'Sinking Middle Qi', 'Prolapse, chronic diarrhea, bearing-down sensation or deficiency fever.'],
      ],
      rule: 'Start with Spleen-Qi deficiency, then identify the complication: Phlegm, stagnation, Damp diarrhea, Cold or sinking.',
      memory: '<strong>Four tonify;</strong> <strong>Six transform Phlegm;</strong> <strong>Xiang Sha moves;</strong> <strong>Shen Ling drains;</strong> <strong>Li Zhong warms;</strong> <strong>Bu Zhong lifts.</strong>',
      safety: 'Tonifying formulas can trap an unresolved pathogen or worsen Food Stagnation. Processed Ban Xia is required in formulas that contain it.'
    },
    es: {
      root: 'Si Jun Zi Tang es la fórmula fundamental de Qi de Bazo: suplementa Qi, fortalece Bazo y apoya transformación de líquidos.',
      architecture: 'Núcleo: Ren Shen, Bai Zhu, Fu Ling y Zhi Gan Cao. La familia cambia al añadir transformación de Flema, movimiento de Qi, drenaje de Humedad, calor o elevación.',
      members: [
        ['Si Jun Zi Tang', 'Núcleo tónico de Qi de cuatro hierbas', 'Deficiencia de Qi de Bazo-Estómago', 'Fatiga, poco apetito, heces sueltas, lengua pálida y pulso débil.'],
        ['Liu Jun Zi Tang', 'Si Jun Zi Tang + Chen Pi + Ban Xia procesada', 'Deficiencia Qi con Humedad-Flema', 'Cuadro base más náuseas, plenitud torácica/epigástrica o Flema.'],
        ['Xiang Sha Liu Jun Zi Tang', 'Liu Jun Zi Tang + Mu Xiang + Sha Ren', 'Deficiencia Qi con Flema-Humedad y estancamiento de Qi', 'Más distensión, dolor, eructos o poco apetito.'],
        ['Shen Ling Bai Zhu San', 'Amplía el núcleo con hierbas que drenan Humedad y detienen diarrea', 'Deficiencia Qi de Bazo con Humedad', 'Diarrea crónica, debilidad, pérdida de peso y lengua pálida grasosa.'],
        ['Li Zhong Wan', 'Sustituye énfasis drenante por calentamiento con Gan Jiang', 'Frío por deficiencia del Jiao Medio', 'Dolor abdominal frío, vómito o diarrea mejorados por calor.'],
        ['Bu Zhong Yi Qi Tang', 'Añade Huang Qi y elevadores Sheng Ma/Chai Hu', 'Hundimiento de Qi del Centro', 'Prolapso, diarrea crónica, sensación de caída o fiebre por deficiencia.'],
      ],
      rule: 'Comienza con deficiencia de Qi de Bazo y busca la complicación: Flema, estancamiento, diarrea por Humedad, Frío o hundimiento.',
      memory: '<strong>Cuatro tonifican;</strong> <strong>Seis transforman Flema;</strong> <strong>Xiang Sha mueve;</strong> <strong>Shen Ling drena;</strong> <strong>Li Zhong calienta;</strong> <strong>Bu Zhong eleva.</strong>',
      safety: 'Los tónicos pueden atrapar un patógeno no resuelto o agravar estancamiento alimentario. Las fórmulas con Ban Xia requieren forma procesada.'
    }
  },
  {
    slug: 'si-wu-tang-family', title: 'Si Wu Tang Family',
    category: { en: 'Tonify and Regulate Blood', es: 'Tonificar y regular la Sangre' },
    summary: { en: 'Follow the four-substance Blood core into combined Qi-Blood deficiency and deficiency Cold.', es: 'Sigue el núcleo de cuatro sustancias desde deficiencia de Sangre hasta deficiencia combinada de Qi-Sangre y Frío por deficiencia.' },
    search: 'si wu tang family ba zhen tang shi quan da bu tang blood qi deficiency cold four substances eight treasures ten complete',
    en: {
      root: 'Si Wu Tang combines nourishment and movement so that Blood is replenished without becoming static.',
      architecture: 'Core: Shu Di Huang nourishes deeply, Bai Shao preserves and softens, Dang Gui nourishes and moves, and Chuan Xiong circulates Blood.',
      members: [
        ['Si Wu Tang', 'Four-substance Blood core', 'Blood deficiency with impaired circulation', 'Pale complexion, dizziness, palpitations and menstrual irregularity.'],
        ['Ba Zhen Tang', 'Si Wu Tang + Si Jun Zi Tang', 'Combined Qi and Blood deficiency', 'Blood-deficiency signs plus fatigue, poor appetite and weak digestion.'],
        ['Shi Quan Da Bu Tang', 'Ba Zhen Tang + Huang Qi + Rou Gui', 'Qi-Blood deficiency with Cold or weak Yang', 'The combined picture plus cold limbs, aversion to cold and deeper weakness.'],
      ],
      rule: 'Blood alone points to Si Wu; digestive fatigue adds Si Jun to make Ba Zhen; Cold adds Huang Qi and Rou Gui to make Shi Quan Da Bu.',
      memory: '<strong>Four Blood;</strong> <strong>Eight Qi + Blood;</strong> <strong>Ten Qi + Blood + warmth.</strong>',
      safety: 'Rich Blood tonics may aggravate Dampness or weak digestion. Blood-moving components require pregnancy and bleeding review.'
    },
    es: {
      root: 'Si Wu Tang combina nutrición y movimiento para reponer Sangre sin volverla estática.',
      architecture: 'Núcleo: Shu Di Huang nutre profundamente, Bai Shao preserva y suaviza, Dang Gui nutre y mueve, y Chuan Xiong circula Sangre.',
      members: [
        ['Si Wu Tang', 'Núcleo de cuatro sustancias para Sangre', 'Deficiencia de Sangre con circulación alterada', 'Palidez, mareo, palpitaciones e irregularidad menstrual.'],
        ['Ba Zhen Tang', 'Si Wu Tang + Si Jun Zi Tang', 'Deficiencia combinada de Qi y Sangre', 'Signos de Sangre más fatiga, poco apetito y digestión débil.'],
        ['Shi Quan Da Bu Tang', 'Ba Zhen Tang + Huang Qi + Rou Gui', 'Deficiencia Qi-Sangre con Frío o Yang débil', 'Cuadro combinado más extremidades frías, aversión al frío y debilidad profunda.'],
      ],
      rule: 'Sangre sola señala Si Wu; fatiga digestiva añade Si Jun para formar Ba Zhen; Frío añade Huang Qi y Rou Gui para Shi Quan Da Bu.',
      memory: '<strong>Cuatro Sangre;</strong> <strong>Ocho Qi + Sangre;</strong> <strong>Diez Qi + Sangre + calor.</strong>',
      safety: 'Tónicos ricos de Sangre pueden agravar Humedad o digestión débil. Los componentes movilizadores requieren revisión de embarazo y sangrado.'
    }
  },
  {
    slug: 'liu-wei-di-huang-wan-family', title: 'Liu Wei Di Huang Wan Family',
    category: { en: 'Tonify Liver-Kidney Yin · Restore Kidney function', es: 'Tonificar Yin de Hígado-Riñón · Restaurar función renal' },
    summary: { en: 'Use the six-herb Yin base, then add deficient-Fire, eye or Kidney-Yang/water-transformation branches.', es: 'Parte de la base Yin de seis hierbas y añade las ramas de Fuego vacío, ojos o Yang renal/transformación del agua.' },
    search: 'liu wei di huang wan family zhi bai di huang wan qi ju di huang wan jin gui shen qi wan ba wei kidney liver yin yang fire eyes water',
    en: {
      root: 'Liu Wei Di Huang Wan balances three tonifying herbs with three draining/regulating herbs for Liver-Kidney Yin deficiency.',
      architecture: 'The six-herb base stays recognizable. Additions identify the branch; Fu Zi and Rou Gui reverse the direction toward Kidney Yang and Qi transformation.',
      members: [
        ['Liu Wei Di Huang Wan', 'Six-herb three-tonify/three-drain base', 'Liver-Kidney Yin deficiency', 'Weak back, tinnitus, night sweating and a red tongue with little coating.'],
        ['Zhi Bai Di Huang Wan', 'Liu Wei + Zhi Mu + Huang Bai', 'Yin deficiency with deficient Fire', 'Stronger five-center Heat, tidal fever, irritability and night sweating.'],
        ['Qi Ju Di Huang Wan', 'Liu Wei + Gou Qi Zi + Ju Hua', 'Liver-Kidney Yin deficiency affecting the eyes', 'Dry, blurred or weak vision.'],
        ['Jin Gui Shen Qi Wan / Ba Wei Di Huang Wan', 'Liu Wei base + processed Fu Zi + Rou Gui', 'Kidney-Yang deficiency with impaired Qi transformation', 'Cold, edema and urinary or fluid-metabolism dysfunction.'],
      ],
      rule: 'Base Yin, then ask: Fire, eyes or Yang/water? Ba Wei Di Huang Wan and Jin Gui Shen Qi Wan are treated here as names from the same eight-ingredient lineage.',
      memory: '<strong>Liu Wei = Yin;</strong> <strong>Zhi Bai = Fire;</strong> <strong>Qi Ju = eyes;</strong> <strong>Ba Wei/Jin Gui = Yang and water.</strong>',
      safety: 'Rich Yin formulas can worsen Dampness or loose stools. Kidney-Yang members contain processed Fu Zi and require professional supervision.'
    },
    es: {
      root: 'Liu Wei Di Huang Wan equilibra tres tónicos con tres drenantes/reguladores para deficiencia de Yin Hígado-Riñón.',
      architecture: 'La base de seis hierbas permanece reconocible. Las adiciones identifican la rama; Fu Zi y Rou Gui invierten la dirección hacia Yang renal y transformación de Qi.',
      members: [
        ['Liu Wei Di Huang Wan', 'Base de seis: tres tonifican/tres drenan', 'Deficiencia de Yin Hígado-Riñón', 'Lumbar débil, tinnitus, sudor nocturno y lengua roja con poca capa.'],
        ['Zhi Bai Di Huang Wan', 'Liu Wei + Zhi Mu + Huang Bai', 'Deficiencia Yin con Fuego vacío', 'Calor de cinco centros, fiebre vespertina, irritabilidad y sudor nocturno más fuertes.'],
        ['Qi Ju Di Huang Wan', 'Liu Wei + Gou Qi Zi + Ju Hua', 'Deficiencia Yin Hígado-Riñón que afecta ojos', 'Visión seca, borrosa o débil.'],
        ['Jin Gui Shen Qi Wan / Ba Wei Di Huang Wan', 'Base Liu Wei + Fu Zi procesado + Rou Gui', 'Deficiencia Yang renal con transformación de Qi alterada', 'Frío, edema y disfunción urinaria o de líquidos.'],
      ],
      rule: 'Base Yin; después pregunta: ¿Fuego, ojos o Yang/agua? Ba Wei Di Huang Wan y Jin Gui Shen Qi Wan se tratan como nombres del mismo linaje de ocho ingredientes.',
      memory: '<strong>Liu Wei = Yin;</strong> <strong>Zhi Bai = Fuego;</strong> <strong>Qi Ju = ojos;</strong> <strong>Ba Wei/Jin Gui = Yang y agua.</strong>',
      safety: 'Fórmulas Yin ricas pueden agravar Humedad o heces sueltas. Miembros Yang renal contienen Fu Zi procesado y requieren supervisión.'
    }
  },
  {
    slug: 'er-chen-tang-family', title: 'Er Chen Tang Family',
    category: { en: 'Transform Phlegm', es: 'Transformar Flema' },
    summary: { en: 'Move from foundational Damp-Phlegm to Phlegm-Heat disturbing the Spirit or Wind-Phlegm causing vertigo.', es: 'Avanza desde Humedad-Flema básica hacia Flema-Calor que perturba el Shen o Viento-Flema que causa vértigo.' },
    search: 'er chen tang family wen dan tang ban xia bai zhu tian ma tang damp phlegm heat wind vertigo insomnia',
    en: {
      root: 'Er Chen Tang is the foundational Damp-Phlegm formula: dry Dampness, transform Phlegm, regulate Qi and harmonize the Middle.',
      architecture: 'Core: processed Ban Xia, Chen Pi, Fu Ling and Zhi Gan Cao, traditionally supported by Sheng Jiang and Wu Mei.',
      members: [
        ['Er Chen Tang', 'Foundational Ban Xia–Chen Pi core', 'Damp-Phlegm in Spleen and Lung', 'Copious white sputum, nausea, fullness and a greasy white coat.'],
        ['Wen Dan Tang', 'Builds on the Phlegm-transforming core with Zhu Ru and Zhi Shi', 'Phlegm-Heat disturbing Gallbladder, Stomach and Spirit', 'Insomnia, easy fright, bitter taste and greasy yellow coating.'],
        ['Ban Xia Bai Zhu Tian Ma Tang', 'Adds Bai Zhu and Tian Ma to a Damp-Phlegm strategy', 'Wind-Phlegm rising from Spleen deficiency', 'Vertigo, dizziness, nausea, headache and a greasy coat.'],
      ],
      rule: 'White Damp-Phlegm stays with Er Chen; Heat disturbing sleep points to Wen Dan; vertigo from Wind-Phlegm points to Ban Xia Bai Zhu Tian Ma.',
      memory: '<strong>Er Chen = white Phlegm;</strong> <strong>Wen Dan = hot restless Phlegm;</strong> <strong>Tian Ma = dizzy Wind-Phlegm.</strong>',
      safety: 'Ban Xia must be properly processed. New severe vertigo, neurologic deficits, fainting or a sudden severe headache requires urgent assessment.'
    },
    es: {
      root: 'Er Chen Tang es la fórmula base de Humedad-Flema: seca Humedad, transforma Flema, regula Qi y armoniza Centro.',
      architecture: 'Núcleo: Ban Xia procesada, Chen Pi, Fu Ling y Zhi Gan Cao, tradicionalmente apoyados por Sheng Jiang y Wu Mei.',
      members: [
        ['Er Chen Tang', 'Núcleo fundamental Ban Xia–Chen Pi', 'Humedad-Flema en Bazo y Pulmón', 'Esputo blanco abundante, náuseas, plenitud y capa blanca grasosa.'],
        ['Wen Dan Tang', 'Amplía el núcleo con Zhu Ru y Zhi Shi', 'Flema-Calor perturbando Vesícula Biliar, Estómago y Shen', 'Insomnio, sobresalto fácil, sabor amargo y capa amarilla grasosa.'],
        ['Ban Xia Bai Zhu Tian Ma Tang', 'Añade Bai Zhu y Tian Ma a estrategia de Humedad-Flema', 'Viento-Flema ascendente desde deficiencia de Bazo', 'Vértigo, mareo, náusea, cefalea y capa grasosa.'],
      ],
      rule: 'Flema blanca permanece en Er Chen; Calor que perturba sueño señala Wen Dan; vértigo por Viento-Flema señala Ban Xia Bai Zhu Tian Ma.',
      memory: '<strong>Er Chen = Flema blanca;</strong> <strong>Wen Dan = Flema caliente e inquieta;</strong> <strong>Tian Ma = Viento-Flema mareante.</strong>',
      safety: 'Ban Xia debe estar procesada. Vértigo grave nuevo, déficit neurológico, desmayo o cefalea súbita intensa requiere urgencia.'
    }
  },
  {
    slug: 'cheng-qi-tang-family', title: 'Cheng Qi Tang Family',
    category: { en: 'Drain Downward · Purge Heat', es: 'Drenar hacia abajo · Purgar Calor' },
    summary: { en: 'Grade Yangming bowel excess, then recognize Blood-stasis or Yin-injury branches.', es: 'Gradúa el exceso intestinal Yangming y reconoce las ramas de estasis de Sangre o lesión de Yin.' },
    search: 'cheng qi tang family da cheng qi tang xiao cheng qi tang tiao wei cheng qi tang tao he cheng qi tang zeng ye constipation heat dryness blood stasis yin',
    en: {
      root: 'The Cheng Qi family clears Heat and moves stool. Zhi Shi/Hou Po move Qi, Da Huang purges Heat and Mang Xiao softens hardness.',
      architecture: 'Removing or adding one functional unit changes the severity and branch pattern.',
      members: [
        ['Da Cheng Qi Tang', 'Da Huang + Mang Xiao + Zhi Shi + Hou Po', 'Severe Yangming bowel excess', 'Marked distention, fullness, hardness and constipation.'],
        ['Xiao Cheng Qi Tang', 'Da Cheng Qi without Mang Xiao', 'Moderate bowel excess with less Dryness/hardness', 'Distention and fullness remain, but hardness is less severe.'],
        ['Tiao Wei Cheng Qi Tang', 'Da Huang + Mang Xiao + Zhi Gan Cao; no Zhi Shi/Hou Po', 'Yangming Heat-Dryness without marked Qi obstruction', 'Constipation and irritability with less focal fullness.'],
        ['Tao He Cheng Qi Tang', 'A Cheng Qi branch with Tao Ren and Gui Zhi', 'Lower-burner Blood stasis with Heat', 'Acute lower-abdominal pain/fullness, mental restlessness and normal urination.'],
        ['Zeng Ye Cheng Qi Tang', 'Zeng Ye Tang + Da Huang + Mang Xiao', 'Yin-fluid injury with dry bowel excess', 'Severe Dryness where moistening alone cannot move stool.'],
      ],
      rule: 'Grade obstruction first; then look for the branch clue—Blood stasis below or damaged Yin and fluids.',
      memory: '<strong>Da = all four;</strong> <strong>Xiao = no salt;</strong> <strong>Tiao Wei = no Qi movers;</strong> <strong>Tao He = Blood;</strong> <strong>Zeng Ye = fluids.</strong>',
      safety: 'These are strong purgative formulas, not self-care laxatives. Pregnancy, frailty, dehydration, severe pain, vomiting or suspected obstruction requires avoidance and urgent assessment.'
    },
    es: {
      root: 'La familia Cheng Qi elimina Calor y moviliza heces. Zhi Shi/Hou Po mueven Qi, Da Huang purga Calor y Mang Xiao ablanda dureza.',
      architecture: 'Quitar o añadir una unidad funcional cambia gravedad y patrón de rama.',
      members: [
        ['Da Cheng Qi Tang', 'Da Huang + Mang Xiao + Zhi Shi + Hou Po', 'Exceso intestinal Yangming grave', 'Distensión, plenitud, dureza y estreñimiento marcados.'],
        ['Xiao Cheng Qi Tang', 'Da Cheng Qi sin Mang Xiao', 'Exceso moderado con menos Sequedad/dureza', 'Persisten distensión y plenitud, pero la dureza es menor.'],
        ['Tiao Wei Cheng Qi Tang', 'Da Huang + Mang Xiao + Zhi Gan Cao; sin Zhi Shi/Hou Po', 'Calor-Sequedad Yangming sin obstrucción marcada de Qi', 'Estreñimiento e irritabilidad con menos plenitud focal.'],
        ['Tao He Cheng Qi Tang', 'Rama Cheng Qi con Tao Ren y Gui Zhi', 'Estasis de Sangre con Calor en Jiao Inferior', 'Dolor/plenitud hipogástrica aguda, inquietud y micción normal.'],
        ['Zeng Ye Cheng Qi Tang', 'Zeng Ye Tang + Da Huang + Mang Xiao', 'Lesión de Yin-líquidos con exceso intestinal seco', 'Sequedad grave donde humedecer solo no mueve las heces.'],
      ],
      rule: 'Primero gradúa la obstrucción; luego busca rama: estasis inferior o Yin/líquidos lesionados.',
      memory: '<strong>Da = los cuatro;</strong> <strong>Xiao = sin sal;</strong> <strong>Tiao Wei = sin movilizadores Qi;</strong> <strong>Tao He = Sangre;</strong> <strong>Zeng Ye = líquidos.</strong>',
      safety: 'Son purgantes fuertes, no laxantes de autocuidado. Embarazo, debilidad, deshidratación, dolor grave, vómito o sospecha de obstrucción exige evitación y evaluación urgente.'
    }
  },
  {
    slug: 'gui-zhi-tang-family', title: 'Gui Zhi Tang Family',
    category: { en: 'Release the Exterior · Harmonize Ying and Wei', es: 'Liberar el Exterior · Armonizar Ying y Wei' },
    summary: { en: 'Modify the harmonizing Wind-Cold base for neck stiffness, wheezing, leakage or Middle-Jiao deficiency Cold.', es: 'Modifica la base armonizadora de Viento-Frío para rigidez cervical, sibilancias, pérdidas o Frío por deficiencia del Jiao Medio.' },
    search: 'gui zhi tang family gui zhi jia ge gen tang gui zhi jia hou po xing zi tang gui zhi jia long gu mu li tang xiao jian zhong tang huang qi jian zhong tang wind cold sweating neck wheezing abdominal pain',
    en: {
      root: 'Gui Zhi Tang releases Wind-Cold from an Exterior-deficiency presentation while harmonizing the nutritive and protective levels.',
      architecture: 'Gui Zhi warms and releases; Bai Shao preserves Yin and moderates the exterior; Sheng Jiang, Da Zao and Zhi Gan Cao harmonize. The Gui Zhi–Bai Shao balance is the family axis.',
      members: [
        ['Gui Zhi Tang', 'Five-herb harmonizing base', 'Exterior Wind-Cold deficiency', 'Spontaneous sweating, aversion to wind, floating-moderate pulse.'],
        ['Gui Zhi Jia Ge Gen Tang', 'Gui Zhi Tang + Ge Gen', 'Exterior deficiency with channel constraint', 'Sweating plus pronounced stiff neck and upper back.'],
        ['Gui Zhi Jia Hou Po Xing Zi Tang', 'Gui Zhi Tang + Hou Po + Xing Ren', 'Exterior deficiency with rebellious Lung Qi', 'The Gui Zhi Tang picture plus cough or wheezing.'],
        ['Gui Zhi Jia Long Gu Mu Li Tang', 'Gui Zhi Tang + Long Gu + Mu Li', 'Disharmony with deficiency leakage', 'Spermatorrhea, dream-disturbed sleep, palpitations or loss of essence.'],
        ['Xiao Jian Zhong Tang', 'Gui Zhi Tang with doubled Bai Shao + Yi Tang', 'Middle-Jiao deficiency Cold with spasm', 'Intermittent abdominal pain relieved by warmth and pressure.'],
        ['Huang Qi Jian Zhong Tang', 'Xiao Jian Zhong Tang + Huang Qi', 'Middle deficiency Cold with stronger Qi deficiency', 'Abdominal pain plus marked fatigue, spontaneous sweating or chronic weakness.'],
      ],
      rule: 'Keep the Gui Zhi–Bai Shao harmony in view; the added symptom identifies the branch—neck, Lung, leakage or abdominal deficiency Cold.',
      memory: '<strong>Ge Gen = neck;</strong> <strong>Hou Po/Xing Ren = Lung;</strong> <strong>Long Gu/Mu Li = leakage;</strong> <strong>Yi Tang = abdomen;</strong> <strong>Huang Qi = deeper deficiency.</strong>',
      safety: 'Do not confuse this spontaneous-sweating Exterior-deficiency picture with a closed, no-sweat Ma Huang Tang presentation. Yi Tang-containing formulas require dietary/glucose review.'
    },
    es: {
      root: 'Gui Zhi Tang libera Viento-Frío en una presentación de deficiencia Exterior mientras armoniza los niveles nutritivo y defensivo.',
      architecture: 'Gui Zhi calienta y libera; Bai Shao preserva Yin y modera el Exterior; Sheng Jiang, Da Zao y Zhi Gan Cao armonizan. El equilibrio Gui Zhi–Bai Shao es el eje familiar.',
      members: [
        ['Gui Zhi Tang', 'Base armonizadora de cinco hierbas', 'Deficiencia Exterior por Viento-Frío', 'Sudor espontáneo, aversión al viento y pulso flotante-moderado.'],
        ['Gui Zhi Jia Ge Gen Tang', 'Gui Zhi Tang + Ge Gen', 'Deficiencia Exterior con constricción de canales', 'Sudor más rigidez marcada de cuello y espalda superior.'],
        ['Gui Zhi Jia Hou Po Xing Zi Tang', 'Gui Zhi Tang + Hou Po + Xing Ren', 'Deficiencia Exterior con Qi de Pulmón rebelde', 'Cuadro Gui Zhi Tang más tos o sibilancias.'],
        ['Gui Zhi Jia Long Gu Mu Li Tang', 'Gui Zhi Tang + Long Gu + Mu Li', 'Desarmonía con pérdidas por deficiencia', 'Espermatorrea, sueños perturbadores, palpitaciones o pérdida de esencia.'],
        ['Xiao Jian Zhong Tang', 'Gui Zhi Tang con Bai Shao duplicada + Yi Tang', 'Frío por deficiencia del Jiao Medio con espasmo', 'Dolor abdominal intermitente aliviado por calor y presión.'],
        ['Huang Qi Jian Zhong Tang', 'Xiao Jian Zhong Tang + Huang Qi', 'Frío del Centro con deficiencia de Qi más fuerte', 'Dolor abdominal más fatiga marcada, sudor espontáneo o debilidad crónica.'],
      ],
      rule: 'Conserva el eje Gui Zhi–Bai Shao; el síntoma añadido identifica la rama: cuello, Pulmón, pérdidas o Frío abdominal por deficiencia.',
      memory: '<strong>Ge Gen = cuello;</strong> <strong>Hou Po/Xing Ren = Pulmón;</strong> <strong>Long Gu/Mu Li = pérdidas;</strong> <strong>Yi Tang = abdomen;</strong> <strong>Huang Qi = deficiencia profunda.</strong>',
      safety: 'No confundir este Exterior deficiente con sudor con Ma Huang Tang cerrado y sin sudor. Las fórmulas con Yi Tang requieren revisión dietética y de glucosa.'
    }
  },
  {
    slug: 'xiao-chai-hu-tang-family', title: 'Xiao Chai Hu Tang Family',
    category: { en: 'Harmonize Shaoyang', es: 'Armonizar Shaoyang' },
    summary: { en: 'Track Shaoyang disharmony as it combines with Yangming excess, Taiyang exterior signs or severe mental restlessness.', es: 'Sigue la desarmonía Shaoyang cuando se combina con exceso Yangming, signos Taiyang o inquietud mental intensa.' },
    search: 'xiao chai hu tang family da chai hu tang chai hu gui zhi tang chai hu jia long gu mu li tang shaoyang yangming taiyang alternating chills fever',
    en: {
      root: 'Xiao Chai Hu Tang harmonizes a half-exterior, half-interior Shaoyang disorder and restores the pivot.',
      architecture: 'Chai Hu vents Shaoyang; Huang Qin clears constrained Heat; processed Ban Xia and Sheng Jiang harmonize the Stomach; Ren Shen, Da Zao and Gan Cao support upright Qi.',
      members: [
        ['Xiao Chai Hu Tang', 'Foundational Shaoyang harmonizer', 'Shaoyang disorder', 'Alternating chills and fever, bitter taste, dry throat and wiry pulse.'],
        ['Da Chai Hu Tang', 'Strengthens interior clearing and downward drainage with Zhi Shi, Da Huang and Shao Yao', 'Shaoyang disorder with Yangming excess', 'Alternation plus epigastric hardness/fullness, constipation or abdominal pain.'],
        ['Chai Hu Gui Zhi Tang', 'Combines reduced Xiao Chai Hu Tang and Gui Zhi Tang strategies', 'Concurrent Shaoyang and Taiyang disorders', 'Alternating chills/fever plus body aches or Exterior signs.'],
        ['Chai Hu Jia Long Gu Mu Li Tang', 'Adds settling minerals and downward-directing herbs to a Chai Hu framework', 'Shaoyang disorder with severe constraint and spirit disturbance', 'Chest fullness, fright, palpitations, insomnia, irritability or difficult urination.'],
      ],
      rule: 'Confirm the Shaoyang pivot first; then identify the companion layer: Yangming bowel, Taiyang exterior or disturbed Spirit.',
      memory: '<strong>Xiao = pivot;</strong> <strong>Da = bowel excess;</strong> <strong>Gui Zhi = Taiyang;</strong> <strong>Long Gu/Mu Li = unsettled Spirit.</strong>',
      safety: 'These formulas are not interchangeable for nonspecific fever. Xiao Chai Hu Tang should not be combined with interferon because of reported serious pulmonary reactions; processed Ban Xia is required.'
    },
    es: {
      root: 'Xiao Chai Hu Tang armoniza un trastorno Shaoyang mitad Exterior-mitad Interior y restaura el pivote.',
      architecture: 'Chai Hu ventila Shaoyang; Huang Qin aclara Calor constreñido; Ban Xia procesada y Sheng Jiang armonizan Estómago; Ren Shen, Da Zao y Gan Cao sostienen Qi correcto.',
      members: [
        ['Xiao Chai Hu Tang', 'Armonizador Shaoyang fundamental', 'Trastorno Shaoyang', 'Alternancia de escalofríos y fiebre, sabor amargo, garganta seca y pulso de cuerda.'],
        ['Da Chai Hu Tang', 'Refuerza aclaramiento y descenso con Zhi Shi, Da Huang y Shao Yao', 'Shaoyang con exceso Yangming', 'Alternancia más dureza/plenitud epigástrica, estreñimiento o dolor abdominal.'],
        ['Chai Hu Gui Zhi Tang', 'Combina estrategias reducidas de Xiao Chai Hu Tang y Gui Zhi Tang', 'Trastornos Shaoyang y Taiyang simultáneos', 'Alternancia más dolores corporales o signos Exteriores.'],
        ['Chai Hu Jia Long Gu Mu Li Tang', 'Añade minerales descendentes y calmantes a una estructura Chai Hu', 'Shaoyang con constricción intensa y perturbación del Shen', 'Plenitud torácica, sobresalto, palpitaciones, insomnio, irritabilidad o micción difícil.'],
      ],
      rule: 'Confirma primero el pivote Shaoyang y luego identifica la capa acompañante: intestino Yangming, Exterior Taiyang o Shen perturbado.',
      memory: '<strong>Xiao = pivote;</strong> <strong>Da = exceso intestinal;</strong> <strong>Gui Zhi = Taiyang;</strong> <strong>Long Gu/Mu Li = Shen inquieto.</strong>',
      safety: 'No son intercambiables ante fiebre inespecífica. Xiao Chai Hu Tang no debe combinarse con interferón por reacciones pulmonares graves reportadas; Ban Xia debe estar procesada.'
    }
  },
  {
    slug: 'bai-hu-tang-family', title: 'Bai Hu Tang Family',
    category: { en: 'Clear Qi-Level Heat', es: 'Aclarar Calor del nivel Qi' },
    summary: { en: 'Differentiate pure blazing Qi-level Heat from Qi-fluid injury, painful obstruction or lingering post-febrile Heat.', es: 'Diferencia Calor intenso puro del nivel Qi de lesión de Qi-líquidos, obstrucción dolorosa o Calor residual posfebril.' },
    search: 'bai hu tang family bai hu jia ren shen tang bai hu jia gui zhi tang zhu ye shi gao tang qi level heat thirst sweating pulse',
    en: {
      root: 'Bai Hu Tang clears blazing Yangming Qi-level Heat and protects fluids.',
      architecture: 'Shi Gao powerfully clears Heat; Zhi Mu clears Heat and enriches Yin; Geng Mi and Zhi Gan Cao protect Stomach Qi and fluids.',
      members: [
        ['Bai Hu Tang', 'Four-ingredient Qi-level Heat base', 'Blazing Yangming Qi-level Heat', 'The four greats: great fever, great thirst, great sweating and a great/forceful pulse.'],
        ['Bai Hu Jia Ren Shen Tang', 'Bai Hu Tang + Ren Shen', 'Qi-level Heat with Qi and fluid injury', 'The four greats plus exhaustion, weak pulse or persistent thirst after heavy sweating.'],
        ['Bai Hu Jia Gui Zhi Tang', 'Bai Hu Tang + Gui Zhi', 'Qi-level Heat with painful channel obstruction', 'High Heat signs together with joint or bone pain.'],
        ['Zhu Ye Shi Gao Tang', 'A later-stage Shi Gao formula with Zhu Ye, Ren Shen and Mai Men Dong', 'Residual Heat after febrile disease with Qi-Yin injury', 'Low lingering fever, thirst, fatigue and nausea or retching.'],
      ],
      rule: 'Bai Hu requires exuberant Heat. Add Ren Shen for collapse of Qi/fluids, Gui Zhi for painful obstruction, or move to Zhu Ye Shi Gao when Heat is residual and deficiency dominates.',
      memory: '<strong>Bai Hu = four greats;</strong> <strong>Ren Shen = depleted;</strong> <strong>Gui Zhi = joints;</strong> <strong>Zhu Ye = recovery stage.</strong>',
      safety: 'Avoid in Cold from deficiency or without genuine blazing Heat. Shi Gao is traditionally pre-decocted; persistent high fever or altered mental status needs medical assessment.'
    },
    es: {
      root: 'Bai Hu Tang aclara Calor Yangming intenso del nivel Qi y protege líquidos.',
      architecture: 'Shi Gao aclara poderosamente Calor; Zhi Mu aclara y enriquece Yin; Geng Mi y Zhi Gan Cao protegen Qi y líquidos de Estómago.',
      members: [
        ['Bai Hu Tang', 'Base de cuatro ingredientes para Calor nivel Qi', 'Calor Yangming intenso del nivel Qi', 'Los cuatro grandes: fiebre, sed, sudor y pulso grandes/fuertes.'],
        ['Bai Hu Jia Ren Shen Tang', 'Bai Hu Tang + Ren Shen', 'Calor nivel Qi con lesión de Qi y líquidos', 'Los cuatro grandes más agotamiento, pulso débil o sed persistente tras sudoración intensa.'],
        ['Bai Hu Jia Gui Zhi Tang', 'Bai Hu Tang + Gui Zhi', 'Calor nivel Qi con obstrucción dolorosa de canales', 'Signos de Calor alto junto con dolor articular u óseo.'],
        ['Zhu Ye Shi Gao Tang', 'Fórmula tardía con Shi Gao, Zhu Ye, Ren Shen y Mai Men Dong', 'Calor residual posfebril con lesión de Qi-Yin', 'Fiebre baja persistente, sed, fatiga y náusea o arcadas.'],
      ],
      rule: 'Bai Hu exige Calor exuberante. Añade Ren Shen si se agotan Qi/líquidos, Gui Zhi si hay obstrucción dolorosa o pasa a Zhu Ye Shi Gao cuando el Calor es residual y domina deficiencia.',
      memory: '<strong>Bai Hu = cuatro grandes;</strong> <strong>Ren Shen = agotamiento;</strong> <strong>Gui Zhi = articulaciones;</strong> <strong>Zhu Ye = recuperación.</strong>',
      safety: 'Evitar en Frío por deficiencia o sin Calor intenso verdadero. Shi Gao suele precoerse; fiebre alta persistente o alteración mental requiere evaluación médica.'
    }
  },
  {
    slug: 'si-ni-tang-family', title: 'Si Ni Tang Family',
    category: { en: 'Rescue Devastated Yang', es: 'Rescatar Yang devastado' },
    summary: { en: 'Grade Yang collapse by severity, pulse recovery and concurrent Qi-fluid injury.', es: 'Gradúa el colapso de Yang por gravedad, recuperación del pulso y lesión concomitante de Qi-líquidos.' },
    search: 'si ni tang family tong mai si ni tang si ni jia ren shen tang hui yang jiu ji tang yang collapse cold extremities pulse diarrhea',
    en: {
      root: 'Si Ni Tang restores devastated Yang and warms the Middle in severe interior Cold.',
      architecture: 'Processed Fu Zi revives Yang, Gan Jiang warms and secures the rescue, and Zhi Gan Cao supports Qi and moderates toxicity.',
      members: [
        ['Si Ni Tang', 'Processed Fu Zi + Gan Jiang + Zhi Gan Cao', 'Yang collapse with interior Cold', 'Cold extremities, profuse clear diarrhea, absence of thirst and a deep-faint pulse.'],
        ['Tong Mai Si Ni Tang', 'Strengthens the Si Ni warming doses', 'Severe Yang collapse with true Cold and false Heat', 'Extremely faint pulse plus flushed face or other apparent-Heat signs that do not match the deep Cold.'],
        ['Si Ni Jia Ren Shen Tang', 'Si Ni Tang + Ren Shen', 'Yang collapse with Qi and fluid injury', 'Collapse after severe vomiting/diarrhea with faint pulse and marked exhaustion.'],
        ['Hui Yang Jiu Ji Tang', 'Expands the Si Ni rescue core with Qi and Blood support', 'Devastated Yang with broader Qi-Blood collapse', 'Cold limbs, faint pulse, sweating and severe systemic weakness.'],
      ],
      rule: 'All members require genuine deep Cold and collapse; apparent Heat with a nearly absent pulse points to Tong Mai, while fluid/Qi depletion points to Ren Shen.',
      memory: '<strong>Si Ni = rescue;</strong> <strong>Tong Mai = pulse and false Heat;</strong> <strong>Jia Ren Shen = depleted fluids/Qi;</strong> <strong>Hui Yang = broader collapse.</strong>',
      safety: 'These are high-risk emergency-level presentations. Fu Zi must be properly processed and pre-decocted; suspected shock, altered consciousness or unstable vital signs requires emergency care, not self-treatment.'
    },
    es: {
      root: 'Si Ni Tang restaura Yang devastado y calienta el Centro en Frío Interior grave.',
      architecture: 'Fu Zi procesado revive Yang, Gan Jiang calienta y consolida el rescate, y Zhi Gan Cao sostiene Qi y modera toxicidad.',
      members: [
        ['Si Ni Tang', 'Fu Zi procesado + Gan Jiang + Zhi Gan Cao', 'Colapso de Yang con Frío Interior', 'Extremidades frías, diarrea clara profusa, ausencia de sed y pulso profundo-casi imperceptible.'],
        ['Tong Mai Si Ni Tang', 'Refuerza las dosis calientes de Si Ni', 'Colapso Yang grave con Frío verdadero y Calor falso', 'Pulso casi ausente más cara roja u otros signos aparentes de Calor que contradicen el Frío profundo.'],
        ['Si Ni Jia Ren Shen Tang', 'Si Ni Tang + Ren Shen', 'Colapso Yang con lesión de Qi y líquidos', 'Colapso tras vómito/diarrea grave con pulso débil y agotamiento marcado.'],
        ['Hui Yang Jiu Ji Tang', 'Amplía el núcleo de rescate con apoyo de Qi y Sangre', 'Yang devastado con colapso más amplio de Qi-Sangre', 'Extremidades frías, pulso débil, sudor y debilidad sistémica grave.'],
      ],
      rule: 'Todos exigen Frío profundo y colapso verdaderos; Calor aparente con pulso casi ausente señala Tong Mai, y agotamiento de Qi/líquidos señala Ren Shen.',
      memory: '<strong>Si Ni = rescate;</strong> <strong>Tong Mai = pulso y Calor falso;</strong> <strong>Jia Ren Shen = Qi/líquidos agotados;</strong> <strong>Hui Yang = colapso amplio.</strong>',
      safety: 'Son presentaciones de alto riesgo y nivel de emergencia. Fu Zi debe estar procesado y precoerse; shock, conciencia alterada o signos vitales inestables requieren urgencias, no autotratamiento.'
    }
  },
  {
    slug: 'wu-ling-san-family', title: 'Wu Ling San Family',
    category: { en: 'Promote Urination · Transform Qi', es: 'Promover micción · Transformar Qi' },
    summary: { en: 'Follow impaired water transformation into Damp accumulation, jaundice or Heat with Yin injury.', es: 'Sigue la transformación alterada del agua hacia acumulación de Humedad, ictericia o Calor con lesión de Yin.' },
    search: 'wu ling san family wei ling tang yin chen wu ling san zhu ling tang water metabolism edema urination thirst damp jaundice heat yin',
    en: {
      root: 'Wu Ling San promotes urination and restores Bladder Qi transformation while warming the Yang that moves fluids.',
      architecture: 'Ze Xie, Zhu Ling and Fu Ling drain water; Bai Zhu strengthens Spleen transport; Gui Zhi warms Yang and transforms Qi.',
      members: [
        ['Wu Ling San', 'Five-ingredient water-transforming base', 'Taiyang water accumulation with impaired Qi transformation', 'Urinary difficulty, thirst with vomiting after drinking, edema or headache.'],
        ['Wei Ling Tang', 'Wu Ling San + Ping Wei San', 'Water accumulation with stronger Spleen Dampness', 'Edema or diarrhea plus abdominal fullness, heaviness and greasy coat.'],
        ['Yin Chen Wu Ling San', 'Wu Ling San + Yin Chen', 'Damp jaundice with impaired water metabolism', 'Jaundice plus urinary difficulty, edema or thirst.'],
        ['Zhu Ling Tang', 'Keeps the three draining herbs; replaces Bai Zhu/Gui Zhi with Hua Shi/E Jiao', 'Water accumulation with Heat and Yin injury', 'Urinary difficulty plus thirst, irritability or urinary bleeding without Cold signs.'],
      ],
      rule: 'Gui Zhi/Bai Zhu indicate warming transformation; add Ping Wei for heavier Dampness, Yin Chen for jaundice, or choose Zhu Ling when Heat and damaged Yin prohibit warming.',
      memory: '<strong>Wu Ling = transform water;</strong> <strong>Wei Ling = Damp middle;</strong> <strong>Yin Chen = yellow;</strong> <strong>Zhu Ling = hot and dry.</strong>',
      safety: 'Water-draining formulas can alter fluid and electrolyte balance and raise lithium levels. Anuria, severe dehydration, shortness of breath or rapidly worsening edema needs urgent assessment.'
    },
    es: {
      root: 'Wu Ling San promueve micción y restaura transformación de Qi de Vejiga mientras calienta el Yang que mueve líquidos.',
      architecture: 'Ze Xie, Zhu Ling y Fu Ling drenan agua; Bai Zhu fortalece transporte del Bazo; Gui Zhi calienta Yang y transforma Qi.',
      members: [
        ['Wu Ling San', 'Base de cinco ingredientes que transforma agua', 'Acumulación de agua Taiyang con transformación de Qi alterada', 'Micción difícil, sed con vómito al beber, edema o cefalea.'],
        ['Wei Ling Tang', 'Wu Ling San + Ping Wei San', 'Acumulación de agua con Humedad de Bazo más fuerte', 'Edema o diarrea más plenitud abdominal, pesadez y capa grasosa.'],
        ['Yin Chen Wu Ling San', 'Wu Ling San + Yin Chen', 'Ictericia por Humedad con metabolismo de agua alterado', 'Ictericia más micción difícil, edema o sed.'],
        ['Zhu Ling Tang', 'Conserva tres drenantes; sustituye Bai Zhu/Gui Zhi por Hua Shi/E Jiao', 'Acumulación de agua con Calor y lesión de Yin', 'Micción difícil más sed, irritabilidad o sangrado urinario sin signos de Frío.'],
      ],
      rule: 'Gui Zhi/Bai Zhu indican transformación caliente; añade Ping Wei para Humedad más pesada, Yin Chen para ictericia o elige Zhu Ling cuando Calor y Yin lesionado impiden calentar.',
      memory: '<strong>Wu Ling = transforma agua;</strong> <strong>Wei Ling = Centro húmedo;</strong> <strong>Yin Chen = amarillo;</strong> <strong>Zhu Ling = caliente y seco.</strong>',
      safety: 'Fórmulas drenantes pueden alterar líquidos/electrolitos y elevar litio. Anuria, deshidratación grave, disnea o edema que empeora rápidamente requiere evaluación urgente.'
    }
  },
  {
    slug: 'zhu-yu-tang-family', title: 'Zhu Yu Tang Family',
    category: { en: 'Invigorate Blood by Location', es: 'Vigorizar Sangre según localización' },
    summary: { en: 'Use Wang Qing-ren’s location map to distinguish stasis in the head, chest, diaphragm, lower abdomen or channels.', es: 'Usa el mapa anatómico de Wang Qing-ren para distinguir estasis en cabeza, pecho, diafragma, abdomen inferior o canales.' },
    search: 'zhu yu tang family xue fu zhu yu tang ge xia zhu yu tang shao fu zhu yu tang shen tong zhu yu tang tong qiao huo xue tang blood stasis head chest abdomen body pain',
    en: {
      root: 'The Zhu Yu formulas invigorate Blood and dispel stasis, but the decisive exam distinction is anatomical location and temperature.',
      architecture: 'Tao Ren, Hong Hua, Chuan Xiong and Chi Shao recur across the family. Guiding and Qi-regulating herbs redirect the strategy to a specific region.',
      members: [
        ['Tong Qiao Huo Xue Tang', 'Directs a Blood-moving core to the head and sensory orifices', 'Blood stasis in the head', 'Chronic fixed headache, dark complexion or sensory disturbance after trauma.'],
        ['Xue Fu Zhu Yu Tang', 'Combines Blood movers with Chai Hu, Zhi Ke and guiding herbs', 'Blood stasis in the chest', 'Fixed chest pain, irritability, insomnia or prolonged hiccup.'],
        ['Ge Xia Zhu Yu Tang', 'Concentrates stasis-dispersing action below the diaphragm', 'Blood stasis in diaphragm and upper abdomen', 'Fixed abdominal masses or pain in the hypochondrium/upper abdomen.'],
        ['Shao Fu Zhu Yu Tang', 'Adds a distinctly warming lower-abdominal strategy', 'Cold Blood stasis in the lower abdomen', 'Menstrual pain, infertility or fixed lower-abdominal pain improved by warmth.'],
        ['Shen Tong Zhu Yu Tang', 'Adds channel-opening and Wind-Damp addressing herbs', 'Blood stasis in channels and body', 'Chronic fixed body, shoulder, back or limb pain.'],
      ],
      rule: 'Locate first: head, chest, diaphragm, lower abdomen or channels. In the lower abdomen, Cold and improvement with warmth make Shao Fu decisive.',
      memory: '<strong>Tong Qiao = head;</strong> <strong>Xue Fu = chest;</strong> <strong>Ge Xia = diaphragm;</strong> <strong>Shao Fu = cold pelvis;</strong> <strong>Shen Tong = body pain.</strong>',
      safety: 'Strong Blood-moving formulas require pregnancy, bleeding, anticoagulant and surgery review. New chest pain, neurologic deficit or severe abdominal pain requires urgent biomedical evaluation.'
    },
    es: {
      root: 'Las fórmulas Zhu Yu vigorizan Sangre y eliminan estasis, pero la distinción decisiva del examen es localización anatómica y temperatura.',
      architecture: 'Tao Ren, Hong Hua, Chuan Xiong y Chi Shao se repiten. Hierbas guía y reguladoras de Qi redirigen la estrategia a una región específica.',
      members: [
        ['Tong Qiao Huo Xue Tang', 'Dirige el núcleo movilizador hacia cabeza y orificios sensoriales', 'Estasis de Sangre en cabeza', 'Cefalea fija crónica, tez oscura o alteración sensorial después de trauma.'],
        ['Xue Fu Zhu Yu Tang', 'Combina movilizadores con Chai Hu, Zhi Ke y hierbas guía', 'Estasis de Sangre en pecho', 'Dolor torácico fijo, irritabilidad, insomnio o hipo prolongado.'],
        ['Ge Xia Zhu Yu Tang', 'Concentra acción dispersante debajo del diafragma', 'Estasis en diafragma y abdomen superior', 'Masas fijas o dolor en hipocondrio/abdomen superior.'],
        ['Shao Fu Zhu Yu Tang', 'Añade estrategia claramente caliente para abdomen inferior', 'Estasis de Sangre por Frío en abdomen inferior', 'Dolor menstrual, infertilidad o dolor fijo hipogástrico mejorado con calor.'],
        ['Shen Tong Zhu Yu Tang', 'Añade hierbas que abren canales y atienden Viento-Humedad', 'Estasis en canales y cuerpo', 'Dolor fijo crónico corporal, de hombro, espalda o extremidades.'],
      ],
      rule: 'Localiza primero: cabeza, pecho, diafragma, abdomen inferior o canales. En abdomen inferior, Frío y mejoría con calor hacen decisiva Shao Fu.',
      memory: '<strong>Tong Qiao = cabeza;</strong> <strong>Xue Fu = pecho;</strong> <strong>Ge Xia = diafragma;</strong> <strong>Shao Fu = pelvis fría;</strong> <strong>Shen Tong = dolor corporal.</strong>',
      safety: 'Movilizadores fuertes exigen revisar embarazo, sangrado, anticoagulantes y cirugía. Dolor torácico nuevo, déficit neurológico o dolor abdominal grave requiere evaluación biomédica urgente.'
    }
  }
];

function esc(value) {
  return String(value).replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;');
}

function page(family, language) {
  const es = language === 'es';
  const data = family[language];
  const index = es ? 'families-es.html' : 'families.html';
  const alternate = `${family.slug}${es ? '' : '-es'}.html`;
  const rows = data.members.map(member => `<tr><th scope="row">${esc(member[0])}</th><td>${esc(member[1])}</td><td>${esc(member[2])}</td><td>${esc(member[3])}</td></tr>`).join('\n');
  return `<!doctype html><html lang="${es ? 'es' : 'en'}"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="description" content="${esc(family.title)}"><title>${esc(family.title)} | AcupunctureTests</title><link rel="alternate" hreflang="${es ? 'en' : 'es'}" href="${alternate}"><link rel="stylesheet" href="comparisons.css?v=${cssVersion}"></head><body>
<header class="comparison-topbar"><div class="comparison-shell comparison-topbar__inner" style="flex-wrap:wrap;padding:10px 0"><a class="comparison-brand" href="${index}"><span class="comparison-brand__mark" aria-hidden="true">☯</span><span>${es ? 'Familias de Fórmulas' : 'Formula Families'}</span></a><span style="display:flex;gap:8px;flex-wrap:wrap"><a class="comparison-back" href="${alternate}">${es ? 'English' : 'Español'}</a><a class="comparison-back" href="${index}">${es ? '← Volver a Familias' : '← Back to Families'}</a></span></div></header>
<main class="comparison-shell"><article class="study-note"><p class="comparison-eyebrow">${es ? 'Familia de fórmulas' : 'Formula family'}</p><h1 class="study-note__title">${esc(family.title)}</h1>
<section class="study-note__block"><h2 class="study-note__heading">${es ? 'Fórmula raíz y arquitectura' : 'Root formula and architecture'}</h2><table class="study-note__focus"><tbody><tr><th scope="row">${es ? 'Raíz' : 'Root'}</th><td>${data.root}</td></tr><tr><th scope="row">${es ? 'Arquitectura' : 'Architecture'}</th><td>${data.architecture}</td></tr></tbody></table></section>
<section class="study-note__block"><h2 class="study-note__heading study-note__heading--orange">${es ? 'Miembros de la familia' : 'Family members'}</h2><table><thead><tr><th>${es ? 'Fórmula' : 'Formula'}</th><th>${es ? 'Cambio estructural' : 'Structural change'}</th><th>${es ? 'Patrón' : 'Pattern'}</th><th>${es ? 'Pista decisiva' : 'Decisive clue'}</th></tr></thead><tbody>${rows}</tbody></table></section>
<section class="study-note__block"><h2 class="study-note__heading study-note__heading--purple">${es ? 'Regla de estudio' : 'Study rule'}</h2><table class="study-note__focus"><tbody><tr><th>${es ? 'Cómo elegir' : 'How to choose'}</th><td>${data.rule}</td></tr><tr><th>${es ? 'Memoria' : 'Memory cue'}</th><td>${data.memory}</td></tr><tr><th>${es ? 'Seguridad' : 'Safety'}</th><td>${data.safety}</td></tr></tbody></table></section>
</article></main><footer class="comparison-footer"><div class="comparison-shell">${es ? 'Grupo independiente · Familias de Fórmulas' : 'Independent group · Formula Families'}</div></footer></body></html>`;
}

function indexPage(language) {
  const es = language === 'es';
  const cards = families.map(family => `<article class="comparison-card" data-type="families" data-search="${esc(family.search)}"><span class="comparison-card__type">${es ? 'Familia' : 'Family'}</span><h3>${esc(family.title)}</h3><p>${esc(family.summary[language])}</p><div class="comparison-card__category"><strong>${es ? 'Categoría:' : 'Category:'}</strong><br>${esc(family.category[language])}</div><a class="comparison-card__open" href="${family.slug}${es ? '-es' : ''}.html">${es ? 'Abrir Familia →' : 'Open Family →'}</a></article>`).join('\n');
  return `<!doctype html><html lang="${es ? 'es' : 'en'}"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="description" content="${es ? 'Familias fundamentales de fórmulas de medicina china.' : 'Fundamental Chinese medicine formula families.'}"><title>${es ? 'Familias de Fórmulas' : 'Formula Families'} | AcupunctureTests</title><link rel="alternate" hreflang="${es ? 'en' : 'es'}" href="families${es ? '' : '-es'}.html"><link rel="stylesheet" href="comparisons.css?v=${cssVersion}"><script defer src="comparisons.js?v=20261025-1"></script></head><body>
<header class="comparison-topbar"><div class="comparison-shell comparison-topbar__inner"><a class="comparison-brand" href="/my/"><span class="comparison-brand__mark" aria-hidden="true">☯</span><span>AcupunctureTests</span></a><span style="display:flex;gap:8px"><a class="comparison-back" href="families${es ? '' : '-es'}.html">${es ? 'English' : 'Español'}</a><a class="comparison-back" href="/my/">${es ? '← Dashboard' : '← Back to Dashboard'}</a></span></div></header>
<main class="comparison-shell"><section class="comparison-hero"><p class="comparison-eyebrow">${es ? 'Grupo 5 · Linajes' : 'Group 5 · Lineages'}</p><h1>${es ? 'Familias de Fórmulas' : 'Formula Families'}</h1><p>${es ? 'Estudia cómo una fórmula raíz se transforma al añadir, retirar o sustituir unidades funcionales para responder a un patrón diferente.' : 'Study how a root formula changes when functional units are added, removed or replaced to address a different pattern.'}</p></section>
<nav class="comparison-collection-links" aria-label="${es ? 'Grupos de estudio' : 'Study groups'}"><a href="index.html">${es ? 'Comparaciones de Hierbas' : 'Single Herbs Comparisons'}</a><a href="formulas${es ? '-es' : ''}.html">${es ? 'Comparaciones de Fórmulas' : 'Formula Comparisons'}</a><a href="pathologies.html">${es ? 'Patologías' : 'Pathology Comparisons'}</a><a href="patterns${es ? '-es' : ''}.html">${es ? 'Patrones' : 'Pattern Comparisons'}</a><a aria-current="page" href="families${es ? '-es' : ''}.html">${es ? 'Familias de Fórmulas' : 'Formula Families'}</a></nav>
<section class="comparison-tools comparison-tools--search-only"><label><span class="sr-only">${es ? 'Buscar' : 'Search'}</span><input class="comparison-search" type="search" placeholder="${es ? 'Buscar familias o fórmulas' : 'Search families or formulas'}" data-comparison-search></label></section><section><header class="comparison-summary"><h2>${es ? 'Familias disponibles' : 'Available families'}</h2><p><span data-visible-count>${families.length}</span> ${es ? 'familias de fórmulas' : 'formula families'}</p></header><div class="comparison-grid" data-comparison-grid>${cards}</div><p class="comparison-empty" data-comparison-empty hidden>${es ? 'No hay familias coincidentes.' : 'No Formula Families match this search.'}</p></section></main><footer class="comparison-footer"><div class="comparison-shell">${es ? 'Material independiente de estudio.' : 'Independent study material.'}</div></footer></body></html>`;
}

for (const family of families) {
  writeFileSync(resolve(root, `${family.slug}.html`), page(family, 'en'));
  writeFileSync(resolve(root, `${family.slug}-es.html`), page(family, 'es'));
}
writeFileSync(resolve(root, 'families.html'), indexPage('en'));
writeFileSync(resolve(root, 'families-es.html'), indexPage('es'));
console.log(`Generated ${families.length * 2} family pages and 2 indexes`);
