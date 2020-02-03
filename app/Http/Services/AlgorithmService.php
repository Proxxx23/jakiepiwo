<?php
declare( strict_types=1 );

namespace App\Http\Services;

use App\Http\Repositories\ScoringRepository;
use Exception;
use App\Http\Objects\Answers;
use App\Http\Objects\AnswersInterface;
use App\Http\Objects\BeerData;
use App\Http\Objects\StylesToAvoid;
use App\Http\Objects\StylesToAvoidCollection;
use App\Http\Objects\StylesToTake;
use App\Http\Objects\StylesToTakeCollection;
use App\Http\Objects\FormData;
use App\Http\Repositories\BeersRepositoryInterface;
use App\Http\Repositories\PolskiKraftRepositoryInterface;
use App\Http\Repositories\ScoringRepositoryInterface;
use App\Http\Repositories\StylesLogsRepositoryInterface;
use App\Http\Utils\ErrorsLoggerInterface;

final class AlgorithmService
{
    /** @var array */
    private array $answers = [];
    /** @var ScoringRepositoryInterface */
    private ScoringRepositoryInterface $scoringRepository;
    /** @var PolskiKraftRepositoryInterface */
    private PolskiKraftRepositoryInterface $polskiKraftRepository;
    /** @var StylesLogsRepositoryInterface */
    private StylesLogsRepositoryInterface $stylesLogsRepository;
    /** @var BeersRepositoryInterface */
    private BeersRepositoryInterface $beersRepository;
    /** @var ErrorsLoggerInterface */
    private ErrorsLoggerInterface $errorsLogger;

    public function __construct
    (
        ScoringRepositoryInterface $scoringRepository,
        PolskiKraftRepositoryInterface $polskiKraftRepository,
        StylesLogsRepositoryInterface $stylesLogsRepository,
        BeersRepositoryInterface $beersRepository,
        ErrorsLoggerInterface $errorsLogger
    ) {
        $this->scoringRepository = $scoringRepository;
        $this->polskiKraftRepository = $polskiKraftRepository;
        $this->stylesLogsRepository = $stylesLogsRepository;
        $this->beersRepository = $beersRepository;
        $this->errorsLogger = $errorsLogger;
    }

