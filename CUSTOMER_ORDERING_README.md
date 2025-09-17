# Customer Ordering System

This system allows customers to place orders directly through a web interface, designed for restaurant or retail environments with table-based ordering.

## Files Created

### Customer Interface
- **`customer-order.php`** - Main customer ordering page
  - Table-based ordering interface
  - Product browsing by categories
  - Shopping cart functionality
  - Order placement with confirmation

### Staff Management Interface
- **`customer-orders.php`** - Staff dashboard for managing customer orders
  - View all customer orders
  - Filter by status and table
  - Update order status (Active → Ordered → Paid)
  - Delete orders
  - Order details modal

### AJAX Endpoints
- **`ajax/submit-customer-order.php`** - Submit customer orders
- **`ajax/get-customer-orders.php`** - Retrieve orders for management
- **`ajax/update-customer-cart.php`** - Update cart items
- **`ajax/get-customer-cart.php`** - Get cart contents
- **`ajax/clear-customer-cart.php`** - Clear cart
- **`ajax/update-order-status.php`** - Update order status
- **`ajax/delete-customer-order.php`** - Delete orders
- **`ajax/get-order-details.php`** - Get detailed order information

## Database Structure

The system uses the existing `customer_cart` table with the following structure:
- `id` - Primary key
- `table_id` - Table number (for restaurant/cafe use)
- `product_id` - Product ID from products table
- `sku` - Product SKU
- `quantity` - Quantity ordered
- `price` - Unit price
- `subtotal` - Total for this line item
- `status` - Order status (active, ordered, paid, abandoned)
- `created_at` - Order creation timestamp
- `updated_at` - Last update timestamp

## How to Use

### For Customers
1. Access the ordering page: `customer-order.php?table=1` (replace 1 with table number)
2. Browse products by category
3. Add items to cart
4. Review order and place it
5. Receive confirmation

### For Staff
1. Access the management dashboard: `customer-orders.php`
2. View all customer orders
3. Filter by status or table number
4. Update order status as items are prepared and served
5. Mark orders as paid when payment is received

## Order Status Flow
1. **Active** - Customer is still adding items
2. **Ordered** - Order has been placed and sent to kitchen
3. **Paid** - Payment has been received
4. **Abandoned** - Order was cleared without payment

## Features

### Customer Features
- ✅ Responsive design for mobile and desktop
- ✅ Category-based product browsing
- ✅ Real-time cart updates
- ✅ Stock availability checking
- ✅ Order confirmation with success modal
- ✅ Toast notifications for user feedback

### Staff Features
- ✅ Real-time order monitoring
- ✅ Status management workflow
- ✅ Order filtering and search
- ✅ Detailed order information
- ✅ Summary statistics
- ✅ Automatic sales record creation when orders are paid

## Integration

The system integrates with the existing POS system:
- Uses existing product database
- Creates sales records when orders are paid
- Updates stock quantities automatically
- Records stock movements for inventory tracking
- Uses existing company settings for branding

## Customization

### Table Numbers
- Default table number is 1
- Pass `?table=X` parameter to set specific table number
- Table numbers are used for order organization and reporting

### Tax Rate
- Currently set to 6% tax rate
- Can be modified in the AJAX files if needed

### Styling
- Uses Tailwind CSS for modern, responsive design
- Font Awesome icons for visual elements
- Consistent with existing POS system styling

## Security Considerations

- Input validation and sanitization
- SQL injection protection
- XSS protection with proper escaping
- Transaction handling for data consistency
- Error handling and logging

## Browser Compatibility

- Modern browsers (Chrome, Firefox, Safari, Edge)
- Mobile responsive design
- Touch-friendly interface for tablets
- JavaScript required for full functionality
