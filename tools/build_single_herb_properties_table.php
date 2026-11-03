<?php
declare(strict_types=1);

// Builds the Board-roster properties table from the verified Single Herbs profiles.

define('MOODLE_INTERNAL', true);
function s(mixed $value): string { return (string)$value; }
final class moodle_url {
    public function __construct(string $path = '', array $params = []) {}
    public function out(bool $escaped = true): string { return '#'; }
}

$root = dirname(__DIR__);
$indexPath = $root . '/blocks/myprogress/single-herb-card-index.json';
$outputPath = $root . '/blocks/myprogress/single-herb-properties.json';
$index = json_decode((string)file_get_contents($indexPath), true, 512, JSON_THROW_ON_ERROR);

$normalize = static function(string $value): string {
    $value = strtolower(trim((string)preg_replace('/\s*\*+$/u', '', $value)));
    return trim((string)preg_replace('/[^a-z0-9]+/', ' ', $value));
};
$aliases = ['xin yi hua' => 'xin yi'];
$keyFor = static function(string $value) use ($normalize, $aliases): string {
    $key = $normalize($value);
    return $aliases[$key] ?? $key;
};

$extractProfiles = static function(string $file, bool $spanish): array {
    $variables = (static function(string $profileFile, bool $spanishMode): array {
        $ises = $spanishMode;
        $selected = ['number' => 1, 'name' => 'Builder'];
        $requestedherb = '';
        $requestedherbcard = null;
        $normalizeherbname = static function(string $value): string {
            $value = strtolower(trim((string)preg_replace('/\s*\*$/', '', $value)));
            $value = trim((string)preg_replace('/[^a-z0-9]+/', ' ', $value));
            return $value === 'xin yi hua' ? 'xin yi' : $value;
        };
        $guidelineherbkeys = [];
        $indexFile = dirname($profileFile) . '/single-herb-card-index.json';
        $indexData = is_readable($indexFile)
            ? json_decode((string)file_get_contents($indexFile), true)
            : null;
        foreach (is_array($indexData) ? ($indexData['herbs'] ?? []) : [] as $indexHerb) {
            if (!empty($indexHerb['name'])) {
                $guidelineherbkeys[$normalizeherbname((string)$indexHerb['name'])] = true;
            }
        }
        $categoryindexurl = new moodle_url('#');
        ob_start();
        include $profileFile;
        ob_end_clean();
        return get_defined_vars();
    })($file, $spanish);

    $profiles = [];
    foreach ($variables as $value) {
        if (!is_array($value)) {
            continue;
        }
        foreach ($value as $row) {
            if (is_array($row) && isset($row['name'], $row['latin'], $row['properties'], $row['channels'], $row['dose'])) {
                $profiles[(string)$row['name']] = $row;
            }
        }
    }
    return $profiles;
};

$english = [];
$spanish = [];
foreach (glob($root . '/blocks/myprogress/single-herbs-category-*.php') ?: [] as $file) {
    $english += $extractProfiles($file, false);
    $spanish += $extractProfiles($file, true);
}

