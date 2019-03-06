<?php
declare( strict_types=1 );

namespace App\Http\Objects;

use App\Exceptions\UnsupportedOperationException;

abstract class AbstractFixedPropertyObject extends AbstractPropertyObject
{
    /**
     * @param string $name
     */
    protected function checkIfPropertyExists( string $name ): void
    {
        if ( !\array_key_exists( self::DECAPSULATED_ARRAY_FIELD_PREFIX . $name, (array) $this ) ) {
            throw new UnsupportedOperationException( 'property "' . $name . '" not present in object' );
        }
    }

    /**
     * @param $name
     */
    public function __unset( string $name )
    {
        throw new UnsupportedOperationException( 'can not unset fixed properties' );
    }
}
