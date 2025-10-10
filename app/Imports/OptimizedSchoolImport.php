<?php

namespace App\Imports;

use App\Models\School;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class OptimizedSchoolImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $results = [
        'success' => 0,
        'failed' => 0,
        'errors' => [],
        'warnings' => [],
    ];

    public function collection(Collection $rows)
    {
        // Increase limits for large imports
        set_time_limit(600); // 10 minutes
        ini_set('memory_limit', '1024M'); // 1GB

        Log::info('[OPTIMIZED_IMPORT] Starting optimized import', [
            'total_rows' => $rows->count(),
            'memory_limit' => ini_get('memory_limit'),
            'time_limit' => ini_get('max_execution_time')
        ]);

        // FASE 1: VALIDASI SEMUA DATA
        $validatedRows = [];
        $hasErrors = false;

        // Pre-load all existing schools (single query)
        $existingSchools = School::withTrashed()->pluck('npsn', 'npsn')->toArray();
        $activeSchools = School::pluck('npsn', 'npsn')->toArray();

        Log::info('[OPTIMIZED_IMPORT] Pre-loaded ' . count($existingSchools) . ' existing schools');

        foreach ($rows as $index => $row) {
            if (collect($row)->filter()->isEmpty()) {
                continue;
            }

            $action = strtoupper($row['aksi'] ?? 'CREATE');
            $validActions = ['CREATE', 'UPDATE', 'DELETE'];

            if (empty($row['npsn']) && !in_array($action, $validActions)) {
                continue;
            }

            // Cast data types
            if (isset($row['npsn'])) {
                $row['npsn'] = (string) $row['npsn'];
            }
            if (isset($row['telepon'])) {
                $row['telepon'] = (string) $row['telepon'];
            }

            try {
                $isValid = $this->validateRowData($row, $index, $action, $existingSchools, $activeSchools);
                if (!$isValid) {
                    $hasErrors = true;
                } else {
                    $validatedRows[] = ['row' => $row, 'index' => $index, 'action' => $action];
                }
            } catch (\Exception $e) {
                $this->addError($index, $e->getMessage());
                $hasErrors = true;
            }
        }

        if ($hasErrors) {
            $this->results['failed'] = count($this->results['errors']);
            Log::error('[OPTIMIZED_IMPORT] Import cancelled due to errors: ' . count($this->results['errors']));
            return;
        }

        // FASE 2: BULK PROCESSING
        Log::info('[OPTIMIZED_IMPORT] Starting bulk processing for ' . count($validatedRows) . ' records');

        // Group by action for bulk processing
        $createData = [];
        $updateData = [];
        $deleteData = [];

        foreach ($validatedRows as $validatedData) {
            $row = $validatedData['row'];
            $action = $validatedData['action'];

            switch ($action) {
                case 'CREATE':
                    $createData[] = $this->prepareCreateData($row);
                    break;
                case 'UPDATE':
                    $updateData[] = $this->prepareUpdateData($row);
                    break;
                case 'DELETE':
                    $deleteData[] = $row['npsn'];
                    break;
            }
        }

        // Process in database transactions
        DB::transaction(function () use ($createData, $updateData, $deleteData) {
            // Bulk create schools
            if (!empty($createData)) {
                $this->bulkCreateSchools($createData);
            }

            // Bulk update schools
            if (!empty($updateData)) {
                $this->bulkUpdateSchools($updateData);
            }

            // Bulk delete schools
            if (!empty($deleteData)) {
                $this->bulkDeleteSchools($deleteData);
            }
        });

        Log::info('[OPTIMIZED_IMPORT] Import completed successfully');
    }

    protected function validateRowData($row, $index, $action, $existingSchools, $activeSchools)
    {
        switch ($action) {
            case 'CREATE':
                return $this->validateCreateData($row, $index, $activeSchools);
            case 'UPDATE':
                return $this->validateUpdateData($row, $index, $existingSchools);
            case 'DELETE':
                return $this->validateDeleteData($row, $index, $existingSchools);
            default:
                $this->addError($index, "Invalid action: {$action}");
                return false;
        }
    }

    protected function validateCreateData($row, $index, $activeSchools)
    {
        if (!$this->validateRequired($row, $index)) {
            return false;
        }

        if (!empty($row['npsn']) && isset($activeSchools[$row['npsn']])) {
            $this->addError($index, "NPSN already exists: " . $row['npsn']);
            return false;
        }

        return true;
    }

    protected function validateUpdateData($row, $index, $existingSchools)
    {
        if (empty($row['npsn'])) {
            $this->addError($index, "NPSN required for UPDATE");
            return false;
        }

        if (!isset($existingSchools[$row['npsn']])) {
            $this->addError($index, "School not found for update: " . $row['npsn']);
            return false;
        }

        return true;
    }

    protected function validateDeleteData($row, $index, $existingSchools)
    {
        if (empty($row['npsn'])) {
            $this->addError($index, "NPSN required for DELETE");
            return false;
        }

        if (!isset($existingSchools[$row['npsn']])) {
            $this->addError($index, "School not found for deletion: " . $row['npsn']);
            return false;
        }

        return true;
    }

    protected function validateRequired($row, $index)
    {
        $required = ['npsn', 'nama_sekolah', 'jenjang_pendidikan', 'status', 'alamat', 'email'];
        $hasError = false;

        foreach ($required as $field) {
            if (empty($row[$field])) {
                $this->addError($index, "Required field '{$field}' is empty");
                $hasError = true;
            }
        }

        return !$hasError;
    }

    protected function prepareCreateData($row)
    {
        return [
            'npsn' => $row['npsn'],
            'name' => $row['nama_sekolah'],
            'education_level' => $row['jenjang_pendidikan'],
            'status' => $row['status'],
            'address' => $row['alamat'],
            'desa' => $row['desa'] ?? null,
            'kecamatan' => $row['kecamatan'] ?? null,
            'kabupaten_kota' => $row['kabupaten_kota'] ?? null,
            'provinsi' => $row['provinsi'] ?? null,
            'google_maps_link' => $row['google_maps_link'] ?? null,
            'latitude' => $row['latitude'] ?? null,
            'longitude' => $row['longitude'] ?? null,
            'phone' => $row['telepon'] ?? null,
            'email' => $row['email'],
            'headmaster' => $row['kepala_sekolah'] ?? null,
            'password_admin' => $row['password_admin'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    protected function prepareUpdateData($row)
    {
        return [
            'npsn' => $row['npsn'],
            'name' => $row['nama_sekolah'],
            'education_level' => $row['jenjang_pendidikan'],
            'status' => $row['status'],
            'address' => $row['alamat'],
            'desa' => $row['desa'] ?? null,
            'kecamatan' => $row['kecamatan'] ?? null,
            'kabupaten_kota' => $row['kabupaten_kota'] ?? null,
            'provinsi' => $row['provinsi'] ?? null,
            'google_maps_link' => $row['google_maps_link'] ?? null,
            'latitude' => $row['latitude'] ?? null,
            'longitude' => $row['longitude'] ?? null,
            'phone' => $row['telepon'] ?? null,
            'email' => $row['email'],
            'headmaster' => $row['kepala_sekolah'] ?? null,
            'password_admin' => $row['password_admin'] ?? null,
            'updated_at' => now(),
        ];
    }

    protected function bulkCreateSchools($createData)
    {
        Log::info('[OPTIMIZED_IMPORT] Bulk creating ' . count($createData) . ' schools');

        // Remove password_admin from school data
        $schoolData = array_map(function ($item) {
            unset($item['password_admin']);
            return $item;
        }, $createData);

        // Bulk insert schools
        School::insert($schoolData);

        // Get created schools for user creation
        $createdSchools = School::whereIn('npsn', array_column($createData, 'npsn'))->get()->keyBy('npsn');

        // Create admin users for schools with passwords
        $userData = [];
        foreach ($createData as $data) {
            if (!empty($data['password_admin'])) {
                $school = $createdSchools[$data['npsn']];
                $userData[] = [
                    'name' => $data['headmaster'] ?? 'Admin Sekolah',
                    'email' => $data['email'],
                    'password' => Hash::make($data['password_admin']),
                    'school_id' => $school->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($userData)) {
            User::insert($userData);

            // Assign roles (this is still individual, but much faster than creating users individually)
            $users = User::whereIn('email', array_column($userData, 'email'))->get();
            foreach ($users as $user) {
                $user->assignRole('admin_sekolah');
            }
        }

        $this->results['success'] += count($createData);
    }

    protected function bulkUpdateSchools($updateData)
    {
        Log::info('[OPTIMIZED_IMPORT] Bulk updating ' . count($updateData) . ' schools');

        foreach ($updateData as $data) {
            $password = $data['password_admin'];
            unset($data['password_admin']);

            School::where('npsn', $data['npsn'])->update($data);

            // Update admin user if password provided
            if (!empty($password)) {
                $school = School::where('npsn', $data['npsn'])->first();
                if ($school) {
                    User::updateOrCreate(
                        ['email' => $data['email']],
                        [
                            'name' => $data['headmaster'] ?? 'Admin Sekolah',
                            'password' => Hash::make($password),
                            'school_id' => $school->id,
                        ]
                    );
                }
            }
        }

        $this->results['success'] += count($updateData);
    }

    protected function bulkDeleteSchools($deleteData)
    {
        Log::info('[OPTIMIZED_IMPORT] Bulk deleting ' . count($deleteData) . ' schools');

        // Detach users
        School::whereIn('npsn', $deleteData)->get()->each(function ($school) {
            $school->users()->update(['school_id' => null]);
        });

        // Soft delete schools
        School::whereIn('npsn', $deleteData)->delete();

        $this->results['success'] += count($deleteData);
    }

    protected function addError($index, $message)
    {
        $this->results['errors'][] = "Row " . ($index + 2) . ": {$message}";
    }

    public function getResults()
    {
        return $this->results;
    }

    public function rules(): array
    {
        return [
            '*.npsn' => ['nullable', 'max:20'],
            '*.nama_sekolah' => ['nullable', 'string', 'min:3', 'max:255'],
            '*.jenjang_pendidikan' => ['nullable', 'string', 'in:TK,SD,SMP,KB,PKBM'],
            '*.status' => ['nullable', 'string', 'in:Negeri,Swasta'],
            '*.alamat' => ['nullable', 'string'],
            '*.telepon' => ['nullable', 'max:20'],
            '*.email' => ['nullable', 'max:255', function ($attribute, $value, $fail) {
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $fail('Invalid email format: ' . $value);
                }
            }],
            '*.kepala_sekolah' => ['nullable', 'string', 'max:255'],
            '*.password_admin' => ['nullable', 'string', 'min:8'],
        ];
    }
}
