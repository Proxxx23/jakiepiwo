<?php
declare( strict_types=1 );

namespace Tests;

use App\Http\Objects\BeerData;
use PHPUnit\Framework\TestCase;

final class BeerDataTest extends TestCase
{
    public function testCompletesHashKeysProperly(): void
    {
        $beerData = BeerData::fromArray(
            [
                'buyThis' => [
                    [
                        'cacheKey' => 'TEST_HASH',
                    ],
                    [
                        'cacheKey' => 'TEST_2_HASH',
                    ],
                    [
                        'cacheKey' => 'TEST_3_HASH',
                    ],
                ],
                'avoidThis' => null,
                'username' => null,
                'barrelAged' => true,
                'answers' => [],
            ]
        );

        self::assertSame( 'TEST_HASH', $beerData->getCacheKeys()[0] );
        self::assertSame( 'TEST_2_HASH', $beerData->getCacheKeys()[1] );
        self::assertSame( 'TEST_3_HASH', $beerData->getCacheKeys()[2] );
    }
}
