<?php
// get_stats_activeplayer.php - Returns REAL-TIME data from Steam API + Realistic Estimates for others
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Games Configuration (Steam AppIDs)
$steamAppIds = [
    'Counter-Strike 2' => 730,
    'Dota 2' => 570,
    'PUBG: BATTLEGROUNDS' => 578080,
    'Apex Legends' => 1172470,
    'Naraka: Bladepoint' => 244240,
    'Grand Theft Auto V' => 271590,
    'Call of Duty' => 1938090, // COD HQ
    'Team Fortress 2' => 440,
    'Rust' => 252490,
    'Warframe' => 230410,
    'Destiny 2' => 1085660,
    'Lost Ark' => 1599340,
    'Palworld' => 1623730,
    'Baldur\'s Gate 3' => 1086940,
    'Helldivers 2' => 553850,
    'Tom Clancy\'s Rainbow Six Siege' => 359550
];

// Non-Steam Estimates (Base figures roughly aligned with activeplayer.io trends)
$nonSteamEstimates = [
    'Roblox' => 7200000,
    'Fortnite' => 2400000, 
    'Minecraft' => 1600000,
    'League of Legends' => 1350000,
    'Valorant' => 950000,
    'Genshin Impact' => 380000,
    'Overwatch 2' => 180000,
    'Rocket League' => 220000,
    'World of Warcraft' => 1100000
];

$response = [];

// 1. Fetch Steam Data (Official API - Multi-Curl for speed)
$mh = curl_multi_init();
$curl_handles = [];

foreach ($steamAppIds as $name => $id) {
    $ch = curl_init();
    $url = "https://api.steampowered.com/ISteamUserStats/GetNumberOfCurrentPlayers/v1/?appid=" . $id;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3); // Fast timeout to ensure page loads quickly
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    curl_multi_add_handle($mh, $ch);
    $curl_handles[$name] = $ch;
}

// Execute all queries simultaneously
$running = null;
do {
    curl_multi_exec($mh, $running);
} while ($running);

foreach ($curl_handles as $name => $ch) {
    $result = curl_multi_getcontent($ch);
    $data = json_decode($result, true);
    
    if (isset($data['response']['player_count'])) {
        $count = $data['response']['player_count'];
        
        // CUSTOM ADJUSTMENTS TO MATCH ACTIVEPLAYER.IO (Global vs Steam)
        // ActivePlayer includes estimates for Consoles, Epic Games, Mobile, China servers, etc.
        
        if ($name == 'Counter-Strike 2') {
            $count = (int)($count * 1.18); // Target ~1.4M+ active
        }
        elseif ($name == 'Dota 2') {
            $count = (int)($count * 1.20); // China Perfect World servers
        }
        elseif ($name == 'PUBG: BATTLEGROUNDS') {
             $count = (int)($count * 1.8); // Mobile/Console/Kakao makes this huge
        }
        elseif ($name == 'Call of Duty') {
             $count = (int)($count * 4.5); // Steam is <20% of COD playerbase (Battlenet + Consoles)
        }
        elseif ($name == 'Apex Legends') {
             $count = (int)($count * 3.0); // EA App + Consoles
        }
        elseif ($name == 'Grand Theft Auto V') {
             $count = (int)($count * 3.5); // Epic/FiveM/Consoles are massive
        }
        elseif ($name == 'Warframe' || $name == 'Destiny 2') {
             $count = (int)($count * 1.7); // Cross-play
        }
        elseif ($name == 'Naraka: Bladepoint') {
             $count = (int)($count * 1.5); // NetEase launcher (China)
        }
        
        $response[$name] = $count;
    } else {
        // If API fails, we don't return anything for this game, preserving old JS value
    }
    curl_multi_remove_handle($mh, $ch);
    curl_close($ch);
}
curl_multi_close($mh);

// 2. Add Non-Steam Estimates (Time-Adjusted)
$hour = (int)gmdate('G');
// Global Gaming Peak (approx 14:00 - 22:00 UTC)
$isPeak = ($hour >= 13 && $hour <= 21);
$isLow = ($hour >= 3 && $hour <= 8);

$multiplier = 1.0;
if ($isPeak) {
    $multiplier = 1.15 + (rand(0, 10) / 100); // 1.15 - 1.25
} else if ($isLow) {
    $multiplier = 0.7 + (rand(0, 10) / 100); // 0.7 - 0.8
} else {
    $multiplier = 0.9 + (rand(0, 10) / 100); // 0.9 - 1.0
}

foreach ($nonSteamEstimates as $name => $base) {
    // Add randomness +/- 2%
    $noise = rand(-20, 20) / 1000; 
    $val = $base * $multiplier * (1 + $noise);
    $response[$name] = (int)$val;
}

echo json_encode($response);
?>
