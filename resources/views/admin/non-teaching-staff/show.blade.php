@extends('layouts.public')

@section('content')
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" id="printable-content">
        {{-- Breadcrumb --}}
    <nav class="mb-6 flex items-center text-white text-base print:hidden" aria-label="Breadcrumb">
        <a href="/" class="font-semibold hover:underline">Dashboard</a>
        <span class="mx-2">&gt;</span>
        <a href="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.non-teaching-staff.index') : route('admin.non-teaching-staff.index') }}" class="font-semibold hover:underline">Manajemen Tenaga Pendidik Non Guru</a>
        <span class="mx-2">&gt;</span>
        <span class="text-green-300 border-b-2 border-green-300 pb-1">Detail Tenaga Pendidik Non Guru / {{ strtoupper($nonTeachingStaff->full_name) }}</span>
        </nav>
    {{-- Header Card --}}
    <div class="bg-[#136e67] rounded-2xl shadow-lg px-8 py-5 mb-8 border-b-4 border-white flex items-center print:bg-white print:text-black">
        <h2 class="text-3xl font-bold text-white mx-auto print:text-black">Detail Biodata Tenaga Pendidik Non Guru</h2>
                </div>

    <div class="bg-[#09443c] p-10 rounded-2xl shadow-lg print:bg-white print:text-black">
        {{-- Photo and Name Section --}}
        <div class="flex flex-col md:flex-row items-center md:items-start gap-8 mb-8 pb-8 border-b border-gray-700 print:border-gray-300">
            <div class="flex-shrink-0">
                @if($nonTeachingStaff->photo)
                    <img src="{{ asset('storage/' . $nonTeachingStaff->photo) }}"
                        alt="Foto {{ $nonTeachingStaff->full_name }}"
                        class="h-40 w-40 rounded-full object-cover border-4 border-gray-200">
                @else
                    <div class="h-40 w-40 rounded-full bg-gray-300 flex items-center justify-center border-4 border-gray-200">
                        <svg class="h-20 w-20 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path>
                        </svg>
                    </div>
                                @endif
                        </div>
            <div class="text-center md:text-left">
                <h3 class="text-2xl font-bold text-white print:text-black">{{ $nonTeachingStaff->full_name }}</h3>
                <p class="text-green-300 text-lg print:text-green-800">{{ $nonTeachingStaff->school->name ?? '-' }}</p>
                    </div>
                </div>

        {{-- Details Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <div>
                <p class="text-sm text-gray-400 print:text-gray-600">NUPTK</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $nonTeachingStaff->nuptk ?? '-' }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">NIP/NIK</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $nonTeachingStaff->nip_nik ?? '-' }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">Tempat, Tanggal Lahir</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ strtoupper($nonTeachingStaff->birth_place ?? '-') }}, {{ $nonTeachingStaff->birth_date ? $nonTeachingStaff->birth_date->translatedFormat('d - F - Y') : '-' }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">Jenis Kelamin</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $nonTeachingStaff->gender ?? '-' }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">Agama</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $nonTeachingStaff->religion ?? '-' }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">Alamat</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $nonTeachingStaff->address ?? '-' }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">Tenaga Pendidikan Satuan Kerja</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $nonTeachingStaff->school->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-400 print:text-gray-600">Status Kepegawaian</p>
                <p class="font-bold text-green-300 text-lg mb-4 print:text-green-800">{{ $nonTeachingStaff->employment_status ?? '-' }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">Golongan</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $nonTeachingStaff->rank ?? '-' }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">Jabatan</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $nonTeachingStaff->position ?? '-' }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">TMT Mengajar</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $nonTeachingStaff->tmt ? $nonTeachingStaff->tmt->translatedFormat('d - F - Y') : '-' }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">Pendidikan Terakhir</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $nonTeachingStaff->education_level ?? '-' }}</p>

                {{-- Admin Action Buttons --}}
                <div class="flex space-x-4 mt-2 print:hidden">
                    <a href="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.non-teaching-staff.edit', $nonTeachingStaff->id) : route('admin.non-teaching-staff.edit', $nonTeachingStaff->id) }}" class="text-green-300 hover:text-green-400 font-semibold text-lg">
                        Edit
                    </a>
                    <a href="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.non-teaching-staff.print', $nonTeachingStaff) : route('admin.non-teaching-staff.print', $nonTeachingStaff) }}" target="_blank" class="text-green-300 hover:text-green-400 font-semibold text-lg">
                        Cetak Data
                    </a>
                    <form action="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.non-teaching-staff.destroy', $nonTeachingStaff->id) : route('admin.non-teaching-staff.destroy', $nonTeachingStaff->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data tenaga pendidik non guru ini?');">
                    @csrf
                    @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-600 font-semibold text-lg">
                            Delete
                    </button>
                </form>
                </div>
            </div>
        </div>
    </div>
</section>

@push('styles')
<style>
    @media print {
        body {
            background-color: white;
            color: black;
        }
        .print\:hidden {
            display: none !important;
        }
        .print\:text-black {
            color: black !important;
        }
        .print\:text-gray-600 {
            color: #4b5563 !important;
        }
        .print\:text-green-800 {
            color: #065f46 !important;
        }
        .print\:bg-white {
            background-color: white !important;
        }
        .print\:border-gray-300 {
            border-color: #d1d5db !important;
        }
    }
</style>
@endpush
@endsection
