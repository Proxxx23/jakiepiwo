<?php
/** @noinspection PhpMethodNamingConventionInspection */
declare( strict_types=1 );

namespace Tests;

use App\Http\Objects\Answers;
use PHPUnit\Framework\TestCase;
use \App\Http\Utils\Filters;

//todo: more tests - test every aspect
final class FiltersTest extends TestCase
{
    public function testFiltersSmokedBeersProperly(): void
    {
        $filters = new Filters();
        $answers = new Answers();
        $answers->setSmoked( true );

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
}
