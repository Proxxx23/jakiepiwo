<?php
declare(strict_types=1);

namespace App\Http\Repositories;

use App\Http\Objects\PolskiKraftData;

interface OnTapRepositoryInterface
{
    /**
     * @param PolskiKraftData $beerData
     * @return array|null
     */
    public function fetchTapsByBeerData( PolskiKraftData $beerData ): ?array;
}