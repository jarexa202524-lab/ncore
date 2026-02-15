<?php
// get_top_sellers.php - Scrapes Steam Global Top Sellers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache, must-revalidate');

// Steam Search URL for Top Sellers (Global)
$url = "https://store.steampowered.com/search/?filter=globaltopsellers&os=win&ignore_preferences=1";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$html = curl_exec($ch);
curl_close($ch);

$games = [];

if ($html) {
    // Regex to extract game data from search rows
    // We look for class="search_result_row"
    // Capture: Link, AppID, Image, Title
    // Note: Steam Deck or Hardware might appear, we can filter by AppID length or context if needed, but usually Top Sellers includes hardware.
    
    // Pattern breakdown:
    // <a href="(url)" ... class="search_result_row" ... data-ds-appid="(id)">
    // ... <img src="(img_url)"> ...
    // ... <span class="title">(title)</span> ...
    
    $pattern = '/<a href="([^"]+)"[^>]*class="search_result_row[^>]*data-ds-appid="([^"]*)"[^>]*>.*?<img src="([^"]+)".*?<span class="title">([^<]+)<\/span>/is';
    
    preg_match_all($pattern, $html, $matches, PREG_SET_ORDER);
    
    $count = 0;
    foreach ($matches as $match) {
        if ($count >= 15) break;
        
        // Steam Image often has 1x and 2x in srcset, we grab src which is usually small capsule.
        // We can upgrade image to header image by ID.
        $appId = $match[2];
        // Only valid apps (exclude bundles often having weird IDs or multiple IDs)
        if (strpos($appId, ',') !== false) {
             $appId = explode(',', $appId)[0];
        }

        $imageUrl = "https://cdn.akamai.steamstatic.com/steam/apps/$appId/header.jpg";
        // Fallback if no ID (hardware?)
        if (!$appId) $imageUrl = $match[3];

        $games[] = [
            'rank' => $count + 1,
            'link' => $match[1],
            'id' => $appId,
            'image_capsule' => $match[3], // Small
            'image_header' => $imageUrl, // Large
            'name' => trim($match[4])
        ];
        $count++;
    }
}

echo json_encode($games);
?>
