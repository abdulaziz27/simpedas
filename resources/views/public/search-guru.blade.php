@extends('layouts.public')

@section('content')
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    {{-- Breadcrumb --}}
    <nav class="mb-6 flex items-center text-white text-base" aria-label="Breadcrumb">
        <a href="/" class="font-semibold hover:underline">Dashboard</a>
        <span class="mx-2">&gt;</span>
        <span class="text-green-300 border-b-2 border-green-300 pb-1">Pencarian Guru</span>
    </nav>
    {{-- Judul dan Search Bar --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between bg-[#136e67] rounded-2xl shadow-lg px-8 py-5">
        <h2 class="text-2xl font-semibold text-white mb-4 md:mb-0">Hasil Pencarian Guru</h2>
        <div class="w-full md:w-1/2 lg:w-1/3">
            <x-public.search-form :action="route('public.search-guru')" placeholder="Cari nama / NUPTK / sekolah" />
        </div>
    </div>
    <div class="mt-10">
        @if($teachers->isEmpty())
            <div class="bg-[#09443c] rounded-2xl shadow-lg flex flex-col md:flex-row items-center px-10 py-10">
                <div class="flex-1 text-left">
                    <p class="text-3xl font-bold text-green-300 mb-2">Tidak ada hasil pencarian</p>
                    <p class="text-lg text-white mb-6">Silahkan cari kata kunci lain</p>
                    <a href="/" class="bg-[#136e67] hover:bg-green-700 text-white px-6 py-2 rounded-md font-semibold shadow">Kembali Ke Beranda</a>
                </div>
                <div class="flex-1 flex justify-center mt-8 md:mt-0">
                    <img src="{{ asset('images/empty-search.svg') }}" alt="Empty State" class="h-56 w-auto">
                </div>
            </div>
        @else
            @php
                $rows = $teachers->map(function($t){
                    return [
                        $t->full_name,
                        $t->nuptk ?? '-',
                        $t->school->name ?? '-',
                        $t->education_major ?? '-',
                        '<a href="'.route('public.detail-guru',$t->id).'" class="text-green-300 hover:underline">Lihat detail</a>'
                    ];
                });
            @endphp
            <div class="bg-[#09443c] rounded-2xl shadow-lg px-0 py-8">
                <x-public.data-table :headers="['Nama','NUPTK','Asal Sekolah','Mata Pelajaran','Aksi']" :rows="$rows" />
            </div>
        @endif
    </div>
</section>
@endsection
