<?php
declare( strict_types=1 );

namespace App\Http\Objects;

class Answers
{
    private const POINT_PERCENT_GAP_WITH_PREVIOUS = 0.90;
    private const POINT_PERCENT_GAP_WITH_FIRST = 0.80;
    private const MARGIN_INCLUDED_EXCLUDED = 1.25;
    private const MARGIN_STYLES_TO_DISTINGUISH = 1.25;
    private const MARGIN_PERCENT_FOR_OPTIONAL_TO_SHOW = 90;

    private bool $barrelAged = false;
    private int $countRecommended = 3;
    private int $countUnsuitable = 3;
    /** @var float[]|null[] */
    private array $unsuitableIds = [];
    /** @var float[]|null */
    private ?array $highlightedIds = null;
    /** @var float[]|null[] */
    private array $recommendedIds = [];
    private bool $smoked = false;
    private bool $sour = false;
    private bool $coffee = false;
    private bool $chocolate = false;
    private bool $shuffled = false;

    public function getRecommendedIds(): array
    {
        return $this->recommendedIds;
    }

    public function getUnsuitableIds(): array
    {
        return $this->unsuitableIds;
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

    public function isSour(): bool
    {
        return $this->sour;
    }

    public function setSour( bool $sour ): Answers
    {
        $this->sour = $sour;

        return $this;
    }

    public function isCoffee(): bool
    {
        return $this->coffee;
    }

    public function setCoffee( bool $coffee ): Answers
    {
        $this->coffee = $coffee;

        return $this;
    }

    public function isChocolate(): bool
    {
        return $this->chocolate;
    }

    public function setChocolate( bool $chocolate ): Answers
    {
        $this->chocolate = $chocolate;

        return $this;
    }

    public function setShuffled( bool $shuffled ): void
    {
        $this->shuffled = $shuffled;
    }

    public function getCountRecommended(): int
    {
        return $this->countRecommended;
    }

    public function getCountUnsuitable(): int
    {
        return $this->countUnsuitable;
    }

    public function getHighlightedIds(): ?array
    {
        return $this->highlightedIds;
    }

    public function addToRecommended( int $styleId, float $strength ): void
    {
        $this->recommendedIds[$styleId] = $strength;
    }

    public function addStrengthToRecommended( int $styleId, float $strength ): void
    {
        $this->recommendedIds[$styleId] += $strength;
    }

    public function addToUnsuitable( int $styleId, float $strength ): void
    {
        $this->unsuitableIds[$styleId] = $strength;
    }

    public function addStrengthToUnsuitable( int $styleId, float $strength ): void
    {
        $this->unsuitableIds[$styleId] += $strength;
    }

    // Builds positive synergy if user ticks 2-4 particular answers
    public function applyPositiveSynergy( array $idsToMultiply, float $multiplier ): self
    {
        foreach ( $idsToMultiply as $id ) {
            if ( !isset( $this->recommendedIds[$id] ) ) {
                $this->recommendedIds[$id] = $multiplier;
                continue;
            }
            $this->recommendedIds[$id] *= $multiplier;
        }

        return $this;
    }

    // Builds negative synergy if user ticks 2-4 particular answers
    public function applyNegativeSynergy( array $idsToDivide, float $divider ): self
    {
        foreach ( $idsToDivide as $id ) {
            if ( !isset( $this->unsuitableIds[$id] ) ) {
                continue;
            }
            $this->unsuitableIds[$id] = \floor( $this->unsuitableIds[$id] / $divider );
        }

        return $this;
    }

    // Excludes sour/smoked beers from recommended styles if user says NO
    public function excludeFromRecommended( array $idsToExclude ): void
    {
        foreach ( $idsToExclude as $id ) {
            if ( !isset( $this->recommendedIds[$id] ) ) {
                continue;
            }
            $this->recommendedIds[$id] = null;
        }
    }

    // Excludes sour/smoked beers from not recommended styles if user says YES
    public function excludeFromUnsuitable( array $idsToExclude ): void
    {
        foreach ( $idsToExclude as $id ) {
            if ( !isset( $this->unsuitableIds[$id] ) ) {
                continue;
            }
            $this->unsuitableIds[$id] = null;
        }
    }

    /**
     * Remove points assigned to beer ids, transforms from [id => strength] to [index => id]
     */
    public function removeAssignedPoints(): void
    {
        $this->recommendedIds = ( $this->shuffled === false )
            ? \array_keys( $this->recommendedIds )
            : \array_values( $this->recommendedIds ); //todo: wybadać, jak ma się to zachowywać!!!
        $this->unsuitableIds = \array_keys( $this->unsuitableIds );
    }

    public function prepareAll(): Answers
    {
        $this->sortRecommendedAndUnsuitable();
        $this->retrieveOptionalStyles();
        $this->removeDuplicated();
        $this->checkMarginBetweenStyles();
        $this->retrieveRecommendedStyles();
        $this->shuffleRecommendedStyles();

        return $this;
    }

    private function sortRecommendedAndUnsuitable(): void
    {
        \arsort( $this->recommendedIds );
        \arsort( $this->unsuitableIds );
    }

    /**
     * If there's an 4th and 5rd style with a little 'margin" to 3rd style
     * Takes 4th or 5th style as an extra styles to take or avoid
     */
    private function retrieveOptionalStyles(): void
    {
        $thirdRecommendedStyle = \array_values( \array_slice( $this->recommendedIds, 0, 3, true ) );
        $thirdUnsuitableStyle = \array_values( \array_slice( $this->unsuitableIds, 0, 3, true ) );

        //todo: remove for loop
        for ( $i = 3; $i <= 4; $i++ ) {

            $recommendedChunk = \array_values( \array_slice( $this->recommendedIds, 0, $i, true ) );
            if ( empty( $recommendedChunk ) ) {
                continue;
            }

            if ( $recommendedChunk[0] >= ( $thirdRecommendedStyle[0] / 100 * self::MARGIN_PERCENT_FOR_OPTIONAL_TO_SHOW ) ) {
                $this->countRecommended++;
            }

            $unsuitableChunk = \array_values( \array_slice( $this->unsuitableIds, 0, $i, true ) );
            if ( empty( $unsuitableChunk ) ) {
                continue;
            }

            if ( $unsuitableChunk[0] >= ( $thirdUnsuitableStyle[0] / 100 * self::MARGIN_PERCENT_FOR_OPTIONAL_TO_SHOW ) ) {
                $this->countUnsuitable++;
            }
        }
    }

    /**
     * Check if consecutive style has more or less than 75% of previous style and emphasize it
     * It works like this:
     * - check if 1st style has 125% of points of 2nd style. If yes - 1st style is distinguish
     * - if not - distinguish 1st and 2nd and check 3rd with 2nd etc etc.
     *
     * todo: refactor slightly
     */
    private function retrieveRecommendedStyles(): void
    {
        $firstFiveRecommended = \array_values( \array_slice( $this->recommendedIds, 0, 5, true ) );
        if ( \in_array( null, $firstFiveRecommended, true ) || \in_array( [], $firstFiveRecommended, true ) ) {
            return;
        }

        //todo: musi być jakaś ogólna maksymalna pula punktów, aby to wykminić

        $firstRecommended = \array_values( \array_slice( $this->recommendedIds, 0, 1, true ) )[0];
        $secondRecommended = \array_values( \array_slice( $this->recommendedIds, 1, 1, true ) )[0];
        $thirdRecommended = \array_values( \array_slice( $this->recommendedIds, 2, 1, true ) )[0];
        $fourthRecommended = \array_values( \array_slice( $this->recommendedIds, 3, 1, true ) )[0];
        $fifthRecommended = \array_values( \array_slice( $this->recommendedIds, 4, 1, true ) )[0];
        $sixthRecommended = \array_values( \array_slice( $this->recommendedIds, 5, 1, true ) )[0];

        $recommendedIds = \array_keys( $this->recommendedIds );

        if ( $secondRecommended * self::MARGIN_STYLES_TO_DISTINGUISH <= $firstRecommended ) {
            $this->highlightedIds = [ $recommendedIds[0] ];
            return;
        } else {
            $this->highlightedIds = [ $recommendedIds[0], $recommendedIds[1] ];
        }

        if ( $thirdRecommended * self::MARGIN_STYLES_TO_DISTINGUISH <= $secondRecommended ) {
            $this->highlightedIds = [ $recommendedIds[0], $recommendedIds[1] ];
            return;
        } else {
            $this->highlightedIds = [ $recommendedIds[0], $recommendedIds[1], $recommendedIds[2] ];
        }

        if ( $fourthRecommended * self::MARGIN_STYLES_TO_DISTINGUISH <= $thirdRecommended ) {
            $this->highlightedIds = [ $recommendedIds[0], $recommendedIds[1], $recommendedIds[2] ];
            return;
        } else {
            $this->highlightedIds = [ $recommendedIds[0], $recommendedIds[1], $recommendedIds[2], $recommendedIds[3] ];
        }

        if ( $fifthRecommended * self::MARGIN_STYLES_TO_DISTINGUISH <= $fourthRecommended ) {
            $this->highlightedIds = [ $recommendedIds[0], $recommendedIds[1], $recommendedIds[2], $recommendedIds[3] ];
            return;
        } else {
            $this->highlightedIds = [
                $recommendedIds[0],
                $recommendedIds[1],
                $recommendedIds[2],
                $recommendedIds[3],
                $recommendedIds[4],
            ];
        }

        if ( $sixthRecommended * self::MARGIN_STYLES_TO_DISTINGUISH <= $fifthRecommended ) {
            $this->highlightedIds = [
                $recommendedIds[0],
                $recommendedIds[1],
                $recommendedIds[2],
                $recommendedIds[3],
                $recommendedIds[4],
            ];
            return;
        } else {
            $this->highlightedIds = null;
        }
    }

    /**
     * Prevents beers to be both included and excluded
     */
    private function removeDuplicated(): void
    {
        $recommended = \array_slice( $this->recommendedIds, 0, $this->countRecommended, true );
        $unsuitable = \array_slice( $this->unsuitableIds, 0, $this->countUnsuitable, true );

        foreach ( $recommended as $id => $points ) {
            if ( \array_key_exists( $id, $unsuitable ) ) {
                unset( $this->recommendedIds[$id] );
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
        foreach ( $this->recommendedIds as $id => $recommendedPoints ) {
            if ( !\array_key_exists( $id, $this->unsuitableIds ) ) {
                continue;
            }
            $unsuitablePoints = $this->unsuitableIds[$id];
            if ( $recommendedPoints > $unsuitablePoints &&
                $recommendedPoints <= $unsuitablePoints * self::MARGIN_INCLUDED_EXCLUDED ) {
                unset( $this->unsuitableIds[$id] );
            }
        }
    }

    /**
     * Checks how many styles should be shuffled and shuffle those
     * Margin between previous and next style should be less than 90% of points
     * Margin between first and n-th style should not be less than 80% of points
     */
    private function shuffleRecommendedStyles(): void
    {
        if ( $this->highlightedIds !== null ) {
            return; //we do not shuffle if we have recommended styles
        }

        $toShuffle = 0;
        $allRecommendedStylesCount = \count( $this->recommendedIds );
        $firstStylePoints = \array_values( \array_slice( $this->recommendedIds, 0, 1, true ) )[0];

        for ( $i = 1; $i < $allRecommendedStylesCount; $i++ ) {
            $previousStylePoints = \array_values( \array_slice( $this->recommendedIds, $i - 1, 1, true ) )[0];
            $followingStylePoints = \array_values( \array_slice( $this->recommendedIds, $i, 1, true ) )[0];

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
            $this->recommendedIds = \array_keys( \array_slice( $this->recommendedIds, 0, $toShuffle, true ) );
            \shuffle( $this->recommendedIds );
            $this->setShuffled( true );
        }
    }
}
