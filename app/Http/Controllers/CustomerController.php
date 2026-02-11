<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Table;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index($tableNumber)
    {
        // Find table by number or fail
        // Using first() instead of firstOrFail() to handle invalid tables gracefully if needed
        $table = Table::where('table_number', $tableNumber)->firstOrFail();

        return view('customer.landing', compact('table'));
    }

    public function menu($tableNumber)
    {
        $table = Table::where('table_number', $tableNumber)->firstOrFail();
        
        // Get active categories with all menus and load category relationship for each menu
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->with(['menus' => function($query) {
                $query->with('category');
            }])
            ->get();

        return view('customer.menu', compact('table', 'categories'));
    }

    public function addToCart(Request $request, $tableNumber)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'quantity' => 'required|integer|min:1',
            'options' => 'nullable|array',
        ]);

        $table = Table::where('table_number', $tableNumber)->firstOrFail();
        $menu = Menu::findOrFail($request->menu_id);

        // Check if menu is available
        if (!$menu->is_available) {
            return response()->json([
                'success' => false,
                'message' => 'Menu item is not available'
            ], 400);
        }

        // Calculate price with options
        $basePrice = $menu->price;
        $finalPrice = $this->calculatePriceWithOptions($basePrice, $request->options ?? []);

        // Create cart item array
        $cartItem = [
            'id' => $menu->id,
            'name' => $menu->name,
            'base_price' => $basePrice,
            'final_price' => $finalPrice,
            'quantity' => $request->quantity,
            'subtotal' => $finalPrice * $request->quantity,
            'options' => $request->options ?? [],
            'table_number' => $tableNumber,
            'image' => $menu->image_url ?: $menu->image
        ];

        // Get existing cart from session
        $cart = session()->get("cart.table_{$tableNumber}", []);

        // Check if same item with same options exists
        $existingIndex = null;
        foreach ($cart as $index => $item) {
            if ($item['id'] == $menu->id && json_encode($item['options']) === json_encode($cartItem['options'])) {
                $existingIndex = $index;
                break;
            }
        }

        if ($existingIndex !== null) {
            // Update quantity of existing item
            $cart[$existingIndex]['quantity'] += $request->quantity;
            $cart[$existingIndex]['subtotal'] = $cart[$existingIndex]['final_price'] * $cart[$existingIndex]['quantity'];
        } else {
            // Add new item to cart
            $cart[] = $cartItem;
        }

        // Save cart to session
        session()->put("cart.table_{$tableNumber}", $cart);

        // Calculate cart totals
        $cartCount = array_sum(array_column($cart, 'quantity'));
        $cartTotal = array_sum(array_column($cart, 'subtotal'));

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart',
            'cart_count' => $cartCount,
            'cart_total' => $cartTotal,
            'item' => $cartItem
        ]);
    }

    /**
     * Calculate price with options
     */
    private function calculatePriceWithOptions($basePrice, $options)
    {
        $additionalPrice = 0;

        // Size pricing for beverages
        if (isset($options['size']) && $options['size'] === 'large') {
            $additionalPrice += 8000;
        }

        // Add-ons pricing
        if (isset($options['addOns']) && is_array($options['addOns'])) {
            $addonPrices = [
                'extra-shot' => 5000,
                'whipped-cream' => 3000,
                'caramel-syrup' => 3000,
                'extra-cheese' => 5000,
                'extra-egg' => 3000,
                'extra-rice' => 5000,
            ];

            foreach ($options['addOns'] as $addon) {
                $additionalPrice += $addonPrices[$addon] ?? 0;
            }
        }

        // Sauce pricing (mostly free except BBQ)
        if (isset($options['sauces']) && is_array($options['sauces'])) {
            foreach ($options['sauces'] as $sauce) {
                if ($sauce === 'bbq') {
                    $additionalPrice += 2000;
                }
            }
        }

        // Topping pricing
        if (isset($options['toppings']) && is_array($options['toppings'])) {
            $toppingPrices = [
                'chocolate' => 3000,
                'caramel' => 3000,
                'whipped' => 5000,
                'ice-cream' => 8000,
            ];

            foreach ($options['toppings'] as $topping) {
                $additionalPrice += $toppingPrices[$topping] ?? 0;
            }
        }

        // Portion/Size pricing
        if (isset($options['portion'])) {
            if ($options['portion'] === 'large') {
                $additionalPrice += 5000; // For food/snack
            } else if ($options['portion'] === 'small') {
                $additionalPrice -= 5000; // Discount for small
            }
        }

        return $basePrice + $additionalPrice;
    }

    /**
     * Get cart contents
     */
    public function getCart($tableNumber)
    {
        $table = Table::where('table_number', $tableNumber)->firstOrFail();
        $cart = session()->get("cart.table_{$tableNumber}", []);

        $cartCount = array_sum(array_column($cart, 'quantity'));
        $cartTotal = array_sum(array_column($cart, 'subtotal'));

        return response()->json([
            'success' => true,
            'cart' => $cart,
            'cart_count' => $cartCount,
            'cart_total' => $cartTotal
        ]);
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart(Request $request, $tableNumber)
    {
        $request->validate([
            'index' => 'required|integer|min:0'
        ]);

        $cart = session()->get("cart.table_{$tableNumber}", []);
        
        if (isset($cart[$request->index])) {
            unset($cart[$request->index]);
            $cart = array_values($cart); // Reindex array
            session()->put("cart.table_{$tableNumber}", $cart);
        }

        $cartCount = array_sum(array_column($cart, 'quantity'));
        $cartTotal = array_sum(array_column($cart, 'subtotal'));

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
            'cart_count' => $cartCount,
            'cart_total' => $cartTotal
        ]);
    }

    /**
     * Update cart item quantity
     */
    public function updateCartItem(Request $request, $tableNumber)
    {
        $request->validate([
            'index' => 'required|integer|min:0',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = session()->get("cart.table_{$tableNumber}", []);
        
        if (isset($cart[$request->index])) {
            $cart[$request->index]['quantity'] = $request->quantity;
            $cart[$request->index]['subtotal'] = $cart[$request->index]['final_price'] * $request->quantity;
            session()->put("cart.table_{$tableNumber}", $cart);
        }

        $cartCount = array_sum(array_column($cart, 'quantity'));
        $cartTotal = array_sum(array_column($cart, 'subtotal'));

        return response()->json([
            'success' => true,
            'message' => 'Cart updated',
            'cart_count' => $cartCount,
            'cart_total' => $cartTotal
        ]);
    }
}
