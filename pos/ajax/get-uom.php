<?php
include '../include/conn.php';

$query = mysqli_query($conn, "SELECT * FROM uom WHERE is_active = 1 ORDER BY name");

echo '<option value="">Select UOM</option>';
while ($uom = mysqli_fetch_array($query)) {
    echo '<option value="' . $uom['id'] . '">' . $uom['name'] . ' (' . $uom['abbreviation'] . ')</option>';
}
?>
