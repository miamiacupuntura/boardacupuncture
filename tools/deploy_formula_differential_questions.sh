#!/usr/bin/env bash
set -euo pipefail
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
TARGET="$MOODLE_ROOT/blocks/myprogress/formula-ingredient-hierarchy.php"
for tool in python3 php cp stat install mktemp date; do
    command -v "$tool" >/dev/null || { echo "ERROR: falta $tool" >&2; exit 1; }
done
[[ -f "$TARGET" ]] || { echo 'ERROR: falta la página de fichas.' >&2; exit 1; }
TEMP_DIR="$(mktemp -d)"
trap 'rm -rf -- "$TEMP_DIR"' EXIT
python3 - "$TARGET" "$TEMP_DIR/page.php" <<'PY'
import ast,json,re,sys
from pathlib import Path
source=Path(sys.argv[1]).read_text()
marker='// formula-differential-questions-v1'
if marker in source:
    raise SystemExit('Las preguntas con pistas diferenciales ya están instaladas; no se cambió nada.')
if '<!-- ingredient-role-flashcards-v1 -->' not in source:
    raise SystemExit('ERROR: no se encontraron las fichas por ingrediente.')
keys={
 'Ma Huang Tang':[
   'Strong chills, no sweating, body aches and wheezing; the exterior is tightly closed.',
   'Escalofríos intensos, ausencia de sudor, dolor corporal y sibilancias; el exterior está cerrado.'],
 'Gui Zhi Tang':[
   'Spontaneous sweating and aversion to wind; Ying and Wei are out of harmony.',
   'Sudoración espontánea y aversión al viento; hay desarmonía entre Ying y Wei.'],
 'Ge Gen Tang':[
   'No sweating and a markedly stiff neck; Sheng Jiang and Da Zao help harmonize Ying and Wei.',
   'Ausencia de sudor y marcada rigidez de nuca; Sheng Jiang y Da Zao ayudan a armonizar Ying y Wei.'],
 'Sang Ju Yin':[
   'Mild early Wind-Heat: cough is more prominent than fever or thirst.',
   'Viento-Calor inicial leve: la tos predomina sobre la fiebre o la sed.'],
 'Yin Qiao San':[
   'Early Wind-Heat with prominent sore throat, thirst and fever greater than chills.',
   'Viento-Calor inicial con dolor de garganta destacado, sed y fiebre mayor que los escalofríos.'],
 'Chai Ge Jie Ji Tang':[
   'Exterior Cold is turning into Heat: fever rises as chills lessen, with orbital pain and thirst.',
   'El Frío exterior se transforma en Calor: aumenta la fiebre, disminuyen los escalofríos y aparecen dolor orbitario y sed.'],
 'Ren Shen Bai Du San':[
   'Wind-Cold-Damp with heavy body aches in a weak or convalescent patient.',
   'Viento-Frío-Humedad con pesadez y dolor corporal en una persona débil o convaleciente.'],
 'Bai Hu Tang':[
   'The Four Greats: high fever, intense thirst, profuse sweating and a large surging pulse.',
   'Los cuatro grandes: fiebre alta, sed intensa, sudoración abundante y pulso grande y desbordante.'],
 'Huang Lian Jie Du Tang':[
   'Fire-Toxin in all Three Burners, with high fever, severe irritability and a red tongue.',
   'Fuego-Toxina en los tres calentadores, con fiebre alta, irritabilidad intensa y lengua roja.'],
 'Ma Xing Shi Gan Tang':[
   'Lung Heat with sweating, thirst and wheezing with flaring nostrils.',
   'Calor de Pulmón con sudoración, sed y sibilancias con aleteo nasal.'],
 'Long Dan Xie Gan Tang':[
   'Liver Fire above or Damp-Heat below: painful red eyes or genital itching and burning urination.',
   'Fuego de Hígado arriba o Humedad-Calor abajo: ojos rojos dolorosos o prurito genital y ardor al orinar.'],
 'Qing Hao Bie Jia Tang':[
   'Night fever recedes by morning without sweating, following a febrile illness that depleted Yin.',
   'Fiebre nocturna que cede por la mañana sin sudor, tras una enfermedad febril que agotó el Yin.'],
 'Da Cheng Qi Tang':[
   'Severe constipation with a hard, full abdomen; pain is worse on pressure.',
   'Estreñimiento intenso con abdomen duro y pleno; el dolor empeora con la presión.'],
 'Xiao Cheng Qi Tang':[
   'Moderate constipation with focal distention and fullness, but less dryness than the stronger purging pattern.',
   'Estreñimiento moderado con distensión focal y plenitud, pero menor sequedad que en el patrón purgante intenso.'],
 'Tiao Wei Cheng Qi Tang':[
   'Dryness predominates: mild constipation and irritability without marked abdominal distention or fullness.',
   'Predomina la sequedad: estreñimiento leve e irritabilidad sin distensión ni plenitud abdominal marcadas.'],
 'Xiao Chai Hu Tang':[
   'Alternating chills and fever, bitter taste, hypochondriac fullness and nausea: a Shaoyang pattern.',
   'Alternancia de escalofríos y fiebre, sabor amargo, plenitud hipocondríaca y náuseas: patrón Shaoyang.'],
 'Si Ni San':[
   'Cold fingers and toes with a warm trunk; constrained Qi prevents Yang from reaching the limbs.',
   'Dedos de manos y pies fríos con tronco caliente; el Qi estancado impide que el Yang llegue a las extremidades.'],
 'Xiao Yao San':[
   'Stress-related hypochondriac distention with fatigue and poor appetite: Liver constraint, Blood deficiency and Spleen weakness.',
   'Distensión hipocondríaca ligada al estrés, fatiga y poco apetito: estancamiento de Hígado, deficiencia de Sangre y debilidad de Bazo.'],
 'Ban Xia Xie Xin Tang':[
   'Soft, nearly painless epigastric focal fullness with vomiting, borborygmus and diarrhea; Cold and Heat are mixed.',
   'Plenitud focal epigástrica blanda y casi indolora con vómitos, borborigmos y diarrea; se mezclan Frío y Calor.'],
}
preparations={
 'Zhi Gan Cao':[
   'Honey-prepared licorice: emphasizes Qi support and harmonization; its rank still depends on the formula.',
   'Regaliz preparado con miel: enfatiza el apoyo al Qi y la armonización; su rango depende de la fórmula.'],
 'Sheng Gan Cao':[
   'Unprepared licorice: favors Heat clearing and throat benefit while also harmonizing; it is distinct from honey-prepared licorice.',
   'Regaliz sin preparar: favorece aclarar Calor y beneficiar la garganta, además de armonizar; se distingue del preparado con miel.'],
 'Gan Cao':[
   'This profile does not specify raw or honey-prepared licorice; do not infer the preparation.',
   'Este perfil no precisa si el regaliz es sin preparar o preparado con miel; no se debe inferir.'],
 'Sheng Jiang':[
   'Fresh ginger: favors exterior release and Stomach harmonization; it is not dried ginger.',
   'Jengibre fresco: favorece liberar el exterior y armonizar el Estómago; no es jengibre seco.'],
 'Gan Jiang':[
   'Dried ginger: emphasizes warming the interior; it is distinct from fresh ginger and from charred ginger (Pao Jiang).',
   'Jengibre seco: enfatiza calentar el interior; se distingue del fresco y del carbonizado (Pao Jiang).'],
 'Sheng Di Huang':[
   'Unprepared Rehmannia root: cools Blood, nourishes Yin and generates fluids; do not substitute the Blood-tonifying emphasis of Shu Di Huang.',
   'Raíz de Rehmannia sin preparar: enfría la Sangre, nutre Yin y genera líquidos; no debe confundirse con el énfasis tonificante de Sangre de Shu Di Huang.'],
 'Shu Di Huang':[
   'Prepared Rehmannia root: emphasizes nourishing Blood and Yin; it is distinct from the Blood-cooling action of Sheng Di Huang.',
   'Raíz de Rehmannia preparada: enfatiza nutrir Sangre y Yin; se distingue de la acción de enfriar la Sangre de Sheng Di Huang.'],
 'Ban Xia':[
   'The profile does not specify the processing method; the name alone does not identify a raw or particular processed form.',
   'El perfil no especifica el método de procesamiento; el nombre solo no identifica una forma cruda ni un procesamiento concreto.'],
}
# Audit exact formula ingredient names, including processing prefixes. Never
# classify a name as processed merely because it starts with "Zhi" or "Sheng".
datafile=Path(sys.argv[1]).with_name('formulas-study-guide-data.json')
profiles={p['name']:p for p in json.loads(datafile.read_text())['formulas']}
match=re.search(r'const assignments=(\{.*?\n \});',source,re.S)
if not match:raise SystemExit('ERROR: no se encontró la clasificación de ingredientes.')
assignments=ast.literal_eval(re.sub(r'([{,]\s*)(chen|zuo|shi)(\s*:)',r"\1'\2'\3",match[1]))
if set(assignments)!=set(keys):raise SystemExit('ERROR: las pistas no cubren exactamente las fórmulas instaladas.')
for name,groups in assignments.items():
    profile=profiles[name]
    ingredients=profile['ingredients']
    herbs={h for group in groups.values() for h in group}
    herbs.update(h.strip() for h in profile['kings'].split(','))
    for herb in herbs:
        # Prevent generic Gan Cao from silently matching Zhi/Sheng Gan Cao.
        canonical=re.sub(r'\b(?:Zhi|Sheng) Gan Cao\b','',ingredients) if herb=='Gan Cao' else ingredients
        if not re.search(r'(?<![A-Za-z])'+re.escape(herb)+r'(?![A-Za-z])',canonical):
            raise SystemExit('ERROR: revisar nombre o procesamiento: '+name+' / '+herb)
