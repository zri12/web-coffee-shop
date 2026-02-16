<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\IngredientLog;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Display ingredient analytics dashboard
     */
    public function ingredients()
    {
        // Weekly usage (last 7 days)
        $weeklyUsage = $this->getWeeklyUsage();
        
        // Top 5 consumed ingredients (last 30 days)
        $topConsumed = $this->getTopConsumedIngredients(5);
        
        // Usage trends (last 30 days)
        $usageTrends = $this->getUsageTrends(30);
        
        // Low stock forecast (next 7 days)
        $stockForecast = $this->getForecastLowStock(7);
        
        // Summary statistics
        $totalIngredients = Ingredient::count();
        $lowStockCount = Ingredient::where('status', 'Hampir Habis')->count();
        $outOfStockCount = Ingredient::where('status', 'Habis')->count();
        $totalUsageThisWeek = IngredientLog::where('type', 'Order Deduct')
            ->where('created_at', '>=', now()->subDays(7))
            ->sum(DB::raw('ABS(change_amount)'));

        return view('admin.analytics.ingredients', compact(
            'weeklyUsage',
            'topConsumed',
            'usageTrends',
            'stockForecast',
            'totalIngredients',
            'lowStockCount',
            'outOfStockCount',
            'totalUsageThisWeek'
        ));
    }

    /**
     * Get weekly usage data
     */
    private function getWeeklyUsage()
    {
        return IngredientLog::where('type', 'Order Deduct')
            ->where('created_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, SUM(ABS(change_amount)) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * Get top consumed ingredients
     */
    private function getTopConsumedIngredients(int $limit = 5)
    {
        return IngredientLog::where('type', 'Order Deduct')
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('ingredient_id, SUM(ABS(change_amount)) as total_used')
            ->groupBy('ingredient_id')
            ->orderByDesc('total_used')
            ->limit($limit)
            ->with('ingredient')
            ->get()
            ->map(function ($log) {
                return [
                    'name' => $log->ingredient->name ?? 'Unknown',
                    'total' => $log->total_used,
                    'unit' => $log->ingredient->unit ?? '',
                ];
            });
    }

    /**
     * Get usage trends for the last N days
     */
    private function getUsageTrends(int $days = 30)
    {
        $trends = [];
        $startDate = now()->subDays($days);

        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->copy()->addDays($i)->format('Y-m-d');
            $usage = IngredientLog::where('type', 'Order Deduct')
                ->whereDate('created_at', $date)
                ->sum(DB::raw('ABS(change_amount)'));
            
            $trends[] = [
                'date' => $date,
                'usage' => $usage
            ];
        }

        return collect($trends);
    }

    /**
     * Forecast ingredients that will run low in the next N days
     */
    private function getForecastLowStock(int $days = 7)
    {
        $ingredients = Ingredient::where('stock', '>', 0)->get();
        $forecast = [];

        foreach ($ingredients as $ingredient) {
            // Calculate average daily usage
            $avgDailyUsage = IngredientLog::where('ingredient_id', $ingredient->id)
                ->where('type', 'Order Deduct')
                ->where('created_at', '>=', now()->subDays(30))
                ->avg(DB::raw('ABS(change_amount)'));

            if ($avgDailyUsage > 0) {
                $daysUntilEmpty = $ingredient->stock / $avgDailyUsage;
                
                if ($daysUntilEmpty <= $days) {
                    $forecast[] = [
                        'name' => $ingredient->name,
                        'current_stock' => $ingredient->stock,
                        'unit' => $ingredient->unit,
                        'days_remaining' => round($daysUntilEmpty, 1),
                        'avg_daily_usage' => round($avgDailyUsage, 2),
                    ];
                }
            }
        }

        return collect($forecast)->sortBy('days_remaining');
    }

    /**
     * Get menu availability statistics
     */
    public function menuAvailability()
    {
        $totalMenus = Menu::count();
        $availableMenus = Menu::where('is_available', true)->count();
        $unavailableMenus = $totalMenus - $availableMenus;

        // Menus affected by stock
        $menusAffectedByStock = Menu::whereHas('recipes.ingredient', function ($query) {
            $query->where('status', '!=', 'Aman');
        })->count();

        return response()->json([
            'total' => $totalMenus,
            'available' => $availableMenus,
            'unavailable' => $unavailableMenus,
            'affected_by_stock' => $menusAffectedByStock,
        ]);
    }
}
