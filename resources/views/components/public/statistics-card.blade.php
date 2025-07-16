@props(['title' => 'Statistik', 'chartId' => 'chart'])
<div class="bg-white rounded-xl shadow-lg p-6 text-[#0d524a]">
    <h3 class="text-lg font-semibold mb-4">{{ $title }}</h3>
    <canvas id="{{ $chartId }}" height="200"
        data-chart-data='@json($chartData ?? [])'
        data-chart-type='{{ $chartType ?? 'bar' }}'></canvas>
</div>
