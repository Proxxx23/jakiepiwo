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
        'lubelskie' => [ 'Lublin',  ],
        'lubuskie' => [ 'Zielona Góra' ],
        'łódzkie' => [ 'Łódź' ],
        'małopolskie' => [ 'Kraków', 'Nowy Sącz', 'Ostrów Wielkopolski', 'Piła', 'Poznań',  ],
        'mazowieckie' => [ 'Legionowo', 'Warszawa', 'Nowy Dwór Mazowiecki',  ],
        'opolskie' => [ 'Opole' ],
        'podkarpackie' => [ 'Rzeszów' ],
        'podlaskie' => [ 'Białystok',  ],
        'śląskie' => [ 'Bielsko-Biała', 'Bytom', 'Chorzów', 'Częstochowa', 'Gliwice', 'Katowice', 'Ruda Śląska', 'Rybnik', 'Sosnowiec', 'Tychy' ],
        'świętokrzyskie' => [ 'Kielce' ],
        'warmińsko-mazurskie' => [ 'Olsztyn',  ],
        'wielkopolskie' => [ 'Kalisz' ],
        'zachodniopomorskie' => [ 'Szczecin' ],
    ];

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
     *
     * @return array|null
     * @throws \GuzzleHttp\Exception\GuzzleException | \JsonException
     */
    public function fetchCitiesByCoordinates( Coordinates $coordinates ): ?array
    {
        if ( $this->citiesList === [] ) {
            return null; // todo: jakoś o tym informować
        }

        try {
            $request = $this->httpClient->request(
                'GET',
                \sprintf( self::API_URL_PATTERN, $coordinates->getLatitude(), $coordinates->getLongitude() )
            );
        } catch ( \Exception $ex ) {
            return null; // todo: informować
        }

        if ( $request->getStatusCode() !== 200 ) {
            return null; // todo: informować
        }

        $xml = \simplexml_load_string(
            $request->getBody()
                ->getContents()
        );
        $json = \json_encode( $xml, \JSON_THROW_ON_ERROR, 512 );
        $content = \json_decode( $json, true, 512, \JSON_THROW_ON_ERROR );

        $partsToSearch = [
            'city' => $content['addressparts']['city'] ?? null,
            'county' => $content['addressparts']['county'] ?? null,
            'suburb' => $content['addressparts']['suburb'] ?? null,
            'neighbourhood' => $content['addressparts']['neighbourhood'] ?? null,
        ];

        foreach ( $partsToSearch as $locality ) {
            if ( \in_array( $locality, $this->citiesList, true ) ) {
                return [ $locality ];
            }
        }

        // brak miasta
        $cities = $this->getAllCitiesInVoivodeship( $content['addressparts']['state'] ?? null );

        return $cities ?? null;
    }

    private function getAllCitiesInVoivodeship( ?string $state ): ?array
    {
        if ( empty( $state ) ) {
            return null;
        }

        /** @var string $state */
        $state = \str_replace( 'województwo ', '', $state );

        return self::ONTAP_CITIES[$state] ?? null;
    }
}
