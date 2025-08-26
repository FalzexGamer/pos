<?php
include '../include/conn.php';

$query = mysqli_query($conn, "SELECT * FROM suppliers WHERE is_active = 1 ORDER BY name");

echo '<option value="">Select Supplier</option>';
while ($supplier = mysqli_fetch_array($query)) {
    echo '<option value="' . $supplier['id'] . '">' . $supplier['name'] . '</option>';
}
?>
