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
    <div x-show="showDetailModal" x-cloak class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="relative bg-white dark:bg-[#1a1612] rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto border border-[#f4f2f0] dark:border-[#3e2d23]" @click.away="closeModal()" x-transition>
            <!-- Header -->
            <div class="p-6 border-b border-[#f4f2f0] dark:border-[#3e2d23] flex items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="material-symbols-outlined text-primary">receipt_long</span>
                        <h3 class="text-lg font-bold text-[#181411] dark:text-white" x-text="detail.order_number || 'Order Details'"></h3>
                    </div>
                    <p class="text-xs text-[#897561] dark:text-[#a89c92]" x-text="detail.created_at || ''"></p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold" :class="statusBadge(detail.status)">
                        <span class="capitalize" x-text="detail.status || 'pending'"></span>
                    </span>
                    <button @click="closeModal()" class="p-2 rounded-full hover:bg-[#f4f2f0] dark:hover:bg-[#2c241b] text-[#897561] dark:text-[#a89c92]">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-6">
                <!-- Loading State -->
                <template x-if="loading">
                    <div class="flex items-center gap-3 text-[#897561] dark:text-[#a89c92]">
                        <span class="material-symbols-outlined animate-spin">progress_activity</span>
                        <span>Loading order details...</span>
                    </div>
                </template>

                <!-- Error State -->
                <template x-if="errorMessage">
                    <div class="p-3 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 text-sm" x-text="errorMessage"></div>
                </template>

                <template x-if="!loading && !errorMessage">
                    <div class="space-y-6">
                        <!-- Customer & Order Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-[#fdfbf7] dark:bg-[#221910] rounded-xl p-4 border border-[#f4f2f0] dark:border-[#3e2d23]">
                            <div class="space-y-2">
                                <div class="flex items-center gap-2 text-sm text-[#897561] dark:text-[#a89c92]">
                                    <span class="material-symbols-outlined text-[#d47311]">person</span>
                                    <span class="font-semibold text-[#181411] dark:text-white" x-text="detail.customer_name || 'Guest'"></span>
                                </div>
                                <div class="flex items-center gap-2 text-sm text-[#897561] dark:text-[#a89c92]">
                                    <span class="material-symbols-outlined text-[#d47311]">restaurant</span>
                                    <span>
                                        <span class="font-semibold text-[#181411] dark:text-white" x-text="orderTypeLabel(detail.order_type)"></span>
                                        <template x-if="detail.table_number">
                                            <span class="ml-1 text-xs text-[#897561] dark:text-[#a89c92]">(Table <span x-text="detail.table_number"></span>)</span>
                                        </template>
                                    </span>
                                </div>
                                <div class="flex items-center gap-2 text-sm text-[#897561] dark:text-[#a89c92]">
                                    <span class="material-symbols-outlined text-[#d47311]">badge</span>
                                    <span class="font-semibold text-[#181411] dark:text-white" x-text="detail.cashier_name || 'Cashier'"></span>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex items-center gap-2 text-sm text-[#897561] dark:text-[#a89c92]">
                                    <span class="material-symbols-outlined text-[#d47311]">payments</span>
                                    <span class="font-semibold text-[#181411] dark:text-white" x-text="paymentLabel(detail.payment_method)"></span>
                                    <span class="px-2 py-1 rounded-full text-[11px] font-semibold" :class="paymentBadge(detail.payment_status)" x-text="paymentStatusLabel(detail.payment_status)"></span>
                                </div>
                                <div class="flex items-center gap-2 text-sm text-[#897561] dark:text-[#a89c92]">
                                    <span class="material-symbols-outlined text-[#d47311]">event</span>
                                    <span x-text="detail.created_at || '-'" class="font-semibold text-[#181411] dark:text-white"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Items -->
                        <div class="space-y-3">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[#d47311]">list_alt</span>
                                <h4 class="text-sm font-semibold text-[#181411] dark:text-white">Order Items</h4>
                            </div>
                            <div class="border border-[#f4f2f0] dark:border-[#3e2d23] rounded-xl divide-y divide-[#f4f2f0] dark:divide-[#3e2d23] bg-white dark:bg-[#2d2115]" x-show="detail.items && detail.items.length" x-transition>
                                <template x-for="(item, idx) in detail.items" :key="idx">
                                    <div class="p-4 flex items-start justify-between gap-3">
                                        <div>
                                            <p class="font-semibold text-[#181411] dark:text-white" x-text="item.menu_name"></p>
                                            <p class="text-xs text-[#897561] dark:text-[#a89c92]" x-text="item.notes || formatOptions(item.options)"></p>
                                        </div>
                                        <div class="text-right min-w-[120px]">
                                            <p class="text-sm text-[#181411] dark:text-white" x-text="'Qty: ' + item.quantity"></p>
                                            <p class="text-sm text-[#897561] dark:text-[#a89c92]" x-text="'Rp ' + formatNumber(item.unit_price)"></p>
                                            <p class="font-semibold text-primary" x-text="'Rp ' + formatNumber(item.subtotal)"></p>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!detail.items || detail.items.length === 0">
                                    <div class="p-4 text-center text-sm text-[#897561] dark:text-[#a89c92]">No items</div>
                                </template>
                            </div>
                        </div>

                        <!-- Payment Summary -->
                        <div class="bg-[#fdfbf7] dark:bg-[#221910] rounded-xl p-4 border border-[#f4f2f0] dark:border-[#3e2d23] space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[#d47311]">credit_card</span>
                                <h4 class="text-sm font-semibold text-[#181411] dark:text-white">Payment Info</h4>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-sm text-[#181411] dark:text-white">
                                <div class="flex justify-between text-[#897561] dark:text-[#a89c92]"><span>Subtotal</span><span x-text="'Rp ' + formatNumber(detail.subtotal)"></span></div>
                                <div class="flex justify-between text-[#897561] dark:text-[#a89c92]"><span>Tax (5%)</span><span x-text="'Rp ' + formatNumber(detail.tax_amount)"></span></div>
                                <div class="flex justify-between font-semibold text-[#181411] dark:text-white col-span-2 border-t border-[#f4f2f0] dark:border-[#3e2d23] pt-2"><span>Total</span><span x-text="'Rp ' + formatNumber(detail.total_amount)"></span></div>
                            </div>
                        </div>

                        <!-- Timeline (optional) -->
                        <div class="space-y-3" x-show="detail.timeline && detail.timeline.length">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[#d47311]">schedule</span>
                                <h4 class="text-sm font-semibold text-[#181411] dark:text-white">Order Timeline</h4>
                            </div>
                            <div class="border border-[#f4f2f0] dark:border-[#3e2d23] rounded-xl divide-y divide-[#f4f2f0] dark:divide-[#3e2d23] bg-white dark:bg-[#2d2115]">
                                <template x-for="(step, idx) in detail.timeline" :key="idx">
                                    <div class="p-3 flex items-center gap-3">
                                        <span class="material-symbols-outlined text-[#d47311]">check_circle</span>
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-[#181411] dark:text-white" x-text="step.label"></p>
                                            <p class="text-xs text-[#897561] dark:text-[#a89c92]" x-text="step.time"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Footer -->
            <div class="p-4 border-t border-[#f4f2f0] dark:border-[#3e2d23] bg-[#fdfbf7] dark:bg-[#221910] flex items-center justify-end gap-3">
                <template x-if="detail.payment_status === 'paid'">
                    <a :href="printUrl(detail.id)" target="_blank" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-semibold hover:bg-[#b95f0d] flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">print</span>
                        Print Receipt
                    </a>
                </template>
                <button @click="closeModal()" class="px-4 py-2 bg-[#f4f2f0] dark:bg-[#2c241b] text-[#181411] dark:text-white rounded-lg text-sm font-semibold hover:bg-[#e8e4df] dark:hover:bg-[#3a2f25]">Tutup</button>
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
        loading: false,
        errorMessage: '',
        detail: {
            items: [],
            timeline: []
        },

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
            this.loading = true;
            this.errorMessage = '';
            this.detail = { items: [], timeline: [] };

            fetch(`/cashier/history/${orderId}/details`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.detail = {
                            ...data.order,
                            items: data.order.items || [],
                            timeline: data.order.timeline || []
                        };
                    } else {
                        this.errorMessage = data.message || 'Order not found';
                    }
                })
                .catch(() => {
                    this.errorMessage = 'Gagal memuat order';
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        closeModal() {
            this.showDetailModal = false;
        },

        statusBadge(status) {
            switch(status) {
                case 'processing': return 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400';
                case 'completed': return 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400';
                case 'cancelled': return 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400';
                default: return 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400';
            }
        },

        paymentBadge(status) {
            return status === 'paid'
                ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400'
                : 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400';
        },

        paymentStatusLabel(status) {
            return status === 'paid' ? 'Paid' : 'Unpaid';
        },

        paymentLabel(method) {
            if (!method) return '-';
            return method.toUpperCase();
        },

        orderTypeLabel(type) {
            if (!type) return 'Walk-in';
            const map = { 'dine-in': 'Dine In', 'take-away': 'Take Away', 'walk-in': 'Walk-in' };
            return map[type] || type;
        },

        formatNumber(val) {
            if (val === undefined || val === null) return '0';
            return new Intl.NumberFormat('id-ID').format(Math.round(val));
        },

        formatOptions(options) {
            if (!options) return '';
            const parts = [];
            Object.keys(options).forEach(key => {
                parts.push(`${key}: ${options[key]}`);
            });
            return parts.join(', ');
        },

        printUrl(id) {
            return `/cashier/orders/${id}/print-bill`;
        }
    }
}
</script>
@endpush
@endsection
