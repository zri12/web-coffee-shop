@extends('layouts.dashboard')

@section('title', isset($category) ? 'Edit Kategori' : 'Tambah Kategori')
@section('header', isset($category) ? 'Edit Kategori' : 'Tambah Kategori')

@section('content')
<div class="max-w-2xl">
    <div class="card">
        <form action="{{ isset($category) ? route('dashboard.categories.update', $category) : route('dashboard.categories.store') }}" method="POST">
            @csrf
            @if(isset($category))
                @method('PUT')
            @endif

            <div class="space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
                    <input type="text" name="name" id="name" class="form-input" value="{{ old('name', $category->name ?? '') }}" required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi (Opsional)</label>
                    <textarea name="description" id="description" rows="3" class="form-input">{{ old('description', $category->description ?? '') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Is Active -->
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="is_active" class="rounded border-gray-300 text-primary focus:ring-primary" value="1" {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }}>
                    <label for="is_active" class="text-sm font-medium text-gray-700">Aktif</label>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('dashboard.categories') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg">Batal</a>
                    <button type="submit" class="btn-primary">
                        {{ isset($category) ? 'Simpan Perubahan' : 'Tambah Kategori' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
