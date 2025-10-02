@extends('layouts.public')

@section('content')
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" id="printable-content">
    {{-- Breadcrumb --}}
    <nav class="mb-6 flex items-center text-white text-base print:hidden" aria-label="Breadcrumb">
        <a href="/" class="font-semibold hover:underline">Dashboard</a>
        <span class="mx-2">&gt;</span>
        <a href="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.students.index') : route('dinas.students.index') }}" class="font-semibold hover:underline">Data Siswa</a>
        <span class="mx-2">&gt;</span>
        <span class="text-green-300 border-b-2 border-green-300 pb-1">Detail Siswa / {{ strtoupper($student->nama_lengkap) }}</span>
    </nav>
    {{-- Header Card --}}
    <div class="bg-[#136e67] rounded-2xl shadow-lg px-8 py-5 mb-8 border-b-4 border-white flex items-center print:bg-white print:text-black">
        <h2 class="text-3xl font-bold text-white mx-auto print:text-black">Detail Biodata Siswa</h2>
    </div>

    <div class="bg-[#09443c] p-10 rounded-2xl shadow-lg print:bg-white print:text-black">
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

        {{-- Photo and Name Section --}}
        <div class="flex flex-col md:flex-row items-center md:items-start gap-8 mb-8 pb-8 border-b border-gray-700 print:border-gray-300">
            <div class="flex-shrink-0">
                <div class="h-44 w-44 bg-gray-300 flex items-center justify-center border-4 border-gray-200 rounded-xl overflow-hidden">
                    @if($student->foto)
                        <img src="{{ asset('storage/' . $student->foto) }}" alt="Foto {{ $student->nama_lengkap }}" class="h-44 w-44 object-cover rounded-xl">
                    @else
                        <svg class="h-20 w-20 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path>
                        </svg>
                    @endif
                </div>
            </div>
            <div class="text-center md:text-left flex-1">
                <h3 class="text-3xl font-bold text-white print:text-black mb-2">{{ $student->nama_lengkap }}</h3>
                <p class="text-green-300 text-xl print:text-green-800 mb-2">{{ $student->school->name ?? '-' }}</p>
                <p class="text-white text-lg print:text-gray-700">NISN: {{ $student->nisn }}</p>
            </div>
        </div>

        {{-- Details Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <div>
                <h3 class="text-xl font-bold text-white mb-4 print:text-black">Data Pribadi</h3>

                <p class="text-sm text-gray-400 print:text-gray-600">Tempat, Tanggal Lahir</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ strtoupper($student->tempat_lahir) }}, {{ $student->tanggal_lahir->translatedFormat('d - F - Y') }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">Umur</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $student->age ?? '-' }} tahun</p>

                <p class="text-sm text-gray-400 print:text-gray-600">Jenis Kelamin</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $student->jenis_kelamin_label }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">Agama</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $student->agama ?? '-' }}</p>

                @if($student->nipd)
                <p class="text-sm text-gray-400 print:text-gray-600">NIPD</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $student->nipd }}</p>
                @endif

                <p class="text-sm text-gray-400 print:text-gray-600">Rombel</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $student->rombel }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">Status Siswa</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $student->status_siswa_label }}</p>

                @if($student->alamat)
                <p class="text-sm text-gray-400 print:text-gray-600">Alamat</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $student->alamat }}</p>
                @endif

                @if($student->kelurahan || $student->kecamatan)
                <p class="text-sm text-gray-400 print:text-gray-600">Kelurahan/Kecamatan</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $student->kelurahan }}, {{ $student->kecamatan }}</p>
                @endif

                @if($student->kode_pos)
                <p class="text-sm text-gray-400 print:text-gray-600">Kode Pos</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $student->kode_pos }}</p>
                @endif

                @if($student->no_hp)
                <p class="text-sm text-gray-400 print:text-gray-600">No. HP</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $student->no_hp }}</p>
                @endif
            </div>

            <div>
                <h3 class="text-xl font-bold text-white mb-4 print:text-black">Data Keluarga</h3>

                @if($student->nama_ayah)
                <p class="text-sm text-gray-400 print:text-gray-600">Nama Ayah</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $student->nama_ayah }}</p>
                @endif

                @if($student->pekerjaan_ayah)
                <p class="text-sm text-gray-400 print:text-gray-600">Pekerjaan Ayah</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $student->pekerjaan_ayah }}</p>
                @endif

                @if($student->nama_ibu)
                <p class="text-sm text-gray-400 print:text-gray-600">Nama Ibu</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $student->nama_ibu }}</p>
                @endif

                @if($student->pekerjaan_ibu)
                <p class="text-sm text-gray-400 print:text-gray-600">Pekerjaan Ibu</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $student->pekerjaan_ibu }}</p>
                @endif

                @if($student->anak_ke)
                <p class="text-sm text-gray-400 print:text-gray-600">Anak Ke-</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $student->anak_ke }}</p>
                @endif

                @if($student->jumlah_saudara)
                <p class="text-sm text-gray-400 print:text-gray-600">Jumlah Saudara</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $student->jumlah_saudara }}</p>
                @endif

                <h3 class="text-xl font-bold text-white mb-4 mt-8 print:text-black">Data Sosial Ekonomi</h3>

                @if($student->kip !== null)
                <p class="text-sm text-gray-400 print:text-gray-600">KIP</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $student->kip_label }}</p>
                @endif

                @if($student->transportasi)
                <p class="text-sm text-gray-400 print:text-gray-600">Transportasi</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $student->transportasi }}</p>
                @endif

                @if($student->jarak_rumah_sekolah)
                <p class="text-sm text-gray-400 print:text-gray-600">Jarak ke Sekolah</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $student->jarak_rumah_sekolah }} km</p>
                @endif

                <h3 class="text-xl font-bold text-white mb-4 mt-8 print:text-black">Data Kesehatan</h3>

                @if($student->tinggi_badan)
                <p class="text-sm text-gray-400 print:text-gray-600">Tinggi Badan</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $student->tinggi_badan }} cm</p>
                @endif

                @if($student->berat_badan)
                <p class="text-sm text-gray-400 print:text-gray-600">Berat Badan</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $student->berat_badan }} kg</p>
                @endif
            </div>
        </div>

        {{-- Academic Records --}}
        <div class="mt-8 pt-8 border-t border-gray-700 print:border-gray-300">
            <h3 class="text-xl font-bold text-white mb-4 print:text-black">Dokumen Akademik</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <p class="text-sm text-gray-400 print:text-gray-600">Raport</p>
                    @php
                        $latestReport = $student->reports()->latest('academic_year')->latest('semester')->first();
                    @endphp
                    <div class="flex items-center gap-2">
                        @if($latestReport && $latestReport->file_path)
                            <a href="{{ asset('storage/' . $latestReport->file_path) }}" target="_blank" class="font-bold text-green-300 hover:underline">Klik Disini</a>
                            <button type="button" onclick="openDeleteModal('raport', {{ $latestReport->id }})" class="text-red-500 hover:text-red-600 font-bold text-xs ml-2">Hapus</button>
                        @else
                            <span class="font-bold text-white">-</span>
                        @endif
                    </div>
                </div>

                <div>
                    <p class="text-sm text-gray-400 print:text-gray-600">Ijazah</p>
                    @php
                        $certificate = $student->certificates()->latest('graduation_date')->first();
                    @endphp
                    <div class="flex items-center gap-2">
                        @if($certificate && $certificate->certificate_file)
                            <a href="{{ asset('storage/' . $certificate->certificate_file) }}" target="_blank" class="font-bold text-green-300 hover:underline">Klik Disini</a>
                            <button type="button" onclick="openDeleteModal('ijazah', {{ $certificate->id }})" class="text-red-500 hover:text-red-600 font-bold text-xs ml-2">Hapus</button>
                        @else
                            <span class="font-bold text-white">-</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="mt-8 pt-8 border-t border-gray-700 print:border-gray-300 print:hidden">
            <div class="flex flex-wrap gap-4 justify-center">
                <a href="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.students.edit', $student) : route('dinas.students.edit', $student) }}"
                   class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Data
                </a>

                <a href="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.students.print', $student) : route('dinas.students.print', $student) }}" target="_blank"
                        class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Data
                </a>

                <button onclick="openDeleteModal('student', {{ $student->id }})"
                        class="inline-flex items-center px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Hapus
                </button>
            </div>
        </div>
    </div>
