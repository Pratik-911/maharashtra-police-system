            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Additional JavaScript for enhanced functionality -->
    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Add active class to current navigation item
        document.addEventListener('DOMContentLoaded', function() {
            var currentUrl = window.location.pathname;
            var navLinks = document.querySelectorAll('.sidebar .nav-link');
            
            navLinks.forEach(function(link) {
                if (link.getAttribute('href') && currentUrl.includes(link.getAttribute('href').split('/').pop())) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>
