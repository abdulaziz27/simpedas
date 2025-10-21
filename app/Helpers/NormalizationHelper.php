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

    /**
     * Normalize status (for students and staff)
     */
    public static function normalizeStatus($status)
    {
        if (empty($status)) {
            return null;
        }

        $status = trim($status);
        $statusUpper = strtoupper($status);
        
        // Common variations for student status
        $normalizations = [
            'Aktif' => ['AKTIF', 'ACTIVE', 'A'],
            'Tidak Aktif' => ['TIDAK AKTIF', 'INACTIVE', 'NON AKTIF', 'TA'],
            'Lulus' => ['LULUS', 'GRADUATED', 'L'],
            'Pindah' => ['PINDAH', 'MOVED', 'TRANSFER', 'P'],
            'Keluar' => ['KELUAR', 'DROPOUT', 'DO', 'K'],
            'Meninggal' => ['MENINGGAL', 'DECEASED', 'WAFAT', 'M'],
        ];
        
        foreach ($normalizations as $standard => $variations) {
            if (in_array($statusUpper, $variations)) {
                return $standard;
            }
        }
        
        return $status; // Return as-is if no match
    }

    /**
     * Normalize rombel (class) format
     */
    public static function normalizeRombel($rombel)
    {
        if (empty($rombel)) {
            return null;
        }

        $rombel = trim($rombel);
        
        // Convert common formats to standard format
        // Examples: "1A" -> "1A", "kelas 1a" -> "1A", "I-A" -> "1A"
        $rombel = strtoupper($rombel);
        $rombel = str_replace(['KELAS ', 'KELAS', 'ROMBEL ', 'ROMBEL'], '', $rombel);
        $rombel = str_replace(['-', '_', ' '], '', $rombel);
        
        // Convert Roman numerals to Arabic
        $romanToArabic = [
            'I' => '1', 'II' => '2', 'III' => '3', 'IV' => '4', 'V' => '5', 'VI' => '6'
        ];
        
        foreach ($romanToArabic as $roman => $arabic) {
            if (strpos($rombel, $roman) === 0) {
                $rombel = str_replace($roman, $arabic, $rombel);
                break;
            }
        }
        
        return $rombel;
    }

    /**
     * Normalize Yes/No values
     */
    public static function normalizeYesNo($value)
    {
        if (empty($value)) {
            return null;
        }

        $value = strtoupper(trim($value));
        
        // Yes variations
        if (in_array($value, ['YES', 'Y', 'YA', 'IYA', 'IYA', '1', 'TRUE', 'BENAR', 'ADA'])) {
            return 'Ya';
        }
        
        // No variations  
        if (in_array($value, ['NO', 'N', 'TIDAK', 'TDK', '0', 'FALSE', 'SALAH', 'TIDAK ADA'])) {
            return 'Tidak';
        }
        
        return $value; // Return as-is if no match
    }
}