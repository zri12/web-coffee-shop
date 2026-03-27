<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $categories = collect();

        try {
            $categoriesQuery = Category::query();

            if (Schema::hasColumn('categories', 'is_active')) {
                $categoriesQuery->where('is_active', true);
            }
            if (Schema::hasColumn('categories', 'sort_order')) {
                $categoriesQuery->orderBy('sort_order');
            }
            $categoriesQuery->orderBy('name');

            $categoriesQuery->with(['menus' => function ($query) {
                if (Schema::hasColumn('menus', 'is_available')) {
                    $query->where('is_available', true);
                }
                if (Schema::hasColumn('menus', 'is_featured')) {
                    $query->orderBy('is_featured', 'desc');
                }
                $query->orderBy('name');
            }]);

            $categories = $categoriesQuery->get();

            // Fallback for legacy datasets where active/available flags are not populated.
            if ($categories->isEmpty()) {
                $categories = Category::query()
                    ->orderBy('name')
                    ->with(['menus' => fn ($query) => $query->orderBy('name')])
                    ->get();
            }
        } catch (\Throwable $e) {
            Log::warning('Menu page loaded without database data', [
                'error' => $e->getMessage(),
            ]);
        }

        return view('pages.menu', compact('categories'));
    }

    public function show(string $slug)
    {
        try {
            $menu = Menu::where('slug', $slug)->firstOrFail();
            $menu->load('category');

            $relatedMenus = Menu::where('category_id', $menu->category_id)
                ->where('id', '!=', $menu->id)
                ->available()
                ->limit(4)
                ->get();

            return view('pages.menu-detail', compact('menu', 'relatedMenus'));
        } catch (\Throwable $e) {
            Log::warning('Menu detail unavailable', [
                'slug' => $slug,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('menu.index')
                ->with('error', 'Menu belum bisa dimuat saat ini. Silakan coba lagi.');
        }
    }

    /**
     * Resilient AI image generator fallback for serverless deployments.
     */
    public function aiImage(Request $request, string $menu)
    {
        $name = $request->query('name', 'Coffee');
        $hint = $request->query('hint', 'beverage product photography');
        
        // Clean up keywords for Unsplash query
        $keywords = collect(explode(' ', strtolower($name)))
            ->merge(explode(' ', $hint))
            ->unique()
            ->filter(fn($w) => strlen($w) > 3)
            ->join(',');

        // Redirect to a high-quality stock image provider
        $providerUrl = "https://source.unsplash.com/featured/?{$keywords},coffee,minimalist";
        
        // Use a more predictable provider as Unsplash source is retiring
        $fallbackUrl = "https://images.unsplash.com/photo-1541167760496-1628856ab752?q=80&w=1000&auto=format&fit=crop";
        if (str_contains(strtolower($name), 'croissant') || str_contains(strtolower($name), 'bread')) {
            $fallbackUrl = "https://images.unsplash.com/photo-1555507036-ab1f4038808a?q=80&w=1000&auto=format&fit=crop";
        } elseif (str_contains(strtolower($name), 'cake') || str_contains(strtolower($name), 'dessert')) {
            $fallbackUrl = "https://images.unsplash.com/photo-1578985545062-69928b1d9587?q=80&w=1000&auto=format&fit=crop";
        }

        return redirect()->away($fallbackUrl);
    }
}
