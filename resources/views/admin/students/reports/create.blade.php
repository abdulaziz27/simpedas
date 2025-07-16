@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#17695a] py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="flex items-center space-x-2 text-white mb-6">
            <a href="{{ route('home') }}" class="hover:text-green-300">Dashboard</a>
            <span class="text-gray-300">&gt;</span>
            <a href="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.students.index') : route('admin.students.index') }}" class="hover:text-green-300">Data Siswa</a>
            <span class="text-gray-300">&gt;</span>
            <a href="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.students.show', $student) : route('admin.students.show', $student) }}" class="hover:text-green-300">Detail Siswa</a>
            <span class="text-gray-300">&gt;</span>
            <span class="border-b-2 border-white">Tambah Raport</span>
        </nav>

        <div class="bg-white rounded-xl p-8 shadow-lg">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Tambah Raport Siswa</h1>

            <form action="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.students.reports.store', $student) : route('admin.students.reports.store', $student) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 gap-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Siswa</label>
                        <input type="text" value="{{ $student->full_name }}" readonly class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100">
                    </div>
                    <div>
                        <label for="grade_class" class="block text-sm font-medium text-gray-700">Kelas / Tingkat</label>
                        <input type="text" name="grade_class" id="grade_class" value="{{ old('grade_class', $student->grade_level) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('grade_class') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="semester" class="block text-sm font-medium text-gray-700">Semester</label>
                        <select name="semester" id="semester" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="">Pilih Semester</option>
                            <option value="Ganjil" {{ old('semester') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="Genap" {{ old('semester') == 'Genap' ? 'selected' : '' }}>Genap</option>
                        </select>
                        @error('semester') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="academic_year" class="block text-sm font-medium text-gray-700">Tahun Ajaran</label>
                        <input type="text" name="academic_year" id="academic_year" value="{{ old('academic_year') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="2023/2024">
                        @error('academic_year') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="file_path" class="block text-sm font-medium text-gray-700">Upload Raport (PDF)</label>
                        <input type="file" name="file_path" id="file_path" required accept=".pdf" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                        @error('file_path') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="additional_notes" class="block text-sm font-medium text-gray-700">Catatan Tambahan</label>
                        <textarea name="additional_notes" id="additional_notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">{{ old('additional_notes') }}</textarea>
                        @error('additional_notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.students.show', $student) : route('admin.students.show', $student) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-lg transition">Batal</a>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
