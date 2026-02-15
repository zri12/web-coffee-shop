<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('category')->latest()->paginate(20);
        return view('dashboard.menus.index', compact('menus'));
    }

    public function create()
    {
        $categories = Category::active()->ordered()->get();
        return view('dashboard.menus.form', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);
        
        $data = $request->only(['name', 'category_id', 'price', 'description']);
        $data['slug'] = Str::slug($request->name);
        $data['is_available'] = $request->boolean('is_available', true);
        $data['is_featured'] = $request->boolean('is_featured', false);
        
        if ($request->hasFile('image')) {
            $filename = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images/menus'), $filename);
            $data['image'] = $filename;
        }
        
        Menu::create($data);
        
        return redirect()->route('dashboard.menus')->with('success', 'Menu berhasil ditambahkan');
    }

    public function edit(Menu $menu)
    {
        $categories = Category::active()->ordered()->get();
        $menu->load('recipes.ingredient'); // Eager load recipes with ingredients
        return view('dashboard.menus.form', compact('menu', 'categories'));
    }

    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);
        
        $data = $request->only(['name', 'category_id', 'price', 'description']);
        $data['slug'] = Str::slug($request->name);
        $data['is_available'] = $request->boolean('is_available', true);
        $data['is_featured'] = $request->boolean('is_featured', false);
        
        if ($request->hasFile('image')) {
            $filename = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images/menus'), $filename);
            $data['image'] = $filename;
        }
        
        $menu->update($data);
        
        return redirect()->route('dashboard.menus')->with('success', 'Menu berhasil diperbarui');
    }

    public function destroy(Menu $menu)
    {
        // Check if menu has been ordered
        if ($menu->orderItems()->count() > 0) {
            // Instead of delete, just disable the menu
            $menu->update(['is_available' => false]);
            return back()->with('warning', 'Menu tidak dapat dihapus karena sudah pernah dipesan. Menu telah dinonaktifkan.');
        }
        
        // If menu never been ordered, allow hard delete
        $menu->delete();
        
        return redirect()->route('dashboard.menus')->with('success', 'Menu berhasil dihapus');
    }
    
    
    public function toggleAvailability(Menu $menu)
    {
        $menu->update(['is_available' => !$menu->is_available]);
        
        $status = $menu->is_available ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Menu berhasil {$status}");
    }

    /**
     * Get recipes for a menu (API)
     */
    public function getRecipes(Menu $menu)
    {
        $recipes = $menu->recipes()->with('ingredient')->get();
        
        return response()->json([
            'success' => true,
            'recipes' => $recipes
        ]);
    }
}
