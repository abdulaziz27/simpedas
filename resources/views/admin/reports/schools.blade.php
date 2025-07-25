@extends('layouts.public')

@section('content')
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    {{-- Breadcrumb --}}
    <nav class="mb-6 flex items-center text-white text-base" aria-label="Breadcrumb">
        <a href="/" class="font-semibold hover:underline">Dashboard</a>
        <span class="mx-2">&gt;</span>
        <a href="{{ route('dinas.reports.index') }}" class="font-semibold hover:underline">Laporan</a>
        <span class="mx-2">&gt;</span>
        <span class="text-green-300 border-b-2 border-green-300 pb-1">Laporan Sekolah</span>
    </nav>

    {{-- Header Card --}}
    <div class="bg-[#136e67] rounded-2xl shadow-lg px-8 py-5 mb-8 border-b-4 border-white flex items-center justify-between">
        <h2 class="text-3xl font-bold text-white">Laporan Rekap Sekolah</h2>
        <button onclick="window.print()" class="bg-white text-[#136e67] px-4 py-2 rounded-lg font-semibold hover:bg-gray-100 transition">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Cetak
        </button>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-[#0E453F] rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-300">Total Sekolah</p>
                    <p class="text-2xl font-bold">{{ $schools->count() }}</p>
                </div>
                <div class="bg-green-500 p-3 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-[#0E453F] rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-300">Total Guru</p>
                    <p class="text-2xl font-bold">{{ $schools->sum('teachers_count') }}</p>
                </div>
                <div class="bg-blue-500 p-3 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-[#0E453F] rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-300">Total Siswa</p>
                    <p class="text-2xl font-bold">{{ $schools->sum('students_count') }}</p>
                </div>
                <div class="bg-yellow-500 p-3 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-[#0E453F] rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-300">Tenaga Pendidik</p>
                    <p class="text-2xl font-bold">{{ $schools->sum('non_teaching_staff_count') }}</p>
                </div>
                <div class="bg-purple-500 p-3 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Detail Sekolah</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Sekolah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NPSN</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenjang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guru</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Siswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenaga Pendidik</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($schools as $index => $school)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $school->school_name }}</div>
                            <div class="text-sm text-gray-500">{{ $school->address }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $school->npsn }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $school->education_level }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($school->school_status == 'Negeri') bg-green-100 text-green-800
                                @else bg-blue-100 text-blue-800 @endif">
                                {{ $school->school_status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $school->teachers_count }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $school->students_count }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $school->non_teaching_staff_count }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('dinas.schools.show', $school->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada data sekolah
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: white !important; }
    .bg-\[#136e67\] { background: #136e67 !important; -webkit-print-color-adjust: exact; }
    .bg-\[#0E453F\] { background: #0E453F !important; -webkit-print-color-adjust: exact; }
}
</style>
@endsection
