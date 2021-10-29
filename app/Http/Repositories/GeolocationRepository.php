<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

use App\Http\Objects\ValueObject\Coordinates;
use GuzzleHttp\ClientInterface;

final class GeolocationRepository implements GeolocationRepositoryInterface
{
    private const ONTAP_CITIES = [
        'pomorskie' => [ 'Gdańsk', 'Gdynia', 'Sopot', 'Elbląg', 'Tleń', ],
        'dolnośląskie' => [ 'Jelenia Góra', 'Świdnica', 'Wrocław' ],
        'kujawsko-pomorskie' => [ 'Bydgoszcz', 'Toruń' ],
        'lubelskie' => [ 'Lublin', ],
        'lubuskie' => [ 'Zielona Góra' ],
        'łódzkie' => [ 'Łódź' ],
        'małopolskie' => [ 'Kraków', 'Nowy Sącz', 'Ostrów Wielkopolski', 'Piła', 'Poznań', ],
        'mazowieckie' => [ 'Legionowo', 'Warszawa', 'Nowy Dwór Mazowiecki', ],
        'opolskie' => [ 'Opole' ],
        'podkarpackie' => [ 'Rzeszów' ],
        'podlaskie' => [ 'Białystok', ],
        'śląskie' => [
            'Bielsko-Biała',
            'Bytom',
            'Chorzów',
            'Częstochowa',
            'Gliwice',
            'Katowice',
            'Ruda Śląska',
            'Rybnik',
            'Sosnowiec',
            'Tychy',
        ],
        'świętokrzyskie' => [ 'Kielce' ],
        'warmińsko-mazurskie' => [ 'Olsztyn', ],
        'wielkopolskie' => [ 'Kalisz' ],
        'zachodniopomorskie' => [ 'Szczecin' ],
    ];

    private const OSM_API_URL_PATTERN = 'https://nominatim.openstreetmap.org/reverse?lat=%f&lon=%f'; // takes only city, no radius
    private const GEODB_API_URL_PATTERN = 'http://geodb-free-service.wirefreethought.com/v1/geo/locations/%f+%f/nearbyCities?limit=5&offset=0&minPopulation=40000&radius=50&sort=-population'; // nearest ciies

    private array $citiesList;
    private ?string $state;

    public function __construct( private ClientInterface $httpClient )
    {
    }

    public function setCitiesList( array $citiesList ): void
    {
        $this->citiesList = ( $citiesList !== [] )
            ? \array_column( $citiesList, 'name' )
            : [];
    }

    /**
     * @param Coordinates $coordinates
     *
     * @return array|null
     * @throws \GuzzleHttp\Exception\GuzzleException|\JsonException
     */
    public function fetchCitiesByCoordinates( Coordinates $coordinates ): ?array
    {
        if ( $this->citiesList === [] ) {
            return null; // todo: jakoś o tym informować
        }

        $osmResults = $this->searchViaOSM( $coordinates ) ?? [];
        $geoDbResults = $this->searchViaGeoDB( $coordinates ) ?? [];
        $results = \array_unique( \array_merge( $osmResults, $geoDbResults ) );

        return !empty( $results )
            ? $results
            : $this->getAllCitiesInVoivodeship();  // no city at all
    }

    /**
     * @param Coordinates $coordinates
     *
     * @return array|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    private function searchViaOSM( Coordinates $coordinates ): ?array
    {
        try {
            $request = $this->httpClient->request(
                'GET',
                \sprintf( self::OSM_API_URL_PATTERN, $coordinates->getLatitude(), $coordinates->getLongitude() )
            );
        } catch ( \Exception ) {
            return null;
        }

        if ( $request->getStatusCode() !== 200 ) {
            return null;
        }

        $xml = \simplexml_load_string(
            $request->getBody()
                ->getContents()
        );
        $json = \json_encode( $xml, \JSON_THROW_ON_ERROR );
        $content = \json_decode( $json, true, 512, \JSON_THROW_ON_ERROR );

        $partsToSearch = [
            'city' => $content['addressparts']['city'] ?? null,
            'county' => $content['addressparts']['county'] ?? null,
            'suburb' => $content['addressparts']['suburb'] ?? null,
            'neighbourhood' => $content['addressparts']['neighbourhood'] ?? null,
        ];

        $this->state = $content['addressparts']['state'] ?? null;

        foreach ( $partsToSearch as $locality ) {
            if ( \in_array( $locality, $this->citiesList, true ) ) {
                return [ $locality ];
            }
        }

        return null;
    }

    /**
     * @param Coordinates $coordinates
     *
     * @return array|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    private function searchViaGeoDB( Coordinates $coordinates ): ?array
    {
        try {
            $request = $this->httpClient->request(
                'GET',
                \sprintf( self::GEODB_API_URL_PATTERN, $coordinates->getLatitude(), $coordinates->getLongitude() )
            );
        } catch ( \Exception ) {
            return null;
        }

        if ( $request->getStatusCode() !== 200 ) {
            return null;
        }


        $content = \json_decode(
            $request->getBody()
                ->getContents(), true, 512, \JSON_THROW_ON_ERROR
        );

        $data = $content['data'] ?? null;
        if ( $data === null ) {
            return null;
        }

        $nearbyCities = null;
        foreach ( $data as $locality ) {
            if ( \in_array( $locality['city'], $this->citiesList, true ) ) {
                $nearbyCities[] = $locality['city'];
            }
        }

        return $nearbyCities;
    }

    private function getAllCitiesInVoivodeship(): ?array
    {
        if ( empty( $this->state ) ) {
            return null;
        }

        /** @var string $state */
        $state = \str_replace( 'województwo ', '', $this->state );

        return self::ONTAP_CITIES[$state] ?? null;
    }
}
