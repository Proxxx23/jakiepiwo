<?php
declare( strict_types=1 );

namespace App\Http\Utils;

final class Dictionary
{
    /**
     * @return array
     */
    public function get(): array
    {
        return [
            1 => 8, // American IPA
            2 => 63,// Belgian IPA
            3 => 15, // Black IPA
            5 => 41, // Rye IPA
            6 => 47, // White IPA
            7 => 38, // Double IPA
            8 => 52, // ABW
            9 => 14, // Pale Lager
            10 => 53, // Czech Pils -> Bohemian Pils
            //11 => '', // Polotmave
            12 => 11, // Tmave -> Schwarzbier
            13 => 50, // German Pils -> Pils
            14 => 151, // Marzen -> Rauch Weizen Bock
            15 => 67, // Rauchbock
            16 => 135, // Rauchmarzen -> Rauchbier
            19 => 69, // Dunkelweizen
            20 => 48,// Weizenbock
            21 => 11, // Dark Lager
            22 => 110, // Doppelbock / Eisbock
            24 => 58, // Baltic Porter
            25 => 75, // Weizen
            27 => 99, // Bitter/ESB
            28 => 1, // English IPA
            30 => 59, // Brown Porter
            32 => 9, // IRA
            33 => 44, // Dry Stout
            34 => 83, // Milk Stout
            35 => 51, // FES
            36 => 32, // RIS
            37 => 58, // Imperial Porter
            38 => 100, // Old Ale
            39 => 52, // English Barleywine
            40 => 56, // Berliner Weisse
            42 => 72, // Flanders / Oud Bruin
            44 => 16, // Lambic / Gueuze
            45 => 17,// Witbier
            47 => 18, // Saison
            48 => 71,// Dubbel
            49 => 92,// Tripel
            50 => 90, // Quadrupel
            51 => 39, // Gose
            52 => 4, // Grodziskie
            53 => 7, // Roggenbier
            // 55 => '', // Wild Ale
            56 => 57, // Sour Ale
            57 => 135, // Smoked Ale
            60 => 136, // NE IPA
            61 => 3, // APA
            64 => 66, // Brown Ale
            67 => 155, // Belgian Strong Ale -> Belgian Ale
            68 => 34, // Blonde
            69 => 28, // American WHeat
            70 => 26, // American Lager
            71 => 12, // Oatmeal Stout
            72 => 49, // Pale ALe
            //73 => '', // Milkshake IPA
            //74 => '', // Coffee Stout
            76 => 110, // Bock
        ];
    }

    public function getById( int $id ): ?int
    {
        return $this->get()[$id] ?? null;
    }
}
