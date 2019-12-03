<?php
declare(strict_types=1);

namespace App\Http\Repositories;

interface OnTapRepositoryInterface
{
    public function fetchCityIdByName( string $cityName ): array;

    public function fetchPlacesByCityId(): ?array;

    public function fetchTapsByPlaceId( string $placeId ): ?array;
}