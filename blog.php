<?php
require_once __DIR__ . '/includes/header.php';

$type_filter = $_GET['type'] ?? '';
$search = $_GET['search'] ?? '';

// Build query
$query = "SELECT * FROM blogs WHERE is_published = 1";
$params = [];

if ($type_filter === 'farming_tips' || $type_filter === 'farmer_story') {
    $query .= " AND type = ?";
    $params[] = $type_filter;
}

if (!empty($search)) {
    $query .= " AND (title LIKE ? OR excerpt LIKE ? OR tags LIKE ?)";
    $search_param = "%{$search}%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

$query .= " ORDER BY created_at DESC";

$blogs = [];
try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $blogs = $stmt->fetchAll();
} catch (Exception $e) {}
?>

<!-- Hero Header -->
<section style="background: linear-gradient(135deg, var(--primary) 0%, #1b4332 50%, var(--primary-light) 100%); padding: 6rem 5% 4rem; color: white; text-align:center; position:relative; overflow:hidden;">
    <!-- Decorative elements -->
    <div style="position:absolute; top:-40px; left:5%; width:200px; height:200px; border-radius:50%; background:var(--accent); opacity:0.08; filter:blur(50px);"></div>
    <div style="position:absolute; bottom:-30px; right:8%; width:250px; height:250px; border-radius:50%; background:#a3e635; opacity:0.06; filter:blur(60px);"></div>
    <div style="position:absolute; top:30%; left:50%; width:300px; height:300px; border-radius:50%; background:var(--primary-light); opacity:0.05; filter:blur(70px); transform:translateX(-50%);"></div>
    
    <div style="position:relative; z-index:2;">
        <div style="display:inline-block; background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.15); padding:0.5rem 1.5rem; border-radius:50px; font-size:0.9rem; margin-bottom:1.5rem; backdrop-filter:blur(10px);">
            <i class="fa-solid fa-newspaper" style="color:var(--accent); margin-right:0.3rem;"></i> জ্ঞান ও অনুপ্রেরণা
        </div>
        <h1 style="font-family:'Outfit',sans-serif; font-size:3.2rem; font-weight:800; color:var(--white); margin-bottom:1rem;">
            <?php if($type_filter === 'farming_tips'): ?>
                🌾 কৃষি টিপস ও পরামর্শ
            <?php elseif($type_filter === 'farmer_story'): ?>
                👨‍🌾 কৃষকের গল্প
            <?php else: ?>
                কৃষি <span style="color:var(--accent);">ব্লগ</span>
            <?php endif; ?>
        </h1>
        <p style="font-size:1.15rem; opacity:0.85; max-width:650px; margin:0 auto; line-height:1.8;">
            <?php if($type_filter === 'farming_tips'): ?>
                ফসল চাষ, সার ব্যবহার, পরিচর্যা — সব তথ্য এক জায়গায়
            <?php elseif($type_filter === 'farmer_story'): ?>
                কৃষকদের জীবনের গল্প, সংগ্রাম ও সাফল্যের অনুপ্রেরণামূলক কাহিনী
            <?php else: ?>
                কৃষি টিপস, ফসলের পরিচর্যা এবং কৃষকদের অনুপ্রেরণার গল্প পড়ুন
            <?php endif; ?>
        </p>
    </div>
</section>

