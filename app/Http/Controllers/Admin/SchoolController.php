<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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

        if ($request->filled('kecamatan')) {
            $query->where('kecamatan', 'like', '%' . $request->kecamatan . '%');
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
            'statuses' => config('school.status'),
            'filters' => $request->only(['education_level', 'kecamatan', 'status', 'search'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.schools.create', [
            'education_levels' => config('school.education_levels'),
            'statuses' => config('school.status')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'npsn' => 'required|string|max:20|unique:schools,npsn',
            'education_level' => ['required', 'string', Rule::in(array_keys(config('school.education_levels')))],
            'status' => ['required', 'string', Rule::in(array_keys(config('school.status')))],
            'address' => 'nullable|string',
            'desa' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kabupaten_kota' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'google_maps_link' => 'nullable|string|max:2000',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'phone' => 'nullable|string|max:20',
            'email' => ['required', 'max:255', 'unique:schools,email', function ($attribute, $value, $fail) {
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $fail('Format email tidak valid: ' . $value);
                }
            }],
            'website' => 'nullable|url|max:255',
            'headmaster' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'admin_password' => 'nullable|string|min:8|confirmed',
            'admin_password_confirmation' => 'nullable|string|min:8'
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('school-logos', 'public');
        }

        // Remove password fields from school data
        $adminPassword = $validated['admin_password'] ?? null;
        unset($validated['admin_password'], $validated['admin_password_confirmation']);

        $school = School::create($validated);

        // Create admin sekolah account if password provided
        if ($adminPassword) {
            try {
                $user = \App\Models\User::create([
                    'name' => $validated['headmaster'] ?? 'Admin Sekolah',
                    'email' => $validated['email'],
                    'password' => \Hash::make($adminPassword),
                    'school_id' => $school->id,
                ]);
                $user->assignRole('admin_sekolah');

                \Log::info("Admin sekolah account created for school: {$school->name} with email: {$validated['email']}");
            } catch (\Exception $e) {
                \Log::error("Failed to create admin sekolah account: " . $e->getMessage());
                return redirect()->back()
                    ->with('warning', 'Sekolah berhasil dibuat, tetapi gagal membuat akun admin sekolah. Silakan buat manual di User Management.')
                    ->withInput();
            }
        }

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
            'statuses' => config('school.status')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, School $school)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'npsn' => ['required', 'string', 'max:20', Rule::unique('schools')->ignore($school->id)],
            'education_level' => ['required', 'string', Rule::in(array_keys(config('school.education_levels')))],
            'status' => ['required', 'string', Rule::in(array_keys(config('school.status')))],
            'address' => 'nullable|string',
            'desa' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kabupaten_kota' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'google_maps_link' => 'nullable|string|max:2000',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'phone' => 'nullable|string|max:20',
            'email' => ['required', 'max:255', Rule::unique('schools', 'email')->ignore($school->id), function ($attribute, $value, $fail) {
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $fail('Format email tidak valid: ' . $value);
                }
            }],
            'website' => 'nullable|url|max:255',
            'headmaster' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'admin_password' => 'nullable|string|min:8|confirmed',
            'admin_password_confirmation' => 'nullable|string|min:8'
        ]);

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($school->logo) {
                \Storage::disk('public')->delete($school->logo);
            }
            $validated['logo'] = $request->file('logo')->store('school-logos', 'public');
        }

        // Remove password fields from school data
        $adminPassword = $validated['admin_password'] ?? null;
        unset($validated['admin_password'], $validated['admin_password_confirmation']);

        $school->update($validated);

        // Create or update admin sekolah account if password provided
        if ($adminPassword) {
            try {
                $user = \App\Models\User::updateOrCreate(
                    ['email' => $validated['email']],
                    [
                        'name' => $validated['headmaster'] ?? 'Admin Sekolah',
                        'password' => \Hash::make($adminPassword),
                        'school_id' => $school->id,
                    ]
                );

                if (!$user->hasRole('admin_sekolah')) {
                    $user->assignRole('admin_sekolah');
                }

                \Log::info("Admin sekolah account updated for school: {$school->name} with email: {$validated['email']}");
            } catch (\Exception $e) {
                \Log::error("Failed to update admin sekolah account: " . $e->getMessage());
                return redirect()->back()
                    ->with('warning', 'Sekolah berhasil diperbarui, tetapi gagal membuat/update akun admin sekolah. Silakan buat manual di User Management.')
                    ->withInput();
            }
        }

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

        // Store import progress in database
        $importId = 'import_' . time() . '_' . auth()->id();
        $progress = \App\Models\ImportProgress::create([
            'import_id' => $importId,
            'user_id' => auth()->id(),
            'status' => 'starting',
            'total' => 0,
            'processed' => 0,
            'success' => 0,
            'failed' => 0,
            'errors' => [],
            'warnings' => [],
            'started_at' => now(),
        ]);

        try {
            // Use optimized import service with auto strategy selection
            $importService = new \App\Services\OptimizedSchoolImportService();
            $results = $importService->processExcel($request->file('file'), $importId, 'auto');

            // Update final progress
            $progress->update([
                'status' => 'completed',
                'total' => $results['total'] ?? 0,
                'processed' => $results['processed'] ?? 0,
                'success' => $results['success'] ?? 0,
                'failed' => $results['failed'] ?? 0,
                'errors' => $results['errors'] ?? [],
                'warnings' => $results['warnings'] ?? [],
                'completed_at' => now(),
            ]);

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
        } catch (\Exception $e) {
            // Update progress with error
            $progress->update([
                'status' => 'error',
                'errors' => [$e->getMessage()],
                'completed_at' => now(),
            ]);

            return redirect()->route('dinas.schools.index')
                ->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }

    /**
     * Get import progress
     */
    public function importProgress(Request $request)
    {
        // Get latest progress from database
        $progress = \App\Models\ImportProgress::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->first();

        // Debug logging
        \Log::info('[PROGRESS] Fetching progress data', [
            'user_id' => auth()->id(),
            'has_progress_data' => !is_null($progress),
            'progress_id' => $progress->id ?? null,
        ]);

        if (!$progress) {
            return response()->json([
                'status' => 'not_started',
                'total' => 0,
                'processed' => 0,
                'success' => 0,
                'failed' => 0,
                'errors' => [],
                'warnings' => [],
                'progress_percentage' => 0,
                'elapsed_time' => 0,
            ]);
        }

        $response = [
            'status' => $progress->status,
            'total' => $progress->total,
            'processed' => $progress->processed,
            'success' => $progress->success,
            'failed' => $progress->failed,
            'errors' => $progress->errors ?? [],
            'warnings' => $progress->warnings ?? [],
            'progress_percentage' => $progress->progress_percentage,
            'elapsed_time' => $progress->elapsed_time,
        ];

        \Log::info('[PROGRESS] Returning progress response', [
            'status' => $response['status'],
            'total' => $response['total'],
            'processed' => $response['processed']
        ]);

        return response()->json($response);
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
