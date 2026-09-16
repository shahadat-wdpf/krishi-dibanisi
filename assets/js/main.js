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

    // ==================== DYNAMIC CART BADGE UPDATE ====================
    window.updateCartBadge = function(count) {
        const cartBadges = document.querySelectorAll('.kd-cart-count, .cart-count');
        cartBadges.forEach(badge => {
            badge.textContent = count;
            if (badge.classList.contains('kd-cart-count')) {
                badge.style.display = 'inline-flex';
            }
            badge.style.transform = 'scale(1.3)';
            badge.style.transition = 'transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
            setTimeout(() => {
                badge.style.transform = 'scale(1)';
            }, 300);
        });
    };

    // ==================== AJAX ADD TO CART ====================
    document.addEventListener('click', function(e) {
        const button = e.target.closest('.add-to-cart');
        if (!button) return;

        const productId = button.getAttribute('data-id') || button.dataset.id || button.getAttribute('data-product-id');
        if (!productId) return;

        e.preventDefault();
        const originalText = button.innerHTML;

        // Visual feedback
        button.disabled = true;
        button.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> যোগ হচ্ছে...';

        fetch('add_to_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `product_id=${encodeURIComponent(productId)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Instantly update cart count UI across navbar
                window.updateCartBadge(data.total_items);

                // Button success state
                button.style.background = '#10b981';
                button.style.color = '#ffffff';
                button.innerHTML = '<i class="fa-solid fa-check"></i> যোগ হয়েছে';

                setTimeout(() => {
                    button.disabled = false;
                    button.style.background = '';
                    button.style.color = '';
                    button.innerHTML = originalText;
                }, 2000);
            } else {
                alert('পণ্য যোগ করতে সমস্যা হয়েছে: ' + (data.message || 'Error'));
                button.disabled = false;
                button.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('একটি ভুল হয়েছে। আবার চেষ্টা করুন।');
            button.disabled = false;
            button.innerHTML = originalText;
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
        cartBtn.setAttribute('data-id', productId);
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