<!-- Filter & Search Bar -->
<section style="padding:2rem 5% 0;">
    <div style="max-width:1200px; margin:0 auto; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1.5rem;">
        
        <!-- Type Filter Tabs -->
        <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
            <a href="blog.php" style="padding:0.7rem 1.5rem; border-radius:50px; text-decoration:none; font-weight:600; font-family:'Outfit',sans-serif; font-size:0.95rem; transition:all 0.3s; <?= empty($type_filter) ? 'background:var(--primary); color:white; box-shadow:0 4px 15px rgba(45,106,79,0.4);' : 'background:rgba(255,255,255,0.05); color:var(--text-light); border:1px solid var(--glass-border);' ?>">
                সব পোস্ট
            </a>
            <a href="blog.php?type=farming_tips" style="padding:0.7rem 1.5rem; border-radius:50px; text-decoration:none; font-weight:600; font-family:'Outfit',sans-serif; font-size:0.95rem; transition:all 0.3s; <?= $type_filter === 'farming_tips' ? 'background:var(--primary); color:white; box-shadow:0 4px 15px rgba(45,106,79,0.4);' : 'background:rgba(255,255,255,0.05); color:var(--text-light); border:1px solid var(--glass-border);' ?>">
                🌾 কৃষি টিপস
            </a>
            <a href="blog.php?type=farmer_story" style="padding:0.7rem 1.5rem; border-radius:50px; text-decoration:none; font-weight:600; font-family:'Outfit',sans-serif; font-size:0.95rem; transition:all 0.3s; <?= $type_filter === 'farmer_story' ? 'background:var(--primary); color:white; box-shadow:0 4px 15px rgba(45,106,79,0.4);' : 'background:rgba(255,255,255,0.05); color:var(--text-light); border:1px solid var(--glass-border);' ?>">
                👨‍🌾 কৃষকের গল্প
            </a>
        </div>
        
        <!-- Search -->
        <form method="GET" action="blog.php" style="display:flex; gap:0.5rem;">
            <?php if($type_filter): ?>
                <input type="hidden" name="type" value="<?= htmlspecialchars($type_filter) ?>">
            <?php endif; ?>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="ব্লগ খুঁজুন..." 
                style="padding:0.7rem 1.2rem; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:50px; color:var(--white); outline:none; font-size:0.95rem; width:250px;">
            <button type="submit" style="padding:0.7rem 1.2rem; background:var(--primary); color:white; border:none; border-radius:50px; cursor:pointer; font-size:0.95rem; transition:all 0.3s;">
                <i class="fa-solid fa-search"></i>
            </button>
        </form>
    </div>
    
    <div style="max-width:1200px; margin:1.5rem auto 0; color:var(--text-light); font-size:0.95rem;">
        মোট <strong style="color:var(--white);"><?= count($blogs) ?></strong> টি পোস্ট পাওয়া গেছে
        <?php if($search): ?>
            — "<em style="color:var(--accent);"><?= htmlspecialchars($search) ?></em>" এর জন্য
        <?php endif; ?>
    </div>
</section>

