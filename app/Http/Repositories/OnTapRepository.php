<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

use GuzzleHttp\ClientInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

final class OnTapRepository implements OnTapRepositoryInterface
{
    private const CITIES_LIST_URI = 'https://ontap.pl/api/v1/cities';
    private const PLACES_LIST_URI = 'https://ontap.pl/api/v1/cities/%s/pubs';
    private const TAPS_LIST_URI = 'https://ontap.pl/api/v1/pubs/%s/taps';

    private const CACHE_KEY_BEER_PATTERN = '%s_BEER_ONTAP';
    private const CACHE_KEY_PLACE_PATTERN = '%s_PLACE_ONTAP';
    private const CACHE_KEY_TAPS_PATTERN = '%s_TAPS_ONTAP';
    private const CACHE_KEY_CITIES = 'CITIES_ONTAP';

    private ClientInterface $httpClient;
    private FilesystemAdapter $cache;
    private ?string $cityId = null;
    private ?array $places = [];
    private bool $connectionError = false;
    private int $reqCount = 0;

    /**
     * @param ClientInterface $httpClient
     * @param FilesystemAdapter $cache
     * @param string $cityName
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function __construct( ClientInterface $httpClient, FilesystemAdapter $cache, ?string $cityName )
    {
        if ( $cityName === null ) {
            $this->connectionError = true;
            return;
        }

        $this->cache = $cache;
        $this->httpClient = $httpClient; //todo: set headers globally

        $this->cityId = $this->fetchCityIdByName( $cityName );
        if ( $this->cityId === null ) {
            $this->connectionError = true;
        }

        $this->places = $this->fetchPlacesByCityId( $this->cityId );
    }

    public function connected(): bool
    {
        return !$this->connectionError;
    }

    public function placesFound(): bool
    {
        return $this->places !== [];
    }

    /**
     * @param string $beerName
     *
     * @return array|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetchTapsByBeerName( string $beerName ): ?array
    {
        if ( empty( $beerName ) ) {
            return null;
        }

        //todo: strategy?
        $toHash = $this->cityId . '_' . $beerName;
        $cacheKey = \sprintf( self::CACHE_KEY_BEER_PATTERN, \md5( $toHash ) );
        $item = $this->getFromCache( $cacheKey );
        if ( $item !== null ) {
            return $item;
        }

        //fetch all the taps in given places and find beer
        $tapsData = [];
        foreach ( $this->places as $place ) {
            $taps = $this->fetchTapsByPlaceId( $place['id'] );
            if ( empty( $taps ) ) {
                continue;
            }

            if ( $this->hasBeer( $beerName, $taps ) ) {
                $tapsData[$place['name']] = true;
                continue;
            }
        }

        if ( $tapsData === [] ) {
            return null;
        }

        $this->setToCache( $cacheKey, $tapsData );

        return $tapsData;
    }

    /**
     * @param string $cityName
     *
     * @return string|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function fetchCityIdByName( string $cityName ): ?string
    {
        $cities = $this->fetchAllCities();
        $cityId = \array_search( $cityName, \array_column( $cities, 'name' ), true );

        return \is_int( $cityId )
            ? $cities[$cityId]['id']
            : null;
    }

    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function fetchAllCities(): array
    {
        $cachedData = $this->getFromCache( self::CACHE_KEY_CITIES );
        if ( $cachedData !== null ) {
            return $cachedData;
        }

        $response = $this->httpClient->request(
            'GET', self::CITIES_LIST_URI, [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Api-Key' => $_ENV['ONTAP_API_KEY'],
            ],
        ]
        );

        $this->reqCount++;

        $data = \json_decode(
            $response->getBody()
                ->getContents(), true, 512, JSON_THROW_ON_ERROR
        );
        $this->setToCache( self::CACHE_KEY_CITIES, $data );

        return $data;
    }

    /**
     * @param string|null $cityId
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function fetchPlacesByCityId( ?string $cityId ): array
    {
        $cacheKey = \sprintf( self::CACHE_KEY_PLACE_PATTERN, $cityId );
        $cachedData = $this->getFromCache( $cacheKey );
        if ( $cachedData !== null ) {
            return $cachedData;
        }

        $response = $this->httpClient->request(
            'GET', \sprintf( self::PLACES_LIST_URI, $cityId ), [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Api-Key' => $_ENV['ONTAP_API_KEY'],
            ],
        ]
        );

        $this->reqCount++;

        $data = \json_decode(
            $response->getBody()
                ->getContents(), true, 512, JSON_THROW_ON_ERROR
        );
        $this->setToCache( $cacheKey, $data );

        return $data;
    }

    /**
     * @param string $placeId
     *
     * @return array|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function fetchTapsByPlaceId( string $placeId ): ?array
    {
        $cacheKey = \sprintf( self::CACHE_KEY_TAPS_PATTERN, $placeId );
        $cachedData = $this->getFromCache( $cacheKey );
        if ( $cachedData !== null ) {
            return $cachedData;
        }

        $response = $this->httpClient->request(
            'GET', \sprintf( self::TAPS_LIST_URI, $placeId ), [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Api-Key' => $_ENV['ONTAP_API_KEY'],
            ],
        ]
        );

        $this->reqCount++;

        $data = \json_decode(
            $response->getBody()
                ->getContents(), true, 512, JSON_THROW_ON_ERROR
        );
        $this->setToCache( $cacheKey, $data );

        return $data;
    }

    private function hasBeer( string $beerName, array $tapBeerData ): bool
    {
        foreach ( $tapBeerData as $tapBeer ) {
            if ( empty( $tapBeer['beer'] ) ) {
                continue;
            }
            if ( \stripos( $tapBeer['beer']['name'], $beerName ) !== false ) {
                return true;
            }
        }

        return false;
    }

    // todo standalone class

    /**
     * @param string $cacheKey
     *
     * @return mixed|null
     *
     * todo: ale to jest kurwa złe, wynieść to w pizdu SRP
     */
    private function getFromCache( string $cacheKey )
    {
        $item = null;
        try {
            $item = $this->cache->getItem( $cacheKey );
        } catch ( InvalidArgumentException $e ) {

        }

        return $item !== null && $item->isHit()
            ? $item->get()
            : null;
    }

    // todo: ale to jest kurwa złe, wynieść to w pizdu SRP
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
