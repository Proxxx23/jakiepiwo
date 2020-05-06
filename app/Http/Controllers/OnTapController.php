<?php
declare( strict_types=1 );

namespace App\Http\Controllers;

use App\Http\Objects\ValueObject\Coordinates;
use App\Http\Utils\UserCache;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class OnTapController
{
    /**
     * @param Request $request
     * @param UserCache $cache
     *
     * @return Response
     */
    public function handle( Request $request, UserCache $cache ): Response
    {
        $payload = $request->input();

        $cacheKeys = $payload['cacheKeys'];
        if ( empty( $cacheKeys ) ) {
            return \response()->json(
                [
                    'message' => 'No cache keys provided.',
                ], JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $coordinates = new Coordinates(
            $payload['userLocation']['latitude'],
            $payload['userLocation']['longitude']
        );
        if ( !$coordinates->isValid() ) {
            return \response()->json(
                [
                    'message' => 'Invalid coordinates format or empty coordinates.',
                ], JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $ontapService = \resolve( 'OnTapService' );

        if ( $ontapService->connectionRefused() ) {
            \response()->json(
                [
                    'message' => 'Could not connect to OnTap API - connection refused.',
                ], JsonResponse::HTTP_SERVICE_UNAVAILABLE
            );
        }

        $cityName = $ontapService->getCityByCoordinates( $coordinates );
        if ( $cityName === null ) {
            return \response()->json(
                [
                    'message' => 'Could not determine city name.',
                ], JsonResponse::HTTP_NO_CONTENT
            );
        }

        $ontapService->setOnTapCityName( $cityName );
        $styles = null;
        foreach ( $cacheKeys as $key ) {
            $item = $cache->get( $key );
            if ( $item !== null ) {
                $styles[] = $item;
                continue;
            }
        }

        if ( $styles === null ) {
            return \response()->json(
                [
                    'message' => 'No styles found in given city.',
                ], JsonResponse::HTTP_NO_CONTENT
            );
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
            return \response()->json(
                [
                    'message' => 'No beers found in given city.',
                ], JsonResponse::HTTP_NO_CONTENT
            );
        }

        return \response()->json( [ 'data' => $data ] );
    }
}
