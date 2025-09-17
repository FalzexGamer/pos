-- =============================================
-- DATABASE ADJUSTMENTS FOR CUSTOMER ORDERING SYSTEM
-- =============================================

-- 1. ADD ORDER NUMBER TO CUSTOMER_CART TABLE
-- This will help group items that belong to the same order
ALTER TABLE `customer_cart` 
ADD COLUMN `order_number` VARCHAR(50) NULL DEFAULT NULL AFTER `table_id`,
ADD COLUMN `order_id` INT NULL DEFAULT NULL AFTER `order_number`;

-- Add index for better performance
ALTER TABLE `customer_cart` 
ADD INDEX `idx_order_number` (`order_number`),
ADD INDEX `idx_order_id` (`order_id`);

-- 2. CREATE CUSTOMER_ORDERS TABLE (OPTIONAL - for better order management)
-- This table will store order-level information
CREATE TABLE IF NOT EXISTS `customer_orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_number` varchar(50) NOT NULL,
  `table_id` int NOT NULL DEFAULT '0',
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_phone` varchar(50) DEFAULT NULL,
  `customer_email` varchar(255) DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` enum('pending','confirmed','preparing','ready','served','paid','cancelled') NOT NULL DEFAULT 'pending',
  `payment_method` varchar(100) DEFAULT NULL,
  `payment_status` enum('pending','paid','refunded') NOT NULL DEFAULT 'pending',
  `notes` text,
  `estimated_ready_time` datetime DEFAULT NULL,
  `ready_time` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `table_id` (`table_id`),
  KEY `status` (`status`),
  KEY `payment_status` (`payment_status`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 3. UPDATE CUSTOMER_CART STATUS ENUM
-- Add more detailed status options
ALTER TABLE `customer_cart` 
MODIFY COLUMN `status` enum('active','ordered','preparing','ready','served','paid','cancelled','abandoned') NOT NULL DEFAULT 'active';

-- 4. ADD SPECIAL INSTRUCTIONS TO CUSTOMER_CART
-- For item-specific notes
ALTER TABLE `customer_cart` 
ADD COLUMN `special_instructions` TEXT NULL DEFAULT NULL AFTER `subtotal`;

-- 5. ADD ORDER TIMESTAMPS TO CUSTOMER_CART
-- Track when items move through different statuses
ALTER TABLE `customer_cart` 
ADD COLUMN `ordered_at` datetime NULL DEFAULT NULL AFTER `special_instructions`,
ADD COLUMN `preparing_at` datetime NULL DEFAULT NULL AFTER `ordered_at`,
ADD COLUMN `ready_at` datetime NULL DEFAULT NULL AFTER `preparing_at`,
ADD COLUMN `served_at` datetime NULL DEFAULT NULL AFTER `ready_at`;

-- 6. CREATE CUSTOMER_ORDER_ITEMS TABLE (ALTERNATIVE APPROACH)
-- If you want to separate order items from cart items
CREATE TABLE IF NOT EXISTS `customer_order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `sku` varchar(100) NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `unit_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `special_instructions` text,
  `status` enum('pending','preparing','ready','served','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 7. ADD FOREIGN KEY CONSTRAINTS (OPTIONAL)
-- For data integrity
-- ALTER TABLE `customer_orders` ADD CONSTRAINT `fk_customer_orders_table` FOREIGN KEY (`table_id`) REFERENCES `tables`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
-- ALTER TABLE `customer_order_items` ADD CONSTRAINT `fk_customer_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `customer_orders`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
-- ALTER TABLE `customer_order_items` ADD CONSTRAINT `fk_customer_order_items_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- 8. CREATE INDEXES FOR BETTER PERFORMANCE
CREATE INDEX `idx_customer_cart_table_status` ON `customer_cart` (`table_id`, `status`);
CREATE INDEX `idx_customer_cart_created_at` ON `customer_cart` (`created_at`);
CREATE INDEX `idx_customer_orders_table_status` ON `customer_orders` (`table_id`, `status`);
CREATE INDEX `idx_customer_orders_created_at` ON `customer_orders` (`created_at`);

-- 9. UPDATE EXISTING DATA (if any)
-- Set order_number for existing records
UPDATE `customer_cart` SET `order_number` = CONCAT('ORD', DATE_FORMAT(created_at, '%Y%m%d%H%i%s'), LPAD(table_id, 3, '0')) WHERE `order_number` IS NULL;

-- =============================================
-- VIEWS FOR EASIER QUERYING
-- =============================================

-- View for order summary
CREATE OR REPLACE VIEW `customer_order_summary` AS
SELECT 
    cc.order_number,
    cc.table_id,
    COUNT(*) as total_items,
    SUM(cc.subtotal) as subtotal,
    SUM(cc.subtotal) * 0.06 as tax_amount,
    SUM(cc.subtotal) * 1.06 as total_amount,
    MIN(cc.created_at) as order_created_at,
    MAX(cc.updated_at) as last_updated_at,
    GROUP_CONCAT(DISTINCT cc.status) as statuses,
    CASE 
        WHEN COUNT(DISTINCT cc.status) = 1 AND MAX(cc.status) = 'paid' THEN 'paid'
        WHEN COUNT(DISTINCT cc.status) = 1 AND MAX(cc.status) = 'cancelled' THEN 'cancelled'
        WHEN COUNT(DISTINCT cc.status) = 1 AND MAX(cc.status) = 'abandoned' THEN 'abandoned'
        ELSE 'in_progress'
    END as overall_status
FROM customer_cart cc
WHERE cc.order_number IS NOT NULL
GROUP BY cc.order_number, cc.table_id;

-- View for order details with products
CREATE OR REPLACE VIEW `customer_order_details` AS
SELECT 
    cc.id,
    cc.order_number,
    cc.table_id,
    cc.product_id,
    p.name as product_name,
    p.img as product_image,
    cc.sku,
    cc.quantity,
    cc.price,
    cc.subtotal,
    cc.status,
    cc.special_instructions,
    cc.created_at,
    cc.updated_at,
    c.name as category_name
FROM customer_cart cc
LEFT JOIN products p ON cc.product_id = p.id
LEFT JOIN categories c ON p.category_id = c.id
WHERE cc.order_number IS NOT NULL
ORDER BY cc.order_number, cc.created_at;