<!-- Blog Cards Grid -->
<section class="section" style="padding-top:2rem;">
    <?php if (!empty($blogs)): ?>
    <div style="max-width:1200px; margin:0 auto; display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:2rem;">
        <?php foreach ($blogs as $blog): ?>
            <?php
            // Determine image
            $img_src = '';
            if (!empty($blog['image']) && $blog['image'] !== 'default_blog.jpg') {
                $img_src = 'assets/images/' . $blog['image'];
            }
            if (empty($img_src)) {
                $img_src = $blog['type'] === 'farming_tips' 
                    ? 'https://images.unsplash.com/photo-1625246333195-78d9c38ad449?q=80&w=600&auto=format&fit=crop'
                    : 'https://images.unsplash.com/photo-1592982537447-6f2eda297c84?q=80&w=600&auto=format&fit=crop';
            }
            ?>
            <a href="blog_detail.php?id=<?= $blog['id'] ?>" style="text-decoration:none; color:inherit;">
                <div style="background:var(--card-bg); border-radius:20px; overflow:hidden; border:1px solid var(--glass-border); box-shadow:0 10px 30px rgba(0,0,0,0.3); transition:all 0.4s cubic-bezier(0.25,0.8,0.25,1); cursor:pointer;" 
                     onmouseover="this.style.transform='translateY(-8px)'; this.style.boxShadow='0 20px 50px rgba(0,0,0,0.5)';"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 30px rgba(0,0,0,0.3)';">
                    
                    <!-- Image -->
                    <div style="position:relative; overflow:hidden; height:220px;">
                        <img src="<?= htmlspecialchars($img_src) ?>" alt="<?= htmlspecialchars($blog['title']) ?>" 
                             style="width:100%; height:100%; object-fit:cover; transition:transform 0.5s ease;"
                             onmouseover="this.style.transform='scale(1.05)';"
                             onmouseout="this.style.transform='scale(1)';">
                        <!-- Type Badge -->
                        <div style="position:absolute; top:1rem; left:1rem;">
                            <?php if ($blog['type'] === 'farming_tips'): ?>
                                <span style="background:rgba(16,185,129,0.9); color:white; padding:0.4rem 1rem; border-radius:50px; font-size:0.8rem; font-weight:700; font-family:'Outfit',sans-serif; backdrop-filter:blur(10px);">🌾 কৃষি টিপস</span>
                            <?php else: ?>
                                <span style="background:rgba(167,139,250,0.9); color:white; padding:0.4rem 1rem; border-radius:50px; font-size:0.8rem; font-weight:700; font-family:'Outfit',sans-serif; backdrop-filter:blur(10px);">👨‍🌾 কৃষকের গল্প</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div style="padding:1.5rem;">
                        <!-- Tags -->
                        <?php if (!empty($blog['tags'])): ?>
                            <div style="display:flex; gap:0.4rem; flex-wrap:wrap; margin-bottom:0.8rem;">
                                <?php foreach (array_slice(explode(',', $blog['tags']), 0, 3) as $tag): ?>
                                    <span style="background:rgba(255,255,255,0.06); color:var(--text-light); padding:0.2rem 0.7rem; border-radius:20px; font-size:0.75rem; border:1px solid rgba(255,255,255,0.08);">#<?= htmlspecialchars(trim($tag)) ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <h3 style="font-family:'Outfit',sans-serif; font-size:1.25rem; font-weight:700; color:var(--white); margin-bottom:0.8rem; line-height:1.4;">
                            <?= htmlspecialchars($blog['title']) ?>
                        </h3>
                        
                        <p style="color:var(--text-light); font-size:0.92rem; line-height:1.7; margin-bottom:1.2rem; opacity:0.8; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden;">
                            <?= htmlspecialchars($blog['excerpt'] ?: mb_strimwidth(strip_tags($blog['content']), 0, 150, '...')) ?>
                        </p>
                        
                        <!-- Footer -->
                        <div style="display:flex; justify-content:space-between; align-items:center; padding-top:1rem; border-top:1px solid var(--glass-border);">
                            <div style="display:flex; align-items:center; gap:0.5rem;">
                                <div style="width:30px; height:30px; border-radius:50%; background:linear-gradient(135deg, var(--primary), var(--primary-light)); display:flex; align-items:center; justify-content:center; font-size:0.7rem; color:white;">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                                <span style="color:var(--text-light); font-size:0.85rem;"><?= htmlspecialchars($blog['author']) ?></span>
                            </div>
                            <span style="color:var(--text-light); font-size:0.8rem; opacity:0.6;">
                                <i class="fa-regular fa-calendar"></i> <?= date('d M Y', strtotime($blog['created_at'])) ?>
                            </span>
                        </div>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
        <div style="max-width:600px; margin:0 auto; text-align:center; padding:5rem 2rem; background:var(--card-bg); border-radius:20px; border:1px solid var(--glass-border);">
            <i class="fa-solid fa-pen-fancy" style="font-size:4rem; color:var(--white); opacity:0.2; margin-bottom:1.5rem; display:block;"></i>
            <h3 style="font-family:'Outfit',sans-serif; color:var(--white); font-size:1.5rem; margin-bottom:0.8rem;">কোনো পোস্ট পাওয়া যায়নি</h3>
            <p style="color:var(--text-light); opacity:0.7;">
                <?php if ($search): ?>
                    "<strong><?= htmlspecialchars($search) ?></strong>" দিয়ে কোনো ফলাফল পাওয়া যায়নি। অন্য কিছু খুঁজুন।
                <?php else: ?>
                    শীঘ্রই নতুন পোস্ট প্রকাশিত হবে। সাথে থাকুন!
                <?php endif; ?>
            </p>
        </div>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
