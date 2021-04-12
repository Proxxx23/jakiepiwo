<?php
declare( strict_types=1 );

namespace App\Http\Controllers;

use App\Http\Services\UntappdService;
use Illuminate\Routing\Controller;

final class UntappdCronController extends Controller
{
    public function handle(): void
    {
        /** @var UntappdService $service */
        $service = \resolve( 'UntappdService' );
        $service->process();
    }
}
