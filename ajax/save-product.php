<?php
include '../include/conn.php';

$sku = $_POST['sku'] ?? '';
$barcode = $_POST['barcode'] ?? '';
$name = $_POST['name'] ?? '';
$description = $_POST['description'] ?? '';
$category_id = $_POST['category_id'] ?? '';
$supplier_id = $_POST['supplier_id'] ?? '';
$uom_id = $_POST['uom_id'] ?? '';
$cost_price = $_POST['cost_price'] ?? 0;
$selling_price = $_POST['selling_price'] ?? 0;
$stock_quantity = $_POST['stock_quantity'] ?? 0;
$min_stock_level = $_POST['min_stock_level'] ?? 0;

// Handle image upload
$image_filename = '-'; // Default value
if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = '../uploads/products/';
    
    // Create directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $file = $_FILES['product_image'];
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png'];
    
    // Validate file type
    if (in_array($file_extension, $allowed_extensions)) {
        // Validate file size (2MB max)
        if ($file['size'] <= 2 * 1024 * 1024) {
            // Generate unique filename
            $image_filename = $sku . '_' . time() . '.' . $file_extension;
            $upload_path = $upload_dir . $image_filename;
            
            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                // Image uploaded successfully
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Image file size must be less than 2MB']);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid image file type. Only JPG, JPEG, and PNG are allowed']);
        exit;
    }
}

// Validation
if (empty($sku) || empty($name) || empty($category_id) || empty($supplier_id) || empty($uom_id)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
    exit;
}

// Check if SKU already exists
$check_sku = mysqli_query($conn, "SELECT id FROM products WHERE sku = '$sku'");
if (mysqli_num_rows($check_sku) > 0) {
    echo json_encode(['success' => false, 'message' => 'SKU already exists']);
    exit;
}

// Check if barcode already exists (if provided)
if (!empty($barcode)) {
    $check_barcode = mysqli_query($conn, "SELECT id FROM products WHERE barcode = '$barcode'");
    if (mysqli_num_rows($check_barcode) > 0) {
        echo json_encode(['success' => false, 'message' => 'Barcode already exists']);
        exit;
    }
}

// Insert product
$insert = mysqli_query($conn, "
    INSERT INTO products (sku, barcode, name, description, category_id, supplier_id, uom_id, cost_price, selling_price, stock_quantity, min_stock_level, img) 
    VALUES ('$sku', " . ($barcode ? "'$barcode'" : "NULL") . ", '$name', '$description', $category_id, $supplier_id, $uom_id, $cost_price, $selling_price, $stock_quantity, $min_stock_level, '$image_filename')
");

if ($insert) {
    $product_id = mysqli_insert_id($conn);
    
    // Record initial stock movement if stock quantity > 0
    if ($stock_quantity > 0) {
        mysqli_query($conn, "
            INSERT INTO stock_movements (product_id, movement_type, quantity, reference_type, reference_id, created_by, notes) 
            VALUES ($product_id, 'in', $stock_quantity, 'adjustment', $product_id, 1, 'Initial stock')
        ");
    }
    
    echo json_encode(['success' => true, 'message' => 'Product saved successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save product']);
}
?>
