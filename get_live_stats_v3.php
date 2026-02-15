<?php
// get_live_stats_v3.php - SCR scraping ActivePlayer.io directly with HEADERS and ROBUST REGEX
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache, no-store, must-revalidate');

// Game Slugs
$games = [
    'Counter-Strike 2' => 'counter-strike-2',
    'Dota 2' => 'dota-2',
    'PUBG: BATTLEGROUNDS' => 'pubg-battlegrounds',
    'Fortnite' => 'fortnite',
    'Roblox' => 'roblox',
    'Minecraft' => 'minecraft',
    'League of Legends' => 'league-of-legends',
    'Valorant' => 'valorant',
    'Apex Legends' => 'apex-legends',
    'Grand Theft Auto V' => 'grand-theft-auto-v',
    'Overwatch 2' => 'overwatch-2',
    'Rocket League' => 'rocket-league',
    'Genshin Impact' => 'genshin-impact',
    'Naraka: Bladepoint' => 'naraka-bladepoint',
    'Call of Duty' => 'call-of-duty-modern-warfare-3'
];

$mh = curl_multi_init();
$curl_handles = [];

// 1. Prepare Requests
foreach ($games as $name => $slug) {
    $ch = curl_init();
    $url = "https://activeplayer.io/$slug/";
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 6); // Increased timeout to 6s
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    // Mimic Real Browser Headers to bypass simple WAF/Bot detection
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
        'Accept-Language: en-US,en;q=0.9',
        'Upgrade-Insecure-Requests: 1',
        'Referer: https://www.google.com/',
        'Cache-Control: no-cache'
    ]);
    
    curl_multi_add_handle($mh, $ch);
    $curl_handles[$name] = $ch;
}

// 2. Execute Parallel
$running = null;
do {
    curl_multi_exec($mh, $running);
} while ($running);

$response = [];

// 3. Process Results
foreach ($curl_handles as $name => $ch) {
    $html = curl_multi_getcontent($ch);
    $info = curl_getinfo($ch);
    
    $count = 0;

    if ($info['http_code'] == 200 && $html) {
        // Updated Regex for ActivePlayer structure
        // Usually: <h3 class="...">1,492,000</h3> ... <p>Current Players</p>
        // Or: <span id="live-count">...
        
        // Pattern 1: Number inside H3 followed eventually by "Current Players"
        if (preg_match('/<h3[^>]*>([\d,]+)<\/h3>\s*(?:<[^>]+>\s*)*Current Players/is', $html, $matches)) {
             $count = (int)str_replace(',', '', $matches[1]);
        }
        // Pattern 2: "Current Players" followed by number (Unlikely, but fallback)
        elseif (preg_match('/Current Players.*?([\d,]+)/is', $html, $matches)) {
             $count = (int)str_replace(',', '', $matches[1]);
        }
        // Pattern 3: ID-based (very common)
        elseif (preg_match('/id="live-count[^"]*">([\d,]+)</', $html, $matches)) {
             $count = (int)str_replace(',', '', $matches[1]);
        }
        // Pattern 4: Generic localized number detection close to "Current Players" text (within 100 chars)
        else {
             // Find position of "Current Players"
             $pos = stripos($html, 'Current Players');
             if ($pos !== false) {
                 // Look backwards 200 chars
                 $snippet = substr($html, max(0, $pos - 200), 200);
                 if (preg_match_all('/>([\d,]{4,})</', $snippet, $m)) {
                     // Get the last match (closest to label)
                     $last = end($m[1]);
                     $count = (int)str_replace(',', '', $last);
                 }
             }
        }
    }
    
    if ($count > 0) {
        $response[$name] = $count;
    }
    
    curl_multi_remove_handle($mh, $ch);
    curl_close($ch);
}
curl_multi_close($mh);

echo json_encode($response);
?>
