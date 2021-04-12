<?php
declare( strict_types=1 );

namespace App\Services;

use App\Exceptions\ConnectionException;
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

    /**
     * @param array $inputAnswers
     * @param FormData $user
     *
     * @return BeerData
     * @throws ConnectionException
     */
    public function createBeerData( array $inputAnswers, FormData $user ): BeerData
    {
        if ( $this->polskiKraftRepository->connectionRefused() ) {
            throw new ConnectionException( 'Problem connecting to PolskiKraft API.' );
        }

        $userAnswers = $user->getAnswers();

        $this->batchMarkTaste( $userAnswers, $inputAnswers );

        foreach ( $inputAnswers as $questionNumber => $givenAnswer ) {

            // If has no meaning - do nothing
            if ( $givenAnswer === 'bez znaczenia' || $givenAnswer === 'nie wiem' ) {
                continue;
            }

            // We don't make anything with barrel-ages - don't calculate
            if ( (int) $questionNumber === 13 ) {
                continue;
            }

            $scoringMap = $this->scoringRepository->fetchByQuestionNumber( (int) $questionNumber );
            $questionIdsMap = $this->createIdsMapForQuestion( $scoringMap );
            foreach ( $scoringMap as $mappedAnswer => $ids ) {

                $idsToCalculate = $this->getIdsToCalculateWithStrength( $ids );
                if ( $idsToCalculate === null ) {
                    continue;
                }

                if ( $givenAnswer === $mappedAnswer ) {
                    foreach ( $idsToCalculate as $styleId => $strength ) {
                        if ( empty( $userAnswers->getRecommendedIds()[$styleId] ) ) {
                            $userAnswers->addToRecommended( $styleId, $strength );
                        } else {
                            $userAnswers->addStrengthToRecommended( $styleId, $strength );
                        }
                    }
                }

                if ( \in_array( $questionNumber, [ 2, 9, 12, ], true ) ) {
                    continue; // we don't give negative points for these questions
                }

                if ( $givenAnswer !== $mappedAnswer ) {
                    foreach ( $idsToCalculate as $styleId => $strength ) {

                        if ( \in_array( (string) $styleId, $questionIdsMap[$givenAnswer], true ) ) {
                            continue; // if we find recommended ID also in unsuitable, do not apply negative strength.
                        }

                        if ( empty( $userAnswers->getUnsuitableIds()[$styleId] ) ) {
                            $userAnswers->addToUnsuitable( $styleId, $strength );
                        } else {
                            $userAnswers->addStrengthToUnsuitable( $styleId, $strength );
                        }
                    }
                }
            }
        }

        Exclude::batch( $inputAnswers, $userAnswers );
        Synergy::apply( $inputAnswers, $user );

        $userAnswers->prepareAll()
            ->removeAssignedPoints();

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
            $this->errorsLogger->log( $ex->getMessage() );
        }

        return BeerData::fromArray(
            [
                'buyThis' => $recommendedStylesCollection !== null
                    ? $recommendedStylesCollection->toArray()
                    : null,
                'avoidThis' => $unsuitableStylesCollection !== null
                    ? $unsuitableStylesCollection->toArray()
                    : null,
                'barrelAged' => $userAnswers->isBarrelAged(),
                'answers' => $inputAnswers,
            ]
        );
    }

    private function batchMarkTaste( Answers $userAnswers, array $inputAnswers ): void
    {
        $userAnswers->setChocolate( $inputAnswers[7] === 'tak' )
            ->setCoffee( $inputAnswers[8] === 'tak' )
            ->setSour( $inputAnswers[11] === 'chętnie' )
            ->setSmoked( $inputAnswers[12] === 'tak' )
            ->setBarrelAged( $inputAnswers[13] === 'tak' );
    }

    /**
     * Builds an array of all the ID-s that will gain points for given question
     * So all the IDs for "yes" and "no" response, in two arrays
     * This is used to check if the same ID won't be added to unsuitables after adding to recommended
     *
     * @param array $pairs
     *
     * @return array
     */
    private function createIdsMapForQuestion( array $pairs ): array
    {
        $sortedIds = [];
        foreach ( $pairs as $answer => $idMultiplierPair ) {
            if ( $idMultiplierPair === null ) {
                continue;
            }

            $pair = \explode( ', ', $idMultiplierPair );
            foreach ( $pair as $item ) {
                if ( \str_contains( \trim( $item ), ':' ) ) {

                    /** @var array $idPointsPair */
                    $idPointsPair = \explode( ':', $item );
                    $styleId = $idPointsPair[0];

                    $sortedIds[$answer][] = $styleId;
                } else {
                    /** @var array $idPointsPair */
                    $idPointsPair = \explode( ', ', $item );
                    $styleId = $idPointsPair[0];
                    $sortedIds[$answer][] = $styleId;
                }
            }
        }

        return $sortedIds;
    }

    /**
     * Buduje siłę dla konkretnych ID stylu
     * Jeśli id ma postać 5:2.5 to zwiększy (przy trafieniu w to ID) punktację tego stylu o 2.5 a nie o 1
     * Domyślnie zwiększa punktację stylu o 1
     * Buduje tablicę z danymi na temat mnożników, aby później kalkulować na tej podstawie
     *
     * @param string|null $styleIds
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
            if ( \str_contains( \trim( $idMultiplierPair ), ':' ) ) {

                /** @var array $idPointsPair */
                $idPointsPair = \explode( ':', $idMultiplierPair );
                [ $styleId, $multiplier ] = $idPointsPair;

                $idsToCalculate[$styleId] = (float) $multiplier;
            } else {
                $idsToCalculate[$idMultiplierPair] = 1;
            }
        }

        return $idsToCalculate;
    }

    private function createRecommendedStylesCollection(
        string $density,
        Answers $answers
    ): ?RecommendedStylesCollection {
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
                $styleInfo->setSmokedNames(
                ); // add "smoked" prefix to smoked beers if user picked yes on smoked question
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
