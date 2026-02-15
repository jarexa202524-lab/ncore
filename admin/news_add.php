<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
require_once '../db.php';

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category = $_POST['category'];
    $image_url = trim($_POST['image_url']);
    
    // Auto-generate slug
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

    if ($title && $content) {
        $stmt = $pdo->prepare("INSERT INTO news (title, slug, content, category, image_url) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$title, $slug, $content, $category, $image_url])) {
            $msg = "<div class='msg success'>Article Published Successfully!</div>";
        } else {
            $msg = "<div class='msg error'>Error Publishing Article.</div>";
        }
    } else {
        $msg = "<div class='msg error'>Title and Content are required.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <title>NCORE ADMIN | ADD NEWS</title>
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
            <h1>CREATE NEW ARTICLE</h1>
            <a href="manage_news.php" class="create-btn">BACK</a>
        </div>

        <?php echo $msg; ?>

        <div class="form-container">
            <form method="POST">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" required>
                </div>
                
                <div class="form-group">
                    <label>Category</label>
                    <select name="category">
                        <option value="news">News</option>
                        <option value="tech">Tech</option>
                        <option value="games">Games</option>
                        <option value="reviews">Reviews</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Image URL</label>
                    <input type="text" name="image_url" placeholder="https://...">
                </div>

                <div class="form-group">
                    <label>Content (HTML Supported)</label>
                    <textarea name="content" rows="10" required></textarea>
                </div>

                <button type="submit" class="create-btn" style="width:100%">PUBLISH</button>
            </form>
        </div>
    </div>
</body>
</html>
