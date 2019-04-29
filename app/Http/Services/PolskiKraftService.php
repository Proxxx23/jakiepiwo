<?php
declare( strict_types=1 );

namespace App\Http\Controllers\PolskiKraft;

class PolskiKraftService
{
    protected const DEFAULT_STYLE_URI = 'https://www.polskikraft.pl/openapi/style/%d/examples';

    protected const DEFAULT_LIST_URI = 'https://www.polskikraft.pl/openapi/style/list';

    /**
     * @return array
     */
    public static function getBeersList(): array
    {
        $request = \json_decode( \file_get_contents( self::DEFAULT_LIST_URI ) );
        if ( !empty( $request ) ) {
            return $request;
        }

        return null;
    }

    /**
     * @param int $beerId
     *
     * @return array|null
     * @throws \Exception
     */
    public static function getBeerInfo( int $beerId ): ?array
    {
        if ( !\array_key_exists( $beerId, Dictionaries::ID ) ) {
            return null;
        }

        $translatedBeerId = Dictionaries::ID[$beerId];

        $content = sprintf( self::DEFAULT_STYLE_URI, $translatedBeerId );

        $request = \json_decode( \file_get_contents( $content ) );

        if ( empty( $request ) || $request === null ) {
            return null;
        }

        while ( \count( $request ) > 3 ) {
            $deletedRandomId = \random_int( 0, \count( $request ) - 1 );
            unset( $request[$deletedRandomId] );
        }

        return \array_values( \array_filter( $request ) );
    }
}
