<?php 
require_once __DIR__ . '/includes/header.php'; 

// Auth Check for Customers
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'User';
$user_email = '';
$user_created = date('d M, Y');

// Fetch user profile info
try {
    $stmt_u = $pdo->prepare("SELECT name, email, role, created_at FROM users WHERE id = ?");
    $stmt_u->execute([$user_id]);
    $u_data = $stmt_u->fetch();
    if ($u_data) {
        $user_name = $u_data['name'] ?? $user_name;
        $user_email = $u_data['email'] ?? '';
        if(!empty($u_data['created_at'])) {
            $user_created = date('d M, Y', strtotime($u_data['created_at']));
        }
    }
} catch(Exception $e) {}

// Fetch user orders & order items
$user_orders = [];
$total_orders_count = 0;
$total_spent = 0;
$order_items_map = [];

try {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
    $stmt->execute([$user_id]);
    $user_orders = $stmt->fetchAll();
    $total_orders_count = count($user_orders);

    foreach($user_orders as $o) {
        if ($o['status'] === 'delivered') {
            $total_spent += (float)$o['total_amount'];
        }
    }

    if (!empty($user_orders)) {
        $order_ids = array_column($user_orders, 'id');
        $in_placeholders = implode(',', array_fill(0, count($order_ids), '?'));
        
        $item_stmt = $pdo->prepare("SELECT oi.*, p.name, p.image, p.unit 
                                    FROM order_items oi 
                                    LEFT JOIN products p ON oi.product_id = p.id 
                                    WHERE oi.order_id IN ($in_placeholders)");
        $item_stmt->execute($order_ids);
        $fetched_items = $item_stmt->fetchAll();
        
        foreach($fetched_items as $item) {
            $img = $item['image'];
            if(!empty($img) && strpos($img, 'http') === 0) {
                $item['resolved_image'] = $img;
            } else if(!empty($img) && file_exists(__DIR__ . '/assets/images/' . $img)) {
                $item['resolved_image'] = 'assets/images/' . $img;
            } else {
                $item['resolved_image'] = 'assets/images/default.jpg';
            }
            $order_items_map[$item['order_id']][] = $item;
        }
    }
} catch(Exception $e) {}

$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) {
        $cart_count += (int)$qty;
    }
}

// Calculate total items purchased across all orders
$total_purchased_items = 0;
foreach($order_items_map as $o_id => $items_list) {
    foreach($items_list as $it) {
        $total_purchased_items += (int)($it['quantity'] ?? 1);
    }
}
?>

