@extends('layouts.app')

@section('title', 'Home')

@section('content')
<!-- Hero Section -->
<section class="relative w-full">
    <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-20">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <!-- Text Content -->
            <div class="flex flex-col gap-6 order-2 lg:order-1">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black leading-[1.1] text-text-main dark:text-white tracking-tight">
                    Pesan Mudah dari <br><span class="text-primary">Meja Anda</span>
                </h1>
                <p class="text-lg text-text-subtle dark:text-gray-400 max-w-lg leading-relaxed">
                    Lewati antrian. Kopi segar dan pastry artisan diantar langsung ke tempat duduk Anda. Scan, pesan, dan nikmati.
                </p>
                <div class="flex flex-wrap gap-4 pt-2">
                    <a href="{{ route('menu.index') }}" class="flex items-center justify-center h-12 px-8 rounded-full bg-primary hover:bg-primary-dark text-white text-base font-bold transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                        Mulai Pesan
                    </a>
                    <a href="{{ route('menu.index') }}" class="flex items-center justify-center h-12 px-8 rounded-full border-2 border-[#e6e0db] dark:border-surface-dark hover:border-primary text-text-main dark:text-white text-base font-bold transition-colors bg-transparent">
                        Lihat Menu
                    </a>
                </div>
            </div>
            
            <!-- Hero Image -->
            <div class="relative order-1 lg:order-2">
                <div class="aspect-[4/3] rounded-2xl overflow-hidden shadow-2xl relative group">
                    <!-- Decorative gradient overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent z-10"></div>
                    <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuC0wbxu_-0SKHhGfZWylXTdXiRVlZjyBtw0ChR8DRc6glkIcuoEmCPUhfvp4oCw7EICpO7PB62wYxSi3cgZfQCvRgo8IT5wzwZK45wyN6HWj1oD55F25BzlxfTIFpKFMkrwpijCmUORphBqJ5Mh1eU9Bihj-zLROzUwi88_PDoMPWUvonmFSnIhOO8Pm0RG9_mwGbpFHEi_UVs5Gag4nO8rFMfwb1rRtR1y8rRpp_UsSmypq-BYRLmjt2ZeBMh3DYdTM3_wlX80_gbn" 
                         alt="Cozy cafe interior with latte art on wooden table" 
                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                </div>
                
                <!-- Floating Badge -->
                <div class="absolute -bottom-6 -left-6 z-20 hidden md:flex bg-white dark:bg-surface-dark p-4 rounded-xl shadow-xl border border-[#f4f2f0] dark:border-none items-center gap-3">
                    <div class="bg-green-100 text-green-700 p-2 rounded-full">
                        <span class="material-symbols-outlined">eco</span>
                    </div>
                    <div>
                        <p class="text-xs text-text-subtle uppercase font-bold tracking-wider">Biji Pilihan</p>
                        <p class="text-sm font-bold text-text-main dark:text-white">100% Kopi Nusantara</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How it Works Section -->
<section class="w-full bg-white dark:bg-[#1a120b] py-20 border-y border-[#f4f2f0] dark:border-[#3E2723]">
    <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="text-primary font-bold text-sm tracking-widest uppercase mb-2 block">Proses Mudah</span>
            <h2 class="text-3xl md:text-4xl font-bold text-text-main dark:text-white mb-4">Cara Kerja</h2>
            <p class="text-text-subtle dark:text-gray-400 max-w-xl mx-auto">Nikmati pengalaman makan yang mulus dalam tiga langkah sederhana.</p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
            <!-- Step 1 -->
            <div class="group flex flex-col items-center text-center p-6 rounded-2xl hover:bg-background-light dark:hover:bg-surface-dark transition-colors duration-300">
                <div class="size-20 bg-[#FDFBF7] dark:bg-surface-dark rounded-full flex items-center justify-center mb-6 group-hover:bg-primary/10 transition-colors">
                    <span class="material-symbols-outlined text-4xl text-primary">qr_code_scanner</span>
                </div>
                <h3 class="text-xl font-bold text-text-main dark:text-white mb-3">Scan QR Code</h3>
                <p class="text-text-subtle dark:text-gray-400">Temukan QR code unik di meja Anda untuk akses menu digital kami.</p>
            </div>
            
            <!-- Step 2 -->
            <div class="group flex flex-col items-center text-center p-6 rounded-2xl hover:bg-background-light dark:hover:bg-surface-dark transition-colors duration-300">
                <div class="size-20 bg-[#FDFBF7] dark:bg-surface-dark rounded-full flex items-center justify-center mb-6 group-hover:bg-primary/10 transition-colors">
                    <span class="material-symbols-outlined text-4xl text-primary">restaurant_menu</span>
                </div>
                <h3 class="text-xl font-bold text-text-main dark:text-white mb-3">Pilih Menu</h3>
                <p class="text-text-subtle dark:text-gray-400">Pilih dari berbagai kopi artisan, pastry segar, dan makanan lezat.</p>
            </div>
            
            <!-- Step 3 -->
            <div class="group flex flex-col items-center text-center p-6 rounded-2xl hover:bg-background-light dark:hover:bg-surface-dark transition-colors duration-300">
                <div class="size-20 bg-[#FDFBF7] dark:bg-surface-dark rounded-full flex items-center justify-center mb-6 group-hover:bg-primary/10 transition-colors">
                    <span class="material-symbols-outlined text-4xl text-primary">credit_card</span>
                </div>
                <h3 class="text-xl font-bold text-text-main dark:text-white mb-3">Checkout Cepat</h3>
                <p class="text-text-subtle dark:text-gray-400">Bayar dan pesanan diantar langsung ke meja Anda.</p>
            </div>
        </div>
    </div>
