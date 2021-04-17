<?php
declare( strict_types=1 );

namespace App\Utils;

use App\Http\Repositories\ErrorLogsRepositoryInterface;

final class ErrorsLogger implements ErrorsLoggerInterface
{
    public function __construct( private ErrorLogsRepositoryInterface $errorLogsRepository )
    { }

    public function log( string $errorMessage ): void
    {
        $this->errorLogsRepository->log( $errorMessage );
    }
}
