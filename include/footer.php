    </div>
</div>

<!-- Footer -->
<footer class="bg-white border-t border-gray-200 py-4 mt-auto">
    <div class="container mx-auto px-4">
        <div class="text-center text-gray-600 text-sm">
            <p>&copy; <?= date('Y') ?> POS System. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- Global Scripts -->
<script>
// Global functions
function showAlert(message, type = 'success') {
    Swal.fire({
        title: type === 'success' ? 'Success!' : 'Error!',
        text: message,
        icon: type,
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'OK'
    });
}

function confirmDelete(url, message = 'Are you sure you want to delete this item?') {
    Swal.fire({
        title: 'Are you sure?',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    try {
                        const data = JSON.parse(response);
                        if (data.success) {
                            showAlert(data.message, 'success');
                            // Refresh the current page data based on the page type
                            if (typeof loadCategories === 'function') {
                                loadCategories();
                            } else if (typeof loadProducts === 'function') {
                                loadProducts();
                            } else if (typeof loadMembers === 'function') {
                                loadMembers();
                            } else if (typeof loadSuppliers === 'function') {
                                loadSuppliers();
                            } else if (typeof loadUom === 'function') {
                                loadUom();
                            } else if (typeof loadStockTakeSessions === 'function') {
                                loadStockTakeSessions();
                            } else {
                                // Fallback: reload the page
                                location.reload();
                            }
                        } else {
                            showAlert(data.message, 'error');
                        }
                    } catch (e) {
                        showAlert('Error processing response', 'error');
                    }
                },
                error: function() {
                    showAlert('Error deleting item', 'error');
                }
            });
        }
    });
}

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-MY', {
        style: 'currency',
        currency: 'MYR'
    }).format(amount);
}

// Format date
function formatDate(date) {
    return new Date(date).toLocaleDateString('en-MY');
}

// Auto-hide alerts
$(document).ready(function() {
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 3000);
});
</script>

</body>
</html>
