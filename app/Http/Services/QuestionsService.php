<?php
declare( strict_types=1 );

namespace App\Http\Services;

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
        return $this->questionsRepository->fetchJsonQuestions();
    }

    /**
     * @param Request $request
     *
     * @return array|null
     */
    public function fetchJsonAnswers( Request $request ): ?array
    {
        if ( empty( $answers = $request->post( 'answers' ) ) ) {
            //            $this->logError( 'Brak odpowiedzi na pytania!' );
            return null;
        }

        $questionsCount = \count( $this->getQuestions() );


        if ( $questionsCount !== \count( $answers ) ) {
//            $this->logError( 'Liczba odpowiedi na pytania nie zgadza się z liczbą pytań!' );
            return null;
        }

        for ( $i = 0; $i < $questionsCount; $i++ ) {
            if ( $answers['answer-'.$i] === null ) {
                //                $this->logError( 'Pytanie numer ' . $i . ' jest puste. Odpowiedz na wszystkie pytania!' );
            }
        }

        return $answers;
    }
}
