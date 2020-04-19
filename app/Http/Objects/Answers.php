<?php
declare( strict_types=1 );

namespace App\Http\Objects;

final class Answers
{
    private const POINT_PERCENT_GAP_WITH_PREVIOUS = 0.90;
    private const POINT_PERCENT_GAP_WITH_FIRST = 0.80;
    private const MARGIN_INCLUDED_EXCLUDED = 1.25;
    private const MARGIN_STYLES_TO_DISTINGUISH = 1.25;
    private const MARGIN_PERCENT_FOR_OPTIONAL_TO_SHOW = 90;

    private bool $barrelAged = false;
    private int $countStylesToTake = 3;
    private int $countStylesToAvoid = 3;
    private array $excludedIds = [];
    private ?array $highlightedIds = null;
    private array $includedIds = [];
    private bool $smoked = false;
    private bool $shuffled = false;

    public function getIncludedIds(): array
    {
        return $this->includedIds;
    }

    public function getExcludedIds(): array
    {
        return $this->excludedIds;
    }

    public function isBarrelAged(): bool
    {
        return $this->barrelAged;
    }

    public function setBarrelAged( bool $barrelAged ): Answers
    {
        $this->barrelAged = $barrelAged;

        return $this;
    }

    public function isSmoked(): bool
    {
        return $this->smoked;
    }

    public function setSmoked( bool $smoked ): Answers
    {
        $this->smoked = $smoked;

        return $this;
    }

    public function setShuffled( bool $shuffled ): void
    {
        $this->shuffled = $shuffled;
    }

    public function getCountStylesToTake(): int
    {
        return $this->countStylesToTake;
    }

    public function getCountStylesToAvoid(): int
    {
        return $this->countStylesToAvoid;
    }

    public function getHighlightedIds(): ?array
    {
        return $this->highlightedIds;
    }

    public function addToIncluded( int $styleId, float $strength ): void
    {
        $this->includedIds[$styleId] = $strength;
    }

    public function addStrengthToIncluded( int $styleId, float $strength ): void
    {
        $this->includedIds[$styleId] += $strength;
    }

    public function addToExcluded( int $styleId, float $strength ): void
    {
        $this->excludedIds[$styleId] = $strength;
    }

    public function addStrengthToExcluded( int $styleId, float $strength ): void
    {
        $this->excludedIds[$styleId] += $strength;
    }

    /**
     * Builds positive synergy if user ticks 2-4 particular answers
     *
     * @param array $idsToMultiply
     * @param float $multiplier
     */
    public function applyPositiveSynergy( array $idsToMultiply, float $multiplier ): void
    {
        foreach ( $idsToMultiply as $id ) {
            if ( !isset( $this->includedIds[$id] ) ) {
                $this->includedIds[$id] = $multiplier;
                continue;
            }
            $this->includedIds[$id] *= $multiplier;
        }
    }

    /**
     * Builds negative synergy if user ticks 2-4 particular answers
     *
     * @param array $idsToDivide
     * @param float $divider
     */
    public function applyNegativeSynergy( array $idsToDivide, float $divider ): void
    {
        foreach ( $idsToDivide as $id ) {
            if ( !isset( $this->excludedIds[$id] ) ) {
                continue;
            }
            $this->excludedIds[$id] = \floor( $this->excludedIds[$id] / $divider );
        }
    }

    /**
     * Excludes sour/smoked beers from recommended styles if user says NO
     *
     * @param array $idsToExclude
     */
    public function excludeFromRecommended( array $idsToExclude ): void
    {
        foreach ( $idsToExclude as $id ) {
            if ( !isset( $this->includedIds[$id] ) ) {
                continue;
            }
            $this->includedIds[$id] = 0; //shouldn't be null?
        }
    }

    /**
     * Remove points assigned to beer ids, transforms from [id => strength] to [index => id]
     */
    public function removeAssignedPoints(): void
    {
        $this->includedIds = ( $this->shuffled === false )
            ? \array_keys( $this->includedIds )
            : \array_values( $this->includedIds ); //todo: wybadać, jak ma się to zachowywać!!!
        $this->excludedIds = \array_keys( $this->excludedIds );
    }

    public function prepareAll(): void
    {
        $this->sortPickedUp();
        $this->retrieveOptionalStyles();
        $this->removeDuplicated();
        $this->checkMarginBetweenStyles();
        $this->retrieveStylesToTake();
        $this->shuffleIncludedStyles();
    }

    private function sortPickedUp(): void
    {
        \arsort( $this->includedIds );
        \arsort( $this->excludedIds );
    }

    /**
     * If there's an 4th and 5rd style with a little 'margin" to 3rd style
     * Takes 4th or 5th style as an extra styles to take or avoid
     */
    private function retrieveOptionalStyles(): void
    {
        $thirdStyleToTake = \array_values( \array_slice( $this->includedIds, 0, 3, true ) );
        $thirdStyleToAvoid = \array_values( \array_slice( $this->excludedIds, 0, 3, true ) );

        for ( $i = 3; $i <= 4; $i++ ) {

            $toTakeChunk = \array_values( \array_slice( $this->includedIds, 0, $i, true ) );
            if ( empty( $toTakeChunk ) ) {
                continue;
            }

            if ( $toTakeChunk[0] >= ( $thirdStyleToTake[0] / 100 * self::MARGIN_PERCENT_FOR_OPTIONAL_TO_SHOW ) ) {
                $this->countStylesToTake++;
            }

            $toAvoidChunk = \array_values( \array_slice( $this->excludedIds, 0, $i, true ) );
            if ( empty( $toAvoidChunk ) ) {
                continue;
            }

            if ( $toAvoidChunk[0] >= ( $thirdStyleToAvoid[0] / 100 * self::MARGIN_PERCENT_FOR_OPTIONAL_TO_SHOW ) ) {
                $this->countStylesToAvoid++;
            }
        }
    }

