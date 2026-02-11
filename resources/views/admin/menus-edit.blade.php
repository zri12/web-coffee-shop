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
