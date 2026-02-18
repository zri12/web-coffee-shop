-- Seed/refresh product recipes & add-ons using name-based lookups (id-agnostic).
-- Safe to rerun; uses ON DUPLICATE KEY UPDATE for product_recipes
-- and full JSON replace for menu add-ons.

-- 1) Ensure supporting ingredients exist
INSERT INTO ingredients (name, category, unit, stock, minimum_stock, status, created_at, updated_at)
SELECT 'Hazelnut Syrup', 'Flavor', 'ml', 2000, 300, 'Aman', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM ingredients WHERE name = 'Hazelnut Syrup');

INSERT INTO ingredients (name, category, unit, stock, minimum_stock, status, created_at, updated_at)
SELECT 'Matcha Powder', 'Flavor', 'gram', 2000, 200, 'Aman', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM ingredients WHERE name = 'Matcha Powder');

INSERT INTO ingredients (name, category, unit, stock, minimum_stock, status, created_at, updated_at)
SELECT 'Mineral Water', 'Other', 'ml', 10000, 2000, 'Aman', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM ingredients WHERE name = 'Mineral Water');

INSERT INTO ingredients (name, category, unit, stock, minimum_stock, status, created_at, updated_at)
SELECT 'Dough Base', 'Bakery', 'pcs', 200, 50, 'Aman', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM ingredients WHERE name = 'Dough Base');

-- 2) Recipes per product (quantities per 1 serving)
INSERT INTO product_recipes (product_id, ingredient_id, quantity_used, created_at, updated_at)
SELECT m.id, i.id, 18, NOW(), NOW()
FROM menus m JOIN ingredients i ON i.name = 'Espresso'
WHERE m.name = 'Hazelnut Latte'
ON DUPLICATE KEY UPDATE quantity_used = VALUES(quantity_used);

INSERT INTO product_recipes (product_id, ingredient_id, quantity_used, created_at, updated_at)
SELECT m.id, i.id, 150, NOW(), NOW()
FROM menus m JOIN ingredients i ON i.name = 'Susu (Milk)'
WHERE m.name = 'Hazelnut Latte'
ON DUPLICATE KEY UPDATE quantity_used = VALUES(quantity_used);

INSERT INTO product_recipes (product_id, ingredient_id, quantity_used, created_at, updated_at)
SELECT m.id, i.id, 20, NOW(), NOW()
FROM menus m JOIN ingredients i ON i.name = 'Hazelnut Syrup'
WHERE m.name = 'Hazelnut Latte'
ON DUPLICATE KEY UPDATE quantity_used = VALUES(quantity_used);

INSERT INTO product_recipes (product_id, ingredient_id, quantity_used, created_at, updated_at)
SELECT m.id, i.id, 10, NOW(), NOW()
FROM menus m JOIN ingredients i ON i.name = 'Gula Cair (Sugar Syrup)'
WHERE m.name = 'Hazelnut Latte'
ON DUPLICATE KEY UPDATE quantity_used = VALUES(quantity_used);

-- Cold Brew
INSERT INTO product_recipes (product_id, ingredient_id, quantity_used, created_at, updated_at)
SELECT m.id, i.id, 30, NOW(), NOW()
FROM menus m JOIN ingredients i ON i.name = 'Coffee Beans'
WHERE m.name = 'Cold Brew'
ON DUPLICATE KEY UPDATE quantity_used = VALUES(quantity_used);

INSERT INTO product_recipes (product_id, ingredient_id, quantity_used, created_at, updated_at)
SELECT m.id, i.id, 250, NOW(), NOW()
FROM menus m JOIN ingredients i ON i.name = 'Mineral Water'
WHERE m.name = 'Cold Brew'
ON DUPLICATE KEY UPDATE quantity_used = VALUES(quantity_used);

INSERT INTO product_recipes (product_id, ingredient_id, quantity_used, created_at, updated_at)
SELECT m.id, i.id, 150, NOW(), NOW()
FROM menus m JOIN ingredients i ON i.name = 'Es Batu (Ice)'
WHERE m.name = 'Cold Brew'
ON DUPLICATE KEY UPDATE quantity_used = VALUES(quantity_used);

