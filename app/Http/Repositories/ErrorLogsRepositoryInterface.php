<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

interface ErrorLogsRepositoryInterface
{
    /**
     * @param string $message
     */
    public function add( string $message ): void;
}
