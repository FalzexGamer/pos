<?php
include '../include/conn.php';

$query = mysqli_query($conn, "SELECT * FROM categories WHERE is_active = 1 ORDER BY name");

echo '<option value="">Select Category</option>';
while ($category = mysqli_fetch_array($query)) {
    echo '<option value="' . $category['id'] . '">' . $category['name'] . '</option>';
}
?>
