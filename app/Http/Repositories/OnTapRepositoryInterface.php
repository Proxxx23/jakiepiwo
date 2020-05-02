<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

interface OnTapRepositoryInterface
{
    public function setCityName( string $cityName ): void;
    public function fetchTapsByBeerName( string $beerName, string $breweryName ): ?array;
    public function fetchAllCities(): array;
    public function connectionRefused(): bool;
}
