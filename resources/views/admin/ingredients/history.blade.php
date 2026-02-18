@extends('layouts.admin')

@section('title', 'Ingredient History - ' . $ingredient->name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#F6E9D7]/40 via-white to-[#EBA83A]/10 p-6">
    <!-- Header with Coffee Branding -->
    <div class="mb-8">
        <a href="{{ route('admin.ingredients.index') }}" class="inline-flex items-center gap-2 text-[#7B4B3A] hover:text-[#5a3828] font-semibold mb-4 transition-colors group">
            <span class="material-symbols-outlined transition-transform group-hover:-translate-x-1">arrow_back</span>
            Back to Ingredients
        </a>
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-4xl font-bold text-[#7B4B3A] mb-2 flex items-center gap-3">
                    <span class="material-symbols-outlined text-5xl">history</span>
                    {{ $ingredient->name }} - History
                </h1>
                <p class="text-gray-600 text-lg">Track all stock changes and movements</p>
            </div>
            
            <!-- Current Stock Card -->
            <div class="bg-gradient-to-br from-[#7B4B3A] to-[#5a3828] rounded-2xl p-6 text-white shadow-xl min-w-[200px]">
                <p class="text-sm text-white/80 mb-1">Current Stock</p>
                <p class="text-3xl font-bold">{{ $ingredient->formatted_stock }}</p>
                <p class="text-xs text-white/70 mt-1">
                    Min: {{ number_format($ingredient->minimum_stock, 2) }} {{ $ingredient->unit }}
                </p>
            </div>
        </div>
    </div>

    <!-- Modern Timeline -->
    <div class="bg-white/80 backdrop-blur-xl rounded-2xl border border-[#F6E9D7] shadow-xl overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-[#7B4B3A] to-[#5a3828] text-white">
            <h2 class="text-xl font-bold flex items-center gap-2">
                <span class="material-symbols-outlined">timeline</span>
                Stock Movement Timeline
            </h2>
        </div>
        
        <div class="p-6">
            <div class="relative">
                <!-- Timeline Line -->
                <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-gradient-to-b from-[#7B4B3A] via-[#EBA83A] to-transparent"></div>
                
                <div class="space-y-6">
                    @forelse($logs as $log)
                    <div class="relative flex gap-6 group">
                        <!-- Timeline Icon -->
                        <div class="flex-shrink-0 relative z-10">
                            <div class="w-16 h-16 rounded-2xl flex items-center justify-center shadow-lg transition-all duration-300 group-hover:scale-110
                                {{ $log->type === 'Restock' ? 'bg-gradient-to-br from-green-400 to-green-600' : '' }}
                                {{ $log->type === 'Order Deduct' ? 'bg-gradient-to-br from-red-400 to-red-600' : '' }}
                                {{ $log->type === 'Manual Adjustment' ? 'bg-gradient-to-br from-blue-400 to-blue-600' : '' }}
                                {{ !in_array($log->type, ['Restock', 'Order Deduct', 'Manual Adjustment']) ? 'bg-gradient-to-br from-gray-400 to-gray-600' : '' }}">
                                <span class="material-symbols-outlined text-white text-3xl">
                                    {{ $log->type === 'Restock' ? 'add_circle' : '' }}
                                    {{ $log->type === 'Order Deduct' ? 'remove_circle' : '' }}
                                    {{ $log->type === 'Manual Adjustment' ? 'edit' : '' }}
                                    {{ !in_array($log->type, ['Restock', 'Order Deduct', 'Manual Adjustment']) ? 'sync_alt' : '' }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Log Content Card -->
                        <div class="flex-1 bg-white border-2 border-[#F6E9D7] rounded-2xl p-6 shadow-sm hover:shadow-lg hover:border-[#EBA83A] transition-all duration-300">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                        {{ $log->type }}
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                            {{ $log->type === 'Restock' ? 'bg-green-100 text-green-700' : '' }}
                                            {{ $log->type === 'Order Deduct' ? 'bg-red-100 text-red-700' : '' }}
                                            {{ $log->type === 'Manual Adjustment' ? 'bg-blue-100 text-blue-700' : '' }}">
                                            {{ $log->type === 'Restock' ? 'Stock In' : '' }}
                                            {{ $log->type === 'Order Deduct' ? 'Stock Out' : '' }}
                                            {{ $log->type === 'Manual Adjustment' ? 'Adjusted' : 'Changed' }}
                                        </span>
                                    </h3>
                                    <p class="text-sm text-gray-500 flex items-center gap-2 mt-1">
                                        <span class="material-symbols-outlined text-[16px]">schedule</span>
                                        {{ $log->created_at->format('d M Y, H:i') }}
                                        <span class="text-gray-400">•</span>
                                        {{ $log->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                
                                <!-- Change Amount Badge -->
                                <div class="text-right">
                                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl font-bold text-lg
                                        {{ $log->change_amount > 0 ? 'bg-green-50 text-green-700 border-2 border-green-200' : 'bg-red-50 text-red-700 border-2 border-red-200' }}">
                                        <span class="material-symbols-outlined">
                                            {{ $log->change_amount > 0 ? 'trending_up' : 'trending_down' }}
                                        </span>
                                        {{ $log->formatted_change }} {{ $ingredient->unit }}
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Stock: {{ number_format($log->stock_after, 2) }} {{ $ingredient->unit }}
                                    </p>
                                </div>
                            </div>
                            
                            @if($log->note)
                            <div class="mt-4 pt-4 border-t border-[#F6E9D7]">
                                <p class="text-sm text-gray-600 flex items-start gap-2">
                                    <span class="material-symbols-outlined text-[#7B4B3A] text-[18px] mt-0.5">description</span>
                                    <span class="flex-1">{{ $log->note }}</span>
                                </p>
                            </div>
                            @endif
                            
                            @if($log->order_id)
                            <div class="mt-3 pt-3 border-t border-[#F6E9D7]">
                                <a href="{{ route('admin.orders.detail', $log->order_id) }}" 
                                   class="inline-flex items-center gap-1 text-sm text-[#7B4B3A] hover:text-[#EBA83A] font-semibold transition-colors">
                                    <span class="material-symbols-outlined text-[16px]">receipt_long</span>
                                    View Order #{{ $log->order_id }}
                                    <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-16">
                        <div class="bg-[#F6E9D7] p-8 rounded-3xl inline-block mb-4">
                            <span class="material-symbols-outlined text-[#7B4B3A] text-6xl">history</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">No History Found</h3>
                        <p class="text-gray-500">This ingredient has no recorded stock movements yet.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-[#F6E9D7] bg-[#F6E9D7]/30">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection


    <!-- Timeline -->
    <div class="bg-white rounded-xl shadow-sm p-6">
