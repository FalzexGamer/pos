<?php
include '../include/conn.php';

// Get active membership tiers
$query = "SELECT id, name, discount_percentage FROM membership_tiers WHERE is_active = 1 ORDER BY name ASC";
$result = mysqli_query($conn, $query);

$tiers = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($tier = mysqli_fetch_assoc($result)) {
        $tiers[] = [
            'id' => $tier['id'],
            'name' => $tier['name'],
            'discount_percentage' => floatval($tier['discount_percentage'])
        ];
    }
}

echo json_encode([
    'success' => true,
    'tiers' => $tiers
]);

mysqli_free_result($result);
?>
