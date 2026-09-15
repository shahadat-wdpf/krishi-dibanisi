<?php require_once __DIR__ . '/includes/header.php'; 

// Fetch categories for the select dropdown
$categories = [];
try {
    $categories = $pdo->query("SELECT * FROM categories")->fetchAll();
} catch(Exception $e) {}

// Handle Form Submission
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $category_id = (int)($_POST['category_id'] ?? 0);
    $price = (float)($_POST['price'] ?? 0);
    $unit = $_POST['unit'] ?? 'kg';
    $stock = (int)($_POST['stock'] ?? 0);
    $description = $_POST['description'] ?? '';
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    // Default image name
    $image_name = 'default.jpg';
    
    // Handle Image Upload if provided
    if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['image']['tmp_name'];
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image_name = uniqid('prod_') . '.' . $ext;
        $upload_dir = __DIR__ . '/../assets/images/';
        
        // Ensure folder exists
        if(!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        move_uploaded_file($tmp_name, $upload_dir . $image_name);
    }
    
    try {
        // Check if product with same name already exists
        $check_stmt = $pdo->prepare("SELECT id FROM products WHERE name = ?");
        $check_stmt->execute([$name]);
        if($check_stmt->fetch()) {
            $error = "এই নামে একটি পণ্য ইতিমধ্যে ডাটাবেসে আছে। অনুগ্রহ করে অন্য নাম ব্যবহার করুন।";
        } else {
            $stmt = $pdo->prepare("INSERT INTO products (name, category_id, price, unit, stock, description, is_featured, image, farmer_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            // Hardcoding farmer_id=1 as the admin doing this
            $stmt->execute([$name, $category_id, $price, $unit, $stock, $description, $is_featured, $image_name, 1]);
            
            echo "<script>alert('নতুন পণ্য সফলভাবে যোগ করা হয়েছে!'); window.location.href='products.php';</script>";
            exit;
        }
    } catch(Exception $e) {
        $error = "প্রোডাক্ট ডাটাবেসে যোগ করতে সমস্যা হয়েছে: " . $e->getMessage();
    }
}
?>

<div style="margin-bottom:2.5rem; display:flex; justify-content:space-between; align-items:center;">
    <div>
        <h1 style="font-size:2rem; margin-bottom:0.5rem;">নতুন পণ্য যোগ করুন</h1>
        <p style="color:var(--text-light); opacity:0.8;">দোকানে বিক্রয়ের জন্য নতুন আইটেম বা পণ্য লিস্ট করুন</p>
    </div>
    <a href="products.php" class="btn-sm" style="background:rgba(255,255,255,0.1);"><i class="fa-solid fa-arrow-left"></i> ফিরে যান</a>
</div>

