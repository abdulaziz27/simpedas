<?php

namespace App\Jobs;

use App\Models\School;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ProcessSchoolImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $validatedRows;
    protected $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(array $validatedRows, int $userId)
    {
        $this->validatedRows = $validatedRows;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('[QUEUE] Starting school import job', [
            'total_records' => count($this->validatedRows),
            'user_id' => $this->userId
        ]);

        $successCount = 0;
        $failedCount = 0;
        $errors = [];

        // Process in batches of 50 for better performance
        $batches = array_chunk($this->validatedRows, 50);

        foreach ($batches as $batchIndex => $batch) {
            Log::info("[QUEUE] Processing batch " . ($batchIndex + 1) . "/" . count($batches));

            foreach ($batch as $validatedData) {
                try {
                    $row = $validatedData['row'];
                    $index = $validatedData['index'];
                    $action = $validatedData['action'];

                    $result = $this->processSchoolData($row, $index, $action);

                    if ($result) {
                        $successCount++;
                    } else {
                        $failedCount++;
                        $errors[] = "Row " . ($index + 2) . ": Failed to process";
                    }
                } catch (\Exception $e) {
                    $failedCount++;
                    $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
                    Log::error('[QUEUE] Error processing row: ' . $e->getMessage());
                }
            }
        }

        Log::info('[QUEUE] School import job completed', [
            'success' => $successCount,
            'failed' => $failedCount,
            'total_errors' => count($errors)
        ]);
    }

    protected function processSchoolData($row, $index, $action)
    {
        switch ($action) {
            case 'CREATE':
                return $this->createSchool($row, $index);
            case 'UPDATE':
                return $this->updateSchool($row, $index);
            case 'DELETE':
                return $this->deleteSchool($row, $index);
            default:
                return false;
        }
    }

    protected function createSchool($row, $index)
    {
        // Check if school exists (restore if soft deleted)
        $existing = School::withTrashed()->where('npsn', $row['npsn'])->first();

        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore();
                $school = $existing;
                $school->update([
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
                return false; // School already exists
            }
        } else {
            // Create new school
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

        // Create admin account if password provided
        if (!empty($row['password_admin'])) {
            try {
                $user = User::create([
                    'name' => $row['kepala_sekolah'] ?? 'Admin Sekolah',
                    'email' => $row['email'],
                    'password' => Hash::make($row['password_admin']),
                    'school_id' => $school->id,
                ]);
                $user->assignRole('admin_sekolah');
            } catch (\Exception $e) {
                Log::error("Failed to create admin account: " . $e->getMessage());
            }
        }

        return true;
    }

    protected function updateSchool($row, $index)
    {
        $school = School::where('npsn', $row['npsn'])->first();
        if (!$school) {
            return false;
        }

        $school->update([
            'name' => $row['nama_sekolah'],
            'education_level' => $row['jenjang_pendidikan'],
            'status' => $row['status'],
            'address' => $row['alamat'],
            'desa' => $row['desa'] ?? $school->desa,
            'kecamatan' => $row['kecamatan'] ?? $school->kecamatan,
            'kabupaten_kota' => $row['kabupaten_kota'] ?? $school->kabupaten_kota,
            'provinsi' => $row['provinsi'] ?? $school->provinsi,
            'google_maps_link' => $row['google_maps_link'] ?? $school->google_maps_link,
            'latitude' => $row['latitude'] ?? $school->latitude,
            'longitude' => $row['longitude'] ?? $school->longitude,
            'phone' => $row['telepon'] ?? $school->phone,
            'email' => $row['email'] ?? $school->email,
            'headmaster' => $row['kepala_sekolah'] ?? $school->headmaster,
        ]);

        // Update admin account if password provided
        if (!empty($row['password_admin'])) {
            try {
                $user = User::updateOrCreate(
                    ['email' => $row['email']],
                    [
                        'name' => $row['kepala_sekolah'] ?? 'Admin Sekolah',
                        'password' => Hash::make($row['password_admin']),
                        'school_id' => $school->id,
                    ]
                );

                if (!$user->hasRole('admin_sekolah')) {
                    $user->assignRole('admin_sekolah');
                }
            } catch (\Exception $e) {
                Log::error("Failed to update admin account: " . $e->getMessage());
            }
        }

        return true;
    }

    protected function deleteSchool($row, $index)
    {
        $school = School::where('npsn', $row['npsn'])->first();
        if (!$school) {
            return false;
        }

        // Detach users
        $school->users()->update(['school_id' => null]);

        // Soft delete
        $school->delete();

        return true;
    }
}
