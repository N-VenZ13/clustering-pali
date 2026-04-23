@extends('layouts.admin')
@section('title', 'DATA WILAYAH')

@section('content')
    <!-- Wrapper Utama Alpine JS (State untuk mengatur modal mana yang terbuka) -->
    <div x-data="{ modalKecamatan: false, modalDesa: false, modalEditKec: false, modalEditDesa: false, 
                   kecId: '', kecNama: '', desaId: '', desaNama: '', idKecamatanUntukDesa: '' }">

        <!-- Action Bar -->
        <div class="flex flex-col md:flex-row justify-end items-center mb-6 gap-4">
            <button @click="modalKecamatan = true" class="w-full md:w-auto bg-[#F97316] hover:bg-orange-600 text-white font-semibold py-2 px-6 rounded-lg flex items-center justify-center gap-2 transition shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Kecamatan
            </button>
        </div>

        <!-- Accordion List Card -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
            @forelse($kecamatans as $kecamatan)
                <div x-data="{ open: false }" class="border-b border-slate-100 last:border-0">
                    <!-- Baris Kecamatan -->
                    <div class="bg-[#F8FAFC] px-6 py-4 flex items-center justify-between hover:bg-slate-100 transition cursor-pointer" @click="open = !open">
                        <h3 class="text-lg font-bold text-[#1E293B]">{{ $kecamatan->nama_kecamatan }}</h3>
                        
                        <div class="flex items-center gap-4" @click.stop>
                            <!-- Tombol Edit Kec -->
                            <button @click="modalEditKec = true; kecId = '{{ $kecamatan->id }}'; kecNama = '{{ $kecamatan->nama_kecamatan }}'" class="text-slate-500 hover:text-[#1E3A8A]" title="Edit Kecamatan">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                            <!-- Tombol Hapus Kec -->
                            <form action="{{ route('kecamatan.destroy', $kecamatan->id) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Hapus kecamatan ini dan seluruh desanya?')" class="text-slate-500 hover:text-red-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                            <!-- Tombol Tambah Desa -->
                            <button @click="modalDesa = true; idKecamatanUntukDesa = '{{ $kecamatan->id }}'" class="text-slate-500 hover:text-[#10B981]" title="Tambah Desa di sini">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </button>
                            
                            <svg :class="open ? 'rotate-180' : ''" class="w-5 h-5 text-slate-500 transition-transform ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>

                    <!-- Baris Desa -->
                    <div x-show="open" x-collapse>
                        <div class="bg-white">
                            @forelse($kecamatan->desas as $desa)
                                <div class="px-6 py-3 border-b border-slate-50 flex items-center justify-between hover:bg-slate-50 transition">
                                    <p class="text-[#1E293B] pl-8 flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span> {{ $desa->nama_desa }}
                                    </p>
                                    <div class="flex items-center gap-4">
                                        <!-- Tombol Edit Desa -->
                                        <button @click="modalEditDesa = true; desaId = '{{ $desa->id }}'; desaNama = '{{ $desa->nama_desa }}'" class="text-slate-400 hover:text-[#1E3A8A]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </button>
                                        <!-- Tombol Hapus Desa -->
                                        <form action="{{ route('desa.destroy', $desa->id) }}" method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" onclick="return confirm('Hapus desa ini?')" class="text-slate-400 hover:text-red-500">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <!-- Teks ini muncul jika tidak ada desa -->
                                <div class="px-6 py-4 text-center text-sm text-slate-400 italic bg-slate-50/50">
                                    Belum ada data desa di kecamatan ini.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-slate-500">Belum ada data kecamatan.</div>
            @endforelse
        </div>

        <!-- ======================= MODAL SECTION ======================= -->
        
        <!-- Modal Tambah Kecamatan -->
        <div x-show="modalKecamatan" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" x-cloak>
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 mx-4" @click.away="modalKecamatan = false">
                <h2 class="text-xl font-bold mb-4">Tambah Kecamatan</h2>
                <form action="{{ route('kecamatan.store') }}" method="POST">
                    @csrf
                    <input type="text" name="nama_kecamatan" required placeholder="Nama Kecamatan..." class="w-full border-gray-200 rounded-lg mb-6 focus:ring-[#1E3A8A]">
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="modalKecamatan = false" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-[#1E3A8A] text-white rounded-lg hover:bg-blue-800">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Edit Kecamatan -->
        <div x-show="modalEditKec" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" x-cloak>
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 mx-4" @click.away="modalEditKec = false">
                <h2 class="text-xl font-bold mb-4">Edit Kecamatan</h2>
                <!-- Gunakan Alpine x-bind:action untuk mengubah URL Form dinamis -->
                <form :action="'/wilayah/kecamatan/' + kecId" method="POST">
                    @csrf @method('PUT')
                    <input type="text" name="nama_kecamatan" x-model="kecNama" required class="w-full border-gray-200 rounded-lg mb-6 focus:ring-[#1E3A8A]">
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="modalEditKec = false" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-[#1E3A8A] text-white rounded-lg hover:bg-blue-800">Update</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Tambah Desa -->
        <div x-show="modalDesa" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" x-cloak>
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 mx-4" @click.away="modalDesa = false">
                <h2 class="text-xl font-bold mb-4">Tambah Desa</h2>
                <form action="{{ route('desa.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="kecamatan_id" x-model="idKecamatanUntukDesa">
                    <input type="text" name="nama_desa" required placeholder="Nama Desa..." class="w-full border-gray-200 rounded-lg mb-6 focus:ring-[#1E3A8A]">
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="modalDesa = false" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-[#1E3A8A] text-white rounded-lg hover:bg-blue-800">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Edit Desa -->
        <div x-show="modalEditDesa" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" x-cloak>
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 mx-4" @click.away="modalEditDesa = false">
                <h2 class="text-xl font-bold mb-4">Edit Desa</h2>
                <form :action="'/wilayah/desa/' + desaId" method="POST">
                    @csrf @method('PUT')
                    <input type="text" name="nama_desa" x-model="desaNama" required class="w-full border-gray-200 rounded-lg mb-6 focus:ring-[#1E3A8A]">
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="modalEditDesa = false" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-[#1E3A8A] text-white rounded-lg hover:bg-blue-800">Update</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection