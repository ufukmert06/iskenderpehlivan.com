<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait CachesApiResponses
{
    /**
     * Cache duration in seconds (6 hours)
     */
    protected int $cacheDuration = 21600;

    /**
     * Get the current cache version for a given entity type
     */
    protected function getCacheVersion(string $entityType): int
    {
        return Cache::get("api_cache_version_{$entityType}", 1);
    }

    /**
     * Increment the cache version for a given entity type
     * This effectively invalidates all cached responses for that entity
     */
    public static function incrementCacheVersion(string $entityType): void
    {
        $currentVersion = Cache::get("api_cache_version_{$entityType}", 1);
        Cache::forever("api_cache_version_{$entityType}", $currentVersion + 1);
    }

    /**
     * Generate a unique cache key for API responses
     */
    protected function getCacheKey(string $baseKey, array $params = []): string
    {
        // Sort params for consistent cache keys
        ksort($params);

        // Build cache key with parameters
        $key = $baseKey;
        foreach ($params as $param => $value) {
            if ($value !== null) {
                $key .= "_{$param}_{$value}";
            }
        }

        return $key;
    }

    /**
     * Cache a response with the configured duration
     */
    protected function cacheResponse(string $key, callable $callback): mixed
    {
        return Cache::remember($key, $this->cacheDuration, $callback);
    }

    /**
     * Clear all API cache (use with caution)
     */
    public static function clearAllApiCache(): void
    {
        $entityTypes = ['posts', 'categories', 'tags', 'settings'];

        foreach ($entityTypes as $entityType) {
            self::incrementCacheVersion($entityType);
        }
    }
}
