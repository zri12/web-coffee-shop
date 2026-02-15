<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\IngredientLog;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IngredientController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Display ingredient list
     */
    public function index(Request $request)
    {
        $query = Ingredient::query();

        // Search filter
        if ($request->filled('search')) {
            $query->where('name', 'LIKE', '%' . $request->search . '%');
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $ingredients = $query->orderBy('name')->get();

        // Summary statistics
        $totalIngredients = Ingredient::count();
        $lowStockCount = Ingredient::lowStock()->count();
        $outOfStockCount = Ingredient::outOfStock()->count();
        $mostUsed = $this->stockService->getMostUsedIngredient(7);

        // Get unique categories
        $categories = Ingredient::select('category')->distinct()->pluck('category');

        return view('admin.ingredients.index', compact(
            'ingredients',
            'totalIngredients',
            'lowStockCount',
            'outOfStockCount',
            'mostUsed',
            'categories'
        ));
    }

    /**
     * Store new ingredient
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:ingredients,name',
            'category' => 'required|string|max:255',
            'unit' => 'required|in:ml,gram,pcs',
            'stock' => 'required|numeric|min:0',
            'minimum_stock' => 'required|numeric|min:0',
        ]);

        Ingredient::create($validated);

        return redirect()->route('admin.ingredients.index')
            ->with('success', 'Ingredient berhasil ditambahkan');
    }

    /**
     * Update ingredient
     */
    public function update(Request $request, Ingredient $ingredient)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:ingredients,name,' . $ingredient->id,
            'category' => 'required|string|max:255',
            'unit' => 'required|in:ml,gram,pcs',
            'stock' => 'required|numeric|min:0',
            'minimum_stock' => 'required|numeric|min:0',
        ]);

        $ingredient->update($validated);

        return redirect()->route('admin.ingredients.index')
            ->with('success', 'Ingredient berhasil diupdate');
    }

    /**
     * Delete ingredient
     */
    public function destroy(Ingredient $ingredient)
    {
        // Check if ingredient is used in any recipes
        if (!$ingredient->canBeDeleted()) {
            return redirect()->route('admin.ingredients.index')
                ->with('error', 'Tidak dapat menghapus ingredient yang digunakan dalam resep produk');
        }

        $ingredient->delete();

        return redirect()->route('admin.ingredients.index')
            ->with('success', 'Ingredient berhasil dihapus');
    }

    /**
     * Restock ingredient
     */
    public function restock(Request $request, Ingredient $ingredient)
    {
        $validated = $request->validate([
            'quantity' => 'required|numeric|min:0.01',
            'note' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $ingredient->restockIngredient(
                $validated['quantity'],
                $validated['note'] ?? 'Manual restock'
            );

            DB::commit();

            return redirect()->route('admin.ingredients.index')
                ->with('success', "Berhasil restock {$ingredient->name}");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.ingredients.index')
                ->with('error', 'Gagal restock: ' . $e->getMessage());
        }
    }

    /**
     * Show ingredient history
     */
    public function history(Ingredient $ingredient)
    {
        $logs = $ingredient->logs()
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('admin.ingredients.history', compact('ingredient', 'logs'));
    }
}
