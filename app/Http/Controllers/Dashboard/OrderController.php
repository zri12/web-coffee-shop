<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Category;
use App\Models\Menu;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function create()
    {
        $categories = Category::with(['menus' => function($query) {
            $query->where('is_available', true);
        }])->where('is_active', true)->ordered()->get();
        
        return view('dashboard.orders.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'table_number' => 'nullable|integer', // Made nullable for take-away
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,card,qris',
            'amount_paid' => 'nullable|numeric|min:0', // For change calculation
        ]);

        try {
            DB::beginTransaction();

            $totalAmount = 0;
            $itemsData = [];

            // Calculate total and prepare items
            foreach ($request->items as $item) {
                $menu = Menu::findOrFail($item['id']);
                $subtotal = $menu->price * $item['quantity'];
                $totalAmount += $subtotal;
                
                $itemsData[] = [
                    'menu_id' => $menu->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $menu->price,
                    'subtotal' => $subtotal,
                    'notes' => $item['notes'] ?? null,
                ];
            }

            // Create Order
            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'user_id' => auth()->id(),
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'table_number' => $request->table_number ?? 0, // 0 for takeaway
                'status' => 'pending', // Pending preparation
                'total_amount' => $totalAmount,
                'order_type' => $request->table_number ? 'dine_in' : 'takeaway',
                'notes' => $request->notes,
            ]);

            // Create Order Items
            foreach ($itemsData as $data) {
                $order->items()->create($data);
            }

            // Create Payment Record (Simulated for manual)
            $order->payment()->create([
                'method' => $request->payment_method,
                'status' => 'paid', // Cashier accepts payment immediately
                'amount' => $totalAmount,
                'paid_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Order Created Successfully', 
                'order_number' => $order->order_number
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function index(Request $request)
    {
        $query = Order::with(['items', 'payment']);
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }
        
        // Today only by default
        if (!$request->filled('date') && !$request->filled('all')) {
            $query->whereDate('created_at', today());
        }
        
        $orders = $query->latest()->paginate(20);
        
        return view('dashboard.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['items.menu', 'payment', 'user']);
        return view('dashboard.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,ready,completed,cancelled',
        ]);
        
        $previousStatus = strtolower((string)$order->status);
        $newStatus = strtolower($request->status);

        $order->update(['status' => $newStatus]);

        // Deduct ingredients when entering processing state (only once)
        if (!in_array($previousStatus, ['processing', 'ready']) && in_array($newStatus, ['processing', 'ready'])) {
            try {
                $order->deductIngredients();
            } catch (\Throwable $e) {
                \Log::error("Ingredient deduction failed (dashboard) for order {$order->order_number}: {$e->getMessage()}");
            }
        }
        
        return back()->with('success', 'Status pesanan berhasil diperbarui');
    }
}
