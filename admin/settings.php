<?php
require_once __DIR__ . '/includes/header.php';

// Handle Settings Update
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
        
        $params = [
            'site_name' => $_POST['site_name'],
            'contact_email' => $_POST['contact_email'],
            'contact_phone' => $_POST['contact_phone'],
            'contact_address' => $_POST['contact_address'],
            'bkash_number' => $_POST['bkash_number'],
            'delivery_charge' => $_POST['delivery_charge'],
            'facebook_url' => $_POST['facebook_url'],
            'youtube_url' => $_POST['youtube_url']
        ];
        
        foreach($params as $key => $val) {
            $stmt->execute([htmlspecialchars($val), $key]);
        }
        
        // Output success and redirect to refresh settings cache
        echo "<script>alert('সেটিংস সফলভাবে আপডেট হয়েছে!'); window.location.href='settings.php';</script>";
        exit;
    } catch(Exception $e) {
        $error = "Error updating settings: " . $e->getMessage();
    }
}
?>

<div style="margin-bottom:2.5rem;">
    <h1 style="font-size:2rem; margin-bottom:0.5rem; color:var(--white);">ওয়েবসাইট সেটিংস</h1>
    <p style="color:var(--text-light); opacity:0.8;">এখান থেকে ওয়েবসাইটের গুরুত্বপূর্ণ তথ্য পরিবর্তন করুন</p>
</div>

<?php if(isset($error)): ?>
    <div style="background:rgba(239, 68, 68, 0.1); border:1px solid rgba(239, 68, 68, 0.3); color:#ef4444; padding:1rem; border-radius:10px; margin-bottom:2rem;">
        <?= $error ?>
    </div>
<?php endif; ?>

<form method="POST">
    <div class="responsive-grid-2">
        <!-- General Settings -->
        <div class="card">
            <h3 style="color:var(--accent); margin-bottom:1.5rem; display:flex; align-items:center; gap:0.5rem; font-size:1.2rem; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:1rem;">
                <i class="fa-solid fa-circle-info"></i> সাধারণ তথ্য
            </h3>
            
            <div style="margin-bottom:1.2rem;">
                <label style="display:block; margin-bottom:0.5rem; color:var(--white); font-size:0.9rem;">ওয়েবসাইটের নাম</label>
                <input type="text" name="site_name" class="modern-input" value="<?= htmlspecialchars($site_settings['site_name'] ?? '') ?>" required>
            </div>
            
            <div style="margin-bottom:1.2rem;">
                <label style="display:block; margin-bottom:0.5rem; color:var(--white); font-size:0.9rem;">যোগাযোগের ইমেইল</label>
                <input type="email" name="contact_email" class="modern-input" value="<?= htmlspecialchars($site_settings['contact_email'] ?? '') ?>">
            </div>
            
            <div style="margin-bottom:1.2rem;">
                <label style="display:block; margin-bottom:0.5rem; color:var(--white); font-size:0.9rem;">ফোন নম্বর</label>
                <input type="text" name="contact_phone" class="modern-input" value="<?= htmlspecialchars($site_settings['contact_phone'] ?? '') ?>">
            </div>
            
            <div style="margin-bottom:1.2rem;">
                <label style="display:block; margin-bottom:0.5rem; color:var(--white); font-size:0.9rem;">অফিসের ঠিকানা</label>
                <textarea name="contact_address" class="modern-input" rows="3"><?= htmlspecialchars($site_settings['contact_address'] ?? '') ?></textarea>
            </div>
        </div>
        
        <div style="display:flex; flex-direction:column; gap:2rem;">
            <!-- Payment Settings -->
            <div class="card">
                <h3 style="color:#34d399; margin-bottom:1.5rem; display:flex; align-items:center; gap:0.5rem; font-size:1.2rem; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:1rem;">
                    <i class="fa-solid fa-money-bill-transfer"></i> পেমেন্ট ও ডেলিভারি
                </h3>
                
                <div style="margin-bottom:1.2rem;">
                    <label style="display:block; margin-bottom:0.5rem; color:var(--white); font-size:0.9rem;">মোবাইল ব্যাংকিং নম্বর (বিকাশ/নগদ/রকেট)</label>
                    <input type="text" name="bkash_number" class="modern-input" value="<?= htmlspecialchars($site_settings['bkash_number'] ?? '') ?>" style="letter-spacing:1px; font-weight:bold; color:var(--primary-light);">
                    <small style="color:var(--text-light); opacity:0.7;">চেকআউট পেজে এই নম্বরটিই গ্রাহকদের দেখানো হবে।</small>
                </div>
                
                <div style="margin-bottom:1.2rem;">
                    <label style="display:block; margin-bottom:0.5rem; color:var(--white); font-size:0.9rem;">ডেলিভারি চার্জ (৳)</label>
                    <input type="number" name="delivery_charge" class="modern-input" value="<?= htmlspecialchars($site_settings['delivery_charge'] ?? '60') ?>" min="0">
                </div>
            </div>
            
            <!-- Social Settings -->
            <div class="card">
                <h3 style="color:#60a5fa; margin-bottom:1.5rem; display:flex; align-items:center; gap:0.5rem; font-size:1.2rem; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:1rem;">
                    <i class="fa-solid fa-share-nodes"></i> সোশ্যাল মিডিয়া
                </h3>
                
                <div style="margin-bottom:1.2rem;">
                    <label style="display:block; margin-bottom:0.5rem; color:var(--white); font-size:0.9rem;"><i class="fa-brands fa-facebook" style="color:#1877F2;"></i> ফেসবুক পেজ লিংক</label>
                    <input type="url" name="facebook_url" class="modern-input" value="<?= htmlspecialchars($site_settings['facebook_url'] ?? '') ?>">
                </div>
                
                <div style="margin-bottom:1.2rem;">
                    <label style="display:block; margin-bottom:0.5rem; color:var(--white); font-size:0.9rem;"><i class="fa-brands fa-youtube" style="color:#FF0000;"></i> ইউটিউব চ্যানেল লিংক</label>
                    <input type="url" name="youtube_url" class="modern-input" value="<?= htmlspecialchars($site_settings['youtube_url'] ?? '') ?>">
                </div>
            </div>
        </div>
    </div>
    
    <div style="text-align:right; margin-bottom:2rem;">
        <button type="submit" class="btn" style="padding:1rem 3rem; font-size:1.1rem;"><i class="fa-solid fa-floppy-disk"></i> পরিবর্তন সেভ করুন</button>
    </div>
</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
