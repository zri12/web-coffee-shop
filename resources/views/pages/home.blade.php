@extends('layouts.app')

@section('title', 'Home')

@section('content')
<!-- Hero Section -->
<section class="relative w-full min-h-[700px] lg:min-h-[850px] flex items-center overflow-hidden">
    <!-- Background Image & Overlay -->
    <div class="absolute inset-0 z-0">
        <img class="w-full h-full object-cover object-[center_25%]" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC2TsRmJG7cbr_BudmT1KMc1X4BfCuQH247b6h8w8VUWe0k3xlT5bVYL9edblgPlnusq3gq1U2oocj97x_MOe4Cbv5rFIACkeX2P3pFoz2EFcoPc3KmPuhT0HRFSL1o8A_HCn8K4O7c5CBgWcCeBrtFj0iSdWpI-fIOe-B2fr8gpPALolKwMkv9AwGKPUZkQeMAEUWczul8OaKpzbh3NoIQID5NhccXVPK3dAcgNJZZf_MfrqcqGJrXukFgA2AGjw6xr6rxQxYm3gxt" alt="Cafe Interior">
        <div class="absolute inset-0 bg-black/45"></div>
        <div class="absolute inset-0 hero-gradient"></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 max-w-7xl mx-auto px-6 w-full pt-16 pb-24 lg:pt-20 lg:pb-32 transform lg:-translate-y-12">
        <div class="max-w-3xl">
            <span class="inline-block px-3 py-1 bg-primary text-on-primary text-[10px] font-extrabold uppercase tracking-[0.2em] rounded-full mb-6 text-white">// Vercel Build: 2026-03-27 22:45
5</span>
            <h1 class="text-4xl md:text-5xl lg:text-[4rem] font-black leading-[1.1] tracking-tight text-white mb-8">
                Order Your Coffee <br>Easily from <span class="text-primary italic">Your Table</span>
            </h1>
            <p class="text-xl text-stone-200 mb-10 leading-relaxed max-w-lg">
                Experience the perfect blend of artisan craft and digital convenience. Freshly roasted beans delivered to your table in minutes.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 items-center">
                <a href="{{ route('menu.index') }}" class="w-full sm:w-auto px-10 py-5 bg-primary text-on-primary rounded-full font-bold text-lg shadow-[0_15px_30px_rgba(207,99,23,0.4)] hover:scale-95 transition-all flex items-center justify-center gap-3">
                    <span class="material-symbols-outlined">qr_code_scanner</span>
                    Scan & Order Now
                </a>
                <a href="{{ route('menu.index') }}" class="w-full sm:w-auto px-10 py-5 bg-white text-[#1b130e] border border-stone-200 rounded-full font-bold text-lg shadow-sm hover:bg-stone-50 transition-all flex items-center justify-center">
                    View Full Menu
                </a>
            </div>
        </div>
    </div>
</section>

<!-- How It Works (Bento Inspired) -->
<section class="py-24 bg-surface px-6">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-black text-on-background mb-4">Effortless Perfection</h2>
            <p class="text-on-surface-variant max-w-sm mx-auto font-medium">Three simple steps to your favorite artisanal cup.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Step 1 -->
            <div class="bg-surface-bright p-10 rounded-[2.5rem] shadow-sm border border-outline-variant/50 group hover:shadow-xl transition-all duration-500 hover:-translate-y-1">
                <div class="w-16 h-16 bg-secondary rounded-2xl flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-primary text-3xl">qr_code_scanner</span>
                </div>
                <h3 class="text-2xl font-bold mb-4">1. Scan</h3>
                <p class="text-on-surface-variant leading-relaxed">Simply scan the QR code located on your table to access our digital menu instantly.</p>
            </div>
            <!-- Step 2 -->
            <div class="bg-[#f4e9df] p-10 rounded-[2.5rem] shadow-sm group hover:shadow-xl transition-all duration-500 transform md:-translate-y-4 hover:-translate-y-5">
                <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-primary text-3xl">coffee</span>
                </div>
                <h3 class="text-2xl font-bold mb-4">2. Choose</h3>
                <p class="text-on-surface-variant leading-relaxed">Browse our curated selection of roasts and customize your brew exactly how you like it.</p>
            </div>
            <!-- Step 3 -->
            <div class="bg-surface-bright p-10 rounded-[2.5rem] shadow-sm border border-outline-variant/50 group hover:shadow-xl transition-all duration-500 hover:-translate-y-1">
                <div class="w-16 h-16 bg-secondary rounded-2xl flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-primary text-3xl">contactless</span>
                </div>
                <h3 class="text-2xl font-bold mb-4">3. Pay</h3>
                <p class="text-on-surface-variant leading-relaxed">Secure checkout via your phone. No waiting for the bill, just pure coffee enjoyment.</p>
            </div>
        </div>
    </div>
</section>

