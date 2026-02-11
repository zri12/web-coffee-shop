@extends('layouts.dashboard')

@section('title', 'Manager Dashboard')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-[#181411] dark:text-white">Dashboard Overview</h1>
        <p class="text-[#897561] text-sm">Welcome back, {{ auth()->user()->name }}</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Revenue -->
        <div class="bg-white dark:bg-[#1a1612] p-5 rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-[#897561] text-xs font-bold uppercase tracking-wider">Daily Revenue</p>
                    <h3 class="text-2xl font-bold text-[#181411] dark:text-white mt-1">Rp {{ number_format($stats['revenue'], 0, ',', '.') }}</h3>
                </div>
                <div class="p-2 bg-green-50 dark:bg-green-900/20 text-green-600 rounded-lg">
                    <span class="material-symbols-outlined">payments</span>
                </div>
            </div>
            @php $revUp = $stats['revenue_change'] >= 0; @endphp
            <div class="flex items-center gap-1 text-xs font-medium {{ $revUp ? 'text-green-600' : 'text-red-500' }}">
                <span class="material-symbols-outlined text-[16px]">{{ $revUp ? 'trending_up' : 'trending_down' }}</span>
                <span>{{ $stats['revenue_change'] }}% vs yesterday</span>
            </div>
        </div>

        <!-- Orders -->
        <div class="bg-white dark:bg-[#1a1612] p-5 rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-[#897561] text-xs font-bold uppercase tracking-wider">Total Orders</p>
                    <h3 class="text-2xl font-bold text-[#181411] dark:text-white mt-1">{{ $stats['orders'] }}</h3>
                </div>
                <div class="p-2 bg-blue-50 dark:bg-blue-900/20 text-blue-600 rounded-lg">
                    <span class="material-symbols-outlined">shopping_cart</span>
                </div>
            </div>
            @php $ordUp = $stats['orders_change'] >= 0; @endphp
            <div class="flex items-center gap-1 text-xs font-medium {{ $ordUp ? 'text-green-600' : 'text-red-500' }}">
                <span class="material-symbols-outlined text-[16px]">{{ $ordUp ? 'trending_up' : 'trending_down' }}</span>
                <span>{{ $stats['orders_change'] }}% vs yesterday</span>
            </div>
        </div>

        <!-- Avg Value -->
        <div class="bg-white dark:bg-[#1a1612] p-5 rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-[#897561] text-xs font-bold uppercase tracking-wider">Avg. Order Value</p>
                    <h3 class="text-2xl font-bold text-[#181411] dark:text-white mt-1">Rp {{ number_format($stats['avg_order_value'], 0, ',', '.') }}</h3>
                </div>
                <div class="p-2 bg-orange-50 dark:bg-orange-900/20 text-orange-600 rounded-lg">
                    <span class="material-symbols-outlined">receipt_long</span>
                </div>
            </div>
            @php $aovUp = $stats['aov_change'] >= 0; @endphp
            <div class="flex items-center gap-1 text-xs font-medium {{ $aovUp ? 'text-green-600' : 'text-red-500' }}">
                <span class="material-symbols-outlined text-[16px]">{{ $aovUp ? 'trending_up' : 'trending_down' }}</span>
                <span>{{ $stats['aov_change'] }}% vs yesterday</span>
            </div>
        </div>

        <!-- Occupancy -->
        <div class="bg-white dark:bg-[#1a1612] p-5 rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-[#897561] text-xs font-bold uppercase tracking-wider">Table Occupancy</p>
                    <h3 class="text-2xl font-bold text-[#181411] dark:text-white mt-1">{{ $stats['occupancy'] }}%</h3>
                </div>
                <div class="p-2 bg-purple-50 dark:bg-purple-900/20 text-purple-600 rounded-lg">
                    <span class="material-symbols-outlined">chair</span>
                </div>
            </div>
            <div class="flex items-center gap-1 text-xs font-medium text-green-600">
                <span class="material-symbols-outlined text-[16px]">trending_up</span>
                <span>+10% peak hour</span>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Daily Orders Chart -->
        <div class="bg-white dark:bg-[#1a1612] p-6 rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm lg:col-span-2">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-bold text-[#181411] dark:text-white">Daily Orders (7 days)</h3>
                    <p class="text-sm text-[#897561]">Real-time order counts</p>
                </div>
            </div>
            <div>
                <canvas id="ordersChart" class="w-full h-80"></canvas>
                <div id="ordersEmpty" class="hidden text-sm text-[#897561] mt-4">No data available</div>
            </div>
        </div>

        <!-- Payment Distribution -->
        <div class="bg-white dark:bg-[#1a1612] p-6 rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-bold text-[#181411] dark:text-white">Payment Methods</h3>
                    <p class="text-sm text-[#897561]">Distribution (today)</p>
                </div>
            </div>
            <div>
                <canvas id="paymentChart" class="w-full h-80"></canvas>
                <div id="paymentEmpty" class="hidden text-sm text-[#897561] mt-4">No data available</div>
            </div>
        </div>
    </div>


    <!-- Recent Orders Table -->
    <div class="bg-white dark:bg-[#1a1612] rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm overflow-hidden">
        <div class="p-6 border-b border-[#e6e0db] dark:border-[#3d362e] flex justify-between items-center">
            <h3 class="text-lg font-bold text-[#181411] dark:text-white">Recent Orders</h3>
            <button class="text-sm font-medium text-[#5c4d40] hover:text-primary">Filter</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 dark:bg-[#2c241b] border-b border-[#e6e0db] dark:border-[#3d362e]">
                    <tr>
                        <th class="px-6 py-4 font-bold text-[#897561] uppercase text-xs">Order ID</th>
                        <th class="px-6 py-4 font-bold text-[#897561] uppercase text-xs">Customer</th>
                        <th class="px-6 py-4 font-bold text-[#897561] uppercase text-xs">Items</th>
                        <th class="px-6 py-4 font-bold text-[#897561] uppercase text-xs">Amount</th>
                        <th class="px-6 py-4 font-bold text-[#897561] uppercase text-xs">Status</th>
                        <th class="px-6 py-4 font-bold text-[#897561] uppercase text-xs">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e6e0db] dark:divide-[#3d362e]">
                    @foreach($recentOrders as $order)
                    <tr class="hover:bg-gray-50 dark:hover:bg-[#2c241b]/50 transition-colors">
                        <td class="px-6 py-4 font-bold text-primary">#{{ $order->order_number }}</td>
                        <td class="px-6 py-4 font-medium text-[#181411] dark:text-white">{{ $order->customer_name }}</td>
                        <td class="px-6 py-4 text-[#5c4d40] dark:text-[#a89c92] truncate max-w-[200px]">
                            @if($order->items)
                                @foreach($order->items->take(2) as $item)
                                    {{ $item->quantity }}x {{ $item->menu->name ?? 'Item' }}{{ !$loop->last ? ',' : '' }}
                                @endforeach
                                @if($order->items->count() > 2)
                                    ...
                                @endif
                            @else
                                No items
                            @endif
                        </td>
                        <td class="px-6 py-4 font-bold text-[#181411] dark:text-white">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-0.5 rounded text-xs font-bold uppercase
                                {{ $order->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $order->status === 'pending' ? 'bg-orange-100 text-orange-700' : '' }}
                                {{ $order->status === 'processing' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}
                            ">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-[#897561] text-xs">
                            {{ $order->created_at->diffForHumans() }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const ordersLabels = @json($ordersPerDayLabels);
    const ordersData = @json($ordersPerDayCounts);
    const revenueData = @json($revenuePerDay);
    const paymentData = @json(array_values($paymentSummary));
    const paymentLabels = ['CASH', 'CARD', 'QRIS'];

    // Orders + Revenue combo chart
    const ordersCtx = document.getElementById('ordersChart');
    if (ordersCtx && ordersData.some(v => v > 0)) {
        new Chart(ordersCtx, {
            type: 'bar',
            data: {
                labels: ordersLabels,
                datasets: [
                    {
                        type: 'bar',
                        label: 'Orders',
                        data: ordersData,
                        backgroundColor: 'rgba(59, 130, 246, 0.6)',
                        borderRadius: 8,
                        borderSkipped: false,
                    },
                    {
                        type: 'line',
                        label: 'Revenue',
                        data: revenueData,
                        borderColor: 'rgba(34, 197, 94, 0.9)',
                        backgroundColor: 'rgba(34, 197, 94, 0.2)',
                        tension: 0.35,
                        yAxisID: 'y1',
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Orders' }
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        title: { display: true, text: 'Revenue (Rp)' },
                        ticks: {
                            callback: (value) => 'Rp ' + new Intl.NumberFormat('id-ID').format(value)
                        }
                    }
                },
                plugins: {
                    legend: { display: true },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => {
                                const val = ctx.parsed[ctx.datasetIndex === 0 ? 'y' : 'y1'] ?? ctx.parsed.y;
                                if (ctx.datasetIndex === 1) {
                                    return ctx.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(val);
                                }
                                return ctx.dataset.label + ': ' + val;
                            }
                        }
                    }
                }
            }
        });
    } else {
        document.getElementById('ordersEmpty').classList.remove('hidden');
    }

    // Payment distribution donut
    const payCtx = document.getElementById('paymentChart');
    const totalPay = paymentData.reduce((a,b)=>a+b,0);
    if (payCtx && totalPay > 0) {
        new Chart(payCtx, {
            type: 'doughnut',
            data: {
                labels: paymentLabels,
                datasets: [{
                    data: paymentData,
                    backgroundColor: [
                        'rgba(34,197,94,0.8)',
                        'rgba(59,130,246,0.8)',
                        'rgba(234,88,12,0.8)'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                cutout: '60%',
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => {
                                const val = ctx.raw;
                                const pct = totalPay > 0 ? ((val/totalPay)*100).toFixed(1) : 0;
                                return `${ctx.label}: ${val} (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });
    } else {
        document.getElementById('paymentEmpty').classList.remove('hidden');
    }
});
</script>
@endpush
