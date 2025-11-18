@extends('layouts.public')

@section('title', 'Galeri Foto - SIMPEDAS')

@section('content')
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    {{-- Breadcrumb --}}
    <nav class="mb-6 flex items-center text-white text-base" aria-label="Breadcrumb">
        <a href="/" class="font-semibold hover:underline">Dashboard</a>
        <span class="mx-2">&gt;</span>
        <span class="text-green-300 border-b-2 border-green-300 pb-1">Galeri Foto</span>
    </nav>

    {{-- Header --}}
    <div class="bg-[#136e67] rounded-2xl shadow-lg px-8 py-5 mb-8">
        <h2 class="text-3xl font-bold text-white mb-4">Galeri Foto</h2>
        <form id="galleryFilterForm" action="{{ route('public.galleries') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Cari foto..." 
                    class="w-full px-4 py-2 rounded-lg border-0 focus:ring-2 focus:ring-green-400">
            </div>
            <div>
                <select name="category" class="w-full px-4 py-2 rounded-lg border-0 focus:ring-2 focus:ring-green-400">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                            {{ $category }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <script>
        (function(){
            const form = document.getElementById('galleryFilterForm');
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

    {{-- Galleries Grid --}}
    @if($galleries->isEmpty())
        <div class="bg-[#09443c] rounded-2xl shadow-lg flex flex-col md:flex-row items-center px-10 py-10">
            <div class="flex-1 text-left">
                <p class="text-3xl font-bold text-green-300 mb-2">Tidak ada foto ditemukan</p>
                <p class="text-lg text-white mb-6">Silahkan cari kata kunci atau kategori lain</p>
                <a href="/" class="bg-[#136e67] hover:bg-green-700 text-white px-6 py-2 rounded-md font-semibold shadow">Kembali Ke Beranda</a>
            </div>
            <div class="flex-1 flex justify-center mt-8 md:mt-0">
                <img src="{{ asset('images/empty-search.svg') }}" alt="Empty State" class="h-56 w-auto">
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($galleries as $gallery)
                <div class="group relative overflow-hidden rounded-xl shadow-lg cursor-pointer bg-white">
                    <img src="{{ asset('storage/' . $gallery->image) }}" alt="{{ $gallery->title }}" 
                         class="w-full h-64 object-cover transition-transform duration-300 group-hover:scale-110">
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-opacity duration-300 flex items-center justify-center">
                        <div class="text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300 text-center px-4">
                            <p class="font-semibold text-lg mb-2">{{ $gallery->title }}</p>
                            @if($gallery->description)
                                <p class="text-sm">{{ Str::limit($gallery->description, 100) }}</p>
                            @endif
                            @if($gallery->category)
                                <span class="inline-block mt-2 px-3 py-1 bg-[#136e67] rounded-full text-xs font-semibold">
                                    {{ $gallery->category }}
                                </span>
                            @endif
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
</section>
@endsection

