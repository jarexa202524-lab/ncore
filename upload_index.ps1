$ftpHost = "ftp://185.229.111.17"
$ftpUser = "ncorege"
$ftpPass = "Chemiyle242424@"
$localFile = "C:\Users\shark\.gemini\antigravity\scratch\ncore\index.html"
$remoteFile = "/httpdocs/index.html"

try {
    $webclient = New-Object System.Net.WebClient
    $webclient.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
    
    Write-Host "Uploading index.html..."
    $uri = New-Object System.Uri($ftpHost + $remoteFile)
    $webclient.UploadFile($uri, $localFile)
    
    Write-Host "SUCCESS! File uploaded successfully!"
}
catch {
    Write-Error "Upload failed: $_"
}
