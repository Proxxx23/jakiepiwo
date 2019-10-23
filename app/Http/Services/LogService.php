<?php
declare( strict_types=1 );

namespace App\Http\Services;

use Illuminate\Support\Facades\DB;

final class LogService
{
    /**
     * @param string $message
     * TODO: Repo
     */
    public static function logError( string $message ): void
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
