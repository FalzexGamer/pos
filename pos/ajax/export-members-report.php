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

// Set headers for CSV download
$filename = 'member_report_' . date('Y-m-d_H-i-s') . '.csv';
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

// Create output stream
$output = fopen('php://output', 'w');

// Add UTF-8 BOM for proper encoding
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Write CSV headers
$headers = [
    'Member Code',
    'Name',
    'Phone',
    'Email',
    'Address',
    'Membership Tier',
    'Status',
    'Total Spent (RM)',
    'Total Points',
    'Date Joined',
    'Last Updated'
];
fputcsv($output, $headers);

// Write data rows
if ($result) {
    while ($row = mysqli_fetch_array($result)) {
        $status = $row['is_active'] ? 'Active' : 'Inactive';
        
        $data = [
            $row['member_code'],
            $row['name'],
            $row['phone'],
            $row['email'],
            $row['address'],
            $row['tier_name'],
            $status,
            number_format($row['total_spent'], 2),
            number_format($row['total_points']),
            date('Y-m-d H:i:s', strtotime($row['created_at'])),
            date('Y-m-d H:i:s', strtotime($row['updated_at']))
        ];
        
        fputcsv($output, $data);
    }
}

fclose($output);
exit;
?>

