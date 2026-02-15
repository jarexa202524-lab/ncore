<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
require_once '../db.php';
$stmt = $pdo->query("SELECT * FROM users ");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <title>NCORE ADMIN | DASHBOARD</title>
    <link href="css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="scanlines"></div>
    <div class="admin-sidebar">
        <div class="admin-logo">NCORE <span style="color:var(--primary)">SYSTEM</span></div>
        <a href="index.php" class="nav-item active">DASHBOARD</a>
        <a href="manage_news.php" class="nav-item">NEWS</a>
        <a href="manage_users.php" class="nav-item">USERS</a>
        <a href="logout.php" class="nav-item logout-btn">LOGOUT</a>
    </div>
    <div class="admin-content">
        <div class="page-header">
            <h1>DASHBOARD</h1>
        </div>
        <div class="grid">
            <div class="card">
                <h3>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></h3>
                <p>System Status: ONLINE</p>
            </div>
            <!-- More stats here -->
        </div>
    </div>
</body>
</html>
