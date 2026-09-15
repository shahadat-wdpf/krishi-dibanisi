<?php require_once __DIR__ . '/includes/header.php';

// Fetch Stats
$total_orders = 0;
$pending_orders = 0;
$total_revenue = 0;
$recent_orders = [];

try {
    $total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $pending_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status='pending'")->fetchColumn();
    $total_revenue = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status='delivered'")->fetchColumn() ?: 0;

    $stmt = $pdo->query("SELECT id, user_id, total_amount, status, payment_method, created_at FROM orders ORDER BY id DESC LIMIT 5");
    $recent_orders = $stmt->fetchAll();

    $stmt_stock = $pdo->query("SELECT id, name, stock, image FROM products WHERE stock <= 10 ORDER BY stock ASC LIMIT 5");
    $low_stock_products = $stmt_stock->fetchAll();

    // Fetch Last 14 Days Sales Data
    $chart_labels = [];
    $chart_data = [];
    $sales_data_map = [];

    // Initialize last 14 days with 0
    for ($i = 13; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $chart_labels[] = date('d M', strtotime("-$i days"));
        $sales_data_map[$date] = 0;
    }

    $sales_query = $pdo->query("SELECT DATE(created_at) as order_date, SUM(total_amount) as daily_revenue 
                                FROM orders 
                                WHERE status='delivered' AND created_at >= DATE(NOW() - INTERVAL 14 DAY)
                                GROUP BY DATE(created_at)");
    
    while($row = $sales_query->fetch(PDO::FETCH_ASSOC)) {
        $date = $row['order_date'];
        if (isset($sales_data_map[$date])) {
            $sales_data_map[$date] = (float)$row['daily_revenue'];
        }
    }
    $chart_data = array_values($sales_data_map);

} catch (Exception $e) {
}
?>

<div style="margin-bottom:2.5rem;">
    <h1 style="font-size:2rem; margin-bottom:0.5rem;">ড্যাশবোর্ড ওভারভিউ</h1>
    <p style="color:var(--text-light); opacity:0.8;">আপনার ওয়েবসাইটের সাম্প্রতিক পরিসংখ্যান এবং ডাটা</p>
</div>



<!-- Stats Grid -->
<div class="grid grid-4" style="margin-bottom:2.5rem;">
    <div class="card" style="border-top:4px solid var(--primary-light);">
        <h3
            style="color:var(--text-light); font-size:0.9rem; margin-bottom:0.75rem; text-transform:uppercase; letter-spacing:1px;">
            মোট অর্ডার</h3>
        <div style="font-size:2.2rem; font-weight:800; color:var(--white);"><?= $total_orders ?></div>
    </div>
    <div class="card" style="border-top:4px solid var(--accent);">
        <h3
            style="color:var(--text-light); font-size:0.9rem; margin-bottom:0.75rem; text-transform:uppercase; letter-spacing:1px;">
            পেন্ডিং অর্ডার</h3>
        <div style="font-size:2.2rem; font-weight:800; color:var(--white);"><?= $pending_orders ?></div>
    </div>
    <div class="card" style="border-top:4px solid #10b981;">
        <h3
            style="color:var(--text-light); font-size:0.9rem; margin-bottom:0.75rem; text-transform:uppercase; letter-spacing:1px;">
            মোট আয় (ডেলিভার্ড)</h3>
        <div style="font-size:2.2rem; font-weight:800; color:var(--white);">৳ <?= number_format($total_revenue) ?></div>
    </div>
    <div class="card" style="border-top:4px solid #8b5cf6;">
        <h3
            style="color:var(--text-light); font-size:0.9rem; margin-bottom:0.75rem; text-transform:uppercase; letter-spacing:1px;">
            মোট গ্রাহক</h3>
        <div style="font-size:2.2rem; font-weight:800; color:var(--white);">১</div>
    </div>
</div>

<!-- Sales Graph Container -->
<div class="card" style="margin-bottom:2.5rem;">
    <h3 style="margin-bottom:1.5rem; padding-bottom:1rem; border-bottom:1px solid var(--glass-border); display:flex; align-items:center; gap:0.75rem;">
        <i class="fa-solid fa-chart-line" style="color:var(--accent);"></i> গত ১৪ দিনের সেলস ট্রেন্ড
    </h3>
    <div style="position: relative; height:300px; width:100%;">
        <canvas id="salesChart"></canvas>
    </div>
</div>

<!-- Recent Orders Table -->
<div class="card">
    <h3
        style="margin-bottom:1.5rem; padding-bottom:1rem; border-bottom:1px solid var(--glass-border); display:flex; align-items:center; gap:0.75rem;">
        <i class="fa-solid fa-clock-rotate-left" style="color:var(--accent);"></i> সাম্প্রতিক অর্ডারসমূহ
    </h3>
    <?php if (empty($recent_orders)): ?>
        <p style="color:var(--text-light); padding:1rem 0; opacity:0.6;">কোনো অর্ডার পাওয়া যায়নি।</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>অর্ডার আইডি</th>
                    <th>টাকার পরিমাণ</th>
                    <th>স্ট্যাটাস</th>
                    <th>পেমেন্ট মেথড</th>
                    <th>তারিখ</th>
                    <th>অ্যাকশন</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_orders as $order): ?>
                    <tr>
                        <td style="font-weight:700; color:var(--white);">#<?= $order['id'] ?></td>
                        <td style="font-weight:600; color:var(--primary-light);">৳
                            <?= number_format($order['total_amount'], 2) ?></td>
                        <td>
                            <?php
                            $status_badge = 'bg-yellow';
                            if ($order['status'] === 'delivered')
                                $status_badge = 'bg-green';
                            else if ($order['status'] === 'cancelled')
                                $status_badge = 'bg-red';
                            ?>
                            <span class="badge <?= $status_badge ?>"><?= ucfirst($order['status']) ?></span>
                        </td>
                        <td><?= htmlspecialchars($order['payment_method']) ?></td>
                        <td style="color:var(--text-light); font-size:0.9rem; opacity:0.7;">
                            <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></td>
                        <td><a href="orders.php" class="btn-sm"><i class="fa-solid fa-eye"></i> দেখুন</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Low Stock Alert and Actions -->
