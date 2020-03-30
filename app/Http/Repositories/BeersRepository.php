<?php
declare( strict_types=1 );

namespace App\Http\Repositories;

use App\Http\Objects\StyleInfo;
use App\Http\Objects\StyleInfoCollection;

final class BeersRepository implements BeersRepositoryInterface
{
    private const BEER_STYLE = [
        1 => [
            'name' => 'American IPA (India Pale Ale)',
            'otherName' => '',
            'polishName' => 'Amerykańskie IPA',
            'description' => '',
        ],
        2 => [
            'name' => 'Belgian IPA (India Pale Ale)',
            'otherName' => '',
            'polishName' => 'Belgijskie IPA',
            'description' => '',
        ],
        3 => [
            'name' => 'Black IPA (India Pale Ale)',
            'otherName' => 'Cascadian Dark Ale',
            'polishName' => 'Czarne IPA',
            'description' => '',
        ],
        5 => [
            'name' => 'Rye IPA (India Pale Ale)',
            'otherName' => '',
            'polishName' => 'Żytnie IPA',
            'description' => '',
        ],
        6 => [
            'name' => 'White IPA (India Pale Ale)',
            'otherName' => '',
            'polishName' => 'Białe IPA',
            'description' => '',
        ],
        7 => [
            'name' => 'Double IPA',
            'otherName' => 'Imperial IPA',
            'polishName' => 'Imperialne IPA',
            'description' => '',
        ],
        8 => [
            'name' => 'American Barleywine',
            'otherName' => '',
            'polishName' => 'Amerykańskie barleywine',
            'description' => '',
        ],
        9 => [
            'name' => 'Pale lager',
            'otherName' => '',
            'polishName' => 'Jasny lager',
            'description' => '',],
        10 => [
            'name' => 'Czech Pils',
            'otherName' => '',
            'polishName' => 'Czeski pils',
            'description' => '',

        ],
        11 => [
            'name' => 'Polotmave',
            'otherName' => '',
            'polishName' => 'Czeskie półciemne',
            'description' => '',

        ],
        12 => [
            'name' => 'Tmave',
            'otherName' => '',
            'polishName' => 'Czeski ciemny lager',
            'description' => '',

        ],
        13 => [
            'name' => 'German Pils',
            'otherName' => '',
            'polishName' => 'Niemiecki pils',
            'description' => '',

        ],
        14 => [
            'name' => 'Marzen',
            'otherName' => '',
            'polishName' => 'Marcowe',
            'description' => '',

        ],
        15 => [
            'name' => 'Rauchbock',
            'otherName' => '',
            'polishName' => 'Koźlak wędzony',
            'description' => '',

        ],
        16 => [
            'name' => 'Rauchmarzen',
            'otherName' => '',
            'polishName' => 'Marcowe wędzone',
            'description' => '',

        ],
        19 => [
            'name' => 'Dunkelweizen',
            'otherName' => '',
            'polishName' => 'Pszeniczne ciemne',
            'description' => '',

        ],
        20 => [
            'name' => 'Weizenbock',
            'otherName' => '',
            'polishName' => 'Koźlak pszeniczny',
            'description' => '',

        ],
        21 => [
            'name' => 'Dark lager',
            'otherName' => '',
            'polishName' => 'Ciemny lager',
            'description' => '',

        ],
        22 => [
            'name' => 'Doppelbock / Eisbock',
            'otherName' => '',
            'polishName' => 'Koźlak podwójny / lodowy',
            'description' => '',

        ],
        25 => [
            'name' => 'Weizen',
            'otherName' => 'Hefeweizen',
            'polishName' => 'Pszeniczne',
            'description' => '',

        ],
        27 => [
            'name' => 'Bitter',
            'otherName' => 'Extra Special Bitter',
            'polishName' => '',
            'description' => '',

        ],
        28 => [
            'name' => 'English IPA',
            'otherName' => '',
            'polishName' => 'Angielskie IPA',
            'description' => '',

        ],
        30 => [
            'name' => 'Brown Porter',
            'otherName' => '',
            'polishName' => '',
            'description' => '',

        ],
        32 => [
            'name' => 'Irish Red Ale',
            'otherName' => '',
            'polishName' => 'Irlandzkie czerwone ale',
            'description' => '',

        ],
        33 => [
            'name' => '(Dry) Stout',
            'otherName' => '',
            'polishName' => '',
            'description' => '',

        ],
        34 => [
            'name' => 'Milk Stout',
            'otherName' => '',
            'polishName' => 'Stout mleczny',
            'description' => '',

        ],
        35 => [
            'name' => 'Foreign Extra Stout',
            'otherName' => 'FES',
            'polishName' => '',
            'description' => '',

        ],
        36 => [
            'name' => 'Imperial Stout',
            'otherName' => 'RIS',
            'polishName' => 'Stout imperialny',
            'description' => '',

        ],
        37 => [
            'name' => '(Imperial) Baltic Porter',
            'otherName' => '',
            'polishName' => '(Imperialny) Porter Bałtycki',
            'description' => '',

        ],
        38 => [
            'name' => 'Old Ale',
            'otherName' => '',
            'polishName' => '',
            'description' => '',

        ],
        39 => [
            'name' => 'English Barleywine',
            'otherName' => '',
            'polishName' => 'Angielskie barleywine',
            'description' => '',

        ],
        40 => [
            'name' => 'Berliner Weisse',
            'otherName' => '',
            'polishName' => '',
            'description' => '',

        ],
        42 => [
            'name' => 'Flanders Red Ale / Oud Bruin',
            'otherName' => '',
            'polishName' => '',
            'description' => '',

        ],
        44 => [
            'name' => 'Lambic / Gueuze',
            'otherName' => '',
            'polishName' => '',
            'description' => '',

        ],
        45 => [
            'name' => 'Witbier',
            'otherName' => '',
            'polishName' => 'Białe pszeniczne',
            'description' => '',

        ],
        47 => [
            'name' => 'Saison',
            'otherName' => '',
            'polishName' => '',
            'description' => '',

        ],
        48 => [
            'name' => 'Dubbel',
            'otherName' => '',
            'polishName' => '',
            'description' => '',

        ],
        49 => [
            'name' => 'Tripel',
            'otherName' => '',
            'polishName' => '',
            'description' => '',

        ],
        50 => [
            'name' => 'Quadrupel',
            'otherName' => 'Belgian dark strong ale',
            'polishName' => '',
            'description' => '',

        ],
        51 => [
            'name' => 'Gose',
            'otherName' => '',
            'polishName' => '',
            'description' => '',

        ],
        52 => [
            'name' => 'Grodziskie',
            'otherName' => '',
            'polishName' => '',
            'description' => '',

        ],
        53 => [
            'name' => 'Roggenbier',
            'otherName' => '',
            'polishName' => 'Piwo żytnie',
            'description' => '',

        ],
        55 => [
            'name' => 'Wild / Farmhouse Ale',
            'otherName' => '',
            'polishName' => 'Dzikie ale',
            'description' => '',

        ],
        56 => [
            'name' => 'Sour Ale',
            'otherName' => '',
            'polishName' => 'Kwaśne ale',
            'description' => '',

        ],
        57 => [
            'name' => 'Smoked Ale',
            'otherName' => '',
            'polishName' => 'Wędzone ale',
            'description' => '',

        ],
        60 => [
            'name' => 'New England / Vermont IPA',
            'otherName' => '',
            'polishName' => '',
            'description' => '',

        ],
        61 => [
            'name' => 'American Pale Ale',
            'otherName' => 'APA',
            'polishName' => '',
            'description' => '',

        ],
        64 => [
            'name' => 'Brown Ale',
            'otherName' => '',
            'polishName' => '',
            'description' => '',

        ],
        67 => [
            'name' => 'Belgian Strong Ale',
            'otherName' => '',
            'polishName' => 'Belgijskie mocne ale',
            'description' => '',

        ],
        68 => [
            'name' => 'Blonde',
            'otherName' => '',
            'polishName' => 'Belgijskie jasne',
            'description' => '',

        ],
        69 => [
            'name' => 'American Wheat',
            'otherName' => '',
            'polishName' => 'Amerykańskie pszeniczne',
            'description' => '',

        ],
        70 => [
            'name' => 'American Lager',
            'otherName' => '',
            'polishName' => '',
            'description' => '',

        ],
        71 => [
            'name' => 'Oatmeal Stout',
            'otherName' => '',
            'polishName' => '',
            'description' => '',

        ],
        72 => [
            'name' => 'Pale Ale',
            'otherName' => '',
            'polishName' => 'Jasne ale',
            'description' => '',

        ],
        73 => [
            'name' => 'Milkshake IPA',
            'otherName' => '',
            'polishName' => '',
            'description' => '',

        ],
        74 => [
            'name' => 'Coffee Stout',
            'otherName' => '',
            'polishName' => 'Stout kawowy',
            'description' => '',

        ],
        76 => [
            'name' => 'Bock',
            'otherName' => '',
            'polishName' => 'Koźlak',
            'description' => '',

        ],
    ];

    public function fetchByIds( array $ids ): ?StyleInfoCollection
    {
        $styleInfoCollection = null;
        if ( !empty( $ids ) ) {
            $styleInfoCollection = new StyleInfoCollection();
            foreach ( $ids as $id ) {
                $styleInfoCollection->add( StyleInfo::fromArray( self::BEER_STYLE[$id], $id ) );
            }
        }

        return $styleInfoCollection;
    }
}
