<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT);
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetchColumn() > 0) {
        $error = "Username already exists.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'editor')");
        if ($stmt->execute([$username, $password])) {
            header('Location: login.php');
            exit;
        } else {
            $error = "Registration failed.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <title>NCORE | REGISTER</title>
    <style>
        body { background: #000; color: #fff; font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .box { padding: 40px; border: 1px solid #00ff88; text-align: center; }
        input {display: block; margin: 10px 0; padding: 10px; width: 100%;}
        button { background: #00ff88; border: none; padding: 10px 20px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
    <div class="box">
        <h2>REGISTER</h2>
        <?php if ($error) echo "<p style='color:red'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">SIGN UP</button>
        </form>
    </div>
</body>
</html>
