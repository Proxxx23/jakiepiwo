<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

use Illuminate\Support\Facades\DB;

final class ResultsRepository implements ResultsRepositoryInterface
{
    public function fetchByResultsHash( string $resultsHash ): ?string
    {
        $results = DB::table( 'user_answers' )
            ->select( 'results' )
            ->where( 'results_hash', '=', $resultsHash )
            ->get();

        return $results->isNotEmpty()
            ? $results[0]->results
            : null;
    }
}
