# NCORE.GE FTP Upload Script
# Upload index.html to hosting

$ftpHost = "ftp://185.229.111.17"
$ftpUser = "ncorege"
$ftpPass = "Chemiyle242424@"
$localFile = "C:\Users\shark\.gemini\antigravity\scratch\ncore\index.html"
$remoteFile = "/httpdocs/index.html"

Write-Host "================================" -ForegroundColor Cyan
Write-Host "  NCORE.GE FTP Upload Script" -ForegroundColor Green
Write-Host "================================" -ForegroundColor Cyan
Write-Host ""

# Check if file exists
if (-Not (Test-Path $localFile)) {
    Write-Host "ERROR: Local file not found: $localFile" -ForegroundColor Red
    exit 1
}

Write-Host "Local file: $localFile" -ForegroundColor Yellow
Write-Host "FTP Server: $ftpHost" -ForegroundColor Yellow
Write-Host "Remote path: $remoteFile" -ForegroundColor Yellow
Write-Host ""

try {
    # Create FTP request
    $ftpUri = "$ftpHost$remoteFile"
    $request = [System.Net.FtpWebRequest]::Create($ftpUri)
    $request.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile
    $request.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
    $request.UseBinary = $true
    $request.KeepAlive = $false

    Write-Host "Uploading file..." -ForegroundColor Cyan
    
    # Read file content
    $fileContent = [System.IO.File]::ReadAllBytes($localFile)
    $fileSize = $fileContent.Length
    Write-Host "File size: $($fileSize / 1024) KB" -ForegroundColor Yellow
    
    # Upload
    $requestStream = $request.GetRequestStream()
    $requestStream.Write($fileContent, 0, $fileContent.Length)
    $requestStream.Close()
    
    # Get response
    $response = $request.GetResponse()
    Write-Host ""
    Write-Host "SUCCESS! File uploaded successfully!" -ForegroundColor Green
    Write-Host "Status: $($response.StatusDescription)" -ForegroundColor Green
    $response.Close()
    
    Write-Host ""
    Write-Host "Visit: http://ncore.ge" -ForegroundColor Cyan
    Write-Host ""
    
}
catch {
    Write-Host ""
    Write-Host "ERROR: Upload failed!" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
    Write-Host ""
    exit 1
}
