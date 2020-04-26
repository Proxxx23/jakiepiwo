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
            'otherName' => 'AIPA',
            'polishName' => 'Amerykańskie IPA',
            'description' => [
                'text' => 'Mocno nachmielone, z wysoką goryczką. Pachnie cytrusami, owocami tropikalnymi, kwiatami czy żywicą. Ikona stylów "nowofalowych" - bardzo aromatyczne i intensywne w smaku.',
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
                'text' => 'Mocno nachmielone, z dośc wysoką goryczką. To mieszanka nowofalowego India Pale Ale z piwem w stylu belgijskim. Miks tych dwóch podejść daje w efekcie piwo pachnące cytrusami, owocami tropikalnymi, kwiatami czy żywicą. Użycie drożdży do trunków w stylach belgijskich wprowadza dodatkowe nuty przyprawowe (na przykład biał pieprz czy goździki) oraz korzenne.',
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
                'text' => 'Ciemne i mocno goryczkowe. Przeważają nuty palone, czekoladowe, żywiczne, prażonego słonecznika i owocowe.',
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
                'text' => 'Mocno nachmielone, z wysoką goryczką. Bardzo aromatyczne i intensywne w smaku. Pachnie cytrusami, owocami tropikalnymi, kwiatami czy żywicą. Dodatek żyta sprawia, że piwo jest gęste, nieco zawiesiste i delikatnie pikantne lub przyprawowe.',
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
                'text' => 'Tutaj znajdzie się skrócony opis stylu.',
                'url' => 'https://piwolucja.pl/piwa-polecane/grodziska-white-ipa-fresh-market-za-piataka/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'white%ipa',
        ],
        7 => [
            'name' => 'Double / Imperial IPA',
            'otherName' => 'DIPA',
            'polishName' => 'Imperialne IPA',
            'description' => [
                'text' => 'Tęższa wersja piwa w stylu India Pale Ale. Gęste, mocne, potężnie nachmielone i wyraźnie gorzkie. Przeważają w nim nuty owocowe, żywiczne czy kwiatowe. W smaku przeważnie słodkawe, nieco karmelowe i wyraźnie chmielowe.',
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
                'text' => 'Tutaj znajdzie się skrócony opis stylu.',
                'url' => null,
                'urlText' => null,
            ],
            'moreUrlQuery' => null,
        ],
        9 => [
            'name' => 'Pale Lager',
            'otherName' => '',
            'polishName' => 'Jasny lager',
            'description' => [
                'text' => 'Jasne piwo codzienne, każdemu dobrze znane. Nieskomplikowane, dobrze gaszące pragnienie, z niską ziołową goryczką.',
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
                'text' => 'Jasne i orzeźwiające. Pachnie ziołami, nierzadko też cytrusami. Cechą charakterystyczną są nutki maślane. Nieskomplikowane, doskonale gasi pragnienie. Dobrze kojarzoną ikoną stylu jest Pilsner Urquell.',
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
                'text' => 'Tutaj znajdzie się skrócony opis stylu.',
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
                'text' => 'Tutaj znajdzie się skrócony opis stylu.',
                'url' => null,
                'urlText' => null,
            ],
            'moreUrlQuery' => null,
        ],
        13 => [
            'name' => 'Pilsner', //dawniej German Pils
            'otherName' => 'Pils',
            'polishName' => 'pils',
            'description' => [
                'text' => 'Tutaj znajdzie się skrócony opis stylu.',
                'url' => 'https://piwolucja.pl/piwa-polecane/pilsner-urquell-piwna-klasyka/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'pils',
        ],
        14 => [
            'name' => 'Marzen',
            'otherName' => '',
            'polishName' => 'Marcowe',
            'description' => [
                'text' => 'Przeważają w nim smaki słodowe, a więc herbatników czy chleba. Niska goryczka i niewielka ilość nut chmielowych powodują, że piwo wydaje się czasami ciężkawe czy nieco mulące.',
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
                'text' => 'Tutaj znajdzie się skrócony opis stylu.',
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
                'text' => 'Tutaj znajdzie się skrócony opis stylu.',
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
                'text' => 'Tutaj znajdzie się skrócony opis stylu.',
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
                'text' => 'Tutaj znajdzie się skrócony opis stylu.',
                'url' => 'https://piwolucja.pl/piwa-polecane/sledze-losy-podroznika-pana-kormorana/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'weizenbock',
        ],
        21 => [
            'name' => 'Dark Lager',
            'otherName' => '',
            'polishName' => 'Ciemny lager',
            'description' => [
                'text' => 'Ciemna wersja popularnego jasnego lagera. Piwo lekkie w odbiorze, z niewysoką goryczką. Przeważają w nim nuty przypiekane, karmelowe i czekoladowe.',
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
                'text' => 'Treściwe, mocno gazowane i orzeźwiające. Wyróżnikiem piw pszenicznych są aromaty bananowe, przyprawowe (przeważnie goździkowe), gumy balonowej, czasami też drożdżowe.',
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
                'text' => 'Styl najbardziej rozpowszechniony na Wyspach Brytyjskich. Nisko nagazowane, orzeźwiające, do picia w większej ilości. Z przyjemną ziołową goryczką i nutami słodowymi w aromacie.',
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
                'text' => 'Tutaj znajdzie się skrócony opis stylu.',
                'url' => null,
                'urlText' => null,
            ],
            'moreUrlQuery' => 'english%20ipa',
        ],
        30 => [
            'name' => 'English Porter',
            'otherName' => 'Brown Porter',
            'polishName' => '',
            'description' => [
                'text' => 'Ciemne i średnio mocne, z niewysoką goryczką. Dominują w nim nuty przypiekane, czekoladowe, karmelowe i orzechowe, czasami też kawowe lub lukrecjowe. W odróżnieniu od porterów bałtyckich, porter angielski jest piwem o wiele lżejszym (do 5,4% alkoholu) i sesyjnym.',
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
                'text' => 'Tutaj znajdzie się skrócony opis stylu.',
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
                'text' => 'Najbatrdziej znanym reprezentantem tego stylu jest Guinness. Lekkie, ciemne, palone, czasami nieco cierpkie, z wyraźnymi czekoladowymi, rzadziej kawowymi. Gładkie na języku, do picia w większej ilości.',
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
                'text' => 'Ciemne lekkie piwo z dodatkiem laktozy. Nuty czekoladowe, palone, mleczne, czasami również kawowe. Przeważnie przypomina czekoladę z niewielką ilości mleka i zazwyczaj jest bardziej słodkie, niż cierpkie czy palone.',
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
                'text' => 'Mocna wersja klasycznego stoutu. Piwo ciemne, gęste, tęgie. Wyraźnie palone, czekoladowe, czasami nieco karmelowe i cierpkie. Goryczka w tym stylu jest dośc mocno wyczuwalna, choć nie zbliża się nawet do ekstremum.',
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
                'text' => 'Najmocniejszy wariant piwa w stylu stout. Gęste, bardzo mocne, intensywne. Bogate i kompleksowe tak w aromacie, jak i w smaku. Nuty od czekoladowych, kakao, palonych przez prażone, po kawowe i palone. "RIS-y" wzbogacane są często przeróżnymi dodatkami i leżakowane w beczkach po alkoholach szlachetnych - najczęściej bourbonie lub whisky. Nadaje im to dodatkowych nut drewnianych, wanilinowych czy kokosowych. Piwo likierowe, do picia w spokoju.',
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
                'text' => 'Tutaj znajdzie się skrócony opis stylu.',
                'url' => 'https://piwolucja.pl/szybki-lyk/imperialny-porter-baltycki-z-bursztynem-prost/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'porter%20baltycki',
        ],
        38 => [
            'name' => 'Old Ale',
            'otherName' => '',
            'polishName' => '',
            'description' => [
                'text' => 'Tutaj znajdzie się skrócony opis stylu.',
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
                'text' => 'Bardzo gęste, mocne i oleiste. Wypełnione nutami przypieczonej skórki od chleba, tostów, karmelu, daktyli czy suszonych owoców. Nuty chmielowe są również wyczuwalne, jednak goryczka w tym stylu jest raczej stonowana; nacisk kładzie się na pełnię i smaki wnoszone przez słody.',
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
                'text' => 'W zasadzie są to dwa odrębne, choć bardzo zbliżone style. Flanders Red Ale przypomina kwaśne, wytrawne, cierpkie, gazowane czerwone wino. Nierzadko bywa nieco octowe, ze słodkim finiszem. Oud Bruin styl bardziej słodowy i mniej kwaśny, z nutami ciemnych lub suszonych owoców.',
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
                'text' => 'Kwaśne, jasne, przeważnie mocno gazowane. Fermentowane mikroflorą obecną w powietrzu, zamiast drożdżami piwowarskimi. Pachnie "kwaśno", czasami nieco octowo, z nutami końskiej derki czy siana.',
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
                'text' => 'Delikatne, gładkie i orzeźwiające, mimo sporej treściwości. Ma bardzo niską goryczkę, pachnie kolendrą i skórką pomarańczy. Nieco kwaskowe i raczej wytrawne na finiszu.',
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
                'text' => 'Tutaj znajdzie się skrócony opis stylu.',
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
                'text' => 'Tutaj znajdzie się skrócony opis stylu.',
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
                'text' => 'Tutaj znajdzie się skrócony opis stylu.',
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
                'text' => 'Ciężkie i mocne piwo w stylu belgijskim. Naładowane karmelem, nutami opiekanymi (tosty czy skórka od chleba) przyprawami i akcentami ciemnych owoców. Degustacyjne.',
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
                'text' => 'Lekkie, orzeźwiające, musujące i delikatnie słone. Praktycznie bez goryczki. Bardzo często łączone z owocami dodanymi do konkretnego piwa.',
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
                'text' => 'Treściwe i nieco zawiesiste. Zbliżone do piwa pszenicznego, jednak ciemniejsze od niego (pomarańczowe lub miedziano-brązowe). Z niską goryczką, nastawione na smaki słodowe - chlebowe, pumperniklowe czy ciasteczkowe. Przeważnie nieco przyprawowe lub pikantne.',
                'url' => 'https://piwolucja.pl/piwa-polecane/apetyt-na-zycie-warto-kupic/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'roggen',
        ],
        55 => [
            'name' => 'Wild / Farmhouse Ale',
            'otherName' => '',
            'polishName' => 'Dzikie ale',
            'description' => [
                'text' => 'Tutaj znajdzie się skrócony opis stylu.',
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
                'text' => 'Smoked Ale to nie konkretny styl piwa, a pewna kategoria. Zaliczają się do niej piwa górnej fermentacji, do których warzenia użyto słodu wędzonego dymem. Są przez to dymione w aromacie i smaku, nierzadko też wędzone czy "szynkowe".',
                'url' => null,
                'urlText' => null,
            ],
            'moreUrlQuery' => 'smoked%20ale',
        ],
        60 => [
            'name' => 'New England / Vermont IPA',
            'otherName' => 'NEIPA',
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
                'text' => 'Tutaj znajdzie się skrócony opis stylu.',
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
                'text' => 'Tutaj znajdzie się skrócony opis stylu.',
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
                'text' => 'Tutaj znajdzie się skrócony opis stylu.',
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
                'text' => 'Tutaj znajdzie się skrócony opis stylu.',
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
                'text' => 'Tutaj znajdzie się skrócony opis stylu.',
                'url' => null,
                'urlText' => null,
            ],
            'moreUrlQuery' => 'american%20wheat',
        ],
        70 => [
            'name' => 'American Pilsner / Lager',
            'otherName' => '',
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
                'text' => 'Lekkie, ciemne, palone, z wyraźnymi nutami czekoladowymi, czasami również kawowymi. Dodatek płatków owsianych (oatmeal) sprawia, że piwo jest gładkie i aksamitne.',
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
                'text' => 'Jasne piwo górnej fermentacji, odpowiednik jasnego lagera (fermentacja dolna). „Ejle” — w zestawieniu z podobnymi piwami dolnej fermentacji — są bardziej złożone, przeważnie szczodrzej nachmielone. Samo pale ale nie występuje raczej jako samoistny styl piwny i prawie zawsze dostaje odpowiedni przedrostek.',
                'url' => 'https://piwolucja.pl/felietony/apa-ipa-aipa-dipa-iipa-tipa-czym-sie-rozni/',
                'urlText' => 'Przeczytaj o przedrostkach i wariacjach tego stylu >>>',
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
                'text' => 'Ciemne piwo z dodatkiem kawy w postaci ziaren, zaparzonego naparu lub cold brew. Dominują w nim smaki palone, czekoladowe czy palone, wspierane przez nuty wnoszone przez kawę. Goryczka jest przeważnie wyczuwalna, w typie ziołowym. W zależności od użytych odmian kawy, piwo może zostać wzbogacone przez akcenty typowe dla konkretnych ziaren - kwiatowe, miodowe, owocowe czy orzechowe.',
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
                'text' => 'Mocny lager o miedzianej barwie. W smaku przeważają nuty przypieczonej skórki chleba czy karmelowe. Goryczka niska. Piwo nie wydaje się tęgie, nie powinno być taż odczuwalnie alkoholowe. Bardziej złożone od typowego lagera.',
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
