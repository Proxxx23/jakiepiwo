<?php
declare( strict_types=1 );

namespace App\Http\Services;

use App\Http\Repositories\ResultsRepositoryInterface;
use App\Http\Utils\SharedCacheInterface;

final class SimpleResultsService
{
    private const RESULTS_TTL = 900;

    private ResultsRepositoryInterface $repository;
    private SharedCacheInterface $cache;

    public function __construct( ResultsRepositoryInterface $repository, SharedCacheInterface $cache )
    {
        $this->repository = $repository;
        $this->cache = $cache;
    }

    public function getResultsByResultsHash( string $resultsHash ): ?string
    {
        $cacheKey = 'RESULTS_' . $resultsHash;
        $cachedResults = $this->cache->get( $cacheKey );

        if ( $cachedResults === null ) {
            $results = $this->repository->fetchByResultsHash( $resultsHash );
            $this->cache->set( $cacheKey, $results, self::RESULTS_TTL );

            return $results;
        }

        return $cachedResults;
    }
}
