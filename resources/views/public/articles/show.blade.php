@extends('layouts.public')

@php
    use Illuminate\Support\Str;
@endphp

@section('title', $article->title . ' - SIMPEDAS')

@section('content')
<section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    {{-- Breadcrumb --}}
    <nav class="mb-6 flex items-center text-white text-base" aria-label="Breadcrumb">
        <a href="/" class="font-semibold hover:underline">Dashboard</a>
        <span class="mx-2">&gt;</span>
        <a href="{{ route('public.articles') }}" class="font-semibold hover:underline">Artikel</a>
        <span class="mx-2">&gt;</span>
        <span class="text-green-300 border-b-2 border-green-300 pb-1">{{ Str::limit($article->title, 30) }}</span>
    </nav>

    {{-- Article Content --}}
    <article class="bg-white rounded-xl shadow-lg overflow-hidden">
        @if($article->featured_image)
            <img src="{{ asset('storage/' . $article->featured_image) }}" alt="{{ $article->title }}" class="w-full h-96 object-cover animate-image-reveal">
        @endif

        <div class="p-8">
            {{-- Article Header --}}
            <header class="mb-6">
                <h1 class="text-4xl font-bold text-gray-800 mb-4">{{ $article->title }}</h1>
                <div class="flex items-center space-x-4 text-sm text-gray-500 mb-4">
                    <span>Oleh: {{ $article->author->name }}</span>
                    <span>•</span>
                    <span>{{ $article->published_at->format('d M Y, H:i') }}</span>
                </div>
                @if($article->excerpt)
                    <p class="text-lg text-gray-600 italic border-l-4 border-[#125047] pl-4">{{ $article->excerpt }}</p>
                @endif
            </header>

            {{-- Article Content --}}
            <div class="prose prose-lg max-w-none mb-8 article-content">
                {!! $article->content !!}
            </div>

            <style>
                /* Ensure Quill editor styles are preserved */
                .article-content {
                    /* Preserve all inline styles from Quill */
                    color: inherit;
                }
                /* Preserve inline styles from Quill - don't override them */
                .article-content [style] {
                    /* Inline styles from Quill will take precedence */
                }
                .article-content p {
                    margin-bottom: 1rem;
                    line-height: 1.75;
                }
                .article-content h1, .article-content h2, .article-content h3, .article-content h4, .article-content h5, .article-content h6 {
                    font-weight: bold;
                    margin-top: 1.5rem;
                    margin-bottom: 1rem;
                    line-height: 1.2;
                }
                .article-content h1 {
                    font-size: 2.25rem;
                }
                .article-content h2 {
                    font-size: 1.875rem;
                }
                .article-content h3 {
                    font-size: 1.5rem;
                }
                .article-content ul, .article-content ol {
                    margin-left: 1.5rem;
                    margin-bottom: 1rem;
                    padding-left: 1.5rem;
                }
                .article-content li {
                    margin-bottom: 0.5rem;
                    line-height: 1.75;
                }
                .article-content img {
                    max-width: 100%;
                    height: auto;
                    margin: 1rem 0;
                    border-radius: 0.5rem;
                }
                .article-content a {
                    color: #125047;
                    text-decoration: underline;
                }
                .article-content a:hover {
                    color: #0E453F;
                }
                .article-content strong {
                    font-weight: bold;
                }
                .article-content em {
                    font-style: italic;
                }
                .article-content u {
                    text-decoration: underline;
                }
                .article-content s {
                    text-decoration: line-through;
                }
                /* Preserve Quill's inline styles for colors and backgrounds */
                .article-content [style*="color"] {
                    /* Preserve inline color styles */
                }
                .article-content [style*="background"] {
                    /* Preserve inline background styles */
                }
            </style>
        </div>
    </article>

    {{-- Related Articles --}}
    @if($relatedArticles->isNotEmpty())
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-white mb-6">Artikel Terkait</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($relatedArticles as $related)
                    <a href="{{ route('public.article.detail', $related->slug) }}" class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition">
                        @if($related->featured_image)
                            <img src="{{ asset('storage/' . $related->featured_image) }}" alt="{{ $related->title }}" class="w-full h-32 object-cover">
                        @else
                            <div class="w-full h-32 bg-gradient-to-br from-[#125047] to-[#0E453F]"></div>
                        @endif
                        <div class="p-4">
                            <h3 class="text-lg font-bold text-gray-800 mb-2 line-clamp-2">{{ $related->title }}</h3>
                            <p class="text-sm text-gray-500">{{ $related->published_at->format('d M Y') }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Back to Articles --}}
    <div class="mt-8 text-center">
        <a href="{{ route('public.articles') }}" class="inline-block bg-[#125047] text-white px-6 py-3 rounded-lg font-semibold hover:bg-[#0E453F] transition ripple-button">
            ← Kembali ke Daftar Artikel
        </a>
    </div>
</section>
@endsection

