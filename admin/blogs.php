<?php require_once __DIR__ . '/includes/header.php';

$blogs = [];
try {
    $blogs = $pdo->query("SELECT * FROM blogs ORDER BY id DESC")->fetchAll();
} catch(Exception $e) {}

// Handle Delete
if(isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    try {
        $pdo->prepare("DELETE FROM blogs WHERE id=?")->execute([$_GET['delete']]);
        echo "<script>alert('ব্লগ পোস্ট মুছে ফেলা হয়েছে!'); window.location.href='blogs.php';</script>";
        exit;
    } catch(Exception $e) {}
}

// Handle Publish Toggle
if(isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    try {
        $current = $pdo->prepare("SELECT is_published FROM blogs WHERE id=?");
        $current->execute([$_GET['toggle']]);
        $row = $current->fetch();
        $new_status = $row['is_published'] ? 0 : 1;
        $pdo->prepare("UPDATE blogs SET is_published=? WHERE id=?")->execute([$new_status, $_GET['toggle']]);
        header("Location: blogs.php");
        exit;
    } catch(Exception $e) {}
}
?>

<div style="margin-bottom:2.5rem; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem;">
    <div>
        <h1 style="font-size:2rem; margin-bottom:0.5rem;">ব্লগ ম্যানেজমেন্ট</h1>
        <p style="color:var(--text-light); opacity:0.8;">কৃষি টিপস ও কৃষকের গল্প পরিচালনা করুন</p>
    </div>
    <a href="add_blog.php" class="btn-sm" style="padding:0.9rem 1.8rem; font-size:1rem;">
        <i class="fa-solid fa-plus"></i> নতুন পোস্ট লিখুন
    </a>
</div>

<!-- Stats -->
<div class="responsive-grid-3">
    <?php
    $total_blogs = count($blogs);
    $tips_count = count(array_filter($blogs, fn($b) => $b['type'] === 'farming_tips'));
    $story_count = count(array_filter($blogs, fn($b) => $b['type'] === 'farmer_story'));
    ?>
    <div class="card" style="border-top:4px solid var(--primary-light); text-align:center;">
        <div style="font-size:2rem; font-weight:800; color:var(--white);"><?= $total_blogs ?></div>
        <div style="color:var(--text-light); font-size:0.9rem; margin-top:0.3rem;">মোট পোস্ট</div>
    </div>
    <div class="card" style="border-top:4px solid var(--accent); text-align:center;">
        <div style="font-size:2rem; font-weight:800; color:var(--white);"><?= $tips_count ?></div>
        <div style="color:var(--text-light); font-size:0.9rem; margin-top:0.3rem;">🌾 কৃষি টিপস</div>
    </div>
    <div class="card" style="border-top:4px solid #a78bfa; text-align:center;">
        <div style="font-size:2rem; font-weight:800; color:var(--white);"><?= $story_count ?></div>
        <div style="color:var(--text-light); font-size:0.9rem; margin-top:0.3rem;">👨‍🌾 কৃষকের গল্প</div>
    </div>
</div>

<div class="card">
    <h3 style="margin-bottom:1.5rem; padding-bottom:1rem; border-bottom:1px solid var(--glass-border); display:flex; align-items:center; gap:0.75rem;">
        <i class="fa-solid fa-newspaper" style="color:var(--accent);"></i> সকল ব্লগ পোস্ট
    </h3>

    <?php if(empty($blogs)): ?>
        <div style="text-align:center; padding:4rem; color:var(--text-light); opacity:0.6;">
            <i class="fa-solid fa-pen-to-square" style="font-size:3rem; margin-bottom:1rem; display:block;"></i>
            <p>এখনো কোনো ব্লগ পোস্ট নেই। প্রথম পোস্টটি লিখুন!</p>
        </div>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>থাম্বনেইল</th>
                <th>শিরোনাম</th>
                <th>ধরন</th>
                <th>লেখক</th>
                <th>তারিখ</th>
                <th>স্ট্যাটাস</th>
                <th>অ্যাকশন</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($blogs as $blog): ?>
            <tr>
                <td style="font-weight:700; color:var(--white);">#<?= $blog['id'] ?></td>
                <td>
                    <?php
                    $thumb = '../assets/images/' . $blog['image'];
                    if(!file_exists($thumb) || $blog['image'] === 'default_blog.jpg') {
                        $thumb = 'https://via.placeholder.com/50x50/2d6a4f/ffffff?text=B';
                    }
                    ?>
                    <img src="<?= htmlspecialchars($thumb) ?>" style="width:48px; height:48px; object-fit:cover; border-radius:10px; border:2px solid var(--glass-border);">
                </td>
                <td style="max-width:280px;">
                    <div style="font-weight:600; color:var(--white); margin-bottom:0.2rem;"><?= htmlspecialchars(mb_strimwidth($blog['title'], 0, 55, '...')) ?></div>
                    <small style="color:var(--text-light); opacity:0.6;"><?= htmlspecialchars(mb_strimwidth($blog['excerpt'] ?? '', 0, 60, '...')) ?></small>
                </td>
                <td>
                    <?php if($blog['type'] === 'farming_tips'): ?>
                        <span class="badge bg-green">🌾 কৃষি টিপস</span>
                    <?php else: ?>
                        <span class="badge" style="background:rgba(167,139,250,0.2); color:#c4b5fd; border:1px solid rgba(167,139,250,0.3);">👨‍🌾 কৃষকের গল্প</span>
                    <?php endif; ?>
                </td>
                <td style="color:var(--text-light); font-size:0.9rem;"><?= htmlspecialchars($blog['author']) ?></td>
                <td style="color:var(--text-light); font-size:0.85rem; opacity:0.7;"><?= date('d M Y', strtotime($blog['created_at'])) ?></td>
                <td>
                    <?php if($blog['is_published']): ?>
                        <span class="badge bg-green">প্রকাশিত</span>
                    <?php else: ?>
                        <span class="badge bg-yellow">খসড়া</span>
                    <?php endif; ?>
                </td>
                <td>
                    <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                        <a href="../blog_detail.php?id=<?= $blog['id'] ?>" target="_blank" class="btn-sm" style="background:rgba(96,165,250,0.2); color:#60a5fa; border:1px solid rgba(96,165,250,0.3); padding:0.4rem 0.8rem; font-size:0.8rem;" title="দেখুন">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        <a href="edit_blog.php?id=<?= $blog['id'] ?>" class="btn-sm" style="padding:0.4rem 0.8rem; font-size:0.8rem;" title="এডিট করুন">
                            <i class="fa-solid fa-pen"></i>
                        </a>
                        <a href="blogs.php?toggle=<?= $blog['id'] ?>" class="btn-sm" style="background:rgba(245,183,89,0.2); color:var(--accent); border:1px solid rgba(245,183,89,0.3); padding:0.4rem 0.8rem; font-size:0.8rem;" title="<?= $blog['is_published'] ? 'আনপাবলিশ করুন' : 'পাবলিশ করুন' ?>">
                            <i class="fa-solid fa-<?= $blog['is_published'] ? 'eye-slash' : 'check' ?>"></i>
                        </a>
                        <a href="blogs.php?delete=<?= $blog['id'] ?>" class="btn-sm" style="background:rgba(239,68,68,0.2); color:#f87171; border:1px solid rgba(239,68,68,0.3); padding:0.4rem 0.8rem; font-size:0.8rem;" title="মুছুন" onclick="return confirm('এই পোস্টটি মুছে ফেলবেন?')">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
