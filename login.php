<?php 
require_once __DIR__ . '/includes/header.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
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
    }
}
?>
<section class="section" style="min-height: 60vh; display: flex; justify-content: center; align-items: center; background:var(--bg-color);">
    <div style="background: var(--card-bg); padding: 3rem; border-radius: var(--radius); box-shadow: var(--card-shadow); max-width: 400px; width: 100%; border: 1px solid rgba(255,255,255,0.05);">
        <h2 style="text-align:center; margin-bottom:2rem; color:var(--white);">লগইন করুন</h2>

        <?php if(isset($error)): ?>
            <div style="background:rgba(239, 68, 68, 0.2); color:#f87171; padding:0.8rem; border-radius:8px; margin-bottom:1.5rem; text-align:center; border:1px solid rgba(239, 68, 68, 0.3);">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.5rem; color:var(--text-light);">ইমেইল</label>
                <input type="email" name="email" placeholder="email@example.com" class="modern-input" required>
            </div>
            <div style="margin-bottom:1.5rem;">
                <label style="display:block; margin-bottom:0.5rem; color:var(--text-light);">পাসওয়ার্ড</label>
                <input type="password" name="password" placeholder="********" class="modern-input" required>
            </div>
            <button type="submit" class="btn" style="width:100%; justify-content:center;">লগইন</button>
        </form>
        <p style="text-align:center; margin-top:1.5rem; font-size:0.9rem; color:var(--text-light);">
            অ্যাকাউন্ট নেই? <a href="register.php" style="color:var(--white); font-weight:600;">রেজিস্ট্রেশন করুন</a>
        </p>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
