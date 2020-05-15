<?php
declare( strict_types=1 );

namespace App\Http\Utils;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

final class OnTapCache implements SharedCacheInterface
{
    public const DEFAULT_ONTAP_CACHE_TTL = 7200;

    private FilesystemAdapter $cache;

    public function __construct( FilesystemAdapter $cache )
    {
        $this->cache = $cache;
    }

    /**
     * @param string $cacheKey
     *
     * @return mixed|null
     */
    public function get( ?string $cacheKey )
    {
        if ( $cacheKey === null ) {
            return null;
        }

        $item = null;
        try {
            /** @var ItemInterface $item */
            $item = $this->cache->getItem( $cacheKey );
        } catch ( InvalidArgumentException $ex ) {

        }

        return $item !== null && $item->isHit()
            ? $item->get()
            : null;
    }

    /**
     * @param string $cacheKey
     * @param mixed $data
     * @param int $ttl
     */
    public function set( string $cacheKey, $data, int $ttl = self::DEFAULT_ONTAP_CACHE_TTL ): void
    {
        $dataCollection = null;
        try {
            /** @var CacheItemInterface $dataCollection */
            $dataCollection = $this->cache->getItem( $cacheKey );
        } catch ( InvalidArgumentException $ex ) {

        }

        if ( $dataCollection !== null ) {
            $dataCollection->set( $data );
            $dataCollection->expiresAfter( $ttl );
            $this->cache->save( $dataCollection );
        }
    }

    public function clear(): void
    {
        $this->cache->clear();
    }
}
