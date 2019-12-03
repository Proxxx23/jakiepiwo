<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

use App\Http\Objects\PolskiKraftBeerData;
use App\Http\Objects\PolskiKraftBeerDataCollection;
use App\Http\Utils\Dictionary;

final class PolskiKraftRepository implements PolskiKraftRepositoryInterface
{
    protected const DEFAULT_LIST_URI = 'https://www.polskikraft.pl/openapi/style/list';

    /** @var Dictionary */
    private $dictionary;

    /**
     * @param Dictionary $dictionary
     */
    public function __construct( Dictionary $dictionary )
    {
        $this->dictionary = $dictionary;
    }

    /**
     * @param int $beerId
     *
     * @return PolskiKraftBeerDataCollection|null
     * @throws \Exception
     *
     * @todo: guzzle
     */
    public function fetchByBeerId( int $beerId ): ?PolskiKraftBeerDataCollection
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

        $beerDataCollection = new PolskiKraftBeerDataCollection();
        foreach ( $data as $item ) {
            $beerData = new PolskiKraftBeerData( $item );
            $beerDataCollection->add( $beerData->toArray() );
        }

        return $beerDataCollection;
    }
}
