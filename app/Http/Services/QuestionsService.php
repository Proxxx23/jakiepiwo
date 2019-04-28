<?php
declare( strict_types=1 );

namespace App\Http\Services;

use App\Exceptions\InternalIncompatibilityException;
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
     * @return string
     * @throws InternalIncompatibilityException
     */
    public function fetchJsonAnswers( array $requestData ): string
    {
        //        if ( empty( $answers ) ) {
        //            $this->logError( 'Brak odpowiedzi na pytania!' );
        //            return null;
        //            //TODO Obsługa błędów
        //        }

        $questionsCount = \count( $this->getQuestions() );

        if ( $questionsCount !== \count( $requestData['answers'] ) ) {
            throw new InternalIncompatibilityException( 'Liczba odpowiedzi na pytania nie zgadza się z liczbą pytań.' );
        }

        //        for ( $i = 0; $i < $questionsCount; $i++ ) {
        //            if ( $answers['answer-'.$i] === null ) {
        //                $this->logError( 'Pytanie numer ' . $i . ' jest puste. Odpowiedz na wszystkie pytania!' );
        //                //TODO obłsuga błędów
        //            }
        //        }

        return $requestData['answers'];
    }
}
