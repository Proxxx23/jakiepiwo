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
    public function indexData(): JsonResponse
    {
        $userService = new UserService();
        $questionsService = new QuestionsService( new QuestionsRepository() );

        return \response()
            ->json(
                [
                    'questions' => $questionsService->getQuestions(),
                    'visitorName' => $userService->getUsername(),
                ], 200, [], JSON_UNESCAPED_UNICODE
            )
            ->setCharset( 'UTF-8' );
    }
}
