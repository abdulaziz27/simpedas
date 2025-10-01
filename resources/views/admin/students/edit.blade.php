@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#125047] py-8" x-data="{ graduationStatus: '{{ old('graduation_status', $student->graduation_status) }}' }">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="flex items-center space-x-2 text-white mb-6">
            <a href="{{ route('home') }}" class="hover:text-green-300">Dashboard</a>
            <span class="text-gray-300">&gt;</span>
            <a href="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.students.index') : route('dinas.students.index') }}" class="hover:text-green-300">Data Siswa</a>
            <span class="text-gray-300">&gt;</span>
            <span class="border-b-2 border-white">Edit Data Siswa</span>
        </nav>

        <div class="bg-white rounded-xl p-8 shadow-lg">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Data Siswa</h1>

            <form action="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.students.update', $student) : route('dinas.students.update', $student) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

                    @if(Auth::user()->hasRole('admin_dinas'))
                    <div class="md:col-span-2">
                        <label for="school_id" class="block text-sm font-medium text-gray-700">Sekolah</label>
                        <select name="school_id" id="school_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}" {{ old('school_id', $student->school_id) == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                            @endforeach
                        </select>
                        @error('school_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    @endif

                    <div>
                        <label for="nisn" class="block text-sm font-medium text-gray-700">NIS / NISN</label>
                        <input type="text" name="nisn" id="nisn" value="{{ old('nisn', $student->nisn) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('nisn') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $student->full_name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('full_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="birth_place_date" class="block text-sm font-medium text-gray-700">Tempat, Tanggal Lahir</label>
                        <div class="flex space-x-2">
                            <input type="text" name="birth_place" value="{{ old('birth_place', $student->birth_place) }}" required class="mt-1 block w-1/2 rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <input type="date" name="birth_date" value="{{ old('birth_date', $student->birth_date->format('Y-m-d')) }}" required class="mt-1 block w-1/2 rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        </div>
                        @error('birth_place') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        @error('birth_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                        <select name="gender" id="gender" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            @foreach(config('student.genders') as $value => $label)
                                <option value="{{ $value }}" {{ old('gender', $student->gender) == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('gender') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="grade_level" class="block text-sm font-medium text-gray-700">Kelas / Jurusan / Tingkat</label>
                        <input type="text" name="grade_level" id="grade_level" value="{{ old('grade_level', $student->grade_level) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('grade_level') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="parent_name" class="block text-sm font-medium text-gray-700">Nama Orang Tua</label>
                        <input type="text" name="parent_name" id="parent_name" value="{{ old('parent_name', $student->parent_name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('parent_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="student_status" class="block text-sm font-medium text-gray-700">Status (Aktif / Tamat)</label>
                        <select name="student_status" id="student_status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            @foreach(config('student.student_statuses') as $value => $label)
                                <option value="{{ $value }}" {{ old('student_status', $student->student_status) == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('student_status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="achievements" class="block text-sm font-medium text-gray-700">Riwayat Prestasi</label>
                        <textarea name="achievements" id="achievements" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">{{ old('achievements', $student->achievements) }}</textarea>
                        @error('achievements') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="graduation_status" class="block text-sm font-medium text-gray-700">Status Kelulusan</label>
                        <select name="graduation_status" id="graduation_status" x-model="graduationStatus" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            @foreach(config('student.graduation_statuses') as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('graduation_status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                        <div x-show="graduationStatus === 'Lulus'" class="mt-4 bg-green-50 border-l-4 border-green-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.21 3.03-1.742 3.03H4.42c-1.532 0-2.492-1.696-1.742-3.03l5.58-9.92zM10 13a1 1 0 110-2 1 1 0 010 2zm-1-8a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-green-700">
                                        Data Ijazah dapat di-upload setelah menyimpan perubahan.
                                        <a href="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.students.certificate.create', $student) : route('dinas.students.certificate.create', $student) }}" class="font-medium underline hover:text-green-600">
                                            Klik di sini untuk upload ijazah.
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-between items-center">
                    <button type="button" x-data @click.prevent="$dispatch('open-modal', 'confirm-student-deletion')" class="text-red-600 hover:text-red-800 font-bold transition text-sm">Hapus Siswa</button>
                    <div class="flex space-x-3">
                        <a href="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.students.show', $student) : route('dinas.students.show', $student) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-lg transition">Batal</a>
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg transition">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<x-modal name="confirm-student-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
    <form method="post" action="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.students.destroy', $student) : route('dinas.students.destroy', $student) }}" class="p-6">
        @csrf
        @method('delete')
        <h2 class="text-lg font-medium text-gray-900">Apakah Anda yakin ingin menghapus data siswa ini?</h2>
        <p class="mt-1 text-sm text-gray-600">Semua data terkait siswa ini akan dihapus secara permanen.</p>
        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
            <x-danger-button class="ml-3">Hapus Siswa</x-danger-button>
        </div>
    </form>
</x-modal>
@endsection
