<?php
declare( strict_types=1 );

namespace App\Helpers;

final class Helper
{
    public static function pregMatchMultiple( string $pattern, array $subjects ): bool
    {
        foreach ( $subjects as $subject ) {
            if ( \preg_match( $pattern, $subject ) ) {
                return true;
            }
        }

        return false;
    }
}
