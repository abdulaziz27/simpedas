@extends('layouts.public')

@section('content')
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" id="printable-content">
    {{-- Breadcrumb --}}
    <nav class="mb-6 flex items-center text-white text-base print:hidden" aria-label="Breadcrumb">
        <a href="/" class="font-semibold hover:underline">Dashboard</a>
        <span class="mx-2">&gt;</span>
        <a href="{{ route('public.search-siswa') }}" class="font-semibold hover:underline">Cari Data Siswa</a>
        <span class="mx-2">&gt;</span>
        <span class="text-green-300 border-b-2 border-green-300 pb-1">Detail Siswa / {{ strtoupper($student->nama_lengkap) }}</span>
    </nav>
    {{-- Header Card --}}
    <div class="bg-[#136e67] rounded-2xl shadow-lg px-8 py-5 mb-8 border-b-4 border-white flex items-center print:bg-white print:text-black">
        <h2 class="text-3xl font-bold text-white mx-auto print:text-black">Detail Biodata Siswa</h2>
    </div>

    <div class="bg-[#09443c] p-10 rounded-2xl shadow-lg print:bg-white print:text-black">
        {{-- Photo and Name Section --}}
        <div class="flex flex-col md:flex-row items-center md:items-start gap-8 mb-8 pb-8 border-b border-gray-700 print:border-gray-300">
            <div class="flex-shrink-0">
                <div class="h-44 w-44 bg-gray-300 flex items-center justify-center border-4 border-gray-200 rounded-xl">
                    @if($student->foto)
                        <img src="{{ asset('storage/' . $student->foto) }}" alt="Foto {{ $student->nama_lengkap }}" class="h-full w-full object-cover rounded-xl">
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

                @if($student->nama_ayah)
                <p class="text-sm text-gray-400 print:text-gray-600">Nama Ayah</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $student->nama_ayah }}</p>
                @endif

                @if($student->nama_ibu)
                <p class="text-sm text-gray-400 print:text-gray-600">Nama Ibu</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $student->nama_ibu }}</p>
                @endif
            </div>
            <div>
                <p class="text-sm text-gray-400 print:text-gray-600">Status Siswa</p>
                <p class="font-bold text-green-300 text-lg mb-4 print:text-green-800">{{ $student->status_siswa_label }}</p>

                @if($student->alamat)
                <p class="text-sm text-gray-400 print:text-gray-600">Alamat</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $student->alamat }}</p>
                @endif

                @if($student->kelurahan || $student->kecamatan)
                <p class="text-sm text-gray-400 print:text-gray-600">Kelurahan/Kecamatan</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $student->kelurahan }}, {{ $student->kecamatan }}</p>
                @endif

                @if($student->no_hp)
                <p class="text-sm text-gray-400 print:text-gray-600">No. HP</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $student->no_hp }}</p>
                @endif

                {{-- Admin Action Buttons - Only visible for admin_dinas --}}
                @auth
                    @if(auth()->user()->hasRole('admin_dinas'))
                        <div class="flex space-x-4 mt-2 print:hidden">
                            <a href="{{ route('dinas.students.edit', $student->id) }}" class="text-green-300 hover:text-green-400 font-semibold text-lg">
                                Edit
                            </a>
                            <button onclick="window.print()" class="text-green-300 hover:text-green-400 font-semibold text-lg">
                                Cetak Data
                            </button>
                            <form action="{{ route('dinas.students.destroy', $student->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data siswa ini?');">
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

        {{-- Reports Section --}}
        @if($student->reports && $student->reports->count() > 0)
            <div class="mt-8 pt-8 border-t border-gray-700 print:border-gray-300">
                <h3 class="text-xl font-bold text-white mb-4 print:text-black">Daftar Raport</h3>

                <div class="overflow-x-auto print:text-black">
                    <table class="min-w-full divide-y divide-gray-600 print:divide-gray-300">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-300 uppercase print:text-gray-700">Kelas</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-300 uppercase print:text-gray-700">Semester</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-300 uppercase print:text-gray-700">Tahun Ajaran</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-300 uppercase print:text-gray-700">File</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-300 uppercase print:text-gray-700">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-600 print:divide-gray-300">
                            @foreach($student->reports as $report)
                                <tr>
                                    <td class="px-4 py-2 text-white print:text-black">{{ $report->grade_class }}</td>
                                    <td class="px-4 py-2 text-white print:text-black">{{ $report->semester }}</td>
                                    <td class="px-4 py-2 text-white print:text-black">{{ $report->academic_year }}</td>
                                    <td class="px-4 py-2">
                                        <a href="{{ asset('storage/' . $report->file_path) }}" target="_blank" class="text-green-300 underline print:text-green-800">Download</a>
                                    </td>
                                    <td class="px-4 py-2 text-white print:text-black">{{ $report->additional_notes ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Ijazah Section --}}
        @if($student->certificates && $student->certificates->count() > 0)
            <div class="mt-8 pt-8 border-t border-gray-700 print:border-gray-300">
                <h3 class="text-xl font-bold text-white mb-4 print:text-black">Ijazah</h3>

                <div class="bg-[#0a403a] rounded-lg p-4 print:bg-gray-100 print:text-black">
                    @php
                        $certificate = $student->certificates->first();
                    @endphp
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h4 class="font-semibold text-white print:text-black mb-1">Ijazah Kelulusan</h4>
                            <p class="text-sm text-gray-300 print:text-gray-600">
                                Status: <span class="font-semibold text-green-300 print:text-green-800">{{ $certificate->graduation_status }}</span>
                                <span class="mx-2">•</span>
                                Tanggal: {{ $certificate->graduation_date->translatedFormat('d M Y') }}
                            </p>
                        </div>
                        <div class="ml-4">
                            <a href="{{ asset('storage/' . $certificate->certificate_file) }}"
                               target="_blank"
                               class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Lihat Ijazah
                            </a>
                        </div>
                    </div>
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
        .print\:divide-gray-300 > * + * {
            border-color: #d1d5db !important;
        }
    }
</style>
@endpush
@endsection
