<?php
declare( strict_types=1 );

namespace App\Http\Utils;

class CommonUtils
{
    /**
     * @param array $array
     * @param $key
     * @param $value
     *
     * @return array
     */
    public static function arrayPushAssoc( array $array, $key, $value ): array
    {
        $array[$key] = $value;

        return $array;
    }
}
