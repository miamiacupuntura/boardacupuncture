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

$windheatherbs = [
    ['Bo He', 'Menthae Herba', 'Acrid', 'Cool, aromatic', 'LU, LV', 'Clears the head and eyes; benefits the throat; soothes constrained Liver Qi; vents rashes.', 'Use cautiously with Qi-deficiency exterior patterns or Yin-deficiency heat.', '3–6 g', 'Add during the final 5 minutes.'],
    ['Niu Bang Zi', 'Arctii Fructus', 'Acrid, bitter', 'Cold', 'LU, ST', 'Disperses Wind-Heat; benefits the throat; clears Heat and toxin; vents rashes; moistens the intestines.', 'Use cautiously with Qi-deficiency diarrhea or open sores without toxin.', '6–12 g', 'Crush before decocting.'],
    ['Chan Tui', 'Cicadae Periostracum', 'Sweet, salty', 'Slightly cold', 'LU, LV', 'Disperses Wind-Heat; vents rashes; relieves itching; benefits the throat; clears the eyes; extinguishes Liver Wind and spasms.', 'Use cautiously in deficiency exterior patterns and during pregnancy.', '3–9 g', '—'],
    ['Sang Ye', 'Mori Folium', 'Sweet, bitter', 'Cold', 'LU, LV', 'Disperses Wind-Heat; clears Lung Heat and moistens dryness; cools Blood; stops bleeding; clears the eyes.', 'No major caution noted at standard dosage.', '4.5–9 g', '—'],
    ['Ju Hua', 'Chrysanthemi Flos', 'Sweet, bitter', 'Slightly cold', 'LU, LV', 'Disperses Wind-Heat; clears the eyes; calms the Liver; extinguishes Wind; clears Heat and toxin.', 'Use cautiously with Qi deficiency, poor appetite or diarrhea.', '4.5–15 g', 'Add late when appropriate.'],
    ['Man Jing Zi', 'Viticis Fructus', 'Bitter, acrid', 'Slightly cold', 'UB, LV, ST', 'Disperses Wind-Heat; clears the head and eyes; relieves headache; expels Wind-Damp.', 'Use cautiously when headache or eye problems arise from Blood deficiency or weak Stomach Qi.', '6–12 g', 'Crush before decocting.'],
    ['Dan Dou Chi', 'Sojae Semen Praeparatum', 'Sweet, slightly bitter', 'Cold or warm', 'LU, ST', 'Gently releases the exterior; eliminates irritability and constraint.', 'Use according to preparation and pattern.', '6–12 g', 'Processing influences its thermal tendency.'],
    ['Mu Zei', 'Equiseti Herba', 'Sweet, bitter', 'Neutral', 'GB, LU, LV', 'Disperses Wind-Heat; brightens the eyes; removes visual obstruction; clears Heat; stops bleeding.', 'Use cautiously with frequent urination, depleted fluids, Qi deficiency or pregnancy.', '3–9 g', '—'],
    ['Chai Hu', 'Bupleuri Radix', 'Bitter, acrid', 'Cool', 'GB, LV, PC, SJ', 'Resolves Shaoyang disorders; reduces fever; spreads Liver Qi; raises clear Yang Qi.', 'Use cautiously with Yin-deficiency cough, Liver Fire ascending, headache, nausea or vomiting.', '3–9 g', '—'],
    ['Sheng Ma', 'Cimicifugae Rhizoma', 'Sweet, acrid', 'Slightly cold', 'LI, LU, SP, ST', 'Disperses Wind-Heat; clears Heat and toxin; vents rashes; raises Yang Qi.', 'Use cautiously with Yin-deficiency heat, upper excess, or fully erupted measles.', '3–9 g', '—'],
    ['Ge Gen', 'Puerariae Radix', 'Sweet, acrid', 'Cool', 'SP, ST', 'Releases the muscle layer; clears Heat; generates fluids; relieves thirst; treats diarrhea; vents rashes.', 'No major caution noted at standard dosage.', '9–12 g', '—'],
];

$windheatsimilarities = [
    ['Benefit the throat and vent rashes', 'Bo He · Niu Bang Zi · Chan Tui · Sheng Ma · Ge Gen', 'Disperse Wind-Heat while addressing sore throat or incomplete rash expression.'],
    ['Clear the head and eyes', 'Bo He · Sang Ye · Ju Hua · Man Jing Zi · Mu Zei', 'Treat Wind-Heat affecting the head or eyes; selection depends on headache, dryness, redness or visual obstruction.'],
    ['Liver and Wind involvement', 'Bo He · Chan Tui · Sang Ye · Ju Hua · Man Jing Zi · Chai Hu', 'Share Liver-channel activity ranging from moving Qi to clearing eyes or extinguishing Wind.'],
    ['Lung Heat and dryness', 'Niu Bang Zi · Sang Ye · Ju Hua', 'Clear upper-burner Heat; Sang Ye emphasizes Lung dryness, while Niu Bang Zi emphasizes throat and toxin.'],
    ['Raise clear Yang Qi', 'Chai Hu · Sheng Ma · Ge Gen', 'Raise clear Yang; Sheng Ma emphasizes sinking Qi, Ge Gen diarrhea, and Chai Hu Shaoyang with Liver Qi constraint.'],
    ['Gentle exterior release and irritability', 'Dan Dou Chi · Bo He · Chan Tui', 'Useful when a lighter exterior-releasing strategy is preferred; Dan Dou Chi especially relieves irritability.'],
];