$topicOne = [
    ['Ma Huang', 'Ephedrae Herba', 'Acrid, slightly bitter; warm', 'Acre, ligeramente amarga; tibia', 'Lung, Bladder', 'Pulmón, Vejiga', '3–9 g'],
    ['Gui Zhi', 'Cinnamomi Ramulus', 'Acrid, sweet; warm', 'Acre, dulce; tibia', 'Heart, Lung, Bladder', 'Corazón, Pulmón, Vejiga', '3–9 g'],
    ['Zi Su Ye', 'Perillae Folium', 'Acrid, aromatic; warm', 'Acre, aromática; tibia', 'Lung, Spleen', 'Pulmón, Bazo', '3–9 g'],
    ['Jing Jie', 'Schizonepetae Herba', 'Acrid; slightly warm', 'Acre; ligeramente tibia', 'Lung, Liver', 'Pulmón, Hígado', '3–10 g'],
    ['Fang Feng', 'Saposhnikoviae Radix', 'Acrid, sweet; slightly warm', 'Acre, dulce; ligeramente tibia', 'Bladder, Liver, Spleen', 'Vejiga, Hígado, Bazo', '4.5–9 g'],
    ['Qiang Huo', 'Notopterygii Rhizoma seu Radix', 'Acrid, bitter; warm', 'Acre, amarga; tibia', 'Bladder, Kidney', 'Vejiga, Riñón', '3–9 g'],
    ['Bai Zhi', 'Angelicae Dahuricae Radix', 'Acrid, aromatic; warm', 'Acre, aromática; tibia', 'Lung, Stomach', 'Pulmón, Estómago', '3–9 g'],
    ['Sheng Jiang', 'Zingiberis Rhizoma Recens', 'Acrid; slightly warm', 'Acre; ligeramente tibia', 'Lung, Spleen, Stomach', 'Pulmón, Bazo, Estómago', '3–10 g'],
    ['Xin Yi Hua', 'Magnoliae Flos', 'Acrid, aromatic; warm', 'Acre, aromática; tibia', 'Lung, Stomach', 'Pulmón, Estómago', '3–9 g, wrapped'],
];
foreach ($topicOne as [$name, $latin, $propertiesEn, $propertiesEs, $channelsEn, $channelsEs, $dose]) {
    $english[$name] = ['name' => $name, 'latin' => $latin, 'properties' => $propertiesEn, 'channels' => $channelsEn, 'dose' => $dose];
    $spanish[$name] = ['name' => $name, 'latin' => $latin, 'properties' => $propertiesEs, 'channels' => $channelsEs, 'dose' => $dose];
}

// Chart 6 supplied by the user contains properties for 60 formula-leading herbs.
// Only rows that complete an official herb not already covered by a detailed profile are added.
$detailedProfileKeys = [];
foreach (array_keys($english) as $profileName) {
    $detailedProfileKeys[$keyFor($profileName)] = true;
}
$chartProfiles = [
    ['Bie Jia', 'Trionycis Carapax', 'Salty; slightly cold', 'Salada; ligeramente fría', 'Liver, Spleen', 'Hígado, Bazo'],
    ['Mu Tong', 'Akebiae Caulis', 'Bitter; cool', 'Amarga; fresca', 'Bladder, Heart, Small Intestine', 'Vejiga, Corazón, Intestino Delgado'],
    ['Ze Xie', 'Alismatis Rhizoma', 'Sweet, bland; cold', 'Dulce, insípida; fría', 'Kidney, Bladder', 'Riñón, Vejiga'],
    ['Zhu Ling', 'Polyporus', 'Sweet, bland; slightly cool', 'Dulce, insípida; ligeramente fresca', 'Spleen, Kidney, Bladder', 'Bazo, Riñón, Vejiga'],
    ['Fu Ling', 'Poria', 'Sweet, bland; neutral', 'Dulce, insípida; neutra', 'Heart, Spleen, Lung', 'Corazón, Bazo, Pulmón'],
    ['Hua Jiao', 'Zanthoxyli Pericarpium', 'Acrid; hot, slightly toxic', 'Acre; caliente, ligeramente tóxica', 'Kidney, Spleen, Stomach', 'Riñón, Bazo, Estómago'],
    ['Zi Su Zi', 'Perillae Fructus', 'Acrid; warm', 'Acre; tibia', 'Large Intestine, Lung', 'Intestino Grueso, Pulmón'],
    ['Bai Guo', 'Ginkgo Semen', 'Sweet, bitter, astringent; neutral, slightly toxic', 'Dulce, amarga, astringente; neutra, ligeramente tóxica', 'Kidney, Lung', 'Riñón, Pulmón'],
    ['Hong Hua', 'Carthami Flos', 'Acrid; warm', 'Acre; tibia', 'Heart, Liver', 'Corazón, Hígado'],
    ['Chuan Xiong', 'Chuanxiong Rhizoma', 'Acrid; warm', 'Acre; tibia', 'Liver, Gallbladder, Pericardium', 'Hígado, Vesícula Biliar, Pericardio'],
    ['Bu Gu Zhi', 'Psoraleae Fructus', 'Acrid, bitter; very warm', 'Acre, amarga; muy tibia', 'Kidney, Spleen', 'Riñón, Bazo'],
    ['Gui Ban', 'Testudinis Plastrum', 'Salty, sweet; cold', 'Salada, dulce; fría', 'Heart, Kidney, Liver', 'Corazón, Riñón, Hígado'],
];
$supplementalSourceByKey = [];
foreach ($chartProfiles as [$name, $latin, $propertiesEn, $propertiesEs, $channelsEn, $channelsEs]) {
    if (!isset($detailedProfileKeys[$keyFor($name)])) {
        $english[$name] = ['name' => $name, 'latin' => $latin, 'properties' => $propertiesEn, 'channels' => $channelsEn, 'dose' => null];
        $spanish[$name] = ['name' => $name, 'latin' => $latin, 'properties' => $propertiesEs, 'channels' => $channelsEs, 'dose' => null];
        $supplementalSourceByKey[$keyFor($name)] = 'king-herbs-chart';
    }
}

