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
            'description' => [
                'text' => 'Mocno nachmielone, z wysoką goryczką. Pachnie cytrusami, owocami tropikalnymi, kwiatami czy żywicą.',
                'url' => null,
                'urlText' => null,
            ],
            'moreUrlQuery' => 'american%20india%20pale%ale',
        ],
        2 => [
            'name' => 'Belgian IPA (India Pale Ale)',
            'otherName' => '',
            'polishName' => 'Belgijskie IPA',
            'description' => [
                'text' => 'Tutaj znajdzie się skrócony opis stylu',
                'url' => 'https://piwolucja.pl/piwa-polecane/deep-love-alebrowar-nogne-o/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'belgian%ipa',
        ],
        3 => [
            'name' => 'Black IPA (India Pale Ale)',
            'otherName' => '',
            'polishName' => 'Czarne IPA',
            'description' => [
                'text' => 'Ciemne i mocno goryczkowe. Przeważają nuty palone, czekoladowe, żywiczne czy owocowe.',
                'url' => 'https://piwolucja.pl/piwa-polecane/bojan-black-ipa-za-piataka/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'black%20ipa',
        ],
        5 => [
            'name' => 'Rye IPA (India Pale Ale)',
            'otherName' => '',
            'polishName' => 'Żytnie IPA',
            'description' => [
                'text' => 'Tutaj znajdzie się skrócony opis stylu',
                'url' => null,
                'urlText' => null,
            ],
            'moreUrlQuery' => 'rye%20ipa',
        ],
        6 => [
            'name' => 'White IPA (India Pale Ale)',
            'otherName' => '',
            'polishName' => 'Białe IPA',
            'description' => [
                'text' => 'Tutaj znajdzie się skrócony opis stylu',
                'url' => 'https://piwolucja.pl/piwa-polecane/grodziska-white-ipa-fresh-market-za-piataka/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'white%ipa',
        ],
        7 => [
            'name' => 'Double IPA',
            'otherName' => 'Imperial IPA',
            'polishName' => 'Imperialne IPA',
            'description' => [
                'text' => 'Tutaj znajdzie się skrócony opis stylu',
                'url' => 'https://piwolucja.pl/piwa-polecane/pliny-the-elder-russian-river-brewing/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'double%20ipa',
        ],
        8 => [
            'name' => 'American Barleywine',
            'otherName' => '',
            'polishName' => 'Amerykańskie barleywine',
            'description' => [
                'text' => 'Gęste i degustacyjne. Tutaj znajdzie się skrócony opis stylu.',
                'url' => null,
                'urlText' => null,
            ],
            'moreUrlQuery' => null,
        ],
        9 => [
            'name' => 'Pale lager',
            'otherName' => '',
            'polishName' => 'Jasny lager',
            'description' => [
                'text' => 'Tutaj znajdzie się skrócony opis stylu',
                'url' => null,
                'urlText' => null,
            ],
            'moreUrlQuery' => 'lager',
        ],
        10 => [
            'name' => 'Czech Pils',
            'otherName' => 'Bohemian Pilsner',
            'polishName' => 'Czeski pils',
            'description' => [
                'text' => 'Tutaj znajdzie się skrócony opis stylu',
                'url' => 'https://piwolucja.pl/felietony/niepasteryzowany-pilsner-urquell-z-tanka/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'bohemian%20pilsner',
        ],
        11 => [
            'name' => 'Polotmave', //Może nie być przykładów
            'otherName' => '',
            'polishName' => 'Czeskie półciemne',
            'description' => [
                'text' => 'Tutaj znajdzie się skrócony opis stylu',
                'url' => null,
                'urlText' => null,
            ],
            'moreUrlQuery' => null,
        ],
        12 => [
            'name' => 'Tmave', //Może nie być przykładów
            'otherName' => '',
            'polishName' => 'Czeski ciemny lager',
            'description' => [
                'text' => 'Tutaj znajdzie się skrócony opis stylu',
                'url' => null,
                'urlText' => null,
            ],
            'moreUrlQuery' => null,
        ],
        13 => [
            'name' => 'Pils', //dawniej German Pils
            'otherName' => '',
            'polishName' => 'pils',
            'description' => [
                'text' => 'Tutaj znajdzie się skrócony opis stylu',
                'url' => null,
                'urlText' => null,
            ],
            'moreUrlQuery' => 'pils',
        ],
        14 => [
            'name' => 'Marzen',
            'otherName' => '',
            'polishName' => 'Marcowe',
            'description' => [
                'text' => 'Przeważają smaki słodowe, a więc herbatników czy chleba. Niska goryczka i niewiele nit chmielowych powodują, że piwo wydaje się czasami ciężkawe czy nieco mulące.',
                'url' => null,
                'urlText' => null,
            ],
            'moreUrlQuery' => 'marcowe',
        ],
        15 => [
            'name' => 'Rauchbock',
            'otherName' => '',
            'polishName' => 'Koźlak wędzony',
            'description' => [
                'text' => 'Tutaj znajdzie się skrócony opis stylu',
                'url' => 'https://piwolucja.pl/piwa-polecane/aecht-schlenkerla-rauchbock-urbock/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'rauchbock',
        ],
        16 => [
            'name' => 'Rauchmarzen',
            'otherName' => '',
            'polishName' => 'Marcowe wędzone',
            'description' => [
                'text' => 'Tutaj znajdzie się skrócony opis stylu',
                'url' => null,
                'urlText' => null,
            ],
            'moreUrlQuery' => 'rauchmarzen',
        ],
        19 => [
            'name' => 'Dunkelweizen',
            'otherName' => '',
            'polishName' => 'Pszeniczne ciemne',
            'description' => [
                'text' => 'Tutaj znajdzie się skrócony opis stylu',
                'url' => null,
                'urlText' => null,
            ],
            'moreUrlQuery' => 'dunkelweizen',
        ],
        20 => [
            'name' => 'Weizenbock',
            'otherName' => '',
            'polishName' => 'Koźlak pszeniczny',
            'description' => [
                'text' => 'Tutaj znajdzie się skrócony opis stylu',
                'url' => 'https://piwolucja.pl/piwa-polecane/sledze-losy-podroznika-pana-kormorana/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'weizenbock',
        ],
        21 => [
            'name' => 'Dark lager',
            'otherName' => '',
            'polishName' => 'Ciemny lager',
            'description' => [
                'text' => 'Tutaj znajdzie się skrócony opis stylu',
                'url' => null,
                'urlText' => null,
            ],
            'moreUrlQuery' => 'dark%20lager',
        ],
        22 => [
            'name' => 'Doppelbock / Eisbock',
            'otherName' => '',
            'polishName' => 'Koźlak podwójny / lodowy',
            'description' => [
                'text' => 'Bardzo mocne i degustacyjne. Pełne, ciężkie, słodowe. Przeważają nuty karmelu i ciemnych lub suszonych owoców. Likierowe.',
                'url' => 'https://piwolucja.pl/piwa-polecane/aventinus-eisbock-pradziadek-wszystkich-wymrazanek-piwna-klasyka/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'doppelbock',
        ],
        25 => [
            'name' => 'Weizen',
            'otherName' => 'Hefeweizen',
            'polishName' => 'Pszeniczne jasne',
            'description' => [
                'text' => 'Tutaj znajdzie się skrócony opis stylu',
                'url' => 'https://piwolucja.pl/felietony/erdinger-vs-schofferhofer-pojedynek-klasykow-strefa-kibica-auchan/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'pszeniczne',
        ],
        27 => [
            'name' => 'Bitter',
            'otherName' => 'Extra Special Bitter',
            'polishName' => '',
            'description' => [
                'text' => 'Styl najbardziej znany na Wyspach Brytyjskich. Nisko nagazowane, orzeźwiające, do picia w większej ilości. Z przyjemną ziołową goryczką i nutami słodowymi w aromacie.',
                'url' => null,
                'urlText' => null,
            ],
            'moreUrlQuery' => 'bitter',
        ],
        28 => [
            'name' => 'English IPA',
            'otherName' => '',
            'polishName' => 'Angielskie IPA',
            'description' => [
                'text' => 'Tutaj znajdzie się skrócony opis stylu',
                'url' => null,
                'urlText' => null,
            ],
            'moreUrlQuery' => 'english%20ipa',
        ],
        30 => [
            'name' => 'Brown Porter',
            'otherName' => '',
            'polishName' => '',
            'description' => [
                'text' => 'Tutaj znajdzie się skrócony opis stylu',
                'url' => 'https://piwolucja.pl/piwa-polecane/grodziski-porter-za-piataka/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'brown%20porter',
        ],
        32 => [
            'name' => 'Irish Red Ale',
            'otherName' => '',
            'polishName' => 'Irlandzkie czerwone ale',
            'description' => [
                'text' => 'Tutaj znajdzie się skrócony opis stylu',
                'url' => null,
                'urlText' => null,
            ],
            'moreUrlQuery' => 'irish%20red%20ale',
        ],
        33 => [
            'name' => '(Dry) Stout',
            'otherName' => '',
            'polishName' => '',
            'description' => [
                'text' => 'Najbatrdziej znanym reprezentantem tego stylu jest Guinness. Lekkie, ciemne, palone, z wyraźnymi nutami kawowymi i czekoladowymi. Do picia w większej ilości.',
                'url' => 'https://piwolucja.pl/piwa-polecane/piwo-guinness-stout/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'dry%20stout',
        ],
        34 => [
            'name' => 'Milk Stout',
            'otherName' => '',
            'polishName' => 'Stout mleczny',
            'description' => [
                'text' => 'Ciemne lekkie piwo z dodatkiem laktozy. Nuty czekoladowe, kawowe, palone i mleczne. Przeważnie przypomina czekoladę z niewielką ilości mleka.',
                'url' => 'https://piwolucja.pl/piwa-polecane/amber-cherry-milk-stout/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'milk%20stout',
        ],
        35 => [
            'name' => 'Foreign Extra Stout',
            'otherName' => 'FES',
            'polishName' => '',
            'description' => [
                'text' => 'Tutaj znajdzie się skrócony opis stylu',
                'url' => 'https://piwolucja.pl/piwa-polecane/oharas-leann-follain-stout/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'foreign%20extra%20stout',
        ],
        36 => [
            'name' => 'Imperial Stout',
            'otherName' => 'RIS',
            'polishName' => 'Stout imperialny',
            'description' => [
                'text' => 'Tutaj znajdzie się skrócony opis stylu',
                'url' => 'https://piwolucja.pl/piwa-polecane/lodz-i-mlyn-piwoteka-de-molen/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'imperial%20stout',
        ],
        37 => [
            'name' => '(Imperial) Baltic Porter',
            'otherName' => '',
            'polishName' => '(Imperialny) Porter Bałtycki',
            'description' => [
                'text' => 'Tutaj znajdzie się skrócony opis stylu',
                'url' => null,
                'urlText' => null,
            ],
            'moreUrlQuery' => 'porter%20baltycki',
        ],
        38 => [
            'name' => 'Old Ale',
            'otherName' => '',
            'polishName' => '',
            'description' => [
                'text' => 'Tutaj znajdzie się skrócony opis stylu',
                'url' => null,
                'urlText' => null,
            ],
            'moreUrlQuery' => 'old%20ale',
        ],
        39 => [
            'name' => 'Barley Wine',
            'otherName' => '',
            'polishName' => 'Angielskie barleywine',
            'description' => [
                'text' => 'Tutaj znajdzie się skrócony opis stylu',
                'url' => 'https://piwolucja.pl/piwa-polecane/warto-zainteresowac-sie-barley-wine/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'barley%20wine',
        ],
        40 => [
            'name' => 'Berliner Weisse',
            'otherName' => '',
            'polishName' => '',
            'description' => [
                'text' => 'Kwaśne, bardzo jasne, orzeźwiające i lekkie piwo pszeniczne. Częstym dodatkiem w tym stylu są owoce, pukpy owocowe lub soki.',
                'url' => 'https://piwolucja.pl/piwa-polecane/art9-oatmeal-hoptart-recenzja/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'breliner',
        ],
        42 => [
            'name' => 'Flanders Red Ale / Oud Bruin',
            'otherName' => '',
            'polishName' => '',
            'description' => [
                'text' => 'W zasadzie są to dwa odrębne, choć bardzo zbliżone style. Flanders przypomina kwaśne, wytrawne, cierpkie, gazowane czerwone wino. Nierzadko bywa nieco octowe, ze słodkim finiszem. Oud Bruin jest bardziej słodowe i mniej kwaśne, z nutami fig czy daktyli.',
                'url' => 'https://piwolucja.pl/piwa-polecane/duchesse-de-bourgogne/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'flanders',
        ],
        44 => [
            'name' => 'Lambic / Gueuze',
            'otherName' => '',
            'polishName' => '',
            'description' => [
                'text' => 'Kwaśne, jasne, przeważnie mocno gazowane. Fermentowane mikroflorą obecną w powietrzu, zamiast drożdżami piwowarskimi. Pachnie "kwaśno", z nutami końskiej derki czy siana.',
                'url' => 'https://piwolucja.pl/piwa-polecane/oude-geuze-boon-kwasne-piwo/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'lambic',
        ],
        45 => [
            'name' => 'Witbier',
            'otherName' => '',
            'polishName' => 'Białe pszeniczne',
            'description' => [
                'text' => 'Delikatne, lekkie i orzeźwiające. Z niską goryczką, pachnące kolendrą i skórką pomarańczy.',
                'url' => 'https://piwolucja.pl/piwa-polecane/witbier-browar-kormoran/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'witbier',
        ],
        47 => [
            'name' => 'Saison',
            'otherName' => '',
            'polishName' => '',
            'description' => [
                'text' => 'Tutaj znajdzie się skrócony opis stylu',
                'url' => 'https://piwolucja.pl/piwa-polecane/po-godzinach-saison-madagaskar-za-piataka/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'saison',
        ],
        48 => [
            'name' => '(Belgian) Dubbel',
            'otherName' => '',
            'polishName' => '',
            'description' => [
                'text' => 'Tutaj znajdzie się skrócony opis stylu',
                'url' => 'https://piwolucja.pl/felietony/grand-champion-birofilia-dubbel/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'dubbel',
        ],
        49 => [
            'name' => '(Belgian) Tripel',
            'otherName' => '',
            'polishName' => '',
            'description' => [
                'text' => 'Tutaj znajdzie się skrócony opis stylu',
                'url' => 'https://piwolucja.pl/felietony/chimay-test-belgijskiej-klasyki/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'tripel',
        ],
        50 => [
            'name' => 'Quadrupel',
            'otherName' => 'Belgian dark strong ale',
            'polishName' => '',
            'description' => [
                'text' => 'Ciężkie i mocne piwo, naładowane karmelem, przyprawani i nutami ciemnych owoców. Degustacyjne.',
                'url' => 'https://piwolucja.pl/piwa-polecane/struise-pannepot-grand-reserva-2011-nudna-belgia/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'quadrupel',
        ],
        51 => [
            'name' => 'Gose',
            'otherName' => '',
            'polishName' => '',
            'description' => [
                'text' => 'Lekkie, orzeźwiające i delikatnie słone. Praktycznie bez goryczki. Bardzo często łączone z owocami dodanymi do konkretnego piwa.',
                'url' => 'https://piwolucja.pl/piwa-polecane/zacny-zalcman/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'gose',
        ],
        52 => [
            'name' => 'Grodziskie',
            'otherName' => '',
            'polishName' => '',
            'description' => [
                'text' => 'Jedyny w 100% polski styl piwny. Bardzo lekkie, mocno gazowane, z niewielką ziołową goryczką. Wyróżnikiem piw w tym stylu jest aromat dymny lub wędzony.',
                'url' => 'https://piwolucja.pl/felietony/mamy-rodzimy-styl-piwny-a-prawie-nikt-o-tym-nie-wie/',
                'urlText' => 'Więcej o tym stylu >>>',
            ],
            'moreUrlQuery' => 'grodziskie',
        ],
        53 => [
            'name' => 'Roggenbier',
            'otherName' => '',
            'polishName' => 'Piwo żytnie',
            'description' => [
                'text' => 'Treściwe i nieco zawiesiste. Zbliżone do piwa pszenicznego, jednak ciemniejsze od niego (pomarańczowe czy miedziano-brązowe). Z niską goryczką, nastawione na smaki słodowe - chlebowe, pumperniklowe czy ciasteczkowe. Przeważnie nieco przyprawowe lub pikantne.',
                'url' => null,
                'urlText' => null,
            ],
            'moreUrlQuery' => 'roggen',
        ],
        55 => [
            'name' => 'Wild / Farmhouse Ale',
            'otherName' => '',
            'polishName' => 'Dzikie ale',
            'description' => [
                'text' => 'Tutaj znajdzie się skrócony opis stylu',
                'url' => null,
                'urlText' => null,
            ],
            'moreUrlQuery' => 'wild%20ale',
        ],
        56 => [
            'name' => 'Sour Ale',
            'otherName' => '',
            'polishName' => 'Kwaśne ale',
            'description' => [
                'text' => 'Kwaśne, przeważnie lekkie w odbiorze, praktycznie bez goryczki. W konkretnym piwie często łączone z owocami. Orzeźwiające.',
                'url' => null,
                'urlText' => null,
            ],
            'moreUrlQuery' => 'sour%20ale',
        ],
        57 => [
            'name' => 'Smoked Ale',
            'otherName' => '',
            'polishName' => 'Wędzone ale',
            'description' => [
                'text' => 'Tutaj znajdzie się skrócony opis stylu',
                'url' => null,
                'urlText' => null,
            ],
            'moreUrlQuery' => 'smoked%20ale',
        ],
        60 => [
            'name' => 'New England / Vermont IPA',
            'otherName' => '',
            'polishName' => '',
            'description' => [
                'text' => 'Mętne i bardzo owocowe z racji użytych odmian chmielu. Niska goryczka oraz spektrum smaków i aromatów kojarzących się z cytrusami, owocami tropikalnymi czy winogronem daje odczucie picia "soku".',
                'url' => 'https://piwolucja.pl/felietony/new-england-ipa-test-polskich-piw/',
                'urlText' => 'Więcej o tym stylu >>>',
            ],
            'moreUrlQuery' => 'ne%20ipa',
        ],
        61 => [
            'name' => 'American Pale Ale',
            'otherName' => 'APA',
            'polishName' => '',
            'description' => [
                'text' => 'Tutaj znajdzie się skrócony opis stylu',
                'url' => 'https://piwolucja.pl/piwa-polecane/cornelius-apa/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'american%20pale%20ale',
        ],
        64 => [
            'name' => 'Brown Ale',
            'otherName' => '',
            'polishName' => '',
            'description' => [
                'text' => 'Tutaj znajdzie się skrócony opis stylu',
                'url' => null,
                'urlText' => null,
            ],
            'moreUrlQuery' => 'brown%ale',
        ],
        67 => [
            'name' => 'Belgian Strong Ale',
            'otherName' => '',
            'polishName' => 'Belgijskie mocne ale',
            'description' => [
                'text' => 'Tutaj znajdzie się skrócony opis stylu',
                'url' => 'https://piwolucja.pl/piwa-polecane/delirium-tremens-nocturnum-red-strefa-kibica-auchan/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'belgian%20strong%20ale',
        ],
        68 => [
            'name' => '(Belgian) Blonde',
            'otherName' => '',
            'polishName' => 'Belgijskie jasne',
            'description' => [
                'text' => 'Tutaj znajdzie się skrócony opis stylu',
                'url' => null,
                'urlText' => null,
            ],
            'moreUrlQuery' => 'blonde',
        ],
        69 => [
            'name' => 'American Wheat',
            'otherName' => '',
            'polishName' => 'Amerykańskie pszeniczne',
            'description' => [
                'text' => 'Tutaj znajdzie się skrócony opis stylu',
                'url' => null,
                'urlText' => null,
            ],
            'moreUrlQuery' => 'american%20wheat',
        ],
        70 => [
            'name' => 'American Lager',
            'otherName' => 'American Pilsner',
            'polishName' => '',
            'description' => [
                'text' => 'Jasny lager, chmielony odmianami chmielu wprowadzającymi do piwa wysoką goryczkę oraz smaki/aromaty cytrusowe, tropikalne, gronowe, kwiatowe, ziołowe czy żywiczne. Piwa w tym stylu są raczej proste, z wyeksponowaną chmielowością i mocną goryczką.',
                'url' => null,
                'urlText' => null,
            ],
            'moreUrlQuery' => 'american%20lager',
        ],
        71 => [
            'name' => 'Oatmeal Stout',
            'otherName' => '',
            'polishName' => 'Stout owsiany',
            'description' => [
                'text' => 'Tutaj znajdzie się skrócony opis stylu',
                'url' => null,
                'urlText' => null,
            ],
            'moreUrlQuery' => 'oatmeal%20stout',
        ],
        72 => [
            'name' => 'Pale Ale',
            'otherName' => '',
            'polishName' => 'Jasne ale',
            'description' => [
                'text' => 'Tutaj znajdzie się skrócony opis stylu',
                'url' => null,
                'urlText' => null,
            ],
            'moreUrlQuery' => 'pale%20ale',
        ],
        73 => [
            'name' => 'Milkshake IPA',
            'otherName' => '',
            'polishName' => '',
            'description' => [
                'text' => 'Mocno nachmielone, z dodatkiem laktozy. Niska goryczka, w połączeniu z owocowymi nutami od chmielu i dodatkiem cukru mlecznego powoduje, że piwo przypomina szejk mleczny lub jogurt pitny. Często z dodatkami owoców lub pulpy.',
                'url' => null,
                'urlText' => null,
            ],
            'moreUrlQuery' => 'milkshake',
        ],
        74 => [
            'name' => 'Coffee Stout',
            'otherName' => '',
            'polishName' => 'Stout kawowy',
            'description' => [
                'text' => 'Tutaj znajdzie się skrócony opis stylu',
                'url' => null,
                'urlText' => null,
            ],
            'moreUrlQuery' => 'coffee%20stout',
        ],
        76 => [
            'name' => 'Bock',
            'otherName' => '',
            'polishName' => 'Koźlak',
            'description' => [
                'text' => 'Tutaj znajdzie się skrócony opis stylu',
                'url' => null,
                'urlText' => null,
            ],
            'moreUrlQuery' => 'bock',
        ],
    ];

    public function fetchByIds( array $ids ): ?StyleInfoCollection
    {
        if ( empty( $ids ) ) {
            return null;
        }

        $styleInfoCollection = new StyleInfoCollection();
        foreach ( $ids as $id ) {
            $styleInfoCollection->add( StyleInfo::fromArray( self::BEER_STYLE[$id], $id ) );
        }

        return $styleInfoCollection;
    }
}
