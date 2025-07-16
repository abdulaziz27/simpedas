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

            @if(session('success'))
                <div class="bg-green-500 text-white p-4 rounded-lg mb-6">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Header Card --}}
            <div class="bg-[#136e67] rounded-2xl shadow-lg px-8 py-5 mb-8 border-b-4 border-white flex items-center">
                <h2 class="text-3xl font-bold text-white mx-auto">Manajemen Data Sekolah</h2>
            </div>

            {{-- Filter Section --}}
            <div class="bg-[#0d524a] rounded-xl p-6 mb-8">
                <h2 class="text-2xl font-bold text-white mb-6">Filter Sekolah</h2>
                <form action="{{ route('admin.schools.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
                    <a href="{{ route('admin.schools.create') }}" class="inline-flex items-center px-4 py-2 bg-white rounded-lg font-semibold text-[#0d524a] hover:bg-green-50 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Tambah Sekolah
                    </a>
                </div>

                {{-- School Cards Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($schools as $school)
                    <div class="bg-white rounded-xl p-6 shadow-lg flex flex-col justify-between">
                        <div>
                            <div class="flex items-center mb-4">
                                <div class="w-16 h-16 bg-gray-200 rounded-full mr-4 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
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
                            <a href="{{ route('admin.schools.show', $school) }}" class="flex-1 block text-center px-4 py-2 bg-[#0d524a] text-white rounded-lg hover:bg-[#125047] transition">
                                Lihat Detail
                            </a>
                            <a href="{{ route('admin.schools.edit', $school) }}" class="flex-1 block text-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
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
    </div>
@endsection
