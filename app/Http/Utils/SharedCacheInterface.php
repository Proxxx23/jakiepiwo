<?php declare(strict_types=1);

namespace App\Http\Utils;

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
     */
    public function set( string $cacheKey, $data ): void;
    public function clear(): void;
}
