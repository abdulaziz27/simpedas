@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#125047] py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="flex items-center space-x-2 text-white mb-6">
            <a href="{{ route('home') }}" class="hover:text-green-300">Dashboard</a>
            <span class="text-gray-300">&gt;</span>
            <a href="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.non-teaching-staff.index') : route('dinas.non-teaching-staff.index') }}" class="hover:text-green-300">Manajemen Tenaga Pendidik Non Guru</a>
            <span class="text-gray-300">&gt;</span>
            <span class="border-b-2 border-white">Form Input Data Tenaga Pendidik Non Guru</span>
        </nav>

        {{-- Form Card --}}
        <div class="bg-white rounded-xl p-8 shadow-lg">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Form Input Data Tenaga Pendidik Non Guru</h1>

            <form action="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.non-teaching-staff.store') : route('dinas.non-teaching-staff.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

                    {{-- Nama Lengkap --}}
                    <div>
                        <label for="full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('full_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- NUPTK --}}
                    <div>
                        <label for="nuptk" class="block text-sm font-medium text-gray-700">NUPTK (kalau ada)</label>
                                <input type="text" name="nuptk" id="nuptk" value="{{ old('nuptk') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                @error('nuptk') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                    {{-- NIP --}}
                    <div>
                        <label for="nip_nik" class="block text-sm font-medium text-gray-700">NIP (kalau PNS)</label>
                        <input type="text" name="nip_nik" id="nip_nik" value="{{ old('nip_nik') }}" placeholder="Kosongkan jika non-PNS" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('nip_nik') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    {{-- Jabatan --}}
                    <div>
                        <label for="position" class="block text-sm font-medium text-gray-700">Jabatan</label>
                        <input type="text" name="position" id="position" value="{{ old('position') }}" placeholder="Contoh: Operator, Pustakawan" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('position') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="Aktif" {{ old('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Tidak Aktif" {{ old('status') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                        @error('status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                    {{-- Tempat, Tanggal Lahir --}}
                            <div>
                        <label for="birth_place_date" class="block text-sm font-medium text-gray-700">Tempat, Tanggal Lahir</label>
                                <div class="flex space-x-2">
                            <input type="text" name="birth_place" value="{{ old('birth_place') }}" placeholder="Tempat Lahir" required class="mt-1 block w-1/2 rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <input type="date" name="birth_date" value="{{ old('birth_date') }}" required class="mt-1 block w-1/2 rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                </div>
                        @error('birth_place') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        @error('birth_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                    {{-- Jenis Kelamin --}}
                             <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                                <select name="gender" id="gender" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                    <option value="">Pilih</option>
                            <option value="Laki-laki" {{ old('gender') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('gender') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('gender') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                    {{-- Agama --}}
                            <div>
                                <label for="religion" class="block text-sm font-medium text-gray-700">Agama</label>
                        <select name="religion" id="religion" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                    <option value="">Pilih</option>
                            <option value="Islam" {{ old('religion') == 'Islam' ? 'selected' : '' }}>Islam</option>
                            <option value="Kristen" {{ old('religion') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                            <option value="Katolik" {{ old('religion') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                            <option value="Hindu" {{ old('religion') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                            <option value="Buddha" {{ old('religion') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                            <option value="Konghucu" {{ old('religion') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                                </select>
                                @error('religion') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                    {{-- Alamat --}}
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700">Alamat</label>
                        <textarea name="address" id="address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" required>{{ old('address') }}</textarea>
                        @error('address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Status Kepegawaian --}}
                    <div>
                        <label for="employment_status" class="block text-sm font-medium text-gray-700">Status Kepegawaian</label>
                        <select name="employment_status" id="employment_status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                    <option value="">Pilih</option>
                            <option value="PNS" {{ old('employment_status') == 'PNS' ? 'selected' : '' }}>PNS</option>
                            <option value="PPPK" {{ old('employment_status') == 'PPPK' ? 'selected' : '' }}>PPPK</option>
                            <option value="PTT" {{ old('employment_status') == 'PTT' ? 'selected' : '' }}>PTT</option>
                            <option value="Kontrak" {{ old('employment_status') == 'Kontrak' ? 'selected' : '' }}>Kontrak</option>
                                </select>
                        @error('employment_status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                    {{-- Golongan --}}
                            <div>
                        <label for="rank" class="block text-sm font-medium text-gray-700">Golongan (jika PNS)</label>
                        <input type="text" name="rank" id="rank" value="{{ old('rank') }}" placeholder="Contoh: III/c" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('rank') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- TMT Mengajar --}}
                    <div>
                        <label for="tmt" class="block text-sm font-medium text-gray-700">TMT Mengajar</label>
                        <input type="date" name="tmt" id="tmt" value="{{ old('tmt') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('tmt') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Pendidikan Terakhir --}}
                    <div>
                        <label for="education_level" class="block text-sm font-medium text-gray-700">Pendidikan Terakhir</label>
                        <input type="text" name="education_level" id="education_level" value="{{ old('education_level') }}" placeholder="Contoh: S1 Administrasi Pendidikan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('education_level') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                    {{-- Sekolah (hanya untuk admin_dinas) --}}
                    @if(Auth::user()->hasRole('admin_dinas'))
                    <div class="md:col-span-2">
                        <label for="school_id" class="block text-sm font-medium text-gray-700">Sekolah</label>
                        <select name="school_id" id="school_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="">Pilih Sekolah</option>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                            @endforeach
                        </select>
                        @error('school_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                    @endif

                    {{-- Foto Profil --}}
                    <div class="md:col-span-2">
                        <label for="photo" class="block text-sm font-medium text-gray-700">Foto Profil</label>
                        <input type="file" name="photo" id="photo" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                        <p class="mt-1 text-sm text-gray-500">Kosongkan jika tidak ingin mengubah foto.</p>
                        @error('photo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.non-teaching-staff.index') : route('dinas.non-teaching-staff.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-lg transition">Batal</a>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
