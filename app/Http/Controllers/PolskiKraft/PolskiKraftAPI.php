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

    public static function getBeersInfo(array $beer_ids) : ?array {

        $ids_to_show = array();

        foreach ($beer_ids AS $id) {
            if (array_key_exists($id, Dictionaries::$ID)) {
                $ids_to_show[] = Dictionaries::$ID[$id];
            }
        }

        for ($i = 0; $i < count($ids_to_show); $i++) {
            $content = 'https://www.polskikraft.pl/openapi/style/'.$ids_to_show[$i].'/examples';
            $request[$i] = json_decode(file_get_contents($content));
        }

        if (!empty($request)) {
            return $request;
        } else {
            return null;
        }

    }


}
