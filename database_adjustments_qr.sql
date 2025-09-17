-- Database adjustments for QR code functionality
-- Add order_id field to customer_cart table for better order tracking

ALTER TABLE `customer_cart` ADD COLUMN `order_id` VARCHAR(50) NULL AFTER `id`;

-- Add index for better performance
ALTER TABLE `customer_cart` ADD INDEX `idx_order_id` (`order_id`);
ALTER TABLE `customer_cart` ADD INDEX `idx_order_table` (`order_id`, `table_id`);

-- Update existing records to have order_id (optional - for existing data)
-- UPDATE `customer_cart` SET `order_id` = CONCAT('ORD', DATE_FORMAT(created_at, '%Y%m%d'), LPAD(id, 4, '0')) WHERE `order_id` IS NULL;
