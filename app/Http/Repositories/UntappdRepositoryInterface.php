<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

interface UntappdRepositoryInterface
{
    public function fetchOne( string $beerName, string $breweryName ): ?array;

    public function add( array $beerData ): void;
}
