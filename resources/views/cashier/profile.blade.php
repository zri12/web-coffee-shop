@extends('layouts.dashboard')

@section('title', 'Profile')

@section('content')
<div class="p-6 space-y-6" x-data="profileManager()">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#181411] dark:text-white">My Profile</h1>
            <p class="text-sm text-[#897561] dark:text-[#a89c92] mt-1">Manage your account settings</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-[#2d2115] rounded-xl border border-[#f4f2f0] dark:border-[#3e2d23] overflow-hidden">
                <!-- Cover -->
                <div class="h-24 bg-gradient-to-r from-primary to-primary-dark"></div>
                
                <!-- Avatar & Info -->
                <div class="px-6 pb-6">
                    <div class="flex flex-col items-center -mt-12">
                        <!-- Avatar -->
                        <div class="size-24 rounded-full bg-white dark:bg-[#2d2115] p-2 shadow-lg">
                            <div class="size-full rounded-full bg-gradient-to-br from-primary to-primary-dark flex items-center justify-center text-white text-3xl font-bold">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        </div>
                        
                        <!-- Name & Role -->
                        <h2 class="mt-4 text-xl font-bold text-[#181411] dark:text-white text-center">{{ auth()->user()->name }}</h2>
                        <span class="mt-2 px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400">
                            {{ ucfirst(auth()->user()->role) }}
                        </span>
                        
                        <!-- Stats -->
                        <div class="w-full mt-6 pt-6 border-t border-[#f4f2f0] dark:border-[#3e2d23]">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="text-center">
                                    <p class="text-2xl font-bold text-[#181411] dark:text-white">{{ floor(auth()->user()->created_at->diffInDays(now())) }}</p>
                                    <p class="text-xs text-[#897561] dark:text-[#a89c92] mt-1">Days Active</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-2xl font-bold text-[#181411] dark:text-white">{{ auth()->user()->created_at->format('Y') }}</p>
                                    <p class="text-xs text-[#897561] dark:text-[#a89c92] mt-1">Joined Year</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Info -->
            <div class="mt-6 bg-white dark:bg-[#2d2115] rounded-xl border border-[#f4f2f0] dark:border-[#3e2d23] p-6">
                <h3 class="text-sm font-bold text-[#181411] dark:text-white mb-4">Account Information</h3>
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[#897561] dark:text-[#a89c92] text-[20px]">badge</span>
                        <div class="flex-1">
                            <p class="text-xs text-[#897561] dark:text-[#a89c92]">User ID</p>
                            <p class="text-sm font-medium text-[#181411] dark:text-white">#{{ auth()->user()->id }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[#897561] dark:text-[#a89c92] text-[20px]">calendar_today</span>
                        <div class="flex-1">
                            <p class="text-xs text-[#897561] dark:text-[#a89c92]">Member Since</p>
                            <p class="text-sm font-medium text-[#181411] dark:text-white">{{ auth()->user()->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[#897561] dark:text-[#a89c92] text-[20px]">schedule</span>
                        <div class="flex-1">
                            <p class="text-xs text-[#897561] dark:text-[#a89c92]">Last Updated</p>
                            <p class="text-sm font-medium text-[#181411] dark:text-white">{{ auth()->user()->updated_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Settings -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Personal Information -->
            <div class="bg-white dark:bg-[#2d2115] rounded-xl border border-[#f4f2f0] dark:border-[#3e2d23] overflow-hidden">
                <div class="p-6 border-b border-[#f4f2f0] dark:border-[#3e2d23]">
                    <h3 class="text-lg font-bold text-[#181411] dark:text-white">Personal Information</h3>
                    <p class="text-sm text-[#897561] dark:text-[#a89c92] mt-1">Update your personal details</p>
                </div>
                
                <form @submit.prevent="updateProfile()" class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-[#181411] dark:text-white mb-2">Full Name</label>
                            <input 
                                type="text" 
                                x-model="formData.name"
                                class="w-full px-4 py-2 bg-[#f4f2f0] dark:bg-[#221910] border border-transparent focus:border-primary rounded-lg text-[#181411] dark:text-white"
                                placeholder="Enter your name">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-[#181411] dark:text-white mb-2">Email Address</label>
                            <input 
                                type="email" 
                                x-model="formData.email"
                                class="w-full px-4 py-2 bg-[#f4f2f0] dark:bg-[#221910] border border-transparent focus:border-primary rounded-lg text-[#181411] dark:text-white"
                                placeholder="Enter your email">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-[#181411] dark:text-white mb-2">Role</label>
                        <input 
                            type="text" 
                            value="{{ ucfirst(auth()->user()->role) }}"
                            disabled
                            class="w-full px-4 py-2 bg-[#e8e4df] dark:bg-[#1a1410] border border-transparent rounded-lg text-[#897561] dark:text-[#a89c92] cursor-not-allowed">
                        <p class="text-xs text-[#897561] dark:text-[#a89c92] mt-1">Role cannot be changed</p>
                    </div>

                    <!-- Success/Error Messages -->
                    <div x-show="successMessage" class="p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                        <p class="text-sm text-green-600 dark:text-green-400" x-text="successMessage"></p>
                    </div>
                    
                    <div x-show="errorMessage" class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                        <p class="text-sm text-red-600 dark:text-red-400" x-text="errorMessage"></p>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors font-medium">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Change Password -->
            <div class="bg-white dark:bg-[#2d2115] rounded-xl border border-[#f4f2f0] dark:border-[#3e2d23] overflow-hidden">
                <div class="p-6 border-b border-[#f4f2f0] dark:border-[#3e2d23]">
                    <h3 class="text-lg font-bold text-[#181411] dark:text-white">Change Password</h3>
                    <p class="text-sm text-[#897561] dark:text-[#a89c92] mt-1">Update your password to keep your account secure</p>
                </div>
                
                <form @submit.prevent="updatePassword()" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-[#181411] dark:text-white mb-2">Current Password</label>
                        <input 
                            type="password" 
                            x-model="passwordData.current_password"
                            class="w-full px-4 py-2 bg-[#f4f2f0] dark:bg-[#221910] border border-transparent focus:border-primary rounded-lg text-[#181411] dark:text-white"
                            placeholder="Enter current password">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-[#181411] dark:text-white mb-2">New Password</label>
                            <input 
                                type="password" 
                                x-model="passwordData.new_password"
                                class="w-full px-4 py-2 bg-[#f4f2f0] dark:bg-[#221910] border border-transparent focus:border-primary rounded-lg text-[#181411] dark:text-white"
                                placeholder="Enter new password">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-[#181411] dark:text-white mb-2">Confirm Password</label>
                            <input 
                                type="password" 
                                x-model="passwordData.confirm_password"
                                class="w-full px-4 py-2 bg-[#f4f2f0] dark:bg-[#221910] border border-transparent focus:border-primary rounded-lg text-[#181411] dark:text-white"
                                placeholder="Confirm new password">
                        </div>
                    </div>

                    <!-- Password Messages -->
                    <div x-show="passwordSuccess" class="p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                        <p class="text-sm text-green-600 dark:text-green-400" x-text="passwordSuccess"></p>
                    </div>
                    
                    <div x-show="passwordError" class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                        <p class="text-sm text-red-600 dark:text-red-400" x-text="passwordError"></p>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors font-medium">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function profileManager() {
    return {
        formData: {
            name: '{{ auth()->user()->name }}',
            email: '{{ auth()->user()->email }}'
        },
        passwordData: {
            current_password: '',
            new_password: '',
            confirm_password: ''
        },
        successMessage: '',
        errorMessage: '',
        passwordSuccess: '',
        passwordError: '',

        async updateProfile() {
            this.successMessage = '';
            this.errorMessage = '';

            try {
                const response = await fetch('/cashier/profile/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.formData)
                });

                const data = await response.json();

                if (data.success) {
                    this.successMessage = 'Profile updated successfully!';
                    setTimeout(() => this.successMessage = '', 3000);
                } else {
                    this.errorMessage = data.message || 'Failed to update profile';
                    if (data.errors) {
                        this.errorMessage += ': ' + Object.values(data.errors).flat().join(', ');
                    }
                }
            } catch (error) {
                console.error('Profile update error:', error);
                this.errorMessage = 'An error occurred. Please try again.';
            }
        },

        async updatePassword() {
            this.passwordSuccess = '';
            this.passwordError = '';

            if (this.passwordData.new_password !== this.passwordData.confirm_password) {
                this.passwordError = 'Passwords do not match';
                return;
            }

            try {
                const response = await fetch('/cashier/profile/password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        current_password: this.passwordData.current_password,
                        new_password: this.passwordData.new_password,
                        new_password_confirmation: this.passwordData.confirm_password
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.passwordSuccess = 'Password updated successfully!';
                    this.passwordData = {
                        current_password: '',
                        new_password: '',
                        confirm_password: ''
                    };
                    setTimeout(() => this.passwordSuccess = '', 3000);
                } else {
                    this.passwordError = data.message || 'Failed to update password';
                    if (data.errors) {
                        this.passwordError += ': ' + Object.values(data.errors).flat().join(', ');
                    }
                }
            } catch (error) {
                console.error('Password update error:', error);
                this.passwordError = 'An error occurred. Please try again.';
            }
        }
    }
}
</script>
@endpush
@endsection
