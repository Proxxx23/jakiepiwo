<?php
declare( strict_types=1 );

namespace App\Http\Services;

use App\Http\Repositories\QuestionsRepositoryInterface;

final class QuestionsService
{
    /** @var QuestionsRepositoryInterface $questionsRepository */
    private $questionsRepository;

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
        return $this->questionsRepository->fetchAllQuestions();
    }

    /**
     * @param array $requestData
     *
     * @return array
     * @throws \UnexpectedValueException
     */
    public function validateInput( array $requestData ): array
    {
        if ( \count( $this->getQuestions() ) !== \count( $requestData['answers'] ) ) {
            throw new \UnexpectedValueException( 'Number of answers do not match number of questions.' );
        }

        return $requestData['answers'];
    }
}