$clearheatherbs = [
    ['Shi Gao', 'Gypsum Fibrosum', 'Sweet, acrid', 'Very cold', 'LU, ST', 'Clears intense Qi-level Heat; drains Lung and Stomach Fire; relieves thirst; used topically for burns or sores.', 'Avoid in Spleen and Stomach deficiency-cold, weak pulse or Yang deficiency.', '15–60 g', 'Crush and decoct 20–30 minutes before other herbs.'],
    ['Zhi Mu', 'Anemarrhenae Rhizoma', 'Bitter, sweet', 'Cold', 'LU, ST, KD', 'Clears Heat and drains Fire; enriches Yin; moistens Lung and Kidney dryness; relieves wasting and thirsting.', 'Use cautiously with diarrhea from Spleen deficiency.', '6–12 g', '—'],
    ['Tian Hua Fen', 'Trichosanthis Radix', 'Bitter, slightly sweet', 'Cold', 'LU, ST', 'Clears Lung and Stomach Heat; generates fluids; relieves thirst; moistens Lung dryness; resolves toxicity and expels pus.', 'Contraindicated during pregnancy; use cautiously with Spleen and Stomach deficiency-cold or diarrhea.', '10–15 g', '—'],
    ['Lu Gen', 'Phragmitis Rhizoma', 'Sweet', 'Cold', 'LU, ST', 'Clears Heat; generates fluids; relieves thirst; clears Lung Heat; stops vomiting; promotes urination.', 'Use cautiously with Spleen and Stomach deficiency-cold.', '15–30 g', 'Fresh form may be used at a larger dose.'],
    ['Dan Zhu Ye', 'Lophatheri Herba', 'Sweet, bland', 'Cold', 'HT, SI, ST', 'Clears Heat and irritability; promotes urination; clears Heart Heat through the Small Intestine.', 'Use cautiously during pregnancy or with depleted fluids.', '6–9 g', '—'],
    ['Zhi Zi', 'Gardeniae Fructus', 'Bitter', 'Cold', 'HT, LU, ST, LV, SJ', 'Drains Fire and eliminates irritability; clears Damp-Heat; cools Blood; stops bleeding; reduces swelling.', 'Use cautiously with loose stools or poor appetite from deficiency-cold.', '6–9 g', 'Crush before decocting.'],
    ['Xia Ku Cao', 'Prunellae Spica', 'Bitter, acrid', 'Cold', 'GB, LV', 'Clears Liver Fire and the eyes; dissipates nodules and swelling; supports patterns of Liver Yang rising.', 'Use cautiously with Spleen and Stomach deficiency.', '9–15 g', '—'],
    ['Jue Ming Zi', 'Cassiae Semen', 'Bitter, sweet, salty', 'Slightly cold', 'LV, LI, KD', 'Clears Liver Heat and brightens the eyes; moistens the intestines; promotes bowel movement; supports hypertension patterns.', 'Use cautiously with diarrhea, loose stools or hypotension.', '9–15 g', 'Crush before decocting.'],
];

$clearheatsimilarities = [
    ['Clear intense Heat and drain Fire', 'Shi Gao · Zhi Mu', 'Both clear strong internal Heat; Shi Gao acts powerfully at the Qi level, while Zhi Mu also enriches Yin and moistens dryness.'],
    ['Generate fluids and relieve thirst', 'Shi Gao · Zhi Mu · Tian Hua Fen · Lu Gen', 'Address Heat damaging fluids; Tian Hua Fen and Lu Gen emphasize fluid generation, while Zhi Mu treats Yin dryness.'],
    ['Clear Lung and Stomach Heat', 'Shi Gao · Zhi Mu · Tian Hua Fen · Lu Gen', 'Share Lung and Stomach Heat indications, with different emphasis on Fire, phlegm, dryness, thirst or vomiting.'],
    ['Eliminate irritability and promote urination', 'Dan Zhu Ye · Zhi Zi', 'Clear Heart Heat and irritability; both can guide Heat out through urination, while Zhi Zi also treats Damp-Heat broadly.'],
    ['Clear Liver Heat and benefit the eyes', 'Xia Ku Cao · Jue Ming Zi', 'Treat red or painful eyes from Liver Heat; Xia Ku Cao dissipates nodules, while Jue Ming Zi moistens the intestines.'],
    ['Reduce swelling, nodules or toxicity', 'Tian Hua Fen · Zhi Zi · Xia Ku Cao', 'Address Heat-related swelling through different mechanisms: expelling pus, reducing swelling, or dissipating nodules.'],
];

$bloodheatherbs = [
    ['Shui Niu Jiao', 'Bubali Cornu', 'Salty', 'Cold', 'HT, LV, ST', 'Clears Heat and toxin; cools the Blood; stops bleeding; arrests tremors and convulsions from blazing Heat.', 'Avoid in deficiency without strong Heat; use cautiously during pregnancy.', '30–120 g decoction; 6–15 g powder', 'Crush and decoct before other herbs.'],
    ['Sheng Di Huang', 'Rehmanniae Radix', 'Sweet, bitter', 'Cold', 'HT, KD, LV', 'Clears Heat and cools Blood; nourishes Yin; generates fluids; relieves wasting and thirsting; clears blazing Heart Fire.', 'Use cautiously with Spleen deficiency, Dampness, poor appetite or diarrhea.', '9–15 g', 'Fresh and prepared forms have different emphases.'],
    ['Xuan Shen', 'Scrophulariae Radix', 'Salty, sweet, bitter', 'Cold', 'KD, LU, ST', 'Cools Blood; nourishes Yin; drains Fire; relieves toxicity, sore throat and swelling; softens hardness and dissipates nodules.', 'Use cautiously with Spleen and Stomach deficiency, Dampness or diarrhea; incompatible with Li Lu.', '9–15 g', '—'],
    ['Mu Dan Pi', 'Moutan Cortex', 'Acrid, bitter', 'Slightly cold', 'HT, LV, KD', 'Clears Heat and cools Blood; clears deficiency Heat without sweating; invigorates Blood; dispels stasis; reduces swelling.', 'Use cautiously with cold patterns, pregnancy, excessive menstruation or bleeding tendency.', '6–12 g', 'Avoid combining with garlic when following traditional cautions.'],
    ['Zi Cao', 'Arnebiae / Lithospermi Radix', 'Sweet', 'Cold', 'HT, LV', 'Cools Blood; vents rashes; relieves Fire toxin; treats Damp-Heat skin disorders; mildly promotes stool and urination.', 'Use cautiously with Spleen and Stomach deficiency or diarrhea because of its mild laxative effect.', '3–9 g', 'Often used topically for skin conditions.'],
];

