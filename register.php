<?php 
require_once __DIR__ . '/includes/header.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'customer')");
        $stmt->execute([$name, $email, $password]);
        echo "<script>alert('রেজিস্ট্রেশন সফল হয়েছে! এখন লগইন করুন।'); window.location.href='login.php';</script>";
        exit;
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $error = "এই ইমেইলটি ইতিপূর্বে ব্যবহার করা হয়েছে।";
        } else {
            $error = "রেজিস্ট্রেশন করতে সমস্যা হয়েছে: " . $e->getMessage();
        }
    }
}
?>
<section class="section" style="min-height: 60vh; display: flex; justify-content: center; align-items: center; background:var(--bg-color);">
    <div style="background: var(--card-bg); padding: 3rem; border-radius: var(--radius); box-shadow: var(--card-shadow); max-width: 450px; width: 100%; border: 1px solid rgba(255,255,255,0.05);">
        <h2 style="text-align:center; margin-bottom:2rem; color:var(--white);">নতুন অ্যাকাউন্ট তৈরি করুন</h2>
        
        <?php if(isset($error)): ?>
            <div style="background:rgba(239, 68, 68, 0.2); color:#f87171; padding:0.8rem; border-radius:8px; margin-bottom:1.5rem; text-align:center; border:1px solid rgba(239, 68, 68, 0.3);">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.5rem; color:var(--text-light);">পুরো নাম</label>
                <input type="text" name="name" placeholder="মোহাম্মদ আব্দুল্লাহ" class="modern-input" required>
            </div>
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.5rem; color:var(--text-light);">ইমেইল</label>
                <input type="email" name="email" placeholder="email@example.com" class="modern-input" required>
            </div>
            <div style="margin-bottom:1.5rem;">
                <label style="display:block; margin-bottom:0.5rem; color:var(--text-light);">পাসওয়ার্ড</label>
                <input type="password" name="password" placeholder="********" class="modern-input" required>
            </div>
            <div style="margin-bottom:1.5rem; display:flex; align-items:center; gap:0.5rem;">
                <input type="checkbox" id="terms" required>
                <label for="terms" style="color:var(--text-light); font-size:0.9rem;">আমি সব শর্তাবলীতে রাজি আছি</label>
            </div>
            <button type="submit" class="btn" style="width:100%; justify-content:center;">রেজিস্টার করুন</button>
        </form>
        <p style="text-align:center; margin-top:1.5rem; font-size:0.9rem; color:var(--text-light);">
            আগে থেকেই অ্যাকাউন্ট আছে? <a href="login.php" style="color:var(--white); font-weight:600;">লগইন করুন</a>
        </p>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
