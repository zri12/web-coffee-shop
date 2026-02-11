<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        // Get all categories with their menus
        $categories = Category::active()
            ->ordered()
            ->with(['menus' => function($query) {
                $query->available()
                      ->orderBy('is_featured', 'desc')
                      ->orderBy('name');
            }])
            ->get();

        return view('pages.menu', compact('categories'));
    }

    public function show(Menu $menu)
    {
        $menu->load('category');
        
        $relatedMenus = Menu::where('category_id', $menu->category_id)
            ->where('id', '!=', $menu->id)
            ->available()
            ->limit(4)
            ->get();

        return view('pages.menu-detail', compact('menu', 'relatedMenus'));
    }
}
