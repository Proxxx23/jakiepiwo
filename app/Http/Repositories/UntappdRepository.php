<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

use GuzzleHttp\ClientInterface;

final class UntappdRepository
{
    private const BEER_SEARCH_URL_PATTERN = 'https://api.untappd.com/v4/search/beer?q=%s&client_id=%s&client_secret=%s';
    private const BEER_INFO_URL_PATTERN = 'https://api.untappd.com/v4/beer/info/%d?client_id=%s&client_secret=%s';

    private ClientInterface $client;

    public function __construct( ClientInterface $client )
    {
        $this->client = $client;
    }

    public function fetchOne( string $beerName, string $breweryName ): ?array
    {
        $searchPhrase = $beerName . ' ' . $breweryName;

        $url = \sprintf(
            self::BEER_SEARCH_URL_PATTERN, $searchPhrase, \env( 'UNTAPPD_CLIENT_ID' ), \env( 'UNTAPPD_CLIENT_SECRET' )
        );

        $request = $this->client->request( 'GET', $url );
        if ( $request->getStatusCode() !== 200 ) {
            return null;
        }

        $response = \json_decode(
            $request->getBody()
                ->getContents(), true
        );

        if ( empty( $response ) ) {
            return null;
        }

        $beerId = $response['response']['beers']['items'][0]['beer']['bid'] ?? null;
        if ( $beerId === null) {
            return null;
        }

        return $this->fetchBeerInfo( $beerId );
    }

    private function fetchBeerInfo( int $beerId ): ?array
    {
        $url = \sprintf(
            self::BEER_INFO_URL_PATTERN, $beerId, \env( 'UNTAPPD_CLIENT_ID' ), \env( 'UNTAPPD_CLIENT_SECRET' )
        );

        $request = $this->client->request( 'GET', $url );
        if ( $request->getStatusCode() !== 200 ) {
            return null;
        }

        $response = \json_decode(
            $request->getBody()
                ->getContents(), true
        );

        if ( empty( $response ) ) {
            return null;
        }

        return $response['response']['beer'] ?? null;
    }
}
