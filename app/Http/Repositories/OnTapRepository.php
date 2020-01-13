<?php
declare(strict_types=1);

namespace App\Http\Repositories;

use GuzzleHttp\ClientInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

final class OnTapRepository implements OnTapRepositoryInterface
{
    private const CITIES_LIST_URI = 'https://ontap.pl/api/v1/cities';
    private const PLACES_LIST_URI = 'https://ontap.pl/api/v1/cities/%s/pubs';
    private const TAPS_LIST_URI = 'https://ontap.pl/api/v1/pubs/%s/taps';

    private ClientInterface $httpClient;
    private FilesystemAdapter $cache;
    private ?string $cityId = null;
    private ?array $places = [];
    private bool $connectionError;
    private int $reqCount = 0;

    /**
     * @param ClientInterface $httpClient
     * @param FilesystemAdapter $cache
     * @param string $cityName
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function __construct( ClientInterface $httpClient, FilesystemAdapter $cache, ?string $cityName )
    {
        if ( $cityName === null ) {
            $this->connectionError = true;
            return;
        }

        $this->httpClient = $httpClient; //todo: set headers globally
        $this->cache = $cache;
        $this->cityId = $this->fetchCityIdByName( $cityName );
        $this->places = $this->fetchPlacesByCityId( $this->cityId );
        $this->connectionError = $this->cityId === null;
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
     * @return array|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function fetchTapsByBeerName( string $beerName ): ?array
    {
        if ( empty( $beerName ) ) {
            return null;
        }

        $toHash = $this->cityId . '_' . $beerName;
        $cacheKey = md5( $toHash ) . '_ONTAP';
        $item = $this->cache->getItem( $cacheKey );
        if ( $item->isHit() ) {
            return $item->get();
        }

        //fetch all the taps in given places and find beer
        $tapsData = [];
        foreach ( $this->places as $place ) {
            $taps = $this->fetchTapsByPlaceId( $place['id'] );
            if ( empty( $taps ) ) {
                continue;
            }

            if ( $this->hasBeer( $beerName, $taps ) ) {
                $tapsData[ $place['name'] ] = true;
                continue;
            }
        }

        if ( $tapsData === [] ) {
            return null;
        }

        $dataCollection = $this->cache->getItem( $cacheKey );
        $dataCollection->set( $tapsData );
        $this->cache->save( $dataCollection );

        return $tapsData;
    }

    /**
     * @param string $cityName
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
        $response = $this->httpClient->request( 'GET', self::CITIES_LIST_URI, [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Api-Key' => $_ENV['ONTAP_API_KEY']
            ]
        ] );

        $this->reqCount++;

        return \json_decode( $response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR );
    }

    /**
     * @param string|null $cityId
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function fetchPlacesByCityId( ?string $cityId ): array
    {
        $response = $this->httpClient->request( 'GET', \sprintf( self::PLACES_LIST_URI, $cityId ), [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Api-Key' => $_ENV['ONTAP_API_KEY']
            ]
        ] );

        $this->reqCount++;

        return \json_decode( $response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR );
    }

    /**
     * @param string $placeId
     * @return array|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function fetchTapsByPlaceId( string $placeId ): ?array
    {
        $response = $this->httpClient->request( 'GET', \sprintf(self::TAPS_LIST_URI, $placeId), [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Api-Key' => $_ENV['ONTAP_API_KEY']
            ]
        ] );

        $this->reqCount++;

        return \json_decode( $response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR );
    }

    private function hasBeer( string $beerName, array $tapBeerData ): bool
    {
        foreach ( $tapBeerData as $tapBeer ) {
            if ( empty( $tapBeer['beer'] ) ) {
                continue;
            }
            if ( stripos( $tapBeer['beer']['name'], $beerName ) !== false ) {
                return true;
            }
        }

        return false;
    }
}