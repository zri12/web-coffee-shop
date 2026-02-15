@extends('layouts.admin')

@section('title', 'Storage & Inventory Management')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-amber-50 to-orange-50 p-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Storage & Inventory Management</h1>
        <p class="text-gray-600">Manage ingredients, track stock levels, and monitor inventory</p>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Total Ingredients -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Ingredients</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalIngredients }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-boxes text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Low Stock Count -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Low Stock</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $lowStockCount }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Out of Stock -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Out of Stock</p>
                    <p class="text-3xl font-bold text-red-600">{{ $outOfStockCount }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Most Used -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Most Used (7 days)</p>
                    <p class="text-lg font-bold text-gray-900">{{ $mostUsed['ingredient_name'] ?? 'N/A' }}</p>
                    @if($mostUsed)
                    <p class="text-xs text-gray-500">{{ number_format($mostUsed['total_used'], 2) }} {{ $mostUsed['unit'] }}</p>
                    @endif
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Actions -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
            <!-- Search & Filters -->
            <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
                <!-- Search -->
                <input type="text" id="searchInput" placeholder="Search ingredients..." 
                       class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent w-full md:w-64">
                
                <!-- Category Filter -->
                <select id="categoryFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                    <option value="{{ $category }}">{{ $category }}</option>
                    @endforeach
                </select>

                <!-- Status Filter -->
                <select id="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="Aman">Aman</option>
                    <option value="Hampir Habis">Hampir Habis</option>
                    <option value="Habis">Habis</option>
                </select>
            </div>

            <!-- Add Button -->
            <button onclick="openAddModal()" class="px-6 py-2 bg-gradient-to-r from-orange-500 to-amber-600 text-white rounded-lg hover:from-orange-600 hover:to-amber-700 transition-all shadow-md hover:shadow-lg whitespace-nowrap">
                <i class="fas fa-plus mr-2"></i>Add Ingredient
            </button>
        </div>
    </div>

    <!-- Ingredients Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-amber-100 to-orange-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Ingredient Name</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Category</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Stock</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Min Stock</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Last Updated</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody id="ingredientsTable" class="divide-y divide-gray-200">
                    @forelse($ingredients as $ingredient)
                    <tr class="hover:bg-gray-50 transition-colors ingredient-row" 
                        data-name="{{ strtolower($ingredient->name) }}"
                        data-category="{{ $ingredient->category }}"
                        data-status="{{ $ingredient->status }}">
                        <td class="px-6 py-4">
                            <span class="font-medium text-gray-900">{{ $ingredient->name }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">{{ $ingredient->category }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-semibold text-gray-900">{{ number_format($ingredient->stock, 2) }}</span>
                            <span class="text-sm text-gray-500">{{ $ingredient->unit }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">{{ number_format($ingredient->minimum_stock, 2) }} {{ $ingredient->unit }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $ingredient->status_badge_class }}">
                                {{ $ingredient->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $ingredient->updated_at->diffForHumans() }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button onclick="openEditModal({{ $ingredient->id }})" 
                                        class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                    Edit
                                </button>
                                <span class="text-gray-300">|</span>
                                <button onclick="openRestockModal({{ $ingredient->id }})" 
                                        class="text-green-600 hover:text-green-800 font-medium text-sm">
                                    Restock
                                </button>
                                <span class="text-gray-300">|</span>
                                <a href="{{ route('admin.ingredients.history', $ingredient) }}" 
                                   class="text-purple-600 hover:text-purple-800 font-medium text-sm">
                                    History
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-box-open text-4xl mb-3 text-gray-300"></i>
                            <p>No ingredients found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="ingredientModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
        <div class="p-6 border-b border-gray-200">
            <h3 id="modalTitle" class="text-xl font-bold text-gray-900">Add Ingredient</h3>
        </div>
        <form id="ingredientForm" class="p-6 space-y-4">
            @csrf
            <input type="hidden" id="ingredientId" name="ingredient_id">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                <input type="text" name="name" id="ingredientName" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <input type="text" name="category" id="ingredientCategory" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                       placeholder="e.g., Dairy, Coffee, Bakery">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Unit</label>
                <select name="unit" id="ingredientUnit" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    <option value="ml">ml (milliliter)</option>
                    <option value="gram">gram</option>
                    <option value="pcs">pcs (pieces)</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Stock</label>
                    <input type="number" name="stock" id="ingredientStock" step="0.01" min="0" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Min Stock</label>
                    <input type="number" name="minimum_stock" id="ingredientMinStock" step="0.01" min="0" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModal()" 
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-gradient-to-r from-orange-500 to-amber-600 text-white rounded-lg hover:from-orange-600 hover:to-amber-700 transition-all">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Restock Modal -->
<div id="restockModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-900">Restock Ingredient</h3>
        </div>
        <form id="restockForm" class="p-6 space-y-4">
            @csrf
            <input type="hidden" id="restockIngredientId">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Current Stock</label>
                <input type="text" id="currentStock" readonly
                       class="w-full px-4 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-600">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Restock Quantity</label>
                <input type="number" name="quantity" id="restockQuantity" step="0.01" min="0.01" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Supplier Note (Optional)</label>
                <textarea name="note" id="restockNote" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                          placeholder="e.g., Supplier delivery, Manual restock"></textarea>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeRestockModal()" 
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all">
                    Confirm Restock
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Filter functionality
document.getElementById('searchInput').addEventListener('input', filterTable);
document.getElementById('categoryFilter').addEventListener('change', filterTable);
document.getElementById('statusFilter').addEventListener('change', filterTable);

function filterTable() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const category = document.getElementById('categoryFilter').value;
    const status = document.getElementById('statusFilter').value;
    
    document.querySelectorAll('.ingredient-row').forEach(row => {
        const name = row.dataset.name;
        const rowCategory = row.dataset.category;
        const rowStatus = row.dataset.status;
        
        const matchSearch = name.includes(search);
        const matchCategory = !category || rowCategory === category;
        const matchStatus = !status || rowStatus === status;
        
        row.style.display = (matchSearch && matchCategory && matchStatus) ? '' : 'none';
    });
}

// Modal functions
function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Add Ingredient';
    document.getElementById('ingredientForm').reset();
    document.getElementById('ingredientId').value = '';
    document.getElementById('ingredientModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('ingredientModal').classList.add('hidden');
}

function openRestockModal(id) {
    // Fetch ingredient data and populate modal
    fetch(`/admin/ingredients/${id}`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('restockIngredientId').value = id;
            document.getElementById('currentStock').value = `${data.stock} ${data.unit}`;
            document.getElementById('restockModal').classList.remove('hidden');
        });
}

function closeRestockModal() {
    document.getElementById('restockModal').classList.add('hidden');
}

// Form submissions
document.getElementById('ingredientForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const id = document.getElementById('ingredientId').value;
    const url = id ? `/admin/ingredients/${id}` : '/admin/ingredients';
    const method = id ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(Object.fromEntries(formData))
    }).then(() => location.reload());
});

document.getElementById('restockForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('restockIngredientId').value;
    const formData = new FormData(this);
    
    fetch(`/admin/ingredients/${id}/restock`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(Object.fromEntries(formData))
    }).then(() => location.reload());
});
</script>
@endpush
@endsection