-- Cappuccino
INSERT INTO product_recipes (product_id, ingredient_id, quantity_used, created_at, updated_at)
SELECT m.id, i.id, 18, NOW(), NOW()
FROM menus m JOIN ingredients i ON i.name = 'Espresso'
WHERE m.name = 'Cappuccino'
ON DUPLICATE KEY UPDATE quantity_used = VALUES(quantity_used);

INSERT INTO product_recipes (product_id, ingredient_id, quantity_used, created_at, updated_at)
SELECT m.id, i.id, 120, NOW(), NOW()
FROM menus m JOIN ingredients i ON i.name = 'Susu (Milk)'
WHERE m.name = 'Cappuccino'
ON DUPLICATE KEY UPDATE quantity_used = VALUES(quantity_used);

INSERT INTO product_recipes (product_id, ingredient_id, quantity_used, created_at, updated_at)
SELECT m.id, i.id, 10, NOW(), NOW()
FROM menus m JOIN ingredients i ON i.name = 'Gula Cair (Sugar Syrup)'
WHERE m.name = 'Cappuccino'
ON DUPLICATE KEY UPDATE quantity_used = VALUES(quantity_used);

-- Matcha Latte
INSERT INTO product_recipes (product_id, ingredient_id, quantity_used, created_at, updated_at)
SELECT m.id, i.id, 10, NOW(), NOW()
FROM menus m JOIN ingredients i ON i.name = 'Matcha Powder'
WHERE m.name = 'Matcha Latte'
ON DUPLICATE KEY UPDATE quantity_used = VALUES(quantity_used);

INSERT INTO product_recipes (product_id, ingredient_id, quantity_used, created_at, updated_at)
SELECT m.id, i.id, 150, NOW(), NOW()
FROM menus m JOIN ingredients i ON i.name = 'Susu (Milk)'
WHERE m.name = 'Matcha Latte'
ON DUPLICATE KEY UPDATE quantity_used = VALUES(quantity_used);

INSERT INTO product_recipes (product_id, ingredient_id, quantity_used, created_at, updated_at)
SELECT m.id, i.id, 15, NOW(), NOW()
FROM menus m JOIN ingredients i ON i.name = 'Gula Cair (Sugar Syrup)'
WHERE m.name = 'Matcha Latte'
ON DUPLICATE KEY UPDATE quantity_used = VALUES(quantity_used);

-- Butter Croissant
INSERT INTO product_recipes (product_id, ingredient_id, quantity_used, created_at, updated_at)
SELECT m.id, i.id, 80, NOW(), NOW()
FROM menus m JOIN ingredients i ON i.name = 'Tepung (Flour)'
WHERE m.name = 'Butter Croissant'
ON DUPLICATE KEY UPDATE quantity_used = VALUES(quantity_used);

INSERT INTO product_recipes (product_id, ingredient_id, quantity_used, created_at, updated_at)
SELECT m.id, i.id, 20, NOW(), NOW()
FROM menus m JOIN ingredients i ON i.name = 'Butter'
WHERE m.name = 'Butter Croissant'
ON DUPLICATE KEY UPDATE quantity_used = VALUES(quantity_used);

INSERT INTO product_recipes (product_id, ingredient_id, quantity_used, created_at, updated_at)
SELECT m.id, i.id, 1, NOW(), NOW()
FROM menus m JOIN ingredients i ON i.name = 'Telur (Eggs)'
WHERE m.name = 'Butter Croissant'
ON DUPLICATE KEY UPDATE quantity_used = VALUES(quantity_used);

-- Matcha Cake
INSERT INTO product_recipes (product_id, ingredient_id, quantity_used, created_at, updated_at)
SELECT m.id, i.id, 8, NOW(), NOW()
FROM menus m JOIN ingredients i ON i.name = 'Matcha Powder'
WHERE m.name = 'Matcha Cake'
ON DUPLICATE KEY UPDATE quantity_used = VALUES(quantity_used);

