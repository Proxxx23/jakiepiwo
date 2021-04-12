<?php
declare( strict_types=1 );

namespace App\Http\Utils;

use App\Http\Objects\Answers;

final class Exclude
{
    public static function batch( array $answerValue, Answers $userOptions ): void
    {
        // wykluczenia dla piw kawowych
        if ( $answerValue[8] ?? null === 'nie' ) {
            $userOptions->excludeFromRecommended( [ 74, ] );
        }
        if ( $answerValue[8] ?? null === 'tak' ) {
            $userOptions->excludeFromUnsuitable( [ 74, ] );
        }

        // wykluczenia dla piw przyprawowych
        if ( $answerValue[9] ?? null === 'nie' ) {
            $userOptions->excludeFromRecommended( [ 47, 48, 49, 53, 67, 68, ] );
        }
        if ( $answerValue[9] ?? null === 'tak' ) {
            $userOptions->excludeFromUnsuitable( [ 47, 48, 49, 53, 67, 68, ] );
        }

        // wykluczenia dla piw kwaśnych
        if ( $answerValue[11] ?? null === 'nie ma mowy' ) {
            $userOptions->excludeFromRecommended( [ 40, 42, 44, 51, 56, ] );
        }
        if ( $answerValue[11] ?? null === 'chętnie' ) {
            $userOptions->excludeFromUnsuitable( [ 40, 42, 44, 51, 56, ] );
        }

        // wykluczenia dla wędzonek
        if ( $answerValue[12] ?? null === 'nie' ) {
            $userOptions->excludeFromRecommended( [ 15, 16, 52, 57, ] );
        }
        if ( $answerValue[12] ?? null === 'tak' ) {
            $userOptions->excludeFromUnsuitable( [ 15, 16, 52, 57, ] );
        }

        // wykluczenia dla piw lekkich
        if ( $answerValue[3] ?? null === 'coś lekkiego' ) {
            $userOptions->excludeFromRecommended( [ 7, 8, 22, 36, 37, 38, 39, 50, 67, 998, 999, ] );
        }

        // wykluczenia dla piw jasnych
        if ( $answerValue[5] ?? null === 'ciemne' ) {
            $userOptions->excludeFromRecommended( [ 1, 2, 6, 9, 10, 11, 13, 14, 25, 40, 44, 45, 49, 51, 52, 60, 61, 68, 69, 70, 72, 77, 999, ] );
            $userOptions->excludeFromUnsuitable( [ 3, 21, 32, 33, 34, 35, 36, 37, 71, 74, 998, ] );
        }

        // wykluczenia dla piw ciemnych
        if ( $answerValue[5] ?? null === 'jasne' ) {
            $userOptions->excludeFromRecommended( [ 3, 21, 32, 33, 34, 35, 36, 37, 71, 74, 998, ] );
            $userOptions->excludeFromUnsuitable( [ 1, 2, 6, 9, 10, 11, 13, 14, 25, 40, 44, 45, 49, 51, 52, 60, 61, 68, 69, 70, 72, 77, 999, ] );
        }
    }
}