if len(keys)!=19 or len({v[0] for v in keys.values()})!=19 or len({v[1] for v in keys.values()})!=19:
    raise SystemExit('ERROR: las claves diferenciales deben ser distintas para cada fórmula.')
old="node('p',es?'¿En qué fórmula cumple este papel y qué hace?':'In which formula does it serve this role, and what does it do?')"
needle="const article=node('article',null,'formula-hierarchy__card');"
if source.count(old)!=1 or source.count(needle)!=1 or source.count(' const cards=[];')!=1:
    raise SystemExit('ERROR: la página cambió; se detuvo la actualización para conservarla.')
insert=marker+'\n const differentialKeys='+json.dumps(keys,ensure_ascii=False)+';\n const preparationClues='+json.dumps(preparations,ensure_ascii=False)+';\n'
source=source.replace(' const cards=[];',insert+' const cards=[];')
question="""const question=es
       ? `¿En qué fórmula actúa ${c.herb} como ${labels[active]} y qué función cumple?`
       : `Which formula uses ${c.herb} as ${labels[active]}, and what is its function?`;
     const key=differentialKeys[c.profile.name];
     if(!key)throw new Error('Missing differential clue: '+c.profile.name);
     """
source=source.replace(needle,question+needle)
source=source.replace(old,"node('p',question),node('p',(es?'Pista clave: ':'Key clue: ')+key[es?1:0],'formula-hierarchy__roles-note')")
answerneedle="const answer=node('details');"
if source.count(answerneedle)!=1:raise SystemExit('ERROR: no se identificó el reverso de la ficha.')
source=source.replace(answerneedle,"""const preparation=preparationClues[c.herb];
     if(preparation)article.append(node('p',(es?'Preparación: ':'Preparation: ')+preparation[es?1:0],'formula-hierarchy__source'));
     """+answerneedle)
