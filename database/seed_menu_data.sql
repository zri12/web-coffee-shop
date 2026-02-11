INSERT INTO categories (name, slug, description, is_active, sort_order) VALUES
('Coffee', 'coffee', 'Freshly brewed coffee', 1, 1),
('Non-Coffee', 'non-coffee', 'Refreshing beverages', 1, 2),
('Snack', 'snack', 'Light bites', 1, 3),
('Dessert', 'dessert', 'Sweet treats', 1, 4),
('Food', 'food', 'Main courses', 1, 5)
ON DUPLICATE KEY UPDATE name=name;

-- Get Category IDs (assuming standard increment, but using subqueries for safety in a real script. Here we mock for speed)
-- Clearing existing menus to avoid duplicates for this demo
DELETE FROM menus; 

INSERT INTO menus (category_id, name, description, price, is_available, is_featured, image_url) VALUES
((SELECT id FROM categories WHERE slug='coffee'), 'Hazelnut Latte', 'Rich espresso with steamed milk and roasted hazelnut flavor.', 45000, 1, 1, 'coffee-1.jpg'),
((SELECT id FROM categories WHERE slug='coffee'), 'Cold Brew', 'Steeped for 20 hours for super smooth flavor.', 35000, 1, 1, 'coffee-2.jpg'),
((SELECT id FROM categories WHERE slug='coffee'), 'Cappuccino', 'Dark, rich espresso lying in wait under a smoothed and stretched layer of thick milk foam.', 42000, 1, 0, 'coffee-3.jpg'),

((SELECT id FROM categories WHERE slug='non-coffee'), 'Matcha Latte', 'Premium Japanese green tea with steamed milk.', 38000, 1, 1, 'tea-1.jpg'),
((SELECT id FROM categories WHERE slug='non-coffee'), 'Chocolate', 'Creamy belgian chocolate milk.', 35000, 1, 0, 'choco-1.jpg'),

((SELECT id FROM categories WHERE slug='snack'), 'Butter Croissant', 'Flaky, buttery, and freshly baked every morning.', 30000, 1, 1, 'bread-1.jpg'),
((SELECT id FROM categories WHERE slug='snack'), 'Choco Cookie', 'Warm, soft, and loaded with dark chocolate chunks.', 25000, 1, 0, 'cookie-1.jpg'),

((SELECT id FROM categories WHERE slug='dessert'), 'Matcha Cake', 'Delicate layers of matcha sponge and cream.', 55000, 1, 1, 'cake-1.jpg'),
((SELECT id FROM categories WHERE slug='dessert'), 'Tiramisu', 'Classic Italian coffee-flavoured dessert.', 50000, 1, 0, 'cake-2.jpg');
