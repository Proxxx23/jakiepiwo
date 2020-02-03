<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

use App\Http\Objects\BeerData;
use App\Http\Objects\FormData;

interface UserAnswersRepositoryInterface
{
    public function addAnswers( FormData $formInput, array $answers, BeerData $results ): void;
}
