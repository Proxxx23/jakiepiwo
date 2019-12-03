<?php
declare( strict_types=1 );

namespace App\Http\Services;

use App\Http\Objects\Answers;
use App\Http\Objects\AnswersInterface;
use App\Http\Objects\BeerData;
use App\Http\Objects\StylesToAvoid;
use App\Http\Objects\StylesToAvoidCollection;
use App\Http\Objects\StylesToTake;
use App\Http\Objects\StylesToTakeCollection;
use App\Http\Objects\FormData;
use App\Http\Repositories\BeersRepositoryInterface;
use App\Http\Repositories\OnTapRepositoryInterface;
use App\Http\Repositories\PolskiKraftRepositoryInterface;
use App\Http\Repositories\ScoringRepositoryInterface;
use App\Http\Repositories\StylesLogsRepositoryInterface;
use App\Http\Utils\ErrorsLoggerInterface;

final class AlgorithmService
{
    /** @var array */
    private $answers = [];
    /** @var ScoringRepositoryInterface */
    private $scoringRepository;
    /** @var PolskiKraftRepositoryInterface */
    private $polskiKraftRepository;
    /** @var StylesLogsRepositoryInterface */
    private $stylesLogsRepository;
    /** @var BeersRepositoryInterface */
    private $beersRepository;
    /** @var ErrorsLoggerInterface */
    private $errorsLogger;
    /** @var OnTapRepositoryInterface */
    private $onTapRepository;

