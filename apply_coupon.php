<?php
session_start();
require_once __DIR__ . '/config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$code = strtoupper(trim($_POST['coupon_code'] ?? ''));
$cart_items = $_SESSION['cart'] ?? [];

if (empty($code)) {
    echo json_encode(['success' => false, 'message' => 'কুপন কোড দিন']);
    exit;
}

if (empty($cart_items)) {
    echo json_encode(['success' => false, 'message' => 'কার্ট খালি']);
    exit;
}

// Calculate cart total to check against min_order_amount
$total_price = 0;
try {
    $placeholders = str_repeat('?,', count($cart_items) - 1) . '?';
    $ids = array_keys($cart_items);
    $stmt = $pdo->prepare("SELECT id, price FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $products = $stmt->fetchAll();
    
    foreach ($products as $p) {
        $total_price += ($p['price'] * $cart_items[$p['id']]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'ডাটাবেস ত্রুটি']);
    exit;
}

try {
    // Check coupon in DB
    $stmt = $pdo->prepare("SELECT * FROM coupons WHERE code = ? AND is_active = 1 LIMIT 1");
    $stmt->execute([$code]);
    $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$coupon) {
        echo json_encode(['success' => false, 'message' => 'কুপন কোডটি সঠিক নয় বা মেয়াদ শেষ।']);
        exit;
    }

    if ($total_price < $coupon['min_order_amount']) {
        echo json_encode(['success' => false, 'message' => 'এই কুপনটি ব্যবহার করতে ন্যূনতম ' . number_format($coupon['min_order_amount']) . ' টাকার বাজার করতে হবে।']);
        exit;
    }

    // Check First Purchase Logic
    if (isset($coupon['is_first_purchase']) && $coupon['is_first_purchase'] == 1) {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'প্রথমবার কেনাকাটার কুপন ব্যবহার করতে অনুগ্রহ করে লগইন করুন।']);
            exit;
        }
        
        $user_id = $_SESSION['user_id'];
        $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
        $check_stmt->execute([$user_id]);
        $order_count = $check_stmt->fetchColumn();
        
        if ($order_count > 0) {
            echo json_encode(['success' => false, 'message' => 'দুঃখিত, এই কুপনটি শুধুমাত্র নতুন গ্রাহকদের প্রথম অর্ডারের জন্য প্রযোজ্য।']);
            exit;
        }
    }

    // Calculate discount
    $discount_amount = 0;
    if ($coupon['type'] === 'fixed') {
        $discount_amount = $coupon['discount_value'];
    } elseif ($coupon['type'] === 'percentage') {
        $discount_amount = ($total_price * $coupon['discount_value']) / 100;
    }

    // Cannot have discount more than total price
    if ($discount_amount > $total_price) {
        $discount_amount = $total_price;
    }

    // Save in session
    $_SESSION['coupon'] = [
        'code' => $coupon['code'],
        'discount_amount' => $discount_amount
    ];

    $delivery_charge = isset($site_settings['delivery_charge']) ? (float)$site_settings['delivery_charge'] : 60;
    $grand_total = $total_price + $delivery_charge - $discount_amount;

    echo json_encode([
        'success' => true,
        'message' => 'কুপন সফলভাবে প্রয়োগ করা হয়েছে!',
        'discount_amount' => $discount_amount,
        'grand_total' => $grand_total
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'ডাটাবেস ত্রুটি: ' . $e->getMessage()]);
}
?>
