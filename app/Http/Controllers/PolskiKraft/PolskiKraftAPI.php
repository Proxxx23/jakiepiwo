<?php
declare(strict_types=1);
namespace App\Http\Controllers\PolskiKraft;

use Illuminate\Http\Request;

class PolskiKraftAPI
{

    public static function getBeersList() {

        $request = json_decode(file_get_contents("https://www.polskikraft.pl/openapi/style/list"));

        if (!empty($request)) {
            return $request;
        } else {
            return false;
        }

    }

    // Slownik
    //

    public static function getBeerInfo($beer_id = 0) : ?array {

        $request = json_decode(file_get_contents("https://www.polskikraft.pl/openapi/style/{$beer_id}/examples"));

        if (!empty($request)) {
            return $request;
        } else {
            return null;
        }

    }


}
