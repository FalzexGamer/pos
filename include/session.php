<?php
session_start();
date_default_timezone_set("Asia/Kuala_Lumpur");

if (empty($_SESSION['user_id'])) {
    header("location: logout.php");
}

// Get user permissions
$user_id = $_SESSION['user_id'];
$query_user = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
$user_permissions = mysqli_fetch_array($query_user);

// Get company info
$query_company = mysqli_query($conn, "SELECT * FROM company_settings LIMIT 1");
$company = mysqli_fetch_array($query_company);
?>
