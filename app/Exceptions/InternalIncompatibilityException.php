<?php
declare( strict_types=1 );

namespace App\Exceptions;

final class InternalIncompatibilityException extends \Exception
{
    /**
     * Constructor.
     *
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct( $message = 'Internal incompatibility exception', $code = 0, \Throwable $previous = null )
    {
        parent::__construct( $message, $code, $previous );
    }
}
