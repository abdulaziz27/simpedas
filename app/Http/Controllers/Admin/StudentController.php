<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\School;
use App\Models\StudentCertificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\StudentImportService;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentTemplateExport;

class StudentController extends Controller
{
    private function getStudentQuery()
    {
        $user = Auth::user();
        $query = Student::query();

        if ($user->hasRole('admin_sekolah')) {
            $query->where('school_id', $user->school_id);
        }

        return $query;
    }

    public function index(Request $request)
    {
        $query = $this->getStudentQuery();

        // Filter by school
        if ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        // Filter by grade level
        if ($request->filled('grade_level')) {
            $query->where('grade_level', $request->grade_level);
        }

        // Filter by student status
        if ($request->filled('student_status')) {
            $query->where('student_status', $request->student_status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        $students = $query->with('school')->latest()->paginate(10);
        $schools = \App\Models\School::all();

        return view('admin.students.index', compact('students', 'schools'));
    }

    public function create()
    {
        $schools = School::all();
        return view('admin.students.create', compact('schools'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'nisn' => 'required|string|max:20|unique:students,nisn',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'religion' => 'nullable|string|max:50',
            'grade_level' => 'required|string|max:20',
            'student_status' => 'required|in:Aktif,Tamat',
            'academic_year' => 'required|string|max:20',
            'school_id' => 'required_if:auth()->user()->hasRole("admin_dinas"),exists:schools,id',
        ]);

        if ($user->hasRole('admin_sekolah')) {
            $data['school_id'] = $user->school_id;
        }

        $student = Student::create($data);

        return redirect()->route($user->hasRole('admin_sekolah') ? 'sekolah.students.show' : 'dinas.students.show', $student)
            ->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function show(Student $student)
    {
        $this->authorizeAccess($student);
        return view('admin.students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $this->authorizeAccess($student);
        $schools = School::all();
        return view('admin.students.edit', compact('student', 'schools'));
    }

    public function update(Request $request, Student $student)
    {
        $this->authorizeAccess($student);

        $data = $request->validate([
            'full_name' => 'sometimes|required|string|max:255',
            'nisn' => 'sometimes|required|string|max:20|unique:students,nisn,' . $student->id,
            'gender' => 'sometimes|required|in:Laki-laki,Perempuan',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'religion' => 'nullable|string|max:50',
            'grade_level' => 'sometimes|required|string|max:20',
            'student_status' => 'sometimes|required|in:Aktif,Tamat',
            'academic_year' => 'sometimes|required|string|max:20',
            'major' => 'nullable|string|max:100',
            'achievements' => 'nullable|string',
            'graduation_status' => 'nullable|in:Belum Lulus,Lulus,Tidak Lulus',
            'school_id' => 'sometimes|required_if:auth()->user()->hasRole("admin_dinas"),exists:schools,id',
        ]);

        if (Auth::user()->hasRole('admin_sekolah')) {
            $data['school_id'] = Auth::user()->school_id;
        }

        $student->update($data);

        return redirect()->route(Auth::user()->hasRole('admin_sekolah') ? 'sekolah.students.show' : 'dinas.students.show', $student)
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Student $student)
    {
        $this->authorizeAccess($student);
        $student->delete();
        return redirect()->route(Auth::user()->hasRole('admin_sekolah') ? 'sekolah.students.index' : 'dinas.students.index')
            ->with('success', 'Data siswa berhasil dihapus.');
    }

    public function createCertificate(Student $student)
    {
        $this->authorizeAccess($student);
        return view('admin.students.certificate.upload', compact('student'));
    }

    public function storeCertificate(Request $request, Student $student)
    {
        $this->authorizeAccess($student);

        // Cek apakah siswa sudah memiliki ijazah
        if ($student->certificates()->exists()) {
            return redirect()->route(Auth::user()->hasRole('admin_sekolah') ? 'sekolah.students.show' : 'dinas.students.show', $student)
                ->with('error', 'Siswa ini sudah memiliki ijazah. Silakan hapus ijazah yang ada terlebih dahulu jika ingin mengunggah yang baru.');
        }

        $request->validate([
            'graduation_date' => 'required|date',
            'graduation_status' => 'required|in:Lulus,Tidak Lulus',
            'certificate_file' => 'required|file|mimes:pdf|max:2048',
        ]);

        $filePath = $request->file('certificate_file')->store('certificates', 'public');

        // Simpan data ijazah
        $certificate = StudentCertificate::create([
            'student_id' => $student->id,
            'student_name' => $student->full_name,
            'graduation_date' => $request->graduation_date,
            'graduation_status' => $request->graduation_status,
            'certificate_file' => $filePath,
            'uploaded_by' => Auth::id(),
        ]);

        // Update status siswa
        $student->update([
            'student_status' => $request->graduation_status === 'Lulus' ? 'Tamat' : $student->student_status,
            'graduation_status' => $request->graduation_status,
        ]);

        return redirect()->route(Auth::user()->hasRole('admin_sekolah') ? 'sekolah.students.show' : 'dinas.students.show', $student)
            ->with('success', 'Ijazah berhasil diunggah.');
    }

    public function showCertificate(Student $student)
    {
        $this->authorizeAccess($student);
        $certificates = $student->certificates()->latest('uploaded_at')->get();
        return view('admin.students.certificate.show', compact('student', 'certificates'));
    }

    public function deleteCertificate(Student $student, StudentCertificate $certificate)
    {
        $this->authorizeAccess($student);
        if ($certificate->certificate_file) {
            \Storage::disk('public')->delete($certificate->certificate_file);
        }
        $certificate->delete();
        return redirect()->route(Auth::user()->hasRole('admin_sekolah') ? 'sekolah.students.show' : 'dinas.students.show', $student)
            ->with('success', 'Ijazah berhasil dihapus.');
    }

    /**
     * Print student data
     */
    public function print(Student $student)
    {
        $this->authorizeAccess($student);
        return view('admin.students.print', compact('student'));
    }

    /**
     * Import students from Excel file
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $importService = new StudentImportService();
        $results = $importService->processExcel($request->file('file'));

        $message = "Import selesai: {$results['success']} berhasil, {$results['failed']} gagal.";

        return redirect()->back()
            ->with('success', $message)
            ->with('import_errors', $results['errors'])
            ->with('import_warnings', $results['warnings']);
    }

    /**
     * Download template Excel untuk import siswa
     */
    public function downloadTemplateSiswa()
    {
        try {
            return Excel::download(new StudentTemplateExport(), 'template_import_siswa.xlsx');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function authorizeAccess(Student $student)
    {
        $user = Auth::user();
        if ($user->hasRole('admin_sekolah') && $student->school_id !== $user->school_id) {
            abort(404);
        }
    }
}
