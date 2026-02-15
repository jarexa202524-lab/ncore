$ftpHost = "ftp://185.229.111.17"
$ftpUser = "ncorege"
$ftpPass = "Chemiyle242424@"
$webclient = New-Object System.Net.WebClient
$webclient.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)

$files = @("get_live_stats_v3.php", "index.html")
$dirs = @("/", "/httpdocs/", "/public_html/", "/www/", "/htdocs/")

foreach ($file in $files) {
    $localPath = "C:\Users\shark\.gemini\antigravity\scratch\ncore\$file"
    foreach ($dir in $dirs) {
        $remoteUri = "$ftpHost$dir$file"
        try {
            Write-Host "Uploading $file to $remoteUri..."
            $webclient.UploadFile($remoteUri, $localPath)
            Write-Host "Success."
        }
        catch {
            Write-Host "Failed to $dir : $_"
        }
    }
}
