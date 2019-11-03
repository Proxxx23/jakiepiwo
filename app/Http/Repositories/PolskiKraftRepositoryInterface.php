<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

use App\Http\Objects\BeerDataCollection;

interface PolskiKraftRepositoryInterface
{
    public function fetchByBeerId( int $beerId ): ?BeerDataCollection;
}
