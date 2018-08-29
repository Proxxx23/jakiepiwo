<?php
declare(strict_types=1);
namespace App\Http\Controllers\PolskiKraft;

use Illuminate\Http\Request;
use App\Http\Controllers\PolskiKraft\Dictionaries as Dictionaries;

class PolskiKraftAPI
{

    public static function getBeersList(): ?array 
    {

        $request = json_decode(file_get_contents("https://www.polskikraft.pl/openapi/style/list"));
        if (!empty($request)) {
            return $request;
        } 

        return null;

    }

    public static function getBeerInfo(int $beer_id): ?array 
    {

        if (!array_key_exists($beer_id, Dictionaries::$ID)) {
            return null;
        }

        $beerId = Dictionaries::$ID[$beer_id];
        $content = 'https://www.polskikraft.pl/openapi/style/'.$beerId.'/examples';
        $request = json_decode(file_get_contents($content));

        if (empty($request) || null === $request) {
            return null;
        }

        $del1 = mt_rand(0, count($request)-1);
        unset($request[$del1]);
        $del2 = mt_rand(0, count($request)-1);
        unset($request[$del2]);
        
        return $request;
        
    }


}
