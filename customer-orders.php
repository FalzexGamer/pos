<?php
include 'include/conn.php';
include 'include/session.php';
include 'include/head.php';
include 'include/header.php';
include 'include/sidebar.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get company settings
$company_query = mysqli_query($conn, "SELECT * FROM company_settings WHERE id = 1");
$company = mysqli_fetch_array($company_query);
?>

<!-- Main Content -->
<div class="main-content ml-0 lg:ml-64 pt-16">
    <div class="p-6">
        <!-- Page Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-shopping-cart mr-3 text-blue-600"></i>
                        Customer Orders Management
                    </h1>
                    <p class="text-gray-600 mt-1">Manage and track customer cart orders</p>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">Last updated: <span id="last-updated">Just now</span></span>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Status</label>
                    <select id="status-filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="ordered">Ordered</option>
                        <option value="paid">Paid</option>
                        <option value="abandoned">Abandoned</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Table</label>
                    <select id="table-filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Tables</option>
                        <?php
                        // Get unique table IDs
                        $table_query = mysqli_query($conn, "SELECT DISTINCT table_id FROM customer_cart ORDER BY table_id");
                        while ($table = mysqli_fetch_array($table_query)) {
                            echo "<option value='{$table['table_id']}'>Table {$table['table_id']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" id="search-input" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Search by product name or SKU...">
                </div>
                <div class="flex items-end">
                    <button onclick="refreshOrders()" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center">
                        <i class="fas fa-sync-alt mr-2"></i>
                        Refresh
                    </button>
                </div>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Customer Orders</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="orders-table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Table</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="orders-tbody" class="bg-white divide-y divide-gray-200">
                        <!-- Orders will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-500 rounded-lg p-4 text-white">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-shopping-cart text-2xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium opacity-90">Active Orders</p>
                        <p id="active-count" class="text-2xl font-bold">0</p>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-green-500 to-emerald-500 rounded-lg p-4 text-white">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium opacity-90">Ordered</p>
                        <p id="ordered-count" class="text-2xl font-bold">0</p>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg p-4 text-white">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-dollar-sign text-2xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium opacity-90">Paid</p>
                        <p id="paid-count" class="text-2xl font-bold">0</p>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-orange-500 to-red-500 rounded-lg p-4 text-white">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-2xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium opacity-90">Abandoned</p>
                        <p id="abandoned-count" class="text-2xl font-bold">0</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Order Details Modal -->
<div id="orderDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg max-w-2xl w-full mx-4 max-h-[90vh] overflow-hidden">
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Order Details</h3>
            <button onclick="closeOrderModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]" id="orderDetailsBody">
            <!-- Order details will be loaded here -->
        </div>
        <div class="flex items-center justify-end space-x-3 p-6 border-t border-gray-200 bg-gray-50">
            <button onclick="closeOrderModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                Close
            </button>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    loadCustomerOrders();
    
    // Filter change events
    $('#status-filter, #table-filter').on('change', function() {
        loadCustomerOrders();
    });
    
    // Search input event
    $('#search-input').on('keyup', function() {
        loadCustomerOrders();
    });
});

function loadCustomerOrders() {
    const status = $('#status-filter').val();
    const table = $('#table-filter').val();
    const search = $('#search-input').val();
    
    $.ajax({
        url: 'ajax/get-customer-orders.php',
        type: 'GET',
        data: {
            status: status,
            table_id: table,
            search: search
        },
        success: function(response) {
            try {
                const data = JSON.parse(response);
                if (data.success) {
                    displayOrders(data.orders);
                    updateSummary(data.summary);
                } else {
                    showAlert('Error loading orders: ' + data.message, 'danger');
                }
            } catch (e) {
                showAlert('Error parsing response', 'danger');
            }
        },
        error: function() {
            showAlert('Error connecting to server', 'danger');
        }
    });
}

