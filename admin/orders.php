<?php require_once __DIR__ . '/includes/header.php'; 

// Handle Status Update & Deletion
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['order_id'], $_POST['status']) && !isset($_POST['action'])) {
        $id = (int)$_POST['order_id'];
        $status = $_POST['status'];
        try {
            $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $stmt->execute([$status, $id]);
            echo "<script>alert('অর্ডারের স্ট্যাটাস আপডেট হয়েছে!'); window.location.href='orders.php';</script>";
            exit;
        } catch(Exception $e) {}
    } 
    elseif(isset($_POST['order_id'], $_POST['action']) && $_POST['action'] === 'delete') {
        $id = (int)$_POST['order_id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
            $stmt->execute([$id]);
            echo "<script>alert('অর্ডারটি স্থায়ীভাবে মুছে ফেলা হয়েছে!'); window.location.href='orders.php';</script>";
            exit;
        } catch(Exception $e) {}
    }
}

// Fetch Orders with Filtering
$orders = [];
$order_items = [];
$status_filter = $_GET['status'] ?? '';
$where_clause = "";
$params = [];

if(in_array($status_filter, ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])) {
    $where_clause = "WHERE status = ?";
    $params[] = $status_filter;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM orders $where_clause ORDER BY id DESC");
    $stmt->execute($params);
    $orders = $stmt->fetchAll();
    
    if($orders) {
        $order_ids = array_column($orders, 'id');
        $in_placeholders = implode(',', array_fill(0, count($order_ids), '?'));
        
        $item_stmt = $pdo->prepare("SELECT oi.*, p.name, p.image, p.unit FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id IN ($in_placeholders)");
        $item_stmt->execute($order_ids);
        $fetched_items = $item_stmt->fetchAll();
        
        foreach($fetched_items as $item) {
            $img = $item['image'];
            if(strpos($img, 'http') === 0) {
                $item['resolved_image'] = $img;
            } else if(file_exists(__DIR__ . '/../assets/images/' . $img)) {
                $item['resolved_image'] = '../assets/images/' . $img;
            } else {
                $item['resolved_image'] = '../assets/images/' . $img;
            }
            $order_items[$item['order_id']][] = $item;
        }
    }
} catch(Exception $e) {}
?>

<div style="margin-bottom:2.5rem; display:flex; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; gap:1.5rem;">
    <div>
        <h1 style="font-size:2rem; margin-bottom:0.5rem;">সব অর্ডারসমূহ</h1>
        <p style="color:var(--text-light); opacity:0.8;">গ্রাহকদের সকল অর্ডারের তালিকা এবং স্ট্যাটাস পরিবর্তন করুন</p>
    </div>
    
    <!-- Quick Filters -->
    <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
        <a href="orders.php" class="btn-sm" style="background: <?= $status_filter=='' ? 'var(--primary-light)' : 'rgba(255,255,255,0.05)' ?>; color:var(--white);">সব অর্ডার</a>
        <a href="orders.php?status=pending" class="btn-sm" style="background: <?= $status_filter=='pending' ? 'var(--accent)' : 'rgba(255,255,255,0.05)' ?>; color: <?= $status_filter=='pending' ? '#063a24' : 'var(--white)' ?>;">Pending</a>
        <a href="orders.php?status=processing" class="btn-sm" style="background: <?= $status_filter=='processing' ? '#3b82f6' : 'rgba(255,255,255,0.05)' ?>; color:var(--white);">Processing</a>
        <a href="orders.php?status=shipped" class="btn-sm" style="background: <?= $status_filter=='shipped' ? '#8b5cf6' : 'rgba(255,255,255,0.05)' ?>; color:var(--white);">Shipped</a>
        <a href="orders.php?status=delivered" class="btn-sm" style="background: <?= $status_filter=='delivered' ? '#10b981' : 'rgba(255,255,255,0.05)' ?>; color:var(--white);">Delivered</a>
        <a href="orders.php?status=cancelled" class="btn-sm" style="background: <?= $status_filter=='cancelled' ? '#ef4444' : 'rgba(255,255,255,0.05)' ?>; color:var(--white);">Cancelled</a>
    </div>
</div>

