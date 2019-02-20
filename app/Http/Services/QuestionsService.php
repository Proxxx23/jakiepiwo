<?php
declare( strict_types=1 );

namespace App\Http\Services;

use Illuminate\Http\Request;

class QuestionsService
{
    /** @var \QuestionsRepository $questionsRepository */
    protected $questionsRepository;

    /**
     * Constructor.
     *
     * @param \QuestionsRepository $questionsRepository
     */
    public function __construct( \QuestionsRepository $questionsRepository )
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
        return $this->questionsRepository->fetchJsonQuestions();
    }

    /**
     * @param Request $request
     *
     * @return array|null
     */
    public function fetchJsonAnswers( Request $request ): ?array
    {
        if ( empty( $request->get( 'json' ) ) ) {
            $this->logError( 'Brak odpowiedzi na pytania!' );
            return null;
        }

        $answers = \json_decode( $request->get( 'json' ) );

        $questionsCount = \count( $this->getQuestions() );
        if ( $questionsCount !== \count( $answers ) ) {
            $this->logError( 'Liczba odpowiedni na pytania nie zgadza się z liczbą pytań!' );
            return null;
        }

        for ( $i = 1; $i <= $questionsCount; $i++ ) {
            if ( $answers[$i] === null ) {
                $this->logError( 'Pytanie numer ' . $i . ' jest puste. Odpowiedz na wszystkie pytania!' );
            }
        }

        return $answers;
    }
}
