@extends('layouts.dashboard')

@section('title', 'Reports & Analytics')

@section('content')
<div class="p-6 space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-[#181411] dark:text-white">Reports & Analytics</h1>
            <p class="text-[#897561] text-sm">Detailed performance insights.</p>
        </div>
         <div class="bg-white dark:bg-[#1a1612] border border-[#e6e0db] dark:border-[#3d362e] rounded-lg p-1 flex">
            <a href="{{ route('manager.reports', ['period' => 'daily']) }}" class="px-3 py-1 {{ request('period', 'daily') === 'daily' ? 'bg-gray-100 dark:bg-[#2c241b] font-bold' : 'text-[#5c4d40] hover:bg-gray-50' }} text-sm rounded">Daily</a>
            <a href="{{ route('manager.reports', ['period' => 'weekly']) }}" class="px-3 py-1 {{ request('period') === 'weekly' ? 'bg-gray-100 dark:bg-[#2c241b] font-bold' : 'text-[#5c4d40] hover:bg-gray-50' }} text-sm rounded">Weekly</a>
            <a href="{{ route('manager.reports', ['period' => 'monthly']) }}" class="px-3 py-1 {{ request('period') === 'monthly' ? 'bg-gray-100 dark:bg-[#2c241b] font-bold' : 'text-[#5c4d40] hover:bg-gray-50' }} text-sm rounded">Monthly</a>
        </div>
    </div>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Sales Chart -->
        <div class="bg-white dark:bg-[#1a1612] p-6 rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm">
            <h3 class="font-bold text-lg text-[#181411] dark:text-white mb-4">Revenue Overview</h3>
            <div class="h-64 flex items-end justify-between gap-2">
                @php
                    $maxRev = $maxRevenue > 0 ? $maxRevenue : 1;
                @endphp
                @foreach($dailyRevenue as $day)
                    @php
                        $heightPercent = $day['revenue'] > 0 ? ($day['revenue'] / $maxRev) * 100 : 5;
                        $isToday = $day['date'] === date('Y-m-d');
                    @endphp
                    <div class="w-full {{ $isToday ? 'bg-primary' : 'bg-orange-100 hover:bg-primary' }} rounded-t transition-colors relative group" style="height: {{ $heightPercent }}%">
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 bg-black text-white text-xs px-2 py-1 rounded mb-1 opacity-0 group-hover:opacity-100 whitespace-nowrap z-10">
                            Rp {{ number_format($day['revenue'], 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="flex justify-between mt-2 text-xs text-[#897561]">
                @foreach($dailyRevenue as $day)
                    <span>{{ $day['day'] }}</span>
                @endforeach
            </div>
        </div>

        <!-- Popular items -->
        <div class="bg-white dark:bg-[#1a1612] p-6 rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm">
            <h3 class="font-bold text-lg text-[#181411] dark:text-white mb-4">Category Performance</h3>
            <div class="space-y-4">
                @php
                    $colors = ['primary', 'green-500', 'yellow-500', 'purple-500', 'blue-500', 'red-500', 'pink-500', 'indigo-500'];
                @endphp
                @forelse($categoryPerformance as $index => $category)
                    @php
                        $colorClass = 'bg-' . ($colors[$index % count($colors)] ?? 'gray-500');
                    @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="font-medium text-[#181411] dark:text-white">{{ $category['name'] }}</span>
                            <span class="font-bold">{{ $category['percentage'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="{{ $colorClass }} h-2 rounded-full" style="width: {{ $category['percentage'] }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-[#897561] text-sm">No category data available</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
