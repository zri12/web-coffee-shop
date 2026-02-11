@extends('layouts.dashboard')

@section('title', 'Menu')
@section('header', 'Kelola Menu')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-xl font-bold text-text-main-light dark:text-text-main-dark">Daftar Menu</h2>
        <p class="text-sm text-text-sec-light dark:text-text-sec-dark mt-1">Kelola semua item menu kafe Anda.</p>
    </div>
    <a href="{{ route('dashboard.menus.create') }}" class="inline-flex items-center justify-center h-10 px-4 rounded-lg bg-primary hover:bg-primary-hover text-white text-sm font-bold transition-colors shadow-lg shadow-primary/30">
        <span class="material-symbols-outlined mr-2 text-[20px]">add</span>
        Tambah Menu
    </a>
</div>

<div class="bg-white dark:bg-card-dark rounded-xl border border-[#e6e2de] dark:border-[#3e342b] shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-[#faf9f8] dark:bg-[#251f18] border-b border-[#e6e2de] dark:border-[#3e342b]">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-text-sec-light dark:text-text-sec-dark uppercase tracking-wider">Menu</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-text-sec-light dark:text-text-sec-dark uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-text-sec-light dark:text-text-sec-dark uppercase tracking-wider">Harga</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-text-sec-light dark:text-text-sec-dark uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-text-sec-light dark:text-text-sec-dark uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#e6e2de] dark:divide-[#3e342b]">
                @forelse($menus as $menu)
                <tr class="hover:bg-[#faf9f8] dark:hover:bg-[#251f18] transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center overflow-hidden shrink-0 border border-[#e6e2de] dark:border-[#3e342b]">
                                @if($menu->image)
                                <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="w-full h-full object-cover">
                                @else
                                <span class="text-xl">☕</span>
                                @endif
                            </div>
                            <div>
                                <p class="font-semibold text-text-main-light dark:text-text-main-dark text-base">{{ $menu->name }}</p>
                                @if($menu->is_featured)
                                <span class="inline-flex items-center text-[10px] font-bold text-yellow-600 bg-yellow-100 px-1.5 py-0.5 rounded border border-yellow-200 mt-0.5">
                                    <span class="material-symbols-outlined text-[12px] mr-0.5">star</span> Favorit
                                </span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#f4f2f0] dark:bg-[#3e362e] text-text-sec-light dark:text-text-sec-dark border border-[#e6e2de] dark:border-[#4a423b]">
                            {{ $menu->category->name ?? '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm font-semibold text-text-main-light dark:text-text-main-dark font-mono">{{ number_format($menu->price, 0, ',', '.') }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold capitalize border {{ $menu->is_available ? 'bg-green-100 text-green-700 border-green-200' : 'bg-red-100 text-red-700 border-red-200' }}">
                            <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $menu->is_available ? 'bg-green-500' : 'bg-red-500' }}"></span>
                            {{ $menu->is_available ? 'Tersedia' : 'Habis' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('dashboard.menus.edit', $menu) }}" class="p-2 text-text-sec-light hover:text-primary hover:bg-primary/10 rounded-lg transition-colors" title="Edit">
                                <span class="material-symbols-outlined text-[20px]">edit</span>
                            </a>
                            <form method="POST" action="{{ route('dashboard.menus.destroy', $menu) }}" onsubmit="return confirm('Yakin hapus menu ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-text-sec-light hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-text-sec-light dark:text-text-sec-dark">
                        <span class="material-symbols-outlined text-4xl mb-2 opacity-50">restaurant_menu</span>
                        <p>Belum ada menu yang ditambahkan.</p>
                        <a href="{{ route('dashboard.menus.create') }}" class="text-primary hover:underline font-medium text-sm mt-1 inline-block">Tambah Menu Sekarang</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($menus->hasPages())
    <div class="p-4 border-t border-[#e6e2de] dark:border-[#3e342b] bg-[#faf9f8] dark:bg-[#251f18]">
        {{ $menus->links() }}
    </div>
    @endif
</div>
@endsection
