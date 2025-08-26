<?php
include '../include/conn.php';
include '../include/session.php';

$sql = "SELECT sts.*, u.full_name as created_by_name, 
        COUNT(sti.id) as items_counted,
        SUM(CASE WHEN sti.difference != 0 THEN 1 ELSE 0 END) as items_with_differences
        FROM stock_take_sessions sts 
        LEFT JOIN users u ON sts.created_by = u.id
        LEFT JOIN stock_take_items sti ON sts.id = sti.session_id
        GROUP BY sts.id 
        ORDER BY sts.start_date DESC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    echo '<tr><td colspan="8" class="px-6 py-4 text-center text-red-500">Error loading sessions</td></tr>';
    exit;
}

$output = '';

while ($session = mysqli_fetch_assoc($result)) {
    $status_class = '';
    $status_text = '';
    
    switch ($session['status']) {
        case 'in_progress':
            $status_class = 'bg-yellow-100 text-yellow-800';
            $status_text = 'In Progress';
            break;
        case 'completed':
            $status_class = 'bg-green-100 text-green-800';
            $status_text = 'Completed';
            break;
        case 'cancelled':
            $status_class = 'bg-red-100 text-red-800';
            $status_text = 'Cancelled';
            break;
    }
    
    $output .= '<tr class="hover:bg-gray-50">';
    $output .= '<td class="px-6 py-4 whitespace-nowrap">';
    $output .= '<div class="text-sm font-medium text-gray-900">' . htmlspecialchars($session['session_name']) . '</div>';
    $output .= '</td>';
    $output .= '<td class="px-6 py-4 whitespace-nowrap">';
    $output .= '<div class="text-sm text-gray-900">' . date('M d, Y H:i', strtotime($session['start_date'])) . '</div>';
    $output .= '</td>';
    $output .= '<td class="px-6 py-4 whitespace-nowrap">';
    $output .= '<div class="text-sm text-gray-900">' . ($session['end_date'] ? date('M d, Y H:i', strtotime($session['end_date'])) : '-') . '</div>';
    $output .= '</td>';
    $output .= '<td class="px-6 py-4 whitespace-nowrap">';
    $output .= '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ' . $status_class . '">' . $status_text . '</span>';
    $output .= '</td>';
    $output .= '<td class="px-6 py-4 whitespace-nowrap">';
    $output .= '<div class="text-sm text-gray-900">' . $session['items_counted'] . ' items</div>';
    $output .= '</td>';
    $output .= '<td class="px-6 py-4 whitespace-nowrap">';
    if ($session['items_with_differences'] > 0) {
        $output .= '<span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">' . $session['items_with_differences'] . ' differences</span>';
    } else {
        $output .= '<span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">No differences</span>';
    }
    $output .= '</td>';
    $output .= '<td class="px-6 py-4 whitespace-nowrap">';
    $output .= '<div class="text-sm text-gray-900">' . htmlspecialchars($session['created_by_name']) . '</div>';
    $output .= '</td>';
    $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium">';
    
    if ($session['status'] == 'in_progress') {
        $output .= '<button onclick="viewSession(' . $session['id'] . ')" class="text-blue-600 hover:text-blue-900 mr-3">View</button>';
        $output .= '<button onclick="completeSession(' . $session['id'] . ')" class="text-green-600 hover:text-green-900 mr-3">Complete</button>';
    } else {
        $output .= '<button onclick="viewSession(' . $session['id'] . ')" class="text-blue-600 hover:text-blue-900 mr-3">View</button>';
    }
    
    if ($session['status'] == 'in_progress') {
        $output .= '<button onclick="deleteSession(' . $session['id'] . ')" class="text-red-600 hover:text-red-900">Delete</button>';
    }
    
    $output .= '</td>';
    $output .= '</tr>';
}

if (mysqli_num_rows($result) == 0) {
    $output = '<tr><td colspan="8" class="px-6 py-4 text-center text-gray-500">No stock take sessions found</td></tr>';
}

echo $output;
?>
