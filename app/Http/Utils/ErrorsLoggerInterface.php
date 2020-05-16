<?php
declare( strict_types=1 );

namespace App\Http\Utils;

interface ErrorsLoggerInterface
{
    public function log( string $errorMessage ): void;
}
