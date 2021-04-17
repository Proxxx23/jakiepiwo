<?php
declare( strict_types=1 );

namespace Tests;

use App\Http\Repositories\GeolocationRepositoryInterface;
use App\Http\Repositories\OnTapRepositoryInterface;
use App\Services\OnTapService;
use Prophecy\Prophet;
use PHPUnit\Framework\TestCase;

final class OnTapServiceTest extends TestCase
{
    public function testReturnsTapsByBeerNameProperly(): void
    {
        $prophet = new Prophet();
        $prophecy = $prophet->prophesize( OnTapRepositoryInterface::class );
        $prophecy->fetchTapsByBeerName( [ 'title' => 'mockBeerName', 'subtitle' => 'mockBreweryName', 'subtitleAlt' => '' ] )
            ->shouldBeCalledOnce();
        $prophecy->fetchAllCities()
            ->willReturn( [] );
        $prophecy->connectionRefused()
            ->willReturn( false );

        $geolocationRepository = $this->createMock( GeolocationRepositoryInterface::class );
        $service = new OnTapService( $prophecy->reveal(), $geolocationRepository );

        self::assertNotNull(
            $service->getTapsByBeerName(
                [ 'title' => 'mockBeerName', 'susbtitle' => 'mockBreweryName', 'subtitleAlt' => '' ]
            )
        );
        $prophecy->checkProphecyMethodsPredictions();
    }

    public function testReturnsNullIfNotConnected(): void
    {
        $onTapRepository = $this->createMock( OnTapRepositoryInterface::class );
        $onTapRepository->method( 'connectionRefused' )
            ->willReturn( true );
        $onTapRepository->method( 'fetchAllCities' )
            ->willReturn( [] );

        $geolocationRepository = $this->createMock( GeolocationRepositoryInterface::class );

        $service = new OnTapService( $onTapRepository, $geolocationRepository );

        self::assertNull( $service->getTapsByBeerName( [ 'title' => 'mockBeerName', 'subtitle' => 'mockBreweryName', 'subtitleAlt' => '' ] ) );
    }
}
