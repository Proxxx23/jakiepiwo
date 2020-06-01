<?php
declare( strict_types=1 );

namespace App\Http\Services;

use App\Http\Repositories\UntappdRepositoryInterface;
use Illuminate\Support\Facades\DB;

final class UntappdService
{
    private const DAILY_LIMIT = 100;
    private const NEXT_UPDATE_IN_SECONDS = 2678400; // 1 month

    private UntappdRepositoryInterface $untappdRepository;

    public function __construct( UntappdRepositoryInterface $untappdRepository )
    {
        $this->untappdRepository = $untappdRepository;
    }

    public function process(): void
    {
        $records = DB::table( 'untappd' )
            ->select( '*' )
            ->orderBy( 'next_update', 'desc' )
            ->limit( self::DAILY_LIMIT );

        foreach ( $records as $record ) {
            $beerInfo = $this->untappdRepository->fetchOne( $record['beer_name'], $record['brewery_name'] );
            if ( $beerInfo === null || $beerInfo === [] ) {
                continue;
            }

            DB::table( 'untappd' )
                ->updateOrInsert(
                    [
                        'beer_name' => $record['beer_name'],
                        'brewery_name' => $record['brewery_name'],
                    ],
                    [
                        'in_production' => $beerInfo['inProduction'],
                        'updated_at' => \time(),
                        'next_update' => \time() + self::NEXT_UPDATE_IN_SECONDS,
                    ],
                );
        }
    }

}
