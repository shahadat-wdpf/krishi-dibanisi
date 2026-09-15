<?php require_once __DIR__ . '/includes/header.php';

// Handle Form Submission
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $content = $_POST['content'] ?? '';
    $excerpt = trim($_POST['excerpt'] ?? '');
    $type = $_POST['type'] ?? 'farming_tips';
    $author = trim($_POST['author'] ?? 'কৃষি দিবানিশি');
    $tags = trim($_POST['tags'] ?? '');
    $is_published = isset($_POST['is_published']) ? 1 : 0;
    
    // Auto-generate slug if empty
    if(empty($slug)) {
        $slug = strtolower(preg_replace('/[^a-zA-Z0-9\x{0980}-\x{09FF}]+/u', '-', $title));
        $slug = trim($slug, '-');
        if(empty($slug)) $slug = 'post-' . time();
    }
    
    // Default image
    $image_name = 'default_blog.jpg';
    
    // Handle Image Upload
    if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['image']['tmp_name'];
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image_name = uniqid('blog_') . '.' . $ext;
        $upload_dir = __DIR__ . '/../assets/images/';
        
        if(!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        move_uploaded_file($tmp_name, $upload_dir . $image_name);
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO blogs (title, slug, content, excerpt, image, type, author, tags, is_published) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$title, $slug, $content, $excerpt, $image_name, $type, $author, $tags, $is_published]);
        
        echo "<script>alert('নতুন ব্লগ পোস্ট সফলভাবে প্রকাশিত হয়েছে!'); window.location.href='blogs.php';</script>";
        exit;
    } catch(Exception $e) {
        $error = "ব্লগ পোস্ট সেভ করতে সমস্যা হয়েছে: " . $e->getMessage();
    }
}
?>

<div style="margin-bottom:2.5rem; display:flex; justify-content:space-between; align-items:center;">
    <div>
        <h1 style="font-size:2rem; margin-bottom:0.5rem;">নতুন পোস্ট লিখুন</h1>
        <p style="color:var(--text-light); opacity:0.8;">কৃষি টিপস বা কৃষকের গল্প শেয়ার করুন</p>
    </div>
    <a href="blogs.php" class="btn-sm" style="background:rgba(255,255,255,0.1);"><i class="fa-solid fa-arrow-left"></i> ফিরে যান</a>
</div>

