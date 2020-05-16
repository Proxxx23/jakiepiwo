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
use App\Http\Objects\UnsuitableStyles;
use App\Http\Objects\UnsuitableStylesCollection;
use App\Http\Objects\RecommendedStyles;
use App\Http\Objects\RecommendedStylesCollection;
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

        foreach ( $inputAnswers as $questionNumber => $givenAnswer ) {

            // Jeśli bez znaczenia, to nic nie robimy
            if ( $givenAnswer === 'bez znaczenia' || $givenAnswer === 'nie wiem' ) {
                continue;
            }

            // Nie idź dalej przy BA, bo nic nie liczymy na tej podstawie
            if ( (int) $questionNumber === 13 ) {
                continue;
            }

            $scoringMap = $this->scoringRepository->fetchByQuestionNumber( (int) $questionNumber );
            foreach ( $scoringMap as $mappedAnswer => $ids ) {
                if ( $givenAnswer === $mappedAnswer ) {
                    $idsToCalculate = $this->getIdsToCalculateWithStrength( $ids );
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

                if ( \in_array( $questionNumber, [ 2, 9, 12 ], true ) ) {
                    continue; // we don't give negative points for these questions
                }

                if ( $givenAnswer !== $mappedAnswer ) {
                    $idsToCalculate = $this->getIdsToCalculateWithStrength( $ids );
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

        $recommendedStylesCollection = $this->createRecommendedStylesCollection( $inputAnswers[3], $userAnswers );
        $unsuitableStylesCollection = $this->createUnsuitableStylesCollection( $userAnswers );

        $recommendedIds = ( $recommendedStylesCollection !== null )
            ? $recommendedStylesCollection->getRecommendedIds()
            : null;

        $unsuitableIds = ( $unsuitableStylesCollection !== null )
            ? $unsuitableStylesCollection->getUnsuitableIds()
            : null;

        try {
            $this->stylesLogsRepository->logStyles( $user, $recommendedIds, $unsuitableIds );
        } catch ( Exception $ex ) {
            $this->errorsLogger->logError( $ex->getMessage() );
        }

        return BeerData::fromArray(
            [
                'buyThis' => $recommendedStylesCollection !== null ? $recommendedStylesCollection->toArray() : null,
                'avoidThis' => $unsuitableStylesCollection !== null ? $unsuitableStylesCollection->toArray() : null,
                'username' => $user->getUsername(),
                'barrelAged' => $userAnswers->isBarrelAged(),
                'answers' => $inputAnswers,
            ]
        );
    }

    private function batchMarkTaste( Answers $userAnswers, array $inputAnswers ): void
    {
        $userAnswers->setChocolate( $inputAnswers[7] === 'tak' );
        $userAnswers->setCoffee( $inputAnswers[8] === 'tak' );
        $userAnswers->setSour( $inputAnswers[11] === 'chętnie' );
        $userAnswers->setSmoked( $inputAnswers[12] === 'tak' );
        $userAnswers->setBarrelAged( $inputAnswers[13] === 'tak' );
    }

    /**
     * Buduje siłę dla konkretnych ID stylu
     * Jeśli id ma postać 5:2.5 to zwiększy (przy trafieniu w to ID) punktację tego stylu o 2.5 a nie o 1
     * Domyślnie zwiększa punktację stylu o 1
     * Buduje tablicę z danymi na temat mnożników, aby później kalkulować na tej podstawie
     *
     * @param string $styleIds
     *
     * @return array|null
     */
    private function getIdsToCalculateWithStrength( ?string $styleIds ): ?array
    {
        if ( $styleIds === null ) {
            return null;
        }

        $idsExploded = \explode( ', ', \trim( $styleIds ) );
        $idsToCalculate = [];

        foreach ( $idsExploded as $idMultiplierPair ) {
            if ( \strpos( \trim( $idMultiplierPair ), ':' ) !== false ) {

                /** @var array $idPointsPair */
                $idPointsPair = \explode( ':', $idMultiplierPair );
                $styleId = $idPointsPair[0];
                $multiplier = $idPointsPair[1];

                $idsToCalculate[$styleId] = (float) $multiplier;
            } else {
                $idsToCalculate[$idMultiplierPair] = 1;
            }
        }

        return $idsToCalculate;
    }

    private function createRecommendedStylesCollection( string $density, Answers $answers ): ?RecommendedStylesCollection
    {
        if ( $answers->getRecommendedIds() === [] ) {
            return null;
        }

        $recommendedIds = \array_slice( $answers->getRecommendedIds(), 0, $answers->getCountRecommended(), true );
        if ( $recommendedIds === [] ) {
            return null; // should never happen
        }

        $styleInfoCollection = $this->beersRepository->fetchByIds( $recommendedIds );
        if ( $styleInfoCollection === null ) {
            return null; // should never happen
        }

        $this->polskiKraftRepository->setUserAnswers( $answers );

        $recommendedStylesCollection = ( new RecommendedStylesCollection() )->setRecommendedIds( $recommendedIds );
        /** @var StyleInfo $styleInfo */
        foreach ( $styleInfoCollection as $styleInfo ) {
            if ( $answers->isSmoked() &&
                \in_array( $styleInfo->getId(), ScoringRepository::POSSIBLE_SMOKED_DARK_BEERS, true ) ) {
                $styleInfo->setSmokedNames(); // add "smoked" prefix to smoked beers if user picked yes on smoked question
            }

            $polskiKraftBeerDataCollection = $this->polskiKraftRepository->fetchByStyleId(
                $density, $styleInfo->getId()
            );
            $stylesToTake = new RecommendedStyles( $styleInfo, $polskiKraftBeerDataCollection );

            if ( $answers->getHighlightedIds() !== null &&
                \in_array( $styleInfo->getId(), $answers->getHighlightedIds(), true ) ) {
                $stylesToTake->setHighlighted( true );
            }

            $recommendedStylesCollection->add( $stylesToTake->toArray() );
        }

        return $recommendedStylesCollection;
    }

    private function createUnsuitableStylesCollection( Answers $answers ): ?UnsuitableStylesCollection
    {
        if ( $answers->getUnsuitableIds() === [] ) {
            return null;
        }

        $unsuitableIds = \array_slice( $answers->getUnsuitableIds(), 0, $answers->getCountUnsuitable(), true );
        if ( $unsuitableIds === [] ) {
            return null; // should never happen
        }

        $styleInfoCollection = $this->beersRepository->fetchByIds( $unsuitableIds );
        if ( $styleInfoCollection === null ) {
            return null; // should never happen
        }

        $unsuitableStylesCollection = ( new UnsuitableStylesCollection() )->setUnsuitableIds( $unsuitableIds );
        /** @var StyleInfo $styleInfo */
        foreach ( $styleInfoCollection as $styleInfo ) {
            $unsuitableStylesCollection->add( ( new UnsuitableStyles( $styleInfo ) )->toArray() );
        }

        return $unsuitableStylesCollection;
    }
}
