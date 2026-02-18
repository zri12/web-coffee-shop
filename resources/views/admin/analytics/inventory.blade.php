@extends('layouts.admin')

@section('title', 'Inventory Analytics')

@section('content')
<div class="min-h-screen bg-white p-6">
    <!-- Header with Coffee Branding -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-4xl font-bold text-[#7B4B3A] mb-2 flex items-center gap-3">
                <span class="material-symbols-outlined text-5xl">analytics</span>
                Inventory Analytics
            </h1>
            <p class="text-gray-600 text-lg">Track ingredient usage, stock trends, and inventory health</p>
        </div>
        
        <!-- Modern Date Range Filter -->
        <div class="flex items-center bg-white/80 backdrop-blur-xl rounded-2xl shadow-lg border border-[#F6E9D7] p-1.5">
            <a href="{{ route('admin.analytics.inventory', ['days' => 7]) }}" 
               class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ $days == 7 ? 'bg-gradient-to-r from-[#7B4B3A] to-[#5a3828] text-white shadow-lg' : 'text-gray-600 hover:bg-[#F6E9D7]/50' }}">
                7 Days
            </a>
            <a href="{{ route('admin.analytics.inventory', ['days' => 30]) }}" 
               class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ $days == 30 ? 'bg-gradient-to-r from-[#7B4B3A] to-[#5a3828] text-white shadow-lg' : 'text-gray-600 hover:bg-[#F6E9D7]/50' }}">
                30 Days
            </a>
            <a href="{{ route('admin.analytics.inventory', ['days' => 90]) }}" 
               class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ $days == 90 ? 'bg-gradient-to-r from-[#7B4B3A] to-[#5a3828] text-white shadow-lg' : 'text-gray-600 hover:bg-[#F6E9D7]/50' }}">
                3 Months
            </a>
        </div>
    </div>

    <!-- Key Metrics with Glassmorphism -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Total Usage -->
        <div class="relative overflow-hidden bg-gradient-to-br from-[#7B4B3A] to-[#5a3828] rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                        <span class="material-symbols-outlined text-3xl">inventory</span>
                    </div>
                    <span class="text-xs font-semibold bg-white/20 backdrop-blur-sm px-3 py-1.5 rounded-full">{{ $days }} days</span>
                </div>
                <p class="text-sm text-white/80 mb-1">Total Usage</p>
                <p class="text-4xl font-bold mb-1">{{ number_format($totalUsage, 0) }}</p>
                <p class="text-xs text-white/70">units of ingredients</p>
            </div>
        </div>

        <!-- Daily Average -->
        <div class="relative overflow-hidden bg-gradient-to-br from-[#EBA83A] to-[#D68910] rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                        <span class="material-symbols-outlined text-3xl">trending_up</span>
                    </div>
                    <span class="text-xs font-semibold bg-white/20 backdrop-blur-sm px-3 py-1.5 rounded-full">Average</span>
                </div>
                <p class="text-sm text-white/80 mb-1">Daily Average</p>
                <p class="text-4xl font-bold mb-1">{{ number_format($avgDailyUsage, 1) }}</p>
                <p class="text-xs text-white/70">units per day</p>
            </div>
        </div>

        <!-- Most Used -->
        <div class="relative overflow-hidden bg-gradient-to-br from-[#4CAF50] to-[#388E3C] rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                        <span class="material-symbols-outlined text-3xl">star</span>
                    </div>
                    <span class="text-xs font-semibold bg-white/20 backdrop-blur-sm px-3 py-1.5 rounded-full">Top</span>
                </div>
                <p class="text-sm text-white/80 mb-1">Most Used Item</p>
                <p class="text-xl font-bold mb-1 truncate" title="{{ $mostUsed['ingredient_name'] ?? 'N/A' }}">
                    {{ $mostUsed['ingredient_name'] ?? 'N/A' }}
                </p>
                @if(isset($mostUsed))
                <p class="text-xs text-white/70">{{ number_format($mostUsed['total_used'], 1) }} {{ $mostUsed['unit'] }} used</p>
                @else
                <p class="text-xs text-white/70">No data</p>
                @endif
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div class="relative overflow-hidden bg-gradient-to-br from-[#E53935] to-[#C62828] rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl {{ $lowStockCount > 0 ? 'animate-pulse' : '' }}">
                        <span class="material-symbols-outlined text-3xl">warning</span>
                    </div>
                    @if($lowStockCount > 0)
                    <span class="flex h-3 w-3 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white/75 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-white"></span>
                    </span>
                    @endif
                </div>
                <p class="text-sm text-white/80 mb-1">Low Stock Items</p>
                <p class="text-4xl font-bold mb-1">{{ $lowStockCount }}</p>
                <p class="text-xs text-white/70">Items below minimum stock</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Daily Usage Chart with Gradient -->
        <div class="bg-white/80 backdrop-blur-xl rounded-2xl border border-[#F6E9D7] shadow-xl p-6 lg:col-span-2">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-[#7B4B3A] flex items-center gap-2">
                    <span class="material-symbols-outlined">show_chart</span>
                    Daily Ingredient Usage
                </h3>
                <span class="text-xs font-semibold text-gray-500 bg-[#F6E9D7] px-3 py-1.5 rounded-full">Last {{ $days }} days</span>
            </div>
            <div class="relative h-72 w-full">
                <canvas id="dailyUsageChart"></canvas>
            </div>
        </div>

        <!-- Category Breakdown Doughnut -->
        <div class="bg-white/80 backdrop-blur-xl rounded-2xl border border-[#F6E9D7] shadow-xl p-6">
            <h3 class="text-xl font-bold text-[#7B4B3A] mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined">donut_small</span>
                Usage by Category
            </h3>
            <div class="relative h-72 w-full flex items-center justify-center">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Consumed Ingredients -->
        <div class="bg-white/80 backdrop-blur-xl rounded-2xl border border-[#F6E9D7] shadow-xl overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-[#7B4B3A] to-[#5a3828] text-white flex items-center justify-between">
                <h3 class="text-lg font-bold flex items-center gap-2">
                    <span class="material-symbols-outlined">leaderboard</span>
                    Top Consumed Ingredients
                </h3>
                <span class="text-xs font-semibold bg-white/20 backdrop-blur-sm px-3 py-1.5 rounded-full">Top 10</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-[#F6E9D7]/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-[#7B4B3A] uppercase tracking-wider">Rank</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-[#7B4B3A] uppercase tracking-wider">Ingredient</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-[#7B4B3A] uppercase tracking-wider">Total Used</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-[#7B4B3A] uppercase tracking-wider">Times</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#F6E9D7]">
                        @forelse(array_slice($usageStats, 0, 10) as $index => $stat)
                        <tr class="hover:bg-[#F6E9D7]/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full font-bold text-sm
                                    {{ $index === 0 ? 'bg-gradient-to-br from-yellow-400 to-yellow-600 text-white' : '' }}
                                    {{ $index === 1 ? 'bg-gradient-to-br from-gray-300 to-gray-500 text-white' : '' }}
                                    {{ $index === 2 ? 'bg-gradient-to-br from-orange-400 to-orange-600 text-white' : '' }}
                                    {{ $index > 2 ? 'bg-[#F6E9D7] text-[#7B4B3A]' : '' }}">
                                    {{ $index + 1 }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-semibold text-gray-900">{{ $stat['ingredient_name'] }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="text-sm font-bold text-[#7B4B3A]">{{ number_format($stat['total_used'], 2) }}</span>
                                <span class="text-xs text-gray-500 ml-1">{{ $stat['unit'] }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-[#EBA83A]/20 text-[#7B4B3A]">
                                    {{ $stat['usage_count'] }}×
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="bg-[#F6E9D7] p-4 rounded-2xl mb-3">
                                        <span class="material-symbols-outlined text-[#7B4B3A] text-4xl">inventory_2</span>
                                    </div>
                                    <p class="text-gray-500 font-medium">No usage data found</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Critical Stock Levels -->
        <div class="bg-white/80 backdrop-blur-xl rounded-2xl border border-[#F6E9D7] shadow-xl overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-[#E53935] to-[#C62828] text-white flex items-center justify-between">
                <h3 class="text-lg font-bold flex items-center gap-2">
                    <span class="material-symbols-outlined">priority_high</span>
                    Critical Stock Levels
                </h3>
                <a href="{{ route('admin.ingredients.index') }}" class="text-xs font-semibold bg-white/20 backdrop-blur-sm px-3 py-1.5 rounded-full hover:bg-white/30 transition-all">
                    Manage Stock →
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-[#F6E9D7]/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-[#7B4B3A] uppercase tracking-wider">Ingredient</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-[#7B4B3A] uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-[#7B4B3A] uppercase tracking-wider">Current / Min</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#F6E9D7]">
                        @php 
                            $criticalItems = $outOfStock->merge($lowStock);
                        @endphp
                        
                        @forelse($criticalItems->take(10) as $item)
                        <tr class="hover:bg-[#F6E9D7]/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-{{ $item->status == 'Habis' ? 'red' : 'yellow' }}-600">
                                        {{ $item->status == 'Habis' ? 'cancel' : 'warning' }}
                                    </span>
                                    <span class="text-sm font-semibold text-gray-900">{{ $item->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold border-2
                                    {{ $item->status == 'Habis' ? 'bg-red-50 text-red-700 border-red-200 animate-pulse' : 'bg-yellow-50 text-yellow-700 border-yellow-200' }}">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="text-sm font-bold text-gray-900">{{ number_format($item->stock, 1) }}</span>
                                <span class="text-xs text-gray-500"> / {{ number_format($item->minimum_stock, 1) }} {{ $item->unit }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="bg-green-50 p-4 rounded-2xl mb-3">
                                        <span class="material-symbols-outlined text-green-600 text-4xl">check_circle</span>
                                    </div>
                                    <p class="text-green-600 font-semibold">All stock levels are healthy!</p>
                                    <p class="text-sm text-gray-500 mt-1">No items below minimum stock</p>
                                </div>
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

    // Daily Usage Chart with Gradient
    const ctxDaily = document.getElementById('dailyUsageChart').getContext('2d');
    const gradientDaily = ctxDaily.createLinearGradient(0, 0, 0, 300);
    gradientDaily.addColorStop(0, 'rgba(123, 75, 58, 0.3)');
    gradientDaily.addColorStop(1, 'rgba(123, 75, 58, 0.0)');

    new Chart(ctxDaily, {
        type: 'line',
        data: {
            labels: dailyData.map(d => {
                const date = new Date(d.date);
                return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            }),
            datasets: [{
                label: 'Total Usage (Units)',
                data: dailyData.map(d => d.total),
                borderColor: '#7B4B3A',
                backgroundColor: gradientDaily,
                tension: 0.4,
                fill: true,
                borderWidth: 3,
                pointRadius: 4,
                pointBackgroundColor: '#7B4B3A',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointHoverRadius: 6,
                pointHoverBackgroundColor: '#EBA83A',
                pointHoverBorderColor: '#fff',
                pointHoverBorderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(123, 75, 58, 0.9)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    padding: 12,
                    borderColor: '#EBA83A',
                    borderWidth: 2,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return 'Usage: ' + context.parsed.y.toFixed(1) + ' units';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(123, 75, 58, 0.1)',
                        drawBorder: false,
                    },
                    ticks: {
                        color: '#7B4B3A',
                        font: {
                            weight: 'bold'
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#7B4B3A',
                        font: {
                            weight: 'bold'
                        }
                    }
                }
            }
        }
    });

    // Category Chart with Coffee Colors
    const ctxCategory = document.getElementById('categoryChart').getContext('2d');
    const categories = Object.keys(categoryData);
    const categoryValues = Object.values(categoryData);
    const coffeeColors = [
        '#7B4B3A', // Coffee Brown
        '#EBA83A', // Warm Orange
        '#D68910', // Dark Orange
        '#4CAF50', // Green
        '#FBC02D', // Yellow
        '#E53935', // Red
        '#8B5A3C', // Light Brown
        '#5a3828', // Dark Brown
        '#F6E9D7', // Cream
        '#64748b'  // Gray
    ];

    new Chart(ctxCategory, {
        type: 'doughnut',
        data: {
            labels: categories,
            datasets: [{
                data: categoryValues,
                backgroundColor: coffeeColors.slice(0, categories.length),
                borderWidth: 3,
                borderColor: '#fff',
                hoverBorderWidth: 4,
                hoverBorderColor: '#EBA83A'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        boxWidth: 15,
                        padding: 15,
                        font: {
                            size: 12,
                            weight: 'bold'
                        },
                        color: '#7B4B3A',
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(123, 75, 58, 0.9)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    padding: 12,
                    borderColor: '#EBA83A',
                    borderWidth: 2,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return label + ': ' + value.toFixed(1) + ' units (' + percentage + '%)';
                        }
                    }
                }
            },
            cutout: '65%'
        }
    });
</script>
@endpush
@endsection
