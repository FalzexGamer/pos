<?php
$conn = mysqli_connect('localhost', 'root', '', 'pos_system');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
