<?php
include '../include/conn.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$search = $_GET['search'] ?? '';

// Log the search term
error_log("Search term: " . $search);

// Only search if there's a search term
if (!empty($search)) {
    $search_term = '%' . mysqli_real_escape_string($conn, $search) . '%';
    $query = "SELECT m.*, mt.name as tier_name, mt.discount_percentage 
              FROM members m 
              LEFT JOIN membership_tiers mt ON m.membership_tier_id = mt.id 
              WHERE m.is_active = 1 
              AND (m.name LIKE ? OR m.member_code LIKE ? OR m.phone LIKE ?)
              ORDER BY 
                CASE 
                  WHEN m.member_code LIKE ? THEN 1
                  WHEN m.name LIKE ? THEN 2
                  ELSE 3
                END,
                m.member_code ASC";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sssss", $search_term, $search_term, $search_term, $search_term, $search_term);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    // Return empty result when no search term
    $result = false;
}

$members = [];

if ($result && mysqli_num_rows($result) > 0) {
    error_log("Found " . mysqli_num_rows($result) . " members");
    while ($member = mysqli_fetch_assoc($result)) {
        $members[] = [
            'id' => $member['id'],
            'name' => $member['name'],
            'member_code' => $member['member_code'],
            'phone' => $member['phone'],
            'email' => $member['email'],
            'tier_name' => $member['tier_name'] ?: 'No Tier',
            'discount_percentage' => floatval($member['discount_percentage'] ?: 0),
            'total_spent' => floatval($member['total_spent'] ?: 0)
        ];
    }
} else {
    error_log("No members found. Result: " . ($result ? "valid" : "invalid"));
    if ($result) {
        error_log("Number of rows: " . mysqli_num_rows($result));
    }
}

$response = [
    'success' => true,
    'members' => $members
];

error_log("Sending response: " . json_encode($response));
echo json_encode($response);

if (isset($stmt)) {
    mysqli_stmt_close($stmt);
}
?>
