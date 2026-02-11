<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::active()
            ->ordered()
            ->withCount(['menus' => fn($q) => $q->available()])
            ->get();

        $featuredMenus = Menu::with('category')
            ->available()
            ->featured()
            ->limit(8)
            ->get();

        return view('pages.home', compact('categories', 'featuredMenus'));
    }
}
