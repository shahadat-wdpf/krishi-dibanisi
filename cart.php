<?php
require_once __DIR__ . '/includes/header.php';

$cart_items = $_SESSION['cart'] ?? [];
$products = [];
$total_price = 0;

if(!empty($cart_items)) {
    try {
        // Build placeholders like ?,?,?
        $placeholders = str_repeat('?,', count($cart_items) - 1) . '?';
        $ids = array_keys($cart_items);
        
        $stmt = $pdo->prepare("SELECT id, name, price, image, unit FROM products WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        $products = $stmt->fetchAll();
    } catch(Exception $e) {}
}
?>

<section class="section" style="min-height: 60vh; background: var(--bg-color);">
    <div style="max-width:1000px; margin:0 auto;">
        <h2 style="color:var(--white); margin-bottom:2rem;">আপনার শপিং কার্ট</h2>
        
        <?php if(empty($products)): ?>
            <div style="background:var(--card-bg); padding:3rem; border-radius:var(--radius); box-shadow:var(--card-shadow); border: 1px solid rgba(255,255,255,0.05); text-align:center;">
                <h2 style="color:var(--white); margin-bottom:1rem;"><i class="fa-solid fa-basket-shopping" style="font-size:3rem;"></i><br>আপনার শপিং কার্ট খালি!</h2>
                <p style="color:var(--text-light); margin-bottom:2rem;">আপনি এখনো কোনো পণ্য আপনার কার্টে যোগ করেননি। আমাদের খাঁটি ও তাজা পণ্যগুলো ঘুরে দেখুন।</p>
                <a href="products.php" class="btn">পণ্য কেনাকাটা শুরু করুন</a>
            </div>
        <?php else: ?>
            <!-- Active Promos Notice -->
            <div style="background: rgba(64, 145, 108, 0.15); border: 1px dashed var(--primary-light); border-radius: var(--radius); padding: 1.5rem; margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <i class="fa-solid fa-tags" style="font-size: 2rem; color: var(--accent);"></i>
                    <div>
                        <strong style="color: var(--white); font-size: 1.1rem; display: block; font-family: 'Outfit', sans-serif;">চেকআউট পেজে ডিসকাউন্ট উপভোগ করুন!</strong>
                        <span style="color: var(--text-light); font-size: 0.95rem;">নতুন গ্রাহক হলে <strong style="color:var(--accent); letter-spacing: 1px;">FIRST20</strong> ব্যবহার করুন (২০% ছাড়)। অথবা ১৫০০ টাকার কেনাকাটায় <strong style="color:var(--accent); letter-spacing: 1px;">SAVE50</strong> ব্যবহার করে পান ৫০ ৳ ক্যাশব্যাক।</span>
                    </div>
                </div>
            </div>

            <div class="grid-cart">
                
                <!-- Cart Items Table -->
                <div class="table-responsive" style="background:var(--card-bg); padding:2rem; border-radius:var(--radius); box-shadow:var(--card-shadow); border: 1px solid rgba(255,255,255,0.05);">
                    <table style="width:100%; border-collapse: collapse; text-align:left; color:var(--text-dark);">
                        <thead>
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.1);">
                                <th style="padding:1rem;">পণ্য</th>
                                <th style="padding:1rem;">পরিমাণ</th>
                                <th style="padding:1rem;">মূল্য</th>
                                <th style="padding:1rem;">মোট</th>
                                <th style="padding:1rem;">মুছুন</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($products as $p): 
                                $qty = $cart_items[$p['id']];
                                $subtotal = $p['price'] * $qty;
                                $total_price += $subtotal;
                            ?>
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.05);">
                                <td style="padding:1rem; display:flex; align-items:center; gap:1rem;">
                                    <?php
                                        $cart_img = (!empty($p['image']) && file_exists(__DIR__ . '/assets/images/' . $p['image'])) 
                                            ? 'assets/images/' . $p['image'] 
                                            : 'assets/images/default.jpg';
                                    ?>
                                    <img src="<?= htmlspecialchars($cart_img) ?>" alt="<?= htmlspecialchars($p['name']) ?>" style="width:45px; height:45px; object-fit:cover; border-radius:8px; border:1px solid rgba(255,255,255,0.1);">
                                    <strong><?= htmlspecialchars($p['name']) ?></strong>
                                </td>
                                <td style="padding:1rem;"><?= $qty ?> <?= htmlspecialchars($p['unit']) ?></td>
                                <td style="padding:1rem;">৳ <?= number_format($p['price'], 2) ?></td>
                                <td style="padding:1rem; font-weight:600;">৳ <?= number_format($subtotal, 2) ?></td>
                                <td style="padding:1rem;">
                                    <a href="remove_from_cart.php?id=<?= $p['id'] ?>" style="color:red; font-size:1.25rem;"><i class="fa-solid fa-trash-can"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Order Summary -->
                <div style="background:var(--card-bg); padding:2rem; border-radius:var(--radius); box-shadow:var(--card-shadow); border: 1px solid rgba(255,255,255,0.05); height:fit-content;">
                    <h3 style="margin-bottom:1.5rem; color:var(--white); border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:1rem;">অর্ডার সামারি</h3>
                    <div style="display:flex; justify-content:space-between; margin-bottom:1rem; color:var(--text-light);">
                        <span>সাবটোটাল</span>
                        <span>৳ <?= number_format($total_price, 2) ?></span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:1rem; color:var(--text-light);">
                        <span>ডেলিভারি চার্জ</span>
                        <span>৳ 60.00</span>
                    </div>
                    <?php $grand_total = $total_price + 60; ?>
                    <div style="display:flex; justify-content:space-between; margin-bottom:2rem; color:var(--white); font-size:1.25rem; font-weight:700; border-top:1px solid rgba(255,255,255,0.1); padding-top:1rem;">
                        <span>সর্বমোট</span>
                        <span>৳ <?= number_format($grand_total, 2) ?></span>
                    </div>
                    
                    <a href="checkout.php" class="btn" style="width:100%; justify-content:center; padding:1rem;">চেকআউট করুন</a>
                </div>
                
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
