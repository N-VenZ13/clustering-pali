@extends('layouts.admin')
@section('title', 'TAMBAH USER BARU')
@section('content')
    <div class="max-w-2xl bg-white rounded-xl shadow-sm border border-slate-100 p-8">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="space-y-6 mb-8">
                <div>
                    <label class="block text-sm font-semibold text-[#1E293B] mb-2">Nama Lengkap</label>
                    <input type="text" name="name" required class="w-full border-gray-200 rounded-lg focus:ring-[#1E3A8A]">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#1E293B] mb-2">Username</label>
                    <input type="text" name="username" required class="w-full border-gray-200 rounded-lg focus:ring-[#1E3A8A]">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#1E293B] mb-2">Password</label>
                    <input type="password" name="password" required class="w-full border-gray-200 rounded-lg focus:ring-[#1E3A8A]">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#1E293B] mb-2">Hak Akses (Role)</label>
                    <select name="role" required class="w-full border-gray-200 rounded-lg focus:ring-[#1E3A8A]">
                        <option value="admin">Administrator</option>
                        <option value="pimpinan">Pimpinan</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-3">
                <a href="{{ route('users.index') }}" class="px-6 py-2.5 rounded-lg text-slate-600 font-semibold hover:bg-slate-100 transition">Batal</a>
                <button type="submit" class="bg-[#F97316] hover:bg-orange-600 text-white font-bold py-2.5 px-8 rounded-lg transition shadow-md">Simpan User</button>
            </div>
        </form>
    </div>
@endsection