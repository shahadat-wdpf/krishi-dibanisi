/* ==========================================================================
   KRISHI DIBANISHI - STICKY NAVBAR INTERACTIVITY (VANILLA JS)
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {
    const hamburgerBtn = document.getElementById('kdHamburgerBtn');
    const mobileMenu = document.getElementById('kdMobileMenu');

    if (hamburgerBtn && mobileMenu) {
        hamburgerBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = mobileMenu.classList.contains('kd-open');

            if (isOpen) {
                closeMobileMenu();
            } else {
                openMobileMenu();
            }
        });

        // Close mobile menu on clicking any link inside mobile menu
        const mobileLinks = mobileMenu.querySelectorAll('a');
        mobileLinks.forEach(link => {
            link.addEventListener('click', () => {
                closeMobileMenu();
            });
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', (e) => {
            if (mobileMenu.classList.contains('kd-open') && 
                !mobileMenu.contains(e.target) && 
                !hamburgerBtn.contains(e.target)) {
                closeMobileMenu();
            }
        });

        // Close mobile menu on window resize above 900px
        window.addEventListener('resize', () => {
            if (window.innerWidth > 900 && mobileMenu.classList.contains('kd-open')) {
                closeMobileMenu();
            }
        });
    }

    function openMobileMenu() {
        hamburgerBtn.classList.add('kd-is-active');
        mobileMenu.classList.add('kd-open');
        hamburgerBtn.setAttribute('aria-expanded', 'true');
    }

    function closeMobileMenu() {
        hamburgerBtn.classList.remove('kd-is-active');
        mobileMenu.classList.remove('kd-open');
        hamburgerBtn.setAttribute('aria-expanded', 'false');
    }
});
