<?php
session_start();
require_once __DIR__ . '/../config/db.php';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Krishi Dibanishi - খাঁটি গ্রামীণ পণ্য</title>
    <!-- Modern Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Scoped Navbar CSS -->
    <link rel="stylesheet" href="assets/css/navbar.css">
    <!-- Scoped Navbar JS -->
    <script src="assets/js/navbar.js" defer></script>
</head>
<body>

<header class="kd-navbar">
    <div class="kd-navbar-container">
        <!-- Brand Logo (Left) -->
        <a href="index.php" class="kd-nav-brand">
            <i class="fa-solid fa-leaf kd-brand-icon"></i>
            <div class="kd-brand-text">
                <span class="kd-brand-name-top">Krishi</span>
                <span class="kd-brand-name-bottom">Dibanishi</span>
            </div>
        </a>

        <!-- Desktop Navigation Links (Middle) -->
        <ul class="kd-nav-links">
            <li class="kd-nav-item">
                <a href="index.php" class="kd-nav-link <?= ($current_page == 'index.php') ? 'kd-active' : '' ?>">হোম (Home)</a>
            </li>
            <li class="kd-nav-item">
                <a href="products.php" class="kd-nav-link <?= ($current_page == 'products.php') ? 'kd-active' : '' ?>">পণ্য (Products)</a>
            </li>
            <li class="kd-nav-item">
                <a href="about.php" class="kd-nav-link <?= ($current_page == 'about.php') ? 'kd-active' : '' ?>">আমাদের সম্পর্কে (About)</a>
            </li>
            <li class="kd-nav-item">
                <a href="blog.php" class="kd-nav-link <?= ($current_page == 'blog.php' || $current_page == 'blog_detail.php') ? 'kd-active' : '' ?>">ব্লগ (Blog)</a>
            </li>
            <li class="kd-nav-item">
                <a href="contact.php" class="kd-nav-link <?= ($current_page == 'contact.php') ? 'kd-active' : '' ?>">যোগাযোগ (Contact)</a>
            </li>
        </ul>

        <!-- Right Side Actions (Cart + Auth Buttons + Mobile Hamburger) -->
        <div class="kd-nav-actions">
            <!-- Cart Icon -->
            <a href="cart.php" class="kd-cart-icon" aria-label="Shopping Cart">
                <i class="fa-solid fa-cart-shopping"></i>
                <?php
                    $cart_count = 0;
                    if(isset($_SESSION['cart'])) {
                        $cart_count = array_sum($_SESSION['cart']);
                    }
                ?>
                <span class="kd-cart-count"><?= $cart_count ?></span>
            </a>

            <!-- Desktop Auth Buttons -->
            <div class="kd-auth-buttons">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php" class="kd-btn kd-btn-outline"><i class="fa-regular fa-user"></i> অ্যাকাউন্ট</a>
                    <a href="logout.php" class="kd-btn kd-btn-primary">লগআউট</a>
                <?php else: ?>
                    <a href="login.php" class="kd-btn kd-btn-outline">লগইন</a>
                    <a href="register.php" class="kd-btn kd-btn-primary">রেজিস্টার</a>
                <?php endif; ?>
            </div>

            <!-- Animated Hamburger Toggle Button (Mobile: max-width 900px) -->
            <button class="kd-hamburger" id="kdHamburgerBtn" aria-label="Toggle Navigation Menu" aria-expanded="false">
                <span class="kd-hamburger-bar"></span>
                <span class="kd-hamburger-bar"></span>
                <span class="kd-hamburger-bar"></span>
            </button>
        </div>
    </div>

    <!-- Mobile Slide-Down Menu (Below 900px) -->
    <div class="kd-mobile-menu" id="kdMobileMenu">
        <ul class="kd-mobile-nav-links">
            <li>
                <a href="index.php" class="kd-mobile-nav-link <?= ($current_page == 'index.php') ? 'kd-active' : '' ?>"><i class="fa-solid fa-house"></i> হোম (Home)</a>
            </li>
            <li>
                <a href="products.php" class="kd-mobile-nav-link <?= ($current_page == 'products.php') ? 'kd-active' : '' ?>"><i class="fa-solid fa-store"></i> পণ্য (Products)</a>
            </li>
            <li>
                <a href="about.php" class="kd-mobile-nav-link <?= ($current_page == 'about.php') ? 'kd-active' : '' ?>"><i class="fa-solid fa-circle-info"></i> আমাদের সম্পর্কে (About)</a>
            </li>
            <li>
                <a href="blog.php" class="kd-mobile-nav-link <?= ($current_page == 'blog.php' || $current_page == 'blog_detail.php') ? 'kd-active' : '' ?>"><i class="fa-solid fa-newspaper"></i> ব্লগ (Blog)</a>
            </li>
            <li>
                <a href="contact.php" class="kd-mobile-nav-link <?= ($current_page == 'contact.php') ? 'kd-active' : '' ?>"><i class="fa-solid fa-envelope"></i> যোগাযোগ (Contact)</a>
            </li>
        </ul>
        
        <!-- Mobile Auth Buttons -->
        <div class="kd-mobile-auth-actions">
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php" class="kd-btn kd-btn-outline kd-btn-full"><i class="fa-regular fa-user"></i> অ্যাকাউন্ট</a>
                <a href="logout.php" class="kd-btn kd-btn-primary kd-btn-full">লগআউট</a>
            <?php else: ?>
                <a href="login.php" class="kd-btn kd-btn-outline kd-btn-full">লগইন</a>
                <a href="register.php" class="kd-btn kd-btn-primary kd-btn-full">রেজিস্টার</a>
            <?php endif; ?>
        </div>
    </div>
</header>
