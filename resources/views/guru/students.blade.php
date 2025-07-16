@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Daftar Siswa</h1>
    <div class="bg-white shadow-md rounded-lg p-6">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2 text-left">NISN</th>
                    <th class="p-2 text-left">Nama</th>
                    <th class="p-2 text-left">Kelas</th>
                    <th class="p-2 text-left">Jenis Kelamin</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                <tr class="border-b">
                    <td class="p-2">{{ $student->nisn }}</td>
                    <td class="p-2">{{ $student->name }}</td>
                    <td class="p-2">{{ $student->class }}</td>
                    <td class="p-2">{{ $student->gender }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection 