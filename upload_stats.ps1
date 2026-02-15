
$ftpHost = "ftp://185.229.111.17"
$ftpUser = "ncorege"
$ftpPass = "Chemiyle242424@"
$localFile = "C:\Users\shark\.gemini\antigravity\scratch\ncore\stats_demo.html"
$remoteFile = "/httpdocs/stats_demo.html"

try {
    $webclient = New-Object System.Net.WebClient
    $webclient.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
    
    Write-Host "Uploading file..."
    $uri = New-Object System.Uri($ftpHost + $remoteFile)
    $webclient.UploadFile($uri, $localFile)
    
    Write-Host "SUCCESS! File uploaded successfully!"
    Write-Host "Visit: http://ncore.ge/stats_demo.html"
}
catch {
    Write-Error "Upload failed: $_"
}
