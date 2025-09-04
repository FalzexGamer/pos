# Payment Methods Management System

This system provides a complete CRUD (Create, Read, Update, Delete) interface for managing payment methods in your POS system.

## Features

- **Add Payment Methods**: Create new payment methods with name, description, and status
- **Edit Payment Methods**: Modify existing payment method details
- **Delete Payment Methods**: Remove payment methods (with safety checks)
- **Search & Filter**: Find payment methods by name, description, or status
- **Pagination**: Navigate through large lists of payment methods
- **Export**: Download payment methods data as CSV
- **Statistics**: View total, active, inactive, and monthly counts
- **Responsive Design**: Works on desktop, tablet, and mobile devices

## Database Structure

The `payment_methods` table has the following structure:

| Column | Type | Description |
|--------|------|-------------|
| `id` | int | Primary key, auto-increment |
| `name` | varchar(100) | Payment method name (required) |
| `description` | text | Optional description |
| `is_active` | tinyint(1) | Status (1=active, 0=inactive) |
| `created_at` | timestamp | Creation timestamp |
| `updated_at` | timestamp | Last update timestamp |

## Setup Instructions

### 1. Database Setup

Run the SQL script to create the table:

```sql
-- Execute the contents of create_payment_methods_table.sql
-- This will create the table and insert sample data
```

### 2. File Structure

Ensure the following files are in place:

```
ajax/
├── get-payment-methods.php          # List payment methods with pagination
├── get-payment-methods-stats.php    # Get statistics
├── get-payment-method.php           # Get single payment method
├── save-payment-method.php          # Create new payment method
├── edit-payment-method.php          # Update existing payment method
├── delete-payment-method.php        # Delete payment method
└── export-payment-methods.php       # Export to CSV

payment-methods.php                   # Main interface
create_payment_methods_table.sql      # Database setup
```

### 3. Access Control

The payment methods page is accessible only to admin users (as configured in the sidebar).

## Usage

### Adding a Payment Method

1. Click the "Add Payment Method" button
2. Fill in the name (required) and description (optional)
3. Set the status (active/inactive)
4. Click "Save"

### Editing a Payment Method

1. Click the edit icon (pencil) next to the payment method
2. Modify the details as needed
3. Click "Save"

### Deleting a Payment Method

1. Click the delete icon (trash) next to the payment method
2. Confirm the deletion in the popup dialog
3. Click "Delete"

### Searching and Filtering

- Use the search box to find payment methods by name or description
- Use the status filter to show only active or inactive methods
- Click "Refresh" to reload the current data
- Click "Export" to download the filtered data as CSV

## API Endpoints

All AJAX endpoints return JSON responses with the following structure:

```json
{
  "success": true/false,
  "message": "Response message",
  "data": {...} // Optional data payload
}
```

### GET Endpoints

- `get-payment-methods.php?page=X&search=Y&status=Z`
- `get-payment-methods-stats.php`
- `get-payment-method.php?id=X`

### POST Endpoints

- `save-payment-method.php` - Create new
- `edit-payment-method.php` - Update existing
- `delete-payment-method.php` - Delete

### Export Endpoint

- `export-payment-methods.php?search=X&status=Y` - Download CSV

## Security Features

- **SQL Injection Protection**: All queries use prepared statements
- **XSS Protection**: Output is properly escaped
- **Session Validation**: Access control through session management
- **Input Validation**: Server-side validation of all inputs
- **CSRF Protection**: Form submissions are validated

## Customization

### Adding New Fields

To add new fields to the payment methods:

1. Modify the database table structure
2. Update the form in `payment-methods.php`
3. Modify the AJAX files to handle the new fields
4. Update the display logic

### Modifying Validation Rules

Edit the validation logic in the AJAX files:

- `save-payment-method.php` - For new payment methods
- `edit-payment-method.php` - For updates

### Changing the UI

The interface uses Tailwind CSS classes and can be customized by:

- Modifying CSS classes in the HTML
- Adding custom CSS in the `<style>` section
- Updating JavaScript functions for enhanced functionality

## Troubleshooting

### Common Issues

1. **Page not loading**: Check database connection and table existence
2. **AJAX errors**: Check browser console for JavaScript errors
3. **Permission denied**: Ensure user has admin role
4. **Database errors**: Verify table structure and SQL syntax

### Debug Mode

Enable error reporting in PHP for debugging:

```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## Browser Compatibility

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+
- Mobile browsers (iOS Safari, Chrome Mobile)

## Performance Considerations

- Pagination limits results to 10 items per page
- Database indexes on frequently queried columns
- Debounced search to reduce API calls
- Efficient SQL queries with proper WHERE clauses

## Future Enhancements

Potential improvements for future versions:

- Bulk operations (bulk delete, bulk status change)
- Payment method categories
- Usage analytics and reporting
- Integration with sales system
- Payment gateway integration
- Audit logging for changes
