<?php
include '../include/conn.php';

// Get filter parameters
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';
$tier_filter = isset($_POST['tier_filter']) ? $_POST['tier_filter'] : '';
$status_filter = isset($_POST['status_filter']) ? $_POST['status_filter'] : '';

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

if ($result) {
    while ($row = mysqli_fetch_array($result)) {
        $status_class = $row['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
        $status_text = $row['is_active'] ? 'Active' : 'Inactive';
        
        echo "<tr class='border-b border-gray-100 hover:bg-gray-50/50 transition-colors duration-200'>";
        echo "<td class='py-3 px-4 font-medium text-gray-900'>" . htmlspecialchars($row['member_code']) . "</td>";
        echo "<td class='py-3 px-4'>";
        echo "<div class='flex items-center space-x-3'>";
        echo "<div class='w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-600 rounded-full flex items-center justify-center text-white text-sm font-bold'>";
        echo strtoupper(substr($row['name'], 0, 1));
        echo "</div>";
        echo "<div>";
        echo "<p class='font-medium text-gray-900'>" . htmlspecialchars($row['name']) . "</p>";
        if (!empty($row['address'])) {
            echo "<p class='text-xs text-gray-500'>" . htmlspecialchars(substr($row['address'], 0, 50)) . (strlen($row['address']) > 50 ? '...' : '') . "</p>";
        }
        echo "</div>";
        echo "</div>";
        echo "</td>";
        echo "<td class='py-3 px-4 text-gray-600'>" . htmlspecialchars($row['phone']) . "</td>";
        echo "<td class='py-3 px-4 text-gray-600'>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td class='py-3 px-4 text-center'>";
        echo "<span class='inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ";
        switch($row['tier_name']) {
            case 'Bronze': echo 'bg-yellow-100 text-yellow-800'; break;
            case 'Silver': echo 'bg-gray-100 text-gray-800'; break;
            case 'Gold': echo 'bg-yellow-100 text-yellow-800'; break;
            case 'Platinum': echo 'bg-purple-100 text-purple-800'; break;
            default: echo 'bg-gray-100 text-gray-800';
        }
        echo "'>" . htmlspecialchars($row['tier_name']) . "</span>";
        echo "</td>";
        echo "<td class='py-3 px-4 text-center'>";
        echo "<span class='inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium $status_class'>$status_text</span>";
        echo "</td>";
        echo "<td class='py-3 px-4 text-center font-medium text-green-600'>RM " . number_format($row['total_spent'], 2) . "</td>";
        echo "<td class='py-3 px-4 text-center text-gray-600'>" . number_format($row['total_points']) . "</td>";
        echo "<td class='py-3 px-4 text-center text-gray-600'>" . date('M j, Y', strtotime($row['created_at'])) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='9' class='py-4 px-4 text-center text-gray-500'>No members found matching the criteria.</td></tr>";
}
?>

