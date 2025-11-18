@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-[#125047] py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <nav class="flex items-center space-x-2 text-white mb-6">
                <a href="{{ route('home') }}" class="hover:text-green-300">Dashboard</a>
                <span class="text-gray-300">&gt;</span>
                <a href="{{ route('dinas.galleries.index') }}" class="hover:text-green-300">Galeri Foto</a>
                <span class="text-gray-300">&gt;</span>
                <span class="border-b-2 border-white">Edit Foto</span>
            </nav>

            {{-- Form Card --}}
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h2 class="text-3xl font-bold text-[#125047] mb-6">Edit Foto Galeri</h2>

                <form action="{{ route('dinas.galleries.update', $gallery) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        {{-- Current Image Preview --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Foto Saat Ini</label>
                            <img src="{{ asset('storage/' . $gallery->image) }}" alt="{{ $gallery->title }}" class="w-full h-64 object-cover rounded-lg">
                        </div>

                        {{-- Title --}}
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Judul Foto *</label>
                            <input type="text" name="title" id="title" value="{{ old('title', $gallery->title) }}" required
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- Description --}}
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                            <textarea name="description" id="description" rows="3"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">{{ old('description', $gallery->description) }}</textarea>
                            @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- Image --}}
                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Ganti Foto (opsional)</label>
                            <input type="file" name="image" id="image" accept="image/*"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                            @error('image') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            <p class="mt-1 text-sm text-gray-500">Kosongkan jika tidak ingin mengganti foto</p>
                        </div>

                        {{-- Category --}}
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                            <input type="text" name="category" id="category" value="{{ old('category', $gallery->category) }}" placeholder="Contoh: Kegiatan, Acara, Prestasi"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            @error('category') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- Order --}}
                        <div>
                            <label for="order" class="block text-sm font-medium text-gray-700 mb-2">Urutan</label>
                            <input type="number" name="order" id="order" value="{{ old('order', $gallery->order) }}" min="0"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <p class="mt-1 text-sm text-gray-500">Angka lebih kecil akan ditampilkan lebih dulu</p>
                            @error('order') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- Is Active --}}
                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $gallery->is_active) ? 'checked' : '' }}
                                class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            <label for="is_active" class="ml-2 block text-sm text-gray-700">
                                Aktif (tampilkan di website)
                            </label>
                        </div>

                        {{-- Submit Buttons --}}
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('dinas.galleries.index') }}" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                Batal
                            </a>
                            <button type="submit" class="px-6 py-2 bg-[#125047] text-white rounded-md hover:bg-[#0E453F] transition">
                                Update Foto
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

