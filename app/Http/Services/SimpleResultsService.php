<?php
declare( strict_types=1 );

namespace App\Http\Services;

use App\Http\Repositories\ResultsRepositoryInterface;

final class SimpleResultsService
{
    private ResultsRepositoryInterface $repository;

    public function __construct( ResultsRepositoryInterface $repository )
    {
        $this->repository = $repository;
    }

    public function getResultsByResultsHash( string $resultsHash ): ?string
    {
        return $this->repository->fetchByResultsHash( $resultsHash );
    }
}