// The user-provided NCCAOM Actions and Indications table supplies additional
// property rows. Only unambiguous official-roster matches are retained; action
// paragraphs and uncertain OCR matches are intentionally excluded.
$actionsProfiles = [
    ['Bai Hua She She Cao', 'Oldenlandiae Herba', 'Bitter, sweet; cold', 'Amarga, dulce; fría', 'Liver, Stomach, Large Intestine, Small Intestine', 'Hígado, Estómago, Intestino Grueso, Intestino Delgado'],
    ['Bai Jiang Can', 'Bombyx Batryticatus', 'Acrid, salty; neutral', 'Acre, salada; neutra', 'Liver, Lung', 'Hígado, Pulmón'],
    ['Bai Tou Weng', 'Pulsatillae Radix', 'Bitter; cold', 'Amarga; fría', 'Liver, Stomach, Large Intestine', 'Hígado, Estómago, Intestino Grueso'],
    ['Bai Xian Pi', 'Dictamni Cortex', 'Bitter; cold', 'Amarga; fría', 'Spleen, Stomach', 'Bazo, Estómago'],
    ['Ban Zhi Lian', 'Scutellariae Barbatae Herba', 'Acrid, slightly bitter; cool', 'Acre, ligeramente amarga; fresca', 'Liver, Lung, Stomach', 'Hígado, Pulmón, Estómago'],
    ['Bing Pian', 'Borneolum', 'Acrid, bitter; slightly cold', 'Acre, amarga; ligeramente fría', 'Heart, Spleen', 'Corazón, Bazo'],
    ['Chi Xiao Dou', 'Vignae Semen', 'Sweet, sour; neutral', 'Dulce, agria; neutra', 'Heart, Small Intestine', 'Corazón, Intestino Delgado'],
    ['Chuan Xin Lian', 'Andrographis Herba', 'Bitter; cold', 'Amarga; fría', 'Large Intestine, Lung, Small Intestine, Stomach', 'Intestino Grueso, Pulmón, Intestino Delgado, Estómago'],
    ['Cong Bai', 'Allii Fistulosi Bulbus', 'Acrid; warm', 'Acre; tibia', 'Lung, Stomach', 'Pulmón, Estómago'],
    ['Da Ji', 'Cirsii Japonici Herba seu Radix', 'Sweet; cool', 'Dulce; fresca', 'Liver, Spleen, Heart', 'Hígado, Bazo, Corazón'],
    ['Da Qing Ye', 'Isatidis Folium', 'Bitter, salty; very cold', 'Amarga, salada; muy fría', 'Heart, Lung, Stomach', 'Corazón, Pulmón, Estómago'],
    ['Dong Kui Zi', 'Malvae Semen', 'Sweet; cold', 'Dulce; fría', 'Bladder, Large Intestine, Small Intestine', 'Vejiga, Intestino Grueso, Intestino Delgado'],
    ['Fo Shou', 'Citri Sarcodactylis Fructus', 'Acrid, bitter; warm', 'Acre, amarga; tibia', 'Liver, Lung, Stomach, Spleen', 'Hígado, Pulmón, Estómago, Bazo'],
    ['Ge Jie', 'Gecko', 'Salty; neutral', 'Salada; neutra', 'Lung, Kidney', 'Pulmón, Riñón'],
    ['Hai Jin Sha', 'Lygodii Spora', 'Sweet, bland; cold', 'Dulce, insípida; fría', 'Bladder, Small Intestine', 'Vejiga, Intestino Delgado'],
    ['Han Lian Cao', 'Ecliptae Herba', 'Sweet, sour; cool', 'Dulce, agria; fresca', 'Kidney, Liver', 'Riñón, Hígado'],
    ['He Shou Wu', 'Polygoni Multiflori Radix Praeparata', 'Bitter, sweet, astringent; slightly warm', 'Amarga, dulce, astringente; ligeramente tibia', 'Liver, Kidney', 'Hígado, Riñón'],
    ['Huang Jing', 'Polygonati Rhizoma', 'Sweet; neutral', 'Dulce; neutra', 'Kidney, Lung, Spleen', 'Riñón, Pulmón, Bazo'],
    ['Ji Xue Teng', 'Spatholobi Caulis', 'Bitter, sweet; warm', 'Amarga, dulce; tibia', 'Heart, Liver, Spleen', 'Corazón, Hígado, Bazo'],
    ['Jin Qian Cao', 'Lysimachiae Herba', 'Sweet, salty; slightly cold', 'Dulce, salada; ligeramente fría', 'Bladder, Gallbladder, Kidney, Liver', 'Vejiga, Vesícula Biliar, Riñón, Hígado'],
    ['Kun Bu', 'Eckloniae Thallus', 'Salty; cold', 'Salada; fría', 'Kidney, Liver, Stomach', 'Riñón, Hígado, Estómago'],
    ['Ling Zhi', 'Ganoderma', 'Sweet; neutral', 'Dulce; neutra', 'Heart, Liver, Lung', 'Corazón, Hígado, Pulmón'],
    ['Liu Huang', 'Sulfur', 'Sour; hot, toxic', 'Agria; caliente, tóxica', 'Kidney, Large Intestine, Pericardium', 'Riñón, Intestino Grueso, Pericardio'],
    ['Lu Dou', 'Vignae Radiatae Semen', 'Sweet; cold', 'Dulce; fría', 'Heart, Stomach', 'Corazón, Estómago'],
    ['Lu Lu Tong', 'Liquidambaris Fructus', 'Bitter; neutral', 'Amarga; neutra', 'Liver, Stomach', 'Hígado, Estómago'],
    ['Ma Bo', 'Lasiosphaera seu Calvatia', 'Acrid; neutral', 'Acre; neutra', 'Lung', 'Pulmón'],
    ['Mei Gui Hua', 'Rosae Rugosae Flos', 'Sweet, slightly bitter; warm', 'Dulce, ligeramente amarga; tibia', 'Liver, Spleen', 'Hígado, Bazo'],
    ['Ou Jie', 'Nelumbinis Nodus Rhizomatis', 'Sweet, astringent; neutral', 'Dulce, astringente; neutra', 'Lung, Stomach, Liver', 'Pulmón, Estómago, Hígado'],
    ['Pang Da Hai', 'Sterculiae Lychnophorae Semen', 'Sweet; cold', 'Dulce; fría', 'Large Intestine, Lung', 'Intestino Grueso, Pulmón'],
    ['Pei Lan', 'Eupatorii Herba', 'Acrid; neutral', 'Acre; neutra', 'Spleen, Stomach', 'Bazo, Estómago'],
    ['Qin Pi', 'Fraxini Cortex', 'Bitter, astringent; cold', 'Amarga, astringente; fría', 'Gallbladder, Large Intestine, Liver, Stomach', 'Vesícula Biliar, Intestino Grueso, Hígado, Estómago'],
    ['Qing Dai', 'Indigo Naturalis', 'Salty; cold', 'Salada; fría', 'Liver, Lung, Stomach', 'Hígado, Pulmón, Estómago'],
    ['Quan Xie', 'Scorpio', 'Salty, acrid; neutral, toxic', 'Salada, acre; neutra, tóxica', 'Liver', 'Hígado'],
    ['Sha Yuan Ji Li', 'Astragali Complanati Semen', 'Sweet; warm', 'Dulce; tibia', 'Kidney, Liver', 'Riñón, Hígado'],
    ['She Xiang', 'Moschus', 'Acrid; warm, aromatic', 'Acre; tibia, aromática', 'Heart, Spleen, Liver', 'Corazón, Bazo, Hígado'],
    ['Si Gua Luo', 'Luffae Fructus Retinervus', 'Sweet; neutral', 'Dulce; neutra', 'Lung, Stomach, Liver', 'Pulmón, Estómago, Hígado'],
    ['Ting Li Zi', 'Lepidii seu Descurainiae Semen', 'Acrid, bitter; very cold', 'Acre, amarga; muy fría', 'Lung, Bladder', 'Pulmón, Vejiga'],
    ['Tong Cao', 'Tetrapanacis Medulla', 'Sweet, bland; slightly cold', 'Dulce, insípida; ligeramente fría', 'Lung, Stomach', 'Pulmón, Estómago'],
    ['Wu Gong', 'Scolopendra', 'Acrid; warm, toxic', 'Acre; tibia, tóxica', 'Liver', 'Hígado'],
    ['Wu Ling Zhi', 'Trogopterori Faeces', 'Bitter, sweet; warm', 'Amarga, dulce; tibia', 'Liver', 'Hígado'],
    ['Xi Yang Shen', 'Panacis Quinquefolii Radix', 'Sweet, slightly bitter; cold', 'Dulce, ligeramente amarga; fría', 'Heart, Kidney, Lung', 'Corazón, Riñón, Pulmón'],
    ['Xiao Ji', 'Cirsii Herba', 'Sweet; cool', 'Dulce; fresca', 'Liver, Heart', 'Hígado, Corazón'],
    ['Xie Bai', 'Allii Macrostemonis Bulbus', 'Acrid, bitter; warm', 'Acre, amarga; tibia', 'Large Intestine, Lung, Stomach', 'Intestino Grueso, Pulmón, Estómago'],
    ['Ye Ju Hua', 'Chrysanthemi Indici Flos', 'Bitter, acrid; slightly cold', 'Amarga, acre; ligeramente fría', 'Lung, Liver', 'Pulmón, Hígado'],
    ['Yu Xing Cao', 'Houttuyniae Herba', 'Acrid; cool', 'Acre; fresca', 'Lung, Large Intestine', 'Pulmón, Intestino Grueso'],
    ['Yu Zhu', 'Polygonati Odorati Rhizoma', 'Sweet; slightly cold', 'Dulce; ligeramente fría', 'Lung, Stomach', 'Pulmón, Estómago'],
];
foreach ($actionsProfiles as [$name, $latin, $propertiesEn, $propertiesEs, $channelsEn, $channelsEs]) {
    $key = $keyFor($name);
    if (!isset($detailedProfileKeys[$key]) && !isset($supplementalSourceByKey[$key])) {
        $english[$name] = ['name' => $name, 'latin' => $latin, 'properties' => $propertiesEn, 'channels' => $channelsEn, 'dose' => null];
        $spanish[$name] = ['name' => $name, 'latin' => $latin, 'properties' => $propertiesEs, 'channels' => $channelsEs, 'dose' => null];
        $supplementalSourceByKey[$key] = 'actions-indications-table';
    }
}

