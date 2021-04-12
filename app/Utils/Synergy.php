<?php
declare( strict_types=1 );

namespace App\Utils;

use App\Http\Objects\FormData;

class Synergy
{
    public static function apply( array $answerValue, FormData $user ): void
    {
        $userOptions = $user->getAnswers();

        // Lekkie + owocowe + kwaśne
        if ( $answerValue[3] === 'coś lekkiego' &&
            $answerValue[10] === 'tak' &&
            $answerValue[11] === 'chętnie' ) {
            $userOptions->applyPositiveSynergy( [ 40, 56 ], 2 );
            $userOptions->applyPositiveSynergy( [ 51, 77 ], 1.5 );
        }

        // nowe smaki LUB szokujące + jasne i ciężkie
        if ( $answerValue[3] === 'coś ciężkiego' &&
            $answerValue[5] === 'jasne' &&
            ( $answerValue[1] === 'tak' || $answerValue[2] === 'tak' ) ) {
            $userOptions->applyPositiveSynergy( [ 7, 22, 39, 42, 50 ], 2 );
            $userOptions->applyPositiveSynergy( [ 76 ], 1.25 );
        }

        // nowe smaki LUB szokujące + jasne i średnie
        if ( $answerValue[3] === 'coś pośrodku' &&
            $answerValue[5] === 'jasne' &&
            ( $answerValue[1] === 'tak' || $answerValue[2] === 'tak' ) ) {
            $userOptions->applyPositiveSynergy( [ 6, 15, 16, 42, 60, 76 ], 2 );
            $userOptions->applyPositiveSynergy( [ 20, 39, 39 ], 1.5 );
        }

        // nowe smaki LUB szokujące + złożone + ciemne
        if ( $answerValue[3] === 'coś ciężkiego' &&
            $answerValue[5] === 'ciemne' &&
            ( $answerValue[1] === 'tak' || $answerValue[2] === 'tak' ) ) {
            $userOptions->applyPositiveSynergy( [ 36, 37 ], 2 );
        }

        // złożone + ciemne + nieowocowe
        if ( $answerValue[3] === 'coś ciężkiego' &&
            $answerValue[5] === 'ciemne' &&
            $answerValue[10] === 'nie' ) {
            $userOptions->applyPositiveSynergy( [ 3, 35, 36, 37, 48 ], 1.5 );
        }

        // złożone + ciemne + nieowocowe + kawowe
        if ( $answerValue[3] === 'coś pośrodku' &&
            $answerValue[5] === 'ciemne' &&
            $answerValue[8] === 'tak' &&
            $answerValue[10] === 'nie' ) {
            $userOptions->applyPositiveSynergy( [ 74 ], 2.5 );
        }

        // Lekkie + ciemne + słodkie + goryczka (ledwie || lekka)
        if ( $answerValue[3] === 'coś lekkiego' &&
            $answerValue[5] === 'ciemne' &&
            $answerValue[6] === 'słodsze' &&
            !\in_array( $answerValue[4], [ 'wyraźnie gorzkiego', 'jestem hopheadem' ], true ) ) {
            $userOptions->applyPositiveSynergy( [ 1, 2, 29, 30, 34 ], 2 );
            $userOptions->applyNegativeSynergy( [ 36, 37 ], 3 );
        }

        // jasne + nieczekoladowe
        if ( $answerValue[5] === 'jasne' &&
            $answerValue[7] === 'nie' ) {
            $userOptions->applyNegativeSynergy(
                [ 21, 29, 32, 33, 34, 35, 36, 37, 71, 74 ], 2
            );
            $userOptions->applyNegativeSynergy( [ 30 ], 1.5 );
        }

        // ciemne + czekoladowe + nieciężkie
        if ( $answerValue[5] === 'ciemne' &&
            $answerValue[7] === 'tak' &&
            $answerValue[3] !== 'coś ciężkiego' ) {
            $userOptions->applyPositiveSynergy( [ 21, 32, 33, 34, 35, 71 ], 2.5 );
            $userOptions->applyPositiveSynergy( [ 3, 30 ], 1.5 );
        }

        // goryczka ledwo || lekka + jasne + nieczekoladowe + nieciężkie
        if ( $answerValue[5] === 'jasne' &&
            $answerValue[7] === 'nie' &&
            $answerValue[3] !== 'coś ciężkiego' &&
            \in_array( $answerValue[4], [ 'prawie bez goryczki', 'z wyczuwalną goryczką' ], true ) ) {
            $userOptions->applyPositiveSynergy( [ 25, 44, 45, 47, 51, 52, 53, 68, ], 2 );
            $userOptions->applyPositiveSynergy( [ 73, 77 ], 1.25 );
            $userOptions->applyNegativeSynergy( [ 3, 30, 35, 36, 37, 71 ], 2 );
        }

        // jasne + lekkie + wędzone = grodziskie
        if ( $answerValue[3] === 'coś lekkiego' &&
            $answerValue[5] === 'jasne' &&
            $answerValue[12] === 'tak' ) {
            $userOptions->applyPositiveSynergy( [ 52 ], 3 );
            $userOptions->applyNegativeSynergy( [ 3, 22, 35, 36, 37, 50, 71 ], 2 );
        }

        // duża/hophead goryczka + jasne
        if ( $answerValue[5] === 'jasne' &&
            \in_array( $answerValue[4], [ 'wyraźnie gorzkiego', 'jestem hopheadem' ], true ) ) {
            $userOptions->applyPositiveSynergy( [ 1, 2, 5, 6, 7, 8, 61 ], 1.75 );
            $userOptions->applyPositiveSynergy( [ 6, 69, 70, 72 ], 1.5 );
            $userOptions->applyNegativeSynergy( [ 14, 25, 45, 47 ], 1.75 );
        }

        // duża/hophead goryczka + ciemne
        if ( $answerValue[5] === 'ciemne' &&
            \in_array( $answerValue[4], [ 'wyraźnie gorzkiego', 'jestem hopheadem' ], true ) ) {
            $userOptions->applyPositiveSynergy( [ 3, 32, 36, 37 ], 2.5 );
        }

        // goryczka ledwo || lekka
        if ( $answerValue[4] === 'prawie bez goryczki' || $answerValue[4] === 'z wyczuwalną goryczką' ) {
            $userOptions->applyNegativeSynergy( [ 1, 2, 3, 5, 7, 8, 61 ], 2 );
            $userOptions->applyNegativeSynergy( [ 6, 32, 69, 71, 72 ], 1.5 );
        }

        // goryczka ledwo && jasne && owoce && słodki && nowe smaki = pastry pale
        if ( $answerValue[1] === 'tak' &&
            $answerValue[4] === 'prawie bez goryczki' &&
            $answerValue[5] === 'jasne' &&
            $answerValue[6] === 'słodsze' &&
            $answerValue[10] === 'tak' ) {
            $userOptions->applyPositiveSynergy( [ 77, 999 ], 3 );
        }

        // goryczka ledwo && ciemne && nie owoce && słodki && nowe smaki = pastry black
        if ( $answerValue[1] === 'tak' &&
            $answerValue[4] === 'prawie bez goryczki' &&
            $answerValue[5] === 'ciemne' &&
            $answerValue[6] === 'słodsze' &&
            $answerValue[10] === 'nie' ) {
            $userOptions->applyPositiveSynergy( [ 998 ], 3 );
        }
    }
}
