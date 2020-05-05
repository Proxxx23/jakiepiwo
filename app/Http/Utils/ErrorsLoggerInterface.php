<?php
declare( strict_types=1 );

namespace App\Http\Utils;

interface ErrorsLoggerInterface
{
    public function logError( string $message ): void;
}
