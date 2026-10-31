<?php
// This file is part of Moodle - http://moodle.org/.

require_once(__DIR__ . '/../../config.php');

require_login();

$returnurl = new moodle_url('/blocks/myprogress/study-guide.php', ['area' => 'herbology']);
$libraryurl = new moodle_url('/blocks/myprogress/study-guide.php', [
    'area' => 'herbology',
    'view' => 'library',
]);
$pageurl = new moodle_url('/blocks/myprogress/common-herbal-combinations.php');

$PAGE->set_url($pageurl);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Commonly Used Herbal Combinations');
$PAGE->set_heading('Commonly Used Herbal Combinations');

$groups = json_decode(<<<'JSON'
[
  {
    "name": "Mutual accentuation (Xiang Xu)",
    "items": [
      {
        "title": "To expel Wind and release the Exterior syndrome",
        "herbs": [
          "Jing Jie (Schizonepetae herba): Expels Wind from the superficial region;",
          "Fang Feng (Saposhnikoviae radix): Expels Wind from skin, subcutaneous region."
        ]
      },
      {
        "title": "To expel Wind-Heat and treat Wind-Heat in the Upper Jiao",
        "herbs": [
          "Sang Ye (Mori folium): Expels Wind-Heat in the Lung and Liver;",
          "Ju Hua (Chrysanthemi flos): Expels Wind-Heat in the Liver and Lung."
        ]
      },
      {
        "title": "To expel Wind-Heat and relieve itch",
        "herbs": [
          "Chan Tui (Cicadae periostracum): Expels Wind, clears Heat of the superficial region;",
          "Bo He (Menthae herba): Expels Wind-Heat from the Upper Jiao."
        ]
      },
      {
        "title": "To treat Wind-Heat in the Lung",
        "herbs": [
          "Jin Yin Hua (Lonicerae flos): Expels Wind-Heat and removes Heat-toxin;",
          "Lian Qiao (Forsythiae fructus): Expels Heat from the Heart and removes Heat-toxin."
        ]
      },
      {
        "title": "To expel Wind, Damp and Cold and treat the Bi syndrome",
        "herbs": [
          "Qiang Huo (Notopterygii rhizoma): Expels Wind-Damp-Cold in the upper body;",
          "Du Huo (Angelicae pubescentis radix): Expels Wind-Damp-Cold in the lower body."
        ]
      },
      {
        "title": "To reduce excess Heat in the Stomach and Lung",
        "herbs": [
          "Shi Gao (Gypsum): Clears excess Heat of Stomach and Lung, increases Body Fluid;",
          "Zhi Mu (Anemarrhenae rhizoma): Clears Heat and nourishes Yin of the Kidney, Stomach and Lung."
        ]
      },
      {
        "title": "To clear Heat in the San Jiao",
        "herbs": [
          "Huang Qin (Scutellariae radix): Clears Heat in the Upper Jiao;",
          "Huang Lian (Coptidis rhizoma): Clears Heat in the Middle Jiao;",
          "Huang Bai (Phellodendri cortex): Clears Heat in the Lower Jiao."
        ]
      },
      {
        "title": "To tonify and stabilize the Spleen-Qi",
        "herbs": [
          "Huang Qi (Astragali radix): Tonifies the Spleen-Qi and ascends the Qi;",
          "Shan Yao (Dioscoreae rhizoma): Tonifies the Spleen-Qi and stabilizes Essence."
        ]
      },
      {
        "title": "To tonify and stabilize the Qi and Essence in the Middle Jiao",
        "herbs": [
          "Shan Yao (Dioscoreae rhizoma): Tonifies the Spleen-Qi and stabilizes Essence;",
          "Bai Bian Dou (Dolichoris lablab semen): Tonifies the Spleen-Qi, stabilizes Essence and removes Dampness."
        ]
      },
      {
        "title": "To tonify the Spleen-Qi and dry Dampness in the Middle Jiao",
        "herbs": [
          "Bai Zhu (Atractylodis macrocephalae rhizoma): Tonifies the Spleen-Qi and dries Dampness;",
          "Fu Ling (Poria): Tonifies the Spleen-Qi and drains Dampness."
        ]
      },
      {
        "title": "To treat Kidney-Yin and Essence deficiency",
        "herbs": [
          "Sheng Di Huang (Rehmanniae radix): Nourishes Kidney-Yin and reduces Empty-Heat;",
          "Shu Di Huang (Rehmanniae radix praeparata): Nourishes Kidney-Yin, Blood and Essence."
        ]
      },
      {
        "title": "To moisten the intestine and promote bowel movement",
        "herbs": [
          "Dang Gui (Angelicae sinensis radix): Tonifies Blood and moistens the intestine;",
          "Rou Cong Rong (Cistanchis herba): Tonifies the Kidney-Yang and Kidney-Essence and moistens the intestine."
        ]
      },
      {
        "title": "To nourish the Yin",
        "herbs": [
          "Tian Men Dong (Asparagi radix): Nourishes the Kidney-Yin and Lung-Yin;",
          "Mai Men Dong (Ophiopogonis radix): Nourishes the Heart-Yin, Stomach-Yin and Lung-Yin."
        ]
      },
      {
        "title": "To tonify the Kidney-Yang and expel Wind, Damp and Cold",
        "herbs": [
          "Xian Mao (Curculinginis rhizoma): Warms the Kidney-Yang, expels Wind, Damp and Cold;",
          "Xian Ling Pi (Epimedii herba): Tonifies the Kidney-Yang, expels Wind, Damp and Cold;",
          "Ba Ji Tian (Morindae radix): Tonifies the Kidney-Yang, expels Wind, Damp and Cold."
        ]
      },
      {
        "title": "To regulate Qi and remove Phlegm in the Stomach and Lung",
        "herbs": [
          "Chen Pi (Citri reticulatae pericarpium): Regulates Qi in the Stomach, Spleen and Lung;",
          "Ban Xia (Pinelliae rhizoma): Soothes Stomach-Qi, removes Phlegm from Stomach and Lung."
        ]
      },
      {
        "title": "To regulate the Qi and relieve distending pain in the abdomen",
        "herbs": [
          "Mu Xiang (Aucklandiae radix)**: Regulates Qi in Spleen and Liver;",
          "Sha Ren (Amomi xanthioidis fructus): Regulates Qi in Spleen and intestine."
        ]
      },
      {
        "title": "To reduce distension in the Stomach and hypochondriac region",
        "herbs": [
          "Qing Pi (Citri reticulatae viride pericarpium): Regulates the Qi of the Liver;",
          "Chen Pi (Citri reticulatae pericarpium): Regulates the Qi of the Spleen and Stomach."
        ]
      },
      {
        "title": "To promote Qi movement and remove Phlegm",
        "herbs": [
          "Zhi Shi (Aurantii fructus immaturus): Descends the Qi in the intestine and removes Phlegm;",
          "Hou Po (Magnoliae cortex): Descends the Qi in the Lung, Spleen and intestine, removes Phlegm."
        ]
      },
      {
        "title": "To reduce Qi, water and food stagnation in the abdomen",
        "herbs": [
          "Da Fu Pi (Arecae pericarpium): Promotes Qi movement and drains water in the abdomen;",
          "Bing Lang (Arecae semen): Promotes Qi movement and digestion, promotes bowel movement."
        ]
      },
      {
        "title": "To relieve pain due to Blood stagnation",
        "herbs": [
          "Pu Huang (Typhae pollen): Promotes Blood circulation, dissolves congealed Blood;",
          "Wu Ling Zhi (Trogopterori faeces): Dissolves congealed Blood."
        ]
      },
      {
        "title": "To remove congealed Blood and stop pain",
        "herbs": [
          "Tao Ren (Persicae semen): Promotes Blood circulation, breaks up congealed Blood;",
          "Hong Hua (Carthami flos): Promotes Blood circulation and dissolves congealed Blood."
        ]
      },
      {
        "title": "To dissolve and break up congealed Blood, stop pain and applied in trauma",
        "herbs": [
          "Ru Xiang (Olibanum): Promotes Qi and Blood circulation, removes congealed Blood;",
          "Mo Yao (Myrrhae): Removes congealed Blood and softens the hardness."
        ]
      },
      {
        "title": "To break up congealed Blood and treat palpable mass",
        "herbs": [
          "E Zhu (Curcumae rhizoma): Promotes Qi and Blood circulation, removes congealed Blood;",
          "San Leng (Sparganii rhizoma): Removes congealed Blood and softens the hardness."
        ]
      },
      {
        "title": "To reduce fullness of Stomach, nausea and vomiting due to Dampness in the Middle Jiao",
        "herbs": [
          "Huo Xiang (Agastachis herba): Aromatically transforms Dampness in the Middle Jiao;",
          "Pei Lan (Eupatorii herba): Aromatically transforms Dampness in the Middle Jiao."
        ]
      },
      {
        "title": "To calm the Mind and treat restlessness and insomnia",
        "herbs": [
          "Bai Zi Ren (Platycladi semen): Calms the Mind and improves sleep;",
          "Suan Zao Ren (Ziziphi spinosae semen): Nourishes the Liver-Blood, improves sleep."
        ]
      },
      {
        "title": "To calm the Mind by descending the Liver-Yang",
        "herbs": [
          "Long Gu (Mastodi fossilium ossis): Descends the Liver-Yang, calms the Mind;",
          "Mu Li (Ostrea concha): Descends the Liver-Yang, clears Heat and calms the Mind."
        ]
      },
      {
        "title": "To calm the Mind by sedating the Heart-Spirit",
        "herbs": [
          "Ci Shi (Magnetitum): Descends the Liver-Yang, sedates the Heart-Spirit and calms the Mind;",
          "Zhen Zhu Mu (Concha margaritifera usta): Sedates the Heart-Spirit, clears Heat and calms the Mind."
        ]
      },
      {
        "title": "To soften hardness, remove Phlegm and therefore treat mass, such as tumor",
        "herbs": [
          "Hai Zao (Sargassum): Removes Phlegm and softens hardness;",
          "Kun Bu (Eckloniae thallus): Removes Phlegm and softens hardness."
        ]
      },
      {
        "title": "To treat concentration disorder, anxiety and confusion due to Damp-Phlegm covering the Mind",
        "herbs": [
          "Shi Chang Pu (Acori graminei rhizoma): Aromatically transforms Damp-Phlegm, opens the orifices;",
          "Yuan Zhi (Polygalae radix): Removes Phlegm from Heart meridian and associates the Heart with the Kidney."
        ]
      },
      {
        "title": "To expel Wind and open the meridians and collaterals",
        "herbs": [
          "Bai Hua She (Agkistrodon acutus)*: Expels Wind;",
          "Wu Shao She (Zaocys): Expels Wind."
        ]
      }
    ]
  },
  {
    "name": "Mutual enhancement (Xiang Shi)",
    "items": [
      {
        "title": "To clear Heat in the Lower Jiao",
        "herbs": [
          "Zhi Mu (Anemarrhenae rhizoma): Nourishes Yin of the Kidney and clears Heat in the Lower Jiao;",
          "Huang Bai (Phellodendri cortex): Reduces Empty-Heat in the Lower Jiao."
        ]
      },
      {
        "title": "To nourish the Kidney-Yin",
        "herbs": [
          "Nu Zhen Zi (Ligustri lucidi fructus): Nourishes Kidney-Yin and clears Heat;",
          "Han Lian Cao (Ecliptae herba): Clears Heat of the Kidney and Liver, nourishes the Yin."
        ]
      },
      {
        "title": "To stimulate the Kidney-Yang, expel Cold and warm the Interior",
        "herbs": [
          "Fu Zi (Aconiti radix lateralis preparata)*: Stimulates Yang, warms the Interior, expels Cold;",
          "Rou Gui (Cinnamomi cassiae cortex): Warms the Interior, stimulates Blood, expels Cold and enhances the function of Fu Zi."
        ]
      },
      {
        "title": "To expel Wind-Cold sufficiently in the Exterior",
        "herbs": [
          "Ma Huang (Ephedrae herba)*: Stimulates the Yang and Qi of the Lung and Bladder meridians, induces sweating and releases the Exterior;",
          "Gui Zhi (Cinnamomi cassiae ramulus): Stimulates the Yang and Qi in Blood, promotes Blood circulation and enhances the function of Ma Huang."
        ]
      },
      {
        "title": "To warm the Middle Jiao, treat abdominal pain and diarrhea due to Cold in the Middle Jiao",
        "herbs": [
          "Gan Jiang (Zingiberis rhizoma): Warms the Spleen-Yang, expels Cold;",
          "Fu Zi (Aconiti radix lateralis preparata): Stimulates Yang, warms the Interior, expels Cold and enhances the function of Gan Jiang."
        ]
      },
      {
        "title": "To regulate the Lung-Qi and relieve wheezing",
        "herbs": [
          "Ma Huang (Ephedrae herba)*: Disperses and ascends the Lung-Qi;",
          "Xing Ren (Armeniacae semen): Disperses and descends the Lung-Qi and transforms Phlegm, particularly enhances the descending function of Ma Huang."
        ]
      }
    ]
  },
  {
    "name": "Mutual enhancement (Xiang Shi) / Mutual counteraction (Xiang Wei)",
    "items": [
      {
        "title": "To ascend the Yang and strengthen the Exterior",
        "herbs": [
          "Huang Qi (Astragali radix): Strengthens and stabilizes the Defensive Qi;",
          "Fang Feng (Saposhnikoviae radix): Disperses the Wind from the Exterior."
        ]
      },
      {
        "title": "To strengthen the Heart-Qi, treat restlessness and palpitations",
        "herbs": [
          "Ren Shen (Ginseng radix): Tonifies the Heart-Qi;",
          "Wu Wei Zi (Schisandrae fructus): Stabilizes the Heart-Qi."
        ]
      },
      {
        "title": "To tonify the Blood and Essence",
        "herbs": [
          "Shu Di Huang (Rehmanniae radix praeparata): Tonifies the Blood and Essence;",
          "Sha Ren (Amomi xanthioidis fructus): Promotes the Qi movement and removes the sticky nature of Shu Di Huang."
        ]
      },
      {
        "title": "To moderately drain Heat accumulation in the intestine",
        "herbs": [
          "Da Huang (Rhei rhizoma): Drains Heat and purges the intestine;",
          "Zhi Gan Cao (Glycyrrhizae radix preparata): Moderates the harsh nature and the speed of Da Huang."
        ]
      },
      {
        "title": "To steadily warm the Interior",
        "herbs": [
          "Fu Zi (Aconiti radix lateralis preparata)*: Strongly moves the Qi, spreads the Yang and disperses the internal Cold;",
          "Zhi Gan Cao (Glycyrrhizae radix preparata): Moderates the harsh nature and the speed of Fu Zi."
        ]
      },
      {
        "title": "To relieve the Cold-type wheezing",
        "herbs": [
          "Xi Xin (Asari herba)*: Disperses the Lung-Qi, warms the Interior;",
          "Sheng Jiang (Zingiberis rhizoma recens): Warms the Stomach and disperses the Cold and congested water;",
          "Wu Wei Zi (Schisandrae fructus): Stabilizes the Lung-Qi."
        ]
      },
      {
        "title": "To treat excessive Heat in the Stomach",
        "herbs": [
          "Huang Lian (Coptidis rhizoma): Descends and clears the Fire from the Stomach;",
          "Sheng Ma (Cimicifugae rhizoma): Ascends and disperses the constrained Qi and Fire in the Stomach."
        ]
      },
      {
        "title": "To reduce constrained Fire in the Spleen",
        "herbs": [
          "Shi Gao (Gypsum): Descends the Fire from Spleen and Stomach;",
          "Fang Feng (Saposhnikoviae radix): Disperses constrained Heat and Qi."
        ]
      },
      {
        "title": "To harmonize the Qi movement in the process of descending the Liver-Yang",
        "herbs": [
          "Dai Zhe Shi (Haematitum) and Shi Jue Ming (Haliotidis concha): Descend Liver-Yang;",
          "Qing Hao (Artemisiae annuae herba) and Mai Ya (Hordei fructus germinatus): Ascend suppressed Qi from the Middle Jiao."
        ]
      },
      {
        "title": "To calm the Mind and treat restlessness and insomnia",
        "herbs": [
          "Long Gu (Mastodi fossilium ossis) and Mu Li (Ostrea concha): Sedate the Spirit and calm the Mind;",
          "Chai Hu (Bupleuri radix): Ascends and spreads the Liver-Qi."
        ]
      },
      {
        "title": "To treat constipation and distension in the abdomen",
        "herbs": [
          "Da Huang (Rhei rhizoma): Purges the intestines and moves stool;",
          "Jie Geng (Platycodi radix): Ascends the Lung-Qi to accelerate the Qi downward moving in the Large Intestine."
        ]
      },
      {
        "title": "To treat irritability due to constraint of Qi and Heat in the chest",
        "herbs": [
          "Zhi Zi (Gardeniae fructus): Descends Heat in the chest;",
          "Dan Dou Chi (Sojae semen praeparatum): Disperses Heat in the chest."
        ]
      }
    ]
  },
  {
    "name": "Combinations for complicated syndromes or disorders",
    "items": [
      {
        "title": "To tonify the Blood and regulate the circulation",
        "herbs": [
          "Dang Gui (Angelicae sinensis radix): Warms the Blood, tonifies the Blood and promotes its circulation;",
          "Bai Shao Yao (Paeoniae radix lactiflora): Cools the Blood, tonifies the Blood and stabilizes the Blood circulation."
        ]
      },
      {
        "title": "To stimulate Blood circulation",
        "herbs": [
          "Dang Gui (Angelicae sinensis radix): Warms the Blood, tonifies the Blood and promotes its circulation;",
          "Chuan Xiong (Chuanxiong rhizoma): Promotes Blood circulation."
        ]
      },
      {
        "title": "To activate the Blood circulation and spread the warmth in the body",
        "herbs": [
          "Dang Gui (Angelicae sinensis radix): Warms the Blood, tonifies the Blood and promotes its circulation;",
          "Gui Zhi (Cinnamomi cassiae ramulus): Warms the Blood and stimulates the circulation."
        ]
      },
      {
        "title": "To harmonize the Ying and Wei (Qi and Blood)",
        "herbs": [
          "Gui Zhi (Cinnamomi cassiae ramulus): Disperses the Defensive Qi;",
          "Bai Shao Yao (Paeoniae radix lactiflora): Nourishes the Nutritive Qi."
        ]
      },
      {
        "title": "To tonify the Yin, soften the Liver and ease muscular cramp",
        "herbs": [
          "Bai Shao Yao (Paeoniae radix lactiflora): Sour and Cold, nourishes the Yin and the Blood, softens the Liver;",
          "Zhi Gan Cao (Glycyrrhizae radix preparata): Sweet and warm, together with Bai Shao Yao generates and stabilizes the Yin."
        ]
      },
      {
        "title": "To harmonize the Liver-Qi",
        "herbs": [
          "Chai Hu (Bupleuri radix): Disperses and ascends the Qi of the Liver and Gall Bladder;",
          "Bai Shao Yao (Paeoniae radix lactiflora): Nourishes the Liver-Yin and Blood, softens the Liver."
        ]
      },
      {
        "title": "To protect the Middle Jiao and promote digestion",
        "herbs": [
          "Sheng Jiang (Zingiberis rhizoma recens): Warms the Stomach, promotes digestion;",
          "Da Zao (Jujubae fructus): Tonifies the Qi and Blood, strengthens the Middle Jiao."
        ]
      },
      {
        "title": "To regulate the Lung-Qi, clear the Heat and relieve shortness of breath",
        "herbs": [
          "Ma Huang (Ephedrae herba)*: Disperses the Lung Qi, opens the obstruction of the Lung;",
          "Shi Gao (Gypsum): Descends the Lung-Qi and clears the Heat of the Lung."
        ]
      },
      {
        "title": "To treat pain due to Qi and Blood stagnation, especially in hypochondriac regions and the lateral sides of the abdomen",
        "herbs": [
          "Chuan Lian Zi (Toosendan fructus): Drains the Liver-Heat and Qi, reduces Qi stagnation;",
          "Yan Hu Suo (Corydalidis rhizoma): Promotes Blood circulation and reduces pain."
        ]
      },
      {
        "title": "To reduce the Heat in the Blood and promote Blood circulation",
        "herbs": [
          "Mu Dan Pi (Moutan cortex): Reduces the Empty-Heat in the Blood;",
          "Chi Shao Yao (Paeoniae radix rubra): Reduces the excess Heat in the Blood."
        ]
      },
      {
        "title": "To promote digestion",
        "herbs": [
          "Mai Ya (Hordei fructus germinatus): Adds the digestion of starch food;",
          "Shen Qu (Massa medicata fermentata): Adds the digestion of cereals and alcohol;",
          "Shan Zha (Crataegi fructus): Adds the digestion of meat and fat."
        ]
      },
      {
        "title": "To purge the Heat accumulation in the intestine",
        "herbs": [
          "Da Huang (Rhei rhizoma): Stimulates the intestine, drains Heat, purges the accumulation in the intestine;",
          "Mang Xiao (Natrii sulfas): Increases the fluid in the intestine, softens feces, drains Heat."
        ]
      },
      {
        "title": "To open the San Jiao, separate Damp-Heat in the San Jiao",
        "herbs": [
          "Xing Ren (Armeniacae semen): Disperses the Lung-Qi, opens the Upper Jiao;",
          "Bai Dou Kou (Amomi fructus rotundus): Promotes the Qi movement, transforms Dampness and opens the Middle Jiao;",
          "Yi Yi Ren (Coicis semen): Promotes urination, drains Damp-Heat and clears the Lower Jiao."
        ]
      },
      {
        "title": "To expel Wind-Heat from the Lung, descend the Lung-Qi and relieve wheezing",
        "herbs": [
          "Sang Ye (Mori folium): Disperses Wind-Heat;",
          "Sang Bai Pi (Mori cortex): Clears Heat and descends Qi of the Lung."
        ]
      },
      {
        "title": "To regulate the function of the Lung and remove Phlegm",
        "herbs": [
          "Jie Geng (Platycodi radix): Ascends the Lung-Qi, eliminates Phlegm and stops cough;",
          "Xing Ren (Armeniacae semen): Descends the Lung-Qi, eliminates Phlegm and stops cough."
        ]
      },
      {
        "title": "Regulating the Qi in the Middle Jiao and promoting digestion",
        "herbs": [
          "Bai Zhu (Atractylodis macrocephalae rhizoma): Strengthens the Spleen-Qi and promotes the function of transportation and transformation of the Spleen;",
          "Zhi Shi (Aurantii fructus immaturus): Descends the Qi in the intestine and removes the accumulation of food, Phlegm and Qi."
        ]
      },
      {
        "title": "To regulate the Stomach, treat nausea and poor appetite, especially under stress",
        "herbs": [
          "Ban Xia (Pinelliae rhizoma): Disperses stagnation of Stomach-Qi and accumulation of Phlegm;",
          "Huang Qin (Scutellariae radix): Clears Heat that is caused by the accumulations in the Stomach."
        ]
      },
      {
        "title": "To eliminate Dampness and treat edema",
        "herbs": [
          "Cang Zhu (Atractylodis rhizoma): Disperses and dries Dampness;",
          "Huang Bai (Phellodendri cortex): Clears Heat and dries Dampness in the Lower Jiao."
        ]
      },
      {
        "title": "To reduce distension in the chest and abdomen",
        "herbs": [
          "Zhi Ke (Aurantii fructus): Disperses the Qi and opens the chest;",
          "Zhi Shi (Aurantii fructus immaturus): Descends the Qi in the intestine."
        ]
      },
      {
        "title": "To associate the Heart and Kidney and treat insomnia",
        "herbs": [
          "Huang Lian (Coptidis rhizoma): Reduces the excessive Heat from the Heart;",
          "Rou Gui (Cinnamomi cassiae cortex): Strengthens the Kidney-Yang and warms the vital Fire."
        ]
      },
      {
        "title": "To eliminate Blood stasis in the chest",
        "herbs": [
          "Chai Hu (Bupleuri radix) and Jie Geng (Platycodi radix): Ascend and disperse Qi;",
          "Zhi Ke (Aurantii fructus): Broadens the chest, activates Qi movement;",
          "Chuan Niu Xi (Cyathulae radix): Directs the Blood downwards."
        ]
      },
      {
        "title": "To enter the Yin level and return to the Qi level in order to eliminate Heat at the Yin level in febrile disease",
        "herbs": [
          "Qing Hao (Artemisiae annuae herba): Enters the Yin level by the guidance of Bie Jia, brings the Heat to the Qi level and disperses it;",
          "Bie Jia (Trionycis carapax)**: Enters the Yin level and clears Heat there, moves out to the Qi level under the guidance of Qing Hao."
        ]
      }
    ]
  }
]
JSON, true, 512, JSON_THROW_ON_ERROR);

