<?php
include 'include/conn.php';

echo "<h1>Member Report Test</h1>";

// Test 1: Check if tables exist
echo "<h2>Test 1: Database Tables</h2>";
$tables = ['members', 'membership_tiers'];
foreach ($tables as $table) {
    $result = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    if (mysqli_num_rows($result) > 0) {
        echo "✓ Table '$table' exists<br>";
    } else {
        echo "✗ Table '$table' does not exist<br>";
    }
}

// Test 2: Check member count
echo "<h2>Test 2: Member Count</h2>";
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM members");
$count = mysqli_fetch_array($result)['count'];
echo "Total members: $count<br>";

// Test 3: Check membership tiers
echo "<h2>Test 3: Membership Tiers</h2>";
$result = mysqli_query($conn, "SELECT name, COUNT(m.id) as member_count FROM membership_tiers mt LEFT JOIN members m ON mt.id = m.membership_tier_id GROUP BY mt.id, mt.name");
while ($row = mysqli_fetch_array($result)) {
    echo "{$row['name']}: {$row['member_count']} members<br>";
}

// Test 4: Test the main query from member-report.php
echo "<h2>Test 4: Main Report Query</h2>";
$query = "SELECT 
    m.member_code,
    m.name,
    m.phone,
    m.email,
    mt.name as tier_name,
    m.is_active,
    m.total_spent,
    m.total_points,
    m.created_at
FROM members m
LEFT JOIN membership_tiers mt ON m.membership_tier_id = mt.id
ORDER BY m.created_at DESC
LIMIT 5";

$result = mysqli_query($conn, $query);
if ($result) {
    echo "✓ Query executed successfully<br>";
    echo "Sample data:<br>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Code</th><th>Name</th><th>Tier</th><th>Status</th><th>Spent</th><th>Points</th></tr>";
    while ($row = mysqli_fetch_array($result)) {
        echo "<tr>";
        echo "<td>{$row['member_code']}</td>";
        echo "<td>{$row['name']}</td>";
        echo "<td>{$row['tier_name']}</td>";
        echo "<td>" . ($row['is_active'] ? 'Active' : 'Inactive') . "</td>";
        echo "<td>RM " . number_format($row['total_spent'], 2) . "</td>";
        echo "<td>" . number_format($row['total_points']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "✗ Query failed: " . mysqli_error($conn) . "<br>";
}

// Test 5: Test statistics queries
echo "<h2>Test 5: Statistics</h2>";
$stats_queries = [
    "Total Members" => "SELECT COUNT(*) as count FROM members",
    "Active Members" => "SELECT COUNT(*) as count FROM members WHERE is_active = 1",
    "Total Spent" => "SELECT SUM(total_spent) as total FROM members WHERE is_active = 1",
    "Average Spent" => "SELECT AVG(total_spent) as avg FROM members WHERE is_active = 1"
];

foreach ($stats_queries as $label => $query) {
    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_array($result);
    $value = $data['count'] ?? $data['total'] ?? $data['avg'] ?? 0;
    echo "$label: " . number_format($value, 2) . "<br>";
}

echo "<h2>Test Complete!</h2>";
echo "<p>If all tests pass, the member report should work correctly.</p>";
echo "<p><a href='member-report.php'>Go to Member Report</a></p>";
?>

