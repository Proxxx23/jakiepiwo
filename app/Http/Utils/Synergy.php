<?php
declare( strict_types=1 );

namespace App\Http\Utils;

use App\Http\Objects\FormData;

class Synergy
{
    public static function apply( array $answerValue, FormData $user ): void
    {
        $userOptions = $user->getAnswers();

        // Lekkie + owocowe + Kwaśne
        if ( $answerValue[4] === 'coś lekkiego' &&
            $answerValue[12] === 'tak' &&
            $answerValue[13] === 'chętnie' ) {
            $userOptions->applyPositiveSynergy( [ 40, 56 ], 2 );
            $userOptions->applyPositiveSynergy( [ 51 ], 1.5 );
        }

        // nowe smaki LUB szokujące + złożone + jasne
        if ( $answerValue[4] === 'coś złożonego' &&
            $answerValue[4] === 'jasne' &&
            ( $answerValue[2] === 'tak' || $answerValue[3] === 'tak' ) ) {
            $userOptions->applyPositiveSynergy( [ 7, 15, 16, 22, 39, 42, 50, 60, 73 ], 2 );
        }

        // nowe smaki LUB szokujące + złożone + ciemne
        if ( $answerValue[4] === 'coś złożonego' &&
            $answerValue[4] === 'ciemne' &&
            ( $answerValue[2] === 'tak' || $answerValue[3] === 'tak' ) ) {
            $userOptions->applyPositiveSynergy( [ 36, 37 ], 2 );
        }

        // złożone + ciemne + nieowocowe
        if ( $answerValue[4] === 'coś złożonego' &&
            $answerValue[6] === 'ciemne' &&
            $answerValue[12] === 'nie' ) {
            $userOptions->applyPositiveSynergy( [ 3, 24, 35, 36, 37, 48 ], 1.5 );
        }

        // złożone + ciemne + nieowocowe + kawowe
        if ( $answerValue[4] === 'coś złożonego' &&
            $answerValue[6] === 'ciemne' &&
            $answerValue[10] === 'tak' &&
            $answerValue[12] === 'nie' ) {
            $userOptions->applyPositiveSynergy( [ 74 ], 2.5 );
        }

        // Lekkie + ciemne + słodkie + goryczka (ledwie || lekka || wyczuwalna)
        if ( $answerValue[4] === 'coś lekkiego' &&
            $answerValue[6] === 'ciemne' &&
            $answerValue[7] === 'słodsze' &&
            !\in_array( $answerValue[5], [ 'zdecydowanie wyczuwalną', 'jestem hopheadem' ], true ) ) {
            $userOptions->applyPositiveSynergy( [ 12, 29, 30, 34, 64 ], 2 );
            $userOptions->applyNegativeSynergy( [ 36, 37 ], 3 );
        }

        // jasne + nieczekoladowe
        if ( $answerValue[6] === 'jasne' &&
            $answerValue[9] === 'nie' ) {
            $userOptions->applyNegativeSynergy(
                [ 12, 21, 24, 29, 33, 34, 35, 36, 37, 71, 74 ], 2
            );
        }

        // ciemne + czekoladowe + lżejsze
        if ( $answerValue[6] === 'ciemne' &&
            $answerValue[9] === 'tak' &&
            $answerValue[8] !== 'mocne i gęste' ) {
            $userOptions->applyPositiveSynergy( [ 12, 33, 34, 35, 71 ], 2.5 );
        }


        // goryczka ledwo || lekka + jasne + nieczekoladowe + niegęste
        if ( $answerValue[6] === 'jasne' &&
            $answerValue[9] === 'nie' &&
            $answerValue[8] !== 'mocne i gęste' &&
            \in_array( $answerValue[5], [ 'ledwie wyczuwalną', 'lekką' ], true ) ) {
            $userOptions->applyPositiveSynergy( [ 20, 25, 40, 44, 45, 47, 51, 52, 53, 68, 73 ], 2 );
            $userOptions->applyNegativeSynergy( [ 3, 24, 35, 36, 37, 71 ], 2 );
        }

        // jasne + lekkie + wodniste + wędzone = grodziskie
        if ( $answerValue[4] === 'coś lekkiego' &&
            $answerValue[6] === 'jasne' &&
            $answerValue[8] === 'wodniste' &&
            $answerValue[14] === 'tak' ) {
            $userOptions->applyPositiveSynergy( [ 52 ], 3 );
            $userOptions->applyNegativeSynergy( [ 3, 22, 24, 35, 36, 37, 50, 71 ], 2 );
        }

        // duża/hophead goryczka + jasne
        if ( $answerValue[6] === 'jasne' &&
            \in_array( $answerValue[5], [ 'zdecydowanie wyczuwalną', 'jestem hopheadem' ], true ) ) {
            $userOptions->applyPositiveSynergy( [ 1, 2, 5, 6, 7, 8, 61 ], 1.75 );
            $userOptions->applyPositiveSynergy( [ 69, 70, 72 ], 1.5 );
            $userOptions->applyNegativeSynergy( [ 14, 25, 45, 47 ], 1.75 );
        }

        // duża/hophead goryczka + ciemne
        if ( $answerValue[6] === 'ciemne' &&
            \in_array( $answerValue[5], [ 'zdecydowanie wyczuwalną', 'jestem hopheadem' ], true ) ) {
            $userOptions->applyPositiveSynergy( [ 3, 36, 37 ], 1.75 );
        }

        // goryczka ledwo || lekka
        if ( $answerValue[5] === 'ledwie wyczuwalną' || $answerValue[5] === 'lekką' ) {
            $userOptions->applyNegativeSynergy( [ 1, 2, 3, 5, 7, 8, 61 ], 2 );
            $userOptions->applyNegativeSynergy( [ 6, 60, 69, 71, 72 ], 1.5 );
        }
    }
}
