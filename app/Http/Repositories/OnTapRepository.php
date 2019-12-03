<?php
declare(strict_types=1);

namespace App\Http\Repositories;

use GuzzleHttp\ClientInterface;

final class OnTapRepository implements OnTapRepositoryInterface
{
    /** @var ClientInterface */
    private $httpClient;

    public function __construct( ClientInterface $httpClient )
    {
        $this->httpClient = $httpClient; //todo: set headers globally
    }

    public function fetchCityIdByName( string $cityName ): array
    {
        $cities = $this->fetchAllCities();

        if (\in_array($cityName, $cities, true)) {
            foreach ($cities as $city) {
                //todo: find city id
            }
        }
    }

    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function fetchAllCities(): array
    {
        $response = $this->httpClient->request('GET', 'http://ontap.pl/api/v1/cities', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Api-Key' => 'a7f1daa6b8e99440217f78d601e6779c' //todo: from env
            ]
        ]);

        return \json_decode( $response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR );
    }

    public function fetchPlacesByCityId(): ?array
    {
        // TODO: Implement fetchPlacesByCityId() method.
    }

    public function fetchTapsByPlaceId( string $placeId ): ?array
    {
        // TODO: Implement fetchTapsByPlaceId() method.
    }
}