<?php
require_once __DIR__ . '/config/db.php';

try {
    // Create kd_coupons table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `kd_coupons` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `code` varchar(50) NOT NULL,
        `type` enum('fixed', 'percentage') NOT NULL DEFAULT 'fixed',
        `discount_value` decimal(10,2) NOT NULL,
        `min_order_amount` decimal(10,2) DEFAULT '0.00',
        `is_first_purchase` tinyint(1) DEFAULT '0',
        `is_active` tinyint(1) DEFAULT '1',
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `code` (`code`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // Add coupon columns to kd_orders table if they don't exist
    $check_column = $pdo->query("SHOW COLUMNS FROM `kd_orders` LIKE 'coupon_code'");
    if ($check_column->rowCount() == 0) {
        $pdo->exec("ALTER TABLE `kd_orders` ADD COLUMN `coupon_code` varchar(50) DEFAULT NULL AFTER `payment_method`");
        $pdo->exec("ALTER TABLE `kd_orders` ADD COLUMN `discount_amount` decimal(10,2) DEFAULT '0.00' AFTER `coupon_code`");
    }

    // Insert sample coupons
    $pdo->exec("INSERT IGNORE INTO `kd_coupons` (`code`, `type`, `discount_value`, `min_order_amount`) VALUES 
        ('KRISHI10', 'percentage', 10.00, 500.00),
        ('DISCOUNT50', 'fixed', 50.00, 200.00)");

    echo "Successfully updated database tables and inserted sample coupons (kd_ prefix).\n";

} catch (Exception $e) {
    echo "Error updating database: " . $e->getMessage() . "\n";
}
?>
