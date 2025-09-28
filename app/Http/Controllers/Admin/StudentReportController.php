<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StudentReportController extends Controller
{
    public function create(Student $student)
    {
        return view('admin.students.reports.create', compact('student'));
    }

    public function store(Request $request, Student $student)
    {
        $validated = $request->validate([
            'grade_class' => 'required|string|max:50',
            'semester' => 'required|in:Ganjil,Genap',
            'academic_year' => 'required|string|max:20',
            'file_path' => 'required|file|mimes:pdf|max:2048',
            'additional_notes' => 'nullable|string',
        ]);

        $filePath = $request->file('file_path')->store('student-reports', 'public');

        StudentReport::create([
            'student_id' => $student->id,
            'student_name' => $student->full_name,
            'grade_class' => $validated['grade_class'],
            'semester' => $validated['semester'],
            'academic_year' => $validated['academic_year'],
            'file_path' => $filePath,
            'additional_notes' => $validated['additional_notes'] ?? null,
            'uploaded_by' => Auth::id(),
        ]);

        return redirect()->route(Auth::user()->hasRole('admin_sekolah') ? 'sekolah.students.show' : 'dinas.students.show', $student)->with('success', 'Raport berhasil ditambahkan.');
    }

    public function destroy(Student $student, StudentReport $report)
    {
        if ($report->file_path) {
            Storage::disk('public')->delete($report->file_path);
        }
        $report->delete();
        return redirect()->route(Auth::user()->hasRole('admin_sekolah') ? 'sekolah.students.show' : 'dinas.students.show', $student)->with('success', 'Raport berhasil dihapus.');
    }
}
