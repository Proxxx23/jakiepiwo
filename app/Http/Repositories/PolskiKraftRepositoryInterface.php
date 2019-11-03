<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

use App\Http\Objects\PolskiKraftBeerDataCollection;

interface PolskiKraftRepositoryInterface
{
    public function fetchByBeerId( int $beerId ): ?PolskiKraftBeerDataCollection;
}
