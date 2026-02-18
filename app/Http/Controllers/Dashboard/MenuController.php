<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Ingredient;
use App\Models\ProductRecipe;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

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
        $ingredients = Ingredient::orderBy('name')->get();
        return view('dashboard.menus.form', compact('categories', 'ingredients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'recipes' => 'array',
            'recipes.*.ingredient_id' => 'nullable|exists:ingredients,id',
            'recipes.*.quantity_used' => 'nullable|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($request) {
            $data = $request->only(['name', 'category_id', 'price', 'description']);
            $data['slug'] = Str::slug($request->name);
            $data['is_available'] = $request->boolean('is_available', true);
            $data['is_featured'] = $request->boolean('is_featured', false);
            
            if ($request->hasFile('image')) {
                $filename = time() . '.' . $request->image->extension();
                $request->image->move(public_path('images/menus'), $filename);
                $data['image'] = $filename;
            }
            
            $menu = Menu::create($data);

            // Store recipe
            $recipes = collect($request->input('recipes', []))
                ->filter(fn($r) => !empty($r['ingredient_id']) && !empty($r['quantity_used']) && $r['quantity_used'] > 0)
                ->map(function ($r) use ($menu) {
                    return [
                        'product_id' => $menu->id,
                        'ingredient_id' => $r['ingredient_id'],
                        'quantity_used' => $r['quantity_used'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                });
            if ($recipes->isNotEmpty()) {
                ProductRecipe::insert($recipes->toArray());
            }
        });
        
        return redirect()->route('dashboard.menus')->with('success', 'Menu berhasil ditambahkan');
    }

    public function edit(Menu $menu)
    {
        $categories = Category::active()->ordered()->get();
        $ingredients = Ingredient::orderBy('name')->get();
        $menuRecipes = $menu->recipes()->with('ingredient')->get();
        return view('dashboard.menus.form', compact('menu', 'categories', 'ingredients', 'menuRecipes'));
    }

    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'recipes' => 'array',
            'recipes.*.ingredient_id' => 'nullable|exists:ingredients,id',
            'recipes.*.quantity_used' => 'nullable|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($request, $menu) {
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

            // Sync recipe
            $recipes = collect($request->input('recipes', []))
                ->filter(fn($r) => !empty($r['ingredient_id']) && !empty($r['quantity_used']) && $r['quantity_used'] > 0)
                ->map(function ($r) use ($menu) {
                    return [
                        'product_id' => $menu->id,
                        'ingredient_id' => $r['ingredient_id'],
                        'quantity_used' => $r['quantity_used'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                });

            ProductRecipe::where('product_id', $menu->id)->delete();
            if ($recipes->isNotEmpty()) {
                ProductRecipe::insert($recipes->toArray());
            }
        });
        
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
}
