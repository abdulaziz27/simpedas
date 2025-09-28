@extends('layouts.public')

@section('content')
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" id="printable-content">
    {{-- Breadcrumb --}}
    <nav class="mb-6 flex items-center text-white text-base print:hidden" aria-label="Breadcrumb">
        <a href="/" class="font-semibold hover:underline">Dashboard</a>
        <span class="mx-2">&gt;</span>
        <a href="{{ route('public.search-guru') }}" class="font-semibold hover:underline">Cari Data Guru</a>
        <span class="mx-2">&gt;</span>
        <span class="text-green-300 border-b-2 border-green-300 pb-1">Detail Guru / {{ strtoupper($teacher->full_name) }}</span>
    </nav>
    {{-- Header Card --}}
    <div class="bg-[#136e67] rounded-2xl shadow-lg px-8 py-5 mb-8 border-b-4 border-white flex items-center print:bg-white print:text-black">
        <h2 class="text-3xl font-bold text-white mx-auto print:text-black">Detail Biodata Guru</h2>
    </div>

    <div class="bg-[#09443c] p-10 rounded-2xl shadow-lg print:bg-white print:text-black">
        {{-- Photo and Name Section --}}
        <div class="flex flex-col md:flex-row items-center md:items-start gap-8 mb-8 pb-8 border-b border-gray-700 print:border-gray-300">
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
            <div class="text-center md:text-left">
                <h3 class="text-2xl font-bold text-white print:text-black">{{ $teacher->full_name }}</h3>
                <p class="text-green-300 text-lg print:text-green-800">{{ $teacher->school->name ?? '-' }}</p>
            </div>
        </div>

        {{-- Details Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <div>
                <p class="text-sm text-gray-400 print:text-gray-600">NUPTK</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $teacher->nuptk ?? '-' }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">NIP</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $teacher->nip ?? '-' }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">Tempat, Tanggal Lahir</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ strtoupper($teacher->birth_place) }}, {{ $teacher->birth_date->translatedFormat('d - F - Y') }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">Jenis Kelamin</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $teacher->gender }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">Agama</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $teacher->religion ?? '-' }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">Alamat</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $teacher->address }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">No. Telepon</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $teacher->phone ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-400 print:text-gray-600">Pendidikan</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $teacher->education_level ?? '-' }} {{ $teacher->education_major ? '- '.$teacher->education_major : '' }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">Golongan</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $teacher->rank ?? '-' }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">Jabatan</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $teacher->position ?? '-' }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">TMT Mengajar</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $teacher->tmt ? $teacher->tmt->translatedFormat('d - F - Y') : '-' }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">Status Kepegawaian</p>
                <p class="font-bold text-green-300 text-lg mb-4 print:text-green-800">{{ $teacher->employment_status }} {{ $teacher->academic_year ? '- '.$teacher->academic_year : '' }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">Mata Pelajaran Yang Di Ajarkan</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $teacher->subjects }}</p>

                {{-- Admin Action Buttons - Only visible for admin_dinas --}}
                @auth
                    @if(auth()->user()->hasRole('admin_dinas'))
                        <div class="flex space-x-4 mt-2 print:hidden">
                            <a href="{{ route('dinas.teachers.edit', $teacher->id) }}" class="text-green-300 hover:text-green-400 font-semibold text-lg">
                                Edit
                            </a>
                            <button onclick="window.print()" class="text-green-300 hover:text-green-400 font-semibold text-lg">
                                Cetak Data
                            </button>
                            <form action="{{ route('dinas.teachers.destroy', $teacher->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data guru ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-600 font-semibold text-lg">
                                    Delete
                                </button>
                            </form>
                        </div>
                    @else
                        <span class="block mt-2 text-xs italic text-gray-300 print:hidden">Note: Jika Ada Kesalahan Data Silahkan hubungi<br>Dinas Pendidikan Kota Pematang Siantar</span>
                    @endif
                @else
                    <span class="block mt-2 text-xs italic text-gray-300 print:hidden">Note: Jika Ada Kesalahan Data Silahkan hubungi<br>Dinas Pendidikan Kota Pematang Siantar</span>
                @endauth
            </div>
        </div>

        {{-- Documents Section --}}
        @if($teacher->documents && $teacher->documents->count() > 0)
            <div class="mt-8 pt-8 border-t border-gray-700 print:border-gray-300">
                <h3 class="text-xl font-bold text-white mb-4 print:text-black">Dokumen Guru</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($teacher->documents as $document)
                        <div class="bg-[#0a403a] rounded-lg p-4 print:bg-gray-100 print:text-black">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-semibold text-white print:text-black">{{ $document->document_type }}</h4>
                                <span class="text-xs text-gray-300 print:text-gray-600">
                                    {{ $document->created_at->translatedFormat('d M Y') }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-300 print:text-gray-600 mb-3">{{ $document->description ?: 'Tidak ada deskripsi' }}</p>
                            <div class="flex space-x-2">
                                <a href="{{ asset('storage/' . $document->file_path) }}"
                                   target="_blank"
                                   class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Lihat
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
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
