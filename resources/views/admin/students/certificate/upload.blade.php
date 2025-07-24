@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#17695a] py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="flex items-center space-x-2 text-white mb-6">
            <a href="{{ route('home') }}" class="hover:text-green-300">Dashboard</a>
            <span class="text-gray-300">&gt;</span>
            <a href="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.students.index') : route('dinas.students.index') }}" class="hover:text-green-300">Data Siswa</a>
            <span class="text-gray-300">&gt;</span>
            <a href="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.students.edit', $student) : route('dinas.students.edit', $student) }}" class="hover:text-green-300">Edit Siswa</a>
            <span class="text-gray-300">&gt;</span>
            <span class="border-b-2 border-white">Upload Ijazah</span>
        </nav>

        <div class="bg-white rounded-xl p-8 shadow-lg">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Upload Ijazah Siswa</h1>

            {{-- Pesan sukses/error --}}
            @if (session('success'))
                <div class="mb-6 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg text-center">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-6 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg text-center">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.students.certificate.store', $student) : route('dinas.students.certificate.store', $student) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Nama Siswa</label>
                        <input type="text" value="{{ $student->full_name }}" readonly class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100">
                    </div>

                    <div>
                        <label for="graduation_date" class="block text-sm font-medium text-gray-700">Tanggal Lulus</label>
                        <input type="date" name="graduation_date" id="graduation_date" value="{{ old('graduation_date', now()->format('Y-m-d')) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('graduation_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="graduation_status" class="block text-sm font-medium text-gray-700">Status Kelulusan</label>
                        <input type="text" name="graduation_status_display" value="Lulus" readonly class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100">
                        <input type="hidden" name="graduation_status" value="Lulus">
                        <input type="hidden" name="student_status" id="student_status" value="Tamat">
                    </div>

                    <div class="md:col-span-2">
                        <label for="certificate_file" class="block text-sm font-medium text-gray-700">Upload Ijazah (PDF)</label>
                        <input type="file" name="certificate_file" id="certificate_file" required accept=".pdf" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                        @error('certificate_file') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.students.show', $student) : route('dinas.students.show', $student) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-lg transition">Batal</a>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg transition">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
