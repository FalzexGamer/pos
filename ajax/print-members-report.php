<?php
include '../include/conn.php';

// Get filter parameters
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$tier_filter = isset($_GET['tier_filter']) ? $_GET['tier_filter'] : '';
$status_filter = isset($_GET['status_filter']) ? $_GET['status_filter'] : '';

// Build the WHERE clause
$where_conditions = [];
$params = [];

if (!empty($start_date) && !empty($end_date)) {
    $where_conditions[] = "DATE(m.created_at) BETWEEN ? AND ?";
    $params[] = $start_date;
    $params[] = $end_date;
}

if (!empty($tier_filter)) {
    $where_conditions[] = "mt.name = ?";
    $params[] = $tier_filter;
}

if (!empty($status_filter)) {
    if ($status_filter === 'active') {
        $where_conditions[] = "m.is_active = 1";
    } elseif ($status_filter === 'inactive') {
        $where_conditions[] = "m.is_active = 0";
    }
}

$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

// Prepare the query
$query = "SELECT 
    m.member_code,
    m.name,
    m.phone,
    m.email,
    m.address,
    mt.name as tier_name,
    m.is_active,
    m.total_spent,
    m.total_points,
    m.created_at,
    m.updated_at
FROM members m
LEFT JOIN membership_tiers mt ON m.membership_tier_id = mt.id
$where_clause
ORDER BY m.created_at DESC";

// Execute the query
if (!empty($params)) {
    $stmt = mysqli_prepare($conn, $query);
    if ($stmt) {
        $types = str_repeat('s', count($params));
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    } else {
        $result = false;
    }
} else {
    $result = mysqli_query($conn, $query);
}

// Get total count
$count_query = "SELECT COUNT(*) as total FROM members m LEFT JOIN membership_tiers mt ON m.membership_tier_id = mt.id $where_clause";
if (!empty($params)) {
    $count_stmt = mysqli_prepare($conn, $count_query);
    if ($count_stmt) {
        $types = str_repeat('s', count($params));
        mysqli_stmt_bind_param($count_stmt, $types, ...$params);
        mysqli_stmt_execute($count_stmt);
        $count_result = mysqli_stmt_get_result($count_stmt);
        $total_count = mysqli_fetch_array($count_result)['total'];
    } else {
        $total_count = 0;
    }
} else {
    $count_result = mysqli_query($conn, $count_query);
    $total_count = mysqli_fetch_array($count_result)['total'];
}

// Get summary statistics
$summary_query = "SELECT 
    COUNT(*) as total_members,
    SUM(CASE WHEN m.is_active = 1 THEN 1 ELSE 0 END) as active_members,
    SUM(CASE WHEN m.is_active = 0 THEN 1 ELSE 0 END) as inactive_members,
    AVG(m.total_spent) as avg_spent,
    SUM(m.total_spent) as total_spent,
    AVG(m.total_points) as avg_points,
    SUM(m.total_points) as total_points
FROM members m
LEFT JOIN membership_tiers mt ON m.membership_tier_id = mt.id
$where_clause";

if (!empty($params)) {
    $summary_stmt = mysqli_prepare($conn, $summary_query);
    if ($summary_stmt) {
        $types = str_repeat('s', count($params));
        mysqli_stmt_bind_param($summary_stmt, $types, ...$params);
        mysqli_stmt_execute($summary_stmt);
        $summary_result = mysqli_stmt_get_result($summary_stmt);
        $summary = mysqli_fetch_array($summary_result);
    } else {
        $summary = ['total_members' => 0, 'active_members' => 0, 'inactive_members' => 0, 'avg_spent' => 0, 'total_spent' => 0, 'avg_points' => 0, 'total_points' => 0];
    }
} else {
    $summary_result = mysqli_query($conn, $summary_query);
    $summary = mysqli_fetch_array($summary_result);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Report - Print</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                margin: 0;
                padding: 20px;
            }
            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>
