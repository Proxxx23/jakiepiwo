<?php
declare( strict_types=1 );

namespace App\Http\Services;

use App\Http\Objects\FormData;
use App\Http\Repositories\UserAnswersRepositoryInterface;

final class AnswersLoggerService
{
    /**
     * @var UserAnswersRepositoryInterface
     */
    private $userAnswersRepository;

    /**
     *
     * @param $userAnswersRepository
     */
    public function __construct( UserAnswersRepositoryInterface $userAnswersRepository )
    {
        $this->userAnswersRepository = $userAnswersRepository;
    }

    /**
     * @param FormData $formInput
     * @param array $answers
     */
    public function logAnswers( FormData $formInput, array $answers ): void
    {
        $this->userAnswersRepository->add($formInput, $answers);
    }

}