<div class="card" style="max-width:950px;">
    <?php if(isset($error)): ?>
        <div style="background:rgba(239, 68, 68, 0.2); color:#f87171; padding:1.2rem; border-radius:var(--radius); margin-bottom:2rem; border:1px solid rgba(239, 68, 68, 0.3);">
            <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="responsive-grid-2">
            
            <!-- Title -->
            <div style="grid-column: 1/-1;">
                <label style="display:block; margin-bottom:0.75rem; color:var(--white); font-weight:600; font-family:'Outfit',sans-serif;">পোস্টের শিরোনাম *</label>
                <input type="text" name="title" placeholder="যেমন: ধান চাষের সঠিক সময় ও পদ্ধতি" required style="width:100%; padding:1rem; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:12px; color:var(--white); outline:none; font-size:1rem; transition:var(--transition);">
            </div>
            
            <!-- Slug -->
            <div>
                <label style="display:block; margin-bottom:0.75rem; color:var(--white); font-weight:600; font-family:'Outfit',sans-serif;">স্লাগ (URL) <small style="color:var(--text-light); opacity:0.6;">— খালি রাখলে স্বয়ংক্রিয় তৈরি হবে</small></label>
                <input type="text" name="slug" placeholder="dhan-chaser-somoy" style="width:100%; padding:1rem; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:12px; color:var(--white); outline:none; font-size:1rem;">
            </div>
            
            <!-- Type -->
            <div>
                <label style="display:block; margin-bottom:0.75rem; color:var(--white); font-weight:600; font-family:'Outfit',sans-serif;">পোস্টের ধরন *</label>
                <select name="type" required style="width:100%; padding:1rem; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:12px; color:var(--white); outline:none; font-size:1rem; cursor:pointer;">
                    <option value="farming_tips" style="background:#063a24;">🌾 কৃষি টিপস</option>
                    <option value="farmer_story" style="background:#063a24;">👨‍🌾 কৃষকের গল্প</option>
                </select>
            </div>
            
            <!-- Author -->
            <div>
                <label style="display:block; margin-bottom:0.75rem; color:var(--white); font-weight:600; font-family:'Outfit',sans-serif;">লেখক</label>
                <input type="text" name="author" value="কৃষি দিবানিশি" style="width:100%; padding:1rem; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:12px; color:var(--white); outline:none; font-size:1rem;">
            </div>
            
            <!-- Tags -->
            <div>
                <label style="display:block; margin-bottom:0.75rem; color:var(--white); font-weight:600; font-family:'Outfit',sans-serif;">ট্যাগ <small style="color:var(--text-light); opacity:0.6;">— কমা দিয়ে আলাদা করুন</small></label>
                <input type="text" name="tags" placeholder="ধান, চাষ, সার, আমন" style="width:100%; padding:1rem; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:12px; color:var(--white); outline:none; font-size:1rem;">
            </div>
            
            <!-- Excerpt -->
            <div style="grid-column: 1/-1;">
                <label style="display:block; margin-bottom:0.75rem; color:var(--white); font-weight:600; font-family:'Outfit',sans-serif;">সংক্ষিপ্ত বর্ণনা (Excerpt)</label>
                <textarea name="excerpt" rows="3" placeholder="পোস্টের একটি সংক্ষিপ্ত বর্ণনা লিখুন (২-৩ লাইন)..." style="width:100%; padding:1rem; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:12px; color:var(--white); outline:none; font-size:1rem; line-height:1.6; resize:vertical;"></textarea>
            </div>
            
            <!-- Content -->
            <div style="grid-column: 1/-1;">
                <label style="display:block; margin-bottom:0.75rem; color:var(--white); font-weight:600; font-family:'Outfit',sans-serif;">সম্পূর্ণ কন্টেন্ট * <small style="color:var(--text-light); opacity:0.6;">— HTML ট্যাগ ব্যবহার করতে পারবেন (&lt;h3&gt;, &lt;p&gt;, &lt;ul&gt;, &lt;strong&gt;)</small></label>
                <textarea name="content" rows="15" required placeholder="বিস্তারিত কন্টেন্ট এখানে লিখুন...

উদাহরণ:
<h3>প্রথম ধাপ</h3>
<p>এখানে বিস্তারিত লিখুন...</p>
<ul>
<li>পয়েন্ট ১</li>
<li>পয়েন্ট ২</li>
</ul>" style="width:100%; padding:1rem; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:12px; color:var(--white); outline:none; font-size:0.95rem; line-height:1.7; resize:vertical; font-family:'Inter',sans-serif;"></textarea>
            </div>
            
            <!-- Image Upload -->
            <div style="grid-column: 1/-1;">
                <label style="display:block; margin-bottom:0.75rem; color:var(--white); font-weight:600; font-family:'Outfit',sans-serif;">কভার ছবি (ঐচ্ছিক)</label>
                <div style="padding:1.5rem; background:rgba(255,255,255,0.02); border:2px dashed var(--glass-border); border-radius:12px; text-align:center;">
                    <input type="file" name="image" accept="image/*" style="width:100%; color:var(--text-light); cursor:pointer;">
                    <p style="font-size:0.8rem; color:var(--text-light); margin-top:0.5rem; opacity:0.6;">প্রস্তাবিত সাইজ: ১২০০x৬০০ পিক্সেল (JPG, PNG)</p>
                </div>
            </div>
            
            <!-- Published Toggle -->
            <div style="grid-column: 1/-1; background:rgba(255,255,255,0.02); padding:1rem; border-radius:12px; display:flex; align-items:center; gap:0.75rem; border:1px solid var(--glass-border);">
                <input type="checkbox" name="is_published" id="published" value="1" checked style="width:20px; height:20px; cursor:pointer; accent-color:var(--accent);">
                <label for="published" style="color:var(--white); cursor:pointer; font-weight:500;">এখনই প্রকাশ করুন (Publish Now)</label>
            </div>
        </div>
        
        <div style="padding-top:1.5rem; border-top:1px solid var(--glass-border);">
            <button type="submit" class="btn-sm" style="padding:1.2rem; font-size:1.1rem; width:100%; justify-content:center; box-shadow:0 10px 20px rgba(0,0,0,0.2);">
                <i class="fa-solid fa-paper-plane"></i> পোস্ট প্রকাশ করুন
            </button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
