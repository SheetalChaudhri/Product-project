-- Create admins table
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE KEY,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `email_idx` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create products table
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `description` longtext,
  `price` decimal(10, 2) NOT NULL,
  `quantity` int(11) DEFAULT 0,
  `image` varchar(255),
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create product_images table
CREATE TABLE IF NOT EXISTS `product_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_id_idx` (`product_id`),
  CONSTRAINT `fk_product_images` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create cart table
CREATE TABLE IF NOT EXISTS `cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id_idx` (`user_id`),
  KEY `product_id_idx` (`product_id`),
  CONSTRAINT `fk_cart_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Safe ALTER statements to add any missing columns (run after creating/updating tables)
-- These ensure `products` and `orders` have required columns for the application

-- Add missing columns to `products` if they don't exist
ALTER TABLE `products`
  ADD COLUMN IF NOT EXISTS `quantity` int(11) DEFAULT 0 AFTER `price`,
  ADD COLUMN IF NOT EXISTS `image` varchar(255) DEFAULT NULL AFTER `quantity`,
  ADD COLUMN IF NOT EXISTS `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

-- Add missing columns to `orders` if they don't exist
-- Add missing columns to `orders` if they don't exist (compat-safe)
-- For each column: check table exists and column missing, then ALTER without using AFTER (avoids errors if referenced columns are missing)

SET @tbl := (SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'orders');

SET @cnt := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'payment_status');
SET @sql := IF(@tbl = 0, 'SELECT "no_orders_table"', IF(@cnt > 0, 'SELECT "column_exists"', 'ALTER TABLE `orders` ADD COLUMN `payment_status` ENUM(\'pending\',\'completed\',\'failed\',\'cancelled\') DEFAULT \'pending\''));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @cnt := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'order_status');
SET @sql := IF(@tbl = 0, 'SELECT "no_orders_table"', IF(@cnt > 0, 'SELECT "column_exists"', 'ALTER TABLE `orders` ADD COLUMN `order_status` ENUM(\'pending\',\'processing\',\'shipped\',\'delivered\',\'cancelled\') DEFAULT \'pending\''));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @cnt := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'payment_method');
SET @sql := IF(@tbl = 0, 'SELECT "no_orders_table"', IF(@cnt > 0, 'SELECT "column_exists"', 'ALTER TABLE `orders` ADD COLUMN `payment_method` VARCHAR(50) DEFAULT NULL'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @cnt := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'transaction_id');
SET @sql := IF(@tbl = 0, 'SELECT "no_orders_table"', IF(@cnt > 0, 'SELECT "column_exists"', 'ALTER TABLE `orders` ADD COLUMN `transaction_id` VARCHAR(255) DEFAULT NULL'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @cnt := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'updated_at');
SET @sql := IF(@tbl = 0, 'SELECT "no_orders_table"', IF(@cnt > 0, 'SELECT "column_exists"', 'ALTER TABLE `orders` ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add missing columns to `cart` if they don't exist (session-based cart support)
ALTER TABLE `cart`
  ADD COLUMN IF NOT EXISTS `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

-- Remove legacy session_id column if present (application no longer uses session_id)
-- Use information_schema + prepared statement for compatibility with older MySQL/MariaDB
SET @cnt := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cart' AND COLUMN_NAME = 'session_id');
SET @sql := IF(@cnt > 0, 'ALTER TABLE `cart` DROP COLUMN `session_id`', 'SELECT "no_column_to_drop"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- End of safe ALTER statements
