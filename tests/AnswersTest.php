<?php
/** @noinspection PhpMethodNamingConventionInspection */
declare( strict_types=1 );

namespace Tests;

use App\Http\Objects\Answers;
use PHPUnit\Framework\TestCase;

class AnswersTest extends TestCase
{
    public function testAddsToIncludedProperly(): void
    {
        $answers = new Answers();
        $answers->addToIncluded( 1, 2 );

        self::assertSame( $answers->getIncludedIds()[1], 2.0 );
    }

    public function testAddsStrengthToIncludedProperly(): void
    {
        $answers = new Answers();
        $answers->addToIncluded( 1, 2 );
        $answers->addStrengthToIncluded( 1, 2 );

        self::assertSame( $answers->getIncludedIds()[1], 4.0 );
    }

    public function testAddsToExcludedProperly(): void
    {
        $answers = new Answers();
        $answers->addToExcluded( 1, 2 );

        self::assertSame( $answers->getExcludedIds()[1], 2.0 );
    }

    public function testAddsStrengthToExcludedProperly(): void
    {
        $answers = new Answers();
        $answers->addToExcluded( 1, 2 );
        $answers->addStrengthToExcluded( 1, 2 );

        self::assertSame( $answers->getExcludedIds()[1], 4.0 );
    }

    public function testBuildsPositiveSynergyProperlyForIdInIncludedAlready(): void
    {
        $answers = new Answers();
        $answers->addToIncluded( 1, 2 );
        $answers->applyPositiveSynergy( [ 1 ], 3.0 );

        self::assertSame( $answers->getIncludedIds()[1], 6.0 );
    }

    public function testBuildsPositiveSynergyProperlyForIdNotInIncluded(): void
    {
        $answers = new Answers();
        $answers->applyPositiveSynergy( [ 1 ], 3.0 );

        self::assertSame( $answers->getIncludedIds()[1], 3.0 );
    }

    public function testBuildsNegativeSynergyProperlyForIdInExcludedAlready(): void
    {
        $answers = new Answers();
        $answers->addToExcluded( 1, 6 );
        $answers->applyNegativeSynergy( [ 1 ], 2.0 );

        self::assertSame( $answers->getExcludedIds()[1], 3.0 );
    }

    public function testDoNothingForBuildingNegativeSynergyIfIdIsNotInExcluded(): void
    {
        $answers = new Answers();
        $answers->applyNegativeSynergy( [ 1 ], 2.0 );

        self::assertSame( [], $answers->getExcludedIds() );
    }

    public function testExcludesFromRecommendedProperlyIfNotPreviouslyInIncludes(): void
    {
        $answers = new Answers();
        $answers->excludeFromRecommended( [ 1, 2 ] );

        self::assertEmpty( $answers->getIncludedIds() );
    }

    public function testExcludesFromRecommendedProperlyIfPreviouslyInIncludes(): void
    {
        $answers = new Answers();
        $answers->addToIncluded(1, 1);
        $answers->addToIncluded(2, 2);
        $answers->excludeFromRecommended( [ 1, 2 ] );

        self::assertSame( 0, $answers->getIncludedIds()[1] );
        self::assertSame( 0, $answers->getIncludedIds()[2] );
    }

    public function testRemovesAssignedPointsProperlyIfNotShuffled(): void
    {
        $answers = new Answers();
        $answers->addToIncluded( 1, 6.0 );
        $answers->addToIncluded( 5, 6.0 );
        $answers->addToExcluded( 2, 6.0 );
        $answers->addToExcluded( 6, 6.0 );
        $answers->setShuffled( false );
        $answers->removeAssignedPoints();

        self::assertSame( 1, $answers->getIncludedIds()[0] );
        self::assertSame( 5, $answers->getIncludedIds()[1] );
        self::assertSame( 2, $answers->getExcludedIds()[0] );
        self::assertSame( 6, $answers->getExcludedIds()[1] );
    }

    public function testRemovesAssignedPointsProperlyIfShuffled(): void
    {
        $answers = new Answers();
        $answers->addToIncluded( 1, 6.0 );
        $answers->addToIncluded( 5, 6.0 );
        $answers->addToExcluded( 2, 6.0 );
        $answers->addToExcluded( 6, 6.0 );
        $answers->setShuffled( true );
        $answers->removeAssignedPoints();

        self::assertSame( 1, $answers->getIncludedIds()[0] );
        self::assertSame( 5, $answers->getIncludedIds()[1] );
        self::assertSame( 2, $answers->getExcludedIds()[0] );
        self::assertSame( 6, $answers->getExcludedIds()[1] );
    }
}