$groupdescriptions = [
    'Mutual accentuation (Xiang Xu)' => 'Herbs with similar functions are combined to reinforce the therapeutic effect.',
    'Mutual enhancement (Xiang Shi)' => 'One herb directly addresses the condition while another increases its therapeutic effect.',
    'Mutual enhancement (Xiang Shi) / Mutual counteraction (Xiang Wei)' => 'The principal herb treats the disorder while assisting herbs moderate side effects, speed, temperature or direction.',
    'Combinations for complicated syndromes or disorders' => 'Herbs with different but complementary functions work together as one therapeutic unit.',
];

$total = array_sum(array_map(static function(array $group): int {
    return count($group['items']);
}, $groups));

echo $OUTPUT->header();
?>
<style>
.herb-combos{max-width:1120px;margin:24px auto 56px;padding:0 16px;color:#173047}
.herb-combos__top{display:flex;align-items:center;justify-content:space-between;gap:18px;margin-bottom:18px}
.herb-combos__top-actions{display:flex;gap:8px;flex-wrap:wrap}
.herb-combos__brand{font-size:18px;font-weight:850;color:#173047}
.herb-combos__back{display:inline-flex;min-height:42px;align-items:center;padding:9px 15px;border:1px solid #c7d3dc;border-radius:9px;background:#fff;color:#173047;font-weight:800;text-decoration:none}
.herb-combos__back:hover{background:#f1f5f9;text-decoration:none}
.herb-combos__hero{padding:clamp(28px,5vw,48px);border-top:5px solid #f77e25;border-radius:14px;background:linear-gradient(135deg,#fffaf5,#faf8ff);box-shadow:0 4px 16px rgba(15,23,42,.06)}
.herb-combos__eyebrow{margin:0 0 8px;color:#08746f;font-size:13px;font-weight:900;letter-spacing:.09em;text-transform:uppercase}
.herb-combos__hero h1{margin:0 0 12px;font-size:clamp(32px,5vw,50px);line-height:1.08;color:#102d46}
.herb-combos__hero p:last-child{max-width:850px;margin:0;color:#52677a;font-size:17px;line-height:1.6}
.herb-combos__source{margin:18px 0;padding:16px 18px;border-left:5px solid #0b817d;border-radius:8px;background:#f0fdfa;color:#345568;line-height:1.55}
.herb-combos__source strong{color:#075f5b}
.herb-combos__tools{position:sticky;top:0;z-index:4;margin:18px 0 24px;padding:14px;border:1px solid #d5e0e7;border-radius:11px;background:rgba(255,255,255,.96);box-shadow:0 4px 12px rgba(15,23,42,.05)}
.herb-combos__search{width:100%;min-height:46px;box-sizing:border-box;padding:10px 14px;border:1px solid #b8c8d3;border-radius:8px;font:inherit;color:#173047}
.herb-combos__summary{display:flex;justify-content:space-between;gap:15px;margin:10px 2px 0;color:#607386;font-size:14px}
.herb-combos__group{margin:0 0 28px;scroll-margin-top:100px}
.herb-combos__group-head{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;margin-bottom:14px;padding-bottom:12px;border-bottom:2px solid #d7e2e8}
.herb-combos__group-head h2{margin:0 0 6px;color:#102d46;font-size:clamp(23px,3vw,30px)}
.herb-combos__group-head p{margin:0;color:#607386;line-height:1.5}
.herb-combos__count{flex:0 0 auto;padding:6px 10px;border-radius:999px;background:#e8f5f1;color:#08645e;font-size:13px;font-weight:850}
.herb-combos__grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}
.herb-combos__item{border:1px solid #d5e0e7;border-top:4px solid #f77e25;border-radius:11px;background:#fff;box-shadow:0 2px 8px rgba(15,23,42,.04)}
.herb-combos__item summary{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;padding:17px 18px;cursor:pointer;list-style:none;color:#123246;font-weight:850;line-height:1.4}
.herb-combos__item summary::-webkit-details-marker{display:none}
.herb-combos__item summary::after{content:'+';flex:0 0 auto;color:#0b817d;font-size:22px;line-height:1}
.herb-combos__item[open] summary::after{content:'−'}
.herb-combos__roles{margin:0;padding:0 18px 17px 38px;color:#52677a;line-height:1.55}
.herb-combos__roles li+li{margin-top:9px}
.herb-combos__roles strong{color:#173047}
.herb-combos__empty{display:none;padding:28px;border:1px dashed #94a3b8;border-radius:11px;text-align:center;color:#607386}
.herb-combos__footer{margin-top:28px;padding:18px;border-radius:10px;background:#fff7e8;color:#75450b;line-height:1.55}
.herb-combos [hidden]{display:none!important}
.herb-combos a:focus-visible,.herb-combos summary:focus-visible,.herb-combos input:focus-visible{outline:3px solid #185cca;outline-offset:3px}
@media(max-width:760px){.herb-combos__top,.herb-combos__group-head{align-items:stretch;flex-direction:column}.herb-combos__back{justify-content:center}.herb-combos__grid{grid-template-columns:1fr}.herb-combos__tools{position:static}.herb-combos__summary{display:block}.herb-combos__summary span{display:block;margin-top:5px}}
</style>
<main class="herb-combos">
    <nav class="herb-combos__top" aria-label="Study Guide navigation">
        <span class="herb-combos__brand">Chinese Herbology · Other chapters</span>
        <span class="herb-combos__top-actions">
            <a class="herb-combos__back" href="<?php echo $libraryurl->out(false); ?>">← Library</a>
            <a class="herb-combos__back" href="<?php echo $returnurl->out(false); ?>">← Study Guide</a>
        </span>
    </nav>

    <header class="herb-combos__hero">
        <p class="herb-combos__eyebrow">Appendix 2 · Source-based study chapter</p>
        <h1>Commonly Used Herbal Combinations</h1>
        <p>Review <?php echo $total; ?> combinations by relationship, clinical application and the specific role assigned to each herb in the supplied source.</p>
    </header>

    <aside class="herb-combos__source">
        <strong>Study boundary:</strong> This Appendix 2 chapter is separate from the 51 official Duì Yào pairs. It preserves the terminology and applications in the supplied Yang excerpt (pp. 209–215); historical names and safety markers require confirmation against current course and clinical standards.
    </aside>

    <section class="herb-combos__tools" aria-label="Search combinations">
        <label class="sr-only" for="herb-combination-search">Search combination, herb or application</label>
        <input id="herb-combination-search" class="herb-combos__search" type="search" placeholder="Search combination, herb or application" autocomplete="off">
        <div class="herb-combos__summary" aria-live="polite">
            <strong id="herb-combination-result-count"><?php echo $total; ?> of <?php echo $total; ?> combinations</strong>
            <span>Open a card to review the role of each herb.</span>
        </div>
    </section>

    <div id="herb-combination-groups">
        <?php foreach ($groups as $groupindex => $group): ?>
            <section class="herb-combos__group" data-combination-group aria-labelledby="combination-group-<?php echo $groupindex; ?>">
                <header class="herb-combos__group-head">
                    <div>
                        <p class="herb-combos__eyebrow">Relationship group <?php echo $groupindex + 1; ?></p>
                        <h2 id="combination-group-<?php echo $groupindex; ?>"><?php echo s($group['name']); ?></h2>
                        <p><?php echo s($groupdescriptions[$group['name']] ?? 'Source-based herbal combinations.'); ?></p>
                    </div>
                    <span class="herb-combos__count"><?php echo count($group['items']); ?> combinations</span>
                </header>
                <div class="herb-combos__grid">
                    <?php foreach ($group['items'] as $itemindex => $item): ?>
                        <?php $searchtext = core_text::strtolower($item['title'] . ' ' . implode(' ', $item['herbs'])); ?>
                        <details class="herb-combos__item" data-combination-item data-search="<?php echo s($searchtext); ?>">
                            <summary><?php echo s($item['title']); ?></summary>
                            <ul class="herb-combos__roles">
                                <?php foreach ($item['herbs'] as $herb): ?>
                                    <?php
                                    $parts = explode(':', $herb, 2);
                                    $herbname = trim($parts[0]);
                                    $role = isset($parts[1]) ? trim($parts[1]) : '';
                                    ?>
                                    <li><strong><?php echo s($herbname); ?><?php echo $role !== '' ? ':' : ''; ?></strong><?php echo $role !== '' ? ' ' . s($role) : ''; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </details>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>
    </div>

    <p id="herb-combination-empty" class="herb-combos__empty">No combinations match that search.</p>

    <footer class="herb-combos__footer">
        <strong>Safety:</strong> This material is for Board study and source comparison. It does not replace current safety references, diagnosis, prescribing standards or supervision by a qualified clinician.
    </footer>
</main>
<script>
(function() {
    const search = document.getElementById('herb-combination-search');
    const items = Array.from(document.querySelectorAll('[data-combination-item]'));
    const groups = Array.from(document.querySelectorAll('[data-combination-group]'));
    const counter = document.getElementById('herb-combination-result-count');
    const empty = document.getElementById('herb-combination-empty');

    if (!search || !items.length) {
        return;
    }

    const normalize = function(value) {
        return value.toLocaleLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').trim();
    };

    const update = function() {
        const query = normalize(search.value);
        let visible = 0;

        items.forEach(function(item) {
            const matches = query === '' || normalize(item.dataset.search || item.textContent).includes(query);
            item.hidden = !matches;
            if (matches) {
                visible += 1;
            }
        });

        groups.forEach(function(group) {
            group.hidden = !group.querySelector('[data-combination-item]:not([hidden])');
        });

        counter.textContent = visible + ' of ' + items.length + ' combinations';
        empty.style.display = visible === 0 ? 'block' : 'none';
    };

    search.addEventListener('input', update);
})();
</script>
<?php
echo $OUTPUT->footer();
