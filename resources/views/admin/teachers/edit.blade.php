@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#125047] py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="flex items-center space-x-2 text-white mb-6">
            <a href="{{ route('home') }}" class="hover:text-green-300">Dashboard</a>
            <span class="text-gray-300">&gt;</span>
            <a href="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.teachers.index') : route('dinas.teachers.index') }}" class="hover:text-green-300">Data Guru</a>
            <span class="text-gray-300">&gt;</span>
            <span class="border-b-2 border-white">Edit Data Guru</span>
        </nav>

        {{-- Form Card --}}
        <div class="bg-white rounded-xl p-8 shadow-lg">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Data Guru</h1>

            <form action="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.teachers.update', $teacher) : route('dinas.teachers.update', $teacher) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

                    {{-- === IDENTITAS DASAR === --}}
                    {{-- Nama Lengkap --}}
                    <div>
                        <label for="full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $teacher->full_name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('full_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- NUPTK --}}
                    <div>
                        <label for="nuptk" class="block text-sm font-medium text-gray-700">NUPTK</label>
                        <input type="text" name="nuptk" id="nuptk" value="{{ old('nuptk', $teacher->nuptk) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('nuptk') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Jenis Kelamin --}}
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700">Jenis Kelamin <span class="text-red-500">*</span></label>
                        <select name="gender" id="gender" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="">Pilih</option>
                            <option value="L" {{ old('gender', $teacher->gender) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('gender', $teacher->gender) == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('gender') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Tempat Lahir --}}
                    <div>
                        <label for="birth_place" class="block text-sm font-medium text-gray-700">Tempat Lahir</label>
                        <input type="text" name="birth_place" id="birth_place" value="{{ old('birth_place', $teacher->birth_place) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('birth_place') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Tanggal Lahir --}}
                    <div>
                        <label for="birth_date" class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                        <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date', $teacher->birth_date ? $teacher->birth_date->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('birth_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- NIP --}}
                    <div>
                        <label for="nip" class="block text-sm font-medium text-gray-700">NIP</label>
                        <input type="text" name="nip" id="nip" value="{{ old('nip', $teacher->nip) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('nip') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Status Kepegawaian --}}
                    <div>
                        <label for="employment_status" class="block text-sm font-medium text-gray-700">Status Kepegawaian</label>
                        <input type="text" name="employment_status" id="employment_status" value="{{ old('employment_status', $teacher->employment_status) }}" placeholder="PNS, PPPK, GTY, PTY" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('employment_status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Jenis PTK --}}
                    <div>
                        <label for="jenis_ptk" class="block text-sm font-medium text-gray-700">Jenis PTK</label>
                        <input type="text" name="jenis_ptk" id="jenis_ptk" value="{{ old('jenis_ptk', $teacher->jenis_ptk) }}" placeholder="Guru, Kepala Sekolah, Wakil Kepala Sekolah" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('jenis_ptk') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>



                    {{-- === GELAR & PENDIDIKAN === --}}
                    {{-- Gelar Depan --}}
                    <div>
                        <label for="gelar_depan" class="block text-sm font-medium text-gray-700">Gelar Depan</label>
                        <input type="text" name="gelar_depan" id="gelar_depan" value="{{ old('gelar_depan', $teacher->gelar_depan) }}" placeholder="Drs., Dr., Prof." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('gelar_depan') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Gelar Belakang --}}
                    <div>
                        <label for="gelar_belakang" class="block text-sm font-medium text-gray-700">Gelar Belakang</label>
                        <input type="text" name="gelar_belakang" id="gelar_belakang" value="{{ old('gelar_belakang', $teacher->gelar_belakang) }}" placeholder="S.Pd., M.Pd., S.Mers" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('gelar_belakang') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Jenjang --}}
                    <div>
                        <label for="jenjang" class="block text-sm font-medium text-gray-700">Jenjang</label>
                        <input type="text" name="jenjang" id="jenjang" value="{{ old('jenjang', $teacher->jenjang) }}" placeholder="S1, S2, S3, D3, D4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('jenjang') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Jurusan/Prodi --}}
                    <div>
                        <label for="education_major" class="block text-sm font-medium text-gray-700">Jurusan/Prodi</label>
                        <input type="text" name="education_major" id="education_major" value="{{ old('education_major', $teacher->education_major) }}" placeholder="Pendidikan Matematika" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('education_major') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Sertifikasi --}}
                    <div>
                        <label for="sertifikasi" class="block text-sm font-medium text-gray-700">Sertifikasi</label>
                        <input type="text" name="sertifikasi" id="sertifikasi" value="{{ old('sertifikasi', $teacher->sertifikasi) }}" placeholder="Mata pelajaran yang disertifikasi" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('sertifikasi') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- TMT Kerja --}}
                    <div>
                        <label for="tmt" class="block text-sm font-medium text-gray-700">TMT Kerja</label>
                        <input type="date" name="tmt" id="tmt" value="{{ old('tmt', $teacher->tmt ? $teacher->tmt->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('tmt') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- === TUGAS & MENGAJAR === --}}
                    {{-- Tugas Tambahan --}}
                    <div class="md:col-span-2">
                        <label for="tugas_tambahan" class="block text-sm font-medium text-gray-700">Tugas Tambahan</label>
                        <textarea name="tugas_tambahan" id="tugas_tambahan" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">{{ old('tugas_tambahan', $teacher->tugas_tambahan) }}</textarea>
                        @error('tugas_tambahan') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Mengajar --}}
                    <div class="md:col-span-2">
                        <label for="mengajar" class="block text-sm font-medium text-gray-700">Mengajar</label>
                        <textarea name="mengajar" id="mengajar" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">{{ old('mengajar', $teacher->mengajar) }}</textarea>
                        @error('mengajar') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- === JAM MENGAJAR === --}}
                    {{-- Jam Tugas Tambahan --}}
                    <div>
                        <label for="jam_tugas_tambahan" class="block text-sm font-medium text-gray-700">Jam Tugas Tambahan</label>
                        <input type="number" name="jam_tugas_tambahan" id="jam_tugas_tambahan" value="{{ old('jam_tugas_tambahan', $teacher->jam_tugas_tambahan) }}" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('jam_tugas_tambahan') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- JJM --}}
                    <div>
                        <label for="jjm" class="block text-sm font-medium text-gray-700">JJM</label>
                        <input type="number" name="jjm" id="jjm" value="{{ old('jjm', $teacher->jjm) }}" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('jjm') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Total JJM --}}
                    <div>
                        <label for="total_jjm" class="block text-sm font-medium text-gray-700">Total JJM</label>
                        <input type="number" name="total_jjm" id="total_jjm" value="{{ old('total_jjm', $teacher->total_jjm) }}" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('total_jjm') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Siswa --}}
                    <div>
                        <label for="siswa" class="block text-sm font-medium text-gray-700">Jumlah Siswa</label>
                        <input type="number" name="siswa" id="siswa" value="{{ old('siswa', $teacher->siswa) }}" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('siswa') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Kompetensi --}}
                    <div class="md:col-span-2">
                        <label for="kompetensi" class="block text-sm font-medium text-gray-700">Kompetensi</label>
                        <textarea name="kompetensi" id="kompetensi" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">{{ old('kompetensi', $teacher->kompetensi) }}</textarea>
                        @error('kompetensi') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- NIP --}}
                    <div>
                        <label for="nip" class="block text-sm font-medium text-gray-700">NIP (kalau PNS)</label>
                        <input type="text" name="nip" id="nip" value="{{ old('nip', $teacher->nip) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('nip') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Tempat, Tanggal Lahir --}}
                    <div>
                        <label for="birth_place_date" class="block text-sm font-medium text-gray-700">Tempat, Tanggal Lahir</label>
                         <div class="flex space-x-2">
                            <input type="text" name="birth_place" value="{{ old('birth_place', $teacher->birth_place) }}" placeholder="Tempat Lahir" required class="mt-1 block w-1/2 rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <input type="date" name="birth_date" value="{{ old('birth_date', $teacher->birth_date->format('Y-m-d')) }}" required class="mt-1 block w-1/2 rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        </div>
                        @error('birth_place') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        @error('birth_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Sekolah (hanya untuk admin_dinas) --}}
                    @if(Auth::user()->hasRole('admin_dinas'))
                    <div class="md:col-span-2">
                        <label for="school_id" class="block text-sm font-medium text-gray-700">Sekolah</label>
                        <select name="school_id" id="school_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="">Pilih Sekolah</option>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}" {{ old('school_id', $teacher->school_id) == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                            @endforeach
                        </select>
                        @error('school_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    @endif

                    {{-- Foto Profil --}}
                    <div class="md:col-span-2">
                        <label for="photo" class="block text-sm font-medium text-gray-700">Foto Profil</label>
                        @if($teacher->photo)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $teacher->photo) }}" alt="Foto saat ini" class="w-20 h-20 rounded-full object-cover">
                                <p class="text-sm text-gray-500 mt-1">Foto saat ini</p>
                            </div>
                        @endif
                        <input type="file" name="photo" id="photo" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                        <p class="mt-1 text-sm text-gray-500">Kosongkan jika tidak ingin mengubah foto.</p>
                        @error('photo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.teachers.index') : route('dinas.teachers.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-lg transition">Batal</a>
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg transition">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
