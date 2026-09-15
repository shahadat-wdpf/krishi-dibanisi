        </main>
    </div> <!-- End Main Wrapper -->
    
    <script>
        // Simple JS for Admin Interactions
        document.querySelectorAll('.sidebar-menu a').forEach(link => {
            if(window.location.href.includes(link.getAttribute('href'))) {
                document.querySelectorAll('.sidebar-menu a').forEach(l => l.classList.remove('active'));
                link.classList.add('active');
            }
        });

        // ==================== Dropdown Toggle Logic ====================
        const notifToggle = document.getElementById('notifToggle');
        const notifDropdown = document.getElementById('notifDropdown');
        const profileToggle = document.getElementById('profileToggle');
        const profileDropdown = document.getElementById('profileDropdown');

        if (notifToggle && notifDropdown) {
            notifToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                notifDropdown.classList.toggle('show');
                if (profileDropdown) profileDropdown.classList.remove('show');
            });
        }

        if (profileToggle && profileDropdown) {
            profileToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                profileDropdown.classList.toggle('show');
                if (notifDropdown) notifDropdown.classList.remove('show');
            });
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (notifDropdown && !document.getElementById('notifWrapper').contains(e.target)) {
                notifDropdown.classList.remove('show');
            }
            if (profileDropdown && !document.getElementById('profileWrapper').contains(e.target)) {
                profileDropdown.classList.remove('show');
            }
        });
    </script>
</body>
</html>
