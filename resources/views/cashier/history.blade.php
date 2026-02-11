@extends('layouts.dashboard')

@section('title', 'Order History')

@section('content')
<div class="p-6 space-y-6" x-data="orderHistory()">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#181411] dark:text-white">Order History</h1>
            <p class="text-sm text-[#897561] dark:text-[#a89c92] mt-1">Riwayat transaksi dengan filter tanggal</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-[#2d2115] rounded-xl border border-[#f4f2f0] dark:border-[#3e2d23] p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Date From -->
            <div>
                <label class="block text-sm font-medium text-[#181411] dark:text-white mb-2">From Date</label>
                <input 
                    type="date" 
                    x-model="dateFrom"
                    @change="applyFilters()"
                    class="w-full px-4 py-2 bg-[#f4f2f0] dark:bg-[#221910] border border-transparent focus:border-primary rounded-lg text-sm text-[#181411] dark:text-white">
            </div>

            <!-- Date To -->
            <div>
                <label class="block text-sm font-medium text-[#181411] dark:text-white mb-2">To Date</label>
                <input 
                    type="date" 
                    x-model="dateTo"
                    @change="applyFilters()"
                    class="w-full px-4 py-2 bg-[#f4f2f0] dark:bg-[#221910] border border-transparent focus:border-primary rounded-lg text-sm text-[#181411] dark:text-white">
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-[#181411] dark:text-white mb-2">Status</label>
                <select 
                    x-model="statusFilter"
                    @change="applyFilters()"
                    class="w-full px-4 py-2 bg-[#f4f2f0] dark:bg-[#221910] border border-transparent focus:border-primary rounded-lg text-sm text-[#181411] dark:text-white">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            <!-- Reset Button -->
            <div class="flex items-end">
                <button @click="resetFilters()" class="w-full px-4 py-2 bg-[#f4f2f0] dark:bg-[#221910] text-[#181411] dark:text-white rounded-lg hover:bg-[#e8e4df] dark:hover:bg-[#2c241b] transition-colors text-sm font-medium">
                    Reset Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-[#2d2115] rounded-xl p-4 border border-[#f4f2f0] dark:border-[#3e2d23]">
            <div class="flex items-center gap-3">
                <div class="size-12 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">receipt_long</span>
                </div>
                <div>
                    <p class="text-sm text-[#897561] dark:text-[#a89c92]">Total Orders</p>
                    <p class="text-xl font-bold text-[#181411] dark:text-white">{{ $orders->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-[#2d2115] rounded-xl p-4 border border-[#f4f2f0] dark:border-[#3e2d23]">
            <div class="flex items-center gap-3">
                <div class="size-12 rounded-lg bg-green-50 dark:bg-green-900/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-green-600 dark:text-green-400">check_circle</span>
                </div>
                <div>
                    <p class="text-sm text-[#897561] dark:text-[#a89c92]">Completed</p>
                    <p class="text-xl font-bold text-[#181411] dark:text-white">{{ $orders->where('status', 'completed')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-[#2d2115] rounded-xl p-4 border border-[#f4f2f0] dark:border-[#3e2d23]">
            <div class="flex items-center gap-3">
                <div class="size-12 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-yellow-600 dark:text-yellow-400">pending</span>
                </div>
                <div>
                    <p class="text-sm text-[#897561] dark:text-[#a89c92]">Pending</p>
                    <p class="text-xl font-bold text-[#181411] dark:text-white">{{ $orders->where('status', 'pending')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-[#2d2115] rounded-xl p-4 border border-[#f4f2f0] dark:border-[#3e2d23]">
            <div class="flex items-center gap-3">
                <div class="size-12 rounded-lg bg-primary/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary">payments</span>
                </div>
                <div>
                    <p class="text-sm text-[#897561] dark:text-[#a89c92]">Total Revenue</p>
                    <p class="text-xl font-bold text-[#181411] dark:text-white">Rp {{ number_format($orders->sum('total_amount'), 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white dark:bg-[#2d2115] rounded-xl border border-[#f4f2f0] dark:border-[#3e2d23] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-[#fdfbf7] dark:bg-[#221910] border-b border-[#f4f2f0] dark:border-[#3e2d23]">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-[#897561] dark:text-[#a89c92] uppercase">Order</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-[#897561] dark:text-[#a89c92] uppercase">Table</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-[#897561] dark:text-[#a89c92] uppercase">Items</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-[#897561] dark:text-[#a89c92] uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-[#897561] dark:text-[#a89c92] uppercase">Payment</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-[#897561] dark:text-[#a89c92] uppercase">Total</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-[#897561] dark:text-[#a89c92] uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-[#897561] dark:text-[#a89c92] uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#f4f2f0] dark:divide-[#3e2d23]">
                    @forelse($orders as $order)
                    <tr class="hover:bg-[#fdfbf7] dark:hover:bg-[#221910] transition-colors">
                        <td class="px-4 py-3">
                            <p class="font-bold text-[#181411] dark:text-white">{{ $order->order_number }}</p>
                            <p class="text-xs text-[#897561] dark:text-[#a89c92]">{{ $order->created_at->format('H:i') }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm text-[#181411] dark:text-white">Table {{ $order->table_number }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm text-[#181411] dark:text-white">{{ $order->items->count() }} items</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                @if($order->status === 'pending') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400
                                @elseif($order->status === 'processing') bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400
                                @elseif($order->status === 'completed') bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400
                                @else bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400
                                @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400">
                                {{ strtoupper($order->payment_method) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-bold text-primary">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm text-[#181411] dark:text-white">{{ $order->created_at->format('d M Y') }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <button @click="viewDetails({{ $order->id }})" class="text-primary hover:text-primary-dark transition-colors">
                                <span class="material-symbols-outlined text-[20px]">visibility</span>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center">
                            <div class="size-16 mx-auto rounded-full bg-[#f4f2f0] dark:bg-[#221910] flex items-center justify-center mb-4">
                                <span class="material-symbols-outlined text-[#897561] dark:text-[#a89c92] text-[32px]">receipt_long</span>
                            </div>
                            <h3 class="text-lg font-medium text-[#181411] dark:text-white mb-2">No Orders Found</h3>
                            <p class="text-sm text-[#897561] dark:text-[#a89c92]">Try adjusting your filters</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="flex justify-center">
        {{ $orders->links() }}
    </div>

    <!-- Order Detail Modal -->
    <div x-show="showDetailModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" x-cloak>
        <div class="bg-white dark:bg-[#2d2115] rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto" @click.away="showDetailModal = false">
            <div class="p-6 border-b border-[#f4f2f0] dark:border-[#3e2d23] flex items-center justify-between">
                <h3 class="text-lg font-bold text-[#181411] dark:text-white">Order Details</h3>
                <button @click="showDetailModal = false" class="text-[#897561] dark:text-[#a89c92] hover:text-[#181411] dark:hover:text-white">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="p-6">
                <p class="text-sm text-[#897561] dark:text-[#a89c92]">Order detail view will be implemented here</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function orderHistory() {
    return {
        dateFrom: '',
        dateTo: '',
        statusFilter: '',
        showDetailModal: false,

        applyFilters() {
            const params = new URLSearchParams();
            if (this.dateFrom) params.append('date_from', this.dateFrom);
            if (this.dateTo) params.append('date_to', this.dateTo);
            if (this.statusFilter) params.append('status', this.statusFilter);
            
            window.location.href = '/cashier/history?' + params.toString();
        },

        resetFilters() {
            this.dateFrom = '';
            this.dateTo = '';
            this.statusFilter = '';
            window.location.href = '/cashier/history';
        },

        viewDetails(orderId) {
            this.showDetailModal = true;
            // Fetch order details via AJAX if needed
        }
    }
}
</script>
@endpush
@endsection
