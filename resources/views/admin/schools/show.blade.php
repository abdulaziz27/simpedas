@extends('layouts.public')

@section('content')
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" id="printable-content">
    {{-- Breadcrumb --}}
    <nav class="mb-6 flex items-center text-white text-base print:hidden" aria-label="Breadcrumb">
        <a href="/" class="font-semibold hover:underline">Dashboard</a>
        <span class="mx-2">&gt;</span>
        <a href="{{ route('dinas.schools.index') }}" class="font-semibold hover:underline">Data Sekolah</a>
        <span class="mx-2">&gt;</span>
        <span class="text-green-300 border-b-2 border-green-300 pb-1">Detail Sekolah / {{ strtoupper($school->name) }}</span>
    </nav>
    {{-- Header Card --}}
    <div class="bg-[#136e67] rounded-2xl shadow-lg px-8 py-5 mb-8 border-b-4 border-white flex items-center print:bg-white print:text-black">
        <h2 class="text-3xl font-bold text-white mx-auto print:text-black">Detail Biodata Sekolah</h2>
    </div>

    <div class="bg-[#09443c] p-10 rounded-2xl shadow-lg print:bg-white print:text-black">
        {{-- Photo and Name Section --}}
        <div class="flex flex-col md:flex-row items-center md:items-start gap-8 mb-8 pb-8 border-b border-gray-700 print:border-gray-300">
            <div class="flex-shrink-0">
                @if($school->logo)
                    <div class="h-40 w-40 rounded-lg border-4 border-gray-200 overflow-hidden">
                        <img src="{{ asset('storage/' . $school->logo) }}" alt="Logo {{ $school->name }}" class="h-40 w-40 object-cover">
                    </div>
                @else
                    <div class="h-40 w-40 rounded-lg bg-gray-300 flex items-center justify-center border-4 border-gray-200">
                        <svg class="h-20 w-20 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path>
                        </svg>
                    </div>
                @endif
            </div>
            <div class="text-center md:text-left">
                <h3 class="text-2xl font-bold text-white print:text-black">{{ $school->name }}</h3>
                <p class="text-green-300 text-lg print:text-green-800">{{ $school->education_level }} - {{ $school->status }}</p>
                <p class="text-white print:text-gray-700">NPSN: {{ $school->npsn }}</p>
            </div>
        </div>

        {{-- Details Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <div>
                <p class="text-sm text-gray-400 print:text-gray-600">Nama Sekolah</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $school->name }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">NPSN</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $school->npsn }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">Jenjang Pendidikan</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $school->education_level }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">Status Sekolah</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $school->status }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">Alamat Lengkap</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $school->address }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-400 print:text-gray-600">Kepala Sekolah</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $school->headmaster ?: '-' }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">Nomor HP</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $school->phone ?: '-' }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">Email</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $school->email ?: '-' }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">Website</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">
                    @if($school->website)
                        <a href="{{ $school->website }}" target="_blank" class="text-green-300 hover:text-green-400 underline print:text-green-800">{{ $school->website }}</a>
                    @else
                        -
                    @endif
                </p>

                <p class="text-sm text-gray-400 print:text-gray-600">Wilayah</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $school->region }}</p>

            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="mt-8 pt-8 border-t border-gray-700 print:border-gray-300 print:hidden">
        <div class="flex flex-wrap gap-4 justify-center">
            <a href="{{ route('dinas.schools.edit', $school->id) }}"
               class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Data
            </a>

            <a href="{{ route('dinas.schools.print', $school) }}" target="_blank"
               class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak Data
            </a>

            <form action="{{ route('dinas.schools.destroy', $school->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data sekolah ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Hapus Data
                </button>
            </form>
        </div>
    </div>

    @role('admin_dinas')
    <!-- Quick Actions Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 print:hidden">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-white mb-6">Aksi Cepat</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
                <a href="{{ route('dinas.non-teaching-staff.index') }}" class="bg-[#0E453F] hover:bg-[#0a403a] text-white rounded-xl shadow-xl p-6 flex items-center justify-between transition group border-2 border-transparent hover:border-blue-400 hover:shadow-blue-400/50">
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-white">Manajemen</h3>
                        <h3 class="text-xl font-bold text-white">Tenaga Pendidik</h3>
                    </div>
                    <div class="flex-shrink-0 ml-4">
                        <img src="{{ asset('images/icon-siswa.png') }}" alt="Icon Tenaga Pendidik" class="h-20 w-20 group-hover:scale-110 transition-transform duration-300">
                    </div>
                </a>
            </div>
        </div>
    </div>
    @endrole
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
        .print\:text-gray-700 {
            color: #374151 !important;
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
