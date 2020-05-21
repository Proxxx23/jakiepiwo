<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

interface OnTapRepositoryInterface
{
    public function setCities( array $cities ): void;
    public function fetchTapsByBeerName( array $beerData ): ?array;
    public function fetchAllCities(): array;
    public function connectionRefused(): bool;
}
