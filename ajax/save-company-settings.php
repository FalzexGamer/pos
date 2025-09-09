<?php
include '../include/conn.php';
include '../include/session.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is admin
if ($_SESSION['role'] != 'admin') {
    echo json_encode(['success' => false, 'message' => 'Access denied. Admin privileges required.']);
    exit();
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}

try {
    // Get form data
    $company_name = trim($_POST['company_name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $website = trim($_POST['website'] ?? '');
    $tax_number = trim($_POST['tax_number'] ?? '');
    $currency = trim($_POST['currency'] ?? 'MYR');
    
    // Validate required fields
    if (empty($company_name)) {
        echo json_encode(['success' => false, 'message' => 'Company name is required.']);
        exit();
    }
    
    // Validate email if provided
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
        exit();
    }
    
    // Validate website URL if provided
    if (!empty($website) && !filter_var($website, FILTER_VALIDATE_URL)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid website URL.']);
        exit();
    }
    
    // Handle logo upload
    $logo_path = '';
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/company/';
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_info = pathinfo($_FILES['logo']['name']);
        $file_extension = strtolower($file_info['extension']);
        
        // Validate file type
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($file_extension, $allowed_extensions)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF, and WebP images are allowed.']);
            exit();
        }
        
        // Validate file size (2MB max)
        if ($_FILES['logo']['size'] > 2 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'File size too large. Maximum size is 2MB.']);
            exit();
        }
        
        // Generate unique filename
        $filename = 'logo_' . time() . '.' . $file_extension;
        $upload_path = $upload_dir . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($_FILES['logo']['tmp_name'], $upload_path)) {
            $logo_path = 'uploads/company/' . $filename;
            
            // Delete old logo if exists
            $query_old_logo = mysqli_query($conn, "SELECT logo FROM company_settings WHERE id = 1");
            if ($query_old_logo && mysqli_num_rows($query_old_logo) > 0) {
                $old_settings = mysqli_fetch_array($query_old_logo);
                if (!empty($old_settings['logo']) && file_exists('../' . $old_settings['logo'])) {
                    unlink('../' . $old_settings['logo']);
                }
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload logo.']);
            exit();
        }
    }
    
    // Check if company settings already exist
    $query_check = mysqli_query($conn, "SELECT id FROM company_settings WHERE id = 1");
    
    if (mysqli_num_rows($query_check) > 0) {
        // Update existing settings
        if (!empty($logo_path)) {
            $sql = "UPDATE company_settings SET 
                    company_name = ?, 
                    address = ?, 
                    phone = ?, 
                    email = ?, 
                    website = ?, 
                    tax_number = ?, 
                    currency = ?, 
                    logo = ?,
                    updated_at = CURRENT_TIMESTAMP 
                    WHERE id = 1";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'ssssssss', $company_name, $address, $phone, $email, $website, $tax_number, $currency, $logo_path);
        } else {
            $sql = "UPDATE company_settings SET 
                    company_name = ?, 
                    address = ?, 
                    phone = ?, 
                    email = ?, 
                    website = ?, 
                    tax_number = ?, 
                    currency = ?,
                    updated_at = CURRENT_TIMESTAMP 
                    WHERE id = 1";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'sssssss', $company_name, $address, $phone, $email, $website, $tax_number, $currency);
        }
    } else {
        // Insert new settings
        if (!empty($logo_path)) {
            $sql = "INSERT INTO company_settings (company_name, address, phone, email, website, tax_number, currency, logo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'ssssssss', $company_name, $address, $phone, $email, $website, $tax_number, $currency, $logo_path);
        } else {
            $sql = "INSERT INTO company_settings (company_name, address, phone, email, website, tax_number, currency) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'sssssss', $company_name, $address, $phone, $email, $website, $tax_number, $currency);
        }
    }
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'Company settings updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update company settings.']);
    }
    
    mysqli_stmt_close($stmt);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>