$bloodheatsimilarities = [
    ['Cool Blood-level Heat', 'Shui Niu Jiao · Sheng Di Huang · Xuan Shen · Mu Dan Pi · Zi Cao', 'All cool the Blood, but differ in emphasis on toxin, Yin damage, stasis, bleeding or rash expression.'],
    ['Nourish Yin while clearing Heat', 'Sheng Di Huang · Xuan Shen', 'Both protect Yin and fluids; Sheng Di Huang emphasizes Blood and fluid generation, while Xuan Shen drains Fire and softens hardness.'],
    ['Heat toxin, swelling and sore throat', 'Shui Niu Jiao · Xuan Shen · Zi Cao', 'Address severe Heat toxin; Xuan Shen emphasizes throat and nodules, while Zi Cao emphasizes skin and rashes.'],
    ['Bleeding versus Blood stasis', 'Shui Niu Jiao · Sheng Di Huang · Mu Dan Pi', 'Shui Niu Jiao and Sheng Di Huang cool Blood to control bleeding; Mu Dan Pi cools while invigorating Blood and dispelling stasis.'],
    ['Deficiency Heat', 'Sheng Di Huang · Xuan Shen · Mu Dan Pi', 'Treat Heat from Yin damage; Mu Dan Pi is notable for deficiency Heat without sweating.'],
    ['Rashes and skin manifestations', 'Zi Cao · Sheng Di Huang · Mu Dan Pi', 'Cool Blood-level Heat affecting the skin; Zi Cao most directly vents rashes and treats topical Fire toxin.'],
];

$dampheatherbs = [
    ['Huang Qin', 'Scutellariae Radix', 'Bitter', 'Cold', 'LU, ST, GB, LI', 'Clears Heat and dries Dampness, especially in the upper burner; clears Lung Heat; treats Shaoyang disorders; clears Heat and stops bleeding when charred; calms the fetus; subdues ascending Liver Yang.', 'Avoid with deficient Lung Heat, cold diarrhea or middle-burner Cold; use cautiously when restless fetus is due to Cold. Traditionally counteracts Mu Dan Pi and Li Lu.', '3–9 g', 'Charred form emphasizes stopping bleeding.'],
    ['Huang Lian', 'Coptidis Rhizoma', 'Bitter', 'Cold', 'HT, LI, LV, ST', 'Clears Heat and dries Dampness, especially in the middle burner; treats Damp-Heat diarrhea or dysentery; clears Heart and Stomach Fire; clears Heat and stops bleeding; may be used topically for Heat.', 'Avoid in Yin deficiency, nausea or vomiting from Spleen-deficient Cold, or diarrhea from Spleen/Kidney deficiency; prolonged use may injure the Spleen and Stomach.', '1.5–9 g', '—'],
    ['Huang Bai', 'Phellodendri Cortex', 'Bitter', 'Cold', 'KD, UB', 'Clears Heat and dries Dampness, especially in the lower burner; drains Kidney deficiency Fire and treats lower-burner Damp-Heat.', 'Avoid in deficient Spleen conditions, particularly with diarrhea.', '3–12 g', '—'],
    ['Long Dan Cao', 'Gentianae Radix', 'Bitter', 'Cold', 'GB, LV, ST', 'Drains Damp-Heat from the Liver and Gallbladder channels, including the eyes and genital region; treats jaundice; drains and pacifies excessive Liver Fire.', 'Avoid with diarrhea due to Spleen and Stomach deficiency.', '3–9 g', '—'],
    ['Ku Shen', 'Sophorae Flavescentis Radix', 'Bitter', 'Cold', 'UB, HT, LV, LI, ST, SI', 'Clears Heat and dries Dampness; dispels Wind, kills parasites and stops itching, especially in the lower body; promotes urination.', 'Avoid in Spleen and Stomach deficient Cold; should not be injected; incompatible with Li Lu and traditionally antagonizes Tu Si Zi and Bei Mu.', '3–9 g; up to 30 g topical', 'May be used externally for itching and parasites.'],
];

$dampheatsimilarities = [
    ['Bitter-cold Damp-Heat clearing', 'Huang Qin · Huang Lian · Huang Bai · Long Dan Cao · Ku Shen', 'All are bitter and cold herbs that clear Heat and dry Dampness; selection depends mainly on location and associated symptoms.'],
    ['Three-burner emphasis', 'Huang Qin · Huang Lian · Huang Bai', 'Huang Qin emphasizes the upper burner, Huang Lian the middle burner and Huang Bai the lower burner.'],
    ['Fire and toxicity', 'Huang Qin · Huang Lian · Long Dan Cao', 'All clear excess Fire; Huang Qin emphasizes Lung and Shaoyang Heat, Huang Lian Heart and Stomach Fire, and Long Dan Cao Liver and Gallbladder Fire.'],
    ['Lower-burner Damp-Heat', 'Huang Bai · Long Dan Cao · Ku Shen', 'Treat lower-burner Damp-Heat; Long Dan Cao emphasizes Liver/Gallbladder and genital signs, while Ku Shen emphasizes itching and parasites.'],
    ['Urinary and genital involvement', 'Long Dan Cao · Ku Shen · Huang Bai', 'Address Damp-Heat below; Long Dan Cao is strong for genital and Liver/Gallbladder patterns, while Ku Shen promotes urination and stops itching.'],
    ['Skin itching and external use', 'Ku Shen · Huang Bai · Huang Lian', 'Clear Damp-Heat affecting the skin; Ku Shen is most specific for parasites and itching, while Huang Lian and Huang Bai emphasize Heat and toxicity.'],
];

