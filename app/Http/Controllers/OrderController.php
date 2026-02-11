<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use Midtrans\Config;
use Midtrans\Snap;

class OrderController extends Controller
{
    public function cart()
    {
        return view('pages.cart');
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'table_number' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:500',
            'cart_items' => 'required|string',
            'payment_method' => 'required|in:cash,qris',
        ]);

        $cartItems = json_decode($request->cart_items, true);

        if (empty($cartItems)) {
            return back()->with('error', 'Keranjang kosong');
        }

        DB::beginTransaction();

        try {
            // Create order
            // Map order_type: dine_in for orders with table number, takeaway for no table
            $orderType = $request->table_number ? 'dine_in' : 'takeaway';
            
            // Determine payment status based on method
            $paymentStatus = $request->payment_method === 'cash' ? 'unpaid' : 'pending';
            
            $order = Order::create([
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone ?? null,
                'table_number' => $request->table_number ?? 0,
                'notes' => $request->notes,
                'order_type' => $orderType,
                'payment_method' => $request->payment_method,
                'payment_status' => $paymentStatus,
                'status' => 'pending',
                'total_amount' => 0,
            ]);

            $totalAmount = 0;
            $itemsDetails = [];

            // Create order items
            foreach ($cartItems as $item) {
                $menu = Menu::find($item['id']);
                if (!$menu || !$menu->is_available) {
                    throw new \Exception("Menu {$item['name']} tidak tersedia");
                }
                
                // Use finalPrice from cart if available, otherwise use menu price
                $unitPrice = isset($item['finalPrice']) ? $item['finalPrice'] : $menu->price;
                $itemSubtotal = $unitPrice * $item['quantity'];
                
                // Format options/customizations for notes
                $notes = '';
                if (isset($item['options'])) {
                    $optionsParts = [];
                    $options = $item['options'];
                    
                    // Size
                    if (!empty($options['size'])) {
                        $optionsParts[] = ucfirst($options['size']);
                    }
                    
                    // Temperature (beverages)
                    if (!empty($options['temperature'])) {
                        $optionsParts[] = ucfirst($options['temperature']);
                    }
                    
                    // Ice Level
                    if (!empty($options['iceLevel'])) {
                        $optionsParts[] = 'Ice: ' . ucfirst($options['iceLevel']);
                    }
                    
                    // Sugar Level
                    if (!empty($options['sugarLevel'])) {
                        $sugar = str_replace('-', ' ', $options['sugarLevel']);
                        $optionsParts[] = 'Sugar: ' . ucfirst($sugar);
                    }
                    
                    // Spice Level (food)
                    if (!empty($options['spiceLevel'])) {
                        $optionsParts[] = 'Spice: ' . ucfirst($options['spiceLevel']);
                    }
                    
                    // Portion
                    if (!empty($options['portion'])) {
                        $optionsParts[] = 'Portion: ' . ucfirst($options['portion']);
                    }
                    
                    // Add-ons
                    if (!empty($options['addOns']) && is_array($options['addOns'])) {
                        foreach ($options['addOns'] as $addon) {
                            $addonName = str_replace('-', ' ', $addon);
                            $optionsParts[] = '+ ' . ucwords($addonName);
                        }
                    }
                    
                    // Toppings
                    if (!empty($options['toppings']) && is_array($options['toppings'])) {
                        foreach ($options['toppings'] as $topping) {
                            $optionsParts[] = '+ ' . ucwords($topping);
                        }
                    }
                    
                    // Sauces
                    if (!empty($options['sauces']) && is_array($options['sauces'])) {
                        foreach ($options['sauces'] as $sauce) {
                            $optionsParts[] = ucwords($sauce) . ' sauce';
                        }
                    }
                    
                    // Special Request
                    if (!empty($options['specialRequest'])) {
                        $optionsParts[] = 'Note: ' . $options['specialRequest'];
                    }
                    
                    $notes = implode(', ', $optionsParts);
                }

                $orderItemData = [
                    'order_id' => $order->id,
                    'menu_id' => $menu->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'subtotal' => $itemSubtotal,
                    'notes' => $notes,
                ];
                
                // Add menu_name if column exists
                if (\Schema::hasColumn('order_items', 'menu_name')) {
                    $orderItemData['menu_name'] = $menu->name;
                }
                
                OrderItem::create($orderItemData);
                
                // Add to item details for Midtrans
                $itemsDetails[] = [
                    'id' => $menu->id,
                    'price' => $unitPrice,
                    'quantity' => $item['quantity'],
                    'name' => substr($menu->name . ' ' . $notes, 0, 50), // Midtrans name limit
                ];

                $totalAmount += $itemSubtotal;
            }

            // Update order total
            $order->update(['total_amount' => $totalAmount]);

            // Create payment record
            Payment::create([
                'order_id' => $order->id,
                'amount' => $totalAmount,
                'method' => $request->payment_method, // 'cash' or 'qris'
                'status' => 'pending',
            ]);

            DB::commit();

            return redirect()->route('order.success', $order->order_number)
                ->with('success', 'Pesanan berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function success($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->with(['items.menu', 'payment'])
            ->firstOrFail();
            
        $snapToken = null;
        $paymentError = null;
        
        // Generate Snap Token if Payment is not CASH and Pending
        if ($order->payment && $order->payment_method !== 'cash' && $order->payment_status !== 'paid') {
            
            // Try to generate Midtrans Snap Token
            try {
                $paymentController = new PaymentController();
                $result = $paymentController->processPayment($order);
                
                if ($result['success']) {
                    $snapToken = $result['snap_token'];
                } else {
                    $paymentError = $result['error'] ?? 'Failed to generate payment token';
                }
            } catch (\Exception $e) {
                $paymentError = $e->getMessage();
                \Log::error('Snap Token Generation Error: ' . $e->getMessage());
            }
        }

        return view('pages.order-success', compact('order', 'snapToken', 'paymentError'));
    }

    public function track()
    {
        return view('pages.track');
    }

    public function trackOrder(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string',
        ]);

        $order = Order::where('order_number', $request->order_number)
            ->with(['items.menu', 'payment'])
            ->first();

        if (!$order) {
            return back()->with('error', 'Pesanan tidak ditemukan');
        }

        return view('pages.track', compact('order'));
    }
}
