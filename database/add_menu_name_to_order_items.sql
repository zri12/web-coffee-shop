-- ========================================
-- MIGRATION: Add menu_name to order_items
-- ========================================
-- This allows storing the menu name at the time of order to preserve historical data
-- even if the menu item name changes later

-- INSTRUCTION:
-- 1. Open phpMyAdmin or MySQL Workbench
-- 2. Select database 'cafe_db'
-- 3. Copy and run this entire SQL script

USE cafe_db;

-- Check if column already exists before adding
SET @column_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'cafe_db' 
    AND TABLE_NAME = 'order_items' 
    AND COLUMN_NAME = 'menu_name'
);

-- Add column if it doesn't exist
SET @query = IF(
    @column_exists = 0,
    'ALTER TABLE `order_items` ADD COLUMN `menu_name` VARCHAR(255) NULL AFTER `menu_id`',
    'SELECT "Column menu_name already exists" AS message'
);

PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Update existing records to populate menu_name from menus table
UPDATE `order_items` oi
JOIN `menus` m ON oi.menu_id = m.id
SET oi.menu_name = m.name
WHERE oi.menu_name IS NULL OR oi.menu_name = '';

-- Make menu_name NOT NULL after populating existing data
ALTER TABLE `order_items` 
MODIFY COLUMN `menu_name` VARCHAR(255) NOT NULL;

-- Verify the change
SHOW COLUMNS FROM order_items LIKE 'menu_name';

SELECT 'Migration completed successfully!' AS status;
