@extends('layouts.dashboard')

@section('title', 'Users')
@section('header', 'Kelola Users')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-xl font-bold text-text-main-light dark:text-text-main-dark">Daftar Users</h2>
        <p class="text-sm text-text-sec-light dark:text-text-sec-dark mt-1">Kelola staf dan akses pengguna aplikasi.</p>
    </div>
    <a href="{{ route('dashboard.users.create') }}" class="inline-flex items-center justify-center h-10 px-4 rounded-lg bg-primary hover:bg-primary-hover text-white text-sm font-bold transition-colors shadow-lg shadow-primary/30">
        <span class="material-symbols-outlined mr-2 text-[20px]">add</span>
        Tambah User
    </a>
</div>

<div class="bg-white dark:bg-card-dark rounded-xl border border-[#e6e2de] dark:border-[#3e342b] shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-[#faf9f8] dark:bg-[#251f18] border-b border-[#e6e2de] dark:border-[#3e342b]">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-text-sec-light dark:text-text-sec-dark uppercase tracking-wider">Nama</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-text-sec-light dark:text-text-sec-dark uppercase tracking-wider">Email</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-text-sec-light dark:text-text-sec-dark uppercase tracking-wider">Role</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-text-sec-light dark:text-text-sec-dark uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#e6e2de] dark:divide-[#3e342b]">
                @forelse($users as $user)
                <tr class="hover:bg-[#faf9f8] dark:hover:bg-[#251f18] transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-lg border border-[#e6e2de] dark:border-[#3e342b]">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <span class="font-bold text-text-main-light dark:text-text-main-dark">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-text-sec-light dark:text-text-sec-dark font-medium">{{ $user->email }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold capitalize 
                            {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800 border border-purple-200 dark:bg-purple-900/30 dark:text-purple-300 dark:border-purple-800' : '' }}
                            {{ $user->role === 'manager' ? 'bg-blue-100 text-blue-800 border border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800' : '' }}
                            {{ $user->role === 'cashier' ? 'bg-orange-100 text-orange-800 border border-orange-200 dark:bg-orange-900/30 dark:text-orange-300 dark:border-orange-800' : '' }}">
                            <span class="w-1.5 h-1.5 rounded-full mr-1.5 
                                {{ $user->role === 'admin' ? 'bg-purple-500' : '' }}
                                {{ $user->role === 'manager' ? 'bg-blue-500' : '' }}
                                {{ $user->role === 'cashier' ? 'bg-orange-500' : '' }}"></span>
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('dashboard.users.edit', $user) }}" class="p-2 text-text-sec-light hover:text-primary hover:bg-primary/10 rounded-lg transition-colors" title="Edit">
                                <span class="material-symbols-outlined text-[20px]">edit</span>
                            </a>
                            @if(auth()->id() !== $user->id)
                            <form method="POST" action="{{ route('dashboard.users.destroy', $user) }}" onsubmit="return confirm('Yakin hapus user ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-text-sec-light hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-text-sec-light dark:text-text-sec-dark">
                        <span class="material-symbols-outlined text-4xl mb-2 opacity-50">group</span>
                        <p>Belum ada user yang ditambahkan.</p>
                        <a href="{{ route('dashboard.users.create') }}" class="text-primary hover:underline font-medium text-sm mt-1 inline-block">Tambah User Sekarang</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($users->hasPages())
    <div class="p-4 border-t border-[#e6e2de] dark:border-[#3e342b] bg-[#faf9f8] dark:bg-[#251f18]">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection
