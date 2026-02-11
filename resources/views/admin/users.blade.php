@extends('layouts.dashboard')

@section('title', 'User Management')

@section('content')
<div class="p-6 space-y-6" x-data="{ showModal: false, editMode: false, currentUser: null }">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#181411] dark:text-white">User Management</h1>
            <p class="text-sm text-[#897561] dark:text-[#a89c92] mt-1">Manage system users and their roles</p>
        </div>
        <button @click="showModal = true; editMode = false; currentUser = null" 
                class="bg-primary text-white px-4 py-2 rounded-lg font-medium hover:bg-primary/90 flex items-center gap-2 transition-colors">
            <span class="material-symbols-outlined text-[20px]">person_add</span>
            <span>Add New User</span>
        </button>
    </div>

    <!-- Users Content -->
    <div class="bg-white dark:bg-[#1a1612] rounded-xl border border-[#e6e0db] dark:border-[#3d362e] overflow-hidden">
        <!-- Table Header -->
        <div class="p-6 border-b border-[#e6e0db] dark:border-[#3d362e]">
            <h2 class="text-lg font-bold text-[#181411] dark:text-white">Staff Members</h2>
        </div>
        
        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-orange-50/50 dark:bg-[#2c241b] border-b border-[#e6e0db] dark:border-[#3d362e]">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-[#897561] uppercase tracking-wider">Staff</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-[#897561] uppercase tracking-wider">Role</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-[#897561] uppercase tracking-wider">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-[#897561] uppercase tracking-wider">Joined</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-[#897561] uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-[#897561] uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e6e0db] dark:divide-[#3d362e]">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 dark:hover:bg-[#2c241b] transition-colors">
                        <!-- Staff Info -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="size-10 rounded-full bg-gradient-to-br from-primary to-primary-dark flex items-center justify-center text-white font-bold">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-bold text-[#181411] dark:text-white">{{ $user->name }}</div>
                                    <div class="text-xs text-[#897561] dark:text-[#a89c92]">ID: {{ $user->id }}</div>
                                </div>
                            </div>
                        </td>

                        <!-- Role -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs font-bold rounded
                                @if($user->role === 'admin') bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400
                                @elseif($user->role === 'manager') bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400
                                @elseif($user->role === 'cashier') bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400
                                @else bg-gray-100 dark:bg-gray-900/30 text-gray-700 dark:text-gray-400
                                @endif">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>

                        <!-- Email -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-[#181411] dark:text-white">{{ $user->email }}</div>
                        </td>

                        <!-- Joined Date -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-[#897561] dark:text-[#a89c92]">{{ $user->created_at->format('M d, Y') }}</div>
                        </td>

                        <!-- Status -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs font-bold rounded bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">
                                Active
                            </span>
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2">
                                <button @click="editMode = true; currentUser = {{ $user->toJson() }}; showModal = true" 
                                        class="p-1.5 text-primary hover:bg-primary/10 rounded-lg transition-colors" title="Edit">
                                    <span class="material-symbols-outlined text-[20px]">edit</span>
                                </button>
                                @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="Delete">
                                        <span class="material-symbols-outlined text-[20px]">delete</span>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <span class="material-symbols-outlined text-6xl text-[#897561]/30">group</span>
                            <p class="text-[#897561] dark:text-[#a89c92] mt-4">No users found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Add/Edit User -->
    <div x-show="showModal" 
         x-cloak
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm"
         @click.self="showModal = false">
        <div class="bg-white dark:bg-[#1a1612] rounded-t-2xl sm:rounded-xl w-full sm:max-w-lg max-h-[95vh] sm:max-h-[90vh] overflow-y-auto">
            <div class="p-4 sm:p-6 border-b border-[#e6e0db] dark:border-[#3d362e] flex items-center justify-between sticky top-0 bg-white dark:bg-[#1a1612] z-10">
                <h3 class="text-lg sm:text-xl font-bold text-[#181411] dark:text-white" x-text="editMode ? 'Edit User' : 'Add New User'"></h3>
                <button @click="showModal = false" class="text-[#897561] hover:text-[#181411] dark:hover:text-white p-2 -mr-2">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            
            <form :action="editMode ? `/admin/users/${currentUser?.id}` : '{{ route('admin.users.store') }}'" method="POST" class="p-4 sm:p-6 space-y-4">
                @csrf
                <template x-if="editMode">
                    @method('PUT')
                </template>
                
                <div>
                    <label class="block text-sm font-medium text-[#181411] dark:text-white mb-2">Full Name</label>
                    <input type="text" name="name" :value="currentUser?.name" required
                           class="w-full px-4 py-3 rounded-lg border border-[#e6e0db] dark:border-[#3d362e] bg-white dark:bg-[#0f0d0b] text-[#181411] dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent text-base">
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#181411] dark:text-white mb-2">Email</label>
                    <input type="email" name="email" :value="currentUser?.email" required
                           class="w-full px-4 py-3 rounded-lg border border-[#e6e0db] dark:border-[#3d362e] bg-white dark:bg-[#0f0d0b] text-[#181411] dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent text-base">
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#181411] dark:text-white mb-2">Role</label>
                    <select name="role" required
                            class="w-full px-4 py-3 rounded-lg border border-[#e6e0db] dark:border-[#3d362e] bg-white dark:bg-[#0f0d0b] text-[#181411] dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent text-base">
                        <option value="admin" :selected="currentUser?.role == 'admin'">Admin</option>
                        <option value="manager" :selected="currentUser?.role == 'manager'">Manager</option>
                        <option value="cashier" :selected="currentUser?.role == 'cashier'">Cashier</option>
                    </select>
                </div>

                <div x-show="!editMode">
                    <label class="block text-sm font-medium text-[#181411] dark:text-white mb-2">Password</label>
                    <input type="password" name="password" :required="!editMode" minlength="8"
                           class="w-full px-4 py-3 rounded-lg border border-[#e6e0db] dark:border-[#3d362e] bg-white dark:bg-[#0f0d0b] text-[#181411] dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent text-base">
                </div>

                <div x-show="editMode">
                    <label class="block text-sm font-medium text-[#181411] dark:text-white mb-2">New Password (optional)</label>
                    <input type="password" name="password" minlength="8"
                           class="w-full px-4 py-3 rounded-lg border border-[#e6e0db] dark:border-[#3d362e] bg-white dark:bg-[#0f0d0b] text-[#181411] dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent text-base">
                    <p class="text-xs text-[#897561] dark:text-[#a89c92] mt-1">Leave empty if you don't want to change the password</p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 pt-4">
                    <button type="button" @click="showModal = false"
                            class="w-full sm:flex-1 px-6 py-3 border border-[#e6e0db] dark:border-[#3d362e] text-[#897561] dark:text-[#a89c92] rounded-lg hover:bg-[#f4f2f0] dark:hover:bg-[#3e2d23] transition-colors font-medium">
                        Cancel
                    </button>
                    <button type="submit"
                            class="w-full sm:flex-1 px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors font-medium">
                        <span x-text="editMode ? 'Update User' : 'Save User'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
     class="fixed bottom-4 right-4 bg-green-500 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg shadow-lg z-50 text-sm sm:text-base">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
     class="fixed bottom-4 right-4 bg-red-500 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg shadow-lg z-50 text-sm sm:text-base">
    {{ session('error') }}
</div>
@endif
@endsection
