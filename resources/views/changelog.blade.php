<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>The Gustator v.0.1 nightly - changelog</title>

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
                <h3>Changelog v. 0.1 nightly (13.03.2018 r.)</h3>
                <h5>Najnowsze zmiany i funkcje (najnowsze na dole)</h5>
                <ul>
                    <li>Pełny algorytm z budowaniem "siły pozytywnej" i "siły negatywnej"</li>
                    <li>Wersja bazująca na 64 stylach</li>
                    <li>Mechanizm synergi pozytywnej i synergii negatywnej</li>
                    <li>Skalowanie pytań (ważne > mniej ważne)</li>
                    <li>Logi błędów i odpowiedzi do bazy</li>
                    <li>Zapis do newslettera przez MailChimp API</li>
                    <li>Niektóre style są ważniejsze w ramach danego pytania</li>
                    <li>Barrel aged jako osobna część</li>
                    <li>Wykluczanie wędzonek, piw kwaśnych i słonych</li>
                    <li>Poprawki algorytmu dla piw lekkich ciemnych</li>
                    <li>Witanie się z użytkownikiem, jeśli już odwiedzał narzędzie</li>
                    <li>Obsługa tooltipów via Bootstrap</li>
                    <li>Wstępny mechanizm prewencyjny dla występowania piwa jako polecane i odradzane jednocześnie</li>
                    <li>Jeśli stylowi z 4 lub 5 pozycji brakowało niewiele do "podium", jeden lub obydwa zostaną pokazane na 4 i 5 pozycji (na szaro, mniejszym fontem)</li> 
                    <li>Wyróżnianie piwa, które w znaczący sposób odbiegło punktami reszcie stawki (pozytywnie i negatywnie)</li>
                    <li>Pierwsze przymiarki do Bootstrap forms</li>
                    <li>Zapis odpowiedzi użytkownika do bazy na jednym ID</li>
                </ul>
                <h5>Bugfixes</h5>
                <ul>
                    <li>Poprawka zliczania punktów</li>
                    <li>W przypadku odrzucenia piw wędzonych, pojawiał się wędzony ris i imperialny porter bałtycki</li>
                    <li>Pusty e-mail nie wysyła się już przy zaznaczeniu newslettera</li>
                    <li>Kliknięcie w treść odpowiedzi zaznacza odpowiedź</li>
                </ul>

                <h5>Planowane usprawnienia (według priotytetu)</h5> 
                <ul>
                    <li>PolskiKraft API - Słowniki + wpięcie (kontroler)</li>
                    <li>Możliwość wykluczania przez użytkownika całych stylów, które już zna (na początek IPA)</li>
                    <li>Pole (0/1) "mail" w bazie logów</li>
                    <li>Uzupełnienie synergii pozytywnych i negatywnych</li>
                    <li>Adnotacja o BA tylko dla piw, które faktycznie leżakuje się w beczkach</li>
                    <li>Mail powitalny z MailChimp + e-book (final welcome e-mail)</li>
                    <li>Pokazywanie tylko jednej nazwy stylu + dymek z nazwą alternatywną i PL</li>
                    <li>Zapisywanie pól formularza w razie błędów</li>
                    <li>Mail z wynikiem na życzenie</li>
                    <li>Przełącznik w mocy piw</li>
                    <li>Czy danych piw możesz napić się w okolicy? (OnTap API)</li>
                    <li>Wyświetlanie stylów z ostatniej wizyty (???)</li>
                    <li>Najczęściej polecane użytkowniom style</li>
                    <li>Facebook Tags, SEO oraz Twitter Cards</li>
                </ul>
            </div>
    </body>
</html>
