<?php declare( strict_types=1 );

namespace App\Utils;

interface SharedCacheInterface
{
    public function get( ?string $cacheKey ): mixed;
    public function set( string $cacheKey, mixed $data, int $ttl ): void;
    public function clear(): void;
}
