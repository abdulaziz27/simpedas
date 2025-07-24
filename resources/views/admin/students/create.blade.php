@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#125047] py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="flex items-center space-x-2 text-white mb-6">
            <a href="{{ route('home') }}" class="hover:text-green-300">Dashboard</a>
            <span class="text-gray-300">&gt;</span>
            <a href="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.students.index') : route('dinas.students.index') }}" class="hover:text-green-300">Data Siswa</a>
            <span class="text-gray-300">&gt;</span>
            <span class="border-b-2 border-white">Form Input Data Siswa</span>
        </nav>

        <div class="bg-white rounded-xl p-8 shadow-lg">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Form Input Data Siswa</h1>

            <form action="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.students.store') : route('dinas.students.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

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

                    <div>
                        <label for="nisn" class="block text-sm font-medium text-gray-700">NIS / NISN</label>
                        <input type="text" name="nisn" id="nisn" value="{{ old('nisn') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('nisn') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}" placeholder="Nama siswa" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('full_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="birth_place_date" class="block text-sm font-medium text-gray-700">Tempat, Tanggal Lahir</label>
                        <div class="flex space-x-2">
                            <input type="text" name="birth_place" value="{{ old('birth_place') }}" placeholder="Contoh: Siantar" required class="mt-1 block w-1/2 rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <input type="date" name="birth_date" value="{{ old('birth_date') }}" required class="mt-1 block w-1/2 rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        </div>
                        @error('birth_place') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        @error('birth_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                        <select name="gender" id="gender" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="">Pilih</option>
                            @foreach(config('student.genders') as $value => $label)
                                <option value="{{ $value }}" {{ old('gender') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('gender') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="grade_level" class="block text-sm font-medium text-gray-700">Kelas / Jurusan / Tingkat</label>
                        <input type="text" name="grade_level" id="grade_level" value="{{ old('grade_level') }}" placeholder="Contoh: 9A / IPA / Kelas 9" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('grade_level') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="student_status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="student_status" id="student_status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            @foreach(config('student.student_statuses') as $value => $label)
                                <option value="{{ $value }}" {{ old('student_status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('student_status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="academic_year" class="block text-sm font-medium text-gray-700">Tahun Ajaran</label>
                        <input type="text" name="academic_year" id="academic_year" value="{{ old('academic_year') }}" placeholder="Contoh: 2024/2025" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('academic_year') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="achievements" class="block text-sm font-medium text-gray-700">Riwayat Prestasi</label>
                        <textarea name="achievements" id="achievements" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">{{ old('achievements') }}</textarea>
                        @error('achievements') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="graduation_status" class="block text-sm font-medium text-gray-700">Status Kelulusan</label>
                        <select name="graduation_status" id="graduation_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="">Pilih Status</option>
                            @foreach(config('student.graduation_statuses') as $value => $label)
                                <option value="{{ $value }}" {{ old('graduation_status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <p class="mt-2 text-sm text-gray-500">
                            Ijazah dapat di-upload setelah data siswa disimpan dan status kelulusan diatur ke 'Lulus'.
                        </p>
                        @error('graduation_status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.students.index') : route('dinas.students.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-lg transition">Batal</a>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
