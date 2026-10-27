#!/usr/bin/env python3
"""Generate the bilingual Herbs by Secondary Actions static study group."""

from __future__ import annotations

from html import escape
from pathlib import Path
import re
import unicodedata


ROOT = Path(__file__).resolve().parents[1]
OUT = ROOT / "study-guide" / "comparisons"


RAW = r"""
## 1|Benefit / Brighten Eyes|Beneficiar / Aclarar los Ojos
Bo He|Disperse Wind-Heat
Sang Ye|Disperse Wind-Heat
Ju Hua|Disperse Wind-Heat
Mu Zei|Disperse Wind-Heat
Man Jing Zi|Disperse Wind-Heat
Jue Ming Zi|Clear & Purge Heat
Xia Ku Cao|Clear & Purge Heat
Che Qian Zi|Drain Damp
Cang Zhu|Aromatics for Damp
Gou Qi Zi|Blood Tonic
Tu Si Zi|Yang Tonic
Nu Zhen Zi|Yin Tonic
Zhen Zhu|Anchor Shen
Bai Ji Li|Extinguish Liver Wind
Shi Jue Ming|Extinguish Liver Wind

## 2|Benefit Hair|Beneficiar el Cabello
Huai Niu Xi|Activate Blood
Da Zao|Qi Tonic
He Shou Wu|Blood Tonic|NCCAOM-only in source
Gou Qi Zi|Blood Tonic
Dang Gui|Blood Tonic
Tu Si Zi|Yang Tonic
Nu Zhen Zi|Yin Tonic
Hei Zhi Ma|Yin Tonic
Han Lian Cao|Yin Tonic|NCCAOM-only in source

## 3|Benefit Throat|Beneficiar la Garganta
Bo He|Disperse Wind-Heat
Niu Bang Zi|Disperse Wind-Heat
Chan Tui|Disperse Wind-Heat
Xuan Shen|Clear Blood-Heat
Lian Qiao|Clear Toxic-Heat
Ban Lan Gen|Clear Toxic-Heat
She Gan|Clear Toxic-Heat
Shan Dou Gen|Clear Toxic-Heat
Jie Geng|Dissolve Cold-Phlegm
Chen Pi|Regulate Qi
Gan Cao|Qi Tonic
He Zi|Astringent

## 4|Benefit Voice|Beneficiar la Voz
Ma Huang|Disperse Wind-Cold
Chan Tui|Disperse Wind-Heat
Pang Da Hai|Dissolve Hot-Phlegm
Xing Ren|Stop Cough/Wheezing
Gan Cao|Qi Tonic
Mai Men Dong|Yin Tonic
Sha Shen|Yin Tonic
He Zi|Astringent

## 5|Calm Fetus|Calmar el Feto
Zi Su Ye|Disperse Wind-Cold
Huang Qin|Clear Damp-Heat
Sang Ji Sheng|Dispel Wind-Damp
Zhu Ru|Dissolve Hot-Phlegm
Sha Ren|Aromatics for Damp
Ai Ye|Warm Interior
Bai Zhu|Qi Tonic
E Jiao|Blood Tonic
Du Zhong|Yang Tonic
Xu Duan|Yang Tonic
Tu Si Zi|Yang Tonic

## 6|Calm Shen|Calmar el Shen
Fu Ling|Drain Damp
Xi Xian Cao|Dispel Wind-Damp
Zhu Ru|Dissolve Hot-Phlegm
Dan Shen|Activate Blood
Ren Shen|Qi Tonic
Da Zao|Qi Tonic
Long Yan Rou|Blood Tonic
Bai He|Yin Tonic
Lian Zi|Astringent
Wu Wei Zi|Astringent
Fu Xiao Mai|Astringent

## 7|Clear Heart Heat via the Small Intestine|Eliminar Calor de Corazón mediante Intestino Delgado
Dan Zhu Ye|Clear & Purge Heat
Deng Xin Cao|Drain Damp
Mu Tong|Drain Damp

## 8|Clear Summer-Heat|Eliminar Calor de Verano
Xiang Ru|Disperse Wind-Cold
He Ye|Clear Toxic-Heat
Qing Hao|Clear Deficient Heat
Hua Shi|Drain Damp
Huo Xiang|Aromatics for Damp
Bai Bian Dou|Qi Tonic
Gan Cao|Qi Tonic

## 9|Clear Yin-Deficient Heat|Eliminar Calor por Deficiencia de Yin
Zhi Mu|Clear & Purge Heat
Mu Dan Pi|Clear Blood-Heat
Qin Jiao|Dispel Wind-Damp

## 10|Dissipate Nodules|Disipar Nódulos
Tian Hua Fen|Clear & Purge Heat
Xia Ku Cao|Clear & Purge Heat
Xuan Shen|Clear Blood-Heat
Lian Qiao|Clear Toxic-Heat
Pu Gong Ying|Clear Toxic-Heat
Yi Yi Ren|Drain Damp
Dong Kui Zi|Drain Damp
Bei Mu|Dissolve Hot-Phlegm
Gua Lou|Dissolve Hot-Phlegm
Hai Zao|Dissolve Hot-Phlegm
Ban Xia|Dissolve Cold-Phlegm
Bai Jie Zi|Dissolve Cold-Phlegm
Jie Geng|Dissolve Cold-Phlegm
Bie Jia|Yin Tonic
Mu Li|Anchor Shen
Yuan Zhi|Calm Shen

## 11|Expel Parasites|Expulsar Parásitos
Ku Shen|Clear Damp-Heat
Lu Hui|Purgative
Bian Xu|Drain Damp
Hai Tong Pi|Dispel Wind-Damp
Bai Bu|Stop Cough/Wheezing
Chuan Lian Zi|Regulate Qi
Xian He Cao|Stop Bleeding
Chuan Jiao|Warm Interior
She Chuang Zi|External Use

## 12|Expel Pus|Expulsar Pus
Bai Zhi|Disperse Wind-Cold
Tian Hua Fen|Clear & Purge Heat
Pu Gong Ying|Clear Toxic-Heat
Bai Jiang Cao|Clear Toxic-Heat
Yi Yi Ren|Drain Damp
Dong Gua Ren|Drain Damp
Bei Mu|Dissolve Hot-Phlegm
Gua Lou|Dissolve Hot-Phlegm
Jie Geng|Dissolve Cold-Phlegm
Zao Jiao Ci|Dissolve Cold-Phlegm
Ru Xiang|Activate Blood
Mo Yao|Activate Blood
Huang Qi|Qi Tonic
Dang Gui|Blood Tonic
Chuan Shan Jia|Obsolete|Protected animal substance; do not use

## 13|Generate Fluids|Generar Líquidos
Ge Gen|Disperse Wind-Heat
Zhi Mu|Clear & Purge Heat
Tian Hua Fen|Clear & Purge Heat
Lu Gen|Clear & Purge Heat
Sheng Di Huang|Clear Blood-Heat
Xuan Shen|Clear Blood-Heat
Ren Shen|Qi Tonic
Dang Shen|Qi Tonic
Shan Yao|Qi Tonic
Tian Men Dong|Yin Tonic
Mai Men Dong|Yin Tonic
Sha Shen|Yin Tonic
Shi Hu|Yin Tonic
Wu Wei Zi|Astringent
Wu Mei|Astringent

## 14|Guiding Herbs by Channels|Hierbas Guía por Canales
Bai Zhi|Disperse Wind-Cold|Lung / Tai Yin
Sheng Ma|Disperse Wind-Heat|Lung / Tai Yin
Jie Geng|Dissolve Cold-Phlegm|Lung / Tai Yin
Sheng Ma|Disperse Wind-Heat|Spleen / Tai Yin
Ge Gen|Disperse Wind-Heat|Spleen / Tai Yin
Cang Zhu|Aromatics for Damp|Spleen / Tai Yin
Bai Shao|Blood Tonic|Large Intestine / Yang Ming
Bai Zhi|Disperse Wind-Cold|Large Intestine / Yang Ming
Sheng Ma|Disperse Wind-Heat|Large Intestine / Yang Ming
Shi Gao|Clear & Purge Heat|Stomach / Yang Ming
Bai Zhi|Disperse Wind-Cold|Stomach / Yang Ming
Sheng Ma|Disperse Wind-Heat|Stomach / Yang Ming
Ge Gen|Disperse Wind-Heat|Stomach / Yang Ming
Xi Xin|Disperse Wind-Cold|Heart / Shao Yin
Huang Lian|Clear Damp-Heat|Heart / Shao Yin
Xi Xin|Disperse Wind-Cold|Kidney / Shao Yin
Zhi Mu|Clear & Purge Heat|Kidney / Shao Yin
Du Huo|Dispel Wind-Damp|Kidney / Shao Yin
Rou Gui|Warm Interior|Kidney / Shao Yin
Gao Ben|Disperse Wind-Cold|Small Intestine / Tai Yang
Huang Bai|Clear Damp-Heat|Small Intestine / Tai Yang
Qiang Huo|Disperse Wind-Cold|Bladder / Tai Yang
Chai Hu|Disperse Wind-Heat|Pericardium / Jue Yin
Mu Dan Pi|Clear Blood-Heat|Pericardium / Jue Yin
Chai Hu|Disperse Wind-Heat|Liver / Jue Yin
Qing Pi|Regulate Qi|Liver / Jue Yin
Chuan Xiong|Activate Blood|Liver / Jue Yin
Wu Zhu Yu|Warm Interior|Liver / Jue Yin
Chai Hu|Disperse Wind-Heat|San Jiao / Shao Yang
Lian Qiao|Clear Toxic-Heat|San Jiao / Shao Yang
Di Gu Pi|Clear Deficient Heat|San Jiao / Shao Yang
Qing Pi|Regulate Qi|San Jiao / Shao Yang
Fu Zi|Warm Interior|Gallbladder / Shao Yang
Chai Hu|Disperse Wind-Heat|Gallbladder / Shao Yang
Qing Pi|Regulate Qi|Gallbladder / Shao Yang

## 15|Guiding Herbs by Regions|Hierbas Guía por Regiones
Jie Geng|Guiding herb|Throat
Sang Zhi|Guiding herb|Upper limbs
Gui Zhi|Guiding herb|Upper limbs
Xiang Fu|Guiding herb|Chest
Chen Pi|Guiding herb|Middle Jiao
Chen Xiang|Guiding herb|Lower Jiao
Deng Xin Cao|Guiding herb|Lower Jiao
Chai Hu|Guiding herb|Flank
Xiao Hui Xiang|Guiding herb|Inguinal region
Chuan Lian Zi|Guiding herb|Inguinal region
Han Fang Ji|Guiding herb|Knee
Niu Xi|Guiding herb|Knee

## 16|Inhibit Lactation|Inhibir la Lactancia
Mai Ya|Digestive

## 17|Lower Cholesterol|Reducir el Colesterol
Ge Gen|Disperse Wind-Heat
Jue Ming Zi|Clear & Purge Heat
Ye Ju Hua|Clear Toxic-Heat|NCCAOM-only in source
Da Huang|Purgative
Ze Xie|Drain Damp
Shan Zha|Digestive
San Qi|Stop Bleeding
Jiang Huang|Activate Blood|NCCAOM-only in source
Hu Zhang|Activate Blood|NCCAOM-only in source
Huang Jing|Qi Tonic|NCCAOM-only in source
He Shou Wu|Blood Tonic|NCCAOM-only in source
Nu Zhen Zi|Yin Tonic
Yu Zhu|Yin Tonic|NCCAOM-only in source
Tian Ma|Extinguish Liver Wind

## 18|Moisten Intestines|Humedecer los Intestinos
Niu Bang Zi|Disperse Wind-Heat
Jue Ming Zi|Clear & Purge Heat
Zi Cao|Clear Blood-Heat
Dong Kui Zi|Drain Damp
Qu Mai|Drain Damp
Qin Jiao|Dispel Wind-Damp
Gua Lou Ren|Dissolve Hot-Phlegm
Xing Ren|Stop Cough/Wheezing
Su Zi|Stop Cough/Wheezing
Zhi Shi|Regulate Qi
Tao Ren|Activate Blood
Feng Mi|Qi Tonic
Dang Gui|Blood Tonic
Sang Shen|Blood Tonic
Rou Cong Rong|Yang Tonic
Suo Yang|Yang Tonic
Hu Tao Ren|Yang Tonic
Tian Men Dong|Yin Tonic
Mai Men Dong|Yin Tonic
Hei Zhi Ma|Yin Tonic
Bai Zi Ren|Calm Shen

## 19|Move & Cool Blood|Mover y Enfriar la Sangre
Mu Dan Pi|Clear Blood-Heat
Zi Cao|Clear Blood-Heat
Chi Shao|Activate Blood
Dan Shen|Activate Blood
Yu Jin|Activate Blood

## 20|Move & Nourish Blood|Mover y Nutrir la Sangre
Dan Shen|Activate Blood
Hong Hua|Activate Blood|Source dose note: 0.9–1.5 g
Ji Xue Teng|Activate Blood|NCCAOM-only in source
Dang Gui|Blood Tonic

## 21|Move Blood|Mover la Sangre
Mu Dan Pi|Clear Blood-Heat
Da Huang|Purgative
Qu Mai|Drain Damp
Pu Huang|Stop Bleeding
San Qi|Stop Bleeding
Qian Cao Gen|Stop Bleeding
Dang Gui|Blood Tonic
Xu Duan|Yang Tonic
Bie Jia|Yin Tonic

## 22|Move Qi|Mover el Qi
Hou Po|Aromatics for Damp
Bai Dou Kou|Aromatics for Damp
Sha Ren|Aromatics for Damp
Cao Dou Kou|Aromatics for Damp
Lai Fu Zi|Digestive
Chuan Xiong|Activate Blood
Yan Hu Suo|Activate Blood
Yu Jin|Activate Blood
Ru Xiang|Activate Blood
San Leng|Activate Blood
E Zhu|Activate Blood

## 23|Open Heart Orifice|Abrir el Orificio del Corazón
Yu Jin|Activate Blood
Yuan Zhi|Calm Shen
Shi Chang Pu|Aromatics for Orifices|Restricted or regulated in some jurisdictions

## 24|Open Nasal Orifice|Abrir el Orificio Nasal
Bai Zhi|Disperse Wind-Cold
Xi Xin|Disperse Wind-Cold
Xin Yi Hua|Disperse Wind-Cold
Cang Er Zi|Disperse Wind-Cold
Bo He|Disperse Wind-Heat

## 25|Prevent Stagnation from Tonics|Prevenir Estancamiento por Tónicos
Sha Ren|Aromatics for Damp
Chen Pi|Regulate Qi
Mu Xiang|Regulate Qi

## 26|Promote Healing & Generate Flesh|Promover Cicatrización y Generar Tejido
Bai Ji|Stop Bleeding
Di Yu|Stop Bleeding
Mo Yao|Activate Blood
Ru Xiang|Activate Blood
Huang Qi|Qi Tonic
Dang Gui|Blood Tonic
Gui Ban|Yin Tonic
Chi Shi Zhi|Astringent
Long Gu|Anchor Shen
Zhen Zhu|Anchor Shen

## 27|Promote Lactation|Promover la Lactancia
Pu Gong Ying|Clear Toxic-Heat
Dong Kui Zi|Drain Damp
Mu Tong|Drain Damp
Wang Bu Liu Xing|Activate Blood
Hei Zhi Ma|Yin Tonic
Chuan Shan Jia|Obsolete|Protected animal substance; do not use

## 28|Promote Urination|Promover la Micción
Ma Huang|Disperse Wind-Cold
Sheng Jiang Pi|Disperse Wind-Cold
Xiang Ru|Disperse Wind-Cold
Zhi Zi|Clear & Purge Heat
Dan Zhu Ye|Clear & Purge Heat
Lu Gen|Clear & Purge Heat
Ku Shen|Clear Damp-Heat
Bai Wei|Clear Deficient Heat
Hai Tong Pi|Dispel Wind-Damp
Wu Jia Pi|Dispel Wind-Damp
Sang Bai Pi|Stop Cough/Wheezing
Da Fu Pi|Regulate Qi
Pu Huang|Stop Bleeding
Bai Mao Gen|Stop Bleeding
Yi Mu Cao|Activate Blood
Ze Lan|Activate Blood
Niu Xi|Activate Blood
Huang Qi|Qi Tonic
Di Long|Extinguish Liver Wind
Bing Lang|Expel Parasites

## 29|Raise Yang Qi|Elevar el Yang Qi
Chai Hu|Disperse Wind-Heat|Organ prolapse
Sheng Ma|Disperse Wind-Heat|Organ prolapse
Huang Qi|Qi Tonic|Organ prolapse
Ge Gen|Disperse Wind-Heat|Diarrhea

## 30|Reduce Food Retention|Reducir la Retención de Alimentos
Da Huang|Purgative
Ban Xia|Dissolve Cold-Phlegm
Cang Zhu|Aromatics for Damp
San Leng|Activate Blood
E Zhu|Activate Blood
Wu Zhu Yu|Warm Interior
Rou Dou Kou|Astringent
Bing Lang|Expel Parasites

## 31|Reduce Poisoning|Reducir la Intoxicación
Sheng Jiang|Disperse Wind-Cold|Historical antidote chart
Zi Su Ye|Disperse Wind-Cold|Historical antidote chart
Fang Feng|Disperse Wind-Cold|Historical antidote chart
Zi Cao|Clear Blood-Heat|Historical antidote chart
Tu Fu Ling|Clear Toxic-Heat|Historical antidote chart
Lu Dou|Clear Toxic-Heat|Historical antidote chart
Gan Cao|Qi Tonic|Historical antidote chart
Gu Sui Bu|Yang Tonic|Historical antidote chart

## 32|Regulate Menstruation|Regular la Menstruación
Xiang Fu|Regulate Qi
Yi Mu Cao|Activate Blood
Ze Lan|Activate Blood
Bai Shao|Blood Tonic
Dang Gui|Blood Tonic

## 33|Release Exterior Wind|Liberar Viento Exterior
Jin Yin Hua|Clear Toxic-Heat
Lian Qiao|Clear Toxic-Heat
Qing Hao|Clear Deficient Heat
Du Huo|Dispel Wind-Damp
Qian Hu|Dissolve Hot-Phlegm
Huo Xiang|Aromatics for Damp
Cang Zhu|Aromatics for Damp
Gou Teng|Extinguish Liver Wind
Bai Ji Li|Extinguish Liver Wind

## 34|Relieve Jaundice|Aliviar la Ictericia
Zhi Zi|Clear & Purge Heat
Huang Qin|Clear Damp-Heat
Huang Lian|Clear Damp-Heat
Huang Bai|Clear Damp-Heat
Ban Lan Gen|Clear Toxic-Heat
Pu Gong Ying|Clear Toxic-Heat
Shan Dou Gen|Clear Toxic-Heat
Tu Fu Ling|Clear Toxic-Heat
Da Huang|Purgative
Yin Chen|Drain Damp
Qin Jiao|Dispel Wind-Damp
Yu Jin|Activate Blood

## 35|Soothe Liver Qi|Suavizar el Qi de Hígado
Bo He|Disperse Wind-Heat
Chai Hu|Disperse Wind-Heat
Mai Ya|Digestive
Qing Pi|Regulate Qi
Xiang Fu|Regulate Qi
Chuan Lian Zi|Regulate Qi
Yu Jin|Activate Blood
Wu Zhu Yu|Warm Interior
Xiao Hui Xiang|Warm Interior
Bai Ji Li|Extinguish Liver Wind

## 36|Stop Bleeding|Detener el Sangrado
Jing Jie|Disperse Wind-Cold
Sang Ye|Disperse Wind-Heat
Mu Zei|Disperse Wind-Heat
Zhi Zi|Clear & Purge Heat
Huang Qin|Clear Damp-Heat
Huang Lian|Clear Damp-Heat
He Ye|Clear Toxic-Heat
Qing Hao|Clear Deficient Heat
Shi Wei|Drain Damp
Zhu Ru|Dissolve Hot-Phlegm
Niu Xi|Activate Blood
E Jiao|Blood Tonic
Xu Duan|Yang Tonic
Gui Ban|Yin Tonic
Shan Zhu Yu|Astringent
Hai Piao Xiao|Astringent
Wu Mei|Astringent
Chi Shi Zhi|Astringent
Dai Zhe Shi|Extinguish Liver Wind

## 37|Stop Cough/Wheezing|Detener Tos y Sibilancias
Sheng Jiang|Disperse Wind-Cold
Sang Ye|Disperse Wind-Heat
Di Gu Pi|Clear Deficient Heat
Shi Wei|Drain Damp
Che Qian Zi|Drain Damp
Hou Po|Aromatics for Damp
Ce Bai Ye|Stop Bleeding
E Jiao|Blood Tonic
Dang Gui|Blood Tonic
Hu Tao Ren|Yang Tonic
Bai He|Yin Tonic

## 38|Stop Diarrhea|Detener la Diarrea
Ge Gen|Disperse Wind-Heat
He Ye|Clear Toxic-Heat
Fu Ling|Drain Damp
Yi Yi Ren|Drain Damp
Che Qian Zi|Drain Damp
Cao Dou Kou|Aromatics for Damp
Cao Guo|Aromatics for Damp
Sha Ren|Aromatics for Damp
Wu Zhu Yu|Warm Interior
Bai Zhu|Qi Tonic
Bai Bian Dou|Qi Tonic
Shan Yao|Qi Tonic
Bu Gu Zhi|Yang Tonic
Yi Zhi Ren|Yang Tonic
Wu Wei Zi|Astringent
Lian Zi|Astringent
Qian Shi|Astringent
Chi Shi Zhi|Astringent
Chun Pi|Astringent
He Zi|Astringent
Jin Ying Zi|Astringent
Rou Dou Kou|Astringent
Wu Mei|Astringent
Bing Lang|Expel Parasites

## 39|Stop Dysentery|Detener la Disentería
Jin Yin Hua|Clear Toxic-Heat
Bai Tou Weng|Clear Toxic-Heat|NCCAOM-only in source
Da Huang|Purgative
Huang Qin|Drain Damp
Huang Lian|Drain Damp
Huang Bai|Drain Damp
Ku Shen|Drain Damp
Cang Zhu|Aromatics for Damp
Hou Po|Aromatics for Damp
Xian He Cao|Stop Bleeding

## 40|Stop Headache|Detener la Cefalea
Chai Hu|Channel guide|Shao Yang
Qiang Huo|Channel guide|Tai Yang
Bai Zhi|Channel guide|Yang Ming
Ge Gen|Channel guide|Yang Ming
Gao Ben|Channel guide|Jue Yin
Wu Zhu Yu|Channel guide|Jue Yin
Xi Xin|Channel guide|Shao Yin
Du Huo|Channel guide|Shao Yin
Ban Xia|Channel guide|Tai Yin
Cang Zhu|Channel guide|Tai Yin
Chuan Xiong|Channel guide|Various types

## 41|Stop Itching|Detener el Prurito
Jing Jie|Disperse Wind-Cold
Zi Cao|Clear Blood-Heat
Ku Shen|Clear Damp-Heat
Bai Xian Pi|Clear Toxic-Heat|NCCAOM-only in source
Di Fu Zi|Drain Damp
Hai Tong Pi|Dispel Wind-Damp
Xian He Cao|Stop Bleeding
Bai Ji Li|Extinguish Liver Wind
She Chuang Zi|External Use

## 42|Stop Nausea, Vomiting & Hiccup|Detener Náusea, Vómito e Hipo
Zi Su Ye|Disperse Wind-Cold
Sheng Jiang|Disperse Wind-Cold
Lu Gen|Clear & Purge Heat
Zhu Ru|Dissolve Hot-Phlegm
Ban Xia|Dissolve Cold-Phlegm
Xuan Fu Hua|Dissolve Cold-Phlegm
Pi Pa Ye|Stop Cough/Wheezing
Huo Xiang|Aromatics for Damp
Bai Dou Kou|Aromatics for Damp
Cao Dou Kou|Aromatics for Damp
Sha Ren|Aromatics for Damp
Chen Pi|Regulate Qi
Chen Xiang|Regulate Qi
Wu Zhu Yu|Warm Interior
Xiao Hui Xiang|Warm Interior
Ding Xiang|Warm Interior
Dai Zhe Shi|Extinguish Liver Wind

## 43|Stop Pain|Detener el Dolor
Fang Feng|Disperse Wind-Cold
Gao Ben|Disperse Wind-Cold
Bai Zhi|Disperse Wind-Cold
Xi Xin|Disperse Wind-Cold
Ge Gen|Disperse Wind-Heat
Wei Ling Xian|Dispel Wind-Damp
Tian Nan Xing|Dissolve Cold-Phlegm
San Qi|Stop Bleeding
Di Yu|Stop Bleeding
Yan Hu Suo|Activate Blood
Ru Xiang|Activate Blood
Mo Yao|Activate Blood
Chuan Xiong|Activate Blood
Chuan Wu|Warm Interior
Cao Wu|Warm Interior
Chuan Jiao|Warm Interior
Gao Liang Jiang|Warm Interior
Yi Tang|Qi Tonic
Bai Shao|Blood Tonic

## 44|Stop Sweating|Detener la Sudoración
Shi Gao|Clear & Purge Heat
Zhi Mu|Clear & Purge Heat
Di Gu Pi|Clear Deficient Heat
Fu Zi|Warm Interior
Huang Qi|Qi Tonic
Bai Zhu|Qi Tonic
Shan Zhu Yu|Astringent
Wu Wei Zi|Astringent
Fu Xiao Mai|Astringent
Wu Mei|Astringent
Ma Huang Gen|Astringent|NCCAOM-only in source
Long Gu (Duan)|Anchor Shen
Mu Li (Duan)|Anchor Shen
Suan Zao Ren|Calm Shen

## 45|Stop Toothache|Detener el Dolor Dental
Bai Zhi|Disperse Wind-Cold
Gao Ben|Disperse Wind-Cold
Xi Xin|Disperse Wind-Cold
Sheng Ma|Disperse Wind-Heat
Shi Gao|Clear & Purge Heat
Huang Lian|Clear Damp-Heat
Da Huang|Purgative
Bi Ba|Warm Interior
Hu Jiao|Warm Interior
Niu Xi|Activate Blood
Gu Sui Bu|Yang Tonic

## 46|Strengthen Sinews & Bones|Fortalecer Tendones y Huesos
Wu Jia Pi|Dispel Wind-Damp
Sang Ji Sheng|Dispel Wind-Damp
Huai Niu Xi|Activate Blood
Du Zhong|Yang Tonic
Xu Duan|Yang Tonic
Gu Sui Bu|Yang Tonic
Gou Ji|Yang Tonic

## 47|Transform Stones|Transformar Cálculos
Yu Jin|Activate Blood|Gallbladder
Ji Nei Jin|Digestive|Gallbladder and Kidney
Jin Qian Cao|Drain Damp|Gallbladder and Kidney; NCCAOM-only in source
Hai Jin Sha|Drain Damp|Kidney; NCCAOM-only in source
Shi Wei|Drain Damp|Kidney; NCCAOM-only in source

## 48|Treat Burns|Tratar Quemaduras
Bai Zhi|Disperse Wind-Cold
Zi Cao|Clear Blood-Heat
Huang Lian|Clear Damp-Heat
Huang Bai|Clear Damp-Heat
Di Yu|Stop Bleeding
Ce Bai Ye|Stop Bleeding
Bai Ji|Stop Bleeding

## 49|Treat Cancer|Tratar Cáncer
Bai Hua She She Cao|Clear Toxic-Heat|Source sites: lung, stomach
Ban Zhi Lian|Clear Toxic-Heat|Source sites: lung, liver
Shan Dou Gen|Clear Toxic-Heat|Source site: throat
Shan Ci Gu|Clear Toxic-Heat|Source site: breast
Tian Kui Zi|Clear Toxic-Heat|Source sites: bladder, throat, lymphatic
Long Kui|Clear Toxic-Heat|Source sites: stomach, cervical
E Zhu|Activate Blood|Source site: cervical
Huang Jing|Qi Tonic|Source site: rectal
Lu Feng Fang|External Use|Source site: breast
Ban Mao|External Use|Source site: liver; toxic substance
Er Cha|External Use|Source site: rectal
Ma Qian Zi|Obsolete|Source site: liver; toxic/restricted
Peng Sha|Obsolete|Source sites: stomach, rectal

## 50|Treat Diabetes (Xiao Ke)|Tratar Diabetes (Xiao Ke)
Ge Gen|Disperse Wind-Heat
Zhi Mu|Clear & Purge Heat
Tian Hua Fen|Clear & Purge Heat
Sheng Di Huang|Clear Blood-Heat
Ren Shen (Hong Shen)|Qi Tonic
Huang Qi|Qi Tonic
Shan Yao|Qi Tonic
Shu Di Huang|Blood Tonic
Mai Men Dong|Yin Tonic
Tian Men Dong|Yin Tonic
Shi Hu|Yin Tonic
Wu Wei Zi|Astringent
Wu Mei|Astringent
Shan Zhu Yu|Astringent

## 51|Treat Hepatitis|Tratar Hepatitis
Chai Hu|Disperse Wind-Heat
Xia Ku Cao|Clear & Purge Heat
Ban Lan Gen|Clear Toxic-Heat
Shan Dou Gen|Clear Toxic-Heat
Lu Hui|Purgative
Yi Yi Ren|Drain Damp
Yin Chen|Drain Damp
Mu Gua|Dispel Wind-Damp
Qin Jiao|Dispel Wind-Damp
Huo Xiang|Aromatics for Damp
Shan Zha|Digestive
Da Ji|Stop Bleeding|NCCAOM-only in source
Xiao Ji|Stop Bleeding|NCCAOM-only in source
Dan Shen|Activate Blood
Chi Shao|Activate Blood
Shan Yao|Qi Tonic
Bai Shao|Blood Tonic
Dang Gui|Blood Tonic
Gou Qi Zi|Blood Tonic
Nu Zhen Zi|Yin Tonic
Wu Wei Zi|Astringent
Wu Mei|Astringent

## 52|Treat Hernia|Tratar Hernia
Chuan Lian Zi|Regulate Qi
Qing Pi|Regulate Qi
Wu Yao|Regulate Qi
Wu Zhu Yu|Warm Interior
Xiao Hui Xiang|Warm Interior

## 53|Treat Hypertension|Tratar Hipertensión
Ge Gen|Disperse Wind-Heat
Zhi Zi|Clear & Purge Heat
Xia Ku Cao|Clear & Purge Heat
Jue Ming Zi|Clear & Purge Heat
Huang Qin|Clear Damp-Heat
Long Dan Cao|Clear Damp-Heat
Sang Ji Sheng|Dispel Wind-Damp
Sang Bai Pi|Stop Cough/Wheezing
Shan Zha|Digestive
Huai Hua Mi|Stop Bleeding
Yi Mu Cao|Activate Blood
Chong Wei Zi|Activate Blood
Du Zhong|Yang Tonic
Gou Teng|Extinguish Liver Wind
Tian Ma|Extinguish Liver Wind
Bai Ji Li|Extinguish Liver Wind
Shi Jue Ming|Extinguish Liver Wind
Di Long|Extinguish Liver Wind
Ma Dou Ling|Obsolete|Aristolochic-acid risk; do not use

## 54|Treat Leukorrhea|Tratar Leucorrea
Bai Zhi|Disperse Wind-Cold
Huang Lian|Clear Damp-Heat
Huang Bai|Clear Damp-Heat
Long Dan Cao|Clear Damp-Heat
Ku Shen|Clear Damp-Heat
Ze Xie|Drain Damp
Bai Xian Pi|Drain Damp
Di Fu Zi|Drain Damp
Bai Bu|Stop Cough/Wheezing
Cang Zhu|Aromatics for Damp
Xian He Cao|Stop Bleeding
Hua Jiao|Warm Interior
Ai Ye|Stop Bleeding
Bai Zhu|Qi Tonic
Tu Si Zi|Yang Tonic
Gou Ji|Yang Tonic
Hai Piao Xiao|Astringent
Jin Ying Zi|Astringent
Lian Zi|Astringent
Qian Shi|Astringent
Sang Piao Xiao|Astringent|NCCAOM-only in source
Long Gu|Anchor Shen
Mu Li|Anchor Shen
She Chuang Zi|External Use

## 55|Treat Lumbago|Tratar Lumbago
Sang Ji Sheng|Dispel Wind-Damp
Du Zhong|Yang Tonic
Xu Duan|Yang Tonic
Gou Ji|Yang Tonic
Bu Gu Zhi|Yang Tonic
Gu Sui Bu|Yang Tonic
Hu Tao Ren|Yang Tonic
Tu Si Zi|Yang Tonic

## 56|Treat Malaria|Tratar Malaria
Zhi Mu|Clear & Purge Heat
Qing Hao|Clear Deficient Heat
Cao Guo|Aromatics for Damp
Bing Lang|Expel Parasites
Chang Shan|Expel Parasites|NCCAOM-only in source

## 57|Treat Spermatorrhea|Tratar Espermatorrea
Shan Yao|Qi Tonic
Lu Rong|Yang Tonic
Xian Mao|Yang Tonic
Yin Yang Huo|Yang Tonic
Yi Zhi Ren|Yang Tonic
Ba Ji Tian|Yang Tonic
Bu Gu Zhi|Yang Tonic
Xu Duan|Yang Tonic
Gou Ji|Yang Tonic
Rou Cong Rong|Yang Tonic
Tu Si Zi|Yang Tonic
Shan Zhu Yu|Astringent
Hai Piao Xiao|Astringent
Sang Piao Xiao|Astringent|NCCAOM-only in source
Qian Shi|Astringent
Lian Zi|Astringent
Bai Guo|Astringent
Wu Wei Zi|Astringent
Long Gu (Duan)|Anchor Shen
Mu Li (Duan)|Anchor Shen
Yuan Zhi|Calm Shen

## 58|Treat Trauma|Tratar Trauma
Mu Dan Pi|Clear Blood-Heat
San Qi|Stop Bleeding
Hong Hua|Activate Blood
Tao Ren|Activate Blood
Ru Xiang|Activate Blood
Mo Yao|Activate Blood
Chi Shao|Activate Blood
Niu Xi|Activate Blood
Ze Lan|Activate Blood
Dang Gui (Wei)|Blood Tonic
Xu Duan|Yang Tonic

## 59|Treat Trichomoniasis|Tratar Tricomoniasis
Ku Shen|Clear Damp-Heat
Bai Bu|Stop Cough/Wheezing
Xian He Cao|Stop Bleeding
Bu Gu Zhi|Yang Tonic
Yuan Zhi|Calm Shen
She Chuang Zi|External Use

## 60|Vent Rashes|Exteriorizar Erupciones
Jing Jie|Disperse Wind-Cold
Bo He|Disperse Wind-Heat
Niu Bang Zi|Disperse Wind-Heat
Chan Tui|Disperse Wind-Heat
Ge Gen|Disperse Wind-Heat
Sheng Ma|Disperse Wind-Heat
Zi Cao|Clear Blood-Heat
Lu Gen|Clear & Purge Heat
"""