<div class="card">
    <?php if(empty($orders)): ?>
        <p style="color:var(--text-light); padding:1rem 0; opacity:0.6;">কোনো অর্ডার পাওয়া যায়নি।</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>অর্ডার আইডি</th>
                    <th>ঠিকানা ও কন্টাক্ট</th>
                    <th>টাকার পরিমাণ</th>
                    <th>পেমেন্ট মেথড</th>
                    <th>তারিখ</th>
                    <th>বর্তমান স্ট্যাটাস</th>
                    <th>অ্যাকশন</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($orders as $order): ?>
                    <tr>
                        <td style="font-weight:700; color:var(--white);">#<?= $order['id'] ?></td>
                        <td style="max-width:250px; font-size:0.9rem; color:var(--text-light); line-height:1.5;"><?= htmlspecialchars($order['shipping_address']) ?></td>
                        <td style="font-weight:700; color:var(--primary-light);">৳ <?= number_format($order['total_amount'], 2) ?></td>
                        <td style="font-weight:500;"><?= htmlspecialchars($order['payment_method']) ?></td>
                        <td style="color:var(--text-light); font-size:0.85rem; opacity:0.7;"><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></td>
                        <td>
                            <?php
                                $badge_class = 'bg-yellow';
                                if($order['status'] === 'delivered') $badge_class = 'bg-green';
                                else if($order['status'] === 'cancelled') $badge_class = 'bg-red';
                            ?>
                            <span class="badge <?= $badge_class ?>"><?= ucfirst($order['status']) ?></span>
                        </td>
                        <td>
                            <div style="display:flex; flex-direction:column; gap:0.5rem;">
                                <form method="POST" style="display:flex; gap:0.5rem; align-items:center; width:100%;">
                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                    <select name="status" style="flex:1; padding:0.4rem; background:rgba(255,255,255,0.05); color:var(--white); border:1px solid var(--glass-border); border-radius:5px; font-size:0.85rem; outline:none; cursor:pointer;">
                                        <option value="pending" <?= $order['status']=='pending'?'selected':'' ?> style="background:#063a24;">Pending</option>
                                        <option value="processing" <?= $order['status']=='processing'?'selected':'' ?> style="background:#063a24;">Processing</option>
                                        <option value="shipped" <?= $order['status']=='shipped'?'selected':'' ?> style="background:#063a24;">Shipped</option>
                                        <option value="delivered" <?= $order['status']=='delivered'?'selected':'' ?> style="background:#063a24;">Delivered</option>
                                        <option value="cancelled" <?= $order['status']=='cancelled'?'selected':'' ?> style="background:#063a24;">Cancelled</option>
                                    </select>
                                    <button type="submit" class="btn-sm" style="padding:0.4rem 0.8rem; border-radius:5px;" title="স্ট্যাটাস আপডেট করুন"><i class="fa-solid fa-check"></i></button>
                                </form>
                                <div style="display:flex; gap:0.5rem; width:100%;">
                                    <button type="button" class="btn-sm view-details-btn" data-order='<?= json_encode($order) ?>' data-items='<?= json_encode($order_items[$order['id']] ?? []) ?>' style="flex:1; background:rgba(96, 165, 250, 0.2); color:#60a5fa; border:1px solid rgba(96, 165, 250, 0.3); border-radius:5px; justify-content:center;">
                                        <i class="fa-solid fa-list-ul"></i> বিস্তারিত
                                    </button>
                                    <form method="POST" onsubmit="return confirm('আপনি কি নিশ্চিত যে এই অর্ডারটি সম্পূর্ণ ডিলিট করতে চান? এর সাথে যুক্ত সব ডাটা মুছে যাবে!');" style="display:inline-block;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                        <button type="submit" class="btn-sm" style="background:rgba(239, 68, 68, 0.2); color:#ef4444; border:1px solid rgba(239, 68, 68, 0.3); border-radius:5px; padding:0.4rem 0.8rem;" title="অর্ডার ডিলিট করুন">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Order Details Modal -->
