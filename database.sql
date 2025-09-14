-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 11, 2025 at 05:45 AM
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
) ENGINE=MyISAM AUTO_INCREMENT=132 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
(93, 1, 4, 'TEST', 1, '200.00', '200.00', 'ordered', '2025-09-06 23:55:55', '2025-09-06 23:55:58'),
(94, 1, 4, 'TEST', 1, '200.00', '200.00', 'ordered', '2025-09-07 00:02:15', '2025-09-07 00:02:22'),
(95, 1, 4, 'TEST', 3, '200.00', '600.00', 'ordered', '2025-09-07 00:06:09', '2025-09-07 00:06:16'),
(96, 1, 4, 'TEST', 1, '200.00', '200.00', 'ordered', '2025-09-07 00:13:50', '2025-09-07 00:13:55'),
(97, 1, 4, 'TEST', 1, '200.00', '200.00', 'ordered', '2025-09-07 09:13:12', '2025-09-07 11:10:53'),
(98, 1, 4, 'TEST', 1, '200.00', '200.00', 'ordered', '2025-09-07 11:12:25', '2025-09-07 11:12:28'),
(99, 1, 4, 'TEST', 2, '200.00', '400.00', 'ordered', '2025-09-07 15:01:00', '2025-09-07 15:02:56'),
(100, 1, 4, 'TEST', 3, '200.00', '600.00', 'ordered', '2025-09-07 16:01:22', '2025-09-07 16:01:33'),
(101, 1, 4, 'TEST', 1, '200.00', '200.00', 'ordered', '2025-09-07 16:04:54', '2025-09-07 16:05:05'),
(102, 1, 2, 'PROD002', 1, '25.00', '25.00', 'ordered', '2025-09-07 16:04:58', '2025-09-07 16:05:05'),
(103, 1, 4, 'TEST', 3, '200.00', '600.00', 'ordered', '2025-09-07 16:16:14', '2025-09-08 09:48:08'),
(104, 1, 4, 'TEST', 1, '200.00', '200.00', 'ordered', '2025-09-08 09:49:19', '2025-09-08 09:49:27'),
(105, 1, 4, 'TEST', 1, '200.00', '200.00', 'ordered', '2025-09-08 12:10:17', '2025-09-08 12:46:20'),
(107, 1, 4, 'TEST', 17, '200.00', '3400.00', 'ordered', '2025-09-08 13:09:00', '2025-09-08 16:21:01'),
(108, 1, 4, 'TEST', 1, '200.00', '200.00', 'ordered', '2025-09-09 09:55:33', '2025-09-09 09:55:40'),
(109, 1, 4, 'TEST', 1, '200.00', '200.00', 'ordered', '2025-09-09 09:58:18', '2025-09-09 09:58:25'),
(110, 1, 4, 'TEST', 1, '200.00', '200.00', 'ordered', '2025-09-09 11:48:35', '2025-09-09 11:48:42'),
(114, 1, 4, 'TEST', 1, '200.00', '200.00', 'ordered', '2025-09-10 12:38:49', '2025-09-10 12:39:12'),
(128, 1, 2, 'PROD002', 10, '25.00', '250.00', 'ordered', '2025-09-10 23:38:57', '2025-09-10 23:39:17'),
(129, 1, 4, 'TEST', 1, '200.00', '200.00', 'ordered', '2025-09-10 23:46:01', '2025-09-10 23:46:30'),
(131, 1, 6, 'asa', 2, '20.00', '40.00', 'active', '2025-09-11 10:10:37', '2025-09-11 10:10:38');

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
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
(1, 'pos System', '123 Main Street, Kuala Lumpur', '+60 12-345 6789', 'info@possystem.com', '', '', 'USD', 'uploads/company/logo_1757382924.png', '2025-08-17 02:55:52', '2025-09-10 03:05:01');

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
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `sku`, `barcode`, `name`, `description`, `category_id`, `supplier_id`, `uom_id`, `cost_price`, `selling_price`, `stock_quantity`, `min_stock_level`, `max_stock_level`, `is_active`, `created_at`, `updated_at`, `img`) VALUES
(1, 'PROD001', '1234567890123', 'Laptop', 'High-performance laptop', 1, 1, 1, '2500.00', '4000.00', 0, 2, 0, 1, '2025-08-17 02:55:52', '2025-09-11 05:45:37', '-'),
(2, 'PROD002', '1234567890124', 'T-Shirt', 'Cotton t-shirt', 2, 2, 1, '15.00', '25.00', 41, 10, 0, 1, '2025-08-17 02:55:52', '2025-09-11 05:45:40', '-'),
(3, 'PROD003', '1234567890125', 'Rice', 'Premium rice 5kg', 3, 1, 3, '20.00', '30.00', 0, 20, 0, 1, '2025-08-17 02:55:52', '2025-09-11 05:45:43', '-'),
(4, 'TEST', '12345678', 'TEST', '', 3, 1, 4, '100.00', '200.00', 951, 100, 0, 1, '2025-08-18 07:11:03', '2025-09-11 05:45:46', '-'),
(6, 'asa', '13437', 'dad', '', 1, 2, 3, '10.00', '20.00', 100, 30, 0, 1, '2025-08-25 08:22:39', '2025-09-11 05:45:48', '-');

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
) ENGINE=MyISAM AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
(43, '202509060010676', 10, NULL, 1, '200.00', '0.00', '12.00', '212.00', '', 7, 'paid', NULL, '2025-09-06 15:55:58', '2025-09-06 15:55:58'),
(44, '202509070010951', 10, NULL, 1, '200.00', '0.00', '12.00', '212.00', 'online', 4, 'paid', NULL, '2025-09-06 16:02:22', '2025-09-06 16:02:22'),
(45, '202509070010950', 10, NULL, 1, '600.00', '0.00', '36.00', '636.00', 'Mobile Money', 6, 'paid', NULL, '2025-09-06 16:06:16', '2025-09-06 16:06:16'),
(46, '202509070010375', 10, NULL, 1, '200.00', '0.00', '12.00', '212.00', 'Check', 7, 'paid', NULL, '2025-09-06 16:13:55', '2025-09-06 16:13:55'),
(47, '202509070010662', 10, NULL, 1, '200.00', '0.00', '12.00', '212.00', 'Gift Card', 8, 'paid', NULL, '2025-09-07 03:10:53', '2025-09-07 03:10:53'),
(48, '202509070010097', 10, NULL, 1, '200.00', '0.00', '12.00', '212.00', 'cash', NULL, 'paid', NULL, '2025-09-07 03:12:28', '2025-09-07 03:12:28'),
(49, '202509070010091', 10, NULL, 1, '400.00', '0.00', '24.00', '424.00', 'Gift Card', 8, 'refunded', '\nRefunded on 2025-09-07 15:51:38 - Reason: test - By: System Administrator', '2025-09-07 07:02:56', '2025-09-07 07:51:38'),
(50, '202509070010698', 10, NULL, 1, '600.00', '0.00', '36.00', '636.00', 'Mobile Money', 6, 'refunded', '\nRefunded on 2025-09-07 16:16:03 - Reason: BARE PUNOH - By: System Administrator', '2025-09-07 08:01:33', '2025-09-07 08:16:03'),
(51, '202509070010912', 10, NULL, 1, '225.00', '0.00', '13.50', '238.50', 'Debit Card', 3, 'paid', NULL, '2025-09-07 08:05:05', '2025-09-07 08:05:05'),
(52, '202509080010536', 10, NULL, 1, '600.00', '0.00', '36.00', '636.00', 'Bank Transfer', 5, 'paid', NULL, '2025-09-08 01:48:08', '2025-09-08 01:48:08'),
(53, '202509080010142', 10, NULL, 1, '200.00', '0.00', '12.00', '212.00', 'Mobile Money', 6, 'paid', NULL, '2025-09-08 01:49:27', '2025-09-08 01:49:27'),
(54, '202509080010253', 10, NULL, 1, '200.00', '0.00', '12.00', '212.00', 'ADMIN', 19, 'paid', NULL, '2025-09-08 04:46:20', '2025-09-08 04:46:20'),
(55, '202509080010223', 10, NULL, 1, '3400.00', '0.00', '204.00', '3604.00', 'Debit Card', 3, 'paid', NULL, '2025-09-08 08:21:01', '2025-09-08 08:21:01'),
(56, '202509090010812', 10, NULL, 1, '200.00', '0.00', '12.00', '212.00', 'Check', 7, 'paid', NULL, '2025-09-09 01:55:40', '2025-09-09 01:55:40'),
(57, '202509090010568', 10, NULL, 1, '200.00', '0.00', '12.00', '212.00', 'Check', 7, 'paid', NULL, '2025-09-09 01:58:25', '2025-09-09 01:58:25'),
(58, '202509090010395', 10, NULL, 1, '200.00', '0.00', '12.00', '212.00', 'Bank Transfer', 5, 'refunded', '\nRefunded on 2025-09-11 01:36:40 - Reason: as - By: System Administrator', '2025-09-09 03:48:42', '2025-09-10 17:36:40'),
(60, '202509100010024', 10, NULL, 1, '200.00', '0.00', '12.00', '212.00', 'Bank Transfer', 5, 'refunded', '\nRefunded on 2025-09-11 01:36:02 - Reason: sa - By: System Administrator', '2025-09-10 04:39:12', '2025-09-10 17:36:02'),
(61, '202509100010962', 10, NULL, 1, '250.00', '0.00', '15.00', '265.00', 'Bank Transfer', 5, 'refunded', '\nRefunded on 2025-09-11 01:35:55 - Reason: as - By: System Administrator', '2025-09-10 15:39:17', '2025-09-10 17:35:55'),
(62, '202509100010896', 10, NULL, 1, '200.00', '0.00', '12.00', '212.00', 'cash', NULL, 'refunded', '\nRefunded on 2025-09-11 01:35:17 - Reason: sa - By: System Administrator', '2025-09-10 15:46:30', '2025-09-10 17:35:16');

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
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sales_sessions`
--

INSERT INTO `sales_sessions` (`id`, `user_id`, `session_start`, `session_end`, `opening_amount`, `closing_amount`, `total_sales`, `total_refunds`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(11, 2, '2025-09-10 01:40:06', NULL, '0.00', '0.00', '0.00', '0.00', 'open', NULL, '2025-09-10 01:40:06', '2025-09-10 01:40:06'),
(10, 1, '2025-09-02 02:26:35', NULL, '1000.00', '0.00', '12688.20', '901.00', 'open', '', '2025-09-02 02:26:35', '2025-09-10 17:36:40');

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
) ENGINE=MyISAM AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
(70, 62, 4, 1, '200.00', '0.00', '200.00', '2025-09-10 15:46:30');

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
) ENGINE=MyISAM AUTO_INCREMENT=90 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
(89, 6, 'in', 90, 'adjustment', 6, 'Stock adjustment (increase)', 1, '2025-09-10 18:32:12');

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
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `email`, `phone`, `role`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin123', 'System Administrator', 'admin@possystem.com', '0123456789', 'admin', 1, '2025-09-11 04:15:24', '2025-08-17 02:55:52', '2025-09-11 04:15:24'),
(2, 'cashier', 'cashier123', 'Cashier User', 'cashier@possystem.com', NULL, 'cashier', 1, '2025-09-10 01:39:21', '2025-08-17 02:55:52', '2025-09-10 01:39:21');

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
