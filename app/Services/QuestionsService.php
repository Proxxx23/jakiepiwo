<?php
declare( strict_types=1 );

namespace App\Services;

use App\Http\Repositories\QuestionsRepositoryInterface;
use UnexpectedValueException;

final readonly class QuestionsService
{
    public function __construct( private QuestionsRepositoryInterface $questionsRepository )
    {
    }

    public function getQuestions(): array
    {
        return $this->questionsRepository->fetchAllQuestions();
    }

    /**
     * @param array $requestData
     *
     * @return array
     * @throws UnexpectedValueException
     */
    public function validateInput( array $requestData ): array
    {
        if ( \count( $this->getQuestions() ) !== \count( $requestData['answers'] ) ) {
            throw new UnexpectedValueException( 'Number of answers do not match number of questions.' );
        }

        $answersFiltered = \array_filter( $requestData['answers'] );

        if ( \count( $answersFiltered ) !== \count( $this->getQuestions() ) ) {
            throw new UnexpectedValueException( 'You must answer all questions.' );
        }

        return $requestData['answers'];
    }
}
