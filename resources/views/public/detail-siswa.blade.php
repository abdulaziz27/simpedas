@extends('layouts.public')

@section('content')
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" id="printable-content">
    {{-- Breadcrumb --}}
    <nav class="mb-6 flex items-center text-white text-base print:hidden" aria-label="Breadcrumb">
        <a href="/" class="font-semibold hover:underline">Dashboard</a>
        <span class="mx-2">&gt;</span>
        <a href="{{ route('public.search-siswa') }}" class="font-semibold hover:underline">Cari Data Siswa</a>
        <span class="mx-2">&gt;</span>
        <span class="text-green-300 border-b-2 border-green-300 pb-1">Detail Siswa / {{ strtoupper($student->full_name) }}</span>
    </nav>
    {{-- Header Card --}}
    <div class="bg-[#136e67] rounded-2xl shadow-lg px-8 py-5 mb-8 border-b-4 border-white flex items-center print:bg-white print:text-black">
        <h2 class="text-3xl font-bold text-white mx-auto print:text-black">Detail Biodata Siswa</h2>
    </div>

    <div class="bg-[#09443c] p-10 rounded-2xl shadow-lg print:bg-white print:text-black">
        {{-- Photo and Name Section --}}
        <div class="flex flex-col md:flex-row items-center md:items-start gap-8 mb-8 pb-8 border-b border-gray-700 print:border-gray-300">
            <div class="flex-shrink-0">
                <div class="h-40 w-40 rounded-full bg-gray-300 flex items-center justify-center border-4 border-gray-200">
                    <svg class="h-20 w-20 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path>
                    </svg>
                </div>
            </div>
            <div class="text-center md:text-left">
                <h3 class="text-2xl font-bold text-white print:text-black">{{ $student->full_name }}</h3>
                <p class="text-green-300 text-lg print:text-green-800">{{ $student->school->name ?? '-' }}</p>
                <p class="text-white print:text-gray-700">NISN: {{ $student->nisn }}</p>
            </div>
        </div>

        {{-- Details Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <div>
                <p class="text-sm text-gray-400 print:text-gray-600">Tempat, Tanggal Lahir</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ strtoupper($student->birth_place) }}, {{ $student->birth_date->translatedFormat('d - F - Y') }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">Jenis Kelamin</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $student->gender }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">NIS</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $student->nis ?? '-' }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">Agama</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $student->religion ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-400 print:text-gray-600">Kelas/Jurusan/Tingkat</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $student->grade_level }}{{ $student->major ? ', '.$student->major : '' }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">Status Kelulusan</p>
                <p class="font-bold text-green-300 text-lg mb-4 print:text-green-800">{{ $student->student_status }} - {{ $student->academic_year }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">Status Kelulusan Detil</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $student->graduation_status ?: '-' }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">Prestasi</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $student->achievements ?: '-' }}</p>

                {{-- Admin Action Buttons - Only visible for admin_dinas --}}
                @auth
                    @if(auth()->user()->hasRole('admin_dinas'))
                        <div class="flex space-x-4 mt-2 print:hidden">
                            <a href="{{ route('admin.students.edit', $student->id) }}" class="text-green-300 hover:text-green-400 font-semibold text-lg">
                                Edit
                            </a>
                            <button onclick="window.print()" class="text-green-300 hover:text-green-400 font-semibold text-lg">
                                Cetak Data
                            </button>
                            <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data siswa ini?');">
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
        @if(auth()->check() && auth()->user()->hasRole('admin_dinas') && isset($student->reports))
            <div class="mt-8 pt-8 border-t border-gray-700 print:border-gray-300">
                <h3 class="text-xl font-bold text-white mb-4 print:text-black">Daftar Raport</h3>

                @if($student->reports->isEmpty())
                    <p class="text-gray-300 print:text-gray-600">Belum ada raport yang diupload.</p>
                @else
                    <div class="overflow-x-auto print:text-black">
                        <table class="min-w-full divide-y divide-gray-600 print:divide-gray-300">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-300 uppercase print:text-gray-700">Kelas</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-300 uppercase print:text-gray-700">Semester</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-300 uppercase print:text-gray-700">Tahun Ajaran</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-300 uppercase print:text-gray-700">File</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-300 uppercase print:text-gray-700">Catatan</th>
                                    <th class="px-4 py-2 print:hidden"></th>
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
                                        <td class="px-4 py-2 text-right print:hidden">
                                            <form action="{{ route('admin.students.reports.destroy', [$student, $report]) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus raport ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-600 font-bold text-xs">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <div class="mt-4 print:hidden">
                    <a href="{{ route('admin.students.reports.create', $student) }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Raport
                    </a>
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
