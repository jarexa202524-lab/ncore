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
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: manage_users.php?msg=deleted');
    exit;
}

// Fetch Users
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <title>NCORE ADMIN | USERS</title>
    <link href="css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="scanlines"></div>
    <div class="admin-sidebar">
        <div class="admin-logo">NCORE <span style="color:var(--primary)">SYSTEM</span></div>
        <a href="index.php" class="nav-item">DASHBOARD</a>
        <a href="manage_news.php" class="nav-item">NEWS</a>
        <a href="manage_users.php" class="nav-item active">USERS</a>
        <a href="logout.php" class="nav-item logout-btn">LOGOUT</a>
    </div>

    <div class="admin-content">
        <div class="page-header">
            <h1>USER MANAGEMENT</h1>
            <a href="user_add.php" class="create-btn">+ NEW USER</a>
        </div>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
            <div class="msg success">User deleted successfully.</div>
        <?php endif; ?>

        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td>#<?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td>
                        <span style="color: <?php echo $user['role'] == 'admin' ? 'var(--primary)' : 'var(--secondary)'; ?>">
                            <?php echo strtoupper($user['role']); ?>
                        </span>
                    </td>
                    <td><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
                    <td>
                        <a href="user_edit.php?id=<?php echo $user['id']; ?>" class="action-btn edit">EDIT</a>
                        <?php if ($user['username'] !== 'admin'): ?>
                            <a href="?delete=<?php echo $user['id']; ?>" class="action-btn delete" onclick="return confirm('Confirm Deletion?');">DELETE</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
