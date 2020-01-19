<?php
declare( strict_types=1 );

namespace App\Http\Controllers;

use App\Http\Repositories\OnTapRepository;
use App\Http\Services\OntapService;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

final class OntapController
{
    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle( Request $request ): JsonResponse
    {
        $payload = $request->input(); //todo: validate + command/object
        $httpClient = new Client();

        $ontapService = new OntapService(
            new OnTapRepository( $httpClient, new FilesystemAdapter( '', 1800 ), $payload['city'] ?? null )
        );

        $data = [];
        foreach ( $payload['styles'] as $style ) {
            foreach ( $style as $beerName ) { //todo: one foreach
                $ontapBeer = $ontapService->get( $beerName );
                if ( $ontapBeer !== null ) {
                    $data[] = $ontapBeer;
                }
            }
        }

        return response()->json( [ 'data' => $data ] );
    }
}
