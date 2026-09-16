<?php
ob_start();
session_start();
require_once __DIR__ . '/../../config/db.php';

// Enforce Admin Auth
if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Krishi Dibanisi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-color: #061910;
            --bg-darker: #04120b;
            --card-bg: rgba(12, 38, 26, 0.75);
            --card-hover: rgba(18, 54, 38, 0.85);
            --glass-border: rgba(255, 255, 255, 0.08);
            --glass-border-light: rgba(255, 255, 255, 0.15);
            --primary: #10b981;
            --primary-dark: #059669;
            --primary-light: #34d399;
            --primary-glow: rgba(16, 185, 129, 0.25);
            --accent: #f59e0b;
            --accent-light: #fbbf24;
            --accent-glow: rgba(245, 158, 11, 0.25);
            --danger: #ef4444;
            --danger-light: #f87171;
            --danger-glow: rgba(239, 68, 68, 0.25);
            --info: #3b82f6;
            --info-light: #60a5fa;
            --text-dark: #f8fafc;
            --text-light: #94a3b8;
            --text-muted: #64748b;
            --white: #ffffff;
            --radius-sm: 8px;
            --radius-md: 14px;
            --radius-lg: 20px;
            --radius-full: 9999px;
            --shadow-main: 0 20px 40px rgba(0, 0, 0, 0.4);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { margin:0; padding:0; box-sizing:border-box; font-family:'Inter', sans-serif; }
        
        body { 
            background: var(--bg-color); 
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(16, 185, 129, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(245, 158, 11, 0.05) 0%, transparent 40%);
            color: var(--text-dark); 
            display: flex; 
            height: 100vh; 
            overflow: hidden; 
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: rgba(0, 0, 0, 0.2); }
        ::-webkit-scrollbar-thumb { background: rgba(16, 185, 129, 0.3); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(16, 185, 129, 0.6); }

        /* Sidebar */
        .sidebar { 
            width: 270px; 
            background: rgba(4, 20, 13, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            color: var(--white); 
            display: flex; 
            flex-direction: column;
            border-right: 1px solid var(--glass-border);
            z-index: 100;
            transition: var(--transition);
        }

        .sidebar-header { 
            padding: 1.8rem 1.5rem; 
            display: flex;
            align-items: center;
            gap: 0.8rem;
            font-family: 'Outfit', sans-serif;
            font-size: 1.35rem; 
            font-weight: 800; 
            border-bottom: 1px solid var(--glass-border); 
            color: var(--white); 
            letter-spacing: -0.5px;
        }

        .sidebar-header-icon {
            width: 40px; height: 40px;
            border-radius: var(--radius-md);
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            display: flex; align-items: center; justify-content: center;
            color: var(--white);
            font-size: 1.2rem;
            box-shadow: 0 4px 15px var(--primary-glow);
        }

        .sidebar-header span {
            background: linear-gradient(135deg, #ffffff, var(--primary-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .sidebar-menu { 
            flex: 1; 
            padding: 1.5rem 0.8rem; 
            overflow-y: auto; 
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .sidebar-label {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: var(--text-muted);
            padding: 0.8rem 1rem 0.4rem;
        }

        .sidebar-menu a { 
            display: flex; 
            align-items: center; 
            gap: 0.85rem; 
            padding: 0.85rem 1.1rem; 
            color: var(--text-light); 
            text-decoration: none; 
            transition: var(--transition);
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            font-size: 0.95rem;
            border-radius: var(--radius-md);
            position: relative;
        }

        .sidebar-menu a i {
            font-size: 1.1rem;
            width: 24px;
            text-align: center;
            transition: var(--transition);
        }

        .sidebar-menu a:hover { 
            background: rgba(255, 255, 255, 0.05); 
            color: var(--white); 
            transform: translateX(4px);
        }

        .sidebar-menu a.active { 
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(16, 185, 129, 0.05)); 
            color: var(--primary-light); 
            border: 1px solid rgba(16, 185, 129, 0.3);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .sidebar-menu a.active i {
            color: var(--primary-light);
        }

        .sidebar-footer { 
            padding: 1.2rem; 
            border-top: 1px solid var(--glass-border); 
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
        }

        .sidebar-btn { 
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            padding: 0.75rem 1rem; 
            border-radius: var(--radius-md);
            transition: var(--transition);
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
        }

        .sidebar-btn-visit {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-light);
            border: 1px solid var(--glass-border);
        }
        .sidebar-btn-visit:hover {
            background: rgba(255, 255, 255, 0.1);
            color: var(--white);
            border-color: var(--glass-border-light);
        }

        .sidebar-btn-logout {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-light);
            border: 1px solid rgba(239, 68, 68, 0.25);
        }
        .sidebar-btn-logout:hover {
            background: var(--danger);
            color: var(--white);
            border-color: var(--danger);
            box-shadow: 0 4px 15px var(--danger-glow);
        }

        /* Main Content */
        .main-wrapper { 
            flex: 1; 
            display: flex; 
            flex-direction: column; 
            overflow: hidden; 
        }

        .topbar { 
            background: rgba(6, 25, 16, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            height: 75px; 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
            padding: 0 2rem; 
            border-bottom: 1px solid var(--glass-border);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.25);
            z-index: 90;
        }

        .topbar-title {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .topbar h2 { 
            font-family: 'Outfit', sans-serif; 
            font-weight: 700; 
            color: var(--white); 
            font-size: 1.35rem; 
            letter-spacing: -0.3px;
        }

        .topbar-subtitle {
            font-size: 0.82rem;
            color: var(--text-light);
            font-weight: 400;
        }

        .content { 
            flex: 1; 
            padding: 2rem; 
            overflow-y: auto; 
        }

        /* UI Cards */
        .card { 
            background: var(--card-bg); 
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-radius: var(--radius-lg); 
            padding: 1.8rem; 
            border: 1px solid var(--glass-border);
            box-shadow: var(--shadow-main);
            transition: var(--transition);
        }

        .card:hover {
            border-color: rgba(255, 255, 255, 0.12);
        }

        .grid { display: grid; gap: 1.5rem; }
        .grid-4 { grid-template-columns: repeat(4, 1fr); }
        .grid-3 { grid-template-columns: repeat(3, 1fr); }
        .grid-2 { grid-template-columns: repeat(2, 1fr); }

        /* Modern Tables */
        table { 
            width: 100%; 
            border-collapse: separate; 
            border-spacing: 0;
            margin-top: 1.2rem; 
            color: var(--text-light); 
        }

        th, td { 
            padding: 1.1rem 1.2rem; 
            text-align: left; 
            border-bottom: 1px solid var(--glass-border); 
        }

        th { 
            background: rgba(255, 255, 255, 0.03); 
            font-weight: 700; 
            color: var(--white); 
            font-family: 'Outfit', sans-serif; 
            text-transform: uppercase; 
            font-size: 0.8rem; 
            letter-spacing: 1px; 
        }

        th:first-child { border-top-left-radius: var(--radius-sm); border-bottom-left-radius: var(--radius-sm); }
        th:last-child { border-top-right-radius: var(--radius-sm); border-bottom-right-radius: var(--radius-sm); }

        tbody tr {
            transition: var(--transition);
        }
        tbody tr:hover {
            background: rgba(255, 255, 255, 0.03);
        }

        /* Badges */
        .badge { 
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.35rem 0.85rem; 
            border-radius: var(--radius-full); 
            font-size: 0.78rem; 
            font-weight: 700; 
            font-family: 'Outfit', sans-serif; 
            letter-spacing: 0.3px;
        }

        .bg-green { background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
        .bg-yellow { background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); }
        .bg-red { background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }
        .bg-blue { background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3); }

        /* Buttons */
        .btn-sm { 
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.55rem 1.1rem; 
            background: linear-gradient(135deg, var(--primary), var(--primary-dark)); 
            color: var(--white); 
            border: none; 
            border-radius: var(--radius-full); 
            cursor: pointer; 
            text-decoration: none; 
            font-size: 0.85rem;
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            transition: var(--transition);
            box-shadow: 0 4px 15px var(--primary-glow);
        }
        .btn-sm:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4); 
            color: var(--white);
        }

        /* Topbar Actions & Dropdowns */
        .topbar-actions { display: flex; align-items: center; gap: 0.8rem; }

        .topbar-icon-btn {
            position: relative;
            width: 44px; height: 44px;
            border-radius: var(--radius-md);
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            color: var(--text-light);
            font-size: 1.1rem;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: var(--transition);
        }
        .topbar-icon-btn:hover { 
            background: rgba(255, 255, 255, 0.1); 
            color: var(--white); 
            border-color: var(--glass-border-light);
        }

        .notif-badge {
            position: absolute; top: -5px; right: -5px;
            background: var(--danger); color: white;
            font-size: 0.65rem; font-weight: 800;
            width: 20px; height: 20px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            border: 2px solid var(--bg-color);
            animation: notifPulse 2s infinite;
        }

        @keyframes notifPulse {
            0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
            50% { transform: scale(1.1); box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); }
        }

        .dropdown-menu {
            position: absolute;
            top: calc(100% + 14px);
            right: 0;
            min-width: 320px;
            background: rgba(6, 25, 16, 0.95);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: var(--radius-lg);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.6);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px) scale(0.97);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            z-index: 1000;
            overflow: hidden;
        }
        .dropdown-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
        }
        .dropdown-header {
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid var(--glass-border);
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 0.95rem;
            color: var(--white);
            display: flex; align-items: center; gap: 0.6rem;
        }
        .dropdown-header i { color: var(--accent); }

        .notif-item {
            display: flex; align-items: flex-start; gap: 1rem;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            transition: var(--transition);
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }
        .notif-item:hover { background: rgba(255, 255, 255, 0.05); }
        .notif-item:last-child { border-bottom: none; }
        .notif-icon {
            width: 38px; height: 38px;
            border-radius: var(--radius-md);
            display: flex; align-items: center; justify-content: center;
            font-size: 0.95rem;
            flex-shrink: 0;
        }
        .notif-icon.order { background: rgba(245, 158, 11, 0.15); color: var(--accent); }
        .notif-text { flex: 1; }
        .notif-text strong { color: var(--white); font-size: 0.88rem; display: block; margin-bottom: 2px; }
        .notif-text small { color: var(--text-light); font-size: 0.78rem; opacity: 0.8; }
        .notif-footer {
            padding: 0.9rem 1.5rem;
            border-top: 1px solid var(--glass-border);
            text-align: center;
            background: rgba(0, 0, 0, 0.2);
        }
        .notif-footer a {
            color: var(--primary-light);
            font-weight: 600;
            text-decoration: none;
            font-size: 0.85rem;
            transition: var(--transition);
        }
        .notif-footer a:hover { color: var(--white); }

        .profile-info {
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px solid var(--glass-border);
            background: rgba(0, 0, 0, 0.2);
        }
        .profile-avatar {
            width: 54px; height: 54px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem; color: var(--white);
            margin: 0 auto 0.8rem;
            border: 2px solid var(--primary-light);
            box-shadow: 0 4px 15px var(--primary-glow);
        }
        .profile-info h4 { color: var(--white); font-size: 1rem; margin-bottom: 0.2rem; font-family: 'Outfit', sans-serif; }
        .profile-info p { color: var(--primary-light); font-size: 0.8rem; font-weight: 600; }

        .profile-menu-item {
            display: flex; align-items: center; gap: 0.8rem;
            padding: 0.85rem 1.4rem;
            color: var(--text-light);
            text-decoration: none;
            transition: var(--transition);
            font-size: 0.9rem;
            font-weight: 500;
            border-bottom: 1px solid rgba(255, 255, 255, 0.03);
        }
        .profile-menu-item:hover { background: rgba(255, 255, 255, 0.05); color: var(--white); }
        .profile-menu-item i { width: 20px; text-align: center; color: var(--text-muted); transition: var(--transition); }
        .profile-menu-item:hover i { color: var(--primary-light); }
        .profile-menu-item.logout { color: var(--danger-light); }
        .profile-menu-item.logout:hover { background: rgba(239, 68, 68, 0.1); color: var(--danger); }
        .profile-menu-item.logout:hover i { color: var(--danger); }

        /* Responsive Admin Sidebar */
        .sidebar-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.7); z-index: 1040; display: none; backdrop-filter: blur(6px);
        }
        .sidebar-overlay.active { display: block; }
        #adminSidebarToggle { display: none; }

        @media (max-width: 992px) {
            .sidebar {
                position: fixed; top: 0; left: -280px; height: 100vh;
                z-index: 1050; transition: left 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .sidebar.active { left: 0; }
            #adminSidebarToggle { display: flex !important; }
            .topbar { padding: 0 1.2rem; }
            .topbar h2 { font-size: 1.15rem; }
            .content { padding: 1.2rem; }
            .grid-4, .grid-3, .grid-2 { grid-template-columns: 1fr; }
            table { display: block; overflow-x: auto; white-space: nowrap; }
        }
    </style>
</head>
<body>
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-header-icon">
                <i class="fa-solid fa-leaf"></i>
            </div>
            <span>Krishi Dibanisi Admin</span>
        </div>
        
        <nav class="sidebar-menu">
            <div class="sidebar-label">মেনু ড্যাশবোর্ড</div>
            <a href="index.php" class="<?= $current_page === 'index.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-gauge"></i> ড্যাশবোর্ড
            </a>
            <a href="orders.php" class="<?= $current_page === 'orders.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-cart-shopping"></i> অর্ডারসমূহ
            </a>
            <a href="products.php" class="<?= in_array($current_page, ['products.php', 'add_product.php', 'edit_product.php']) ? 'active' : '' ?>">
                <i class="fa-solid fa-box-open"></i> পণ্যসমূহ
            </a>
            
            <div class="sidebar-label">কন্টেন্ট ও গ্রাহক</div>
            <a href="blogs.php" class="<?= in_array($current_page, ['blogs.php', 'add_blog.php', 'edit_blog.php']) ? 'active' : '' ?>">
                <i class="fa-solid fa-newspaper"></i> ব্লগ ম্যানেজমেন্ট
            </a>
            <a href="customers.php" class="<?= $current_page === 'customers.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-users"></i> গ্রাহক তালিকা
            </a>
            
            <div class="sidebar-label">কনফিগারেশন</div>
            <a href="settings.php" class="<?= $current_page === 'settings.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-gear"></i> সেটিংস
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="../index.php" target="_blank" class="sidebar-btn sidebar-btn-visit">
                <i class="fa-solid fa-globe"></i> সাইট ভিজিট করুন
            </a>
            <a href="../logout.php" class="sidebar-btn sidebar-btn-logout">
                <i class="fa-solid fa-right-from-bracket"></i> লগআউট
            </a>
        </div>
    </aside>

    <?php
    // Fetch pending orders count for notification badge
    $notif_pending = 0;
    $notif_orders = [];
    try {
        $notif_pending = $pdo->query("SELECT COUNT(*) FROM orders WHERE status='pending'")->fetchColumn();
        $notif_orders = $pdo->query("SELECT id, total_amount, payment_method, created_at FROM orders WHERE status='pending' ORDER BY id DESC LIMIT 5")->fetchAll();
    } catch(Exception $e) {}
    ?>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <header class="topbar">
            <div class="topbar-title">
                <button id="adminSidebarToggle" class="topbar-icon-btn" aria-label="Toggle Menu">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div>
                    <h2>স্বাগতম, <?= htmlspecialchars($_SESSION['user_name'] ?? 'অ্যাডমিন') ?></h2>
                    <div class="topbar-subtitle">কৃষি দিবানিশি কন্ট্রোল প্যানেল</div>
                </div>
            </div>

            <div class="topbar-actions">
                <!-- Notification Icon -->
                <div style="position:relative;" id="notifWrapper">
                    <button class="topbar-icon-btn" id="notifToggle" aria-label="Notifications">
                        <i class="fa-solid fa-bell"></i>
                        <?php if($notif_pending > 0): ?>
                            <span class="notif-badge"><?= $notif_pending > 9 ? '9+' : $notif_pending ?></span>
                        <?php endif; ?>
                    </button>

                    <div class="dropdown-menu" id="notifDropdown">
                        <div class="dropdown-header"><i class="fa-solid fa-bell"></i> নোটিফিকেশন</div>
                        <?php if(empty($notif_orders)): ?>
                            <div class="notif-item" style="justify-content:center;">
                                <div class="notif-text" style="text-align:center;">
                                    <small style="opacity:1; color:var(--text-light);">কোনো নতুন নোটিফিকেশন নেই 🎉</small>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach($notif_orders as $nOrder): ?>
                                <a href="orders.php" class="notif-item">
                                    <div class="notif-icon order"><i class="fa-solid fa-bag-shopping"></i></div>
                                    <div class="notif-text">
                                        <strong>নতুন অর্ডার #<?= $nOrder['id'] ?></strong>
                                        <small>৳ <?= number_format($nOrder['total_amount'], 2) ?> — <?= htmlspecialchars($nOrder['payment_method']) ?></small>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <div class="notif-footer">
                            <a href="orders.php">সব অর্ডার দেখুন <i class="fa-solid fa-arrow-right" style="font-size:0.75rem;"></i></a>
                        </div>
                    </div>
                </div>

                <!-- Profile Icon -->
                <div style="position:relative;" id="profileWrapper">
                    <button class="topbar-icon-btn" id="profileToggle" aria-label="Profile">
                        <i class="fa-regular fa-circle-user"></i>
                    </button>
                    <div class="dropdown-menu" id="profileDropdown" style="min-width:260px;">
                        <div class="profile-info">
                            <div class="profile-avatar"><i class="fa-solid fa-user-shield"></i></div>
                            <h4><?= htmlspecialchars($_SESSION['user_name'] ?? 'অ্যাডমিন') ?></h4>
                            <p>প্রধান অ্যাডমিনিস্ট্রেটর</p>
                        </div>
                        <a href="index.php" class="profile-menu-item"><i class="fa-solid fa-gauge"></i> ড্যাশবোর্ড</a>
                        <a href="settings.php" class="profile-menu-item"><i class="fa-solid fa-sliders"></i> অ্যাকাউন্ট সেটিংস</a>
                        <a href="../index.php" target="_blank" class="profile-menu-item"><i class="fa-solid fa-globe"></i> সাইট ভিজিট</a>
                        <a href="../logout.php" class="profile-menu-item logout"><i class="fa-solid fa-right-from-bracket"></i> লগআউট</a>
                    </div>
                </div>
            </div>
        </header>
        <main class="content">

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const adminSidebarToggle = document.getElementById('adminSidebarToggle');
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            if (adminSidebarToggle && sidebar && overlay) {
                adminSidebarToggle.addEventListener('click', () => {
                    sidebar.classList.add('active');
                    overlay.classList.add('active');
                });

                overlay.addEventListener('click', () => {
                    sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                });
            }
        });
    </script>
