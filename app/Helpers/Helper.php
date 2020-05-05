<?php
declare(strict_types=1);

namespace App\Helpers;

class Helper
{
    public static function PregMatchMultiple( string $pattern, array $subjects ): bool
    {
        foreach ( $subjects as $subject ) {
            if ( \preg_match( $pattern, $subject, $matches ) ) {
                return true;
            }
        }

        return false;
    }
}
