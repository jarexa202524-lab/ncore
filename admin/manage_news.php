<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
require_once '../db.php';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: manage_news.php?msg=deleted');
    exit;
}

// Fetch News
$stmt = $pdo->query("SELECT * FROM news ORDER BY created_at DESC");
$news = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <title>NCORE ADMIN | NEWS</title>
    <link href="css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="scanlines"></div>
    <div class="admin-sidebar">
        <div class="admin-logo">NCORE <span style="color:var(--primary)">SYSTEM</span></div>
        <a href="index.php" class="nav-item">DASHBOARD</a>
        <a href="manage_news.php" class="nav-item active">NEWS</a>
        <a href="manage_users.php" class="nav-item">USERS</a>
        <a href="logout.php" class="nav-item logout-btn">LOGOUT</a>
    </div>

    <div class="admin-content">
        <div class="page-header">
            <h1>NEWS MANAGEMENT</h1>
            <a href="news_add.php" class="create-btn">+ NEW ARTICLE</a>
        </div>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
            <div class="msg success">Article deleted successfully.</div>
        <?php endif; ?>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Views</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($news as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['title']); ?></td>
                    <td><?php echo strtoupper($item['category']); ?></td>
                    <td style="color:var(--secondary)"><?php echo $item['views']; ?></td>
                    <td><?php echo date('Y-m-d', strtotime($item['created_at'])); ?></td>
                    <td>
                        <a href="news_edit.php?id=<?php echo $item['id']; ?>" class="action-btn edit">EDIT</a>
                        <a href="?delete=<?php echo $item['id']; ?>" class="action-btn delete" onclick="return confirm('Confirm Deletion?');">DELETE</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
