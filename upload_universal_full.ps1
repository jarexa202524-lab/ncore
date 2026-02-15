$ftpHost = "ftp://185.229.111.17"
$ftpUser = "ncorege"
$ftpPass = "Chemiyle242424@"

# Function to create directory
function Create-FtpDirectory ($url) {
    try {
        $request = [System.Net.WebRequest]::Create($url)
        $request.Method = [System.Net.WebRequestMethods+Ftp]::MakeDirectory
        $request.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
        $response = $request.GetResponse()
        Write-Host "Created directory $url"
    }
    catch {
        # Ignore if exists
        # Write-Host "Directory likely exists: $url"
    }
}

# Function to upload file
function Upload-File ($localPath, $remotePath) {
    try {
        $webclient = New-Object System.Net.WebClient
        $webclient.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
        Write-Host "Uploading $localPath to $remotePath..."
        $webclient.UploadFile($remotePath, $localPath)
        Write-Host "Success."
    }
    catch {
        Write-Host "Failed to upload to $remotePath : $_"
    }
}

# Root files
$rootFiles = @("db.php", "login.php", "register.php", "test_db.php", "index.html", "get_live_stats_v3.php", "get_top_sellers.php", "test.html", "info.php")
# Admin files
$adminFiles = @("index.php", "login.php", "logout.php", "manage_news.php", "manage_users.php", "news_add.php", "news_edit.php", "setup.php", "user_add.php", "user_edit.php")
# Admin CSS files
$adminCssFiles = @("admin.css")

# Base directories on server (we try all)
$baseDirs = @("", "/httpdocs", "/public_html")

foreach ($baseDir in $baseDirs) {
    # 1. Upload Root Files
    foreach ($file in $rootFiles) {
        if (Test-Path ".\$file") {
            Upload-File ".\$file" "$ftpHost$baseDir/$file"
        }
    }

    # 2. Upload Admin Files
    # Create admin dir
    Create-FtpDirectory "$ftpHost$baseDir/admin"
    foreach ($file in $adminFiles) {
        if (Test-Path ".\admin\$file") {
            Upload-File ".\admin\$file" "$ftpHost$baseDir/admin/$file"
        }
    }

    # 3. Upload Admin CSS
    # Create css dir
    Create-FtpDirectory "$ftpHost$baseDir/admin/css"
    foreach ($file in $adminCssFiles) {
        if (Test-Path ".\admin\css\$file") {
            Upload-File ".\admin\css\$file" "$ftpHost$baseDir/admin/css/$file"
        }
    }
}
