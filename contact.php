<?php 
require_once __DIR__ . '/includes/header.php'; 

// Handle form submission
$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Fallback: If 'messages' table doesn't exist yet, we capture but silently succeed/fail 
    // based on original logic. The user didn't mention adding a messages table, but let's keep the existing logic.
    if (!empty($name) && !empty($email) && !empty($message)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $message]);
            $success_msg = 'আপনার বার্তা সফলভাবে পাঠানো হয়েছে! আমরা দ্রুত আপনার সাথে যোগাযোগ করব।';
        } catch (Exception $e) {
            // If the table doesn't exist, we'll still show success just to not break UI 
            // if we are purely doing frontend work, but better to log error_msg.
            // Actually, let's keep the error_msg but gracefully handle it.
            $error_msg = 'দুঃখিত, কোনো একটি সমস্যা হয়েছে। আবার চেষ্টা করুন। (' . $e->getMessage() . ')';
        }
    } else {
        $error_msg = 'দয়া করে সকল তথ্য সঠিকভাবে পূরণ করুন।';
    }
}
?>

<!-- Hero Header Section -->
<section style="background: linear-gradient(135deg, var(--primary) 0%, #1b4332 50%, var(--primary-light) 100%); padding: 6rem 5% 4rem; color: white; text-align:center; position:relative; overflow:hidden;">
    <!-- Decorative elements -->
    <div style="position:absolute; top:-40px; left:5%; width:200px; height:200px; border-radius:50%; background:var(--accent); opacity:0.08; filter:blur(50px);"></div>
    <div style="position:absolute; bottom:-30px; right:8%; width:250px; height:250px; border-radius:50%; background:#a3e635; opacity:0.06; filter:blur(60px);"></div>
    <div style="position:absolute; top:30%; left:50%; width:300px; height:300px; border-radius:50%; background:var(--primary-light); opacity:0.05; filter:blur(70px); transform:translateX(-50%);"></div>
    
    <div style="position:relative; z-index:2;">
        <div style="display:inline-block; background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.15); padding:0.5rem 1.5rem; border-radius:50px; font-size:0.9rem; margin-bottom:1.5rem; backdrop-filter:blur(10px);">
            <i class="fa-solid fa-headset" style="color:var(--accent); margin-right:0.3rem;"></i> ২৪/৭ গ্রাহক সেবা
        </div>
        <h1 style="font-family:'Outfit',sans-serif; font-size:3.5rem; font-weight:800; color:var(--white); margin-bottom:1rem;">
            আমাদের সাথে <span style="color:var(--accent);">যোগাযোগ</span> করুন
        </h1>
        <p style="font-size:1.15rem; opacity:0.85; max-width:650px; margin:0 auto; line-height:1.8;">
            যেকোনো প্রশ্ন, মতামত বা সহায়তার জন্য আমাদের সাপোর্ট টিম সর্বদা আপনার পাশে আছে। নির্দ্বিধায় আমাদের মেসেজ করুন বা কল করুন।
        </p>
    </div>
</section>

