<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

use Illuminate\Support\Facades\DB;

class StylesLogsRepository implements StylesLogsRepositoryInterface
{
    /**
     * @param string $ipAddress
     *
     * @return string|null
     * TODO: refactor
     */
    public function fetchUsername( string $ipAddress ): ?string
    {
        $lastVisit = DB::select(
            'SELECT 
              `username` 
              FROM 
              `styles_logs` 
              WHERE 
              `ip_address` = "' . $_SERVER['REMOTE_ADDR'] . '" AND 
              `username` != "" 
              ORDER BY 
              `created_at` 
              DESC 
              LIMIT 1'
        );

        if ( $lastVisit ) {
            $v = \get_object_vars( $lastVisit[0] );
            return $v['username'];
        }

        return null;
    }
}
