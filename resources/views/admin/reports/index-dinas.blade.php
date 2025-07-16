@extends('layouts.app')

@section('content')
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    {{-- Breadcrumb --}}
    <nav class="mb-6 flex items-center text-white text-base" aria-label="Breadcrumb">
        <a href="/" class="font-semibold hover:underline">Dashboard</a>
        <span class="mx-2">&gt;</span>
        <span class="text-green-300 border-b-2 border-green-300 pb-1">Dashboard Laporan</span>
    </nav>

    {{-- Header Card --}}
    <div class="bg-[#136e67] rounded-2xl shadow-lg px-8 py-5 mb-8 border-b-4 border-white flex items-center">
        <h2 class="text-3xl font-bold text-white mx-auto">Dashboard Laporan Admin Dinas</h2>
    </div>

    {{-- Reports Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {{-- Laporan Sekolah --}}
        <a href="{{ route('admin.reports.schools') }}" class="bg-[#0E453F] hover:bg-[#0a403a] text-white rounded-xl shadow-xl p-6 flex flex-col items-center justify-center transition group border-2 border-transparent hover:border-blue-400 hover:shadow-blue-400/50">
            <div class="mb-4">
                <svg class="h-16 w-16 text-green-300 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-center">Laporan Sekolah</h3>
            <p class="text-sm text-gray-300 text-center mt-2">Rekap data semua sekolah</p>
        </a>

        {{-- Laporan Guru --}}
        <a href="{{ route('admin.reports.teachers') }}" class="bg-[#0E453F] hover:bg-[#0a403a] text-white rounded-xl shadow-xl p-6 flex flex-col items-center justify-center transition group border-2 border-transparent hover:border-blue-400 hover:shadow-blue-400/50">
            <div class="mb-4">
                <svg class="h-16 w-16 text-green-300 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-center">Laporan Guru</h3>
            <p class="text-sm text-gray-300 text-center mt-2">Statistik data guru</p>
        </a>

        {{-- Laporan Siswa --}}
        <a href="{{ route('admin.reports.students') }}" class="bg-[#0E453F] hover:bg-[#0a403a] text-white rounded-xl shadow-xl p-6 flex flex-col items-center justify-center transition group border-2 border-transparent hover:border-blue-400 hover:shadow-blue-400/50">
            <div class="mb-4">
                <svg class="h-16 w-16 text-green-300 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-center">Laporan Siswa</h3>
            <p class="text-sm text-gray-300 text-center mt-2">Statistik data siswa</p>
        </a>

        {{-- Laporan Kelulusan --}}
        <a href="{{ route('admin.reports.graduation') }}" class="bg-[#0E453F] hover:bg-[#0a403a] text-white rounded-xl shadow-xl p-6 flex flex-col items-center justify-center transition group border-2 border-transparent hover:border-blue-400 hover:shadow-blue-400/50">
            <div class="mb-4">
                <svg class="h-16 w-16 text-green-300 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-center">Laporan Kelulusan</h3>
            <p class="text-sm text-gray-300 text-center mt-2">Data kelulusan siswa</p>
        </a>

        {{-- Laporan Tenaga Pendidik --}}
        <a href="{{ route('admin.non-teaching-staff.index') }}" class="bg-[#0E453F] hover:bg-[#0a403a] text-white rounded-xl shadow-xl p-6 flex flex-col items-center justify-center transition group border-2 border-transparent hover:border-blue-400 hover:shadow-blue-400/50">
            <div class="mb-4">
                <svg class="h-16 w-16 text-green-300 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-center">Laporan Tenaga Pendidik</h3>
            <p class="text-sm text-gray-300 text-center mt-2">Data tenaga pendidik non guru</p>
        </a>

        {{-- Laporan Statistik --}}
        <a href="{{ route('statistik') }}" class="bg-[#0E453F] hover:bg-[#0a403a] text-white rounded-xl shadow-xl p-6 flex flex-col items-center justify-center transition group border-2 border-transparent hover:border-blue-400 hover:shadow-blue-400/50">
            <div class="mb-4">
                <svg class="h-16 w-16 text-green-300 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-center">Statistik</h3>
            <p class="text-sm text-gray-300 text-center mt-2">Statistik visual data</p>
        </a>
    </div>
</section>
@endsection
