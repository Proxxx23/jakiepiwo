<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

interface ResultsRepositoryInterface
{
    public function fetchByResultsHash( string $resultsHash ): ?string;
}