CATEGORY_ES = {
    "Disperse Wind-Cold": "Dispersa Viento-Frío",
    "Disperse Wind-Heat": "Dispersa Viento-Calor",
    "Clear & Purge Heat": "Aclara y Purga Calor",
    "Clear Blood-Heat": "Enfría la Sangre",
    "Clear Damp-Heat": "Aclara Calor-Humedad",
    "Clear Toxic-Heat": "Aclara Calor-Tóxico",
    "Clear Deficient Heat": "Aclara Calor por Deficiencia",
    "Drain Damp": "Drena Humedad",
    "Dispel Wind-Damp": "Disipa Viento-Humedad",
    "Dissolve Hot-Phlegm": "Transforma Flema-Calor",
    "Dissolve Cold-Phlegm": "Transforma Flema-Frío",
    "Stop Cough/Wheezing": "Detiene Tos/Sibilancias",
    "Aromatics for Damp": "Aromática que Transforma Humedad",
    "Digestive": "Digestiva",
    "Regulate Qi": "Regula Qi",
    "Warm Interior": "Calienta el Interior",
    "Stop Bleeding": "Detiene Sangrado",
    "Activate Blood": "Activa Sangre",
    "Qi Tonic": "Tónico de Qi",
    "Blood Tonic": "Tónico de Sangre",
    "Yang Tonic": "Tónico de Yang",
    "Yin Tonic": "Tónico de Yin",
    "Astringent": "Astringente",
    "Anchor Shen": "Ancla el Shen",
    "Calm Shen": "Calma el Shen",
    "Extinguish Liver Wind": "Extingue Viento de Hígado",
    "Expel Parasites": "Expulsa Parásitos",
    "External Use": "Uso Externo",
    "Aromatics for Orifices": "Aromática que Abre Orificios",
    "Purgative": "Purgante",
    "Obsolete": "Obsoleta",
    "Guiding herb": "Hierba guía",
    "Channel guide": "Guía de canal",
}


