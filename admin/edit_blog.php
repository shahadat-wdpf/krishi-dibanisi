<?php require_once __DIR__ . '/includes/header.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: blogs.php");
    exit;
}

// Fetch blog details
try {
    $stmt = $pdo->prepare("SELECT * FROM blogs WHERE id = ?");
    $stmt->execute([$id]);
    $blog = $stmt->fetch();
    if (!$blog) {
        header("Location: blogs.php");
        exit;
    }
} catch (Exception $e) {
    $error = "ডাটা লোড করতে সমস্যা হয়েছে: " . $e->getMessage();
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $content = $_POST['content'] ?? '';
    $excerpt = trim($_POST['excerpt'] ?? '');
    $type = $_POST['type'] ?? 'farming_tips';
    $author = trim($_POST['author'] ?? 'কৃষি দিবানিশি');
    $tags = trim($_POST['tags'] ?? '');
    $is_published = isset($_POST['is_published']) ? 1 : 0;
    
    // Maintain old image name by default
    $image_name = $blog['image'];
    
    // Handle Image Upload if provided
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['image']['tmp_name'];
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image_name = uniqid('blog_') . '.' . $ext;
        $upload_dir = __DIR__ . '/../assets/images/';
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        move_uploaded_file($tmp_name, $upload_dir . $image_name);
        
        // Optional: delete old image if it wasn't default
        if ($blog['image'] !== 'default_blog.jpg' && file_exists($upload_dir . $blog['image'])) {
            // unlink($upload_dir . $blog['image']);
        }
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE blogs SET title=?, slug=?, content=?, excerpt=?, image=?, type=?, author=?, tags=?, is_published=? WHERE id=?");
        $stmt->execute([$title, $slug, $content, $excerpt, $image_name, $type, $author, $tags, $is_published, $id]);
        
        echo "<script>alert('ব্লগ পোস্ট সফলভাবে আপডেট করা হয়েছে!'); window.location.href='blogs.php';</script>";
        exit;
    } catch (Exception $e) {
        $error = "ব্লগ পোস্ট আপডেট করতে সমস্যা হয়েছে: " . $e->getMessage();
    }
}
?>

<div style="margin-bottom:2.5rem; display:flex; justify-content:space-between; align-items:center;">
    <div>
        <h1 style="font-size:2rem; margin-bottom:0.5rem;">ব্লগ এডিট করুন</h1>
        <p style="color:var(--text-light); opacity:0.8;">"<?= htmlspecialchars($blog['title']) ?>" পোস্টটি পরিমার্জন করুন</p>
    </div>
    <a href="blogs.php" class="btn-sm" style="background:rgba(255,255,255,0.1);"><i class="fa-solid fa-arrow-left"></i> ফিরে যান</a>
</div>

