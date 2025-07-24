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
        foreach ($rows as $index => $row) {
            if (collect($row)->filter()->isEmpty()) continue;
            // CASTING
            if (isset($row['nisn'])) $row['nisn'] = (string) $row['nisn'];
            if ($user->hasRole('admin_sekolah')) {
                $row['school_id'] = $user->school_id;
            }
            $action = strtoupper($row['action'] ?? 'CREATE');
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
                        $this->addError($index, "Invalid action: {$action}");
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

    protected function createStudent($row, $index)
    {
        if (!$this->validateRequired($row, $index)) return false;
        $existing = Student::where('nisn', $row['nisn'])->first();
        if ($existing) {
            $this->addError($index, "NISN sudah terdaftar.", $row);
            return false;
        }
        Student::create($row->toArray());
        return true;
    }

    protected function updateStudent($row, $index)
    {
        if (empty($row['nisn'])) {
            $this->addError($index, "NISN wajib diisi untuk update.", $row);
            return false;
        }
        $student = Student::where('nisn', $row['nisn'])->first();
        if (!$student) {
            $this->addError($index, "Student not found for update", $row);
            return false;
        }
        $student->update($row->toArray());
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
            $this->addError($index, "Student not found for deletion", $row);
            return false;
        }
        $student->delete();
        return true;
    }

    protected function validateRequired($row, $index)
    {
        $required = ['nisn', 'full_name', 'gender', 'grade_level', 'student_status', 'academic_year', 'school_id'];
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
            if (!empty($row['nisn'])) $infoParts[] = 'NISN: ' . $row['nisn'];
            if (!empty($row['full_name'])) $infoParts[] = 'Nama: ' . $row['full_name'];
            if ($infoParts) $info = ' (' . implode(', ', $infoParts) . ')';
        }
        $this->results['errors'][] = "Baris " . ($index + 2) . "{$info}: {$message}";
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
            '*.nisn' => ['nullable', 'max:20'],
            '*.full_name' => ['nullable', 'string', 'max:255'],
            '*.gender' => ['nullable', 'string', 'in:Laki-laki,Perempuan'],
            '*.birth_place' => ['nullable', 'string', 'max:100'],
            '*.birth_date' => ['nullable', 'date'],
            '*.religion' => ['nullable', 'string', 'max:50'],
            '*.grade_level' => ['nullable', 'string', 'max:20'],
            '*.student_status' => ['nullable', 'string', 'in:Aktif,Tamat'],
            '*.academic_year' => ['nullable', 'string', 'max:20'],
            '*.school_id' => ['nullable', 'integer'],
        ];
    }
}
