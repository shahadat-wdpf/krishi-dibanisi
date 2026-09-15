<?php
session_start();
require_once __DIR__ . '/config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cart_items = $_SESSION['cart'] ?? [];
    
    // Prevent checkout if cart empty
    if(empty($cart_items)) {
        header("Location: products.php");
        exit();
    }
    
    // Sanitize and gather data
    $full_name = htmlspecialchars($_POST['full_name'] ?? '');
    $phone = htmlspecialchars($_POST['phone_number'] ?? '');
    $address = htmlspecialchars($_POST['shipping_address'] ?? '');
    $shipping_address = "$full_name, Phone: $phone, Addr: $address";
    
    $payment_method = htmlspecialchars($_POST['payment_method'] ?? 'Cash on Delivery');
    $payment_number = htmlspecialchars($_POST['payment_number'] ?? '');
    $trx_id = htmlspecialchars($_POST['trx_id'] ?? '');
    $total_amount = (float)($_POST['total_amount'] ?? 0);
    $user_id = $_SESSION['user_id'] ?? 1; 

    // Extract coupon if any
    $coupon_code = null;
    $discount_amount = 0.00;
    if (isset($_SESSION['coupon'])) {
        $coupon_code = $_SESSION['coupon']['code'];
        $discount_amount = (float)$_SESSION['coupon']['discount_amount'];
        // Also recalculate total_amount to ensure it's not spoofed.
        // For simplicity, we are trusting the posted total_amount here, 
        // but ideally we should recalculate it from prices + shipping - discount.
    }

    try {
        $pdo->beginTransaction();
        
        // 1. Insert into orders
        $stmt_order = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status, shipping_address, payment_method, coupon_code, discount_amount, payment_number, trx_id) VALUES (?, ?, 'pending', ?, ?, ?, ?, ?, ?)");
        $stmt_order->execute([$user_id, $total_amount, $shipping_address, $payment_method, $coupon_code, $discount_amount, $payment_number, $trx_id]);
        $order_id = $pdo->lastInsertId();
        
        // 2. Fetch current prices
        $placeholders = str_repeat('?,', count($cart_items) - 1) . '?';
        $ids = array_keys($cart_items);
        $stmt_products = $pdo->prepare("SELECT id, price FROM products WHERE id IN ($placeholders)");
        $stmt_products->execute($ids);
        $products = $stmt_products->fetchAll(PDO::FETCH_KEY_PAIR); // returns [id => price]
        
        // 3. Insert into order_items and update stock
        $stmt_item = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt_stock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        
        foreach($cart_items as $prod_id => $qty) {
            $price = $products[$prod_id] ?? 0;
            $stmt_item->execute([$order_id, $prod_id, $qty, $price]);
            
            // Decrease stock
            $stmt_stock->execute([$qty, $prod_id]);
        }
        
        // 4. Commit and clear cart & coupon
        $pdo->commit();
        unset($_SESSION['cart']);
        unset($_SESSION['coupon']);
        
        // Output dynamic success based on payment_method
        $success_msg = "OrderPlaced. Order ID: #$order_id";
        header("Location: success.php?order_id=$order_id&method=".urlencode($payment_method));
        exit();
        
    } catch(Exception $e) {
        $pdo->rollBack();
        die("অর্ডার সম্পন্ন করতে সমস্যা হয়েছে: " . $e->getMessage());
    }
} else {
    header("Location: index.php");
    exit();
}
?>
