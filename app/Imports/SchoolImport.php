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
            $action = strtoupper($row['aksi'] ?? '');
            $validActions = ['CREATE', 'UPDATE', 'DELETE'];
            if (empty($row['npsn']) && !in_array($action, $validActions)) {
                // Baris ini kemungkinan besar baris petunjuk, skip
                continue;
            }
            // CASTING: pastikan npsn dan telepon selalu string
            if (isset($row['npsn'])) {
                $row['npsn'] = (string) $row['npsn'];
            }
            if (isset($row['telepon'])) {
                $row['telepon'] = (string) $row['telepon'];
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
                        $this->addError($index, "Aksi tidak valid: {$action}");
                        $this->results['failed']++;
                        \Log::error('[IMPORT] Aksi tidak valid pada baris ' . ($index + 2) . ': ' . $action);
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
                    'email' => $row['email'] ?? null,
                    'headmaster' => $row['kepala_sekolah'] ?? null,
                ]);
            } else {
                $this->addError($index, "NPSN sudah terdaftar.", $row);
                return false;
            }
        } else {
            // Buat sekolah baru
            $school = School::create([
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
                'email' => $row['email'] ?? null,
                'headmaster' => $row['kepala_sekolah'] ?? null,
            ]);
        }

        // Auto user creation if email provided (and send reset link)
        if (!empty($row['email'])) {
            try {
                $randomPassword = Str::random(8);
                $user = User::firstOrCreate(
                    ['email' => $row['email']],
                    [
                        'name' => $row['kepala_sekolah'] ?? 'Admin Sekolah',
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

                // Kirim reset link password ke email
                \Illuminate\Support\Facades\Password::sendResetLink(['email' => $user->email]);

                $this->addWarning($index, "User admin sekolah dibuat. Reset link dikirim ke email: {$user->email}.");
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
            $this->addError($index, "ERROR KRITIS: Sekolah tidak ditemukan untuk update");
            return false;
        }

        // Validasi field yang diperlukan untuk update
        if (empty($row['nama_sekolah']) || empty($row['jenjang_pendidikan']) || empty($row['status']) || empty($row['alamat'])) {
            $this->addError($index, "ERROR KRITIS: Field wajib tidak lengkap untuk update");
            return false;
        }

        // Validasi education level
        if (!in_array($row['jenjang_pendidikan'], ['TK', 'SD', 'SMP', 'KB', 'PKBM'])) {
            $this->addError($index, "ERROR KRITIS: Jenjang pendidikan tidak valid");
            return false;
        }

        // Validasi status
        if (!in_array($row['status'], ['Negeri', 'Swasta'])) {
            $this->addError($index, "ERROR KRITIS: Status tidak valid");
            return false;
        }

        // Validasi business logic untuk nama sekolah
        $this->validateSchoolName($row, $index);

        // Update sekolah
        $school->update([
            'name' => $row['nama_sekolah'],
            'education_level' => $row['jenjang_pendidikan'],
            'status' => $row['status'],
            'address' => $row['alamat'],
            'desa' => $row['desa'] ?? $school->desa,
            'kecamatan' => $row['kecamatan'] ?? $school->kecamatan,
            'kabupaten_kota' => $row['kabupaten_kota'] ?? $school->kabupaten_kota,
            'provinsi' => $row['provinsi'] ?? $school->provinsi,
            'latitude' => $row['latitude'] ?? $school->latitude,
            'longitude' => $row['longitude'] ?? $school->longitude,
            'phone' => $row['telepon'] ?? $school->phone,
            'email' => $row['email'] ?? $school->email,
            'headmaster' => $row['kepala_sekolah'] ?? $school->headmaster,
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
            $this->addError($index, "ERROR KRITIS: Sekolah tidak ditemukan untuk penghapusan");
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
        $required = ['npsn', 'nama_sekolah', 'jenjang_pendidikan', 'status', 'alamat', 'email'];
        $hasError = false;
        foreach ($required as $field) {
            if (empty($row[$field])) {
                $this->addError($index, "Kolom '{$field}' wajib diisi.", $row);
                $hasError = true;
            }
        }
        // Validasi panjang minimum untuk name
        if (!empty($row['nama_sekolah']) && strlen($row['nama_sekolah']) < 3) {
            $this->addError($index, "Nama sekolah minimal 3 karakter.", $row);
            $hasError = true;
        }
        // Validasi panjang maksimum
        if (!empty($row['npsn']) && strlen($row['npsn']) > 20) {
            $this->addError($index, "NPSN maksimal 20 karakter.", $row);
            $hasError = true;
        }
        if (!empty($row['nama_sekolah']) && strlen($row['nama_sekolah']) > 255) {
            $this->addError($index, "Nama sekolah maksimal 255 karakter.", $row);
            $hasError = true;
        }
        if (!empty($row['telepon']) && strlen($row['telepon']) > 20) {
            $this->addWarning($index, "Nomor telepon dipotong maksimal 20 karakter.", $row);
            $row['telepon'] = substr($row['telepon'], 0, 20);
        }
        if (!empty($row['kepala_sekolah']) && strlen($row['kepala_sekolah']) > 255) {
            $this->addWarning($index, "Nama kepala sekolah dipotong maksimal 255 karakter.", $row);
            $row['kepala_sekolah'] = substr($row['kepala_sekolah'], 0, 255);
        }
        return !$hasError;
    }

    protected function validateSchoolName($row, $index)
    {
        $name = $row['nama_sekolah'];
        $level = $row['jenjang_pendidikan'];
        $status = $row['status'];

        // Validasi nama berdasarkan jenjang pendidikan
        $levelPatterns = [
            'TK' => ['TK', 'Taman Kanak'],
            'SD' => ['SD', 'Sekolah Dasar'],
            'SMP' => ['SMP'],
            'KB' => ['KB', 'Kelompok Bermain'],
            'PKBM' => ['PKBM', 'Non Formal', 'Nonformal', 'SKB']
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
            if (!empty($row['nama_sekolah'])) $infoParts[] = 'Nama: ' . $row['nama_sekolah'];
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
            '*.nama_sekolah' => ['nullable', 'string', 'max:255'],
            '*.jenjang_pendidikan' => ['nullable', 'string', 'in:TK,SD,SMP,KB,PKBM'],
            '*.status' => ['nullable', 'string', 'in:Negeri,Swasta'],
            '*.alamat' => ['nullable', 'string'],
            '*.telepon' => ['nullable', 'max:20'],
            '*.email' => ['nullable', 'max:255', function ($attribute, $value, $fail) {
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $fail('Format email tidak valid: ' . $value);
                }
            }],
            '*.kepala_sekolah' => ['nullable', 'string', 'max:255'],
        ];
    }
}
