<?php

namespace App\Imports;

use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class StudentImport implements ToCollection, WithHeadingRow, WithValidation
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
        \Log::info('[STUDENT_IMPORT] Mulai import', [
            'user_id' => optional($user)->id,
            'user_email' => optional($user)->email,
            'rows_count' => $rows->count(),
        ]);

        foreach ($rows as $index => $row) {
            \Log::info('[STUDENT_IMPORT] Processing row ' . ($index + 2), [
                'row_data' => $row->toArray(),
                'row_keys' => array_keys($row->toArray()),
                'is_empty' => $this->isEmptyRow($row),
            ]);

            if ($this->isEmptyRow($row)) {
                \Log::info('[STUDENT_IMPORT] Skipping empty row ' . ($index + 2));
                continue;
            }
            // CASTING - Convert all data to proper types
            $row = $this->castRowData($row);
            if ($user->hasRole('admin_sekolah')) {
                $row['sekolah_id'] = $user->school_id;
            } else {
                // admin dinas: cari sekolah_id dari NPSN_SEKOLAH
                if (!empty($row['npsn_sekolah'])) {
                    $school = \App\Models\School::where('npsn', $row['npsn_sekolah'])->first();
                    if ($school) {
                        $row['sekolah_id'] = $school->id;
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
            \Log::info('[STUDENT_IMPORT] Row ' . ($index + 2) . ' action: ' . $action);

            $result = false;
            try {
                switch ($action) {
                    case 'CREATE':
                        $result = $this->createStudent($row, $index);
                        break;
                    case 'UPDATE':
                        $result = $this->updateStudent($row, $index);
                        break;
                    case 'DELETE':
                        $result = $this->deleteStudent($row, $index);
                        break;
                    default:
                        \Log::error('[STUDENT_IMPORT] Row ' . ($index + 2) . ' invalid action: ' . $action);
                        $this->addError($index, "Aksi tidak valid: {$action}");
                        $this->results['failed']++;
                        continue 2;
                }
                if ($result === true) {
                    \Log::info('[STUDENT_IMPORT] Row ' . ($index + 2) . ' ' . $action . ' success');
                    $this->results['success']++;
                } else {
                    \Log::warning('[STUDENT_IMPORT] Row ' . ($index + 2) . ' ' . $action . ' failed');
                    $this->results['failed']++;
                }
            } catch (\Exception $e) {
                $this->results['failed']++;
                $this->addError($index, $e->getMessage());
                \Log::error('[STUDENT_IMPORT] Exception baris ' . ($index + 2) . ': ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                    'row_hint' => [
                        'nisn' => $row['nisn'] ?? null,
                        'nama_lengkap' => $row['nama_lengkap'] ?? null,
                        'school_id' => $row['school_id'] ?? null,
                    ],
                ]);
            }
        }
        \Log::info('[STUDENT_IMPORT] Selesai', [
            'success' => $this->results['success'],
            'failed' => $this->results['failed'],
            'errors_sample' => array_slice($this->results['errors'], 0, 5),
            'warnings_sample' => array_slice($this->results['warnings'], 0, 5),
        ]);
    }

    protected function createStudent($row, $index)
    {
        if (!$this->validateRequired($row, $index)) return false;
        $existing = Student::where('nisn', $row['nisn'])->first();
        if ($existing) {
            $this->addError($index, "NISN sudah terdaftar.", $row);
            return false;
        }

        // Map header -> DB fields
        $data = [
            'sekolah_id' => $row['sekolah_id'] ?? null,
            'nisn' => $row['nisn'] ?? null,
            'nipd' => $row['nipd'] ?? null,
            'nama_lengkap' => $row['nama_lengkap'] ?? null,
            'jenis_kelamin' => $row['jenis_kelamin'] ?? null,
            'tempat_lahir' => $row['tempat_lahir'] ?? null,
            'tanggal_lahir' => $row['tanggal_lahir'] ?? null,
            'agama' => $row['agama'] ?? null,
            'rombel' => $row['rombel'] ?? null,
            'status_siswa' => $row['status_siswa'] ?? 'aktif',
            'alamat' => $row['alamat'] ?? null,
            'kelurahan' => $row['kelurahan'] ?? null,
            'kecamatan' => $row['kecamatan'] ?? null,
            'kode_pos' => $row['kode_pos'] ?? null,
            'nama_ayah' => $row['nama_ayah'] ?? null,
            'pekerjaan_ayah' => $row['pekerjaan_ayah'] ?? null,
            'nama_ibu' => $row['nama_ibu'] ?? null,
            'pekerjaan_ibu' => $row['pekerjaan_ibu'] ?? null,
            'anak_ke' => $row['anak_ke'] ?? null,
            'jumlah_saudara' => $row['jumlah_saudara'] ?? null,
            'no_hp' => $row['no_hp'] ?? null,
            'kip' => $this->convertKipToBoolean($row['kip'] ?? null),
            'transportasi' => $row['transportasi'] ?? null,
            'jarak_rumah_sekolah' => $row['jarak_rumah_sekolah'] ?? null,
            'tinggi_badan' => $row['tinggi_badan'] ?? null,
            'berat_badan' => $row['berat_badan'] ?? null,
        ];

        try {
            Student::create($data);
            \Log::info('[STUDENT_IMPORT] Student created successfully', [
                'nisn' => $data['nisn'],
                'nama_lengkap' => $data['nama_lengkap'],
                'sekolah_id' => $data['sekolah_id'],
            ]);
            return true;
        } catch (\Exception $e) {
            \Log::error('[STUDENT_IMPORT] Failed to create student', [
                'nisn' => $data['nisn'],
                'nama_lengkap' => $data['nama_lengkap'],
                'error' => $e->getMessage(),
            ]);
            $this->addError($index, "Gagal membuat siswa: " . $e->getMessage(), $row);
            return false;
        }
    }

    protected function updateStudent($row, $index)
    {
        if (empty($row['nisn'])) {
            $this->addError($index, "NISN wajib diisi untuk update.", $row);
            return false;
        }
        $student = Student::where('nisn', $row['nisn'])->first();
        if (!$student) {
            $this->addError($index, "Siswa tidak ditemukan untuk update", $row);
            return false;
        }

        $data = [
            'nama_lengkap' => $row['nama_lengkap'] ?? $student->nama_lengkap,
            'nipd' => $row['nipd'] ?? $student->nipd,
            'jenis_kelamin' => $row['jenis_kelamin'] ?? $student->jenis_kelamin,
            'tempat_lahir' => $row['tempat_lahir'] ?? $student->tempat_lahir,
            'tanggal_lahir' => $row['tanggal_lahir'] ?? $student->tanggal_lahir,
            'agama' => $row['agama'] ?? $student->agama,
            'rombel' => $row['rombel'] ?? $student->rombel,
            'status_siswa' => $row['status_siswa'] ?? $student->status_siswa,
            'alamat' => $row['alamat'] ?? $student->alamat,
            'kelurahan' => $row['kelurahan'] ?? $student->kelurahan,
            'kecamatan' => $row['kecamatan'] ?? $student->kecamatan,
            'kode_pos' => $row['kode_pos'] ?? $student->kode_pos,
            'nama_ayah' => $row['nama_ayah'] ?? $student->nama_ayah,
            'pekerjaan_ayah' => $row['pekerjaan_ayah'] ?? $student->pekerjaan_ayah,
            'nama_ibu' => $row['nama_ibu'] ?? $student->nama_ibu,
            'pekerjaan_ibu' => $row['pekerjaan_ibu'] ?? $student->pekerjaan_ibu,
            'anak_ke' => $row['anak_ke'] ?? $student->anak_ke,
            'jumlah_saudara' => $row['jumlah_saudara'] ?? $student->jumlah_saudara,
            'no_hp' => $row['no_hp'] ?? $student->no_hp,
            'kip' => isset($row['kip']) ? $this->convertKipToBoolean($row['kip']) : $student->kip,
            'transportasi' => $row['transportasi'] ?? $student->transportasi,
            'jarak_rumah_sekolah' => $row['jarak_rumah_sekolah'] ?? $student->jarak_rumah_sekolah,
            'tinggi_badan' => $row['tinggi_badan'] ?? $student->tinggi_badan,
            'berat_badan' => $row['berat_badan'] ?? $student->berat_badan,
        ];

        $student->update($data);
        return true;
    }

    protected function deleteStudent($row, $index)
    {
        if (empty($row['nisn'])) {
            $this->addError($index, "NISN wajib diisi untuk delete.", $row);
            return false;
        }
        $student = Student::where('nisn', $row['nisn'])->first();
        if (!$student) {
            $this->addError($index, "Siswa tidak ditemukan untuk penghapusan", $row);
            return false;
        }
        $student->delete();
        return true;
    }

    protected function validateRequired($row, $index)
    {
        // Skip validation if row is actually empty
        if ($this->isEmptyRow($row)) {
            return false;
        }

        $required = ['aksi', 'nisn', 'nama_lengkap', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'agama', 'rombel'];
        if (Auth::user()->hasRole('admin_dinas')) {
            $required[] = 'npsn_sekolah';
        }

        $hasError = false;
        foreach ($required as $field) {
            $value = $row[$field] ?? null;
            if ($value === null || $value === '' || trim($value) === '') {
                $this->addError($index, "Kolom '{$field}' wajib diisi.", $row);
                $hasError = true;
            }
        }

        // Additional validations only if values are present
        if (!empty($row['jenis_kelamin']) && !in_array($row['jenis_kelamin'], ['L', 'P'])) {
            $this->addError($index, "Jenis kelamin harus L atau P.", $row);
            $hasError = true;
        }

        if (!empty($row['agama']) && !in_array($row['agama'], ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'])) {
            $this->addError($index, "Agama tidak valid. Pilih: Islam, Kristen, Katolik, Hindu, Buddha, Konghucu.", $row);
            $hasError = true;
        }

        if (!empty($row['aksi']) && !in_array(strtoupper($row['aksi']), ['CREATE', 'UPDATE', 'DELETE'])) {
            $this->addError($index, "Aksi harus CREATE, UPDATE, atau DELETE.", $row);
            $hasError = true;
        }

        return !$hasError;
    }

    protected function addError($index, $message, $row = null)
    {
        $info = '';
        if ($row) {
            $infoParts = [];
            if (!empty($row['nisn'])) $infoParts[] = 'NISN: ' . $row['nisn'];
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

    protected function isEmptyRow($row)
    {
        // Define the actual data columns we care about (exclude instruction columns)
        $dataColumns = [
            'aksi',
            'npsn_sekolah',
            'nisn',
            'nipd',
            'nama_lengkap',
            'jenis_kelamin',
            'tempat_lahir',
            'tanggal_lahir',
            'agama',
            'rombel',
            'status_siswa',
            'alamat',
            'kelurahan',
            'kecamatan',
            'kode_pos',
            'nama_ayah',
            'pekerjaan_ayah',
            'nama_ibu',
            'pekerjaan_ibu',
            'anak_ke',
            'jumlah_saudara',
            'no_hp',
            'kip',
            'transportasi',
            'jarak_rumah_sekolah',
            'tinggi_badan',
            'berat_badan'
        ];

        // Check if row has any meaningful data in actual data columns only
        foreach ($dataColumns as $column) {
            $value = $row[$column] ?? null;
            // Skip null, empty strings, whitespace-only strings, and other "empty" values
            if (
                $value !== null &&
                $value !== '' &&
                trim($value) !== '' &&
                $value !== 0 &&
                $value !== '0' &&
                $value !== false &&
                $value !== 'false'
            ) {
                return false;
            }
        }
        return true;
    }

    protected function castRowData($row)
    {
        // Convert numeric fields to strings
        if (isset($row['nisn'])) $row['nisn'] = (string) $row['nisn'];
        if (isset($row['nipd'])) $row['nipd'] = (string) $row['nipd'];
        if (isset($row['npsn_sekolah'])) $row['npsn_sekolah'] = (string) $row['npsn_sekolah'];
        if (isset($row['no_hp'])) $row['no_hp'] = (string) $row['no_hp'];
        if (isset($row['kode_pos'])) $row['kode_pos'] = (string) $row['kode_pos'];

        // Convert numeric fields to integers
        if (isset($row['anak_ke']) && $row['anak_ke'] !== null) $row['anak_ke'] = (int) $row['anak_ke'];
        if (isset($row['jumlah_saudara']) && $row['jumlah_saudara'] !== null) $row['jumlah_saudara'] = (int) $row['jumlah_saudara'];
        if (isset($row['tinggi_badan']) && $row['tinggi_badan'] !== null) $row['tinggi_badan'] = (int) $row['tinggi_badan'];
        if (isset($row['berat_badan']) && $row['berat_badan'] !== null) $row['berat_badan'] = (int) $row['berat_badan'];

        // Convert decimal fields
        if (isset($row['jarak_rumah_sekolah']) && $row['jarak_rumah_sekolah'] !== null) $row['jarak_rumah_sekolah'] = (float) $row['jarak_rumah_sekolah'];

        // Convert boolean fields
        if (isset($row['kip'])) $row['kip'] = $this->convertKipToBoolean($row['kip']);

        // Trim all string fields
        foreach ($row as $key => $value) {
            if (is_string($value)) {
                $row[$key] = trim($value);
            }
        }

        return $row;
    }

    protected function convertKipToBoolean($value)
    {
        if (empty($value)) {
            return null;
        }

        $value = strtolower(trim($value));
        if ($value === 'ya' || $value === 'yes' || $value === '1' || $value === 'true') {
            return true;
        }
        if ($value === 'tidak' || $value === 'no' || $value === '0' || $value === 'false') {
            return false;
        }

        return null;
    }

    public function rules(): array
    {
        // Temporarily disable validation to debug import issues
        return [];
    }
}