$toxicheatherbs = [
    ['Jin Yin Hua', 'Lonicerae Flos', 'Sweet', 'Cold', 'LI, LU, ST', 'Clears Heat and relieves Fire toxicity; disperses externally contracted Wind-Heat; clears Damp-Heat from the lower burner.', 'Avoid in diarrhea from Spleen and Stomach deficient Cold, Qi-deficient sores with clear exudate, or Yin-type ulcers.', '6–20 g', 'Use smaller doses for Wind-Heat and larger doses for toxic Heat.'],
    ['Lian Qiao', 'Forsythiae Fructus', 'Bitter, slightly acrid', 'Slightly cold', 'HT, LU, GB', 'Clears Heat and relieves Fire toxicity; disperses Wind-Heat; reduces abscesses and dissipates clumps; clears Heart Heat.', 'Avoid in diarrhea from Spleen and Stomach deficient Cold, ulcerated carbuncles, or cold Yin-type ulcers.', '6–15 g', '—'],
    ['She Gan', 'Belamcandae Rhizoma', 'Bitter', 'Cold', 'LU', 'Clears Heat and relieves toxicity; benefits the throat; transforms phlegm and clears the Lung.', 'Avoid during pregnancy and use cautiously with diarrhea due to Spleen deficiency.', '3–9 g', '—'],
    ['Ban Lan Gen', 'Isatidis Radix', 'Bitter', 'Cold', 'HT, LU, ST', 'Clears Heat and relieves toxicity; cools the Blood; benefits the throat; helps prevent epidemic febrile disease.', 'Use cautiously in weak patients or when Fire toxin is absent.', '9–15 g', '—'],
    ['Shan Dou Gen', 'Sophorae Tonkinensis Radix', 'Bitter', 'Cold', 'LU, LI', 'Clears Heat and relieves toxicity; strongly benefits a swollen, painful throat; clears Lung Heat and cough.', 'Avoid with diarrhea due to Spleen-deficient Cold; use carefully because excessive doses may cause adverse effects.', '3–9 g', '—'],
    ['Tu Fu Ling', 'Smilacis Glabrae Rhizoma', 'Sweet, bland', 'Neutral', 'LV, ST', 'Relieves toxicity and Dampness; benefits the joints; clears Damp-Heat from the skin; traditionally treats syphilitic lesions.', 'Use cautiously with Liver or Kidney Yin deficiency; traditionally should not be taken with tea.', '15–60 g', 'Do not combine with tea.'],
    ['Pu Gong Ying', 'Taraxaci Herba', 'Bitter, sweet', 'Cold', 'LV, ST', 'Clears Heat and relieves toxicity; reduces abscesses, especially breast abscess and mastitis; promotes lactation; clears Liver Heat from the eyes; treats painful urinary dribbling.', 'Large doses may cause mild diarrhea.', '9–30 g', '—'],
    ['Bai Jiang Cao', 'Patriniae Herba', 'Acrid, bitter', 'Slightly cold', 'LI, LV, ST', 'Clears Heat and relieves toxicity; reduces abscesses; expels pus from intestinal abscesses; dispels Blood stasis and relieves pain.', 'Use cautiously with Spleen and Stomach deficiency; excessive doses may cause nausea or dizziness.', '6–15 g', '—'],
    ['He Ye', 'Nelumbinis Folium', 'Bitter, slightly sweet', 'Neutral', 'HT, LV, SP', 'Clears Summer-Heat; raises and clears Spleen Yang to treat diarrhea; stops bleeding.', 'Avoid when bleeding is due to deficiency or Cold; traditionally counteracts Fu Ling.', '3–9 g', '—'],
];

$toxicheatsimilarities = [
    ['Core Fire-toxin clearing', 'Jin Yin Hua · Lian Qiao · Ban Lan Gen · Pu Gong Ying', 'All clear Heat and toxicity; Jin Yin Hua and Lian Qiao also disperse Wind-Heat, Ban Lan Gen emphasizes epidemic and throat disorders, and Pu Gong Ying emphasizes abscesses.'],
    ['Externally contracted Wind-Heat', 'Jin Yin Hua · Lian Qiao', 'A classic paired comparison for early warm febrile disease; both disperse Wind-Heat while clearing toxicity.'],
    ['Painful or swollen throat', 'She Gan · Ban Lan Gen · Shan Dou Gen', 'All benefit the throat; She Gan adds phlegm transformation, Ban Lan Gen cools Blood and addresses epidemics, and Shan Dou Gen is especially strong for severe throat swelling.'],
    ['Abscesses and suppuration', 'Lian Qiao · Pu Gong Ying · Bai Jiang Cao', 'Lian Qiao dissipates clumps, Pu Gong Ying emphasizes breast abscess and lactation, and Bai Jiang Cao emphasizes intestinal abscess with pus and Blood stasis.'],
    ['Damp-Heat and toxicity', 'Jin Yin Hua · Tu Fu Ling · Pu Gong Ying', 'Treat toxicity complicated by Dampness; Jin Yin Hua emphasizes the lower burner, Tu Fu Ling skin and joints, and Pu Gong Ying urinary or breast involvement.'],
    ['Skin lesions and toxicity', 'Tu Fu Ling · Jin Yin Hua · Pu Gong Ying', 'Clear toxic skin manifestations with different emphases on Dampness, sores, swelling and abscess formation.'],
    ['Distinct supportive actions', 'He Ye · Bai Jiang Cao · Pu Gong Ying', 'He Ye uniquely clears Summer-Heat and raises Spleen Yang; Bai Jiang Cao moves Blood and relieves pain; Pu Gong Ying promotes lactation and urination.'],
];

$deficientheatherbs = [
    ['Qing Hao', 'Artemisiae Annuae Herba', 'Bitter', 'Cold', 'KD, LV, GB', 'Clears deficient Heat and cools the Blood; clears Summer-Heat; stops bleeding; checks malarial disorders and relieves Heat.', 'Avoid in postpartum women with Blood deficiency and in Spleen or Stomach deficient Cold; traditionally should not be used with Sheng Di or Dang Gui.', '6–12 g', 'Do not subject to prolonged cooking.'],
    ['Di Gu Pi', 'Lycii Cortex', 'Sweet, bland', 'Cold', 'LU, LV, KD', 'Clears deficient Heat and cools the Blood; reduces steaming from Kidney Yin deficiency with sweating; clears Lung Heat and stops cough; drains floating Fire from the Kidney channel, including toothache.', 'Avoid with loose stools from Spleen and Stomach deficient Cold and during external disorders.', '6–15 g', '—'],
    ['Bai Wei', 'Cynanchi Atrati Radix', 'Bitter, salty', 'Cold', 'LU, ST, KD', 'Clears deficient Heat and cools the Blood; promotes urination; relieves toxicity and reduces swelling.', 'Traditionally antagonizes Huang Qi, Da Huang, Da Ji, Gan Jiang, Da Zao and Shan Zhu Yu.', '3–12 g', '—'],
];

$deficientheatsimilarities = [
    ['Clear deficient Heat', 'Qing Hao · Di Gu Pi · Bai Wei', 'All clear deficient Heat and cool the Blood, but differ in their secondary emphasis on Summer-Heat, Lung Heat, urination or toxicity.'],
    ['Yin-deficiency steaming', 'Qing Hao · Di Gu Pi', 'Both treat lingering deficiency Heat; Di Gu Pi is especially associated with steaming from Kidney Yin deficiency accompanied by sweating.'],
    ['Cool the Blood', 'Qing Hao · Di Gu Pi · Bai Wei', 'All cool Blood-level Heat; Qing Hao additionally stops bleeding, Di Gu Pi treats steaming Heat, and Bai Wei promotes urination.'],
    ['Febrile, Summer-Heat and malarial patterns', 'Qing Hao', 'Qing Hao is the distinctive choice when deficient Heat overlaps with Summer-Heat or malarial-type febrile disorders.'],
    ['Lung Heat versus urinary toxicity', 'Di Gu Pi · Bai Wei', 'Di Gu Pi emphasizes Lung Heat with cough, while Bai Wei emphasizes urination, toxicity and swelling.'],
];

