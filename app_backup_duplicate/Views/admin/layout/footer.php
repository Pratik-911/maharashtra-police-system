            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        // Initialize DataTables
        $(document).ready(function() {
            $('.data-table').DataTable({
                "language": {
                    "lengthMenu": "_MENU_ नोंदी प्रति पृष्ठ दाखवा",
                    "zeroRecords": "कोणतीही नोंद सापडली नाही",
                    "info": "_START_ ते _END_ (_TOTAL_ पैकी)",
                    "infoEmpty": "0 ते 0 (0 पैकी)",
                    "infoFiltered": "(_MAX_ एकूण नोंदींमधून फिल्टर केले)",
                    "search": "शोधा:",
                    "paginate": {
                        "first": "पहिला",
                        "last": "शेवटचा",
                        "next": "पुढील",
                        "previous": "मागील"
                    }
                },
                "pageLength": 10,
                "responsive": true
            });
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