SPECIAL_WARNING = {
    5: "Pregnancy is not a self-treatment setting; use requires qualified obstetric review.",
    16: "This is a historical study association, not advice to alter lactation without clinical assessment.",
    17: "This list does not establish lipid-lowering efficacy or replace cardiovascular care.",
    31: "This historical antidote chart must never delay emergency care or Poison Control. These herbs are not substitutes for validated antidotes.",
    36: "Unexplained, heavy or persistent bleeding requires prompt medical evaluation.",
    37: "Breathing difficulty, cyanosis, chest pain or severe wheezing requires urgent evaluation.",
    39: "Bloody diarrhea, fever or dehydration requires medical evaluation.",
    42: "Persistent vomiting, blood, severe pain or dehydration requires medical evaluation.",
    43: "Severe, sudden or unexplained pain requires diagnosis before symptomatic treatment.",
    45: "Dental pain may signal infection and requires dental assessment when persistent or accompanied by swelling or fever.",
    48: "Significant burns require first aid and medical evaluation; do not apply unverified substances to broken skin.",
    49: "Historical exam associations do not establish anticancer efficacy. Cancer requires oncology diagnosis and treatment.",
    50: "Historical Xiao Ke associations do not replace glucose monitoring, insulin or prescribed diabetes care.",
    51: "Hepatitis requires diagnosis, liver testing and medical care; herbs can also cause liver injury.",
    52: "A painful, irreducible hernia, vomiting or abdominal distension requires urgent evaluation.",
    53: "These historical associations do not replace antihypertensive care. Severe blood pressure elevation may require urgent assessment.",
    54: "Discharge may reflect infection or another condition requiring diagnosis; this is not an empiric treatment list.",
    56: "Suspected malaria requires urgent testing and effective antimalarial treatment.",
    58: "Major trauma, head injury, deformity, uncontrolled bleeding or loss of function requires emergency care.",
    59: "Trichomoniasis requires diagnostic testing, antimicrobial treatment and partner management.",
}


