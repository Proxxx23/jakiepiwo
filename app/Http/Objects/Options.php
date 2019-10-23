<?php
declare( strict_types=1 );

namespace App\Http\Objects;

/**
 * @method getIncludedIds()
 * @method getExcludedIds()
 * @method getMustTakeOpt()
 * @method getMustAvoidOpt()
 * @method setBarrelAged( bool $param )
 * @method getBarrelAged()
 * @method getCountStylesToTake()
 * @method getCountStylesToAvoid()
 */
final class Options extends AbstractFixedPropertyObject implements OptionsInterface
{
    protected const POINT_PERCENT_GAP = 0.90;

    /** @var array */
    protected $includedIds = [];
    /** @var array */
    protected $excludedIds = [];
    /** @var bool */
    protected $mustTakeOpt = false;
    /** @var bool */
    protected $mustAvoidOpt = false;
    /** @var bool */
    protected $barrelAged = false;
    /** @var bool */
    protected $shuffled = false;
    /** @var int */
    protected $countStylesToTake = 3;
    /** @var int */
    protected $countStylesToAvoid = 3;

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

    protected function sortIncludedIds(): void
    {
        \arsort( $this->includedIds );
    }

    protected function sortExcludedIds(): void
    {
        \arsort( $this->excludedIds );
    }

    /**
     * If there's an 4th and 5rd style with a little 'margin" to 3rd style
     * Takes 4th or 5th style as an extra styles to take or avoid
     */
    protected function fetchOptionalStyles(): void
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
    protected function fetchStylesToTakeAndAvoid(): void
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
    protected function removeDuplicates(): void
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
    protected function checkMarginBetweenBeerStyles(): void
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
    protected function checkHowManyStylesShouldBeShuffled(): void
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
    protected function shuffleStyles( int $toShuffle ): void
    {
        $this->includedIds = \array_keys( \array_slice( $this->includedIds, 0, $toShuffle, true ) );

        \shuffle( $this->includedIds );

        $this->shuffled = true;
    }
}
