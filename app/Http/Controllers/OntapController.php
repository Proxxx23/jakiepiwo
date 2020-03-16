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
    private const DEFAULT_ONTAP_TIMEOUT = 10; // in seconds
    private const DEFAULT_GEOLOCATION_TIMEOUT = 2; // in seconds

    /**
     * @param Request $request
     * @return Response
     * @throws \GuzzleHttp\Exception\GuzzleException|\Psr\Cache\InvalidArgumentException
     */
    public function handle( Request $request ): Response
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

        $coordinates = new Coordinates( $payload['userLocation']['latitude'], $payload['userLocation']['longitude'] );
        if ( !$coordinates->isValid() ) {
            return \response()->json(
                [
                    'message' => 'Invalid coordinates format or empty coordinates.',
                ], JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $cache = new FilesystemAdapter( '', self::DEFAULT_CACHE_TIME );

        $OntapConfig = ['timeout' => self::DEFAULT_ONTAP_TIMEOUT];
        $GeolocationConfig = ['timeout' => self::DEFAULT_GEOLOCATION_TIMEOUT];

        $ontapService = new OnTapService(
            new OnTapRepository( new Client( $OntapConfig ), $cache ), //todo: httpClient wpuszczać raz
            new GeolocationRepository( new Client( $GeolocationConfig ) )
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
            foreach ( $style as $item ) {
                $ontapBeer = $ontapService->getTapsByBeerName( $item['title'] );
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