</section>

<!-- Featured Items Grid -->
<section class="w-full py-20 bg-background-light dark:bg-background-dark">
    <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-end mb-10">
            <div>
                <h2 class="text-3xl font-bold text-text-main dark:text-white">Menu Favorit</h2>
                <p class="text-text-subtle dark:text-gray-400 mt-2">Pilihan favorit pelanggan kami.</p>
            </div>
            <a href="{{ route('menu.index') }}" class="hidden sm:flex items-center gap-1 text-primary font-bold hover:text-primary-dark transition-colors">
                Lihat Semua <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($featuredMenus as $menu)
            <!-- Menu Card -->
            <div class="bg-white dark:bg-surface-dark rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow group flex flex-col h-full border border-[#f4f2f0] dark:border-[#3E2723]">
                <div class="aspect-[4/3] overflow-hidden relative bg-gradient-to-br from-primary/10 to-primary/5">
                    @if($menu->image)
                    <img src="{{ asset('images/menus/' . $menu->image) }}" 
                         alt="{{ $menu->name }}" 
                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    @else
                    <div class="w-full h-full flex items-center justify-center">
                        <span class="material-symbols-outlined text-6xl text-primary/30">coffee</span>
                    </div>
                    @endif
                    <button onclick="addToCart({{ $menu->id }}, '{{ $menu->name }}', {{ $menu->price }})" 
                            class="absolute bottom-3 right-3 size-10 bg-white/90 dark:bg-black/60 rounded-full flex items-center justify-center hover:bg-primary hover:text-white transition-colors shadow-sm text-text-main dark:text-white">
                        <span class="material-symbols-outlined">add</span>
                    </button>
                </div>
                <div class="p-4 flex flex-col flex-1">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-bold text-text-main dark:text-white text-lg">{{ $menu->name }}</h3>
                        <span class="font-bold text-primary">{{ $menu->formatted_price }}</span>
                    </div>
                    <p class="text-sm text-text-subtle dark:text-gray-400 line-clamp-2">{{ $menu->description ?? 'Minuman lezat dengan cita rasa premium.' }}</p>
                </div>
            </div>
            @empty
            <!-- Placeholder Cards when no menus -->
            <div class="bg-white dark:bg-surface-dark rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow group flex flex-col h-full border border-[#f4f2f0] dark:border-[#3E2723]">
                <div class="aspect-[4/3] overflow-hidden relative">
                    <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuBLq8VwP5OqsrRuk_kYuvhNbBzx5UoNLxVKRYhAVKMk8KFzM3EaaSPFKR_6v690LB9h_ICXe4r-GLRa5PQU3MFYjAOwoETa6gwWu8KH-f2cVjMe_d5uNU1kk81fZ8NmySjHw0Q5LU1sFmggDoZ25TaCojlzPK3irjUw1sq59HuZBXrMWyl0Lo4IrOJjiGBWyKsGrxQSzgwEZrh5UTJZArRZr-BSFOxM8zM1QXpFNJWlYb61MqK65WyqgScI_SLXeI_c39OmNxHhJP94" 
                         alt="Caramel Macchiato" 
                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    <button class="absolute bottom-3 right-3 size-10 bg-white/90 dark:bg-black/60 rounded-full flex items-center justify-center hover:bg-primary hover:text-white transition-colors shadow-sm text-text-main dark:text-white">
                        <span class="material-symbols-outlined">add</span>
                    </button>
                </div>
                <div class="p-4 flex flex-col flex-1">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-bold text-text-main dark:text-white text-lg">Caramel Macchiato</h3>
                        <span class="font-bold text-primary">Rp 45.000</span>
                    </div>
                    <p class="text-sm text-text-subtle dark:text-gray-400 line-clamp-2">Espresso kaya rasa dengan sirup vanilla, susu steamed, dan drizzle karamel.</p>
                </div>
            </div>
            
            <div class="bg-white dark:bg-surface-dark rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow group flex flex-col h-full border border-[#f4f2f0] dark:border-[#3E2723]">
                <div class="aspect-[4/3] overflow-hidden relative">
                    <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDgK5VZiy5Pc6GBtL_Vt4sFL6xvNUpKiWsROMwlMIxwqxAgt1ELEW7jnLDbVpIcBhJWWULWsbDx3T32QHRE5FwNoZxgaKAExDv5LVALMw5Ruwlt82Jn4LKtRSkLzjy-XjJ5VxvJ3-NRItliCEqjZAFQuk5aHwwZiICkv0qv8wH8BlGVvCcRTDXsafmpOnatWbjGDSmhUBzR2ZoP-ZGwhMVHaClIU5IbMdT8zcPWakjyjeRHumwViOgQGv6kZyN4B7BCU5lfSHH8mMbo" 
                         alt="Avocado Toast" 
                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    <button class="absolute bottom-3 right-3 size-10 bg-white/90 dark:bg-black/60 rounded-full flex items-center justify-center hover:bg-primary hover:text-white transition-colors shadow-sm text-text-main dark:text-white">
                        <span class="material-symbols-outlined">add</span>
                    </button>
                </div>
                <div class="p-4 flex flex-col flex-1">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-bold text-text-main dark:text-white text-lg">Avocado Toast</h3>
                        <span class="font-bold text-primary">Rp 55.000</span>
                    </div>
                    <p class="text-sm text-text-subtle dark:text-gray-400 line-clamp-2">Roti sourdough dengan alpukat hancur, chili flakes, dan microgreens.</p>
                </div>
            </div>
            
            <div class="bg-white dark:bg-surface-dark rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow group flex flex-col h-full border border-[#f4f2f0] dark:border-[#3E2723]">
                <div class="aspect-[4/3] overflow-hidden relative">
                    <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDTf7rAJzKPLtNdzp2GJEUu6KQvGdUAhDGZWtkdKyQPvvviWTLOmO9nt1vyTufGrrk9JjZrJJdZCVelyyz0SLUHeP7hW5O_35U2cYm9160F5s9dO5H4om5d2QX_OBt2PYo52VWGT-CJGkst26gN0lIjB41SgznCboJPN4_0Xh3HB29VER6hl0RkCztSpaZ6htspbP_ChCWl5Oo1Lu4mvQyXriwlIbHTTXnpp7TMy2w6ogEksLZgqdPSCN0kQ7AL990u-BnxpsHK8XxG" 
                         alt="Blueberry Scone" 
                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    <button class="absolute bottom-3 right-3 size-10 bg-white/90 dark:bg-black/60 rounded-full flex items-center justify-center hover:bg-primary hover:text-white transition-colors shadow-sm text-text-main dark:text-white">
                        <span class="material-symbols-outlined">add</span>
                    </button>
                </div>
                <div class="p-4 flex flex-col flex-1">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-bold text-text-main dark:text-white text-lg">Blueberry Scone</h3>
                        <span class="font-bold text-primary">Rp 35.000</span>
                    </div>
                    <p class="text-sm text-text-subtle dark:text-gray-400 line-clamp-2">Scone buttery dan flaky dengan blueberry segar dan taburan gula.</p>
                </div>
            </div>
            
            <div class="bg-white dark:bg-surface-dark rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow group flex flex-col h-full border border-[#f4f2f0] dark:border-[#3E2723]">
                <div class="aspect-[4/3] overflow-hidden relative">
                    <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDk8A2yGc4Pgm65z7zJRBUBYZdkSShHq1TPDi6Rr5NpXesGoqjsL1MAACLNJ25wGgTHf1IPCFtkBVMo3WsFOgzKcj-2IZ28Q5kEDyLzutqyt2PymGWDV0J9CxBiUp1-ncVlYxYBDUdofQQ3bR1fJo0lNMVLc9dz2_xIFJMhrrhMsxWxRCeBQfaiVweeIYgpKYEm6BwSBczY25V-wUvJscdVsk1WH-t5TltqSzhHdIggUz9iqn1DU8uHKMkvzyPeJdf67CSSAOs2k2Nh" 
                         alt="Pour Over Coffee" 
                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    <button class="absolute bottom-3 right-3 size-10 bg-white/90 dark:bg-black/60 rounded-full flex items-center justify-center hover:bg-primary hover:text-white transition-colors shadow-sm text-text-main dark:text-white">
                        <span class="material-symbols-outlined">add</span>
                    </button>
                </div>
                <div class="p-4 flex flex-col flex-1">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-bold text-text-main dark:text-white text-lg">Pour Over</h3>
                        <span class="font-bold text-primary">Rp 38.000</span>
                    </div>
                    <p class="text-sm text-text-subtle dark:text-gray-400 line-clamp-2">Biji single-origin diseduh perlahan untuk rasa yang bersih dan kompleks.</p>
                </div>
            </div>
            @endforelse
        </div>
        
        <div class="mt-8 flex justify-center sm:hidden">
            <a href="{{ route('menu.index') }}" class="w-full max-w-xs flex items-center justify-center h-12 px-6 rounded-lg border border-[#e6e0db] dark:border-surface-dark text-text-main dark:text-white text-sm font-bold bg-white dark:bg-surface-dark">
                Lihat Semua Menu
            </a>
        </div>
    </div>
</section>

@push('scripts')
<script>
// Simple cart functionality (qty=1 per add, no merging)
function addToCart(id, name, price) {
    let cart = JSON.parse(localStorage.getItem('cart') || '[]');
    
    // Always add as new item with unique ID (no merging)
    cart.push({ 
        id, 
        name, 
        price, 
        quantity: 1,
        finalPrice: price,
        type: 'beverage',
        options: {}, // No options for quick add
        cartItemId: Date.now() + Math.random()
    });
    
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
