<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Menu;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics (Admin/Manager only)
     */
    public function index(Request $request)
    {
        // Today's stats
        $todayOrders = Order::today()->count();
        $todayRevenue = Payment::whereHas('order', function ($q) {
            $q->today()->where('status', '!=', 'cancelled');
        })->paid()->sum('amount');

        // Total stats
        $totalOrders = Order::where('status', '!=', 'cancelled')->count();
        $totalRevenue = Payment::paid()->sum('amount');

        // Pending orders
        $pendingOrders = Order::pending()->count();
        $processingOrders = Order::processing()->count();

        // Average order value
        $avgOrderValue = Order::where('status', 'completed')->avg('total_amount') ?? 0;

        // Best selling items (top 5)
        $bestSellers = DB::table('order_items')
            ->join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->select(
                'menus.id',
                'menus.name',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->groupBy('menus.id', 'menus.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // Recent orders
        $recentOrders = Order::with(['items.menu', 'payment'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => [
                    'today_orders' => $todayOrders,
                    'today_revenue' => (float) $todayRevenue,
                    'total_orders' => $totalOrders,
                    'total_revenue' => (float) $totalRevenue,
                    'pending_orders' => $pendingOrders,
                    'processing_orders' => $processingOrders,
                    'avg_order_value' => round($avgOrderValue, 0),
                ],
                'best_sellers' => $bestSellers,
                'recent_orders' => $recentOrders,
            ],
        ]);
    }

    /**
     * Get reports data (Admin/Manager only)
     */
    public function reports(Request $request)
    {
        $request->validate([
            'period' => 'in:today,week,month,year',
            'date_from' => 'date',
            'date_to' => 'date',
        ]);

        $period = $request->input('period', 'week');
        
        // Determine date range
        switch ($period) {
            case 'today':
                $dateFrom = today();
                $dateTo = today();
                break;
            case 'week':
                $dateFrom = today()->startOfWeek();
                $dateTo = today()->endOfWeek();
                break;
            case 'month':
                $dateFrom = today()->startOfMonth();
                $dateTo = today()->endOfMonth();
                break;
            case 'year':
                $dateFrom = today()->startOfYear();
                $dateTo = today()->endOfYear();
                break;
            default:
                $dateFrom = $request->input('date_from', today()->startOfWeek());
                $dateTo = $request->input('date_to', today());
        }

        // Daily revenue
        $dailyRevenue = DB::table('orders')
            ->join('payments', 'orders.id', '=', 'payments.order_id')
            ->where('payments.status', 'paid')
            ->where('orders.status', '!=', 'cancelled')
            ->whereBetween('orders.created_at', [$dateFrom, $dateTo->endOfDay()])
            ->select(
                DB::raw('DATE(orders.created_at) as date'),
                DB::raw('COUNT(DISTINCT orders.id) as total_orders'),
                DB::raw('SUM(payments.amount) as total_revenue')
            )
            ->groupBy(DB::raw('DATE(orders.created_at)'))
            ->orderBy('date')
            ->get();

        // By category
        $byCategory = DB::table('order_items')
            ->join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->join('categories', 'menus.category_id', '=', 'categories.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->whereBetween('orders.created_at', [$dateFrom, $dateTo->endOfDay()])
            ->select(
                'categories.name as category',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_revenue')
            ->get();

        // By payment method
        $byPaymentMethod = DB::table('payments')
            ->join('orders', 'payments.order_id', '=', 'orders.id')
            ->where('payments.status', 'paid')
            ->whereBetween('orders.created_at', [$dateFrom, $dateTo->endOfDay()])
            ->select(
                'payments.method',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(payments.amount) as total')
            )
            ->groupBy('payments.method')
            ->get();

        // Orders by hour (for today)
        $byHour = [];
        if ($period === 'today') {
            $byHour = DB::table('orders')
                ->whereDate('created_at', today())
                ->where('status', '!=', 'cancelled')
                ->select(
                    DB::raw('HOUR(created_at) as hour'),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy(DB::raw('HOUR(created_at)'))
                ->orderBy('hour')
                ->get();
        }

        // Summary
        $summary = [
            'total_orders' => Order::where('status', '!=', 'cancelled')
                ->whereBetween('created_at', [$dateFrom, $dateTo->endOfDay()])
                ->count(),
            'total_revenue' => Payment::paid()
                ->whereHas('order', function ($q) use ($dateFrom, $dateTo) {
                    $q->whereBetween('created_at', [$dateFrom, $dateTo->endOfDay()]);
                })
                ->sum('amount'),
            'completed_orders' => Order::completed()
                ->whereBetween('created_at', [$dateFrom, $dateTo->endOfDay()])
                ->count(),
            'cancelled_orders' => Order::cancelled()
                ->whereBetween('created_at', [$dateFrom, $dateTo->endOfDay()])
                ->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'period' => $period,
                'date_from' => $dateFrom->format('Y-m-d'),
                'date_to' => $dateTo->format('Y-m-d'),
                'summary' => $summary,
                'daily_revenue' => $dailyRevenue,
                'by_category' => $byCategory,
                'by_payment_method' => $byPaymentMethod,
                'by_hour' => $byHour,
            ],
        ]);
    }
}
