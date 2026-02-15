@extends('layouts.admin')

@section('title', 'Storage & Inventory Management')

@section('content')
<div class="min-h-screen bg-gray-50/50 p-6 space-y-8 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Storage & Inventory</h1>
            <p class="text-gray-500 mt-1">Manage ingredients stock levels and monitor real-time availability</p>
        </div>
        <button onclick="openAddModal()" class="inline-flex items-center justify-center px-5 py-2.5 bg-primary text-white font-medium rounded-xl hover:bg-primary/90 transition-all shadow-lg shadow-primary/20 hover:shadow-primary/40 active:scale-95 group">
            <span class="material-symbols-outlined text-[20px] mr-2 transition-transform group-hover:rotate-90">add</span>
            Add Ingredient
        </button>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Ingredients -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] hover:shadow-[0_8px_30px_-4px_rgba(6,81,237,0.1)] transition-all duration-300 group">
            <div class="flex justify-between items-start">
                <div class="p-2 bg-blue-50 text-blue-600 rounded-xl group-hover:scale-110 transition-transform duration-300">
                    <span class="material-symbols-outlined text-[24px]">inventory_2</span>
                </div>
                <span class="flex items-center text-xs font-semibold text-green-600 bg-green-50 px-2 py-1 rounded-lg">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                    Active
                </span>
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-bold text-gray-900 tracking-tight">{{ $totalIngredients }}</h3>
                <p class="text-sm font-medium text-gray-400 mt-1">Total Ingredients</p>
            </div>
        </div>

        <!-- Low Stock -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] hover:shadow-[0_8px_30px_-4px_rgba(6,81,237,0.1)] transition-all duration-300 group">
            <div class="flex justify-between items-start">
                <div class="p-2 bg-amber-50 text-amber-600 rounded-xl group-hover:scale-110 transition-transform duration-300">
                    <span class="material-symbols-outlined text-[24px]">warning</span>
                </div>
                @if($lowStockCount > 0)
                <span class="flex items-center text-xs font-semibold text-amber-600 bg-amber-50 px-2 py-1 rounded-lg animate-pulse">
                    Action needed
                </span>
                @endif
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-bold text-gray-900 tracking-tight">{{ $lowStockCount }}</h3>
                <p class="text-sm font-medium text-gray-400 mt-1">Low Stock Items</p>
            </div>
        </div>

        <!-- Out of Stock -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] hover:shadow-[0_8px_30px_-4px_rgba(6,81,237,0.1)] transition-all duration-300 group">
            <div class="flex justify-between items-start">
                <div class="p-2 bg-red-50 text-red-600 rounded-xl group-hover:scale-110 transition-transform duration-300">
                    <span class="material-symbols-outlined text-[24px]">block</span>
                </div>
                @if($outOfStockCount > 0)
                <span class="flex items-center text-xs font-semibold text-red-600 bg-red-50 px-2 py-1 rounded-lg">
                    Critical
                </span>
                @endif
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-bold text-gray-900 tracking-tight">{{ $outOfStockCount }}</h3>
                <p class="text-sm font-medium text-gray-400 mt-1">Out of Stock</p>
            </div>
        </div>

        <!-- Most Used -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] hover:shadow-[0_8px_30px_-4px_rgba(6,81,237,0.1)] transition-all duration-300 group">
            <div class="flex justify-between items-start">
                <div class="p-2 bg-purple-50 text-purple-600 rounded-xl group-hover:scale-110 transition-transform duration-300">
                    <span class="material-symbols-outlined text-[24px]">trending_up</span>
                </div>
                <span class="text-xs font-semibold text-purple-600 bg-purple-50 px-2 py-1 rounded-lg">7 Days</span>
            </div>
            <div class="mt-4">
                <h3 class="text-lg font-bold text-gray-900 tracking-tight truncate" title="{{ $mostUsed['ingredient_name'] ?? 'N/A' }}">
                    {{ $mostUsed['ingredient_name'] ?? 'N/A' }}
                </h3>
                <p class="text-sm font-medium text-gray-400 mt-1">Top Ingredient</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)]">
        <!-- Toolbar -->
        <div class="p-5 border-b border-gray-50 flex flex-col sm:flex-row gap-4 justify-between items-center">
            <div class="relative w-full sm:w-72">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 material-symbols-outlined text-[20px]">search</span>
                <input type="text" id="searchInput" placeholder="Search ingredients..." 
                       class="w-full pl-10 pr-4 py-2 bg-gray-50 border-gray-100 rounded-xl focus:bg-white focus:border-primary focus:ring-primary/20 transition-all text-sm font-medium placeholder-gray-400 text-gray-600">
            </div>

            <div class="flex gap-2 w-full sm:w-auto">
                <select id="categoryFilter" class="w-full sm:w-40 px-3 py-2 bg-gray-50 border-gray-100 rounded-xl focus:bg-white focus:border-primary focus:ring-primary/20 text-sm font-medium text-gray-600 cursor-pointer">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                    <option value="{{ $category }}">{{ $category }}</option>
                    @endforeach
                </select>

                <select id="statusFilter" class="w-full sm:w-36 px-3 py-2 bg-gray-50 border-gray-100 rounded-xl focus:bg-white focus:border-primary focus:ring-primary/20 text-sm font-medium text-gray-600 cursor-pointer">
                    <option value="">All Status</option>
                    <option value="Aman">Normal</option>
                    <option value="Hampir Habis">Low Stock</option>
                    <option value="Habis">Empty</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Ingredient</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Stock Level</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50" id="ingredientsTable">
                    @forelse($ingredients as $ingredient)
                    <tr class="hover:bg-gray-50/80 transition-colors group ingredient-row"
                        data-name="{{ strtolower($ingredient->name) }}"
                        data-category="{{ $ingredient->category }}"
                        data-status="{{ $ingredient->status }}">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="bg-primary/5 p-2 rounded-lg mr-3 group-hover:bg-primary/10 transition-colors">
                                    <span class="material-symbols-outlined text-primary text-[20px]">inventory_2</span>
                                </div>
                                <span class="font-semibold text-gray-900">{{ $ingredient->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-gray-100 text-gray-600">
                                {{ $ingredient->category }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-gray-900">{{ number_format($ingredient->stock, 2) }} <span class="text-gray-400 font-normal ml-0.5">{{ $ingredient->unit }}</span></span>
                                <span class="text-xs text-gray-400">Min: {{ number_format($ingredient->minimum_stock, 2) }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusClass = match($ingredient->status) {
                                    'Aman' => 'bg-green-50 text-green-700 border-green-100',
                                    'Hampir Habis' => 'bg-amber-50 text-amber-700 border-amber-100',
                                    'Habis' => 'bg-red-50 text-red-700 border-red-100',
                                    default => 'bg-gray-50 text-gray-700 border-gray-100'
                                };
                                $statusIcon = match($ingredient->status) {
                                    'Aman' => 'check_circle',
                                    'Hampir Habis' => 'warning',
                                    'Habis' => 'error',
                                    default => 'help'
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $statusClass }}">
                                <span class="material-symbols-outlined text-[14px] mr-1.5">{{ $statusIcon }}</span>
                                {{ $ingredient->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button onclick="openRestockModal({{ $ingredient->id }})" 
                                        class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors tooltip" title="Restock">
                                    <span class="material-symbols-outlined text-[20px]">add_shopping_cart</span>
                                </button>
                                <button onclick="openEditModal({{ $ingredient->id }})" 
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors tooltip" title="Edit">
                                    <span class="material-symbols-outlined text-[20px]">edit</span>
                                </button>
                                <a href="{{ route('admin.ingredients.history', $ingredient) }}" 
                                   class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition-colors tooltip" title="History">
                                    <span class="material-symbols-outlined text-[20px]">history</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="bg-gray-50 p-4 rounded-full mb-3">
                                    <span class="material-symbols-outlined text-gray-400 text-4xl">inventory_2</span>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900">No ingredients found</h3>
                                <p class="text-gray-500 max-w-sm mt-1">Get started by adding your first ingredient to track inventory.</p>
                                <button onclick="openAddModal()" class="mt-4 text-primary font-medium hover:text-primary/80">
                                    Add New Ingredient
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination (if needed) -->
        <div class="px-6 py-4 border-t border-gray-50 bg-gray-50/30">
            <p class="text-xs text-gray-500 text-center">Showing all {{ count($ingredients) }} ingredients</p>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="ingredientModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-900/20 backdrop-blur-sm transition-opacity opacity-0" id="modalBackdrop"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <!-- Modal Panel -->
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" id="modalPanel">
                
                <!-- Modal Header -->
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modalTitle">Add Ingredient</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Enter ingredient details below</p>
                    </div>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500 transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form id="ingredientForm" class="px-6 py-6 space-y-5">
                    @csrf
                    <input type="hidden" id="ingredientId" name="ingredient_id">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Ingredient Name</label>
                        <input type="text" name="name" id="ingredientName" required placeholder="e.g. Espresso Beans"
                               class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Category</label>
                        <div class="relative">
                            <input type="text" name="category" id="ingredientCategory" required placeholder="Select or type..." list="categoryList"
                               class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all text-sm">
                            <datalist id="categoryList">
                                @foreach($categories as $category)
                                <option value="{{ $category }}">
                                @endforeach
                            </datalist>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Unit</label>
                            <select name="unit" id="ingredientUnit" required
                                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all text-sm appearance-none">
                                <option value="ml">ml (Liquid)</option>
                                <option value="gram">gram (Solid)</option>
                                <option value="pcs">pcs (Countable)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Current Stock</label>
                            <input type="number" name="stock" id="ingredientStock" step="0.01" min="0" required
                                   class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Minimum Stock Alert</label>
                        <div class="relative">
                            <input type="number" name="minimum_stock" id="ingredientMinStock" step="0.01" min="0" required
                                   class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all text-sm pl-10">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 material-symbols-outlined text-[18px]">notifications</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-1.5">System will alert when stock falls below this amount</p>
                    </div>

                    <div class="pt-2">
                        <button type="submit" 
                                class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all">
                            Save Ingredient
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Restock Modal -->
<div id="restockModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/20 backdrop-blur-sm transition-opacity opacity-0" id="restockBackdrop"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" id="restockPanel">
                
                <div class="bg-green-50 px-6 py-4 border-b border-green-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-green-100 rounded-lg text-green-600">
                            <span class="material-symbols-outlined text-[20px]">add_shopping_cart</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold leading-6 text-green-900">Restock Stock</h3>
                            <p class="text-xs text-green-700 mt-0.5">Add quantity to existing stock</p>
                        </div>
                    </div>
                    <button onclick="closeRestockModal()" class="text-green-700 hover:text-green-900 transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form id="restockForm" class="px-6 py-6 space-y-5">
                    @csrf
                    <input type="hidden" id="restockIngredientId">
                    
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Current Stock</label>
                        <input type="text" id="currentStock" readonly
                               class="block w-full border-0 bg-transparent p-0 text-gray-900 font-bold text-2xl focus:ring-0">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Add Quantity</label>
                        <input type="number" name="quantity" id="restockQuantity" step="0.01" min="0.01" required placeholder="0.00"
                               class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:border-green-500 focus:ring-4 focus:ring-green-500/10 transition-all text-lg font-bold text-green-600">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Note (Optional)</label>
                        <textarea name="note" id="restockNote" rows="2" placeholder="e.g. Weekly supply from Vendor A"
                                  class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:border-green-500 focus:ring-4 focus:ring-green-500/10 transition-all text-sm"></textarea>
                    </div>

                    <div class="pt-2">
                        <button type="submit" 
                                class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all">
                            Confirm Restock
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// UI functions using native classes for transitions
function toggleModal(modalId, show) {
    const modal = document.getElementById(modalId);
    const backdrop = modal.querySelector('div[id$="Backdrop"]');
    const panel = modal.querySelector('div[id$="Panel"]');
    
    if (show) {
        modal.classList.remove('hidden');
        // Small timeout to allow removing hidden class before animating opacity
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');
            panel.classList.add('opacity-100', 'translate-y-0', 'sm:scale-100');
        }, 10);
    } else {
        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'translate-y-0', 'sm:scale-100');
        panel.classList.add('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
}

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
    toggleModal('ingredientModal', true);
}

function openEditModal(id) {
    document.getElementById('modalTitle').textContent = 'Edit Ingredient';
    
    // Fetch data
    fetch(`/admin/ingredients/${id}`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('ingredientId').value = data.id;
            document.getElementById('ingredientName').value = data.name;
            document.getElementById('ingredientCategory').value = data.category;
            document.getElementById('ingredientUnit').value = data.unit;
            document.getElementById('ingredientStock').value = data.stock;
            document.getElementById('ingredientMinStock').value = data.minimum_stock;
            
            toggleModal('ingredientModal', true);
        })
        .catch(err => {
            alert('Failed to load ingredient data');
            console.error(err);
        });
}

function closeModal() {
    toggleModal('ingredientModal', false);
}

function openRestockModal(id) {
    toggleModal('restockModal', true);
    fetch(`/admin/ingredients/${id}`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('restockIngredientId').value = id;
            document.getElementById('currentStock').value = `${data.stock} ${data.unit}`;
        });
}

function closeRestockModal() {
    toggleModal('restockModal', false);
}

// Form submissions
document.getElementById('ingredientForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const id = document.getElementById('ingredientId').value;
    const url = id ? `/admin/ingredients/${id}` : '/admin/ingredients';
    
    // For PUT method in Laravel (HTML forms don't support PUT natively)
    if (id) {
        formData.append('_method', 'PUT');
    }

    fetch(url, {
        method: 'POST', // Use POST with _method override for Laravel
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
             // Do NOT set Content-Type to application/json when sending FormData
             'Accept': 'application/json' 
        },
        body: formData
    })
    .then(async response => {
        if (response.ok) {
            location.reload();
        } else {
            const data = await response.json();
            alert(data.message || 'Error occurred');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Network error occurred');
    });
});

document.getElementById('restockForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('restockIngredientId').value;
    const formData = new FormData(this);
    
    fetch(`/admin/ingredients/${id}/restock`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(async response => {
        if (response.ok) {
            location.reload();
        } else {
            const data = await response.json();
            alert(data.message || 'Error occurred');
        }
    });
});
</script>
@endpush
@endsection
