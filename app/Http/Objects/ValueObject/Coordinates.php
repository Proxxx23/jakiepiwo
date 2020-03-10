<?php
declare( strict_types=1 );

namespace App\Http\Objects\ValueObject;

final class Coordinates
{
    private ?float $latitude;
    private ?float $longitude;

    public function __construct( ?float $lat, ?float $lng )
    {
        $this->latitude = $lat;
        $this->longitude = $lng;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function isValid(): bool
    {
        if ( $this->latitude === null || $this->longitude === null ) {
            return false;
        }

        $regexp = '/^\d{2}\.\d+$/';
        if ( !\preg_match( $regexp, (string) $this->latitude ) ) {
            return false;
        }

        if ( !\preg_match( $regexp, (string) $this->longitude ) ) {
            return false;
        }

        return true;
    }

}