<div class="card" style="max-width:950px;">
    <?php if (isset($error)): ?>
        <div style="background:rgba(239, 68, 68, 0.2); color:#f87171; padding:1.2rem; border-radius:var(--radius); margin-bottom:2rem; border:1px solid rgba(239, 68, 68, 0.3);">
            <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="responsive-grid-2">
            
            <!-- Title -->
            <div style="grid-column: 1/-1;">
                <label style="display:block; margin-bottom:0.75rem; color:var(--white); font-weight:600; font-family:'Outfit',sans-serif;">পোস্টের শিরোনাম *</label>
                <input type="text" name="title" value="<?= htmlspecialchars($blog['title']) ?>" required style="width:100%; padding:1rem; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:12px; color:var(--white); outline:none; font-size:1rem; transition:var(--transition);">
            </div>
            
            <!-- Slug -->
            <div>
                <label style="display:block; margin-bottom:0.75rem; color:var(--white); font-weight:600; font-family:'Outfit',sans-serif;">স্লাগ (URL)</label>
                <input type="text" name="slug" value="<?= htmlspecialchars($blog['slug']) ?>" style="width:100%; padding:1rem; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:12px; color:var(--white); outline:none; font-size:1rem;">
            </div>
            
            <!-- Type -->
            <div>
                <label style="display:block; margin-bottom:0.75rem; color:var(--white); font-weight:600; font-family:'Outfit',sans-serif;">পোস্টের ধরন *</label>
                <select name="type" required style="width:100%; padding:1rem; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:12px; color:var(--white); outline:none; font-size:1rem; cursor:pointer;">
                    <option value="farming_tips" <?= $blog['type'] == 'farming_tips' ? 'selected' : '' ?> style="background:#063a24;">🌾 কৃষি টিপস</option>
                    <option value="farmer_story" <?= $blog['type'] == 'farmer_story' ? 'selected' : '' ?> style="background:#063a24;">👨‍🌾 কৃষকের গল্প</option>
                </select>
            </div>
            
            <!-- Author -->
            <div>
                <label style="display:block; margin-bottom:0.75rem; color:var(--white); font-weight:600; font-family:'Outfit',sans-serif;">লেখক</label>
                <input type="text" name="author" value="<?= htmlspecialchars($blog['author']) ?>" style="width:100%; padding:1rem; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:12px; color:var(--white); outline:none; font-size:1rem;">
            </div>
            
            <!-- Tags -->
            <div>
                <label style="display:block; margin-bottom:0.75rem; color:var(--white); font-weight:600; font-family:'Outfit',sans-serif;">ট্যাগ <small style="color:var(--text-light); opacity:0.6;">— কমা দিয়ে আলাদা করুন</small></label>
                <input type="text" name="tags" value="<?= htmlspecialchars($blog['tags']) ?>" style="width:100%; padding:1rem; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:12px; color:var(--white); outline:none; font-size:1rem;">
            </div>
            
            <!-- Excerpt -->
            <div style="grid-column: 1/-1;">
                <label style="display:block; margin-bottom:0.75rem; color:var(--white); font-weight:600; font-family:'Outfit',sans-serif;">সংক্ষিপ্ত বর্ণনা (Excerpt)</label>
                <textarea name="excerpt" rows="3" style="width:100%; padding:1rem; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:12px; color:var(--white); outline:none; font-size:1rem; line-height:1.6; resize:vertical;"><?= htmlspecialchars($blog['excerpt']) ?></textarea>
            </div>
            
            <!-- Content -->
            <div style="grid-column: 1/-1;">
                <label style="display:block; margin-bottom:0.75rem; color:var(--white); font-weight:600; font-family:'Outfit',sans-serif;">সম্পূর্ণ কন্টেন্ট *</label>
                <textarea name="content" rows="15" required style="width:100%; padding:1rem; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:12px; color:var(--white); outline:none; font-size:0.95rem; line-height:1.7; resize:vertical; font-family:'Inter',sans-serif;"><?= htmlspecialchars($blog['content']) ?></textarea>
            </div>
            
            <!-- Image Upload -->
            <div style="grid-column: 1/-1;">
                <label style="display:block; margin-bottom:0.75rem; color:var(--white); font-weight:600; font-family:'Outfit',sans-serif;">কভার ছবি</label>
                <div style="display:flex; gap:1.5rem; align-items:center; background:rgba(255,255,255,0.02); padding:1rem; border-radius:12px; border:1px solid var(--glass-border);">
                    <div>
                        <?php 
                        $img_path = '../assets/images/' . $blog['image'];
                        if(!file_exists($img_path)) $img_path = '../assets/images/' . $blog['image'];
                        if(!file_exists($img_path)) $img_path = 'https://via.placeholder.com/150x80/2d6a4f/ffffff?text=No+Image';
                        ?>
                        <img src="<?= $img_path ?>" style="width:150px; height:80px; object-fit:cover; border-radius:8px;">
                        <p style="font-size:0.7rem; color:var(--text-light); text-align:center; margin-top:0.3rem;">বর্তমান ছবি</p>
                    </div>
                    <div style="flex:1;">
                        <input type="file" name="image" accept="image/*" style="width:100%; color:var(--text-light); cursor:pointer;">
                        <p style="font-size:0.8rem; color:var(--text-light); margin-top:0.5rem; opacity:0.6;">পরিবর্তন করতে চাইলে নতুন ছবি সিলেক্ট করুন</p>
                    </div>
                </div>
            </div>
            
            <!-- Published Toggle -->
            <div style="grid-column: 1/-1; background:rgba(255,255,255,0.02); padding:1rem; border-radius:12px; display:flex; align-items:center; gap:0.75rem; border:1px solid var(--glass-border);">
                <input type="checkbox" name="is_published" id="published" value="1" <?= $blog['is_published'] ? 'checked' : '' ?> style="width:20px; height:20px; cursor:pointer; accent-color:var(--accent);">
                <label for="published" style="color:var(--white); cursor:pointer; font-weight:500;">প্রকাশিত (Published)</label>
            </div>
        </div>
        
        <div style="padding-top:1.5rem; border-top:1px solid var(--glass-border);">
            <button type="submit" class="btn-sm" style="padding:1.2rem; font-size:1.1rem; width:100%; justify-content:center; box-shadow:0 10px 20px rgba(0,0,0,0.2);">
                <i class="fa-solid fa-cloud-arrow-up"></i> পোস্ট আপডেট করুন
            </button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
