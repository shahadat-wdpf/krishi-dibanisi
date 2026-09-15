<?php require_once __DIR__ . '/includes/header.php';

// Handle Delete
if(isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    try {
        // Prevent deleting self (if admin is in users table and logged in)
        // For simplicity, we just delete but in a real app check for permissions
        $pdo->prepare("DELETE FROM users WHERE id=? AND role='customer'")->execute([$_GET['delete']]);
        echo "<script>alert('গ্রাহক অ্যাকাউন্ট সফলভাবে মুছে ফেলা হয়েছে!'); window.location.href='customers.php';</script>";
        exit;
    } catch(Exception $e) {
        $error = "গ্রাহক মুছতে সমস্যা হয়েছে: " . $e->getMessage();
    }
}

// Search and Fetch Customers
$search = $_GET['search'] ?? '';
$customers = [];
try {
    if(!empty($search)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE role='customer' AND (name LIKE ? OR email LIKE ?) ORDER BY id DESC");
        $search_param = "%$search%";
        $stmt->execute([$search_param, $search_param]);
    } else {
        $stmt = $pdo->query("SELECT * FROM users WHERE role='customer' ORDER BY id DESC");
    }
    $customers = $stmt->fetchAll();
} catch(Exception $e) {}

// Stats
$total_customers = count($customers);
if(!empty($search)) {
    // Total count without search filter for stats
    $total_count = $pdo->query("SELECT COUNT(*) FROM users WHERE role='customer'")->fetchColumn();
} else {
    $total_count = $total_customers;
}
?>

<div style="margin-bottom:2.5rem; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem;">
    <div>
        <h1 style="font-size:2rem; margin-bottom:0.5rem;">গ্রাহক ম্যানেজমেন্ট</h1>
        <p style="color:var(--text-light); opacity:0.8;">রেজিস্ট্রিকৃত গ্রাহকদের তথ্য পরিচালনা করুন</p>
    </div>
</div>

<!-- Stats Card -->
<div style="max-width:300px; margin-bottom:2.5rem;">
    <div class="card" style="border-top:4px solid var(--accent); text-align:center;">
        <div style="font-size:2rem; font-weight:800; color:var(--white);"><?= $total_count ?></div>
        <div style="color:var(--text-light); font-size:0.9rem; margin-top:0.3rem;">মোট রেজিস্ট্রিকৃত গ্রাহক</div>
    </div>
</div>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; padding-bottom:1rem; border-bottom:1px solid var(--glass-border); flex-wrap:wrap; gap:1rem;">
        <h3 style="display:flex; align-items:center; gap:0.75rem;">
            <i class="fa-solid fa-users" style="color:var(--accent);"></i> গ্রাহকদের তালিকা
        </h3>
        
        <!-- Search Bar -->
        <form method="GET" style="display:flex; gap:0.5rem;">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="নাম বা ইমেইল খুঁজুন..." 
                style="padding:0.6rem 1.2rem; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:12px; color:var(--white); outline:none; font-size:0.9rem; width:220px;">
            <button type="submit" class="btn-sm" style="padding:0.6rem 1rem;">
                <i class="fa-solid fa-search"></i>
            </button>
            <?php if(!empty($search)): ?>
                <a href="customers.php" class="btn-sm" style="background:rgba(255,255,255,0.1); padding:0.6rem 1rem;" title="Clear Search">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            <?php endif; ?>
        </form>
    </div>

    <?php if(isset($error)): ?>
        <div style="background:rgba(239, 68, 68, 0.2); color:#f87171; padding:1.2rem; border-radius:var(--radius); margin-bottom:1.5rem; border:1px solid rgba(239, 68, 68, 0.3);">
            <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if(empty($customers)): ?>
        <div style="text-align:center; padding:4rem; color:var(--text-light); opacity:0.6;">
            <i class="fa-solid fa-user-slash" style="font-size:3.5rem; margin-bottom:1rem; display:block;"></i>
            <p><?= !empty($search) ? 'আপনার খোঁজা অনুযায়ী কোনো গ্রাহক পাওয়া যায়নি।' : 'এখনো কোনো গ্রাহক রেজিস্ট্রেশন করেননি।' ?></p>
        </div>
    <?php else: ?>
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>নাম</th>
                    <th>ইমেইল</th>
                    <th>রেজিস্ট্রেশন তারিখ</th>
                    <th style="text-align:right;">অ্যাকশন</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($customers as $customer): ?>
                <tr>
                    <td style="font-weight:700; color:var(--white);">#<?= $customer['id'] ?></td>
                    <td>
                        <div style="display:flex; align-items:center; gap:0.75rem;">
                            <div style="width:35px; height:35px; border-radius:50%; background:rgba(255,255,255,0.05); display:flex; align-items:center; justify-content:center; color:var(--accent); font-weight:700; border:1px solid var(--glass-border);">
                                <?= strtoupper(substr($customer['name'], 0, 1)) ?>
                            </div>
                            <span style="font-weight:600; color:var(--white);"><?= htmlspecialchars($customer['name']) ?></span>
                        </div>
                    </td>
                    <td style="color:var(--text-light);"><?= htmlspecialchars($customer['email']) ?></td>
                    <td style="color:var(--text-light); font-size:0.9rem; opacity:0.7;">
                        <i class="fa-regular fa-calendar-days" style="margin-right:0.3rem;"></i>
                        <?= date('d M Y, h:i A', strtotime($customer['created_at'])) ?>
                    </td>
                    <td style="text-align:right;">
                        <div style="display:flex; gap:0.5rem; justify-content:flex-end;">
                            <a href="mailto:<?= htmlspecialchars($customer['email']) ?>" class="btn-sm" style="background:rgba(16,185,129,0.15); color:#34d399; border:1px solid rgba(16,185,129,0.2); padding:0.5rem; border-radius:8px;" title="ইমেইল পাঠান">
                                <i class="fa-solid fa-envelope"></i>
                            </a>
                            <a href="customers.php?delete=<?= $customer['id'] ?>" class="btn-sm" style="background:rgba(239,68,68,0.15); color:#f87171; border:1px solid rgba(239,68,68,0.2); padding:0.5rem; border-radius:8px;" title="মুছে ফেলুন" onclick="return confirm('আপনি কি নিশ্চিত যে এই গ্রাহক অ্যাকাউন্টটি স্থায়ীভাবে মুছে ফেলতে চান?')">
                                <i class="fa-solid fa-trash-can"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
