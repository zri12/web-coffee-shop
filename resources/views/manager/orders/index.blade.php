@extends('layouts.dashboard')

@section('title', 'Order Management')

@section('content')
@php
    $statusMeta = [
        'pending' => [
            'label' => 'Pending',
            'icon' => 'schedule',
            'pill' => 'bg-amber-100 text-amber-800 ring-1 ring-amber-200',
            'soft' => 'bg-amber-50 text-amber-700',
        ],
        'processing' => [
            'label' => 'Processing',
            'icon' => 'autorenew',
            'pill' => 'bg-blue-100 text-blue-800 ring-1 ring-blue-200',
            'soft' => 'bg-blue-50 text-blue-700',
        ],
        'completed' => [
            'label' => 'Completed',
            'icon' => 'check_circle',
            'pill' => 'bg-emerald-100 text-emerald-800 ring-1 ring-emerald-200',
            'soft' => 'bg-emerald-50 text-emerald-700',
        ],
        'cancelled' => [
            'label' => 'Cancelled',
            'icon' => 'cancel',
            'pill' => 'bg-rose-100 text-rose-800 ring-1 ring-rose-200',
            'soft' => 'bg-rose-50 text-rose-700',
        ],
    ];

    $paymentMeta = [
        'cash' => ['label' => 'Cash', 'icon' => 'payments'],
        'card' => ['label' => 'Card', 'icon' => 'credit_card'],
        'qris' => ['label' => 'QRIS', 'icon' => 'qr_code_2'],
    ];
@endphp

