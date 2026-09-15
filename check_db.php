<?php
require_once __DIR__ . '/config/db.php';
try {
    $stmt = $pdo->query("DESCRIBE orders");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch(Exception $e) {
    echo $e->getMessage();
}
