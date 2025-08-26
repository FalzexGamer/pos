<?php
include 'include/conn.php';
include 'include/session.php';
include 'include/head.php';
include 'include/header.php';
include 'include/sidebar.php';

if (!isset($_GET['id'])) {
    header('Location: stock-take.php');
    exit;
}

$session_id = $_GET['id'];

// Get session details
$session_sql = "SELECT sts.*, u.full_name as created_by_name 
                FROM stock_take_sessions sts 
                LEFT JOIN users u ON sts.created_by = u.id 
                WHERE sts.id = ?";
$session_stmt = mysqli_prepare($conn, $session_sql);
mysqli_stmt_bind_param($session_stmt, "i", $session_id);
mysqli_stmt_execute($session_stmt);
$session_result = mysqli_stmt_get_result($session_stmt);
$session = mysqli_fetch_array($session_result);

if (!$session) {
    header('Location: stock-take.php');
    exit;
}
?>

<!-- Main Content -->
<div class="main-content ml-0 lg:ml-64 pt-16">
    <!-- Background Gradient -->
    <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-white to-indigo-50 -z-10"></div>
    
    <div class="p-4 sm:p-6 lg:p-8">
        <!-- Page Header with Glassmorphism -->
        <div class="backdrop-blur-sm bg-white/70 rounded-2xl shadow-xl border border-white/20 p-6 mb-8">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-4 sm:space-y-0">
                <div>
                    <div class="flex items-center space-x-3 mb-2">
                        <div class="p-2 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl">
                            <i class="fas fa-clipboard-check text-white text-xl"></i>
                        </div>
                        <h1 class="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                            Stock Take Session
                        </h1>
                    </div>
                    <p class="text-gray-600 text-lg"><?= htmlspecialchars($session['session_name']) ?></p>
                </div>
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                    <a href="stock-take.php" class="inline-flex items-center px-4 py-2 text-gray-600 border border-gray-300 rounded-xl hover:bg-gray-50 transition-all duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Stock Take
                    </a>
                    <?php if ($session['status'] == 'in_progress'): ?>
                        <button onclick="completeSession(<?= $session_id ?>)" class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <i class="fas fa-check mr-2"></i>Complete Session
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Session Details -->
        <div class="backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-xl border border-white/20 overflow-hidden mb-8">
            <div class="px-6 py-6 border-b border-gray-200/50 bg-gradient-to-r from-gray-50/50 to-gray-100/50">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg">
                        <i class="fas fa-info-circle text-white"></i>
                    </div>
                    <h3 class="text-lg lg:text-xl font-semibold text-gray-900">Session Details</h3>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Session Name</label>
                        <p class="text-sm text-gray-900 font-medium"><?= htmlspecialchars($session['session_name']) ?></p>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Status</label>
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                            <?= $session['status'] == 'completed' ? 'bg-green-100 text-green-800' : ($session['status'] == 'in_progress' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') ?>">
                            <?= ucfirst(str_replace('_', ' ', $session['status'])) ?>
                        </span>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Start Date</label>
                        <p class="text-sm text-gray-900"><?= date('M d, Y H:i', strtotime($session['start_date'])) ?></p>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">End Date</label>
                        <p class="text-sm text-gray-900"><?= $session['end_date'] ? date('M d, Y H:i', strtotime($session['end_date'])) : 'Not completed' ?></p>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Created By</label>
                        <p class="text-sm text-gray-900"><?= htmlspecialchars($session['created_by_name']) ?></p>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Notes</label>
                        <p class="text-sm text-gray-900"><?= htmlspecialchars($session['notes'] ?: 'No notes') ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Take Items -->
        <div class="backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-xl border border-white/20 overflow-hidden">
            <div class="px-6 py-6 border-b border-gray-200/50 bg-gradient-to-r from-gray-50/50 to-gray-100/50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-lg">
                            <i class="fas fa-list text-white"></i>
                        </div>
                        <h3 class="text-lg lg:text-xl font-semibold text-gray-900">Counted Items</h3>
                    </div>
                    <div class="flex items-center space-x-2 text-sm text-gray-600">
                        <i class="fas fa-info-circle"></i>
                        <span id="items-count">0 items</span>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table id="items-table" class="w-full">
                    <thead class="bg-gradient-to-r from-gray-50/80 to-gray-100/80">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">SKU</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">System Stock</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Counted Stock</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Difference</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Notes</th>
                        </tr>
                    </thead>
                    <tbody id="items-tbody" class="bg-white/50 divide-y divide-gray-200/50">
                        <!-- Items will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom DataTable Styling for Stock Take Session */
.dataTables_wrapper .dataTables_filter input {
    padding-left: 3rem !important;
    background: rgba(255, 255, 255, 0.8) !important;
    backdrop-filter: blur(8px) !important;
    border: 1px solid rgba(229, 231, 235, 1) !important;
    border-radius: 0.75rem !important;
    transition: all 0.2s ease !important;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06) !important;
}

