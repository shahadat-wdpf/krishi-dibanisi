<?php
require_once __DIR__ . '/includes/header.php';

$cart_items = $_SESSION['cart'] ?? [];
$products = [];
$total_price = 0;

if(!empty($cart_items)) {
    try {
        $placeholders = str_repeat('?,', count($cart_items) - 1) . '?';
        $ids = array_keys($cart_items);
        
        $stmt = $pdo->prepare("SELECT id, name, price, image, unit, stock FROM products WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        $products = $stmt->fetchAll();
    } catch(Exception $e) {}
}
?>

<style>
    .cart-card {
        background: var(--card-bg);
        padding: 2rem;
        border-radius: var(--radius);
        box-shadow: var(--card-shadow);
        border: 1px solid rgba(255, 255, 255, 0.06);
    }
    .qty-btn {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: var(--white);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        font-weight: bold;
        transition: var(--transition);
    }
    .qty-btn:hover {
        background: var(--primary);
        border-color: var(--primary);
        color: var(--white);
    }
    .cart-table th {
        padding: 1rem;
        font-family: 'Outfit', sans-serif;
        color: var(--white);
        font-weight: 700;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        text-transform: uppercase;
        font-size: 0.82rem;
        letter-spacing: 0.5px;
    }
    .cart-table td {
        padding: 1.1rem 1rem;
        border-bottom: 1px dashed rgba(255, 255, 255, 0.06);
        vertical-align: middle;
    }
</style>

<section class="section" style="min-height: 65vh; background: var(--bg-color);">
    <div style="max-width: 1050px; margin: 0 auto;">
        
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 style="color: var(--white); font-family: 'Outfit', sans-serif; font-size: 2rem; margin: 0; display: flex; align-items: center; gap: 0.75rem;">
                    <i class="fa-solid fa-basket-shopping" style="color: var(--accent);"></i> আপনার শপিং কার্ট
                </h2>
                <p style="color: var(--text-light); font-size: 0.95rem; margin-top: 0.3rem;">আপনার ঝুড়িতে যুক্ত হওয়া পণ্যসমূহ এবং সামারি</p>
            </div>
            <a href="products.php" class="btn btn-outline" style="font-size: 0.9rem; padding: 0.6rem 1.2rem;">
                <i class="fa-solid fa-plus"></i> আরো পণ্য যোগ করুন
            </a>
        </div>
        
        <?php if(empty($products)): ?>
            <div class="cart-card" style="text-align: center; padding: 4rem 2rem;">
                <div style="width: 80px; height: 80px; background: rgba(245, 158, 11, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; color: var(--accent); font-size: 2.5rem;">
                    <i class="fa-solid fa-cart-arrow-down"></i>
                </div>
                <h2 style="color: var(--white); margin-bottom: 0.8rem; font-family: 'Outfit', sans-serif;">আপনার শপিং কার্ট খালি!</h2>
                <p style="color: var(--text-light); margin-bottom: 2rem; max-width: 500px; margin-left: auto; margin-right: auto;">
                    আপনি এখনো কোনো পণ্য আপনার কার্টে যোগ করেননি। আমাদের খাঁটি ও তাজা অর্গানিক কৃষিপণ্যগুলো ঘুরে দেখুন।
                </p>
                <a href="products.php" class="btn" style="padding: 0.8rem 2rem;">
                    <i class="fa-solid fa-store"></i> পণ্য কেনাকাটা শুরু করুন
                </a>
            </div>
        <?php else: ?>
            
            <!-- Promo Announcement Banner -->
            <div style="background: rgba(16, 185, 129, 0.12); border: 1px dashed rgba(16, 185, 129, 0.3); border-radius: var(--radius); padding: 1.2rem 1.5rem; margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <i class="fa-solid fa-tags" style="font-size: 1.8rem; color: var(--accent);"></i>
                    <div>
                        <strong style="color: var(--white); font-size: 1rem; display: block; font-family: 'Outfit', sans-serif;">বিশেষ প্রোমো কোড প্রযোজ্য!</strong>
                        <span style="color: var(--text-light); font-size: 0.9rem;">চেকআউটে কুপন কোড <strong style="color:var(--accent); font-family:'Outfit',sans-serif;">FIRST20</strong> ব্যবহার করে পান বিশেষ ছাড়!</span>
                    </div>
                </div>
            </div>

            <div class="grid-cart" style="display: grid; grid-template-columns: 1fr 340px; gap: 2rem;">
                
                <!-- Cart Items List Table -->
                <div class="cart-card table-responsive" style="padding: 1.5rem;">
                    <table class="cart-table" style="width: 100%; border-collapse: collapse; text-align: left; color: var(--text-dark);">
                        <thead>
                            <tr>
                                <th>পণ্য</th>
                                <th>পরিমাণ</th>
                                <th>একক মূল্য</th>
                                <th>মোট</th>
                                <th style="text-align: right;">মুছুন</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($products as $p): 
                                $qty = $cart_items[$p['id']] ?? 1;
                                $subtotal = $p['price'] * $qty;
                                $total_price += $subtotal;

                                $img = $p['image'];
                                $cart_img = 'assets/images/default.jpg';
                                if(!empty($img) && strpos($img, 'http') === 0) {
                                    $cart_img = $img;
                                } else if(!empty($img) && file_exists(__DIR__ . '/assets/images/' . $img)) {
                                    $cart_img = 'assets/images/' . $img;
                                }
                            ?>
                            <tr>
                                <td style="display: flex; align-items: center; gap: 1rem;">
                                    <img src="<?= htmlspecialchars($cart_img) ?>" onerror="this.src='assets/images/default.jpg'" alt="<?= htmlspecialchars($p['name']) ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.3);">
                                    <div>
                                        <strong style="color: var(--white); display: block; font-size: 1rem; margin-bottom: 2px;"><?= htmlspecialchars($p['name']) ?></strong>
                                        <span style="color: var(--text-light); font-size: 0.8rem;"><?= htmlspecialchars($p['unit']) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <a href="update_cart.php?id=<?= $p['id'] ?>&action=decrease" class="qty-btn" title="কমান">-</a>
                                        <span style="font-weight: bold; color: var(--white); width: 28px; text-align: center; display: inline-block; font-family: 'Outfit', sans-serif;"><?= $qty ?></span>
                                        <a href="update_cart.php?id=<?= $p['id'] ?>&action=increase" class="qty-btn" title="বাড়ান">+</a>
                                    </div>
                                </td>
                                <td style="color: var(--text-light); font-weight: 500;">
                                    ৳ <?= number_format($p['price'], 2) ?>
                                </td>
                                <td style="font-weight: 700; color: #34d399; font-family: 'Outfit', sans-serif;">
                                    ৳ <?= number_format($subtotal, 2) ?>
                                </td>
                                <td style="text-align: right;">
                                    <a href="remove_from_cart.php?id=<?= $p['id'] ?>" style="color: #f87171; font-size: 1.1rem; padding: 0.4rem; border-radius: 6px; transition: var(--transition);" title="রিমুভ করুন">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Order Summary Side Card -->
                <div class="cart-card" style="height: fit-content; position: sticky; top: 90px;">
                    <h3 style="margin-bottom: 1.5rem; color: var(--white); border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 1rem; font-family: 'Outfit', sans-serif; display: flex; align-items: center; gap: 0.6rem;">
                        <i class="fa-solid fa-receipt" style="color: var(--primary-light);"></i> অর্ডার সামারি
                    </h3>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; color: var(--text-light); font-size: 0.95rem;">
                        <span>পণ্যসমূহের দাম (Subtotal)</span>
                        <span style="color: var(--white); font-weight: 600;">৳ <?= number_format($total_price, 2) ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1.2rem; color: var(--text-light); font-size: 0.95rem;">
                        <span>ডেলিভারি চার্জ</span>
                        <span style="color: var(--white); font-weight: 600;">৳ 60.00</span>
                    </div>
                    
                    <?php $grand_total = $total_price + 60; ?>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1.8rem; color: var(--white); font-size: 1.3rem; font-weight: 800; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1.2rem; font-family: 'Outfit', sans-serif;">
                        <span>সর্বমোট</span>
                        <span style="color: #34d399;">৳ <?= number_format($grand_total, 2) ?></span>
                    </div>
                    
                    <a href="checkout.php" class="btn" style="width: 100%; justify-content: center; padding: 0.9rem; font-size: 1.05rem;">
                        <i class="fa-solid fa-credit-card"></i> চেকআউট করুন
                    </a>
                </div>
                
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
