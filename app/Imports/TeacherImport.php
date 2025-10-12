<?php

namespace App\Imports;

use App\Models\Teacher;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class TeacherImport implements ToCollection, WithHeadingRow, WithValidation
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
        \Log::info('TeacherImport: Starting collection processing', [
            'total_rows' => $rows->count(),
            'user_id' => $user->id
        ]);

        // Validasi template di baris pertama
        if ($rows->count() > 0) {
            $firstRow = $rows->first();
            $availableColumns = array_keys($firstRow->toArray());
            $expectedColumns = ['aksi', 'npsn_sekolah', 'nama_lengkap', 'nuptk', 'nip', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'agama', 'alamat', 'telepon', 'tingkat_pendidikan', 'jurusan_pendidikan', 'mata_pelajaran', 'status_ke_pegawaian', 'pangkat', 'jabatan', 'tmt', 'status', 'email'];

            $missingColumns = array_diff($expectedColumns, $availableColumns);
            if (!empty($missingColumns)) {
                $this->addError(0, "Template tidak sesuai! Kolom yang hilang: " . implode(', ', $missingColumns) . ". Silakan download template terbaru.", $firstRow);
                $this->results['failed']++;
                return;
            }

            \Log::info('TeacherImport: Template validation passed', [
                'available_columns' => $availableColumns,
                'expected_columns' => $expectedColumns
            ]);
        }

        foreach ($rows as $index => $row) {
            \Log::info('TeacherImport: Processing row ' . ($index + 1), [
                'row_data' => $row->toArray(),
                'has_data' => !collect($row)->filter(function ($value) {
                    return !empty(trim($value));
                })->isEmpty()
            ]);

            // Skip baris kosong atau baris yang hanya berisi whitespace
            if (collect($row)->filter(function ($value) {
                return !empty(trim($value));
            })->isEmpty()) {
                \Log::info('TeacherImport: Skipping empty row ' . ($index + 1));
                continue;
            }

            // Skip baris yang tidak memiliki data penting (nuptk atau nama_lengkap)
            if (empty($row['nuptk']) && empty($row['nama_lengkap'])) {
                \Log::info('TeacherImport: Skipping row without essential data ' . ($index + 1));
                continue;
            }

            // CASTING
            if (isset($row['nuptk'])) $row['nuptk'] = (string) $row['nuptk'];
            if (isset($row['nip'])) $row['nip'] = (string) $row['nip'];
            if (isset($row['npsn_sekolah'])) $row['npsn_sekolah'] = (string) $row['npsn_sekolah'];
            // Validasi kolom yang diperlukan
            $requiredColumns = ['nuptk', 'nama_lengkap', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'agama', 'status_ke_pegawaian'];
            $missingColumns = [];
            foreach ($requiredColumns as $col) {
                if (!isset($row[$col]) || empty($row[$col])) {
                    $missingColumns[] = $col;
                }
            }

            if (!empty($missingColumns)) {
                $this->addError($index, "Kolom yang diperlukan tidak ditemukan atau kosong: " . implode(', ', $missingColumns) . ". Pastikan menggunakan template yang benar.", $row);
                $this->results['failed']++;
                continue;
            }

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
            // Validasi format tanggal
            if (isset($row['tanggal_lahir'])) {
                \Log::info('TeacherImport: Parsing tanggal_lahir', [
                    'original' => $row['tanggal_lahir'],
                    'type' => gettype($row['tanggal_lahir']),
                    'is_numeric' => is_numeric($row['tanggal_lahir']),
                    'row_index' => $index + 1
                ]);

                $tanggalLahir = $this->parseDate($row['tanggal_lahir']);
                if (!$tanggalLahir) {
                    $this->addError($index, "Format tanggal lahir tidak valid: '{$row['tanggal_lahir']}'. Gunakan format YYYY-MM-DD (contoh: 1985-07-10) atau DD/MM/YY (contoh: 10/07/85).", $row);
                    $this->results['failed']++;
                    continue;
                }
                $row['tanggal_lahir'] = $tanggalLahir;

                \Log::info('TeacherImport: Tanggal lahir parsed successfully', [
                    'original' => $row['tanggal_lahir'],
                    'parsed' => $tanggalLahir
                ]);
            }

            if (isset($row['tmt'])) {
                \Log::info('TeacherImport: Parsing TMT', [
                    'original' => $row['tmt'],
                    'type' => gettype($row['tmt']),
                    'is_numeric' => is_numeric($row['tmt']),
                    'row_index' => $index + 1
                ]);

                $tmt = $this->parseDate($row['tmt']);
                if (!$tmt) {
                    $this->addError($index, "Format TMT tidak valid: '{$row['tmt']}'. Gunakan format YYYY-MM-DD (contoh: 2010-01-01) atau DD/MM/YY (contoh: 01/01/10).", $row);
                    $this->results['failed']++;
                    continue;
                }
                $row['tmt'] = $tmt;

                \Log::info('TeacherImport: TMT parsed successfully', [
                    'original' => $row['tmt'],
                    'parsed' => $tmt
                ]);
            }

            $action = strtoupper($row['aksi'] ?? 'CREATE');
            $result = false;
            try {
                switch ($action) {
                    case 'CREATE':
                        $result = $this->createTeacher($row, $index);
                        break;
                    case 'UPDATE':
                        $result = $this->updateTeacher($row, $index);
                        break;
                    case 'DELETE':
                        $result = $this->deleteTeacher($row, $index);
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

    protected function createTeacher($row, $index)
    {
        if (!$this->validateRequired($row, $index)) return false;
        $existing = Teacher::where('nuptk', $row['nuptk'])->first();
        if ($existing) {
            $this->addError($index, "NUPTK sudah terdaftar.", $row);
            return false;
        }
        // Mapping field dari header bahasa Indonesia ke field database
        $data = [
            'school_id' => $row['school_id'] ?? null,
            'full_name' => $row['nama_lengkap'] ?? null,
            'nuptk' => $row['nuptk'] ?? null,
            'nip' => $row['nip'] ?? null,
            'birth_place' => $row['tempat_lahir'] ?? null,
            'birth_date' => $row['tanggal_lahir'] ?? null,
            'gender' => $row['jenis_kelamin'] ?? null,
            'religion' => $row['agama'] ?? null,
            'address' => $row['alamat'] ?? null,
            'phone' => $row['telepon'] ?? null,
            'education_level' => $row['tingkat_pendidikan'] ?? null,
            'education_major' => $row['jurusan_pendidikan'] ?? null,
            'subjects' => $row['mata_pelajaran'] ?? null,
            'employment_status' => $row['status_ke_pegawaian'] ?? null,
            'rank' => $row['pangkat'] ?? null,
            'position' => $row['jabatan'] ?? null,
            'tmt' => $row['tmt'] ?? null,
            'status' => $row['status'] ?? 'Aktif',
            'academic_year' => $row['tahun_akademik'] ?? null,
        ];

        $teacher = Teacher::create($data);

        // Create user account if password provided (manual password)
        if (!empty($row['email']) && !empty($row['password_guru'])) {
            try {
                $user = \App\Models\User::firstOrCreate(
                    ['email' => $row['email']],
                    [
                        'name' => $row['nama_lengkap'] ?? $teacher->full_name,
                        'password' => \Illuminate\Support\Facades\Hash::make($row['password_guru']),
                        'school_id' => $teacher->school_id,
                        'teacher_id' => $teacher->id,
                    ]
                );

                if (!$user->hasRole('guru')) {
                    $user->assignRole('guru');
                }

                // Ensure linkage
                $user->school_id = $teacher->school_id;
                $user->teacher_id = $teacher->id;
                $user->save();

                $this->addWarning($index, "User guru berhasil dibuat dengan password manual.");
            } catch (\Exception $e) {
                $this->addWarning($index, "Gagal membuat user guru: {$e->getMessage()}");
            }
        }
        return true;
    }

    protected function updateTeacher($row, $index)
    {
        if (empty($row['nuptk'])) {
            $this->addError($index, "NUPTK wajib diisi untuk update.", $row);
            return false;
        }
        $teacher = Teacher::where('nuptk', $row['nuptk'])->first();
        if (!$teacher) {
            $this->addError($index, "Guru tidak ditemukan untuk update", $row);
            return false;
        }
        // Mapping field dari header bahasa Indonesia ke field database
        $data = [
            'full_name' => $row['nama_lengkap'] ?? $teacher->full_name,
            'nip' => $row['nip'] ?? $teacher->nip,
            'birth_place' => $row['tempat_lahir'] ?? $teacher->birth_place,
            'birth_date' => $row['tanggal_lahir'] ?? $teacher->birth_date,
            'gender' => $row['jenis_kelamin'] ?? $teacher->gender,
            'religion' => $row['agama'] ?? $teacher->religion,
            'address' => $row['alamat'] ?? $teacher->address,
            'phone' => $row['telepon'] ?? $teacher->phone,
            'education_level' => $row['tingkat_pendidikan'] ?? $teacher->education_level,
            'education_major' => $row['jurusan_pendidikan'] ?? $teacher->education_major,
            'subjects' => $row['mata_pelajaran'] ?? $teacher->subjects,
            'employment_status' => $row['status_ke_pegawaian'] ?? $teacher->employment_status,
            'rank' => $row['pangkat'] ?? $teacher->rank,
            'position' => $row['jabatan'] ?? $teacher->position,
            'tmt' => $row['tmt'] ?? $teacher->tmt,
            'status' => $row['status'] ?? $teacher->status,
            'academic_year' => $row['tahun_akademik'] ?? $teacher->academic_year,
        ];

        $teacher->update($data);
        return true;
    }

    protected function deleteTeacher($row, $index)
    {
        if (empty($row['nuptk'])) {
            $this->addError($index, "NUPTK wajib diisi untuk delete.", $row);
            return false;
        }
        $teacher = Teacher::where('nuptk', $row['nuptk'])->first();
        if (!$teacher) {
            $this->addError($index, "Guru tidak ditemukan untuk penghapusan", $row);
            return false;
        }
        $teacher->delete();
        return true;
    }

    protected function validateRequired($row, $index)
    {
        $required = ['nuptk', 'nama_lengkap', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'agama', 'status_ke_pegawaian'];
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

    protected function addError($index, $message, $row = null)
    {
        $info = '';
        if ($row) {
            $infoParts = [];
            if (!empty($row['nuptk'])) $infoParts[] = 'NUPTK: ' . $row['nuptk'];
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

    public function rules(): array
    {
        return [
            '*.nuptk' => ['nullable', 'max:20'],
            '*.nip' => ['nullable', 'max:20'],
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
            '*.telepon' => ['nullable', 'max:20'],
            '*.tingkat_pendidikan' => ['nullable', 'max:100'],
            '*.jurusan_pendidikan' => ['nullable', 'max:100'],
            '*.mata_pelajaran' => ['nullable'],
            '*.status_ke_pegawaian' => ['nullable', 'in:PNS,PPPK,GTY,PTY'],
            '*.pangkat' => ['nullable', 'max:50'],
            '*.jabatan' => ['nullable', 'max:100'],
            '*.tmt' => ['nullable', function ($attribute, $value, $fail) {
                if (!empty($value) && !$this->isValidDate($value)) {
                    $fail('Format TMT tidak valid: ' . $value . '. Gunakan format DD/MM/YY atau YYYY-MM-DD.');
                }
            }],
            '*.status' => ['nullable', 'in:Aktif,Tidak Aktif'],
            '*.email' => ['nullable', 'max:255', function ($attribute, $value, $fail) {
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $fail('Format email tidak valid: ' . $value);
                }
            }],
            '*.npsn_sekolah' => ['nullable', 'max:20'],
        ];
    }
}
