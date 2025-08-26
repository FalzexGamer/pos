# POS System - Quick Setup Guide

## Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Modern web browser

## Installation Steps

### 1. Database Setup
```bash
# Create database
mysql -u root -p
CREATE DATABASE pos_system;
USE pos_system;

# Import database structure
mysql -u root -p pos_system < database.sql
```

### 2. Configure Database Connection
Edit `pos/include/conn.php`:
```php
$conn = mysqli_connect('localhost', 'your_username', 'your_password', 'pos_system');
```

### 3. Set File Permissions
```bash
chmod 755 pos/
chmod 644 pos/include/conn.php
```

### 4. Access the Application
Open your browser and navigate to:
```
http://your-domain.com/pos/
```

## Default Login Credentials
- **Admin**: username: `admin`, password: `admin123`
- **Cashier**: username: `cashier`, password: `cashier123`

## Initial Configuration

### 1. Company Settings
1. Login as admin
2. Go to Settings > Company Settings
3. Update company information

### 2. Add Basic Data
1. **Categories**: Add product categories (Electronics, Clothing, etc.)
2. **Suppliers**: Add supplier information
3. **UOM**: Add units of measurement (pcs, kg, L, etc.)
4. **Products**: Add your inventory products

### 3. Create Users
1. Go to Settings > Users
2. Add cashier accounts for your staff

## Quick Start Guide

### For Cashiers:
1. Login with cashier credentials
2. Click "Point of Sale" to start selling
3. Search products by name, SKU, or scan barcode
4. Add products to cart
5. Select member (optional) for discounts
6. Choose payment method and complete sale
7. Receipt will auto-print

### For Admins:
1. Monitor dashboard for sales statistics
2. Manage inventory through Products section
3. Add/edit members in Members section
4. Generate reports as needed
5. Perform stock takes when required

## Features Overview

### POS Features:
- ✅ Product search and barcode scanning
- ✅ Multiple payment methods
- ✅ Member discounts
- ✅ Receipt printing
- ✅ Real-time stock updates

### Inventory Management:
- ✅ Product management with SKU/barcode
- ✅ Category and supplier management
- ✅ UOM support
- ✅ Stock alerts
- ✅ Stock take functionality

### Membership System:
- ✅ Member management
- ✅ Membership tiers with discounts
- ✅ Purchase history tracking
- ✅ Points system

### Reporting:
- ✅ Sales reports
- ✅ Inventory reports
- ✅ Member reports
- ✅ Dashboard analytics

## Troubleshooting

### Common Issues:

1. **Database Connection Error**
   - Check database credentials in `include/conn.php`
   - Ensure MySQL service is running

2. **Session Issues**
   - Check PHP session configuration
   - Clear browser cookies

3. **Printing Problems**
   - Check printer settings
   - Test with different browsers

4. **Performance Issues**
   - Check database indexes
   - Monitor server resources

## Support
For additional help, refer to the main README.md file or contact the development team.

---

**Note**: This is a basic setup guide. For detailed documentation, see README.md
