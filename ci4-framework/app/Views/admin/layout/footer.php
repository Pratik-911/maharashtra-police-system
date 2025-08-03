            </main>
        </div>
    </div>

    <!-- jQuery - Local -->
    <script src="<?= base_url('assets/js/jquery-3.7.1.min.js') ?>"></script>
    
    <!-- Bootstrap JS - Local -->
    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
    
    <!-- Simple Tables JS - Local -->
    <script src="<?= base_url('assets/js/simple-tables.js') ?>"></script>
    
    <script>
        // Initialize Enhanced Tables
        $(document).ready(function() {
            // Convert regular tables to enhanced tables
            $('.table').addClass('table-enhanced');
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);

        // Confirmation for delete actions
        $('.delete-btn').on('click', function(e) {
            if (!confirm('आपल्याला खात्री आहे की आपण हे डिलीट करू इच्छिता?')) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
