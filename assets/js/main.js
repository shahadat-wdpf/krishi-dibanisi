// Main JS for Krishi Dibanisi

document.addEventListener('DOMContentLoaded', () => {
    
    // ==================== HERO CAROUSEL ====================
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');
    const prevBtn = document.querySelector('.btn-prev');
    const nextBtn = document.querySelector('.btn-next');
    
    let currentSlide = 0;
    let slideInterval;

    if (slides.length > 0) {
        
        const showSlide = (index) => {
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));
            
            slides[index].classList.add('active');
            dots[index].classList.add('active');
            currentSlide = index;
        };

        const nextSlide = () => {
            let index = (currentSlide + 1) % slides.length;
            showSlide(index);
        };

        const prevSlide = () => {
            let index = (currentSlide - 1 + slides.length) % slides.length;
            showSlide(index);
        };

        // Auto play
        const startAutoPlay = () => {
            stopAutoPlay();
            slideInterval = setInterval(nextSlide, 6000);
        };

        const stopAutoPlay = () => {
            if (slideInterval) clearInterval(slideInterval);
        };

        // Event Listeners
        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                nextSlide();
                startAutoPlay();
            });
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                prevSlide();
                startAutoPlay();
            });
        }

        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                showSlide(index);
                startAutoPlay();
            });
        });

        // Initialize
        showSlide(0);
        startAutoPlay();
    }

    // ==================== AJAX ADD TO CART ====================
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    const cartCountSpan = document.querySelector('.cart-count');

    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            const originalText = this.innerHTML;
            
            // Visual feedback
            this.disabled = true;
            this.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> যোগ হচ্ছে...';

            fetch('add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update cart count UI
                    if(cartCountSpan) {
                        cartCountSpan.textContent = data.total_items;
                        // Small animation
                        cartCountSpan.style.transform = 'scale(1.5)';
                        setTimeout(() => cartCountSpan.style.transform = 'scale(1)', 300);
                    }
                    
                    // Button success state
                    this.style.background = '#40916c';
                    this.innerHTML = '<i class="fa-solid fa-check"></i> যোগ হয়েছে';
                    
                    setTimeout(() => {
                        this.disabled = false;
                        this.style.background = '';
                        this.innerHTML = originalText;
                    }, 2000);
                } else {
                    if (data.login_required) {
                        alert('পণ্য কার্টে যোগ করতে দয়া করে আগে লগইন করুন।');
                        window.location.href = 'login.php';
                    } else {
                        alert('পণ্য যোগ করতে সমস্যা হয়েছে: ' + data.message);
                    }
                    this.disabled = false;
                    this.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('একটি ভুল হয়েছে। আবার চেষ্টা করুন।');
                this.disabled = false;
                this.innerHTML = originalText;
            });
        });
    });

    // ==================== PRODUCT DETAIL MODAL ====================
    const modalOverlay = document.getElementById('productModalOverlay');
    const modalCloseBtn = document.getElementById('modalCloseBtn');

    // Global function to open the modal
    window.openProductModal = function(card) {
        if (!card || !modalOverlay) return;

        const productId = card.dataset.productId;
        const name = card.dataset.productName;
        const desc = card.dataset.productDesc;
        const price = card.dataset.productPrice;
        const unit = card.dataset.productUnit;
        const category = card.dataset.productCategory;
        const stock = parseInt(card.dataset.productStock);
        const featured = card.dataset.productFeatured === '1';
        const img = card.dataset.productImg;

        // Populate modal
        document.getElementById('modalProductImg').src = img;
        document.getElementById('modalProductImg').alt = name;
        document.getElementById('modalProductName').textContent = name;
        document.getElementById('modalCategory').textContent = category;
        document.getElementById('modalDescription').textContent = desc;
        document.getElementById('modalPrice').textContent = '৳ ' + price;
        document.getElementById('modalUnit').textContent = '/' + unit;

        // Badge
        const badge = document.getElementById('modalBadge');
        badge.style.display = featured ? 'inline-block' : 'none';

        // Stock info
        const stockEl = document.getElementById('modalStock');
        if (stock > 0) {
            stockEl.className = 'modal-stock-info in-stock';
            stockEl.innerHTML = '<i class="fa-solid fa-circle-check"></i> স্টকে আছে (' + stock + ' টি)';
        } else {
            stockEl.className = 'modal-stock-info out-of-stock';
            stockEl.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> আউট অফ স্টক';
        }

        // Cart button
        const cartBtn = document.getElementById('modalCartBtn');
        cartBtn.dataset.id = productId;
        cartBtn.disabled = stock <= 0;
        cartBtn.innerHTML = '<i class="fa-solid fa-cart-plus"></i> কার্টে যোগ করুন';

        // Show modal
        modalOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    };

    window.closeProductModal = function() {
        if (!modalOverlay) return;
        modalOverlay.classList.remove('active');
        document.body.style.overflow = '';
    };

    // Close button
    if (modalCloseBtn) {
        modalCloseBtn.addEventListener('click', closeProductModal);
    }

    // Click outside modal to close
    if (modalOverlay) {
        modalOverlay.addEventListener('click', function(e) {
            if (e.target === modalOverlay) {
                closeProductModal();
            }
        });
    }

    // Escape key to close
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeProductModal();
        }
    });

});
