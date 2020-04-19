<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

use App\Http\Objects\ValueObject\Coordinates;
use GuzzleHttp\ClientInterface;

final class GeolocationRepository implements GeolocationRepositoryInterface
{
    private const API_URL_PATTERN = 'https://nominatim.openstreetmap.org/reverse?lat=%f&lon=%f';

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
     * @throws \GuzzleHttp\Exception\GuzzleException | \JsonException
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

        $xml = \simplexml_load_string($request->getBody()->getContents());
        $json = \json_encode($xml, JSON_THROW_ON_ERROR, 512);
        $content = \json_decode( $json, true, 512, JSON_THROW_ON_ERROR );

        $partsToSearch = [
            'city' => $content['addressparts']['city'] ?? null,
            'county' => $content['addressparts']['county'] ?? null,
            'suburb' => $content['addressparts']['suburb'] ?? null,
            'neighbourhood' => $content['addressparts']['neighbourhood'] ?? null,
        ];

        foreach ( $partsToSearch as $locality ) {
            if ( \in_array( $locality, $this->citiesList, true ) ) {
                return $locality;
            }
        }

        return null;
    }
}
