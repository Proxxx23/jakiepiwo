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
        $answers = new Answers();
        $answers->setSmoked( false );

        $beers = [
            17 => [
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
            ],
        ];

        Filters::filter( $answers, $beers, 'mocne i gęste' );

        self::assertEmpty( $beers );
    }

    /**
     * @dataProvider providerBarrelAgedKeywords
     *
     * @param string $ba
     */
    public function testFiltersBarrelAgedBeersByKeywordsProperly( string $ba ): void
    {
        $answers = new Answers();
        $answers->setBarrelAged( false );

        $beers = [
            17 => [
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
            ],
        ];

        Filters::filter( $answers, $beers, 'mocne i gęste' );

        self::assertEmpty( $beers );
    }

    public function providerBarrelAgedKeywords(): array
    {
        return [
            'jack daniels' => [ 'jack daniels' ],
            'barrel aged' => [ 'barrel aged' ],
            'laphroaig' => [ 'laphroaig' ],
            'bourbon ba' => [ 'bourbon ba' ],
            'bourbon' => [ 'bourbon' ],
            'ardbeg' => [ 'ardbeg' ],
            'woodford reserve' => [ 'woodford reserve' ],
            'whisky' => [ 'whisky' ],
            'islay blend' => [ 'islay blend' ],
            'bourbon barrel' => [ 'bourbon barrel' ],
        ];
    }

    /**
     * @dataProvider providerBarrelAgedTitles
     *
     * @param string $title
     */
    public function testFiltersBarrelAgedBeersByTitleProperly( string $title ): void
    {
        $answers = new Answers();
        $answers->setBarrelAged( false );

        $beers = [
            17 => [
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
            ],
        ];

        Filters::filter( $answers, $beers, 'mocne i gęste' );

        self::assertCount( 1, $beers );
        self::assertEquals( 'Other Beer Title', $beers[0]['title'] );
    }

    public function providerBarrelAgedTitles(): array
    {
        return [
            'Królowa Lodu Jack Daniels BA' => [ 'Królowa Lodu Jack Daniels BA' ],
            'Baltic Porter Laphroaig Barrel Aged' => [ 'Baltic Porter Laphroaig Barrel Aged' ],
            'Old Ale Woodford Reserve' => [ 'Old Ale Woodford Reserve' ],
            'Bock Rum BA' => [ 'Bock Rum BA' ],
        ];
    }

    public function testFiltersSourBeersByKeywordsProperly(): void
    {
        $answers = new Answers();
        $answers->setSour( false );

        $beers = [
            17 => [
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
            ],
        ];

        Filters::filter( $answers, $beers, 'mocne i gęste' );

        self::assertEmpty( $beers );
    }

    public function testFiltersChocolateBeersByKeywordsProperly(): void
    {
        $answers = new Answers();
        $answers->setChocolate( false );

        $beers = [
            17 => [
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
            ],
        ];

        Filters::filter( $answers, $beers, 'mocne i gęste' );

        self::assertEmpty( $beers );
    }

    public function testFiltersCoffeeBeersByKeywordsProperly(): void
    {
        $answers = new Answers();
        $answers->setCoffee( false );

        $beers = [
            17 => [
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
            ],
        ];

        Filters::filter( $answers, $beers, 'mocne i gęste' );

        self::assertEmpty( $beers );
    }

    public function testFiltersCoffeeStoutsByKeywordsProperly(): void
    {
        $answers = new Answers();
        $answers->setRecommendedIds( 74 );
        $answers->setCoffee( true );

        $beers = [
            [
                'title' => 'Should stay title',
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
            [
                'title' => 'Should be removed title',
                'keywords' => [
                    [
                        'keyword' => 'catharina',
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

        Filters::filter( $answers, $beers, 'mocne i gęste' );

        self::assertNotEmpty( $beers );
        self::assertCount( 1, $beers );
        self::assertSame( 'Should stay title', $beers[0]['title'] );
    }

    public function testFiltersCoffeeStoutsByTitleProperly(): void
    {
        $answers = new Answers();
        $answers->setRecommendedIds( 74 );
        $answers->setCoffee( true );

        $beers = [
            [
                'title' => 'Coffee vanilla stout',
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
                'title' => 'Should be removed title',
                'keywords' => [
                    [
                        'keyword' => 'catharina',
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

        Filters::filter( $answers, $beers, 'mocne i gęste' );

        self::assertNotEmpty( $beers );
        self::assertCount( 1, $beers );
        self::assertSame( 'Coffee vanilla stout', $beers[0]['title'] );
    }

    public function testFiltersMilkshakesByKeywordsProperly(): void
    {
        $answers = new Answers();
        $answers->setRecommendedIds( 73 );
        $answers->setCoffee( true );

        $beers = [
            [
                'title' => 'Should stay title',
                'keywords' => [
                    [
                        'keyword' => 'milkshake',
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
                'title' => 'Should be removed title',
                'keywords' => [
                    [
                        'keyword' => 'catharina',
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

        Filters::filter( $answers, $beers, 'mocne i gęste' );

        self::assertNotEmpty( $beers );
        self::assertCount( 1, $beers );
        self::assertSame( 'Should stay title', $beers[0]['title'] );
    }

    public function testFiltersMilkshakesByTitleProperly(): void
    {
        $answers = new Answers();
        $answers->setRecommendedIds( 73 );
        $answers->setCoffee( true );

        $beers = [
            [
                'title' => 'Milkshake IPA',
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
                'title' => 'Should be removed title',
                'keywords' => [
                    [
                        'keyword' => 'catharina',
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

        Filters::filter( $answers, $beers, 'mocne i gęste' );

        self::assertNotEmpty( $beers );
        self::assertCount( 1, $beers );
        self::assertSame( 'Milkshake IPA', $beers[0]['title'] );
    }

    /**
     * Shoudl remove both
     */
    public function testNoCoffeeButCoffeeStoutIncluded(): void
    {
        $answers = new Answers();
        $answers->setRecommendedIds( 74 );
        $answers->setCoffee( false );

        $beers = [
            [
                'title' => 'Coffee Vanilla Stout',
                'keywords' => [
                    [
                        'keyword' => 'kawa',
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
                'title' => 'Should not be removed title',
                'keywords' => [
                    [
                        'keyword' => 'catharina',
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

        Filters::filter( $answers, $beers, 'mocne i gęste' );

        self::assertEmpty( $beers );
    }
}
