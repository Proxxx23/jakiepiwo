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
            'moreUrl' => '',
        ],
        2 => [
            'name' => 'Belgian IPA (India Pale Ale)',
            'otherName' => '',
            'polishName' => 'Belgijskie IPA',
            'description' => '',
            'moreUrl' => '',
        ],
        3 => [
            'name' => 'Black IPA (India Pale Ale)',
            'otherName' => 'Cascadian Dark Ale',
            'polishName' => '',
            'description' => '',
            'moreUrl' => '',
        ],
        5 => [
            'name' => 'Rye IPA (India Pale Ale)',
            'otherName' => '',
            'polishName' => 'Żytnie IPA',
            'description' => '',
            'moreUrl' => '',
        ],
        6 => [
            'name' => 'White IPA (India Pale Ale)',
            'otherName' => '',
            'polishName' => 'Białe IPA',
            'description' => '',
            'moreUrl' => '',
        ],
        7 => [
            'name' => 'Double IPA',
            'otherName' => 'Imperial IPA',
            'polishName' => 'Imperialne IPA',
            'description' => '',
            'moreUrl' => '',
        ],
        8 => [
            'name' => 'American Barleywine',
            'otherName' => '',
            'polishName' => 'Amerykańskie barleywine',
            'description' => '',
            'moreUrl' => '',
        ],
        9 => [
            'name' => 'Pale lager',
            'otherName' => '',
            'polishName' => 'Jasny lager',
            'description' => '',
            'moreUrl' => '',
        ],
        10 => [
            'name' => 'Czech Pils',
            'otherName' => '',
            'polishName' => 'Czeski pils',
            'description' => '',
            'moreUrl' => '',

        ],
        11 => [
            'name' => 'Polotmave',
            'otherName' => '',
            'polishName' => 'Czeskie półciemne',
            'description' => '',
            'moreUrl' => '',

        ],
        12 => [
            'name' => 'Tmave',
            'otherName' => '',
            'polishName' => 'Czeski ciemny lager',
            'description' => '',
            'moreUrl' => '',

        ],
        13 => [
            'name' => 'German Pils',
            'otherName' => '',
            'polishName' => 'Niemiecki pils',
            'description' => '',
            'moreUrl' => '',

        ],
        14 => [
            'name' => 'Marzen',
            'otherName' => '',
            'polishName' => 'Marcowe',
            'description' => '',
            'moreUrl' => '',

        ],
        15 => [
            'name' => 'Rauchbock',
            'otherName' => '',
            'polishName' => 'Koźlak wędzony',
            'description' => '',
            'moreUrl' => '',

        ],
        16 => [
            'name' => 'Rauchmarzen',
            'otherName' => '',
            'polishName' => 'Marcowe wędzone',
            'description' => '',
            'moreUrl' => '',

        ],
        19 => [
            'name' => 'Dunkelweizen',
            'otherName' => '',
            'polishName' => 'Pszeniczne ciemne',
            'description' => '',
            'moreUrl' => '',

        ],
        20 => [
            'name' => 'Weizenbock',
            'otherName' => '',
            'polishName' => 'Koźlak pszeniczny',
            'description' => '',
            'moreUrl' => '',

        ],
        21 => [
            'name' => 'Dark lager',
            'otherName' => '',
            'polishName' => 'Ciemny lager',
            'description' => '',
            'moreUrl' => '',

        ],
        22 => [
            'name' => 'Doppelbock / Eisbock',
            'otherName' => '',
            'polishName' => 'Koźlak podwójny / lodowy',
            'description' => '',
            'moreUrl' => '',

        ],
        25 => [
            'name' => 'Weizen',
            'otherName' => 'Hefeweizen',
            'polishName' => 'Pszeniczne',
            'description' => '',
            'moreUrl' => '',

        ],
        27 => [
            'name' => 'Bitter',
            'otherName' => 'Extra Special Bitter',
            'polishName' => '',
            'description' => '',
            'moreUrl' => '',

        ],
        28 => [
            'name' => 'English IPA',
            'otherName' => '',
            'polishName' => 'Angielskie IPA',
            'description' => '',
            'moreUrl' => '',

        ],
        30 => [
            'name' => 'Brown Porter',
            'otherName' => '',
            'polishName' => '',
            'description' => '',
            'moreUrl' => '',

        ],
        32 => [
            'name' => 'Irish Red Ale',
            'otherName' => '',
            'polishName' => 'Irlandzkie czerwone ale',
            'description' => '',
            'moreUrl' => '',

        ],
        33 => [
            'name' => '(Dry) Stout',
            'otherName' => '',
            'polishName' => '',
            'description' => '',
            'moreUrl' => '',

        ],
        34 => [
            'name' => 'Milk Stout',
            'otherName' => '',
            'polishName' => 'Stout mleczny',
            'description' => '',
            'moreUrl' => '',

        ],
        35 => [
            'name' => 'Foreign Extra Stout',
            'otherName' => 'FES',
            'polishName' => '',
            'description' => '',
            'moreUrl' => '',

        ],
        36 => [
            'name' => 'Imperial Stout',
            'otherName' => 'RIS',
            'polishName' => 'Stout imperialny',
            'description' => '',
            'moreUrl' => '',

        ],
        37 => [
            'name' => '(Imperial) Baltic Porter',
            'otherName' => '',
            'polishName' => '(Imperialny) Porter Bałtycki',
            'description' => '',
            'moreUrl' => '',

        ],
        38 => [
            'name' => 'Old Ale',
            'otherName' => '',
            'polishName' => '',
            'description' => '',
            'moreUrl' => '',

        ],
        39 => [
            'name' => 'English Barleywine',
            'otherName' => '',
            'polishName' => 'Angielskie barleywine',
            'description' => '',
            'moreUrl' => '',

        ],
        40 => [
            'name' => 'Berliner Weisse',
            'otherName' => '',
            'polishName' => '',
            'description' => '',
            'moreUrl' => '',

        ],
        42 => [
            'name' => 'Flanders Red Ale / Oud Bruin',
            'otherName' => '',
            'polishName' => '',
            'description' => '',
            'moreUrl' => '',

        ],
        44 => [
            'name' => 'Lambic / Gueuze',
            'otherName' => '',
            'polishName' => '',
            'description' => '',
            'moreUrl' => '',

        ],
        45 => [
            'name' => 'Witbier',
            'otherName' => '',
            'polishName' => 'Białe pszeniczne',
            'description' => '',
            'moreUrl' => '',

        ],
        47 => [
            'name' => 'Saison',
            'otherName' => '',
            'polishName' => '',
            'description' => '',
            'moreUrl' => '',

        ],
        48 => [
            'name' => 'Dubbel',
            'otherName' => '',
            'polishName' => '',
            'description' => '',
            'moreUrl' => '',

        ],
        49 => [
            'name' => 'Tripel',
            'otherName' => '',
            'polishName' => '',
            'description' => '',
            'moreUrl' => '',

        ],
        50 => [
            'name' => 'Quadrupel',
            'otherName' => 'Belgian dark strong ale',
            'polishName' => '',
            'description' => '',
            'moreUrl' => '',

        ],
        51 => [
            'name' => 'Gose',
            'otherName' => '',
            'polishName' => '',
            'description' => '',
            'moreUrl' => '',

        ],
        52 => [
            'name' => 'Grodziskie',
            'otherName' => '',
            'polishName' => '',
            'description' => '',
            'moreUrl' => '',

        ],
        53 => [
            'name' => 'Roggenbier',
            'otherName' => '',
            'polishName' => 'Piwo żytnie',
            'description' => '',
            'moreUrl' => '',

        ],
        55 => [
            'name' => 'Wild / Farmhouse Ale',
            'otherName' => '',
            'polishName' => 'Dzikie ale',
            'description' => '',
            'moreUrl' => '',

        ],
        56 => [
            'name' => 'Sour Ale',
            'otherName' => '',
            'polishName' => 'Kwaśne ale',
            'description' => '',
            'moreUrl' => '',

        ],
        57 => [
            'name' => 'Smoked Ale',
            'otherName' => '',
            'polishName' => 'Wędzone ale',
            'description' => '',
            'moreUrl' => '',

        ],
        60 => [
            'name' => 'New England / Vermont IPA',
            'otherName' => '',
            'polishName' => '',
            'description' => '',
            'moreUrl' => '',

        ],
        61 => [
            'name' => 'American Pale Ale',
            'otherName' => 'APA',
            'polishName' => '',
            'description' => '',
            'moreUrl' => '',

        ],
        64 => [
            'name' => 'Brown Ale',
            'otherName' => '',
            'polishName' => '',
            'description' => '',
            'moreUrl' => '',

        ],
        67 => [
            'name' => 'Belgian Strong Ale',
            'otherName' => '',
            'polishName' => 'Belgijskie mocne ale',
            'description' => '',
            'moreUrl' => '',

        ],
        68 => [
            'name' => 'Blonde',
            'otherName' => '',
            'polishName' => 'Belgijskie jasne',
            'description' => '',
            'moreUrl' => '',

        ],
        69 => [
            'name' => 'American Wheat',
            'otherName' => '',
            'polishName' => 'Amerykańskie pszeniczne',
            'description' => '',
            'moreUrl' => '',

        ],
        70 => [
            'name' => 'American Lager',
            'otherName' => '',
            'polishName' => '',
            'description' => '',
            'moreUrl' => '',

        ],
        71 => [
            'name' => 'Oatmeal Stout',
            'otherName' => '',
            'polishName' => 'Stout owsiany',
            'description' => '',
            'moreUrl' => '',

        ],
        72 => [
            'name' => 'Pale Ale',
            'otherName' => '',
            'polishName' => 'Jasne ale',
            'description' => '',
            'moreUrl' => '',

        ],
        73 => [
            'name' => 'Milkshake IPA',
            'otherName' => '',
            'polishName' => '',
            'description' => '',
            'moreUrl' => '',

        ],
        74 => [
            'name' => 'Coffee Stout',
            'otherName' => '',
            'polishName' => 'Stout kawowy',
            'description' => '',
            'moreUrl' => '',

        ],
        76 => [
            'name' => 'Bock',
            'otherName' => '',
            'polishName' => 'Koźlak',
            'description' => '',
            'moreUrl' => '',

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
