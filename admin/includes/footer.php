            </div> <!-- End of main-content -->
        </div> <!-- End of admin-container -->
        <script>
            // Add any JavaScript functionality here
            document.addEventListener('DOMContentLoaded', function() {
                // Example: Add confirmation for delete actions
                const deleteButtons = document.querySelectorAll('.delete-btn');
                deleteButtons.forEach(button => {
                    button.addEventListener('click', function(e) {
                        if (!confirm('Are you sure you want to delete this item?')) {
                            e.preventDefault();
                        }
                    });
                });
            });
        </script>
    </body>
</html> 