<?php
declare( strict_types=1 );

namespace App\Http\Services;

use App\Http\Repositories\QuestionsRepositoryInterface;

class QuestionsService
{
    /** @var QuestionsRepositoryInterface $questionsRepository */
    protected $questionsRepository;

    /**
     * Constructor.
     *
     * @param QuestionsRepositoryInterface $questionsRepository
     */
    public function __construct( QuestionsRepositoryInterface $questionsRepository )
    {
        $this->questionsRepository = $questionsRepository;
    }

    /**
     * @return array
     */
    public function getQuestions(): array
    {
        return $this->questionsRepository->fetchQuestions();
    }

    /**
     * @param array $requestData
     *
     * @return array
     * @throws \UnexpectedValueException
     */
    public function validateInput( array $requestData ): array
    {
        $questionsCount = \count( $this->getQuestions() );

        if ( $questionsCount !== \count( $requestData['answers'] ) ) {
            throw new \UnexpectedValueException( 'Liczba odpowiedzi na pytania nie zgadza się z liczbą pytań.' );
        }

        return $requestData['answers'];
    }
}
