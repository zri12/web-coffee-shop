<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Get all categories (public)
     */
    public function index(Request $request)
    {
        $query = Category::query();

        // Filter by active status
        if ($request->boolean('active_only', true)) {
            $query->active();
        }

        $categories = $query->ordered()
            ->withCount(['menus' => function ($q) {
                $q->available();
            }])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * Get single category
     */
    public function show(Category $category)
    {
        $category->load(['menus' => function ($q) {
            $q->available()->orderBy('name');
        }]);

        return response()->json([
            'success' => true,
            'data' => $category,
        ]);
    }

    /**
     * Create new category (Admin only)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $category = Category::create($request->only([
            'name', 'slug', 'description', 'is_active', 'sort_order'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil ditambahkan',
            'data' => $category,
        ], 201);
    }

    /**
     * Update category (Admin only)
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $category->update($request->only([
            'name', 'slug', 'description', 'is_active', 'sort_order'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil diperbarui',
            'data' => $category,
        ]);
    }

    /**
     * Delete category (Admin only)
     */
    public function destroy(Category $category)
    {
        // Check if category has menus
        if ($category->menus()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus kategori yang masih memiliki menu',
            ], 422);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus',
        ]);
    }
}
