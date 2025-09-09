# Company Settings Module

This module provides a comprehensive company settings management system for the POS application.

## Features

- **Company Information Management**: Update company name, address, contact details
- **Logo Upload**: Upload and manage company logo with image validation
- **Currency Settings**: Configure system currency (MYR, USD, EUR, SGD, THB, IDR)
- **Responsive Design**: Fully responsive interface using Tailwind CSS
- **Admin Access Control**: Only admin users can access and modify settings
- **Real-time Validation**: Client-side and server-side validation
- **Secure File Upload**: Logo upload with file type and size validation

## Files Created

### Main Files
- `company-settings.php` - Main settings page with responsive form
- `ajax/save-company-settings.php` - AJAX endpoint for saving settings
- `ajax/get-company-settings.php` - AJAX endpoint for retrieving settings

### Directory Structure
- `uploads/company/` - Directory for company logo uploads
- `uploads/company/.htaccess` - Security configuration for uploads
- `uploads/company/index.php` - Prevents direct directory access

## Database Schema

The module uses the existing `company_settings` table with the following structure:

```sql
CREATE TABLE `company_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) NOT NULL,
  `address` text,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `tax_number` varchar(100) DEFAULT NULL,
  `currency` varchar(10) DEFAULT 'MYR',
  `logo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);
```

## Usage

1. **Access**: Navigate to Company Settings from the admin sidebar menu
2. **Update Information**: Fill in company details and click "Save Settings"
3. **Upload Logo**: Select an image file (JPG, PNG, GIF, WebP) up to 2MB
4. **Currency**: Select the appropriate currency for your business

## Security Features

- Admin-only access control
- File upload validation (type and size)
- SQL injection prevention using prepared statements
- XSS protection with proper output escaping
- Secure file upload directory with .htaccess protection

## Responsive Design

The interface is fully responsive and optimized for:
- Desktop (1024px+)
- Tablet (768px - 1023px)
- Mobile (320px - 767px)

## Integration

The company settings can be accessed by other parts of the system using:
```javascript
$.get('ajax/get-company-settings.php', function(response) {
    if (response.success) {
        const settings = response.data;
        // Use settings data
    }
});
```

## Technical Stack

- **Backend**: Procedural PHP with MySQLi
- **Frontend**: Tailwind CSS for styling
- **JavaScript**: jQuery for AJAX and DOM manipulation
- **Validation**: Client-side and server-side validation
- **File Upload**: Secure file handling with validation