<div class="card" style="max-width:850px;">
    <?php if(isset($error)): ?>
        <div style="background:rgba(239, 68, 68, 0.2); color:#f87171; padding:1.2rem; border-radius:var(--radius); margin-bottom:2rem; border:1px solid rgba(239, 68, 68, 0.3);">
            <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="responsive-grid-2">
            <!-- Name -->
            <div style="grid-column: 1/-1;">
                <label style="display:block; margin-bottom:0.75rem; color:var(--white); font-weight:600; font-family:'Outfit',sans-serif;">পণ্যের নাম *</label>
                <input type="text" name="name" placeholder="যেমন: খাঁটি চিনিগুঁড়া চাল" required style="width:100%; padding:1rem; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:12px; color:var(--white); outline:none; font-size:1rem; transition:var(--transition);">
            </div>
            
            <!-- Category -->
            <div>
                <label style="display:block; margin-bottom:0.75rem; color:var(--white); font-weight:600; font-family:'Outfit',sans-serif;">ক্যাটাগরি *</label>
                <select name="category_id" required style="width:100%; padding:1rem; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:12px; color:var(--white); outline:none; font-size:1rem; cursor:pointer;">
                    <option value="" style="background:#063a24;">নির্বাচন করুন</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" style="background:#063a24;"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Unit -->
            <div>
                <label style="display:block; margin-bottom:0.75rem; color:var(--white); font-weight:600; font-family:'Outfit',sans-serif;">পরিমাপের একক *</label>
                <select name="unit" required style="width:100%; padding:1rem; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:12px; color:var(--white); outline:none; font-size:1rem; cursor:pointer;">
                    <option value="kg" style="background:#063a24;">কেজি (kg)</option>
                    <option value="gram" style="background:#063a24;">গ্রাম (g)</option>
                    <option value="liter" style="background:#063a24;">লিটার (L)</option>
                    <option value="piece" style="background:#063a24;">পিস (pcs)</option>
                    <option value="pack" style="background:#063a24;">প্যাকেট (pack)</option>
                </select>
            </div>

            <!-- Price -->
            <div>
                <label style="display:block; margin-bottom:0.75rem; color:var(--white); font-weight:600; font-family:'Outfit',sans-serif;">মূল্য (৳) *</label>
                <div style="position:relative;">
                    <span style="position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:var(--accent);">৳</span>
                    <input type="number" name="price" step="0.01" min="0" required style="width:100%; padding:1rem 1rem 1rem 2.2rem; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:12px; color:var(--white); outline:none; font-size:1rem;">
                </div>
            </div>
            
            <!-- Quantity/Stock -->
            <div>
                <label style="display:block; margin-bottom:0.75rem; color:var(--white); font-weight:600; font-family:'Outfit',sans-serif;">স্টকে পরিমাণ *</label>
                <input type="number" name="stock" min="0" required style="width:100%; padding:1rem; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:12px; color:var(--white); outline:none; font-size:1rem;">
            </div>
            
            <!-- Image -->
            <div style="grid-column: 1/-1;">
                <label style="display:block; margin-bottom:0.75rem; color:var(--white); font-weight:600; font-family:'Outfit',sans-serif;">পণ্যের ছবি (ঐচ্ছিক)</label>
                <div style="padding:1.5rem; background:rgba(255,255,255,0.02); border:2px dashed var(--glass-border); border-radius:12px; text-align:center;">
                    <input type="file" name="image" accept="image/*" style="width:100%; color:var(--text-light); cursor:pointer;">
                    <p style="font-size:0.8rem; color:var(--text-light); margin-top:0.5rem; opacity:0.6;">প্রস্তাবিত সাইজ: ৮০০x৮০০ পিক্সেল (JPG, PNG)</p>
                </div>
            </div>
            
            <!-- Description -->
            <div style="grid-column: 1/-1;">
                <label style="display:block; margin-bottom:0.75rem; color:var(--white); font-weight:600; font-family:'Outfit',sans-serif;">পণ্যের বিবরণ</label>
                <textarea name="description" rows="5" placeholder="পণ্যের গুণাগুণ এবং বিস্তারিত তথ্য এখানে লিখুন..." style="width:100%; padding:1rem; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:12px; color:var(--white); outline:none; font-size:1rem; line-height:1.6; resize:vertical;"></textarea>
            </div>
            
            <!-- Featured Checkbox -->
            <div style="grid-column: 1/-1; background:rgba(255,255,255,0.02); padding:1rem; border-radius:12px; display:flex; align-items:center; gap:0.75rem; border:1px solid var(--glass-border);">
                <input type="checkbox" name="is_featured" id="featured" value="1" style="width:20px; height:20px; cursor:pointer; accent-color:var(--accent);">
                <label for="featured" style="color:var(--white); cursor:pointer; font-weight:500;">হোম পেজের "জনপ্রিয় পণ্য" লিস্টে এটি প্রদর্শন করুন (Featured Product)</label>
            </div>
        </div>
        
        <div style="padding-top:1.5rem; border-top:1px solid var(--glass-border);">
            <button type="submit" class="btn-sm" style="padding:1.2rem; font-size:1.1rem; width:100%; justify-content:center; box-shadow:0 10px 20px rgba(0,0,0,0.2);">
                <i class="fa-solid fa-cloud-arrow-up"></i> নতুন পণ্য যোগ করুন
            </button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
