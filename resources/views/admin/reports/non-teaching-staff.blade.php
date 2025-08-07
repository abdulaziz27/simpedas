@extends('layouts.public')

@section('content')
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    {{-- Breadcrumb --}}
    <nav class="mb-6 flex items-center text-white text-base" aria-label="Breadcrumb">
        <a href="/" class="font-semibold hover:underline">Dashboard</a>
        <span class="mx-2">&gt;</span>
        <a href="{{ route('dinas.reports.index') }}" class="font-semibold hover:underline">Laporan</a>
        <span class="mx-2">&gt;</span>
        <span class="text-green-300 border-b-2 border-green-300 pb-1">Laporan Tenaga Kerja Non Pendidik</span>
    </nav>

    {{-- Header Card --}}
    <div class="bg-[#136e67] rounded-2xl shadow-lg px-8 py-5 mb-8 border-b-4 border-white flex items-center justify-between">
        <h2 class="text-3xl font-bold text-white">Laporan Tenaga Kerja Non Pendidik</h2>
        <a href="{{ route('dinas.reports.non-teaching-staff.export') }}" class="bg-white text-[#136e67] px-4 py-2 rounded-lg font-semibold hover:bg-gray-100 transition">
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
                    <p class="text-sm text-gray-300">Total Tenaga Kerja</p>
                    <p class="text-2xl font-bold">{{ number_format($totalStaff) }}</p>
                </div>
                <div class="bg-indigo-500 p-3 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-[#0E453F] rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-300">PNS</p>
                    <p class="text-2xl font-bold">{{ number_format($totalPNS) }}</p>
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
                    <p class="text-sm text-gray-300">PPPK</p>
                    <p class="text-2xl font-bold">{{ number_format($totalPPPK) }}</p>
                </div>
                <div class="bg-yellow-500 p-3 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Detail Tenaga Kerja Non Pendidik per Sekolah</h3>
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
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">PNS</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">PPPK</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Honorer</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">PTY</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Kontrak</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($schoolsWithStaff as $index => $school)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ ($schoolsWithStaff->currentPage() - 1) * $schoolsWithStaff->perPage() + $index + 1 }}
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
                            <span class="font-semibold text-green-600">{{ number_format($school->total_pns) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                            <span class="font-semibold text-yellow-600">{{ number_format($school->total_pppk) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                            <span class="font-semibold text-orange-600">{{ number_format($school->total_honorer) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                            <span class="font-semibold text-purple-600">{{ number_format($school->total_pty) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                            <span class="font-semibold text-red-600">{{ number_format($school->total_kontrak) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                            <span class="font-bold text-blue-600">{{ number_format($school->total_pns + $school->total_pppk + $school->total_honorer + $school->total_pty + $school->total_kontrak) }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada data tenaga kerja non pendidik yang ditemukan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($schoolsWithStaff->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $schoolsWithStaff->links() }}
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
