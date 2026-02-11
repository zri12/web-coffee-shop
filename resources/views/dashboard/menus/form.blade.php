@extends('layouts.dashboard')

@section('title', isset($menu) ? 'Edit Menu' : 'Tambah Menu')
@section('header', isset($menu) ? 'Edit Menu' : 'Tambah Menu')

@section('content')
<div class="max-w-2xl">
    <div class="card p-6">
        <form method="POST" action="{{ isset($menu) ? route('dashboard.menus.update', $menu) : route('dashboard.menus.store') }}" enctype="multipart/form-data">
            @csrf
            @if(isset($menu))
            @method('PUT')
            @endif
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Menu *</label>
                    <input type="text" name="name" value="{{ old('name', $menu->name ?? '') }}" class="input" required>
                    @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori *</label>
                    <select name="category_id" class="input" required>
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $menu->category_id ?? '') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('category_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Harga (Rp) *</label>
                    <input type="number" name="price" value="{{ old('price', $menu->price ?? '') }}" class="input" min="0" required>
                    @error('price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="description" rows="3" class="input">{{ old('description', $menu->description ?? '') }}</textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gambar</label>
                    @if(isset($menu) && $menu->image)
                    <div class="mb-2">
                        <img src="{{ asset('images/menus/' . $menu->image) }}" alt="{{ $menu->name }}" class="w-32 h-32 object-cover rounded-lg">
                    </div>
                    @endif
                    <input type="file" name="image" accept="image/*" class="input">
                    <p class="text-xs text-gray-500 mt-1">Max 2MB. Format: JPG, PNG</p>
                </div>
                
                <div class="flex gap-6">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_available" value="1" class="w-4 h-4 text-coffee-600 rounded" {{ old('is_available', $menu->is_available ?? true) ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">Tersedia</span>
                    </label>
                    
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_featured" value="1" class="w-4 h-4 text-coffee-600 rounded" {{ old('is_featured', $menu->is_featured ?? false) ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">Menu Favorit</span>
                    </label>
                </div>
            </div>
            
            <div class="flex gap-4 mt-8">
                <button type="submit" class="btn-primary">
                    {{ isset($menu) ? 'Simpan Perubahan' : 'Tambah Menu' }}
                </button>
                <a href="{{ route('dashboard.menus') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
