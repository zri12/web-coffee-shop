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

        return view('pages.menu', compact('table', 'categories'));
    }

    public function checkout($tableNumber)
    {
        $table = Table::where('table_number', $tableNumber)->firstOrFail();
        return view('pages.cart', compact('table'));
    }

    public function addToCart(Request $request, $tableNumber)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'quantity' => 'nullable|integer|min:1',
            'options' => 'nullable|array',
        ]);

        $table = Table::where('table_number', $tableNumber)->firstOrFail();
        $menu = Menu::findOrFail($request->menu_id);

        if (!$menu->is_available) {
            return response()->json([
                'success' => false,
                'message' => 'Menu item is not available'
            ], 400);
        }

        $quantity = (int) ($request->quantity ?? 1);
        $normalizedOptions = $this->normalizeOptions($request->options ?? []);

        $basePrice = $menu->price;
        $finalPrice = $this->calculatePriceWithOptions($basePrice, $normalizedOptions);

        $cart = session()->get("cart.table_{$tableNumber}", []);
        $optionSignature = $this->buildOptionSignature($normalizedOptions);
        $existingIndex = $this->findCartItemIndex($cart, $menu->id, $optionSignature);

        if ($existingIndex !== null) {
            $cart[$existingIndex]['quantity'] += $quantity;
            $cart[$existingIndex]['subtotal'] = $cart[$existingIndex]['final_price'] * $cart[$existingIndex]['quantity'];
        } else {
            $cart[] = [
                'id' => $menu->id,
                'name' => $menu->name,
                'base_price' => $basePrice,
                'final_price' => $finalPrice,
                'quantity' => $quantity,
                'subtotal' => $finalPrice * $quantity,
                'options' => $normalizedOptions,
                'options_signature' => $optionSignature,
                'table_number' => $tableNumber,
                'image' => $menu->image_url ?: $menu->image,
                'cart_item_id' => uniqid('cart_', true)
            ];
        }

        session()->put("cart.table_{$tableNumber}", array_values($cart));

        $cartCount = $this->countCartItems($cart);
        $cartTotal = $this->sumCartTotals($cart);

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart',
            'cart_count' => $cartCount,
            'cart_total' => $cartTotal,
            'cart' => $cart
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

        $cartCount = $this->countCartItems($cart);
        $cartTotal = $this->sumCartTotals($cart);

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
            'cart_item_id' => 'required|string'
        ]);

        $cart = session()->get("cart.table_{$tableNumber}", []);
        $cart = array_values(array_filter($cart, function ($item) use ($request) {
            return $item['cart_item_id'] !== $request->cart_item_id;
        }));

        session()->put("cart.table_{$tableNumber}", $cart);

        $cartCount = $this->countCartItems($cart);
        $cartTotal = $this->sumCartTotals($cart);

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
            'cart_count' => $cartCount,
            'cart_total' => $cartTotal,
            'cart' => $cart,
        ]);
    }

    /**
     * Update cart item - deprecated in new qty=1 model
     * Now just removes the item since we don't allow quantity changes
     */
    public function updateCartItem(Request $request, $tableNumber)
    {
        $request->validate([
            'cart_item_id' => 'required|string',
            'quantity' => 'required|integer',
        ]);

        $cart = session()->get("cart.table_{$tableNumber}", []);
        foreach ($cart as $index => $item) {
            if ($item['cart_item_id'] === $request->cart_item_id) {
                if ($request->quantity <= 0) {
                    unset($cart[$index]);
                } else {
                    $cart[$index]['quantity'] = $request->quantity;
                    $cart[$index]['subtotal'] = $cart[$index]['final_price'] * $request->quantity;
                }
                break;
            }
        }

        $cart = array_values($cart);
        session()->put("cart.table_{$tableNumber}", $cart);

        $cartCount = $this->countCartItems($cart);
        $cartTotal = $this->sumCartTotals($cart);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated',
            'cart_count' => $cartCount,
            'cart_total' => $cartTotal,
            'cart' => $cart,
        ]);
    }

    private function countCartItems(array $cart): int
    {
        return array_sum(array_column($cart, 'quantity'));
    }

    private function sumCartTotals(array $cart): int
    {
        return array_sum(array_column($cart, 'subtotal'));
    }

    private function normalizeOptions(array $options): array
    {
        ksort($options);

        foreach ($options as $key => $value) {
            if (is_array($value)) {
                sort($options[$key]);
            }
        }

        return $options;
    }

    private function buildOptionSignature(array $options): string
    {
        return md5(json_encode($options));
    }

    private function findCartItemIndex(array $cart, int $menuId, string $optionSignature): ?int
    {
        foreach ($cart as $index => $item) {
            if (($item['id'] ?? null) === $menuId && ($item['options_signature'] ?? '') === $optionSignature) {
                return $index;
            }
        }

        return null;
    }
}
