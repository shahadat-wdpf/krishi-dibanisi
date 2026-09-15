<?php
require_once __DIR__ . '/includes/header.php';

$category_slug = $_GET['category'] ?? '';

// Fetch query based on category or all
$query_str = "
    SELECT p.*, c.name as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id
";
$params = [];

if($category_slug) {
    $query_str .= " WHERE c.slug = ?";
    $params[] = $category_slug;
}

$query_str .= " ORDER BY p.id DESC";

$products = [];
try {
    $stmt = $pdo->prepare($query_str);
    $stmt->execute($params);
    $products = $stmt->fetchAll();
} catch(Exception $e) {}

// Category placeholder images
$category_images = [
    1 => 'https://images.unsplash.com/photo-1586201375761-83865001e8ac?q=80&w=400&auto=format&fit=crop', // চাল ও ডাল
    2 => 'https://images.unsplash.com/photo-1566385101042-1a0aa0c1268c?q=80&w=400&auto=format&fit=crop', // শাকসবজি
    3 => 'https://images.unsplash.com/photo-1610832958506-aa56368176cf?q=80&w=400&auto=format&fit=crop', // ফলমূল
    4 => 'https://images.unsplash.com/photo-1587049352847-4d4b124041d8?q=80&w=400&auto=format&fit=crop', // মধু ও ঘি
    5 => 'https://images.unsplash.com/photo-1516280030429-27679b3dc9ec?q=80&w=400&auto=format&fit=crop', // দেশি হাঁস-মুরগি
    6 => 'https://images.unsplash.com/photo-1506976694689-ffacb6b29efb?q=80&w=400&auto=format&fit=crop', // ডিম
    7 => 'https://images.unsplash.com/photo-1603048297172-c92544798d5e?q=80&w=400&auto=format&fit=crop', // গরু-ছাগলের মাংস
    8 => 'https://images.unsplash.com/photo-1566318536130-10f8454b600d?q=80&w=400&auto=format&fit=crop', // দেশি মাছ
    9 => 'https://images.unsplash.com/photo-1628088062854-d1870b4553da?q=80&w=400&auto=format&fit=crop'  // ডেইরি পণ্য
];

// Current category name for header
$current_category_name = '';
if($category_slug && !empty($products)) {
    $current_category_name = $products[0]['category_name'];
}
?>

<!-- Header Section -->
<section style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); padding: 6rem 5% 4rem; color: white; text-align:center; position:relative;">
    <h1 style="font-family:'Outfit',sans-serif; font-size:3.5rem; font-weight:800; color:var(--white); margin-bottom:1rem; position:relative; z-index:2;">
        <?= $category_slug ? htmlspecialchars($current_category_name) : 'সকল ফ্রেশ পণ্য' ?>
    </h1>
    <p style="font-size:1.2rem; opacity:0.9; max-width:600px; margin:0 auto; position:relative; z-index:2;">তাজা এবং ভেজালমুক্ত পণ্য সরাসরি আপনার জন্য</p>
    
    <!-- Light organic circles for decoration -->
    <div style="position:absolute; top:20px; left:10%; width:150px; height:150px; border-radius:50%; background:var(--accent); opacity:0.2; filter:blur(40px); z-index:1;"></div>
    <div style="position:absolute; bottom:20px; right:10%; width:200px; height:200px; border-radius:50%; background:#a3e635; opacity:0.1; filter:blur(50px); z-index:1;"></div>
</section>

