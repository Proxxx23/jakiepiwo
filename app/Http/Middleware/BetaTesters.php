<?php

namespace App\Http\Middleware;

use Closure;

class BetaTesters
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    private const BETA_IP = array(''); // adresy IP betatesterów

    private $DEV = false;

    public function handle($request, Closure $next)
    {

        if ($_SERVER['REMOTE_ADDR'] === '89.64.49.156' || $_SERVER['REMOTE_ADDR'] === '127.0.0.1') {
            $this->DEV = true;
        } elseif (in_array($_SERVER['REMOTE_ADDR'], self::BETA_IP)) {
            $this->DEV = true;
        }
        
        if (!$this->DEV) {
            die('Brak uprawnień!');
        }
        

        return $next($request);
    }
}
