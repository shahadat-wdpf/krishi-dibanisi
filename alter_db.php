<?php
require_once __DIR__ . '/config/db.php';
try {
    $pdo->exec("ALTER TABLE kd_orders ADD COLUMN payment_number VARCHAR(20) DEFAULT NULL, ADD COLUMN trx_id VARCHAR(100) DEFAULT NULL");
    echo "Success — kd_orders table আপডেট হয়েছে।";
} catch(Exception $e) {
    echo $e->getMessage();
}