.dataTables_wrapper .dataTables_filter input:focus {
    outline: none !important;
    border-color: transparent !important;
    box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.5) !important;
    background: rgba(255, 255, 255, 0.95) !important;
}

.dataTables_wrapper .dataTables_length select {
    background: rgba(255, 255, 255, 0.8) !important;
    backdrop-filter: blur(8px) !important;
    border: 1px solid rgba(229, 231, 235, 1) !important;
    border-radius: 0.75rem !important;
    transition: all 0.2s ease !important;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06) !important;
}

.dataTables_wrapper .dataTables_length select:focus {
    outline: none !important;
    border-color: transparent !important;
    box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.5) !important;
    background: rgba(255, 255, 255, 0.95) !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    background: rgba(255, 255, 255, 0.8) !important;
    backdrop-filter: blur(8px) !important;
    border: 1px solid rgba(229, 231, 235, 1) !important;
    border-radius: 0.5rem !important;
    transition: all 0.2s ease !important;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06) !important;
    margin: 0 0.25rem !important;
    padding: 0.5rem 0.75rem !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: rgba(240, 253, 244, 1) !important;
    border-color: rgba(187, 247, 208, 1) !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: linear-gradient(135deg, #22c55e, #16a34a) !important;
    color: white !important;
    border-color: #22c55e !important;
    box-shadow: 0 4px 6px -1px rgba(34, 197, 94, 0.3), 0 2px 4px -1px rgba(34, 197, 94, 0.2) !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    background: linear-gradient(135deg, #16a34a, #15803d) !important;
    transform: translateY(-1px) !important;
}
</style>

<script>
    $(document).ready(function() {
        loadSessionItems();

        // Initialize DataTable with modern styling
        $('#items-table').DataTable({
            pageLength: 25,
            order: [[0, 'asc']],
            responsive: true,
            language: {
                search: "",
                searchPlaceholder: "Search items...",
                lengthMenu: "Show _MENU_ items per page",
                info: "Showing _START_ to _END_ of _TOTAL_ items",
                paginate: {
                    first: '<i class="fas fa-angle-double-left"></i>',
                    previous: '<i class="fas fa-angle-left"></i>',
                    next: '<i class="fas fa-angle-right"></i>',
                    last: '<i class="fas fa-angle-double-right"></i>'
                }
            },
            dom: '<"flex flex-col sm:flex-row justify-between items-center mb-6"lf>rt<"flex flex-col sm:flex-row justify-between items-center mt-6"ip>',
            initComplete: function() {
                // Add custom styling to DataTable elements
                $('.dataTables_filter input').addClass('px-4 py-3 pl-12 pr-4 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 placeholder-gray-400');
                $('.dataTables_length select').addClass('px-4 py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200');
                
                // Ensure proper positioning for search icon
                $('.dataTables_filter').addClass('relative');
                
                // Style pagination buttons
                $('.dataTables_paginate .paginate_button').addClass('px-3 py-2 mx-1 rounded-lg border border-gray-200 bg-white/80 backdrop-blur-sm hover:bg-green-50 hover:border-green-200 transition-all duration-200');
                $('.dataTables_paginate .paginate_button.current').addClass('bg-green-600 text-white border-green-600 hover:bg-green-700');
                $('.dataTables_paginate .paginate_button.disabled').addClass('bg-gray-100 text-gray-400 border-gray-100 cursor-not-allowed');
                
                // Style info text
                $('.dataTables_info').addClass('text-sm text-gray-600 font-medium');
                
                // Style length menu
                $('.dataTables_length label').addClass('text-sm font-medium text-gray-700');
            }
        });
    });

    // Load session items
    function loadSessionItems() {
        $.ajax({
            url: 'ajax/get-stock-take-items.php',
            type: 'GET',
            data: {
                session_id: <?= $session_id ?>
            },
            success: function(response) {
                $('#items-tbody').html(response);
                
                // Update item count
                const itemCount = $('#items-tbody tr').not('.no-data').length;
                $('#items-count').text(itemCount + ' items');
            },
            error: function() {
                showAlert('Error loading session items', 'error');
            }
        });
    }

    // Complete session
    function completeSession(sessionId) {
        if (confirm('Are you sure you want to complete this stock take session? This will update all stock levels based on the counted quantities.')) {
            $.ajax({
                url: 'ajax/complete-stock-take-session.php',
                type: 'POST',
                data: {
                    id: sessionId
                },
                success: function(response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        showAlert(data.message, 'success');
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showAlert(data.message, 'error');
                    }
                },
                error: function() {
                    showAlert('Error completing session', 'error');
                }
            });
        }
    }
</script>

<?php 
mysqli_stmt_close($session_stmt);
include 'include/footer.php'; 
?>