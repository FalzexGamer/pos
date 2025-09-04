-- Create payment_methods table
CREATE TABLE `payment_methods` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Insert some sample payment methods
INSERT INTO `payment_methods` (`name`, `description`, `is_active`) VALUES
('Cash', 'Physical cash payment', 1),
('Credit Card', 'Payment via credit card', 1),
('Debit Card', 'Payment via debit card', 1),
('Bank Transfer', 'Direct bank transfer', 1),
('Mobile Payment', 'Payment via mobile apps (e.g., GrabPay, Touch n Go)', 1),
('E-Wallet', 'Digital wallet payment', 1),
('Check', 'Payment via check', 0),
('Gift Card', 'Payment via gift card or voucher', 1);

-- Add index for better performance
CREATE INDEX `idx_payment_methods_name` ON `payment_methods` (`name`);
CREATE INDEX `idx_payment_methods_status` ON `payment_methods` (`is_active`);
CREATE INDEX `idx_payment_methods_created` ON `payment_methods` (`created_at`);
