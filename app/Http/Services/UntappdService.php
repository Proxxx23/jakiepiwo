<?php
declare( strict_types=1 );

namespace App\Http\Services;

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
            ->where( 'next_update', '=', '0000-00-00 00:00' )
            ->limit( self::DAILY_LIMIT )
            ->get();

        self::$remainingLimit -= $newRecords->count();

        $recordsToUpdate = [];
        if ( self::$remainingLimit > 0 ) {
            $time = Carbon::now()
                ->format( 'Y-m-d H:i:s' );
            $recordsToUpdate = DB::table( 'untappd' )
                ->select( 'id', 'beer_name', 'brewery_name' )
                ->where( 'next_update', '<>', '0000-00-00 00:00' )
                ->where( 'next_update', '<', $time )
                ->where( 'beer_id', '<>', 'null' )
                ->orderBy( 'next_update', 'desc' )
                ->limit( self::$remainingLimit )
                ->get();
        }

        $records = \array_merge( $newRecords->toArray(), $recordsToUpdate->toArray() );
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
