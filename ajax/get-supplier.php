<?php
include '../include/conn.php';
include '../include/session.php';

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'Supplier ID is required']);
    exit;
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM suppliers WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    
    // Use bind_result instead of get_result for better compatibility
    $id_result = $name = $contact_person = $phone = $email = $address = $is_active = $created_at = $updated_at = null;
    mysqli_stmt_bind_result($stmt, $id_result, $name, $contact_person, $phone, $email, $address, $is_active, $created_at, $updated_at);
    
    if (mysqli_stmt_fetch($stmt)) {
        $supplier = [
            'id' => $id_result,
            'name' => $name,
            'contact_person' => $contact_person,
            'phone' => $phone,
            'email' => $email,
            'address' => $address,
            'is_active' => $is_active,
            'created_at' => $created_at,
            'updated_at' => $updated_at
        ];
        echo json_encode($supplier);
    } else {
        echo json_encode(['error' => 'Supplier not found']);
    }
} else {
    echo json_encode(['error' => 'Database error: ' . mysqli_error($conn)]);
}

mysqli_stmt_close($stmt);
?>
