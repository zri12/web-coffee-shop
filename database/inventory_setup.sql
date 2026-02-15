-- ========================================
-- INVENTORY MANAGEMENT TABLES
-- Add to fazriluk_coffeeshop database
-- ========================================

-- Table: ingredients
CREATE TABLE `ingredients` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `category` enum('Coffee','Milk','Syrup','Topping','Bakery','Sauce','Other') NOT NULL DEFAULT 'Other',
  `unit` enum('ml','gram','pcs') NOT NULL DEFAULT 'ml',
  `stock` decimal(10,2) NOT NULL DEFAULT 0.00,
  `minimum_stock` decimal(10,2) NOT NULL DEFAULT 100.00,
  `status` enum('Aman','Hampir Habis','Habis') NOT NULL DEFAULT 'Aman',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `ingredients_category_index` (`category`),
  KEY `ingredients_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: product_recipes
CREATE TABLE `product_recipes` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) UNSIGNED NOT NULL COMMENT 'References menus.id',
  `ingredient_id` bigint(20) UNSIGNED NOT NULL,
  `quantity_used` decimal(10,2) NOT NULL COMMENT 'Amount used per product',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_recipes_unique` (`product_id`,`ingredient_id`),
  KEY `product_id` (`product_id`),
  KEY `ingredient_id` (`ingredient_id`),
  CONSTRAINT `product_recipes_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_recipes_ibfk_2` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: ingredient_logs
