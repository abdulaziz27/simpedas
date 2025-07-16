@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Laporan Siswa</h1>
    <div class="bg-white shadow-md rounded-lg p-6">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2 text-left">Nama Siswa</th>
                    <th class="p-2 text-left">Semester</th>
                    <th class="p-2 text-left">Tahun Ajaran</th>
                    <th class="p-2 text-left">Status</th>
                    <th class="p-2 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reports as $report)
                <tr class="border-b">
                    <td class="p-2">{{ $report->student->name }}</td>
                    <td class="p-2">{{ $report->semester }}</td>
                    <td class="p-2">{{ $report->academic_year }}</td>
                    <td class="p-2">{{ $report->status }}</td>
                    <td class="p-2">
                        <a href="{{ route('guru.report.detail', $report->id) }}" class="text-blue-500 hover:underline">Lihat Detail</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection 