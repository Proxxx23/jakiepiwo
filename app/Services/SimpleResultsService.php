<?php
declare( strict_types=1 );

namespace App\Services;

use App\Http\Repositories\ResultsRepositoryInterface;
use App\Utils\SharedCacheInterface;

final class SimpleResultsService
{
    public const RESULTS_CACHE_KEY_PREFIX = 'RESULTS_';

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
        $cacheKey = self::RESULTS_CACHE_KEY_PREFIX . $resultsHash;
        $cachedResults = $this->cache->get( $cacheKey );
        if ( $cachedResults !== null ) {
            return $cachedResults;
        }

        $results = $this->repository->fetchByResultsHash( $resultsHash );
        $this->cache->set( $cacheKey, $results, self::RESULTS_TTL );

        return $results;
    }
}
