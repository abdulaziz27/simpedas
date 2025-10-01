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
        foreach ($rows as $index => $row) {
            if (collect($row)->filter()->isEmpty()) continue;
            // CASTING
            if (isset($row['nuptk'])) $row['nuptk'] = (string) $row['nuptk'];
            if (isset($row['nip'])) $row['nip'] = (string) $row['nip'];
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
        $teacher = Teacher::create($row->toArray());

        // Auto user creation if email provided
        if (!empty($row['email'])) {
            try {
                $randomPassword = \Illuminate\Support\Str::random(8);
                $user = \App\Models\User::firstOrCreate(
                    ['email' => $row['email']],
                    [
                        'name' => $row['nama_lengkap'] ?? $teacher->full_name,
                        'password' => \Illuminate\Support\Facades\Hash::make($randomPassword),
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

                \Illuminate\Support\Facades\Password::sendResetLink(['email' => $user->email]);
                $this->addWarning($index, "User guru dibuat. Reset link dikirim ke email: {$user->email}.");
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
        $teacher->update($row->toArray());
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
        $required = ['nuptk', 'nama_lengkap', 'jenis_kelamin', 'status_ke_pegawaian', 'email'];
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
            '*.nama_lengkap' => ['nullable', 'string', 'max:255'],
            '*.jenis_kelamin' => ['nullable', 'string', 'in:Laki-laki,Perempuan'],
            '*.tempat_lahir' => ['nullable', 'string', 'max:100'],
            '*.tanggal_lahir' => ['nullable', 'date'],
            '*.agama' => ['nullable', 'string', 'max:50'],
            '*.alamat' => ['nullable', 'string'],
            '*.telepon' => ['nullable', 'string', 'max:20'],
            '*.tingkat_pendidikan' => ['nullable', 'string', 'max:100'],
            '*.jurusan_pendidikan' => ['nullable', 'string', 'max:100'],
            '*.mata_pelajaran' => ['nullable', 'string'],
            '*.status_ke_pegawaian' => ['nullable', 'string', 'in:PNS,PPPK,GTY,PTY'],
            '*.pangkat' => ['nullable', 'string', 'max:50'],
            '*.jabatan' => ['nullable', 'string', 'max:100'],
            '*.npsn_sekolah' => ['nullable', 'string'],
        ];
    }
}