    //todo otestować
    public function applySynergy( array $answerValue, FormData $user ): void
    {
        /** @var Answers $userOptions */
        $userOptions = $user->getAnswers();

        // Lekkie + owocowe + Kwaśne
        if ( $answerValue[4] === 'coś lekkiego' &&
            $answerValue[12] === 'tak' &&
            $answerValue[13] === 'chętnie' ) {
            $userOptions->applyPositiveSynergy( [ 40, 56 ], 2 );
            $userOptions->applyPositiveSynergy( [ 51 ], 1.5 );
        }

        // nowe smaki LUB szokujące + złożone + jasne
        if ( $answerValue[4] === 'coś złożonego' &&
            $answerValue[4] === 'jasne' &&
            ( $answerValue[2] === 'tak' || $answerValue[3] === 'tak' ) ) {
            $userOptions->applyPositiveSynergy( [ 7, 15, 16, 23, 39, 42, 50, 60, 73 ], 2 );
        }

        // nowe smaki LUB szokujące + złożone + ciemne
        if ( $answerValue[4] === 'coś złożonego' &&
            $answerValue[4] === 'ciemne' &&
            ( $answerValue[2] === 'tak' || $answerValue[3] === 'tak' ) ) {
            $userOptions->applyPositiveSynergy( [ 36, 37, 59 ], 2 );
        }

        // złożone + ciemne + nieowocowe
        if ( $answerValue[4] === 'coś złożonego' &&
            $answerValue[6] === 'ciemne' &&
            $answerValue[12] === 'nie' ) {
            $userOptions->applyPositiveSynergy( [ 3, 24, 35, 36, 37, 48, 59, 75 ], 1.5 );
        }

        // złożone + ciemne + nieowocowe + kawowe
        if ( $answerValue[4] === 'coś złożonego' &&
            $answerValue[6] === 'ciemne' &&
            $answerValue[10] === 'tak' &&
            $answerValue[12] === 'nie' ) {
            $userOptions->applyPositiveSynergy( [ 74 ], 2.5 );
        }

        // Lekkie + ciemne + słodkie + goryczka (ledwie || lekka || wyczuwalna)
        if ( $answerValue[4] === 'coś lekkiego' &&
            $answerValue[6] === 'ciemne' &&
            $answerValue[7] === 'słodsze' &&
            !\in_array( $answerValue[5], [ 'zdecydowanie wyczuwalną', 'jestem hopheadem' ], true ) ) {
            $userOptions->applyPositiveSynergy( [ 12, 29, 30, 34, 64 ], 2 );
            $userOptions->applyNegativeSynergy( [ 36, 37 ], 3 );
        }

        // jasne + nieczekoladowe
        if ( $answerValue[6] === 'jasne' &&
            $answerValue[9] === 'nie' ) {
            $userOptions->applyNegativeSynergy(
                [ 12, 21, 24, 29, 33, 34, 35, 36, 37, 59, 71, 74, 75 ], 2
            );
        }

        // ciemne + czekoladowe + lżejsze
        if ( $answerValue[6] === 'ciemne' &&
            $answerValue[9] === 'tak' &&
            $answerValue[8] !== 'mocne i gęste' ) {
            $userOptions->applyPositiveSynergy( [ 12, 31, 33, 34, 35, 59, 71 ], 2.5 );
        }


        // goryczka ledwo || lekka + jasne + nieczekoladowe + niegęste
        if ( $answerValue[6] === 'jasne' &&
            $answerValue[9] === 'nie' &&
            $answerValue[8] !== 'mocne i gęste' &&
            \in_array( $answerValue[5], ['ledwie wyczuwalną', 'lekką'], true ) ) {
            $userOptions->applyPositiveSynergy( [ 20, 25, 40, 44, 45, 47, 51, 52, 53, 68, 73 ], 2 );
            $userOptions->applyNegativeSynergy( [ 3, 24, 35, 36, 37, 59, 71, 75 ], 2 );
        }

        // jasne + lekkie + wodniste + wędzone = grodziskie
        if ( $answerValue[4] === 'coś lekkiego' &&
            $answerValue[6] === 'jasne' &&
            $answerValue[8] === 'wodniste' &&
            $answerValue[14] === 'tak' ) {
            $userOptions->applyPositiveSynergy( [ 52 ], 3 );
            $userOptions->applyNegativeSynergy( [ 3, 22, 23, 24, 35, 36, 37, 50, 59, 71, 75 ], 2 );
        }

        // duża/hophead goryczka + jasne
        if ( $answerValue[6] === 'jasne' &&
            \in_array( $answerValue[5], [ 'zdecydowanie wyczuwalną', 'jestem hopheadem' ], true ) ) {
            $userOptions->applyPositiveSynergy( [ 1, 2, 5, 6, 7, 8, 28, 61 ], 1.75 );
            $userOptions->applyPositiveSynergy( [ 65, 69, 70, 72 ], 1.5 );
            $userOptions->applyNegativeSynergy( [ 14, 25, 45, 47 ], 1.75 );
        }

        // duża/hophead goryczka + ciemne
        if ( $answerValue[6] === 'ciemne' &&
            \in_array( $answerValue[5], [ 'zdecydowanie wyczuwalną', 'jestem hopheadem' ], true ) ) {
            $userOptions->applyPositiveSynergy( [ 3, 36, 37, 75 ], 1.75 );
        }

        // goryczka ledwo || lekka
        if ( $answerValue[5] === 'ledwie wyczuwalną' || $answerValue[5] === 'lekką' ) {
            $userOptions->applyNegativeSynergy( [ 1, 2, 3, 5, 7, 8, 28, 61 ], 2 );
            $userOptions->applyNegativeSynergy( [ 6, 60, 65, 69, 71, 72 ], 1.5 );
        }
    }

