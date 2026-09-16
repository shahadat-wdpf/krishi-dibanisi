<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    
    if ($product_id > 0) {
        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        // Add or increment quantity
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]++;
        } else {
            $_SESSION['cart'][$product_id] = 1;
        }
        
        // Calculate total items in cart
        $total_items = array_sum($_SESSION['cart']);
        
        echo json_encode(['success' => true, 'total_items' => $total_items]);
    } else {
        echo json_encode(['success' => false, 'message' => 'অবৈধ পণ্য আইডি।']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'অবৈধ রিকোয়েস্ট।']);
}