</section>

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
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Konfirmasi Hapus</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500" id="modal-message">Apakah Anda yakin ingin menghapus data ini?</p>
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
    const title = document.getElementById('modal-title');
    const message = document.getElementById('modal-message');

    if (type === 'student') {
        title.textContent = 'Konfirmasi Hapus Siswa';
        message.textContent = 'Apakah Anda yakin ingin menghapus data siswa ini? Tindakan ini tidak dapat dibatalkan.';
    } else if (type === 'raport') {
        title.textContent = 'Konfirmasi Hapus Raport';
        message.textContent = 'Apakah Anda yakin ingin menghapus file raport ini?';
    } else if (type === 'ijazah') {
        title.textContent = 'Konfirmasi Hapus Ijazah';
        message.textContent = 'Apakah Anda yakin ingin menghapus file ijazah ini?';
    }

    modal.classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

document.getElementById('confirmDelete').addEventListener('click', function() {
    if (deleteType === 'student') {
        // Create delete form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ auth()->user()->hasRole("admin_sekolah") ? route("sekolah.students.destroy", ":id") : route("dinas.students.destroy", ":id") }}'.replace(':id', deleteId);

        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        // Add DELETE method
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);

        document.body.appendChild(form);
        form.submit();
    } else if (deleteType === 'raport') {
        // Delete raport logic
        fetch(`/admin/students/reports/${deleteId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Gagal menghapus raport');
            }
        });
    } else if (deleteType === 'ijazah') {
        // Delete certificate logic
        fetch(`/admin/students/certificates/${deleteId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Gagal menghapus ijazah');
            }
        });
    }

    closeDeleteModal();
});
</script>
@endsection
