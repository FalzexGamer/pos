-- Create membership_tiers table
CREATE TABLE IF NOT EXISTS `membership_tiers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `discount_percentage` decimal(5,2) DEFAULT 0.00,
  `points_multiplier` decimal(5,2) DEFAULT 1.00,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create members table
CREATE TABLE IF NOT EXISTS `members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_code` varchar(50) NOT NULL UNIQUE,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20),
  `email` varchar(100),
  `address` text,
  `membership_tier_id` int(11),
  `is_active` tinyint(1) DEFAULT 1,
  `total_spent` decimal(10,2) DEFAULT 0.00,
  `total_points` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`membership_tier_id`) REFERENCES `membership_tiers`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample membership tiers
INSERT IGNORE INTO `membership_tiers` (`id`, `name`, `description`, `discount_percentage`, `points_multiplier`) VALUES
(1, 'Bronze', 'Basic membership tier', 0.00, 1.00),
(2, 'Silver', 'Mid-level membership tier', 5.00, 1.25),
(3, 'Gold', 'Premium membership tier', 10.00, 1.50),
(4, 'Platinum', 'VIP membership tier', 15.00, 2.00);

-- Insert sample members
INSERT IGNORE INTO `members` (`member_code`, `name`, `phone`, `email`, `address`, `membership_tier_id`, `is_active`, `total_spent`, `total_points`) VALUES
('MEM001', 'John Doe', '0123456789', 'john@example.com', '123 Main Street, Kuala Lumpur', 1, 1, 1500.00, 150),
('MEM002', 'Jane Smith', '0123456790', 'jane@example.com', '456 Oak Avenue, Petaling Jaya', 2, 1, 2500.00, 312),
('MEM003', 'Mike Johnson', '0123456791', 'mike@example.com', '789 Pine Road, Subang Jaya', 3, 1, 5000.00, 750),
('MEM004', 'Sarah Wilson', '0123456792', 'sarah@example.com', '321 Elm Street, Shah Alam', 4, 1, 8000.00, 1600),
('MEM005', 'David Brown', '0123456793', 'david@example.com', '654 Maple Drive, Klang', 2, 1, 1800.00, 225),
('MEM006', 'Lisa Davis', '0123456794', 'lisa@example.com', '987 Cedar Lane, Ampang', 3, 1, 3500.00, 525),
('MEM007', 'Tom Miller', '0123456795', 'tom@example.com', '147 Birch Court, Cheras', 1, 1, 800.00, 80),
('MEM008', 'Amy Garcia', '0123456796', 'amy@example.com', '258 Spruce Way, Damansara', 4, 1, 12000.00, 2400);
