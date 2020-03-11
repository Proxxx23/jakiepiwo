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

    private const DEFAULT_TTL = 900;

    private ClientInterface $httpClient;
    private FilesystemAdapter $cache;
    private ?string $cityId = null;
    private bool $connectionError;
    private string $cityName;

    /**
     * @param ClientInterface $httpClient
     * @param FilesystemAdapter $cache
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function __construct( ClientInterface $httpClient, FilesystemAdapter $cache )
    {
        $this->cache = $cache;
        $this->httpClient = $httpClient; //todo: set headers globally
        $this->connectionError = $this->checkIsConnectionRefused();

    }

    public function setCityName( string $cityName ): void
    {
        $this->cityName = $cityName;
    }

    public function connectionNotRefused(): bool
    {
        return !$this->connectionError;
    }

    /**
     * @param string $beerName
     *
     * @return array|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetchTapsByBeerName( string $beerName ): ?array
    {
        if ( $beerName === '' ) {
            return null;
        }

        //todo: strategy?
        $toHash = $this->cityId . '_' . $beerName;
        $cacheKey = \sprintf( self::CACHE_KEY_BEER_PATTERN, \md5( $toHash ) );
        $item = $this->getFromCache( $cacheKey );
        if ( $item !== null ) {
            return $item;
        }

        $places = $this->fetchPlacesByCityId();

        //fetch all the taps in given places and find beer
        $tapsData = null;
        foreach ( $places as $place ) {
            $taps = $this->fetchTapsByPlaceId( $place['id'] );
            if ( empty( $taps ) ) {
                continue;
            }
            if ( $this->hasBeer( $beerName, $taps ) ) {
                $tapsData[$beerName][] = $place['name'];
                continue;
            }
        }

        if ( $tapsData === null ) {
            return null;
        }

        $this->setToCache( $cacheKey, $tapsData );

        return $tapsData;
    }

    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetchAllCities(): array
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

        $data = \json_decode( $response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR );
        $this->setToCache( self::CACHE_KEY_CITIES, $data );

        return $data;
    }

    /**
     * @return string|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function fetchCityIdByName(): ?string
    {
        $cities = $this->fetchAllCities();
        $cityId = \array_search( $this->cityName, \array_column( $cities, 'name' ), true );

        return \is_int( $cityId )
            ? $cities[$cityId]['id']
            : null;
    }

    /**
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function checkIsConnectionRefused(): bool
    {
        $response = $this->httpClient->request(
            'GET', self::CITIES_LIST_URI, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Api-Key' => $_ENV['ONTAP_API_KEY'],
                ],
            ]
        );

        return empty( $response->getBody()->getContents() ) || $response->getStatusCode() !== 200;
    }

    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function fetchPlacesByCityId(): array
    {
        $cityId = $this->fetchCityIdByName();
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

        $data = \json_decode( $response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR );
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

        $data = \json_decode( $response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR );
        $this->setToCache( $cacheKey, $data );

        return $data;
    }

    private function hasBeer( string $beerName, array $tapBeerData ): bool
    {
        foreach ( $tapBeerData as &$tapBeer ) {
            if ( empty( $tapBeer['beer'] ) ) {
                continue;
            }
            if ( \stripos( $tapBeer['beer']['name'], $beerName ) !== false ) {
                return true;
            }
        }
        unset( $tapBeer );

        return false;
    }

    // todo standalone class

    /**
     * todo: ale to jest kurwa złe, wynieść to w pizdu SRP
     * @param string $cacheKey
     * @return mixed|null
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


    /**
     * todo: ale to jest kurwa złe, wynieść to w pizdu SRP
     * todo: wyczaić jak ustawiać TTL przy save
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
