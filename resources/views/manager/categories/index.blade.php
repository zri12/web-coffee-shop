@extends('layouts.dashboard')

@section('title', 'Categories')

@section('content')
<div class="p-6 space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-[#181411] dark:text-white">Categories</h1>
            <p class="text-[#897561] text-sm">Organize your menu items into categories.</p>
        </div>
        <button class="bg-primary text-white px-4 py-2 rounded-lg font-medium hover:bg-primary/90 flex items-center gap-2 transition-colors">
            <span class="material-symbols-outlined text-[20px]">add</span> Add Category
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Category Card -->
        <div class="bg-white dark:bg-[#1a1612] p-5 rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm hover:border-primary/50 transition-colors group cursor-pointer relative overflow-hidden">
             <div class="absolute top-0 right-0 p-4 opacity-0 group-hover:opacity-100 transition-opacity">
                <button class="text-[#897561] hover:text-primary"><span class="material-symbols-outlined">edit</span></button>
            </div>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-lg bg-orange-100 dark:bg-orange-900/20 flex items-center justify-center text-orange-600">
                    <span class="material-symbols-outlined">coffee</span>
                </div>
                <div>
                    <h3 class="font-bold text-lg text-[#181411] dark:text-white">Coffee</h3>
                    <p class="text-xs text-[#897561]">12 Items</p>
                </div>
            </div>
            <p class="text-sm text-[#5c4d40] dark:text-[#a89c92] line-clamp-2">Hot and cold coffee beverages including espresso based drinks.</p>
        </div>

        <!-- Category Card -->
        <div class="bg-white dark:bg-[#1a1612] p-5 rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm hover:border-primary/50 transition-colors group cursor-pointer relative overflow-hidden">
             <div class="absolute top-0 right-0 p-4 opacity-0 group-hover:opacity-100 transition-opacity">
                <button class="text-[#897561] hover:text-primary"><span class="material-symbols-outlined">edit</span></button>
            </div>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-lg bg-orange-100 dark:bg-orange-900/20 flex items-center justify-center text-orange-600">
                    <span class="material-symbols-outlined">bakery_dining</span>
                </div>
                <div>
                    <h3 class="font-bold text-lg text-[#181411] dark:text-white">Bakery</h3>
                    <p class="text-xs text-[#897561]">8 Items</p>
                </div>
            </div>
            <p class="text-sm text-[#5c4d40] dark:text-[#a89c92] line-clamp-2">Freshly baked pastries, cakes, and bread.</p>
        </div>

        <!-- Category Card -->
        <div class="bg-white dark:bg-[#1a1612] p-5 rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm hover:border-primary/50 transition-colors group cursor-pointer relative overflow-hidden">
             <div class="absolute top-0 right-0 p-4 opacity-0 group-hover:opacity-100 transition-opacity">
                <button class="text-[#897561] hover:text-primary"><span class="material-symbols-outlined">edit</span></button>
            </div>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-lg bg-orange-100 dark:bg-orange-900/20 flex items-center justify-center text-orange-600">
                    <span class="material-symbols-outlined">icecream</span>
                </div>
                <div>
                    <h3 class="font-bold text-lg text-[#181411] dark:text-white">Dessert</h3>
                    <p class="text-xs text-[#897561]">5 Items</p>
                </div>
            </div>
            <p class="text-sm text-[#5c4d40] dark:text-[#a89c92] line-clamp-2">Sweet treats and desserts.</p>
        </div>
    </div>
</div>
@endsection
