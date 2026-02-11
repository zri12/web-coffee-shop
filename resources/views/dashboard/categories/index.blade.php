@extends('layouts.dashboard')

@section('title', 'Kategori')
@section('header', 'Kelola Kategori')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-xl font-bold text-text-main-light dark:text-text-main-dark">Daftar Kategori</h2>
        <p class="text-sm text-text-sec-light dark:text-text-sec-dark mt-1">Atur kategori untuk mengelompokkan menu Anda.</p>
    </div>
    <a href="{{ route('dashboard.categories.create') }}" class="inline-flex items-center justify-center h-10 px-4 rounded-lg bg-primary hover:bg-primary-hover text-white text-sm font-bold transition-colors shadow-lg shadow-primary/30">
        <span class="material-symbols-outlined mr-2 text-[20px]">add</span>
        Tambah Kategori
    </a>
</div>

<div class="bg-white dark:bg-card-dark rounded-xl border border-[#e6e2de] dark:border-[#3e342b] shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-[#faf9f8] dark:bg-[#251f18] border-b border-[#e6e2de] dark:border-[#3e342b]">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-text-sec-light dark:text-text-sec-dark uppercase tracking-wider">Nama</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-text-sec-light dark:text-text-sec-dark uppercase tracking-wider">Slug</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-text-sec-light dark:text-text-sec-dark uppercase tracking-wider">Jumlah Menu</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-text-sec-light dark:text-text-sec-dark uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-text-sec-light dark:text-text-sec-dark uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#e6e2de] dark:divide-[#3e342b]">
                @forelse($categories as $category)
                <tr class="hover:bg-[#faf9f8] dark:hover:bg-[#251f18] transition-colors group">
                    <td class="px-6 py-4 font-bold text-text-main-light dark:text-text-main-dark">{{ $category->name }}</td>
                    <td class="px-6 py-4">
                        <code class="text-xs font-mono bg-[#f4f2f0] dark:bg-[#3e362e] px-2 py-1 rounded text-text-sec-light dark:text-text-sec-dark border border-[#e6e2de] dark:border-[#4a423b]">{{ $category->slug }}</code>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                            {{ $category->menus_count }} item
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold capitalize border {{ $category->is_active ? 'bg-green-100 text-green-700 border-green-200' : 'bg-gray-100 text-gray-700 border-gray-200' }}">
                            <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $category->is_active ? 'bg-green-500' : 'bg-gray-500' }}"></span>
                            {{ $category->is_active ? 'Aktif' : 'Non-Aktif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('dashboard.categories.edit', $category) }}" class="p-2 text-text-sec-light hover:text-primary hover:bg-primary/10 rounded-lg transition-colors" title="Edit">
                                <span class="material-symbols-outlined text-[20px]">edit</span>
                            </a>
                            <form method="POST" action="{{ route('dashboard.categories.destroy', $category) }}" onsubmit="return confirm('Yakin hapus kategori ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-text-sec-light hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors" {{ $category->menus_count > 0 ? 'disabled title="Tidak bisa hapus kategori yang memiliki menu"' : 'title="Hapus"' }} style="{{ $category->menus_count > 0 ? 'opacity: 0.5; cursor: not-allowed;' : '' }}">
                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-text-sec-light dark:text-text-sec-dark">
                        <span class="material-symbols-outlined text-4xl mb-2 opacity-50">category</span>
                        <p>Belum ada kategori yang ditambahkan.</p>
                        <a href="{{ route('dashboard.categories.create') }}" class="text-primary hover:underline font-medium text-sm mt-1 inline-block">Tambah Kategori Sekarang</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
