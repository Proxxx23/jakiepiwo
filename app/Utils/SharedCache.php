<?php
declare( strict_types=1 );

namespace App\Utils;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

final class SharedCache implements SharedCacheInterface
{
    public const DEFAULT_CACHE_TTL = 1800;

    public function __construct( private FilesystemAdapter $cache )
    {
    }

    public function get( ?string $cacheKey ): mixed
    {
        if ( $cacheKey === null ) {
            return null;
        }

        $item = null;
        try {
            /** @var CacheItemInterface $item */
            $item = $this->cache->getItem( $cacheKey );
        } catch ( InvalidArgumentException ) {

        }

        return $item !== null && $item->isHit()
            ? $item->get()
            : null;
    }

    public function set( string $cacheKey, mixed $data, int $ttl = self::DEFAULT_CACHE_TTL ): void
    {
        $item = null;
        try {
            /** @var CacheItemInterface $item */
            $item = $this->cache->getItem( $cacheKey );
        } catch ( InvalidArgumentException ) {

        }

        $item->set( $data )
            ->expiresAfter( $ttl );
        $this->cache->save( $item );
    }

    public function clear(): void
    {
        $this->cache->clear();
    }
}
