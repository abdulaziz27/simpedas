<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\TeacherImportService;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TeacherTemplateExport;

class TeacherController extends Controller
{
    private function getTeacherQuery()
    {
        $user = Auth::user();
        $query = Teacher::query();

        if ($user->hasRole('admin_sekolah')) {
            $query->where('school_id', $user->school_id);
        }

        return $query;
    }

    public function index(Request $request)
    {
        $query = $this->getTeacherQuery();

        // Filter by school
        if ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        // Filter by jenis_ptk
        if ($request->filled('jenis_ptk')) {
            $query->where('jenis_ptk', 'like', '%' . $request->jenis_ptk . '%');
        }

        // Filter by employment status
        if ($request->filled('employment_status')) {
            $query->where('employment_status', 'like', '%' . $request->employment_status . '%');
        }

        // Filter by mengajar (subjects)
        if ($request->filled('mengajar')) {
            $query->where(function($q) use ($request) {
                $q->where('mengajar', 'like', '%' . $request->mengajar . '%')
                  ->orWhere('subjects', 'like', '%' . $request->mengajar . '%');
            });
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('nuptk', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        $teachers = $query->with('school')->latest()->paginate(10)->withQueryString();
        $schools = Auth::user()->hasRole('admin_sekolah')
            ? \App\Models\School::where('id', Auth::user()->school_id)->get()
            : \App\Models\School::all();

        return view('admin.teachers.index', compact('teachers', 'schools'));
    }

    public function create()
    {
        $schools = School::all();
        return view('admin.teachers.create', compact('schools'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Validation rules for Dapodik format
        $data = $request->validate([
            // Required fields
            'full_name' => 'required|string|max:255',
            'nuptk' => 'nullable|string|max:20|unique:teachers,nuptk',
            
            // Basic info
            'gender' => 'required|in:L,P',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'nip' => 'nullable|string|max:20',
            
            // Employment info
            'employment_status' => 'nullable|string|max:100',
            'jenis_ptk' => 'nullable|string|max:100',
            
            // Academic credentials
            'gelar_depan' => 'nullable|string|max:50',
            'gelar_belakang' => 'nullable|string|max:50',
            'jenjang' => 'nullable|string|max:50',
            'education_major' => 'nullable|string|max:100',
            'sertifikasi' => 'nullable|string|max:100',
            
            // Work details
            'tmt' => 'nullable|date',
            'tugas_tambahan' => 'nullable|string',
            'mengajar' => 'nullable|string',
            'jam_tugas_tambahan' => 'nullable|integer|min:0',
            'jjm' => 'nullable|integer|min:0',
            'total_jjm' => 'nullable|integer|min:0',
            'siswa' => 'nullable|integer|min:0',
            'kompetensi' => 'nullable|string',
            
            // Additional fields
            'subjects' => 'nullable|string',
            'photo' => 'nullable|file|image|max:2048',
            'school_id' => $user->hasRole('admin_dinas')
                ? 'required|exists:schools,id'
                : 'sometimes',
        ], [
            // Custom error messages
            'school_id.required' => 'Pilih sekolah terlebih dahulu.',
            'full_name.required' => 'Nama lengkap harus diisi.',
            'nuptk.unique' => 'NUPTK sudah terdaftar.',
            'gender.required' => 'Jenis kelamin harus dipilih.',
            'gender.in' => 'Jenis kelamin harus L atau P.',
        ]);

        // Set school_id for admin_sekolah
        if ($user->hasRole('admin_sekolah')) {
            $data['school_id'] = $user->school_id;
        }

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('teacher_photos', 'public');
        }

        try {
            $teacher = Teacher::create($data);

            // Redirect with success message
            return redirect()->route(
                $user->hasRole('admin_sekolah') ? 'sekolah.teachers.index' : 'dinas.teachers.index'
            )->with('success', 'Data guru berhasil ditambahkan.');
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Teacher creation failed: ' . $e->getMessage());

            // Redirect back with error message
            return back()->withInput()->with('error', 'Gagal menambahkan data guru. Silakan cek kembali formulir Anda.');
        }
    }

    public function show(Teacher $teacher)
    {
        $this->authorizeAccess($teacher);
        return view('admin.teachers.show', compact('teacher'));
    }

    public function edit(Teacher $teacher)
    {
        $this->authorizeAccess($teacher);
        $schools = School::all();
        return view('admin.teachers.edit', compact('teacher', 'schools'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $this->authorizeAccess($teacher);

        $data = $request->validate([
            // Required fields
            'full_name' => 'required|string|max:255',
            'nuptk' => 'nullable|string|max:20|unique:teachers,nuptk,' . $teacher->id,
            
            // Basic info
            'gender' => 'required|in:L,P',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'nip' => 'nullable|string|max:20',
            
            // Employment info
            'employment_status' => 'nullable|string|max:100',
            'jenis_ptk' => 'nullable|string|max:100',
            
            // Academic credentials
            'gelar_depan' => 'nullable|string|max:50',
            'gelar_belakang' => 'nullable|string|max:50',
            'jenjang' => 'nullable|string|max:50',
            'education_major' => 'nullable|string|max:100',
            'sertifikasi' => 'nullable|string|max:100',
            
            // Work details
            'tmt' => 'nullable|date',
            'tugas_tambahan' => 'nullable|string',
            'mengajar' => 'nullable|string',
            'jam_tugas_tambahan' => 'nullable|integer|min:0',
            'jjm' => 'nullable|integer|min:0',
            'total_jjm' => 'nullable|integer|min:0',
            'siswa' => 'nullable|integer|min:0',
            'kompetensi' => 'nullable|string',
            
            // Additional fields
            'subjects' => 'nullable|string',
            'photo' => 'nullable|file|image|max:2048',
            'school_id' => Auth::user()->hasRole('admin_dinas')
                ? 'required|exists:schools,id'
                : 'sometimes',
        ], [
            'full_name.required' => 'Nama lengkap harus diisi.',
            'nuptk.unique' => 'NUPTK sudah terdaftar.',
            'gender.required' => 'Jenis kelamin harus dipilih.',
            'gender.in' => 'Jenis kelamin harus L atau P.',
        ]);

        try {
            // Handle photo upload
            if ($request->hasFile('photo')) {
                // Delete old photo if exists
                if ($teacher->photo && \Storage::disk('public')->exists($teacher->photo)) {
                    \Storage::disk('public')->delete($teacher->photo);
                }
                $data['photo'] = $request->file('photo')->store('teacher_photos', 'public');
            }

            // Update teacher data
            $teacher->update($data);

            return redirect()->route(
                Auth::user()->hasRole('admin_sekolah') ? 'sekolah.teachers.index' : 'dinas.teachers.index'
            )->with('success', "Data guru {$teacher->full_name} berhasil diperbarui.");
        } catch (\Exception $e) {
            \Log::error('Teacher update failed: ' . $e->getMessage());
            return back()->withInput()->with('error', "Gagal memperbarui data guru {$teacher->full_name}. Silakan coba lagi.");
        }
    }

    public function destroy(Teacher $teacher)
    {
        $this->authorizeAccess($teacher);

        try {
            // Delete associated photo if exists
            if ($teacher->photo && \Storage::disk('public')->exists($teacher->photo)) {
                \Storage::disk('public')->delete($teacher->photo);
            }

            $teacherName = $teacher->full_name;
            $teacher->delete();

            return redirect()->route(
                Auth::user()->hasRole('admin_sekolah') ? 'sekolah.teachers.index' : 'dinas.teachers.index'
            )->with('success', "Guru {$teacherName} berhasil dihapus.");
        } catch (\Exception $e) {
            \Log::error('Teacher deletion failed: ' . $e->getMessage());
            return back()->with('error', "Gagal menghapus guru {$teacher->full_name}. Silakan coba lagi.");
        }
    }

    /**
     * Print teacher data
     */
    public function print(Teacher $teacher)
    {
        $this->authorizeAccess($teacher);
        return view('admin.teachers.print', compact('teacher'));
    }

    /**
     * Import teachers from Excel file
     */
    public function import(Request $request)
    {
        try {
            \Log::info('Teacher import started', [
                'user_id' => Auth::id(),
                'user_email' => Auth::user()->email,
                'request_method' => $request->method(),
                'has_file' => $request->hasFile('file'),
                'file_size' => $request->hasFile('file') ? $request->file('file')->getSize() : 'no file',
                'all_input' => $request->all()
            ]);

            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            ]);

            \Log::info('File validation passed', [
                'file_name' => $request->file('file')->getClientOriginalName(),
                'file_size' => $request->file('file')->getSize(),
                'file_mime' => $request->file('file')->getMimeType()
            ]);

            $importService = new TeacherImportService();
            $results = $importService->processExcel($request->file('file'));

            \Log::info('Import completed', $results);

            $message = "Import selesai: {$results['success']} berhasil, {$results['failed']} gagal.";

            return redirect()->back()
                ->with('success', $message)
                ->with('import_errors', $results['errors'])
                ->with('import_warnings', $results['warnings']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Teacher import validation error: ' . $e->getMessage());
            \Log::error('Validation errors: ' . json_encode($e->errors()));

            $errorMessages = [];
            foreach ($e->errors() as $field => $errors) {
                foreach ($errors as $error) {
                    $errorMessages[] = $error;
                }
            }

            return redirect()->back()
                ->with('error', 'Validasi gagal: ' . implode(' | ', $errorMessages))
                ->with('import_errors', $errorMessages);
        } catch (\Exception $e) {
            \Log::error('Teacher import error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    /**
     * Download template Excel untuk import guru
     */
    public function downloadTemplateGuru()
    {
        try {
            return Excel::download(new TeacherTemplateExport(), 'template_import_guru.xlsx');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function authorizeAccess(Teacher $teacher)
    {
        $user = Auth::user();
        if ($user->hasRole('admin_sekolah') && $teacher->school_id !== $user->school_id) {
            abort(404);
        }
    }
}
