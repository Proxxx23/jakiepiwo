<?php
declare( strict_types=1 );

namespace App\Http\Controllers;

use App\Http\Repositories\StylesLogsRepository;
use App\Http\Services\UserService;
use Illuminate\Http\JsonResponse;

final class VisitorController
{
    /**
     * @return JsonResponse
     */
    public function handle(): JsonResponse
    {
        return response()->json(
            [ 'visitorName' => ( new UserService( new StylesLogsRepository() ) )->getUsername() ]
        );
    }
}
