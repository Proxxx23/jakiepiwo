<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

use Illuminate\Support\Facades\DB;

final class ResultsRepository implements ResultsRepositoryInterface
{
    public function fetchByResultsHash( string $resultsHash ): string
    {
        $results = DB::select( "SELECT results FROM user_answers WHERE results_hash = '" . $resultsHash . "'" );

        return $results[0]->results;
    }
}
