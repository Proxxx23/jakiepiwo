<?php
declare( strict_types=1 );

namespace App\Exceptions;

class EmptyRequestException extends \UnexpectedValueException
{
    /**
     * @param string $message
     */
    public function __construct( string $message )
    {
        parent::__construct( $message, 400 );
    }
}