// User-provided Bensky Chapter 1 excerpts are used only to complete fields that
// are still empty. They never replace a detailed Yang-based profile or any
// existing property value. Page references follow the printed chapter pages.
$benskyChapterOneSource = 'Bensky et al., Chinese Herbal Medicine: Materia Medica, 3rd ed., Chapter 1 (user-provided excerpts)';
$benskyChapterOneProfiles = [
    ['Cong Bai', 'Allii fistulosi Bulbus', 'Acrid; warm', 'Acre; tibia', 'Lung, Stomach', 'Pulmón, Estómago', '3–9 g', 'p. 34'],
    ['Gao Ben', 'Ligustici Rhizoma', 'Acrid; warm', 'Acre; tibia', 'Bladder, Governing Vessel', 'Vejiga, Vaso Gobernador', '3–9 g', 'p. 22'],
    ['Xi Xin', 'Asari Herba', 'Acrid; warm', 'Acre; tibia', 'Lung, Heart, Kidney', 'Pulmón, Corazón, Riñón', '1–3 g', 'pp. 27–29'],
    ['Xiang Ru', 'Moslae Herba', 'Acrid, aromatic; slightly warm', 'Acre, aromática; ligeramente tibia', 'Lung, Stomach', 'Pulmón, Estómago', '3–9 g', 'p. 37'],
    ['Dan Dou Chi', 'Sojae Semen preparatum', 'Sweet, slightly bitter; cold or warm depending on preparation', 'Dulce, ligeramente amarga; fría o tibia según la preparación', 'Lung, Stomach', 'Pulmón, Estómago', '6–12 g', 'pp. 63–65'],
    ['Mu Zei', 'Equiseti hiemalis Herba', 'Sweet, bitter; neutral', 'Dulce, amarga; neutra', 'Liver, Lung, Gallbladder', 'Hígado, Pulmón, Vesícula Biliar', '3–9 g', 'p. 69'],
];
$benskyReferenceByKey = [];
$benskyDoseSourceByKey = [];
foreach ($benskyChapterOneProfiles as [$name, $latin, $propertiesEn, $propertiesEs, $channelsEn, $channelsEs, $dose, $reference]) {
    $key = $keyFor($name);
    $existingName = null;
    foreach (array_keys($english) as $candidateName) {
        if ($keyFor((string)$candidateName) === $key) {
            $existingName = (string)$candidateName;
            break;
        }
    }
    if ($existingName === null) {
        $english[$name] = ['name' => $name, 'latin' => $latin, 'properties' => $propertiesEn, 'channels' => $channelsEn, 'dose' => $dose];
        $spanish[$name] = ['name' => $name, 'latin' => $latin, 'properties' => $propertiesEs, 'channels' => $channelsEs, 'dose' => $dose];
        $supplementalSourceByKey[$key] = 'bensky-chapter-1';
        $benskyDoseSourceByKey[$key] = 'bensky-chapter-1';
    } else {
        $spanishName = $existingName;
        foreach (array_keys($spanish) as $candidateName) {
            if ($keyFor((string)$candidateName) === $key) {
                $spanishName = (string)$candidateName;
                break;
            }
        }
        if (empty($english[$existingName]['properties'])) {
            $english[$existingName]['properties'] = $propertiesEn;
            $spanish[$spanishName]['properties'] = $propertiesEs;
            $supplementalSourceByKey[$key] = 'bensky-chapter-1';
        }
        if (empty($english[$existingName]['channels'])) {
            $english[$existingName]['channels'] = $channelsEn;
            $spanish[$spanishName]['channels'] = $channelsEs;
            $supplementalSourceByKey[$key] = 'bensky-chapter-1';
        }
        if (empty($english[$existingName]['dose'])) {
            $english[$existingName]['dose'] = $dose;
            $spanish[$spanishName]['dose'] = $dose;
            $benskyDoseSourceByKey[$key] = 'bensky-chapter-1';
        }
    }
    $benskyReferenceByKey[$key] = $reference;
}

