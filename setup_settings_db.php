<?php
require_once __DIR__ . '/config/db.php';
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(50) UNIQUE NOT NULL,
            setting_value TEXT
        )
    ");
    
    $settings = [
        ['site_name', 'কৃষি দিবানিশি'],
        ['contact_email', 'info@krishidibanisi.com'],
        ['contact_phone', '+880 171XXXXXXX'],
        ['contact_address', 'ঢাকা, বাংলাদেশ'],
        ['bkash_number', '+880 171XXXXXXX'],
        ['delivery_charge', '60'],
        ['facebook_url', '#'],
        ['youtube_url', '#']
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES (?, ?)");
    foreach($settings as $s) {
        $stmt->execute($s);
    }
    echo "Setup Complete.";
} catch(Exception $e) {
    echo $e->getMessage();
}
