<?php

namespace App\Helpers;

class ReligionHelper
{
    /**
     * Normalize religion input to standard format
     */
    public static function normalizeReligion($religion)
    {
        if (empty($religion)) {
            return null;
        }

        $religion = trim($religion);
        $religionUpper = strtoupper($religion);
        
        // Common variations and normalizations
        $normalizations = [
            'Islam' => ['ISLAM', 'MUSLIM', 'MOSLEM'],
            'Kristen' => ['KRISTEN', 'KRISTIANI', 'CHRISTIAN'],
            'Katolik' => ['KATOLIK', 'CATHOLIC'],
            'Hindu' => ['HINDU', 'HINDUISME'],
            'Buddha' => ['BUDDHA', 'BUDDHIS', 'BUDHA', 'BUDHIS'],
            'Konghucu' => ['KONGHUCU', 'KHONGHUCU', 'CONFUCIUS'],
            'Protestan' => ['PROTESTAN', 'PROTESTANT'],
        ];
        
        foreach ($normalizations as $standard => $variations) {
            if (in_array($religionUpper, $variations)) {
                return $standard;
            }
        }
        
        // Return original if no match found
        return $religion;
    }

    /**
     * Get list of valid religions
     */
    public static function getValidReligions()
    {
        return [
            'Islam',
            'Kristen',
            'Katolik', 
            'Hindu',
            'Buddha',
            'Konghucu',
            'Protestan'
        ];
    }

    /**
     * Check if religion is valid
     */
    public static function isValidReligion($religion)
    {
        $normalized = self::normalizeReligion($religion);
        return in_array($normalized, self::getValidReligions());
    }
}