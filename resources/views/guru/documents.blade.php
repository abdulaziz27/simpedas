@extends('layouts.app')

@section('content')
<div class="bg-[#125047] text-white min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold">Manajemen Dokumen</h1>
            <p class="text-green-200 text-lg">Kelola informasi dokumen Anda.</p>
        </div>

        <!-- Success/Error Messages -->
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

        @if ($errors->any())
            <div id="validationErrors" class="mb-6 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg text-center">
                <ul class="text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Document Card -->
        <div class="bg-white text-gray-800 rounded-xl shadow-lg p-6 md:p-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Dokumen Pribadi</h2>
            </div>

            <!-- Upload Form -->
            <form id="uploadForm" action="{{ route('guru.documents.store') }}" method="POST" enctype="multipart/form-data" class="mb-6">
                @csrf
                <div class="mb-4">
                    <input type="file" name="document" id="documentFile" accept=".pdf,.jpg,.jpeg,.png" class="hidden">
                    <button type="button" id="uploadButton" class="w-full bg-[#125047] text-white font-bold py-3 px-6 rounded-lg shadow-md hover:bg-[#0d524a] transition-colors duration-300 tracking-wider">
                        UNGGAH DOKUMEN BARU
                    </button>
                </div>
                <div id="uploadSection" class="hidden">
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Dokumen</label>
                        <input type="text" name="document_name" id="documentName" placeholder="Contoh: Ijazah S2 Pendidikan" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#125047] focus:border-[#125047]" required>
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-2">File yang dipilih</label>
                        <p id="fileName" class="text-gray-600 text-sm"></p>
                    </div>
                    <div class="flex space-x-3">
                        <button type="submit" class="bg-[#125047] text-white font-semibold py-2 px-4 rounded-lg hover:bg-[#0d524a] transition-colors duration-300">
                            Upload
                        </button>
                        <button type="button" id="cancelButton" class="bg-gray-500 text-white font-semibold py-2 px-4 rounded-lg hover:bg-gray-600 transition-colors duration-300">
                            Batal
                        </button>
                    </div>
                </div>
            </form>

            <!-- Document List -->
            <div class="space-y-3">
                @forelse ($documents as $document)
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex items-center">
                            <!-- Document Icon -->
                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $document->document_name }}</p>
                                <p class="text-sm text-gray-500">{{ $document->uploaded_at->format('d M Y') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <!-- Download Button -->
                            <a href="{{ route('guru.documents.download', $document) }}" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 transition-colors duration-200">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                            </a>
                            <!-- Delete Button -->
                            <button type="button" onclick="openDeleteModal('{{ $document->id }}', '{{ $document->document_name }}')" class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center hover:bg-red-200 transition-colors duration-200">
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 px-4 border-2 border-dashed border-gray-200 rounded-lg">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-gray-500 text-lg">Belum ada dokumen yang diunggah</p>
                        <p class="text-gray-400 text-sm mt-1">Klik tombol "UNGGAH DOKUMEN BARU" untuk menambahkan dokumen</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Konfirmasi Hapus Dokumen</h2>
        <p class="text-gray-600 mb-6">
            Apakah Anda yakin ingin menghapus dokumen "<span id="documentNameSpan"></span>"? Tindakan ini tidak dapat dibatalkan.
        </p>
        <div class="flex space-x-4">
            <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">
                Batal
            </button>
            <form id="deleteForm" action="" method="POST" class="flex-1">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Hapus Dokumen
                </button>
            </form>
        </div>
    </div>
    </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadButton = document.getElementById('uploadButton');
    const documentFile = document.getElementById('documentFile');
    const uploadSection = document.getElementById('uploadSection');
    const fileName = document.getElementById('fileName');
    const cancelButton = document.getElementById('cancelButton');
    const documentName = document.getElementById('documentName');

    // Handle upload button click
    uploadButton.addEventListener('click', function() {
        documentFile.click();
    });

    // Handle file selection
    documentFile.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            fileName.textContent = this.files[0].name;
        uploadSection.classList.remove('hidden');
    }
    });

    // Handle cancel button
    cancelButton.addEventListener('click', function() {
        uploadSection.classList.add('hidden');
        documentFile.value = '';
        documentName.value = '';
        fileName.textContent = '';
    });

    // Auto-hide messages after 3 seconds
    setTimeout(function() {
        const successMessage = document.getElementById('successMessage');
        const errorMessage = document.getElementById('errorMessage');
        const validationErrors = document.getElementById('validationErrors');

        if (successMessage) successMessage.style.display = 'none';
        if (errorMessage) errorMessage.style.display = 'none';
        if (validationErrors) validationErrors.style.display = 'none';
    }, 3000);
});

function openDeleteModal(documentId, documentName) {
    const modal = document.getElementById('deleteModal');
    const form = document.getElementById('deleteForm');
    const nameSpan = document.getElementById('documentNameSpan');

    nameSpan.textContent = documentName;
    form.action = `/guru/documents/${documentId}`;
    modal.classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}
</script>
@endsection
