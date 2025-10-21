<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NonTeachingStaff;
use App\Models\School;
use App\Services\NonTeachingStaffImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class NonTeachingStaffController extends Controller
{
    private function getStaffQuery()
    {
        $user = Auth::user();
        $query = NonTeachingStaff::query();

        if ($user->hasRole('admin_sekolah')) {
            $query->where('school_id', $user->school_id);
        }

        return $query;
    }

    public function index(Request $request)
    {
        $query = $this->getStaffQuery();

        // Filter by school
        if ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        // Filter by position
        if ($request->filled('position')) {
            $query->where('position', 'like', '%' . $request->position . '%');
        }

        // Filter by employment status
        if ($request->filled('employment_status')) {
            $query->where('employment_status', $request->employment_status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('nip_nik', 'like', "%{$search}%");
            });
        }

        $staff = $query->with('school')->latest()->paginate(10)->withQueryString();
        $schools = Auth::user()->hasRole('admin_sekolah')
            ? \App\Models\School::where('id', Auth::user()->school_id)->get()
            : \App\Models\School::all();

        return view('admin.non-teaching-staff.index', compact('staff', 'schools'));
    }

    public function create()
    {
        $schools = School::all();
        return view('admin.non-teaching-staff.create', compact('schools'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'nip_nik' => 'nullable|string|max:20',
            'nuptk' => 'nullable|string|max:20',
            'birth_place' => 'required|string|max:100',
            'birth_date' => 'required|date',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'religion' => 'required|string|max:50',
            'address' => 'required|string',
            'position' => 'required|string|max:100',
            'education_level' => 'required|string|max:100',
            'employment_status' => 'required|string|max:20',
            'rank' => 'nullable|string|max:50',
            'tmt' => 'nullable|date',
            'status' => 'required|in:Aktif,Tidak Aktif',
            'school_id' => 'required_if:auth()->user()->hasRole("admin_dinas"),exists:schools,id',
        ]);
        if ($user->hasRole('admin_sekolah')) {
            $data['school_id'] = $user->school_id;
        }
        NonTeachingStaff::create($data);
        return redirect()->route(Auth::user()->hasRole('admin_sekolah') ? 'sekolah.non-teaching-staff.index' : 'dinas.non-teaching-staff.index')
            ->with('success', 'Data staf berhasil ditambahkan.');
    }

    public function show(NonTeachingStaff $nonTeachingStaff)
    {
        $this->authorizeAccess($nonTeachingStaff);
        return view('admin.non-teaching-staff.show', compact('nonTeachingStaff'));
    }

    public function edit(NonTeachingStaff $nonTeachingStaff)
    {
        $this->authorizeAccess($nonTeachingStaff);
        $schools = School::all();
        return view('admin.non-teaching-staff.edit', compact('nonTeachingStaff', 'schools'));
    }

    public function update(Request $request, NonTeachingStaff $nonTeachingStaff)
    {
        $this->authorizeAccess($nonTeachingStaff);

        $data = $request->validate([
            'full_name' => 'sometimes|nullable|string|max:255',
            'nip_nik' => 'nullable|string|max:20',
            'nuptk' => 'nullable|string|max:20',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:Laki-laki,Perempuan',
            'religion' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'position' => 'nullable|string|max:100',
            'education_level' => 'nullable|string|max:100',
            'employment_status' => 'nullable|string|max:20',
            'rank' => 'nullable|string|max:50',
            'tmt' => 'nullable|date',
            'status' => 'nullable|in:Aktif,Tidak Aktif',
            'school_id' => 'nullable|exists:schools,id',
        ]);

        if (Auth::user()->hasRole('admin_sekolah')) {
            $data['school_id'] = Auth::user()->school_id;
        }

        $nonTeachingStaff->update($data);

        return redirect()->route(Auth::user()->hasRole('admin_sekolah') ? 'sekolah.non-teaching-staff.show' : 'dinas.non-teaching-staff.show', $nonTeachingStaff->id)
            ->with('success', 'Data staf berhasil diperbarui.');
    }

    public function destroy(NonTeachingStaff $nonTeachingStaff)
    {
        $this->authorizeAccess($nonTeachingStaff);
        $nonTeachingStaff->delete();
        return redirect()->route(Auth::user()->hasRole('admin_sekolah') ? 'sekolah.non-teaching-staff.index' : 'dinas.non-teaching-staff.index')
            ->with('success', 'Data staf berhasil dihapus.');
    }

    /**
     * Print non-teaching staff data
     */
    public function print(NonTeachingStaff $nonTeachingStaff)
    {
        $this->authorizeAccess($nonTeachingStaff);
        return view('admin.non-teaching-staff.print', compact('nonTeachingStaff'));
    }

    /**
     * Import non-teaching staff from Excel file
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $importService = new NonTeachingStaffImportService();
        $results = $importService->processExcel($request->file('file'));

        $message = "Import selesai: {$results['success']} berhasil, {$results['failed']} gagal.";

        if (!empty($results['errors'])) {
            $message .= "\n\nError: " . implode("\n", array_slice($results['errors'], 0, 5));
            if (count($results['errors']) > 5) {
                $message .= "\n... dan " . (count($results['errors']) - 5) . " error lainnya.";
            }
        }

        if (!empty($results['warnings'])) {
            $message .= "\n\nWarning: " . implode("\n", array_slice($results['warnings'], 0, 5));
            if (count($results['warnings']) > 5) {
                $message .= "\n... dan " . (count($results['warnings']) - 5) . " warning lainnya.";
            }
        }

        $routeName = Auth::user()->hasRole('admin_sekolah') ? 'sekolah.non-teaching-staff.index' : 'dinas.non-teaching-staff.index';

        return redirect()->route($routeName)
            ->with('success', $message)
            ->with('import_errors', $results['errors'])
            ->with('import_warnings', $results['warnings']);
    }

    /**
     * Download template Excel untuk import
     */
    public function downloadTemplateStaff()
    {
        \Log::info('NonTeachingStaffController downloadTemplateStaff: mulai', [
            'user_id' => auth()->id(),
            'user_email' => optional(auth()->user())->email,
        ]);
        try {
            $response = \Excel::download(
                new \App\Exports\NonTeachingStaffTemplateExport(),
                'template_import_staff.xlsx'
            );
            \Log::info('NonTeachingStaffController downloadTemplateStaff: sukses', [
                'user_id' => auth()->id(),
            ]);
            return $response;
        } catch (\Exception $e) {
            \Log::error('Error downloading template staff: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function authorizeAccess(NonTeachingStaff $staff)
    {
        $user = Auth::user();
        if ($user->hasRole('admin_sekolah') && $staff->school_id !== $user->school_id) {
            abort(404);
        }
    }
}
