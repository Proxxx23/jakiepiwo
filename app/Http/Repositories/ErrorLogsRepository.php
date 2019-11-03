<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

use Illuminate\Support\Facades\DB;

class ErrorLogsRepository implements ErrorLogsRepositoryInterface
{
    /**
     * @param string $message
     */
    public function log( string $message ): void
    {
        DB::insert(
            'INSERT INTO error_logs (error, created_at) VALUES (:error, :created_at)',
            [
                'error' => $message,
                'created_at' => now(),
            ]
        );
    }
}