function displayOrders(orders) {
    const tbody = $('#orders-tbody');
    tbody.empty();
    
    if (orders.length === 0) {
        tbody.html('<tr><td colspan="10" class="text-center">No orders found</td></tr>');
        return;
    }
    
    orders.forEach(order => {
        const statusBadge = getStatusBadge(order.status);
        const createdDate = new Date(order.created_at).toLocaleString();
        
        const row = `
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${order.id}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                        Table ${order.table_id}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        ${order.product_img && order.product_img !== '-' ? 
                            `<img src="uploads/products/${order.product_img}" alt="${order.product_name}" class="h-8 w-8 rounded-full mr-3">` :
                            `<div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center mr-3">
                                <i class="fas fa-box text-gray-600 text-sm"></i>
                            </div>`
                        }
                        <div class="text-sm font-medium text-gray-900">${order.product_name}</div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${order.sku}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                        ${order.quantity}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">RM ${parseFloat(order.price).toFixed(2)}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">RM ${parseFloat(order.subtotal).toFixed(2)}</td>
                <td class="px-6 py-4 whitespace-nowrap">${statusBadge}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${createdDate}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex items-center space-x-2">
                        <button onclick="viewOrderDetails(${order.id})" class="text-blue-600 hover:text-blue-900 p-1 rounded" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${order.status === 'active' ? 
                            `<button onclick="markAsOrdered(${order.id})" class="text-green-600 hover:text-green-900 p-1 rounded" title="Mark as Ordered">
                                <i class="fas fa-check"></i>
                            </button>` : ''
                        }
                        ${order.status === 'ordered' ? 
                            `<button onclick="markAsPaid(${order.id})" class="text-indigo-600 hover:text-indigo-900 p-1 rounded" title="Mark as Paid">
                                <i class="fas fa-dollar-sign"></i>
                            </button>` : ''
                        }
                        <button onclick="deleteOrder(${order.id})" class="text-red-600 hover:text-red-900 p-1 rounded" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        
        tbody.append(row);
    });
}

function getStatusBadge(status) {
    const badges = {
        'active': '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Active</span>',
        'ordered': '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Ordered</span>',
        'paid': '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">Paid</span>',
        'abandoned': '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">Abandoned</span>'
    };
    
    return badges[status] || '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Unknown</span>';
}

function updateSummary(summary) {
    $('#active-count').text(summary.active || 0);
    $('#ordered-count').text(summary.ordered || 0);
    $('#paid-count').text(summary.paid || 0);
    $('#abandoned-count').text(summary.abandoned || 0);
}

function viewOrderDetails(orderId) {
    $.ajax({
        url: 'ajax/get-order-details.php',
        type: 'GET',
        data: { order_number: orderId },
        success: function(response) {
            try {
                const data = JSON.parse(response);
                if (data.success) {
                    $('#orderDetailsBody').html(data.html);
                    $('#orderDetailsModal').removeClass('hidden').addClass('flex');
                } else {
                    showAlert('Error loading order details: ' + data.message, 'danger');
                }
            } catch (e) {
                showAlert('Error parsing response', 'danger');
            }
        },
        error: function() {
            showAlert('Error connecting to server', 'danger');
        }
    });
}

function closeOrderModal() {
    $('#orderDetailsModal').removeClass('flex').addClass('hidden');
}

function markAsOrdered(orderId) {
    updateOrderStatus(orderId, 'ordered');
}

function markAsPaid(orderId) {
    updateOrderStatus(orderId, 'paid');
}

function updateOrderStatus(orderId, status) {
    if (!confirm(`Are you sure you want to mark this order as ${status}?`)) {
        return;
    }
    
    $.ajax({
        url: 'ajax/update-order-status.php',
        type: 'POST',
        data: {
            order_number: orderId,
            status: status
        },
        success: function(response) {
            try {
                const data = JSON.parse(response);
                if (data.success) {
                    showAlert(`Order marked as ${status} successfully`, 'success');
                    loadCustomerOrders();
                } else {
                    showAlert('Error updating order: ' + data.message, 'danger');
                }
            } catch (e) {
                showAlert('Error parsing response', 'danger');
            }
        },
        error: function() {
            showAlert('Error connecting to server', 'danger');
        }
    });
}

function deleteOrder(orderId) {
    if (!confirm('Are you sure you want to delete this order? This action cannot be undone.')) {
        return;
    }
    
    $.ajax({
        url: 'ajax/delete-customer-order.php',
        type: 'POST',
        data: { order_number: orderId },
        success: function(response) {
            try {
                const data = JSON.parse(response);
                if (data.success) {
                    showAlert('Order deleted successfully', 'success');
                    loadCustomerOrders();
                } else {
                    showAlert('Error deleting order: ' + data.message, 'danger');
                }
            } catch (e) {
                showAlert('Error parsing response', 'danger');
            }
        },
        error: function() {
            showAlert('Error connecting to server', 'danger');
        }
    });
}

function refreshOrders() {
    loadCustomerOrders();
    showAlert('Orders refreshed', 'info');
}

function showAlert(message, type) {
    const alertIcons = {
        'success': 'fas fa-check-circle',
        'danger': 'fas fa-exclamation-circle',
        'warning': 'fas fa-exclamation-triangle',
        'info': 'fas fa-info-circle'
    };
    
    const alertHtml = `
        <div class="fixed top-4 right-4 z-50 max-w-sm w-full">
            <div class="bg-white border-l-4 border-${type === 'success' ? 'green' : type === 'danger' ? 'red' : type === 'warning' ? 'yellow' : 'blue'}-400 p-4 rounded-lg shadow-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="${alertIcons[type]} text-${type === 'success' ? 'green' : type === 'danger' ? 'red' : type === 'warning' ? 'yellow' : 'blue'}-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">${message}</p>
                    </div>
                    <div class="ml-auto pl-3">
                        <div class="-mx-1.5 -my-1.5">
                            <button onclick="closeAlert(this)" class="inline-flex rounded-md p-1.5 text-gray-400 hover:text-gray-500">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing alerts
    $('.fixed.top-4.right-4').remove();
    
    // Add new alert
    $('body').append(alertHtml);
    
    // Auto dismiss after 4 seconds
    setTimeout(function() {
        $('.fixed.top-4.right-4').fadeOut(300, function() {
            $(this).remove();
        });
    }, 4000);
}

function closeAlert(button) {
    $(button).closest('.fixed.top-4.right-4').fadeOut(300, function() {
        $(this).remove();
    });
}
</script>

    </div>
</div>

<script>
// Update last updated time
function updateLastUpdated() {
    const now = new Date();
    const timeString = now.toLocaleTimeString();
    document.getElementById('last-updated').textContent = timeString;
}

// Update time every minute
setInterval(updateLastUpdated, 60000);
</script>

</body>
</html>