SPECIAL_WARNING_ES = {
    5: "El embarazo no es contexto de automedicación; cualquier uso requiere revisión obstétrica cualificada.",
    16: "Es una asociación histórica de estudio, no una indicación para alterar la lactancia sin evaluación clínica.",
    17: "La lista no demuestra eficacia hipolipemiante ni sustituye atención cardiovascular.",
    31: "Esta tabla histórica de antídotos nunca debe retrasar urgencias o toxicología. Estas hierbas no sustituyen antídotos validados.",
    36: "El sangrado inexplicable, abundante o persistente requiere evaluación médica pronta.",
    37: "Dificultad respiratoria, cianosis, dolor torácico o sibilancias graves requieren evaluación urgente.",
    39: "La diarrea sanguinolenta, fiebre o deshidratación requiere evaluación médica.",
    42: "Vómito persistente, sangre, dolor intenso o deshidratación requiere evaluación médica.",
    43: "El dolor intenso, súbito o inexplicable requiere diagnóstico antes del tratamiento sintomático.",
    45: "El dolor dental puede indicar infección y requiere evaluación odontológica si persiste o hay hinchazón o fiebre.",
    48: "Las quemaduras importantes requieren primeros auxilios y evaluación médica; no aplicar sustancias no verificadas sobre piel abierta.",
    49: "Las asociaciones históricas de examen no demuestran eficacia anticancerosa. El cáncer requiere diagnóstico y tratamiento oncológico.",
    50: "Las asociaciones históricas con Xiao Ke no sustituyen glucemia, insulina ni tratamiento indicado.",
    51: "La hepatitis requiere diagnóstico, pruebas hepáticas y atención médica; las hierbas también pueden lesionar el hígado.",
    52: "Una hernia dolorosa, irreducible, con vómito o distensión abdominal requiere evaluación urgente.",
    53: "Estas asociaciones históricas no sustituyen el tratamiento antihipertensivo. La elevación grave puede requerir atención urgente.",
    54: "El flujo puede indicar infección u otra afección que necesita diagnóstico; no es una lista de tratamiento empírico.",
    56: "La sospecha de malaria requiere pruebas urgentes y tratamiento antipalúdico eficaz.",
    58: "Trauma mayor, lesión craneal, deformidad, sangrado incontrolado o pérdida funcional requiere urgencias.",
    59: "La tricomoniasis requiere diagnóstico, antimicrobianos y manejo de parejas.",
}


