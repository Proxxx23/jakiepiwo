<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

interface QuestionsRepositoryInterface
{
    public function fetchAllQuestions(): array;
}