$purgativeherbs = [
    ['Da Huang', 'Rhei Radix', 'Bitter', 'Cold', 'HT, LI, LV, ST', 'Drains Heat and moves stool to purge accumulation; clears Damp-Heat in jaundice or acute hot dysenteric disorders; clears Blood Heat and stops bleeding when charred; invigorates Blood and dispels stasis when wine-treated; clears toxic Heat.', 'Avoid with exterior conditions, Qi or Blood deficiency, Spleen and Stomach deficient Cold, pregnancy or menstruation; use cautiously in nursing mothers because it may enter breast milk.', '3–15 g', 'Cooking longer than 10 minutes reduces the purgative effect.'],
    ['Mang Xiao', 'Natrii Sulfas', 'Acrid, bitter, salty', 'Very cold', 'ST, LI', 'Purges accumulation; moistens dryness and softens hardness; treats excess Heat in the Stomach and Large Intestine; clears Heat and reduces swelling when used externally.', 'Avoid without excess Heat, in elderly or debilitated patients, Spleen deficiency, pregnancy, the postpartum period or menstruation.', '6–18 g', 'Dissolve into the strained decoction.'],
    ['Lu Hui', 'Aloe', 'Bitter', 'Cold', 'LI, LV, ST', 'Purges accumulation; clears Heat and cools the Liver; kills parasites and strengthens the Stomach.', 'Avoid during pregnancy or menstruation, with Spleen and Stomach deficient Cold, or with rectal bleeding.', '1.5–4.5 g', 'Usually taken as a pill or capsule.'],
];

$purgativesimilarities = [
    ['Strong Heat-purgation', 'Da Huang · Mang Xiao · Lu Hui', 'All drain Heat and move stool, but their secondary actions determine the best choice.'],
    ['Hot, dry or hard stool', 'Da Huang · Mang Xiao', 'Da Huang strongly purges accumulation, while Mang Xiao moistens dryness and softens hardness; they are often paired for excess Heat constipation.'],
    ['Liver and toxic Heat', 'Da Huang · Lu Hui', 'Both enter the Liver; Da Huang adds Blood-moving and toxin-clearing actions, while Lu Hui more specifically cools the Liver and kills parasites.'],
    ['Blood Heat and Blood stasis', 'Da Huang', 'Da Huang uniquely stops bleeding when charred and invigorates Blood when wine-treated.'],
    ['External swelling', 'Mang Xiao', 'Mang Xiao may be used externally to clear Heat and reduce swelling in addition to its internal purgative action.'],
    ['Cold or deficient patients', 'Da Huang · Mang Xiao · Lu Hui', 'All are strong, cold downward-draining substances and require caution in deficiency, Cold patterns and pregnancy.'],
];

$laxativeherbs = [
    ['Huo Ma Ren', 'Cannabis Semen', 'Sweet', 'Neutral', 'LI, SP, ST', 'Nourishes and moistens the Intestines; nourishes Yin; clears Heat and promotes the healing of sores.', 'Excessive or long-term use may result in vaginal discharge or spermatorrhea.', '9–15 g', 'Crush before cooking.'],
];

$laxativesimilarities = [
    ['Gentle moistening laxative', 'Huo Ma Ren', 'Moistens the Intestines and gently moves stool without the strong cold purgative action of the Purgatives.'],
    ['Dryness versus excess Heat', 'Huo Ma Ren · Da Huang · Mang Xiao', 'Choose Huo Ma Ren for dryness or deficiency, Da Huang for strong excess accumulation, and Mang Xiao for hard, dry stool from excess Heat.'],
    ['Yin and intestinal moisture', 'Huo Ma Ren', 'Its nourishing quality makes it especially useful when constipation accompanies fluid or Yin insufficiency.'],
    ['Additional healing action', 'Huo Ma Ren', 'Beyond moistening the bowels, it clears Heat and supports the healing of sores.'],
];

$catharticherbs = [
    ['Da Ji', 'Euphorbiae Pekinensis Radix', 'Bitter, acrid', 'Cold, toxic', 'KD, LI, SP', 'Drains water downward and drives out congested fluids; reduces swelling and dissipates nodules.', 'Contraindicated in cold deficient edema and pregnancy; use cautiously in weak patients; incompatible with Gan Cao.', '1.5–3 g', '—'],
];

$catharticsimilarities = [
    ['Strong water-expelling cathartic', 'Da Ji', 'Drives congested fluids downward rather than primarily treating Heat constipation.'],
    ['Fluid accumulation versus stool accumulation', 'Da Ji · Da Huang · Mang Xiao', 'Use Da Ji for severe retained fluids and edema; use Da Huang or Mang Xiao when Heat and stool accumulation predominate.'],
    ['Swelling and nodules', 'Da Ji', 'Its downward-draining action is paired with an ability to reduce swelling and dissipate nodules.'],
    ['Toxicity and incompatibility', 'Da Ji', 'Because it is toxic and incompatible with Gan Cao, dosing and formula compatibility require particular care.'],
];

