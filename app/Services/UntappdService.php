<?php
declare( strict_types=1 );

namespace App\Services;

use App\Http\Repositories\UntappdRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

final class UntappdService
{
    private static int $remainingLimit = self::DAILY_LIMIT;
    private const DAILY_LIMIT = 100;

    private UntappdRepositoryInterface $untappdRepository;

    public function __construct( UntappdRepositoryInterface $untappdRepository )
    {
        $this->untappdRepository = $untappdRepository;
    }

    public function process(): void
    {
        $newRecords = DB::table( 'untappd' )
            ->select( 'id', 'beer_name', 'brewery_name' )
            ->whereNull( 'next_update' )
            ->limit( self::DAILY_LIMIT )
            ->get()
            ->toArray();

        self::$remainingLimit -= \count( $newRecords );

        $recordsToUpdate = [];
        if ( self::$remainingLimit > 0 ) {
            $time = Carbon::now()
                ->format( 'Y-m-d H:i:s' );
            $recordsToUpdate = DB::table( 'untappd' )
                ->select( [ 'id', 'beer_name', 'brewery_name' ] )
                ->whereNotNull( 'next_update' )
                ->where( 'next_update', '<', $time )
                ->where( 'beer_id', '<>', 'null' )
                ->orderBy( 'next_update', 'desc' )
                ->limit( self::$remainingLimit )
                ->get()
                ->toArray();
        }

        $records = \array_merge( $newRecords, $recordsToUpdate );
        if ( \count( $records ) === 0 ) {
            return;
        }

        foreach ( $records as $record ) {

            $beerName = $record->beer_name;
            $breweryName = $record->brewery_name;
            $beerInfo = $this->untappdRepository->fetchOne( $beerName, $breweryName );
            if ( $beerInfo === null || $beerInfo === [] ) {
                continue;
            }

            DB::table( 'untappd' )
                ->updateOrInsert(
                    [
                        'beer_name' => $beerName,
                        'brewery_name' => $breweryName,
                    ],
                    [
                        'beer_id' => $beerInfo['beerId'],
                        'beer_abv' => $beerInfo['beerAbv'],
                        'beer_ibu' => $beerInfo['beerIbu'],
                        'beer_description' => $beerInfo['beerDescription'],
                        'beer_style' => $beerInfo['beerStyle'],
                        'checkin_count' => $beerInfo['checkinCount'],
                        'in_production' => $beerInfo['inProduction'],
                        'updated_at' => Carbon::now()
                            ->format( 'Y-m-d H:i:s' ),
                        'next_update' => Carbon::now()
                            ->addMonth()
                            ->format( 'Y-m-d H:i:s' ),
                    ],
                );
        }
    }

}
