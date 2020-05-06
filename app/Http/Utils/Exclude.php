<?php
declare( strict_types=1 );

namespace App\Http\Utils;

use App\Http\Objects\Answers;

class Exclude
{
    public static function batch( array $answers, Answers $userOptions ): void
    {
        // wykluczenia dla piw kwaśnych
        if ( isset( $answers[12] ) && $answers[12] === 'nie ma mowy' ) {
            $userOptions->excludeFromRecommended( [ 40, 42, 44, 51, 56, ] );
        } elseif ( isset( $answers[12] ) && $answers[12] === 'chętnie' ) {
            $userOptions->excludeFromUnsuitable( [ 40, 42, 44, 51, 56, ] );
        }

        // wykluczenia dla wędzonek
        if ( isset( $answers[13] ) && $answers[13] === 'nie' ) {
            $userOptions->excludeFromRecommended( [ 15, 16, 52, 57, ] );
        } elseif ( isset( $answers[13] ) && $answers[13] === 'tak' ) {
            $userOptions->excludeFromUnsuitable( [ 15, 16, 52, 57, ] );
        }

        // wykluczenia dla piw lekkich
        if ( isset( $answers[3] ) && $answers[3] === 'coś lekkiego' ) {
            $userOptions->excludeFromRecommended( [ 7, 8, 22, 24, 36, 37, 39, 50, 998, 999 ] );
        }
    }
}
