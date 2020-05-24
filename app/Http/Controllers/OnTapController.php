<?php
declare( strict_types=1 );

namespace App\Http\Controllers;

use App\Http\Objects\ValueObject\Coordinates;
use App\Http\Services\SimpleResultsService;
use App\Http\Utils\SharedCache;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class OnTapController
{
    /**
     * @param Request $request
     * @param SharedCache $cache
     *
     * @return Response
     */
    public function handle( Request $request, SharedCache $cache ): Response
    {
        $payload = $request->input();

        $resultsHash = $payload['resultsHash'];
        if ( empty( $resultsHash ) ) {
            return \response( 'No results hash provided.', Response::HTTP_BAD_REQUEST );
        }

        $coordinates = new Coordinates(
            $payload['userLocation']['latitude'],
            $payload['userLocation']['longitude']
        );
        if ( !$coordinates->isValid() ) {
            return \response( 'Invalid coordinates format or empty coordinates.', Response::HTTP_BAD_REQUEST );
        }

        $ontapService = \resolve( 'OnTapService' );
        if ( $ontapService->connectionRefused() ) {
            return \response( 'Could not connect to OnTap API - connection refused.', Response::HTTP_SERVICE_UNAVAILABLE );
        }

        $cities = $ontapService->getCitiesByCoordinates( $coordinates );
        if ( empty( $cities ) ) {
            return \response( 'Could not determine user locale (city, nearby cities or voivodeship).', Response::HTTP_NO_CONTENT );
        }

        $ontapService->setOnTapCities( $cities );
        $cacheKey = SimpleResultsService::RESULTS_CACHE_KEY_PREFIX . $resultsHash;
        $cachedData = $cache->get( $cacheKey );

        $resulsJson = ( $cachedData !== null )
            ? \json_decode( $cachedData, true )
            : \json_decode(
                \resolve( 'SimpleResultsService' )
                    ->getResultsByResultsHash( $resultsHash ), true
            ); // first we ask DB, then cache same as above

        $styles = $resulsJson['buyThis'] ?? null;
        if ( $styles === null ) {
            return \response( 'Could not obtain beer data by results hash', Response::HTTP_NOT_FOUND );
        }

        $data = null;
        foreach ( $styles as $style ) {
            foreach ( $style['beerDataCollection'] as $item ) {
                $ontapBeer = $ontapService->getTapsByBeerName( $item );
                if ( $ontapBeer !== null ) {
                    $data[] = $ontapBeer;
                }
            }
        }

        if ( $data === null ) {
            return \response()->json( 'No beers found in given city.', Response::HTTP_NO_CONTENT );
        }

        return \response()->json( [ 'data' => $data ] );
    }
}
