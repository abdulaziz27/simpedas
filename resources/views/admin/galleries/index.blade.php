@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-[#125047] py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumb & Session Message --}}
            <nav class="flex items-center space-x-2 text-white mb-6">
                <a href="{{ route('home') }}" class="hover:text-green-300">Dashboard</a>
                <span class="text-gray-300">&gt;</span>
                <span class="border-b-2 border-white">Galeri Foto</span>
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
                <h2 class="text-3xl font-bold text-white">Galeri Foto</h2>
                <a href="{{ route('dinas.galleries.create') }}" class="bg-white text-[#125047] px-6 py-2 rounded-lg font-semibold hover:bg-green-50 transition">
                    + Tambah Foto
                </a>
            </div>

            {{-- Filter Section --}}
            <div class="bg-[#0d524a] rounded-xl p-6 mb-8">
                <h2 class="text-2xl font-bold text-white mb-6">Filter Galeri</h2>
                <form id="filtersForm" action="{{ route('dinas.galleries.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Kategori</label>
                        <select name="category" class="block w-full bg-white rounded-lg border-0 py-2.5 pl-4 pr-10 focus:ring-2 focus:ring-green-400">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>{{ ucfirst($category) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Cari Foto</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul atau deskripsi..."
                            class="block w-full bg-white rounded-lg border-0 py-2.5 pl-4 pr-10 focus:ring-2 focus:ring-green-400">
                    </div>
                </form>
            </div>

            {{-- Gallery Grid --}}
            @if($galleries->isEmpty())
                <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                    <p class="text-gray-500 text-lg">Tidak ada foto galeri ditemukan.</p>
                    <a href="{{ route('dinas.galleries.create') }}" class="mt-4 inline-block bg-[#125047] text-white px-6 py-2 rounded-lg font-semibold hover:bg-[#0E453F] transition">
                        + Tambah Foto Pertama
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($galleries as $gallery)
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition">
                            <div class="relative">
                                <img src="{{ asset('storage/' . $gallery->image) }}" alt="{{ $gallery->title }}" class="w-full h-48 object-cover">
                                @if(!$gallery->is_active)
                                    <div class="absolute top-2 right-2 bg-red-500 text-white px-2 py-1 rounded text-xs font-semibold">
                                        Nonaktif
                                    </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="font-bold text-gray-800 mb-2 line-clamp-2">{{ $gallery->title }}</h3>
                                @if($gallery->category)
                                    <span class="inline-block bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded mb-2">{{ $gallery->category }}</span>
                                @endif
                                @if($gallery->description)
                                    <p class="text-sm text-gray-600 line-clamp-2 mb-3">{{ $gallery->description }}</p>
                                @endif
                                <div class="flex space-x-2 mt-4">
                                    <a href="{{ route('dinas.galleries.edit', $gallery) }}" class="flex-1 text-center bg-green-600 text-white px-3 py-2 rounded text-sm font-semibold hover:bg-green-700 transition">
                                        Edit
                                    </a>
                                    <form action="{{ route('dinas.galleries.destroy', $gallery) }}" method="POST" class="flex-1" onsubmit="return confirm('Apakah Anda yakin ingin menghapus foto ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full bg-red-600 text-white px-3 py-2 rounded text-sm font-semibold hover:bg-red-700 transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($galleries->hasPages())
                    <div class="mt-8">
                        {{ $galleries->links() }}
                    </div>
                @endif
            @endif
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

