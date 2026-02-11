@extends('layouts.app')

@section('title', 'Login Staff')

@section('content')
<section class="min-h-[80vh] flex items-center bg-background-light dark:bg-background-dark py-12">
    <div class="max-w-md w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-surface-dark border border-[#f4f2f0] dark:border-[#3E2723] rounded-2xl p-8 shadow-xl animate-slide-up">
            <div class="text-center mb-8">
                <div class="w-16 h-16 mx-auto mb-4 bg-primary/10 rounded-full flex items-center justify-center">
                    <span class="material-symbols-outlined text-3xl text-primary">coffee_maker</span>
                </div>
                <h1 class="text-2xl font-bold text-text-main dark:text-white">Login Staff</h1>
                <p class="text-text-subtle dark:text-gray-400 mt-2">Masuk untuk mengelola pesanan dan menu</p>
            </div>
            
            @if($errors->any())
            <div class="bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-900/20 text-red-700 dark:text-red-400 px-4 py-3 rounded-xl mb-6 flex items-start gap-3">
                <span class="material-symbols-outlined text-xl mt-0.5">error</span>
                <ul class="text-sm list-disc list-inside">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf
                
                <div>
                    <label for="email" class="block text-sm font-bold text-text-main dark:text-white mb-2">Email</label>
                    <div class="relative">
                        <input type="email" id="email" name="email" value="{{ old('email') }}" 
                               class="w-full pl-10 pr-4 py-3 bg-background-light dark:bg-background-dark border border-[#e6e0db] dark:border-[#3E2723] rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" 
                               placeholder="email@cafe.com" required autofocus>
                        <span class="material-symbols-outlined text-text-subtle dark:text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 text-[20px]">mail</span>
                    </div>
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-bold text-text-main dark:text-white mb-2">Password</label>
                    <div class="relative" x-data="{ show: false }">
                        <input :type="show ? 'text' : 'password'" id="password" name="password" 
                               class="w-full pl-10 pr-12 py-3 bg-background-light dark:bg-background-dark border border-[#e6e0db] dark:border-[#3E2723] rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" 
                               placeholder="••••••••" required>
                        <span class="material-symbols-outlined text-text-subtle dark:text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 text-[20px]">lock</span>
                        
                        <button type="button" @click="show = !show" 
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-text-subtle dark:text-gray-400 hover:text-text-main dark:hover:text-white transition-colors">
                            <span x-show="!show" class="material-symbols-outlined text-[20px]">visibility</span>
                            <span x-show="show" class="material-symbols-outlined text-[20px]">visibility_off</span>
                        </button>
                    </div>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                    <label for="remember" class="ml-2 text-sm text-text-subtle dark:text-gray-400">Ingat saya</label>
                </div>
                
                <button type="submit" class="w-full flex items-center justify-center h-12 px-6 rounded-xl bg-primary hover:bg-primary-dark text-white text-base font-bold transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                    Masuk
                    <span class="material-symbols-outlined ml-2 text-[20px]">login</span>
                </button>
            </form>
            
            <div class="mt-8 pt-6 border-t border-[#f4f2f0] dark:border-[#3E2723]">
                <p class="text-center text-xs font-bold text-text-subtle dark:text-gray-500 mb-4 uppercase tracking-wider">Demo Credentials</p>
                <div class="grid grid-cols-3 gap-2">
                    <button onclick="fillLogin('admin@cafe.com', 'password')" class="bg-background-light dark:bg-background-dark border border-[#e6e0db] dark:border-[#3E2723] rounded-lg p-2 text-center hover:bg-gray-100 dark:hover:bg-[#3E2723] transition-colors">
                        <p class="font-bold text-xs text-text-main dark:text-white">Admin</p>
                    </button>
                    <button onclick="fillLogin('cashier@cafe.com', 'password')" class="bg-background-light dark:bg-background-dark border border-[#e6e0db] dark:border-[#3E2723] rounded-lg p-2 text-center hover:bg-gray-100 dark:hover:bg-[#3E2723] transition-colors">
                        <p class="font-bold text-xs text-text-main dark:text-white">Cashier</p>
                    </button>
                    <button onclick="fillLogin('manager@cafe.com', 'password')" class="bg-background-light dark:bg-background-dark border border-[#e6e0db] dark:border-[#3E2723] rounded-lg p-2 text-center hover:bg-gray-100 dark:hover:bg-[#3E2723] transition-colors">
                        <p class="font-bold text-xs text-text-main dark:text-white">Manager</p>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    function fillLogin(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
    }
</script>
<style>
@keyframes slide-up {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-slide-up { animation: slide-up 0.5s ease-out; }
</style>
@endpush
@endsection
