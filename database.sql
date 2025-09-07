-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 06, 2025 at 03:58 PM
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
-- Database: `pos_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
CREATE TABLE IF NOT EXISTS `cart` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `sku` varchar(50) NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `status` enum('active','ordered','abandoned') DEFAULT 'active',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cart_user_status` (`user_id`,`status`),
  KEY `idx_cart_product` (`product_id`),
  KEY `idx_cart_sku` (`sku`),
  KEY `idx_cart_created` (`created_at`)
) ENGINE=MyISAM AUTO_INCREMENT=94 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `sku`, `quantity`, `price`, `subtotal`, `status`, `created_at`, `updated_at`) VALUES
(13, 1, 2, 'PROD002', 1, '25.00', '25.00', 'ordered', '2025-08-17 12:25:35', '2025-08-17 16:10:20'),
(12, 1, 1, 'PROD001', 2, '4000.00', '8000.00', 'ordered', '2025-08-17 11:51:41', '2025-08-17 16:10:20'),
(4, 1, 3, 'PROD003', 2, '30.00', '60.00', 'ordered', '2025-08-17 11:11:00', '2025-08-17 11:12:00'),
(8, 1, 3, 'PROD003', 2, '30.00', '60.00', 'ordered', '2025-08-17 11:18:52', '2025-08-17 11:31:07'),
(14, 2, 1, 'PROD001', 1, '4000.00', '4000.00', 'ordered', '2025-08-17 14:43:28', '2025-08-17 14:43:34'),
(15, 1, 1, 'PROD001', 2, '4000.00', '8000.00', 'ordered', '2025-08-18 16:18:41', '2025-08-18 16:20:28'),
(16, 1, 3, 'PROD003', 1, '30.00', '30.00', 'ordered', '2025-08-18 16:19:34', '2025-08-18 16:20:28'),
(17, 1, 1, 'PROD00', 2, '4000.00', '8000.00', 'ordered', '2025-08-20 10:42:34', '2025-08-20 10:43:00'),
(19, 1, 1, 'PROD00', 2, '4000.00', '8000.00', 'ordered', '2025-08-21 09:32:32', '2025-08-21 09:36:24'),
(20, 1, 2, 'PROD002', 1, '25.00', '25.00', 'ordered', '2025-08-21 09:36:00', '2025-08-21 09:36:24'),
(21, 1, 3, 'PROD003', 1, '30.00', '30.00', 'ordered', '2025-08-21 09:36:08', '2025-08-21 09:36:24'),
(27, 1, 4, 'TEST', 1, '200.00', '200.00', 'ordered', '2025-08-21 10:10:11', '2025-08-21 10:10:48'),
(26, 1, 1, 'PROD001', 3, '4000.00', '12000.00', 'ordered', '2025-08-21 10:09:50', '2025-08-21 10:10:48'),
(25, 1, 2, 'PROD002', 1, '25.00', '25.00', 'ordered', '2025-08-21 10:09:38', '2025-08-21 10:10:48'),
(56, 1, 6, 'asas', 1, '200.00', '200.00', 'ordered', '2025-08-29 10:22:34', '2025-08-29 10:22:39'),
(55, 1, 6, 'asas', 1, '200.00', '200.00', 'ordered', '2025-08-29 10:21:53', '2025-08-29 10:22:00'),
(43, 1, 3, 'PROD003', 1, '30.00', '30.00', 'ordered', '2025-08-25 14:55:36', '2025-08-25 14:55:40'),
(54, 1, 6, 'asas', 1, '200.00', '200.00', 'ordered', '2025-08-27 16:08:41', '2025-08-29 00:23:53'),
(57, 1, 2, 'PROD002', 4, '25.00', '100.00', 'ordered', '2025-08-29 10:23:10', '2025-08-29 10:23:18'),
(58, 1, 6, 'asas', 1, '200.00', '200.00', 'ordered', '2025-08-29 14:20:47', '2025-08-29 14:23:30'),
(61, 1, 2, 'PROD002', 1, '25.00', '25.00', 'ordered', '2025-09-02 10:01:29', '2025-09-02 10:32:20'),
(62, 1, 6, 'asas', 1, '200.00', '200.00', 'ordered', '2025-09-02 10:37:17', '2025-09-02 10:37:40'),
(63, 1, 4, 'TEST', 1, '200.00', '200.00', 'ordered', '2025-09-02 13:01:45', '2025-09-02 13:01:51'),
(64, 1, 6, 'asas', 1, '200.00', '200.00', 'ordered', '2025-09-02 13:04:48', '2025-09-02 13:04:58'),
(66, 1, 6, 'asas', 1, '200.00', '200.00', 'ordered', '2025-09-03 13:20:14', '2025-09-03 14:46:47'),
(67, 1, 6, 'asas', 1, '200.00', '200.00', 'ordered', '2025-09-03 14:52:40', '2025-09-03 14:53:03'),
(68, 1, 3, 'PROD003', 1, '30.00', '30.00', 'ordered', '2025-09-03 14:53:44', '2025-09-03 14:53:50'),
(69, 1, 6, 'asas', 1, '200.00', '200.00', 'ordered', '2025-09-03 14:54:34', '2025-09-03 14:54:37'),
(70, 1, 4, 'TEST', 1, '200.00', '200.00', 'ordered', '2025-09-03 15:01:36', '2025-09-03 15:01:45'),
(71, 1, 4, 'TEST', 1, '200.00', '200.00', 'ordered', '2025-09-03 15:02:20', '2025-09-03 15:02:26'),
(72, 1, 4, 'TEST', 1, '200.00', '200.00', 'ordered', '2025-09-03 15:05:38', '2025-09-03 15:05:44'),
(73, 1, 4, 'TEST', 2, '200.00', '400.00', 'ordered', '2025-09-03 15:06:41', '2025-09-03 15:09:38'),
(74, 1, 4, 'TEST', 1, '200.00', '200.00', 'ordered', '2025-09-03 15:11:26', '2025-09-03 15:17:17'),
(75, 1, 4, 'TEST', 1, '200.00', '200.00', 'ordered', '2025-09-03 15:18:47', '2025-09-03 15:18:50'),
(76, 1, 4, 'TEST', 1, '200.00', '200.00', 'ordered', '2025-09-03 15:19:48', '2025-09-03 15:19:56'),
(77, 1, 4, 'TEST', 1, '200.00', '200.00', 'ordered', '2025-09-03 15:20:14', '2025-09-03 15:20:19'),
(78, 1, 4, 'TEST', 1, '200.00', '200.00', 'ordered', '2025-09-03 15:21:48', '2025-09-03 15:21:54'),
(79, 1, 4, 'TEST', 1, '200.00', '200.00', 'ordered', '2025-09-03 15:27:40', '2025-09-03 16:14:24'),
(80, 1, 3, 'PROD003', 1, '30.00', '30.00', 'ordered', '2025-09-03 16:10:59', '2025-09-03 16:14:24'),
(81, 1, 3, 'PROD003', 1, '30.00', '30.00', 'ordered', '2025-09-03 16:14:49', '2025-09-03 16:14:58'),
(82, 1, 6, 'asas', 1, '200.00', '200.00', 'ordered', '2025-09-03 16:14:55', '2025-09-03 16:14:58'),
(84, 1, 4, 'TEST', 1, '200.00', '200.00', 'ordered', '2025-09-03 16:17:49', '2025-09-03 16:18:35'),
(85, 1, 4, 'TEST', 1, '200.00', '200.00', 'ordered', '2025-09-03 16:19:03', '2025-09-03 16:19:05'),
(86, 1, 4, 'TEST', 1, '200.00', '200.00', 'ordered', '2025-09-03 16:19:24', '2025-09-03 16:19:27'),
(87, 1, 3, 'PROD003', 1, '30.00', '30.00', 'ordered', '2025-09-04 11:05:07', '2025-09-04 11:05:51'),
(88, 1, 4, 'TEST', 1, '200.00', '200.00', 'ordered', '2025-09-04 11:10:24', '2025-09-04 12:08:58'),
(89, 1, 4, 'TEST', 1, '200.00', '200.00', 'ordered', '2025-09-04 12:18:09', '2025-09-04 12:20:00'),
(90, 1, 4, 'TEST', 1, '200.00', '200.00', 'ordered', '2025-09-04 12:34:45', '2025-09-04 12:36:43'),
(91, 1, 4, 'TEST', 1, '200.00', '200.00', 'ordered', '2025-09-06 23:49:28', '2025-09-06 23:49:32'),
(92, 1, 4, 'TEST', 1, '200.00', '200.00', 'ordered', '2025-09-06 23:52:47', '2025-09-06 23:52:50'),
(93, 1, 4, 'TEST', 1, '200.00', '200.00', 'ordered', '2025-09-06 23:55:55', '2025-09-06 23:55:58');

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
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Electronics', 'Electronic devices and accessories', 1, '2025-08-17 02:55:52', '2025-08-17 02:55:52'),
(2, 'Clothing', 'Apparel and fashion items', 1, '2025-08-17 02:55:52', '2025-08-18 07:34:38'),
(3, 'Food & Beverages', 'Food and drink products', 1, '2025-08-17 02:55:52', '2025-08-17 02:55:52'),
(4, 'Home & Garden', 'Home improvement and garden items', 1, '2025-08-17 02:55:52', '2025-08-25 08:21:58');

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
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `company_settings`
--

