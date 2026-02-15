@extends('layouts.admin')

@section('title', 'Ingredient History - ' . $ingredient->name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-amber-50 to-orange-50 p-6">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('admin.ingredients.index') }}" class="text-orange-600 hover:text-orange-800 mb-4 inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Back to Ingredients
        </a>
        <h1 class="text-3xl font-bold text-gray-900">{{ $ingredient->name }} - History</h1>
        <p class="text-gray-600">Current Stock: <span class="font-semibold">{{ $ingredient->formatted_stock }}</span></p>
    </div>

    <!-- Timeline -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="space-y-4">
            @forelse($logs as $log)
            <div class="flex gap-4 pb-4 border-b border-gray-200 last:border-0">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $log->type_badge_class }}">
                        <i class="fas {{ $log->type === 'Restock' ? 'fa-plus' : 'fa-minus' }}"></i>
                    </div>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-semibold text-gray-900">{{ $log->type }}</span>
                        <span class="text-sm text-gray-500">{{ $log->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    <p class="text-sm text-gray-600">
                        Amount: <span class="font-semibold {{ $log->change_amount > 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $log->formatted_change }} {{ $ingredient->unit }}
                        </span>
                    </p>
                    @if($log->note)
                    <p class="text-sm text-gray-500 mt-1">Note: {{ $log->note }}</p>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-history text-4xl mb-3 text-gray-300"></i>
                <p>No history found</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
