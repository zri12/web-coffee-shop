@extends('layouts.dashboard')

@section('title', isset($user) ? 'Edit User' : 'Tambah User')
@section('header', isset($user) ? 'Edit User' : 'Tambah User')

@section('content')
<div class="max-w-2xl">
    <div class="card">
        <form action="{{ isset($user) ? route('dashboard.users.update', $user) : route('dashboard.users.store') }}" method="POST">
            @csrf
            @if(isset($user))
                @method('PUT')
            @endif

            <div class="space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" id="name" class="form-input" value="{{ old('name', $user->name ?? '') }}" required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" id="email" class="form-input" value="{{ old('email', $user->email ?? '') }}" required>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select name="role" id="role" class="form-input">
                        <option value="cashier" {{ old('role', $user->role ?? '') == 'cashier' ? 'selected' : '' }}>Cashier</option>
                        <option value="manager" {{ old('role', $user->role ?? '') == 'manager' ? 'selected' : '' }}>Manager</option>
                        <option value="admin" {{ old('role', $user->role ?? '') == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    @error('role')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ isset($user) ? 'Password (Kosongkan jika tidak ingin mengubah)' : 'Password' }}
                    </label>
                    <input type="password" name="password" id="password" class="form-input" {{ isset($user) ? '' : 'required' }}>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-input" {{ isset($user) ? '' : 'required' }}>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('dashboard.users') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg">Batal</a>
                    <button type="submit" class="btn-primary">
                        {{ isset($user) ? 'Simpan Perubahan' : 'Tambah User' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