INSERT INTO `company_settings` (`id`, `company_name`, `address`, `phone`, `email`, `website`, `tax_number`, `currency`, `logo`, `created_at`, `updated_at`) VALUES
(1, 'POS System', '123 Main Street, Kuala Lumpur', '+60 12-345 6789', 'info@possystem.com', NULL, NULL, 'MYR', NULL, '2025-08-17 02:55:52', '2025-08-17 02:55:52');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

DROP TABLE IF EXISTS `members`;
CREATE TABLE IF NOT EXISTS `members` (
  `id` int NOT NULL AUTO_INCREMENT,
  `member_code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text,
  `membership_tier_id` int DEFAULT NULL,
  `total_points` int DEFAULT '0',
  `total_spent` decimal(10,2) DEFAULT '0.00',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `member_code` (`member_code`),
  KEY `membership_tier_id` (`membership_tier_id`),
  KEY `idx_members_code` (`member_code`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `name` varchar(100) NOT NULL,
  `description` text,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Cash', 'Cash payment method', 1, '2025-08-29 06:34:52', '2025-09-03 07:18:38'),
(2, 'Credit Card', 'Credit card payment method', 1, '2025-08-29 06:34:52', '2025-08-29 06:34:52'),
(3, 'Debit Card', 'Debit card payment method', 1, '2025-08-29 06:34:52', '2025-08-29 06:34:52'),
(4, 'E-Wallet', 'Electronic wallet payment method', 1, '2025-08-29 06:34:52', '2025-08-29 06:34:52'),
(5, 'Bank Transfer', 'Bank transfer payment method', 1, '2025-08-29 06:34:52', '2025-09-03 07:18:22'),
(6, 'Mobile Money', 'Mobile money payment (M-Pesa, etc.)', 1, '2025-09-03 08:08:32', '2025-09-03 08:08:32'),
(7, 'Check', 'Check payment', 1, '2025-09-03 08:08:32', '2025-09-03 08:08:32'),
(8, 'Gift Card', 'Gift card or voucher payment', 0, '2025-09-03 08:08:32', '2025-09-04 04:26:58');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sku` varchar(100) NOT NULL,
  `barcode` varchar(100) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `category_id` int DEFAULT NULL,
  `supplier_id` int DEFAULT NULL,
  `uom_id` int DEFAULT NULL,
  `cost_price` decimal(10,2) NOT NULL,
  `selling_price` decimal(10,2) NOT NULL,
  `stock_quantity` int DEFAULT '0',
  `min_stock_level` int DEFAULT '0',
  `max_stock_level` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`),
  UNIQUE KEY `barcode` (`barcode`),
  KEY `category_id` (`category_id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `uom_id` (`uom_id`),
  KEY `idx_products_sku` (`sku`),
  KEY `idx_products_barcode` (`barcode`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `sku`, `barcode`, `name`, `description`, `category_id`, `supplier_id`, `uom_id`, `cost_price`, `selling_price`, `stock_quantity`, `min_stock_level`, `max_stock_level`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'PROD001', '1234567890123', 'Laptop', 'High-performance laptop', 1, 1, 1, '2500.00', '4000.00', 0, 2, 0, 1, '2025-08-17 02:55:52', '2025-08-25 06:52:24'),
(2, 'PROD002', '1234567890124', 'T-Shirt', 'Cotton t-shirt', 2, 2, 1, '15.00', '25.00', 42, 10, 0, 1, '2025-08-17 02:55:52', '2025-09-02 02:32:20'),
(3, 'PROD003', '1234567890125', 'Rice', 'Premium rice 5kg', 3, 1, 3, '20.00', '30.00', 0, 20, 0, 1, '2025-08-17 02:55:52', '2025-09-04 03:05:51'),
(4, 'TEST', '12345678', 'TEST', '', 3, 1, 4, '100.00', '200.00', 978, 100, 0, 1, '2025-08-18 07:11:03', '2025-09-06 15:55:58'),
(6, 'asas', '134375', 'dads', '', 4, 1, 2, '100.00', '200.00', 0, 3, 0, 1, '2025-08-25 08:22:39', '2025-09-03 08:14:58');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
CREATE TABLE IF NOT EXISTS `sales` (
  `id` int NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(50) NOT NULL,
  `session_id` int DEFAULT NULL,
  `member_id` int DEFAULT NULL,
  `user_id` int NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) DEFAULT '0.00',
  `tax_amount` decimal(10,2) DEFAULT '0.00',
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'cash',
  `payment_method_id` int DEFAULT NULL,
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
) ENGINE=MyISAM AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `invoice_number`, `session_id`, `member_id`, `user_id`, `subtotal`, `discount_amount`, `tax_amount`, `total_amount`, `payment_method`, `payment_method_id`, `payment_status`, `notes`, `created_at`, `updated_at`) VALUES
(1, '202508170001400', 1, NULL, 1, '60.00', '0.00', '3.60', '63.60', 'cash', 1, 'paid', NULL, '2025-08-17 03:12:00', '2025-09-04 04:19:47'),
(2, '202508170001720', 1, NULL, 1, '60.00', '0.00', '3.60', '63.60', 'ewallet', 4, 'paid', NULL, '2025-08-17 03:31:07', '2025-09-04 04:19:47'),
(3, '202508170002044', 2, NULL, 2, '4000.00', '0.00', '240.00', '4240.00', 'cash', 1, 'paid', NULL, '2025-08-17 06:43:34', '2025-09-04 04:19:47'),
(4, '202508170001620', 1, NULL, 1, '8025.00', '0.00', '481.50', '8506.50', 'cash', 1, 'paid', NULL, '2025-08-17 08:10:20', '2025-09-04 04:19:47'),
(5, '202508180001905', 1, 1, 1, '8030.00', '401.50', '457.71', '8086.21', 'cash', 1, 'paid', NULL, '2025-08-18 08:20:28', '2025-09-04 04:19:47'),
(6, '202508200001962', 1, 1, 1, '8000.00', '400.00', '456.00', '8056.00', 'cash', 1, 'paid', NULL, '2025-08-20 02:43:00', '2025-09-04 04:19:47'),
(7, '202508210001373', 1, NULL, 1, '8055.00', '0.00', '483.30', '8538.30', 'cash', 1, 'paid', NULL, '2025-08-21 01:36:24', '2025-09-04 04:19:47'),
(8, '202508210001945', 1, NULL, 1, '12225.00', '0.00', '733.50', '12958.50', 'cash', 1, 'paid', NULL, '2025-08-21 02:10:48', '2025-09-04 04:19:47'),
(9, '202508250001112', 1, NULL, 1, '30.00', '0.00', '1.80', '31.80', 'cash', 1, 'paid', NULL, '2025-08-25 06:55:40', '2025-09-04 04:19:47'),
(10, '202508290001689', 1, NULL, 1, '200.00', '0.00', '12.00', '212.00', 'cash', 1, 'paid', NULL, '2025-08-28 16:23:53', '2025-09-04 04:19:47'),
(11, '202508290003643', 3, NULL, 1, '200.00', '0.00', '12.00', '212.00', 'cash', 1, 'paid', NULL, '2025-08-29 02:22:00', '2025-09-04 04:19:47'),
(12, '202508290003190', 3, NULL, 1, '200.00', '0.00', '12.00', '212.00', 'card', 2, 'paid', NULL, '2025-08-29 02:22:39', '2025-09-04 04:19:47'),
(13, '202508290003665', 3, NULL, 1, '100.00', '0.00', '6.00', '106.00', 'ewallet', 4, 'paid', NULL, '2025-08-29 02:23:18', '2025-09-04 04:19:47'),
(14, '202508290004276', 4, NULL, 1, '200.00', '0.00', '12.00', '212.00', 'card', 2, 'paid', NULL, '2025-08-29 06:23:30', '2025-09-04 04:19:47'),
(15, '202509020010867', 10, NULL, 1, '25.00', '0.00', '1.50', '26.50', 'cash', 1, 'paid', NULL, '2025-09-02 02:32:20', '2025-09-04 04:19:47'),
(16, '202509020010217', 10, NULL, 1, '200.00', '0.00', '12.00', '212.00', 'cash', 1, 'paid', NULL, '2025-09-02 02:37:40', '2025-09-04 04:19:47'),
(17, '202509020010014', 10, NULL, 1, '200.00', '0.00', '12.00', '212.00', 'cash', 1, 'paid', NULL, '2025-09-02 05:01:51', '2025-09-04 04:19:47'),
(18, '202509020010783', 10, NULL, 1, '200.00', '0.00', '12.00', '212.00', 'cash', 1, 'paid', NULL, '2025-09-02 05:04:58', '2025-09-04 04:19:47'),
(19, '202509030010674', 10, NULL, 1, '200.00', '0.00', '12.00', '212.00', '', NULL, 'paid', NULL, '2025-09-03 06:46:47', '2025-09-03 06:46:47'),
(20, '202509030010055', 10, NULL, 1, '200.00', '0.00', '12.00', '212.00', '', NULL, 'paid', NULL, '2025-09-03 06:53:03', '2025-09-03 06:53:03'),
(21, '202509030010056', 10, NULL, 1, '30.00', '0.00', '1.80', '31.80', '', NULL, 'paid', NULL, '2025-09-03 06:53:50', '2025-09-03 06:53:50'),
(22, '202509030010919', 10, NULL, 1, '200.00', '0.00', '12.00', '212.00', 'cash', 1, 'paid', NULL, '2025-09-03 06:54:37', '2025-09-04 04:19:47'),
(23, '202509030010097', 10, NULL, 1, '200.00', '0.00', '12.00', '212.00', '', NULL, 'paid', NULL, '2025-09-03 07:01:45', '2025-09-03 07:01:45'),
(24, '202509030010481', 10, NULL, 1, '200.00', '0.00', '12.00', '212.00', '', NULL, 'paid', NULL, '2025-09-03 07:02:26', '2025-09-03 07:02:26'),
(25, '202509030010401', 10, NULL, 1, '200.00', '0.00', '12.00', '212.00', '', NULL, 'paid', NULL, '2025-09-03 07:05:44', '2025-09-03 07:05:44'),
(26, '202509030010864', 10, NULL, 1, '400.00', '0.00', '24.00', '424.00', '', NULL, 'paid', NULL, '2025-09-03 07:09:37', '2025-09-03 07:09:37'),
(27, '202509030010363', 10, NULL, 1, '200.00', '0.00', '12.00', '212.00', 'card', 2, 'paid', NULL, '2025-09-03 07:17:17', '2025-09-04 04:19:47'),
(28, '202509030010288', 10, NULL, 1, '200.00', '0.00', '12.00', '212.00', 'ewallet', 4, 'paid', NULL, '2025-09-03 07:18:50', '2025-09-04 04:19:47'),
(29, '202509030010315', 10, NULL, 1, '200.00', '0.00', '12.00', '212.00', 'card', 2, 'paid', NULL, '2025-09-03 07:19:56', '2025-09-04 04:19:47'),
(30, '202509030010797', 10, NULL, 1, '200.00', '0.00', '12.00', '212.00', 'ewallet', 4, 'paid', NULL, '2025-09-03 07:20:19', '2025-09-04 04:19:47'),
(31, '202509030010319', 10, NULL, 1, '200.00', '0.00', '12.00', '212.00', '', NULL, 'paid', NULL, '2025-09-03 07:21:54', '2025-09-03 07:21:54'),
(32, '202509030010340', 10, NULL, 1, '230.00', '0.00', '13.80', '243.80', 'cash', 1, 'paid', NULL, '2025-09-03 08:14:24', '2025-09-04 04:19:47'),
(33, '202509030010159', 10, NULL, 1, '230.00', '0.00', '13.80', '243.80', 'cash', 1, 'paid', NULL, '2025-09-03 08:14:58', '2025-09-04 04:19:47'),
(34, '202509030010848', 10, NULL, 1, '200.00', '0.00', '12.00', '212.00', 'card', 2, 'paid', NULL, '2025-09-03 08:18:35', '2025-09-04 04:19:47'),
(35, '202509030010149', 10, NULL, 1, '200.00', '0.00', '12.00', '212.00', 'cash', 1, 'paid', NULL, '2025-09-03 08:19:05', '2025-09-04 04:19:47'),
(36, '202509030010269', 10, NULL, 1, '200.00', '0.00', '12.00', '212.00', 'cash', 1, 'paid', NULL, '2025-09-03 08:19:27', '2025-09-04 04:19:47'),
(37, '202509040010669', 10, NULL, 1, '30.00', '0.00', '1.80', '31.80', 'ewallet', 4, 'paid', NULL, '2025-09-04 03:05:51', '2025-09-04 04:19:47'),
(38, '202509040010629', 10, NULL, 1, '200.00', '0.00', '12.00', '212.00', 'cash', 1, 'paid', NULL, '2025-09-04 04:08:58', '2025-09-04 04:19:47'),
(39, '202509040010390', 10, NULL, 1, '200.00', '0.00', '12.00', '212.00', 'card', 2, 'paid', NULL, '2025-09-04 04:20:00', '2025-09-04 04:20:00'),
(40, '202509040010068', 10, NULL, 1, '200.00', '0.00', '12.00', '212.00', '', 5, 'paid', NULL, '2025-09-04 04:36:43', '2025-09-04 04:36:43'),
(41, '202509060010736', 10, NULL, 1, '200.00', '0.00', '12.00', '212.00', '', 5, 'paid', NULL, '2025-09-06 15:49:32', '2025-09-06 15:49:32'),
(42, '202509060010076', 10, NULL, 1, '200.00', '0.00', '12.00', '212.00', '', 5, 'paid', NULL, '2025-09-06 15:52:50', '2025-09-06 15:52:50'),
(43, '202509060010676', 10, NULL, 1, '200.00', '0.00', '12.00', '212.00', '', 7, 'paid', NULL, '2025-09-06 15:55:58', '2025-09-06 15:55:58');

