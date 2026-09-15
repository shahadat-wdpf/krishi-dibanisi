<?php
require_once __DIR__ . '/config/db.php';

echo "<h2>Database Setup: Creating 'messages' table...</h2>";

try {
    $sql = "CREATE TABLE IF NOT EXISTS `messages` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `email` varchar(100) NOT NULL,
        `message` text NOT NULL,
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $pdo->exec($sql);
    echo "<h3>Success! The 'messages' table has been created.</h3>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
