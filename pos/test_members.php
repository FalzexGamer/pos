<?php
include 'include/conn.php';

echo "<h2>Testing Member Database Connection</h2>";

// Test 1: Check if tables exist
$tables_query = mysqli_query($conn, "SHOW TABLES LIKE 'members'");
if (mysqli_num_rows($tables_query) > 0) {
    echo "<p style='color: green;'>✓ Members table exists</p>";
} else {
    echo "<p style='color: red;'>✗ Members table does not exist</p>";
}

$tiers_query = mysqli_query($conn, "SHOW TABLES LIKE 'membership_tiers'");
if (mysqli_num_rows($tiers_query) > 0) {
    echo "<p style='color: green;'>✓ Membership tiers table exists</p>";
} else {
    echo "<p style='color: red;'>✗ Membership tiers table does not exist</p>";
}

// Test 2: Check member count
$count_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM members WHERE is_active = 1");
$count_result = mysqli_fetch_assoc($count_query);
echo "<p>Total active members: " . $count_result['count'] . "</p>";

// Test 3: Show sample members
$members_query = mysqli_query($conn, "
    SELECT m.*, mt.name as tier_name, mt.discount_percentage 
    FROM members m 
    LEFT JOIN membership_tiers mt ON m.membership_tier_id = mt.id 
    WHERE m.is_active = 1 
    ORDER BY m.member_code ASC 
    LIMIT 5
");

echo "<h3>Sample Members:</h3>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>ID</th><th>Member Code</th><th>Name</th><th>Tier</th><th>Discount</th><th>Total Spent</th></tr>";

while ($member = mysqli_fetch_assoc($members_query)) {
    echo "<tr>";
    echo "<td>" . $member['id'] . "</td>";
    echo "<td><strong>" . $member['member_code'] . "</strong></td>";
    echo "<td>" . $member['name'] . "</td>";
    echo "<td>" . ($member['tier_name'] ?: 'No Tier') . "</td>";
    echo "<td>" . ($member['discount_percentage'] ?: '0') . "%</td>";
    echo "<td>RM " . number_format($member['total_spent'], 2) . "</td>";
    echo "</tr>";
}

echo "</table>";

// Test 4: Test search functionality
echo "<h3>Testing Search:</h3>";
$search_term = 'MEM';
$search_query = "SELECT m.*, mt.name as tier_name, mt.discount_percentage 
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

$search_result = mysqli_query($conn, $search_query);
echo "<p>Search results for '$search_term': " . mysqli_num_rows($search_result) . " members found</p>";

if (mysqli_num_rows($search_result) > 0) {
    echo "<ul>";
    while ($member = mysqli_fetch_assoc($search_result)) {
        echo "<li><strong>" . $member['member_code'] . "</strong> - " . $member['name'] . " (" . ($member['tier_name'] ?: 'No Tier') . ")</li>";
    }
    echo "</ul>";
}
?>
