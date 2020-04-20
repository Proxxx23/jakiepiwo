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
            'moreUrlQuery' => 'american%20india%20pale%ale',
        ],
        2 => [
            'name' => 'Belgian IPA (India Pale Ale)',
            'otherName' => '',
            'polishName' => 'Belgijskie IPA',
            'description' => '<a href="https://piwolucja.pl/piwa-polecane/deep-love-alebrowar-nogne-o/">Recenzja piwa w tym stylu >>></a>',
            'moreUrlQuery' => 'belgian%ipa',
        ],
        3 => [
            'name' => 'Black IPA (India Pale Ale)',
            'otherName' => 'Cascadian Dark Ale',
            'polishName' => '',
            'description' => '<a href="https://piwolucja.pl/piwa-polecane/bojan-black-ipa-za-piataka/">Recenzja piwa w tym stylu >>></a>',
            'moreUrlQuery' => 'black%20ipa',
        ],
        5 => [
            'name' => 'Rye IPA (India Pale Ale)',
            'otherName' => '',
            'polishName' => 'Żytnie IPA',
            'description' => '',
            'moreUrlQuery' => 'rye%20ipa',
        ],
        6 => [
            'name' => 'White IPA (India Pale Ale)',
            'otherName' => '',
            'polishName' => 'Białe IPA',
            'description' => '<a href="https://piwolucja.pl/piwa-polecane/grodziska-white-ipa-fresh-market-za-piataka/">Recenzja piwa w tym stylu >>></a>',
            'moreUrlQuery' => 'white%ipa',
        ],
        7 => [
            'name' => 'Double IPA',
            'otherName' => 'Imperial IPA',
            'polishName' => 'Imperialne IPA',
            'description' => '<a href="https://piwolucja.pl/piwa-polecane/pliny-the-elder-russian-river-brewing/">Recenzja piwa w tym stylu >>></a>',
            'moreUrlQuery' => 'double%20ipa',
        ],
        8 => [
            'name' => 'American Barleywine',
            'otherName' => '',
            'polishName' => 'Amerykańskie barleywine',
            'description' => '',
            'moreUrlQuery' => '',
        ],
        9 => [
            'name' => 'Pale lager',
            'otherName' => '',
            'polishName' => 'Jasny lager',
            'description' => '',
            'moreUrlQuery' => 'lager',
        ],
        10 => [
            'name' => 'Czech Pils',
            'otherName' => 'Bohemian Pilsner',
            'polishName' => 'Czeski pils',
            'description' => '<a href="https://piwolucja.pl/felietony/niepasteryzowany-pilsner-urquell-z-tanka/">Recenzja piwa w tym stylu >>></a>',
            'moreUrlQuery' => 'bohemian%20pilsner',

        ],
        11 => [
            'name' => 'Polotmave', //Może nie być przykładów
            'otherName' => '',
            'polishName' => 'Czeskie półciemne',
            'description' => '',
            'moreUrlQuery' => '',

        ],
        12 => [
            'name' => 'Tmave', //Może nie być przykładów
            'otherName' => '',
            'polishName' => 'Czeski ciemny lager',
            'description' => '',
            'moreUrlQuery' => '',

        ],
        13 => [
            'name' => 'Pils', //dawniej German Pils
            'otherName' => '',
            'polishName' => 'pils',
            'description' => '',
            'moreUrlQuery' => 'pils',

        ],
        14 => [
            'name' => 'Marzen',
            'otherName' => '',
            'polishName' => 'Marcowe',
            'description' => '',
            'moreUrlQuery' => 'marcowe',

        ],
        15 => [
            'name' => 'Rauchbock',
            'otherName' => '',
            'polishName' => 'Koźlak wędzony',
            'description' => '<a href="https://piwolucja.pl/piwa-polecane/aecht-schlenkerla-rauchbock-urbock/">Recenzja piwa w tym stylu >>></a>a',
            'moreUrlQuery' => 'rauchbock',

        ],
        16 => [
            'name' => 'Rauchmarzen',
            'otherName' => '',
            'polishName' => 'Marcowe wędzone',
            'description' => '',
            'moreUrlQuery' => 'rauchmarzen',

        ],
        19 => [
            'name' => 'Dunkelweizen',
            'otherName' => '',
            'polishName' => 'Pszeniczne ciemne',
            'description' => '',
            'moreUrlQuery' => 'dunkelweizen',

        ],
        20 => [
            'name' => 'Weizenbock',
            'otherName' => '',
            'polishName' => 'Koźlak pszeniczny',
            'description' => '<a href="https://piwolucja.pl/piwa-polecane/sledze-losy-podroznika-pana-kormorana/">Recenzja piwa w tym stylu >>></a>',
            'moreUrlQuery' => 'weizenbock',

        ],
        21 => [
            'name' => 'Dark lager',
            'otherName' => '',
            'polishName' => 'Ciemny lager',
            'description' => '',
            'moreUrlQuery' => 'dark%20lager',

        ],
        22 => [
            'name' => 'Doppelbock / Eisbock',
            'otherName' => '',
            'polishName' => 'Koźlak podwójny / lodowy',
            'description' => '<a href="https://piwolucja.pl/piwa-polecane/aventinus-eisbock-pradziadek-wszystkich-wymrazanek-piwna-klasyka/">Recenzja piwa w tym stylu >>></a>',
            'moreUrlQuery' => 'doppelbock',

        ],
        25 => [
            'name' => 'Weizen',
            'otherName' => 'Hefeweizen',
            'polishName' => 'Pszeniczne',
            'description' => '<a href="https://piwolucja.pl/felietony/erdinger-vs-schofferhofer-pojedynek-klasykow-strefa-kibica-auchan/">Recenzja piw w tym stylu >>></a>',
            'moreUrlQuery' => 'pszeniczne',

        ],
        27 => [
            'name' => 'Bitter',
            'otherName' => 'Extra Special Bitter',
            'polishName' => '',
            'description' => '',
            'moreUrlQuery' => 'bitter',

        ],
        28 => [
            'name' => 'English IPA',
            'otherName' => '',
            'polishName' => 'Angielskie IPA',
            'description' => '',
            'moreUrlQuery' => 'english%20ipa',

        ],
        30 => [
            'name' => 'Brown Porter',
            'otherName' => '',
            'polishName' => '',
            'description' => '<a href="https://piwolucja.pl/piwa-polecane/grodziski-porter-za-piataka/">Recenzja piwa w tym stylu >>></a>',
            'moreUrlQuery' => 'brown%20porter',

        ],
        32 => [
            'name' => 'Irish Red Ale',
            'otherName' => '',
            'polishName' => 'Irlandzkie czerwone ale',
            'description' => '',
            'moreUrlQuery' => 'irish%20red%20ale',

        ],
        33 => [
            'name' => '(Dry) Stout',
            'otherName' => '',
            'polishName' => '',
            'description' => '<a href="https://piwolucja.pl/piwa-polecane/piwo-guinness-stout/">Recenzja piwa w tym stylu >>></a>',
            'moreUrlQuery' => 'dry%20stout',

        ],
        34 => [
            'name' => 'Milk Stout',
            'otherName' => '',
            'polishName' => 'Stout mleczny',
            'description' => '<a href="https://piwolucja.pl/piwa-polecane/amber-cherry-milk-stout/">Recenzja piwa w tym stylu >>></a>',
            'moreUrlQuery' => 'milk%20stout',

        ],
        35 => [
            'name' => 'Foreign Extra Stout',
            'otherName' => 'FES',
            'polishName' => '',
            'description' => '<a href="https://piwolucja.pl/piwa-polecane/oharas-leann-follain-stout/">Recenzja piwa w tym stylu >>></a>',
            'moreUrlQuery' => 'foreign%20extra%20stout',

        ],
        36 => [
            'name' => 'Imperial Stout',
            'otherName' => 'RIS',
            'polishName' => 'Stout imperialny',
            'description' => '<a href="https://piwolucja.pl/piwa-polecane/lodz-i-mlyn-piwoteka-de-molen/">Recenzja piwa w tym stylu >>></a>',
            'moreUrlQuery' => 'imperial%20stout',

        ],
        37 => [
            'name' => '(Imperial) Baltic Porter',
            'otherName' => '',
            'polishName' => '(Imperialny) Porter Bałtycki',
            'description' => '',
            'moreUrlQuery' => 'porter%20baltycki',

        ],
        38 => [
            'name' => 'Old Ale',
            'otherName' => '',
            'polishName' => '',
            'description' => '',
            'moreUrlQuery' => 'old%20ale',

        ],
        39 => [
            'name' => 'Barley Wine',
            'otherName' => '',
            'polishName' => 'Angielskie barleywine',
            'description' => '<a href="https://piwolucja.pl/piwa-polecane/warto-zainteresowac-sie-barley-wine/">Recenzja piwa w tym stylu >>></a>',
            'moreUrlQuery' => 'barley%20wine',

        ],
        40 => [
            'name' => 'Berliner Weisse',
            'otherName' => '',
            'polishName' => '',
            'description' => '<a href="https://piwolucja.pl/piwa-polecane/art9-oatmeal-hoptart-recenzja/">Recenzja piwa w tym stylu >>></a>',
            'moreUrlQuery' => 'breliner',

        ],
        42 => [
            'name' => 'Flanders Red Ale / Oud Bruin',
            'otherName' => '',
            'polishName' => '',
            'description' => '<a href="https://piwolucja.pl/piwa-polecane/duchesse-de-bourgogne/">Recenzja piwa w tyl stylu >>></a>',
            'moreUrlQuery' => 'flanders',

        ],
        44 => [
            'name' => 'Lambic / Gueuze',
            'otherName' => '',
            'polishName' => '',
            'description' => '<a href="https://piwolucja.pl/piwa-polecane/oude-geuze-boon-kwasne-piwo/">Recenzja piwa w tym stylu >>></a>',
            'moreUrlQuery' => 'lambic',

        ],
        45 => [
            'name' => 'Witbier',
            'otherName' => '',
            'polishName' => 'Białe pszeniczne',
            'description' => 'Delikatne, lekkie i orzeźwiające. Z niską goryczką, pachnące kolendrą i skórką pomarańczy. <a href="https://piwolucja.pl/piwa-polecane/witbier-browar-kormoran/">Recenzja piwa w stym stylu >>></a>',
            'moreUrlQuery' => 'witbier',

        ],
        47 => [
            'name' => 'Saison',
            'otherName' => '',
            'polishName' => '',
            'description' => '<a href="https://piwolucja.pl/piwa-polecane/po-godzinach-saison-madagaskar-za-piataka/">Recenzja piwa w tym stylu >>></a>',
            'moreUrlQuery' => 'saison',

        ],
        48 => [
            'name' => 'Dubbel',
            'otherName' => '',
            'polishName' => '',
            'description' => '<a href="https://piwolucja.pl/felietony/grand-champion-birofilia-dubbel/">Recenzja piw w tym stylu >>></a>',
            'moreUrlQuery' => 'dubbel',

        ],
        49 => [
            'name' => 'Tripel',
            'otherName' => '',
            'polishName' => '',
            'description' => '<a href="https://piwolucja.pl/felietony/chimay-test-belgijskiej-klasyki/">Recenzja piwa w tym stylu >>></a>',
            'moreUrlQuery' => 'tripel',

        ],
        50 => [
            'name' => 'Quadrupel',
            'otherName' => 'Belgian dark strong ale',
            'polishName' => '',
            'description' => 'Ciężkie i mocne piwo, naładowane karmelem, przyprawani i nutami ciemnych owoców. Degustacyjne. <a href="https://piwolucja.pl/piwa-polecane/struise-pannepot-grand-reserva-2011-nudna-belgia/">Recenzja piwa w tym stylu >>></a>',
            'moreUrlQuery' => 'quadrupel',

        ],
        51 => [
            'name' => 'Gose',
            'otherName' => '',
            'polishName' => '',
            'description' => 'Lekkie, orzeźwiające i delikatnie słone piwo. Bardzo często łączone z owocami dodanymi do konkretnego piwa. <a href="https://piwolucja.pl/piwa-polecane/zacny-zalcman/">Recenzja piwa w tym stylu >>></a>',
            'moreUrlQuery' => 'gose',

        ],
        52 => [
            'name' => 'Grodziskie',
            'otherName' => '',
            'polishName' => '',
            'description' => 'Jedyny w 100% polski styl piwny. Bardzo lekkie, mocno gazowane, pachnące dymem lub wędzonką. <a href="https://piwolucja.pl/felietony/mamy-rodzimy-styl-piwny-a-prawie-nikt-o-tym-nie-wie/">Więcej o tym stylu >>></a>',
            'moreUrlQuery' => 'grodziskie',

        ],
        53 => [
            'name' => 'Roggenbier',
            'otherName' => '',
            'polishName' => 'Piwo żytnie',
            'description' => '',
            'moreUrlQuery' => 'roggen',

        ],
        55 => [
            'name' => 'Wild / Farmhouse Ale',
            'otherName' => '',
            'polishName' => 'Dzikie ale',
            'description' => '',
            'moreUrlQuery' => 'wild%20ale',

        ],
        56 => [
            'name' => 'Sour Ale',
            'otherName' => '',
            'polishName' => 'Kwaśne ale',
            'description' => 'Kwaśne, przeważnie lekkie w odbiorze, praktycznie bez goryczki. W konkretnym piwie często łączone z owocami.',
            'moreUrlQuery' => 'sour%20ale',

        ],
        57 => [
            'name' => 'Smoked Ale',
            'otherName' => '',
            'polishName' => 'Wędzone ale',
            'description' => '',
            'moreUrlQuery' => 'smoked%20ale',

        ],
        60 => [
            'name' => 'New England / Vermont IPA',
            'otherName' => '',
            'polishName' => '',
            'description' => 'Mętne i bardzo owocowe z racji użytych odmian chmielu. Niska goryczka oraz multum smaków i aromatów kojarzących się z cytrusami, owocami tropikalnymi czy winogronem daje odczucie picia "soku". <a href="https://piwolucja.pl/felietony/new-england-ipa-test-polskich-piw/">Więcej o tym stylu >>></a>',
            'moreUrlQuery' => 'ne%20ipa',

        ],
        61 => [
            'name' => 'American Pale Ale',
            'otherName' => 'APA',
            'polishName' => '',
            'description' => '<a href="https://piwolucja.pl/piwa-polecane/cornelius-apa/">Recenzja piwa w tym stylu >>></a>',
            'moreUrlQuery' => 'american%20pale%20ale',

        ],
        64 => [
            'name' => 'Brown Ale',
            'otherName' => '',
            'polishName' => '',
            'description' => '',
            'moreUrlQuery' => 'brown%ale',

        ],
        67 => [
            'name' => 'Belgian Strong Ale',
            'otherName' => '',
            'polishName' => 'Belgijskie mocne ale',
            'description' => '<a href="https://piwolucja.pl/piwa-polecane/delirium-tremens-nocturnum-red-strefa-kibica-auchan/">Recenzja piwa w tym stylu >>></a>',
            'moreUrlQuery' => 'belgian%20strong%20ale',

        ],
        68 => [
            'name' => 'Blonde',
            'otherName' => '',
            'polishName' => 'Belgijskie jasne',
            'description' => '',
            'moreUrlQuery' => 'blonde',

        ],
        69 => [
            'name' => 'American Wheat',
            'otherName' => '',
            'polishName' => 'Amerykańskie pszeniczne',
            'description' => '',
            'moreUrlQuery' => 'american%20wheat',

        ],
        70 => [
            'name' => 'American Lager',
            'otherName' => '',
            'polishName' => '',
            'description' => '',
            'moreUrlQuery' => 'american%20lager',

        ],
        71 => [
            'name' => 'Oatmeal Stout',
            'otherName' => '',
            'polishName' => 'Stout owsiany',
            'description' => '',
            'moreUrlQuery' => 'oatmeal%20stout',

        ],
        72 => [
            'name' => 'Pale Ale',
            'otherName' => '',
            'polishName' => 'Jasne ale',
            'description' => '',
            'moreUrlQuery' => 'pale%20ale',

        ],
        73 => [
            'name' => 'Milkshake IPA',
            'otherName' => '',
            'polishName' => '',
            'description' => 'Mocno nachmielone, z dodatkiem laktozy. Niska goryczka, w połączeniu z owocowymi nutami od chmielu i dodatkiem cukru mlecznego powoduje, że piwo przypomina szejk mleczny lub jogurt pitny. Często z dodatkami owocoów lub pulpy.',
            'moreUrlQuery' => 'milkshake',

        ],
        74 => [
            'name' => 'Coffee Stout',
            'otherName' => '',
            'polishName' => 'Stout kawowy',
            'description' => '',
            'moreUrlQuery' => 'coffee%20stout',

        ],
        76 => [
            'name' => 'Bock',
            'otherName' => '',
            'polishName' => 'Koźlak',
            'description' => '',
            'moreUrlQuery' => 'bock',

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