def parse():
    chapters = []
    current = None
    for raw_line in RAW.strip().splitlines():
        line = raw_line.strip()
        if not line:
            continue
        if line.startswith("## "):
            number, en, es = line[3:].split("|", 2)
            current = {"number": int(number), "en": en, "es": es, "rows": []}
            chapters.append(current)
            continue
        parts = line.split("|", 2)
        current["rows"].append(tuple(parts + [""] * (3 - len(parts))))
    assert [c["number"] for c in chapters] == list(range(1, 61))
    return chapters


def slug(text):
    plain = unicodedata.normalize("NFKD", text).encode("ascii", "ignore").decode()
    return re.sub(r"[^a-z0-9]+", "-", plain.lower()).strip("-")


def translated_detail(detail):
    replacements = {
        "NCCAOM-only in source": "Solo NCCAOM según la fuente",
        "Historical antidote chart": "Tabla histórica de antídotos",
        "Protected animal substance; do not use": "Sustancia animal protegida; no usar",
        "Restricted or regulated in some jurisdictions": "Restringida o regulada en algunas jurisdicciones",
        "Organ prolapse": "Prolapso de órganos",
        "Diarrhea": "Diarrea",
        "Various types": "Varios tipos",
        "Gallbladder and Kidney": "Vesícula Biliar y Riñón",
        "Gallbladder": "Vesícula Biliar",
        "Kidney": "Riñón",
        "toxic substance": "sustancia tóxica",
        "toxic/restricted": "tóxica/restringida",
        "Aristolochic-acid risk; do not use": "Riesgo por ácido aristolóquico; no usar",
        "Source sites": "Localizaciones de la fuente",
        "Source site": "Localización de la fuente",
        "lung": "pulmón", "stomach": "estómago", "liver": "hígado",
        "bladder": "vejiga", "throat": "garganta", "breast": "mama",
        "rectal": "recto", "lymphatic": "linfático", "cervical": "cuello uterino",
    }
    result = detail
    for source, target in replacements.items():
        result = result.replace(source, target)
    return result


