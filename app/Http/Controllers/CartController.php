<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /**
     * Get cart contents
     */
    public function index(Request $request)
    {
        $cart = $this->getCart($request);
        
        return response()->json([
            'success' => true,
            'cart' => $cart,
            'cart_count' => count($cart),
            'cart_total' => $this->calculateTotal($cart)
        ]);
    }

    /**
     * Add item to cart
     */
    public function store(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'quantity' => 'nullable|integer|min:1',
            'options' => 'nullable|array',
            'table_number' => 'nullable|string' // Optional context
        ]);

        $menu = Menu::findOrFail($request->menu_id);
        
        // Calculate price
        $basePrice = $menu->price;
        $options = $request->options ?? [];
        $finalPrice = $this->calculatePriceWithOptions($basePrice, $options);
        
        $cartItem = [
            'id' => $menu->id,
            'name' => $menu->name,
            'price' => $basePrice,
            'final_price' => $finalPrice,
            'image' => $menu->image_url ? asset('storage/' . $menu->image_url) : ($menu->image ? asset('images/menus/' . $menu->image) : null),
            'quantity' => (int) ($request->quantity ?? 1),
            'type' => $this->getMenuType($menu), // helper to determine type
            'options' => $options,
            'cartItemId' => uniqid('cart_', true),
            'subtotal' => $finalPrice * (int) ($request->quantity ?? 1)
        ];

        $this->addToSessionCart($request, $cartItem);

        $cart = $this->getCart($request);
        
        return response()->json([
            'success' => true,
            'message' => 'Item added to cart',
            'cart' => $cart,
            'cart_count' => count($cart),
            'cart_total' => $this->calculateTotal($cart)
        ]);
    }

    /**
     * Remove item from cart
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'cartItemId' => 'required|string',
            'table_number' => 'nullable|string'
        ]);

        $this->removeFromSessionCart($request, $request->cartItemId);
        
        $cart = $this->getCart($request);

        return response()->json([
            'success' => true,
            'message' => 'Item removed',
            'cart' => $cart,
            'cart_count' => count($cart),
            'cart_total' => $this->calculateTotal($cart)
        ]);
    }

    /**
     * Update item quantity
     */
    public function update(Request $request)
    {
        $request->validate([
            'cartItemId' => 'required|string',
            'quantity' => 'required|integer|min:0',
            'table_number' => 'nullable|string'
        ]);

        if ($request->quantity <= 0) {
            $this->removeFromSessionCart($request, $request->cartItemId);
        } else {
            $this->updateSessionCartQuantity($request, $request->cartItemId, $request->quantity);
        }

        $cart = $this->getCart($request);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated',
            'cart' => $cart,
            'cart_count' => count($cart),
            'cart_total' => $this->calculateTotal($cart)
        ]);
    }
    
    // --- Helper Methods ---

    private function getCart(Request $request)
    {
        $key = $this->getSessionKey($request);
        return Session::get($key, []);
    }

    private function addToSessionCart(Request $request, $item)
    {
        $key = $this->getSessionKey($request);
        $cart = Session::get($key, []);
        
        // Always push as new item (no merging for simplicty & options support)
        $cart[] = $item;
        
        Session::put($key, array_values($cart));
    }

    private function removeFromSessionCart(Request $request, $cartItemId)
    {
        $key = $this->getSessionKey($request);
        $cart = Session::get($key, []);
        
        $cart = array_filter($cart, function($item) use ($cartItemId) {
            return ($item['cartItemId'] ?? '') !== $cartItemId;
        });
        
        Session::put($key, array_values($cart));
    }
    
    private function updateSessionCartQuantity(Request $request, $cartItemId, $quantity)
    {
        $key = $this->getSessionKey($request);
        $cart = Session::get($key, []);
        
        foreach ($cart as &$item) {
            if (($item['cartItemId'] ?? '') === $cartItemId) {
                $item['quantity'] = $quantity;
                $item['subtotal'] = $item['final_price'] * $quantity;
                break;
            }
        }
        
        Session::put($key, $cart);
    }

    private function getSessionKey(Request $request)
    {
        if ($request->filled('table_number')) {
            return "cart_table_{$request->table_number}";
        }
        return 'cart_general';
    }

    private function calculateTotal($cart)
    {
        return array_reduce($cart, function($total, $item) {
            return $total + ($item['subtotal'] ?? 0);
        }, 0);
    }
    
    private function calculatePriceWithOptions($basePrice, $options)
    {
        $total = $basePrice;
        
        // Add logic similar to frontend/CustomerController
        // Simplified for brevity, ensures critical price factors are handled
        
        // Size
        if (($options['size'] ?? '') === 'large') $total += 8000;
        
        // Addons
        if (!empty($options['addOns'])) {
            $prices = [
                'extra-shot' => 5000,
                'whipped-cream' => 3000,
                'caramel-syrup' => 3000,
                'extra-cheese' => 5000,
                'extra-egg' => 3000,
                'extra-rice' => 5000,
            ];
            foreach ($options['addOns'] as $addon) {
                $total += $prices[$addon] ?? 0;
            }
        }
        
        // Toppings
        if (!empty($options['toppings'])) {
            $prices = [
                'chocolate' => 3000, 
                'caramel' => 3000, 
                'whipped' => 5000, 
                'ice-cream' => 8000
            ];
            foreach ($options['toppings'] as $t) {
                $total += $prices[$t] ?? 0;
            }
        }
        
        // Portion
        if (isset($options['portion'])) {
             if ($options['portion'] === 'large') $total += 5000; // Generic logic
             if ($options['portion'] === 'small') $total -= 5000;
        }

        return $total;
    }

    private function getMenuType($menu)
    {
        // Simple heuristic based on category
        // In a real app, this should be on the model
        $slug = $menu->category->slug ?? '';
        if (str_contains($slug, 'kopi') || str_contains($slug, 'coffee')) return 'beverage';
        if (str_contains($slug, 'snack')) return 'snack';
        if (str_contains($slug, 'dessert')) return 'dessert';
        return 'food';
    }
}
