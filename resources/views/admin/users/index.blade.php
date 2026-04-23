@extends('layouts.admin')

@section('title', 'DATA USER')

@section('content')
    <!-- Action Bar -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div class="relative w-full md:w-1/3">
            <svg class="w-5 h-5 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input type="text" placeholder="Cari nama pengguna..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-[#1E3A8A] focus:border-[#1E3A8A]">
        </div>
        <a href="{{ route('users.create') }}" class="w-full md:w-auto bg-[#F97316] hover:bg-orange-600 text-white font-semibold py-2 px-6 rounded-lg flex items-center justify-center gap-2 transition shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah User
        </a>
    </div>

    <!-- User List Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        
        @foreach($users as $user)
            <div class="px-6 py-4 border-b border-slate-100 last:border-0 flex items-center justify-between hover:bg-slate-50 transition">
                
                <!-- Info User & Role Badge -->
                <div class="flex items-center gap-4">
                    <h3 class="text-base font-bold text-[#1E293B]">{{ $user->name }}</h3>
                    
                    @if($user->role === 'admin')
                        <span class="px-3 py-1 bg-blue-50 text-blue-600 text-xs font-semibold rounded-md border border-blue-100">Admin</span>
                    @else
                        <span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-xs font-semibold rounded-md border border-emerald-100">Pimpinan</span>
                    @endif
                </div>
                
                <!-- Ikon Aksi -->
                <div class="flex items-center gap-4">
                    <a href="{{ route('users.edit', $user->id) }}" class="text-slate-400 hover:text-[#1E3A8A]" title="Edit User">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    </a>

                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Hapus user ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-slate-400 hover:text-red-500" title="Hapus User">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </form>

                    <!-- Jangan biarkan user menghapus dirinya sendiri -->
                    <!-- @if(Auth::id() !== $user->id)
                        <button class="text-slate-400 hover:text-red-500" title="Hapus User">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    @endif -->
                </div>

            </div>
        @endforeach

    </div>
@endsection