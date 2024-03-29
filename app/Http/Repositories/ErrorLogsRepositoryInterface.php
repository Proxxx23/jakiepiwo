<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

interface ErrorLogsRepositoryInterface
{
    public function log( string $errorMessage ): void;
}
