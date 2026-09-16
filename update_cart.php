<?php
session_start();

if (isset($_REQUEST['id'], $_REQUEST['action'])) {
    $id = (int)$_REQUEST['id'];
    $action = $_REQUEST['action'];
    
    if (isset($_SESSION['cart'][$id])) {
        if ($action === 'increase') {
            $_SESSION['cart'][$id]++;
        } else if ($action === 'decrease') {
            $_SESSION['cart'][$id]--;
            if ($_SESSION['cart'][$id] <= 0) {
                unset($_SESSION['cart'][$id]);
            }
        } else if ($action === 'set' && isset($_REQUEST['qty'])) {
            $qty = (int)$_REQUEST['qty'];
            if ($qty > 0) {
                $_SESSION['cart'][$id] = $qty;
            } else {
                unset($_SESSION['cart'][$id]);
            }
        }
    }
}

// Redirect back to cart page
header('Location: cart.php');
exit;
