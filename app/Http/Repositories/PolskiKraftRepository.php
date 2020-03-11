<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

use App\Http\Objects\PolskiKraftData;
use App\Http\Objects\PolskiKraftDataCollection;
use App\Http\Utils\Dictionary;
use GuzzleHttp\ClientInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

final class PolskiKraftRepository implements PolskiKraftRepositoryInterface
{
//    private const DEFAULT_LIST_URI = 'https://www.polskikraft.pl/openapi/style/list';
    private const BEER_LIST_BY_STYLE_URL_PATTERN = 'https://www.polskikraft.pl/openapi/style/%d/examples';
    private const CACHE_KEY_PATTERN = '%s_POLSKIKRAFT';

    private Dictionary $dictionary;
    private FilesystemAdapter $cache;
    private ClientInterface $httpClient;

    public function __construct(
        Dictionary $dictionary,
        FilesystemAdapter $cache,
        ClientInterface $httpClient
    ) {
        $this->dictionary = $dictionary;
        $this->cache = $cache;
        $this->httpClient = $httpClient;
    }

    /**
     * @param int $styleId
     *
     * @return PolskiKraftDataCollection|null
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetchByStyleId( int $styleId ): ?PolskiKraftDataCollection
    {
        if ( !\array_key_exists( $styleId, $this->dictionary->get() ) ) {
            return null;
        }

        $translatedStyleId = $this->dictionary->getById( $styleId );

        $cacheKey = $translatedStyleId !== null
            ? \sprintf( self::CACHE_KEY_PATTERN, $translatedStyleId )
            : null;

        $cachedData = $this->getFromCache( $cacheKey );
        if ( $cachedData !== null ) {
            $cachedData->setCacheKey( $cacheKey );

            return $cachedData;
        }

        $response = $this->httpClient->request(
            'GET',
            \sprintf( self::BEER_LIST_BY_STYLE_URL_PATTERN, $translatedStyleId )
        );

        if ( $response->getStatusCode() !== 200 ) {
            return null; //todo: any message or exception - this case should be covered
        }

        $data = \json_decode(
            $response->getBody()
                ->getContents(), true, 512, JSON_THROW_ON_ERROR
        );
        if ( empty( $data ) ) {
            return null;
        }

        while ( \count( $data ) > 3 ) {
            $randomIdToDelete = \random_int( 0, \count( $data ) - 1 );
            unset( $data[$randomIdToDelete] );
        }

        $polskiKraftCollection = new PolskiKraftDataCollection();
        foreach ( $data as $item ) {
            $polskiKraft = new PolskiKraftData( $item );
            $polskiKraftCollection->add( $polskiKraft->toArray() );
        }

        if ( $cacheKey !== null ) {
            $this->setToCache( $cacheKey, $polskiKraftCollection );
            $polskiKraftCollection->setCacheKey( $cacheKey );
        }

        return $polskiKraftCollection;
    }

    /**
     * @param string $cacheKey
     *
     * @return mixed|null
     *
     * todo: ale to jest kurwa złe, wynieść to w pizdu SRP
     */
    private function getFromCache( ?string $cacheKey )
    {
        if ( $cacheKey === null ) {
            return null;
        }

        $item = null;
        try {
            $item = $this->cache->getItem( $cacheKey );
        } catch ( InvalidArgumentException $e ) {

        }

        return $item !== null && $item->isHit()
            ? $item->get()
            : null;
    }

    /**
     * todo: ale to jest kurwa złe, wynieść to w pizdu SRP
     * @param string $cacheKey
     * @param mixed $data
     */
    private function setToCache( string $cacheKey, $data ): void
    {
        $dataCollection = null;
        try {
            $dataCollection = $this->cache->getItem( $cacheKey );
        } catch ( InvalidArgumentException $e ) {

        }

        if ( $dataCollection !== null ) {
            $dataCollection->set( $data );
            $this->cache->save( $dataCollection );
        }
    }
}
