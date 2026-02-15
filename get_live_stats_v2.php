<?php
// get_live_stats_v2.php - V2 with Aggressive Multipliers for Total Player Estimates
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

// 1. STEAM APP CONFIGURATION
$steamAppIds = [
    'Counter-Strike 2' => 730,
    'Dota 2' => 570,
    'PUBG: BATTLEGROUNDS' => 578080,
    'Apex Legends' => 1172470,
    'Naraka: Bladepoint' => 244240,
    'Grand Theft Auto V' => 271590,
    'Call of Duty' => 1938090,
    'Team Fortress 2' => 440,
    'Rust' => 252490,
    'Warframe' => 230410,
    'Destiny 2' => 1085660,
    'Overwatch 2' => 2357570, // Steam ID, but mostly Bnet
    'Rocket League' => 252950, // Legacy Steam ID, mostly Epic
    'Baldur\'s Gate 3' => 1086940,
    'Helldivers 2' => 553850,
    'Rainbow Six Siege' => 359550
];

// 2. NON-STEAM ESTIMATES (Baselines for ActivePlayer.io sync)
// These fluctuate by time of day logic below
$nonSteamEstimates = [
    'Roblox' => 7100000,
    'Fortnite' => 2850000,
    'Minecraft' => 1700000,
    'League of Legends' => 1450000,
    'Valorant' => 1050000,
    'Genshin Impact' => 420000,
    'World of Warcraft' => 1150000
];

$response = [];

// --- STEP 1: FETCH REAL STEAM DATA ---
$mh = curl_multi_init();
$curl_handles = [];

foreach ($steamAppIds as $name => $id) {
    $ch = curl_init();
    $url = "https://api.steampowered.com/ISteamUserStats/GetNumberOfCurrentPlayers/v1/?appid=" . $id;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 2); // Fast
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_multi_add_handle($mh, $ch);
    $curl_handles[$name] = $ch;
}

$running = null;
do {
    curl_multi_exec($mh, $running);
} while ($running);

foreach ($curl_handles as $name => $ch) {
    $result = curl_multi_getcontent($ch);
    $data = json_decode($result, true);
    
    // Default to 0 if fail
    $count = 0;
    if (isset($data['response']['player_count'])) {
        $count = $data['response']['player_count'];
    }

    // --- STEP 2: APPLY PLATFORM MULTIPLIERS (The "ActivePlayer.io" Factor) ---
    // Steam numbers are often 10-20% of total for cross-platform games.
    // For PC-exclusive Steam games, they are 90-100%.
    
    // CS2: Steam + Perfect World (China) + others
    if ($name == 'Counter-Strike 2') $count *= 1.20; // Corrected to match ActivePlayer 1.49M+
    
    // Dota 2: Steam + China
    elseif ($name == 'Dota 2') $count *= 1.25;

    // PUBG: Steam is small vs Mobile/Console/Asia
    elseif ($name == 'PUBG: BATTLEGROUNDS') $count *= 2.1;

    // Apex: EA App & Consoles are huge
    elseif ($name == 'Apex Legends') $count *= 3.5;

    // COD: Battle.net & Consoles are 90% of base
    elseif ($name == 'Call of Duty') $count *= 5.0;

    // GTA V: Epic, Rockstar Launcher, Consoles (FiveM included in estimates usually)
    elseif ($name == 'Grand Theft Auto V') $count *= 4.0;

    // Overwatch 2: Battle.net is main
    elseif ($name == 'Overwatch 2') $count *= 6.0;

    // Rocket League: Epic is main
    elseif ($name == 'Rocket League') $count *= 6.0; // Steam version is dead/delisted

    // Naraka: NetEase
    elseif ($name == 'Naraka: Bladepoint') $count *= 1.6;

    // Rainbow Six: Uplay + Consoles
    elseif ($name == 'Rainbow Six Siege') $count *= 3.0;

    // Warframe/Destiny: Crossplay
    elseif ($name == 'Warframe' || $name == 'Destiny 2') $count *= 1.8;

    $response[$name] = (int)$count;

    curl_multi_remove_handle($mh, $ch);
    curl_close($ch);
}
curl_multi_close($mh);


// --- STEP 3: NON-STEAM CALCULATIONS ---
$hour = (int)gmdate('G');
// Peak: 13:00 - 22:00 UTC
$isPeak = ($hour >= 13 && $hour <= 22);
$isLow = ($hour >= 4 && $hour <= 9);

$timeMult = 1.0;
if ($isPeak) $timeMult = 1.15;
if ($isLow) $timeMult = 0.75;

foreach ($nonSteamEstimates as $name => $base) {
    // Random fluctuation +/- 2%
    $noise = rand(980, 1020) / 1000;
    $val = $base * $timeMult * $noise;
    $response[$name] = (int)$val;
}

echo json_encode($response);
?>
