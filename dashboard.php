<?php 
require_once __DIR__ . '/includes/header.php'; 

// Auth Check for Customers
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_name = $_SESSION['user_name'] ?? 'User';
?>

<section class="section" style="min-height: 80vh; background: var(--bg-color);">
    <div style="max-width: 1000px; margin: 0 auto;">
        
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:3rem; gap:1.5rem; flex-wrap:wrap;">
            <div>
                <h1 style="font-size:2.5rem; margin-bottom:0.5rem; font-family:'Outfit',sans-serif;">👋 স্বাগতম, <span style="color:var(--white);"><?= htmlspecialchars($user_name) ?></span>!</h1>
                <p style="color:var(--text-light); font-size:1.1rem; opacity:0.8;">আপনার অ্যাকাউন্টের তথ্যাদি এবং অর্ডার সামারি এখান থেকে দেখুন।</p>
            </div>
            <a href="logout.php" class="btn" style="background:#ef4444; border-color:#ef4444;">লগআউট করুন</a>
        </div>

        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:2rem;">
            
            <!-- Account Info Card -->
            <div style="background:var(--card-bg); padding:2rem; border-radius:var(--radius); border:1px solid rgba(255,255,255,0.05); box-shadow:var(--card-shadow);">
                <div style="display:flex; align-items:center; gap:1rem; margin-bottom:1.5rem;">
                    <div style="width:50px; height:50px; background:var(--accent); border-radius:50%; display:flex; align-items:center; justify-content:center; color:var(--bg-color); font-size:1.5rem;">
                        <i class="fa-solid fa-user-gear"></i>
                    </div>
                    <h3 style="margin:0; color:var(--white);">প্রোফাইল তথ্য</h3>
                </div>
                <div style="color:var(--text-light);">
                    <p style="margin-bottom:0.8rem;"><strong>নাম:</strong> <?= htmlspecialchars($user_name) ?></p>
                    <p style="margin-bottom:0.8rem;"><strong>তথ্যাবশত:</strong> আপনি একজন <span style="color:var(--accent); font-weight:600; text-transform:capitalize;"><?= $_SESSION['user_role'] ?></span></p>
                    <p><strong>যোগদান:</strong> <?= date('d M, Y') ?></p>
                </div>
                <button class="btn btn-outline" style="margin-top:2rem; width:100%; justify-content:center;">প্রোফাইল এডিট করুন</button>
            </div>

            <!-- Stats Card 1 -->
            <div style="background:var(--card-bg); padding:2rem; border-radius:var(--radius); border:1px solid rgba(255,255,255,0.05); text-align:center;">
                <h4 style="color:var(--text-light); font-weight:500; margin-bottom:1rem;">মোট অর্ডার</h4>
                <div style="font-size:3rem; font-weight:800; color:var(--white); font-family:'Outfit',sans-serif; margin-bottom:1rem;">০</div>
                <p style="color:var(--text-light); font-size:0.9rem; opacity:0.6;">আপনি এখনো কোনো অর্ডার করেননি।</p>
                <a href="products.php" class="btn" style="margin-top:1.5rem; width:100%; justify-content:center;">কেনাকাটা করুন</a>
            </div>

            <!-- Stats Card 2 -->
            <div style="background:var(--card-bg); padding:2rem; border-radius:var(--radius); border:1px solid rgba(255,255,255,0.05); text-align:center;">
                <h4 style="color:var(--text-light); font-weight:500; margin-bottom:1rem;">কার্টে আইটেম</h4>
                <div style="font-size:3rem; font-weight:800; color:var(--accent); font-family:'Outfit',sans-serif; margin-bottom:1rem;">
                    <?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?>
                </div>
                <p style="color:var(--text-light); font-size:0.9rem; opacity:0.6;">আপনার কার্টে পণ্যগুলো জমা আছে।</p>
                <a href="cart.php" class="btn btn-outline" style="margin-top:1.5rem; width:100%; justify-content:center;">কার্ট দেখুন</a>
            </div>

        </div>

    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
