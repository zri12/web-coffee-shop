@extends('layouts.admin')

@section('title', 'Inventory Analytics')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-amber-50 to-orange-50 p-6">
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Inventory Analytics</h1>
            <p class="text-gray-600">Track ingredient usage, stock trends, and inventory health</p>
        </div>
        
        <!-- Date Range Filter -->
        <div class="flex items-center bg-white rounded-lg shadow-sm p-1">
            <a href="{{ route('admin.analytics.inventory', ['days' => 7]) }}" 
               class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $days == 7 ? 'bg-orange-100 text-orange-700' : 'text-gray-600 hover:bg-gray-50' }}">
                7 Days
            </a>
            <a href="{{ route('admin.analytics.inventory', ['days' => 30]) }}" 
               class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $days == 30 ? 'bg-orange-100 text-orange-700' : 'text-gray-600 hover:bg-gray-50' }}">
                30 Days
            </a>
            <a href="{{ route('admin.analytics.inventory', ['days' => 90]) }}" 
               class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $days == 90 ? 'bg-orange-100 text-orange-700' : 'text-gray-600 hover:bg-gray-50' }}">
                3 Months
            </a>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Total Usage -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
            <p class="text-sm text-gray-600 mb-1">Total Usage ({{ $days }} days)</p>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($totalUsage, 0) }}</p>
            <p class="text-xs text-gray-500 mt-1">units of ingredients</p>
        </div>

        <!-- Daily Average -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
            <p class="text-sm text-gray-600 mb-1">Daily Average</p>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($avgDailyUsage, 1) }}</p>
            <p class="text-xs text-gray-500 mt-1">units per day</p>
        </div>

        <!-- Most Used -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
            <p class="text-sm text-gray-600 mb-1">Most Used Item</p>
            <p class="text-lg font-bold text-gray-900 truncate" title="{{ $mostUsed['ingredient_name'] ?? 'N/A' }}">
                {{ $mostUsed['ingredient_name'] ?? 'N/A' }}
            </p>
            @if(isset($mostUsed))
            <p class="text-xs text-gray-500 mt-1">{{ number_format($mostUsed['total_used'], 1) }} {{ $mostUsed['unit'] }} used</p>
            @else
            <p class="text-xs text-gray-500 mt-1">No data</p>
            @endif
        </div>

        <!-- Low Stock Alert -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-red-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Low Stock Items</p>
                    <p class="text-3xl font-bold text-red-600">{{ $lowStockCount }}</p>
                </div>
                @if($lowStockCount > 0)
                <span class="flex h-3 w-3 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                </span>
                @endif
            </div>
            <p class="text-xs text-gray-500 mt-1">Items below minimum stock</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Daily Usage Chart -->
        <div class="bg-white rounded-xl shadow-sm p-6 lg:col-span-2">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Daily Ingredient Usage</h3>
            <div class="relative h-64 w-full">
                <canvas id="dailyUsageChart"></canvas>
            </div>
        </div>

        <!-- Category Breakdown -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Usage by Category</h3>
            <div class="relative h-64 w-full flex items-center justify-center">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Consumed Ingredients -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900">Top Consumed Ingredients</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ingredient</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Used</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Times Used</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse(array_slice($usageStats, 0, 10) as $stat)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-gray-900">{{ $stat['ingredient_name'] }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="text-sm text-gray-500">{{ number_format($stat['total_used'], 2) }} {{ $stat['unit'] }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="text-sm text-gray-500">{{ $stat['usage_count'] }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-gray-500 text-sm">No usage data found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Critical Stock Levels -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900">Critical Stock Levels</h3>
                <a href="{{ route('admin.ingredients.index') }}" class="text-sm text-orange-600 hover:text-orange-700 font-medium">Manage Stock &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ingredient</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Current / Min</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @php 
                            $criticalItems = $outOfStock->merge($lowStock);
                        @endphp
                        
                        @forelse($criticalItems->take(10) as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-gray-900">{{ $item->name }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->status == 'Habis' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="text-sm text-gray-900 font-medium">{{ number_format($item->stock, 1) }}</span>
                                <span class="text-xs text-gray-500">/ {{ number_format($item->minimum_stock, 1) }} {{ $item->unit }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-check-circle text-green-500 text-3xl mb-2"></i>
                                <p class="text-sm">All stock levels are healthy</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Prepare data for charts
    const dailyData = @json($dailyUsage);
    const categoryData = @json($categoryBreakdown);

    // Daily Usage Chart
    const ctxDaily = document.getElementById('dailyUsageChart').getContext('2d');
    new Chart(ctxDaily, {
        type: 'line',
        data: {
            labels: dailyData.map(d => d.date),
            datasets: [{
                label: 'Total Usage (Units)',
                data: dailyData.map(d => d.total),
                borderColor: '#f97316', // orange-500
                backgroundColor: 'rgba(249, 115, 22, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false,
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Category Chart
    const ctxCategory = document.getElementById('categoryChart').getContext('2d');
    const categories = Object.keys(categoryData);
    const categoryValues = Object.values(categoryData);
    const backgroundColors = [
        '#f97316', '#eab308', '#84cc16', '#22c55e', '#06b6d4', 
        '#3b82f6', '#8b5cf6', '#d946ef', '#f43f5e', '#64748b'
    ];

    new Chart(ctxCategory, {
        type: 'doughnut',
        data: {
            labels: categories,
            datasets: [{
                data: categoryValues,
                backgroundColor: backgroundColors.slice(0, categories.length),
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        boxWidth: 12,
                        padding: 15
                    }
                }
            },
            cutout: '70%'
        }
    });
</script>
@endpush
@endsection
