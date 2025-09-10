<?php
$conn = mysqli_connect('powershareserver.com', 'powersha_pos', 'Condition5594.', 'powersha_pos');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
