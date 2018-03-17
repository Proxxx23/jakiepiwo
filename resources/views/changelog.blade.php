<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>The Gustator v0.4 nightly - changelog</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Mina" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Mina', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }

            .changelog {
                position: absolute;
                right: 25px;
                top: 18px;
            }

            .todo {
                position: absolute;
                right: 25px;
                top: 180px;
            }

            .todo h3 {
                font-size: 14px;
                color: lime;
            }

            .todo ul li {
                font-size: 14px;
                color: black;
            }

            }
        </style>
    </head>
    <body><div class="changelog">
                <h3>Changelog v0.4 nightly (17.03.2018 r.)</h3>
                <h5>Najnowsze zmiany i funkcje</h5>
                <ul>
                    <li>Usunięto 8 stylów piwnych</li>
                    <li>Bitter / ESB jako jeden styl</li>
                    <li>Nowe style: Blonde, Belgian Strong Ale, American Wheat, Oatmeal Stout, Pale Ale, Milkshake IPA, Coffee Stout, Rye Porter, Bock</li>
                    <li>Synergie dla nowych stylów</li>
                    <li>Nowy układ pytań</li>
                    <li>PolskiKraft API - Słowniki + gotowy kontroler</li>
                    <li>Propozycje najlepiej ocenianych piw w danym stylu z PolskiKraft.pl wraz z miniaturkami w tooltipie</li>
                    <li>Debug odpowiedzi z formularza w wynikach (v 0.5)</li>
                    <li>Uzupełnienie negatywnych synergii (v 0.5)</li>
                    <li>Piwa polecane wyświetlają się losowo z puli (v 0.5)</li>
                </ul>
                <h5>Bugfixes</h5>
                <ul>
                    <li>Brak</li>
                </ul>

                <h5>Planowane usprawnienia (według priotytetu)</h5> 
                <ul>
                    <li>FEATURE: Złączenie ciemnych imperiali w jedno (v0.5 nightly)</li>
                    <li>FEATURE: Inna koncepcja pytania o goryczkę (goryczka DO lub skala suwakowa) (v0.6 nightly)</li>
                    <li>FEATURE: Zapisywanie pól formularza w razie błędów (v0.6 nightly)</li>
                    <li>FEATURE: Bootstrap Theme + Form (v0.1 beta)</li>
                    <li>FEATURE: Inny układ wyników (v0.1 beta)</li>
                    <li>FEATURE: Najczęściej polecane użytkowniom style (v0.2 beta)</li>
                    <li>FEATURE: Mail z wynikiem na życzenie (v0.2 beta)</li>
                    <li>FEATURE: Pole (0/1) "mail" w bazie logów (v0.2 beta)</li>
                    <li>FEATURE: Dalsze prace z PK API</li>
                    <li>FEATURE: Ratebeer API dla stylów, których nie ma w PK</li>
                    <li>FEATURE: Inny układ pytań o smaki</li>
                    <li>FEATURE: Pokazywanie tylko jednej nazwy stylu + dymek z nazwą alternatywną i PL</li>
                    <li>FEATURE: Czy danych piw możesz napić się w okolicy? (OnTap API)</li>
                    <li>FEATURE: Facebook Tags, SEO oraz Twitter Cards</li>
                </ul>
            </div>
    </body>
</html>
