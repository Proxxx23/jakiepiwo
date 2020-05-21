<?php
declare( strict_types=1 );

namespace App\Http\Controllers;

use App\Http\Objects\ValueObject\Coordinates;
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

        $cacheKeys = $payload['cacheKeys'];
        if ( empty( $cacheKeys ) ) {
            return \response( 'No cache keys provided.', Response::HTTP_BAD_REQUEST );
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
            return \response( 'Could not determine city name.', Response::HTTP_NO_CONTENT );
        }

        $ontapService->setOnTapCities( $cities );
        $styles = null;
        foreach ( $cacheKeys as $key ) {
            $item = $cache->get( $key ); // todo: we now holds it in cache for 7 days - we should write it to DB forever
            if ( $item !== null ) {
                $styles[] = $item;
                continue;
            }
        }

        if ( $styles === null ) {
            return \response( 'No styles found in cache.', Response::HTTP_NOT_FOUND );
        }

        $data = null;
        foreach ( $styles as $style ) {
            foreach ( $style as $item ) {
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
