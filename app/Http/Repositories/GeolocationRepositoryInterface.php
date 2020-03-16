<?php
declare(strict_types=1);

namespace App\Http\Repositories;

use App\Http\Objects\ValueObject\Coordinates;

interface GeolocationRepositoryInterface
{
    public function setCitiesList( array $citiesList ): void;
    public function fetchCityByCoordinates( Coordinates $coordinates ): ?string;
}