<!-- Featured Menu -->
<section class="py-24 bg-surface-container-high px-6 overflow-hidden">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
            <div>
                <span class="text-primary font-bold tracking-widest text-xs uppercase mb-3 block">From Our Barista</span>
                <h2 class="text-4xl md:text-5xl font-black text-on-background">Featured Menu</h2>
            </div>
            <div class="flex gap-4">
                <a href="{{ route('menu.index') }}" class="px-6 py-3 bg-white hover:bg-primary hover:text-white transition-all rounded-full border border-stone-200 font-bold text-sm">
                    Explore All Menu
                </a>
            </div>
        </div>
        
        <div class="flex flex-wrap justify-center gap-x-8 gap-y-12">
            @forelse($featuredMenus as $menu)
            <!-- Item -->
            <div class="group w-full sm:w-[calc(50%-1.5rem)] lg:w-[calc(25%-1.75rem)] max-w-[300px]">
                <div class="relative aspect-[4/5] rounded-[2.5rem] overflow-hidden mb-6 shadow-lg bg-white">
                    <img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" 
                         src="{{ $menu->display_image_url }}" alt="{{ $menu->name }}"
                         onerror="this.onerror=null;this.src='{{ $menu->placeholder_image_url }}'">
                    
                    @if($menu->is_featured)
                    <div class="absolute top-4 right-4 px-3 py-1 bg-white/90 backdrop-blur rounded-full text-xs font-bold text-primary shadow-sm">
                        Best Seller
                    </div>
                    @endif


                </div>
                <div class="mt-4 flex justify-between items-start">
                    <div class="flex-grow pr-4">
                        <h4 class="text-xl font-black text-[#1b130e] dark:text-stone-100 mb-1 leading-tight tracking-tight">{{ $menu->name }}</h4>
                        <p class="text-on-surface-variant font-medium text-sm line-clamp-2 leading-snug">
                            {{ $menu->description ?? 'Rich artisan flavors crafted for your enjoyment.' }}
                        </p>
                    </div>
                    <div class="text-right flex flex-col items-end shrink-0">
                        <span class="text-[11px] font-black uppercase text-primary tracking-widest mb-0.5 opacity-80">Rp</span>
                        <span class="text-2xl font-black text-primary tracking-tighter leading-none">{{ number_format($menu->price, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            @empty
            <!-- Placeholder if empty -->
            <p class="col-span-full text-center text-on-surface-variant">Check back soon for our barista's picks!</p>
            @endforelse
        </div>
    </div>
</section>

<!-- Newsletter / App CTA -->
<section class="py-24 bg-surface px-6 relative overflow-hidden">
    <div class="max-w-7xl mx-auto flex flex-col lg:flex-row items-center gap-16">
        <div class="lg:w-1/2 relative">
            <div class="absolute -top-10 -left-10 w-64 h-64 bg-secondary rounded-full filter blur-3xl opacity-50 z-0"></div>
            <div class="relative z-10 bg-inverse-surface p-8 rounded-[3rem] shadow-2xl">
                <div class="bg-white p-6 rounded-2xl mb-8 flex items-center justify-center">
                    <div class="w-48 h-48 bg-stone-100 rounded-xl flex items-center justify-center border-4 border-stone-200">
                        <span class="material-symbols-outlined text-stone-300 text-9xl">qr_code_2</span>
                    </div>
                </div>
                <h3 class="text-white text-2xl font-bold text-center mb-2">Scan for Exclusive Rewards</h3>
                <p class="text-stone-400 text-center text-sm font-medium">Join the Amber Roast circle for special discounts.</p>
            </div>
        </div>
        <div class="lg:w-1/2">
            <h2 class="text-5xl font-black text-on-background mb-8 leading-tight">Elevate Your Morning Ritual</h2>
            <p class="text-xl text-on-surface-variant mb-10 leading-relaxed">
                Stay connected with our seasonal releases, events, and workshops. Join our community of coffee lovers and experience the craft.
            </p>
            <form action="#" class="flex flex-col sm:flex-row gap-4">
                <input class="flex-1 px-8 py-5 rounded-full border border-outline-variant bg-surface-bright focus:ring-primary focus:border-primary outline-none" placeholder="Enter your email" type="email">
                <button class="px-10 py-5 bg-primary text-on-primary rounded-full font-bold shadow-lg hover:bg-on-primary-fixed transition-all active:scale-95">Subscribe</button>
            </form>
        </div>
    </div>
</section>


@push('scripts')
<script>
// Simple cart functionality (qty=1 per add, no merging)
function addToCart(id, name, price) {
    if (window.Cart && typeof window.Cart.add === 'function') {
        window.Cart.add(id, name, price, null, 1, { type: 'beverage' });
        return;
    }

    // Fallback only if global cart is unavailable
    let cart = JSON.parse(localStorage.getItem('cart') || '[]');
    cart.push({ id, name, price, quantity: 1, finalPrice: price, cartItemId: Date.now() + Math.random() });
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartBadge();
    showToast('Added to cart!');
}

function updateCartBadge() {
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');
    const badge = document.getElementById('cart-badge');
    // Each item is qty=1, so count = array length
    const totalItems = cart.length;
    
    if (totalItems > 0) {
        badge.classList.remove('hidden');
    } else {
        badge.classList.add('hidden');
    }
}

function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'fixed bottom-4 right-4 z-50 bg-primary text-white px-6 py-3 rounded-xl shadow-lg animate-fade-in';
    toast.innerHTML = `<div class="flex items-center gap-2"><span class="material-symbols-outlined">check_circle</span><span>${message}</span></div>`;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateCartBadge();
});
</script>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in { animation: fade-in 0.3s ease-out; }
</style>
@endpush
@endsection