CREATE TABLE `ingredient_logs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ingredient_id` bigint(20) UNSIGNED NOT NULL,
  `change_amount` decimal(10,2) NOT NULL COMMENT 'Positive for restock, negative for deduction',
  `type` enum('Order Deduct','Restock','Adjustment','Expired') NOT NULL DEFAULT 'Restock',
  `reference_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Order ID or restock reference',
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `ingredient_id` (`ingredient_id`),
  KEY `ingredient_logs_type_index` (`type`),
  KEY `ingredient_logs_created_at_index` (`created_at`),
  CONSTRAINT `ingredient_logs_ibfk_1` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- SAMPLE DATA - INGREDIENTS
-- ========================================

INSERT INTO `ingredients` (`id`, `name`, `category`, `unit`, `stock`, `minimum_stock`, `status`) VALUES
(1, 'Espresso Beans', 'Coffee', 'gram', 5000.00, 1000.00, 'Aman'),
(2, 'Fresh Milk', 'Milk', 'ml', 10000.00, 2000.00, 'Aman'),
(3, 'Hazelnut Syrup', 'Syrup', 'ml', 2000.00, 500.00, 'Aman'),
(4, 'Vanilla Syrup', 'Syrup', 'ml', 1500.00, 500.00, 'Aman'),
(5, 'Caramel Syrup', 'Syrup', 'ml', 1800.00, 500.00, 'Aman'),
(6, 'Matcha Powder', 'Other', 'gram', 1000.00, 200.00, 'Aman'),
(7, 'Whipped Cream', 'Topping', 'ml', 3000.00, 500.00, 'Aman'),
(8, 'Chocolate Chips', 'Topping', 'gram', 800.00, 200.00, 'Aman'),
(9, 'Butter', 'Bakery', 'gram', 2000.00, 500.00, 'Aman'),
(10, 'Flour', 'Bakery', 'gram', 5000.00, 1000.00, 'Aman'),
(11, 'Sugar', 'Other', 'gram', 3000.00, 500.00, 'Aman'),
(12, 'Cocoa Powder', 'Other', 'gram', 1000.00, 200.00, 'Aman'),
(13, 'Mascarpone Cheese', 'Other', 'gram', 1500.00, 300.00, 'Aman'),
(14, 'Coffee Liqueur', 'Other', 'ml', 500.00, 100.00, 'Aman'),
(15, 'Ice Cubes', 'Other', 'gram', 10000.00, 2000.00, 'Aman');

-- ========================================
-- SAMPLE DATA - PRODUCT RECIPES
-- Mapping existing menu items to ingredients
-- ========================================

-- Hazelnut Latte (ID: 1)
-- Ingredients: Espresso (30g), Fresh Milk (200ml), Hazelnut Syrup (30ml)
INSERT INTO `product_recipes` (`product_id`, `ingredient_id`, `quantity_used`) VALUES
(1, 1, 30.00),  -- Espresso Beans
(1, 2, 200.00), -- Fresh Milk
(1, 3, 30.00);  -- Hazelnut Syrup

-- Cold Brew (ID: 2)
-- Ingredients: Espresso (40g), Ice (100g)
INSERT INTO `product_recipes` (`product_id`, `ingredient_id`, `quantity_used`) VALUES
(2, 1, 40.00),  -- Espresso Beans
(2, 15, 100.00); -- Ice Cubes

-- Cappuccino (ID: 3)
-- Ingredients: Espresso (25g), Fresh Milk (150ml)
INSERT INTO `product_recipes` (`product_id`, `ingredient_id`, `quantity_used`) VALUES
(3, 1, 25.00),  -- Espresso Beans
(3, 2, 150.00); -- Fresh Milk

-- Matcha Latte (ID: 4)
-- Ingredients: Matcha Powder (15g), Fresh Milk (200ml)
INSERT INTO `product_recipes` (`product_id`, `ingredient_id`, `quantity_used`) VALUES
(4, 6, 15.00),  -- Matcha Powder
(4, 2, 200.00); -- Fresh Milk

-- Butter Croissant (ID: 5)
-- Ingredients: Flour (80g), Butter (40g), Sugar (10g)
INSERT INTO `product_recipes` (`product_id`, `ingredient_id`, `quantity_used`) VALUES
(5, 10, 80.00), -- Flour
(5, 9, 40.00),  -- Butter
(5, 11, 10.00); -- Sugar

-- Matcha Cake (ID: 6)
-- Ingredients: Flour (100g), Matcha Powder (20g), Sugar (50g), Butter (30g)
INSERT INTO `product_recipes` (`product_id`, `ingredient_id`, `quantity_used`) VALUES
(6, 10, 100.00), -- Flour
(6, 6, 20.00),   -- Matcha Powder
(6, 11, 50.00),  -- Sugar
(6, 9, 30.00);   -- Butter

-- Tiramisu (ID: 7)
-- Ingredients: Mascarpone (80g), Cocoa Powder (15g), Coffee Liqueur (20ml), Sugar (30g)
INSERT INTO `product_recipes` (`product_id`, `ingredient_id`, `quantity_used`) VALUES
(7, 13, 80.00),  -- Mascarpone Cheese
(7, 12, 15.00),  -- Cocoa Powder
(7, 14, 20.00),  -- Coffee Liqueur
(7, 11, 30.00);  -- Sugar

-- KENTANG (ID: 8) - Potato Snack
-- Ingredients: (Assuming it's french fries - simplified)
-- Note: Add potato ingredient if needed, for now using generic ingredients
INSERT INTO `product_recipes` (`product_id`, `ingredient_id`, `quantity_used`) VALUES
(8, 11, 5.00);  -- Sugar (for seasoning)

-- ========================================
-- SAMPLE INGREDIENT LOGS (Optional)
-- ========================================

INSERT INTO `ingredient_logs` (`ingredient_id`, `change_amount`, `type`, `note`) VALUES
(1, 5000.00, 'Restock', 'Initial stock - Espresso Beans'),
(2, 10000.00, 'Restock', 'Initial stock - Fresh Milk'),
(3, 2000.00, 'Restock', 'Initial stock - Hazelnut Syrup'),
(4, 1500.00, 'Restock', 'Initial stock - Vanilla Syrup'),
(5, 1800.00, 'Restock', 'Initial stock - Caramel Syrup'),
(6, 1000.00, 'Restock', 'Initial stock - Matcha Powder'),
(7, 3000.00, 'Restock', 'Initial stock - Whipped Cream'),
(8, 800.00, 'Restock', 'Initial stock - Chocolate Chips'),
(9, 2000.00, 'Restock', 'Initial stock - Butter'),
(10, 5000.00, 'Restock', 'Initial stock - Flour'),
(11, 3000.00, 'Restock', 'Initial stock - Sugar'),
(12, 1000.00, 'Restock', 'Initial stock - Cocoa Powder'),
(13, 1500.00, 'Restock', 'Initial stock - Mascarpone Cheese'),
(14, 500.00, 'Restock', 'Initial stock - Coffee Liqueur'),
(15, 10000.00, 'Restock', 'Initial stock - Ice Cubes');

-- ========================================
-- END OF INVENTORY SETUP
-- ========================================

COMMIT;
