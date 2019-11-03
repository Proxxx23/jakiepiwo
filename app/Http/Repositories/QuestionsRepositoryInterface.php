<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

interface QuestionsRepositoryInterface
{
    /**
     * @return array
     */
    public function fetchAllQuestions(): array;
}
