<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductRecipe;
use App\Models\Menu;
use App\Models\Ingredient;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    /**
     * Store new recipe ingredient
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:menus,id',
            'ingredient_id' => 'required|exists:ingredients,id',
            'quantity_used' => 'required|numeric|min:0.01',
        ]);

        // Check for duplicate
        $exists = ProductRecipe::where('product_id', $validated['product_id'])
            ->where('ingredient_id', $validated['ingredient_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Ingredient sudah ada dalam resep produk ini'
            ], 422);
        }

        $recipe = ProductRecipe::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Ingredient berhasil ditambahkan ke resep',
            'recipe' => $recipe->load('ingredient')
        ]);
    }

    /**
     * Update recipe quantity
     */
    public function update(Request $request, ProductRecipe $recipe)
    {
        $validated = $request->validate([
            'quantity_used' => 'required|numeric|min:0.01',
        ]);

        $recipe->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Quantity berhasil diupdate',
            'recipe' => $recipe->load('ingredient')
        ]);
    }

    /**
     * Remove ingredient from recipe
     */
    public function destroy(ProductRecipe $recipe)
    {
        $recipe->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ingredient berhasil dihapus dari resep'
        ]);
    }

    /**
     * Get ingredients for select dropdown (API)
     */
    public function getIngredients()
    {
        $ingredients = Ingredient::orderBy('name')->get(['id', 'name', 'unit', 'stock']);

        return response()->json([
            'success' => true,
            'ingredients' => $ingredients
        ]);
    }
}
