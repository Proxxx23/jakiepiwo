<?php
declare( strict_types=1 );

namespace App\Http\Services;

use App\Http\Objects\StyleInfo;
use App\Http\Repositories\ScoringRepository;
use App\Http\Utils\Exclude;
use App\Http\Utils\Synergy;
use Exception;
use App\Http\Objects\Answers;
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

    public function createBeerData( array $inputAnswers, FormData $user ): BeerData
    {
        $userAnswers = $user->getAnswers();

        $this->batchMarkTaste( $userAnswers, $inputAnswers );

        $inputAnswers = \array_filter( $inputAnswers, fn( $v ) => $v !== 'nie wiem' );

        foreach ( $inputAnswers as $questionNumber => $givenAnswer ) {

            // Jeśli bez znaczenia, to nic nie robimy
            if ( $givenAnswer === 'bez znaczenia' ) {
                continue;
            }

            // Nie idź dalej przy BA, bo nic nie liczymy na tej podstawie
            if ( (int) $questionNumber === 14 ) {
                continue;
            }

            $scoringMap = $this->scoringRepository->fetchByQuestionNumber( (int) $questionNumber );
            foreach ( $scoringMap as $mappedAnswer => $ids ) {

                if ( $givenAnswer === $mappedAnswer ) {
                    $idsToCalculate = $this->buildStrength( $ids );
                    if ( $idsToCalculate !== null ) {
                        foreach ( $idsToCalculate as $styleId => $strength ) {
                            if ( empty( $userAnswers->getRecommendedIds()[$styleId] ) ) {
                                $userAnswers->addToRecommended( $styleId, $strength );
                            } else {
                                $userAnswers->addStrengthToRecommended( $styleId, $strength );
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
                            if ( empty( $userAnswers->getUnsuitableIds()[$styleId] ) ) {
                                $userAnswers->addToUnsuitable( $styleId, $strength );
                            } else {
                                $userAnswers->addStrengthToUnsuitable( $styleId, $strength );
                            }
                        }
                    }
                }

            }
        }

        Exclude::batch( $inputAnswers, $userAnswers );
        Synergy::apply( $inputAnswers, $user );

        $userAnswers->prepareAll();
        $userAnswers->removeAssignedPoints();

        $stylesToTakeCollection = $this->createStylesToTakeCollection( $inputAnswers, $userAnswers );
        $stylesToAvoidCollection = $this->createStylesToAvoidCollection( $userAnswers );

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
                'username' => $user->getUsername(),
                'barrelAged' => $userAnswers->isBarrelAged(),
                'answers' => $inputAnswers,
            ]
        );
    }

    private function batchMarkTaste( Answers $userAnswers, array $inputAnswers ): void
    {
        $userAnswers->setChocolate( $inputAnswers[8] === 'tak' );
        $userAnswers->setCoffee( $inputAnswers[9] === 'tak' );
        $userAnswers->setSour( $inputAnswers[12] === 'tak' );
        $userAnswers->setSmoked( $inputAnswers[13] === 'tak' );
        $userAnswers->setBarrelAged( $inputAnswers[14] === 'tak' );
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

    private function createStylesToTakeCollection( array $inputAnswers, Answers $answers ): ?StylesToTakeCollection
    {
        if ( $answers->getRecommendedIds() === [] ) {
            return null;
        }

        $idStylesToTake = null;
        for ( $i = 0; $i < $answers->getCountStylesToTake(); $i++ ) {
            $idStylesToTake[] = $answers->getRecommendedIds()[$i];
        }

        $styleInfoCollection = null;
        if ( $idStylesToTake !== [] && $idStylesToTake !== null ) {
            $styleInfoCollection = $this->beersRepository->fetchByIds( $idStylesToTake );
        }

        if ( $styleInfoCollection === null ) {
            return null; // should never happen
        }

        $this->polskiKraftRepository->setUserAnswers( $answers );

        $stylesToTakeCollection = ( new StylesToTakeCollection() )->setIdStylesToTake( $idStylesToTake );
        //todo to jest tak złe xDDDDD - rozplątać koniecznie w pizdu tę rzeźbę
        /** @var StyleInfo $styleInfo */
        foreach ( $styleInfoCollection as $styleInfo ) {
            if ( $answers->isSmoked() &&
                \in_array( $styleInfo->getId(), ScoringRepository::POSSIBLE_SMOKED_DARK_BEERS, true ) ) {
                $styleInfo->setSmokedNames();
            }

            $polskiKraftBeerDataCollection = $this->polskiKraftRepository->fetchByStyleId( $inputAnswers[7], $styleInfo->getId() );
            $stylesToTake = new StylesToTake( $styleInfo, $polskiKraftBeerDataCollection );

            if ( \is_array( $answers->getHighlightedIds() ) &&
                \in_array( $styleInfo->getId(), $answers->getHighlightedIds(), true ) ) {
                $stylesToTake->setHighlighted( true );
            }

            $stylesToTakeCollection->add( $stylesToTake->toArray() );
        }

        return $stylesToTakeCollection;
    }

    private function createStylesToAvoidCollection( Answers $answers ): ?StylesToAvoidCollection
    {
        if ( $answers->getUnsuitableIds() === [] ) {
            return null;
        }

        $idStylesToAvoid = null;
        for ( $i = 0; $i < $answers->getCountStylesToAvoid(); $i++ ) {
            $idStylesToAvoid[] = $answers->getUnsuitableIds()[$i];
        }

        $styleInfoCollection = null;
        if ( $idStylesToAvoid !== [] && $idStylesToAvoid !== null ) {
            $styleInfoCollection = $this->beersRepository->fetchByIds( $idStylesToAvoid );
        }

        if ( $styleInfoCollection === null ) {
            return null; // should never happen
        }

        $stylesToAvoidCollection = ( new StylesToAvoidCollection() )->setIdStylesToAvoid( $idStylesToAvoid );
        /** @var StyleInfo $styleInfo */
        foreach ( $styleInfoCollection as $styleInfo ) {
            $stylesToAvoidCollection->add( ( new StylesToAvoid( $styleInfo ) )->toArray() );
        }

        return $stylesToAvoidCollection;
    }
}
