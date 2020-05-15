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
                'tooltip' => 'Chodzi o ogólnodostępne piwa, takie jak Lech, Kasztelan, Tyskie czy Żywiec.',
            ],
            [
                'question' => 'Czy chcesz poznać nowe piwne smaki?',
                'tooltip' => '',
            ],
            [
                'question' => 'Czy wolałbyś spróbować wyłącznie piw, które potrafią zszokować?',
                'tooltip' => '',
            ],

            [
                'question' => 'Wolisz lekkie piwo do ugaszenia pragnienia, czy piwo cięższe i bardziej degustacyjne?',
                'answers' => [ 'coś lekkiego', 'coś pośrodku', 'coś ciężkiego' ],
                'tooltip' => 'Piwa lekkie oferują zazwyczaj mniejszą głębię. Piwa ciężkie to większe nagromadzenie różnych aromatów i smaków.',
            ],
            [
                'question' => 'Jak gorzkiego piwa chciałbyś się napić?',
                'answers' => [
                    'prawie bez goryczki',
                    'z wyczuwalną goryczką',
                    'wyraźnie gorzkiego',
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
                'question' => 'Czy odpowiadałby Ci smak czekoladowy w piwie?',
                'tooltip' => 'Taki efekt uzyskuje się przeważnie dzięki zastosowaniu odpowiednich słodów czy łuski kakaowej, o wiele rzadziej aromatów.',
            ],
            [
                'question' => 'Czy odpowiadałby Ci smak kawowy w piwie?',
                'tooltip' => 'Taki efekt uzyskuje się przeważnie dzięki zastosowaniu odpowiednich słodów czy kawy, o wiele rzadziej aromatów.',
            ],
            [
                'question' => 'Czy odpowiadałoby Ci piwo nieco przyprawowe?',
                'tooltip' => 'W niektórych piwach da się wyczuć nuty przyprawowe. Przeważnie są to akcenty goździków, białego pieprzu, gałki muszkatołowej czy ziela angielskiego. Często będzie to mieszanka wymienionych.',
            ],
            [
                'question' => 'Czy chciałbyś piwo w klimatach owocowych?',
                'tooltip' => 'Chodzi o piwa z dodatkiem owoców lub pulp owocowych, a także te, w których nuty owocowe otrzymano dzięki użyciu odpowiednich odmian chmielu lub dzięki pracy drożdży. Piw z sokiem nie będę Ci proponował.',
            ],
            [
                'question' => 'Co powiesz na piwo kwaśne?',
                'answers' => [ 'chętnie', 'nie ma mowy' ],
                'tooltip' => 'Kwaśne nie oznacza, że piwo jest zepsute czy stare. Kwaśność wprowadzana jest świadomie, przez dodatek bakterii kwasu mlekowego, kwaśnych owoców czy odpowiednich szczepów drożdży.',
            ],
            [
                'question' => 'Czy odpowiadałby Ci smak wędzony/dymny w piwie?',
                'tooltip' => 'Użycie specjalnych odmian słodu wprowadza do gotowego piwa aromaty kojarzące się z dymem z ogniska, okopconą szynką, wędzonką czy oscypkiem.',
            ],
            [
                'question' => 'Czy lubisz bourbon, whisky lub inne alkohole szlachetne?',
                'tooltip' => 'Domeną piw mocnych jest fakt, że doskonale nadają się do leżakowania w beczkach po alkoholach szlachetnych.',
            ],
        ];
    }
}
