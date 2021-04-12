<?php
declare( strict_types=1 );

namespace App\Utils;

use App\Http\Repositories\ErrorLogsRepositoryInterface;

final class ErrorsLogger implements ErrorsLoggerInterface
{
    private ErrorLogsRepositoryInterface $errorLogsRepository;

    public function __construct( ErrorLogsRepositoryInterface $errorLogsRepository )
    {
        $this->errorLogsRepository = $errorLogsRepository;
    }

    public function log( string $errorMessage ): void
    {
        $this->errorLogsRepository->log( $errorMessage );
    }
}
