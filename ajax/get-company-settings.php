<?php
include '../include/conn.php';
include '../include/session.php';

// Set content type to JSON
header('Content-Type: application/json');

try {
    // Get company settings
    $query = mysqli_query($conn, "SELECT * FROM company_settings WHERE id = 1");
    
    if ($query && mysqli_num_rows($query) > 0) {
        $settings = mysqli_fetch_array($query);
        
        // Remove sensitive data if needed and format response
        $response = [
            'success' => true,
            'data' => [
                'id' => $settings['id'],
                'company_name' => $settings['company_name'],
                'address' => $settings['address'],
                'phone' => $settings['phone'],
                'email' => $settings['email'],
                'website' => $settings['website'],
                'tax_number' => $settings['tax_number'],
                'currency' => $settings['currency'],
                'logo' => $settings['logo'],
                'created_at' => $settings['created_at'],
                'updated_at' => $settings['updated_at']
            ]
        ];
        
        echo json_encode($response);
    } else {
        // Return default settings if none exist
        $default_settings = [
            'success' => true,
            'data' => [
                'id' => 0,
                'company_name' => 'POS System',
                'address' => '',
                'phone' => '',
                'email' => '',
                'website' => '',
                'tax_number' => '',
                'currency' => 'MYR',
                'logo' => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        
        echo json_encode($default_settings);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>
