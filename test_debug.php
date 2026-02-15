<?php
// test_debug.php - Check if Server can see ActivePlayer contents
error_reporting(E_ALL);
$url = "https://activeplayer.io/counter-strike-2/";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
    'Accept-Language: en-US,en;q=0.9',
    'Upgrade-Insecure-Requests: 1'
]);

$html = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);

echo "HTTP Status: " . $info['http_code'] . "\n";
echo "URL: " . $info['url'] . "\n";
echo "Size: " . strlen($html) . "\n";

if ($html) {
    echo "Title: ";
    if (preg_match('/<title>(.*?)<\/title>/', $html, $m)) echo $m[1];
    echo "\n\n";
    
    // Check for numbers
    if (preg_match('/([\d,]+)\s*(?:<\/.*?>\s*)*Current Players/i', $html, $m)) {
        echo "MATCH FOUND: " . $m[1] . "\n";
    } else {
        echo "NO MATCH in snippet.\n";
    }
    
    // Dump Start
    echo "--- START OF HTML ---\n";
    echo htmlspecialchars(substr($html, 0, 1500));
} else {
    echo "NO HTML RETURNED";
}
?>
