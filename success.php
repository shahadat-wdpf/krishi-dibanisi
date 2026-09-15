<?php
require_once __DIR__ . '/includes/header.php';

$order_id = $_GET['order_id'] ?? null;
$method = urldecode($_GET['method'] ?? 'Unknown');

if(!$order_id) {
    echo "<h2 style='text-align:center; padding:5rem;'>অর্ডার পাওয়া যায়নি।</h2>";
    require_once __DIR__ . '/includes/footer.php';
    exit();
}
?>

<section class="section" style="min-height:70vh; display:flex; justify-content:center; align-items:center; background:var(--bg-color);">
    <div style="background:var(--white); padding:4rem; border-radius:var(--radius); box-shadow:var(--card-shadow); text-align:center; max-width:600px; width:100%;">
        
        <div style="width:100px; height:100px; background:var(--primary-light); color:white; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:4rem; margin:0 auto 2rem;">
            <i class="fa-solid fa-check"></i>
        </div>
        
        <h2 style="color:#000000; margin-bottom:1rem; font-size:2rem;">অর্ডার সফলভাবে সম্পন্ন হয়েছে!</h2>
        <p style="color:#333333; margin-bottom:2rem; font-size:1.1rem;">কৃষি দিবানিশি-তে কেনাকাটা করার জন্য আপনাকে ধন্যবাদ। আপনার অর্ডার নম্বর:</p>
        
        <div style="background:#f4f9f4; padding:1.5rem; border-radius:var(--radius); margin-bottom:2rem; border:1px dashed var(--primary);">
            <h3 style="color:#000000; margin:0;">অর্ডার আইডি: #<?= htmlspecialchars($order_id) ?></h3>
            <p style="color:#333333; margin-top:0.5rem;">পেমেন্ট মেথড: <?= htmlspecialchars($method) ?></p>
        </div>
        
        <?php if($method !== 'Cash on Delivery'): ?>
            <p style="margin-bottom:2rem; color:#000000; background:#d4edda; border:1px solid #c3e6cb; padding:1rem; border-radius:var(--radius); font-size:0.95rem;">
                <i class="fa-solid fa-circle-info"></i> যেহেতু আপনি <strong><?= htmlspecialchars($method) ?></strong> সিলেক্ট করেছেন, তাই আমাদের প্রতিনিধি খুব দ্রুত আপনাকে কল করে পেমেন্টের বিস্তারিত জানিয়ে দেবেন।
            </p>
        <?php endif; ?>
        
        <div style="display:flex; justify-content:center; gap:1rem;">
            <a href="dashboard.php" class="btn btn-outline" style="padding:1rem 2rem;">আমার অর্ডারগুলো দেখুন</a>
            <a href="products.php" class="btn" style="padding:1rem 2rem;">আরও কেনাকাটা করুন</a>
        </div>
        
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
