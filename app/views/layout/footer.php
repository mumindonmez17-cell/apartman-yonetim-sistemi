        <footer class="mt-auto pt-5 pb-3 text-center">
            <hr class="opacity-10 mb-4">
            <p class="text-muted small">
                &copy; <?php echo date('Y'); ?> <?php echo e($site_name); ?> - Tüm Hakları Saklıdır.<br>
                <span class="opacity-75">Mümin Dönmez Tarafından tasarlanmıştır</span>
            </p>
        </footer>
    </div> <!-- End Main Content -->

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            // Set active link
            var url = window.location.href;
            $('.sidebar .nav-link').each(function() {
                if (url.includes($(this).attr('href'))) {
                    $(this).addClass('active');
                }
            });

            // Sidebar Toggle Logic
            const sidebar = $('.sidebar');
            const mainContent = $('.main-content');
            const overlay = $('#sidebarOverlay');
            
            // Check local storage for preference (only for desktop)
            if ($(window).width() > 991 && localStorage.getItem('sidebar-collapsed') === 'true') {
                sidebar.addClass('collapsed');
                mainContent.addClass('expanded');
            }

            $('#sidebarToggle, #sidebarOverlay').on('click', function() {
                if ($(window).width() <= 991) {
                    // Mobile Behavior
                    sidebar.toggleClass('mobile-show');
                    overlay.toggleClass('active');
                    sidebar.removeClass('collapsed');
                    mainContent.removeClass('expanded');
                } else {
                    // Desktop Behavior
                    sidebar.toggleClass('collapsed');
                    mainContent.toggleClass('expanded');
                    sidebar.removeClass('mobile-show');
                    overlay.removeClass('active');
                    
                    // Save preference
                    localStorage.setItem('sidebar-collapsed', sidebar.hasClass('collapsed'));
                }
            });

            // Close mobile sidebar on link click
            $('.sidebar .nav-link').on('click', function() {
                if ($(window).width() <= 991) {
                    sidebar.removeClass('mobile-show');
                    overlay.removeClass('active');
                }
            });
        });
    </script>
</body>
</html>
