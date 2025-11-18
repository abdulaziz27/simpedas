@extends('layouts.app')

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
    <div class="min-h-screen bg-[#125047] py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumb & Session Message --}}
            <nav class="flex items-center space-x-2 text-white mb-6">
                <a href="{{ route('home') }}" class="hover:text-green-300">Dashboard</a>
                <span class="text-gray-300">&gt;</span>
                <span class="border-b-2 border-white">Manajemen Artikel</span>
            </nav>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Header Card --}}
            <div class="bg-[#136e67] rounded-2xl shadow-lg px-8 py-5 mb-8 border-b-4 border-white flex items-center justify-between">
                <h2 class="text-3xl font-bold text-white">Manajemen Artikel</h2>
                <a href="{{ route('dinas.articles.create') }}" class="bg-white text-[#125047] px-6 py-2 rounded-lg font-semibold hover:bg-green-50 transition">
                    + Buat Artikel Baru
                </a>
            </div>

            {{-- Filter Section --}}
            <div class="bg-[#0d524a] rounded-xl p-6 mb-8">
                <h2 class="text-2xl font-bold text-white mb-6">Filter Artikel</h2>
                <form id="filtersForm" action="{{ route('dinas.articles.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                        <select name="status" class="block w-full bg-white rounded-lg border-0 py-2.5 pl-4 pr-10 focus:ring-2 focus:ring-green-400">
                            <option value="">Semua Status</option>
                            <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Cari Artikel</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul, excerpt, atau konten..."
                            class="block w-full bg-white rounded-lg border-0 py-2.5 pl-4 pr-10 focus:ring-2 focus:ring-green-400">
                    </div>
                </form>
            </div>

            {{-- Articles Table --}}
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Published At</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($articles as $article)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ Str::limit($article->title, 50) }}</div>
                                        @if($article->excerpt)
                                            <div class="text-sm text-gray-500">{{ Str::limit($article->excerpt, 60) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $article->author->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($article->status === 'published')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Published
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                Draft
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $article->published_at ? $article->published_at->format('d M Y') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('dinas.articles.show', $article) }}" class="text-blue-600 hover:text-blue-900">Lihat</a>
                                            <a href="{{ route('dinas.articles.edit', $article) }}" class="text-green-600 hover:text-green-900">Edit</a>
                                            <form action="{{ route('dinas.articles.destroy', $article) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus artikel ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        Tidak ada artikel ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($articles->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $articles->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        (function(){
            const form = document.getElementById('filtersForm');
            if (!form) return;
            const selects = form.querySelectorAll('select');
            const inputs = form.querySelectorAll('input[type="text"]');
            let t;
            const debounce = (fn, delay) => {
                clearTimeout(t);
                t = setTimeout(fn, delay);
            };
            selects.forEach(el => el.addEventListener('change', () => form.submit()));
            inputs.forEach(el => el.addEventListener('input', () => debounce(() => form.submit(), 400)));
        })();
    </script>
@endsection

