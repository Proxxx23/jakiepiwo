<?php
declare( strict_types=1 );

namespace App\Http\Services;

use App\Exceptions\InternalIncompatibilityException;
use App\Http\Repositories\QuestionsRepositoryInterface;
use Illuminate\Http\Request;

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
     * @return string
     */
    public function getJsonQuestions(): string
    {
        return $this->questionsRepository->fetchQuestions( true );
    }

    /**
     * @param Request $request
     *
     * @return string
     * @throws InternalIncompatibilityException
     */
    public function fetchJsonAnswers( Request $request ): string
    {
        $answers = $request->post( 'answers' );
        //        if ( empty( $answers ) ) {
        //            $this->logError( 'Brak odpowiedzi na pytania!' );
        //            return null;
        //            //TODO Obsługa błędów
        //        }

        $questionsCount = \count( $this->getQuestions() );

        if ( $questionsCount !== \count( $answers ) ) {
            throw new InternalIncompatibilityException( 'Liczba odpowiedzi na pytania nie zgadza się z liczbą pytań.' );
        }

        //        for ( $i = 0; $i < $questionsCount; $i++ ) {
        //            if ( $answers['answer-'.$i] === null ) {
        //                $this->logError( 'Pytanie numer ' . $i . ' jest puste. Odpowiedz na wszystkie pytania!' );
        //                //TODO obłsuga błędów
        //            }
        //        }

        return \json_encode( $answers );
    }
}
