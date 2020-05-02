<?php
/** @noinspection PhpMethodNamingConventionInspection */
declare( strict_types=1 );

namespace Tests;

use App\Http\Objects\Answers;
use PHPUnit\Framework\TestCase;

final class AnswersTest extends TestCase
{
    public function testAddsToIncludedProperly(): void
    {
        $answers = new Answers();
        $answers->addToRecommended( 1, 2 );

        self::assertSame( $answers->getRecommendedIds()[1], 2.0 );
    }

    public function testAddsStrengthToIncludedProperly(): void
    {
        $answers = new Answers();
        $answers->addToRecommended( 1, 2 );
        $answers->addStrengthToRecommended( 1, 2 );

        self::assertSame( $answers->getRecommendedIds()[1], 4.0 );
    }

    public function testAddsToExcludedProperly(): void
    {
        $answers = new Answers();
        $answers->addToUnsuitable( 1, 2 );

        self::assertSame( $answers->getUnsuitableIds()[1], 2.0 );
    }

    public function testAddsStrengthToExcludedProperly(): void
    {
        $answers = new Answers();
        $answers->addToUnsuitable( 1, 2 );
        $answers->addStrengthToUnsuitable( 1, 2 );

        self::assertSame( $answers->getUnsuitableIds()[1], 4.0 );
    }

    public function testBuildsPositiveSynergyProperlyForIdInIncludedAlready(): void
    {
        $answers = new Answers();
        $answers->addToRecommended( 1, 2 );
        $answers->applyPositiveSynergy( [ 1 ], 3.0 );

        self::assertSame( $answers->getRecommendedIds()[1], 6.0 );
    }

    public function testBuildsPositiveSynergyProperlyForIdNotInIncluded(): void
    {
        $answers = new Answers();
        $answers->applyPositiveSynergy( [ 1 ], 3.0 );

        self::assertSame( $answers->getRecommendedIds()[1], 3.0 );
    }

    public function testBuildsNegativeSynergyProperlyForIdInExcludedAlready(): void
    {
        $answers = new Answers();
        $answers->addToUnsuitable( 1, 6 );
        $answers->applyNegativeSynergy( [ 1 ], 2.0 );

        self::assertSame( $answers->getUnsuitableIds()[1], 3.0 );
    }

    public function testDoNothingForBuildingNegativeSynergyIfIdIsNotInExcluded(): void
    {
        $answers = new Answers();
        $answers->applyNegativeSynergy( [ 1 ], 2.0 );

        self::assertSame( [], $answers->getUnsuitableIds() );
    }

    public function testExcludesFromRecommendedProperlyIfNotPreviouslyInIncludes(): void
    {
        $answers = new Answers();
        $answers->excludeFromRecommended( [ 1, 2 ] );

        self::assertEmpty( $answers->getRecommendedIds() );
    }

    public function testExcludesFromRecommendedProperlyIfPreviouslyInIncludes(): void
    {
        $answers = new Answers();
        $answers->addToRecommended( 1, 1 );
        $answers->addToRecommended( 2, 2 );
        $answers->excludeFromRecommended( [ 1, 2 ] );

        self::assertSame( 0, $answers->getRecommendedIds()[1] );
        self::assertSame( 0, $answers->getRecommendedIds()[2] );
    }

    public function testRemovesAssignedPointsProperlyIfNotShuffled(): void
    {
        $answers = new Answers();
        $answers->addToRecommended( 1, 6.0 );
        $answers->addToRecommended( 5, 6.0 );
        $answers->addToUnsuitable( 2, 6.0 );
        $answers->addToUnsuitable( 6, 6.0 );
        $answers->setShuffled( false );
        $answers->removeAssignedPoints();

        self::assertSame( 1, $answers->getRecommendedIds()[0] );
        self::assertSame( 5, $answers->getRecommendedIds()[1] );
        self::assertSame( 2, $answers->getUnsuitableIds()[0] );
        self::assertSame( 6, $answers->getUnsuitableIds()[1] );
    }

    // todo: on php 7.4 acts different than on 7.2 ;/
    public function testRemovesAssignedPointsProperlyIfShuffled(): void
    {
        $answers = new Answers();
        $answers->addToRecommended( 1, 6.0 );
        $answers->addToRecommended( 5, 6.0 );
        $answers->addToUnsuitable( 2, 6.0 );
        $answers->addToUnsuitable( 6, 6.0 );
        $answers->setShuffled( true );
        $answers->removeAssignedPoints();

        self::assertSame( 1, $answers->getRecommendedIds()[0] );
        self::assertSame( 5, $answers->getRecommendedIds()[1] );
        self::assertSame( 2, $answers->getUnsuitableIds()[0] );
        self::assertSame( 6, $answers->getUnsuitableIds()[1] );
    }
}
