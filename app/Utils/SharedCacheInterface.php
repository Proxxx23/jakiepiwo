<?php declare(strict_types=1);

namespace App\Utils;

interface SharedCacheInterface
{
    /**
     * @param string|null $cacheKey
     * @return mixed|null
     */
    public function get( ?string $cacheKey );

    /**
     * @param string $cacheKey
     * @param mixed $data
     * @param int $ttl
     */
    public function set( string $cacheKey, $data, int $ttl ): void;
    public function clear(): void;
}
