<?php
// Category-by-category herb comparison library.

require_once(__DIR__ . '/../../config.php');

require_login();

$lang = optional_param('lang', 'en', PARAM_ALPHA);
$lang = $lang === 'es' ? 'es' : 'en';
$ises = $lang === 'es';

$pageurl = new moodle_url('/blocks/myprogress/herbs-category-comparison.php', $ises ? ['lang' => 'es'] : []);
$backurl = new moodle_url('/blocks/myprogress/study-guide.php', [
    'area' => 'herbology',
    'view' => 'library',
] + ($ises ? ['lang' => 'es'] : []));

$herbs = [
    ['Ma Huang', 'Ephedrae Herba', 'Acrid, slightly bitter', 'Warm', 'LU, UB', 'Strongly releases an excess exterior pattern; disseminates Lung Qi; relieves wheezing; promotes urination.', 'Avoid in deficiency sweating; use caution with hypertension.', '2–9 g', 'Cook first; remove foam.'],
    ['Gui Zhi', 'Cinnamomi Ramulus', 'Acrid, sweet', 'Warm', 'HT, LU, UB', 'Releases the muscle layer; warms channels; harmonizes Ying and Wei; assists Yang Qi.', 'Use caution with febrile disease, Yin-deficiency heat, pregnancy, or excessive bleeding.', '3–9 g', '—'],
    ['Zi Su Ye', 'Perillae Folium', 'Acrid', 'Warm, aromatic', 'LU, SP', 'Releases mild Wind-Cold; moves Qi; expands the chest; calms the fetus; helps seafood poisoning.', 'Use cautiously with Qi deficiency or spontaneous sweating.', '5–9 g', 'Add late when appropriate.'],
    ['Jing Jie', 'Schizonepetae Herba', 'Acrid', 'Slightly warm', 'LU, LV', 'Releases the exterior; vents rashes; relieves itching; charred form stops bleeding.', 'Use cautiously with exterior deficiency and spontaneous sweating.', '4.5–9 g', 'Char for bleeding indications.'],
    ['Fang Feng', 'Saposhnikoviae Radix', 'Acrid, sweet', 'Slightly warm', 'UB, LV, SP', 'Expels Wind; relieves pain, headache and spasms; addresses Wind-Damp and diarrhea.', 'Use cautiously with Blood or Yin deficiency.', '4.5–9 g', '—'],
    ['Qiang Huo', 'Notopterygii Rhizoma', 'Acrid, bitter', 'Warm, aromatic', 'UB, KD', 'Expels Wind-Cold-Damp; relieves occipital and upper-body pain.', 'Use cautiously with Blood or Yin deficiency; may cause nausea in excess.', '3–9 g', '—'],
    ['Gao Ben', 'Ligustici Rhizoma', 'Acrid', 'Warm', 'UB, DU', 'Expels Wind-Cold and relieves vertex headache.', 'Avoid in Blood or Yin deficiency and heat patterns.', '3–9 g', '—'],
    ['Bai Zhi', 'Angelicae Dahuricae Radix', 'Acrid', 'Warm', 'LU, ST, SP', 'Relieves frontal headache; opens the nose; reduces swelling; expels pus and damp discharge.', 'Use cautiously with Blood or Yin deficiency and dryness.', '3–9 g', '—'],
    ['Xi Xin', 'Asari Herba', 'Acrid', 'Warm', 'LU, HT, KD', 'Disperses Wind-Cold; warms Lung; transforms thin mucus; relieves pain; opens the nose.', 'Dose carefully; avoid in deficiency sweating and use caution with renal risk.', '1–3 g', 'Do not exceed the prescribed dose.'],
    ['Xin Yi', 'Magnoliae Flos', 'Acrid', 'Warm', 'LU, ST', 'Opens the nasal passages and relieves congestion, discharge, headache and loss of smell.', 'Use cautiously with Yin-deficiency heat.', '3–9 g', 'Wrap in cloth while decocting.'],
    ['Cang Er Zi', 'Xanthii Fructus', 'Sweet, bitter', 'Warm, toxic', 'LU', 'Dispels Wind; opens the nose; relieves sinus obstruction, discharge and headache.', 'Toxic in excess; avoid overdose and use cautiously in Blood deficiency.', '3–9 g', 'Use only properly prepared material.'],
    ['Sheng Jiang', 'Zingiberis Rhizoma Recens', 'Acrid', 'Slightly warm', 'LU, SP, ST', 'Releases mild exterior Cold; warms Lung; stops cough; harmonizes the Middle; relieves vomiting.', 'Use cautiously with Lung Heat or Yin-deficiency heat.', '3–9 g', 'Fresh ginger.'],
    ['Xiang Ru', 'Moslae Herba', 'Acrid', 'Slightly warm, aromatic', 'LU, ST', 'Releases Summer Wind-Cold; transforms Dampness; promotes urination; reduces swelling.', 'Avoid in Qi deficiency with spontaneous sweating.', '3–9 g', 'Cook briefly for exterior disorders.'],
    ['Cong Bai', 'Allii Fistulosi Bulbus', 'Acrid', 'Warm', 'LU, ST', 'Releases mild Wind-Cold; opens the Lung; unblocks Yang Qi and resolves clumps.', 'Use cautiously with Yin deficiency or excessive sweating.', '3–9 g', 'Use the white bulb.'],
    ['Dan Dou Chi', 'Sojae Semen Praeparatum', 'Acrid, bitter', 'Slightly cold', 'LU, ST', 'Gently releases the exterior; relieves constraint and irritability; harmonizes the Middle.', 'Use according to preparation and pattern.', '6–15 g', 'Prepared soybean; processing influences its tendency.'],
];

