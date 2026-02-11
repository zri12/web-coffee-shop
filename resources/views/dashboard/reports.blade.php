@extends('layouts.dashboard')

@section('title', 'Laporan')
@section('header', 'Laporan Penjualan')

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-bold text-text-main-light dark:text-text-main-dark">Ringkasan Penjualan</h2>
    <p class="text-sm text-text-sec-light dark:text-text-sec-dark mt-1">Analisis performa penjualan dan menu terlaris.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Sales Chart/Table -->
    <div class="lg:col-span-2 bg-white dark:bg-card-dark rounded-xl border border-[#e6e2de] dark:border-[#3e342b] shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-text-main-light dark:text-text-main-dark">Penjualan 7 Hari Terakhir</h3>
            <span class="p-2 bg-primary/10 text-primary rounded-lg">
                <span class="material-symbols-outlined">show_chart</span>
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-[#faf9f8] dark:bg-[#251f18] rounded-lg">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-text-sec-light dark:text-text-sec-dark uppercase tracking-wider rounded-l-lg">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-text-sec-light dark:text-text-sec-dark uppercase tracking-wider">Total Order</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-text-sec-light dark:text-text-sec-dark uppercase tracking-wider rounded-r-lg">Pendapatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e6e2de] dark:divide-[#3e342b]">
                    @forelse($salesByDate as $sale)
                    <tr class="hover:bg-[#faf9f8] dark:hover:bg-[#251f18] transition-colors">
                        <td class="px-4 py-3 text-sm font-medium text-text-main-light dark:text-text-main-dark">{{ \Carbon\Carbon::parse($sale->date)->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-sm text-text-sec-light dark:text-text-sec-dark">{{ $sale->count }} Transaksi</td>
                        <td class="px-4 py-3 text-right text-sm font-bold text-primary font-mono">{{ 'Rp ' . number_format($sale->total, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-4 py-12 text-center text-text-sec-light dark:text-text-sec-dark">
                            <span class="material-symbols-outlined text-4xl mb-2 opacity-50">calendar_today</span>
                            <p>Tidak ada data penjualan dalam 7 hari terakhir.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Orders By Type -->
    <div class="space-y-6">
        <h3 class="text-lg font-bold text-text-main-light dark:text-text-main-dark">Metode Pemesanan</h3>
        
        @foreach($ordersByType as $type => $count)
        <div class="bg-white dark:bg-card-dark rounded-xl border border-[#e6e2de] dark:border-[#3e342b] shadow-sm p-6 relative overflow-hidden group hover:border-primary transition-colors">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <span class="material-symbols-outlined text-6xl text-primary">{{ $type == 'qr' ? 'qr_code_scanner' : 'point_of_sale' }}</span>
            </div>
            
            <div class="flex items-center gap-4 mb-2">
                <div class="w-12 h-12 rounded-full {{ $type == 'qr' ? 'bg-blue-100 text-blue-600' : 'bg-orange-100 text-orange-600' }} flex items-center justify-center shadow-sm">
                    <span class="material-symbols-outlined text-2xl">{{ $type == 'qr' ? 'qr_code' : 'person' }}</span>
                </div>
                <div>
                    <p class="font-bold text-text-main-light dark:text-text-main-dark capitalize text-lg">{{ $type == 'qr' ? 'QR Code' : 'Manual (Kasir)' }}</p>
                    <p class="text-xs text-text-sec-light dark:text-text-sec-dark font-medium">Total Transaksi</p>
                </div>
            </div>
            
            <div class="mt-4">
                <span class="text-3xl font-bold text-text-main-light dark:text-text-main-dark">{{ $count }}</span>
                <span class="text-sm text-text-sec-light dark:text-text-sec-dark ml-1">Orders</span>
            </div>
        </div>
        @endforeach
        
        @if(count($ordersByType) == 0)
        <div class="bg-white dark:bg-card-dark rounded-xl border border-dashed border-[#e6e2de] dark:border-[#3e342b] p-6 text-center text-text-sec-light dark:text-text-sec-dark">
            <p>Belum ada data transaksi.</p>
        </div>
        @endif
    </div>
</div>

<!-- Top Menus -->
<div class="bg-white dark:bg-card-dark rounded-xl border border-[#e6e2de] dark:border-[#3e342b] shadow-sm overflow-hidden">
    <div class="p-6 border-b border-[#e6e2de] dark:border-[#3e342b] flex items-center gap-2">
        <span class="material-symbols-outlined text-primary">trophy</span>
        <h3 class="text-lg font-bold text-text-main-light dark:text-text-main-dark">Menu Terlaris</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-[#faf9f8] dark:bg-[#251f18]">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-text-sec-light dark:text-text-sec-dark uppercase tracking-wider w-16">#</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-text-sec-light dark:text-text-sec-dark uppercase tracking-wider">Nama Menu</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-text-sec-light dark:text-text-sec-dark uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-text-sec-light dark:text-text-sec-dark uppercase tracking-wider">Terjual</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#e6e2de] dark:divide-[#3e342b]">
                @forelse($topMenus as $index => $menu)
                <tr class="hover:bg-[#faf9f8] dark:hover:bg-[#251f18] transition-colors">
                    <td class="px-6 py-4">
                        @if($index == 0)
                            <span class="material-symbols-outlined text-yellow-500">emoji_events</span>
                        @elseif($index == 1)
                            <span class="material-symbols-outlined text-gray-400">emoji_events</span>
                        @elseif($index == 2)
                            <span class="material-symbols-outlined text-orange-700">emoji_events</span>
                        @else
                            <span class="text-sm font-bold text-text-sec-light dark:text-text-sec-dark w-6 text-center inline-block">{{ $index + 1 }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center overflow-hidden shrink-0 border border-[#e6e2de] dark:border-[#3e342b]">
                                @if($menu->image)
                                <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="w-full h-full object-cover">
                                @else
                                <span class="text-lg">☕</span>
                                @endif
                            </div>
                            <span class="font-bold text-text-main-light dark:text-text-main-dark">{{ $menu->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-text-sec-light dark:text-text-sec-dark">{{ $menu->category_name }}</td>
                    <td class="px-6 py-4 text-right">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-primary/10 text-primary">
                            {{ $menu->total_sold }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-text-sec-light dark:text-text-sec-dark">
                        <span class="material-symbols-outlined text-4xl mb-2 opacity-50">bar_chart</span>
                        <p>Belum ada data penjualan.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
