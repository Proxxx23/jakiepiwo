<?php
declare( strict_types=1 );

namespace App\Http\Controllers\PolskiKraft;

class PolskiKraftService
{
    protected const DEFAULT_STYLE_URI = 'https://www.polskikraft.pl/openapi/style/%d/examples';

    protected const DEFAULT_LIST_URI = 'https://www.polskikraft.pl/openapi/style/list';

    /**
     * @param int $beerId
     *
     * @return array|null
     * @throws \Exception
     *
     * TODO: collection of objects only with needed fields
     */
    public static function fetchBeerInfo( int $beerId ): ?array
    {
        if ( !\array_key_exists( $beerId, Dictionaries::ID ) ) {
            return null;
        }

        $translatedBeerId = Dictionaries::ID[$beerId];

        $url = sprintf( self::DEFAULT_STYLE_URI, $translatedBeerId );

        $data = \json_decode( \file_get_contents( $url ) );

        if ( empty( $data ) || $data === null ) {
            return null;
        }

        while ( \count( $data ) > 3 ) {
            $randomIdToDelete = \random_int( 0, \count( $data ) - 1 );
            unset( $data[$randomIdToDelete] );
        }

        return \array_values( \array_filter( $data ) );
    }
}
