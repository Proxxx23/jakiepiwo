<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

use App\Http\Utils\SharedCache;
use GuzzleHttp\ClientInterface;
use Illuminate\Support\Facades\DB;
use Transliterator;

final class UntappdRepository implements UntappdRepositoryInterface
{
    private const BEER_SEARCH_URL_PATTERN = 'https://api.untappd.com/v4/search/beer?q=%s&client_id=%s&client_secret=%s';

    private ClientInterface $client;
    private SharedCache $cache;

    public function __construct( ClientInterface $client, SharedCache $cache )
    {
        $this->client = $client;
        $this->cache = $cache;
    }

    // todo refactor this class and method to UntappdAPI
    public function fetchOne( string $beerName, string $breweryName ): ?array
    {
        $searchPhrase = $this->buildSearchPhrase( $breweryName, $beerName );

        $cacheKey = \md5( $searchPhrase ) . '_UNTAPPD';
        $cachedItem = $this->cache->get( $cacheKey );
        if ( $cachedItem !== null ) {
            return $cachedItem;
        }

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

        $inProduction = isset( $response['response']['beers'] ) && $response['response']['beers']['count'] === 1
            ? $response['response']['beers']['items'][0]['beer']['in_production'] ?? null
            : null;

        return [ 'inProduction' => $inProduction ];
    }

    private function buildSearchPhrase( string $breweryName, string $beerName ): string
    {
        return \str_replace( ' ', '+', $breweryName ) . '+' . \str_replace( ' ', '+', $beerName );
    }

    public function add( array $beerData ): void
    {
        $rule = ':: Latin-ASCII; :: NFD; :: [:Nonspacing Mark:] Remove; :: Lower(); :: NFC;';
        $i18n = \Transliterator::createFromRules( $rule, Transliterator::FORWARD );

        $data = null;
        foreach ( $beerData as $index => $beer ) {
            $breweryName = $i18n->transliterate( $beer['subtitle'] );
            $beerName = \preg_replace( '/[^A-Za-z0-9_ ]/', '', $i18n->transliterate( $beer['title'] ) );
            $data[] = [
                'beer_name' => \str_replace( '  ', ' ', $beerName ),
                'brewery_name' => \str_replace( '  ', ' ', $breweryName ),
                'next_update' => \time(),
            ];
        }

        try {
            DB::table( 'untappd' )
                ->insertOrIgnore( $data );
        } catch ( \Exception $ex ) {

        }
    }
}