<!-- Main Contact Section -->
<section class="section" style="padding-top: 4rem;">
    <div style="max-width:1200px; margin:0 auto;">
        
        <!-- Info Cards Grid -->
        <div class="grid-2" style="margin-bottom:4rem;">
            <!-- Location Card -->
            <div style="background:var(--card-bg); padding:2.5rem; border-radius:20px; text-align:center; box-shadow:0 15px 35px rgba(0,0,0,0.2); border:1px solid var(--glass-border); transition:transform 0.3s;" onmouseover="this.style.transform='translateY(-10px)';" onmouseout="this.style.transform='translateY(0)';">
                <div style="width:70px; height:70px; background:rgba(16,185,129,0.1); color:var(--accent); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:2rem; margin:0 auto 1.5rem;">
                    <i class="fa-solid fa-location-dot"></i>
                </div>
                <h3 style="color:var(--white); font-family:'Outfit',sans-serif; font-size:1.5rem; margin-bottom:1rem;">অফিসের ঠিকানা</h3>
                <p style="color:var(--text-light); line-height:1.6; font-size:1.05rem;">১২২, মিরপুর ১০, সেনপাড়া পর্বতা<br>ঢাকা ১২১৬, বাংলাদেশ</p>
            </div>
            
            <!-- Phone Card -->
            <div style="background:var(--card-bg); padding:2.5rem; border-radius:20px; text-align:center; box-shadow:0 15px 35px rgba(0,0,0,0.2); border:1px solid var(--glass-border); transition:transform 0.3s;" onmouseover="this.style.transform='translateY(-10px)';" onmouseout="this.style.transform='translateY(0)';">
                <div style="width:70px; height:70px; background:rgba(96,165,250,0.1); color:#60a5fa; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:2rem; margin:0 auto 1.5rem;">
                    <i class="fa-solid fa-phone-volume"></i>
                </div>
                <h3 style="color:var(--white); font-family:'Outfit',sans-serif; font-size:1.5rem; margin-bottom:1rem;">ফোন নম্বর</h3>
                <p style="color:var(--text-light); line-height:1.6; font-size:1.05rem;">+88 01700-000000<br>+88 01900-000000</p>
            </div>
            
            <!-- Email Card -->
            <div style="background:var(--card-bg); padding:2.5rem; border-radius:20px; text-align:center; box-shadow:0 15px 35px rgba(0,0,0,0.2); border:1px solid var(--glass-border); transition:transform 0.3s;" onmouseover="this.style.transform='translateY(-10px)';" onmouseout="this.style.transform='translateY(0)';">
                <div style="width:70px; height:70px; background:rgba(167,139,250,0.1); color:#a78bfa; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:2rem; margin:0 auto 1.5rem;">
                    <i class="fa-solid fa-envelope-open-text"></i>
                </div>
                <h3 style="color:var(--white); font-family:'Outfit',sans-serif; font-size:1.5rem; margin-bottom:1rem;">ইমেইল ও সাপোর্ট</h3>
                <p style="color:var(--text-light); line-height:1.6; font-size:1.05rem;">support@krishidibanisi.com<br>info@krishidibanisi.com</p>
            </div>
        </div>

        <div style="display:flex; flex-wrap:wrap; gap:3rem;">
            
            <!-- Message Form -->
            <div style="flex:1; min-width:400px; background:var(--card-bg); border-radius:24px; padding:3rem; border:1px solid var(--glass-border); box-shadow:0 20px 50px rgba(0,0,0,0.3);">
                <h2 style="font-family:'Outfit',sans-serif; color:var(--white); font-size:2.2rem; margin-bottom:0.5rem;">আমাদের লিখুন</h2>
                <p style="color:var(--text-light); margin-bottom:2.5rem; font-size:1.05rem;">কোনো প্রশ্ন বা পরামর্শ থাকলে নিচের ফর্মটি পূরণ করুন।</p>
                
                <?php if ($success_msg): ?>
                    <div style="background: rgba(16, 185, 129, 0.15); color: #10b981; padding: 1.2rem; border-radius: 12px; margin-bottom: 2rem; border: 1px solid rgba(16, 185, 129, 0.3); font-weight:500; display:flex; align-items:center; gap:0.8rem;">
                        <i class="fa-solid fa-circle-check" style="font-size:1.4rem;"></i> <?= $success_msg ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error_msg): ?>
                    <div style="background: rgba(239, 68, 68, 0.15); color: #ef4444; padding: 1.2rem; border-radius: 12px; margin-bottom: 2rem; border: 1px solid rgba(239, 68, 68, 0.3); font-weight:500; display:flex; align-items:center; gap:0.8rem;">
                        <i class="fa-solid fa-circle-exclamation" style="font-size:1.4rem;"></i> <?= $error_msg ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="contact.php" style="display:flex; flex-direction:column; gap:1.5rem;">
                    <div class="grid-2">
                        <div>
                            <label style="display:block; color:var(--white); font-weight:600; margin-bottom:0.5rem;">আপনার নাম</label>
                            <input type="text" name="name" class="modern-input" placeholder="যেমন: রহিম মিয়া" required style="width:100%; padding:1rem 1.2rem; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.1); border-radius:12px; color:white; font-size:1rem;">
                        </div>
                        <div>
                            <label style="display:block; color:var(--white); font-weight:600; margin-bottom:0.5rem;">ইমেইল এড্রেস</label>
                            <input type="email" name="email" class="modern-input" placeholder="example@email.com" required style="width:100%; padding:1rem 1.2rem; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.1); border-radius:12px; color:white; font-size:1rem;">
                        </div>
                    </div>
                    
                    <div>
                        <label style="display:block; color:var(--white); font-weight:600; margin-bottom:0.5rem;">আপনার বার্তা</label>
                        <textarea name="message" class="modern-input" rows="6" placeholder="কীভাবে আমরা আপনাকে সাহায্য করতে পারি?" required style="width:100%; padding:1.2rem; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.1); border-radius:12px; color:white; font-size:1rem; resize:vertical;"></textarea>
                    </div>
                    
                    <button type="submit" class="btn" style="padding:1.2rem; font-size:1.1rem; border-radius:12px; display:flex; justify-content:center; align-items:center; gap:0.5rem; margin-top:0.5rem;">
                        <i class="fa-regular fa-paper-plane"></i> বার্তা পাঠান
                    </button>
                </form>
            </div>
            
            <!-- Embedded Map & Hours -->
            <div style="flex:1; min-width:400px; display:flex; flex-direction:column; gap:2rem;">
                
                <div style="background:var(--card-bg); border-radius:24px; padding:2rem; border:1px solid var(--glass-border); box-shadow:0 20px 50px rgba(0,0,0,0.3);">
                    <h3 style="font-family:'Outfit',sans-serif; color:var(--white); font-size:1.5rem; margin-bottom:1.5rem;">অফিস সূচি</h3>
                    <ul style="list-style:none; padding:0; margin:0; color:var(--text-light); font-size:1.05rem;">
                        <li style="display:flex; justify-content:space-between; padding:0.8rem 0; border-bottom:1px solid rgba(255,255,255,0.05);">
                            <span style="font-weight:600; color:var(--white);">শনিবার - বৃস্পতিবার</span>
                            <span style="color:var(--accent);">সকাল ৯:০০ - সন্ধ্যা ৭:০০</span>
                        </li>
                        <li style="display:flex; justify-content:space-between; padding:0.8rem 0;">
                            <span style="font-weight:600; color:var(--white);">শুক্রবার</span>
                            <span style="color:#ef4444; font-weight:600;">বন্ধ (সংগ্রহের জন্য শুধু)</span>
                        </li>
                    </ul>
                </div>

                <!-- Google Map Placeholder / Real iframe -->
                <div style="border-radius:24px; overflow:hidden; border:1px solid var(--glass-border); box-shadow:0 20px 50px rgba(0,0,0,0.3); height:100%; min-height:300px; position:relative;">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3650.5983460988937!2d90.3654215154316!3d23.823616884552467!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755c14c8682a473%3A0xa6c74743d52adb88!2sMirpur-10%2C%20Dhaka!5e0!3m2!1sen!2sbd!4v1680000000000!5m2!1sen!2sbd" width="100%" height="100%" style="border:0; filter:invert(90%) hue-rotate(180deg) contrast(80%);" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    <div style="position:absolute; bottom:15px; right:15px; background:rgba(0,0,0,0.7); padding:5px 15px; border-radius:20px; font-size:0.8rem; color:white; backdrop-filter:blur(5px);">
                        <i class="fa-solid fa-map-pin" style="color:var(--accent);"></i> গুগল ম্যাপ
                    </div>
                </div>

            </div>
            
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
