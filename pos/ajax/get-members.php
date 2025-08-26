<?php
include '../include/conn.php';

$query = mysqli_query($conn, "SELECT * FROM members WHERE is_active = 1 ORDER BY name");

while ($member = mysqli_fetch_array($query)) {
    echo '<option value="' . $member['id'] . '">' . $member['name'] . ' (' . $member['member_code'] . ')</option>';
}
?>
