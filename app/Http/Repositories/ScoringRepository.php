<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

final class ScoringRepository implements ScoringRepositoryInterface
{
    private const SCORE = [
        // Czy smakują Ci lekkie piwa koncernowe dostępne w sklepach?
        [
            'tak' => '9:2, 10:2, 11:2, 13:2, 14:2, 25:2, 27:2, 45:2, 52:2, 68:2, 70:2, 77, 72:2, 76:2',
            'nie' => '5, 6, 7, 8, 22, 30, 32, 33, 34, 35, 36, 37, 38, 39, 40, 42, 44, 47, 48, 49, 50, 51, 53, 55, 56, 57, 60, 61, 67, 69, 71, 73, 74, 77, 998, 999',
        ],
        // Czy chcesz poznać nowe smaki?
        [
            'tak' => '1, 2, 3, 4, 5, 6, 7, 8, 15, 16, 19, 20, 22, 30, 33, 34, 35, 36, 37, 38, 39, 40, 42, 44, 45, 47, 50, 51, 52, 53, 55, 56, 57, 60, 61, 67, 69, 70, 71, 72, 73, 74, 77, 998, 999',
            'nie' => '9:2, 10:2, 11:2, 13:2, 14, 21, 25, 27, 32, 48, 49, 68, 72:2, 76',
        ],
        // Czy wolałbyś poznać wyłącznie style, które potrafią zszokować?
        [
            'tak' => '1:1.5, 2:2.5, 3:2.5, 5:2.5, 6:2.5, 7:2.5, 8:2.5, 15:2.5, 16:2.5, 32:1.5, 36:2.5, 37:2.5, 40:2.5, 42:2.5, 44:2.5, 50:2.5, 51:2.5, 55:2, 56:2.5, 57:2.5, 60:1.5, 61:1.5, 73:2, 74:2, 998:2.5, 999:2.5',
            'nie' => null,
        ],
        // Chcesz czegoś lekkiego do ugaszenia pragnienia, czy złożonego i degustacyjnego?
        [
            'coś lekkiego' => '9:4, 10:4, 11:4.5, 13:4, 33:4, 40:4.5, 44:4, 45:3, 51:4, 52:4, 56:4, 68:4, 70:2, 72:3, 73:3, 77:3',
            'coś pośrodku' => '1:4, 2:4, 3:4, 5:4, 6:4, 14:4, 15:4, 16:4, 19:4, 20:4, 21:4, 25:4, 27:4, 30:4, 32:4, 34:4, 39:2, 42:4, 44:3, 45:4, 47:4, 48:4, 49:4, 53, 55, 56:3, 57:4, 60:4, 61:4, 68:4, 69:4, 70:4, 71:4, 72:4, 73:4, 74:4, 76:4, 77:3, 999:2',
            'coś ciężkiego' => '3:4, 7:4, 8:4, 15:4, 22:4, 35:3, 36:4, 37:4, 32:2, 39:4, 42:4, 49:3, 50:4, 67:4, 998:3, 999:3',
        ],
        // Jak wysoką goryczkę preferujesz?
        [
            'prawie bez goryczki' => '9:2, 14:2, 15:2, 16:2, 19:2, 20:2, 25:2, 40:2, 44:2, 45:2, 50:2, 51:2, 53:2, 56:2, 73:2, 77:2, 998:2, 999:2',
            'z wyczuwalną goryczką' => '11:2, 21:2, 22:2, 34:2, 42:2, 45:2, 47:2, 48:2, 49:2, 50:2, 52:2, 55:2, 57:2, 60:2, 67:2, 68:2, 76:2, 72:2, 71:2',
            'wyraźnie gorzkiego' => '1:2, 2:2, 3:2, 5:2, 6:2, 7:2, 8:2, 10:2, 13:2, 21:2, 27:2, 30:2, 32:2, 33:2, 35:2, 36:2, 37:2, 38:2, 39:2, 55:2, 57:2, 60:2, 61:2, 70:2, 74:2, 69:2',
            'jestem hopheadem' => '1:3, 3:3, 5:3, 7:3, 8:3, 32:1.5',
        ],
        // Wolisz piwa jasne czy ciemne?
        [
            'jasne' => '1:2.5, 2:2.5, 5:2.5, 6:2.5, 7:2.5, 8:2.5, 9:2.5, 10:2.5, 11:2.5, 13:2.5, 14:2.5, 15:2.5, 16:2.5, 20:2.5, 22:2.5, 25:2.5, 27:2.5, 38:2.5, 39:2.5, 40:2.5, 42:2.5, 44:2.5, 45:2.5, 47:2.5, 49:2.5, 50:2.5, 51:2.5, 52:2.5, 53:2.5, 55:2.5, 56:2.5, 57:2.5, 60:2.5, 61:2.5, 67:2.5, 68:2.5, 69:2.5, 70:2.5, 72:2.5, 73:2.5, 76:2.5, 999:2.5',
            'ciemne' => '3:2.5, 19:2.5, 21:2.5, 30:2.5, 32:2.5, 33:2.5, 34:2.5, 35:2.5, 36:2.5, 37:2.5, 48:2.5, 71:2.5, 47:2.5, 74:2.5, 998:2.5',
        ],
        // Wolisz piwa słodsze czy wytrawniejsze?
        [
            'słodsze' => '1, 2, 5, 6, 7:1.5, 8, 14:1.5, 15:1.5, 16:1.5, 19, 20:1.5, 22:2, 25, 34:2.5, 36, 38, 39:1.5, 49:1.5, 50, 53, 56, 60:1.5, 67:2.5, 68, 69, 73:2.5, 76:1.5, 77:3, 998:3, 999:3',
            'wytrawniejsze' => '3:1.5, 5, 9, 10:1.5, 11, 13:1.5, 21, 32:1.5, 33:2, 35:2, 36, 37, 40, 45, 47, 48, 52:1.5, 55, 56, 57, 61, 70, 71, 72, 74',
        ],
        // Czy odpowiadałby Ci smak czekoladowy w piwie?
        [
            'tak' => '3:1.5, 21:1.5, 30:1.5, 32:1.5, 33:1.5, 34:1.5, 35:2, 36:2, 37:2, 48, 71:1.5, 74:2, 998:1.5',
            'nie' => '1, 2, 5, 6, 7, 8, 9, 10, 11, 13, 14, 15, 16, 19, 20, 22, 25, 27, 38, 39, 40, 42, 44, 45, 47, 49, 50, 51, 52, 53, 55, 56, 57, 60, 61, 67, 68, 69, 70, 72, 73, 76, 77, 999',
        ],
        // Czy odpowiadałby Ci smak kawowy w piwie?
        [
            'tak' => '3, 30, 32:1.5, 33:1.5, 34:1.5, 35:1.5, 36:1.5, 37:1.5, 71:1.5, 74:3, 998',
            'nie' => '1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 25, 27, 30, 38, 39, 40, 42, 44, 45, 47, 48, 49, 50, 51, 52, 53, 55, 56, 57, 60, 61, 67, 68, 69, 70, 72, 73, 76, 77, 999',
        ],
        // Czy odpowiadałoby Ci piwo nieco przyprawowe
        [
            'tak' => '2:1.5, 20:1.5, 25:1.5, 45:1.5, 47:1.5, 48, 49:2, 50:2, 53:2, 55, 67:1.5, 68',
            'nie' => null,
        ],
        // Czy chciałbyś piwo w klimatach owocowych?
        [
            'tak' => '1:2, 2:1.5, 5:1.5, 6:1.5, 7:2, 8:2, 25, 40:1.5, 42:1.5, 44, 45, 47:0.5, 49:1.5, 50, 51:1.25, 55, 56:1.5, 60:2, 61:2, 67, 69:2, 70:1.5, 72, 73:2.5, 76, 77:3, 999:3',
            'nie' => '3, 9, 10, 11, 13, 14, 15, 16, 19, 20, 21, 22, 27, 30, 32, 33, 34, 35, 36, 37, 38, 39, 47, 48, 52, 53, 57, 68, 71, 74, 998',
        ],
        // Co powiesz na piwo kwaśne?
        [
            'chętnie' => '40:2, 42:3, 44:3, 51:2, 56:3, 77:1.5, 999:1.5',
            'nie ma mowy' => '1, 2, 3, 5, 6, 7, 8, 9, 10, 11, 13, 14, 15, 16, 19, 20, 21, 22, 25, 27, 30, 32, 33, 34, 35, 36, 37, 38, 39, 40, 45, 47, 48, 49, 50, 52, 53, 55:0.5, 57, 60, 61, 67, 68, 69, 70, 71, 72, 73, 74, 76, 998',
        ],
        // Czy odpowiadałby Ci smak wędzony/dymny w piwie?
        [
            'tak' => '15:1.5, 16:1.5, 52:1.5, 57:1.5',
            'nie' => null,
        ],
        // BA?
        [
            'tak' => 'tak',
            'nie' => 'nie',
        ],
    ];

    public const POSSIBLE_SMOKED_DARK_BEERS = [ 36,  37,  ];

    public function fetchByQuestionNumber( int $questionNumber ): array
    {
        return self::SCORE[$questionNumber];
    }

}