$similarities = [
    ['Strong exterior release / diaphoresis', 'Ma Huang · Gui Zhi', 'Warm the channels and promote sweating; Ma Huang is stronger and Gui Zhi harmonizes Ying and Wei.'],
    ['Gentle Wind-Cold release', 'Zi Su Ye · Jing Jie · Fang Feng · Sheng Jiang · Cong Bai · Dan Dou Chi', 'Release mild exterior Wind-Cold when strong diaphoresis is unnecessary.'],
    ['Wind-Cold-Damp pain and headache', 'Fang Feng · Qiang Huo · Gao Ben · Bai Zhi', 'Qiang Huo emphasizes occiput and upper body; Gao Ben the vertex; Bai Zhi the frontal region.'],
    ['Nasal congestion and sinus discharge', 'Bai Zhi · Xi Xin · Xin Yi · Cang Er Zi', 'Open the nasal passages and relieve obstruction, discharge or headache.'],
    ['Lung Qi, cough and phlegm', 'Ma Huang · Zi Su Ye · Xi Xin · Sheng Jiang', 'Warm or diffuse Lung Qi, transform phlegm and relieve cough or wheezing.'],
    ['Aromatic / Damp or Summer-Damp overlap', 'Zi Su Ye · Qiang Huo · Xiang Ru', 'Address exterior Wind-Cold with Dampness; Xiang Ru especially emphasizes Summer-Damp.'],
];

$PAGE->set_url($pageurl);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title($ises ? 'Comparación de hierbas por categoría' : 'Herbs Category Comparison');
$PAGE->set_heading($ises ? 'Comparación de hierbas por categoría' : 'Herbs Category Comparison');

