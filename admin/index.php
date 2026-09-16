<?php require_once __DIR__ . '/includes/header.php';

// Fetch Stats
$total_orders = 0;
$pending_orders = 0;
$total_revenue = 0;
$total_customers = 0;
$recent_orders = [];
$low_stock_products = [];

try {
    $total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $pending_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status='pending'")->fetchColumn();
    $total_revenue = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status='delivered'")->fetchColumn() ?: 0;
    
    // Fetch total customer count safely
    try {
        $total_customers = $pdo->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn();
    } catch (Exception $e) {
        $total_customers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn() ?: 1;
    }

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

<style>
    .stat-card {
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        gap: 1.25rem;
        background: var(--card-bg);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        transition: var(--transition);
        backdrop-filter: blur(16px);
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
        border-color: rgba(255, 255, 255, 0.15);
    }
    .stat-icon {
        width: 60px; height: 60px;
        border-radius: var(--radius-md);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.6rem;
        flex-shrink: 0;
    }
    .stat-icon.orders { background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(59, 130, 246, 0.05)); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3); }
    .stat-icon.pending { background: linear-gradient(135deg, rgba(245, 158, 11, 0.2), rgba(245, 158, 11, 0.05)); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); }
    .stat-icon.revenue { background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(16, 185, 129, 0.05)); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
    .stat-icon.customers { background: linear-gradient(135deg, rgba(168, 85, 247, 0.2), rgba(168, 85, 247, 0.05)); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.3); }

    .stat-info h3 {
        color: var(--text-light);
        font-size: 0.82rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 0.25rem;
    }
    .stat-value {
        font-family: 'Outfit', sans-serif;
        font-size: 2.1rem;
        font-weight: 800;
        color: var(--white);
        line-height: 1.1;
    }

    .section-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.25rem;
        padding-bottom: 0.85rem;
        border-bottom: 1px solid var(--glass-border);
    }
    .section-title h3 {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--white);
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }

    .stock-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.9rem 1.1rem;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-md);
        transition: var(--transition);
    }
    .stock-item:hover {
        background: rgba(255, 255, 255, 0.06);
    }
</style>

<!-- Page Header Banner -->
<div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.85rem; font-weight: 800; font-family: 'Outfit', sans-serif; color: var(--white); margin-bottom: 0.3rem;">
            ড্যাশবোর্ড ওভারভিউ
        </h1>
        <p style="color: var(--text-light); font-size: 0.95rem;">আপনার শপের রিয়েল-টাইম পারফরম্যান্স এবং অর্ডার বিশ্লেষণ</p>
    </div>
    <div style="display: flex; gap: 0.8rem;">
        <a href="products.php" class="btn-sm" style="background: rgba(255, 255, 255, 0.08); border: 1px solid var(--glass-border);">
            <i class="fa-solid fa-plus"></i> নতুন পণ্য
        </a>
        <a href="orders.php" class="btn-sm">
            <i class="fa-solid fa-list-check"></i> অর্ডার তালিকা
        </a>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-4" style="margin-bottom: 2rem;">
    <div class="stat-card">
        <div class="stat-icon orders"><i class="fa-solid fa-bag-shopping"></i></div>
        <div class="stat-info">
            <h3>মোট অর্ডার</h3>
            <div class="stat-value"><?= number_format($total_orders) ?></div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon pending"><i class="fa-solid fa-clock"></i></div>
        <div class="stat-info">
            <h3>পেন্ডিং অর্ডার</h3>
            <div class="stat-value"><?= number_format($pending_orders) ?></div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon revenue"><i class="fa-solid fa-bangladeshi-taka-sign"></i></div>
        <div class="stat-info">
            <h3>মোট আয় (ডেলিভার্ড)</h3>
            <div class="stat-value">৳ <?= number_format($total_revenue) ?></div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon customers"><i class="fa-solid fa-users"></i></div>
        <div class="stat-info">
            <h3>মোট গ্রাহক</h3>
            <div class="stat-value"><?= number_format($total_customers) ?></div>
        </div>
    </div>
</div>

