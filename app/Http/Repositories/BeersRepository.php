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
                'url' => 'https://kompendiumpiwa.pl/india-pale-ale/',
                'urlText' => 'Węcej o stylu India Pale Ale >>>',
            ],
            'moreUrlQuery' => 'american%20ipa',
        ],
        2 => [
            'name' => 'Belgian IPA (India Pale Ale)',
            'otherName' => '',
            'polishName' => 'Belgijskie IPA',
            'description' => [
                'text' => 'Mocno nachmielone, z dość wysoką goryczką. To mieszanka nowofalowego India Pale Ale z piwem w stylu belgijskim. Miks tych dwóch podejść daje w efekcie piwo pachnące cytrusami, owocami tropikalnymi, kwiatami czy żywicą. Użycie drożdży do trunków w stylach belgijskich wprowadza dodatkowe nuty przyprawowe (na przykład biały pieprz czy goździki) oraz korzenne.',
                'url' => 'https://piwolucja.pl/piwa-polecane/deep-love-alebrowar-nogne-o/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'belgian%20ipa',
        ],
        3 => [
            'name' => 'Black IPA (India Pale Ale)',
            'otherName' => '',
            'polishName' => 'Czarne IPA',
            'description' => [
                'text' => 'Ciemne i mocno goryczkowe. Przeważają nuty palone, czekoladowe, żywiczne, prażonego słonecznika i owocowe. Niektóre piwa w tym stylu mogą nie wykazywać prawie żadnych smaków czy aromatów "ciemnych". W takich reprezentantach stylu jedynie barwa będzie ciemnobrązowa lub czarna.',
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
                'url' => 'https://kompendiumpiwa.pl/zytnia-ipa/',
                'urlText' => 'Więcej o stylu Żytnie IPA >>>',
            ],
            'moreUrlQuery' => 'rye%20ipa',
        ],
        6 => [
            'name' => 'White IPA (India Pale Ale)',
            'otherName' => '',
            'polishName' => 'Białe IPA',
            'description' => [
                'text' => 'Hybryda stylów India Pale Ale i belgijskiego Witbiera. Łączy w sobie ogrom chmielowych aromatów cytrusowych, tropikalnych i kwiatowych, z nutami znanymi z belgijskich piw pszenicznych — drożdżowymi i nieco przyprawowymi. Goryczka średnia, a samo piwo przeważnie nieco zmętnione i jasne.',
                'url' => 'https://piwolucja.pl/piwa-polecane/grodziska-white-ipa-fresh-market-za-piataka/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'white%20ipa',
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
                'text' => 'W aromacie cytrusowe lub żywiczne, ale również karmelowe czy ogólnie - "słodkie". Piwo bardzo mocne, intensywne i gładkie. Rozgrzewające od alkoholu. Przeważnie nie jest bardzo gorzkie, a chmiel wyczuwalny jest bardziej w aromacie i smaku. Gęste i słodowe.',
                'url' => 'https://kompendiumpiwa.pl/american-barleywine/',
                'urlText' => 'Więcej o stylu American Barleywine >>>',
            ],
            'moreUrlQuery' => 'american%20barley%20wine',
        ],
        9 => [
            'name' => 'Pale Lager',
            'otherName' => '',
            'polishName' => 'Jasny Lager',
            'description' => [
                'text' => 'Jasne piwo codzienne, znane doskonale każdemu. Nieskomplikowane, świetnie gaszące pragnienie, z niską (preważnie) prostą ziołową goryczką.',
                'url' => 'https://kompendiumpiwa.pl/jasny-lager/',
                'urlText' => 'Więcej o stylu Jasny Lager >>>',
            ],
            'moreUrlQuery' => 'jasny%20lager',
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
            'name' => 'Desitka', //Może nie być przykładów
            'otherName' => '',
            'polishName' => 'Czeski Lekki Pils',
            'description' => [
                'text' => 'Styl dobrze znany w Czechach. Piwo bardzo lekkie, ale nie wodniste, o niskiej zawartości alkoholu. Raczej słodowe, wytrawne, pachnące ziołowym chmielem, z nie za wysoką goryczką.',
                'url' => null,
                'urlText' => null,
            ],
            'moreUrlQuery' => 'desitka',
        ],
        13 => [
            'name' => 'Pilsner', //dawniej German Pils
            'otherName' => 'Pils',
            'polishName' => '',
            'description' => [
                'text' => 'Jasne i orzeźwiające, przypominające klasycznego jasnego lagera. Jest od niego jednak bardziej gorzkie i wyczuwalnie mocniej nachmielone. W zależności od użytych odmian chmielu może pachnieć ziołowo lub nieco cytrusowo. Nieskomplikowane, świetnie gasi pragnienie.',
                'url' => 'https://piwolucja.pl/piwa-polecane/pilsner-urquell-piwna-klasyka/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'pils',
        ],
        14 => [
            'name' => 'Marzen',
            'otherName' => 'Oktoberfest',
            'polishName' => 'Marcowe',
            'description' => [
                'text' => 'Przeważają w nim słodkie smaki słodowe, a więc herbatników czy chleba. Pozbawione nut owocowych. Niezbyt intensywna goryczka i niewielka ilość nut chmielowych (wyłącznie w tle) powodują, że piwo wydaje się czasami nieco mulące.',
                'url' => 'https://kompendiumpiwa.pl/marcowe/',
                'urlText' => 'Więcej o stylu Marcowe >>>',
            ],
            'moreUrlQuery' => 'marcowe',
        ],
        15 => [
            'name' => 'Rauchbock',
            'otherName' => '',
            'polishName' => 'Koźlak Wędzony',
            'description' => [
                'text' => 'Mocny lager o miedzianej barwie. W smaku przeważają nuty przypieczonej skórki chleba czy karmelowe. Dodatek słodu wędzonego wprowadza do piwa wyraźne nuty dymne, wędzone czy szynkowe. Goryczka niska. Piwo nie wydaje się tęgie, nie powinno być taż odczuwalnie alkoholowe. Bardziej złożone od typowego lagera, wyczuwalnie "podwędzane".',
                'url' => 'https://piwolucja.pl/piwa-polecane/aecht-schlenkerla-rauchbock-urbock/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'rauchbock',
        ],
        16 => [
            'name' => 'Rauchmarzen',
            'otherName' => '',
            'polishName' => 'Marcowe Wędzone',
            'description' => [
                'text' => 'Przeważają w nim słodkie smaki słodowe, a więc herbatników czy chleba. Wzbogacone są dodatkowo przez słód wędzony, wprowadzający do piwa wyraźne elementy dymne, wędzone czy szynkowe. Pozbawione nut owocowych, z niewielką goryczką.',
                'url' => 'https://kompendiumpiwa.pl/wedzone-marcowe-rauchmaerzen/',
                'urlText' => 'Więcej o stylu Rauchmarzen >>>',
            ],
            'moreUrlQuery' => 'rauchmarzen',
        ],
        19 => [
            'name' => 'Dunkelweizen',
            'otherName' => 'Dunkles Weissbier',
            'polishName' => 'Pszeniczne Ciemne',
            'description' => [
                'text' => 'Gładkie i aksamitne. Łączy sobie cechy piwa pszenicznego - biszkoptowe smaki zmieszane z drożdżowością, przyprawami i nutami bananów czy gumy balonowej - z wyraźnie "opiekanymi" elementami słodowymi. Goryczka praktycznie nieodczuwalna.',
                'url' => 'https://kompendiumpiwa.pl/dunkles-weissbier/',
                'urlText' => 'Więcej o stylu Dunkelweizen >>>',
            ],
            'moreUrlQuery' => 'dunkelweizen',
        ],
        20 => [
            'name' => 'Weizenbock',
            'otherName' => '',
            'polishName' => 'Koźlak Pszeniczny',
            'description' => [
                'text' => 'Gęste, treściwe i mocne. Przede wszystkim słodowe, chlebowe, karmelowe, wyraźnie owocowe (ciemne owoce, śliwki, rodzynki). Kremowe i rozgrzewające, z niską goryczką, wyzbyte aromatu chmielowego.',
                'url' => 'http://kompendiumpiwa.pl/kozlak-pszeniczny-weizenbock/',
                'urlText' => 'Więcej o stylu Koźlak Pszeniczny >>>',
            ],
            'moreUrlQuery' => 'weizenbock',
        ],
        21 => [
            'name' => 'Dark Lager',
            'otherName' => '',
            'polishName' => 'Ciemny Lager',
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
            'polishName' => 'Koźlak Podwójny / Lodowy',
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
            'polishName' => 'Pszeniczne Jasne',
            'description' => [
                'text' => 'Treściwe, mocno gazowane i orzeźwiające. Wyróżnikiem piw pszenicznych są aromaty bananowe, przyprawowe (przeważnie goździkowe), gumy balonowej, czasami też drożdżowe.',
                'url' => 'https://piwolucja.pl/felietony/erdinger-vs-schofferhofer-pojedynek-klasykow-strefa-kibica-auchan/',
                'urlText' => 'Recenzja piw w tym stylu >>>',
            ],
            'moreUrlQuery' => 'pszeniczne',
        ],
        27 => [
            'name' => 'Bitter',
            'otherName' => 'Extra Special Bitter',
            'polishName' => '',
            'description' => [
                'text' => 'Styl najbardziej rozpowszechniony na Wyspach Brytyjskich. Nisko nagazowane, orzeźwiające, do picia w większej ilości. Z przyjemną ziołową goryczką i nutami słodowymi w aromacie.',
                'url' => 'https://kompendiumpiwa.pl/bitter/',
                'urlText' => 'Więcej o stylu Bitter >>>',
            ],
            'moreUrlQuery' => 'bitter',
        ],
        30 => [
            'name' => 'English Porter',
            'otherName' => 'Brown Porter',
            'polishName' => '',
            'description' => [
                'text' => 'Ciemne i średnio mocne, z niewysoką goryczką. Dominują w nim nuty przypiekane, czekoladowe, karmelowe i orzechowe, czasami też kawowe lub lukrecjowe. W odróżnieniu od porterów bałtyckich porter angielski jest piwem o wiele lżejszym (do 5,4% alkoholu) i sesyjnym.',
                'url' => 'https://piwolucja.pl/piwa-polecane/grodziski-porter-za-piataka/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'brown%20porter',
        ],
        32 => [
            'name' => 'American Stout',
            'otherName' => '',
            'polishName' => '',
            'description' => [
                'text' => 'Ciemne, wyraźnie palone i intensywnie goryczkowe. Dominują w nim nuty czekolady, żywiczne, prażone, sosnowe, czasami wspierane przez nuty owocowe. Połącznie klasycznego stoutu ze sporą dawką amerykańskich chmieli.',
                'url' => 'https://kompendiumpiwa.pl/american-stout/',
                'urlText' => 'Więcej o stylu American Stout >>>',
            ],
            'moreUrlQuery' => 'american%20stout',
        ],
        33 => [
            'name' => '(Dry) Stout',
            'otherName' => '',
            'polishName' => '',
            'description' => [
                'text' => 'Najbardziej znanym reprezentantem tego stylu jest Guinness. Lekkie, ciemne, palone, czasami nieco cierpkie, z wyraźnymi smakami czekoladowymi, rzadziej kawowymi. Gładkie na języku, do picia w większej ilości.',
                'url' => 'https://piwolucja.pl/piwa-polecane/piwo-guinness-stout/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'dry%20stout',
        ],
        34 => [
            'name' => 'Milk / Sweet Stout',
            'otherName' => '',
            'polishName' => 'Stout Mleczny',
            'description' => [
                'text' => 'Ciemne lekkie piwo z dodatkiem laktozy. Nuty czekoladowe, palone, mleczne, czasami również kawowe. Przeważnie przypomina czekoladę z niewielką ilości mleka i zazwyczaj jest bardziej słodkie niż cierpkie czy palone.',
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
                'text' => 'Mocna wersja klasycznego stoutu. Piwo ciemne, gęste, tęgie. Wyraźnie palone, czekoladowe, czasami nieco karmelowe i cierpkie. Goryczka w tym stylu jest dość mocno wyczuwalna, choć nie zbliża się nawet do ekstremum.',
                'url' => 'https://piwolucja.pl/piwa-polecane/oharas-leann-follain-stout/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'foreign%20extra%20stout',
        ],
        36 => [
            'name' => 'Imperial Stout',
            'otherName' => 'RIS',
            'polishName' => 'Stout Imperialny',
            'description' => [
                'text' => 'Najmocniejszy wariant piwa w stylu stout. Gęste, bardzo mocne, intensywne. Bogate i kompleksowe tak w aromacie, jak i w smaku. Nuty od czekoladowych, kakao, palonych przez prażone, po kawowe i popiołowe. "RIS-y" wzbogacane są często przeróżnymi dodatkami i leżakowane w beczkach po alkoholach szlachetnych - najczęściej bourbonie lub whisky. Nadaje im to dodatkowych nut drewnianych, wanilinowych czy kokosowych. Piwo likierowe, do picia w spokoju.',
                'url' => 'https://kompendiumpiwa.pl/russian-imperial-stout/',
                'urlText' => 'Więcej o stylu Imperial Stout >>>',
            ],
            'moreUrlQuery' => 'imperial%20stout',
        ],
        37 => [
            'name' => '(Imperial) Baltic Porter',
            'otherName' => '',
            'polishName' => '(Imperialny) Porter Bałtycki',
            'description' => [
                'text' => 'Mocny, skomplikowany i ciemny tak w barwie, jak również w aromacie i smaku. Kombinacja nut palonych, karmelowych, kawowych czy popiołowych. Wyczuwalne alkoholowe rozgrzewanie, czasami w klimacie nut znanych z alkoholi szlachetnych. Innymi często obecnymi aromatami w porterach są: czerwone owoce, owoce suszone, zioła, melasa, palony karmel, pumpernikiel czy orzechy. Piwo degustacyjne, wielowymiarowe i agresywne.',
                'url' => 'https://piwolucja.pl/szybki-lyk/imperialny-porter-baltycki-z-bursztynem-prost/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'porter%20bałtycki',
        ],
        38 => [
            'name' => 'Old Ale',
            'otherName' => '',
            'polishName' => '',
            'description' => [
                'text' => 'Tradycyjny angielski styl piwa. Dość mocne i ciężkie, słodowe, przeważnie nieco słodkie. Rozgrzewające, z nutami orzechowymi, karmelowymi, ciemnych/suszonych owoców czy melasy.  ',
                'url' => 'https://kompendiumpiwa.pl/old-ale/',
                'urlText' => 'Więcej o stylu Old Ale >>>',
            ],
            'moreUrlQuery' => 'old%20ale',
        ],
        39 => [
            'name' => 'Barley Wine',
            'otherName' => '',
            'polishName' => 'Angielskie Barleywine',
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
                'text' => 'Kwaśne, bardzo jasne, orzeźwiające i lekkie piwo pszeniczne. Częstym dodatkiem w tym stylu są owoce, pulpy owocowe lub soki.',
                'url' => 'https://kompendiumpiwa.pl/berliner-weisse/',
                'urlText' => 'Więcej o stylu Berliner Weisse >>>',
            ],
            'moreUrlQuery' => 'berliner',
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
            'polishName' => 'Białe Pszeniczne',
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
                'text' => 'Orzeźwiające, a jednocześnie treściwe. Nieco owocowe, lekko pikantne, przyprawowe i kwaskowe. Raczej wytrawne. Nuty, które można w nim wyczuć to między innymi zioła, siano, cytrusy czy kwiaty. Goryczka wyraźna, jednak piwo nie jest nastawione na dużą gorzkość.',
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
                'text' => 'Mocne, treściwe, wielowymiarowe z niską goryczką. Mariaż nut owocowych (ciemne owoce), karmelowych, chlebowych/ciastowych, czekoladowych i przyprawowych. Jest raczej mocne (6-8%), nieco rozgrzewające, czasami perfumowe.',
                'url' => 'http://kompendiumpiwa.pl/belgian-dubbel/',
                'urlText' => 'Więcej o stylu Dubbel >>>',
            ],
            'moreUrlQuery' => 'dubbel',
        ],
        49 => [
            'name' => '(Belgian) Tripel',
            'otherName' => '',
            'polishName' => '',
            'description' => [
                'text' => 'Jasne, mocne piwo z niewysoką goryczką. Pachnie kwiatowo, perfumowo i nieco owocowo (pomarańcze, cytryny, gruszki, morele). W aromacie i smaku obecne są zawsze nuty przyprawowe, z białym pieprzem jako często przywoływanym wyróżnikiem. Mimo swojej mocy, alkohol ma przeważnie dobrze ukryty, przez co piwa w tym stylu pije się szybciej, niż wskazywałby na to woltaż.',
                'url' => 'https://piwolucja.pl/felietony/chimay-test-belgijskiej-klasyki/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'tripel',
        ],
        50 => [
            'name' => 'Quadrupel',
            'otherName' => 'Belgian Dark Strong Ale',
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
                'urlText' => 'Więcej o stylu Grodziskie >>>',
            ],
            'moreUrlQuery' => 'grodziskie',
        ],
        53 => [
            'name' => 'Roggenbier',
            'otherName' => '',
            'polishName' => 'Piwo Żytnie',
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
            'polishName' => 'Dzikie Ale',
            'description' => [
                'text' => 'Piwo fermentowane z udziałem tzw. "dzikich drożdży", w stylach klasycznych odpowidających za ewentualne zakażenie i finalnie psucie się piwa. Fermentacja z użyciem tzw. "brettów" wprowadza do piwa nuty określanem mianem "funky". W piwach "dzikich" lub "zdziczałych" wyczuć można intensywne aromaty stajni, mokrego siana, końskiej derki, owocowe czy nieco ziemiste. Piwa takie mogą też często być nieco kwaskowe lub wyraźnie kwaśne.',
                'url' => null,
                'urlText' => null,
            ],
            'moreUrlQuery' => 'wild%20ale',
        ],
        56 => [
            'name' => 'Sour Ale',
            'otherName' => '',
            'polishName' => 'Kwaśne Ale',
            'description' => [
                'text' => 'Kwaśne, przeważnie lekkie w odbiorze, praktycznie bez goryczki. W konkretnym piwie często łączone z owocami. Orzeźwiające, bez wyraźnych nut chmielowych.',
                'url' => null,
                'urlText' => null,
            ],
            'moreUrlQuery' => 'sour%20ale',
        ],
        57 => [
            'name' => 'Smoked Ale',
            'otherName' => '',
            'polishName' => 'Wędzone Ale',
            'description' => [
                'text' => 'Smoked Ale to nie konkretny styl piwa, a pewna kategoria. Zaliczają się do niej piwa górnej fermentacji, do których warzenia użyto słodu wędzonego. Są przez to dymione w aromacie i smaku, nierzadko też wędzone czy "szynkowe".',
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
                'urlText' => 'Więcej o stylu NEIPA >>>',
            ],
            'moreUrlQuery' => 'ne%20ipa',
        ],
        61 => [
            'name' => 'American Pale Ale',
            'otherName' => 'APA',
            'polishName' => '',
            'description' => [
                'text' => 'Mocno nachmielone, raczej lekkie, ze średnią goryczką. Pachnie i smakuje cytrusami, owocami tropikalnymi, kwiatami czy żywicą. Ikona stylów "nowofalowych" - bardzo aromatyczne i chmielowe.',
                'url' => 'https://piwolucja.pl/piwa-polecane/cornelius-apa/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'american%20pale%20ale',
        ],
        67 => [
            'name' => 'Belgian Strong Ale',
            'otherName' => '',
            'polishName' => 'Belgijskie Mocne Ale',
            'description' => [
                'text' => 'Bardzo mocne, jasne piwo belgijskie. Wyróżnia je intensywny aromat estrowy, a więc owocowy (przeważnie są to morele, gruszki lub brzoskwinie). Zapach przypominać może białe wino. Dochodzą do tego nuty przyprawowe - pieprzne, gałki muszkatołowej czy kolendry. Nie powinno pachnieć alkoholem, ani też tak smakować; alkohol powinien być zgrabnie wkomponowany w bazę słodową.',
                'url' => 'https://piwolucja.pl/piwa-polecane/delirium-tremens-nocturnum-red-strefa-kibica-auchan/',
                'urlText' => 'Recenzja piwa w tym stylu >>>',
            ],
            'moreUrlQuery' => 'belgian%20strong%20ale',
        ],
        68 => [
            'name' => '(Belgian) Blonde',
            'otherName' => '',
            'polishName' => 'Belgijskie Jasne',
            'description' => [
                'text' => 'Dość tęgie, z bardzo niską goryczką. Pachnie owocami (morele, agrest, gruszki, brzoskwinie) i przyprawami. Nieco słodkawe, czasami także kwiatowe lub perfumowe.',
                'url' => 'https://kompendiumpiwa.pl/belgian-blond-ale/',
                'urlText' => 'Więcej o stylu Blonde >>>',
            ],
            'moreUrlQuery' => 'blonde',
        ],
        69 => [
            'name' => 'American Wheat',
            'otherName' => '',
            'polishName' => 'Amerykańskie Pszeniczne',
            'description' => [
                'text' => 'Podstawa z lekkiego piwa pszenicznegoo, połączona z wyraźnym nachmieleniem za pomocą nowofalowych odmian chmielu. Chmiele amerykańskie wzbogacają ciasteczkową/biszkoptową podstawę słodową o wyraźne nuty żywiczne, kwiatowe i owocowe. Piwo lekkie, owocowe, charakteryzujące się zdecydowanie wyczuwalną goryczką. Przeważnie nieco słodkie.',
                'url' => 'https://kompendiumpiwa.pl/american-wheat/',
                'urlText' => 'Więcej o stylu Amerykańskie Pszeniczne >>>',
            ],
            'moreUrlQuery' => 'american%20wheat',
        ],
        70 => [
            'name' => 'American Pilsner / Lager',
            'otherName' => '',
            'polishName' => '',
            'description' => [
                'text' => 'Jasny lager, chmielony odmianami chmielu wprowadzającymi do piwa wysoką goryczkę oraz smaki/aromaty cytrusowe, tropikalne, gronowe, kwiatowe, ziołowe czy żywiczne. Piwa w tym stylu są raczej proste, z wyeksponowaną chmielowością i mocną goryczką.',
                'url' => 'Więcej o nowofalowych Pilsach >>>',
                'urlText' => 'http://kompendiumpiwa.pl/nowofalowy-pils/',
            ],
            'moreUrlQuery' => 'american%20lager',
        ],
        71 => [
            'name' => 'Oatmeal Stout',
            'otherName' => '',
            'polishName' => 'Stout Owsiany',
            'description' => [
                'text' => 'Lekkie, ciemne, palone, z wyraźnymi nutami czekoladowymi, czasami również kawowymi. Dodatek płatków owsianych (oatmeal) sprawia, że piwo jest gładkie i aksamitne.',
                'url' => 'https://kompendiumpiwa.pl/stout-owsiany/',
                'urlText' => 'Więcej o stylu Stout Owsiany >>>',
            ],
            'moreUrlQuery' => 'oatmeal%20stout',
        ],
        72 => [
            'name' => 'Pale Ale',
            'otherName' => '',
            'polishName' => 'Jasne Ale',
            'description' => [
                'text' => 'Jasne piwo górnej fermentacji, odpowiednik jasnego lagera (fermentacja dolna). „Ejle” — w zestawieniu z podobnymi piwami dolnej fermentacji — są bardziej złożone, przeważnie szczodrzej nachmielone. Samo pale ale nie występuje raczej jako samoistny styl piwny i prawie zawsze dostaje odpowiedni przedrostek. Zazwyczaj są to piwa lekkie, w których obecne są nuty od ziemistych, ziołowych i tytoniowych przez owocowe po żywiczne.',
                'url' => 'https://piwolucja.pl/felietony/apa-ipa-aipa-dipa-iipa-tipa-czym-sie-rozni/',
                'urlText' => 'Przeczytaj o przedrostkach i wariantach tego stylu >>>',
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
            'polishName' => 'Stout Kawowy',
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
                'url' => 'https://kompendiumpiwa.pl/kozlak-bock/',
                'urlText' => 'Więcej o stylu Koźlak >>>',
            ],
            'moreUrlQuery' => 'bock',
        ],
        77 => [
            'name' => 'Fruit Beer',
            'otherName' => '',
            'polishName' => 'Piwo Owocowe',
            'description' => [
                'text' => 'To segment piw z dodatkiem owoców w różnej postaci - całych, przecierów, pulp czy soków. Znajdziesz tu piwa w różnych stylach.',
                'url' => null,
                'urlText' => null,
            ],
            'moreUrlQuery' => 'fruit%20beer',
        ],
        998 => [
            'name' => 'Pastry Beer',
            'otherName' => '',
            'polishName' => 'Piwo deserowe (wersja ciemna)',
            'description' => [
                'text' => 'To segment piw mających przypominać deser lub cokolwiek związanego z cukrem. Do wywołania takiego efektu używa się soków, owoców czy aromatów (przeważnie naturalnych). Bardzo słodkie, przypominające znane słodycze, ciasta czy desery.',
                'url' => 'https://piwolucja.pl/piwa-polecane/piwa-pastry-na-przykladzie-beeramisu-deer-bear/',
                'urlText' => 'Recenzja piwa w stylu Pastry >>>',
            ],
            'moreUrlQuery' => 'pastry',
        ],
        999 => [
            'name' => 'Pastry Beer',
            'otherName' => '',
            'polishName' => 'Piwo deserowe (wersja jasna)',
            'description' => [
                'text' => 'To segment piw mających przypominać deser lub cokolwiek związanego z cukrem. Do wywołania takiego efektu używa się soków, owoców czy aromatów (przeważnie naturalnych). Bardzo słodkie, często również kwaśne, przypominające znane słodycze czy desery.',
                'url' => 'https://piwolucja.pl/piwa-polecane/piwa-pastry-na-przykladzie-beeramisu-deer-bear/',
                'urlText' => 'Recenzja piwa w stylu Pastry >>>',
            ],
            'moreUrlQuery' => 'pastry',
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
