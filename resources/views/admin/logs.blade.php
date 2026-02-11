@extends('layouts.dashboard')

@section('title', 'Logs & Audit')

@section('content')
<div class="p-4 sm:p-6 space-y-4 sm:space-y-6" x-data="{ activeTab: '{{ $tab }}' }">
    <!-- Header -->
    <div class="flex flex-col gap-1">
        <h1 class="text-xl sm:text-2xl font-bold text-[#181411] dark:text-white">Logs & Audit</h1>
        <p class="text-xs sm:text-sm text-[#897561] dark:text-[#a89c92]">Riwayat aktivitas sistem</p>
    </div>

    <!-- Tabs Container -->
    <div class="bg-white dark:bg-[#1a1612] rounded-xl border border-[#e6e0db] dark:border-[#3d362e] overflow-hidden flex flex-col h-[calc(100vh-12rem)] sm:h-auto">
        <!-- Scrollable Tabs Header -->
        <div class="border-b border-[#e6e0db] dark:border-[#3d362e] overflow-x-auto">
            <div class="flex min-w-max">
                <button @click="activeTab = 'transactions'" 
                        :class="activeTab === 'transactions' ? 'border-b-2 border-primary text-primary' : 'text-[#897561] dark:text-[#a89c92]'"
                        class="px-4 sm:px-6 py-3 sm:py-4 font-medium transition-colors hover:text-primary text-sm sm:text-base whitespace-nowrap">
                    Transaksi
                </button>
                <button @click="activeTab = 'login'" 
                        :class="activeTab === 'login' ? 'border-b-2 border-primary text-primary' : 'text-[#897561] dark:text-[#a89c92]'"
                        class="px-4 sm:px-6 py-3 sm:py-4 font-medium transition-colors hover:text-primary text-sm sm:text-base whitespace-nowrap">
                    Login
                </button>
                <button @click="activeTab = 'changes'" 
                        :class="activeTab === 'changes' ? 'border-b-2 border-primary text-primary' : 'text-[#897561] dark:text-[#a89c92]'"
                        class="px-4 sm:px-6 py-3 sm:py-4 font-medium transition-colors hover:text-primary text-sm sm:text-base whitespace-nowrap">
                    Perubahan Data
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="p-4 sm:p-6 border-b border-[#e6e0db] dark:border-[#3d362e]">
            <div class="flex flex-col sm:flex-row flex-wrap gap-3 sm:gap-4">
                <input type="date" 
                       class="w-full sm:w-auto px-4 py-2 rounded-lg border border-[#e6e0db] dark:border-[#3d362e] bg-white dark:bg-[#0f0d0b] text-[#181411] dark:text-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                <input type="date" 
                       class="w-full sm:w-auto px-4 py-2 rounded-lg border border-[#e6e0db] dark:border-[#3d362e] bg-white dark:bg-[#0f0d0b] text-[#181411] dark:text-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                <div class="flex gap-2 w-full sm:w-auto">
                    <button class="flex-1 sm:flex-none px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors text-sm font-medium touch-manipulation">
                        Filter
                    </button>
                    <button class="flex-1 sm:flex-none px-6 py-2 border border-[#e6e0db] dark:border-[#3d362e] text-[#897561] rounded-lg hover:bg-[#f4f2f0] dark:hover:bg-[#3e2d23] transition-colors text-sm font-medium touch-manipulation">
                        Export
                    </button>
                </div>
            </div>
        </div>

        <!-- Content Area with Internal Scroll -->
        <div class="flex-1 overflow-y-auto">
            <!-- Transaction Logs -->
            <div x-show="activeTab === 'transactions'" class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-gray-50 dark:bg-[#2c241b] border-b border-[#e6e0db] dark:border-[#3d362e]">
                            <tr>
                                <th class="px-6 py-4 font-bold text-[#897561] uppercase text-[10px] sm:text-xs">Order ID</th>
                                <th class="px-6 py-4 font-bold text-[#897561] uppercase text-[10px] sm:text-xs">Customer</th>
                                <th class="px-6 py-4 font-bold text-[#897561] uppercase text-[10px] sm:text-xs text-center">Metode</th>
                                <th class="px-6 py-4 font-bold text-[#897561] uppercase text-[10px] sm:text-xs">Total</th>
                                <th class="px-6 py-4 font-bold text-[#897561] uppercase text-[10px] sm:text-xs">Status</th>
                                <th class="px-6 py-4 font-bold text-[#897561] uppercase text-[10px] sm:text-xs">Waktu</th>
                                <th class="px-6 py-4 font-bold text-[#897561] uppercase text-[10px] sm:text-xs text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#e6e0db] dark:divide-[#3d362e]">
                            @forelse($transactionLogs as $log)
                            <tr class="hover:bg-gray-50 dark:hover:bg-[#2c241b]/50 transition-colors">
                                <td class="px-6 py-4 font-bold text-primary">#{{ $log->order_number }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-medium text-[#181411] dark:text-white">{{ $log->customer_name }}</span>
                                        <span class="text-[10px] text-[#897561] dark:text-[#a89c92]">Meja {{ $log->table_number ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-1 bg-[#faf8f6] dark:bg-[#0f0d0b] border border-[#e6e0db] dark:border-[#3d362e] rounded text-[10px] font-bold uppercase text-[#897561]">
                                        {{ $log->payment_method ?? 'CASH' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-bold text-[#181411] dark:text-white">Rp {{ number_format($log->total_amount, 0, ',', '.') }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                        {{ $log->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                                        {{ $log->status === 'pending' ? 'bg-orange-100 text-orange-700' : '' }}
                                        {{ $log->status === 'processing' ? 'bg-purple-100 text-purple-700' : '' }}
                                        {{ $log->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">
                                        {{ $log->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-[#897561] text-xs">{{ $log->created_at->diffForHumans() }}</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.orders.detail', $log) }}" class="p-2 text-[#897561] hover:text-primary hover:bg-primary/10 rounded-lg transition-colors inline-block">
                                        <span class="material-symbols-outlined text-sm">visibility</span>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-[#897561]">
                                    <span class="material-symbols-outlined text-5xl opacity-20 block mb-2">receipt_long</span>
                                    <p class="text-xs">Tidak ada data transaksi ditemukan</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($transactionLogs instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="p-4 border-t border-[#e6e0db] dark:border-[#3d362e]">
                    {{ $transactionLogs->links() }}
                </div>
                @endif
            </div>

            <!-- Login Logs -->
            <div x-show="activeTab === 'login'" class="p-4 sm:p-6" style="display: none;">
                <div class="text-center py-8">
                    <p class="text-[#897561] dark:text-[#a89c92] text-sm">Fitur login logs akan segera diimplementasikan</p>
                </div>
            </div>

            <!-- Change Logs -->
            <div x-show="activeTab === 'changes'" class="p-4 sm:p-6" style="display: none;">
                <div class="text-center py-8">
                    <p class="text-[#897561] dark:text-[#a89c92] text-sm">Fitur change logs akan segera diimplementasikan</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
