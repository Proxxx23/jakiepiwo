<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

use App\Http\Objects\FormData;
use Illuminate\Support\Facades\DB;

final class StylesLogsRepository implements StylesLogsRepositoryInterface
{
    /**
     * @param string $ipAddress
     *
     * @return string|null
     * TODO: refactor
     */
    public function fetchUsernameByIpAddress( string $ipAddress ): ?string
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

    /**
     * @param FormData $user
     * @param array|null $styleToTake
     * @param array|null $styleToAvoid
     */
    public function logStyles( FormData $user, ?array $styleToTake, ?array $styleToAvoid ): void
    {
        $lastID = DB::select( 'SELECT MAX(id_answer) AS lastid FROM `styles_logs` LIMIT 1' );
        $nextID = (int) $lastID[0]->lastid + 1;

        $insertsCount = $styleToTake !== null ? \count( $styleToTake ) : \count($styleToAvoid) ?? null;
        if ($insertsCount === null || $insertsCount === 0) {
            return;
        }

        for ( $i = 0; $i < $insertsCount; $i++ ) {
            DB::insert(
                'INSERT INTO `styles_logs` 
                          (id_answer, 
                           username, 
                           email, 
                           newsletter, 
                           style_take, 
                           style_avoid, 
                           ip_address, 
                           created_at)
    					VALUES
    					(?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $nextID,
                    $user->getUsername(),
                    $user->getEmail(),
                    $user->addToNewsletterList(),
                    $styleToTake[$i] ?? null,
                    $styleToAvoid[$i] ?? null,
                    $_SERVER['REMOTE_ADDR'],
                    now(),
                ]
            );
        }
    }
}