INSERT INTO product_recipes (product_id, ingredient_id, quantity_used, created_at, updated_at)
SELECT m.id, i.id, 90, NOW(), NOW()
FROM menus m JOIN ingredients i ON i.name = 'Tepung (Flour)'
WHERE m.name = 'Matcha Cake'
ON DUPLICATE KEY UPDATE quantity_used = VALUES(quantity_used);

INSERT INTO product_recipes (product_id, ingredient_id, quantity_used, created_at, updated_at)
SELECT m.id, i.id, 1, NOW(), NOW()
FROM menus m JOIN ingredients i ON i.name = 'Telur (Eggs)'
WHERE m.name = 'Matcha Cake'
ON DUPLICATE KEY UPDATE quantity_used = VALUES(quantity_used);

INSERT INTO product_recipes (product_id, ingredient_id, quantity_used, created_at, updated_at)
SELECT m.id, i.id, 30, NOW(), NOW()
FROM menus m JOIN ingredients i ON i.name = 'Gula Pasir (Sugar)'
WHERE m.name = 'Matcha Cake'
ON DUPLICATE KEY UPDATE quantity_used = VALUES(quantity_used);

INSERT INTO product_recipes (product_id, ingredient_id, quantity_used, created_at, updated_at)
SELECT m.id, i.id, 15, NOW(), NOW()
FROM menus m JOIN ingredients i ON i.name = 'Butter'
WHERE m.name = 'Matcha Cake'
ON DUPLICATE KEY UPDATE quantity_used = VALUES(quantity_used);

-- Tiramisu
INSERT INTO product_recipes (product_id, ingredient_id, quantity_used, created_at, updated_at)
SELECT m.id, i.id, 30, NOW(), NOW()
FROM menus m JOIN ingredients i ON i.name = 'Espresso'
WHERE m.name = 'Tiramisu'
ON DUPLICATE KEY UPDATE quantity_used = VALUES(quantity_used);

INSERT INTO product_recipes (product_id, ingredient_id, quantity_used, created_at, updated_at)
SELECT m.id, i.id, 25, NOW(), NOW()
FROM menus m JOIN ingredients i ON i.name = 'Gula Pasir (Sugar)'
WHERE m.name = 'Tiramisu'
ON DUPLICATE KEY UPDATE quantity_used = VALUES(quantity_used);

INSERT INTO product_recipes (product_id, ingredient_id, quantity_used, created_at, updated_at)
SELECT m.id, i.id, 1, NOW(), NOW()
FROM menus m JOIN ingredients i ON i.name = 'Telur (Eggs)'
WHERE m.name = 'Tiramisu'
ON DUPLICATE KEY UPDATE quantity_used = VALUES(quantity_used);

INSERT INTO product_recipes (product_id, ingredient_id, quantity_used, created_at, updated_at)
SELECT m.id, i.id, 80, NOW(), NOW()
FROM menus m JOIN ingredients i ON i.name = 'Susu (Milk)'
WHERE m.name = 'Tiramisu'
ON DUPLICATE KEY UPDATE quantity_used = VALUES(quantity_used);

-- 3) Add-ons JSON presets per menu
UPDATE menus SET addons = JSON_ARRAY(
    JSON_OBJECT('name','Extra Shot','price',5000),
    JSON_OBJECT('name','Whipped Cream','price',7000),
    JSON_OBJECT('name','Less Sugar','price',0)
) WHERE name IN ('Hazelnut Latte','Cappuccino','Cold Brew','Matcha Latte');

UPDATE menus SET addons = JSON_ARRAY(
    JSON_OBJECT('name','Extra Butter','price',5000),
    JSON_OBJECT('name','Warmed','price',0)
) WHERE name = 'Butter Croissant';

UPDATE menus SET addons = JSON_ARRAY(
    JSON_OBJECT('name','Extra Cream','price',7000),
    JSON_OBJECT('name','Birthday Topper','price',10000)
) WHERE name IN ('Matcha Cake','Tiramisu');
