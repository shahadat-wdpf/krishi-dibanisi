<?php
session_start();
require_once __DIR__ . '/config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    
    if ($product_id > 0) {
        $stmt = $pdo->prepare("SELECT stock FROM kd_products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'পণ্য পাওয়া যায়নি।']);
            exit;
        }
        
        if ($product['stock'] <= 0) {
            echo json_encode(['success' => false, 'message' => 'পণ্যটি স্টক অতিক্রম হয়েছে।']);
            exit;
        }
        
        // Check if adding would exceed stock
        $current_cart_qty = $_SESSION['cart'][$product_id] ?? 0;
        if ($current_cart_qty + 1 > $product['stock']) {
            echo json_encode(['success' => false, 'message' => 'আরও পণ্য যোগ করা যায় না। স্টক অতিক্রম।']);
            exit;
        }
        
        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        // Add or increment quantity
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]++;
        } else {
            $_SESSION['cart'][$product_id] = 1;
        }
        
        // Decrement stock in database
        $stmt_update = $pdo->prepare("UPDATE kd_products SET stock = stock - 1 WHERE id = ?");
        $stmt_update->execute([$product_id]);
        
        // Calculate total items in cart
        $total_items = array_sum($_SESSION['cart']);
        
        echo json_encode(['success' => true, 'total_items' => $total_items]);
    } else {
        echo json_encode(['success' => false, 'message' => 'অবৈধ পণ্য আইডি।']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'অবৈধ রিকোয়েস্ট।']);
}
