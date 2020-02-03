<?php
declare( strict_types=1 );

namespace App\Http\Services;

use App\Http\Objects\FormData;
use App\Http\Repositories\UserAnswersRepositoryInterface;

final class AnswersLoggerService
{
    /** @var UserAnswersRepositoryInterface */
    private UserAnswersRepositoryInterface $userAnswersRepository;

    public function __construct( UserAnswersRepositoryInterface $userAnswersRepository )
    {
        $this->userAnswersRepository = $userAnswersRepository;
    }

    public function logAnswers( FormData $formInput, array $answers, array $results ): void
    {
        $this->userAnswersRepository->addAnswers( $formInput, $answers, $results );
    }

}
