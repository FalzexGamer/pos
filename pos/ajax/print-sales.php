<?php
include '../include/conn.php';
include '../include/session.php';

// Get filter parameters
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Build the query
$query = "SELECT s.*, 
                 m.name as member_name,
                 m.email as member_email,
                 u.full_name as cashier_name,
                 COUNT(si.id) as item_count
          FROM sales s
          LEFT JOIN members m ON s.member_id = m.id
          LEFT JOIN users u ON s.user_id = u.id
          LEFT JOIN sale_items si ON s.id = si.sale_id
          WHERE 1=1";

$params = [];
$types = '';

// Add search filter
if (!empty($search)) {
    $query .= " AND (s.invoice_number LIKE ? OR m.name LIKE ? OR m.email LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'sss';
}

// Add date filters
if (!empty($start_date)) {
    $query .= " AND DATE(s.created_at) >= ?";
    $params[] = $start_date;
    $types .= 's';
}

if (!empty($end_date)) {
    $query .= " AND DATE(s.created_at) <= ?";
    $params[] = $end_date;
    $types .= 's';
}

// Add status filter
if (!empty($status)) {
    $query .= " AND s.payment_status = ?";
    $params[] = $status;
    $types .= 's';
}

$query .= " GROUP BY s.id ORDER BY s.created_at DESC";

// Prepare and execute the query
$stmt = mysqli_prepare($conn, $query);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Get company info
$query_company = mysqli_query($conn, "SELECT * FROM company_settings LIMIT 1");
$company = mysqli_fetch_array($query_company);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report - <?php echo $company['company_name'] ?? 'POS System'; ?></title>
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none !important; }
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: white;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .report-title {
            font-size: 18px;
            color: #666;
            margin-bottom: 10px;
        }
        
        .report-date {
            font-size: 14px;
            color: #999;
        }
        
        .filters {
            margin-bottom: 20px;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        
        .filters h3 {
            margin: 0 0 10px 0;
            font-size: 16px;
            color: #333;
        }
        
        .filter-item {
            display: inline-block;
            margin-right: 20px;
            font-size: 14px;
        }
        
        .filter-label {
            font-weight: bold;
            color: #666;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px 12px;
            text-align: left;
            font-size: 12px;
        }
        
        th {
            background-color: #f5f5f5;
            font-weight: bold;
            color: #333;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .status-paid {
            color: #28a745;
            font-weight: bold;
        }
        
        .status-pending {
            color: #ffc107;
            font-weight: bold;
        }
        
        .status-refunded {
            color: #dc3545;
            font-weight: bold;
        }
        
        .summary {
            margin-top: 30px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        
        .summary h3 {
            margin: 0 0 15px 0;
            font-size: 16px;
            color: #333;
        }
        
        .summary-item {
            display: inline-block;
            margin-right: 30px;
            font-size: 14px;
        }
        
        .summary-value {
            font-weight: bold;
            color: #333;
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .print-button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-button no-print">Print Report</button>
    
    <div class="header">
        <div class="company-name"><?php echo htmlspecialchars($company['company_name'] ?? 'POS System'); ?></div>
        <div class="report-title">Sales Report</div>
        <div class="report-date">Generated on: <?php echo date('F j, Y \a\t g:i A'); ?></div>
    </div>
    
    <div class="filters">
        <h3>Filters Applied:</h3>
        <?php if (!empty($search)): ?>
            <div class="filter-item">
                <span class="filter-label">Search:</span> <?php echo htmlspecialchars($search); ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($start_date)): ?>
            <div class="filter-item">
                <span class="filter-label">From:</span> <?php echo date('M j, Y', strtotime($start_date)); ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($end_date)): ?>
            <div class="filter-item">
                <span class="filter-label">To:</span> <?php echo date('M j, Y', strtotime($end_date)); ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($status)): ?>
            <div class="filter-item">
                <span class="filter-label">Status:</span> <?php echo ucfirst($status); ?>
            </div>
        <?php endif; ?>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Invoice</th>
                <th>Customer</th>
                <th>Items</th>
                <th>Subtotal</th>
                <th>Discount</th>
                <th>Tax</th>
                <th>Total</th>
                <th>Payment</th>
                <th>Status</th>
                <th>Date</th>
                <th>Cashier</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total_sales = 0;
            $total_revenue = 0;
            $total_items = 0;
            
            while ($sale = mysqli_fetch_assoc($result)): 
                $total_sales++;
                $total_revenue += $sale['total_amount'];
                $total_items += $sale['item_count'];
                
                $status_class = 'status-' . $sale['payment_status'];
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($sale['invoice_number']); ?></td>
                    <td><?php echo htmlspecialchars($sale['member_name'] ?: 'Walk-in Customer'); ?></td>
                    <td><?php echo $sale['item_count']; ?></td>
                    <td>RM <?php echo number_format($sale['subtotal'], 2); ?></td>
                    <td>RM <?php echo number_format($sale['discount_amount'], 2); ?></td>
                    <td>RM <?php echo number_format($sale['tax_amount'], 2); ?></td>
                    <td>RM <?php echo number_format($sale['total_amount'], 2); ?></td>
                    <td><?php echo ucfirst($sale['payment_method']); ?></td>
                    <td class="<?php echo $status_class; ?>"><?php echo ucfirst($sale['payment_status']); ?></td>
                    <td><?php echo date('M j, Y g:i A', strtotime($sale['created_at'])); ?></td>
                    <td><?php echo htmlspecialchars($sale['cashier_name'] ?: 'Unknown'); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    
    <div class="summary">
        <h3>Summary</h3>
        <div class="summary-item">
            <span class="filter-label">Total Sales:</span> 
            <span class="summary-value"><?php echo $total_sales; ?></span>
        </div>
        <div class="summary-item">
            <span class="filter-label">Total Revenue:</span> 
            <span class="summary-value">RM <?php echo number_format($total_revenue, 2); ?></span>
        </div>
        <div class="summary-item">
            <span class="filter-label">Total Items Sold:</span> 
            <span class="summary-value"><?php echo $total_items; ?></span>
        </div>
        <div class="summary-item">
            <span class="filter-label">Average Sale Value:</span> 
            <span class="summary-value">RM <?php echo $total_sales > 0 ? number_format($total_revenue / $total_sales, 2) : '0.00'; ?></span>
        </div>
    </div>
</body>
</html>
