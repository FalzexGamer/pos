<?php
include '../include/conn.php';

// Get active membership tiers
$query = "SELECT id, name FROM membership_tiers WHERE is_active = 1 ORDER BY name ASC";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    while ($tier = mysqli_fetch_assoc($result)) {
        echo '<option value="' . $tier['id'] . '">' . htmlspecialchars($tier['name']) . '</option>';
    }
} else {
    echo '<option value="">No tiers available</option>';
}

mysqli_free_result($result);
?>
