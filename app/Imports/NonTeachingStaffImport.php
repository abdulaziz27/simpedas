<?php

namespace App\Imports;

use App\Models\NonTeachingStaff;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class NonTeachingStaffImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $results = [
        'success' => 0,
        'failed' => 0,
        'errors' => [],
        'warnings' => [],
    ];

    public function collection(Collection $rows)
    {
        $user = Auth::user();
        \Log::info('NonTeachingStaffImport: Starting collection processing', [
            'total_rows' => $rows->count(),
            'user_id' => $user->id
        ]);

        foreach ($rows as $index => $row) {
            \Log::info('NonTeachingStaffImport: Processing row ' . ($index + 1), [
                'row_data' => $row->toArray(),
                'has_data' => !collect($row)->filter(function ($value) {
                    return !empty(trim($value));
                })->isEmpty()
            ]);

            // Skip baris kosong atau baris yang hanya berisi whitespace
            if (collect($row)->filter(function ($value) {
                return !empty(trim($value));
            })->isEmpty()) {
                \Log::info('NonTeachingStaffImport: Skipping empty row ' . ($index + 1));
                continue;
            }

            // Skip baris yang tidak memiliki data penting (nama_lengkap atau nip_nik)
            if (empty($row['nama_lengkap']) && empty($row['nip_nik'])) {
                \Log::info('NonTeachingStaffImport: Skipping row without essential data ' . ($index + 1));
                continue;
            }
            // CASTING
            if (isset($row['nip_nik'])) $row['nip_nik'] = (string) $row['nip_nik'];
            if (isset($row['nuptk'])) $row['nuptk'] = (string) $row['nuptk'];
            if (isset($row['npsn_sekolah'])) $row['npsn_sekolah'] = (string) $row['npsn_sekolah'];
            if ($user->hasRole('admin_sekolah')) {
                $row['school_id'] = $user->school_id;
            } else {
                // admin dinas: cari school_id dari NPSN_SEKOLAH
                if (!empty($row['npsn_sekolah'])) {
                    $school = \App\Models\School::where('npsn', $row['npsn_sekolah'])->first();
                    if ($school) {
                        $row['school_id'] = $school->id;
                    } else {
                        $this->addError($index, "NPSN sekolah tidak ditemukan di database.", $row);
                        $this->results['failed']++;
                        continue;
                    }
                } else {
                    $this->addError($index, "Kolom NPSN_SEKOLAH wajib diisi untuk admin dinas.", $row);
                    $this->results['failed']++;
                    continue;
                }
            }
            $action = strtoupper($row['aksi'] ?? 'CREATE');
            $result = false;
            try {
                switch ($action) {
                    case 'CREATE':
                        $result = $this->createStaff($row, $index);
                        break;
                    case 'UPDATE':
                        $result = $this->updateStaff($row, $index);
                        break;
                    case 'DELETE':
                        $result = $this->deleteStaff($row, $index);
                        break;
                    default:
                        $this->addError($index, "Aksi tidak valid: {$action}");
                        $this->results['failed']++;
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
            }
        }
    }

    protected function createStaff($row, $index)
    {
        if (!$this->validateRequired($row, $index)) return false;
        $existing = NonTeachingStaff::where('nip_nik', $row['nip_nik'])->first();
        if ($existing) {
            $this->addError($index, "NIP/NIK sudah terdaftar.", $row);
            return false;
        }

        // Mapping field dari header bahasa Indonesia ke field database
        $data = [
            'school_id' => $row['school_id'] ?? null,
            'full_name' => $row['nama_lengkap'] ?? null,
            'nip_nik' => $row['nip_nik'] ?? null,
            'nuptk' => $row['nuptk'] ?? null,
            'birth_place' => $row['tempat_lahir'] ?? null,
            'birth_date' => $row['tanggal_lahir'] ?? null,
            'gender' => $row['jenis_kelamin'] ?? null,
            'religion' => $row['agama'] ?? null,
            'address' => $row['alamat'] ?? null,
            'position' => $row['jabatan'] ?? null,
            'education_level' => $row['tingkat_pendidikan'] ?? null,
            'education_major' => $row['jurusan_pendidikan'] ?? null,
            'employment_status' => $row['status_ke_pegawaian'] ?? null,
            'rank' => $row['pangkat'] ?? null,
            'tmt' => $row['tmt'] ?? null,
            'status' => $row['status'] ?? null,
        ];

        $staff = NonTeachingStaff::create($data);

        // Auto user creation for staff if email provided (optional policy)
        if (!empty($row['email'])) {
            try {
                $randomPassword = \Illuminate\Support\Str::random(8);
                $user = \App\Models\User::firstOrCreate(
                    ['email' => $row['email']],
                    [
                        'name' => $row['nama_lengkap'] ?? $staff->full_name,
                        'password' => \Illuminate\Support\Facades\Hash::make($randomPassword),
                        'school_id' => $staff->school_id,
                    ]
                );
                // Note: no special role assigned by default; adjust if needed
                $user->school_id = $staff->school_id;
                $user->save();
                \Illuminate\Support\Facades\Password::sendResetLink(['email' => $user->email]);
                $this->addWarning($index, "User staff dibuat. Reset link dikirim ke email: {$user->email}.");
            } catch (\Exception $e) {
                $this->addWarning($index, "Gagal membuat user staff: {$e->getMessage()}");
            }
        }
        return true;
    }

    protected function updateStaff($row, $index)
    {
        if (empty($row['nip_nik'])) {
            $this->addError($index, "NIP/NIK wajib diisi untuk update.", $row);
            return false;
        }
        $staff = NonTeachingStaff::where('nip_nik', $row['nip_nik'])->first();
        if (!$staff) {
            $this->addError($index, "Staff tidak ditemukan untuk update", $row);
            return false;
        }

        // Mapping field dari header bahasa Indonesia ke field database
        $data = [
            'full_name' => $row['nama_lengkap'] ?? $staff->full_name,
            'nuptk' => $row['nuptk'] ?? $staff->nuptk,
            'birth_place' => $row['tempat_lahir'] ?? $staff->birth_place,
            'birth_date' => $row['tanggal_lahir'] ?? $staff->birth_date,
            'gender' => $row['jenis_kelamin'] ?? $staff->gender,
            'religion' => $row['agama'] ?? $staff->religion,
            'address' => $row['alamat'] ?? $staff->address,
            'position' => $row['jabatan'] ?? $staff->position,
            'education_level' => $row['tingkat_pendidikan'] ?? $staff->education_level,
            'education_major' => $row['jurusan_pendidikan'] ?? $staff->education_major,
            'employment_status' => $row['status_ke_pegawaian'] ?? $staff->employment_status,
            'rank' => $row['pangkat'] ?? $staff->rank,
            'tmt' => $row['tmt'] ?? $staff->tmt,
            'status' => $row['status'] ?? $staff->status,
        ];

        $staff->update($data);
        return true;
    }

    protected function deleteStaff($row, $index)
    {
        if (empty($row['nip_nik'])) {
            $this->addError($index, "NIP/NIK wajib diisi untuk delete.", $row);
            return false;
        }
        $staff = NonTeachingStaff::where('nip_nik', $row['nip_nik'])->first();
        if (!$staff) {
            $this->addError($index, "Staff tidak ditemukan untuk penghapusan", $row);
            return false;
        }
        $staff->delete();
        return true;
    }

    protected function validateRequired($row, $index)
    {
        $required = ['nip_nik', 'nama_lengkap', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'agama', 'alamat', 'jabatan', 'status_ke_pegawaian', 'status'];
        if (Auth::user()->hasRole('admin_dinas')) {
            $required[] = 'npsn_sekolah';
        }
        $hasError = false;
        foreach ($required as $field) {
            if (empty($row[$field])) {
                $this->addError($index, "Kolom '{$field}' wajib diisi.", $row);
                $hasError = true;
            }
        }
        return !$hasError;
    }

    protected function addError($index, $message, $row = null)
    {
        $info = '';
        if ($row) {
            $infoParts = [];
            if (!empty($row['nip_nik'])) $infoParts[] = 'NIP/NIK: ' . $row['nip_nik'];
            if (!empty($row['nama_lengkap'])) $infoParts[] = 'Nama: ' . $row['nama_lengkap'];
            if ($infoParts) $info = ' (' . implode(', ', $infoParts) . ')';
        }
        $this->results['errors'][] = "Baris " . ($index + 2) . "{$info}: {$message}";
    }

    protected function addWarning($index, $message)
    {
        $this->results['warnings'][] = "Baris " . ($index + 2) . ": {$message}";
    }

    public function getResults()
    {
        return $this->results;
    }

    protected function isValidDate($dateValue)
    {
        if (empty($dateValue)) return true; // Allow empty dates

        // Handle Excel serial number (integer)
        if (is_numeric($dateValue)) {
            try {
                $excelDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateValue);
                if ($excelDate) {
                    return true;
                }
            } catch (\Exception $e) {
                // If Excel conversion fails, continue with other methods
            }
        }

        // Convert to string for other format checks
        $dateString = trim((string) $dateValue);

        // Coba berbagai format tanggal
        $formats = [
            'Y-m-d',           // 1985-07-10
            'd/m/Y',           // 10/07/1985
            'd/m/y',           // 10/07/85
            'd-m-Y',           // 10-07-1985
            'd-m-y',           // 10-07-85
            'Y/m/d',           // 1985/07/10
            'd.m.Y',           // 10.07.1985
            'd.m.y',           // 10.07.85
        ];

        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $dateString);
            if ($date !== false) {
                return true;
            }
        }

        // Jika semua format gagal, coba dengan strtotime sebagai fallback
        $timestamp = strtotime($dateString);
        if ($timestamp !== false) {
            return true;
        }

        return false;
    }

    protected function parseDate($dateString)
    {
        if (empty($dateString)) return null;

        // Trim whitespace
        $dateString = trim($dateString);

        // Handle Excel date conversion - jika Excel mengkonversi tanggal ke format lokal
        // Cek apakah ini adalah format Excel (serial number atau format lokal)
        if (is_numeric($dateString)) {
            // Jika ini adalah serial number Excel (seperti 29584 untuk 1980-12-12)
            $excelDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateString);
            if ($excelDate) {
                return $excelDate->format('Y-m-d');
            }
        }

        // Coba berbagai format tanggal
        $formats = [
            'Y-m-d',           // 1985-07-10
            'd/m/Y',           // 10/07/1985
            'd/m/y',           // 10/07/85
            'd-m-Y',           // 10-07-1985
            'd-m-y',           // 10-07-85
            'Y/m/d',           // 1985/07/10
            'd.m.Y',           // 10.07.1985
            'd.m.y',           // 10.07.85
        ];

        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $dateString);
            if ($date !== false) {
                // Jika tahun 2 digit, asumsikan 19xx untuk tahun < 50, 20xx untuk tahun >= 50
                if (strpos($format, 'y') !== false) {
                    $year = $date->format('Y');
                    if ($year < 1950) {
                        $date->add(new \DateInterval('P100Y')); // Tambah 100 tahun
                    }
                }
                return $date->format('Y-m-d');
            }
        }

        // Jika semua format gagal, coba dengan strtotime sebagai fallback
        $timestamp = strtotime($dateString);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }

        return null;
    }

    public function rules(): array
    {
        return [
            '*.nip_nik' => ['nullable', 'max:20'],
            '*.nuptk' => ['nullable', 'max:20'],
            '*.nama_lengkap' => ['nullable', 'max:255'],
            '*.jenis_kelamin' => ['nullable', 'in:Laki-laki,Perempuan'],
            '*.tempat_lahir' => ['nullable', 'max:100'],
            '*.tanggal_lahir' => ['nullable', function ($attribute, $value, $fail) {
                if (!empty($value) && !$this->isValidDate($value)) {
                    $fail('Format tanggal tidak valid: ' . $value . '. Gunakan format DD/MM/YY atau YYYY-MM-DD.');
                }
            }],
            '*.agama' => ['nullable', 'max:50'],
            '*.alamat' => ['nullable'],
            '*.jabatan' => ['nullable', 'max:100'],
            '*.tingkat_pendidikan' => ['nullable', 'max:100'],
            '*.jurusan_pendidikan' => ['nullable', 'max:100'],
            '*.status_ke_pegawaian' => ['nullable', 'in:PNS,PPPK,GTY,PTY'],
            '*.pangkat' => ['nullable', 'max:50'],
            '*.tmt' => ['nullable', function ($attribute, $value, $fail) {
                if (!empty($value) && !$this->isValidDate($value)) {
                    $fail('Format TMT tidak valid: ' . $value . '. Gunakan format DD/MM/YY atau YYYY-MM-DD.');
                }
            }],
            '*.status' => ['nullable', 'in:Aktif,Tidak Aktif'],
            '*.npsn_sekolah' => ['nullable', 'max:20'],
        ];
    }
}
