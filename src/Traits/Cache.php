<?php

/**
 * Trait Cache.
 */

declare(strict_types=1);

namespace SEOCLI\Traits;

/**
 * Trait Cache.
 */
trait Cache
{
    /**
     * Cache.
     *
     * @var array
     */
    protected static $cache = [];

    /**
     * Get the cache entry.
     *
     * @param int $time Time in seconds (0 = request based, <0 = force execution, >0 = seconds)
     *
     * @return mixed
     */
    public function getCache(string $identifier, callable $callback, int $time = 0)
    {
        if ($this->hasCache($identifier)) {
            return self::$cache[$identifier];
        }
        self::$cache[$identifier] = \call_user_func($callback);

        return self::$cache[$identifier];
    }

    /**
     * Check if there is a cache entry.
     */
    public function hasCache(string $identifier): bool
    {
        return \array_key_exists($identifier, self::$cache);
    }
}