def nav_html(language, current=False):
    source_name = "herbal-incompatibilities-es.html" if language == "es" else "herbal-incompatibilities.html"
    source = (OUT / source_name).read_text(encoding="utf-8")
    nav = re.search(r'<nav class="comparison-collection-links".*?</nav>', source, re.S).group(0)
    if "secondary-actions" not in nav:
        label = "Acciones Secundarias" if language == "es" else "Secondary Actions"
        suffix = "-es" if language == "es" else ""
        nav = nav.replace("</nav>", f'<a href="secondary-actions{suffix}.html">{label}</a></nav>')
    if current:
        nav = nav.replace(' aria-current="page"', "")
        suffix = "-es" if language == "es" else ""
        nav = nav.replace(f'<a href="secondary-actions{suffix}.html">', f'<a aria-current="page" href="secondary-actions{suffix}.html">')
    return nav


def index_page(chapters, language):
    es = language == "es"
    suffix = "-es" if es else ""
    cards = []
    for chapter in chapters:
        n = chapter["number"]
        title = chapter["es" if es else "en"]
        filename = f'secondary-actions-{n:02d}-{slug(chapter["en"])}{suffix}.html'
        terms = " ".join([chapter["en"], chapter["es"]] + [row[0] + " " + row[1] for row in chapter["rows"]]).lower()
        sensitive = n in {31, 48, 49, 50, 51, 52, 53, 54, 56, 58, 59}
        if sensitive:
            description = (f'Etiqueta histórica de examen: revisa {len(chapter["rows"])} medicinales junto con su límite clínico moderno.' if es else f'Historical exam label: review {len(chapter["rows"])} medicinals together with the modern clinical boundary.')
        else:
            description = (f'Conecta esta acción secundaria con {len(chapter["rows"])} medicinales sin perder de vista su categoría primaria.' if es else f'Connect this secondary action with {len(chapter["rows"])} medicinals while keeping each primary category in view.')
        rule = ("Una acción secundaria es una pista de examen, no una indicación clínica independiente." if es else "A secondary action is an exam clue, not a stand-alone clinical indication.")
        type_label = f'Acción {n}' if es else f'Action {n}'
        read = "Leer y continuar →" if es else "Read and continue →"
        cards.append(f'<article class="comparison-card" data-type="secondary-actions" data-sequence="{n}" data-search="{escape(terms, quote=True)}"><span class="comparison-card__type">{type_label}</span><h3>{escape(title)}</h3><p>{description}</p><div class="comparison-card__category"><strong>{"Regla" if es else "Rule"}:</strong><br>{rule}</div><a class="comparison-card__open" data-chapter-url="{filename}" data-chapter-number="{n}" href="continuous-reader.html?group=secondary-actions&amp;lang={language}&amp;chapter={n}">{read}</a></article>')
    if es:
        head = '<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="description" content="Sesenta categorías de hierbas organizadas por acciones secundarias para estudio de examen."><title>Hierbas por Acciones Secundarias | AcupunctureTests</title><link rel="alternate" hreflang="en" href="secondary-actions.html"><link rel="stylesheet" href="comparisons.css?v=20261026-2"><script defer src="comparisons.js?v=20261026-3"></script></head><body>'
        top = '<header class="comparison-topbar"><div class="comparison-shell comparison-topbar__inner"><a class="comparison-brand" href="/my/"><span class="comparison-brand__mark" aria-hidden="true">☯</span><span>AcupunctureTests</span></a><span style="display:flex;gap:8px;flex-wrap:wrap"><a class="comparison-back" href="secondary-actions.html">English</a><a class="comparison-back" href="/my/">← Dashboard</a></span></div></header>'
        hero = '<main class="comparison-shell"><section class="comparison-hero"><p class="comparison-eyebrow">Grupo 18 · Clasificación funcional</p><h1>Hierbas por Acciones Secundarias</h1><p>Estudia 60 funciones secundarias y relaciónalas con la categoría primaria de cada medicinal.</p></section>'
        tools = '<section class="comparison-tools comparison-tools--search-only"><label><span class="sr-only">Buscar acción o hierba</span><input class="comparison-search" type="search" placeholder="Buscar acción, hierba o categoría" data-comparison-search></label></section><section><header class="comparison-summary"><h2>Acciones disponibles</h2><p><span data-visible-count>60</span> capítulos</p></header><div class="comparison-grid" data-comparison-grid>'
        end = '</div><p class="comparison-empty" data-comparison-empty hidden>Ninguna acción coincide.</p></section></main><footer class="comparison-footer"><div class="comparison-shell">60 acciones · Categorías primarias · Lectura continua</div></footer></body></html>'
    else:
        head = '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="description" content="Sixty Chinese herb categories organized by secondary actions for exam study."><title>Herbs by Secondary Actions | AcupunctureTests</title><link rel="alternate" hreflang="es" href="secondary-actions-es.html"><link rel="stylesheet" href="comparisons.css?v=20261026-2"><script defer src="comparisons.js?v=20261026-3"></script></head><body>'
        top = '<header class="comparison-topbar"><div class="comparison-shell comparison-topbar__inner"><a class="comparison-brand" href="/my/"><span class="comparison-brand__mark" aria-hidden="true">☯</span><span>AcupunctureTests</span></a><span style="display:flex;gap:8px;flex-wrap:wrap"><a class="comparison-back" href="secondary-actions-es.html">Español</a><a class="comparison-back" href="/my/">← Back to Dashboard</a></span></div></header>'
        hero = '<main class="comparison-shell"><section class="comparison-hero"><p class="comparison-eyebrow">Group 18 · Functional classification</p><h1>Herbs by Secondary Actions</h1><p>Study 60 secondary functions and connect each medicinal to its primary category.</p></section>'
        tools = '<section class="comparison-tools comparison-tools--search-only"><label><span class="sr-only">Search action or herb</span><input class="comparison-search" type="search" placeholder="Search action, herb or category" data-comparison-search></label></section><section><header class="comparison-summary"><h2>Available actions</h2><p><span data-visible-count>60</span> chapters</p></header><div class="comparison-grid" data-comparison-grid>'
        end = '</div><p class="comparison-empty" data-comparison-empty hidden>No secondary actions match this search.</p></section></main><footer class="comparison-footer"><div class="comparison-shell">60 actions · Primary categories · Continuous reading</div></footer></body></html>'
    return "\n".join([head, top, hero, nav_html(language, True), tools, *cards, end]) + "\n"


