<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Support\Str;

class MockMenuSeeder extends Seeder
{
    public function run()
    {
        // 1. Categories
        $categories = [
            ['name' => 'Coffee', 'slug' => 'coffee', 'sort_order' => 1],
            ['name' => 'Non-Coffee', 'slug' => 'non-coffee', 'sort_order' => 2],
            ['name' => 'Snack', 'slug' => 'snack', 'sort_order' => 3],
            ['name' => 'Dessert', 'slug' => 'dessert', 'sort_order' => 4],
            ['name' => 'Food', 'slug' => 'food', 'sort_order' => 5],
        ];

        foreach ($categories as $cat) {
            // Using updateOrCreate with name to avoid slug unique constraint issues on re-seed if slug changed
            Category::updateOrCreate(
                ['slug' => $cat['slug']], 
                [
                    'name' => $cat['name'],
                    'description' => $cat['name'] . ' Selection',
                    'is_active' => true,
                    'sort_order' => $cat['sort_order']
                ]
            );
        }

        // 2. Menus
        // Clear existing to avoid dupes/mess
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Menu::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $menus = [
            // Coffee
            [
                'category_slug' => 'coffee',
                'name' => 'Hazelnut Latte',
                'description' => 'Rich espresso with steamed milk and roasted hazelnut flavor.',
                'price' => 45000,
                'is_featured' => true,
                'image_url' => 'coffee-1.jpg' // Placeholder
            ],
            [
                'category_slug' => 'coffee',
                'name' => 'Cold Brew',
                'description' => 'Steeped for 20 hours for super smooth flavor.',
                'price' => 35000,
                'is_featured' => true,
                'image_url' => 'coffee-2.jpg'
            ],
            [
                'category_slug' => 'coffee',
                'name' => 'Cappuccino',
                'description' => 'Dark, rich espresso lying in wait under a smoothed and stretched layer of thick milk foam.',
                'price' => 42000,
                'is_featured' => false,
                'image_url' => 'coffee-3.jpg'
            ],
             // Non-Coffee
            [
                'category_slug' => 'non-coffee',
                'name' => 'Matcha Latte',
                'description' => 'Premium Japanese green tea with steamed milk.',
                'price' => 38000,
                'is_featured' => true,
                'image_url' => 'tea-1.jpg'
            ],
             // Snack
            [
                'category_slug' => 'snack',
                'name' => 'Butter Croissant',
                'description' => 'Flaky, buttery, and freshly baked every morning.',
                'price' => 30000,
                'is_featured' => true,
                'image_url' => 'bread-1.jpg'
            ],
             // Dessert
            [
                'category_slug' => 'dessert',
                'name' => 'Matcha Cake',
                'description' => 'Delicate layers of matcha sponge and cream.',
                'price' => 55000,
                'is_featured' => true,
                'image_url' => 'cake-1.jpg'
            ],
             [
                'category_slug' => 'dessert',
                'name' => 'Tiramisu',
                'description' => 'Classic Italian coffee-flavoured dessert.',
                'price' => 50000,
                'is_featured' => false,
                'image_url' => 'cake-2.jpg'
            ],
        ];

        foreach ($menus as $menu) {
            $cat = Category::where('slug', $menu['category_slug'])->first();
            if ($cat) {
                 Menu::create([
                    'category_id' => $cat->id,
                    'name' => $menu['name'],
                    'description' => $menu['description'],
                    'price' => $menu['price'],
                    'is_featured' => $menu['is_featured'],
                    'image_url' => $menu['image_url'], // Will show placeholder icon in view if file missing
                    'is_available' => true,
                ]);
            }
        }
    }
}
