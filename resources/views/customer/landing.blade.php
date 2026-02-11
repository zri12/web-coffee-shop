@extends('layouts.customer')

@section('content')
<div class="h-screen flex flex-col items-center justify-between p-8 relative overflow-hidden">
    
    <!-- Background Decor -->
    <div class="absolute top-[-20%] left-[-10%] w-[120%] h-[60%] bg-gradient-to-b from-orange-100/40 to-transparent rounded-b-full blur-3xl -z-10"></div>

    <!-- Top Icons -->
    <div class="w-full flex justify-between items-center text-[#897561] mt-2">
        <button class="p-2 -ml-2">
            <span class="material-symbols-outlined text-2xl">menu</span>
        </button>
        <button class="p-2 -mr-2">
            <span class="material-symbols-outlined text-2xl">help</span>
        </button>
    </div>

    <!-- Center Content -->
    <div class="flex-1 flex flex-col items-center justify-center w-full max-w-xs mx-auto -mt-16 text-center">
        <!-- Hero Circle -->
        <div class="relative w-48 h-48 mb-8">
            <div class="absolute inset-0 rounded-full border border-orange-200"></div>
            <div class="absolute inset-2 rounded-full border border-orange-100"></div>
            <div class="absolute inset-0 flex items-center justify-center bg-white rounded-full shadow-sm">
                <!-- Fallback Icon if Image Fails -->
                <span class="material-symbols-outlined text-action text-[64px]">local_cafe</span>
                <!-- <img src="{{ asset('images/hero-cup.png') }}" alt="Coffee" class="w-24 h-24 object-contain"> -->
            </div>
            
            <!-- Floating Badge -->
            <div class="absolute bottom-2 right-0 bg-[#4a3b32] text-white p-3 rounded-full shadow-lg flex items-center justify-center border-4 border-[#faf8f6]">
                <span class="material-symbols-outlined text-xl">restaurant</span>
            </div>
        </div>

        <!-- Table Pill -->
        <div class="bg-[#f2eadd] text-[#c67c4e] px-6 py-2 rounded-full font-bold text-sm tracking-wide mb-6">
            TABLE NO. {{ $table->table_number }}
        </div>

        <!-- Heading -->
        <h1 class="text-3xl font-bold text-[#2f2d2c] leading-tight mb-4">
            Welcome, please<br>start your order
        </h1>

        <!-- Subtext -->
        <p class="text-[#9b9b9b] text-sm leading-relaxed px-4">
            Discover our new seasonal blends and fresh pastries today.
        </p>
    </div>

    <!-- Bottom Actions -->
    <div class="w-full space-y-6 pb-4">
        <a href="{{ route('customer.menu', $table->table_number) }}" 
           class="block w-full bg-[#c67c4e] text-white font-semibold py-4 rounded-2xl text-center shadow-lg hover:bg-[#b06b3e] transition-all transform active:scale-95">
            Start Ordering <span class="material-symbols-outlined align-middle ml-1 text-sm">arrow_forward</span>
        </a>
        

    </div>
</div>
@endsection
