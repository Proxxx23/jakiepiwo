<?php
declare( strict_types=1 );

namespace App\Http\Controllers;

use App\Http\Objects\ValueObject\Coordinates;
use App\Http\Repositories\GeolocationRepository;
use App\Http\Repositories\OnTapRepository;
use App\Http\Services\OnTapService;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Response;

final class OntapController
{
    private const DEFAULT_CACHE_TIME = 900;

    /**
     * @param Request $request
     * @return Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function handle( Request $request ): Response
    {
        $payload = $request->input();

        $cacheKeys = $payload['cacheKeys'];
        if ( empty( $cacheKeys ) ) {
            return \response()->json(
                [
                    'message' => 'No cache keys provided.',
                ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        $coordinates = new Coordinates( $payload['userLocation']['latitude'], $payload['userLocation']['longitude'] );
        if ( !$coordinates->isValid() ) {
            return \response()->json(
                [
                    'message' => 'Invalid coordinates format.',
                ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        $cache = new FilesystemAdapter( '', self::DEFAULT_CACHE_TIME );
        $httpClient = new Client();
        $ontapService = new OnTapService(
            new OnTapRepository( $httpClient, $cache ), //todo: httpClient wpuszczać raz
            new GeolocationRepository( $httpClient )
        );

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
            $item = $cache->getItem( $key );
            if ( $item !== null && $item->isHit() ) {
                $styles[] = $item->get();
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
            foreach ( $style as $item ) { //todo: one foreach
                $ontapBeer = $ontapService->getTapsByBeerName( $item['title'] );
                if ( $ontapBeer !== null ) {
                    $data[][$item['title']] = $ontapBeer;
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
