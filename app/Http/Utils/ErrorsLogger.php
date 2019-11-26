<?php
declare( strict_types=1 );

namespace App\Http\Utils;

use App\Http\Repositories\ErrorLogsRepositoryInterface;

final class ErrorsLogger implements ErrorsLoggerInterface
{
    /** @var ErrorLogsRepositoryInterface */
    private $errorLogsRepository;

    /**
     * @param ErrorLogsRepositoryInterface $errorLogsRepository
     */
    public function __construct( ErrorLogsRepositoryInterface $errorLogsRepository )
    {
        $this->errorLogsRepository = $errorLogsRepository;
    }

    /**
     * @param string $message
     */
    public function logError( string $message ): void
    {
        $this->errorLogsRepository->log($message);
    }
}
