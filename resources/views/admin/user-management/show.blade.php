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

        {{-- Action Buttons --}}
        <div class="mt-8 pt-8 border-t border-gray-700 print:border-gray-300 print:hidden">
            <div class="flex flex-wrap gap-4 justify-center">
                @if(!$isFirstAdminDinas && $user->id !== auth()->id())
                    <a href="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.user-management.edit', $user) : route('dinas.user-management.edit', $user) }}"
                       class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Data
                    </a>
                @endif

                <a href="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.user-management.print', $user) : route('dinas.user-management.print', $user) }}" target="_blank"
                        class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Data
                </a>

                @if(!$isFirstAdminDinas && $user->id !== auth()->id())
                    <button onclick="openDeleteModal('user', {{ $user->id }})"
                            class="inline-flex items-center px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Hapus Data
                    </button>
                @endif
            </div>
        </div>
                </div>
            </div>
        </div>
    </div>

{{-- Delete Confirmation Modal --}}
<div id="deleteModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeDeleteModal()"></div>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Hapus Data Pengguna</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500" id="modal-message">Apakah Anda yakin ingin menghapus data pengguna ini? Tindakan ini tidak dapat dibatalkan.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" id="confirmDelete" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Hapus
                </button>
                <button type="button" onclick="closeDeleteModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let deleteType = '';
let deleteId = '';

function openDeleteModal(type, id) {
    deleteType = type;
    deleteId = id;

    const modal = document.getElementById('deleteModal');
    modal.classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

document.getElementById('confirmDelete').addEventListener('click', function() {
    if (deleteType === 'user') {
        // Create delete form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ auth()->user()->hasRole("admin_sekolah") ? route("sekolah.user-management.destroy", ":id") : route("dinas.user-management.destroy", ":id") }}'.replace(':id', deleteId);

        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        // Add method override
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);

        document.body.appendChild(form);
        form.submit();
    }

    closeDeleteModal();
});
</script>
@endsection
