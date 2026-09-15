<?php 
require_once __DIR__ . '/includes/header.php'; 

// Ensure database connection and auto-seed default admin user if missing
try {
    if (isset($pdo)) {
        $admin_email = 'admin@krishidibanisi.com';
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $checkStmt->execute([$admin_email]);
        $existingAdmin = $checkStmt->fetch();
        
        if (!$existingAdmin) {
            $default_hash = password_hash('password', PASSWORD_DEFAULT);
            $insertStmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'admin')");
            $insertStmt->execute(['Admin User', $admin_email, $default_hash]);
        }
    }
} catch (Exception $e) {
    // Graceful silent handling if DB is initializing
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    try {
        if (!isset($pdo)) {
            throw new Exception("ডাটাবেস কানেকশন প্রস্তুত নয়। অনুগ্রহ করে MySQL চালু রাখুন।");
        }

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            if ($user['role'] === 'admin') {
                echo "<script>window.location.href='admin/index.php';</script>";
            } else {
                echo "<script>window.location.href='dashboard.php';</script>";
            }
            exit;
        } else {
            $error = "ইমেইল অথবা পাসওয়ার্ড সঠিক নয়।";
        }
    } catch (PDOException $e) {
        $error = "লগইন করতে সমস্যা হয়েছে: " . $e->getMessage();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<section class="section" style="min-height: 70vh; display: flex; justify-content: center; align-items: center; background:var(--bg-color); padding: 3rem 1rem;">
    <div style="background: var(--card-bg); padding: 2.5rem; border-radius: var(--radius); box-shadow: var(--card-shadow); max-width: 440px; width: 100%; border: 1px solid rgba(255,255,255,0.08); backdrop-filter: blur(12px);">
        
        <div style="text-align:center; margin-bottom:1.8rem;">
            <div style="width:50px; height:50px; background:rgba(245, 183, 89, 0.15); color:var(--accent); border-radius:50%; display:inline-flex; align-items:center; justify-content:center; font-size:1.4rem; margin-bottom:0.8rem; border:1px solid rgba(245, 183, 89, 0.3);">
                <i class="fa-solid fa-lock"></i>
            </div>
            <h2 style="margin:0; color:var(--white); font-size:1.6rem;">লগইন করুন</h2>
            <p style="color:var(--text-light); font-size:0.85rem; margin-top:0.3rem;">কৃষি দিবানিশি সিস্টেমে প্রবেশ করতে লগইন করুন</p>
        </div>

        <?php if(isset($error)): ?>
            <div style="background:rgba(239, 68, 68, 0.2); color:#f87171; padding:0.8rem 1rem; border-radius:10px; margin-bottom:1.5rem; text-align:center; border:1px solid rgba(239, 68, 68, 0.3); font-size:0.9rem; display:flex; align-items:center; justify-content:center; gap:0.5rem;">
                <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="loginForm">
            <div style="margin-bottom:1.2rem;">
                <label style="display:block; margin-bottom:0.5rem; color:var(--text-light); font-size:0.9rem; font-weight:500;">ইমেইল অ্যাড্রেস</label>
                <div style="position:relative;">
                    <input type="email" id="email" name="email" placeholder="email@example.com" class="modern-input" required style="padding-left:2.5rem;">
                    <i class="fa-solid fa-envelope" style="position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:var(--text-light); opacity:0.6;"></i>
                </div>
            </div>
            
            <div style="margin-bottom:1.5rem;">
                <label style="display:block; margin-bottom:0.5rem; color:var(--text-light); font-size:0.9rem; font-weight:500;">পাসওয়ার্ড</label>
                <div style="position:relative;">
                    <input type="password" id="password" name="password" placeholder="********" class="modern-input" required style="padding-left:2.5rem;">
                    <i class="fa-solid fa-key" style="position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:var(--text-light); opacity:0.6;"></i>
                </div>
            </div>
            
            <button type="submit" class="btn" style="width:100%; justify-content:center; padding:0.75rem; font-size:0.95rem;">
                <i class="fa-solid fa-right-to-bracket"></i> লগইন করুন
            </button>
        </form>

        <!-- Default Admin Credentials Badge -->
        <div style="margin-top:1.8rem; padding:1.2rem; background:rgba(255,255,255,0.04); border-radius:12px; border:1px dashed rgba(245, 183, 89, 0.4);">
            <div style="display:flex; align-items:center; gap:0.6rem; color:var(--accent); font-weight:600; font-size:0.875rem; margin-bottom:0.6rem;">
                <i class="fa-solid fa-user-shield"></i> ডিফল্ট অ্যাডমিন ক্রেডেনশিয়াল (Default Admin)
            </div>
            <div style="font-size:0.825rem; color:var(--text-light); line-height:1.6;">
                <div><strong>ইমেইল:</strong> <code style="background:rgba(0,0,0,0.3); padding:0.15rem 0.4rem; border-radius:4px; color:var(--white);">admin@krishidibanisi.com</code></div>
                <div><strong>পাসওয়ার্ড:</strong> <code style="background:rgba(0,0,0,0.3); padding:0.15rem 0.4rem; border-radius:4px; color:var(--white);">password</code></div>
            </div>
            <button type="button" id="fillAdminBtn" class="btn btn-outline" style="width:100%; margin-top:0.8rem; padding:0.4rem 0.8rem; font-size:0.8rem; border-radius:50px; justify-content:center; border-color:rgba(245, 183, 89, 0.5); color:var(--accent);">
                <i class="fa-solid fa-wand-magic-sparkles"></i> অটো-ফিল অ্যাডমিন তথ্য (Auto-fill Admin)
            </button>
        </div>

        <p style="text-align:center; margin-top:1.5rem; font-size:0.875rem; color:var(--text-light);">
            অ্যাকাউন্ট নেই? <a href="register.php" style="color:var(--accent); font-weight:600;">রেজিস্ট্রেশন করুন</a>
        </p>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const fillAdminBtn = document.getElementById('fillAdminBtn');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    if (fillAdminBtn && emailInput && passwordInput) {
        fillAdminBtn.addEventListener('click', () => {
            emailInput.value = 'admin@krishidibanisi.com';
            passwordInput.value = 'password';
            emailInput.focus();
        });
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
