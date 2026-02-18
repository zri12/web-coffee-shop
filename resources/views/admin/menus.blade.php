@extends('layouts.dashboard')

@section('title', 'Menu Management')

@section('content')
<div class="p-6 space-y-6" x-data="{
    showAddModal: false,
    showEditModal: false,
    showDeleteModal: false,
    selectedMenu: null,
    searchQuery: '{{ request('search', '') }}',
    selectedCategory: '{{ request('category', 'all') }}',
    applyFilters() {
        let params = new URLSearchParams();
        if (this.searchQuery) params.append('search', this.searchQuery);
        if (this.selectedCategory !== 'all') params.append('category', this.selectedCategory);
        window.location.href = '{{ route('admin.menus') }}?' + params.toString();
    },
    editMenu(menu) {
        this.selectedMenu = menu;
        this.showEditModal = true;
    },
    deleteMenu(menuId) {
        if(confirm('Are you sure you want to delete this menu item?')) {
            fetch(`/admin/menus/${menuId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            }).then(response => response.json())
            .then(data => {
                if(data.success) {
                    window.location.reload();
                }
            });
        }
    }
}">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#181411] dark:text-white">Menu Management</h1>
            <p class="text-[#897561] text-sm">Maintain your menu items, update prices, and control stock visibility.</p>
        </div>
        <button @click="showAddModal = true" class="bg-primary text-white px-4 py-2 rounded-lg font-medium hover:bg-primary/90 flex items-center gap-2 transition-colors">
            <span class="material-symbols-outlined text-[20px]">add_circle</span>
            Add New Product
        </button>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white dark:bg-[#1a1612] p-4 rounded-xl border border-[#e6e0db] dark:border-[#3d362e] flex flex-col md:flex-row gap-4 items-center justify-between">
        <div class="relative w-full md:w-96">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#897561] text-[20px]">search</span>
            <input type="text" x-model="searchQuery" @keyup.enter="applyFilters()" placeholder="Search by product name or SKU..." class="w-full pl-10 pr-4 py-2 bg-gray-50 dark:bg-[#2c241b] border border-[#e6e0db] dark:border-[#3d362e] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 text-[#181411] dark:text-white placeholder-[#897561]">
        </div>
        
        <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto pb-2 md:pb-0">
            <span class="text-xs font-bold text-[#897561] uppercase tracking-wider whitespace-nowrap mr-2">Filters:</span>
            <button @click="selectedCategory='all'; applyFilters()" :class="selectedCategory === 'all' ? 'bg-orange-100 text-orange-700 border-orange-200' : 'bg-gray-50 dark:bg-[#2c241b] text-[#5c4d40] dark:text-[#a89c92] hover:bg-gray-100 dark:hover:bg-[#3d362e] border-transparent hover:border-[#e6e0db]'" class="px-3 py-1.5 rounded-lg text-sm font-medium whitespace-nowrap border transition-colors">All Items</button>
            @foreach($categories as $category)
            <button @click="selectedCategory='{{ $category->slug }}'; applyFilters()" :class="selectedCategory === '{{ $category->slug }}' ? 'bg-orange-100 text-orange-700 border-orange-200' : 'bg-gray-50 dark:bg-[#2c241b] text-[#5c4d40] dark:text-[#a89c92] hover:bg-gray-100 dark:hover:bg-[#3d362e] border-transparent hover:border-[#e6e0db]'" class="px-3 py-1.5 rounded-lg text-sm font-medium whitespace-nowrap transition-colors border">{{ $category->name }}</button>
            @endforeach
        </div>
    </div>

    <!-- Menu Table -->
    <div class="bg-white dark:bg-[#1a1612] rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-orange-50/50 dark:bg-[#2c241b] border-b border-[#e6e0db] dark:border-[#3d362e]">
                    <tr>
                        <th class="px-6 py-4 font-bold text-[#897561] uppercase text-xs w-20">Image</th>
                        <th class="px-6 py-4 font-bold text-[#897561] uppercase text-xs">Name & SKU</th>
                        <th class="px-6 py-4 font-bold text-[#897561] uppercase text-xs">Category</th>
                        <th class="px-6 py-4 font-bold text-[#897561] uppercase text-xs">Price</th>
                        <th class="px-6 py-4 font-bold text-[#897561] uppercase text-xs">Stock Status</th>
                        <th class="px-6 py-4 font-bold text-[#897561] uppercase text-xs text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e6e0db] dark:divide-[#3d362e]">
                    @foreach($menus as $menu)
                    <tr class="hover:bg-gray-50 dark:hover:bg-[#2c241b]/50 transition-colors group">
                        <td class="px-6 py-4">
                           <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-[#3d362e] overflow-hidden border border-[#e6e0db] dark:border-[#3d362e]">
                                @if($menu->display_image_url)
                                    <img src="{{ $menu->display_image_url }}" alt="{{ $menu->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-[#897561]">
                                        <span class="material-symbols-outlined text-[20px]">image</span>
                                    </div>
                                @endif
                           </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-[#181411] dark:text-white text-base">{{ $menu->name }}</div>
                            <div class="text-[#897561] text-xs mt-0.5">SKU: BEV-{{ str_pad($menu->id, 3, '0', STR_PAD_LEFT) }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded bg-gray-100 dark:bg-[#2c241b] text-[#5c4d40] dark:text-[#a89c92] text-xs font-bold uppercase tracking-wide border border-[#e6e0db] dark:border-[#3d362e]">
                                {{ $menu->category->name ?? 'Uncategorized' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-bold text-[#181411] dark:text-white">
                            Rp {{ number_format($menu->price, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer" checked>
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 dark:peer-focus:ring-orange-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-orange-500"></div>
                                <span class="ms-3 text-sm font-medium text-orange-600 dark:text-orange-400">In Stock</span>
                            </label>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.menus.edit', $menu->id) }}" class="p-1.5 hover:bg-gray-100 dark:hover:bg-[#2c241b] rounded text-[#5c4d40] dark:text-[#a89c92] hover:text-[#181411] dark:hover:text-white transition-colors" title="Edit">
                                    <span class="material-symbols-outlined text-[20px]">edit</span>
                                </a>
                                <button @click="deleteMenu({{ $menu->id }})" class="p-1.5 hover:bg-red-50 dark:hover:bg-red-900/20 rounded text-[#897561] hover:text-red-500 transition-colors" title="Delete">
                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-[#e6e0db] dark:border-[#3d362e] flex items-center justify-between bg-gray-50/50 dark:bg-[#1a1612]">
            <p class="text-sm text-[#5c4d40] dark:text-[#a89c92]">
                Showing <span class="font-bold text-[#181411] dark:text-white">{{ $menus->firstItem() ?? 0 }}</span> to <span class="font-bold text-[#181411] dark:text-white">{{ $menus->lastItem() ?? 0 }}</span> of <span class="font-bold text-[#181411] dark:text-white">{{ $menus->total() }}</span> results
            </p>
            <div class="flex gap-1">
                @if ($menus->onFirstPage())
                    <span class="px-3 py-1 border border-[#e6e0db] dark:border-[#3d362e] rounded bg-gray-100 dark:bg-[#2c241b] text-[#897561] opacity-50 text-sm cursor-not-allowed">Prev</span>
                @else
                    <a href="{{ $menus->previousPageUrl() }}" class="px-3 py-1 border border-[#e6e0db] dark:border-[#3d362e] rounded bg-white dark:bg-[#1a1612] text-[#5c4d40] dark:text-[#a89c92] hover:bg-gray-50 dark:hover:bg-[#2c241b] text-sm">Prev</a>
                @endif
                
                @foreach ($menus->getUrlRange(1, $menus->lastPage()) as $page => $url)
                    @if ($page == $menus->currentPage())
                        <span class="px-3 py-1 bg-primary text-white rounded text-sm font-bold">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="px-3 py-1 border border-[#e6e0db] dark:border-[#3d362e] rounded bg-white dark:bg-[#1a1612] text-[#5c4d40] dark:text-[#a89c92] hover:bg-gray-50 dark:hover:bg-[#2c241b] text-sm">{{ $page }}</a>
                    @endif
                @endforeach
                
                @if ($menus->hasMorePages())
                    <a href="{{ $menus->nextPageUrl() }}" class="px-3 py-1 border border-[#e6e0db] dark:border-[#3d362e] rounded bg-white dark:bg-[#1a1612] text-[#5c4d40] dark:text-[#a89c92] hover:bg-gray-50 dark:hover:bg-[#2c241b] text-sm">Next</a>
                @else
                    <span class="px-3 py-1 border border-[#e6e0db] dark:border-[#3d362e] rounded bg-gray-100 dark:bg-[#2c241b] text-[#897561] opacity-50 text-sm cursor-not-allowed">Next</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Stats Summary (Bottom) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-orange-50 dark:bg-[#1a1612] border border-orange-100 dark:border-[#3d362e] p-4 rounded-xl flex items-center gap-4">
            <div class="bg-orange-100 dark:bg-orange-900/20 p-3 rounded-full text-orange-600">
                <span class="material-symbols-outlined">inventory_2</span>
            </div>
            <div>
                <p class="text-xs font-bold text-[#897561] uppercase tracking-wider">Total Items</p>
                <h4 class="text-xl font-bold text-[#181411] dark:text-white">{{ $menus->total() }}</h4>
            </div>
        </div>
        <div class="bg-green-50 dark:bg-[#1a1612] border border-green-100 dark:border-[#3d362e] p-4 rounded-xl flex items-center gap-4">
            <div class="bg-green-100 dark:bg-green-900/20 p-3 rounded-full text-green-600">
                <span class="material-symbols-outlined">check_circle</span>
            </div>
            <div>
                <p class="text-xs font-bold text-[#897561] uppercase tracking-wider">Active Items</p>
                <h4 class="text-xl font-bold text-[#181411] dark:text-white">{{ $menus->total() }}</h4>
            </div>
        </div>
        <div class="bg-red-50 dark:bg-[#1a1612] border border-red-100 dark:border-[#3d362e] p-4 rounded-xl flex items-center gap-4">
            <div class="bg-red-100 dark:bg-red-900/20 p-3 rounded-full text-red-600">
                <span class="material-symbols-outlined">warning</span>
            </div>
            <div>
                <p class="text-xs font-bold text-[#897561] uppercase tracking-wider">Out of Stock</p>
                <h4 class="text-xl font-bold text-[#181411] dark:text-white">0</h4>
            </div>
        </div>
    </div>

    <!-- Add Menu Modal -->
    <div x-show="showAddModal" x-transition class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showAddModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="showAddModal = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div x-show="showAddModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-[#1a1612] shadow-xl rounded-2xl border border-[#e6e0db] dark:border-[#3d362e]">
                @php
                    $initialAddons = old('addons', []);
                    $initialRecipes = old('recipes', []);
                @endphp
                <form action="{{ route('admin.menus.store') }}" method="POST" enctype="multipart/form-data"
                      x-data="{
                        addons: @json($initialAddons ?: [{name:'',price:''}]),
                        recipes: @json($initialRecipes ?: []),
                        addAddon(){ this.addons.push({name:'',price:''}); },
                        removeAddon(i){ this.addons.splice(i,1); },
                        addRecipe(){ this.recipes.push({ingredient_id:'', quantity_used:''}); },
                        removeRecipe(i){ this.recipes.splice(i,1); }
                      }">
                    @csrf
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-[#181411] dark:text-white">Add New Menu Item</h3>
                        <button type="button" @click="showAddModal = false" class="text-[#897561] hover:text-[#181411] dark:hover:text-white transition-colors">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2">
                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-bold text-[#181411] dark:text-white mb-2">Product Name *</label>
                            <input type="text" name="name" required class="w-full px-4 py-2 bg-gray-50 dark:bg-[#2c241b] border border-[#e6e0db] dark:border-[#3d362e] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 text-[#181411] dark:text-white">
                        </div>

                        <!-- Category -->
                        <div>
                            <label class="block text-sm font-bold text-[#181411] dark:text-white mb-2">Category *</label>
                            <select name="category_id" required class="w-full px-4 py-2 bg-gray-50 dark:bg-[#2c241b] border border-[#e6e0db] dark:border-[#3d362e] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 text-[#181411] dark:text-white">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Price -->
                        <div>
                            <label class="block text-sm font-bold text-[#181411] dark:text-white mb-2">Price (Rp) *</label>
                            <input type="number" name="price" required min="0" step="1000" class="w-full px-4 py-2 bg-gray-50 dark:bg-[#2c241b] border border-[#e6e0db] dark:border-[#3d362e] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 text-[#181411] dark:text-white">
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-bold text-[#181411] dark:text-white mb-2">Description</label>
                            <textarea name="description" rows="3" class="w-full px-4 py-2 bg-gray-50 dark:bg-[#2c241b] border border-[#e6e0db] dark:border-[#3d362e] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 text-[#181411] dark:text-white"></textarea>
                        </div>

                        <!-- Image -->
                        <div>
                            <label class="block text-sm font-bold text-[#181411] dark:text-white mb-2">Product Image</label>
                            <input type="file" name="image" accept="image/*" class="w-full px-4 py-2 bg-gray-50 dark:bg-[#2c241b] border border-[#e6e0db] dark:border-[#3d362e] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 text-[#181411] dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90">
                            <p class="text-xs text-[#897561] mt-1">Max size: 2MB. Supported formats: JPG, PNG, WEBP</p>
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs uppercase tracking-wide text-[#a89c92]">Add-ons</p>
                                    <h3 class="text-lg font-semibold text-[#181411] dark:text-white">Product Add-ons</h3>
                                </div>
                                <button type="button" @click="addAddon()" class="text-primary text-xs font-semibold flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[18px]">add</span>
                                    Add add-on
                                </button>
                            </div>
                            <template x-for="(addon, index) in addons" :key="index">
                                <div class="grid grid-cols-2 gap-3 items-end">
                                    <div>
                                        <label class="text-xs font-medium text-[#897561] dark:text-[#a89c92]">Name</label>
                                        <input type="text" :name="'addons['+index+'][name]'" x-model="addon.name"
                                               class="w-full px-3 py-2 border border-[#e6e0db] dark:border-[#3d362e] rounded-lg text-sm focus:outline-none focus:border-primary" placeholder="Extra shot">
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-[#897561] dark:text-[#a89c92]">Price (Rp)</label>
                                        <input type="number" min="0" step="500" :name="'addons['+index+'][price]'" x-model="addon.price"
                                               class="w-full px-3 py-2 border border-[#e6e0db] dark:border-[#3d362e] rounded-lg text-sm focus:outline-none focus:border-primary" placeholder="0">
                                    </div>
                                    <button type="button" @click="removeAddon(index)"
                                            class="text-red-600 text-xs font-semibold justify-self-start flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                        Remove
                                    </button>
                                </div>
                            </template>
                            <template x-if="addons.length === 0">
                                <p class="text-xs text-[#897561]">No add-ons yet. Define extras to appear on the detail modal.</p>
                            </template>
                            @error('addons.*.name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            @error('addons.*.price')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Recipe / Ingredients -->
                        <div class="space-y-3 pt-2 border-t border-[#e6e0db] dark:border-[#3d362e] mt-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs uppercase tracking-wide text-[#a89c92]">Resep</p>
                                    <h3 class="text-lg font-semibold text-[#181411] dark:text-white">Bahan per porsi</h3>
                                    <p class="text-xs text-[#897561]">Stok bahan otomatis berkurang saat pesanan diproses.</p>
                                </div>
                                <button type="button" @click="addRecipe()" class="text-primary text-xs font-semibold flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[18px]">add</span>
                                    Tambah bahan
                                </button>
                            </div>
                            <template x-for="(recipe, index) in recipes" :key="index">
                                <div class="grid grid-cols-12 gap-3 items-end border border-[#f0e9df] dark:border-[#3d362e] rounded-lg p-3 bg-gray-50/60 dark:bg-[#1f1914]">
                                    <div class="col-span-7">
                                        <label class="text-xs font-medium text-[#897561] dark:text-[#a89c92]">Ingredient</label>
                                        <select :name="'recipes['+index+'][ingredient_id]'" x-model="recipe.ingredient_id"
                                                class="w-full px-3 py-2 border border-[#e6e0db] dark:border-[#3d362e] rounded-lg text-sm focus:outline-none focus:border-primary">
                                            <option value="">Pilih bahan</option>
                                            @foreach($ingredients as $ingredient)
                                                <option value="{{ $ingredient->id }}">{{ $ingredient->name }} ({{ $ingredient->unit }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-span-4">
                                        <label class="text-xs font-medium text-[#897561] dark:text-[#a89c92]">Kuantitas / porsi</label>
                                        <input type="number" min="0.01" step="0.01" :name="'recipes['+index+'][quantity_used]'"
                                               x-model="recipe.quantity_used"
                                               class="w-full px-3 py-2 border border-[#e6e0db] dark:border-[#3d362e] rounded-lg text-sm focus:outline-none focus:border-primary" placeholder="0.00">
                                    </div>
                                    <button type="button" @click="removeRecipe(index)"
                                            class="col-span-1 text-red-600 text-xs font-semibold justify-self-start flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </div>
                            </template>
                            <template x-if="recipes.length === 0">
                                <p class="text-xs text-[#897561]">Belum ada bahan. Tambahkan komposisi agar stok berkurang otomatis.</p>
                            </template>
                            @error('recipes.*.ingredient_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            @error('recipes.*.quantity_used')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex gap-3 mt-6">
                        <button type="button" @click="showAddModal = false" class="flex-1 px-4 py-2 border border-[#e6e0db] dark:border-[#3d362e] rounded-lg text-[#5c4d40] dark:text-[#a89c92] hover:bg-gray-50 dark:hover:bg-[#2c241b] font-medium transition-colors">Cancel</button>
                        <button type="submit" class="flex-1 px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 transition-colors">Save Menu Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
     class="fixed bottom-4 right-4 bg-green-500 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg shadow-lg z-50 text-sm sm:text-base">
    {{ session('success') }}
</div>
@endif
@endsection

