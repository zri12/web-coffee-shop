<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $categories = collect();

        try {
            $categories = Category::active()
                ->ordered()
                ->with(['menus' => function ($query) {
                    $query->available()
                        ->orderBy('is_featured', 'desc')
                        ->orderBy('name');
                }])
                ->get();
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
}
