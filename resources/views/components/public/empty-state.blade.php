@props(['message' => 'Tidak ada data', 'buttonRoute' => '/', 'buttonText' => 'Kembali'])
<div class="bg-[#09443c] rounded-xl p-10 flex flex-col items-center justify-center space-y-4 text-center">
    <img src="{{ asset('images/empty-search.svg') }}" alt="Empty State" class="h-40 w-auto">
    <p class="text-2xl font-semibold text-green-300">{{ $message }}</p>
    <a href="{{ $buttonRoute }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md">{{ $buttonText }}</a>
</div>