$appendixGroups = [
    'Less than 0.1 g' => 'Bing Pian|She Xiang|Tan Xiang',
    'Up to 1 g' => 'Zhen Zhu|Zhu Sha',
    'Up to 3 g' => 'Bai Fu Zi|Cao Dou Kou|Ding Xiang|Hu Po|Lu Rong|Ma Bo|Mang Chong|Quan Xie|Wu Gong|Xi Xin|Zhe Chong',
    'Up to 6 g' => 'Ai Ye|Bai Dou Kou|Bo He|Chen Xiang|Deng Xin Cao|Fan Xie Ye|Gao Liang Jiang|Ge Jie|He Zi|Hua Jiao|Huang Lian|Ling Yang Jiao|Mu Tong|Qing Dai|Rou Gui|Sha Ren|Sheng Ma|Tan Xiang|Wu Wei Zi|Wu Zhu Yu|Xi Yang Shen|Yi Zhi Ren|Yue Ji Hua|Zi He Che',
    'Up to 12 g' => 'Bai Tou Weng|Chi Shao Yao|Da Huang|Huang Qin|Mai Ya|Mu Dan Pi|Pi Pa Ye|Zhi Mu',
    'Up to 15 g' => 'Bai Ji|Ba Ji Tian|Bai Mao Gen|Bai Zhu|Bei Sha Shen|Ce Bai Ye|Che Qian Zi|Da Ji|Da Zao|Dan Shen|Dan Zhu Ye|Dang Gui|Dang Shen|Di Ding|Di Fu Zi|Di Yu|Du Zhong|Gou Ji|Gou Teng|Hai Zao|He Huan Pi|Hua Shi|Huai Hua|Kun Bu|Lai Fu Zi|Lian Qiao|Lian Zi|Lu Gen|Ma Chi Xian|Mang Xiao|Nu Zhen Zi|Pu Gong Ying|Qian Shi|Shan Zha|Shan Zhu Yu|Shen Qu|Tian Hua Fen|Tian Men Dong|Tu Si Zi|Xiao Ji|Xuan Shen|Yin Yang Huo|Yu Zhu',
    'Up to 20 g' => 'Bai Bian Dou|Bai Zi Ren|Chi Shi Zhi|He Shou Wu|Huang Jing|Huang Qi|Mai Men Dong|Rou Cong Rong|Sha Yuan Zi|Suan Zao Ren|Xu Duan',
    'Up to 30 g' => 'Bai He|Bai Shao Yao|Bian Xu|Bie Jia|Ci Shi|Chi Xiao Dou|Dai Zhe Shi|Feng Mi|Fu Xiao Mai|Gua Lou|Gui Ban|Han Lian Cao|Hei Zhi Ma|Hu Tao Rou|Huo Ma Ren|Ji Xue Teng|Long Gu|Lu Dou|Mu Li|Ou Jie|Qu Mai|Sang Ji Sheng|Shan Yao|Sheng Di Huang|Shi Jue Ming|Shu Di Huang|Tai Zi Shen|Ye Jiao Teng|Yi Mu Cao|Yi Yi Ren|Yin Chen Hao|Yu Xing Cao|Zao Xin Tu|Zi Zhu Cao',
    'Up to 60 g' => 'Fei Zi|Jin Qian Cao|Jin Yin Hua|Long Yan Rou|Shi Wei|Xian He Cao|Xiao Mai|Yi Tang|Zhu Li',
    'Up to 120 g' => 'Nan Gua Zi',
];
$appendixDose = [];
foreach ($appendixGroups as $limit => $names) {
    foreach (explode('|', $names) as $name) {
        $key = $keyFor($name);
        if (isset($appendixDose[$key]) && $appendixDose[$key] !== $limit) {
            $appendixDose[$key] .= ' / ' . $limit . ' (source lists both)';
        } else {
            $appendixDose[$key] = $limit;
        }
    }
}

