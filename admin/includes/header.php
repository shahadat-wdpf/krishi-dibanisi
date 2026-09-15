<?php
ob_start();
session_start();
require_once __DIR__ . '/../../config/db.php';

// Enforce Admin Auth
if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Green Tech Farm</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2d6a4f;
            --primary-light: #40916c;
            --accent: #f5b759;
            --bg-color: #063a24;
            --text-dark: #f8fafc;
            --text-light: #cbd5e1;
            --white: #ffffff;
            --card-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
            --radius: 16px;
            --transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        }
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Inter', sans-serif; }
        body { background:var(--bg-color); color:var(--text-dark); display:flex; height:100vh; overflow:hidden; }
        
        /* Sidebar */
        .sidebar { 
            width:280px; 
            background: rgba(4, 39, 24, 0.8);
            backdrop-filter: blur(10px);
            color: var(--white); 
            display:flex; 
            flex-direction:column;
            border-right: 1px solid var(--glass-border);
        }
        .sidebar-header { 
            padding:2rem 1.5rem; 
            text-align:center; 
            font-family: 'Outfit', sans-serif;
            font-size:1.6rem; 
            font-weight:800; 
            border-bottom:1px solid var(--glass-border); 
            color: var(--primary-light); 
        }
        .sidebar-header i { color: var(--accent); margin-right: 0.5rem; }
        .sidebar-menu { flex:1; padding:1.5rem 0; overflow-y:auto; }
        .sidebar-menu a { 
            display:flex; 
            align-items:center; 
            gap:1rem; 
            padding:1rem 2rem; 
            color: var(--text-light); 
            text-decoration:none; 
            transition: var(--transition);
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
        }
        .sidebar-menu a:hover, .sidebar-menu a.active { 
            background: rgba(255, 255, 255, 0.05); 
            color: var(--white); 
            border-left: 4px solid var(--accent); 
        }
        .sidebar-footer { padding:1.5rem; border-top:1px solid var(--glass-border); }
        .sidebar-footer a { 
            color: #fca5a5; 
            text-decoration:none; 
            display:block; 
            text-align:center; 
            padding:0.6rem; 
            border:1px solid #fca5a5; 
            border-radius: 50px;
            transition: var(--transition);
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
        }
        .sidebar-footer a:hover { background:#ef4444; color:white; border-color: #ef4444; }

        /* Main Content */
        .main-wrapper { flex:1; display:flex; flex-direction:column; overflow:hidden; }
        .topbar { 
            background: rgba(6, 58, 36, 0.8);
            backdrop-filter: blur(10px);
            height:70px; 
            display:flex; 
            align-items:center; 
            justify-content:space-between; 
            padding:0 2.5rem; 
            border-bottom: 1px solid var(--glass-border);
            box-shadow:0 4px 30px rgba(0, 0, 0, 0.2);
        }
        .topbar h2 { font-family: 'Outfit', sans-serif; font-weight: 700; color: var(--white); font-size: 1.4rem; }
        .content { flex:1; padding:2.5rem; overflow-y:auto; }

        /* General Styles */
        h1, h2, h3, h4 { font-family: 'Outfit', sans-serif; color: var(--white); }
        .card { 
            background: var(--card-bg); 
            border-radius: var(--radius); 
            padding:2rem; 
            border: 1px solid var(--glass-border);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        .grid { display:grid; gap:1.5rem; }
        .grid-4 { grid-template-columns: repeat(4, 1fr); }
        table { width:100%; border-collapse:collapse; margin-top:1.5rem; color: var(--text-light); }
        th, td { padding:1.2rem; text-align:left; border-bottom:1px solid var(--glass-border); }
        th { background: rgba(255, 255, 255, 0.05); font-weight:700; color:var(--white); font-family: 'Outfit', sans-serif; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px; }
        .badge { padding:0.4rem 0.8rem; border-radius:30px; font-size:0.8rem; font-weight:700; font-family: 'Outfit', sans-serif; }
        .bg-green { background: rgba(16, 185, 129, 0.2); color:#34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
        .bg-yellow { background: rgba(245, 183, 89, 0.2); color:var(--accent); border: 1px solid rgba(245, 183, 89, 0.3); }
        .bg-red { background: rgba(239, 68, 68, 0.2); color:#f87171; border: 1px solid rgba(239, 68, 68, 0.3); }
        .btn-sm { 
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding:0.6rem 1.2rem; 
            background:var(--primary); 
            color:white; 
            border:none; 
            border-radius: 50px; 
            cursor:pointer; 
            text-decoration:none; 
            font-size:0.9rem;
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            transition: var(--transition);
        }
        .btn-sm:hover { background:var(--primary-light); transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.3); }

        /* Responsive Auto Grids */
        .responsive-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:2rem; margin-bottom:2rem; }
        .responsive-grid-3 { display:grid; grid-template-columns:repeat(3,1fr); gap:1.5rem; margin-bottom:2.5rem; }

        /* ==================== TOPBAR DROPDOWNS ==================== */
        .topbar-actions { display:flex; align-items:center; gap:0.5rem; }

        .topbar-icon-btn {
            position: relative;
            width: 42px; height: 42px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
            border: 1px solid var(--glass-border);
            color: var(--text-light);
            font-size: 1.15rem;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: var(--transition);
        }
        .topbar-icon-btn:hover { background: rgba(255,255,255,0.12); color: var(--white); }

        .notif-badge {
            position: absolute; top: -4px; right: -4px;
            background: #ef4444; color: white;
            font-size: 0.65rem; font-weight: 800;
            width: 20px; height: 20px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            border: 2px solid var(--bg-color);
            animation: notifPulse 2s infinite;
        }
        @keyframes notifPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.15); }
        }

        .dropdown-menu {
            position: absolute;
            top: calc(100% + 12px);
            right: 0;
            min-width: 320px;
            background: rgba(6, 58, 36, 0.96);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 16px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.5);
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
            font-size: 1rem;
            color: var(--white);
            display: flex; align-items: center; gap: 0.6rem;
        }
        .dropdown-header i { color: var(--accent); }

        /* Notification Dropdown Items */
        .notif-item {
            display: flex; align-items: flex-start; gap: 1rem;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            transition: var(--transition);
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }
        .notif-item:hover { background: rgba(255,255,255,0.06); }
        .notif-item:last-child { border-bottom: none; }
        .notif-icon {
            width: 38px; height: 38px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.95rem;
            flex-shrink: 0;
        }
        .notif-icon.order { background: rgba(245,183,89,0.15); color: var(--accent); }
        .notif-icon.info { background: rgba(96,165,250,0.15); color: #60a5fa; }
        .notif-icon.success { background: rgba(52,211,153,0.15); color: #34d399; }
        .notif-text { flex: 1; }
        .notif-text strong { color: var(--white); font-size: 0.9rem; display: block; margin-bottom: 2px; }
        .notif-text small { color: var(--text-light); font-size: 0.8rem; opacity: 0.7; }
        .notif-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--glass-border);
            text-align: center;
        }
        .notif-footer a {
            color: var(--accent);
            font-weight: 600;
            text-decoration: none;
            font-size: 0.9rem;
            transition: var(--transition);
        }
        .notif-footer a:hover { color: var(--white); }

        /* Profile Dropdown */
        .profile-info {
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px solid var(--glass-border);
        }
        .profile-avatar {
            width: 60px; height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; color: var(--white);
            margin: 0 auto 0.8rem;
            border: 2px solid var(--accent);
        }
        .profile-info h4 { color: var(--white); font-size: 1.05rem; margin-bottom: 0.2rem; }
        .profile-info p { color: var(--text-light); font-size: 0.85rem; opacity: 0.7; }
        .profile-menu-item {
            display: flex; align-items: center; gap: 0.8rem;
            padding: 0.9rem 1.5rem;
            color: var(--text-light);
            text-decoration: none;
            transition: var(--transition);
            font-size: 0.95rem;
            border-bottom: 1px solid rgba(255,255,255,0.04);
        }
        .profile-menu-item:hover { background: rgba(255,255,255,0.06); color: var(--white); }
        .profile-menu-item i { width: 20px; text-align: center; }
        .profile-menu-item.logout { color: #f87171; }
        .profile-menu-item.logout:hover { background: rgba(239,68,68,0.1); color: #fca5a5; }

        /* Responsive Admin Sidebar */
        .sidebar-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5); z-index: 1040; display: none; backdrop-filter: blur(4px);
        }
        .sidebar-overlay.active { display: block; }
        #adminSidebarToggle { display: none; }

        @media (max-width: 900px) {
            .sidebar {
                position: fixed; top: 0; left: -280px; height: 100vh;
                z-index: 1050; transition: left 0.4s ease;
            }
            .sidebar.active { left: 0; }
            #adminSidebarToggle { display: flex !important; }
            .topbar h2 { font-size: 1.1rem; }
            .topbar { padding: 0 1.5rem; }
            .content { padding: 1.5rem; }
            .grid-4 { grid-template-columns: 1fr; }
            .responsive-grid-2, .responsive-grid-3 { grid-template-columns: 1fr; }
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
            <i class="fa-solid fa-leaf"></i> গ্রীন টেক Admin
        </div>
        <nav class="sidebar-menu">
            <a href="index.php" style="<?= strpos($_SERVER['PHP_SELF'], 'index.php') !== false ? 'background:rgba(255,255,255,0.1);' : '' ?>"><i class="fa-solid fa-gauge"></i> ড্যাশবোর্ড</a>
            <a href="orders.php" style="<?= strpos($_SERVER['PHP_SELF'], 'orders.php') !== false ? 'background:rgba(255,255,255,0.1);' : '' ?>"><i class="fa-solid fa-cart-shopping"></i> অর্ডারসমূহ</a>
            <a href="products.php" style="<?= strpos($_SERVER['PHP_SELF'], 'products.php') !== false ? 'background:rgba(255,255,255,0.1);' : '' ?>"><i class="fa-solid fa-box-open"></i> পণ্যসমূহ</a>
            <a href="blogs.php" style="<?= strpos($_SERVER['PHP_SELF'], 'blogs.php') !== false ? 'background:rgba(255,255,255,0.1);' : '' ?>"><i class="fa-solid fa-newspaper"></i> ব্লগ ম্যানেজমেন্ট</a>
            <a href="customers.php" style="<?= strpos($_SERVER['PHP_SELF'], 'customers.php') !== false ? 'background:rgba(255,255,255,0.1);' : '' ?>"><i class="fa-solid fa-users"></i> গ্রাহক</a>
            <a href="settings.php" style="<?= strpos($_SERVER['PHP_SELF'], 'settings.php') !== false ? 'background:rgba(255,255,255,0.1); border-left:3px solid var(--accent);' : '' ?>"><i class="fa-solid fa-gear"></i> সেটিংস</a>
        </nav>
        <div class="sidebar-footer">
            <a href="../index.php" target="_blank" style="margin-bottom:0.5rem;"><i class="fa-solid fa-globe"></i> সাইট ভিজিট করুন</a>
            <a href="../logout.php" style="border-color:#ef4444; color:#ef4444;"><i class="fa-solid fa-right-from-bracket"></i> লগআউট</a>
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
            <div style="display: flex; align-items: center; gap: 1rem;">
                <button id="adminSidebarToggle" class="topbar-icon-btn" aria-label="Toggle Menu">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h2>স্বাগতম, <?= htmlspecialchars($_SESSION['user_name'] ?? 'অ্যাডমিন') ?></h2>
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
                            <p>অ্যাডমিনিস্ট্রেটর</p>
                        </div>
                        <a href="index.php" class="profile-menu-item"><i class="fa-solid fa-gauge"></i> ড্যাশবোর্ড</a>
                        <a href="../index.php" target="_blank" class="profile-menu-item"><i class="fa-solid fa-globe"></i> সাইট ভিজিট</a>
                        <a href="../logout.php" class="profile-menu-item logout"><i class="fa-solid fa-right-from-bracket"></i> লগআউট</a>
                    </div>
                </div>

            </div>
        </header>
        <main class="content">

    <script>
        // Responsive Sidebar Toggle Logic
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