<div class="p-6 space-y-6 max-w-7xl mx-auto" x-data="managerOrdersPage({
    status: '{{ $filters['status'] ?? 'all' }}',
    paymentMethod: '{{ $filters['payment_method'] ?? 'all' }}',
    dateFrom: '{{ $filters['date_from'] ?? '' }}',
    dateTo: '{{ $filters['date_to'] ?? '' }}',
    search: '{{ $filters['search'] ?? '' }}'
})" x-init="init()">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <div class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-[#f5eee6] to-[#efe6dd] px-3 py-1 text-[11px] font-semibold uppercase tracking-wide text-[#7a5f43]">
                <span class="material-symbols-outlined text-base">speed</span>
                Manager View
            </div>
            <h1 class="mt-2 text-3xl font-semibold text-[#1c1713] dark:text-white">Order Management</h1>
            <p class="text-sm text-[#8a7a66]">Monitor order flow, status, and payment performance in one clean dashboard.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <div class="relative">
                <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[#b8a894]">search</span>
                <input type="search" x-model.debounce.400ms="search" @keydown.enter.prevent="applyFilter" placeholder="Search order ID, customer, table" class="w-72 md:w-80 rounded-xl border border-[#e6e0db] bg-white px-10 py-2 text-sm text-[#1c1713] shadow-sm transition focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" />
            </div>
            <button @click="showFilter = !showFilter" class="group relative flex h-11 w-11 items-center justify-center rounded-xl border border-[#e6e0db] bg-white text-[#6d5844] shadow-sm transition hover:-translate-y-0.5 hover:shadow-md" title="Filter">
                <span class="material-symbols-outlined">tune</span>
            </button>
            <div class="relative z-40">
                <button @click="showExport = !showExport" class="group relative flex h-11 w-11 items-center justify-center rounded-xl border border-[#e6e0db] bg-white text-[#6d5844] shadow-sm transition hover:-translate-y-0.5 hover:shadow-md" title="Export">
                    <span class="material-symbols-outlined">ios_share</span>
                </button>
                <div x-cloak x-show="showExport" @click.away="showExport = false" x-transition class="absolute right-0 mt-2 w-52 rounded-2xl border border-[#e6e0db] bg-white shadow-xl ring-1 ring-black/5 z-50">
                    <button @click="exportData('pdf')" class="flex w-full items-center gap-2 px-4 py-3 text-left text-sm text-[#1c1713] hover:bg-[#f7f2ec]">
                        <span class="material-symbols-outlined text-rose-500">picture_as_pdf</span>
                        Export PDF
                    </button>
                    <button @click="exportData('excel')" class="flex w-full items-center gap-2 px-4 py-3 text-left text-sm text-[#1c1713] hover:bg-[#f7f2ec]">
                        <span class="material-symbols-outlined text-emerald-500">table_chart</span>
                        Export Excel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="group relative overflow-hidden rounded-2xl border border-[#e6e0db] bg-gradient-to-br from-white to-[#f7f1ea] p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg min-h-[160px]">
            <div class="flex items-start justify-between">
                <div class="rounded-2xl bg-blue-50 p-3 text-blue-600 shadow-inner">
                    <span class="material-symbols-outlined">inventory_2</span>
                </div>
                <span class="rounded-full bg-blue-100 px-3 py-1 text-[11px] font-semibold text-blue-700">All time</span>
            </div>
            <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-[#8a7a66]">Total Orders</p>
            <p class="text-3xl font-semibold text-[#1c1713]">{{ number_format($totalOrders) }}</p>
            <p class="text-xs text-[#8a7a66]">Includes all statuses</p>
        </div>
        <div class="group relative overflow-hidden rounded-2xl border border-[#e6e0db] bg-gradient-to-br from-white to-[#fff7ec] p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg min-h-[160px]">
            <div class="flex items-start justify-between">
                <div class="rounded-2xl bg-amber-50 p-3 text-amber-600 shadow-inner">
                    <span class="material-symbols-outlined">schedule</span>
                </div>
                <span class="rounded-full bg-amber-100 px-3 py-1 text-[11px] font-semibold text-amber-700">Attention</span>
            </div>
            <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-[#8a7a66]">Pending Orders</p>
            <p class="text-3xl font-semibold text-[#1c1713]">{{ number_format($pendingOrders) }}</p>
            <p class="text-xs text-[#8a7a66]">Awaiting confirmation</p>
        </div>
        <div class="group relative overflow-hidden rounded-2xl border border-[#e6e0db] bg-gradient-to-br from-white to-[#eef4ff] p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg min-h-[160px]">
            <div class="flex items-start justify-between">
                <div class="rounded-2xl bg-blue-50 p-3 text-blue-600 shadow-inner">
                    <span class="material-symbols-outlined">autorenew</span>
                </div>
                <span class="rounded-full bg-blue-100 px-3 py-1 text-[11px] font-semibold text-blue-700">In progress</span>
            </div>
            <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-[#8a7a66]">Processing Orders</p>
            <p class="text-3xl font-semibold text-[#1c1713]">{{ number_format($processingOrders) }}</p>
            <p class="text-xs text-[#8a7a66]">Being prepared</p>
        </div>
        <div class="group relative overflow-hidden rounded-2xl border border-[#e6e0db] bg-gradient-to-br from-white to-[#ecf9f2] p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg min-h-[160px]">
            <div class="flex items-start justify-between">
                <div class="rounded-2xl bg-emerald-50 p-3 text-emerald-600 shadow-inner">
                    <span class="material-symbols-outlined">check_circle</span>
                </div>
                <span class="rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-semibold text-emerald-700">Closed</span>
            </div>
            <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-[#8a7a66]">Completed Orders</p>
            <p class="text-3xl font-semibold text-[#1c1713]">{{ number_format($completedOrders) }}</p>
            <p class="text-xs text-[#8a7a66]">Paid & delivered</p>
        </div>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-[#e6e0db] bg-white/80 px-4 py-3 shadow-sm backdrop-blur">
        <div class="flex flex-wrap items-center gap-2 overflow-x-auto">
            @foreach (['all' => 'All', 'pending' => 'Pending', 'processing' => 'Processing', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $value => $label)
                <button @click="status = '{{ $value }}'; applyFilter()" class="flex items-center gap-2 rounded-full border px-3 py-1.5 text-xs font-semibold transition {{ ($filters['status'] ?? 'all') === $value ? 'border-[#c8b69f] bg-[#f3ebe3] text-[#5a4634]' : 'border-transparent bg-[#f7f2ec] text-[#8a7a66] hover:border-[#e6e0db]' }}">
                    @if($value !== 'all')
                        <span class="material-symbols-outlined text-[16px]">{{ $statusMeta[$value]['icon'] ?? 'trip_origin' }}</span>
                    @endif
                    {{ $label }}
                </button>
            @endforeach
        </div>
        <div class="flex flex-wrap items-center gap-2 text-xs text-[#8a7a66]">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-[#c49a6c]">event</span>
                <input type="date" x-model="dateFrom" class="rounded-lg border border-[#e6e0db] px-3 py-1.5 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20" />
                <span class="text-[#c4b7a4]">–</span>
                <input type="date" x-model="dateTo" class="rounded-lg border border-[#e6e0db] px-3 py-1.5 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20" />
            </div>
            <div class="h-4 w-px bg-[#e6e0db]"></div>
            <div class="flex items-center gap-2 overflow-x-auto">
                @foreach(['all' => 'All payments', 'cash' => 'Cash', 'card' => 'Card', 'qris' => 'QRIS'] as $value => $label)
                    <button @click="paymentMethod='{{ $value }}'; applyFilter()" class="rounded-full px-3 py-1.5 text-xs font-semibold transition {{ ($filters['payment_method'] ?? 'all') === $value ? 'bg-[#e9f5ef] text-[#27664c] ring-1 ring-emerald-200' : 'bg-[#f7f2ec] text-[#8a7a66] hover:ring-1 hover:ring-[#e6e0db]' }}">{{ $label }}</button>
                @endforeach
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-[#e6e0db] bg-white/95 p-2 shadow-sm">
        <div class="hidden px-4 py-2 text-[11px] font-semibold uppercase tracking-wide text-[#b19f8c] md:grid md:grid-cols-12">
            <div class="col-span-3">Order ID</div>
            <div class="col-span-2">Customer</div>
            <div class="col-span-1">Table</div>
            <div class="col-span-2">Total</div>
            <div class="col-span-2">Status</div>
            <div class="col-span-1">When</div>
            <div class="col-span-1 text-right">Action</div>
        </div>

        <div class="hidden flex-col gap-2 md:flex">
            @forelse($orders as $order)
                @php
                    $detailPayload = [
                        'order_number' => $order->order_number,
                        'status' => $order->status,
                        'customer_name' => $order->customer_name,
                        'table' => ($order->table_number && $order->order_type === 'dine_in') ? 'Table ' . $order->table_number : 'Takeaway',
                        'total' => 'Rp ' . number_format($order->total_amount, 0, ',', '.'),
                        'payment_method' => optional($order->payment)->method,
                        'payment_status' => optional($order->payment)->status,
                        'created_at' => $order->created_at->format('M d, Y H:i'),
                        'relative_time' => $order->created_at->diffForHumans(),
                        'detail_url' => route('dashboard.orders.show', $order),
                        'items' => $order->items->map(function ($item) {
                            return [
                                'name' => $item->menu_name ?? optional($item->menu)->name ?? 'Item',
                                'qty' => $item->quantity,
                                'notes' => $item->notes,
                                'options' => $item->options_text,
                                'subtotal' => 'Rp ' . number_format($item->subtotal, 0, ',', '.'),
                            ];
                        })->values(),
                    ];
                @endphp
                <div class="group grid grid-cols-12 items-center gap-3 rounded-2xl border border-transparent bg-white/95 px-4 py-3 shadow-[0_1px_0_rgba(0,0,0,0.03)] transition hover:-translate-y-0.5 hover:border-[#e0d5c9] hover:shadow-lg odd:bg-[#fbf7f2]">
                    <div class="col-span-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg text-[#c49a6c]">receipt_long</span>
                        <a href="{{ route('dashboard.orders.show', $order) }}" class="font-mono text-sm font-semibold text-primary hover:underline">{{ $order->order_number }}</a>
                    </div>
                    <div class="col-span-2 flex items-center gap-2 text-[#1c1713]">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-[#f2e8de] text-sm font-semibold text-[#6d5844]">
                            {{ strtoupper(substr($order->customer_name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold">{{ $order->customer_name }}</p>
                            <p class="text-xs text-[#8a7a66]">{{ optional($order->payment)->method ? strtoupper(optional($order->payment)->method) : 'Unpaid' }}</p>
                        </div>
                    </div>
                    <div class="col-span-1">
                        <span class="rounded-full bg-[#f3ebe3] px-3 py-1 text-xs font-semibold text-[#5a4634]">
                            @if($order->table_number && $order->order_type === 'dine_in')
                                Table {{ $order->table_number }}
                            @else
                                Takeaway
                            @endif
                        </span>
                    </div>
                    <div class="col-span-2 text-lg font-semibold text-[#1c1713]">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
                    <div class="col-span-2">
                        <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-sm font-semibold ring-1 ring-inset {{ $statusMeta[$order->status]['pill'] ?? 'bg-[#f7f2ec] text-[#5a4634] ring-[#e6e0db]' }}">
                            <span class="material-symbols-outlined text-base">{{ $statusMeta[$order->status]['icon'] ?? 'info' }}</span>
                            {{ $statusMeta[$order->status]['label'] ?? ucfirst($order->status) }}
                        </span>
                    </div>
                    <div class="col-span-1 text-xs font-semibold text-[#7a5f43]" title="{{ $order->created_at->format('M d, Y H:i') }}">{{ $order->created_at->diffForHumans() }}</div>
                    <div class="col-span-1 text-right">
                        <button @click="openDetail(@js($detailPayload))" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-[#e6e0db] bg-white text-[#6d5844] shadow-sm transition hover:-translate-y-0.5 hover:bg-[#f7f2ec] hover:shadow-md" title="View Order Details">
                            <span class="material-symbols-outlined">visibility</span>
                        </button>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-[#e6e0db] bg-[#fdfaf6] px-6 py-10 text-center">
                    <span class="material-symbols-outlined text-4xl text-[#d0b89c]">sentiment_neutral</span>
                    <p class="mt-2 text-lg font-semibold text-[#1c1713]">No orders found</p>
                    <p class="text-sm text-[#8a7a66]">Try adjusting filters or date range.</p>
                </div>
            @endforelse
        </div>

        <div class="space-y-3 md:hidden">
            @forelse($orders as $order)
                @php
                    $detailPayload = [
                        'order_number' => $order->order_number,
                        'status' => $order->status,
                        'customer_name' => $order->customer_name,
                        'table' => ($order->table_number && $order->order_type === 'dine_in') ? 'Table ' . $order->table_number : 'Takeaway',
                        'total' => 'Rp ' . number_format($order->total_amount, 0, ',', '.'),
                        'payment_method' => optional($order->payment)->method,
                        'payment_status' => optional($order->payment)->status,
                        'created_at' => $order->created_at->format('M d, Y H:i'),
                        'relative_time' => $order->created_at->diffForHumans(),
                        'detail_url' => route('dashboard.orders.show', $order),
                        'items' => $order->items->map(function ($item) {
                            return [
                                'name' => $item->menu_name ?? optional($item->menu)->name ?? 'Item',
                                'qty' => $item->quantity,
                                'notes' => $item->notes,
                                'options' => $item->options_text,
                                'subtotal' => 'Rp ' . number_format($item->subtotal, 0, ',', '.'),
                            ];
                        })->values(),
                    ];
                @endphp
                <div class="rounded-2xl border border-[#e6e0db] bg-white px-4 py-3 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-mono text-sm font-semibold text-primary">{{ $order->order_number }}</p>
                            <p class="text-xs text-[#8a7a66]">{{ $order->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset {{ $statusMeta[$order->status]['pill'] ?? 'bg-[#f7f2ec] text-[#5a4634] ring-[#e6e0db]' }}">
                            <span class="material-symbols-outlined text-sm">{{ $statusMeta[$order->status]['icon'] ?? 'info' }}</span>
                            {{ $statusMeta[$order->status]['label'] ?? ucfirst($order->status) }}
                        </span>
                    </div>
                    <div class="mt-3 space-y-2 text-sm text-[#1c1713]">
                        <div class="flex items-center justify-between">
                            <span class="text-[#8a7a66]">Customer</span>
                            <span class="font-semibold">{{ $order->customer_name }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[#8a7a66]">Table</span>
                            <span class="rounded-full bg-[#f3ebe3] px-3 py-1 text-xs font-semibold text-[#5a4634]">
                                @if($order->table_number && $order->order_type === 'dine_in')
                                    Table {{ $order->table_number }}
                                @else
                                    Takeaway
                                @endif
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[#8a7a66]">Total</span>
                            <span class="text-lg font-semibold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center justify-between">
                        <div class="flex items-center gap-2 text-xs text-[#8a7a66]">
                            <span class="material-symbols-outlined text-base">{{ $paymentMeta[optional($order->payment)->method]['icon'] ?? 'account_balance_wallet' }}</span>
                            <span>{{ optional($order->payment)->method ? strtoupper(optional($order->payment)->method) : 'UNPAID' }}</span>
                        </div>
                        <button @click="openDetail(@js($detailPayload))" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-[#e6e0db] bg-white text-[#6d5844] shadow-sm transition hover:-translate-y-0.5 hover:bg-[#f7f2ec] hover:shadow-md" title="View Order Details">
                            <span class="material-symbols-outlined">visibility</span>
                        </button>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-[#e6e0db] bg-[#fdfaf6] px-6 py-10 text-center">
                    <span class="material-symbols-outlined text-4xl text-[#d0b89c]">sentiment_neutral</span>
                    <p class="mt-2 text-lg font-semibold text-[#1c1713]">No orders found</p>
                    <p class="text-sm text-[#8a7a66]">Try adjusting filters or date range.</p>
                </div>
            @endforelse
        </div>
    </div>

    <div x-cloak x-show="showFilter" @click.away="showFilter = false" x-transition.opacity class="fixed inset-0 z-40 flex items-start justify-center bg-black/50 backdrop-blur-sm pt-20 md:pt-24 px-4">
        <div x-transition:enter="transition transform ease-out duration-250" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100" x-transition:leave="transition transform ease-in duration-200" x-transition:leave-start="scale-100 opacity-100" x-transition:leave-end="scale-95 opacity-0" class="w-full max-w-[420px] rounded-2xl bg-white p-6 shadow-2xl border border-[#e6e0db]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase text-[#b19f8c]">Refine results</p>
                    <h3 class="text-lg font-semibold text-[#1c1713]">Filters</h3>
                </div>
                <button @click="showFilter = false" class="rounded-full bg-[#f7f2ec] p-2 text-[#6d5844] hover:bg-[#efe6dd]">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="mt-4 space-y-4">
                <div>
                    <p class="text-xs font-semibold uppercase text-[#8a7a66]">Status</p>
                    <div class="mt-2 grid grid-cols-2 gap-2">
                        @foreach (['all' => 'All', 'pending' => 'Pending', 'processing' => 'Processing', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $value => $label)
                            <button @click="status='{{ $value }}'" :class="status === '{{ $value }}' ? 'border-[#c8b69f] bg-[#f3ebe3] text-[#5a4634]' : 'border-[#e6e0db] bg-white text-[#8a7a66]'" class="flex items-center gap-2 rounded-xl border px-3 py-2 text-sm font-semibold transition">
                                @if($value !== 'all')
                                    <span class="material-symbols-outlined text-[18px]">{{ $statusMeta[$value]['icon'] ?? 'info' }}</span>
                                @endif
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-semibold uppercase text-[#8a7a66]">Date from</label>
                        <input type="date" x-model="dateFrom" class="mt-1 w-full rounded-xl border border-[#e6e0db] px-3 py-2 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20">
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase text-[#8a7a66]">Date to</label>
                        <input type="date" x-model="dateTo" class="mt-1 w-full rounded-xl border border-[#e6e0db] px-3 py-2 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20">
                    </div>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase text-[#8a7a66]">Payment method</p>
                    <div class="mt-2 grid grid-cols-3 gap-2">
                        @foreach(['all' => 'All', 'cash' => 'Cash', 'card' => 'Card', 'qris' => 'QRIS'] as $value => $label)
                            <button @click="paymentMethod='{{ $value }}'" :class="paymentMethod === '{{ $value }}' ? 'border-[#c8b69f] bg-[#f3ebe3] text-[#5a4634]' : 'border-[#e6e0db] bg-white text-[#8a7a66]'" class="flex items-center justify-center gap-2 rounded-xl border px-3 py-2 text-sm font-semibold transition">
                                @if($value !== 'all')
                                    <span class="material-symbols-outlined text-[18px]">{{ $paymentMeta[$value]['icon'] ?? 'account_balance_wallet' }}</span>
                                @endif
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="status='all'; paymentMethod='all'; dateFrom=''; dateTo=''; search=''; applyFilter()" class="flex-1 rounded-xl border border-[#e6e0db] px-4 py-3 text-sm font-semibold text-[#6d5844] hover:bg-[#f7f2ec]">Reset</button>
                    <button @click="applyFilter(); showFilter=false" class="flex-1 rounded-xl bg-primary px-4 py-3 text-sm font-semibold text-white shadow hover:bg-primary/90">Apply</button>
                </div>
            </div>
        </div>
    </div>

    <div x-cloak x-show="showDetail" class="fixed inset-0 z-50 flex items-start justify-end bg-black/50 backdrop-blur-sm" x-transition>
        <div class="h-full w-full max-w-2xl overflow-y-auto rounded-l-3xl bg-white p-6 shadow-2xl">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase text-[#b19f8c]">Order</p>
                    <h3 class="text-2xl font-semibold text-[#1c1713]" x-text="selectedOrder?.order_number"></h3>
                    <p class="text-sm text-[#8a7a66]" x-text="selectedOrder?.relative_time"></p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-sm font-semibold" :class="badgeClass(selectedOrder?.status)">
                        <span class="material-symbols-outlined text-sm" x-text="badgeIcon(selectedOrder?.status)"></span>
                        <span x-text="badgeLabel(selectedOrder?.status)"></span>
                    </span>
                    <button @click="closeDetail" class="rounded-full bg-[#f7f2ec] p-2 text-[#6d5844] hover:bg-[#efe6dd]">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-3 rounded-2xl border border-[#e6e0db] bg-[#fbf7f2] p-4 md:grid-cols-2">
                <div>
                    <p class="text-xs font-semibold uppercase text-[#8a7a66]">Customer</p>
                    <p class="text-base font-semibold text-[#1c1713]" x-text="selectedOrder?.customer_name"></p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase text-[#8a7a66]">Table</p>
                    <p class="text-base font-semibold text-[#1c1713]" x-text="selectedOrder?.table"></p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase text-[#8a7a66]">Payment</p>
                    <p class="text-base font-semibold text-[#1c1713]"><span class="capitalize" x-text="selectedOrder?.payment_method || 'Unpaid'"></span> · <span class="uppercase" x-text="selectedOrder?.payment_status || '-' "></span></p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase text-[#8a7a66]">Date</p>
                    <p class="text-base font-semibold text-[#1c1713]" x-text="selectedOrder?.created_at"></p>
                </div>
            </div>

            <div class="mt-4">
                <div class="flex items-center justify-between">
                    <h4 class="text-lg font-semibold text-[#1c1713]">Items</h4>
                    <span class="text-sm font-semibold text-[#5a4634]" x-text="selectedOrder?.total"></span>
                </div>
                <div class="mt-3 space-y-3">
                    <template x-for="(item, index) in selectedOrder?.items || []" :key="index">
                        <div class="rounded-2xl border border-[#e6e0db] bg-white px-4 py-3 shadow-sm">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#f2e8de] text-sm font-semibold text-[#6d5844]" x-text="item.qty + 'x'"></div>
                                    <div>
                                        <p class="font-semibold text-[#1c1713]" x-text="item.name"></p>
                                        <p class="text-xs text-[#8a7a66]" x-text="item.notes || item.options"></p>
                                    </div>
                                </div>
                                <p class="text-sm font-semibold text-[#1c1713]" x-text="item.subtotal"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="mt-4 flex flex-wrap items-center gap-3">
                <a :href="selectedOrder?.detail_url" target="_blank" class="inline-flex items-center gap-2 rounded-xl border border-[#e6e0db] bg-white px-4 py-2 text-sm font-semibold text-[#6d5844] shadow-sm transition hover:-translate-y-0.5 hover:bg-[#f7f2ec] hover:shadow-md">
                    <span class="material-symbols-outlined">visibility</span>
                    Open Full Page
                </a>
                <button @click="window.print()" class="inline-flex items-center gap-2 rounded-xl bg-[#1f2937] px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
                    <span class="material-symbols-outlined text-base">print</span>
                    Print Invoice
                </button>
                <a :href="selectedOrder?.detail_url" target="_blank" class="inline-flex items-center gap-2 rounded-xl bg-[#2563eb] px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
                    <span class="material-symbols-outlined text-base">picture_as_pdf</span>
                    Export PDF
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function managerOrdersPage(preset) {
        return {
            showFilter: false,
            showExport: false,
            showDetail: false,
            loading: true,
            status: preset.status || 'all',
            paymentMethod: preset.paymentMethod || 'all',
            dateFrom: preset.dateFrom || '',
            dateTo: preset.dateTo || '',
            search: preset.search || '',
            selectedOrder: null,
            init() {
                setTimeout(() => this.loading = false, 180);
            },
            applyFilter() {
                const params = new URLSearchParams();
                if (this.status && this.status !== 'all') params.append('status', this.status);
                if (this.paymentMethod && this.paymentMethod !== 'all') params.append('payment_method', this.paymentMethod);
                if (this.search) params.append('search', this.search);
                if (this.dateFrom) params.append('date_from', this.dateFrom);
                if (this.dateTo) params.append('date_to', this.dateTo);
                const query = params.toString();
                window.location.href = '{{ route('manager.orders') }}' + (query ? '?' + query : '');
            },
            exportData(type) {
                const params = new URLSearchParams();
                params.append('export', type);
                if (this.status && this.status !== 'all') params.append('status', this.status);
                if (this.paymentMethod && this.paymentMethod !== 'all') params.append('payment_method', this.paymentMethod);
                if (this.search) params.append('search', this.search);
                if (this.dateFrom) params.append('date_from', this.dateFrom);
                if (this.dateTo) params.append('date_to', this.dateTo);
                const query = params.toString();
                if (type === 'pdf') {
                    window.open('{{ route('manager.orders') }}' + '?' + query, '_blank');
                } else {
                    window.location.href = '{{ route('manager.orders') }}' + '?' + query;
                }
                this.showExport = false;
            },
            openDetail(order) {
                this.selectedOrder = order;
                this.showDetail = true;
                document.body.classList.add('overflow-hidden');
            },
            closeDetail() {
                this.showDetail = false;
                this.selectedOrder = null;
                document.body.classList.remove('overflow-hidden');
            },
            badgeClass(status) {
                const classes = {
                    pending: 'bg-amber-100 text-amber-800 ring-1 ring-amber-200',
                    processing: 'bg-blue-100 text-blue-800 ring-1 ring-blue-200',
                    completed: 'bg-emerald-100 text-emerald-800 ring-1 ring-emerald-200',
                    cancelled: 'bg-rose-100 text-rose-800 ring-1 ring-rose-200',
                };
                return classes[status] || 'bg-[#f7f2ec] text-[#5a4634] ring-1 ring-[#e6e0db]';
            },
            badgeIcon(status) {
                const icons = {
                    pending: 'schedule',
                    processing: 'autorenew',
                    completed: 'check_circle',
                    cancelled: 'cancel',
                };
                return icons[status] || 'info';
            },
            badgeLabel(status) {
                const labels = {
                    pending: 'Pending',
                    processing: 'Processing',
                    completed: 'Completed',
                    cancelled: 'Cancelled',
                };
                return labels[status] || 'Status';
            },
        };
    }
</script>
@endpush
