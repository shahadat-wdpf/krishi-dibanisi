<?php
session_start();
require_once __DIR__ . '/config/db.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if (isset($_SESSION['cart'][$id])) {
        $qty = $_SESSION['cart'][$id];
        // Restore stock when removing from cart
        $pdo->prepare("UPDATE kd_products SET stock = stock + ? WHERE id = ?")->execute([$qty, $id]);
        unset($_SESSION['cart'][$id]);
    }
}
header('Location: cart.php');
exit;
?>
