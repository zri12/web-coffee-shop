<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

use Midtrans\Config;
use Midtrans\Snap;
use App\Services\PricingService;

class OrderController extends Controller
{
    private PricingService $pricing;

    public function __construct(PricingService $pricing)
    {
        $this->pricing = $pricing;
    }

    public function cart()
    {
        $cart = session()->get('cart', []);
        $subtotal = array_sum(array_map(function ($item) {
            return ($item['total_price'] ?? $item['final_price'] ?? $item['price'] ?? 0) * ($item['quantity'] ?? $item['qty'] ?? 1);
        }, $cart));

        return view('pages.cart', [
            'cartItems' => array_values($cart),
            'subtotal' => $subtotal,
            'total' => $subtotal,
        ]);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'table_number' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:500',
            'cart_items' => 'nullable|string',
            'payment_method' => 'required|in:cash,qris',
        ]);

        $cartItems = json_decode($request->cart_items ?? '[]', true);
        if (empty($cartItems)) {
            $cartItems = array_values(session()->get('cart', []));
        }

        if (empty($cartItems)) {
            return back()->with('error', 'Keranjang kosong');
        }

        $tableNumber = $request->table_number ?? session('order_meta.table_number');
        $orderType = session('order_meta.order_type') === 'qr'
            ? 'dine_in'
            : ($tableNumber ? 'dine_in' : 'takeaway');

        DB::beginTransaction();

        try {
            // Create order
            // Map order_type: dine_in for orders with table number, takeaway for no table
            $tableNumber = $tableNumber ?? 0;

            // Payment policy (new status flow)
            // - QRIS online: payment_status pending, order status waiting_payment
            // - Cash (bayar di kasir): payment_status unpaid, order status waiting_cashier_confirmation
            $isCash = $request->payment_method === 'cash';
            $paymentStatus = $isCash ? 'unpaid' : 'pending';
            $orderStatus = $isCash ? 'waiting_cashier_confirmation' : 'waiting_payment';
            
            $order = Order::create([
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone ?? null,
                'table_number' => $tableNumber,
                'notes' => $request->notes,
                'order_type' => $orderType,
                'payment_method' => $request->payment_method,
                'payment_status' => $paymentStatus,
                'status' => $orderStatus,
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
                
                $quantity = max(1, (int)($item['quantity'] ?? 1));
                $pricing = $this->pricing->calculate($menu, $item['raw_options'] ?? $item['options'] ?? [], $quantity);
                $unitPrice = $pricing['unit_price'];

                if ($unitPrice <= 0) {
                    throw new \Exception("Harga item {$menu->name} tidak valid.");
                }

                $itemSubtotal = $pricing['subtotal'];

                if ($itemSubtotal <= 0) {
                    $itemSubtotal = $unitPrice * $quantity;
                }
                
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
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $itemSubtotal,
                    'notes' => $notes,
                    'options' => $pricing['raw_options'] ?? ($item['raw_options'] ?? $item['options'] ?? []),
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
                    'quantity' => $quantity,
                    'name' => substr($menu->name . ' ' . $notes, 0, 50), // Midtrans name limit
                ];

                $totalAmount += $itemSubtotal;
            }

            if ($totalAmount <= 0) {
                throw new \Exception('Total pesanan tidak valid. Silakan ulangi pemesanan.');
            }

            // Update order total
            $order->update(['total_amount' => $totalAmount]);

            // Create payment record
            $payment = Payment::create([
                'order_id' => $order->id,
                'amount' => $totalAmount,
                'method' => $request->payment_method, // 'cash' or 'qris'
                'status' => $paymentStatus,
            ]);

            // Generate Midtrans Snap Token for QRIS payments
            $snapToken = null;
            $paymentError = null;
            
            if ($request->payment_method === 'qris') {
                try {
                    // Verify Midtrans configuration
                    if (!config('midtrans.server_key') || !config('midtrans.client_key')) {
                        throw new \Exception('Midtrans configuration is missing. Please check .env file.');
                    }

                    // Configure Midtrans
                    Config::$serverKey = config('midtrans.server_key');
                    Config::$clientKey = config('midtrans.client_key');
                    Config::$isProduction = config('midtrans.is_production', false);
                    Config::$isSanitized = config('midtrans.is_sanitized', true);
                    Config::$is3ds = config('midtrans.is_3ds', true);
                    
                    // DEVELOPMENT ONLY: Disable SSL verification for sandbox on Windows
                    if (!Config::$isProduction) {
                        Config::$curlOptions = array(
                            CURLOPT_HTTPHEADER => array(),
                            CURLOPT_SSL_VERIFYHOST => 0,
                            CURLOPT_SSL_VERIFYPEER => 0
                        );
                        \Log::warning('⚠️ SSL verification disabled for sandbox mode (development only)');
                    }

                    // Prepare Midtrans transaction parameters
                    $params = [
                        'transaction_details' => [
                            'order_id' => $order->order_number,
                            'gross_amount' => (int)$totalAmount,
                        ],
                        'customer_details' => [
                            'first_name' => $request->customer_name,
                            'phone' => $request->customer_phone ?? '',
                        ],
                        'item_details' => $itemsDetails,
                    ];

                    \Log::info('Generating Snap token...', [
                        'order' => $order->order_number,
                        'amount' => $totalAmount,
                        'items_count' => count($itemsDetails)
                    ]);

                    // Generate snap token using Midtrans SDK
                    $snapToken = Snap::getSnapToken($params);

                    // Save snap token to payment record
                    $payment->update([
                        'midtrans_transaction_id' => $snapToken,
                    ]);

                    \Log::info('✅ Snap token generated successfully', [
                        'order' => $order->order_number,
                        'token_preview' => substr($snapToken, 0, 20) . '...',
                        'token_length' => strlen($snapToken)
                    ]);
                    
                } catch (\Exception $e) {
                    \Log::error('❌ Snap token generation failed', [
                        'order' => $order->order_number,
                        'error_message' => $e->getMessage(),
                        'error_trace' => $e->getTraceAsString(),
                        'server_key_exists' => !empty(config('midtrans.server_key')),
                        'client_key_exists' => !empty(config('midtrans.client_key'))
                    ]);
                    
                    $paymentError = 'Gagal membuat pembayaran: ' . $e->getMessage();
                    // Don't rollback - order is still created, payment can be done manually
                }
            }

            DB::commit();

            // Clear session cart after successful order
            session()->forget('cart');
            session()->forget('order_meta');

            // Redirect based on payment method
            if ($request->payment_method === 'cash') {
                // Cash orders: redirect to payment waiting page
                return redirect()->route('order.waiting', $order->order_number)
                    ->with('success', 'Pesanan berhasil dibuat!');
            } else {
                // QRIS orders: redirect to success page with payment button
                return redirect()->route('order.success', $order->order_number)
                    ->with('success', 'Pesanan berhasil dibuat!');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show waiting for payment page (cash orders)
     */
    public function waiting($orderNumber)
    {
        $order = Order::with('items.menu')
            ->where('order_number', $orderNumber)
            ->firstOrFail();
        
        return view('pages.order-waiting', compact('order'));
    }

    public function success($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->with(['items.menu', 'payment'])
            ->firstOrFail();
            
        // Get snap token from payment record if exists
        $snapToken = null;
        $paymentError = null;
        
        if ($order->payment && $order->payment_method === 'qris' && $order->payment_status !== 'paid') {
            $snapToken = $order->payment->midtrans_transaction_id;
            
            \Log::info('Order success page loaded', [
                'order' => $order->order_number,
                'payment_method' => $order->payment_method,
                'payment_status' => $order->payment_status,
                'has_payment_record' => !is_null($order->payment),
                'snap_token_exists' => !is_null($snapToken),
                'snap_token_length' => $snapToken ? strlen($snapToken) : 0
            ]);
            
            if (!$snapToken) {
                $paymentError = 'Token pembayaran tidak ditemukan. Silakan hubungi kasir.';
                \Log::warning('⚠️ Snap token not found in payment record', [
                    'order' => $order->order_number,
                    'payment_id' => $order->payment->id ?? null
                ]);
            }
        }

        return view('pages.order-success', compact('order', 'snapToken', 'paymentError'));
    }

    /**
     * Get order status for polling (API endpoint)
     */
    public function getOrderStatus($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->select('id', 'order_number', 'status', 'payment_status', 'payment_method', 'updated_at')
            ->first();
        
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }
        
        return response()->json([
            'success' => true,
            'order' => [
                'order_number' => $order->order_number,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'payment_method' => $order->payment_method,
                'updated_at' => $order->updated_at->toISOString(),
            ]
        ]);
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

        try {
            $order = Order::where('order_number', $request->order_number)
                ->with(['items.menu', 'payment'])
                ->first();
        } catch (\Throwable $e) {
            Log::warning('Track order unavailable', ['error' => $e->getMessage()]);
            return back()->with('error', 'Layanan lacak pesanan sementara tidak tersedia.');
        }

        if (!$order) {
            return back()->with('error', 'Pesanan tidak ditemukan');
        }

        return view('pages.track', compact('order'));
    }
}
