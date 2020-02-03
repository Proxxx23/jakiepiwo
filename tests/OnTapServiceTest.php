<?php

namespace Tests;

use App\Http\Repositories\OnTapRepository;
use App\Http\Services\OnTapService;
use Prophecy\Prophet;

final class OnTapServiceTest extends FinalsBypassedTestCase
{
    public function testReturnsTapsByBeerNameProperly(): void
    {
        $prophet = new Prophet();
        $prophecy = $prophet->prophesize( OnTapRepository::class );
        $prophecy->fetchTapsByBeerName( 'mockBeerName' )->shouldBeCalledOnce();
        $prophecy->connected()->willReturn( true );
        $prophecy->placesFound()->willReturn( true );

        $service = new OnTapService( $prophecy->reveal() );

        self::assertNotNull( $service->getTapsByBeerName( 'mockBeerName' ) );
        $prophecy->checkProphecyMethodsPredictions();
    }

    public function testReturnsNullIfNotConnected(): void
    {
        $repository = $this->createMock( OnTapRepository::class );
        $repository->method( 'connected' )->willReturn( false );
        $repository->method( 'placesFound' )->willReturn( true );

        $service = new OnTapService( $repository );

        self::assertNull( $service->getTapsByBeerName( 'mockBeerName' ) );
    }

    public function testReturnsNullIfNoPlacesFound(): void
    {
        $repository = $this->createMock( OnTapRepository::class );
        $repository->method( 'connected' )->willReturn( true );
        $repository->method( 'placesFound' )->willReturn( false );

        $service = new OnTapService( $repository );

        self::assertNull( $service->getTapsByBeerName( 'mockBeerName' ) );
    }

    public function testReturnsNullIfNoPlacesFoundAndNotConnected(): void
    {
        $repository = $this->createMock( OnTapRepository::class );
        $repository->method( 'connected' )->willReturn( false );
        $repository->method( 'placesFound' )->willReturn( false );

        $service = new OnTapService( $repository );

        self::assertNull( $service->getTapsByBeerName( 'mockBeerName' ) );
    }
}
