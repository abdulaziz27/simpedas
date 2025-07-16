<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\NonTeachingStaff;
use App\Models\StudentReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display reports dashboard
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('admin_dinas')) {
            return view('admin.reports.index-dinas');
        } elseif ($user->hasRole('admin_sekolah')) {
            return view('admin.reports.index-sekolah');
        }

        return redirect()->route('home');
    }

    /**
     * Laporan Rekap Sekolah (Admin Dinas)
     */
    public function schoolsReport()
    {
        $this->authorizeRole('admin_dinas');

        $schools = School::withCount(['teachers', 'students', 'nonTeachingStaff'])
            ->with(['students' => function ($query) {
                $query->select('school_id', 'student_status', DB::raw('count(*) as total'))
                    ->groupBy('school_id', 'student_status');
            }])
            ->get();

        return view('admin.reports.schools', compact('schools'));
    }

    /**
     * Laporan Statistik Guru (Admin Dinas)
     */
    public function teachersReport()
    {
        $this->authorizeRole('admin_dinas');

        $teachers = Teacher::with('school')
            ->select('employment_status', 'education_level', 'school_id', DB::raw('count(*) as total'))
            ->groupBy('employment_status', 'education_level', 'school_id')
            ->get();

        $statusStats = Teacher::select('employment_status', DB::raw('count(*) as total'))
            ->groupBy('employment_status')
            ->get();

        $educationStats = Teacher::select('education_level', DB::raw('count(*) as total'))
            ->groupBy('education_level')
            ->get();

        return view('admin.reports.teachers', compact('teachers', 'statusStats', 'educationStats'));
    }

    /**
     * Laporan Statistik Siswa (Admin Dinas)
     */
    public function studentsReport()
    {
        $this->authorizeRole('admin_dinas');

        $students = Student::with('school')
            ->select('student_status', 'grade_level', 'school_id', DB::raw('count(*) as total'))
            ->groupBy('student_status', 'grade_level', 'school_id')
            ->get();

        $statusStats = Student::select('student_status', DB::raw('count(*) as total'))
            ->groupBy('student_status')
            ->get();

        $gradeStats = Student::select('grade_level', DB::raw('count(*) as total'))
            ->groupBy('grade_level')
            ->get();

        return view('admin.reports.students', compact('students', 'statusStats', 'gradeStats'));
    }

    /**
     * Laporan Kelulusan (Admin Dinas)
     */
    public function graduationReport()
    {
        $this->authorizeRole('admin_dinas');

        $graduationStats = Student::with('school')
            ->select('school_id', 'graduation_status', 'academic_year', DB::raw('count(*) as total'))
            ->where('student_status', 'Tamat')
            ->groupBy('school_id', 'graduation_status', 'academic_year')
            ->orderBy('academic_year', 'desc')
            ->get();

        return view('admin.reports.graduation', compact('graduationStats'));
    }

    /**
     * Laporan Siswa Sekolah (Admin Sekolah)
     */
    public function schoolStudentsReport()
    {
        $this->authorizeRole('admin_sekolah');

        $user = Auth::user();
        $students = Student::where('school_id', $user->school_id)
            ->with('school')
            ->orderBy('full_name')
            ->get();

        return view('admin.reports.school-students', compact('students'));
    }

    /**
     * Laporan Guru Sekolah (Admin Sekolah)
     */
    public function schoolTeachersReport()
    {
        $this->authorizeRole('admin_sekolah');

        $user = Auth::user();
        $teachers = Teacher::where('school_id', $user->school_id)
            ->with('school')
            ->orderBy('full_name')
            ->get();

        return view('admin.reports.school-teachers', compact('teachers'));
    }

    /**
     * Laporan Raport (Admin Sekolah)
     */
    public function schoolReportsReport()
    {
        $this->authorizeRole('admin_sekolah');

        $user = Auth::user();
        $reports = StudentReport::whereHas('student', function ($query) use ($user) {
            $query->where('school_id', $user->school_id);
        })
            ->with(['student.school'])
            ->orderBy('academic_year', 'desc')
            ->get();

        return view('admin.reports.school-reports', compact('reports'));
    }

    /**
     * Helper method to authorize roles
     */
    private function authorizeRole($role)
    {
        if (!Auth::user()->hasRole($role)) {
            abort(403, 'Unauthorized action.');
        }
    }
}
