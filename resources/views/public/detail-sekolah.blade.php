@extends('layouts.public')

@section('content')
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" id="printable-content">
    {{-- Breadcrumb --}}
    <nav class="mb-6 flex items-center text-white text-base print:hidden" aria-label="Breadcrumb">
        <a href="/" class="font-semibold hover:underline">Dashboard</a>
        <span class="mx-2">&gt;</span>
        <a href="{{ route('public.search-sekolah') }}" class="font-semibold hover:underline">Cari Data Sekolah</a>
        <span class="mx-2">&gt;</span>
        <span class="text-green-300 border-b-2 border-green-300 pb-1">Detail Sekolah / {{ strtoupper($school->name) }}</span>
    </nav>
    {{-- Header Card --}}
    <div class="bg-[#136e67] rounded-2xl shadow-lg px-8 py-5 mb-8 border-b-4 border-white flex items-center print:bg-white print:text-black">
        <h2 class="text-3xl font-bold text-white mx-auto print:text-black">Detail Biodata Sekolah</h2>
    </div>

    <div class="bg-[#09443c] p-10 rounded-2xl shadow-lg print:bg-white print:text-black">
        <div class="flex flex-col md:flex-row items-center md:items-start gap-8 mb-8 pb-8 border-b border-gray-700 print:border-gray-300">
            <div class="flex-shrink-0">
                @if($school->logo)
                    <img src="{{ asset('storage/' . $school->logo) }}" alt="Logo {{ $school->name }}" class="h-44 w-44 object-cover border-4 border-gray-200 rounded-xl">
                @else
                    <div class="h-44 w-44 bg-gray-300 flex items-center justify-center border-4 border-gray-200 rounded-xl">
                        <svg class="h-20 w-20 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path>
                        </svg>
                    </div>
                @endif
            </div>
            <div class="text-center md:text-left">
                <h3 class="text-2xl font-bold text-white print:text-black">{{ $school->name }}</h3>
                <p class="text-green-300 text-lg print:text-green-800">NPSN: {{ $school->npsn }}</p>
                <p class="text-white print:text-gray-700">Jenjang: {{ $school->education_level }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <div>
                <p class="text-sm text-gray-400 print:text-gray-600">Status</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $school->status }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">Kepala Sekolah</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $school->headmaster }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">Wilayah</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $school->region }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">Alamat</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $school->address }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-400 print:text-gray-600">Telepon</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $school->phone ?? '-' }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">Email</p>
                <p class="font-bold text-white text-lg mb-4 print:text-black">{{ $school->email ?? '-' }}</p>

                <p class="text-sm text-gray-400 print:text-gray-600">Website</p>
                <p class="font-bold text-green-300 text-lg mb-4 print:text-green-800">
                    @if($school->website)
                        <a href="{{ $school->website }}" target="_blank" class="hover:underline">{{ $school->website }}</a>
                    @else
                        -
                    @endif
                </p>
            </div>
        </div>
    </div>
</section>
@endsection