    public function createBeerData( array $answers, FormData $user ): BeerData
    {
        $this->answers = $answers;

        /** @var Answers $userOptions */
        $userOptions = $user->getAnswers();

        $userOptions->setSmoked( $answers[13] === 'tak' );
        $userOptions->setBarrelAged( $answers[14] === 'tak' );

        $answers = \array_filter( $answers, fn($v) => $v !== 'nie wiem');

        foreach ( $answers as $questionNumber => &$givenAnswer ) {

            // we calculate nothing
            if ( $givenAnswer === 'bez znaczenia' ) {
                continue;
            }

            // Nie idź dalej przy BA bo nic nie liczymy na tej podstawie
            if ( $questionNumber === 14 ) {
                continue;
            }

            $scoringMap = $this->scoringRepository->fetchByQuestionNumber( $questionNumber );
            foreach ( $scoringMap as $mappedAnswer => $ids ) {

                if ( $givenAnswer === $mappedAnswer ) {
                    $idsToCalculate = $this->buildStrength( $ids );
                    if ( $idsToCalculate !== null ) {
                        foreach ( $idsToCalculate as $styleId => $strength ) {
                            if ( empty( $userOptions->getIncludedIds()[$styleId] ) ) {
                                $userOptions->addToIncluded( $styleId, $strength );
                            } else {
                                $userOptions->addStrengthToIncluded( $styleId, $strength );
                            }
                        }
                    }
                }

                //todo: wtf?
//                if ( \in_array( $questionNumber, [ 3, 5, 9 ], true ) ) {
//                    continue;
//                }

                if ( $givenAnswer !== $mappedAnswer ) {
                    $idsToCalculate = $this->buildStrength( $ids );
                    if ( $idsToCalculate !== null ) {
                        foreach ( $idsToCalculate as $styleId => $strength ) {
                            if ( empty( $userOptions->getExcludedIds()[$styleId] ) ) {
                                $userOptions->addToExcluded( $styleId, $strength );
                            } else {
                                $userOptions->addStrengthToExcluded( $styleId, $strength );
                            }
                        }
                    }
                }

            }
        }
        unset( $givenAnswer );

        $this->excludeBatch( $answers, $userOptions );
        $this->applySynergy( $answers, $user );

        /** @var FormData $user */
        $answers = $user->getAnswers();
        $answers->fetchAll();
        $answers->removeAssignedPoints();

        $stylesToTakeCollection = $this->createStylesToTakeCollection( $answers, $userOptions->isSmoked() );
        $stylesToAvoidCollection = $this->createStylesToAvoidCollection( $answers );

        $idStylesToTake = ( $stylesToTakeCollection !== null )
            ? $stylesToTakeCollection->getIdStylesToTake()
            : null;

        $idStylesToAvoid = ( $stylesToAvoidCollection !== null )
            ? $stylesToAvoidCollection->getIdStylesToAvoid()
            : null;

        try {
            $this->stylesLogsRepository->logStyles( $user, $idStylesToTake, $idStylesToAvoid );
        } catch ( Exception $e ) {
            $this->errorsLogger->logError( $e->getMessage() );
        }

        return BeerData::fromArray(
            [
                'buyThis' => $stylesToTakeCollection !== null ? $stylesToTakeCollection->toArray() : null,
                'avoidThis' => $stylesToAvoidCollection !== null ? $stylesToAvoidCollection->toArray() : null,
                'mustTake' => $answers->isMustTakeOpt(),
                'mustAvoid' => $answers->isMustAvoidOpt(),
                'username' => $user->getUsername(),
                'barrelAged' => $answers->isBarrelAged(),
                'answers' => $this->answers,
                'resultsHash' => $this->makeResultsHash( $this->answers ),
            ]
        );
    }

    private function makeResultsHash( array $answers ): string
    {
        $hashKey = \str_replace( ' ', '', \implode( '', $answers ) ) . \now();

        return \hash('ripemd128', $hashKey);
    }

