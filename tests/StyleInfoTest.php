<?php
declare( strict_types=1 );

namespace Tests;

use App\Http\Objects\StyleInfo;

final class StyleInfoTest extends FinalsBypassedTestCase
{
    public function testSetsSmokedNamesProperly(): void
    {
        $styleInfo = StyleInfo::fromArray(
            [
                'name' => 'Mock',
                'otherName' => '',
                'polishName' => 'Mock',
                'description' => '',
                'moreLink' => '',
            ], 5
        );
        $styleInfo->setSmokedNames();

        self::assertSame( '(Smoked) Mock', $styleInfo->getName() );
        self::assertSame( '(Wędzone) Mock', $styleInfo->getPolishName() );
    }
}
