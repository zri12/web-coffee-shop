<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use App\Services\PricingService;

class CartController extends Controller
{
    private PricingService $pricing;

    public function __construct(PricingService $pricing)
    {
        $this->pricing = $pricing;
    }

    /**
     * Display cart page with session data
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        $cartItems = [];
        $subtotal = 0;

        foreach ($cart as $key => $item) {
            $item['cart_key'] = $key; // Add key for delete button
            $cartItems[] = $item;
            $itemSubtotal = $item['total_price'] * ($item['quantity'] ?? $item['qty'] ?? 1);
            $subtotal += $itemSubtotal;
        }

        return view('pages.cart', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'total' => $subtotal,
        ]);
    }

    /**
     * Add item to cart (session-based)
     */
    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'nullable|exists:menus,id',
            'menu_id' => 'nullable|exists:menus,id',
            'qty' => 'nullable|integer|min:1',
            'options' => 'nullable',
            'order_type' => 'nullable|in:menu,qr,manual',
            'table_number' => 'nullable|string',
        ]);

        $productId = $validated['product_id'] ?? $validated['menu_id'] ?? null;
        if (!$productId) {
            return response()->json(['success' => false, 'message' => 'Product id is required.'], 422);
        }

        $product = Menu::findOrFail($productId);
        if (!$product->is_available) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Produk tidak tersedia'], 400);
            }
            return back()->with('error', 'Produk tidak tersedia');
        }

        $cart = session()->get('cart', []);
        $options = $request->input('options', []);
        if (is_string($options)) {
            $decoded = json_decode($options, true);
            $options = is_array($decoded) ? $decoded : [];
        }

        $quantity = max(1, (int) ($validated['qty'] ?? 1));
        $pricing = $this->pricing->calculate($product, $options, $quantity);

        // Generate unique cart key based on product ID and options
        $cartKey = $this->generateCartKey($product->id, $pricing['normalized_options']);

        if (isset($cart[$cartKey])) {
            // Product with same options exists, increase quantity
            $cart[$cartKey]['quantity'] += $quantity;
            $cart[$cartKey]['total_price'] = $pricing['unit_price'];
            $cart[$cartKey]['subtotal'] = $pricing['unit_price'] * $cart[$cartKey]['quantity'];
        } else {
            // Add new item
            $cart[$cartKey] = [
                'id' => $product->id,
                'name' => $product->name,
                'base_price' => $pricing['base_price'],
                'total_price' => $pricing['unit_price'],
                'subtotal' => $pricing['subtotal'],
                'image' => $product->display_image_url, // Use display_image_url accessor for full path
                'quantity' => $quantity,
                'options' => $pricing['options'],
                'raw_options' => $pricing['raw_options'],
                'options_signature' => $this->pricing->optionSignature($pricing['normalized_options']),
                'order_type' => $request->input('order_type', 'menu'),
                'table_number' => $request->input('table_number'),
            ];
        }

        session()->put('cart', $cart);
        session()->put('order_meta', [
            'order_type' => $request->input('order_type', 'menu'),
            'table_number' => $request->input('table_number'),
        ]);

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true, 
                'message' => 'Produk ditambahkan ke keranjang!',
                'cart_count' => array_sum(array_column($cart, 'quantity')),
                'cart_total' => array_sum(array_map(function ($item) {
                    return $item['total_price'] * ($item['quantity'] ?? 1);
                }, $cart)),
                'cart' => array_values($cart),
            ]);
        }

        return back()->with('success', 'Produk ditambahkan ke keranjang!');
    }

    /**
     * Remove item from cart
     */
    public function remove($cartKey)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$cartKey])) {
            unset($cart[$cartKey]);
            session()->put('cart', $cart);
            
            return back()->with('success', 'Item dihapus dari keranjang');
        }

        return back()->with('error', 'Item tidak ditemukan');
    }

    /**
     * Clear entire cart
     */
    public function clear()
    {
        session()->forget('cart');
        
        return redirect()->route('cart')->with('success', 'Keranjang dikosongkan');
    }

    /**
     * Update item quantity
     */
    public function updateQuantity(Request $request, $cartKey)
    {
        $validated = $request->validate([
            'qty' => 'required|integer|min:1|max:99',
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] = $validated['qty'];
            $cart[$cartKey]['subtotal'] = $cart[$cartKey]['total_price'] * $validated['qty'];
            session()->put('cart', $cart);
            
            return back()->with('success', 'Jumlah diperbarui');
        }

        return back()->with('error', 'Item tidak ditemukan');
    }

    /**
     * Get cart count for navbar badge (AJAX)
     */
    public function getCount()
    {
        $cart = session()->get('cart', []);
        $count = 0;
        $total = 0;
        
        foreach ($cart as $item) {
            $quantity = $item['quantity'] ?? $item['qty'] ?? 1;
            $count += $quantity;
            $total += ($item['total_price'] ?? $item['price'] ?? 0) * $quantity;
        }

        return response()->json([
            'count' => $count,
            'total' => $total
        ]);
    }

    /**
     * Generate unique cart key based on product ID and options
     */
    private function generateCartKey($productId, $options = null)
    {
        if ($options && !empty($options)) {
            // Create hash of options for uniqueness
            ksort($options); // Sort to ensure consistent hashing
            return $productId . '_' . md5(json_encode($options));
        }
        return (string) $productId;
    }
}
