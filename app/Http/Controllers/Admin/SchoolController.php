<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\SchoolImportService;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SchoolTemplateExport;

\Log::info('SchoolController loaded');

class SchoolController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = School::query();

        if ($request->filled('education_level')) {
            $query->where('education_level', $request->education_level);
        }

        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('npsn', 'like', '%' . $request->search . '%');
            });
        }

        $schools = $query->latest()->paginate(9)->withQueryString();

        return view('admin.schools.index', [
            'schools' => $schools,
            'education_levels' => config('school.education_levels'),
            'regions' => config('school.regions'),
            'statuses' => config('school.status'),
            'filters' => $request->only(['education_level', 'region', 'status', 'search'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.schools.create', [
            'education_levels' => config('school.education_levels'),
            'regions' => config('school.regions'),
            'statuses' => config('school.status')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'npsn' => 'required|string|max:20|unique:schools,npsn',
            'education_level' => ['required', 'string', Rule::in(array_keys(config('school.education_levels')))],
            'status' => ['required', 'string', Rule::in(array_keys(config('school.status')))],
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'headmaster' => 'nullable|string|max:255',
            'region' => ['required', 'string', Rule::in(array_keys(config('school.regions')))],
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('school-logos', 'public');
        }

        School::create($validated);

        return redirect()->route('dinas.schools.index')->with('success', 'Data sekolah berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(School $school)
    {
        return view('admin.schools.show', compact('school'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(School $school)
    {
        return view('admin.schools.edit', [
            'school' => $school,
            'education_levels' => config('school.education_levels'),
            'regions' => config('school.regions'),
            'statuses' => config('school.status')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, School $school)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'npsn' => ['required', 'string', 'max:20', Rule::unique('schools')->ignore($school->id)],
            'education_level' => ['required', 'string', Rule::in(array_keys(config('school.education_levels')))],
            'status' => ['required', 'string', Rule::in(array_keys(config('school.status')))],
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'headmaster' => 'nullable|string|max:255',
            'region' => ['required', 'string', Rule::in(array_keys(config('school.regions')))],
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($school->logo) {
                \Storage::disk('public')->delete($school->logo);
            }
            $validated['logo'] = $request->file('logo')->store('school-logos', 'public');
        }

        $school->update($validated);

        return redirect()->route('dinas.schools.index')->with('success', 'Data sekolah berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(School $school)
    {
        $school->delete();
        return redirect()->route('dinas.schools.index')->with('success', 'Data sekolah berhasil dihapus.');
    }

    /**
     * Print school data
     */
    public function print(School $school)
    {
        return view('admin.schools.print', compact('school'));
    }

    /**
     * Import schools from Excel file
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $importService = new SchoolImportService();
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

        return redirect()->route('dinas.schools.index')
            ->with('success', $message)
            ->with('import_errors', $results['errors'])
            ->with('import_warnings', $results['warnings']);
    }

    /**
     * Download template Excel untuk import
     */
    public function downloadTemplateSekolah()
    {
        \Log::info('SchoolController downloadTemplateSekolah: mulai', [
            'user_id' => auth()->id(),
            'user_email' => optional(auth()->user())->email,
        ]);
        try {
            $response = \Excel::download(
                new \App\Exports\SchoolTemplateExport(),
                'template_import_sekolah.xlsx'
            );
            \Log::info('SchoolController downloadTemplateSekolah: sukses', [
                'user_id' => auth()->id(),
            ]);
            return $response;
        } catch (\Exception $e) {
            \Log::error('Error downloading template sekolah: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
