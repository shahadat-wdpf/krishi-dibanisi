<!-- footer.php -->
<footer class="footer">
    <div class="footer-inner">
        <div class="footer-grid">
            <div class="footer-col">
                <h4 style="font-family:'Outfit',sans-serif; font-size:1.8rem; font-weight:800; color:var(--white); margin-bottom:1.5rem; display:flex; align-items:center; gap:0.5rem;">
                    <i class="fa-solid fa-leaf" style="color:var(--accent);"></i> Green Tech Farm
                </h4>
                <p>গ্রীন টেক ফার্ম হলো গ্রামে উৎপাদিত সম্পূর্ণ খাঁটি এবং নিরাপদ খাদ্যপণ্য সরাসরি কৃষকের হাত থেকে আপনার ঘরে পৌঁছে দেওয়ার একটি বিশ্বস্ত উদ্যোগ। ভেজালমুক্ত জীবনের প্রতি আমাদের প্রতিশ্রুতি।</p>
                <div class="social-links">
                    <a href="<?= htmlspecialchars($site_settings['facebook_url'] ?? '#') ?>"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#"><i class="fa-brands fa-whatsapp"></i></a>
                    <a href="#"><i class="fa-brands fa-instagram"></i></a>
                    <a href="<?= htmlspecialchars($site_settings['youtube_url'] ?? '#') ?>"><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>
            
            <div class="footer-col" style="padding-top:0.5rem;">
                <h4>দ্রুত লিঙ্ক</h4>
                <ul>
                    <li><a href="index.php">হোম (Home)</a></li>
                    <li><a href="products.php">সকল পণ্য (All Products)</a></li>
                    <li><a href="about.php">আমাদের উদ্যোগ (Our Mission)</a></li>
                    <li><a href="contact.php">যোগাযোগ করুন (Contact Us)</a></li>
                </ul>
            </div>
            
            <div class="footer-col" style="padding-top:0.5rem;">
                <h4>ক্যাটাগরি</h4>
                <ul>
                    <li><a href="products.php?category=honey-ghee">খাঁটি মধু ও ঘি</a></li>
                    <li><a href="products.php?category=vegetables">বিষমুক্ত শাকসবজি</a></li>
                    <li><a href="products.php?category=rice-lentils">চিনিগুঁড়া চাল ও ডাল</a></li>
                    <li><a href="products.php?category=fruits">তাজা মৌসুমি ফলমূল</a></li>
                </ul>
            </div>
            
            <div class="footer-col" style="padding-top:0.5rem;">
                <h4>আমাদের সাথে থাকুন</h4>
                <p style="margin-bottom:1.2rem;">সবচেয়ে তাজা পণ্যের আপডেট এবং অফার পেতে সাবস্ক্রাইব করুন।</p>
                <form onsubmit="event.preventDefault(); alert('ধন্যবাদ! আপনি সাবস্ক্রাইব করেছেন।');" style="display:flex; flex-direction:column; gap:0.8rem;">
                    <input type="email" class="modern-input" placeholder="আপনার ইমেইল ঠিকানা..." required style="border:none; padding:1.2rem; background:rgba(255,255,255,0.08); color:white;">
                    <button type="submit" class="btn" style="background:var(--accent); color:var(--primary); width:100%;">সাবস্ক্রাইব</button>
                </form>
                
                <div style="margin-top:2rem;">
                    <p style="margin-bottom:0.5rem;"><i class="fa-solid fa-envelope" style="color:var(--accent); margin-right:0.5rem;"></i> <?= htmlspecialchars($site_settings['contact_email'] ?? 'admin@greentechfarm.com') ?></p>
                    <p><i class="fa-solid fa-phone" style="color:var(--accent); margin-right:0.5rem;"></i> <?= htmlspecialchars($site_settings['contact_phone'] ?? '+88 01700-000000') ?></p>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Green Tech Farm - গ্রীন টেক ফার্ম। সর্বস্বত্ব সংরক্ষিত। একটি স্বাস্থ্যকর পৃথিবীর প্রত্যাশায়।</p>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="assets/js/main.js"></script>
<!-- Font Awesome -->
<script src="https://kit.fontawesome.com/yourcode.js" crossorigin="anonymous"></script>
</body>
</html>
