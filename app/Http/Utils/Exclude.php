<?php
declare( strict_types=1 );

namespace App\Http\Utils;

use App\Http\Objects\Answers;

class Exclude
{
    public static function batch( array $answerValue, Answers $userOptions ): void
    {
        // wykluczenia dla piw kawowych
        if ( isset( $answerValue[8] ) && $answerValue[8] === 'nie' ) {
            $userOptions->excludeFromRecommended( [ 74, ] );
        }
        if ( isset( $answerValue[8] ) && $answerValue[8] === 'tak' ) {
            $userOptions->excludeFromUnsuitable( [ 74, ] );
        }

        // wykluczenia dla piw przyprawowych
        if ( isset( $answerValue[9] ) && $answerValue[9] === 'nie' ) {
            $userOptions->excludeFromRecommended( [ 47, 48, 49, 53, 67, 68, ] );
        }
        if ( isset( $answerValue[9] ) && $answerValue[9] === 'tak' ) {
            $userOptions->excludeFromUnsuitable( [ 47, 48, 49, 53, 67, 68, ] );
        }

        // wykluczenia dla piw kwaśnych
        if ( isset( $answerValue[11] ) && $answerValue[11] === 'nie ma mowy' ) {
            $userOptions->excludeFromRecommended( [ 40, 42, 44, 51, 56, ] );
        }
        if ( isset( $answerValue[11] ) && $answerValue[11] === 'chętnie' ) {
            $userOptions->excludeFromUnsuitable( [ 40, 42, 44, 51, 56, ] );
        }

        // wykluczenia dla wędzonek
        if ( isset( $answerValue[12] ) && $answerValue[12] === 'nie' ) {
            $userOptions->excludeFromRecommended( [ 15, 16, 52, 57, ] );
        }
        if ( isset( $answerValue[12] ) && $answerValue[12] === 'tak' ) {
            $userOptions->excludeFromUnsuitable( [ 15, 16, 52, 57, ] );
        }

        // wykluczenia dla piw lekkich
        if ( isset( $answerValue[3] ) && $answerValue[3] === 'coś lekkiego' ) {
            $userOptions->excludeFromRecommended( [ 7, 8, 22, 36, 37, 38, 39, 50, 67, 998, 999 ] );
        }

        // wykluczenia dla piw ciemnych
        if ( isset( $answerValue[5] ) && $answerValue[5] === 'ciemne' ) {
            $userOptions->excludeFromRecommended( [ 999 ] );
        }

        // wykluczenia dla piw jasnych
        if ( isset( $answerValue[5] ) && $answerValue[5] === 'jasne' ) {
            $userOptions->excludeFromRecommended( [ 3, 21, 33, 34, 35, 36, 37, 71, 74, 998 ] );
        }
    }
}
