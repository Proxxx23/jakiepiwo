<?php
declare( strict_types=1 );

namespace App\Http\Services;

use App\Http\Objects\FormInput;
use App\Http\Repositories\UserAnswersRepositoryInterface;

final class DatabaseLoggerService
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
     * @param FormInput $formInput
     * @param array $answers
     */
    public function logAnswers( FormInput $formInput, array $answers )
    {
        $this->userAnswersRepository->add($formInput, $answers);
    }

}
