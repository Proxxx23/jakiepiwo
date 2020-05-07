<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

final class QuestionsRepository implements QuestionsRepositoryInterface
{
    public function fetchAllQuestions(): array
    {
        return [
            [
                'question' => 'Czy smakują Ci piwa koncernowe dostępne w sklepach?',
                'tooltip' => 'Chodzi o jasne piwa koncernowe, takie jak Lech, Kasztelan, Tyskie czy Specjal.',
            ],
            [
                'question' => 'Czy chcesz poznać nowe piwne smaki?',
            ],
            [
                'question' => 'Czy wolałbyś poznać wyłącznie piwa, które potrafią zszokować?',
            ],

            [
                'question' => 'Wolisz lekkie piwo do ugaszenia pragnienia, czy piwo cięższe i bardziej degustacyjne?',
                'answers' => [ 'coś lekkiego', 'coś pośrodku', 'coś ciężkiego' ],
                'tooltip' => 'Piwa lekkie oferują mniejszą głębię. Piwa ciężkie to większe nagromadzenie różnych aromatów i smaków.',
            ],
            [
                'question' => 'Jak wysoką goryczkę preferujesz?',
                'answers' => [
                    'ledwie wyczuwalną',
                    'lekką',
                    'mocną',
                    'jestem hopheadem',
                ],
            ],
            [
                'question' => 'Smakują Ci bardziej piwa jasne, czy piwa ciemne?',
                'answers' => [ 'jasne', 'bez znaczenia', 'ciemne' ],
            ],
            [
                'question' => 'Smakują Ci bardziej piwa słodsze, czy piwa wytrawniejsze?',
                'answers' => [ 'słodsze', 'bez znaczenia', 'wytrawniejsze' ],
            ],

            [
                'question' => 'Jak mocne i gęste piwa preferujesz?',
                'answers' => [ 'wodniste i lekkie', 'średnie', 'mocne i gęste' ],
            ],
            [
                'question' => 'Czy odpowiadałby Ci smak czekoladowy w piwie?',
            ],
            [
                'question' => 'Czy odpowiadałby Ci smak kawowy w piwie?',
            ],
            [
                'question' => 'Czy odpowiadałoby Ci piwo nieco przyprawowe?',
                'tooltip' => 'W niektórych piwach da się wyczuć na przykład goździki czy nuty pieprzowe.',
            ],
            [
                'question' => 'Czy chciałbyś piwo w klimatach owocowych?',
                'tooltip' => 'Chodzi o piwa z dodatkiem owoców lub pulp owocowych, a także te, w których nuty owocowe otrzymano dzięki użyciu odpowiednich odmian chmielu lub dzięki pracy drożdży.',
            ],
            [
                'question' => 'Co powiesz na piwo kwaśne?',
                'answers' => [ 'chętnie', 'nie ma mowy' ],
                'tooltip' => 'Kwaśne nie oznacza, że piwo jest zepsute czy stare.',
            ],
            [
                'question' => 'Czy odpowiadałby Ci smak wędzony/dymny w piwie?',
            ],
            [
                'question' => 'Czy lubisz bourbon, whisky lub inne alkohole szlachetne?',
            ],
        ];
    }
}
