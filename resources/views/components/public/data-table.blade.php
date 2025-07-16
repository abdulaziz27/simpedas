@props(['headers' => [], 'rows' => []])
<div class="overflow-x-auto px-8">
    <table class="min-w-full text-left text-lg text-white rounded-2xl overflow-hidden">
        <thead class="bg-[#136e67] text-green-300">
            <tr>
                @foreach($headers as $header)
                    <th class="px-6 py-4 border-b-2 border-green-400 font-bold text-xl">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody class="bg-[#09443c]">
            @forelse($rows as $row)
                <tr class="hover:bg-[#0f6056]">
                    @foreach($row as $cell)
                        <td class="px-6 py-4 border-b border-green-700 align-middle">{!! $cell !!}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headers) }}" class="px-6 py-8 text-center text-green-300">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
