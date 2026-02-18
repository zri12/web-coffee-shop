<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\IngredientLog;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InventoryAnalyticsController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Display analytics dashboard
     */
    public function index(Request $request)
    {
        $days = $request->input('days', 7);

        // Usage statistics
        $usageStats = $this->stockService->getUsageStatistics($days);

        // Most used ingredient
        $mostUsed = $this->stockService->getMostUsedIngredient($days);

        // Low stock ingredients
        $lowStock = $this->stockService->getLowStockIngredients();

        // Out of stock ingredients
        $outOfStock = $this->stockService->getOutOfStockIngredients();

        // Daily usage for chart
        $dailyUsage = $this->getDailyUsage($days);

        // Category breakdown
        $categoryBreakdown = $this->getCategoryBreakdown($days);

        // Summary cards
        $totalUsage = collect($usageStats)->sum('total_used');
        $avgDailyUsage = $totalUsage / max(1, $days);
        $lowStockCount = $lowStock->count();

        return view('admin.analytics.inventory', compact(
            'usageStats',
            'mostUsed',
            'lowStock',
            'outOfStock',
            'dailyUsage',
            'categoryBreakdown',
            'totalUsage',
            'avgDailyUsage',
            'lowStockCount',
            'days'
        ));
    }

    /**
     * Get daily usage data for charts
     */
    protected function getDailyUsage($days)
    {
        $startDate = now()->subDays($days)->startOfDay();
        $endDate = now()->endOfDay();

        $logs = $this->outScope(IngredientLog::with('ingredient'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $dailyData = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dailyData[$date] = [
                'date' => now()->subDays($i)->format('d M'),
                'total' => 0,
                'ingredients' => []
            ];
        }

        foreach ($logs as $log) {
            $date = $log->created_at->format('Y-m-d');
            if (isset($dailyData[$date])) {
                $dailyData[$date]['total'] += abs($log->change_amount);
                
                // Handle orphaned logs where ingredient might have been deleted
                if ($log->ingredient) {
                    $ingredientName = $log->ingredient->name;
                    if (!isset($dailyData[$date]['ingredients'][$ingredientName])) {
                        $dailyData[$date]['ingredients'][$ingredientName] = 0;
                    }
                    $dailyData[$date]['ingredients'][$ingredientName] += abs($log->change_amount);
                }
            }
        }

        return array_values($dailyData);
    }

    /**
     * Get category breakdown
     */
    protected function getCategoryBreakdown($days)
    {
        $startDate = now()->subDays($days)->startOfDay();
        $endDate = now()->endOfDay();

        $logs = $this->outScope(IngredientLog::with('ingredient'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $categoryData = [];

        foreach ($logs as $log) {
            // Handle orphaned logs where ingredient might have been deleted
            if ($log->ingredient) {
                $category = $log->ingredient->category;
                if (!isset($categoryData[$category])) {
                    $categoryData[$category] = 0;
                }
                $categoryData[$category] += abs($log->change_amount);
            }
        }

        return $categoryData;
    }

    /**
     * Get usage report (API endpoint)
     */
    public function usageReport(Request $request)
    {
        $days = $request->input('days', 7);
        $ingredientId = $request->input('ingredient_id');

        $startDate = now()->subDays($days)->startOfDay();
        $endDate = now()->endOfDay();

        $query = $this->outScope(IngredientLog::with('ingredient'))
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($ingredientId) {
            $query->where('ingredient_id', $ingredientId);
        }

        $logs = $query->get();

        return response()->json([
            'success' => true,
            'logs' => $logs,
            'summary' => [
                'total_deductions' => $logs->count(),
                'total_amount' => $logs->sum(function ($log) {
                    return abs($log->change_amount);
                })
            ]
        ]);
    }

    /**
     * Get low stock report
     */
    public function lowStockReport()
    {
        $lowStock = $this->stockService->getLowStockIngredients();
        $outOfStock = $this->stockService->getOutOfStockIngredients();

        return response()->json([
            'success' => true,
            'low_stock' => $lowStock,
            'out_of_stock' => $outOfStock
        ]);
    }

    private function outScope($query)
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
}
