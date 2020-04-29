<?php
/** @noinspection PhpMethodNamingConventionInspection */
declare( strict_types=1 );

namespace Tests;

use App\Http\Objects\Answers;
use PHPUnit\Framework\TestCase;
use \App\Http\Utils\Filters;

final class FiltersTest extends TestCase
{
    public function testFiltersSmokedBeersByKeywordsProperly(): void
    {
        $filters = new Filters();
        $answers = new Answers();
        $answers->setSmoked( false );

        $beers = [
            [
                'title' => 'Some title',
                'keywords' => [
                    [
                        'keyword' => 'smoked',
                    ],
                    [
                        'keyword' => 'other keyword',
                    ],
                    [
                        'keyword' => 'something',
                    ],
                ],
            ],
        ];

        $filters->filter( $answers, $beers );

        self::assertEmpty( $beers );
    }

    /**
     * @dataProvider providerBarrelAgedKeywords
     *
     * @param string $ba
     */
    public function testFiltersBarrelAgedBeersByKeywordsProperly( string $ba ): void
    {
        $filters = new Filters();
        $answers = new Answers();
        $answers->setBarrelAged( false );

        $beers = [
            [
                'title' => 'Some title',
                'keywords' => [
                    [
                        'keyword' => $ba,
                    ],
                    [
                        'keyword' => 'other keyword',
                    ],
                    [
                        'keyword' => 'something',
                    ],
                ],
            ],
        ];

        $filters->filter( $answers, $beers );

        self::assertEmpty( $beers );
    }

    public function providerBarrelAgedKeywords(): array
    {
        return [
            'jack daniels' => ['jack daniels'],
            'barrel aged' => ['barrel aged'],
            'laphroaig' => ['laphroaig'],
            'bourbon ba' => ['bourbon ba'],
            'bourbon' => ['bourbon'],
            'ardbeg' => ['ardbeg'],
            'woodford reserve' => ['woodford reserve'],
            'whisky' => ['whisky'],
            'islay blend' => ['islay blend'],
            'bourbon barrel' => ['bourbon barrel'],
        ];
    }

    /**
     * @dataProvider providerBarrelAgedTitles
     *
     * @param string $title
     */
    public function testFiltersBarrelAgedBeersByTitleProperly( string $title ): void
    {
        $filters = new Filters();
        $answers = new Answers();
        $answers->setBarrelAged( false );

        $beers = [
            [
                'title' => $title,
                'keywords' => [
                    [
                        'keyword' => 'keyword',
                    ],
                    [
                        'keyword' => 'other keyword',
                    ],
                    [
                        'keyword' => 'something',
                    ],
                ],
            ],
            [
                'title' => 'Other Beer Title',
                'keywords' => [
                    [
                        'keyword' => 'pszenica',
                    ],
                    [
                        'keyword' => 'other keyword',
                    ],
                    [
                        'keyword' => 'something',
                    ],
                ],
            ],
        ];

        $filters->filter( $answers, $beers );

        self::assertCount( 1, $beers );
        self::assertEquals( 'Other Beer Title', $beers[0]['title'] );
    }

    public function providerBarrelAgedTitles(): array
    {
        return [
            'Królowa Lodu Jack Daniels BA' => ['Królowa Lodu Jack Daniels BA'],
            'Baltic Porter Laphroaig Barrel Aged' => ['Baltic Porter Laphroaig Barrel Aged'],
            'Old Ale Woodford Reserve' => ['Old Ale Woodford Reserve'],
            'Bock Rum BA' => ['Bock Rum BA'],
        ];
    }

    public function testFiltersSourBeersByKeywordsProperly(): void
    {
        $filters = new Filters();
        $answers = new Answers();
        $answers->setSour( false );

        $beers = [
            [
                'title' => 'Some title',
                'keywords' => [
                    [
                        'keyword' => 'kwaśne',
                    ],
                    [
                        'keyword' => 'other keyword',
                    ],
                    [
                        'keyword' => 'something',
                    ],
                ],
            ],
        ];

        $filters->filter( $answers, $beers );

        self::assertEmpty( $beers );
    }

    public function testFiltersChocolateBeersByKeywordsProperly(): void
    {
        $filters = new Filters();
        $answers = new Answers();
        $answers->setChocolate( false );

        $beers = [
            [
                'title' => 'Some title',
                'keywords' => [
                    [
                        'keyword' => 'słód czekoladowy ciemny',
                    ],
                    [
                        'keyword' => 'other keyword',
                    ],
                    [
                        'keyword' => 'something',
                    ],
                ],
            ],
        ];

        $filters->filter( $answers, $beers );

        self::assertEmpty( $beers );
    }

    public function testFiltersCoffeeBeersByKeywordsProperly(): void
    {
        $filters = new Filters();
        $answers = new Answers();
        $answers->setCoffee( false );

        $beers = [
            [
                'title' => 'Some title',
                'keywords' => [
                    [
                        'keyword' => 'kawowy',
                    ],
                    [
                        'keyword' => 'other keyword',
                    ],
                    [
                        'keyword' => 'something',
                    ],
                ],
            ],
        ];

        $filters->filter( $answers, $beers );

        self::assertEmpty( $beers );
    }
}
