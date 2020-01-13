<?php

namespace App\Http\Services;

use App\Http\Repositories\OnTapRepositoryInterface;

final class OntapService
{
    private OnTapRepositoryInterface $repository;

    public function __construct( OnTapRepositoryInterface $onTapRepository )
    {
        $this->repository = $onTapRepository;
    }

    public function get( string $beerName ): ?array
    {
        if ( $this->repository->connected() && $this->repository->placesFound() ) {
            return $this->repository->fetchTapsByBeerName( $beerName );
        }

        return null;
    }
}