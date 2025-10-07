-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 05, 2025 at 04:08 AM
-- Server version: 8.0.31
-- PHP Version: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pos`
--

-- --------------------------------------------------------

--
-- Stand-in structure for view `access_level_summary`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `access_level_summary`;
CREATE TABLE IF NOT EXISTS `access_level_summary` (
);

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
CREATE TABLE IF NOT EXISTS `cart` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL DEFAULT '0',
  `product_id` int NOT NULL DEFAULT '0',
  `sku` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '-',
  `quantity` int NOT NULL DEFAULT '1',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` enum('active','ordered','abandoned') DEFAULT 'active',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cart_user_status` (`user_id`,`status`),
  KEY `idx_cart_product` (`product_id`),
  KEY `idx_cart_sku` (`sku`),
  KEY `idx_cart_created` (`created_at`)
) ENGINE=MyISAM AUTO_INCREMENT=234 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `sku`, `quantity`, `price`, `subtotal`, `status`, `created_at`, `updated_at`) VALUES
(233, 1, 7, 'PROD007', 1, '900.00', '900.00', 'active', '2025-09-23 12:21:15', '2025-09-23 12:21:15'),
(232, 1, 4, 'PROD004', 1, '1200.00', '1200.00', 'active', '2025-09-23 12:21:13', '2025-09-23 12:21:13'),
(230, 1, 36, 'AUTO016', 1, '15.00', '15.00', 'ordered', '2025-09-18 16:37:56', '2025-09-18 16:38:10'),
(229, 1, 38, 'AUTO018', 1, '28.00', '28.00', 'ordered', '2025-09-18 16:37:56', '2025-09-18 16:38:10'),
(228, 1, 5, 'PROD005', 1, '120.00', '120.00', 'ordered', '2025-09-18 13:05:13', '2025-09-18 13:05:19'),
(227, 1, 11, 'PROD011', 1, '35.00', '35.00', 'ordered', '2025-09-18 13:05:13', '2025-09-18 13:05:19'),
(226, 1, 36, 'AUTO016', 2, '15.00', '30.00', 'ordered', '2025-09-18 13:01:12', '2025-09-18 13:01:18'),
(225, 1, 36, 'AUTO016', 2, '15.00', '30.00', 'ordered', '2025-09-18 12:59:01', '2025-09-18 12:59:06');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Electronics', 'Electronic devices and accessories', 1, '2025-08-17 02:55:52', '2025-08-17 02:55:52'),
(2, 'Clothing', 'Apparel and fashion items', 1, '2025-08-17 02:55:52', '2025-08-18 07:34:38'),
(3, 'Food & Beverages', 'Food and drink products', 1, '2025-08-17 02:55:52', '2025-08-17 02:55:52'),
(4, 'Home & Garden', 'Home improvement and garden items', 1, '2025-08-17 02:55:52', '2025-08-25 08:21:58'),
(5, 'Health & Beauty', 'Health and beauty products', 1, '2025-08-17 02:55:52', '2025-08-17 02:55:52'),
(6, 'Sports & Fitness', 'Sports equipment and fitness gear', 1, '2025-08-17 02:55:52', '2025-08-17 02:55:52'),
(7, 'Books & Stationery', 'Books, office supplies and stationery', 1, '2025-08-17 02:55:52', '2025-08-17 02:55:52'),
(8, 'Toys & Games', 'Children toys and games', 1, '2025-08-17 02:55:52', '2025-08-17 02:55:52'),
(9, 'Automotive', 'Car accessories and automotive parts', 1, '2025-08-17 02:55:52', '2025-08-17 02:55:52'),
(10, 'Pet Supplies', 'Pet food, toys and accessories', 1, '2025-08-17 02:55:52', '2025-08-17 02:55:52');

-- --------------------------------------------------------

--
-- Table structure for table `company_settings`
--

DROP TABLE IF EXISTS `company_settings`;
CREATE TABLE IF NOT EXISTS `company_settings` (
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
  `toyyibpay_secret_key` varchar(255) DEFAULT NULL,
  `toyyibpay_category_code` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `company_settings`
--

INSERT INTO `company_settings` (`id`, `company_name`, `address`, `phone`, `email`, `website`, `tax_number`, `currency`, `logo`, `created_at`, `updated_at`, `toyyibpay_secret_key`, `toyyibpay_category_code`) VALUES
(1, 'pos System', '123 Main Street, Kuala Lumpur', '+60 12-345 6789', 'info@possystem.com', '', '', 'USD', 'uploads/company/logo_1757382924.png', '2025-08-17 02:55:52', '2025-09-28 02:48:58', 'c3alqk05-mwhy-aib7-fya0-1lg7zaquxd57', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customer_cart`
--