<div id="orderModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); backdrop-filter:blur(8px); -webkit-backdrop-filter:blur(8px); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:var(--bg-color); border:1px solid var(--glass-border); border-radius:var(--radius); width:90%; max-width:650px; max-height:90vh; display:flex; flex-direction:column; box-shadow:0 25px 50px rgba(0,0,0,0.5);">
        <div style="padding:1.5rem; border-bottom:1px solid var(--glass-border); display:flex; justify-content:space-between; align-items:center; background:rgba(255,255,255,0.03); border-radius:var(--radius) var(--radius) 0 0;">
            <h2 id="modalOrderTitle" style="font-size:1.3rem;">অর্ডার বিবরণ</h2>
            <button id="closeOrderModal" style="background:none; border:none; color:var(--text-light); font-size:1.5rem; cursor:pointer; width:32px; height:32px; display:flex; align-items:center; justify-content:center; border-radius:50%; transition:background 0.3s;"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div style="padding:1.5rem; overflow-y:auto; flex:1;">
            <div style="background:rgba(255,255,255,0.05); padding:1.2rem; border-radius:8px; margin-bottom:1.5rem; border-left:4px solid var(--primary-light);">
                <p style="color:var(--text-light); font-size:0.9rem; margin-bottom:0.8rem; text-transform:uppercase; letter-spacing:1px;"><i class="fa-solid fa-location-dot"></i> ডেলিভারি ঠিকানা ও কন্টাক্ট:</p>
                <p id="modalOrderAddress" style="color:var(--white); font-weight:500; line-height:1.6;"></p>
            </div>
            
            <div id="modalPaymentInfo" style="display:none; background:rgba(245,183,89,0.05); padding:1.2rem; border-radius:8px; margin-bottom:1.5rem; border-left:4px solid var(--accent);">
                <p style="color:var(--text-light); font-size:0.9rem; margin-bottom:0.8rem; text-transform:uppercase; letter-spacing:1px;"><i class="fa-solid fa-money-bill-transfer"></i> মোবাইল ব্যাংকিং পেমেন্ট তথ্য:</p>
                <div style="color:var(--white); font-weight:500; line-height:1.6;">
                    <p style="margin-bottom:0.4rem;"><strong>সেন্ডার নম্বর:</strong> <span id="modalPaymentNumber"></span></p>
                    <p><strong>ট্রানজ্যাকশন আইডি:</strong> <span id="modalTrxId" style="color:var(--accent); font-family:monospace; letter-spacing:1px; font-size:1.1rem;"></span></p>
                </div>
            </div>
            
            <h3 style="font-size:1.1rem; margin-bottom:1.2rem; color:var(--white); display:flex; align-items:center; gap:0.5rem;"><i class="fa-solid fa-box-open" style="color:var(--accent);"></i> অর্ডার করা পণ্যসমূহ:</h3>
            <div id="modalOrderItems" style="display:flex; flex-direction:column; gap:1rem;">
                <!-- Items will be injected here via JS -->
            </div>
            
            <div style="margin-top:2rem; padding-top:1.5rem; border-top:1px solid var(--glass-border); display:flex; justify-content:space-between; align-items:center; background:rgba(0,0,0,0.2); padding:1.5rem; border-radius:8px;">
                <h3 style="color:var(--text-light); font-size:1.2rem;">সবমিলিয়ে মোট মূল্য:</h3>
                <h2 id="modalOrderTotal" style="color:var(--accent); font-size:1.8rem; font-weight:800;"></h2>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('orderModal');
    const closeBtn = document.getElementById('closeOrderModal');
    
    // Close modal hover effect
    closeBtn.addEventListener('mouseenter', () => closeBtn.style.background = 'rgba(255,255,255,0.1)');
    closeBtn.addEventListener('mouseleave', () => closeBtn.style.background = 'none');
    
    closeBtn.addEventListener('click', () => {
        modal.style.display = 'none';
        document.body.style.overflow = ''; // Restore scrolling
    });
    window.addEventListener('click', (e) => {
        if(e.target === modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    });

    document.querySelectorAll('.view-details-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const order = JSON.parse(btn.getAttribute('data-order'));
            const items = JSON.parse(btn.getAttribute('data-items'));
            
            // Format Title and Status Bagde
            let statusBadge = '';
            if(order.status === 'delivered') statusBadge = '<span style="font-size:0.8rem; vertical-align:middle; margin-left:0.8rem; background:rgba(16, 185, 129, 0.2); color:#34d399; padding:0.3rem 0.8rem; border-radius:20px; border:1px solid rgba(16, 185, 129, 0.3);">DELIVERED <i class="fa-solid fa-check"></i></span>';
            else if(order.status === 'pending') statusBadge = '<span style="font-size:0.8rem; vertical-align:middle; margin-left:0.8rem; background:rgba(245, 183, 89, 0.2); color:var(--accent); padding:0.3rem 0.8rem; border-radius:20px; border:1px solid rgba(245, 183, 89, 0.3);">PENDING</span>';
            else statusBadge = `<span style="font-size:0.8rem; vertical-align:middle; margin-left:0.8rem; background:rgba(255, 255, 255, 0.1); color:var(--white); padding:0.3rem 0.8rem; border-radius:20px; border:1px solid rgba(255, 255, 255, 0.2);">${order.status.toUpperCase()}</span>`;
            
            document.getElementById('modalOrderTitle').innerHTML = `অর্ডার #${order.id} ${statusBadge}`;
            document.getElementById('modalOrderAddress').innerText = order.shipping_address;
            
            const paymentInfo = document.getElementById('modalPaymentInfo');
            if(order.payment_method === 'Mobile Banking') {
                paymentInfo.style.display = 'block';
                document.getElementById('modalPaymentNumber').innerText = order.payment_number || 'দেওয়া হয়নি';
                document.getElementById('modalTrxId').innerText = order.trx_id || 'দেওয়া হয়নি';
            } else {
                paymentInfo.style.display = 'none';
            }
            
            document.getElementById('modalOrderTotal').innerText = `৳ ${parseFloat(order.total_amount).toLocaleString('en-US', {minimumFractionDigits:2})}`;
            
            const itemsContainer = document.getElementById('modalOrderItems');
            itemsContainer.innerHTML = '';
            
            if(!items || items.length === 0) {
                itemsContainer.innerHTML = '<p style="color:#f87171; text-align:center; padding:2rem; background:rgba(239, 68, 68, 0.1); border-radius:8px;">কোনো পণ্যের বিবরণ পাওয়া যায়নি।</p>';
            } else {
                items.forEach(item => {
                    const itemTotal = parseFloat(item.price) * parseInt(item.quantity);
                    const el = document.createElement('div');
                    el.style.cssText = 'display:flex; gap:1.2rem; align-items:center; background:rgba(255,255,255,0.03); padding:1rem; border-radius:8px; border:1px solid rgba(255,255,255,0.05); transition:background 0.3s;';
                    el.addEventListener('mouseenter', () => el.style.background = 'rgba(255,255,255,0.06)');
                    el.addEventListener('mouseleave', () => el.style.background = 'rgba(255,255,255,0.03)');
                    
                    // Fallback to default if there is broken image
                    const resolvedImage = item.resolved_image || '../assets/images/default.jpg';
                    
                    el.innerHTML = `
                        <div style="width:70px; height:70px; overflow:hidden; border-radius:8px; border:1px solid var(--glass-border); flex-shrink:0; background:#000;">
                            <img src="${resolvedImage}" onerror="this.src='../assets/images/default.jpg'" alt="${item.name}" style="width:100%; height:100%; object-fit:cover;">
                        </div>
                        <div style="flex:1;">
                            <h4 style="color:var(--white); font-size:1.05rem; margin-bottom:0.4rem;">${item.name}</h4>
                            <div style="color:var(--text-light); font-size:0.9rem; display:inline-block; background:rgba(0,0,0,0.3); padding:0.2rem 0.6rem; border-radius:4px;">
                                ৳ ${parseFloat(item.price).toLocaleString()} &times; <span style="font-weight:700; color:var(--white);">${item.quantity} ${item.unit}</span>
                            </div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-weight:700; color:var(--primary-light); font-size:1.15rem;">৳ ${itemTotal.toLocaleString('en-US', {minimumFractionDigits:2})}</div>
                        </div>
                    `;
                    itemsContainer.appendChild(el);
                });
            }
            
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        });
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
