<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class FastHashService
{
    protected static $hashCache = [];
    protected static $batchSize = 50;

    /**
     * Batch hash passwords for better performance
     */
    public static function batchHashPasswords(array $passwords): array
    {
        $startTime = microtime(true);
        $hashedPasswords = [];

        Log::info('[FAST_HASH] Starting batch hash', ['count' => count($passwords)]);

        // Process passwords in smaller batches to avoid memory issues
        $chunks = array_chunk($passwords, self::$batchSize);

        foreach ($chunks as $chunk) {
            foreach ($chunk as $key => $password) {
                // Check cache first
                $cacheKey = md5($password);
                if (isset(self::$hashCache[$cacheKey])) {
                    $hashedPasswords[$key] = self::$hashCache[$cacheKey];
                } else {
                    // Hash and cache
                    $hashed = Hash::make($password);
                    self::$hashCache[$cacheKey] = $hashed;
                    $hashedPasswords[$key] = $hashed;
                }
            }
        }

        $endTime = microtime(true);
        $totalTime = ($endTime - $startTime) * 1000;

        Log::info('[FAST_HASH] Batch hash completed', [
            'count' => count($passwords),
            'time_ms' => number_format($totalTime, 2),
            'avg_per_hash' => number_format($totalTime / count($passwords), 2) . 'ms'
        ]);

        return $hashedPasswords;
    }

    /**
     * Fast hash with caching for duplicate passwords
     */
    public static function fastHash(string $password): string
    {
        $cacheKey = md5($password);

        if (isset(self::$hashCache[$cacheKey])) {
            return self::$hashCache[$cacheKey];
        }

        $hashed = Hash::make($password);
        self::$hashCache[$cacheKey] = $hashed;

        return $hashed;
    }

    /**
     * Pre-generate common passwords for faster import
     */
    public static function preGenerateCommonHashes(): void
    {
        $commonPasswords = [
            'password123',
            'admin123',
            'sekolah123',
            '12345678',
            'password',
            'admin',
        ];

        Log::info('[FAST_HASH] Pre-generating common password hashes');

        foreach ($commonPasswords as $password) {
            self::fastHash($password);
        }
    }

    /**
     * Clear hash cache to free memory
     */
    public static function clearCache(): void
    {
        self::$hashCache = [];
        Log::info('[FAST_HASH] Cache cleared');
    }

    /**
     * Get cache statistics
     */
    public static function getCacheStats(): array
    {
        return [
            'cached_hashes' => count(self::$hashCache),
            'memory_usage' => memory_get_usage(),
            'cache_keys' => array_keys(self::$hashCache)
        ];
    }
}
