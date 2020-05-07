<?php
declare( strict_types=1 );

namespace App\Http\Services;

use App\Http\Objects\ValueObject\Coordinates;
use App\Http\Repositories\GeolocationRepositoryInterface;
use App\Http\Repositories\OnTapRepositoryInterface;

final class OnTapService
{
    private OnTapRepositoryInterface $onTapRepository;
    private GeolocationRepositoryInterface $geolocationRepository;
    private bool $connectionRefused;

    public function __construct(
        OnTapRepositoryInterface $onTapRepository,
        GeolocationRepositoryInterface $geolocationRepository
    ) {
        $this->onTapRepository = $onTapRepository;
        $this->connectionRefused = $onTapRepository->connectionRefused();
        if ( $onTapRepository->connectionRefused() ) {
            return; // we don't want to go further if connection refused
        }
        $this->geolocationRepository = $geolocationRepository;
        $this->geolocationRepository->setCitiesList( $this->onTapRepository->fetchAllCities() ); //todo bardzo nieładnie
    }

    public function connectionRefused(): bool
    {
        return $this->connectionRefused;
    }

    public function getTapsByBeerName( array $beerData ): ?array
    {
        return $this->onTapRepository->fetchTapsByBeerName( $beerData );
    }

    public function getCityByCoordinates( Coordinates $coordinates ): ?string
    {
        return $this->geolocationRepository->fetchCityByCoordinates( $coordinates );
    }

    public function setOnTapCityName( string $cityName ): void
    {
        $this->onTapRepository->setCityName( $cityName ); //todo: Lukasz, chryste Panie, strzeż się boga...
    }
}