    /**
     * @param ScoringRepositoryInterface $scoringRepository
     * @param PolskiKraftRepositoryInterface $polskiKraftRepository
     * @param StylesLogsRepositoryInterface $stylesLogsRepository
     * @param BeersRepositoryInterface $beersRepository
     * @param ErrorsLoggerInterface $errorsLogger
     */
    public function __construct
    (
        ScoringRepositoryInterface $scoringRepository,
        PolskiKraftRepositoryInterface $polskiKraftRepository,
        StylesLogsRepositoryInterface $stylesLogsRepository,
        BeersRepositoryInterface $beersRepository,
        ErrorsLoggerInterface $errorsLogger,
        OnTapRepositoryInterface $onTapRepository
    ) {
        $this->scoringRepository = $scoringRepository;
        $this->polskiKraftRepository = $polskiKraftRepository;
        $this->stylesLogsRepository = $stylesLogsRepository;
        $this->beersRepository = $beersRepository;
        $this->errorsLogger = $errorsLogger;
        $this->onTapRepository = $onTapRepository;
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

        $idsExploded = \explode( ',', trim( $styleIds ) );
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

    /**
     * Positive and negative synergies executor
     * There are all the synergies
     *
     * @param array $answerValue
     * @param FormData $user
     */
    public function applySynergy( array $answerValue, FormData $user ): void
    {
        /** @var Answers $userOptions */
        $userOptions = $user->getAnswers();

        // Lekkie + owocowe + Kwaśne
        if ( $answerValue[4] === 'coś lekkiego' &&
            $answerValue[12] === 'tak' &&
            $answerValue[13] === 'chętnie' ) {
            $userOptions->buildPositiveSynergy( [ 40, 56 ], 2 );
            $userOptions->buildPositiveSynergy( [ 51 ], 1.5 );
        }

        // nowe smaki LUB szokujące + złożone + jasne
        if ( $answerValue[4] === 'coś złożonego' &&
            $answerValue[4] === 'jasne' &&
            ( $answerValue[2] === 'tak' || $answerValue[3] === 'tak' ) ) {
            $userOptions->buildPositiveSynergy( [ 7, 15, 16, 23, 39, 42, 50, 60, 73 ], 2 );
        }

        // nowe smaki LUB szokujące + złożone + ciemne
        if ( $answerValue[4] === 'coś złożonego' &&
            $answerValue[4] === 'ciemne' &&
            ( $answerValue[2] === 'tak' || $answerValue[3] === 'tak' ) ) {
            $userOptions->buildPositiveSynergy( [ 36, 37, 58, 59, 62, 63 ], 2 );
        }

        // złożone + ciemne + nieowocowe
        if ( $answerValue[4] === 'coś złożonego' &&
            $answerValue[6] === 'ciemne' &&
            $answerValue[12] === 'nie' ) {
            $userOptions->buildPositiveSynergy( [ 3, 24, 35, 36, 37, 48, 58, 59, 62, 63, 75 ], 1.5 );
        }

        // złożone + ciemne + nieowocowe + kawowe
        if ( $answerValue[4] === 'coś złożonego' &&
            $answerValue[6] === 'ciemne' &&
            $answerValue[10] === 'tak' &&
            $answerValue[12] === 'nie' ) {
            $userOptions->buildPositiveSynergy( [ 74 ], 2.5 );
        }

        // Lekkie + ciemne + słodkie + goryczka (ledwie || lekka || wyczuwalna)
        if ( $answerValue[4] === 'coś lekkiego' &&
            $answerValue[6] === 'ciemne' &&
            $answerValue[7] === 'słodsze' &&
            !\in_array( $answerValue[5], [ 'mocną', 'jestem hopheadem' ] ) ) {
            $userOptions->buildPositiveSynergy( [ 12, 29, 30, 34, 64 ], 2 );
            $userOptions->buildNegativeSynergy( [ 36, 37 ], 3 );
        }

        // jasne + nieczekoladowe
        if ( $answerValue[6] === 'jasne' &&
            $answerValue[9] === 'nie' ) {
            $userOptions->buildNegativeSynergy(
                [ 12, 21, 24, 29, 33, 34, 35, 36, 37, 58, 59, 62, 63, 71, 74, 75 ], 2
            );
        }

        // ciemne + czekoladowe + lżejsze
        if ( $answerValue[6] === 'ciemne' &&
            $answerValue[9] === 'tak' &&
            $answerValue[8] !== 'mocne i gęste' ) {
            $userOptions->buildPositiveSynergy( [ 12, 31, 33, 34, 35, 59, 71 ], 2.5 );
        }


        // goryczka ledwo || lekka + jasne + nieczkoladowe + niegęste
        if ( $answerValue[6] === 'jasne' &&
            $answerValue[9] === 'nie' &&
            $answerValue[8] !== 'mocne i gęste' &&
            ( $answerValue[5] === 'ledwie wyczuwalną' || $answerValue[5] === 'lekką' ) ) {
            $userOptions->buildPositiveSynergy( [ 20, 25, 40, 44, 45, 47, 51, 52, 53, 68, 73 ], 2 );
            $userOptions->buildNegativeSynergy( [ 3, 24, 35, 36, 37, 58, 59, 62, 63, 71, 75 ], 2 );
        }

        // jasne + lekkie + wodniste + wędzone = grodziskie
        if ( $answerValue[4] === 'coś lekkiego' &&
            $answerValue[6] === 'jasne' &&
            $answerValue[8] === 'wodniste' &&
            $answerValue[14] === 'tak' ) {
            $userOptions->buildPositiveSynergy( [ 52 ], 3 );
            $userOptions->buildNegativeSynergy( [ 3, 22, 23, 24, 35, 36, 37, 50, 58, 59, 62, 63, 71, 75 ], 2 );
        }

        // duża/hophead goryczka + jasne
        if ( $answerValue[6] === 'jasne' &&
            ( $answerValue[5] === 'mocną' || $answerValue[5] === 'jestem hopheadem' ) ) {
            $userOptions->buildPositiveSynergy( [ 1, 2, 5, 6, 7, 8, 28, 61 ], 1.75 );
            $userOptions->buildPositiveSynergy( [ 65, 69, 70, 72 ], 1.5 );
        }

        // duża/hophead goryczka + ciemne
        if ( $answerValue[6] === 'ciemne' &&
            ( $answerValue[5] === 'mocną' || $answerValue[5] === 'jestem hopheadem' ) ) {
            $userOptions->buildPositiveSynergy( [ 3, 36, 37, 58, 62, 63, 75 ], 1.75 );
        }

        // goryczka ledwo || lekka
        if ( $answerValue[5] === 'ledwie wyczuwalną' || $answerValue[5] === 'lekką' ) {
            $userOptions->buildNegativeSynergy( [ 1, 2, 3, 5, 7, 8, 28, 61 ], 2 );
            $userOptions->buildNegativeSynergy( [ 6, 60, 65, 69, 71, 72 ], 1.5 );
        }
    }

    /**
     * @param array $answers
     * @param FormData $user
     * @return BeerData
     *
     * todo: dodać mechanizm, który informuje, że granice były marginalne i wyniki mogą byc niejednoznaczne
     */
    public function createBeerData( array $answers, FormData $user ): BeerData
    {
        $this->answers = $answers;

        /** @var Answers $userOptions */
        $userOptions = $user->getAnswers();

        $userOptions->setBarrelAged( $answers[14] === 'tak' );

        if ( $answers[12] === 'nie ma mowy' ) {
            $userOptions->excludeFromRecommended( [ 40, 42, 44, 51, 56 ] );
        }

        if ( $answers[13] === 'nie' ) {
            $userOptions->excludeFromRecommended( [ 15, 16, 52, 57, 58, 59, 62, 63 ] );
        }

        $answers = \array_filter( $answers, static function ( $v )
        {
            return $v !== 'nie wiem';
        });

        foreach ( $answers as $questionNumber => &$givenAnswer ) {

            $scoringMap = $this->scoringRepository->fetchByQuestionNumber( $questionNumber );

            foreach ( $scoringMap as $mappedAnswer => $ids ) {

                // Nie idź dalej przy BA
                if ( $questionNumber === 14 ) {
                    continue;
                }

                // todo rozplątać
                if ( $givenAnswer === $mappedAnswer &&
                    $givenAnswer !== 'bez znaczenia' ) {
                    $idsToCalculate = $this->buildStrength( $ids );
                    if ( $idsToCalculate !== null ) {
                        foreach ( $idsToCalculate as $styleId => $strength ) {
                            if ( empty( $userOptions->getIncludedIds()[$styleId] ) ) { //todo bez sensu...
                                $userOptions->addToIncluded( $styleId, $strength );
                            } else {
                                $userOptions->addStrengthToIncluded( $styleId, $strength );
                            }
                        }
                    }
                }

                // todo rozplątać
                if ( $givenAnswer !== $mappedAnswer &&
                    $givenAnswer !== 'bez znaczenia' &&
                    !\in_array( $questionNumber, [ 3, 5, 9 ], true ) ) {
                    $idsToCalculate = $this->buildStrength( $ids );
                    if ( $idsToCalculate !== null ) {
                        foreach ( $idsToCalculate as $styleId => $strength ) {
                            if ( empty( $userOptions->getExcludedIds()[$styleId] ) ) { //todo bez sensu...
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

        $this->applySynergy( $answers, $user );

        /** @var AnswersInterface $answers */
        $answers = $user->getAnswers();
        $answers->fetchAll();
        $answers->removeAssignedPoints();

        $stylesToTakeCollection = $this->createStylesToTakeCollection( $answers );
        $stylesToAvoidCollection = $this->createStylesToAvoidCollection( $answers );

        $idStylesToTake = $stylesToTakeCollection !== null
            ? $stylesToTakeCollection->getIdStylesToTake()
            : null;

        $idStylesToAvoid = $stylesToAvoidCollection !== null
            ? $stylesToAvoidCollection->getIdStylesToAvoid()
            : null;

        try {
            $this->stylesLogsRepository->logStyles( $user, $idStylesToTake, $idStylesToAvoid );
        } catch ( \Exception $e ) {
            $this->errorsLogger->logError( $e->getMessage() );
        }

        return new BeerData(
            [
                'buyThis' => $stylesToTakeCollection !== null ? $stylesToTakeCollection->toArray() : null,
                'avoidThis' => $stylesToAvoidCollection !== null ? $stylesToAvoidCollection->toArray() : null,
                'mustTake' => $answers->isMustTakeOpt(),
                'mustAvoid' => $answers->isMustAvoidOpt(),
                'username' => $user->getUsername(),
                'barrelAged' => $answers->isBarrelAged(),
                'answers' => $this->answers,
            ]
        );
    }

    /**
     * @param AnswersInterface $answers
     * @return StylesToTakeCollection|null
     */
    private function createStylesToTakeCollection( AnswersInterface $answers ): ?StylesToTakeCollection
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
            $buyThis = $this->beersRepository->fetchByIds( $idStylesToTake );
        }

        $stylesToTakeCollection = ( new StylesToTakeCollection() )
            ->setIdStylesToTake( $idStylesToTake );

        foreach ( $buyThis as $styleInfo ) {
            $beerDataCollection = $this->polskiKraftRepository->fetchByBeerId( (int) $styleInfo->id );
            $stylesToTakeCollection->add( ( new StylesToTake( $styleInfo, $beerDataCollection ) )->toArray() );
        }

        return $stylesToTakeCollection;
    }

    /**
     * @param AnswersInterface $answers
     * @return StylesToAvoidCollection|null
     */
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
}
