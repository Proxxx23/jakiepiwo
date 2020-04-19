<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

interface ScoringRepositoryInterface
{
    public function fetchByQuestionNumber( int $questionNumber ): array;
}
