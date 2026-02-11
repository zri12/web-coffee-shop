-- Add payment columns to orders table
-- Run this SQL script to add payment_status and payment_method columns

ALTER TABLE `orders` 
ADD COLUMN `payment_method` ENUM('cash', 'qris', 'card') NULL AFTER `order_type`,
ADD COLUMN `payment_status` ENUM('unpaid', 'paid') NOT NULL DEFAULT 'unpaid' AFTER `payment_method`;

-- Add index for payment_status for better query performance
ALTER TABLE `orders` ADD INDEX `orders_payment_status_index` (`payment_status`);
