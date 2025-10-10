@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#125047] py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="flex items-center space-x-2 text-white mb-6">
            <a href="{{ route('home') }}" class="hover:text-green-300">Dashboard</a>
            <span class="text-gray-300">&gt;</span>
            <a href="{{ route('dinas.schools.index') }}" class="hover:text-green-300">Data Sekolah</a>
            <span class="text-gray-300">&gt;</span>
            <span class="border-b-2 border-white">Edit Data Sekolah</span>
        </nav>

        {{-- Form Card --}}
        <div class="bg-white rounded-xl p-8 shadow-lg">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Data Sekolah</h1>

            <form action="{{ route('dinas.schools.update', $school) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

                    {{-- Logo Sekolah --}}
                    <div class="md:col-span-2">
                        <label for="logo" class="block text-sm font-medium text-gray-700">Logo Sekolah</label>
                        @if($school->logo)
                            <div class="mt-2 mb-4">
                                <div class="h-32 w-32 rounded-lg border-2 border-gray-200 overflow-hidden">
                                    <img src="{{ asset('storage/' . $school->logo) }}" alt="Logo {{ $school->name }}" class="h-32 w-32 object-cover">
                                </div>
                            </div>
                        @endif
                        <input type="file" name="logo" id="logo" accept="image/jpeg,image/png,image/jpg"
                            class="mt-1 block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-md file:border-0
                                file:text-sm file:font-semibold
                                file:bg-green-50 file:text-green-700
                                hover:file:bg-green-100">
                        <p class="mt-1 text-sm text-gray-500">Format: JPG, JPEG, PNG (Max. 2MB)</p>
                        @error('logo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Nama Sekolah --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nama Sekolah</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $school->name) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                            required>
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- NPSN --}}
                    <div>
                        <label for="npsn" class="block text-sm font-medium text-gray-700">NPSN</label>
                        <input type="text" name="npsn" id="npsn" value="{{ old('npsn', $school->npsn) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                             required>
                        @error('npsn') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Jenjang Pendidikan --}}
                    <div>
                        <label for="education_level" class="block text-sm font-medium text-gray-700">Jenjang Pendidikan</label>
                        <select name="education_level" id="education_level" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="">Pilih Jenjang</option>
                            @foreach($education_levels as $value => $label)
                                <option value="{{ $value }}" {{ old('education_level', $school->education_level) == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('education_level') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Status Sekolah --}}
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status Sekolah</label>
                        <select name="status" id="status" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="">Pilih Status</option>
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" {{ old('status', $school->status) == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Alamat Lengkap --}}
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                        <textarea name="address" id="address" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">{{ old('address', $school->address) }}</textarea>
                        @error('address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Desa --}}
                    <div>
                        <label for="desa" class="block text-sm font-medium text-gray-700">Desa</label>
                        <input type="text" name="desa" id="desa" value="{{ old('desa', $school->desa) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                            placeholder="Nama Desa">
                        @error('desa') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Kecamatan --}}
                    <div>
                        <label for="kecamatan" class="block text-sm font-medium text-gray-700">Kecamatan</label>
                        <input type="text" name="kecamatan" id="kecamatan" value="{{ old('kecamatan', $school->kecamatan) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                            placeholder="Nama Kecamatan">
                        @error('kecamatan') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Kabupaten/Kota --}}
                    <div>
                        <label for="kabupaten_kota" class="block text-sm font-medium text-gray-700">Kabupaten/Kota</label>
                        <input type="text" name="kabupaten_kota" id="kabupaten_kota" value="{{ old('kabupaten_kota', $school->kabupaten_kota) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                            placeholder="Nama Kabupaten/Kota">
                        @error('kabupaten_kota') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Provinsi --}}
                    <div>
                        <label for="provinsi" class="block text-sm font-medium text-gray-700">Provinsi</label>
                        <input type="text" name="provinsi" id="provinsi" value="{{ old('provinsi', $school->provinsi) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                            placeholder="Nama Provinsi">
                        @error('provinsi') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Google Maps Link (Recommended) --}}
                    <div class="md:col-span-2">
                        <label for="google_maps_link" class="block text-sm font-medium text-gray-700">
                            Link Google Maps <span class="text-green-600">(Direkomendasikan)</span>
                        </label>
                        <textarea name="google_maps_link" id="google_maps_link" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                            placeholder="Paste iframe code dari Google Maps di sini...">{{ old('google_maps_link', $school->google_maps_link) }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">
                            💡 <strong>Cara mudah:</strong> Buka Google Maps → Cari sekolah → Klik "Share" → Pilih "Embed a map" → Copy iframe code<br>
                            📝 <strong>Contoh iframe:</strong> &lt;iframe src="https://www.google.com/maps/embed?pb=..."&gt;&lt;/iframe&gt;
                        </p>
                        @error('google_maps_link') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Latitude (Optional) --}}
                    <div>
                        <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude (Opsional)</label>
                        <input type="text" name="latitude" id="latitude" value="{{ old('latitude', $school->latitude) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                            placeholder="Contoh: 2.9876543">
                        <p class="mt-1 text-sm text-gray-500">Hanya jika tidak ada link Google Maps</p>
                        @error('latitude') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Longitude (Optional) --}}
                    <div>
                        <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude (Opsional)</label>
                        <input type="text" name="longitude" id="longitude" value="{{ old('longitude', $school->longitude) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                            placeholder="Contoh: 99.0123456">
                        <p class="mt-1 text-sm text-gray-500">Hanya jika tidak ada link Google Maps</p>
                        @error('longitude') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Nomor HP --}}
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Nomor HP</label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone', $school->phone) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $school->email) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Website --}}
                    <div>
                        <label for="website" class="block text-sm font-medium text-gray-700">Website</label>
                        <input type="url" name="website" id="website" value="{{ old('website', $school->website) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('website') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Kepala Sekolah --}}
                    <div>
                        <label for="headmaster" class="block text-sm font-medium text-gray-700">Kepala Sekolah</label>
                        <input type="text" name="headmaster" id="headmaster" value="{{ old('headmaster', $school->headmaster) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('headmaster') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Password Admin Sekolah (Optional) --}}
                    <div class="md:col-span-2">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-blue-800 mb-2">🔐 Buat/Update Akun Admin Sekolah</h4>
                            <p class="text-xs text-blue-600 mb-3">Opsional: Jika diisi, akan membuat atau update akun admin sekolah dengan email yang sama</p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="admin_password" class="block text-sm font-medium text-gray-700">Password Admin Sekolah</label>
                                    <input type="password" name="admin_password" id="admin_password" value="{{ old('admin_password') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                        placeholder="Minimal 8 karakter">
                                    {{-- <p class="mt-1 text-xs text-gray-500">Password akan diberikan via WhatsApp</p> --}}
                                    @error('admin_password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="admin_password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                                    <input type="password" name="admin_password_confirmation" id="admin_password_confirmation" value="{{ old('admin_password_confirmation') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                        placeholder="Ulangi password">
                                    @error('admin_password_confirmation') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Action Buttons --}}
                <div class="mt-8 flex justify-between items-center">
                    <button type="button" x-data @click.prevent="$dispatch('open-modal', 'confirm-school-deletion')"
                        class="text-red-600 hover:text-red-800 font-bold transition text-sm">
                        Hapus Sekolah
                    </button>
                    <div class="flex space-x-3">
                        <a href="{{ route('dinas.schools.index') }}"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-lg transition">
                            Batal
                        </a>
                        <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg transition">
                            Update
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<x-modal name="confirm-school-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
    <form method="post" action="{{ route('dinas.schools.destroy', $school) }}" class="p-6">
        @csrf
        @method('delete')

        <h2 class="text-lg font-medium text-gray-900">
            Apakah Anda yakin ingin menghapus data sekolah ini?
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Setelah data sekolah dihapus, semua sumber daya dan data terkait akan dihapus secara permanen.
        </p>

        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')">
                Batal
            </x-secondary-button>

            <x-danger-button class="ml-3">
                Hapus Sekolah
            </x-danger-button>
        </div>
    </form>
</x-modal>
@endsection
