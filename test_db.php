<?php
$host = 'localhost';
$db   = 'ncorege_corebuild';
$user = 'ncorege'; // Trying FTP user
$pass = 'Chemiyle242424@'; // Trying FTP pass
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "<h1>Database Connection Successful!</h1>";
    echo "<p>Connected to <strong>$db</strong> as <strong>$user</strong>.</p>";
    
    // Check if tables exist
    $tables = $pdo->query("SHOW TABLES")->fetchAll();
    echo "<h3>Tables:</h3><ul>";
    foreach ($tables as $table) {
        echo "<li>" . reset($table) . "</li>";
    }
    echo "</ul>";

} catch (\PDOException $e) {
    echo "<h1>Connection Failed</h1>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>User used: $user</p>";
}
?>
