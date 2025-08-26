# POS System - Point of Sale Web Application

A comprehensive, responsive Point of Sale (POS) web application built with PHP, MySQL, and Tailwind CSS. This system includes inventory management, membership system, stock take functionality, and complete sales processing.

## Features

### 🛍️ Point of Sale Features
- **Barcode Scanning & Manual Search**: Search products by name, SKU, or barcode
- **Multiple Payment Methods**: Cash, Card, E-Wallet support
- **Discount & Voucher Support**: Member discounts and promotional vouchers
- **Sales Receipt Printing**: Thermal printer support with auto-print functionality
- **Multiple Cash Registers**: Support for multiple sales sessions
- **Real-time Stock Updates**: Automatic stock deduction on sales

### 👥 Membership System
- **Member Management**: Add, edit, and manage customer profiles
- **Member Details**: Store name, phone, email, address
- **Purchase History**: Track all purchases by member
- **Membership Tiers**: Regular, Gold, Platinum with different discount rates
- **Points System**: Track member points and total spent

### 📦 Inventory Management
- **Product Management**: Add/edit/delete products with SKU and barcode
- **Unit of Measurement (UOM)**: Support for pcs, box, kg, liter, etc.
- **Stock Alerts**: Low inventory notifications
- **Category Management**: Organize products by categories
- **Supplier Management**: Track product suppliers
- **Stock Movements**: Complete audit trail of stock changes

### 📋 Stock Take System
- **Stock Take Sessions**: Create and manage stock take sessions
- **Physical Count**: Compare physical stock vs system stock
- **Discrepancy Management**: Update stock after verification
- **History Log**: Complete history of all stock take sessions
- **Reporting**: Stock take reports and analysis

### 📊 Reporting & Analytics
- **Sales Reports**: Comprehensive sales analytics with interactive charts
  - Daily, weekly, monthly, and yearly reporting
  - Revenue trends and sales count visualization
  - Top products and categories analysis
  - Payment method breakdown
  - Growth comparison with previous periods
  - Export to CSV and print functionality
- **Inventory Reports**: Stock levels, low stock alerts
- **Member Reports**: Member activity and spending analysis
- **Profit & Loss**: Financial performance tracking
- **Dashboard**: Real-time statistics and insights

## Technology Stack

- **Backend**: PHP 7.4+ (Pure PHP)
- **Database**: MySQL 5.7+
- **Frontend**: Tailwind CSS, jQuery, DataTables
- **Icons**: Font Awesome
- **Charts**: ApexCharts
- **Printing**: Print.js for receipt printing

## Installation & Setup

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Modern web browser

### Installation Steps

1. **Clone/Download the Project**
   ```bash
   # If using git
   git clone <repository-url>
   cd pos
   
   # Or download and extract to your web server directory
   ```

2. **Database Setup**
   ```bash
   # Import the database structure
   mysql -u root -p < database.sql
   
   # Or use phpMyAdmin to import database.sql
   ```

3. **Configure Database Connection**
   ```php
   # Edit pos/include/conn.php
   $conn = mysqli_connect('localhost', 'your_username', 'your_password', 'pos_system');
   ```

4. **Set Permissions**
   ```bash
   # Ensure web server has read/write permissions
   chmod 755 pos/
   chmod 644 pos/include/conn.php
   ```

5. **Access the Application**
   ```
   http://your-domain.com/pos/
   ```

### Default Login Credentials
- **Admin**: username: `admin`, password: `admin123`
- **Cashier**: username: `cashier`, password: `cashier123`

## Database Structure

### Core Tables
- `users` - System users (admin, manager, cashier)
- `company_settings` - Company information
- `products` - Product inventory
- `categories` - Product categories
- `suppliers` - Product suppliers
- `uom` - Units of measurement
- `members` - Customer/member information
- `membership_tiers` - Membership levels and discounts
- `sales` - Sales transactions
- `sale_items` - Individual items in sales
- `sales_sessions` - Cash register sessions
- `stock_movements` - Stock change audit trail
- `stock_take_sessions` - Stock take sessions
- `stock_take_items` - Individual stock take items
- `vouchers` - Discount vouchers