<body class="bg-white text-gray-900">
    <!-- Print Header -->
    <div class="no-print mb-6">
        <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            <i class="fas fa-print mr-2"></i>Print Report
        </button>
        <button onclick="window.close()" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 ml-2">
            <i class="fas fa-times mr-2"></i>Close
        </button>
    </div>

    <!-- Report Header -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Member Report</h1>
        <p class="text-gray-600">Generated on <?php echo date('F j, Y \a\t g:i A'); ?></p>
        <?php if (!empty($start_date) && !empty($end_date)): ?>
            <p class="text-gray-600">Period: <?php echo date('F j, Y', strtotime($start_date)); ?> to <?php echo date('F j, Y', strtotime($end_date)); ?></p>
        <?php endif; ?>
        <?php if (!empty($tier_filter)): ?>
            <p class="text-gray-600">Tier Filter: <?php echo htmlspecialchars($tier_filter); ?></p>
        <?php endif; ?>
        <?php if (!empty($status_filter)): ?>
            <p class="text-gray-600">Status Filter: <?php echo ucfirst(htmlspecialchars($status_filter)); ?></p>
        <?php endif; ?>
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-blue-50 p-4 rounded-lg border">
            <h3 class="text-sm font-semibold text-blue-800">Total Members</h3>
            <p class="text-2xl font-bold text-blue-900"><?php echo number_format($summary['total_members']); ?></p>
        </div>
        <div class="bg-green-50 p-4 rounded-lg border">
            <h3 class="text-sm font-semibold text-green-800">Active Members</h3>
            <p class="text-2xl font-bold text-green-900"><?php echo number_format($summary['active_members']); ?></p>
        </div>
        <div class="bg-purple-50 p-4 rounded-lg border">
            <h3 class="text-sm font-semibold text-purple-800">Total Spent</h3>
            <p class="text-2xl font-bold text-purple-900">RM <?php echo number_format($summary['total_spent'], 2); ?></p>
        </div>
        <div class="bg-orange-50 p-4 rounded-lg border">
            <h3 class="text-sm font-semibold text-orange-800">Avg. Spent</h3>
            <p class="text-2xl font-bold text-orange-900">RM <?php echo number_format($summary['avg_spent'], 2); ?></p>
        </div>
    </div>

    <!-- Member Details Table -->
    <div class="mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Member Details (<?php echo number_format($total_count); ?> records)</h2>
        
        <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 px-3 py-2 text-left text-sm font-semibold">Member Code</th>
                        <th class="border border-gray-300 px-3 py-2 text-left text-sm font-semibold">Name</th>
                        <th class="border border-gray-300 px-3 py-2 text-left text-sm font-semibold">Phone</th>
                        <th class="border border-gray-300 px-3 py-2 text-left text-sm font-semibold">Email</th>
                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-semibold">Tier</th>
                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-semibold">Status</th>
                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-semibold">Total Spent</th>
                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-semibold">Points</th>
                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-semibold">Joined</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_array($result)): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="border border-gray-300 px-3 py-2 text-sm"><?php echo htmlspecialchars($row['member_code']); ?></td>
                                <td class="border border-gray-300 px-3 py-2 text-sm font-medium"><?php echo htmlspecialchars($row['name']); ?></td>
                                <td class="border border-gray-300 px-3 py-2 text-sm"><?php echo htmlspecialchars($row['phone']); ?></td>
                                <td class="border border-gray-300 px-3 py-2 text-sm"><?php echo htmlspecialchars($row['email']); ?></td>
                                <td class="border border-gray-300 px-3 py-2 text-sm text-center"><?php echo htmlspecialchars($row['tier_name']); ?></td>
                                <td class="border border-gray-300 px-3 py-2 text-sm text-center">
                                    <span class="<?php echo $row['is_active'] ? 'text-green-600' : 'text-red-600'; ?> font-medium">
                                        <?php echo $row['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td class="border border-gray-300 px-3 py-2 text-sm text-center font-medium">RM <?php echo number_format($row['total_spent'], 2); ?></td>
                                <td class="border border-gray-300 px-3 py-2 text-sm text-center"><?php echo number_format($row['total_points']); ?></td>
                                <td class="border border-gray-300 px-3 py-2 text-sm text-center"><?php echo date('M j, Y', strtotime($row['created_at'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="border border-gray-300 px-3 py-4 text-center text-gray-500">No members found matching the criteria.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Footer -->
    <div class="mt-8 pt-4 border-t border-gray-300 text-center text-sm text-gray-600">
        <p>This report was generated automatically by the POS System</p>
        <p>Page 1 of 1</p>
    </div>

    <script>
        // Auto-print when page loads
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>

