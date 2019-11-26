<?php
declare(strict_types=1);

namespace App\Http\Utils;

interface ErrorsLoggerInterface
{
    /**
     * @param string $message
     */
    public function logError( string $message ): void;
}