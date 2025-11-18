@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-[#125047] py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <nav class="flex items-center space-x-2 text-white mb-6">
                <a href="{{ route('home') }}" class="hover:text-green-300">Dashboard</a>
                <span class="text-gray-300">&gt;</span>
                <span class="border-b-2 border-white">Pengaturan Website</span>
            </nav>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Form Card --}}
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h2 class="text-3xl font-bold text-[#125047] mb-8">Pengaturan Website</h2>

                <form action="{{ route('dinas.settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Tentang Kami Section --}}
                    <div class="mb-8">
                        <h3 class="text-2xl font-bold text-[#125047] mb-6 pb-2 border-b-2 border-gray-200">Tentang Kami</h3>
                        
                        <div class="space-y-6">
                            <div>
                                <label for="about_visi" class="block text-sm font-medium text-gray-700 mb-2">Visi</label>
                                <textarea name="about_visi" id="about_visi" rows="3"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">{{ isset($aboutSettings['about_visi']) ? $aboutSettings['about_visi']->value : old('about_visi', '') }}</textarea>
                            </div>

                            <div>
                                <label for="about_misi" class="block text-sm font-medium text-gray-700 mb-2">Misi</label>
                                <textarea name="about_misi" id="about_misi" rows="5"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">{{ isset($aboutSettings['about_misi']) ? $aboutSettings['about_misi']->value : old('about_misi', '') }}</textarea>
                                <p class="mt-1 text-sm text-gray-500">Gunakan baris baru untuk setiap poin misi</p>
                            </div>

                            <div>
                                <label for="about_tugas_pokok" class="block text-sm font-medium text-gray-700 mb-2">Tugas Pokok</label>
                                <textarea name="about_tugas_pokok" id="about_tugas_pokok" rows="5"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">{{ isset($aboutSettings['about_tugas_pokok']) ? $aboutSettings['about_tugas_pokok']->value : old('about_tugas_pokok', '') }}</textarea>
                            </div>

                            <div>
                                <label for="about_fungsi" class="block text-sm font-medium text-gray-700 mb-2">Fungsi</label>
                                <textarea name="about_fungsi" id="about_fungsi" rows="5"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">{{ isset($aboutSettings['about_fungsi']) ? $aboutSettings['about_fungsi']->value : old('about_fungsi', '') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Kontak & Lokasi Section --}}
                    <div class="mb-8">
                        <h3 class="text-2xl font-bold text-[#125047] mb-6 pb-2 border-b-2 border-gray-200">Kontak & Lokasi</h3>
                        
                        <div class="space-y-6">
                            <div>
                                <label for="contact_address" class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                                <textarea name="contact_address" id="contact_address" rows="3"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">{{ isset($contactSettings['contact_address']) ? $contactSettings['contact_address']->value : old('contact_address', '') }}</textarea>
                            </div>

                            <div>
                                <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">Telepon</label>
                                <input type="text" name="contact_phone" id="contact_phone" value="{{ isset($contactSettings['contact_phone']) ? $contactSettings['contact_phone']->value : old('contact_phone', '') }}"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            </div>

                            <div>
                                <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" name="contact_email" id="contact_email" value="{{ isset($contactSettings['contact_email']) ? $contactSettings['contact_email']->value : old('contact_email', '') }}"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            </div>

                            <div>
                                <label for="contact_hours" class="block text-sm font-medium text-gray-700 mb-2">Jam Operasional</label>
                                <textarea name="contact_hours" id="contact_hours" rows="3"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">{{ isset($contactSettings['contact_hours']) ? $contactSettings['contact_hours']->value : old('contact_hours', '') }}</textarea>
                                <p class="mt-1 text-sm text-gray-500">Contoh: Senin - Jumat: 08:00 - 16:00 WIB</p>
                            </div>

                            <div>
                                <label for="contact_map_url" class="block text-sm font-medium text-gray-700 mb-2">URL Peta Google Maps</label>
                                <input type="url" name="contact_map_url" id="contact_map_url" value="{{ isset($contactSettings['contact_map_url']) ? $contactSettings['contact_map_url']->value : old('contact_map_url', '') }}"
                                    placeholder="https://www.google.com/maps/embed?pb=..."
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                <p class="mt-1 text-sm text-gray-500">
                                    Cara mendapatkan embed URL: Buka Google Maps → Pilih lokasi → Klik "Bagikan" → Pilih "Sematkan peta" → Salin URL iframe
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-[#125047] text-white rounded-md hover:bg-[#0E453F] transition">
                            Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