$profilesEn = [];
foreach ($english as $name => $profile) {
    $profilesEn[$keyFor($name)] = $profile;
}
$profilesEs = [];
foreach ($spanish as $name => $profile) {
    $profilesEs[$keyFor($name)] = $profile;
}

$splitProperties = static function(?string $value): array {
    if ($value === null || trim($value) === '') {
        return [null, null];
    }
    $parts = array_map('trim', explode(';', $value, 2));
    return [$parts[0] ?: null, $parts[1] ?? null];
};

$rows = [];
$verified = 0;
$propertiesOnly = 0;
$doseOnly = 0;
foreach ($index['herbs'] ?? [] as $card) {
    $key = $keyFor((string)$card['name']);
    $profileEn = $profilesEn[$key] ?? null;
    $profileEs = $profilesEs[$key] ?? null;
    [$flavorEn, $temperatureEn] = $splitProperties($profileEn['properties'] ?? null);
    [$flavorEs, $temperatureEs] = $splitProperties($profileEs['properties'] ?? null);
    $hasProfile = $profileEn !== null;
    $hasDetailedProfile = isset($detailedProfileKeys[$key]);
    $hasPropertiesOnly = $hasProfile && !$hasDetailedProfile;
    $hasAppendixDose = isset($appendixDose[$key]);
    if ($hasDetailedProfile) {
        $verified++;
    } elseif ($hasPropertiesOnly) {
        $propertiesOnly++;
    } elseif ($hasAppendixDose) {
        $doseOnly++;
    }
    $rows[] = [
        'name' => $card['name'],
        'latin' => $card['latin'],
        'guideNumber' => $card['guideNumber'],
        'status' => $hasDetailedProfile ? 'verified' : ($hasPropertiesOnly ? 'properties-only' : ($hasAppendixDose ? 'dose-only' : 'pending')),
        'propertySource' => $hasDetailedProfile ? 'detailed-profile' : ($hasPropertiesOnly ? ($supplementalSourceByKey[$key] ?? 'supplemental-table') : null),
        'flavor' => ['en' => $flavorEn, 'es' => $flavorEs],
        'temperature' => ['en' => $temperatureEn, 'es' => $temperatureEs],
        'channels' => ['en' => $profileEn['channels'] ?? null, 'es' => $profileEs['channels'] ?? null],
        'dose' => $profileEn['dose'] ?? null,
        'doseSource' => $benskyDoseSourceByKey[$key] ?? null,
        'appendixDose' => $appendixDose[$key] ?? null,
        'benskyReference' => $benskyReferenceByKey[$key] ?? null,
    ];
}

