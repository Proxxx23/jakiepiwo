<?php
declare( strict_types=1 );

namespace App\Http\Utils;

final class Dictionary
{
    public function get(): array
    {
        return [
            1 => [ 8, 20, 82, 87, ], // American IPA + WCIPA + Oatmeal IPA
            2 => [ 63, 73 ],// Belgian IPA
            3 => [ 15, 80, ], // Black IPA + CDA
            5 => [ 41, ], // Rye IPA
            6 => [ 47, 76, ], // White IPA + Wheat IPA
            7 => [ 33, 38 ], // Double IPA -> Double/Imperial
            8 => [ 52, ], // ABW
            9 => [ 14, 29, ], // Pale Lager + International Pale Lager
            10 => [ 53, ], // Czech Pils -> Bohemian Pils
            11 => [ 145, ], // Desitka
            13 => [ 50, ], // German Pils -> Pils (ogólnie)
            14 => [ 36, 13, ], // Marzen -> Marzen + Oktoberfest
            15 => [ 67, 151, ], // Rauchbock + Rauchweizenbock
            16 => [ 135, ], // Rauchmarzen -> Rauchbier
            19 => [ 69, 149 ], // Dunkelweizen
            20 => [ 48, ],// Weizenbock
            21 => [ 11, 31, ], // Dark Lager -> Schwarzbier + International Dark Lager
            22 => [ 110, 79, 88, ], // Doppelbock / Eisbock
            25 => [ 35, 75, 89, 105, ], // Weizen + Hefe + Weiss + Pszeniczne
            27 => [ 6, 99, ], // Bitter/ESB
            30 => [ 59, 78, ], // Brown Porter -> Porter + Robust
            33 => [ 44, 65, 22 ], // Dry Stout + Cream Stout
            34 => [ 46, 83, ], // Milk Stout
            35 => [ 51, 84, 60 ], // FES + Double Milk Stout
            36 => [ 32, 62 ], // RIS
            37 => [ 58, ], // (Imperial) Baltic Porter
            38 => [ 100, ], // Old Ale
            39 => [ 52, 142, ], // English Barleywine -> Barleywine + Wheat Wine
            40 => [ 56, ], // Berliner Weisse
            42 => [ 72, 158, ], // Flanders -> Flanders + Oud Bruin
            44 => [ 16, 164, ], // Lambic / Gueuze -> Lambic + Kriek
            45 => [ 17, ],// Witbier
            47 => [ 18, ], // Saison
            48 => [ 71, 96 ],// Dubbel
            49 => [ 92, ],// Tripel
            50 => [ 90, 125 ], // Quadrupel
            51 => [ 39, ], // Gose
            52 => [ 4, ], // Grodziskie
            53 => [ 7, ], // Roggenbier
            55 => [ 170, ], // Wild Ale -> Brett Ale
            56 => [ 57, ], // Sour Ale -> Sour
            57 => [ 12, 15, 52, 59, 44, 83, 51, 66, 100, 142, 135, 169, ], // Smoked Ales
            60 => [ 136, 154, ], // NE IPA -> NE IPA + Vermont IPA
            61 => [ 3, 54, 42, ], // APA -> APA + White APA + Summer Ale
            67 => [ 155, ], // Belgian Strong Ale -> Belgian Ale
            68 => [ 34, 119, ], // Blonde -> Blonde + Blond Ale
            69 => [ 28, ], // American Wheat
            70 => [ 26, ], // American Lager
            71 => [ 12, ], // Oatmeal Stout
            72 => [ 49, 109, 115, 122, 73, 86 ], // Pale Ale -> Pale Ale + Red Ale + Rye Pale Ale + Amber Ale + Belgian + American Rye
            73 => [ 8, 20, 41, 82, 87, 47, 38, 136, 154, 3, 54, 28, 49, 109, 115, 122, 42, 73, 134 ], // Milkshake -> wszystkie IPA/APA (see Filters)
            74 => [ 12, 44, 46, 51, 65, 83, 84, 60 ], // Coffee Stout -> stouty bez RISA (see Filters)
            76 => [ 79, 110, 107, 88, ], // Bock -> Bock + Koźlak + Lentebock + American Bock
            77 => [ 134, 161 ], // Fruit Beer + Radler
            998 => [ 12, 15, 80, 11, 31, 59, 78, 44, 65, 46, 83, 51, 84, 32, 58, 60, 22, 62 ], // Dark Pastry Beer
            999 => [ 8, 20, 82, 87, 41, 47, 76, 56, 17, 39, 170, 57, 3, 54, 42, 28, 26, 49, 109, 115, 122, ], // Pale Pastry Beer
        ];
    }

    public function getById( int $id ): ?array
    {
        return $this->get()[$id] ?? null;
    }
}
