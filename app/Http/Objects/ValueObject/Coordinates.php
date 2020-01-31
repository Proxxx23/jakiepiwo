<?php
declare( strict_types=1 );

namespace App\Http\Objects\ValueObject;

final class Coordinates
{
    private string $lat;
    private string $lng;

    public function __construct( string $lat, string $lng )
    {
        $this->lat = $lat;
        $this->lng = $lng;
    }

    public function getLat(): string
    {
        return $this->lat;
    }

    public function getLng(): string
    {
        return $this->lng;
    }

    public function isValid(): bool
    {
        $regexp = '/^\d{2}\.\d+$/';
        if ( !\preg_match( $regexp, $this->lat ) ) {
            return false;
        }

        if ( !\preg_match( $regexp, $this->lng ) ) {
            return false;
        }

        return true;
    }

}
