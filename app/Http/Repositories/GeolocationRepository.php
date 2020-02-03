<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

use App\Http\Objects\ValueObject\Coordinates;
use GuzzleHttp\ClientInterface;

final class GeolocationRepository implements GeolocationRepositoryInterface
{
    private const API_URL_PATTERN = 'https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=%f&longitude=%f&localityLanguage=pl';

    private ClientInterface $httpClient;
    private array $citiesList;

    //todo indicator like in ontap - errorconnection

    public function __construct( ClientInterface $httpClient )
    {
        $this->httpClient = $httpClient;
    }

    public function setCitiesList( array $citiesList ): void
    {
        $this->citiesList = ( $citiesList !== [] )
            ? \array_column( $citiesList, 'name' )
            : [];
    }

    /**
     * @param Coordinates $coordinates
     * @return string|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetchCityByCoordinates( Coordinates $coordinates ): ?string
    {
        if ( $this->citiesList === [] ) {
            return null; // todo: jakoś o tym informować
        }

        $request = $this->httpClient->request(
            'GET',
            \sprintf( self::API_URL_PATTERN, $coordinates->getLatitude(), $coordinates->getLongitude() )
        );

        if ( $request->getStatusCode() !== 200 ) {
            return null;
        }

        $content = \json_decode( $request->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR );
        $found = \preg_grep( '/^miasto.*$/', \array_column( $content['localityInfo']['administrative'], 'description' ) );
        if ( empty( $found ) ) {
            return null;
        }

        $index = \array_keys( $found )[0];
        $cityName = $content['localityInfo']['administrative'][$index]['name'];
        if ( !\in_array( $cityName, $this->citiesList, true ) ) {
            return null;
        }

        return $cityName;
    }
}
