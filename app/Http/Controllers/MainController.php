<?php
declare( strict_types=1 );

namespace App\Http\Controllers;

use App\Http\Repositories\QuestionsRepository;
use App\Http\Services\QuestionsService;
use App\Http\Services\UserService;
use Illuminate\Http\JsonResponse;

class MainController
{
    /**
     * @return JsonResponse
     */
    public function questionsData(): JsonResponse
    {
        $questionsService = new QuestionsService( new QuestionsRepository() );

        return response()->json(
            $questionsService->getQuestions(), 200, [], JSON_UNESCAPED_UNICODE
        );
    }

    /**
     * @return JsonResponse
     */
    public function visitorData(): JsonResponse
    {
        $userService = new UserService();

        return response()->json(
            [ 'visitorName' => $userService->getUsername() ]
        );
    }
}
