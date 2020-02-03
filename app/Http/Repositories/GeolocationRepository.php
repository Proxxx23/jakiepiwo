<?php

namespace App\Http\Repositories;

use App\Http\Objects\ValueObject\Coordinates;
use GuzzleHttp\ClientInterface;

final class GeolocationRepository implements GeolocationRepositoryInterface
{
    private const API_URL_PATTERN = 'https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=%f&longitude=%f&localityLanguage=pl';

    private ClientInterface $httpClient;
    private array $citiesList;

    public function __construct( ClientInterface $httpClient, array $citiesList )
    {
        $this->httpClient = $httpClient;
        $this->citiesList = \array_column( $citiesList, 'name' );
    }

    public function fetchCityByCoordinates( Coordinates $coordinates ): ?string
    {
        $request = $this->httpClient->request('GET',
            \sprintf(self::API_URL_PATTERN, $coordinates->getLatitude(), $coordinates->getLongitude()));

        if ( $request->getStatusCode() !== 200 ) {
            return null;
        }

        $content = \json_decode( $request->getBody()->getContents() , true);
        $found = \preg_grep( '/^miasto.*$/', \array_column( $content['localityInfo']['administrative'], 'description') );
        if ( empty( $found )) {
            return null;
        }

        $index = array_keys($found)[0];
        $cityName = $content['localityInfo']['administrative'][$index]['name'];
        if ( !in_array( $cityName, $this->citiesList, true ) ) {
            return null;
        }

        return $cityName;
    }
}