$diureticherbs = [
    ['Fu Ling', 'Poria', 'Sweet, bland', 'Neutral', 'HT, SP, KD, LU', 'Promotes urination, leaches Dampness and reduces edema; strengthens the Spleen; harmonizes the middle burner; transforms phlegm; quiets the Heart and calms the spirit.', 'Avoid with frequent, copious urination from deficient Cold; traditionally counteracts Di Yu, Qin Jiao and Bie Jia; avoid taking with vinegar.', '9–15 g', '—'],
    ['Yi Yi Ren', 'Coicis Semen', 'Sweet, bland', 'Slightly cold', 'LU, SP, ST, KD', 'Promotes urination and leaches Dampness; strengthens the Spleen; clears Heat and expels pus; relieves Wind-Damp painful obstruction.', 'Use cautiously during pregnancy.', '9–30 g', '—'],
    ['Zhu Ling', 'Polyporus', 'Sweet, bland', 'Slightly cold', 'SP, KD, UB', 'Promotes urination and leaches out Dampness, especially when water retention and edema predominate.', 'Avoid when Dampness is absent; prolonged use may injure Yin.', '6–12 g', '—'],
    ['Ze Xie', 'Alismatis Rhizoma', 'Sweet, bland', 'Cold', 'KD, UB', 'Promotes urination and leaches Dampness; drains Damp-Heat from the lower burner; settles ministerial Fire in the Kidney.', 'Avoid in spermatorrhea or vaginal discharge from Kidney Yang deficiency and in Damp-Cold patterns.', '4.5–9 g', '—'],
    ['Dong Gua Ren', 'Benincasae Semen', 'Sweet', 'Cold', 'LU, ST, LI, SI', 'Clears Heat and transforms phlegm; promotes urination; expels pus and supports the discharge of abscesses.', 'Use cautiously with Damp-Cold or loose stools.', '9–15 g', '—'],
    ['Han Fang Ji', 'Stephaniae Tetrandrae Radix', 'Bitter, acrid', 'Cold', 'UB, SP, KD', 'Promotes urination and reduces edema; dispels Wind-Damp and relieves pain from painful obstruction.', 'Avoid in Yin deficiency or when Dampness is absent; use cautiously with interior Cold.', '4.5–9 g', '—'],
    ['Yin Chen', 'Artemisiae Scopariae Herba', 'Bitter', 'Slightly cold', 'LV, SP, GB, ST', 'Clears Damp-Heat from the Liver and Gallbladder; promotes urination; is a principal herb for jaundice.', 'Avoid in deficiency jaundice with pale coloration, normal urine and signs that Damp-Heat is absent.', '9–30 g', '—'],
    ['Deng Xin Cao', 'Junci Medulla', 'Sweet, bland', 'Slightly cold', 'HT, LU, SI', 'Promotes urination and relieves painful urinary dribbling; clears Heart Heat and directs Fire downward through the Small Intestine.', 'Use cautiously in deficient Cold patterns.', '1.5–4.5 g', '—'],
    ['Mu Tong', 'Akebiae Caulis', 'Bitter', 'Slightly cold', 'UB, HT, SI', 'Promotes urination and relieves painful urinary dribbling; clears Heart Heat; unblocks Blood stasis and menstruation; promotes lactation.', 'Avoid during pregnancy and in deficiency without Damp-Heat; excessive use may damage Kidney function.', '3–6 g', 'Use only the correctly identified, non-aristolochic medicinal species.'],
    ['Dong Kui Zi', 'Malvae Semen', 'Sweet', 'Cold', 'UB, LI, SI', 'Promotes urination and relieves painful urinary dribbling; benefits the breasts and promotes lactation; moistens the Intestines.', 'Avoid with diarrhea from Spleen deficiency; use cautiously during pregnancy.', '3–9 g', '—'],
    ['Qu Mai', 'Dianthi Herba', 'Bitter', 'Cold', 'UB, HT, SI', 'Promotes urination and relieves painful urinary dribbling; breaks Blood stasis, unblocks menstruation and promotes lactation.', 'Avoid during pregnancy and in Spleen or Kidney deficiency; traditionally antagonizes Sang Piao Xiao.', '9–15 g', '—'],
    ['Bian Xu', 'Polygoni Avicularis Herba', 'Bitter', 'Slightly cold', 'LI, UB', 'Promotes urination and relieves painful urinary dribbling; expels parasites and stops itching.', 'Excessive dosage may drain essential Qi.', '9–15 g', '—'],
    ['Shi Wei', 'Pyrrosiae Folium', 'Bitter, sweet', 'Slightly cold', 'UB, LU', 'Promotes urination and relieves painful urinary dribbling; clears Heat and stops bleeding; clears Lung Heat, expels phlegm and stops cough.', 'Use cautiously when Damp-Heat is absent.', '6–9 g', '—'],
    ['Che Qian Zi', 'Plantaginis Semen', 'Sweet', 'Cold', 'UB, KD, LV, LU', 'Promotes urination and leaches Dampness; relieves painful urinary dribbling; solidifies stool in diarrhea; clears the eyes; clears Lung Heat and phlegm.', 'Avoid during pregnancy and use cautiously with exhausted Yang Qi, spermatorrhea from Kidney deficiency or severe exhaustion.', '9–15 g', 'Wrap in a porous bag before decocting.'],
    ['Hua Shi', 'Talcum', 'Sweet, bland', 'Cold', 'ST, UB', 'Promotes urination and relieves painful urinary dribbling; clears Summer-Heat; absorbs Dampness when used topically for eczema or sores.', 'Avoid with Spleen Qi deficiency, spermatorrhea, depleted fluids after warm-febrile disease or excessive urination; use cautiously during pregnancy.', '9–15 g', '—'],
    ['Di Fu Zi', 'Kochiae Fructus', 'Sweet, bitter', 'Cold', 'UB, KD', 'Promotes urination and clears Damp-Heat; expels Dampness and stops itching, especially in the genital region or skin.', 'Traditionally antagonizes Hai Piao Xiao.', '9–15 g', '—'],
    ['Bi Xie', 'Dioscoreae Hypoglaucae Rhizoma', 'Bitter', 'Neutral', 'UB, LV, ST', 'Separates the clear from the turbid in cloudy painful urinary disorders; dispels Wind-Damp and unblocks channels; clears Damp-Heat from the skin.', 'Avoid in Kidney Yin deficiency patterns.', '9–15 g', '—'],
];

