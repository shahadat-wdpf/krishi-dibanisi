<?php
session_start();
require_once __DIR__ . '/../config/db.php';
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Krishi Dibanishi - খাঁটি গ্রামীণ পণ্য</title>
    <!-- Modern Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="navbar">
    <a href="index.php" class="nav-brand">
        <i class="fa-solid fa-leaf"></i> Krishi Dibanishi
    </a>
    
    <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Toggle Menu">
        <i class="fa-solid fa-bars"></i>
    </button>
    
    <ul class="nav-links">
        <li><a href="index.php">হোম (Home)</a></li>
        <li><a href="products.php">পণ্য (Products)</a></li>
        <li><a href="about.php">আমাদের সম্পর্কে (About)</a></li>
        <li><a href="blog.php">ব্লগ (Blog)</a></li>
        <li><a href="contact.php">যোগাযোগ (Contact)</a></li>
    </ul>
    
    <div class="nav-actions">
        <a href="cart.php" class="cart-icon">
            <i class="fa-solid fa-cart-shopping"></i>
            <?php
                $cart_count = 0;
                if(isset($_SESSION['cart'])) {
                    $cart_count = array_sum($_SESSION['cart']);
                }
            ?>
            <span class="cart-count"><?= $cart_count ?></span>
        </a>
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="dashboard.php" class="btn btn-outline"><i class="fa-regular fa-user"></i> অ্যাকাউন্ট</a>
            <a href="logout.php" class="btn">লগআউট</a>
        <?php else: ?>
            <a href="login.php" class="btn btn-outline">লগইন</a>
            <a href="register.php" class="btn">রেজিস্টার</a>
        <?php endif; ?>
    </div>
</nav>

<script>
// Mobile Menu Toggle Logic
document.addEventListener('DOMContentLoaded', () => {
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const navLinks = document.querySelector('.nav-links');
    
    if (mobileMenuBtn && navLinks) {
        mobileMenuBtn.addEventListener('click', function(e) {
            e.preventDefault();
            navLinks.classList.toggle('active');
            
            // Toggle Icon
            const icon = this.querySelector('i');
            if (navLinks.classList.contains('active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-xmark');
            } else {
                icon.classList.remove('fa-xmark');
                icon.classList.add('fa-bars');
            }
        });
    }
});
</script>
