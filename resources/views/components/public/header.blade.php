@props(['active' => null])
<nav class="bg-[#125047] shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <!-- Logo & Brand -->
            <div class="flex items-center space-x-2">
                <a href="/">
                    <img src="{{ asset('images/logo-simpedas.svg') }}" alt="Logo" class="h-8 w-auto">
                </a>
            </div>
            <!-- Navigation Links & User Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                @guest
                    <a href="/" class="px-3 py-2 rounded transition {{ request()->routeIs('home') ? 'text-[#6ee7b7]' : 'text-white hover:text-[#6ee7b7]' }}">Dashboard</a>
                    <a href="{{ route('public.search-siswa') }}" class="px-3 py-2 rounded transition {{ request()->routeIs('public.search-siswa') ? 'text-[#6ee7b7]' : 'text-white hover:text-[#6ee7b7]' }}">Data Siswa</a>
                    <a href="{{ route('public.search-guru') }}" class="px-3 py-2 rounded transition {{ request()->routeIs('public.search-guru') ? 'text-[#6ee7b7]' : 'text-white hover:text-[#6ee7b7]' }}">Data Guru</a>
                    <a href="{{ route('login') }}" class="ml-4 px-4 py-2 rounded bg-white text-[#125047] font-bold hover:bg-green-100 transition">Login</a>
                @endguest

                @auth
                    @role('admin_dinas')
                        <a href="/" class="px-3 py-2 rounded transition {{ request()->routeIs('home') ? 'text-[#6ee7b7]' : 'text-white hover:text-[#6ee7b7]' }}">Dashboard</a>
                        <div class="relative group">
                            <button class="px-3 py-2 rounded transition text-white hover:text-[#6ee7b7] font-semibold flex items-center">
                                Manajemen Data
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-10">
                                <div class="py-1">
                                    <a href="{{ route('admin.schools.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Manajemen Sekolah</a>
                                    <a href="{{ route('admin.teachers.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Manajemen Guru</a>
                                    <a href="{{ route('admin.students.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Manajemen Siswa</a>
                                    <a href="{{ route('admin.non-teaching-staff.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Manajemen Staf</a>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('statistik.detail', 'sekolah') }}" class="px-3 py-2 rounded transition text-white hover:text-[#6ee7b7] font-semibold">Statistik</a>
                        <a href="{{ route('admin.reports.index') }}" class="px-3 py-2 rounded transition text-white hover:text-[#6ee7b7] font-semibold">Laporan</a>
                    @endrole

                    @role('admin_sekolah')
                        <a href="/" class="px-3 py-2 rounded transition {{ request()->routeIs('home') ? 'text-[#6ee7b7]' : 'text-white hover:text-[#6ee7b7]' }}">Dashboard</a>
                        <div class="relative group">
                            <button class="px-3 py-2 rounded transition text-white hover:text-[#6ee7b7] font-semibold flex items-center">
                                Manajemen Data
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-10">
                                <div class="py-1">
                                    <a href="{{ route('sekolah.students.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Manajemen Siswa</a>
                                    <a href="{{ route('sekolah.teachers.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Manajemen Guru</a>
                                    <a href="{{ route('sekolah.non-teaching-staff.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Manajemen Staf</a>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('statistik.detail', 'sekolah') }}" class="px-3 py-2 rounded transition text-white hover:text-[#6ee7b7] font-semibold">Statistik</a>
                        {{-- <a href="{{ route('admin.reports.index') }}" class="px-3 py-2 rounded transition text-white hover:text-[#6ee7b7] font-semibold">Laporan</a> --}}
                    @endrole

                    @role('guru')
                        <a href="/" class="px-3 py-2 rounded transition {{ request()->routeIs('home') ? 'text-[#6ee7b7]' : 'text-white hover:text-[#6ee7b7]' }}">Dashboard</a>
                        <a href="{{ route('guru.profile.show') }}" class="px-3 py-2 rounded transition {{ request()->routeIs('guru.profile.show') ? 'text-[#6ee7b7]' : 'text-white hover:text-[#6ee7b7]' }}">Profil Saya</a>
                        <a href="{{ route('guru.documents') }}" class="px-3 py-2 rounded transition {{ request()->routeIs('guru.documents') ? 'text-[#6ee7b7]' : 'text-white hover:text-[#6ee7b7]' }}">Dokumen Pribadi</a>
                    @endrole

                    @hasanyrole('admin_dinas|admin_sekolah')
                        {{-- Tidak ada menu Profil Saya di navigation utama untuk admin --}}
                    @endhasanyrole

                    <!-- User Dropdown -->
                    <div class="relative ml-4 group">
                        <button class="flex items-center text-white hover:text-[#6ee7b7] font-semibold focus:outline-none" id="user-menu-button">
                            {{ Auth::user()->name }}
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                </button>
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-10" id="user-menu">
                            <div class="py-1">
                                @role('guru')
                                    <a href="{{ route('guru.profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                @else
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                @endrole
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endauth
            </div>
            <!-- Mobile Menu Button -->
            <div class="md:hidden">
                <button id="mobile-menu-button" class="text-white focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
    <!-- Mobile Menu -->
    <div id="mobile-menu" class="md:hidden hidden px-4 pb-4 space-y-2 bg-[#125047]">
        @guest
            <a href="/" class="block px-3 py-2 rounded {{ request()->routeIs('home') ? 'text-[#6ee7b7]' : 'text-white hover:text-[#6ee7b7]' }}">Dashboard</a>
            <a href="{{ route('public.search-siswa') }}" class="block px-3 py-2 rounded {{ request()->routeIs('public.search-siswa') ? 'text-[#6ee7b7]' : 'text-white hover:text-[#6ee7b7]' }}">Data Siswa</a>
            <a href="{{ route('public.search-guru') }}" class="block px-3 py-2 rounded {{ request()->routeIs('public.search-guru') ? 'text-[#6ee7b7]' : 'text-white hover:text-[#6ee7b7]' }}">Data Guru</a>
            <a href="{{ route('login') }}" class="block mt-2 px-4 py-2 rounded bg-white text-[#125047] font-bold hover:bg-green-100 transition">Login</a>
        @endguest

        @auth
            @role('admin_dinas')
                <a href="/" class="block px-3 py-2 rounded {{ request()->routeIs('home') ? 'text-[#6ee7b7]' : 'text-white hover:text-[#6ee7b7]' }}">Dashboard</a>
                <div class="mt-2">
                    <div class="font-bold text-white mb-1">Manajemen Data</div>
                    <a href="{{ route('admin.schools.index') }}" class="block px-4 py-2 rounded hover:bg-[#6ee7b7] hover:text-[#125047] font-bold text-[#125047] bg-white mb-1">Manajemen Sekolah</a>
                    <a href="{{ route('admin.teachers.index') }}" class="block px-4 py-2 rounded hover:bg-[#6ee7b7] hover:text-[#125047] font-bold text-[#125047] bg-white mb-1">Manajemen Guru</a>
                    <a href="{{ route('admin.students.index') }}" class="block px-4 py-2 rounded hover:bg-[#6ee7b7] hover:text-[#125047] font-bold text-[#125047] bg-white mb-1">Manajemen Siswa</a>
                    <a href="{{ route('admin.non-teaching-staff.index') }}" class="block px-4 py-2 rounded hover:bg-[#6ee7b7] hover:text-[#125047] font-bold text-[#125047] bg-white">Manajemen Staf</a>
                </div>
                <a href="{{ route('statistik.detail', 'sekolah') }}" class="block mt-2 px-4 py-2 rounded text-white hover:text-[#6ee7b7] font-semibold transition">Statistik</a>
                <a href="{{ route('admin.reports.index') }}" class="block mt-2 px-4 py-2 rounded text-white hover:text-[#6ee7b7] font-semibold transition">Laporan</a>
            @endrole

            @role('admin_sekolah')
                <a href="/" class="block px-3 py-2 rounded {{ request()->routeIs('home') ? 'text-[#6ee7b7]' : 'text-white hover:text-[#6ee7b7]' }}">Dashboard</a>
                <div class="mt-2">
                    <div class="font-bold text-white mb-1">Manajemen Data</div>
                    <a href="{{ route('sekolah.students.index') }}" class="block px-4 py-2 rounded hover:bg-[#6ee7b7] hover:text-[#125047] font-bold text-[#125047] bg-white mb-1">Manajemen Siswa</a>
                    <a href="{{ route('sekolah.teachers.index') }}" class="block px-4 py-2 rounded hover:bg-[#6ee7b7] hover:text-[#125047] font-bold text-[#125047] bg-white mb-1">Manajemen Guru</a>
                    <a href="{{ route('sekolah.non-teaching-staff.index') }}" class="block px-4 py-2 rounded hover:bg-[#6ee7b7] hover:text-[#125047] font-bold text-[#125047] bg-white">Manajemen Staf</a>
                </div>
                <a href="{{ route('statistik.detail', 'sekolah') }}" class="block mt-2 px-4 py-2 rounded text-white hover:text-[#6ee7b7] font-semibold transition">Statistik</a>
                {{-- <a href="{{ route('admin.reports.index') }}" class="block mt-2 px-4 py-2 rounded text-white hover:text-[#6ee7b7] font-semibold transition">Laporan</a> --}}
            @endrole

            @role('guru')
                <a href="/" class="block px-3 py-2 rounded {{ request()->routeIs('home') ? 'text-[#6ee7b7]' : 'text-white hover:text-[#6ee7b7]' }}">Dashboard</a>
                <a href="{{ route('guru.profile.show') }}" class="block px-3 py-2 rounded {{ request()->routeIs('guru.profile.show') ? 'text-[#6ee7b7]' : 'text-white hover:text-[#6ee7b7]' }}">Profil Saya</a>
                <a href="{{ route('guru.documents') }}" class="block px-3 py-2 rounded {{ request()->routeIs('guru.documents') ? 'text-[#6ee7b7]' : 'text-white hover:text-[#6ee7b7]' }}">Dokumen Pribadi</a>
            @endrole

            @hasanyrole('admin_dinas|admin_sekolah')
                {{-- Tidak ada menu Profil Saya di navigation utama untuk admin --}}
            @endhasanyrole

            <div class="mt-2 px-4 py-2 bg-white rounded">
                <div class="font-bold text-[#125047]">{{ Auth::user()->name }}</div>
                <div class="text-sm text-gray-600">{{ Auth::user()->email }}</div>
                @role('guru')
                    <a href="{{ route('guru.profile.show') }}" class="block mt-2 px-4 py-2 rounded bg-[#125047] text-white font-bold hover:bg-[#0E453F] transition">Profile</a>
                @else
                    <a href="{{ route('profile.edit') }}" class="block mt-2 px-4 py-2 rounded bg-[#125047] text-white font-bold hover:bg-[#0E453F] transition">Profile</a>
                @endrole
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 rounded bg-red-600 text-white font-bold hover:bg-red-700 transition">Logout</button>
                </form>
            </div>
        @endauth
    </div>
    <script>
        // Simple toggle for mobile menu
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('mobile-menu-button');
            const menu = document.getElementById('mobile-menu');
            if (btn) {
                btn.addEventListener('click', () => {
                    menu.classList.toggle('hidden');
                });
            }
        });
    </script>
</nav>
