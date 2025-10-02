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
            $query->where('sekolah_id', $user->school_id);
        }

        return $query;
    }

    public function index(Request $request)
    {
        $query = $this->getStudentQuery();

        // Filter by school
        if ($request->filled('school_id')) {
            $query->where('sekolah_id', $request->school_id);
        }

        // Filter by rombel
        if ($request->filled('rombel')) {
            $query->where('rombel', $request->rombel);
        }

        // Filter by student status
        if ($request->filled('status_siswa')) {
            $query->where('status_siswa', $request->status_siswa);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('nipd', 'like', "%{$search}%");
            });
        }

        $students = $query->with('school')->latest()->paginate(15)->withQueryString();
        $schools = Auth::user()->hasRole('admin_sekolah')
            ? \App\Models\School::where('id', Auth::user()->school_id)->get()
            : \App\Models\School::all();

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
            // ==== W A J I B ====
            'nisn' => 'required|string|max:20|unique:students,nisn',
            'nama_lengkap' => 'required|string|max:150',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'agama' => 'required|string|max:50',
            'rombel' => 'required|string|max:50',
            'sekolah_id' => 'required|exists:schools,id',

            // ==== O P S I O N A L ====
            'nipd' => 'nullable|string|max:20',
            'foto' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'kelurahan' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            'status_siswa' => 'nullable|in:aktif,tamat,pindah',
            'nama_ayah' => 'nullable|string|max:150',
            'pekerjaan_ayah' => 'nullable|string|max:100',
            'nama_ibu' => 'nullable|string|max:150',
            'pekerjaan_ibu' => 'nullable|string|max:100',
            'anak_ke' => 'nullable|integer|min:1|max:20',
            'jumlah_saudara' => 'nullable|integer|min:0|max:20',
            'no_hp' => 'nullable|string|max:20',
            'kip' => 'nullable|boolean',
            'transportasi' => 'nullable|string|max:50',
            'jarak_rumah_sekolah' => 'nullable|numeric|min:0|max:999.99',
            'tinggi_badan' => 'nullable|integer|min:50|max:250',
            'berat_badan' => 'nullable|integer|min:10|max:200',
        ]);

        if ($user->hasRole('admin_sekolah')) {
            $data['sekolah_id'] = $user->school_id;
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
            // ==== W A J I B ====
            'nisn' => 'required|string|max:20|unique:students,nisn,' . $student->id,
            'nama_lengkap' => 'required|string|max:150',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'agama' => 'required|string|max:50',
            'rombel' => 'required|string|max:50',
            'sekolah_id' => 'required|exists:schools,id',

            // ==== O P S I O N A L ====
            'nipd' => 'nullable|string|max:20',
            'foto' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'kelurahan' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            'status_siswa' => 'nullable|in:aktif,tamat,pindah',
            'nama_ayah' => 'nullable|string|max:150',
            'pekerjaan_ayah' => 'nullable|string|max:100',
            'nama_ibu' => 'nullable|string|max:150',
            'pekerjaan_ibu' => 'nullable|string|max:100',
            'anak_ke' => 'nullable|integer|min:1|max:20',
            'jumlah_saudara' => 'nullable|integer|min:0|max:20',
            'no_hp' => 'nullable|string|max:20',
            'kip' => 'nullable|boolean',
            'transportasi' => 'nullable|string|max:50',
            'jarak_rumah_sekolah' => 'nullable|numeric|min:0|max:999.99',
            'tinggi_badan' => 'nullable|integer|min:50|max:250',
            'berat_badan' => 'nullable|integer|min:10|max:200',
        ]);

        if (Auth::user()->hasRole('admin_sekolah')) {
            $data['sekolah_id'] = Auth::user()->school_id;
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
            'student_name' => $student->nama_lengkap,
            'graduation_date' => $request->graduation_date,
            'graduation_status' => $request->graduation_status,
            'certificate_file' => $filePath,
            'uploaded_by' => Auth::id(),
        ]);

        // Update status siswa
        $student->update([
            'status_siswa' => $request->graduation_status === 'Lulus' ? 'tamat' : $student->status_siswa,
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

        try {
            \Log::info('[STUDENT_IMPORT] Import dimulai', [
                'user_id' => auth()->id(),
                'user_email' => auth()->user()->email,
                'file_name' => $request->file('file')->getClientOriginalName(),
                'file_size' => $request->file('file')->getSize(),
            ]);

            $import = new \App\Imports\StudentImport();
            \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('file'), null, \Maatwebsite\Excel\Excel::XLSX, [
                'validate' => false
            ]);
            $results = $import->getResults();

            \Log::info('[STUDENT_IMPORT] Import selesai', [
                'user_id' => auth()->id(),
                'success_count' => $results['success'],
                'failed_count' => $results['failed'],
                'errors' => $results['errors'],
                'warnings' => $results['warnings'],
            ]);

            $message = "Import selesai: {$results['success']} berhasil, {$results['failed']} gagal.";

            if ($results['failed'] > 0) {
                $message .= " Silakan periksa error di bawah ini.";
            }

            return redirect()->back()
                ->with('success', $message)
                ->with('import_errors', $results['errors'])
                ->with('import_warnings', $results['warnings'])
                ->with('import_results', $results);
        } catch (\Exception $e) {
            \Log::error('[STUDENT_IMPORT] Import gagal', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'Import gagal: ' . $e->getMessage());
        }
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
        if ($user->hasRole('admin_sekolah') && $student->sekolah_id !== $user->school_id) {
            abort(404);
        }
    }
}
