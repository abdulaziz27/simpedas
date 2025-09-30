@extends('layouts.public')

@section('content')

@guest
    {{-- Tampilan untuk Guest / Penggunu Umum --}}
    <x-public.hero-section />
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <img src="{{ asset('images/banner-pemko.png') }}" alt="Banner Pemko" class="rounded-xl shadow-lg w-full">
    </section>
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
        <a href="{{ route('public.search-guru') }}" class="bg-white/90 hover:bg-white text-[#125047] rounded-xl shadow-lg p-4 flex items-center transition">
            <div class="icon-bg-green mr-6"><img src="{{ asset('images/icon-guru.png') }}" alt="Icon Guru" class="h-10 w-10"></div>
            <span class="text-lg font-semibold">Lihat Data Guru</span>
        </a>
        <a href="{{ route('public.search-siswa') }}" class="bg-white/90 hover:bg-white text-[#125047] rounded-xl shadow-lg p-4 flex items-center transition">
            <div class="icon-bg-green mr-6"><img src="{{ asset('images/icon-siswa.png') }}" alt="Icon Siswa" class="h-10 w-10"></div>
            <span class="text-lg font-semibold">Cari Siswa</span>
        </a>
        <a href="{{ route('public.search-guru') }}" class="bg-white/90 hover:bg-white text-[#125047] rounded-xl shadow-lg p-4 flex items-center transition">
            <div class="icon-bg-green mr-6"><img src="{{ asset('images/icon-guru.png') }}" alt="Icon Guru" class="h-10 w-10"></div>
            <span class="text-lg font-semibold">Cari Guru</span>
        </a>
        <a href="{{ route('public.search-sekolah') }}" class="bg-white/90 hover:bg-white text-[#125047] rounded-xl shadow-lg p-4 flex items-center transition">
            <div class="icon-bg-green mr-6"><img src="{{ asset('images/icon-siswa.png') }}" alt="Icon Sekolah" class="h-10 w-10"></div>
            <span class="text-lg font-semibold">Cari Sekolah</span>
        </a>
        <a href="{{ route('statistik') }}" class="bg-white/90 hover:bg-white text-[#125047] rounded-xl shadow-lg p-4 flex items-center transition">
            <div class="icon-bg-green mr-6"><img src="{{ asset('images/icon-stats.png') }}" alt="Icon Statistik" class="h-10 w-10"></div>
            <span class="text-lg font-semibold">Statistik</span>
        </a>
        <a href="{{ route('public.search-non-teaching-staff') }}" class="bg-white/90 hover:bg-white text-[#125047] rounded-xl shadow-lg p-4 flex items-center transition">
            <div class="icon-bg-green mr-6"><img src="{{ asset('images/icon-siswa.png') }}" alt="Icon Non Pegawai" class="h-10 w-10"></div>
            <span class="text-lg font-semibold">Cari T.Pendidik Non Pegawai</span>
        </a>
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

                    <a href="{{ route('guru.students') }}" class="bg-[#0E453F] rounded-xl shadow-lg overflow-hidden hover:bg-[#0a403a] transition">
                        <div class="p-6 text-center">
                            <div class="icon-bg-green mx-auto mb-4 w-16 h-16 flex items-center justify-center rounded-full">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2">Data Siswa</h3>
                            <p class="text-gray-300 text-sm">Lihat data siswa yang Anda ajar</p>
                        </div>
                    </a>
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
                    <div class="bg-white/90 hover:bg-white text-[#125047] rounded-xl shadow-lg p-6 flex items-center transition">
                        <div class="icon-bg-green mr-6">
                            <img src="{{ asset('images/icon-stats.png') }}" alt="Icon Stats" class="h-12 w-12">
                        </div>
                        <div>
                            <div class="text-3xl font-bold">{{ $stats['total_sekolah'] ?? 0 }}</div>
                            <div class="text-sm text-gray-600">Jumlah Sekolah</div>
                        </div>
                    </div>
                    <div class="bg-white/90 hover:bg-white text-[#125047] rounded-xl shadow-lg p-6 flex items-center transition">
                        <div class="icon-bg-green mr-6">
                            <img src="{{ asset('images/icon-guru.png') }}" alt="Icon Guru" class="h-12 w-12">
                        </div>
                        <div>
                            <div class="text-3xl font-bold">{{ $stats['total_guru'] ?? 0 }}</div>
                            <div class="text-sm text-gray-600">Jumlah Guru</div>
                        </div>
                    </div>
                    <div class="bg-white/90 hover:bg-white text-[#125047] rounded-xl shadow-lg p-6 flex items-center transition">
                        <div class="icon-bg-green mr-6">
                            <img src="{{ asset('images/icon-siswa.png') }}" alt="Icon Siswa" class="h-12 w-12">
                        </div>
                        <div>
                            <div class="text-3xl font-bold">{{ $stats['total_siswa_aktif'] ?? 0 }}</div>
                            <div class="text-sm text-gray-600">Jumlah Siswa Aktif</div>
                        </div>
                    </div>

                    <!-- Row 2 - Statistics Cards -->
                    <div class="bg-white/90 hover:bg-white text-[#125047] rounded-xl shadow-lg p-6 flex items-center transition">
                        <div class="icon-bg-green mr-6">
                            <img src="{{ asset('images/icon-siswa.png') }}" alt="Icon Siswa" class="h-12 w-12">
                        </div>
                        <div>
                            <div class="text-3xl font-bold">{{ $stats['total_siswa_tamat'] ?? 0 }}</div>
                            <div class="text-sm text-gray-600">Jumlah Siswa Tamat</div>
                        </div>
                    </div>
                    <a href="{{ route('statistik.detail', 'sekolah') }}" class="bg-white/90 hover:bg-white text-[#125047] rounded-xl shadow-lg p-6 flex items-center transition cursor-pointer">
                        <div class="icon-bg-green mr-6">
                            <img src="{{ asset('images/icon-guru.png') }}" alt="Icon Statistik" class="h-12 w-12">
                        </div>
                        <div>
                            <div class="text-xl font-bold">Statistik</div>
                            <div class="text-sm text-gray-600">Lihat statistik lengkap</div>
                        </div>
                    </a>
                    <a href="{{ route('dinas.user-management.index') }}" class="bg-white/90 hover:bg-white text-[#125047] rounded-xl shadow-lg p-6 flex items-center transition cursor-pointer">
                        <div class="icon-bg-green mr-6">
                            <img src="{{ asset('images/icon-siswa.png') }}" alt="Icon Validasi" class="h-12 w-12">
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
                        <a href="{{ route('dinas.schools.index') }}" class="bg-[#0E453F] hover:bg-[#0a403a] text-white rounded-xl shadow-xl p-6 flex items-center justify-between transition group border-2 border-transparent hover:border-blue-400 hover:shadow-blue-400/50">
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-white">Manajemen</h3>
                                <h3 class="text-xl font-bold text-white">Sekolah</h3>
                            </div>
                            <div class="flex-shrink-0 ml-4">
                                <img src="{{ asset('images/icon-stats.png') }}" alt="Icon Sekolah" class="h-20 w-20 group-hover:scale-110 transition-transform duration-300">
                            </div>
                        </a>
                        <a href="{{ route('dinas.teachers.index') }}" class="bg-[#0E453F] hover:bg-[#0a403a] text-white rounded-xl shadow-xl p-6 flex items-center justify-between transition group border-2 border-transparent hover:border-blue-400 hover:shadow-blue-400/50">
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-white">Manajemen</h3>
                                <h3 class="text-xl font-bold text-white">Guru</h3>
                            </div>
                            <div class="flex-shrink-0 ml-4">
                                <img src="{{ asset('images/icon-guru.png') }}" alt="Icon Guru" class="h-20 w-20 group-hover:scale-110 transition-transform duration-300">
                            </div>
                        </a>
                        <a href="{{ route('dinas.students.index') }}" class="bg-[#0E453F] hover:bg-[#0a403a] text-white rounded-xl shadow-xl p-6 flex items-center justify-between transition group border-2 border-transparent hover:border-blue-400 hover:shadow-blue-400/50">
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
            <a href="{{ route('public.search-guru') }}" class="bg-white/90 hover:bg-white text-[#125047] rounded-xl shadow-lg p-4 flex items-center transition">
                <div class="icon-bg-green mr-6"><img src="{{ asset('images/icon-guru.png') }}" alt="Icon Guru" class="h-10 w-10"></div>
                <span class="text-lg font-semibold">Lihat Data Guru</span>
            </a>
            <a href="{{ route('public.search-siswa') }}" class="bg-white/90 hover:bg-white text-[#125047] rounded-xl shadow-lg p-4 flex items-center transition">
                <div class="icon-bg-green mr-6"><img src="{{ asset('images/icon-siswa.png') }}" alt="Icon Siswa" class="h-10 w-10"></div>
                <span class="text-lg font-semibold">Cari Siswa</span>
            </a>
            <a href="{{ route('public.search-guru') }}" class="bg-white/90 hover:bg-white text-[#125047] rounded-xl shadow-lg p-4 flex items-center transition">
                <div class="icon-bg-green mr-6"><img src="{{ asset('images/icon-guru.png') }}" alt="Icon Guru" class="h-10 w-10"></div>
                <span class="text-lg font-semibold">Cari Guru</span>
            </a>
            <a href="{{ route('public.search-sekolah') }}" class="bg-white/90 hover:bg-white text-[#125047] rounded-xl shadow-lg p-4 flex items-center transition">
                <div class="icon-bg-green mr-6"><img src="{{ asset('images/icon-siswa.png') }}" alt="Icon Sekolah" class="h-10 w-10"></div>
                <span class="text-lg font-semibold">Cari Sekolah</span>
            </a>
            <a href="{{ route('statistik') }}" class="bg-white/90 hover:bg-white text-[#125047] rounded-xl shadow-lg p-4 flex items-center transition">
                <div class="icon-bg-green mr-6"><img src="{{ asset('images/icon-stats.png') }}" alt="Icon Statistik" class="h-10 w-10"></div>
                <span class="text-lg font-semibold">Statistik</span>
            </a>
            <a href="{{ route('public.search-non-teaching-staff') }}" class="bg-white/90 hover:bg-white text-[#125047] rounded-xl shadow-lg p-4 flex items-center transition">
                <div class="icon-bg-green mr-6"><img src="{{ asset('images/icon-siswa.png') }}" alt="Icon Non Pegawai" class="h-10 w-10"></div>
                <span class="text-lg font-semibold">Cari T.Pendidik Non Pegawai</span>
            </a>
        </section>
    @endif
@endauth
@endsection
