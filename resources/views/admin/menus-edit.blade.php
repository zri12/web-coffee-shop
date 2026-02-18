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
        @php
            $initialRecipes = old('recipes') ?? ($menuRecipes ?? collect())->map(function($r){
                return [
                    'ingredient_id' => $r->ingredient_id,
                    'quantity_used' => (float) $r->quantity_used,
                ];
            })->toArray();
            $initialAddons = old('addons', $menu->addons ?? [['name'=>'','price'=>'']]);
        @endphp
        <form id="editMenuForm" action="{{ route('admin.menus.update', $menu->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
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
                @if($menu->display_image_url)
                <div class="mb-4">
                    <p class="text-xs font-bold text-[#897561] uppercase tracking-wider mb-2">Current Image:</p>
                    <img src="{{ $menu->display_image_url }}" alt="{{ $menu->name }}" class="w-32 h-32 object-cover rounded-lg border border-[#e6e0db] dark:border-[#3d362e]">
                </div>
                @endif
                <input type="file" name="image" accept="image/*"
                       class="w-full px-4 py-3 rounded-lg border border-[#e6e0db] dark:border-[#3d362e] bg-white dark:bg-[#0f0d0b] text-[#181411] dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent text-base">
                <p class="text-xs text-[#897561] mt-2">Leave empty to keep current image. Max size: 2MB. Supported formats: JPG, PNG, WEBP</p>
                @error('image')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Addons -->
            <div class="space-y-3" id="edit-menu-addons">
                <div class="flex items-center justify-between pt-2">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-[#a89c92]">Add-ons</p>
                        <h3 class="text-lg font-semibold text-[#181411] dark:text-white">Product add-ons</h3>
                    </div>
                    <button type="button" id="edit-menu-add-addon" class="text-primary text-xs font-semibold flex items-center gap-1">
                        <span class="material-symbols-outlined text-[18px]">add</span>
                        Add add-on
                    </button>
                </div>
                <div id="edit-menu-addon-container" class="space-y-2">
                    @foreach($initialAddons as $i => $addon)
                    <div class="grid grid-cols-2 gap-3 items-end addon-row">
                        <div>
                            <label class="text-xs font-medium text-[#897561] dark:text-[#a89c92]">Name</label>
                            <input type="text" name="addons[{{ $i }}][name]" value="{{ $addon['name'] ?? '' }}"
                                   class="w-full px-3 py-2 border border-[#e6e0db] dark:border-[#3d362e] rounded-lg text-sm focus:outline-none focus:border-primary" placeholder="Extra shot">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-[#897561] dark:text-[#a89c92]">Price (Rp)</label>
                            <input type="number" min="0" step="500" name="addons[{{ $i }}][price]" value="{{ $addon['price'] ?? '' }}"
                                   class="w-full px-3 py-2 border border-[#e6e0db] dark:border-[#3d362e] rounded-lg text-sm focus:outline-none focus:border-primary" placeholder="0">
                        </div>
                        <button type="button" class="text-red-600 text-xs font-semibold justify-self-start flex items-center gap-1 remove-addon-btn">
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                            Remove
                        </button>
                    </div>
                    @endforeach
                </div>
                @error('addons.*.name')
                    <p class="text-red-500 text-sm block">{{ $message }}</p>
                @enderror
                @error('addons.*.price')
                    <p class="text-red-500 text-sm block">{{ $message }}</p>
                @enderror
            </div>

            <!-- Recipe / Ingredients -->
            <div class="space-y-3 pt-2 border-t border-[#e6e0db] dark:border-[#3d362e]" id="edit-menu-recipes">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-[#a89c92]">Resep</p>
                        <h3 class="text-lg font-semibold text-[#181411] dark:text-white">Bahan per porsi</h3>
                        <p class="text-xs text-[#897561]">Stok bahan otomatis berkurang saat pesanan diproses.</p>
                    </div>
                    <button type="button" id="edit-menu-add-recipe" class="text-primary text-xs font-semibold flex items-center gap-1">
                        <span class="material-symbols-outlined text-[18px]">add</span>
                        Tambah bahan
                    </button>
                </div>
                <div id="edit-menu-recipe-container" class="space-y-2">
                    @foreach($initialRecipes as $i => $recipe)
                    <div class="grid grid-cols-12 gap-3 items-end border border-[#f0e9df] dark:border-[#3d362e] rounded-lg p-3 bg-gray-50/60 dark:bg-[#1f1914] recipe-row">
                        <div class="col-span-7">
                            <label class="text-xs font-medium text-[#897561] dark:text-[#a89c92]">Ingredient</label>
                            <select name="recipes[{{ $i }}][ingredient_id]" class="w-full px-3 py-2 border border-[#e6e0db] dark:border-[#3d362e] rounded-lg text-sm focus:outline-none focus:border-primary">
                                <option value="">Pilih bahan</option>
                                @foreach($ingredients as $ingredient)
                                    <option value="{{ $ingredient->id }}" {{ ($recipe['ingredient_id'] ?? null)==$ingredient->id ? 'selected':'' }}>{{ $ingredient->name }} ({{ $ingredient->unit }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-4">
                            <label class="text-xs font-medium text-[#897561] dark:text-[#a89c92]">Kuantitas / porsi</label>
                            <input type="number" min="0.01" step="0.01" name="recipes[{{ $i }}][quantity_used]" value="{{ $recipe['quantity_used'] ?? '' }}"
                                   class="w-full px-3 py-2 border border-[#e6e0db] dark:border-[#3d362e] rounded-lg text-sm focus:outline-none focus:border-primary" placeholder="0.00">
                        </div>
                        <button type="button" class="col-span-1 text-red-600 text-xs font-semibold justify-self-start flex items-center gap-1 remove-recipe-btn">
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                            Remove
                        </button>
                    </div>
                    @endforeach
                </div>
                @if(empty($initialRecipes))
                    <p class="text-xs text-[#897561]">Belum ada bahan. Tambahkan komposisi agar stok berkurang otomatis.</p>
                @endif
                @error('recipes.*.ingredient_id')
                    <span class="text-red-500 text-sm block">{{ $message }}</span>
                @enderror
                @error('recipes.*.quantity_used')
                    <span class="text-red-500 text-sm block">{{ $message }}</span>
                @enderror
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

@push('scripts')
<div class="hidden" data-ingredients-options>
    @foreach($ingredients as $ingredient)
        <option value="{{ $ingredient->id }}">{{ $ingredient->name }} ({{ $ingredient->unit }})</option>
    @endforeach
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const ingredientOptions = document.querySelector('[data-ingredients-options]')?.innerHTML || '';

    function bindAddons(containerSelector, addBtnSelector) {
        const container = document.querySelector(containerSelector);
        const addBtn = document.querySelector(addBtnSelector);
        if (!container || !addBtn) return;
        const tpl = () => {
            const i = container.querySelectorAll('.addon-row').length;
            return `<div class="grid grid-cols-2 gap-3 items-end addon-row">
                <div>
                    <label class="text-xs font-medium text-[#897561] dark:text-[#a89c92]">Name</label>
                    <input type="text" name="addons[${i}][name]" class="w-full px-3 py-2 border border-[#e6e0db] dark:border-[#3d362e] rounded-lg text-sm focus:outline-none focus:border-primary" placeholder="Extra shot">
                </div>
                <div>
                    <label class="text-xs font-medium text-[#897561] dark:text-[#a89c92]">Price (Rp)</label>
                    <input type="number" min="0" step="500" name="addons[${i}][price]" class="w-full px-3 py-2 border border-[#e6e0db] dark:border-[#3d362e] rounded-lg text-sm focus:outline-none focus:border-primary" placeholder="0">
                </div>
                <button type="button" class="text-red-600 text-xs font-semibold justify-self-start flex items-center gap-1 remove-addon-btn">
                    <span class="material-symbols-outlined text-[18px]">delete</span>
                    Remove
                </button>
            </div>`;
        };
        addBtn.addEventListener('click', (e) => { e.preventDefault(); container.insertAdjacentHTML('beforeend', tpl()); });
        container.addEventListener('click', (e) => {
            const btn = e.target.closest('.remove-addon-btn');
            if (btn) { e.preventDefault(); btn.closest('.addon-row')?.remove(); }
        });
    }

    function bindRecipes(containerSelector, addBtnSelector) {
        const container = document.querySelector(containerSelector);
        const addBtn = document.querySelector(addBtnSelector);
        if (!container || !addBtn) return;
        const tpl = () => {
            const i = container.querySelectorAll('.recipe-row').length;
            return `<div class="grid grid-cols-12 gap-3 items-end border border-[#f0e9df] dark:border-[#3d362e] rounded-lg p-3 bg-gray-50/60 dark:bg-[#1f1914] recipe-row">
                <div class="col-span-7">
                    <label class="text-xs font-medium text-[#897561] dark:text-[#a89c92]">Ingredient</label>
                    <select name="recipes[${i}][ingredient_id]" class="w-full px-3 py-2 border border-[#e6e0db] dark:border-[#3d362e] rounded-lg text-sm focus:outline-none focus:border-primary">
                        <option value=\"\">Pilih bahan</option>${ingredientOptions}
                    </select>
                </div>
                <div class="col-span-4">
                    <label class="text-xs font-medium text-[#897561] dark:text-[#a89c92]">Kuantitas / porsi</label>
                    <input type="number" min="0.01" step="0.01" name="recipes[${i}][quantity_used]" class="w-full px-3 py-2 border border-[#e6e0db] dark:border-[#3d362e] rounded-lg text-sm focus:outline-none focus:border-primary" placeholder="0.00">
                </div>
                <button type="button" class="col-span-1 text-red-600 text-xs font-semibold justify-self-start flex items-center gap-1 remove-recipe-btn">
                    <span class="material-symbols-outlined text-[18px]">delete</span>
                </button>
            </div>`;
        };
        addBtn.addEventListener('click', (e) => { e.preventDefault(); container.insertAdjacentHTML('beforeend', tpl()); });
        container.addEventListener('click', (e) => {
            const btn = e.target.closest('.remove-recipe-btn');
            if (btn) { e.preventDefault(); btn.closest('.recipe-row')?.remove(); }
        });
    }

    bindAddons('#edit-menu-addon-container', '#edit-menu-add-addon');
    bindRecipes('#edit-menu-recipe-container', '#edit-menu-add-recipe');
});
</script>
@endpush