Path(sys.argv[2]).write_text(source)
print('Preparadas 19 pistas diferenciales bilingües; nombres y formas de ingredientes cotejados con los perfiles. Las fórmulas siguen ocultas.')
PY
php -l "$TEMP_DIR/page.php"
BACKUP="${BACKUP_ROOT:-/var/backups/acupuncturetests}/formula-differential-questions-$(date +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP"
cp -a "$TARGET" "$BACKUP/formula-ingredient-hierarchy.php"
OWNER_GROUP="$(stat -c '%u:%g' "$TARGET")"
MODE="$(stat -c '%a' "$TARGET")"
if ! install -o "${OWNER_GROUP%:*}" -g "${OWNER_GROUP#*:}" -m "$MODE" "$TEMP_DIR/page.php" "$TARGET" || ! php -l "$TARGET"; then
    cp -a "$BACKUP/formula-ingredient-hierarchy.php" "$TARGET"
    echo 'ERROR: se restauró la página anterior.' >&2
    exit 1
fi
echo 'OK: preguntas específicas con pistas diferenciales, en español e inglés.'
echo 'OK: pistas de preparación cuando aportan contexto; no se infieren formas no especificadas.'
echo 'OK: fórmulas ocultas hasta abrir la respuesta; enlaces de ida y regreso conservados.'
echo "RESPALDO: $BACKUP"
