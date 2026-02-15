@extends('layouts.dashboard')

@section('title', 'Edit Menu')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-[#181411] dark:text-white">Edit Menu Item</h1>
            <p class="text-[#897561] text-sm">Update product details</p>
        </div>
        <a href="{{ route('admin.menus') }}" class="px-4 py-2 border border-[#e6e0db] dark:border-[#3d362e] text-[#897561] rounded-lg hover:bg-gray-50 dark:hover:bg-[#2c241b] transition-colors flex items-center gap-2">
            <span class="material-symbols-outlined">arrow_back</span>
            Back to Menus
        </a>
    </div>

    <!-- Edit Form -->
    <div class="bg-white dark:bg-[#1a1612] rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm p-6">
        <form action="{{ route('admin.menus.update', $menu->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Product Name -->
                <div>
                    <label class="block text-sm font-bold text-[#181411] dark:text-white mb-3">Product Name *</label>
                    <input type="text" name="name" value="{{ old('name', $menu->name) }}" required
                           class="w-full px-4 py-3 rounded-lg border border-[#e6e0db] dark:border-[#3d362e] bg-white dark:bg-[#0f0d0b] text-[#181411] dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent text-base">
                    @error('name')
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-sm font-bold text-[#181411] dark:text-white mb-3">Category *</label>
                    <select name="category_id" required
                            class="w-full px-4 py-3 rounded-lg border border-[#e6e0db] dark:border-[#3d362e] bg-white dark:bg-[#0f0d0b] text-[#181411] dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent text-base">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $menu->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Price -->
                <div>
                    <label class="block text-sm font-bold text-[#181411] dark:text-white mb-3">Price (Rp) *</label>
                    <input type="number" name="price" value="{{ old('price', $menu->price) }}" required min="0" step="1000"
                           class="w-full px-4 py-3 rounded-lg border border-[#e6e0db] dark:border-[#3d362e] bg-white dark:bg-[#0f0d0b] text-[#181411] dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent text-base">
                    @error('price')
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Stock Status -->
                <div>
                    <label class="block text-sm font-bold text-[#181411] dark:text-white mb-3">Stock Status</label>
                    <div class="flex items-center gap-4 pt-2">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_available" value="1" {{ old('is_available', $menu->is_available) ? 'checked' : '' }}
                                   class="w-5 h-5 text-primary border-[#e6e0db] dark:border-[#3d362e] rounded focus:ring-primary">
                            <span class="ml-3 text-sm font-medium text-[#181411] dark:text-white">Available</span>
                        </label>
                    </div>
                    @error('is_available')
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-bold text-[#181411] dark:text-white mb-3">Description</label>
                <textarea name="description" rows="4"
                          class="w-full px-4 py-3 rounded-lg border border-[#e6e0db] dark:border-[#3d362e] bg-white dark:bg-[#0f0d0b] text-[#181411] dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent text-base">{{ old('description', $menu->description) }}</textarea>
                @error('description')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Product Image -->
            <div>
                <label class="block text-sm font-bold text-[#181411] dark:text-white mb-3">Product Image</label>
                
                <!-- Current Image -->
                @if($menu->image)
                <div class="mb-4">
                    <p class="text-xs font-bold text-[#897561] uppercase tracking-wider mb-2">Current Image:</p>
                    <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="w-32 h-32 object-cover rounded-lg border border-[#e6e0db] dark:border-[#3d362e]">
                </div>
                @endif

                <!-- File Input -->
                <input type="file" name="image" accept="image/*"
                       class="w-full px-4 py-3 rounded-lg border border-[#e6e0db] dark:border-[#3d362e] bg-white dark:bg-[#0f0d0b] text-[#181411] dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent text-base">
                <p class="text-xs text-[#897561] mt-2">Leave empty to keep current image. Max size: 2MB. Supported formats: JPG, PNG, WEBP</p>
                @error('image')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Recipe / Composition Section -->
            <div class="border-t border-[#e6e0db] dark:border-[#3d362e] pt-6 mt-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">restaurant</span>
                            Recipe / Composition
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Define ingredients needed per serving of this product</p>
                    </div>
                    <button type="button" onclick="openAddIngredientModal()" 
                            class="px-4 py-2.5 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all shadow-lg shadow-green-600/20 hover:shadow-green-600/40 flex items-center gap-2 font-medium">
                        <span class="material-symbols-outlined text-[18px]">add</span>
                        Add Ingredient
                    </button>
                </div>

                <!-- Recipe Table -->
                <div id="recipeTableContainer" class="bg-white dark:bg-[#1a1612] rounded-xl border border-gray-200 dark:border-[#3d362e] overflow-hidden shadow-sm">
                    <table class="w-full" id="recipeTable">
                        <thead class="bg-gray-50 dark:bg-[#0f0d0b]">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Ingredient</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Quantity Used</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Unit</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-[#3d362e]" id="recipeTableBody">
                            <!-- Will be populated via JavaScript -->
                        </tbody>
                    </table>
                    <div id="emptyRecipeState" class="hidden p-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="bg-gray-50 dark:bg-[#0f0d0b] p-4 rounded-full mb-3">
                                <span class="material-symbols-outlined text-gray-400 text-4xl">restaurant</span>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">No ingredients added yet</h3>
                            <p class="text-gray-500 dark:text-gray-400 max-w-sm mt-1">Start building your recipe by adding ingredients and their quantities.</p>
                            <button onclick="openAddIngredientModal()" class="mt-4 text-primary font-medium hover:text-primary/80">
                                Add First Ingredient
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-4 mt-8 pt-6 border-t border-[#e6e0db] dark:border-[#3d362e]">
                <a href="{{ route('admin.menus') }}"
                   class="flex-1 px-6 py-3 border border-[#e6e0db] dark:border-[#3d362e] text-[#897561] dark:text-[#a89c92] rounded-lg hover:bg-[#f4f2f0] dark:hover:bg-[#3e2d23] transition-colors font-medium text-center">
                    Cancel
                </a>
                <button type="submit"
                        class="flex-1 px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors font-medium">
                    Update Menu
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add/Edit Ingredient Modal -->
<div id="ingredientModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/20 backdrop-blur-sm transition-opacity opacity-0" id="ingredientBackdrop"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-[#1a1612] text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" id="ingredientPanel">
                
                <div class="bg-green-50 dark:bg-green-900/20 px-6 py-4 border-b border-green-100 dark:border-green-800 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-green-100 dark:bg-green-800 rounded-lg text-green-600 dark:text-green-300">
                            <span class="material-symbols-outlined text-[20px]">restaurant</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold leading-6 text-green-900 dark:text-green-100" id="ingredientModalTitle">Add Ingredient to Recipe</h3>
                            <p class="text-xs text-green-700 dark:text-green-300 mt-0.5">Specify quantity per serving</p>
                        </div>
                    </div>
                    <button onclick="closeIngredientModal()" class="text-green-700 dark:text-green-300 hover:text-green-900 dark:hover:text-green-100 transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form id="ingredientForm" class="px-6 py-6 space-y-5">
                    @csrf
                    <input type="hidden" id="recipeId" name="recipe_id">
                    <input type="hidden" name="product_id" value="{{ $menu->id }}">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Select Ingredient</label>
                        <select name="ingredient_id" id="ingredientSelect" required
                                class="w-full px-4 py-2.5 bg-white dark:bg-[#0f0d0b] border border-gray-200 dark:border-[#3d362e] rounded-xl focus:border-green-500 focus:ring-4 focus:ring-green-500/10 transition-all text-sm">
                            <option value="">Choose ingredient...</option>
                        </select>
                        <p class="text-xs text-gray-400 mt-1.5" id="ingredientStockInfo"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Quantity Used (per serving)</label>
                        <div class="flex gap-3">
                            <input type="number" name="quantity_used" id="quantityUsed" step="0.01" min="0.01" required placeholder="0.00"
                                   class="flex-1 px-4 py-2.5 bg-white dark:bg-[#0f0d0b] border border-gray-200 dark:border-[#3d362e] rounded-xl focus:border-green-500 focus:ring-4 focus:ring-green-500/10 transition-all text-lg font-bold text-green-600">
                            <input type="text" id="unitDisplay" readonly placeholder="unit"
                                   class="w-24 px-4 py-2.5 bg-gray-50 dark:bg-[#0f0d0b] border border-gray-200 dark:border-[#3d362e] rounded-xl text-sm text-gray-500 text-center">
                        </div>
                        <p class="text-xs text-gray-400 mt-1.5">Amount needed to make one serving of this product</p>
                    </div>

                    <div class="pt-2">
                        <button type="submit" 
                                class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all">
                            <span id="submitBtnText">Add to Recipe</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const menuId = {{ $menu->id }};
let ingredients = [];
let recipes = [];

// Load initial data
document.addEventListener('DOMContentLoaded', function() {
    loadIngredients();
    loadRecipes();
});

// Load available ingredients
function loadIngredients() {
    fetch('/admin/api/ingredients')
        .then(r => r.json())
        .then(data => {
            ingredients = data.ingredients;
            populateIngredientSelect();
        });
}

// Load existing recipes
function loadRecipes() {
    fetch(`/admin/menus/${menuId}/recipes`)
        .then(r => r.json())
        .then(data => {
            recipes = data.recipes || [];
            renderRecipeTable();
        })
        .catch(err => {
            console.error('Failed to load recipes:', err);
            renderRecipeTable(); // Show empty state
        });
}

// Populate ingredient dropdown
function populateIngredientSelect() {
    const select = document.getElementById('ingredientSelect');
    select.innerHTML = '<option value="">Choose ingredient...</option>';
    
    ingredients.forEach(ing => {
        const option = document.createElement('option');
        option.value = ing.id;
        option.textContent = `${ing.name} (Stock: ${ing.stock} ${ing.unit})`;
        option.dataset.unit = ing.unit;
        option.dataset.stock = ing.stock;
        select.appendChild(option);
    });
}

// Render recipe table
function renderRecipeTable() {
    const tbody = document.getElementById('recipeTableBody');
    const emptyState = document.getElementById('emptyRecipeState');
    
    if (recipes.length === 0) {
        tbody.innerHTML = '';
        emptyState.classList.remove('hidden');
        return;
    }
    
    emptyState.classList.add('hidden');
    tbody.innerHTML = recipes.map(recipe => `
        <tr class="hover:bg-gray-50 dark:hover:bg-[#0f0d0b] transition-colors group">
            <td class="px-6 py-4">
                <div class="flex items-center">
                    <div class="bg-primary/5 p-2 rounded-lg mr-3">
                        <span class="material-symbols-outlined text-primary text-[18px]">inventory_2</span>
                    </div>
                    <span class="font-semibold text-gray-900 dark:text-white">${recipe.ingredient.name}</span>
                </div>
            </td>
            <td class="px-6 py-4 text-right">
                <span class="text-lg font-bold text-gray-900 dark:text-white">${parseFloat(recipe.quantity_used).toFixed(2)}</span>
            </td>
            <td class="px-6 py-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-gray-100 dark:bg-[#3d362e] text-gray-600 dark:text-gray-300">
                    ${recipe.ingredient.unit}
                </span>
            </td>
            <td class="px-6 py-4">
                <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button onclick="editRecipe(${recipe.id})" 
                            class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors" title="Edit">
                        <span class="material-symbols-outlined text-[18px]">edit</span>
                    </button>
                    <button onclick="deleteRecipe(${recipe.id})" 
                            class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="Delete">
                        <span class="material-symbols-outlined text-[18px]">delete</span>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

// Modal functions
function openAddIngredientModal() {
    document.getElementById('ingredientModalTitle').textContent = 'Add Ingredient to Recipe';
    document.getElementById('submitBtnText').textContent = 'Add to Recipe';
    document.getElementById('ingredientForm').reset();
    document.getElementById('recipeId').value = '';
    document.getElementById('ingredientSelect').disabled = false;
    toggleModal('ingredientModal', true);
}

function editRecipe(recipeId) {
    const recipe = recipes.find(r => r.id === recipeId);
    if (!recipe) return;
    
    document.getElementById('ingredientModalTitle').textContent = 'Edit Recipe Quantity';
    document.getElementById('submitBtnText').textContent = 'Update Quantity';
    document.getElementById('recipeId').value = recipe.id;
    document.getElementById('ingredientSelect').value = recipe.ingredient_id;
    document.getElementById('ingredientSelect').disabled = true;
    document.getElementById('quantityUsed').value = recipe.quantity_used;
    document.getElementById('unitDisplay').value = recipe.ingredient.unit;
    
    toggleModal('ingredientModal', true);
}

function closeIngredientModal() {
    toggleModal('ingredientModal', false);
}

function toggleModal(modalId, show) {
    const modal = document.getElementById(modalId);
    const backdrop = modal.querySelector('div[id$="Backdrop"]');
    const panel = modal.querySelector('div[id$="Panel"]');
    
    if (show) {
        modal.classList.remove('hidden');
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

// Update unit display when ingredient selected
document.getElementById('ingredientSelect').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const unit = selected.dataset.unit || '';
    const stock = selected.dataset.stock || '';
    
    document.getElementById('unitDisplay').value = unit;
    document.getElementById('ingredientStockInfo').textContent = stock ? `Current stock: ${stock} ${unit}` : '';
});

// Form submission
document.getElementById('ingredientForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const recipeId = document.getElementById('recipeId').value;
    
    const url = recipeId ? `/admin/recipes/${recipeId}` : '/admin/recipes';
    const method = recipeId ? 'PUT' : 'POST';
    
    // Convert FormData to JSON
    const data = {};
    formData.forEach((value, key) => data[key] = value);
    
    if (recipeId) {
        data._method = 'PUT';
    }
    
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(async response => {
        const result = await response.json();
        if (response.ok) {
            closeIngredientModal();
            loadRecipes(); // Reload table
            showNotification(result.message, 'success');
        } else {
            showNotification(result.message || 'Error occurred', 'error');
        }
    })
    .catch(err => {
        console.error(err);
        showNotification('Network error occurred', 'error');
    });
});

// Delete recipe
function deleteRecipe(recipeId) {
    if (!confirm('Remove this ingredient from recipe?')) return;
    
    fetch(`/admin/recipes/${recipeId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ _method: 'DELETE' })
    })
    .then(async response => {
        const result = await response.json();
        if (response.ok) {
            loadRecipes();
            showNotification(result.message, 'success');
        } else {
            showNotification(result.message || 'Error occurred', 'error');
        }
    });
}

// Notification helper
function showNotification(message, type = 'success') {
    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    const notification = document.createElement('div');
    notification.className = `fixed bottom-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 text-sm`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>

@if(session('error'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
     class="fixed bottom-4 right-4 bg-red-500 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg shadow-lg z-50 text-sm sm:text-base">
    {{ session('error') }}
</div>
@endif

@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
     class="fixed bottom-4 right-4 bg-green-500 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg shadow-lg z-50 text-sm sm:text-base">
    {{ session('success') }}
</div>
@endif
@endsection
