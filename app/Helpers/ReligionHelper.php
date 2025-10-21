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
            'Islam' => ['ISLAM', 'MUSLIM', 'MOSLEM', 'MOESLIM'],
            'Kristen' => ['KRISTEN', 'KRISTIANI', 'CHRISTIAN', 'KRISTEN PROTESTAN', 'NASRANI'],
            'Katolik' => ['KATOLIK', 'KATHOLIK', 'CATHOLIC', 'KRISTEN KATOLIK', 'KATOLIK ROMA'],
            'Hindu' => ['HINDU', 'HINDUISME', 'HINDHU'],
            'Buddha' => ['BUDDHA', 'BUDDHIS', 'BUDHA', 'BUDHIS', 'BUDDHISME', 'BUDHA DHARMA'],
            'Konghucu' => ['KONGHUCU', 'KHONGHUCU', 'CONFUCIUS', 'KONG HU CU', 'KONGHUTSU'],
            'Protestan' => ['PROTESTAN', 'PROTESTANT', 'KRISTEN PROTESTAN', 'REFORMED'],
        ];
        
        foreach ($normalizations as $standard => $variations) {
            if (in_array($religionUpper, $variations)) {
                return $standard;
            }
        }
        
        // Try partial matching for edge cases
        foreach ($normalizations as $standard => $variations) {
            foreach ($variations as $variation) {
                if (strpos($religionUpper, $variation) !== false || strpos($variation, $religionUpper) !== false) {
                    return $standard;
                }
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
     * Get all possible religion variations for validation messages
     */
    public static function getAllVariations()
    {
        return [
            'Islam', 'Muslim', 'Moslem',
            'Kristen', 'Kristiani', 'Christian', 'Protestan', 'Protestant',
            'Katolik', 'Katholik', 'Catholic', 'Kristen Katolik',
            'Hindu', 'Hinduisme',
            'Buddha', 'Buddhis', 'Budha', 'Budhis', 'Buddhisme',
            'Konghucu', 'Khonghucu', 'Confucius', 'Kong Hu Cu'
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

    /**
     * Get validation rule for religion field
     */
    public static function getValidationRule($required = false)
    {
        $validReligions = implode(',', self::getValidReligions());
        
        if ($required) {
            return "required|in:{$validReligions}";
        }
        
        return "nullable|in:{$validReligions}";
    }
}