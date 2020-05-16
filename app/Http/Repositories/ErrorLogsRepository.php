<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

use Illuminate\Support\Facades\DB;

final class ErrorLogsRepository implements ErrorLogsRepositoryInterface
{
    public function log( string $errorMessage ): void
    {
        try {
            DB::table( 'error_logs' )
                ->insert(
                    [
                        'error' => $errorMessage,
                        'created_at' => \now(),
                    ]
                );
        } catch ( \Exception $exception ) {

        }
    }
}
