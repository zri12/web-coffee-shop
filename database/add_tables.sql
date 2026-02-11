-- Table Management Enhancement SQL
-- Run this after the main cafe_db.sql has been executed
-- This adds the tables table and sample data

-- Create Tables Table (if not exists)
CREATE TABLE IF NOT EXISTS `tables` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `table_number` VARCHAR(20) NOT NULL UNIQUE,
    `capacity` INT NOT NULL DEFAULT 4,
    `status` ENUM('available', 'occupied', 'reserved') NOT NULL DEFAULT 'available',
    `qr_code_path` VARCHAR(500) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert Sample Tables (15 tables)
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
('Bar-1', 2, 'available')
ON DUPLICATE KEY UPDATE `table_number` = `table_number`;

-- Usage Instructions:
-- 1. Make sure cafe_db.sql has been run first
-- 2. Run this file: SOURCE /path/to/add_tables.sql;
-- 3. Verify tables: SELECT * FROM tables;
