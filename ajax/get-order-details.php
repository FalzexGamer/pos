<?php
include '../include/conn.php';

// Get order number (order_id) from GET parameter
$order_number = isset($_GET['order_number']) ? $_GET['order_number'] : '';

if (empty($order_number)) {
    echo json_encode(['success' => false, 'message' => 'Invalid order number']);
    exit;
}

try {
    // Get all items for this order
    $query = "SELECT cc.*, p.name as product_name, p.description, p.img as product_img, p.stock_quantity,
                     c.name as category_name
              FROM customer_cart cc 
              LEFT JOIN products p ON cc.product_id = p.id 
              LEFT JOIN categories c ON p.category_id = c.id
              WHERE cc.order_id = '" . mysqli_real_escape_string($conn, $order_number) . "'
              ORDER BY cc.created_at ASC";
    
    $result = mysqli_query($conn, $query);
    
    if (!$result || mysqli_num_rows($result) === 0) {
        throw new Exception("Order not found");
    }
    
    $order_items = [];
    $total_subtotal = 0;
    $table_id = 0;
    $order_statuses = [];
    $created_at = '';
    $updated_at = '';
    
    while ($row = mysqli_fetch_assoc($result)) {
        $order_items[] = $row;
        $total_subtotal += $row['subtotal'];
        $table_id = $row['table_id'];
        $order_statuses[] = $row['status'];
        if (empty($created_at)) $created_at = $row['created_at'];
        $updated_at = $row['updated_at'];
    }
    
    // Calculate tax and total
    $tax_amount = $total_subtotal * 0.06;
    $total_amount = $total_subtotal + $tax_amount;
    
    // Determine overall order status
    $overall_status = 'active';
    if (in_array('paid', $order_statuses)) {
        $overall_status = 'paid';
    } elseif (in_array('abandoned', $order_statuses)) {
        $overall_status = 'abandoned';
    } elseif (in_array('ordered', $order_statuses)) {
        $overall_status = 'ordered';
    }
    
    // Generate HTML for order details
    $html = '
    <div class="row">
        <div class="col-md-6">
            <h6 class="text-muted">Order Information</h6>
            <table class="table table-sm">
                <tr>
                    <td><strong>Order ID:</strong></td>
                    <td>' . htmlspecialchars($order_number) . '</td>
                </tr>
                <tr>
                    <td><strong>Table:</strong></td>
                    <td><span class="badge bg-info">Table ' . $table_id . '</span></td>
                </tr>
                <tr>
                    <td><strong>Status:</strong></td>
                    <td>' . getStatusBadge($overall_status) . '</td>
                </tr>
                <tr>
                    <td><strong>Total Items:</strong></td>
                    <td><span class="badge bg-primary">' . count($order_items) . '</span></td>
                </tr>
                <tr>
                    <td><strong>Created:</strong></td>
                    <td>' . date('M d, Y H:i:s', strtotime($created_at)) . '</td>
                </tr>
                <tr>
                    <td><strong>Updated:</strong></td>
                    <td>' . date('M d, Y H:i:s', strtotime($updated_at)) . '</td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <h6 class="text-muted">Order Summary</h6>
            <table class="table table-sm">
                <tr>
                    <td><strong>Subtotal:</strong></td>
                    <td><strong>RM ' . number_format($total_subtotal, 2) . '</strong></td>
                </tr>
                <tr>
                    <td><strong>Tax (6%):</strong></td>
                    <td>RM ' . number_format($tax_amount, 2) . '</td>
                </tr>
                <tr class="table-success">
                    <td><strong>Total:</strong></td>
                    <td><strong>RM ' . number_format($total_amount, 2) . '</strong></td>
                </tr>
            </table>
        </div>
    </div>
    
    <hr>
    
    <div class="row">
        <div class="col-12">
            <h6 class="text-muted">Order Items</h6>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>';
    
    foreach ($order_items as $item) {
        $html .= '
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    ' . ($item['product_img'] && $item['product_img'] !== '-' ? 
                                        '<img src="uploads/products/' . htmlspecialchars($item['product_img']) . '" alt="' . htmlspecialchars($item['product_name']) . '" class="me-2" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">' :
                                        '<div class="bg-secondary rounded d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                                            <i class="fas fa-image text-white"></i>
                                        </div>'
                                    ) . '
                                    <div>
                                        <strong>' . htmlspecialchars($item['product_name']) . '</strong>
                                        ' . ($item['description'] ? '<br><small class="text-muted">' . htmlspecialchars($item['description']) . '</small>' : '') . '
                                    </div>
                                </div>
                            </td>
                            <td>' . htmlspecialchars($item['sku']) . '</td>
                            <td>' . htmlspecialchars($item['category_name']) . '</td>
                            <td><span class="badge bg-primary">' . $item['quantity'] . '</span></td>
                            <td>RM ' . number_format($item['price'], 2) . '</td>
                            <td><strong>RM ' . number_format($item['subtotal'], 2) . '</strong></td>
                            <td>' . getStatusBadge($item['status']) . '</td>
                        </tr>';
    }
    
    $html .= '
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    ';
    
    echo json_encode([
        'success' => true,
        'html' => $html
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

function getStatusBadge($status) {
    $badges = [
        'active' => '<span class="badge bg-primary">Active</span>',
        'ordered' => '<span class="badge bg-success">Ordered</span>',
        'preparing' => '<span class="badge bg-info">Preparing</span>',
        'ready' => '<span class="badge bg-warning">Ready</span>',
        'served' => '<span class="badge bg-secondary">Served</span>',
        'paid' => '<span class="badge bg-dark">Paid</span>',
        'cancelled' => '<span class="badge bg-danger">Cancelled</span>',
        'abandoned' => '<span class="badge bg-warning">Abandoned</span>'
    ];
    
    return $badges[$status] ?? '<span class="badge bg-secondary">Unknown</span>';
}

mysqli_close($conn);
?>
