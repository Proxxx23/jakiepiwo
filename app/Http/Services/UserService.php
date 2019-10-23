<?php
declare( strict_types=1 );

namespace App\Http\Services;

use Illuminate\Support\Facades\DB;

final class UserService
{
    /**
     * Gets name of an user using IP address
     * TODO: Repo
     */
    public function getUsername(): ?string
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
