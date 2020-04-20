<?php
declare( strict_types=1 );

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

final class QuestionsController
{
    public function handle(): JsonResponse
    {
        return \response()->json(
            \resolve('QuestionsService')->getQuestions(), 200, [], JSON_UNESCAPED_UNICODE
        );
    }
}
