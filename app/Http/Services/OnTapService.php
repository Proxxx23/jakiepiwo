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
    private bool $connectionNotRefused;

    public function __construct( OnTapRepositoryInterface $onTapRepository, GeolocationRepositoryInterface $geolocationRepository )
    {
        $this->onTapRepository = $onTapRepository;
        $this->connectionNotRefused = $onTapRepository->connectionNotRefused();
        // todo jak refused to nie pakować się w to dalej
        $this->geolocationRepository = $geolocationRepository;
        $this->geolocationRepository->setCitiesList( $this->onTapRepository->fetchAllCities() ); //todo bardzo nieładnie
    }

    public function getTapsByBeerName( string $beerName ): ?array
    {
        if ( $this->connectionNotRefused ) {
            return $this->onTapRepository->fetchTapsByBeerName( $beerName );
        }

        return null;
    }

    //todo tests
    public function getCityByCoordinates( Coordinates $coordinates ): ?string
    {
        return $this->geolocationRepository->fetchCityByCoordinates( $coordinates );
    }

    public function setOnTapCityName( string $cityName ): void
    {
        $this->onTapRepository->setCityName( $cityName ); //todo: Lukasz, chryste Panie, strzeż się boga...
    }
}
