-- Migration: Add options column to order_items table
-- This will store customization options like temperature, ice level, sugar, etc.

-- Check if column already exists
SET @sql = IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE table_name = 'order_items' 
       AND table_schema = DATABASE() 
       AND column_name = 'options') > 0,
    'SELECT "Column options already exists" as message',
    'ALTER TABLE order_items ADD COLUMN options JSON NULL COMMENT "Customization options (temperature, ice, sugar, etc.)" AFTER notes'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Show the updated table structure
DESCRIBE order_items;

-- Sample data structure for options column:
-- {
--   "temperature": "ice",
--   "iceLevel": "normal", 
--   "sugarLevel": "normal",
--   "size": "regular",
--   "spiceLevel": "mild",
--   "portion": "regular",
--   "addOns": ["extra-shot", "whipped-cream"],
--   "sauces": ["ketchup", "chili"],
--   "toppings": ["chocolate", "caramel"],
--   "specialRequest": "Extra hot please"
-- }