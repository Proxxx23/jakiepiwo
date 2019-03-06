<?php
declare( strict_types=1 );

namespace App\Http\Objects;

use App\Exceptions\SanityException;
use App\Exceptions\UnsupportedOperationException;

abstract class AbstractPropertyObject
{
    public const DECAPSULATED_ARRAY_FIELD_PREFIX = "\0*\0";

    /**
     * @param string $name
     */
    abstract protected function checkIfPropertyExists( string $name ): void;

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function __get( string $name )
    {
        $this->checkIfPropertyExists( $name );

        return $this->$name ?? null; // note, the null value will be returned only for dynamic objects
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function __isset( string $name ): bool
    {
        return isset( $this->$name );
    }

    /**
     * @param string $name
     * @param $value
     *
     * @return self
     */
    public function __set( string $name, $value ): self
    {
        $this->checkIfPropertyExists( $name );

        $this->$name = $value;
        return $this;
    }

    /**
     * @param $name
     */
    abstract public function __unset( string $name );

    /**
     * @param string $name
     * @param array $args
     *
     * @return mixed
     */
    public function __call( string $name, array $args )
    {
        $parts = [];
        /** @noinspection NotOptimalRegularExpressionsInspection */
        if ( !\preg_match( '/^(set|get)([A-Z_][A-Za-z0-9_]*)/', $name, $parts ) ) {
            throw new UnsupportedOperationException( 'method "' . $name . '" not present in object' );
        }

        $argsCount = \count( $args );

        $propertyName = \lcfirst( $parts[2] );

        $this->checkIfPropertyExists( $propertyName );

        switch ( $parts[1] ) {
            case 'get':
                if ( $argsCount !== 0 ) {
                    throw new UnsupportedOperationException(
                        'magic getter "' . $name . '" does not take arguments'
                    );
                }
                return $this->__get( $propertyName );
            case 'set':
                if ( $argsCount !== 1 ) {
                    throw new UnsupportedOperationException(
                        'magic setter "' . $name . '" requires a single argument'
                    );
                }
                $this->__set( $propertyName, $args[0] );
                return $this;
        }

        throw new SanityException('This should not happen');
    }
}
