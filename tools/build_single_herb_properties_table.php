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
        $guidelineherbkeys = [];
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
foreach ($chartProfiles as [$name, $latin, $propertiesEn, $propertiesEs, $channelsEn, $channelsEs]) {
    if (!isset($detailedProfileKeys[$keyFor($name)])) {
        $english[$name] = ['name' => $name, 'latin' => $latin, 'properties' => $propertiesEn, 'channels' => $channelsEn, 'dose' => null];
        $spanish[$name] = ['name' => $name, 'latin' => $latin, 'properties' => $propertiesEs, 'channels' => $channelsEs, 'dose' => null];
    }
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
        'propertySource' => $hasDetailedProfile ? 'detailed-profile' : ($hasPropertiesOnly ? 'king-herbs-chart' : null),
        'flavor' => ['en' => $flavorEn, 'es' => $flavorEs],
        'temperature' => ['en' => $temperatureEn, 'es' => $temperatureEs],
        'channels' => ['en' => $profileEn['channels'] ?? null, 'es' => $profileEs['channels'] ?? null],
        'dose' => $profileEn['dose'] ?? null,
        'appendixDose' => $appendixDose[$key] ?? null,
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
    'total' => $total,
    'verified' => $verified,
    'propertiesOnly' => $propertiesOnly,
    'doseOnly' => $doseOnly,
    'pending' => $pending,
    'herbs' => $rows,
];
file_put_contents($outputPath, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n");
fwrite(STDOUT, "Built $outputPath: $total total, $verified detailed, $propertiesOnly properties-only, $doseOnly dose-only, $pending pending.\n");