$total = count($rows);
$pending = $total - $verified - $propertiesOnly - $doseOnly;
if ($total !== 304 || $verified < 170 || $propertiesOnly < 10 || $verified + $propertiesOnly + $doseOnly + $pending !== $total) {
    throw new RuntimeException("Invalid table coverage: total=$total verified=$verified propertiesOnly=$propertiesOnly doseOnly=$doseOnly pending=$pending");
}

$payload = [
    'version' => 1,
    'source' => 'NCBAHM Appendix A roster plus verified Single Herbs profiles',
    'doseAppendixSource' => 'User-provided Appendix 1: Daily dosages for individual crude herbs above 6–9 grams',
    'propertyChartSource' => 'User-provided Chart 6: Property of King Herbs',
    'actionsTableSource' => 'User-provided NCCAOM Herbs: Actions and Indications table; property fields only',
    'benskyChapterOneSource' => $benskyChapterOneSource,
    'benskyMergeRule' => 'Bensky fills empty fields only; existing Yang-based profiles are never replaced',
    'total' => $total,
    'verified' => $verified,
    'propertiesOnly' => $propertiesOnly,
    'doseOnly' => $doseOnly,
    'pending' => $pending,
    'herbs' => $rows,
];
file_put_contents($outputPath, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n");
fwrite(STDOUT, "Built $outputPath: $total total, $verified detailed, $propertiesOnly properties-only, $doseOnly dose-only, $pending pending.\n");
