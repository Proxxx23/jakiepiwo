<?php
declare( strict_types=1 );

namespace App\Http\Services;

use App\Http\Repositories\UntappdRepositoryInterface;
use Illuminate\Support\Facades\DB;

final class UntappdService
{
    private static int $remainingLimit = self::DAILY_LIMIT;
    private const DAILY_LIMIT = 2;
    private const NEXT_UPDATE_IN_SECONDS = 2678400; // 1 month

    private UntappdRepositoryInterface $untappdRepository;

    public function __construct( UntappdRepositoryInterface $untappdRepository )
    {
        $this->untappdRepository = $untappdRepository;
    }

    public function process(): void
    {
        $newRecords = DB::table( 'untappd' )
            ->select( 'beer_name', 'brewery_name' )
            ->where( 'next_update', '=', '0000-00-00 00:00' )
            ->limit( self::DAILY_LIMIT )
            ->get()
            ->toArray();

        self::$remainingLimit -= \count( $newRecords );

        $recordsToUpdate = DB::table( 'untappd' )
            ->select( 'beer_name', 'brewery_name' )
            ->where( 'next_update', '<>', '0000-00-00 00:00' )
            ->orderBy( 'next_update', 'desc' )
            ->limit( self::$remainingLimit )
            ->get()
            ->toArray();

        $records = \array_merge( $newRecords, $recordsToUpdate );
        if ( \count($records) === 0 ) {
            return;
        }

        foreach ( $records as $record ) {
            $beerInfo = $this->untappdRepository->fetchOne( $record['beer_name'], $record['brewery_name'] );
            if ( $beerInfo === null || $beerInfo === [] ) {
                continue;
            }

            DB::table( 'untappd' )
                ->updateOrInsert(
                    [
                        'beer_name' => $record['beerName'],
                        'brewery_name' => $record['breweryName'],
                    ],
                    [
                        'beer_id' => $beerInfo['beerId'],
                        'beer_abv' => $beerInfo['beerAbv'],
                        'beer_ibu' => $beerInfo['beerIbu'],
                        'beer_description' => $beerInfo['beerDescription'],
                        'beer_style' => $beerInfo['beerStyle'],
                        'checkin_count' => $beerInfo['checkingCount'],
                        'in_production' => $beerInfo['inProduction'],
                        'updated_at' => \time(),
                        'next_update' => \time() + self::NEXT_UPDATE_IN_SECONDS,
                    ],
                );
        }
    }

}
