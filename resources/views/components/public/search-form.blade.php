@props(['action' => '', 'placeholder' => 'Cari...', 'name' => 'q'])
<form method="GET" action="{{ $action }}" class="w-full flex">
    <input type="text" name="{{ $name }}" value="{{ request($name) }}" placeholder="{{ $placeholder }}"
        class="flex-1 rounded-l-xl px-6 py-3 text-gray-700 focus:outline-none border-2 border-[#136e67] bg-white shadow text-lg" />
    <button type="submit" class="bg-[#09443c] hover:bg-green-700 text-white px-6 rounded-r-xl shadow text-lg flex items-center justify-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-4.35-4.35m2.35-6.65a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
    </button>
</form>
