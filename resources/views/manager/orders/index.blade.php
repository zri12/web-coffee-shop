@extends('layouts.dashboard')

@section('title', 'Order Management')

@section('content')
<div class="p-6 space-y-6" x-data="{
    showFilter: false,
    showExport: false,
    status: 'all',
    dateFrom: '',
    dateTo: '',
    applyFilter() {
        let params = new URLSearchParams();
        if (this.status !== 'all') params.append('status', this.status);
        if (this.dateFrom) params.append('date_from', this.dateFrom);
        if (this.dateTo) params.append('date_to', this.dateTo);
        window.location.href = '{{ route('manager.orders') }}?' + params.toString();
    },
    exportData(type) {
        let params = new URLSearchParams();
        params.append('export', type);
        if (this.status !== 'all') params.append('status', this.status);
        if (this.dateFrom) params.append('date_from', this.dateFrom);
        if (this.dateTo) params.append('date_to', this.dateTo);
        
        if (type === 'pdf') {
            // Open PDF in new window for printing
            window.open('{{ route('manager.orders') }}?' + params.toString(), '_blank');
        } else {
            // Download Excel/CSV
            window.location.href = '{{ route('manager.orders') }}?' + params.toString();
        }
        this.showExport = false;
    }
}">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-[#181411] dark:text-white">Order Management</h1>
            <p class="text-[#897561] text-sm">Track and manage customer orders.</p>
        </div>
        <div class="flex gap-2 relative">
            <button @click="showFilter = !showFilter" class="bg-white dark:bg-[#1a1612] border border-[#e6e0db] dark:border-[#3d362e] px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-[#2c241b] flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">filter_list</span> Filter
            </button>
            <div class="relative">
                <button @click="showExport = !showExport" class="bg-white dark:bg-[#1a1612] border border-[#e6e0db] dark:border-[#3d362e] px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-[#2c241b] flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">download</span> Export
                </button>
                <!-- Export Dropdown -->
                <div x-show="showExport" @click.away="showExport = false" x-transition class="absolute right-0 mt-2 w-48 bg-white dark:bg-[#1a1612] border border-[#e6e0db] dark:border-[#3d362e] rounded-lg shadow-lg z-50">
                    <button @click="exportData('pdf')" class="w-full px-4 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-[#2c241b] flex items-center gap-2 border-b border-[#e6e0db] dark:border-[#3d362e]">
                        <span class="material-symbols-outlined text-[18px] text-red-600">picture_as_pdf</span>
                        <span>Export as PDF</span>
                    </button>
                    <button @click="exportData('excel')" class="w-full px-4 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-[#2c241b] flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px] text-green-600">table_chart</span>
                        <span>Export as Excel</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Modal -->
    <div x-show="showFilter" @click.away="showFilter = false" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white dark:bg-[#1a1612] rounded-xl border border-[#e6e0db] dark:border-[#3d362e] p-6 w-full max-w-md mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-[#181411] dark:text-white">Filter Orders</h3>
                <button @click="showFilter = false" class="text-[#897561] hover:text-[#181411] dark:hover:text-white">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-[#5c4d40] dark:text-[#a89c92] mb-2">Status</label>
                    <select x-model="status" class="w-full px-4 py-2 bg-white dark:bg-[#2c241b] border border-[#e6e0db] dark:border-[#3d362e] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                        <option value="all">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#5c4d40] dark:text-[#a89c92] mb-2">Date From</label>
                    <input type="date" x-model="dateFrom" class="w-full px-4 py-2 bg-white dark:bg-[#2c241b] border border-[#e6e0db] dark:border-[#3d362e] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#5c4d40] dark:text-[#a89c92] mb-2">Date To</label>
                    <input type="date" x-model="dateTo" class="w-full px-4 py-2 bg-white dark:bg-[#2c241b] border border-[#e6e0db] dark:border-[#3d362e] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                </div>
                <div class="flex gap-2 pt-2">
                    <button @click="status='all'; dateFrom=''; dateTo=''; applyFilter()" class="flex-1 px-4 py-2 bg-gray-100 dark:bg-[#2c241b] text-[#5c4d40] dark:text-[#a89c92] rounded-lg font-medium hover:bg-gray-200 dark:hover:bg-[#3d362e]">
                        Reset
                    </button>
                    <button @click="applyFilter(); showFilter=false" class="flex-1 px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary/90">
                        Apply Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-[#1a1612] p-4 rounded-xl border border-[#e6e0db] dark:border-[#3d362e] flex items-center gap-4">
            <div class="p-3 bg-blue-50 dark:bg-blue-900/20 text-blue-600 rounded-full">
                <span class="material-symbols-outlined">shopping_bag</span>
            </div>
            <div>
                <p class="text-xs font-bold text-[#897561] uppercase">Total Orders</p>
                <h4 class="text-xl font-bold text-[#181411] dark:text-white">{{ number_format($totalOrders) }}</h4>
            </div>
        </div>
        <div class="bg-white dark:bg-[#1a1612] p-4 rounded-xl border border-[#e6e0db] dark:border-[#3d362e] flex items-center gap-4">
            <div class="p-3 bg-orange-50 dark:bg-orange-900/20 text-orange-600 rounded-full">
                <span class="material-symbols-outlined">pending</span>
            </div>
            <div>
                <p class="text-xs font-bold text-[#897561] uppercase">Pending</p>
                <h4 class="text-xl font-bold text-[#181411] dark:text-white">{{ $pendingOrders }}</h4>
            </div>
        </div>
        <div class="bg-white dark:bg-[#1a1612] p-4 rounded-xl border border-[#e6e0db] dark:border-[#3d362e] flex items-center gap-4">
            <div class="p-3 bg-purple-50 dark:bg-purple-900/20 text-purple-600 rounded-full">
                <span class="material-symbols-outlined">local_shipping</span>
            </div>
            <div>
                <p class="text-xs font-bold text-[#897561] uppercase">Processing</p>
                <h4 class="text-xl font-bold text-[#181411] dark:text-white">{{ $processingOrders }}</h4>
            </div>
        </div>
        <div class="bg-white dark:bg-[#1a1612] p-4 rounded-xl border border-[#e6e0db] dark:border-[#3d362e] flex items-center gap-4">
            <div class="p-3 bg-green-50 dark:bg-green-900/20 text-green-600 rounded-full">
                <span class="material-symbols-outlined">check_circle</span>
            </div>
            <div>
                <p class="text-xs font-bold text-[#897561] uppercase">Completed</p>
                <h4 class="text-xl font-bold text-[#181411] dark:text-white">{{ number_format($completedOrders) }}</h4>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white dark:bg-[#1a1612] rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 dark:bg-[#2c241b] border-b border-[#e6e0db] dark:border-[#3d362e]">
                    <tr>
                        <th class="px-6 py-4 font-bold text-[#897561] uppercase text-xs">Order ID</th>
                        <th class="px-6 py-4 font-bold text-[#897561] uppercase text-xs">Customer</th>
                        <th class="px-6 py-4 font-bold text-[#897561] uppercase text-xs">Table</th>
                        <th class="px-6 py-4 font-bold text-[#897561] uppercase text-xs">Total</th>
                        <th class="px-6 py-4 font-bold text-[#897561] uppercase text-xs">Status</th>
                        <th class="px-6 py-4 font-bold text-[#897561] uppercase text-xs">Date</th>
                        <th class="px-6 py-4 font-bold text-[#897561] uppercase text-xs text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e6e0db] dark:divide-[#3d362e]">
                    @forelse($orders as $order)
                    <tr class="hover:bg-gray-50 dark:hover:bg-[#2c241b]/50">
                        <td class="px-6 py-4 font-bold text-primary">{{ $order->order_number }}</td>
                        <td class="px-6 py-4 text-[#181411] dark:text-white">{{ $order->customer_name }}</td>
                        <td class="px-6 py-4 text-[#5c4d40] dark:text-[#a89c92]">
                            @if($order->table_number && $order->order_type === 'dine_in')
                                Table {{ $order->table_number }}
                            @else
                                Takeaway
                            @endif
                        </td>
                        <td class="px-6 py-4 font-bold text-[#181411] dark:text-white">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded text-xs font-bold uppercase
                                {{ $order->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $order->status === 'pending' ? 'bg-orange-100 text-orange-700' : '' }}
                                {{ $order->status === 'processing' ? 'bg-purple-100 text-purple-700' : '' }}
                                {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-[#897561] text-xs">{{ $order->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('dashboard.orders.show', $order) }}" class="text-[#897561] hover:text-primary font-medium text-xs">View Details</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-[#897561]">
                            No orders found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-[#e6e0db] dark:border-[#3d362e] bg-gray-50/50 dark:bg-[#1a1612] text-center text-xs text-[#897561]">
            Showing {{ $orders->count() }} latest orders
        </div>
    </div>
</div>
@endsection
