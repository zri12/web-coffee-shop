<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Menu;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample ingredients for coffee shop
        $ingredients = [
            // Coffee & Espresso
            ['name' => 'Espresso', 'category' => 'Coffee', 'unit' => 'gram', 'stock' => 5000, 'minimum_stock' => 500],
            ['name' => 'Coffee Beans', 'category' => 'Coffee', 'unit' => 'gram', 'stock' => 10000, 'minimum_stock' => 1000],
            
            // Dairy
            ['name' => 'Susu (Milk)', 'category' => 'Dairy', 'unit' => 'ml', 'stock' => 15000, 'minimum_stock' => 2000],
            ['name' => 'Whipped Cream', 'category' => 'Dairy', 'unit' => 'ml', 'stock' => 2000, 'minimum_stock' => 300],
            
            // Sweeteners
            ['name' => 'Gula Cair (Sugar Syrup)', 'category' => 'Sweetener', 'unit' => 'ml', 'stock' => 5000, 'minimum_stock' => 500],
            ['name' => 'Gula Pasir (Sugar)', 'category' => 'Sweetener', 'unit' => 'gram', 'stock' => 3000, 'minimum_stock' => 500],
            ['name' => 'Madu (Honey)', 'category' => 'Sweetener', 'unit' => 'ml', 'stock' => 1000, 'minimum_stock' => 200],
            
            // Bakery Ingredients
            ['name' => 'Tepung (Flour)', 'category' => 'Bakery', 'unit' => 'gram', 'stock' => 20000, 'minimum_stock' => 3000],
            ['name' => 'Butter', 'category' => 'Bakery', 'unit' => 'gram', 'stock' => 5000, 'minimum_stock' => 500],
            ['name' => 'Telur (Eggs)', 'category' => 'Bakery', 'unit' => 'pcs', 'stock' => 200, 'minimum_stock' => 30],
            
            // Flavors & Syrups
            ['name' => 'Vanilla Syrup', 'category' => 'Flavor', 'unit' => 'ml', 'stock' => 2000, 'minimum_stock' => 300],
            ['name' => 'Caramel Syrup', 'category' => 'Flavor', 'unit' => 'ml', 'stock' => 2000, 'minimum_stock' => 300],
            ['name' => 'Chocolate Syrup', 'category' => 'Flavor', 'unit' => 'ml', 'stock' => 2000, 'minimum_stock' => 300],
            
            // Others
            ['name' => 'Es Batu (Ice)', 'category' => 'Other', 'unit' => 'gram', 'stock' => 50000, 'minimum_stock' => 5000],
            ['name' => 'Coklat Bubuk (Cocoa Powder)', 'category' => 'Other', 'unit' => 'gram', 'stock' => 1000, 'minimum_stock' => 200],
        ];

        foreach ($ingredients as $ingredient) {
            // Calculate status based on stock
            $status = 'Aman';
            if ($ingredient['stock'] <= 0) {
                $status = 'Habis';
            } elseif ($ingredient['stock'] <= $ingredient['minimum_stock']) {
                $status = 'Hampir Habis';
            }
            
            DB::table('ingredients')->insert([
                'name' => $ingredient['name'],
                'category' => $ingredient['category'],
                'unit' => $ingredient['unit'],
                'stock' => $ingredient['stock'],
                'minimum_stock' => $ingredient['minimum_stock'],
                'status' => $status,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Sample recipes for common coffee drinks
        // Get ingredient IDs
        $espresso = DB::table('ingredients')->where('name', 'Espresso')->first();
        $milk = DB::table('ingredients')->where('name', 'Susu (Milk)')->first();
        $sugarSyrup = DB::table('ingredients')->where('name', 'Gula Cair (Sugar Syrup)')->first();
        $vanilla = DB::table('ingredients')->where('name', 'Vanilla Syrup')->first();
        $caramel = DB::table('ingredients')->where('name', 'Caramel Syrup')->first();
        $whippedCream = DB::table('ingredients')->where('name', 'Whipped Cream')->first();
        $flour = DB::table('ingredients')->where('name', 'Tepung (Flour)')->first();
        $butter = DB::table('ingredients')->where('name', 'Butter')->first();
        $sugar = DB::table('ingredients')->where('name', 'Gula Pasir (Sugar)')->first();
        $eggs = DB::table('ingredients')->where('name', 'Telur (Eggs)')->first();

        // Find menu items (if they exist)
        $recipes = [];

        // Latte recipe
        $latte = Menu::where('name', 'LIKE', '%Latte%')->first();
        if ($latte && $espresso && $milk) {
            $recipes[] = [
                'product_id' => $latte->id,
                'ingredient_id' => $espresso->id,
                'quantity_used' => 18, // 18 grams
            ];
            $recipes[] = [
                'product_id' => $latte->id,
                'ingredient_id' => $milk->id,
                'quantity_used' => 150, // 150 ml
            ];
        }

        // Cappuccino recipe
        $cappuccino = Menu::where('name', 'LIKE', '%Cappuccino%')->first();
        if ($cappuccino && $espresso && $milk) {
            $recipes[] = [
                'product_id' => $cappuccino->id,
                'ingredient_id' => $espresso->id,
                'quantity_used' => 18,
            ];
            $recipes[] = [
                'product_id' => $cappuccino->id,
                'ingredient_id' => $milk->id,
                'quantity_used' => 100,
            ];
        }

        // Vanilla Latte recipe
        $vanillaLatte = Menu::where('name', 'LIKE', '%Vanilla%')->where('name', 'LIKE', '%Latte%')->first();
        if ($vanillaLatte && $espresso && $milk && $vanilla) {
            $recipes[] = [
                'product_id' => $vanillaLatte->id,
                'ingredient_id' => $espresso->id,
                'quantity_used' => 18,
            ];
            $recipes[] = [
                'product_id' => $vanillaLatte->id,
                'ingredient_id' => $milk->id,
                'quantity_used' => 150,
            ];
            $recipes[] = [
                'product_id' => $vanillaLatte->id,
                'ingredient_id' => $vanilla->id,
                'quantity_used' => 20,
            ];
        }

        // Caramel Macchiato recipe
        $caramelMacchiato = Menu::where('name', 'LIKE', '%Caramel%')->first();
        if ($caramelMacchiato && $espresso && $milk && $caramel && $whippedCream) {
            $recipes[] = [
                'product_id' => $caramelMacchiato->id,
                'ingredient_id' => $espresso->id,
                'quantity_used' => 18,
            ];
            $recipes[] = [
                'product_id' => $caramelMacchiato->id,
                'ingredient_id' => $milk->id,
                'quantity_used' => 150,
            ];
            $recipes[] = [
                'product_id' => $caramelMacchiato->id,
                'ingredient_id' => $caramel->id,
                'quantity_used' => 20,
            ];
            $recipes[] = [
                'product_id' => $caramelMacchiato->id,
                'ingredient_id' => $whippedCream->id,
                'quantity_used' => 30,
            ];
        }

        // Croissant recipe (if exists)
        $croissant = Menu::where('name', 'LIKE', '%Croissant%')->first();
        if ($croissant && $flour && $butter && $sugar && $eggs) {
            $recipes[] = [
                'product_id' => $croissant->id,
                'ingredient_id' => $flour->id,
                'quantity_used' => 100,
            ];
            $recipes[] = [
                'product_id' => $croissant->id,
                'ingredient_id' => $butter->id,
                'quantity_used' => 50,
            ];
            $recipes[] = [
                'product_id' => $croissant->id,
                'ingredient_id' => $sugar->id,
                'quantity_used' => 20,
            ];
            $recipes[] = [
                'product_id' => $croissant->id,
                'ingredient_id' => $eggs->id,
                'quantity_used' => 1,
            ];
        }

        // Insert recipes
        foreach ($recipes as $recipe) {
            DB::table('product_recipes')->insert([
                'product_id' => $recipe['product_id'],
                'ingredient_id' => $recipe['ingredient_id'],
                'quantity_used' => $recipe['quantity_used'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ Ingredients and sample recipes seeded successfully!');
    }
}
