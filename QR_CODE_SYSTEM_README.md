# QR Code Order System

## Overview
This system allows customers to place orders through a customer-facing interface and receive a receipt with a QR code. Staff can then scan this QR code at the POS to retrieve all order details and process the payment.

## Features

### Customer Side (`customer-order.php`)
- **Order Placement**: Customers can browse products by category and add items to their cart
- **QR Receipt**: After placing an order, customers receive a receipt with:
  - Order details (items, quantities, prices)
  - Total amount with tax calculation
  - Unique QR code containing all order information
- **Print Receipt**: Customers can print the receipt for their records

### Staff Side (`pos.php`)
- **QR Scanner**: Staff can scan customer QR codes using:
  - Camera scanner (for mobile devices)
  - Manual QR code data input
- **Order Loading**: Scanned orders are automatically loaded into the POS cart
- **Payment Processing**: Staff can then process payment for the loaded order

## How It Works

### 1. Customer Places Order
1. Customer visits `customer-order.php?table=X` (where X is the table number)
2. Customer browses products by category and adds items to cart
3. Customer clicks "Place Order"
4. System generates a unique order ID and creates QR code
5. Customer receives receipt with QR code

### 2. Staff Processes Order
1. Staff opens POS system (`pos.php`)
2. Staff clicks "Scan QR" button
3. Staff scans customer's QR code or manually enters QR data
4. System loads all order items into POS cart
5. Staff processes payment as normal

## Database Changes

### Required Database Updates
Run the following SQL to add QR code support:

```sql
-- Add order_id field to customer_cart table
ALTER TABLE `customer_cart` ADD COLUMN `order_id` VARCHAR(50) NULL AFTER `id`;

-- Add indexes for better performance
ALTER TABLE `customer_cart` ADD INDEX `idx_order_id` (`order_id`);
ALTER TABLE `customer_cart` ADD INDEX `idx_order_table` (`order_id`, `table_id`);
```

## File Structure

### New Files Created
- `ajax/submit-customer-order.php` - Handles customer order submission with QR generation
- `ajax/process-qr-order.php` - Processes scanned QR codes and loads orders
- `database_adjustments_qr.sql` - Database schema updates

### Modified Files
- `customer-order.php` - Added QR receipt display and print functionality
- `pos.php` - Added QR scanner modal and functionality

## QR Code Format

The QR code contains JSON data with the following structure:

```json
{
    "order_id": "ORD202412011234",
    "table_id": 5,
    "items": [
        {
            "product_id": 1,
            "name": "Product Name",
            "price": 10.00,
            "quantity": 2,
            "subtotal": 20.00
        }
    ],
    "subtotal": 20.00,
    "tax": 1.20,
    "total": 21.20,
    "timestamp": "2024-12-01 15:30:00",
    "status": "ordered"
}
```

## Usage Instructions

### For Customers
1. Visit the customer order page: `http://yoursite.com/customer-order.php?table=1`
2. Browse products by category
3. Add desired items to cart
4. Click "Place Order"
5. View and print receipt with QR code

### For Staff
1. Open POS system: `http://yoursite.com/pos.php`
2. Click "Scan QR" button
3. Either:
   - Use camera to scan QR code, or
   - Manually paste QR code data
4. Click "Process QR Code"
5. Confirm loading the order (this clears current cart)
6. Process payment as normal

## Security Considerations

- QR codes contain sensitive order information
- QR codes are generated server-side to prevent tampering
- Order verification is done against database records
- Staff confirmation required before loading orders

## Technical Details

### QR Code Generation
- Uses Google Charts API for QR code generation
- QR codes are 300x300 pixels for good scanability
- Contains complete order information for offline verification

### Camera Scanner
- Uses WebRTC getUserMedia API
- Automatically detects rear camera on mobile devices
- Includes fallback manual input option

### Order Processing
- Validates QR data against database records
- Prevents duplicate order loading
- Maintains order integrity and stock tracking

## Troubleshooting

### Common Issues

1. **QR Code Not Scanning**
   - Ensure good lighting
   - Hold camera steady
   - Try manual QR data input instead

2. **Order Not Found**
   - Verify QR code is from current system
   - Check if order was placed recently
   - Ensure database connection is working

3. **Camera Not Working**
   - Grant camera permissions
   - Use HTTPS (required for camera access)
   - Try manual QR data input

### Browser Compatibility
- Camera scanner: Modern browsers with WebRTC support
- Manual input: All browsers
- Mobile optimized for iOS and Android

## Future Enhancements

1. **Real QR Library Integration**
   - Replace basic camera detection with jsQR library
   - Automatic QR code detection from camera stream

2. **Order Status Updates**
   - Real-time order status updates
   - Push notifications for order completion

3. **Advanced Features**
   - Order modification after QR scan
   - Split bill functionality
   - Order history tracking

## Support

For technical support or feature requests, please refer to the main POS system documentation or contact the development team.
