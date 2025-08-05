@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-[#125047] py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <nav class="flex items-center space-x-2 text-white mb-6">
                <a href="{{ route('home') }}" class="hover:text-green-300">Dashboard</a>
                <span class="text-gray-300">&gt;</span>
                <a href="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.user-management.index') : route('dinas.user-management.index') }}" class="hover:text-green-300">Manajemen Pengguna</a>
                <span class="text-gray-300">&gt;</span>
                <span class="border-b-2 border-white">Detail Pengguna</span>
            </nav>

            {{-- Header Card --}}
            <div class="bg-[#136e67] rounded-2xl shadow-lg px-8 py-5 mb-8 border-b-4 border-white flex items-center">
                <h2 class="text-3xl font-bold text-white mx-auto">Detail Pengguna</h2>
            </div>

            {{-- Detail Card --}}
            <div class="bg-[#0d524a] rounded-xl p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Nama Lengkap</label>
                        <p class="text-white text-lg">{{ $user->name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Email</label>
                        <p class="text-white text-lg">{{ $user->email }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Role</label>
                        <p class="text-white text-lg">
                            @foreach($user->roles as $role)
                                <span class="inline-block bg-green-600 text-white px-3 py-1 rounded-full text-sm mr-2">
                                    {{ ucfirst($role->name) }}
                                </span>
                            @endforeach
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Sekolah</label>
                        <p class="text-white text-lg">{{ $user->school ? $user->school->name : '-' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Tanggal Dibuat</label>
                        <p class="text-white text-lg">{{ $user->created_at ? $user->created_at->format('d F Y H:i') : '-' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Terakhir Diupdate</label>
                        <p class="text-white text-lg">{{ $user->updated_at ? $user->updated_at->format('d F Y H:i') : '-' }}</p>
                    </div>
                </div>

                <div class="flex justify-end space-x-4 mt-8">
                    <a href="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.user-management.index') : route('dinas.user-management.index') }}"
                       class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                        Kembali
                    </a>

                    @php
                        // Cek apakah user adalah admin dinas pertama
                        $isFirstAdminDinas = false;
                        if ($user->hasRole('admin_dinas')) {
                            $firstAdminDinas = \App\Models\User::whereHas('roles', function ($query) {
                                $query->where('name', 'admin_dinas');
                            })->orderBy('id')->first();
                            $isFirstAdminDinas = $firstAdminDinas && $firstAdminDinas->id === $user->id;
                        }
                    @endphp

                    @if(!$isFirstAdminDinas && $user->id !== auth()->id())
                        <a href="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.user-management.edit', $user) : route('dinas.user-management.edit', $user) }}"
                           class="px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                            Edit
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
