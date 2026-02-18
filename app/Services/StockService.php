<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Menu;
use App\Models\Ingredient;
use App\Models\IngredientLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class StockService
{
    /**
     * Validate if sufficient stock exists for an order.
     *
     * @return array ['valid' => bool, 'errors' => array, 'requirements' => array]
     */
    public function validateStockForOrder(Order $order): array
    {
        $requiredIngredients = $this->calculateRequiredStock($order);
        $errors = [];

        foreach ($requiredIngredients as $data) {
            $ingredient = $data['ingredient'];
            $required = $data['required'];

            if ($ingredient && $ingredient->stock < $required) {
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
     * Calculate required stock for an order.
     *
     * @return array [ingredient_id => ['ingredient' => Ingredient, 'required' => float]]
     */
    public function calculateRequiredStock(Order $order): array
    {
        $requiredIngredients = [];

        $order->load('items.menu.recipes.ingredient');

        foreach ($order->items as $item) {
            if (!$item->menu || $item->menu->recipes->count() === 0) {
                continue;
            }

            $quantity = max(1, (int) $item->quantity);

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

    private function alreadyDeducted(Order $order): bool
    {
        $hasDirection = Schema::hasColumn('ingredient_logs', 'direction');
        $hasOrderId = Schema::hasColumn('ingredient_logs', 'order_id');
        $hasReference = Schema::hasColumn('ingredient_logs', 'reference_id');

        return IngredientLog::where(function ($q) use ($hasDirection) {
                if ($hasDirection) {
                    $q->where('direction', 'OUT')->orWhere('type', 'Order Deduct');
                } else {
                    $q->where('type', 'Order Deduct');
                }
            })
            ->when($hasOrderId, fn($q) => $q->where('order_id', $order->id))
            ->when(!$hasOrderId && $hasReference, fn($q) => $q->where('reference_id', $order->id))
            ->exists();
    }

    /**
     * Deduct stock for an order (idempotent per order).
     */
    public function deductStockForOrder(Order $order, bool $withTransaction = true): void
    {
        if ($this->alreadyDeducted($order)) {
            Log::info("Skip deduction: order #{$order->order_number} already deducted.");
            return;
        }

        $validation = $this->validateStockForOrder($order);
        if (!$validation['valid']) {
            throw new \Exception('Stok tidak mencukupi: ' . implode(', ', $validation['errors']));
        }

        $runner = function () use ($order) {
            $order->load('items.menu.recipes.ingredient');

            foreach ($order->items as $item) {
                if (!$item->menu || $item->menu->recipes->isEmpty()) {
                    continue;
                }

                $qty = max(1, (int) $item->quantity);

                foreach ($item->menu->recipes as $recipe) {
                    $ingredient = $recipe->ingredient;
                    if (!$ingredient) {
                        continue;
                    }

                    $required = (float) $recipe->quantity_used * $qty;

                    $ingredient->deductStock(
                        $required,
                        $order->id,
                        "Order #{$order->order_number} - {$item->menu_name}",
                        $item->menu_id
                    );

                    Log::info('Stock deducted for order', [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                        'ingredient' => $ingredient->name,
                        'product_id' => $item->menu_id,
                        'amount' => $required,
                        'remaining_stock' => $ingredient->stock,
                    ]);
                }
            }

            $this->updateMenuAvailability();
        };

        if ($withTransaction) {
            DB::transaction($runner);
        } else {
            $runner();
        }

        Log::info("Stock deduction completed for order #{$order->order_number}");
    }

    /**
     * Roll back stock for an order cancellation.
     */
    public function rollbackStockForOrder(Order $order, bool $withTransaction = true): void
    {
        $hasDirection = Schema::hasColumn('ingredient_logs', 'direction');
        $hasOrderId = Schema::hasColumn('ingredient_logs', 'order_id');
        $hasReference = Schema::hasColumn('ingredient_logs', 'reference_id');

        $alreadyRestored = IngredientLog::when($hasOrderId, fn($q) => $q->where('order_id', $order->id))
            ->when(!$hasOrderId && $hasReference, fn($q) => $q->where('reference_id', $order->id))
            ->when($hasDirection, fn($q) => $q->where('direction', 'IN'))
            ->exists();

        if ($alreadyRestored) {
            Log::info("Skip rollback: order #{$order->order_number} already restocked.");
            return;
        }

        $runner = function () use ($order) {
            $logs = IngredientLog::where(function ($q) use ($hasDirection) {
                    if ($hasDirection) {
                        $q->where('direction', 'OUT')->orWhere('type', 'Order Deduct');
                    } else {
                        $q->where('type', 'Order Deduct');
                    }
                })
                ->when($hasOrderId, fn($q) => $q->where('order_id', $order->id))
                ->when(!$hasOrderId && $hasReference, fn($q) => $q->where('reference_id', $order->id))
                ->get();

            foreach ($logs as $log) {
                $ingredient = $log->ingredient;
                if (!$ingredient) {
                    continue;
                }

                $ingredient->restockIngredient(
                    abs($log->change_amount),
                    "Rollback for cancelled order #{$order->order_number}",
                    $order->id,
                    $log->product_id,
                    'Restock'
                );

                Log::info('Stock rolled back', [
                    'order_id' => $order->id,
                    'ingredient' => $ingredient->name,
                    'amount' => abs($log->change_amount),
                ]);
            }

            $this->updateMenuAvailability();
        };

        if ($withTransaction) {
            DB::transaction($runner);
        } else {
            $runner();
        }

        Log::info("Stock rollback completed for order #{$order->order_number}");
    }

    public function getLowStockIngredients()
    {
        return Ingredient::lowStock()->get();
    }

    public function getOutOfStockIngredients()
    {
        return Ingredient::outOfStock()->get();
    }

    /**
     * Aggregate ingredient usage statistics.
     */
    public function getUsageStatistics(int $days = 7): array
    {
        $startDate = now()->subDays($days)->startOfDay();
        $endDate = now()->endOfDay();

        $logs = $this->scopeOrderOut(IngredientLog::with('ingredient'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $statistics = [];

        foreach ($logs as $log) {
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

        usort($statistics, function ($a, $b) {
            return $b['total_used'] <=> $a['total_used'];
        });

        return $statistics;
    }

    public function getMostUsedIngredient(int $days = 7): ?array
    {
        $statistics = $this->getUsageStatistics($days);
        return !empty($statistics) ? $statistics[0] : null;
    }

    /**
     * Apply OUT filter compatibly (supports old schema without direction column).
     */
    public function scopeOrderOut($query)
    {
        $hasDirection = Schema::hasColumn('ingredient_logs', 'direction');

        return $query->where(function ($q) use ($hasDirection) {
            if ($hasDirection) {
                $q->where('direction', 'OUT')->orWhere('type', 'Order Deduct');
            } else {
                $q->where('type', 'Order Deduct');
            }
        });
    }

    /**
     * Update menu availability based on ingredient stock.
     */
    public function updateMenuAvailability(): void
    {
        Log::info('Updating menu availability based on stock levels');

        $menus = Menu::with('recipes.ingredient')->get();
        $updated = 0;

        foreach ($menus as $menu) {
            $wasBefore = $menu->is_available;
            $menu->updateAvailabilityByStock();

            if ($wasBefore !== $menu->is_available) {
                $updated++;
            }
        }

        Log::info("Menu availability update completed. {$updated} menus updated.");
    }
}
