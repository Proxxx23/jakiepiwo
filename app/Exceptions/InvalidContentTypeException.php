<?php
declare( strict_types=1 );

namespace App\Exceptions;

final class InvalidContentTypeException extends \UnexpectedValueException
{
    /**
     * @param string $message
     */
    public function __construct( string $message )
    {
        parent::__construct( $message, 422 );
    }
}
