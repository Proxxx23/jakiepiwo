<?php
declare(strict_types=1);

namespace App\Http\Repositories;

use GuzzleHttp\ClientInterface;

final class OnTapRepository implements OnTapRepositoryInterface
{
    private const CITIES_LIST_URI = 'https://ontap.pl/api/v1/cities';
    private const PLACES_LIST_URI = 'https://ontap.pl/api/v1/cities/%s/pubs';
    private const TAPS_LIST_URI = 'https://ontap.pl/api/v1/pubs/%s/taps';

    /** @var ClientInterface */
    private $httpClient;
    /** @var string */
    private $cityId;

    /**
     * OnTapRepository constructor.
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
     * @param string $beerName
     * @return array|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetchTapsByBeerName( string $beerName ): ?array
    {
        if ( $this->cityId === null || $beerName === '' ) {
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
            $taps = $this->fetchTapsByPlaceId($place['id']);
            if ( $taps === [] ) {
                continue;
            }

            if ( $this->hasBeer( $beerName, $taps ) ) {
                $tapsData[$place['name']] = true;
                continue;
            }
        }

        return $tapsData;
    }

    /**
     * @param string $cityName
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function fetchCityIdByName( string $cityName ): string
    {
        $cities = $this->fetchAllCities();

        if ( \in_array( $cityName, $cities, true ) ) {
            foreach ( $cities as $city ) {
                dd( $city );
            }
        }

        return '';

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
     * @param string $cityId
     * @return array|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function fetchPlacesByCityId( string $cityId ): ?array
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

    private function hasBeer( string $beerName, array $tap ): bool
    {
        foreach ( $tap as $beer ) {
            if ( stripos( $beer['name'], $beerName ) ) {
                return true;
            }
        }

        return false;
    }
}