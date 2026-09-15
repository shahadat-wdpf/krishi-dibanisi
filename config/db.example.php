
<?php
// config/db.example.php
// Copy this file to db.php and fill in your own credentials

$host = '127.0.0.1';
$db   = 'krishi_dibanisi';
$user = 'root';
$pass = ''; // Set your MySQL password here
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
    die("Database connection failed. Please ensure MySQL is running and the database is configured.");
}