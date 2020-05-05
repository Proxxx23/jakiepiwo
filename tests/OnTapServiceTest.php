<?php
declare( strict_types=1 );

namespace Tests;

use App\Http\Repositories\GeolocationRepository;
use App\Http\Repositories\OnTapRepository;
use App\Http\Services\OnTapService;
use Prophecy\Prophet;

final class OnTapServiceTest extends FinalsBypassedTestCase
{
    public function testReturnsTapsByBeerNameProperly(): void
    {
        $prophet = new Prophet();
        $prophecy = $prophet->prophesize( OnTapRepository::class );
        $prophecy->fetchTapsByBeerName( 'mockBeerName', 'mockBreweryName' )
            ->shouldBeCalledOnce();
        $prophecy->fetchAllCities()
            ->willReturn( [] );
        $prophecy->connectionRefused()
            ->willReturn( false );

        $geolocationRepository = $this->createMock( GeolocationRepository::class );
        $service = new OnTapService( $prophecy->reveal(), $geolocationRepository );

        self::assertNotNull( $service->getTapsByBeerName( 'mockBeerName', 'mockBreweryName' ) );
        $prophecy->checkProphecyMethodsPredictions();
    }

    public function testReturnsNullIfNotConnected(): void
    {
        $onTapRepository = $this->createMock( OnTapRepository::class );
        $onTapRepository->method( 'connectionRefused' )
            ->willReturn( true );
        $onTapRepository->method( 'fetchAllCities' )
            ->willReturn( [] );

        $geolocationRepository = $this->createMock( GeolocationRepository::class );

        $service = new OnTapService( $onTapRepository, $geolocationRepository );

        self::assertNull( $service->getTapsByBeerName( 'mockBeerName', 'mockBreweryName' ) );
    }
}
