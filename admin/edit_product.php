<?php require_once __DIR__ . '/includes/header.php';

// Check ID
if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit;
}

$id = (int) $_GET['id'];

// Fetch product data
try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if (!$product) {
        header("Location: products.php");
        exit;
    }
} catch (Exception $e) {
    die("Error fetching product: " . $e->getMessage());
}

// Update logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $unit = $_POST['unit'];
    $category_id = $_POST['category_id'];
    $description = $_POST['description'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    // Keep existing image by default
    $image_name = $product['image'];
    
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
        
        // Try to delete old image if it's not the default
        if($product['image'] !== 'default.jpg' && file_exists($upload_dir . $product['image'])) {
            unlink($upload_dir . $product['image']);
        }
        
        move_uploaded_file($tmp_name, $upload_dir . $image_name);
    }

    try {
        $stmt = $pdo->prepare("
            UPDATE products 
            SET name=?, price=?, stock=?, unit=?, category_id=?, description=?, image=?, is_featured=?
            WHERE id=?
        ");
        $stmt->execute([$name, $price, $stock, $unit, $category_id, $description, $image_name, $is_featured, $id]);

        echo "<script>alert('পণ্য সফলভাবে আপডেট হয়েছে!'); window.location.href='products.php';</script>";
        exit;
    } catch (Exception $e) {
        $error = "আপডেট করতে সমস্যা হয়েছে: " . $e->getMessage();
    }
}

// Fetch categories
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>

<div style="margin-bottom:2.5rem; display:flex; justify-content:space-between; align-items:center;">
    <div>
        <h1 style="font-size:2rem; margin-bottom:0.5rem;">পণ্য এডিট করুন</h1>
        <p style="color:var(--text-light); opacity:0.8;">পণ্যের তথ্য এবং ছবি পরিবর্তন করুন</p>
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
            
            <div style="grid-column: 1/-1;">
                <label style="display:block; margin-bottom:0.75rem; color:var(--white); font-weight:600; font-family:'Outfit',sans-serif;">পণ্যের নাম *</label>
                <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required style="width:100%; padding:1rem; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:12px; color:var(--white); outline:none; font-size:1rem; transition:var(--transition);">
            </div>

            <div>
                <label style="display:block; margin-bottom:0.75rem; color:var(--white); font-weight:600; font-family:'Outfit',sans-serif;">মূল্য (৳) *</label>
                <div style="position:relative;">
                    <span style="position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:var(--accent);">৳</span>
                    <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" required style="width:100%; padding:1rem 1rem 1rem 2.2rem; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:12px; color:var(--white); outline:none; font-size:1rem;">
                </div>
            </div>

            <div>
                <label style="display:block; margin-bottom:0.75rem; color:var(--white); font-weight:600; font-family:'Outfit',sans-serif;">স্টক *</label>
                <input type="number" name="stock" value="<?= $product['stock'] ?>" required style="width:100%; padding:1rem; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:12px; color:var(--white); outline:none; font-size:1rem;">
            </div>

            <div>
                <label style="display:block; margin-bottom:0.75rem; color:var(--white); font-weight:600; font-family:'Outfit',sans-serif;">ইউনিট</label>
                <input type="text" name="unit" value="<?= htmlspecialchars($product['unit']) ?>" style="width:100%; padding:1rem; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:12px; color:var(--white); outline:none; font-size:1rem;">
            </div>

            <div>
                <label style="display:block; margin-bottom:0.75rem; color:var(--white); font-weight:600; font-family:'Outfit',sans-serif;">ক্যাটাগরি</label>
                <select name="category_id" style="width:100%; padding:1rem; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:12px; color:var(--white); outline:none; font-size:1rem; cursor:pointer;">
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $product['category_id'] ? 'selected' : '' ?> style="background:#063a24;">
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="grid-column: 1/-1;">
                <label style="display:block; margin-bottom:1rem; color:var(--white); font-weight:600; font-family:'Outfit',sans-serif;">পণ্যের ছবি</label>
                <div style="display:flex; gap:2rem; align-items:center; background:rgba(255,255,255,0.02); padding:1.5rem; border-radius:12px; border:1px solid var(--glass-border);">
                    <div style="text-align:center;">
                        <p style="font-size:0.8rem; color:var(--text-light); margin-bottom:0.5rem; opacity:0.6;">বর্তমান ছবি</p>
                        <img src="../assets/images/<?= htmlspecialchars($product['image']) ?>" alt="Product image" style="width:120px; height:120px; object-fit:cover; border-radius:12px; border:2px solid var(--glass-border); box-shadow:0 10px 20px rgba(0,0,0,0.2);">
                    </div>
                    <div style="flex:1;">
                        <label style="display:block; margin-bottom:0.5rem; color:var(--text-light); font-size:0.9rem;">নতুন ছবি আপলোড করুন</label>
                        <input type="file" name="image" accept="image/*" style="width:100%; padding:0.75rem; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:10px; color:var(--text-light); cursor:pointer;">
                        <p style="font-size:0.75rem; color:var(--text-light); margin-top:0.5rem; opacity:0.5;">পরিবর্তন না করতে চাইলে খালি রাখুন।</p>
                    </div>
                </div>
            </div>

            <div style="grid-column: 1/-1;">
                <label style="display:block; margin-bottom:0.75rem; color:var(--white); font-weight:600; font-family:'Outfit',sans-serif;">বিবরণ</label>
                <textarea name="description" rows="5" placeholder="পণ্যের বিবরণ এখানে লিখুন..." style="width:100%; padding:1rem; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:12px; color:var(--white); outline:none; font-size:1rem; line-height:1.6; resize:vertical;"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
            </div>

            <div style="grid-column: 1/-1; background:rgba(255,255,255,0.02); padding:1rem; border-radius:12px; display:flex; align-items:center; gap:0.75rem; border:1px solid var(--glass-border);">
                <input type="checkbox" name="is_featured" id="featured" value="1" <?= $product['is_featured'] ? 'checked' : '' ?> style="width:20px; height:20px; cursor:pointer; accent-color:var(--accent);">
                <label for="featured" style="color:var(--white); cursor:pointer; font-weight:500;">হোম পেজের "জনপ্রিয় পণ্য" লিস্টে এটি প্রদর্শন করুন (Featured Product)</label>
            </div>
        </div>

        <div style="padding-top:1.5rem; border-top:1px solid var(--glass-border);">
            <button type="submit" class="btn-sm" style="padding:1.2rem; font-size:1.1rem; width:100%; justify-content:center; box-shadow:0 10px 20px rgba(0,0,0,0.2);">
                <i class="fa-solid fa-cloud-arrow-up"></i> আপডেট করুন
            </button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>