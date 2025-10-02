@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-[#125047] py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumb & Session Message --}}
            <nav class="flex items-center space-x-2 text-white mb-6">
                <a href="{{ route('home') }}" class="hover:text-green-300">Dashboard</a>
                <span class="text-gray-300">&gt;</span>
                <span class="border-b-2 border-white">Data Sekolah</span>
            </nav>

            {{-- Pesan sukses/error import (hanya di sini, bukan di modal) --}}
            @if(session('success'))
                @php
                    $successMsg = session('success');
                    $successLines = preg_split('/\n+/', $successMsg);
                    $mainMsg = array_shift($successLines);
                @endphp
                <div class="mb-6">
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-2" role="alert">
                        <strong>{{ $mainMsg }}</strong>
                    </div>
                    @if(session('import_errors') && count(session('import_errors')))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-2" role="alert">
                            <strong>Error:</strong>
                            <ul class="list-disc pl-5">
                                @foreach(session('import_errors') as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if(session('import_warnings') && count(session('import_warnings')))
                        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-2" role="alert">
                            <strong>Warning:</strong>
                            <ul class="list-disc pl-5">
                                @foreach(session('import_warnings') as $warn)
                                    <li>{{ $warn }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                    {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Header Card --}}
            <div class="bg-[#136e67] rounded-2xl shadow-lg px-8 py-5 mb-8 border-b-4 border-white flex items-center">
                <h2 class="text-3xl font-bold text-white mx-auto">Manajemen Data Sekolah</h2>
            </div>

            {{-- Filter Section --}}
            <div class="bg-[#0d524a] rounded-xl p-6 mb-8">
                <h2 class="text-2xl font-bold text-white mb-6">Filter Sekolah</h2>
                <form id="filtersForm" action="{{ route('dinas.schools.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    {{-- Form Filters --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Jenjang Pendidikan</label>
                        <div class="relative">
                            <select name="education_level" class="block w-full bg-white rounded-lg border-0 py-2.5 pl-4 pr-10 focus:ring-2 focus:ring-green-400">
                                <option value="">Semua Jenjang</option>
                                @foreach($education_levels as $value => $label)
                                    <option value="{{ $value }}" {{ ($filters['education_level'] ?? '') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Kecamatan</label>
                        <div class="relative">
                             <select name="region" class="block w-full bg-white rounded-lg border-0 py-2.5 pl-4 pr-10 focus:ring-2 focus:ring-green-400">
                                <option value="">Semua kecamatan</option>
                                @foreach($regions as $value => $label)
                                    <option value="{{ $value }}" {{ ($filters['region'] ?? '') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Status sekolah</label>
                        <div class="relative">
                            <select name="status" class="block w-full bg-white rounded-lg border-0 py-2.5 pl-4 pr-10 focus:ring-2 focus:ring-green-400">
                                <option value="">Semua Status</option>
                                @foreach($statuses as $value => $label)
                                    <option value="{{ $value }}" {{ ($filters['status'] ?? '') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">&nbsp;</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nama atau NPSN..."
                                class="block w-full bg-white rounded-lg border-0 py-2.5 pl-4 pr-10 focus:ring-2 focus:ring-green-400">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <button type="submit" class="text-gray-700 hover:text-gray-900">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Daftar Sekolah Section --}}
            <div class="bg-[#0d524a] rounded-xl p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-white">Daftar Sekolah</h2>
                    <div class="flex space-x-3">
                        {{-- Tombol Import --}}
                        <button onclick="document.getElementById('import-modal').classList.remove('hidden')"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 rounded-lg font-semibold text-white hover:bg-blue-700 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                            </svg>
                            Import Excel
                        </button>
                        {{-- Tombol Tambah Sekolah --}}
                        <a href="{{ route('dinas.schools.create') }}" class="inline-flex items-center px-4 py-2 bg-white rounded-lg font-semibold text-[#0d524a] hover:bg-green-50 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Sekolah
                        </a>
                    </div>
                </div>


                {{-- School Cards Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($schools as $school)
                    <div class="bg-white rounded-xl p-6 shadow-lg flex flex-col justify-between">
                        <div>
                            <div class="flex items-center mb-4">
                                <div class="w-16 h-16 rounded-full mr-4 flex items-center justify-center flex-shrink-0 overflow-hidden">
                                    @if($school->logo)
                                        <img src="{{ asset('storage/' . $school->logo) }}" alt="Logo {{ $school->name }}" class="w-16 h-16 object-cover">
                                    @else
                                        <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                        </div>
                                    @endif
                                </div>
                                <h3 class="text-xl font-bold text-[#0d524a] truncate flex-1">{{ $school->name }}</h3>
                            </div>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $school->address }}</p>
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <p class="text-sm text-gray-500">NPSN</p>
                                    <p class="font-semibold text-sm">{{ $school->npsn }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status</p>
                                    <p class="font-semibold text-sm">{{ $school->status }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Jenjang</p>
                                    <p class="font-semibold text-sm">{{ $school->education_level }}</p>
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-sm text-gray-500">Email</p>
                                    <p class="font-semibold text-sm truncate" title="{{ $school->email ?: '-' }}">
                                        {{ $school->email ?: '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-2 mt-4">
                            <a href="{{ route('dinas.schools.show', $school) }}" class="flex-1 block text-center px-4 py-2 bg-[#0d524a] text-white rounded-lg hover:bg-[#125047] transition">
                                Lihat Detail
                            </a>
                            <a href="{{ route('dinas.schools.edit', $school) }}" class="flex-1 block text-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                                Edit
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-1 md:col-span-2 lg:col-span-3">
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-8 text-center">
                            <svg class="w-16 h-16 text-white/50 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            <h3 class="text-lg font-medium text-white">Tidak ada sekolah ditemukan</h3>
                            <p class="mt-1 text-sm text-white/70">Coba ubah filter atau kata kunci pencarian Anda.</p>
                        </div>
                    </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                @if($schools->hasPages())
                    <div class="mt-8">
                        {{ $schools->links() }}
                    </div>
                @endif
            </div>
        </div>
        {{-- Import Modal --}}
    <div id="import-modal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-start justify-center min-h-screen pt-16 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('import-modal').classList.add('hidden')"></div>

            <span class="hidden sm:inline-block sm:align-top sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-top bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-top sm:max-w-lg sm:w-full sm:p-6">
                {{-- Hapus pesan error/success di dalam modal, hanya tampilkan di atas manajemen data sekolah --}}
                <form action="{{ route('dinas.schools.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Import Data Sekolah</h3>
                            <div class="mt-2">
                                <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <p class="text-xs text-gray-500 mt-1">Format yang didukung: .xlsx, .xls, .csv</p>
                                <div class="mt-3">
                                    <a href="{{ route('dinas.schools.template') }}" class="text-sm text-blue-600 hover:underline">
                                        Download template Excel
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Import
                        </button>
                        <button type="button" onclick="document.getElementById('import-modal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
        </div>
    </div>
    <script>
        (function(){
            const form = document.getElementById('filtersForm');
            if (!form) return;
            const selects = form.querySelectorAll('select');
            const inputs = form.querySelectorAll('input[type="text"]');
            let t;
            const debounce = (fn, delay) => {
                clearTimeout(t);
                t = setTimeout(fn, delay);
            };
            selects.forEach(el => el.addEventListener('change', () => form.submit()));
            inputs.forEach(el => el.addEventListener('input', () => debounce(() => form.submit(), 400)));
        })();
    </script>
@endsection