def detail_page(chapters, position, language):
    chapter = chapters[position]
    es = language == "es"
    suffix = "-es" if es else ""
    n = chapter["number"]
    title = chapter["es" if es else "en"]
    own = f'secondary-actions-{n:02d}-{slug(chapter["en"])}'
    other = own + ("" if es else "-es") + ".html"
    if es:
        other = own + ".html"
    rows = []
    has_detail = any(row[2] for row in chapter["rows"])
    for herb, category, detail in chapter["rows"]:
        category_out = CATEGORY_ES.get(category, category) if es else category
        detail_out = translated_detail(detail) if es else detail
        cells = f'<th scope="row">{escape(herb)}</th><td>{escape(category_out)}</td>'
        if has_detail:
            cells += f'<td>{escape(detail_out) if detail_out else "—"}</td>'
        rows.append(f'<tr>{cells}</tr>')
    headers = ("<th>Medicinal</th><th>Categoría primaria</th>" if es else "<th>Medicinal</th><th>Primary category</th>")
    if has_detail:
        headers += "<th>Detalle de la fuente</th>" if es else "<th>Source detail</th>"
    warning = SPECIAL_WARNING_ES.get(n) if es else SPECIAL_WARNING.get(n)
    if not warning:
        warning = ("La tabla conserva una clasificación histórica para estudio. Confirma identidad, procesamiento, seguridad y pertinencia del patrón antes de cualquier uso clínico." if es else "This table preserves a historical study classification. Confirm identity, processing, safety and pattern fit before any clinical use.")
    description = (f'Conecta «{title}» con {len(chapter["rows"])} medicinales y sus categorías primarias.' if es else f'Connect “{title}” with {len(chapter["rows"])} medicinals and their primary categories.')
    prev_link = ""
    next_link = ""
    if position:
        prev = chapters[position - 1]
        prev_file = f'secondary-actions-{prev["number"]:02d}-{slug(prev["en"])}{suffix}.html'
        prev_link = f'<a class="comparison-back" href="{prev_file}">← {"Capítulo anterior" if es else "Previous chapter"}</a>'
    if position < len(chapters) - 1:
        nxt = chapters[position + 1]
        next_file = f'secondary-actions-{nxt["number"]:02d}-{slug(nxt["en"])}{suffix}.html'
        next_link = f'<a class="comparison-card__open" href="{next_file}">{"Capítulo siguiente" if es else "Next chapter"} →</a>'
    lang_link = own + (".html" if es else "-es.html")
    lang_label = "English" if es else "Español"
    group_file = f'secondary-actions{suffix}.html'
    brand = "Acciones Secundarias" if es else "Secondary Actions"
    back = "← Volver al grupo" if es else "← Back to group"
    eyebrow = f'{"Acción" if es else "Action"} {n} {"de" if es else "of"} 60'
    core_heading = "Regla principal" if es else "Core rule"
    core = ("La acción secundaria complementa la categoría primaria y el patrón; no constituye por sí sola una indicación." if es else "The secondary action complements the primary category and pattern; it is not a stand-alone indication.")
    table_heading = "Medicinales de la fuente" if es else "Medicinals in the source"
    boundary = "Límite clínico" if es else "Clinical boundary"
    source = ("Fuente: <em>Handbook of Oriental Medicine</em>, “Herb Category by Secondary Actions”, pp. 374–382. Se normalizaron errores tipográficos evidentes; las etiquetas históricas se conservan para estudio." if es else "Source: <em>Handbook of Oriental Medicine</em>, “Herb Category by Secondary Actions,” pp. 374–382. Obvious typographic errors were normalized; historical labels are retained for study.")
    all_chapters = f'Todos los capítulos · {n}/60' if es else f'All chapters · {n}/60'
    return f'''<!doctype html><html lang="{language}"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="description" content="{escape(description, quote=True)}"><title>{escape(title)} | {brand} | AcupunctureTests</title><link rel="alternate" hreflang="{"en" if es else "es"}" href="{lang_link}"><link rel="stylesheet" href="comparisons.css?v=20261026-2"></head><body>
<header class="comparison-topbar"><div class="comparison-shell comparison-topbar__inner" style="flex-wrap:wrap;padding:10px 0"><a class="comparison-brand" href="{group_file}"><span class="comparison-brand__mark" aria-hidden="true">☯</span><span>{brand}</span></a><span style="display:flex;gap:8px;flex-wrap:wrap"><a class="comparison-back" href="{lang_link}">{lang_label}</a><a class="comparison-back" href="{group_file}">{back}</a></span></div></header>
<main class="comparison-shell"><article class="study-note"><p class="comparison-eyebrow">{eyebrow}</p><h1 class="study-note__title">{escape(title)}</h1><p>{escape(description)}</p>
<section class="study-note__block"><h2 class="study-note__heading">{core_heading}</h2><table class="study-note__focus"><tbody><tr><th>{"Regla" if es else "Rule"}</th><td>{core}</td></tr></tbody></table></section>
<section class="study-note__block"><h2 class="study-note__heading study-note__heading--orange">{table_heading}</h2><table><thead><tr>{headers}</tr></thead><tbody>{''.join(rows)}</tbody></table></section>
<section class="study-note__block"><h2 class="study-note__heading study-note__heading--purple">{boundary}</h2><table class="study-note__focus"><tbody><tr><th>{"Uso responsable" if es else "Responsible use"}</th><td>{escape(warning)}</td></tr><tr><th>{"Fuente" if es else "Source"}</th><td>{source}</td></tr></tbody></table></section>
<nav aria-label="{"Navegación entre capítulos" if es else "Chapter navigation"}" style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-top:28px;padding-top:22px;border-top:1px solid #d5e0ea">{prev_link}<a class="comparison-back" href="{group_file}">{all_chapters}</a>{next_link}</nav>
</article></main><footer class="comparison-footer"><div class="comparison-shell">{"Grupo 18 · Clasificación funcional" if es else "Group 18 · Functional classification"}</div></footer></body></html>
'''


