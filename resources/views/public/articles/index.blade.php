@extends('layouts.public')

@section('title', 'Artikel & Berita - SIMPEDAS')

@section('content')
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    {{-- Breadcrumb --}}
    <nav class="mb-6 flex items-center text-white text-base" aria-label="Breadcrumb">
        <a href="/" class="font-semibold hover:underline">Dashboard</a>
        <span class="mx-2">&gt;</span>
        <span class="text-green-300 border-b-2 border-green-300 pb-1">Artikel</span>
    </nav>

    {{-- Header --}}
    <div class="bg-[#136e67] rounded-2xl shadow-lg px-8 py-5 mb-8 animate-fade-in-down">
        <h2 class="text-3xl font-bold text-white mb-4">Artikel & Berita</h2>
        <form action="{{ route('public.articles') }}" method="GET" class="w-full md:w-1/2 animate-scale-in" style="animation-delay: 0.2s;">
            <div class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Cari artikel..." 
                    class="flex-1 px-4 py-2 rounded-lg border-0 focus:ring-2 focus:ring-green-400">
                <button type="submit" class="bg-white text-[#125047] px-6 py-2 rounded-lg font-semibold hover:bg-green-50 transition ripple-button">
                    Cari
                </button>
            </div>
        </form>
    </div>

    {{-- Articles Grid --}}
    @if($articles->isEmpty())
        <div class="bg-[#09443c] rounded-2xl shadow-lg flex flex-col md:flex-row items-center px-10 py-10">
            <div class="flex-1 text-left">
                <p class="text-3xl font-bold text-green-300 mb-2">Tidak ada artikel ditemukan</p>
                <p class="text-lg text-white mb-6">Silahkan cari kata kunci lain</p>
                <a href="/" class="bg-[#136e67] hover:bg-green-700 text-white px-6 py-2 rounded-md font-semibold shadow">Kembali Ke Beranda</a>
            </div>
            <div class="flex-1 flex justify-center mt-8 md:mt-0">
                <img src="{{ asset('images/empty-search.svg') }}" alt="Empty State" class="h-56 w-auto">
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($articles as $index => $article)
                <a href="{{ route('public.article.detail', $article->slug) }}" 
                   class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1 hover:scale-[1.02] animate-fade-in-stagger {{ 'article-stagger-' . min($index + 1, 9) }}">
                    @if($article->featured_image)
                        <img src="{{ asset('storage/' . $article->featured_image) }}" alt="{{ $article->title }}" class="w-full h-48 object-cover transition-transform duration-300 hover:scale-105">
                    @else
                        <div class="w-full h-48 bg-gradient-to-br from-[#125047] to-[#0E453F] flex items-center justify-center">
                            <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                            </svg>
                        </div>
                    @endif
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-2 line-clamp-2">{{ $article->title }}</h3>
                        @if($article->excerpt)
                            <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ $article->excerpt }}</p>
                        @endif
                        <div class="flex items-center justify-between text-sm text-gray-500">
                            <span>{{ $article->published_at->format('d M Y') }}</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($articles->hasPages())
            <div class="mt-8 animate-fade-in-up" style="animation-delay: 0.5s;">
                {{ $articles->links() }}
            </div>
        @endif
    @endif
</section>
@endsection