echo $OUTPUT->header();
?>
<style>
.herb-category-library{width:min(1500px,calc(100% - 28px));margin:24px auto 56px;color:#102a43}
.herb-category-library__top{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;padding:24px;border:1px solid #b9d8d2;border-top:5px solid #087c69;border-radius:14px;background:#fff}
.herb-category-library__eyebrow{margin:0 0 5px;color:#087c69;font-size:12px;font-weight:900;letter-spacing:.09em;text-transform:uppercase}
.herb-category-library h2{margin:0 0 8px;font-size:clamp(26px,4vw,40px)}
.herb-category-library__intro{max-width:820px;margin:0;color:#526b84;line-height:1.55}
.herb-category-library__back{display:inline-flex;min-height:42px;align-items:center;padding:9px 15px;border:1px solid #b9c9d5;border-radius:9px;background:#fff;color:#0f4c81;font-weight:800;text-decoration:none;white-space:nowrap}
.herb-category-library__back:hover{background:#eef7f5;text-decoration:none}
.herb-category-library__index{display:flex;flex-wrap:wrap;gap:10px;margin:18px 0}
.herb-category-library__index a{padding:9px 13px;border-radius:999px;background:#087c69;color:#fff;font-weight:800;text-decoration:none}
.herb-category-library__section{margin-top:20px;padding:20px;border:1px solid #b9d8d2;border-radius:14px;background:#fff;box-shadow:0 8px 24px rgba(15,76,129,.06)}
.herb-category-library__section h3{margin:0;font-size:25px}
.herb-category-library__summary{margin:7px 0 16px;color:#526b84}
.herb-category-library__scroll{overflow-x:auto;border:1px solid #a9bbc8;border-radius:10px}
.herb-category-library table{width:100%;border-collapse:collapse;font-size:13px;line-height:1.35}
.herb-category-library th,.herb-category-library td{padding:9px 10px;border:1px solid #b9c7d1;vertical-align:top;overflow-wrap:anywhere}
.herb-category-library thead th{background:#d8f2ef;color:#102a43;text-align:left}
.herb-category-library__comparison-table{min-width:1180px;table-layout:fixed}
.herb-category-library__comparison-table col:nth-child(1){width:14%}
.herb-category-library__comparison-table col:nth-child(2){width:8%}
.herb-category-library__comparison-table col:nth-child(3){width:8%}
.herb-category-library__comparison-table col:nth-child(4){width:7%}
.herb-category-library__comparison-table col:nth-child(5){width:25%}
.herb-category-library__comparison-table col:nth-child(6){width:20%}
.herb-category-library__comparison-table col:nth-child(7){width:6%}
.herb-category-library__comparison-table col:nth-child(8){width:12%}
.herb-category-library__herb-identity{background:#eef9f7}
.herb-category-library__herb-identity strong{display:block;color:#102a43;font-size:14px}
.herb-category-library__herb-identity small{display:block;margin-top:4px;color:#526b84;font-size:12px;font-style:italic;font-weight:600}
.herb-category-library__compact{font-size:12px}
.herb-category-library__dosage{white-space:nowrap;text-align:center}
.herb-category-library__similarities{margin:18px 0}
.herb-category-library__similarities h4{margin:0;padding:12px 14px;border-radius:10px 10px 0 0;background:#087c69;color:#fff;font-size:20px}
.herb-category-library__similarities table td:nth-child(1){font-weight:800;background:#eef9f7}
.herb-category-library__similarities table td:nth-child(2){font-weight:700}
.herb-category-library__foundation{margin:14px 0 0;padding:13px 15px;border-left:5px solid #087c69;background:#e5f6f2}
@media(max-width:720px){.herb-category-library__top{flex-direction:column}.herb-category-library__back{width:100%;justify-content:center}}
</style>
<main class="herb-category-library">
    <header class="herb-category-library__top">
        <div>
            <p class="herb-category-library__eyebrow"><?php echo s($ises ? 'Biblioteca comparativa' : 'Comparative library'); ?></p>
            <h2><?php echo s($ises ? 'Comparación de hierbas por categoría' : 'Herbs Category Comparison'); ?></h2>
            <p class="herb-category-library__intro"><?php echo s($ises
                ? 'Compara propiedades, canales, funciones, precauciones y semejanzas clínicas dentro de cada categoría.'
                : 'Compare properties, channels, functions, cautions and clinical similarities within each herb category.'); ?></p>
        </div>
        <a class="herb-category-library__back" href="<?php echo $backurl->out(false); ?>"><?php echo s($ises ? '← Actividades Board Level' : '← Board Level Activities'); ?></a>
    </header>

    <nav class="herb-category-library__index" aria-label="<?php echo s($ises ? 'Índice de categorías' : 'Category index'); ?>">
        <a href="#disperse-wind-cold">1. Disperse Wind-Cold</a>
    </nav>

    <section class="herb-category-library__section" id="disperse-wind-cold">
        <h3>1. Disperse Wind-Cold</h3>
        <p class="herb-category-library__summary">Board Guideline 2026 · 15 herbs · Common foundation: predominantly acrid herbs that release exterior Wind-Cold.</p>
        <div class="herb-category-library__similarities">
            <h4>Similarities and Clinical Overlaps</h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th>Comparison</th><th>Herbs</th><th>Shared clinical relationship</th></tr></thead>
                    <tbody>
                    <?php foreach ($similarities as $similarity): ?>
                        <tr><td><?php echo s($similarity[0]); ?></td><td><?php echo s($similarity[1]); ?></td><td><?php echo s($similarity[2]); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong>Selection focus:</strong> sweating strength, pain location, nasal involvement, Lung symptoms and Dampness.</p>
        </div>

        <div class="herb-category-library__scroll">
            <table class="herb-category-library__comparison-table">
                <colgroup>
                    <col><col><col><col><col><col><col><col>
                </colgroup>
                <thead>
                    <tr>
                        <th>Herb / Pharmaceutical name</th><th>Taste</th><th>Nature</th><th>Channels</th>
                        <th>Main functions and indications</th><th>Cautions / contraindications</th><th>Dosage</th><th>Preparation</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($herbs as $herb): ?>
                    <tr>
                        <th class="herb-category-library__herb-identity" scope="row">
                            <strong><?php echo s($herb[0]); ?></strong>
                            <small><?php echo s($herb[1]); ?></small>
                        </th>
                        <td class="herb-category-library__compact"><?php echo s($herb[2]); ?></td>
                        <td class="herb-category-library__compact"><?php echo s($herb[3]); ?></td>
                        <td class="herb-category-library__compact"><?php echo s($herb[4]); ?></td>
                        <td><?php echo s($herb[5]); ?></td>
                        <td><?php echo s($herb[6]); ?></td>
                        <td class="herb-category-library__compact herb-category-library__dosage"><?php echo s($herb[7]); ?></td>
                        <td><?php echo s($herb[8]); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
<?php
echo $OUTPUT->footer();
