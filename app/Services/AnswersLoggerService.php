<?php
declare( strict_types=1 );

namespace App\Services;

use App\Http\Objects\BeerData;
use App\Http\Objects\FormData;
use App\Http\Repositories\UserAnswersRepositoryInterface;

final readonly class AnswersLoggerService
{
    public function __construct( private UserAnswersRepositoryInterface $userAnswersRepository )
    {
    }

    public function logAnswers( FormData $formInput, array $answers, BeerData $results ): void
    {
        $this->userAnswersRepository->addAnswers( $formInput, $answers, $results );
    }
}
