<?php
declare( strict_types=1 );

namespace App\Http\Controllers;

use App\Http\Repositories\QuestionsRepository;
use App\Http\Services\QuestionsService;
use App\Http\Services\UserService;
use Illuminate\Http\JsonResponse;

final class MainController
{
    /**
     * @return JsonResponse
     */
    public function questionsData(): JsonResponse
    {
        return response()->json(
            ( new QuestionsService( new QuestionsRepository() ) )
                ->getQuestions(), 200, [], JSON_UNESCAPED_UNICODE
        );
    }

    /**
     * @return JsonResponse
     */
    public function visitorData(): JsonResponse
    {
        return response()->json(
            [ 'visitorName' => ( new UserService() )->getUsername() ]
        );
    }
}
