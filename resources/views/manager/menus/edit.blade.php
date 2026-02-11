@extends('layouts.dashboard')

@section('title', 'Edit Menu')

@section('content')
<div class="p-6 max-w-3xl mx-auto">
    <div class="bg-white dark:bg-[#1a1612] rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-[#181411] dark:text-white">Edit Menu Item</h1>
                <p class="text-[#897561] text-sm">Update details for {{ $menu->name }}</p>
            </div>
            <a href="{{ route('manager.menus') }}" class="text-[#897561] hover:text-[#181411] dark:hover:text-white transition-colors">
                <span class="material-symbols-outlined">close</span>
            </a>
        </div>

        <form action="{{ route('manager.menus.update', $menu->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <!-- Current Image -->
                @if($menu->image)
                <div>
                    <label class="block text-sm font-bold text-[#181411] dark:text-white mb-2">Current Image</label>
                    <div class="w-32 h-32 rounded-lg overflow-hidden border border-[#e6e0db] dark:border-[#3d362e]">
                        <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="w-full h-full object-cover">
                    </div>
                </div>
                @endif

                <!-- Name -->
                <div>
                    <label class="block text-sm font-bold text-[#181411] dark:text-white mb-2">Product Name *</label>
                    <input type="text" name="name" value="{{ old('name', $menu->name) }}" required class="w-full px-4 py-2 bg-gray-50 dark:bg-[#2c241b] border border-[#e6e0db] dark:border-[#3d362e] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 text-[#181411] dark:text-white">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-sm font-bold text-[#181411] dark:text-white mb-2">Category *</label>
                    <select name="category_id" required class="w-full px-4 py-2 bg-gray-50 dark:bg-[#2c241b] border border-[#e6e0db] dark:border-[#3d362e] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 text-[#181411] dark:text-white">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $menu->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price -->
                <div>
                    <label class="block text-sm font-bold text-[#181411] dark:text-white mb-2">Price (Rp) *</label>
                    <input type="number" name="price" value="{{ old('price', $menu->price) }}" required min="0" step="1000" class="w-full px-4 py-2 bg-gray-50 dark:bg-[#2c241b] border border-[#e6e0db] dark:border-[#3d362e] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 text-[#181411] dark:text-white">
                    @error('price')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-bold text-[#181411] dark:text-white mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2 bg-gray-50 dark:bg-[#2c241b] border border-[#e6e0db] dark:border-[#3d362e] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 text-[#181411] dark:text-white">{{ old('description', $menu->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- New Image -->
                <div>
                    <label class="block text-sm font-bold text-[#181411] dark:text-white mb-2">Update Image (Optional)</label>
                    <input type="file" name="image" accept="image/*" class="w-full px-4 py-2 bg-gray-50 dark:bg-[#2c241b] border border-[#e6e0db] dark:border-[#3d362e] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 text-[#181411] dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90">
                    <p class="text-xs text-[#897561] mt-1">Leave empty to keep current image. Max size: 2MB.</p>
                    @error('image')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <a href="{{ route('manager.menus') }}" class="flex-1 text-center px-4 py-2 border border-[#e6e0db] dark:border-[#3d362e] rounded-lg text-[#5c4d40] dark:text-[#a89c92] hover:bg-gray-50 dark:hover:bg-[#2c241b] font-medium transition-colors">Cancel</a>
                <button type="submit" class="flex-1 px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 transition-colors">Update Menu Item</button>
            </div>
        </form>
    </div>
</div>
@endsection
