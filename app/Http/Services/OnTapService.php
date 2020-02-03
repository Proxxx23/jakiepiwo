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

    public function __construct( OnTapRepositoryInterface $onTapRepository, GeolocationRepositoryInterface $geolocationRepository )
    {
        $this->onTapRepository = $onTapRepository;
        $this->geolocationRepository = $geolocationRepository;
        $this->geolocationRepository->setCitiesList( $this->getAllCities() ); //todo bardzo nieładnie
    }

    public function getTapsByBeerName( string $beerName ): ?array
    {
//        if ( $this->onTapRepository->connected() && $this->onTapRepository->placesFound() ) {
        if ( $this->onTapRepository->connected() ) {
            return $this->onTapRepository->fetchTapsByBeerName( $beerName );
        }

        return null;
    }

    public function getAllCities(): ?array
    {
//        if ( $this->onTapRepository->connected() && $this->onTapRepository->placesFound() ) {
        if ( $this->onTapRepository->connected() ) {
            return $this->onTapRepository->fetchAllCities();
        }

        return null;
    }

    //todo tests
    public function getCityByCoordinates( Coordinates $coordinates ): ?string
    {
        return $this->geolocationRepository->fetchCityByCoordinates( $coordinates );
    }
}
