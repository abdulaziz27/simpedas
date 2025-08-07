@extends('layouts.public')

@section('content')
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    {{-- Breadcrumb --}}
    <nav class="mb-6 flex items-center text-white text-base" aria-label="Breadcrumb">
        <a href="/" class="font-semibold hover:underline">Dashboard</a>
        <span class="mx-2">&gt;</span>
        <a href="{{ route('dinas.reports.index') }}" class="font-semibold hover:underline">Laporan</a>
        <span class="mx-2">&gt;</span>
        <span class="text-green-300 border-b-2 border-green-300 pb-1">Laporan Siswa</span>
    </nav>

    {{-- Header Card --}}
    <div class="bg-[#136e67] rounded-2xl shadow-lg px-8 py-5 mb-8 border-b-4 border-white flex items-center justify-between">
        <h2 class="text-3xl font-bold text-white">Laporan Statistik Siswa</h2>
        <a href="{{ route('dinas.reports.students.export') }}" class="bg-white text-[#136e67] px-4 py-2 rounded-lg font-semibold hover:bg-gray-100 transition">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Cetak Excel
        </a>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-[#0E453F] rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-300">Total Siswa</p>
                    <p class="text-2xl font-bold">{{ number_format($totalStudents) }}</p>
                </div>
                <div class="bg-blue-500 p-3 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-[#0E453F] rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-300">Siswa Aktif</p>
                    <p class="text-2xl font-bold">{{ number_format($totalAktif) }}</p>
                </div>
                <div class="bg-green-500 p-3 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-[#0E453F] rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-300">Siswa Tamat</p>
                    <p class="text-2xl font-bold">{{ number_format($totalTamat) }}</p>
                </div>
                <div class="bg-purple-500 p-3 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Detail Siswa per Sekolah</h3>
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
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Siswa Aktif</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Siswa Tamat</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($schoolsWithStudents as $index => $school)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ ($schoolsWithStudents->currentPage() - 1) * $schoolsWithStudents->perPage() + $index + 1 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $school->name }}</div>
                            <div class="text-sm text-gray-500">{{ $school->address }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $school->npsn }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $school->education_level }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($school->status == 'Negeri') bg-green-100 text-green-800
                                @else bg-blue-100 text-blue-800 @endif">
                                {{ $school->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                            <span class="font-semibold text-green-600">{{ number_format($school->total_aktif) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                            <span class="font-semibold text-purple-600">{{ number_format($school->total_tamat) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                            <span class="font-bold text-blue-600">{{ number_format($school->total_aktif + $school->total_tamat) }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada data sekolah yang ditemukan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($schoolsWithStudents->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $schoolsWithStudents->links() }}
        </div>
        @endif
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
