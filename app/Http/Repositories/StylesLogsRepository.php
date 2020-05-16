<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

use App\Http\Objects\FormData;
use Illuminate\Support\Facades\DB;

final class StylesLogsRepository implements StylesLogsRepositoryInterface
{
    public function logStyles( FormData $user, ?array $recommendedIds, ?array $unsuitableIds ): void
    {
        if ( $recommendedIds === null && $unsuitableIds === null ) {
            return;
        }

        try {
            DB::table( 'styles_logs' )
                ->insert(
                    [
                        'username' => $user->getUsername(),
                        'email' => $user->getEmail(),
                        'recommended_ids' => \implode( ', ', $recommendedIds ),
                        'unsuitable_ids' => \implode( ', ', $unsuitableIds ),
                        'ip_address' => $_SERVER['REMOTE_ADDR'],
                        'created_at' => \now(),
                    ]
                );
        } catch ( \Exception $ex ) {

        }
    }
}