<style>
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2.5rem;
        gap: 1.5rem;
        flex-wrap: wrap;
    }
    .user-badge {
        display: inline-block;
        padding: 0.3rem 0.8rem;
        background: rgba(16, 185, 129, 0.15);
        color: var(--primary);
        border: 1px solid rgba(16, 185, 129, 0.3);
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 600;
    }
    .order-card {
        background: var(--card-bg);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: var(--radius);
        margin-bottom: 1.5rem;
        overflow: hidden;
        transition: var(--transition);
        box-shadow: var(--card-shadow);
    }
    .order-card:hover {
        border-color: rgba(255, 255, 255, 0.15);
    }
    .order-header {
        padding: 1.25rem 1.5rem;
        background: rgba(255, 255, 255, 0.03);
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .order-body {
        padding: 1.5rem;
    }
    .item-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.8rem 0;
        border-bottom: 1px dashed rgba(255, 255, 255, 0.06);
    }
    .item-row:last-child {
        border-bottom: none;
    }
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.35rem 0.85rem;
        border-radius: 50px;
        font-size: 0.82rem;
        font-weight: 700;
        font-family: 'Outfit', sans-serif;
    }
    .status-pending { background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); }
    .status-processing { background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3); }
    .status-delivered { background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
    .status-cancelled { background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }
</style>

<section class="section" style="min-height: 80vh; background: var(--bg-color);">
    <div style="max-width: 1100px; margin: 0 auto;">
        
        <!-- Welcome Banner -->
        <div class="dashboard-header">
            <div>
                <h1 style="font-size: 2.2rem; margin-bottom: 0.4rem; font-family: 'Outfit', sans-serif; color: var(--white);">
                    👋 স্বাগতম, <span style="color: #34d399;"><?= htmlspecialchars($user_name) ?></span>!
                </h1>
                <p style="color: var(--text-light); font-size: 1rem; opacity: 0.85;">
                    আপনার অ্যাকাউন্টের সাম্প্রতিক তথ্য এবং অর্ডার হিস্টোরি নিচে দেওয়া হলো।
                </p>
            </div>
            <div style="display: flex; gap: 0.8rem; align-items: center;">
                <a href="products.php" class="btn btn-outline" style="padding: 0.6rem 1.2rem; font-size: 0.9rem;">
                    <i class="fa-solid fa-store"></i> শপ ভিজিট করুন
                </a>
                <a href="logout.php" class="btn" style="background: #ef4444; border-color: #ef4444; padding: 0.6rem 1.2rem; font-size: 0.9rem;">
                    <i class="fa-solid fa-right-from-bracket"></i> লগআউট
                </a>
            </div>
        </div>

        <!-- Metric Stats Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
            
            <!-- Profile Info Card -->
            <div style="background: var(--card-bg); padding: 1.6rem; border-radius: var(--radius); border: 1px solid rgba(255,255,255,0.06); box-shadow: var(--card-shadow);">
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.2rem;">
                    <div style="width: 48px; height: 48px; background: rgba(16, 185, 129, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #34d399; font-size: 1.3rem; border: 1px solid rgba(16, 185, 129, 0.3);">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; color: var(--white); font-size: 1.1rem; font-family: 'Outfit', sans-serif;"><?= htmlspecialchars($user_name) ?></h3>
                        <span class="user-badge"><?= ucfirst($_SESSION['user_role'] ?? 'Customer') ?></span>
                    </div>
                </div>
                <div style="color: var(--text-light); font-size: 0.9rem; display: flex; flex-direction: column; gap: 0.4rem;">
                    <?php if(!empty($user_email)): ?>
                        <p style="margin: 0;"><i class="fa-regular fa-envelope" style="width: 20px; color: var(--accent);"></i> <?= htmlspecialchars($user_email) ?></p>
                    <?php endif; ?>
                    <p style="margin: 0;"><i class="fa-regular fa-calendar-check" style="width: 20px; color: var(--accent);"></i> যোগদান: <?= $user_created ?></p>
                </div>
            </div>

            <!-- Total Orders Card -->
            <div style="background: var(--card-bg); padding: 1.6rem; border-radius: var(--radius); border: 1px solid rgba(255,255,255,0.06); text-align: center; box-shadow: var(--card-shadow);">
                <h4 style="color: var(--text-light); font-weight: 600; margin-bottom: 0.6rem; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.5px;">মোট অর্ডার</h4>
                <div style="font-size: 2.8rem; font-weight: 800; color: var(--white); font-family: 'Outfit', sans-serif; line-height: 1; margin-bottom: 0.6rem;">
                    <?= number_format($total_orders_count) ?>
                </div>
                <p style="color: var(--text-light); font-size: 0.85rem; opacity: 0.7; margin: 0;">অর্ডারকৃত মোট সংখ্যা</p>
            </div>

            <!-- Cart Items Card -->
            <div onclick="window.location.href='cart.php'" style="background: var(--card-bg); padding: 1.6rem; border-radius: var(--radius); border: 1px solid rgba(255,255,255,0.06); text-align: center; box-shadow: var(--card-shadow); cursor: pointer; transition: var(--transition);" onmouseover="this.style.borderColor='rgba(16, 185, 129, 0.4)'; this.style.transform='translateY(-3px)';" onmouseout="this.style.borderColor='rgba(255,255,255,0.06)'; this.style.transform='none';">
                <h4 style="color: var(--text-light); font-weight: 600; margin-bottom: 0.6rem; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.5px;">কার্টে আইটেম</h4>
                <div style="font-size: 2.8rem; font-weight: 800; color: var(--accent); font-family: 'Outfit', sans-serif; line-height: 1; margin-bottom: 0.6rem;">
                    <span class="cart-count"><?= number_format($cart_count) ?></span>
                </div>
                <p style="color: var(--text-light); font-size: 0.85rem; opacity: 0.8; margin-bottom: 0.8rem;">
                    <?php if($cart_count > 0): ?>
                        বর্তমানে ঝুড়িতে যুক্ত পণ্য
                    <?php else: ?>
                        ঝুড়ি খালি (মোট ক্রয়কৃত পণ্য: <?= number_format($total_purchased_items) ?> টি)
                    <?php endif; ?>
                </p>
                <a href="cart.php" style="color: #34d399; font-size: 0.85rem; font-weight: 600; text-decoration: none;">
                    কার্ট দেখুন <i class="fa-solid fa-arrow-right" style="font-size: 0.75rem;"></i>
                </a>
            </div>

            <!-- Total Delivered Spent Card -->
            <div style="background: var(--card-bg); padding: 1.6rem; border-radius: var(--radius); border: 1px solid rgba(255,255,255,0.06); text-align: center; box-shadow: var(--card-shadow);">
                <h4 style="color: var(--text-light); font-weight: 600; margin-bottom: 0.6rem; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.5px;">ডেলিভার্ড পারচেজ</h4>
                <div style="font-size: 2.2rem; font-weight: 800; color: #34d399; font-family: 'Outfit', sans-serif; line-height: 1.2; margin-bottom: 0.6rem;">
                    ৳ <?= number_format($total_spent, 2) ?>
                </div>
                <p style="color: var(--text-light); font-size: 0.85rem; opacity: 0.7; margin: 0;">সফল কেনাকাটার পরিমাণ</p>
            </div>

        </div>

        <!-- Order History List Section -->
        <div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
                <h2 style="font-size: 1.5rem; font-family: 'Outfit', sans-serif; color: var(--white); margin: 0; display: flex; align-items: center; gap: 0.6rem;">
                    <i class="fa-solid fa-clock-rotate-left" style="color: var(--accent);"></i> আমার অর্ডারসমূহ (Order History)
                </h2>
            </div>

            <?php if (empty($user_orders)): ?>
                <div style="background: var(--card-bg); padding: 3rem 2rem; border-radius: var(--radius); border: 1px solid rgba(255,255,255,0.06); text-align: center;">
                    <i class="fa-solid fa-box-open" style="font-size: 3.5rem; color: var(--text-light); opacity: 0.3; margin-bottom: 1rem; display: block;"></i>
                    <h3 style="color: var(--white); margin-bottom: 0.5rem; font-family: 'Outfit', sans-serif;">আপনি এখনো কোনো অর্ডার করেননি</h3>
                    <p style="color: var(--text-light); font-size: 0.95rem; max-width: 500px; margin: 0 auto 1.5rem;">
                        আমাদের মানসম্মত খাঁটি কৃষিপণ্য এবং অর্গানিক সামগ্রী দেখতে এবং প্রথম অর্ডার সম্পন্ন করতে নিচে ক্লিক করুন।
                    </p>
                    <a href="products.php" class="btn" style="padding: 0.75rem 1.8rem;">
                        <i class="fa-solid fa-cart-plus"></i> এখনই কেনাকাটা করুন
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($user_orders as $order): ?>
                    <?php 
                        $o_id = $order['id'];
                        $items = $order_items_map[$o_id] ?? [];
                        
                        $status_class = 'status-pending';
                        $status_text = 'Pending (পেন্ডিং)';
                        if ($order['status'] === 'delivered') {
                            $status_class = 'status-delivered';
                            $status_text = 'Delivered (সম্পন্ন)';
                        } else if ($order['status'] === 'processing' || $order['status'] === 'shipped') {
                            $status_class = 'status-processing';
                            $status_text = ucfirst($order['status']);
                        } else if ($order['status'] === 'cancelled') {
                            $status_class = 'status-cancelled';
                            $status_text = 'Cancelled (বাতিল)';
                        }
                    ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div>
                                <span style="font-weight: 800; color: var(--white); font-size: 1.15rem; font-family: 'Outfit', sans-serif; margin-right: 0.8rem;">
                                    অর্ডার #<?= $o_id ?>
                                </span>
                                <span style="color: var(--text-light); font-size: 0.85rem;">
                                    <i class="fa-regular fa-clock" style="margin-right: 4px;"></i> <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?>
                                </span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <span class="status-pill <?= $status_class ?>">
                                    <i class="fa-solid fa-circle" style="font-size: 0.5rem;"></i> <?= $status_text ?>
                                </span>
                                <span style="font-weight: 800; color: #34d399; font-size: 1.15rem; font-family: 'Outfit', sans-serif;">
                                    ৳ <?= number_format($order['total_amount'], 2) ?>
                                </span>
                            </div>
                        </div>

                        <div class="order-body">
                            <!-- Shipping & Payment Info -->
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1.2rem; background: rgba(0,0,0,0.2); padding: 1rem; border-radius: 8px;">
                                <div>
                                    <div style="color: var(--text-light); font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; margin-bottom: 2px;">পেমেন্ট পদ্ধতি</div>
                                    <div style="color: var(--white); font-size: 0.9rem; font-weight: 600;">
                                        <?= htmlspecialchars($order['payment_method']) ?>
                                        <?php if(!empty($order['trx_id'])): ?>
                                            <span style="font-size: 0.8rem; color: var(--accent); display: block;">TrxID: <?= htmlspecialchars($order['trx_id']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div>
                                    <div style="color: var(--text-light); font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; margin-bottom: 2px;">ডেলিভারি ঠিকানা</div>
                                    <div style="color: var(--white); font-size: 0.85rem; line-height: 1.3;">
                                        <?= htmlspecialchars($order['shipping_address']) ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Purchased Items List -->
                            <div style="margin-top: 0.5rem;">
                                <div style="color: var(--text-light); font-size: 0.82rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.6rem;">
                                    পণ্যসমূহ (<?= count($items) ?> টি আইটেম)
                                </div>
                                <?php foreach($items as $it): ?>
                                    <div class="item-row">
                                        <div style="display: flex; align-items: center; gap: 0.85rem;">
                                            <img src="<?= htmlspecialchars($it['resolved_image']) ?>" onerror="this.src='assets/images/default.jpg'" alt="" style="width: 44px; height: 44px; border-radius: 6px; object-fit: cover; background: rgba(0,0,0,0.3);">
                                            <div>
                                                <div style="color: var(--white); font-weight: 600; font-size: 0.95rem;"><?= htmlspecialchars($it['name'] ?? 'পণ্য') ?></div>
                                                <div style="color: var(--text-light); font-size: 0.8rem;">
                                                    ৳ <?= number_format($it['price'], 2) ?> &times; <?= $it['quantity'] ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div style="font-weight: 700; color: var(--white); font-family: 'Outfit', sans-serif;">
                                            ৳ <?= number_format($it['price'] * $it['quantity'], 2) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
