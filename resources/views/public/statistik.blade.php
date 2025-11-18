@extends('layouts.public')

@section('title', 'Statistik - SIMPEDAS')

@section('content')
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    {{-- Header Card --}}
    <div class="bg-[#136e67] rounded-2xl shadow-lg px-4 py-3 md:px-8 md:py-5 mb-8 border-b-4 border-white flex items-center">
        <h2 class="text-xl md:text-3xl font-bold text-white mx-auto text-center leading-tight">
            Jumlah Statistik {{ ucfirst($type ?? 'Sekolah') }}
        </h2>
    </div>
    {{-- Tabs --}}
    <div class="flex justify-center mb-10">
        <div class="bg-white rounded-full flex p-1 shadow-md">
            @foreach(['sekolah' => 'Sekolah', 'siswa' => 'Siswa', 'guru' => 'Guru', 'non-guru' => 'Tenaga P Non Guru'] as $key => $label)
                <a href="{{ route('statistik.detail', $key) }}"
                   class="px-8 py-3 rounded-full font-bold text-lg transition-all duration-200
                   {{ ($type ?? 'sekolah') === $key
                        ? 'bg-[#136e67] text-white shadow'
                        : 'text-[#136e67] bg-white hover:bg-[#e5e7eb]' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>
    {{-- Card Statistik Bar (persentase) --}}
    <div class="bg-white rounded-xl shadow-lg p-8 mb-10">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h4 class="text-sm text-gray-500 font-semibold mb-1">Statistics</h4>
                <h3 class="text-xl font-bold text-[#136e67]">Jumlah {{ ucfirst($type ?? 'Sekolah') }}</h3>
            </div>
            <div class="flex flex-col items-end">
                <span class="text-gray-500 font-semibold text-base">Total</span>
                <span class="text-[#22223b] font-extrabold text-2xl leading-tight">{{ $total ?? '-' }}</span>
            </div>
        </div>
        <canvas id="statistikBarChart" height="120"
            style="min-height:120px;"
            data-chart-data='@json($barData)'
            data-chart-options='@json($barOptions)'
            data-chart-type="bar"
            data-index-axis="y"></canvas>
    </div>
    {{-- Card Statistik Chart (jumlah) --}}
    <div class="bg-white rounded-xl shadow-lg p-8 mt-10">
        <h4 class="text-sm text-gray-500 font-semibold mb-1">Statistics</h4>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-[#136e67]">Jumlah {{ ucfirst($type ?? 'Sekolah') }}</h3>
            <form method="GET" action="{{ route('statistik.detail', $type) }}" class="flex items-center">
                <label for="statistikBarFilter" class="sr-only">Menampilkan:</label>
                <select
                    id="statistikBarFilter"
                    name="filter"
                    class="rounded-lg border-gray-300 focus:ring-2 focus:ring-[#136e67] min-w-[140px] text-[#14532d] text-base px-4 py-2"
                    onchange="this.form.submit()"
                >
                    <option value="all" {{ ($filter ?? 'all') === 'all' ? 'selected' : '' }}>Semua data</option>
                    @foreach($allLabels as $label)
                        <option value="{{ $label }}" {{ $filter === $label ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </form>
        </div>
        <canvas id="statistikChart" height="180"
            style="min-height:180px;"
            data-chart-data='@json($data)'
            data-chart-options='@json($options)'
            data-chart-type="{{ $chartType ?? 'bar' }}"></canvas>
    </div>
</section>

@push('scripts')
    {{-- Pastikan script public.js di-load langsung sebelum </body> --}}
    @vite(['resources/js/public.js'])
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Debugging chart
        console.log('Chart.js loaded:', typeof Chart !== 'undefined');
        console.log('Chart data:', @json($data));
        console.log('Bar data:', @json($barData));
    </script>
@endpush
@endsection
