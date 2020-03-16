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
            'moreLink' => '',
        ],
        2 => [
            'name' => 'Belgian IPA (India Pale Ale)',
            'otherName' => '',
            'polishName' => 'Belgijskie IPA',
            'description' => '',
            'moreLink' => '',
        ],
        3 => [
            'name' => 'Black IPA (India Pale Ale)',
            'otherName' => 'Cascadian Dark Ale',
            'polishName' => 'Czarne IPA',
            'description' => '',
            'moreLink' => '',
        ],
        5 => [
            'name' => 'Rye IPA (India Pale Ale)',
            'otherName' => '',
            'polishName' => 'Żytnie IPA',
            'description' => '',
            'moreLink' => '',
        ],
        6 => [
            'name' => 'White IPA (India Pale Ale)',
            'otherName' => '',
            'polishName' => 'Białe IPA',
            'description' => '',
            'moreLink' => '',
        ],
        7 => [
            'name' => 'Double IPA',
            'otherName' => 'Imperial IPA',
            'polishName' => 'Imperialne IPA',
            'description' => '',
            'moreLink' => '',
        ],
        8 => [
            'name' => 'American Barleywine',
            'otherName' => '',
            'polishName' => 'Amerykańskie barleywine',
            'description' => '',
            'moreLink' => '',
        ],
        9 => [
            'name' => 'Pale lager',
            'otherName' => '',
            'polishName' => 'Jasny lager',
            'description' => '',
            'moreLink' => '',
        ],
        10 => [
            'name' => 'Czech Pils',
            'otherName' => '',
            'polishName' => 'Czeski pils',
            'description' => '',
            'moreLink' => '',
        ],
        11 => [
            'name' => 'Polotmave',
            'otherName' => '',
            'polishName' => 'Czeskie półciemne',
            'description' => '',
            'moreLink' => '',
        ],
        12 => [
            'name' => 'Tmave',
            'otherName' => '',
            'polishName' => 'Czeski ciemny lager',
            'description' => '',
            'moreLink' => '',
        ],
        13 => [
            'name' => 'German Pils',
            'otherName' => '',
            'polishName' => 'Niemiecki pils',
            'description' => '',
            'moreLink' => '',
        ],
        14 => [
            'name' => 'Marzen',
            'otherName' => '',
            'polishName' => 'Marcowe',
            'description' => '',
            'moreLink' => '',
        ],
        15 => [
            'name' => 'Rauchbock',
            'otherName' => '',
            'polishName' => 'Koźlak wędzony',
            'description' => '',
            'moreLink' => '',
        ],
        16 => [
            'name' => 'Rauchmarzen',
            'otherName' => '',
            'polishName' => 'Marcowe wędzone',
            'description' => '',
            'moreLink' => '',
        ],
        19 => [
            'name' => 'Dunkelweizen',
            'otherName' => '',
            'polishName' => 'Pszeniczne ciemne',
            'description' => '',
            'moreLink' => '',
        ],
        20 => [
            'name' => 'Weizenbock',
            'otherName' => '',
            'polishName' => 'Koźlak pszeniczny',
            'description' => '',
            'moreLink' => '',
        ],
        21 => [
            'name' => 'Dark lager',
            'otherName' => '',
            'polishName' => 'Ciemny lager',
            'description' => '',
            'moreLink' => '',
        ],
        22 => [
            'name' => 'Doppelbock',
            'otherName' => '',
            'polishName' => 'Koźlak podwójny',
            'description' => '',
            'moreLink' => '',
        ],
        23 => [
            'name' => 'Eisbock',
            'otherName' => '',
            'polishName' => 'Koźlak lodowy',
            'description' => '',
            'moreLink' => '', //todo: połączyć z tym wyżej
        ],
        25 => [
            'name' => 'Weizen',
            'otherName' => 'Hefeweizen',
            'polishName' => 'Pszeniczne',
            'description' => '',
            'moreLink' => '',
        ],
        27 => [
            'name' => 'Bitter',
            'otherName' => 'Extra Special Bitter',
            'polishName' => '',
            'description' => '',
            'moreLink' => '',
        ],
        28 => [
            'name' => 'English IPA',
            'otherName' => '',
            'polishName' => 'Anguelskie IPA',
            'description' => '',
            'moreLink' => '',
        ],
        30 => [
            'name' => 'Brown Porter',
            'otherName' => '',
            'polishName' => '',
            'description' => '',
            'moreLink' => '',
        ],
        32 => [
            'name' => 'Irish Red Ale',
            'otherName' => '',
            'polishName' => 'Irlandzkie czerwone ale',
            'description' => '',
            'moreLink' => '',
        ],
        33 => [
            'name' => '(Dry) Stout',
            'otherName' => '',
            'polishName' => '',
            'description' => '',
            'moreLink' => '',
        ],
        34 => [
            'name' => 'Milk Stout',
            'otherName' => '',
            'polishName' => 'Stout mleczny',
            'description' => '',
            'moreLink' => '',
        ],
        35 => [
            'name' => 'Foreign Extra Stout',
            'otherName' => 'FES',
            'polishName' => '',
            'description' => '',
            'moreLink' => '',
        ],
        36 => [
            'name' => 'Imperial Stout',
            'otherName' => 'RIS',
            'polishName' => 'Stout imperialny',
            'description' => '',
            'moreLink' => '',
        ],
        37 => [
            'name' => '(Imperial) Baltic Porter',
            'otherName' => '',
            'polishName' => '(Imperialny) Porter Bałtycki',
            'description' => '',
            'moreLink' => '',
        ],
        38 => [
            'name' => 'Old Ale',
            'otherName' => '',
            'polishName' => '',
            'description' => '',
            'moreLink' => '',
        ],
        39 => [
            'name' => 'English Barleywine',
            'otherName' => '',
            'polishName' => 'Angielskie barleywine',
            'description' => '',
            'moreLink' => '',
        ],
        40 => [
            'name' => 'Berliner Weisse',
            'otherName' => '',
            'polishName' => '',
            'description' => '',
            'moreLink' => '',
        ],
        42 => [
            'name' => 'Flanders Red Ale / Oud Bruin',
            'otherName' => '',
            'polishName' => '',
            'description' => '',
            'moreLink' => '',
        ],
        44 => [
            'name' => 'Lambic / Gueuze',
            'otherName' => '',
            'polishName' => '',
            'description' => '',
            'moreLink' => '',
        ],
        45 => [
            'name' => 'Witbier',
            'otherName' => '',
            'polishName' => 'Białe pszeniczne',
            'description' => '',
            'moreLink' => '',
        ],
        47 => [
            'name' => 'Saison',
            'otherName' => '',
            'polishName' => '',
            'description' => '',
            'moreLink' => '',
        ],
        48 => [
            'name' => 'Dubbel',
            'otherName' => '',
            'polishName' => '',
            'description' => '',
            'moreLink' => '',
        ],
        49 => [
            'name' => 'Tripel',
            'otherName' => '',
            'polishName' => '',
            'description' => '',
            'moreLink' => '',
        ],
        50 => [
            'name' => 'Quadrupel',
            'otherName' => 'Belgian dark strong ale',
            'polishName' => '',
            'description' => '',
            'moreLink' => '',
        ],
        51 => [
            'name' => 'Gose',
            'otherName' => '',
            'polishName' => '',
            'description' => '',
            'moreLink' => '',
        ],
        52 => [
            'name' => 'Grodziskie',
            'otherName' => '',
            'polishName' => '',
            'description' => '',
            'moreLink' => '',
        ],
        53 => [
            'name' => 'Roggenbier',
            'otherName' => '',
            'polishName' => 'Piwo żytnie',
            'description' => '',
            'moreLink' => '',
        ],
        55 => [
            'name' => 'Wild / Farmhouse Ale',
            'otherName' => '',
            'polishName' => 'Dzikie ale',
            'description' => '',
            'moreLink' => '',
        ],
        56 => [
            'name' => 'Sour Ale',
            'otherName' => '',
            'polishName' => 'Kwaśne ale',
            'description' => '',
            'moreLink' => '',
        ],
        57 => [
            'name' => 'Smoked Ale',
            'otherName' => '',
            'polishName' => 'Wędzone ale',
            'description' => '',
            'moreLink' => '',
        ],
        60 => [
            'name' => 'New England / Vermont IPA',
            'otherName' => '',
            'polishName' => '',
            'description' => '',
            'moreLink' => '',
        ],
        61 => [
            'name' => 'American Pale Ale',
            'otherName' => 'APA',
            'polishName' => '',
            'description' => '',
            'moreLink' => '',
        ],
        64 => [
            'name' => 'Brown Ale',
            'otherName' => '',
            'polishName' => '',
            'description' => '',
            'moreLink' => '',
        ],
        65 => [
            'name' => 'Lite American Pale Ale',
            'otherName' => '',
            'polishName' => '',
            'description' => '',
            'moreLink' => '',
        ],
        67 => [
            'name' => 'Belgian Strong Ale',
            'otherName' => '',
            'polishName' => 'Belgijskie mocne ale',
            'description' => '',
            'moreLink' => '',
        ],
        68 => [
            'name' => 'Blonde',
            'otherName' => '',
            'polishName' => 'Belgijskie jasne',
            'description' => '',
            'moreLink' => '',
        ],
        69 => [
            'name' => 'American Wheat',
            'otherName' => '',
            'polishName' => 'Amerykańskie pszeniczne',
            'description' => '',
            'moreLink' => '',
        ],
        70 => [
            'name' => 'American Lager',
            'otherName' => '',
            'polishName' => '',
            'description' => '',
            'moreLink' => '',
        ],
        71 => [
            'name' => 'Oatmeal Stout', // todo: połączyć ze stoutem
            'otherName' => '',
            'polishName' => '',
            'description' => '',
            'moreLink' => '',
        ],
        72 => [
            'name' => 'Pale Ale',
            'otherName' => '',
            'polishName' => 'Jasne ale',
            'description' => '',
            'moreLink' => '',
        ],
        73 => [
            'name' => 'Milkshake IPA',
            'otherName' => '',
            'polishName' => '',
            'description' => '',
            'moreLink' => '',
        ],
        74 => [
            'name' => 'Coffee Stout',
            'otherName' => '',
            'polishName' => 'Stout kawowy',
            'description' => '',
            'moreLink' => '',
        ],
        76 => [
            'name' => 'Bock',
            'otherName' => '',
            'polishName' => 'Koźlak',
            'description' => '',
            'moreLink' => '',
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
