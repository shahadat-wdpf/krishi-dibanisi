<?php
require_once __DIR__ . '/config/db.php';
try {
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables: " . implode(', ', $tables);
} catch(Exception $e) {
    echo $e->getMessage();
}
