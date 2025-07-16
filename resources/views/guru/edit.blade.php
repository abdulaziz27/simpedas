@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#125047] py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="flex items-center space-x-2 text-white mb-6">
            <a href="{{ route('guru.profile.show') }}" class="hover:text-green-300">Profil Saya</a>
            <span class="text-gray-300">&gt;</span>
            <span class="border-b-2 border-white">Edit Data Guru</span>
        </nav>

        {{-- Success/Error Messages --}}
        @if (session('success'))
            <div id="successMessage" class="mb-6 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg text-center">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div id="errorMessage" class="mb-6 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg text-center">
                {{ session('error') }}
        </div>
        @endif

        {{-- Form Card --}}
        <div class="bg-white rounded-xl p-8 shadow-lg">
            <h1 class="text-2xl font-bold text-green-600 mb-6">Edit Data Guru</h1>

            <form action="{{ route('guru.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

                    {{-- Nama Lengkap --}}
                    <div>
                        <label for="full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $teacher->full_name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#125047] focus:ring-[#125047]">
                        @error('full_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- NUPTK --}}
                    <div>
                        <label for="nuptk" class="block text-sm font-medium text-gray-700">NUPTK</label>
                        <input type="text" name="nuptk" id="nuptk" value="{{ old('nuptk', $teacher->nuptk) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#125047] focus:ring-[#125047]">
                        @error('nuptk') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- NIP --}}
                    <div>
                        <label for="nip" class="block text-sm font-medium text-gray-700">NIP</label>
                        <input type="text" name="nip" id="nip" value="{{ old('nip', $teacher->nip) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#125047] focus:ring-[#125047]">
                        @error('nip') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Tempat, Tanggal Lahir --}}
                    <div>
                        <label for="birth_place_date" class="block text-sm font-medium text-gray-700">Tempat, Tanggal Lahir</label>
                        <div class="flex space-x-2">
                            <input type="text" name="birth_place" value="{{ old('birth_place', $teacher->birth_place) }}" placeholder="Tempat Lahir" required class="mt-1 block w-1/2 rounded-md border-gray-300 shadow-sm focus:border-[#125047] focus:ring-[#125047]">
                            <input type="date" name="birth_date" value="{{ old('birth_date', $teacher->birth_date ? $teacher->birth_date->format('Y-m-d') : '') }}" required class="mt-1 block w-1/2 rounded-md border-gray-300 shadow-sm focus:border-[#125047] focus:ring-[#125047]">
                        </div>
                        @error('birth_place') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        @error('birth_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Jenis Kelamin --}}
                     <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                        <select name="gender" id="gender" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#125047] focus:ring-[#125047]">
                            <option value="">Pilih</option>
                            <option value="Laki-laki" {{ old('gender', $teacher->gender) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('gender', $teacher->gender) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('gender') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Agama --}}
                    <div>
                        <label for="religion" class="block text-sm font-medium text-gray-700">Agama</label>
                        <select name="religion" id="religion" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#125047] focus:ring-[#125047]">
                            <option value="">Pilih</option>
                            <option value="Islam" {{ old('religion', $teacher->religion) == 'Islam' ? 'selected' : '' }}>Islam</option>
                            <option value="Kristen" {{ old('religion', $teacher->religion) == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                            <option value="Katolik" {{ old('religion', $teacher->religion) == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                            <option value="Hindu" {{ old('religion', $teacher->religion) == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                            <option value="Buddha" {{ old('religion', $teacher->religion) == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                            <option value="Konghucu" {{ old('religion', $teacher->religion) == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                        </select>
                        @error('religion') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Alamat --}}
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700">Alamat</label>
                        <textarea name="address" id="address" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#125047] focus:ring-[#125047]">{{ old('address', $teacher->address) }}</textarea>
                        @error('address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                    {{-- Mata Pelajaran --}}
                    <div>
                        <label for="subjects" class="block text-sm font-medium text-gray-700">Mata Pelajaran</label>
                        <input type="text" name="subjects" id="subjects" value="{{ old('subjects', $teacher->subjects) }}" placeholder="Contoh: Matematika, Fisika" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#125047] focus:ring-[#125047]">
                        @error('subjects') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- TMT Mengajar --}}
                    <div>
                        <label for="tmt" class="block text-sm font-medium text-gray-700">TMT Mengajar</label>
                        <input type="date" name="tmt" id="tmt" value="{{ old('tmt', $teacher->tmt ? $teacher->tmt->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#125047] focus:ring-[#125047]">
                        @error('tmt') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Pendidikan Terakhir --}}
                    <div>
                        <label for="education_level" class="block text-sm font-medium text-gray-700">Pendidikan Terakhir</label>
                        <input type="text" name="education_level" id="education_level" value="{{ old('education_level', $teacher->education_level) }}" placeholder="Contoh: S1 Pendidikan Matematika" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#125047] focus:ring-[#125047]">
                        @error('education_level') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Golongan --}}
                    <div>
                        <label for="rank" class="block text-sm font-medium text-gray-700">Golongan</label>
                        <input type="text" name="rank" id="rank" value="{{ old('rank', $teacher->rank) }}" placeholder="Contoh: III/c" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#125047] focus:ring-[#125047]">
                        @error('rank') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Status Kepegawaian --}}
                     <div>
                        <label for="employment_status" class="block text-sm font-medium text-gray-700">Status Kepegawaian</label>
                        <select name="employment_status" id="employment_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#125047] focus:ring-[#125047]">
                            <option value="">Pilih Status</option>
                            <option value="PNS" {{ old('employment_status', $teacher->employment_status) == 'PNS' ? 'selected' : '' }}>PNS</option>
                            <option value="Non PNS" {{ old('employment_status', $teacher->employment_status) == 'Non PNS' ? 'selected' : '' }}>Non PNS</option>
                            <option value="Honorer" {{ old('employment_status', $teacher->employment_status) == 'Honorer' ? 'selected' : '' }}>Honorer</option>
                        </select>
                        @error('employment_status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Jabatan --}}
                    <div>
                        <label for="position" class="block text-sm font-medium text-gray-700">Jabatan</label>
                        <input type="text" name="position" id="position" value="{{ old('position', $teacher->position) }}" placeholder="Contoh: Guru Kelas" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#125047] focus:ring-[#125047]">
                        @error('position') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Foto Profil --}}
                    <div class="md:col-span-2">
                        <label for="photo" class="block text-sm font-medium text-gray-700">Foto Profil</label>
                        @if($teacher->photo)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $teacher->photo) }}" alt="Foto saat ini" class="w-20 h-20 rounded-full object-cover">
                                <p class="text-sm text-gray-500 mt-1">Foto saat ini</p>
                            </div>
                        @endif
                        <input type="file" name="photo" id="photo" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#125047] file:text-white hover:file:bg-[#0E453F]">
                        <p class="mt-1 text-sm text-gray-500">Kosongkan jika tidak ingin mengubah foto.</p>
                        @error('photo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ route('guru.profile.show') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-lg transition">Batal</a>
                    <button type="submit" class="bg-[#125047] hover:bg-[#0E453F] text-white font-bold py-2 px-6 rounded-lg transition">Update</button>
            </div>
        </form>
        </div>
    </div>
</div>

<script>
// Auto-hide messages after 3 seconds
setTimeout(function() {
    const successMessage = document.getElementById('successMessage');
    const errorMessage = document.getElementById('errorMessage');
    if (successMessage) successMessage.style.display = 'none';
    if (errorMessage) errorMessage.style.display = 'none';
}, 3000);
</script>
@endsection
