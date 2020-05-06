<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

use App\Http\Utils\SharedCache;
use GuzzleHttp\ClientInterface;

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
    private SharedCache $cache;
    private ?string $cityId = null;
    private bool $connectionError;
    private string $cityName;

    /**
     * @param ClientInterface $httpClient
     * @param SharedCache $cache
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function __construct( ClientInterface $httpClient, SharedCache $cache )
    {
        $this->cache = $cache;
        $this->httpClient = $httpClient; //todo: set headers globally
        $this->connectionError = $this->checkIsConnectionRefused();

    }

    public function setCityName( string $cityName ): void
    {
        $this->cityName = $cityName;
    }

    public function connectionRefused(): bool
    {
        return $this->connectionError;
    }

    /**
     * @param string $beerName
     *
     * @param string $breweryName
     *
     * @return array|null
     * @throws \GuzzleHttp\Exception\GuzzleException | \JsonException
     */
    public function fetchTapsByBeerName( string $beerName, string $breweryName ): ?array
    {
        if ( $beerName === '' ) {
            return null;
        }

        //todo: strategy?
        $toHash = $this->cityId . '_' . $beerName;
        $cacheKey = \sprintf( self::CACHE_KEY_BEER_PATTERN, \md5( $toHash ) );
        $item = $this->cache->get( $cacheKey );
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
            if ( $this->hasBeer( $beerName, $breweryName, $taps ) ) {
                $tapsData[$beerName][] = $place['name'];
                continue;
            }
        }

        if ( $tapsData === null ) {
            return null;
        }

        $this->cache->set( $cacheKey, $tapsData );

        return $tapsData;
    }

    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException | \JsonException
     */
    public function fetchAllCities(): array
    {
        $cachedData = $this->cache->get( self::CACHE_KEY_CITIES );
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

        $data = \json_decode(
            $response->getBody()
                ->getContents(), true, 512, JSON_THROW_ON_ERROR
        );
        $this->cache->set( self::CACHE_KEY_CITIES, $data );

        return $data;
    }

    /**
     * @return string|null
     * @throws \GuzzleHttp\Exception\GuzzleException | \JsonException
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

        return empty(
            $response->getBody()
                ->getContents()
            ) || $response->getStatusCode() !== 200;
    }

    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException | \JsonException
     */
    private function fetchPlacesByCityId(): array
    {
        $cityId = $this->fetchCityIdByName();
        $cacheKey = \sprintf( self::CACHE_KEY_PLACE_PATTERN, $cityId );
        $cachedData = $this->cache->get( $cacheKey );
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

        $data = \json_decode(
            $response->getBody()
                ->getContents(), true, 512, JSON_THROW_ON_ERROR
        );
        $this->cache->set( $cacheKey, $data );

        return $data;
    }

    /**
     * @param string $placeId
     *
     * @return array|null
     * @throws \GuzzleHttp\Exception\GuzzleException | \JsonException
     */
    private function fetchTapsByPlaceId( string $placeId ): ?array
    {
        $cacheKey = \sprintf( self::CACHE_KEY_TAPS_PATTERN, $placeId );
        $cachedData = $this->cache->get( $cacheKey );
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

        $data = \json_decode(
            $response->getBody()
                ->getContents(), true, 512, JSON_THROW_ON_ERROR
        );
        $this->cache->set( $cacheKey, $data );

        return $data;
    }

    private function hasBeer( string $beerName, string $breweryName, array $tapBeerData ): bool
    {
        $beerNameWordsCount = \count( \explode( ' ', $beerName ) );

        foreach ( $tapBeerData as $tapBeer ) {
            if ( empty( $tapBeer['beer'] ) ) {
                continue;
            }

            if ( $beerNameWordsCount > 1 &&
                \stripos( $tapBeer['beer']['name'], $beerName ) !== false ) {
                return true;
            }

            if ( $beerNameWordsCount === 1 &&
                \stripos( $tapBeer['beer']['name'], $beerName ) !== false &&
                \stripos( $tapBeer['beer']['brewery'], $breweryName ) !== false ) {
                // todo: if beer name === picked style name
                return true;
            }
        }

        return false;
    }
}
