<?php
require_once __DIR__ . '/includes/header.php';

// Fetch Categories
$categories = [];
try {
    $stmt = $pdo->query("SELECT * FROM categories LIMIT 12");
    $categories = $stmt->fetchAll();
} catch(Exception $e) {}

// Fetch Featured Products
$featured_products = [];
try {
    $stmt = $pdo->query("
        SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.is_featured = 1 
        ORDER BY p.id DESC LIMIT 4
    ");
    $featured_products = $stmt->fetchAll();
} catch(Exception $e) {}

// Category placeholder images
$category_images = [
    1 => 'https://images.unsplash.com/photo-1586201375761-83865001e8ac?q=80&w=400&auto=format&fit=crop', // Rice/Lentils
    2 => 'https://images.unsplash.com/photo-1566385101042-1a0aa0c1268c?q=80&w=400&auto=format&fit=crop', // Vegetables
    3 => 'https://images.unsplash.com/photo-1610832958506-aa56368176cf?q=80&w=400&auto=format&fit=crop', // Fruits
    4 => 'https://images.unsplash.com/photo-1587049352847-4d4b124041d8?q=80&w=400&auto=format&fit=crop', // Honey/Ghee
    5 => 'https://images.unsplash.com/photo-1516280030429-27679b3dc9ec?q=80&w=400&auto=format&fit=crop', // Poultry
    6 => 'https://images.unsplash.com/photo-1506976694689-ffacb6b29efb?q=80&w=400&auto=format&fit=crop', // Eggs
    7 => 'https://images.unsplash.com/photo-1603048297172-c92544798d5e?q=80&w=400&auto=format&fit=crop', // Meat
    8 => 'https://images.unsplash.com/photo-1566318536130-10f8454b600d?q=80&w=400&auto=format&fit=crop', // Fish
    9 => 'https://images.unsplash.com/photo-1628088062854-d1870b4553da?q=80&w=400&auto=format&fit=crop'  // Dairy
];
?>

<!-- Hero Carousel Section -->
<section class="hero-slider">
    <!-- Slide 1 -->
    <div class="slide active">
        <img src="assets/images/slide1.jpg" class="slide-img" alt="Green Field">
        <div class="slide-overlay"></div>
        <div class="hero-content">
            <h1>১০০% তাজা, <span>প্রকৃতির</span> সেরা উপহার।</h1>
            <p>গ্রীন টেক ফার্ম (Green Tech Farm) - ভেজালমুক্ত, তাজা, এবং স্বাস্থ্যকর গ্রামীণ পণ্য কৃষকদের কাছ থেকে সরাসরি আপনার ঘরে পৌঁছে দেওয়ার বিশ্বস্ত মাধ্যম।</p>
            <a href="products.php" class="btn"><i class="fa-solid fa-basket-shopping"></i> কেনাকাটা শুরু করুন</a>
        </div>
    </div>

    <!-- Slide 2 -->
    <div class="slide">
        <img src="assets/images/krishim_ath.jpg" class="slide-img" alt="Scenic Agricultural Field">
        <div class="slide-overlay"></div>
        <div class="hero-content">
            <h1>দেশি <span>কৃষি পণ্যের</span> সমারোহ।</h1>
            <p>আমাদের প্রতিটি পণ্য সরাসরি কৃষকের মাঠ থেকে সংগৃহীত। বিষমুক্ত এবং শতভাগ সতেজ গ্রামীণ স্বাদ আপনার দোরগোড়ায় পৌঁছে দিতে আমরা প্রতিশ্রুতিবদ্ধ।</p>
            <a href="products.php" class="btn"><i class="fa-solid fa-seedling"></i> আমাদের পণ্য দেখুন</a>
        </div>
    </div>

    <!-- Slide 3 -->
    <div class="slide">
        <img src="assets/images/slide3.jpg" class="slide-img" alt="Winter Vegetables">
        <div class="slide-overlay"></div>
        <div class="hero-content">
            <h1>তাজা <span>শীতকালীন সবজি</span> এখন আপনার ঝুড়িতে।</h1>
            <p>ফুলকপি, বাঁধাকপি, শিম থেকে শুরু করে টাটকা গাজর—সবই পাবেন সরাসরি কৃষকের বাগান থেকে। সতেজ এবং স্বাস্থ্যকর ডায়েটের শুরু হোক আমাদের সাথেই।</p>
            <a href="products.php?category=vegetables" class="btn"><i class="fa-solid fa-leaf"></i> সবজি কিনুন</a>
        </div>
    </div>

    <!-- Slide 4: Happy Farmer -->
    <div class="slide">
        <img src="assets/images/krishok2.jpg" class="slide-img" alt="বাংলাদেশের কৃষক">
        <div class="slide-overlay"></div>
        <div class="hero-content">
            <h1>কৃষকের <span>হাসি</span>, খাঁটি পণ্যের নিশ্চয়তা</h1>
            <p>সোনালী রোদ আর ঘামের বিনিময়ে আমাদের কৃষকরা আপনার জন্য ফলান সেরা সবজি। সরাসরি কৃষকের হাত থেকে সংগৃহীত এই বিশুদ্ধ পণ্যগুলোই আপনাদের পৌঁছে দেয় এক নিরাপদ ও স্বাস্থ্যকর জীবনের স্বাদ।</p>
            <a href="about.php" class="btn"><i class="fa-solid fa-person-digging"></i> কৃষকদের গল্প জানুন</a>
        </div>
    </div>

    <!-- Controls -->
    <button class="carousel-btn btn-prev"><i class="fa-solid fa-chevron-left"></i></button>
    <button class="carousel-btn btn-next"><i class="fa-solid fa-chevron-right"></i></button>

    <!-- Indicators -->
    <div class="carousel-dots">
        <div class="dot active"></div>
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
    </div>
</section>

<!-- Promo Banner Section -->
<section class="section" style="padding-top: 2rem; padding-bottom: 2rem;">
    <div style="max-width: 1400px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
        
        <!-- First Purchase Promo (Flip Card) -->
        <div class="promo-flip-container">
            <div class="promo-flip-card">
                <!-- Front Side -->
                <div class="promo-card-front promo-bg-1">
                    <div style="position: absolute; top: -20px; right: -20px; font-size: 8rem; color: rgba(255,255,255,0.05); transform: rotate(-15deg); pointer-events: none;"><i class="fa-solid fa-gift"></i></div>
                    <div style="position: relative; z-index: 1; width: 100%;">
                        <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem; font-family: 'Outfit', sans-serif;">প্রথম অর্ডারে <span style="color: var(--accent);">২০% ছাড়!</span></h3>
                        <p style="font-size: 0.95rem; color: rgba(255,255,255,0.8);">গ্রীন টেক ফার্মের নতুন গ্রাহকদের জন্য বিশেষ উপহার।<br><br><span style="font-size: 0.85rem; opacity: 0.8; color: var(--accent);"><i class="fa-solid fa-arrow-rotate-right"></i> প্রোমো কোড দেখতে কার্ডটি ঘোরান</span></p>
                    </div>
                </div>
                <!-- Back Side -->
                <div class="promo-card-back promo-bg-1">
                    <div style="position: absolute; bottom: -20px; left: -20px; font-size: 8rem; color: rgba(255,255,255,0.05); transform: rotate(15deg); pointer-events: none;"><i class="fa-solid fa-ticket"></i></div>
                    <div style="position: relative; z-index: 1; text-align: center;">
                        <p style="font-size: 1rem; color: rgba(255,255,255,0.9); margin-bottom: 1rem;">চেকআউটের সময় নিচের কোডটি ব্যবহার করুন</p>
                        <div style="display: inline-block; background: rgba(0,0,0,0.3); border: 2px dashed var(--accent); padding: 1rem 2rem; border-radius: 12px; font-weight: 800; letter-spacing: 3px; font-family: 'Outfit', sans-serif; font-size: 1.5rem;">FIRST20</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 1500tk Promo (Flip Card) -->
        <div class="promo-flip-container">
            <div class="promo-flip-card">
                <!-- Front Side -->
                <div class="promo-card-front promo-bg-2">
                    <div style="position: absolute; bottom: -20px; right: -10px; font-size: 8rem; color: rgba(0,0,0,0.05); transform: rotate(15deg); pointer-events: none;"><i class="fa-solid fa-tags"></i></div>
                    <div style="position: relative; z-index: 1; width: 100%;">
                        <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem; font-family: 'Outfit', sans-serif; color: #042718;">১৫০০ টাকার কেনাকাটায় <span style="color: #2d6a4f;">৫০ ৳ ছাড়!</span></h3>
                        <p style="font-size: 0.95rem; color: rgba(4,39,24,0.8);">আজই অর্ডার করুন এবং বুঝে নিন আপনার ক্যাশ ডিসকাউন্ট।<br><br><span style="font-size: 0.85rem; opacity: 1; color: #2d6a4f; font-weight:600;"><i class="fa-solid fa-arrow-rotate-right"></i> প্রোমো কোড দেখতে কার্ডটি ঘোরান</span></p>
                    </div>
                </div>
                <!-- Back Side -->
                <div class="promo-card-back promo-bg-2">
                    <div style="position: absolute; top: -20px; left: -10px; font-size: 8rem; color: rgba(0,0,0,0.05); transform: rotate(-15deg); pointer-events: none;"><i class="fa-solid fa-sack-dollar"></i></div>
                    <div style="position: relative; z-index: 1; text-align: center;">
                        <p style="font-size: 1rem; color: rgba(4,39,24,0.9); margin-bottom: 1rem;">চেকআউটের সময় নিচের কোডটি ব্যবহার করুন</p>
                        <div style="display: inline-block; background: rgba(255,255,255,0.4); border: 2px dashed #2d6a4f; padding: 1rem 2rem; border-radius: 12px; font-weight: 800; letter-spacing: 3px; font-family: 'Outfit', sans-serif; font-size: 1.5rem; color: #042718;">SAVE50</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- Categories Section -->
<section class="section">
    <h2 class="section-title">আমাদের <span>ক্যাটাগরি সমূহ</span></h2>
    <div class="categories-grid">
        <?php if($categories): ?>
            <?php foreach($categories as $cat): ?>
                <a href="products.php?category=<?= htmlspecialchars($cat['slug']) ?>" class="category-card">
                    <img src="assets/images/cat_<?= htmlspecialchars($cat['slug']) ?>.jpg" class="category-img" alt="<?= htmlspecialchars($cat['name']) ?>" onerror="this.onerror=null; this.src='assets/images/default_category.jpg'; this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <div class="category-icon" style="display:none;"><?= htmlspecialchars($cat['icon'] ?? '🌾') ?></div>
                    <h3><?= htmlspecialchars($cat['name']) ?></h3>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Fallbacks -->
            <a href="products.php?category=rice-lentils" class="category-card">
                <img src="assets/images/cat_rice-lentils.jpg" class="category-img" alt="চাল ও ডাল" onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='block';"><div class="category-icon" style="display:none;">🌾</div><h3>চাল ও ডাল</h3>
            </a>
            <a href="products.php?category=vegetables" class="category-card">
                <img src="assets/images/cat_vegetables.jpg" class="category-img" alt="শাকসবজি" onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='block';"><div class="category-icon" style="display:none;">🥦</div><h3>শাকসবজি</h3>
            </a>
            <a href="products.php?category=fruits" class="category-card">
                <img src="assets/images/cat_fruits.jpg" class="category-img" alt="ফলমূল" onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='block';"><div class="category-icon" style="display:none;">🍎</div><h3>ফলমূল</h3>
            </a>
            <a href="products.php?category=honey-ghee" class="category-card">
                <img src="assets/images/cat_honey-ghee.jpg" class="category-img" alt="মধু ও ঘি" onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='block';"><div class="category-icon" style="display:none;">🍯</div><h3>মধু ও ঘি</h3>
            </a>
            <a href="products.php?category=deshi-has-murgi" class="category-card">
                <img src="assets/images/cat_deshi-has-murgi.jpg" class="category-img" alt="দেশি হাঁস-মুরগি" onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='block';"><div class="category-icon" style="display:none;">🐔</div><h3>দেশি হাঁস-মুরগি</h3>
            </a>
            <a href="products.php?category=dim" class="category-card">
                <img src="assets/images/cat_dim.jpg" class="category-img" alt="ডিম" onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='block';"><div class="category-icon" style="display:none;">🥚</div><h3>ডিম</h3>
            </a>
            <a href="products.php?category=meat" class="category-card">
                <img src="assets/images/cat_meat.jpg" class="category-img" alt="গরু-ছাগলের মাংস" onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='block';"><div class="category-icon" style="display:none;">🥩</div><h3>গরু-ছাগলের মাংস</h3>
            </a>
            <a href="products.php?category=deshi-mas" class="category-card">
                <img src="assets/images/cat_deshi-mas.jpg" class="category-img" alt="দেশি মাছ" onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='block';"><div class="category-icon" style="display:none;">🐟</div><h3>দেশি মাছ</h3>
            </a>
            <a href="products.php?category=dairy-ponno" class="category-card">
                <img src="assets/images/cat_dairy-ponno.jpg" class="category-img" alt="ডেইরি পণ্য" onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='block';"><div class="category-icon" style="display:none;">🥛</div><h3>ডেইরি পণ্য</h3>
            </a>
        <?php endif; ?>
    </div>
</section>

<!-- Featured Products Section -->
<section class="section">
    <h2 class="section-title">সবচেয়ে <span>জনপ্রিয় পণ্য</span></h2>
    <div class="marquee-wrapper">
        <?php if($featured_products): ?>
            <div class="marquee-track">
                <?php 
                // Duplicate for seamless loop
                $marquee_items = array_merge($featured_products, $featured_products, $featured_products); 
                foreach($marquee_items as $product): 
                ?>
                <?php 
                    $img_src = '';
                    if (!empty($product['image']) && (strpos($product['image'], 'http') === 0 || file_exists(__DIR__ . '/assets/images/' . $product['image']))) {
                        $img_src = (strpos($product['image'], 'http') === 0) ? $product['image'] : 'assets/images/' . $product['image'];
                    } else {
                        $cat_id = $product['category_id'] ?? 1;
                        $img_src = $category_images[$cat_id] ?? 'https://images.unsplash.com/photo-1628102491629-77858ab5721f?q=80&w=400&auto=format&fit=crop';
                    }
                ?>
                <div class="product-card"
                     data-product-id="<?= $product['id'] ?>"
                     data-product-name="<?= htmlspecialchars($product['name']) ?>"
                     data-product-desc="<?= htmlspecialchars($product['description']) ?>"
                     data-product-price="<?= number_format($product['price'], 2) ?>"
                     data-product-unit="<?= htmlspecialchars($product['unit']) ?>"
                     data-product-category="<?= htmlspecialchars($product['category_name']) ?>"
                     data-product-stock="<?= $product['stock'] ?>"
                     data-product-featured="1"
                     data-product-img="<?= htmlspecialchars($img_src) ?>">
                    <span class="badge-featured">Popular</span>
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
                            <div style="font-size:0.85rem; color:var(--primary-light); font-weight:600; margin-bottom:1rem; flex-grow:1;"><i class="fa-solid fa-circle-check"></i> স্টকে আছে</div>
                        <?php else: ?>
                            <div style="font-size:0.85rem; color:#dc2626; font-weight:600; margin-bottom:1rem; flex-grow:1;"><i class="fa-solid fa-circle-xmark"></i> আউট অফ স্টক</div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="product-card-actions">
                        <button class="add-to-cart-seamless add-to-cart" data-id="<?= $product['id'] ?>" <?= $product['stock'] <= 0 ? 'disabled' : '' ?>>
                            <i class="fa-solid fa-cart-plus"></i> কার্টে যোগ করুন
                        </button>
                        <button class="btn-details" onclick="openProductModal(this.closest('.product-card'))">
                            <i class="fa-solid fa-eye"></i> বিস্তারিত
                        </button>
                    </div>
                    
                </div>
            <?php endforeach; ?>
            </div>
        <?php else: ?>
             <p style="text-align:center; color:gray;">কোনো জনপ্রিয় পণ্য পাওয়া যায়নি। দয়া করে অ্যাডমিন প্যানেল থেকে যোগ করুন।</p>
        <?php endif; ?>
    </div>
    
    <div style="text-align: center; margin-top: 4rem;">
        <a href="products.php" class="btn btn-outline" style="padding: 1rem 3rem; font-size:1.15rem;">সব পণ্য দেখুন <i class="fa-solid fa-arrow-right-long" style="margin-left:0.5rem;"></i></a>
    </div>
</section>

<!-- Story/About CTA Section -->
<section class="section" style="background: var(--bg-color);">
    <div style="display:flex; flex-wrap:wrap; gap:5rem; align-items:center;">
        <div style="flex:1; min-width:300px; position:relative;">
            <!-- Decorative organic element -->
            <div style="position:absolute; top:-20px; left:-20px; width:100px; height:100px; background:var(--accent); border-radius:50%; filter:blur(30px); opacity:0.3; z-index:0;"></div>
            <img src="assets/images/krishok6.jpg" style="width:100%; border-radius:30px; box-shadow:0 30px 60px rgba(0,0,0,0.1); position:relative; z-index:1;" alt="Happy Farmer Handing Organic Food">
        </div>
        <div style="flex:1; min-width:300px;">
            <h2 class="section-title" style="text-align:left; margin-bottom:1.5rem; color:var(--text-dark);">কৃষকের মুখের <span>হাসি</span> আর আপনার স্বাস্থ্য।</h2>
            <p style="font-size:1.15rem; color:var(--text-light); margin-bottom:2.5rem; line-height:1.8;">
                গ্রীন টেক ফার্ম-এর মূল উদ্দেশ্য হলো প্রান্তিক কৃষকদের ন্যায্য পাওনা নিশ্চিত করা এবং শহরের মানুষদের সম্পূর্ণ ভেজালমুক্ত পণ্য পৌঁছে দেওয়া। আমরা মধ্যস্বত্বভোগীদের এড়িয়ে সরাসরি কৃষকের জমি থেকে পণ্য সংগ্রহ করি। এতে কৃষক যেমন লাভবান হন, তেমনি আপনিও পান ১০০% খাঁটি পণ্যের নিশ্চয়তা।
            </p>
            <div style="display:flex; gap:3rem; margin-bottom: 3rem;">
                <div>
                    <h3 style="font-family:'Outfit',sans-serif; color:var(--primary); font-size:2.8rem; font-weight:800;">১০০+</h3>
                    <p style="color:var(--text-light); font-weight:500;">সংযুক্ত কৃষক</p>
                </div>
                <div>
                    <h3 style="font-family:'Outfit',sans-serif; color:var(--primary); font-size:2.8rem; font-weight:800;">৫০+</h3>
                    <p style="color:var(--text-light); font-weight:500;">তাজা পণ্য</p>
                </div>
                <div>
                    <h3 style="font-family:'Outfit',sans-serif; color:var(--primary); font-size:2.8rem; font-weight:800;">৫k+</h3>
                    <p style="color:var(--text-light); font-weight:500;">সন্তুষ্ট গ্রাহক</p>
                </div>
            </div>
            <a href="about.php" class="btn" style="padding:1rem 2.5rem;">আমাদের গল্প পড়ুন</a>
        </div>
    </div>
</section>

<!-- Latest Blog Posts Section -->
<section class="section">
    <h2 class="section-title">সাম্প্রতিক <span>ব্লগ পোস্ট</span></h2>
    <?php
    $latest_blogs = [];
    try {
        $latest_blogs = $pdo->query("SELECT * FROM blogs WHERE is_published = 1 ORDER BY created_at DESC LIMIT 3")->fetchAll();
    } catch(Exception $e) {}
    ?>
    
    <?php if(!empty($latest_blogs)): ?>
    <div style="max-width:1200px; margin:0 auto; display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:2rem;">
        <?php foreach($latest_blogs as $lb): ?>
            <?php
            $lb_img = '';
            if(!empty($lb['image']) && $lb['image'] !== 'default_blog.jpg') {
                $lb_img = 'assets/images/'.$lb['image'];
            }
            if(empty($lb_img)) {
                $lb_img = $lb['type'] === 'farming_tips'
                    ? 'https://images.unsplash.com/photo-1625246333195-78d9c38ad449?q=80&w=600&auto=format&fit=crop'
                    : 'https://images.unsplash.com/photo-1592982537447-6f2eda297c84?q=80&w=600&auto=format&fit=crop';
            }
            ?>
            <a href="blog_detail.php?id=<?= $lb['id'] ?>" style="text-decoration:none; color:inherit;">
                <div style="background:var(--card-bg); border-radius:20px; overflow:hidden; border:1px solid var(--glass-border); box-shadow:0 10px 30px rgba(0,0,0,0.3); transition:all 0.4s cubic-bezier(0.25,0.8,0.25,1); cursor:pointer;"
                     onmouseover="this.style.transform='translateY(-8px)'; this.style.boxShadow='0 20px 50px rgba(0,0,0,0.5)';"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 30px rgba(0,0,0,0.3)';">
                    <div style="position:relative; overflow:hidden; height:200px;">
                        <img src="<?= htmlspecialchars($lb_img) ?>" alt="<?= htmlspecialchars($lb['title']) ?>" 
                             style="width:100%; height:100%; object-fit:cover; transition:transform 0.5s ease;"
                             onmouseover="this.style.transform='scale(1.05)';"
                             onmouseout="this.style.transform='scale(1)';">
                        <div style="position:absolute; top:1rem; left:1rem;">
                            <?php if($lb['type'] === 'farming_tips'): ?>
                                <span style="background:rgba(16,185,129,0.9); color:white; padding:0.35rem 0.9rem; border-radius:50px; font-size:0.78rem; font-weight:700; font-family:'Outfit',sans-serif;">🌾 কৃষি টিপস</span>
                            <?php else: ?>
                                <span style="background:rgba(167,139,250,0.9); color:white; padding:0.35rem 0.9rem; border-radius:50px; font-size:0.78rem; font-weight:700; font-family:'Outfit',sans-serif;">👨‍🌾 কৃষকের গল্প</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div style="padding:1.3rem;">
                        <h3 style="font-family:'Outfit',sans-serif; font-size:1.15rem; font-weight:700; color:var(--white); margin-bottom:0.6rem; line-height:1.4;">
                            <?= htmlspecialchars($lb['title']) ?>
                        </h3>
                        <p style="color:var(--text-light); font-size:0.9rem; line-height:1.6; opacity:0.8; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; margin-bottom:1rem;">
                            <?= htmlspecialchars($lb['excerpt'] ?: mb_strimwidth(strip_tags($lb['content']), 0, 120, '...')) ?>
                        </p>
                        <div style="display:flex; justify-content:space-between; align-items:center; padding-top:0.8rem; border-top:1px solid var(--glass-border);">
                            <span style="color:var(--text-light); font-size:0.8rem; opacity:0.6;">
                                <i class="fa-regular fa-calendar"></i> <?= date('d M Y', strtotime($lb['created_at'])) ?>
                            </span>
                            <span style="color:var(--accent); font-size:0.85rem; font-weight:600;">
                                পড়ুন <i class="fa-solid fa-arrow-right" style="font-size:0.7rem;"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
    
    <div style="text-align:center; margin-top:3rem;">
        <a href="blog.php" class="btn btn-outline" style="padding:1rem 3rem; font-size:1.1rem;">সব পোস্ট পড়ুন <i class="fa-solid fa-arrow-right-long" style="margin-left:0.5rem;"></i></a>
    </div>
    <?php else: ?>
        <p style="text-align:center; color:var(--text-light); opacity:0.6;">শীঘ্রই নতুন পোস্ট আসছে!</p>
    <?php endif; ?>
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
