<?php
require_once __DIR__ . '/includes/header.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: blog.php");
    exit;
}

// Fetch blog post
$blog = null;
try {
    $stmt = $pdo->prepare("SELECT * FROM blogs WHERE id = ? AND is_published = 1");
    $stmt->execute([$id]);
    $blog = $stmt->fetch();
} catch (Exception $e) {}

if (!$blog) {
    header("Location: blog.php");
    exit;
}

// Fetch related posts (same type, exclude current)
$related = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM blogs WHERE type = ? AND id != ? AND is_published = 1 ORDER BY created_at DESC LIMIT 3");
    $stmt->execute([$blog['type'], $blog['id']]);
    $related = $stmt->fetchAll();
} catch (Exception $e) {}

// Determine cover image
$cover_img = '';
if (!empty($blog['image']) && $blog['image'] !== 'default_blog.jpg') {
    $cover_img = 'assets/images/' . $blog['image'];
}
if (empty($cover_img)) {
    $cover_img = $blog['type'] === 'farming_tips'
        ? 'https://images.unsplash.com/photo-1625246333195-78d9c38ad449?q=80&w=1200&auto=format&fit=crop'
        : 'https://images.unsplash.com/photo-1592982537447-6f2eda297c84?q=80&w=1200&auto=format&fit=crop';
}
?>

<!-- Hero Cover Image -->
<section style="position:relative; height:420px; overflow:hidden;">
    <img src="<?= htmlspecialchars($cover_img) ?>" alt="<?= htmlspecialchars($blog['title']) ?>"
         style="width:100%; height:100%; object-fit:cover; filter:brightness(0.4);">
    <div style="position:absolute; inset:0; background:linear-gradient(to top, var(--bg-color) 0%, rgba(6,58,36,0.3) 50%, transparent 100%);"></div>
    
    <div style="position:absolute; bottom:3rem; left:5%; right:5%; z-index:2; max-width:900px;">
        <!-- Type Badge -->
        <div style="margin-bottom:1rem;">
            <?php if ($blog['type'] === 'farming_tips'): ?>
                <span style="background:rgba(16,185,129,0.9); color:white; padding:0.5rem 1.2rem; border-radius:50px; font-size:0.85rem; font-weight:700; font-family:'Outfit',sans-serif;">🌾 কৃষি টিপস</span>
            <?php else: ?>
                <span style="background:rgba(167,139,250,0.9); color:white; padding:0.5rem 1.2rem; border-radius:50px; font-size:0.85rem; font-weight:700; font-family:'Outfit',sans-serif;">👨‍🌾 কৃষকের গল্প</span>
            <?php endif; ?>
        </div>
        
        <h1 style="font-family:'Outfit',sans-serif; font-size:2.8rem; font-weight:800; color:white; line-height:1.3; margin-bottom:1rem; text-shadow:0 2px 20px rgba(0,0,0,0.3);">
            <?= htmlspecialchars($blog['title']) ?>
        </h1>
        
        <div style="display:flex; align-items:center; gap:1.5rem; flex-wrap:wrap; color:rgba(255,255,255,0.8); font-size:0.9rem;">
            <span><i class="fa-solid fa-user" style="margin-right:0.3rem; color:var(--accent);"></i> <?= htmlspecialchars($blog['author']) ?></span>
            <span><i class="fa-regular fa-calendar" style="margin-right:0.3rem; color:var(--accent);"></i> <?= date('d F, Y', strtotime($blog['created_at'])) ?></span>
            <?php if (!empty($blog['tags'])): ?>
                <span><i class="fa-solid fa-tags" style="margin-right:0.3rem; color:var(--accent);"></i> <?= htmlspecialchars($blog['tags']) ?></span>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Blog Content -->