    /**
     * Buduje siłę dla konkretnych ID stylu
     * Jeśli id ma postać 5:2.5 to zwiększy (przy trafieniu w to ID) punktację tego stylu o 2.5 a nie o 1
     * Domyślnie zwiększa punktację stylu o 1
     *
     * @param string $styleIds
     *
     * @return array|null
     */
    private function buildStrength( ?string $styleIds ): ?array
    {
        if ( $styleIds === null ) {
            return null;
        }

        $idsExploded = \explode( ',', \trim( $styleIds ) );
        $idsToCalculate = [];

        foreach ( $idsExploded as $idMultiplierPair ) {
            if ( \strpos( $idMultiplierPair, ':' ) !== false || \strpos( $idMultiplierPair, ' :' ) !== false ) {
                $tmp = \explode( ':', $idMultiplierPair );
                $idsToCalculate[$tmp[0]] = (float) $tmp[1];
            } else {
                $idsToCalculate[$idMultiplierPair] = 1;
            }
        }

        return $idsToCalculate;
    }

    private function createStylesToTakeCollection( AnswersInterface $answers, bool $isSmoked ): ?StylesToTakeCollection
    {
        if ( $answers->getIncludedIds() === [] ) {
            return null;
        }

        $idStylesToTake = [];
        for ( $i = 0; $i < $answers->getCountStylesToTake(); $i++ ) {
            $idStylesToTake[] = $answers->getIncludedIds()[$i];
        }

        $buyThis = [];
        if ( $idStylesToTake !== [] ) {
            $buyThis = $this->beersRepository->fetchByIds( $idStylesToTake, $answers->isShuffled() );
            //todo: czasami bierze 3, 4 albo 5 z bazy
        }
        $stylesToTakeCollection = ( new StylesToTakeCollection() )->setIdStylesToTake( $idStylesToTake );

        //todo to jest tak złe xDDDDD - rozplątać koniecznie w pizdu tę rzeźbę
        foreach ( $buyThis as $styleInfo ) {
            if ( $isSmoked && \in_array( (int) $styleInfo->id, ScoringRepository::POSSIBLE_SMOKED_DARK_BEERS, true ) ) {
                $styleInfo->name = '(Smoked) ' . $styleInfo->name;
                $styleInfo->name_pl = '(Wędzony) ' . $styleInfo->name_pl;
            }
            $polskiKraftBeerDataCollection = $this->polskiKraftRepository->fetchByBeerId( (int) $styleInfo->id );
            $stylesToTakeCollection->add( ( new StylesToTake( $styleInfo, $polskiKraftBeerDataCollection ) )->toArray() );
        }

        return $stylesToTakeCollection;
    }

    private function createStylesToAvoidCollection( AnswersInterface $answers ): ?StylesToAvoidCollection
    {
        if ( $answers->getExcludedIds() === [] ) {
            return null;
        }

        $idStylesToAvoid = [];
        for ( $i = 0; $i < $answers->getCountStylesToAvoid(); $i++ ) {
            $idStylesToAvoid[] = $answers->getExcludedIds()[$i];
        }

        $avoidThis = [];
        if ( $idStylesToAvoid !== [] ) {
            $avoidThis = $this->beersRepository->fetchByIds( $idStylesToAvoid );
        }

        $stylesToAvoidCollection = ( new StylesToAvoidCollection() )
            ->setIdStylesToAvoid( $idStylesToAvoid );

        foreach ( $avoidThis as $styleInfo ) {
            $stylesToAvoidCollection->add( ( new StylesToAvoid( $styleInfo ) )->toArray() );
        }

        return $stylesToAvoidCollection;
    }

    private function excludeBatch( array $answers, Answers $userOptions ): void
    {
        if ( isset( $answers[13] ) && $answers[12] === 'nie ma mowy' ) {
            $userOptions->excludeFromRecommended( [ 40, 42, 44, 51, 56 ] );
        }

        if ( isset( $answers[13] ) && $answers[13] === 'nie' ) {
            $userOptions->excludeFromRecommended( [ 15, 16, 52, 57 ] );
        }

        if ( isset( $answers[13] ) && $answers[3] === 'coś lekkiego' ) {
            $userOptions->excludeFromRecommended( [ 50 ] );
        }
    }
}
