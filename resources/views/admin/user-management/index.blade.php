@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-[#125047] py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumb & Session Message --}}
            <nav class="flex items-center space-x-2 text-white mb-6">
                <a href="{{ route('home') }}" class="hover:text-green-300">Dashboard</a>
                <span class="text-gray-300">&gt;</span>
                <span class="border-b-2 border-white">Manajemen Pengguna</span>
            </nav>

            {{-- Pesan sukses/error --}}
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                    {{ session('success') }}
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
                <h2 class="text-3xl font-bold text-white mx-auto">Manajemen Pengguna Sistem</h2>
            </div>

            {{-- Filter Section --}}
            <div class="bg-[#0d524a] rounded-xl p-6 mb-8">
                <h2 class="text-2xl font-bold text-white mb-6">Filter Pengguna</h2>
                <form action="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.user-management.index') : route('dinas.user-management.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Role</label>
                        <div class="relative">
                            <select name="role" class="block w-full bg-white rounded-lg border-0 py-2.5 pl-4 pr-10 focus:ring-2 focus:ring-green-400">
                                <option value="">Semua Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ (request('role') == $role->name) ? 'selected' : '' }}>
                                        {{ ucfirst($role->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">&nbsp;</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..."
                                class="block w-full bg-white rounded-lg border-0 py-2.5 pl-4 pr-10 focus:ring-2 focus:ring-green-400">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <button type="submit" class="text-gray-700 hover:text-gray-900">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">&nbsp;</label>
                        <button type="submit" class="w-full bg-green-600 text-white rounded-lg py-2.5 hover:bg-green-700 transition">
                            Filter
                        </button>
                    </div>
                </form>
            </div>

            {{-- Daftar Pengguna Section --}}
            <div class="bg-[#0d524a] rounded-xl p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-white">Daftar Pengguna</h2>
                    <div class="flex space-x-3">
                        <a href="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.user-management.create') : route('dinas.user-management.create') }}" class="inline-flex items-center px-4 py-2 bg-white rounded-lg font-semibold text-[#0d524a] hover:bg-green-50 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Pengguna
                        </a>
                    </div>
                </div>

                {{-- Users Table --}}
                @if($users->isEmpty())
                    <div class="bg-[#09443c] rounded-xl shadow-lg p-8 text-center">
                        <div class="mb-4">
                            <svg class="w-16 h-16 mx-auto text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-2">Tidak ada data pengguna</h3>
                        <p class="text-white/70">Silahkan tambah data pengguna atau ubah filter pencarian</p>
                    </div>
                @else
                    @php
                        $rows = $users->map(function($user){
                            $roles = $user->roles->pluck('name')->map(function($role) {
                                return ucfirst($role);
                            })->join(', ');

                            $actions = '<div class="flex space-x-2">';
                            $actions .= '<a href="'.(auth()->user()->hasRole('admin_sekolah') ? route('sekolah.user-management.show', $user) : route('dinas.user-management.show', $user)).'" class="text-green-300 hover:underline">Detail</a>';

                            // Cek apakah user adalah admin dinas pertama
                            $isFirstAdminDinas = false;
                            if ($user->hasRole('admin_dinas')) {
                                $firstAdminDinas = \App\Models\User::whereHas('roles', function ($query) {
                                    $query->where('name', 'admin_dinas');
                                })->orderBy('id')->first();
                                $isFirstAdminDinas = $firstAdminDinas && $firstAdminDinas->id === $user->id;
                            }

                            if (!$isFirstAdminDinas && $user->id !== auth()->id()) {
                                $actions .= '<a href="'.(auth()->user()->hasRole('admin_sekolah') ? route('sekolah.user-management.edit', $user) : route('dinas.user-management.edit', $user)).'" class="text-yellow-300 hover:underline">Edit</a>';

                                $actions .= '<button type="button" onclick="openDeleteModal('.$user->id.', \''.$user->name.'\')" class="text-red-300 hover:underline">Hapus</button>';
                            }

                            $actions .= '</div>';

                            return [
                                $user->name,
                                $user->email,
                                $roles,
                                $user->school ? $user->school->name : '-',
                                $actions
                            ];
                        });
                    @endphp
                    <div class="bg-[#09443c] rounded-xl shadow-lg px-0 py-8">
                        <x-public.data-table :headers="['Nama','Email','Role','Sekolah','Aksi']" :rows="$rows" />
                    </div>
                @endif

                {{-- Pagination --}}
                @if($users->hasPages())
                    <div class="mt-6">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-4">Konfirmasi Hapus</h3>
                <div class="mt-2 px-7 pt-4">
                    <p class="text-sm text-gray-500" id="deleteMessage">
                        Apakah Anda yakin ingin menghapus pengguna ini?
                    </p>
                </div>
                <div class="items-center px-4 py-3 mt-4">
                    <form id="deleteForm" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300 mr-2">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openDeleteModal(userId, userName) {
            const modal = document.getElementById('deleteModal');
            const form = document.getElementById('deleteForm');
            const message = document.getElementById('deleteMessage');

            message.textContent = `Apakah Anda yakin ingin menghapus pengguna "${userName}"?`;

            // Gunakan route yang benar dengan parameter yang tepat
            const baseUrl = '{{ auth()->user()->hasRole("admin_sekolah") ? route("sekolah.user-management.destroy", ["user" => "ID_PLACEHOLDER"]) : route("dinas.user-management.destroy", ["user" => "ID_PLACEHOLDER"]) }}';
            form.action = baseUrl.replace('ID_PLACEHOLDER', userId);

            modal.classList.remove('hidden');
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            modal.classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
    </script>
@endsection
