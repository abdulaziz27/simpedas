@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#125047] py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="flex items-center space-x-2 text-white mb-6">
            <a href="{{ route('home') }}" class="hover:text-green-300">Dashboard</a>
            <span class="text-gray-300">&gt;</span>
            <a href="{{ route('dinas.articles.index') }}" class="hover:text-green-300">Manajemen Artikel</a>
            <span class="text-gray-300">&gt;</span>
            <span class="border-b-2 border-white">Detail Artikel</span>
        </nav>

        {{-- Article Card --}}
        <div class="bg-white rounded-xl p-8 shadow-lg">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $article->title }}</h1>
                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                        <span>Oleh: {{ $article->author->name }}</span>
                        <span>•</span>
                        <span>{{ $article->published_at ? $article->published_at->format('d M Y') : 'Belum dipublikasikan' }}</span>
                    </div>
                </div>
                <div>
                    @if($article->status === 'published')
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Published
                        </span>
                    @else
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                            Draft
                        </span>
                    @endif
                </div>
            </div>

            @if($article->featured_image)
                <div class="mb-6">
                    <img src="{{ asset('storage/' . $article->featured_image) }}" alt="{{ $article->title }}" class="w-full h-auto rounded-lg">
                </div>
            @endif

            @if($article->excerpt)
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <p class="text-gray-700 italic">{{ $article->excerpt }}</p>
                </div>
            @endif

            <div class="prose prose-lg max-w-none mb-6 article-content">
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

            @if($article->meta_title || $article->meta_description)
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">SEO Meta</h3>
                    @if($article->meta_title)
                        <p class="text-sm text-gray-600 mb-2"><strong>Meta Title:</strong> {{ $article->meta_title }}</p>
                    @endif
                    @if($article->meta_description)
                        <p class="text-sm text-gray-600"><strong>Meta Description:</strong> {{ $article->meta_description }}</p>
                    @endif
                </div>
            @endif

            <div class="mt-8 flex justify-end space-x-4">
                <a href="{{ route('dinas.articles.index') }}" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Kembali
                </a>
                <a href="{{ route('dinas.articles.edit', $article) }}" class="px-6 py-2 bg-[#125047] text-white rounded-md hover:bg-[#0E453F] transition">
                    Edit Artikel
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

