@props(['title' => 'Selamat Datang', 'subtitle' => 'Di Sistem Pangkalan Data Dinas Pendidikan Kota Pematang Siantar'])
<section class="bg-[#125047] py-16 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col lg:flex-row items-start lg:items-center justify-between space-y-8 lg:space-y-0">
        <div class="max-w-lg text-left">
            <h1 class="text-4xl sm:text-5xl font-bold leading-tight text-white">{{ $title }}</h1>
            <p class="mt-4 text-2xl font-semibold text-green-300">{{ $subtitle }}</p>
            <p class="mt-4 text-sm text-gray-200">Jl. Merdeka No.228c, DwiKora, Kec. Siantar Bar., Kota Pematang Siantar, Sumatera Utara 21146</p>
        </div>
        @if(auth()->check() && auth()->user()->hasRole('guru'))
            <div class="flex-shrink-0">
                <!-- Placeholder statistics illustration -->
                <img src="{{ asset('images/pemko.png') }}" alt="Hero Stats" class="w-80 mx-auto lg:mx-0">
            </div>
        @else
            <div class="flex-shrink-0">
                <!-- Placeholder statistics illustration -->
                <img src="{{ asset('images/hero-stats.svg') }}" alt="Hero Stats" class="w-80 mx-auto lg:mx-0">
            </div>
        @endif
    </div>
</section>
