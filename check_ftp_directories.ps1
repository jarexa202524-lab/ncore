# Check FTP Directories
$ftpHost = "ftp://185.229.111.17"
$ftpUser = "ncorege"
$ftpPass = "Chemiyle242424@"

Write-Host "Checking FTP server directories..." -ForegroundColor Cyan
Write-Host ""

try {
    $request = [System.Net.FtpWebRequest]::Create("$ftpHost/")
    $request.Method = [System.Net.WebRequestMethods+Ftp]::ListDirectoryDetails
    $request.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
    
    $response = $request.GetResponse()
    $stream = $response.GetResponseStream()
    $reader = New-Object System.IO.StreamReader($stream)
    
    Write-Host "Files and directories in root (/):" -ForegroundColor Yellow
    Write-Host "-----------------------------------" -ForegroundColor Gray
    
    while ($line = $reader.ReadLine()) {
        Write-Host $line
    }
    
    $reader.Close()
    $response.Close()
    
    Write-Host ""
    Write-Host "Done!" -ForegroundColor Green
    
}
catch {
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
}
