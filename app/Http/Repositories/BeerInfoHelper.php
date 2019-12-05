<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

use App\Http\Objects\PolskiKraftData;
use App\Http\Objects\PolskiKraftDataCollection;
use App\Http\Utils\Dictionary;

final class BeerInfoHelper implements PolskiKraftRepositoryInterface
{
    private const DEFAULT_LIST_URI = 'https://www.polskikraft.pl/openapi/style/list';

    private Dictionary $dictionary;
    private OnTapRepositoryInterface $onTapRepository;

    public function __construct( Dictionary $dictionary, OnTapRepositoryInterface $onTapRepository )
    {
        $this->dictionary = $dictionary;
        $this->onTapRepository = $onTapRepository;
    }

    /**
     * @param int $beerId
     *
     * @return PolskiKraftDataCollection|null
     * @throws \Exception
     *
     * @todo: guzzle
     */
    public function fetchByBeerId( int $beerId ): ?PolskiKraftDataCollection
    {
        if ( !\array_key_exists( $beerId, $this->dictionary->get() ) ) {
            return null;
        }

        $translatedBeerId = $this->dictionary->getById( $beerId );

        $url = 'https://www.polskikraft.pl/openapi/style/' . $translatedBeerId . '/examples';
        $data = \json_decode(\file_get_contents($url), true, 512, JSON_THROW_ON_ERROR);

        if ( empty( $data ) ) {
            return null;
        }

        while ( \count( $data ) > 3 ) {
            $randomIdToDelete = \random_int( 0, \count( $data ) - 1 );
            unset( $data[$randomIdToDelete] );
        }

        $polskiKraftCollection = new PolskiKraftDataCollection();
        foreach ( $data as $item ) {
            $polskiKraft = new PolskiKraftData( $item );

            $onTap = $this->onTapRepository->fetchTapsByBeerData( $polskiKraft );
            $polskiKraft->setOnTap( $onTap );

            $polskiKraftCollection->add( $polskiKraft->toArray() );
        }

        return $polskiKraftCollection;
    }
}
