<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index()
    {
        $categories = collect();
        $featuredMenus = collect();

        try {
            $categories = Category::active()
                ->ordered()
                ->withCount(['menus' => fn($q) => $q->available()])
                ->get();

            $featuredMenus = Menu::with('category')
                ->available()
                ->featured()
                ->limit(8)
                ->get();
        } catch (\Throwable $e) {
            Log::warning('Home page loaded without database data', [
                'error' => $e->getMessage(),
            ]);
        }

        return view('pages.home', compact('categories', 'featuredMenus'));
    }
}
