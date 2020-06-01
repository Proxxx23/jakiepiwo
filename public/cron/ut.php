<?php
declare( strict_types=1 );

use App\Http\Services\UntappdService;

/** @var UntappdService $service */
$service = \resolve( 'UntappdService' );
$service->process();
