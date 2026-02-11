-- ========================================
-- SETUP SCRIPT: Enable Manual Order Feature
-- ========================================
-- This script adds all necessary columns and updates ENUM values
-- for the manual order feature to work properly

-- INSTRUCTIONS:
-- 1. Open phpMyAdmin or MySQL Workbench
-- 2. Select database 'cafe_db'
-- 3. Copy and run this entire SQL script

USE cafe_db;

-- Step 1: Fix order_type ENUM values
-- Change from ('qr', 'manual') to ('dine_in', 'takeaway')
ALTER TABLE `orders` 
MODIFY COLUMN `order_type` ENUM('qr', 'manual', 'dine_in', 'takeaway') NOT NULL DEFAULT 'dine_in';

-- Update existing records if any
UPDATE `orders` SET `order_type` = 'dine_in' WHERE `order_type` = 'qr';
UPDATE `orders` SET `order_type` = 'takeaway' WHERE `order_type` = 'manual';

-- Remove old enum values
ALTER TABLE `orders` 
MODIFY COLUMN `order_type` ENUM('dine_in', 'takeaway') NOT NULL DEFAULT 'dine_in';

-- Step 2: Add payment columns to orders table (if not exists)
-- Check if columns exist first
SET @col_exists = (SELECT COUNT(*) 
                   FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_SCHEMA = 'cafe_db' 
                   AND TABLE_NAME = 'orders' 
                   AND COLUMN_NAME = 'payment_method');

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `orders` 
     ADD COLUMN `payment_method` ENUM(''cash'', ''qris'', ''card'') NULL AFTER `order_type`,
     ADD COLUMN `payment_status` ENUM(''unpaid'', ''paid'') NOT NULL DEFAULT ''unpaid'' AFTER `payment_method`,
     ADD INDEX `orders_payment_status_index` (`payment_status`);',
    'SELECT "Payment columns already exist" AS message;');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 3: Add menu_name column to order_items (if not exists)
SET @col_exists = (SELECT COUNT(*) 
                   FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_SCHEMA = 'cafe_db' 
                   AND TABLE_NAME = 'order_items' 
                   AND COLUMN_NAME = 'menu_name');

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `order_items` 
     ADD COLUMN `menu_name` VARCHAR(255) NULL AFTER `menu_id`;',
    'SELECT "menu_name column already exists" AS message;');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 4: Add price column to order_items (alternative to unit_price)
SET @col_exists = (SELECT COUNT(*) 
                   FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_SCHEMA = 'cafe_db' 
                   AND TABLE_NAME = 'order_items' 
                   AND COLUMN_NAME = 'price');

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `order_items` 
     ADD COLUMN `price` DECIMAL(12, 2) NULL AFTER `quantity`;',
    'SELECT "price column already exists" AS message;');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 5: Make unit_price nullable to support custom pricing
ALTER TABLE `order_items` 
MODIFY COLUMN `unit_price` DECIMAL(12, 2) NULL;

-- Step 6: Update existing order_items to populate price from unit_price
UPDATE `order_items` SET `price` = `unit_price` WHERE `price` IS NULL;
UPDATE `order_items` SET `unit_price` = `price` WHERE `unit_price` IS NULL;

-- Verify the changes
SELECT 'Setup completed successfully!' AS status;

SELECT 'Checking orders table structure...' AS step;
SHOW COLUMNS FROM orders WHERE Field IN ('order_type', 'payment_method', 'payment_status');

SELECT 'Checking order_items table structure...' AS step;
SHOW COLUMNS FROM order_items WHERE Field IN ('menu_name', 'price', 'unit_price');

SELECT CONCAT('Total orders in database: ', COUNT(*)) AS info FROM orders;
SELECT CONCAT('Total order items in database: ', COUNT(*)) AS info FROM order_items;