## Usage Guide

### 1. Initial Setup
1. Login as admin
2. Configure company settings
3. Add categories, suppliers, and UOM
4. Add initial products
5. Create user accounts for cashiers

### 2. Daily Operations
1. **Start Sales Session**: Cashier logs in and starts a new session
2. **Process Sales**: Use POS interface to scan/search products and complete sales
3. **Member Management**: Add new members and process member transactions
4. **Stock Management**: Monitor stock levels and perform stock takes
5. **End Session**: Close sales session and generate reports

### 3. POS Interface
- **Product Search**: Type product name, SKU, or scan barcode
- **Add to Cart**: Click on product or scan barcode
- **Quantity Adjustment**: Use +/- buttons or type quantity
- **Member Selection**: Select member for discounts
- **Payment Processing**: Choose payment method and complete transaction
- **Receipt Printing**: Automatic receipt generation and printing

### 4. Inventory Management
- **Add Products**: Use the product management interface
- **Stock Updates**: Automatic updates on sales, manual adjustments available
- **Low Stock Alerts**: Dashboard shows products below minimum stock level
- **Stock Take**: Regular physical count verification

### 5. Reporting
- **Sales Reports**: Filter by date range, payment method, cashier
- **Inventory Reports**: Stock levels, movement history
- **Member Reports**: Member activity, spending patterns
- **Financial Reports**: Profit & loss analysis

## Security Features

- **Session Management**: Secure session handling
- **Role-based Access**: Admin, Manager, Cashier roles
- **Input Validation**: Server-side validation for all inputs
- **SQL Injection Protection**: Prepared statements and input sanitization
- **XSS Protection**: Output encoding and sanitization

## Performance Optimization

- **Database Indexing**: Optimized indexes for fast queries
- **Query Optimization**: Efficient SQL queries
- **Caching**: Session-based caching for frequently accessed data
- **Responsive Design**: Mobile-first approach for all devices

## Customization

### Adding New Payment Methods
1. Update the `payment_method` enum in the `sales` table
2. Modify the POS interface payment buttons
3. Update the payment processing logic

### Customizing Receipt Layout
1. Edit `print-receipt.php`
2. Modify CSS styles for different printer types
3. Add custom fields as needed

### Adding New Reports
1. Create new PHP files in the reports directory
2. Add menu items in the sidebar
3. Implement the reporting logic

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Check database credentials in `include/conn.php`
   - Ensure MySQL service is running
   - Verify database exists

2. **Session Issues**
   - Check PHP session configuration
   - Ensure session directory is writable
   - Clear browser cookies

3. **Printing Problems**
   - Check printer settings
   - Ensure print.js is loaded
   - Test with different browsers

4. **Performance Issues**
   - Check database indexes
   - Monitor server resources
   - Optimize large datasets

### Error Logs
- Check PHP error logs for detailed error messages
- Database errors are logged in MySQL error log
- Application errors may appear in browser console

## Support & Maintenance

### Regular Maintenance
- **Database Backups**: Regular automated backups
- **Log Monitoring**: Monitor error logs and access logs
- **Performance Monitoring**: Regular performance checks
- **Security Updates**: Keep PHP and MySQL updated

### Backup Procedures
```bash
# Database backup
mysqldump -u username -p pos_system > backup_$(date +%Y%m%d).sql

# File backup
tar -czf pos_backup_$(date +%Y%m%d).tar.gz pos/
```

## License

This project is provided as-is for educational and commercial use. Please ensure compliance with any third-party library licenses.

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## Version History

- **v1.0.0** - Initial release with core POS functionality
- **v1.1.0** - Added membership system and stock take
- **v1.2.0** - Enhanced reporting and mobile responsiveness

---

For additional support or questions, please refer to the documentation or contact the development team.
