<?php
declare(strict_types=1);

namespace App\Http\Repositories;

use App\Http\Objects\PolskiKraftData;
use GuzzleHttp\ClientInterface;

final class OnTapRepository implements OnTapRepositoryInterface
{
    private const CITIES_LIST_URI = 'https://ontap.pl/api/v1/cities';
    private const PLACES_LIST_URI = 'https://ontap.pl/api/v1/cities/%s/pubs';
    private const TAPS_LIST_URI = 'https://ontap.pl/api/v1/pubs/%s/taps';

    private ClientInterface $httpClient;
    private ?string $cityId;

    /**
     * @param ClientInterface $httpClient
     * @param string $cityName
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function __construct( ClientInterface $httpClient, string $cityName )
    {
        $this->httpClient = $httpClient; //todo: set headers globally
        $this->cityId = $this->fetchCityIdByName( $cityName );
    }

    /**
     * @param PolskiKraftData $beerData
     * @return array|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetchTapsByBeerData( PolskiKraftData $beerData ): ?array
    {
        if ( $this->cityId === null || $beerData === null ) {
            return null;
        }

        // first - find all places in the city
        $places = $this->fetchPlacesByCityId( $this->cityId );
        if ( $places === [] ) {
            return null;
        }

        // then - fetch all the taps in given places and find beer
        $tapsData = [];
        foreach ( $places as $place ) {
            $taps = $this->fetchTapsByPlaceId( $place['id'] );
            if ( empty($taps) ) {
                continue;
            }

            if ( $this->hasBeer( $beerData, $taps ) ) {
                $tapsData[$place['name']] = true;
                continue;
            }
        }

        return $tapsData !== []
            ? $tapsData
            : null;
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

        return \json_decode( $response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR );
    }

    private function hasBeer( PolskiKraftData $beerData, array $tapBeerData ): bool
    {
        foreach ( $tapBeerData as $tapBeer ) {
            if ( empty( $tapBeer['beer'] ) ) {
                continue;
            }
            if ( stripos( $tapBeer['beer']['name'], $beerData->getTitle() ) !== false ) {
                return true;
            }
        }

        return false;
    }
}