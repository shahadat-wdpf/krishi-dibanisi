<?php
require_once __DIR__ . '/includes/header.php';
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$cart_items = $_SESSION['cart'] ?? [];
if(empty($cart_items)) {
    // If cart empty, redirect
    header("Location: products.php");
    exit();
}

$products = [];
$total_price = 0;
try {
    $placeholders = str_repeat('?,', count($cart_items) - 1) . '?';
    $ids = array_keys($cart_items);
    $stmt = $pdo->prepare("SELECT id, name, price, image FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $products = $stmt->fetchAll();
    
    foreach($products as $p) {
        $total_price += ($p['price'] * $cart_items[$p['id']]);
    }
} catch(Exception $e) {}

$delivery_charge = isset($site_settings['delivery_charge']) ? (float)$site_settings['delivery_charge'] : 60;
$grand_total = $total_price + $delivery_charge;
?>

<section class="section" style="min-height:70vh; background:var(--bg-color);">
    <div style="max-width:1100px; margin:0 auto;">
        
        <form action="process_checkout.php" method="POST" class="grid-checkout">
            
            <!-- Shipping and Payment Details -->
            <div style="display:flex; flex-direction:column; gap:2rem;">
                
                <!-- Shipping Details -->
                <div style="background:var(--card-bg); padding:2rem; border-radius:var(--radius); box-shadow:var(--card-shadow); border: 1px solid rgba(255,255,255,0.05);">
                    <h3 style="margin-bottom:1.5rem; color:var(--white); border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:1rem;">ডেলিভারি ঠিকানা</h3>
                    <div class="grid-2">
                        <div style="grid-column: 1 / -1;">
                            <label style="display:block; margin-bottom:0.5rem; color:var(--text-light);">পুরো নাম *</label>
                            <input type="text" name="full_name" class="modern-input" required>
                        </div>
                        <div>
                            <label style="display:block; margin-bottom:0.5rem; color:var(--text-light);">ফোন নম্বর *</label>
                            <input type="tel" name="phone_number" class="modern-input" required>
                        </div>
                        <div>
                            <label style="display:block; margin-bottom:0.5rem; color:var(--text-light);">ইমেইল (ঐচ্ছিক)</label>
                            <input type="email" name="email" class="modern-input">
                        </div>
                        <div style="grid-column: 1 / -1;">
                            <label style="display:block; margin-bottom:0.5rem; color:var(--text-light);">বিস্তারিত ঠিকানা *</label>
                            <textarea name="shipping_address" rows="3" placeholder="বাড়ি নং, রাস্তা, এলাকা..." class="modern-input" required></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Methods -->
                <div style="background:var(--card-bg); padding:2rem; border-radius:var(--radius); box-shadow:var(--card-shadow); border: 1px solid rgba(255,255,255,0.05);">
                    <h3 style="margin-bottom:1.5rem; color:var(--white); border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:1rem;">পেমেন্ট পদ্ধতি</h3>
                    
                    <div style="display:flex; flex-direction:column; gap:1rem;">
                        <label style="padding:1rem; border:1px solid var(--primary); border-radius:var(--radius); cursor:pointer; display:flex; align-items:center; gap:1rem; background:rgba(43, 122, 11, 0.05);">
                            <input type="radio" name="payment_method" value="Cash on Delivery" checked style="width:18px; height:18px;">
                            <div style="flex:1;">
                                <strong style="display:block; color:var(--white); font-size:1.1rem;">ক্যাশ অন ডেলিভারি (COD)</strong>
                                <span style="color:var(--text-light); font-size:0.9rem;">পণ্য হাতে পেয়ে মূল্য পরিশোধ করুন।</span>
                            </div>
                            <i class="fa-solid fa-hand-holding-dollar" style="font-size:1.5rem; color:var(--primary);"></i>
                        </label>
                        
                        <label style="padding:1rem; border:1px solid rgba(255,255,255,0.2); border-radius:var(--radius); cursor:pointer; display:flex; align-items:center; gap:1rem; transition:var(--transition);" onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.2)'">
                            <input type="radio" name="payment_method" value="Mobile Banking" style="width:18px; height:18px;">
                            <div style="flex:1;">
                                <strong style="display:block; color:var(--white); font-size:1.1rem;">মোবাইল ব্যাংকিং (বিকাশ/নগদ/রকেট)</strong>
                                <span style="color:var(--text-light); font-size:0.9rem;">নিরাপদে মোবাইল ব্যাংকিংয়ের মাধ্যমে পেমেন্ট করুন।</span>
                            </div>
                            <i class="fa-solid fa-mobile-screen-button" style="font-size:1.5rem; color:var(--primary);"></i>
                        </label>
                        
                        <label style="padding:1rem; border:1px solid rgba(255,255,255,0.2); border-radius:var(--radius); cursor:pointer; display:flex; align-items:center; gap:1rem; transition:var(--transition);" onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.2)'">
                            <input type="radio" name="payment_method" value="Credit/Debit Card" style="width:18px; height:18px;">
                            <div style="flex:1;">
                                <strong style="display:block; color:var(--white); font-size:1.1rem;">ক্রেডিট/ডেবিট কার্ড (Visa/Mastercard)</strong>
                                <span style="color:var(--text-light); font-size:0.9rem;">আপনার কার্ড ব্যবহার করে নিরাপদে কিনুন।</span>
                            </div>
                            <i class="fa-regular fa-credit-card" style="font-size:1.5rem; color:var(--primary);"></i>
                        </label>
                    </div>

                    <!-- Mobile Banking Hidden Panel -->
                    <div id="mobileBankingPanel" style="display:none; background:rgba(45, 106, 79, 0.1); border:1px dashed var(--primary); padding:1.5rem; border-radius:12px; margin-top:1.5rem; animation:fadeIn 0.3s ease;">
                        <style>@keyframes fadeIn { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }</style>
                        <p style="color:var(--text-light); margin-bottom:1.5rem; font-size:0.95rem; line-height:1.6;">
                            <strong style="color:var(--accent);">নির্দেশনা:</strong> নিচের নম্বরে সেন্ড মানি (Send Money) করে আপনার পেমেন্ট নম্বর এবং ট্রানজ্যাকশন আইডি (TrxID) নির্দিষ্ট বক্সে দিন।<br>
                            <span style="font-family:'Outfit',sans-serif; color:var(--white); font-size:1.3rem; display:inline-block; margin-top:0.8rem; letter-spacing:1px; background:rgba(0,0,0,0.2); padding:0.5rem 1rem; border-radius:8px;"><i class="fa-solid fa-money-bill-transfer" style="color:#34d399; margin-right:8px;"></i> বিকাশ/নগদ (Personal): <strong style="color:var(--accent);"><?= htmlspecialchars($site_settings['bkash_number'] ?? '+880 171XXXXXXX') ?></strong></span>
                        </p>
                        <div class="grid-2">
                            <div>
                                <label style="display:block; margin-bottom:0.5rem; color:var(--white); font-size:0.95rem;">যে নম্বর থেকে টাকা পাঠিয়েছেন <span style="color:#ef4444;">*</span></label>
                                <input type="text" name="payment_number" id="payment_number" class="modern-input" placeholder="যেমন: 017XXXXXXXX">
                            </div>
                            <div>
                                <label style="display:block; margin-bottom:0.5rem; color:var(--white); font-size:0.95rem;">ট্রানজ্যাকশন আইডি (TrxID) <span style="color:#ef4444;">*</span></label>
                                <input type="text" name="trx_id" id="trx_id" class="modern-input" placeholder="যেমন: 8NXZ2B..." style="text-transform:uppercase;">
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
            
            <!-- Order Summary -->
            <div style="background:var(--card-bg); padding:2rem; border-radius:var(--radius); box-shadow:var(--card-shadow); border: 1px solid rgba(255,255,255,0.05); height:fit-content; position:sticky; top:100px;">
                <h3 style="margin-bottom:1.5rem; color:var(--white); border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:1rem;">অর্ডারের বিবরণ</h3>
                
                <div style="margin-bottom:1.5rem; max-height:250px; overflow-y:auto; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:1rem;">
                    <?php foreach($products as $p): ?>
                        <div style="display:flex; justify-content:space-between; margin-bottom:1rem;">
                            <div style="flex:1;">
                                <div style="color:var(--white); font-weight:500;"><?= htmlspecialchars($p['name']) ?></div>
                                <div style="color:var(--text-light); font-size:0.85rem;">পরিমাণ: <?= $cart_items[$p['id']] ?></div>
                            </div>
                            <div style="color:var(--white); font-weight:600;">
                                ৳ <?= number_format($p['price'] * $cart_items[$p['id']], 2) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div style="display:flex; justify-content:space-between; margin-bottom:1rem; color:var(--text-light);">
                    <span>সাবটোটাল</span>
                    <span>৳ <?= number_format($total_price, 2) ?></span>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom:1rem; color:var(--text-light);">
                    <span>ডেলিভারি চার্জ</span>
                    <span>৳ <?= number_format($delivery_charge, 2) ?></span>
                </div>
                
                <!-- Coupon Section -->
                <div style="margin-bottom:1rem; border-top:1px dashed rgba(255,255,255,0.1); padding-top:1rem;">
                    <label style="display:block; margin-bottom:0.5rem; color:var(--text-light); font-size:0.9rem;">কুপন কোড (যদি থাকে)</label>
                    <div style="display:flex; gap:0.5rem;">
                        <input type="text" id="couponCodeInput" class="modern-input" placeholder="code" style="text-transform:uppercase; flex:1;">
                        <button type="button" class="btn btn-outline" id="applyCouponBtn" style="padding:0.75rem 1rem;">প্রয়োগ করুন</button>
                    </div>
                    <div id="couponMessage" style="font-size:0.85rem; margin-top:0.5rem; display:none;"></div>
                </div>

                <div id="discountRow" style="display:none; justify-content:space-between; margin-bottom:1rem; color:#34d399;">
                    <span>ডিসকাউন্ট</span>
                    <span>- ৳ <span id="discountValDisplay">0.00</span></span>
                </div>
                
                <div style="display:flex; justify-content:space-between; margin-bottom:2rem; color:var(--white); font-size:1.25rem; font-weight:700; border-top:1px solid rgba(255,255,255,0.1); padding-top:1rem;">
                    <span>সর্বমোট</span>
                    <span>৳ <span id="grandTotalDisplay"><?= number_format($grand_total, 2) ?></span></span>
                </div>
                
                <!-- Hidden total amount field for backend processing -->
                <input type="hidden" name="total_amount" id="totalAmountInput" value="<?= $grand_total ?>">
                
                <button type="submit" class="btn" style="width:100%; justify-content:center; padding:1.25rem; font-size:1.1rem;">অর্ডার সম্পন্ন করুন</button>
            </div>
            
        </form>
        
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const applyBtn = document.getElementById('applyCouponBtn');
    const input = document.getElementById('couponCodeInput');
    const msg = document.getElementById('couponMessage');
    const discRow = document.getElementById('discountRow');
    const discVal = document.getElementById('discountValDisplay');
    const gtVal = document.getElementById('grandTotalDisplay');
    const gtInput = document.getElementById('totalAmountInput');

    if(applyBtn) {
        applyBtn.addEventListener('click', function() {
            const code = input.value.trim();
            if(!code) {
                msg.textContent = "দয়া করে কুপন কোড দিন।";
                msg.style.color = "#f87171";
                msg.style.display = 'block';
                return;
            }

            // Loading state
            const origText = applyBtn.innerHTML;
            applyBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
            applyBtn.disabled = true;

            fetch('apply_coupon.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `coupon_code=${code}`
            })
            .then(res => res.json())
            .then(data => {
                applyBtn.innerHTML = origText;
                applyBtn.disabled = false;
                
                msg.textContent = data.message;
                msg.style.display = 'block';

                if(data.success) {
                    msg.style.color = "#34d399";
                    // Update UI
                    discRow.style.display = 'flex';
                    discVal.textContent = parseFloat(data.discount_amount).toFixed(2);
                    
                    // Format number for display
                    gtVal.textContent = parseFloat(data.grand_total).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    
                    // Value for form
                    gtInput.value = data.grand_total;

                    // Disable input once applied
                    input.disabled = true;
                    applyBtn.style.display = 'none';
                } else {
                    msg.style.color = "#f87171";
                    discRow.style.display = 'none';
                }
            })
            .catch(err => {
                console.error(err);
                applyBtn.innerHTML = origText;
                applyBtn.disabled = false;
                msg.textContent = "নেটওয়ার্ক সমস্যা, আবার চেষ্টা করুন।";
                msg.style.color = "#f87171";
                msg.style.display = 'block';
            });
        });
    }
    // Payment Method Toggle
    const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
    const mobilePanel = document.getElementById('mobileBankingPanel');
    const payNum = document.getElementById('payment_number');
    const trxId = document.getElementById('trx_id');

    paymentRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if(this.value === 'Mobile Banking') {
                mobilePanel.style.display = 'block';
                payNum.required = true;
                trxId.required = true;
            } else {
                mobilePanel.style.display = 'none';
                payNum.required = false;
                trxId.required = false;
            }
        });
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
