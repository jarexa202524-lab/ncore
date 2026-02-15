$ftpServer = "185.229.111.17"
$ftpUsername = "ncore"
$ftpPassword = "Ncoreuser123!"
$localFile = "C:\Users\shark\.gemini\antigravity\scratch\ncore\get_live_stats_v2.php"
$remoteFile = "/get_live_stats_v2.php"

$webclient = New-Object System.Net.WebClient
$webclient.Credentials = New-Object System.Net.NetworkCredential($ftpUsername, $ftpPassword)
$uri = New-Object System.Uri("ftp://$ftpServer/$remoteFile")
$webclient.UploadFile($uri, $localFile)

Write-Host "Uploaded get_live_stats_v2.php"