    /**
     * Check if consecutive style has more or less than 75% of previous style and emphasize it
     * It works like this:
     * - check if 1st style has 125% of points of 2nd style. If yes - 1st style is distinguish
     * - if not - distinguish 1st and 2nd and check 3rd with 2nd etc etc.
     */
    private function retrieveStylesToTake(): void
    {
        $firstStyleToTake = \array_values( \array_slice( $this->includedIds, 0, 1, true ) );
        $secondStyleToTake = \array_values( \array_slice( $this->includedIds, 1, 1, true ) );
        $thirdStyleToTake = \array_values( \array_slice( $this->includedIds, 2, 1, true ) );
        $fourthStyleToTake = \array_values( \array_slice( $this->includedIds, 3, 1, true ) );

        if ( empty( $firstStyleToTake ) ||
            empty( $secondStyleToTake ) ||
            empty( $thirdStyleToTake ) ||
            empty( $fourthStyleToTake ) ) {
            return;
        }

        $includedBeerIds = \array_keys( $this->includedIds );

        if ( $secondStyleToTake[0] * self::MARGIN_STYLES_TO_DISTINGUISH <= $firstStyleToTake[0] ) {
            $this->highlightedIds = [ $includedBeerIds[0] ];
        } else {
            $this->highlightedIds = [ $includedBeerIds[0], $includedBeerIds[1] ];
        }

        if ( $thirdStyleToTake[0] * self::MARGIN_STYLES_TO_DISTINGUISH <= $secondStyleToTake[0] ) {
            $this->highlightedIds = [ $includedBeerIds[0], $includedBeerIds[1] ];
        } else {
            $this->highlightedIds = [ $includedBeerIds[0], $includedBeerIds[1], $includedBeerIds[2] ];
        }

        if ( $fourthStyleToTake[0] * self::MARGIN_STYLES_TO_DISTINGUISH <= $thirdStyleToTake[0] ) {
            $this->highlightedIds = [ $includedBeerIds[0], $this->includedIds[1], $includedBeerIds[2] ];
        } else {
            $this->highlightedIds = null;
        }
    }

    /**
     * Prevents beers to be both included and excluded
     */
    private function removeDuplicated(): void
    {
        $included = \array_slice( $this->includedIds, 0, $this->countStylesToTake, true );
        $excluded = \array_slice( $this->excludedIds, 0, $this->countStylesToAvoid, true );

        foreach ( $included as $id => $points ) {
            if ( \array_key_exists( $id, $excluded ) ) {
                unset( $this->includedIds[$id] );
            }
        }
    }

    /**
     * There must at least 125% margin between included and excluded beer
     * included > excluded
     * If not - remove from excluded
     */
    private function checkMarginBetweenStyles(): void
    {
        foreach ( $this->includedIds as $id => $points ) {
            if ( \array_key_exists( $id, $this->excludedIds ) ) {
                $excludedPoints = $this->excludedIds[$id];
                $includedPoints = $points;
                if ( $includedPoints > $excludedPoints &&
                    $includedPoints <= $excludedPoints * self::MARGIN_INCLUDED_EXCLUDED ) {
                    unset( $this->excludedIds[$id] );
                }
            }
        }
    }

    /**
     * Checks how many styles should be shuffled and shuffle those
     * Margin between previous and next style should be less than 90% of points
     * Margin between first and n-th style should not be less than 80% of points
     */
    private function shuffleIncludedStyles(): void
    {
        $toShuffle = 0;
        $allIncludedStyles = \count( $this->includedIds );
        $firstStylePoints = \array_values( \array_slice( $this->includedIds, 0, 1, true ) )[0];

        for ( $i = 1; $i < $allIncludedStyles; $i++ ) {
            $previousStylePoints = \array_values( \array_slice( $this->includedIds, $i - 1, 1, true ) )[0];
            $followingStylePoints = \array_values( \array_slice( $this->includedIds, $i, 1, true ) )[0];

            if ( $followingStylePoints >= $previousStylePoints * self::POINT_PERCENT_GAP_WITH_PREVIOUS ) {
                $toShuffle++;
            }

            if ( $followingStylePoints <= $previousStylePoints * self::POINT_PERCENT_GAP_WITH_PREVIOUS && $i > 5 ) {
                break; // we want to shuffle from 5 to n-th style, where gap is to much
            }

            if ( $followingStylePoints <= $firstStylePoints * self::POINT_PERCENT_GAP_WITH_FIRST && $i > 5 ) {
                break; // we want to include up to POINT_PERCENT_GAP_WITH_FIRST of first points
            }
        }

        if ( $toShuffle > 4 ) {
            $this->includedIds = \array_keys( \array_slice( $this->includedIds, 0, $toShuffle, true ) );
            \shuffle( $this->includedIds );
            $this->setShuffled( true );
        }
    }
}
