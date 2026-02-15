$ftpHost = "ftp://185.229.111.17"
$ftpUser = "ncorege"
$ftpPass = "Chemiyle242424@"
$localFile = "C:\Users\shark\.gemini\antigravity\scratch\ncore\get_live_stats_v2.php"
$remoteFile = "/httpdocs/get_live_stats_v2.php"

try {
    $webclient = New-Object System.Net.WebClient
    $webclient.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
    
    Write-Host "Uploading get_live_stats_v2.php..."
    $uri = New-Object System.Uri($ftpHost + $remoteFile)
    $webclient.UploadFile($uri, $localFile)
    
    Write-Host "SUCCESS! File uploaded successfully!"
}
catch {
    Write-Error "Upload failed: $_"
}
