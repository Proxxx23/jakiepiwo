<?php
declare( strict_types=1 );

namespace App\Http\Controllers;

use App\Http\Services\QuestionsService;
use App\Http\Services\UserService;
use Illuminate\View\View;

class MainController
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(): View
    {
        $userService = new UserService();
        $questionsService = new QuestionsService(new \QuestionsRepository());

        return view(
            'index', [
                'questions' => $questionsService->getQuestions(),
                'jsonQuestions' => $questionsService->getJsonQuestions(),
                'lastvisitName' => $userService->getUsername(),
                'errors' => null,
                'errorsCount' => 0,
            ]
        );
    }
}
