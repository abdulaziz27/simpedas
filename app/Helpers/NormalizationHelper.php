<?php

namespace App\Helpers;

class NormalizationHelper
{
    /**
     * Normalize gender input to standard format
     */
    public static function normalizeGender($gender)
    {
        if (empty($gender)) {
            return null;
        }

        $gender = strtoupper(trim($gender));
        
        // Common variations for male
        if (in_array($gender, ['L', 'LAKI-LAKI', 'LAKI', 'MALE', 'M', 'PRIA'])) {
            return 'L';
        }
        
        // Common variations for female
        if (in_array($gender, ['P', 'PEREMPUAN', 'WANITA', 'FEMALE', 'F'])) {
            return 'P';
        }
        
        return $gender; // Return as-is if no match
    }

    /**
     * Normalize employment status
     */
    public static function normalizeEmploymentStatus($status)
    {
        if (empty($status)) {
            return null;
        }

        $status = trim($status);
        $statusUpper = strtoupper($status);
        
        // Common variations
        $normalizations = [
            'PNS' => ['PNS', 'PEGAWAI NEGERI SIPIL'],
            'PPPK' => ['PPPK', 'P3K', 'PEGAWAI PEMERINTAH DENGAN PERJANJIAN KERJA'],
            'GTY' => ['GTY', 'GURU TIDAK TETAP YAYASAN'],
            'PTY' => ['PTY', 'PEGAWAI TIDAK TETAP YAYASAN'],
            'Honorer' => ['HONORER', 'HONOR', 'GTT', 'GURU TIDAK TETAP'],
            'Kontrak' => ['KONTRAK', 'PEGAWAI KONTRAK'],
        ];
        
        foreach ($normalizations as $standard => $variations) {
            if (in_array($statusUpper, $variations)) {
                return $standard;
            }
        }
        
        return $status; // Return as-is if no match
    }

    /**
     * Normalize action for import
     */
    public static function normalizeAction($action)
    {
        if (empty($action)) {
            return 'CREATE'; // Default action
        }

        $action = strtoupper(trim($action));
        
        // Common variations
        $normalizations = [
            'CREATE' => ['CREATE', 'TAMBAH', 'ADD', 'INSERT', 'BUAT'],
            'UPDATE' => ['UPDATE', 'EDIT', 'UBAH', 'MODIFY'],
            'DELETE' => ['DELETE', 'HAPUS', 'REMOVE', 'DEL'],
        ];
        
        foreach ($normalizations as $standard => $variations) {
            if (in_array($action, $variations)) {
                return $standard;
            }
        }
        
        return 'CREATE'; // Default to CREATE if no match
    }
}