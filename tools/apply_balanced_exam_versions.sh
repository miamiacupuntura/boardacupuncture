#!/usr/bin/env bash
set -euo pipefail
for tool in curl python3 php mktemp; do
    command -v "$tool" >/dev/null || { echo "ERROR: falta $tool" >&2; exit 1; }
done
WORK_DIR="$(mktemp -d)"
trap 'rm -rf -- "$WORK_DIR"' EXIT
BASE='https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture/a2ffbd20a0e0452a21aef6e093a45afa68c1fe9c/tools'
for file in audit_exam_diversity.py plan_exam_versions.py rebalance_published_exams.py herbology-exam-source-note.php balanced-exam-replacements.json; do
    curl -fsSL "$BASE/$file" -o "$WORK_DIR/$file"
done
python3 -m py_compile "$WORK_DIR/audit_exam_diversity.py" "$WORK_DIR/plan_exam_versions.py" "$WORK_DIR/rebalance_published_exams.py"
python3 -m json.tool "$WORK_DIR/balanced-exam-replacements.json" >/dev/null
php -l "$WORK_DIR/herbology-exam-source-note.php"
# Exercise the new note route against isolated published/draft fixtures before
# changing production files. Moodle's authentication/bootstrap is stubbed here.
python3 - "$WORK_DIR" <<'PY'
from pathlib import Path
import json,sys,shutil,subprocess,os
work=Path(sys.argv[1]);fixture=work/'note-check';route=fixture/'blocks/myprogress'
route.mkdir(parents=True);(fixture/'data/board-domain-banks').mkdir(parents=True)
shutil.copy(work/'herbology-exam-source-note.php',route/'herbology-exam-source-note.php')
(fixture/'config.php').write_text('''<?php
define('PARAM_ALPHANUMEXT',1);define('PARAM_ALPHA',2);
$CFG=(object)['dataroot'=>__DIR__.'/data'];
function require_login(){}
function required_param($name,$type){return getenv('NOTE_TEST_ID') ?: 'CF-TEST';}
function optional_param($name,$default,$type){return getenv('NOTE_TEST_LANG') ?: 'en';}
function current_language(){return 'en';}
function s($value){return htmlspecialchars((string)$value,ENT_QUOTES,'UTF-8');}
class context_system{public static function instance(){return new self();}}
class NotePage{public function __call($name,$args){}}
class NoteOutput{public function header(){return '<html>';}public function footer(){return '</html>';}public function notification($text,$type){return s($text);}}
class moodle_url{private $url;public function __construct($path,$params){$this->url=$path.'?'.http_build_query($params);}public function out($escaped=true){return $escaped?s($this->url):$this->url;}}
$PAGE=new NotePage();$OUTPUT=new NoteOutput();
''')
q={'questionId':'CF-TEST','prompt':'Original prompt','explanation':'EXPLANATION_EN',
   'source':{'title':'Textbook & Original','locator':'Question 12'},
   'translations':{'es':{'prompt':'Pregunta original','explanation':'EXPLICACION_ES'}}}
draft=dict(q,questionId='DRAFT-ONLY',explanation='DRAFT_SECRET')
store={'publishedExams':{'simulation_exam_1':{'status':'published','questions':[q]*100},
    'draft':{'status':'draft','questions':[draft]*100}}}
(fixture/'data/board-domain-banks/herbology-question-overrides.json').write_text(json.dumps(store))
for lang,expected in [('en','EXPLANATION_EN'),('es','EXPLICACION_ES')]:
    env=dict(os.environ,NOTE_TEST_ID='CF-TEST',NOTE_TEST_LANG=lang)
    result=subprocess.run(['php',str(route/'herbology-exam-source-note.php')],env=env,capture_output=True,text=True,check=True)
    assert expected in result.stdout and 'Textbook &amp; Original' in result.stdout
    assert 'DRAFT_SECRET' not in result.stdout and not result.stderr,result.stderr
result=subprocess.run(['php',str(route/'herbology-exam-source-note.php')],env=dict(os.environ,NOTE_TEST_ID='DRAFT-ONLY',NOTE_TEST_LANG='en'),capture_output=True,text=True,check=True)
assert 'DRAFT_SECRET' not in result.stdout and 'not available' in result.stdout and not result.stderr,result.stderr
print('OK: ficha de consulta EN/ES; referencia original escapada; borradores excluidos.')
PY
python3 "$WORK_DIR/rebalance_published_exams.py" "$@"
