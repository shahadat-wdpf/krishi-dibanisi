<?php require_once __DIR__ . '/includes/header.php';

// Delete Product Logic
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    try {
        $pdo->query("DELETE FROM products WHERE id = $id");
        echo "<script>alert('পণ্য মুছে ফেলা হয়েছে!'); window.location.href='products.php';</script>";
        exit;
    } catch (Exception $e) {
    }
}

// Fetch Categories for filter dropdown
$categories = [];
try {
    $categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();
} catch (Exception $e) {}

// Get selected category filter
$selected_category = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// Fetch Products (with optional category filter)
$products = [];
try {
    $sql = "SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id";
    
    if ($selected_category > 0) {
        $sql .= " WHERE p.category_id = ?";
        $sql .= " ORDER BY p.id DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$selected_category]);
    } else {
        $sql .= " ORDER BY p.id DESC";
        $stmt = $pdo->query($sql);
    }
    $products = $stmt->fetchAll();
} catch (Exception $e) {
}

// Count all products (for "all" filter badge)
$total_count = 0;
try {
    $total_count = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
} catch (Exception $e) {}
?>

<div style="margin-bottom:2.5rem; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem;">
    <div>
        <h1 style="font-size:2rem; margin-bottom:0.5rem;">পণ্যসমূহ (Products)</h1>
        <p style="color:var(--text-light); opacity:0.8;">ওয়েবসাইটের সকল পণ্য ম্যানেজ করুন</p>
    </div>
    <a href="add_product.php" class="btn-sm" style="padding:0.8rem 1.8rem; font-size:1rem;">
        <i class="fa-solid fa-plus"></i> নতুন পণ্য যোগ করুন
    </a>
</div>

<!-- Category Filter -->
<div style="margin-bottom:1.5rem; display:flex; align-items:center; gap:0.8rem; flex-wrap:wrap;">
    <span style="color:var(--text-light); font-weight:600; margin-right:0.5rem;"><i class="fa-solid fa-filter"></i> ক্যাটাগরি ফিল্টার:</span>
    <a href="products.php" 
       style="padding:0.5rem 1.2rem; border-radius:20px; font-size:0.85rem; text-decoration:none; transition:all 0.3s;
              <?= $selected_category == 0 ? 'background:var(--primary); color:var(--white); font-weight:700;' : 'background:rgba(255,255,255,0.05); color:var(--text-light); border:1px solid rgba(255,255,255,0.1);' ?>">
        সব পণ্য (<?= $total_count ?>)
    </a>
    <?php foreach($categories as $cat): ?>
        <a href="products.php?category=<?= $cat['id'] ?>" 
           style="padding:0.5rem 1.2rem; border-radius:20px; font-size:0.85rem; text-decoration:none; transition:all 0.3s;
                  <?= $selected_category == $cat['id'] ? 'background:var(--primary); color:var(--white); font-weight:700;' : 'background:rgba(255,255,255,0.05); color:var(--text-light); border:1px solid rgba(255,255,255,0.1);' ?>">
            <?= htmlspecialchars($cat['name']) ?>
        </a>
    <?php endforeach; ?>
</div>

<div class="card">
    <?php if (empty($products)): ?>
        <p style="color:var(--text-light); padding:1rem 0; opacity:0.6;">
            <?= $selected_category > 0 ? 'এই ক্যাটাগরিতে কোনো পণ্য পাওয়া যায়নি।' : 'কোনো পণ্য পাওয়া যায়নি।' ?>
        </p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>আইডি</th>
                    <th>ছবি</th>
                    <th>নাম</th>
                    <th>ক্যাটাগরি</th>
                    <th>মূল্য</th>
                    <th>স্টক</th>
                    <th>অ্যাকশন</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td style="font-weight:700; color:var(--white);">#<?= $product['id'] ?></td>
                        <td>
                            <?php 
                                $thumb_path = file_exists('../assets/images/'.$product['image']) ? '../assets/images/'.$product['image'] : '../assets/images/'.$product['image'];
                                if(!file_exists($thumb_path) || $product['image'] == 'default.jpg') {
                                    $thumb_path = 'https://via.placeholder.com/40x40/063a24/ffffff?text=' . urlencode(mb_substr($product['name'],0,1));
                                }
                            ?>
                            <img src="<?= $thumb_path ?>" style="width:45px; height:45px; object-fit:cover; border-radius:8px; border:1px solid var(--glass-border);" alt="Thumb">
                        </td>
                        <td style="color:var(--white); font-weight:600;"><?= htmlspecialchars($product['name']) ?></td>
                        <td>
                            <a href="products.php?category=<?= $product['category_id'] ?>" style="text-decoration:none;">
                                <span class="badge" style="background: rgba(245, 183, 89, 0.1); color:var(--accent); border: 1px solid rgba(245, 183, 89, 0.2); cursor:pointer;">
                                    <?= htmlspecialchars($product['category_name']) ?>
                                </span>
                            </a>
                        </td>
                        <td style="font-weight:700; color:var(--primary-light);">৳ <?= number_format($product['price'], 2) ?></td>
                        <td>
                            <?php if ($product['stock'] > 0): ?>
                                <span class="badge bg-green">
                                    <i class="fa-solid fa-check"></i> <?= $product['stock'] ?> <?= htmlspecialchars($product['unit']) ?>
                                </span>
                            <?php else: ?>
                                <span class="badge bg-red">আউট অফ স্টক</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display:flex; gap:0.5rem;">
                                <a href="edit_product.php?id=<?= $product['id'] ?>" class="btn-sm" style="background:#3b82f6; width:35px; height:35px; padding:0; justify-content:center;">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <a href="products.php?delete=<?= $product['id'] ?><?= $selected_category > 0 ? '&category='.$selected_category : '' ?>" class="btn-sm" style="background:#ef4444; width:35px; height:35px; padding:0; justify-content:center;"
                                    onclick="return confirm('আপনি কি নিশ্চিত এটি মুছতে চান?');">
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