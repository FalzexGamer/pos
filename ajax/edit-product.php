<?php
include '../include/conn.php';

$product_id = $_POST['product_id'] ?? 0;
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

// Validation
if (!$product_id || empty($sku) || empty($name) || empty($category_id) || empty($supplier_id) || empty($uom_id)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
    exit;
}

// Check if product exists
$escaped_product_id = mysqli_real_escape_string($conn, $product_id);
$check_sql = "SELECT id, sku, barcode, stock_quantity, img FROM products WHERE id = '$escaped_product_id'";
$result_check = mysqli_query($conn, $check_sql);

if (!$result_check || mysqli_num_rows($result_check) == 0) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit;
}

$current_product = mysqli_fetch_assoc($result_check);
$old_stock_quantity = $current_product['stock_quantity'];
$current_image = $current_product['img'] ?? '-';

// Check if SKU already exists for other products
$escaped_sku = mysqli_real_escape_string($conn, $sku);
$sku_check_sql = "SELECT id FROM products WHERE sku = '$escaped_sku' AND id != '$escaped_product_id'";
$result_sku = mysqli_query($conn, $sku_check_sql);

if (!$result_sku || mysqli_num_rows($result_sku) > 0) {
    echo json_encode(['success' => false, 'message' => 'SKU already exists']);
    exit;
}

// Check if barcode already exists for other products (if provided)
if (!empty($barcode)) {
    $escaped_barcode = mysqli_real_escape_string($conn, $barcode);
    $barcode_check_sql = "SELECT id FROM products WHERE barcode = '$escaped_barcode' AND id != '$escaped_product_id'";
    $result_barcode = mysqli_query($conn, $barcode_check_sql);
    
    if (!$result_barcode || mysqli_num_rows($result_barcode) > 0) {
        echo json_encode(['success' => false, 'message' => 'Barcode already exists']);
        exit;
    }
}

// Handle image upload
$image_filename = $current_image; // Keep current image by default
$remove_current_image = isset($_POST['remove_current_image']) && $_POST['remove_current_image'] == '1';

if ($remove_current_image) {
    // Remove current image file if it exists
    if ($current_image && $current_image !== '-') {
        $old_image_path = '../uploads/products/' . $current_image;
        if (file_exists($old_image_path)) {
            unlink($old_image_path);
        }
    }
    $image_filename = '-';
} elseif (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
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
            // Remove old image if it exists
            if ($current_image && $current_image !== '-') {
                $old_image_path = '../uploads/products/' . $current_image;
                if (file_exists($old_image_path)) {
                    unlink($old_image_path);
                }
            }
            
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

// Update product with proper escaping
$escaped_name = mysqli_real_escape_string($conn, $name);
$escaped_description = mysqli_real_escape_string($conn, $description);
$escaped_barcode_value = !empty($barcode) ? "'$escaped_barcode'" : "NULL";
$escaped_image = mysqli_real_escape_string($conn, $image_filename);

$update_sql = "
    UPDATE products 
    SET sku = '$escaped_sku', 
        barcode = $escaped_barcode_value, 
        name = '$escaped_name', 
        description = '$escaped_description', 
        category_id = $category_id, 
        supplier_id = $supplier_id, 
        uom_id = $uom_id, 
        cost_price = $cost_price, 
        selling_price = $selling_price, 
        stock_quantity = $stock_quantity, 
        min_stock_level = $min_stock_level, 
        img = '$escaped_image',
        updated_at = CURRENT_TIMESTAMP
    WHERE id = '$escaped_product_id'
";

if (mysqli_query($conn, $update_sql)) {
    // Record stock movement if stock quantity changed
    $stock_difference = $stock_quantity - $old_stock_quantity;
    
    if ($stock_difference != 0) {
        $movement_type = $stock_difference > 0 ? 'in' : 'out';
        $quantity = abs($stock_difference);
        $notes = $stock_difference > 0 ? 'Stock adjustment (increase)' : 'Stock adjustment (decrease)';
        $escaped_notes = mysqli_real_escape_string($conn, $notes);
        
        $movement_sql = "
            INSERT INTO stock_movements (product_id, movement_type, quantity, reference_type, reference_id, created_by, notes) 
            VALUES ('$escaped_product_id', '$movement_type', $quantity, 'adjustment', '$escaped_product_id', 1, '$escaped_notes')
        ";
        mysqli_query($conn, $movement_sql);
    }
    
    echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update product: ' . mysqli_error($conn)]);
}

// No need to close prepared statements since we're using regular queries
?>
