<?php
session_start();
require_once __DIR__ . '/config/db.php';

if (isset($_REQUEST['id'], $_REQUEST['action'])) {
    $id = (int)$_REQUEST['id'];
    $action = $_REQUEST['action'];
    
    // Fetch current stock from database
    $stmt = $pdo->prepare("SELECT stock FROM kd_products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($product) {
        $current_stock = (int)$product['stock'];
        $current_cart_qty = $_SESSION['cart'][$id] ?? 0;
        
        if (isset($_SESSION['cart'][$id])) {
            if ($action === 'increase') {
                // Check if we have enough stock to increase
                if ($current_stock <= 0) {
                    header('Location: cart.php');
                    exit;
                }
                $_SESSION['cart'][$id]++;
                // Decrement stock in DB
                $pdo->prepare("UPDATE kd_products SET stock = stock - 1 WHERE id = ?")->execute([$id]);
            } else if ($action === 'decrease') {
                $_SESSION['cart'][$id]--;
                // Restore stock in DB
                $pdo->prepare("UPDATE kd_products SET stock = stock + 1 WHERE id = ?")->execute([$id]);
                if ($_SESSION['cart'][$id] <= 0) {
                    unset($_SESSION['cart'][$id]);
                }
            } else if ($action === 'set' && isset($_REQUEST['qty'])) {
                $qty = (int)$_REQUEST['qty'];
                $diff = $qty - $current_cart_qty;
                if ($qty > 0) {
                    if ($diff > 0) {
                        // Check if we have enough stock
                        if ($diff > $current_stock) {
                            header('Location: cart.php');
                            exit;
                        }
                        // Decrement stock
                        $pdo->prepare("UPDATE kd_products SET stock = stock - ? WHERE id = ?")->execute([$diff, $id]);
                    } elseif ($diff < 0) {
                        // Restore stock
                        $pdo->prepare("UPDATE kd_products SET stock = stock + ? WHERE id = ?")->execute([abs($diff), $id]);
                    }
                    $_SESSION['cart'][$id] = $qty;
                } else {
                    // Restore all stock
                    $pdo->prepare("UPDATE kd_products SET stock = stock + ? WHERE id = ?")->execute([$current_cart_qty, $id]);
                    unset($_SESSION['cart'][$id]);
                }
            }
        }
    }
}

// Redirect back to cart page
header('Location: cart.php');
exit;
