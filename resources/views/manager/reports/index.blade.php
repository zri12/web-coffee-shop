@extends('layouts.dashboard')

@section('title', 'Reports & Analytics')

@section('content')
@php
    $statusCards = [
        ['label' => 'Total Orders', 'value' => $totalOrders, 'icon' => 'stacked_bar_chart', 'bg' => 'from-[#f8efe6] to-[#f2e6d8]'],
        ['label' => 'Pending', 'value' => $pendingOrders, 'icon' => 'schedule', 'bg' => 'from-[#fff5e6] to-[#ffe7c2]'],
        ['label' => 'Processing', 'value' => $processingOrders, 'icon' => 'autorenew', 'bg' => 'from-[#e9f1ff] to-[#d9e8ff]'],
        ['label' => 'Completed', 'value' => $completedOrders, 'icon' => 'check_circle', 'bg' => 'from-[#e9f8f1] to-[#d7f1e3]'],
        ['label' => 'Cancelled', 'value' => $cancelledOrders, 'icon' => 'cancel', 'bg' => 'from-[#ffecec] to-[#ffdede]'],
    ];
@endphp
<div class="p-6 space-y-6" x-data="reportsPage({
    period: '{{ $filters['period'] ?? 'daily' }}',
    dateFrom: '{{ $filters['date_from'] ?? '' }}',
    dateTo: '{{ $filters['date_to'] ?? '' }}',
    paymentMethod: '{{ $filters['payment_method'] ?? 'all' }}',
    orderType: '{{ $filters['order_type'] ?? 'all' }}',
})">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="space-y-1">
            <div class="inline-flex items-center gap-2 rounded-full bg-[#f6efe6] px-3 py-1 text-[11px] font-semibold uppercase tracking-wide text-[#7a5f43]">
                <span class="material-symbols-outlined text-base">insights</span>
                Business Insights
            </div>
            <h1 class="text-3xl font-semibold text-[#1c1713]">Reports & Analytics</h1>
            <p class="text-sm text-[#8a7a66]">Monitor revenue, orders, payments, products, and peak hours in one glance.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <div class="flex overflow-hidden rounded-xl border border-[#e6e0db] bg-white shadow-sm">
                @foreach(['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly'] as $key => $label)
                    <button @click="changePeriod('{{ $key }}')" class="px-3 py-2 text-sm font-semibold {{ ($filters['period'] ?? 'daily') === $key ? 'bg-[#f7f0e6] text-[#5a4634]' : 'text-[#8a7a66]' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
            <button @click="exportCsv" class="flex items-center gap-2 rounded-xl border border-[#e6e0db] bg-white px-3 py-2 text-sm font-semibold text-[#6d5844] shadow-sm hover:-translate-y-0.5 hover:shadow-md transition" title="Export CSV">
                <span class="material-symbols-outlined text-base">file_save</span>
                Export CSV
            </button>
        </div>
    </div>

    <div class="flex flex-wrap gap-3 rounded-2xl border border-[#e6e0db] bg-white/90 px-4 py-3 shadow-sm">
        <div class="flex items-center gap-2 text-sm text-[#8a7a66]">
            <span class="material-symbols-outlined text-[18px] text-[#c49a6c]">event</span>
            <input type="date" x-model="dateFrom" class="rounded-lg border border-[#e6e0db] px-3 py-1.5 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20" />
            <span class="text-[#c4b7a4]">–</span>
            <input type="date" x-model="dateTo" class="rounded-lg border border-[#e6e0db] px-3 py-1.5 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20" />
        </div>
        <div class="flex items-center gap-2 text-sm text-[#8a7a66]">
            <span class="material-symbols-outlined text-[18px] text-[#c49a6c]">payments</span>
            <select x-model="paymentMethod" class="rounded-lg border border-[#e6e0db] px-3 py-1.5 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20">
                <option value="all">All payments</option>
                <option value="cash">Cash</option>
                <option value="card">Card</option>
                <option value="qris">QRIS</option>
            </select>
        </div>
        <div class="flex items-center gap-2 text-sm text-[#8a7a66]">
            <span class="material-symbols-outlined text-[18px] text-[#c49a6c]">storefront</span>
            <select x-model="orderType" class="rounded-lg border border-[#e6e0db] px-3 py-1.5 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20">
                <option value="all">All order types</option>
                <option value="dine_in">Dine-in</option>
                <option value="takeaway">Takeaway / Walk-in</option>
                <option value="qr">QR</option>
            </select>
        </div>
        <div class="flex items-center gap-2 ml-auto">
            <button @click="resetFilters" class="rounded-xl border border-[#e6e0db] px-4 py-2 text-sm font-semibold text-[#6d5844] hover:bg-[#f7f2ec]">Reset</button>
            <button @click="applyFilters" class="rounded-xl bg-primary px-4 py-2 text-sm font-semibold text-white shadow hover:bg-primary/90">Apply Filters</button>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
        <div class="group relative overflow-hidden rounded-2xl border border-[#e6e0db] bg-gradient-to-br from-[#fff7ed] to-[#f4e8db] p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div class="rounded-2xl bg-white/70 p-3 text-primary shadow-inner">
                    <span class="material-symbols-outlined">payments</span>
                </div>
                <span class="rounded-full bg-white/70 px-3 py-1 text-[11px] font-semibold text-[#5a4634]">Revenue</span>
            </div>
            <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-[#8a7a66]">Total Revenue ({{ ucfirst($filters['period'] ?? 'daily') }})</p>
            <p class="text-3xl font-semibold text-[#1c1713]">Rp {{ number_format($currentRevenue, 0, ',', '.') }}</p>
            <div class="mt-2 inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold {{ $revenueChange >= 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                <span class="material-symbols-outlined text-[16px]">{{ $revenueChange >= 0 ? 'trending_up' : 'trending_down' }}</span>
                {{ $revenueChange }}% vs prev period
            </div>
        </div>
        @foreach($statusCards as $card)
            <div class="rounded-2xl border border-[#e6e0db] bg-gradient-to-br {{ $card['bg'] }} p-5 shadow-sm">
                <div class="flex items-start justify-between">
                    <div class="rounded-2xl bg-white/70 p-3 text-[#5a4634] shadow-inner">
                        <span class="material-symbols-outlined">{{ $card['icon'] }}</span>
                    </div>
                </div>
                <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-[#8a7a66]">{{ $card['label'] }}</p>
                <p class="text-3xl font-semibold text-[#1c1713]">{{ number_format($card['value']) }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-4">
            <div class="rounded-2xl border border-[#e6e0db] bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs font-semibold uppercase text-[#b19f8c]">Revenue & Orders</p>
                        <h3 class="text-lg font-semibold text-[#1c1713]">Revenue Overview</h3>
                    </div>
                </div>
                <canvas id="revenueChart" class="h-64"></canvas>
            </div>
            <div class="rounded-2xl border border-[#e6e0db] bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs font-semibold uppercase text-[#b19f8c]">Peak Hours</p>
                        <h3 class="text-lg font-semibold text-[#1c1713]">Busy vs Quiet Times</h3>
                    </div>
                </div>
                <canvas id="peakChart" class="h-56"></canvas>
            </div>
        </div>
        <div class="space-y-4">
            <div class="rounded-2xl border border-[#e6e0db] bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-xs font-semibold uppercase text-[#b19f8c]">Payments</p>
                        <h3 class="text-lg font-semibold text-[#1c1713]">Payment Method Mix</h3>
                    </div>
                </div>
                <div class="h-56"><canvas id="paymentChart"></canvas></div>
            </div>
            <div class="rounded-2xl border border-[#e6e0db] bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-xs font-semibold uppercase text-[#b19f8c]">Service Mix</p>
                        <h3 class="text-lg font-semibold text-[#1c1713]">Dine-in vs Takeaway</h3>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="h-40 w-40"><canvas id="serviceChart"></canvas></div>
                    <div class="space-y-2 text-sm text-[#5a4634]">
                        <div class="flex items-center gap-2"><span class="inline-block h-3 w-3 rounded-full bg-[#5b8def]"></span> Dine-in: {{ $dineIn }}</div>
                        <div class="flex items-center gap-2"><span class="inline-block h-3 w-3 rounded-full bg-[#f59e0b]"></span> Takeaway: {{ $takeAway }}</div>
                        <div class="flex items-center gap-2"><span class="inline-block h-3 w-3 rounded-full bg-[#10b981]"></span> Occupancy: {{ $occupancyRate }}%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-2xl border border-[#e6e0db] bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-xs font-semibold uppercase text-[#b19f8c]">Products</p>
                    <h3 class="text-lg font-semibold text-[#1c1713]">Best Selling Items</h3>
                </div>
            </div>
            <div class="space-y-3">
                @forelse($bestItems as $item)
                    <div class="flex items-center justify-between rounded-xl bg-[#f9f5ef] px-4 py-3">
                        <div>
                            <p class="font-semibold text-[#1c1713]">{{ $item->name }}</p>
                            <p class="text-xs text-[#8a7a66]">Qty {{ $item->qty }} · Rp {{ number_format($item->revenue, 0, ',', '.') }}</p>
                        </div>
                        <div class="text-lg font-semibold text-[#5a4634]">{{ $item->qty }}</div>
                    </div>
                @empty
                    <p class="text-sm text-[#8a7a66]">No data in this range.</p>
                @endforelse
            </div>
        </div>
        <div class="rounded-2xl border border-[#e6e0db] bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-xs font-semibold uppercase text-[#b19f8c]">Categories</p>
                    <h3 class="text-lg font-semibold text-[#1c1713]">Category Contribution</h3>
                </div>
            </div>
            <div class="space-y-3">
                @forelse($categoryPerformance as $cat)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="font-semibold text-[#1c1713]">{{ $cat['name'] }}</span>
                            <span class="text-[#5a4634] font-semibold">{{ $cat['percentage'] }}%</span>
                        </div>
                        <div class="h-2 w-full rounded-full bg-[#f1e9de] overflow-hidden">
                            <div class="h-2 rounded-full bg-gradient-to-r from-[#f59e0b] to-[#d47311]" style="width: {{ $cat['percentage'] }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-[#8a7a66]">No category data.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function reportsPage(initial) {
        return {
            period: initial.period,
            dateFrom: initial.dateFrom,
            dateTo: initial.dateTo,
            paymentMethod: initial.paymentMethod,
            orderType: initial.orderType,
            changePeriod(p) {
                this.period = p;
                this.applyFilters();
            },
            resetFilters() {
                this.dateFrom = '';
                this.dateTo = '';
                this.paymentMethod = 'all';
                this.orderType = 'all';
                this.applyFilters();
            },
            applyFilters() {
                const params = new URLSearchParams();
                params.set('period', this.period);
                if (this.dateFrom) params.set('date_from', this.dateFrom);
                if (this.dateTo) params.set('date_to', this.dateTo);
                if (this.paymentMethod && this.paymentMethod !== 'all') params.set('payment_method', this.paymentMethod);
                if (this.orderType && this.orderType !== 'all') params.set('order_type', this.orderType);
                window.location.href = '{{ route('manager.reports') }}' + '?' + params.toString();
            },
            exportCsv() {
                const params = new URLSearchParams();
                params.set('period', this.period);
                params.set('export', 'csv');
                if (this.dateFrom) params.set('date_from', this.dateFrom);
                if (this.dateTo) params.set('date_to', this.dateTo);
                if (this.paymentMethod && this.paymentMethod !== 'all') params.set('payment_method', this.paymentMethod);
                if (this.orderType && this.orderType !== 'all') params.set('order_type', this.orderType);
                window.location.href = '{{ route('manager.reports') }}' + '?' + params.toString();
            }
        }
    }

    const revenueLabels = @json($revenueLabels);
    const revenueSeries = @json($revenueSeries);
    const paymentChart = @json($paymentChart);
    const peakHours = @json($peakHours);
    const dineIn = {{ (int) $dineIn }};
    const takeAway = {{ (int) $takeAway }};
    const occupancyRate = {{ (int) $occupancyRate }};

    const fmtCurrency = (v) => 'Rp ' + new Intl.NumberFormat('id-ID').format(v || 0);

    const ctxRev = document.getElementById('revenueChart');
    if (ctxRev) {
        new Chart(ctxRev, {
            type: 'bar',
            data: {
                labels: revenueLabels,
                datasets: [
                    {
                        type: 'bar',
                        label: 'Revenue',
                        data: revenueSeries,
                        backgroundColor: '#d47311',
                        borderRadius: 8,
                        maxBarThickness: 36,
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => fmtCurrency(ctx.parsed.y)
                        }
                    }
                },
                scales: {
                    y: {
                        ticks: { callback: (v) => 'Rp ' + (v/1000) + 'k' },
                        beginAtZero: true,
                        grid: { color: '#f0e8dd' }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    const ctxPay = document.getElementById('paymentChart');
    if (ctxPay) {
        new Chart(ctxPay, {
            type: 'doughnut',
            data: {
                labels: paymentChart.labels,
                datasets: [{
                    data: paymentChart.data,
                    backgroundColor: ['#f59e0b', '#5b8def', '#10b981'],
                    borderWidth: 0,
                }]
            },
            options: {
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: { callbacks: { label: ctx => `${ctx.label}: ${fmtCurrency(ctx.parsed)}` } }
                },
                cutout: '60%'
            }
        });
    }

    const ctxPeak = document.getElementById('peakChart');
    if (ctxPeak) {
        new Chart(ctxPeak, {
            type: 'bar',
            data: {
                labels: peakHours.map(h => h.label),
                datasets: [{
                    label: 'Orders',
                    data: peakHours.map(h => h.value),
                    backgroundColor: '#5b8def',
                    borderRadius: 6,
                    maxBarThickness: 22,
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f0e8dd' } },
                    x: { grid: { display: false }, ticks: { maxRotation: 0, autoSkip: true } }
                }
            }
        });
    }

    const ctxService = document.getElementById('serviceChart');
    if (ctxService) {
        new Chart(ctxService, {
            type: 'doughnut',
            data: {
                labels: ['Dine-in', 'Takeaway'],
                datasets: [{
                    data: [dineIn, takeAway],
                    backgroundColor: ['#5b8def', '#f59e0b'],
                    borderWidth: 0,
                }]
            },
            options: {
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => `${ctx.label}: ${ctx.parsed}` } } },
                cutout: '60%'
            }
        });
    }
</script>
@endpush
