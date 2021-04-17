<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

use App\Utils\SharedCache;
use GuzzleHttp\ClientInterface;

final class OnTapRepository implements OnTapRepositoryInterface
{
    private const CITIES_LIST_URI = 'https://ontap.pl/api/v1/cities';
    private const PLACES_LIST_URI = 'https://ontap.pl/api/v1/cities/%s/pubs';
    private const TAPS_LIST_URI = 'https://ontap.pl/api/v1/pubs/%s/taps';

    private const CACHE_KEY_BEER_PATTERN = '%s_BEER_ONTAP';
    private const CACHE_KEY_PLACES_PATTERN = '%s_PLACES_ONTAP';
    private const CACHE_KEY_TAPS_PATTERN = '%s_TAPS_ONTAP';
    private const CACHE_KEY_CITIES = 'CITIES_ONTAP';

    private const CITIES_CACHE_TTL = 604800; // 7 days
    private const PLACES_CACHE_TTL = 604800; // 7 days
    private const TAPS_CACHE_TTL = 7200; // 1 hour
    private const TAPS_BY_BEER_CACHE_TTL = 7200; // 1 hour

    private bool $connectionError;
    private array $cities;

    /**
     * @param ClientInterface $httpClient
     * @param SharedCache $cache
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function __construct( private ClientInterface $httpClient, private SharedCache $cache )
    {
        $this->connectionError = $this->checkIsConnectionRefused();
    }

    public function setCities( array $cities ): void
    {
        $this->cities = $cities;
    }

    public function connectionRefused(): bool
    {
        return $this->connectionError;
    }

    /**
     * @param array $beerData
     *
     * @return array|null
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function fetchTapsByBeerName( array $beerData ): ?array
    {
        $beerName = $beerData['title'];
        if ( $beerName === '' ) {
            return null;
        }

        //todo: strategy?
        $toHash = \implode( '_', $this->cities ) . '_' . $beerName;
        $cacheKey = \sprintf( self::CACHE_KEY_BEER_PATTERN, \md5( $toHash ) );
        $item = $this->cache->get( $cacheKey );
        if ( $item !== null ) {
            return $item;
        }

        $places = $this->fetchPlacesByCityId();

        //fetch all the taps in given places and find beer
        $tapsData = null;
        foreach ( $places as $cityId ) {
            foreach ( $cityId as $place ) {
                $taps = $this->fetchTapsByPlaceId( $place['id'] );
                if ( empty( $taps ) ) {
                    continue;
                }
                if ( $this->hasBeer( $beerData, $taps ) ) {
                    $tapsData[$beerName][] = [
                        'name' => $place['name'],
                        'url' => 'https://ontap.pl/?multitap_id=' . $place['id'],
                    ];
                    continue;
                }
            }
        }

        if ( $tapsData === null ) {
            return null;
        }

        $this->cache->set( $cacheKey, $tapsData, self::TAPS_BY_BEER_CACHE_TTL );

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
                ->getContents(), true, 512, \JSON_THROW_ON_ERROR
        );
        $this->cache->set( self::CACHE_KEY_CITIES, $data, self::CITIES_CACHE_TTL );

        return $data;
    }

    /**
     * @return array|null
     * @throws \GuzzleHttp\Exception\GuzzleException | \JsonException
     */
    private function fetchCityIdsByName(): ?array
    {
        $cities = $this->fetchAllCities();
        $cityIds = null;
        foreach ( $this->cities as $city ) {
            $cityId = \array_search( $city, \array_column( $cities, 'name' ), true );
            if ( \is_int( $cityId ) ) {
                $cityIds[] = $cities[$cityId]['id'];
                continue;
            }
        }

        return $cityIds ?? null;
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
        $cityIds = $this->fetchCityIdsByName();
        $cacheKey = \sprintf( self::CACHE_KEY_PLACES_PATTERN, \md5( \implode( '_', $cityIds ) ) );
        $cachedData = $this->cache->get( $cacheKey );
        if ( $cachedData !== null ) {
            return $cachedData;
        }

        $data = null;
        foreach ( $cityIds as $cityId ) {
            $response = $this->httpClient->request(
                'GET', \sprintf( self::PLACES_LIST_URI, $cityId ), [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'Api-Key' => $_ENV['ONTAP_API_KEY'],
                    ],
                ]
            );

            $data[$cityId] = \json_decode(
                $response->getBody()
                    ->getContents(), true, 512, \JSON_THROW_ON_ERROR
            );
        }

        $this->cache->set( $cacheKey, $data, self::PLACES_CACHE_TTL );

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
                ->getContents(), true, 512, \JSON_THROW_ON_ERROR
        );
        $this->cache->set( $cacheKey, $data, self::TAPS_CACHE_TTL );

        return $data;
    }

    private function hasBeer( array $beerData, array $tapBeerData ): bool
    {
        $beerName = \strtolower( $beerData['title'] );
        $breweryName = \strtolower( $beerData['subtitle'] );
        $style = \strtolower( $beerData['subtitleAlt'] );

        foreach ( $tapBeerData as $tapBeer ) {
            if ( empty( $tapBeer['beer'] ) ) {
                continue;
            }

            $onTapBeerName = \strtolower( $tapBeer['beer']['name'] );
            $breweryNameMatches = $this->breweryNameMatches( $breweryName, $tapBeer['beer']['brewery'] );

            if ( ( $style === $onTapBeerName || $onTapBeerName === $beerName ) && $breweryNameMatches ) {
                return true;
            }
        }

        return false;
    }

    private function breweryNameMatches( string $breweryName, string $onTapBreweryName ): bool
    {
        $variants = [ $breweryName ];

        if ( \preg_match( '/^Browar (?P<breweryName>[a-zA-Z ]+)/', $breweryName, $matches ) ) {
            $variants[] = $matches['breweryName'];
        }

        return (bool) \preg_match( '/.*' . \implode( '|', $variants ) . '.*/i', $onTapBreweryName );
    }
}
