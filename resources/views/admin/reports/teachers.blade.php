@extends('layouts.public')

@section('content')
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    {{-- Breadcrumb --}}
    <nav class="mb-6 flex items-center text-white text-base" aria-label="Breadcrumb">
        <a href="/" class="font-semibold hover:underline">Dashboard</a>
        <span class="mx-2">&gt;</span>
        <a href="{{ route('dinas.reports.index') }}" class="font-semibold hover:underline">Laporan</a>
        <span class="mx-2">&gt;</span>
        <span class="text-green-300 border-b-2 border-green-300 pb-1">Laporan Guru</span>
    </nav>

    {{-- Header Card --}}
    <div class="bg-[#136e67] rounded-2xl shadow-lg px-8 py-5 mb-8 border-b-4 border-white flex items-center justify-between">
        <h2 class="text-3xl font-bold text-white">Laporan Statistik Guru</h2>
        <button onclick="window.print()" class="bg-white text-[#136e67] px-4 py-2 rounded-lg font-semibold hover:bg-gray-100 transition">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Cetak
        </button>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        {{-- Status Employment --}}
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistik Status Kepegawaian</h3>
            <div class="space-y-3">
                @foreach($statusStats as $stat)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">{{ $stat->employment_status }}</span>
                    <span class="text-sm font-bold text-[#136e67]">{{ $stat->total }} orang</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Education Level --}}
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistik Tingkat Pendidikan</h3>
            <div class="space-y-3">
                @foreach($educationStats as $stat)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">{{ $stat->education_level }}</span>
                    <span class="text-sm font-bold text-[#136e67]">{{ $stat->total }} orang</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Detail Guru per Sekolah</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sekolah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Kepegawaian</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tingkat Pendidikan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($teachers->groupBy('school.school_name') as $schoolName => $schoolTeachers)
                        @foreach($schoolTeachers as $index => $teacher)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($index === 0)
                                    {{ $loop->parent->iteration }}
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                @if($index === 0)
                                    {{ $schoolName }}
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $teacher->employment_status }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $teacher->education_level }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $teacher->total }} orang</td>
                        </tr>
                        @endforeach
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada data guru
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
