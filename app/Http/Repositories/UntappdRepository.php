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

    /**
     * todo refactor this class and method to UntappdAPI
     *
     * @param string $beerName
     * @param string $breweryName
     *
     * @return array|null
     * @throws \GuzzleHttp\Exception\GuzzleException|\JsonException
     */
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

        $response = \json_decode( $request->getBody()->getContents(), true, 512, \JSON_THROW_ON_ERROR );

        $beerCount = $response['response']['beers']['count'] ?? 0;
        if ( empty( $response ) || $beerCount !== 1 ) {
            return null;
        }

        return [
            'beerId' => $response['response']['beers']['items'][0]['beer']['bid'] ?? null,
            'beerAbv' => $response['response']['beers']['items'][0]['beer']['beer_abv'] ?? null,
            'beerIbu' => $response['response']['beers']['items'][0]['beer']['beer_ibu'] ?? null,
            'beerDescription' => $response['response']['beers']['items'][0]['beer']['beer_description'] ?? null,
            'checkinCount' => $response['response']['beers']['items'][0]['checkin_count'] ?? null,
            'beerStyle' => $response['response']['beers']['items'][0]['beer']['beer_style'] ?? null,
            'inProduction' => $response['response']['beers']['items'][0]['beer']['in_production'] ?? null,
        ];
    }

    private function buildSearchPhrase( string $breweryName, string $beerName ): string
    {
        return \str_replace( ' ', '+', $breweryName ) . '+' . \str_replace( ' ', '+', $beerName );
    }

    public function add( array $beerData ): void
    {
        $rule = ':: Latin-ASCII; :: NFD; :: [:Nonspacing Mark:] Remove; :: Lower(); :: NFC;';
        $i18n = Transliterator::createFromRules( $rule, Transliterator::FORWARD );
        $data = null;
        foreach ( $beerData as $index => $beer ) {
            $breweryName = $i18n->transliterate( $beer['subtitle'] );
            $beerName = \preg_replace( '/[^A-Za-z0-9_ ]/', '', $i18n->transliterate( $beer['title'] ) );
            $data[] = [
                'beer_name' => \str_replace( '  ', ' ', $beerName ),
                'brewery_name' => \str_replace( '  ', ' ', $breweryName ),
            ];
        }

        if ($data === null) {
            return;
        }

        try {
            DB::table( 'untappd' )
                ->insertOrIgnore( $data );
        } catch ( \Exception $ex ) {

        }
    }
}