<div class="grid" style="grid-template-columns: 1fr; margin-top:2.5rem;">
    <div class="card" style="border-top:4px solid #f87171;">
        <h3 style="margin-bottom:1.5rem; padding-bottom:1rem; border-bottom:1px solid var(--glass-border); display:flex; align-items:center; gap:0.75rem;">
            <i class="fa-solid fa-triangle-exclamation" style="color:#f87171;"></i> ইনভেন্টরি অ্যালার্ট (Low Stock)
        </h3>
        <?php if (empty($low_stock_products)): ?>
            <div style="background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); padding: 1rem; border-radius: 8px; color: #34d399; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-circle-check"></i> সব পণ্যের পর্যাপ্ত স্টক আছে!
            </div>
        <?php else: ?>
            <div style="display:flex; flex-direction:column; gap:1rem;">
                <?php foreach ($low_stock_products as $item): ?>
                    <?php 
                        $img = $item['image'];
                        $resolved_img = '../assets/images/default.jpg'; // fallback
                        if(strpos($img, 'http') === 0) {
                            $resolved_img = $img;
                        } else if(file_exists(__DIR__ . '/../assets/images/' . $img) && $img !== 'default.jpg' && !empty($img)) {
                            $resolved_img = '../assets/images/' . $img;
                        }
                    ?>
                    <div style="display:flex; align-items:center; justify-content:space-between; padding:0.8rem; background:rgba(255,255,255,0.03); border-radius:8px; border-left:4px solid <?= $item['stock']==0 ? '#ef4444' : '#f5b759' ?>;">
                        <div style="display:flex; align-items:center; gap:1rem;">
                            <img src="<?= htmlspecialchars($resolved_img) ?>" onerror="this.src='../assets/images/default.jpg'" alt="" style="width:40px; height:40px; border-radius:6px; object-fit:cover; background:rgba(0,0,0,0.5);">
                            <div>
                                <div style="color:var(--white); font-weight:600; font-size:0.95rem; margin-bottom:2px;"><?= htmlspecialchars($item['name']) ?></div>
                                <div style="color:var(--text-light); font-size:0.8rem;">আইডি: #<?= $item['id'] ?></div>
                            </div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-weight:800; font-size:1.1rem; color:<?= $item['stock']==0 ? '#f87171' : 'var(--accent)' ?>;"><?= $item['stock'] ?></div>
                            <div style="font-size:0.75rem; color:var(--text-light); text-transform:uppercase;">স্টক রিমেইনিং</div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div style="margin-top:1.5rem; text-align:center;">
                <a href="products.php" class="btn-sm" style="background:#f87171; border:1px solid #ef4444;"><i class="fa-solid fa-arrow-up-right-dots"></i> স্টক ম্যানেজ করুন</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        
        // Create gradient corresponding to Dark Forest Green / Glassmorphism
        let gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(16, 185, 129, 0.4)'); // primary-light equivalent
        gradient.addColorStop(1, 'rgba(16, 185, 129, 0.05)');

        const chartLabels = <?= json_encode($chart_labels ?? []) ?>;
        const chartData = <?= json_encode($chart_data ?? []) ?>;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'আয় (Delivered)',
                    data: chartData,
                    borderColor: '#10b981', // var(--primary-light)
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#1fb58b', // var(--accent)
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4 // curved lines
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleColor: '#fff',
                        bodyColor: '#10b981', 
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                return ' ৳' + context.parsed.y.toLocaleString('en-US');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.05)',
                            borderColor: 'transparent'
                        },
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.6)',
                            callback: function(value) {
                                return '৳' + value.toLocaleString('en-US');
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                        },
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.6)',
                        }
                    }
                }
            }
        });
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>