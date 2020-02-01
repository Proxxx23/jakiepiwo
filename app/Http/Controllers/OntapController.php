<?php
declare( strict_types=1 );

namespace App\Http\Controllers;

use App\Http\Objects\ValueObject\Coordinates;
use App\Http\Repositories\OnTapRepository;
use App\Http\Services\OntapService;
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
     *
     * @return JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function handle( Request $request ): Response
    {
        $payload = $request->input();
        $coordinates = new Coordinates( $payload['lng'], $payload['lat'] );
        if ( !$coordinates->isValid() ) {
            return \response()->json(
                [
                    'message' => 'Invalid coordinated format',
                ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        $cacheKeys = $payload['cacheKeys'];
        if ( empty( $cacheKeys ) ) {
            return \response()->json(
                [
                    'message' => 'No cache keys provided',
                ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        $cityName = 'Gdańsk'; //todo: get by lat/lng
        if ( $cityName === null ) {
            return \response()->json(
                [
                    'message' => 'Could not determine city name',
                ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        $cache = new FilesystemAdapter( '', self::DEFAULT_CACHE_TIME );
        $ontapService = new OntapService(
            new OnTapRepository( new Client(), $cache, $cityName )
        );

        $styles = [];
        foreach ( $cacheKeys as $key ) {
            $item = $cache->getItem( $key );
            $styles[] = $item !== null && $item->isHit()
                ? $item->get()
                : null;
        }

        if ( $styles === [] ) {
            return \response()->json(
                [
                    'message' => 'No styles found in given city.',
                ], JsonResponse::HTTP_NO_CONTENT
            );
        }

        $data = [];
        foreach ( $styles as $style ) {
            foreach ( $style as $item ) { //todo: one foreach
                $ontapBeer = $ontapService->get( $item['title'] );
                if ( $ontapBeer !== null ) {
                    $data[] = $ontapBeer;
                }
            }
        }

        if ( $data === [] ) {
            return \response()->json(
                [
                    'message' => 'No beers found in given city.',
                ], JsonResponse::HTTP_NO_CONTENT
            );
        }

        return \response()->json( [ 'data' => $data ] );
    }
}
