<?php
// This file is part of Moodle - http://moodle.org/.

require_once(__DIR__ . '/../../config.php');

require_login();

$modules = [
    'internalmedicine' => [
        'title' => 'Internal Medicine',
        'description' => 'Integrated clinical study of patterns, key symptoms, treatment methods, formulas, modifications and acupuncture points.',
        'status' => 'CONTENT RECEIVED',
        'available' => true,
    ],
    'orthopedics' => [
        'title' => 'Orthopedics',
        'description' => 'Musculoskeletal assessment, channel differentiation, treatment strategy and clinical point selection.',
        'status' => 'COMING SOON',
        'available' => false,
    ],
    'advancedacupuncture' => [
        'title' => 'Advanced Acupuncture',
        'description' => 'Advanced clinical reasoning, treatment design and specialized acupuncture strategies.',
        'status' => 'COMING SOON',
        'available' => false,
    ],
];

$topicgroups = [
    'Respiratory and Exterior' => [
        'Common Cold', 'Cough', 'Asthma', 'Pulmonary Consumption',
    ],
    'Digestive and Hepatobiliary' => [
        'Stomach Pain', 'Hiccough', 'Vomiting', 'Esophageal Constriction', 'Stomach Reflux',
        'Abdominal Pain', 'Diarrhea', 'Dysentery', 'Constipation', 'Lateral Costal Pain',
        'Gallstones', 'Jaundice', 'Drum Distention', 'Intestinal Abscess', 'Hemorrhoids',
    ],
    'Fluid Metabolism and Urogenital' => [
        'Edema', 'Seminal Emission', 'Impotence', 'Wasting-Thirst', 'Urinary Strangury',
        'Dribbling Urinary Block',
    ],
    'Cardiovascular, Shen and Neurologic' => [
        'Thoracic Bi', 'Palpitations', 'Insomnia', 'Headache', 'Dizziness and Vertigo',
        'Hypertension', 'Depression Patterns', 'Mania and Withdrawal', 'Epilepsy Patterns',
    ],
    'Pain, Bi and Trauma' => [
        'Low Back Pain', 'Bi Patterns', 'Atony Patterns', 'Facial Pain', 'Sprain', 'Crick in the Neck',
    ],
    'Systemic and Miscellaneous' => [
        'Internal Damage Fever', 'Parasitic Worm Patterns', 'Malarial Patterns', 'Goiter',
    ],
    'Dermatology and External Disorders' => [
        'Gangrene', 'Patch Balding', 'Erysipelas (Dan Du)', 'Wind Rash (Urticaria)',
        'Herpes Zoster (She Dan)', 'Eczema (Shi Zhen)', 'Psoriasis (Niu Pi Xian)',
        'Clove Sores (Ding Chuang)', 'Flat Warts (Bian Ping You)',
    ],
];

$moduleid = optional_param('module', '', PARAM_ALPHANUM);
if ($moduleid !== '' && !array_key_exists($moduleid, $modules)) {
    $moduleid = '';
}

$pageurl = new moodle_url('/blocks/myprogress/module-school.php');
if ($moduleid !== '') {
    $pageurl->param('module', $moduleid);
}

$PAGE->set_url($pageurl);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title($moduleid === '' ? 'Module School' : $modules[$moduleid]['title'] . ' | Module School');
$PAGE->set_heading('Module School');

