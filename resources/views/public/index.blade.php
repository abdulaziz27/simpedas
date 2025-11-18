@extends('layouts.public')

@section('title', 'Dashboard - SIMPEDAS')

@section('content')

@guest
    {{-- Tampilan untuk Guest / Penggunu Umum --}}
    <x-public.hero-section />
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <img src="{{ asset('images/banner-pemko.png') }}" alt="Banner Pemko" class="rounded-xl shadow-lg w-full animate-scale-in">
    </section>

    {{-- Statistik Ringkas Section --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">
        <div class="bg-[#136e67] rounded-2xl shadow-lg px-8 py-6 mb-6 animate-fade-in-down">
            <h2 class="text-3xl font-bold text-white mb-2">Statistik Pendidikan</h2>
            <p class="text-green-200 text-sm">Data terkini sistem pendidikan Kota Pematang Siantar</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow-lg p-6 text-center animate-fade-in-up menu-card-delay-1">
                <div class="icon-bg-green mx-auto mb-4 w-16 h-16 flex items-center justify-center">
                    <img src="{{ asset('images/icon-stats.png') }}" alt="Icon Sekolah" class="h-10 w-10">
                </div>
                <div class="text-4xl font-bold text-[#125047] mb-2 counter-number" data-target="{{ $publicStats['total_sekolah'] ?? 0 }}">0</div>
                <div class="text-gray-600 font-semibold">Total Sekolah</div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 text-center animate-fade-in-up menu-card-delay-2">
                <div class="icon-bg-green mx-auto mb-4 w-16 h-16 flex items-center justify-center">
                    <img src="{{ asset('images/icon-guru.png') }}" alt="Icon Guru" class="h-10 w-10">
                </div>
                <div class="text-4xl font-bold text-[#125047] mb-2 counter-number" data-target="{{ $publicStats['total_guru'] ?? 0 }}">0</div>
                <div class="text-gray-600 font-semibold">Total Guru</div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 text-center animate-fade-in-up menu-card-delay-3">
                <div class="icon-bg-green mx-auto mb-4 w-16 h-16 flex items-center justify-center">
                    <img src="{{ asset('images/icon-siswa.png') }}" alt="Icon Siswa" class="h-10 w-10">
                </div>
                <div class="text-4xl font-bold text-[#125047] mb-2 counter-number" data-target="{{ $publicStats['total_siswa_aktif'] ?? 0 }}">0</div>
                <div class="text-gray-600 font-semibold">Siswa Aktif</div>
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
        <a href="{{ route('public.search-guru') }}" class="menu-card bg-white/90 hover:bg-white text-[#125047] rounded-xl shadow-lg p-4 flex items-center transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl group animate-fade-in-up menu-card-delay-1">
            <div class="icon-bg-green mr-6"><img src="{{ asset('images/icon-guru.png') }}" alt="Icon Guru" class="h-10 w-10 transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3"></div>
            <span class="text-lg font-semibold">Cari Guru</span>
        </a>
        <a href="{{ route('public.search-siswa') }}" class="menu-card bg-white/90 hover:bg-white text-[#125047] rounded-xl shadow-lg p-4 flex items-center transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl group animate-fade-in-up menu-card-delay-2">
            <div class="icon-bg-green mr-6"><img src="{{ asset('images/icon-siswa.png') }}" alt="Icon Siswa" class="h-10 w-10 transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3"></div>
            <span class="text-lg font-semibold">Cari Siswa</span>
        </a>
        <a href="{{ route('public.search-guru') }}" class="menu-card bg-white/90 hover:bg-white text-[#125047] rounded-xl shadow-lg p-4 flex items-center transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl group animate-fade-in-up menu-card-delay-3">
            <div class="icon-bg-green mr-6"><img src="{{ asset('images/icon-guru.png') }}" alt="Icon Guru" class="h-10 w-10 transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3"></div>
            <span class="text-lg font-semibold">Cari Tutor</span>
        </a>
        <a href="{{ route('public.search-sekolah') }}" class="menu-card bg-white/90 hover:bg-white text-[#125047] rounded-xl shadow-lg p-4 flex items-center transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl group animate-fade-in-up menu-card-delay-4">
            <div class="icon-bg-green mr-6"><img src="{{ asset('images/icon-siswa.png') }}" alt="Icon Sekolah" class="h-10 w-10 transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3"></div>
            <span class="text-lg font-semibold">Cari Sekolah</span>
        </a>
        <a href="{{ route('statistik') }}" class="menu-card bg-white/90 hover:bg-white text-[#125047] rounded-xl shadow-lg p-4 flex items-center transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl group animate-fade-in-up menu-card-delay-5">
            <div class="icon-bg-green mr-6"><img src="{{ asset('images/icon-stats.png') }}" alt="Icon Statistik" class="h-10 w-10 transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3"></div>
            <span class="text-lg font-semibold">Statistik</span>
        </a>
        <a href="{{ route('public.search-non-teaching-staff') }}" class="menu-card bg-white/90 hover:bg-white text-[#125047] rounded-xl shadow-lg p-4 flex items-center transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl group animate-fade-in-up menu-card-delay-6">
            <div class="icon-bg-green mr-6"><img src="{{ asset('images/icon-siswa.png') }}" alt="Icon Non Pegawai" class="h-10 w-10 transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3"></div>
            <span class="text-lg font-semibold">Cari T.Pendidik Non Pegawai</span>
        </a>
    </section>

    {{-- Artikel Terbaru Section --}}
    @if(isset($latestArticles) && $latestArticles->isNotEmpty())
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">
            <div class="bg-[#136e67] rounded-2xl shadow-lg px-8 py-5 mb-6 animate-fade-in-down">
                <div class="flex items-center justify-between">
                    <h2 class="text-3xl font-bold text-white">Baca Berita/Artikel Terbaru Kami</h2>
                    <a href="{{ route('public.articles') }}" class="text-white hover:text-green-300 font-semibold">
                        Lihat Lebih Banyak
                    </a>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($latestArticles as $index => $article)
                    <a href="{{ route('public.article.detail', $article->slug) }}" class="article-card bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1 animate-slide-in-left {{ 'article-card-delay-' . ($index + 1) }}">
                        @if($article->featured_image)
                            <img src="{{ asset('storage/' . $article->featured_image) }}" alt="{{ $article->title }}" class="w-full h-48 object-cover transition-transform duration-300 hover:scale-105">
                        @else
                            <div class="w-full h-48 bg-gradient-to-br from-[#125047] to-[#0E453F] flex items-center justify-center">
                                <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                                </svg>
                            </div>
                        @endif
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-2 line-clamp-2">{{ $article->title }}</h3>
                            @if($article->excerpt)
                                <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ $article->excerpt }}</p>
                            @endif
                            <div class="flex items-center justify-between text-sm text-gray-500">
                                <span>{{ $article->published_at->format('d M Y') }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Tentang Kami Section -- HIDDEN --}}
    {{-- @if(isset($aboutSettings) && ($aboutSettings->has('about_visi') || $aboutSettings->has('about_misi') || $aboutSettings->has('about_tugas_pokok') || $aboutSettings->has('about_fungsi')))
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">
            <div class="bg-[#136e67] rounded-2xl shadow-lg px-8 py-5 mb-6">
                <h2 class="text-3xl font-bold text-white">Tentang Kami</h2>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-8">
                @if($aboutSettings->has('about_visi') && $aboutSettings['about_visi']->value)
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-[#125047] mb-3">Visi</h3>
                        <p class="text-gray-700 leading-relaxed">{{ $aboutSettings['about_visi']->value }}</p>
                    </div>
                @endif
                @if($aboutSettings->has('about_misi') && $aboutSettings['about_misi']->value)
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-[#125047] mb-3">Misi</h3>
                        <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $aboutSettings['about_misi']->value }}</p>
                    </div>
                @endif
                @if($aboutSettings->has('about_tugas_pokok') && $aboutSettings['about_tugas_pokok']->value)
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-[#125047] mb-3">Tugas Pokok</h3>
                        <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $aboutSettings['about_tugas_pokok']->value }}</p>
                    </div>
                @endif
                @if($aboutSettings->has('about_fungsi') && $aboutSettings['about_fungsi']->value)
                    <div>
                        <h3 class="text-2xl font-bold text-[#125047] mb-3">Fungsi</h3>
                        <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $aboutSettings['about_fungsi']->value }}</p>
                    </div>
                @endif
            </div>
        </section>
    @endif --}}

    {{-- Galeri Foto Section --}}
    @if(isset($latestGalleries) && $latestGalleries->isNotEmpty())
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">
            <div class="bg-[#136e67] rounded-2xl shadow-lg px-8 py-5 mb-6 animate-fade-in-down">
                <div class="flex items-center justify-between">
                    <h2 class="text-3xl font-bold text-white">Galeri Foto</h2>
                    <a href="{{ route('public.galleries') }}" class="text-white hover:text-green-300 font-semibold">
                        Lihat Lebih Banyak
                    </a>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($latestGalleries as $index => $gallery)
                    <div class="group relative overflow-hidden rounded-xl shadow-lg cursor-pointer animate-fade-in-up {{ 'menu-card-delay-' . ($index + 1) }}">
                        <img src="{{ asset('storage/' . $gallery->image) }}" alt="{{ $gallery->title }}" 
                             class="w-full h-64 object-cover transition-transform duration-300 group-hover:scale-110">
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-opacity duration-300 flex items-center justify-center">
                            <div class="text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300 text-center px-2">
                                <p class="font-semibold text-sm">{{ $gallery->title }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Kontak & Lokasi Section --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 mb-12">
        <div class="bg-[#136e67] rounded-2xl shadow-lg px-8 py-5 mb-6 animate-fade-in-down">
            <h2 class="text-3xl font-bold text-white">Kontak & Lokasi</h2>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-lg p-8 animate-on-scroll">
                <h3 class="text-2xl font-bold text-[#125047] mb-6">Informasi Kontak</h3>
                <div class="space-y-4">
                    @if(isset($contactSettings['contact_address']) && $contactSettings['contact_address']->value)
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-[#125047] mr-4 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-800 mb-1">Alamat</p>
                                <p class="text-gray-600">{{ $contactSettings['contact_address']->value }}</p>
                            </div>
                        </div>
                    @endif
                    @if(isset($contactSettings['contact_phone']) && $contactSettings['contact_phone']->value)
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-[#125047] mr-4 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-800 mb-1">Telepon</p>
                                <p class="text-gray-600">{{ $contactSettings['contact_phone']->value }}</p>
                            </div>
                        </div>
                    @endif
                    @if(isset($contactSettings['contact_email']) && $contactSettings['contact_email']->value)
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-[#125047] mr-4 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-800 mb-1">Email</p>
                                <p class="text-gray-600">{{ $contactSettings['contact_email']->value }}</p>
                            </div>
                        </div>
                    @endif
                    @if(isset($contactSettings['contact_hours']) && $contactSettings['contact_hours']->value)
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-[#125047] mr-4 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-800 mb-1">Jam Operasional</p>
                                <p class="text-gray-600 whitespace-pre-line">{{ $contactSettings['contact_hours']->value }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-8 animate-on-scroll">
                <h3 class="text-2xl font-bold text-[#125047] mb-6">Lokasi</h3>
                @php
                    $mapUrl = isset($contactSettings['contact_map_url']) && $contactSettings['contact_map_url']->value 
                        ? $contactSettings['contact_map_url']->value 
                        : 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3984.494668834142!2d99.06709037543456!3d2.9601531542844213!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x303184429d8b212f%3A0x382944b91c345d9b!2sDinas%20Pendidikan%20Kota%20Pematangsiantar!5e0!3m2!1sid!2sid!4v1763474070417!5m2!1sid!2sid';
                @endphp
                <div class="rounded-lg overflow-hidden">
                    <iframe src="{{ $mapUrl }}" 
                            width="100%" 
                            height="400" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
    </section>
@endguest

@auth
    @if(auth()->user()->hasRole('guru'))
        {{-- Tampilan Dashboard Guru --}}
        <div class="bg-[#125047] min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Welcome Section with Image -->
                <div class="py-8 mb-8">
                    <div class="flex flex-col lg:flex-row items-center lg:items-start justify-between space-y-8 lg:space-y-0">
                        <!-- Welcome Message - Left Side -->
                        <div class="max-w-lg text-left">
                            <h1 class="text-4xl sm:text-5xl font-bold leading-tight text-white">Selamat Datang Guru</h1>
                            <p class="mt-4 text-2xl font-semibold text-green-300">Di Sistem Informasi Dinas Pendidikan</p>
                            <p class="mt-2 text-xl text-green-300">Kota Pematang Siantar</p>
                            <p class="mt-4 text-sm text-gray-200">Jl. Merdeka No.228c, Dwikora, Kec. Siantar Bar., Kota Pematang Siantar, Sumatera Utara 21146</p>
                        </div>
                        <!-- Image - Right Side -->
                        <div class="flex-shrink-0">
                            <img src="{{ asset('images/pemko.png') }}" alt="Logo Pemko" class="w-80 mx-auto lg:mx-0 rounded-lg">
                        </div>
                    </div>
                </div>

                <!-- Quick Access Menu -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <a href="{{ route('guru.profile.show') }}" class="bg-[#0E453F] rounded-xl shadow-lg overflow-hidden hover:bg-[#0a403a] transition">
                        <div class="p-6 text-center">
                            <div class="icon-bg-green mx-auto mb-4 w-16 h-16 flex items-center justify-center rounded-full">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2">Profil Saya</h3>
                            <p class="text-gray-300 text-sm">Lihat dan edit profil Anda</p>
                        </div>
                    </a>

                    <a href="{{ route('guru.documents') }}" class="bg-[#0E453F] rounded-xl shadow-lg overflow-hidden hover:bg-[#0a403a] transition">
                        <div class="p-6 text-center">
                            <div class="icon-bg-green mx-auto mb-4 w-16 h-16 flex items-center justify-center rounded-full">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2">Dokumen Pribadi</h3>
                            <p class="text-gray-300 text-sm">Kelola dokumen dan sertifikat</p>
                        </div>
                    </a>

                    {{-- <a href="{{ route('guru.students') }}" class="bg-[#0E453F] rounded-xl shadow-lg overflow-hidden hover:bg-[#0a403a] transition">
                        <div class="p-6 text-center">
                            <div class="icon-bg-green mx-auto mb-4 w-16 h-16 flex items-center justify-center rounded-full">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2">Data Siswa</h3>
                            <p class="text-gray-300 text-sm">Lihat data siswa yang Anda ajar</p>
                        </div>
                    </a> --}}
                </div>
            </div>
        </div>
    @elseif(auth()->user()->hasRole('admin_dinas'))
        {{-- Tampilan Dashboard Khusus Admin Dinas --}}
        <div class="bg-[#125047] min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Header Banner -->
                <section class="mb-8">
                    <img src="{{ asset('images/banner-pemko.png') }}" alt="Banner Pemko" class="rounded-xl shadow-lg w-full">
                </section>

                <!-- Statistics Cards Grid - 3 columns per row -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Row 1 - Statistics Cards -->
                    <div class="bg-white/90 hover:bg-white text-[#125047] rounded-xl shadow-lg p-6 flex items-center transition-all duration-300 hover:-translate-y-1 hover:shadow-xl animate-fade-in-up menu-card-delay-1">
                        <div class="icon-bg-green mr-6">
                            <img src="{{ asset('images/icon-stats.png') }}" alt="Icon Stats" class="h-12 w-12 transition-transform duration-300 hover:scale-110">
                        </div>
                        <div>
                            <div class="text-3xl font-bold">{{ $stats['total_sekolah'] ?? 0 }}</div>
                            <div class="text-sm text-gray-600">Jumlah Sekolah</div>
                        </div>
                    </div>
                    <div class="bg-white/90 hover:bg-white text-[#125047] rounded-xl shadow-lg p-6 flex items-center transition-all duration-300 hover:-translate-y-1 hover:shadow-xl animate-fade-in-up menu-card-delay-2">
                        <div class="icon-bg-green mr-6">
                            <img src="{{ asset('images/icon-guru.png') }}" alt="Icon Guru" class="h-12 w-12 transition-transform duration-300 hover:scale-110">
                        </div>
                        <div>
                            <div class="text-3xl font-bold">{{ $stats['total_guru'] ?? 0 }}</div>
                            <div class="text-sm text-gray-600">Jumlah Guru</div>
                        </div>
                    </div>
                    <div class="bg-white/90 hover:bg-white text-[#125047] rounded-xl shadow-lg p-6 flex items-center transition-all duration-300 hover:-translate-y-1 hover:shadow-xl animate-fade-in-up menu-card-delay-3">
                        <div class="icon-bg-green mr-6">
                            <img src="{{ asset('images/icon-siswa.png') }}" alt="Icon Siswa" class="h-12 w-12 transition-transform duration-300 hover:scale-110">
                        </div>
                        <div>
                            <div class="text-3xl font-bold">{{ $stats['total_siswa_aktif'] ?? 0 }}</div>
                            <div class="text-sm text-gray-600">Jumlah Siswa Aktif</div>
                        </div>
                    </div>

                    <!-- Row 2 - Statistics Cards -->
                    <div class="bg-white/90 hover:bg-white text-[#125047] rounded-xl shadow-lg p-6 flex items-center transition-all duration-300 hover:-translate-y-1 hover:shadow-xl animate-fade-in-up menu-card-delay-4">
                        <div class="icon-bg-green mr-6">
                            <img src="{{ asset('images/icon-siswa.png') }}" alt="Icon Siswa" class="h-12 w-12 transition-transform duration-300 hover:scale-110">
                        </div>
                        <div>
                            <div class="text-3xl font-bold">{{ $stats['total_siswa_tamat'] ?? 0 }}</div>
                            <div class="text-sm text-gray-600">Jumlah Siswa Tamat</div>
                        </div>
                    </div>
                    <a href="{{ route('statistik.detail', 'sekolah') }}" class="bg-white/90 hover:bg-white text-[#125047] rounded-xl shadow-lg p-6 flex items-center transition-all duration-300 hover:-translate-y-1 hover:shadow-xl cursor-pointer animate-fade-in-up menu-card-delay-5">
                        <div class="icon-bg-green mr-6">
                            <img src="{{ asset('images/icon-guru.png') }}" alt="Icon Statistik" class="h-12 w-12 transition-transform duration-300 hover:scale-110">
                        </div>
                        <div>
                            <div class="text-xl font-bold">Statistik</div>
                            <div class="text-sm text-gray-600">Lihat statistik lengkap</div>
                        </div>
                    </a>
                    <a href="{{ route('dinas.user-management.index') }}" class="bg-white/90 hover:bg-white text-[#125047] rounded-xl shadow-lg p-6 flex items-center transition-all duration-300 hover:-translate-y-1 hover:shadow-xl cursor-pointer animate-fade-in-up menu-card-delay-6">
                        <div class="icon-bg-green mr-6">
                            <img src="{{ asset('images/icon-siswa.png') }}" alt="Icon Validasi" class="h-12 w-12 transition-transform duration-300 hover:scale-110">
                        </div>
                        <div>
                            <div class="text-xl font-bold">Manajemen Pengguna</div>
                            <div class="text-sm text-gray-600">Manajemen pengguna sistem</div>
                        </div>
                    </a>
                </div>

                <!-- Quick Actions Section -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-white mb-6">Aksi Cepat</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <a href="{{ route('dinas.schools.index') }}" class="bg-[#0E453F] hover:bg-[#0a403a] text-white rounded-xl shadow-xl p-6 flex items-center justify-between transition-all duration-300 group border-2 border-transparent hover:border-blue-400 hover:shadow-blue-400/50 hover:-translate-y-1 animate-fade-in-up menu-card-delay-1">
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-white">Manajemen</h3>
                                <h3 class="text-xl font-bold text-white">Sekolah</h3>
                            </div>
                            <div class="flex-shrink-0 ml-4">
                                <img src="{{ asset('images/icon-stats.png') }}" alt="Icon Sekolah" class="h-20 w-20 group-hover:scale-110 transition-transform duration-300">
                            </div>
                        </a>
                        <a href="{{ route('dinas.teachers.index') }}" class="bg-[#0E453F] hover:bg-[#0a403a] text-white rounded-xl shadow-xl p-6 flex items-center justify-between transition-all duration-300 group border-2 border-transparent hover:border-blue-400 hover:shadow-blue-400/50 hover:-translate-y-1 animate-fade-in-up menu-card-delay-2">
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-white">Manajemen</h3>
                                <h3 class="text-xl font-bold text-white">Guru</h3>
                            </div>
                            <div class="flex-shrink-0 ml-4">
                                <img src="{{ asset('images/icon-guru.png') }}" alt="Icon Guru" class="h-20 w-20 group-hover:scale-110 transition-transform duration-300">
                            </div>
                        </a>
                        <a href="{{ route('dinas.students.index') }}" class="bg-[#0E453F] hover:bg-[#0a403a] text-white rounded-xl shadow-xl p-6 flex items-center justify-between transition-all duration-300 group border-2 border-transparent hover:border-blue-400 hover:shadow-blue-400/50 hover:-translate-y-1 animate-fade-in-up menu-card-delay-3">
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-white">Manajemen</h3>
                                <h3 class="text-xl font-bold text-white">Siswa</h3>
                            </div>
                            <div class="flex-shrink-0 ml-4">
                                <img src="{{ asset('images/icon-siswa.png') }}" alt="Icon Siswa" class="h-20 w-20 group-hover:scale-110 transition-transform duration-300">
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @elseif(auth()->user()->hasRole('admin_sekolah'))
        {{-- Tampilan Dashboard Khusus Admin Sekolah --}}
        <div class="bg-[#125047] min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Header Banner -->
                <section class="mb-8">
                    <img src="{{ asset('images/banner-pemko.png') }}" alt="Banner Pemko" class="rounded-xl shadow-lg w-full">
                </section>

                <!-- Management Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <a href="{{ route('sekolah.students.index') }}" class="bg-[#0E453F] hover:bg-[#0a403a] text-white rounded-xl shadow-lg p-6 flex flex-col items-center transition">
                        <div class="icon-bg-green mb-4"><img src="{{ asset('images/icon-siswa.png') }}" alt="Icon Siswa" class="h-12 w-12"></div>
                        <span class="text-lg font-semibold">Manajemen Data Siswa</span>
                    </a>
                    <a href="{{ route('sekolah.teachers.index') }}" class="bg-[#0E453F] hover:bg-[#0a403a] text-white rounded-xl shadow-lg p-6 flex flex-col items-center transition">
                        <div class="icon-bg-green mb-4"><img src="{{ asset('images/icon-guru.png') }}" alt="Icon Guru" class="h-12 w-12"></div>
                        <span class="text-lg font-semibold">Manajemen Data Guru</span>
                    </a>
                    <a href="{{ route('sekolah.non-teaching-staff.index') }}" class="bg-[#0E453F] hover:bg-[#0a403a] text-white rounded-xl shadow-lg p-6 flex flex-col items-center transition">
                        <div class="icon-bg-green mb-4"><img src="{{ asset('images/icon-siswa.png') }}" alt="Icon Pegawai" class="h-12 w-12"></div>
                        <span class="text-lg font-semibold">Manajemen Data Pegawai</span>
                    </a>
                </div>

                <!-- Statistics Cards -->
                <h2 class="text-2xl font-bold text-white mb-6">Statistik Sekolah</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white/90 text-[#125047] rounded-xl shadow-lg p-4 flex items-center">
                        <div class="icon-bg-green mr-6"><img src="{{ asset('images/icon-guru.png') }}" alt="Icon Guru" class="h-10 w-10"></div>
                        <div>
                            <div class="text-2xl font-bold">{{ $stats['total_guru'] ?? 0 }}</div>
                            <div class="text-sm">Jumlah Guru</div>
                        </div>
                    </div>
                    <div class="bg-white/90 text-[#125047] rounded-xl shadow-lg p-4 flex items-center">
                        <div class="icon-bg-green mr-6"><img src="{{ asset('images/icon-siswa.png') }}" alt="Icon Siswa" class="h-10 w-10"></div>
                        <div>
                            <div class="text-2xl font-bold">{{ $stats['total_siswa'] ?? 0 }}</div>
                            <div class="text-sm">Jumlah Siswa</div>
                        </div>
                    </div>
                    <div class="bg-white/90 text-[#125047] rounded-xl shadow-lg p-4 flex items-center">
                        <div class="icon-bg-green mr-6"><img src="{{ asset('images/icon-siswa.png') }}" alt="Icon Pegawai" class="h-10 w-10"></div>
                        <div>
                            <div class="text-2xl font-bold">{{ $stats['total_non_teaching_staff'] ?? 0 }}</div>
                            <div class="text-sm">Jumlah Tenaga Pendidik Non Guru</div>
                        </div>
                    </div>
                </div>

                <!-- Chart Section -->
                <h2 class="text-2xl font-bold text-white mb-6">Statistik Total guru, Siswa & Pegawai Non Guru</h2>
                <div class="bg-white/90 rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h4 class="text-sm text-gray-500 font-semibold mb-1">Statistics</h4>
                            <h2 class="text-xl font-bold text-[#125047]">Total guru, Siswa & Pegawai Non Guru</h2>
                        </div>
                        <div class="flex items-center space-x-6">
                            <div class="flex items-center space-x-2">
                                <div class="w-4 h-4 bg-[#125047] rounded-full"></div>
                                <span class="text-sm text-gray-600">Guru</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-4 h-4 bg-[#4FE9A4] rounded-full"></div>
                                <span class="text-sm text-gray-600">Siswa</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-4 h-4 bg-[#1E1E1E] rounded-full"></div>
                                <span class="text-sm text-gray-600">Tenaga Pendidik</span>
                            </div>
                        </div>
                    </div>
                    <div class="relative" style="height: 400px;">
                        <canvas id="schoolStatsChart"></canvas>
                    </div>
                </div>

                                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const ctx = document.getElementById('schoolStatsChart').getContext('2d');

                    // Debug data
                    console.log('Stats data:', {
                        total_guru: {{ $stats['total_guru'] ?? 0 }},
                        total_siswa: {{ $stats['total_siswa'] ?? 0 }},
                        total_non_teaching_staff: {{ $stats['total_non_teaching_staff'] ?? 0 }}
                    });

                    const chartData = {
                        labels: ['GURU', 'SISWA', 'TENAGA NON GURU'],
                        datasets: [{
                            data: [
                                {{ $stats['total_guru'] ?? 0 }},
                                {{ $stats['total_siswa'] ?? 0 }},
                                {{ $stats['total_non_teaching_staff'] ?? 0 }}
                            ],
                            backgroundColor: ['#125047', '#4FE9A4', '#1E1E1E'],
                            borderColor: ['#125047', '#4FE9A4', '#1E1E1E'],
                            borderWidth: 1
                        }]
                    };

                    const chartOptions = {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.label + ': ' + context.parsed.y;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: {{ max(($stats['total_guru'] ?? 0), ($stats['total_siswa'] ?? 0), ($stats['total_non_teaching_staff'] ?? 0)) + 10 }},
                                ticks: {
                                    stepSize: 1
                                },
                                grid: {
                                    color: '#E5E7EB'
                                }
                            },
                            x: {
                                ticks: {
                                    font: {
                                        weight: 'bold'
                                    }
                                },
                                grid: {
                                    display: false
                                }
                            }
                        }
                    };

                    new Chart(ctx, {
                        type: 'bar',
                        data: chartData,
                        options: chartOptions
                    });
                });
                </script>
            </div>
        </div>
    @else
        {{-- Tampilan untuk user yang login tanpa role (sama dengan guest tapi tanpa tombol login) --}}
        <x-public.hero-section />
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <img src="{{ asset('images/banner-pemko.png') }}" alt="Banner Pemko" class="rounded-xl shadow-lg w-full">
        </section>

        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
            <a href="{{ route('public.search-guru') }}" class="menu-card bg-white/90 hover:bg-white text-[#125047] rounded-xl shadow-lg p-4 flex items-center transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl group animate-fade-in-up menu-card-delay-1">
                <div class="icon-bg-green mr-6"><img src="{{ asset('images/icon-guru.png') }}" alt="Icon Guru" class="h-10 w-10 transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3"></div>
                <span class="text-lg font-semibold">Lihat Data Guru</span>
            </a>
            <a href="{{ route('public.search-siswa') }}" class="menu-card bg-white/90 hover:bg-white text-[#125047] rounded-xl shadow-lg p-4 flex items-center transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl group animate-fade-in-up menu-card-delay-2">
                <div class="icon-bg-green mr-6"><img src="{{ asset('images/icon-siswa.png') }}" alt="Icon Siswa" class="h-10 w-10 transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3"></div>
                <span class="text-lg font-semibold">Cari Siswa</span>
            </a>
            <a href="{{ route('public.search-guru') }}" class="menu-card bg-white/90 hover:bg-white text-[#125047] rounded-xl shadow-lg p-4 flex items-center transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl group animate-fade-in-up menu-card-delay-3">
                <div class="icon-bg-green mr-6"><img src="{{ asset('images/icon-guru.png') }}" alt="Icon Guru" class="h-10 w-10 transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3"></div>
                <span class="text-lg font-semibold">Cari Guru</span>
            </a>
            <a href="{{ route('public.search-sekolah') }}" class="menu-card bg-white/90 hover:bg-white text-[#125047] rounded-xl shadow-lg p-4 flex items-center transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl group animate-fade-in-up menu-card-delay-4">
                <div class="icon-bg-green mr-6"><img src="{{ asset('images/icon-siswa.png') }}" alt="Icon Sekolah" class="h-10 w-10 transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3"></div>
                <span class="text-lg font-semibold">Cari Sekolah</span>
            </a>
            <a href="{{ route('statistik') }}" class="menu-card bg-white/90 hover:bg-white text-[#125047] rounded-xl shadow-lg p-4 flex items-center transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl group animate-fade-in-up menu-card-delay-5">
                <div class="icon-bg-green mr-6"><img src="{{ asset('images/icon-stats.png') }}" alt="Icon Statistik" class="h-10 w-10 transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3"></div>
                <span class="text-lg font-semibold">Statistik</span>
            </a>
            <a href="{{ route('public.search-non-teaching-staff') }}" class="menu-card bg-white/90 hover:bg-white text-[#125047] rounded-xl shadow-lg p-4 flex items-center transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl group animate-fade-in-up menu-card-delay-6">
                <div class="icon-bg-green mr-6"><img src="{{ asset('images/icon-siswa.png') }}" alt="Icon Non Pegawai" class="h-10 w-10 transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3"></div>
                <span class="text-lg font-semibold">Cari T.Pendidik Non Pegawai</span>
            </a>
        </section>

        {{-- Artikel Terbaru Section --}}
        @if(isset($latestArticles) && $latestArticles->isNotEmpty())
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">
                <div class="bg-[#136e67] rounded-2xl shadow-lg px-8 py-5 mb-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-3xl font-bold text-white">Baca Berita/Artikel Terbaru Kami</h2>
                        <a href="{{ route('public.articles') }}" class="text-white hover:text-green-300 font-semibold">
                            Lihat Lebih Banyak
                        </a>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($latestArticles as $index => $article)
                        <a href="{{ route('public.article.detail', $article->slug) }}" class="article-card bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1 animate-slide-in-left {{ 'article-card-delay-' . ($index + 1) }}">
                            @if($article->featured_image)
                                <img src="{{ asset('storage/' . $article->featured_image) }}" alt="{{ $article->title }}" class="w-full h-48 object-cover transition-transform duration-300 hover:scale-105">
                            @else
                                <div class="w-full h-48 bg-gradient-to-br from-[#125047] to-[#0E453F] flex items-center justify-center">
                                    <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                                    </svg>
                                </div>
                            @endif
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-800 mb-2 line-clamp-2">{{ $article->title }}</h3>
                                @if($article->excerpt)
                                    <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ $article->excerpt }}</p>
                                @endif
                                <div class="flex items-center justify-between text-sm text-gray-500">
                                    <span>{{ $article->published_at->format('d M Y') }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Statistik Ringkas Section --}}
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">
            <div class="bg-[#136e67] rounded-2xl shadow-lg px-8 py-6 mb-6">
                <h2 class="text-3xl font-bold text-white mb-2">Statistik Pendidikan</h2>
                <p class="text-green-200 text-sm">Data terkini sistem pendidikan Kota Pematang Siantar</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-xl shadow-lg p-6 text-center animate-fade-in-up menu-card-delay-1">
                    <div class="icon-bg-green mx-auto mb-4 w-16 h-16 flex items-center justify-center">
                        <img src="{{ asset('images/icon-stats.png') }}" alt="Icon Sekolah" class="h-10 w-10">
                    </div>
                    <div class="text-4xl font-bold text-[#125047] mb-2 counter-number" data-target="{{ $publicStats['total_sekolah'] ?? 0 }}">0</div>
                    <div class="text-gray-600 font-semibold">Total Sekolah</div>
                </div>
                <div class="bg-white rounded-xl shadow-lg p-6 text-center animate-fade-in-up menu-card-delay-2">
                    <div class="icon-bg-green mx-auto mb-4 w-16 h-16 flex items-center justify-center">
                        <img src="{{ asset('images/icon-guru.png') }}" alt="Icon Guru" class="h-10 w-10">
                    </div>
                    <div class="text-4xl font-bold text-[#125047] mb-2 counter-number" data-target="{{ $publicStats['total_guru'] ?? 0 }}">0</div>
                    <div class="text-gray-600 font-semibold">Total Guru</div>
                </div>
                <div class="bg-white rounded-xl shadow-lg p-6 text-center animate-fade-in-up menu-card-delay-3">
                    <div class="icon-bg-green mx-auto mb-4 w-16 h-16 flex items-center justify-center">
                        <img src="{{ asset('images/icon-siswa.png') }}" alt="Icon Siswa" class="h-10 w-10">
                    </div>
                    <div class="text-4xl font-bold text-[#125047] mb-2 counter-number" data-target="{{ $publicStats['total_siswa_aktif'] ?? 0 }}">0</div>
                    <div class="text-gray-600 font-semibold">Siswa Aktif</div>
                </div>
            </div>
        </section>

        {{-- Tentang Kami Section --}}
        @if(isset($aboutSettings) && ($aboutSettings->has('about_visi') || $aboutSettings->has('about_misi') || $aboutSettings->has('about_tugas_pokok') || $aboutSettings->has('about_fungsi')))
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">
                <div class="bg-[#136e67] rounded-2xl shadow-lg px-8 py-5 mb-6">
                    <h2 class="text-3xl font-bold text-white">Tentang Kami</h2>
                </div>
                <div class="bg-white rounded-xl shadow-lg p-8">
                    @if($aboutSettings->has('about_visi') && $aboutSettings['about_visi']->value)
                        <div class="mb-6">
                            <h3 class="text-2xl font-bold text-[#125047] mb-3">Visi</h3>
                            <p class="text-gray-700 leading-relaxed">{{ $aboutSettings['about_visi']->value }}</p>
                        </div>
                    @endif
                    @if($aboutSettings->has('about_misi') && $aboutSettings['about_misi']->value)
                        <div class="mb-6">
                            <h3 class="text-2xl font-bold text-[#125047] mb-3">Misi</h3>
                            <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $aboutSettings['about_misi']->value }}</p>
                        </div>
                    @endif
                    @if($aboutSettings->has('about_tugas_pokok') && $aboutSettings['about_tugas_pokok']->value)
                        <div class="mb-6">
                            <h3 class="text-2xl font-bold text-[#125047] mb-3">Tugas Pokok</h3>
                            <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $aboutSettings['about_tugas_pokok']->value }}</p>
                        </div>
                    @endif
                    @if($aboutSettings->has('about_fungsi') && $aboutSettings['about_fungsi']->value)
                        <div>
                            <h3 class="text-2xl font-bold text-[#125047] mb-3">Fungsi</h3>
                            <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $aboutSettings['about_fungsi']->value }}</p>
                        </div>
                    @endif
                </div>
            </section>
        @endif

        {{-- Galeri Foto Section --}}
        @if(isset($latestGalleries) && $latestGalleries->isNotEmpty())
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">
                <div class="bg-[#136e67] rounded-2xl shadow-lg px-8 py-5 mb-6">
                    <h2 class="text-3xl font-bold text-white">Galeri Foto</h2>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    @foreach($latestGalleries as $index => $gallery)
                        <div class="group relative overflow-hidden rounded-xl shadow-lg cursor-pointer animate-fade-in-up {{ 'menu-card-delay-' . ($index + 1) }}">
                            <img src="{{ asset('storage/' . $gallery->image) }}" alt="{{ $gallery->title }}" 
                                 class="w-full h-48 object-cover transition-transform duration-300 group-hover:scale-110">
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-opacity duration-300 flex items-center justify-center">
                                <div class="text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300 text-center px-2">
                                    <p class="font-semibold text-sm">{{ $gallery->title }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Kontak & Lokasi Section --}}
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 mb-12">
            <div class="bg-[#136e67] rounded-2xl shadow-lg px-8 py-5 mb-6">
                <h2 class="text-3xl font-bold text-white">Kontak & Lokasi</h2>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <h3 class="text-2xl font-bold text-[#125047] mb-6">Informasi Kontak</h3>
                    <div class="space-y-4">
                        @if(isset($contactSettings['contact_address']) && $contactSettings['contact_address']->value)
                            <div class="flex items-start">
                                <svg class="w-6 h-6 text-[#125047] mr-4 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <div>
                                    <p class="font-semibold text-gray-800 mb-1">Alamat</p>
                                    <p class="text-gray-600">{{ $contactSettings['contact_address']->value }}</p>
                                </div>
                            </div>
                        @endif
                        @if(isset($contactSettings['contact_phone']) && $contactSettings['contact_phone']->value)
                            <div class="flex items-start">
                                <svg class="w-6 h-6 text-[#125047] mr-4 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <div>
                                    <p class="font-semibold text-gray-800 mb-1">Telepon</p>
                                    <p class="text-gray-600">{{ $contactSettings['contact_phone']->value }}</p>
                                </div>
                            </div>
                        @endif
                        @if(isset($contactSettings['contact_email']) && $contactSettings['contact_email']->value)
                            <div class="flex items-start">
                                <svg class="w-6 h-6 text-[#125047] mr-4 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <div>
                                    <p class="font-semibold text-gray-800 mb-1">Email</p>
                                    <p class="text-gray-600">{{ $contactSettings['contact_email']->value }}</p>
                                </div>
                            </div>
                        @endif
                        @if(isset($contactSettings['contact_hours']) && $contactSettings['contact_hours']->value)
                            <div class="flex items-start">
                                <svg class="w-6 h-6 text-[#125047] mr-4 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <p class="font-semibold text-gray-800 mb-1">Jam Operasional</p>
                                    <p class="text-gray-600 whitespace-pre-line">{{ $contactSettings['contact_hours']->value }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <h3 class="text-2xl font-bold text-[#125047] mb-6">Lokasi</h3>
                    @if(isset($contactSettings['contact_map_url']) && $contactSettings['contact_map_url']->value)
                        <div class="rounded-lg overflow-hidden">
                            <iframe src="{{ $contactSettings['contact_map_url']->value }}" 
                                    width="100%" 
                                    height="400" 
                                    style="border:0;" 
                                    allowfullscreen="" 
                                    loading="lazy" 
                                    referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>
                    @else
                        <div class="bg-gray-100 rounded-lg h-96 flex items-center justify-center">
                            <p class="text-gray-500">Peta lokasi belum dikonfigurasi</p>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif
@endauth

{{-- JavaScript untuk Animasi --}}
<script>
    // Number Counter Animation
    function animateCounter(element) {
        const target = parseInt(element.getAttribute('data-target'));
        const duration = 2000; // 2 seconds
        const increment = target / (duration / 16); // 60fps
        let current = 0;

        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                element.textContent = target.toLocaleString('id-ID');
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current).toLocaleString('id-ID');
            }
        }, 16);
    }

    // Scroll-triggered Animation
    function initScrollAnimations() {
        const elements = document.querySelectorAll('.animate-on-scroll');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        elements.forEach(el => observer.observe(el));
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', () => {
        // Counter animation - trigger when stat cards are visible
        const statSection = document.querySelector('.counter-number');
        if (statSection) {
            const counterObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        document.querySelectorAll('.counter-number').forEach(counter => {
                            if (!counter.classList.contains('counted')) {
                                counter.classList.add('counted');
                                animateCounter(counter);
                            }
                        });
                        counterObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.5 });
            
            const statCards = document.querySelectorAll('.bg-white.rounded-xl.shadow-lg.p-6.text-center');
            statCards.forEach(card => counterObserver.observe(card));
        }

        // Scroll animations
        initScrollAnimations();
    });
</script>
@endsection