<!-- Products Section -->
<section class="section">
    <div style="display:flex; justify-content:space-between; margin-bottom:3rem; align-items:center; flex-wrap:wrap; gap:1.5rem;">
        <div style="font-weight:600; color:var(--text-dark); font-size:1.1rem;">
            সর্বমোট <span style="color:var(--white); font-size:1.3rem; margin:0 5px;"><?= count($products) ?></span> টি পণ্য পাওয়া গেছে
        </div>
        <div>
            <select class="modern-input" style="width:250px; cursor:pointer;" onchange="location = this.value;">
                <option value="products.php">সব ক্যাটাগরি</option>
                <?php
                try {
                    $cats = $pdo->query("SELECT * FROM categories")->fetchAll();
                    foreach($cats as $cat) {
                        $selected = ($category_slug === $cat['slug']) ? 'selected' : '';
                        echo "<option value='products.php?category={$cat['slug']}' {$selected}>{$cat['name']}</option>";
                    }
                } catch(Exception $e) {}
                ?>
            </select>
        </div>
    </div>

    <div class="product-grid">
        <?php if($products): ?>
            <?php foreach($products as $product): ?>
                <div class="product-card"
                     data-product-id="<?= $product['id'] ?>"
                     data-product-name="<?= htmlspecialchars($product['name']) ?>"
                     data-product-desc="<?= htmlspecialchars($product['description']) ?>"
                     data-product-price="<?= number_format($product['price'], 2) ?>"
                     data-product-unit="<?= htmlspecialchars($product['unit']) ?>"
                     data-product-category="<?= htmlspecialchars($product['category_name']) ?>"
                     data-product-stock="<?= $product['stock'] ?>"
                     data-product-featured="<?= $product['is_featured'] ?>"
                     data-product-img="<?php                             $img_src = '';
                             if (!empty($product['image']) && (strpos($product['image'], 'http') === 0 || file_exists(__DIR__ . '/assets/images/' . $product['image']))) {
                                 $img_src = (strpos($product['image'], 'http') === 0) ? $product['image'] : 'assets/images/' . $product['image'];
                             } else {
                                 $cat_id = $product['category_id'] ?? 1;
                                 $img_src = $category_images[$cat_id] ?? 'https://images.unsplash.com/photo-1628102491629-77858ab5721f?q=80&w=400&auto=format&fit=crop';
                             }
                             echo htmlspecialchars($img_src);
                     ?>">
                    <?php if($product['is_featured']): ?>
                        <span class="badge-featured">Popular</span>
                    <?php endif; ?>
                    
                    <div class="product-img-wrapper">
                        <img src="<?= htmlspecialchars($img_src) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-img">
                    </div>
                    
                    <div class="product-content">
                        <div class="product-category"><?= htmlspecialchars($product['category_name']) ?></div>
                        <h3 class="product-title"><?= htmlspecialchars($product['name']) ?></h3>
                        <p style="color:var(--text-light); font-size:0.95rem; margin-bottom:1rem; min-height:45px; line-height:1.6;">
                            <?= htmlspecialchars(mb_strimwidth($product['description'], 0, 60, '...')) ?>
                        </p>
                        <div class="product-price">৳ <?= number_format($product['price'], 2) ?> <span>/<?= htmlspecialchars($product['unit']) ?></span></div>
                        
                        <?php if($product['stock'] > 0): ?>
                            <div style="font-size:0.85rem; color:var(--primary-light); font-weight:600; margin-bottom:1rem; flex-grow:1;"><i class="fa-solid fa-circle-check"></i> স্টকে আছে (<?= $product['stock'] ?>)</div>
                        <?php else: ?>
                            <div style="font-size:0.85rem; color:#dc2626; font-weight:600; margin-bottom:1rem; flex-grow:1;"><i class="fa-solid fa-circle-xmark"></i> আউট অফ স্টক</div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="product-card-actions">
                        <button class="add-to-cart-seamless add-to-cart" data-id="<?= $product['id'] ?>" <?= $product['stock'] <= 0 ? 'disabled style="opacity:0.6;background:#9ca3af;"' : '' ?>>
                            <i class="fa-solid fa-cart-plus"></i> কার্টে যোগ করুন
                        </button>
                        <button class="btn-details" onclick="openProductModal(this.closest('.product-card'))">
                            <i class="fa-solid fa-eye"></i> বিস্তারিত
                        </button>
                    </div>
                    
                </div>
            <?php endforeach; ?>
        <?php else: ?>
             <div style="grid-column:1/-1; padding:5rem; background:var(--card-bg); border-radius:var(--radius); box-shadow:var(--card-shadow); border: 1px solid rgba(255,255,255,0.05); text-align:center;">
                 <i class="fa-solid fa-basket-shopping" style="font-size:4rem; color:var(--white); opacity:0.3; margin-bottom:1rem;"></i>
                 <h3 style="color:var(--white); font-size:1.8rem;">দুঃখিত!</h3>
                 <p style="color:var(--text-light); font-size:1.1rem; margin-top:0.5rem;">এই ক্যাটাগরিতে কোনো পণ্য পাওয়া যায়নি।</p>
             </div>
        <?php endif; ?>
    </div>
</section>

<!-- Product Detail Modal -->
<div class="product-modal-overlay" id="productModalOverlay">
    <div class="product-modal">
        <button class="modal-close-btn" id="modalCloseBtn"><i class="fa-solid fa-xmark"></i></button>
        <div class="modal-image-section">
            <span class="modal-image-badge" id="modalBadge" style="display:none;">Popular</span>
            <img src="" alt="" id="modalProductImg">
        </div>
        <div class="modal-info-section">
            <div class="modal-category-label" id="modalCategory"></div>
            <h2 class="modal-product-name" id="modalProductName"></h2>
            <div class="modal-divider"></div>
            <p class="modal-description" id="modalDescription"></p>
            <div class="modal-price-section">
                <span class="modal-price" id="modalPrice"></span>
                <span class="modal-price-unit" id="modalUnit"></span>
            </div>
            <div class="modal-stock-info" id="modalStock"></div>
            <button class="modal-cart-btn add-to-cart" id="modalCartBtn" data-id="">
                <i class="fa-solid fa-cart-plus"></i> কার্টে যোগ করুন
            </button>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
