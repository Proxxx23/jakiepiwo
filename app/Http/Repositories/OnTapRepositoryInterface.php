<?php
declare(strict_types=1);

namespace App\Http\Repositories;

use App\Http\Objects\PolskiKraftData;

interface OnTapRepositoryInterface
{
    public function fetchTapsByBeerData( PolskiKraftData $beerData ): ?array;
    public function connected(): bool;
    public function placesFound(): bool;
}