<?php
declare( strict_types=1 );

namespace Tests;

use App\Http\Objects\StyleInfo;

final class StyleInfoTest extends FinalsBypassedTestCase
{
    public function testSetsSmokedPorterProperly(): void
    {
        $styleInfo = StyleInfo::fromArray(
            [
                'name' => 'Porter',
                'otherName' => '',
                'polishName' => 'Porter',
                'description' => '',
                'moreUrlQuery' => '',
            ], 5
        );
        $styleInfo->setSmokedNames();

        self::assertSame( '(Smoked) Porter', $styleInfo->getName() );
        self::assertSame( '(Wędzony) Porter', $styleInfo->getPolishName() );
    }

    public function testSetsSmokedStoutProperly(): void
    {
        $styleInfo = StyleInfo::fromArray(
            [
                'name' => 'Stout',
                'otherName' => '',
                'polishName' => 'Stout',
                'description' => '',
                'moreUrlQuery' => '',
            ], 5
        );
        $styleInfo->setSmokedNames();

        self::assertSame( '(Smoked) Stout', $styleInfo->getName() );
        self::assertSame( '(Wędzony) Stout', $styleInfo->getPolishName() );
    }

    public function testSetsSmokedBockProperly(): void
    {
        $styleInfo = StyleInfo::fromArray(
            [
                'name' => 'Bock',
                'otherName' => '',
                'polishName' => 'Koźlak',
                'description' => '',
                'moreUrlQuery' => '',
            ], 5
        );
        $styleInfo->setSmokedNames();

        self::assertSame( '(Smoked) Bock', $styleInfo->getName() );
        self::assertSame( '(Wędzony) Koźlak', $styleInfo->getPolishName() );
    }

    public function providerBeerStyles(): array
    {
        return [
            ['Stout'],
            ['Porter'],
            ['Koźlak'],
        ];
    }

    public function testSetsSmokedOthersProperly(): void
    {
        $styleInfo = StyleInfo::fromArray(
            [
                'name' => 'Barleywine',
                'otherName' => '',
                'polishName' => 'Barleywine',
                'description' => '',
                'moreUrlQuery' => '',
            ], 5
        );
        $styleInfo->setSmokedNames();

        self::assertSame( '(Smoked) Barleywine', $styleInfo->getName() );
        self::assertSame( '(Wędzone) Barleywine', $styleInfo->getPolishName() );
    }
}
