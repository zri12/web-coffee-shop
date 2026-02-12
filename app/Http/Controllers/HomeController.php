<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index()
    {
        $categories = collect();
        $featuredMenus = collect();

        try {
            $categoriesQuery = Category::query();
            if (Schema::hasColumn('categories', 'is_active')) {
                $categoriesQuery->where('is_active', true);
            }
            if (Schema::hasColumn('categories', 'sort_order')) {
                $categoriesQuery->orderBy('sort_order');
            }
            $categoriesQuery->orderBy('name');
            $categories = $categoriesQuery->get();

            if ($categories->isEmpty()) {
                $categories = Category::query()->orderBy('name')->get();
            }

            $featuredQuery = Menu::with('category');
            if (Schema::hasColumn('menus', 'is_available')) {
                $featuredQuery->where('is_available', true);
            }
            if (Schema::hasColumn('menus', 'is_featured')) {
                $featuredQuery->where('is_featured', true);
            }

            $featuredMenus = $featuredQuery->limit(8)->get();

            if ($featuredMenus->isEmpty()) {
                $featuredMenus = Menu::with('category')->limit(8)->get();
            }
        } catch (\Throwable $e) {
            Log::warning('Home page loaded without database data', [
                'error' => $e->getMessage(),
            ]);
        }

        return view('pages.home', compact('categories', 'featuredMenus'));
    }
}
