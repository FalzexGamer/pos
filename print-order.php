<?php
include 'include/conn.php';

// Get order number from URL
$order_number = isset($_GET['order_number']) ? $_GET['order_number'] : '';

if (empty($order_number)) {
    die('Order number is required');
}

// Get company settings
$company_query = mysqli_query($conn, "SELECT * FROM company_settings WHERE id = 1");
$company = mysqli_fetch_array($company_query);

// Get order details
$order_query = "SELECT 
                cc.order_number,
                cc.table_id,
                COUNT(*) as total_items,
                SUM(cc.subtotal) as subtotal,
                SUM(cc.subtotal) * 0.06 as tax_amount,
                SUM(cc.subtotal) * 1.06 as total_amount,
                MIN(cc.created_at) as created_at,
                MAX(cc.updated_at) as last_updated_at,
                CASE 
                    WHEN COUNT(DISTINCT cc.status) = 1 AND MAX(cc.status) = 'paid' THEN 'paid'
                    WHEN COUNT(DISTINCT cc.status) = 1 AND MAX(cc.status) = 'cancelled' THEN 'cancelled'
                    WHEN COUNT(DISTINCT cc.status) = 1 AND MAX(cc.status) = 'abandoned' THEN 'abandoned'
                    WHEN MAX(cc.status) = 'served' THEN 'served'
                    WHEN MAX(cc.status) = 'ready' THEN 'ready'
                    WHEN MAX(cc.status) = 'preparing' THEN 'preparing'
                    WHEN MAX(cc.status) = 'ordered' THEN 'confirmed'
                    ELSE 'pending'
                END as status
              FROM customer_cart cc 
              WHERE cc.order_number = '" . mysqli_real_escape_string($conn, $order_number) . "'
              GROUP BY cc.order_number, cc.table_id";

$order_result = mysqli_query($conn, $order_query);

if (!$order_result || mysqli_num_rows($order_result) === 0) {
    die('Order not found');
}

$order = mysqli_fetch_assoc($order_result);

// Get order items
$items_query = "SELECT cc.*, p.name as product_name, c.name as category_name
                FROM customer_cart cc 
                LEFT JOIN products p ON cc.product_id = p.id 
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE cc.order_number = '" . mysqli_real_escape_string($conn, $order_number) . "'
                ORDER BY cc.created_at";

$items_result = mysqli_query($conn, $items_query);
$items = [];
while ($item = mysqli_fetch_assoc($items_result)) {
    $items[] = $item;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Receipt - <?= htmlspecialchars($order_number) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            background: white;
        }
        .receipt {
            max-width: 300px;
            margin: 0 auto;
            border: 1px solid #ccc;
            padding: 15px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .company-info {
            font-size: 10px;
            color: #666;
        }
        .order-info {
            margin-bottom: 15px;
        }
        .order-info div {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        .items {
            border-top: 1px solid #ccc;
            border-bottom: 1px solid #ccc;
            padding: 10px 0;
            margin: 15px 0;
        }
        .item {
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 1px dotted #ccc;
        }
        .item:last-child {
            border-bottom: none;
        }
        .item-name {
            font-weight: bold;
        }
        .item-details {
            font-size: 10px;
            color: #666;
            margin-top: 2px;
        }
        .item-price {
            text-align: right;
            margin-top: 2px;
        }
        .totals {
            margin-top: 15px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        .total-final {
            border-top: 2px solid #000;
            padding-top: 5px;
            font-weight: bold;
            font-size: 14px;
        }
        .status {
            text-align: center;
            margin: 15px 0;
            padding: 10px;
            background: #f0f0f0;
            border-radius: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
            color: #666;
        }
        @media print {
            body { margin: 0; padding: 10px; }
            .receipt { border: none; max-width: none; }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <!-- Header -->
        <div class="header">
            <div class="company-name"><?= htmlspecialchars($company['company_name']) ?></div>
            <?php if ($company['address']): ?>
            <div class="company-info"><?= htmlspecialchars($company['address']) ?></div>
            <?php endif; ?>
            <?php if ($company['phone']): ?>
            <div class="company-info">Tel: <?= htmlspecialchars($company['phone']) ?></div>
            <?php endif; ?>
        </div>

        <!-- Order Information -->
        <div class="order-info">
            <div>
                <span>Order #:</span>
                <span><strong><?= htmlspecialchars($order_number) ?></strong></span>
            </div>
            <div>
                <span>Table:</span>
                <span><strong><?= $order['table_id'] ?></strong></span>
            </div>
            <div>
                <span>Date:</span>
                <span><?= date('M d, Y', strtotime($order['created_at'])) ?></span>
            </div>
            <div>
                <span>Time:</span>
                <span><?= date('H:i:s', strtotime($order['created_at'])) ?></span>
            </div>
            <div>
                <span>Status:</span>
                <span><strong><?= ucfirst($order['status']) ?></strong></span>
            </div>
        </div>

        <!-- Order Items -->
        <div class="items">
            <?php foreach ($items as $item): ?>
            <div class="item">
                <div class="item-name"><?= htmlspecialchars($item['product_name']) ?></div>
                <div class="item-details">
                    SKU: <?= htmlspecialchars($item['sku']) ?> | 
                    Qty: <?= $item['quantity'] ?> | 
                    Unit: RM <?= number_format($item['price'], 2) ?>
                </div>
                <div class="item-price">
                    <strong>RM <?= number_format($item['subtotal'], 2) ?></strong>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Totals -->
        <div class="totals">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>RM <?= number_format($order['subtotal'], 2) ?></span>
            </div>
            <div class="total-row">
                <span>Tax (6%):</span>
                <span>RM <?= number_format($order['tax_amount'], 2) ?></span>
            </div>
            <div class="total-row total-final">
                <span>TOTAL:</span>
                <span>RM <?= number_format($order['total_amount'], 2) ?></span>
            </div>
        </div>

        <!-- Status -->
        <div class="status">
            <strong>Order Status: <?= ucfirst($order['status']) ?></strong>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for your order!</p>
            <p>Generated on: <?= date('M d, Y H:i:s') ?></p>
        </div>
    </div>

    <script>
        // Auto-print when page loads
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
