<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Menu;
use App\Models\Ingredient;
use App\Models\IngredientLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockService
{
    /**
     * Validate if sufficient stock exists for an order
     * 
     * @param Order $order
     * @return array ['valid' => bool, 'errors' => array, 'requirements' => array]
     */
    public function validateStockForOrder(Order $order): array
    {
        $requiredIngredients = $this->calculateRequiredStock($order);
        $errors = [];

        foreach ($requiredIngredients as $ingredientId => $data) {
            $ingredient = $data['ingredient'];
            $required = $data['required'];

            if ($ingredient->stock < $required) {
                $errors[] = "{$ingredient->name} (tersedia: {$ingredient->formatted_stock}, dibutuhkan: " . 
                           number_format($required, 2) . " {$ingredient->unit})";
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'requirements' => $requiredIngredients,
        ];
    }

    /**
     * Calculate required stock for an order
     * 
     * @param Order $order
     * @return array [ingredient_id => ['ingredient' => Ingredient, 'required' => float]]
     */
    public function calculateRequiredStock(Order $order): array
    {
        $requiredIngredients = [];

        // Load order items with menu and recipes
        $order->load('items.menu.recipes.ingredient');

        foreach ($order->items as $item) {
            if (!$item->menu || $item->menu->recipes->count() === 0) {
                continue; // Skip items without recipes
            }

            $quantity = max(1, (int)$item->quantity);

            foreach ($item->menu->recipes as $recipe) {
                $ingredientId = $recipe->ingredient_id;

                if (!isset($requiredIngredients[$ingredientId])) {
                    $requiredIngredients[$ingredientId] = [
                        'ingredient' => $recipe->ingredient,
                        'required' => 0,
                    ];
                }

                $requiredIngredients[$ingredientId]['required'] += ($recipe->quantity_used * $quantity);
            }
        }

        return $requiredIngredients;
    }

    /**
     * Deduct stock for an order
     * 
     * @param Order $order
     * @return void
     * @throws \Exception
     */
    public function deductStockForOrder(Order $order): void
    {
        // Validate first
        $validation = $this->validateStockForOrder($order);
        
        if (!$validation['valid']) {
            throw new \Exception('Stok tidak mencukupi: ' . implode(', ', $validation['errors']));
        }

        DB::beginTransaction();

        try {
            foreach ($validation['requirements'] as $ingredientId => $data) {
                $ingredient = $data['ingredient'];
                $required = $data['required'];

                // Deduct stock and create log
                $ingredient->deductStock(
                    $required,
                    $order->id,
                    "Order #{$order->order_number}"
                );

                Log::info("Stock deducted for order", [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'ingredient' => $ingredient->name,
                    'amount' => $required,
                    'remaining_stock' => $ingredient->stock,
                ]);
            }

            DB::commit();

            Log::info("✅ Stock deduction completed for order #{$order->order_number}");
            
            // Update menu availability after stock deduction
            $this->updateMenuAvailability();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("❌ Stock deduction failed for order #{$order->order_number}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Rollback stock for an order (in case of cancellation)
     * 
     * @param Order $order
     * @return void
     */
    public function rollbackStockForOrder(Order $order): void
    {
        DB::beginTransaction();

        try {
            // Find all deduction logs for this order
            $logs = IngredientLog::where('type', 'Order Deduct')
                ->where('reference_id', $order->id)
                ->get();

            foreach ($logs as $log) {
                $ingredient = $log->ingredient;
                
                // Add back the deducted amount
                $ingredient->restockIngredient(
                    abs($log->change_amount),
                    "Rollback for cancelled order #{$order->order_number}"
                );

                Log::info("Stock rolled back", [
                    'order_id' => $order->id,
                    'ingredient' => $ingredient->name,
                    'amount' => abs($log->change_amount),
                ]);
            }

            DB::commit();

            Log::info("✅ Stock rollback completed for order #{$order->order_number}");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("❌ Stock rollback failed for order #{$order->order_number}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get low stock ingredients
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLowStockIngredients()
    {
        return Ingredient::lowStock()->get();
    }

    /**
     * Get out of stock ingredients
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOutOfStockIngredients()
    {
        return Ingredient::outOfStock()->get();
    }

    /**
     * Get ingredient usage statistics
     * 
     * @param int $days Number of days to analyze
     * @return array
     */
    public function getUsageStatistics(int $days = 7): array
    {
        $startDate = now()->subDays($days)->startOfDay();
        $endDate = now()->endOfDay();

        $logs = IngredientLog::with('ingredient')
            ->where('type', 'Order Deduct')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $statistics = [];

        foreach ($logs as $log) {
            // Skip orphaned logs (ingredient deleted)
            if (!$log->ingredient) {
                continue;
            }

            $ingredientId = $log->ingredient_id;
            $ingredientName = $log->ingredient->name;

            if (!isset($statistics[$ingredientId])) {
                $statistics[$ingredientId] = [
                    'ingredient_id' => $ingredientId,
                    'ingredient_name' => $ingredientName,
                    'total_used' => 0,
                    'unit' => $log->ingredient->unit,
                    'usage_count' => 0,
                ];
            }

            $statistics[$ingredientId]['total_used'] += abs($log->change_amount);
            $statistics[$ingredientId]['usage_count']++;
        }

        // Sort by total used (descending)
        usort($statistics, function ($a, $b) {
            return $b['total_used'] <=> $a['total_used'];
        });

        return $statistics;
    }

    /**
     * Get most used ingredient
     * 
     * @param int $days
     * @return array|null
     */
    public function getMostUsedIngredient(int $days = 7): ?array
    {
        $statistics = $this->getUsageStatistics($days);
        return !empty($statistics) ? $statistics[0] : null;
    }

    /**
     * Update all menu availability based on current stock levels
     * 
     * @return void
     */
    public function updateMenuAvailability(): void
    {
        Log::info("🔄 Updating menu availability based on stock levels");
        
        $menus = Menu::with('recipes.ingredient')->get();
        $updated = 0;
        
        foreach ($menus as $menu) {
            $wasBefore = $menu->is_available;
            $menu->updateAvailabilityByStock();
            
            if ($wasBefore !== $menu->is_available) {
                $updated++;
            }
        }
        
        Log::info("✅ Menu availability update completed. {$updated} menus updated.");
    }
}
