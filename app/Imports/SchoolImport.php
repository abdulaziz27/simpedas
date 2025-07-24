<?php

namespace App\Imports;

use App\Models\School;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SchoolImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $results = [
        'success' => 0,
        'failed' => 0,
        'errors' => [],
        'warnings' => [],
    ];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            // Skip baris yang semua kolomnya kosong
            if (collect($row)->filter()->isEmpty()) {
                continue;
            }
            // Skip baris petunjuk/template (bukan data sekolah)
            $action = strtoupper($row['action'] ?? '');
            $validActions = ['CREATE', 'UPDATE', 'DELETE'];
            if (empty($row['npsn']) && !in_array($action, $validActions)) {
                // Baris ini kemungkinan besar baris petunjuk, skip
                continue;
            }
            // CASTING: pastikan npsn dan phone selalu string
            if (isset($row['npsn'])) {
                $row['npsn'] = (string) $row['npsn'];
            }
            if (isset($row['phone'])) {
                $row['phone'] = (string) $row['phone'];
            }
            try {
                if (empty($action)) {
                    $action = 'CREATE';
                }
                $result = false;
                switch ($action) {
                    case 'CREATE':
                        $result = $this->createSchool($row, $index);
                        break;
                    case 'UPDATE':
                        $result = $this->updateSchool($row, $index);
                        break;
                    case 'DELETE':
                        $result = $this->deleteSchool($row, $index);
                        break;
                    default:
                        $this->addError($index, "Invalid action: {$action}");
                        $this->results['failed']++;
                        \Log::error('[IMPORT] Invalid action pada baris ' . ($index + 2) . ': ' . $action);
                        continue 2;
                }

                if ($result === true) {
                    $this->results['success']++;
                } else {
                    $this->results['failed']++;
                }
            } catch (\Exception $e) {
                $this->results['failed']++;
                $this->addError($index, $e->getMessage());
                \Log::error('[IMPORT] Exception pada baris ' . ($index + 2) . ': ' . $e->getMessage());
            }
        }
        // Log hasil akhir error dan warning
        \Log::info('[IMPORT] Selesai. Jumlah error: ' . count($this->results['errors']) . ', error: ' . json_encode($this->results['errors']));
        \Log::info('[IMPORT] Selesai. Jumlah warning: ' . count($this->results['warnings']) . ', warning: ' . json_encode($this->results['warnings']));
    }

    protected function createSchool($row, $index)
    {
        // Validasi field yang diperlukan
        if (!$this->validateRequired($row, $index)) return false;

        // Cek NPSN, restore jika soft deleted, error jika aktif, create jika tidak ada
        $existing = School::withTrashed()->where('npsn', $row['npsn'])->first();
        if ($existing) {
            if ($existing->trashed()) {
                // Restore dan update data
                $existing->restore();
                $existing->update([
                    'name' => $row['name'],
                    'education_level' => $row['education_level'],
                    'status' => $row['status'],
                    'address' => $row['address'],
                    'phone' => $row['phone'] ?? null,
                    'email' => $row['email'] ?? null,
                    'headmaster' => $row['headmaster'] ?? null,
                    'region' => $row['region'] ?? 'Siantar Utara',
                ]);
            } else {
                $this->addError($index, "NPSN sudah terdaftar.", $row);
                return false;
            }
        } else {
            // Buat sekolah baru
            $school = School::create([
                'npsn' => $row['npsn'],
                'name' => $row['name'],
                'education_level' => $row['education_level'],
                'status' => $row['status'],
                'address' => $row['address'],
                'phone' => $row['phone'] ?? null,
                'email' => $row['email'] ?? null,
                'headmaster' => $row['headmaster'] ?? null,
                'region' => $row['region'] ?? 'Siantar Utara',
            ]);
        }

        // Auto user creation if email provided
        if (!empty($row['email'])) {
            try {
                $randomPassword = Str::random(8);
                $user = User::firstOrCreate(
                    ['email' => $row['email']],
                    [
                        'name' => $row['headmaster'] ?? 'Admin Sekolah',
                        'password' => Hash::make($randomPassword),
                    ]
                );

                // Assign role admin_sekolah
                if (!$user->hasRole('admin_sekolah')) {
                    $user->assignRole('admin_sekolah');
                }

                // Update school_id
                $user->school_id = $existing ? $existing->id : $school->id;
                $user->save();

                $this->addWarning($index, "User created with password: {$randomPassword}. Please change it immediately.");
            } catch (\Exception $e) {
                $this->addWarning($index, "Failed to create user: {$e->getMessage()}");
            }
        }
        return true;
    }

    protected function updateSchool($row, $index)
    {
        // Cari sekolah berdasarkan NPSN saja
        $school = null;
        if (!empty($row['npsn'])) {
            $school = School::where('npsn', $row['npsn'])->first();
        }
        if (!$school) {
            $this->addError($index, "CRITICAL ERROR: School not found for update");
            return false;
        }

        // Validasi field yang diperlukan untuk update
        if (empty($row['name']) || empty($row['education_level']) || empty($row['status']) || empty($row['address'])) {
            $this->addError($index, "CRITICAL ERROR: Required fields missing for update");
            return false;
        }

        // Validasi education level
        if (!in_array($row['education_level'], ['TK', 'SD', 'SMP', 'SMA', 'SMK'])) {
            $this->addError($index, "CRITICAL ERROR: Invalid education level");
            return false;
        }

        // Validasi status
        if (!in_array($row['status'], ['Negeri', 'Swasta'])) {
            $this->addError($index, "CRITICAL ERROR: Invalid status");
            return false;
        }

        // Validasi business logic untuk nama sekolah
        $this->validateSchoolName($row, $index);

        // Update sekolah
        $school->update([
            'name' => $row['name'],
            'education_level' => $row['education_level'],
            'status' => $row['status'],
            'address' => $row['address'],
            'phone' => $row['phone'] ?? $school->phone,
            'email' => $row['email'] ?? $school->email,
            'headmaster' => $row['headmaster'] ?? $school->headmaster,
            'region' => $row['region'] ?? $school->region,
        ]);
        return true;
    }

    protected function deleteSchool($row, $index)
    {
        // Cari sekolah berdasarkan NPSN saja
        $school = null;
        if (!empty($row['npsn'])) {
            $school = School::where('npsn', $row['npsn'])->first();
        }
        if (!$school) {
            $this->addError($index, "CRITICAL ERROR: School not found for deletion");
            return false;
        }

        // Detach users
        $school->users()->update(['school_id' => null]);

        // Soft delete
        $school->delete();
        return true;
    }

    protected function validateRequired($row, $index)
    {
        $required = ['npsn', 'name', 'education_level', 'status', 'address'];
        $hasError = false;
        foreach ($required as $field) {
            if (empty($row[$field])) {
                $this->addError($index, "Kolom '{$field}' wajib diisi.", $row);
                $hasError = true;
            }
        }
        // Validasi panjang minimum untuk name
        if (!empty($row['name']) && strlen($row['name']) < 3) {
            $this->addError($index, "Nama sekolah minimal 3 karakter.", $row);
            $hasError = true;
        }
        // Validasi panjang maksimum
        if (!empty($row['npsn']) && strlen($row['npsn']) > 20) {
            $this->addError($index, "NPSN maksimal 20 karakter.", $row);
            $hasError = true;
        }
        if (!empty($row['name']) && strlen($row['name']) > 255) {
            $this->addError($index, "Nama sekolah maksimal 255 karakter.", $row);
            $hasError = true;
        }
        if (!empty($row['phone']) && strlen($row['phone']) > 20) {
            $this->addWarning($index, "Nomor telepon dipotong maksimal 20 karakter.", $row);
            $row['phone'] = substr($row['phone'], 0, 20);
        }
        if (!empty($row['headmaster']) && strlen($row['headmaster']) > 255) {
            $this->addWarning($index, "Nama kepala sekolah dipotong maksimal 255 karakter.", $row);
            $row['headmaster'] = substr($row['headmaster'], 0, 255);
        }
        return !$hasError;
    }

    protected function validateSchoolName($row, $index)
    {
        $name = $row['name'];
        $level = $row['education_level'];
        $status = $row['status'];

        // Validasi nama berdasarkan jenjang pendidikan
        $levelPatterns = [
            'TK' => ['TK', 'Taman Kanak'],
            'SD' => ['SD', 'Sekolah Dasar'],
            'SMP' => ['SMP'],
            'SMA' => ['SMA'],
            'SMK' => ['SMK']
        ];

        $matchFound = false;
        foreach ($levelPatterns[$level] as $pattern) {
            if (stripos($name, $pattern) !== false) {
                $matchFound = true;
                break;
            }
        }

        if (!$matchFound) {
            $this->addWarning($index, "Name-education level mismatch: {$name} does not contain {$level} identifier");
        }

        // Validasi format nama untuk sekolah negeri
        if ($status === 'Negeri') {
            $pattern = "/^{$level}\s+Negeri\s+\d+/i";
            if (!preg_match($pattern, $name)) {
                $this->addWarning($index, "Naming convention for Negeri schools should follow '{$level} Negeri [Number]' format");
            }
        }
    }

    protected function addError($index, $message, $row = null)
    {
        $info = '';
        if ($row) {
            $infoParts = [];
            if (!empty($row['npsn'])) $infoParts[] = 'NPSN: ' . $row['npsn'];
            if (!empty($row['name'])) $infoParts[] = 'Nama: ' . $row['name'];
            if ($infoParts) $info = ' (' . implode(', ', $infoParts) . ')';
        }
        $errorMsg = "Baris " . ($index + 2) . "{$info}: {$message}";
        $this->results['errors'][] = $errorMsg;
        \Log::error('[IMPORT] Error pada baris ' . ($index + 2) . ': ' . $errorMsg);
    }

    protected function addWarning($index, $message)
    {
        $this->results['warnings'][] = "Row " . ($index + 2) . ": {$message}";
    }

    public function getResults()
    {
        return $this->results;
    }

    public function rules(): array
    {
        return [
            '*.npsn' => ['nullable', 'max:20'],
            '*.name' => ['nullable', 'string', 'max:255'],
            '*.education_level' => ['nullable', 'string', 'in:TK,SD,SMP,SMA,SMK'],
            '*.status' => ['nullable', 'string', 'in:Negeri,Swasta'],
            '*.address' => ['nullable', 'string'],
            '*.phone' => ['nullable', 'max:20'],
            '*.email' => ['nullable', 'email'],
            '*.headmaster' => ['nullable', 'string', 'max:255'],
        ];
    }
}
