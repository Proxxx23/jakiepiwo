<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

class QuestionsRepository implements QuestionsRepositoryInterface
{
    /**
     * @return array
     */
    public function fetchQuestions(): array
    {
        return [
            [
                'question' => 'Czy smakują Ci piwa koncernowe dostępne w sklepach?',
                'type' => 0,
                'tooltip' => 'Chodzi o jasne piwa koncernowe, takie jak Lech, Kasztelan, Tyskie czy Specjal.',
            ],
            [
                'question' => 'Czy chcesz poznać nowe piwne smaki?',
                'type' => 0,
            ],
            [
                'question' => 'Czy wolałbyś poznać wyłącznie style piwne, które potrafią zszokować?',
                'type' => 0,
            ],

            [
                'question' => 'Wolisz lekkie piwo do ugaszenia pragnienia, czy piwo bardziej złożone i degustacyjne?',
                'type' => 1,
                'answers' => [ 'coś lekkiego', 'coś pośrodku', 'coś złożonego' ],
                'tooltip' => 'Piwa lekkie oferują mniejszą głębię, zaś piwa złożone to większe nagromadzenie różnych aromatów i smaków.',
            ],
            [
                'question' => 'Jak wysoką goryczkę preferujesz?',
                'type' => 1,
                'answers' => [ 'ledwie wyczuwalną', 'lekką', 'zdecydowanie wyczuwalną', 'mocną', 'jestem hopheadem' ],
            ],
            [
                'question' => 'Smakują Ci bardziej piwa jasne, czy piwa ciemne?',
                'type' => 1,
                'answers' => [ 'jasne', 'bez znaczenia', 'ciemne' ],
            ],
            [
                'question' => 'Smakują Ci bardziej piwa słodsze, czy piwa wytrawniejsze?',
                'type' => 1,
                'answers' => [ 'słodsze', 'bez znaczenia', 'wytrawniejsze' ],
            ],

            [
                'question' => 'Jak mocne i gęste piwa preferujesz?',
                'type' => 1,
                'answers' => [ 'wodniste i lekkie', 'średnie', 'mocne i gęste' ],
            ],
            [
                'question' => 'Czy odpowiadałby Ci smak czekoladowy w piwie?',
                'type' => 0,
            ],
            [
                'question' => 'Czy odpowiadałby Ci smak kawowy w piwie?',
                'type' => 0,
            ],
            [
                'question' => 'Czy odpowiadałoby Ci piwo nieco przyprawowe?',
                'type' => 0,
                'tooltip' => 'W niektórych piwach da się wyczuć na przykład goździki czy nuty pieprzowe.',
            ],
            [
                'question' => 'Czy chciałbyś piwo w klimatach owocowych?',
                'type' => 0,
                'tooltip' => 'Chodzi o piwa bez dodatku soku, w których nuty owocowe otrzymano dzięki użyciu odpowiednich odmian chmielu lub dzięki pracy drożdży.',
            ],
            [
                'question' => 'Co powiesz na piwo kwaśne?',
                'type' => 1,
                'answers' => [ 'chętnie', 'nie ma mowy' ],
                'tooltip' => 'Kwaśne nie oznacza, że piwo jest zepsute czy stare.',
            ],
            [
                'question' => 'Czy odpowiadałby Ci smak wędzony/dymny w piwie?',
                'type' => 0,
            ],
            [
                'question' => 'Czy lubisz bourbon, whisky lub inne alkohole szlachetne?',
                'type' => 0,
            ],
        ];
    }

    /**
     * @return string
     */
    public function fetchJsonQuestions(): string
    {
        return \json_encode(
            [
                [ 'question' => 'Czy smakują Ci piwa koncernowe dostępne w sklepach?' ],
                [ 'question' => 'Czy chcesz poznać nowe piwne smaki?' ],
                [ 'question' => 'Czy wolałbyś poznać wyłącznie style piwne, które potrafią zszokować?' ],

                [
                    'question' => 'Wolisz lekkie piwo do ugaszenia pragnienia, czy piwo bardziej złożone i degustacyjne?',
                    'answer' => [ 'coś lekkiego', 'coś pośrodku', 'coś złożonego' ],
                ],
                [
                    'question' => 'Jak wysoką goryczkę preferujesz?',
                    'answer' => [
                        'ledwie wyczuwalną',
                        'lekką',
                        'zdecydowanie wyczuwalną',
                        'mocną',
                        'jestem hopheadem',
                    ],
                ],
                [
                    'question' => 'Smakują Ci bardziej piwa jasne, czy piwa ciemne?',
                    'answer' => [ 'jasne', 'bez znaczenia', 'ciemne' ],
                ],
                [
                    'question' => 'Smakują Ci bardziej piwa słodsze, czy piwa wytrawniejsze?',
                    'answer' => [ 'słodsze', 'bez znaczenia', 'wytrawniejsze' ],
                ],

                [
                    'question' => 'Jak mocne i gęste piwa preferujesz?',
                    'answer' => [ 'wodniste i lekkie', 'średnie', 'mocne i gęste' ],
                ],
                [ 'question' => 'Czy odpowiadałby Ci smak czekoladowy w piwie?' ],
                [ 'question' => 'Czy odpowiadałby Ci smak kawowy w piwie?' ],
                [ 'question' => 'Czy odpowiadałoby Ci piwo nieco przyprawowe?' ],
                [ 'question' => 'Czy chciałbyś piwo w klimatach owocowych?' ],
                [
                    'question' => 'Co powiesz na piwo kwaśne?',
                    'answer' => [ 'chętnie', 'nie ma mowy' ],
                ],
                [ 'question' => 'Czy odpowiadałby Ci smak wędzony/dymny w piwie?' ],
                [ 'question' => 'Czy lubisz bourbon, whisky lub inne alkohole szlachetne?' ],
            ]
        );
    }
}