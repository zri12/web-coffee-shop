@extends('layouts.dashboard')

@section('title', 'Profile')

@section('content')
<div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-1">
        <h1 class="text-xl sm:text-2xl font-bold text-[#181411] dark:text-white">Profile</h1>
        <p class="text-xs sm:text-sm text-[#897561] dark:text-[#a89c92]">Kelola informasi profil Anda</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-[#1a1612] rounded-xl border border-[#e6e0db] dark:border-[#3d362e] p-4 sm:p-6 sticky top-6">
                <div class="text-center">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 bg-primary/10 rounded-full flex items-center justify-center text-primary font-bold text-2xl sm:text-3xl mx-auto">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <h2 class="text-lg sm:text-xl font-bold text-[#181411] dark:text-white mt-4">{{ $user->name }}</h2>
                    <p class="text-sm text-[#897561] dark:text-[#a89c92] mt-1 break-all">{{ $user->email }}</p>
                    
                    @php
                        $roleColors = [
                            'admin' => 'bg-purple-50 dark:bg-purple-900/20 text-purple-600',
                            'manager' => 'bg-blue-50 dark:bg-blue-900/20 text-blue-600',
                            'cashier' => 'bg-green-50 dark:bg-green-900/20 text-green-600',
                        ];
                    @endphp
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-xs sm:text-sm font-medium {{ $roleColors[$user->role] ?? '' }} mt-4">
                        {{ ucfirst($user->role) }}
                    </span>

                    <div class="mt-6 pt-6 border-t border-[#e6e0db] dark:border-[#3d362e] text-left space-y-3">
                        <div class="flex items-center gap-3 text-sm">
                            <span class="material-symbols-outlined text-[#897561]">calendar_today</span>
                            <span class="text-[#897561] dark:text-[#a89c92]">Bergabung {{ $user->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm">
                            <span class="material-symbols-outlined text-[#897561]">history</span>
                            <span class="text-[#897561] dark:text-[#a89c92]">Terakhir Update {{ $user->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Forms -->
        <div class="lg:col-span-2 space-y-4 sm:space-y-6">
            <!-- Edit Profile -->
            <div class="bg-white dark:bg-[#1a1612] rounded-xl border border-[#e6e0db] dark:border-[#3d362e] p-4 sm:p-6">
                <h3 class="text-base sm:text-lg font-bold text-[#181411] dark:text-white mb-4 sm:mb-6">Edit Profil</h3>
                
                <form action="{{ route('admin.profile.update') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-medium text-[#181411] dark:text-white mb-2">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ $user->name }}" required
                               class="w-full px-4 py-3 rounded-lg border border-[#e6e0db] dark:border-[#3d362e] bg-white dark:bg-[#0f0d0b] text-[#181411] dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent text-sm sm:text-base">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-[#181411] dark:text-white mb-2">Email</label>
                        <input type="email" name="email" value="{{ $user->email }}" required
                               class="w-full px-4 py-3 rounded-lg border border-[#e6e0db] dark:border-[#3d362e] bg-white dark:bg-[#0f0d0b] text-[#181411] dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent text-sm sm:text-base">
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors font-medium touch-manipulation">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Change Password -->
            <div class="bg-white dark:bg-[#1a1612] rounded-xl border border-[#e6e0db] dark:border-[#3d362e] p-4 sm:p-6">
                <h3 class="text-base sm:text-lg font-bold text-[#181411] dark:text-white mb-4 sm:mb-6">Ubah Password</h3>
                
                <form action="{{ route('admin.profile.password') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-medium text-[#181411] dark:text-white mb-2">Password Lama</label>
                        <input type="password" name="current_password" required
                               class="w-full px-4 py-3 rounded-lg border border-[#e6e0db] dark:border-[#3d362e] bg-white dark:bg-[#0f0d0b] text-[#181411] dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent text-sm sm:text-base">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-[#181411] dark:text-white mb-2">Password Baru</label>
                        <input type="password" name="password" required minlength="8"
                               class="w-full px-4 py-3 rounded-lg border border-[#e6e0db] dark:border-[#3d362e] bg-white dark:bg-[#0f0d0b] text-[#181411] dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent text-sm sm:text-base">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-[#181411] dark:text-white mb-2">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" required minlength="8"
                               class="w-full px-4 py-3 rounded-lg border border-[#e6e0db] dark:border-[#3d362e] bg-white dark:bg-[#0f0d0b] text-[#181411] dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent text-sm sm:text-base">
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors font-medium touch-manipulation">
                            Ubah Password
                        </button>
                    </div>
                </form>
            </div>

            <!-- Activity -->
            <div class="bg-white dark:bg-[#1a1612] rounded-xl border border-[#e6e0db] dark:border-[#3d362e] p-4 sm:p-6">
                <h3 class="text-base sm:text-lg font-bold text-[#181411] dark:text-white mb-4 sm:mb-6">Aktivitas Terakhir</h3>
                
                <div class="space-y-3 sm:space-y-4">
                    <div class="flex items-start gap-3 sm:gap-4 p-3 sm:p-4 bg-[#faf8f6] dark:bg-[#0f0d0b] rounded-lg">
                        <div class="w-10 h-10 bg-green-50 dark:bg-green-900/20 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-green-600">login</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-semibold text-sm sm:text-base text-[#181411] dark:text-white">Login</h4>
                            <p class="text-xs sm:text-sm text-[#897561] dark:text-[#a89c92] mt-0.5">Anda login ke sistem</p>
                            <p class="text-xs text-[#897561] dark:text-[#a89c92] mt-2">{{ now()->diffForHumans() }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 sm:gap-4 p-3 sm:p-4 bg-[#faf8f6] dark:bg-[#0f0d0b] rounded-lg">
                        <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-blue-600">edit</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-semibold text-sm sm:text-base text-[#181411] dark:text-white">Update Profil</h4>
                            <p class="text-xs sm:text-sm text-[#897561] dark:text-[#a89c92] mt-0.5">Anda mengubah informasi profil</p>
                            <p class="text-xs text-[#897561] dark:text-[#a89c92] mt-2">2 hari yang lalu</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
     class="fixed bottom-4 right-4 bg-green-500 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg shadow-lg z-50 text-sm sm:text-base flex items-center gap-2">
    <span class="material-symbols-outlined">check_circle</span>
    {{ session('success') }}
</div>
@endif

@if(session('error') || $errors->any())
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
     class="fixed bottom-4 right-4 bg-red-500 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg shadow-lg z-50 text-sm sm:text-base">
    <div class="flex items-center gap-2">
        <span class="material-symbols-outlined">error</span>
        <span>{{ session('error') ?? 'Terdapat kesalahan pada input Anda.' }}</span>
    </div>
    @if($errors->any())
    <ul class="mt-1 text-xs list-disc list-inside opacity-90">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
    @endif
</div>
@endif
@endsection
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
     class="fixed bottom-4 right-4 bg-red-500 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg shadow-lg z-50 text-sm sm:text-base">
    {{ session('error') }}
</div>
@endif
@endsection
