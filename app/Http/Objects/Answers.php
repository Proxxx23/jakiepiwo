<?php
declare( strict_types=1 );

namespace App\Http\Objects;

final class Answers implements AnswersInterface
{
    private const POINT_PERCENT_GAP = 0.90;

    /** @var array */
    private $includedIds = [];
    /** @var array */
    private $excludedIds = [];
    /** @var bool */
    private $mustTakeOpt = false;
    /** @var bool */
    private $mustAvoidOpt = false;
    /** @var bool */
    private $barrelAged = false;
    /** @var bool */
    private $shuffled = false;
    /** @var int */
    private $countStylesToTake = 3;
    /** @var int */
    private $countStylesToAvoid = 3;

    /**
     * @return array
     */
    public function getIncludedIds(): array
    {
        return $this->includedIds;
    }

    /**
     * @param array $includedIds
     *
     * @return Answers
     */
    public function setIncludedIds( array $includedIds ): Answers
    {
        $this->includedIds = $includedIds;

        return $this;
    }

    /**
     * @return array
     */
    public function getExcludedIds(): array
    {
        return $this->excludedIds;
    }

    /**
     * @param array $excludedIds
     *
     * @return Answers
     */
    public function setExcludedIds( array $excludedIds ): Answers
    {
        $this->excludedIds = $excludedIds;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMustTakeOpt(): bool
    {
        return $this->mustTakeOpt;
    }

    /**
     * @param bool $mustTakeOpt
     *
     * @return Answers
     */
    public function setMustTakeOpt( bool $mustTakeOpt ): Answers
    {
        $this->mustTakeOpt = $mustTakeOpt;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMustAvoidOpt(): bool
    {
        return $this->mustAvoidOpt;
    }

    /**
     * @param bool $mustAvoidOpt
     *
     * @return Answers
     */
    public function setMustAvoidOpt( bool $mustAvoidOpt ): Answers
    {
        $this->mustAvoidOpt = $mustAvoidOpt;

        return $this;
    }

    /**
     * @return bool
     */
    public function isBarrelAged(): bool
    {
        return $this->barrelAged;
    }

    /**
     * @param bool $barrelAged
     *
     * @return Answers
     */
    public function setBarrelAged( bool $barrelAged ): Answers
    {
        $this->barrelAged = $barrelAged;

        return $this;
    }

    /**
     * @return bool
     */
    public function isShuffled(): bool
    {
        return $this->shuffled;
    }

    /**
     * @param bool $shuffled
     *
     * @return Answers
     */
    public function setShuffled( bool $shuffled ): Answers
    {
        $this->shuffled = $shuffled;

        return $this;
    }

    /**
     * @return int
     */
    public function getCountStylesToTake(): int
    {
        return $this->countStylesToTake;
    }

    /**
     * @param int $countStylesToTake
     *
     * @return Answers
     */
    public function setCountStylesToTake( int $countStylesToTake ): Answers
    {
        $this->countStylesToTake = $countStylesToTake;

        return $this;
    }

    /**
     * @return int
     */
    public function getCountStylesToAvoid(): int
    {
        return $this->countStylesToAvoid;
    }

    /**
     * @param int $countStylesToAvoid
     *
     * @return Answers
     */
    public function setCountStylesToAvoid( int $countStylesToAvoid ): Answers
    {
        $this->countStylesToAvoid = $countStylesToAvoid;

        return $this;
    }

    /**
     * @param int $styleId
     * @param float $strength
     */
    public function addToIncluded( int $styleId, float $strength ): void
    {
        $this->includedIds[$styleId] = $strength;
    }

    /**
     * @param int $styleId
     * @param float $strength
     */
    public function addStrengthToIncluded( int $styleId, float $strength ): void
    {
        $this->includedIds[$styleId] += $strength;
    }

    /**
     * @param int $styleId
     * @param float $strength
     */
    public function addToExcluded( int $styleId, float $strength ): void
    {
        $this->excludedIds[$styleId] = $strength;
    }

    /**
     * @param int $styleId
     * @param float $strength
     */
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
    public function buildPositiveSynergy( array $idsToMultiply, float $multiplier ): void
    {
        foreach ( $idsToMultiply as $id ) {
            $this->includedIds[$id] *= $multiplier;
        }
    }

    /**
     * Builds negative synergy if user ticks 2-4 particular answers
     *
     * @param array $idsToDivide
     * @param float $divider
     */
    public function buildNegativeSynergy( array $idsToDivide, float $divider ): void
    {
        foreach ( $idsToDivide as $id ) {
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
            $this->includedIds[$id] = 0;
        }
    }

    /**
     * Remove points assigned to beer ids
     */
    public function removeAssignedPoints(): void
    {
        $this->includedIds = ( $this->shuffled === false )
            ? \array_keys( $this->includedIds )
            : \array_values( $this->includedIds );

        $this->excludedIds = \array_keys( $this->excludedIds );
    }

    /**
     * Facade
     */
    public function fetchAll(): void
    {
        $this->sortIncludedIds();
        $this->sortExcludedIds();
        $this->fetchOptionalStyles();
        $this->removeDuplicates();
        $this->checkMarginBetweenBeerStyles();
        $this->fetchStylesToTakeAndAvoid();
        $this->checkHowManyStylesShouldBeShuffled();
    }

    private function sortIncludedIds(): void
    {
        \arsort( $this->includedIds );
    }

    private function sortExcludedIds(): void
    {
        \arsort( $this->excludedIds );
    }

    /**
     * If there's an 4th and 5rd style with a little 'margin" to 3rd style
     * Takes 4th or 5th style as an extra styles to take or avoid
     */
    private function fetchOptionalStyles(): void
    {
        $thirdStyleToTake = \array_values( \array_slice( $this->includedIds, 0, 3, true ) );
        $thirdStyleToAvoid = \array_values( \array_slice( $this->excludedIds, 0, 3, true ) );

        for ( $i = 3; $i <= 4; $i++ ) {

            $toTakeChunk = \array_values( \array_slice( $this->includedIds, 0, $i, true ) );
            if ( $toTakeChunk[0] >= ( $thirdStyleToTake[0] / 100 * 90 ) ) {
                $this->countStylesToTake++;
            }

            $toAvoidChunk = \array_values( \array_slice( $this->excludedIds, 0, $i, true ) );
            if ( $toAvoidChunk[0] >= ( $thirdStyleToAvoid[0] / 100 * 90 ) ) {
                $this->countStylesToAvoid++;
            }
        }
    }

    /**
     * If 1st styles to take and avoid has more than/equal 150% points of 2nd or 3rd styles
     * Emphasize them!
     */
    private function fetchStylesToTakeAndAvoid(): void
    {
        $firstStyleToTake = \array_values( \array_slice( $this->includedIds, 0, 1, true ) );
        $firstStyleToAvoid = \array_values( \array_slice( $this->excludedIds, 0, 1, true ) );

        $secondStyleToTake = \array_values( \array_slice( $this->includedIds, 1, 1, true ) );
        $secondStyleToAvoid = \array_values( \array_slice( $this->excludedIds, 1, 1, true ) );

        $thirdStyleToTake = \array_values( \array_slice( $this->includedIds, 2, 1, true ) );
        $thirdStyleToAvoid = \array_values( \array_slice( $this->excludedIds, 2, 1, true ) );

        if ( $secondStyleToTake[0] * 1.25 <= $firstStyleToTake[0] ||
            $thirdStyleToTake[0] * 1.25 <= $firstStyleToTake[0] ) {
            $this->mustTakeOpt = true;
        }

        if ( $secondStyleToAvoid[0] * 1.25 <= $firstStyleToAvoid[0] ||
            $thirdStyleToAvoid[0] * 1.25 <= $firstStyleToAvoid[0] ) {
            $this->mustAvoidOpt = true;
        }
    }

    /**
     * Prevents beers to be both included and excluded
     */
    private function removeDuplicates(): void
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
     */
    private function checkMarginBetweenBeerStyles(): void
    {
        foreach ( $this->includedIds as $id => $points ) {
            if ( \array_key_exists( $id, $this->excludedIds ) ) {
                $excludedPoints = $this->excludedIds[$id];
                $includedPoints = $points;
                if ( $includedPoints > $excludedPoints && $includedPoints <= $excludedPoints * 1.25 ) {
                    unset( $this->excludedIds[$id] );
                }
            }
        }
    }

    /**
     * Checks how many styles should be shuffled.
     * Margin between first and n-th style should be less than 90% of points).
     */
    private function checkHowManyStylesShouldBeShuffled(): void
    {
        //		$firstStyleIndex = key(array_slice($this->includedIds, 0, 1, true));

        $toShuffle = 0;
        $countIncluded = \count( $this->includedIds );
        $firstStylePoints = \array_values( \array_slice( $this->includedIds, 0, 1, true ) );

        for ( $i = 1; $i < $countIncluded; $i++ ) {
            $nthStylePoints = \array_values( \array_slice( $this->includedIds, $i, 1, true ) );
            if ( $nthStylePoints[0] >= $firstStylePoints[0] * self::POINT_PERCENT_GAP ) {
                $toShuffle++;
            }
        }

        if ( $toShuffle > 4 ) {
            $this->shuffleStyles( $toShuffle );
        }
    }

    /**
     * Shuffle n-elements of an includedIds array
     *
     * @param int $toShuffle
     */
    private function shuffleStyles( int $toShuffle ): void
    {
        $this->includedIds = \array_keys( \array_slice( $this->includedIds, 0, $toShuffle, true ) );

        \shuffle( $this->includedIds );

        $this->shuffled = true;
    }
}
