-- Cafe Web Ordering System - Database Schema
-- MySQL Database

-- ==============================================
-- Drop tables if exists (untuk fresh install)
-- ==============================================
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `payments`;
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `tables`;
DROP TABLE IF EXISTS `menus`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `personal_access_tokens`;
DROP TABLE IF EXISTS `users`;

SET FOREIGN_KEY_CHECKS = 1;

-- ==============================================
-- Create Tables
-- ==============================================

-- Users Table (Staff: Admin, Cashier, Manager)
CREATE TABLE `users` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `email_verified_at` TIMESTAMP NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'cashier', 'manager') NOT NULL DEFAULT 'cashier',
    `remember_token` VARCHAR(100) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Personal Access Tokens (Laravel Sanctum)
CREATE TABLE `personal_access_tokens` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `tokenable_type` VARCHAR(255) NOT NULL,
    `tokenable_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `token` VARCHAR(64) NOT NULL UNIQUE,
    `abilities` TEXT NULL,
    `last_used_at` TIMESTAMP NULL,
    `expires_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`, `tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Categories Table
CREATE TABLE `categories` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `description` TEXT NULL,
    `is_active` BOOLEAN NOT NULL DEFAULT TRUE,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Menus Table
CREATE TABLE `menus` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `category_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `price` DECIMAL(12, 2) NOT NULL,
    `image_url` VARCHAR(500) NULL,
    `is_available` BOOLEAN NOT NULL DEFAULT TRUE,
    `is_featured` BOOLEAN NOT NULL DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tables Table (Cafe Tables with QR Codes)
CREATE TABLE `tables` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `table_number` VARCHAR(20) NOT NULL UNIQUE,
    `capacity` INT NOT NULL DEFAULT 4,
    `status` ENUM('available', 'occupied', 'reserved') NOT NULL DEFAULT 'available',
    `qr_code_path` VARCHAR(500) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Orders Table
CREATE TABLE `orders` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `order_number` VARCHAR(50) NOT NULL UNIQUE,
    `user_id` BIGINT UNSIGNED NULL COMMENT 'Cashier who created manual order',
    `customer_name` VARCHAR(255) NOT NULL,
    `customer_phone` VARCHAR(20) NULL,
    `table_number` INT NOT NULL,
    `notes` TEXT NULL,
    `status` ENUM('pending', 'processing', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    `total_amount` DECIMAL(12, 2) NOT NULL DEFAULT 0,
    `order_type` ENUM('qr', 'manual') NOT NULL DEFAULT 'qr',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `orders_status_index` (`status`),
    INDEX `orders_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Order Items Table
CREATE TABLE `order_items` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `order_id` BIGINT UNSIGNED NOT NULL,
    `menu_id` BIGINT UNSIGNED NOT NULL,
    `quantity` INT NOT NULL DEFAULT 1,
    `unit_price` DECIMAL(12, 2) NOT NULL,
    `subtotal` DECIMAL(12, 2) NOT NULL,
    `notes` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`menu_id`) REFERENCES `menus`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Payments Table