DROP TABLE IF EXISTS `customer_cart`;
CREATE TABLE IF NOT EXISTS `customer_cart` (
  `id` int NOT NULL AUTO_INCREMENT,
  `table_id` int NOT NULL DEFAULT '0',
  `order_number` varchar(50) DEFAULT NULL,
  `order_id` int DEFAULT NULL,
  `product_id` int NOT NULL DEFAULT '0',
  `sku` varchar(150) NOT NULL DEFAULT '-',
  `quantity` int NOT NULL DEFAULT '1',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `special_instructions` text,
  `ordered_at` datetime DEFAULT NULL,
  `preparing_at` datetime DEFAULT NULL,
  `ready_at` datetime DEFAULT NULL,
  `served_at` datetime DEFAULT NULL,
  `status` enum('active','ordered','preparing','ready','served','paid','cancelled','abandoned') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `payment_bill_code` varchar(255) DEFAULT NULL,
  `payment_status` enum('pending','completed','failed','cancelled') DEFAULT NULL,
  `payment_reference` varchar(255) DEFAULT NULL,
  `payment_amount` decimal(10,2) DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `table_id` (`table_id`),
  KEY `product_id` (`product_id`),
  KEY `sku` (`sku`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`),
  KEY `idx_order_number` (`order_number`),
  KEY `idx_order_id` (`order_id`),
  KEY `idx_customer_cart_table_status` (`table_id`,`status`),
  KEY `idx_customer_cart_created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `customer_cart`
--

INSERT INTO `customer_cart` (`id`, `table_id`, `order_number`, `order_id`, `product_id`, `sku`, `quantity`, `price`, `subtotal`, `special_instructions`, `ordered_at`, `preparing_at`, `ready_at`, `served_at`, `status`, `created_at`, `updated_at`, `payment_bill_code`, `payment_status`, `payment_reference`, `payment_amount`, `payment_date`) VALUES
(25, 10, NULL, 0, 36, 'AUTO016', 1, '15.00', '15.00', NULL, NULL, NULL, NULL, NULL, 'ordered', '2025-09-18 10:38:04', '2025-09-18 10:38:04', NULL, NULL, NULL, NULL, NULL),
(28, 10, 'ORD20250918024725', 0, 36, 'AUTO016', 1, '15.00', '15.00', NULL, NULL, NULL, NULL, NULL, 'ordered', '2025-09-18 10:47:38', '2025-09-18 10:47:38', NULL, NULL, NULL, NULL, NULL),
(29, 10, 'ORD20250918024725', 0, 25, 'AUTO005', 2, '28.00', '56.00', NULL, NULL, NULL, NULL, NULL, 'ordered', '2025-09-18 10:47:38', '2025-09-18 10:47:38', NULL, NULL, NULL, NULL, NULL),
(30, 10, 'ORD20250918024725', 0, 23, 'AUTO003', 1, '150.00', '150.00', NULL, NULL, NULL, NULL, NULL, 'ordered', '2025-09-18 10:47:38', '2025-09-30 11:03:48', NULL, NULL, NULL, NULL, NULL),
(31, 10, 'ORD20250918030647', 0, 36, 'AUTO016', 1, '15.00', '15.00', NULL, NULL, NULL, NULL, NULL, 'ordered', '2025-09-18 11:06:15', '2025-09-18 11:06:15', NULL, NULL, NULL, NULL, NULL),
(32, 10, 'ORD20250918030647', 0, 23, 'AUTO003', 1, '140.00', '140.00', NULL, NULL, NULL, NULL, NULL, 'ordered', '2025-09-18 11:06:15', '2025-09-18 11:06:15', NULL, NULL, NULL, NULL, NULL),
(33, 10, 'ORD20250918030647', 0, 25, 'AUTO005', 1, '28.00', '28.00', NULL, NULL, NULL, NULL, NULL, 'ordered', '2025-09-18 11:06:15', '2025-09-18 11:06:15', NULL, NULL, NULL, NULL, NULL),
(34, 10, 'ORD20250918031094', 0, 36, 'AUTO016', 1, '15.00', '15.00', NULL, NULL, NULL, NULL, NULL, 'ordered', '2025-09-18 11:10:43', '2025-09-18 11:10:43', NULL, NULL, NULL, NULL, NULL),
(35, 10, 'ORD20250918031094', 0, 21, 'AUTO001', 1, '6.00', '6.00', NULL, NULL, NULL, NULL, NULL, 'ordered', '2025-09-18 11:10:43', '2025-09-18 11:10:43', NULL, NULL, NULL, NULL, NULL),
(36, 10, 'ORD20250918031094', 0, 34, 'AUTO014', 1, '110.00', '110.00', NULL, NULL, NULL, NULL, NULL, 'ordered', '2025-09-18 11:10:43', '2025-09-18 11:10:43', NULL, NULL, NULL, NULL, NULL),
(37, 10, 'ORD20250918045651', 0, 16, 'PROD016', 2, '100.00', '200.00', NULL, NULL, NULL, NULL, NULL, 'ordered', '2025-09-18 12:56:35', '2025-09-18 12:56:35', NULL, NULL, NULL, NULL, NULL),
(38, 10, 'ORD20250918045651', 0, 1, 'PROD001', 1, '4000.00', '4000.00', NULL, NULL, NULL, NULL, NULL, 'ordered', '2025-09-18 12:56:35', '2025-09-18 12:56:35', NULL, NULL, NULL, NULL, NULL),
(39, 10, 'ORD20250918045651', 0, 4, 'PROD004', 1, '1200.00', '1200.00', NULL, NULL, NULL, NULL, NULL, 'ordered', '2025-09-18 12:56:35', '2025-09-18 12:56:35', NULL, NULL, NULL, NULL, NULL),
(40, 10, 'ORD20250918045884', 0, 36, 'AUTO016', 2, '15.00', '30.00', NULL, NULL, NULL, NULL, NULL, 'ordered', '2025-09-18 12:58:12', '2025-09-18 12:58:12', NULL, NULL, NULL, NULL, NULL),
(41, 10, 'ORD20250918050459', 0, 11, 'PROD011', 1, '35.00', '35.00', NULL, NULL, NULL, NULL, NULL, 'ordered', '2025-09-18 13:04:51', '2025-09-18 13:04:51', NULL, NULL, NULL, NULL, NULL),
(42, 10, 'ORD20250918050459', 0, 5, 'PROD005', 1, '120.00', '120.00', NULL, NULL, NULL, NULL, NULL, 'ordered', '2025-09-18 13:04:51', '2025-09-18 13:04:51', NULL, NULL, NULL, NULL, NULL),
(43, 1, 'ORD20250918083720', 0, 36, 'AUTO016', 1, '15.00', '15.00', NULL, NULL, NULL, NULL, NULL, 'ordered', '2025-09-18 16:37:28', '2025-09-18 16:37:28', NULL, NULL, NULL, NULL, NULL),
(44, 1, 'ORD20250918083720', 0, 38, 'AUTO018', 1, '28.00', '28.00', NULL, NULL, NULL, NULL, NULL, 'ordered', '2025-09-18 16:37:28', '2025-09-18 16:37:28', NULL, NULL, NULL, NULL, NULL),
(45, 1, 'ORD20250918084057', 0, 36, 'AUTO016', 1, '15.00', '15.00', NULL, NULL, NULL, NULL, NULL, 'ordered', '2025-09-18 16:40:37', '2025-09-18 16:40:37', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customer_orders`
--

DROP TABLE IF EXISTS `customer_orders`;
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
  KEY `created_at` (`created_at`),
  KEY `idx_customer_orders_table_status` (`table_id`,`status`),
  KEY `idx_customer_orders_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `customer_order_details`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `customer_order_details`;
CREATE TABLE IF NOT EXISTS `customer_order_details` (
`category_name` varchar(100)
,`created_at` datetime
,`id` int
,`order_number` varchar(50)
,`price` decimal(10,2)
,`product_id` int
,`product_image` varchar(150)
,`product_name` varchar(255)
,`quantity` int
,`sku` varchar(150)
,`special_instructions` text
,`status` enum('active','ordered','preparing','ready','served','paid','cancelled','abandoned')
,`subtotal` decimal(10,2)
,`table_id` int
,`updated_at` datetime
);

-- --------------------------------------------------------

--
-- Table structure for table `customer_order_items`
--

DROP TABLE IF EXISTS `customer_order_items`;
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

-- --------------------------------------------------------

--
-- Stand-in structure for view `customer_order_summary`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `customer_order_summary`;
CREATE TABLE IF NOT EXISTS `customer_order_summary` (
`last_updated_at` datetime
,`order_created_at` datetime
,`order_number` varchar(50)
,`overall_status` varchar(11)
,`statuses` text
,`subtotal` decimal(32,2)
,`table_id` int
,`tax_amount` decimal(35,4)
,`total_amount` decimal(35,4)
,`total_items` bigint
);

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

DROP TABLE IF EXISTS `members`;
CREATE TABLE IF NOT EXISTS `members` (
  `id` int NOT NULL AUTO_INCREMENT,
  `member_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '-',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '-',
  `phone` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT '-',
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT '-',
  `address` text,
  `membership_tier_id` int DEFAULT '0',
  `total_points` int DEFAULT '0',
  `total_spent` decimal(10,2) DEFAULT '0.00',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `member_code` (`member_code`),
  KEY `membership_tier_id` (`membership_tier_id`),
  KEY `idx_members_code` (`member_code`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `member_code`, `name`, `phone`, `email`, `address`, `membership_tier_id`, `total_points`, `total_spent`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'asa', 'sad', 'dad', 'fieas', 'xad', 2, 0, '16142.21', 1, '2025-08-17 04:22:05', '2025-08-20 02:43:00'),
(9, 'MEM900807808', 'FIRDAUS', '', '', '', 0, 0, '0.00', 0, '2025-08-27 08:21:51', '2025-08-27 08:21:51');

-- --------------------------------------------------------

--
-- Table structure for table `membership_tiers`
--

DROP TABLE IF EXISTS `membership_tiers`;
CREATE TABLE IF NOT EXISTS `membership_tiers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `discount_percentage` decimal(5,2) DEFAULT '0.00',
  `description` text,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `membership_tiers`
--

INSERT INTO `membership_tiers` (`id`, `name`, `discount_percentage`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Regular', '0.00', 'Standard membership', 1, '2025-08-17 02:55:52', '2025-08-17 02:55:52'),
(2, 'Gold', '5.00', 'Gold membership with 5% discount', 1, '2025-08-17 02:55:52', '2025-08-17 02:55:52'),
(3, 'Platinum', '10.00', 'Platinum membership with 10% discount', 1, '2025-08-17 02:55:52', '2025-08-17 02:55:52');

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

DROP TABLE IF EXISTS `payment_methods`;
CREATE TABLE IF NOT EXISTS `payment_methods` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '-',
  `description` text,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Cash', 'Cash payment method', 1, '2025-08-29 06:34:52', '2025-09-07 03:13:05'),
(2, 'Credit Card', 'Credit card payment method', 0, '2025-08-29 06:34:52', '2025-09-07 03:11:47'),
(3, 'Debit Card', 'Debit card payment method', 1, '2025-08-29 06:34:52', '2025-08-29 06:34:52'),
(4, 'E-Wallet', 'Electronic wallet payment method', 1, '2025-08-29 06:34:52', '2025-08-29 06:34:52'),
(5, 'Bank Transfer', 'Bank transfer payment method', 1, '2025-08-29 06:34:52', '2025-09-06 16:07:45'),
(6, 'Mobile Money', 'Mobile money payment (M-Pesa, etc.)', 1, '2025-09-03 08:08:32', '2025-09-03 08:08:32'),
(7, 'Check', 'Check payment', 1, '2025-09-03 08:08:32', '2025-09-03 08:08:32'),
(8, 'Gift Card', 'Gift card or voucher payment', 1, '2025-09-03 08:08:32', '2025-09-07 01:14:14'),
(19, 'ADMI', '', 1, '2025-09-08 04:42:38', '2025-09-09 01:46:33');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sku` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '-',
  `barcode` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT '-',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '-',
  `description` text,
  `category_id` int DEFAULT '0',
  `supplier_id` int DEFAULT '0',
  `uom_id` int DEFAULT '0',
  `cost_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `selling_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stock_quantity` int DEFAULT '0',
  `min_stock_level` int DEFAULT '0',
  `max_stock_level` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `img` varchar(150) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`),
  UNIQUE KEY `barcode` (`barcode`),
  KEY `category_id` (`category_id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `uom_id` (`uom_id`),
  KEY `idx_products_sku` (`sku`),
  KEY `idx_products_barcode` (`barcode`)
) ENGINE=MyISAM AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `sku`, `barcode`, `name`, `description`, `category_id`, `supplier_id`, `uom_id`, `cost_price`, `selling_price`, `stock_quantity`, `min_stock_level`, `max_stock_level`, `is_active`, `created_at`, `updated_at`, `img`) VALUES
(1, 'PROD001', '1234567890123', 'Dell Laptop XPS 13', 'High-performance laptop with Intel i7 processor', 1, 1, 1, '2500.00', '4000.00', 4, 2, 50, 1, '2025-08-17 02:55:52', '2025-09-18 04:56:35', '_1757570029.png'),
(2, 'PROD002', '1234567890124', 'Cotton T-Shirt', 'Premium cotton t-shirt in various colors', 2, 2, 1, '15.00', '25.00', 41, 10, 100, 1, '2025-08-17 02:55:52', '2025-09-11 06:38:33', 'asa_1757570041.png'),
(3, 'PROD003', '1234567890125', 'Premium Rice 5kg', 'High-quality jasmine rice 5kg bag', 3, 1, 2, '20.00', '30.00', 25, 20, 200, 1, '2025-08-17 02:55:52', '2025-09-11 06:45:51', 'asa_1757570041.png'),
(4, 'PROD004', '1234567890126', 'iPhone 15 Pro', 'Latest iPhone with advanced camera system', 1, 1, 1, '800.00', '1200.00', 7, 3, 30, 1, '2025-08-18 07:11:03', '2025-09-18 04:56:35', 'asa_1757570041.png'),
(5, 'PROD005', '1234567890127', 'Nike Air Max', 'Comfortable running shoes', 6, 2, 1, '80.00', '120.00', 13, 5, 50, 1, '2025-08-18 07:12:00', '2025-09-18 05:05:19', 'asa_1757570041.png'),
(6, 'PROD006', '1234567890128', 'Organic Coffee Beans', 'Premium organic coffee beans 500g', 3, 1, 2, '15.00', '25.00', 30, 10, 100, 1, '2025-08-18 07:13:00', '2025-09-11 06:45:54', 'asa_1757570041.png'),
(7, 'PROD007', '1234567890129', 'Samsung 55\" Smart TV', '4K Ultra HD Smart TV with HDR', 1, 1, 1, '600.00', '900.00', 3, 2, 20, 1, '2025-08-18 07:14:00', '2025-09-11 06:45:54', 'asa_1757570041.png'),
(8, 'PROD008', '1234567890130', 'Denim Jeans', 'Classic blue denim jeans', 2, 2, 1, '25.00', '45.00', 20, 8, 80, 1, '2025-08-18 07:15:00', '2025-09-11 06:45:54', 'asa_1757570041.png'),
(9, 'PROD009', '1234567890131', 'Kitchen Knife Set', 'Professional 6-piece knife set', 4, 1, 1, '40.00', '70.00', 12, 5, 40, 1, '2025-08-18 07:16:00', '2025-09-11 06:45:54', 'asa_1757570041.png'),
(10, 'PROD010', '1234567890132', 'Shampoo & Conditioner', 'Moisturizing hair care set', 5, 2, 1, '8.00', '15.00', 25, 10, 100, 1, '2025-08-18 07:17:00', '2025-09-11 06:45:54', 'asa_1757570041.png'),
(11, 'PROD011', '1234567890133', 'Yoga Mat', 'Non-slip premium yoga mat', 6, 2, 1, '20.00', '35.00', 16, 5, 50, 1, '2025-08-18 07:18:00', '2025-09-18 05:05:19', 'asa_1757570041.png'),
(12, 'PROD012', '1234567890134', 'Notebook Set', 'Premium A4 notebooks 5-pack', 7, 1, 1, '12.00', '20.00', 35, 15, 150, 1, '2025-08-18 07:19:00', '2025-09-11 06:45:54', 'asa_1757570041.png'),
(13, 'PROD013', '1234567890135', 'LEGO Building Set', 'Creative building blocks for kids', 8, 2, 1, '30.00', '50.00', 22, 8, 60, 1, '2025-08-18 07:20:00', '2025-09-11 06:45:54', 'asa_1757570041.png'),
(14, 'PROD014', '1234567890136', 'Car Air Freshener', 'Long-lasting car air freshener', 9, 1, 1, '3.00', '6.00', 37, 20, 200, 1, '2025-08-18 07:21:00', '2025-09-18 02:41:08', 'asa_1757570041.png'),
(15, 'PROD015', '1234567890137', 'Dog Food 10kg', 'Premium dry dog food', 10, 2, 2, '35.00', '55.00', 16, 5, 40, 1, '2025-08-18 07:22:00', '2025-09-11 06:45:54', 'asa_1757570041.png'),
(16, 'PROD016', '1234567890138', 'Wireless Headphones', 'Noise-cancelling wireless headphones', 1, 1, 1, '60.00', '100.00', 12, 5, 50, 1, '2025-08-18 07:23:00', '2025-09-18 04:56:35', 'asa_1757570041.png'),
(17, 'PROD017', '1234567890139', 'Hoodie Sweatshirt', 'Comfortable cotton hoodie', 2, 2, 1, '22.00', '40.00', 28, 10, 80, 1, '2025-08-18 07:24:00', '2025-09-11 06:45:54', 'asa_1757570041.png'),
(18, 'PROD018', '1234567890140', 'Fresh Milk 1L', 'Fresh whole milk 1 liter', 3, 1, 3, '2.50', '4.00', 40, 20, 200, 1, '2025-08-18 07:25:00', '2025-09-11 06:45:54', 'asa_1757570041.png'),
(19, 'PROD019', '1234567890141', 'Garden Hose', 'Flexible garden hose 50ft', 4, 1, 1, '25.00', '45.00', 8, 3, 25, 1, '2025-08-18 07:26:00', '2025-09-11 06:45:54', 'asa_1757570041.png'),
(20, 'PROD020', '1234567890142', 'Face Cream', 'Anti-aging face cream 50ml', 5, 2, 1, '18.00', '32.00', 20, 8, 60, 1, '2025-08-18 07:27:00', '2025-09-11 06:45:54', 'asa_1757570041.png'),
(21, 'AUTO001', '2000000000001', 'Car Air Freshener', 'Long-lasting car air freshener', 9, 1, 1, '3.00', '6.00', 47, 20, 200, 1, '2025-08-18 08:00:00', '2025-09-18 03:10:43', 'asa_1757570041.png'),
(22, 'AUTO002', '2000000000002', 'Engine Oil 5W-30', 'Premium synthetic engine oil 5L', 9, 1, 3, '25.00', '45.00', 30, 10, 100, 1, '2025-08-18 08:01:00', '2025-09-11 06:45:54', 'asa_1757570041.png'),
(23, 'AUTO003', '2000000000003', 'Car Battery', '12V 60Ah car battery', 9, 2, 1, '80.00', '140.00', 6, 5, 30, 1, '2025-08-18 08:02:00', '2025-09-18 03:06:15', 'asa_1757570041.png'),
(24, 'AUTO004', '2000000000004', 'Tire Pressure Gauge', 'Digital tire pressure gauge', 9, 1, 1, '8.00', '15.00', 25, 10, 80, 1, '2025-08-18 08:03:00', '2025-09-11 06:45:54', 'asa_1757570041.png'),
(25, 'AUTO005', '2000000000005', 'Car Floor Mats', 'Rubber car floor mats set of 4', 9, 2, 1, '15.00', '28.00', 12, 8, 60, 1, '2025-08-18 08:04:00', '2025-09-18 03:06:15', 'asa_1757570041.png'),
(26, 'AUTO006', '2000000000006', 'Windshield Wiper Blades', 'Premium windshield wiper blades pair', 9, 1, 1, '12.00', '22.00', 35, 15, 100, 1, '2025-08-18 08:05:00', '2025-09-11 06:45:54', 'asa_1757570041.png'),
(27, 'AUTO007', '2000000000007', 'Car Phone Mount', 'Magnetic car phone mount', 9, 2, 1, '6.00', '12.00', 37, 20, 150, 1, '2025-08-18 08:06:00', '2025-09-18 02:34:48', 'asa_1757570041.png'),
(28, 'AUTO008', '2000000000008', 'Car Charger USB', 'Dual USB car charger 2.4A', 9, 1, 1, '4.00', '8.00', 60, 25, 200, 1, '2025-08-18 08:07:00', '2025-09-11 06:45:54', 'asa_1757570041.png'),
(29, 'AUTO009', '2000000000009', 'Car Seat Covers', 'Neoprene car seat covers set', 9, 2, 1, '35.00', '65.00', 15, 5, 40, 1, '2025-08-18 08:08:00', '2025-09-11 06:45:54', 'asa_1757570041.png'),
(30, 'AUTO010', '2000000000010', 'Car Wax Polish', 'Premium car wax polish 500ml', 9, 1, 1, '18.00', '32.00', 22, 8, 70, 1, '2025-08-18 08:09:00', '2025-09-11 06:45:54', 'asa_1757570041.png'),
(31, 'AUTO011', '2000000000011', 'Jump Starter Pack', 'Portable car jump starter 10000mAh', 9, 2, 1, '45.00', '80.00', 8, 3, 25, 1, '2025-08-18 08:10:00', '2025-09-11 06:45:54', 'asa_1757570041.png'),
(32, 'AUTO012', '2000000000012', 'Car Vacuum Cleaner', '12V portable car vacuum cleaner', 9, 1, 1, '25.00', '45.00', 18, 6, 50, 1, '2025-08-18 08:11:00', '2025-09-11 06:45:54', 'asa_1757570041.png'),
(33, 'AUTO013', '2000000000013', 'Tire Repair Kit', 'Complete tire repair kit with tools', 9, 2, 1, '20.00', '35.00', 16, 5, 40, 1, '2025-08-18 08:12:00', '2025-09-11 06:45:54', 'asa_1757570041.png'),
(34, 'AUTO014', '2000000000014', 'Car Dashboard Camera', 'HD 1080p car dash cam', 9, 1, 1, '60.00', '110.00', 9, 4, 30, 1, '2025-08-18 08:13:00', '2025-09-18 03:10:43', 'asa_1757570041.png'),
(35, 'AUTO015', '2000000000015', 'Car Sunshade', 'Foldable car sunshade windshield', 9, 2, 1, '12.00', '22.00', 28, 10, 80, 1, '2025-08-18 08:14:00', '2025-09-11 06:45:54', 'asa_1757570041.png'),
(36, 'AUTO016', '2000000000016', 'Brake Fluid DOT 4', 'High-performance brake fluid 1L', 9, 1, 3, '8.00', '15.00', 0, 12, 100, 1, '2025-08-18 08:15:00', '2025-09-18 08:40:37', 'asa_1757570041.png'),
(37, 'AUTO017', '2000000000017', 'Car Emergency Kit', 'Complete car emergency kit', 9, 2, 1, '30.00', '55.00', 14, 5, 35, 1, '2025-08-18 08:16:00', '2025-09-11 06:45:54', 'asa_1757570041.png'),
(38, 'AUTO018', '2000000000018', 'Car Air Filter', 'High-flow air filter replacement', 9, 1, 1, '15.00', '28.00', 4, 8, 60, 1, '2025-08-18 08:17:00', '2025-09-18 08:38:10', 'asa_1757570041.png'),
(39, 'AUTO019', '2000000000019', 'Car LED Headlight Bulbs', 'Ultra-bright LED headlight bulbs pair', 9, 2, 1, '35.00', '65.00', 6, 4, 30, 1, '2025-08-18 08:18:00', '2025-09-18 02:34:48', 'asa_1757570041.png'),
(40, 'AUTO020', '2000000000020', 'Car Trunk Organizer', 'Collapsible car trunk organizer', 9, 1, 1, '22.00', '40.00', 18, 6, 45, 1, '2025-08-18 08:19:00', '2025-09-11 06:45:54', 'asa_1757570041.png');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
CREATE TABLE IF NOT EXISTS `sales` (
  `id` int NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '-',
  `session_id` int DEFAULT '0',
  `member_id` int DEFAULT '0',
  `user_id` int NOT NULL DEFAULT '0',
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(10,2) DEFAULT '0.00',
  `tax_amount` decimal(10,2) DEFAULT '0.00',
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payment_method` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'cash',
  `payment_method_id` int DEFAULT '0',
  `payment_status` enum('paid','pending','refunded') DEFAULT 'paid',
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_number` (`invoice_number`),
  KEY `session_id` (`session_id`),
  KEY `member_id` (`member_id`),
  KEY `user_id` (`user_id`),
  KEY `idx_sales_invoice` (`invoice_number`),
  KEY `idx_sales_date` (`created_at`),
  KEY `fk_sales_payment_method` (`payment_method_id`)
) ENGINE=MyISAM AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `invoice_number`, `session_id`, `member_id`, `user_id`, `subtotal`, `discount_amount`, `tax_amount`, `total_amount`, `payment_method`, `payment_method_id`, `payment_status`, `notes`, `created_at`, `updated_at`) VALUES
(68, '202509180010434', 10, NULL, 1, '155.00', '0.00', '9.30', '164.30', 'cash', NULL, 'paid', NULL, '2025-09-18 05:05:19', '2025-09-18 05:05:19'),
(69, '202509180010484', 10, NULL, 1, '43.00', '0.00', '2.58', '45.58', 'cash', NULL, 'paid', NULL, '2025-09-18 08:38:10', '2025-09-18 08:38:10'),
(66, '202509180010277', 10, NULL, 1, '30.00', '0.00', '1.80', '31.80', 'cash', NULL, 'paid', NULL, '2025-09-18 04:59:05', '2025-09-18 04:59:05'),
(67, '202509180010144', 10, NULL, 1, '30.00', '0.00', '1.80', '31.80', 'cash', NULL, 'paid', NULL, '2025-09-18 05:01:18', '2025-09-18 05:01:18'),
(65, '202509180010247', 10, NULL, 1, '781.00', '0.00', '46.86', '827.86', 'cash', NULL, 'paid', NULL, '2025-09-18 02:34:48', '2025-09-18 02:34:48'),
(64, '202509180010692', 10, NULL, 1, '373.00', '0.00', '22.38', '395.38', 'E-Wallet', 4, 'paid', NULL, '2025-09-18 02:27:29', '2025-09-18 02:27:29');

-- --------------------------------------------------------

--
-- Table structure for table `sales_sessions`
--

DROP TABLE IF EXISTS `sales_sessions`;
CREATE TABLE IF NOT EXISTS `sales_sessions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL DEFAULT '0',
  `session_start` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `session_end` timestamp NULL DEFAULT NULL,
  `opening_amount` decimal(10,2) DEFAULT '0.00',
  `closing_amount` decimal(10,2) DEFAULT '0.00',
  `total_sales` decimal(10,2) DEFAULT '0.00',
  `total_refunds` decimal(10,2) DEFAULT '0.00',
  `status` enum('open','closed') DEFAULT 'open',
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sales_sessions`
--

INSERT INTO `sales_sessions` (`id`, `user_id`, `session_start`, `session_end`, `opening_amount`, `closing_amount`, `total_sales`, `total_refunds`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(11, 2, '2025-09-10 01:40:06', NULL, '0.00', '0.00', '0.00', '0.00', 'open', NULL, '2025-09-10 01:40:06', '2025-09-10 01:40:06'),
(10, 1, '2025-09-02 02:26:35', NULL, '1000.00', '0.00', '14206.12', '901.00', 'open', '', '2025-09-02 02:26:35', '2025-09-18 08:38:10'),
(13, 1523, '2025-09-22 06:30:36', NULL, '0.00', '0.00', '0.00', '0.00', 'open', NULL, '2025-09-22 06:30:36', '2025-09-22 06:30:36');

-- --------------------------------------------------------

--
-- Table structure for table `sale_items`
--

DROP TABLE IF EXISTS `sale_items`;
CREATE TABLE IF NOT EXISTS `sale_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sale_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) DEFAULT '0.00',
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `sale_id` (`sale_id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=92 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sale_items`
--

INSERT INTO `sale_items` (`id`, `sale_id`, `product_id`, `quantity`, `unit_price`, `discount_amount`, `total_price`, `created_at`) VALUES
(1, 1, 3, 2, '30.00', '0.00', '60.00', '2025-08-17 03:12:00'),
(2, 2, 3, 2, '30.00', '0.00', '60.00', '2025-08-17 03:31:07'),
(3, 3, 1, 1, '4000.00', '0.00', '4000.00', '2025-08-17 06:43:34'),
(4, 4, 2, 1, '25.00', '0.00', '25.00', '2025-08-17 08:10:20'),
(5, 4, 1, 2, '4000.00', '0.00', '8000.00', '2025-08-17 08:10:20'),
(6, 5, 1, 2, '4000.00', '0.00', '8000.00', '2025-08-18 08:20:28'),
(7, 5, 3, 1, '30.00', '0.00', '30.00', '2025-08-18 08:20:28'),
(8, 6, 1, 2, '4000.00', '0.00', '8000.00', '2025-08-20 02:43:00'),
(9, 7, 1, 2, '4000.00', '0.00', '8000.00', '2025-08-21 01:36:24'),
(10, 7, 2, 1, '25.00', '0.00', '25.00', '2025-08-21 01:36:24'),
(11, 7, 3, 1, '30.00', '0.00', '30.00', '2025-08-21 01:36:24'),
(12, 8, 4, 1, '200.00', '0.00', '200.00', '2025-08-21 02:10:48'),
(13, 8, 1, 3, '4000.00', '0.00', '12000.00', '2025-08-21 02:10:48'),
(14, 8, 2, 1, '25.00', '0.00', '25.00', '2025-08-21 02:10:48'),
(15, 9, 3, 1, '30.00', '0.00', '30.00', '2025-08-25 06:55:40'),
(16, 10, 6, 1, '200.00', '0.00', '200.00', '2025-08-28 16:23:53'),
(17, 11, 6, 1, '200.00', '0.00', '200.00', '2025-08-29 02:22:00'),
(18, 12, 6, 1, '200.00', '0.00', '200.00', '2025-08-29 02:22:39'),
(19, 13, 2, 4, '25.00', '0.00', '100.00', '2025-08-29 02:23:18'),
(20, 14, 6, 1, '200.00', '0.00', '200.00', '2025-08-29 06:23:30'),
(21, 15, 2, 1, '25.00', '0.00', '25.00', '2025-09-02 02:32:20'),
(22, 16, 6, 1, '200.00', '0.00', '200.00', '2025-09-02 02:37:40'),
(23, 17, 4, 1, '200.00', '0.00', '200.00', '2025-09-02 05:01:51'),
(24, 18, 6, 1, '200.00', '0.00', '200.00', '2025-09-02 05:04:58'),
(25, 19, 6, 1, '200.00', '0.00', '200.00', '2025-09-03 06:46:47'),
(26, 20, 6, 1, '200.00', '0.00', '200.00', '2025-09-03 06:53:03'),
(27, 21, 3, 1, '30.00', '0.00', '30.00', '2025-09-03 06:53:50'),
(28, 22, 6, 1, '200.00', '0.00', '200.00', '2025-09-03 06:54:37'),
(29, 23, 4, 1, '200.00', '0.00', '200.00', '2025-09-03 07:01:45'),
(30, 24, 4, 1, '200.00', '0.00', '200.00', '2025-09-03 07:02:26'),
(31, 25, 4, 1, '200.00', '0.00', '200.00', '2025-09-03 07:05:44'),
(32, 26, 4, 2, '200.00', '0.00', '400.00', '2025-09-03 07:09:37'),
(33, 27, 4, 1, '200.00', '0.00', '200.00', '2025-09-03 07:17:17'),
(34, 28, 4, 1, '200.00', '0.00', '200.00', '2025-09-03 07:18:50'),
(35, 29, 4, 1, '200.00', '0.00', '200.00', '2025-09-03 07:19:56'),
(36, 30, 4, 1, '200.00', '0.00', '200.00', '2025-09-03 07:20:19'),
(37, 31, 4, 1, '200.00', '0.00', '200.00', '2025-09-03 07:21:54'),
(38, 32, 4, 1, '200.00', '0.00', '200.00', '2025-09-03 08:14:24'),
(39, 32, 3, 1, '30.00', '0.00', '30.00', '2025-09-03 08:14:24'),
(40, 33, 3, 1, '30.00', '0.00', '30.00', '2025-09-03 08:14:58'),
(41, 33, 6, 1, '200.00', '0.00', '200.00', '2025-09-03 08:14:58'),
(42, 34, 4, 1, '200.00', '0.00', '200.00', '2025-09-03 08:18:35'),
(43, 35, 4, 1, '200.00', '0.00', '200.00', '2025-09-03 08:19:05'),
(44, 36, 4, 1, '200.00', '0.00', '200.00', '2025-09-03 08:19:27'),
(45, 37, 3, 1, '30.00', '0.00', '30.00', '2025-09-04 03:05:51'),
(46, 38, 4, 1, '200.00', '0.00', '200.00', '2025-09-04 04:08:58'),
(47, 39, 4, 1, '200.00', '0.00', '200.00', '2025-09-04 04:20:00'),
(48, 40, 4, 1, '200.00', '0.00', '200.00', '2025-09-04 04:36:43'),
(49, 41, 4, 1, '200.00', '0.00', '200.00', '2025-09-06 15:49:32'),
(50, 42, 4, 1, '200.00', '0.00', '200.00', '2025-09-06 15:52:50'),
(51, 43, 4, 1, '200.00', '0.00', '200.00', '2025-09-06 15:55:58'),
(52, 44, 4, 1, '200.00', '0.00', '200.00', '2025-09-06 16:02:22'),
(53, 45, 4, 3, '200.00', '0.00', '600.00', '2025-09-06 16:06:16'),
(54, 46, 4, 1, '200.00', '0.00', '200.00', '2025-09-06 16:13:55'),
(55, 47, 4, 1, '200.00', '0.00', '200.00', '2025-09-07 03:10:53'),
(56, 48, 4, 1, '200.00', '0.00', '200.00', '2025-09-07 03:12:28'),
(57, 49, 4, 2, '200.00', '0.00', '400.00', '2025-09-07 07:02:56'),
(58, 50, 4, 3, '200.00', '0.00', '600.00', '2025-09-07 08:01:33'),
(59, 51, 4, 1, '200.00', '0.00', '200.00', '2025-09-07 08:05:05'),
(60, 51, 2, 1, '25.00', '0.00', '25.00', '2025-09-07 08:05:05'),
(61, 52, 4, 3, '200.00', '0.00', '600.00', '2025-09-08 01:48:08'),
(62, 53, 4, 1, '200.00', '0.00', '200.00', '2025-09-08 01:49:27'),
(63, 54, 4, 1, '200.00', '0.00', '200.00', '2025-09-08 04:46:20'),
(64, 55, 4, 17, '200.00', '0.00', '3400.00', '2025-09-08 08:21:01'),
(65, 56, 4, 1, '200.00', '0.00', '200.00', '2025-09-09 01:55:40'),
(66, 57, 4, 1, '200.00', '0.00', '200.00', '2025-09-09 01:58:25'),
(67, 58, 4, 1, '200.00', '0.00', '200.00', '2025-09-09 03:48:42'),
(68, 60, 4, 1, '200.00', '0.00', '200.00', '2025-09-10 04:39:12'),
(69, 61, 2, 10, '25.00', '0.00', '250.00', '2025-09-10 15:39:17'),
(70, 62, 4, 1, '200.00', '0.00', '200.00', '2025-09-10 15:46:30'),
(71, 63, 6, 1, '20.00', '0.00', '20.00', '2025-09-14 01:31:28'),
(72, 64, 36, 3, '15.00', '0.00', '45.00', '2025-09-18 02:27:29'),
(73, 64, 38, 5, '28.00', '0.00', '140.00', '2025-09-18 02:27:29'),
(74, 64, 39, 2, '65.00', '0.00', '130.00', '2025-09-18 02:27:29'),
(75, 64, 27, 1, '12.00', '0.00', '12.00', '2025-09-18 02:27:29'),
(76, 64, 14, 3, '6.00', '0.00', '18.00', '2025-09-18 02:27:29'),
(77, 64, 25, 1, '28.00', '0.00', '28.00', '2025-09-18 02:27:29'),
(78, 65, 36, 7, '15.00', '0.00', '105.00', '2025-09-18 02:34:48'),
(79, 65, 38, 6, '28.00', '0.00', '168.00', '2025-09-18 02:34:48'),
(80, 65, 14, 4, '6.00', '0.00', '24.00', '2025-09-18 02:34:48'),
(81, 65, 27, 1, '12.00', '0.00', '12.00', '2025-09-18 02:34:48'),
(82, 65, 39, 2, '65.00', '0.00', '130.00', '2025-09-18 02:34:48'),
(83, 65, 25, 2, '28.00', '0.00', '56.00', '2025-09-18 02:34:48'),
(84, 65, 23, 2, '140.00', '0.00', '280.00', '2025-09-18 02:34:48'),
(85, 65, 21, 1, '6.00', '0.00', '6.00', '2025-09-18 02:34:48'),
(86, 66, 36, 2, '15.00', '0.00', '30.00', '2025-09-18 04:59:05'),
(87, 67, 36, 2, '15.00', '0.00', '30.00', '2025-09-18 05:01:18'),
(88, 68, 5, 1, '120.00', '0.00', '120.00', '2025-09-18 05:05:19'),
(89, 68, 11, 1, '35.00', '0.00', '35.00', '2025-09-18 05:05:19'),
(90, 69, 36, 1, '15.00', '0.00', '15.00', '2025-09-18 08:38:10'),
(91, 69, 38, 1, '28.00', '0.00', '28.00', '2025-09-18 08:38:10');

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

DROP TABLE IF EXISTS `stock_movements`;
CREATE TABLE IF NOT EXISTS `stock_movements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL DEFAULT '0',
  `movement_type` enum('in','out','adjustment') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `reference_type` enum('sale','purchase','stock_take','adjustment') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `reference_id` int DEFAULT '0',
  `notes` text,
  `created_by` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `idx_stock_movements_product` (`product_id`),
  KEY `idx_stock_movements_date` (`created_at`)
) ENGINE=MyISAM AUTO_INCREMENT=111 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `stock_movements`
--

INSERT INTO `stock_movements` (`id`, `product_id`, `movement_type`, `quantity`, `reference_type`, `reference_id`, `notes`, `created_by`, `created_at`) VALUES
(1, 3, 'out', 2, 'sale', 1, NULL, 1, '2025-08-17 03:12:00'),
(2, 3, 'out', 2, 'sale', 2, NULL, 1, '2025-08-17 03:31:07'),
(3, 1, 'out', 1, 'sale', 3, NULL, 2, '2025-08-17 06:43:34'),
(4, 2, 'out', 1, 'sale', 4, NULL, 1, '2025-08-17 08:10:20'),
(5, 1, 'out', 2, 'sale', 4, NULL, 1, '2025-08-17 08:10:20'),
(6, 4, 'in', 10, 'adjustment', 4, 'Initial stock', 1, '2025-08-18 07:11:03'),
(7, 4, 'in', 990, 'adjustment', 4, 'Stock adjustment (increase)', 1, '2025-08-18 07:12:15'),
(8, 1, 'out', 2, 'sale', 5, NULL, 1, '2025-08-18 08:20:28'),
(9, 3, 'out', 1, 'sale', 5, NULL, 1, '2025-08-18 08:20:28'),
(10, 5, 'in', 90, 'adjustment', 5, 'Initial stock', 1, '2025-08-20 02:31:56'),
(11, 1, 'out', 2, 'sale', 6, NULL, 1, '2025-08-20 02:43:00'),
(12, 1, 'out', 2, 'sale', 7, NULL, 1, '2025-08-21 01:36:24'),
(13, 2, 'out', 1, 'sale', 7, NULL, 1, '2025-08-21 01:36:24'),
(14, 3, 'out', 1, 'sale', 7, NULL, 1, '2025-08-21 01:36:24'),
(15, 4, 'out', 1, 'sale', 8, NULL, 1, '2025-08-21 02:10:48'),
(16, 1, 'out', 3, 'sale', 8, NULL, 1, '2025-08-21 02:10:48'),
(17, 2, 'out', 1, 'sale', 8, NULL, 1, '2025-08-21 02:10:48'),
(18, 1, 'in', 2, 'adjustment', 1, 'Stock adjustment (increase)', 1, '2025-08-23 14:43:03'),
(19, 3, 'out', 84, 'adjustment', 3, 'Stock adjustment (decrease)', 1, '2025-08-23 14:43:16'),
(20, 1, 'in', 10, 'stock_take', 0, '', 1, '2025-08-24 03:31:00'),
(21, 3, 'out', 5, 'stock_take', 0, '', 1, '2025-08-24 03:31:12'),
(22, 1, 'out', 10, 'adjustment', 1, 'Stock adjustment (decrease)', 1, '2025-08-25 06:52:24'),
(23, 3, 'out', 1, 'sale', 9, NULL, 1, '2025-08-25 06:55:40'),
(24, 6, 'in', 10, 'adjustment', 6, 'Initial stock', 1, '2025-08-25 08:22:39'),
(25, 6, 'out', 1, 'sale', 10, NULL, 1, '2025-08-28 16:23:53'),
(26, 6, 'out', 1, 'sale', 11, NULL, 1, '2025-08-29 02:22:00'),
(27, 6, 'out', 1, 'sale', 12, NULL, 1, '2025-08-29 02:22:39'),
(28, 2, 'out', 4, 'sale', 13, NULL, 1, '2025-08-29 02:23:18'),
(29, 6, 'out', 1, 'sale', 14, NULL, 1, '2025-08-29 06:23:30'),
(30, 2, 'out', 1, 'sale', 15, NULL, 1, '2025-09-02 02:32:20'),
(31, 6, 'out', 1, 'sale', 16, NULL, 1, '2025-09-02 02:37:40'),
(32, 4, 'out', 1, 'sale', 17, NULL, 1, '2025-09-02 05:01:51'),
(33, 6, 'out', 1, 'sale', 18, NULL, 1, '2025-09-02 05:04:58'),
(34, 6, 'out', 1, 'sale', 19, NULL, 1, '2025-09-03 06:46:47'),
(35, 6, 'out', 1, 'sale', 20, NULL, 1, '2025-09-03 06:53:03'),
(36, 3, 'out', 1, 'sale', 21, NULL, 1, '2025-09-03 06:53:50'),
(37, 6, 'out', 1, 'sale', 22, NULL, 1, '2025-09-03 06:54:37'),
(38, 4, 'out', 1, 'sale', 23, NULL, 1, '2025-09-03 07:01:45'),
(39, 4, 'out', 1, 'sale', 24, NULL, 1, '2025-09-03 07:02:26'),
(40, 4, 'out', 1, 'sale', 25, NULL, 1, '2025-09-03 07:05:44'),
(41, 4, 'out', 2, 'sale', 26, NULL, 1, '2025-09-03 07:09:38'),
(42, 4, 'out', 1, 'sale', 27, NULL, 1, '2025-09-03 07:17:17'),
(43, 4, 'out', 1, 'sale', 28, NULL, 1, '2025-09-03 07:18:50'),
(44, 4, 'out', 1, 'sale', 29, NULL, 1, '2025-09-03 07:19:56'),
(45, 4, 'out', 1, 'sale', 30, NULL, 1, '2025-09-03 07:20:19'),
(46, 4, 'out', 1, 'sale', 31, NULL, 1, '2025-09-03 07:21:54'),
(47, 4, 'out', 1, 'sale', 32, NULL, 1, '2025-09-03 08:14:24'),
(48, 3, 'out', 1, 'sale', 32, NULL, 1, '2025-09-03 08:14:24'),
(49, 3, 'out', 1, 'sale', 33, NULL, 1, '2025-09-03 08:14:58'),
(50, 6, 'out', 1, 'sale', 33, NULL, 1, '2025-09-03 08:14:58'),
(51, 4, 'out', 1, 'sale', 34, NULL, 1, '2025-09-03 08:18:35'),
(52, 4, 'out', 1, 'sale', 35, NULL, 1, '2025-09-03 08:19:05'),
(53, 4, 'out', 1, 'sale', 36, NULL, 1, '2025-09-03 08:19:27'),
(54, 3, 'out', 1, 'sale', 37, NULL, 1, '2025-09-04 03:05:51'),
(55, 4, 'out', 1, 'sale', 38, NULL, 1, '2025-09-04 04:08:58'),
(56, 4, 'out', 1, 'sale', 39, NULL, 1, '2025-09-04 04:20:00'),
(57, 4, 'out', 1, 'sale', 40, NULL, 1, '2025-09-04 04:36:43'),
(58, 4, 'out', 1, 'sale', 41, NULL, 1, '2025-09-06 15:49:32'),
(59, 4, 'out', 1, 'sale', 42, NULL, 1, '2025-09-06 15:52:50'),
(60, 4, 'out', 1, 'sale', 43, NULL, 1, '2025-09-06 15:55:58'),
(61, 4, 'out', 1, 'sale', 44, NULL, 1, '2025-09-06 16:02:22'),
(62, 4, 'out', 3, 'sale', 45, NULL, 1, '2025-09-06 16:06:16'),
(63, 4, 'out', 1, 'sale', 46, NULL, 1, '2025-09-06 16:13:55'),
(64, 4, 'out', 1, 'sale', 47, NULL, 1, '2025-09-07 03:10:53'),
(65, 4, 'out', 1, 'sale', 48, NULL, 1, '2025-09-07 03:12:28'),
(66, 4, 'out', 2, 'sale', 49, NULL, 1, '2025-09-07 07:02:56'),
(67, 4, 'in', 2, '', 49, 'Refund for sale #202509070010091', 1, '2025-09-07 07:51:38'),
(68, 4, 'out', 3, 'sale', 50, NULL, 1, '2025-09-07 08:01:33'),
(69, 4, 'out', 1, 'sale', 51, NULL, 1, '2025-09-07 08:05:05'),
(70, 2, 'out', 1, 'sale', 51, NULL, 1, '2025-09-07 08:05:05'),
(71, 4, 'in', 3, '', 50, 'Refund for sale #202509070010698', 1, '2025-09-07 08:16:03'),
(72, 4, 'out', 3, 'sale', 52, NULL, 1, '2025-09-08 01:48:08'),
(73, 4, 'out', 1, 'sale', 53, NULL, 1, '2025-09-08 01:49:27'),
(74, 4, 'out', 1, 'sale', 54, NULL, 1, '2025-09-08 04:46:20'),
(75, 4, 'out', 17, 'sale', 55, NULL, 1, '2025-09-08 08:21:01'),
(76, 4, 'out', 1, 'sale', 56, NULL, 1, '2025-09-09 01:55:40'),
(77, 4, 'out', 1, 'sale', 57, NULL, 1, '2025-09-09 01:58:25'),
(78, 4, 'out', 1, 'sale', 58, NULL, 1, '2025-09-09 03:48:42'),
(79, 4, 'out', 1, 'sale', 60, NULL, 1, '2025-09-10 04:39:12'),
(80, 0, NULL, 0, NULL, 0, NULL, 0, '2025-09-10 13:27:28'),
(81, 2, 'out', 10, 'sale', 61, NULL, 1, '2025-09-10 15:39:17'),
(82, 4, 'out', 1, 'sale', 62, NULL, 1, '2025-09-10 15:46:30'),
(83, 4, 'in', 1, 'adjustment', 62, 'Refund for sale #202509100010896', 1, '2025-09-10 17:35:16'),
(84, 2, 'in', 10, 'adjustment', 61, 'Refund for sale #202509100010962', 1, '2025-09-10 17:35:55'),
(85, 4, 'in', 1, 'adjustment', 60, 'Refund for sale #202509100010024', 1, '2025-09-10 17:36:02'),
(86, 4, 'in', 1, 'adjustment', 58, 'Refund for sale #202509090010395', 1, '2025-09-10 17:36:40'),
(87, 8, 'in', 100, 'adjustment', 8, 'Initial stock', 1, '2025-09-10 18:19:53'),
(88, 6, 'in', 10, 'adjustment', 6, 'Stock adjustment (increase)', 1, '2025-09-10 18:25:01'),
(89, 6, 'in', 90, 'adjustment', 6, 'Stock adjustment (increase)', 1, '2025-09-10 18:32:12'),
(90, 6, 'out', 1, 'sale', 63, NULL, 1, '2025-09-14 01:31:28'),
(91, 36, 'out', 3, 'sale', 64, NULL, 1, '2025-09-18 02:27:29'),
(92, 38, 'out', 5, 'sale', 64, NULL, 1, '2025-09-18 02:27:29'),
(93, 39, 'out', 2, 'sale', 64, NULL, 1, '2025-09-18 02:27:29'),
(94, 27, 'out', 1, 'sale', 64, NULL, 1, '2025-09-18 02:27:29'),
(95, 14, 'out', 3, 'sale', 64, NULL, 1, '2025-09-18 02:27:29'),
(96, 25, 'out', 1, 'sale', 64, NULL, 1, '2025-09-18 02:27:29'),
(97, 36, 'out', 7, 'sale', 65, NULL, 1, '2025-09-18 02:34:48'),
(98, 38, 'out', 6, 'sale', 65, NULL, 1, '2025-09-18 02:34:48'),
(99, 14, 'out', 4, 'sale', 65, NULL, 1, '2025-09-18 02:34:48'),
(100, 27, 'out', 1, 'sale', 65, NULL, 1, '2025-09-18 02:34:48'),
(101, 39, 'out', 2, 'sale', 65, NULL, 1, '2025-09-18 02:34:48'),
(102, 25, 'out', 2, 'sale', 65, NULL, 1, '2025-09-18 02:34:48'),
(103, 23, 'out', 2, 'sale', 65, NULL, 1, '2025-09-18 02:34:48'),
(104, 21, 'out', 1, 'sale', 65, NULL, 1, '2025-09-18 02:34:48'),
(105, 36, 'out', 2, 'sale', 66, NULL, 1, '2025-09-18 04:59:05'),
(106, 36, 'out', 2, 'sale', 67, NULL, 1, '2025-09-18 05:01:18'),
(107, 5, 'out', 1, 'sale', 68, NULL, 1, '2025-09-18 05:05:19'),
(108, 11, 'out', 1, 'sale', 68, NULL, 1, '2025-09-18 05:05:19'),
(109, 36, 'out', 1, 'sale', 69, NULL, 1, '2025-09-18 08:38:10'),
(110, 38, 'out', 1, 'sale', 69, NULL, 1, '2025-09-18 08:38:10');

-- --------------------------------------------------------

--
-- Table structure for table `stock_take_items`
--

DROP TABLE IF EXISTS `stock_take_items`;
CREATE TABLE IF NOT EXISTS `stock_take_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `session_id` int NOT NULL,
  `product_id` int NOT NULL,
  `system_quantity` int NOT NULL,
  `counted_quantity` int NOT NULL,
  `difference` int NOT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `session_id` (`session_id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_take_sessions`
--

DROP TABLE IF EXISTS `stock_take_sessions`;
CREATE TABLE IF NOT EXISTS `stock_take_sessions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `session_name` varchar(255) NOT NULL,
  `start_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `end_date` timestamp NULL DEFAULT NULL,
  `status` enum('in_progress','completed','cancelled') DEFAULT 'in_progress',
  `created_by` int NOT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `contact_person`, `phone`, `email`, `address`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'AB Supplier', 'John Doe', '+60 12-345 6789', 'john@abcsupplier.com', '', 1, '2025-08-17 02:55:52', '2025-08-18 03:59:20'),
(2, 'XYZ Trading', 'Jane Smith', '+60 12-345 6790', 'jane@xyztrading.com', '', 1, '2025-08-17 02:55:52', '2025-08-20 02:40:33');

-- --------------------------------------------------------

--
-- Table structure for table `uom`
--

DROP TABLE IF EXISTS `uom`;
CREATE TABLE IF NOT EXISTS `uom` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `abbreviation` varchar(10) DEFAULT NULL,
  `description` text,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `uom`
--

INSERT INTO `uom` (`id`, `name`, `abbreviation`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Piece', 'pcs', NULL, 1, '2025-08-17 02:55:52', '2025-08-17 02:55:52'),
(2, 'Kilogram', 'kg', 'test', 1, '2025-08-17 02:55:52', '2025-08-24 01:35:13'),
(3, 'Liter', 'L', NULL, 1, '2025-08-17 02:55:52', '2025-08-17 02:55:52'),
(4, 'Box', 'box', NULL, 1, '2025-08-17 02:55:52', '2025-08-17 02:55:52'),
(5, 'Pack', 'pack', NULL, 1, '2025-08-17 02:55:52', '2025-08-17 02:55:52');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `role` enum('admin','manager','cashier') DEFAULT 'cashier',
  `is_active` tinyint(1) DEFAULT '1',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `access_dashboard` tinyint(1) DEFAULT '1' COMMENT 'Access to Dashboard',
  `access_pos` tinyint(1) DEFAULT '1' COMMENT 'Access to Point of Sale',
  `access_sales` tinyint(1) DEFAULT '1' COMMENT 'Access to Sales History',
  `access_opening_closing` tinyint(1) DEFAULT '0' COMMENT 'Access to Opening/Closing Cash',
  `access_products` tinyint(1) DEFAULT '0' COMMENT 'Access to Products Management',
  `access_categories` tinyint(1) DEFAULT '0' COMMENT 'Access to Categories Management',
  `access_suppliers` tinyint(1) DEFAULT '0' COMMENT 'Access to Suppliers Management',
  `access_uom` tinyint(1) DEFAULT '0' COMMENT 'Access to Unit of Measure Management',
  `access_stock_take` tinyint(1) DEFAULT '0' COMMENT 'Access to Stock Take',
  `access_inventory_report` tinyint(1) DEFAULT '0' COMMENT 'Access to Inventory Reports',
  `access_members` tinyint(1) DEFAULT '0' COMMENT 'Access to Members Management',
  `access_customer_orders` tinyint(1) DEFAULT '0' COMMENT 'Access to Customer Orders',
  `access_customer_order` tinyint(1) DEFAULT '0' COMMENT 'Access to Customer Order Entry',
  `access_sales_report` tinyint(1) DEFAULT '0' COMMENT 'Access to Sales Reports',
  `access_member_report` tinyint(1) DEFAULT '0' COMMENT 'Access to Member Reports',
  `access_profit_loss` tinyint(1) DEFAULT '0' COMMENT 'Access to Profit & Loss Reports',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `email`, `phone`, `role`, `is_active`, `last_login`, `created_at`, `updated_at`, `access_dashboard`, `access_pos`, `access_sales`, `access_opening_closing`, `access_products`, `access_categories`, `access_suppliers`, `access_uom`, `access_stock_take`, `access_inventory_report`, `access_members`, `access_customer_orders`, `access_customer_order`, `access_sales_report`, `access_member_report`, `access_profit_loss`) VALUES
(1, 'admin', 'admin123', 'System Administrator', 'admin@possystem.com', '0123456789', 'admin', 1, '2025-10-05 03:25:00', '2025-08-17 02:55:52', '2025-10-05 03:25:00', 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(2, 'cashier', 'cashier123', 'Cashier User', 'cashier@possystem.com', NULL, 'cashier', 1, '2025-10-05 03:24:21', '2025-08-17 02:55:52', '2025-10-05 03:24:21', 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `vouchers`
--

DROP TABLE IF EXISTS `vouchers`;
CREATE TABLE IF NOT EXISTS `vouchers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `type` enum('percentage','fixed') NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `min_purchase_amount` decimal(10,2) DEFAULT '0.00',
  `max_discount_amount` decimal(10,2) DEFAULT '0.00',
  `usage_limit` int DEFAULT '1',
  `used_count` int DEFAULT '0',
  `valid_from` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `valid_until` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure for view `access_level_summary`
--
DROP TABLE IF EXISTS `access_level_summary`;

DROP VIEW IF EXISTS `access_level_summary`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `access_level_summary`  AS SELECT `al`.`id` AS `id`, `al`.`level_name` AS `level_name`, `al`.`level_key` AS `level_key`, `al`.`description` AS `description`, `al`.`status` AS `status`, count(distinct `u`.`id`) AS `user_count`, count(distinct `alp`.`permission`) AS `permissions_count`, group_concat(distinct `alp`.`permission` order by `alp`.`permission` ASC separator ', ') AS `permissions_list`, `al`.`created_at` AS `created_at`, `al`.`updated_at` AS `updated_at` FROM ((`access_level` `al` left join `users` `u` on((`al`.`level_key` = `u`.`role`))) left join `access_level_permissions` `alp` on((`al`.`id` = `alp`.`access_level_id`))) GROUP BY `al`.`id`, `al`.`level_name`, `al`.`level_key`, `al`.`description`, `al`.`status`, `al`.`created_at`, `al`.`updated_at` ORDER BY `al`.`level_name` ASC  ;

-- --------------------------------------------------------

--
-- Structure for view `customer_order_details`
--
DROP TABLE IF EXISTS `customer_order_details`;

DROP VIEW IF EXISTS `customer_order_details`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `customer_order_details`  AS SELECT `cc`.`id` AS `id`, `cc`.`order_number` AS `order_number`, `cc`.`table_id` AS `table_id`, `cc`.`product_id` AS `product_id`, `p`.`name` AS `product_name`, `p`.`img` AS `product_image`, `cc`.`sku` AS `sku`, `cc`.`quantity` AS `quantity`, `cc`.`price` AS `price`, `cc`.`subtotal` AS `subtotal`, `cc`.`status` AS `status`, `cc`.`special_instructions` AS `special_instructions`, `cc`.`created_at` AS `created_at`, `cc`.`updated_at` AS `updated_at`, `c`.`name` AS `category_name` FROM ((`customer_cart` `cc` left join `products` `p` on((`cc`.`product_id` = `p`.`id`))) left join `categories` `c` on((`p`.`category_id` = `c`.`id`))) WHERE (`cc`.`order_number` is not null) ORDER BY `cc`.`order_number` ASC, `cc`.`created_at` ASC  ;

-- --------------------------------------------------------

--
-- Structure for view `customer_order_summary`
--
DROP TABLE IF EXISTS `customer_order_summary`;

DROP VIEW IF EXISTS `customer_order_summary`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `customer_order_summary`  AS SELECT `cc`.`order_number` AS `order_number`, `cc`.`table_id` AS `table_id`, count(0) AS `total_items`, sum(`cc`.`subtotal`) AS `subtotal`, (sum(`cc`.`subtotal`) * 0.06) AS `tax_amount`, (sum(`cc`.`subtotal`) * 1.06) AS `total_amount`, min(`cc`.`created_at`) AS `order_created_at`, max(`cc`.`updated_at`) AS `last_updated_at`, group_concat(distinct `cc`.`status` separator ',') AS `statuses`, (case when ((count(distinct `cc`.`status`) = 1) and (max(`cc`.`status`) = 'paid')) then 'paid' when ((count(distinct `cc`.`status`) = 1) and (max(`cc`.`status`) = 'cancelled')) then 'cancelled' when ((count(distinct `cc`.`status`) = 1) and (max(`cc`.`status`) = 'abandoned')) then 'abandoned' else 'in_progress' end) AS `overall_status` FROM `customer_cart` AS `cc` WHERE (`cc`.`order_number` is not null) GROUP BY `cc`.`order_number`, `cc`.`table_id``table_id`  ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
