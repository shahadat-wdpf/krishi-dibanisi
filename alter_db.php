<?php
require_once __DIR__ . '/config/db.php';
try {
    $pdo->exec("ALTER TABLE orders ADD COLUMN payment_number VARCHAR(20) DEFAULT NULL, ADD COLUMN trx_id VARCHAR(100) DEFAULT NULL");
    echo "Success";
} catch(Exception $e) {
    echo $e->getMessage();
}
