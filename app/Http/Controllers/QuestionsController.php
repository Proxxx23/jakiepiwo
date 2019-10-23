<?php
declare( strict_types=1 );

namespace App\Http\Controllers;

use App\Http\Repositories\QuestionsRepository;
use App\Http\Services\QuestionsService;
use Illuminate\Http\JsonResponse;

final class QuestionsController
{
    /**
     * @return JsonResponse
     */
    public function handle(): JsonResponse
    {
        return response()->json(
            ( new QuestionsService( new QuestionsRepository() ) )
                ->getQuestions(), 200, [], JSON_UNESCAPED_UNICODE
        );
    }
}
