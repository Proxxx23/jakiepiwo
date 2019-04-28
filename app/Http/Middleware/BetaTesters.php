<?php
declare( strict_types=1 );

namespace App\Http\Middleware;

use Closure;

/**
 * Class BetaTesters
 * @package App\Http\Middleware
 */
class BetaTesters
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */

    // private const BETA_IP = array('217.173.4.20', '87.207.174.210', '89.65.74.223', '31.178.112.4', '37.47.184.249', '62.61.61.40', '94.42.123.186', '89.71.154.241', '87.207.217.42', '89.67.40.6', '31.0.35.136', '79.191.70.105'); // adresy IP betatesterów

    /**
     * @param $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle( $request, Closure $next )
    {
        if ( $_GET['beta'] !== 'dev' ) {
            die( 'Brak uprawnień!' );
        }

        // if ($_SERVER['REMOTE_ADDR'] === '89.64.48.232' || $_SERVER['REMOTE_ADDR'] === '127.0.0.1') {
        //     $this->DEV = true;
        // } elseif (in_array($_SERVER['REMOTE_ADDR'], self::BETA_IP)) {
        //     $this->DEV = true;
        // }

        // if (!$this->DEV) {
        //     die('Brak uprawnień!');
        // }

        return $next( $request );
    }
}
