$ftpHost = "ftp://185.229.111.17"
$ftpUser = "ncorege"
$ftpPass = "Chemiyle242424@"
$localFile = "C:\Users\shark\.gemini\antigravity\scratch\ncore\test_debug.php"
$remoteFile = "/httpdocs/test_debug.php"

try {
    $webclient = New-Object System.Net.WebClient
    $webclient.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
    
    Write-Host "Uploading test_debug.php to $ftpHost$remoteFile..."
    $uri = New-Object System.Uri($ftpHost + $remoteFile)
    $webclient.UploadFile($uri, $localFile)
    
    Write-Host "SUCCESS! File uploaded successfully!"
}
catch {
    Write-Error "Upload failed: $_"
}
