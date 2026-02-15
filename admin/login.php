<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../db.php';

session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "ყველა ველი სავალდებულოა!";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header('Location: index.php');
            exit;
        } else {
            $error = "არასწორი მომხმარებელი ან პაროლი.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <title>NCORE ADMIN | ACCESS CONTROL</title>
    <link href="css/admin.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            width: 400px;
            padding: 3rem;
            background: var(--panel);
            border: 1px solid var(--primary);
            text-align: center;
            box-shadow: 0 0 30px rgba(0, 255, 136, 0.2);
        }
        .login-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(90deg, transparent, var(--primary), transparent);
            border: 1px solid var(--primary);
            color: #000;
            font-family: 'Orbitron', sans-serif;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 3px;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 1rem;
        }
        .login-btn:hover {
            background: var(--primary);
            box-shadow: 0 0 30px var(--primary);
        }
    </style>
</head>
<body>
    <div class="scanlines"></div>
    <div class="login-container">
        <h1>ACCESS <span style="color:var(--primary)">CONTROL</span></h1>
        
        <?php if ($error): ?>
            <div class="msg error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <input type="text" name="username" placeholder="USER_ID" required>
            </div>
            
            <div class="form-group">
                <input type="password" name="password" placeholder="SECURITY_KEY" required>
            </div>

            <button type="submit" class="login-btn">INITIALIZE</button>
        </form>
    </div>
</body>
</html>