$diureticsimilarities = [
    ['Edema and water retention', 'Fu Ling · Yi Yi Ren · Zhu Ling · Ze Xie · Han Fang Ji', 'All promote urination and reduce retained fluids; Fu Ling and Yi Yi Ren strengthen the Spleen, Zhu Ling emphasizes urination, Ze Xie drains lower-burner Heat, and Han Fang Ji treats Wind-Damp pain.'],
    ['Strengthen the Spleen while draining Dampness', 'Fu Ling · Yi Yi Ren', 'Both drain Dampness without strongly damaging the middle; Fu Ling also calms the spirit and transforms phlegm, while Yi Yi Ren clears Heat and expels pus.'],
    ['Damp-Heat jaundice', 'Yin Chen · Ze Xie', 'Yin Chen is the principal herb for Liver and Gallbladder Damp-Heat jaundice; Ze Xie supports lower-burner drainage when urinary Damp-Heat is present.'],
    ['Heart Fire through the Small Intestine', 'Deng Xin Cao · Mu Tong', 'Both clear Heart Heat and guide Fire downward through urination; Mu Tong is stronger for Blood stasis and lactation.'],
    ['Painful urinary dribbling', 'Deng Xin Cao · Mu Tong · Dong Kui Zi · Qu Mai · Bian Xu · Shi Wei · Che Qian Zi · Hua Shi', 'All address Lin syndrome, with selection based on Heart Fire, Blood stasis, bleeding, stones, diarrhea, Summer-Heat or constitutional deficiency.'],
    ['Blood stasis, menstruation and lactation', 'Mu Tong · Dong Kui Zi · Qu Mai', 'Mu Tong and Qu Mai unblock Blood and menstruation; all three may promote lactation, while Dong Kui Zi also moistens the Intestines.'],
    ['Lung, phlegm and cough involvement', 'Fu Ling · Dong Gua Ren · Shi Wei · Che Qian Zi', 'Fu Ling transforms phlegm, Dong Gua Ren clears Heat and expels pus, Shi Wei stops cough, and Che Qian Zi clears Lung Heat and phlegm.'],
    ['Skin Dampness and itching', 'Bian Xu · Hua Shi · Di Fu Zi · Bi Xie', 'Treat Damp-Heat affecting the skin; Bian Xu and Di Fu Zi emphasize itching, Hua Shi absorbs Dampness topically, and Bi Xie separates turbid Dampness.'],
    ['Cloudy urine and turbid Dampness', 'Bi Xie · Che Qian Zi · Ze Xie', 'Bi Xie is most specific for separating clear from turbid, while Che Qian Zi and Ze Xie promote urinary drainage and clear lower-burner Dampness.'],
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
.herb-category-library__comparison-table col:nth-child(2){width:7%}
.herb-category-library__comparison-table col:nth-child(3){width:7%}
.herb-category-library__comparison-table col:nth-child(4){width:7%}
.herb-category-library__comparison-table col:nth-child(5){width:24%}
.herb-category-library__comparison-table col:nth-child(6){width:19%}
.herb-category-library__comparison-table col:nth-child(7){width:8%}
.herb-category-library__comparison-table col:nth-child(8){width:14%}
.herb-category-library__herb-identity{background:#eef9f7}
.herb-category-library__herb-identity strong{display:block;color:#102a43;font-size:14px}
.herb-category-library__herb-identity small{display:block;margin-top:4px;color:#526b84;font-size:12px;font-style:italic;font-weight:600}
.herb-category-library__compact{font-size:12px}
.herb-category-library__dosage{white-space:normal;text-align:center;overflow-wrap:anywhere}
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
        <a href="#disperse-wind-heat">2. Disperse Wind-Heat</a>
        <a href="#clear-and-purge-heat">3. Clear and Purge Heat</a>
        <a href="#clear-blood-heat">4. Clear Blood-Heat</a>
        <a href="#clear-damp-heat">5. Clear Damp-Heat</a>
        <a href="#clear-toxic-heat">6. Clear Toxic-Heat</a>
        <a href="#clear-deficient-heat">7. Clear Deficient-Heat</a>
        <a href="#purgatives">8. Purgatives</a>
        <a href="#laxatives">9. Laxatives</a>
        <a href="#cathartics">10. Cathartics</a>
        <a href="#diuretics">11. Diuretics</a>
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

    <section class="herb-category-library__section" id="disperse-wind-heat">
        <h3>2. Disperse Wind-Heat</h3>
        <p class="herb-category-library__summary">Board Guideline 2026 · 11 herbs · Common foundation: predominantly cool and acrid herbs that disperse exterior Wind-Heat.</p>

        <div class="herb-category-library__similarities">
            <h4>Similarities and Clinical Overlaps</h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th>Comparison</th><th>Herbs</th><th>Shared clinical relationship</th></tr></thead>
                    <tbody>
                    <?php foreach ($windheatsimilarities as $similarity): ?>
                        <tr><td><?php echo s($similarity[0]); ?></td><td><?php echo s($similarity[1]); ?></td><td><?php echo s($similarity[2]); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong>Selection focus:</strong> throat and rash involvement, eye symptoms, headache location, Lung dryness, Shaoyang signs and sinking Qi.</p>
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
                <?php foreach ($windheatherbs as $herb): ?>
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


    <section class="herb-category-library__section" id="clear-and-purge-heat">
        <h3>3. Clear and Purge Heat</h3>
        <p class="herb-category-library__summary">Board Guideline 2026 · 8 herbs · Common foundation: cold herbs that clear internal Heat, drain Fire or protect fluids.</p>

        <div class="herb-category-library__similarities">
            <h4>Similarities and Clinical Overlaps</h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th>Comparison</th><th>Herbs</th><th>Shared clinical relationship</th></tr></thead>
                    <tbody>
                    <?php foreach ($clearheatsimilarities as $similarity): ?>
                        <tr><td><?php echo s($similarity[0]); ?></td><td><?php echo s($similarity[1]); ?></td><td><?php echo s($similarity[2]); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong>Selection focus:</strong> severity of Fire, fluid injury, Lung or Stomach involvement, irritability, urinary signs, eye symptoms and nodules.</p>
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
                <?php foreach ($clearheatherbs as $herb): ?>
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


    <section class="herb-category-library__section" id="clear-blood-heat">
        <h3>4. Clear Blood-Heat</h3>
        <p class="herb-category-library__summary">Board Guideline 2026 · 5 herbs · Common foundation: cool Blood-level Heat and protect against injury from Fire.</p>

        <div class="herb-category-library__similarities">
            <h4>Similarities and Clinical Overlaps</h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th>Comparison</th><th>Herbs</th><th>Shared clinical relationship</th></tr></thead>
                    <tbody>
                    <?php foreach ($bloodheatsimilarities as $similarity): ?>
                        <tr><td><?php echo s($similarity[0]); ?></td><td><?php echo s($similarity[1]); ?></td><td><?php echo s($similarity[2]); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong>Selection focus:</strong> toxin severity, bleeding, Yin injury, deficiency Heat, Blood stasis, throat swelling and skin manifestations.</p>
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
                <?php foreach ($bloodheatherbs as $herb): ?>
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

    <section class="herb-category-library__section" id="clear-damp-heat">
        <h3>5. Clear Damp-Heat</h3>
        <p class="herb-category-library__summary">Board Guideline 2026 · 5 herbs · Common foundation: bitter, cold herbs that clear Heat and dry Dampness.</p>

        <div class="herb-category-library__similarities">
            <h4>Similarities and Clinical Overlaps</h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th>Comparison</th><th>Herbs</th><th>Shared clinical relationship</th></tr></thead>
                    <tbody>
                    <?php foreach ($dampheatsimilarities as $similarity): ?>
                        <tr><td><?php echo s($similarity[0]); ?></td><td><?php echo s($similarity[1]); ?></td><td><?php echo s($similarity[2]); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong>Selection focus:</strong> upper, middle or lower burner location; Liver/Gallbladder Fire; diarrhea; jaundice; urinary or genital signs; and itching.</p>
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
                <?php foreach ($dampheatherbs as $herb): ?>
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

    <section class="herb-category-library__section" id="clear-toxic-heat">
        <h3>6. Clear Toxic-Heat</h3>
        <p class="herb-category-library__summary">Board Guideline 2026 · 9 herbs · Common foundation: clear Heat and relieve Fire toxicity, with distinct clinical locations and secondary actions.</p>

        <div class="herb-category-library__similarities">
            <h4>Similarities and Clinical Overlaps</h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th>Comparison</th><th>Herbs</th><th>Shared clinical relationship</th></tr></thead>
                    <tbody>
                    <?php foreach ($toxicheatsimilarities as $similarity): ?>
                        <tr><td><?php echo s($similarity[0]); ?></td><td><?php echo s($similarity[1]); ?></td><td><?php echo s($similarity[2]); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong>Selection focus:</strong> Wind-Heat stage, throat severity, abscess location, Damp-Heat involvement, skin lesions, Summer-Heat and bleeding.</p>
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
                <?php foreach ($toxicheatherbs as $herb): ?>
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

    <section class="herb-category-library__section" id="clear-deficient-heat">
        <h3>7. Clear Deficient-Heat</h3>
        <p class="herb-category-library__summary">Board Guideline 2026 · 3 herbs · Common foundation: clear deficient Heat and cool the Blood.</p>

        <div class="herb-category-library__similarities">
            <h4>Similarities and Clinical Overlaps</h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th>Comparison</th><th>Herbs</th><th>Shared clinical relationship</th></tr></thead>
                    <tbody>
                    <?php foreach ($deficientheatsimilarities as $similarity): ?>
                        <tr><td><?php echo s($similarity[0]); ?></td><td><?php echo s($similarity[1]); ?></td><td><?php echo s($similarity[2]); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong>Selection focus:</strong> sweating with Yin-deficiency Heat, Lung cough, Summer-Heat or malarial patterns, urinary involvement, toxicity and swelling.</p>
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
                <?php foreach ($deficientheatherbs as $herb): ?>
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

    <section class="herb-category-library__section" id="purgatives">
        <h3>8. Purgatives</h3>
        <p class="herb-category-library__summary">Board Guideline 2026 · 3 herbs · Common foundation: drain Heat and move stool to purge accumulation.</p>

        <div class="herb-category-library__similarities">
            <h4>Similarities and Clinical Overlaps</h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th>Comparison</th><th>Herbs</th><th>Shared clinical relationship</th></tr></thead>
                    <tbody>
                    <?php foreach ($purgativesimilarities as $similarity): ?>
                        <tr><td><?php echo s($similarity[0]); ?></td><td><?php echo s($similarity[1]); ?></td><td><?php echo s($similarity[2]); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong>Selection focus:</strong> strength of purgation, dry-hard stool, Blood Heat or stasis, Liver Heat, external swelling and constitutional deficiency.</p>
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
                <?php foreach ($purgativeherbs as $herb): ?>
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

    <section class="herb-category-library__section" id="laxatives">
        <h3>9. Laxatives</h3>
        <p class="herb-category-library__summary">Board Guideline 2026 · 1 herb · Common foundation: gently moisten the Intestines and relieve dry constipation.</p>

        <div class="herb-category-library__similarities">
            <h4>Similarities and Clinical Overlaps</h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th>Comparison</th><th>Herbs</th><th>Shared clinical relationship</th></tr></thead>
                    <tbody>
                    <?php foreach ($laxativesimilarities as $similarity): ?>
                        <tr><td><?php echo s($similarity[0]); ?></td><td><?php echo s($similarity[1]); ?></td><td><?php echo s($similarity[2]); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong>Selection focus:</strong> dryness or deficiency constipation, need for gentle action, Yin and fluid status, and long-term-use cautions.</p>
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
                <?php foreach ($laxativeherbs as $herb): ?>
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

    <section class="herb-category-library__section" id="cathartics">
        <h3>10. Cathartics</h3>
        <p class="herb-category-library__summary">Board Guideline 2026 · 1 herb · Common foundation: strongly drain retained fluids downward.</p>

        <div class="herb-category-library__similarities">
            <h4>Similarities and Clinical Overlaps</h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th>Comparison</th><th>Herbs</th><th>Shared clinical relationship</th></tr></thead>
                    <tbody>
                    <?php foreach ($catharticsimilarities as $similarity): ?>
                        <tr><td><?php echo s($similarity[0]); ?></td><td><?php echo s($similarity[1]); ?></td><td><?php echo s($similarity[2]); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong>Selection focus:</strong> severe fluid accumulation, edema, swelling or nodules, patient strength, toxicity and formula incompatibility.</p>
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
                <?php foreach ($catharticherbs as $herb): ?>
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

    <section class="herb-category-library__section" id="diuretics">
        <h3>11. Diuretics</h3>
        <p class="herb-category-library__summary">Board Guideline 2026 · 17 herbs · Common foundation: promote urination, eliminate Water and Dampness, reduce edema or relieve painful urinary dribbling.</p>

        <div class="herb-category-library__similarities">
            <h4>Similarities and Clinical Overlaps</h4>
            <div class="herb-category-library__scroll">
                <table>
                    <thead><tr><th>Comparison</th><th>Herbs</th><th>Shared clinical relationship</th></tr></thead>
                    <tbody>
                    <?php foreach ($diureticsimilarities as $similarity): ?>
                        <tr><td><?php echo s($similarity[0]); ?></td><td><?php echo s($similarity[1]); ?></td><td><?php echo s($similarity[2]); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="herb-category-library__foundation"><strong>Selection focus:</strong> edema versus Lin syndrome, Spleen deficiency, jaundice, Heart Fire, bleeding, Blood stasis, lactation, Lung phlegm, itching and cloudy urine.</p>
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
                <?php foreach ($diureticherbs as $herb): ?>
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