<!-- Sales Graph & Low Stock Split Grid -->
<div class="grid grid-3" style="margin-bottom: 2rem;">
    <!-- Sales Graph (2 Columns wide in desktop) -->
    <div class="card" style="grid-column: span 2;">
        <div class="section-title">
            <h3><i class="fa-solid fa-chart-line" style="color: var(--primary-light);"></i> গত ১৪ দিনের সেলস ট্রেন্ড</h3>
            <span class="badge bg-green"><i class="fa-solid fa-arrow-trend-up"></i> রিয়েল-টাইম</span>
        </div>
        <div style="position: relative; height: 320px; width: 100%;">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    <!-- Low Stock Alert Card (1 Column wide) -->
    <div class="card">
        <div class="section-title">
            <h3><i class="fa-solid fa-triangle-exclamation" style="color: var(--accent);"></i> ইনভেন্টরি অ্যালার্ট</h3>
            <span class="badge bg-yellow">Low Stock</span>
        </div>
        
        <?php if (empty($low_stock_products)): ?>
            <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); padding: 1.2rem; border-radius: var(--radius-md); color: #34d399; text-align: center; margin-top: 1rem;">
                <i class="fa-solid fa-circle-check" style="font-size: 1.5rem; margin-bottom: 0.5rem; display: block;"></i>
                <strong>সব পণ্যের পর্যাপ্ত স্টক রয়েছে!</strong>
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 0.8rem; margin-top: 0.5rem;">
                <?php foreach ($low_stock_products as $item): ?>
                    <?php 
                        $img = $item['image'];
                        $resolved_img = '../assets/images/default.jpg';
                        if(strpos($img, 'http') === 0) {
                            $resolved_img = $img;
                        } else if(!empty($img) && file_exists(__DIR__ . '/../assets/images/' . $img)) {
                            $resolved_img = '../assets/images/' . $img;
                        }
                    ?>
                    <div class="stock-item" style="border-left: 3px solid <?= $item['stock'] == 0 ? 'var(--danger)' : 'var(--accent)' ?>;">
                        <div style="display: flex; align-items: center; gap: 0.85rem;">
                            <img src="<?= htmlspecialchars($resolved_img) ?>" onerror="this.src='../assets/images/default.jpg'" alt="" style="width: 42px; height: 42px; border-radius: var(--radius-sm); object-fit: cover; background: rgba(0,0,0,0.4);">
                            <div>
                                <div style="color: var(--white); font-weight: 600; font-size: 0.9rem; margin-bottom: 2px; line-height: 1.2;"><?= htmlspecialchars($item['name']) ?></div>
                                <div style="color: var(--text-muted); font-size: 0.78rem;">ID: #<?= $item['id'] ?></div>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <span class="badge <?= $item['stock'] == 0 ? 'bg-red' : 'bg-yellow' ?>">
                                <?= $item['stock'] ?> <?= $item['stock'] == 0 ? 'Out' : 'Left' ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div style="margin-top: 1.2rem; text-align: center;">
                <a href="products.php" class="btn-sm" style="width: 100%; justify-content: center; background: rgba(245, 158, 11, 0.15); color: var(--accent); border: 1px solid rgba(245, 158, 11, 0.3); box-shadow: none;">
                    <i class="fa-solid fa-boxes-stacked"></i> স্টক রিফিল করুন
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Recent Orders Table -->
<div class="card">
    <div class="section-title">
        <h3><i class="fa-solid fa-clock-rotate-left" style="color: var(--info-light);"></i> সাম্প্রতিক নির্দেশিত অর্ডারসমূহ</h3>
        <a href="orders.php" style="color: var(--primary-light); font-weight: 600; font-size: 0.88rem; text-decoration: none;">
            সব দেখুন <i class="fa-solid fa-arrow-right" style="font-size: 0.75rem;"></i>
        </a>
    </div>

    <?php if (empty($recent_orders)): ?>
        <div style="padding: 2rem; text-align: center; color: var(--text-light);">
            <i class="fa-solid fa-inbox" style="font-size: 2.5rem; opacity: 0.4; margin-bottom: 0.5rem;"></i>
            <p>কোনো সাম্প্রতিক অর্ডার পাওয়া যায়নি।</p>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>অর্ডার আইডি</th>
                    <th>টাকার পরিমাণ</th>
                    <th>স্ট্যাটাস</th>
                    <th>পেমেন্ট মেথড</th>
                    <th>তারিখ</th>
                    <th style="text-align: right;">অ্যাকশন</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_orders as $order): ?>
                    <tr>
                        <td style="font-weight: 700; color: var(--white); font-family: 'Outfit', sans-serif;">
                            #<?= $order['id'] ?>
                        </td>
                        <td style="font-weight: 700; color: var(--primary-light); font-family: 'Outfit', sans-serif;">
                            ৳ <?= number_format($order['total_amount'], 2) ?>
                        </td>
                        <td>
                            <?php
                            $status_badge = 'bg-yellow';
                            if ($order['status'] === 'delivered') $status_badge = 'bg-green';
                            else if ($order['status'] === 'cancelled') $status_badge = 'bg-red';
                            else if ($order['status'] === 'processing' || $order['status'] === 'shipped') $status_badge = 'bg-blue';
                            ?>
                            <span class="badge <?= $status_badge ?>">
                                <i class="fa-solid fa-circle" style="font-size: 0.5rem;"></i>
                                <?= ucfirst($order['status']) ?>
                            </span>
                        </td>
                        <td style="color: var(--text-light); text-transform: uppercase; font-size: 0.85rem; font-weight: 600;">
                            <?= htmlspecialchars($order['payment_method']) ?>
                        </td>
                        <td style="color: var(--text-muted); font-size: 0.85rem;">
                            <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?>
                        </td>
                        <td style="text-align: right;">
                            <a href="orders.php" class="btn-sm" style="padding: 0.4rem 0.9rem; font-size: 0.8rem;">
                                <i class="fa-solid fa-eye"></i> বিস্তারিত
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        
        let gradient = ctx.createLinearGradient(0, 0, 0, 320);
        gradient.addColorStop(0, 'rgba(16, 185, 129, 0.45)');
        gradient.addColorStop(1, 'rgba(16, 185, 129, 0.01)');

        const chartLabels = <?= json_encode($chart_labels ?? []) ?>;
        const chartData = <?= json_encode($chart_data ?? []) ?>;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'সেলস আয় (৳)',
                    data: chartData,
                    borderColor: '#10b981',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#34d399',
                    pointBorderColor: '#061910',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    fill: true,
                    tension: 0.38
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(4, 20, 13, 0.95)',
                        titleColor: '#ffffff',
                        bodyColor: '#34d399', 
                        borderColor: 'rgba(16, 185, 129, 0.3)',
                        borderWidth: 1,
                        padding: 12,
                        boxPadding: 6,
                        usePointStyle: true,
                        callbacks: {
                            label: function(context) {
                                return ' মোট সেলস: ৳' + context.parsed.y.toLocaleString('en-US');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#94a3b8',
                            font: { family: 'Inter', size: 11 },
                            callback: function(value) {
                                return '৳' + value.toLocaleString('en-US');
                            }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: '#94a3b8',
                            font: { family: 'Inter', size: 11 }
                        }
                    }
                }
            }
        });
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>