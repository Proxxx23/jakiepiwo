<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>deGustator v.0.1 nightly - changelog</title>

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
                <h3>Changelog v. 0.1 nightly (12.03.2018 r.)</h3>
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
                </ul>
                <h5>Bugfixes</h5>
                <ul>
                    <li>W przypadku odrzucenia piw wędzonych, pojawiał się wędzony ris i imperialny porter bałtycki</li>
                    <li>Pusty e-mail nie wysyła się już przy zaznaczeniu newslettera</li>
                    <li>Kliknięcie w treść odpowiedzi zaznacza odpowiedź</li>
                </ul>

                <h5>Planowane usprawnienia</h5> 
                <ul>
                    <li>W przypadku 3 podobnych stylów do polecenia, algorytm wybierze tylko 1-2 z nich i dorzuci 1 styl z 4 pozycji (w trakcie prac...)</li>
                    <li>W przypadku kilku zbliżonych stylów w tej samej punktacji końcowej, algorytm wylosuje 3 z nich (w trakcie prac...)</li>
                    <li>Adnotacja o BA tylko dla piw, które faktycznie leżakuje się w beczkach (w trakcie prac...)</li>
                    <li>Mail powitalny z MailChimp + e-book</li>
                    <li>Zapisywanie pól formularza w razie błędów</li>
                    <li>Mail z wynikiem na życzenie</li>
                    <li>Polecane, najlepiej oceniane piwa w danych stylach po połączeniu z PolskiKraft API</li>
                    <li>Czy danych piw możesz napić się w okolicy? (OnTap API)</li>
                    <li>Wyświetlanie stylów z ostatniej wizyty</li>
                    <li>Najczęściej polecane użytkowniom style</li>
                    <li>Mechanizm, który zapobiegnie pokazywaniu tego samego stylu w polecanych i odradzanych (co i tak nie będzie mieć raczej miejsca)</li>
                    <li>Pokazywanie użytkownikowi - na życzenie - ile punktów dostały dane style</li>
                    <li>Wyróżnianie piwa, które w znaczący sposób odbiegło punktami reszcie stawki</li>
                    <li>Możliwość wykluczania przez użytkownika całych stylów, które już zna (na początek IPA)</li>
                </ul>
            </div>
    </body>
</html>
