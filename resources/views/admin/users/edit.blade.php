@extends('layouts.admin')
@section('title', 'EDIT DATA USER')
@section('content')
    <div class="max-w-2xl bg-white rounded-xl shadow-sm border border-slate-100 p-8">
        <form action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-6 mb-8">
                <div>
                    <label class="block text-sm font-semibold text-[#1E293B] mb-2">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ $user->name }}" required class="w-full border-gray-200 rounded-lg focus:ring-[#1E3A8A]">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#1E293B] mb-2">Username</label>
                    <input type="text" name="username" value="{{ $user->username }}" required class="w-full border-gray-200 rounded-lg focus:ring-[#1E3A8A]">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#1E293B] mb-2">Password <span class="text-xs text-slate-400 font-normal">(Kosongkan jika tidak ingin diubah)</span></label>
                    <input type="password" name="password" class="w-full border-gray-200 rounded-lg focus:ring-[#1E3A8A]">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#1E293B] mb-2">Hak Akses (Role)</label>
                    <select name="role" required class="w-full border-gray-200 rounded-lg focus:ring-[#1E3A8A]">
                        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Administrator</option>
                        <option value="pimpinan" {{ $user->role == 'pimpinan' ? 'selected' : '' }}>Pimpinan</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-3">
                <a href="{{ route('users.index') }}" class="px-6 py-2.5 rounded-lg text-slate-600 font-semibold hover:bg-slate-100 transition">Batal</a>
                <button type="submit" class="bg-[#1E3A8A] hover:bg-blue-800 text-white font-bold py-2.5 px-8 rounded-lg transition shadow-md">Update User</button>
            </div>
        </form>
    </div>
@endsection