<section style="padding:3rem 5%;">
    <div style="max-width:850px; margin:0 auto;">
        
        <!-- Excerpt Box -->
        <?php if (!empty($blog['excerpt'])): ?>
        <div style="background:rgba(45,106,79,0.15); border-left:4px solid var(--accent); padding:1.5rem 2rem; border-radius:0 16px 16px 0; margin-bottom:2.5rem; font-size:1.05rem; color:var(--text-light); line-height:1.8; font-style:italic;">
            <?= htmlspecialchars($blog['excerpt']) ?>
        </div>
        <?php endif; ?>
        
        <!-- Main Content -->
        <article style="
            color:var(--text-light); 
            font-size:1.05rem; 
            line-height:2; 
            letter-spacing:0.02em;
        ">
            <style>
                .blog-content h2, .blog-content h3, .blog-content h4 {
                    color: var(--white);
                    font-family: 'Outfit', sans-serif;
                    margin-top: 2.5rem;
                    margin-bottom: 1rem;
                    position: relative;
                    padding-left: 1rem;
                    border-left: 3px solid var(--accent);
                }
                .blog-content h2 { font-size: 1.6rem; }
                .blog-content h3 { font-size: 1.35rem; }
                .blog-content h4 { font-size: 1.15rem; }
                .blog-content p {
                    margin-bottom: 1.5rem;
                    color: var(--text-light);
                    opacity: 0.9;
                }
                .blog-content ul, .blog-content ol {
                    margin: 1.5rem 0;
                    padding-left: 1.5rem;
                }
                .blog-content li {
                    margin-bottom: 0.8rem;
                    padding-left: 0.5rem;
                    color: var(--text-light);
                }
                .blog-content li::marker {
                    color: var(--accent);
                }
                .blog-content strong {
                    color: var(--white);
                    font-weight: 700;
                }
                .blog-content blockquote {
                    background: rgba(255,255,255,0.03);
                    border-left: 4px solid var(--primary-light);
                    padding: 1.5rem 2rem;
                    margin: 2rem 0;
                    border-radius: 0 12px 12px 0;
                    font-style: italic;
                    color: var(--text-light);
                }
                .blog-content img {
                    max-width: 100%;
                    border-radius: 16px;
                    margin: 2rem 0;
                    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
                }
            </style>
            <div class="blog-content">
                <?= $blog['content'] ?>
            </div>
        </article>
        
        <!-- Tags Section -->
        <?php if (!empty($blog['tags'])): ?>
        <div style="margin-top:3rem; padding-top:2rem; border-top:1px solid var(--glass-border);">
            <h4 style="color:var(--white); font-family:'Outfit',sans-serif; margin-bottom:1rem; font-size:1rem;">
                <i class="fa-solid fa-tags" style="color:var(--accent); margin-right:0.3rem;"></i> ট্যাগসমূহ
            </h4>
            <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                <?php foreach (explode(',', $blog['tags']) as $tag): ?>
                    <a href="blog.php?search=<?= urlencode(trim($tag)) ?>" style="background:rgba(255,255,255,0.06); color:var(--text-light); padding:0.4rem 1rem; border-radius:50px; font-size:0.85rem; text-decoration:none; border:1px solid var(--glass-border); transition:all 0.3s;"
                       onmouseover="this.style.background='var(--primary)'; this.style.color='white';"
                       onmouseout="this.style.background='rgba(255,255,255,0.06)'; this.style.color='var(--text-light)';">
                        #<?= htmlspecialchars(trim($tag)) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Share & Back -->
        <div style="margin-top:2.5rem; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem;">
            <a href="blog.php" class="btn btn-outline" style="padding:0.8rem 2rem;">
                <i class="fa-solid fa-arrow-left" style="margin-right:0.3rem;"></i> সব পোস্ট দেখুন
            </a>
            <a href="blog.php?type=<?= $blog['type'] ?>" class="btn" style="padding:0.8rem 2rem;">
                <?= $blog['type'] === 'farming_tips' ? '🌾 আরো কৃষি টিপস' : '👨‍🌾 আরো কৃষকের গল্প' ?>
            </a>
        </div>
    </div>
</section>

<!-- Related Posts -->
<?php if (!empty($related)): ?>
<section class="section" style="border-top:1px solid var(--glass-border);">
    <h2 class="section-title">আরও <span>পড়ুন</span></h2>
    <div style="max-width:1200px; margin:0 auto; display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:2rem;">
        <?php foreach ($related as $rel): ?>
            <?php
            $rel_img = '';
            if (!empty($rel['image']) && $rel['image'] !== 'default_blog.jpg') {
                $rel_img = 'assets/images/' . $rel['image'];
            }
            if (empty($rel_img)) {
                $rel_img = $rel['type'] === 'farming_tips'
                    ? 'https://images.unsplash.com/photo-1625246333195-78d9c38ad449?q=80&w=600&auto=format&fit=crop'
                    : 'https://images.unsplash.com/photo-1592982537447-6f2eda297c84?q=80&w=600&auto=format&fit=crop';
            }
            ?>
            <a href="blog_detail.php?id=<?= $rel['id'] ?>" style="text-decoration:none; color:inherit;">
                <div style="background:var(--card-bg); border-radius:16px; overflow:hidden; border:1px solid var(--glass-border); box-shadow:0 8px 25px rgba(0,0,0,0.25); transition:all 0.3s;"
                     onmouseover="this.style.transform='translateY(-5px)';"
                     onmouseout="this.style.transform='translateY(0)';">
                    <div style="height:180px; overflow:hidden;">
                        <img src="<?= htmlspecialchars($rel_img) ?>" alt="<?= htmlspecialchars($rel['title']) ?>"
                             style="width:100%; height:100%; object-fit:cover;">
                    </div>
                    <div style="padding:1.2rem;">
                        <h3 style="font-family:'Outfit',sans-serif; font-size:1.1rem; color:var(--white); margin-bottom:0.5rem; line-height:1.4;">
                            <?= htmlspecialchars($rel['title']) ?>
                        </h3>
                        <p style="color:var(--text-light); font-size:0.85rem; opacity:0.7;">
                            <?= date('d M Y', strtotime($rel['created_at'])) ?> — <?= htmlspecialchars($rel['author']) ?>
                        </p>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
