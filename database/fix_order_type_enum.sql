-- ========================================
-- MIGRATION: Fix order_type ENUM values
-- ========================================
-- Change order_type ENUM from ('qr', 'manual') to ('dine_in', 'takeaway')
-- This matches the application logic in OrderController

-- INSTRUCTION:
-- 1. Open phpMyAdmin or MySQL Workbench
-- 2. Select database 'cafe_db'
-- 3. Copy and run this entire SQL script

USE cafe_db;

-- First, update existing records to match new ENUM values
UPDATE `orders` SET `order_type` = 'dine_in' WHERE `order_type` = 'qr';
UPDATE `orders` SET `order_type` = 'takeaway' WHERE `order_type` = 'manual';

-- Change the ENUM type
ALTER TABLE `orders` 
MODIFY COLUMN `order_type` ENUM('dine_in', 'takeaway') NOT NULL DEFAULT 'takeaway';

-- Verify the change
SHOW COLUMNS FROM orders LIKE 'order_type';

SELECT 'Migration completed successfully! order_type now supports dine_in and takeaway' AS status;