echo $OUTPUT->header();
?>
<style>
.module-school{max-width:1080px;margin:24px auto 58px;padding:0 14px;color:#17232a}
.module-school__top{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;margin-bottom:22px}
.module-school__eyebrow{margin:0 0 7px;color:#176b9a;font-size:13px;font-weight:900;letter-spacing:.09em;text-transform:uppercase}
.module-school h2{margin:0 0 10px;font-size:clamp(30px,5vw,46px);line-height:1.08}
.module-school__lead{max-width:780px;margin:0;color:#52677a;font-size:16px;line-height:1.6}
.module-school__actions{display:flex;flex-wrap:wrap;justify-content:flex-end;gap:9px}
.module-school__back{display:inline-flex;min-height:42px;box-sizing:border-box;align-items:center;justify-content:center;padding:9px 14px;border:1px solid #c8d3dc;border-radius:8px;background:#fff;color:#334155;font-weight:800;text-decoration:none;white-space:nowrap}
.module-school__back:hover{background:#f1f5f9;text-decoration:none}
.module-school__hero{margin-bottom:24px;padding:34px 38px;border-top:5px solid #176b9a;border-radius:14px;background:linear-gradient(135deg,#f4f9fc,#fff9f5);box-shadow:0 4px 16px rgba(15,23,42,.06)}
.module-school__grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px}
.module-school__card{display:flex;min-height:260px;flex-direction:column;padding:23px;border:1px solid #d7e0e6;border-top:5px solid #176b9a;border-radius:12px;background:#fff;box-shadow:0 3px 12px rgba(15,23,42,.06)}
.module-school__card--future{border-top-color:#94a3b8}
.module-school__status{align-self:flex-start;margin-bottom:14px;padding:5px 9px;border-radius:999px;background:#e2f1f8;color:#145b83;font-size:11px;font-weight:900;letter-spacing:.04em}
.module-school__card--future .module-school__status{background:#eef1f4;color:#64748b}
.module-school__card h3{margin:0 0 10px;font-size:22px}.module-school__card p{margin:0 0 20px;color:#5b6d7d;line-height:1.55}
.module-school__open{display:inline-flex;min-height:43px;align-items:center;justify-content:center;align-self:flex-start;margin-top:auto;padding:9px 15px;border-radius:8px;background:#176b9a;color:#fff;font-weight:900;text-decoration:none}
.module-school__open:hover{background:#115579;color:#fff;text-decoration:none}.module-school__card--future .module-school__open{background:#64748b}
.module-school__principles{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:13px;margin:0 0 24px}
.module-school__principle{padding:18px;border:1px solid #d7e0e6;border-radius:10px;background:#fff}.module-school__principle h3{margin:0 0 7px;font-size:16px}.module-school__principle p{margin:0;color:#5b6d7d;font-size:14px;line-height:1.5}
.module-school__topic-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}
.module-school__topic-group{padding:21px;border:1px solid #d7e0e6;border-radius:11px;background:#fff}.module-school__topic-group h3{margin:0 0 14px;font-size:18px}
.module-school__topics{display:flex;flex-wrap:wrap;gap:7px;margin:0;padding:0;list-style:none}.module-school__topics li{padding:6px 9px;border-radius:999px;background:#f0f6f9;color:#32546a;font-size:13px;font-weight:700}
.module-school__notice{margin-top:20px;padding:18px;border-left:5px solid #f77e25;border-radius:9px;background:#fff8f1;color:#68411f;line-height:1.55}
.module-school__construction{padding:58px 24px;border:1px dashed #aeb8c0;border-radius:14px;background:#fff;text-align:center}.module-school__construction h3{margin:0 0 10px;font-size:28px}.module-school__construction p{max-width:620px;margin:0 auto 24px;color:#64748b;line-height:1.6}
.module-school a:focus-visible{outline:3px solid #185cca;outline-offset:3px}
@media(max-width:820px){.module-school__grid{grid-template-columns:1fr}.module-school__principles{grid-template-columns:1fr}.module-school__topic-grid{grid-template-columns:1fr}}
@media(max-width:650px){.module-school__top{display:block}.module-school__actions{align-items:stretch;flex-direction:column;margin-top:16px}.module-school__actions a{width:100%}.module-school__hero{padding:26px 22px}}
</style>
<main class="module-school">
<?php if ($moduleid === ''): ?>
    <header class="module-school__top">
        <div>
            <p class="module-school__eyebrow">Clinical learning collection</p>
            <h2>Module School</h2>
            <p class="module-school__lead">Independent clinical modules that integrate pattern differentiation, symptoms, herbs, formulas, modifications and acupuncture points without mixing them into the Board Comparisons library.</p>
        </div>
        <nav class="module-school__actions" aria-label="Module School navigation">
            <a class="module-school__back" href="<?php echo (new moodle_url('/my/'))->out(false); ?>">← Back to Dashboard</a>
        </nav>
    </header>
    <section class="module-school__hero" aria-labelledby="module-school-title">
        <p class="module-school__eyebrow">Separate from Board Comparisons</p>
        <h2 id="module-school-title">Choose a clinical module</h2>
        <p class="module-school__lead">Each module will have its own lessons and navigation. Material may combine biomedical topics, Chinese medicine patterns, formulas, herbs and point prescriptions.</p>
    </section>
    <section class="module-school__grid" aria-label="Module School subjects">
        <?php foreach ($modules as $key => $module): ?>
            <article class="module-school__card<?php echo $module['available'] ? '' : ' module-school__card--future'; ?>">
                <span class="module-school__status"><?php echo s($module['status']); ?></span>
                <h3><?php echo s($module['title']); ?></h3>
                <p><?php echo s($module['description']); ?></p>
                <a class="module-school__open" href="<?php echo (new moodle_url('/blocks/myprogress/module-school.php', ['module' => $key]))->out(false); ?>"><?php echo $module['available'] ? 'Open module outline →' : 'View module plan →'; ?></a>
            </article>
        <?php endforeach; ?>
    </section>
<?php elseif ($moduleid === 'internalmedicine'): ?>
    <header class="module-school__top">
        <div>
            <p class="module-school__eyebrow">Module School · Content received</p>
            <h2>Internal Medicine</h2>
            <p class="module-school__lead">A structured clinical module combining pattern differentiation, diagnostic clues, treatment methods, formulas, modifications and acupuncture prescriptions.</p>
        </div>
        <nav class="module-school__actions" aria-label="Internal Medicine navigation">
            <a class="module-school__back" href="<?php echo (new moodle_url('/blocks/myprogress/module-school.php'))->out(false); ?>">← Module School</a>
            <a class="module-school__back" href="<?php echo (new moodle_url('/my/'))->out(false); ?>">Dashboard</a>
        </nav>
    </header>
    <section class="module-school__principles" aria-label="Internal Medicine module structure">
        <article class="module-school__principle"><h3>1. Differentiate the pattern</h3><p>Compare causes, key symptoms, tongue and pulse before selecting treatment.</p></article>
        <article class="module-school__principle"><h3>2. Integrate treatment</h3><p>Connect method, base formula, modifications, herbs and acupuncture points.</p></article>
        <article class="module-school__principle"><h3>3. Review safely</h3><p>Separate study content from emergencies, contraindications and referral decisions.</p></article>
    </section>
    <section class="module-school__topic-grid" aria-label="Internal Medicine topics received">
        <?php foreach ($topicgroups as $group => $topics): ?>
            <article class="module-school__topic-group">
                <h3><?php echo s($group); ?></h3>
                <ul class="module-school__topics">
                    <?php foreach ($topics as $topic): ?><li><?php echo s($topic); ?></li><?php endforeach; ?>
                </ul>
            </article>
        <?php endforeach; ?>
    </section>
    <div class="module-school__notice"><strong>Current status:</strong> the source material has been received and indexed. Individual lessons will be published only after terminology, formulas, point prescriptions and safety statements are reviewed.</div>
<?php else: ?>
    <header class="module-school__top">
        <div><p class="module-school__eyebrow">Module School</p><h2><?php echo s($modules[$moduleid]['title']); ?></h2></div>
        <nav class="module-school__actions" aria-label="Module navigation"><a class="module-school__back" href="<?php echo (new moodle_url('/blocks/myprogress/module-school.php'))->out(false); ?>">← Module School</a><a class="module-school__back" href="<?php echo (new moodle_url('/my/'))->out(false); ?>">Dashboard</a></nav>
    </header>
    <section class="module-school__construction">
        <p class="module-school__eyebrow">Coming soon</p>
        <h3><?php echo s($modules[$moduleid]['title']); ?></h3>
        <p><?php echo s($modules[$moduleid]['description']); ?> This module has its own reserved location and will not be mixed with Internal Medicine or Board Comparisons.</p>
        <a class="module-school__open" href="<?php echo (new moodle_url('/blocks/myprogress/module-school.php'))->out(false); ?>">Back to Module School</a>
    </section>
<?php endif; ?>
</main>
<?php
echo $OUTPUT->footer();