CREATE TABLE `payments` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `order_id` BIGINT UNSIGNED NOT NULL,
    `method` ENUM('cash', 'qris', 'transfer') NOT NULL DEFAULT 'cash',
    `status` ENUM('pending', 'paid', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
    `amount` DECIMAL(12, 2) NOT NULL,
    `midtrans_transaction_id` VARCHAR(255) NULL,
    `midtrans_order_id` VARCHAR(255) NULL,
    `midtrans_response` JSON NULL,
    `paid_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    INDEX `payments_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- Seed Data
-- ==============================================

-- Default Admin User (password: admin123)
INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES
('Admin Cafe', 'admin@cafe.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Cashier 1', 'cashier@cafe.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cashier'),
('Manager', 'manager@cafe.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager');

-- Tables (Sample cafe tables)
INSERT INTO `tables` (`table_number`, `capacity`, `status`) VALUES
('1', 2, 'available'),
('2', 2, 'available'),
('3', 4, 'available'),
('4', 4, 'available'),
('5', 4, 'available'),
('6', 4, 'available'),
('7', 6, 'available'),
('8', 6, 'available'),
('9', 8, 'available'),
('10', 8, 'available'),
('VIP-1', 4, 'available'),
('VIP-2', 6, 'available'),
('Outdoor-1', 4, 'available'),
('Outdoor-2', 4, 'available'),
('Bar-1', 2, 'available');

-- Categories
INSERT INTO `categories` (`name`, `slug`, `description`, `sort_order`) VALUES
('Coffee', 'coffee', 'Berbagai pilihan kopi premium', 1),
('Non-Coffee', 'non-coffee', 'Minuman non-kopi yang menyegarkan', 2),
('Snack', 'snack', 'Camilan ringan', 3),
('Dessert', 'dessert', 'Makanan penutup', 4),
('Food', 'food', 'Makanan berat', 5);

-- Menus - Coffee
INSERT INTO `menus` (`category_id`, `name`, `description`, `price`, `is_available`, `is_featured`) VALUES
(1, 'Espresso', 'Espresso murni dengan crema yang sempurna', 25000, TRUE, FALSE),
(1, 'Americano', 'Espresso dengan air panas, cita rasa klasik', 28000, TRUE, FALSE),
(1, 'Cappuccino', 'Espresso, steamed milk, dan foam yang lembut', 35000, TRUE, TRUE),
(1, 'Caffe Latte', 'Espresso dengan susu steamed yang creamy', 35000, TRUE, TRUE),
(1, 'Mocha', 'Espresso dengan cokelat dan susu, manis dan nikmat', 40000, TRUE, TRUE),
(1, 'Vanilla Latte', 'Latte dengan sirup vanilla yang harum', 38000, TRUE, FALSE),
(1, 'Caramel Macchiato', 'Espresso, vanilla, susu, dan caramel drizzle', 42000, TRUE, TRUE),
(1, 'Cold Brew', 'Kopi dingin yang di-brew selama 18 jam', 35000, TRUE, FALSE),
(1, 'Affogato', 'Espresso panas dengan es krim vanilla', 45000, TRUE, FALSE);

-- Menus - Non-Coffee
INSERT INTO `menus` (`category_id`, `name`, `description`, `price`, `is_available`, `is_featured`) VALUES
(2, 'Matcha Latte', 'Matcha premium Jepang dengan susu steamed', 38000, TRUE, TRUE),
(2, 'Chocolate', 'Cokelat hangat dengan susu creamy', 32000, TRUE, FALSE),
(2, 'Fresh Orange Juice', 'Jus jeruk segar tanpa tambahan gula', 28000, TRUE, FALSE),
(2, 'Lemon Tea', 'Teh lemon yang menyegarkan', 22000, TRUE, FALSE),
(2, 'Green Tea Latte', 'Teh hijau dengan susu yang lembut', 35000, TRUE, FALSE);

-- Menus - Snack
INSERT INTO `menus` (`category_id`, `name`, `description`, `price`, `is_available`, `is_featured`) VALUES
(3, 'Croissant', 'Croissant butter yang crispy dan flaky', 28000, TRUE, TRUE),
(3, 'Almond Croissant', 'Croissant dengan filling almond cream', 35000, TRUE, FALSE),
(3, 'Cheese Cake Slice', 'New York cheesecake yang creamy', 38000, TRUE, FALSE),
(3, 'Chocolate Brownie', 'Brownie cokelat yang fudgy', 32000, TRUE, FALSE),
(3, 'French Fries', 'Kentang goreng renyah dengan saus', 25000, TRUE, FALSE);

-- Menus - Dessert
INSERT INTO `menus` (`category_id`, `name`, `description`, `price`, `is_available`, `is_featured`) VALUES
(4, 'Tiramisu', 'Classic Italian dessert dengan mascarpone', 45000, TRUE, TRUE),
(4, 'Panna Cotta', 'Italian cream dessert dengan berry sauce', 42000, TRUE, FALSE),
(4, 'Chocolate Lava Cake', 'Warm chocolate cake dengan molten center', 48000, TRUE, FALSE);

-- Menus - Food
INSERT INTO `menus` (`category_id`, `name`, `description`, `price`, `is_available`, `is_featured`) VALUES
(5, 'Nasi Goreng Special', 'Nasi goreng dengan telur, ayam, dan kerupuk', 45000, TRUE, TRUE),
(5, 'Mie Goreng', 'Mie goreng dengan sayuran dan telur', 40000, TRUE, FALSE),
(5, 'Chicken Sandwich', 'Grilled chicken breast dengan salad', 48000, TRUE, FALSE),
(5, 'Club Sandwich', 'Triple decker sandwich dengan ham dan cheese', 52000, TRUE, FALSE),
(5, 'Pasta Carbonara', 'Spaghetti dengan creamy carbonara sauce', 55000, TRUE, FALSE);

-- Sample Orders (optional - untuk testing)
INSERT INTO `orders` (`order_number`, `customer_name`, `customer_phone`, `table_number`, `status`, `total_amount`, `order_type`) VALUES
('ORD-SAMPLE001', 'Budi Santoso', '081234567890', 5, 'completed', 85000, 'qr'),
('ORD-SAMPLE002', 'Siti Rahayu', '081987654321', 3, 'processing', 55000, 'qr'),
('ORD-SAMPLE003', 'Ahmad Pratama', '082112345678', 8, 'pending', 120000, 'manual');

-- Sample Order Items
INSERT INTO `order_items` (`order_id`, `menu_id`, `quantity`, `unit_price`, `subtotal`) VALUES
(1, 3, 2, 35000, 70000),  -- 2x Cappuccino
(1, 16, 1, 28000, 28000), -- 1x Croissant (adjusted to match)
(2, 10, 1, 38000, 38000), -- 1x Matcha Latte
(2, 13, 1, 22000, 22000), -- 1x Lemon Tea (adjusted)
(3, 5, 2, 40000, 80000),  -- 2x Mocha
(3, 24, 1, 45000, 45000); -- 1x Nasi Goreng

-- Sample Payments
INSERT INTO `payments` (`order_id`, `method`, `status`, `amount`, `paid_at`) VALUES
(1, 'qris', 'paid', 85000, NOW()),
(2, 'cash', 'paid', 55000, NOW()),
(3, 'cash', 'pending', 120000, NULL);

-- ==============================================
-- Views (Optional - untuk reporting)
-- ==============================================

-- Daily Sales Summary View
CREATE OR REPLACE VIEW `v_daily_sales` AS
SELECT 
    DATE(o.created_at) AS sale_date,
    COUNT(DISTINCT o.id) AS total_orders,
    SUM(o.total_amount) AS total_revenue,
    AVG(o.total_amount) AS avg_order_value
FROM orders o
WHERE o.status = 'completed'
GROUP BY DATE(o.created_at);

-- Best Selling Items View
CREATE OR REPLACE VIEW `v_best_sellers` AS
SELECT 
    m.id,
    m.name,
    c.name AS category_name,
    SUM(oi.quantity) AS total_sold,
    SUM(oi.subtotal) AS total_revenue
FROM order_items oi
JOIN menus m ON oi.menu_id = m.id
JOIN categories c ON m.category_id = c.id
JOIN orders o ON oi.order_id = o.id
WHERE o.status = 'completed'
GROUP BY m.id, m.name, c.name
ORDER BY total_sold DESC;

-- ==============================================
-- Usage Instructions
-- ==============================================
-- 1. Create database: CREATE DATABASE cafe_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- 2. Use database: USE cafe_db;
-- 3. Run this script: SOURCE /path/to/cafe_db.sql;
--
-- Default login credentials:
-- Admin: admin@cafe.com / password (hashed with bcrypt)
-- Cashier: cashier@cafe.com / password
-- Manager: manager@cafe.com / password
