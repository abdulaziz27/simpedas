@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#125047] py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Page Header --}}
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-white">Profil Saya</h1>
            <p class="text-green-200 text-lg">Kelola informasi pribadi, foto, dan dokumen Anda.</p>
        </div>

        {{-- Success/Error Messages --}}
        @if (session('success'))
            <div id="successMessage" class="mb-6 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg text-center">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div id="errorMessage" class="mb-6 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg text-center">
                {{ session('error') }}
            </div>
        @endif

        {{-- Profile Photo Card --}}
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
            <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
                <div class="flex-shrink-0">
                    @if($teacher->photo)
                        <img src="{{ asset('storage/' . $teacher->photo) }}"
                            alt="Foto {{ $teacher->full_name }}"
                            class="h-40 w-40 rounded-full object-cover border-4 border-gray-200">
                    @else
                        <div class="h-40 w-40 rounded-full bg-gray-300 flex items-center justify-center border-4 border-gray-200">
                            <svg class="h-20 w-20 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path>
                            </svg>
                        </div>
                    @endif
                </div>
                <div class="text-center md:text-left flex-1">
                    <h3 class="text-3xl font-bold text-gray-800">{{ $teacher->full_name }}</h3>
                    <p class="text-[#125047] text-xl mb-4">{{ $teacher->subjects ?? '-' }}</p>
                    <form action="{{ route('guru.profile.photo.update') }}" method="POST" enctype="multipart/form-data" class="inline-block">
                        @csrf
                            <input type="file" name="photo" id="photo" accept="image/*" class="hidden" onchange="this.form.submit()">
                        <label for="photo" class="inline-flex items-center px-6 py-3 bg-[#125047] text-white font-semibold rounded-lg hover:bg-[#0E453F] transition-colors duration-300 cursor-pointer">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                                Ganti Profil
                            </label>
                    </form>
                </div>
            </div>
                    </div>

        {{-- Detail Profile Card --}}
        <div class="bg-[#0E453F] p-10 rounded-2xl shadow-lg">
            {{-- Details Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div>
                    <p class="text-sm text-gray-400">Nama Lengkap</p>
                    <p class="font-bold text-white text-lg mb-4">{{ $teacher->full_name ?? '-' }}</p>

                    <p class="text-sm text-gray-400">NUPTK</p>
                    <p class="font-bold text-white text-lg mb-4">{{ $teacher->nuptk ?? '-' }}</p>

                    <p class="text-sm text-gray-400">NIP</p>
                    <p class="font-bold text-white text-lg mb-4">{{ $teacher->nip ?? '-' }}</p>

                    <p class="text-sm text-gray-400">Tempat, Tanggal Lahir</p>
                    <p class="font-bold text-white text-lg mb-4">{{ strtoupper($teacher->birth_place ?? '-') }}, {{ $teacher->birth_date ? $teacher->birth_date->translatedFormat('d - F - Y') : '-' }}</p>

                    <p class="text-sm text-gray-400">Jenis Kelamin</p>
                    <p class="font-bold text-white text-lg mb-4">{{ $teacher->gender ?? '-' }}</p>

                    <p class="text-sm text-gray-400">Agama</p>
                    <p class="font-bold text-white text-lg mb-4">{{ $teacher->religion ?? '-' }}</p>

                    <p class="text-sm text-gray-400">Alamat</p>
                    <p class="font-bold text-white text-lg mb-4">{{ $teacher->address ?? '-' }}</p>

                    <p class="text-sm text-gray-400">Tenaga Pendidikan Satuan Kerja</p>
                    <p class="font-bold text-white text-lg mb-4">{{ $teacher->school->name ?? '-' }}</p>
                </div>
                    <div>
                    <p class="text-sm text-gray-400">Status Kepegawaian</p>
                    <p class="font-bold text-green-300 text-lg mb-4">{{ $teacher->employment_status ?? '-' }}</p>

                    <p class="text-sm text-gray-400">Golongan</p>
                    <p class="font-bold text-white text-lg mb-4">{{ $teacher->rank ?? '-' }}</p>

                    <p class="text-sm text-gray-400">Jabatan</p>
                    <p class="font-bold text-white text-lg mb-4">{{ $teacher->position ?? '-' }}</p>

                    <p class="text-sm text-gray-400">TMT Mengajar</p>
                    <p class="font-bold text-white text-lg mb-4">{{ $teacher->tmt ? $teacher->tmt->translatedFormat('d - F - Y') : '-' }}</p>

                    <p class="text-sm text-gray-400">Mata Pelajaran Yang Di Ajar</p>
                    <p class="font-bold text-white text-lg mb-4">{{ $teacher->subjects ?? '-' }}</p>

                    <p class="text-sm text-gray-400">Pendidikan Terakhir</p>
                    <p class="font-bold text-white text-lg mb-4">{{ $teacher->education_level ?? '-' }}</p>

                    {{-- Action Buttons --}}
                    <div class="flex space-x-4 mt-2">
                        <a href="{{ route('guru.profile.edit') }}" class="text-green-300 hover:text-green-400 font-semibold text-lg">
                            Edit
                        </a>
                        <a href="{{ route('guru.profile.print') }}" target="_blank" class="text-green-300 hover:text-green-400 font-semibold text-lg">
                            Cetak Data
                        </a>
                        <button onclick="openDeleteModal()" class="text-red-400 hover:text-red-300 font-semibold text-lg">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Konfirmasi Hapus Profil</h2>
        <p class="text-gray-600 mb-6">
            Apakah Anda yakin ingin menghapus profil ini? Semua data terkait termasuk dokumen dan foto akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.
        </p>
        <div class="flex space-x-4">
            <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">
                Batal
            </button>
            <form action="{{ route('guru.profile.destroy') }}" method="POST" class="flex-1">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Hapus Profil
                </button>
            </form>
        </div>
    </div>
    </div>
</div>

<script>
function openDeleteModal() {
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Auto-hide messages after 3 seconds
setTimeout(function() {
    const successMessage = document.getElementById('successMessage');
    const errorMessage = document.getElementById('errorMessage');
    if (successMessage) successMessage.style.display = 'none';
    if (errorMessage) errorMessage.style.display = 'none';
}, 3000);
</script>
@endsection