def update_navigation():
    for path in OUT.glob("*.html"):
        text = path.read_text(encoding="utf-8")
        if '<nav class="comparison-collection-links"' not in text or "secondary-actions" in text:
            continue
        es = '<html lang="es">' in text
        suffix = "-es" if es else ""
        label = "Acciones Secundarias" if es else "Secondary Actions"
        start = text.index('<nav class="comparison-collection-links"')
        end = text.index("</nav>", start)
        text = text[:end] + f'<a href="secondary-actions{suffix}.html">{label}</a>' + text[end:]
        path.write_text(text, encoding="utf-8")


def main():
    chapters = parse()
    for old_detail in OUT.glob("secondary-actions-[0-9][0-9]-*.html"):
        old_detail.unlink()
    (OUT / "secondary-actions.html").write_text(index_page(chapters, "en"), encoding="utf-8")
    (OUT / "secondary-actions-es.html").write_text(index_page(chapters, "es"), encoding="utf-8")
    for position, chapter in enumerate(chapters):
        base = f'secondary-actions-{chapter["number"]:02d}-{slug(chapter["en"])}'
        (OUT / f"{base}.html").write_text(detail_page(chapters, position, "en"), encoding="utf-8")
        (OUT / f"{base}-es.html").write_text(detail_page(chapters, position, "es"), encoding="utf-8")
    update_navigation()


if __name__ == "__main__":
    main()
