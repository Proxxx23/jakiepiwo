<?php
declare(strict_types=1);
namespace App\Http\Controllers\PolskiKraft;

use Illuminate\Http\Request;
use App\Http\Controllers\PolskiKraft\Dictionaries as Dictionaries;

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

    public static function getBeerInfo(int $beer_id) {

        $ids_to_show = array();

        if (array_key_exists($beer_id, Dictionaries::$ID)) {
            $beer_id = Dictionaries::$ID[$beer_id];
        } else {
            return null;
        }

        $content = 'https://www.polskikraft.pl/openapi/style/'.$beer_id.'/examples';
        $request = json_decode(file_get_contents($content));

        $del1 = mt_rand(0, 4);
        unset($request[$del1]);
        $del2 = mt_rand(0, 3);
        unset($request[$del2]);
        
        if (!empty($request)) {
            return $request;
        } else {
            return null;
        }

    }


}
