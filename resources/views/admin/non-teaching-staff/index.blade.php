@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-[#125047] py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <nav class="flex items-center space-x-2 text-white mb-6">
                <a href="{{ route('home') }}" class="hover:text-green-300">Dashboard</a>
                <span class="text-gray-300">&gt;</span>
                <span class="border-b-2 border-white">Data Tenaga Kependidikan</span>
            </nav>

            {{-- Header Card --}}
            <div class="bg-[#136e67] rounded-2xl shadow-lg px-8 py-5 mb-8 border-b-4 border-white flex items-center">
                <h2 class="text-3xl font-bold text-white mx-auto">Manajemen Data Tenaga Pendidik Non Guru</h2>
            </div>

            {{-- Filter Section --}}
            <div class="bg-[#0d524a] rounded-xl p-6 mb-8">
                <h2 class="text-2xl font-bold text-white mb-6">Filter Tenaga Kependidikan</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Sekolah</label>
                        <div class="relative">
                            <select name="sekolah" class="block w-full bg-white rounded-lg border-0 py-2.5 pl-4 pr-10 focus:ring-2 focus:ring-green-400">
                                <option value="">Semua Sekolah</option>
                                @foreach($schools ?? [] as $school)
                                    <option value="{{ $school->id }}">{{ $school->name }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Jabatan</label>
                        <div class="relative">
                            <select name="position" class="block w-full bg-white rounded-lg border-0 py-2.5 pl-4 pr-10 focus:ring-2 focus:ring-green-400">
                                <option value="">Semua Jabatan</option>
                                <option value="Tata Usaha">Tata Usaha</option>
                                <option value="Pustakawan">Pustakawan</option>
                                <option value="Laboran">Laboran</option>
                                <option value="Keamanan">Keamanan</option>
                                <option value="Kebersihan">Kebersihan</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                        <div class="relative">
                            <select name="status" class="block w-full bg-white rounded-lg border-0 py-2.5 pl-4 pr-10 focus:ring-2 focus:ring-green-400">
                                <option value="">Semua Status</option>
                                <option value="PNS">PNS</option>
                                <option value="PPPK">PPPK</option>
                                <option value="GTT/PTT">GTT/PTT</option>
                                <option value="Honorer">Honorer</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">&nbsp;</label>
                        <div class="relative">
                            <input type="text" name="search" placeholder="Cari nama atau NIP..." class="block w-full bg-white rounded-lg border-0 py-2.5 pl-4 pr-10 focus:ring-2 focus:ring-green-400">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <button type="submit" class="text-gray-700 hover:text-gray-900">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Daftar Staff Section --}}
            <div class="bg-[#0d524a] rounded-xl p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-white">Daftar Tenaga Pendidik Non Guru</h2>
                    <a href="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.non-teaching-staff.create') : route('admin.non-teaching-staff.create') }}" class="inline-flex items-center px-4 py-2 bg-white rounded-lg font-semibold text-[#0d524a] hover:bg-green-50 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Tenaga Pendidik Non Guru
                    </a>
                </div>

                {{-- Staff Table --}}
                @if($staff->isEmpty())
                    <div class="bg-[#09443c] rounded-xl shadow-lg p-8 text-center">
                        <div class="mb-4">
                            <svg class="w-16 h-16 mx-auto text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-2">Tidak ada data tenaga pendidik non guru</h3>
                        <p class="text-white/70">Silahkan tambah data tenaga pendidik non guru atau ubah filter pencarian</p>
                            </div>
                @else
                    @php
                        $rows = $staff->map(function($person){
                            return [
                                $person->full_name,
                                $person->nip_nik ?? '-',
                                $person->school->name ?? '-',
                                $person->position ?? '-',
                                // $person->tmt ? ($person->tmt instanceof \Carbon\Carbon ? $person->tmt->translatedFormat('d-m-Y') : $person->tmt) : '-',
                                '<a href="'.(auth()->user()->hasRole('admin_sekolah') ? route('sekolah.non-teaching-staff.show', $person) : route('admin.non-teaching-staff.show', $person)).'" class="text-green-300 hover:underline">Lihat detail</a>'
                            ];
                        });
                    @endphp
                    <div class="bg-[#09443c] rounded-xl shadow-lg px-0 py-8">
                        <x-public.data-table :headers="['Nama','NIP/NIK','Asal Sekolah','Jabatan','Aksi']" :rows="$rows" />
                    </div>
                @endif

                {{-- Pagination --}}
                @if(isset($staff) && method_exists($staff, 'hasPages') && $staff->hasPages())
                <div class="mt-6">
                    {{ $staff->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
