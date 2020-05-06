<?php
declare( strict_types=1 );

namespace App\Http\Utils;

use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

final class SharedCache
{
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
     */
    public function set( string $cacheKey, $data ): void
    {
        $dataCollection = null;
        try {
            $dataCollection = $this->cache->getItem( $cacheKey );
        } catch ( InvalidArgumentException $ex ) {

        }

        if ( $dataCollection !== null ) {
            $dataCollection->set( $data );
            $this->cache->save( $dataCollection );
        }
    }

    public function clear(): void
    {
        $this->cache->clear();
    }
}
