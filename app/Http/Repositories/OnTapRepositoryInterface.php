<?php
declare(strict_types=1);

namespace App\Http\Repositories;

interface OnTapRepositoryInterface
{
    public function fetchTapsByBeerName( string $beerName ): ?array;
    public function connected(): bool;
    public function placesFound(): bool;
}