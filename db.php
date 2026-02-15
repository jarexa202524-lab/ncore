<?php
$host = 'localhost';
$db   = 'ncorege_corebuild';
$user = 'ncorege'; // Should be correct based on FTP
$pass = 'Chemiyle242424@'; // FTP pass
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Determine if we are in production or dev
    // For now, show error to debug. In prod, log it.
    die("Database Connection Failed: " . $e->getMessage());
}
?>
