<?php
include 'include/conn.php';

echo "<h2>Debug Members Database</h2>";

// Check if we can connect
if (!$conn) {
    echo "<p style='color: red;'>✗ Database connection failed</p>";
    exit;
}

echo "<p style='color: green;'>✓ Database connected</p>";

// Check if tables exist
$tables = ['members', 'membership_tiers'];
foreach ($tables as $table) {
    $result = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    if (mysqli_num_rows($result) > 0) {
        echo "<p style='color: green;'>✓ Table '$table' exists</p>";
    } else {
        echo "<p style='color: red;'>✗ Table '$table' does not exist</p>";
    }
}

// Check member count
$count_result = mysqli_query($conn, "SELECT COUNT(*) as count FROM members");
$count = mysqli_fetch_assoc($count_result)['count'];
echo "<p>Total members in database: $count</p>";

// Check active member count
$active_result = mysqli_query($conn, "SELECT COUNT(*) as count FROM members WHERE is_active = 1");
$active_count = mysqli_fetch_assoc($active_result)['count'];
echo "<p>Active members: $active_count</p>";

// Show all members
echo "<h3>All Members:</h3>";
$all_members = mysqli_query($conn, "SELECT * FROM members ORDER BY member_code");
if (mysqli_num_rows($all_members) > 0) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Member Code</th><th>Name</th><th>Active</th><th>Tier ID</th></tr>";
    while ($member = mysqli_fetch_assoc($all_members)) {
        echo "<tr>";
        echo "<td>" . $member['id'] . "</td>";
        echo "<td><strong>" . $member['member_code'] . "</strong></td>";
        echo "<td>" . $member['name'] . "</td>";
        echo "<td>" . ($member['is_active'] ? 'Yes' : 'No') . "</td>";
        echo "<td>" . $member['membership_tier_id'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>No members found in database!</p>";
}

// Test the exact query from get-members-for-modal.php
echo "<h3>Testing Search Query:</h3>";
$search_term = 'MEM';
$query = "SELECT m.*, mt.name as tier_name, mt.discount_percentage 
          FROM members m 
          LEFT JOIN membership_tiers mt ON m.membership_tier_id = mt.id 
          WHERE m.is_active = 1 
          AND (m.name LIKE '%$search_term%' OR m.member_code LIKE '%$search_term%' OR m.phone LIKE '%$search_term%')
          ORDER BY 
            CASE 
              WHEN m.member_code LIKE '%$search_term%' THEN 1
              WHEN m.name LIKE '%$search_term%' THEN 2
              ELSE 3
            END,
            m.member_code ASC";

echo "<p>Query: " . htmlspecialchars($query) . "</p>";

$search_result = mysqli_query($conn, $query);
if ($search_result) {
    echo "<p>Search results for '$search_term': " . mysqli_num_rows($search_result) . " members found</p>";
    
    if (mysqli_num_rows($search_result) > 0) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Member Code</th><th>Name</th><th>Tier</th><th>Discount</th></tr>";
        while ($member = mysqli_fetch_assoc($search_result)) {
            echo "<tr>";
            echo "<td>" . $member['id'] . "</td>";
            echo "<td><strong>" . $member['member_code'] . "</strong></td>";
            echo "<td>" . $member['name'] . "</td>";
            echo "<td>" . ($member['tier_name'] ?: 'No Tier') . "</td>";
            echo "<td>" . ($member['discount_percentage'] ?: '0') . "%</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} else {
    echo "<p style='color: red;'>Query failed: " . mysqli_error($conn) . "</p>";
}
?>