-- --------------------------------------------------------

--
-- Table structure for table `sales_sessions`
--

DROP TABLE IF EXISTS `sales_sessions`;
CREATE TABLE IF NOT EXISTS `sales_sessions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
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
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sales_sessions`
--

INSERT INTO `sales_sessions` (`id`, `user_id`, `session_start`, `session_end`, `opening_amount`, `closing_amount`, `total_sales`, `total_refunds`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(10, 1, '2025-09-02 02:26:35', NULL, '1000.00', '0.00', '5877.70', '0.00', 'open', '', '2025-09-02 02:26:35', '2025-09-06 15:55:58');

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
) ENGINE=MyISAM AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
(51, 43, 4, 1, '200.00', '0.00', '200.00', '2025-09-06 15:55:58');

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

DROP TABLE IF EXISTS `stock_movements`;
CREATE TABLE IF NOT EXISTS `stock_movements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `movement_type` enum('in','out','adjustment') NOT NULL,
  `quantity` int NOT NULL,
  `reference_type` enum('sale','purchase','stock_take','adjustment') NOT NULL,
  `reference_id` int DEFAULT NULL,
  `notes` text,
  `created_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `idx_stock_movements_product` (`product_id`),
  KEY `idx_stock_movements_date` (`created_at`)
) ENGINE=MyISAM AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
(60, 4, 'out', 1, 'sale', 43, NULL, 1, '2025-09-06 15:55:58');

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
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `stock_take_sessions`
--

INSERT INTO `stock_take_sessions` (`id`, `session_name`, `start_date`, `end_date`, `status`, `created_by`, `notes`, `created_at`, `updated_at`) VALUES
(3, 'JANUARI', '2025-08-24 07:42:28', NULL, 'in_progress', 1, '', '2025-08-24 07:42:28', '2025-08-24 07:42:28'),
(2, 'test1', '2025-08-24 03:26:24', NULL, 'in_progress', 1, '', '2025-08-24 03:26:24', '2025-08-24 03:26:24');

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
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `email`, `phone`, `role`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin123', 'System Administrator', 'admin@possystem.com', NULL, 'admin', 1, '2025-09-06 15:26:30', '2025-08-17 02:55:52', '2025-09-06 15:26:30'),
(2, 'cashier', 'cashier123', 'Cashier User', 'cashier@possystem.com', NULL, 'cashier', 1, '2025-08-17 06:43:07', '2025-08-17 02:55:52', '2025-08-17 06:43:07');

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
