@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Detail Laporan Siswa</h1>
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <strong>Nama Siswa:</strong> {{ $report->student->name }}
            </div>
            <div>
                <strong>NISN:</strong> {{ $report->student->nisn }}
            </div>
            <div>
                <strong>Semester:</strong> {{ $report->semester }}
            </div>
            <div>
                <strong>Tahun Ajaran:</strong> {{ $report->academic_year }}
            </div>
        </div>
        <div class="mt-6">
            <h2 class="text-xl font-semibold mb-4">Nilai Mata Pelajaran</h2>
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="p-2 text-left">Mata Pelajaran</th>
                        <th class="p-2 text-left">Nilai</th>
                        <th class="p-2 text-left">Predikat</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report->subjects as $subject)
                    <tr class="border-b">
                        <td class="p-2">{{ $subject->name }}</td>
                        <td class="p-2">{{ $subject->score }}</td>
                        <td class="p-2">{{ $subject->grade }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            <strong>Catatan:</strong>
            <p>{{ $report->notes }}</p>
        </div>
    </div>
</div>
@endsection 